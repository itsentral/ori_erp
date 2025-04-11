<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_quotation extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('set_buttom_price_model');
		
		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	//==========================================================================================================================
	//==================================================SET BUTTOM PRICE========================================================
	//==========================================================================================================================
	
	public function project(){
		$this->set_buttom_price_model->index_project();
	}
	
	public function modal_approve(){
		$this->set_buttom_price_model->modal_approve();
	}
	
	public function server_side_set_buttom_price(){
		$this->set_buttom_price_model->get_data_set_buttom_price();
	}
	
	public function approve_set_price(){
		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		
		$StatuxX		= $this->input->post('status');
		$perubahan		= $this->input->post('perubahan');
		
		$data_session	= $this->session->userdata;
		$username 		= $data_session['ORI_User']['username'];
		$datetime		= date('Y-m-d H:i:s');
		
		$ArrMonitoring = [];
		
		if($StatuxX == 'Y'){
			$Arr_Edit	= array(
				'status' => 'ALREADY ESTIMATED PRICE',
				'sts_price_reason' => $this->input->post('approve_reason'),
				'sts_price_by' => $username,
				'sts_price_date' => $datetime
			);

			$ArrMonitoring	= array(
				'est_price_release_by' => $username,
				'est_price_release_date' => $datetime
			);

			$ArrInsert	= array(
				'id_bq' => $id_bq,
				'cost_material' => str_replace(',', '', $this->input->post('total_kg')),
				'cost_total' => str_replace(',', '', $this->input->post('total_cost')),
				'status' => 'Y',
				'created_by' => $username,
				'created_date' => $datetime
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
			$SUM_PROJECT 					= 0;
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
					$SUM_PROJECT += $unitAllowanceLast;
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


			//Insert Header Report Etc
			$sqlRevisedEtc 		= "SELECT * FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category <> 'material' ";
			$restRevisedEtc 	= $this->db->query($sqlRevisedEtc)->result_array();
			
			$ArrEtcRevised = array();
			if(!empty($restRevisedEtc)){
				foreach($restRevisedEtc AS $val => $valx){
					$SUM_PROJECT += $valx['price_total'];
					$ArrEtcRevised[$val]['id_bq'] = $valx['id_bq'];
					$ArrEtcRevised[$val]['category'] = $valx['category'];
					$ArrEtcRevised[$val]['caregory_sub'] = $valx['caregory_sub'];
					$ArrEtcRevised[$val]['option_type'] = $valx['option_type'];
					$ArrEtcRevised[$val]['area'] = $valx['area'];
					$ArrEtcRevised[$val]['tujuan'] = $valx['tujuan'];
					$ArrEtcRevised[$val]['kendaraan'] = $valx['kendaraan'];
					$ArrEtcRevised[$val]['weight'] = $valx['weight'];
					$ArrEtcRevised[$val]['unit'] = $valx['unit'];
					$ArrEtcRevised[$val]['qty'] = $valx['qty'];
					$ArrEtcRevised[$val]['fumigasi'] = $valx['fumigasi'];
					$ArrEtcRevised[$val]['price'] = $valx['price'];
					$ArrEtcRevised[$val]['price_total'] = $valx['price_total'];
					$ArrEtcRevised[$val]['profit'] = $valx['persen'];
					$ArrEtcRevised[$val]['allow'] = $valx['extra'];
					$ArrEtcRevised[$val]['revised_no'] = $revised_no;
					$ArrEtcRevised[$val]['insert_by'] = $data_session['ORI_User']['username'];
					$ArrEtcRevised[$val]['insert_date'] = date('Y-m-d H:i:s');
				}
			}
			
			//Insert Header Report Revised
			$sqlRevisedHead 	= "SELECT id_customer, nm_customer, project FROM production WHERE no_ipp='".str_replace('BQ-','',$id_bq)."' ";
			$restRevisedHead 	= $this->db->query($sqlRevisedHead)->result_array();

			$restsqlTotPro 	= $this->db->select('price_project')->get_where('cost_project_header',array('id_bq'=>$id_bq))->result_array();

			if(!empty($restsqlTotPro)){
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
				'perubahan' => strtolower($this->input->post('perubahan')),
				'price_project' => $SUM_PROJECT,
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
				'insert_by' => $username,
				'insert_date' => $datetime
			);
			// echo "<pre>";
			// print_r($ArrHeadRevised);
			// print_r($ArrDetRevised);
			// print_r($ArrEtcRevised);
			// exit;
			
			$HistSts = "Approve Project Price with BQ : ".$id_bq;
		}
		
		if($StatuxX == 'N'){
			$sqlNoRev_eng 	= "SELECT revised_no FROM laporan_costing_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
			$restNoRev_eng 	= $this->db->query($sqlNoRev_eng)->result();
			$revisi_eng 	= $restNoRev_eng[0]->revised_no;
		
			$Arr_Edit	= array(
				'status' => 'WAITING STRUCTURE BQ',
				'sts_price_reason' => $this->input->post('approve_reason'),
				'sts_price_by' => $username,
				'sts_price_date' => $datetime
			);

			$ArrConfirm = array(
				'approved' 		=> 'N',
				'approved_by' 	=> $username,
				'approved_date' => $datetime,
				
				'aju_approved' 		=> 'N',
				'aju_approved_by' 	=> $username,
				'aju_approved_date' => $datetime,
				
				'approved_est' 		=> 'N',
				'approved_est_by' 	=> $username,
				'approved_est_date' => $datetime,
				
				'aju_approved_est' 		=> 'N',
				'aju_approved_est_by' 	=> $username,
				'aju_approved_est_date' => $datetime
			);
			
			$ArrRevisi	= array(
				'revisi' 	=> $this->input->post('approve_reason')
			);
			
			$HistSts = "Reject Project Price with BQ : ".$id_bq;
		}
		//insert semua total harga di price
		
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $Imp[1]);
			$this->db->update('production', $Arr_Edit);

			if(!empty($ArrMonitoring)){
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('monitoring_ipp', $ArrMonitoring);
			}
			
			if($StatuxX == 'Y'){
				$this->db->delete('bq_price_project', array('id_bq' => $id_bq));
				$this->db->insert('bq_price_project', $ArrInsert);

				$this->db->insert('laporan_revised_header', $ArrHeadRevised);
				if(!empty($ArrDetRevised)){
					$this->db->insert_batch('laporan_revised_detail', $ArrDetRevised);
				}
				if(!empty($ArrEtcRevised)){
					$this->db->insert_batch('laporan_revised_etc', $ArrEtcRevised);
				}
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
	
	
	
	
	
	
	
	
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$id_price		 	= $this->uri->segment(3);
		$get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.parent_product='".$id_price."' AND a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Estimation Product Price',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Estimation Product Price');
		$this->load->view('Cost_quotation/index',$data);
	}
	
	public function ajukan_price(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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
		$this->load->view('Cost_quotation/ajukan_price',$data);
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
		$this->load->view('Cost_quotation/ajukan_price2',$data);
	}
	
	public function app_mat(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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
		$this->load->view('Cost_quotation/app_mat',$data);
	}
	
	
	public function modalDetailProcess(){
		$this->load->view('Cost_quotation/modalDetailProcess');
	}
	
	public function modalDetail(){
		$this->load->view('Cost_quotation/modalDetail');
	}
	
	public function modalDetailMat(){
		$this->load->view('Cost_quotation/modalDetailMat');
	}
	
	public function modalDetailMatCost(){
		$this->load->view('Cost_quotation/modalDetailCost');
	}
	
	public function modalPrice(){
		$this->load->view('Cost_quotation/modalPrice');
	}
	
	public function modalAppMat(){
		$this->load->view('Cost_quotation/modalAppMat');
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
		$this->load->view('Cost_quotation/cost_control',$data);
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
		$this->load->view('Cost_quotation/priceReal',$data);
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
		$this->load->view('Cost_quotation/priceProcessCost',$data);
	}
	
	
	public function modalDetailPrice(){
		$this->load->view('Cost_quotation/modalDetailPrice');
	}
	
	public function modalDetailPriceDetail(){
		$this->load->view('Cost_quotation/modalDetailPriceDetail');
	}
	
	public function modalDetailBQ(){
		$this->load->view('Cost_quotation/modalDetailBQ');
	}
	
	public function modalviewDT(){
		$this->load->view('Cost_quotation/modalviewDT');
	}
	
	public function modalTotalCost(){
		$this->load->view('Cost_quotation/modalTotalCost');
	}
	
	public function modalDetailDT(){
		$this->load->view('Cost_quotation/modalDetailDT');
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
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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
		$data	= $this->input->post();
		
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
		$this->load->view('Cost_quotation/modalDetail2');
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

	public function print_penawaran2(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPenawaran.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Penawaran Sales Project BQ '.$id_bq); 
		
		PrintSetButtomPrice($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function print_penawaran3(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id_bq' => $id_bq
		);
		history('Print Hasil Penawaran Set Buttom Price '.$id_bq);
		$this->load->view('Print/print_set_buttom_price', $data);
		
		
	}
	
}