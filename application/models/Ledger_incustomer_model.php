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

		// Ambil saldo awal dari tabel begining_stock
		$sql_saldo = "SELECT saldoawal FROM begining_stock 
					  WHERE no_perkiraan = 'incustomer' 
					  AND bln = '".$this->db->escape_str($bulan)."' 
					  AND thn = '".$this->db->escape_str($tahun)."' 
					  LIMIT 1";
		$row_saldo = $this->db->query($sql_saldo)->row();
		$saldo_awal = (!empty($row_saldo)) ? (float)$row_saldo->saldoawal : 0;

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
			'saldo_awal'	=> $saldo_awal,
			'detail'	=> array()
		);

		$running_saldo = $saldo_awal;
		$total_debet = 0;
		$total_kredit = 0;

		foreach($detail_rows as $row){
			$nilai_unit	= (float)$row['nilai_unit'];
			$jenis		= strtolower($row['jenis']);
			$val_in		= 0;
			$val_out	= 0;

			if(strpos($jenis, 'in') !== false){
				$val_in = $nilai_unit;
				$running_saldo += $nilai_unit;
				$total_debet += $nilai_unit;
			} else {
				$val_out = $nilai_unit;
				$running_saldo -= $nilai_unit;
				$total_kredit += $nilai_unit;
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

		if(!empty($group['detail']) || $saldo_awal != 0){
			$result['data'][] = $group;
		}

		// Update saldo_akhir di begining_stock setelah proses
		$saldo_akhir = $running_saldo;
		$cek = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = 'incustomer' AND bln = '".$this->db->escape_str($bulan)."' AND thn = '".$this->db->escape_str($tahun)."'")->row();
		if(!empty($cek)){
			$this->db->where('id', $cek->id);
			$this->db->update('begining_stock', array(
				'saldo_akhir' => $saldo_akhir,
				'debet' => $total_debet,
				'kredit' => $total_kredit
			));
		}

		// Saldo akhir jadi saldo awal bulan berikutnya
		$bln_next = (int)$bulan + 1;
		$thn_next = (int)$tahun;
		if($bln_next > 12){
			$bln_next = 1;
			$thn_next = $thn_next + 1;
		}
		$bln_next_str = str_pad($bln_next, 2, '0', STR_PAD_LEFT);

		$cek_next = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = 'incustomer' AND bln = '".$bln_next_str."' AND thn = '".$thn_next."'")->row();
		if(!empty($cek_next)){
			$this->db->where('id', $cek_next->id);
			$this->db->update('begining_stock', array('saldoawal' => $saldo_akhir));
		} else {
			$this->db->insert('begining_stock', array(
				'no_perkiraan'	=> 'incustomer',
				'nama'			=> 'incustomer',
				'saldoawal'		=> $saldo_akhir,
				'bln'			=> $bln_next_str,
				'thn'			=> $thn_next
			));
		}

		return $result;
	}
}
