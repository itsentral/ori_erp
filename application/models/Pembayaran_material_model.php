<?php
class Pembayaran_material_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	public function get_data_json_request_payment_header($sqlwhere=''){
		$sql = "SELECT a.*, b.nm_supplier FROM purchase_order_request_payment_header a left join supplier b on a.id_supplier =b.id_supplier WHERE 1=1 ".($sqlwhere==''?'':" and ".$sqlwhere)." order by a.id desc ";
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function get_data_json_request_payment($sqlwhere=''){ 

		$sql = "SELECT a.*, b.nm_supplier FROM purchase_order_request_paymentx a left join supplier b on a.id_supplier =b.id_supplier WHERE 1=1 ".($sqlwhere==''?'':" and ".$sqlwhere)." order by a.approved_on desc ";
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function get_data_json_request_payment_nm($sqlwhere=''){

		$sql = "SELECT a.*, b.nm_supplier FROM purchase_order_request_payment_nm a left join supplier b on a.id_supplier =b.id_supplier WHERE 1=1 ".($sqlwhere==''?'':" and ".$sqlwhere)." order by a.approved_on desc ";
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function get_data_json_jurnal($sqlwhere=''){

		$sql = "SELECT nomor,tanggal,no_reff,stspos FROM jurnaltras a WHERE 1=1 ".($sqlwhere==''?'':" and ".$sqlwhere)." group by nomor,tanggal,no_reff,stspos order by no_reff desc ";
		$query = $this->db->query($sql);
		return $query->result();
	}
}