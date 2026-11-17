<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backfill jurnal modul Delivery untuk data_erp_in_transit yang sudah masuk
 * tetapi belum terjurnal (reff_trans_stok kosong).
 *
 *   tipe=intransit (default) : mengikuti Delivery::jurnalIntransit()
 *                              jenis 'in', keterangan 'Finish Good to In Transit'
 *                              D 1103-04-06 Intransit / K 1103-04-01 Finish Good
 *   tipe=customer            : mengikuti Delivery::jurnalIntransitCustomer()
 *                              jenis 'out', keterangan 'In Transit to Customer'
 *                              D 1103-04-07 In Customer / K 1103-04-06 Intransit
 *
 * Satu JV per (kode_delivery, created_date), sama dengan satu kali proses asli.
 * Stok (warehouse_stock_fg / intransit / incustomer) TIDAK diubah.
 *
 * Pemakaian (default = DRY RUN):
 *   /repair_jurnal_intransit?tipe=intransit&from=2026-09-12&to=2026-09-30&limit=1000
 *   /repair_jurnal_intransit?tipe=intransit&kode_delivery=DV-xxx&run=1
 *   /repair_jurnal_intransit?tipe=customer&from=2026-09-12&to=2026-09-30&limit=1000&run=1
 *
 * Parameter:
 *   tipe          : intransit | customer
 *   from, to      : rentang data_erp_in_transit.tanggal (default 2026-09-12 s/d 2026-11-12)
 *   kode_delivery : batasi ke satu delivery
 *   run=1         : eksekusi sungguhan
 *   limit         : maksimal grup per request (default 50)
 *   dedup=0       : matikan pembuangan duplikat (default aktif)
 */
class Repair_jurnal_intransit extends CI_Controller {

	const JENIS_JURNAL = 'Finishgood-Intransit';

	private $cfg;
	private $dedup = TRUE;

	private $TIPE = [
		'intransit' => [
			'jenis'      => 'in',
			'ket_src'    => 'Finish Good to In Transit',
			'ket_jurnal' => 'FINISHED GOOD - INTRANSIT',
			'coa_debet'  => '1103-04-06',
			'coa_kredit' => '1103-04-01',
			'created_by' => 'intransit',
		],
		'customer' => [
			'jenis'      => 'out',
			'ket_src'    => 'In Transit to Customer',
			'ket_jurnal' => 'INTRANSIT-CUSTOMER',
			'coa_debet'  => '1103-04-07',
			'coa_kredit' => '1103-04-06',
			'created_by' => 'customer',
		],
	];

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

		$tipe     = $this->input->get('tipe') ?: 'intransit';
		if(!isset($this->TIPE[$tipe])){
			$this->out('tipe tidak dikenal. Pakai tipe=intransit atau tipe=customer');
			return;
		}
		$this->cfg = $this->TIPE[$tipe];

		$from     = $this->input->get('from') ?: '2026-09-12';
		$to       = $this->input->get('to') ?: '2026-11-12';
		$kodeDel  = trim((string)$this->input->get('kode_delivery'));
		$run      = ($this->input->get('run') == '1');
		$limit    = (int) ($this->input->get('limit') ?: 50);
		$this->dedup = ($this->input->get('dedup') !== '0');
		$username = $this->session->userdata['ORI_User']['username'];

		$this->db->db_debug = FALSE;

		$this->out('MODE   : '.($run ? 'RUN (menulis data)' : 'DRY RUN (tidak menulis data)'));
		$this->out('TIPE   : '.$tipe.' ('.$this->cfg['ket_src'].', jenis '.$this->cfg['jenis'].')'
			.' | D '.$this->cfg['coa_debet'].' / K '.$this->cfg['coa_kredit']);
		$this->out("RANGE  : $from s/d $to | limit $limit | buang duplikat ".($this->dedup ? 'YA' : 'tidak').' | stok TIDAK diubah');
		$this->out(str_repeat('-', 100));

		$where = "tanggal BETWEEN ".$this->db->escape($from)." AND ".$this->db->escape($to)
			." AND jenis = ".$this->db->escape($this->cfg['jenis'])
			." AND keterangan = ".$this->db->escape($this->cfg['ket_src'])
			." AND (reff_trans_stok IS NULL OR reff_trans_stok = '')";
		if($kodeDel !== ''){
			$where .= " AND kode_delivery = ".$this->db->escape($kodeDel);
		}

		$noDel = $this->db->query("SELECT COUNT(*) AS n FROM data_erp_in_transit WHERE $where AND (kode_delivery IS NULL OR kode_delivery = '')")->row();
		if($noDel === NULL){
			$this->out('ERROR: '.$this->db->error()['message']);
			return;
		}
		if(!empty($noDel->n)){
			$this->out("PERINGATAN: {$noDel->n} baris tanpa kode_delivery, TIDAK diproses (cek manual).");
		}

