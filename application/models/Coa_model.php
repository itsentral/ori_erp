<?php
class Coa_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	function GetCoa($level='5'){
		$GL = $this->load->database('gl', TRUE);
		$aMenu		= array();
		$GL->select("a.no_perkiraan as coa, a.nama, a.no_perkiraan, a.nama as nama_perkiraan");
		$GL->from('coa_master a');
		$GL->where('a.level',$level);
		$GL->order_by('a.no_perkiraan', 'asc');
		$query = $GL->get();
		if($query->num_rows() != 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	
}