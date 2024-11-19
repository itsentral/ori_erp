<?php
class Component_custom_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function edit_save_default($id_product=null,$parent_product=null,$standart=null,$diameter=null,$id_product_before=null){

        $data_session	= $this->session->userdata;
		$dateTime = date('Y-m-d H:i:s');
		
		$ArrHistoryDefault = [];
        $checkDefault	= $this->db->get_where('component_default', array('id_product'=>$id_product_before))->num_rows();
        if($checkDefault < 1){
            $getDefault	= $this->db->limit(1)->get_where('help_default', array('product_parent'=>$parent_product,'standart_code'=>$standart,'diameter'=>$diameter))->result();
        }
        if($checkDefault > 0){
            $getDefault	= $this->db->limit(1)->get_where('component_default', array('id_product'=>$id_product_before))->result();
			$ArrHistoryDefault = [
				'id_product' => $getDefault[0]->id_product,
				'product_parent' => $getDefault[0]->product_parent,
				'kd_cust' => $getDefault[0]->kd_cust,
				'customer' => $getDefault[0]->customer,
				'standart_code' => $getDefault[0]->standart_code,
				'diameter' => $getDefault[0]->diameter,
				'diameter2' => $getDefault[0]->diameter2,
				'liner' => $getDefault[0]->liner,
				'pn' => $getDefault[0]->pn,
				'overlap' => $getDefault[0]->overlap,
				'waste' => $getDefault[0]->waste,
				'waste_n1' => $getDefault[0]->waste_n1,
				'waste_n2' => $getDefault[0]->waste_n2,
				'max' => $getDefault[0]->max,
				'min' => $getDefault[0]->min,
				'plastic_film' => $getDefault[0]->plastic_film,
				'lin_resin_veil_a' => $getDefault[0]->lin_resin_veil_a,
				'lin_resin_veil_b' => $getDefault[0]->lin_resin_veil_b,
				'lin_resin_veil' => $getDefault[0]->lin_resin_veil,
				'lin_resin_veil_add_a' => $getDefault[0]->lin_resin_veil_add_a,
				'lin_resin_veil_add_b' => $getDefault[0]->lin_resin_veil_add_b,
				'lin_resin_veil_add' => $getDefault[0]->lin_resin_veil_add,
				'lin_resin_csm_a' => $getDefault[0]->lin_resin_csm_a,
				'lin_resin_csm_b' => $getDefault[0]->lin_resin_csm_b,
				'lin_resin_csm' => $getDefault[0]->lin_resin_csm,
				'lin_resin_csm_add_a' => $getDefault[0]->lin_resin_csm_add_a,
				'lin_resin_csm_add_b' => $getDefault[0]->lin_resin_csm_add_b,
				'lin_resin_csm_add' => $getDefault[0]->lin_resin_csm_add,
				'lin_faktor_veil' => $getDefault[0]->lin_faktor_veil,
				'lin_faktor_veil_add' => $getDefault[0]->lin_faktor_veil_add,
				'lin_faktor_csm' => $getDefault[0]->lin_faktor_csm,
				'lin_faktor_csm_add' => $getDefault[0]->lin_faktor_csm_add,
				'lin_resin' => $getDefault[0]->lin_resin,
				'lin_resin_thickness' => $getDefault[0]->lin_resin_thickness,
				'str_resin_csm_a' => $getDefault[0]->str_resin_csm_a,
				'str_resin_csm_b' => $getDefault[0]->str_resin_csm_b,
				'str_resin_csm' => $getDefault[0]->str_resin_csm,
				'str_resin_csm_add_a' => $getDefault[0]->str_resin_csm_add_a,
				'str_resin_csm_add_b' => $getDefault[0]->str_resin_csm_add_b,
				'str_resin_csm_add' => $getDefault[0]->str_resin_csm_add,
				'str_resin_wr_a' => $getDefault[0]->str_resin_wr_a,
				'str_resin_wr_b' => $getDefault[0]->str_resin_wr_b,
				'str_resin_wr' => $getDefault[0]->str_resin_wr,
				'str_resin_wr_add_a' => $getDefault[0]->str_resin_wr_add_a,
				'str_resin_wr_add_b' => $getDefault[0]->str_resin_wr_add_b,
				'str_resin_wr_add' => $getDefault[0]->str_resin_wr_add,
				'str_resin_rv_a' => $getDefault[0]->str_resin_rv_a,
				'str_resin_rv_b' => $getDefault[0]->str_resin_rv_b,
				'str_resin_rv' => $getDefault[0]->str_resin_rv,
				'str_resin_rv_add_a' => $getDefault[0]->str_resin_rv_add_a,
				'str_resin_rv_add_b' => $getDefault[0]->str_resin_rv_add_b,
				'str_resin_rv_add' => $getDefault[0]->str_resin_rv_add,
				'str_faktor_csm' => $getDefault[0]->str_faktor_csm,
				'str_faktor_csm_add' => $getDefault[0]->str_faktor_csm_add,
				'str_faktor_wr' => $getDefault[0]->str_faktor_wr,
				'str_faktor_wr_add' => $getDefault[0]->str_faktor_wr_add,
				'str_faktor_rv' => $getDefault[0]->str_faktor_rv,
				'str_faktor_rv_bw' => $getDefault[0]->str_faktor_rv_bw,
				'str_faktor_rv_jb' => $getDefault[0]->str_faktor_rv_jb,
				'str_faktor_rv_add' => $getDefault[0]->str_faktor_rv_add,
				'str_faktor_rv_add_bw' => $getDefault[0]->str_faktor_rv_add_bw,
				'str_faktor_rv_add_jb' => $getDefault[0]->str_faktor_rv_add_jb,
				'str_resin' => $getDefault[0]->str_resin,
				'str_resin_thickness' => $getDefault[0]->str_resin_thickness,
				'str_resin_default' => $getDefault[0]->str_resin_default,
				'eks_resin_veil_a' => $getDefault[0]->eks_resin_veil_a,
				'eks_resin_veil_b' => $getDefault[0]->eks_resin_veil_b,
				'eks_resin_veil' => $getDefault[0]->eks_resin_veil,
				'eks_resin_veil_add_a' => $getDefault[0]->eks_resin_veil_add_a,
				'eks_resin_veil_add_b' => $getDefault[0]->eks_resin_veil_add_b,
				'eks_resin_veil_add' => $getDefault[0]->eks_resin_veil_add,
				'eks_resin_csm_a' => $getDefault[0]->eks_resin_csm_a,
				'eks_resin_csm_b' => $getDefault[0]->eks_resin_csm_b,
				'eks_resin_csm' => $getDefault[0]->eks_resin_csm,
				'eks_resin_csm_add_a' => $getDefault[0]->eks_resin_csm_add_a,
				'eks_resin_csm_add_b' => $getDefault[0]->eks_resin_csm_add_b,
				'eks_resin_csm_add' => $getDefault[0]->eks_resin_csm_add,
				'eks_faktor_veil' => $getDefault[0]->eks_faktor_veil,
				'eks_faktor_veil_add' => $getDefault[0]->eks_faktor_veil_add,
				'eks_faktor_csm' => $getDefault[0]->eks_faktor_csm,
				'eks_faktor_csm_add' => $getDefault[0]->eks_faktor_csm_add,
				'eks_resin' => $getDefault[0]->eks_resin,
				'eks_resin_default' => $getDefault[0]->eks_resin_default,
				'eks_resin_thickness' => $getDefault[0]->eks_resin_thickness,
				'topcoat_resin' => $getDefault[0]->topcoat_resin,
				'created_by' => $getDefault[0]->created_by,
				'created_date' => $getDefault[0]->created_date,
				'modified_by' => $getDefault[0]->modified_by,
				'modified_date' => $getDefault[0]->modified_date,
				'str_n1_resin_csm_a' => $getDefault[0]->str_n1_resin_csm_a,
				'str_n1_resin_csm_b' => $getDefault[0]->str_n1_resin_csm_b,
				'str_n1_resin_csm' => $getDefault[0]->str_n1_resin_csm,
				'str_n1_resin_csm_add_a' => $getDefault[0]->str_n1_resin_csm_add_a,
				'str_n1_resin_csm_add_b' => $getDefault[0]->str_n1_resin_csm_add_b,
				'str_n1_resin_csm_add' => $getDefault[0]->str_n1_resin_csm_add,
				'str_n1_resin_wr_a' => $getDefault[0]->str_n1_resin_wr_a,
				'str_n1_resin_wr_b' => $getDefault[0]->str_n1_resin_wr_b,
				'str_n1_resin_wr' => $getDefault[0]->str_n1_resin_wr,
				'str_n1_resin_wr_add_a' => $getDefault[0]->str_n1_resin_wr_add_a,
				'str_n1_resin_wr_add_b' => $getDefault[0]->str_n1_resin_wr_add_b,
				'str_n1_resin_wr_add' => $getDefault[0]->str_n1_resin_wr_add,
				'str_n1_resin_rv_a' => $getDefault[0]->str_n1_resin_rv_a,
				'str_n1_resin_rv_b' => $getDefault[0]->str_n1_resin_rv_b,
				'str_n1_resin_rv' => $getDefault[0]->str_n1_resin_rv,
				'str_n1_resin_rv_add_a' => $getDefault[0]->str_n1_resin_rv_add_a,
				'str_n1_resin_rv_add_b' => $getDefault[0]->str_n1_resin_rv_add_b,
				'str_n1_resin_rv_add' => $getDefault[0]->str_n1_resin_rv_add,
				'str_n1_faktor_csm' => $getDefault[0]->str_n1_faktor_csm,
				'str_n1_faktor_csm_add' => $getDefault[0]->str_n1_faktor_csm_add,
				'str_n1_faktor_wr' => $getDefault[0]->str_n1_faktor_wr,
				'str_n1_faktor_wr_add' => $getDefault[0]->str_n1_faktor_wr_add,
				'str_n1_faktor_rv' => $getDefault[0]->str_n1_faktor_rv,
				'str_n1_faktor_rv_bw' => $getDefault[0]->str_n1_faktor_rv_bw,
				'str_n1_faktor_rv_jb' => $getDefault[0]->str_n1_faktor_rv_jb,
				'str_n1_faktor_rv_add' => $getDefault[0]->str_n1_faktor_rv_add,
				'str_n1_faktor_rv_add_bw' => $getDefault[0]->str_n1_faktor_rv_add_bw,
				'str_n1_faktor_rv_add_jb' => $getDefault[0]->str_n1_faktor_rv_add_jb,
				'str_n1_resin' => $getDefault[0]->str_n1_resin,
				'str_n1_resin_thickness' => $getDefault[0]->str_n1_resin_thickness,
				'str_n2_resin_csm_a' => $getDefault[0]->str_n2_resin_csm_a,
				'str_n2_resin_csm_b' => $getDefault[0]->str_n2_resin_csm_b,
				'str_n2_resin_csm' => $getDefault[0]->str_n2_resin_csm,
				'str_n2_resin_csm_add_a' => $getDefault[0]->str_n2_resin_csm_add_a,
				'str_n2_resin_csm_add_b' => $getDefault[0]->str_n2_resin_csm_add_b,
				'str_n2_resin_csm_add' => $getDefault[0]->str_n2_resin_csm_add,
				'str_n2_resin_wr_a' => $getDefault[0]->str_n2_resin_wr_a,
				'str_n2_resin_wr_b' => $getDefault[0]->str_n2_resin_wr_b,
				'str_n2_resin_wr' => $getDefault[0]->str_n2_resin_wr,
				'str_n2_resin_wr_add_a' => $getDefault[0]->str_n2_resin_wr_add_a,
				'str_n2_resin_wr_add_b' => $getDefault[0]->str_n2_resin_wr_add_b,
				'str_n2_resin_wr_add' => $getDefault[0]->str_n2_resin_wr_add,
				'str_n2_faktor_csm' => $getDefault[0]->str_n2_faktor_csm,
				'str_n2_faktor_csm_add' => $getDefault[0]->str_n2_faktor_csm_add,
				'str_n2_faktor_wr' => $getDefault[0]->str_n2_faktor_wr,
				'str_n2_faktor_wr_add' => $getDefault[0]->str_n2_faktor_wr_add,
				'str_n2_resin' => $getDefault[0]->str_n2_resin,
				'str_n2_resin_thickness' => $getDefault[0]->str_n2_resin_thickness,
				'hist_by' => $data_session['ORI_User']['username'],
				'hist_date' => $dateTime
			];
        }

