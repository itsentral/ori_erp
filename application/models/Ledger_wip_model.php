<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_wip_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get ledger data WIP
	 * In  = dari tabel data_erp_wip dimana total_price_debet > 0
	 * Out = dari tabel data_erp_wip_group dimana jenis = 'out'
	 */
	public function get_ledger_data($bulan, $tahun){
		$result = array('data' => array());

		$tgl_filter = $tahun.'-'.str_pad($bulan, 2, '0', STR_PAD_LEFT);

		// Ambil saldo awal dari tabel begining_stock
		$sql_saldo = "SELECT saldoawal FROM begining_stock 
					  WHERE no_perkiraan = 'wip' 
					  AND bln = '".$this->db->escape_str($bulan)."' 
					  AND thn = '".$this->db->escape_str($tahun)."' 
					  LIMIT 1";
		$row_saldo = $this->db->query($sql_saldo)->row();
		$saldo_awal = (!empty($row_saldo)) ? (float)$row_saldo->saldoawal : 0;

		// =============================================
		// DATA IN: dari tabel data_erp_wip dimana total_price_debet > 0
		// =============================================
		$sql_in = "SELECT 
						a.id,
						a.tanggal,
						a.keterangan,
						a.no_so,
						a.product,
						a.no_spk,
						a.kode_trans,
						a.id_trans,
						a.nm_material,
						a.total_price_debet,
						a.created_date
					FROM data_erp_wip a
					WHERE DATE_FORMAT(a.tanggal, '%Y-%m') = '".$tgl_filter."'
					AND a.total_price_debet > 0
					ORDER BY a.tanggal ASC, a.id ASC";
		$rows_in = $this->db->query($sql_in)->result_array();

		// =============================================
		// DATA OUT: dari tabel data_erp_wip_group dimana jenis = 'out'
		// =============================================
		$sql_out = "SELECT 
						a.id,
						a.tanggal,
						a.keterangan,
						a.no_so,
						a.product,
						a.no_spk,
						a.kode_trans,
						a.id_trans,
						a.nm_material,
						a.nilai_wip,
						a.jenis,
						a.created_date
					FROM data_erp_wip_group a
					WHERE DATE_FORMAT(a.tanggal, '%Y-%m') = '".$tgl_filter."'
					AND LOWER(a.jenis) = 'out'
					ORDER BY a.tanggal ASC, a.id ASC";
		$rows_out = $this->db->query($sql_out)->result_array();

		// Gabungkan IN dan OUT ke satu array lalu sort berdasarkan tanggal
		$all_rows = array();

		foreach($rows_in as $row){
			$all_rows[] = array(
				'id'			=> $row['id'],
				'tanggal'		=> $row['tanggal'],
				'keterangan'	=> $row['keterangan'],
				'no_so'			=> $row['no_so'],
				'product'		=> $row['product'],
				'no_spk'		=> $row['no_spk'],
				'kode_trans'	=> $row['kode_trans'],
				'id_trans'		=> $row['id_trans'],
				'nm_material'	=> $row['nm_material'],
				'nilai'			=> (float)$row['total_price_debet'],
				'type'			=> 'in'
			);
		}

		foreach($rows_out as $row){
			$all_rows[] = array(
				'id'			=> $row['id'],
				'tanggal'		=> $row['tanggal'],
				'keterangan'	=> $row['keterangan'],
				'no_so'			=> $row['no_so'],
				'product'		=> $row['product'],
				'no_spk'		=> $row['no_spk'],
				'kode_trans'	=> $row['kode_trans'],
				'id_trans'		=> $row['id_trans'],
				'nm_material'	=> $row['nm_material'],
				'nilai'			=> (float)$row['nilai_wip'],
				'type'			=> 'out'
			);
		}

		// Sort by tanggal ASC, id ASC
		usort($all_rows, function($a, $b){
			$cmp = strcmp($a['tanggal'], $b['tanggal']);
			if($cmp === 0){
				return $a['id'] - $b['id'];
			}
			return $cmp;
		});

		// Build group
		$group = array(
			'nama'			=> 'WORK IN PROCESS (WIP)',
			'saldo_awal'	=> $saldo_awal,
			'detail'		=> array()
		);

		$running_saldo = $saldo_awal;
		$total_debet = 0;
		$total_kredit = 0;

		foreach($all_rows as $row){
			$nilai	= (float)$row['nilai'];
			$val_in		= 0;
			$val_out	= 0;

			if($row['type'] == 'in'){
				$val_in = $nilai;
				$running_saldo += $nilai;
				$total_debet += $nilai;
			} else {
				$val_out = $nilai;
				$running_saldo -= $nilai;
				$total_kredit += $nilai;
			}

			// No Reff: id_trans + no_so
			$no_reff = $row['id_trans'].' '.$row['no_so'];

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

		// Update saldo_akhir di begining_stock bulan ini
		$saldo_akhir = $running_saldo;
		$cek = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = 'wip' AND bln = '".$this->db->escape_str($bulan)."' AND thn = '".$this->db->escape_str($tahun)."'")->row();
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

		$cek_next = $this->db->query("SELECT id FROM begining_stock WHERE no_perkiraan = 'wip' AND bln = '".$bln_next_str."' AND thn = '".$thn_next."'")->row();
		if(!empty($cek_next)){
			$this->db->where('id', $cek_next->id);
			$this->db->update('begining_stock', array('saldoawal' => $saldo_akhir));
		} else {
			$this->db->insert('begining_stock', array(
				'no_perkiraan'	=> 'wip',
				'nama'			=> 'wip',
				'saldoawal'		=> $saldo_akhir,
				'bln'			=> $bln_next_str,
				'thn'			=> $thn_next
			));
		}

		return $result;
	}
}
