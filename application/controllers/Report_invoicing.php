<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_invoicing extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('report_invoicing_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

    public function index(){
		$this->report_invoicing_model->invoicing();
	}
	public function server_side_inv(){
		$this->report_invoicing_model->get_data_json_inv();
	}

	public function modal_detail_invoice(){
		$this->report_invoicing_model->modal_detail_invoice();
	}

  

}
?>