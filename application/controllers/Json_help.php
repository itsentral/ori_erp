<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Json_help extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	public function editDefaultEst(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		$qIdProduct		= "SELECT * FROM component_default WHERE id_product='".$data['id_product']."' ";
		$getIdProduct	= $this->db->query($qIdProduct)->num_rows();
		// echo $qIdProduct; exit;
		$getProduct	= $this->db->query($qIdProduct)->result_array();
		
		$insertData	= array(
			'id_product' 			=> $data['id_product'],
			'product_parent' 		=> $data['product_parent'],
			'standart_code' 		=> $data['standart_code'],
			'diameter' 				=> $data['diameter'],
			'diameter2' 			=> $data['diameter2'],
			'liner' 				=> $data['liner'],
			'pn' 					=> $data['pn'],

			'waste' 				=> $data['waste'],
			'overlap' 				=> $data['overlap'],
			'max' 					=> (!empty($data['max']))?$data['max']/100:'0',
			'min' 					=> (!empty($data['min']))?$data['min']/100:'0',
			'plastic_film' 			=> (!empty($data['plastic_film']))?$data['plastic_film']:'1',
			
			'lin_resin_veil_a' 		=> $data['lin_resin_veil_a'],
			'lin_resin_veil_b' 		=> $data['lin_resin_veil_b'],
			'lin_resin_veil' 		=> (!empty($data['lin_resin_veil']))?$data['lin_resin_veil']:'1',
			'lin_resin_veil_add_a' 	=> $data['lin_resin_veil_add_a'],
			'lin_resin_veil_add_b' 	=> $data['lin_resin_veil_add_b'],
			'lin_resin_veil_add' 	=> (!empty($data['lin_resin_veil_add']))?$data['lin_resin_veil_add']:'1',
			'lin_resin_csm_a' 		=> $data['lin_resin_csm_a'],
			'lin_resin_csm_b' 		=> $data['lin_resin_csm_b'],
			'lin_resin_csm' 		=> (!empty($data['lin_resin_csm']))?$data['lin_resin_csm']:'1',
			'lin_resin_csm_add_a' 	=> $data['lin_resin_csm_add_a'],
			'lin_resin_csm_add_b' 	=> $data['lin_resin_csm_add_b'],
			'lin_resin_csm_add' 	=> (!empty($data['lin_resin_csm_add']))?$data['lin_resin_csm_add']:'1',
			'lin_faktor_veil' 		=> (!empty($data['lin_faktor_veil']))?$data['lin_faktor_veil']:'1',
			'lin_faktor_veil_add' 	=> (!empty($data['lin_faktor_veil_add']))?$data['lin_faktor_veil_add']:'1',
			'lin_faktor_csm' 		=> (!empty($data['lin_faktor_csm']))?$data['lin_faktor_csm']:'1',
			'lin_faktor_csm_add' 	=> (!empty($data['lin_faktor_csm_add']))?$data['lin_faktor_csm_add']:'1',
			'lin_resin' 			=> (!empty($data['lin_resin']))?$data['lin_resin']:'1',
			
			'str_resin_csm_a' 		=> $data['str_resin_csm_a'],
			'str_resin_csm_b' 		=> $data['str_resin_csm_b'],
			'str_resin_csm' 		=> (!empty($data['str_resin_csm']))?$data['str_resin_csm']:'1',
			'str_resin_csm_add_a' 	=> $data['str_resin_csm_add_a'],
			'str_resin_csm_add_b' 	=> $data['str_resin_csm_add_b'],
			'str_resin_csm_add' 	=> (!empty($data['str_resin_csm_add']))?$data['str_resin_csm_add']:'1',
			'str_resin_wr_a' 		=> $data['str_resin_wr_a'],
			'str_resin_wr_b' 		=> $data['str_resin_wr_b'],
			'str_resin_wr' 			=> (!empty($data['str_resin_wr']))?$data['str_resin_wr']:'1',
			'str_resin_wr_add_a' 	=> $data['str_resin_wr_add_a'],
			'str_resin_wr_add_b' 	=> $data['str_resin_wr_add_b'],
			'str_resin_wr_add' 		=> (!empty($data['str_resin_wr_add']))?$data['str_resin_wr_add']:'1',
			'str_resin_rv_a' 		=> $data['str_resin_rv_a'],
			'str_resin_rv_b' 		=> $data['str_resin_rv_b'],
			'str_resin_rv' 			=> (!empty($data['str_resin_rv']))?$data['str_resin_rv']:'1',
			'str_resin_rv_add_a' 	=> $data['str_resin_rv_add_a'],
			'str_resin_rv_add_b' 	=> $data['str_resin_rv_add_b'],
			'str_resin_rv_add' 		=> (!empty($data['str_resin_rv_add']))?$data['str_resin_rv_add']:'1',
			'str_faktor_csm' 		=> (!empty($data['str_faktor_csm']))?$data['str_faktor_csm']:'1',
			'str_faktor_csm_add' 	=> (!empty($data['str_faktor_csm_add']))?$data['str_faktor_csm_add']:'1',
			'str_faktor_wr' 		=> (!empty($data['str_faktor_wr']))?$data['str_faktor_wr']:'1',
			'str_faktor_wr_add' 	=> (!empty($data['str_faktor_wr_add']))?$data['str_faktor_wr_add']:'1',
			'str_faktor_rv' 		=> (!empty($data['str_faktor_rv']))?$data['str_faktor_rv']:'1',
			'str_faktor_rv_bw' 		=> (!empty($data['str_faktor_rv_bw']))?$data['str_faktor_rv_bw']:'1',
			'str_faktor_rv_jb' 		=> (!empty($data['str_faktor_rv_jb']))?$data['str_faktor_rv_jb']:'1',
			'str_faktor_rv_add' 	=> (!empty($data['str_faktor_rv_add']))?$data['str_faktor_rv_add']:'1',
			'str_faktor_rv_add_bw' 	=> (!empty($data['str_faktor_rv_add_bw']))?$data['str_faktor_rv_add_bw']:'1',
			'str_faktor_rv_add_jb' 	=> (!empty($data['str_faktor_rv_add_jb']))?$data['str_faktor_rv_add_jb']:'1',
			'str_resin' 			=> (!empty($data['str_resin']))?$data['str_resin']:'1',

			'str_n1_resin_csm_a' 		=> (!empty($data['str_n1_resin_csm_a']))?$data['str_n1_resin_csm_a']:'1',
			'str_n1_resin_csm_b' 		=> (!empty($data['str_n1_resin_csm_b']))?$data['str_n1_resin_csm_b']:'1',
			'str_n1_resin_csm' 			=> (!empty($data['str_n1_resin_csm']))?$data['str_n1_resin_csm']:'1',
			'str_n1_resin_csm_add_a' 	=> (!empty($data['str_n1_resin_csm_add_a']))?$data['str_n1_resin_csm_add_a']:'1',
			'str_n1_resin_csm_add_b' 	=> (!empty($data['str_n1_resin_csm_add_b']))?$data['str_n1_resin_csm_add_b']:'1',
			'str_n1_resin_csm_add' 		=> (!empty($data['str_n1_resin_csm_add']))?$data['str_n1_resin_csm_add']:'1',
			'str_n1_resin_wr_a' 		=> (!empty($data['str_n1_resin_wr_a']))?$data['str_n1_resin_wr_a']:'1',
			'str_n1_resin_wr_b' 		=> (!empty($data['str_n1_resin_wr_b']))?$data['str_n1_resin_wr_b']:'1',
			'str_n1_resin_wr' 			=> (!empty($data['str_n1_resin_wr']))?$data['str_n1_resin_wr']:'1',
			'str_n1_resin_wr_add_a' 	=> (!empty($data['str_n1_resin_wr_add_a']))?$data['str_n1_resin_wr_add_a']:'1',
			'str_n1_resin_wr_add_b' 	=> (!empty($data['str_n1_resin_wr_add_b']))?$data['str_n1_resin_wr_add_b']:'1',
			'str_n1_resin_wr_add' 		=> (!empty($data['str_n1_resin_wr_add']))?$data['str_n1_resin_wr_add']:'1',
			'str_n1_resin_rv_a' 		=> (!empty($data['str_n1_resin_rv_a']))?$data['str_n1_resin_rv_a']:'1',
			'str_n1_resin_rv_b' 		=> (!empty($data['str_n1_resin_rv_b']))?$data['str_n1_resin_rv_b']:'1',
			'str_n1_resin_rv' 			=> (!empty($data['str_n1_resin_rv']))?$data['str_n1_resin_rv']:'1',
			'str_n1_resin_rv_add_a' 	=> (!empty($data['str_n1_resin_rv_add_a']))?$data['str_n1_resin_rv_add_a']:'1',
			'str_n1_resin_rv_add_b' 	=> (!empty($data['str_n1_resin_rv_add_b']))?$data['str_n1_resin_rv_add_b']:'1',
			'str_n1_resin_rv_add' 		=> (!empty($data['str_n1_resin_rv_add']))?$data['str_n1_resin_rv_add']:'1',
			'str_n1_faktor_csm' 		=> (!empty($data['str_n1_faktor_csm']))?$data['str_n1_faktor_csm']:'1',
			'str_n1_faktor_csm_add' 	=> (!empty($data['str_n1_faktor_csm_add']))?$data['str_n1_faktor_csm_add']:'1',
			'str_n1_faktor_wr' 			=> (!empty($data['str_n1_faktor_wr']))?$data['str_n1_faktor_wr']:'1',
			'str_n1_faktor_wr_add' 		=> (!empty($data['str_n1_faktor_wr_add']))?$data['str_n1_faktor_wr_add']:'1',
			'str_n1_faktor_rv' 			=> (!empty($data['str_n1_faktor_rv']))?$data['str_n1_faktor_rv']:'1',
			'str_n1_faktor_rv_bw' 		=> (!empty($data['str_n1_faktor_rv_bw']))?$data['str_n1_faktor_rv_bw']:'1',
			'str_n1_faktor_rv_jb' 		=> (!empty($data['str_n1_faktor_rv_jb']))?$data['str_n1_faktor_rv_jb']:'1',
			'str_n1_faktor_rv_add' 		=> (!empty($data['str_n1_faktor_rv_add']))?$data['str_n1_faktor_rv_add']:'1',
			'str_n1_faktor_rv_add_bw' 	=> (!empty($data['str_n1_faktor_rv_add_bw']))?$data['str_n1_faktor_rv_add_bw']:'1',
			'str_n1_faktor_rv_add_jb' 	=> (!empty($data['str_n1_faktor_rv_add_jb']))?$data['str_n1_faktor_rv_add_jb']:'1',
			'str_n1_resin' 				=> (!empty($data['str_n1_resin']))?$data['str_n1_resin']:'1',
			'str_n1_resin_thickness' 	=> (!empty($data['str_n1_resin_thickness']))?$data['str_n1_resin_thickness']:'1',
			
			'str_n2_resin_csm_a' 		=> (!empty($data['str_n2_resin_csm_a']))?$data['str_n2_resin_csm_a']:'1',
			'str_n2_resin_csm_b' 		=> (!empty($data['str_n2_resin_csm_b']))?$data['str_n2_resin_csm_b']:'1',
			'str_n2_resin_csm' 			=> (!empty($data['str_n2_resin_csm']))?$data['str_n2_resin_csm']:'1',
			'str_n2_resin_csm_add_a' 	=> (!empty($data['str_n2_resin_csm_add_a']))?$data['str_n2_resin_csm_add_a']:'1',
			'str_n2_resin_csm_add_b' 	=> (!empty($data['str_n2_resin_csm_add_b']))?$data['str_n2_resin_csm_add_b']:'1',
			'str_n2_resin_csm_add' 		=> (!empty($data['str_n2_resin_csm_add']))?$data['str_n2_resin_csm_add']:'1',
			'str_n2_resin_wr_a' 		=> (!empty($data['str_n2_resin_wr_a']))?$data['str_n2_resin_wr_a']:'1',
			'str_n2_resin_wr_b' 		=> (!empty($data['str_n2_resin_wr_b']))?$data['str_n2_resin_wr_b']:'1',
			'str_n2_resin_wr' 			=> (!empty($data['str_n2_resin_wr']))?$data['str_n2_resin_wr']:'1',
			'str_n2_resin_wr_add_a' 	=> (!empty($data['str_n2_resin_wr_add_a']))?$data['str_n2_resin_wr_add_a']:'1',
			'str_n2_resin_wr_add_b' 	=> (!empty($data['str_n2_resin_wr_add_b']))?$data['str_n2_resin_wr_add_b']:'1',
			'str_n2_resin_wr_add' 		=> (!empty($data['str_n2_resin_wr_add']))?$data['str_n2_resin_wr_add']:'1',
			'str_n2_faktor_csm' 		=> (!empty($data['str_n2_faktor_csm']))?$data['str_n2_faktor_csm']:'1',
			'str_n2_faktor_csm_add' 	=> (!empty($data['str_n2_faktor_csm_add']))?$data['str_n2_faktor_csm_add']:'1',
			'str_n2_faktor_wr' 			=> (!empty($data['str_n2_faktor_wr']))?$data['str_n2_faktor_wr']:'1',
			'str_n2_faktor_wr_add' 		=> (!empty($data['str_n2_faktor_wr_add']))?$data['str_n2_faktor_wr_add']:'1',
			'str_n2_resin' 				=> (!empty($data['str_n2_resin']))?$data['str_n2_resin']:'1',
			'str_n2_resin_thickness' 	=> (!empty($data['str_n2_resin_thickness']))?$data['str_n2_resin_thickness']:'1',

			'eks_resin_veil_a' 		=> $data['eks_resin_veil_a'],
			'eks_resin_veil_b' 		=> $data['eks_resin_veil_b'],
			'eks_resin_veil' 		=> (!empty($data['eks_resin_veil']))?$data['eks_resin_veil']:'1',
			'eks_resin_veil_add_a' 	=> $data['eks_resin_veil_add_a'],
			'eks_resin_veil_add_b' 	=> $data['eks_resin_veil_add_b'],
			'eks_resin_veil_add' 	=> (!empty($data['eks_resin_veil_add']))?$data['eks_resin_veil_add']:'1',
			'eks_resin_csm_a' 		=> $data['eks_resin_csm_a'],
			'eks_resin_csm_b' 		=> $data['eks_resin_csm_b'],
			'eks_resin_csm' 		=> (!empty($data['eks_resin_csm']))?$data['eks_resin_csm']:'1',
			'eks_resin_csm_add_a' 	=> $data['eks_resin_csm_add_a'],
			'eks_resin_csm_add_b' 	=> $data['eks_resin_csm_add_b'],
			'eks_resin_csm_add' 	=> (!empty($data['eks_resin_csm_add']))?$data['eks_resin_csm_add']:'1',
			'eks_faktor_veil' 		=> (!empty($data['eks_faktor_veil']))?$data['eks_faktor_veil']:'1',
			'eks_faktor_veil_add' 	=> (!empty($data['eks_faktor_veil_add']))?$data['eks_faktor_veil_add']:'1',
			'eks_faktor_csm' 		=> (!empty($data['eks_faktor_csm']))?$data['eks_faktor_csm']:'1',
			'eks_faktor_csm_add' 	=> (!empty($data['eks_faktor_csm_add']))?$data['eks_faktor_csm_add']:'1',
			'eks_resin' 			=> (!empty($data['eks_resin']))?$data['eks_resin']:'1',
			'topcoat_resin' 		=> (!empty($data['topcoat_resin']))?$data['topcoat_resin']:'1',
			'modified_by'			=> $data_session['ORI_User']['username'],
			'modified_date'			=> date('Y-m-d H:i:s')
		);
		
		// print_r($insertData);
		// echo $getProduct[0]['product_parent'];
		// exit; $data['product_parent']
		if($getIdProduct > 0){
			if($getProduct[0]['product_parent'] == 'pipe'){
				$helpc	= 'pipe_edit';
			}
			if($getProduct[0]['product_parent'] == 'end cap'){
				$helpc	= 'end_cap_edit';
			}
			if($getProduct[0]['product_parent'] == 'blind flange'){
				$helpc	= 'blind_flange_edit';
			}
			if($getProduct[0]['product_parent'] == 'concentric reducer'){
				$helpc	= 'concentric_reducer_edit';
			}
			if($getProduct[0]['product_parent'] == 'eccentric reducer'){
				$helpc	= 'eccentric_reducer_edit';
			}
			if($getProduct[0]['product_parent'] == 'elbow mould'){
				$helpc	= 'elbow_mould_edit';
			}
			if($getProduct[0]['product_parent'] == 'elbow mitter'){
				$helpc	= 'elbow_mitter_edit';
			}
			if($getProduct[0]['product_parent'] == 'flange mould'){
				$helpc	= 'flange_mould_edit';
			}
			if($getProduct[0]['product_parent'] == 'flange slongsong'){
				$helpc	= 'flange_slongsong_edit';
			}
			if($getProduct[0]['product_parent'] == 'colar'){
				$helpc	= 'colar_edit';
			}
			if($getProduct[0]['product_parent'] == 'colar slongsong'){
				$helpc	= 'colar_slongsong_edit';
			}
			if($getProduct[0]['product_parent'] == 'reducer tee mould'){
				$helpc	= 'reducer_tee_mould_edit';
			}
			if($getProduct[0]['product_parent'] == 'reducer tee slongsong'){
				$helpc	= 'reducer_tee_slongsong_edit';
			}
			if($getProduct[0]['product_parent'] == 'equal tee mould'){
				$helpc	= 'equal_tee_mould_edit';
			}
			if($getProduct[0]['product_parent'] == 'equal tee slongsong'){
				$helpc	= 'equal_tee_slongsong_edit';
			}
		}

		if($getIdProduct < 1){
			if($data['product_parent'] == 'pipe'){
				$helpc	= 'pipe_edit';
			}
			if($data['product_parent'] == 'end cap'){
				$helpc	= 'end_cap_edit';
			}
			if($data['product_parent'] == 'blind flange'){
				$helpc	= 'blind_flange_edit';
			}
			if($data['product_parent'] == 'concentric reducer'){
				$helpc	= 'concentric_reducer_edit';
			}
			if($data['product_parent'] == 'eccentric reducer'){
				$helpc	= 'eccentric_reducer_edit';
			}
			if($data['product_parent'] == 'elbow mould'){
				$helpc	= 'elbow_mould_edit';
			}
			if($data['product_parent'] == 'elbow mitter'){
				$helpc	= 'elbow_mitter_edit';
			}
			if($data['product_parent'] == 'flange mould'){
				$helpc	= 'flange_mould_edit';
			}
			if($data['product_parent'] == 'flange slongsong'){
				$helpc	= 'flange_slongsong_edit';
			}
			if($data['product_parent'] == 'colar'){
				$helpc	= 'colar_edit';
			}
			if($data['product_parent'] == 'colar slongsong'){
				$helpc	= 'colar_slongsong_edit';
			}
			if($data['product_parent'] == 'reducer tee mould'){
				$helpc	= 'reducer_tee_mould_edit';
			}
			if($data['product_parent'] == 'reducer tee slongsong'){
				$helpc	= 'reducer_tee_slongsong_edit';
			}
			if($data['product_parent'] == 'equal tee mould'){
				$helpc	= 'equal_tee_mould_edit';
			}
			if($data['product_parent'] == 'equal tee slongsong'){
				$helpc	= 'equal_tee_slongsong_edit';
			}
		}
		
		
		$this->db->trans_start();
			if($getIdProduct > 0){
				$this->db->where('id_product', $data['id_product']);
				$this->db->update('component_default', $insertData);
			}
			if($getIdProduct < 1){
				$this->db->insert('component_default', $insertData);
			}
			
			// $this->db->update('help_default', $insertData, array('product_parent' => $data['parent_product'], 'standart_code' => $data['standart_code'], 'diameter' => $data['diameter']));
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'			=> 'Failed Edit Default. Please try again later ...',
				'status'		=> 0,
				'helpx'			=> $helpc,
				'id_product' 	=> $data['id_product']
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=> 'Success Edit Default. Thanks ...',
				'status'		=> 1,
				'helpx'			=> $helpc,
				'id_product' 	=> $data['id_product']
				
			);
			history('Edit Default Data = '.$data['id_product']); 
		}
		

		echo json_encode($Arr_Kembali);
	}  
	
	public function getDefault(){
		$id_product		= $this->input->post("id_product");
		
		$qDefault	= "SELECT * FROM component_default WHERE id_product='".$id_product."' ";
		// echo $qDefault;
		$getDefault	= $this->db->query($qDefault)->result();
		$ArrJson	= array(
			'waste' 				=> floatval($getDefault[0]->waste),
			'overlap' 				=> floatval($getDefault[0]->overlap),
			'maxx' 					=> floatval($getDefault[0]->max),
			'minx' 					=> floatval($getDefault[0]->min),
			'plastic_film' 			=> floatval($getDefault[0]->plastic_film),
			'lin_resin_veil' 		=> floatval($getDefault[0]->lin_resin_veil),
			'lin_resin_veil_add' 	=> floatval($getDefault[0]->lin_resin_veil_add),
			'lin_resin_csm' 		=> floatval($getDefault[0]->lin_resin_csm),
			'lin_resin_csm_add' 	=> floatval($getDefault[0]->lin_resin_csm_add),
			'lin_faktor_veil' 		=> floatval($getDefault[0]->lin_faktor_veil),
			'lin_faktor_veil_add' 	=> floatval($getDefault[0]->lin_faktor_veil_add),
			'lin_faktor_csm' 		=> floatval($getDefault[0]->lin_faktor_csm),
			'lin_faktor_csm_add' 	=> floatval($getDefault[0]->lin_faktor_csm_add),
			'lin_resin' 			=> floatval($getDefault[0]->lin_resin),
			'str_resin_csm' 		=> floatval($getDefault[0]->str_resin_csm),
			'str_resin_csm_add' 	=> floatval($getDefault[0]->str_resin_csm_add),
			'str_resin_wr' 			=> floatval($getDefault[0]->str_resin_wr),
			'str_resin_wr_add' 		=> floatval($getDefault[0]->str_resin_wr_add),
			'str_resin_rv' 			=> floatval($getDefault[0]->str_resin_rv),
			'str_resin_rv_add' 		=> floatval($getDefault[0]->str_resin_rv_add),
			'str_faktor_csm' 		=> floatval($getDefault[0]->str_faktor_csm),
			'str_faktor_csm_add' 	=> floatval($getDefault[0]->str_faktor_csm_add),
			'str_faktor_wr' 		=> floatval($getDefault[0]->str_faktor_wr),
			'str_faktor_wr_add' 	=> floatval($getDefault[0]->str_faktor_wr_add),
			'str_faktor_rv' 		=> floatval($getDefault[0]->str_faktor_rv),
			'str_faktor_rv_bw' 		=> floatval($getDefault[0]->str_faktor_rv_bw),
			'str_faktor_rv_jb' 		=> floatval($getDefault[0]->str_faktor_rv_jb),
			'str_faktor_rv_add' 	=> floatval($getDefault[0]->str_faktor_rv_add),
			'str_faktor_rv_add_bw' 	=> floatval($getDefault[0]->str_faktor_rv_add_bw),
			'str_faktor_rv_add_jb' 	=> floatval($getDefault[0]->str_faktor_rv_add_jb),
			'str_resin' 			=> floatval($getDefault[0]->str_resin),
			'eks_resin_veil' 		=> floatval($getDefault[0]->eks_resin_veil),
			'eks_resin_veil_add' 	=> floatval($getDefault[0]->eks_resin_veil_add),
			'eks_resin_csm' 		=> floatval($getDefault[0]->eks_resin_csm),
			'eks_resin_csm_add' 	=> floatval($getDefault[0]->eks_resin_csm_add),
			'eks_faktor_veil' 		=> floatval($getDefault[0]->eks_faktor_veil),
			'eks_faktor_veil_add' 	=> floatval($getDefault[0]->eks_faktor_veil_add),
			'eks_faktor_csm' 		=> floatval($getDefault[0]->eks_faktor_csm),
			'eks_faktor_csm_add' 	=> floatval($getDefault[0]->eks_faktor_csm_add),
			'eks_resin' 			=> floatval($getDefault[0]->eks_resin),
			'topcoat_resin' 		=> floatval($getDefault[0]->topcoat_resin) 
		);
		
		// echo "<pre>";
		// print_r($ArrJson); exit;
		
		echo json_encode($ArrJson);
	}
	
	public function getDefaultOri(){
		$diameter			= $this->input->post("diameter");
		$standart			= $this->input->post("standart");
		$parent_product		= $this->input->post("parent_product");
		$id_product			= $this->input->post("id_product");
		
		$qIdProduct		= "SELECT * FROM component_default WHERE id_product='".$id_product."' ";
		$getIdProduct	= $this->db->query($qIdProduct)->num_rows();
		if($getIdProduct < 1){
			$qDefault	= "SELECT * FROM help_default WHERE product_parent='".$parent_product."' AND standart_code='".$standart."' AND diameter='".$diameter."' LIMIT 1";
		}
		if($getIdProduct > 0){
			$qDefault	= "SELECT * FROM component_default WHERE id_product='".$id_product."' LIMIT 1 ";
		}
		// echo $qDefault; exit;
		$getDefault	= $this->db->query($qDefault)->result();
		$ArrJson	= array(
			'waste' 				=> floatval($getDefault[0]->waste),
			'waste_n1' 				=> floatval($getDefault[0]->waste_n1),
			'waste_n2' 				=> floatval($getDefault[0]->waste_n2),
			'overlap' 				=> floatval($getDefault[0]->overlap),
			'maxx' 					=> floatval($getDefault[0]->max),
			'minx' 					=> floatval($getDefault[0]->min),
			'plastic_film' 			=> floatval($getDefault[0]->plastic_film),
			'lin_resin_veil' 		=> floatval($getDefault[0]->lin_resin_veil),
			'lin_resin_veil_add' 	=> floatval($getDefault[0]->lin_resin_veil_add),
			'lin_resin_csm' 		=> floatval($getDefault[0]->lin_resin_csm),
			'lin_resin_csm_add' 	=> floatval($getDefault[0]->lin_resin_csm_add),
			'lin_faktor_veil' 		=> floatval($getDefault[0]->lin_faktor_veil),
			'lin_faktor_veil_add' 	=> floatval($getDefault[0]->lin_faktor_veil_add),
			'lin_faktor_csm' 		=> floatval($getDefault[0]->lin_faktor_csm),
			'lin_faktor_csm_add' 	=> floatval($getDefault[0]->lin_faktor_csm_add),
			'lin_resin' 			=> floatval($getDefault[0]->lin_resin),
			'lin_resin_thickness' 	=> floatval($getDefault[0]->lin_resin_thickness),
			'str_resin_csm' 		=> floatval($getDefault[0]->str_resin_csm),
			'str_resin_csm_add' 	=> floatval($getDefault[0]->str_resin_csm_add),
			'str_resin_wr' 			=> floatval($getDefault[0]->str_resin_wr),
			'str_resin_wr_add' 		=> floatval($getDefault[0]->str_resin_wr_add),
			'str_resin_rv' 			=> floatval($getDefault[0]->str_resin_rv),
			'str_resin_rv_add' 		=> floatval($getDefault[0]->str_resin_rv_add),
			'str_faktor_csm' 		=> floatval($getDefault[0]->str_faktor_csm),
			'str_faktor_csm_add' 	=> floatval($getDefault[0]->str_faktor_csm_add),
			'str_faktor_wr' 		=> floatval($getDefault[0]->str_faktor_wr),
			'str_faktor_wr_add' 	=> floatval($getDefault[0]->str_faktor_wr_add),
			'str_faktor_rv' 		=> floatval($getDefault[0]->str_faktor_rv),
			'str_faktor_rv_bw' 		=> floatval($getDefault[0]->str_faktor_rv_bw),
			'str_faktor_rv_jb' 		=> floatval($getDefault[0]->str_faktor_rv_jb),
			'str_faktor_rv_add' 	=> floatval($getDefault[0]->str_faktor_rv_add),
			'str_faktor_rv_add_bw' 	=> floatval($getDefault[0]->str_faktor_rv_add_bw),
			'str_faktor_rv_add_jb' 	=> floatval($getDefault[0]->str_faktor_rv_add_jb),
			'str_resin' 			=> floatval($getDefault[0]->str_resin),
			'str_resin_thickness' 	=> floatval($getDefault[0]->str_resin_thickness),
			'eks_resin_veil' 		=> floatval($getDefault[0]->eks_resin_veil),
			'eks_resin_veil_add' 	=> floatval($getDefault[0]->eks_resin_veil_add),
			'eks_resin_csm' 		=> floatval($getDefault[0]->eks_resin_csm),
			'eks_resin_csm_add' 	=> floatval($getDefault[0]->eks_resin_csm_add),
			'eks_faktor_veil' 		=> floatval($getDefault[0]->eks_faktor_veil),
			'eks_faktor_veil_add' 	=> floatval($getDefault[0]->eks_faktor_veil_add),
			'eks_faktor_csm' 		=> floatval($getDefault[0]->eks_faktor_csm),
			'eks_faktor_csm_add' 	=> floatval($getDefault[0]->eks_faktor_csm_add),
			'eks_resin' 			=> floatval($getDefault[0]->eks_resin),
			'eks_resin_thickness' 	=> floatval($getDefault[0]->eks_resin_thickness),
			'str_n1_resin_csm' 			=> floatval($getDefault[0]->str_n1_resin_csm),
			'str_n1_resin_csm_add' 		=> floatval($getDefault[0]->str_n1_resin_csm_add),
			'str_n1_resin_wr' 			=> floatval($getDefault[0]->str_n1_resin_wr),
			'str_n1_resin_wr_add' 		=> floatval($getDefault[0]->str_n1_resin_wr_add),
			'str_n1_resin_rv' 			=> floatval($getDefault[0]->str_n1_resin_rv),
			'str_n1_resin_rv_add' 		=> floatval($getDefault[0]->str_n1_resin_rv_add),
			'str_n1_faktor_csm' 		=> floatval($getDefault[0]->str_n1_faktor_csm),
			'str_n1_faktor_csm_add' 	=> floatval($getDefault[0]->str_n1_faktor_csm_add),
			'str_n1_faktor_wr' 			=> floatval($getDefault[0]->str_n1_faktor_wr),
			'str_n1_faktor_wr_add' 		=> floatval($getDefault[0]->str_n1_faktor_wr_add),
			'str_n1_faktor_rv' 			=> floatval($getDefault[0]->str_n1_faktor_rv),
			'str_n1_faktor_rv_bw' 		=> floatval($getDefault[0]->str_n1_faktor_rv_bw),
			'str_n1_faktor_rv_jb' 		=> floatval($getDefault[0]->str_n1_faktor_rv_jb),
			'str_n1_faktor_rv_add' 		=> floatval($getDefault[0]->str_n1_faktor_rv_add),
			'str_n1_faktor_rv_add_bw' 	=> floatval($getDefault[0]->str_n1_faktor_rv_add_bw),
			'str_n1_faktor_rv_add_jb' 	=> floatval($getDefault[0]->str_n1_faktor_rv_add_jb),
			'str_n1_resin' 				=> floatval($getDefault[0]->str_n1_resin),
			'str_n1_resin_thickness' 	=> floatval($getDefault[0]->str_n1_resin_thickness),
			'str_n2_resin_csm' 			=> floatval($getDefault[0]->str_n2_resin_csm),
			'str_n2_resin_csm_add' 		=> floatval($getDefault[0]->str_n2_resin_csm_add),
			'str_n2_resin_wr' 			=> floatval($getDefault[0]->str_n2_resin_wr),
			'str_n2_resin_wr_add' 		=> floatval($getDefault[0]->str_n2_resin_wr_add),
			'str_n2_faktor_csm' 		=> floatval($getDefault[0]->str_n2_faktor_csm),
			'str_n2_faktor_csm_add' 	=> floatval($getDefault[0]->str_n2_faktor_csm_add),
			'str_n2_faktor_wr' 			=> floatval($getDefault[0]->str_n2_faktor_wr),
			'str_n2_faktor_wr_add' 		=> floatval($getDefault[0]->str_n2_faktor_wr_add),
			'str_n2_resin' 				=> floatval($getDefault[0]->str_n2_resin),
			'str_n2_resin_thickness' 	=> floatval($getDefault[0]->str_n2_resin_thickness),
			'topcoat_resin' 			=> floatval($getDefault[0]->topcoat_resin) 
		);
		
		// echo "<pre>";
		// print_r($ArrJson); exit;
		
		echo json_encode($ArrJson);
	}
	
	public function getDefaultEditBq(){
		$diameter			= $this->input->post("diameter");
		$standart			= $this->input->post("standart");
		$parent_product		= $this->input->post("parent_product");
		$id_milik			= $this->input->post("id_milik");
		
		$qDefault	= "SELECT * FROM bq_component_default WHERE id_milik='".$id_milik."' ";
		$getNum		= $this->db->query($qDefault)->num_rows();
		
		// echo $getNum; exit;
		if($getNum > 0){
			$getDefault	= $this->db->query($qDefault)->result(); 
			$ArrJson	= array(
				'tamp' 					=> "Default berhasil ditemukan",
				'color'					=> "green",
				'hasilx' 				=> $getNum,
				'standart' 				=> $standart,
				'waste' 				=> floatval($getDefault[0]->waste),
				'overlap' 				=> floatval($getDefault[0]->overlap),
				'maxx' 					=> floatval($getDefault[0]->max),
				'minx' 					=> floatval($getDefault[0]->min),
				'plastic_film' 			=> floatval($getDefault[0]->plastic_film),
				'lin_resin_veil' 		=> floatval($getDefault[0]->lin_resin_veil),
				'lin_resin_veil_add' 	=> floatval($getDefault[0]->lin_resin_veil_add),
				'lin_resin_csm' 		=> floatval($getDefault[0]->lin_resin_csm),
				'lin_resin_csm_add' 	=> floatval($getDefault[0]->lin_resin_csm_add),
				'lin_faktor_veil' 		=> floatval($getDefault[0]->lin_faktor_veil),
				'lin_faktor_veil_add' 	=> floatval($getDefault[0]->lin_faktor_veil_add),
				'lin_faktor_csm' 		=> floatval($getDefault[0]->lin_faktor_csm),
				'lin_faktor_csm_add' 	=> floatval($getDefault[0]->lin_faktor_csm_add),
				'lin_resin' 			=> floatval($getDefault[0]->lin_resin),
				'lin_resin_thickness' 	=> floatval($getDefault[0]->lin_resin_thickness),
				'str_resin_csm' 		=> floatval($getDefault[0]->str_resin_csm),
				'str_resin_csm_add' 	=> floatval($getDefault[0]->str_resin_csm_add),
				'str_resin_wr' 			=> floatval($getDefault[0]->str_resin_wr),
				'str_resin_wr_add' 		=> floatval($getDefault[0]->str_resin_wr_add),
				'str_resin_rv' 			=> floatval($getDefault[0]->str_resin_rv),
				'str_resin_rv_add' 		=> floatval($getDefault[0]->str_resin_rv_add),
				'str_faktor_csm' 		=> floatval($getDefault[0]->str_faktor_csm),
				'str_faktor_csm_add' 	=> floatval($getDefault[0]->str_faktor_csm_add),
				'str_faktor_wr' 		=> floatval($getDefault[0]->str_faktor_wr),
				'str_faktor_wr_add' 	=> floatval($getDefault[0]->str_faktor_wr_add),
				'str_faktor_rv' 		=> floatval($getDefault[0]->str_faktor_rv),
				'str_faktor_rv_bw' 		=> floatval($getDefault[0]->str_faktor_rv_bw),
				'str_faktor_rv_jb' 		=> floatval($getDefault[0]->str_faktor_rv_jb),
				'str_faktor_rv_add' 	=> floatval($getDefault[0]->str_faktor_rv_add),
				'str_faktor_rv_add_bw' 	=> floatval($getDefault[0]->str_faktor_rv_add_bw),
				'str_faktor_rv_add_jb' 	=> floatval($getDefault[0]->str_faktor_rv_add_jb),
				'str_resin' 			=> floatval($getDefault[0]->str_resin),
				'str_resin_thickness' 	=> floatval($getDefault[0]->str_resin_thickness),
				'eks_resin_veil' 		=> floatval($getDefault[0]->eks_resin_veil),
				'eks_resin_veil_add' 	=> floatval($getDefault[0]->eks_resin_veil_add),
				'eks_resin_csm' 		=> floatval($getDefault[0]->eks_resin_csm),
				'eks_resin_csm_add' 	=> floatval($getDefault[0]->eks_resin_csm_add),
				'eks_faktor_veil' 		=> floatval($getDefault[0]->eks_faktor_veil),
				'eks_faktor_veil_add' 	=> floatval($getDefault[0]->eks_faktor_veil_add),
				'eks_faktor_csm' 		=> floatval($getDefault[0]->eks_faktor_csm),
				'eks_faktor_csm_add' 	=> floatval($getDefault[0]->eks_faktor_csm_add),
				'eks_resin' 			=> floatval($getDefault[0]->eks_resin),
				'eks_resin_thickness' 	=> floatval($getDefault[0]->eks_resin_thickness),
				'str_n1_resin_csm' 			=> floatval($getDefault[0]->str_n1_resin_csm),
				'str_n1_resin_csm_add' 		=> floatval($getDefault[0]->str_n1_resin_csm_add),
				'str_n1_resin_wr' 			=> floatval($getDefault[0]->str_n1_resin_wr),
				'str_n1_resin_wr_add' 		=> floatval($getDefault[0]->str_n1_resin_wr_add),
				'str_n1_resin_rv' 			=> floatval($getDefault[0]->str_n1_resin_rv),
				'str_n1_resin_rv_add' 		=> floatval($getDefault[0]->str_n1_resin_rv_add),
				'str_n1_faktor_csm' 		=> floatval($getDefault[0]->str_n1_faktor_csm),
				'str_n1_faktor_csm_add' 	=> floatval($getDefault[0]->str_n1_faktor_csm_add),
				'str_n1_faktor_wr' 			=> floatval($getDefault[0]->str_n1_faktor_wr),
				'str_n1_faktor_wr_add' 		=> floatval($getDefault[0]->str_n1_faktor_wr_add),
				'str_n1_faktor_rv' 			=> floatval($getDefault[0]->str_n1_faktor_rv),
				'str_n1_faktor_rv_bw' 		=> floatval($getDefault[0]->str_n1_faktor_rv_bw),
				'str_n1_faktor_rv_jb' 		=> floatval($getDefault[0]->str_n1_faktor_rv_jb),
				'str_n1_faktor_rv_add' 		=> floatval($getDefault[0]->str_n1_faktor_rv_add),
				'str_n1_faktor_rv_add_bw' 	=> floatval($getDefault[0]->str_n1_faktor_rv_add_bw),
				'str_n1_faktor_rv_add_jb' 	=> floatval($getDefault[0]->str_n1_faktor_rv_add_jb),
				'str_n1_resin' 				=> floatval($getDefault[0]->str_n1_resin),
				'str_n1_resin_thickness' 	=> floatval($getDefault[0]->str_n1_resin_thickness),
				'str_n2_resin_csm' 			=> floatval($getDefault[0]->str_n2_resin_csm),
				'str_n2_resin_csm_add' 		=> floatval($getDefault[0]->str_n2_resin_csm_add),
				'str_n2_resin_wr' 			=> floatval($getDefault[0]->str_n2_resin_wr),
				'str_n2_resin_wr_add' 		=> floatval($getDefault[0]->str_n2_resin_wr_add),
				'str_n2_faktor_csm' 		=> floatval($getDefault[0]->str_n2_faktor_csm),
				'str_n2_faktor_csm_add' 	=> floatval($getDefault[0]->str_n2_faktor_csm_add),
				'str_n2_faktor_wr' 			=> floatval($getDefault[0]->str_n2_faktor_wr),
				'str_n2_faktor_wr_add' 		=> floatval($getDefault[0]->str_n2_faktor_wr_add),
				'str_n2_resin' 				=> floatval($getDefault[0]->str_n2_resin),
				'str_n2_resin_thickness' 	=> floatval($getDefault[0]->str_n2_resin_thickness),
				'topcoat_resin' 			=> floatval($getDefault[0]->topcoat_resin) 
			);
			
			
		}
		else{
			$ArrJson	= array(
				'tamp' 					=> "Default tidak ditemukan",
				'color'					=> "red",
				'hasilx' 				=> $getNum,
				'pipeD' 				=> $diameter,
				'product' 				=> $parent_product
			);
		}
		
		// echo "<pre>";
		// print_r($ArrJson); exit;
		
		
		echo json_encode($ArrJson);
		
		
		
	}
	
	public function editDefaultEstProject(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		// echo $data['help_url'];
		
		$insertData	= array(
			'waste' 				=> $data['waste'],
			'overlap' 				=> $data['overlap'],
			'max' 					=> (!empty($data['max']))?$data['max']/100:'0',
			'min' 					=> (!empty($data['min']))?$data['min']/100:'0',
			'plastic_film' 			=> (!empty($data['plastic_film']))?$data['plastic_film']:'1',
			'lin_resin_veil_a' 		=> $data['lin_resin_veil_a'],
			'lin_resin_veil_b' 		=> $data['lin_resin_veil_b'],
			'lin_resin_veil' 		=> (!empty($data['lin_resin_veil']))?$data['lin_resin_veil']:'1',
			'lin_resin_veil_add_a' 	=> $data['lin_resin_veil_add_a'],
			'lin_resin_veil_add_b' 	=> $data['lin_resin_veil_add_b'],
			'lin_resin_veil_add' 	=> (!empty($data['lin_resin_veil_add']))?$data['lin_resin_veil_add']:'1',
			'lin_resin_csm_a' 		=> $data['lin_resin_csm_a'],
			'lin_resin_csm_b' 		=> $data['lin_resin_csm_b'],
			'lin_resin_csm' 		=> (!empty($data['lin_resin_csm']))?$data['lin_resin_csm']:'1',
			'lin_resin_csm_add_a' 	=> $data['lin_resin_csm_add_a'],
			'lin_resin_csm_add_b' 	=> $data['lin_resin_csm_add_b'],
			'lin_resin_csm_add' 	=> (!empty($data['lin_resin_csm_add']))?$data['lin_resin_csm_add']:'1',
			'lin_faktor_veil' 		=> (!empty($data['lin_faktor_veil']))?$data['lin_faktor_veil']:'1',
			'lin_faktor_veil_add' 	=> (!empty($data['lin_faktor_veil_add']))?$data['lin_faktor_veil_add']:'1',
			'lin_faktor_csm' 		=> (!empty($data['lin_faktor_csm']))?$data['lin_faktor_csm']:'1',
			'lin_faktor_csm_add' 	=> (!empty($data['lin_faktor_csm_add']))?$data['lin_faktor_csm_add']:'1',
			'lin_resin' 			=> (!empty($data['lin_resin']))?$data['lin_resin']:'1',
			'str_resin_csm_a' 		=> $data['str_resin_csm_a'],
			'str_resin_csm_b' 		=> $data['str_resin_csm_b'],
			'str_resin_csm' 		=> (!empty($data['str_resin_csm']))?$data['str_resin_csm']:'1',
			'str_resin_csm_add_a' 	=> $data['str_resin_csm_add_a'],
			'str_resin_csm_add_b' 	=> $data['str_resin_csm_add_b'],
			'str_resin_csm_add' 	=> (!empty($data['str_resin_csm_add']))?$data['str_resin_csm_add']:'1',
			'str_resin_wr_a' 		=> $data['str_resin_wr_a'],
			'str_resin_wr_b' 		=> $data['str_resin_wr_b'],
			'str_resin_wr' 			=> (!empty($data['str_resin_wr']))?$data['str_resin_wr']:'1',
			'str_resin_wr_add_a' 	=> $data['str_resin_wr_add_a'],
			'str_resin_wr_add_b' 	=> $data['str_resin_wr_add_b'],
			'str_resin_wr_add' 		=> (!empty($data['str_resin_wr_add']))?$data['str_resin_wr_add']:'1',
			'str_resin_rv_a' 		=> $data['str_resin_rv_a'],
			'str_resin_rv_b' 		=> $data['str_resin_rv_b'],
			'str_resin_rv' 			=> (!empty($data['str_resin_rv']))?$data['str_resin_rv']:'1',
			'str_resin_rv_add_a' 	=> $data['str_resin_rv_add_a'],
			'str_resin_rv_add_b' 	=> $data['str_resin_rv_add_b'],
			'str_resin_rv_add' 		=> (!empty($data['str_resin_rv_add']))?$data['str_resin_rv_add']:'1',
			'str_faktor_csm' 		=> (!empty($data['str_faktor_csm']))?$data['str_faktor_csm']:'1',
			'str_faktor_csm_add' 	=> (!empty($data['str_faktor_csm_add']))?$data['str_faktor_csm_add']:'1',
			'str_faktor_wr' 		=> (!empty($data['str_faktor_wr']))?$data['str_faktor_wr']:'1',
			'str_faktor_wr_add' 	=> (!empty($data['str_faktor_wr_add']))?$data['str_faktor_wr_add']:'1',
			'str_faktor_rv' 		=> (!empty($data['str_faktor_rv']))?$data['str_faktor_rv']:'1',
			'str_faktor_rv_bw' 		=> (!empty($data['str_faktor_rv_bw']))?$data['str_faktor_rv_bw']:'1',
			'str_faktor_rv_jb' 		=> (!empty($data['str_faktor_rv_jb']))?$data['str_faktor_rv_jb']:'1',
			'str_faktor_rv_add' 	=> (!empty($data['str_faktor_rv_add']))?$data['str_faktor_rv_add']:'1',
			'str_faktor_rv_add_bw' 	=> (!empty($data['str_faktor_rv_add_bw']))?$data['str_faktor_rv_add_bw']:'1',
			'str_faktor_rv_add_jb' 	=> (!empty($data['str_faktor_rv_add_jb']))?$data['str_faktor_rv_add_jb']:'1',
			'str_resin' 			=> (!empty($data['str_resin']))?$data['str_resin']:'1',
			'str_resin_thickness' 	=> (!empty($data['str_resin_thickness']))?$data['str_resin_thickness']:'1',
			'eks_resin_veil_a' 		=> $data['eks_resin_veil_a'],
			'eks_resin_veil_b' 		=> $data['eks_resin_veil_b'],
			'eks_resin_veil' 		=> (!empty($data['eks_resin_veil']))?$data['eks_resin_veil']:'1',
			'eks_resin_veil_add_a' 	=> $data['eks_resin_veil_add_a'],
			'eks_resin_veil_add_b' 	=> $data['eks_resin_veil_add_b'],
			'eks_resin_veil_add' 	=> (!empty($data['eks_resin_veil_add']))?$data['eks_resin_veil_add']:'1',
			'eks_resin_csm_a' 		=> $data['eks_resin_csm_a'],
			'eks_resin_csm_b' 		=> $data['eks_resin_csm_b'],
			'eks_resin_csm' 		=> (!empty($data['eks_resin_csm']))?$data['eks_resin_csm']:'1',
			'eks_resin_csm_add_a' 	=> $data['eks_resin_csm_add_a'],
			'eks_resin_csm_add_b' 	=> $data['eks_resin_csm_add_b'],
			'eks_resin_csm_add' 	=> (!empty($data['eks_resin_csm_add']))?$data['eks_resin_csm_add']:'1',
			'eks_faktor_veil' 		=> (!empty($data['eks_faktor_veil']))?$data['eks_faktor_veil']:'1',
			'eks_faktor_veil_add' 	=> (!empty($data['eks_faktor_veil_add']))?$data['eks_faktor_veil_add']:'1',
			'eks_faktor_csm' 		=> (!empty($data['eks_faktor_csm']))?$data['eks_faktor_csm']:'1',
			'eks_faktor_csm_add' 	=> (!empty($data['eks_faktor_csm_add']))?$data['eks_faktor_csm_add']:'1',
			'eks_resin' 			=> (!empty($data['eks_resin']))?$data['eks_resin']:'1',
			'topcoat_resin' 		=> (!empty($data['topcoat_resin']))?$data['topcoat_resin']:'1',
			
			'str_n1_resin_csm_a' 		=> (!empty($data['str_n1_resin_csm_a']))?$data['str_n1_resin_csm_a']:'1',
			'str_n1_resin_csm_b' 		=> (!empty($data['str_n1_resin_csm_b']))?$data['str_n1_resin_csm_b']:'1',
			'str_n1_resin_csm' 			=> (!empty($data['str_n1_resin_csm']))?$data['str_n1_resin_csm']:'1',
			'str_n1_resin_csm_add_a' 	=> (!empty($data['str_n1_resin_csm_add_a']))?$data['str_n1_resin_csm_add_a']:'1',
			'str_n1_resin_csm_add_b' 	=> (!empty($data['str_n1_resin_csm_add_b']))?$data['str_n1_resin_csm_add_b']:'1',
			'str_n1_resin_csm_add' 		=> (!empty($data['str_n1_resin_csm_add']))?$data['str_n1_resin_csm_add']:'1',
			'str_n1_resin_wr_a' 		=> (!empty($data['str_n1_resin_wr_a']))?$data['str_n1_resin_wr_a']:'1',
			'str_n1_resin_wr_b' 		=> (!empty($data['str_n1_resin_wr_b']))?$data['str_n1_resin_wr_b']:'1',
			'str_n1_resin_wr' 			=> (!empty($data['str_n1_resin_wr']))?$data['str_n1_resin_wr']:'1',
			'str_n1_resin_wr_add_a' 	=> (!empty($data['str_n1_resin_wr_add_a']))?$data['str_n1_resin_wr_add_a']:'1',
			'str_n1_resin_wr_add_b' 	=> (!empty($data['str_n1_resin_wr_add_b']))?$data['str_n1_resin_wr_add_b']:'1',
			'str_n1_resin_wr_add' 		=> (!empty($data['str_n1_resin_wr_add']))?$data['str_n1_resin_wr_add']:'1',
			'str_n1_resin_rv_a' 		=> (!empty($data['str_n1_resin_rv_a']))?$data['str_n1_resin_rv_a']:'1',
			'str_n1_resin_rv_b' 		=> (!empty($data['str_n1_resin_rv_b']))?$data['str_n1_resin_rv_b']:'1',
			'str_n1_resin_rv' 			=> (!empty($data['str_n1_resin_rv']))?$data['str_n1_resin_rv']:'1',
			'str_n1_resin_rv_add_a' 	=> (!empty($data['str_n1_resin_rv_add_a']))?$data['str_n1_resin_rv_add_a']:'1',
			'str_n1_resin_rv_add_b' 	=> (!empty($data['str_n1_resin_rv_add_b']))?$data['str_n1_resin_rv_add_b']:'1',
			'str_n1_resin_rv_add' 		=> (!empty($data['str_n1_resin_rv_add']))?$data['str_n1_resin_rv_add']:'1',
			'str_n1_faktor_csm' 		=> (!empty($data['str_n1_faktor_csm']))?$data['str_n1_faktor_csm']:'1',
			'str_n1_faktor_csm_add' 	=> (!empty($data['str_n1_faktor_csm_add']))?$data['str_n1_faktor_csm_add']:'1',
			'str_n1_faktor_wr' 			=> (!empty($data['str_n1_faktor_wr']))?$data['str_n1_faktor_wr']:'1',
			'str_n1_faktor_wr_add' 		=> (!empty($data['str_n1_faktor_wr_add']))?$data['str_n1_faktor_wr_add']:'1',
			'str_n1_faktor_rv' 			=> (!empty($data['str_n1_faktor_rv']))?$data['str_n1_faktor_rv']:'1',
			'str_n1_faktor_rv_bw' 		=> (!empty($data['str_n1_faktor_rv_bw']))?$data['str_n1_faktor_rv_bw']:'1',
			'str_n1_faktor_rv_jb' 		=> (!empty($data['str_n1_faktor_rv_jb']))?$data['str_n1_faktor_rv_jb']:'1',
			'str_n1_faktor_rv_add' 		=> (!empty($data['str_n1_faktor_rv_add']))?$data['str_n1_faktor_rv_add']:'1',
			'str_n1_faktor_rv_add_bw' 	=> (!empty($data['str_n1_faktor_rv_add_bw']))?$data['str_n1_faktor_rv_add_bw']:'1',
			'str_n1_faktor_rv_add_jb' 	=> (!empty($data['str_n1_faktor_rv_add_jb']))?$data['str_n1_faktor_rv_add_jb']:'1',
			'str_n1_resin' 				=> (!empty($data['str_n1_resin']))?$data['str_n1_resin']:'1',
			'str_n1_resin_thickness' 	=> (!empty($data['str_n1_resin_thickness']))?$data['str_n1_resin_thickness']:'1',
			
			'str_n2_resin_csm_a' 		=> (!empty($data['str_n2_resin_csm_a']))?$data['str_n2_resin_csm_a']:'1',
			'str_n2_resin_csm_b' 		=> (!empty($data['str_n2_resin_csm_b']))?$data['str_n2_resin_csm_b']:'1',
			'str_n2_resin_csm' 			=> (!empty($data['str_n2_resin_csm']))?$data['str_n2_resin_csm']:'1',
			'str_n2_resin_csm_add_a' 	=> (!empty($data['str_n2_resin_csm_add_a']))?$data['str_n2_resin_csm_add_a']:'1',
			'str_n2_resin_csm_add_b' 	=> (!empty($data['str_n2_resin_csm_add_b']))?$data['str_n2_resin_csm_add_b']:'1',
			'str_n2_resin_csm_add' 		=> (!empty($data['str_n2_resin_csm_add']))?$data['str_n2_resin_csm_add']:'1',
			'str_n2_resin_wr_a' 		=> (!empty($data['str_n2_resin_wr_a']))?$data['str_n2_resin_wr_a']:'1',
			'str_n2_resin_wr_b' 		=> (!empty($data['str_n2_resin_wr_b']))?$data['str_n2_resin_wr_b']:'1',
			'str_n2_resin_wr' 			=> (!empty($data['str_n2_resin_wr']))?$data['str_n2_resin_wr']:'1',
			'str_n2_resin_wr_add_a' 	=> (!empty($data['str_n2_resin_wr_add_a']))?$data['str_n2_resin_wr_add_a']:'1',
			'str_n2_resin_wr_add_b' 	=> (!empty($data['str_n2_resin_wr_add_b']))?$data['str_n2_resin_wr_add_b']:'1',
			'str_n2_resin_wr_add' 		=> (!empty($data['str_n2_resin_wr_add']))?$data['str_n2_resin_wr_add']:'1',
			'str_n2_faktor_csm' 		=> (!empty($data['str_n2_faktor_csm']))?$data['str_n2_faktor_csm']:'1',
			'str_n2_faktor_csm_add' 	=> (!empty($data['str_n2_faktor_csm_add']))?$data['str_n2_faktor_csm_add']:'1',
			'str_n2_faktor_wr' 			=> (!empty($data['str_n2_faktor_wr']))?$data['str_n2_faktor_wr']:'1',
			'str_n2_faktor_wr_add' 		=> (!empty($data['str_n2_faktor_wr_add']))?$data['str_n2_faktor_wr_add']:'1',
			'str_n2_resin' 				=> (!empty($data['str_n2_resin']))?$data['str_n2_resin']:'1',
			'str_n2_resin_thickness' 	=> (!empty($data['str_n2_resin_thickness']))?$data['str_n2_resin_thickness']:'1',
			
			'modified_by'			=> $data_session['ORI_User']['username'],
			'modified_date'			=> date('Y-m-d H:i:s')
		);
		
		// print_r($insertData);
		// exit; 
		
		
		
		$this->db->trans_start();
			$this->db->update('bq_component_default', $insertData, array('id' => $data['id']));
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'			=> 'Failed Edit Default. Please try again later ...',
				'status'		=> 0,
				'pembeda'		=> '',
				'help_url'		=> $data['help_url'],
				'id_milik' 		=> $data['id_milik'],
				'id_bq' 		=> $data['id_bq']
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=> 'Success Edit Default. Thanks ...',
				'status'		=> 1,
				'pembeda'		=> '',
				'help_url'		=> $data['help_url'],
				'id_milik' 		=> $data['id_milik'],
				'id_bq' 		=> $data['id_bq']
				
			);
			history("Edit Default Data Estimasi Project ".$data['id_bq']." id milik = ".$data['id_milik']); 
		}
		

		echo json_encode($Arr_Kembali);
	}  
	
	public function editDefaultEstProjectFD(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		// echo $data['help_url'];
		
		$insertData	= array(
			'waste' 				=> $data['waste'],
			'overlap' 				=> $data['overlap'],
			'max' 					=> (!empty($data['max']))?$data['max']/100:'0',
			'min' 					=> (!empty($data['min']))?$data['min']/100:'0',
			'plastic_film' 			=> (!empty($data['plastic_film']))?$data['plastic_film']:'1',
			'lin_resin_veil_a' 		=> $data['lin_resin_veil_a'],
			'lin_resin_veil_b' 		=> $data['lin_resin_veil_b'],
			'lin_resin_veil' 		=> (!empty($data['lin_resin_veil']))?$data['lin_resin_veil']:'1',
			'lin_resin_veil_add_a' 	=> $data['lin_resin_veil_add_a'],
			'lin_resin_veil_add_b' 	=> $data['lin_resin_veil_add_b'],
			'lin_resin_veil_add' 	=> (!empty($data['lin_resin_veil_add']))?$data['lin_resin_veil_add']:'1',
			'lin_resin_csm_a' 		=> $data['lin_resin_csm_a'],
			'lin_resin_csm_b' 		=> $data['lin_resin_csm_b'],
			'lin_resin_csm' 		=> (!empty($data['lin_resin_csm']))?$data['lin_resin_csm']:'1',
			'lin_resin_csm_add_a' 	=> $data['lin_resin_csm_add_a'],
			'lin_resin_csm_add_b' 	=> $data['lin_resin_csm_add_b'],
			'lin_resin_csm_add' 	=> (!empty($data['lin_resin_csm_add']))?$data['lin_resin_csm_add']:'1',
			'lin_faktor_veil' 		=> (!empty($data['lin_faktor_veil']))?$data['lin_faktor_veil']:'1',
			'lin_faktor_veil_add' 	=> (!empty($data['lin_faktor_veil_add']))?$data['lin_faktor_veil_add']:'1',
			'lin_faktor_csm' 		=> (!empty($data['lin_faktor_csm']))?$data['lin_faktor_csm']:'1',
			'lin_faktor_csm_add' 	=> (!empty($data['lin_faktor_csm_add']))?$data['lin_faktor_csm_add']:'1',
			'lin_resin' 			=> (!empty($data['lin_resin']))?$data['lin_resin']:'1',
			'str_resin_csm_a' 		=> $data['str_resin_csm_a'],
			'str_resin_csm_b' 		=> $data['str_resin_csm_b'],
			'str_resin_csm' 		=> (!empty($data['str_resin_csm']))?$data['str_resin_csm']:'1',
			'str_resin_csm_add_a' 	=> $data['str_resin_csm_add_a'],
			'str_resin_csm_add_b' 	=> $data['str_resin_csm_add_b'],
			'str_resin_csm_add' 	=> (!empty($data['str_resin_csm_add']))?$data['str_resin_csm_add']:'1',
			'str_resin_wr_a' 		=> $data['str_resin_wr_a'],
			'str_resin_wr_b' 		=> $data['str_resin_wr_b'],
			'str_resin_wr' 			=> (!empty($data['str_resin_wr']))?$data['str_resin_wr']:'1',
			'str_resin_wr_add_a' 	=> $data['str_resin_wr_add_a'],
			'str_resin_wr_add_b' 	=> $data['str_resin_wr_add_b'],
			'str_resin_wr_add' 		=> (!empty($data['str_resin_wr_add']))?$data['str_resin_wr_add']:'1',
			'str_resin_rv_a' 		=> $data['str_resin_rv_a'],
			'str_resin_rv_b' 		=> $data['str_resin_rv_b'],
			'str_resin_rv' 			=> (!empty($data['str_resin_rv']))?$data['str_resin_rv']:'1',
			'str_resin_rv_add_a' 	=> $data['str_resin_rv_add_a'],
			'str_resin_rv_add_b' 	=> $data['str_resin_rv_add_b'],
			'str_resin_rv_add' 		=> (!empty($data['str_resin_rv_add']))?$data['str_resin_rv_add']:'1',
			'str_faktor_csm' 		=> (!empty($data['str_faktor_csm']))?$data['str_faktor_csm']:'1',
			'str_faktor_csm_add' 	=> (!empty($data['str_faktor_csm_add']))?$data['str_faktor_csm_add']:'1',
			'str_faktor_wr' 		=> (!empty($data['str_faktor_wr']))?$data['str_faktor_wr']:'1',
			'str_faktor_wr_add' 	=> (!empty($data['str_faktor_wr_add']))?$data['str_faktor_wr_add']:'1',
			'str_faktor_rv' 		=> (!empty($data['str_faktor_rv']))?$data['str_faktor_rv']:'1',
			'str_faktor_rv_bw' 		=> (!empty($data['str_faktor_rv_bw']))?$data['str_faktor_rv_bw']:'1',
			'str_faktor_rv_jb' 		=> (!empty($data['str_faktor_rv_jb']))?$data['str_faktor_rv_jb']:'1',
			'str_faktor_rv_add' 	=> (!empty($data['str_faktor_rv_add']))?$data['str_faktor_rv_add']:'1',
			'str_faktor_rv_add_bw' 	=> (!empty($data['str_faktor_rv_add_bw']))?$data['str_faktor_rv_add_bw']:'1',
			'str_faktor_rv_add_jb' 	=> (!empty($data['str_faktor_rv_add_jb']))?$data['str_faktor_rv_add_jb']:'1',
			'str_resin' 			=> (!empty($data['str_resin']))?$data['str_resin']:'1',
			'str_resin_thickness' 	=> (!empty($data['str_resin_thickness']))?$data['str_resin_thickness']:'1',
			'eks_resin_veil_a' 		=> $data['eks_resin_veil_a'],
			'eks_resin_veil_b' 		=> $data['eks_resin_veil_b'],
			'eks_resin_veil' 		=> (!empty($data['eks_resin_veil']))?$data['eks_resin_veil']:'1',
			'eks_resin_veil_add_a' 	=> $data['eks_resin_veil_add_a'],
			'eks_resin_veil_add_b' 	=> $data['eks_resin_veil_add_b'],
			'eks_resin_veil_add' 	=> (!empty($data['eks_resin_veil_add']))?$data['eks_resin_veil_add']:'1',
			'eks_resin_csm_a' 		=> $data['eks_resin_csm_a'],
			'eks_resin_csm_b' 		=> $data['eks_resin_csm_b'],
			'eks_resin_csm' 		=> (!empty($data['eks_resin_csm']))?$data['eks_resin_csm']:'1',
			'eks_resin_csm_add_a' 	=> $data['eks_resin_csm_add_a'],
			'eks_resin_csm_add_b' 	=> $data['eks_resin_csm_add_b'],
			'eks_resin_csm_add' 	=> (!empty($data['eks_resin_csm_add']))?$data['eks_resin_csm_add']:'1',
			'eks_faktor_veil' 		=> (!empty($data['eks_faktor_veil']))?$data['eks_faktor_veil']:'1',
			'eks_faktor_veil_add' 	=> (!empty($data['eks_faktor_veil_add']))?$data['eks_faktor_veil_add']:'1',
			'eks_faktor_csm' 		=> (!empty($data['eks_faktor_csm']))?$data['eks_faktor_csm']:'1',
			'eks_faktor_csm_add' 	=> (!empty($data['eks_faktor_csm_add']))?$data['eks_faktor_csm_add']:'1',
			'eks_resin' 			=> (!empty($data['eks_resin']))?$data['eks_resin']:'1',
			'topcoat_resin' 		=> (!empty($data['topcoat_resin']))?$data['topcoat_resin']:'1',
			
			'str_n1_resin_csm_a' 		=> (!empty($data['str_n1_resin_csm_a']))?$data['str_n1_resin_csm_a']:'1',
			'str_n1_resin_csm_b' 		=> (!empty($data['str_n1_resin_csm_b']))?$data['str_n1_resin_csm_b']:'1',
			'str_n1_resin_csm' 			=> (!empty($data['str_n1_resin_csm']))?$data['str_n1_resin_csm']:'1',
			'str_n1_resin_csm_add_a' 	=> (!empty($data['str_n1_resin_csm_add_a']))?$data['str_n1_resin_csm_add_a']:'1',
			'str_n1_resin_csm_add_b' 	=> (!empty($data['str_n1_resin_csm_add_b']))?$data['str_n1_resin_csm_add_b']:'1',
			'str_n1_resin_csm_add' 		=> (!empty($data['str_n1_resin_csm_add']))?$data['str_n1_resin_csm_add']:'1',
			'str_n1_resin_wr_a' 		=> (!empty($data['str_n1_resin_wr_a']))?$data['str_n1_resin_wr_a']:'1',
			'str_n1_resin_wr_b' 		=> (!empty($data['str_n1_resin_wr_b']))?$data['str_n1_resin_wr_b']:'1',
			'str_n1_resin_wr' 			=> (!empty($data['str_n1_resin_wr']))?$data['str_n1_resin_wr']:'1',
			'str_n1_resin_wr_add_a' 	=> (!empty($data['str_n1_resin_wr_add_a']))?$data['str_n1_resin_wr_add_a']:'1',
			'str_n1_resin_wr_add_b' 	=> (!empty($data['str_n1_resin_wr_add_b']))?$data['str_n1_resin_wr_add_b']:'1',
			'str_n1_resin_wr_add' 		=> (!empty($data['str_n1_resin_wr_add']))?$data['str_n1_resin_wr_add']:'1',
			'str_n1_resin_rv_a' 		=> (!empty($data['str_n1_resin_rv_a']))?$data['str_n1_resin_rv_a']:'1',
			'str_n1_resin_rv_b' 		=> (!empty($data['str_n1_resin_rv_b']))?$data['str_n1_resin_rv_b']:'1',
			'str_n1_resin_rv' 			=> (!empty($data['str_n1_resin_rv']))?$data['str_n1_resin_rv']:'1',
			'str_n1_resin_rv_add_a' 	=> (!empty($data['str_n1_resin_rv_add_a']))?$data['str_n1_resin_rv_add_a']:'1',
			'str_n1_resin_rv_add_b' 	=> (!empty($data['str_n1_resin_rv_add_b']))?$data['str_n1_resin_rv_add_b']:'1',
			'str_n1_resin_rv_add' 		=> (!empty($data['str_n1_resin_rv_add']))?$data['str_n1_resin_rv_add']:'1',
			'str_n1_faktor_csm' 		=> (!empty($data['str_n1_faktor_csm']))?$data['str_n1_faktor_csm']:'1',
			'str_n1_faktor_csm_add' 	=> (!empty($data['str_n1_faktor_csm_add']))?$data['str_n1_faktor_csm_add']:'1',
			'str_n1_faktor_wr' 			=> (!empty($data['str_n1_faktor_wr']))?$data['str_n1_faktor_wr']:'1',
			'str_n1_faktor_wr_add' 		=> (!empty($data['str_n1_faktor_wr_add']))?$data['str_n1_faktor_wr_add']:'1',
			'str_n1_faktor_rv' 			=> (!empty($data['str_n1_faktor_rv']))?$data['str_n1_faktor_rv']:'1',
			'str_n1_faktor_rv_bw' 		=> (!empty($data['str_n1_faktor_rv_bw']))?$data['str_n1_faktor_rv_bw']:'1',
			'str_n1_faktor_rv_jb' 		=> (!empty($data['str_n1_faktor_rv_jb']))?$data['str_n1_faktor_rv_jb']:'1',
			'str_n1_faktor_rv_add' 		=> (!empty($data['str_n1_faktor_rv_add']))?$data['str_n1_faktor_rv_add']:'1',
			'str_n1_faktor_rv_add_bw' 	=> (!empty($data['str_n1_faktor_rv_add_bw']))?$data['str_n1_faktor_rv_add_bw']:'1',
			'str_n1_faktor_rv_add_jb' 	=> (!empty($data['str_n1_faktor_rv_add_jb']))?$data['str_n1_faktor_rv_add_jb']:'1',
			'str_n1_resin' 				=> (!empty($data['str_n1_resin']))?$data['str_n1_resin']:'1',
			'str_n1_resin_thickness' 	=> (!empty($data['str_n1_resin_thickness']))?$data['str_n1_resin_thickness']:'1',
			
			'str_n2_resin_csm_a' 		=> (!empty($data['str_n2_resin_csm_a']))?$data['str_n2_resin_csm_a']:'1',
			'str_n2_resin_csm_b' 		=> (!empty($data['str_n2_resin_csm_b']))?$data['str_n2_resin_csm_b']:'1',
			'str_n2_resin_csm' 			=> (!empty($data['str_n2_resin_csm']))?$data['str_n2_resin_csm']:'1',
			'str_n2_resin_csm_add_a' 	=> (!empty($data['str_n2_resin_csm_add_a']))?$data['str_n2_resin_csm_add_a']:'1',
			'str_n2_resin_csm_add_b' 	=> (!empty($data['str_n2_resin_csm_add_b']))?$data['str_n2_resin_csm_add_b']:'1',
			'str_n2_resin_csm_add' 		=> (!empty($data['str_n2_resin_csm_add']))?$data['str_n2_resin_csm_add']:'1',
			'str_n2_resin_wr_a' 		=> (!empty($data['str_n2_resin_wr_a']))?$data['str_n2_resin_wr_a']:'1',
			'str_n2_resin_wr_b' 		=> (!empty($data['str_n2_resin_wr_b']))?$data['str_n2_resin_wr_b']:'1',
			'str_n2_resin_wr' 			=> (!empty($data['str_n2_resin_wr']))?$data['str_n2_resin_wr']:'1',
			'str_n2_resin_wr_add_a' 	=> (!empty($data['str_n2_resin_wr_add_a']))?$data['str_n2_resin_wr_add_a']:'1',
			'str_n2_resin_wr_add_b' 	=> (!empty($data['str_n2_resin_wr_add_b']))?$data['str_n2_resin_wr_add_b']:'1',
			'str_n2_resin_wr_add' 		=> (!empty($data['str_n2_resin_wr_add']))?$data['str_n2_resin_wr_add']:'1',
			'str_n2_faktor_csm' 		=> (!empty($data['str_n2_faktor_csm']))?$data['str_n2_faktor_csm']:'1',
			'str_n2_faktor_csm_add' 	=> (!empty($data['str_n2_faktor_csm_add']))?$data['str_n2_faktor_csm_add']:'1',
			'str_n2_faktor_wr' 			=> (!empty($data['str_n2_faktor_wr']))?$data['str_n2_faktor_wr']:'1',
			'str_n2_faktor_wr_add' 		=> (!empty($data['str_n2_faktor_wr_add']))?$data['str_n2_faktor_wr_add']:'1',
			'str_n2_resin' 				=> (!empty($data['str_n2_resin']))?$data['str_n2_resin']:'1',
			'str_n2_resin_thickness' 	=> (!empty($data['str_n2_resin_thickness']))?$data['str_n2_resin_thickness']:'1',
			
			'modified_by'			=> $data_session['ORI_User']['username'],
			'modified_date'			=> date('Y-m-d H:i:s')
		);
		
		// print_r($insertData);
		// exit; 
		
		
		
		$this->db->trans_start();
			$this->db->update('so_component_default', $insertData, array('id' => $data['id']));
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'			=> 'Failed Edit Default. Please try again later ...',
				'status'		=> 0,
				'pembeda'		=> '',
				'help_url'		=> $data['help_url'],
				'id_milik' 		=> $data['id_milik'],
				'id_bq' 		=> $data['id_bq']
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=> 'Success Edit Default. Thanks ...',
				'status'		=> 1,
				'pembeda'		=> '',
				'help_url'		=> $data['help_url'],
				'id_milik' 		=> $data['id_milik'],
				'id_bq' 		=> $data['id_bq']
				
			);
			history("Edit Default Data Estimasi in final drawing Project ".$data['id_bq']." id milik = ".$data['id_milik']); 
		}
		

		echo json_encode($Arr_Kembali);
	}  
	
	public function getDefaultEditBqFD(){
		$diameter			= $this->input->post("diameter");
		$standart			= $this->input->post("standart");
		$parent_product		= $this->input->post("parent_product");
		$id_milik			= $this->input->post("id_milik");
		
		$qDefault	= "SELECT * FROM so_component_default WHERE id_milik='".$id_milik."' ";
		$getNum		= $this->db->query($qDefault)->num_rows();
		
		// echo $getNum; exit;
		if($getNum > 0){
			$getDefault	= $this->db->query($qDefault)->result(); 
			$ArrJson	= array(
				'tamp' 					=> "Default berhasil ditemukan",
				'color'					=> "green",
				'hasilx' 				=> $getNum,
				'standart' 				=> $standart,
				'waste' 				=> floatval($getDefault[0]->waste),
				'overlap' 				=> floatval($getDefault[0]->overlap),
				'maxx' 					=> floatval($getDefault[0]->max),
				'minx' 					=> floatval($getDefault[0]->min),
				'plastic_film' 			=> floatval($getDefault[0]->plastic_film),
				'lin_resin_veil' 		=> floatval($getDefault[0]->lin_resin_veil),
				'lin_resin_veil_add' 	=> floatval($getDefault[0]->lin_resin_veil_add),
				'lin_resin_csm' 		=> floatval($getDefault[0]->lin_resin_csm),
				'lin_resin_csm_add' 	=> floatval($getDefault[0]->lin_resin_csm_add),
				'lin_faktor_veil' 		=> floatval($getDefault[0]->lin_faktor_veil),
				'lin_faktor_veil_add' 	=> floatval($getDefault[0]->lin_faktor_veil_add),
				'lin_faktor_csm' 		=> floatval($getDefault[0]->lin_faktor_csm),
				'lin_faktor_csm_add' 	=> floatval($getDefault[0]->lin_faktor_csm_add),
				'lin_resin' 			=> floatval($getDefault[0]->lin_resin),
				'lin_resin_thickness' 	=> floatval($getDefault[0]->lin_resin_thickness),
				'str_resin_csm' 		=> floatval($getDefault[0]->str_resin_csm),
				'str_resin_csm_add' 	=> floatval($getDefault[0]->str_resin_csm_add),
				'str_resin_wr' 			=> floatval($getDefault[0]->str_resin_wr),
				'str_resin_wr_add' 		=> floatval($getDefault[0]->str_resin_wr_add),
				'str_resin_rv' 			=> floatval($getDefault[0]->str_resin_rv),
				'str_resin_rv_add' 		=> floatval($getDefault[0]->str_resin_rv_add),
				'str_faktor_csm' 		=> floatval($getDefault[0]->str_faktor_csm),
				'str_faktor_csm_add' 	=> floatval($getDefault[0]->str_faktor_csm_add),
				'str_faktor_wr' 		=> floatval($getDefault[0]->str_faktor_wr),
				'str_faktor_wr_add' 	=> floatval($getDefault[0]->str_faktor_wr_add),
				'str_faktor_rv' 		=> floatval($getDefault[0]->str_faktor_rv),
				'str_faktor_rv_bw' 		=> floatval($getDefault[0]->str_faktor_rv_bw),
				'str_faktor_rv_jb' 		=> floatval($getDefault[0]->str_faktor_rv_jb),
				'str_faktor_rv_add' 	=> floatval($getDefault[0]->str_faktor_rv_add),
				'str_faktor_rv_add_bw' 	=> floatval($getDefault[0]->str_faktor_rv_add_bw),
				'str_faktor_rv_add_jb' 	=> floatval($getDefault[0]->str_faktor_rv_add_jb),
				'str_resin' 			=> floatval($getDefault[0]->str_resin),
				'str_resin_thickness' 	=> floatval($getDefault[0]->str_resin_thickness),
				'eks_resin_veil' 		=> floatval($getDefault[0]->eks_resin_veil),
				'eks_resin_veil_add' 	=> floatval($getDefault[0]->eks_resin_veil_add),
				'eks_resin_csm' 		=> floatval($getDefault[0]->eks_resin_csm),
				'eks_resin_csm_add' 	=> floatval($getDefault[0]->eks_resin_csm_add),
				'eks_faktor_veil' 		=> floatval($getDefault[0]->eks_faktor_veil),
				'eks_faktor_veil_add' 	=> floatval($getDefault[0]->eks_faktor_veil_add),
				'eks_faktor_csm' 		=> floatval($getDefault[0]->eks_faktor_csm),
				'eks_faktor_csm_add' 	=> floatval($getDefault[0]->eks_faktor_csm_add),
				'eks_resin' 			=> floatval($getDefault[0]->eks_resin),
				'eks_resin_thickness' 	=> floatval($getDefault[0]->eks_resin_thickness),
				'str_n1_resin_csm' 			=> floatval($getDefault[0]->str_n1_resin_csm),
				'str_n1_resin_csm_add' 		=> floatval($getDefault[0]->str_n1_resin_csm_add),
				'str_n1_resin_wr' 			=> floatval($getDefault[0]->str_n1_resin_wr),
				'str_n1_resin_wr_add' 		=> floatval($getDefault[0]->str_n1_resin_wr_add),
				'str_n1_resin_rv' 			=> floatval($getDefault[0]->str_n1_resin_rv),
				'str_n1_resin_rv_add' 		=> floatval($getDefault[0]->str_n1_resin_rv_add),
				'str_n1_faktor_csm' 		=> floatval($getDefault[0]->str_n1_faktor_csm),
				'str_n1_faktor_csm_add' 	=> floatval($getDefault[0]->str_n1_faktor_csm_add),
				'str_n1_faktor_wr' 			=> floatval($getDefault[0]->str_n1_faktor_wr),
				'str_n1_faktor_wr_add' 		=> floatval($getDefault[0]->str_n1_faktor_wr_add),
				'str_n1_faktor_rv' 			=> floatval($getDefault[0]->str_n1_faktor_rv),
				'str_n1_faktor_rv_bw' 		=> floatval($getDefault[0]->str_n1_faktor_rv_bw),
				'str_n1_faktor_rv_jb' 		=> floatval($getDefault[0]->str_n1_faktor_rv_jb),
				'str_n1_faktor_rv_add' 		=> floatval($getDefault[0]->str_n1_faktor_rv_add),
				'str_n1_faktor_rv_add_bw' 	=> floatval($getDefault[0]->str_n1_faktor_rv_add_bw),
				'str_n1_faktor_rv_add_jb' 	=> floatval($getDefault[0]->str_n1_faktor_rv_add_jb),
				'str_n1_resin' 				=> floatval($getDefault[0]->str_n1_resin),
				'str_n1_resin_thickness' 	=> floatval($getDefault[0]->str_n1_resin_thickness),
				'str_n2_resin_csm' 			=> floatval($getDefault[0]->str_n2_resin_csm),
				'str_n2_resin_csm_add' 		=> floatval($getDefault[0]->str_n2_resin_csm_add),
				'str_n2_resin_wr' 			=> floatval($getDefault[0]->str_n2_resin_wr),
				'str_n2_resin_wr_add' 		=> floatval($getDefault[0]->str_n2_resin_wr_add),
				'str_n2_faktor_csm' 		=> floatval($getDefault[0]->str_n2_faktor_csm),
				'str_n2_faktor_csm_add' 	=> floatval($getDefault[0]->str_n2_faktor_csm_add),
				'str_n2_faktor_wr' 			=> floatval($getDefault[0]->str_n2_faktor_wr),
				'str_n2_faktor_wr_add' 		=> floatval($getDefault[0]->str_n2_faktor_wr_add),
				'str_n2_resin' 				=> floatval($getDefault[0]->str_n2_resin),
				'str_n2_resin_thickness' 	=> floatval($getDefault[0]->str_n2_resin_thickness),
				'topcoat_resin' 			=> floatval($getDefault[0]->topcoat_resin) 
			);
			
			
		}
		else{
			$ArrJson	= array(
				'tamp' 					=> "Default tidak ditemukan",
				'color'					=> "red",
				'hasilx' 				=> $getNum,
				'pipeD' 				=> $diameter,
				'product' 				=> $parent_product
			);
		}
		
		// echo "<pre>";
		// print_r($ArrJson); exit;
		
		
		echo json_encode($ArrJson);
		
		
		
	}
	
}