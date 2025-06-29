<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Price extends CI_Controller { 
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		
		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		// print_r($Arr_Akses); exit; 
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$id_price		= $this->uri->segment(3);
		$get_Data		= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.parent_product='".$id_price."' AND a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses		= $this->master_model->getMenu();
		$getSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted = 'N' AND `status` = 'Y' ORDER BY pressure ASC, resin_system ASC, liner ASC")->result_array();
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		$ListCustomer	= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
		
		$getBy				= "SELECT updated_date FROM table_product_list ORDER BY updated_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result();
		
		$data = array(
			'title'			=> 'Indeks Of Product Price',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'listseries'	=> $getSeries,
			'listkomponen'	=> $getKomp,
			'cust'			=> $ListCustomer,
			'last_by'		=> 'system',
			'last_date'		=> (!empty($restgetBy[0]->updated_date))?$restgetBy[0]->updated_date:date('Y-m-d')
		);
		history('View Data Product Price');
		$this->load->view('Price/index',$data);
	}
	
	public function ajukan_price(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/ajukan_price";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$id_price		 	= $this->uri->segment(3);
		$get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		$getProduct			= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Estimation Submit',
			'action'		=> 'index',
			'listparent'	=> $getProduct,
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Pengajuan Product Price');
		$this->load->view('Price/ajukan_price',$data);
	}
	
	public function ajukan_price2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$id_price		 	= $this->uri->segment(3);
		// $get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM product_header a LEFT JOIN customer b ON b.id_customer=a.id_customer WHERE a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		$getProduct			= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Estimation Submit',
			'action'		=> 'index',
			'listparent'	=> $getProduct,
			// 'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		// history('View Pengajuan Product Price');
		$this->load->view('Price/ajukan_price2',$data);
	}
	
	public function app_mat(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/app_mat";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.sts_price='WAITING APPROVE' AND a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Waiting Approval perMaterial',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Approval per Material');
		$this->load->view('Price/app_mat',$data);
	}
	
	
	public function modalDetailProcess(){
		$this->load->view('Price/modalDetailProcess');
	}
	
	public function modalDetail(){
		$this->load->view('Price/modalDetail');
	}
	
	public function modalDetailMat(){
		$id_product = $this->uri->segment(3);
		$id_milik 	= $this->uri->segment(4);
		$qty 		= floatval($this->uri->segment(5));
		$length 	= $this->uri->segment(6);
		$id_bq 		= $this->uri->segment(7);

		$ArrWhere1 = array(
			'id_product' => $id_product,
			'id_milik' => $id_milik,
			'id_bq' => $id_bq,
			'id_material <>' => 'MTL-1903000',
			'id_category <>' => 'TYP-0001'
		);

		$ArrWhere2 = array(
			'id_product' => $id_product,
			'id_milik' => $id_milik,
			'id_bq' => $id_bq,
			'id_material <>' => 'MTL-1903000'
		);

		$ArrWhere3 = array(
			'id_product' => $id_product,
			'id_milik' => $id_milik,
			'id_bq' => $id_bq,
			'id_material <>' => 'MTL-1903000',
			'id_category' => 'TYP-0001'
		);

		$getDetail1 = $this->db->get_where('bq_component_detail',$ArrWhere1)->result_array();
		$ArrDetail1 = [];
		foreach($getDetail1 AS $valx => $value){
			$ArrDetail1[$value['detail_name']][] = $value;
		}

		$getPlus1 = $this->db->get_where('bq_component_detail_plus',$ArrWhere2)->result_array();
		$ArrPlus1 = [];
		foreach($getPlus1 AS $valx => $value){
			$ArrPlus1[$value['detail_name']][] = $value;
		}

		$getAdd1 = $this->db->get_where('bq_component_detail_add',$ArrWhere2)->result_array();
		$ArrAdd1 = [];
		foreach($getAdd1 AS $valx => $value){
			$ArrAdd1[$value['detail_name']][] = $value;
		}

		$getResinMax 	= $this->db->select('MAX(id_detail) AS id_detail')->group_by('detail_name')->get_where('bq_component_detail',$ArrWhere3)->result_array();
		$wherein = [];
		foreach($getResinMax AS $val => $value){
			$wherein[] = $value['id_detail'];
		}
		$ArrResin1 = [];
		if(!empty($wherein)){
			$getResin1 		= $this->db->select('*')->from('bq_component_detail')->where_in('id_detail',$wherein)->get()->result_array();
			foreach($getResin1 AS $valx => $value){
				$ArrResin1[$value['detail_name']][] = $value;
			}
		}

		$restHeader		= $this->db->get_where('bq_component_header',array('id_product'=>$id_product,'id_milik'=>$id_milik,'id_bq'=>$id_bq))->result_array();

		$ArrData = [
			'restHeader' => $restHeader,
			'restDetail' => $ArrDetail1,
			'restDetailPlus' => $ArrPlus1,
			'restDetailAdd' => $ArrAdd1,
			'restResin' => $ArrResin1
		];
		$this->load->view('Price/modalDetailMat',$ArrData);
	}
	
	public function modalDetailMatCost(){
		$this->load->view('Price/modalDetailCost');
	}
	
	public function modalPrice(){
		$this->load->view('Price/modalPrice');
	}
	
	public function modalAppMat(){
		$this->load->view('Price/modalAppMat');
	}
	
	public function modalAppCost(){
		$this->load->view('Price/modalAppCost');
	}
	
	public function AppCost(){
		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		
		$StatuxX		= $this->input->post('status');
		
		$data_session	= $this->session->userdata;
		
		$sqlNoRev_eng 	= "SELECT revised_no FROM laporan_costing_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
		$restNoRev_eng 	= $this->db->query($sqlNoRev_eng)->result();
		$revisi_eng 	= (!empty($restNoRev_eng))?$restNoRev_eng[0]->revised_no:0;
		
		// $stsX			= ($this->input->post('status') == 'Y')?'ALREADY ESTIMATED PRICE':'WAITING STRUCTURE BQ';
		
		// $Arr_Edit	= array(
		// 	'status' => $stsX,
		// 	'sts_price_reason' => $this->input->post('approve_reason'),
		// 	'sts_price_by' => $data_session['ORI_User']['username'],
		// 	'sts_price_date' => date('Y-m-d H:i:s')
		// );
		
		if($StatuxX == 'Y'){
			$Arr_Edit	= array(
				'status' => 'ALREADY ESTIMATED PRICE',
				'sts_price_reason' => $this->input->post('approve_reason'),
				'sts_price_by' => $data_session['ORI_User']['username'],
				'sts_price_date' => date('Y-m-d H:i:s')
			);

			$ArrInsert	= array(
				'id_bq' => $id_bq,
				'cost_material' => str_replace(',', '', $this->input->post('total_kg')),
				'cost_total' => str_replace(',', '', $this->input->post('total_cost')),
				'status' => 'Y',
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			);

			//Insert Detail Report Revised
			$sqlNoRev 	= "SELECT revised_no FROM laporan_revised_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
			$restNoRev 	= $this->db->query($sqlNoRev)->result_array();
			$restNumRev = $this->db->query($sqlNoRev)->num_rows();

			if($restNumRev > 0){
				$revised_no = $restNoRev[0]['revised_no'] + 1;
			}
			else{
				$revised_no = 0;
			}

			$sqlRevised 	= SQL_Revised_EXQTY($id_bq); 
			$restRevised 	= $this->db->query($sqlRevised)->result_array();
			$ArrDetRevised 	= array();
			$SUM_est_material 				= 0;
			$SUM_est_harga 					= 0;
			$SUM_direct_labour 				= 0;
			$SUM_indirect_labour 			= 0;
			$SUM_machine 					= 0;
			$SUM_mould_mandrill 			= 0;
			$SUM_consumable	 				= 0;
			$SUM_foh_consumable 			= 0;
			$SUM_foh_depresiasi 			= 0;
			$SUM_biaya_gaji_non_produksi 	= 0;
			$SUM_biaya_non_produksi 		= 0;
			$SUM_biaya_rutin_bulanan 		= 0;
			
			$EXQTY_SUM_est_material 				= 0;
			$EXQTY_SUM_est_harga 					= 0;
			$EXQTY_SUM_direct_labour 				= 0;
			$EXQTY_SUM_indirect_labour 			= 0;
			$EXQTY_SUM_machine 					= 0;
			$EXQTY_SUM_mould_mandrill 			= 0;
			$EXQTY_SUM_consumable	 				= 0;
			$EXQTY_SUM_foh_consumable 			= 0;
			$EXQTY_SUM_foh_depresiasi 			= 0;
			$EXQTY_SUM_biaya_gaji_non_produksi 	= 0;
			$EXQTY_SUM_biaya_non_produksi 		= 0;
			$EXQTY_SUM_biaya_rutin_bulanan 		= 0;
			foreach($restRevised AS $val => $valx){
				$SUM_est_material 				+= $valx['est_material'] * $valx['qty'];
				$SUM_est_harga 					+= $valx['est_harga'] * $valx['qty'];
				$SUM_direct_labour 				+= $valx['direct_labour'] * $valx['qty'];
				$SUM_indirect_labour 			+= $valx['indirect_labour'] * $valx['qty'];
				$SUM_machine 					+= $valx['machine'] * $valx['qty'];
				$SUM_mould_mandrill 			+= $valx['mould_mandrill'] * $valx['qty'];
				$SUM_consumable 				+= $valx['consumable'] * $valx['qty'];
				$SUM_foh_consumable 			+= $valx['foh_consumable'] * $valx['qty'];
				$SUM_foh_depresiasi 			+= $valx['foh_depresiasi'] * $valx['qty'];
				$SUM_biaya_gaji_non_produksi 	+= $valx['biaya_gaji_non_produksi'] * $valx['qty'];
				$SUM_biaya_non_produksi 		+= $valx['biaya_non_produksi'] * $valx['qty'];
				$SUM_biaya_rutin_bulanan 		+= $valx['biaya_rutin_bulanan'] * $valx['qty'];
				
				$EXQTY_SUM_est_material 			+= $valx['est_material'];
				$EXQTY_SUM_est_harga 				+= $valx['est_harga'];
				$EXQTY_SUM_direct_labour 			+= $valx['direct_labour'];
				$EXQTY_SUM_indirect_labour 			+= $valx['indirect_labour'];
				$EXQTY_SUM_machine 					+= $valx['machine'];
				$EXQTY_SUM_mould_mandrill 			+= $valx['mould_mandrill'];
				$EXQTY_SUM_consumable 				+= $valx['consumable'];
				$EXQTY_SUM_foh_consumable 			+= $valx['foh_consumable'];
				$EXQTY_SUM_foh_depresiasi 			+= $valx['foh_depresiasi'];
				$EXQTY_SUM_biaya_gaji_non_produksi 	+= $valx['biaya_gaji_non_produksi'];
				$EXQTY_SUM_biaya_non_produksi 		+= $valx['biaya_non_produksi'];
				$EXQTY_SUM_biaya_rutin_bulanan 		+= $valx['biaya_rutin_bulanan'];

				$sqlTambahan 	= "SELECT `length`, thickness, sudut, id_standard, `type` FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id = '".$valx['id_milik']."' LIMIT 1 ";
				$restTambahan 	= $this->db->query($sqlTambahan)->result_array();

				$ArrDetRevised[$val]['id_bq'] = $valx['id_bq'];
				$ArrDetRevised[$val]['id_milik'] = $valx['id_milik'];
				$ArrDetRevised[$val]['product_parent'] = $valx['parent_product'];
				$ArrDetRevised[$val]['id_product'] = $valx['id_product'];
				$ArrDetRevised[$val]['series'] = $valx['series'];
				$ArrDetRevised[$val]['diameter'] = $valx['diameter'];
				$ArrDetRevised[$val]['diameter2'] = $valx['diameter2'];
				$ArrDetRevised[$val]['length'] = $restTambahan[0]['length'];
				$ArrDetRevised[$val]['thickness'] = $restTambahan[0]['thickness'];
				$ArrDetRevised[$val]['sudut'] = $restTambahan[0]['sudut'];
				$ArrDetRevised[$val]['id_standard'] = $restTambahan[0]['id_standard'];
				$ArrDetRevised[$val]['type'] = $restTambahan[0]['type'];
				$ArrDetRevised[$val]['pressure'] = $valx['pressure'];
				$ArrDetRevised[$val]['liner'] = $valx['liner'];
				$ArrDetRevised[$val]['qty'] = $valx['qty'];
				$ArrDetRevised[$val]['est_material'] = $valx['est_material'] * $valx['qty'];
				$ArrDetRevised[$val]['est_harga'] = $valx['est_harga'] * $valx['qty'];
				$ArrDetRevised[$val]['direct_labour'] = $valx['direct_labour'] * $valx['qty'];
				$ArrDetRevised[$val]['indirect_labour'] = $valx['indirect_labour'] * $valx['qty'];
				$ArrDetRevised[$val]['machine'] = $valx['machine'] * $valx['qty'];
				$ArrDetRevised[$val]['mould_mandrill'] = $valx['mould_mandrill'] * $valx['qty'];
				$ArrDetRevised[$val]['consumable'] = $valx['consumable'] * $valx['qty'];
				$ArrDetRevised[$val]['foh_consumable'] = $valx['foh_consumable'] * $valx['qty'];
				$ArrDetRevised[$val]['foh_depresiasi'] = $valx['foh_depresiasi'] * $valx['qty'];
				$ArrDetRevised[$val]['biaya_gaji_non_produksi'] = $valx['biaya_gaji_non_produksi'] * $valx['qty'];
				$ArrDetRevised[$val]['biaya_non_produksi'] = $valx['biaya_non_produksi'] * $valx['qty'];
				$ArrDetRevised[$val]['biaya_rutin_bulanan'] = $valx['biaya_rutin_bulanan'] * $valx['qty'];
					$unitPriceX = (	$valx['est_harga']
									+$valx['direct_labour']
									+$valx['indirect_labour']
									+$valx['machine']
									+$valx['mould_mandrill']
									+$valx['consumable']
									+$valx['foh_consumable']
									+$valx['foh_depresiasi']
									+$valx['biaya_gaji_non_produksi']
									+$valx['biaya_non_produksi']
									+$valx['biaya_rutin_bulanan']);
				$ArrDetRevised[$val]['unit_price'] = $unitPriceX;
				$ArrDetRevised[$val]['profit'] = $valx['profit'];
					$unitProfitX = $unitPriceX *($valx['profit']/100);
					$unitAllowanceX = (($unitPriceX) + ($unitProfitX)) * $valx['qty'];
				$ArrDetRevised[$val]['total_price'] = $unitAllowanceX;
				$ArrDetRevised[$val]['allowance'] = $valx['allowance'];
					$unitAllowanceLast = (($unitAllowanceX) + ($unitAllowanceX * ($valx['allowance']/100)));
				$ArrDetRevised[$val]['total_price_last'] = $unitAllowanceLast;
				$ArrDetRevised[$val]['man_power'] = $valx['man_power'];
				$ArrDetRevised[$val]['id_mesin'] = $valx['id_mesin'];
				$ArrDetRevised[$val]['total_time'] = $valx['total_time'];
				$ArrDetRevised[$val]['man_hours'] = $valx['man_hours'];
				$ArrDetRevised[$val]['pe_direct_labour'] = $valx['pe_direct_labour'];
				$ArrDetRevised[$val]['pe_indirect_labour'] = $valx['pe_indirect_labour'];
				$ArrDetRevised[$val]['pe_machine'] = $valx['pe_machine'];
				$ArrDetRevised[$val]['pe_mould_mandrill'] = $valx['pe_mould_mandrill'];
				$ArrDetRevised[$val]['pe_consumable'] = $valx['pe_consumable'];
				$ArrDetRevised[$val]['pe_foh_consumable'] = $valx['pe_foh_consumable'];
				$ArrDetRevised[$val]['pe_foh_depresiasi'] = $valx['pe_foh_depresiasi'];
				$ArrDetRevised[$val]['pe_biaya_gaji_non_produksi'] = $valx['pe_biaya_gaji_non_produksi'];
				$ArrDetRevised[$val]['pe_biaya_non_produksi'] = $valx['pe_biaya_non_produksi'];
				$ArrDetRevised[$val]['pe_biaya_rutin_bulanan'] = $valx['pe_biaya_rutin_bulanan'];
				$ArrDetRevised[$val]['revised_no'] = $revised_no;
				$ArrDetRevised[$val]['insert_by'] = $data_session['ORI_User']['username'];
				$ArrDetRevised[$val]['insert_date'] = date('Y-m-d H:i:s');
			}

			//Insert Header Report Revised
			$sqlRevisedHead 	= "SELECT id_customer, nm_customer, project FROM production WHERE no_ipp='".str_replace('BQ-','',$id_bq)."' ";
			$restRevisedHead 	= $this->db->query($sqlRevisedHead)->result_array();

			$sqlTotPro 		= "SELECT price_project FROM cost_project_header WHERE id_bq='".$id_bq."' ";
			$restsqlTotPro 	= $this->db->query($sqlTotPro)->result_array();
			$restNumTotPro 	= $this->db->query($sqlTotPro)->num_rows();

			if($restNumTotPro > 0){
				$totProject = $restsqlTotPro[0]['price_project'];
			}
			else{
				$totProject = 0;
			}
			
			$ArrHeadRevised = array(
				'id_bq' => $id_bq,
				'id_customer' => $restRevisedHead[0]['id_customer'],
				'nm_customer' => $restRevisedHead[0]['nm_customer'],
				'nm_project' => $restRevisedHead[0]['project'],
				'revised_no' => $revised_no,
				'price_project' => $totProject,
				'est_material' => $SUM_est_material,
				'est_harga' => $SUM_est_harga,
				'direct_labour' => $SUM_direct_labour,
				'indirect_labour' => $SUM_indirect_labour,
				'machine' => $SUM_machine,
				'mould_mandrill' => $SUM_mould_mandrill,
				'consumable' => $SUM_consumable,
				'foh_consumable' => $SUM_foh_consumable,
				'foh_depresiasi' => $SUM_foh_depresiasi,
				'biaya_gaji_non_produksi' => $SUM_biaya_gaji_non_produksi,
				'biaya_non_produksi' => $SUM_biaya_non_produksi,
				'biaya_rutin_bulanan' => $SUM_biaya_rutin_bulanan,
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s')
			);
			
			$ArrUpdateHarga = array(
				'price_project' => $totProject,
				'est_material' => $EXQTY_SUM_est_material,
				'est_harga' => $EXQTY_SUM_est_harga,
				'direct_labour' => $EXQTY_SUM_direct_labour,
				'indirect_labour' => $EXQTY_SUM_indirect_labour,
				'machine' => $EXQTY_SUM_machine,
				'mould_mandrill' => $EXQTY_SUM_mould_mandrill,
				'consumable' => $EXQTY_SUM_consumable,
				'foh_consumable' => $EXQTY_SUM_foh_consumable,
				'foh_depresiasi' => $EXQTY_SUM_foh_depresiasi,
				'biaya_gaji_non_produksi' => $EXQTY_SUM_biaya_gaji_non_produksi,
				'biaya_non_produksi' => $EXQTY_SUM_biaya_non_produksi,
				'biaya_rutin_bulanan' => $EXQTY_SUM_biaya_rutin_bulanan
			);

			//Insert Header Report Etc
			$sqlRevisedEtc 		= "SELECT * FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category <> 'material' ";
			$restRevisedEtc 	= $this->db->query($sqlRevisedEtc)->result_array();
			$restNumRevisedEtc 	= $this->db->query($sqlRevisedEtc)->num_rows();
			
			if($restNumRevisedEtc > 0){
				$ArrEtcRevised = array();
				foreach($restRevisedEtc AS $val => $valx){
					$ArrEtcRevised[$val]['id_bq'] = $valx['id_bq'];
					$ArrEtcRevised[$val]['category'] = $valx['category'];
					$ArrEtcRevised[$val]['caregory_sub'] = $valx['caregory_sub'];
					$ArrEtcRevised[$val]['option_type'] = $valx['option_type'];
					$ArrEtcRevised[$val]['area'] = $valx['area'];
					$ArrEtcRevised[$val]['tujuan'] = $valx['tujuan'];
					$ArrEtcRevised[$val]['kendaraan'] = $valx['kendaraan'];
					$ArrEtcRevised[$val]['unit'] = $valx['unit'];
					$ArrEtcRevised[$val]['qty'] = $valx['qty'];
					$ArrEtcRevised[$val]['fumigasi'] = $valx['fumigasi'];
					$ArrEtcRevised[$val]['price'] = $valx['price'];
					$ArrEtcRevised[$val]['price_total'] = $valx['price_total'];
					$ArrEtcRevised[$val]['revised_no'] = $revised_no;
					$ArrEtcRevised[$val]['insert_by'] = $data_session['ORI_User']['username'];
					$ArrEtcRevised[$val]['insert_date'] = date('Y-m-d H:i:s');
				}
			}
			// echo "<pre>";
			// print_r($ArrHeadRevised);
			// print_r($ArrDetRevised);
			// print_r($ArrEtcRevised);
			// exit;
			
			$HistSts = "Approve Project Price with BQ : ".$id_bq;
		}
		
		if($StatuxX == 'N'){
			$Arr_Edit	= array(
				'status' => 'WAITING STRUCTURE BQ',
				'sts_price_reason' => $this->input->post('approve_reason'),
				'sts_price_by' => $data_session['ORI_User']['username'],
				'sts_price_date' => date('Y-m-d H:i:s')
			);

			$ArrConfirm = array(
				'approved' 		=> 'N',
				'approved_by' 	=> $data_session['ORI_User']['username'],
				'approved_date' => date('Y-m-d H:i:s'),
				
				'aju_approved' 		=> 'N',
				'aju_approved_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_date' => date('Y-m-d H:i:s'),
				
				'approved_est' 		=> 'N',
				'approved_est_by' 	=> $data_session['ORI_User']['username'],
				'approved_est_date' => date('Y-m-d H:i:s'),
				
				'aju_approved_est' 		=> 'N',
				'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_est_date' => date('Y-m-d H:i:s')
			);	
			
			$ArrRevisi	= array(
				'revisi' 	=> $this->input->post('approve_reason')
			);
			
			$HistSts = "Reject Project Price to enggenering (BQ) with BQ : ".$id_bq;
		}

		if($StatuxX == 'X'){
			$Arr_Edit	= array(
				'status' => 'WAITING ESTIMATION PROJECT',
				'sts_price_reason' => $this->input->post('approve_reason'), 
				'sts_price_by' => $data_session['ORI_User']['username'],
				'sts_price_date' => date('Y-m-d H:i:s')
			);

			$ArrConfirm = array(
				'approved_est' 		=> 'N',
				'approved_est_by' 	=> $data_session['ORI_User']['username'],
				'approved_est_date' => date('Y-m-d H:i:s'),
				
				'aju_approved_est' 		=> 'N',
				'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_est_date' => date('Y-m-d H:i:s')
			);	
			
			$ArrRevisi	= array(
				'revisi' 	=> $this->input->post('approve_reason')
			);
			
			$HistSts = "Reject Project Price to enggenering (Est) with BQ : ".$id_bq;
		}
		//insert semua total harga di price
		
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $Imp[1]);
			$this->db->update('production', $Arr_Edit);
			
			if($StatuxX == 'Y'){
				$this->db->insert('bq_price_project', $ArrInsert);

				$this->db->insert('laporan_revised_header', $ArrHeadRevised);
				$this->db->insert_batch('laporan_revised_detail', $ArrDetRevised);
				if($restNumRevisedEtc > 0){
					$this->db->insert_batch('laporan_revised_etc', $ArrEtcRevised);
				}
				
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('bq_header', $ArrUpdateHarga);
			}
			if($StatuxX == 'N'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('bq_header', $ArrConfirm);
				
				if(!empty($restNoRev_eng)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where('revised_no', $revisi_eng);
					$this->db->update('laporan_costing_header', $ArrRevisi);
				}
			}
			if($StatuxX == 'X'){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('bq_header', $ArrConfirm);
				
				if(!empty($restNoRev_eng)){
					$this->db->where('id_bq', $id_bq);
					$this->db->where('revised_no', $revisi_eng);
					$this->db->update('laporan_costing_header', $ArrRevisi);
				}
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve success. Thanks ...',
				'status'	=> 1
			);				
			history($HistSts);
		}
		echo json_encode($Arr_Data);
	}
	
	public function approveMat(){
		$id_produk 	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$stsX	= ($this->input->post('status') == 'Y')?'REGISTERED':'UNREGISTERED';
		
		$sqlDet2		= "SELECT * FROM component_header WHERE id_product = '".$id_produk."' ";
		$restDet2	= $this->db->query($sqlDet2)->result();
		
		$Arr_Edit	= array(
			// 'rev'		=> $restDet2[0]->rev + 1,
			'sts_price' => $stsX,
			'sts_price_reason' => $this->input->post('approve_reason'),
			'sts_price_by' => $data_session['ORI_User']['username'],
			'sts_price_date' => date('Y-m-d H:i:s')
		);
		$Arr_Price	= array(
			'status' => $this->input->post('status'),
			'approve_by' => $data_session['ORI_User']['username'],
			'approve_date' => date('Y-m-d H:i:s')
		);
		// print_r($Arr_Edit);
		// print_r($Arr_Price);
		// exit;
		$this->db->trans_start();
		$this->db->where('id_product', $id_produk);
		$this->db->update('component_header', $Arr_Edit);
		
		$this->db->where('id_product', $id_produk);
		$this->db->update('component_price', $Arr_Price);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve success. Thanks ...',
				'status'	=> 1
			);				
			history('Approve/Reject Price Comp with Kode : '.$id_produk);
		}
		echo json_encode($Arr_Data);
	}
	
	public function printSPK(){
		$kode_product	= $this->uri->segment(3);
		// $kodeSPJ		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
	
		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		PrintSPKOri($Nama_Beda, $kode_product, $koneksi, $printby);
	}
	
	public function project(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/project";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT create_by, create_date FROM group_cost_project_table ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of COGS',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View index cogs');
		$this->load->view('Price/project',$data);
	}
	
	public function cost_control(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Cost Control',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Estimation Project Price');
		$this->load->view('Price/cost_control',$data);
	}
	
	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/project";
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
			$requestData['status'],
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode("<br>", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_mat'], 3)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_harga'], 2)."</div>";
			// $nestedData[]	= "<div align='right'><a id='detail_process_cost' style='cursor:pointer;' data-id_bq='".$row['id_bq']."'>".number_format($row['process_cost'], 2)."</a></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['process_cost'], 2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['process_cost'] + $row['est_harga'], 2)."</div>";
			
			$get_rev_cos = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_revised_header',array('id_bq'=>$row['id_bq']))->result();
			$rev_cos = (!empty($get_rev_cos[0]->revised))?$get_rev_cos[0]->revised:0;

			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#ce9021'>".$rev_cos."</span></div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND id_category <> 'pipe slongsong' AND (id_product= '' OR id_product  is null) ")->num_rows();
				
				// $class = Color_status($row['sts_ipp']);
			// $nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$updXCost	= "";
					$ApprvX	= "";
					$ApprvX2	= "";
					$viewX	= "";
					// if($Arr_Akses['update']=='1'){
						// $updX	= "&nbsp;<button class='btn btn-sm btn-success' id='editBQ' title='Estimation BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-edit'></i></button>";
					// }
					if($row['estimasi']=='Y'){
						$viewX	= "<button class='btn btn-sm btn-primary detail_data' title='View Data' data-id_bq='".$row['id_bq']."' data-cost_control='cost_control'><i class='fa fa-eye'></i></button>";
					}
					
					//ditabambahkan ini ya AND $Check == '0'
					if($row['estimasi'] == 'Y' AND $Check == '0' AND $row['sts_ipp'] == 'WAITING EST PRICE PROJECT'){
						if($Arr_Akses['approve']=='1'){
							$ApprvX	= "&nbsp;<button class='btn btn-sm btn-success data_approve' title='Approve Project Price' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						}
						// $ApprvX2	= "&nbsp;<a a href='".base_url('price/priceProcessCost/'.$row['id_bq'])."' class='btn btn-sm btn-danger'  title='Approve Project Price' ><i class='fa fa-close'></i></a>";
					}
					
					if($uri_code == 'cost_control'){
						$updXCost	= "&nbsp;<button class='btn btn-sm btn-warning' id='TotalCost' title='Total All Material' data-id_bq='".$row['id_bq']."'><i class='fa fa-money'></i></button>";
					}
					//<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
			$nestedData[]	= "<div align='center'>
									".$priX."
									".$viewX."
									".$updX."
									".$ApprvX."
									".$ApprvX2."
									".$updXCost."
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

	public function queryDataJSON($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_wait_est = "";
		$where_wait_est_plus = "";
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id_bq,
				a.no_ipp,
				a.estimasi,
				a.rev,
				a.order_type,
				a.nm_customer,
				a.sts_ipp,
				a.est_mat,
				a.est_harga,
				a.project,
				(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable + a.foh_consumable + a.foh_depresiasi + a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS process_cost
			FROM
				group_cost_project_table a,
				(SELECT @row:=0) r
		    WHERE 
				1=1
				".$where_wait_est."
				".$where_wait_est_plus."
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
			1 => 'nomor',
			2 => 'nm_customer',
			3 => 'project',
			4 => 'order_type'
		);

		// $sql .= " GROUP BY x.id_bq ORDER BY x.".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " ORDER BY a.no_ipp DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function priceReal(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('users'));
		}
		
		$id_produksi	= $this->uri->segment(3);
		$id_produksi = $this->uri->segment(3);

		$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row	= $this->db->query($qSupplier)->result_array();

		$qDetail	= "	SELECT 
							a.*, 
							b.nm_product,
							c.delivery_name
						FROM 
							production_detail a 
							LEFT JOIN bq_component_header b ON a.id_milik=b.id_milik 
							LEFT JOIN delivery c ON a.id_delivery=c.id_delivery 
						WHERE 
							a.id_produksi = '".$id_produksi."' ";
		$rowD	= $this->db->query($qDetail)->result_array();
		
		$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN bq_component_header b ON a.id_milik=b.id_milik WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
		$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();
		
		$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN bq_component_header b ON a.id_milik=b.id_milik WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
		$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
		// echo $qDetailBtn;
		$data = array(
			'title'		=> 'Price Estimasi Detail',
			'action'	=> 'updateReal',
			'row'		=> $row,
			'rowD'		=> $rowD,
			'numB'		=> $rowDBtn,
			'numB2'		=> $rowDBtn2
		);
		$this->load->view('Price/priceReal',$data);
	}
	
	public function priceProcessCost(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('users'));
		}
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		$qMatr 		= "SELECT a.*, b.* FROM estimasi_cost_and_mat a INNER JOIN bq_detail_header b ON a.id_milik=b.id WHERE a.id_bq = '".$id_bq."' ";
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT * FROM list_help WHERE group_by = 'eng cost' ORDER BY id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		
		$engCPC 	= "SELECT * FROM list_help WHERE group_by = 'pack cost' ORDER BY id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		$gTruck 	= "SELECT * FROM list_shipping WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$gTruckP 	= "SELECT * FROM list_packing WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruckP	= $this->db->query($gTruckP)->result_array();
		
		$engCPCV 	= "SELECT * FROM list_help WHERE group_by = 'via' ORDER BY id ASC ";
		$rowengCPCV	= $this->db->query($engCPCV)->result_array();
		
		$qOpt 		= "SELECT * FROM list_help WHERE group_by = 'opt' ORDER BY id DESC ";
		$getOpt		= $this->db->query($qOpt)->result_array();
		
		$qOptPl 	= "SELECT * FROM list_help WHERE group_by = 'opt' OR group_by = 'opt plus' ORDER BY id DESC ";
		$getOptPl	= $this->db->query($qOptPl)->result_array();
		
		$qArea		= "SELECT area FROM cost_trucking WHERE category='darat' GROUP BY area ORDER BY area ASC ";
		$getArea	= $this->db->query($qArea)->result_array();
		
		$qAreaL		= "SELECT area FROM cost_trucking WHERE category='laut' GROUP BY area ORDER BY area ASC ";
		$getAreaL	= $this->db->query($qAreaL)->result_array();
		
		
		$data = array(
			'title'		=> 'Offer Structure',
			'action'	=> 'updateReal',
			'getHeader'		=> $row,
			'getDetail'		=> $rowDet,
			'getEngCost'	=> $rowengC,
			'getPackCost'	=> $rowengCPC,
			'getPackP'	=> $rowgTruckP,
			'getTruck'		=> $rowgTruck,
			'getVia'		=> $rowengCPCV,
			'getOpt'		=> $getOpt,
			'getOptP'		=> $getOptPl,
			'getArea'		=> $getArea,
			'getAreaL'		=> $getAreaL
		);
		$this->load->view('Price/priceProcessCost',$data);
	}
	
	public function modalDetailPrice(){
		$this->load->view('Price/modalDetailPrice');
	}
	
	public function modalDetailPriceDetail(){
		$this->load->view('Price/modalDetailPriceDetail');
	}
	
	public function modalDetailBQ(){
		$this->load->view('Price/modalDetailBQ');
	}

	public function agus_modalDetailMat($id_product,$id_milik,$qty,$length,$id_bq){
		$ArrWhere1 = array(
			'id_product' => $id_product,
			'id_milik' => $id_milik,
			'id_bq' => $id_bq,
			'id_material <>' => 'MTL-1903000',
			'id_category <>' => 'TYP-0001'
		);
		$ArrWhere2 = array(
			'id_product' => $id_product,
			'id_milik' => $id_milik,
			'id_bq' => $id_bq,
			'id_material <>' => 'MTL-1903000'
		);
		$ArrWhere3 = array(
			'id_product' => $id_product,
			'id_milik' => $id_milik,
			'id_bq' => $id_bq,
			'id_material <>' => 'MTL-1903000',
			'id_category' => 'TYP-0001'
		);
		$getDetail1 = $this->db->get_where('bq_component_detail',$ArrWhere1)->result_array();
		$ArrDetail1 = [];
		foreach($getDetail1 AS $valx => $value){
			$ArrDetail1[$value['detail_name']][] = $value;
		}
		$getPlus1 = $this->db->get_where('bq_component_detail_plus',$ArrWhere2)->result_array();
		$ArrPlus1 = [];
		foreach($getPlus1 AS $valx => $value){
			$ArrPlus1[$value['detail_name']][] = $value;
		}
		$getAdd1 = $this->db->get_where('bq_component_detail_add',$ArrWhere2)->result_array();
		$ArrAdd1 = [];
		foreach($getAdd1 AS $valx => $value){
			$ArrAdd1[$value['detail_name']][] = $value;
		}
		$getResinMax 	= $this->db->select('MAX(id_detail) AS id_detail')->group_by('detail_name')->get_where('bq_component_detail',$ArrWhere3)->result_array();
		$wherein = [];
		foreach($getResinMax AS $val => $value){
			$wherein[] = $value['id_detail'];
		}
		$ArrResin1 = [];
		if(!empty($wherein)){
			$getResin1 		= $this->db->select('*')->from('bq_component_detail')->where_in('id_detail',$wherein)->get()->result_array();
			foreach($getResin1 AS $valx => $value){
				$ArrResin1[$value['detail_name']][] = $value;
			}
		}
		$restHeader		= $this->db->get_where('bq_component_header',array('id_product'=>$id_product,'id_milik'=>$id_milik,'id_bq'=>$id_bq))->result_array();
		$ArrData = [
			'restHeader' => $restHeader,
			'restDetail' => $ArrDetail1,
			'restDetailPlus' => $ArrPlus1,
			'restDetailAdd' => $ArrAdd1,
			'restResin' => $ArrResin1
		];
		$this->load->view('Price/modalDetailMat',$ArrData);
	}
	
	public function agus_modalviewDT(){
		$id_bq 		= $this->uri->segment(3);
		$sql 		= "	SELECT id_product,id,id_bq,qty,length FROM bq_detail_header WHERE id_bq = '".$id_bq."' ORDER BY  id ASC";
		$result		= $this->db->query($sql)->result();
		$ArrData = [
			'result' => $result,
		];
		$this->load->view('Price/agusDetailMat',$ArrData);
/*
		$i=0;
		$dataview=array();
		foreach ($result as $keys=>$val){
			$i++;
			//echo $i." . ".$val->id_product." # ".$val->id." # ".$val->id_bq."<br >";
			$dataview[]=$this->agus_modalDetailMat($val->id_product,$val->id,$val->id_bq);
		}
		print_r($dataview);	
*/
	}
	
	public function modalviewDT(){
		$id_bq 		= $this->uri->segment(3);
		$tanda_cost = $this->uri->segment(4); 

		// $sql 		= "	SELECT
		// 					a.id_milik,
		// 					a.id_bq,
		// 					b.parent_product AS id_category,
		// 					a.qty,
		// 					b.diameter AS diameter_1,
		// 					b.diameter2 AS diameter_2,
		// 					b.panjang AS length,
		// 					b.thickness,
		// 					b.angle AS sudut,
		// 					b.type,
		// 					a.id_product,
		// 					b.standart_code,
		// 					( a.est_harga * a.qty ) AS est_harga2,
		// 					( a.sum_mat * a.qty ) AS sum_mat2,
		// 					b.pressure,
		// 					b.liner,
		// 					a.man_power,
		// 					a.man_hours,
		// 					a.id_mesin,
		// 					a.total_time,
		// 					(a.direct_labour * a.qty) AS direct_labour,
		// 					(a.indirect_labour * a.qty) AS indirect_labour,
		// 					(a.machine * a.qty) AS machine,
		// 					(a.mould_mandrill * a.qty) AS mould_mandrill,
		// 					(a.consumable * a.qty) AS consumable,
		// 					(
		// 						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) * `a`.`qty` 
		// 					) AS `cost_process`,
		// 					(
		// 						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
		// 					) * ( (b.pe_foh_consumable) / 100 ) * a.qty AS foh_consumable,
		// 					(
		// 						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
		// 					) * ( (b.pe_foh_depresiasi) / 100 ) * a.qty AS foh_depresiasi,
		// 					(
		// 						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
		// 					) * ( (b.pe_biaya_gaji_non_produksi) / 100 ) * a.qty AS biaya_gaji_non_produksi,
		// 					(
		// 						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
		// 					) * ( (b.pe_biaya_non_produksi) / 100 ) * a.qty AS biaya_non_produksi,
		// 					(
		// 						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
		// 					) * ( (b.pe_biaya_rutin_bulanan) / 100 ) * a.qty AS biaya_rutin_bulanan 
		// 				FROM
		// 					estimasi_cost_and_mat a
		// 					INNER JOIN bq_product b ON a.id_milik = b.id
		// 				WHERE
		// 					b.parent_product <> 'pipe slongsong' 
		// 					AND b.parent_product <> 'product kosong' 
		// 					AND a.id_bq = '".$id_bq."' 
		// 				ORDER BY a.id_milik ASC";
		$sql 		= "	SELECT 
							a.id,
							a.id_category,
							a.length,
							a.id_product,
							a.qty,
							a.man_power AS man_power,
							a.id_mesin AS id_mesin,
							a.total_time AS total_time,
							a.man_hours AS man_hours,
							a.pe_direct_labour,
							a.pe_indirect_labour,
							a.pe_machine,
							ifnull( a.pe_mould_mandrill, 0 ) AS pe_mould_mandrill,
							a.pe_consumable,
							a.pe_foh_consumable,
							a.pe_foh_depresiasi,
							a.pe_biaya_gaji_non_produksi,
							a.pe_biaya_non_produksi,
							a.pe_biaya_rutin_bulanan

						FROM 
							bq_detail_header a 
						WHERE 
							a.id_category <> 'pipe slongsong' 
							AND a.id_category <> 'product kosong' 
							AND a.id_bq = '$id_bq' 
						ORDER BY 
							a.id ASC";
		$result		= $this->db->query($sql)->result_array();
		
		// $detail 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'acc'))->result_array();
		$detail2 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'mat'))->result_array();
		$detail3 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'baut'))->result_array();
		$detail4 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'plate'))->result_array();
		$detail4g 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'gasket'))->result_array();
		$detail5 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'lainnya'))->result_array();
		
		$data = array(
			'id_bq' 		=> $id_bq,
			'tanda_cost' 	=> $tanda_cost,
			'result' 		=> $result,
			// 'detail' 		=> $detail,
			'detail2'		=> $detail2,
			'detail3'		=> $detail3,
			'detail4'		=> $detail4,
			'detail4g'		=> $detail4g,
			'detail5'		=> $detail5,
			'GET_DET_ACC' => get_detail_accessories()
		);
		
		// $this->load->view('Price/modalviewDT', $data);
		$this->load->view('Price/modalviewDT_Fast', $data);
	}
	
	public function modalTotalCost(){
		$this->load->view('Price/modalTotalCost');
	}
	
	public function modalDetailDT(){
		$this->load->view('Price/modalDetailDT');
	}
	
	public function printPriceperMat(){ 
		$kode_produksi	= $this->uri->segment(3);
		$kode_product	= $this->uri->segment(4);
		$product_to		= $this->uri->segment(5);
		$id_delivery	= $this->uri->segment(6);
		$id				= $this->uri->segment(7);
		$id_milik		= $this->uri->segment(8);
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");
		// echo $data_url;
		// exit;
		
		// $sqlUpdate = "UPDATE production_detail SET print_to=print_to +1 WHERE id='".$id."'";
		// $this->db->query($sqlUpdate);
		// echo $sqlUpdate; exit;
		// history('Print SPK Production '.$kode_produksi.'/'.$kode_product); 
		
		PrintPricePerComp($Nama_Beda, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery, $id, $id_milik);
	}
	
	public function printCostControl(){ 
		$id_product	= $this->uri->segment(3);
		$id_milik	= $this->uri->segment(4);
		$id_bq	= $this->uri->segment(5);
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		printCostControl($Nama_Beda, $id_product, $koneksi, $printby, $id_milik, $id_bq);
	}
	
	public function PrintHasilProject(){ 
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Perbandingan Project'); 
		
		PrintHasilProject($Nama_Beda, $koneksi, $printby);
	}
	
	public function PrintHasilProjectPerBQ(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Perbandingan Project BQ '.$id_bq); 
		
		PrintHasilProjectPerBQ($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function print_project_costing(){
		$id_bq			= $this->uri->segment(3);
		$id_bq_ex		= explode('-', $id_bq);
		$no_ipp 		= $id_bq_ex[1];
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id_bq' => $id_bq,
			'no_ipp' => $no_ipp
		);
		history('Print Hasil Perbandingan Project BQ '.$id_bq); 
		$this->load->view('Print/print_project_costing', $data);
	}
	
	public function PrintTotalMaterial(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Total Material Project BQ '.$id_bq); 
		
		PrintTotalMaterial($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function updatePrice(){
		$ArrKembali = array();
		$data		= $this->input->post();
		$dataID		= $data['id_product'];
		$priceMat	= str_replace(',', '', $data['product_price']);
		
		$sqlDet		= "SELECT * FROM component_price WHERE id_product = '".$dataID."' ";
		$restDet	= $this->db->query($sqlDet)->result();
		$numDet		= $this->db->query($sqlDet)->num_rows();
		
		$sqlDet2		= "SELECT * FROM component_header WHERE id_product = '".$dataID."' ";
		$restDet2	= $this->db->query($sqlDet2)->result();
		
		// echo $numDet;
		
		$dataInsert = array (
			'id_product'	=> $dataID,
			'product_price' => $priceMat,
			'revisi_ke'		=> 0,
			'modified_by'	=> $this->session->userdata['ORI_User']['username'],
			'modified_date'	=> date('Y-m-d H:i:s')
		);
		if($numDet > 0){
			$dataUpdate = array (
				'id_product'	=> $dataID,
				'product_price' => $priceMat,
				'revisi_ke'		=> $restDet[0]->revisi_ke + 1,
				'modified_by'	=> $this->session->userdata['ORI_User']['username'],
				'modified_date'	=> date('Y-m-d H:i:s')
			);
		}
		
		$ArrUpdate	= array(
			
			'sts_price'		=> 'WAITING APPROVE',
			'sts_price_by'	=> $this->session->userdata['ORI_User']['username'],
			'sts_price_date'	=> date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
			if($numDet == 0){
				$this->db->insert('component_price', $dataInsert);
				$this->db->insert('hist_component_price', $dataInsert);
			}
			else{
				$this->db->insert('hist_component_price', $dataUpdate);
				
				$this->db->where('id_product', $dataID);
				$this->db->update('component_price', $dataUpdate);
				
				$this->db->where('id_product', $dataID);
				$this->db->update('component_header', $ArrUpdate);
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Add price data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Add price data success. Thanks ...',
				'status'	=> 1
			);
			history("Add Price ID ".$dataID);
		}
		
		echo json_encode($Arr_Kembali);
	}
	
	//Pengajuan Harga Material Per Komponent
	public function getDataJSONKomp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/ajukan_price";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONKomp(
			$requestData['product'], 
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
				$detail = "";
				if(strtolower($row['parent_product']) == 'pipe'){
					$detail = "(".$row['diameter']." x ".$row['panjang']." x ".$row['design'].")";
				}
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>"; 
			$nestedData[]	= "<div align='left'>".$row['nm_product']." ".$detail."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['standart_toleransi']."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['aplikasi_product']."</div>"; 
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($row['created_by']))."</div>";   
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['rev']."</span></div>";
				if($row['sts_price'] == 'REGISTERED'){
					$warna = 'bg-green';
				}
				elseif($row['sts_price'] == 'UNREGISTERED'){
					$warna = 'bg-red';
				}
				else{
					$warna = 'bg-blue';
				}
			$nestedData[]	= "<div align='left'><span class='badge ".$warna."'>".$row['sts_price']."</span></div>";
					$delX	= "";
					$updX	= "";
					$priX	= "";
					if($row['sts_price'] == 'UNREGISTERED'){
						if($Arr_Akses['update']=='1'){
							$updX	= "&nbsp;<button type='button' id='MatPrice' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-success' title='Registered Now' data-role='qtip'><i class='fa fa-edit'></i></button>";
						}
					}
			$nestedData[]	= "<div align='left'>
									<button type='button' id='MatDetail' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$priX."
									".$updX."
									".$delX."
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

	public function queryDataJSONKomp($product, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_product = "";
		if(!empty($product)){
			$where_product = " AND a.parent_product = '".$product."' ";
		}
		
		$sql = "
			SELECT 
				a.*, b.nm_customer 
			FROM 
				component_header a 
				LEFT JOIN customer b ON b.id_customer=a.standart_by  
			WHERE 1=1 
				".$where_product."
				AND a.status='APPROVED' AND a.deleted ='N' AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_product LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'nm_product',
			3 => 'standart_toleransi',
			4 => 'aplikasi_product',
			5 => 'created_by',
			6 => 'rev'
		);

		$sql .= " ORDER BY a.sts_price DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	
	//======================================================EXCEL===============================================================
	public function ExcelPerbandingan(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_product		= $this->uri->segment(3);
		$id_produksi	= $this->uri->segment(4);
		$id_delivery	= $this->uri->segment(5);
		$id_production	= $this->uri->segment(6);
		$produk_ke		= $this->uri->segment(7);
		$nama_produk	= $this->uri->segment(8);
		$id_milik		= $this->uri->segment(9);

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
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'CCFF99'),
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
				'color' => array('rgb'=>'FFB266'),
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
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'PERBANDINGAN ESTIMASI VS ACTUAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Layer');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Category Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Material Name');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Price (USD)');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Est Total');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Est Sub (USD)');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Real Total');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Real Sub (USD)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);
		
		$qDetail1		= "SELECT * FROM banding_mat WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND id_material <> 'MTL-1903000' AND id_detail = '".$id_production."' ORDER BY detail_name ASC";
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		// echo $qDetail1; exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost	= $row_Cek['cost'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $row_Cek['est_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga	= $row_Cek['est_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $row_Cek['real_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga	= $row_Cek['real_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		
		
		$sheet->setTitle('Perbandingan');
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
		header('Content-Disposition: attachment;filename="Perbandingan_material_'.$nama_produk.'_produk_ke'.$produk_ke.'_'.$id_produksi.'_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	
	public function getFumigasi(){
		$category 	= $this->input->post('category');
		$type 		= $this->input->post('type');
		
		echo $category."-".$type;
		exit;
		
		$sqlSup		= "SELECT * FROM list_shipping WHERE shipping_name='N' AND  ORDER BY category ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Category</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_category']."'>".$valx['category']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getTujuan(){
		$data1 		= $this->input->post('data1');

		$sqlSup		= "SELECT tujuan FROM cost_trucking WHERE area='".$data1."' GROUP BY tujuan ORDER BY tujuan ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Destination</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['tujuan']."'>".strtoupper($valx['tujuan'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	
	public function getTruck(){
		$data1 		= $this->input->post('data1');
		$data2 		= $this->input->post('data2');

		$sqlSup		= "SELECT a.id_truck, b.nama_truck FROM cost_trucking a INNER JOIN truck b ON a.id_truck = b.id WHERE a.area='".$data1."' AND a.tujuan='".$data2."' GROUP BY id_truck ORDER BY b.nama_truck ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Truck</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_truck']."'>".strtoupper($valx['nama_truck'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getPriceTruck(){
		$data1 		= $this->input->post('data1');
		$data2 		= $this->input->post('data2');
		$data3 		= $this->input->post('data3');

		$sqlSup		= "SELECT price FROM cost_trucking WHERE area='".$data1."' AND tujuan='".$data2."' AND id_truck='".$data3."' LIMIT 1";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$ArrJson	= array(
			'price' => $restSup[0]['price']
		);
		echo json_encode($ArrJson);
	}
	
	public function save_cost_project(){
		$data_session	= $this->session->userdata;
		$data			= $this->input->post();
		
		$MatCost	= $data['MatCost'];
		$EngCost	= $data['EngCost'];
		$PackCost	= $data['PackCost'];
		$ExportCost	= $data['ExportCost'];
		$LokalCost	= $data['LokalCost'];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'price_project' => $data['total_all'],
			'created_by' 	=> $data_session['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrMatCost = array();
		foreach($MatCost AS $val => $valx){
			$ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			$ArrMatCost[$val]['category']		= $valx['category'];
			$ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			$ArrMatCost[$val]['persen']			= $valx['persen'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= $valx['unit'];
			$ArrEngCost[$val]['price']			= $valx['price'];
			$ArrEngCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrPackCost = array();
		foreach($PackCost AS $val => $valx){
			$ArrPackCost[$val]['id_bq']			= $data['id_bq'];
			$ArrPackCost[$val]['category']		= $valx['category'];
			$ArrPackCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrPackCost[$val]['option_type']	= $valx['option_type'];
			$ArrPackCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrExportCost = array();
		foreach($ExportCost AS $val => $valx){
			$ArrExportCost[$val]['id_bq']			= $data['id_bq'];
			$ArrExportCost[$val]['category']		= $valx['category'];
			$ArrExportCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrExportCost[$val]['option_type']		= $valx['option_type'];
			$ArrExportCost[$val]['qty']				= $valx['qty'];
			$ArrExportCost[$val]['price']			= $valx['price'];
			$ArrExportCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrLokalCost = array();
		foreach($LokalCost AS $val => $valx){
			$ArrLokalCost[$val]['id_bq']			= $data['id_bq'];
			$ArrLokalCost[$val]['category']			= $valx['category'];
			$ArrLokalCost[$val]['caregory_sub']		= $valx['caregory_sub'];
			$ArrLokalCost[$val]['area']				= $valx['area'];
			$ArrLokalCost[$val]['tujuan']			= $valx['tujuan'];
			$ArrLokalCost[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrLokalCost[$val]['qty']				= $valx['qty'];
			$ArrLokalCost[$val]['price']			= $valx['price'];
			$ArrLokalCost[$val]['price_total']		= $valx['price_total'];
		}
		
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		// print_r($ArrExportCost);
		// print_r($LokalCost); 
		
		
		exit;
		
		$this->db->trans_start();
			$this->db->insert('cost_project_header', $ArrHeader);
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation success. Thanks ...',
				'status'	=> 1
			);				
			history('Cost Quotation with bq : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//DUPLIKAT
	public function modalDetail2(){
		$this->load->view('Price/modalDetail2');
	}
	
	public function getDataJSONKomp2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONKomp2(
			// $requestData['product'], 
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
				$detail = "";
				if(strtolower($row['parent_product']) == 'pipe'){
					$detail = "(".$row['diameter']." x ".$row['panjang']." x ".$row['design'].")";
				}
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['id_product']."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['nm_product']." ".$detail."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['standart_toleransi']."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['aplikasi_product']."</div>"; 
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($row['created_by']))."</div>";   
				
					$delX	= "";
					$updX	= "";
					$priX	= "";
					
			$nestedData[]	= "<div align='center'>
									<button type='button' id='MatDetail' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$priX."
									".$updX."
									".$delX."
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

	public function queryDataJSONKomp2($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		
		$sql = "
			SELECT 
				a.*, b.nm_customer 
			FROM 
				product_header a 
				LEFT JOIN customer b ON b.id_customer=a.id_customer  
			WHERE id_product='PDK-19050030' OR id_product='PDK-19050031' OR id_product='PDK-19050032'
				
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'nm_product',
			3 => 'standart_toleransi',
			4 => 'aplikasi_product',
			5 => 'created_by',
		);

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	function insert_select(){ 
		$this->db->trans_start();
			$this->db->truncate('group_cost_project_table');
				
			$sqlUpdate = "
				INSERT INTO group_cost_project_table ( id_bq, no_ipp, estimasi, rev, order_type, nm_customer, sts_ipp, qty, est_harga, est_mat, direct_labour, indirect_labour, machine, mould_mandrill, consumable, process_cost, foh_consumable, foh_depresiasi, biaya_gaji_non_produksi, biaya_non_produksi, biaya_rutin_bulanan, project, create_by, create_date ) SELECT
					a.id_bq,
					a.no_ipp,
					a.estimasi,
					a.rev,
					a.order_type,
					a.nm_customer,
					a.sts_ipp,
					a.qty,
					a.est_harga,
					a.est_mat,
					a.direct_labour,
					a.indirect_labour,
					a.machine,
					a.mould_mandrill,
					a.consumable,
					a.process_cost,
					a.foh_consumable,
					a.foh_depresiasi,
					a.biaya_gaji_non_produksi,
					a.biaya_non_produksi,
					a.biaya_rutin_bulanan,
					a.project,
					'".$this->session->userdata['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
					FROM
						group_cost_project a";
			
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
			history('Success insert select group cost project');
		}
		echo json_encode($Arr_Data);
	}

	function update_cycle_time(){
		$data_session	= $this->session->userdata;
		$id_bq 			= $this->uri->segment(3);
		$id_milik 		= $this->uri->segment(4);

		$sqlHead 	= "SELECT * FROM bq_detail_header WHERE id='".$id_milik."' LIMIT 1 ";
		$restHead 	= $this->db->query($sqlHead)->result_array();
		$pro_par	= $restHead[0]['id_category'];
		$diameter_1	= $restHead[0]['diameter_1'];
		$diameter_2	= $restHead[0]['diameter_2'];
		$pn			= substr($restHead[0]['series'], 3,2);
		$liner		= substr($restHead[0]['series'], 6,3);

		// if($pro_par == '')
		$wherePlus = " AND diameter = '".$diameter_1."' ";
		if($pro_par == 'concentric reducer' OR $pro_par == 'eccentric reducer' OR $pro_par == 'reducer tee mould' OR $pro_par == 'reducer tee slongsong'){
			$wherePlus = " AND diameter = '".$diameter_1."' AND diameter2 = '".$diameter_2."' ";
		}
		if($pro_par == 'branch joint'){
			$wherePlus = " AND diameter2 = '".$diameter_2."' ";
		}

		$sqlCy 		= "SELECT * FROM cycletime_default WHERE product_parent='".$pro_par."' AND pn='".$pn."' AND liner='".$liner."' ".$wherePlus." LIMIT 1";
		// echo $sqlCy; exit;
		$restCy 	= $this->db->query($sqlCy)->result_array();
		// echo $sqlCy;exit;
		if(!empty($restCy)){
			$ArrWhere = array(
				'man_power' 	=> (!empty($restCy[0]['man_power']))?$restCy[0]['man_power']:'',
				'id_mesin' 		=> (!empty($restCy[0]['id_mesin']))?$restCy[0]['id_mesin']:'FW00',
				'total_time' 	=> (!empty($restCy[0]['total_time']))?$restCy[0]['total_time']:'',
				'man_hours' 	=> (!empty($restCy[0]['man_hours']))?$restCy[0]['man_hours']:'',
				'pe_direct_labour' 		=> pe_direct_labour(),
				'pe_indirect_labour' 	=> pe_indirect_labour(),
				'pe_consumable' 		=> pe_consumable($pro_par),
				'pe_mould_mandrill' 	=> pe_mould_mandrill($pro_par, $diameter_1, $diameter_2)
			);
			// print_r($ArrWhere);

			// echo $pro_par.'-'.$diameter_1.'-'.$diameter_2.'-'.$pn.'-'.$liner;
			// exit;
			$this->db->trans_start();
				$this->db->where(array('id_bq' => $id_bq, 'id' => $id_milik));
				$this->db->update('bq_detail_header', $ArrWhere);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update cycletime failed. Please try again later ...',
					'status'	=> 0,
					'id_bqx'	=> $id_bq
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update cycletime success. Thanks ...',
					'status'	=> 1,
					'id_bqx'	=> $id_bq
				);
				history("Change cycletime ".$id_bq." : ".$pro_par." id_milik ".$id_milik);
			}
		}
		else{
			$Arr_Data	= array(
				'pesan'		=>'Cycletime Empty. Please try again later ...',
				'status'	=> 0,
				'id_bqx'	=> $id_bq
			);
			history("Cycletime empty ".$id_bq." : ".$pro_par." id_milik ".$id_milik);
		}
		echo json_encode($Arr_Data);
	}

	function update_man_hours(){
		$data 		= $this->input->post();
		$id_bq 		= str_replace('BQ-','',$data['id_bq']);
		$id_milik 	= $data['id_milik'];
		$manpower 	= $data['manpower'];
		$manhours 	= $data['manhours'];
		$totaltime 	= 0;
		if($manhours > 0 AND $manpower > 0){
			$totaltime 	= $manhours/$manpower;
		}

		$ArrUpdate = array(
			'total_time' 	=> $totaltime,
			'man_hours' 	=> $manhours
		);
		// print_r($ArrUpdate);

		$this->db->trans_start();
			$this->db->where(array('id' => $id_milik));
			$this->db->update('bq_detail_header', $ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Failed',
				'warna'		=>'text-red',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Success!!!',
				'warna'		=>'text-green',
				'status'	=> 1
			);
			history("Change man hours ".$id_bq."/".$id_milik);
		}

		echo json_encode($Arr_Data);
	}

	function update_all_price(){ 
		$data_session	= $this->session->userdata;
		$id_bq 			= $this->uri->segment(3);

		$sqlCD 		= "SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_material <> 'MTL-1903000' ";
		$resCD 		= $this->db->query($sqlCD)->result_array();

		$sqlCDA 	= "SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_material <> 'MTL-1903000' ";
		$resCDA 	= $this->db->query($sqlCDA)->result_array();

		$sqlCDP 	= "SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_material <> 'MTL-1903000' ";
		$resCDP 	= $this->db->query($sqlCDP)->result_array();
		
		// $sqlMAT 	= "SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='mat' AND id_material <> 'MTL-1903000' ";
		// $resMAT 	= $this->db->query($sqlMAT)->result_array();
		
		// $sqlACC 	= "SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category <> 'mat' ";
		// $resACC 	= $this->db->query($sqlACC)->result_array();

		$ArrCD 		= array();
		$ArrCDA 	= array();
		$ArrCDP 	= array();
		$ArrMAT 	= array();
		$ArrACC 	= array();

		foreach($resCD AS $val => $valx){
			$ArrCD[$val]['id_detail'] = $valx['id_detail'];
			$ArrCD[$val]['price_mat'] = get_price_ref($valx['id_material']);
		}

		foreach($resCDA AS $val => $valx){
			$ArrCDA[$val]['id_detail'] = $valx['id_detail'];
			$ArrCDA[$val]['price_mat'] = get_price_ref($valx['id_material']);
		}

		foreach($resCDP AS $val => $valx){
			$ArrCDP[$val]['id_detail'] = $valx['id_detail'];
			$ArrCDP[$val]['price_mat'] = get_price_ref($valx['id_material']);
		}
		
		// foreach($resMAT AS $val => $valx){
		// 	$ArrMAT[$val]['id'] = $valx['id'];
		// 	$ArrMAT[$val]['unit_price'] = get_price_ref($valx['id_material']);
		// 	$ArrMAT[$val]['total_price'] = get_price_ref($valx['id_material']) * $valx['qty'];
		// }
		
		// foreach($resACC AS $val => $valx){
		// 	$qty = $valx['qty'];
		// 	if($valx['category'] == 'plate'){
		// 		$qty = $valx['berat'];
		// 	}
		// 	$ArrACC[$val]['id'] = $valx['id'];
		// 	$ArrACC[$val]['unit_price'] = get_price_acc($valx['id_material']);
		// 	$ArrACC[$val]['total_price'] = get_price_acc($valx['id_material']) * $qty;
		// }

		// print_r($ArrCD);
		// print_r($ArrCDP);
		// print_r($ArrCDA);
		// exit;
		
		$this->db->trans_start();
		if(!empty($ArrCD)){
			$this->db->update_batch('bq_component_detail', $ArrCD, 'id_detail');
		}
		if(!empty($ArrCDA)){
			$this->db->update_batch('bq_component_detail_add', $ArrCDA, 'id_detail');
		}
		if(!empty($ArrCDP)){
			$this->db->update_batch('bq_component_detail_plus', $ArrCDP, 'id_detail');
		}
		// if(!empty($ArrMAT)){
		// 	$this->db->update_batch('bq_acc_and_mat', $ArrMAT, 'id');
		// }
		// if(!empty($ArrACC)){
		// 	$this->db->update_batch('bq_acc_and_mat', $ArrACC, 'id');
		// }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update price failed. Please try again later ...',
				'status'	=> 0,
				'id_bqx'	=> $id_bq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update price success. Thanks ...',
				'status'	=> 1,
				'id_bqx'	=> $id_bq
			);
			history("Update all price reference product ".$id_bq);
		}
		echo json_encode($Arr_Data);
	}

	function update_all_price_non_frp(){ 
		$data_session	= $this->session->userdata;
		$id_bq 			= $this->uri->segment(3);
		
		$sqlACC 	= "SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category <> 'mat' ";
		$resACC 	= $this->db->query($sqlACC)->result_array();

		$ArrACC 	= array();
		foreach($resACC AS $val => $valx){
			$ID_ACC 	= $valx['id_material'];
			$getRef 	= get_price_aksesoris($ID_ACC);
			$price 		= $getRef['price'];

			$qty = $valx['qty'];
			if($valx['category'] == 'plate'){
				$qty = $valx['berat'];
			}
			$ArrACC[$val]['id'] = $valx['id'];
			$ArrACC[$val]['unit_price'] = $price;
			$ArrACC[$val]['total_price'] = $price * $qty;
			$ArrACC[$val]['expired_date']	= $getRef['expired'];
		}
		
		$this->db->trans_start();
		if(!empty($ArrACC)){
			$this->db->update_batch('bq_acc_and_mat', $ArrACC, 'id');
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update price failed. Please try again later ...',
				'status'	=> 0,
				'id_bqx'	=> $id_bq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update price success. Thanks ...',
				'status'	=> 1,
				'id_bqx'	=> $id_bq
			);
			history("Update all price reference non frp ".$id_bq);
		}
		echo json_encode($Arr_Data);
	}

	function update_all_price_material(){ 
		$data_session	= $this->session->userdata;
		$id_bq 			= $this->uri->segment(3);

		$sqlMAT 	= "SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='mat' AND id_material <> 'MTL-1903000' ";
		$resMAT 	= $this->db->query($sqlMAT)->result_array();
		
		$ArrMAT 	= array();

		foreach($resMAT AS $val => $valx){
			$ArrMAT[$val]['id'] = $valx['id'];
			$ArrMAT[$val]['unit_price'] = get_price_ref($valx['id_material']);
			$ArrMAT[$val]['total_price'] = get_price_ref($valx['id_material']) * $valx['qty'];
		}
		
		$this->db->trans_start();
		if(!empty($ArrMAT)){
			$this->db->update_batch('bq_acc_and_mat', $ArrMAT, 'id');
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update price failed. Please try again later ...',
				'status'	=> 0,
				'id_bqx'	=> $id_bq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update price success. Thanks ...',
				'status'	=> 1,
				'id_bqx'	=> $id_bq
			);
			history("Update all price reference material ".$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	//serverside cost kmponent
	public function getDataJSONComp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONComp(
			$requestData['series'],
			$requestData['komponen'], 
			$requestData['cust'],
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
			
			$cust = (!empty($row['cust']))?$row['cust']:'C100-1903000';
			
			$delCust = $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$cust."' ")->result();
			
			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_product']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($delCust[0]->nm_customer))."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['parent_product']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['stiffness']))."</div>";
			$nestedData[]	= "<div align='left'>".spec_master($row['id_product'])."</div>";
			
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['created_by']))."</div>";
			$nestedData[]	= "<div align='left'>".date('d-m-Y',strtotime($row['created_date']))."</div>";
			
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#217ece'>".$row['rev']."</span></div>";
			$nestedData[]	= "<div align='right'>".number_format(get_weight_comp($row['id_product'], $row['series'], $row['parent_product'], $row['diameter'], $row['diameter2'])['weight'],3)." Kg</div>";
			$nestedData[]	= "<div align='right'>".number_format(get_weight_comp($row['id_product'], $row['series'], $row['parent_product'], $row['diameter'], $row['diameter2'])['price'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(get_weight_comp($row['id_product'], $row['series'], $row['parent_product'], $row['diameter'], $row['diameter2'])['process'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(get_weight_comp($row['id_product'], $row['series'], $row['parent_product'], $row['diameter'], $row['diameter2'])['foh'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(get_weight_comp($row['id_product'], $row['series'], $row['parent_product'], $row['diameter'], $row['diameter2'])['profit'],2)."</div>";
				$det	= "<button type='button' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-warning MatDetail' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>";
			$nestedData[]	= "<div align='center'>
									".$det."
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

	public function queryDataJSONComp($series, $komponen, $cust, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_series = "";
		if(!empty($series)){
			$where_series = " AND series = '".$series."' ";
		}
		
		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND parent_product = '".$komponen."' ";
		}
		
		$where_cust = "";
		if(!empty($cust)){
			$where_cust = " AND cust = '".$cust."' ";
			if($cust == 'C100-1903000'){
				$where_cust = " AND (cust = '".$cust."' OR cust IS NULL OR cust = '') ";
			}
		} 
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				component_header a,
				(SELECT @row:=0) r 
		    WHERE 
				1=1
				".$where_series."
				".$where_komponen."
				".$where_cust."
				AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.parent_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'cust',
			3 => 'parent_product',
			4 => 'stiffness',
			6 => 'created_by',
			7 => 'created_date'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function getDataJSONComp2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONComp2(
			$requestData['series'],
			$requestData['komponen'],
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
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            if($asc_desc == 'desc')
            {
				$nomor = $urut1 + $start_dari;
            }
			
			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_product']))."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['product']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['stifness']))."</div>";
			$nestedData[]	= "<div align='left'>".spec_master($row['id_product'])."</div>";
			
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['created_by']))."</div>";
			// $nestedData[]	= "<div align='left'>".date('d-m-Y',strtotime($row['created_date']))."</div>";
			
			// $nestedData[]	= "<div align='center'><span class='badge' style='background-color:#217ece'>".$row['rev']."</span></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['weight'],3)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['price'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['process'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['foh'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['profit'],2)."</div>";
				$det	= "<button type='button' data-id_product='".$row['id_product']."' class='btn btn-sm btn-success updateData' title='Update Data' data-role='qtip'><i class='fa fa-level-down'></i></button>";
			$nestedData[]	= "<div align='center'>
									".$det."
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

	public function queryDataJSONComp2($series, $komponen, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_series = "";
		if(!empty($series)){
			$where_series = " AND series = '".$series."' ";
		}
		
		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND product = '".$komponen."' ";
		}
		
		$where_cust = " AND id_customer = 'C100-1903000' ";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				table_product_list a,
				(SELECT @row:=0) r 
		    WHERE 
				1=1
				".$where_series."
				".$where_komponen."
				".$where_cust."
				AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.stifness LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.rev LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.weight LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.price LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.process LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.foh LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.profit LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'product',
			3 => 'stifness',
			4 => 'spec',
			5 => 'created_by',
			6 => 'created_date',
			7 => 'rev',
			8 => 'weight',
			9 => 'price',
			10 => 'process',
			11 => 'foh',
			12 => 'profit'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//CUSTOM
	public function getDataJSONComp2Cust(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONComp2Cust(
			$requestData['series'],
			$requestData['komponen'], 
			$requestData['cust'],
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
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            if($asc_desc == 'desc')
            {
				$nomor = $urut1 + $start_dari;
            }
			
			// $cust = (!empty($row['cust']))?$row['cust']:'C100-1903000';
			
			$delCust = $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$row['id_customer']."' ")->result();
			
			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_product']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($delCust[0]->nm_customer)."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['product']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['stifness']))."</div>";
			$nestedData[]	= "<div align='left'>".spec_master($row['id_product'])."</div>";
			
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['created_by']))."</div>";
			// $nestedData[]	= "<div align='left'>".date('d-m-Y',strtotime($row['created_date']))."</div>";
			
			// $nestedData[]	= "<div align='center'><span class='badge' style='background-color:#217ece'>".$row['rev']."</span></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['weight'],3)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['price'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['process'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['foh'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['profit'],2)."</div>";
				$det	= "<button type='button' data-id_product='".$row['id_product']."' class='btn btn-sm btn-success updateData' title='Update Data' data-role='qtip'><i class='fa fa-level-down'></i></button>";
			$nestedData[]	= "<div align='center'>
									".$det."
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

	public function queryDataJSONComp2Cust($series, $komponen, $cust, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_series = "";
		if(!empty($series)){
			$where_series = " AND series = '".$series."' ";
		}
		
		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND product = '".$komponen."' ";
		}
		
		$where_cust = "";
		if(!empty($cust)){
			$where_cust = " AND id_customer = '".$cust."' ";
		} 

		$where_cust2 = " AND id_customer <> 'C100-1903000' ";
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				table_product_list a,
				(SELECT @row:=0) r 
		    WHERE 
				1=1
				".$where_series."
				".$where_komponen."
				".$where_cust."
				".$where_cust2."
				AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.stifness LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.rev LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.weight LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.price LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.process LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.foh LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.profit LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'nm_customer',
			3 => 'product',
			4 => 'stifness',
			5 => 'spec',
			6 => 'created_by',
			7 => 'created_date',
			8 => 'rev',
			9 => 'weight',
			10 => 'price',
			11 => 'process',
			12 => 'foh',
			13 => 'profit'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function update_product_list(){
		$data_session		= $this->session->userdata;
		$id_product			= $this->input->post('id_product');
		$data 	= $this->db->limit(1)->get_where('component_header', array('id_product'=>$id_product))->result();

		$cal_data = get_weight_comp($data[0]->id_product, $data[0]->series, $data[0]->parent_product, $data[0]->diameter, $data[0]->diameter2);

		$dataUpdate = array(
			'weight' 		=> $cal_data['weight'],
			'price' 		=> $cal_data['price'],
			'process' 		=> $cal_data['process'],
			'foh'			=> $cal_data['foh'],
			'profit' 		=> $cal_data['profit'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);
		
		// print_r($dataUpdate); exit;


		$this->db->trans_start();
			$this->db->where('id_product', $id_product)->update('table_product_list', $dataUpdate);
			
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Update Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Update Product Price Satuan '.$id_product);	
			
		}
		echo json_encode($Arr_Kembali);
	}
	
	public function update_function_revised(){
		$id_bq 		= "BQ-IPP20040L";
		//Insert Detail Report Revised
		$sqlNoRev 	= "SELECT revised_no FROM laporan_revised_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
		$restNoRev 	= $this->db->query($sqlNoRev)->result_array();
		$restNumRev = $this->db->query($sqlNoRev)->num_rows();

		if($restNumRev > 0){
			$revised_no = $restNoRev[0]['revised_no'] + 1;
		}
		else{
			$revised_no = 0;
		}

		$sqlRevised 	= SQL_Revised($id_bq); 
		$restRevised 	= $this->db->query($sqlRevised)->result_array();
		$ArrDetRevised 	= array();
		$SUM_est_material 				= 0;
		$SUM_est_harga 					= 0;
		$SUM_direct_labour 				= 0;
		$SUM_indirect_labour 			= 0;
		$SUM_machine 					= 0;
		$SUM_mould_mandrill 			= 0;
		$SUM_consumable	 				= 0;
		$SUM_foh_consumable 			= 0;
		$SUM_foh_depresiasi 			= 0;
		$SUM_biaya_gaji_non_produksi 	= 0;
		$SUM_biaya_non_produksi 		= 0;
		$SUM_biaya_rutin_bulanan 		= 0;
		foreach($restRevised AS $val => $valx){
			$SUM_est_material 				+= $valx['est_material'];
			$SUM_est_harga 					+= $valx['est_harga'];
			$SUM_direct_labour 				+= $valx['direct_labour'];
			$SUM_indirect_labour 			+= $valx['indirect_labour'];
			$SUM_machine 					+= $valx['machine'];
			$SUM_mould_mandrill 			+= $valx['mould_mandrill'];
			$SUM_consumable 				+= $valx['consumable'];
			$SUM_foh_consumable 			+= $valx['foh_consumable'];
			$SUM_foh_depresiasi 			+= $valx['foh_depresiasi'];
			$SUM_biaya_gaji_non_produksi 	+= $valx['biaya_gaji_non_produksi'];
			$SUM_biaya_non_produksi 		+= $valx['biaya_non_produksi'];
			$SUM_biaya_rutin_bulanan 		+= $valx['biaya_rutin_bulanan'];

			$sqlTambahan 	= "SELECT `length`, thickness, sudut, id_standard, `type` FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id = '".$valx['id_milik']."' LIMIT 1 ";
			$restTambahan 	= $this->db->query($sqlTambahan)->result_array();

			$ArrDetRevised[$val]['id_bq'] = $valx['id_bq'];
			$ArrDetRevised[$val]['id_milik'] = $valx['id_milik'];
			$ArrDetRevised[$val]['product_parent'] = $valx['parent_product'];
			$ArrDetRevised[$val]['id_product'] = $valx['id_product'];
			$ArrDetRevised[$val]['series'] = $valx['series'];
			$ArrDetRevised[$val]['diameter'] = $valx['diameter'];
			$ArrDetRevised[$val]['diameter2'] = $valx['diameter2'];
			$ArrDetRevised[$val]['length'] = $restTambahan[0]['length'];
			$ArrDetRevised[$val]['thickness'] = $restTambahan[0]['thickness'];
			$ArrDetRevised[$val]['sudut'] = $restTambahan[0]['sudut'];
			$ArrDetRevised[$val]['id_standard'] = $restTambahan[0]['id_standard'];
			$ArrDetRevised[$val]['type'] = $restTambahan[0]['type'];
			$ArrDetRevised[$val]['pressure'] = $valx['pressure'];
			$ArrDetRevised[$val]['liner'] = $valx['liner'];
			$ArrDetRevised[$val]['qty'] = $valx['qty'];
			$ArrDetRevised[$val]['est_material'] = $valx['est_material'];
			$ArrDetRevised[$val]['est_harga'] = $valx['est_harga'];
			$ArrDetRevised[$val]['direct_labour'] = $valx['direct_labour'];
			$ArrDetRevised[$val]['indirect_labour'] = $valx['indirect_labour'];
			$ArrDetRevised[$val]['machine'] = $valx['machine'];
			$ArrDetRevised[$val]['mould_mandrill'] = $valx['mould_mandrill'];
			$ArrDetRevised[$val]['consumable'] = $valx['consumable'];
			$ArrDetRevised[$val]['foh_consumable'] = $valx['foh_consumable'];
			$ArrDetRevised[$val]['foh_depresiasi'] = $valx['foh_depresiasi'];
			$ArrDetRevised[$val]['biaya_gaji_non_produksi'] = $valx['biaya_gaji_non_produksi'];
			$ArrDetRevised[$val]['biaya_non_produksi'] = $valx['biaya_non_produksi'];
			$ArrDetRevised[$val]['biaya_rutin_bulanan'] = $valx['biaya_rutin_bulanan'];
				$unitPriceX = ($valx['est_harga']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan']) / $valx['qty'];
			$ArrDetRevised[$val]['unit_price'] = $unitPriceX;
			$ArrDetRevised[$val]['profit'] = $valx['profit'];
				$unitProfitX = $unitPriceX *($valx['profit']/100);
				$unitAllowanceX = (($unitPriceX) + ($unitProfitX)) * $valx['qty'];
			$ArrDetRevised[$val]['total_price'] = $unitAllowanceX;
			$ArrDetRevised[$val]['allowance'] = $valx['allowance'];
				$unitAllowanceLast = (($unitAllowanceX) + ($unitAllowanceX * ($valx['allowance']/100)));
			$ArrDetRevised[$val]['total_price_last'] = $unitAllowanceLast;
			$ArrDetRevised[$val]['man_power'] = $valx['man_power'];
			$ArrDetRevised[$val]['id_mesin'] = $valx['id_mesin'];
			$ArrDetRevised[$val]['total_time'] = $valx['total_time'];
			$ArrDetRevised[$val]['man_hours'] = $valx['man_hours'];
			$ArrDetRevised[$val]['pe_direct_labour'] = $valx['pe_direct_labour'];
			$ArrDetRevised[$val]['pe_indirect_labour'] = $valx['pe_indirect_labour'];
			$ArrDetRevised[$val]['pe_machine'] = $valx['pe_machine'];
			$ArrDetRevised[$val]['pe_mould_mandrill'] = $valx['pe_mould_mandrill'];
			$ArrDetRevised[$val]['pe_consumable'] = $valx['pe_consumable'];
			$ArrDetRevised[$val]['pe_foh_consumable'] = $valx['pe_foh_consumable'];
			$ArrDetRevised[$val]['pe_foh_depresiasi'] = $valx['pe_foh_depresiasi'];
			$ArrDetRevised[$val]['pe_biaya_gaji_non_produksi'] = $valx['pe_biaya_gaji_non_produksi'];
			$ArrDetRevised[$val]['pe_biaya_non_produksi'] = $valx['pe_biaya_non_produksi'];
			$ArrDetRevised[$val]['pe_biaya_rutin_bulanan'] = $valx['pe_biaya_rutin_bulanan'];
			$ArrDetRevised[$val]['revised_no'] = $revised_no;
			$ArrDetRevised[$val]['insert_by'] = 'Cost2';
			$ArrDetRevised[$val]['insert_date'] = '2020-01-28 09:17:28';
		}

		//Insert Header Report Revised
		$sqlRevisedHead 	= "SELECT id_customer, nm_customer, project FROM production WHERE no_ipp='".str_replace('BQ-','',$id_bq)."' ";
		$restRevisedHead 	= $this->db->query($sqlRevisedHead)->result_array();

		$sqlTotPro 		= "SELECT price_project FROM cost_project_header WHERE id_bq='".$id_bq."' ";
		$restsqlTotPro 	= $this->db->query($sqlTotPro)->result_array();
		$restNumTotPro 	= $this->db->query($sqlTotPro)->num_rows();

		if($restNumTotPro > 0){
			$totProject = $restsqlTotPro[0]['price_project'];
		}
		else{
			$totProject = 0;
		}
		
		$ArrHeadRevised = array(
			'id_bq' => $id_bq,
			'id_customer' => $restRevisedHead[0]['id_customer'],
			'nm_customer' => $restRevisedHead[0]['nm_customer'],
			'nm_project' => $restRevisedHead[0]['project'],
			'revised_no' => $revised_no,
			'price_project' => $totProject,
			'est_material' => $SUM_est_material,
			'est_harga' => $SUM_est_harga,
			'direct_labour' => $SUM_direct_labour,
			'indirect_labour' => $SUM_indirect_labour,
			'machine' => $SUM_machine,
			'mould_mandrill' => $SUM_mould_mandrill,
			'consumable' => $SUM_consumable,
			'foh_consumable' => $SUM_foh_consumable,
			'foh_depresiasi' => $SUM_foh_depresiasi,
			'biaya_gaji_non_produksi' => $SUM_biaya_gaji_non_produksi,
			'biaya_non_produksi' => $SUM_biaya_non_produksi,
			'biaya_rutin_bulanan' => $SUM_biaya_rutin_bulanan,
			'insert_by' => 'Cost2',
			'insert_date' => '2020-01-28 09:17:28'
		);

		//Insert Header Report Etc
		$sqlRevisedEtc 		= "SELECT * FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category <> 'material' ";
		$restRevisedEtc 	= $this->db->query($sqlRevisedEtc)->result_array();
		$restNumRevisedEtc 	= $this->db->query($sqlRevisedEtc)->num_rows();
		
		if($restNumRevisedEtc > 0){
			$ArrEtcRevised = array();
			foreach($restRevisedEtc AS $val => $valx){
				$ArrEtcRevised[$val]['id_bq'] = $valx['id_bq'];
				$ArrEtcRevised[$val]['category'] = $valx['category'];
				$ArrEtcRevised[$val]['caregory_sub'] = $valx['caregory_sub'];
				$ArrEtcRevised[$val]['option_type'] = $valx['option_type'];
				$ArrEtcRevised[$val]['area'] = $valx['area'];
				$ArrEtcRevised[$val]['tujuan'] = $valx['tujuan'];
				$ArrEtcRevised[$val]['kendaraan'] = $valx['kendaraan'];
				$ArrEtcRevised[$val]['unit'] = $valx['unit'];
				$ArrEtcRevised[$val]['qty'] = $valx['qty'];
				$ArrEtcRevised[$val]['fumigasi'] = $valx['fumigasi'];
				$ArrEtcRevised[$val]['price'] = $valx['price'];
				$ArrEtcRevised[$val]['price_total'] = $valx['price_total'];
				$ArrEtcRevised[$val]['revised_no'] = $revised_no;
				$ArrEtcRevised[$val]['insert_by'] = 'Cost2';
				$ArrEtcRevised[$val]['insert_date'] = '2020-01-28 09:17:28';
			}
		}
		// echo "<pre>";
		// print_r($ArrHeadRevised);
		// print_r($ArrDetRevised);
		// print_r($ArrEtcRevised);
		// exit;

		$this->db->insert('laporan_revised_header', $ArrHeadRevised);
		$this->db->insert_batch('laporan_revised_detail', $ArrDetRevised);
		if($restNumRevisedEtc > 0){
			$this->db->insert_batch('laporan_revised_etc', $ArrEtcRevised);
		}

	}
	
	function insert_select_product_list(){ 
		// $time = microtime();
		// $time = explode(' ', $time);
		// $time = $time[1] + $time[0];
		// $start = $time;
		history('Try insert new product list');
		// $sql 	= "SELECT * FROM component_header ORDER BY created_date DESC";
		// $sql 	= "SELECT
		// 				a.* 
		// 			FROM
		// 				component_header a
		// 				LEFT JOIN table_product_list b ON a.id_product = b.id_product 
		// 			WHERE
		// 				b.id_product IS NULL 
		// 			ORDER BY
		// 				a.created_date DESC";
		$rest 	= $this->db
						->select('a.*')
						->from('component_header a')
						->join('table_product_list b','a.id_product = b.id_product','left')
						->where('b.id_product', NULL)
						->order_by('a.created_date','DESC')
						->get()
						->result_array();
		$ArrInsert = array();
		foreach($rest AS $val => $valx){
			$cust 		= (!empty($valx['cust']))?$valx['cust']:'C100-1903000';
			$delCust 	= $this->db->select('nm_customer')->get_where('customer', array('id_customer'=>$cust))->result();
			
			$ArrInsert[$val]['id_product'] 		= $valx['id_product'];
			$ArrInsert[$val]['id_customer'] 	= $cust;
			$ArrInsert[$val]['nm_customer'] 	= $delCust[0]->nm_customer;
			$ArrInsert[$val]['product'] 		= $valx['parent_product'];
			$ArrInsert[$val]['stifness'] 		= $valx['stiffness'];
			$ArrInsert[$val]['spec'] 			= spec_master($valx['id_product']);
			$ArrInsert[$val]['created_by'] 		= $valx['created_by'];
			$ArrInsert[$val]['created_date'] 	= $valx['created_date'];
			$ArrInsert[$val]['rev'] 			= $valx['rev'];
			$ArrInsert[$val]['series'] 			= $valx['series'];
			// $ArrInsert[$val]['weight'] 			= get_weight_comp($valx['id_product'], $valx['series'], $valx['parent_product'], $valx['diameter'], $valx['diameter2'])['weight'];
			// $ArrInsert[$val]['price'] 			= get_weight_comp($valx['id_product'], $valx['series'], $valx['parent_product'], $valx['diameter'], $valx['diameter2'])['price'];
			// $ArrInsert[$val]['process'] 		= get_weight_comp($valx['id_product'], $valx['series'], $valx['parent_product'], $valx['diameter'], $valx['diameter2'])['process'];
			// $ArrInsert[$val]['foh'] 			= get_weight_comp($valx['id_product'], $valx['series'], $valx['parent_product'], $valx['diameter'], $valx['diameter2'])['foh'];
			// $ArrInsert[$val]['profit'] 			= get_weight_comp($valx['id_product'], $valx['series'], $valx['parent_product'], $valx['diameter'], $valx['diameter2'])['profit'];
			$ArrInsert[$val]['updated_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrInsert[$val]['updated_date'] 	= date('Y-m-d H:i:s');
		}
		// echo "<pre>";
		// print_r($ArrInsert);
		
		// $time = microtime();
		// $time = explode(' ', $time);
		// $time = $time[1] + $time[0];
		// $finish = $time;
		// $total_time = round(($finish - $start), 4);
		// echo "Selesai dalam ".$total_time." detik";
		
		// exit;
			
		$this->db->trans_start();
			$this->db->insert_batch('table_product_list',$ArrInsert);
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
			history('Success insert new product list');
		}
		echo json_encode($Arr_Data);
	}
	
	public function excel_price_project(){
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
		$Col_Akhir	= $Cols	= getColsChar(19);
		$sheet->setCellValue('A'.$Row, 'DETAIL PRICE PROJECT '.str_replace('BQ-','',$id_bq));
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Product');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Spesifikasi');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Qty');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Product ID');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Material Est (Kg)');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'COGS ($)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'Material Cost ($)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'Direct Labour ($)');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Indirect Labour ($)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Consumable ($)');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Machine Cost ($)');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'Mould Mandrill FOH ($)');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'Consumable FOH ($)');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'Depresiasi FOH ($)');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue('Q'.$NewRow, 'Gaji Non Produksi (FNA, PCH, HRGA) ($)');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'Biaya Admin ($)');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'Biaya Bulanan (Listrik, Air, Tlp, internet) ($)');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		
		
		$sql 		= "	SELECT
							a.id_milik,
							a.id_bq,
							b.parent_product AS id_category,
							a.qty,
							b.diameter AS diameter_1,
							b.diameter2 AS diameter_2,
							b.panjang AS length,
							b.thickness,
							b.angle AS sudut,
							b.type,
							a.id_product,
							b.standart_code,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							b.pressure,
							b.liner,
							(a.direct_labour * a.qty) AS direct_labour,
							(a.indirect_labour * a.qty) AS indirect_labour,
							(a.machine * a.qty) AS machine,
							(a.mould_mandrill * a.qty) AS mould_mandrill,
							(a.consumable * a.qty) AS consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) * `a`.`qty` 
							) AS `cost_process`,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
							) * ( (b.pe_foh_consumable) / 100 ) * a.qty AS foh_consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
							) * ( (b.pe_foh_depresiasi) / 100 ) * a.qty AS foh_depresiasi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
							) * ( (b.pe_biaya_gaji_non_produksi) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
							) * ( (b.pe_biaya_non_produksi) / 100 ) * a.qty AS biaya_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
							) * ( (b.pe_biaya_rutin_bulanan) / 100 ) * a.qty AS biaya_rutin_bulanan 
						FROM
							estimasi_cost_and_mat a
							INNER JOIN bq_product b ON a.id_milik = b.id
						WHERE
							b.parent_product <> 'pipe slongsong' AND 
							a.id_bq = '".$id_bq."' ORDER BY a.id_milik ASC";
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			$Sum = 0;
			$SumX = 0;
			$Sum2 = 0;
			$SumX2 = 0;
			$Cost = 0;
			
			$EstMatKg = 0;
			$EstMat = 0;
			$EstProcess = 0;
			
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
			$COGS = 0;
			foreach($result as $key => $valx){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$EstMatKg += $valx['sum_mat2'];
				$EstMat += $valx['est_harga2'];
				
				
				$Direct += $valx['direct_labour'];
				$Indirect += $valx['indirect_labour'];
				$Machi += $valx['machine'];
				$MouldM += $valx['mould_mandrill'];
				$Consumab += $valx['consumable'];
				
				$ConsFOH += $valx['foh_consumable'];
				$DepFOH += $valx['foh_depresiasi'];
				$GjNonP += $valx['biaya_gaji_non_produksi'];
				$ByAdmin += $valx['biaya_non_produksi'];
				$ByBulanan += $valx['biaya_rutin_bulanan'];
				
				$SumQty	= $valx['sum_mat2'];
				$Sum += $SumQty;
				
				$SumQtyX	= $valx['est_harga2'];
				$SumX += $SumQtyX;
				
				$Costx2	= $valx['direct_labour'] + $valx['indirect_labour'] + $valx['machine'] + $valx['mould_mandrill'] + $valx['consumable'] + $valx['foh_consumable'] + $valx['foh_depresiasi'] + $valx['biaya_gaji_non_produksi'] + $valx['biaya_non_produksi'] + $valx['biaya_rutin_bulanan'];
				$Cost += $Costx2;
				
				$COGS += $SumQtyX + $Costx2;
				
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_bqx		= str_replace('BQ-','',$id_bq);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_bqx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_category	= $valx['id_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_milik	= spec_bq($valx['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_milik);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty	= $valx['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$id_product	= $valx['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$material_qty	= $SumQty;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$cogs	= $SumQtyX + $Costx2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cogs);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$material_harga	= $SumQtyX;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$direct_labour	= $valx['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$indirect_labour	= $valx['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$consumable	= $valx['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$machine	= $valx['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$mould_mandrill	= $valx['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_consumable	= $valx['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_depresiasi	= $valx['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_gaji_non_produksi	= $valx['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_non_produksi	= $valx['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_rutin_bulanan	= $valx['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				
			}
			// echo $no;exit;
			$Colsw = floatval($no) +6;
			
			// echo $Colsw."-".$Colse; exit;
			
			$sheet->setCellValue("A".$Colsw."", '');
			$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			$sheet->setCellValue("B".$Colsw."", '');
			$sheet->getStyle("B".$Colsw.":B".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("B".$Colsw.":B".$Colsw."");
			$sheet->getColumnDimension('B')->setAutoSize(true);
			
			$sheet->setCellValue("C".$Colsw."", '');
			$sheet->getStyle("C".$Colsw.":C".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("C".$Colsw.":C".$Colsw."");
			$sheet->getColumnDimension('C')->setAutoSize(true);
			
			$sheet->setCellValue("D".$Colsw."", '');
			$sheet->getStyle("D".$Colsw.":D".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("D".$Colsw.":D".$Colsw."");
			$sheet->getColumnDimension('D')->setAutoSize(true);
			
			$sheet->setCellValue("E".$Colsw."", '');
			$sheet->getStyle("E".$Colsw.":E".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("E".$Colsw.":E".$Colsw."");
			$sheet->getColumnDimension('E')->setAutoSize(true);
			
			$sheet->setCellValue("F".$Colsw."", 'SUM');
			$sheet->getStyle("F".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("F".$Colsw.":F".$Colsw."");
			$sheet->getColumnDimension('F')->setAutoSize(true);
			
			$sheet->setCellValue("G".$Colsw."", $EstMatKg);
			$sheet->getStyle("G".$Colsw.":G".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("G".$Colsw.":G".$Colsw."");
			$sheet->getColumnDimension('G')->setAutoSize(true);
			
			$sheet->setCellValue("H".$Colsw."", $COGS);
			$sheet->getStyle("H".$Colsw.":H".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("H".$Colsw.":H".$Colsw."");
			$sheet->getColumnDimension('H')->setAutoSize(true);
			
			$sheet->setCellValue("I".$Colsw."", $EstMat);
			$sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("I".$Colsw.":I".$Colsw."");
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue("J".$Colsw."", $Direct);
			$sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("J".$Colsw.":J".$Colsw."");
			$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue("K".$Colsw."", $Indirect );
			$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
			$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue("L".$Colsw."", $Consumab);
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

		}
		
		//material dan non frp
		// $Colsw = floatval($no) +8;
			
		// echo $Colsw."-".$Colse; exit;
		
		// $sheet->setCellValue("A".$Colsw."", '');
		// $sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
		// $sheet->mergeCells("A".$Colsw.":A".$Colsw."");
		// $sheet->getColumnDimension('A')->setAutoSize(true);
		
		// $sheet->setCellValue("B".$Colsw."", '');
		// $sheet->getStyle("B".$Colsw.":B".$Colsw."")->applyFromArray($style_header);
		// $sheet->mergeCells("B".$Colsw.":B".$Colsw."");
		// $sheet->getColumnDimension('B')->setAutoSize(true);
		
		// $sheet->setCellValue("C".$Colsw."", '');
		// $sheet->getStyle("C".$Colsw.":C".$Colsw."")->applyFromArray($style_header);
		// $sheet->mergeCells("C".$Colsw.":C".$Colsw."");
		// $sheet->getColumnDimension('C')->setAutoSize(true);
		
		// $sheet->setCellValue("D".$Colsw."", '');
		// $sheet->getStyle("D".$Colsw.":D".$Colsw."")->applyFromArray($style_header);
		// $sheet->mergeCells("D".$Colsw.":D".$Colsw."");
		// $sheet->getColumnDimension('D')->setAutoSize(true);
		
		// $sheet->setCellValue("E".$Colsw."", '');
		// $sheet->getStyle("E".$Colsw.":E".$Colsw."")->applyFromArray($style_header);
		// $sheet->mergeCells("E".$Colsw.":E".$Colsw."");
		// $sheet->getColumnDimension('E')->setAutoSize(true);
		
		// $sheet->setCellValue("F".$Colsw."", 'SUM');
		// $sheet->getStyle("F".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
		// $sheet->mergeCells("F".$Colsw.":F".$Colsw."");
		// $sheet->getColumnDimension('F')->setAutoSize(true);
		
		// $sheet->setCellValue("G".$Colsw."", $EstMatKg);
		// $sheet->getStyle("G".$Colsw.":G".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("G".$Colsw.":G".$Colsw."");
		// $sheet->getColumnDimension('G')->setAutoSize(true);
		
		// $sheet->setCellValue("H".$Colsw."", $COGS);
		// $sheet->getStyle("H".$Colsw.":H".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("H".$Colsw.":H".$Colsw."");
		// $sheet->getColumnDimension('H')->setAutoSize(true);
		
		// $sheet->setCellValue("I".$Colsw."", $EstMat);
		// $sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("I".$Colsw.":I".$Colsw."");
		// $sheet->getColumnDimension('I')->setAutoSize(true);
		
		// $sheet->setCellValue("J".$Colsw."", $Direct);
		// $sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("J".$Colsw.":J".$Colsw."");
		// $sheet->getColumnDimension('J')->setAutoSize(true);
		
		// $sheet->setCellValue("K".$Colsw."", $Indirect );
		// $sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("K".$Colsw.":K".$Colsw."");
		// $sheet->getColumnDimension('K')->setAutoSize(true);
		
		// $sheet->setCellValue("L".$Colsw."", $Consumab);
		// $sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("L".$Colsw.":L".$Colsw."");
		// $sheet->getColumnDimension('L')->setAutoSize(true);
		
		// $sheet->setCellValue("M".$Colsw."", $Machi);
		// $sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("M".$Colsw.":M".$Colsw."");
		// $sheet->getColumnDimension('M')->setAutoSize(true);
		
		// $sheet->setCellValue("N".$Colsw."", $MouldM);
		// $sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("N".$Colsw.":N".$Colsw."");
		// $sheet->getColumnDimension('N')->setAutoSize(true);
		
		// $sheet->setCellValue("O".$Colsw."", $ConsFOH);
		// $sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("O".$Colsw.":O".$Colsw."");
		// $sheet->getColumnDimension('O')->setAutoSize(true);
		
		// $sheet->setCellValue("P".$Colsw."", $DepFOH);
		// $sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("P".$Colsw.":P".$Colsw."");
		// $sheet->getColumnDimension('P')->setAutoSize(true);
		
		// $sheet->setCellValue("Q".$Colsw."", $GjNonP);
		// $sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
		// $sheet->getColumnDimension('Q')->setAutoSize(true);
		
		// $sheet->setCellValue("R".$Colsw."", $ByAdmin);
		// $sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("R".$Colsw.":R".$Colsw."");
		// $sheet->getColumnDimension('R')->setAutoSize(true);
		
		// $sheet->setCellValue("S".$Colsw."", $ByBulanan);
		// $sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($styleArray4);
		// $sheet->mergeCells("S".$Colsw.":S".$Colsw."");
		// $sheet->getColumnDimension('S')->setAutoSize(true);
		
		history('Download excel price project (estimation price project) '.str_replace('BQ-','',$id_bq));
		
		$sheet->setTitle(str_replace('BQ-','',$id_bq));
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
		header('Content-Disposition: attachment;filename="Price project '.str_replace('BQ-','',$id_bq).' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function update_harga_this_ipp(){
		if($this->input->post()){
			$data = $this->input->post();

			$id_bq = $data['id_bq'];

			$ArrInsert = [];
			$ArrUpdate1 = [];
			$ArrUpdate2 = [];
			$ArrUpdate3 = [];
			$ArrUpdate4 = [];

			if(!empty($data['detail'])){
				foreach($data['detail'] AS $val => $valx){
					if(!empty($valx['price_after']) AND $valx['price_after'] > 0){
						$ArrInsert[$val]['no_ipp'] = $id_bq;
						$ArrInsert[$val]['id_material'] = $valx['id_material'];
						$ArrInsert[$val]['nm_material'] = $valx['nm_material'];
						$ArrInsert[$val]['price_before'] = $valx['price_before'];
						$ArrInsert[$val]['price_after'] = str_replace(',','',$valx['price_after']);
						$ArrInsert[$val]['keterangan'] = $valx['keterangan'];
						$ArrInsert[$val]['hist_by'] = $this->session->userdata['ORI_User']['username'];
						$ArrInsert[$val]['hist_date'] = date('Y-m-d H:i:s');
						//update detail
						$bq_detail = $this->db->select('id_detail')->get_where('bq_component_detail', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material']))->result_array();
						foreach($bq_detail AS $val1 => $valx1){
							$ArrUpdate1[$val.$val1]['id_detail'] = $valx1['id_detail'];
							$ArrUpdate1[$val.$val1]['price_mat'] = str_replace(',','',$valx['price_after']);
						}
						//update detail plus
						$bq_detail_plus = $this->db->select('id_detail')->get_where('bq_component_detail_plus', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material']))->result_array();
						foreach($bq_detail_plus AS $val2 => $valx2){
							$ArrUpdate2[$val.$val2]['id_detail'] = $valx2['id_detail'];
							$ArrUpdate2[$val.$val2]['price_mat'] = str_replace(',','',$valx['price_after']);
						}
						//update detail add
						$bq_detail_add = $this->db->select('id_detail')->get_where('bq_component_detail_add', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material']))->result_array();
						foreach($bq_detail_add AS $val3 => $valx3){
							$ArrUpdate3[$val.$val3]['id_detail'] = $valx3['id_detail'];
							$ArrUpdate3[$val.$val3]['price_mat'] = str_replace(',','',$valx['price_after']);
						}
						//update material
						$bq_detail_mat = $this->db->select('id, qty')->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material']))->result_array();
						foreach($bq_detail_mat AS $val4 => $valx4){
							$ArrUpdate4[$val.$val4]['id'] = $valx4['id'];
							$ArrUpdate4[$val.$val4]['unit_price'] = str_replace(',','',$valx['price_after']);
							$ArrUpdate4[$val.$val4]['total_price'] = $valx4['qty'] * str_replace(',','',$valx['price_after']);
						}
					}
				}
			}
			// print_r($ArrInsert);
			// print_r($ArrUpdate1);
			// print_r($ArrUpdate2);
			// print_r($ArrUpdate3);
			// exit;
			$this->db->trans_start();
				if(!empty($ArrInsert)){
					$this->db->insert_batch('hist_price_per_ipp', $ArrInsert, 'id_detail');
				}
				if(!empty($ArrUpdate1)){
					$this->db->update_batch('bq_component_detail', $ArrUpdate1, 'id_detail');
				}
				if(!empty($ArrUpdate2)){
					$this->db->update_batch('bq_component_detail_plus', $ArrUpdate2, 'id_detail');
				}
				if(!empty($ArrUpdate3)){
					$this->db->update_batch('bq_component_detail_add', $ArrUpdate3, 'id_detail');
				}
				if(!empty($ArrUpdate4)){
					$this->db->update_batch('bq_acc_and_mat', $ArrUpdate4, 'id');
				}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update failed. Please try again later ...',
					'status'	=> 0,
					'id_bqx' => $id_bq
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update success. Thanks ...',
					'status'	=> 1,
					'id_bqx' => $id_bq
				);				
				history('Update price material only : '.$id_bq);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$id_bq 	= $this->uri->segment(3);
			$result	= $this->db->order_by('nm_material','ASC')->get_where('estimasi_total_material',array('id_bq'=>$id_bq, 'id_material <>'=>'MTL-1903000'))->result_array();
			$result2	= $this->db->get_where('bq_acc_and_mat',array('id_bq'=>$id_bq, 'category'=>'mat'))->result_array();
			$data 	= array(
				'detail' => $result,
				'detail2' => $result2,
				'id_bq' => $id_bq
			);
			$this->load->view('Price/update_harga_this_ipp', $data);
		}
	}
	
	public function pricebook(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/pricebook";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT create_by, create_date FROM group_cost_project_table ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Pricebook',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View index pricebook');
		$this->load->view('Price/pricebook',$data);
	}

}