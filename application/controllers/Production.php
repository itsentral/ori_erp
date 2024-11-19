<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('update_produksi_model');
		$this->load->model('produksi_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}
	
	//========================================================================================================================
	//==============================================UPDATE PRODUKSI===========================================================
	//========================================================================================================================
	public function updateRealNew2(){
		$this->update_produksi_model->update_produksi_1();
	}
	
	public function updateRealNew3(){
		$this->update_produksi_model->update_produksi_2();
	}

	public function server_side_update_produksi_1(){
		$this->update_produksi_model->get_data_json_update_produksi_1();	
	}
	
	public function modalReal1New(){
		$this->update_produksi_model->modal_update_produksi_1();
	}
	
	public function modalReal3New(){
		$this->update_produksi_model->modal_update_produksi_2();
	}
	
	public function save_update_produksi_1(){
		$this->update_produksi_model->save_update_produksi_1();
	}
	
	public function save_update_produksi_2(){
		$this->update_produksi_model->save_update_produksi_2();
	}
	
	public function modalPerbandingan(){
		$this->update_produksi_model->modal_actual_vs_real();
	}


	//========================================================================================================================
	//=====================================================PRODUKSI===========================================================
	//========================================================================================================================
	public function index(){
		$this->produksi_model->index();
	}
	
	public function server_side_spk_produksi(){
		$this->produksi_model->get_data_json_spk_produksi();
	}
	
	public function modalDetail(){
		$this->produksi_model->modal_detail_spk();
	}

	public function backToFinalDrawing(){
		$id_milik 		= $this->uri->segment(3);
		$menu_baru 		= $this->uri->segment(4);
		$no_ipp			= $this->input->post('no_ipp');
		$id_produksi 	= "PRO-".$no_ipp;
		$data_session	= $this->session->userdata;

		$get_id_detail = $this->db->get_where('so_detail_header', array('id'=>$id_milik))->result();
		$id_bq_header = $get_id_detail[0]->id_bq_header;

		$ArrUpdate = [
			'approve' => 'N',
			'approve_by' => $data_session['ORI_User']['username'],
			'approve_date' => date('Y-m-d H:i:s')
		];

		$ArrUpdateSOHeader = [
			'approved_est' => 'N',
			'approved_est_by' => $data_session['ORI_User']['username'],
			'approved_est_date' => date('Y-m-d H:i:s')
		];

		// exit;
		$this->db->trans_start();
			//hapus production detail
			$this->db->where('id_milik', $id_milik);
			$this->db->where('id_produksi', $id_produksi);
			$this->db->delete('production_detail');
			//update sales order detail
			$this->db->where('id', $id_milik);
			$this->db->update('so_detail_header', $ArrUpdate);
			//update sales order detail
			$this->db->where('id_bq_header', $id_bq_header);
			$this->db->update('so_detail_detail', $ArrUpdate);
			//update sales order detail
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', array('status'=>'PARTIAL PROCESS'));
			//update sales order header
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('so_header', $ArrUpdateSOHeader);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'			=>'Process data failed. Please try again later ...',
				'status'		=> 0,
				'menu_baru' => $menu_baru,
				'id_produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'			=>'Process data success. Thanks ...',
				'status'		=> 1,
				'menu_baru' => $menu_baru,
				'id_produksi'	=> $id_produksi
			);
			history('Back to final drawing dari ppic : '.$id_milik.'/'.$no_ipp);
		}
		echo json_encode($Arr_Data);
	}
	
	//========================================================================================================================
	//=================================================PROGRESS PRODUKSI======================================================
	//========================================================================================================================
	public function progress_produksi(){
		$this->produksi_model->progress_produksi();
	}
	
	public function server_side_spk_produksi_progress(){
		$this->produksi_model->get_data_json_spk_produksi_progress();
	}
	
	public function modal_detail_progress(){
		$this->produksi_model->modal_detail_progress();
	}
	
	public function print_progress_produksi(){
		$id_produksi	= $this->uri->segment(3);
		$id_bq 			= "BQ-".str_replace('PRO-','',$id_produksi);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		$help2 = "";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
			$help2 = " b.id_milik AS id_milik2,";
		}
		
		$qDetail	= "	SELECT
							a.*,
							b.no_komponen,
							b.id_category AS comp,
							c.type AS typeProduct,
							b.id AS id_uniq
						FROM
							production_detail a
							LEFT JOIN product_parent c ON a.id_category = c.product_parent
							LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
						WHERE
							a.id_produksi = '".$id_produksi."'
						GROUP BY
							b.no_komponen,
							a.sts_delivery,
							a.id_product
						ORDER BY
							b.id_bq_header ASC";
		$rowD		= $this->db->query($qDetail)->result_array();
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id_bq' => $id_bq,
			'rowD' => $rowD,
			'HelpDet' => $HelpDet,
		);
		history('Print progress produksi '.$id_bq); 
		$this->load->view('Print/print_progress_produksi', $data);
	}
	
	
	public function spk_mat_acc(){
		$id_bq			= $this->uri->segment(3);
		$tanda			= $this->uri->segment(4);
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
			'tanda' => $tanda
		);
		history('Print SPK '.$id_bq); 
		$this->load->view('Print/print_spk_mat_acc', $data);
	}





	

	public function history(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT create_by, create_date FROM table_history_pro_header ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();

		$data = array(
			'title'			=> 'Indeks Of History Production',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View Data History Production');
		$this->load->view('Production/history',$data);
	}

	public function check_real(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT create_by, create_date FROM table_history_pro_header_tmp ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();

		$data = array(
			'title'			=> 'Indeks Of Check Production Input',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View Data Check Production Input');
		$this->load->view('Production/check_real',$data);
	}

	

	public function add(){
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');

			$productDetail = $data['ListDetail'];

			// print_r($productDetail);

			//pengurutan kode
			$srcPlant			= "SELECT MAX(id_produksi) as maxP FROM production_header WHERE id_produksi LIKE 'PRO-".$YM."-%' ";
			$numrowPlant		= $this->db->query($srcPlant)->num_rows();
			$resultPlant		= $this->db->query($srcPlant)->result_array();
			$angkaUrut2			= $resultPlant[0]['maxP'];
			$urutan2			= (int)substr($angkaUrut2, 9, 4);
			$urutan2++;
			$urut2				= sprintf('%04s',$urutan2);
			$id_produksi		= "PRO-".$YM."-".$urut2;

			// $qCust	= "SELECT nm_customer FROM customer WHERE id_customer = '".$data['id_customer']."' LIMIT 1";
			// $dCust	= $this->db->query($qCust)->result_array();
			$qMch	= "SELECT nm_mesin FROM machine WHERE id_mesin = '".$data['id_mesin']."' LIMIT 1";
			$dMch	= $this->db->query($qMch)->result();
			// echo $id_produksi;
			// exit;

			$Data_Insert			= array(
				'id_produksi'		=> $id_produksi,
				// 'no_ipp'			=> $data['no_ipp'],
				'so_number'			=> strtoupper($data['so_number']),
				// 'nm_customer'	=> $dCust[0]['nm_customer'],
				'id_mesin'			=>  $data['id_mesin'],
				'nm_mesin'			=> $dMch[0]->nm_mesin,
				'no_dokumen'		=> "",
				'rev'				=> "",
				'plan_start_produksi'	=> $data['plan_start_produksi'],
				'plan_end_produksi'		=> $data['plan_end_produksi'],
				'ket'				=> $data['ket'],
				'nm_project'		=> strtoupper($data['nm_project']),
				'created_date'		=> date('Y-m-d H:i:s'),
				'created_by'		=> $this->session->userdata['ORI_User']['username']
			);
			// echo "<pre>";
			// print_r($Data_Insert);
			// exit;

			$detailData	= array();
			$lopp = 0;
			foreach($productDetail AS $val => $valx){
				for($no=1; $no <= $valx['qty']; $no++){
					$lopp++;
					$detailData[$lopp]['id_produksi'] 	= $id_produksi;
					$detailData[$lopp]['id_delivery'] 	= $valx['id_delivery'];
					$detailData[$lopp]['id_category'] 	= $valx['id_category'];
					$detailData[$lopp]['id_product'] 	= $valx['id_product'];
					$detailData[$lopp]['qty'] 			= $valx['qty'];
					$detailData[$lopp]['product_ke'] 	= $no;
				}
			}

			// print_r($Data_Insert); print_r($detailData);
			// exit;

			$this->db->trans_start();
			$this->db->insert('production_header', $Data_Insert);
			$this->db->insert_batch('production_detail', $detailData);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add production data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Addproduction data success. Thanks ...',
					'status'	=> 1
				);
				history('Add Production with code : '.$id_produksi);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)."/".$this->uri->segment(2)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$det_Province		= $this->master_model->getArray('provinsi',array(),'nama','nama');
			$det_branch			= $this->master_model->getArray('branch',array(),'nocab','cabang');
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
			$ListMachine		= $this->db->query("SELECT id_mesin, nm_mesin FROM machine WHERE sts_mesin='Y' ORDER BY nm_mesin ASC")->result_array();
			$ListNoIPP			= $this->db->query("SELECT no_ipp FROM production WHERE deleted='N' AND status = 'WAITING STRUCTURE BQ' ORDER BY no_ipp ASC")->result_array();
			// print_r($ListNoIPP);
			$data = array(
				'title'			=> 'New Production',
				'action'		=> 'add',
				'customer'		=> $ListCustomer,
				'machine'		=> $ListMachine,
				'noIPP'			=> $ListNoIPP
			);
			$this->load->view('Production/add',$data);
		}
	}

	public function add2(){
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');

			if(!empty($data['ListDetailKomp'])){
				$productDetKomp = $data['ListDetailKomp'];
			}
			if(!empty($data['ListDetailKompSub'])){
				$productDetKomSub = $data['ListDetailKompSub'];
			}
			if(!empty($data['ListDetailKompSingle'])){
				$productDetKomSigle = $data['ListDetailKompSingle'];
			}

			// print_r($productDetKomp);
			// print_r($productDetKomSub);
			// print_r($productDetKomSigle);


			// exit;
			//pengurutan kode
			$srcPlant			= "SELECT MAX(id_produksi) as maxP FROM production_header WHERE id_produksi LIKE 'PRO-".$YM."-%' ";
			$numrowPlant		= $this->db->query($srcPlant)->num_rows();
			$resultPlant		= $this->db->query($srcPlant)->result_array();
			$angkaUrut2			= $resultPlant[0]['maxP'];
			$urutan2			= (int)substr($angkaUrut2, 9, 4);
			$urutan2++;
			$urut2				= sprintf('%04s',$urutan2);
			$id_produksi		= "PRO-".$YM."-".$urut2;

			$qCust	= "SELECT nm_customer FROM customer WHERE id_customer = '".$data['id_customer']."' LIMIT 1";
			$dCust	= $this->db->query($qCust)->result_array();
			// echo $id_produksi;
			// exit;

			$Data_Insert			= array(
				'id_produksi'			=> $id_produksi,
				'id_customer'			=> $data['id_customer'],
				'nm_customer'			=> $dCust[0]['nm_customer'],
				'id_mesin'				=> "",
				'nm_mesin'				=> "",
				'no_dokumen'			=> "",
				'rev'					=> "",
				'plan_start_produksi'	=> $data['plan_start_produksi'],
				'plan_end_produksi'		=> $data['plan_end_produksi'],
				'ket'					=> $data['ket'],
				'created_date'			=> date('Y-m-d H:i:s'),
				'created_by'			=> $this->session->userdata['ORI_User']['username']
			);
			// echo "<pre>";
			// print_r($Data_Insert);
			// exit;

			if(!empty($data['ListDetailKomp'])){
				$detailData	= array();
				$lopp = 0;
				foreach($productDetKomp AS $val => $valx){
					for($no=1; $no <= $valx['qty']; $no++){
						$lopp++;
						$detailData[$lopp]['id_produksi'] 	= $id_produksi;
						$detailData[$lopp]['id_delivery'] 	= $valx['id_delivery'];
						$detailData[$lopp]['sts_delivery'] 	= "parent";
						$detailData[$lopp]['sub_delivery'] 	= $valx['sub_delivery'];
						$detailData[$lopp]['id_category'] 	= $valx['id_category'];
						$detailData[$lopp]['id_product'] 	= $valx['id_product'];
						$detailData[$lopp]['qty'] 			= $valx['qty'];
						$detailData[$lopp]['product_ke'] 	= $no;
					}
				}
				// print_r($detailData);
			}

			if(!empty($data['ListDetailKompSub'])){
				$detailData2	= array();
				$lopp2 = 0;
				foreach($productDetKomSub AS $val => $valx){
					for($no=1; $no <= $valx['qty']; $no++){
						$lopp2++;
						$detailData2[$lopp2]['id_produksi'] 	= $id_produksi;
						$detailData2[$lopp2]['id_delivery'] 	= $valx['id_delivery'];
						$detailData2[$lopp2]['sts_delivery'] 	= "child";
						$detailData2[$lopp2]['sub_delivery'] 	= $valx['sub_delivery'];
						$detailData2[$lopp2]['id_category'] 	= $valx['id_category'];
						$detailData2[$lopp2]['id_product'] 		= $valx['id_product'];
						$detailData2[$lopp2]['qty'] 			= $valx['qty'];
						$detailData2[$lopp2]['product_ke'] 		= $no;
					}
				}
				// print_r($detailData2);
			}

			if(!empty($data['ListDetailKompSingle'])){
				$detailData3	= array();
				$lopp3 = 0;
				foreach($productDetKomSigle AS $val => $valx){
					for($no=1; $no <= $valx['qty']; $no++){
						$lopp3++;
						$detailData3[$lopp3]['id_produksi'] 	= $id_produksi;
						$detailData3[$lopp3]['id_delivery'] 	= $valx['id_delivery'];
						$detailData3[$lopp3]['sts_delivery'] 	= "general";
						$detailData3[$lopp3]['sub_delivery'] 	= "-";
						$detailData3[$lopp3]['id_category'] 	= $valx['id_category'];
						$detailData3[$lopp3]['id_product'] 		= $valx['id_product'];
						$detailData3[$lopp3]['qty'] 			= $valx['qty'];
						$detailData3[$lopp3]['product_ke'] 		= $no;
					}
				}
				// print_r($detailData3);
			}

			// exit;

			$this->db->trans_start();
				$this->db->insert('production_header', $Data_Insert);
				if(!empty($data['ListDetailKomp'])){
					$this->db->insert_batch('production_detail', $detailData);
				}
				if(!empty($data['ListDetailKompSub'])){
					$this->db->insert_batch('production_detail', $detailData2);
				}
				if(!empty($data['ListDetailKompSingle'])){
					$this->db->insert_batch('production_detail', $detailData3);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add production data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Addproduction data success. Thanks ...',
					'status'	=> 1
				);
				history('Add Production with code : '.$id_produksi);
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

			$det_Province		= $this->master_model->getArray('provinsi',array(),'nama','nama');
			$det_branch			= $this->master_model->getArray('branch',array(),'nocab','cabang');
			$ListCustomer		= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
			$ListMachine		= $this->db->query("SELECT id_mesin, nm_mesin FROM machine WHERE sts_mesin='Y' ORDER BY nm_mesin ASC")->result_array();

			$data = array(
				'title'			=> 'New Production TESTING',
				'action'		=> 'add2',
				'customer'		=> $ListCustomer,
				'machine'		=> $ListMachine
			);
			$this->load->view('Production/add2',$data);
		}
	}



	

	public function modalstartPro(){
		$this->load->view('Production/modalStartPro');
	}

	public function modalcloseProduksi($id_produksi){
		$list_machine	= $this->db->order_by('nm_mesin','asc')->get_where('machine',array('sts_mesin'=>'Y'))->result_array();
		$get_header		= $this->db->get_where('production_header',array('id_produksi'=>$id_produksi))->result_array();

		$data = [
			'list_mesin' => $list_machine,
			'header' => $get_header,
		];
		$this->load->view('Production/modalcloseProduksi',$data);
	}

	

	


	public function modalReal(){
		$this->load->view('Production/modalReal');
	}

	

	public function modalReal1(){
		if (substr($this->uri->segment(3),0,2) == 'BJ' || substr($this->uri->segment(3),0,2) == 'FJ' || substr($this->uri->segment(3),0,2) == 'SJ') {
			$this->load->view('Production/modalReal_joint1');
		}else {
			$this->load->view('Production/modalReal1');
		}
	}

	public function modalReal2(){
		if (substr($this->uri->segment(3),0,2) == 'BJ' || substr($this->uri->segment(3),0,2) == 'FJ' || substr($this->uri->segment(3),0,2) == 'SJ') {
			$this->load->view('Production/modalReal_joint2');
		}else {
			$this->load->view('Production/modalReal2');
		}
	}

	public function modalReal3(){
		if (substr($this->uri->segment(3),0,2) == 'BJ' || substr($this->uri->segment(3),0,2) == 'FJ' || substr($this->uri->segment(3),0,2) == 'SJ') {
			$this->load->view('Production/modalReal_joint2');
		}else {
			$this->load->view('Production/modalReal3');
		}
	}

	

	

	public function modalPerbandingan_tmp(){
		$this->load->view('Production/modalPerbandingan_tmp');
	}

	public function modalEditReal(){
		$this->load->view('Production/modalEditReal');
	}

	public function UpdateRealMat(){

		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$id_production_detail 	= $data['id_production_detail'];

		if(!empty($data['DetailUtama'])){
			$DetailUtama	= $data['DetailUtama'];
		}

		if(!empty($data['DetailUtama2'])){
			$DetailUtama2	= $data['DetailUtama2'];
		}

		if(!empty($data['DetailUtama3'])){
			$DetailUtama3	= $data['DetailUtama3'];
		}

		if(!empty($data['DetailResin'])){
			$DetailResin	= $data['DetailResin'];
		}

		if(!empty($data['DetailResin2'])){
			$DetailResin2	= $data['DetailResin2'];
		}

		if(!empty($data['DetailResin3'])){
			$DetailResin3	= $data['DetailResin3'];
		}

		if(!empty($data['DetailPlus'])){
			$DetailPlus		= $data['DetailPlus'];
		}

		if(!empty($data['DetailPlus2'])){
			$DetailPlus2	= $data['DetailPlus2'];
		}

		if(!empty($data['DetailPlus3'])){
			$DetailPlus3	= $data['DetailPlus3'];
		}

		if(!empty($data['DetailPlus4'])){
			$DetailPlus4	= $data['DetailPlus4'];
		}

		if(!empty($data['DetailAdd'])){
			$DetailAdd		= $data['DetailAdd'];
		}

		if(!empty($data['DetailAdd2'])){
			$DetailAdd2		= $data['DetailAdd2'];
		}

		if(!empty($data['DetailAdd3'])){
			$DetailAdd3		= $data['DetailAdd3'];
		}

		if(!empty($data['DetailAdd4'])){
			$DetailAdd4		= $data['DetailAdd4'];
		}

		// echo "<pre>";
		if(!empty($data['DetailUtama'])){
			$ArrDetailUtama	= array();
			foreach($DetailUtama AS $val => $valx){
				$ArrDetailUtama[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama);
		}

		if(!empty($data['DetailUtama2'])){
			$ArrDetailUtama2	= array();
			foreach($DetailUtama2 AS $val => $valx){
				$ArrDetailUtama2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama2[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama2);
		}

		if(!empty($data['DetailUtama3'])){
			$ArrDetailUtama3	= array();
			foreach($DetailUtama3 AS $val => $valx){
				$ArrDetailUtama3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama3[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama3);
		}

		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus4);
		}

		if(!empty($data['DetailAdd'])){
			$ArrDetailAdd	= array();
			foreach($DetailAdd AS $val => $valx){
				$ArrDetailAdd[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd);
		}

		if(!empty($data['DetailAdd2'])){
			$ArrDetailAdd2	= array();
			foreach($DetailAdd2 AS $val => $valx){
				$ArrDetailAdd2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd2);
		}

		if(!empty($data['DetailAdd3'])){
			$ArrDetailAdd3	= array();
			foreach($DetailAdd3 AS $val => $valx){
				$ArrDetailAdd3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd3);
		}

		if(!empty($data['DetailAdd4'])){
			$ArrDetailAdd4	= array();
			foreach($DetailAdd4 AS $val => $valx){
				$ArrDetailAdd4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd4);
		}

		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'upload_real' => "Y",
			'upload_by' => $data_session['ORI_User']['username'],
			'upload_date' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);
			//Utama
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama);
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama2);
			if(!empty($data['DetailUtama3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailUtama3);
			}
			//Resin
			$this->db->insert_batch('production_real_detail', $ArrDetailResin);
			$this->db->insert_batch('production_real_detail', $ArrDetailResin2);
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailResin3);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus);
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus4);
			}
			//Detail Add
			if(!empty($data['DetailAdd'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd);
			}
			if(!empty($data['DetailAdd2'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd2);
			}
			if(!empty($data['DetailAdd3'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd3);
			}
			if(!empty($data['DetailAdd4'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Failed. Please try again later ...',
				'status'	=> 2,
				'produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'produksi'	=> $id_produksi
			);
			history('Add Real Production '.$id_produksi.'/'.$product);
		}
		echo json_encode($Arr_Kembali);
	}
	//Modal1
	public function UpdateRealMat1(){

		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$est_real				= $data['est_real'];
		$id_production_detail 	= $data['id_production_detail'];

		if(!empty($data['DetailUtama'])){
			$DetailUtama	= $data['DetailUtama'];
		}

		if(!empty($data['DetailUtama2'])){
			$DetailUtama2	= $data['DetailUtama2'];
		}

		if(!empty($data['DetailUtama3'])){
			$DetailUtama3	= $data['DetailUtama3'];
		}

		if(!empty($data['DetailResin'])){
			$DetailResin	= $data['DetailResin'];
		}

		if(!empty($data['DetailResin2'])){
			$DetailResin2	= $data['DetailResin2'];
		}

		if(!empty($data['DetailResin3'])){
			$DetailResin3	= $data['DetailResin3'];
		}

		if(!empty($data['DetailPlus'])){
			$DetailPlus		= $data['DetailPlus'];
		}

		if(!empty($data['DetailPlus2'])){
			$DetailPlus2	= $data['DetailPlus2'];
		}

		if(!empty($data['DetailPlus3'])){
			$DetailPlus3	= $data['DetailPlus3'];
		}

		if(!empty($data['DetailPlus4'])){
			$DetailPlus4	= $data['DetailPlus4'];
		}

		// echo "<pre>";
		if(!empty($data['DetailUtama'])){
			$ArrDetailUtama	= array();
			foreach($DetailUtama AS $val => $valx){
				$ArrDetailUtama[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama);
		}

		if(!empty($data['DetailUtama2'])){
			$ArrDetailUtama2	= array();
			foreach($DetailUtama2 AS $val => $valx){
				$ArrDetailUtama2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama2[$val]['benang'] = (!empty($valx['benang']))?$valx['benang']:'';
				$ArrDetailUtama2[$val]['bw'] = (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetailUtama2[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama2);
		}

		if(!empty($data['DetailUtama3'])){
			$ArrDetailUtama3	= array();
			foreach($DetailUtama3 AS $val => $valx){
				$ArrDetailUtama3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama3[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama3);
		}

		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus4);
		}

		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'est_real' => $est_real,
			'upload_real' => "Y",
			'upload_by' => $data_session['ORI_User']['username'],
			'upload_date' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);
			//Utama
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama);
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama2);
			if(!empty($data['DetailUtama3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailUtama3);
			}
			//Resin
			$this->db->insert_batch('production_real_detail', $ArrDetailResin);
			$this->db->insert_batch('production_real_detail', $ArrDetailResin2);
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailResin3);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus);
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus4);
			}

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Failed. Please try again later ...',
				'status'	=> 2,
				'produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'produksi'	=> $id_produksi
			);
			history('Add Real Production 1 '.$id_produksi.'/'.$product);
		}
		echo json_encode($Arr_Kembali);
	}

	//Modal 2
	public function UpdateRealMat2(){

		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$id_production_detail 	= $data['id_production_detail'];

		if(!empty($data['DetailResin'])){
			$DetailResin	= $data['DetailResin'];
		}

		if(!empty($data['DetailResin2'])){
			$DetailResin2	= $data['DetailResin2'];
		}

		if(!empty($data['DetailResin3'])){
			$DetailResin3	= $data['DetailResin3'];
		}

		if(!empty($data['DetailPlus'])){
			$DetailPlus		= $data['DetailPlus'];
		}

		if(!empty($data['DetailPlus2'])){
			$DetailPlus2	= $data['DetailPlus2'];
		}

		if(!empty($data['DetailPlus3'])){
			$DetailPlus3	= $data['DetailPlus3'];
		}

		if(!empty($data['DetailPlus4'])){
			$DetailPlus4	= $data['DetailPlus4'];
		}

		if(!empty($data['DetailAdd'])){
			$DetailAdd		= $data['DetailAdd'];
		}

		if(!empty($data['DetailAdd2'])){
			$DetailAdd2		= $data['DetailAdd2'];
		}

		if(!empty($data['DetailAdd3'])){
			$DetailAdd3		= $data['DetailAdd3'];
		}

		if(!empty($data['DetailAdd4'])){
			$DetailAdd4		= $data['DetailAdd4'];
		}

		// echo "<pre>";

		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus4);
		}

		if(!empty($data['DetailAdd'])){
			$ArrDetailAdd	= array();
			foreach($DetailAdd AS $val => $valx){
				$ArrDetailAdd[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd);
		}

		if(!empty($data['DetailAdd2'])){
			$ArrDetailAdd2	= array();
			foreach($DetailAdd2 AS $val => $valx){
				$ArrDetailAdd2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd2);
		}

		if(!empty($data['DetailAdd3'])){
			$ArrDetailAdd3	= array();
			foreach($DetailAdd3 AS $val => $valx){
				$ArrDetailAdd3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd3);
		}

		if(!empty($data['DetailAdd4'])){
			$ArrDetailAdd4	= array();
			foreach($DetailAdd4 AS $val => $valx){
				$ArrDetailAdd4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd4);
		}

		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'upload_real2' => "Y",
			'upload_by2' => $data_session['ORI_User']['username'],
			'upload_date2' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);

			//Resin
			$this->db->insert_batch('production_real_detail', $ArrDetailResin);
			$this->db->insert_batch('production_real_detail', $ArrDetailResin2);
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailResin3);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus);
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus4);
			}
			//Detail Add
			if(!empty($data['DetailAdd'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd);
			}
			if(!empty($data['DetailAdd2'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd2);
			}
			if(!empty($data['DetailAdd3'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd3);
			}
			if(!empty($data['DetailAdd4'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Failed. Please try again later ...',
				'status'	=> 2,
				'produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'produksi'	=> $id_produksi
			);
			history('Add Real Production 2 '.$id_produksi.'/'.$product);
		}
		echo json_encode($Arr_Kembali);
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

	public function getTypeDelivery(){
		$sqlDel		= "SELECT * FROM delivery ORDER BY id_delivery ASC";
		$restDel	= $this->db->query($sqlDel)->result_array();

		$option	= "<option value='0'>Select An Delivery</option>";
		foreach($restDel AS $val => $valx){
			$option .= "<option value='".$valx['id_delivery']."'>".strtoupper($valx['delivery_name'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getProduct(){
		$category = $this->input->post('category');
		$sqlSup		= "SELECT * FROM component_header WHERE parent_product='".$category."' AND deleted <> 'Y' ORDER BY diameter ASC";
		// echo $category."<br>";
		$restSup	= $this->db->query($sqlSup)->result_array();
		$dataNum	= $this->db->query($sqlSup)->num_rows();
		// echo $dataNum;
		// $option = "";
		if($dataNum > 0 ){
			$option	= "<option value='0'>Select An Product</option>";
			foreach($restSup AS $val => $valx){
				$elbowmould	= "";
				if($category == 'elbow mould'){
					$elbowmould	= "[".$valx['type_elbow']." | ".$valx['angle']."]";
				}
				$option .= "<option value='".$valx['id_product']."'>".strtoupper($valx['nm_product'])." [".$valx['diameter']." x ".$valx['panjang']." x ".$valx['design']."] ".$elbowmould." | ".$valx['est']."</option>";
			}
		}
		elseif($category != 0 AND $dataNum == 0){
			$option	= "<option value='0'>Empty Product</option>";
		}
		elseif($category == 0 AND $dataNum == 0){
			$option	= "<option value='0'>Empty List</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function updateReal(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$id_produksi	= $data['id_produksi'];
			$Imp			= explode('-', $id_produksi);

			$dataUpdate = array(
				'plan_start_produksi' => $data['plan_start_produksi'],
				'plan_end_produksi' => $data['plan_end_produksi'],
				'sts_produksi' => 'FINISH',
				'modified_by' => $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
			);

			$dtUpdIpp = array(
				'status' => 'FINISH',
			);

			$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi)->update('production_header', $dataUpdate);
			$this->db->where('no_ipp', $Imp[1])->update('production', $dtUpdIpp);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Failed. Please try again later ...',
					'status'	=> 2,
					'produksi'	=> $id_produksi
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Success. Thank you & have a nice day ...',
					'status'	=> 1,
					'produksi'	=> $id_produksi
				);
				history('Finish Production code : '.$id_produksi);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
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
								b.no_komponen
							FROM
								production_detail a LEFT JOIN bq_detail_header b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."' ";
			$rowD	= $this->db->query($qDetail)->result_array();

			$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
			$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();

			$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
			$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
			// echo $qDetailBtn;
			$data = array(
				'title'		=> 'Upload Production',
				'action'	=> 'updateReal',
				'row'		=> $row,
				'rowD'		=> $rowD,
				'numB'		=> $rowDBtn,
				'numB2'		=> $rowDBtn2
			);
			$this->load->view('Production/updateReal',$data);
		}
	}

	public function print_spk_produksi(){
		$kode_produksi	= $this->uri->segment(3);
		$kode_product	= $this->uri->segment(4);
		$product_to		= $this->uri->segment(5);
		$id_delivery	= $this->uri->segment(6);
		$id				= $this->uri->segment(7);
		$id_milik		= $this->uri->segment(8);
		$qty			= ($this->uri->segment(9) != '')?$this->uri->segment(9):1;
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' 	=> $Nama_Beda,
			'printby' 		=> $printby,
			'kode_produksi' => $kode_produksi,
			'kode_product' 	=> $kode_product,
			'product_to' 	=> $product_to,
			'id_delivery' 	=> $id_delivery,
			'id' 			=> $id,
			'id_milik' 		=> $id_milik,
			'qty' 			=> $qty
		);
		history('Print SPK Production '.$kode_produksi.'/'.$kode_product);
		$this->load->view('Print/print_spk_produksi', $data);
	}

	

	public function printRealProduction(){
		$kode_produksi			= $this->uri->segment(3);
		$kode_product			= $this->uri->segment(4);
		$product_to				= $this->uri->segment(5);
		$id_production_detail	= $this->uri->segment(6);
		$id_delivery			= $this->uri->segment(7);
		$id_milik	= $this->uri->segment(8);
		$data_session			= $this->session->userdata;
		$printby				= $data_session['ORI_User']['username'];
		$koneksi				= akses_server_side();

		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print Real Production '.$kode_produksi.'/'.$kode_product);

		PrintSPKRealOri($Nama_Beda, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_production_detail, $id_delivery, $id_milik);
	}

	

	public function getDataJSON2(){

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON2(
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
			$nestedData[]	= "<div align='left'>".$row['id_produksi']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_category'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			if($row['product_ke'] != $row['qty_akhir']){
				$Prodc = $row['product_ke']." to ".$row['qty_akhir'];
			}
			else{
				$Prodc = $row['product_ke'];
			}
			$nestedData[]	= "<div align='left'>Product <b>".$Prodc."</b> of <b>".$row['qty']."<b></div>";
			$nestedData[]	= "<div align='center'>".$row['status_by']."</div>";
			$nestedData[]	= "<div align='left'>".date('Y-m-d H:i:s', strtotime($row['status_date']))."</div>";
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					$priX	= "";
			$nestedData[]	= "<div align='center'>
									<button class='btn btn-sm btn-success Perbandingan' title='Detail Production' data-awal='".$row['product_ke']."' data-akhir='".$row['qty_akhir']."' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_pro_detail='".$row['id_production_detail']."'><i class='fa fa-eye'></i></button>
									".$priX."
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

	public function queryDataJSON2($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				table_history_pro_header a
			WHERE (
				a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.status_date LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'status_date',
			1 => 'id_produksi',
			2 => 'id_category',
			3 => 'id_product',
			4 => 'product_ke'
		);

		$sql .= " ORDER BY status_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


	public function UpdateProduksiBEF1402() {
		$Arr_Kembali		= array();
		$data				= $this->input->post();
		$YM	= date('ym');
		$Y	= date('y');

		$plan_start_produksi	= $data['plan_start_produksi'];
		$plan_end_produksi		= $data['plan_end_produksi'];
		$id_mesin				= $data['id_mesin'];
		$id_produksi			= $data['id_produksi'];
		$so_number				= explode('-',$data['so_number']);

		$no_ipp = $so_number[1];

		$qMch	= "SELECT nm_mesin FROM machine WHERE id_mesin = '".$data['id_mesin']."' LIMIT 1";
		$dMch	= $this->db->query($qMch)->result();

		$Arr_Update			= array(
			'plan_start_produksi'	=> $plan_start_produksi,
			'plan_end_produksi'		=> $plan_end_produksi,
			'id_mesin'				=> $id_mesin,
			'sts_produksi'			=> "PROCESS PRODUCTION",
			'nm_mesin'				=> $dMch[0]->nm_mesin,
			'modified_by'			=> $this->session->userdata['ORI_User']['username'],
			'modified_date'			=> date('Y-m-d H:i:s')
		);

		$Arr_Update2			= array(
			'status'			=> "PROCESS PRODUCTION"
		);

		$restGet = "SELECT
						a.id,
						a.id_bq,
						a.id_category,
						a.no_spk,
						b.type
					FROM
						bq_detail_header a
						LEFT JOIN product_parent b ON a.id_category = b.product_parent
					WHERE
						a.id_bq = 'BQ-".$no_ipp."'";
		$getRes	= $this->db->query($restGet)->result_array();

		$ArrDes = array();
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


			$srcMtr			= "SELECT MAX(no_spk) as maxP FROM bq_detail_header WHERE no_spk LIKE '".$simbol.$Y.".%' ";


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
			$this->db->update('bq_detail_header');

			history('Create SPK Produksi: '.$no_spk.' / '.$no_ipp);
		}

		$this->db->trans_start();
		$this->db->where('id_produksi', $id_produksi);
		$this->db->update('production_header', $Arr_Update);

		$this->db->where('no_ipp', $no_ipp);
		$this->db->update('production', $Arr_Update2);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Update So data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Update So data success. Thanks ...',
				'status'	=> 1
			);
			history('Ready production kode: '.$id_produksi);
		}

		echo json_encode($Arr_Kembali);
	}

	public function UpdateProduksi() {
		$Arr_Kembali		= array();
		$data				= $this->input->post();
		$YM	= date('ym');
		$Y	= date('y');

		$plan_start_produksi	= $data['plan_start_produksi'];
		$plan_end_produksi		= $data['plan_end_produksi'];
		$id_mesin				= $data['id_mesin'];
		$id_produksi			= $data['id_produksi'];
		$so_number				= explode('-',$data['so_number']);

		$no_ipp = $so_number[1];

		//pembeda produksi
		$qSupplier	= "SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
		}

		$qMch	= "SELECT nm_mesin FROM machine WHERE id_mesin = '".$data['id_mesin']."' LIMIT 1";
		$dMch	= $this->db->query($qMch)->result();

		$Arr_Update			= array(
			'plan_start_produksi'	=> $plan_start_produksi,
			'plan_end_produksi'		=> $plan_end_produksi,
			'id_mesin'				=> $id_mesin,
			'sts_produksi'			=> "PROCESS PRODUCTION",
			'nm_mesin'				=> $dMch[0]->nm_mesin,
			'modified_by'			=> $this->session->userdata['ORI_User']['username'],
			'modified_date'			=> date('Y-m-d H:i:s')
		);

		$Arr_Update2			= array(
			'status'			=> "PROCESS PRODUCTION"
		);

		// $restGet = "SELECT a.id, a.id_bq, a.id_category, a.no_spk, b.type
					// FROM
						// ".$HelpDet." a
						// LEFT JOIN product_parent b ON a.id_category = b.product_parent
					// WHERE
						// a.id_bq = 'BQ-".$no_ipp."'";
		// $getRes	= $this->db->query($restGet)->result_array();

		// $ArrDes = array();
		// foreach($getRes AS $val => $valx){
			// if($valx['type'] == 'pipe'){
				// $simbol = '20P.';
			// }
			// if($valx['type'] == 'fitting'){
				// $simbol = '30F.';
			// }
			// if($valx['type'] == 'joint' OR $valx['type'] == 'field'){
				// $simbol = '60A.';
			// }


			// $srcMtr			= "SELECT MAX(no_spk) as maxP FROM nomor_spk WHERE no_spk LIKE '".$simbol.$Y.".%' ";


			// $numrowMtr		= $this->db->query($srcMtr)->num_rows();
			// $resultMtr		= $this->db->query($srcMtr)->result_array();
			// $angkaUrut2		= $resultMtr[0]['maxP'];
			// $urutan2		= (int)substr($angkaUrut2, 7, 4);
			// $urutan2++;
			// $urut2			= sprintf('%04s',$urutan2);
			// $no_spk			= $simbol.$Y.".".$urut2;
			// echo $no_spk;
			// exit;

			// $this->db->set('no_spk', $no_spk);
			// $this->db->where('id', $valx['id']);
			// $this->db->update($HelpDet);

			// history('Create SPK Produksi: '.$no_spk.' / '.$no_ipp);
		// }

		$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi);
			$this->db->update('production_header', $Arr_Update);

			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $Arr_Update2);


			check_approve('BQ-'.$no_ipp);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Update So data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Update So data success. Thanks ...',
				'status'	=> 1
			);
			history('Ready production kode: '.$id_produksi);
		}

		echo json_encode($Arr_Kembali);
	}

	public function UpdateCloseProduksi() {
		$Arr_Kembali	= array();
		$data			= $this->input->post();

		$real_start_produksi	= $data['real_start_produksi'];
		$real_end_produksi		= $data['real_end_produksi'];
		$id_produksi			= $data['id_produksi'];
		$no_ipp 				= str_replace('PRO-','',$id_produksi);

		$ArrUpdateProduksi	= array(
			'real_start_produksi'	=> $real_start_produksi,
			'real_end_produksi'		=> $real_end_produksi,
			'sts_produksi'			=> "FINISH",
			'modified_by'			=> $this->session->userdata['ORI_User']['username'],
			'modified_date'			=> date('Y-m-d H:i:s')
		);

		$ArrUpdateIPP = array(
			'status' => "FINISH"
		);

		$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi);
			$this->db->update('production_header', $ArrUpdateProduksi);

			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $ArrUpdateIPP);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Proccess data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Proccess data success. Thanks ...',
				'status'	=> 1
			);
			history('Close produksi : '.$no_ipp);
		}
		echo json_encode($Arr_Kembali);
	}

	public function getDataJSONUP(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONUP(
			$requestData['id_produksi'],
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
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_komponen'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_category'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['product_ke']."</span></div>";

					$btn1	= "";
					$btn2	= "";
					$btn3	= "";
					$btn4	= "";
					$btn5	= "";
					$btn6	= "";
					$btn7	= "";
					if($row['sts_produksi'] == 'Y'){
						$jumlah = $row['upload_real'];
						if($jumlah == 'N'){
							// $btn1	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='inputReal1' title='SPK 1 SEBELUM ".ucwords(strtolower($row['id_category']))." ke (".$row['product_ke'].")' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_producktion='".$row['id']."' data-id_milik='".$row['id_milik']."'><i class='fa fa-edit'></i></button>";
							$btn6	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='inputReal1New' title='SPK 1 ".ucwords(strtolower($row['id_category']))." ke (".$row['product_ke'].")' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_producktion='".$row['id']."' data-id_milik='".$row['id_milik']."'><i class='fa fa-edit'></i></button>";
						}
						else{
							$btn2	= "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
						}

						$jumlah2 = $row['upload_real2'];
						if($jumlah2 == 'N'){
							// $btn3	= "&nbsp;<button type='button' class='btn btn-sm btn-primary' id='inputReal3' title='SPK 2 ".ucwords(strtolower($row['id_category']))." ke (".$row['product_ke'].")' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_producktion='".$row['id']."' data-id_milik='".$row['id_milik']."'><i class='fa fa-edit'></i></button>";
							$btn7	= "&nbsp;<button type='button' class='btn btn-sm btn-primary' id='inputReal3New' title='SPK 2 ".ucwords(strtolower($row['id_category']))." ke (".$row['product_ke'].")' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_producktion='".$row['id']."' data-id_milik='".$row['id_milik']."'><i class='fa fa-edit'></i></button>";
						}
						else{
							$btn4	= "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
						}
					}
					else{
						$btn5	= "&nbsp;<button type='button' class='btn btn-sm btn-danger' title='SPK belum turun !!!'><i class='fa fa-close'></i></button>";
					}
			$nestedData[]	= "<div align='center'>
									".$btn1."
									".$btn6."
									".$btn2."
									".$btn3."
									".$btn7."
									".$btn4."
									".$btn5."
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

	public function queryDataJSONUP($id_produksi, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
			$row	= $this->db->query($qSupplier)->result_array();

			$HelpDet 	= "bq_detail_header";
			if($row[0]['jalur'] == 'FD'){
				$HelpDet = "so_detail_header";
			}

		$sql = "
			SELECT
				a.*,
				b.no_komponen
			FROM
				production_detail a LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
			WHERE
				a.id_produksi = '".$id_produksi."'
				AND b.id_category <> 'pipe slongsong'
				AND (
				a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_produksi'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function updateRealNew_09102019(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$id_produksi	= $data['id_produksi'];
			$Imp			= explode('-', $id_produksi);

			$dataUpdate = array(
				'plan_start_produksi' => $data['plan_start_produksi'],
				'plan_end_produksi' => $data['plan_end_produksi'],
				'sts_produksi' => 'FINISH',
				'modified_by' => $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
			);

			$dtUpdIpp = array(
				'status' => 'FINISH',
			);

			$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi)->update('production_header', $dataUpdate);
			$this->db->where('no_ipp', $Imp[1])->update('production', $dtUpdIpp);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Failed. Please try again later ...',
					'status'	=> 2,
					'produksi'	=> $id_produksi
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Success. Thank you & have a nice day ...',
					'status'	=> 1,
					'produksi'	=> $id_produksi
				);
				history('Finish Production code : '.$id_produksi);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
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
								b.no_komponen
							FROM
								production_detail a LEFT JOIN bq_detail_header b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."' ";
			$rowD	= $this->db->query($qDetail)->result_array();

			$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
			$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();

			$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
			$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
			// echo $qDetailBtn;
			$data = array(
				'title'		=> 'Upload Production',
				'action'	=> 'updateReal',
				'row'		=> $row,
				'rowD'		=> $rowD,
				'numB'		=> $rowDBtn,
				'numB2'		=> $rowDBtn2
			);
			$this->load->view('Production/updateRealNew',$data);
		}
	}

	public function updateRealNew(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$id_produksi	= $data['id_produksi'];
			$Imp			= explode('-', $id_produksi);

			$dataUpdate = array(
				'real_start_produksi' => $data['real_start_produksi'],
				'real_end_produksi' => $data['real_end_produksi'],
				'sts_produksi' => 'FINISH',
				'modified_by' => $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
			);

			$dtUpdIpp = array(
				'status' => 'FINISH',
			);

			$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi)->update('production_header', $dataUpdate);
			$this->db->where('no_ipp', $Imp[1])->update('production', $dtUpdIpp);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Failed. Please try again later ...',
					'status'	=> 2,
					'produksi'	=> $id_produksi
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Success. Thank you & have a nice day ...',
					'status'	=> 1,
					'produksi'	=> $id_produksi
				);
				history('Finish Production code : '.$id_produksi);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
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

			$HelpDet 	= "bq_detail_header";
			if($row[0]['jalur'] == 'FD'){
				$HelpDet = "so_detail_header";
			}

			$qDetail	= "	SELECT
								a.*,
								b.no_komponen
							FROM
								production_detail a LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."' ";
			$rowD	= $this->db->query($qDetail)->result_array();

			$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_category <> 'pipe slongsong' AND a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
			$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();

			$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_category <> 'pipe slongsong' AND a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
			$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
			// echo $qDetailBtn;
			$data = array(
				'title'		=> 'Upload Production',
				'action'	=> 'updateReal',
				'row'		=> $row,
				'rowD'		=> $rowD,
				'numB'		=> $rowDBtn,
				'numB2'		=> $rowDBtn2
			);
			$this->load->view('Production/updateRealNew',$data);
		}
	}

	public function getDataJSONSPK(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONSPK(
			$requestData['id_produksispk'],
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
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_komponen'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_category'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-orange'>".$row['product_ke']."</span></div>";

			$SPK1 	= "<a href='".site_url($this->uri->segment(1).'/printSPK1/'.$row['id_produksi'].'/'.$row['id_product'].'/'.$row['product_ke'].'/'.$row['id_delivery'].'/'.$row['id'].'/'.$row['id_milik'])."' class='btn btn-sm btn-success' target='_blank' title='Print SPK 1' data-role='qtip'><i class='fa fa-print'></i></a>";
			$SPK2 	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/printSPK2/'.$row['id_produksi'].'/'.$row['id_product'].'/'.$row['product_ke'].'/'.$row['id_delivery'].'/'.$row['id'].'/'.$row['id_milik'])."' class='btn btn-sm btn-primary' target='_blank' title='Print SPK Mixing' data-role='qtip'><i class='fa fa-print'></i></a>";
			$NotUp	= "<button type='button' class='btn btn-sm btn-danger' title='Not Uploaded' data-role='qtip'><i class='fa fa-minus-circle'></i></button>";
			$RdyUp	= "<button type='button' class='btn btn-sm btn-primary' title='Already Uploaded' data-role='qtip'><i class='fa fa-check'></i></button>";
			$Link1	= "<a href='".site_url($this->uri->segment(1).'/printRealProduction/'.$row['id_produksi'].'/'.$row['id_product'].'/'.$row['product_ke'].'/'.$row['id'].'/'.$row['id_delivery'].'/'.$row['id_milik'])."' class='btn btn-sm btn-success' target='_blank' title='Print Comparison' data-role='qtip'><i class='fa fa-print'></i></a>";
			$Link2	= "&nbsp;<button type='button' id='Perbandingan' class='btn btn-sm btn-primary' data-id_product = '".$row['id_product']."' data-id_pro_detail = '".$row['id']."' data-id_produksi = '".$row['id_produksi']."' data-id_milik = '".$row['id_milik']."' title='Production Comparison'><i class='fa fa-balance-scale '></i></button>";

			$SPKSlongsong 	= "";
			if($row['id_category'] == 'flange slongsong'){
				// $SPKSlongsong 	= "<a href='".site_url($this->uri->segment(1).'/printSPKSlong/'.$row['id_produksi'].'/'.$row['id_product'].'/'.$row['product_ke'].'/'.$row['id_delivery'].'/'.$row['id'].'/'.$row['id_milik'])."' class='btn btn-sm btn-warning' target='_blank' title='Print SPK Slongsong' data-role='qtip'><i class='fa fa-print'></i></a>";
			}

			if($row['upload_real'] == 'N' OR $row['upload_real2'] == 'N'){
				if(!empty($row['id_product'])){
					$nestedData[]	= "<div align='center'>".$SPK1." ".$SPKSlongsong."".$SPK2."</div>";
				}
				if(!empty($row['id_product'])){
					$nestedData[]	= "<div align='center'>".$NotUp."</div>";
				}
			}
			else{
				$nestedData[]	= "<div align='center'>".$RdyUp."</div>";

				$nestedData[]	= "<div align='center'>".$Link1." ".$Link2."</div>";
			}

			$stsX	= "";
			if(!empty($row['id_product'])){
				$stsX	= "<button type='button' id='realSPK' class='btn btn-sm btn-success' data-id_product = '".$row['id_product']."' data-id_pro_detail = '".$row['id']."' data-id_produksi = '".$row['id_produksi']."' title='Turunkan SPK !'><i class='fa fa-calendar-check-o '></i></button>";
			}

			$stsX2 = "<td align='center'><button type='button' class='btn btn-sm btn-primary' title='SPK sudah turun' data-role='qtip'><i class='fa fa-check'></i></button></td>";

			if($row['sts_produksi'] == 'N'){
				$nestedData[]	= "<div align='center'>".$stsX."</div>";
			}
			else{
				$nestedData[]	= "<div align='center'>".$stsX2."</div>";
			}

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

	public function queryDataJSONSPK($id_produksi, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row	= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
		}
		$sql = "
			SELECT
				a.*,
				b.no_komponen
			FROM
				production_detail a LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
			WHERE
				a.id_produksi = '".$id_produksi."'
				AND b.id_category <> 'pipe slongsong'
					AND (
				a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_produksi'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function ExcelProduksi(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_produksi		= $this->uri->segment(3);

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
		  $styleArray5 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
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

		$DataEx		= explode('-', $id_produksi);
		$qHeaderx		= "SELECT * FROM production WHERE no_ipp='".$DataEx[1]."' ";
		$getData		= $this->db->query($qHeaderx)->row();

		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
		}

		$qDetail1		= "	SELECT
								a.*,
								b.no_komponen,
								b.no_spk
							FROM
								production_detail a
								LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."'
							GROUP BY
								b.no_komponen,
								a.sts_delivery,
								a.id_product
							ORDER BY
								b.id_bq_header  ASC";
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($qDetail1)->result_array();


		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'DETAIL PRODUKSI');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'IPP');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet->setCellValue('B'.$NewRow, $DataEx[1]);
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No SO');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet->setCellValue('B'.$NewRow, 'SO-'.$DataEx[1]);
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'Project');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet->setCellValue('B'.$NewRow, $getData->project);
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		// $nm_category	= $row_Cek['no_komponen'];
		// $Cols			= getColsChar($awal_col);
		// $sheet->setCellValue($Cols.$awal_row, $nm_category);
		// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'Product Delivery');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'Product');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'Product Code');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Qty');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'Note');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'No SPK');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Qty SO');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'Qty Approve SO');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Qty Turun SPK');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Qty Belum Turun SPK');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);	


		// echo $qDetail1; exit;

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$sqlCheck 		= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$row_Cek['id_milik'],'id_produksi'=>$row_Cek['id_produksi'],'sts_produksi'=>'Y'))->result();
				$sqlCheckRed 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$row_Cek['id_milik'],'id_produksi'=>$row_Cek['id_produksi'],'sts_produksi'=>'N'))->result();
				
				$QTY_APP_SO 	= $sqlCheck[0]->Numc + $sqlCheckRed[0]->Numc;

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$nm_category	= $row_Cek['no_komponen'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$nm_material	= strtoupper($row_Cek['id_category']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$cost	= $row_Cek['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$est_material	= $row_Cek['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$est_harga	= "";
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= $row_Cek['no_spk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $row_Cek['qty']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_APP_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sqlCheck[0]->Numc);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sqlCheckRed[0]->Numc);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

			}
		}


		$sheet->setTitle('Detail Produksi');
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
		header('Content-Disposition: attachment;filename="Detail Produksi '.$id_produksi.' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	//New

	public function getDataJSONUP2_2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONUP2_2(
			$requestData['id_produksi'],
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
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_komponen'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_category'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			if($row['qty_awal'] <> $row['qty_akhir']){
				$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['qty_awal']." - ".$row['qty_akhir']."</span></div>";
			}
			if($row['qty_awal'] == $row['qty_akhir']){
				$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['product_ke']."</span></div>";
			}


					$btn1	= "";
					$btn2	= "";
					$btn3	= "";
					$btn4	= "";
					$btn5	= "";
					$btn6	= "";
					$btn7	= "";
					if($row['sts_produksi'] == 'Y'){
						// $jumlah = $row['upload_real'];
						// if($jumlah == 'N'){
							// $btn6	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='inputReal1New' title='SPK 1 ".ucwords(strtolower($row['id_category']))." ke (".$row['product_ke'].")' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_producktion='".$row['id']."' data-id_milik='".$row['id_milik']."' data-awal='".$row['qty_awal']."' data-akhir='".$row['qty_akhir']."'><i class='fa fa-edit'></i></button>";
						// }
						// else{
							// $btn2	= "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
						// }

						$jumlah2 = $row['upload_real2'];
						if($jumlah2 == 'N'){
							$btn7	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='inputReal3New' title='SPK 2 ".ucwords(strtolower($row['id_category']))." ke (".$row['product_ke'].")' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_producktion='".$row['id']."' data-id_milik='".$row['id_milik']."' data-awal='".$row['qty_awal']."' data-akhir='".$row['qty_akhir']."'><i class='fa fa-edit'></i></button>";
						}
						else{
							$btn4	= "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
						}
					}
					else{
						$btn5	= "&nbsp;<button type='button' class='btn btn-sm btn-danger' title='SPK belum turun !!!'><i class='fa fa-close'></i></button>";
					}
			$nestedData[]	= "<div align='center'>
									".$btn1."
									".$btn6."
									".$btn2."
									".$btn3."
									".$btn7."
									".$btn4."
									".$btn5."
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

	public function queryDataJSONUP2_2($id_produksi, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_komponen
			FROM
				update_real_list_mixing a LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id,
				(SELECT @row:=0) r
			WHERE
				a.id_produksi = '".$id_produksi."'
				AND b.id_category <> 'pipe slongsong'
				AND (
					a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.no_komponen LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_komponen',
			2 => 'id_category',
			3 => 'id_product'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function spk_turun_all(){
		$id = $this->uri->segment(3);
		$id_produksi = $this->uri->segment(4);
		$menu_baru = $this->uri->segment(5);
		$data_session	= $this->session->userdata;

		$sqlTurunAll = "SELECT * FROM production_detail WHERE id_milik='".$id."' AND id_produksi='".$id_produksi."' AND sts_produksi = 'N' ";
		$restTurunAll = $this->db->query($sqlTurunAll)->result_array();

		$ArrTurunAll = array();
		foreach($restTurunAll AS $val=>$valx){
			$ArrTurunAll[$val]['id'] = $valx['id'];
			$ArrTurunAll[$val]['sts_produksi'] = 'Y';
			$ArrTurunAll[$val]['sts_produksi_by'] = $data_session['ORI_User']['username'];
			$ArrTurunAll[$val]['sts_produksi_date'] = date('Y-m-d H:i:s');
		}

		$ArrHeader		= array(
			'sts_produksi' 		=> 'PROCESS PRODUCTION',
			'modified_by' 	=> $data_session['ORI_User']['username'],
			'modified_date' => date('Y-m-d H:i:s')
		);
		$this->db->trans_start();
		$this->db->update_batch('production_detail', $ArrTurunAll, 'id');

		$this->db->where('id_produksi', $id_produksi);
		$this->db->update('production_header', $ArrHeader);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 0,
				'menu_baru' => $menu_baru,
				'id_produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1,
				'menu_baru' => $menu_baru,
				'id_produksi'	=> $id_produksi
			);
			history('Update SPK Turun : '.$id);
		}
		echo json_encode($Arr_Data);
	}

	

	public function printSPK1MergeNew(){
		$data 		= $this->input->post();
		$id			= $this->uri->segment(3);
		$qty_print 	= $this->uri->segment(4);
		$spk 		= $this->uri->segment(5);

		$SpkMn 		= ($spk == '1')?'print_spk_produksi':'printSPK2';

		if($spk == '1'){
			$ListPrdKe	= $this->db->query("SELECT * FROM production_detail WHERE id ='".$id."' LIMIT 1")->result_array();
			$AngkaFirst	= $this->db->query("SELECT * FROM production_detail WHERE id_milik ='".$ListPrdKe[0]['id_milik']."' AND print_merge='N' ORDER BY id ASC LIMIT 1")->result_array();
			$dtQty = $AngkaFirst[0]['product_ke']."-".($AngkaFirst[0]['product_ke'] + ($qty_print-1));
			$qUpdate = $this->db->query("update production_detail SET print_merge='Y', print_merge_by='".$this->session->userdata['ORI_User']['username']."', print_merge_date='".date('Y-m-d H:i:s')."', print_merge2='Y', print_merge2_by='".$this->session->userdata['ORI_User']['username']."', print_merge2_date='".date('Y-m-d H:i:s')."' WHERE id_milik='".$ListPrdKe[0]['id_milik']."' AND id_produksi='".$ListPrdKe[0]['id_produksi']."' AND print_merge='N' ORDER BY id ASC LIMIT ".$qty_print." ");

		}
		if($spk == '2'){
			$ListPrdKe	= $this->db->query("SELECT * FROM production_detail WHERE id ='".$id."' LIMIT 1")->result_array();
			$AngkaFirst	= $this->db->query("SELECT * FROM production_detail WHERE id_milik ='".$ListPrdKe[0]['id_milik']."' AND print_merge2='N' ORDER BY id ASC LIMIT 1")->result_array();
			$dtQty = $AngkaFirst[0]['product_ke']."-".($AngkaFirst[0]['product_ke'] + ($qty_print-1));
			$qUpdate = $this->db->query("update production_detail SET print_merge2='Y', print_merge2_by='".$this->session->userdata['ORI_User']['username']."', print_merge2_date='".date('Y-m-d H:i:s')."' WHERE id_milik='".$ListPrdKe[0]['id_milik']."' AND id_produksi='".$ListPrdKe[0]['id_produksi']."' AND print_merge2='N' ORDER BY id ASC LIMIT ".$qty_print." ");

		}

		$kode_produksi	= $ListPrdKe[0]['id_produksi'];
		$kode_product	= $ListPrdKe[0]['id_product'];
		$product_to		= $dtQty;
		$id_delivery	= $ListPrdKe[0]['id_delivery'];
		$id_milik		= $ListPrdKe[0]['id_milik'];


		$Arr_Kembali	= array(
			'kode_produksi'	=> $kode_produksi,
			'kode_product'	=> $kode_product,
			'product_to'	=> $product_to,
			'id_delivery'	=> $id_delivery,
			'id_milik'		=> $id_milik,
			'qty_print'		=> $qty_print,
			'spk'			=> $SpkMn,
			'id'			=> $id,
			'status'		=> 1
		);
		// print_r($Arr_Kembali);
		// exit;

		echo json_encode($Arr_Kembali);
	}

	public function getDataJSON2_check(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/check_real";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON2_check(
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
			$nestedData[]	= "<div align='left'>".str_replace('PRO-','',$row['id_produksi'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_category'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			if($row['product_ke'] != $row['qty_akhir']){
				$Prodc = $row['product_ke']." to ".$row['qty_akhir'];
			}
			else{
				$Prodc = $row['product_ke'];
			}
			$nestedData[]	= "<div align='left'>Product <b>".$Prodc."</b> of <b>".$row['qty']."<b></div>";
			$nestedData[]	= "<div align='center'>".$row['status_by']."</div>";
			$nestedData[]	= "<div align='left'>".date('Y-m-d H:i:s', strtotime($row['status_date']))."</div>";
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					$priX	= "";
					$priX2	= "";
					$priX3	= "";
					if($row['check_id'] == NULL){
						if($Arr_Akses['update']=='1'){
							$priX2	= "<button class='btn btn-sm btn-primary check_real' title='Edit Real Production' data-id_product='".$row['id_product']."' data-id_milik='".$row['id_milik']."' data-id_produksi='".$row['id_produksi']."' data-id_pro_detail='".$row['id_production_detail']."' data-qty_awal='".$row['product_ke']."' data-qty_akhir='".$row['qty_akhir']."'><i class='fa fa-edit'></i></button>";
						}
						// $priX3	= "<button class='btn btn-sm btn-success check_real_deal' title='Send Real Production' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_pro_detail='".$row['id_production_detail']."'><i class='fa fa-check'></i></button>";

					}

			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-success' id='Perbandingan' title='Detail Production' data-id_product='".$row['id_product']."' data-id_milik='".$row['id_milik']."' data-id_produksi='".$row['id_produksi']."' data-id_pro_detail='".$row['id_production_detail']."' data-qty_awal='".$row['product_ke']."' data-qty_akhir='".$row['qty_akhir']."'><i class='fa fa-eye'></i></button>
									".$priX."
									".$priX2."
									".$priX3."
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

	public function queryDataJSON2_check($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.id AS check_id
			FROM
				table_history_pro_header_tmp a LEFT JOIN table_history_pro_header b ON a.id_production_detail = b.id_production_detail
			WHERE b.id IS NULL AND (
				a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.status_date LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'status_date',
			1 => 'id_produksi',
			2 => 'id_category',
			3 => 'id_product',
			4 => 'product_ke'
		);

		$sql .= " GROUP BY a.id_production_detail ORDER BY a.status_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function update_real_edit(){
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;

		if(!empty($data['DetailLiner'])){
		$DetailLiner		= $data['DetailLiner'];
		}
		if(!empty($data['DetailLinerPlus'])){
		$DetailLinerPlus	= $data['DetailLinerPlus'];
		}
		if(!empty($data['DetailLinerAdd'])){
		$DetailLinerAdd		= $data['DetailLinerAdd'];
		}

		if(!empty($data['DetailN1'])){
		$DetailN1			= $data['DetailN1'];
		}
		if(!empty($data['DetailN1Plus'])){
		$DetailN1Plus		= $data['DetailN1Plus'];
		}
		if(!empty($data['DetailN1Add'])){
		$DetailN1Add		= $data['DetailN1Add'];
		}

		if(!empty($data['DetailN2'])){
		$DetailN2			= $data['DetailN2'];
		}
		if(!empty($data['DetailN2Plus'])){
		$DetailN2Plus		= $data['DetailN2Plus'];
		}
		if(!empty($data['DetailN2Add'])){
		$DetailN2Add		= $data['DetailN2Add'];
		}

		if(!empty($data['DetailStructure'])){
		$DetailStructure	= $data['DetailStructure'];
		}
		if(!empty($data['DetailSturcturePlus'])){
		$DetailSturcturePlus= $data['DetailSturcturePlus'];
		}
		if(!empty($data['DetailStructureAdd'])){
		$DetailStructureAdd	= $data['DetailStructureAdd'];
		}

		if(!empty($data['DetailExternal'])){
		$DetailExternal		= $data['DetailExternal'];
		}
		if(!empty($data['DetailExternalPlus'])){
		$DetailExternalPlus	= $data['DetailExternalPlus'];
		}
		if(!empty($data['DetailExternalAdd'])){
		$DetailExternalAdd	= $data['DetailExternalAdd'];
		}

		if(!empty($data['DetailTCPlus'])){
		$DetailTCPlus		= $data['DetailTCPlus'];
		}
		if(!empty($data['DetailTCAdd'])){
		$DetailTCAdd		= $data['DetailTCAdd'];
		}

		if(!empty($data['DetailLiner'])){
			$ArrDetailLiner = array();
			foreach($DetailLiner AS $val => $valx){
				$ArrDetailLiner[$val]['id'] = $valx['id_real'];
				$ArrDetailLiner[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailLiner[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailLiner[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailLiner[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailLiner[$val]['status_date'] = date('Y-m-d H:i:s');

			}
		}
		// print_r($ArrDetailLiner); exit;
		if(!empty($data['DetailLinerPlus'])){
			$ArrDetailLinerPlus = array();
			foreach($DetailLinerPlus AS $val => $valx){
				$ArrDetailLinerPlus[$val]['id'] = $valx['id_real'];
				$ArrDetailLinerPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailLinerPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailLinerPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailLinerPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailLinerPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailLinerAdd'])){
			$ArrDetailLinerAdd = array();
			foreach($DetailLinerAdd AS $val => $valx){
				$ArrDetailLinerAdd[$val]['id'] = $valx['id_real'];
				$ArrDetailLinerAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailLinerAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailLinerAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailLinerAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailLinerAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailN1'])){
			$ArrDetailN1 = array();
			foreach($DetailN1 AS $val => $valx){
				$ArrDetailN1[$val]['id'] = $valx['id_real'];
				$ArrDetailN1[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailN1[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailN1[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailN1[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailN1[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailN1Plus'])){
			$ArrDetailN1Plus = array();
			foreach($DetailN1Plus AS $val => $valx){
				$ArrDetailN1Plus[$val]['id'] = $valx['id_real'];
				$ArrDetailN1Plus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailN1Plus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailN1Plus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailN1Plus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailN1Plus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailN1Add'])){
			$ArrDetailN1Add = array();
			foreach($DetailN1Add AS $val => $valx){
				$ArrDetailN1Add[$val]['id'] = $valx['id_real'];
				$ArrDetailN1Add[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailN1Add[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailN1Add[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailN1Add[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailN1Add[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailN2'])){
			$ArrDetailN2 = array();
			foreach($DetailN2 AS $val => $valx){
				$ArrDetailN2[$val]['id'] = $valx['id_real'];
				$ArrDetailN2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailN2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailN2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailN2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailN2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailN2Plus'])){
			$ArrDetailN2Plus = array();
			foreach($DetailN2Plus AS $val => $valx){
				$ArrDetailN2Plus[$val]['id'] = $valx['id_real'];
				$ArrDetailN2Plus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailN2Plus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailN2Plus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailN2Plus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailN2Plus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailN2Add'])){
			$ArrDetailN2Add = array();
			foreach($DetailN2Add AS $val => $valx){
				$ArrDetailN2Add[$val]['id'] = $valx['id_real'];
				$ArrDetailN2Add[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailN2Add[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailN2Add[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailN2Add[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailN2Add[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailStructure'])){
			$ArrDetailStructure = array();
			foreach($DetailStructure AS $val => $valx){
				$ArrDetailStructure[$val]['id'] = $valx['id_real'];
				$ArrDetailStructure[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailStructure[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailStructure[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailStructure[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailStructure[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailSturcturePlus'])){
			$ArrDetailSturcturePlus = array();
			foreach($DetailSturcturePlus AS $val => $valx){
				$ArrDetailSturcturePlus[$val]['id'] = $valx['id_real'];
				$ArrDetailSturcturePlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailSturcturePlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailSturcturePlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailSturcturePlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailSturcturePlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailStructureAdd'])){
			$ArrDetailStructureAdd = array();
			foreach($DetailStructureAdd AS $val => $valx){
				$ArrDetailStructureAdd[$val]['id'] = $valx['id_real'];
				$ArrDetailStructureAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailStructureAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailStructureAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailStructureAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailStructureAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailExternal'])){
			$ArrDetailExternal = array();
			foreach($DetailExternal AS $val => $valx){
				$ArrDetailExternal[$val]['id'] = $valx['id_real'];
				$ArrDetailExternal[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailExternal[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailExternal[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailExternal[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailExternal[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailExternalPlus'])){
			$ArrDetailExternalPlus = array();
			foreach($DetailExternalPlus AS $val => $valx){
				$ArrDetailExternalPlus[$val]['id'] = $valx['id_real'];
				$ArrDetailExternalPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailExternalPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailExternalPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailExternalPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailExternalPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailExternalAdd'])){
			$ArrDetailExternalAdd = array();
			foreach($DetailExternalAdd AS $val => $valx){
				$ArrDetailExternalAdd[$val]['id'] = $valx['id_real'];
				$ArrDetailExternalAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailExternalAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailExternalAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailExternalAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailExternalAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailTCPlus'])){
			$ArrDetailTCPlus = array();
			foreach($DetailTCPlus AS $val => $valx){
				$ArrDetailTCPlus[$val]['id'] = $valx['id_real'];
				$ArrDetailTCPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailTCPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailTCPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailTCPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailTCPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($data['DetailTCAdd'])){
			$ArrDetailTCAdd = array();
			foreach($DetailTCAdd AS $val => $valx){
				$ArrDetailTCAdd[$val]['id'] = $valx['id_real'];
				$ArrDetailTCAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailTCAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailTCAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailTCAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailTCAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}

		// print_r($ArrDetailLiner); exit;

		$this->db->trans_start();
			if(!empty($data['DetailLiner'])){
				$this->db->update_batch('tmp_production_real_detail', $ArrDetailLiner, 'id');
			}
			if(!empty($data['DetailLinerPlus'])){
				$this->db->update_batch('tmp_production_real_detail_plus', $ArrDetailLinerPlus, 'id');
			}
			if(!empty($data['DetailLinerAdd'])){
				$this->db->update_batch('tmp_production_real_detail_add', $ArrDetailLinerAdd, 'id');
			}

			if(!empty($data['DetailN1'])){
				$this->db->update_batch('tmp_production_real_detail', $ArrDetailN1, 'id');
			}
			if(!empty($data['DetailN1Plus'])){
				$this->db->update_batch('tmp_production_real_detail_plus', $ArrDetailN1Plus, 'id');
			}
			if(!empty($data['DetailN1Add'])){
				$this->db->update_batch('tmp_production_real_detail_add', $ArrDetailN1Add, 'id');
			}

			if(!empty($data['DetailN2'])){
				$this->db->update_batch('tmp_production_real_detail', $ArrDetailN2, 'id');
			}
			if(!empty($data['DetailN2Plus'])){
				$this->db->update_batch('tmp_production_real_detail_plus', $ArrDetailN2Plus, 'id');
			}
			if(!empty($data['DetailN2Add'])){
				$this->db->update_batch('tmp_production_real_detail_add', $ArrDetailN2Add, 'id');
			}

			if(!empty($data['DetailStructure'])){
				$this->db->update_batch('tmp_production_real_detail', $ArrDetailStructure, 'id');
			}
			if(!empty($data['DetailSturcturePlus'])){
				$this->db->update_batch('tmp_production_real_detail_plus', $ArrDetailSturcturePlus, 'id');
			}
			if(!empty($data['DetailStructureAdd'])){
				$this->db->update_batch('tmp_production_real_detail_add', $ArrDetailStructureAdd, 'id');
			}

			if(!empty($data['DetailExternal'])){
				$this->db->update_batch('tmp_production_real_detail', $ArrDetailExternal, 'id');
			}
			if(!empty($data['DetailExternalPlus'])){
				$this->db->update_batch('tmp_production_real_detail_plus', $ArrDetailExternalPlus, 'id');
			}
			if(!empty($data['DetailExternalAdd'])){
				$this->db->update_batch('tmp_production_real_detail_add', $ArrDetailExternalAdd, 'id');
			}

			if(!empty($data['DetailTCPlus'])){
				$this->db->update_batch('tmp_production_real_detail_plus', $ArrDetailTCPlus, 'id');
			}
			if(!empty($data['DetailTCAdd'])){
				$this->db->update_batch('tmp_production_real_detail_add', $ArrDetailTCAdd, 'id');
			}
  		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Failed Add Default. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Success Add Default. Thanks ...',
				'status'	=> 1
			);
			history('Edit Real in Check Real Produksi = '.$data['id_produksi'].' / '.$data['id_milik'].' / '.$data['id_product']);
		}
		echo json_encode($Arr_Kembali);

	}

	public function real_send(){
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$id_product				= $data['id_product'];
		$id_milik				= $data['id_milik'];
		$id_milik2				= $data['id_milik2'];

		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet3 	= "tmp_production_real_detail";
		$HelpDet4 	= "tmp_production_real_detail_plus";
		$HelpDet5 	= "tmp_production_real_detail_add";
		// if($row[0]['jalur'] == 'FD'){
		// 	$HelpDet3 	= "tmp_fd_banding_mat_detail";
		// 	$HelpDet4 	= "tmp_fd_banding_mat_plus";
		// 	$HelpDet5 	= "tmp_fd_banding_mat_add";
		// }

		$qDetail1		= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_production_detail='".$id_milik."' ";
		$qDetail2		= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_production_detail='".$id_milik."') ";
		$qDetail3		= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_production_detail='".$id_milik."') ";
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();

		$qDetail2Mix		= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_production_detail='".$id_milik2."') ";
		$qDetail3Mix		= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_production_detail='".$id_milik2."') ";
		$restDetail2Mix		= $this->db->query($qDetail2Mix)->result_array();
		$restDetail3Mix		= $this->db->query($qDetail3Mix)->result_array();

		$NumqDetail2		= "SELECT a.* FROM production_real_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_production_detail='".$id_milik2."' ";
		$NumqDetail3		= "SELECT a.* FROM production_real_detail_add a WHERE a.id_product='".$id_product."' AND a.id_production_detail='".$id_milik2."' ";
		$NumrestDetail2		= $this->db->query($NumqDetail2)->num_rows();
		$NumrestDetail3		= $this->db->query($NumqDetail3)->num_rows();

		// print_r($restDetail1);
		// print_r($restDetail2);
		// exit;

		if(!empty($restDetail1)){
			$ArrDetail = array();
			foreach($restDetail1 AS $val => $valx){
				$ArrDetail[$val]['id_produksi'] = $valx['id_produksi'];
				$ArrDetail[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetail[$val]['id_production_detail'] = $valx['id_production_detail'];
				$ArrDetail[$val]['id_product'] = $valx['id_product'];
				$ArrDetail[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetail[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetail[$val]['benang'] = $valx['benang'];
				$ArrDetail[$val]['bw'] = $valx['bw'];
				$ArrDetail[$val]['layer'] = $valx['layer'];
				$ArrDetail[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetail[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetail[$val]['status_date'] = date('Y-m-d H:i:s');
			}
		}
		if(!empty($restDetail2)){
			// if($NumrestDetail2 < 1){
				$ArrPlus = array();
				foreach($restDetail2 AS $val => $valx){
					$ArrPlus[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrPlus[$val]['id_detail'] = $valx['id_detail'];
					$ArrPlus[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrPlus[$val]['id_product'] = $valx['id_product'];
					$ArrPlus[$val]['batch_number'] = $valx['batch_number'];
					$ArrPlus[$val]['actual_type'] = $valx['actual_type'];
					$ArrPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrPlus[$val]['status_date'] = date('Y-m-d H:i:s');
				}
			// }
		}
		if(!empty($restDetail3)){
			// if($NumrestDetail3 < 1){
				$ArrAdd = array();
				foreach($restDetail3 AS $val => $valx){
					$ArrAdd[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrAdd[$val]['id_detail'] = $valx['id_detail'];
					$ArrAdd[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrAdd[$val]['id_product'] = $valx['id_product'];
					$ArrAdd[$val]['batch_number'] = $valx['batch_number'];
					$ArrAdd[$val]['actual_type'] = $valx['actual_type'];
					$ArrAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrAdd[$val]['status_date'] = date('Y-m-d H:i:s');
				}
			// }
		}

		//Mixing
		if(!empty($restDetail2Mix)){
			if($NumrestDetail2 < 1){
				$ArrPlusMix = array();
				foreach($restDetail2Mix AS $val => $valx){
					$ArrPlusMix[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrPlusMix[$val]['id_detail'] = $valx['id_detail'];
					$ArrPlusMix[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrPlusMix[$val]['id_product'] = $valx['id_product'];
					$ArrPlusMix[$val]['batch_number'] = $valx['batch_number'];
					$ArrPlusMix[$val]['actual_type'] = $valx['actual_type'];
					$ArrPlusMix[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrPlusMix[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrPlusMix[$val]['status_date'] = date('Y-m-d H:i:s');
				}
			}
		}
		if(!empty($restDetail3Mix)){
			if($NumrestDetail3 < 1){
				$ArrAddMix = array();
				foreach($restDetail3Mix AS $val => $valx){
					$ArrAddMix[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrAddMix[$val]['id_detail'] = $valx['id_detail'];
					$ArrAddMix[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrAddMix[$val]['id_product'] = $valx['id_product'];
					$ArrAddMix[$val]['batch_number'] = $valx['batch_number'];
					$ArrAddMix[$val]['actual_type'] = $valx['actual_type'];
					$ArrAddMix[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrAddMix[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrAddMix[$val]['status_date'] = date('Y-m-d H:i:s');
				}
			}
		}

		// print_r($ArrDetail);
		// print_r($ArrPlus);
		// print_r($ArrDetailLiner);
		// exit;

		$this->db->trans_start();
			if(!empty($restDetail1)){
				$this->db->insert_batch('production_real_detail', $ArrDetail);
			}
			if(!empty($restDetail2)){
				$this->db->insert_batch('production_real_detail_plus', $ArrPlus);
			}
			if(!empty($restDetail3)){
				$this->db->insert_batch('production_real_detail_add', $ArrAdd);
			}

			//Save Mixing
			if(!empty($restDetail2Mix)){
				if($NumrestDetail2 < 1){
					$this->db->insert_batch('production_real_detail_plus', $ArrPlusMix);
				}
			}
			if(!empty($restDetail3Mix)){
				if($NumrestDetail3 < 1){
					$this->db->insert_batch('production_real_detail_add', $ArrAddMix);
				}
			}
  		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Failed Add Default. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Success Add Default. Thanks ...',
				'status'	=> 1
			);
			history('Insert Production Real by Check Real Produksi = '.$data['id_produksi'].' / '.$data['id_milik'].' / '.$data['id_product']);
		}
		echo json_encode($Arr_Kembali);

	}

	public function progress_produksi_excel(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_produksi		= $this->uri->segment(3);
		$no_so		= $this->uri->segment(4);

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
		  $styleArray5 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
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

		$no_ipp		= str_replace('PRO-','',$id_produksi);
		$qHeaderx	= "SELECT * FROM production WHERE no_ipp='".$no_ipp."' ";
		$getData	= $this->db->query($qHeaderx)->row();

		$qDetail1	= "	SELECT
							a.*,
							b.no_komponen,
							b.id_category AS comp,
							b.id AS id_uniq,
							e.type AS typeProduct,
							c.delivery_date AS delivery_date2
						FROM
							production_detail a
							LEFT JOIN so_detail_header b ON a.id_milik = b.id
							LEFT JOIN so_bf_detail_header d ON b.id_milik = d.id
							LEFT JOIN scheduling_master c ON d.id_milik = c.id_milik
							LEFT JOIN product_parent e ON a.id_category = e.product_parent
						WHERE
							a.id_produksi = '".$id_produksi."'
						GROUP BY
							b.no_komponen,
							a.sts_delivery,
							a.id_product
						ORDER BY
							b.id_bq_header ASC";
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($qDetail1)->result_array();


		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(12);
		$sheet->setCellValue('A'.$Row, 'DETAIL PROGRESS PRODUKSI');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'IPP');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet->setCellValue('B'.$NewRow, $no_ipp);
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No SO');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet->setCellValue('B'.$NewRow, $no_so);
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'Project');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet->setCellValue('B'.$NewRow, $getData->project);
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray5);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		// $nm_category	= $row_Cek['no_komponen'];
		// $Cols			= getColsChar($awal_col);
		// $sheet->setCellValue($Cols.$awal_row, $nm_category);
		// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'PRODUCT TYPE');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'NO SPK');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'SPEC');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'PRODUCT NAME');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'QTY ORDER');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'QTY ACTUAL');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'QTY BALANCE');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'QTY DELIVERY');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'QTY FG');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'PROGRESS (%)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'DELIVERY DATE');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);


		// echo $qDetail1; exit;

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $valx){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				//check delivery
				$sqlCheck3 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'kode_delivery !='=>NULL))->result();
				$QTY_DELIVERY	=$sqlCheck3[0]->Numc;

				//check selain shop joint & type field
				if($valx['typeProduct'] != 'field'){
					$sqlCheck2 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'daycode !='=>NULL))->result();
					$QTY_PRODUCT 		= $valx['qty'];
					$QTY 		= $valx['qty'];
					$ACT 		= $sqlCheck2[0]->Numc;
					$ACT_OUT 	= $sqlCheck2[0]->Numc;
					$balance 	= $QTY - $ACT;
					$progress = 0;
					if($ACT != 0 AND $QTY != 0){
					$progress 	= ($ACT/$QTY) *(100);
					}
					if($progress == 100){
						$bgc = '#75e975';
					}
					else if($progress == 0){
						$bgc = '#f65b5b';
					}
					else{
						$bgc = '#67a4ff';
					}

					$bal_dev	=$ACT_OUT-$QTY_DELIVERY;
				}

				if($valx['typeProduct'] == 'field'){
					//check selain shop joint
					$sqlCheck2 	= $this->db->select('SUM(qty) as Numc')->get_where('outgoing_field_joint', array('id_milik'=>$valx['id_milik'],'no_ipp'=>str_replace('PRO-','',$valx['id_produksi'])))->result();
					$QTY_PRODUCT 		= $valx['qty'];
					$QTY 		= $valx['qty'];
					$ACT 		= $sqlCheck2[0]->Numc;
					$ACT_OUT 	= number_format($sqlCheck2[0]->Numc);
					$balance 	= $QTY - $ACT;
					$progress = 0;
					if($ACT != 0 AND $QTY != 0){
					$progress 	= ($ACT/$QTY) *(100);
					}
					if($progress == 100){
						$bgc = '#75e975';
					}
					else if($progress == 0){
						$bgc = '#f65b5b';
					}
					else{
						$bgc = '#67a4ff';
					}

					$bal_dev	=$ACT_OUT-$QTY_DELIVERY;
				}


				//check field joint
				if (in_array($valx['comp'], NotInProductArray())) {
					$sqlCheck2 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi']))->result();
					$QTY 		= number_format($valx['qty']);
					$QTY_ 		= $valx['qty'];
					
					$checkActShopJoin 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'closing_produksi_date !='=>NULL))->result();
					$ACT_OUT 	= number_format($checkActShopJoin[0]->Numc);
					$ACT_OUT_ 	= $checkActShopJoin[0]->Numc;

					$balance 	= number_format($QTY_ - $ACT_OUT_);
					$progress = 0;
					if($ACT_OUT_ != 0 AND $QTY_ != 0){
						$progress 	= ($ACT_OUT_/$QTY_) *(100);
					}

					$bal_dev	= $ACT_OUT - $QTY_DELIVERY;
					if($progress == 100){
						$bgc = '#75e975';
					}
					else if($progress == 0){
						$bgc = '#f65b5b';
					}
					else{
						$bgc = '#67a4ff';
					}
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$comp	= strtoupper($valx['comp']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $comp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$no_spk	= $valx['no_spk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$spec	= spec_fd($valx['id_uniq'], 'so_detail_header');
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_product	= $valx['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ACT_OUT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $balance);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_DELIVERY);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $bal_dev);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $progress);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$delivery_date	= $valx['delivery_date2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $delivery_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

			}
		}


		$sheet->setTitle('Detail Progress Produksi');
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
		header('Content-Disposition: attachment;filename="detail-progress-produksi-'.$no_so.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function data_progress_produksi_excel(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$tgl_awal		= $this->uri->segment(3);
		$tgl_akhir		= $this->uri->segment(4);

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
		  $styleArray5 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
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

		$WHERE_IPP = '';
		if($tgl_awal != '0'){
			$GET_DELIVERY_DATE_RANGE = get_delivery_date_between($tgl_awal,$tgl_akhir);
			// echo '<pre>';
			// print_r($GET_DELIVERY_DATE_RANGE);
			// exit;
			$ArrDeliv = [];
			if(!empty($GET_DELIVERY_DATE_RANGE)){
				$ArrDeliv = $GET_DELIVERY_DATE_RANGE;
				$DELIVERY_IMP = implode("','",$ArrDeliv);
				$WHERE_IPP = "AND b.no_ipp IN ('".$DELIVERY_IMP."')";
			}
		}
		$qDetail1 = "
			SELECT
				a.*,
				b.project,
				c.so_number AS so_number2
			FROM
				production_header a 
				LEFT JOIN production b ON a.no_ipp = b.no_ipp
				LEFT JOIN so_number c ON a.no_ipp = REPLACE(c.id_bq, 'BQ-', '')
		    WHERE a.deleted = 'N' AND a.sts_produksi != 'FINISH' $WHERE_IPP
			ORDER BY a.created_date DESC
		";
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($qDetail1)->result_array();


		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(7);
		$sheet->setCellValue('A'.$Row, 'PROGRESS PRODUKSI');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'SO');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'PROJECT');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'TANGGAL MULAI');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'TANGGAL DELIVERY');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'PROGRESS');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);


		// echo $qDetail1; exit;
		$GET_DELIVERY_DATE = get_delivery_date();
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $valx){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$no_ipp	= strtoupper($valx['no_ipp']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$so_number = (!empty($valx['so_number2']))?$valx['so_number2']:'';

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $so_number);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$project	= $valx['project'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$DELIVERY_DATE = (!empty($GET_DELIVERY_DATE[$valx['no_ipp']]))?$GET_DELIVERY_DATE[$valx['no_ipp']]:array();
				$DELIVERY_DATE_ = '';
				if(!empty($DELIVERY_DATE)){
					$DELIVERY_DATE_UNIQ = array_unique($DELIVERY_DATE);
					$DELIVERY_DATE_ = implode('<br>',$DELIVERY_DATE_UNIQ);
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, date('Y-m-d', strtotime($valx['created_date'])));
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $DELIVERY_DATE_);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, persen_progress_produksi($valx['id_produksi']));
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

			}
		}


		$sheet->setTitle('Progress Produksi');
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
		header('Content-Disposition: attachment;filename="progress-produksi.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	function print_qrcode($idmilik){
		$detail=str_replace("~","','",$idmilik);
		$qDetail1 = " SELECT a.* FROM production_detail a WHERE a.id_milik in ('".$detail."') ORDER BY a.id";
		$restDetail1 = $this->db->query($qDetail1)->result_array();		
		$data = array(
            'detail'	=> $restDetail1,
		);
		history('Print Qrcode'); 
		$this->load->view('Production/print_qrcode', $data);
	}

}
