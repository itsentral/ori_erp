<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Revenue extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('revenue_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login'); 
		}
	}
	
	public function index(){
		$this->revenue_model->index();
	}
	
	public function server_side_so(){
		$this->revenue_model->get_data_json_so();
	}
	
	public function modal_detail_so(){
		$this->revenue_model->modal_detail_so();
	}
	
	public function modal_deal_so(){
		$this->revenue_model->modal_deal_so();
	}
	
	public function delete_sebagian_so(){
		$this->revenue_model->delete_sebagian_so();
	}
	
	public function delete_sebagian_so_mat(){
		$this->revenue_model->delete_sebagian_so_mat();
	}
	
	public function delete_sebagian_so_eng_pack_trans(){
		$this->revenue_model->delete_sebagian_so_eng_pack_trans();
	}

	public function add_sebagian_so_eng_pack_trans(){
		$this->revenue_model->add_sebagian_so_eng_pack_trans();
	}
	
	public function update_qty_so(){
		$this->revenue_model->update_qty_so();
	}
	
	public function insert_sales_order(){
		$this->revenue_model->insert_sales_order();
	}
	
	public function ajukan_so(){
		$this->revenue_model->ajukan_so();
	}
	
	public function deal_project(){
		$this->revenue_model->deal_project();
	}
	
	public function get_add(){
		$this->revenue_model->get_add();
	}
	
	public function print_sales_order(){
		$this->revenue_model->print_sales_order();
	}
	
	public function print_sales_order_ex_price(){
		$this->revenue_model->print_sales_order_ex_price();
	}
	
	public function print_sales_order_usd(){
		$this->revenue_model->print_sales_order_usd();
	}
	
	//==========================================================================================================================
	//=================================================APPROVE SALES ORDER======================================================
	//==========================================================================================================================
	
	public function approve_so(){
		$this->revenue_model->approve_so();
	}
	
	public function server_side_so_app(){
		$this->revenue_model->get_data_json_so_app();
	}
	
	public function modal_approve_so(){
		$this->revenue_model->modal_approve_so();
	}

	public function modal_approve_so_new(){
		$this->revenue_model->modal_approve_so_new();
	}
	
	public function process_approve_so(){
		$this->revenue_model->process_approve_so();
	}
	
	public function insert_sales_order_approve(){
		$this->revenue_model->insert_sales_order_approve();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function modalDetailBQ(){
		$this->load->view('Sales_order/modalDetailBQ');
	}
	
	public function modalViewQuo(){
		$this->load->view('Sales_order/modalViewQuo');
	}
	
	public function AppCost(){

		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		$data_session	= $this->session->userdata;
		
		// echo $id_bq;
		//Duplicate Bq Header
		$getBq_Header = $this->db->query("SELECT * FROM bq_header WHERE id_bq='".$id_bq."' ")->result();
		$ArrBqHeader = array(
			'id_bq' => $getBq_Header[0]->id_bq,
			'no_ipp'  => $getBq_Header[0]->no_ipp,
			'series'  => $getBq_Header[0]->series,
			'order_type'  => $getBq_Header[0]->order_type,
			'ket' => $getBq_Header[0]->ket,
			'estimasi'  => $getBq_Header[0]->estimasi,
			'rev' => $getBq_Header[0]->rev,
			'created_by'  => $data_session['ORI_User']['username'],
			'created_date'  => date('Y-m-d H:i:s'),
			'modified_by' => $getBq_Header[0]->modified_by,
			'modified_date' => $getBq_Header[0]->modified_date,
			// 'cancel_by' => $getBq_Header[0]->cancel_by,
			// 'cancel_date' => $getBq_Header[0]->cancel_date,
			'est_by'  => $getBq_Header[0]->est_by,
			'est_date'  => $getBq_Header[0]->est_date,
			// 'approved'  => $getBq_Header[0]->approved,
			// 'approved_by' => $getBq_Header[0]->approved_by,
			// 'approved_date' => $getBq_Header[0]->approved_date,
			// 'approved_est'  => $getBq_Header[0]->approved_est,
			// 'approved_est_by' => $getBq_Header[0]->approved_est_by,
			// 'approved_est_date' => $getBq_Header[0]->approved_est_date,
			// 'aju_approved'  => $getBq_Header[0]->aju_approved,
			// 'aju_approved_by' => $getBq_Header[0]->aju_approved_by,
			// 'aju_approved_date' => $getBq_Header[0]->aju_approved_date,
			// 'aju_approved_est'  => $getBq_Header[0]->aju_approved_est,
			// 'aju_approved_est_by' => $getBq_Header[0]->aju_approved_est_by,
			// 'aju_approved_est_date' => $getBq_Header[0]->aju_approved_est_date,
			// 'reason_approved' => $getBq_Header[0]->reason_approved,
			// 'reason_approved_est' => $getBq_Header[0]->reason_approved_est,
			// 'so_sts'  => $getBq_Header[0]->so_sts,
			// 'so_by' => $getBq_Header[0]->so_by,
			// 'so_date' => $getBq_Header[0]->so_date
		);
		
		//Duplicate Bq Detail Header
		$getBq_DetailHeader = $this->db->query("SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND so_sts='Y' ")->result_array();
		$ArrBqDetailHeader = array();
		foreach($getBq_DetailHeader AS $val => $valx){
			$ArrBqDetailHeader[$val]['id_milik'] = $valx['id'];
			$ArrBqDetailHeader[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqDetailHeader[$val]['id_bq_header'] = $valx['id_bq_header'];
			$ArrBqDetailHeader[$val]['id_delivery'] = $valx['id_delivery'];
			$ArrBqDetailHeader[$val]['series'] = $valx['series'];
			$ArrBqDetailHeader[$val]['sub_delivery'] = $valx['sub_delivery'];
			$ArrBqDetailHeader[$val]['no_komponen'] = $valx['no_komponen'];
			$ArrBqDetailHeader[$val]['sts_delivery'] = $valx['sts_delivery'];
			$ArrBqDetailHeader[$val]['id_category'] = $valx['id_category'];
			$ArrBqDetailHeader[$val]['qty'] = $valx['qty'];
			$ArrBqDetailHeader[$val]['diameter_1'] = $valx['diameter_1'];
			$ArrBqDetailHeader[$val]['diameter_2'] = $valx['diameter_2'];
			$ArrBqDetailHeader[$val]['length'] = $valx['length'];
			$ArrBqDetailHeader[$val]['thickness'] = $valx['thickness'];
			$ArrBqDetailHeader[$val]['sudut'] = $valx['sudut'];
			$ArrBqDetailHeader[$val]['id_standard'] = $valx['id_standard'];
			$ArrBqDetailHeader[$val]['type'] = $valx['type'];
			$ArrBqDetailHeader[$val]['id_product'] = $valx['id_product'];
			$ArrBqDetailHeader[$val]['no_spk'] = $valx['no_spk'];
			$ArrBqDetailHeader[$val]['so_sts'] = $valx['so_sts'];
			$ArrBqDetailHeader[$val]['so_by'] = $valx['so_by'];
			$ArrBqDetailHeader[$val]['so_date'] = $valx['so_date'];

			$ArrBqDetailHeader[$val]['man_power'] = $valx['man_power'];
			$ArrBqDetailHeader[$val]['id_mesin'] = $valx['id_mesin'];
			$ArrBqDetailHeader[$val]['total_time'] = $valx['total_time'];
			$ArrBqDetailHeader[$val]['man_hours'] = $valx['man_hours'];

			$ArrBqDetailHeader[$val]['pe_direct_labour'] 			= $valx['pe_direct_labour'];
			$ArrBqDetailHeader[$val]['pe_indirect_labour'] 		= $valx['pe_indirect_labour'];
			$ArrBqDetailHeader[$val]['pe_machine'] 				= $valx['pe_machine'];
			$ArrBqDetailHeader[$val]['pe_mould_mandrill'] 			= $valx['pe_mould_mandrill'];
			$ArrBqDetailHeader[$val]['pe_consumable'] 				= $valx['pe_consumable'];
			$ArrBqDetailHeader[$val]['pe_foh_consumable'] 			= $valx['pe_foh_consumable'];
			$ArrBqDetailHeader[$val]['pe_foh_depresiasi'] 			= $valx['pe_foh_depresiasi'];
			$ArrBqDetailHeader[$val]['pe_biaya_gaji_non_produksi'] = $valx['pe_biaya_gaji_non_produksi'];
			$ArrBqDetailHeader[$val]['pe_biaya_non_produksi'] 		= $valx['pe_biaya_non_produksi'];
			$ArrBqDetailHeader[$val]['pe_biaya_rutin_bulanan'] 	= $valx['pe_biaya_rutin_bulanan'];
		}
		
		$dtListArray = array();
		foreach($getBq_DetailHeader AS $val => $valx){
			$dtListArray[$val] = $valx['id_bq_header'];
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";
		
		$dtListArray2 = array();
		foreach($getBq_DetailHeader AS $val => $valx){
			$dtListArray2[$val] = $valx['id'];
		}
		$dtImplode2	= "('".implode("','", $dtListArray2)."')";
		
		//Duplicate Bq Detail Detail
		$getBq_DetailDetail = $this->db->query("SELECT * FROM bq_detail_detail WHERE id_bq='".$id_bq."' AND id_bq_header IN ".$dtImplode." ")->result_array();
		$ArrBqDetailDetail = array();
		foreach($getBq_DetailDetail AS $val => $valx){
			$ArrBqDetailDetail[$val]['id_milik'] = $valx['id'];
			$ArrBqDetailDetail[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqDetailDetail[$val]['id_bq_header'] = $valx['id_bq_header'];
			$ArrBqDetailDetail[$val]['id_delivery'] = $valx['id_delivery'];
			$ArrBqDetailDetail[$val]['series'] = $valx['series'];
			$ArrBqDetailDetail[$val]['sub_delivery'] = $valx['sub_delivery'];
			$ArrBqDetailDetail[$val]['sts_delivery'] = $valx['sts_delivery'];
			$ArrBqDetailDetail[$val]['id_category'] = $valx['id_category'];
			$ArrBqDetailDetail[$val]['diameter_1'] = $valx['diameter_1'];
			$ArrBqDetailDetail[$val]['diameter_2'] = $valx['diameter_2'];
			$ArrBqDetailDetail[$val]['length'] = $valx['length'];
			$ArrBqDetailDetail[$val]['thickness'] = $valx['thickness'];
			$ArrBqDetailDetail[$val]['sudut'] = $valx['sudut'];
			$ArrBqDetailDetail[$val]['id_standard'] = $valx['id_standard'];
			$ArrBqDetailDetail[$val]['type'] = $valx['type'];
			$ArrBqDetailDetail[$val]['qty'] = $valx['qty'];
			$ArrBqDetailDetail[$val]['product_ke'] = $valx['product_ke'];
		}
		
		//Duplicate Bq Component Header
		$getBq_CompHeader = $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$id_bq."' AND id_milik IN ".$dtImplode2." ")->result_array();
		$ArrBqCompHeader = array();
		foreach($getBq_CompHeader AS $val => $valx){
			$ArrBqCompHeader[$val]['id_product'] = $valx['id_product'];
			$ArrBqCompHeader[$val]['id_milik'] = $valx['id_milik'];
			$ArrBqCompHeader[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqCompHeader[$val]['parent_product'] = $valx['parent_product'];
			$ArrBqCompHeader[$val]['standart_code'] = $valx['standart_code'];
			$ArrBqCompHeader[$val]['nm_product'] = $valx['nm_product'];
			$ArrBqCompHeader[$val]['series'] = $valx['series'];
			$ArrBqCompHeader[$val]['resin_sistem'] = $valx['resin_sistem'];
			$ArrBqCompHeader[$val]['pressure'] = $valx['pressure'];
			$ArrBqCompHeader[$val]['diameter'] = $valx['diameter'];
			$ArrBqCompHeader[$val]['liner'] = $valx['liner'];
			$ArrBqCompHeader[$val]['aplikasi_product'] = $valx['aplikasi_product'];
			$ArrBqCompHeader[$val]['criminal_barier'] = $valx['criminal_barier'];
			$ArrBqCompHeader[$val]['vacum_rate'] = $valx['vacum_rate'];
			$ArrBqCompHeader[$val]['stiffness'] = $valx['stiffness'];
			$ArrBqCompHeader[$val]['design_life'] = $valx['design_life'];
			$ArrBqCompHeader[$val]['standart_by'] = $valx['standart_by'];
			$ArrBqCompHeader[$val]['standart_toleransi'] = $valx['standart_toleransi'];
			$ArrBqCompHeader[$val]['diameter2'] = $valx['diameter2'];
			$ArrBqCompHeader[$val]['panjang'] = $valx['panjang'];
			$ArrBqCompHeader[$val]['radius'] = $valx['radius'];
			$ArrBqCompHeader[$val]['type_elbow'] = $valx['type_elbow'];
			$ArrBqCompHeader[$val]['angle'] = $valx['angle'];
			$ArrBqCompHeader[$val]['design'] = $valx['design'];
			$ArrBqCompHeader[$val]['est'] = $valx['est'];
			$ArrBqCompHeader[$val]['min_toleransi'] = $valx['min_toleransi'];
			$ArrBqCompHeader[$val]['max_toleransi'] = $valx['max_toleransi'];
			$ArrBqCompHeader[$val]['waste'] = $valx['waste'];
			$ArrBqCompHeader[$val]['area'] = $valx['area'];
			$ArrBqCompHeader[$val]['wrap_length'] = $valx['wrap_length'];
			$ArrBqCompHeader[$val]['wrap_length2'] = $valx['wrap_length2'];
			$ArrBqCompHeader[$val]['high'] = $valx['high'];
			$ArrBqCompHeader[$val]['area2'] = $valx['area2'];
			$ArrBqCompHeader[$val]['panjang_neck_1'] = $valx['panjang_neck_1'];
			$ArrBqCompHeader[$val]['panjang_neck_2'] = $valx['panjang_neck_2'];
			$ArrBqCompHeader[$val]['design_neck_1'] = $valx['design_neck_1'];
			$ArrBqCompHeader[$val]['design_neck_2'] = $valx['design_neck_2'];
			$ArrBqCompHeader[$val]['est_neck_1'] = $valx['est_neck_1'];
			$ArrBqCompHeader[$val]['est_neck_2'] = $valx['est_neck_2'];
			$ArrBqCompHeader[$val]['area_neck_1'] = $valx['area_neck_1'];
			$ArrBqCompHeader[$val]['area_neck_2'] = $valx['area_neck_2'];
			$ArrBqCompHeader[$val]['flange_od'] = $valx['flange_od'];
			$ArrBqCompHeader[$val]['flange_bcd'] = $valx['flange_bcd'];
			$ArrBqCompHeader[$val]['flange_n'] = $valx['flange_n'];
			$ArrBqCompHeader[$val]['flange_oh'] = $valx['flange_oh'];
			$ArrBqCompHeader[$val]['rev'] = $valx['rev'];
			$ArrBqCompHeader[$val]['status'] = $valx['status'];
			$ArrBqCompHeader[$val]['approve_by'] = $valx['approve_by'];
			$ArrBqCompHeader[$val]['approve_date'] = $valx['approve_date'];
			$ArrBqCompHeader[$val]['approve_reason'] = $valx['approve_reason'];
			$ArrBqCompHeader[$val]['sts_price'] = $valx['sts_price'];
			$ArrBqCompHeader[$val]['sts_price_by'] = $valx['sts_price_by'];
			$ArrBqCompHeader[$val]['sts_price_date'] = $valx['sts_price_date'];
			$ArrBqCompHeader[$val]['sts_price_reason'] = $valx['sts_price_reason'];
			$ArrBqCompHeader[$val]['created_by'] = $data_session['ORI_User']['username'];
			$ArrBqCompHeader[$val]['created_date'] = date('Y-m-d H:i:s');
			$ArrBqCompHeader[$val]['deleted'] = $valx['deleted'];
			$ArrBqCompHeader[$val]['deleted_date'] = $valx['deleted_date'];
			$ArrBqCompHeader[$val]['pipe_thickness'] = $valx['pipe_thickness'];
			$ArrBqCompHeader[$val]['joint_thickness'] = $valx['joint_thickness'];
			$ArrBqCompHeader[$val]['factor_thickness'] = $valx['factor_thickness'];
			$ArrBqCompHeader[$val]['factor'] = $valx['factor'];
		}
		
		//Duplicate Bq Component Detail
		$getBq_CompDetail = $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik IN ".$dtImplode2." ")->result_array();
		$ArrBqCompDetail = array();
		foreach($getBq_CompDetail AS $val => $valx){
			$ArrBqCompDetail[$val]['id_milik'] = $valx['id_milik'];
			$ArrBqCompDetail[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqCompDetail[$val]['id_product'] = $valx['id_product'];
			$ArrBqCompDetail[$val]['detail_name'] = $valx['detail_name'];
			$ArrBqCompDetail[$val]['acuhan'] = $valx['acuhan'];
			$ArrBqCompDetail[$val]['id_ori'] = $valx['id_ori'];
			$ArrBqCompDetail[$val]['id_ori2'] = $valx['id_ori2'];
			$ArrBqCompDetail[$val]['id_category'] = $valx['id_category'];
			$ArrBqCompDetail[$val]['nm_category'] = $valx['nm_category'];
			$ArrBqCompDetail[$val]['id_material'] = $valx['id_material'];
			$ArrBqCompDetail[$val]['nm_material'] = $valx['nm_material'];
			$ArrBqCompDetail[$val]['value'] = $valx['value'];
			$ArrBqCompDetail[$val]['thickness'] = $valx['thickness'];
			$ArrBqCompDetail[$val]['fak_pengali'] = $valx['fak_pengali'];
			$ArrBqCompDetail[$val]['bw'] = $valx['bw'];
			$ArrBqCompDetail[$val]['jumlah'] = $valx['jumlah'];
			$ArrBqCompDetail[$val]['layer'] = $valx['layer'];
			$ArrBqCompDetail[$val]['containing'] = $valx['containing'];
			$ArrBqCompDetail[$val]['total_thickness'] = $valx['total_thickness'];
			$ArrBqCompDetail[$val]['last_full'] = $valx['last_full'];
			$ArrBqCompDetail[$val]['last_cost'] = $valx['last_cost'];
			$ArrBqCompDetail[$val]['rev'] = $valx['rev'];
			$ArrBqCompDetail[$val]['area_weight'] = $valx['area_weight'];
			$ArrBqCompDetail[$val]['material_weight'] = $valx['material_weight'];
			$ArrBqCompDetail[$val]['percentage'] = $valx['percentage'];
			$ArrBqCompDetail[$val]['resin_content'] = $valx['resin_content'];
		}
		
		//Duplicate Bq Component Detail Plus
		$getBq_CompDetailPlus = $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik IN ".$dtImplode2." ")->result_array();
		$ArrBqCompDetailPlus = array();
		foreach($getBq_CompDetailPlus AS $val => $valx){
			$ArrBqCompDetailPlus[$val]['id_milik'] = $valx['id_milik'];
			$ArrBqCompDetailPlus[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqCompDetailPlus[$val]['id_product'] = $valx['id_product'];
			$ArrBqCompDetailPlus[$val]['detail_name'] = $valx['detail_name'];
			$ArrBqCompDetailPlus[$val]['id_ori'] = $valx['id_ori'];
			$ArrBqCompDetailPlus[$val]['id_ori2'] = $valx['id_ori2'];
			$ArrBqCompDetailPlus[$val]['id_category'] = $valx['id_category'];
			$ArrBqCompDetailPlus[$val]['nm_category'] = $valx['nm_category'];
			$ArrBqCompDetailPlus[$val]['id_material'] = $valx['id_material'];
			$ArrBqCompDetailPlus[$val]['nm_material'] = $valx['nm_material'];
			$ArrBqCompDetailPlus[$val]['containing'] = $valx['containing'];
			$ArrBqCompDetailPlus[$val]['perse'] = $valx['perse'];
			$ArrBqCompDetailPlus[$val]['last_full'] = $valx['last_full'];
			$ArrBqCompDetailPlus[$val]['last_cost'] = $valx['last_cost'];
			$ArrBqCompDetailPlus[$val]['rev'] = $valx['rev'];
		}
		
		//Duplicate Bq Component Detail Add
		$getBq_CompDetailAdd = $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik IN ".$dtImplode2." ")->result_array();
		$ArrBqCompDetailAdd = array();
		foreach($getBq_CompDetailAdd AS $val => $valx){
			$ArrBqCompDetailAdd[$val]['id_milik'] = $valx['id_milik'];
			$ArrBqCompDetailAdd[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqCompDetailAdd[$val]['id_product'] = $valx['id_product'];
			$ArrBqCompDetailAdd[$val]['detail_name'] = $valx['detail_name'];
			$ArrBqCompDetailAdd[$val]['id_category'] = $valx['id_category'];
			$ArrBqCompDetailAdd[$val]['nm_category'] = $valx['nm_category'];
			$ArrBqCompDetailAdd[$val]['id_material'] = $valx['id_material'];
			$ArrBqCompDetailAdd[$val]['nm_material'] = $valx['nm_material'];
			$ArrBqCompDetailAdd[$val]['containing'] = $valx['containing'];
			$ArrBqCompDetailAdd[$val]['perse'] = $valx['perse'];
			$ArrBqCompDetailAdd[$val]['last_full'] = $valx['last_full'];
			$ArrBqCompDetailAdd[$val]['last_cost'] = $valx['last_cost'];
			$ArrBqCompDetailAdd[$val]['rev'] = $valx['rev'];
		}
		
		//Duplicate Bq Component Detail Lamination
		$getBq_CompDetailLam = $this->db->query("SELECT * FROM bq_component_lamination WHERE id_bq='".$id_bq."' AND id_milik IN ".$dtImplode2." ")->result_array();
		$ArrBqCompDetailLam = array();
		foreach($getBq_CompDetailLam AS $val => $valx){
			$ArrBqCompDetailLam[$val]['id_milik'] = $valx['id_milik'];
			$ArrBqCompDetailLam[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqCompDetailLam[$val]['id_product'] = $valx['id_product'];
			$ArrBqCompDetailLam[$val]['detail_name'] = $valx['detail_name'];
			$ArrBqCompDetailLam[$val]['lapisan'] = $valx['lapisan'];
			$ArrBqCompDetailLam[$val]['std_glass'] = $valx['std_glass'];
			$ArrBqCompDetailLam[$val]['width'] = $valx['width'];
			$ArrBqCompDetailLam[$val]['stage'] = $valx['stage'];
			$ArrBqCompDetailLam[$val]['glass'] = $valx['glass'];
			$ArrBqCompDetailLam[$val]['thickness_1'] = $valx['thickness_1'];
			$ArrBqCompDetailLam[$val]['thickness_2'] = $valx['thickness_2'];
			$ArrBqCompDetailLam[$val]['glass_length'] = $valx['glass_length'];
			$ArrBqCompDetailLam[$val]['weight_veil'] = $valx['weight_veil'];
			$ArrBqCompDetailLam[$val]['weight_csm'] = $valx['weight_csm'];
			$ArrBqCompDetailLam[$val]['weight_wr'] = $valx['weight_wr'];
		}
		
		//Duplicate Bq Component Default
		$getBq_CompDetailDefault = $this->db->query("SELECT * FROM bq_component_default WHERE id_bq='".$id_bq."' AND id_milik IN ".$dtImplode2." ")->result_array();
		$ArrBqCompDetailDefault = array();
		foreach($getBq_CompDetailDefault AS $val => $valx){
			$ArrBqCompDetailDefault[$val]['id_product']				= $valx['id_product'];
			$ArrBqCompDetailDefault[$val]['id_bq']					= $valx['id_bq'];
			$ArrBqCompDetailDefault[$val]['id_milik']				= $valx['id_milik'];
			$ArrBqCompDetailDefault[$val]['product_parent']			= $valx['product_parent'];
			$ArrBqCompDetailDefault[$val]['kd_cust']				= $valx['kd_cust'];
			$ArrBqCompDetailDefault[$val]['customer']				= $valx['customer'];
			$ArrBqCompDetailDefault[$val]['standart_code']			= $valx['standart_code'];
			$ArrBqCompDetailDefault[$val]['diameter']				= $valx['diameter'];
			$ArrBqCompDetailDefault[$val]['diameter2']				= $valx['diameter2'];
			$ArrBqCompDetailDefault[$val]['liner']					= $valx['liner'];
			$ArrBqCompDetailDefault[$val]['pn']						= $valx['pn'];
			$ArrBqCompDetailDefault[$val]['overlap']				= $valx['overlap'];
			$ArrBqCompDetailDefault[$val]['waste']					= $valx['waste'];
			$ArrBqCompDetailDefault[$val]['waste_n1']				= $valx['waste_n1'];
			$ArrBqCompDetailDefault[$val]['waste_n2']				= $valx['waste_n2'];
			$ArrBqCompDetailDefault[$val]['max']					= $valx['max'];
			$ArrBqCompDetailDefault[$val]['min']					= $valx['min'];
			$ArrBqCompDetailDefault[$val]['plastic_film']			= $valx['plastic_film'];
			$ArrBqCompDetailDefault[$val]['lin_resin_veil_a']		= $valx['lin_resin_veil_a'];
			$ArrBqCompDetailDefault[$val]['lin_resin_veil_b']		= $valx['lin_resin_veil_b'];
			$ArrBqCompDetailDefault[$val]['lin_resin_veil']			= $valx['lin_resin_veil'];
			$ArrBqCompDetailDefault[$val]['lin_resin_veil_add_a']	= $valx['lin_resin_veil_add_a'];
			$ArrBqCompDetailDefault[$val]['lin_resin_veil_add_b']	= $valx['lin_resin_veil_add_b'];
			$ArrBqCompDetailDefault[$val]['lin_resin_veil_add']		= $valx['lin_resin_veil_add'];
			$ArrBqCompDetailDefault[$val]['lin_resin_csm_a']		= $valx['lin_resin_csm_a'];
			$ArrBqCompDetailDefault[$val]['lin_resin_csm_b']		= $valx['lin_resin_csm_b'];
			$ArrBqCompDetailDefault[$val]['lin_resin_csm']			= $valx['lin_resin_csm'];
			$ArrBqCompDetailDefault[$val]['lin_resin_csm_add_a']	= $valx['lin_resin_csm_add_a'];
			$ArrBqCompDetailDefault[$val]['lin_resin_csm_add_b']	= $valx['lin_resin_csm_add_b'];
			$ArrBqCompDetailDefault[$val]['lin_resin_csm_add']		= $valx['lin_resin_csm_add'];
			$ArrBqCompDetailDefault[$val]['lin_faktor_veil']		= $valx['lin_faktor_veil'];
			$ArrBqCompDetailDefault[$val]['lin_faktor_veil_add']	= $valx['lin_faktor_veil_add'];
			$ArrBqCompDetailDefault[$val]['lin_faktor_csm']			= $valx['lin_faktor_csm'];
			$ArrBqCompDetailDefault[$val]['lin_faktor_csm_add']		= $valx['lin_faktor_csm_add'];
			$ArrBqCompDetailDefault[$val]['lin_resin']				= $valx['lin_resin'];
			$ArrBqCompDetailDefault[$val]['lin_resin_thickness']	= $valx['lin_resin_thickness'];
			$ArrBqCompDetailDefault[$val]['str_resin_csm_a']		= $valx['str_resin_csm_a'];
			$ArrBqCompDetailDefault[$val]['str_resin_csm_b']		= $valx['str_resin_csm_b'];
			$ArrBqCompDetailDefault[$val]['str_resin_csm']			= $valx['str_resin_csm'];
			$ArrBqCompDetailDefault[$val]['str_resin_csm_add_a']	= $valx['str_resin_csm_add_a'];
			$ArrBqCompDetailDefault[$val]['str_resin_csm_add_b']	= $valx['str_resin_csm_add_b'];
			$ArrBqCompDetailDefault[$val]['str_resin_csm_add']		= $valx['str_resin_csm_add'];
			$ArrBqCompDetailDefault[$val]['str_resin_wr_a']			= $valx['str_resin_wr_a'];
			$ArrBqCompDetailDefault[$val]['str_resin_wr_b']			= $valx['str_resin_wr_b'];
			$ArrBqCompDetailDefault[$val]['str_resin_wr']			= $valx['str_resin_wr'];
			$ArrBqCompDetailDefault[$val]['str_resin_wr_add_a']		= $valx['str_resin_wr_add_a'];
			$ArrBqCompDetailDefault[$val]['str_resin_wr_add_b']		= $valx['str_resin_wr_add_b'];
			$ArrBqCompDetailDefault[$val]['str_resin_wr_add']		= $valx['str_resin_wr_add'];
			$ArrBqCompDetailDefault[$val]['str_resin_rv_a']			= $valx['str_resin_rv_a'];
			$ArrBqCompDetailDefault[$val]['str_resin_rv_b']			= $valx['str_resin_rv_b'];
			$ArrBqCompDetailDefault[$val]['str_resin_rv']			= $valx['str_resin_rv'];
			$ArrBqCompDetailDefault[$val]['str_resin_rv_add_a']		= $valx['str_resin_rv_add_a'];
			$ArrBqCompDetailDefault[$val]['str_resin_rv_add_b']		= $valx['str_resin_rv_add_b'];
			$ArrBqCompDetailDefault[$val]['str_resin_rv_add']		= $valx['str_resin_rv_add'];
			$ArrBqCompDetailDefault[$val]['str_faktor_csm']			= $valx['str_faktor_csm'];
			$ArrBqCompDetailDefault[$val]['str_faktor_csm_add']		= $valx['str_faktor_csm_add'];
			$ArrBqCompDetailDefault[$val]['str_faktor_wr']			= $valx['str_faktor_wr'];
			$ArrBqCompDetailDefault[$val]['str_faktor_wr_add']		= $valx['str_faktor_wr_add'];
			$ArrBqCompDetailDefault[$val]['str_faktor_rv']			= $valx['str_faktor_rv'];
			$ArrBqCompDetailDefault[$val]['str_faktor_rv_bw']		= $valx['str_faktor_rv_bw'];
			$ArrBqCompDetailDefault[$val]['str_faktor_rv_jb']		= $valx['str_faktor_rv_jb'];
			$ArrBqCompDetailDefault[$val]['str_faktor_rv_add']		= $valx['str_faktor_rv_add'];
			$ArrBqCompDetailDefault[$val]['str_faktor_rv_add_bw']	= $valx['str_faktor_rv_add_bw'];
			$ArrBqCompDetailDefault[$val]['str_faktor_rv_add_jb']	= $valx['str_faktor_rv_add_jb'];
			$ArrBqCompDetailDefault[$val]['str_resin']				= $valx['str_resin'];
			$ArrBqCompDetailDefault[$val]['str_resin_thickness']	= $valx['str_resin_thickness'];
			$ArrBqCompDetailDefault[$val]['eks_resin_veil_a']		= $valx['eks_resin_veil_a'];
			$ArrBqCompDetailDefault[$val]['eks_resin_veil_b']		= $valx['eks_resin_veil_b'];
			$ArrBqCompDetailDefault[$val]['eks_resin_veil']			= $valx['eks_resin_veil'];
			$ArrBqCompDetailDefault[$val]['eks_resin_veil_add_a']	= $valx['eks_resin_veil_add_a'];
			$ArrBqCompDetailDefault[$val]['eks_resin_veil_add_b']	= $valx['eks_resin_veil_add_b'];
			$ArrBqCompDetailDefault[$val]['eks_resin_veil_add']		= $valx['eks_resin_veil_add'];
			$ArrBqCompDetailDefault[$val]['eks_resin_csm_a']		= $valx['eks_resin_csm_a'];
			$ArrBqCompDetailDefault[$val]['eks_resin_csm_b']		= $valx['eks_resin_csm_b'];
			$ArrBqCompDetailDefault[$val]['eks_resin_csm']			= $valx['eks_resin_csm'];
			$ArrBqCompDetailDefault[$val]['eks_resin_csm_add_a']	= $valx['eks_resin_csm_add_a'];
			$ArrBqCompDetailDefault[$val]['eks_resin_csm_add_b']	= $valx['eks_resin_csm_add_b'];
			$ArrBqCompDetailDefault[$val]['eks_resin_csm_add']		= $valx['eks_resin_csm_add'];
			$ArrBqCompDetailDefault[$val]['eks_faktor_veil']		= $valx['eks_faktor_veil'];
			$ArrBqCompDetailDefault[$val]['eks_faktor_veil_add']	= $valx['eks_faktor_veil_add'];
			$ArrBqCompDetailDefault[$val]['eks_faktor_csm']			= $valx['eks_faktor_csm'];
			$ArrBqCompDetailDefault[$val]['eks_faktor_csm_add']		= $valx['eks_faktor_csm_add'];
			$ArrBqCompDetailDefault[$val]['eks_resin']				= $valx['eks_resin'];
			$ArrBqCompDetailDefault[$val]['eks_resin_thickness']	= $valx['eks_resin_thickness'];
			$ArrBqCompDetailDefault[$val]['topcoat_resin']			= $valx['topcoat_resin'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_csm_a']		= $valx['str_n1_resin_csm_a'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_csm_b']		= $valx['str_n1_resin_csm_b'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_csm']		= $valx['str_n1_resin_csm'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_csm_add_a']	= $valx['str_n1_resin_csm_add_a'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_csm_add_b']	= $valx['str_n1_resin_csm_add_b'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_csm_add']	= $valx['str_n1_resin_csm_add'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_wr_a']		= $valx['str_n1_resin_wr_a'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_wr_b']		= $valx['str_n1_resin_wr_b'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_wr']		= $valx['str_n1_resin_wr'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_wr_add_a']	= $valx['str_n1_resin_wr_add_a'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_wr_add_b']	= $valx['str_n1_resin_wr_add_b'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_wr_add']	= $valx['str_n1_resin_wr_add'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_rv_a']		= $valx['str_n1_resin_rv_a'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_rv_b']		= $valx['str_n1_resin_rv_b'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_rv']		= $valx['str_n1_resin_rv'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_rv_add_a']	= $valx['str_n1_resin_rv_add_a'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_rv_add_b']	= $valx['str_n1_resin_rv_add_b'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_rv_add']	= $valx['str_n1_resin_rv_add'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_csm']		= $valx['str_n1_faktor_csm'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_csm_add']	= $valx['str_n1_faktor_csm_add'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_wr']		= $valx['str_n1_faktor_wr'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_wr_add']	= $valx['str_n1_faktor_wr_add'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_rv']		= $valx['str_n1_faktor_rv'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_rv_bw']	= $valx['str_n1_faktor_rv_bw'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_rv_jb']	= $valx['str_n1_faktor_rv_jb'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_rv_add']	= $valx['str_n1_faktor_rv_add'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_rv_add_bw']= $valx['str_n1_faktor_rv_add_bw'];
			$ArrBqCompDetailDefault[$val]['str_n1_faktor_rv_add_jb']= $valx['str_n1_faktor_rv_add_jb'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin']			= $valx['str_n1_resin'];
			$ArrBqCompDetailDefault[$val]['str_n1_resin_thickness']	= $valx['str_n1_resin_thickness'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_csm_a']		= $valx['str_n2_resin_csm_a'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_csm_b']		= $valx['str_n2_resin_csm_b'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_csm']		= $valx['str_n2_resin_csm'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_csm_add_a']	= $valx['str_n2_resin_csm_add_a'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_csm_add_b']	= $valx['str_n2_resin_csm_add_b'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_csm_add']	= $valx['str_n2_resin_csm_add'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_wr_a']		= $valx['str_n2_resin_wr_a'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_wr_b']		= $valx['str_n2_resin_wr_b'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_wr']		= $valx['str_n2_resin_wr'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_wr_add_a']	= $valx['str_n2_resin_wr_add_a'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_wr_add_b']	= $valx['str_n2_resin_wr_add_b'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_wr_add']	= $valx['str_n2_resin_wr_add'];
			$ArrBqCompDetailDefault[$val]['str_n2_faktor_csm']		= $valx['str_n2_faktor_csm'];
			$ArrBqCompDetailDefault[$val]['str_n2_faktor_csm_add']	= $valx['str_n2_faktor_csm_add'];
			$ArrBqCompDetailDefault[$val]['str_n2_faktor_wr']		= $valx['str_n2_faktor_wr'];
			$ArrBqCompDetailDefault[$val]['str_n2_faktor_wr_add']	= $valx['str_n2_faktor_wr_add'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin']			= $valx['str_n2_resin'];
			$ArrBqCompDetailDefault[$val]['str_n2_resin_thickness']	= $valx['str_n2_resin_thickness'];
			$ArrBqCompDetailDefault[$val]['created_by']				= $this->session->userdata['ORI_User']['username'];
			$ArrBqCompDetailDefault[$val]['created_date']			= date('Y-m-d H:i:s');
		}
		
		//Duplicate Bq Component Footer
		$getBq_CompFooter = $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_milik IN ".$dtImplode2." ")->result_array();
		$ArrBqCompFooter = array();
		foreach($getBq_CompFooter AS $val => $valx){
			$ArrBqCompFooter[$val]['id_milik'] = $valx['id_milik'];
			$ArrBqCompFooter[$val]['id_bq'] = $valx['id_bq'];
			$ArrBqCompFooter[$val]['id_product'] = $valx['id_product'];
			$ArrBqCompFooter[$val]['detail_name'] = $valx['detail_name'];
			$ArrBqCompFooter[$val]['total'] = $valx['total'];
			$ArrBqCompFooter[$val]['min'] = $valx['min'];
			$ArrBqCompFooter[$val]['max'] = $valx['max'];
			$ArrBqCompFooter[$val]['hasil'] = $valx['hasil'];
			$ArrBqCompFooter[$val]['rev'] = $valx['rev'];
		}
		// print_r($ArrBqCompFooter); 

		$Arr_Edit	= array(
			'status' => "WAITING FINAL DRAWING",
			'quo_reason' => $this->input->post('approve_reason'),
			'quo_by' => $data_session['ORI_User']['username'],
			'quo_date' => date('Y-m-d H:i:s')
		);
		// print_r($ArrBqHeader); 
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $Imp[1]);
			$this->db->update('production', $Arr_Edit);
		
			$this->db->insert('so_header', $ArrBqHeader);
			$this->db->insert_batch('so_detail_header', $ArrBqDetailHeader);
			$this->db->insert_batch('so_detail_detail', $ArrBqDetailDetail);
			$this->db->insert_batch('so_component_header', $ArrBqCompHeader);
			$this->db->insert_batch('so_component_detail', $ArrBqCompDetail);
			$this->db->insert_batch('so_component_detail_plus', $ArrBqCompDetailPlus);
			if(!empty($ArrBqCompDetailAdd)){
				$this->db->insert_batch('so_component_detail_add', $ArrBqCompDetailAdd);
			}
			if(!empty($ArrBqCompDetailLam)){
				$this->db->insert_batch('so_component_lamination', $ArrBqCompDetailLam);
			}
			if(!empty($ArrBqCompDetailDefault)){
				$this->db->insert_batch('so_component_default', $ArrBqCompDetailDefault);
			}
			$this->db->insert_batch('so_component_footer', $ArrBqCompFooter);
			
			
			history('Final Drawing Insert with BQ : '.$id_bq);

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);				
			
		}
		echo json_encode($Arr_Data);
	}
	
	//APPROVE SO
	function insert_sales_order_manual(){
		history('Try manual insert batch sales order');
		$sqlUpdate = "SELECT
					a.*,
					a.no_ipp,
					b.id_customer,
					b.nm_customer,
					b.project,
					b.ref_quo,
					b.status,
					b.sts_price_quo 
					FROM
						so_bf_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp";
		$restUpdate = $this->db->query($sqlUpdate)->result_array();
		$ArrUpdate = array();
		foreach($restUpdate AS $val => $valx){
			$ArrUpdate[$val]['id_bq'] 			= $valx['id_bq'];
			$ArrUpdate[$val]['no_ipp'] 			= $valx['no_ipp'];
			$ArrUpdate[$val]['id_customer'] 	= $valx['id_customer'];
			$ArrUpdate[$val]['nm_customer'] 	= $valx['nm_customer'];
			$ArrUpdate[$val]['project'] 		= $valx['project'];
			$ArrUpdate[$val]['ref_quo'] 		= $valx['ref_quo'];
			$ArrUpdate[$val]['sum_sales_order'] = 0;
			$ArrUpdate[$val]['sum_material_so'] = SUM_SO_MATERIAL_WEIGHT($valx['id_bq']);
			$ArrUpdate[$val]['sum_quotation'] 	= 0;
			$ArrUpdate[$val]['sum_final_drawing'] 	= 0;
			$ArrUpdate[$val]['status'] 			= $valx['status'];
			$ArrUpdate[$val]['sts_price_quo'] 	= $valx['sts_price_quo'];
			$ArrUpdate[$val]['aju_approved'] 	= $valx['aju_approved'];
			$ArrUpdate[$val]['aju_approved_est'] 	= $valx['aju_approved_est'];
			$ArrUpdate[$val]['approved_est'] 	= $valx['approved_est'];
			$ArrUpdate[$val]['approved'] 		= $valx['approved'];
			$ArrUpdate[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrUpdate[$val]['create_date'] 	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			// $this->db->truncate('table_sales_order_manual');
			$this->db->insert_batch('table_sales_order_manual', $ArrUpdate);
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
			history('Success manual insert batch sales order');
		}
		echo json_encode($Arr_Data);
		
	}

	function update_berat_so($no_ipp){

		$dataRevisi 	= $this->db->select('MAX(revised_no) AS nomor_revisi')->get_where('laporan_costing_header',array('id_bq'=>'BQ-'.$no_ipp))->result();
		$revisiNumber 	= $dataRevisi[0]->nomor_revisi;
		$BERAT_MATERIAL = 0;

		$dataArrayRev	= $this->db->get_where('laporan_costing_detail',array('id_bq'=>'BQ-'.$no_ipp, 'revised_no'=>$revisiNumber))->result_array();
		$DataCheckBerat	= array();
		foreach ($dataArrayRev as $key => $value) {
			$DataCheckBerat[$value['id_milik']] = $value['est_material'] / $value['qty'];
		}

		$restUpdate 	= $this->db->get_where('billing_so_product',array('no_ipp'=>$no_ipp))->result_array();
		
		if(!empty($DataCheckBerat) AND !empty($restUpdate)){
			foreach($restUpdate AS $val => $valx){
				$BERAT_MATERIAL += $DataCheckBerat[$valx['id_milik']] * $valx['qty'];
			}
		}

		$ArrUpdate = array(
			'sum_material_so' => $BERAT_MATERIAL
		);
		
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp',$no_ipp);
			$this->db->update('table_sales_order', $ArrUpdate);
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
			history('Update manual berat sales order '.$no_ipp);
		}
		echo json_encode($Arr_Data);
		
	}

	function update_customer(){
		if($this->input->post()){
			$data = $this->input->post();
			$no_ipp = $data['no_ipp'];
			$old_customer = $data['old_customer'];
			$new_customer = $data['new_customer'];
			$new_customer_nama = get_name('customer','nm_customer','id_customer',$new_customer);
			$ArrUpdate = array(
				'id_customer' => $new_customer,
				'nm_customer' => $new_customer_nama,
			);
			// echo 'Change customer '.$no_ipp.',chCust '.$old_customer.' to '.$new_customer.' ('.$new_customer_nama.')';
			// print_r($ArrUpdate);
			// exit;
			$this->db->trans_start();
				$this->db->where('no_ipp',$no_ipp);
				$this->db->update('production', $ArrUpdate);

				$this->db->where('no_ipp',$no_ipp);
				$this->db->update('table_sales_order', $ArrUpdate);
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
				history('Change customer '.$no_ipp.',chCust '.$old_customer.' to '.$new_customer.' ('.$new_customer_nama.')');
			}
			echo json_encode($Arr_Data);
		}
		else{
			$this->load->view('Sales_order/update_customer');
		}
		
	}

}