		$groups = $this->db->query("SELECT kode_delivery, created_date, MIN(tanggal) AS tanggal, COUNT(*) AS n
				FROM data_erp_in_transit
				WHERE $where AND kode_delivery IS NOT NULL AND kode_delivery <> ''
				GROUP BY kode_delivery, created_date
				ORDER BY MIN(tanggal) ASC, created_date ASC, kode_delivery ASC")->result_array();

		$this->out('Kandidat belum terjurnal: '.count($groups).' grup (kode_delivery + created_date)');
		$this->out(str_repeat('-', 100));

		$cnt = ['ok'=>0,'skip'=>0,'fail'=>0];
		$processed = 0;
		foreach($groups as $g){
			if($processed >= $limit){
				$this->out("Batas limit $limit tercapai. Jalankan ulang untuk sisanya.");
				break;
			}
			$processed++;
			$res = $this->process_group($g['kode_delivery'], $g['created_date'], $where, $run, $username);
			$cnt[$res['status']]++;
			$this->out(sprintf('[%s] %s | %s | %s baris | %s',
				strtoupper($res['status']), $g['kode_delivery'], $g['tanggal'], $g['n'], $res['msg']));
		}

		$this->out(str_repeat('-', 100));
		$this->out("SELESAI: ok={$cnt['ok']} skip={$cnt['skip']} fail={$cnt['fail']}");
		if(!$run){
			$this->out('Ini DRY RUN. Tambahkan &run=1 untuk eksekusi sungguhan.');
		}
	}

