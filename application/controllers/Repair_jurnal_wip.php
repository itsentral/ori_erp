<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backfill jurnal WIP closing SPK untuk data_erp_wip yang sudah masuk
 * tetapi belum terjurnal (mis. akibat kolom di live belum ditambahkan).
 *
 * Logika jurnal mengikuti Produksi::jurnalWIP().
 *
 * Pemakaian (default = DRY RUN, tidak menulis apa pun):
 *   /repair_jurnal_wip?from=2026-09-12&to=2026-11-12
 *   /repair_jurnal_wip?from=2026-09-12&to=2026-11-12&run=1&limit=20
 *   /repair_jurnal_wip?id_trans=1234&run=1          (satu transaksi saja)
 *
 * Parameter:
 *   from, to   : rentang data_erp_wip.tanggal (default 2026-09-12 s/d 2026-11-12)
 *   id_trans   : batasi ke satu id_trans
 *   run=1      : eksekusi sungguhan
 *   limit      : maksimal id_trans yang diproses per request (default 50)
 *   stok=0     : jangan backfill warehouse_stock_wip (default 1)
 */
class Repair_jurnal_wip extends CI_Controller {

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
		$doStok   = ($this->input->get('stok') !== '0');
		$username = $this->session->userdata['ORI_User']['username'];

		$this->db->db_debug = FALSE;

		$this->out('MODE   : '.($run ? 'RUN (menulis data)' : 'DRY RUN (tidak menulis data)'));
		$this->out("RANGE  : $from s/d $to | limit $limit | stok_wip ".($doStok ? 'ya' : 'tidak'));
		$this->out(str_repeat('-', 90));

		// Kandidat: id_trans yang belum pernah dijurnal
		$where = "tanggal BETWEEN ".$this->db->escape($from)." AND ".$this->db->escape($to);
		if($idTrans > 0){
			$where .= " AND id_trans = ".$idTrans;
		}
		$sql = "SELECT id_trans, MIN(tanggal) AS tanggal, MAX(kode_trans) AS kode_trans, MAX(no_spk) AS no_spk
				FROM data_erp_wip
				WHERE $where AND id_trans > 0
				GROUP BY id_trans
				HAVING SUM(reff_trans_stok IS NOT NULL AND reff_trans_stok <> '') = 0
				ORDER BY MIN(tanggal) ASC, id_trans ASC";
		$candidates = $this->db->query($sql);
		if(!$candidates){
			$this->out('ERROR query kandidat: '.$this->db->error()['message']);
			return;
		}
		$candidates = $candidates->result_array();

		// Baris dengan id_trans = 0 perlu diperbaiki manual (tidak bisa dijurnal otomatis)
		$zero = $this->db->query("SELECT COUNT(*) AS n FROM data_erp_wip WHERE tanggal BETWEEN ".$this->db->escape($from)." AND ".$this->db->escape($to)." AND (id_trans IS NULL OR id_trans = 0)")->row();
		if(!empty($zero->n)){
			$this->out("PERINGATAN: ada {$zero->n} baris data_erp_wip dengan id_trans 0/NULL, TIDAK diproses (cek manual).");
		}

		$this->out('Kandidat belum terjurnal: '.count($candidates).' id_trans');
		$this->out(str_repeat('-', 90));

		$cnt = ['ok'=>0,'skip'=>0,'fail'=>0];
		$processed = 0;
		foreach($candidates as $cand){
			if($processed >= $limit){
				$this->out("Batas limit $limit tercapai. Jalankan ulang untuk sisanya.");
				break;
			}
			$processed++;
			$res = $this->process_one($cand['id_trans'], $run, $doStok, $username);
			$cnt[$res['status']]++;
			$this->out(sprintf('[%s] id_trans=%s | %s | %s | %s',
				strtoupper($res['status']), $cand['id_trans'], $cand['tanggal'], $cand['no_spk'], $res['msg']));
		}

