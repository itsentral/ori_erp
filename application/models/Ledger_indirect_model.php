<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_indirect_model extends CI_Model {

	private $id_gudang = 10;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get ledger data Indirect dari tabel warehouse_rutin_history
	 * Filter id_gudang = 10 (Indirect)
	 * Logika In/Out:
	 * - gudang_dari kosong/null dan id_gudang_dari = 0 => In (adjustment/purchase)
	 * - id_gudang = id_gudang_ke => In (penambahan gudang)
	 * - id_gudang = id_gudang_dari => Out (pengurangan gudang)
	 * Nilai = jumlah_qty * harga
	 */
	public function get_ledger_data($bulan, $tahun){
		$result = array('data' => array(), 'saldo_awal' => 0);

		$tgl_filter = $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT);

		// Ambil saldo awal dari begining_stock
		$saldo_awal = 0;
		$sql_saldo = "SELECT saldoawal FROM begining_stock 
					  WHERE no_perkiraan = 'indirect' 
					  AND bln = '".$this->db->escape_str($bulan)."' 
					  AND thn = '".$this->db->escape_str($tahun)."' 
					  LIMIT 1";
		$row_saldo = $this->db->query($sql_saldo)->row();
		$saldo_awal = (!empty($row_saldo)) ? (float)$row_saldo->saldoawal : 0;
		$result['saldo_awal'] = $saldo_awal;

		$sql_detail = "SELECT 
							a.id,
							a.code_group,
							a.category_awal,
							a.category_code,
							a.material_name,
							a.id_gudang,
							a.gudang,
							a.id_gudang_dari,
							a.gudang_dari,
							a.id_gudang_ke,
							a.gudang_ke,
							a.qty_stock_awal,
							a.qty_stock_akhir,
							a.qty_rusak_awal,
							a.qty_rusak_akhir,
							a.no_trans,
							a.jumlah_qty,
							a.ket,
							a.update_by,
							a.update_date,
							a.harga,
							a.saldo_awal AS hist_saldo_awal,
							a.saldo_akhir AS hist_saldo_akhir,
							b.material_name AS material_name_new,
							c.category AS nm_category
						FROM warehouse_rutin_history a
						LEFT JOIN con_nonmat_new b ON a.code_group = b.code_group AND b.deleted_date IS NULL
						LEFT JOIN con_nonmat_category_awal c ON a.category_awal = c.id
						WHERE a.id_gudang = '".$this->id_gudang."'
						AND DATE_FORMAT(a.update_date, '%Y-%m') = '".$tgl_filter."'
						ORDER BY a.update_date ASC, a.id ASC";
		$detail_rows = $this->db->query($sql_detail)->result_array();

		$running_saldo = $saldo_awal;
		$total_debet = 0;
		$total_kredit = 0;

		foreach($detail_rows as $row){
			$harga = (float)$row['harga'];
			$jumlah_qty = abs((float)$row['jumlah_qty']);
			$nilai = $jumlah_qty * $harga;
			$val_in		= 0;
			$val_out	= 0;
			$keterangan = '';

			// Logika In/Out
			if($row['id_gudang_dari'] === null || $row['id_gudang_dari'] === ''){
				// adjustment keluar
				$val_out = $nilai;
				$running_saldo -= $nilai;
				$total_kredit += $nilai;
				$keterangan = 'adjustment';
			} else if($row['id_gudang_dari'] == '0' || $row['id_gudang_dari'] == 0){
				// adjustment masuk / purchase
				$val_in = $nilai;
				$running_saldo += $nilai;
				$total_debet += $nilai;
				$keterangan = 'adjustment';
			} else if($row['id_gudang'] == $row['id_gudang_dari']){
				// keluar dari gudang ini
				$val_out = $nilai;
				$running_saldo -= $nilai;
				$total_kredit += $nilai;
				$keterangan = 'pengurangan gudang';
			} else if($row['id_gudang'] == $row['id_gudang_ke']){
				// masuk ke gudang ini
				$val_in = $nilai;
				$running_saldo += $nilai;
				$total_debet += $nilai;
				$keterangan = 'penambahan gudang';
			}

			$nm_material = (!empty($row['material_name_new'])) ? $row['material_name_new'] : $row['material_name'];
			$nm_category = (!empty($row['nm_category'])) ? $row['nm_category'] : '';

			$result['data'][] = array(
				'nm_material'	=> $nm_material,
				'nm_category'	=> $nm_category,
				'tanggal'		=> date('d-m-Y H:i', strtotime($row['update_date'])),
				'kode_trans'	=> $row['no_trans'],
				'keterangan'	=> $keterangan,
				'harga'			=> $harga,
				'in'			=> $val_in,
				'out'			=> $val_out,
				'saldo'			=> $running_saldo
			);
		}

		// Update begining_stock untuk indirect
		$saldo_akhir = $running_saldo;
		$bln_str = str_pad($bulan, 2, '0', STR_PAD_LEFT);
		$cek = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = 'indirect' AND bln = '".$this->db->escape_str($bln_str)."' AND thn = '".$this->db->escape_str($tahun)."'")->row();
		if(!empty($cek)){
			$this->db->where('id', $cek->id);
			$this->db->update('begining_stock', array(
				'saldo_akhir' => $saldo_akhir,
				'debet' => $total_debet,
				'kredit' => $total_kredit
			));
		} else {
			$this->db->insert('begining_stock', array(
				'no_perkiraan'	=> 'indirect',
				'nama'			=> 'indirect',
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

		$cek_next = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = 'indirect' AND bln = '".$bln_next_str."' AND thn = '".$thn_next."'")->row();
		if(!empty($cek_next)){
			$this->db->where('id', $cek_next->id);
			$this->db->update('begining_stock', array('saldoawal' => $saldo_akhir));
		} else {
			$this->db->insert('begining_stock', array(
				'no_perkiraan'	=> 'indirect',
				'nama'			=> 'indirect',
				'saldoawal'		=> $saldo_akhir,
				'bln'			=> $bln_next_str,
				'thn'			=> $thn_next
			));
		}

		return $result;
	}
}