        if($id_product != $id_product_before){
            history('Estimasi '.$id_product.' cloning dari '.$id_product_before);
        }
        
        $ArrJson	= array(
            'id_product' 			=> $id_product,
            'product_parent' 		=> $getDefault[0]->product_parent,
            'standart_code' 		=> $getDefault[0]->standart_code,
            'diameter' 				=> $getDefault[0]->diameter,
            'diameter2' 			=> $getDefault[0]->diameter2,
            'liner' 				=> $getDefault[0]->liner,
            'pn' 					=> $getDefault[0]->pn,
            'waste' 				=> floatval($getDefault[0]->waste),
            'waste_n1' 				=> floatval($getDefault[0]->waste_n1),
            'waste_n2' 				=> floatval($getDefault[0]->waste_n2),
            'overlap' 				=> floatval($getDefault[0]->overlap),
            'max' 					=> floatval($getDefault[0]->max),
            'min' 					=> floatval($getDefault[0]->min),
            'plastic_film' 			=> floatval($getDefault[0]->plastic_film),
            'lin_resin_veil_a' => $getDefault[0]->lin_resin_veil_a,
            'lin_resin_veil_b' => $getDefault[0]->lin_resin_veil_b,
            'lin_resin_veil' => $getDefault[0]->lin_resin_veil,
            'lin_resin_veil_add_a' => $getDefault[0]->lin_resin_veil_add_a,
            'lin_resin_veil_add_b' => $getDefault[0]->lin_resin_veil_add_b,
            'lin_resin_veil_add' => $getDefault[0]->lin_resin_veil_add,
            'lin_resin_csm_a' => $getDefault[0]->lin_resin_csm_a,
            'lin_resin_csm_b' => $getDefault[0]->lin_resin_csm_b,
            'lin_resin_csm' => $getDefault[0]->lin_resin_csm,
            'lin_resin_csm_add_a' => $getDefault[0]->lin_resin_csm_add_a,
            'lin_resin_csm_add_b' => $getDefault[0]->lin_resin_csm_add_b,
            'lin_resin_csm_add' => $getDefault[0]->lin_resin_csm_add,
            'lin_faktor_veil' => $getDefault[0]->lin_faktor_veil,
            'lin_faktor_veil_add' => $getDefault[0]->lin_faktor_veil_add,
            'lin_faktor_csm' => $getDefault[0]->lin_faktor_csm,
            'lin_faktor_csm_add' => $getDefault[0]->lin_faktor_csm_add,
            'lin_resin' => $getDefault[0]->lin_resin,
            'lin_resin_thickness' => $getDefault[0]->lin_resin_thickness,
            'str_resin_csm_a' => $getDefault[0]->str_resin_csm_a,
            'str_resin_csm_b' => $getDefault[0]->str_resin_csm_b,
            'str_resin_csm' => $getDefault[0]->str_resin_csm,
            'str_resin_csm_add_a' => $getDefault[0]->str_resin_csm_add_a,
            'str_resin_csm_add_b' => $getDefault[0]->str_resin_csm_add_b,
            'str_resin_csm_add' => $getDefault[0]->str_resin_csm_add,
            'str_resin_wr_a' => $getDefault[0]->str_resin_wr_a,
            'str_resin_wr_b' => $getDefault[0]->str_resin_wr_b,
            'str_resin_wr' => $getDefault[0]->str_resin_wr,
            'str_resin_wr_add_a' => $getDefault[0]->str_resin_wr_add_a,
            'str_resin_wr_add_b' => $getDefault[0]->str_resin_wr_add_b,
            'str_resin_wr_add' => $getDefault[0]->str_resin_wr_add,
            'str_resin_rv_a' => $getDefault[0]->str_resin_rv_a,
            'str_resin_rv_b' => $getDefault[0]->str_resin_rv_b,
            'str_resin_rv' => $getDefault[0]->str_resin_rv,
            'str_resin_rv_add_a' => $getDefault[0]->str_resin_rv_add_a,
            'str_resin_rv_add_b' => $getDefault[0]->str_resin_rv_add_b,
            'str_resin_rv_add' => $getDefault[0]->str_resin_rv_add,
            'str_faktor_csm' => $getDefault[0]->str_faktor_csm,
            'str_faktor_csm_add' => $getDefault[0]->str_faktor_csm_add,
            'str_faktor_wr' => $getDefault[0]->str_faktor_wr,
            'str_faktor_wr_add' => $getDefault[0]->str_faktor_wr_add,
            'str_faktor_rv' => $getDefault[0]->str_faktor_rv,
            'str_faktor_rv_bw' => $getDefault[0]->str_faktor_rv_bw,
            'str_faktor_rv_jb' => $getDefault[0]->str_faktor_rv_jb,
            'str_faktor_rv_add' => $getDefault[0]->str_faktor_rv_add,
            'str_faktor_rv_add_bw' => $getDefault[0]->str_faktor_rv_add_bw,
            'str_faktor_rv_add_jb' => $getDefault[0]->str_faktor_rv_add_jb,
            'str_resin' => $getDefault[0]->str_resin,
            'str_resin_thickness' => $getDefault[0]->str_resin_thickness,
            'str_resin_default' => $getDefault[0]->str_resin_default,
            'eks_resin_veil_a' => $getDefault[0]->eks_resin_veil_a,
            'eks_resin_veil_b' => $getDefault[0]->eks_resin_veil_b,
            'eks_resin_veil' => $getDefault[0]->eks_resin_veil,
            'eks_resin_veil_add_a' => $getDefault[0]->eks_resin_veil_add_a,
            'eks_resin_veil_add_b' => $getDefault[0]->eks_resin_veil_add_b,
            'eks_resin_veil_add' => $getDefault[0]->eks_resin_veil_add,
            'eks_resin_csm_a' => $getDefault[0]->eks_resin_csm_a,
            'eks_resin_csm_b' => $getDefault[0]->eks_resin_csm_b,
            'eks_resin_csm' => $getDefault[0]->eks_resin_csm,
            'eks_resin_csm_add_a' => $getDefault[0]->eks_resin_csm_add_a,
            'eks_resin_csm_add_b' => $getDefault[0]->eks_resin_csm_add_b,
            'eks_resin_csm_add' => $getDefault[0]->eks_resin_csm_add,
            'eks_faktor_veil' => $getDefault[0]->eks_faktor_veil,
            'eks_faktor_veil_add' => $getDefault[0]->eks_faktor_veil_add,
            'eks_faktor_csm' => $getDefault[0]->eks_faktor_csm,
            'eks_faktor_csm_add' => $getDefault[0]->eks_faktor_csm_add,
            'eks_resin' => $getDefault[0]->eks_resin,
            'eks_resin_default' => $getDefault[0]->eks_resin_default,
            'eks_resin_thickness' => $getDefault[0]->eks_resin_thickness,
            'topcoat_resin' => $getDefault[0]->topcoat_resin,
            'str_n1_resin_csm_a' => $getDefault[0]->str_n1_resin_csm_a,
            'str_n1_resin_csm_b' => $getDefault[0]->str_n1_resin_csm_b,
            'str_n1_resin_csm' => $getDefault[0]->str_n1_resin_csm,
            'str_n1_resin_csm_add_a' => $getDefault[0]->str_n1_resin_csm_add_a,
            'str_n1_resin_csm_add_b' => $getDefault[0]->str_n1_resin_csm_add_b,
            'str_n1_resin_csm_add' => $getDefault[0]->str_n1_resin_csm_add,
            'str_n1_resin_wr_a' => $getDefault[0]->str_n1_resin_wr_a,
            'str_n1_resin_wr_b' => $getDefault[0]->str_n1_resin_wr_b,
            'str_n1_resin_wr' => $getDefault[0]->str_n1_resin_wr,
            'str_n1_resin_wr_add_a' => $getDefault[0]->str_n1_resin_wr_add_a,
            'str_n1_resin_wr_add_b' => $getDefault[0]->str_n1_resin_wr_add_b,
            'str_n1_resin_wr_add' => $getDefault[0]->str_n1_resin_wr_add,
            'str_n1_resin_rv_a' => $getDefault[0]->str_n1_resin_rv_a,
            'str_n1_resin_rv_b' => $getDefault[0]->str_n1_resin_rv_b,
            'str_n1_resin_rv' => $getDefault[0]->str_n1_resin_rv,
            'str_n1_resin_rv_add_a' => $getDefault[0]->str_n1_resin_rv_add_a,
            'str_n1_resin_rv_add' => $getDefault[0]->str_n1_resin_rv_add,
            'str_n1_resin_rv_add_b' => $getDefault[0]->str_n1_resin_rv_add_b,
            'str_n1_faktor_csm' => $getDefault[0]->str_n1_faktor_csm,
            'str_n1_faktor_csm_add' => $getDefault[0]->str_n1_faktor_csm_add,
            'str_n1_faktor_wr' => $getDefault[0]->str_n1_faktor_wr,
            'str_n1_faktor_wr_add' => $getDefault[0]->str_n1_faktor_wr_add,
            'str_n1_faktor_rv' => $getDefault[0]->str_n1_faktor_rv,
            'str_n1_faktor_rv_bw' => $getDefault[0]->str_n1_faktor_rv_bw,
            'str_n1_faktor_rv_jb' => $getDefault[0]->str_n1_faktor_rv_jb,
            'str_n1_faktor_rv_add' => $getDefault[0]->str_n1_faktor_rv_add,
            'str_n1_faktor_rv_add_bw' => $getDefault[0]->str_n1_faktor_rv_add_bw,
            'str_n1_faktor_rv_add_jb' => $getDefault[0]->str_n1_faktor_rv_add_jb,
            'str_n1_resin' => $getDefault[0]->str_n1_resin,
            'str_n1_resin_thickness' => $getDefault[0]->str_n1_resin_thickness,
            'str_n2_resin_csm_a' => $getDefault[0]->str_n2_resin_csm_a,
            'str_n2_resin_csm_b' => $getDefault[0]->str_n2_resin_csm_b,
            'str_n2_resin_csm' => $getDefault[0]->str_n2_resin_csm,
            'str_n2_resin_csm_add_a' => $getDefault[0]->str_n2_resin_csm_add_a,
            'str_n2_resin_csm_add_b' => $getDefault[0]->str_n2_resin_csm_add_b,
            'str_n2_resin_csm_add' => $getDefault[0]->str_n2_resin_csm_add,
            'str_n2_resin_wr_a' => $getDefault[0]->str_n2_resin_wr_a,
            'str_n2_resin_wr_b' => $getDefault[0]->str_n2_resin_wr_b,
            'str_n2_resin_wr' => $getDefault[0]->str_n2_resin_wr,
            'str_n2_resin_wr_add_a' => $getDefault[0]->str_n2_resin_wr_add_a,
            'str_n2_resin_wr_add_b' => $getDefault[0]->str_n2_resin_wr_add_b,
            'str_n2_resin_wr_add' => $getDefault[0]->str_n2_resin_wr_add,
            'str_n2_faktor_csm' => $getDefault[0]->str_n2_faktor_csm,
            'str_n2_faktor_csm_add' => $getDefault[0]->str_n2_faktor_csm_add,
            'str_n2_faktor_wr' => $getDefault[0]->str_n2_faktor_wr,
            'str_n2_faktor_wr_add' => $getDefault[0]->str_n2_faktor_wr_add,
            'str_n2_resin' => $getDefault[0]->str_n2_resin,
            'str_n2_resin_thickness' => $getDefault[0]->str_n2_resin_thickness,
            'created_by' => $data_session['ORI_User']['username'],
            'created_date' => $dateTime
        );
		
