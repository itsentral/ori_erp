<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Satuan extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
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
		
		$get_Data			= $this->master_model->getData('raw_pieces');
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Type',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Pieces/Satuan');
		$this->load->view('Satuan/index',$data);
	}
	public function add(){		
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$kode_satuan		= strtoupper($data['kode_satuan']);
			$nama_satuan		= strtoupper($data['nama_satuan']);
			$descr				= $data['descr'];
			
			//check kode satuan
			$qCodeSatu	= "SELECT * FROM raw_pieces WHERE kode_satuan = '".$kode_satuan."' ";
			$numCdSt	= $this->db->query($qCodeSatu)->num_rows();
			
			//check kode satuan
			$qNmSatu	= "SELECT * FROM raw_pieces WHERE nama_satuan = '".$nama_satuan."' ";
			$numNmSt	= $this->db->query($qNmSatu)->num_rows();
			
			// echo $numType; exit;
			$data	= array(
				'kode_satuan' 	=> $kode_satuan,
				'nama_satuan' 	=> $nama_satuan,
				'descr' 		=> $descr,
				'flag_active' 	=> 'Y',
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			// echo "<pre>"; print_r($data);
			if($numCdSt > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Pieces code already exists. Please check back ...'
				);
			}
			elseif($numNmSt > 0){
				$Arr_Kembali		= array(
					'status'		=> 4,
					'pesan'			=> 'Pieces name already exists. Please check back ...'
				);
			}
			else{
				if($this->master_model->simpan('raw_pieces',$data)){
					$Arr_Kembali		= array(
						'status'		=> 1,
						'pesan'			=> 'Add Pieces Success. Thank you & have a nice day.......'
					);
					history('Add Pieces with code '.$kode_satuan);
				}else{
					$Arr_Kembali		= array(
						'status'		=> 2,
						'pesan'			=> 'Add Pieces failed. Please try again later......'
					);
					
				}
			}
			echo json_encode($Arr_Kembali);
		}else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$arr_Where			= array('flag_active'=>'1');
			$get_Data			= $this->master_model->getMenu($arr_Where);
			$data = array(
				'title'			=> 'Add Pieces',
				'action'		=> 'add',
				'data_menu'		=> $get_Data
			);
			$this->load->view('Satuan/add',$data);
		}
	}
	public function edit(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data					= $this->input->post();
			$Arr_Kembali			= array();
			$id_satuan				= $this->input->post('id_satuan');
			$kode_satuan			= strtoupper($this->input->post('kode_satuan'));
			$nama_satuan			= strtoupper($this->input->post('nama_satuan'));
			$flag_active			= ($this->input->post('flag_active') == 'Y')?'Y':'N';
			$descr					= $this->input->post('descr');
			$data_session			= $this->session->userdata;			
			
			//check kode satuan
			$qCodeSatu	= "SELECT * FROM raw_pieces WHERE kode_satuan = '".$kode_satuan."' ";
			$numCdSt	= $this->db->query($qCodeSatu)->num_rows();
			
			//check kode satuan
			$qNmSatu	= "SELECT * FROM raw_pieces WHERE nama_satuan = '".$nama_satuan."' ";
			$numNmSt	= $this->db->query($qNmSatu)->num_rows();
			
			$Arr_Update = array(
				'kode_satuan' 		=> $kode_satuan,
				'nama_satuan' 		=> $nama_satuan,
				'descr' 			=> $descr,
				'flag_active' 		=> $flag_active,
				'modified_by' 		=> $data_session['ORI_User']['username'],
				'modified_date' 	=> date('Y-m-d H:i:s')
			);
			// echo "<pre>"; print_r($Arr_Update);
			// exit;
			$this->db->trans_start();
			$this->db->where('id_satuan', $id_satuan);
			$this->db->update('raw_pieces', $Arr_Update);
			$this->db->trans_complete();
			if($numCdSt > 0){
				$Arr_Data		= array(
					'status'		=> 3,
					'pesan'			=> 'Pieces code already exists. Please check back ...'
				);
			}
			elseif($numNmSt > 0){
				$Arr_Data		= array(
					'status'		=> 4,
					'pesan'			=> 'Pieces name already exists. Please check back ...'
				);
			}
			else{
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Data	= array(
						'pesan'		=>'Update type material data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Data	= array(
						'pesan'		=>'Update type material data success. Thanks ...',
						'status'	=> 1
					);
					history('Update Type Material ['.$id_satuan.'] with username : '.$data_session['ORI_User']['username']);
				}
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$id = $this->uri->segment(3);
			
			// $detail				= $this->master_model->getData('raw_pieces','id_category',$id);  
			$detail		= $this->db->query("SELECT * FROM raw_pieces WHERE id_satuan = '".$id."' ")->result_array();
			$data = array(
				'title'			=> 'Edit Pieces',
				'action'		=> 'edit',
				'row'			=> $detail
			);
			
			$this->load->view('Satuan/edit',$data);   
		}
	}

	function hapus(){
		$idCategory = $this->uri->segment(3);
		// echo $idCategory; exit;
		//nm satuan yang dihapus untuk history
		$qNmStuan	= "SELECT * FROM raw_pieces WHERE id_satuan='".$idCategory."' ";
		$restDtSt	= $this->db->query($qNmStuan)->result_array();
		$kd_satuan	= $restDtSt[0]['kode_satuan'];
		
		$this->db->trans_start();
		$this->db->where('id_satuan', $idCategory);
		$this->db->delete('raw_pieces');
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Pieces with Kode/Id : '.$kd_satuan.'/'.$idCategory);
		}
		echo json_encode($Arr_Data);
	}
}