<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan_schedule extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('plan_schedule_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}
	
	public function so(){
		$this->plan_schedule_model->so();
	}
	
	public function server_side_schedule_so(){
		$this->plan_schedule_model->server_side_schedule_so();
	}
	
	public function modal_detail_so(){
		$this->plan_schedule_model->modal_detail_so();
	}
	
	public function modal_choose_so(){
		$this->plan_schedule_model->modal_choose_so();
	}
	
	public function temp_format(){
		$this->plan_schedule_model->temp_format();
	}
	
	public function import_data(){
		$this->plan_schedule_model->import_data();
	}
	
	public function update_spool(){
		$this->plan_schedule_model->update_spool();
	}
	
	public function save_category(){
		$this->plan_schedule_model->save_category();
	}
	
	public function import_data2(){
		$this->plan_schedule_model->import_data2();
	}
	
	public function modal_edit_choose_so(){
		$this->plan_schedule_model->modal_edit_choose_so();
	}
	
	public function save_new_spool(){
		$this->plan_schedule_model->save_new_spool();
	}
	
	public function proses_jadwal(){
		$this->plan_schedule_model->proses_jadwal();
	}
	
	public function dropdown_estimasi(){
		$this->plan_schedule_model->dropdown_estimasi();
	}
	
	public function dropdown_estimasi_pipe(){
		$this->plan_schedule_model->dropdown_estimasi_pipe();
	}
	
	public function get_update_estimasi(){
		$this->plan_schedule_model->get_update_estimasi();
	}
	
	public function get_update_estimasi_auto(){
		$this->plan_schedule_model->get_update_estimasi_auto();
	}
	
	public function get_remove_estimasi(){
		$this->plan_schedule_model->get_remove_estimasi();
	} 
	
	public function get_product(){
		$this->plan_schedule_model->get_product();
	}
	
	public function save_schedule(){
		$this->plan_schedule_model->save_schedule();
	} 
	
	public function proses_split(){
		$this->plan_schedule_model->proses_split();
	}
	
	public function delete_spool_satuan(){
		$this->plan_schedule_model->delete_spool_satuan();
	} 
	
	public function proses_costcenter(){
		$this->plan_schedule_model->proses_costcenter();
	} 
	
	public function dropdown_costcenter(){
		$this->plan_schedule_model->dropdown_costcenter();
	}
	
	public function approve_satuan_product(){
		$this->plan_schedule_model->approve_satuan_product();
	}
	
	
	
	

	public function order_produksi(){
		$this->plan_schedule_model->order_produksi();
	}
	
	public function server_side_order_produksi(){
		$this->plan_schedule_model->server_side_order_produksi();
	}
	
	public function print_order_produksi(){
		$this->plan_schedule_model->print_order_produksi();
	}
	
	
	
	public function fd_plus(){
		$this->plan_schedule_model->fd_plus();
	}
}
