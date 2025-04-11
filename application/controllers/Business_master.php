<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Business_Master extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
		$this->folder		= 'Master_Business';
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->master_model->getData('bidang_usaha');
		
		
		$data = array(
			'title'			=> 'Indeks Of Business Fields',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Business Fields');
		$this->load->view($this->folder.'/index',$data);
	}
	public function add(){
		if($this->input->post()){
			$Group_Name			= $this->input->post('bidang_usaha');
			$Keterangan			= $this->input->post('keterangan');
			$Cek_Data			= $this->master_model->getCount('bidang_usaha',"LOWER(bidang_usaha)",strtolower($Group_Name));
			if($Cek_Data > 0){
				$Arr_Kembali		= array(
					'status'		=> 2,
					'pesan'			=> 'Business Fields Already Exist. Please Different Business Fields.......'
				);
			}else{
				$data_session		= $this->session->userdata;
				$det_Insert			= array(
					'bidang_usaha'		=> strtoupper($Group_Name),
					'keterangan'		=> ucwords(strtolower($Keterangan)),
					'created_on'		=> date('Y-m-d H:i:s'),
					'created_by'		=> $data_session['ORI_User']['username']
					
				);
				if($this->master_model->simpan('bidang_usaha',$det_Insert)){
					$Arr_Kembali		= array(
						'status'		=> 1,
						'pesan'			=> 'Add Business Fields. Thank you & have a nice day.......'
					);
					history('Add Business Fields'.$Group_Name);
					
				}else{
					$Arr_Kembali		= array(
						'status'		=> 2,
						'pesan'			=> 'Add Business Fields failed. Please try again later......'
					);
					
				}
			}
			echo json_encode($Arr_Kembali);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('business_master'));
			}
			$data = array(
				'title'			=> 'Add Business Fields',
				'action'		=> 'add'
			);
			
			$this->load->view($this->folder.'/add',$data);
		}
	}
	
	public function edit($kode=''){
		if($this->input->post()){
			$Group_id			= $this->input->post('id_bidang_usaha');
			$Group_Name			= $this->input->post('bidang_usaha');
			$Keterangan			= $this->input->post('keterangan');
			$Query_Cek			= "SELECT * FROM bidang_usaha WHERE LOWER(bidang_usaha)='".strtolower($Group_Name)."' AND id_bidang_usaha <> '".$Group_id."'";
			$Cek_Data			= $this->db->query($Query_Cek)->num_rows();
			if($Cek_Data > 0){
				$Arr_Kembali		= array(
					'status'		=> 2,
					'pesan'			=> 'Business Fields Already Exist. Please Different Business Fields.......'
				);
			}else{
				$data_session		= $this->session->userdata;
				//echo"<pre>";print_r($data_session);exit;
				$det_Insert			= array(
					'bidang_usaha'		=> strtoupper($Group_Name),
					'keterangan'		=> ucwords(strtolower($Keterangan)),
					'modified_on'		=> date('Y-m-d H:i:s'),
					'modified_by'		=> $data_session['ORI_User']['username']
					
				);
				if($this->master_model->getUpdate('bidang_usaha',$det_Insert,'id_bidang_usaha',$Group_id)){
					
					$Arr_Kembali		= array(
						'status'		=> 1,
						'pesan'			=> 'Update Business Fields Success. Thank you & have a nice day.......'
					);
					history('Edit Data Business Fields ID : '.$Group_id);
				}else{
					$Arr_Kembali		= array(
						'status'		=> 2,
						'pesan'			=> 'Update Business Fields failed. Please try again later......'
					);
					
				}
			}
			echo json_encode($Arr_Kembali);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('business_master'));
			}
			$int_data			= $this->master_model->getData('bidang_usaha','id_bidang_usaha',$kode);
			$data = array(
				'title'			=> 'Edit Business Fields',
				'action'		=> 'edit',
				'rows'			=> $int_data
			);
			
			$this->load->view($this->folder.'/edit',$data);
		}
	}
	
	public function view($kode=''){
		
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('business_master'));
		}
		$int_data			= $this->master_model->getData('bidang_usaha','id_bidang_usaha',$kode);
		$data = array(
			'title'			=> 'View Business Fields',
			'action'		=> 'view',
			'rows'			=> $int_data
		);
		
		$this->load->view($this->folder.'/view',$data);
		
	}
	
	function delete_data($id=''){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if($Arr_Akses['delete'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('business_master'));
		}
		$rows_data			= $this->master_model->getData('bidang_usaha','id_bidang_usaha',$id);
		$count_data			= $this->master_model->getCount('customer','LOWER(bidang_usaha)',strtolower($rows_data[0]->bidang_usaha));
		if($count_data > 0 ){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-danger\" id=\"flash-message\">This Business Fields Can't Be Deleted, Data Used By Other Tables..........!!</div>");
			
		}else{
			$this->db->delete("bidang_usaha",array('id_bidang_usaha'=>$id));
			if($this->db->affected_rows()>0){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-success\" id=\"flash-message\">Data has been successfully deleted...........!!</div>");
				history('Delete Data Business Fields : '.$rows_data[0]->bidang_usaha);
				
			}
		}
		redirect(site_url('business_master'));
	}
}