		if(!empty($ArrHistoryDefault)){
			$this->db->insert('hist_component_default', $ArrHistoryDefault);
		}

        $this->db->delete('component_default', array('id_product' => $id_product));
        $this->db->insert('component_default', $ArrJson);
	}

    public function insert_history($kode_product=null){

        $data_session	= $this->session->userdata;
		$dateTime = date('Y-m-d H:i:s');
        $ArrBqHeaderHist = array();
        $ArrBqDetailHist = array();
        $ArrBqDetailPlusHist = array();
        $ArrBqDetailAddHist = array();
        $ArrBqFooterHist = array();
        //Insert Component Header To Hist
        $qHeaderHist	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$kode_product."' ")->result_array();
        if(!empty($qHeaderHist)){
            foreach($qHeaderHist AS $val2HistA => $valx2HistA){
                $ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
                $ArrBqHeaderHist[$val2HistA]['cust']				= $valx2HistA['cust'];
                $ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
                $ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
                $ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
                $ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
                $ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
                $ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
                $ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
                $ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
                $ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
                $ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
                $ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
                $ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
                $ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
                $ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
                $ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
                $ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
                $ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
                $ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
                $ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
                $ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
                $ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
                $ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
                $ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
                $ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
                $ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
                $ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
                $ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
                $ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
                $ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
                $ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
                $ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
                $ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
                $ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
                $ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
                $ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
                $ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
                $ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
                $ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
                $ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
                $ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
                $ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
                $ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
                $ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
                $ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
                $ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
                $ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
                $ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
                $ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
                $ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
                $ArrBqHeaderHist[$val2HistA]['deleted_by']			= $this->session->userdata['ORI_User']['username'];
                $ArrBqHeaderHist[$val2HistA]['deleted_date']		= $dateTime;
            }
        }

        //Insert Component Detail To Hist
        $qDetailHist	= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$kode_product."' ")->result_array();
        if(!empty($qDetailHist)){
            foreach($qDetailHist AS $val2Hist => $valx2Hist){
                $ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
                $ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
                $ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
                $ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
                $ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
                $ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
                $ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
                $ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
                $ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
                $ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
                $ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
                $ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
                $ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
                $ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
                $ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
                $ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
                $ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
                $ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
                $ArrBqDetailHist[$val2Hist]['deleted_by']		= $this->session->userdata['ORI_User']['username'];
                $ArrBqDetailHist[$val2Hist]['deleted_date']		= $dateTime;
            }
        }

        //Insert Component Detail Plus To Hist
        $qDetailPlusHist	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$kode_product."' ")->result_array();
        if(!empty($qDetailPlusHist)){
            foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
                $ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
                $ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
                $ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
                $ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
                $ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
                $ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
                $ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
                $ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
                $ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
                $ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
                $ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
                $ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
                $ArrBqDetailPlusHist[$val3Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
                $ArrBqDetailPlusHist[$val3Hist]['deleted_date']	= $dateTime;
            }
        }

        //Insert Component Detail Add To Hist
        $qDetailAddHist		= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$kode_product."' ")->result_array();
        if(!empty($qDetailAddHist)){
            foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
                $ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
                $ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
                $ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
                $ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
                $ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
                $ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
                $ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
                $ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
                $ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
                $ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
                $ArrBqDetailAddHist[$val4Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
                $ArrBqDetailAddHist[$val4Hist]['deleted_date']	= $dateTime;
            }
        }

        //Insert Component Footer To Hist
        $qDetailFooterHist		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$kode_product."' ")->result_array();
        if(!empty($qDetailFooterHist)){
            foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
                $ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
                $ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
                $ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
                $ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
                $ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
                $ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
                $ArrBqFooterHist[$val5Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
                $ArrBqFooterHist[$val5Hist]['deleted_date']	= $dateTime;
            }
        }

        //Insert Batch Histories
        if(!empty($ArrBqHeaderHist)){
            $this->db->insert_batch('hist_component_header', $ArrBqHeaderHist);
        }
        if(!empty($ArrBqDetailHist)){
            $this->db->insert_batch('hist_component_detail', $ArrBqDetailHist);
        }
        if(!empty($ArrBqDetailPlusHist)){
            $this->db->insert_batch('hist_component_detail_plus', $ArrBqDetailPlusHist);
        }
        if(!empty($ArrBqDetailAddHist)){
            $this->db->insert_batch('hist_component_detail_add', $ArrBqDetailAddHist);
        }
        if(!empty($ArrBqFooterHist)){
            $this->db->insert_batch('hist_component_footer', $ArrBqFooterHist);
        }

	}
	
	
}
