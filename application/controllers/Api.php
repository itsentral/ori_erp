<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('api_model');
	}

	public function users($id = null)
	{
		if($id != null ):
			$this->db->where('id',$id);
		endif;
		$sql = $this->db->get('api_sample')->result();
		echo  json_encode($sql);
	}

	public function api_late_enginnering()
	{
		$data_result = $this->api_model->api_late_enginnering();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$release_plus2 = date('Y-m-d', strtotime('+2 days', strtotime($value['modified_date'])));
			if($value['release_est_date'] > $release_plus2){
				$ArrDetail[$key]['no_ipp'] = $value['no_ipp'];
				$ArrDetail[$key]['nm_customer'] = $value['nm_customer'];
				$ArrDetail[$key]['project'] = $value['project'];
				$ArrDetail[$key]['status'] = $value['status'];
				$ArrDetail[$key]['release_date'] = $value['modified_date'];
				$ArrDetail[$key]['release_date_est'] = $value['release_est_date'];
			}
		}
		echo json_encode($ArrDetail);
	}

	public function api_late_enginnering_count()
	{
		$data_result = $this->api_model->api_late_enginnering();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$release_plus2 = date('Y-m-d', strtotime('+2 days', strtotime($value['modified_date'])));
			if($value['release_est_date'] > $release_plus2){
				$ArrDetail[$key]['no_ipp'] = $value['no_ipp'];
				$ArrDetail[$key]['nm_customer'] = $value['nm_customer'];
				$ArrDetail[$key]['project'] = $value['project'];
				$ArrDetail[$key]['status'] = $value['status'];
				$ArrDetail[$key]['release_date'] = $value['modified_date'];
				$ArrDetail[$key]['release_date_est'] = $value['release_est_date'];
			}
		}
		echo count($ArrDetail);
	}

	public function api_get_material()
	{
		$where = $this->input->post('id');
		$where_array = explode(",", $where);
		// print_r($where_array);
		// exit;
		$data_result = $this->api_model->api_get_material($where_array);
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['id'] = $value['id_material'];
			$ArrDetail[$key]['nama'] = $value['nm_material'];
		}
		echo json_encode($ArrDetail);
	}

	public function api_get_price_ref_material()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_price_ref_material($id_material);
		$price 			= (!empty($data_result[0]->price))?$data_result[0]->price:0;
		echo $price;
	}

	public function api_get_material_name()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_material_name($id_material);
		$nama 			= (!empty($data_result[0]->nama))?$data_result[0]->nama:0;
		echo $nama;
	}

	public function api_get_accessories()
	{
		$data_result = $this->api_model->api_get_accessories();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['id'] = $value['id'];
			$ArrDetail[$key]['nama'] = get_name_acc($value['id']);
		}
		echo json_encode($ArrDetail);
	}

	public function api_get_price_ref_acc()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_price_ref_acc($id_material);
		$price 			= (!empty($data_result[0]->price))?$data_result[0]->price:0;
		echo $price;
	}

	public function api_get_acc_name()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_acc_name($id_material);
		$nama 			= (!empty($data_result[0]->id))?get_name_acc($data_result[0]->id):'not found';
		echo $nama;
	}

	public function api_get_unit_name()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_unit_name($id_material);
		$nama 			= (!empty($data_result[0]->kode))?strtoupper($data_result[0]->kode):'not found';
		echo $nama;
	}

	public function api_get_rate_mp()
	{
		$data_result 	= $this->api_model->api_get_rate_mp();
		$nama 			= (!empty($data_result[0]->rate))?strtoupper($data_result[0]->rate):0;
		echo $nama;
	}

	public function api_get_percent_foh()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_percent_foh($id_material);
		$nama 			= (!empty($data_result[0]->rate))?strtoupper($data_result[0]->rate):0;
		echo $nama;
	}

	public function api_get_percent_process()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_percent_process($id_material);
		$nama 			= (!empty($data_result[0]->rate))?strtoupper($data_result[0]->rate):0;
		echo $nama;
	}
	
	public function api_get_list_shipping()
	{
		$data_result 	= $this->api_model->api_get_list_shipping();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['id'] = $value['id'];
			$ArrDetail[$key]['nama'] = $value['nama'];
			$ArrDetail[$key]['tipe'] = $value['tipe'];
		}
		echo json_encode($ArrDetail);
	}
	
	public function api_get_country()
	{
		$id_material 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_country($id_material);
		$nama 			= (!empty($data_result[0]->nama))?strtoupper($data_result[0]->nama):'';
		echo $nama;
	}
	
	public function api_get_list_trucking()
	{
		$data_result 	= $this->api_model->api_get_list_trucking();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['id'] = $value['id'];
			$ArrDetail[$key]['nama'] = $value['name'];
		}
		echo json_encode($ArrDetail);
	}
	
	public function api_get_list_darat()
	{
		$data_result 	= $this->api_model->api_get_list_darat();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['area'] = $value['area'];
		}
		echo json_encode($ArrDetail);
	}
	
	public function api_get_list_laut()
	{
		$data_result 	= $this->api_model->api_get_list_laut();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['area'] = $value['area'];
		}
		echo json_encode($ArrDetail);
	}
	
	public function api_get_cost_export()
	{
		$id 	= $this->input->post('id');
		$id2 	= $this->input->post('id2');
		$data_result 	= $this->api_model->api_get_cost_export($id, $id2);
		$nama 			= (!empty($data_result[0]->harga))?number_format($data_result[0]->harga,2):0;
		echo $nama;
	}
	
	public function api_get_destination()
	{
		$area 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_destination($area);
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['tujuan'] = $value['tujuan'];
		}
		echo json_encode($ArrDetail);
	}
	
	public function api_get_truck()
	{
		$area 	= $this->input->post('id');
		$dest 	= $this->input->post('id2');
		$data_result 	= $this->api_model->api_get_truck($area, $dest);
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['id'] 		= $value['id_truck'];
			$ArrDetail[$key]['nama'] 	= $value['nama_truck'];
		}
		echo json_encode($ArrDetail);
	}
	
	public function api_get_rate_truck_lokal()
	{
		$id 	= $this->input->post('id');
		$id2 	= $this->input->post('id2');
		$id3 	= $this->input->post('id3');
		
		$data_result 	= $this->api_model->api_get_rate_truck_lokal($id, $id2, $id3);
		$rate 			= (!empty($data_result[0]->rate))?$data_result[0]->rate:0;
		
		$getCur		= $this->db->limit(1)->get_where('cost_convert', array('cur_1'=>'USD','cur_2'=>'IDR'))->result();
		$kurs		= $getCur[0]->price;
		
		$price		= number_format($rate / $kurs,2);
		
		echo $price;
	}
	
	public function api_get_truck_name()
	{
		$id 	= $this->input->post('id');
		$data_result 	= $this->api_model->api_get_truck_name($id);
		$nama 			= (!empty($data_result[0]->nama_truck))?$data_result[0]->nama_truck:'';
		echo $nama;
	}
	
	public function api_get_customer()
	{
		$data_result = $this->api_model->api_get_customer();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$ArrDetail[$key]['id'] = $value['id_customer'];
			$ArrDetail[$key]['nama'] = $value['nm_customer'];
		}
		echo json_encode($ArrDetail);
	}

	// tambahan
	public function api_cost_machine_tanki(){
		$data_result 			= $this->api_model->api_cost_machine_tanki();
		$machine_cost_per_hour 	= (!empty($data_result[0]->machine_cost_per_hour))?$data_result[0]->machine_cost_per_hour:0;
		echo $machine_cost_per_hour;
	}

	public function getDataAccessories(){
		$term2 = $this->input->get('term');
		$term = $term2['term'];
		$SQL = "SELECT id, id_material, nama, SUBSTRING(spesifikasi,1,30) as spesifikasi2, SUBSTRING(material,1,30) as material FROM accessories WHERE nama LIKE '%".$term."%' LIMIT 50 ";
		// echo $SQL;
		$result = $this->db->query($SQL)->result_array();

		$Array = [];
		foreach ($result as $key => $value) {
			$Array[$key]['id'] 		= $value['id'];
			$Array[$key]['title'] 	= strtoupper($value['id_material']." - ".$value['nama']." ".$value['spesifikasi2']." ".$value['material']);
		}

		echo json_encode($Array);
	}
	
	
}
