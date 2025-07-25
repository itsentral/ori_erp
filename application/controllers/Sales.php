<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('sales_model');
		$this->load->model('quotation_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}
	
	//==========================================================================================================================
	//======================================================= IPP ==============================================================
	//==========================================================================================================================
	
	public function index(){
		$this->sales_model->index_sales();
	}
	
	public function request(){
		$this->sales_model->add_request();
	}
	
	public function add_country(){
		$this->sales_model->add_country();
	}
	
	public function server_side_sales_ipp(){
		$this->sales_model->get_data_json_sales_ipp();
	}
	
	public function detail_ipp(){
		$this->sales_model->detail_ipp();
	}
	
	public function cancel_ipp(){
		$this->sales_model->cancel_ipp();
	}
	
	public function edit_ipp(){
		$this->sales_model->edit_ipp();
	}
	
	public function printIPP(){
		$this->sales_model->print_ipp();
	}
	
	public function ajukan_ipp(){
		$this->sales_model->ajukan_ipp();
	}
	
	public function getFluida(){
		$sqlDel		= "SELECT * FROM list_fluida";
		$restDel	= $this->db->query($sqlDel)->result_array();
		
		$option	= "<option value='0'>Select An Fluida</option>";
		foreach($restDel AS $val => $valx){
			$option .= "<option value='".$valx['id_fluida']."'>".ucwords(strtolower($valx['fluida_name']))."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getColorLin(){
		$sqlDel		= "SELECT * FROM list_color ORDER BY color_name ASC";
		$restDel	= $this->db->query($sqlDel)->result_array();
		
		$option	= "<option value='0'>Color Liner</option>";
		foreach($restDel AS $val => $valx){
			$option .= "<option value='".$valx['color_name']."'>".ucwords(strtolower($valx['color_name']))."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getColorStr(){
		$sqlDel		= "SELECT * FROM list_color ORDER BY color_name ASC";
		$restDel	= $this->db->query($sqlDel)->result_array();
		
		$option	= "<option value='0'>Color Str</option>";
		foreach($restDel AS $val => $valx){
			$option .= "<option value='".$valx['color_name']."'>".ucwords(strtolower($valx['color_name']))."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getColorEks(){
		$sqlDel		= "SELECT * FROM list_color ORDER BY color_name ASC";
		$restDel	= $this->db->query($sqlDel)->result_array();
		
		$option	= "<option value='0'>Color Eks</option>";
		foreach($restDel AS $val => $valx){
			$option .= "<option value='".$valx['color_name']."'>".ucwords(strtolower($valx['color_name']))."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getColorTC(){
		$sqlDel		= "SELECT * FROM list_color ORDER BY color_name ASC";
		$restDel	= $this->db->query($sqlDel)->result_array();
		
		$option	= "<option value='0'>Color TC</option>";
		foreach($restDel AS $val => $valx){
			$option .= "<option value='".$valx['color_name']."'>".ucwords(strtolower($valx['color_name']))."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	//==========================================================================================================================
	//======================================================= END IPP ==========================================================
	//==========================================================================================================================
	
	//==========================================================================================================================
	//===================================================== QUOTATION ==========================================================
	//==========================================================================================================================
	
	public function quotation(){
		$this->quotation_model->index_quotation();
	}
	
	public function server_side_quotation(){
		$this->quotation_model->get_data_json_quotation();
	}
	
	public function modal_detail_quotation(){
		$this->quotation_model->modal_detail_quotation();
	}
	
	public function modal_approve_quotation2(){
		$this->quotation_model->modal_approve_quotation2();
	}
	
	public function modal_view_material(){
		$this->quotation_model->modal_view_material();
	}
	
	public function approve_quotation(){
		$this->quotation_model->approve_quotation();
	}
	
	public function server_side_quotation_app(){
		$this->quotation_model->get_data_json_quotation_app();
	}
	
	public function modal_approve_quotation(){
		$this->quotation_model->modal_approve_quotation();
	}
	
	//==========================================================================================================================
	//================================================= END QUOTATION ==========================================================
	//==========================================================================================================================

	
	public function modalDetailHist(){
		$this->load->view('Sales/modalDetailHist');
	}
	
	public function modalHist(){
		$this->load->view('Sales/modalHist');
	}
	
	public function modalviewDT(){
		$this->load->view('Sales/modalviewDT');
	}
	
	public function modalAppCost(){
		$this->load->view('Sales/modalAppCost');
	}
	
	public function AppCostNew(){
		// echo "Perbaikan";  
		// exit;
		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		$data_session	= $this->session->userdata;
		$stsX			= ($this->input->post('status') == 'Y')?"WAITING PRODUCTION":"WAITING EST PRICE PROJECT";
		$DtQuo	= $this->db->query("SELECT ref_quo FROM production WHERE no_ipp='".$Imp[1]."' ")->result_array();
		
		$sqlNoRev_eng 	= "SELECT revised_no FROM laporan_costing_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
		$restNoRev_eng 	= $this->db->query($sqlNoRev_eng)->result();
		$revisi_eng 	= $restNoRev_eng[0]->revised_no;
		
		$sqlNoRev_cost 	= "SELECT revised_no FROM laporan_revised_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
		$restNoRev_cost = $this->db->query($sqlNoRev_cost)->result();
		$revisi_cost 	= $restNoRev_cost[0]->revised_no;
		
		//IF TO ENGGENERING
		if($this->input->post('status') == 'N'){
			$Arr_Edit2	= array(
				'approved' => 'N',
				'approved_est' => 'N',
				'aju_approved' => 'N',
				'aju_approved_est' => 'N',
				'estimasi' => 'Y',
				'app_quo' 	=> 'N',
				'app_quo_by' 	=> $data_session['ORI_User']['username'],
				'app_quo_date' 	=> date('Y-m-d H:i:s')
			);
			
			$Arr_Edit3	= array(
				'status' => "WAITING STRUCTURE BQ",
				'ref_quo' => $DtQuo[0]['ref_quo'] + 1,
				'quo_reason' => $this->input->post('approve_reason'),
				'quo_by' => $data_session['ORI_User']['username'],
				'quo_date' => date('Y-m-d H:i:s')
			);
			$ArrDetalHdNo	= array(
				'so_sts' 	=> 'N',
				'so_by' 	=> NULL,
				'so_date' 	=> NULL
			);
			
			$ArrRevisi	= array(
				'revisi' 	=> $this->input->post('approve_reason')
			);
		}
		//IF TO COSTING
		if($this->input->post('status') == 'X'){
			$Arr_Edit	= array(
				'status' => 'WAITING EST PRICE PROJECT',
				'ref_quo' => $DtQuo[0]['ref_quo'] + 1,
				'sts_price_reason' => $this->input->post('approve_reason'),
				'sts_price_by' => $data_session['ORI_User']['username'],
				'sts_price_date' => date('Y-m-d H:i:s')
			);
			$ArrDetalHdNo	= array(
				'so_sts' 	=> 'N',
				'so_by' 	=> NULL,
				'so_date' 	=> NULL
			);
			
			$Arr_Edit2	= array(
				'app_quo' 	=> 'N',
				'app_quo_by' 	=> $data_session['ORI_User']['username'],
				'app_quo_date' 	=> date('Y-m-d H:i:s')
			);
			
			$ArrRevisi	= array(
				'revisi' 	=> $this->input->post('approve_reason')
			);
		}
		//IF TO APPROVE
		if($this->input->post('status') == 'Y'){
			$Arr_Edit	= array(
				'status' => "WAITING SALES ORDER",
				'quo_reason' => $this->input->post('approve_reason'),
				'quo_by' => $data_session['ORI_User']['username'],
				'quo_date' => date('Y-m-d H:i:s')
			);
			
			$ArrInsertPro = array(
				'id_produksi' => "PRO-".$Imp[1],
				'no_ipp' => $Imp[1],
				'so_number' => "SO-".$Imp[1],
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			);
			
			$Arr_Edit2	= array(
				'so_sts' 	=> 'Y',
				'so_by' 	=> $data_session['ORI_User']['username'],
				'so_date' 	=> date('Y-m-d H:i:s')
			);
			
			$check = $this->input->post('check');
			$dtListArray = array();
			if(!empty($check)){
				foreach($check AS $val => $valx){
					$dtListArray[$val] = $valx;
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";
			}

			//check material
			$check2 = $this->input->post('check2');
			$dtListArray2 = array();
			if(!empty($check2)){
				foreach($check2 AS $val => $valx){
					$dtListArray2[$val] = $valx;
				}
				$dtImplode2	= "('".implode("','", $dtListArray2)."')";
			}
			// print_r($check);
			// print_r($check2);
			// exit;
			
			if(!empty($check)){
				$qDet_Hd	= "SELECT a.* FROM bq_detail_header a WHERE a.id_bq = '".$id_bq."' AND a.id IN ".$dtImplode."  ";
				$restHd		= $this->db->query($qDet_Hd)->result_array();
				
				$ArrDetalHd = array();
				foreach($restHd AS $val => $valx){
					$ArrDetalHd[$val]['id'] 		= $valx['id'];
					$ArrDetalHd[$val]['so_sts'] 	= 'Y';
					$ArrDetalHd[$val]['so_by'] 	= $data_session['ORI_User']['username'];
					$ArrDetalHd[$val]['so_date'] 	= date('Y-m-d H:i:s');
				}
			}

			if(!empty($check2)){
				$qDet_Hd	= "SELECT a.* FROM bq_acc_and_mat a WHERE a.id_bq = '".$id_bq."' AND a.id IN ".$dtImplode2."  ";
				$restHd		= $this->db->query($qDet_Hd)->result_array();
				
				$ArrDetalHd2 = array();
				foreach($restHd AS $val => $valx){
					$ArrDetalHd2[$val]['id'] 		= $valx['id'];
					$ArrDetalHd2[$val]['so_sts'] 	= 'Y';
					$ArrDetalHd2[$val]['so_by'] 	= $data_session['ORI_User']['username'];
					$ArrDetalHd2[$val]['so_date'] 	= date('Y-m-d H:i:s');
				}
			}

			//Pengurutan IPP
			$ChkSO			= "SELECT so_number FROM so_number WHERE id_bq ='".$id_bq."' ORDER BY id DESC LIMIT 1 ";
			$numrowSO		= $this->db->query($ChkSO)->num_rows();
			$resultSO		= $this->db->query($ChkSO)->result_array();

			$Y = date('y');
			$LE = substr($id_bq, 12,1);
			$CH = substr($id_bq, 6,2);
			if($CH == '19' OR $CH == '20'){
				$LE = substr($id_bq, 11,1);
			}
			$qIPP			= "SELECT MAX(urutan) as maxP FROM so_number WHERE thn='".$Y."'  ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 0, 4); 
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);

			if($numrowSO > 0){
				$IDSO		= $resultSO[0]['so_number'];
			}
			else{
				$IDSO		= "SO".$LE.$Y.$urut2;

				$ArrInsertSONum = array(
					'id_bq' 	=> $id_bq,
					'so_number'	=> $IDSO,
					'wilayah'  	=> $LE,
					'thn'  		=> $Y,
					'urutan'  	=> $urut2,
					'so_by' 	=> $data_session['ORI_User']['username'],
					'so_date' 	=> date('Y-m-d H:i:s')
				);
			}
			
			// echo $IDSO; exit;
			
			//Duplicate Bq Header
			$getBq_Header = $this->db->query("SELECT * FROM bq_header WHERE id_bq='".$id_bq."' ")->result();
			// if(empty($getBq_Header)){
				$ArrBqHeader = array(
					'id_bq' => $getBq_Header[0]->id_bq,
					'no_ipp'  => $getBq_Header[0]->no_ipp,
					'so_number'  => $IDSO,
					'series'  => $getBq_Header[0]->series,
					'order_type'  => $getBq_Header[0]->order_type,
					'ket' => $getBq_Header[0]->ket,
					'estimasi'  => $getBq_Header[0]->estimasi,
					'rev' => $getBq_Header[0]->rev,
					'created_by'  => $data_session['ORI_User']['username'],
					'created_date'  => date('Y-m-d H:i:s'),
					'modified_by' => $getBq_Header[0]->modified_by,
					'modified_date' => $getBq_Header[0]->modified_date,
					'est_by'  => $getBq_Header[0]->est_by,
					'est_date'  => $getBq_Header[0]->est_date,
				);
			// }
			
			$ArrBqDetailHeader = array();
			$ArrBqDetailDetail = array();
			
			if(!empty($check)){
				//Duplicate Bq Detail Header
				$getBq_DetailHeader = $this->db->query("SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id IN ".$dtImplode." ")->result_array();
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
				// echo $dtImplode;
				//Duplicate Bq Detail Detail
				$getBq_DetailDetail = $this->db->query("SELECT * FROM bq_detail_detail WHERE id_bq='".$id_bq."' AND id_bq_header IN ".$dtImplode." LIMIT 0,1000 ")->result_array();
				
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
			}

			//Material
			$Arr_bq_aksesoris = array();
			if(!empty($check2)){
				$get_bq_aksesoris = $this->db->query("SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND id IN ".$dtImplode2." ")->result_array();
				foreach($get_bq_aksesoris AS $val => $valx){
					$Arr_bq_aksesoris[$val]['id_milik'] 	= $valx['id'];
					$Arr_bq_aksesoris[$val]['id_bq'] 		= $valx['id_bq'];
					$Arr_bq_aksesoris[$val]['category'] 	= $valx['category'];
					$Arr_bq_aksesoris[$val]['id_material'] 	= $valx['id_material'];
					$Arr_bq_aksesoris[$val]['qty'] 			= $valx['qty'];
					$Arr_bq_aksesoris[$val]['satuan'] 		= $valx['satuan'];
					$Arr_bq_aksesoris[$val]['note'] 		= $valx['note'];
					$Arr_bq_aksesoris[$val]['unit_price'] 	= $valx['unit_price'];
					$Arr_bq_aksesoris[$val]['total_price'] 	= $valx['total_price'];
					$Arr_bq_aksesoris[$val]['lebar'] 		= $valx['lebar'];
					$Arr_bq_aksesoris[$val]['panjang'] 		= $valx['panjang'];
					$Arr_bq_aksesoris[$val]['berat'] 		= $valx['berat'];
					$Arr_bq_aksesoris[$val]['sheet'] 		= $valx['sheet'];
					$Arr_bq_aksesoris[$val]['updated_by'] 	= $data_session['ORI_User']['username'];
					$Arr_bq_aksesoris[$val]['updated_date'] = date('Y-m-d H:i:s');
				}
			}
			
			// $ArrNonFrp = array();
			// $get_bq_nonfrp = $this->db->query("SELECT * FROM bq_acc_and_mat WHERE category='acc' AND id_bq='".$id_bq."'")->result_array();
			// if(!empty($get_bq_nonfrp)){
				// foreach($get_bq_nonfrp AS $val => $valx){
					// $ArrNonFrp[$val]['id_milik'] 	= $valx['id'];
					// $ArrNonFrp[$val]['id_bq'] 		= $valx['id_bq'];
					// $ArrNonFrp[$val]['category'] 	= $valx['category'];
					// $ArrNonFrp[$val]['id_material'] 	= $valx['id_material'];
					// $ArrNonFrp[$val]['qty'] 			= $valx['qty'];
					// $ArrNonFrp[$val]['satuan'] 		= $valx['satuan'];
					// $ArrNonFrp[$val]['note'] 		= $valx['note'];
					// $ArrNonFrp[$val]['unit_price'] 	= $valx['unit_price'];
					// $ArrNonFrp[$val]['total_price'] 	= $valx['total_price'];
					// $ArrNonFrp[$val]['updated_by'] 	= $data_session['ORI_User']['username'];
					// $ArrNonFrp[$val]['updated_date'] = date('Y-m-d H:i:s');
				// }
			// }
		}
		//insert semua total harga di price
		// print_r($ArrBqDetailHeader);
		// print_r($ArrBqDetailDetail); 
		// print_r($Arr_bq_aksesoris); 
		// exit;
		$this->db->trans_start();
			if($this->input->post('status') == 'N'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit3);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);

				//delete all sales order, because revised quotation
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_detail');
				
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_acc_and_mat');

				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_detail_header', $ArrDetalHdNo);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_acc_and_mat', $ArrDetalHdNo);
				
				if(!empty($restNoRev_eng)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where('revised_no', $revisi_eng);
					$this->db->update('laporan_costing_header', $ArrRevisi);
				}
				
				history('Quotation Revisi to Enggenering with BQ : '.$id_bq);
			}
			
			if($this->input->post('status') == 'X'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);

				//delete all sales order, because revised quotation
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_header');
				
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_detail');
				
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_acc_and_mat');

				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_detail_header', $ArrDetalHdNo);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_acc_and_mat', $ArrDetalHdNo);
				
				if(!empty($restNoRev_eng)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where('revised_no', $revisi_cost);
					$this->db->update('laporan_revised_header', $ArrRevisi);
				}
				
				history('Quotation Revisi to Costing with BQ : '.$id_bq);
			}
			
			if($this->input->post('status') == 'Y'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit);
			
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);
				
				if(!empty($ArrDetalHd)){
					$this->db->update_batch('bq_detail_header', $ArrDetalHd, 'id');
				}

				if(!empty($ArrDetalHd2)){
					$this->db->update_batch('bq_acc_and_mat', $ArrDetalHd2, 'id');
				}
				
				if($numrowSO < 1){
					$this->db->insert('so_number', $ArrInsertSONum);
				}

				// if(empty($getBq_Header)){ Arr_bq_aksesoris
					$this->db->where('id_bq', $id_bq);
					$this->db->delete('so_bf_header');
					
					$this->db->insert('so_bf_header', $ArrBqHeader);
				// }
				
				if(!empty($Arr_bq_aksesoris)){
					$this->db->insert_batch('so_bf_acc_and_mat', $Arr_bq_aksesoris);
				}
				// if(!empty($ArrNonFrp)){
					// $this->db->where('id_bq', $id_bq);
					// $this->db->where('category', 'acc');
					// $this->db->delete('so_bf_acc_and_mat');
					// $this->db->insert_batch('so_bf_acc_and_mat', $ArrNonFrp);
				// }
				if(!empty($ArrBqDetailHeader)){
					$this->db->insert_batch('so_bf_detail_header', $ArrBqDetailHeader);
				}
				if(!empty($ArrBqDetailDetail)){
					$this->db->insert_batch('so_bf_detail_detail', $ArrBqDetailDetail);
				}
				
				history('Quotation Deal with BQ : '.$id_bq);
			}
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
	
	public function AppCostNew_Report(){
		// echo "Perbaikan";  
		// exit;
		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		$data_session	= $this->session->userdata;
		$stsX			= ($this->input->post('status') == 'Y')?"WAITING PRODUCTION":"WAITING EST PRICE PROJECT";
		$DtQuo	= $this->db->query("SELECT ref_quo FROM production WHERE no_ipp='".$Imp[1]."' ")->result_array();
		
		$sqlNoRev_eng 	= "SELECT revised_no FROM laporan_costing_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
		$restNoRev_eng 	= $this->db->query($sqlNoRev_eng)->result();
		$revisi_eng 	= (!empty($restNoRev_eng))?$restNoRev_eng[0]->revised_no:'';
		
		$sqlNoRev_cost 	= "SELECT revised_no FROM laporan_revised_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
		$restNoRev_cost = $this->db->query($sqlNoRev_cost)->result();
		$revisi_cost 	= (!empty($restNoRev_cost))?$restNoRev_cost[0]->revised_no:'';
		
		//IF TO ENGGENERING
		if($this->input->post('status') == 'N'){
			$Arr_Edit2	= array(
				'approved' => 'N',
				'approved_est' => 'N',
				'aju_approved' => 'N',
				'aju_approved_est' => 'N',
				'estimasi' => 'Y',
				'app_quo' 	=> 'N',
				'app_quo_by' 	=> $data_session['ORI_User']['username'],
				'app_quo_date' 	=> date('Y-m-d H:i:s')
			);
			
			$Arr_Edit3	= array(
				'status' => "WAITING STRUCTURE BQ",
				'ref_quo' => $DtQuo[0]['ref_quo'] + 1,
				'quo_reason' => $this->input->post('approve_reason'),
				'quo_by' => $data_session['ORI_User']['username'],
				'quo_date' => date('Y-m-d H:i:s')
			);
			$ArrDetalHdNo	= array(
				'so_sts' 	=> 'N',
				'so_by' 	=> NULL,
				'so_date' 	=> NULL
			);
			$ArrRevisi	= array(
				'revisi' 	=> $this->input->post('approve_reason')
			);
		}
		//IF TO COSTING
		if($this->input->post('status') == 'X'){
			$Arr_Edit	= array(
				'status' => 'WAITING EST PRICE PROJECT',
				'ref_quo' => $DtQuo[0]['ref_quo'] + 1,
				'sts_price_reason' => $this->input->post('approve_reason'),
				'sts_price_by' => $data_session['ORI_User']['username'],
				'sts_price_date' => date('Y-m-d H:i:s')
			);
			$ArrDetalHdNo	= array(
				'so_sts' 	=> 'N',
				'so_by' 	=> NULL,
				'so_date' 	=> NULL
			);
			
			$Arr_Edit2	= array(
				'app_quo' 	=> 'N',
				'app_quo_by' 	=> $data_session['ORI_User']['username'],
				'app_quo_date' 	=> date('Y-m-d H:i:s')
			);
			$ArrRevisi	= array(
				'revisi' 	=> $this->input->post('approve_reason')
			);
		}
		//IF TO APPROVE
		if($this->input->post('status') == 'Y'){
			
			$Arr_Edit2	= array(
				'app_quo' 	=> 'Y',
				'app_quo_by' 	=> $data_session['ORI_User']['username'],
				'app_quo_date' 	=> date('Y-m-d H:i:s')
			);
			
		}
		//insert semua total harga di price
		// print_r($ArrBqDetailHeader);
		// print_r($ArrBqDetailDetail); 
		// print_r($Arr_bq_aksesoris); 
		// exit;
		$this->db->trans_start();
			if($this->input->post('status') == 'N'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit3);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);

				//delete all sales order, because revised quotation
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_detail');
				
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_acc_and_mat');

				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_detail_header', $ArrDetalHdNo);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_acc_and_mat', $ArrDetalHdNo);
				
				if(!empty($restNoRev_eng)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where('revised_no', $revisi_eng);
					$this->db->update('laporan_costing_header', $ArrRevisi);
				}
				
				history('Quotation Revisi to Enggenering (approval) with BQ : '.$id_bq);
			}
			
			if($this->input->post('status') == 'X'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);

				//delete all sales order, because revised quotation
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_header');
				
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_detail');
				
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_acc_and_mat');

				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_detail_header', $ArrDetalHdNo);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_acc_and_mat', $ArrDetalHdNo);
				
				if(!empty($restNoRev_cost)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where('revised_no', $revisi_cost);
					$this->db->update('laporan_revised_header', $ArrRevisi);
				}
				
				history('Quotation Revisi to Costing (approval) with BQ : '.$id_bq);
			}
			
			if($this->input->post('status') == 'Y'){
			
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);
				
				history('Quotation approve with BQ : '.$id_bq);
			}
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
	
	public function AppCostNewX(){
		// echo "Perbaikan";  
		// exit;
		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		$data_session	= $this->session->userdata;
		$stsX			= ($this->input->post('status') == 'Y')?"WAITING PRODUCTION":"WAITING EST PRICE PROJECT";
		$DtQuo	= $this->db->query("SELECT ref_quo FROM production WHERE no_ipp='".$Imp[1]."' ")->result_array();
		
		//IF TO ENGGENERING
		if($this->input->post('status') == 'N'){
			$Arr_Edit2	= array(
				'approved' => 'N',
				'approved_est' => 'N',
				'aju_approved' => 'N',
				'aju_approved_est' => 'N',
				'estimasi' => 'Y'
			);
			
			$Arr_Edit3	= array(
				'status' => "WAITING STRUCTURE BQ",
				'ref_quo' => $DtQuo[0]['ref_quo'] + 1,
				'quo_reason' => $this->input->post('approve_reason'),
				'quo_by' => $data_session['ORI_User']['username'],
				'quo_date' => date('Y-m-d H:i:s')
			);
			$ArrDetalHdNo	= array(
				'so_sts' 	=> 'N',
				'so_by' 	=> NULL,
				'so_date' 	=> NULL
			);
		}
		//IF TO COSTING
		if($this->input->post('status') == 'X'){
			$Arr_Edit	= array(
				'status' => 'WAITING EST PRICE PROJECT',
				'ref_quo' => $DtQuo[0]['ref_quo'] + 1,
				'sts_price_reason' => $this->input->post('approve_reason'),
				'sts_price_by' => $data_session['ORI_User']['username'],
				'sts_price_date' => date('Y-m-d H:i:s')
			);
			$ArrDetalHdNo	= array(
				'so_sts' 	=> 'N',
				'so_by' 	=> NULL,
				'so_date' 	=> NULL
			);
		}
		//IF TO APPROVE
		if($this->input->post('status') == 'Y'){
			$Arr_Edit	= array(
				'status' => "WAITING SALES ORDER",
				'quo_reason' => $this->input->post('approve_reason'),
				'quo_by' => $data_session['ORI_User']['username'],
				'quo_date' => date('Y-m-d H:i:s')
			);
			
			$ArrInsertPro = array(
				'id_produksi' => "PRO-".$Imp[1],
				'no_ipp' => $Imp[1],
				'so_number' => "SO-".$Imp[1],
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			);
			
			$Arr_Edit2	= array(
				'so_sts' 	=> 'Y',
				'so_by' 	=> $data_session['ORI_User']['username'],
				'so_date' 	=> date('Y-m-d H:i:s')
			);
			
			$check = $this->input->post('check');
			$dtListArray = array();
			foreach($check AS $val => $valx){
				$dtListArray[$val] = $valx;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			// print_r($dtImplode);
			// print_r($check);
			// exit;
		
			$qDet_Hd	= "SELECT a.* FROM bq_detail_header a WHERE a.id_bq = '".$id_bq."' AND a.id IN ".$dtImplode."  ";
			$restHd		= $this->db->query($qDet_Hd)->result_array();
			
			$ArrDetalHd = array();
			foreach($restHd AS $val => $valx){
				$ArrDetalHd[$val]['id'] 		= $valx['id'];
				$ArrDetalHd[$val]['so_sts'] 	= 'Y';
				$ArrDetalHd[$val]['so_by'] 	= $data_session['ORI_User']['username'];
				$ArrDetalHd[$val]['so_date'] 	= date('Y-m-d H:i:s');
			}

			//Pengurutan IPP
			$ChkSO			= "SELECT so_number FROM so_number WHERE id_bq ='".$id_bq."' ORDER BY id DESC LIMIT 1 ";
			$numrowSO		= $this->db->query($ChkSO)->num_rows();
			$resultSO		= $this->db->query($ChkSO)->result_array();

			$Y = date('y');
			$LE = substr($id_bq, 12,1);
			if($Y == '20' OR $Y == '19'){
				$LE = substr($id_bq, 11,1);
			}
			$qIPP			= "SELECT MAX(urutan) as maxP FROM so_number WHERE thn='".$Y."'  ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 0, 4); 
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);

			if($numrowSO > 0){
				$IDSO		= $resultSO[0]['so_number'];
			}
			else{
				$IDSO		= "SO".$LE.$Y.$urut2;

				$ArrInsertSONum = array(
					'id_bq' 	=> $id_bq,
					'so_number'	=> $IDSO,
					'wilayah'  	=> $LE,
					'thn'  		=> $Y,
					'urutan'  	=> $urut2,
					'so_by' 	=> $data_session['ORI_User']['username'],
					'so_date' 	=> date('Y-m-d H:i:s')
				);
			}
			
			// echo $IDSO; exit;
			
			//Duplicate Bq Header
			$getBq_Header = $this->db->query("SELECT * FROM bq_header WHERE id_bq='".$id_bq."' ")->result();
			// if(empty($getBq_Header)){
				$ArrBqHeader = array(
					'id_bq' => $getBq_Header[0]->id_bq,
					'no_ipp'  => $getBq_Header[0]->no_ipp,
					'so_number'  => $IDSO,
					'series'  => $getBq_Header[0]->series,
					'order_type'  => $getBq_Header[0]->order_type,
					'ket' => $getBq_Header[0]->ket,
					'estimasi'  => $getBq_Header[0]->estimasi,
					'rev' => $getBq_Header[0]->rev,
					'created_by'  => $data_session['ORI_User']['username'],
					'created_date'  => date('Y-m-d H:i:s'),
					'modified_by' => $getBq_Header[0]->modified_by,
					'modified_date' => $getBq_Header[0]->modified_date,
					'est_by'  => $getBq_Header[0]->est_by,
					'est_date'  => $getBq_Header[0]->est_date,
				);
			// }
			
			//Duplicate Bq Detail Header
			$getBq_DetailHeader = $this->db->query("SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id IN ".$dtImplode." ")->result_array();
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
			// echo $dtImplode;
			//Duplicate Bq Detail Detail
			$getBq_DetailDetail = $this->db->query("SELECT * FROM bq_detail_detail WHERE id_bq='".$id_bq."' AND id_bq_header IN ".$dtImplode." LIMIT 0,1000 ")->result_array();
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
		
		}
		//insert semua total harga di price
		// print_r($ArrBqDetailHeader);
		// print_r($ArrBqDetailDetail); 
		// exit;
		$this->db->trans_start();
			if($this->input->post('status') == 'N'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit3);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);

				//delete all sales order, because revised quotation
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_detail');

				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_detail_header', $ArrDetalHdNo);
				
				history('Quotation Revisi to Enggenering with BQ : '.$id_bq);
			}
			
			if($this->input->post('status') == 'X'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit);

				//delete all sales order, because revised quotation
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_header');

				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_header');
				
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_detail');

				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_detail_header', $ArrDetalHdNo);
				
				history('Quotation Revisi to Costing with BQ : '.$id_bq);
			}
			
			if($this->input->post('status') == 'Y'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit);
			
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit2);
				
				$this->db->update_batch('bq_detail_header', $ArrDetalHd, 'id');
				
				if($numrowSO < 1){
					$this->db->insert('so_number', $ArrInsertSONum);
				}

				// if(empty($getBq_Header)){
					$this->db->where('id_bq', $id_bq);
					$this->db->delete('so_bf_header');
					
					$this->db->insert('so_bf_header', $ArrBqHeader);
				// }
				$this->db->insert_batch('so_bf_detail_header', $ArrBqDetailHeader);
				$this->db->insert_batch('so_bf_detail_detail', $ArrBqDetailDetail);
				
				history('Quotation Deal with BQ : '.$id_bq);
			}
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

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	



	public function edit() {
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');
			
			$paret_product	= $data['parent_product'];
			$diameter		= $data['value_d'];
			$id				= $data['id'];
			
			//Pencarian data yang sudah ada 
			$ValueProduct	= "SELECT * FROM product WHERE parent_product='".$paret_product."' AND value_d='".$diameter."' LIMIT 1";
			$NumProduct		= $this->db->query($ValueProduct)->num_rows();
			
			// echo $ValueProduct."<br>";
			// echo $NumProduct;
			
			if($NumProduct > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Diameter of the product already exists. Please input different ...'
				);
			}
			else{
				$Arr_Update			= array(
					'nm_product'		=> ucfirst(strtolower($data['nm_product'])),
					'parent_product'	=> $data['parent_product'],
					'value_d'			=> $data['value_d'],
					'ket'				=> $data['ket'],
					'modified_by'		=> $this->session->userdata['ORI_User']['username'],
					'modified_date'		=> date('Y-m-d H:i:s')
				);
				
				// echo "<pre>"; print_r($Arr_Update);
				// exit;
			
				$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('product', $Arr_Update);
				$this->db->trans_complete();
				
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Edit type product data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Edit type product data success. Thanks ...',
						'status'	=> 1
					);
					history('Edit Type Product kode: '.$id);
				}	
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			
			$id = $this->uri->segment(3);
			
			$qProduct		= "SELECT * FROM product WHERE id='".$id."' LIMIT 1 ";
			$dataProduct	= $this->db->query($qProduct)->result_array();
			
			$dataType		= "SELECT * FROM product_parent ORDER BY product_parent ASC";
			$restType		= $this->db->query($dataType)->result_array();
			$data = array(
				'title'		=> 'Add So',
				'action'	=> 'add',
				'data'		=> $dataProduct,
				'type'		=> $restType
			);
			
			$this->load->view('So/edit',$data);
		}
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
