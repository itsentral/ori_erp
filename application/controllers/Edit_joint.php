<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_joint extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');

		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

	//branchjoint
	public function edit($id=NULL){
		if (empty($id) || !isset($id)) {
			$id = $_POST['id_product'];
		}
		if (substr($id,0,2) == 'BJ') {
			$comp = 'branch joint';
			$comp2 = 'branchjoint';
			$title = 'Branch';
		}elseif (substr($id,0,2) == 'FJ') {
			$comp = 'field joint';
			$comp2 = 'fieldjoint';
			$title = 'Field';
		}else {
			$comp = 'shop joint';
			$comp2 = 'shopjoint';
			$title = 'Shop';
		}
		//echo $id;
		//exit;
		if($this->input->post()){
			$data = $this->input->post();
			if (empty($id) || !isset($id)) {
				$id = $data['id_product'];
			}
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
			
			if (substr($id,0,2) == 'BJ'){
				$diameter_2	= $data['diameter_2'];
			}
			
			$cust			= $data['cust'];
			$url_help		= $data['url_help'];
			if($url_help == 'standart'){
				$link_n 	= 'component';
			}
			else{
				$link_n 	= 'component_custom';
			}
			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter_1	= sprintf('%04s',$diameter_1);
			if (substr($id,0,2) == 'BJ'){
				$KdDiameter_2		= sprintf('%04s',$diameter_2);
			}
			$KdLiner		= $liner;

			if($cust == 'C100-1903000' OR $cust == '0'){
				$Tambahan 	= "";
				$custX		= "";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
			}

			$ket_plus		= "";
			$ket_plus2		= "";
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>=| '), '-', $data['ket_plus'])));
			}

			if(substr($id,0,2) == 'BJ'){
				$kode_product	= "BJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner."-DN".$KdDiameter_2."-".$KdLiner.$Tambahan.$ket_plus;
			}elseif (substr($id,0,2) == 'FJ') {
				$kode_product	= "FJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner.$Tambahan.$ket_plus;
			}elseif (substr($id,0,2) == 'SJ') {
				$kode_product	= "SJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner.$Tambahan.$ket_plus;
			}
			//$kode_product	= $id;

			// echo $kode_product; exit;
			$srcType	= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow		= $this->db->query($srcType)->num_rows();

			
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
			$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> $comp,
				'cust'					=> $custX,
				'ket_plus'				=> $ket_plus2,
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
			if (substr($id,0,2) == 'BJ'){
				$ArrHeader['diameter2']		= $data['diameter_2'];
				$ArrHeader['nm_product']	= $data['top_type_1']." X ".$diameter_2;
			}else {
				$ArrHeader['nm_product']	= $data['top_type_1'];
			}

			// print_r($ArrHeader); exit;
			foreach ($ListDetail_Glass as $key => $value) {
				foreach ($value as $k => $val) {
					$idm = $ListDetail_Glass['id_material'][$k];
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->result_array();

					$glass[$k][$key] = $val;
					$glass[$k]['id_product'] 	= $kode_product;
					//$glass[$k]['detail_name'] = $data['detail_name'];
					//$glass[$k]['acuhan'] 		= $data['acuhan_1'];
					//$glass[$k]['id_category'] = $dataMaterial[0]['id_category'];
					$glass[$k]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					//$glass[$k]['id_material'] = $valx['id_material'];
					$glass[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$valueM					= (!empty($ListDetail_Glass['value'][$k]))?$ListDetail_Glass['value'][$k]:'';
					$glass[$k]['value'] 		= $valueM;
						$thicknessM				= (!empty($ListDetail_Glass['thickness'][$k]))?$ListDetail_Glass['thickness'][$k]:'';
					$glass[$k]['thickness'] 	= $thicknessM;
						$pengaliM				= (!empty($ListDetail_Glass['fak_pengali'][$k]))?$ListDetail_Glass['fak_pengali'][$k]:'';
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
					$glass[$k]['total_thickness'] 	= $total_thicknessM;
						$lastfullM					= (!empty($ListDetail_Glass['last_full'][$k]))?$ListDetail_Glass['last_full'][$k]:'';
					$glass[$k]['last_full'] 		= $lastfullM;
						$lastcostM					= (!empty($ListDetail_Glass['last_cost'][$k]))?$ListDetail_Glass['last_cost'][$k]:'';
					$glass[$k]['last_cost'] 		= $lastcostM;
				}

			}
			// print_r($ListDetail_resinnadd);
			foreach ($ListDetail_resinnadd as $key => $value) {
				foreach ($value as $k => $val) {
					$idm = $ListDetail_resinnadd['id_material'][$k];
					//$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$idm."' LIMIT 1")->row();
					$dataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$idm))->row();
					$resinnadd[$k][$key] = $val;
					$resinnadd[$k]['id_product'] 		= $kode_product;
					//$resinnadd[$k]['detail_name'] 	= $data['detail_name'];
					//$resinnadd[$k]['acuhan'] 			= $data['acuhan_1'];
					//$resinnadd[$k]['id_category'] 	= $dataMaterial[0]['id_category'];
					//$resinnadd[$k]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$resinnadd[$k]['nm_category'] 		= $ListDetail_resinnadd['nm_category'][$k];
					//$resinnadd[$k]['id_material'] 	= $valx['id_material'];
					$nm 								= $dataMaterial->nm_material;
					// echo $idm."<br>";
					// exit;
					//$nm_mat = $dataMaterial->nm_material;
					$resinnadd[$k]['nm_material'] 		= $nm;
						$valueM							= (!empty($ListDetail_resinnadd['value'][$k]))?$ListDetail_resinnadd['value'][$k]:'';
					$resinnadd[$k]['value'] 			= $valueM;
						$thicknessM						= (!empty($ListDetail_resinnadd['thickness'][$k]))?$ListDetail_resinnadd['thickness'][$k]:'';
					$resinnadd[$k]['thickness'] 		= $thicknessM;
						$pengaliM						= (!empty($ListDetail_resinnadd['fak_pengali'][$k]))?$ListDetail_resinnadd['fak_pengali'][$k]:'';
					$resinnadd[$k]['fak_pengali'] 		= $pengaliM;
						$bwM							= (!empty($ListDetail_resinnadd['bw'][$k]))?$ListDetail_resinnadd['bw'][$k]:'';
					$resinnadd[$k]['bw'] 				= $bwM;
						$jumlahM						= (!empty($ListDetail_resinnadd['jumlah'][$k]))?$ListDetail_resinnadd['jumlah'][$k]:'';
					$resinnadd[$k]['jumlah'] 			= $jumlahM;
						$layerM							= (!empty($ListDetail_resinnadd['layer'][$k]))?$ListDetail_resinnadd['layer'][$k]:'';
					$resinnadd[$k]['layer'] 			= $layerM;;
						$containingM					= (!empty($ListDetail_resinnadd['containing'][$k]))?$ListDetail_resinnadd['containing'][$k]:'';
					$resinnadd[$k]['containing'] 		= $containingM;
						$total_thicknessM				= (!empty($ListDetail_resinnadd['total_thickness'][$k]))?$ListDetail_resinnadd['total_thickness'][$k]:'';
					$resinnadd[$k]['total_thickness'] 	= $total_thicknessM;
						$lastfullM						= (!empty($ListDetail_resinnadd['last_full'][$k]))?$ListDetail_resinnadd['last_full'][$k]:'';
					$resinnadd[$k]['last_full'] 		= $lastfullM;
						$lastcostM						= (!empty($ListDetail_resinnadd['last_cost'][$k]))?$ListDetail_resinnadd['last_cost'][$k]:'';
					$resinnadd[$k]['last_cost'] 		= $lastcostM;
				}
				// exit;
			}
			
			for ($i=0; $i < $no_il; $i++) {
				$ArrIl[$i]['id_product']			= $kode_product;
				$ArrIl[$i]['detail_name']			= 'Inside Lamination';
				$ArrIl[$i]['lapisan'] 				= $data['lapisan_'.($i+1)];
				$ArrIl[$i]['std_glass'] 			= $data['std_'.($i+1)];
				$ArrIl[$i]['width'] 				= $data['width_'.($i+1)];
				$ArrIl[$i]['stage'] 				= $data['stage_1'];
				$ArrIl[$i]['glass'] 				= $data['glassconfiguration_'.($i+1)];
				$ArrIl[$i]['thickness_1'] 			= $data['thickness1_'.($i+1)];
				$ArrIl[$i]['thickness_2'] 			= $data['thickness2_'.($i+1)];
				$ArrIl[$i]['glass_length'] 			= $data['glasslength_1'];
				$ArrIl[$i]['weight_veil'] 			= $data['veil_weight_'.($i+1)];
				$ArrIl[$i]['weight_csm'] 			= $data['csm_weight_'.($i+1)];
				$ArrIl[$i]['weight_wr'] 			= $data['wr_weight_'.($i+1)];
			}
			for ($i=0; $i < $no_ol; $i++) {
				$ArrOl[$i]['id_product']			= $kode_product;
				$ArrOl[$i]['detail_name']			= 'Outside Lamination';
				$ArrOl[$i]['lapisan'] 				= $data['o_lapisan_'.($i+1)];
				$ArrOl[$i]['std_glass'] 			= $data['o_std_'.($i+1)];
				$ArrOl[$i]['width'] 				= $data['o_width_'.($i+1)];
				$ArrOl[$i]['stage'] 				= $data['o_stage_ke_'.($i+1)];
				$ArrOl[$i]['glass'] 				= $data['o_glassconfiguration_'.($i+1)];
				$ArrOl[$i]['thickness_1'] 			= $data['o_thickness1_'.($i+1)];
				$ArrOl[$i]['thickness_2'] 			= $data['o_thickness2_'.($i+1)];
				if (isset($data['o_glasslength_'.($i+1)]) && !empty($data['o_glasslength_'.($i+1)])) {
					$ArrOl[$i]['glass_length'] 		= $data['o_glasslength_'.($i+1)];
				}else {
					$ArrOl[$i]['glass_length'] 		= 0;
				}
				$ArrOl[$i]['weight_veil'] 			= $data['o_veil_weight_'.($i+1)];
				$ArrOl[$i]['weight_csm'] 			= $data['o_csm_weight_'.($i+1)];
				$ArrOl[$i]['weight_wr'] 			= $data['o_wr_weight_'.($i+1)];
			}
			//echo $id."<br>".$kode_product;
			//exit;
			
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
			
			if(!empty($data['ResinAdd'])){
				$LA_Resin2 = $data['ResinAdd'];
				// print_r($LA_Resin2);
				$ArrResinAdd2 = array();
				foreach($LA_Resin2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();
					
					$ArrResinAdd2[$val]['id_product'] 	= $kode_product;
					$ArrResinAdd2[$val]['detail_name'] 	= $valx['detail_name'];
					$ArrResinAdd2[$val]['id_category'] 	= $valx['id_category'];
					$ArrResinAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrResinAdd2[$val]['id_material'] 	= $valx['id_material'];
					$ArrResinAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrResinAdd2[$val]['containing'] 	= 0;
						$perseM							= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrResinAdd2[$val]['perse'] 		= $perseM;
					$ArrResinAdd2[$val]['last_cost'] 	= $valx['last_cost'];
				}
				// print_r($ArrResinAdd2); 
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
					$ArrBqHeaderHist[$val2HistA]['pipe_thickness']		= $valx2HistA['pipe_thickness'];
					$ArrBqHeaderHist[$val2HistA]['joint_thickness']		= $valx2HistA['joint_thickness'];
					$ArrBqHeaderHist[$val2HistA]['factor_thickness']	= $valx2HistA['factor_thickness'];
					$ArrBqHeaderHist[$val2HistA]['factor']				= $valx2HistA['factor'];
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

					$ArrBqDetailHist[$val2Hist]['area_weight']		= $valx2Hist['area_weight'];
					$ArrBqDetailHist[$val2Hist]['material_weight']	= $valx2Hist['material_weight'];
					$ArrBqDetailHist[$val2Hist]['percentage']		= $valx2Hist['percentage'];
					$ArrBqDetailHist[$val2Hist]['resin_content']	= $valx2Hist['resin_content'];
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
					$ArrBqDetailPlusHist[$val3Hist]['deleted_by']	= $this->session->userdata['ORI_User']['username'];
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

			// print_r($ArrBqDetailHist);
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
				
				$getP = $this->db->get_where('component_header', array('id_product'=>$kode_product))->num_rows();
				if ($getP>0) {
					$this->db->where(array('id_product'=>$kode_product))->delete('component_header');
					$this->db->where(array('id_product'=>$kode_product))->delete('component_detail');
					$this->db->where(array('id_product'=>$kode_product))->delete('component_lamination');
					$this->db->where(array('id_product'=>$kode_product))->delete('component_detail_add');
				}
				$this->db->insert('component_header', $ArrHeader);
				$this->db->insert_batch('component_detail', $glass);
				$this->db->insert_batch('component_detail', $resinnadd);
				$this->db->insert_batch('component_lamination', $ArrIl);
				$this->db->insert_batch('component_lamination', $ArrOl);
				
				if(!empty($data['ListAdd_Resin'])){
					$this->db->insert_batch('component_detail_add', $ArrResinAdd);
				}
				if(!empty($data['ResinAdd'])){
					$this->db->insert_batch('component_detail_add', $ArrResinAdd2);
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
					'tanda'		=> $link_n,
					'pesan'		=>'Add Calculation Success. Thank you & have a nice day ...',
					'status'	=> 1
				);
				update_berat_est($kode_product);
				history('Add estimation code '.$kode_product);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct				= $this->db->query("SELECT * FROM product WHERE parent_product='$comp' AND deleted='N'")->result_array();
			$ListILamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='INSIDE LAMINATION'")->result_array();
			$ListOLamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION'")->result_array();
			$ListResinSystem			= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure				= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner					= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCustomer				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();


			$List_Veil					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm				= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();

			$dataStandart				= $this->db->query("SELECT * FROM identitas")->result_array();
			$component_header			= $this->db->get_where('component_header', array('id_product'=>$id))->row();
			$component_detail			= $this->db->get_where('component_detail', array('id_product'=>$id))->result();
			$component_lamination		= $this->db->get_where('component_lamination', array('id_product'=>$id))->result();
			$component_add				= $this->db->get_where('component_detail_add', array('id_product'=>$id))->result_array();

			$data = array(
				'title'					=> 'Edit Estimation '.$title.' Joint',
				'action'				=> $comp2,
				'product'				=> $ListProduct,
				'resin_system'			=> $ListResinSystem,
				'pressure'				=> $ListPressure,
				'liner'					=> $ListLiner,
				'series'				=> $ListSeries,
				'standard'				=> $ListCustomer,
				'customer'				=> $ListCustomer2,

				'ILamination'			=> $ListILamination,
				'OLamination'			=> $ListOLamination,

				'component_header'		=> $component_header,
				'component_detail'		=> $component_detail,
				'component_lamination'	=> $component_lamination,
				'component_add'			=> $component_add
			);

			$this->load->view('Component/edit/joint/'.$comp2, $data);
		}
	}

	//fieldjoint
	public function fieldjoint(){ 
		if($this->input->post()){
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');
			$ListDetail_Glass		= $data['glass'];
			$ListDetail_resinnadd		= $data['resinnadd'];
			//print_r($ListDetail_Glass);
			$glass = array();
			$resinnadd = array();
			$count = 0;
			//echo $ListDetail_Glass['id_material'][0];

			$ArrDet1 = array();

			$ArrIl = array();
			$ArrOl = array();
			$no_il = $data['no_il'];
			$no_ol = $data['no_ol'];
			/*foreach ($glass as $key => $value) {
				$idm = $value['id_material'];
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$value['id_material']."' LIMIT 1")->result_array();
				$glass[$key]['nm_category'] = $dataMaterial[0]['nm_category'];
				foreach ($value as $k => $val) {
					//$glass[$k][$key] = $val;
					//echo $key." -> ".$k." -> ".$val."<br>";
				}
			}
			foreach ($glass as $key => $value) {

				foreach ($value as $k => $val) {
					//$glass[$k][$key] = $val;
					echo $key." -> ".$k." -> ".$val."<br>";
				}
			}
			exit;*/
			/*
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
			*/

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter_1		= $data['diameter_1'];
			//$diameter_2		= $data['diameter_2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter_1		= sprintf('%04s',$diameter_1);
			//$KdDiameter_2		= sprintf('%04s',$diameter_2);
			$KdLiner		= $liner;

			$kode_product	= "FJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner;
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
				//$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'					=> $kode_product,
					'parent_product'			=> 'field joint',
					'nm_product'					=> $data['top_type_1'],
					'series'							=> $data['series'],
					'resin_sistem'				=> $DataSeries[0]['resin_system'],
					'pressure'						=> $DataSeries[0]['pressure'],
					'diameter'						=> $data['diameter_1'],
					'liner'								=> $DataSeries[0]['liner'],
					//'aplikasi_product'		=> $data['top_app'],
					//'criminal_barier'			=> $data['criminal_barier'],
					//'vacum_rate'					=> $data['vacum_rate'],
					//'stiffness'						=> $DataApp[0]['data2'],
					//'design_life'					=> $data['design_life'],
					'standart_by'					=> $data['top_toleran'],
					'standart_toleransi'	=> $DataCust[0]['nm_customer'],
					//'diameter2'						=> $data['diameter_2'],
					'panjang'							=> $data['minimum_width'],
					//'design'							=> $data['top_tebal_design'],
					//'radius'							=> $data['radius'],
					//'area'								=> $data['area'],
					//'est'									=> $data['top_tebal_est'],
					//'min_toleransi'				=> $data['top_min_toleran'],
					//'max_toleransi'				=> $data['top_max_toleran'],
					//'waste'								=> $data['waste'],
					'pipe_thickness'			=> $data['pipe_thickness'],
					'joint_thickness'			=> $data['joint_thickness'],
					'factor_thickness'		=> $data['factor_thickness'],
					'created_by'					=> $data_session['ORI_User']['username'],
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
						$glass[$k]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						//$glass[$k]['id_material'] 	= $valx['id_material'];
						$glass[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$valueM							= (!empty($ListDetail_Glass['value'][$k]))?$ListDetail_Glass['value'][$k]:'';
						$glass[$k]['value'] 			= $valueM;
							$thicknessM						= (!empty($ListDetail_Glass['thickness'][$k]))?$ListDetail_Glass['thickness'][$k]:'';
						$glass[$k]['thickness'] 		= $thicknessM;
							$pengaliM						= (!empty($ListDetail_Glass['fak_pengali'][$k]))?$ListDetail_Glass['fak_pengali'][$k]:'';
						$glass[$k]['fak_pengali'] 	= $pengaliM;
							$bwM							= (!empty($ListDetail_Glass['bw'][$k]))?$ListDetail_Glass['bw'][$k]:'';
						$glass[$k]['bw'] 			= $bwM;
							$jumlahM						= (!empty($ListDetail_Glass['jumlah'][$k]))?$ListDetail_Glass['jumlah'][$k]:'';
						$glass[$k]['jumlah'] 		= $jumlahM;
							$layerM							= (!empty($ListDetail_Glass['layer'][$k]))?$ListDetail_Glass['layer'][$k]:'';
						$glass[$k]['layer'] 			= $layerM;;
							$containingM					= (!empty($ListDetail_Glass['containing'][$k]))?$ListDetail_Glass['containing'][$k]:'';
						$glass[$k]['containing'] 	= $containingM;
							$total_thicknessM				= (!empty($ListDetail_Glass['total_thickness'][$k]))?$ListDetail_Glass['total_thickness'][$k]:'';
						$glass[$k]['total_thickness'] = $total_thicknessM;
							$lastfullM			= (!empty($ListDetail_Glass['last_full'][$k]))?$ListDetail_Glass['last_full'][$k]:'';
						$glass[$k]['last_full'] 		= $lastfullM;
							$lastcostM			= (!empty($ListDetail_Glass['last_cost'][$k]))?$ListDetail_Glass['last_cost'][$k]:'';
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
						$resinnadd[$k]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						//$resinnadd[$k]['id_material'] 	= $valx['id_material'];
						$resinnadd[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$valueM							= (!empty($ListDetail_resinnadd['value'][$k]))?$ListDetail_resinnadd['value'][$k]:'';
						$resinnadd[$k]['value'] 			= $valueM;
							$thicknessM						= (!empty($ListDetail_resinnadd['thickness'][$k]))?$ListDetail_resinnadd['thickness'][$k]:'';
						$resinnadd[$k]['thickness'] 		= $thicknessM;
							$pengaliM						= (!empty($ListDetail_resinnadd['fak_pengali'][$k]))?$ListDetail_resinnadd['fak_pengali'][$k]:'';
						$resinnadd[$k]['fak_pengali'] 	= $pengaliM;
							$bwM							= (!empty($ListDetail_resinnadd['bw'][$k]))?$ListDetail_resinnadd['bw'][$k]:'';
						$resinnadd[$k]['bw'] 			= $bwM;
							$jumlahM						= (!empty($ListDetail_resinnadd['jumlah'][$k]))?$ListDetail_resinnadd['jumlah'][$k]:'';
						$resinnadd[$k]['jumlah'] 		= $jumlahM;
							$layerM							= (!empty($ListDetail_resinnadd['layer'][$k]))?$ListDetail_resinnadd['layer'][$k]:'';
						$resinnadd[$k]['layer'] 			= $layerM;;
							$containingM					= (!empty($ListDetail_resinnadd['containing'][$k]))?$ListDetail_resinnadd['containing'][$k]:'';
						$resinnadd[$k]['containing'] 	= $containingM;
							$total_thicknessM				= (!empty($ListDetail_resinnadd['total_thickness'][$k]))?$ListDetail_resinnadd['total_thickness'][$k]:'';
						$resinnadd[$k]['total_thickness'] = $total_thicknessM;
							$lastfullM			= (!empty($ListDetail_resinnadd['last_full'][$k]))?$ListDetail_resinnadd['last_full'][$k]:'';
						$resinnadd[$k]['last_full'] 		= $lastfullM;
							$lastcostM			= (!empty($ListDetail_resinnadd['last_cost'][$k]))?$ListDetail_resinnadd['last_cost'][$k]:'';
						$resinnadd[$k]['last_cost'] 		= $lastcostM;
					}

				}
				for ($i=0; $i < $no_il; $i++) {
					$ArrIl[$i]['id_product']		=	$kode_product;
					$ArrIl[$i]['detail_name']		=	'Inside Lamination';
					$ArrIl[$i]['lapisan'] 			= $data['lapisan_'.($i+1)];
					$ArrIl[$i]['std_glass'] 		= $data['std_'.($i+1)];
					$ArrIl[$i]['width'] 				= $data['width_'.($i+1)];
					$ArrIl[$i]['stage'] 				= $data['stage_1'];
					$ArrIl[$i]['glass'] 				= $data['glassconfiguration_'.($i+1)];
					$ArrIl[$i]['thickness_1'] 	= $data['thickness1_'.($i+1)];
					$ArrIl[$i]['thickness_2'] 	= $data['thickness2_'.($i+1)];
					$ArrIl[$i]['glass_length'] 	= $data['glasslength_1'];
					$ArrIl[$i]['weight_veil'] 	= $data['veil_weight_'.($i+1)];
					$ArrIl[$i]['weight_csm'] 		= $data['csm_weight_'.($i+1)];
					$ArrIl[$i]['weight_wr'] 		= $data['wr_weight_'.($i+1)];
				}
				for ($i=0; $i < $no_ol; $i++) {
					$ArrOl[$i]['id_product']		=	$kode_product;
					$ArrOl[$i]['detail_name']		=	'Outside Lamination';
					$ArrOl[$i]['lapisan'] 			= $data['o_lapisan_'.($i+1)];
					$ArrOl[$i]['std_glass'] 		= $data['o_std_'.($i+1)];
					$ArrOl[$i]['width'] 				= $data['o_width_'.($i+1)];
					$ArrOl[$i]['stage'] 				= $data['o_stage_ke_'.($i+1)];
					$ArrOl[$i]['glass'] 				= $data['o_glassconfiguration_'.($i+1)];
					$ArrOl[$i]['thickness_1'] 	= $data['o_thickness1_'.($i+1)];
					$ArrOl[$i]['thickness_2'] 	= $data['o_thickness2_'.($i+1)];
					if (isset($data['o_glasslength_'.($i+1)]) && !empty($data['o_glasslength_'.($i+1)])) {
						$ArrOl[$i]['glass_length'] 	= $data['o_glasslength_'.($i+1)];
					}else {
						$ArrOl[$i]['glass_length'] 	= 0;
					}
					$ArrOl[$i]['weight_veil'] 	= $data['o_veil_weight_'.($i+1)];
					$ArrOl[$i]['weight_csm'] 		= $data['o_csm_weight_'.($i+1)];
					$ArrOl[$i]['weight_wr'] 		= $data['o_wr_weight_'.($i+1)];
				}



				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert_batch('component_detail', $glass);
					$this->db->insert_batch('component_detail', $resinnadd);
					$this->db->insert_batch('component_lamination', $ArrIl);
					$this->db->insert_batch('component_lamination', $ArrOl);
					/*$this->db->insert_batch('component_detail', $ArrDetail13);
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
					}*/
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
					history('Add estimation code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct					= $this->db->query("SELECT * FROM product WHERE parent_product='field joint' AND deleted='N'")->result_array();
			$ListILamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='INSIDE LAMINATION'")->result_array();
			$ListOLamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION'")->result_array();
			$ListResinSystem			= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure					= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner						= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries						= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate				= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife				= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer					= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();

			$List_Veil						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();

			$List_MatKatalis			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt				= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor				= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart					= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'							=> 'Estimation Field Joint',
				'action'						=> 'fieldjoint',
				'product'						=> $ListProduct,
				'resin_system'			=> $ListResinSystem,
				'pressure'					=> $ListPressure,
				'liner'							=> $ListLiner,
				'series'						=> $ListSeries,

				'ILamination'				=> $ListILamination,
				'OLamination'				=> $ListOLamination,

				'criminal_barier'		=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'				=> $ListVacumRate,
				'design_life'				=> $ListDesignLife,
				'standard'					=> $ListCustomer,
				'customer'					=> $ListCustomer2,

				'ListRealise'				=> $List_Realese,
				'ListPlastic'				=> $List_PlasticFirm,
				'ListVeil'					=> $List_Veil,
				'ListResin'					=> $List_Resin,
				'ListMatCsm'				=> $List_MatCsm,

				'ListMatKatalis'		=> $List_MatKatalis,
				'ListMatSm'					=> $List_MatSm,
				'ListMatCobalt'			=> $List_MatCobalt,
				'ListMatDma'				=> $List_MatDma,
				'ListMatHydo'				=> $List_MatHydo,
				'ListMatMethanol'		=> $List_MatMethanol,
				'ListMatAdditive'		=> $List_MatAdditive,

				'ListMatWR'					=> $List_MatWR,
				'ListMatRooving'		=> $List_MatRooving,

				'ListMatColor'			=> $List_MatColor,
				'ListMatTinuvin'		=> $List_MatTinuvin,
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
			$data = $this->input->post();
			$data_session			= $this->session->userdata;
			$mY		=  date('ym');
			$ListDetail_Glass		= $data['glass'];
			$ListDetail_resinnadd		= $data['resinnadd'];
			//print_r($ListDetail_Glass);
			$glass = array();
			$resinnadd = array();
			$count = 0;
			//echo $ListDetail_Glass['id_material'][0];

			$ArrDet1 = array();
			$ArrIl = array();
			$ArrOl = array();
			$no_il = $data['no_il'];
			$no_ol = $data['no_ol'];
			/*foreach ($glass as $key => $value) {
				$idm = $value['id_material'];
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$value['id_material']."' LIMIT 1")->result_array();
				$glass[$key]['nm_category'] = $dataMaterial[0]['nm_category'];
				foreach ($value as $k => $val) {
					//$glass[$k][$key] = $val;
					//echo $key." -> ".$k." -> ".$val."<br>";
				}
			}
			foreach ($glass as $key => $value) {

				foreach ($value as $k => $val) {
					//$glass[$k][$key] = $val;
					echo $key." -> ".$k." -> ".$val."<br>";
				}
			}
			exit;*/
			/*
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
			*/

			//pengurutan kode
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter_1		= $data['diameter_1'];
			//$diameter_2		= $data['diameter_2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter_1		= sprintf('%04s',$diameter_1);
			//$KdDiameter_2		= sprintf('%04s',$diameter_2);
			$KdLiner		= $liner;

			$kode_product	= "SJ-".$KdResinSystem."PN".$KdPressure."DN".$KdDiameter_1."-".$KdLiner;
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
				//$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'					=> $kode_product,
					'parent_product'			=> 'shop joint',
					'nm_product'					=> $data['top_type_1'],
					'series'							=> $data['series'],
					'resin_sistem'				=> $DataSeries[0]['resin_system'],
					'pressure'						=> $DataSeries[0]['pressure'],
					'diameter'						=> $data['diameter_1'],
					'liner'								=> $DataSeries[0]['liner'],
					//'aplikasi_product'		=> $data['top_app'],
					//'criminal_barier'			=> $data['criminal_barier'],
					//'vacum_rate'					=> $data['vacum_rate'],
					//'stiffness'						=> $DataApp[0]['data2'],
					//'design_life'					=> $data['design_life'],
					'standart_by'					=> $data['top_toleran'],
					'standart_toleransi'	=> $DataCust[0]['nm_customer'],
					//'diameter2'						=> $data['diameter_2'],
					'panjang'							=> $data['minimum_width'],
					//'design'							=> $data['top_tebal_design'],
					//'radius'							=> $data['radius'],
					//'area'								=> $data['area'],
					//'est'									=> $data['top_tebal_est'],
					//'min_toleransi'				=> $data['top_min_toleran'],
					//'max_toleransi'				=> $data['top_max_toleran'],
					//'waste'								=> $data['waste'],
					'pipe_thickness'			=> $data['pipe_thickness'],
					'joint_thickness'			=> $data['joint_thickness'],
					'factor_thickness'		=> $data['factor_thickness'],
					'created_by'					=> $data_session['ORI_User']['username'],
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
						$glass[$k]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						//$glass[$k]['id_material'] 	= $valx['id_material'];
						$glass[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$valueM							= (!empty($ListDetail_Glass['value'][$k]))?$ListDetail_Glass['value'][$k]:'';
						$glass[$k]['value'] 			= $valueM;
							$thicknessM						= (!empty($ListDetail_Glass['thickness'][$k]))?$ListDetail_Glass['thickness'][$k]:'';
						$glass[$k]['thickness'] 		= $thicknessM;
							$pengaliM						= (!empty($ListDetail_Glass['fak_pengali'][$k]))?$ListDetail_Glass['fak_pengali'][$k]:'';
						$glass[$k]['fak_pengali'] 	= $pengaliM;
							$bwM							= (!empty($ListDetail_Glass['bw'][$k]))?$ListDetail_Glass['bw'][$k]:'';
						$glass[$k]['bw'] 			= $bwM;
							$jumlahM						= (!empty($ListDetail_Glass['jumlah'][$k]))?$ListDetail_Glass['jumlah'][$k]:'';
						$glass[$k]['jumlah'] 		= $jumlahM;
							$layerM							= (!empty($ListDetail_Glass['layer'][$k]))?$ListDetail_Glass['layer'][$k]:'';
						$glass[$k]['layer'] 			= $layerM;;
							$containingM					= (!empty($ListDetail_Glass['containing'][$k]))?$ListDetail_Glass['containing'][$k]:'';
						$glass[$k]['containing'] 	= $containingM;
							$total_thicknessM				= (!empty($ListDetail_Glass['total_thickness'][$k]))?$ListDetail_Glass['total_thickness'][$k]:'';
						$glass[$k]['total_thickness'] = $total_thicknessM;
							$lastfullM			= (!empty($ListDetail_Glass['last_full'][$k]))?$ListDetail_Glass['last_full'][$k]:'';
						$glass[$k]['last_full'] 		= $lastfullM;
							$lastcostM			= (!empty($ListDetail_Glass['last_cost'][$k]))?$ListDetail_Glass['last_cost'][$k]:'';
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
						$resinnadd[$k]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						//$resinnadd[$k]['id_material'] 	= $valx['id_material'];
						$resinnadd[$k]['nm_material'] 	= $dataMaterial[0]['nm_material'];
							$valueM							= (!empty($ListDetail_resinnadd['value'][$k]))?$ListDetail_resinnadd['value'][$k]:'';
						$resinnadd[$k]['value'] 			= $valueM;
							$thicknessM						= (!empty($ListDetail_resinnadd['thickness'][$k]))?$ListDetail_resinnadd['thickness'][$k]:'';
						$resinnadd[$k]['thickness'] 		= $thicknessM;
							$pengaliM						= (!empty($ListDetail_resinnadd['fak_pengali'][$k]))?$ListDetail_resinnadd['fak_pengali'][$k]:'';
						$resinnadd[$k]['fak_pengali'] 	= $pengaliM;
							$bwM							= (!empty($ListDetail_resinnadd['bw'][$k]))?$ListDetail_resinnadd['bw'][$k]:'';
						$resinnadd[$k]['bw'] 			= $bwM;
							$jumlahM						= (!empty($ListDetail_resinnadd['jumlah'][$k]))?$ListDetail_resinnadd['jumlah'][$k]:'';
						$resinnadd[$k]['jumlah'] 		= $jumlahM;
							$layerM							= (!empty($ListDetail_resinnadd['layer'][$k]))?$ListDetail_resinnadd['layer'][$k]:'';
						$resinnadd[$k]['layer'] 			= $layerM;;
							$containingM					= (!empty($ListDetail_resinnadd['containing'][$k]))?$ListDetail_resinnadd['containing'][$k]:'';
						$resinnadd[$k]['containing'] 	= $containingM;
							$total_thicknessM				= (!empty($ListDetail_resinnadd['total_thickness'][$k]))?$ListDetail_resinnadd['total_thickness'][$k]:'';
						$resinnadd[$k]['total_thickness'] = $total_thicknessM;
							$lastfullM			= (!empty($ListDetail_resinnadd['last_full'][$k]))?$ListDetail_resinnadd['last_full'][$k]:'';
						$resinnadd[$k]['last_full'] 		= $lastfullM;
							$lastcostM			= (!empty($ListDetail_resinnadd['last_cost'][$k]))?$ListDetail_resinnadd['last_cost'][$k]:'';
						$resinnadd[$k]['last_cost'] 		= $lastcostM;
					}

				}
				for ($i=0; $i < $no_il; $i++) {
					$ArrIl[$i]['id_product']		=	$kode_product;
					$ArrIl[$i]['detail_name']		=	'Inside Lamination';
					$ArrIl[$i]['lapisan'] 			= $data['lapisan_'.($i+1)];
					$ArrIl[$i]['std_glass'] 		= $data['std_'.($i+1)];
					$ArrIl[$i]['width'] 				= $data['width_'.($i+1)];
					$ArrIl[$i]['stage'] 				= $data['stage_1'];
					$ArrIl[$i]['glass'] 				= $data['glassconfiguration_'.($i+1)];
					$ArrIl[$i]['thickness_1'] 	= $data['thickness1_'.($i+1)];
					$ArrIl[$i]['thickness_2'] 	= $data['thickness2_'.($i+1)];
					$ArrIl[$i]['glass_length'] 	= $data['glasslength_1'];
					$ArrIl[$i]['weight_veil'] 	= $data['veil_weight_'.($i+1)];
					$ArrIl[$i]['weight_csm'] 		= $data['csm_weight_'.($i+1)];
					$ArrIl[$i]['weight_wr'] 		= $data['wr_weight_'.($i+1)];
				}
				for ($i=0; $i < $no_ol; $i++) {
					$ArrOl[$i]['id_product']		=	$kode_product;
					$ArrOl[$i]['detail_name']		=	'Outside Lamination';
					$ArrOl[$i]['lapisan'] 			= $data['o_lapisan_'.($i+1)];
					$ArrOl[$i]['std_glass'] 		= $data['o_std_'.($i+1)];
					$ArrOl[$i]['width'] 				= $data['o_width_'.($i+1)];
					$ArrOl[$i]['stage'] 				= $data['o_stage_ke_'.($i+1)];
					$ArrOl[$i]['glass'] 				= $data['o_glassconfiguration_'.($i+1)];
					$ArrOl[$i]['thickness_1'] 	= $data['o_thickness1_'.($i+1)];
					$ArrOl[$i]['thickness_2'] 	= $data['o_thickness2_'.($i+1)];
					if (isset($data['o_glasslength_'.($i+1)]) && !empty($data['o_glasslength_'.($i+1)])) {
						$ArrOl[$i]['glass_length'] 	= $data['o_glasslength_'.($i+1)];
					}else {
						$ArrOl[$i]['glass_length'] 	= 0;
					}
					$ArrOl[$i]['weight_veil'] 	= $data['o_veil_weight_'.($i+1)];
					$ArrOl[$i]['weight_csm'] 		= $data['o_csm_weight_'.($i+1)];
					$ArrOl[$i]['weight_wr'] 		= $data['o_wr_weight_'.($i+1)];
				}



				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
					$this->db->insert_batch('component_detail', $glass);
					$this->db->insert_batch('component_detail', $resinnadd);
					$this->db->insert_batch('component_lamination', $ArrIl);
					$this->db->insert_batch('component_lamination', $ArrOl);
					/*$this->db->insert_batch('component_detail', $ArrDetail13);
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
					}*/
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
					history('Add estimation code '.$kode_product);
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$ListProduct					= $this->db->query("SELECT * FROM product WHERE parent_product='shop joint' AND deleted='N'")->result_array();
			$ListILamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='INSIDE LAMINATION'")->result_array();
			$ListOLamination			= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION'")->result_array();
			$ListResinSystem			= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure					= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner						= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$ListSeries						= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

			$ListCriminalBarier		= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
			$ListAplikasiProduct	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
			$ListVacumRate				= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
			$ListDesignLife				= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();

			$ListCustomer					= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
			$ListCustomer2				= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY id_customer ASC")->result_array();

			//Realease Agent Sementara Sama dengan Plastic Firm
			$List_Realese					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
			$List_PlasticFirm			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();

			$List_Veil						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
			$List_Resin						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
			$List_MatCsm					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();

			$List_MatKatalis			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
			$List_MatSm						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
			$List_MatCobalt				= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatDma					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
			$List_MatHydo					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatMethanol			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
			$List_MatAdditive			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWR						= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
			$List_MatRooving			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatColor				= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
			$List_MatTinuvin			= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatChl					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatWax					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl					= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart					= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'							=> 'Estimation Shop Joint',
				'action'						=> 'shopjoint',
				'product'						=> $ListProduct,
				'resin_system'			=> $ListResinSystem,
				'pressure'					=> $ListPressure,
				'liner'							=> $ListLiner,
				'series'						=> $ListSeries,

				'ILamination'				=> $ListILamination,
				'OLamination'				=> $ListOLamination,

				'criminal_barier'		=> $ListCriminalBarier,
				'aplikasi_product'	=> $ListAplikasiProduct,
				'vacum_rate'				=> $ListVacumRate,
				'design_life'				=> $ListDesignLife,
				'standard'					=> $ListCustomer,
				'customer'					=> $ListCustomer2,

				'ListRealise'				=> $List_Realese,
				'ListPlastic'				=> $List_PlasticFirm,
				'ListVeil'					=> $List_Veil,
				'ListResin'					=> $List_Resin,
				'ListMatCsm'				=> $List_MatCsm,

				'ListMatKatalis'		=> $List_MatKatalis,
				'ListMatSm'					=> $List_MatSm,
				'ListMatCobalt'			=> $List_MatCobalt,
				'ListMatDma'				=> $List_MatDma,
				'ListMatHydo'				=> $List_MatHydo,
				'ListMatMethanol'		=> $List_MatMethanol,
				'ListMatAdditive'		=> $List_MatAdditive,

				'ListMatWR'					=> $List_MatWR,
				'ListMatRooving'		=> $List_MatRooving,

				'ListMatColor'			=> $List_MatColor,
				'ListMatTinuvin'		=> $List_MatTinuvin,
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



	public function flangemould2(){
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

				$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['top_toleran']."' LIMIT 1 ")->result_array();
				$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
				$DataSeries	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

				$ArrHeader	= array(
					'id_product'			=> $kode_product,
					'nm_product'			=> $data['top_type'],
					'parent_product'		=> 'flange slongsong',
					'series'				=> $data['series'],
					'resin_sistem'			=> $DataSeries[0]['resin_system'],
					'pressure'				=> $DataSeries[0]['pressure'],

					'liner'					=> $DataSeries[0]['liner'],
					'aplikasi_product'		=> $data['top_app'],
					'criminal_barier'		=> $data['criminal_barier'],
					'vacum_rate'			=> $data['vacum_rate'],
					'stiffness'				=> $DataApp[0]['data2'],
					'design_life'			=> $data['design_life'],
					'standart_by'			=> $data['top_toleran'],
					'standart_toleransi'	=> $DataCust[0]['nm_customer'],

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
				exit;

				$this->db->trans_start();
					$this->db->insert('component_header', $ArrHeader);
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
					history('Add estimation code '.$kode_product);
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
			$List_MatChl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0002' OR id_material='MTL-1903173' ORDER BY nm_material ASC")->result_array();

			// $List_MatStery		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
			$List_MatWax		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
			$List_MatMchl		= $this->db->query("SELECT * FROM raw_materials WHERE id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();

			$dataStandart		= $this->db->query("SELECT * FROM identitas")->result_array();
			$data = array(
				'title'			=> 'Estimation Flange Mould 2',
				'action'		=> 'flangemould2',
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

			$this->load->view('Component/est/flangemould2', $data);
		}
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
		$parent_product		= $this->input->post("parent_product");

		$std		= 'PRODUCT-ORI';
		if(!empty($this->input->post("std"))){
			$std		= $this->input->post("std");
		}

		$qDefault	= "SELECT * FROM help_default WHERE standart_code='".$std."' AND diameter = '".$dim."' AND product_parent = '".$parent_product."' ";
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