		$this->out(str_repeat('-', 90));
		$this->out("SELESAI: ok={$cnt['ok']} skip={$cnt['skip']} fail={$cnt['fail']}");
		if(!$run){
			$this->out('Ini DRY RUN. Tambahkan &run=1 untuk eksekusi sungguhan.');
		}
	}

	private function process_one($idtrans, $run, $doStok, $username){
		$rows = $this->db->query("SELECT * FROM data_erp_wip WHERE id_trans = ".(int)$idtrans." ORDER BY id ASC")->result();
		if(empty($rows)){
			return ['status'=>'skip','msg'=>'data_erp_wip kosong'];
		}

		// Guard 1: sudah ada di gl_interface_detail
		$ids = array_map(function($r){ return (int)$r->id; }, $rows);
		$ada = $this->db->query("SELECT COUNT(*) AS n FROM gl_interface_detail WHERE id_trans_stok IN (".implode(',', $ids).")")->row();
		if(!empty($ada->n)){
			return ['status'=>'skip','msg'=>'sudah ada di gl_interface_detail ('.$ada->n.' baris), cek status gl_interface'];
		}

		// Susun baris jurnal (mengikuti Produksi::jurnalWIP)
		$lines = [];
		$debetTotal = 0;
		$kreditTotal = 0;
		$tgl = $rows[0]->tanggal;
		$noSo = $rows[0]->no_so;
		$product = $rows[0]->product;
		$noSpk = $rows[0]->no_spk;

		foreach($rows as $data){
			$kredit = round((float)$data->total_price);
			$debet  = round((float)$data->total_price_debet);

			if($debet != 0){
				$coa = ($data->product == 'pipe') ? '1103-03-02' : '1103-03-03';
				$lines[] = $this->line($data, $coa, $debet, 0);
				$debetTotal += $debet;
			}
			elseif($kredit != 0){
				switch($data->nm_material){
					case 'WIP Direct labour':   $coa = '2107-01-02'; break;
					case 'WIP Indirect labour': $coa = '2107-01-03'; break;
					case 'WIP Consumable':      $coa = '2107-01-01'; break;
					case 'WIP FOH':             $coa = '2107-01-04'; break;
					default:                    $coa = '1103-01-03';
				}
				$lines[] = $this->line($data, $coa, 0, $kredit);
				$kreditTotal += $kredit;
			}
		}

		if(empty($lines)){
			return ['status'=>'skip','msg'=>'tidak ada nilai untuk dijurnal'];
		}
		if($debetTotal != $kreditTotal){
			return ['status'=>'fail','msg'=>"tidak balance: debet=".number_format($debetTotal)." kredit=".number_format($kreditTotal)];
		}

		// Guard 2: jurnal sudah ada di accounting (no_reff + tanggal + akun WIP)
		$db2 = $this->load->database('accounting', TRUE);
		$db2->db_debug = FALSE;
		$noReff = $idtrans.','.$noSo;
		$dup = $db2->query("SELECT COUNT(*) AS n FROM jurnal WHERE tipe='JV' AND no_reff = ".$db2->escape($noReff)." AND tanggal = ".$db2->escape($tgl)." AND no_perkiraan IN ('1103-03-02','1103-03-03')")->row();
		if(!empty($dup->n)){
			return ['status'=>'skip','msg'=>'jurnal sudah ada di accounting (no_reff '.$noReff.'), cek manual'];
		}

		$info = 'debet=kredit '.number_format($debetTotal).' | '.count($lines).' baris';

		// Stok WIP (backfill warehouse_stock_wip) bila belum ada
		$stokRow = NULL;
		if($doStok){
			$cekStok = $this->db->query("SELECT COUNT(*) AS n FROM warehouse_stock_wip WHERE id_trans = ".(int)$idtrans)->row();
			if(empty($cekStok->n)){
				$stokRow = $this->db->query("SELECT * FROM data_erp_wip_group WHERE id_trans = ".(int)$idtrans." AND jenis = 'in' ORDER BY id ASC LIMIT 1")->row();
				$info .= $stokRow ? ' | +warehouse_stock_wip' : ' | (data_erp_wip_group tidak ditemukan)';
			}
		}

		if(!$run){
			return ['status'=>'ok','msg'=>'akan dijurnal: '.$info];
		}

		// ---------- EKSEKUSI ----------
		$now = date('Y-m-d H:i:s');
		$Bln = substr($tgl, 5, 2);
		$Thn = substr($tgl, 0, 4);
		$ket = 'Jurnal Produksi - WIP';

		$this->db->trans_begin();
		$noReffStok = getReffTranStok('101');
		$Nomor_JV   = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl);
		$headKet    = $ket.$idtrans.' No. Produksi'.$idtrans;

		$err = NULL;

		// jurnaltras
		$jurnaltras = [];
		foreach($lines as $l){
			$jurnaltras[] = [
				'nomor'=>'', 'tanggal'=>$tgl, 'tipe'=>'JV', 'no_perkiraan'=>$l['no_perkiraan'],
				'keterangan'=>$l['keterangan'], 'no_reff'=>$noReff, 'debet'=>$l['debet'], 'kredit'=>$l['kredit'],
				'jenis_jurnal'=>'produksi wip', 'no_request'=>$idtrans, 'stspos'=>1,
				'id_trans_stok'=>$l['id_trans_stok'], 'reff_trans_stok'=>$noReffStok,
			];
		}
		if(!$this->db->insert_batch('jurnaltras', $jurnaltras)){
			$err = 'jurnaltras: '.$this->db->error()['message'];
		}

		// gl_interface (header)
		$idGl = 0;
		if(!$err){
			$ok = $this->db->insert('gl_interface', [
				'nomor'=>$Nomor_JV, 'tgl'=>$tgl, 'jml'=>$debetTotal, 'koreksi_no'=>'-', 'kdcab'=>'101', 'jenis'=>'JV',
				'keterangan'=>$headKet, 'bulan'=>$Bln, 'tahun'=>$Thn, 'user_id'=>$username, 'memo'=>$idtrans,
				'tgl_jvkoreksi'=>$tgl, 'ho_valid'=>'', 'jenis_transaksi'=>'wip', 'status'=>'pending',
				'posted_at'=>NULL, 'error_msg'=>NULL,
			]);
			if(!$ok){ $err = 'gl_interface: '.$this->db->error()['message']; }
			else { $idGl = $this->db->insert_id(); }
		}

		// gl_interface_detail
		$glDetail = [];
		if(!$err){
			foreach($lines as $l){
				$glDetail[] = [
					'id_gl_interface'=>$idGl, 'no_batch'=>$Nomor_JV, 'tipe'=>'JV', 'tanggal'=>$tgl,
					'no_perkiraan'=>$l['no_perkiraan'], 'keterangan'=>$l['keterangan'], 'no_reff'=>$noReff,
					'debet'=>$l['debet'], 'kredit'=>$l['kredit'], 'jenis_jurnal'=>'produksi wip',
					'no_request'=>$idtrans, 'id_trans_stok'=>$l['id_trans_stok'], 'reff_trans_stok'=>$noReffStok,
				];
			}
			if(!$this->db->insert_batch('gl_interface_detail', $glDetail)){
				$err = 'gl_interface_detail: '.$this->db->error()['message'];
			}
		}

		// update reff_trans_stok di data_erp_wip
		if(!$err){
			$this->db->where_in('id', $ids)->update('data_erp_wip', ['reff_trans_stok'=>$noReffStok]);
			if($this->db->error()['code']){ $err = 'update data_erp_wip: '.$this->db->error()['message']; }
		}

		// warehouse_stock_wip
		if(!$err && $stokRow){
			$qty = (float)$stokRow->qty;
			$ok = $this->db->insert('warehouse_stock_wip', [
				'tanggal'=>$tgl, 'keterangan'=>'Gudang produksi to WIP', 'no_so'=>$stokRow->no_so,
				'product'=>$stokRow->product, 'no_spk'=>$stokRow->no_spk, 'kode_trans'=>$stokRow->kode_trans,
				'id_pro_det'=>$stokRow->id_pro_det, 'qty'=>$stokRow->qty,
				'nilai_wip'=>($qty > 0) ? round($stokRow->nilai_wip / $qty, 0) : 0,
				'material'=>$stokRow->material, 'wip_direct'=>$stokRow->wip_direct,
				'wip_indirect'=>$stokRow->wip_indirect, 'wip_consumable'=>$stokRow->wip_consumable,
				'wip_foh'=>$stokRow->wip_foh, 'created_by'=>$stokRow->created_by,
				'created_date'=>$stokRow->created_date, 'id_trans'=>$stokRow->id_trans,
			]);
			if(!$ok){ $err = 'warehouse_stock_wip: '.$this->db->error()['message']; }
		}

		// Posting ke accounting
		if(!$err){
			$db2->trans_begin();
			$ok = $db2->insert('javh', [
				'nomor'=>$Nomor_JV, 'tgl'=>$tgl, 'jml'=>$debetTotal, 'koreksi_no'=>'-', 'kdcab'=>'101', 'jenis'=>'JV',
				'keterangan'=>$headKet, 'bulan'=>$Bln, 'tahun'=>$Thn, 'user_id'=>$username, 'memo'=>$idtrans,
				'tgl_jvkoreksi'=>$tgl, 'ho_valid'=>'',
			]);
			if(!$ok){ $err = 'javh: '.$db2->error()['message']; }

			if(!$err){
				foreach($glDetail as $d){
					$ok = $db2->insert('jurnal', [
						'tipe'=>'JV', 'nomor'=>$Nomor_JV, 'tanggal'=>$d['tanggal'],
						'no_perkiraan'=>$d['no_perkiraan'], 'keterangan'=>$d['keterangan'],
						'no_reff'=>$d['no_reff'], 'debet'=>$d['debet'], 'kredit'=>$d['kredit'],
						'id_trans_stok'=>$d['id_trans_stok'], 'reff_trans_stok'=>$d['reff_trans_stok'],
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

		$this->db->where('id', $idGl)->update('gl_interface', ['status'=>'posted', 'posted_at'=>$now]);
		$this->db->trans_commit();
		updateKonterReff('101');

		return ['status'=>'ok','msg'=>'TERJURNAL '.$Nomor_JV.' | '.$info];
	}

	private function line($data, $coa, $debet, $kredit){
		return [
			'no_perkiraan'  => $coa,
			'keterangan'    => $data->nm_material,
			'debet'         => $debet,
			'kredit'        => $kredit,
			'id_trans_stok' => $data->id,
		];
	}
}
