<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_incustomer_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get ledger data In Customer dari tabel data_erp_in_customer
	 * In = jenis 'in' (nilai_unit)
	 * Out = jenis 'out' (nilai_unit)
	 * Data murni tanpa filter keterangan
	 */
	public function get_ledger_data($bulan, $tahun){
		$result = array('data' => array());

		$tgl_filter = $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT);

		// Detail transaksi pada periode ini - data murni tanpa filter
		$sql_detail = "SELECT 
							a.id,
							a.tanggal,
							a.keterangan,
							a.no_so,
							a.product,
							a.no_spk,
							a.kode_trans,
							a.id_trans,
							a.kode_delivery,
							a.qty,
							a.nilai_unit,
							a.jenis,
							a.nm_material
						FROM data_erp_in_customer a
						WHERE DATE_FORMAT(a.tanggal, '%Y-%m') = '".$tgl_filter."'
						ORDER BY a.tanggal ASC, a.id ASC";
		$detail_rows = $this->db->query($sql_detail)->result_array();

		$group = array(
			'nama'		=> 'FINISHED GOODS IN CUSTOMER',
			'detail'	=> array()
		);

		$running_saldo = 0;

		foreach($detail_rows as $row){
			$nilai_unit	= (float)$row['nilai_unit'];
			$jenis		= strtolower($row['jenis']);
			$val_in		= 0;
			$val_out	= 0;

			if(strpos($jenis, 'in') !== false){
				$val_in = $nilai_unit;
				$running_saldo += $nilai_unit;
			} else {
				$val_out = $nilai_unit;
				$running_saldo -= $nilai_unit;
			}

			// No Reff: kode_delivery + no_so
			$no_reff = $row['kode_delivery'].$row['no_so'];

			$group['detail'][] = array(
				'keterangan'	=> $row['keterangan'],
				'tanggal'		=> date('d-m-Y', strtotime($row['tanggal'])),
				'nomor_bukti'	=> $row['kode_trans'],
				'sm'			=> $no_reff,
				'in'			=> $val_in,
				'out'			=> $val_out,
				'saldo'			=> $running_saldo
			);
		}

		if(!empty($group['detail'])){
			$result['data'][] = $group;
		}

		return $result;
	}
}
