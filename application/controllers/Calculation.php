<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calculation extends CI_Controller {
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
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');
		$get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM product_header a LEFT JOIN customer b ON b.id_customer=a.customer_real WHERE a.deleted ='N'")->result();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Estimation Product',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Estimation');
		$this->load->view('Calculation/index',$data);
	}
	
	public function pipe(){
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
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetailPlus);
			// exit; 
			
			//pengurutan kode
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
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
			
			
			// print_r($ArrHeader); exit;
		
			//Detail1
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array(); 
			$data = array(
				'title'			=> 'Estimation Pipe',
				'action'		=> 'pipe',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/pipe', $data);
		}
	}
	
	//pipeslongsong
	public function pipeslongsong(){
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> str_replace(',', '', $data['top_length']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Pipe Slongsong',
				'action'		=> 'pipeslongsong',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/pipeslongsong', $data);
		}
	}
	
	//flangemould
	public function flangemould(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');
			
			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail2_neck1	= $data['ListDetail2_neck1'];
			$ListDetail2_neck2	= $data['ListDetail2_neck1'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus2_neck1	= $data['ListDetailPlus2_neck1'];
			$ListDetailPlus2_neck2	= $data['ListDetailPlus2_neck2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'flange mould',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> "",
				
				'panjang_neck_1'		=> $data['panjang_neck_1'],
				'panjang_neck_2'		=> $data['panjang_neck_2'],
				'design_neck_1'			=> $data['design_neck_1'],
				'design_neck_2'			=> $data['design_neck_2'],
				'est_neck_1'			=> $data['est_neck_1'],
				'est_neck_2'			=> $data['est_neck_2'],
				'area_neck_1'			=> $data['area'],
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
				'area'					=> $data['area_neck_1'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
			}
			// print_r($ArrDetail2_neck2);
			
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
			// print_r($ArrFooter3);
			
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail2_neck1);
				$this->db->insert_batch('product_detail', $ArrDetail2_neck2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2_neck1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2_neck2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter2_neck1);
				$this->db->insert('product_footer', $ArrFooter2_neck2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_strukture_neck1 != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2_neck1);
				}
				if($numberMax_strukture_neck2 != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2_neck2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='flange mould' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Flange Mould',
				'action'		=> 'flangemould',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/flangemould', $data);
		}
	}
	
	//flangeslongsong
	public function flangeslongsong(){
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> str_replace(',', '', $data['top_length']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Flange Slongsong',
				'action'		=> 'flangeslongsong',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/flangeslongsong', $data);
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'elbow mould',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> 0,
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'type_elbow'			=> $data['type_elbow'],
				'angle'					=> $data['angle'],
				'waste'					=> $data['waste'],
				'radius'				=> $data['radius'],
				'area'					=> $data['area'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='elbow mould' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
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
				'title'			=> 'Estimation Elbow Mould',
				'action'		=> 'elbowmould',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/elbowmould', $data);
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'elbow mitter',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> $data['panjang'],
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'type_elbow'			=> $data['type_elbow'],
				'angle'					=> $data['angle'],
				'waste'					=> $data['waste'],
				'radius'				=> $data['radius'],
				'area'					=> $data['area'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='elbow mitter' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
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
				'title'			=> 'Estimation Elbow Mitter',
				'action'		=> 'elbowmitter',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/elbowmitter', $data); 
		}
	}
	
	//equalteemould
	public function equalteemould(){
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> str_replace(',', '', $data['top_length']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Equal Tee Mould',
				'action'		=> 'equalteemould',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/equalteemould', $data);
		}
	}
	
	//equalteeslongsong
	public function equalteeslongsong(){
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> str_replace(',', '', $data['top_length']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Equal Tee Slongsong',
				'action'		=> 'equalteeslongsong',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/equalteeslongsong', $data);
		}
	}
	
	//reducerteemould
	public function reducerteemould(){
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> str_replace(',', '', $data['top_length']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Reducer Tee Mould',
				'action'		=> 'reducerteemould',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/reducerteemould', $data);
		}
	}
	
	//reduceteeslongsong
	public function reduceteeslongsong(){
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> str_replace(',', '', $data['top_length']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Reduce Tee Slongsong',
				'action'		=> 'reduceteeslongsong',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/reduceteeslongsong', $data);
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'concentric reducer',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'diameter2'				=> str_replace(',', '', $data['diameter2']),
				'panjang'				=> str_replace(',', '', $data['panjang']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Estimation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='concentric reducer' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
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
			
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Concentric Reducer',
				'action'		=> 'concentricreducer',
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/concentricreducer', $data);
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'eccentric reducer',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'diameter2'				=> str_replace(',', '', $data['diameter2']),
				'panjang'				=> str_replace(',', '', $data['panjang']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='eccentric reducer' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Eccentric Reducer',
				'action'		=> 'eccentricreducer',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/eccentricreducer', $data);
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
			// print_r($ListDetailPlus);
			// exit; 
			
			//pengurutan kode
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'end cap',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> 0,
				'design'				=> $data['top_tebal_design'],
				'radius'				=> $data['radius'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'area'					=> $data['area'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='end cap' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
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
				'title'			=> 'Estimation End Cap',
				'action'		=> 'endcap',
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/endcap', $data);
		}
	}
	
	//blindflange
	public function blindflange(){
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
			$srcType		= "SELECT MAX(id_product) as maxP FROM product_header WHERE id_product LIKE 'PDK-".$mY."%' ";
			$numrowPlant	= $this->db->query($srcType)->num_rows();
			$resultPlant	= $this->db->query($srcType)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_product	= "PDK-".$mY.$urut2;
			// echo $srcType;
			// echo $kode_product; exit;
			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'nm_product'			=> $data['top_type'],
				'parent_product'		=> 'pipe',
				'aplikasi_product'		=> $data['top_app'],
				'id_customer'			=> $data['top_toleran'],
				'customer_real'			=> $data['customer'],
				'standart_toleransi'	=> $DataCust[0]['nm_customer'],
				'diameter'				=> str_replace(',', '', $data['top_diameter']),
				'panjang'				=> str_replace(',', '', $data['top_length']),
				'design'				=> $data['top_tebal_design'],
				'est'					=> $data['top_tebal_est'],
				'min_toleransi'			=> $data['top_min_toleran'],
				'max_toleransi'			=> $data['top_max_toleran'],
				'waste'					=> $data['waste'],
				'created_by'			=> $data_session['ORI_User']['username'],
				'created_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
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
				$this->db->insert('product_header', $ArrHeader);
				$this->db->insert_batch('product_detail', $ArrDetail1);
				$this->db->insert_batch('product_detail', $ArrDetail2);
				$this->db->insert_batch('product_detail', $ArrDetail13);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('product_detail_plus', $ArrDetailPlus4);
				$this->db->insert('product_footer', $ArrFooter);
				$this->db->insert('product_footer', $ArrFooter2);
				$this->db->insert('product_footer', $ArrFooter3);
				if($numberMax_liner != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd1);
				}
				if($numberMax_strukture != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd2);
				}
				if($numberMax_external != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd3);
				}
				if($numberMax_topcoat != 0){
					$this->db->insert_batch('product_detail_add', $ArrDataAdd4);
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
				history('Add Calculation '.$data['top_app']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();
			
			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			// $List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0009' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
			
			
			$List_Veil			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
			
			$List_MatKatalis	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			//Sementara SM sama SOlvet Sama TYP-0024
			// $List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0010' ORDER BY nm_material ASC")->result_array();
			$List_MatSm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Coblat Sama dengan Accelator TYP-0021
			// $List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0012' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara MAT DMA Sama dengan Accelator TYP-0021
			// $List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0011' ORDER BY nm_material ASC")->result_array();
			$List_MatDma		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			
			//Sementara Hydo sama Dengan Inhibitor TYP-0026
			// $List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0013' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			
			$List_MatMethanol	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			$List_MatWR			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			
			$List_MatColor		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin	= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			
			//Cholofoarm sama dengan catalys (baru belum ditanyakan)
			// $List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0016' ORDER BY nm_material ASC")->result_array();
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			
			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0018' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0017' ORDER BY nm_material ASC")->result_array();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Blind Flange',
				'action'		=> 'blindflange',
				// 'standard'		=> $dataStandart,
				'product'		=> $ListProduct,
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
				
			$this->load->view('Calculation/blindflange', $data);
		}
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
		// echo $resinutama;
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
				$bw			= floatval(($weight >= '2200')?'160':(($weight < '2000')?'100':'0'));
				$jumRoov	= floatval(($weight >= '2200')?'62':(($weight < '2000')?'42':'0'));    
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
			$NamePipe		= $restD[0]['nm_product'];
		}

		$ArrJson	= array(
			'pipeD' => $DiamaterPipe,
			'pipeN'	=> $NamePipe
		);
		echo json_encode($ArrJson);
	}
	
	public function modalDetail(){
		$this->load->view('Calculation/modalDetail');
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
	
	function hapus(){
		$id_produk 	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;	
		$Arr_Edit	= array(
			'deleted' => '1',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
		$this->db->where('id_product', $id_produk);
		$this->db->update('product_header', $Arr_Edit);
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
	
	//concentricreducer
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
}