<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_opname_generate_model extends CI_Model {

	private $table = 'warehouse_stock_per_day_duplikat';

	public function __construct(){
		parent::__construct();
	}

	/**
	 * Hitung jumlah record berdasarkan tanggal
	 */
	public function count_by_date($date){
		$this->db->where("DATE(hist_date)", $date);
		return $this->db->count_all_results($this->table);
	}

	/**
	 * Ambil tanggal terakhir yang ada datanya sebelum tanggal target
	 */
	public function get_last_date_before($date){
		$sql = "SELECT DATE(hist_date) as tgl 
				FROM ".$this->table." 
				WHERE DATE(hist_date) < '".$this->db->escape_str($date)."' 
				GROUP BY DATE(hist_date) 
				ORDER BY DATE(hist_date) DESC 
				LIMIT 1";
		$result = $this->db->query($sql)->row();
		return (!empty($result)) ? $result->tgl : null;
	}

	/**
	 * Ambil tanggal setelah tanggal tertentu (untuk validasi hapus)
	 */
	public function get_next_date_after($date){
		$sql = "SELECT DATE(hist_date) as tgl 
				FROM ".$this->table." 
				WHERE DATE(hist_date) > '".$this->db->escape_str($date)."' 
				GROUP BY DATE(hist_date) 
				ORDER BY DATE(hist_date) ASC 
				LIMIT 1";
		$result = $this->db->query($sql)->row();
		return (!empty($result)) ? $result->tgl : null;
	}

	/**
	 * Ambil semua data stok pada tanggal tertentu
	 */
	public function get_stock_by_date($date){
		$sql = "SELECT * FROM ".$this->table." 
				WHERE DATE(hist_date) = '".$this->db->escape_str($date)."' 
				ORDER BY id_material ASC, id_gudang ASC";
		return $this->db->query($sql)->result_array();
	}

	/**
	 * Ambil transaksi dari warehouse_history pada tanggal tertentu
	 */
	public function get_transactions_by_date($date){
		$sql = "SELECT 
					id_material,
					idmaterial,
					nm_material,
					id_category,
					nm_category,
					id_gudang,
					kd_gudang,
					id_gudang_dari,
					kd_gudang_dari,
					id_gudang_ke,
					kd_gudang_ke,
					jumlah_mat,
					harga,
					total_harga,
					ket,
					no_ipp,
					update_date
				FROM warehouse_history 
				WHERE DATE(update_date) = '".$this->db->escape_str($date)."'
				ORDER BY id ASC";
		return $this->db->query($sql)->result_array();
	}

	/**
	 * Insert batch data stok
	 */
	public function insert_batch_stock($data){
		if(empty($data)) return false;

		$this->db->trans_start();

		// Insert in chunks of 500 to avoid memory issues
		$chunks = array_chunk($data, 500);
		foreach($chunks as $chunk){
			$this->db->insert_batch($this->table, $chunk);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * Hapus data pada tanggal tertentu
	 */
	public function delete_by_date($date){
		$this->db->trans_start();
		$this->db->where("DATE(hist_date)", $date);
		$this->db->delete($this->table);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * Ambil semua tanggal yang ada datanya
	 */
	public function get_all_dates(){
		$sql = "SELECT DATE(hist_date) as tgl, COUNT(*) as total_item, 
					SUM(total_harga) as total_nilai
				FROM ".$this->table." 
				GROUP BY DATE(hist_date) 
				ORDER BY DATE(hist_date) ASC";
		return $this->db->query($sql)->result_array();
	}

	/**
	 * Summary per tanggal
	 */
	public function get_summary_by_date($date){
		$sql = "SELECT 
					COUNT(*) as total_item,
					SUM(qty_stock) as total_qty,
					SUM(total_value) as total_value_costbook,
					SUM(total_harga) as total_value_harga
				FROM ".$this->table." 
				WHERE DATE(hist_date) = '".$this->db->escape_str($date)."'";
		$result = $this->db->query($sql)->row_array();
		return $result;
	}

	/**
	 * Get data JSON untuk DataTables server-side
	 */
	public function get_data_json($date_filter){
		$CI =& get_instance();

		$where = "DATE(a.hist_date) = '".$this->db->escape_str($date_filter)."'";

		// Search
		$search = $CI->input->post('search')['value'];
		$search_where = '';
		if(!empty($search)){
			$search_where = " AND (a.idmaterial LIKE '%".$this->db->escape_like_str($search)."%' 
							  OR a.nm_material LIKE '%".$this->db->escape_like_str($search)."%' 
							  OR a.nm_category LIKE '%".$this->db->escape_like_str($search)."%'
							  OR a.kd_gudang LIKE '%".$this->db->escape_like_str($search)."%')";
		}

		// Total records
		$sql_total = "SELECT COUNT(*) as total FROM ".$this->table." a WHERE ".$where;
		$total_records = $this->db->query($sql_total)->row()->total;

		// Filtered records
		$sql_filtered = "SELECT COUNT(*) as total FROM ".$this->table." a WHERE ".$where.$search_where;
		$total_filtered = $this->db->query($sql_filtered)->row()->total;

		// Order
		$columns = array('', 'a.idmaterial', 'a.nm_material', 'a.nm_category', 'a.kd_gudang', 'a.qty_stock', 'a.qty_booking', 'a.qty_rusak', 'a.costbook', 'a.total_value', 'a.harga', 'a.total_harga');
		$order_col = $CI->input->post('order')[0]['column'];
		$order_dir = $CI->input->post('order')[0]['dir'];
		$order_by = '';
		if(isset($columns[$order_col]) && !empty($columns[$order_col])){
			$order_by = " ORDER BY ".$columns[$order_col]." ".$order_dir;
		} else {
			$order_by = " ORDER BY a.id_material ASC";
		}

		// Limit
		$start  = $CI->input->post('start');
		$length = $CI->input->post('length');
		$limit  = " LIMIT ".$start.", ".$length;

		// Query data
		$sql = "SELECT 
					a.id,
					a.idmaterial,
					a.id_material,
					a.nm_material,
					a.nm_category,
					a.kd_gudang,
					a.id_gudang,
					a.qty_stock,
					a.qty_booking,
					a.qty_rusak,
					a.costbook,
					a.total_value,
					a.harga,
					a.total_harga,
					a.update_by,
					a.update_date,
					a.hist_date
				FROM ".$this->table." a 
				WHERE ".$where.$search_where.$order_by.$limit;
		$data = $this->db->query($sql)->result_array();

		$output = array(
			"draw"            => intval($CI->input->post('draw')),
			"recordsTotal"    => $total_records,
			"recordsFiltered" => $total_filtered,
			"data"            => array()
		);

		$no = $start + 1;
		foreach($data as $row){
			$output['data'][] = array(
				$no,
				$row['idmaterial'],
				$row['nm_material'],
				$row['nm_category'],
				$row['kd_gudang'],
				number_format((float)$row['qty_stock'], 2, '.', ','),
				number_format((float)$row['qty_booking'], 2, '.', ','),
				number_format((float)$row['qty_rusak'], 2, '.', ','),
				number_format((float)$row['costbook'], 0, ',', '.'),
				number_format((float)$row['total_value'], 0, ',', '.'),
				number_format((float)$row['harga'], 0, ',', '.'),
				number_format((float)$row['total_harga'], 0, ',', '.'),
			);
			$no++;
		}

		echo json_encode($output);
	}
}
