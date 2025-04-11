<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accessories extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('accessories_model');

		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}
	
	public function index(){
		$this->accessories_model->index();
	}
	
	//Bolt & Nut
	public function data_side_bold_nut(){
		$this->accessories_model->get_json_bold_nut();
	}
	
	public function add_bold_nut(){
		$this->accessories_model->add_bold_nut();
	}
	
	//Plate
	public function data_side_plate(){
		$this->accessories_model->get_json_plate();
	}
	
	public function add_plate(){
		$this->accessories_model->add_plate();
	}
	
	//Gasket
	public function data_side_gasket(){
		$this->accessories_model->get_json_gasket();
	}
	
	public function add_gasket(){
		$this->accessories_model->add_gasket();
	}
	
	//Lainnya
	public function data_side_lainnya(){
		$this->accessories_model->get_json_lainnya();
	}
	
	public function add_lainnya(){
		$this->accessories_model->add_lainnya();
	}
	
	
	
	public function hapus(){
		$this->accessories_model->hapus();
	}
	
	public function tab_last(){
		$this->accessories_model->tab_last();
	}
	
}