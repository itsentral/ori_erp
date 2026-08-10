<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_indirect_model extends CI_Model {

	private $id_gudang = 10;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Ambil list material dari warehouse_rutin_history yang id_gudang = 10
	 */
	public function get_list_material(){
		$sql = "SELECT a.code_group, a.material_name, b.material_name AS material_name_new
				FROM warehouse_rutin_history a 
				LEFT JOIN con_nonmat_new b ON a.code_group = b.code_group AND b.deleted_date IS NULL
				WHERE a.id_gudang = '".$this->id_gudang."'
				GROUP BY a.code_group 
				ORDER BY a.material_name ASC";
		return $this->db->query($sql)->result_array();
	}

	/**
	 * Get ledger data Indirect dari tabel warehouse_rutin_history
	 * Filter id_gudang = 10
	 * In = barang masuk ke gudang 10 (id_gudang_ke = 10 atau gudang_dari = PURCHASE)
	 * Out = barang keluar dari gudang 10 (id_gudang != id_gudang_ke)
	 */
	public function get_ledger_data($bulan, $tahun, $code_group = ''){
		$result = array('data' => array(), 'saldo_awal' => 0);

		$tgl_filter = $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT);

		// Hitung saldo awal dari transaksi sebelum bulan yang dipilih
		$saldo_awal = $this->get_saldo_awal($tgl_filter, $code_group);
		$result['saldo_awal'] = $saldo_awal;

		$where_material = '';
		if(!empty($code_group)){
			$where_material = "AND a.code_group = '".$this->db->escape_str($code_group)."'";
		}

		$sql_detail = "SELECT 
							a.id,
							a.code_group,
							a.material_name,
							a.category_awal,
							a.category_code,
							a.id_gudang,
							a.gudang,
							a.gudang_dari,
							a.id_gudang_ke,
							a.gudang_ke,
							a.no_trans,
							a.jumlah_qty,
							a.qty_stock_awal,
							a.qty_stock_akhir,
							a.ket,
							a.update_date,
							a.update_by,
							b.material_name AS material_name_new
						FROM warehouse_rutin_history a
						LEFT JOIN con_nonmat_new b ON a.code_group = b.code_group AND b.deleted_date IS NULL
						WHERE a.id_gudang = '".$this->id_gudang."'
						AND DATE_FORMAT(a.update_date, '%Y-%m') = '".$tgl_filter."'
						".$where_material."
						ORDER BY a.update_date ASC, a.id ASC";
		$detail_rows = $this->db->query($sql_detail)->result_array();

		$running_saldo = $saldo_awal;
		$total_in = 0;
		$total_out = 0;

		foreach($detail_rows as $row){
			$qty = abs((float)$row['jumlah_qty']);
			$val_in  = 0;
			$val_out = 0;
			$keterangan = $row['ket'];

			// Tentukan In atau Out berdasarkan logika gudang
			// Jika gudang_dari = PURCHASE atau id_gudang_ke = id_gudang ini, berarti IN
			// Jika barang keluar (gudang_ke bukan gudang ini), berarti OUT
			if(strtoupper($row['gudang_dari']) == 'PURCHASE'){
				// Incoming dari purchase
				$val_in = $qty;
				$running_saldo += $qty;
				$total_in += $qty;
			} else if($row['id_gudang_ke'] == $this->id_gudang){
				// Masuk ke gudang ini
				$val_in = $qty;
				$running_saldo += $qty;
				$total_in += $qty;
			} else {
				// Keluar dari gudang ini
				$val_out = $qty;
				$running_saldo -= $qty;
				$total_out += $qty;
			}

			$nm_material = (!empty($row['material_name_new'])) ? $row['material_name_new'] : $row['material_name'];

			$result['data'][] = array(
				'code_group'	=> $row['code_group'],
				'material_name'	=> $nm_material,
				'tanggal'		=> date('d-m-Y H:i', strtotime($row['update_date'])),
				'no_trans'		=> $row['no_trans'],
				'keterangan'	=> $keterangan,
				'gudang_dari'	=> $row['gudang_dari'],
				'gudang_ke'		=> $row['gudang_ke'],
				'in'			=> $val_in,
				'out'			=> $val_out,
				'saldo'			=> $running_saldo
			);
		}

		$result['total_in']  = $total_in;
		$result['total_out'] = $total_out;

		return $result;
	}

	/**
	 * Hitung saldo awal dari semua transaksi sebelum periode yang dipilih
	 */
	private function get_saldo_awal($tgl_filter, $code_group = ''){
		$where_material = '';
		if(!empty($code_group)){
			$where_material = "AND a.code_group = '".$this->db->escape_str($code_group)."'";
		}

		$sql = "SELECT 
					a.id,
					a.id_gudang,
					a.id_gudang_ke,
					a.gudang_dari,
					a.jumlah_qty
				FROM warehouse_rutin_history a
				WHERE a.id_gudang = '".$this->id_gudang."'
				AND DATE_FORMAT(a.update_date, '%Y-%m') < '".$tgl_filter."'
				".$where_material."
				ORDER BY a.update_date ASC, a.id ASC";
		$rows = $this->db->query($sql)->result_array();

		$saldo = 0;
		foreach($rows as $row){
			$qty = abs((float)$row['jumlah_qty']);
			if(strtoupper($row['gudang_dari']) == 'PURCHASE'){
				$saldo += $qty;
			} else if($row['id_gudang_ke'] == $this->id_gudang){
				$saldo += $qty;
			} else {
				$saldo -= $qty;
			}
		}

		return $saldo;
	}
}
