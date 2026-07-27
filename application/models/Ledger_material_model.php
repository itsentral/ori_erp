<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_material_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Ambil list gudang dari warehouse_history join warehouse untuk nama category
	 */
	public function get_list_gudang(){
		$sql = "SELECT a.id_gudang, b.category, b.nm_gudang 
				FROM warehouse_history a 
				LEFT JOIN warehouse b ON a.id_gudang = b.id 
				GROUP BY a.id_gudang 
				ORDER BY a.id_gudang ASC";
		return $this->db->query($sql)->result_array();
	}

	/**
	 * Get ledger data Material dari tabel warehouse_history
	 * In = jumlah_mat > 0
	 * Out = jumlah_mat < 0 (absolute value)
	 */
	public function get_ledger_data($bulan, $tahun, $id_gudang = ''){
		$result = array('data' => array());

		$tgl_filter = $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT);

		$where_gudang = '';
		if(!empty($id_gudang)){
			$where_gudang = "AND a.id_gudang = '".$this->db->escape_str($id_gudang)."'";
		}

		$sql_detail = "SELECT 
							a.id,
							a.id_material,
							a.nm_material,
							a.id_category,
							a.nm_category,
							a.id_gudang,
							a.kd_gudang,
							a.id_gudang_dari,
							a.kd_gudang_dari,
							a.id_gudang_ke,
							a.kd_gudang_ke,
							a.no_ipp,
							a.jumlah_mat,
							a.ket,
							a.update_date,
							a.harga,
							a.total_harga,
							a.saldo_awal,
							a.saldo_akhir,
							a.harga_baru
						FROM warehouse_history a
						WHERE DATE_FORMAT(a.update_date, '%Y-%m') = '".$tgl_filter."'
						".$where_gudang."
						ORDER BY a.update_date ASC, a.id ASC";
		$detail_rows = $this->db->query($sql_detail)->result_array();

		$running_saldo = 0;

		foreach($detail_rows as $row){
			$total_harga = abs((float)$row['total_harga']);
			$val_in		= 0;
			$val_out	= 0;

			// Jika id_gudang = id_gudang_dari berarti In, jika id_gudang = id_gudang_ke berarti Out
			$keterangan = '';
			if($row['id_gudang'] == $row['id_gudang_dari']){
				$val_in = $total_harga;
				$running_saldo += $total_harga;
				$keterangan = 'penambahan gudang';
			} else if($row['id_gudang'] == $row['id_gudang_ke']){
				$val_out = $total_harga;
				$running_saldo -= $total_harga;
				$keterangan = 'pengurangan gudang';
			}

			$result['data'][] = array(
				'nm_material'	=> $row['nm_material'],
				'nm_category'	=> $row['nm_category'],
				'tanggal'		=> date('d-m-Y H:i', strtotime($row['update_date'])),
				'kode_trans'	=> $row['no_ipp'],
				'keterangan'	=> $keterangan,
				'harga'			=> (float)$row['harga'],
				'in'			=> $val_in,
				'out'			=> $val_out,
				'saldo'			=> $running_saldo
			);
		}

		return $result;
	}
}