	private function process_group($kodeDel, $createdDate, $where, $run, $username){
		$cfg = $this->cfg;
		$rows = $this->db->query("SELECT * FROM data_erp_in_transit WHERE $where AND kode_delivery = ".$this->db->escape($kodeDel)
			." AND created_date ".($createdDate === NULL ? "IS NULL" : "= ".$this->db->escape($createdDate))
			." ORDER BY id ASC")->result();
		if(empty($rows)){
			return ['status'=>'skip','msg'=>'data kosong'];
		}

		// Duplikat: baris identik dari proses close yang terkirim dua kali (created_date lebih awal)
		$dupNote = '';
		if($this->dedup){
			$allIds = implode(',', array_map(function($r){ return (int)$r->id; }, $rows));
			$dupRows = $this->db->query("SELECT f.id, MIN(o.id) AS orig_id
				FROM data_erp_in_transit f
				JOIN data_erp_in_transit o
				  ON o.kode_delivery = f.kode_delivery AND o.jenis = f.jenis AND o.keterangan = f.keterangan
				 AND o.created_date < f.created_date
				 AND o.kode_trans <=> f.kode_trans AND o.id_pro <=> f.id_pro
				 AND o.id_material <=> f.id_material AND o.qty_mat <=> f.qty_mat
				 AND o.nilai_unit <=> f.nilai_unit
				WHERE f.id IN ($allIds)
				GROUP BY f.id")->result();
			if(!empty($dupRows)){
				$dupMap = [];
				foreach($dupRows as $dr){ $dupMap[(int)$dr->id] = (int)$dr->orig_id; }
				$rows = array_values(array_filter($rows, function($r) use ($dupMap){ return !isset($dupMap[(int)$r->id]); }));
				$origs = array_unique(array_values($dupMap)); sort($origs);
				$dupNote = ' | '.count($dupMap).' baris duplikat diabaikan (id: '.implode(',', array_keys($dupMap)).', asli: '.implode(',', $origs).')';
				if(empty($rows)){
					return ['status'=>'skip','msg'=>'DUPLIKAT, tidak dijurnal'.$dupNote];
				}
			}
		}

		$ids = array_map(function($r){ return (int)$r->id; }, $rows);
		$idList = implode(',', $ids);

		$db2 = $this->load->database('accounting', TRUE);
		$db2->db_debug = FALSE;

		// Guard dobel (dibatasi jenis/akun agar tidak bentrok dengan id tabel lain)
		$dupErp = $this->db->query("SELECT COUNT(*) AS n FROM jurnaltras
			WHERE jenis_jurnal = ".$this->db->escape(self::JENIS_JURNAL)."
			  AND no_perkiraan = ".$this->db->escape($cfg['coa_debet'])."
			  AND keterangan = ".$this->db->escape($cfg['ket_jurnal'])."
			  AND id_trans_stok IN ($idList)")->row();
		if(!empty($dupErp->n)){
			return ['status'=>'skip','msg'=>'sudah ada di jurnaltras ('.$dupErp->n.' baris), cek manual'];
		}
		$dupAcc = $db2->query("SELECT COUNT(*) AS n FROM jurnal
			WHERE no_perkiraan = ".$db2->escape($cfg['coa_debet'])."
			  AND keterangan LIKE ".$db2->escape($cfg['ket_jurnal'].'%')."
			  AND id_trans_stok IN ($idList)")->row();
		if($dupAcc === NULL){
			return ['status'=>'fail','msg'=>'cek gl.jurnal gagal: '.$db2->error()['message']];
		}
		if(!empty($dupAcc->n)){
			return ['status'=>'skip','msg'=>'sudah ada di gl.jurnal ('.$dupAcc->n.' baris), cek manual'];
		}

		// Susun jurnal
		$tgl   = $rows[0]->tanggal;
		$total = 0;
		$lines = [];
		$last  = end($rows);
		$ketHeadBase = $cfg['ket_jurnal'].$last->keterangan.','.$last->product.','.$last->no_spk.','.$last->no_so;

		foreach($rows as $d){
			$nilai = (float)$d->nilai_unit;
			$total += $nilai;
			if($cfg['jenis'] == 'in'){
				$reff = $kodeDel.$d->no_so;
				$req  = $d->no_spk;
			} else {
				$reff = $kodeDel;
				$req  = $kodeDel;
			}
			$lines[] = ['no_perkiraan'=>$cfg['coa_debet'],  'debet'=>$nilai, 'kredit'=>0,      'no_reff'=>$reff, 'no_request'=>$req, 'id_trans_stok'=>$d->id];
			$lines[] = ['no_perkiraan'=>$cfg['coa_kredit'], 'debet'=>0,      'kredit'=>$nilai, 'no_reff'=>$reff, 'no_request'=>$req, 'id_trans_stok'=>$d->id];
		}

		if($total == 0){
			return ['status'=>'skip','msg'=>'nilai_unit total 0, tidak ada yang dijurnal'.$dupNote];
		}

		// Header javh yatim dari proses asli (header masuk, detail gagal)
		$orphan = $db2->query("SELECT h.nomor FROM javh h
			WHERE h.tgl = ".$db2->escape($tgl)."
			  AND h.keterangan LIKE ".$db2->escape($cfg['ket_jurnal'].'%')."
			  AND h.keterangan LIKE ".$db2->escape('%'.$kodeDel.' No. Produksi'.$kodeDel)."
			  AND NOT EXISTS (SELECT 1 FROM jurnal j WHERE j.nomor = h.nomor)
			ORDER BY h.nomor ASC LIMIT 1")->row();
		$orphanNo = $orphan ? $orphan->nomor : NULL;

		$info = count($rows).' baris dijurnal | nilai '.number_format($total).' | '.count($lines).' baris jurnal'
			.($orphanNo ? ' | pakai header yatim '.$orphanNo : ' | header baru').$dupNote;

		if(!$run){
			return ['status'=>'ok','msg'=>'akan dijurnal: '.$info];
		}

		// ---------- EKSEKUSI ----------
		$noReffStok = getReffTranStok('101');
		$Nomor_JV   = $orphanNo ?: $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl);
		$Bln = substr($tgl, 5, 2);
		$Thn = substr($tgl, 0, 4);
		$ketHead = $ketHeadBase.$kodeDel.' No. Produksi'.$kodeDel;
		$err = NULL;

		$this->db->trans_begin();

		$jt = [];
		foreach($lines as $l){
			$jt[] = [
				'nomor'=>'', 'tanggal'=>$tgl, 'tipe'=>'JV', 'no_perkiraan'=>$l['no_perkiraan'],
				'keterangan'=>$cfg['ket_jurnal'], 'no_reff'=>$l['no_reff'], 'debet'=>$l['debet'], 'kredit'=>$l['kredit'],
				'jenis_jurnal'=>self::JENIS_JURNAL, 'no_request'=>$l['no_request'], 'stspos'=>1,
				'id_trans_stok'=>$l['id_trans_stok'], 'reff_trans_stok'=>$noReffStok,
			];
		}
		if(!$this->db->insert_batch('jurnaltras', $jt)){
			$err = 'jurnaltras: '.$this->db->error()['message'];
		}

		if(!$err){
			$this->db->where_in('id', $ids)->update('data_erp_in_transit', ['reff_trans_stok'=>$noReffStok]);
			if($this->db->error()['code']){ $err = 'update data_erp_in_transit: '.$this->db->error()['message']; }
		}

		if(!$err){
			$db2->trans_begin();
			if(!$orphanNo){
				$ok = $db2->insert('javh', [
					'nomor'=>$Nomor_JV, 'tgl'=>$tgl, 'jml'=>$total, 'koreksi_no'=>'-', 'kdcab'=>'101', 'jenis'=>'JV',
					'keterangan'=>$ketHead, 'bulan'=>$Bln, 'tahun'=>$Thn, 'user_id'=>$username,
					'memo'=>($cfg['jenis'] == 'in') ? 'intransit' : $kodeDel,
					'tgl_jvkoreksi'=>$tgl, 'ho_valid'=>'',
				]);
				if(!$ok){ $err = 'javh: '.$db2->error()['message']; }
			}
			if(!$err){
				$now = date('Y-m-d H:i:s');
				foreach($lines as $l){
					$ok = $db2->insert('jurnal', [
						'tipe'=>'JV', 'nomor'=>$Nomor_JV, 'tanggal'=>$tgl, 'no_perkiraan'=>$l['no_perkiraan'],
						'keterangan'=>$ketHeadBase, 'no_reff'=>$l['no_reff'], 'debet'=>$l['debet'], 'kredit'=>$l['kredit'],
						'created_on'=>$now, 'created_by'=>$cfg['created_by'],
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
}
