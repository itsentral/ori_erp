<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_fg_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get ledger data Finish Good dari tabel data_erp_fg
	 * In = jenis 'in' (nilai_wip)
	 * Out = jenis 'out' (nilai_wip)
	 */
	public function get_ledger_data($bulan, $tahun){
		$result = array('data' => array());

		$tgl_awal	= $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT).'-01';
		$tgl_akhir	= date('Y-m-t', strtotime($tgl_awal));

		// Detail transaksi pada periode ini
		$sql_detail = "SELECT 
							a.id,
							a.tanggal,
							a.keterangan,
							a.no_so,
							a.product,
							a.no_spk,
							a.kode_trans,
							a.id_trans,
							a.qty,
							a.nilai_wip,
							a.jenis,
							a.nm_material
						FROM data_erp_fg a
						WHERE DATE(a.tanggal) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
						ORDER BY a.tanggal ASC, a.id ASC";
		$detail_rows = $this->db->query($sql_detail)->result_array();

		$group = array(
			'nama'			=> 'FINISHED GOODS SO',
			'detail'		=> array()
		);

		$running_saldo = 0;

		foreach($detail_rows as $row){
			$nilai_wip	= (float)$row['nilai_wip'];
			$jenis		= strtolower($row['jenis']);
			$val_in		= 0;
			$val_out	= 0;

			if(strpos($jenis, 'in') !== false){
				$val_in = $nilai_wip;
				$running_saldo += $nilai_wip;
			} else {
				$val_out = $nilai_wip;
				$running_saldo -= $nilai_wip;
			}

			$group['detail'][] = array(
				'keterangan'	=> $row['keterangan'],
				'tanggal'		=> date('d-m-Y', strtotime($row['tanggal'])),
				'nomor_bukti'	=> $row['kode_trans'],
				'sm'			=> $row['id_trans'],
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
