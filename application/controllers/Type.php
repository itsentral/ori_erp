<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Type extends CI_Controller {
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
		
		// $get_Data			= $this->master_model->getData('raw_categories');
		$get_Data			= $this->db->query("SELECT*FROM raw_categories WHERE `delete`='N'")->result();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Category Material',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Category Material');
		$this->load->view('Type/index',$data);
	}
	public function add(){		
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$category		= strtoupper($data['category']);
			$descr			= $data['descr'];
			
			//pengurutan kode
			$srcType			= "SELECT MAX(id_category) as maxP FROM raw_categories WHERE id_category LIKE 'TYP-%' ";
			$numrowPlant		= $this->db->query($srcType)->num_rows();
			$resultPlant		= $this->db->query($srcType)->result_array();
			$angkaUrut2			= $resultPlant[0]['maxP'];
			$urutan2			= (int)substr($angkaUrut2, 4, 4);
			$urutan2++;
			$urut2				= sprintf('%04s',$urutan2);
			$kode_plant			= "TYP-".$urut2;
			
			$numberMax_en		= $data['numberMax_en'];
			$numberMax_bq		= $data['numberMax_bq'];
			
			if($numberMax_en != 0){
				$ListDetail_en		= $data['ListDetail_en'];
			}
			if($numberMax_bq != 0){
				$ListDetail_bq		= $data['ListDetail_bq'];
			}
			
			$ArrListDetail_en	= array();
			$ArrListDetail_bq	= array();
			
			if($numberMax_en != 0){
				foreach($ListDetail_en AS $val => $valx){
					$flagEn = 'N';
						if(!empty($valx['flag_active'])){
							$flagEn = 'Y';
						}
					$ArrListDetail_en[$val]['id_category'] = $kode_plant;
					$ArrListDetail_en[$val]['nm_category_standard'] = $valx['nm_category_standard'];
					$ArrListDetail_en[$val]['descr'] 				= $valx['descr'];
					$ArrListDetail_en[$val]['type'] 				= "ENG";
					$ArrListDetail_en[$val]['flag_active'] 			= $flagEn;
					$ArrListDetail_en[$val]['created_date']	 		= date('Y-m-d H:i:s');
					$ArrListDetail_en[$val]['created_by'] 			= $data_session['ORI_User']['username'];
				}
				// print_r($ArrListDetail_en);
			}
			
			if($numberMax_bq != 0){
				foreach($ListDetail_bq AS $val => $valx){
					$flagBq = 'N';
						if(!empty($valx['flag_active'])){
							$flagBq = 'Y';
						}
					$ArrListDetail_bq[$val]['id_category'] = $kode_plant;
					$ArrListDetail_bq[$val]['nm_category_standard'] = $valx['nm_category_standard'];
					$ArrListDetail_bq[$val]['descr'] 				= $valx['descr'];
					$ArrListDetail_bq[$val]['type'] 				= "BQ";
					$ArrListDetail_bq[$val]['flag_active'] 			= $flagBq;
					$ArrListDetail_bq[$val]['created_date']	 		= date('Y-m-d H:i:s');
					$ArrListDetail_bq[$val]['created_by'] 			= $data_session['ORI_User']['username'];
				}
				// print_r($ArrListDetail_bq);
			}
			
			//check nama type material
			$qNmType	= "SELECT * FROM raw_categories WHERE category = '".$category."' ";
			$numType	= $this->db->query($qNmType)->num_rows();
			// echo $numType; exit;
			$data	= array(
				'id_category' 	=> $kode_plant,
				'category' 		=> $category,
				'descr' 		=> $descr,
				'flag_active' 	=> 'Y',
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			// echo "<pre>"; print_r($data);
			// exit;
			if($numType > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Material name already exists. Please check back ...'
				);
			}
			else{
				$this->db->trans_start();
				$this->db->insert('raw_categories', $data);
				if($numberMax_en != 0){
					$this->db->insert_batch('raw_category_standard', $ArrListDetail_en);
				}
				if($numberMax_bq != 0){
					$this->db->insert_batch('raw_category_standard', $ArrListDetail_bq);
				}
				$this->db->trans_complete();
				
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Type failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Type Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					history('Add Category Material '.$kode_plant);
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
				'title'			=> 'Add Category Material',
				'action'		=> 'add',
				'data_menu'		=> $get_Data
			);
			$this->load->view('Type/add',$data);
		}
	}
	
	public function updateDetail(){
		$data			= $this->input->post();
		$Arr_Data		= array();
		$categoryID		= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$detEditEn		= $data['EdListDetail_en'];
		$detEditBq		= $data['EdListDetail_bq'];
		
		$ArrListDetail_en	= array();
		$ArrListDetail_bq	= array();
		
		foreach($detEditEn AS $val => $valx){
			$flagEn = 'N';
				if(!empty($valx['flag_active'])){
					$flagEn = 'Y';
				}
			$ArrListDetail_en[$val]['id_category_standard'] = $valx['id_category_standard'];
			$ArrListDetail_en[$val]['nm_category_standard'] = $valx['nm_category_standard'];
			$ArrListDetail_en[$val]['descr'] 				= $valx['descr'];
			$ArrListDetail_en[$val]['flag_active'] 			= $flagEn;
			$ArrListDetail_en[$val]['modified_date']	 	= date('Y-m-d H:i:s');
			$ArrListDetail_en[$val]['modified_by'] 			= $data_session['ORI_User']['username'];
		}
		
		foreach($detEditBq AS $val => $valx){
			$flagBq = 'N';
				if(!empty($valx['flag_active'])){
					$flagBq = 'Y';
				}
			$ArrListDetail_bq[$val]['id_category_standard'] = $valx['id_category_standard'];
			$ArrListDetail_bq[$val]['nm_category_standard'] = $valx['nm_category_standard'];
			$ArrListDetail_bq[$val]['descr'] 				= $valx['descr'];
			$ArrListDetail_bq[$val]['flag_active'] 			= $flagBq;
			$ArrListDetail_bq[$val]['modified_date']	 	= date('Y-m-d H:i:s');
			$ArrListDetail_bq[$val]['modified_by'] 			= $data_session['ORI_User']['username'];
		}
		
		// print_r($ArrListDetail_en);
		// print_r($ArrListDetail_bq);
		// exit;
		$this->db->trans_start();
		$this->db->update_batch('raw_category_standard', $ArrListDetail_en, 'id_category_standard');
		$this->db->update_batch('raw_category_standard', $ArrListDetail_bq, 'id_category_standard');
		$this->db->trans_complete();
		
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
			history('Update Categori Standard '.$categoryID);
		}
		
		// print_r($Arr_Data); exit; 
		echo json_encode($Arr_Data);
	}
	
	public function edit(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data					= $this->input->post();
			$Arr_Kembali			= array();
			$category				= strtoupper($this->input->post('category'));
			$id_category			= $this->input->post('id_category');
			$flag_active			= ($this->input->post('flag_active') == 'Y')?'Y':'N';
			$descr					= $this->input->post('descr');
			$data_session			= $this->session->userdata;			
			
			//check nama type material
			// $qNmType	= "SELECT * FROM raw_categories WHERE category = '".$category."' ";
			// $numType	= $this->db->query($qNmType)->num_rows();
			
			$Arr_Update = array(
				'category' 		=> $category,
				'descr' 		=> $descr,
				'flag_active' 	=> $flag_active,
				'modified_by' 	=> $data_session['ORI_User']['username'],
				'modified_date' 	=> date('Y-m-d H:i:s')
			);
			
			$numberMax_en			= $data['numberMax_en'];
			$numberMax_bq			= $data['numberMax_bq'];
			
			if($numberMax_en != 0){
				$ListDetail_en		= $data['ListDetail_en'];
			}
			if($numberMax_bq != 0){
				$ListDetail_bq		= $data['ListDetail_bq'];
			}
			
			$ArrListDetail_en	= array();
			$ArrListDetail_bq	= array();
			
			if($numberMax_en != 0){
				foreach($ListDetail_en AS $val => $valx){
					$flagEn = 'N';
						if(!empty($valx['flag_active'])){
							$flagEn = 'Y';
						}
					$ArrListDetail_en[$val]['id_category'] 			= $id_category;
					$ArrListDetail_en[$val]['nm_category_standard'] = $valx['nm_category_standard'];
					$ArrListDetail_en[$val]['descr'] 				= $valx['descr'];
					$ArrListDetail_en[$val]['type'] 				= "ENG";
					$ArrListDetail_en[$val]['flag_active'] 			= $flagEn;
					$ArrListDetail_en[$val]['created_date']	 		= date('Y-m-d H:i:s');
					$ArrListDetail_en[$val]['created_by'] 			= $data_session['ORI_User']['username'];
				}
				// print_r($ArrListDetail_en);
			}
			
			if($numberMax_bq != 0){
				foreach($ListDetail_bq AS $val => $valx){
					$flagBq = 'N';
						if(!empty($valx['flag_active'])){
							$flagBq = 'Y';
						}
					$ArrListDetail_bq[$val]['id_category'] 			= $id_category;
					$ArrListDetail_bq[$val]['nm_category_standard'] = $valx['nm_category_standard'];
					$ArrListDetail_bq[$val]['descr'] 				= $valx['descr'];
					$ArrListDetail_bq[$val]['type'] 				= "BQ";
					$ArrListDetail_bq[$val]['flag_active'] 			= $flagBq;
					$ArrListDetail_bq[$val]['created_date']	 		= date('Y-m-d H:i:s');
					$ArrListDetail_bq[$val]['created_by'] 			= $data_session['ORI_User']['username'];
				}
				// print_r($ArrListDetail_bq);
			}
			
			// echo "<pre>"; print_r($Arr_Update);
			// exit;
			$this->db->trans_start();
			$this->db->where('id_category', $id_category);
			$this->db->update('raw_categories', $Arr_Update);
			if($numberMax_en != 0){
				$this->db->insert_batch('raw_category_standard',$ArrListDetail_en );
			}
			if($numberMax_bq != 0){
				$this->db->insert_batch('raw_category_standard',$ArrListDetail_bq );
			}
			$this->db->trans_complete();
			// if($numType > 1){
				// $Arr_Data		= array(
					// 'status'		=> 3,
					// 'pesan'			=> 'Material name already exists. Please check back ...'
				// );
			// }
			// else{
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
					history('Update Type Material '.$id_category.' with username : '.$data_session['ORI_User']['username']);
				}
			// }
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$id = $this->uri->segment(3);
			
			// $detail				= $this->master_model->getData('raw_categories','id_category',$id); 
			$detail			= $this->db->query("SELECT * FROM raw_categories WHERE id_category = '".$id."' ")->result_array();
			$detailEn		= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category = '".$id."' AND type='ENG' ")->result_array();
			$detailBQ		= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category = '".$id."' AND type='BQ' ")->result_array();
			$data = array(
				'title'			=> 'Edit Category Material',
				'action'		=> 'edit',
				'row'			=> $detail,
				'detailEn'		=> $detailEn,
				'detailBQ'		=> $detailBQ
			);
			
			$this->load->view('Type/edit',$data);
		}
	}

	function hapus(){
		$idCategory = $this->uri->segment(3);
		// echo $idCategory; exit;
		$data_session			= $this->session->userdata;	
		$Arr_Delete = array(
			'delete' 		=> "Y",
			'delete_by' 	=> $data_session['ORI_User']['username'],
			'delete_date' 	=> date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
		$this->db->where('id_category', $idCategory);
		$this->db->update('raw_categories', $Arr_Delete);
		$this->db->where('id_category', $idCategory);
		$this->db->update('raw_category_standard', $Arr_Delete);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete type material data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete type material data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Type Material with ID : '.$idCategory);
		}
		echo json_encode($Arr_Data);
	}
	
	public function modalDetail(){
		$this->load->view('Type/modalDetail');
	}
	
	public function getStandard(){
		$sqlSup	= "SELECT * FROM standard_permanen WHERE deleted='N' ORDER BY nm_spesifikasi ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();
		
		$option	= "<option value='0'>Select An Standard</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['nm_spesifikasi']."'>".$valx['nm_spesifikasi']." (".$valx['satuan'].")</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
}