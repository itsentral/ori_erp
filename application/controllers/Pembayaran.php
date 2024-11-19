<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('pembayaran_rutin_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}
	
	//================================================================================================================
	//===========================================BUDGET RUTIN=========================================================
	//================================================================================================================
	
	public function master_rutin(){
		$this->pembayaran_rutin_model->index();
	}
	
	public function add_master_pembayaran_rutin(){
		$this->pembayaran_rutin_model->add_master_pembayaran_rutin();
	}
	
	public function server_side_rutin(){
		$this->pembayaran_rutin_model->get_data_json_rutin();
	}
	
	public function get_add(){
		$this->pembayaran_rutin_model->get_add();
	}
	
	public function delete_permanent(){
		$this->pembayaran_rutin_model->delete_permanent();
	}
	
	//================================================================================================================
	//================================PERMINTAAN PEMBAYARAN RUTIN=====================================================
	//================================================================================================================
	
	public function payment_request_rutin(){
		$this->pembayaran_rutin_model->payment_request_rutin();
	}
	
	public function server_side_request_rutin(){
		$this->pembayaran_rutin_model->get_data_json_request_rutin(); 
	}
	
	public function add_request_pembayaran_rutin(){
		$this->pembayaran_rutin_model->add_request_pembayaran_rutin();
	}
	
	public function ajukan_pembayaran(){
		$this->pembayaran_rutin_model->ajukan_pembayaran();
	}
	
	public function payment_pembayaran_rutin(){
		$this->pembayaran_rutin_model->payment_pembayaran_rutin();
	}
	
	public function get_add_payment(){
		$this->pembayaran_rutin_model->get_add_payment();
	}
	
}