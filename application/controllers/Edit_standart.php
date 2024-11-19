<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_standart extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		
		$this->load->database();
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	public function elbow_mould_edit(){
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

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$sudut			= $data['angle'];
			$radiusX		= $data['type_elbow'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdSudut		= sprintf('%03s',$sudut);
			$KdLiner		= $liner;
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OF-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "F-";
			}


			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-".$KdSudut."-".$radiusX.$Tambahan;
			// echo $kode_product; exit;

			// echo "Masuk Save";
			// exit;
			$DataCust	= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'elbow mould',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'cust'					=> $custX,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
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
				
				'type_elbow'			=> $data['type_elbow'],
				'angle'					=> $data['angle'],
				'radius'				=> $data['radius'],
				
				'panjang'				=> 0,
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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

			// if(!empty($data['ListDetailAdd'])){
				// print_r($ArrDataAdd1);
			// }
			// if(!empty($data['ListDetailAdd2'])){
				// print_r($ArrDataAdd2);
			// }
			// if(!empty($data['ListDetailAdd3'])){
				// print_r($ArrDataAdd3);
			// }
			// if(!empty($data['ListDetailAdd4'])){
				// print_r($ArrDataAdd4);
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

				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product));

				$this->db->insert('component_header', $ArrHeader);
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
				update_berat_est($kode_product);
				history('Edit Est elbow mould code : '.$kode_product.' to '.$kode_product);
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

			$ComponentDetailEksternal		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$NumRowsEksternalAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
			$ComponentFooterEksternal		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

			$ComponentDetailTopPlus		= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$ComponentDetailTopAdd		= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$NumRowsTopAdd				= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->num_rows();

			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='elbow mould' AND deleted='N'")->result_array();
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
				'action'			=> 'pipe_edit',
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
				'footerStructure'		=> $ComponentFooterStructure,

				'detEksternal'			=> $ComponentDetailEksternal,
				'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
				'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
				'detEksternalNumRows'	=> $NumRowsEksternalAdd,
				'footerEksternal'		=> $ComponentFooterEksternal,

				'detTopPlus'		=> $ComponentDetailTopPlus,
				'detTopAdd'			=> $ComponentDetailTopAdd,
				'detTopNumRows'		=> $NumRowsTopAdd
			);
			$this->load->view('Component/edit/elbow_mould_edit', $data);
		}
	}
	
	public function flange_mould_edit(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetail2N1		= $data['ListDetail2N1'];
			$ListDetail2N2		= $data['ListDetail2N2'];
			
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$ListDetailPlus2N1	= $data['ListDetailPlus2N1'];
			$ListDetailPlus2N2	= $data['ListDetailPlus2N2'];
			
			
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ListDetailAdd2N1	= $data['ListDetailAdd2N1'];
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ListDetailAdd2N2	= $data['ListDetailAdd2N2'];
			}
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd_Liner	= $data['ListDetailAdd_Liner'];
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd_Strukture	= $data['ListDetailAdd_Strukture'];
			}
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_StruktureN1	= $data['ListDetailAdd_StruktureN1'];
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_StruktureN2	= $data['ListDetailAdd_StruktureN2'];
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd_External	= $data['ListDetailAdd_External'];
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd_TopCoat	= $data['ListDetailAdd_TopCoat'];
			}

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			
			$ket_plus		= "";
			$ket_plus2		= "";
			
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OG-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "G-";
			}

			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner.$Tambahan.$ket_plus; 
			// echo $kode_product; exit;

			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'flange mould',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'cust'					=> $custX,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series']."-".$KdLiner,
				'liner'					=> $data['ThLin'],
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				'design'				=> $data['design'],
				'area'					=> $data['area'],
				
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
			
			//Detail2N2
			$ArrDetail2N1	= array();
			foreach($ListDetail2N1 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetail2N1[$val]['acuhan'] 		= $data['ThStrN1'];
				$ArrDetail2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N1[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N1[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N1[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N1[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N1[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N1[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N1[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N1[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N1[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N1[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N1);
			// exit;
			
			
			//Detail2N2
			$ArrDetail2N2	= array();
			foreach($ListDetail2N2 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetail2N2[$val]['acuhan'] 		= $data['ThStrN2'];
				$ArrDetail2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N2[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N2[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N2[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N2[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N2[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N2[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N2[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N2[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N2[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N2[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N2);
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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
			
			$ArrDetailPlus2N1	= array();
			foreach($ListDetailPlus2N1 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetailPlus2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N1[$val]['perse'] = $perseM;
				$ArrDetailPlus2N1[$val]['last_full'] 		= '';
				$ArrDetailPlus2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N1);
			
			$ArrDetailPlus2N2	= array();
			foreach($ListDetailPlus2N2 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetailPlus2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N2[$val]['perse'] = $perseM;
				$ArrDetailPlus2N2[$val]['last_full'] 		= '';
				$ArrDetailPlus2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N2);

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
			
			$ArrFooter2N1	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N1'],
					'total'			=> $data['thickStrN1'],
					'min'			=> $data['minStrN1'],
					'max'			=> $data['maxStrN1'],
					'hasil'			=> $data['hasilStrN1']
			);
			
			$ArrFooter2N2	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N2'],
					'total'			=> $data['thickStrN2'],
					'min'			=> $data['minStrN2'],
					'max'			=> $data['maxStrN2'],
					'hasil'			=> $data['hasilStrN2']
			);

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ArrDataAdd2N1 = array();
				foreach($ListDetailAdd2N1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N1[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ArrDataAdd2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N1[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N1[$val]['last_full'] 	= '';
					$ArrDataAdd2N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ArrDataAdd2N2 = array();
				foreach($ListDetailAdd2N2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N2[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ArrDataAdd2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N2[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N2[$val]['last_full'] 	= '';
					$ArrDataAdd2N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_Strukture1N1 = array();
				foreach($ListDetailAdd_StruktureN1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ListDetailAdd_Strukture1N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_Strukture1N2 = array();
				foreach($ListDetailAdd_StruktureN2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N2[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ListDetailAdd_Strukture1N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N2[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N2[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			// print_r($ArrDetail2N1);
			// print_r($ArrDetail2N2);
			// print_r($ArrDetail13);
			// print_r($ArrDetailPlus1);
			// print_r($ArrDetailPlus2);
			// print_r($ArrDetailPlus2N1);
			// print_r($ArrDetailPlus2N2);
			// print_r($ArrDetailPlus3);
			// print_r($ArrDetailPlus4);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
			// print_r($ArrFooter3);

			// if(!empty($data['ListDetailAdd'])){
				// print_r($ArrDataAdd1);
			// }
			// if(!empty($data['ListDetailAdd2'])){
				// print_r($ArrDataAdd2);
			// }
			// if(!empty($data['ListDetailAdd2N1'])){
				// print_r($ArrDataAdd2N1);
			// }
			// if(!empty($data['ListDetailAdd2N2'])){
				// print_r($ArrDataAdd2N2);
			// }
			// if(!empty($data['ListDetailAdd3'])){
				// print_r($ArrDataAdd3);
			// }
			// if(!empty($data['ListDetailAdd4'])){
				// print_r($ArrDataAdd4);
			// }
			
			// if(!empty($data['ListDetailAdd_Liner'])){
				// print_r($ListDetailAdd_Liner1);
			// }
			// if(!empty($data['ListDetailAdd_Strukture'])){
				// print_r($ListDetailAdd_Strukture1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN1'])){
				// print_r($ListDetailAdd_Strukture1N1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN2'])){
				// print_r($ListDetailAdd_Strukture1N2);
			// }
			// if(!empty($data['ListDetailAdd_External'])){
				// print_r($ListDetailAdd_External1);
			// }
			// if(!empty($data['ListDetailAdd_TopCoat'])){
				// print_r($ListDetailAdd_TopCoat1);
			// }
			
			// echo "Tahan dulu ya, buat contoh belum, siapa tau ini inputan orang !!!";
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
			
				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product)); 
			
				$this->db->insert('component_header', $ArrHeader);
				$this->db->insert_batch('component_detail', $ArrDetail1);
				$this->db->insert_batch('component_detail', $ArrDetail2);
				$this->db->insert_batch('component_detail', $ArrDetail2N1);
				$this->db->insert_batch('component_detail', $ArrDetail2N2);
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter2N1);
				$this->db->insert('component_footer', $ArrFooter2N2);
				$this->db->insert('component_footer', $ArrFooter3);
				if(!empty($data['ListDetailAdd'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
				}
				if(!empty($data['ListDetailAdd2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
				}
				if(!empty($data['ListDetailAdd2N1'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N1);
				}
				if(!empty($data['ListDetailAdd2N2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N2);
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
				if(!empty($data['ListDetailAdd_StruktureN1'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N1);
				}
				if(!empty($data['ListDetailAdd_StruktureN2'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N2);
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
				update_berat_est($kode_product);
				history('Edit Est Flange Mould in custom code : '.$kode_product.' to '.$kode_product);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//Edit Komponent
			$id_product	= $this->uri->segment(3);
			$ComponentHeader			= $this->db->query("SELECT a.*, b.id FROM component_header a LEFT JOIN product b ON a.diameter = b.value_d WHERE a.id_product='".$id_product."' AND b.parent_product = 'flange mould' LIMIT 1 ")->result();
			
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

			$ComponentDetailEksternal		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$NumRowsEksternalAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
			$ComponentFooterEksternal		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

			$ComponentDetailTopPlus			= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$ComponentDetailTopAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$NumRowsTopAdd					= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->num_rows();
			
			$ComponentDetailStructureN1		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructurePlusN1	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructureAddN1	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$NumRowsStructureAddN1			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->num_rows();
			$ComponentFooterStructureN1		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();


			$ComponentDetailStructureN2		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructurePlusN2	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructureAddN2	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$NumRowsStructureAddN2			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->num_rows();
			$ComponentFooterStructureN2		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();

			//List Dropdown
			$ListProduct			= $this->db->query("SELECT * FROM product WHERE parent_product='flange mould' AND deleted='N' AND value_d='".$ComponentHeader[0]->diameter."'")->result_array();
			$ListResinSystem		= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure			= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'				=> 'REVISED FLANGE MOULD ESTIMATE',
				'action'			=> 'flange_mould_edit',
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
				'footerStructure'		=> $ComponentFooterStructure,
				
				'detStructureN1'		=> $ComponentDetailStructureN1,
				'detStructurePlusN1'	=> $ComponentDetailStructurePlusN1,
				'detStructureAddN1'		=> $ComponentDetailStructureAddN1,
				'detStructureNumRowsN1'	=> $NumRowsStructureAddN1,
				'footerStructureN1'		=> $ComponentFooterStructureN1,
				
				'detStructureN2'		=> $ComponentDetailStructureN2,
				'detStructurePlusN2'	=> $ComponentDetailStructurePlusN2,
				'detStructureAddN2'		=> $ComponentDetailStructureAddN2,
				'detStructureNumRowsN2'	=> $NumRowsStructureAddN2,
				'footerStructureN2'		=> $ComponentFooterStructureN2,

				'detEksternal'			=> $ComponentDetailEksternal,
				'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
				'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
				'detEksternalNumRows'	=> $NumRowsEksternalAdd,
				'footerEksternal'		=> $ComponentFooterEksternal,

				'detTopPlus'		=> $ComponentDetailTopPlus,
				'detTopAdd'			=> $ComponentDetailTopAdd,
				'detTopNumRows'		=> $NumRowsTopAdd
			);
			$this->load->view('Component/edit/flange_mould_edit_new', $data);
		}
	}
	
	public function colar_edit(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetail2N1		= $data['ListDetail2N1'];
			$ListDetail2N2		= $data['ListDetail2N2'];
			
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$ListDetailPlus2N1	= $data['ListDetailPlus2N1'];
			$ListDetailPlus2N2	= $data['ListDetailPlus2N2'];
			
			
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ListDetailAdd2N1	= $data['ListDetailAdd2N1'];
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ListDetailAdd2N2	= $data['ListDetailAdd2N2'];
			}
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd_Liner	= $data['ListDetailAdd_Liner'];
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd_Strukture	= $data['ListDetailAdd_Strukture'];
			}
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_StruktureN1	= $data['ListDetailAdd_StruktureN1'];
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_StruktureN2	= $data['ListDetailAdd_StruktureN2'];
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd_External	= $data['ListDetailAdd_External'];
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd_TopCoat	= $data['ListDetailAdd_TopCoat'];
			}

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			
			$ket_plus		= "";
			$ket_plus2		= "";
			
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OO-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "O-";
			}

			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner.$Tambahan.$ket_plus; 
			// echo $kode_product; exit;

			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'colar',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'cust'					=> $custX,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series']."-".$KdLiner,
				'liner'					=> $data['ThLin'],
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				'design'				=> $data['design'],
				'area'					=> $data['area'],
				
				'panjang_neck_1'		=> $data['panjang_neck_1'],
				'panjang_neck_2'		=> $data['panjang_neck_2'],
				'design_neck_1'			=> $data['design_neck_1'],
				'design_neck_2'			=> $data['design_neck_2'],
				'est_neck_1'			=> $data['est_neck_1'],
				'est_neck_2'			=> $data['est_neck_2'],
				'area_neck_1'			=> $data['area_neck_1'],
				'area_neck_2'			=> $data['area_neck_2'],
				'flange_od'				=> $data['flange_od'],
				
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
			
			//Detail2N2
			$ArrDetail2N1	= array();
			foreach($ListDetail2N1 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetail2N1[$val]['acuhan'] 		= $data['ThStrN1'];
				$ArrDetail2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N1[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N1[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N1[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N1[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N1[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N1[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N1[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N1[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N1[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N1[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N1);
			// exit;
			
			
			//Detail2N2
			$ArrDetail2N2	= array();
			foreach($ListDetail2N2 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetail2N2[$val]['acuhan'] 		= $data['ThStrN2'];
				$ArrDetail2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N2[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N2[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N2[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N2[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N2[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N2[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N2[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N2[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N2[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N2[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N2);
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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
			
			$ArrDetailPlus2N1	= array();
			foreach($ListDetailPlus2N1 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetailPlus2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N1[$val]['perse'] = $perseM;
				$ArrDetailPlus2N1[$val]['last_full'] 		= '';
				$ArrDetailPlus2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N1);
			
			$ArrDetailPlus2N2	= array();
			foreach($ListDetailPlus2N2 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetailPlus2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N2[$val]['perse'] = $perseM;
				$ArrDetailPlus2N2[$val]['last_full'] 		= '';
				$ArrDetailPlus2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N2);

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
			
			$ArrFooter2N1	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N1'],
					'total'			=> $data['thickStrN1'],
					'min'			=> $data['minStrN1'],
					'max'			=> $data['maxStrN1'],
					'hasil'			=> $data['hasilStrN1']
			);
			
			$ArrFooter2N2	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N2'],
					'total'			=> $data['thickStrN2'],
					'min'			=> $data['minStrN2'],
					'max'			=> $data['maxStrN2'],
					'hasil'			=> $data['hasilStrN2']
			);

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ArrDataAdd2N1 = array();
				foreach($ListDetailAdd2N1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N1[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ArrDataAdd2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N1[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N1[$val]['last_full'] 	= '';
					$ArrDataAdd2N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ArrDataAdd2N2 = array();
				foreach($ListDetailAdd2N2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N2[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ArrDataAdd2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N2[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N2[$val]['last_full'] 	= '';
					$ArrDataAdd2N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_Strukture1N1 = array();
				foreach($ListDetailAdd_StruktureN1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ListDetailAdd_Strukture1N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_Strukture1N2 = array();
				foreach($ListDetailAdd_StruktureN2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N2[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ListDetailAdd_Strukture1N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N2[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N2[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			// print_r($ArrDetail2N1);
			// print_r($ArrDetail2N2);
			// print_r($ArrDetail13);
			// print_r($ArrDetailPlus1);
			// print_r($ArrDetailPlus2);
			// print_r($ArrDetailPlus2N1);
			// print_r($ArrDetailPlus2N2);
			// print_r($ArrDetailPlus3);
			// print_r($ArrDetailPlus4);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
			// print_r($ArrFooter3);

			// if(!empty($data['ListDetailAdd'])){
				// print_r($ArrDataAdd1);
			// }
			// if(!empty($data['ListDetailAdd2'])){
				// print_r($ArrDataAdd2);
			// }
			// if(!empty($data['ListDetailAdd2N1'])){
				// print_r($ArrDataAdd2N1);
			// }
			// if(!empty($data['ListDetailAdd2N2'])){
				// print_r($ArrDataAdd2N2);
			// }
			// if(!empty($data['ListDetailAdd3'])){
				// print_r($ArrDataAdd3);
			// }
			// if(!empty($data['ListDetailAdd4'])){
				// print_r($ArrDataAdd4);
			// }
			
			// if(!empty($data['ListDetailAdd_Liner'])){
				// print_r($ListDetailAdd_Liner1);
			// }
			// if(!empty($data['ListDetailAdd_Strukture'])){
				// print_r($ListDetailAdd_Strukture1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN1'])){
				// print_r($ListDetailAdd_Strukture1N1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN2'])){
				// print_r($ListDetailAdd_Strukture1N2);
			// }
			// if(!empty($data['ListDetailAdd_External'])){
				// print_r($ListDetailAdd_External1);
			// }
			// if(!empty($data['ListDetailAdd_TopCoat'])){
				// print_r($ListDetailAdd_TopCoat1);
			// }
			
			// echo "Tahan dulu ya, buat contoh belum, siapa tau ini inputan orang !!!";
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
			
				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product)); 
			
				$this->db->insert('component_header', $ArrHeader);
				$this->db->insert_batch('component_detail', $ArrDetail1);
				$this->db->insert_batch('component_detail', $ArrDetail2);
				$this->db->insert_batch('component_detail', $ArrDetail2N1);
				$this->db->insert_batch('component_detail', $ArrDetail2N2);
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter2N1);
				$this->db->insert('component_footer', $ArrFooter2N2);
				$this->db->insert('component_footer', $ArrFooter3);
				if(!empty($data['ListDetailAdd'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
				}
				if(!empty($data['ListDetailAdd2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
				}
				if(!empty($data['ListDetailAdd2N1'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N1);
				}
				if(!empty($data['ListDetailAdd2N2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N2);
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
				if(!empty($data['ListDetailAdd_StruktureN1'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N1);
				}
				if(!empty($data['ListDetailAdd_StruktureN2'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N2);
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
				update_berat_est($kode_product);
				history('Edit Est Colar in custom code : '.$kode_product.' to '.$kode_product);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//Edit Komponent
			$id_product	= $this->uri->segment(3);
			$ComponentHeader			= $this->db->query("SELECT a.*, b.id FROM component_header a LEFT JOIN product b ON a.diameter = b.value_d WHERE a.id_product='".$id_product."' AND b.parent_product = 'colar' LIMIT 1 ")->result();
			
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

			$ComponentDetailEksternal		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$NumRowsEksternalAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
			$ComponentFooterEksternal		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

			$ComponentDetailTopPlus			= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$ComponentDetailTopAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$NumRowsTopAdd					= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->num_rows();
			
			$ComponentDetailStructureN1		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructurePlusN1	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructureAddN1	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$NumRowsStructureAddN1			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->num_rows();
			$ComponentFooterStructureN1		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();


			$ComponentDetailStructureN2		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructurePlusN2	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructureAddN2	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$NumRowsStructureAddN2			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->num_rows();
			$ComponentFooterStructureN2		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();

			//List Dropdown
			$ListProduct			= $this->db->query("SELECT * FROM product WHERE parent_product='colar' AND deleted='N' AND value_d='".$ComponentHeader[0]->diameter."'")->result_array();
			$ListResinSystem		= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure			= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'				=> 'REVISED COLAR ESTIMATE',
				'action'			=> 'colar_edit',
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
				'footerStructure'		=> $ComponentFooterStructure,
				
				'detStructureN1'		=> $ComponentDetailStructureN1,
				'detStructurePlusN1'	=> $ComponentDetailStructurePlusN1,
				'detStructureAddN1'		=> $ComponentDetailStructureAddN1,
				'detStructureNumRowsN1'	=> $NumRowsStructureAddN1,
				'footerStructureN1'		=> $ComponentFooterStructureN1,
				
				'detStructureN2'		=> $ComponentDetailStructureN2,
				'detStructurePlusN2'	=> $ComponentDetailStructurePlusN2,
				'detStructureAddN2'		=> $ComponentDetailStructureAddN2,
				'detStructureNumRowsN2'	=> $NumRowsStructureAddN2,
				'footerStructureN2'		=> $ComponentFooterStructureN2,

				'detEksternal'			=> $ComponentDetailEksternal,
				'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
				'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
				'detEksternalNumRows'	=> $NumRowsEksternalAdd,
				'footerEksternal'		=> $ComponentFooterEksternal,

				'detTopPlus'		=> $ComponentDetailTopPlus,
				'detTopAdd'			=> $ComponentDetailTopAdd,
				'detTopNumRows'		=> $NumRowsTopAdd
			);
			$this->load->view('Component/edit/colar_edit', $data);
		}
	}
	
	public function colar_slongsong_edit(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetail2N1		= $data['ListDetail2N1'];
			$ListDetail2N2		= $data['ListDetail2N2'];
			
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$ListDetailPlus2N1	= $data['ListDetailPlus2N1'];
			$ListDetailPlus2N2	= $data['ListDetailPlus2N2'];
			
			
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ListDetailAdd2N1	= $data['ListDetailAdd2N1'];
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ListDetailAdd2N2	= $data['ListDetailAdd2N2'];
			}
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd_Liner	= $data['ListDetailAdd_Liner'];
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd_Strukture	= $data['ListDetailAdd_Strukture'];
			}
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_StruktureN1	= $data['ListDetailAdd_StruktureN1'];
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_StruktureN2	= $data['ListDetailAdd_StruktureN2'];
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd_External	= $data['ListDetailAdd_External'];
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd_TopCoat	= $data['ListDetailAdd_TopCoat'];
			}

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			
			$ket_plus		= "";
			$ket_plus2		= "";
			
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OU-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "U-";
			}

			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner.$Tambahan.$ket_plus; 
			// echo $kode_product; exit;

			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'colar slongsong',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'cust'					=> $custX,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series']."-".$KdLiner,
				'liner'					=> $data['ThLin'],
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				'design'				=> $data['design'],
				'area'					=> $data['area'],
				
				'panjang_neck_1'		=> $data['panjang_neck_1'],
				'panjang_neck_2'		=> $data['panjang_neck_2'],
				'design_neck_1'			=> $data['design_neck_1'],
				'design_neck_2'			=> $data['design_neck_2'],
				'est_neck_1'			=> $data['est_neck_1'],
				'est_neck_2'			=> $data['est_neck_2'],
				'area_neck_1'			=> $data['area_neck_1'],
				'area_neck_2'			=> $data['area_neck_2'],
				'flange_od'				=> $data['flange_od'],
				
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
			
			//Detail2N2
			$ArrDetail2N1	= array();
			foreach($ListDetail2N1 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetail2N1[$val]['acuhan'] 		= $data['ThStrN1'];
				$ArrDetail2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N1[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N1[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N1[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N1[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N1[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N1[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N1[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N1[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N1[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N1[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N1);
			// exit;
			
			
			//Detail2N2
			$ArrDetail2N2	= array();
			foreach($ListDetail2N2 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetail2N2[$val]['acuhan'] 		= $data['ThStrN2'];
				$ArrDetail2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N2[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N2[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N2[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N2[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N2[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N2[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N2[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N2[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N2[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N2[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N2);
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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
			
			$ArrDetailPlus2N1	= array();
			foreach($ListDetailPlus2N1 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetailPlus2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N1[$val]['perse'] = $perseM;
				$ArrDetailPlus2N1[$val]['last_full'] 		= '';
				$ArrDetailPlus2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N1);
			
			$ArrDetailPlus2N2	= array();
			foreach($ListDetailPlus2N2 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetailPlus2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N2[$val]['perse'] = $perseM;
				$ArrDetailPlus2N2[$val]['last_full'] 		= '';
				$ArrDetailPlus2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N2);

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
			
			$ArrFooter2N1	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N1'],
					'total'			=> $data['thickStrN1'],
					'min'			=> $data['minStrN1'],
					'max'			=> $data['maxStrN1'],
					'hasil'			=> $data['hasilStrN1']
			);
			
			$ArrFooter2N2	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N2'],
					'total'			=> $data['thickStrN2'],
					'min'			=> $data['minStrN2'],
					'max'			=> $data['maxStrN2'],
					'hasil'			=> $data['hasilStrN2']
			);

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ArrDataAdd2N1 = array();
				foreach($ListDetailAdd2N1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N1[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ArrDataAdd2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N1[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N1[$val]['last_full'] 	= '';
					$ArrDataAdd2N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ArrDataAdd2N2 = array();
				foreach($ListDetailAdd2N2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N2[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ArrDataAdd2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N2[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N2[$val]['last_full'] 	= '';
					$ArrDataAdd2N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_Strukture1N1 = array();
				foreach($ListDetailAdd_StruktureN1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ListDetailAdd_Strukture1N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_Strukture1N2 = array();
				foreach($ListDetailAdd_StruktureN2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N2[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ListDetailAdd_Strukture1N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N2[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N2[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			// print_r($ArrDetail2N1);
			// print_r($ArrDetail2N2);
			// print_r($ArrDetail13);
			// print_r($ArrDetailPlus1);
			// print_r($ArrDetailPlus2);
			// print_r($ArrDetailPlus2N1);
			// print_r($ArrDetailPlus2N2);
			// print_r($ArrDetailPlus3);
			// print_r($ArrDetailPlus4);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
			// print_r($ArrFooter3);

			// if(!empty($data['ListDetailAdd'])){
				// print_r($ArrDataAdd1);
			// }
			// if(!empty($data['ListDetailAdd2'])){
				// print_r($ArrDataAdd2);
			// }
			// if(!empty($data['ListDetailAdd2N1'])){
				// print_r($ArrDataAdd2N1);
			// }
			// if(!empty($data['ListDetailAdd2N2'])){
				// print_r($ArrDataAdd2N2);
			// }
			// if(!empty($data['ListDetailAdd3'])){
				// print_r($ArrDataAdd3);
			// }
			// if(!empty($data['ListDetailAdd4'])){
				// print_r($ArrDataAdd4);
			// }
			
			// if(!empty($data['ListDetailAdd_Liner'])){
				// print_r($ListDetailAdd_Liner1);
			// }
			// if(!empty($data['ListDetailAdd_Strukture'])){
				// print_r($ListDetailAdd_Strukture1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN1'])){
				// print_r($ListDetailAdd_Strukture1N1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN2'])){
				// print_r($ListDetailAdd_Strukture1N2);
			// }
			// if(!empty($data['ListDetailAdd_External'])){
				// print_r($ListDetailAdd_External1);
			// }
			// if(!empty($data['ListDetailAdd_TopCoat'])){
				// print_r($ListDetailAdd_TopCoat1);
			// }
			
			// echo "Tahan dulu ya, buat contoh belum, siapa tau ini inputan orang !!!";
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
			
				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product)); 
			
				$this->db->insert('component_header', $ArrHeader);
				$this->db->insert_batch('component_detail', $ArrDetail1);
				$this->db->insert_batch('component_detail', $ArrDetail2);
				$this->db->insert_batch('component_detail', $ArrDetail2N1);
				$this->db->insert_batch('component_detail', $ArrDetail2N2);
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter2N1);
				$this->db->insert('component_footer', $ArrFooter2N2);
				$this->db->insert('component_footer', $ArrFooter3);
				if(!empty($data['ListDetailAdd'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
				}
				if(!empty($data['ListDetailAdd2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
				}
				if(!empty($data['ListDetailAdd2N1'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N1);
				}
				if(!empty($data['ListDetailAdd2N2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N2);
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
				if(!empty($data['ListDetailAdd_StruktureN1'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N1);
				}
				if(!empty($data['ListDetailAdd_StruktureN2'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N2);
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
				update_berat_est($kode_product);
				history('Edit Est Colar Slongsong in custom code : '.$kode_product.' to '.$kode_product);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//Edit Komponent
			$id_product	= $this->uri->segment(3);
			$ComponentHeader			= $this->db->query("SELECT a.*, b.id FROM component_header a LEFT JOIN product b ON a.diameter = b.value_d WHERE a.id_product='".$id_product."' AND b.parent_product = 'colar slongsong' LIMIT 1 ")->result();
			
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

			$ComponentDetailEksternal		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$NumRowsEksternalAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
			$ComponentFooterEksternal		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

			$ComponentDetailTopPlus			= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$ComponentDetailTopAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$NumRowsTopAdd					= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->num_rows();
			
			$ComponentDetailStructureN1		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructurePlusN1	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructureAddN1	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$NumRowsStructureAddN1			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->num_rows();
			$ComponentFooterStructureN1		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();


			$ComponentDetailStructureN2		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructurePlusN2	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructureAddN2	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$NumRowsStructureAddN2			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->num_rows();
			$ComponentFooterStructureN2		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();

			//List Dropdown
			$ListProduct			= $this->db->query("SELECT * FROM product WHERE parent_product='colar slongsong' AND deleted='N' AND value_d='".$ComponentHeader[0]->diameter."'")->result_array();
			$ListResinSystem		= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure			= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'				=> 'REVISED COLAR SLONGSONG ESTIMATE',
				'action'			=> 'colar_slongsong_edit',
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
				'footerStructure'		=> $ComponentFooterStructure,
				
				'detStructureN1'		=> $ComponentDetailStructureN1,
				'detStructurePlusN1'	=> $ComponentDetailStructurePlusN1,
				'detStructureAddN1'		=> $ComponentDetailStructureAddN1,
				'detStructureNumRowsN1'	=> $NumRowsStructureAddN1,
				'footerStructureN1'		=> $ComponentFooterStructureN1,
				
				'detStructureN2'		=> $ComponentDetailStructureN2,
				'detStructurePlusN2'	=> $ComponentDetailStructurePlusN2,
				'detStructureAddN2'		=> $ComponentDetailStructureAddN2,
				'detStructureNumRowsN2'	=> $NumRowsStructureAddN2,
				'footerStructureN2'		=> $ComponentFooterStructureN2,

				'detEksternal'			=> $ComponentDetailEksternal,
				'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
				'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
				'detEksternalNumRows'	=> $NumRowsEksternalAdd,
				'footerEksternal'		=> $ComponentFooterEksternal,

				'detTopPlus'		=> $ComponentDetailTopPlus,
				'detTopAdd'			=> $ComponentDetailTopAdd,
				'detTopNumRows'		=> $NumRowsTopAdd
			);
			$this->load->view('Component/edit/colar_slongsong_edit', $data);
		}
	}
	
	public function flange_slongsong_edit(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetail2N1		= $data['ListDetail2N1'];
			$ListDetail2N2		= $data['ListDetail2N2'];
			
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			$ListDetailPlus2N1	= $data['ListDetailPlus2N1'];
			$ListDetailPlus2N2	= $data['ListDetailPlus2N2'];
			
			
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ListDetailAdd2N1	= $data['ListDetailAdd2N1'];
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ListDetailAdd2N2	= $data['ListDetailAdd2N2'];
			}
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd_Liner	= $data['ListDetailAdd_Liner'];
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd_Strukture	= $data['ListDetailAdd_Strukture'];
			}
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_StruktureN1	= $data['ListDetailAdd_StruktureN1'];
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_StruktureN2	= $data['ListDetailAdd_StruktureN2'];
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd_External	= $data['ListDetailAdd_External'];
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd_TopCoat	= $data['ListDetailAdd_TopCoat'];
			}

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			
			$ket_plus		= "";
			$ket_plus2		= "";
			
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OA-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "A-";
			}

			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner.$Tambahan.$ket_plus; 
			// echo $kode_product; exit;

			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'flange slongsong',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'cust'					=> $custX,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				'design'				=> $data['design'],
				'area'					=> $data['area'],
				
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
			
			//Detail2N2
			$ArrDetail2N1	= array();
			foreach($ListDetail2N1 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetail2N1[$val]['acuhan'] 		= $data['ThStrN1'];
				$ArrDetail2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N1[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N1[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N1[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N1[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N1[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N1[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N1[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N1[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N1[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N1[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N1);
			// exit;
			
			
			//Detail2N2
			$ArrDetail2N2	= array();
			foreach($ListDetail2N2 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				if($valx['id_material'] == null || $valx['id_material'] == ''){
					$IDMat2			= "MTL-1903000";
				}
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetail2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetail2N2[$val]['acuhan'] 		= $data['ThStrN2'];
				$ArrDetail2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetail2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2N2[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$valueM							= (!empty($valx['value']))?$valx['value']:'';
				$ArrDetail2N2[$val]['value'] 			= $valueM;
					$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
				$ArrDetail2N2[$val]['thickness'] 		= $thicknessM;
					$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
				$ArrDetail2N2[$val]['fak_pengali'] 	= $pengaliM;
					$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetail2N2[$val]['bw'] 			= $bwM;
					$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
				$ArrDetail2N2[$val]['jumlah'] 		= $jumlahM;
					$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
				$ArrDetail2N2[$val]['layer'] 			= $layerM;;
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetail2N2[$val]['containing'] 	= $containingM;
					$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
				$ArrDetail2N2[$val]['total_thickness'] = $total_thicknessM;
				$ArrDetail2N2[$val]['last_full'] 		= $valx['last_full'];
				$ArrDetail2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetail2N2);
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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
			
			$ArrDetailPlus2N1	= array();
			foreach($ListDetailPlus2N1 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N1[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDetailPlus2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N1[$val]['perse'] = $perseM;
				$ArrDetailPlus2N1[$val]['last_full'] 		= '';
				$ArrDetailPlus2N1[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N1);
			
			$ArrDetailPlus2N2	= array();
			foreach($ListDetailPlus2N2 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2N2[$val]['id_product'] 	= $kode_product;
				$ArrDetailPlus2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDetailPlus2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2N2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2N2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
				$ArrDetailPlus2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDetailPlus2N2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDetailPlus2N2[$val]['perse'] = $perseM;
				$ArrDetailPlus2N2[$val]['last_full'] 		= '';
				$ArrDetailPlus2N2[$val]['last_cost'] 		= $valx['last_cost'];
			}
			// print_r($ArrDetailPlus2N2);

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
			
			$ArrFooter2N1	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N1'],
					'total'			=> $data['thickStrN1'],
					'min'			=> $data['minStrN1'],
					'max'			=> $data['maxStrN1'],
					'hasil'			=> $data['hasilStrN1']
			);
			
			$ArrFooter2N2	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name2N2'],
					'total'			=> $data['thickStrN2'],
					'min'			=> $data['minStrN2'],
					'max'			=> $data['maxStrN2'],
					'hasil'			=> $data['hasilStrN2']
			);

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
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
			if(!empty($data['ListDetailAdd2N1'])){
				$ArrDataAdd2N1 = array();
				foreach($ListDetailAdd2N1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N1[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ArrDataAdd2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N1[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N1[$val]['last_full'] 	= '';
					$ArrDataAdd2N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$ArrDataAdd2N2 = array();
				foreach($ListDetailAdd2N2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2N2[$val]['id_product'] 	= $kode_product;
					$ArrDataAdd2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ArrDataAdd2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2N2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ArrDataAdd2N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ArrDataAdd2N2[$val]['perse'] 		= $perseM;
					$ArrDataAdd2N2[$val]['last_full'] 	= '';
					$ArrDataAdd2N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			if(!empty($data['ListDetailAdd_StruktureN1'])){
				$ListDetailAdd_Strukture1N1 = array();
				foreach($ListDetailAdd_StruktureN1 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N1[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N1[$val]['detail_name'] 	= $data['detail_name2N1'];
					$ListDetailAdd_Strukture1N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N1[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N1[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N1[$val]['last_cost'] 	= $valx['last_cost'];
				}
			}
			if(!empty($data['ListDetailAdd_StruktureN2'])){
				$ListDetailAdd_Strukture1N2 = array();
				foreach($ListDetailAdd_StruktureN2 AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1N2[$val]['id_product'] 	= $kode_product;
					$ListDetailAdd_Strukture1N2[$val]['detail_name'] 	= $data['detail_name2N2'];
					$ListDetailAdd_Strukture1N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1N2[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
					$ListDetailAdd_Strukture1N2[$val]['containing'] 	= $containingM;
						$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
					$ListDetailAdd_Strukture1N2[$val]['perse'] 		= $perseM;
					$ListDetailAdd_Strukture1N2[$val]['last_cost'] 	= $valx['last_cost'];
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
			// print_r($ArrDetail2N1);
			// print_r($ArrDetail2N2);
			// print_r($ArrDetail13);
			// print_r($ArrDetailPlus1);
			// print_r($ArrDetailPlus2);
			// print_r($ArrDetailPlus2N1);
			// print_r($ArrDetailPlus2N2);
			// print_r($ArrDetailPlus3);
			// print_r($ArrDetailPlus4);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter2N1);
			// print_r($ArrFooter2N2);
			// print_r($ArrFooter3);

			// if(!empty($data['ListDetailAdd'])){
				// print_r($ArrDataAdd1);
			// }
			// if(!empty($data['ListDetailAdd2'])){
				// print_r($ArrDataAdd2);
			// }
			// if(!empty($data['ListDetailAdd2N1'])){
				// print_r($ArrDataAdd2N1);
			// }
			// if(!empty($data['ListDetailAdd2N2'])){
				// print_r($ArrDataAdd2N2);
			// }
			// if(!empty($data['ListDetailAdd3'])){
				// print_r($ArrDataAdd3);
			// }
			// if(!empty($data['ListDetailAdd4'])){
				// print_r($ArrDataAdd4);
			// }
			
			// if(!empty($data['ListDetailAdd_Liner'])){
				// print_r($ListDetailAdd_Liner1);
			// }
			// if(!empty($data['ListDetailAdd_Strukture'])){
				// print_r($ListDetailAdd_Strukture1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN1'])){
				// print_r($ListDetailAdd_Strukture1N1);
			// }
			// if(!empty($data['ListDetailAdd_StruktureN2'])){
				// print_r($ListDetailAdd_Strukture1N2);
			// }
			// if(!empty($data['ListDetailAdd_External'])){
				// print_r($ListDetailAdd_External1);
			// }
			// if(!empty($data['ListDetailAdd_TopCoat'])){
				// print_r($ListDetailAdd_TopCoat1);
			// }
			
			// echo "Tahan dulu ya, buat contoh belum, siapa tau ini inputan orang !!!";
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
			
				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product)); 
			
				$this->db->insert('component_header', $ArrHeader);
				$this->db->insert_batch('component_detail', $ArrDetail1);
				$this->db->insert_batch('component_detail', $ArrDetail2);
				$this->db->insert_batch('component_detail', $ArrDetail2N1);
				$this->db->insert_batch('component_detail', $ArrDetail2N2);
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2N2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter2N1);
				$this->db->insert('component_footer', $ArrFooter2N2);
				$this->db->insert('component_footer', $ArrFooter3);
				if(!empty($data['ListDetailAdd'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd1);
				}
				if(!empty($data['ListDetailAdd2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2);
				}
				if(!empty($data['ListDetailAdd2N1'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N1);
				}
				if(!empty($data['ListDetailAdd2N2'])){
					$this->db->insert_batch('component_detail_add', $ArrDataAdd2N2);
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
				if(!empty($data['ListDetailAdd_StruktureN1'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N1);
				}
				if(!empty($data['ListDetailAdd_StruktureN2'])){
					$this->db->insert_batch('component_detail_add', $ListDetailAdd_Strukture1N2);
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
				update_berat_est($kode_product);
				history('Edit Est Flange Slongsong in custom code : '.$kode_product.' to '.$kode_product);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			//Edit Komponent
			$id_product	= $this->uri->segment(3);
			$ComponentHeader			= $this->db->query("SELECT a.*, b.id FROM component_header a LEFT JOIN product b ON a.diameter = b.value_d WHERE a.id_product='".$id_product."' AND b.parent_product = 'flange slongsong' LIMIT 1 ")->result();
			
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

			$ComponentDetailEksternal		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$NumRowsEksternalAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
			$ComponentFooterEksternal		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

			$ComponentDetailTopPlus			= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$ComponentDetailTopAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$NumRowsTopAdd					= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->num_rows();
			
			$ComponentDetailStructureN1		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructurePlusN1	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$ComponentDetailStructureAddN1	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();
			$NumRowsStructureAddN1			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->num_rows();
			$ComponentFooterStructureN1		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 1' ")->result_array();


			$ComponentDetailStructureN2		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructurePlusN2	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$ComponentDetailStructureAddN2	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();
			$NumRowsStructureAddN2			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->num_rows();
			$ComponentFooterStructureN2		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='STRUKTUR NECK 2' ")->result_array();

			//List Dropdown
			$ListProduct			= $this->db->query("SELECT * FROM product WHERE parent_product='flange mould' AND deleted='N' AND value_d='".$ComponentHeader[0]->diameter."'")->result_array();
			$ListResinSystem		= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure			= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'				=> 'REVISED FLANGE SLONGSONG ESTIMATE',
				'action'			=> 'flange_songsong_edit',
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
				'footerStructure'		=> $ComponentFooterStructure,
				
				'detStructureN1'		=> $ComponentDetailStructureN1,
				'detStructurePlusN1'	=> $ComponentDetailStructurePlusN1,
				'detStructureAddN1'		=> $ComponentDetailStructureAddN1,
				'detStructureNumRowsN1'	=> $NumRowsStructureAddN1,
				'footerStructureN1'		=> $ComponentFooterStructureN1,
				
				'detStructureN2'		=> $ComponentDetailStructureN2,
				'detStructurePlusN2'	=> $ComponentDetailStructurePlusN2,
				'detStructureAddN2'		=> $ComponentDetailStructureAddN2,
				'detStructureNumRowsN2'	=> $NumRowsStructureAddN2,
				'footerStructureN2'		=> $ComponentFooterStructureN2,

				'detEksternal'			=> $ComponentDetailEksternal,
				'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
				'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
				'detEksternalNumRows'	=> $NumRowsEksternalAdd,
				'footerEksternal'		=> $ComponentFooterEksternal,

				'detTopPlus'		=> $ComponentDetailTopPlus,
				'detTopAdd'			=> $ComponentDetailTopAdd,
				'detTopNumRows'		=> $NumRowsTopAdd
			);
			$this->load->view('Component/edit/flange_slongsong_edit_new', $data);
		}
	}
	
	public function concentric_reducer_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OR-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "R-";
			}
			
			$ket_plus		= "";
			$ket_plus2		= "";
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$Tambahan.$ket_plus;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			

			// echo "Masuk Save";
			// exit;
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'concentric reducer',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'diameter2'				=> $data['diameter2'],
				'panjang'				=> $data['panjang'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter3);
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
				history('Edit Est Concentric Reducer code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
			
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='concentric reducer' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation Concentric Reducer',
				'action'		=> 'concentric_reducer_edit',
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
				
			$this->load->view('Component/edit/concentric_reducer_edit', $data);
		}
	}
	
	public function eccentric_reducer_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OD-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "D-";
			}
			
			$ket_plus		= "";
			$ket_plus2		= "";
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$Tambahan.$ket_plus;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			

			// echo "Masuk Save";
			// exit;
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'eccentric reducer',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'diameter2'				=> $data['diameter2'],
				'panjang'				=> $data['panjang'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter3);
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
				history('Edit Est Eccentric Reducer code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='eccentric reducer' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation Eccentric Reducer',
				'action'		=> 'eccentric_reducer_edit',
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
				
			$this->load->view('Component/edit/eccentric_reducer_edit', $data);
		}
	}
	
	public function elbow_mitter_edit(){
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

			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $DataSeries2[0]['liner'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$sudut			= $data['angle'];
			$radiusX		= $data['type_elbow'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdSudut		= sprintf('%03s',$sudut);
			$KdLiner		= $liner;
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OM-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "M-";
			}


			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-".$KdSudut."-".$radiusX.$Tambahan;
			// echo $kode_product; exit;

			// echo "Masuk Save";
			// exit;
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'elbow mitter',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'cust'					=> $custX,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				
				'type_elbow'			=> $data['type_elbow'],
				'angle'					=> $data['angle'],
				'radius'				=> $data['radius'],
				'panjang'				=> $data['panjang'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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

			// if(!empty($data['ListDetailAdd'])){
				// print_r($ArrDataAdd1);
			// }
			// if(!empty($data['ListDetailAdd2'])){
				// print_r($ArrDataAdd2);
			// }
			// if(!empty($data['ListDetailAdd3'])){
				// print_r($ArrDataAdd3);
			// }
			// if(!empty($data['ListDetailAdd4'])){
				// print_r($ArrDataAdd4);
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

				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product));

				$this->db->insert('component_header', $ArrHeader);
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
				update_berat_est($kode_product);
				history('Edit Est Elbow Mitter code : '.$kode_product.' to '.$kode_product);
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

			$ComponentDetailEksternal		= $this->db->query("SELECT * FROM component_detail WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalPlus	= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$ComponentDetailEksternalAdd	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
			$NumRowsEksternalAdd			= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
			$ComponentFooterEksternal		= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

			$ComponentDetailTopPlus		= $this->db->query("SELECT * FROM component_detail_plus WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$ComponentDetailTopAdd		= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->result_array();
			$NumRowsTopAdd				= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$id_product."' AND detail_name='TOPCOAT' ")->num_rows();

			//List Dropdown
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='elbow mitter' AND deleted='N'")->result_array();
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
				'title'				=> 'REVISED ELBOW MITTER ESTIMATE',
				'action'			=> 'elbow_mitter_edit',
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
				'footerStructure'		=> $ComponentFooterStructure,

				'detEksternal'			=> $ComponentDetailEksternal,
				'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
				'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
				'detEksternalNumRows'	=> $NumRowsEksternalAdd,
				'footerEksternal'		=> $ComponentFooterEksternal,

				'detTopPlus'		=> $ComponentDetailTopPlus,
				'detTopAdd'			=> $ComponentDetailTopAdd,
				'detTopNumRows'		=> $NumRowsTopAdd
			);
			$this->load->view('Component/edit/elbow_mitter_edit', $data);
		}
	}

	public function equal_tee_mould_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OT-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "T-";
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$Tambahan;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			

			// echo "Masuk Save";
			// exit;
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'equal tee mould',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'diameter'				=> $data['diameter'],
				'diameter2'				=> $data['diameter2'],
				'panjang'				=> $data['panjang'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				
				'wrap_length'			=> $data['wrap_length'],
				'area2'					=> $data['area2'],
				'high'					=> $data['high'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter3);
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
				history('Edit Est Equal Tee Mould code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='equal tee mould' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation Equal Tee Mould',
				'action'		=> 'equal_tee_mould_edit',
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
				
			$this->load->view('Component/edit/equal_tee_mould_edit', $data);
		}
	}
	
	public function equal_tee_slongsong_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OQ-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "Q-";
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$Tambahan;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			

			// echo "Masuk Save";
			// exit;
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'equal tee slongsong',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'diameter'				=> $data['diameter'],
				'diameter2'				=> $data['diameter2'],
				'panjang'				=> $data['panjang'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				
				'wrap_length'			=> $data['wrap_length'],
				'area2'					=> $data['area2'],
				'high'					=> $data['high'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter3);
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
				history('Edit Est Equal Tee Slongsong code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='equal tee slongsong' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation Equal Tee Slongsong',
				'action'		=> 'equal_tee_slongsong_edit',
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
				
			$this->load->view('Component/edit/equal_tee_slongsong_edit', $data);
		}
	}
	
	public function reducer_tee_mould_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OC-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "C-";
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$Tambahan;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			

			// echo "Masuk Save";
			// exit;
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'reducer tee mould',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'diameter'				=> $data['diameter'],
				'diameter2'				=> $data['diameter2'],
				'panjang'				=> $data['panjang'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				
				'wrap_length'			=> $data['wrap_length'],
				'wrap_length2'			=> $data['wrap_length2'],
				'area2'					=> $data['area2'],
				'high'					=> $data['high'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter3);
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
				history('Edit Est Reducer Tee Mould code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='reducer tee mould' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation Reducer Tee Mould',
				'action'		=> 'reducer_tee_mould_edit',
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
				
			$this->load->view('Component/edit/reducer_tee_mould_edit', $data);
		}
	}
	
	public function reducer_tee_slongsong_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];
			$diameter2		= $data['diameter2'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			$KdDiameter2	= sprintf('%04s',$diameter2);
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OL-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "L-";
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner."-DN".$KdDiameter2.$Tambahan;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			

			// echo "Masuk Save";
			// exit;
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'reducer tee slongsong',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'diameter'				=> $data['diameter'],
				'diameter2'				=> $data['diameter2'],
				'panjang'				=> $data['panjang'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				
				'wrap_length'			=> $data['wrap_length'],
				'wrap_length2'			=> $data['wrap_length2'],
				'area2'					=> $data['area2'],
				'high'					=> $data['high'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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
				$this->db->insert_batch('component_detail', $ArrDetail13);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus1);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus2);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus3);
				$this->db->insert_batch('component_detail_plus', $ArrDetailPlus4);
				$this->db->insert('component_footer', $ArrFooter);
				$this->db->insert('component_footer', $ArrFooter2);
				$this->db->insert('component_footer', $ArrFooter3);
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
				history('Edit Est Reducer Tee Slongsong code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='reducer tee slongsong' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation Reducer Tee Slongsong',
				'action'		=> 'reducer_tee_slongsong_edit',
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
				
			$this->load->view('Component/edit/reducer_tee_slongsong_edit', $data);
		}
	}
	
	public function end_cap_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OE-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "E-";
			}
			
			$ket_plus		= "";
			$ket_plus2		= "";
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner.$Tambahan.$ket_plus;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'end cap',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				'design'				=> $data['design'],
				'area'					=> $data['area'],
				
				'radius'				=> $data['radius'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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

				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product));
				
				$this->db->insert('component_header', $ArrHeader);
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
				history('Edit Est End Cap code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
			
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='end cap' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation End Cap Edit',
				'action'		=> 'end_cap_edit',
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
				
			$this->load->view('Component/edit/end_cap_edit', $data);
		}
	}
	
	public function blind_flange_edit(){
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
			// echo "<pre>";
			// print_r($ListDetail);
			// print_r($ListDetail2);
			// print_r($ListDetail2);
			// print_r($ListDetail3);  
			// exit; 
			$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

			$resin_sistem	= $DataSeries2[0]['resin_system'];
			$liner			= $data['ThLin'];
			$pressure		= $DataSeries2[0]['pressure'];
			$diameter		= $data['diameter'];
			$cust			= $data['cust'];

			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdDiameter		= sprintf('%04s',$diameter);
			$KdLiner		= ($liner == '2.45')?'2.5':$liner;
			
			if($cust == 'C100-1903000'){
				$Tambahan 	= "";
				$custX		= "";
				$kdx		= "OB-";
			}
			else{
				$Tambahan 	= "-".$cust;
				$custX		= $cust;
				$kdx		= "B-";
			}
			
			$ket_plus		= "";
			$ket_plus2		= "";
			if(!empty($data['ket_plus'])){
				$ket_plus		= "-".trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
				$ket_plus2		= trim(strtoupper(str_replace(str_split('\\/:*?"<>| '), '-', $data['ket_plus'])));
			}
			
			$kode_product	= $kdx.$KdResinSystem."PN".$KdPressure."DN".$KdDiameter."-".$KdLiner.$Tambahan.$ket_plus;
			// echo $kode_product; exit;
			$srcType		= "SELECT id_product FROM component_header WHERE id_product='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			
			$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
			$DataApp	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();
			
			$ArrHeader	= array(
				'id_product'			=> $kode_product,
				'parent_product'		=> 'blind flange',
				'nm_product'			=> $data['top_type'],
				'resin_sistem'			=> $resin_sistem,
				'pressure'				=> $pressure,
				'standart_code'			=> $data['standart_code'],
				'cust'					=> $custX,
				'ket_plus'				=> $ket_plus2,
				'diameter'				=> $data['diameter'],
				'series'				=> $data['series'],
				'liner'					=> $data['ThLin'],					
				'aplikasi_product'		=> $data['top_app'],
				'criminal_barier'		=> $data['criminal_barier'],
				'vacum_rate'			=> $data['vacum_rate'],
				'stiffness'				=> $DataApp[0]['data2'],
				'design_life'			=> $data['design_life'],
				'design'				=> $data['design'],
				'area'					=> $data['area'],
				
				'flange_od'				=> $data['flange_od'],
				
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
				$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
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

			$ArrFooter3	= array(
					'id_product'	=> $kode_product,
					'detail_name'	=> $data['detail_name3'],
					'total'			=> $data['thickEks'],
					'min'			=> $data['minEks'],
					'max'			=> $data['maxEks'],
					'hasil'			=> $data['hasilEks']
			);
			// print_r($ArrFooter);
			// print_r($ArrFooter2);
			// print_r($ArrFooter3);
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

				//Delete
				$this->db->delete('component_header', array('id_product' => $kode_product));
				$this->db->delete('component_detail', array('id_product' => $kode_product));
				$this->db->delete('component_detail_plus', array('id_product' => $kode_product));
				$this->db->delete('component_detail_add', array('id_product' => $kode_product));
				$this->db->delete('component_footer', array('id_product' => $kode_product));
				
				$this->db->insert('component_header', $ArrHeader);
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
				history('Edit Est Blind Flange code : '.$kode_product);
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
			$ListSeries					= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
			
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
			$ListProduct		= $this->db->query("SELECT * FROM product WHERE parent_product='blind flange' AND deleted='N'")->result_array();
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner				= $this->db->query("SELECT * FROM list_thickness WHERE layer ='liner' ORDER BY tampil ASC")->result_array();

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
				'title'			=> 'Revision Estimation Blind Flange Edit',
				'action'		=> 'blind_flange_edit',
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
				
			$this->load->view('Component/edit/blindflange_edit', $data);
		}
	}
	
}