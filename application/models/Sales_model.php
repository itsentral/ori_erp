<?php
class Sales_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	//INDEX
	public function index_sales(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$query	 	= "SELECT * FROM color_status WHERE `status_aktif` = 'Y' ORDER BY urut ASC";
		$status		= $this->db->query($query)->result();
		
		$data = array(
			'title'			=> 'Indeks Of Request',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'status'		=> $status,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Request');
		$this->load->view('Sales/index',$data);
	}
	
	//DETAIL
	public function detail_ipp(){
		$no_ipp = $this->uri->segment(3);

		$qRequest 		= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' ";
		$RestRequest	= $this->db->query($qRequest)->result();

		$qReqCust 		= "	SELECT * FROM production_req_sp WHERE no_ipp = '".$no_ipp."' ";
		$RestReqCust	= $this->db->query($qReqCust)->result();
		$RestReqCustDet	= $this->db->query($qReqCust)->result_array();

		$qShipping 		= "	SELECT a.*, b.country_name FROM production_delivery a INNER JOIN country b ON a.country_code=b.country_code WHERE a.no_ipp = '".$no_ipp."' ";
		$RestShipping	= $this->db->query($qShipping)->result();

		$qCountry		= "SELECT * FROM country WHERE country_code='".$RestShipping[0]->country_code."'";
		$restCountry	= $this->db->query($qCountry)->result();
		
		$restFluida = array();
		if(!empty($RestReqCust)){
			$qFluida		= "SELECT * FROM list_fluida WHERE id_fluida='".$RestReqCust[0]->id_fluida."'";
			$restFluida		= $this->db->query($qFluida)->result();
		}

		$qCust			= "SELECT id_customer, nm_customer FROM customer";
		$CustList		= $this->db->query($qCust)->result_array();
		
		$data = array(
			'no_ipp' => $no_ipp,
			'RestRequest' => $RestRequest,
			'RestReqCust' => $RestReqCust,
			'RestReqCustDet' => $RestReqCustDet,
			'RestShipping' => $RestShipping,
			'restCountry' => $restCountry,
			'restFluida' => $restFluida,
			'CustList' => $CustList
		);

		$this->load->view('Sales/modalDetail', $data);
	}
	
	//ADD
	public function add_request(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$YM				= date('ym');
			$Y				= date('y');
			$country		= $data['country_code'];
			$DetailSp		= $data['ListDetailKomp'];
			
			//Pengurutan IPP
			
			$LocInt	= ($country == 'IDN')?'L':'E';
			//IPP19001E/L
			$qIPP			= "SELECT MAX(no_ipp) as maxP FROM production WHERE no_ipp LIKE 'IPP".$Y."%' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$IdIPP			= "IPP".$Y.$urut2.$LocInt;
			// echo $IdIPP; exit;
			
			//Customer
			$qCust			= "SELECT nm_customer FROM customer WHERE id_customer='".$data['id_customer']."' LIMIT 1";
			$NmCust			= $this->db->query($qCust)->result_array();
			
			$Data_Insert			= array(
				'no_ipp'			=> $IdIPP,
				'id_customer'		=> $data['id_customer'],
				'nm_customer'		=> $NmCust[0]['nm_customer'],
				'project'			=> $data['project'],
				'max_tol'			=> $data['max_tol'],
				'min_tol'			=> $data['min_tol'],
				'note'				=> $data['note'], 
				'validity'			=> $data['validity'], 
				'payment'			=> $data['payment'], 
				'ref_cust'			=> $data['ref_cust'], 
				'syarat_cust'		=> $data['syarat_cust'], 
				'created_by'		=> $this->session->userdata['ORI_User']['username'],
				'created_date'		=> date('Y-m-d H:i:s')
			);

			$Data_Insert2			= array(
				'no_ipp'			=> $IdIPP
			);
			
			$Data_Shipping			= array(
				'no_ipp'			=> $IdIPP,
				'date_delivery'		=> $data['date_delivery'],
				'address_delivery'	=> $data['address_delivery'],
				'country_code'		=> $data['country_code'],
				'metode_delivery'	=> $data['metode_delivery'],
				'alat_berat'		=> $data['alat_berat'],
				'isntalasi_by'		=> $data['isntalasi_by'],
				'packing'			=> $data['packing'],
				'garansi'			=> $data['garansi']
			);
			
			$ArrDetailPre	= array();
			foreach($DetailSp AS $val => $valx){
				//Fluida
				$qFluida			= "SELECT * FROM list_fluida WHERE id_fluida='".$valx['id_fluida']."' LIMIT 1";
				$NmLiner			= $this->db->query($qFluida)->result_array();
			
				$ArrDetailPre[$val]['no_ipp'] 		= $IdIPP;
				$ArrDetailPre[$val]['product'] 		= $valx['product'];
				$ArrDetailPre[$val]['type_resin'] 	= $valx['type_resin'];
				$ArrDetailPre[$val]['time_life'] 	= $valx['time_life'];
				$ArrDetailPre[$val]['id_fluida'] 	= $valx['id_fluida'];
				$ArrDetailPre[$val]['liner_thick'] 	= $valx['id_fluida'];
				$ArrDetailPre[$val]['stifness'] 	= $valx['stifness'];
				$ArrDetailPre[$val]['aplikasi'] 	= $valx['aplikasi'];
				$ArrDetailPre[$val]['pressure'] 	= $valx['pressure'];
				$ArrDetailPre[$val]['vacum_rate'] 	= $valx['vacum_rate'];
				$ArrDetailPre[$val]['note'] 		= $valx['note'];
				$ArrDetailPre[$val]['product_supply'] 		= $valx['product_supply'];

				$ArrDetailPre[$val]['resin_req_cust'] 			= $valx['resin_req_cust'];
				$ArrDetailPre[$val]['ck_minat_warna_tc'] 		= (!empty($valx['ck_minat_warna_tc']))?'Y':'N';
				$ArrDetailPre[$val]['ck_minat_warna_pigment'] 	= (!empty($valx['ck_minat_warna_pigment']))?'Y':'N';
				$ArrDetailPre[$val]['minat_warna_tc'] 			= (!empty($valx['minat_warna_tc']))?$valx['minat_warna_tc']:'';
				$ArrDetailPre[$val]['minat_warna_pigment'] 		= (!empty($valx['minat_warna_pigment']))?$valx['minat_warna_pigment']:'';

				$ArrDetailPre[$val]['std_asme'] 	= (!empty($valx['std_asme']))?'Y':'N';
				$ArrDetailPre[$val]['std_ansi'] 	= (!empty($valx['std_ansi']))?'Y':'N';
				$ArrDetailPre[$val]['std_astm'] 	= (!empty($valx['std_astm']))?'Y':'N';
				$ArrDetailPre[$val]['std_awwa'] 	= (!empty($valx['std_awwa']))?'Y':'N';
				$ArrDetailPre[$val]['std_bsi'] 		= (!empty($valx['std_bsi']))?'Y':'N';
				$ArrDetailPre[$val]['std_jis'] 		= (!empty($valx['std_jis']))?'Y':'N';
				$ArrDetailPre[$val]['std_sni'] 		= (!empty($valx['std_sni']))?'Y':'N';
				$ArrDetailPre[$val]['std_etc'] 		= (!empty($valx['std_etc']))?'Y':'N';
				$ArrDetailPre[$val]['std_din'] 		= (!empty($valx['std_din']))?'Y':'N';
				$ArrDetailPre[$val]['std_fff'] 		= (!empty($valx['std_fff']))?'Y':'N';
				$ArrDetailPre[$val]['std_rf'] 		= (!empty($valx['std_rf']))?'Y':'N';
				$ArrDetailPre[$val]['etc_1'] 		= (!empty($valx['std_etc']))?$valx['etc_1']:'';
				$ArrDetailPre[$val]['etc_2'] 		= (!empty($valx['std_etc']))?$valx['etc_2']:'';
				$ArrDetailPre[$val]['etc_3'] 		= (!empty($valx['std_etc']))?$valx['etc_3']:'';
				$ArrDetailPre[$val]['etc_4'] 		= (!empty($valx['std_etc']))?$valx['etc_4']:'';
				$ArrDetailPre[$val]['document'] 	= (!empty($valx['document']))?'Y':'N';
				$ArrDetailPre[$val]['document_1'] 	= (!empty($valx['document']))?$valx['document_1']:'';
				$ArrDetailPre[$val]['document_2'] 	= (!empty($valx['document']))?$valx['document_2']:'';
				$ArrDetailPre[$val]['document_3'] 	= (!empty($valx['document']))?$valx['document_3']:'';
				$ArrDetailPre[$val]['document_4'] 	= (!empty($valx['document']))?$valx['document_4']:'';
				$ArrDetailPre[$val]['color'] 		= (!empty($valx['color']))?'Y':'N';
				$ArrDetailPre[$val]['color_liner'] 		= (!empty($valx['color']))?$valx['color_liner']:'';
				$ArrDetailPre[$val]['color_structure'] 	= (!empty($valx['color']))?$valx['color_structure']:'';
				$ArrDetailPre[$val]['color_external'] 	= (!empty($valx['color']))?$valx['color_external']:'';
				$ArrDetailPre[$val]['color_topcoat'] 	= (!empty($valx['color']))?$valx['color_topcoat']:'';
				$ArrDetailPre[$val]['test'] 			= (!empty($valx['test']))?'Y':'N';
				$ArrDetailPre[$val]['test_1'] 			= (!empty($valx['test']))?$valx['test_1']:'';
				$ArrDetailPre[$val]['test_2'] 			= (!empty($valx['test']))?$valx['test_2']:'';
				$ArrDetailPre[$val]['test_3'] 			= (!empty($valx['test']))?$valx['test_3']:'';
				$ArrDetailPre[$val]['test_4'] 			= (!empty($valx['test']))?$valx['test_4']:'';
				$ArrDetailPre[$val]['sertifikat'] 		= (!empty($valx['sertifikat']))?'Y':'N';
				$ArrDetailPre[$val]['sertifikat_1'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_1']:'';
				$ArrDetailPre[$val]['sertifikat_2'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_2']:'';
				$ArrDetailPre[$val]['sertifikat_3'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_3']:'';
				$ArrDetailPre[$val]['sertifikat_4'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_4']:'';
				$ArrDetailPre[$val]['abrasi'] 			= (!empty($valx['abrasi']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_liner'] 		= (!empty($valx['konduksi_liner']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_structure'] 	= (!empty($valx['konduksi_structure']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_eksternal'] 	= (!empty($valx['konduksi_eksternal']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_topcoat'] 	= (!empty($valx['konduksi_topcoat']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_liner'] 		= (!empty($valx['tahan_api_liner']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_structure'] 	= (!empty($valx['tahan_api_structure']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_eksternal'] 	= (!empty($valx['tahan_api_eksternal']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_topcoat'] 	= (!empty($valx['tahan_api_topcoat']))?'Y':'N';
			}
			
			// print_r($ArrDetailPre);
			// exit;
			
			// echo "<pre>"; print_r($Data_Insert); print_r($Data_Shipping); print_r($ArrDetailPre);
			// exit;
		
			$this->db->trans_start();
			$this->db->insert('production', $Data_Insert);
			$this->db->insert('monitoring_ipp', $Data_Insert2);
			$this->db->insert('production_delivery', $Data_Shipping);
			$this->db->insert_batch('production_req_sp', $ArrDetailPre); 
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add request data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Add request data success. Thanks ...',
					'status'	=> 1
				);
				history("Add Request IPP ".$IdIPP);
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			//customer
			$dataType	= "SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC";
			$restType	= $this->db->query($dataType)->result_array();
			//country
			$qCountry	= "SELECT * FROM country ORDER BY country_name ASC";
			$restContry	= $this->db->query($qCountry)->result_array();
			//packing
			$qPack		= "SELECT * FROM list_packing WHERE flag='Y' ORDER BY urut ASC";
			$restPack	= $this->db->query($qPack)->result_array();
			//shipping
			$qShipping	= "SELECT * FROM list_shipping";
			$restShip	= $this->db->query($qShipping)->result_array();
			//application
			$qApp		= "SELECT * FROM product_category";
			$restApp	= $this->db->query($qApp)->result_array();
			//fluida
			$qFluida	= "SELECT * FROM list_fluida";
			$restFluida	= $this->db->query($qFluida)->result_array();
			//standard
			$qStandard		= "SELECT * FROM list_standard ORDER BY urut ASC";
			$restStandard	= $this->db->query($qStandard)->result_array();
			
			$data = array(
				'title'			=> 'Identification Of Customer Requests', 
				'action'		=> 'request',
				'CustList'		=> $restType,
				'CountryName'	=> $restContry,
				'PackningName'	=> $restPack,
				'ShippingName'	=> $restShip,
				'AppName'		=> $restApp,
				'FluidaName'	=> $restFluida,
				'StdName'		=> $restStandard
			);
			$this->load->view('Sales/request',$data);
		}
	}
	
	public function cancel_ipp(){
		if($this->input->post()){
			$no_ipp 		= $this->input->post('no_ipp');
			$status_reason 	= $this->input->post('status_reason');
			$data_session	= $this->session->userdata;
			// echo $no_ipp."<== IPP";
			$ArrCancel		= array(
				'status' 		=> 'CANCELED',
				'status_reason' => $status_reason,
				'modified_by' 	=> $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
				);
			
			// print_r($ArrCancel); exit;
			$this->db->trans_start();
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $ArrCancel);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Cancel IPP data failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Cancel IPP data success. Thanks ...',
					'status'	=> 1
				);				
				history('Cancel IPP with IPP : '.$no_ipp);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$no_ipp = $this->uri->segment(3);

			$qRequest 		= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' ";
			$RestRequest	= $this->db->query($qRequest)->result();
			
			$data = array(
				'no_ipp'	=> $no_ipp,
				'RestRequest'	=> $RestRequest
			);

			$this->load->view('Sales/modalCancel', $data);
		}
	}
	
	public function ajukan_ipp(){
		$no_ipp 		= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$username 		= $data_session['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		// echo $no_ipp."<== IPP";
		$ArrCancel		= array(
			'status' 		=> 'WAITING STRUCTURE BQ',
			'modified_by' 	=> $username,
			'modified_date' => $datetime
			);

		$ArrMonitoring		= array(
			'ipp_release_by' 	=> $username,
			'ipp_release_date' => $datetime
			);
		
		// print_r($ArrCancel); exit;
		$this->db->trans_start();
		$this->db->where('no_ipp', $no_ipp);
		$this->db->update('production', $ArrCancel);

		$this->db->where('no_ipp', $no_ipp);
		$this->db->update('monitoring_ipp', $ArrMonitoring);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'AJukan IPP failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'AJukan IPP success. Thanks ...',
				'status'	=> 1
			);				
			history('Mengajukan IPP to enggenering, '.$no_ipp);
		}
		echo json_encode($Arr_Data);
	}
	
	//EDIT IPP
	public function edit_ipp(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$YM				= date('ym');
			
			$no_ipp			= $data['no_ipp'];
			$DetailSp		= $data['ListDetailEdit'];
			
			// echo "<pre>";
			// print_r($DetailSp);
			// exit;
			
			//Customer
			// $qCust			= "SELECT nm_customer FROM customer WHERE id_customer='".$data['id_customer']."' LIMIT 1";
			// $NmCust			= $this->db->query($qCust)->result_array();
			
			$Data_Insert			= array(
				'project'			=> $data['project'],
				'note'				=> $data['note'], 
				'max_tol'			=> $data['max_tol'],
				'min_tol'			=> $data['min_tol'],
				'validity'			=> $data['validity'], 
				'payment'			=> $data['payment'], 
				'ref_cust'			=> $data['ref_cust'], 
				'syarat_cust'		=> $data['syarat_cust'],
				'ref_ke'			=> $data['ref_ke'] + 1, 
				'modified_by'		=> $this->session->userdata['ORI_User']['username'],
				'modified_date'		=> date('Y-m-d H:i:s')
			);
			
			$Data_Shipping			= array(
				'date_delivery'		=> date('Y-m-d', strtotime($data['date_delivery'])),
				'address_delivery'	=> $data['address_delivery'],
				'country_code'		=> $data['country_code'],
				'metode_delivery'	=> $data['metode_delivery'],
				'alat_berat'		=> $data['alat_berat'],
				'isntalasi_by'		=> $data['isntalasi_by'],
				'packing'			=> $data['packing'],
				'garansi'			=> $data['garansi']
			);
			
			$ArrDetailPre	= array();
			foreach($DetailSp AS $val => $valx){ 
				//Fluida
				$qFluida			= "SELECT * FROM list_fluida WHERE id_fluida='".$valx['id_fluida']."' LIMIT 1";
				$NmLiner			= $this->db->query($qFluida)->result_array();
				
				$ArrDetailPre[$val]['id'] 			= $valx['id'];
				$ArrDetailPre[$val]['no_ipp'] 		= $no_ipp;
				$ArrDetailPre[$val]['product'] 		= $valx['product'];
				$ArrDetailPre[$val]['type_resin'] 	= $valx['type_resin'];
				$ArrDetailPre[$val]['time_life'] 	= $valx['time_life'];
				$ArrDetailPre[$val]['id_fluida'] 	= $valx['id_fluida'];
				$ArrDetailPre[$val]['liner_thick'] 	= $valx['id_fluida'];
				$ArrDetailPre[$val]['stifness'] 	= $valx['stifness'];
				$ArrDetailPre[$val]['aplikasi'] 	= $valx['aplikasi'];
				$ArrDetailPre[$val]['pressure'] 	= $valx['pressure'];
				$ArrDetailPre[$val]['vacum_rate'] 	= $valx['vacum_rate'];
				$ArrDetailPre[$val]['note'] 		= $valx['note'];
				$ArrDetailPre[$val]['product_supply'] 		= $valx['product_supply'];

				$ArrDetailPre[$val]['resin_req_cust'] 			= $valx['resin_req_cust'];
				$ArrDetailPre[$val]['ck_minat_warna_tc'] 		= (!empty($valx['ck_minat_warna_tc']))?'Y':'N';
				$ArrDetailPre[$val]['ck_minat_warna_pigment'] 	= (!empty($valx['ck_minat_warna_pigment']))?'Y':'N';
				$ArrDetailPre[$val]['minat_warna_tc'] 			= (!empty($valx['minat_warna_tc']))?$valx['minat_warna_tc']:'';
				$ArrDetailPre[$val]['minat_warna_pigment'] 		= (!empty($valx['minat_warna_pigment']))?$valx['minat_warna_pigment']:'';

				$ArrDetailPre[$val]['std_asme'] 	= (!empty($valx['std_asme']))?'Y':'N';
				$ArrDetailPre[$val]['std_ansi'] 	= (!empty($valx['std_ansi']))?'Y':'N';
				$ArrDetailPre[$val]['std_astm'] 	= (!empty($valx['std_astm']))?'Y':'N';
				$ArrDetailPre[$val]['std_awwa'] 	= (!empty($valx['std_awwa']))?'Y':'N';
				$ArrDetailPre[$val]['std_bsi'] 		= (!empty($valx['std_bsi']))?'Y':'N';
				$ArrDetailPre[$val]['std_jis'] 		= (!empty($valx['std_jis']))?'Y':'N';
				$ArrDetailPre[$val]['std_sni'] 		= (!empty($valx['std_sni']))?'Y':'N';
				$ArrDetailPre[$val]['std_etc'] 		= (!empty($valx['std_etc']))?'Y':'N';
				$ArrDetailPre[$val]['std_din'] 		= (!empty($valx['std_din']))?'Y':'N';
				$ArrDetailPre[$val]['std_fff'] 		= (!empty($valx['std_fff']))?'Y':'N';
				$ArrDetailPre[$val]['std_rf'] 		= (!empty($valx['std_rf']))?'Y':'N';
				$ArrDetailPre[$val]['etc_1'] 		= (!empty($valx['std_etc']))?$valx['etc_1']:'';
				$ArrDetailPre[$val]['etc_2'] 		= (!empty($valx['std_etc']))?$valx['etc_2']:'';
				$ArrDetailPre[$val]['etc_3'] 		= (!empty($valx['std_etc']))?$valx['etc_3']:'';
				$ArrDetailPre[$val]['etc_4'] 		= (!empty($valx['std_etc']))?$valx['etc_4']:'';
				$ArrDetailPre[$val]['document'] 	= (!empty($valx['document']))?'Y':'N';
				$ArrDetailPre[$val]['document_1'] 	= (!empty($valx['document']))?$valx['document_1']:'';
				$ArrDetailPre[$val]['document_2'] 	= (!empty($valx['document']))?$valx['document_2']:'';
				$ArrDetailPre[$val]['document_3'] 	= (!empty($valx['document']))?$valx['document_3']:'';
				$ArrDetailPre[$val]['document_4'] 	= (!empty($valx['document']))?$valx['document_4']:'';
				$ArrDetailPre[$val]['color'] 		= (!empty($valx['color']))?'Y':'N';
				$ArrDetailPre[$val]['color_liner'] 		= (!empty($valx['color']))?$valx['color_liner']:'';
				$ArrDetailPre[$val]['color_structure'] 	= (!empty($valx['color']))?$valx['color_structure']:'';
				$ArrDetailPre[$val]['color_external'] 	= (!empty($valx['color']))?$valx['color_external']:'';
				$ArrDetailPre[$val]['color_topcoat'] 	= (!empty($valx['color']))?$valx['color_topcoat']:'';
				$ArrDetailPre[$val]['test'] 			= (!empty($valx['test']))?'Y':'N';
				$ArrDetailPre[$val]['test_1'] 			= (!empty($valx['test']))?$valx['test_1']:'';
				$ArrDetailPre[$val]['test_2'] 			= (!empty($valx['test']))?$valx['test_2']:'';
				$ArrDetailPre[$val]['test_3'] 			= (!empty($valx['test']))?$valx['test_3']:'';
				$ArrDetailPre[$val]['test_4'] 			= (!empty($valx['test']))?$valx['test_4']:'';
				$ArrDetailPre[$val]['sertifikat'] 		= (!empty($valx['sertifikat']))?'Y':'N';
				$ArrDetailPre[$val]['sertifikat_1'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_1']:'';
				$ArrDetailPre[$val]['sertifikat_2'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_2']:'';
				$ArrDetailPre[$val]['sertifikat_3'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_3']:'';
				$ArrDetailPre[$val]['sertifikat_4'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_4']:'';
				$ArrDetailPre[$val]['abrasi'] 			= (!empty($valx['abrasi']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_liner'] 		= (!empty($valx['konduksi_liner']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_structure'] 	= (!empty($valx['konduksi_structure']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_eksternal'] 	= (!empty($valx['konduksi_eksternal']))?'Y':'N';
				$ArrDetailPre[$val]['konduksi_topcoat'] 	= (!empty($valx['konduksi_topcoat']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_liner'] 		= (!empty($valx['tahan_api_liner']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_structure'] 	= (!empty($valx['tahan_api_structure']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_eksternal'] 	= (!empty($valx['tahan_api_eksternal']))?'Y':'N';
				$ArrDetailPre[$val]['tahan_api_topcoat'] 	= (!empty($valx['tahan_api_topcoat']))?'Y':'N';
				$ArrDetailPre[$val]['modified_by'] 			= $this->session->userdata['ORI_User']['username'];
				$ArrDetailPre[$val]['modified_date'] 		= date('Y-m-d H:i:s');
			}
			
			// print_r($ArrDetailPre); 
			// exit;
			
			// echo "<pre>"; print_r($Data_Insert); print_r($Data_Shipping); print_r($ArrDetailPre);
			// exit;
		
			$this->db->trans_start();
			// $this->db->insert('production', $Data_Insert);
			// $this->db->insert('production_delivery', $Data_Shipping);
			// $this->db->insert_batch('production_req_sp', $ArrDetailPre);
			$this->db->query("INSERT hist_production (
											no_ipp,id_customer,nm_customer,project,note,status,status_reason,ref_ke,modified_by,modified_date 
										) 
										SELECT
											no_ipp,id_customer,nm_customer,project,note,status,status_reason,ref_ke,'".$this->session->userdata['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
										FROM
											production 
										WHERE
											no_ipp = '".$no_ipp."'");
											
			$this->db->query("INSERT hist_production_delivery (
											no_ipp,date_delivery,address_delivery,country_code,metode_delivery,truck,
											vendor,qty_truck,metode_lain,alat_berat,isntalasi_by,garansi,
											packing,packing_fitting_qty,packing_dg_qty,packing_pipa_qty,
											modified_by,modified_date 
										) 
										SELECT
											no_ipp,date_delivery,address_delivery,country_code,metode_delivery,truck,
											vendor,qty_truck,metode_lain,alat_berat,isntalasi_by,garansi,
											packing,packing_fitting_qty,packing_dg_qty,packing_pipa_qty,
											'".$this->session->userdata['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
										FROM
											production_delivery 
										WHERE
											no_ipp = '".$no_ipp."'");
			$this->db->insert_batch('hist_production_req_sp', $ArrDetailPre);
			
			$this->db->where('no_ipp', $no_ipp)->update('production', $Data_Insert);
			$this->db->where('no_ipp', $no_ipp)->update('production_delivery', $Data_Shipping);
			$this->db->update_batch('production_req_sp', $ArrDetailPre, 'id');
		
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit request data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit request data success. Thanks ...',
					'status'	=> 1
				);
				history("Edit Request IPP ".$no_ipp);
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{
			$no_ipp = $this->uri->segment(3);

			$qRequest 		= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' ";
			$RestRequest	= $this->db->query($qRequest)->result();

			$qReqCust 		= "	SELECT * FROM production_req_sp WHERE no_ipp = '".$no_ipp."' ";
			$RestReqCust	= $this->db->query($qReqCust)->result_array();

			$qShipping 		= "	SELECT * FROM production_delivery WHERE no_ipp = '".$no_ipp."' ";
			$RestShipping	= $this->db->query($qShipping)->result();

			$qCountry		= "SELECT * FROM country WHERE country_code='".$RestShipping[0]->country_code."'";
			$restCountry	= $this->db->query($qCountry)->result();
			//customer
			$qCust			= "SELECT id_customer, nm_customer FROM customer";
			$CustList		= $this->db->query($qCust)->result_array();
			//country
			$qCountry	= "SELECT * FROM country ORDER BY country_name ASC";
			$CountryName	= $this->db->query($qCountry)->result_array();
			//packing
			$qPack		= "SELECT * FROM list_packing WHERE flag='Y' ORDER BY urut ASC";
			$PackningName	= $this->db->query($qPack)->result_array();
			//shipping
			$qShipping	= "SELECT * FROM list_shipping";
			$ShippingName	= $this->db->query($qShipping)->result_array();
			//application
			$qApp		= "SELECT * FROM product_category";
			$restApp	= $this->db->query($qApp)->result_array();
			//fluida
			$qFluida	= "SELECT * FROM list_fluida";
			$restFluida	= $this->db->get_where('list_help',array('group_by'=>'liner'))->result_array();
			//standard
			$qStandard		= "SELECT * FROM list_standard ORDER BY urut ASC";
			$restStandard	= $this->db->query($qStandard)->result_array();
			//color
			$qColor		= "SELECT * FROM list_color ORDER BY color_name ASC";
			$restColor	= $this->db->query($qColor)->result_array();
			
			$data = array(
				'no_ipp'		=> $no_ipp,
				'RestRequest'	=> $RestRequest,
				'RestReqCust'	=> $RestReqCust,
				'RestShipping'	=> $RestShipping,
				'restCountry'	=> $restCountry,
				'CustList'		=> $CustList,
				'CountryName'	=> $CountryName,
				'PackningName'	=> $PackningName,
				'ShippingName'	=> $ShippingName,
				'restApp'		=> $restApp,
				'restFluida'	=> $restFluida,
				'restColor'		=> $restColor,
				'restStandard'	=> $restStandard
			);
			
			$this->load->view('Sales/modalEdit', $data);
		}
	}
	
	
	//ADD COUNTRY
	public function add_country(){
		if($this->input->post()){
			$data				= $this->input->post();
		
			$getNum	= $this->db->query("SELECT * FROM country WHERE country_code='".strtoupper($data['country'])."' ")->num_rows();
			$getCountry	= $this->db->query("SELECT * FROM country_all WHERE iso3='".strtoupper($data['country'])."' ")->result_array();
			
			$insertData	= array(
				'country_code'	=> strtoupper($data['country']),
				'country_name'	=> $getCountry[0]['name']
			);
			
			if($getNum < 1){
				$this->db->trans_start();
					$this->db->insert('country', $insertData);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Add Country. Please try again later ...',
						'status'	=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Success Add Country. Thanks ...',
						'status'	=> 1
					);
					history('Add Country Data'); 
				}
			}
			else{
				$Arr_Kembali	= array(
						'pesan'		=>'Country Name Already exists',
						'status'	=> 0
					);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$result		= $this->db->query("SELECT a.iso3, a.`name` FROM country_all a LEFT JOIN country b ON a.iso3 = b.country_code WHERE b.country_code IS NULL AND a.iso3 IS NOT NULL ORDER BY a.`name` ASC ")->result_array();
		
			$data = array(
				'result' => $result
			);
			
			$this->load->view('Sales/modalAddCountry', $data);
		}
	}
	
	//Print
	public function print_ipp(){
		$no_ipp			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'no_ipp' => $no_ipp
		);
		history('Print IPP '.$no_ipp); 
		$this->load->view('Print/print_ipp', $data);
	}
	
	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	
	public function get_data_json_sales_ipp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_sales_ipp(
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['no_ipp']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_customer']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>"; 
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['ref_ke']))."</span></div>";
				$dataModif = (!empty($row['ref_ke']))?$row['modified_by']:$row['created_by'];
				$get_name = get_name('users','nm_lengkap','username',$dataModif);
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($get_name))."</div>";
				$dataModifx = (!empty($row['ref_ke']))?$row['modified_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($dataModifx))."</div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='BQ-".$row['no_ipp']."' AND (id_product= '' OR id_product  is null) ")->num_rows();
				
				$class = Color_status($row['status']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['status']."</span></div>";
				$updX = "";
				$delX = "";
				$PrintX	= "";
				$ajukan	= "";
					
				if($row['status'] == 'WAITING IPP RELEASE'){
					if($Arr_Akses['update']=='1'){
						$updX	= "<button type='button' class='btn btn-sm btn-primary' id='EditIPP' title='Edit IPP' data-no_ipp='".$row['no_ipp']."'><i class='fa fa-edit'></i></button>";
					}
					if($Arr_Akses['delete']=='1'){
						$delX	= "<button type='button' class='btn btn-sm btn-danger' id='CancelIPP' title='Cancel IPP' data-no_ipp='".$row['no_ipp']."'><i class='fa fa-close'></i></button>";
					}
					$ajukan	= "<button type='button' class='btn btn-sm btn-info ajukan_ipp' title='Release IPP' data-ipp='".$row['no_ipp']."'><i class='fa fa-check'></i></button>";
				}
				if($Arr_Akses['download']=='1'){
					$PrintX	= "<a href='".site_url($this->uri->segment(1).'/printIPP/'.$row['no_ipp'])."' class='btn btn-sm btn-success' target='_blank' title='Print IPP' data-role='qtip'><i class='fa fa-print'></i></a>";
				}
			$nestedData[]	= "<div align='left'>
									<button type='button' id='detailSO' data-no_ipp='".$row['no_ipp']."' class='btn btn-sm btn-warning' title='View IPP' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$updX."
									".$ajukan."
									".$delX."
									".$PrintX."
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

	public function query_data_json_sales_ipp($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($status <> '0'){
			$where = " AND a.`status`='".$status."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				production a,
				(SELECT @row:=0) r
		    WHERE a.deleted = 'N' ".$where." AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer',
			3 => 'project'
			
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}
