<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Component extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('component_model');
		$this->load->model('comp_pipe_model');

		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	//==========================================================================================================================
	//=======================================================COMPONENT==========================================================
	//==========================================================================================================================
	
	public function index(){
		$this->component_model->index_component();
	}
	
	public function server_side_component(){
		$this->component_model->get_data_json_component();
	}
	
	public function pipe(){
		$this->comp_pipe_model->pipe();
	}
	
	public function pipe_edit(){
		$this->comp_pipe_model->pipe_edit();
	}
	
	//==========================================================================================================================
	//====================================================END COMPONENT=========================================================
	//==========================================================================================================================

	

	public function master(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$productN		= $this->uri->segment(3);
		$get_Data		= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.parent_product='".$productN."' AND a.status='APPROVED' AND a.deleted ='N'")->result();
		$menu_akses		= $this->master_model->getMenu();
		$getSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted = 'N' AND `status` = 'Y' ORDER BY pressure ASC, resin_system ASC, liner ASC")->result_array();
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();


		$data = array(
			'title'			=> 'Indeks Of Component Master',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'listseries'	=> $getSeries,
			'listkomponen'	=> $getKomp,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history("View Master ".$productN);
		$this->load->view('Component/master',$data);
	}

	public function approve(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$productN		= $this->uri->segment(3);
		$get_Data		= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.status='WAITING APPROVAL' AND a.deleted ='N' AND a.cust IS NULL")->result();
		$menu_akses		= $this->master_model->getMenu();

		$data = array(
			'title'			=> 'Indeks Of Component Approve',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history("View Master ".$productN);
		$this->load->view('Component/approve',$data);
	}

	public function modalDetail(){
		$this->load->view('Component/modalDetail');
	}

	public function modalWeight(){
		$this->load->view('Component/modalWeight');
	}

	public function modalApprove(){
		$this->load->view('Component/modalApprove');
	}

	public function approved(){
		$id_produk 	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$stsX	= ($this->input->post('status') == 'Y')?'APPROVED':'REJECTED';
		// echo "Post-".$this->input->post('status');
		$Arr_Edit	= array(
			'status' => $stsX,
			'approve_reason' => $this->input->post('approve_reason'),
			'approve_by' => $data_session['ORI_User']['username'],
			'approve_date' => date('Y-m-d H:i:s')
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
		$this->db->where('id_product', $id_produk);
		$this->db->update('component_header', $Arr_Edit);
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
			history('Approve with Kode : '.$id_produk);
		}
		echo json_encode($Arr_Data);
	}

	public function pipe_slongsong_edit(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];

			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];

			if(!empty($data['ListDetailAdd'])){
				$ListDetailAdd1	= $data['ListDetailAdd'];
			}
			if(!empty($data['ListDetailAdd'])){
				$ListDetailAdd2	= $data['ListDetailAdd2'];
			}

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;
			$pipe_for		= $data['pipe_for'];

			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
			}


			$kode_product	= "OS-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-".$pipe_for.$Tambahan;
			// echo $kode_product; exit;

			// echo "Masuk Save";
			// exit;
			$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'pipe slongsong',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'cust'					=> $custX,
				'pressure'				=> $pressure,
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				'standart_by'			=> $data['toleransi'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],

				'panjang'				=> str_replace(',', '', $data['length']),
				'design'				=> $data['design'],
				'area'					=> $data['area'],
				'est'					=> $data['estimasi'],
				'min_toleransi'			=> $data['min_toleran'],
				'max_toleransi'			=> $data['max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);


			// print_r($ArrHeader); exit;

			// Detail1
			$ArrDetail1	= array();
			foreach($ListDetail AS $val => $valx){
				$IDMat1			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat1			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

				$ArrDetail1[$val]['id_product'] 	= $kode_product;
				$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
				$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail1[$val]['id_material'] 	= $IDMat1;
				$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail1[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail1[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail1[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail1[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail1[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail1);
			// exit;
			//Detail2
			$ArrDetail2	= array();
			foreach($ListDetail2 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2[$val]['id_product'] 	= $kode_product;
				$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
				$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2);
			// exit;

			$ArrDetailPlus1	= array();
			foreach($ListDetailPlus AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus1[$val]['perse'] = $perseM;
				$ArrDetailPlus1[$val]['last_full'] 		= '';
				$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus1);

			$ArrDetailPlus2	= array();
			foreach($ListDetailPlus2 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2[$val]['perse'] = $perseM;
				$ArrDetailPlus2[$val]['last_full'] 		= '';
				$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2);

			$ArrFooter	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name'],
					'total'			=> $data['thickLin'],
					'min'			=> $data['minLin'],
					'max'			=> $data['maxLin'],
					'hasil'			=> $data['hasilLin']
			);

			$ArrFooter2	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2'],
					'total'			=> $data['thickStr'],
					'min'			=> $data['minStr'],
					'max'			=> $data['maxStr'],
					'hasil'			=> $data['hasilStr']
			);

			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// exit;

			if(!empty($data['ListDetailAdd'])){
				$ArrDataAdd1 = array();
				foreach($ListDetailAdd1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
					$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd1[$val]['perse'] 		= $perseM;
					$ArrDataAdd1[$val]['last_full'] 	= '';
					$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd2'])){
				$ArrDataAdd2 = array();
				foreach($ListDetailAdd2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2[$val]['perse'] 		= $perseM;
					$ArrDataAdd2[$val]['last_full'] 	= '';
					$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}

			$ArrBqHeaderHist = array();
			$ArrBqDetailHist = array();
			$ArrBqDetailPlusHist = array();
			$ArrBqDetailAddHist = array();
			$ArrBqFooterHist = array();
			$ArrBqTimeHist = array();
			//Insert Component Header To Hist
			$qHeaderHist	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$kode_product."' ")->result_array();
			$qHeaderHistNum	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$kode_product."' ")->num_rows();
			if($qHeaderHistNum > 0){
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
					$ArrBqHeaderHist[$val2HistA]['deleted_date']		= date('Y-m-d H:i:s');
				}
			}

			//Insert Component Detail To Hist
			$qDetailHist	= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$kode_product."' ")->result_array();
			$qDetailHistNum	= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$kode_product."' ")->num_rows();
			if($qDetailHistNum > 0){
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
					$ArrBqDetailHist[$val2Hist]['deleted_date']		= date('Y-m-d H:i:s');
				}
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$kode_product."' ")->result_array();
			$qDetailPlusHistNum	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$kode_product."' ")->num_rows();
			if($qDetailPlusHistNum > 0){
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
					$ArrBqDetailPlusHist[$val3Hist]['deleted_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailPlusHist[$val3Hist]['deleted_date']	= date('Y-m-d H:i:s');
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$kode_product."' ")->result_array();
			$qDetailAddNumHist	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$kode_product."' ")->num_rows();
			if($qDetailAddNumHist > 0){
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
					$ArrBqDetailAddHist[$val4Hist]['deleted_date']	= date('Y-m-d H:i:s');
				}
			}

			//Insert Component Footer To Hist
			$qDetailFooterHist		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$kode_product."' ")->result_array();
			$qDetailFooterHistNum	= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$kode_product."' ")->num_rows();
			if($qDetailFooterHistNum > 0){
				foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
					$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
					$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
					$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
					$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
					$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
					$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
					$ArrBqFooterHist[$val5Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
					$ArrBqFooterHist[$val5Hist]['deleted_date']	= date('Y-m-d H:i:s');
				}
			}

			//Insert Component Time To Hist
			// $qDetailTimeHist	= $this->db->query("SELECT * FROM component_time WHERE id_product='".$kode_product."' ")->result_array();
			// $qDetailTimeHistNum	= $this->db->query("SELECT * FROM component_time WHERE id_product='".$kode_product."' ")->num_rows();
			// if($qDetailTimeHistNum > 0){
				// foreach($qDetailTimeHist AS $val6Hist => $valx5Hist){
					// $ArrBqTimeHist[$val6Hist]['id_product']	= $valx5Hist['id_product'];
					// $ArrBqTimeHist[$val6Hist]['process']		= $valx5Hist['detail_name'];
					// $ArrBqTimeHist[$val6Hist]['sub_process']	= $valx5Hist['total'];
					// $ArrBqTimeHist[$val6Hist]['time_process']	= $valx5Hist['min'];
					// $ArrBqTimeHist[$val6Hist]['man_power']	= $valx5Hist['max'];
					// $ArrBqTimeHist[$val6Hist]['man_hours']	= $valx5Hist['hasil'];
					// $ArrBqTimeHist[$val6Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
					// $ArrBqTimeHist[$val6Hist]['deleted_date']	= date('Y-m-d H:i:s');
				// }
			// }

			// echo "<pre>";
			// print_r($ArrHeader);
			// print_r($ArrDetail1);
			// print_r($ArrDetail2);
			// print_r($ArrDetailPlus1);
			// print_r($ArrDetailPlus2);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);

			// if(!empty($data['ListDetailAdd'])){
				// print_r($ArrDataAdd1);
			// }
			// if(!empty($data['ListDetailAdd2'])){
				// print_r($ArrDataAdd2);
			// }
			// echo "Tahan dulu ya !!!";
			// exit;

			$this->db->trans_start();
				//Insert Batch Histories
				if($qHeaderHistNum > 0){
					$this->db->insert_batch('hist_component_header', $ArrBqHeaderHist);
				}
				if($qDetailHistNum > 0){
					$this->db->insert_batch('hist_component_detail', $ArrBqDetailHist);
				}
				if($qDetailPlusHistNum > 0){
					$this->db->insert_batch('hist_component_detail_plus', $ArrBqDetailPlusHist);
				}
				if($qDetailAddNumHist > 0){
					$this->db->insert_batch('hist_component_detail_add', $ArrBqDetailAddHist);
				}
				if($qDetailFooterHistNum > 0){
					$this->db->insert_batch('hist_component_footer', $ArrBqFooterHist);
				}
				// if($qDetailTimeHistNum > 0){
					// $this->db->insert_batch('hist_component_time', $ArrBqTimeHist);
				// }

				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product));
				// $this->db->delete('component_time', array('id_product' => $kode_product));

				$this->db->insert('component_header', $ArrHeader);
				$this->db->insert_batch('component_detail', $ArrDetail1);
				$this->db->insert_batch('component_detail', $ArrDetail2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				if(!empty($data['ListDetailAdd'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
				}
				if(!empty($data['ListDetailAdd2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Estimation failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
					'status'	=> 1
				);
				history('Edit Est Pipe Slongsong code : '.$kode_product.' to '.$kode_product);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//Edit Komponent
			$id_product	= $this->uri->segment(3);
			$ComponentHeader			= $this->db->query("SELECT a.*, b.id FROM component_header a LEFT JOIN product b ON a.nm_product = b.nm_product WHERE a.id_product='".$id_product."' LIMIT 1 ")->result();

			$ComponentDetailLiner		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$ComponentDetailLinerPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$ComponentDetailLinerAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$NumRowsLinerAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->num_rows();
			$ComponentFooter			= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ComponentDetailStructure		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();
			$ComponentDetailStructurePlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();
			$ComponentDetailStructureAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();
			$NumRowsStructureAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->num_rows();
			$ComponentFooterStructure		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();

			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe slongsong' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();

			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();

			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();

			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();

			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'				=> 'REVISED PIPE ESTIMATE',
				'action'			=> 'pipe_slongsong_edit',
				'product'			=> $ListProduct,
				'resin_system'		=> $ListResinSystem,
				'pressure'			=> $ListPressure,
				'liner'				=> $ListLiner,
				'series'			=> $ListSeries,
				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,

				'standard'			=> $ListCustomer,
				'customer'			=> $ListCustomer2,

				'ListRealise'		=> $List_Realese,
				'ListPlastic'		=> $List_PlasticFirm,
				'ListVeil'			=> $List_Veil,
				'ListResin'			=> $List_Resin,
				'ListMatCsm'		=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl,


				'header'			=> $ComponentHeader,
				'detLiner'			=> $ComponentDetailLiner,
				'detLinerPlus'		=> $ComponentDetailLinerPlus,
				'detLinerAdd'		=> $ComponentDetailLinerAdd,
				'detLinerNumRows'	=> $NumRowsLinerAdd,
				'footer'			=> $ComponentFooter,

				'detStructure'			=> $ComponentDetailStructure,
				'detStructurePlus'		=> $ComponentDetailStructurePlus,
				'detStructureAdd'		=> $ComponentDetailStructureAdd,
				'detStructureNumRows'	=> $NumRowsStructureAdd,
				'footerStructure'		=> $ComponentFooterStructure
			);
			$this->load->view('Component/edit/pipe_slongsong_edit', $data);
		}
	}

	public function getMaterialx(){
		$id_material	= $this->input->post('id_material');
		$diameter 		= $this->input->post('diameter');
		$resin			= $this->input->post('resin');
		$id_category	= $this->input->post('id_ori');
		$resinOri		= $this->input->post('resinOri');
		

		// echo $nomor;
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
			//9 = awal 11= add
			// echo $sqlMicron."<br>";
			
			$weight			= 0;
			$bw				= 0;
			$jumRoov		= 0;
			$thickness		= 0;
			if($NumMic != 0){
				$weight		=  floatval($restMicron[0]['nilai_standard']);
				if($weight != 0 OR $weight != null OR $weight != ''){
					$bw			= floatval($this->input->post('bw'));
					$jumRoov		= floatval($this->input->post('jumlah')); 
					// echo $weight."<br>";
					// echo $bw."<br>";
					// echo $jumRoov."<br>";
					// echo $resin."<br>";
					if($bw != 0){
						$thickness	= (($weight/1000)/ $bw * $jumRoov * (2 / 2.56)) + (($weight/1000)/ $bw * $jumRoov * (2 / 1.2) * $resin);
					}
				}
			}

			// echo $thickness; 
			
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

	public function getMicronPlastic(){
		$id_material 	= $this->input->post('id_material');
		$top_diameter 	= $this->input->post('top_diameter');
		// echo $top_diameter."<br>";
		$sqlMicron	= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='thickness' LIMIT 1 ";
		$restMicron	= $this->db->query($sqlMicron)->result_array();

		if(!empty($restMicron[0]['nilai_standard'])){
			if($top_diameter < 40){
				$micron	= 0;
				// echo "Lana";
			}
			else{
				// echo "Sini";
				if($top_diameter < 400){
					$micron	= $restMicron[0]['nilai_standard']/1000000;
				}
				else{
					$micron	= $restMicron[0]['nilai_standard']/1000000;
				}
			}
		}
		else{
			$micron	= 0;
		}

		$ArrJson	= array(
			'micron' => $micron
		);

		// print_r($ArrJson); exit;
		echo json_encode($ArrJson);
	}

	public function getVeil(){ 
		$id_material 	= $this->input->post('id_material');
		$resin1 		= $this->input->post('resin1');
		$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
		$restMicron		= $this->db->query($sqlMicron)->result_array();
		$NumMic			= $this->db->query($sqlMicron)->num_rows();

		$micron			= 0;
		$thickness		= 0;
		if($NumMic != 0){
			$micron		=  $restMicron[0]['nilai_standard'];
			$thickness	= ($micron/1000/2.56)+($micron/1000/1.2*$resin1);
		}

		$resin = "";
		$LayerR	= "";
		if($id_material == 'MTL-1903000'){
			$resin = "MTL-1903000";
			$LayerR	= 0;
		}

		$ArrJson		= array(
			'micron' 	=> $micron,
			'thickness'	=> $thickness,
			'resin'		=> $resin,
			'layer'		=> $LayerR
		);
		echo json_encode($ArrJson);
	}

	public function getVeil2(){
		$id_material 	= $this->input->post('id_material');
		$resin1 		= $this->input->post('resin2');

		$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
		$restMicron		= $this->db->query($sqlMicron)->result_array();
		$NumMic			= $this->db->query($sqlMicron)->num_rows();

		$micron			= 0;
		$thickness		= 0;
		if($NumMic != 0){
			$micron		=  $restMicron[0]['nilai_standard'];
			$thickness	= ($micron/1000/2.56)+($micron/1000/1.2*$resin1);
		}

		$resinX = "";
		$LayerR	= "";
		if($id_material == 'MTL-1903000'){
			$resinX = 'MTL-1903000';
			$LayerR	= 0;
		}

		$ArrJson		= array(
				'micron' 	=> $micron,
				'resin'		=> $resinX,
				'thickness'	=> $thickness,
				'layer'		=> $LayerR
			);
		echo json_encode($ArrJson);
	}

	public function getCsm(){
		$id_material 	= $this->input->post('id_material');
		$resin1 		= $this->input->post('resin3');

		$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
		$restMicron		= $this->db->query($sqlMicron)->result_array();
		$NumMic			= $this->db->query($sqlMicron)->num_rows();

		$micron			= 0;
		$thickness		= 0;
		if($NumMic != 0){
			$micron		=  $restMicron[0]['nilai_standard'];
			$thickness	= ($micron/1000/2.56)+($micron/1000/1.2*$resin1);
		}

		$resinX = "";
		$LayerR	= "";
		if($id_material == 'MTL-1903000'){
			$resinX = 'MTL-1903000';
			$LayerR	= 0;
		}

		$ArrJson		= array(
			'micron' 	=> $micron,
			'resin'		=> $resinX,
			'thickness'	=> $thickness,
			'layer'		=> $LayerR
		);
		echo json_encode($ArrJson);
	}

	public function getCsmX(){
		$id_material 	= $this->input->post('id_material');
		$resin1 		= $this->input->post('resin3');

		$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
		$restMicron		= $this->db->query($sqlMicron)->result_array();
		$NumMic			= $this->db->query($sqlMicron)->num_rows();

		$micron			= 0;
		$thickness		= 0;
		if($NumMic != 0){
			$micron		=  $restMicron[0]['nilai_standard'];
			$thickness	= ($micron/1000/2.56)+($micron/1000/1.2*$resin1);
		}

		$resinX = "";
		$LayerR	= "";
		if($id_material == 'MTL-1903000'){
			$resinX = 'MTL-1903000';
			$LayerR	= 0;
		}

		$ArrJson		= array(
			'micron' 	=> $micron,
			'resin'		=> $resinX,
			'thickness'	=> $thickness,
			'layer'		=> $LayerR
		);
		echo json_encode($ArrJson);
	}

	public function getCsm2(){
		$id_material 	= $this->input->post('id_material');
		$resin1 		= $this->input->post('resin4');

		$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
		$restMicron		= $this->db->query($sqlMicron)->result_array();
		$NumMic			= $this->db->query($sqlMicron)->num_rows();

		$micron			= 0;
		$thickness			= 0;
		if($NumMic != 0){
			$micron		=  $restMicron[0]['nilai_standard'];
			$thickness	= ($micron/1000/2.56)+($micron/1000/1.2*$resin1);
		}

		$resinX = "";
		$LayerR	= "";
		if($id_material == 'MTL-1903000'){
			$resinX = 'MTL-1903000';
			$LayerR	= 0;
		}

		$ArrJson		= array(
			'micron' 	=> $micron,
			'resin'		=> $resinX,
			'thickness'	=> $thickness,
			'layer'		=> $LayerR
		);
		echo json_encode($ArrJson);
	}

	public function getWoodR(){
		$id_material 	= $this->input->post('id_material');
		$resin1 		= $this->input->post('resin4');

		if(!empty($resin1)){

			$sqlWrW			= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
			$restWrW		= $this->db->query($sqlWrW)->result_array();
			$NumWrW			= $this->db->query($sqlWrW)->num_rows();

			// echo $sqlWrT;
			$weight			= 0;
			$thickness		= 0;
			if($NumWrW != 0){
				$weight		=  $restWrW[0]['nilai_standard'];
				$thickness	= ($weight/1000/2.56)+($weight/1000/1.2*$resin1);
			}
		}
		elseif(empty($resin1)){
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
		}

		$resinX = "";
		$LayerR	= "";
		if($id_material == 'MTL-1903000'){
			$resinX = 'MTL-1903000';
			$LayerR	= 0;
		}

		$ArrJson		= array(
			'weight' 	=> $weight,
			'resin'		=> $resinX,
			'thickness'	=> $thickness,
			'layer'		=> $LayerR
		);
		echo json_encode($ArrJson);
	}

	public function getRooving(){
		$id_material 	= $this->input->post('id_material');
		$r_cont			= $this->input->post('resin');
		$diameter2		= $this->input->post('diameter2');
		$standart_code	= $this->input->post('standart_code');
		$beda			= $this->input->post('beda');

		// if(!empty($this->input->post('diameter'))){
			$diameter	= $this->input->post('diameter');
		// }
		// echo $resinutama;
		$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
		$restMicron		= $this->db->query($sqlMicron)->result_array();
		$NumMic			= $this->db->query($sqlMicron)->num_rows();

		$sqlDef			= "SELECT * FROM help_default WHERE standart_code ='".$standart_code."' AND diameter ='".$diameter."' AND diameter2 ='".$diameter2."'  LIMIT 1 ";
		$restDef		= $this->db->query($sqlDef)->result_array();
		$NumDef			= $this->db->query($sqlDef)->num_rows();

		$weight			= 0;
		$bw				= 0;
		$jumRoov		= 0;
		$thickness		= 0;
		if($NumMic != 0){
			$weight		=  floatval($restMicron[0]['nilai_standard']);
			if($weight != 0 OR $weight != null OR $weight != ''){
				//diameter sudah di set
				if($NumDef > 0){
					if($beda == 'utama'){
						$bw			= $restDef[0]['str_faktor_rv_bw'];
						$jumRoov	= $restDef[0]['str_faktor_rv_jb'];
					}
					if($beda == 'add'){
						$bw			= $restDef[0]['str_faktor_rv_add_bw'];
						$jumRoov	= $restDef[0]['str_faktor_rv_add_jb'];
					}
				}
				
				if($NumDef < 1){
					if(!empty($this->input->post('diameter'))){
						if($diameter <= 100){
							$bw			= floatval(($weight >= '2200')?'100':(($weight < '2000')?'50':'0'));
							$jumRoov	= floatval(($weight >= '2200')?'52':(($weight < '2000')?'22':'0'));
						}
						if($diameter >= 125 AND $diameter <= 350){
							$bw			= floatval(($weight >= '2200')?'100':(($weight < '2000')?'100':'0'));
							$jumRoov	= floatval(($weight >= '2200')?'52':(($weight < '2000')?'52':'0'));
						}
						if($diameter > 350){
							$bw			= floatval(($weight >= '2200')?'160':(($weight < '2000')?'100':'0'));
							$jumRoov	= floatval(($weight >= '2200')?'54':(($weight < '2000')?'52':'0'));
						}

					}
					//diameter belum di set
					if(empty($this->input->post('diameter'))){
						$bw			= floatval(($weight >= '2200')?'160':(($weight < '2000')?'100':'0'));
						$jumRoov	= floatval(($weight >= '2200')?'54':(($weight < '2000')?'52':'0'));

					}
				}

				if($bw != 0){
					$thickness	= (($weight/1000)/ $bw * $jumRoov * (2 / 2.56)) + (($weight/1000)/ $bw * $jumRoov * (2 / 1.2) * $r_cont);
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

		$ArrJson		= array(
			'weight' 	=> $weight,
			'bw' 		=> $bw,
			'jumRoov' 	=> $jumRoov,
			'resin'		=> $resinX,
			'thickness'	=> $thickness,
			'layer'		=> $LayerR
		);

		// echo "<pre>";
		// print_r($ArrJson);
		// exit;
		echo json_encode($ArrJson);
	}

	public function getRoovingEdit(){
		$id_material 	= $this->input->post('id_material');
		$r_cont			= $this->input->post('resin');

		if(!empty($this->input->post('diameter'))){
			$diameter	= $this->input->post('diameter');
		}

		if(!empty($this->input->post('layer'))){
			$LayerR		= floatval($this->input->post('layer'));
		}
		$bw 			= floatval($this->input->post('bw'));
		$jumRoov		= floatval($this->input->post('jumlah'));
		// echo $resinutama;
		$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
		$restMicron		= $this->db->query($sqlMicron)->result_array();
		$NumMic			= $this->db->query($sqlMicron)->num_rows();

		// echo $sqlMicron."<br>";

		$weight			= 0;
		$thickness		= 0;
		if($NumMic != 0){
			$weight		=  floatval($restMicron[0]['nilai_standard']);
			if($weight != 0 OR $weight != null OR $weight != ''){
				$thickness	= (($weight/1000)/ $bw * $jumRoov * (2 / 2.56)) + (($weight/1000)/ $bw * $jumRoov * (2 / 1.2) * $r_cont);
			}
		}

		// echo $weight;
		$resinX = "";
		if($id_material == 'MTL-1903000'){
			$resinX = 'MTL-1903000';
			$LayerR	= 0;
		}

		$ArrJson		= array(
			'weight' 	=> $weight,
			'bw' 		=> $bw,
			'jumRoov' 	=> $jumRoov,
			'resin'		=> $resinX,
			'thickness'	=> $thickness,
			'layer'		=> $LayerR
		);

		echo json_encode($ArrJson);
	}

	public function getCategory(){
		$sqlSup		= "SELECT * FROM raw_categories WHERE `delete`='N' ORDER BY category ASC";
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
	
	public function getCategoryJoint(){
		$sqlSup		= "SELECT * FROM raw_categories WHERE `delete`='N' AND id_category IN ('TYP-0006','TYP-0003','TYP-0004') ORDER BY category ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Type</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_category']."'>".$valx['name2']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getMaterial(){
		$id_category	= $this->input->post("id_category");
		$sqlSup		= "SELECT * FROM raw_materials WHERE id_category='".$id_category."' AND `delete`='N' ORDER BY nm_material ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();
		// echo $sqlSup; exit;
		$option	= "<option value='0'>Select An Material</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_material']."'>".$valx['nm_material']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getTolerance(){
		$cust		= $this->input->post("cust");
		$sqlTol		= "SELECT * FROM customer WHERE (id_customer='".$cust."' OR id_customer='C100-1903000') AND `deleted`='N' ORDER BY id_customer ASC";
		$restTol	= $this->db->query($sqlTol)->result_array();
		// echo $sqlTol; exit;
		$option	= "";
		foreach($restTol AS $val => $valx){
			$seL = ($valx['id_customer'] == 'C100-1903000')?'selected':'';
			$option .= "<option value='".$valx['id_customer']."' ".$seL.">".strtoupper($valx['nm_customer'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getDiameter(){
		$id		= $this->input->post("id");

		$DiamaterPipe	= 0;
		$NamePipe		= "";

		if($id != 0){
			$sqlD		= "SELECT * FROM product WHERE id='".$id."' LIMIT 1";
			$restD	= $this->db->query($sqlD)->result_array();

			$DiamaterPipe	= $restD[0]['value_d'];
			$DiamaterPipe2	= $restD[0]['value_d2'];
			$NamePipe		= $restD[0]['nm_product'];
			$parent_product		= $restD[0]['parent_product'];

			if($DiamaterPipe <= 50){ $waste = 0.07;}
			if($DiamaterPipe >= 60 AND $DiamaterPipe <= 100){ $waste = 0.05;}
			if($DiamaterPipe >= 125 AND $DiamaterPipe <= 600){ $waste = 0.03;}
			if($DiamaterPipe > 600){ $waste = 0.02;}
		}

		$ArrJson	= array(
			'pipeD' 	=> $DiamaterPipe,
			'pipeD2' 	=> $DiamaterPipe2,
			'pipeN'		=> $NamePipe,
			'product'	=> $parent_product,
			'wasted'	=> $waste
		);
		echo json_encode($ArrJson);
	}

	public function getDiameterNoWaste(){
		$id		= $this->input->post("id");

		$DiamaterPipe	= 0;
		$NamePipe		= "";

		if($id != 0){
			$sqlD		= "SELECT * FROM product WHERE id='".$id."' LIMIT 1";
			$restD	= $this->db->query($sqlD)->result_array();

			$DiamaterPipe	= $restD[0]['value_d'];
			$NamePipe		= $restD[0]['nm_product'];

			// if($DiamaterPipe <= 50){ $waste = 0.07;}
			// if($DiamaterPipe >= 60 AND $DiamaterPipe <= 100){ $waste = 0.05;}
			// if($DiamaterPipe >= 125 AND $DiamaterPipe <= 600){ $waste = 0.03;}
			// if($DiamaterPipe > 600){ $waste = 0.02;}
		}

		$ArrJson	= array(
			'pipeD' 	=> $DiamaterPipe,
			'pipeN'		=> $NamePipe,
			// 'wasted'	=> $waste
		);
		echo json_encode($ArrJson);
	}

	public function modalSetResin(){
		$this->load->view('Calculation/modalSetResin');
	}

	public function setResin(){
		$dataSet	= $this->input->post('SetResinC');
		$data_session			= $this->session->userdata;
		// echo "<pre>";
		// print_r($dataSet);

		$ArrUpdateSet	= array();
		foreach($dataSet AS $val => $valx){
			$ArrUpdateSet[$val]['id']				= $valx['id'];
			$ArrUpdateSet[$val]['start']			= $valx['start'];
			$ArrUpdateSet[$val]['end']				= $valx['end'];
			$ArrUpdateSet[$val]['value1']			= $valx['value1'];
			$ArrUpdateSet[$val]['value2']			= $valx['value2'];
			$ArrUpdateSet[$val]['modified_by']		= $data_session['ORI_User']['username'];
			$ArrUpdateSet[$val]['modified_date']	= date('Y-m-d H:i:s');
		}

		$this->db->trans_start();
			$this->db->update_batch('help_resin_containing', $ArrUpdateSet, 'id');
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update set resin containing data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update set resin containing data success. Thanks ...',
				'status'	=> 1
			);
			history('Update Set Resin Containing ');
		}

		// print_r($Arr_Data); exit;
		echo json_encode($Arr_Data);

	}

	public function getMirrorMat(){

		$List_PlasticFirm	= "SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC";
		$restTol	= $this->db->query($List_PlasticFirm)->result_array();
		// echo $sqlTol; exit;
		$option	= "<option value=''>Select An Mirror Glass</option>";
		foreach($restTol AS $val => $valx){
			$option .= "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
		}
		$option .= "<option value='MTL-1903000'>NONE MATERIAL</option>";

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getPlasticMat(){

		$List_PlasticFirm	= "SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND (nm_material LIKE 'PE%' OR nm_material LIKE 'POLYESTER%') ORDER BY nm_material ASC";
		$restTol	= $this->db->query($List_PlasticFirm)->result_array();
		// echo $sqlTol; exit;
		$option	= "<option value=''>Select An Plastic Film</option>";
		foreach($restTol AS $val => $valx){
			$option .= "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
		}
		$option .= "<option value='MTL-1903000'>NONE MATERIAL</option>";

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
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

	public function hapus(){
		$id_produk 		= $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();
		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$id_produk."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$id_produk."' ")->num_rows();
		if($qHeaderHistNum > 0){
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
				$ArrBqHeaderHist[$val2HistA]['deleted_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_produk."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_produk."' ")->num_rows();
		if($qDetailHistNum > 0){
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
				$ArrBqDetailHist[$val2Hist]['deleted_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_produk."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_produk."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
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
				$ArrBqDetailPlusHist[$val3Hist]['deleted_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['deleted_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_produk."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_produk."' ")->num_rows();
		if($qDetailAddNumHist > 0){
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
				$ArrBqDetailAddHist[$val4Hist]['deleted_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_produk."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_produk."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['deleted_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Time To Hist
		$qDetailTimeHist	= $this->db->query("SELECT * FROM component_time WHERE id_product='".$id_produk."' ")->result_array();
		$qDetailTimeHistNum	= $this->db->query("SELECT * FROM component_time WHERE id_product='".$id_produk."' ")->num_rows();
		if($qDetailTimeHistNum > 0){
			foreach($qDetailTimeHist AS $val6Hist => $valx5Hist){
				$ArrBqTimeHist[$val6Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqTimeHist[$val6Hist]['process']		= $valx5Hist['detail_name'];
				$ArrBqTimeHist[$val6Hist]['sub_process']	= $valx5Hist['total'];
				$ArrBqTimeHist[$val6Hist]['time_process']	= $valx5Hist['min'];
				$ArrBqTimeHist[$val6Hist]['man_power']	= $valx5Hist['max'];
				$ArrBqTimeHist[$val6Hist]['man_hours']	= $valx5Hist['hasil'];
				$ArrBqTimeHist[$val6Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqTimeHist[$val6Hist]['deleted_date']	= date('Y-m-d H:i:s');
			}
		}

		// print_r($ArrBqHeaderHist);
		// print_r($ArrBqDetailHist);
		// print_r($ArrBqDetailPlusHist);
		// print_r($ArrBqDetailAddHist);
		// print_r($ArrBqFooterHist);
		// print_r($ArrBqTimeHist);
		// exit;
		$this->db->trans_start();
		if($qHeaderHistNum > 0){
			$this->db->insert_batch('hist_component_header', $ArrBqHeaderHist);
		}
		if($qDetailHistNum > 0){
			$this->db->insert_batch('hist_component_detail', $ArrBqDetailHist);
		}
		if($qDetailPlusHistNum > 0){
			$this->db->insert_batch('hist_component_detail_plus', $ArrBqDetailPlusHist);
		}
		if($qDetailAddNumHist > 0){
			$this->db->insert_batch('hist_component_detail_add', $ArrBqDetailAddHist);
		}
		if($qDetailFooterHistNum > 0){
			$this->db->insert_batch('hist_component_footer', $ArrBqFooterHist);
		}
		if($qDetailTimeHistNum > 0){
			$this->db->insert_batch('hist_component_time', $ArrBqTimeHist);
		}
		$this->db->delete('component_header', array('id_product' => $id_produk));
		$this->db->delete('component_detail', array('id_product' => $id_produk));
		$this->db->delete('component_detail_add', array('id_product' => $id_produk));
		$this->db->delete('component_detail_plus', array('id_product' => $id_produk));
		$this->db->delete('component_footer', array('id_product' => $id_produk));
		$this->db->delete('component_default', array('id_product' => $id_produk));
		$this->db->delete('component_time', array('id_product' => $id_produk));
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete product data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete product data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Product with ID : '.$id_produk);
		}
		echo json_encode($Arr_Data);
	}

	public function getDiameterCCR(){
		$id		= $this->input->post("id");

		$DiamaterPipe	= 0;
		$DiamaterPipe2	= 0;
		$NamePipe		= "";

		if($id != 0){
			$sqlD		= "SELECT * FROM product WHERE id='".$id."' LIMIT 1";
			$restD	= $this->db->query($sqlD)->result_array();

			$DiamaterPipe	= $restD[0]['value_d'];
			$DiamaterPipe2	= $restD[0]['value_d2'];
			$NamePipe		= $restD[0]['nm_product'];
		}

		$ArrJson	= array(
			'pipeD' => $DiamaterPipe,
			'pipeD2' => $DiamaterPipe2,
			'pipeN'	=> $NamePipe
		);
		echo json_encode($ArrJson);
	}

	public function VacumKomp($pressure, $liner, $diameter){
		//PRESSURE PN6 & PN8
		if($pressure == 6 OR $pressure == 8 ){
			if($liner == 0.5){
				if($diameter <= 200){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter == 250 OR $diameter == 300){
					$Vacum	= 'HALF VACCUM';
				}
				if($diameter >= 350){
					$Vacum	= 'NON VACCUM';
				}
			}
			if($liner == 1.3){
				if($diameter <= 150){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter == 200){
					$Vacum	= 'HALF VACCUM';
				}
				if($diameter >= 250){
					$Vacum	= 'NON VACCUM';
				}
			}
			if($liner == 2.5){
				if($diameter <= 100){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter == 125 OR $diameter == 150){
					$Vacum	= 'HALF VACCUM';
				}
				if($diameter >= 200){
					$Vacum	= 'NON VACCUM';
				}
			}
		}
		//PRESSURE PN10
		if($pressure == 10 ){
			if($liner == 0.5){
				if($diameter <= 200){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter == 250 OR $diameter == 300){
					$Vacum	= 'HALF VACCUM';
				}
				if($diameter >= 350){
					$Vacum	= 'NON VACCUM';
				}
			}
			if($liner == 1.3){
				if($diameter <= 150){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter == 200){
					$Vacum	= 'HALF VACCUM';
				}
				if($diameter >= 250){
					$Vacum	= 'NON VACCUM';
				}
			}
			if($liner == 2.5){
				if($diameter <= 100){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter >= 125){
					$Vacum	= 'HALF VACCUM';
				}
			}
		}
		//PRESSURE PN12
		if($pressure == 12 ){
			if($liner == 0.5){
				if($diameter <= 200){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter >= 250){
					$Vacum	= 'HALF VACCUM';
				}
			}
			if($liner == 1.3){
				if($diameter <= 150){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter >= 200){
					$Vacum	= 'HALF VACCUM';
				}
			}
			if($liner == 2.5){
				if($diameter <= 100){
					$Vacum	= 'FULL VACCUM';
				}
				if($diameter >= 125){
					$Vacum	= 'HALF VACCUM';
				}
			}
		}
		//PRESSURE PN14 & PN16
		if($pressure == 12 OR $pressure == 16){
			$Vacum	= 'FULL VACCUM';
		}

		return $Vacum;
	}

	public function StiffnessKomp($pressure, $liner, $diameter){
		//PRESSURE PN6 & PN8
		if($pressure == 6 OR $pressure == 8 ){
			if($liner == 0.5){
				if($diameter <= 200){
					$Stiffness	= 'SN10000';
				}
				if($diameter == 250){
					$Stiffness	= 'SN5000';
				}
				if($diameter == 300){
					$Stiffness	= 'SN2500';
				}
				if($diameter >= 350){
					$Stiffness	= 'SN1250';
				}
			}
			if($liner == 1.3){
				if($diameter <= 150){
					$Stiffness	= 'SN10000';
				}
				if($diameter == 200){
					$Stiffness	= 'SN5000';
				}
				if($diameter == 250){
					$Stiffness	= 'SN2500';
				}
				if($diameter >= 300){
					$Stiffness	= 'SN1250';
				}
			}
			if($liner == 2.5){
				if($diameter <= 80){
					$Stiffness	= 'SN10000';
				}
				if($diameter == 100){
					$Stiffness	= 'SN5000';
				}
				if($diameter == 125 OR $diameter == 150){
					$Stiffness	= 'SN2500';
				}
				if($diameter >= 200){
					$Stiffness	= 'SN1250';
				}
			}

		}
		//PRESSURE PN10 & PN12
		if($pressure == 10 OR $pressure == 12){
			if($liner == 0.5){
				if($diameter <= 200){
					$Stiffness	= 'SN10000';
				}
				if($diameter == 250){
					$Stiffness	= 'SN5000';
				}
				if($diameter >= 300){
					$Stiffness	= 'SN2500';
				}
			}
			if($liner == 1.3){
				if($diameter <= 150){
					$Stiffness	= 'SN10000';
				}
				if($diameter == 200){
					$Stiffness	= 'SN5000';
				}
				if($diameter >= 250){
					$Stiffness	= 'SN2500';
				}
			}
			if($liner == 2.5){
				if($diameter <= 80){
					$Stiffness	= 'SN10000';
				}
				if($diameter == 100){
					$Stiffness	= 'SN5000';
				}
				if($diameter >= 125){
					$Stiffness	= 'SN2500';
				}
			}
		}
		//PRESSURE PN14
		if($pressure == 14){
			if($liner == 0.5){
				if($diameter <= 200){
					$Stiffness	= 'SN10000';
				}
				if($diameter >= 250){
					$Stiffness	= 'SN5000';
				}
			}
			if($liner == 1.3){
				if($diameter <= 150){
					$Stiffness	= 'SN10000';
				}
				if($diameter >= 200){
					$Stiffness	= 'SN5000';
				}
			}
			if($liner == 2.5){
				if($diameter <= 80){
					$Stiffness	= 'SN10000';
				}
				if($diameter >= 100){
					$Stiffness	= 'SN5000';
				}
			}
		}
		//PRESSURE PN16
		if($pressure == 16){
			$Stiffness	= 'SN10000';
		}

		return $Stiffness;
	}

	public function FluidaKomp($resin_sistem, $liner){
		//RESIN SISTEM ISO THALIC
		if($resin_sistem == 'ISO THALIC'){
			if($liner == 0.5){
				$Fluida	= 'LOW CORROSION';
			}
			else{
				$Fluida	= 'MIDDLE CORROSION';
			}
		}
		//RESIN SISTEM VINYLESTER
		if($resin_sistem == 'VINYLESTER'){
			if($liner == 1.3){
				$Fluida	= 'MIDDLE CORROSION';
			}
			else{
				$Fluida	= 'HIGH CORROSION';
			}
		}

		return $Fluida;
	}

	public function AppKomp($stiffness){
		if($stiffness == 'SN1250'){
			$App	= 'ABOVE GROUND';
		}
		else{
			$App	= 'ABOVE GROUND';
		}

		return $App;
	}
	//JSON Master
	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
			$requestData['series'],
			$requestData['group'],
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
				$kode1 = substr($row['id_product'], 0,3);
				$kode2 = substr($row['id_product'], 8,6);
			$nestedData[]	= "<div align='left'>".$kode1.$row['series'].$kode2."</div>";
			// $nestedData[]	= "<div align='center'>".$row['series']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_product']." ".$detail."</div>";
			$nestedData[]	= "<div align='center'>".$row['stiffness']."</div>";
			$nestedData[]	= "<div align='center'>".$row['criminal_barier']."</div>";
			$nestedData[]	= "<div align='center'>".$row['vacum_rate']."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['rev']."</span></div>";
			$nestedData[]	= "<div align='center'>
									<button type='button' id='MatDetail' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-success' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>

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

	public function queryDataJSON($series, $group, $komponen, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		// echo $series."<br>";
		// echo $group."<br>";
		// echo $komponen."<br>";
		$where_series = "";
		if(!empty($series)){
			$where_series = " AND a.series = '".$series."' ";
		}

		$where_group = "";
		if(!empty($group)){
			$where_group = " AND a.parent_product = '".$group."' ";
		}

		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND a.parent_product = '".$komponen."' ";
		}

		$sql = "
			SELECT
				a.*, b.nm_customer
			FROM
				component_header a
				LEFT JOIN customer b ON b.id_customer=a.standart_by
			WHERE 1=1
				".$where_group."
				".$where_series."
				".$where_komponen."
				AND a.status='APPROVED' AND a.deleted ='N' AND a.cust IS NULL AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_product LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'series',
			3 => 'nm_product',
			4 => 'standart_toleransi',
			5 => 'aplikasi_product',
			6 => 'created_by',
			7 => 'rev'
		);

		$sql .= " ORDER BY a.sts_price DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	//JSON Master
	
	//flangemould
	public function flangemould(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail2_neck1	= $data['ListDetail2_neck1'];
			$ListDetail2_neck2	= $data['ListDetail2_neck2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus2_neck1	= $data['ListDetailPlus2_neck1'];
			$ListDetailPlus2_neck2	= $data['ListDetailPlus2_neck2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			// print_r($ListDetail2_neck2);

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_strukture_neck1	= $data['numberMax_strukture_neck1'];
			$numberMax_strukture_neck2	= $data['numberMax_strukture_neck2'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_strukture_neck1 != 0){
				$ListDetailAdd2_neck1	= $data['ListDetailAdd_Strukture_neck1'];
			}
			if($numberMax_strukture_neck2 != 0){
				$ListDetailAdd2_neck2	= $data['ListDetailAdd_Strukture_neck2'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['top_diameter'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;

			$kode_product	= "OG-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{

				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'nm_product'			=> $data['top_type'],
					'parent_product'		=> 'flange mould',
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],

					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'diameter'				=> str_replace(',', '', $data['top_diameter']),
					'panjang'				=> "",

					'panjang_neck_1'		=> $data['panjang_neck_1'],
					'panjang_neck_2'		=> $data['panjang_neck_2'],
					'design_neck_1'			=> $data['design_neck_1'],
					'design_neck_2'			=> $data['design_neck_2'],
					'est_neck_1'			=> $data['est_neck_1'],
					'est_neck_2'			=> $data['est_neck_2'],
					'area_neck_1'			=> $data['area_neck_1'],
					'area_neck_2'			=> $data['area_neck_2'],
					'flange_od'				=> $data['flange_od'],
					'flange_bcd'			=> $data['flange_bcd'],
					'flange_n'				=> $data['flange_n'],
					'flange_oh'				=> $data['flange_oh'],

					'design'				=> $data['top_tebal_design'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'area'					=> $data['area'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['top_diameter']."' AND product_parent = 'flange mould' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,

					
					'str_n1_resin_csm_a'=> $getDefault[0]->str_n1_resin_csm_a,
					'str_n1_resin_csm_b'=> $getDefault[0]->str_n1_resin_csm_b,
					'str_n1_resin_csm'=> $getDefault[0]->str_n1_resin_csm,
					'str_n1_resin_csm_add_a'=> $getDefault[0]->str_n1_resin_csm_add_a,
					'str_n1_resin_csm_add_b'=> $getDefault[0]->str_n1_resin_csm_add_b,
					'str_n1_resin_csm_add'=> $getDefault[0]->str_n1_resin_csm_add,
					'str_n1_resin_wr_a'=> $getDefault[0]->str_n1_resin_wr_a,
					'str_n1_resin_wr_b'=> $getDefault[0]->str_n1_resin_wr_b,
					'str_n1_resin_wr'=> $getDefault[0]->str_n1_resin_wr,
					'str_n1_resin_wr_add_a'=> $getDefault[0]->str_n1_resin_wr_add_a,
					'str_n1_resin_wr_add_b'=> $getDefault[0]->str_n1_resin_wr_add_b,
					'str_n1_resin_wr_add'=> $getDefault[0]->str_n1_resin_wr_add,
					'str_n1_resin_rv_a'=> $getDefault[0]->str_n1_resin_rv_a,
					'str_n1_resin_rv_b'=> $getDefault[0]->str_n1_resin_rv_b,
					'str_n1_resin_rv'=> $getDefault[0]->str_n1_resin_rv,
					'str_n1_resin_rv_add_a'=> $getDefault[0]->str_n1_resin_rv_add_a,
					'str_n1_resin_rv_add_b'=> $getDefault[0]->str_n1_resin_rv_add_b,
					'str_n1_resin_rv_add'=> $getDefault[0]->str_n1_resin_rv_add,
					'str_n1_faktor_csm'=> $getDefault[0]->str_n1_faktor_csm,
					'str_n1_faktor_csm_add'=> $getDefault[0]->str_n1_faktor_csm_add,
					'str_n1_faktor_wr'=> $getDefault[0]->str_n1_faktor_wr,
					'str_n1_faktor_wr_add'=> $getDefault[0]->str_n1_faktor_wr_add,
					'str_n1_faktor_rv'=> $getDefault[0]->str_n1_faktor_rv,
					'str_n1_faktor_rv_bw'=> $getDefault[0]->str_n1_faktor_rv_bw,
					'str_n1_faktor_rv_jb'=> $getDefault[0]->str_n1_faktor_rv_jb,
					'str_n1_faktor_rv_add'=> $getDefault[0]->str_n1_faktor_rv_add,
					'str_n1_faktor_rv_add_bw'=> $getDefault[0]->str_n1_faktor_rv_add_bw,
					'str_n1_faktor_rv_add_jb'=> $getDefault[0]->str_n1_faktor_rv_add_jb,
					'str_n1_resin'=> $getDefault[0]->str_n1_resin,
					'str_n1_resin_thickness'=> $getDefault[0]->str_n1_resin_thickness,
					'str_n2_resin_csm_a'=> $getDefault[0]->str_n2_resin_csm_a,
					'str_n2_resin_csm_b'=> $getDefault[0]->str_n2_resin_csm_b,
					'str_n2_resin_csm'=> $getDefault[0]->str_n2_resin_csm,
					'str_n2_resin_csm_add_a'=> $getDefault[0]->str_n2_resin_csm_add_a,
					'str_n2_resin_csm_add_b'=> $getDefault[0]->str_n2_resin_csm_add_b,
					'str_n2_resin_csm_add'=> $getDefault[0]->str_n2_resin_csm_add,
					'str_n2_resin_wr_a'=> $getDefault[0]->str_n2_resin_wr_a,
					'str_n2_resin_wr_b'=> $getDefault[0]->str_n2_resin_wr_b,
					'str_n2_resin_wr'=> $getDefault[0]->str_n2_resin_wr,
					'str_n2_resin_wr_add_a'=> $getDefault[0]->str_n2_resin_wr_add_a,
					'str_n2_resin_wr_add_b'=> $getDefault[0]->str_n2_resin_wr_add_b,
					'str_n2_resin_wr_add'=> $getDefault[0]->str_n2_resin_wr_add,
					'str_n2_faktor_csm'=> $getDefault[0]->str_n2_faktor_csm,
					'str_n2_faktor_csm_add'=> $getDefault[0]->str_n2_faktor_csm_add,
					'str_n2_faktor_wr'=> $getDefault[0]->str_n2_faktor_wr,
					'str_n2_faktor_wr_add'=> $getDefault[0]->str_n2_faktor_wr_add,
					'str_n2_resin'=> $getDefault[0]->str_n2_resin,
					'str_n2_resin_thickness'=> $getDefault[0]->str_n2_resin_thickness,


					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetail1[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetail2[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail2
				$ArrDetail2_neck1	= array();
				foreach($ListDetail2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetail2_neck1[$val]['acuhan'] 		= $data['acuhan_2_neck1'];
					$ArrDetail2_neck1[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;;
					$ArrDetail2_neck1[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;;
					$ArrDetail2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck1[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;;
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2_neck1);

				//Detail2
				$ArrDetail2_neck2	= array();
				foreach($ListDetail2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetail2_neck2[$val]['acuhan'] 		= $data['acuhan_2_neck2'];
					$ArrDetail2_neck2[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetail2_neck2[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetail2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck2[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck2[$val]['last_cost'] 		= $valx['last_cost'];


					// echo "SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1 <br>";
				}
				// print_r($ArrDetail2_neck2);
				// exit;
				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetail13[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetailPlus1[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetailPlus2[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus2_neck1	= array();
				foreach($ListDetailPlus2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetailPlus2_neck1[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetailPlus2_neck1[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetailPlus2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck1[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck1[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck1);

				$ArrDetailPlus2_neck2	= array();
				foreach($ListDetailPlus2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetailPlus2_neck2[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetailPlus2_neck2[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetailPlus2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck2[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck2[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetailPlus3[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_category'] 	= (!empty($dataMaterial[0]['id_category']))?$dataMaterial[0]['id_category']:null;
					$ArrDetailPlus4[$val]['nm_category'] 	= (!empty($dataMaterial[0]['nm_category']))?$dataMaterial[0]['nm_category']:null;
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= (!empty($dataMaterial[0]['nm_material']))?$dataMaterial[0]['nm_material']:null;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter2_neck1	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck1'],
						'total'			=> $data['tot_lin_thickness2_neck1'],
						'min'			=> $data['mix_lin_thickness2_neck1'],
						'max'			=> $data['max_lin_thickness2_neck1'],
						'hasil'			=> $data['hasil_linier_thickness2_neck1']
				);

				$ArrFooter2_neck2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck2'],
						'total'			=> $data['tot_lin_thickness2_neck2'],
						'min'			=> $data['mix_lin_thickness2_neck2'],
						'max'			=> $data['max_lin_thickness2_neck2'],
						'hasil'			=> $data['hasil_linier_thickness2_neck2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck1 != 0){
					$ArrDataAdd2_neck1 = array();
					foreach($ListDetailAdd2_neck1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
						$ArrDataAdd2_neck1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck1[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck2 != 0){
					$ArrDataAdd2_neck2 = array();
					foreach($ListDetailAdd2_neck2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
						$ArrDataAdd2_neck2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail2_neck1);
				// print_r($ArrDetail2_neck2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus2_neck1);
				// print_r($ArrDetailPlus2_neck2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter2_neck1);
				// print_r($ArrFooter2_neck2);
				// print_r($ArrDefault);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_strukture_neck1 != 0){
					// print_r($ArrDataAdd2_neck1);
				// }
				// if($numberMax_strukture_neck2 != 0){
					// print_r($ArrDataAdd2_neck2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck1);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter2_neck1);
					$this->db->insert('component_footer', $ArrFooter2_neck2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_strukture_neck1 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck1);
					}
					if($numberMax_strukture_neck2 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation flange mould code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='flange mould' AND deleted='N'")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' OR id_material='MTL-1903173' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Flange Mould',
				'action'		=> 'flangemould',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/flangemould', $data);
		}
	}

	public function colar(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail2_neck1	= $data['ListDetail2_neck1'];
			$ListDetail2_neck2	= $data['ListDetail2_neck2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus2_neck1	= $data['ListDetailPlus2_neck1'];
			$ListDetailPlus2_neck2	= $data['ListDetailPlus2_neck2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			// print_r($ListDetail2_neck2);

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_strukture_neck1	= $data['numberMax_strukture_neck1'];
			$numberMax_strukture_neck2	= $data['numberMax_strukture_neck2'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_strukture_neck1 != 0){
				$ListDetailAdd2_neck1	= $data['ListDetailAdd_Strukture_neck1'];
			}
			if($numberMax_strukture_neck2 != 0){
				$ListDetailAdd2_neck2	= $data['ListDetailAdd_Strukture_neck2'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['top_diameter'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;

			$kode_product	= "OO-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{

				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'nm_product'			=> $data['top_type'],
					'parent_product'		=> 'colar',
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],

					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'diameter'				=> str_replace(',', '', $data['top_diameter']),
					'panjang'				=> "",

					'panjang_neck_1'		=> $data['panjang_neck_1'],
					'panjang_neck_2'		=> $data['panjang_neck_2'],
					'design_neck_1'			=> $data['design_neck_1'],
					'design_neck_2'			=> $data['design_neck_2'],
					'est_neck_1'			=> $data['est_neck_1'],
					'est_neck_2'			=> $data['est_neck_2'],
					'area_neck_1'			=> $data['area_neck_1'],
					'area_neck_2'			=> $data['area_neck_2'],
					'flange_od'				=> $data['flange_od'],

					'design'				=> $data['top_tebal_design'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'area'					=> $data['area'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['top_diameter']."' AND product_parent = 'colar' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,

					'str_n1_resin_csm_a'=> $getDefault[0]->str_n1_resin_csm_a,
					'str_n1_resin_csm_b'=> $getDefault[0]->str_n1_resin_csm_b,
					'str_n1_resin_csm'=> $getDefault[0]->str_n1_resin_csm,
					'str_n1_resin_csm_add_a'=> $getDefault[0]->str_n1_resin_csm_add_a,
					'str_n1_resin_csm_add_b'=> $getDefault[0]->str_n1_resin_csm_add_b,
					'str_n1_resin_csm_add'=> $getDefault[0]->str_n1_resin_csm_add,
					'str_n1_resin_wr_a'=> $getDefault[0]->str_n1_resin_wr_a,
					'str_n1_resin_wr_b'=> $getDefault[0]->str_n1_resin_wr_b,
					'str_n1_resin_wr'=> $getDefault[0]->str_n1_resin_wr,
					'str_n1_resin_wr_add_a'=> $getDefault[0]->str_n1_resin_wr_add_a,
					'str_n1_resin_wr_add_b'=> $getDefault[0]->str_n1_resin_wr_add_b,
					'str_n1_resin_wr_add'=> $getDefault[0]->str_n1_resin_wr_add,
					'str_n1_resin_rv_a'=> $getDefault[0]->str_n1_resin_rv_a,
					'str_n1_resin_rv_b'=> $getDefault[0]->str_n1_resin_rv_b,
					'str_n1_resin_rv'=> $getDefault[0]->str_n1_resin_rv,
					'str_n1_resin_rv_add_a'=> $getDefault[0]->str_n1_resin_rv_add_a,
					'str_n1_resin_rv_add_b'=> $getDefault[0]->str_n1_resin_rv_add_b,
					'str_n1_resin_rv_add'=> $getDefault[0]->str_n1_resin_rv_add,
					'str_n1_faktor_csm'=> $getDefault[0]->str_n1_faktor_csm,
					'str_n1_faktor_csm_add'=> $getDefault[0]->str_n1_faktor_csm_add,
					'str_n1_faktor_wr'=> $getDefault[0]->str_n1_faktor_wr,
					'str_n1_faktor_wr_add'=> $getDefault[0]->str_n1_faktor_wr_add,
					'str_n1_faktor_rv'=> $getDefault[0]->str_n1_faktor_rv,
					'str_n1_faktor_rv_bw'=> $getDefault[0]->str_n1_faktor_rv_bw,
					'str_n1_faktor_rv_jb'=> $getDefault[0]->str_n1_faktor_rv_jb,
					'str_n1_faktor_rv_add'=> $getDefault[0]->str_n1_faktor_rv_add,
					'str_n1_faktor_rv_add_bw'=> $getDefault[0]->str_n1_faktor_rv_add_bw,
					'str_n1_faktor_rv_add_jb'=> $getDefault[0]->str_n1_faktor_rv_add_jb,
					'str_n1_resin'=> $getDefault[0]->str_n1_resin,
					'str_n1_resin_thickness'=> $getDefault[0]->str_n1_resin_thickness,
					'str_n2_resin_csm_a'=> $getDefault[0]->str_n2_resin_csm_a,
					'str_n2_resin_csm_b'=> $getDefault[0]->str_n2_resin_csm_b,
					'str_n2_resin_csm'=> $getDefault[0]->str_n2_resin_csm,
					'str_n2_resin_csm_add_a'=> $getDefault[0]->str_n2_resin_csm_add_a,
					'str_n2_resin_csm_add_b'=> $getDefault[0]->str_n2_resin_csm_add_b,
					'str_n2_resin_csm_add'=> $getDefault[0]->str_n2_resin_csm_add,
					'str_n2_resin_wr_a'=> $getDefault[0]->str_n2_resin_wr_a,
					'str_n2_resin_wr_b'=> $getDefault[0]->str_n2_resin_wr_b,
					'str_n2_resin_wr'=> $getDefault[0]->str_n2_resin_wr,
					'str_n2_resin_wr_add_a'=> $getDefault[0]->str_n2_resin_wr_add_a,
					'str_n2_resin_wr_add_b'=> $getDefault[0]->str_n2_resin_wr_add_b,
					'str_n2_resin_wr_add'=> $getDefault[0]->str_n2_resin_wr_add,
					'str_n2_faktor_csm'=> $getDefault[0]->str_n2_faktor_csm,
					'str_n2_faktor_csm_add'=> $getDefault[0]->str_n2_faktor_csm_add,
					'str_n2_faktor_wr'=> $getDefault[0]->str_n2_faktor_wr,
					'str_n2_faktor_wr_add'=> $getDefault[0]->str_n2_faktor_wr_add,
					'str_n2_resin'=> $getDefault[0]->str_n2_resin,
					'str_n2_resin_thickness'=> $getDefault[0]->str_n2_resin_thickness,

					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail2
				$ArrDetail2_neck1	= array();
				foreach($ListDetail2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetail2_neck1[$val]['acuhan'] 		= $data['acuhan_2_neck1'];
					$ArrDetail2_neck1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2_neck1);

				//Detail2
				$ArrDetail2_neck2	= array();
				foreach($ListDetail2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetail2_neck2[$val]['acuhan'] 		= $data['acuhan_2_neck2'];
					$ArrDetail2_neck2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck2[$val]['last_cost'] 		= $valx['last_cost'];


					// echo "SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1 <br>";
				}
				// print_r($ArrDetail2_neck2);
				// exit;
				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus2_neck1	= array();
				foreach($ListDetailPlus2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetailPlus2_neck1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck1[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck1);

				$ArrDetailPlus2_neck2	= array();
				foreach($ListDetailPlus2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetailPlus2_neck2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck2[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter2_neck1	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck1'],
						'total'			=> $data['tot_lin_thickness2_neck1'],
						'min'			=> $data['mix_lin_thickness2_neck1'],
						'max'			=> $data['max_lin_thickness2_neck1'],
						'hasil'			=> $data['hasil_linier_thickness2_neck1']
				);

				$ArrFooter2_neck2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck2'],
						'total'			=> $data['tot_lin_thickness2_neck2'],
						'min'			=> $data['mix_lin_thickness2_neck2'],
						'max'			=> $data['max_lin_thickness2_neck2'],
						'hasil'			=> $data['hasil_linier_thickness2_neck2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck1 != 0){
					$ArrDataAdd2_neck1 = array();
					foreach($ListDetailAdd2_neck1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
						$ArrDataAdd2_neck1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck1[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck2 != 0){
					$ArrDataAdd2_neck2 = array();
					foreach($ListDetailAdd2_neck2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
						$ArrDataAdd2_neck2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail2_neck1);
				// print_r($ArrDetail2_neck2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus2_neck1);
				// print_r($ArrDetailPlus2_neck2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter2_neck1);
				// print_r($ArrFooter2_neck2);
				// print_r($ArrDefault);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_strukture_neck1 != 0){
					// print_r($ArrDataAdd2_neck1);
				// }
				// if($numberMax_strukture_neck2 != 0){
					// print_r($ArrDataAdd2_neck2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck1);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter2_neck1);
					$this->db->insert('component_footer', $ArrFooter2_neck2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_strukture_neck1 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck1);
					}
					if($numberMax_strukture_neck2 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation colar code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='colar' AND deleted='N'")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' OR id_material='MTL-1903173' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Colar Mould',
				'action'		=> 'colar',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/colar', $data);
		}
	}

	public function colar_slongsong(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail2_neck1	= $data['ListDetail2_neck1'];
			$ListDetail2_neck2	= $data['ListDetail2_neck2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus2_neck1	= $data['ListDetailPlus2_neck1'];
			$ListDetailPlus2_neck2	= $data['ListDetailPlus2_neck2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			// print_r($ListDetail2_neck2);

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_strukture_neck1	= $data['numberMax_strukture_neck1'];
			$numberMax_strukture_neck2	= $data['numberMax_strukture_neck2'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_strukture_neck1 != 0){
				$ListDetailAdd2_neck1	= $data['ListDetailAdd_Strukture_neck1'];
			}
			if($numberMax_strukture_neck2 != 0){
				$ListDetailAdd2_neck2	= $data['ListDetailAdd_Strukture_neck2'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['top_diameter'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;

			$kode_product	= "OU-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{

				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'nm_product'			=> $data['top_type'],
					'parent_product'		=> 'colar slongsong',
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],

					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'diameter'				=> str_replace(',', '', $data['top_diameter']),
					'panjang'				=> "",

					'panjang_neck_1'		=> $data['panjang_neck_1'],
					'panjang_neck_2'		=> $data['panjang_neck_2'],
					'design_neck_1'			=> $data['design_neck_1'],
					'design_neck_2'			=> $data['design_neck_2'],
					'est_neck_1'			=> $data['est_neck_1'],
					'est_neck_2'			=> $data['est_neck_2'],
					'area_neck_1'			=> $data['area_neck_1'],
					'area_neck_2'			=> $data['area_neck_2'],
					'flange_od'				=> $data['flange_od'],

					'design'				=> $data['top_tebal_design'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'area'					=> $data['area'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['top_diameter']."' AND product_parent = 'colar slongsong' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,

					'str_n1_resin_csm_a'=> $getDefault[0]->str_n1_resin_csm_a,
					'str_n1_resin_csm_b'=> $getDefault[0]->str_n1_resin_csm_b,
					'str_n1_resin_csm'=> $getDefault[0]->str_n1_resin_csm,
					'str_n1_resin_csm_add_a'=> $getDefault[0]->str_n1_resin_csm_add_a,
					'str_n1_resin_csm_add_b'=> $getDefault[0]->str_n1_resin_csm_add_b,
					'str_n1_resin_csm_add'=> $getDefault[0]->str_n1_resin_csm_add,
					'str_n1_resin_wr_a'=> $getDefault[0]->str_n1_resin_wr_a,
					'str_n1_resin_wr_b'=> $getDefault[0]->str_n1_resin_wr_b,
					'str_n1_resin_wr'=> $getDefault[0]->str_n1_resin_wr,
					'str_n1_resin_wr_add_a'=> $getDefault[0]->str_n1_resin_wr_add_a,
					'str_n1_resin_wr_add_b'=> $getDefault[0]->str_n1_resin_wr_add_b,
					'str_n1_resin_wr_add'=> $getDefault[0]->str_n1_resin_wr_add,
					'str_n1_resin_rv_a'=> $getDefault[0]->str_n1_resin_rv_a,
					'str_n1_resin_rv_b'=> $getDefault[0]->str_n1_resin_rv_b,
					'str_n1_resin_rv'=> $getDefault[0]->str_n1_resin_rv,
					'str_n1_resin_rv_add_a'=> $getDefault[0]->str_n1_resin_rv_add_a,
					'str_n1_resin_rv_add_b'=> $getDefault[0]->str_n1_resin_rv_add_b,
					'str_n1_resin_rv_add'=> $getDefault[0]->str_n1_resin_rv_add,
					'str_n1_faktor_csm'=> $getDefault[0]->str_n1_faktor_csm,
					'str_n1_faktor_csm_add'=> $getDefault[0]->str_n1_faktor_csm_add,
					'str_n1_faktor_wr'=> $getDefault[0]->str_n1_faktor_wr,
					'str_n1_faktor_wr_add'=> $getDefault[0]->str_n1_faktor_wr_add,
					'str_n1_faktor_rv'=> $getDefault[0]->str_n1_faktor_rv,
					'str_n1_faktor_rv_bw'=> $getDefault[0]->str_n1_faktor_rv_bw,
					'str_n1_faktor_rv_jb'=> $getDefault[0]->str_n1_faktor_rv_jb,
					'str_n1_faktor_rv_add'=> $getDefault[0]->str_n1_faktor_rv_add,
					'str_n1_faktor_rv_add_bw'=> $getDefault[0]->str_n1_faktor_rv_add_bw,
					'str_n1_faktor_rv_add_jb'=> $getDefault[0]->str_n1_faktor_rv_add_jb,
					'str_n1_resin'=> $getDefault[0]->str_n1_resin,
					'str_n1_resin_thickness'=> $getDefault[0]->str_n1_resin_thickness,
					'str_n2_resin_csm_a'=> $getDefault[0]->str_n2_resin_csm_a,
					'str_n2_resin_csm_b'=> $getDefault[0]->str_n2_resin_csm_b,
					'str_n2_resin_csm'=> $getDefault[0]->str_n2_resin_csm,
					'str_n2_resin_csm_add_a'=> $getDefault[0]->str_n2_resin_csm_add_a,
					'str_n2_resin_csm_add_b'=> $getDefault[0]->str_n2_resin_csm_add_b,
					'str_n2_resin_csm_add'=> $getDefault[0]->str_n2_resin_csm_add,
					'str_n2_resin_wr_a'=> $getDefault[0]->str_n2_resin_wr_a,
					'str_n2_resin_wr_b'=> $getDefault[0]->str_n2_resin_wr_b,
					'str_n2_resin_wr'=> $getDefault[0]->str_n2_resin_wr,
					'str_n2_resin_wr_add_a'=> $getDefault[0]->str_n2_resin_wr_add_a,
					'str_n2_resin_wr_add_b'=> $getDefault[0]->str_n2_resin_wr_add_b,
					'str_n2_resin_wr_add'=> $getDefault[0]->str_n2_resin_wr_add,
					'str_n2_faktor_csm'=> $getDefault[0]->str_n2_faktor_csm,
					'str_n2_faktor_csm_add'=> $getDefault[0]->str_n2_faktor_csm_add,
					'str_n2_faktor_wr'=> $getDefault[0]->str_n2_faktor_wr,
					'str_n2_faktor_wr_add'=> $getDefault[0]->str_n2_faktor_wr_add,
					'str_n2_resin'=> $getDefault[0]->str_n2_resin,
					'str_n2_resin_thickness'=> $getDefault[0]->str_n2_resin_thickness,

					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail2
				$ArrDetail2_neck1	= array();
				foreach($ListDetail2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetail2_neck1[$val]['acuhan'] 		= $data['acuhan_2_neck1'];
					$ArrDetail2_neck1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2_neck1);

				//Detail2
				$ArrDetail2_neck2	= array();
				foreach($ListDetail2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetail2_neck2[$val]['acuhan'] 		= $data['acuhan_2_neck2'];
					$ArrDetail2_neck2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck2[$val]['last_cost'] 		= $valx['last_cost'];


					// echo "SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1 <br>";
				}
				// print_r($ArrDetail2_neck2);
				// exit;
				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus2_neck1	= array();
				foreach($ListDetailPlus2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetailPlus2_neck1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck1[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck1);

				$ArrDetailPlus2_neck2	= array();
				foreach($ListDetailPlus2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetailPlus2_neck2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck2[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter2_neck1	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck1'],
						'total'			=> $data['tot_lin_thickness2_neck1'],
						'min'			=> $data['mix_lin_thickness2_neck1'],
						'max'			=> $data['max_lin_thickness2_neck1'],
						'hasil'			=> $data['hasil_linier_thickness2_neck1']
				);

				$ArrFooter2_neck2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck2'],
						'total'			=> $data['tot_lin_thickness2_neck2'],
						'min'			=> $data['mix_lin_thickness2_neck2'],
						'max'			=> $data['max_lin_thickness2_neck2'],
						'hasil'			=> $data['hasil_linier_thickness2_neck2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck1 != 0){
					$ArrDataAdd2_neck1 = array();
					foreach($ListDetailAdd2_neck1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
						$ArrDataAdd2_neck1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck1[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck2 != 0){
					$ArrDataAdd2_neck2 = array();
					foreach($ListDetailAdd2_neck2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
						$ArrDataAdd2_neck2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail2_neck1);
				// print_r($ArrDetail2_neck2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus2_neck1);
				// print_r($ArrDetailPlus2_neck2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter2_neck1);
				// print_r($ArrFooter2_neck2);
				// print_r($ArrDefault);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_strukture_neck1 != 0){
					// print_r($ArrDataAdd2_neck1);
				// }
				// if($numberMax_strukture_neck2 != 0){
					// print_r($ArrDataAdd2_neck2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck1);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter2_neck1);
					$this->db->insert('component_footer', $ArrFooter2_neck2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_strukture_neck1 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck1);
					}
					if($numberMax_strukture_neck2 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation colar slongsong code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='colar slongsong' AND deleted='N'")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' OR id_material='MTL-1903173' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Colar Slongsong',
				'action'		=> 'colar_slongsong',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/colar_slongsong', $data);
		}
	}
	//elbowmould
	public function elbowmould(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail		= $data['ListDetail'];
			$ListDetail2	= $data['ListDetail2'];
			$ListDetail3	= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// exit;

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$sudut			= $data['angle'];
			$radiusX		= $data['type_elbow'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdSudut		= sprintf('%03s',$sudut);
			$KdLiner		= $liner;

			$kode_product	= "OF-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-".$KdSudut."-".$radiusX;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{

				$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
						'id_product'			=> $kode_product,
						'parent_product'		=> 'elbow mould',
						'nm_product'			=> $data['top_type'].", ".$data['angle'],
						'series'				=> $data['series'],
						'standart_code'			=> $data['standart_code'],
						'resin_sistem'			=> $DataSeries[0]['resin_system'],
						'pressure'				=> $DataSeries[0]['pressure'],
						'diameter'				=> $data['diameter'],
						'liner'					=> $DataSeries[0]['liner'],
						'aplikasi_product'		=> $data['top_app'],
						'criminal_barier'		=> $data['criminal_barier'],
						'vacum_rate'			=> $data['vacum_rate'],
						'stiffness'				=> $DataApp[0]['data2'],
						'design_life'			=> $data['design_life'],
						'standart_by'			=> $data['top_toleran'],
						'standart_toleransi'	=> $DataCust[0]['nm_customer'],

						'type_elbow'			=> $data['type_elbow'],
						'angle'					=> $data['angle'],
						'radius'				=> $data['radius'],

						'panjang'				=> 0,
						'design'				=> $data['top_tebal_design'],
						'area'					=> $data['area'],
						'est'					=> $data['top_tebal_est'],
						'min_toleransi'			=> $data['top_min_toleran'],
						'max_toleransi'			=> $data['top_max_toleran'],
						'waste'					=> $data['waste'],
						'created_by'			=> $data_session['ORI_User']['username'],
						'created_date'			=> date('Y-m-d H:i:s')
					);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND product_parent = 'elbow mould' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation elbow mould code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='elbow mould' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Elbow Mould',
				'action'		=> 'elbowmould',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/elbowmould', $data);
		}
	}
	//elbowmitter
	public function elbowmitter(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail		= $data['ListDetail'];
			$ListDetail2	= $data['ListDetail2'];
			$ListDetail3	= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// exit;

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['top_diameter'];
			$sudut			= $data['angle'];
			$radiusX		= $data['type_elbow'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdSudut		= sprintf('%03s',$sudut);
			$KdLiner		= $liner;

			$kode_product	= "OM-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-".$KdSudut."-".$radiusX;

			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();


			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				// echo "Masuk Save";
				// exit;
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'elbow mitter',
					'nm_product'			=> $data['top_type'].", ".$data['angle'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['top_diameter'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],

					'type_elbow'			=> $data['type_elbow'],
					'angle'					=> $data['angle'],
					'panjang'				=> str_replace(',', '', $data['panjang']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'radius'				=> $data['radius'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['top_diameter']."' AND product_parent = 'elbow mitter' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation elbow mitter code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='elbow mitter' AND deleted='N' ORDER BY value_d ASC")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND (nm_material LIKE 'PE%' OR nm_material LIKE 'POLYESTER%') ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Elbow Mitter',
				'action'		=> 'elbowmitter',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/elbowmitter', $data);
		}
	}
	//concentricreducer
	public function concentricreducer(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail		= $data['ListDetail'];
			$ListDetail2	= $data['ListDetail2'];
			$ListDetail3	= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// exit;

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdDiameter2	= sprintf('%04s',$diameter2);
			$KdLiner		= $liner;

			$kode_product	= "OR-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2;

			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			// echo $NumRow; exit;
			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				// echo "Masuk Save";
				// exit;
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();


				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'concentric reducer',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> str_replace(',', '', $data['diameter']),
					'diameter2'				=> str_replace(',', '', $data['diameter2']),
					'liner'					=> $liner,
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'panjang'				=> str_replace(',', '', $data['panjang']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);


				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND diameter2 = '".$data['diameter2']."' AND product_parent = 'concentric reducer' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrDefault);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add Estimation Concentric Reducer '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='concentric reducer' AND deleted='N' ORDER BY value_d ASC, value_d2 ASC")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' OR id_material='MTL-1903173' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Concentric Reducer',
				'action'		=> 'concentricreducer',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/concentricreducer', $data);
		}
	}
	//eccentricreducer
	public function eccentricreducer(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail		= $data['ListDetail'];
			$ListDetail2	= $data['ListDetail2'];
			$ListDetail3	= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// exit;

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdDiameter2	= sprintf('%04s',$diameter2);
			$KdLiner		= $liner;

			$kode_product	= "OD-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2;

			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			// echo $NumRow; exit;
			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				// echo "Masuk Save";
				// exit;
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();


				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'eccentric reducer',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> str_replace(',', '', $data['diameter']),
					'diameter2'				=> str_replace(',', '', $data['diameter2']),
					'liner'					=> $liner,
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'panjang'				=> str_replace(',', '', $data['panjang']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);


				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND diameter2 = '".$data['diameter2']."' AND product_parent = 'eccentric reducer' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrDefault);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add Estimation Eccentric Reducer '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='eccentric reducer' AND deleted='N' ORDER BY value_d ASC, value_d2 ASC")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' OR id_material='MTL-1903173' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Eccentric Reducer',
				'action'		=> 'eccentricreducer',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/eccentricreducer', $data);
		}
	}
	//endcap
	public function endcap(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail		= $data['ListDetail'];
			$ListDetail2	= $data['ListDetail2'];
			$ListDetail3	= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListDetailPlus);
			// print_r($ListDetailPlus);
			// print_r($ListDetail3);
			// exit;

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;

			$kode_product	= "OE-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{

				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'end cap',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['diameter'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],

					'panjang'				=> 0,
					'design'				=> $data['top_tebal_design'],
					'radius'				=> $data['radius'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND product_parent = 'end cap' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();
					// echo "SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1 <br>";
					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13); exit;

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component__detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation End Cap code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='end cap' AND deleted='N'")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation End Cap',
				'action'		=> 'endcap',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/endcap', $data);
		}
	}

	//blindflange
	public function blindflange(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];
			// $ListTiming	= $data['ListTime'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListTiming);
			// exit;
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;

			$kode_product	= "OB-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'blind flange',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['diameter'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'flange_od'				=> $data['flange_od'],
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);
				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND product_parent = 'blind flange' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){
					$IDMat1			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat1			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $IDMat1;
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){
					$IDMat2			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat2			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $IDMat2;
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){
					$IDMat3			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat3			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $IDMat3;
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					// $this->db->insert_batch('component_time', $ArrTiming);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation Blind Flange code : '.$kode_product);
				}
			}


			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='blind flange' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Blind Flange',
				'action'		=> 'blindflange',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,

				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/blindflange', $data);
		}
	}

	//pipeslongsong
	public function pipeslongsong(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$ListTiming	= $data['ListTime'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}

			// echo "<pre>";
			// print_r($ListTiming);
			// exit;
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;
			$pipe_for		= $data['pipe_for'];

			$kode_product	= "OS-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-".$pipe_for;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				// echo "Masuk Save";
				// exit;
				$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'pipe slongsong',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['diameter'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'standart_by'			=> $data['top_toleran'],
					'standart_toleransi'	=> $DataCust[0]['nm_customer'],

					'panjang'				=> str_replace(',', '', $data['top_length']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);


				$ArrTiming	= array();
				foreach($ListTiming AS $val => $valx){
					$ArrTiming[$val]['id_product'] 		= $kode_product;
					$ArrTiming[$val]['process'] 		= $valx['process'];
					$ArrTiming[$val]['sub_process'] 	= $valx['sub_process'];
					$ArrTiming[$val]['time_process'] 	= $valx['time_process'];
					$ArrTiming[$val]['man_power'] 		= (!empty($valx['man_power']))?$valx['man_power'] : '0';
					$ArrTiming[$val]['man_hours'] 		= $valx['man_hours'];
				}

				// print_r($ArrTiming); exit;

				// Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){
					$IDMat1			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat1			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $IDMat1;
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){
					$IDMat2			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat2			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $IDMat2;
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_time', $ArrTiming);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation code : '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe slongsong' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Pipe Slongsong',
				'action'		=> 'pipeslongsong',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,

				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/pipeslongsong', $data);
		}
	}
	//equalteemould
	public function equalteemould(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];
			// $ListTiming	= $data['ListTime'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListTiming);
			// exit;
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);

			$kode_product	= "OT-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'equal tee mould',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['diameter'],
					'diameter2'				=> $data['diameter2'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],

					'wrap_length'	=> $data['wrap_length'],
					'area2'			=> $data['area2'],
					'high'			=> $data['high'],

					'panjang'				=> str_replace(',', '', $data['panjang']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND diameter2 = '".$data['diameter2']."' AND product_parent = 'equal tee mould' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){
					$IDMat1			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat1			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $IDMat1;
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){
					$IDMat2			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat2			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $IDMat2;
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){
					$IDMat3			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat3			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $IDMat3;
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					// $this->db->insert_batch('component_time', $ArrTiming);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					history('Add Est Equal Tee Mould code : '.$kode_product);
				}
			}


			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='equal tee mould' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Equal Tee Mould',
				'action'		=> 'equalteemould',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,

				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/equalteemould', $data);
		}
	}
	//reducerteemould
	public function reducerteemould(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];
			// $ListTiming	= $data['ListTime'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListTiming);
			// exit;
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdDiameter2		= sprintf('%04s',$diameter2);
			$KdLiner		= $liner;

			$kode_product	= "OC-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'reducer tee mould',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['diameter'],
					'diameter2'				=> $data['diameter2'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],

					'wrap_length'		=> $data['wrap_length'],
					'wrap_length2'		=> $data['wrap_length2'],
					'area2'				=> $data['area2'],
					'high'				=> $data['high'],
					'diameter2'				=> $data['diameter2'],

					'panjang'				=> str_replace(',', '', $data['panjang']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND diameter2 = '".$data['diameter2']."' AND product_parent = 'reducer tee mould' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){
					$IDMat1			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat1			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $IDMat1;
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){
					$IDMat2			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat2			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $IDMat2;
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){
					$IDMat3			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat3			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $IDMat3;
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM						= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM								= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] 			= $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					// $this->db->insert_batch('component_time', $ArrTiming);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation Reducer Tee Mould code : '.$kode_product);
				}
			}


			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='reducer tee mould' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Reducer Tee Mould',
				'action'		=> 'reducerteemould',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,

				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/reducerteemould', $data);
		}
	}
	//equalteeslongsong
	public function equalteeslongsong(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];
			// $ListTiming	= $data['ListTime'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListTiming);
			// exit;
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);

			$kode_product	= "OQ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'equal tee slongsong',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['diameter'],
					'diameter2'				=> $data['diameter2'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],

					'wrap_length'	=> $data['wrap_length'],
					'area2'			=> $data['area2'],
					'high'			=> $data['high'],

					'panjang'				=> str_replace(',', '', $data['panjang']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader);
				// exit;


				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND diameter2 = '".$data['diameter2']."' AND product_parent = 'equal tee slongsong' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){
					$IDMat1			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat1			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $IDMat1;
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){
					$IDMat2			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat2			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $IDMat2;
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){
					$IDMat3			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat3			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $IDMat3;
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					// $this->db->insert_batch('component_time', $ArrTiming);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					history('Add Est Equal Tee Slongsong code : '.$kode_product);
				}
			}


			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='equal tee slongsong' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Equal Tee Slongsong',
				'action'		=> 'equalteeslongsong',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,

				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/equalteeslongsong', $data);
		}
	}
	//reducerteeslongsong
	public function reducerteeslongsong(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];
			// $ListTiming	= $data['ListTime'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			// echo "<pre>";
			// print_r($ListTiming);
			// exit;
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdDiameter2		= sprintf('%04s',$diameter2);
			$KdLiner		= $liner;

			$kode_product	= "OL-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> 'reducer tee slongsong',
					'nm_product'			=> $data['top_type'],
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'diameter'				=> $data['diameter'],
					'diameter2'				=> $data['diameter2'],
					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],

					'wrap_length'		=> $data['wrap_length'],
					'wrap_length2'		=> $data['wrap_length2'],
					'area2'				=> $data['area2'],
					'high'				=> $data['high'],
					'diameter2'				=> $data['diameter2'],

					'panjang'				=> str_replace(',', '', $data['panjang']),
					'design'				=> $data['top_tebal_design'],
					'area'					=> $data['area'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['diameter']."' AND diameter2 = '".$data['diameter2']."' AND product_parent = 'reducer tee slongsong' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,
					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){
					$IDMat1			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat1			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $IDMat1;
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){
					$IDMat2			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat2			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $IDMat2;
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){
					$IDMat3			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat3			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $IDMat3;
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM						= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM								= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] 			= $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter3);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					// $this->db->insert_batch('component_time', $ArrTiming);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation Reducer Tee Slongsong code : '.$kode_product);
				}
			}


			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='reducer tee slongsong' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Reducer Tee Slongsong',
				'action'		=> 'reducerteeslongsong',
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,

				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/reducerteeslongsong', $data);
		}
	}
	//flangeslongsong
	public function flangeslongsong(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail2_neck1	= $data['ListDetail2_neck1'];
			$ListDetail2_neck2	= $data['ListDetail2_neck2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus2_neck1	= $data['ListDetailPlus2_neck1'];
			$ListDetailPlus2_neck2	= $data['ListDetailPlus2_neck2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];

			// print_r($ListDetail2_neck2);

			$numberMax_liner		= $data['numberMax_liner'];
			$numberMax_strukture	= $data['numberMax_strukture'];
			$numberMax_strukture_neck1	= $data['numberMax_strukture_neck1'];
			$numberMax_strukture_neck2	= $data['numberMax_strukture_neck2'];
			$numberMax_external		= $data['numberMax_external'];
			$numberMax_topcoat		= $data['numberMax_topcoat'];

			if($numberMax_liner != 0){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if($numberMax_strukture != 0){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if($numberMax_strukture_neck1 != 0){
				$ListDetailAdd2_neck1	= $data['ListDetailAdd_Strukture_neck1'];
			}
			if($numberMax_strukture_neck2 != 0){
				$ListDetailAdd2_neck2	= $data['ListDetailAdd_Strukture_neck2'];
			}
			if($numberMax_external != 0){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if($numberMax_topcoat != 0){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['top_diameter'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= $liner;

			$kode_product	= "OA-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();

			if($NumRow > 0){
				$Arr_Kembali	= array(
					'pesan'		=>'Specifications are already in the list. Check again ...',
					'status'	=> 3
				);
			}
			else{

				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'nm_product'			=> $data['top_type'],
					'parent_product'		=> 'flange slongsong',
					'series'				=> $data['series'],
					'standart_code'			=> $data['standart_code'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],

					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'diameter'				=> str_replace(',', '', $data['top_diameter']),
					'panjang'				=> "",

					'panjang_neck_1'		=> $data['panjang_neck_1'],
					'panjang_neck_2'		=> $data['panjang_neck_2'],
					'design_neck_1'			=> $data['design_neck_1'],
					'design_neck_2'			=> $data['design_neck_2'],
					'est_neck_1'			=> $data['est_neck_1'],
					'est_neck_2'			=> $data['est_neck_2'],
					'area_neck_1'			=> $data['area_neck_1'],
					'area_neck_2'			=> $data['area_neck_2'],
					'flange_od'				=> $data['flange_od'],
					'flange_bcd'			=> $data['flange_bcd'],
					'flange_n'				=> $data['flange_n'],
					'flange_oh'				=> $data['flange_oh'],

					'design'				=> $data['top_tebal_design'],
					'est'					=> $data['top_tebal_est'],
					'min_toleransi'			=> $data['top_min_toleran'],
					'max_toleransi'			=> $data['top_max_toleran'],
					'waste'					=> $data['waste'],
					'area'					=> $data['area'],
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				// print_r($ArrHeader); exit;

				$qDefault		= "SELECT * FROM help_default WHERE standart_code='".$data['standart_code']."' AND diameter = '".$data['top_diameter']."' AND product_parent = 'flange slongsong' ";
				$getDefault		= $this->db->query($qDefault)->result();
				$ArrDefault		= array(
					'id_product'			=>$kode_product,
					'product_parent'		=> $getDefault[0]->product_parent,
					'kd_cust'				=> $getDefault[0]->kd_cust,
					'customer'				=> $getDefault[0]->customer,
					'standart_code'			=> $getDefault[0]->standart_code,
					'diameter'				=> $getDefault[0]->diameter,
					'diameter2'				=> $getDefault[0]->diameter2,
					'liner'					=> $getDefault[0]->liner,
					'pn'					=> $getDefault[0]->pn,
					'overlap'				=> $getDefault[0]->overlap,
					'waste'					=> $getDefault[0]->waste,
					'max'					=> $getDefault[0]->max,
					'min'					=> $getDefault[0]->min,

					'plastic_film'			=> $getDefault[0]->plastic_film,
					'lin_resin_veil_a'		=> $getDefault[0]->lin_resin_veil_a,
					'lin_resin_veil_b'		=> $getDefault[0]->lin_resin_veil_b,
					'lin_resin_veil'		=> $getDefault[0]->lin_resin_veil,
					'lin_resin_veil_add_a'	=> $getDefault[0]->lin_resin_veil_add_a,
					'lin_resin_veil_add_b'	=> $getDefault[0]->lin_resin_veil_add_b,
					'lin_resin_veil_add'	=> $getDefault[0]->lin_resin_veil_add,
					'lin_resin_csm_a'		=> $getDefault[0]->lin_resin_csm_a,
					'lin_resin_csm_b'		=> $getDefault[0]->lin_resin_csm_b,
					'lin_resin_csm'			=> $getDefault[0]->lin_resin_csm,
					'lin_resin_csm_add_a'	=> $getDefault[0]->lin_resin_csm_add_a,
					'lin_resin_csm_add_b'	=> $getDefault[0]->lin_resin_csm_add_b,
					'lin_resin_csm_add'		=> $getDefault[0]->lin_resin_csm_add,
					'lin_faktor_veil'		=> $getDefault[0]->lin_faktor_veil,
					'lin_faktor_veil_add'	=> $getDefault[0]->lin_faktor_veil_add,
					'lin_faktor_csm'		=> $getDefault[0]->lin_faktor_csm,

					'lin_faktor_csm_add'	=> $getDefault[0]->lin_faktor_csm_add,
					'lin_resin'				=> $getDefault[0]->lin_resin,
					'str_resin_csm_a'		=> $getDefault[0]->str_resin_csm_a,
					'str_resin_csm_b'		=> $getDefault[0]->str_resin_csm_b,
					'str_resin_csm'			=> $getDefault[0]->str_resin_csm,
					'str_resin_csm_add_a'	=> $getDefault[0]->str_resin_csm_add_a,
					'str_resin_csm_add_b'	=> $getDefault[0]->str_resin_csm_add_b,
					'str_resin_csm_add'		=> $getDefault[0]->str_resin_csm_add,
					'str_resin_wr_a'		=> $getDefault[0]->str_resin_wr_a,
					'str_resin_wr_b'		=> $getDefault[0]->str_resin_wr_b,
					'str_resin_wr'			=> $getDefault[0]->str_resin_wr,
					'str_resin_wr_add_a'	=> $getDefault[0]->str_resin_wr_add_a,
					'str_resin_wr_add_b'	=> $getDefault[0]->str_resin_wr_add_b,
					'str_resin_wr_add'		=> $getDefault[0]->str_resin_wr_add,
					'str_resin_rv_a'		=> $getDefault[0]->str_resin_rv_a,

					'str_resin_rv_b'		=> $getDefault[0]->str_resin_rv_b,
					'str_resin_rv'			=> $getDefault[0]->str_resin_rv,
					'str_resin_rv_add_a'	=> $getDefault[0]->str_resin_rv_add_a,
					'str_resin_rv_add_b'	=> $getDefault[0]->str_resin_rv_add_b,
					'str_resin_rv_add'		=> $getDefault[0]->str_resin_rv_add,
					'str_faktor_csm'		=> $getDefault[0]->str_faktor_csm,
					'str_faktor_csm_add'	=> $getDefault[0]->str_faktor_csm_add,
					'str_faktor_wr'			=> $getDefault[0]->str_faktor_wr,
					'str_faktor_wr_add'		=> $getDefault[0]->str_faktor_wr_add,
					'str_faktor_rv'			=> $getDefault[0]->str_faktor_rv,
					'str_faktor_rv_bw'		=> $getDefault[0]->str_faktor_rv_bw,
					'str_faktor_rv_jb'		=> $getDefault[0]->str_faktor_rv_jb,
					'str_faktor_rv_add'		=> $getDefault[0]->str_faktor_rv_add,

					'str_faktor_rv_add_bw'	=> $getDefault[0]->str_faktor_rv_add_bw,
					'str_faktor_rv_add_jb'	=> $getDefault[0]->str_faktor_rv_add_jb,
					'str_resin'				=> $getDefault[0]->str_resin,
					'eks_resin_veil_a'		=> $getDefault[0]->eks_resin_veil_a,
					'eks_resin_veil_b'		=> $getDefault[0]->eks_resin_veil_b,
					'eks_resin_veil'		=> $getDefault[0]->eks_resin_veil,
					'eks_resin_veil_add_a'	=> $getDefault[0]->eks_resin_veil_add_a,
					'eks_resin_veil_add_b'	=> $getDefault[0]->eks_resin_veil_add_b,
					'eks_resin_veil_add'	=> $getDefault[0]->eks_resin_veil_add,
					'eks_resin_csm_a'		=> $getDefault[0]->eks_resin_csm_a,
					'eks_resin_csm_b'		=> $getDefault[0]->eks_resin_csm_b,
					'eks_resin_csm'			=> $getDefault[0]->eks_resin_csm,

					'eks_resin_csm_add_a'	=> $getDefault[0]->eks_resin_csm_add_a,
					'eks_resin_csm_add_b'	=> $getDefault[0]->eks_resin_csm_add_b,
					'eks_resin_csm_add'		=> $getDefault[0]->eks_resin_csm_add,
					'eks_faktor_veil'		=> $getDefault[0]->eks_faktor_veil,
					'eks_faktor_veil_add'	=> $getDefault[0]->eks_faktor_veil_add,
					'eks_faktor_csm'		=> $getDefault[0]->eks_faktor_csm,
					'eks_faktor_csm_add'	=> $getDefault[0]->eks_faktor_csm_add,
					'eks_resin'				=> $getDefault[0]->eks_resin,

					'str_n1_resin_csm_a'=> $getDefault[0]->str_n1_resin_csm_a,
					'str_n1_resin_csm_b'=> $getDefault[0]->str_n1_resin_csm_b,
					'str_n1_resin_csm'=> $getDefault[0]->str_n1_resin_csm,
					'str_n1_resin_csm_add_a'=> $getDefault[0]->str_n1_resin_csm_add_a,
					'str_n1_resin_csm_add_b'=> $getDefault[0]->str_n1_resin_csm_add_b,
					'str_n1_resin_csm_add'=> $getDefault[0]->str_n1_resin_csm_add,
					'str_n1_resin_wr_a'=> $getDefault[0]->str_n1_resin_wr_a,
					'str_n1_resin_wr_b'=> $getDefault[0]->str_n1_resin_wr_b,
					'str_n1_resin_wr'=> $getDefault[0]->str_n1_resin_wr,
					'str_n1_resin_wr_add_a'=> $getDefault[0]->str_n1_resin_wr_add_a,
					'str_n1_resin_wr_add_b'=> $getDefault[0]->str_n1_resin_wr_add_b,
					'str_n1_resin_wr_add'=> $getDefault[0]->str_n1_resin_wr_add,
					'str_n1_resin_rv_a'=> $getDefault[0]->str_n1_resin_rv_a,
					'str_n1_resin_rv_b'=> $getDefault[0]->str_n1_resin_rv_b,
					'str_n1_resin_rv'=> $getDefault[0]->str_n1_resin_rv,
					'str_n1_resin_rv_add_a'=> $getDefault[0]->str_n1_resin_rv_add_a,
					'str_n1_resin_rv_add_b'=> $getDefault[0]->str_n1_resin_rv_add_b,
					'str_n1_resin_rv_add'=> $getDefault[0]->str_n1_resin_rv_add,
					'str_n1_faktor_csm'=> $getDefault[0]->str_n1_faktor_csm,
					'str_n1_faktor_csm_add'=> $getDefault[0]->str_n1_faktor_csm_add,
					'str_n1_faktor_wr'=> $getDefault[0]->str_n1_faktor_wr,
					'str_n1_faktor_wr_add'=> $getDefault[0]->str_n1_faktor_wr_add,
					'str_n1_faktor_rv'=> $getDefault[0]->str_n1_faktor_rv,
					'str_n1_faktor_rv_bw'=> $getDefault[0]->str_n1_faktor_rv_bw,
					'str_n1_faktor_rv_jb'=> $getDefault[0]->str_n1_faktor_rv_jb,
					'str_n1_faktor_rv_add'=> $getDefault[0]->str_n1_faktor_rv_add,
					'str_n1_faktor_rv_add_bw'=> $getDefault[0]->str_n1_faktor_rv_add_bw,
					'str_n1_faktor_rv_add_jb'=> $getDefault[0]->str_n1_faktor_rv_add_jb,
					'str_n1_resin'=> $getDefault[0]->str_n1_resin,
					'str_n1_resin_thickness'=> $getDefault[0]->str_n1_resin_thickness,
					'str_n2_resin_csm_a'=> $getDefault[0]->str_n2_resin_csm_a,
					'str_n2_resin_csm_b'=> $getDefault[0]->str_n2_resin_csm_b,
					'str_n2_resin_csm'=> $getDefault[0]->str_n2_resin_csm,
					'str_n2_resin_csm_add_a'=> $getDefault[0]->str_n2_resin_csm_add_a,
					'str_n2_resin_csm_add_b'=> $getDefault[0]->str_n2_resin_csm_add_b,
					'str_n2_resin_csm_add'=> $getDefault[0]->str_n2_resin_csm_add,
					'str_n2_resin_wr_a'=> $getDefault[0]->str_n2_resin_wr_a,
					'str_n2_resin_wr_b'=> $getDefault[0]->str_n2_resin_wr_b,
					'str_n2_resin_wr'=> $getDefault[0]->str_n2_resin_wr,
					'str_n2_resin_wr_add_a'=> $getDefault[0]->str_n2_resin_wr_add_a,
					'str_n2_resin_wr_add_b'=> $getDefault[0]->str_n2_resin_wr_add_b,
					'str_n2_resin_wr_add'=> $getDefault[0]->str_n2_resin_wr_add,
					'str_n2_faktor_csm'=> $getDefault[0]->str_n2_faktor_csm,
					'str_n2_faktor_csm_add'=> $getDefault[0]->str_n2_faktor_csm_add,
					'str_n2_faktor_wr'=> $getDefault[0]->str_n2_faktor_wr,
					'str_n2_faktor_wr_add'=> $getDefault[0]->str_n2_faktor_wr_add,
					'str_n2_resin'=> $getDefault[0]->str_n2_resin,
					'str_n2_resin_thickness'=> $getDefault[0]->str_n2_resin_thickness,


					'topcoat_resin'			=> $getDefault[0]->topcoat_resin,
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);

				//Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['id_product'] 	= $kode_product;
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['acuhan'] 		= $data['acuhan_1'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail1);

				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['acuhan'] 		= $data['acuhan_2'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);

				//Detail2
				$ArrDetail2_neck1	= array();
				foreach($ListDetail2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetail2_neck1[$val]['acuhan'] 		= $data['acuhan_2_neck1'];
					$ArrDetail2_neck1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck1[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck1[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck1[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck1[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck1[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck1[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck1[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck1[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2_neck1);

				//Detail2
				$ArrDetail2_neck2	= array();
				foreach($ListDetail2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetail2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetail2_neck2[$val]['acuhan'] 		= $data['acuhan_2_neck2'];
					$ArrDetail2_neck2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail2_neck2[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail2_neck2[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail2_neck2[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail2_neck2[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail2_neck2[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail2_neck2[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail2_neck2[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail2_neck2[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail2_neck2[$val]['last_cost'] 		= $valx['last_cost'];


					// echo "SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1 <br>";
				}
				// print_r($ArrDetail2_neck2);
				// exit;
				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['id_product'] 	= $kode_product;
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['acuhan'] 		= $data['acuhan_3'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM							= (!empty($valx['value']))?$valx['value']:'';
					$ArrDetail13[$val]['value'] 			= $valueM;
						$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
					$ArrDetail13[$val]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
					$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
						$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
					$ArrDetail13[$val]['bw'] 			= $bwM;
						$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
					$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
						$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
					$ArrDetail13[$val]['layer'] 			= $layerM;;
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetail13[$val]['containing'] 	= $containingM;
						$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
					$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
					$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);

				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus1[$val]['perse'] = $perseM;
					$ArrDetailPlus1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);

				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2[$val]['perse'] = $perseM;
					$ArrDetailPlus2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);

				$ArrDetailPlus2_neck1	= array();
				foreach($ListDetailPlus2_neck1 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck1[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
					$ArrDetailPlus2_neck1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2_neck1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck1[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck1[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck1);

				$ArrDetailPlus2_neck2	= array();
				foreach($ListDetailPlus2_neck2 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus2_neck2[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
					$ArrDetailPlus2_neck2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2_neck2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus2_neck2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus2_neck2[$val]['perse'] = $perseM;
					$ArrDetailPlus2_neck2[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus2_neck2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2_neck2);

				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus3[$val]['perse'] = $perseM;
					$ArrDetailPlus3[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['id_product'] 	= $kode_product;
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDetailPlus4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDetailPlus4[$val]['perse'] = $perseM;
					$ArrDetailPlus4[$val]['last_full'] 		= $valx['last_full'];
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

				$ArrFooter	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name'],
						'total'			=> $data['tot_lin_thickness'],
						'min'			=> $data['mix_lin_thickness'],
						'max'			=> $data['max_lin_thickness'],
						'hasil'			=> $data['hasil_linier_thickness']
				);

				$ArrFooter2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2'],
						'total'			=> $data['tot_lin_thickness2'],
						'min'			=> $data['mix_lin_thickness2'],
						'max'			=> $data['max_lin_thickness2'],
						'hasil'			=> $data['hasil_linier_thickness2']
				);

				$ArrFooter2_neck1	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck1'],
						'total'			=> $data['tot_lin_thickness2_neck1'],
						'min'			=> $data['mix_lin_thickness2_neck1'],
						'max'			=> $data['max_lin_thickness2_neck1'],
						'hasil'			=> $data['hasil_linier_thickness2_neck1']
				);

				$ArrFooter2_neck2	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name2_neck2'],
						'total'			=> $data['tot_lin_thickness2_neck2'],
						'min'			=> $data['mix_lin_thickness2_neck2'],
						'max'			=> $data['max_lin_thickness2_neck2'],
						'hasil'			=> $data['hasil_linier_thickness2_neck2']
				);

				$ArrFooter3	= array(
						'id_product'	=> $kode_product,
						'detail_name'	=> $data['detail_name3'],
						'total'			=> $data['tot_lin_thickness3'],
						'min'			=> $data['mix_lin_thickness3'],
						'max'			=> $data['max_lin_thickness3'],
						'hasil'			=> $data['hasil_linier_thickness3']
				);

				if($numberMax_liner != 0){
					$ArrDataAdd1 = array();
					foreach($ListDetailAdd1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd1[$val]['perse'] 		= $perseM;
						$ArrDataAdd1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture != 0){
					$ArrDataAdd2 = array();
					foreach($ListDetailAdd2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck1 != 0){
					$ArrDataAdd2_neck1 = array();
					foreach($ListDetailAdd2_neck1 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck1[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck1[$val]['detail_name'] 	= $data['detail_name2_neck1'];
						$ArrDataAdd2_neck1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck1[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck1[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck1[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck1[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_strukture_neck2 != 0){
					$ArrDataAdd2_neck2 = array();
					foreach($ListDetailAdd2_neck2 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2_neck2[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd2_neck2[$val]['detail_name'] 	= $data['detail_name2_neck2'];
						$ArrDataAdd2_neck2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2_neck2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2_neck2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2_neck2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd2_neck2[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd2_neck2[$val]['perse'] 		= $perseM;
						$ArrDataAdd2_neck2[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd2_neck2[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_external != 0){
					$ArrDataAdd3 = array();
					foreach($ListDetailAdd3 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd3[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd3[$val]['perse'] 		= $perseM;
						$ArrDataAdd3[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}
				if($numberMax_topcoat != 0){
					$ArrDataAdd4 = array();
					foreach($ListDetailAdd4 AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
						$ArrDataAdd4[$val]['containing'] 	= $containingM;
							$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
						$ArrDataAdd4[$val]['perse'] 		= $perseM;
						$ArrDataAdd4[$val]['last_full'] 	= $valx['last_full'];
						$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail2_neck1);
				// print_r($ArrDetail2_neck2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus2_neck1);
				// print_r($ArrDetailPlus2_neck2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrFooter);
				// print_r($ArrFooter2);
				// print_r($ArrFooter2_neck1);
				// print_r($ArrFooter2_neck2);
				// print_r($ArrDefault);

				// if($numberMax_liner != 0){
					// print_r($ArrDataAdd1);
				// }
				// if($numberMax_strukture != 0){
					// print_r($ArrDataAdd2);
				// }
				// if($numberMax_strukture_neck1 != 0){
					// print_r($ArrDataAdd2_neck1);
				// }
				// if($numberMax_strukture_neck2 != 0){
					// print_r($ArrDataAdd2_neck2);
				// }
				// if($numberMax_external != 0){
					// print_r($ArrDataAdd3);
				// }
				// if($numberMax_topcoat != 0){
					// print_r($ArrDataAdd4);
				// }
				// exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck1);
					$this->db->insert_batch('component_detail', $ArrDetail2_neck2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2_neck2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					$this->db->insert('component_footer', $ArrFooter);
					$this->db->insert('component_footer', $ArrFooter2);
					$this->db->insert('component_footer', $ArrFooter2_neck1);
					$this->db->insert('component_footer', $ArrFooter2_neck2);
					$this->db->insert('component_footer', $ArrFooter3);
					if($numberMax_liner != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
					}
					if($numberMax_strukture != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
					}
					if($numberMax_strukture_neck1 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck1);
					}
					if($numberMax_strukture_neck2 != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd2_neck2);
					}
					if($numberMax_external != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
					}
					if($numberMax_topcoat != 0){
						$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					update_berat_est($kode_product);
					history('Add estimation flange slongsong code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='flange slongsong' AND deleted='N'")->result_array();

			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries			= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' OR id_material='MTL-1903173' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Flange Slongsong',
				'action'		=> 'flangeslongsong',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner,
				'series'		=> $ListSeries,

				'criminal_barier'	=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'		=> $ListVacumRate,
				'design_life'		=> $ListDesignLife,
				'standard'		=> $ListCustomer,
				'customer'		=> $ListCustomer2,

				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,

				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,

				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,

				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl
			);

			$this->load->view('Component/est/flangeslongsong', $data);
		}
	}
	//branchjoint
	public function branchjoint(){
	  if($this->input->post()){
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$mY						=  date('ym');
		$ListDetail_Glass		= $data['glass'];
		$ListDetail_resinnadd	= $data['resinnadd'];
		//print_r($ListDetail_Glass);
		$glass 		= array();
		$resinnadd 	= array();
		$count 		= 0;
		//echo $ListDetail_Glass['id_material'][0];

		$ArrDet1 	= array();
		$ArrIl 		= array();
		$ArrOl 		= array();
		$no_il 		= $data['no_il'];
		$no_ol 		= $data['no_ol'];

		//pengurutan kode
		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter_1		= $data['diameter_1'];
		$diameter_2		= $data['diameter_2'];

		$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
		$KdPressure		= sprintf('%02s',$pressure);
		$KdDiameter_1	= sprintf('%04s',$diameter_1);
		$KdDiameter_2	= sprintf('%04s',$diameter_2);
		$KdLiner		= $liner;
		$KDCust			=	$data['cust'];

		$ket_plus		= "";
		$ket_plus2		= "";
		if(!empty($data['ket_plus'])){
			$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
			$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
		}

		if ($KDCust == 'C100-1903000' OR empty($KDCust)) {
			$kode_product	= "BJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner."-DN".$KdDiameter_2."-".$KdLiner.$ket_plus;
			$KDCust			=	"";
		}else {
			$kode_product	= "BJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner."-DN".$KdDiameter_2."-".$KdLiner."-".$KDCust.$ket_plus;
			$KDCust			=	$data['cust'];
		}
		// echo $kode_product; exit;
		$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
		$NumRow			= $this->db->query($srcType)->num_rows();

		if($NumRow > 0){
		  $Arr_Kembali	= array(
			'pesan'		=>'Specifications are already in the list. Check again ...',
			'status'	=> 3
		  );
		}
		else{

		  $DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
		  $DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		  $ArrHeader	= array(
			'id_product'				=> $kode_product,
			'cust'						=> $KDCust,
			'ket_plus'					=> $ket_plus2,
			'parent_product'			=> 'branch joint',
			'nm_product'				=> $data['top_type_1']." X ".$diameter_2,
			'series'					=> $data['series'],
			'resin_sistem'				=> $DataSeries[0]['resin_system'],
			'pressure'					=> $DataSeries[0]['pressure'],
			'diameter'					=> $data['diameter_1'],
			'liner'						=> $DataSeries[0]['liner'],
			'standart_by'				=> $data['top_toleran'],
			'standart_toleransi'		=> $DataCust[0]['nm_customer'],
			'diameter2'					=> $data['diameter_2'],
			'panjang'					=> $data['minimum_width'],
			'waste'						=> $data['waste'],
			'pipe_thickness'			=> $data['pipe_thickness'],
			'joint_thickness'			=> $data['joint_thickness'],
			'factor_thickness'			=> $data['factor_thickness'],
			'created_by'				=> $data_session['ORI_User']['username'],
			'created_date'				=> date('Y-m-d H:i:s')
		  );

		  // print_r($ArrHeader); exit;
		  foreach ($ListDetail_Glass as $key => $value) {
			foreach ($value as $k => $val) {
			  $idm = $ListDetail_Glass['id_material'][$k];
			  $dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->result_array();

			  $glass[$k][$key] = $val;
			  $glass[$k]['id_product'] 	= $kode_product;
			  //$glass[$k]['detail_name'] 	= $data['detail_name'];
			  //$glass[$k]['acuhan'] 		= $data['acuhan_1'];
			  //$glass[$k]['id_category'] 	= $dataMaterial[0]['id_category'];
			  $glass[$k]['nm_category'] 	= $ListDetail_resinnadd['nm_category'][$k];//$dataMaterial[0]['nm_category'];
			  //$glass[$k]['id_material'] 	= $valx['id_material'];
			  $glass[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				
			  $valueM						= (!empty($ListDetail_Glass['value'][$k]))?$ListDetail_Glass['value'][$k]:'';
			  $glass[$k]['value'] 			= $valueM;
				$thicknessM					= (!empty($ListDetail_Glass['thickness'][$k]))?$ListDetail_Glass['thickness'][$k]:'';
			  $glass[$k]['thickness'] 		= $thicknessM;
				$pengaliM					= (!empty($ListDetail_Glass['fak_pengali'][$k]))?$ListDetail_Glass['fak_pengali'][$k]:'';
			  $glass[$k]['fak_pengali'] 	= $pengaliM;
				$bwM						= (!empty($ListDetail_Glass['bw'][$k]))?$ListDetail_Glass['bw'][$k]:'';
			  $glass[$k]['bw'] 				= $bwM;
				$jumlahM					= (!empty($ListDetail_Glass['jumlah'][$k]))?$ListDetail_Glass['jumlah'][$k]:'';
			  $glass[$k]['jumlah'] 			= $jumlahM;
				$layerM						= (!empty($ListDetail_Glass['layer'][$k]))?$ListDetail_Glass['layer'][$k]:'';
			  $glass[$k]['layer'] 			= $layerM;;
				$containingM				= (!empty($ListDetail_Glass['containing'][$k]))?$ListDetail_Glass['containing'][$k]:'';
			  $glass[$k]['containing'] 		= $containingM;
				$total_thicknessM			= (!empty($ListDetail_Glass['total_thickness'][$k]))?$ListDetail_Glass['total_thickness'][$k]:'';
			  $glass[$k]['total_thickness'] = $total_thicknessM;
				$lastfullM					= (!empty($ListDetail_Glass['last_full'][$k]))?$ListDetail_Glass['last_full'][$k]:'';
			  $glass[$k]['last_full'] 		= $lastfullM;
				$lastcostM					= (!empty($ListDetail_Glass['last_cost'][$k]))?$ListDetail_Glass['last_cost'][$k]:'';
			  $glass[$k]['last_cost'] 		= $lastcostM;
			}

		  }
		  foreach ($ListDetail_resinnadd as $key => $value) {
			foreach ($value as $k => $val) {
			  $idm = $ListDetail_resinnadd['id_material'][$k];
			  $dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->result_array();

			  $resinnadd[$k][$key] 				= $val;
			  $resinnadd[$k]['id_product'] 		= $kode_product;
			  //$resinnadd[$k]['detail_name'] 	= $data['detail_name'];
			  //$resinnadd[$k]['acuhan'] 		= $data['acuhan_1'];
			  //$resinnadd[$k]['id_category'] 	= $dataMaterial[0]['id_category'];
			  //$resinnadd[$k]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			  $resinnadd[$k]['nm_category'] 	= $ListDetail_resinnadd['nm_category'][$k];
			  //$resinnadd[$k]['id_material'] 	= $valx['id_material'];
			  $resinnadd[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($ListDetail_resinnadd['value'][$k]))?$ListDetail_resinnadd['value'][$k]:'';
			  $resinnadd[$k]['value'] 			= $valueM;
				$thicknessM						= (!empty($ListDetail_resinnadd['thickness'][$k]))?$ListDetail_resinnadd['thickness'][$k]:'';
			  $resinnadd[$k]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($ListDetail_resinnadd['fak_pengali'][$k]))?$ListDetail_resinnadd['fak_pengali'][$k]:'';
			  $resinnadd[$k]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($ListDetail_resinnadd['bw'][$k]))?$ListDetail_resinnadd['bw'][$k]:'';
			  $resinnadd[$k]['bw'] 				= $bwM;
				$jumlahM						= (!empty($ListDetail_resinnadd['jumlah'][$k]))?$ListDetail_resinnadd['jumlah'][$k]:'';
			  $resinnadd[$k]['jumlah'] 			= $jumlahM;
				$layerM							= (!empty($ListDetail_resinnadd['layer'][$k]))?$ListDetail_resinnadd['layer'][$k]:'';
			  $resinnadd[$k]['layer'] 			= $layerM;;
				$containingM					= (!empty($ListDetail_resinnadd['containing'][$k]))?$ListDetail_resinnadd['containing'][$k]:'';
			  $resinnadd[$k]['containing'] 		= $containingM;
				$total_thicknessM				= (!empty($ListDetail_resinnadd['total_thickness'][$k]))?$ListDetail_resinnadd['total_thickness'][$k]:'';
			  $resinnadd[$k]['total_thickness'] = $total_thicknessM;
				$lastfullM						= (!empty($ListDetail_resinnadd['last_full'][$k]))?$ListDetail_resinnadd['last_full'][$k]:'';
			  $resinnadd[$k]['last_full'] 		= $lastfullM;
				$lastcostM						= (!empty($ListDetail_resinnadd['last_cost'][$k]))?$ListDetail_resinnadd['last_cost'][$k]:'';
			  $resinnadd[$k]['last_cost'] 		= $lastcostM;
			}

		  }
		  for ($i=0; $i < $no_il; $i++) {
			$ArrIl[$i]['id_product']		= $kode_product;
			$ArrIl[$i]['detail_name']		= 'Inside Lamination';
			$ArrIl[$i]['lapisan'] 			= $data['lapisan_'.($i+1)];
			$ArrIl[$i]['std_glass'] 		= $data['std_'.($i+1)];
			$ArrIl[$i]['width'] 			= $data['width_'.($i+1)];
			$ArrIl[$i]['stage'] 			= $data['stage_1'];
			$ArrIl[$i]['glass'] 			= $data['glassconfiguration_'.($i+1)];
			$ArrIl[$i]['thickness_1'] 		= $data['thickness1_'.($i+1)];
			$ArrIl[$i]['thickness_2'] 		= $data['thickness2_'.($i+1)];
			$ArrIl[$i]['glass_length'] 		= $data['glasslength_1'];
			$ArrIl[$i]['weight_veil'] 		= $data['veil_weight_'.($i+1)];
			$ArrIl[$i]['weight_csm'] 		= $data['csm_weight_'.($i+1)];
			$ArrIl[$i]['weight_wr'] 		= $data['wr_weight_'.($i+1)];
		  }
		  for ($i=0; $i < $no_ol; $i++) {
			$ArrOl[$i]['id_product']		= $kode_product;
			$ArrOl[$i]['detail_name']		= 'Outside Lamination';
			$ArrOl[$i]['lapisan'] 			= $data['o_lapisan_'.($i+1)];
			$ArrOl[$i]['std_glass'] 		= $data['o_std_'.($i+1)];
			$ArrOl[$i]['width'] 			= $data['o_width_'.($i+1)];
			$ArrOl[$i]['stage'] 			= $data['o_stage_ke_'.($i+1)];
			$ArrOl[$i]['glass'] 			= $data['o_glassconfiguration_'.($i+1)];
			$ArrOl[$i]['thickness_1'] 		= $data['o_thickness1_'.($i+1)];
			$ArrOl[$i]['thickness_2'] 		= $data['o_thickness2_'.($i+1)];
			if (isset($data['o_glasslength_'.($i+1)]) && !empty($data['o_glasslength_'.($i+1)])) {
			  $ArrOl[$i]['glass_length'] 	= $data['o_glasslength_'.($i+1)];
			}else {
			  $ArrOl[$i]['glass_length'] 	= 0;
			}
			$ArrOl[$i]['weight_veil'] 		= $data['o_veil_weight_'.($i+1)];
			$ArrOl[$i]['weight_csm'] 		= $data['o_csm_weight_'.($i+1)];
			$ArrOl[$i]['weight_wr'] 		= $data['o_wr_weight_'.($i+1)];
		  }
		  
		  //ADD MATERIAL ADD ARWANT 19 12 2019
		if(!empty($data['ListAdd_Resin'])){
			$LA_Resin = $data['ListAdd_Resin'];
			$ArrResinAdd = array();
			foreach($LA_Resin AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();
				
				$ArrResinAdd[$val]['id_product'] 	= $kode_product;
				$ArrResinAdd[$val]['detail_name'] 	= $valx['detail_name'];
				$ArrResinAdd[$val]['id_category'] 	= $valx['id_category'];
				$ArrResinAdd[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrResinAdd[$val]['id_material'] 	= $valx['id_material'];
				$ArrResinAdd[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrResinAdd[$val]['containing'] 	= 0;
					$perseM							= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrResinAdd[$val]['perse'] 		= $perseM;
				$ArrResinAdd[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrResinAdd);
		}

		$this->db->trans_start();
			$this->db->insert('component_header', $ArrHeader);
			$this->db->insert_batch('component_detail', $glass);
			$this->db->insert_batch('component_detail', $resinnadd);
			$this->db->insert_batch('component_lamination', $ArrIl);
			$this->db->insert_batch('component_lamination', $ArrOl);
			
			if(!empty($data['ListAdd_Resin'])){
				$this->db->insert_batch('component_detail_add', $ArrResinAdd);
			}
		$this->db->trans_complete();

		  if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
			  'pesan'		=>'Add Calculation failed. Please try again later ...',
			  'status'	=> 2
			);
		  }
		  else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
			  'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
			  'status'	=> 1
			);
			update_berat_est($kode_product);
			history('Add estimation code '.$kode_product);
		  }
		}
		echo json_encode($Arr_Kembali);
	  }
	  else{
		//List Dropdown
		$ListProduct				= $this->db->query("SELECT * FROM product WHERE parent_product='branch joint' AND deleted='N'")->result_array();
		$ListILamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='INSIDE LAMINATION'")->result_array();
		$ListOLamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION'")->result_array();
		$ListResinSystem			= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
		$ListPressure				= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
		$ListLiner					= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

		$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

		$ListCriminalBarier			= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
		$ListAplikasiProduct		= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
		$ListVacumRate				= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
		$ListDesignLife				= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

		$ListCustomer				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
		$ListCustomer2				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

		//Realease Agent Sementara Sama dengan Plastic Firm
		$List_Realese				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
		$List_PlasticFirm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();

		$List_Veil					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
		$List_Resin					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
		$List_MatCsm				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();

		$List_MatKatalis			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
		$List_MatSm					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
		$List_MatCobalt				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		$List_MatDma				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		$List_MatHydo				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		$List_MatMethanol			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		$List_MatAdditive			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatWR					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
		$List_MatRooving			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
		$List_MatColor				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
		$List_MatTinuvin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatChl				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatWax				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatMchl				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

		$dataStandart				= $this->db->query("SELECT * FROM identitas")->result_array();
		$data = array(
		  'title'					=> 'Estimation Branch Joint',
		  'action'					=> 'branchjoint',
		  'product'					=> $ListProduct,
		  'resin_system'			=> $ListResinSystem,
		  'pressure'				=> $ListPressure,
		  'liner'					=> $ListLiner,
		  'series'					=> $ListSeries,

		  'ILamination'				=> $ListILamination,
		  'OLamination'				=> $ListOLamination,

		  'criminal_barier'			=> $ListCriminalBarier,
		  'aplikasi_product'		=> $ListAplikasiProduct,
		  'vacum_rate'				=> $ListVacumRate,
		  'design_life'				=> $ListDesignLife,
		  'standard'				=> $ListCustomer,
		  'customer'				=> $ListCustomer2,

		  'ListRealise'				=> $List_Realese,
		  'ListPlastic'				=> $List_PlasticFirm,
		  'ListVeil'				=> $List_Veil,
		  'ListResin'				=> $List_Resin,
		  'ListMatCsm'				=> $List_MatCsm,

		  'ListMatKatalis'			=> $List_MatKatalis,
		  'ListMatSm'				=> $List_MatSm,
		  'ListMatCobalt'			=> $List_MatCobalt,
		  'ListMatDma'				=> $List_MatDma,
		  'ListMatHydo'				=> $List_MatHydo,
		  'ListMatMethanol'			=> $List_MatMethanol,
		  'ListMatAdditive'			=> $List_MatAdditive,

		  'ListMatWR'				=> $List_MatWR,
		  'ListMatRooving'			=> $List_MatRooving,

		  'ListMatColor'			=> $List_MatColor,
		  'ListMatTinuvin'			=> $List_MatTinuvin,
		  'ListMatChl'				=> $List_MatChl,
		  'ListMatStery'			=> $List_MatSm,
		  'ListMatWax'				=> $List_MatWax,
		  'ListMatMchl'				=> $List_MatMchl
		);

		$this->load->view('Component/est/branchjoint', $data);
	  }
	}
	//fieldjoint
	public function fieldjoint(){
	  if($this->input->post()){
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$mY						=  date('ym');
		$ListDetail_Glass		= $data['glass'];
		$ListDetail_resinnadd	= $data['resinnadd'];
		//print_r($ListDetail_Glass);
		$glass 		= array();
		$resinnadd 	= array();
		$count 		= 0;
		//echo $ListDetail_Glass['id_material'][0];

		$ArrDet1 = array();
		$ArrIl	= array();
		$ArrOl 	= array();
		$no_il 	= $data['no_il'];
		$no_ol	= $data['no_ol'];

		//pengurutan kode
		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter_1		= $data['diameter_1'];

		$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
		$KdPressure		= sprintf('%02s',$pressure);
		$KdDiameter_1	= sprintf('%04s',$diameter_1);
		$KdLiner		= $liner;
		$KDCust			= $data['cust'];

		$ket_plus		= "";
		$ket_plus2		= "";
		if(!empty($data['ket_plus'])){
			$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
			$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
		}

		if ($KDCust == 'C100-1903000' || empty($KDCust)) {
			$kode_product	= "FJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner.$ket_plus;
			$KDCust			= "";
		}else {
			$kode_product	= "FJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner."-".$KDCust.$ket_plus;
			$KDCust			= $data['cust'];
		}
		
		// echo $kode_product; exit;
		$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
		$NumRow			= $this->db->query($srcType)->num_rows();

		if($NumRow > 0){
		  $Arr_Kembali	= array(
			'pesan'		=>'Specifications are already in the list. Check again ...',
			'status'	=> 3
		  );
		}
		else{

		  $DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
		  $DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		  $ArrHeader	= array(
			'id_product'			=> $kode_product,
			'cust'					=> $KDCust,
			'ket_plus'				=> $ket_plus2,
			'parent_product'		=> 'field joint',
			'nm_product'			=> $data['top_type_1'],
			'series'				=> $data['series'],
			'resin_sistem'			=> $DataSeries[0]['resin_system'],
			'pressure'				=> $DataSeries[0]['pressure'],
			'diameter'				=> $data['diameter_1'],
			'liner'					=> $DataSeries[0]['liner'],
			'standart_by'			=> $data['top_toleran'],
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],
			'panjang'				=> $data['minimum_width'],
			'waste'					=> $data['waste'],
			'pipe_thickness'		=> $data['pipe_thickness'],
			'joint_thickness'		=> $data['joint_thickness'],
			'factor_thickness'		=> $data['factor_thickness'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		  );

		  // print_r($ArrHeader); exit;
		  foreach ($ListDetail_Glass as $key => $value) {
			foreach ($value as $k => $val) {
			  $idm = $ListDetail_Glass['id_material'][$k];
			  $dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->result_array();

			  $glass[$k][$key] 				= $val;
			  $glass[$k]['id_product'] 		= $kode_product;
			  //$glass[$k]['detail_name'] 	= $data['detail_name'];
			  //$glass[$k]['acuhan'] 		= $data['acuhan_1'];
			  //$glass[$k]['id_category'] 	= $dataMaterial[0]['id_category'];
			  $glass[$k]['nm_category'] 	= $ListDetail_resinnadd['nm_category'][$k];//$dataMaterial[0]['nm_category'];
			  //$glass[$k]['id_material'] 	= $valx['id_material'];
			  $glass[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
			  
				$valueM						= (!empty($ListDetail_Glass['value'][$k]))?$ListDetail_Glass['value'][$k]:'';
			  $glass[$k]['value'] 			= $valueM;
				$thicknessM					= (!empty($ListDetail_Glass['thickness'][$k]))?$ListDetail_Glass['thickness'][$k]:'';
			  $glass[$k]['thickness'] 		= $thicknessM;
				$pengaliM					= (!empty($ListDetail_Glass['fak_pengali'][$k]))?$ListDetail_Glass['fak_pengali'][$k]:'';
			  $glass[$k]['fak_pengali'] 	= $pengaliM;
				$bwM						= (!empty($ListDetail_Glass['bw'][$k]))?$ListDetail_Glass['bw'][$k]:'';
			  $glass[$k]['bw'] 				= $bwM;
				$jumlahM					= (!empty($ListDetail_Glass['jumlah'][$k]))?$ListDetail_Glass['jumlah'][$k]:'';
			  $glass[$k]['jumlah'] 			= $jumlahM;
				$layerM						= (!empty($ListDetail_Glass['layer'][$k]))?$ListDetail_Glass['layer'][$k]:'';
			  $glass[$k]['layer'] 			= $layerM;;
				$containingM				= (!empty($ListDetail_Glass['containing'][$k]))?$ListDetail_Glass['containing'][$k]:'';
			  $glass[$k]['containing'] 		= $containingM;
				$total_thicknessM			= (!empty($ListDetail_Glass['total_thickness'][$k]))?$ListDetail_Glass['total_thickness'][$k]:'';
			  $glass[$k]['total_thickness'] = $total_thicknessM;
				$lastfullM					= (!empty($ListDetail_Glass['last_full'][$k]))?$ListDetail_Glass['last_full'][$k]:'';
			  $glass[$k]['last_full'] 		= $lastfullM;
				$lastcostM					= (!empty($ListDetail_Glass['last_cost'][$k]))?$ListDetail_Glass['last_cost'][$k]:'';
			  $glass[$k]['last_cost'] 		= $lastcostM;
			}

		  }
		  foreach ($ListDetail_resinnadd as $key => $value) {
			foreach ($value as $k => $val) {
			  $idm = $ListDetail_resinnadd['id_material'][$k];
			  $dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->result_array();

			  $resinnadd[$k][$key] = $val;
			  $resinnadd[$k]['id_product'] 	= $kode_product;
			  //$resinnadd[$k]['detail_name'] 	= $data['detail_name'];
			  //$resinnadd[$k]['acuhan'] 		= $data['acuhan_1'];
			  //$resinnadd[$k]['id_category'] 	= $dataMaterial[0]['id_category'];
			  $resinnadd[$k]['nm_category'] 	= $ListDetail_resinnadd['nm_category'][$k];
			  //$resinnadd[$k]['id_material'] 	= $valx['id_material'];
			  $resinnadd[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($ListDetail_resinnadd['value'][$k]))?$ListDetail_resinnadd['value'][$k]:'';
			  $resinnadd[$k]['value'] 			= $valueM;
				$thicknessM						= (!empty($ListDetail_resinnadd['thickness'][$k]))?$ListDetail_resinnadd['thickness'][$k]:'';
			  $resinnadd[$k]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($ListDetail_resinnadd['fak_pengali'][$k]))?$ListDetail_resinnadd['fak_pengali'][$k]:'';
			  $resinnadd[$k]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($ListDetail_resinnadd['bw'][$k]))?$ListDetail_resinnadd['bw'][$k]:'';
			  $resinnadd[$k]['bw'] 				= $bwM;
				$jumlahM						= (!empty($ListDetail_resinnadd['jumlah'][$k]))?$ListDetail_resinnadd['jumlah'][$k]:'';
			  $resinnadd[$k]['jumlah'] 			= $jumlahM;
				$layerM							= (!empty($ListDetail_resinnadd['layer'][$k]))?$ListDetail_resinnadd['layer'][$k]:'';
			  $resinnadd[$k]['layer'] 			= $layerM;;
				$containingM					= (!empty($ListDetail_resinnadd['containing'][$k]))?$ListDetail_resinnadd['containing'][$k]:'';
			  $resinnadd[$k]['containing'] 		= $containingM;
				$total_thicknessM				= (!empty($ListDetail_resinnadd['total_thickness'][$k]))?$ListDetail_resinnadd['total_thickness'][$k]:'';
			  $resinnadd[$k]['total_thickness'] = $total_thicknessM;
				$lastfullM						= (!empty($ListDetail_resinnadd['last_full'][$k]))?$ListDetail_resinnadd['last_full'][$k]:'';
			  $resinnadd[$k]['last_full'] 		= $lastfullM;
				$lastcostM						= (!empty($ListDetail_resinnadd['last_cost'][$k]))?$ListDetail_resinnadd['last_cost'][$k]:'';
			  $resinnadd[$k]['last_cost'] 		= $lastcostM;
			}

		  }
		  for ($i=0; $i < $no_il; $i++) {
			$ArrIl[$i]['id_product']		= $kode_product;
			$ArrIl[$i]['detail_name']		= 'Inside Lamination';
			$ArrIl[$i]['lapisan'] 			= $data['lapisan_'.($i+1)];
			$ArrIl[$i]['std_glass'] 		= $data['std_'.($i+1)];
			$ArrIl[$i]['width'] 			= $data['width_'.($i+1)];
			$ArrIl[$i]['stage'] 			= $data['stage_1'];
			$ArrIl[$i]['glass'] 			= $data['glassconfiguration_'.($i+1)];
			$ArrIl[$i]['thickness_1'] 		= $data['thickness1_'.($i+1)];
			$ArrIl[$i]['thickness_2'] 		= $data['thickness2_'.($i+1)];
			$ArrIl[$i]['glass_length'] 		= $data['glasslength_1'];
			$ArrIl[$i]['weight_veil'] 		= $data['veil_weight_'.($i+1)];
			$ArrIl[$i]['weight_csm'] 		= $data['csm_weight_'.($i+1)];
			$ArrIl[$i]['weight_wr'] 		= $data['wr_weight_'.($i+1)];
		  }
		  for ($i=0; $i < $no_ol; $i++) {
			$ArrOl[$i]['id_product']		= $kode_product;
			$ArrOl[$i]['detail_name']		= 'Outside Lamination';
			$ArrOl[$i]['lapisan'] 			= $data['o_lapisan_'.($i+1)];
			$ArrOl[$i]['std_glass'] 		= $data['o_std_'.($i+1)];
			$ArrOl[$i]['width'] 			= $data['o_width_'.($i+1)];
			$ArrOl[$i]['stage'] 			= $data['o_stage_ke_'.($i+1)];
			$ArrOl[$i]['glass'] 			= $data['o_glassconfiguration_'.($i+1)];
			$ArrOl[$i]['thickness_1'] 		= $data['o_thickness1_'.($i+1)];
			$ArrOl[$i]['thickness_2'] 		= $data['o_thickness2_'.($i+1)];
			if (isset($data['o_glasslength_'.($i+1)]) && !empty($data['o_glasslength_'.($i+1)])) {
			  $ArrOl[$i]['glass_length'] 	= $data['o_glasslength_'.($i+1)];
			}else {
			  $ArrOl[$i]['glass_length'] 	= 0;
			}
			$ArrOl[$i]['weight_veil'] 		= $data['o_veil_weight_'.($i+1)];
			$ArrOl[$i]['weight_csm'] 		= $data['o_csm_weight_'.($i+1)];
			$ArrOl[$i]['weight_wr'] 		= $data['o_wr_weight_'.($i+1)];
		  }
			
			//ADD MATERIAL ADD ARWANT 19 12 2019
		if(!empty($data['ListAdd_Resin'])){
			$LA_Resin = $data['ListAdd_Resin'];
			$ArrResinAdd = array();
			foreach($LA_Resin AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();
				
				$ArrResinAdd[$val]['id_product'] 	= $kode_product;
				$ArrResinAdd[$val]['detail_name'] 	= $valx['detail_name'];
				$ArrResinAdd[$val]['id_category'] 	= $valx['id_category'];
				$ArrResinAdd[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrResinAdd[$val]['id_material'] 	= $valx['id_material'];
				$ArrResinAdd[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrResinAdd[$val]['containing'] 	= 0;
					$perseM							= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrResinAdd[$val]['perse'] 		= $perseM;
				$ArrResinAdd[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrResinAdd);
		}


		$this->db->trans_start();
			$this->db->insert('component_header', $ArrHeader);
			$this->db->insert_batch('component_detail', $glass);
			$this->db->insert_batch('component_detail', $resinnadd);
			$this->db->insert_batch('component_lamination', $ArrIl);
			$this->db->insert_batch('component_lamination', $ArrOl);
			
			if(!empty($data['ListAdd_Resin'])){
				$this->db->insert_batch('component_detail_add', $ArrResinAdd);
			}
		$this->db->trans_complete();

		  if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
			  'pesan'		=>'Add Calculation failed. Please try again later ...',
			  'status'	=> 2
			);
		  }
		  else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
			  'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
			  'status'	=> 1
			);
			update_berat_est($kode_product);
			history('Add estimation code '.$kode_product);
		  }
		}
		echo json_encode($Arr_Kembali);
	  }
	  else{
		//List Dropdown
		$ListProduct				= $this->db->query("SELECT * FROM product WHERE parent_product='field joint' AND deleted='N'")->result_array();
		$ListILamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='INSIDE LAMINATION'")->result_array();
		$ListOLamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION'")->result_array();
		$ListResinSystem			= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
		$ListPressure				= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
		$ListLiner					= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

		$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

		$ListCriminalBarier			= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
		$ListAplikasiProduct		= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
		$ListVacumRate				= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
		$ListDesignLife				= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

		$ListCustomer				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
		$ListCustomer2				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

		//Realease Agent Sementara Sama dengan Plastic Firm
		$List_Realese				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
		$List_PlasticFirm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();

		$List_Veil					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
		$List_Resin					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
		$List_MatCsm				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();

		$List_MatKatalis			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
		$List_MatSm					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
		$List_MatCobalt				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		$List_MatDma				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		$List_MatHydo				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		$List_MatMethanol			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		$List_MatAdditive			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatWR					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
		$List_MatRooving			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
		$List_MatColor				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
		$List_MatTinuvin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatChl				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatWax				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatMchl				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

		$dataStandart				= $this->db->query("SELECT * FROM identitas")->result_array();
		$data = array(
		  'title'					=> 'Estimation Field Joint',
		  'action'					=> 'fieldjoint',
		  'product'					=> $ListProduct,
		  'resin_system'			=> $ListResinSystem,
		  'pressure'				=> $ListPressure,
		  'liner'					=> $ListLiner,
		  'series'					=> $ListSeries,

		  'ILamination'				=> $ListILamination,
		  'OLamination'				=> $ListOLamination,

		  'criminal_barier'			=> $ListCriminalBarier,
		  'aplikasi_product'		=> $ListAplikasiProduct,
		  'vacum_rate'				=> $ListVacumRate,
		  'design_life'				=> $ListDesignLife,
		  'standard'				=> $ListCustomer,
		  'customer'				=> $ListCustomer2,

		  'ListRealise'				=> $List_Realese,
		  'ListPlastic'				=> $List_PlasticFirm,
		  'ListVeil'				=> $List_Veil,
		  'ListResin'				=> $List_Resin,
		  'ListMatCsm'				=> $List_MatCsm,

		  'ListMatKatalis'			=> $List_MatKatalis,
		  'ListMatSm'				=> $List_MatSm,
		  'ListMatCobalt'			=> $List_MatCobalt,
		  'ListMatDma'				=> $List_MatDma,
		  'ListMatHydo'				=> $List_MatHydo,
		  'ListMatMethanol'			=> $List_MatMethanol,
		  'ListMatAdditive'			=> $List_MatAdditive,

		  'ListMatWR'				=> $List_MatWR,
		  'ListMatRooving'			=> $List_MatRooving,

		  'ListMatColor'			=> $List_MatColor,
		  'ListMatTinuvin'			=> $List_MatTinuvin,
		  'ListMatChl'				=> $List_MatChl,
		  'ListMatStery'			=> $List_MatSm,
		  'ListMatWax'				=> $List_MatWax,
		  'ListMatMchl'				=> $List_MatMchl
		);

		$this->load->view('Component/est/fieldjoint', $data);
	  }
	}
	//shopjoint
	public function shopjoint(){
	  if($this->input->post()){
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$mY						= date('ym');
		$ListDetail_Glass		= $data['glass'];
		$ListDetail_resinnadd	= $data['resinnadd'];
		//print_r($ListDetail_Glass);
		$glass 		= array();
		$resinnadd 	= array();
		$count 		= 0;
		//echo $ListDetail_Glass['id_material'][0];

		$ArrDet1 	= array();
		$ArrIl 		= array();
		$ArrOl 		= array();
		$no_il 		= $data['no_il'];
		$no_ol 		= $data['no_ol'];

		//pengurutan kode
		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter_1		= $data['diameter_1'];

		$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
		$KdPressure		= sprintf('%02s',$pressure);
		$KdDiameter_1	= sprintf('%04s',$diameter_1);
		$KdLiner		= $liner;
		$KDCust			= $data['cust'];

		$ket_plus		= "";
		$ket_plus2		= "";
		if(!empty($data['ket_plus'])){
			$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
			$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
		}

		if ($KDCust == 'C100-1903000' || empty($KDCust)) {
			$kode_product	= "SJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner.$ket_plus;
			$KDCust			= "";
		}else {
			$kode_product	= "SJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner."-".$KDCust.$ket_plus;
			$KDCust			= $data['cust'];
		}
		// echo $kode_product; exit;
		$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
		$NumRow			= $this->db->query($srcType)->num_rows();

		if($NumRow > 0){
		  $Arr_Kembali	= array(
			'pesan'		=>'Specifications are already in the list. Check again ...',
			'status'	=> 3
		  );
		}
		else{

		  $DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
		  $DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		  $ArrHeader	= array(
			'id_product'			=> $kode_product,
			'cust'					=> $KDCust,
			'ket_plus'				=> $ket_plus2,
			'parent_product'		=> 'shop joint',
			'nm_product'			=> $data['top_type_1'],
			'series'				=> $data['series'],
			'waste'					=> $data['waste'],
			'resin_sistem'			=> $DataSeries[0]['resin_system'],
			'pressure'				=> $DataSeries[0]['pressure'],
			'diameter'				=> $data['diameter_1'],
			'liner'					=> $DataSeries[0]['liner'],
			'standart_by'			=> $data['top_toleran'],
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],
			'panjang'				=> $data['minimum_width'],
			'pipe_thickness'		=> $data['pipe_thickness'],
			'joint_thickness'		=> $data['joint_thickness'],
			'factor_thickness'		=> $data['factor_thickness'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		  );

		  // print_r($ArrHeader); exit;
		  foreach ($ListDetail_Glass as $key => $value) {
			foreach ($value as $k => $val) {
			  $idm = $ListDetail_Glass['id_material'][$k];
			  $dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->result_array();

			  $glass[$k][$key] = $val;
			  $glass[$k]['id_product'] 		= $kode_product;
			  //$glass[$k]['detail_name'] 	= $data['detail_name'];
			  //$glass[$k]['acuhan'] 		= $data['acuhan_1'];
			  //$glass[$k]['id_category'] 	= $dataMaterial[0]['id_category'];

			  $glass[$k]['nm_category'] 	= $ListDetail_resinnadd['nm_category'][$k];//$dataMaterial[0]['nm_category'];
			  //$glass[$k]['id_material'] 	= $valx['id_material'];
			  $glass[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];

				$valueM						= (!empty($ListDetail_Glass['value'][$k]))?$ListDetail_Glass['value'][$k]:'';
			  $glass[$k]['value'] 			= $valueM;
				$thicknessM					= (!empty($ListDetail_Glass['thickness'][$k]))?$ListDetail_Glass['thickness'][$k]:'';
			  $glass[$k]['thickness'] 		= $thicknessM;
				$pengaliM					= (!empty($ListDetail_Glass['fak_pengali'][$k]))?$ListDetail_Glass['fak_pengali'][$k]:'';
			  $glass[$k]['fak_pengali'] 	= $pengaliM;
				$bwM						= (!empty($ListDetail_Glass['bw'][$k]))?$ListDetail_Glass['bw'][$k]:'';
			  $glass[$k]['bw'] 				= $bwM;
				$jumlahM					= (!empty($ListDetail_Glass['jumlah'][$k]))?$ListDetail_Glass['jumlah'][$k]:'';
			  $glass[$k]['jumlah'] 			= $jumlahM;
				$layerM						= (!empty($ListDetail_Glass['layer'][$k]))?$ListDetail_Glass['layer'][$k]:'';
			  $glass[$k]['layer'] 			= $layerM;;
				$containingM				= (!empty($ListDetail_Glass['containing'][$k]))?$ListDetail_Glass['containing'][$k]:'';
			  $glass[$k]['containing'] 		= $containingM;
				$total_thicknessM			= (!empty($ListDetail_Glass['total_thickness'][$k]))?$ListDetail_Glass['total_thickness'][$k]:'';
			  $glass[$k]['total_thickness'] = $total_thicknessM;
				$lastfullM					= (!empty($ListDetail_Glass['last_full'][$k]))?$ListDetail_Glass['last_full'][$k]:'';
			  $glass[$k]['last_full'] 		= $lastfullM;
				$lastcostM					= (!empty($ListDetail_Glass['last_cost'][$k]))?$ListDetail_Glass['last_cost'][$k]:'';
			  $glass[$k]['last_cost'] 		= $lastcostM;
			}

		  }
		  foreach ($ListDetail_resinnadd as $key => $value) {
			foreach ($value as $k => $val) {
			  $idm = $ListDetail_resinnadd['id_material'][$k];
			  $dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->result_array();

			  $resinnadd[$k][$key] = $val;
			  $resinnadd[$k]['id_product'] 		= $kode_product;
			  //$resinnadd[$k]['detail_name'] 	= $data['detail_name'];
			  //$resinnadd[$k]['acuhan'] 		= $data['acuhan_1'];
			  //$resinnadd[$k]['id_category'] 	= $dataMaterial[0]['id_category'];
			  $resinnadd[$k]['nm_category'] 	= $ListDetail_resinnadd['nm_category'][$k]; //$dataMaterial[0]['nm_category'];
			  //$resinnadd[$k]['id_material'] 	= $valx['id_material'];
			  $resinnadd[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($ListDetail_resinnadd['value'][$k]))?$ListDetail_resinnadd['value'][$k]:'';
			  $resinnadd[$k]['value'] 			= $valueM;
				$thicknessM						= (!empty($ListDetail_resinnadd['thickness'][$k]))?$ListDetail_resinnadd['thickness'][$k]:'';
			  $resinnadd[$k]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($ListDetail_resinnadd['fak_pengali'][$k]))?$ListDetail_resinnadd['fak_pengali'][$k]:'';
			  $resinnadd[$k]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($ListDetail_resinnadd['bw'][$k]))?$ListDetail_resinnadd['bw'][$k]:'';
			  $resinnadd[$k]['bw'] 				= $bwM;
				$jumlahM						= (!empty($ListDetail_resinnadd['jumlah'][$k]))?$ListDetail_resinnadd['jumlah'][$k]:'';
			  $resinnadd[$k]['jumlah'] 			= $jumlahM;
				$layerM							= (!empty($ListDetail_resinnadd['layer'][$k]))?$ListDetail_resinnadd['layer'][$k]:'';
			  $resinnadd[$k]['layer'] 			= $layerM;;
				$containingM					= (!empty($ListDetail_resinnadd['containing'][$k]))?$ListDetail_resinnadd['containing'][$k]:'';
			  $resinnadd[$k]['containing'] 		= $containingM;
				$total_thicknessM				= (!empty($ListDetail_resinnadd['total_thickness'][$k]))?$ListDetail_resinnadd['total_thickness'][$k]:'';
			  $resinnadd[$k]['total_thickness'] = $total_thicknessM;
				$lastfullM						= (!empty($ListDetail_resinnadd['last_full'][$k]))?$ListDetail_resinnadd['last_full'][$k]:'';
			  $resinnadd[$k]['last_full'] 		= $lastfullM;
				$lastcostM						= (!empty($ListDetail_resinnadd['last_cost'][$k]))?$ListDetail_resinnadd['last_cost'][$k]:'';
			  $resinnadd[$k]['last_cost'] 		= $lastcostM;
			}

		  }
		  for ($i=0; $i < $no_il; $i++) {
			$ArrIl[$i]['id_product']		= $kode_product;
			$ArrIl[$i]['detail_name']		= 'Inside Lamination';
			$ArrIl[$i]['lapisan'] 			= $data['lapisan_'.($i+1)];
			$ArrIl[$i]['std_glass'] 		= $data['std_'.($i+1)];
			$ArrIl[$i]['width'] 			= $data['width_'.($i+1)];
			$ArrIl[$i]['stage'] 			= $data['stage_1'];
			$ArrIl[$i]['glass'] 			= $data['glassconfiguration_'.($i+1)];
			$ArrIl[$i]['thickness_1'] 		= $data['thickness1_'.($i+1)];
			$ArrIl[$i]['thickness_2'] 		= $data['thickness2_'.($i+1)];
			$ArrIl[$i]['glass_length'] 		= $data['glasslength_1'];
			$ArrIl[$i]['weight_veil'] 		= $data['veil_weight_'.($i+1)];
			$ArrIl[$i]['weight_csm'] 		= $data['csm_weight_'.($i+1)];
			$ArrIl[$i]['weight_wr'] 		= $data['wr_weight_'.($i+1)];
		  }
		  for ($i=0; $i < $no_ol; $i++) {
			$ArrOl[$i]['id_product']		= $kode_product;
			$ArrOl[$i]['detail_name']		= 'Outside Lamination';
			$ArrOl[$i]['lapisan'] 			= $data['o_lapisan_'.($i+1)];
			$ArrOl[$i]['std_glass'] 		= $data['o_std_'.($i+1)];
			$ArrOl[$i]['width'] 			= $data['o_width_'.($i+1)];
			$ArrOl[$i]['stage'] 			= $data['o_stage_ke_'.($i+1)];
			$ArrOl[$i]['glass'] 			= $data['o_glassconfiguration_'.($i+1)];
			$ArrOl[$i]['thickness_1'] 		= $data['o_thickness1_'.($i+1)];
			$ArrOl[$i]['thickness_2'] 		= $data['o_thickness2_'.($i+1)];
			if (isset($data['o_glasslength_'.($i+1)]) && !empty($data['o_glasslength_'.($i+1)])) {
			  $ArrOl[$i]['glass_length'] 	= $data['o_glasslength_'.($i+1)];
			}else {
			  $ArrOl[$i]['glass_length'] 	= 0;
			}
			$ArrOl[$i]['weight_veil'] 		= $data['o_veil_weight_'.($i+1)];
			$ArrOl[$i]['weight_csm'] 		= $data['o_csm_weight_'.($i+1)];
			$ArrOl[$i]['weight_wr'] 		= $data['o_wr_weight_'.($i+1)];
		  }
		
		//ADD MATERIAL ADD ARWANT 19 12 2019
		if(!empty($data['ListAdd_Resin'])){
			$LA_Resin = $data['ListAdd_Resin'];
			$ArrResinAdd = array();
			foreach($LA_Resin AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();
				
				$ArrResinAdd[$val]['id_product'] 	= $kode_product;
				$ArrResinAdd[$val]['detail_name'] 	= $valx['detail_name'];
				$ArrResinAdd[$val]['id_category'] 	= $valx['id_category'];
				$ArrResinAdd[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrResinAdd[$val]['id_material'] 	= $valx['id_material'];
				$ArrResinAdd[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrResinAdd[$val]['containing'] 	= 0;
					$perseM							= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrResinAdd[$val]['perse'] 		= $perseM;
				$ArrResinAdd[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrResinAdd);
		}
		
		// exit;
		
		  $this->db->trans_start();
			$this->db->insert('component_header', $ArrHeader);
			$this->db->insert_batch('component_detail', $glass);
			$this->db->insert_batch('component_detail', $resinnadd);
			$this->db->insert_batch('component_lamination', $ArrIl);
			$this->db->insert_batch('component_lamination', $ArrOl);
			
			if(!empty($data['ListAdd_Resin'])){
				$this->db->insert_batch('component_detail_add', $ArrResinAdd);
			}
		  $this->db->trans_complete();

		  if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
			  'pesan'		=>'Add Calculation failed. Please try again later ...',
			  'status'	=> 2
			);
		  }
		  else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
			  'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
			  'status'	=> 1
			);
			update_berat_est($kode_product);
			history('Add estimation shop joint code '.$kode_product);
		  }
		}
		echo json_encode($Arr_Kembali);
	  }
	  else{
		//List Dropdown
		$ListProduct				= $this->db->query("SELECT * FROM product WHERE parent_product='shop joint' AND deleted='N'")->result_array();
		$ListILamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='INSIDE LAMINATION'")->result_array();
		$ListOLamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION'")->result_array();
		$ListResinSystem			= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
		$ListPressure				= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
		$ListLiner					= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

		$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

		$ListCriminalBarier			= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
		$ListAplikasiProduct		= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
		$ListVacumRate				= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
		$ListDesignLife				= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

		$ListCustomer				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
		$ListCustomer2				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

		//Realease Agent Sementara Sama dengan Plastic Firm
		$List_Realese				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
		$List_PlasticFirm			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
		$List_Veil					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
		$List_Resin					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
		$List_MatCsm				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
		$List_MatKatalis			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
		$List_MatSm					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
		$List_MatCobalt				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		$List_MatDma				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		$List_MatHydo				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		$List_MatMethanol			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		$List_MatAdditive			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatWR					= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
		$List_MatRooving			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
		$List_MatColor				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
		$List_MatTinuvin			= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatChl				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatWax				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		$List_MatMchl				= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

		$dataStandart				= $this->db->query("SELECT * FROM identitas")->result_array();
		$data = array(
		  'title'					=> 'Estimation Shop Joint',
		  'action'					=> 'shopjoint',
		  'product'					=> $ListProduct,
		  'resin_system'			=> $ListResinSystem,
		  'pressure'				=> $ListPressure,
		  'liner'					=> $ListLiner,
		  'series'					=> $ListSeries,

		  'ILamination'				=> $ListILamination,
		  'OLamination'				=> $ListOLamination,

		  'criminal_barier'			=> $ListCriminalBarier,
		  'aplikasi_product'		=> $ListAplikasiProduct,
		  'vacum_rate'				=> $ListVacumRate,
		  'design_life'				=> $ListDesignLife,
		  'standard'				=> $ListCustomer,
		  'customer'				=> $ListCustomer2,

		  'ListRealise'				=> $List_Realese,
		  'ListPlastic'				=> $List_PlasticFirm,
		  'ListVeil'				=> $List_Veil,
		  'ListResin'				=> $List_Resin,
		  'ListMatCsm'				=> $List_MatCsm,

		  'ListMatKatalis'			=> $List_MatKatalis,
		  'ListMatSm'				=> $List_MatSm,
		  'ListMatCobalt'			=> $List_MatCobalt,
		  'ListMatDma'				=> $List_MatDma,
		  'ListMatHydo'				=> $List_MatHydo,
		  'ListMatMethanol'			=> $List_MatMethanol,
		  'ListMatAdditive'			=> $List_MatAdditive,

		  'ListMatWR'				=> $List_MatWR,
		  'ListMatRooving'			=> $List_MatRooving,

		  'ListMatColor'			=> $List_MatColor,
		  'ListMatTinuvin'			=> $List_MatTinuvin,
		  'ListMatChl'				=> $List_MatChl,
		  'ListMatStery'			=> $List_MatSm,
		  'ListMatWax'				=> $List_MatWax,
		  'ListMatMchl'				=> $List_MatMchl
		);

		$this->load->view('Component/est/shopjoint', $data);
	  }
	}

	public function get_detail_mat(){
		$table 				= $this->input->get('nm_standard');
		$id_material 	= $this->input->get('id_material');
		$data = $this->db->get_where('raw_material_bq_standard',array('id_material'=>$id_material,'nm_standard'=>$table))->row();

		echo json_encode($data);
	}

	public function getStandartCode(){

		$std = $this->input->post('std');
		$parent_product = $this->input->post('parent_product');

		$stdx 	= '';
		$StNum	= 0;
		if(!empty($std)){
			$stdx	= 'selected';
			$StNum	= 1;
		}

		$sqlSup		= "SELECT * FROM help_default WHERE diameter='".$this->input->post('dim')."' AND product_parent='".$parent_product."' GROUP BY standart_code ORDER BY standart_code ASC";

		// echo $sqlSup;
		$restSup	= $this->db->query($sqlSup)->result_array();
		$restNum	= $this->db->query($sqlSup)->num_rows();

		if($restNum > 0){
			$option	= "<option value='0'>Select An Standart</option>";
			foreach($restSup AS $val => $valx){
				$selx	= ($valx['standart_code'] == $std)?'selected':'';
				$option .= "<option value='".$valx['standart_code']."' ".$selx.">".strtoupper($valx['standart_code'])."</option>";
			}
			$tamp = "Standart Default Berhasil Ditemukan";
			$col= 'green';
		}
		else{
			$option	= "<option value='0'>No Data</option>";
			$tamp = "Standart Default Tidak Ditemukan";
			$col= 'red';
		}

		$ArrJson	= array(
			'option' => $option,
			'tamp' => $tamp,
			'color'	=> $col,
			'StNum'	=> $StNum,
			'StDim'	=> $this->input->post('dim'),
			'PrPdc' => $parent_product,
			'Standart'	=> $std
		);
		echo json_encode($ArrJson);
	}

	public function getDefault(){
		$dim		= $this->input->post("dim");
		$dim2		= $this->input->post("dim2");
		$parent_product		= $this->input->post("parent_product");

		$std		= 'PRODUCT-ORI';
		if(!empty($this->input->post("std"))){
			$std		= $this->input->post("std");
		}
		$tamSql = "";
		if($parent_product == 'reducer tee mould' OR $parent_product == 'reducer tee slongsong' OR $parent_product == 'concentric reducer' OR $parent_product == 'eccentric reducer'){
			$tamSql = " AND diameter2='".$dim2."' ";
		}

		$qDefault	= "SELECT * FROM help_default WHERE standart_code='".$std."' AND diameter = '".$dim."' ".$tamSql." AND product_parent = '".$parent_product."' ";
		// echo $qDefault;

		$getDefault	= $this->db->query($qDefault)->result();
		$ArrJson	= array(
			'waste' 				=> floatval($getDefault[0]->waste),
			'overlap' 				=> floatval($getDefault[0]->overlap),
			'waste_n1' 				=> floatval($getDefault[0]->waste_n1),
			'waste_n2' 				=> floatval($getDefault[0]->waste_n2),
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

			'str_n2_resin_csm' 			=> floatval($getDefault[0]->str_n2_resin_csm),
			'str_n2_resin_csm_add' 		=> floatval($getDefault[0]->str_n2_resin_csm_add),
			'str_n2_resin_wr' 			=> floatval($getDefault[0]->str_n2_resin_wr),
			'str_n2_resin_wr_add' 		=> floatval($getDefault[0]->str_n2_resin_wr_add),
			'str_n2_faktor_csm' 		=> floatval($getDefault[0]->str_n2_faktor_csm),
			'str_n2_faktor_csm_add' 	=> floatval($getDefault[0]->str_n2_faktor_csm_add),
			'str_n2_faktor_wr' 			=> floatval($getDefault[0]->str_n2_faktor_wr),
			'str_n2_faktor_wr_add' 		=> floatval($getDefault[0]->str_n2_faktor_wr_add),
			'str_n2_resin' 				=> floatval($getDefault[0]->str_n2_resin),

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

}
