<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Insert_select extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
    
    function insert_select_history_production(){ 
		$this->db->trans_start();
			$this->db->truncate('table_history_pro_header');
				
			$sqlUpdate = "
                INSERT INTO table_history_pro_header ( id_produksi, 
                id_category, 
                id_product, 
                product_ke, 
                qty_akhir, 
                qty, 
                status_by, 
                status_date, 
                id_production_detail, 
				id,
				id_milik, 
                create_by, 
                create_date ) 
                SELECT
					a.id_produksi,
					a.id_category,
					a.id_product,
					a.product_ke,
					a.qty_akhir,
					a.qty,
					a.status_by,
					a.status_date,
					a.id_production_detail,
					a.id,
					a.id_milik,
					'".$this->session->userdata['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
					FROM
                        history_pro_header a";
			
			$this->db->query($sqlUpdate);
			
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);				
			history('Success insert select history production');
		}
		echo json_encode($Arr_Data);
    }
    
    function insert_select_history_production_check(){ 
		$this->db->trans_start();
			$this->db->truncate('table_history_pro_header_tmp');
				
			$sqlUpdate = "
                INSERT INTO table_history_pro_header_tmp ( id_produksi, 
                id_category, 
                id_product, 
                product_ke, 
                qty_akhir, 
                qty, 
                status_by, 
                status_date, 
                id_production_detail, 
				id,
				id_milik,
                create_by, 
                create_date ) 
                SELECT
					a.id_produksi,
					a.id_category,
					a.id_product,
					a.product_ke,
					a.qty_akhir,
					a.qty,
					a.status_by,
					a.status_date,
					a.id_production_detail,
					a.id,
					a.id_milik,
					'".$this->session->userdata['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
					FROM
                        history_pro_header_tmp a";
			
			$this->db->query($sqlUpdate);
			
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);				
			history('Success insert select check history production');
		}
		echo json_encode($Arr_Data);
	}

	function update_manual_product_list(){ 

		$rest 	= $this->db
						->select('a.*')
						->from('component_header a')
						->join('table_product_list b','a.id_product = b.id_product','left')
						->order_by('a.created_date','DESC')
						->limit(10)
						->get()
						->result_array();
		$ArrInsert = array();
		foreach($rest AS $val => $valx){
			$ArrInsert[$val]['id'] 			= $valx['id_product'];
			$ArrInsert[$val]['diameter'] 	= $valx['diameter'];
			$ArrInsert[$val]['diameter2'] 	= $valx['diameter2'];
		}
		print_r($ArrInsert); exit;
		$this->db->trans_start();
				
		$this->db->update('table_product_list',$ArrInsert,'id')	;
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);				
			history('Update diameter dan diameter 2 manual');
		}
		print_r($Arr_Data);
	}

	
}
