<?php
class Api_sample_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
    
    //=================================================================================================================
    //==========================================LATE ENGINNERING=======================================================
    //=================================================================================================================

    public function apiDataSample()
	{
		$result = $this->db->get('api_sample')->result_array();
		return $result;
	}
	
	public function apiInsertData($dataArray=null)
	{
		$this->db->insert('api_sample', $dataArray);
	}
	
	public function apiDeleteData($id=null)
	{
		$this->db->where('id', $id);
		$this->db->delete('api_sample');
	}

}
?>