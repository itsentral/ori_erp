<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_rutin extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Pengajuan_rutin_model');
		$this->load->database();
        $list_tahun=array();
		for($i=2020;$i<=(date("Y")+1);$i++){
			$list_tahun[]=$i;
		}
        $this->listtahun=$list_tahun;
		$this->waktu=array("bulan"=>"bulan","tahun"=>"tahun");
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

	//MASTER BUDGET
    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $data       = $this->Pengajuan_rutin_model->GetPengajuanRutin();
        $datdept    = $this->master_model->GetDeptCombo();
		$get_detail = $this->db->get('tr_pengajuan_rutin_detail')->result_array();
		$ArrDetail = [];
		foreach ($get_detail as $key => $value) {
			$ArrDetail[$value['no_doc']][] = $value;
		}
		
		$data = array(
			'title'			=> 'Pengajuan Pembayaran Periodik',
			'action'		=> 'index',
			'tanda'			=> 'pengajuan',
			'results'		=> $data,
			'datdept'		=> $datdept,
			'get_detail'	=> $ArrDetail,
			'listtahun'		=> $this->listtahun
		);
		history('View data pengajuan pembayaran periodik');
		$this->load->view('Pengajuan_rutin/index',$data);
	}

	public function create($key) {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $datdept  = $this->master_model->GetDeptCombo($key);

		$data = array(
			'title'			=> 'Input Pembayaran Periodik',
			'action'		=> 'index',
			'datdept'		=> $datdept
		);
		
		$this->load->view('Pengajuan_rutin/create',$data);
    }

	public function get_data() {
		$allbudget		= $this->input->post("allbudget");
        $dept       	= $this->input->post("dept");
        $tanggal           = $this->input->post("tanggal");
		$data=$this->Pengajuan_rutin_model->GetDataBudgetRutin($dept,$tanggal,$allbudget);
		$param = array(
				'save' =>1,
				'data'=>$data,
				'tahun'=>date("Y",strtotime($tanggal)),
				'bulan'=>date("m",strtotime($tanggal)),
				);
		echo json_encode($param);
	}

	public function save_data(){

        $departement	= $this->input->post("departement");
        $id				= $this->input->post("id");
		$no_doc			= $this->input->post("no_doc");
		$tanggal_doc	= $this->input->post("tanggal_doc");
		$detail_id		= $this->input->post("detail_id");
		$id_budget		= $this->input->post("id_budget");
        $coa       		= $this->input->post("coa");
        $nama           = $this->input->post("nama");
		$tanggal		= $this->input->post("tanggal");
		$tipe  			= 'rutin';
        $budget			= $this->input->post("budget");
        $nilai			= $this->input->post("nilai");
        $keterangan		= $this->input->post("keterangan");
		// exit;
			$this->db->trans_begin();
			if($no_doc=='') {
				$no_doc=$this->Pengajuan_rutin_model->GenerateAutoNumber('pengajuan_rutin');
				$dataheader =  array(
							'tipe'			=> $tipe,
							'no_doc'		=> $no_doc,
							'tanggal_doc'	=> $tanggal_doc,
							'departement'	=> $departement
							// 'nilai'=>0,
						);
				$this->db->insert('tr_pengajuan_rutin',$dataheader);

			}else{
				$dataheader =  array(
					array(
							'id'=>$id,
							'tanggal_doc'=>$tanggal_doc,
						)
					);
				$this->db->update_batch('tr_pengajuan_rutin',$dataheader,'id');
				if (is_array($detail_id)) {
					$delid=implode("','",$detail_id);
					$this->Pengajuan_rutin_model->DataDelete('tr_pengajuan_rutin_detail'," id not in ('".$delid."') and no_doc='".$no_doc."'");
				}else{
					$this->Pengajuan_rutin_model->DataDelete('tr_pengajuan_rutin_detail',"no_doc='".$no_doc."'");
				}
			}
			$config['upload_path'] = './assets/bayar_rutin/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|pdf';
			$config['remove_spaces'] = TRUE;
			$config['encrypt_name'] = TRUE;
			// echo $config['upload_path']."<br>";
			for ($x = 0; $x < count($detail_id); $x++) {
				$x1 = $x+1;
				if($detail_id[$x]!='') {
					$data = array(
								'id_budget'=>$id_budget[$x],
								'coa'=>$coa[$x],
								'nama'=>$nama[$x],
								'tanggal'=>$tanggal[$x],
								'budget'=>$budget[$x],
								'nilai'=>$nilai[$x],
								'keterangan'=>$keterangan[$x],
						);
					if(!empty($_FILES['doc_file_'.$x1]['name'])){
						$_FILES['file']['name'] = $_FILES['doc_file_'.$x1]['name'];
						$_FILES['file']['type'] = $_FILES['doc_file_'.$x1]['type'];
						$_FILES['file']['tmp_name'] = $_FILES['doc_file_'.$x1]['tmp_name'];
						$_FILES['file']['error'] = $_FILES['doc_file_'.$x1]['error'];
						$_FILES['file']['size'] = $_FILES['doc_file_'.$x1]['size'];
						$this->load->library('upload',$config); 
						// $error = array('error' => $this->upload->display_errors());	
						// print_r($_FILES['file']);				
						if($this->upload->do_upload('file')){
							// echo "Upload";
							$uploadData = $this->upload->data();
							$filename = $uploadData['file_name'];
							$data ['doc_file']=$filename;
						}
					}
					$this->Pengajuan_rutin_model->DataUpdate('tr_pengajuan_rutin_detail',$data,array('id'=>$detail_id[$x]));
				}else{
					// echo $x1.'-'.$_FILES['doc_file_'.$x1]['name']."<br>";
					$data =  array(
								'no_doc'=>$no_doc,
								'id_budget'=>$id_budget[$x],
								'coa'=>$coa[$x],
								'nama'=>$nama[$x],
								'tanggal'=>$tanggal[$x],
								'budget'=>$budget[$x],
								'nilai'=>$nilai[$x],
								'keterangan'=>$keterangan[$x],
							);
					if(!empty($_FILES['doc_file_'.$x1]['name'])){
						// echo "Masuk<br>";
						$_FILES['file']['name'] = $_FILES['doc_file_'.$x1]['name'];
						$_FILES['file']['type'] = $_FILES['doc_file_'.$x1]['type'];
						$_FILES['file']['tmp_name'] = $_FILES['doc_file_'.$x1]['tmp_name'];
						$_FILES['file']['error'] = $_FILES['doc_file_'.$x1]['error'];
						$_FILES['file']['size'] = $_FILES['doc_file_'.$x1]['size'];
						$this->load->library('upload',$config); 					
						if($this->upload->do_upload('file')){
							// echo "Upload<br>";
							$uploadData = $this->upload->data();
							$filename = $uploadData['file_name'];
							$data ['doc_file']=$filename;
						}
					}
					// echo $filename."<br>";
					$this->Pengajuan_rutin_model->DataSave('tr_pengajuan_rutin_detail',$data);
				}
			}
			// exit;
            if($this->db->trans_status()) {
                $keterangan     = "SUKSES, tambah data ";
                $status         = 1;
                $result         = TRUE;
            } else {
                $keterangan     = "GAGAL, tambah data ";
                $status         = 0;
                $result = FALSE;
            }
            history("Add pengajuan pembayaran periodik ".$departement);
			$this->db->trans_complete();
			$param = array(
					'save' => $result
					);
			echo json_encode($param);
    }

	public function save_data_new(){
		$data_session	= $this->session->userdata;
		$Username 		= $this->session->userdata['ORI_User']['username'];
		$dateTime		= date('Y-m-d H:i:s');
        $departement	= $this->input->post("departement");
        $id				= $this->input->post("id");
		$no_doc			= $this->input->post("no_doc");
		$tanggal_doc	= $this->input->post("tanggal_doc");
		$detail_id		= $this->input->post("detail_id");
		$id_budget		= $this->input->post("id_budget");
        $coa       		= $this->input->post("coa");
        $nama           = $this->input->post("nama");
		$tanggal		= $this->input->post("tanggal");
		$tipe  			= 'rutin';
        $budget			= $this->input->post("budget");
        $nilai			= $this->input->post("nilai");
        $keterangan		= $this->input->post("keterangan");

		//header data
		if($id=='') {
			$no_doc		= $this->Pengajuan_rutin_model->GenerateAutoNumber('pengajuan_rutin');
			$dataheader = array(
								'tipe'			=> $tipe,
								'no_doc'		=> $no_doc,
								'tanggal_doc'	=> $tanggal_doc,
								'departement'	=> $departement,
								'created_by'	=> $Username,
								'created_on'	=> $dateTime
							);
		}
		else{
			$dataheader = array(
								array(
									'id'			=> $id,
									'tanggal_doc'	=> $tanggal_doc,
									'modified_by'	=> $Username,
									'modified_on'	=> $dateTime
								)
							);
		}

		//detail data dan upload
		$config['upload_path'] = './assets/bayar_rutin/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|pdf';
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name'] = TRUE;
		// echo $config['upload_path']."<br>";
		for ($x = 0; $x < count($detail_id); $x++) {
			$x1 = $x+1;
			if($detail_id[$x]!='') {
				// echo 'doc_file_'.$x1;
				// exit;
				$data[$x]['id'] 		= $detail_id[$x];
				$data[$x]['id_budget'] 	= $id_budget[$x];
				$data[$x]['no_doc'] 	= $no_doc;
				$data[$x]['coa'] 		= $coa[$x];
				$data[$x]['nama'] 		= $nama[$x];
				$data[$x]['tanggal'] 	= $tanggal[$x];
				$data[$x]['budget'] 	= $budget[$x];
				$data[$x]['nilai'] 		= $nilai[$x];
				$data[$x]['keterangan'] = $keterangan[$x];
				$data[$x]['modified_by'] = $Username;
				$data[$x]['modified_on'] = $dateTime;
				
				$doc_upload = 'doc_file_'.$x1;
				if(!empty($_FILES[$doc_upload]["name"])){
					$target_dir     = "assets/bayar_rutin/";
					$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/bayar_rutin/";
					$name_file      = 'periodik_'.$x1.'_'.date('Ymdhis');
					$target_file    = $target_dir . basename($_FILES[$doc_upload]["name"]);
					$name_file_ori  = basename($_FILES[$doc_upload]["name"]);
					$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
					$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
					$file_name    	= $name_file.".".$imageFileType;
		
					if(!empty($_FILES[$doc_upload]["tmp_name"])){
						$terupload = move_uploaded_file($_FILES[$doc_upload]["tmp_name"], $nama_upload);
						$data[$x]['doc_file'] = $file_name;
					}
				}
			}
			else{
				// echo $x1.'-'.$_FILES['doc_file_'.$x1]['name']."<br>";
				$data[$x]['id_budget'] 	= $id_budget[$x];
				$data[$x]['no_doc'] 	= $no_doc;
				$data[$x]['coa'] 		= $coa[$x];
				$data[$x]['nama'] 		= $nama[$x];
				$data[$x]['tanggal'] 	= $tanggal[$x];
				$data[$x]['budget'] 	= $budget[$x];
				$data[$x]['nilai'] 		= $nilai[$x];
				$data[$x]['keterangan'] = $keterangan[$x];
				$data[$x]['created_by'] = $Username;
				$data[$x]['created_on'] = $dateTime;

				$doc_upload = 'doc_file_'.$x1;
				if(!empty($_FILES[$doc_upload]["name"])){
					$target_dir     = "assets/bayar_rutin/";
					$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/bayar_rutin/";
					$name_file      = 'periodik_'.$x1.'_'.date('Ymdhis');
					$target_file    = $target_dir . basename($_FILES[$doc_upload]["name"]);
					$name_file_ori  = basename($_FILES[$doc_upload]["name"]);
					$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
					$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
					$file_name    	= $name_file.".".$imageFileType;
		
					if(!empty($_FILES[$doc_upload]["tmp_name"])){
						$terupload = move_uploaded_file($_FILES[$doc_upload]["tmp_name"], $nama_upload);
						$data[$x]['doc_file'] = $file_name;
					}
				}
			}
		}

		// echo $no_doc;
		// print_r($dataheader);
		// print_r($data);
		// exit();

		$this->db->trans_start();
			if($id == ''){
				$this->db->insert('tr_pengajuan_rutin',$dataheader);
				$this->db->insert_batch('tr_pengajuan_rutin_detail',$data);
			}
			else{
				$this->db->update_batch('tr_pengajuan_rutin',$dataheader,'id');
				$this->db->update_batch('tr_pengajuan_rutin_detail',$data,'id');
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1
			);
			history("Add pengajuan pembayaran periodik : ".$no_doc);
		}
		echo json_encode($Arr_Kembali);
    }

	public function view($id) {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$data	= $this->Pengajuan_rutin_model->GetDataPengajuanRutin($id);

		if($Arr_Akses['read'] !='1' AND !$data){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('pengajuan_rutin'));
		}

		$datdept  		= $this->master_model->GetDeptCombo($data->departement);
		$data_detail	= $this->Pengajuan_rutin_model->GetDataPengajuanRutinDetail($data->no_doc);

		$data = array(
			'title'			=> 'Detail Pembayaran Periodik',
			'action'		=> 'index',
			'type'			=> 'view',
			'datdept'		=> $datdept,
			'data_detail'	=> $data_detail,
			'data'			=> $data
		);
		
		$this->load->view('Pengajuan_rutin/create',$data);
    }

	public function edit($id) {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$data	= $this->Pengajuan_rutin_model->GetDataPengajuanRutin($id);

		if($Arr_Akses['read'] !='1' AND !$data){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('pengajuan_rutin'));
		}

		$datdept  		= $this->master_model->GetDeptCombo($data->departement);
		$data_detail	= $this->Pengajuan_rutin_model->GetDataPengajuanRutinDetail($data->no_doc);

		$data = array(
			'title'			=> 'Edit Pembayaran Periodik',
			'action'		=> 'index',
			'datdept'		=> $datdept,
			'data_detail'	=> $data_detail,
			'data'			=> $data
		);
		
		$this->load->view('Pengajuan_rutin/create',$data);
    }

	public function request($id) {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$data	= $this->Pengajuan_rutin_model->GetDataPengajuanRutin($id);

		if($Arr_Akses['read'] !='1' AND !$data){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('pengajuan_rutin'));
		}

		$datdept  		= $this->master_model->GetDeptCombo($data->departement);
		$data_detail	= $this->Pengajuan_rutin_model->GetDataPengajuanRutinDetail($data->no_doc);

		$data = array(
			'title'			=> 'Mengajukan Pembayaran Periodik',
			'action'		=> 'index',
			'datdept'		=> $datdept,
			'data_detail'	=> $data_detail,
			'data'			=> $data
		);
		
		$this->load->view('Pengajuan_rutin/request',$data);
    }

	public function approval($id) {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$data	= $this->Pengajuan_rutin_model->GetDataPengajuanRutin($id);

		if($Arr_Akses['read'] !='1' AND !$data){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('pengajuan_rutin'));
		}

		$datdept  		= $this->master_model->GetDeptCombo($data->departement);
		$data_detail	= $this->Pengajuan_rutin_model->GetDataPengajuanRutinDetailApproval($data->no_doc);

		$data = array(
			'title'			=> 'Approval Pembayaran Periodik',
			'action'		=> 'index',
			'datdept'		=> $datdept,
			'data_detail'	=> $data_detail,
			'data'			=> $data
		);
		
		$this->load->view('Pengajuan_rutin/create',$data);
    }

	public function hapus_data($id){
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['delete'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('pengajuan_rutin'));
		}
        if($id!=''){
			$this->db->trans_begin();
            $this->Pengajuan_rutin_model->DataDelete('tr_pengajuan_rutin',array('no_doc'=>$id));
			$this->Pengajuan_rutin_model->DataDelete('tr_pengajuan_rutin_detail',array('no_doc'=>$id));
            $result = $this->db->trans_status();
			$this->db->trans_complete();
            $keterangan     = "SUKSES, Delete data  ";
            $status         = 1; 
			history('Delete data pembayaran periodik '.$id);
        } else {
            $result = 0;
            $keterangan     = "GAGAL, Delete data  ";
            $status         = 0; 
        }
		
        $param = array(
                'delete' => $result,
                'idx'=>$id
                );
        echo json_encode($param);
    }

	public function approve($id=''){
		$result=false;
        if($id!="") {
			$data = array(
						array(
							'id'=>$id,
							'status'=>1,
						)
					);
			$result = $this->db->update_batch('tr_pengajuan_rutin',$data,'id');
			history("Approve pengajuan budget periodik ".$id);
        }
        $param = array(
                'save' => $result, 'id'=>$id
                );
        echo json_encode($param);
	}

	//APPROVAL & SUBMISSION PEMBAYARAN PERIODIK
	public function save_request(){
		$Username 	= $this->session->userdata['ORI_User']['username'];
		$dateTime	= date('Y-m-d H:i:s');
		$dateTime2	= date('Ymdhis');
		$data 		= $this->input->post();
        $check		= $data['check'];
        $id			= $data['id'];

		$UpdateBatch = [];
		foreach ($check as $key => $value) {

			//UPLOAD
			$NM_FILE = 'doc_file_'.$value;
			$file_name = '';
			if(!empty($_FILES[$NM_FILE]["name"])){
				$target_dir     = "assets/bayar_rutin/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/bayar_rutin/";
				$name_file      = 'periodik_'.$value.'_'.$dateTime2;
				$target_file    = $target_dir . basename($_FILES[$NM_FILE]["name"]);
				$name_file_ori  = basename($_FILES[$NM_FILE]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
				$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
				$file_name    	= $name_file.".".$imageFileType;

				if(!empty($_FILES[$NM_FILE]["tmp_name"])){
					$terupload = move_uploaded_file($_FILES[$NM_FILE]["tmp_name"], $nama_upload);
				}
			}

			$UpdateBatch[$key]['id'] = $value;
			$UpdateBatch[$key]['nilai'] = str_replace(',','',$data['nilai_'.$value]);
			$UpdateBatch[$key]['keterangan'] = $data['keterangan_'.$value];
			$UpdateBatch[$key]['tgl_bayar'] = $data['tanggalbayar_'.$value];
			$UpdateBatch[$key]['doc_file'] = $file_name;
			$UpdateBatch[$key]['status'] = 'Y';
			$UpdateBatch[$key]['status_by'] = $Username;
			$UpdateBatch[$key]['status_date'] = $dateTime;
		}

		// print_r($UpdateBatch);
		// exit;

		$this->db->trans_start();
			if(!empty($UpdateBatch)){
				$this->db->update_batch('tr_pengajuan_rutin_detail',$UpdateBatch,'id');
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1
			);
			history("Pengajuan pembayaran periodik : ".$id);
		}
		echo json_encode($Arr_Kembali);
    }

	public function save_approval(){
		$Username 		= $this->session->userdata['ORI_User']['username'];
		$dateTime		= date('Y-m-d H:i:s');
        $check			= $this->input->post("check");
        $id				= $this->input->post("id");
        $app_action		= $this->input->post("app_action");
        $app_reason		= strtolower($this->input->post("app_reason"));

		$AvtionN = ($app_action == 'A')?'Approval':'Reject';

		$array_update = [
			'status' 		=> $app_action,
			'status_by' 	=> $Username,
			'status_date' 	=> $dateTime,
			'reason' 		=> $app_reason
		];

		$this->db->trans_start();
			$this->db->where_in('id',$check);
			$this->db->update('tr_pengajuan_rutin_detail',$array_update);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 2,
				'id'		=> $id
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1,
				'id'		=> $id
			);
			history($AvtionN." pembayaran periodik : ".$id);
		}
		echo json_encode($Arr_Kembali);
    }

	//APPROVE
	public function list_approve(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $data       = $this->Pengajuan_rutin_model->GetPengajuanRutinApproval();
        $datdept    = $this->master_model->GetDeptCombo();
		$get_detail = $this->db->get('tr_pengajuan_rutin_detail')->result_array();
		$ArrDetail = [];
		foreach ($get_detail as $key => $value) {
			$ArrDetail[$value['no_doc']][] = $value;
		}
		
		$data = array(
			'title'			=> 'Approval Pengajuan Pembayaran Periodik',
			'action'		=> 'index',
			'tanda'			=> 'approval',
			'results'		=> $data,
			'datdept'		=> $datdept,
			'get_detail'	=> $ArrDetail,
			'listtahun'		=> $this->listtahun
		);
		history('View data approval pengajuan pembayaran periodik');
		$this->load->view('Pengajuan_rutin/index',$data);
	}

	public function print_request(){
		$ID     = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$rest_d		= $this->db->get_where('tr_pengajuan_rutin',array('id'=>$ID))->result();
		$rest_data 	= $this->db->get_where('tr_pengajuan_rutin_detail',array('no_doc'=>$rest_d[0]->no_doc,'status'=>'Y'))->result_array();
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'rest_d' => $rest_d,
			'rest_data' => $rest_data
		);
		
		history('Print request pembayaran periodik '.$ID);
		$this->load->view('Print/print_request_periodik', $data);
	}
}
?>