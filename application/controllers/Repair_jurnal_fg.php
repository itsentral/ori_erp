<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backfill jurnal WIP -> Finish Good (QC release) untuk data_erp_fg yang
 * sudah masuk tetapi belum terjurnal (reff_trans_stok kosong).
 *
 * Logika jurnal mengikuti Qc::jurnalFG() (jenis_jurnal 'WIP-Finishgood').
 * Satu JV per (id_trans, created_date), sama seperti pemanggilan aslinya.
 *
 * Header javh yatim (sudah ter-insert tapi detail jurnal gagal) dipakai ulang
 * nomornya, sehingga tidak muncul nomor JV ganda.
 *
 * Pemakaian (default = DRY RUN):
 *   /repair_jurnal_fg?from=2026-09-12&to=2026-09-30
 *   /repair_jurnal_fg?from=2026-09-12&to=2026-09-30&limit=1000&run=1
 *   /repair_jurnal_fg?id_trans=7440&run=1
 *
 * Parameter:
 *   from, to : rentang data_erp_fg.tanggal (default 2026-09-12 s/d 2026-11-12)
 *   id_trans : batasi ke satu id_trans
 *   run=1    : eksekusi sungguhan
 *   limit    : maksimal grup per request (default 50)
 *   dedup=0  : matikan pembuangan duplikat (default aktif: baris FG dengan
 *              id_trans + id_pro sama seperti baris yang lebih awal diabaikan)
 *   stok=1   : ikut update warehouse_stock_wip (-qty) dan warehouse_stock_fg (+qty).
 *              Default 0, karena di live update stok kemungkinan sudah jalan.
 */
class Repair_jurnal_fg extends CI_Controller {

	const KETERANGAN_FG = 'WIP to Finish Good';
	const JENIS_JURNAL  = 'WIP-Finishgood';

	private $dedup = TRUE;

