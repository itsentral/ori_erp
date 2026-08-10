<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_opname_generate extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('stock_opname_generate_model');
		$this->load->model('master_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	/**
	 * Halaman utama - tampilkan form pilih tanggal dan list data
	 */
	public function index(){
		$controller = ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses  = getAcccesmenu($controller);

		$data = array(
			'title'      => 'Generate Stock Opname Per Day',
			'action'     => 'stock_opname_generate',
			'akses_menu' => $Arr_Akses,
		);
		$this->load->view('Stock_opname_generate/index', $data);
	}

	/**
	 * Server-side DataTable untuk menampilkan data warehouse_stock_per_day_duplikat
	 */
	public function server_side(){
		$date_filter = $this->input->post('date_filter');
		$this->stock_opname_generate_model->get_data_json($date_filter);
	}

	/**
	 * Cek apakah data pada tanggal tertentu sudah ada
	 */
	public function check_date(){
		$date = $this->input->post('date');
		$count = $this->stock_opname_generate_model->count_by_date($date);
		echo json_encode(array('count' => $count));
	}

	/**
	 * Proses generate data untuk tanggal yang dipilih
	 * Logic:
	 * 1. Ambil data terakhir (tanggal sebelumnya) dari warehouse_stock_per_day_duplikat
	 * 2. Ambil transaksi dari warehouse_history pada tanggal yang dipilih
	 * 3. Hitung saldo baru = saldo sebelumnya +/- transaksi
	 * 4. Insert ke warehouse_stock_per_day_duplikat dengan tanggal baru
	 */
	public function process_generate(){
		$date_target = $this->input->post('date_target');
		$UserName    = $this->session->userdata('username');

		if(empty($date_target)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tanggal harus dipilih!'));
			return;
		}

		// Cek apakah data sudah ada di tanggal tersebut
		$existing = $this->stock_opname_generate_model->count_by_date($date_target);
		if($existing > 0){
			echo json_encode(array('status' => 0, 'pesan' => 'Data pada tanggal '.$date_target.' sudah ada! Hapus terlebih dahulu jika ingin generate ulang.'));
			return;
		}

		// Cari tanggal terakhir yang ada datanya (sebelum tanggal target)
		$last_date = $this->stock_opname_generate_model->get_last_date_before($date_target);
		if(empty($last_date)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data stok opname sebelum tanggal '.$date_target.'. Pastikan data awal (01/01/2026) sudah tersedia.'));
			return;
		}

		// Validasi: tanggal harus berurutan (H+1 dari tanggal terakhir)
		$expected_date = date('Y-m-d', strtotime($last_date.' +1 day'));
		if($date_target != $expected_date){
			echo json_encode(array('status' => 0, 'pesan' => 'Tanggal harus berurutan! Data terakhir tersedia: '.$last_date.'. Generate berikutnya harus tanggal '.$expected_date));
			return;
		}

		// Ambil data stok dari tanggal terakhir
		$data_stok = $this->stock_opname_generate_model->get_stock_by_date($last_date);
		if(empty($data_stok)){
			echo json_encode(array('status' => 0, 'pesan' => 'Data stok pada tanggal '.$last_date.' kosong.'));
			return;
		}

		// Ambil transaksi dari warehouse_history pada tanggal target
		$transaksi = $this->stock_opname_generate_model->get_transactions_by_date($date_target);

		// Index transaksi berdasarkan id_material + id_gudang
		$trans_map = array();
		foreach($transaksi as $trx){
			$mapKey = $trx['id_material'].'_'.$trx['id_gudang'];
			if(!isset($trans_map[$mapKey])){
				$trans_map[$mapKey] = array(
					'qty_in'      => 0,
					'qty_out'     => 0,
					'total_value_in'  => 0,
					'total_value_out' => 0,
				);
			}
			$jumlah = (float)$trx['jumlah_mat'];
			$total_harga = (float)$trx['total_harga'];

			// Tentukan apakah transaksi IN atau OUT berdasarkan field
			// Jika id_gudang = id_gudang_ke → material masuk (IN)
			// Jika id_gudang = id_gudang_dari → material keluar (OUT)
			if($trx['id_gudang'] == $trx['id_gudang_ke'] || $trx['kd_gudang_dari'] == 'PURCHASE'){
				$trans_map[$mapKey]['qty_in'] += abs($jumlah);
				$trans_map[$mapKey]['total_value_in'] += abs($total_harga);
			} else {
				$trans_map[$mapKey]['qty_out'] += abs($jumlah);
				$trans_map[$mapKey]['total_value_out'] += abs($total_harga);
			}
		}

		// Generate data baru
		$ArrInsert = array();
		$DateTime  = date('Y-m-d H:i:s');

		foreach($data_stok as $row){
			$mapKey = $row['id_material'].'_'.$row['id_gudang'];

			$qty_stock_prev  = (float)$row['qty_stock'];
			$costbook_prev   = (float)$row['costbook'];
			$total_val_prev  = (float)$row['total_value'];
			$harga_prev      = (float)$row['harga'];
			$total_harga_prev= (float)$row['total_harga'];

			$qty_in  = 0;
			$qty_out = 0;
			$val_in  = 0;
			$val_out = 0;

			if(isset($trans_map[$mapKey])){
				$qty_in  = $trans_map[$mapKey]['qty_in'];
				$qty_out = $trans_map[$mapKey]['qty_out'];
				$val_in  = $trans_map[$mapKey]['total_value_in'];
				$val_out = $trans_map[$mapKey]['total_value_out'];
			}

			// Hitung qty baru
			$qty_new = $qty_stock_prev + $qty_in - $qty_out;

			// Hitung total_harga baru (nilai persediaan)
			$total_harga_new = $total_harga_prev + $val_in - $val_out;

			// Hitung harga rata-rata baru
			$harga_new = ($qty_new > 0) ? ($total_harga_new / $qty_new) : $harga_prev;

			// Costbook tetap dari price_book (tidak berubah kecuali ada update)
			$costbook_new = $costbook_prev;

			// Total value berdasarkan costbook
			$total_value_new = $costbook_new * $qty_new;

			$ArrInsert[] = array(
				'id_material'  => $row['id_material'],
				'idmaterial'   => $row['idmaterial'],
				'nm_material'  => $row['nm_material'],
				'id_category'  => $row['id_category'],
				'nm_category'  => $row['nm_category'],
				'id_gudang'    => $row['id_gudang'],
				'kd_gudang'    => $row['kd_gudang'],
				'qty_stock'    => $qty_new,
				'qty_booking'  => $row['qty_booking'],
				'qty_rusak'    => $row['qty_rusak'],
				'costbook'     => $costbook_new,
				'total_value'  => $total_value_new,
				'update_by'    => $UserName,
				'update_date'  => $DateTime,
				'hist_by'      => $UserName,
				'hist_date'    => $date_target,
				'harga'        => $harga_new,
				'total_harga'  => $total_harga_new,
			);
		}

		// Cek apakah ada material baru dari transaksi yang belum ada di data sebelumnya
		foreach($transaksi as $trx){
			$mapKey = $trx['id_material'].'_'.$trx['id_gudang'];
			// Cek apakah sudah ada di data_stok
			$exists = false;
			foreach($data_stok as $row){
				if($row['id_material'].'_'.$row['id_gudang'] == $mapKey){
					$exists = true;
					break;
				}
			}
			if(!$exists && ($trx['id_gudang'] == $trx['id_gudang_ke'] || $trx['kd_gudang_dari'] == 'PURCHASE')){
				// Material baru masuk ke gudang, cek apakah sudah di-insert
				$alreadyInserted = false;
				foreach($ArrInsert as $ins){
					if($ins['id_material'].'_'.$ins['id_gudang'] == $mapKey){
						$alreadyInserted = true;
						break;
					}
				}
				if(!$alreadyInserted){
					$jumlah = abs((float)$trx['jumlah_mat']);
					$total_harga_trx = abs((float)$trx['total_harga']);
					$harga_trx = ($jumlah > 0) ? ($total_harga_trx / $jumlah) : 0;

					$ArrInsert[] = array(
						'id_material'  => $trx['id_material'],
						'idmaterial'   => $trx['idmaterial'],
						'nm_material'  => $trx['nm_material'],
						'id_category'  => $trx['id_category'],
						'nm_category'  => $trx['nm_category'],
						'id_gudang'    => $trx['id_gudang'],
						'kd_gudang'    => $trx['kd_gudang'],
						'qty_stock'    => $jumlah,
						'qty_booking'  => 0,
						'qty_rusak'    => 0,
						'costbook'     => 0,
						'total_value'  => 0,
						'update_by'    => $UserName,
						'update_date'  => $DateTime,
						'hist_by'      => $UserName,
						'hist_date'    => $date_target,
						'harga'        => $harga_trx,
						'total_harga'  => $total_harga_trx,
					);
				}
			}
		}

		// Insert batch
		$result = $this->stock_opname_generate_model->insert_batch_stock($ArrInsert);

		if($result){
			echo json_encode(array(
				'status' => 1,
				'pesan'  => 'Berhasil generate '.count($ArrInsert).' data stok opname untuk tanggal '.$date_target,
			));
		} else {
			echo json_encode(array(
				'status' => 0,
				'pesan'  => 'Gagal menyimpan data. Silakan coba lagi.',
			));
		}
	}

	/**
	 * Hapus data pada tanggal tertentu (untuk regenerate)
	 */
	public function delete_by_date(){
		$date = $this->input->post('date');
		if(empty($date)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tanggal harus dipilih!'));
			return;
		}

		// Jangan izinkan hapus tanggal awal (01/01/2026)
		if($date == '2026-01-01'){
			echo json_encode(array('status' => 0, 'pesan' => 'Data stok opname tanggal awal (01/01/2026) tidak boleh dihapus!'));
			return;
		}

		// Cek apakah ada data di tanggal setelahnya
		$next_date = $this->stock_opname_generate_model->get_next_date_after($date);
		if(!empty($next_date)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak bisa hapus tanggal '.$date.' karena masih ada data di tanggal '.$next_date.'. Hapus dari tanggal terbaru terlebih dahulu.'));
			return;
		}

		$result = $this->stock_opname_generate_model->delete_by_date($date);
		if($result){
			echo json_encode(array('status' => 1, 'pesan' => 'Data tanggal '.$date.' berhasil dihapus.'));
		} else {
			echo json_encode(array('status' => 0, 'pesan' => 'Gagal menghapus data.'));
		}
	}

	/**
	 * Get daftar tanggal yang sudah ada datanya
	 */
	public function get_available_dates(){
		$dates = $this->stock_opname_generate_model->get_all_dates();
		echo json_encode(array('data' => $dates));
	}

	/**
	 * Summary total value per tanggal
	 */
	public function get_summary(){
		$date = $this->input->post('date');
		$summary = $this->stock_opname_generate_model->get_summary_by_date($date);
		echo json_encode($summary);
	}

	/**
	 * Rekonsiliasi: Paksa total_harga di warehouse_stock_per_day_duplikat (gudang 3)
	 * agar sama dengan saldo akhir di ledger_subgudang per tanggal.
	 * Selisih didistribusikan proporsional ke semua material di gudang 3.
	 */
	public function reconcile(){
		$date_target = $this->input->post('date_target');
		if(empty($date_target)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tanggal harus dipilih!'));
			return;
		}

		$id_gudang = '3'; // Sub Gudang

		// 1. Ambil saldo akhir dari ledger_subgudang (baris terakhir di tanggal tersebut)
		$sql_ledger = "SELECT saldo FROM ledger_subgudang 
						WHERE DATE(tanggal_bukti) = '".$this->db->escape_str($date_target)."' 
						ORDER BY id DESC LIMIT 1";
		$row_ledger = $this->db->query($sql_ledger)->row();

		if(empty($row_ledger)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data ledger_subgudang pada tanggal '.$date_target));
			return;
		}
		$saldo_ledger = (float)$row_ledger->saldo;

		// 2. Ambil semua data dari warehouse_stock_per_day_duplikat gudang 3 tanggal tersebut
		$sql_duplikat = "SELECT id, id_material, qty_stock, harga, total_harga 
						FROM warehouse_stock_per_day_duplikat 
						WHERE id_gudang = '".$id_gudang."' AND DATE(hist_date) = '".$this->db->escape_str($date_target)."'";
		$rows_duplikat = $this->db->query($sql_duplikat)->result_array();

		if(empty($rows_duplikat)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data duplikat gudang 3 pada tanggal '.$date_target));
			return;
		}

		// Hitung total saat ini
		$total_duplikat = 0;
		foreach($rows_duplikat as $row){
			$total_duplikat += (float)$row['total_harga'];
		}

		$selisih_total = $saldo_ledger - $total_duplikat;

		// 3. Jika tidak ada selisih, selesai
		if(abs($selisih_total) < 1){
			echo json_encode(array(
				'status' => 1, 
				'pesan' => 'Data sudah cocok. Saldo ledger: '.number_format($saldo_ledger,0,',','.').
						   ' | Total duplikat: '.number_format($total_duplikat,0,',','.'),
				'selisih' => 0
			));
			return;
		}

		// 4. Distribusikan selisih proporsional ke semua material
		$ArrUpdate = array();
		$corrected = 0;

		// Hindari pembagian nol
		if($total_duplikat == 0) $total_duplikat = 1;

		foreach($rows_duplikat as $row){
			$old_total_harga = (float)$row['total_harga'];
			$proporsi = $old_total_harga / $total_duplikat;
			$koreksi = $selisih_total * $proporsi;

			$total_harga_baru = $old_total_harga + $koreksi;
			$qty = (float)$row['qty_stock'];
			$harga_baru = ($qty != 0) ? ($total_harga_baru / $qty) : (float)$row['harga'];

			$ArrUpdate[] = array(
				'id' => $row['id'],
				'total_harga' => $total_harga_baru,
				'harga' => $harga_baru,
			);
			$corrected++;
		}

		// 5. Update batch
		if(!empty($ArrUpdate)){
			$this->db->trans_start();
			$this->db->update_batch('warehouse_stock_per_day_duplikat', $ArrUpdate, 'id');
			$this->db->trans_complete();

			if($this->db->trans_status()){
				// Verifikasi
				$sql_verify = "SELECT SUM(total_harga) as total FROM warehouse_stock_per_day_duplikat 
							WHERE id_gudang = '".$id_gudang."' AND DATE(hist_date) = '".$this->db->escape_str($date_target)."'";
				$verify = $this->db->query($sql_verify)->row();
				$total_after = (float)$verify->total;

				echo json_encode(array(
					'status' => 1,
					'pesan' => 'Rekonsiliasi selesai. Selisih: '.number_format($selisih_total,0,',','.').
							   ' | Dikoreksi '.$corrected.' material.'.
							   ' Saldo ledger: '.number_format($saldo_ledger,0,',','.').
							   ' | Total setelah koreksi: '.number_format($total_after,0,',','.'),
					'selisih' => $selisih_total,
					'corrected' => $corrected
				));
			} else {
				echo json_encode(array('status' => 0, 'pesan' => 'Gagal update data.'));
			}
		} else {
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data yang perlu diupdate.'));
		}
	}

	/**
	 * Perbaiki harga di tran_warehouse_jurnal_detail agar cocok dengan ledger_subgudang.
	 * Ambil selisih per material antara ledger dan warehouse_history,
	 * lalu update harga & nilai_akhir_rp di record terakhir tran_warehouse_jurnal_detail.
	 */
	public function fix_tran_detail(){
		$date_target = $this->input->post('date_target');
		if(empty($date_target)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tanggal harus dipilih!'));
			return;
		}

		$id_gudang = '3'; // Sub Gudang

		// 1. Ambil transaksi dari ledger_subgudang tanggal tersebut, parse id_material
		$sql_ledger = "SELECT id, keterangan, debet, kredit 
						FROM ledger_subgudang 
						WHERE DATE(tanggal_bukti) = '".$this->db->escape_str($date_target)."'
						ORDER BY id ASC";
		$ledger_rows = $this->db->query($sql_ledger)->result_array();

		if(empty($ledger_rows)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data ledger tanggal '.$date_target));
			return;
		}

		// Parse keterangan: "transfer pusat - subgudang,MTL-XXXXXXX,NAMA,QTYxHARGA"
		$ledger_per_material = array();
		foreach($ledger_rows as $lr){
			if(preg_match('/(MTL-\d+)/', $lr['keterangan'], $matches)){
				$mat_id = $matches[1];
				if(!isset($ledger_per_material[$mat_id])){
					$ledger_per_material[$mat_id] = array('debet' => 0, 'kredit' => 0);
				}
				$ledger_per_material[$mat_id]['debet'] += (float)$lr['debet'];
				$ledger_per_material[$mat_id]['kredit'] += (float)$lr['kredit'];
			}
		}

		// 2. Ambil transaksi warehouse_history tanggal tersebut gudang 3
		$sql_hist = "SELECT id_material, jumlah_mat, total_harga, 
						id_gudang_dari, kd_gudang_dari, id_gudang_ke, id_gudang
					FROM warehouse_history 
					WHERE DATE(update_date) = '".$this->db->escape_str($date_target)."'
					AND id_gudang = '".$id_gudang."'
					ORDER BY id ASC";
		$hist_rows = $this->db->query($sql_hist)->result_array();

		$hist_per_material = array();
		foreach($hist_rows as $h){
			$mat = $h['id_material'];
			if(!isset($hist_per_material[$mat])){
				$hist_per_material[$mat] = array('total_in' => 0, 'total_out' => 0);
			}
			$val = abs((float)$h['total_harga']);
			if($h['id_gudang'] == $h['id_gudang_ke'] || $h['kd_gudang_dari'] == 'PURCHASE'){
				$hist_per_material[$mat]['total_in'] += $val;
			} else {
				$hist_per_material[$mat]['total_out'] += $val;
			}
		}

		// 3. Untuk setiap material yang ada selisih, update tran_warehouse_jurnal_detail
		$ArrUpdate = array();
		$corrected = 0;

		foreach($ledger_per_material as $mat_id => $ledger_vals){
			$ledger_in = $ledger_vals['debet'];
			$hist_in = isset($hist_per_material[$mat_id]) ? $hist_per_material[$mat_id]['total_in'] : 0;

			$selisih = $ledger_in - $hist_in;

			if(abs($selisih) >= 1){
				// Ambil record terakhir di tran_warehouse_jurnal_detail sampai tanggal target
				$sql_tras = "SELECT a.id, a.harga, a.nilai_akhir_rp, a.qty_stock_akhir 
							FROM tran_warehouse_jurnal_detail a
							WHERE a.id_material = '".$this->db->escape_str($mat_id)."'
							AND a.id_gudang = '".$id_gudang."'
							AND DATE(a.tgl_trans) <= '".$this->db->escape_str($date_target)."'
							ORDER BY a.id DESC LIMIT 1";
				$rec = $this->db->query($sql_tras)->row();

				if(!empty($rec)){
					$qty = (float)$rec->qty_stock_akhir;
					$old_nilai = (float)$rec->nilai_akhir_rp;
					$new_nilai = $old_nilai + $selisih;
					$new_harga = ($qty != 0) ? ($new_nilai / $qty) : (float)$rec->harga;

					$ArrUpdate[] = array(
						'id' => $rec->id,
						'harga' => $new_harga,
						'nilai_akhir_rp' => $new_nilai,
					);
					$corrected++;
				}
			}
		}

		// 4. Update batch
		if(!empty($ArrUpdate)){
			$this->db->trans_start();
			$this->db->update_batch('tran_warehouse_jurnal_detail', $ArrUpdate, 'id');
			$this->db->trans_complete();

			if($this->db->trans_status()){
				echo json_encode(array(
					'status' => 1,
					'pesan' => 'Fix selesai. Dikoreksi '.$corrected.' material di tran_warehouse_jurnal_detail.',
					'corrected' => $corrected
				));
			} else {
				echo json_encode(array('status' => 0, 'pesan' => 'Gagal update data.'));
			}
		} else {
			echo json_encode(array(
				'status' => 1, 
				'pesan' => 'Tidak ada selisih yang perlu diperbaiki.',
				'corrected' => 0
			));
		}
	}
}
