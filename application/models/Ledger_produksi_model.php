<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_produksi_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get ledger data Produksi dari tabel warehouse_history
	 * Filter gudang kategori produksi
	 */
	public function get_ledger_data($bulan, $tahun){
		$result = array('data' => array(), 'saldo_awal' => 0);

		$tgl_filter = $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT);
		$no_perkiraan = 'produksi';

		// Ambil saldo awal dari begining_stock
		$saldo_awal = 0;
		$sql_saldo = "SELECT saldoawal FROM begining_stock 
					  WHERE no_perkiraan = '".$this->db->escape_str($no_perkiraan)."' 
					  AND bln = '".$this->db->escape_str($bulan)."' 
					  AND thn = '".$this->db->escape_str($tahun)."' 
					  LIMIT 1";
		$row_saldo = $this->db->query($sql_saldo)->row();
		$saldo_awal = (!empty($row_saldo)) ? (float)$row_saldo->saldoawal : 0;
		$result['saldo_awal'] = $saldo_awal;

		// Ambil list gudang produksi
		$gudang_produksi = getGudangProduksi();
		if(empty($gudang_produksi)){
			return $result;
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
						AND a.id_gudang IN (".$gudang_produksi.")
						ORDER BY a.update_date ASC, a.id ASC";
		$detail_rows = $this->db->query($sql_detail)->result_array();

		$running_saldo = $saldo_awal;
		$total_debet = 0;
		$total_kredit = 0;

		foreach($detail_rows as $row){
			$total_harga = abs((float)$row['total_harga']);
			$val_in		= 0;
			$val_out	= 0;

			$keterangan = '';
			if($row['id_gudang_dari'] === null || $row['id_gudang_dari'] === ''){
				$val_out = $total_harga;
				$running_saldo -= $total_harga;
				$total_kredit += $total_harga;
				$keterangan = 'adjustment';
			} else if($row['id_gudang_dari'] == '0' || $row['id_gudang_dari'] == 0){
				$val_in = $total_harga;
				$running_saldo += $total_harga;
				$total_debet += $total_harga;
				$keterangan = 'adjustment';
			} else if($row['id_gudang'] == $row['id_gudang_dari']){
				$val_out = $total_harga;
				$running_saldo -= $total_harga;
				$total_kredit += $total_harga;
				$keterangan = 'pengurangan gudang';
			} else if($row['id_gudang'] == $row['id_gudang_ke']){
				$val_in = $total_harga;
				$running_saldo += $total_harga;
				$total_debet += $total_harga;
				$keterangan = 'penambahan gudang';
			}

			$result['data'][] = array(
				'id_material'	=> $row['id_material'],
				'nm_material'	=> $row['nm_material'],
				'nm_category'	=> $row['nm_category'],
				'qty'			=> (float)$row['jumlah_mat'],
				'tanggal'		=> date('d-m-Y H:i', strtotime($row['update_date'])),
				'kode_trans'	=> $row['no_ipp'],
				'keterangan'	=> $keterangan,
				'harga'			=> (float)$row['harga'],
				'in'			=> $val_in,
				'out'			=> $val_out,
				'saldo'			=> $running_saldo
			);
		}

		// Update begining_stock
		$saldo_akhir = $running_saldo;
		$bln_str = str_pad($bulan, 2, '0', STR_PAD_LEFT);
		$cek = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = '".$this->db->escape_str($no_perkiraan)."' AND bln = '".$this->db->escape_str($bln_str)."' AND thn = '".$this->db->escape_str($tahun)."'")->row();
		if(!empty($cek)){
			$this->db->where('id', $cek->id);
			$this->db->update('begining_stock', array(
				'saldo_akhir' => $saldo_akhir,
				'debet' => $total_debet,
				'kredit' => $total_kredit
			));
		} else {
			$this->db->insert('begining_stock', array(
				'no_perkiraan'	=> $no_perkiraan,
				'nama'			=> $no_perkiraan,
				'saldoawal'		=> $saldo_awal,
				'bln'			=> $bln_str,
				'thn'			=> $tahun,
				'saldo_akhir'	=> $saldo_akhir,
				'debet'			=> $total_debet,
				'kredit'		=> $total_kredit
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

		$cek_next = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = '".$this->db->escape_str($no_perkiraan)."' AND bln = '".$bln_next_str."' AND thn = '".$thn_next."'")->row();
		if(!empty($cek_next)){
			$this->db->where('id', $cek_next->id);
			$this->db->update('begining_stock', array('saldoawal' => $saldo_akhir));
		} else {
			$this->db->insert('begining_stock', array(
				'no_perkiraan'	=> $no_perkiraan,
				'nama'			=> $no_perkiraan,
				'saldoawal'		=> $saldo_akhir,
				'bln'			=> $bln_next_str,
				'thn'			=> $thn_next
			));
		}

		return $result;
	}
}