	public function __construct() {
		parent::__construct();
		$this->load->model('Jurnal_model');

		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	private function out($msg){
		echo $msg."\n";
		@ob_flush(); @flush();
	}

	public function index(){
		header('Content-Type: text/plain; charset=utf-8');
		set_time_limit(0);

		$from     = $this->input->get('from') ?: '2026-09-12';
		$to       = $this->input->get('to') ?: '2026-11-12';
		$idTrans  = (int) $this->input->get('id_trans');
		$run      = ($this->input->get('run') == '1');
		$limit    = (int) ($this->input->get('limit') ?: 50);
		$doStok   = ($this->input->get('stok') === '1');
		$this->dedup = ($this->input->get('dedup') !== '0');
		$username = $this->session->userdata['ORI_User']['username'];

		$this->db->db_debug = FALSE;

		$this->out('MODE   : '.($run ? 'RUN (menulis data)' : 'DRY RUN (tidak menulis data)'));
		$this->out("RANGE  : $from s/d $to | limit $limit | update stok ".($doStok ? 'YA' : 'tidak')
			.' | buang duplikat '.($this->dedup ? 'YA' : 'tidak'));
		$this->out(str_repeat('-', 100));

		$where = "tanggal BETWEEN ".$this->db->escape($from)." AND ".$this->db->escape($to)
			." AND keterangan = ".$this->db->escape(self::KETERANGAN_FG)
			." AND (reff_trans_stok IS NULL OR reff_trans_stok = '')";
		if($idTrans > 0){
			$where .= " AND id_trans = ".$idTrans;
		}

		$zero = $this->db->query("SELECT COUNT(*) AS n FROM data_erp_fg WHERE $where AND (id_trans IS NULL OR id_trans = 0)")->row();
		if(!empty($zero->n)){
			$this->out("PERINGATAN: {$zero->n} baris data_erp_fg dengan id_trans 0/NULL, TIDAK diproses (cek manual).");
		}

		$sql = "SELECT id_trans, created_date, MIN(tanggal) AS tanggal, MAX(no_spk) AS no_spk, COUNT(*) AS n
				FROM data_erp_fg
				WHERE $where AND id_trans > 0
				GROUP BY id_trans, created_date
				ORDER BY MIN(tanggal) ASC, created_date ASC, id_trans ASC";
		$q = $this->db->query($sql);
		if(!$q){
			$this->out('ERROR query kandidat: '.$this->db->error()['message']);
			return;
		}
		$groups = $q->result_array();

		$this->out('Kandidat belum terjurnal: '.count($groups).' grup (id_trans + created_date)');
		$this->out(str_repeat('-', 100));

		$cnt = ['ok'=>0,'skip'=>0,'fail'=>0];
		$processed = 0;
		foreach($groups as $g){
			if($processed >= $limit){
				$this->out("Batas limit $limit tercapai. Jalankan ulang untuk sisanya.");
				break;
			}
			$processed++;
			$res = $this->process_group($g['id_trans'], $g['created_date'], $where, $run, $doStok, $username);
			$cnt[$res['status']]++;
			$this->out(sprintf('[%s] id_trans=%s | %s | %s | %s FG | %s',
				strtoupper($res['status']), $g['id_trans'], $g['tanggal'], $g['no_spk'], $g['n'], $res['msg']));
		}

		$this->out(str_repeat('-', 100));
		$this->out("SELESAI: ok={$cnt['ok']} skip={$cnt['skip']} fail={$cnt['fail']}");
		if(!$run){
			$this->out('Ini DRY RUN. Tambahkan &run=1 untuk eksekusi sungguhan.');
		}
	}

	private function process_group($idtrans, $createdDate, $where, $run, $doStok, $username){
		$rows = $this->db->query("SELECT * FROM data_erp_fg WHERE $where AND id_trans = ".(int)$idtrans
			." AND created_date = ".$this->db->escape($createdDate)." ORDER BY id ASC")->result();
		if(empty($rows)){
			return ['status'=>'skip','msg'=>'data kosong'];
		}

		// Buang baris duplikat: unit (id_pro) yang sama sudah punya baris FG lebih awal
		// untuk id_trans yang sama (mis. klik ganda saat release QC).
		$dupNote = '';
		if($this->dedup){
			$allIds = implode(',', array_map(function($r){ return (int)$r->id; }, $rows));
			$dupRows = $this->db->query("SELECT f.id, MIN(o.id) AS orig_id
				FROM data_erp_fg f
				JOIN data_erp_fg o ON o.id_trans = f.id_trans AND o.id_pro = f.id_pro
				                  AND o.keterangan = f.keterangan AND o.id < f.id
				WHERE f.id IN ($allIds) AND f.id_pro IS NOT NULL AND f.id_pro <> 0
				GROUP BY f.id")->result();
			if(!empty($dupRows)){
				$dupMap = [];
				foreach($dupRows as $dr){ $dupMap[(int)$dr->id] = (int)$dr->orig_id; }
				$rows = array_values(array_filter($rows, function($r) use ($dupMap){ return !isset($dupMap[(int)$r->id]); }));
				$origs = array_unique(array_values($dupMap));
				sort($origs);
				$dupNote = ' | '.count($dupMap).' baris duplikat diabaikan (id FG: '.implode(',', array_keys($dupMap)).', asli: '.implode(',', $origs).')';
				if(empty($rows)){
					return ['status'=>'skip','msg'=>'DUPLIKAT, tidak dijurnal'.$dupNote];
				}
			}
		}

		$ids = array_map(function($r){ return (int)$r->id; }, $rows);
		$idList = implode(',', $ids);

		$db2 = $this->load->database('accounting', TRUE);
		$db2->db_debug = FALSE;

		// Guard dobel: sudah ada detail jurnal untuk baris FG ini
		$dupErp = $this->db->query("SELECT COUNT(*) AS n FROM jurnaltras WHERE jenis_jurnal = ".$this->db->escape(self::JENIS_JURNAL)." AND id_trans_stok IN ($idList)")->row();
		if(!empty($dupErp->n)){
			return ['status'=>'skip','msg'=>'sudah ada di jurnaltras ('.$dupErp->n.' baris), cek manual'];
		}
		$dupAcc = $db2->query("SELECT COUNT(*) AS n FROM jurnal WHERE id_trans_stok IN ($idList)")->row();
		if($dupAcc === NULL){
			return ['status'=>'fail','msg'=>'cek gl.jurnal gagal: '.$db2->error()['message']];
		}
		if(!empty($dupAcc->n)){
			return ['status'=>'skip','msg'=>'sudah ada di gl.jurnal ('.$dupAcc->n.' baris), cek manual'];
		}

		// Susun jurnal (sama dengan Qc::jurnalFG)
		$tgl   = $rows[0]->tanggal;
		$lines = [];
		$totalCogs = 0;
		$debet = 0; $kredit = 0;
		foreach($rows as $d){
			$material   = (float)$d->material;
			$direct     = (float)$d->wip_direct;
			$indirect   = (float)$d->wip_indirect;
			$foh        = (float)$d->wip_foh;
			$consumable = (float)$d->wip_consumable;
			$cogs       = $material + $direct + $indirect + $foh + $consumable;
			$totalCogs += $cogs;

			$coaWip = ($d->product == 'pipe') ? '1103-03-02' : '1103-03-03';
			$ket    = $d->keterangan.','.$d->product.','.$d->no_spk.','.$d->no_so;
			$ketFg  = 'FINISHED GOOD,'.$d->product.','.$d->no_spk.','.$d->no_so;
			$ketCg  = 'COGS,'.$d->product.','.$d->no_spk.','.$d->no_so;
			$reff   = $d->id_trans.','.$d->no_so;

			$set = [
				['5101-01-01', $ket,   $material,   0],
				['5101-03-01', $ket,   $direct,     0],
				['5101-04-01', $ket,   $indirect,   0],
				['5101-05-01', $ket,   $foh,        0],
				['5101-02-01', $ket,   $consumable, 0],
				[$coaWip,      $ket,   0,           $cogs],
				['1103-04-01', $ketFg, $cogs,       0],
				['5103-01-01', $ketCg, 0,           $cogs],
			];
			foreach($set as $s){
				$lines[] = [
					'no_perkiraan'=>$s[0], 'keterangan'=>$s[1], 'debet'=>$s[2], 'kredit'=>$s[3],
					'no_reff'=>$reff, 'no_request'=>$d->no_spk, 'id_trans_stok'=>$d->id,
				];
				$debet += $s[2]; $kredit += $s[3];
			}
		}

		if($totalCogs == 0){
			return ['status'=>'skip','msg'=>'nilai cogs 0, tidak ada yang dijurnal'];
		}
		if(round($debet, 2) != round($kredit, 2)){
			return ['status'=>'fail','msg'=>'tidak balance: debet='.number_format($debet,2).' kredit='.number_format($kredit,2)];
		}

		// Header javh yatim dari proses asli yang gagal di tengah
		$orphan = $db2->query("SELECT h.nomor FROM javh h
			WHERE h.memo = ".$db2->escape($idtrans)." AND h.tgl = ".$db2->escape($tgl)."
			  AND h.keterangan LIKE 'WIP-Finishgood%'
			  AND NOT EXISTS (SELECT 1 FROM jurnal j WHERE j.nomor = h.nomor)
			ORDER BY h.nomor ASC LIMIT 1")->row();
		$orphanNo = $orphan ? $orphan->nomor : NULL;

		$info = count($rows).' FG dijurnal | cogs '.number_format($totalCogs).' | '.count($lines).' baris'
			.($orphanNo ? ' | pakai header yatim '.$orphanNo : ' | header baru').$dupNote;

		if(!$run){
			return ['status'=>'ok','msg'=>'akan dijurnal: '.$info];
		}

		// ---------- EKSEKUSI ----------
		$noReffStok = getReffTranStok('101');
		$Nomor_JV   = $orphanNo ?: $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl);
		$Bln = substr($tgl, 5, 2);
		$Thn = substr($tgl, 0, 4);
		$err = NULL;

		$this->db->trans_begin();

		$jt = [];
		foreach($lines as $l){
			$jt[] = [
				'nomor'=>'', 'tanggal'=>$tgl, 'tipe'=>'JV', 'no_perkiraan'=>$l['no_perkiraan'],
				'keterangan'=>$l['keterangan'], 'no_reff'=>$l['no_reff'], 'debet'=>$l['debet'], 'kredit'=>$l['kredit'],
				'jenis_jurnal'=>self::JENIS_JURNAL, 'no_request'=>$l['no_request'], 'stspos'=>1,
				'id_trans_stok'=>$l['id_trans_stok'], 'reff_trans_stok'=>$noReffStok,
			];
		}
		if(!$this->db->insert_batch('jurnaltras', $jt)){
			$err = 'jurnaltras: '.$this->db->error()['message'];
		}

		if(!$err){
			$this->db->where_in('id', $ids)->update('data_erp_fg', ['reff_trans_stok'=>$noReffStok]);
			if($this->db->error()['code']){ $err = 'update data_erp_fg: '.$this->db->error()['message']; }
		}

		if(!$err && $doStok){
			$err = $this->update_stok($rows);
		}

		if(!$err){
			$db2->trans_begin();
			if(!$orphanNo){
				$first = $rows[0];
				$ketHead = 'WIP-Finishgood'.$first->keterangan.','.$first->product.','.$first->no_spk.','.$first->no_so.$idtrans.' No. Produksi'.$idtrans;
				$ok = $db2->insert('javh', [
					'nomor'=>$Nomor_JV, 'tgl'=>$tgl, 'jml'=>$totalCogs, 'koreksi_no'=>'-', 'kdcab'=>'101', 'jenis'=>'JV',
					'keterangan'=>$ketHead, 'bulan'=>$Bln, 'tahun'=>$Thn, 'user_id'=>$username, 'memo'=>$idtrans,
					'tgl_jvkoreksi'=>$tgl, 'ho_valid'=>'',
				]);
				if(!$ok){ $err = 'javh: '.$db2->error()['message']; }
			}
			if(!$err){
				foreach($lines as $l){
					$ok = $db2->insert('jurnal', [
						'tipe'=>'JV', 'nomor'=>$Nomor_JV, 'tanggal'=>$tgl, 'no_perkiraan'=>$l['no_perkiraan'],
						'keterangan'=>$l['keterangan'], 'no_reff'=>$l['no_reff'], 'debet'=>$l['debet'], 'kredit'=>$l['kredit'],
						'id_trans_stok'=>$l['id_trans_stok'], 'reff_trans_stok'=>$noReffStok,
					]);
					if(!$ok){ $err = 'jurnal: '.$db2->error()['message']; break; }
				}
			}
			if($err || $db2->trans_status() === FALSE){
				$db2->trans_rollback();
				if(!$err){ $err = 'rollback accounting'; }
			} else {
				$db2->trans_commit();
			}
		}

		if($err){
			$this->db->trans_rollback();
			return ['status'=>'fail','msg'=>'GAGAL ('.$err.') - ERP di-rollback'];
		}

		$this->db->trans_commit();
		updateKonterReff('101');

		return ['status'=>'ok','msg'=>'TERJURNAL '.$Nomor_JV.' | '.$info];
	}

	/**
	 * Sama dengan efek stok di Qc::jurnalFG: WIP -1 per unit FG, FG +n.
	 * Hanya dipakai bila stok=1.
	 */
	private function update_stok($rows){
		$first = $rows[0];
		$n = count($rows);

		$this->db->query("UPDATE warehouse_stock_wip SET qty = qty - ".(int)$n."
			WHERE no_so = ".$this->db->escape($first->no_so)." AND kode_trans = ".$this->db->escape($first->kode_trans)."
			  AND no_spk = ".$this->db->escape($first->no_spk)." AND product = ".$this->db->escape($first->product));
		if($this->db->error()['code']){ return 'warehouse_stock_wip: '.$this->db->error()['message']; }

		$cek = $this->db->query("SELECT id FROM warehouse_stock_fg
			WHERE kode_trans = ".$this->db->escape($first->kode_trans)." AND no_so = ".$this->db->escape($first->no_so)."
			  AND no_spk = ".$this->db->escape($first->no_spk)." AND product = ".$this->db->escape($first->product)." LIMIT 1")->row();
		if($cek){
			$this->db->query("UPDATE warehouse_stock_fg SET qty = qty + ".(int)$n." WHERE id = ".(int)$cek->id);
		} else {
			$wg = $this->db->query("SELECT * FROM data_erp_wip_group
				WHERE kode_trans = ".$this->db->escape($first->kode_trans)." AND jenis = 'out'
				  AND tanggal = ".$this->db->escape($first->tanggal)." ORDER BY id ASC LIMIT 1")->row();
			if(!$wg){ return 'warehouse_stock_fg: data_erp_wip_group out tidak ditemukan'; }
			$tot = (float)$wg->qty;
			$this->db->insert('warehouse_stock_fg', [
				'tanggal'=>$first->tanggal, 'keterangan'=>'WIP To FG', 'no_so'=>$wg->no_so, 'product'=>$wg->product,
				'no_spk'=>$wg->no_spk, 'kode_trans'=>$wg->kode_trans, 'id_pro_det'=>$wg->id_pro_det, 'qty'=>$n,
				'nilai_wip'=>($tot > 0) ? $wg->nilai_wip / $tot : 0, 'material'=>$wg->material,
				'wip_direct'=>$wg->wip_direct, 'wip_indirect'=>$wg->wip_indirect,
				'wip_consumable'=>$wg->wip_consumable, 'wip_foh'=>$wg->wip_foh,
				'created_by'=>$wg->created_by, 'created_date'=>$wg->created_date, 'id_trans'=>$wg->id_trans,
			]);
		}
		if($this->db->error()['code']){ return 'warehouse_stock_fg: '.$this->db->error()['message']; }
		return NULL;
	}
}
