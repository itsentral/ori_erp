<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Api_sample extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('api_sample_model');
	}

	public function api_get_data()
	{
		$data_result = $this->api_sample_model->apiDataSample();
		$ArrData = [];
		foreach ($data_result as $key => $value) {
			$ArrData[$key]['id'] = $value['id'];
			$ArrData[$key]['nama'] = $value['nama_lengkap'];
			$ArrData[$key]['telp'] = $value['no_telp'];
			$ArrData[$key]['gender'] = $value['gender'];
			$ArrData[$key]['hobi'] = $value['hobi'];
			$ArrData[$key]['alamat'] = $value['alamat'];
			$ArrData[$key]['pekerjaan'] = $value['pekerjaan'];
		}
		echo json_encode($ArrData);
	}
	
	public function api_post_data()
	{
		$data_api 	= file_get_contents('php://input');
		// $data 		= json_decode($data_api, true); //data menjadi array $data['nama'];
		$data 		= json_decode($data_api); //data menjadi object $data->nama;
		
		$dataInsert = array(
			'nama_lengkap' => $data->nama,
			'no_telp' => $data->no_telp,
			'gender' => $data->gender,
			'hobi' => json_encode($data->hobi),
			'pekerjaan' => $data->profesi,
			'alamat' => $data->alamat,
		);
		
		// print_r($dataInsert); exit;
		
		$this->db->trans_start();
			if(!empty($data->nama)){
				$this->api_sample_model->apiInsertData($dataInsert);
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Failed process data ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Success process data ...',
				'status'	=> 1
			);
		}
		
		echo json_encode($Arr_Data);
	}
	
	public function api_delete_data()
	{
		$data_api 	= file_get_contents('php://input');
		$data 		= json_decode($data_api); //data menjadi object $data->nama;
		
		$id = $data->id;
		
		// echo $id; 
		// exit;
		
		$this->db->trans_start();
			$this->api_sample_model->apiDeleteData($id);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Failed process data ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Success process data ...',
				'status'	=> 1
			);
		}
		
		echo json_encode($Arr_Data);
	}

}
