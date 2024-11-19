<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Material extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
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

		// $get_Data			= $this->master_model->getData('raw_materials');
		$get_Data			= $this->db->query("SELECT*FROM raw_materials WHERE `delete` = 'N' ORDER BY nm_material ASC")->result();
		$menu_akses			= $this->master_model->getMenu();

		$data = array(
			'title'			=> 'Indeks Of Material',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Material');
		$this->load->view('Material/index',$data);
	}

	public function index2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$menu_akses			= $this->master_model->getMenu();

		$data = array(
			'title'			=> 'Indeks Of Material',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Material');
		$this->load->view('Material/index_new',$data);
	}

	public function add(){
		if($this->input->post()){
			$Arr_Kembali			= array();
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			$UserName 				= $data_session['ORI_User']['username'];
			$DateTime 				= date('Y-m-d H:i:s');;

			$nm_material			= strtoupper($data['nm_material']);
			$nm_dagang				= strtoupper($data['nm_dagang']);
			$nm_international		= strtoupper($data['nm_international']);

			$idmaterial				= $data['idmaterial'];
			$satuan_kg				= $data['satuan_kg'];
			$saldo_kg				= $data['saldo_kg'];

			$id_category			= $data['id_category'];
			$dataNmCty				= $this->db->query("SELECT*FROM raw_categories WHERE id_category='".$id_category."'")->result_array();
			$id_satuan				= $data['id_satuan'];
			$dataNmSat				= $this->db->query("SELECT*FROM raw_pieces WHERE id_satuan='".$id_satuan."'")->result_array();
			$nilai_konversi			= $data['nilai_konversi'];
			$price_ref_estimation	= $data['price_ref_estimation'];
			$price_ref_purchase		= $data['price_ref_purchase'];
			$descr					= $data['descr'];
			$detSUpplier			= $data['ListDetail'];
			$detListDetail_bq		= $data['ListDetail_bq'];
			$detListDetail_en		= $data['ListDetail_en'];

			$Ym						= date('ym');

			//pengurutan kode
			$srcMtr			= "SELECT MAX(id_material) as maxP FROM raw_materials WHERE id_material LIKE 'MTL-".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$id_material	= "MTL-".$Ym.$urut2;

			$kdYMH	= date('ymd');
			$ArrSup	= array();
			$ArrSupLog	= array();

			$no		= 0;
			foreach($detSUpplier AS $val => $valx){
				$no++;
				$flag = 'N';
					if(!empty($valx['flag_active'])){
						$flag = 'Y';
					}

				$sqlUrutan		= "SELECT MAX(id_supplier_material) AS maxEX FROM raw_material_supplier WHERE id_material='".$id_material."' AND id_supplier='".$valx['id_supplier']."' ";
				$urutanEncode	= $this->db->query($sqlUrutan)->result_array();
				$urutaN			= $urutanEncode[0]['maxEX'];
				$ListUrt		= explode("/", $urutaN);

				$urutX	= ($urutaN == null || $urutaN == '')?$no:$ListUrt[2];
				// print_r($urutanEncode);
				// echo $urutX; exit;

				$ChNmSUpp	= $this->db->query("SELECT * FROM supplier WHERE id_supplier='".$valx['id_supplier']."' LIMIT 1")->result_array();
				$ArrSup[$val]['id_supplier_material'] 	= $id_material."/".$valx['id_supplier']."/".$urutX."/".$kdYMH;
				$ArrSup[$val]['id_material'] 			= $id_material;
				$ArrSup[$val]['nm_material'] 			= $nm_material;
				$ArrSup[$val]['id_supplier'] 			= $valx['id_supplier'];
				$ArrSup[$val]['nm_supplier'] 			= $ChNmSUpp[0]['nm_supplier'];
				$ArrSup[$val]['price'] 					= str_replace(',', '', $valx['price']);
				$ArrSup[$val]['valid_until'] 			= $valx['valid_until'];
				$ArrSup[$val]['descr'] 					= $valx['descr'];
				$ArrSup[$val]['flag_active'] 			= $flag;
				$ArrSup[$val]['created_by'] 			= $UserName;
				$ArrSup[$val]['created_date'] 			= $DateTime;

				$ArrSupLog[$val]['id_supplier_material'] 	= $id_material."/".$valx['id_supplier']."/".$urutX."/".$kdYMH;
				$ArrSupLog[$val]['id_material'] 			= $id_material;
				$ArrSupLog[$val]['nm_material'] 			= $nm_material;
				$ArrSupLog[$val]['id_supplier'] 			= $valx['id_supplier'];
				$ArrSupLog[$val]['nm_supplier'] 			= $ChNmSUpp[0]['nm_supplier'];
				$ArrSupLog[$val]['price'] 					= str_replace(',', '', $valx['price']);
				$ArrSupLog[$val]['valid_until'] 			= $valx['valid_until'];
				$ArrSupLog[$val]['created_by'] 				= $UserName;
				$ArrSupLog[$val]['created_date'] 			= $DateTime;
			}

			// echo "<pre>";
			// print_r($ArrSup);
			// print_r($ArrSupLog);			exit;

			//check nama name material
			$qNmType	= "SELECT * FROM raw_materials WHERE nm_material = '".$nm_material."' ";
			$numType	= $this->db->query($qNmType)->num_rows();
			// echo $numType; exit;
			$data	= array(
				'id_material' 			=> $id_material,
				'idmaterial' 			=> $idmaterial,
				'nm_material' 			=> $nm_material,
				'nm_dagang' 			=> $nm_dagang,
				'nm_international' 		=> $nm_international,
				'id_category' 			=> $id_category,
				'nm_category' 			=> $dataNmCty[0]['category'],
				'id_satuan' 			=> $id_satuan,
				'satuan_kg' 			=> $satuan_kg,
				'saldo_kg' 				=> $saldo_kg,
				'cost_satuan' 			=> strtolower($dataNmSat[0]['kode_satuan']),
				'nilai_konversi' 		=> str_replace(',', '', $nilai_konversi),
				'price_ref_estimation' 	=> str_replace(',', '', $price_ref_estimation),
				'price_ref_purchase' 	=> str_replace(',', '', $price_ref_purchase),
				'descr' 				=> $descr,
				'flag_active' 			=> 'Y',
				'created_by' 			=> $UserName,
				'created_date' 			=> $DateTime
			);

			$dataStock	= array(
				'id_material' 			=> $id_material,
				'idmaterial' 			=> $idmaterial,
				'nm_material' 			=> $nm_material,
				'id_category' 			=> $id_category,
				'nm_category' 			=> $dataNmCty[0]['category'],
				'id_gudang' 			=> 2,
				'kd_gudang' 			=> 'OPC2',
				'update_by' 			=> $UserName,
				'update_date' 			=> $DateTime
			);

			// echo "<pre>"; print_r($data);

			$ArrdetListDetail_en	= array();
			foreach($detListDetail_en AS $val => $valx){
				$flagEn = 'N';
					if(!empty($valx['flag_active_en'])){
						$flagEn = 'Y';
					}
				$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_en']."' AND type='ENG' LIMIT 1")->result_array();

				$ArrdetListDetail_en[$val]['id_material'] 			= $id_material;
				$ArrdetListDetail_en[$val]['id_category_standard'] 	= $valx['id_category_standard_en'];
				$ArrdetListDetail_en[$val]['id_category'] 			= $id_category;
				$ArrdetListDetail_en[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
				$ArrdetListDetail_en[$val]['nilai_standard'] 		= str_replace(',', '', $valx['nilai_standard_en']);
				$ArrdetListDetail_en[$val]['descr'] 				= $valx['descr_en'];
				$ArrdetListDetail_en[$val]['flag_active'] 			= $flagEn;
				$ArrdetListDetail_en[$val]['created_by'] 			= $UserName;
				$ArrdetListDetail_en[$val]['created_date'] 			= $DateTime;
			}

			// echo "<pre>";
			// print_r($ArrdetListDetail_en);

			$ArrdetListDetail_bq	= array();
			foreach($detListDetail_bq AS $val => $valx){
				$flagBq = 'N';
					if(!empty($valx['flag_active_bq'])){
						$flagBq = 'Y';
					}
				$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_bq']."' AND type='BQ' LIMIT 1")->result_array();

				$ArrdetListDetail_bq[$val]['id_material'] 			= $id_material;
				$ArrdetListDetail_bq[$val]['id_category_standard'] 	= $valx['id_category_standard_bq'];
				$ArrdetListDetail_bq[$val]['id_category'] 			= $id_category;
				$ArrdetListDetail_bq[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
				$ArrdetListDetail_bq[$val]['nilai_standard'] 		= str_replace(',','',$valx['nilai_standard_bq']);
				$ArrdetListDetail_bq[$val]['descr'] 				= $valx['descr_bq'];
				$ArrdetListDetail_bq[$val]['flag_active'] 			= $flagBq;
				$ArrdetListDetail_bq[$val]['created_by'] 			= $UserName;
				$ArrdetListDetail_bq[$val]['created_date'] 			= $DateTime;
			}

			// echo "<pre>";
			// print_r($ArrdetListDetail_bq);

			// exit;

			if($numType > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Material name already exists. Please check back ...'
				);
			}
			else{
				$this->db->trans_start();
				$this->db->insert('raw_materials', $data);
				$this->db->insert('warehouse_stock', $dataStock);
				$this->db->insert_batch('raw_material_supplier', $ArrSup);
				$this->db->insert_batch('raw_material_supplier_log', $ArrSupLog);
				$this->db->insert_batch('raw_material_engineer_standard', $ArrdetListDetail_en);
				$this->db->insert_batch('raw_material_bq_standard', $ArrdetListDetail_bq);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Insert material data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Insert material data success. Thanks ...',
						'status'	=> 1
					);
					history('Input Material '.$id_material.' with username : '.$data_session['ORI_User']['username']);
				}
			}
			echo json_encode($Arr_Kembali);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$arr_Where			= array('flag_active'=>'1');
			$get_Data			= $this->master_model->getMenu($arr_Where);
			// $getType			= $this->master_model->getArray('raw_categories',array(),'id_category','category');
			$getType			= $this->db->query("SELECT*FROM raw_categories ORDER BY category ASC")->result_array();
			// $getPiece			= $this->master_model->getArray('raw_pieces',array(),'id_satuan','nama_satuan');
			$getPiece			= $this->db->query("SELECT*FROM raw_pieces ORDER BY nama_satuan ASC")->result_array();
			$data = array(
				'title'			=> 'Add Type Material',
				'action'		=> 'add',
				'data_type'		=> $getType,
				'data_pieces'	=> $getPiece
			);
			$this->load->view('Material/add',$data);
		}
	}
	public function edit(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;

			$id_material			= strtoupper($data['id_material']);
			$nm_material			= strtoupper($data['nm_material']);
			$nm_dagang				= strtoupper($data['nm_dagang']);
			$nm_international		= strtoupper($data['nm_international']);
			$id_category			= $data['id_category'];
			$id_satuan				= $data['id_satuan'];
			$dataNmSat				= $this->db->query("SELECT*FROM raw_pieces WHERE id_satuan='".$id_satuan."'")->result_array();
			$nilai_konversi			= $data['nilai_konversi'];
			$price_ref_estimation	= $data['price_ref_estimation'];
			$price_ref_purchase		= $data['price_ref_purchase'];
			$descr					= $data['descr'];

			$numberMax				= $data['numberMax'];
			$numberMax_en			= $data['numberMax_en'];
			$numberMax_bq			= $data['numberMax_bq'];


			if($numberMax != 0){
				$detSUpplier			= $data['ListDetail'];
			}

			if($numberMax_bq != 0){
				$detListDetail_bq		= $data['ListDetail_bq'];
			}

			if($numberMax_en != 0){
				$detListDetail_en		= $data['ListDetail_en'];
			}

			$flaG = 'N';
				if(!empty($data['flag_active'])){
					$flaG = 'Y';
				}

			$Arr_Update	= array(
				'id_material' 			=> $id_material,
				'nm_dagang' 			=> $nm_dagang,
				'nm_international' 		=> $nm_international,
				'id_satuan' 			=> $id_satuan,
				'id_category' 			=> $id_category,
				'cost_satuan' 			=> strtolower($dataNmSat[0]['kode_satuan']),
				'nilai_konversi' 		=> str_replace(',', '', $nilai_konversi),
				'price_ref_estimation' 	=> str_replace(',', '', $price_ref_estimation),
				'price_ref_purchase' 	=> str_replace(',', '', $price_ref_purchase),
				'descr' 				=> $descr,
				'flag_active' 			=> $flaG,
				'modified_by' 			=> $data_session['ORI_User']['username'],
				'modified_date' 		=> date('Y-m-d H:i:s')
			);
			// print_r($Arr_Update);

			if($numberMax != 0){
				$kdYMH	= date('ymd');
				$ArrSup	= array();
				$ArrSupLog	= array();
				$no = 0;
				foreach($detSUpplier AS $val => $valx){
					$no++;
					$flag = 'N';
						if(!empty($valx['flag_active'])){
							$flag = 'Y';
						}

					$sqlUrutan		= "SELECT MAX(id_supplier_material) AS maxEX FROM raw_material_supplier WHERE id_material='".$id_material."' ";
					$urutanEncode	= $this->db->query($sqlUrutan)->result_array();
					$urutaN			= $urutanEncode[0]['maxEX'];
					$ListUrt		= explode("/", $urutaN);

					$urutX	= ($urutaN == null || $urutaN == '')?$no:($ListUrt[2] + 1);
					// print_r($urutanEncode);
					// echo $urutX; exit;

					$ChNmSUpp	= $this->db->query("SELECT * FROM supplier WHERE id_supplier='".$valx['id_supplier']."' LIMIT 1")->result_array();
					$ArrSup[$val]['id_supplier_material'] 	= $id_material."/".$valx['id_supplier']."/".$urutX."/".$kdYMH;
					$ArrSup[$val]['id_material'] 	= $id_material;
					$ArrSup[$val]['nm_material'] 	= $nm_material;
					$ArrSup[$val]['id_supplier'] 	= $valx['id_supplier'];
					$ArrSup[$val]['nm_supplier'] 	= $ChNmSUpp[0]['nm_supplier'];
					$ArrSup[$val]['price'] 			= str_replace(',', '', $valx['price']);
					$ArrSup[$val]['valid_until'] 	= $valx['valid_until'];
					$ArrSup[$val]['descr'] 			= $valx['descr'];
					$ArrSup[$val]['flag_active'] 	= $flag;
					$ArrSup[$val]['created_by'] 	= $data_session['ORI_User']['username'];
					$ArrSup[$val]['created_date'] 	= date('Y-m-d H:i:s');

					$ArrSupLog[$val]['id_supplier_material'] 	= $id_material."/".$valx['id_supplier']."/".$urutX."/".$kdYMH;
					$ArrSupLog[$val]['id_material'] 			= $id_material;
					$ArrSupLog[$val]['nm_material'] 			= $nm_material;
					$ArrSupLog[$val]['id_supplier'] 			= $valx['id_supplier'];
					$ArrSupLog[$val]['nm_supplier'] 			= $ChNmSUpp[0]['nm_supplier'];
					$ArrSupLog[$val]['price'] 					= str_replace(',', '', $valx['price']);
					$ArrSupLog[$val]['valid_until'] 			= $valx['valid_until'];
					$ArrSupLog[$val]['created_by'] 				= $data_session['ORI_User']['username'];
					$ArrSupLog[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}

				// print_r($ArrSup);
				// print_r($ArrSupLog);
			}

			if($numberMax_en != 0){
				$ArrdetListDetail_en	= array();
				foreach($detListDetail_en AS $val => $valx){
					$flagEn = 'N';
						if(!empty($valx['flag_active_en'])){
							$flagEn = 'Y';
						}
					$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_en']."' AND type='ENG' LIMIT 1")->result_array();

					$ArrdetListDetail_en[$val]['id_material'] 			= $id_material;
					$ArrdetListDetail_en[$val]['id_category_standard'] 	= $valx['id_category_standard_en'];
					$ArrdetListDetail_en[$val]['id_category'] 			= $id_category;
					$ArrdetListDetail_en[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
					$ArrdetListDetail_en[$val]['nilai_standard'] 		= str_replace(',', '', $valx['nilai_standard_en']);
					$ArrdetListDetail_en[$val]['descr'] 				= $valx['descr_en'];
					$ArrdetListDetail_en[$val]['flag_active'] 			= $flagEn;
					$ArrdetListDetail_en[$val]['created_by'] 			= $data_session['ORI_User']['username'];
					$ArrdetListDetail_en[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}

				// print_r($ArrdetListDetail_en);
			}

			if($numberMax_bq != 0){
				$ArrdetListDetail_bq	= array();
				foreach($detListDetail_bq AS $val => $valx){
					$flagBq = 'N';
						if(!empty($valx['flag_active_bq'])){
							$flagBq = 'Y';
						}
					$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_bq']."' AND type='BQ' LIMIT 1")->result_array();

					$ArrdetListDetail_bq[$val]['id_material'] 			= $id_material;
					$ArrdetListDetail_bq[$val]['id_category_standard'] 	= $valx['id_category_standard_bq'];
					$ArrdetListDetail_bq[$val]['id_category'] 			= $id_category;
					$ArrdetListDetail_bq[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
					$ArrdetListDetail_bq[$val]['nilai_standard'] 		= str_replace(',','',$valx['nilai_standard_bq']);
					$ArrdetListDetail_bq[$val]['descr'] 				= $valx['descr_bq'];
					$ArrdetListDetail_bq[$val]['flag_active'] 			= $flagBq;
					$ArrdetListDetail_bq[$val]['created_by'] 			= $data_session['ORI_User']['username'];
					$ArrdetListDetail_bq[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}

				// print_r($ArrdetListDetail_bq);
			}

			// exit;

			$this->db->trans_start();
				$this->db->where('id_material', $id_material);
				$this->db->update('raw_materials', $Arr_Update);

				if($numberMax != 0){
					$this->db->insert_batch('raw_material_supplier', $ArrSup);
					$this->db->insert_batch('raw_material_supplier_log', $ArrSupLog);
				}
				if($numberMax_en != 0){
					$this->db->insert_batch('raw_material_engineer_standard', $ArrdetListDetail_en);
				}
				if($numberMax_bq != 0){
					$this->db->insert_batch('raw_material_bq_standard', $ArrdetListDetail_bq);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update material data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update material data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Material '.$id_material.' with username : '.$data_session['ORI_User']['username']);
			}
			// print_r($Arr_Data); exit;
			echo json_encode($Arr_Data);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}

			$id = $this->uri->segment(3);

			$detail		= $this->db->query("SELECT * FROM raw_materials WHERE id_material = '".$id."' ")->result_array();
			$getType	= $this->db->query("SELECT*FROM raw_categories ORDER BY category ASC")->result_array();
			$getPiece	= $this->db->query("SELECT*FROM raw_pieces ORDER BY nama_satuan ASC")->result_array();
			$detailBQ	= $this->db->query("SELECT * FROM raw_material_bq_standard WHERE id_material = '".$id."' ")->result_array();
			$detailEn	= $this->db->query("SELECT * FROM raw_material_engineer_standard WHERE id_material = '".$id."' ")->result_array();
			$Supply		= $this->db->query("SELECT * FROM raw_material_supplier WHERE id_material = '".$id."' ")->result_array();
			$ListSup		= $this->db->query("SELECT * FROM supplier")->result_array();
			$data = array(
				'title'			=> 'Edit Type Material',
				'action'		=> 'edit',
				'row'			=> $detail,
				'data_type'		=> $getType,
				'data_pieces'	=> $getPiece,
				'detailEn'		=> $detailEn,
				'detailBQ'		=> $detailBQ,
				'Supply'		=> $Supply,
				'ListSup'		=> $ListSup
			);

			$this->load->view('Material/edit',$data);
		}
	}

	public function editData(){
		$data = $this->input->post();
		$data_session			= $this->session->userdata;

		$id_material		= $data['id_material'];
		if(!empty($data['NmSupply'])){
			if($data['NmSupply'] != 0){
				$detSUpplier		= $data['EdListDetail_sp'];
			}
		}
		if($data['NmdetailEn'] != 0){
			$detListDetail_en	= $data['EdListDetail_en'];
		}
		if($data['NmdetailBQ'] != 0){
			$detListDetail_bq	= $data['EdListDetail_bq'];
		}
		if($data['NmSubMat'] != 0){
			$detListDetail_sub	= $data['EdListDetail_sub'];
		}

		// print_r($detSUpplier);
		// print_r($detListDetail_en);
		// print_r($detListDetail_bq);
		// exit;
		if(!empty($data['NmSupply'])){
			if($data['NmSupply'] != 0){
				$ArrSup	= array();
				$ArrSupLog	= array();
				foreach($detSUpplier AS $val => $valx){
					$flag = 'N';
						if(!empty($valx['flag_active'])){
							$flag = 'Y';
						}


					$ArrSup[$val]['id_supplier_material'] 	= $valx['id_supplier_material'];
					$ArrSup[$val]['price'] 					= str_replace(',', '', $valx['price']);
					$ArrSup[$val]['valid_until'] 			= $valx['valid_until'];
					$ArrSup[$val]['descr'] 					= $valx['descr'];
					$ArrSup[$val]['flag_active'] 			= $flag;
					$ArrSup[$val]['modify_by'] 				= $data_session['ORI_User']['username'];
					$ArrSup[$val]['modify_date'] 			= date('Y-m-d H:i:s');

					$ArrSupLog[$val]['id_supplier_material'] 	= $valx['id_supplier_material'];
					$ArrSupLog[$val]['id_material'] 			= $valx['id_material'];
					$ArrSupLog[$val]['nm_material'] 			= $valx['nm_material'];
					$ArrSupLog[$val]['id_supplier'] 			= $valx['id_supplier'];
					$ArrSupLog[$val]['nm_supplier'] 			= $valx['nm_supplier'];
					$ArrSupLog[$val]['price'] 					= str_replace(',', '', $valx['price']);
					$ArrSupLog[$val]['valid_until'] 			= $valx['valid_until'];
					$ArrSupLog[$val]['created_by'] 				= $data_session['ORI_User']['username'];
					$ArrSupLog[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}
				// print_r($ArrSup);
				// print_r($ArrSupLog);
			}
		}

		if($data['NmdetailEn'] != 0){
			$ArrdetListDetail_en	= array();
			foreach($detListDetail_en AS $val => $valx){
				$flagEn = 'N';
					if(!empty($valx['flag_active'])){
						$flagEn = 'Y';
					}

				$ArrdetListDetail_en[$val]['id_standard'] 			= $valx['id_standard'];
				$ArrdetListDetail_en[$val]['nilai_standard'] 		= str_replace(',', '', $valx['nilai_standard']);
				$ArrdetListDetail_en[$val]['descr'] 				= $valx['descr'];
				$ArrdetListDetail_en[$val]['flag_active'] 			= $flagEn;
				$ArrdetListDetail_en[$val]['modified_by'] 			= $data_session['ORI_User']['username'];
				$ArrdetListDetail_en[$val]['modified_by'] 			= date('Y-m-d H:i:s');
			}
			// print_r($ArrdetListDetail_en);
		}

		if($data['NmdetailBQ'] != 0){
			$ArrdetListDetail_bq	= array();
			foreach($detListDetail_bq AS $val => $valx){
				$flagBq = 'N';
					if(!empty($valx['flag_active'])){
						$flagBq = 'Y';
					}

				$ArrdetListDetail_bq[$val]['id_standard'] 			= $valx['id_standard'];
				$ArrdetListDetail_bq[$val]['nilai_standard'] 		= str_replace(',','',$valx['nilai_standard']);
				$ArrdetListDetail_bq[$val]['descr'] 				= $valx['descr'];
				$ArrdetListDetail_bq[$val]['flag_active'] 			= $flagBq;
				$ArrdetListDetail_bq[$val]['modified_by'] 			= $data_session['ORI_User']['username'];
				$ArrdetListDetail_bq[$val]['modified_by'] 			= date('Y-m-d H:i:s');
			}
			// print_r($ArrdetListDetail_bq);
		}

		if($data['NmSubMat'] != 0){
			$ArrdetdetListDetail_sub	= array();
			foreach($detListDetail_sub AS $val => $valx){
				$flagSub = 'N';
					if(!empty($valx['flag_active'])){
						$flagSub = 'Y';
					}

				$ArrdetdetListDetail_sub[$val]['id_subtitusi'] 			= $valx['id_subtitusi'];
				$ArrdetdetListDetail_sub[$val]['descr'] 				= $valx['descr'];
				$ArrdetdetListDetail_sub[$val]['flag_active'] 			= $flagSub;
				$ArrdetdetListDetail_sub[$val]['modified_by'] 			= $data_session['ORI_User']['username'];
				$ArrdetdetListDetail_sub[$val]['modified_by'] 			= date('Y-m-d H:i:s');
			}
			// print_r($ArrdetdetListDetail_sub);
		}

		// exit;

		$this->db->trans_start();
		if(!empty($data['NmSupply'])){
			if($data['NmSupply'] != 0){
				$this->db->insert_batch('raw_material_supplier_log', $ArrSupLog);
				$this->db->update_batch('raw_material_supplier', $ArrSup, 'id_supplier_material');
			}
		}
		if($data['NmdetailBQ'] != 0){
			$this->db->update_batch('raw_material_bq_standard', $ArrdetListDetail_bq, 'id_standard');
		}
		if($data['NmdetailEn'] != 0){
			$this->db->update_batch('raw_material_engineer_standard', $ArrdetListDetail_en, 'id_standard');
		}
		if($data['NmSubMat'] != 0){
			$this->db->update_batch('raw_material_subtitutions', $ArrdetdetListDetail_sub, 'id_subtitusi');
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update type material data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update type material data success. Thanks ...',
				'status'	=> 1
			);
			history('Update Material Standard '.$id_material);
		}

		// print_r($Arr_Data); exit;
		echo json_encode($Arr_Data);
	}

	function hapus(){
		$idMaterial = $this->uri->segment(3);
		// echo $idCategory; exit;
		$data_session			= $this->session->userdata;

		$Arr_Delete = array(
			'delete' 		=> "Y",
			'flag_active' 	=> "N",
			'delete_by' 	=> $data_session['ORI_User']['username'],
			'delete_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
		$this->db->update('raw_materials', $Arr_Delete, array('id_material' => $idMaterial));
		$this->db->update('raw_material_engineer_standard', $Arr_Delete, array('id_material' => $idMaterial));
		$this->db->update('raw_material_bq_standard', $Arr_Delete, array('id_material' => $idMaterial));
		$this->db->update('raw_material_supplier', $Arr_Delete, array('id_material' => $idMaterial));
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete type material data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete type material data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Type Material with ID : '.$idMaterial);
		}
		echo json_encode($Arr_Data);
	}

	public function getSupplier(){
		$sqlSup	= "SELECT * FROM supplier WHERE sts_aktif='aktif' ORDER BY nm_supplier ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Supplier</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_supplier']."'>".$valx['nm_supplier']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getSupplierED(){
		$materialID = $this->input->post('id_material');

		$qSupDipakai		= "SELECT id_supplier FROM raw_material_supplier WHERE id_material = '".$materialID."'";
		$dataSupDipakai	= $this->db->query($qSupDipakai)->result_array();
		$dtListArray = array();
		foreach($dataSupDipakai AS $val => $valx){
			$dtListArray[$val] = $valx['id_supplier'];
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$sqlSup	= "SELECT * FROM supplier WHERE id_supplier NOT IN ".$dtImplode." AND sts_aktif='aktif' ORDER BY nm_supplier ASC";

		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Supplier</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_supplier']."'>".$valx['nm_supplier']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getId_category_standard_en(){
		$IDCategory = $this->input->post('id_category');
		$sqlSup	= "SELECT * FROM raw_category_standard WHERE id_category='".$IDCategory."' AND type='ENG'  ORDER BY nm_category_standard ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Standard En</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_category_standard']."'>".$valx['nm_category_standard']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getId_category_standard_enED(){
		$IDCategory = $this->input->post('id_category');
		$materialID = $this->input->post('id_material');

		$qSupDipakai		= "SELECT id_category_standard FROM raw_material_engineer_standard WHERE id_material = '".$materialID."'";
		$dataSupDipakai	= $this->db->query($qSupDipakai)->result_array();
		$dtListArray = array();
		foreach($dataSupDipakai AS $val => $valx){
			$dtListArray[$val] = $valx['id_category_standard'];
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$sqlSup	= "SELECT * FROM raw_category_standard WHERE id_category_standard NOT IN ".$dtImplode." AND id_category='".$IDCategory."' AND type='ENG'  ORDER BY nm_category_standard ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Standard En</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_category_standard']."'>".$valx['nm_category_standard']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getId_category_standard_bq(){
		$IDCategory = $this->input->post('id_category');
		$sqlSup	= "SELECT * FROM raw_category_standard WHERE id_category='".$IDCategory."' AND type='BQ'  ORDER BY nm_category_standard ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Standard Bq</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_category_standard']."'>".ucfirst(strtolower($valx['nm_category_standard']))."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getMaterialED(){
		$id_category = $this->input->post('id_category');
		$materialID = $this->input->post('id_material');

		$qSupDipakai	= "SELECT id_material_subtitusi FROM raw_material_subtitutions WHERE id_material = '".$materialID."'";
		$dataSupDipakai	= $this->db->query($qSupDipakai)->result_array();
		$dtListArray 	= array();
		foreach($dataSupDipakai AS $val => $valx){
			$dtExp	= explode('/', $valx['id_material_subtitusi']);
			$dtListArray[$val] = $dtExp[0];
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$sqlSup	= "SELECT * FROM raw_materials WHERE id_material NOT IN ".$dtImplode." AND id_material NOT IN ('".$materialID."') AND id_category='".$id_category."'  ORDER BY nm_material ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Material</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getId_category_standard_bqED(){
		$IDCategory = $this->input->post('id_category');
		$materialID = $this->input->post('id_material');

		$qSupDipakai		= "SELECT id_category_standard FROM raw_material_bq_standard WHERE id_material = '".$materialID."'";

		$dataSupDipakai	= $this->db->query($qSupDipakai)->result_array();
		$dtListArray = array();
		foreach($dataSupDipakai AS $val => $valx){
			$dtListArray[$val] = $valx['id_category_standard'];
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$sqlSup	= "SELECT * FROM raw_category_standard WHERE id_category_standard NOT IN ".$dtImplode." AND id_category='".$IDCategory."' AND type='BQ'  ORDER BY nm_category_standard ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();
		// echo $sqlSup;
		$option	= "<option value='0'>Select An Standard Bq</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_category_standard']."'>".ucfirst(strtolower($valx['nm_category_standard']))."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function modalDetail(){
		$this->load->view('Material/modalDetail');
	}

	public function add2(){
		if($this->input->post()){
			$Arr_Kembali			= array();
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;

			$numberMax_en			= $data['numberMax_en'];
			$numberMax_bq			= $data['numberMax_bq'];

			// echo $numberMax_en."-".$numberMax_bq;
			// exit;

			$nm_material			= $data['nm_material'];
			$nm_dagang				= $data['nm_dagang'];
			$nm_international		= $data['nm_international'];
			$idmaterial				= $data['idmaterial'];
			$id_accurate				= $data['id_accurate'];
			$satuan_kg				= $data['satuan_kg'];
			$kg_per_bulan				= $data['kg_per_bulan'];
			$id_category			= $data['id_category'];
			$dataNmCty				= $this->db->query("SELECT*FROM raw_categories WHERE id_category='".$id_category."'")->result_array();
			$id_satuan				= $data['id_satuan'];
			$id_packing				= $data['id_packing'];
			$dataNmSat				= $this->db->query("SELECT*FROM raw_pieces WHERE id_satuan='".$id_satuan."'")->result_array();
			$nilai_konversi			= $data['nilai_konversi'];
			$safety_stock			= $data['safety_stock'];
			$max_stock			    = $data['max_stock'];
			// $price_ref_estimation	= $data['price_ref_estimation'];
			// $price_ref_purchase		= $data['price_ref_purchase'];
			// $exp_price_ref_est		= $data['exp_price_ref_est'];
			// $exp_price_ref_pur		= $data['exp_price_ref_pur'];
			$descr					= $data['descr'];

			$id_warehouse			= $data['id_warehouse'];

			if($numberMax_bq > 0){
				$detListDetail_bq		= $data['ListDetail_bq'];
			}
			if($numberMax_en > 0){
				$detListDetail_en		= $data['ListDetail_en'];
			}

			$Ym						= date('ym');

			//pengurutan kode
			$srcMtr			= "SELECT MAX(id_material) as maxP FROM raw_materials WHERE id_material LIKE 'MTL-".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$id_material	= "MTL-".$Ym.$urut2;

			//check nama name material
			$qNmType	= "SELECT * FROM raw_materials WHERE idmaterial = '".$idmaterial."' ";
			$numType	= $this->db->query($qNmType)->num_rows();
			// echo $numType; exit;
			$data	= array(
				'id_material' 			=> $id_material,
				'id_accurate' 			=> $id_accurate,
				'idmaterial' 			=> $idmaterial,
				'nm_material' 			=> $nm_material,
				'nm_dagang' 			=> $nm_dagang,
				'nm_international' 		=> $nm_international,
				'id_category' 			=> $id_category,
				'nm_category' 			=> $dataNmCty[0]['category'],
				'id_satuan' 			=> $id_satuan,
				'id_packing' 			=> $id_packing,
				'satuan_kg' 			=> $satuan_kg,
				'kg_per_bulan' 			=> str_replace(',', '', $kg_per_bulan),
				'cost_satuan' 			=> strtoupper($dataNmSat[0]['kode_satuan']),
				'safety_stock' 		=> str_replace(',', '', $safety_stock),				
				'max_stock' 		=> str_replace(',', '', $max_stock),
				'nilai_konversi' 		=> str_replace(',', '', $nilai_konversi),
				// 'price_ref_estimation' 	=> str_replace(',', '', $price_ref_estimation),
				// 'price_ref_purchase' 	=> str_replace(',', '', $price_ref_purchase),
				// 'exp_price_ref_est' 	=> $exp_price_ref_est,
				// 'exp_price_ref_pur' 	=> $exp_price_ref_pur,
				'descr' 				=> $descr,
				'flag_active' 			=> 'Y',
				'created_by' 			=> $data_session['ORI_User']['username'],
				'created_date' 			=> date('Y-m-d H:i:s')
			);

			$data2	= array(
				'id_material' 			=> $id_material,
				'idmaterial' 			=> $idmaterial,
				'nm_material' 			=> $nm_material,
				'nm_dagang' 			=> $nm_dagang,
				'nm_international' 		=> $nm_international,
				'id_category' 			=> $id_category,
				'nm_category' 			=> $dataNmCty[0]['category'],
				'id_satuan' 			=> $id_satuan,
				'satuan_kg' 			=> str_replace(',', '', $satuan_kg),
				// 'kg_per_bulan' 			=> str_replace(',', '', $kg_per_bulan),
				'cost_satuan' 			=> strtoupper($dataNmSat[0]['kode_satuan']),
				'nilai_konversi' 		=> str_replace(',', '', $nilai_konversi),
				// 'safety_stock' 		=> str_replace(',', '', $safety_stock),				
				// 'max_stock' 		=> str_replace(',', '', $max_stock),
				// 'price_ref_estimation' 	=> str_replace(',', '', $price_ref_estimation),
				// 'price_ref_purchase' 	=> str_replace(',', '', $price_ref_purchase),
				// 'exp_price_ref_est' 	=> $exp_price_ref_est,
				// 'exp_price_ref_pur' 	=> $exp_price_ref_pur,
				'descr' 				=> $descr,
				'flag_active' 			=> 'Y',
				'modified_by' 			=> $data_session['ORI_User']['username'],
				'modified_date' 		=> date('Y-m-d H:i:s')
			);

			// echo "<pre>"; print_r($data);
			if($numberMax_en > 0){
				$ArrdetListDetail_en	= array();
				foreach($detListDetail_en AS $val => $valx){
					$flagEn = 'N';
						if(!empty($valx['flag_active_en'])){
							$flagEn = 'Y';
						}
					$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_en']."' AND type='ENG' LIMIT 1")->result_array();

					$ArrdetListDetail_en[$val]['id_material'] 			= $id_material;
					$ArrdetListDetail_en[$val]['id_category_standard'] 	= $valx['id_category_standard_en'];
					$ArrdetListDetail_en[$val]['id_category'] 			= $id_category;
					$ArrdetListDetail_en[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
					$ArrdetListDetail_en[$val]['nilai_standard'] 		= str_replace(',', '', $valx['nilai_standard_en']);
					$ArrdetListDetail_en[$val]['descr'] 				= $valx['descr_en'];
					$ArrdetListDetail_en[$val]['flag_active'] 			= $flagEn;
					$ArrdetListDetail_en[$val]['created_by'] 			= $data_session['ORI_User']['username'];
					$ArrdetListDetail_en[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}
				// echo "<pre>";
				// print_r($ArrdetListDetail_en);
			}

			if($numberMax_bq > 0){
				$ArrdetListDetail_bq	= array();
				foreach($detListDetail_bq AS $val => $valx){
					$flagBq = 'N';
						if(!empty($valx['flag_active_bq'])){
							$flagBq = 'Y';
						}
					$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_bq']."' AND type='BQ' LIMIT 1")->result_array();

					$ArrdetListDetail_bq[$val]['id_material'] 			= $id_material;
					$ArrdetListDetail_bq[$val]['id_category_standard'] 	= $valx['id_category_standard_bq'];
					$ArrdetListDetail_bq[$val]['id_category'] 			= $id_category;
					$ArrdetListDetail_bq[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
					$ArrdetListDetail_bq[$val]['nilai_standard'] 		= str_replace(',','',$valx['nilai_standard_bq']);
					$ArrdetListDetail_bq[$val]['descr'] 				= $valx['descr_bq'];
					$ArrdetListDetail_bq[$val]['flag_active'] 			= $flagBq;
					$ArrdetListDetail_bq[$val]['created_by'] 			= $data_session['ORI_User']['username'];
					$ArrdetListDetail_bq[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}

				// echo "<pre>";
				// print_r($ArrdetListDetail_bq);
			}
			// exit;

			$getWarehouse		= $this->db->query("SELECT*FROM warehouse WHERE category='pusat' AND id='".$id_warehouse."' ORDER BY id DESC")->result_array();

			$ArrStockAwal	= array(
				'id_material' 	=> $id_material,
				'idmaterial' 	=> $idmaterial,
				'nm_material' 	=> $nm_material,
				'nm_category' 	=> $dataNmCty[0]['category'],
				'id_gudang' 	=> $id_warehouse,
				'kd_gudang' 	=> $getWarehouse[0]['kd_gudang'],
				'qty_stock' 	=> 0,
				'qty_booking' 	=> 0,
				'qty_rusak' 	=> 0,
				'update_by' 	=> $data_session['ORI_User']['username'],
				'update_date' 	=> date('Y-m-d H:i:s')
			);

			if($numType > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Material ID already exists. Please check back ...'
				);
			}
			else{
				$this->db->trans_start();
				$this->db->insert('raw_materials', $data);
				$this->db->insert('hist_raw_materials', $data2);
				$this->db->insert('warehouse_stock', $ArrStockAwal);
				if($numberMax_en > 0){
					$this->db->insert_batch('raw_material_engineer_standard', $ArrdetListDetail_en);
				}
				if($numberMax_bq > 0){
					$this->db->insert_batch('raw_material_bq_standard', $ArrdetListDetail_bq);
				}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Insert material data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Insert material data success. Thanks ...',
						'status'	=> 1
					);
					history('Input Material '.$id_material.' with username : '.$data_session['ORI_User']['username']);
				}
			}
			echo json_encode($Arr_Kembali);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$arr_Where			= array('flag_active'=>'1');
			$get_Data			= $this->master_model->getMenu($arr_Where);
			// $getType			= $this->master_model->getArray('raw_categories',array(),'id_category','category');
			$getType			= $this->db->query("SELECT*FROM raw_categories ORDER BY category ASC")->result_array();
			// $getPiece			= $this->master_model->getArray('raw_pieces',array(),'id_satuan','nama_satuan');
			$getPiece			= $this->db->query("SELECT*FROM raw_pieces WHERE delete_date IS NULL and tipe='unit' ORDER BY nama_satuan ASC")->result_array();
			$getPiecePacking	= $this->db->query("SELECT*FROM raw_pieces WHERE delete_date IS NULL and tipe='packing' ORDER BY nama_satuan ASC")->result_array();
			$getWarehouse		= $this->db->query("SELECT*FROM warehouse WHERE category='pusat' ORDER BY id ASC")->result_array();
			$data = array(
				'title'			=> 'Add Material',
				'action'		=> 'add',
				'getWarehouse'	=> $getWarehouse,
				'data_type'		=> $getType,
				'data_pieces'	=> $getPiece,
				'data_pieces_pack'	=> $getPiecePacking,
			);
			$this->load->view('Material/add2',$data);
		}
	}

	public function edit2(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;

			$id_material			= $data['id_material'];
			$nm_material			= $data['nm_material'];
			$nm_dagang				= $data['nm_dagang'];
			$nm_international		= $data['nm_international'];
			$id_category			= $data['id_category'];
			$dataNmCty				= $this->db->query("SELECT*FROM raw_categories WHERE id_category='".$id_category."'")->result_array();
			$id_accurate			= $data['id_accurate'];
			$id_satuan				= $data['id_satuan'];
			$id_packing				= $data['id_packing'];
			$dataNmSat				= $this->db->query("SELECT*FROM raw_pieces WHERE id_satuan='".$id_satuan."'")->result_array();
			$nilai_konversi			= $data['nilai_konversi'];
			$safety_stock			= $data['safety_stock'];
			$max_stock			    = $data['max_stock'];
			// $price_ref_estimation	= $data['price_ref_estimation'];
			// $price_ref_purchase		= $data['price_ref_purchase'];
			// $exp_price_ref_est		= $data['exp_price_ref_est'];
			// $exp_price_ref_pur		= $data['exp_price_ref_pur'];
			$descr					= $data['descr'];

			$satuan_kg				= $data['satuan_kg'];
			$kg_per_bulan				= $data['kg_per_bulan'];

			$numberMax_en			= $data['numberMax_en'];
			$numberMax_bq			= $data['numberMax_bq'];
			$numberMax_sub			= $data['numberMax_sub'];

			$Ym						= date('ym');

			//pengurutan kode subtitusi
			$srcMtr			= "SELECT MAX(id_subtitusi) as maxP FROM raw_material_subtitutions WHERE id_subtitusi LIKE 'SUB-".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$id_subtitusi	= "SUB-".$Ym.$urut2;


			if($numberMax_bq != 0){
				$detListDetail_bq		= $data['ListDetail_bq'];
			}

			if($numberMax_en != 0){
				$detListDetail_en		= $data['ListDetail_en'];
			}

			if($numberMax_sub != 0){
				$detListDetail_subx		= $data['ListDetail_sub'];
				//filter data array yang sama
				$detListDetail_sub 		= array_intersect_key(
												$detListDetail_subx,
												array_unique(array_map(function($item) {
													return $item['id_material'];
												}, $detListDetail_subx))
											);
			}

			$flaG = 'N';
				if(!empty($data['flag_active'])){
					$flaG = 'Y';
				}

			$Arr_Update	= array(
				'id_material' 			=> $id_material,
				'id_accurate' 			=> $id_accurate, 
				'nm_material' 			=> $nm_material,
				'nm_dagang' 				=> $nm_dagang,
				'nm_international' 	=> $nm_international,
				'id_satuan' 				=> $id_satuan,
				'id_packing' 				=> $id_packing,
				'id_category' 			=> $id_category,
				'nm_category' 			=> $dataNmCty[0]['category'],
				'satuan_kg' 				=> str_replace(',', '', $satuan_kg),
				'kg_per_bulan' 					=> str_replace(',', '', $kg_per_bulan),
				'cost_satuan' 			=> strtolower($dataNmSat[0]['kode_satuan']),
				'nilai_konversi' 		=> str_replace(',', '', $nilai_konversi),
				'safety_stock' 			=> str_replace(',', '', $safety_stock),
				'max_stock' 			=> str_replace(',', '', $max_stock),
				// 'price_ref_estimation' 	=> str_replace(',', '', $price_ref_estimation),
				// 'price_ref_purchase' 	=> str_replace(',', '', $price_ref_purchase),
				// 'exp_price_ref_est' 	=> $exp_price_ref_est,
				// 'exp_price_ref_pur' 	=> $exp_price_ref_pur,
				'descr' 						=> $descr,
				'flag_active' 			=> $flaG,
				'modified_by' 			=> $data_session['ORI_User']['username'],
				'modified_date' 		=> date('Y-m-d H:i:s')
			);
			// print_r($Arr_Update);

			if($numberMax_en != 0){
				$ArrdetListDetail_en	= array();
				foreach($detListDetail_en AS $val => $valx){
					$flagEn = 'N';
						if(!empty($valx['flag_active_en'])){
							$flagEn = 'Y';
						}
					$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_en']."' AND type='ENG' LIMIT 1")->result_array();

					$ArrdetListDetail_en[$val]['id_material'] 			= $id_material;
					$ArrdetListDetail_en[$val]['id_category_standard'] 	= $valx['id_category_standard_en'];
					$ArrdetListDetail_en[$val]['id_category'] 			= $id_category;
					$ArrdetListDetail_en[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
					$ArrdetListDetail_en[$val]['nilai_standard'] 		= str_replace(',', '', $valx['nilai_standard_en']);
					$ArrdetListDetail_en[$val]['descr'] 				= $valx['descr_en'];
					$ArrdetListDetail_en[$val]['flag_active'] 			= $flagEn;
					$ArrdetListDetail_en[$val]['created_by'] 			= $data_session['ORI_User']['username'];
					$ArrdetListDetail_en[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}

				// print_r($ArrdetListDetail_en);
			}

			if($numberMax_bq != 0){
				$ArrdetListDetail_bq	= array();
				foreach($detListDetail_bq AS $val => $valx){
					$flagBq = 'N';
						if(!empty($valx['flag_active_bq'])){
							$flagBq = 'Y';
						}
					$ChNmStandard	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category_standard='".$valx['id_category_standard_bq']."' AND type='BQ' LIMIT 1")->result_array();

					$ArrdetListDetail_bq[$val]['id_material'] 			= $id_material;
					$ArrdetListDetail_bq[$val]['id_category_standard'] 	= $valx['id_category_standard_bq'];
					$ArrdetListDetail_bq[$val]['id_category'] 			= $id_category;
					$ArrdetListDetail_bq[$val]['nm_standard'] 			= $ChNmStandard[0]['nm_category_standard'];
					$ArrdetListDetail_bq[$val]['nilai_standard'] 		= str_replace(',','',$valx['nilai_standard_bq']);
					$ArrdetListDetail_bq[$val]['descr'] 				= $valx['descr_bq'];
					$ArrdetListDetail_bq[$val]['flag_active'] 			= $flagBq;
					$ArrdetListDetail_bq[$val]['created_by'] 			= $data_session['ORI_User']['username'];
					$ArrdetListDetail_bq[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}

				// print_r($ArrdetListDetail_bq);
			}

			if($numberMax_sub != 0){
				$ArrdetListDetail_sub	= array();
				foreach($detListDetail_sub AS $val => $valx){
					$flagSub = 'N';
						if(!empty($valx['flag_active'])){
							$flagSub = 'Y';
						}
					$sqlMtr			= "SELECT * FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1";
					// echo $sqlMtr;
					$ChNmMaterials	= $this->db->query($sqlMtr)->result_array();

					// $ArrdetListDetail_sub[$val]['id_subtitusi'] 			= $id_material."/".$id_subtitusi;
					$ArrdetListDetail_sub[$val]['id_material'] 				= $id_material;
					$ArrdetListDetail_sub[$val]['id_material_subtitusi'] 	= $valx['id_material']."/".$id_subtitusi;
					$ArrdetListDetail_sub[$val]['nm_subtitusi'] 			= trim($ChNmMaterials[0]['nm_material']);
					$ArrdetListDetail_sub[$val]['descr'] 					= $valx['descr'];
					$ArrdetListDetail_sub[$val]['flag_active'] 				= $flagSub;
					$ArrdetListDetail_sub[$val]['created_by'] 				= $data_session['ORI_User']['username'];
					$ArrdetListDetail_sub[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}
				// echo "<pre>";
				// print_r($ArrdetListDetail_sub);
			}

			// exit;

			$this->db->trans_start();
				$this->db->query("INSERT hist_raw_materials (
										id_material,idmaterial,nm_material,nm_dagang,nm_international,id_category,
										nm_category,id_satuan,cost_satuan,satuan_kg,saldo_kg,nilai_konversi,price_ref_estimation,
										price_ref_purchase,exp_price_ref_est,exp_price_ref_pur,flag_active,descr,modified_by,modified_date
									)
									SELECT
										id_material,idmaterial,nm_material,nm_dagang,nm_international,id_category,
										nm_category,id_satuan,cost_satuan,satuan_kg,saldo_kg,nilai_konversi,price_ref_estimation,
										price_ref_purchase,exp_price_ref_est,exp_price_ref_pur,flag_active,descr,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										raw_materials
									WHERE
										id_material = '".$id_material."'");

				$this->db->where('id_material', $id_material);
				$this->db->update('raw_materials', $Arr_Update);

				if($numberMax_en != 0){
					$this->db->insert_batch('raw_material_engineer_standard', $ArrdetListDetail_en);
				}
				if($numberMax_bq != 0){
					$this->db->insert_batch('raw_material_bq_standard', $ArrdetListDetail_bq);
				}
				if($numberMax_sub != 0){
					$this->db->insert_batch('raw_material_subtitutions', $ArrdetListDetail_sub);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update material data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update material data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Material '.$id_material.' with username : '.$data_session['ORI_User']['username']);
			}
			// print_r($Arr_Data); exit;
			echo json_encode($Arr_Data);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}

			$id = $this->uri->segment(3);

			$detail			= $this->db->query("SELECT * FROM raw_materials WHERE id_material = '".$id."' ")->result_array();
			$getType		= $this->db->query("SELECT * FROM raw_categories ORDER BY category ASC")->result_array();
			$getPiece			= $this->db->query("SELECT*FROM raw_pieces WHERE delete_date IS NULL and tipe='unit' ORDER BY nama_satuan ASC")->result_array();
			$getPiecePacking	= $this->db->query("SELECT*FROM raw_pieces WHERE delete_date IS NULL and tipe='packing' ORDER BY nama_satuan ASC")->result_array();
			$detailBQ		= $this->db->query("SELECT * FROM raw_material_bq_standard WHERE id_material = '".$id."' ")->result_array();
			$detailEn		= $this->db->query("SELECT * FROM raw_material_engineer_standard WHERE id_material = '".$id."' ")->result_array();
			$Supply			= $this->db->query("SELECT * FROM raw_material_supplier WHERE id_material = '".$id."' ")->result_array();
			$SubMat			= $this->db->query("SELECT * FROM raw_material_subtitutions WHERE id_material = '".$id."' ")->result_array();
			$ListSup		= $this->db->query("SELECT * FROM supplier")->result_array();

			$NmdetailBQ		= $this->db->query("SELECT * FROM raw_material_bq_standard WHERE id_material = '".$id."' ")->num_rows();
			$NmdetailEn		= $this->db->query("SELECT * FROM raw_material_engineer_standard WHERE id_material = '".$id."' ")->num_rows();
			$NmSupply		= $this->db->query("SELECT * FROM raw_material_supplier WHERE id_material = '".$id."' ")->num_rows();
			$NmSubMat		= $this->db->query("SELECT * FROM raw_material_subtitutions WHERE id_material = '".$id."' ")->num_rows();

			$data = array(
				'title'			=> 'Edit Material',
				'action'		=> 'edit',
				'row'			=> $detail,
				'data_type'		=> $getType,
				'data_pieces'	=> $getPiece,
				'detailEn'		=> $detailEn,
				'detailBQ'		=> $detailBQ,
				'Supply'		=> $Supply,
				'ListSup'		=> $ListSup,
				'detSubMat'		=> $SubMat,
				'NmdetailBQ'	=> $NmdetailBQ,
				'NmdetailEn'	=> $NmdetailEn,
				'NmSupply'		=> $NmSupply,
				'NmSubMat'		=> $NmSubMat,
				'data_pieces_pack'	=> $getPiecePacking,
			);

			$this->load->view('Material/edit2',$data);
		}
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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
			$nestedData[]	= "<div align='left'>".strtoupper($row['idmaterial'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_category'])."</div>";

				$class	= 'bg-green';
				$status	= 'Active';
				if($row['flag_active'] == 'N'){
					$class	= 'bg-red';
					$status	= 'Not Active';
				}

				$date_now 	= date('Y-m-d');
				$date_exp 	= $row['exp_price_ref_est'];

				$tgl1x = new DateTime($date_now);
				$tgl2x = new DateTime($date_exp);
				$selisihx = $tgl2x->diff($tgl1x)->days + 1;

				$date_expv 	= date('d M Y', strtotime($date_exp));
				$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));

				$tambahan = "No Set";
				if($tgl2x < $tgl1x){
					$status2="Expired price";
					$tambahan = "<span class='badge bg-red'>$status2</span>";
				}
				if($tgl2x >= $tgl1x AND $selisihx <= 7){
					$status2="Less one week expired price";
					$tambahan = "<span class='badge bg-blue'>$status2</span>";
				}
				if($tgl2x >= $tgl1x AND $selisihx > 7){
					$tambahan = "";
				}

			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='center'><span class='badge $class'>$status</span>&nbsp;".$tambahan."</div>";
					$view	= "<button type='button' data-id_material='".$row['id_material']."' data-nm_material='".$row['nm_material']."' class='btn btn-sm btn-warning MatDetail' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>";
					$update	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/edit2/'.$row['id_material'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
					$delete	= "&nbsp;<button data-id_material='".$row['id_material']."' class='btn btn-sm btn-danger del_type' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></button>";

			$nestedData[]	= "<div align='center'>
									".$view."
									".$update."
									".$delete."
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_wait_est = "";

		$sql = "
			SELECT
				a.*
			FROM
				raw_materials a
		    WHERE
				1=1
				".$where_wait_est."
				AND `delete` = 'N' AND (
				a.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'idmaterial',
			2 => 'nm_material',
			3 => 'nm_category',
			4 => 'exp_price_ref_est'
		);

		// $sql .= " GROUP BY x.id_bq ORDER BY x.".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function download_excel(){
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $this->load->library("PHPExcel");

        $objPHPExcel    = new PHPExcel();
		
		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

        $Arr_Bulan  = array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $sheet      = $objPHPExcel->getActiveSheet();

        $dateX	= date('Y-m-d H:i:s');
        $Row        = 1;
        $NewRow     = $Row+1;
        $Col_Akhir  = $Cols = getColsChar(13);
        $sheet->setCellValue('A'.$Row, "MASTER MATERIAL");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'ID PROGRAM');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'ID MATERIAL');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'ID ACCURATE');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'NM MATERIAL');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'NM CATEGORY');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'STATUS');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Unit Packing');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'Konversi');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

		$sheet ->getColumnDimension("J")->setAutoSize(true);
		$sheet->setCellValue('J'.$NewRow, 'Unit');
        $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);

		$sheet ->getColumnDimension("K")->setAutoSize(true);
		$sheet->setCellValue('K'.$NewRow, 'Min Stock');
        $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);

		$sheet ->getColumnDimension("L")->setAutoSize(true);
		$sheet->setCellValue('L'.$NewRow, 'Max Stock');
        $sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('L'.$NewRow.':L'.$NextRow);

		$sheet ->getColumnDimension("M")->setAutoSize(true);
		$sheet->setCellValue('M'.$NewRow, 'Kebutuhan Per Bulan');
        $sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('M'.$NewRow.':M'.$NextRow);

		$SQL = "SELECT a.* FROM raw_materials a WHERE a.delete_date IS NULL";
		$dataResult   = $this->db->query($SQL)->result_array();

		if($dataResult){
			$awal_row   = $NextRow;
			 $no = 0;
			foreach($dataResult as $key=>$vals){
				$no++;
				$awal_row++;
				$awal_col   = 0;

				$awal_col++;
				$no   = $no;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_material   = $vals['id_material'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$idmaterial   = $vals['idmaterial'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_accurate   = $vals['id_accurate'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_accurate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material   = $vals['nm_material'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_category   = $vals['nm_category'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$flag_active   = $vals['flag_active'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $flag_active);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_packing   = get_name('raw_pieces','kode_satuan','id_satuan',$vals['id_packing']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_packing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nilai_konversi   = $vals['nilai_konversi'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nilai_konversi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$id_satuan   = get_name('raw_pieces','kode_satuan','id_satuan',$vals['id_satuan']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_satuan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nilai_konversi   = $vals['nilai_konversi'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nilai_konversi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$max_stock   = $vals['max_stock'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $max_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$kg_per_bulan   = $vals['kg_per_bulan'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kg_per_bulan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}

        $sheet->setTitle('Material');
        //mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
        $objWriter      = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        //sesuaikan headernya
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //ubah nama file saat diunduh
        header('Content-Disposition: attachment;filename="master-material.xls"');
        //unduh file
        $objWriter->save("php://output");
    }
}
