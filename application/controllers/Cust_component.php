<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cust_component extends CI_Controller {
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

			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd1	= $data['ListDetailAdd_Liner'];
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd2	= $data['ListDetailAdd_Strukture'];
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd3	= $data['ListDetailAdd_External'];
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd4	= $data['ListDetailAdd_TopCoat'];
			}
			
			
			$kd_spc = $data['component_list']; 
			// exit;
			$KodeProduct	= $this->db->query("SELECT kode FROM product_parent WHERE product_parent='".$kd_spc."' LIMIT 1 ")->result_array();
			
			

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= trim(str_replace(',','',$data['diameter']));
			$diameter2		= trim(str_replace(',','',$data['diameter2']));
			$cust			= (!empty($data['cust']))?"-".$data['cust']:'C100-1903000';
			$cust2			= (!empty($data['cust']))?$data['cust']:'C100-1903000';

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdDiameter2	= sprintf('%04s',$diameter2);
			$KdLiner		= $liner;
			
			$ket_plus		= "";
			$ket_plus2		= "";
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}

			$kode_product	= $KodeProduct[0]['kode']."-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$cust.$ket_plus;
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
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'parent_product'		=> $data['component_list'],
					'nm_product'			=> ucwords($data['component_list'])." ".$KdDiameter." x ".$KdDiameter2,
					'series'				=> $data['series'],
					'cust'					=> $cust2,
					'standart_by'			=> $cust2,
					'ket_plus'				=> $ket_plus2,
					'standart_code'			=> '',
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],
					'liner'					=> $DataSeries[0]['liner'],

					'diameter'				=> $data['diameter'],
					'diameter2'				=> $data['diameter2'],
					'design'				=> $data['design'],
					
					'created_by'			=> $data_session['ORI_User']['username'],
					'created_date'			=> date('Y-m-d H:i:s')
				);
				
				// print_r($ArrHeader);
				// exit;

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
					$ArrDetail1[$val]['acuhan'] 		= '';
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
					$ArrDetail1[$val]['last_full'] 		= '';
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
					$ArrDetail2[$val]['acuhan'] 		= '';
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
					$ArrDetail2[$val]['last_full'] 		= '';
					$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail2);
				// exit;
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
					$ArrDetail13[$val]['acuhan'] 		= '';
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
					$ArrDetail13[$val]['last_full'] 		= '';
					$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetail13);
				// exit;
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
					$ArrDetailPlus1[$val]['last_full'] 		= '';
					$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus1);
				// exit;
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
					$ArrDetailPlus2[$val]['last_full'] 		= '';
					$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus2);
				// exit;
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
					$ArrDetailPlus3[$val]['last_full'] 		= '';
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
					$ArrDetailPlus4[$val]['last_full'] 		= '';
					$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
				}
				// print_r($ArrDetailPlus4);

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
						$ArrDataAdd1[$val]['last_full'] 	= '';
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
						$ArrDataAdd2[$val]['last_full'] 	= '';
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
						$ArrDataAdd3[$val]['last_full'] 	= '';
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
						$ArrDataAdd4[$val]['last_full'] 	= '';
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
					// $this->db->insert('component_default', $ArrDefault);
					$this->db->insert_batch('component_detail', $ArrDetail1);
					$this->db->insert_batch('component_detail', $ArrDetail2);
					$this->db->insert_batch('component_detail', $ArrDetail13);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
					$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
					// $this->db->insert_batch('component_time', $ArrTiming);
					// $this->db->insert('component_footer', $ArrFooter);
					// $this->db->insert('component_footer', $ArrFooter2);
					// $this->db->insert('component_footer', $ArrFooter3);
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
					history('Add estimation '.$kd_spc.' code : '.$kode_product);
				}
			}


			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product_parent WHERE type2 = 'custom' ORDER BY product_parent ASC")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			// $ListSeries			= $this->db->query("SELECT SUBSTRING_INDEX(kode_group, '-', 1) AS seriesx FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY SUBSTRING_INDEX(kode_group, '-', 1) ORDER BY resin_system ASC, pressure ASC, liner ASC")->result_array();
			$ListSeries			= $this->db->query("SELECT kode_group AS seriesx FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC")->result_array();

			$ListCrimBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListApProduct		= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate		= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife		= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

			$List_Realese		= List_Realese();
			$List_PlasticFirm	= List_PlasticFirm();
			$List_Veil			= List_Veil();
			$List_Resin			= List_Resin();
			$List_MatCsm		= List_MatCsm();
			$List_MatKatalis	= List_MatKatalis();
			$List_MatSm			= List_MatSm();
			$List_MatCobalt		= List_MatCobalt();
			$List_MatDma		= List_MatDma();
			$List_MatHydo		= List_MatHydo();
			$List_MatMethanol	= List_MatMethanol();
			$List_MatAdditive	= List_MatAdditive();
			$List_MatWR			= List_MatWR();
			$List_MatRooving	= List_MatRooving();
			$List_MatColor		= List_MatColor();
			$List_MatTinuvin	= List_MatTinuvin();
			$List_MatChl		= List_MatChl();
			$List_MatWax		= List_MatWax();
			$List_MatMchl		= List_MatMchl();
			
			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'					=> 'Custom Component Estimation',
				'action'				=> 'index',
				'product'				=> $ListProduct,
				'resin_system'			=> $ListResinSystem,
				'pressure'				=> $ListPressure,
				'liner'					=> $ListLiner,
				'series'				=> $ListSeries,
				'criminal_barier'		=> $ListCrimBarier,
				'aplikasi_product'		=> $ListApProduct,
				'vacum_rate'			=> $ListVacumRate,
				'design_life'			=> $ListDesignLife,
				'standard'				=> $ListCustomer,
				'customer'				=> $ListCustomer2,
				'ListRealise'			=> $List_Realese,
				'ListPlastic'			=> $List_PlasticFirm,
				'ListVeil'				=> $List_Veil,
				'ListResin'				=> $List_Resin,
				'ListMatCsm'			=> $List_MatCsm,
				'ListMatKatalis'		=> $List_MatKatalis,
				'ListMatSm'				=> $List_MatSm,
				'ListMatCobalt'			=> $List_MatCobalt,
				'ListMatDma'			=> $List_MatDma,
				'ListMatHydo'			=> $List_MatHydo,
				'ListMatMethanol'		=> $List_MatMethanol,
				'ListMatAdditive'		=> $List_MatAdditive,
				'ListMatWR'				=> $List_MatWR,
				'ListMatRooving'		=> $List_MatRooving,
				'ListMatColor'			=> $List_MatColor,
				'ListMatTinuvin'		=> $List_MatTinuvin,
				'ListMatChl'			=> $List_MatChl,
				'ListMatStery'			=> $List_MatSm,
				'ListMatWax'			=> $List_MatWax,
				'ListMatMchl'			=> $List_MatMchl
			);

			$this->load->view('Cust_component/custom', $data); 
		}
	}

	public function custom_edit(){
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
			
			
			if(!empty($data['ListDetailAdd'])){
				$ListDetailAdd1	= $data['ListDetailAdd'];
			}
			if(!empty($data['ListDetailAdd2'])){
				$ListDetailAdd2	= $data['ListDetailAdd2'];
			}
			if(!empty($data['ListDetailAdd3'])){
				$ListDetailAdd3	= $data['ListDetailAdd3'];
			}
			if(!empty($data['ListDetailAdd4'])){
				$ListDetailAdd4	= $data['ListDetailAdd4'];
			}
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd_Liner	= $data['ListDetailAdd_Liner'];
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd_Strukture	= $data['ListDetailAdd_Strukture'];
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd_External	= $data['ListDetailAdd_External'];
				// print_r($ListDetailAdd_External);
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd_TopCoat	= $data['ListDetailAdd_TopCoat'];
			}
			
			$kd_spc = $data['component_list'];
			$KodeProduct	= $this->db->query("SELECT kode FROM product_parent WHERE product_parent='".$kd_spc."' LIMIT 1 ")->result_array();
			
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2']; 
			$cust			= (!empty($data['cust']))?"-".$data['cust']:'C100-1903000';
			$cust2			= (!empty($data['cust']))?$data['cust']:'C100-1903000';

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdDiameter2	= sprintf('%04s',$diameter2);
			$KdLiner		= $liner;
			
			$ket_plus		= "";
			$ket_plus2		= "";
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}

			$kode_product	= $KodeProduct[0]['kode']."-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$cust.$ket_plus;

			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			
			$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> $data['component_list'],
				'nm_product'			=> ucwords($data['component_list'])." ".$KdDiameter." x ".$KdDiameter2,
				'series'				=> $data['series'],
				'cust'					=> $cust2,
				'ket_plus'				=> $ket_plus2,
				'standart_code'			=> '',
				'resin_sistem'			=> $DataSeries[0]['resin_system'],
				'pressure'				=> $DataSeries[0]['pressure'],
				'liner'					=> $DataSeries[0]['liner'],

				'diameter'				=> $data['diameter'],
				'diameter2'				=> $data['diameter2'],
				'design'				=> $data['design'],
				
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
				$ArrDetail1[$val]['acuhan'] 		= '';
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
				$ArrDetail2[$val]['acuhan'] 		= '';
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
				$ArrDetail13[$val]['acuhan'] 		= '';
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

			$ArrDetailPlus3	= array();
			foreach($ListDetailPlus3 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus3[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus3[$val]['perse'] = $perseM;
				$ArrDetailPlus3[$val]['last_full'] 		= '';
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
				$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus4[$val]['perse'] = $perseM;
				$ArrDetailPlus4[$val]['last_full'] 		= '';
				$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus4);
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
			if(!empty($data['ListDetailAdd3'])){
				$ArrDataAdd3 = array();
				foreach($ListDetailAdd3 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd3[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd3[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd3[$val]['perse'] 		= $perseM;
					$ArrDataAdd3[$val]['last_full'] 	= '';
					$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd4'])){
				$ArrDataAdd4 = array();
				foreach($ListDetailAdd4 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd4[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd4[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd4[$val]['perse'] 		= $perseM;
					$ArrDataAdd4[$val]['last_full'] 	= '';
					$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			
			//ADD TEMP
			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd_Liner1 = array();
				foreach($ListDetailAdd_Liner AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Liner1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Liner1[$val]['detail_name'] 	= $data['detail_name'];
					$ListDetailAdd_Liner1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
					$ListDetailAdd_Liner1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Liner1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Liner1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Liner1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Liner1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Liner1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd_Strukture1 = array();
				foreach($ListDetailAdd_Strukture AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1[$val]['detail_name'] 	= $data['detail_name2'];
					$ListDetailAdd_Strukture1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd_External1 = array();
				foreach($ListDetailAdd_External AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_External1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_External1[$val]['detail_name'] 	= $data['detail_name3'];
					$ListDetailAdd_External1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_External1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_External1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_External1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_External1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_External1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_External1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd_TopCoat1 = array();
				foreach($ListDetailAdd_TopCoat AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_TopCoat1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_TopCoat1[$val]['detail_name'] 	= $data['detail_name4'];
					$ListDetailAdd_TopCoat1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_TopCoat1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_TopCoat1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_TopCoat1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_TopCoat1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_TopCoat1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_TopCoat1[$val]['last_cost'] 	= $valx['last_cost'];
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
			
			// print_r($ListDetailAdd_Liner1);
			// print_r($ListDetailAdd_Strukture1);
			// print_r($ListDetailAdd_External1);
			// print_r($ListDetailAdd_TopCoat1);
			// exit;
			
			$this->db->trans_start();
				//Insert Batch Histories
				if(!empty( $ArrBqHeaderHist)){
					$this->db->insert_batch('hist_component_header', $ArrBqHeaderHist);
				}
				if(!empty( $ArrBqDetailHist)){
					$this->db->insert_batch('hist_component_detail', $ArrBqDetailHist);
				}
				if(!empty( $ArrBqDetailPlusHist)){
					$this->db->insert_batch('hist_component_detail_plus', $ArrBqDetailPlusHist);
				}
				if(!empty( $ArrBqDetailAddHist)){
					$this->db->insert_batch('hist_component_detail_add', $ArrBqDetailAddHist);
				}

				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));

				$this->db->insert('component_header', $ArrHeader);
				$this->db->insert_batch('component_detail', $ArrDetail1);
				$this->db->insert_batch('component_detail', $ArrDetail2);
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				if(!empty($data['ListDetailAdd'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
				}
				if(!empty($data['ListDetailAdd2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
				}
				if(!empty($data['ListDetailAdd3'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd3);
				}
				if(!empty($data['ListDetailAdd4'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd4);
				}
				
				if(!empty($data['ListDetailAdd_Liner'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Liner1);
				}
				if(!empty($data['ListDetailAdd_Strukture'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1);
				}
				if(!empty($data['ListDetailAdd_External'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_External1);
				}
				if(!empty($data['ListDetailAdd_TopCoat'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_TopCoat1);
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
				history('Edit Est '.$data['component_list'].' code : '.$kode_product);
			}
			
			
			echo json_encode($Arr_Kembali);
		}
		else{
			//Edit Komponent
			$id_product	= $this->uri->segment(3);
			// echo $id_product;
			$ComponentHeader			= $this->db->query("SELECT a.*, b.id FROM component_header a LEFT JOIN product b ON a.nm_product = b.nm_product WHERE a.id_product='".$id_product."' LIMIT 1 ")->result();
			
			$ComponentDetailLiner		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$ComponentDetailLinerPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$ComponentDetailLinerAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$NumRowsLinerAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->num_rows();
			$ComponentFooter			= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
			$ListSeries					= $this->db->query("SELECT kode_group AS series FROM component_group WHERE deleted ='N' AND `status`='Y' ORDER BY kode_group ASC")->result_array();
			// echo "SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' ";
			$ComponentDetailStructure		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();
			$ComponentDetailStructurePlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();
			$ComponentDetailStructureAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();
			$NumRowsStructureAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->num_rows();
			$ComponentFooterStructure		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' ")->result_array();
			
			$ComponentDetailEksternal		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$NumRowsEksternalAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
			$ComponentFooterEksternal		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			
			$ComponentDetailTopPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$ComponentDetailTopAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$NumRowsTopAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->num_rows();
			
			
			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product_parent WHERE type2 = 'custom' ORDER BY product_parent ASC")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife			= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();
			
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2		= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			
			$List_Realese		= List_Realese();
			$List_PlasticFirm	= List_PlasticFirm();
			$List_Veil			= List_Veil();
			$List_Resin			= List_Resin();
			$List_MatCsm		= List_MatCsm();
			$List_MatKatalis	= List_MatKatalis();
			$List_MatSm			= List_MatSm();
			$List_MatCobalt		= List_MatCobalt();
			$List_MatDma		= List_MatDma();
			$List_MatHydo		= List_MatHydo();
			$List_MatMethanol	= List_MatMethanol();
			$List_MatAdditive	= List_MatAdditive();
			$List_MatWR			= List_MatWR();
			$List_MatRooving	= List_MatRooving();
			$List_MatColor		= List_MatColor();
			$List_MatTinuvin	= List_MatTinuvin();
			$List_MatChl		= List_MatChl();
			$List_MatWax		= List_MatWax();
			$List_MatMchl		= List_MatMchl();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array(); 
			$data = array(
				'title'			=> 'Revision Estimation Custom',
				'action'		=> 'pipe',
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
				'footerStructure'		=> $ComponentFooterStructure,
				'detEksternal'			=> $ComponentDetailEksternal,
				'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
				'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
				'detEksternalNumRows'	=> $NumRowsEksternalAdd,
				'footerEksternal'		=> $ComponentFooterEksternal,
				
				'detTopPlus'	=> $ComponentDetailTopPlus,
				'detTopAdd'		=> $ComponentDetailTopAdd,
				'detTopNumRows'		=> $NumRowsTopAdd
				
			);
				
			$this->load->view('Cust_component/custom_edit', $data);
		}
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

	public function modal_custom(){
		history('View edit product estimasi custom');
		$this->load->view('Cust_component/modal_custom');
	}

	public function custom_bq(){
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
		
		
		if(!empty($data['ListDetailAdd'])){
			$ListDetailAdd1	= $data['ListDetailAdd'];
		}
		if(!empty($data['ListDetailAdd2'])){
			$ListDetailAdd2	= $data['ListDetailAdd2'];
		}
		if(!empty($data['ListDetailAdd3'])){
			$ListDetailAdd3	= $data['ListDetailAdd3'];
		}
		if(!empty($data['ListDetailAdd4'])){
			$ListDetailAdd4	= $data['ListDetailAdd4'];
		}
		
		if(!empty($data['ListDetailAdd_Liner'])){
			$ListDetailAdd_Liner	= $data['ListDetailAdd_Liner'];
		}
		if(!empty($data['ListDetailAdd_Strukture'])){
			$ListDetailAdd_Strukture	= $data['ListDetailAdd_Strukture'];
		}
		if(!empty($data['ListDetailAdd_External'])){
			$ListDetailAdd_External	= $data['ListDetailAdd_External'];
		}
		if(!empty($data['ListDetailAdd_TopCoat'])){
			$ListDetailAdd_TopCoat	= $data['ListDetailAdd_TopCoat'];
		}
		
		$kd_spc = $data['component_list'];
		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];

		$product_id = $data['id_product'];
		$ArrHeader	= array(
			'id_product'			=> $product_id,
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> $kd_spc,
			'nm_product'			=> $data['top_type'],
			'standart_code'			=> '',
			'resin_sistem'			=> $resin_sistem,
			'liner'					=> $liner,
			'pressure'				=> $pressure,
			'series'				=> $data['series'],

			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,

			'diameter'				=> $data['diameter'],
			'diameter2'				=> $data['diameter2'],
			'design'				=> $data['design'],

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

			$ArrDetail1[$val]['id_product'] 	= $product_id;
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= '';
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

			$ArrDetail2[$val]['id_product'] 	= $product_id;
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= '';
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
		//Detail3
		$ArrDetail13	= array();
		foreach($ListDetail3 AS $val => $valx){
			$IDMat3			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat3			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

			$ArrDetail13[$val]['id_product'] 	= $product_id;
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 		= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= '';
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
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $product_id;
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
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

			$ArrDetailPlus2[$val]['id_product'] 	= $product_id;
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
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

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $product_id;
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $product_id;
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		
		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1Bef = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1Bef[$val]['id_product'] 	= $product_id;
				$ArrDataAdd1Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1Bef[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1Bef[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1Bef[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd1Bef[$val]['last_full'] 	= '';
				$ArrDataAdd1Bef[$val]['last_cost'] 	= $valx['last_cost']; 
			}
			// print_r($ArrDataAdd1Bef);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2Bef = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2Bef[$val]['id_product'] 	= $product_id;
				$ArrDataAdd2Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2Bef[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2Bef[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2Bef[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd2Bef[$val]['last_full'] 	= '';
				$ArrDataAdd2Bef[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3Bef = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3Bef[$val]['id_product'] 	= $product_id;
				$ArrDataAdd3Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3Bef[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd3Bef[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3Bef[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd3Bef[$val]['last_full'] 	= '';
				$ArrDataAdd3Bef[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4Bef = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4Bef[$val]['id_product'] 	= $product_id;
				$ArrDataAdd4Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4Bef[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd4Bef[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4Bef[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd4Bef[$val]['last_full'] 	= '';
				$ArrDataAdd4Bef[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}

		if(!empty($data['ListDetailAdd_Liner'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd_Liner AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $product_id;
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 		= $data['id_bq'];
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
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd_Strukture'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd_Strukture AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $product_id;
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 		= $data['id_bq'];
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
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd_External'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd_External AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $product_id;
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd_TopCoat'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd_TopCoat AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $product_id;
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
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
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
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
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
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
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrBqDefault);

		// if(!empty($data['ListDetailAdd_Liner'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd_Strukture'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd_External'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd_TopCoat'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			
			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1Bef);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2Bef);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3Bef);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4Bef);
			}
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd_External'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
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
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation '.$kd_spc.' in project code : '.$data['id_bq'].' and '.$data['id_milik'].' product: '.$product_id);
		}
		echo json_encode($Arr_Kembali);
	}
}
