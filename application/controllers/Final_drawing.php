<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Final_drawing extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('final_drawing_bq_model');
		$this->load->model('final_drawing_est_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){ 
			redirect('login');
		}
	}
	
	//============================================================================================================
	//========================================FINAL DRAWING BQ====================================================
	//============================================================================================================
	
	//INDEX FINAL DRAWING
	public function index(){
		$this->final_drawing_bq_model->index();
	}
	
	public function server_side_fd_bq(){
		$this->final_drawing_bq_model->get_data_json_fd_est();
	}
	
	public function modal_detail_bq(){
		$this->final_drawing_bq_model->modal_detail_bq();
	}
	
	public function modal_edit_bq(){
		$this->final_drawing_bq_model->modal_edit_bq();
	}
	
	public function delete_sebagian_bq(){
		$this->final_drawing_bq_model->delete_sebagian_bq();
	}
	
	public function update_bq(){
		$this->final_drawing_bq_model->update_bq();
	}
	
	public function ajukan_bq(){
		$this->final_drawing_bq_model->ajukan_bq();
	}
	
	
	//============================================================================================================
	//========================================FINAL DRAWING ESTIMASI==============================================
	//============================================================================================================
	
	public function fd_estimasi(){
		$this->final_drawing_est_model->index_estimasi();
	}
	
	public function server_side_fd_est(){
		$this->final_drawing_est_model->get_data_json_fd_est();
	}
	
	public function view_data(){
		$this->final_drawing_est_model->view_data();
	}
	
	public function modal_detail_material(){
		$this->final_drawing_est_model->modal_detail_material();
	}
	
	public function modal_est_bq(){
		$this->final_drawing_est_model->modal_est_bq();
	}
	
	public function update_est_get_last(){
		$this->final_drawing_est_model->update_est_get_last();
	}
	
	public function update_est_get_master(){
		$this->final_drawing_est_model->update_est_get_master();
	}
	
	public function update_mat_acc(){
		$this->final_drawing_est_model->update_mat_acc();
	}
	
	public function modal_detail_product_est(){
		$this->final_drawing_est_model->modal_detail_product_est();
	}
	
	public function update_satuan_est_master(){
		$this->final_drawing_est_model->update_satuan_est_master();
	}
	
	public function update_satuan_est_bq(){
		$this->final_drawing_est_model->update_satuan_est_bq();
	}
	
	public function back_to_fd_est_bq(){
		$this->final_drawing_est_model->back_to_fd_est_bq();
	}
	
	public function ajukan_fd_parsial(){
		$this->final_drawing_est_model->ajukan_fd_parsial();
	}
	
	public function ajukan_satuan_product(){
		$this->final_drawing_est_model->ajukan_satuan_product();
	}
	
	public function ajukan_all_product(){
		$this->final_drawing_est_model->ajukan_all_product();
	}
	
	public function ajukan_all_material(){
		$this->final_drawing_est_model->ajukan_all_material();
	}
	
	public function ajukan_all_acc(){
		$this->final_drawing_est_model->ajukan_all_acc();
	}
	
	public function ajukan_satuan_material(){
		$this->final_drawing_est_model->ajukan_satuan_material();
	}
	
	// public function update_resin(){
	// 	$this->final_drawing_est_model->update_resin();
	// }

	public function update_resin(){
		$data  		= $this->input->post();
		$resin 		= $this->uri->segment(3);
		$tanda 		= $this->uri->segment(4);
		$id_milik 	= $this->uri->segment(5);
		$beda 	= $data['pembeda'];
		$id_bq 	= $data['id_bq'];
		$category_id 	= $data['category_id'];

		$data_session	= $this->session->userdata;
		$username = $data_session['ORI_User']['username'];

		$check = $this->input->post('check2');
		$dtListArray = array();
		foreach($check AS $val => $valx){
			$dtListArray[$val] = $valx;
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";
		$dtImplodeHist	= implode("','", $dtListArray);

		$qListResin = "SELECT id_material, nm_material FROM raw_materials WHERE id_material='".$resin."' LIMIT 1 ";
		$dataResin	= $this->db->query($qListResin)->result();
		$resinNew	= $dataResin[0]->nm_material;

		if($tanda == 'liner'){
			$layer = "AND (detail_name = 'LINER THIKNESS / CB')";
			$table = "so_component_detail";
			$table2 = "so_component_detail_plus";
		}
		if($tanda == 'str'){
			$layer = "AND (detail_name = 'STRUKTUR THICKNESS' OR detail_name = 'STRUKTUR NECK 1' OR detail_name = 'STRUKTUR NECK 2')";
			$table = "so_component_detail";
			$table2 = "so_component_detail_plus";
		}
		if($tanda == 'eks'){
			$layer = "AND (detail_name = 'EXTERNAL LAYER THICKNESS')";
			$table = "so_component_detail";
			$table2 = "so_component_detail_plus";
		}
		if($tanda == 'tc'){
			$layer = "AND (detail_name = 'TOPCOAT')";
			$table = "so_component_detail_plus";
		}
		
		$WHERE_ID_MILIK = "";
		if(!empty($this->input->post('check2'))){
			$WHERE_ID_MILIK = " AND id_milik IN ".$dtImplode." ";
		}

		if($tanda == 'liner' OR $tanda == 'str' OR $tanda == 'eks'){
			$sqlUpdate 	= "SELECT * FROM ".$table." WHERE id_bq='".$id_bq."' AND id_category = '".$category_id."' ".$layer." ".$WHERE_ID_MILIK." ";
			$sqlUpdate2 	= "SELECT * FROM ".$table2." WHERE id_bq='".$id_bq."' AND id_category = '".$category_id."' ".$layer." ".$WHERE_ID_MILIK." ";
		}
		if($tanda == 'tc'){
			$sqlUpdate 	= "SELECT * FROM ".$table." WHERE id_bq='".$id_bq."' AND id_category = '".$category_id."' ".$layer." ".$WHERE_ID_MILIK." ";
		}

		$restUpdate = $this->db->query($sqlUpdate)->result_array();
		$ArrUpdate3 	= array();
		if($tanda == 'liner' OR $tanda == 'str' OR $tanda == 'eks'){
			$restUpdate3 = $this->db->query($sqlUpdate2)->result_array();
			
			foreach($restUpdate3 AS $val => $valx){
				$ArrUpdate3[$val]['id_detail'] 	= $valx['id_detail'];
				$ArrUpdate3[$val]['id_material'] = $resin;
				$ArrUpdate3[$val]['nm_material'] = $resinNew;
				$ArrUpdate3[$val]['price_mat'] 	= get_price_ref($resin);
			}
		}

		$ArrUpdate 	= array();
		foreach($restUpdate AS $val => $valx){
			$ArrUpdate[$val]['id_detail'] 	= $valx['id_detail'];
			$ArrUpdate[$val]['id_material'] = $resin;
			$ArrUpdate[$val]['nm_material'] = $resinNew;
			$ArrUpdate[$val]['price_mat'] 	= get_price_ref($resin);
		}

		
		//Update Joint
		$sqlv = "";
		if($category_id == 'TYP-0001'){
			if($tanda == 'liner'){
				// selain resin topcoat
				$sqlv_tc = "SELECT id_detail AS id_detail FROM help_update_joint_tc_so WHERE id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND (nm_category='RESIN INSIDE' OR nm_category='RESIN CARBOSIL')";
				$ListBQipp		= $this->db->query($sqlv_tc)->result_array();

				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['id_detail'];
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

				$sqlv = "SELECT id_detail FROM so_component_detail WHERE detail_name = 'RESIN AND ADD' AND id_category = '".$category_id."' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND id_detail IN ".$dtImplode." ";
			}
			if($tanda == 'str'){
				// selain resin topcoat
				$sqlv_tc = "SELECT id_detail AS id_detail FROM help_update_joint_tc_so WHERE id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND (nm_category='RESIN OUTSIDE' OR nm_category='RESIN')";
				$ListBQipp		= $this->db->query($sqlv_tc)->result_array();

				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['id_detail'];
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

				$sqlv = "SELECT id_detail FROM so_component_detail WHERE detail_name = 'RESIN AND ADD' AND id_category = '".$category_id."' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND id_detail IN ".$dtImplode." ";
			}
			if($tanda == 'tc'){
				//resin top topcoat
				$sqlv = "SELECT id_detail AS id_detail FROM help_update_joint_tc_so WHERE nm_category='RESIN TOPCOAT' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." GROUP BY id_milik";
			}
		}
		else{
			if($tanda == 'str'){
				$sqlv = "SELECT id_detail FROM so_component_detail WHERE id_category = '".$category_id."' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." ";
			} 
		}
		// echo $sqlv; exit; 
		$ArrUpdate2 	= array();
		if($tanda == 'str' OR $tanda == 'tc' OR $tanda == 'liner'){
			if($sqlv != ""){
				$restUpdateJoint = $this->db->query($sqlv)->result_array();
				foreach($restUpdateJoint AS $val => $valx){
					$ArrUpdate2[$val]['id_detail'] 		= $valx['id_detail'];
					$ArrUpdate2[$val]['id_material'] 	= $resin;
					$ArrUpdate2[$val]['nm_material'] 	= $resinNew;
					$ArrUpdate2[$val]['price_mat'] 		= get_price_ref($resin);
				}
			}
		}

		// print_r($ArrUpdate);
		// print_r($ArrUpdate3);
		// print_r($ArrUpdate2);
		// exit; 
		$NameType = str_replace('-','',$category_id);
		$ArrInsertChange = [
			'no_ipp' => $id_bq,
			'id_material_before' => $data[$NameType],
			'id_material_after' => $resin,
			'layer' => $tanda,
			'type' => $category_id,
			'id_milik' => $dtImplodeHist,
			'change_by ' => $username,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch($table, $ArrUpdate, 'id_detail');
			}
			if(!empty($ArrUpdate3)){
				$this->db->update_batch($table2, $ArrUpdate3, 'id_detail');
			}
			if(!empty($ArrUpdate2)){
				$this->db->update_batch("so_component_detail", $ArrUpdate2, 'id_detail');
			}

			if(!empty($ArrInsertChange)){
				$this->db->insert('change_material_hist',$ArrInsertChange);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update failed. Please try again later ...',
				'status'	=> 0,
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $beda
			);
		}
		else{
			$this->db->trans_commit();

			//TAMPILAN BARU
			$sqlResin = "(SELECT id_material, nm_material, id_category  FROM so_component_detail WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)
			 UNION
			(SELECT id_material, nm_material, id_category  FROM so_component_detail_plus WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)";
			$ListBQipp		= $this->db->query($sqlResin)->result_array();
			$dtListArrayResin = array();
			$dtListArrayVeil = array();
			$dtListArrayCsm = array();
			$dtListArrayWR = array();
			$dtListArrayRooving = array();
			$dtListArrayCatalys = array();
			$dtListArrayPigment = array();
			foreach($ListBQipp AS $val => $valx){
				if($valx['id_category'] == 'TYP-0001'){
					$dtListArrayResin[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0003'){
					$dtListArrayVeil[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0004'){
					$dtListArrayCsm[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0006'){
					$dtListArrayWR[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0005'){
					$dtListArrayRooving[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0002'){
					$dtListArrayCatalys[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0007'){
					$dtListArrayPigment[$val] = $valx['nm_material'];
				}
			}
			$dtImplodeResin	= "".implode("  ---  ", $dtListArrayResin)."";
			$dtImplodeVeil	= "".implode("  ---  ", $dtListArrayVeil)."";
			$dtImplodeCsm	= "".implode("  ---  ", $dtListArrayCsm)."";
			$dtImplodeWR	= "".implode("  ---  ", $dtListArrayWR)."";
			$dtImplodeRooving	= "".implode("  ---  ", $dtListArrayRooving)."";
			$dtImplodeCatalys	= "".implode("  ---  ", $dtListArrayCatalys)."";
			$dtImplodePigment	= "".implode("  ---  ", $dtListArrayPigment)."";

			$Arr_Data	= array(
				'pesan'		=>'Update success. Thanks ...',
				'status'	=> 1,
				'id_bqx'	=> $id_bq,
				'jumlah_resin'	=> COUNT($dtListArrayResin),
				'nama_resin'	=> $dtImplodeResin,
				'jumlah_veil'	=> COUNT($dtListArrayVeil),
				'nama_veil'	=> $dtImplodeVeil,
				'jumlah_csm'	=> COUNT($dtListArrayCsm),
				'nama_csm'	=> $dtImplodeCsm,
				'jumlah_wr'	=> COUNT($dtListArrayWR),
				'nama_wr'	=> $dtImplodeWR,
				'jumlah_rooving'	=> COUNT($dtListArrayRooving),
				'nama_rooving'	=> $dtImplodeRooving,
				'jumlah_catalys'	=> COUNT($dtListArrayCatalys),
				'nama_catalys'	=> $dtImplodeCatalys,
				'jumlah_pigment'	=> COUNT($dtListArrayPigment),
				'nama_pigment'	=> $dtImplodePigment,
				'pembeda'	=> $beda
			);
			history("Update all resin layer ".$tanda." / ".$id_bq." / ".$resin);
		}
		echo json_encode($Arr_Data);
	}
	
	public function close_parsial(){
		$this->final_drawing_est_model->close_parsial();
	}
	
	//============================================================================================================
	//=============================================LIST PRODUKSI==================================================
	//============================================================================================================
	
	public function produksi(){
		$this->final_drawing_est_model->produksi();
	}
	
	public function server_side_fd_est_produksi(){
		$this->final_drawing_est_model->get_data_json_fd_est_produksi();
	}
	
	
	
	
	
	
	public function getDeliveryMX(){
		$dataL 		= $this->input->post('series');
		$id_bq 		= $this->input->post('id_bq');
		$sqlSup		= "SELECT id_delivery FROM so_detail_header WHERE id_bq = '".$id_bq."' GROUP BY id_delivery";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option>Iso M</option>";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['id_delivery']."'>".strtoupper($val['id_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getSubDeliveryMX(){
		$dataL = $this->input->post('series');
		$dataM = $this->input->post('id_delivery');
		$id_bq = $this->input->post('id_bq');

		$sqlSup		= "SELECT sub_delivery FROM so_detail_header WHERE id_bq = '".$id_bq."'  GROUP BY sub_delivery";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['sub_delivery']."'>".strtoupper($val['sub_delivery'])."</option>";
		}
		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getStandard(){
		$sqlSup		= "SELECT * FROM list_standard ORDER BY urut ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Standard</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_standard']."'>".strtoupper($valx['nm_standard'])."</option>";
		}
		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getTypeProduct(){
		$sqlSup		= "SELECT * FROM product_parent ORDER BY product_parent ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Type Product</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>";
		}
		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getSeries(){
		$sqlSup		= "SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Series</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['kode_group']."'>".strtoupper($valx['kode_group'])."</option>";
		}
		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function modalviewSO(){
		$id_bq = $this->uri->segment(3);

		history('View Result Sales Order in Approve FD BQ: '.$id_bq);
		
		$this->load->view('FinalDrawing/modalViewSO');
	}
	
	public function modalviewFD(){
		$id_bq = $this->uri->segment(3);

		history('View Result Final Drawing in Approve FD BQ: '.$id_bq);
		
		$this->load->view('FinalDrawing/modalViewFD');
	}
	
	//APPROVE FINAL Drawing
	public function approve(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT `user_id`, created FROM histories WHERE `description`='Success insert final drawing table' ORDER BY created DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		$data = array(
			'title'			=> 'Approved Estimasi Final Drawing',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View Data Approved Est Final Drawing');
		$this->load->view('FinalDrawing/approve',$data);
	}
	
	public function modalDetailKomponent(){
		$this->load->view('FinalDrawing/modalDetail');
	}
	
	public function ajukanAppFD(){
		$id_bq 		= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'aju_approved_est' => 'Y',
			'aju_approved_est_by' => $data_session['ORI_User']['username'],
			'aju_approved_est_date' => date('Y-m-d H:i:s')
		);
		
		$Arr_Edit2	= array(
			'status' => 'WAITING APPROVE FINAL DRAWING'
		);
		// print_r($ArrDetalHd); 
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $Imp[1]);
			$this->db->update('production', $Arr_Edit2);
				
			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $Arr_Edit);
			
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
			history('Mengajukan Final Drawing : '.$id_bq);
			
		}
		echo json_encode($Arr_Data);
	}
	
	public function getDataJSONAppFD(){ 
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approve";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONAppFD(
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		foreach($query->result_array() as $row)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'desc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['rev']))."</span></div>";
			$nestedData[]	= "<div align='right' style='margin-right:15px;'>".number_format($row['sum_sales_order'],2)."</div>";
			$nestedData[]	= "<div align='right' style='margin-right:15px;'>".number_format($row['sum_final_drawing'],2)."</div>";
			
				$warna = Color_status($row['status']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['status']."</span></div>";
					$ApprvX	= "";
					$ApprvX_new	= "";
					$Print	= "";
					if($row['status'] == 'WAITING APPROVE FINAL DRAWING' OR $row['status'] == 'PARTIAL PROCESS'){
						if($Arr_Akses['approve']=='1'){
							// $ApprvX	= "&nbsp;<button type='button' class='btn btn-sm btn-success AppFinalDrawing' title='Approve Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
							$ApprvX_new	= "<button type='button' class='btn btn-sm btn-success AppFinalDrawingNew' title='Approve Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						
						}
					}
					$Print	= "&nbsp;<button type='button' class='btn btn-sm btn-info download_excel' title='Download Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-file-excel-o'></i></button>";
					$det_so = "";
					$det_fd = "";
					
					// $det_so = "<button type='button' class='btn btn-sm btn-warning detailSO' title='Detail Sales Order' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					// $det_fd = "&nbsp;<button type='button' class='btn btn-sm btn-primary detailFD' title='Detail Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";

			$nestedData[]	= "<div align='left'>
									".$det_so."
									".$det_fd."
									".$ApprvX."
									".$ApprvX_new."
									".$Print."
									</div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function queryDataJSONAppFD($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				table_final_drawing a,
				(SELECT @row:=0) r
		   WHERE 1=1 AND a.aju_approved_est = 'Y' AND a.approved_est = 'N'
			AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'nm_customer',
			3 => 'project'
		);

		$sql .= " ORDER BY a.no_ipp DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modalAppFD(){
		$this->load->view('FinalDrawing/approve_modal');
	}
	
	public function modalAppFD_new($id_bq=null){
		$no_ipp = str_replace('BQ-','',$id_bq);
		$QUERY_PACKING	= "	SELECT
								a.option_type AS jenis,
								a.caregory_sub AS category,
								a.price_total AS price_total,
								b.total_deal_usd AS price_deal
							FROM 
								cost_project_detail a
								LEFT JOIN billing_so_add b ON a.id=b.id_milik
							WHERE
								a.category = 'packing'
								AND a.id_bq='$id_bq'
								AND a.price_total != 0 
							ORDER BY 
								a.id ASC
							";
		$getPackCost	= $this->db->query($QUERY_PACKING)->result_array();

		$QUERY_EXPORT 	= "	SELECT
							b.caregory_sub AS category,
							b.price_total AS price_total,
							b.qty,
							b.fumigasi,
							d.total_deal_usd AS price_deal 
						FROM 
							cost_project_detail b
							LEFT JOIN billing_so_add d ON b.id=d.id_milik
						WHERE 
							b.category = 'export' 
							AND b.id_bq='$id_bq' 
							AND b.option_type='Y' 
							AND b.price_total != 0 
						ORDER BY 
							b.id ASC ";
		$getTruck	= $this->db->query($QUERY_EXPORT)->result_array();

		$QUERY_LOCAL 	= "	SELECT
							b.caregory_sub AS category,
							b.price_total AS price_total,
							b.qty,
							b.area,
							b.tujuan,
							c.nama_truck,
							b.kendaraan,
							d.total_deal_usd AS price_deal
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
							LEFT JOIN billing_so_add d ON b.id=d.id_milik
						WHERE
							b.category = 'lokal' 
							AND b.id_bq = '$id_bq' 
							AND b.price_total <> 0
						ORDER BY
							b.id ASC ";
		$getLocal	= $this->db->query($QUERY_LOCAL)->result_array();

		$QUERY_ENG 		= "	SELECT 
								a.total_deal_usd AS price_deal,
								a.qty,
								a.satuan,
								b.caregory_sub AS name_test,
								b.price_total AS price_total
							FROM 
								billing_so_add a 
								INNER JOIN cost_project_detail b ON a.id_milik=b.id 
							WHERE 
								a.category = 'eng' 
								AND b.category = 'engine' 
								AND b.id_bq='$id_bq' 
								AND a.qty > 0
							ORDER BY 
								a.id ASC";
		$getEngCost	= $this->db->query($QUERY_ENG)->result_array();

		$QUERY_MATERIAL 	= "	SELECT 
									a.total_deal_usd AS price_deal,
									a.qty,
									a.satuan,
									a.nm_material
								FROM 
									billing_so_add a
								WHERE 
									a.category='mat'
									AND a.no_ipp='$no_ipp'";
		$getMaterial		= $this->db->query($QUERY_MATERIAL)->result_array();

		$QUERY_NONFRP 	= "	SELECT
								a.total_deal_usd AS price_deal,
								a.qty,
								a.satuan,
								a.id_material
							FROM
								billing_so_add a
							WHERE
								(a.category='baut' OR a.category='plate' OR a.category='gasket' OR a.category='lainnya')
								AND a.no_ipp='$no_ipp'";
		$getNonFRP	= $this->db->query($QUERY_NONFRP)->result_array();

		$QYERY_PRODUCT 		= "	SELECT
									a.*,
									c.id AS id_milik2
								FROM
									billing_so_product a
									LEFT JOIN so_bf_detail_header b ON a.id_milik = b.id_milik
									LEFT JOIN so_detail_header c ON b.id = c.id_milik
								WHERE
									a.no_ipp='$no_ipp'
									AND a.product <> 'product kosong'";					
		$getProduct	= $this->db->query($QYERY_PRODUCT)->result_array();

		//FINAL DRAWING
		$sql_material2 	= "	SELECT 
								b.id AS id2,
								b.qty AS qty_fd,
								b.id_bq AS id_bq2,
								b.id_material,
								c.qty AS qty_deal,
								c.total_deal_usd AS deal_price
							FROM 
								so_acc_and_mat b
								LEFT JOIN billing_so_add c ON b.id_milik = c.id_milik
							WHERE 
								b.category='mat'
								-- AND c.category='mat'
								AND b.approve <> 'N'
								AND b.id_bq='$id_bq'";
		$material2		= $this->db->query($sql_material2)->result_array();

		$sql_non_frp2 	= "	SELECT 
								b.id AS id2,
								b.qty AS qty_fd,
								b.id_bq AS id_bq2,
								b.id_material,
								b.satuan,
								c.qty AS qty_deal,
								c.total_deal_usd AS deal_price
							FROM 
								so_acc_and_mat b
								LEFT JOIN billing_so_add c ON b.id_milik = c.id_milik
							WHERE 
								b.approve <> 'N'
								AND b.id_bq='$id_bq'
								AND (b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								-- AND (c.category='baut' OR c.category='plate' OR c.category='gasket' OR c.category='lainnya')
								";
							// echo $sql_non_frp2;
		$non_frp2		= $this->db->query($sql_non_frp2)->result_array();

		$QUERY_PRODUCT2 = "	SELECT 
								a.id,
								a.id_bq,
								a.series,
								a.diameter_1,
								a.diameter_2,
								a.id_category AS product,
								a.qty AS qty_fd,
								c.qty AS qty_deal,
								c.total_deal_usd AS deal_price
							FROM 
								so_detail_header a
								LEFT JOIN so_bf_detail_header b ON a.id_milik = b.id
								LEFT JOIN billing_so_product c ON b.id_milik = c.id_milik
							WHERE 
								a.id_bq='$id_bq'";	
		// echo $QUERY_PRODUCT2;				
		$getDetail_FD	= $this->db->query($QUERY_PRODUCT2)->result_array();

		//CHECK
		$checkSO 	= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' AND (`status`='WAITING APPROVE FINAL DRAWING' OR `status`='PARTIAL PROCESS') ";
		$restChkSO	= $this->db->query($checkSO)->num_rows();

		$sqlCheckY 	= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."' AND (approve = 'Y')";
		$numCheckY 	= $this->db->query($sqlCheckY)->num_rows();

		$resultHistory = $this->db->select('a.*, b.type AS typeMaterial')->order_by('a.change_date','ASC')->join('change_material_help b','a.type=b.type_material','left')->get_where('change_material_hist a',array('a.no_ipp'=>$id_bq))->result_array();
		$data = [
			'id_bq' 		=> $id_bq,
			'no_ipp' 		=> $no_ipp,
			'getDetail_FD' 	=> $getDetail_FD,
			'non_frp2' 		=> $non_frp2,
			'material2' 	=> $material2,
			'getDetail' 	=> $getProduct,
			'non_frp' 		=> $getNonFRP,
			'material' 		=> $getMaterial,
			'getEngCost' 	=> $getEngCost,
			'getPackCost' 	=> $getPackCost,
			'getVia' 		=> $getLocal,
			'getTruck' 		=> $getTruck,
			'restChkSO' 	=> $restChkSO,
			'resultHistory' => $resultHistory,
			'numCheckY' 	=> $numCheckY,
			'GET_MATERIAL' => get_detail_material(),
			'GET_PRODUCT' => get_detail_final_drawing()
		];
		$this->load->view('FinalDrawing/approve_modal_new',$data);
	}
	
	public function AppBQFDEstNew(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$status		= $this->input->post('status');
		$reason		= $this->input->post('approve_reason');
		
		$Imp			= explode('-', $id_bq);
		
		$checkPro 	= "SELECT * FROM production_header WHERE no_ipp='".$Imp[1]."'";
		$numPro 	= $this->db->query($checkPro)->num_rows();
		// echo $numPro; exit;
		if($numPro < 1){
			if($status == 'Y'){
				$Arr_Edit	= array(
					'approved_est' 		=> 'Y',
					'approved_est_by' 	=> $data_session['ORI_User']['username'],
					'approved_est_date' => date('Y-m-d H:i:s')
				);
				
				$Arr_Edit2	= array(
					'status' => 'WAITING PRODUCTION',
					'quo_reason' => $reason,
					'quo_by' => $data_session['ORI_User']['username'],
					'quo_date' => date('Y-m-d H:i:s')
				);
				$HistReason	= 'Approve Final Drawing with BQ : '.$id_bq;
				
				//save to production
				$ArrInsertPro = array(
					'id_produksi' => "PRO-".$Imp[1],
					'no_ipp' => $Imp[1],
					'jalur' => 'FD',
					'so_number' => "SO-".$Imp[1],
					'created_by' => $data_session['ORI_User']['username'],
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$qDet_Gt	= "SELECT a.*, b.id AS id_milik , b.id_product AS id_product FROM so_detail_detail a INNER JOIN so_detail_header b ON a.id_bq_header = b.id_bq_header  WHERE a.id_bq = '".$id_bq."' ";
				$restBq		= $this->db->query($qDet_Gt)->result_array();
				 
				$ArrDetalPro = array();
				foreach($restBq AS $val => $valx){
					$ArrDetalPro[$val]['id_milik'] 		= $valx['id_milik']; 
					$ArrDetalPro[$val]['id_produksi'] 	= "PRO-".$Imp[1];
					$ArrDetalPro[$val]['id_delivery'] 	= $valx['id_delivery'];
					$ArrDetalPro[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$ArrDetalPro[$val]['sub_delivery'] 	= $valx['sub_delivery'];
					$ArrDetalPro[$val]['id_category'] 	= $valx['id_category'];
					$ArrDetalPro[$val]['id_product'] 	= $valx['id_product'];
					$ArrDetalPro[$val]['product_ke'] 	= $valx['product_ke'];
					$ArrDetalPro[$val]['qty'] 			= $valx['qty'];
				}
			}
			
			if($status == 'N'){
				$Arr_Edit	= array(
					'reason_approved_est'	=> $reason,
					'aju_approved_est' 		=> 'N',
					'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
					'aju_approved_est_date' => date('Y-m-d H:i:s')
				);
				
				$Arr_Edit2	= array(
					'status' => 'WAITING FINAL DRAWING'
				);
				$HistReason	= 'Reject Final Drawing To Est with BQ : '.$id_bq;
			}
			
			if($status == 'M'){
				$Arr_Edit	= array(
					'reason_approved_est'	=> $reason,
					'aju_approved_est' 		=> 'N',
					'aju_approved' 			=> 'N',
					'approved_est' 			=> 'N',
					'approved' 				=> 'N',
					'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
					'aju_approved_est_date' => date('Y-m-d H:i:s')
				);
				
				$Arr_Edit2	= array(
					'status' => 'WAITING FINAL DRAWING'
				);
				$HistReason	= 'Reject Final Drawing To Bq with BQ : '.$id_bq;
			}
			// print_r($Arr_Edit);
			// exit;
			$this->db->trans_start();
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit2);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('so_header', $Arr_Edit);
				
				if($status == 'Y'){
					$this->db->insert('production_header', $ArrInsertPro);
					$this->db->insert_batch('production_detail', $ArrDetalPro);
				}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Approve process failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Approve process success. Thanks ...',
					'status'	=> 1
				);				
				history($HistReason);
			}
		}
		else{
			$Arr_Data	= array(
				'pesan'		=>'Data already produced. Please refresh page ...',
				'status'	=> 0
			);	
		}
		echo json_encode($Arr_Data);
	}

	public function AppBQFDMatPlan(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$status		= $this->input->post('status');
		$reason		= $this->input->post('approve_reason');
		
		$Imp			= explode('-', $id_bq);
		
		$checkPro 	= "SELECT * FROM production_header WHERE no_ipp='".$Imp[1]."'";
		$numPro 	= $this->db->query($checkPro)->num_rows();
		// echo $numPro; exit;
		if($numPro < 1){
			if($status == 'Y'){
				$Arr_Edit	= array(
					'approved_est' 		=> 'Y',
					'approved_est_by' 	=> $data_session['ORI_User']['username'],
					'approved_est_date' => date('Y-m-d H:i:s')
				);
				
				$Arr_Edit2	= array(
					// 'status' => 'WAITING MATERIAL PLANNING',
					'status' => 'WAITING PRODUCTION',
					'quo_reason' => $reason,
					'quo_by' => $data_session['ORI_User']['username'],
					'quo_date' => date('Y-m-d H:i:s')
					// 'mp' => 'Y',
					// 'mp_by' => $data_session['ORI_User']['username'],
					// 'mp_date' => date('Y-m-d H:i:s')
				);
				$HistReason	= 'Approve Final Drawing (in material planning) with BQ : '.$id_bq;
				
				//save to production
				$ArrInsertPro = array(
					'id_produksi' => "PRO-".$Imp[1],
					'no_ipp' => $Imp[1],
					'jalur' => 'FD',
					'so_number' => "SO-".$Imp[1],
					'created_by' => $data_session['ORI_User']['username'],
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$qDet_Gt	= "SELECT a.*, b.id AS id_milik , b.id_product AS id_product FROM so_detail_detail a INNER JOIN so_detail_header b ON a.id_bq_header = b.id_bq_header  WHERE a.id_bq = '".$id_bq."' ";
				$restBq		= $this->db->query($qDet_Gt)->result_array();
				 
				$ArrDetalPro = array();
				foreach($restBq AS $val => $valx){
					$ArrDetalPro[$val]['id_milik'] 		= $valx['id_milik']; 
					$ArrDetalPro[$val]['id_produksi'] 	= "PRO-".$Imp[1];
					$ArrDetalPro[$val]['id_delivery'] 	= $valx['id_delivery'];
					$ArrDetalPro[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$ArrDetalPro[$val]['sub_delivery'] 	= $valx['sub_delivery'];
					$ArrDetalPro[$val]['id_category'] 	= $valx['id_category'];
					$ArrDetalPro[$val]['id_product'] 	= $valx['id_product'];
					$ArrDetalPro[$val]['product_ke'] 	= $valx['product_ke'];
					$ArrDetalPro[$val]['qty'] 			= $valx['qty'];
				}
			}
			
			if($status == 'N'){
				$Arr_Edit	= array(
					'reason_approved_est'	=> $reason,
					'aju_approved_est' 		=> 'N',
					'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
					'aju_approved_est_date' => date('Y-m-d H:i:s')
				);
				
				$Arr_Edit2	= array(
					'status' => 'WAITING FINAL DRAWING'
				);
				// $HistReason	= 'Reject Final Drawing To Est with BQ : '.$id_bq;
			}
			
			if($status == 'M'){
				$Arr_Edit	= array(
					'reason_approved_est'	=> $reason,
					'aju_approved_est' 		=> 'N',
					'aju_approved' 			=> 'N',
					'approved_est' 			=> 'N',
					'approved' 				=> 'N',
					'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
					'aju_approved_est_date' => date('Y-m-d H:i:s')
				);
				
				$Arr_Edit2	= array(
					'status' => 'WAITING FINAL DRAWING'
				);
				$HistReason	= 'Reject Final Drawing To Bq with BQ : '.$id_bq;
			}
			// print_r($Arr_Edit);
			// exit;
			$this->db->trans_start();
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit2);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('so_header', $Arr_Edit);
				
				if($status == 'Y'){
					$this->db->insert('production_header', $ArrInsertPro);
					$this->db->insert_batch('production_detail', $ArrDetalPro);
				}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Approve process failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Approve process success. Thanks ...',
					'status'	=> 1
				);				
				history($HistReason);
			}
		}
		else{
			$Arr_Data	= array(
				'pesan'		=>'Data already produced. Please refresh page ...',
				'status'	=> 0
			);	
		}
		echo json_encode($Arr_Data);
	}
	
	public function modalFD_pipe(){
		history('View edit product estimasi in final drawing pipe');
		$this->load->view('Machine_modal_fd/pipe');
	}

	public function modalFD_end_cap(){
		history('View edit product estimasi in final drawing end cap');
		$this->load->view('Machine_modal_fd/end_cap');
	}

	public function modalFD_blindflange(){
		history('View edit product estimasi in final drawing blind flange');
		$this->load->view('Machine_modal_fd/blind_flange');
	}

	public function modalFD_pipeslongsong(){
		history('View edit product estimasi in final drawing pipe slongsong');
		$this->load->view('Machine_modal_fd/pipe_slongsong');
	}

	public function modalFD_elbowmould(){
		history('View edit product estimasi in final drawing elbow mould');
		$this->load->view('Machine_modal_fd/elbow_mould');
	}

	public function modalFD_elbowmitter(){
		history('View edit product estimasi in final drawing elbow mitter');
		$this->load->view('Machine_modal_fd/elbow_mitter');
	}

	public function modalFD_eccentric_reducer(){
		history('View edit product estimasi in final drawing eccentric reducer');
		$this->load->view('Machine_modal_fd/eccentric_reducer');
	}

	public function modalFD_concentric_reducer(){
		history('View edit product estimasi in final drawing concentric reducer');
		$this->load->view('Machine_modal_fd/concentric_reducer');
	}

	public function modalFD_equal_tee_mould(){
		history('View edit product estimasi in final drawing equal tee mould');
		$this->load->view('Machine_modal_fd/equal_tee_mould');
	}

	public function modalFD_reducer_tee_mould(){
		history('View edit product estimasi in final drawing reducer tee mould');
		$this->load->view('Machine_modal_fd/reducer_tee_mould');
	}

	public function modalFD_equal_tee_slongsong(){
		history('View edit product estimasi in final drawing equal tee slongsong');
		$this->load->view('Machine_modal_fd/equal_tee_slongsong');
	}

	public function modalFD_reducer_tee_slongsong(){
		history('View edit product estimasi in final drawing reducer tee slongsong');
		$this->load->view('Machine_modal_fd/reducer_tee_slongsong');
	}

	public function modalFD_flange_mould(){
		history('View edit product estimasi in final drawing flange mould');
		$this->load->view('Machine_modal_fd/flange_mould');
	}
	
	public function modalFD_colar(){
		history('View edit product estimasi in final drawing colar');
		$this->load->view('Machine_modal_fd/colar');
	}
	
	public function modalFD_colar_slongsong(){
		history('View edit product estimasi in final drawing colar slongsong');
		$this->load->view('Machine_modal_fd/colar_slongsong');
	}

	public function modalFD_flange_slongsong(){
		history('View edit product estimasi in final drawing flange slongsong');
		$this->load->view('Machine_modal_fd/flange_slongsong');
	}

	public function modalFD_field_joint(){
		history('View edit product estimasi in final drawing field joint');
		$this->load->view('Machine_modal_fd/field_joint');
	}
	
	public function getMaterialx(){
		$id_material	= $this->input->post('id_material');
		$diameter 		= $this->input->post('diameter');
		$resin			= $this->input->post('resin');
		$id_category	= $this->input->post('id_ori');
		$resinOri		= $this->input->post('resinOri');

		// echo $id_material;
		// exit;

		//TYPE RELEASE AGENT $id_category == 'TYP-0030'
		if($id_category == 'TYP-0008'){
			$nm_standard 	= 'thickness';
			$sqlMaterial	= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='".$nm_standard."' LIMIT 1 ";
			$restMicron		= $this->db->query($sqlMaterial)->result();
			if(!empty($restMicron[0]->nilai_standard)){
				if($diameter < 40){$micron	= 0;}
				else{
					if($diameter < 400){$micron	= $restMicron[0]->nilai_standard/1000000;}
					else{$micron	= $restMicron[0]->nilai_standard/1000000;}
				}
			}
			else{$micron = 0;}

			$ArrJson	= array(
				'weight' => $micron
			);
		}
		//TYPE VEIL
		if($id_category == 'TYP-0003' OR $id_category == 'TYP-0004' OR $id_category == 'TYP-0001'){
			$nm_standard 	= 'area weight';

			$sqlMaterial	= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='".$nm_standard."' LIMIT 1 ";
			$restMicron		= $this->db->query($sqlMaterial)->result();
			$NumMic			= $this->db->query($sqlMaterial)->num_rows();

			$micron			= 0;
			$thickness		= 0;
			if($NumMic != 0){
				$micron		=  $restMicron[0]->nilai_standard;
				$thickness	= ($micron/1000/2.56)+($micron/1000/1.2*$resin);
			}

			$resin = $resinOri;
			$LayerR	= "";
			if($id_material == 'MTL-1903000'){
				$resin = "MTL-1903000";
				$LayerR	= 0;
			}

			//resinAkhir
			$sqlResin	= $this->db->query("SELECT id_category FROM raw_materials WHERE id_material ='".$id_material."' LIMIT 1 ")->result();

			$resinAkhir	= "N";
			if($sqlResin[0]->id_category == 'TYP-0001'){
				$resinAkhir	= "Y";
			}

			$ArrJson		= array(
				'weight' 	=> $micron,
				'thickness'	=> $thickness,
				'resin'		=> $resin,
				'resinUt'	=> $id_material,
				'layer'		=> $LayerR,
				'resinAk'	=> $resinAkhir
			);
		}

		if($id_category == 'TYP-0005'){

			$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
			$restMicron		= $this->db->query($sqlMicron)->result_array();
			$NumMic			= $this->db->query($sqlMicron)->num_rows();

			// echo $sqlMicron."<br>";

			$weight			= 0;
			$bw				= 0;
			$jumRoov		= 0;
			$thickness		= 0;
			if($NumMic != 0){
				$weight		=  floatval($restMicron[0]['nilai_standard']);
				if($weight != 0 OR $weight != null OR $weight != ''){
					// $bw			= floatval(($weight >= '2200')?'160':(($weight < '2000')?'100':'0'));
					// $jumRoov	= floatval(($weight >= '2200')?'54':(($weight < '2000')?'52':'0'));
					$bw			= floatval($this->input->post('bw'));
					$jumRoov		= floatval($this->input->post('jumlah')); 
					if($bw != 0){
						$thickness	= (($weight/1000)/ $bw * $jumRoov * (2 / 2.56)) + (($weight/1000)/ $bw * $jumRoov * (2 / 1.2) * $resin);
					}
				}
			}

			// echo $weight;
			$resinX = "";
			$LayerR	= "";
			if($id_material == 'MTL-1903000'){
				$resinX = 'MTL-1903000';
				$LayerR	= 0;
			}

			//resinAkhir
			$sqlResin	= $this->db->query("SELECT id_category FROM raw_materials WHERE id_material ='".$id_material."' LIMIT 1 ")->result();

			$resinAkhir	= "N";
			if($sqlResin[0]->id_category == 'TYP-0001'){
				$resinAkhir	= "Y";
			}

			$ArrJson		= array(
				'weight' 	=> $weight,
				'bw' 		=> $bw,
				'jumRoov' 	=> $jumRoov,
				'resin'		=> $resinX,
				'thickness'	=> $thickness,
				'layer'		=> $LayerR,
				'resinUt'	=> $id_material,
				'resinAk'	=> $resinAkhir
			);
		}

		if($id_category == 'TYP-0006'){

			$sqlWrW			= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
			$restWrW		= $this->db->query($sqlWrW)->result_array();
			$NumWrW			= $this->db->query($sqlWrW)->num_rows();

			$sqlWrT			= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='thickness' LIMIT 1 ";
			$restWrT		= $this->db->query($sqlWrT)->result_array();
			$NumWrT			= $this->db->query($sqlWrT)->num_rows();

			$weight			= 0;
			$thickness		= 0;
			if($NumWrW != 0){
				$weight		=  $restWrW[0]['nilai_standard'];
			}
			if($NumWrT != 0){
				$thickness	=  $restWrT[0]['nilai_standard'];
			}

			$resinX = "";
			$LayerR	= "";
			if($id_material == 'MTL-1903000'){
				$resinX = 'MTL-1903000';
				$LayerR	= 0;
			}

			//resinAkhir
			$sqlResin	= $this->db->query("SELECT id_category FROM raw_materials WHERE id_material ='".$id_material."' LIMIT 1 ")->result();

			$resinAkhir	= "N";
			if($sqlResin[0]->id_category == 'TYP-0001'){
				$resinAkhir	= "Y";
			}

			$ArrJson		= array(
				'weight' 	=> $weight,
				'resin'		=> $resinX,
				'thickness'	=> $thickness,
				'layer'		=> $LayerR,
				'resinUt'	=> $id_material,
				'resinAk'	=> $resinAkhir
			);
		}

		echo json_encode($ArrJson);
	}
	
	public function modalEditEstDefault(){
		$this->load->view('Machine_modal_fd/modalEditEstDefault');
	}
	
	
	
	function insert_final_drawing(){
		history('Try insert batch final drawing approve');
		$sqlUpdate = "SELECT
					a.*,
					a.no_ipp,
					b.id_customer,
					b.nm_customer,
					b.project,
					b.ref_quo,
					b.`status`,
					b.sts_price_quo
					FROM
						so_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp WHERE (b.`status`='WAITING APPROVE FINAL DRAWING' OR b.`status`='PARTIAL PROCESS')";
		$restUpdate = $this->db->query($sqlUpdate)->result_array();
		$ArrUpdate = array();
		foreach($restUpdate AS $val => $valx){ 
			$ArrUpdate[$val]['id_bq'] 			= $valx['id_bq'];
			$ArrUpdate[$val]['no_ipp'] 			= $valx['no_ipp'];
			$ArrUpdate[$val]['id_customer'] 	= $valx['id_customer'];
			$ArrUpdate[$val]['nm_customer'] 	= $valx['nm_customer'];
			$ArrUpdate[$val]['project'] 		= $valx['project'];
			$ArrUpdate[$val]['ref_quo'] 		= $valx['ref_quo'];
			$ArrUpdate[$val]['sum_sales_order'] = SUM_SO_ALL_FAST($valx['id_bq']);
			// $ArrUpdate[$val]['sum_quotation'] 	= SUM_QUO_ALL($valx['id_bq']);
			// $ArrUpdate[$val]['sum_final_drawing'] 	= SUM_FD_ALL($valx['id_bq']);
			
			
			
			// $ArrUpdate[$val]['sum_sales_order'] = 0;
			$ArrUpdate[$val]['sum_quotation'] 	= 0;
			// $ArrUpdate[$val]['sum_final_drawing'] 	= 0;
			$ArrUpdate[$val]['status'] 			= $valx['status'];
			$ArrUpdate[$val]['rev'] 			= $valx['rev'];
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
			$this->db->truncate('table_final_drawing'); 
			if(!empty($ArrUpdate)){
				$this->db->insert_batch('table_final_drawing', $ArrUpdate);
			}
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
			history('Success insert final drawing table');
		}
		echo json_encode($Arr_Data);
		
	}
	
	//excel final drawing
	public function ExcelBudgetSo(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(24);
		$sheet->setCellValue('A'.$Row, 'DETAIL BUDGET FINAL DRAWING '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'SO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Project');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Liner');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'PN');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spesifikasi');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Est Mat (Kg)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'Est Mat');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'Direct Labour');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Machine');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Mould Mandrill');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'Consumable');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'Consumable FOH');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'Depresiasi FOH');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue('Q'.$NewRow, 'Gaji Non Produksi');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'Biaya Admin');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'Biaya Bulanan');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sheet->setCellValue('T'.$NewRow, 'Profit');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
		$sheet->getColumnDimension('T')->setAutoSize(true);
		
		$sheet->setCellValue('U'.$NewRow, 'Allowance');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setAutoSize(true);
		
		$sheet->setCellValue('V'.$NewRow, 'Packing');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow);
		$sheet->getColumnDimension('V')->setAutoSize(true);
		
		$sheet->setCellValue('W'.$NewRow, 'Enggenering');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow);
		$sheet->getColumnDimension('W')->setAutoSize(true);
		
		$sheet->setCellValue('X'.$NewRow, 'Trucking');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow);
		$sheet->getColumnDimension('X')->setAutoSize(true);
		
		// $qMatr 		= SQL_SO($id_bq);
		$qMatr 		= SQL_FD($id_bq);
		// echo $qMatr;exit;
		$restDetail1= $this->db->query($qMatr)->result_array(); 
		
		$SQLbGsO 	= "SELECT * FROM budget_so WHERE id_bq='".$id_bq."'";
		$rESTbgSO	= $this->db->query($SQLbGsO)->result_array();
		// echo $qMatr; exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			$SUM = 0;
			$SumEstHarga = 0;
			$SumTot2 = 0;
			$HPP_Tot = 0;
			
			$EstMatKg = 0;
			$EstMat = 0;
			
			$Direct = 0;
			$Indirect = 0;
			$Machi = 0;
			$MouldM = 0;
			$Consumab = 0;
			
			$ConsFOH = 0;
			$DepFOH = 0;
			$GjNonP = 0;
			$ByAdmin = 0;
			$ByBulanan = 0;
			
			$Profits = 0;
			$Allowancex = 0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				if(check_fd($row_Cek['id']) != 'N'){
					$awal_row++;
					$awal_col	= 0;
				
					$EstMatKg += $row_Cek['sum_mat2'];
					
					$Direct += $row_Cek['direct_labour'];
					$Indirect += $row_Cek['indirect_labour'];
					$Machi += $row_Cek['machine'];
					$MouldM += $row_Cek['mould_mandrill'];
					$Consumab += $row_Cek['consumable'];
					
					$ConsFOH += $row_Cek['foh_consumable'];
					$DepFOH += $row_Cek['foh_depresiasi'];
					$GjNonP += $row_Cek['biaya_gaji_non_produksi'];
					$ByAdmin += $row_Cek['biaya_non_produksi'];
					$ByBulanan += $row_Cek['biaya_rutin_bulanan'];
					
					
					// $getProfit = $this->db->query("SELECT profit FROM cost_profit WHERE diameter='".str_replace('.','',$row_Cek['diameter_1'])."' AND diameter2='".str_replace('.','',$row_Cek['diameter_2'])."' AND product_parent='".$row_Cek['id_category']."' ")->result_array();
					// $est_harga = (($row_Cek['est_harga2']+$row_Cek['cost_process']+$row_Cek['foh_consumable']+$row_Cek['foh_depresiasi']+$row_Cek['biaya_gaji_non_produksi']+$row_Cek['biaya_non_produksi']+$row_Cek['biaya_rutin_bulanan'])) / $row_Cek['qty'];
					$est_harga = (($row_Cek['est_harga2']+$row_Cek['direct_labour']+$row_Cek['indirect_labour']+$row_Cek['machine']+$row_Cek['mould_mandrill']+$row_Cek['consumable']+$row_Cek['foh_consumable']+$row_Cek['foh_depresiasi']+$row_Cek['biaya_gaji_non_produksi']+$row_Cek['biaya_non_produksi']+$row_Cek['biaya_rutin_bulanan'])) / $row_Cek['qty'];
					

					$profit = (!empty($row_Cek['persen']))?$row_Cek['persen']:30;
					$EstMat += $row_Cek['est_harga2'];
					
					$helpProfit = $est_harga *($profit/100);

					
					$HrgTot   = (($est_harga) + ($helpProfit)) * $row_Cek['qty'];
					$HPP_Tot += $est_harga * $row_Cek['qty'];
					$SumTot2 += $HrgTot;

					$allow 		= (!empty($row_Cek['extra']))?$row_Cek['extra']:15;
					
					$HrgTot2  = (($HrgTot) + ($HrgTot * ($allow/100)));
					$SumEstHarga = $est_harga * $row_Cek['qty'];
					
					// $HrgTot2   	= (($est_harga) + ($est_harga * ($profit/100))) * $valx['qty'];
					// $HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($allow/100)));
					
					$SUM	 += (($HrgTot) + ($HrgTot * ($allow/100)));
					
					//cek project
					$sqlP = $this->db->query("SELECT project FROM production WHERE no_ipp='".str_replace('BQ-', '', $id_bq)."'")->result_array();
					
					$Profits += $HrgTot - $SumEstHarga;
					$Allowancex += $HrgTot2 - $HrgTot;
					
					
					$awal_col++;
					$nomorx		= $no;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $nomorx);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
					
					$awal_col++;
					$id_bqx		= $id_bq;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $id_bqx);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
					
					$awal_col++;
					$project	= $sqlP[0]['project'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $project);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
					
					$awal_col++;
					$id_category	= $row_Cek['id_category'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $id_category);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
					
					$awal_col++;
					$liner	= $row_Cek['liner'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $liner);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$pressure	= $row_Cek['pressure'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $pressure);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$spesifik	= spec_fd($row_Cek['id'], 'so_detail_header');
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $spesifik);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$sum_mat2	= $row_Cek['sum_mat2'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $sum_mat2);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$est_harga2	= $row_Cek['est_harga2'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $est_harga2);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$direct_labour	= $row_Cek['direct_labour'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $direct_labour);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$indirect_labour	= $row_Cek['indirect_labour'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$machine	= $row_Cek['machine'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $machine);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$mould_mandrill	= $row_Cek['mould_mandrill'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$consumable	= $row_Cek['consumable'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $consumable);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$foh_consumable	= $row_Cek['foh_consumable'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$foh_depresiasi	= $row_Cek['foh_depresiasi'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					// $profit	= $est_harga ; 
					$profit	= $HrgTot - $SumEstHarga ;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $profit);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$allowance	= $HrgTot2 - $HrgTot;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $allowance);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$packing	= '-';
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $packing);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$enggenering	= '-';
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $enggenering);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
					
					$awal_col++;
					$trucking	= '-';
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $trucking);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				}
			}
			// echo $no;exit;
			$Colsw = floatval($no) +6;
			
			// echo $Colsw."-".$Colse; exit;
			
			$sheet->setCellValue("A".$Colsw."", 'SUM');
			$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$Colsw.":G".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			
			$sheet->setCellValue("H".$Colsw."", $EstMatKg);
			$sheet->getStyle("H".$Colsw.":H".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("H".$Colsw.":H".$Colsw."");
			$sheet->getColumnDimension('H')->setAutoSize(true);
			
			$sheet->setCellValue("I".$Colsw."", $EstMat);
			$sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("I".$Colsw.":I".$Colsw."");
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue("J".$Colsw."", $Direct );
			$sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("J".$Colsw.":J".$Colsw."");
			$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue("K".$Colsw."", $Indirect);
			$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
			$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue("L".$Colsw."", $Machi);
			$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
			$sheet->getColumnDimension('L')->setAutoSize(true);
			
			$sheet->setCellValue("M".$Colsw."", $MouldM);
			$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$Colsw."", $Consumab);
			$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$Colsw."", $ConsFOH);
			$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$Colsw."", $DepFOH);
			$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$Colsw."", $GjNonP);
			$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$Colsw."", $ByAdmin);
			$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$Colsw."", $ByBulanan);
			$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
			
			$sheet->setCellValue("T".$Colsw."", $Profits);
			$sheet->getStyle("T".$Colsw.":T".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("T".$Colsw.":T".$Colsw."");
			$sheet->getColumnDimension('T')->setAutoSize(true);
			
			$sheet->setCellValue("U".$Colsw."", $Allowancex);
			$sheet->getStyle("U".$Colsw.":U".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("U".$Colsw.":U".$Colsw."");
			$sheet->getColumnDimension('U')->setAutoSize(true);
			
			$sheet->setCellValue("V".$Colsw."", $rESTbgSO[0]['packing']);
			$sheet->getStyle("V".$Colsw.":V".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("V".$Colsw.":V".$Colsw."");
			$sheet->getColumnDimension('V')->setAutoSize(true);
			
			$sheet->setCellValue("W".$Colsw."", $rESTbgSO[0]['enggenering']);
			$sheet->getStyle("W".$Colsw.":W".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("W".$Colsw.":W".$Colsw."");
			$sheet->getColumnDimension('W')->setAutoSize(true);
			
			$sheet->setCellValue("X".$Colsw."", $rESTbgSO[0]['truck_export'] + $rESTbgSO[0]['truck_lokal']);
			$sheet->getStyle("X".$Colsw.":X".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("X".$Colsw.":X".$Colsw."");
			$sheet->getColumnDimension('X')->setAutoSize(true);
				
			// $awal_col+1;
			// $SumNox	= $SumNo;
			// $Cols			= getColsChar($awal_col+1);
			// $sheet->setCellValue($Cols.$awal_row, $SumNox);
			// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			
			
		}
		
		
		$sheet->setTitle('Excel Budget Final Drawing');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Detail Budget Final Drawing '.$id_bq.' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	
	
	
	
	
	
	public function AppBQFDSatuan(){
		$id_bq 			= $this->uri->segment(3);
		$id 			= $this->uri->segment(4);
		$nomor 			= $this->uri->segment(5);
		$data_session	= $this->session->userdata;
		$Y	= date('y');
		$status		= $this->input->post('sts_'.$nomor);
		$reason		= $this->input->post('reason_'.$nomor);
		$id_bq_header	= get_name('so_detail_header','id_bq_header','id',$id);
		
		$Imp			= explode('-', $id_bq);
		
		$checkPro 	= "SELECT * FROM production_header WHERE no_ipp='".$Imp[1]."'";
		$numPro 	= $this->db->query($checkPro)->num_rows();
		// echo $numPro; exit;
		// if($numPro < 1){
			if($status == 'Y'){
				//save to production
				$chkPH	= "SELECT * FROM production_header WHERE id_produksi = 'PRO-".$Imp[1]."' LIMIT 1";
				$numPH	= $this->db->query($chkPH)->num_rows();
				
				$ArrInsertPro = array(
					'id_produksi' => "PRO-".$Imp[1],
					'no_ipp' => $Imp[1],
					'jalur' => 'FD',
					'so_number' => "SO-".$Imp[1],
					'created_by' => $data_session['ORI_User']['username'],
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$qDet_Gt	= "SELECT 
									a.*, 
									b.id AS id_milik , 
									b.id_product AS id_product,
									b.cutting
								FROM 
									so_detail_detail a 
									INNER JOIN so_detail_header b ON a.id_bq_header = b.id_bq_header  
								WHERE 
									a.id_bq = '".$id_bq."' 
									AND a.approve = 'Y'
									AND b.id='".$id."' ";
				// echo $qDet_Gt; exit;
				$restBq		= $this->db->query($qDet_Gt)->result_array();
				 
				$ArrDetalPro = array();
				$ArrCutting = array();
				foreach($restBq AS $val => $valx){
					$ArrDetalPro[$val]['id_milik'] 		= $valx['id_milik']; 
					$ArrDetalPro[$val]['id_produksi'] 	= "PRO-".$Imp[1];
					$ArrDetalPro[$val]['id_delivery'] 	= $valx['id_delivery'];
					$ArrDetalPro[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$ArrDetalPro[$val]['sub_delivery'] 	= $valx['sub_delivery'];
					$ArrDetalPro[$val]['id_category'] 	= $valx['id_category'];
					$ArrDetalPro[$val]['id_product'] 	= $valx['id_product'];
					$ArrDetalPro[$val]['product_ke'] 	= $valx['product_ke'];
					$ArrDetalPro[$val]['qty'] 			= $valx['qty'];
					
					if($valx['cutting'] == 'Y'){
						$ArrCutting[$val]['id_milik'] 		= $valx['id_milik'];
						$ArrCutting[$val]['id_bq'] 			= $valx['id_bq'];
						$ArrCutting[$val]['id_category'] 	= $valx['id_category'];
						$ArrCutting[$val]['qty'] 			= $valx['qty'];
						$ArrCutting[$val]['qty_ke'] 		= $valx['product_ke'];
						$ArrCutting[$val]['diameter_1'] 	= $valx['diameter_1'];
						$ArrCutting[$val]['diameter_2'] 	= $valx['diameter_2'];
						$ArrCutting[$val]['length'] 		= $valx['length'];
						$ArrCutting[$val]['thickness'] 		= $valx['thickness'];
						$ArrCutting[$val]['created_by'] 	= $data_session['ORI_User']['username'];
						$ArrCutting[$val]['created_date'] 	= date('Y-m-d H:i:s');

						// $ArrDetalPro[$val]['sts_cutting'] 	= 'Y';
					}
				}
				
				$Arr_Edit_DetHeader	= array(
					'approve' 			=> 'P',
					'approve_by' 		=> $data_session['ORI_User']['username'],
					'approve_date' 	=> date('Y-m-d H:i:s')
				);
				
				$HistReason	= 'Approve Sebagian Final Drawing (in material planning) with BQ : '.$id_bq.' / '.$id;
				
				//Update SPK Nomor
				$restGet = "SELECT a.id, a.id_bq, a.id_category, a.no_spk, b.type 
							FROM
								so_detail_header a
								LEFT JOIN product_parent b ON a.id_category = b.product_parent 
							WHERE
								a.id_bq = '".$id_bq."' AND a.id='".$id."' AND a.no_spk IS NULL";
				$getRes	= $this->db->query($restGet)->result_array();
				
				$ArrDes = array();
				if(!empty($getRes)){
					foreach($getRes AS $val => $valx){
						if($valx['type'] == 'pipe'){
							$simbol = '20P.';
						}
						if($valx['type'] == 'fitting'){
							$simbol = '30F.';
						}
						if($valx['type'] == 'joint' OR $valx['type'] == 'field'){
							$simbol = '60A.';
						}
						
						
						$srcMtr			= "SELECT MAX(no_spk) as maxP FROM nomor_spk WHERE no_spk LIKE '".$simbol.$Y.".%' ";
						
					
						$numrowMtr		= $this->db->query($srcMtr)->num_rows();
						$resultMtr		= $this->db->query($srcMtr)->result_array();
						$angkaUrut2		= $resultMtr[0]['maxP'];
						$urutan2		= (int)substr($angkaUrut2, 7, 4);
						$urutan2++;
						$urut2			= sprintf('%04s',$urutan2);
						$no_spk			= $simbol.$Y.".".$urut2;
						// echo $no_spk;
						// exit;
						
						$this->db->set('no_spk', $no_spk);
						$this->db->where('id', $valx['id']);
						$this->db->update('so_detail_header');
						
						history('Create SPK Produksi: '.$no_spk.' / '.$id_bq.' / '.$id);
					}
				}
				$statusFlag = 'P';
			}
			
			if($status == 'N'){
				$Arr_Edit_DetHeader	= array(
					'approve' 			=> 'N',
					'approve_reason' 	=> $reason,
					'approve_by' 		=> $data_session['ORI_User']['username'],
					'approve_date' 		=> date('Y-m-d H:i:s')
				);
				$statusFlag = 'N';
				
				$HistReason	= 'Reject Sebagian Final Drawing To Est with BQ : '.$id_bq.' / '.$id;
			}
			
			if($status == 'M'){
				$Arr_Edit_DetHeader	= array(
					'approve' 			=> 'N',
					'approve_reason' 	=> $reason,
					'approve_by' 		=> $data_session['ORI_User']['username'],
					'approve_date' 		=> date('Y-m-d H:i:s')
				);
				$statusFlag = 'N';
				
				$HistReason	= 'Reject Sebagian Final Drawing To Bq with BQ : '.$id_bq.' / '.$id;
			}

			$get_detail_detail = $this->db->get_where('so_detail_detail', array('id_bq_header'=>$id_bq_header,'approve'=>'Y'))->result_array();
			$ArrUpdateDetail = [];
			foreach($get_detail_detail AS $val => $valx){
				$ArrUpdateDetail[$val]['id'] 			= $valx['id'];
				$ArrUpdateDetail[$val]['approve'] 		= $statusFlag;
				$ArrUpdateDetail[$val]['approve_by'] 	= $data_session['ORI_User']['username'];
				$ArrUpdateDetail[$val]['approve_date'] 	= date('Y-m-d H:i:s');
			}
			
			
			// print_r($Arr_Edit_DetHeader);
			// exit;
			
			
			$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('so_detail_header', $Arr_Edit_DetHeader);

				$this->db->update_batch('so_detail_detail', $ArrUpdateDetail,'id');
				
				if($status == 'Y'){
					if($numPH < 1){
						$this->db->insert('production_header', $ArrInsertPro);
					}
					$this->db->insert_batch('production_detail', $ArrDetalPro);
					// if(!empty($ArrCutting)){
					// 	$this->db->insert_batch('so_cutting_header', $ArrCutting);
					// }
				}
				
				check_approve($id_bq);
				check_status($id_bq_header);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Approve process failed. Please try again later ...',
					'status'	=> 0,
					'id_bq' => $id_bq
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Approve process success. Thanks ...',
					'status'	=> 1,
					'id_bq' => $id_bq
				);				
				history($HistReason);
			}
		// }
		// else{
			// $Arr_Data	= array(
				// 'pesan'		=>'Data already produced. Please refresh page ...',
				// 'status'	=> 0
			// );	
		// }
		echo json_encode($Arr_Data);
	}
	
	public function approve_fd_aksesoris(){
		$id_bq 			= $this->uri->segment(3);
		$id 			= $this->uri->segment(4);
		$nomor 			= $this->uri->segment(5);
		$data_session	= $this->session->userdata;
		$Y	= date('y');
		$status		= $this->input->post('sts_'.$nomor);
		$reason		= $this->input->post('reason_'.$nomor);
		
		$Imp			= explode('-', $id_bq);
		
		$checkPro 	= "SELECT * FROM production_header WHERE no_ipp='".$Imp[1]."'";
		$numPro 	= $this->db->query($checkPro)->num_rows();
		// echo $numPro; exit;
		// if($numPro < 1){
			if($status == 'Y'){
				// echo "Tahan";
				// exit;
				//save to production
				$chkPH	= "SELECT * FROM production_header WHERE id_produksi = 'PRO-".$Imp[1]."' LIMIT 1";
				$numPH	= $this->db->query($chkPH)->num_rows();
				
				$ArrInsertPro = array(
					'id_produksi' 	=> "PRO-".$Imp[1],
					'no_ipp' 		=> $Imp[1],
					'jalur' 		=> 'FD',
					'so_number' 	=> "SO-".$Imp[1],
					'created_by' 	=> $data_session['ORI_User']['username'],
					'created_date' 	=> date('Y-m-d H:i:s')
				);
			
				$qDet_Gt	= "SELECT a.* FROM so_acc_and_mat a WHERE a.id_bq = '".$id_bq."' AND a.id='".$id."' ";
				$restBq		= $this->db->query($qDet_Gt)->result_array();
				 
				$ArrDetalPro = array();
				foreach($restBq AS $val => $valx){
					$ArrDetalPro[$val]['id_milik'] 		= $valx['id']; 
					$ArrDetalPro[$val]['id_bq'] 		= $id_bq;
					$ArrDetalPro[$val]['category'] 		= $valx['category'];
					$ArrDetalPro[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetalPro[$val]['qty'] 			= $valx['qty'];
					$ArrDetalPro[$val]['satuan'] 		= $valx['satuan'];
					$ArrDetalPro[$val]['note'] 			= $valx['note'];
				}
				
				$Arr_Edit_DetHeader	= array(
					'approve' 		=> 'P',
					'approve_by' 	=> $data_session['ORI_User']['username'],
					'approve_date' 	=> date('Y-m-d H:i:s')
				);
				
				$HistReason	= 'Approve sebagian final drawing aksesoris with : '.$id_bq.' / '.$id;
			}
			
			if($status == 'N'){
				$Arr_Edit_DetHeader	= array(
					'approve' 			=> 'N',
					'approve_reason' 	=> $reason,
					'approve_by' 		=> $data_session['ORI_User']['username'],
					'approve_date' 		=> date('Y-m-d H:i:s')
				);
				
				$HistReason	= 'Reject sebagian aksesoris final drawing to est : '.$id_bq.' / '.$id;
			}
			
			
			// print_r($Arr_Edit_DetHeader);
			// exit;
			
			
			$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('so_acc_and_mat', $Arr_Edit_DetHeader);
				
				if($status == 'Y'){
					if($numPH < 1){
						$this->db->insert('production_header', $ArrInsertPro);
					}
					$this->db->insert_batch('production_acc_and_mat', $ArrDetalPro);
				}
				
				check_approve($id_bq);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Approve process failed. Please try again later ...',
					'status'	=> 0,
					'id_bq' => $id_bq
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Approve process success. Thanks ...',
					'status'	=> 1,
					'id_bq' => $id_bq
				);				
				history($HistReason);
			}
		// }
		// else{
			// $Arr_Data	= array(
				// 'pesan'		=>'Data already produced. Please refresh page ...',
				// 'status'	=> 0
			// );	
		// }
		echo json_encode($Arr_Data);
	}
	
	public function approve_fd_aksesoris_all(){
		$id_bq 			= $this->uri->segment(3);
		$id 			= $this->uri->segment(4);
		$nomor 			= $this->uri->segment(5);
		$data_session	= $this->session->userdata;
		$Y	= date('y');
		$status		= $this->input->post('sts_'.$nomor);
		$reason		= $this->input->post('reason_'.$nomor);
		
		$Imp			= explode('-', $id_bq);
		
		$checkPro 	= "SELECT * FROM production_header WHERE no_ipp='".$Imp[1]."'";
		$numPro 	= $this->db->query($checkPro)->num_rows();
		// echo $numPro; exit;
		// if($numPro < 1){
			if($status == 'Y'){
				// echo "Tahan";
				// exit;
				//save to production
				$chkPH	= "SELECT * FROM production_header WHERE id_produksi = 'PRO-".$Imp[1]."' LIMIT 1";
				$numPH	= $this->db->query($chkPH)->num_rows();
				
				$ArrInsertPro = array(
					'id_produksi' 	=> "PRO-".$Imp[1],
					'no_ipp' 		=> $Imp[1],
					'jalur' 		=> 'FD',
					'so_number' 	=> "SO-".$Imp[1],
					'created_by' 	=> $data_session['ORI_User']['username'],
					'created_date' 	=> date('Y-m-d H:i:s')
				);
			
				$qDet_Gt	= "SELECT a.* FROM so_acc_and_mat a WHERE a.id_bq = '".$id_bq."' AND a.id='".$id."' ";
				$restBq		= $this->db->query($qDet_Gt)->result_array();
				 
				$ArrDetalPro = array();
				foreach($restBq AS $val => $valx){
					$ArrDetalPro[$val]['id_milik'] 		= $valx['id']; 
					$ArrDetalPro[$val]['id_bq'] 		= $id_bq;
					$ArrDetalPro[$val]['category'] 		= $valx['category'];
					$ArrDetalPro[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetalPro[$val]['qty'] 			= $valx['qty'];
					$ArrDetalPro[$val]['satuan'] 		= $valx['satuan'];
					$ArrDetalPro[$val]['note'] 			= $valx['note'];
				}
				
				$Arr_Edit_DetHeader	= array(
					'approve' 		=> 'P',
					'approve_by' 	=> $data_session['ORI_User']['username'],
					'approve_date' 	=> date('Y-m-d H:i:s')
				);
				
				$HistReason	= 'Approve sebagian final drawing aksesoris with : '.$id_bq.' / '.$id;
			}
			
			if($status == 'N'){
				$Arr_Edit_DetHeader	= array(
					'approve' 			=> 'N',
					'approve_reason' 	=> $reason,
					'approve_by' 		=> $data_session['ORI_User']['username'],
					'approve_date' 		=> date('Y-m-d H:i:s')
				);
				
				$HistReason	= 'Reject sebagian aksesoris final drawing to est : '.$id_bq.' / '.$id;
			}
			
			
			// print_r($Arr_Edit_DetHeader);
			// exit;
			
			
			$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('so_acc_and_mat', $Arr_Edit_DetHeader);
				
				if($status == 'Y'){
					if($numPH < 1){
						$this->db->insert('production_header', $ArrInsertPro);
					}
					$this->db->insert_batch('production_acc_and_mat', $ArrDetalPro);
				}
				
				check_approve($id_bq);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Approve process failed. Please try again later ...',
					'status'	=> 0,
					'id_bq' => $id_bq
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Approve process success. Thanks ...',
					'status'	=> 1,
					'id_bq' => $id_bq
				);				
				history($HistReason);
			}
		// }
		// else{
			// $Arr_Data	= array(
				// 'pesan'		=>'Data already produced. Please refresh page ...',
				// 'status'	=> 0
			// );	
		// }
		echo json_encode($Arr_Data);
	}
	
	public function AppBQFD_All(){
		$id_bq 			= $this->uri->segment(3);
		$id 			= $this->uri->segment(4);
		$nomor 			= $this->uri->segment(5);
		$data_session	= $this->session->userdata;
		$Y	= date('y');
		$status		= $this->input->post('status');
		$reason		= $this->input->post('approve_reason');
		
		$Imp			= explode('-', $id_bq);
		
		$check2 = [];
		if(!empty($this->input->post('check2'))){
			$check2 = $this->input->post('check2');
		}
		
		if(!empty($this->input->post('check'))){
			$check = $this->input->post('check');
			$dtListArray = array();
			foreach($check AS $val => $valx){
				$dtListArray[$val] = $valx;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			
			$qDet_Gt2	= "SELECT b.* FROM so_detail_header b WHERE b.id_bq = '".$id_bq."' AND b.id IN ".$dtImplode." ";
			$restBq2		= $this->db->query($qDet_Gt2)->result_array();
		}
		
		$ArrUpdate = array();
		// echo print_r($restBq2); 
		// exit;

		if($status == 'Y'){
			$HistReason	= 'Approve Sebagian Checklist Final Drawing (in material planning) with BQ : '.$id_bq;
			// save to production
			$chkPH	= "SELECT * FROM production_header WHERE id_produksi = 'PRO-".$Imp[1]."' LIMIT 1";
			$numPH	= $this->db->query($chkPH)->num_rows();
			
			$ArrInsertPro = array(
				'id_produksi' => "PRO-".$Imp[1],
				'no_ipp' => $Imp[1],
				'jalur' => 'FD',
				'so_number' => "SO-".$Imp[1],
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			);
			
			if(!empty($this->input->post('check'))){
				$qDet_Gt	= "SELECT a.*, b.id AS id_milik , b.id_product AS id_product, b.cutting FROM so_detail_detail a INNER JOIN so_detail_header b ON a.id_bq_header = b.id_bq_header  WHERE a.id_bq = '".$id_bq."' AND a.approve = 'Y' AND b.id IN ".$dtImplode." ";
				$restBq		= $this->db->query($qDet_Gt)->result_array();
				 
				$ArrDetalPro = array();
				$ArrCutting = array();
				foreach($restBq AS $val => $valx){
					$ArrDetalPro[$val]['id_milik'] 		= $valx['id_milik']; 
					$ArrDetalPro[$val]['id_produksi'] 	= "PRO-".$Imp[1];
					$ArrDetalPro[$val]['id_delivery'] 	= $valx['id_delivery'];
					$ArrDetalPro[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$ArrDetalPro[$val]['sub_delivery'] 	= $valx['sub_delivery'];
					$ArrDetalPro[$val]['id_category'] 	= $valx['id_category'];
					$ArrDetalPro[$val]['id_product'] 	= $valx['id_product'];
					$ArrDetalPro[$val]['product_ke'] 	= $valx['product_ke'];
					$ArrDetalPro[$val]['qty'] 			= $valx['qty'];
					$ArrDetalPro[$val]['sts_cutting'] 	= 'N';
					if($valx['cutting'] == 'Y'){
						$ArrCutting[$val]['id_milik'] 		= $valx['id_milik'];
						$ArrCutting[$val]['id_bq'] 			= $valx['id_bq'];
						$ArrCutting[$val]['id_category'] 	= $valx['id_category'];
						$ArrCutting[$val]['qty'] 			= $valx['qty'];
						$ArrCutting[$val]['qty_ke'] 		= $valx['product_ke'];
						$ArrCutting[$val]['diameter_1'] 	= $valx['diameter_1'];
						$ArrCutting[$val]['diameter_2'] 	= $valx['diameter_2'];
						$ArrCutting[$val]['length'] 		= $valx['length'];
						$ArrCutting[$val]['thickness'] 		= $valx['thickness'];
						$ArrCutting[$val]['created_by'] 	= $data_session['ORI_User']['username'];
						$ArrCutting[$val]['created_date'] 	= date('Y-m-d H:i:s');

						// $ArrDetalPro[$val]['sts_cutting'] 	= 'Y';
					}
				}
				
				
				foreach($restBq2 AS $val => $valx){
					$ArrUpdate[$val]['id'] 				= $valx['id']; 
					$ArrUpdate[$val]['approve'] 		= 'P';
					$ArrUpdate[$val]['approve_by'] 		= $data_session['ORI_User']['username'];
					$ArrUpdate[$val]['approve_date'] 	= date('Y-m-d H:i:s');
				}
				
				$HistReason	= 'Approve Sebagian Checklist Final Drawing (in material planning) with BQ : '.$id_bq.' / '.$dtImplode;
				
				// Update SPK Nomor
				$restGet = "SELECT a.id, a.id_bq, a.id_category, a.no_spk, b.type 
							FROM
								so_detail_header a
								LEFT JOIN product_parent b ON a.id_category = b.product_parent 
							WHERE
								a.id_bq = '".$id_bq."' AND a.id IN ".$dtImplode."";
				$getRes	= $this->db->query($restGet)->result_array();
				// echo $restGet; exit;
				$ArrDes = array();
				foreach($getRes AS $val => $valx){
					$no_spk_old = $valx['no_spk'];
					if($valx['type'] == 'pipe'){
						$simbol = '20P.';
					}
					if($valx['type'] == 'fitting'){
						$simbol = '30F.';
					}
					if($valx['type'] == 'joint' OR $valx['type'] == 'field'){
						$simbol = '60A.';
					}
					
					$srcMtr			= "SELECT MAX(no_spk) as maxP FROM nomor_spk WHERE no_spk LIKE '".$simbol.$Y.".%' ";
				
					$numrowMtr		= $this->db->query($srcMtr)->num_rows();
					$resultMtr		= $this->db->query($srcMtr)->result_array();
					$angkaUrut2		= $resultMtr[0]['maxP'];
					$urutan2		= (int)substr($angkaUrut2, 7, 4);
					$urutan2++;
					$urut2			= sprintf('%04s',$urutan2);
					$no_spk			= $simbol.$Y.".".$urut2;
					// echo $no_spk;
					// exit;
					if(!empty($no_spk_old)){
						$no_spk		= $no_spk_old;
					}
					
					$this->db->set('no_spk', $no_spk);
					$this->db->where('id', $valx['id']);
					$this->db->update('so_detail_header');
					
					history('Create SPK Produksi: '.$no_spk.' / '.$id_bq.' / '.$valx['id']);
				} 
			}
			
			
			
			//ALL AKSESORIS & MATERIAN
			$ArrDetalProAcc = array();
			if(!empty($this->input->post('check2'))){
				$check2 = $this->input->post('check2');
				
				$restBq		= $this->db->select('*')->from('so_acc_and_mat')->where('id_bq',$id_bq)->where_in('id',$check2)->get()->result_array();
				 
				
				foreach($restBq AS $val => $valx){
					$ArrDetalProAcc[$val]['id_milik'] 		= $valx['id']; 
					$ArrDetalProAcc[$val]['id_bq'] 		= $id_bq;
					$ArrDetalProAcc[$val]['category'] 		= $valx['category'];
					$ArrDetalProAcc[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetalProAcc[$val]['qty'] 			= $valx['qty'];
					$ArrDetalProAcc[$val]['satuan'] 		= $valx['satuan'];
					$ArrDetalProAcc[$val]['note'] 			= $valx['note'];
				}
				
				$Arr_Edit_DetHeader	= array(
					'approve' 		=> 'P',
					'approve_by' 	=> $data_session['ORI_User']['username'],
					'approve_date' 	=> date('Y-m-d H:i:s')
				);
			}

			
		}
		
		if($status == 'N'){
			$HistReason	= 'Reject Sebagian Checklist Final Drawing To Bq with BQ : '.$id_bq;
			if(!empty($this->input->post('check'))){
				foreach($restBq2 AS $val => $valx){
					$ArrUpdate[$val]['id'] 				= $valx['id']; 
					$ArrUpdate[$val]['approve'] 		= 'N';
					$ArrUpdate[$val]['approve_reason'] 	= $reason;
					$ArrUpdate[$val]['approve_by'] 		= $data_session['ORI_User']['username'];
					$ArrUpdate[$val]['approve_date'] 	= date('Y-m-d H:i:s');
				}
				$HistReason	= 'Reject Sebagian Checklist Final Drawing To Est with BQ : '.$id_bq.' / '.$dtImplode;
			}
			
			$Arr_Edit_DetHeader	= array(
				'approve' 			=> 'N',
				'approve_reason' 	=> $reason,
				'approve_by' 		=> $data_session['ORI_User']['username'],
				'approve_date' 		=> date('Y-m-d H:i:s')
			);
			
			
		}
		
		if($status == 'M'){
			$HistReason	= 'Reject Sebagian Checklist Final Drawing To Bq with BQ : '.$id_bq;
			if(!empty($this->input->post('check'))){
				foreach($restBq2 AS $val => $valx){
					$ArrUpdate[$val]['id'] 				= $valx['id']; 
					$ArrUpdate[$val]['approve'] 		= 'N';
					$ArrUpdate[$val]['approve_reason'] 	= $reason;
					$ArrUpdate[$val]['approve_by'] 		= $data_session['ORI_User']['username'];
					$ArrUpdate[$val]['approve_date'] 	= date('Y-m-d H:i:s');
				}
				$HistReason	= 'Reject Sebagian Checklist Final Drawing To Bq with BQ : '.$id_bq.' / '.$dtImplode;
			}
			
			$Arr_Edit_DetHeader	= array(
				'approve' 			=> 'N',
				'approve_reason' 	=> $reason,
				'approve_by' 		=> $data_session['ORI_User']['username'],
				'approve_date' 		=> date('Y-m-d H:i:s')
			);
			
			
		}
		
		// echo $status;
		// print_r($ArrDetalPro);
		// print_r($ArrUpdate);
		// exit;
		
		
		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch('so_detail_header', $ArrUpdate, 'id');
			}
			
			if($status == 'Y'){
				if($numPH < 1){
					$this->db->insert('production_header', $ArrInsertPro);
				}
				if(!empty($ArrDetalPro)){
					$this->db->insert_batch('production_detail', $ArrDetalPro);
				}
				// if(!empty($ArrCutting)){
				// 	$this->db->insert_batch('so_cutting_header', $ArrCutting);
				// }
				
				if(!empty($this->input->post('check'))){
					foreach($check AS $val => $valx){
						$id_bq_header	= get_name('so_detail_header','id_bq_header','id',$valx);
						$this->db->query("UPDATE 
											so_detail_detail
										SET
											approve='P', 
											release_by='".$data_session['ORI_User']['username']."', 
											release_date='".date('Y-m-d H:i:s')."'
										WHERE 
											id_bq_header='".$id_bq_header."'
											AND id_bq='".$id_bq."'
											AND approve = 'Y'
										ORDER BY 
											id ASC");
					}
				}
				
				
				//AKSESORIS & MATERIAL
				if(!empty($check2)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where_in('id', $check2);
					$this->db->update('so_acc_and_mat', $Arr_Edit_DetHeader);
					
					if(!empty($ArrDetalProAcc)){
						$this->db->insert_batch('production_acc_and_mat', $ArrDetalProAcc);
					}
				}
			}
			if($status != 'Y'){
				if(!empty($this->input->post('check'))){
					foreach($check AS $val => $valx){
						$id_bq_header	= get_name('so_detail_header','id_bq_header','id',$valx);
						$this->db->query("UPDATE 
											so_detail_detail
										SET
											approve='N', 
											approve_by='".$data_session['ORI_User']['username']."', 
											approve_date='".date('Y-m-d H:i:s')."'
										WHERE 
											id_bq_header='".$id_bq_header."'
											AND id_bq='".$id_bq."'
											AND approve = 'Y'
										ORDER BY 
											id ASC");
					}
				}
				
				//AKSESORIS & MATERIAL
				if(!empty($check2)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where_in('id', $check2);
					$this->db->update('so_acc_and_mat', $Arr_Edit_DetHeader);
				}
			}
			
			
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq' => $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve process success. Thanks ...',
				'status'	=> 1,
				'id_bq' => $id_bq
			);
			check_approve($id_bq);
			if(!empty($this->input->post('check'))){
			check_status_all($id_bq,$check);	
			}			
			history($HistReason);
		}
		
		echo json_encode($Arr_Data);
	}
	
	
	public function budget_fd(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))); 
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT create_by, create_date FROM budget_fd ORDER BY create_date DESC LIMIT 1";
		$ListIPP 			= $this->db->query("SELECT * FROM so_header ORDER BY no_ipp ASC")->result_array();
		$restgetBy			= $this->db->query($getBy)->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Budget Final Drawing',
			'action'		=> 'budget_fd',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'ListIPP'		=> $ListIPP
		);
		history('View Data Budget Project Final Drawing');
		$this->load->view('FinalDrawing/budget_fd',$data);
	}
	
	function insert_select_budget_fd(){ 
		$data = $this->input->post();
		$where2 = "";
		if(!empty($data['no_ipp_filter'])){
			$ListIPP = $data['no_ipp_filter'];
			$dtListArray = array();
			foreach($ListIPP AS $val => $valx){
				$dtListArray[$val] = $valx;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			// echo $dtImplode;
			$where2 = " WHERE a.id_bq IN ".$dtImplode."";
		}

		history('Try update budget FD');
		$sql_ = "SELECT a.id_bq FROM budget_fd_1 a ".$where2."";
		// echo $sql_; exit;
		$sqlCheck = $this->db->query($sql_)->result_array();
		$ArrBudget3 = array();
		foreach($sqlCheck AS $val => $valx){
			$ArrBudget3[$val]['id_bq'] = $valx['id_bq'];
			$ArrBudget3[$val]['profit'] = Profit($valx['id_bq']);
			$ArrBudget3[$val]['allowance'] = Allowance($valx['id_bq']);
		}
		
		// print_r($ArrBudget3);
		// exit;
		$this->db->trans_start();
			$this->db->truncate('budget_fd_3');
			$this->db->insert_batch('budget_fd_3',$ArrBudget3);
	
			$this->db->truncate('budget_fd');
			//Check sudah input		
			$sqlUpdate = "
				INSERT INTO budget_fd ( id_bq, 
										id_customer, 
										nm_customer, 
										project, 
										est_mat, 
										est_cost, 
										direct_labour, 
										indirect_labour, 
										machine, 
										mould_mandrill, 
										consumable, 
										depresiasi_foh, 
										consumable_foh, 
										gaji_non_produksi, 
										biaya_admin, 
										biaya_bulanan,
										profit,
										allowance,
										packing,													
										enggenering,
										truck_export,
										truck_lokal,
										create_by,
										create_date ) 
				SELECT
					a.id_bq, 
					(SELECT id_customer FROM production WHERE no_ipp=REPLACE(a.id_bq, 'BQ-', '')) AS id_customer, 
					(SELECT nm_customer FROM production WHERE no_ipp=REPLACE(a.id_bq, 'BQ-', '')) AS nm_customer, 
					(SELECT project FROM production WHERE no_ipp=REPLACE(a.id_bq, 'BQ-', '')) AS project, 
					a.sum_mat, 
					a.est_harga,
					a.direct_labour, 
					a.indirect_labour, 
					a.machine, 
					a.mould_mandrill,
					a.consumable, 
					a.foh_depresiasi, 
					a.foh_consumable, 
					a.biaya_gaji_non_produksi, 
					a.biaya_non_produksi, 
					a.biaya_rutin_bulanan,
					c.profit,
					c.allowance,
					b.packing,													
					b.engine_,
					b.export,
					b.lokal,
					'".$this->session->userdata['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
				FROM
					budget_fd_1 a 
						LEFT JOIN budget_fd_2 b ON a.id_bq=b.id_bq
						LEFT JOIN budget_fd_3 c ON a.id_bq=c.id_bq ".$where2." ";
			
			$this->db->query($sqlUpdate);
			history('Success insert budget fd');
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
			history('Success insert budget fd');
		}
		echo json_encode($Arr_Data);
	}

	function insert_select_budget_fd2(){ 
		$data = $this->input->post();
		$where2 = "";
		if(!empty($data['no_ipp_filter'])){
			$ListIPP = $data['no_ipp_filter'];
			$dtListArray = array();
			foreach($ListIPP AS $val => $valx){
				$dtListArray[$val] = $valx;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			// echo $dtImplode;
			$where2 = " WHERE a.id_bq IN ".$dtImplode."";
		}

		history('Try update budget FD');
		$sql_ = "SELECT a.* FROM budget_fd_1 a ".$where2."";
		// echo $sql_; exit;
		$sqlCheck = $this->db->query($sql_)->result_array();
		$ArrBudget3 = array();
		foreach($sqlCheck AS $val => $valx){
			
			$get_acc_mat = $this->db->select('SUM(qty) AS qty')->get_where('so_acc_and_mat', array('id_bq'=>$valx['id_bq'], 'category'=>'mat'))->result();
			$sum_mat = (!empty($get_acc_mat))?$get_acc_mat[0]->qty : 0;
			
			$get_acc_mat_price = $this->db->select('SUM(price_total) AS price_total')->get_where('cost_project_detail', array('id_bq'=>$valx['id_bq'], 'category'=>'aksesoris'))->result();
			$sum_mat_price = (!empty($get_acc_mat_price))?$get_acc_mat_price[0]->price_total : 0;
			
			$get_acc_mat_price_acc = $this->db
											->select('SUM(price_total) AS price_total')
											->from('cost_project_detail')
											->where("id_bq = '".$valx['id_bq']."' AND (category = 'baut' OR category = 'gasket' OR category = 'plate' OR category = 'lainnya') ")
											->get()
											->result();
			$sum_mat_price_acc = (!empty($get_acc_mat_price_acc))?$get_acc_mat_price_acc[0]->price_total : 0;
			
			
			$ArrBudget3[$val]['id_bq'] 				= $valx['id_bq'];
			$ArrBudget3[$val]['id_customer'] 		= get_name('production','id_customer','no_ipp',str_replace('BQ-','',$valx['id_bq']));
			$ArrBudget3[$val]['nm_customer'] 		= get_name('production','nm_customer','no_ipp',str_replace('BQ-','',$valx['id_bq']));
			$ArrBudget3[$val]['project'] 			= get_name('production','project','no_ipp',str_replace('BQ-','',$valx['id_bq']));
			
			$ArrBudget3[$val]['est_mat'] 			= $valx['sum_mat'] + $sum_mat;
			$ArrBudget3[$val]['est_cost'] 			= $valx['est_harga'] + $sum_mat_price + $sum_mat_price_acc;
			$ArrBudget3[$val]['direct_labour'] 		= $valx['direct_labour'];
			$ArrBudget3[$val]['indirect_labour'] 	= $valx['indirect_labour'];
			$ArrBudget3[$val]['machine'] 			= $valx['machine'];
			$ArrBudget3[$val]['mould_mandrill'] 	= $valx['mould_mandrill'];
			$ArrBudget3[$val]['consumable'] 		= $valx['consumable'];
			$ArrBudget3[$val]['depresiasi_foh'] 	= $valx['foh_depresiasi'];
			$ArrBudget3[$val]['consumable_foh'] 	= $valx['foh_consumable'];
			$ArrBudget3[$val]['gaji_non_produksi'] 	= $valx['biaya_gaji_non_produksi'];
			$ArrBudget3[$val]['biaya_admin'] 		= $valx['biaya_non_produksi'];
			$ArrBudget3[$val]['biaya_bulanan'] 		= $valx['biaya_rutin_bulanan'];
			
			$ArrBudget3[$val]['profit'] 		= Profit($valx['id_bq']);
			$ArrBudget3[$val]['allowance'] 		= Allowance($valx['id_bq']);
			$ArrBudget3[$val]['packing'] 		= manual_packing_cost($valx['id_bq']);
			$ArrBudget3[$val]['enggenering'] 	= manual_eng_cost($valx['id_bq']);
			$ArrBudget3[$val]['truck_export'] 	= manual_export_cost($valx['id_bq']);
			$ArrBudget3[$val]['truck_lokal'] 	= manual_lokal_cost($valx['id_bq']);
			$ArrBudget3[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrBudget3[$val]['create_date'] 	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrBudget3);
		// exit;
		$this->db->trans_start();
			$this->db->truncate('budget_fd');
			$this->db->insert_batch('budget_fd',$ArrBudget3);
			history('Success insert budget fd');
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
			history('Success insert budget fd');
		}
		echo json_encode($Arr_Data);
	}
	
	public function getDataJSONQuo(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONQuo(
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		foreach($query->result_array() as $row)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'desc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }
			

			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_mat'],3)." Kg</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_cost'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['direct_labour']+$row['indirect_labour']+$row['machine']+$row['mould_mandrill']+$row['consumable'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['depresiasi_foh']+$row['consumable_foh']+$row['gaji_non_produksi']+$row['biaya_admin']+$row['biaya_bulanan'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['profit'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['allowance'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['packing'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['enggenering'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['truck_export']+$row['truck_lokal'],2)."</div>";
					$priX	= "";
					$updX	= "";
					$ApprvX	= "";
					$Print	= "";
					$Hist	= "";
					$ApprvX2Edit = "";
					
					$viewX	= "&nbsp;<button type='button' class='btn btn-sm btn-warning ViewDT' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					$ApprvX	= "&nbsp;<button type='button' class='btn btn-sm btn-success ViewSO' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					$Print	= "&nbsp;<button type='button' class='btn btn-sm btn-info download_excel' title='Download Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-file-excel-o'></i></button>";
					
					// if($row['sts_ipp'] == 'WAITING SALES ORDER'){
						// $ApprvX	= "&nbsp;<button class='btn btn-sm btn-success' id='ApproveDT' title='Approve To Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
					// }

					// <button class='btn btn-sm btn-primary' id='detailBQ'  title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
			$nestedData[]	= "<div align='left'>
									".$priX."
									".$updX."
									".$viewX."
									".$ApprvX."
									".$Hist."
									".$ApprvX2Edit."
									".$Print."
									</div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function queryDataJSONQuo($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				budget_fd a
		    WHERE 1=1
				AND (
				a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modalViewBudgetFd(){
		$this->load->view('FinalDrawing/modalViewBudgetFD');
	}
	
	public function modalViewBudgetDetail(){
		$this->load->view('FinalDrawing/modalViewBudgetDetail');
	}
	
	public function modalDetailBudgetCost(){
		$this->load->view('FinalDrawing/modalDetailBudgetCost');
	}
	
	public function modalDetailBudgetGroup(){
		$id_milik 	= $this->uri->segment(3);
		$id_bq 		= $this->uri->segment(4); 	
		$qty 		= $this->uri->segment(5); 
		// echo $id_bq;
		$qHeader	= "SELECT * FROM so_component_header WHERE id_milik='".$id_milik."' AND id_bq='".$id_bq."'";
		$restHeader	= $this->db->query($qHeader)->result_array();

		$qDet		= "SELECT * FROM so_estimasi_total_component WHERE id_milik='".$id_milik."' AND id_bq='".$id_bq."'";
		$restDet	= $this->db->query($qDet)->result_array();

		$data = array(
			'restHeader' => $restHeader,
			'detail' => $restDet,
			'qty' => $qty
		);

		$this->load->view('FinalDrawing/modalDetailBudgetGroup', $data);
	}
	
	public function ExcelBudgetFd(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(25);
		$sheet->setCellValue('A'.$Row, 'DETAIL BUDGET SO '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'SO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Project');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Liner');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'PN');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spesifikasi');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Qty');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'Est Mat (Kg)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'Est Mat');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Direct Labour');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Machine');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'Mould Mandrill');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'Consumable');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'Consumable FOH');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue('Q'.$NewRow, 'Depresiasi FOH');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'Gaji Non Produksi');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'Biaya Admin');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sheet->setCellValue('T'.$NewRow, 'Biaya Bulanan');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
		$sheet->getColumnDimension('T')->setAutoSize(true);
		
		$sheet->setCellValue('U'.$NewRow, 'Profit');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setAutoSize(true);
		
		$sheet->setCellValue('V'.$NewRow, 'Allowance');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow);
		$sheet->getColumnDimension('V')->setAutoSize(true);
		
		$sheet->setCellValue('W'.$NewRow, 'Packing');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow);
		$sheet->getColumnDimension('W')->setAutoSize(true); 
		
		$sheet->setCellValue('X'.$NewRow, 'Enggenering');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow);
		$sheet->getColumnDimension('X')->setAutoSize(true);
		
		$sheet->setCellValue('Y'.$NewRow, 'Trucking');
		$sheet->getStyle('Y'.$NewRow.':Y'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Y'.$NewRow.':Y'.$NextRow);
		$sheet->getColumnDimension('Y')->setAutoSize(true);
		
		// $qMatr 		= SQL_SO($id_bq);
		$qMatr 		= SQL_FD($id_bq);
		$restDetail1= $this->db->query($qMatr)->result_array(); 
		// echo $qMatr; exit;
		$SQLbGsO 	= "SELECT * FROM budget_fd WHERE id_bq='".$id_bq."'";
		$rESTbgSO	= $this->db->query($SQLbGsO)->result_array();
		// echo $qMatr; exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			$SUM = 0;
			$SumEstHarga = 0;
			$SumTot2 = 0;
			$HPP_Tot = 0;
			
			$EstMatKg = 0;
			$EstMat = 0;
			
			$Direct = 0;
			$Indirect = 0;
			$Machi = 0;
			$MouldM = 0;
			$Consumab = 0;
			
			$ConsFOH = 0;
			$DepFOH = 0;
			$GjNonP = 0;
			$ByAdmin = 0;
			$ByBulanan = 0;
			
			$Profits = 0;
			$Allowancex = 0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$EstMatKg += $row_Cek['sum_mat2'];
				
				
				$Direct += $row_Cek['direct_labour'];
				$Indirect += $row_Cek['indirect_labour'];
				$Machi += $row_Cek['machine'];
				$MouldM += $row_Cek['mould_mandrill'];
				$Consumab += $row_Cek['consumable'];
				
				$ConsFOH += $row_Cek['foh_consumable'];
				$DepFOH += $row_Cek['foh_depresiasi'];
				$GjNonP += $row_Cek['biaya_gaji_non_produksi'];
				$ByAdmin += $row_Cek['biaya_non_produksi'];
				$ByBulanan += $row_Cek['biaya_rutin_bulanan'];
				
				
				// $getProfit = $this->db->query("SELECT profit FROM cost_profit WHERE diameter='".str_replace('.','',$row_Cek['diameter_1'])."' AND diameter2='".str_replace('.','',$row_Cek['diameter_2'])."' AND product_parent='".$row_Cek['id_category']."' ")->result_array();
				// $est_harga = (($row_Cek['est_harga2']+$row_Cek['cost_process']+$row_Cek['foh_consumable']+$row_Cek['foh_depresiasi']+$row_Cek['biaya_gaji_non_produksi']+$row_Cek['biaya_non_produksi']+$row_Cek['biaya_rutin_bulanan'])) / $row_Cek['qty'];
				$est_harga = (($row_Cek['est_harga2']+$row_Cek['direct_labour']+$row_Cek['indirect_labour']+$row_Cek['machine']+$row_Cek['mould_mandrill']+$row_Cek['consumable']+$row_Cek['foh_consumable']+$row_Cek['foh_depresiasi']+$row_Cek['biaya_gaji_non_produksi']+$row_Cek['biaya_non_produksi']+$row_Cek['biaya_rutin_bulanan'])) / $row_Cek['qty'];
				

				$profit = (!empty($row_Cek['persen']))?$row_Cek['persen']:30;
				$EstMat += $row_Cek['est_harga2'];
				
				$helpProfit = $est_harga *($profit/100);

				
				$HrgTot   = (($est_harga) + ($helpProfit)) * $row_Cek['qty'];
				$HPP_Tot += $est_harga * $row_Cek['qty'];
				$SumTot2 += $HrgTot;

				$allow 		= (!empty($row_Cek['extra']))?$row_Cek['extra']:15;
				
				$HrgTot2  = (($HrgTot) + ($HrgTot * ($allow/100)));
				$SumEstHarga = $est_harga * $row_Cek['qty'];
				
				// $HrgTot2   	= (($est_harga) + ($est_harga * ($profit/100))) * $valx['qty'];
				// $HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($allow/100)));
				
				$SUM	 += (($HrgTot) + ($HrgTot * ($allow/100)));
				
				//cek project
				$sqlP = $this->db->query("SELECT project FROM production WHERE no_ipp='".str_replace('BQ-', '', $id_bq)."'")->result_array();
				
				$Profits += $HrgTot - $SumEstHarga;
				$Allowancex += $HrgTot2 - $HrgTot;
				
				
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$id_bqx		= $id_bq;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_bqx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$project	= $sqlP[0]['project'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$id_category	= $row_Cek['parent_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$liner	= $row_Cek['liner'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $liner);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$pressure	= $row_Cek['pressure'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $pressure);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$spesifik	= spec_fd($row_Cek['id'], 'so_detail_header');
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spesifik);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$qty	= $row_Cek['qty'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$sum_mat2	= $row_Cek['sum_mat2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sum_mat2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga2	= $row_Cek['est_harga2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				// $profit	= $est_harga ; 
				$profit	= $HrgTot - $SumEstHarga ;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $profit);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$allowance	= $HrgTot2 - $HrgTot;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $allowance);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$packing	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $packing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$enggenering	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $enggenering);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$trucking	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $trucking);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
			// echo $no;exit;
			$Colsw = floatval($no) +6;
			
			// echo $Colsw."-".$Colse; exit;
			
			$sheet->setCellValue("A".$Colsw."", 'SUM');
			$sheet->getStyle("A".$Colsw.":H".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$Colsw.":H".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			
			$sheet->setCellValue("I".$Colsw."", $EstMatKg);
			$sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("I".$Colsw.":I".$Colsw."");
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue("J".$Colsw."", $EstMat);
			$sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("J".$Colsw.":J".$Colsw."");
			$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue("K".$Colsw."", $Direct );
			$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
			$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue("L".$Colsw."", $Indirect);
			$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
			$sheet->getColumnDimension('L')->setAutoSize(true);
			
			$sheet->setCellValue("M".$Colsw."", $Machi);
			$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$Colsw."", $MouldM);
			$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$Colsw."", $Consumab);
			$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$Colsw."", $ConsFOH);
			$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$Colsw."", $DepFOH);
			$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$Colsw."", $GjNonP);
			$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$Colsw."", $ByAdmin);
			$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
			
			$sheet->setCellValue("T".$Colsw."", $ByBulanan);
			$sheet->getStyle("T".$Colsw.":T".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("T".$Colsw.":T".$Colsw."");
			$sheet->getColumnDimension('T')->setAutoSize(true);
			
			$sheet->setCellValue("U".$Colsw."", $Profits);
			$sheet->getStyle("U".$Colsw.":U".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("U".$Colsw.":U".$Colsw."");
			$sheet->getColumnDimension('U')->setAutoSize(true);
			
			$sheet->setCellValue("V".$Colsw."", $Allowancex);
			$sheet->getStyle("V".$Colsw.":V".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("V".$Colsw.":V".$Colsw."");
			$sheet->getColumnDimension('V')->setAutoSize(true);
			
			$sheet->setCellValue("W".$Colsw."", $rESTbgSO[0]['packing']);
			$sheet->getStyle("W".$Colsw.":W".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("W".$Colsw.":W".$Colsw."");
			$sheet->getColumnDimension('W')->setAutoSize(true);
			
			$sheet->setCellValue("X".$Colsw."", $rESTbgSO[0]['enggenering']);
			$sheet->getStyle("X".$Colsw.":X".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("X".$Colsw.":X".$Colsw."");
			$sheet->getColumnDimension('X')->setAutoSize(true);
			
			$sheet->setCellValue("Y".$Colsw."", $rESTbgSO[0]['truck_export'] + $rESTbgSO[0]['truck_lokal']);
			$sheet->getStyle("Y".$Colsw.":Y".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Y".$Colsw.":Y".$Colsw."");
			$sheet->getColumnDimension('Y')->setAutoSize(true);
				
			// $awal_col+1;
			// $SumNox	= $SumNo;
			// $Cols			= getColsChar($awal_col+1);
			// $sheet->setCellValue($Cols.$awal_row, $SumNox);
			// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			
			
		}
		
		
		$sheet->setTitle('Excel Budget Final Drawing');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Detail Budget FD '.$id_bq.' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}


	public function pembetulan_status_manual(){
		$get_asset = $this->db->get('bantuan_upload')->result_array();
		$nomor = 0;
		foreach ($get_asset as $key => $value) { 
			$created_by 	= $value['created_by'];
			$created_date 	= $value['created_date'];
			$id_bq_header 	= $value['id_bq_header'];
			$qty 			= $value['qty'];
			$SQL = "UPDATE FROM so_detail_detail SET approve='Y', approve_by='$created_by', approve_date='$created_date' WHERE id_bq_header='$id_bq_header' LIMIT $qty;";
			echo $SQL.'<br>';
		}
		// echo 'Success Insert !';
	}
	
}
