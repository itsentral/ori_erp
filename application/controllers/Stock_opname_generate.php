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
			// Skip gudang 1, 2 (pusat), 3 dan 4
			if(in_array($row['id_gudang'], array('1','2','3','4'))) continue;

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
			// Skip gudang 1, 2 (pusat), 3 dan 4
			if(in_array($trx['id_gudang'], array('1','2','3','4'))) continue;

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
			// Update harga di tran_warehouse_jurnal_detail untuk semua gudang
			$this->_update_tran_detail($date_target, $ArrInsert);

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
	 * Update harga di tran_warehouse_jurnal_detail berdasarkan transaksi warehouse_history.
	 * Hanya untuk material yang ada transaksi IN (penambahan) pada tanggal target.
	 * Hitung harga average: (nilai_lama + nilai_masuk) / (qty_lama + qty_masuk)
	 * SKIP id_gudang = 3 (sudah selesai diproses terpisah)
	 */
	private function _update_tran_detail($date_target, $data_stok){
		$ArrUpdate = array();

		// Ambil transaksi IN pada tanggal target (skip gudang 3)
		$sql_trx = "SELECT id_material, id_gudang, jumlah_mat, total_harga, 
						id_gudang_dari, kd_gudang_dari, id_gudang_ke
					FROM warehouse_history 
					WHERE DATE(update_date) = '".$this->db->escape_str($date_target)."'
					AND id_gudang NOT IN ('1','2','3','4')
					AND (id_gudang = id_gudang_ke OR kd_gudang_dari = 'PURCHASE')
					ORDER BY id ASC";
		$transaksi = $this->db->query($sql_trx)->result_array();

		if(empty($transaksi)) return;

		// Group per material + gudang
		$trans_map = array();
		foreach($transaksi as $trx){
			$key = $trx['id_material'].'_'.$trx['id_gudang'];
			if(!isset($trans_map[$key])){
				$trans_map[$key] = array(
					'id_material' => $trx['id_material'],
					'id_gudang' => $trx['id_gudang'],
					'qty_in' => 0,
					'val_in' => 0,
				);
			}
			$trans_map[$key]['qty_in'] += abs((float)$trx['jumlah_mat']);
			$trans_map[$key]['val_in'] += abs((float)$trx['total_harga']);
		}

		// Untuk setiap material yang masuk, hitung harga average baru
		foreach($trans_map as $key => $trx_data){
			$id_material = $trx_data['id_material'];
			$id_gudang = $trx_data['id_gudang'];

			// Ambil record terakhir di tran_warehouse_jurnal_detail sebelum tanggal target
			$sql = "SELECT id, harga, nilai_akhir_rp, qty_stock_akhir 
					FROM tran_warehouse_jurnal_detail 
					WHERE id_material = '".$this->db->escape_str($id_material)."'
					AND id_gudang = '".$this->db->escape_str($id_gudang)."'
					AND DATE(tgl_trans) <= '".$this->db->escape_str($date_target)."'
					ORDER BY id DESC LIMIT 1";
			$rec = $this->db->query($sql)->row();

			if(!empty($rec)){
				$qty_lama = (float)$rec->qty_stock_akhir;
				$nilai_lama = (float)$rec->nilai_akhir_rp;
				$qty_masuk = $trx_data['qty_in'];
				$val_masuk = $trx_data['val_in'];

				// Harga average = (nilai_lama + nilai_masuk) / (qty_lama)
				// qty_lama sudah termasuk qty_masuk (karena record ini sudah terupdate qty-nya)
				$new_nilai = $nilai_lama + $val_masuk;
				$new_harga = ($qty_lama != 0) ? ($new_nilai / $qty_lama) : (float)$rec->harga;

				if(abs((float)$rec->harga - $new_harga) >= 0.01 || abs($nilai_lama - $new_nilai) >= 1){
					$ArrUpdate[] = array(
						'id' => $rec->id,
						'harga' => $new_harga,
						'nilai_akhir_rp' => $new_nilai,
					);
				}
			}
		}

		if(!empty($ArrUpdate)){
			$chunks = array_chunk($ArrUpdate, 500);
			foreach($chunks as $chunk){
				$this->db->update_batch('tran_warehouse_jurnal_detail', $chunk, 'id');
			}
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
	 * Adjust harga di tran_warehouse_jurnal_detail agar total inventory dari ExcelStockCompare
	 * sama dengan total yang diinput user.
	 * 
	 * Logic:
	 * 1. Hitung total inventory hari ini (tanggal target) per material+gudang — ExcelStockCompare logic
	 * 2. Hitung total inventory sehari sebelumnya (H-1) per material+gudang — sama logic
	 * 3. Bandingkan per material+gudang — yang ada selisih, itu yang harganya di-update
	 * 4. Update harga di tran_warehouse_jurnal_detail pada transaksi tanggal target
	 *    hanya untuk material+gudang yang ada selisih
	 *    Rumus: harga_baru = (total_inventory_input / total_inventory_current) * harga_lama
	 *
	 * Input POST: date_target, total_inventory_input
	 */
	public function adjust_harga_inventory(){
		$date_target         = $this->input->post('date_target');
		$total_input         = (float)$this->input->post('total_inventory_input');

		if(empty($date_target)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tanggal harus dipilih!'));
			return;
		}
		if($total_input <= 0){
			echo json_encode(array('status' => 0, 'pesan' => 'Total inventory harus lebih dari 0!'));
			return;
		}

		$date_prev = date('Y-m-d', strtotime($date_target.' -1 day'));

		// === HELPER: Hitung total inventory per material+gudang pada tanggal tertentu ===
		$map_today = $this->_get_inventory_per_material($date_target);
		$map_prev  = $this->_get_inventory_per_material($date_prev);

		if(empty($map_today)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data inventory gudang produksi pada tanggal '.$date_target));
			return;
		}

		// === 1. Hitung total inventory saat ini ===
		$total_inventory_current = 0;
		foreach($map_today as $key => $val){
			$total_inventory_current += $val['total'];
		}

		// === 2. Bandingkan total inventory saat ini dengan total input ===
		$selisih_global = $total_input - $total_inventory_current;

		if(abs($selisih_global) < 1){
			echo json_encode(array(
				'status' => 1,
				'pesan'  => 'Total inventory sudah cocok. Total saat ini: '.number_format($total_inventory_current,0,',','.'),
				'total_current' => $total_inventory_current,
				'selisih' => 0
			));
			return;
		}

		// === 3. Cari material+gudang yang ada selisih antara hari ini dan kemarin ===
		$material_selisih = array(); // key => true
		foreach($map_today as $key => $val){
			$total_today = $val['total'];
			$total_kemarin = isset($map_prev[$key]) ? $map_prev[$key]['total'] : 0;

			if(abs($total_today - $total_kemarin) >= 1){
				$material_selisih[$key] = true;
			}
		}
		// Cek juga material yang ada kemarin tapi tidak ada hari ini
		foreach($map_prev as $key => $val){
			if(!isset($map_today[$key])){
				$material_selisih[$key] = true;
			}
		}

		if(empty($material_selisih)){
			echo json_encode(array(
				'status' => 0,
				'pesan'  => 'Tidak ada material yang berubah antara tanggal '.$date_prev.' dan '.$date_target.'. Total inventory: '.number_format($total_inventory_current,0,',','.'),
			));
			return;
		}

		// === 4. Update harga di tran_warehouse_jurnal_detail pada tanggal target ===
		// Hanya untuk material+gudang yang ada selisih
		// Hitung total inventory selisih (sum total dari material yang berselisih)
		$total_inventory_selisih = 0;
		foreach($material_selisih as $key => $val){
			if(isset($map_today[$key])){
				$total_inventory_selisih += $map_today[$key]['total'];
			}
		}

		// Rumus: harga_baru = (((total_inventory_current - total_inventory_selisih) - total_input) / total_inventory_selisih) * harga_lama
		if($total_inventory_selisih == 0){
			echo json_encode(array(
				'status' => 0,
				'pesan'  => 'Total inventory selisih = 0, tidak bisa menghitung rasio.',
			));
			return;
		}
		$rasio = (($total_inventory_current - $total_inventory_selisih) - $total_input) / $total_inventory_selisih;

		// Ambil transaksi di tanggal target untuk gudang produksi
		$sql_trans = "SELECT tras.id, tras.id_material, tras.id_gudang, tras.harga
					FROM tran_warehouse_jurnal_detail tras
					LEFT JOIN warehouse head_whr ON tras.id_gudang = head_whr.id
					WHERE DATE(tras.tgl_trans) = '".$this->db->escape_str($date_target)."'
					AND tras.id_gudang NOT IN ('1','2','3','4')
					AND head_whr.category = 'produksi'";
		$rows_trans = $this->db->query($sql_trans)->result_array();

		if(empty($rows_trans)){
			echo json_encode(array(
				'status' => 0,
				'pesan'  => 'Tidak ada transaksi di tran_warehouse_jurnal_detail pada tanggal '.$date_target.' untuk gudang produksi.',
				'total_current' => $total_inventory_current
			));
			return;
		}

		// Filter hanya material+gudang yang ada selisih
		$ArrUpdate = array();
		foreach($rows_trans as $t){
			$key = $t['id_gudang'].'^_^'.$t['id_material'];
			if(!isset($material_selisih[$key])) continue;

			$harga_lama = (float)$t['harga'];
			$harga_baru = $rasio * $harga_lama;

			$ArrUpdate[] = array(
				'id'    => $t['id'],
				'harga' => $harga_baru,
			);
		}

		if(empty($ArrUpdate)){
			echo json_encode(array(
				'status' => 0,
				'pesan'  => 'Tidak ada transaksi yang perlu di-update pada tanggal '.$date_target.' untuk material yang berselisih.',
				'material_selisih' => count($material_selisih)
			));
			return;
		}

		// Execute update
		$this->db->trans_start();
		$chunks = array_chunk($ArrUpdate, 500);
		foreach($chunks as $chunk){
			$this->db->update_batch('tran_warehouse_jurnal_detail', $chunk, 'id');
		}
		$this->db->trans_complete();

		if($this->db->trans_status()){
			echo json_encode(array(
				'status'        => 1,
				'pesan'         => 'Berhasil adjust harga '.count($ArrUpdate).' transaksi (dari '.count($material_selisih).' material berselisih). Rasio: '.number_format($rasio,6).
								   ' | Total inventory: '.number_format($total_inventory_current,0,',','.').
								   ' | Total selisih: '.number_format($total_inventory_selisih,0,',','.').
								   ' | Total target: '.number_format($total_input,0,',','.').
								   ' | Selisih global: '.number_format($selisih_global,0,',','.'),
				'total_before'  => $total_inventory_current,
				'total_target'  => $total_input,
				'total_selisih' => $total_inventory_selisih,
				'rasio'         => $rasio,
				'updated_count' => count($ArrUpdate),
				'material_selisih' => count($material_selisih)
			));
		} else {
			echo json_encode(array('status' => 0, 'pesan' => 'Gagal update data. Transaction rollback.'));
		}
	}

	/**
	 * Helper: Ambil inventory per material+gudang pada tanggal tertentu
	 * Logic sama seperti ExcelStockCompare (harga last record × qty stock)
	 * Return: array [ 'id_gudang^_^id_material' => ['harga' => x, 'qty' => y, 'total' => x*y] ]
	 */
	private function _get_inventory_per_material($date){
		$result = array();

		// Tentukan tabel stock
		$Table_Stock = "warehouse_stock";
		$WHERE_Stock = "head_whr.category = 'produksi' AND head_stock.qty_stock <> 0";
		if($date < date('Y-m-d')){
			$Table_Stock = "warehouse_stock_per_day_duplikat";
			$WHERE_Stock .= " AND DATE(head_stock.hist_date) = '".$this->db->escape_str($date)."'";
		}

		// Ambil qty stock per material per gudang
		$sql_stock = "SELECT
						head_stock.id_material,
						head_stock.id_gudang,
						head_stock.qty_stock
					FROM ".$Table_Stock." head_stock
					LEFT JOIN warehouse head_whr ON head_stock.id_gudang = head_whr.id
					WHERE ".$WHERE_Stock."
					AND head_stock.id_gudang NOT IN ('1','2','3','4')";
		$rows_stock = $this->db->query($sql_stock)->result_array();

		if(empty($rows_stock)) return $result;

		// Index qty
		$map_qty = array();
		foreach($rows_stock as $row){
			$key = $row['id_gudang'].'^_^'.$row['id_material'];
			$map_qty[$key] = (float)$row['qty_stock'];
		}

		// Ambil harga terakhir per material+gudang dari tran_warehouse_jurnal_detail
		$Sub_Find = "NOT(head_whr.coa_1 IS NULL OR head_whr.coa_1 ='' OR head_whr.coa_1 ='-')";
		$Sub_Find .= " AND DATE(tras_stock.tgl_trans) <= '".$this->db->escape_str($date)."'";
		$Sub_Find .= " AND tras_stock.id_gudang NOT IN ('1','2','3','4')";
		$Sub_Find .= " AND head_whr.category = 'produksi'";

		$sql_last = "SELECT
						tras_stock.id_material,
						tras_stock.id_gudang,
						MAX(tras_stock.id) AS last_id
					FROM tran_warehouse_jurnal_detail tras_stock
					LEFT JOIN warehouse head_whr ON tras_stock.id_gudang = head_whr.id
					WHERE ".$Sub_Find."
					GROUP BY tras_stock.id_material, tras_stock.id_gudang";
		$rows_last = $this->db->query($sql_last)->result_array();

		if(empty($rows_last)) return $result;

		// Ambil detail harga
		$ids = array();
		foreach($rows_last as $r){
			$ids[] = $r['last_id'];
		}

		$sql_detail = "SELECT id, id_material, id_gudang, harga FROM tran_warehouse_jurnal_detail WHERE id IN (".implode(',', $ids).")";
		$rows_detail = $this->db->query($sql_detail)->result_array();

		// Hitung total per material+gudang
		foreach($rows_detail as $d){
			$key = $d['id_gudang'].'^_^'.$d['id_material'];
			$harga = (float)$d['harga'];
			$qty = isset($map_qty[$key]) ? $map_qty[$key] : 0;

			$result[$key] = array(
				'harga' => $harga,
				'qty'   => $qty,
				'total' => $harga * $qty,
			);
		}

		return $result;
	}

	/**
	 * Perbaiki harga di tran_warehouse_jurnal_detail agar total nilai_akhir_rp gudang 3
	 * cocok dengan saldo ledger_subgudang.
	 * Koreksi proporsional ke semua material yang punya nilai_akhir_rp > 0.
	 */
	public function fix_tran_detail(){
		$date_target = $this->input->post('date_target');
		if(empty($date_target)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tanggal harus dipilih!'));
			return;
		}

		$id_gudang = '3'; // Sub Gudang

		// 1. Ambil saldo akhir ledger
		$sql_ledger = "SELECT saldo FROM ledger_subgudang 
						WHERE DATE(tanggal_bukti) = '".$this->db->escape_str($date_target)."' 
						ORDER BY id DESC LIMIT 1";
		$row_ledger = $this->db->query($sql_ledger)->row();

		if(empty($row_ledger)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data ledger tanggal '.$date_target));
			return;
		}
		$saldo_ledger = (float)$row_ledger->saldo;

		// 2. Ambil record terakhir per material di tran_warehouse_jurnal_detail sampai tanggal target
		$sql_sub = "SELECT tras_stock.id_material, MAX(tras_stock.id) AS last_id
					FROM tran_warehouse_jurnal_detail tras_stock
					LEFT JOIN warehouse head_whr ON tras_stock.id_gudang = head_whr.id
					WHERE tras_stock.id_gudang = '".$id_gudang."'
					AND DATE(tras_stock.tgl_trans) <= '".$this->db->escape_str($date_target)."'
					GROUP BY tras_stock.id_material";
		$sub_rows = $this->db->query($sql_sub)->result_array();

		if(empty($sub_rows)){
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data tran_warehouse_jurnal_detail gudang 3.'));
			return;
		}

		// Ambil detail record
		$ids = array();
		foreach($sub_rows as $s){
			$ids[] = $s['last_id'];
		}

		$sql_detail = "SELECT id, id_material, harga, nilai_akhir_rp, qty_stock_akhir 
						FROM tran_warehouse_jurnal_detail 
						WHERE id IN (".implode(',', $ids).")";
		$detail_rows = $this->db->query($sql_detail)->result_array();

		// Hitung total nilai_akhir_rp saat ini
		$total_tras = 0;
		foreach($detail_rows as $d){
			$total_tras += (float)$d['nilai_akhir_rp'];
		}

		$selisih = $saldo_ledger - $total_tras;

		// 3. Jika sudah cocok
		if(abs($selisih) < 1){
			echo json_encode(array(
				'status' => 1,
				'pesan' => 'Data sudah cocok. Saldo ledger: '.number_format($saldo_ledger,0,',','.').
						   ' | Total tras: '.number_format($total_tras,0,',','.'),
				'corrected' => 0
			));
			return;
		}

		// 4. Koreksi proporsional
		$ArrUpdate = array();
		$corrected = 0;

		if($total_tras == 0) $total_tras = 1;

		foreach($detail_rows as $d){
			$old_nilai = (float)$d['nilai_akhir_rp'];
			if($old_nilai == 0) continue;

			$proporsi = $old_nilai / $total_tras;
			$koreksi = $selisih * $proporsi;
			$new_nilai = $old_nilai + $koreksi;
			$qty = (float)$d['qty_stock_akhir'];
			$new_harga = ($qty != 0) ? ($new_nilai / $qty) : (float)$d['harga'];

			$ArrUpdate[] = array(
				'id' => $d['id'],
				'harga' => $new_harga,
				'nilai_akhir_rp' => $new_nilai,
			);
			$corrected++;
		}

		// 5. Update batch
		if(!empty($ArrUpdate)){
			$this->db->trans_start();
			$this->db->update_batch('tran_warehouse_jurnal_detail', $ArrUpdate, 'id');
			$this->db->trans_complete();

			if($this->db->trans_status()){
				echo json_encode(array(
					'status' => 1,
					'pesan' => 'Fix selesai. Selisih: '.number_format($selisih,0,',','.').
							   ' | Dikoreksi '.$corrected.' material.'.
							   ' Saldo ledger: '.number_format($saldo_ledger,0,',','.'),
					'corrected' => $corrected
				));
			} else {
				echo json_encode(array('status' => 0, 'pesan' => 'Gagal update data.'));
			}
		} else {
			echo json_encode(array('status' => 0, 'pesan' => 'Tidak ada data yang perlu diupdate.'));
		}
	}
}
