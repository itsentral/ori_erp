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

		$tgl_filter = $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT);

		// Detail transaksi pada periode ini (filter berdasarkan created_date)
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
							a.nilai_wip,
							a.jenis,
							a.nm_material,
							a.created_date
						FROM data_erp_fg a
						WHERE DATE_FORMAT(a.tanggal, '%Y-%m') = '".$tgl_filter."'
						AND a.keterangan NOT LIKE '%Join to Finish Good%'
						AND a.keterangan NOT LIKE '%Material to Finish Good%'
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

			// No Reff: jika out = kode_delivery + no_so, jika in = id_trans + no_so
			$no_reff = '';
			if(strpos($jenis, 'in') !== false){
				$no_reff = $row['id_trans'].$row['no_so'];
			} else {
				$no_reff = $row['kode_delivery'].$row['no_so'];
			}

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
