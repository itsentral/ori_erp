<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran_periodik extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Pembayaran_periodik_model');
		$this->load->model('Pengajuan_rutin_model');
		$this->load->model('Budget_coa_model');
		$this->load->model('Acc_model');
		$this->load->model('All_model');
		$this->load->model('Jurnal_model');
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

    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
        $data       = $this->Pengajuan_rutin_model->GetPengajuanRutin();
        $datdept    = $this->master_model->GetDeptCombo();
		$get_detail = $this->db->select('*')->from('tr_pengajuan_rutin_detail')->where("status='A' OR status='P'")->get()->result_array();
		$ArrDetail = [];
		foreach ($get_detail as $key => $value) {
			$ArrDetail[$value['no_doc']][] = $value;
		}
		
		$data = array(
			'title'			=> 'Pembayaran Periodik',
			'action'		=> 'index',
			'results'		=> $data,
			'datdept'		=> $datdept,
			'get_detail'	=> $ArrDetail,
			'listtahun'		=> $this->listtahun
		);
		history('View data pembayaran periodik');
		$this->load->view('Pembayaran_periodik/index',$data);
	}

	public function view($id) {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$data	= $this->Pembayaran_periodik_model->GetDataPengajuanRutin($id);

		if($Arr_Akses['read'] !='1' AND !$data){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('pembayaran_periodik'));
		}

		$datacoapayment=$this->master_model->get_coa_payment('bayarrutin',$data->no_doc);
        $datdept  = $this->master_model->GetDeptCombo($data->departement);
		$data_detail=$this->Pembayaran_periodik_model->GetDataPengajuanRutinDetailView($data->no_doc);
        $datcoa	= $this->Budget_coa_model->GetCoa();

		$data = array(
			'title'			=> 'Detail Pembayaran Periodik',
			'action'		=> 'index',
			'datacoapayment'		=> $datacoapayment,
			'type'	=> 'view',
			'datdept'			=> $datdept,
			'datcoa'		=> $datcoa,
			'data'	=> $data,
			'data_detail'			=> $data_detail
		);
		
		$this->load->view('Pembayaran_periodik/create',$data);
    }

	public function edit($id,$tanda=null) {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$data	= $this->Pembayaran_periodik_model->GetDataPengajuanRutin($id);

		if($Arr_Akses['read'] !='1' AND !$data){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('pembayaran_periodik'));
		}

		$datacoapayment=$this->master_model->get_coa_payment('bayarrutin',$data->no_doc);
        $datdept  = $this->master_model->GetDeptCombo($data->departement);
		$data_detail=$this->Pembayaran_periodik_model->GetDataPengajuanRutinDetailPayment($data->no_doc);
		$Judul = 'Payment';
		if(!empty($tanda)){
			$data_detail=$this->Pembayaran_periodik_model->GetDataPengajuanRutinDetailPaymentOnly($data->no_doc);
			$Judul = 'Detail Payment';
		}
        $datcoa	= $this->Budget_coa_model->GetCoa();

		$data = array(
			'title'				=> $Judul.' Pembayaran Periodik',
			'action'			=> 'index',
			'datacoapayment'	=> $datacoapayment,
			'type'				=> 'edit',
			'datdept'			=> $datdept,
			'datcoa'			=> $datcoa,
			'data'				=> $data,
			'tanda'				=> $tanda,
			'data_detail'		=> $data_detail
		);
		
		$this->load->view('Pembayaran_periodik/payment',$data);
    }

	public function get_data() {
		$allbudget		= $this->input->post("allbudget");
        $dept       	= $this->input->post("dept");
        $tanggal           = $this->input->post("tanggal");
		$data=$this->Pembayaran_periodik_model->GetDataBudgetRutin($dept,$tanggal,$allbudget);
		$param = array(
				'save' =>1,
				'data'=>$data,
				);
		echo json_encode($param);
	}

    public function save_data(){
        $departement	= $this->input->post("departement");
        $id				= $this->input->post("id");
		$no_doc			= $this->input->post("no_doc");
		$tanggal_doc	= $this->input->post("tanggal_doc");
        $nilai_total	= str_replace(',','',$this->input->post("nilai_total"));
        $coa_bank		= $this->input->post("coa_bank");
        $coa_ppn		= $this->input->post("coa_ppn");
        $nilai_ppn		= str_replace(',','',$this->input->post("nilai_ppn"));
        $nilai_pph		= str_replace(',','',$this->input->post("nilai_pph"));
        $pph			= str_replace(',','',$this->input->post("pph"));
        $ppn			= str_replace(',','',$this->input->post("ppn"));

		$detail_id		= $this->input->post("detail_id");
		$id_budget		= $this->input->post("id_budget");
        $coa       		= $this->input->post("coa");
        $nama           = $this->input->post("nama");
		$tanggal		= $this->input->post("tanggal");
		$tipe  			= 'rutin';
        $nilai_bayar	= $this->input->post("nilai_bayar");
        $keterangan		= $this->input->post("keterangan");

        $modul			= $this->input->post("modul");
        $detail_id_coa	= $this->input->post("detail_id_coa");
        $detail_coa		= $this->input->post("detail_coa");
        $kredit			= $this->input->post("kredit");
        $debit			= $this->input->post("debit");
        $keterangancoa	= $this->input->post("keterangancoa");

		$Username 	= $this->session->userdata['ORI_User']['username'];
		$dateTime	= date('Y-m-d H:i:s');

// data jurnal
		$kodejurnal	='BUK040';
		$jurnal_coa	=array();
		$det_Jurnaltes=array();
		$tgl_voucher=date("Y-m-d");
		$datajurnal	= $this->Acc_model->GetTemplateJurnal($kodejurnal);
		foreach($datajurnal AS $record){
			$jurnal_coa[$record->field]= $record->no_perkiraan;
		}
		$kodejurnal=$kodejurnal.rand('101','999');
			$this->db->trans_begin();
			$dataheader =  array(
				array(
						'id'=>$id,
						'coa_bank'=>$coa_bank,
						'nilai_total'=>$nilai_total,
						'coa_ppn'=>$coa_ppn,
						'nilai_ppn'=>$nilai_ppn,
						'nilai_pph'=>$nilai_pph,
						'status'=>'2',
					)
				);
			//jurnal bank
			$det_Jurnaltes[] =array(
			  'nomor'         => $kodejurnal.$no_doc,
			  'tanggal'       => $tgl_voucher,
			  'tipe'          => 'BUK',
			  'no_perkiraan'  => $coa_bank,
			  'keterangan'    => 'Pembayaran periodik no. '.$no_doc,
			  'no_reff'       => $no_doc,
			  'debet'         => 0,
			  'kredit'        => $nilai_total,
			  'jenis_jurnal'  => 'pembayaran periodik',
			  'no_request'    => $no_doc
			);
			$this->db->update_batch('tr_pengajuan_rutin',$dataheader,'id');

			$config['upload_path'] = './assets/bayar_rutin/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|pdf';
			$config['remove_spaces'] = TRUE;
			$config['encrypt_name'] = TRUE;
			$ArrID_Detail = [];
			for ($x = 0; $x < count($detail_id); $x++) {
				$NILAI_BAYAR = str_replace(',','',$nilai_bayar[$x]);
				if($NILAI_BAYAR > 0){
					if(!empty($_FILES['doc_file_'.$x]['name'])){
						$_FILES['file']['name'] = $_FILES['doc_file_'.$x]['name'];
						$_FILES['file']['type'] = $_FILES['doc_file_'.$x]['type'];
						$_FILES['file']['tmp_name'] = $_FILES['doc_file_'.$x]['tmp_name'];
						$_FILES['file']['error'] = $_FILES['doc_file_'.$x]['error'];
						$_FILES['file']['size'] = $_FILES['doc_file_'.$x]['size'];
						$this->load->library('upload',$config); 					
						if($this->upload->do_upload('file')){
							$uploadData = $this->upload->data();
							$filename = $uploadData['file_name'];
							$data = array(
									'doc_file'=>$filename,
									'ppn'=>str_replace(',','',$ppn[$x]),
									'pph'=>str_replace(',','',$pph[$x]),
									'nilai_bayar'=>$NILAI_BAYAR,
									'status'=>'P',
									'payment_by'=>$Username,
									'payment_date'=>$dateTime
							);
						}
						else{
							$data = array(
									'ppn'=>str_replace(',','',$ppn[$x]),
									'pph'=>str_replace(',','',$pph[$x]),
									'nilai_bayar'=>$NILAI_BAYAR,
									'status'=>'P',
									'payment_by'=>$Username,
									'payment_date'=>$dateTime
							);
						}
					}
					else {
						$data = array(
								'ppn'=>str_replace(',','',$ppn[$x]),
								'pph'=>str_replace(',','',$pph[$x]),
								'nilai_bayar'=>$NILAI_BAYAR,
								'status'=>'P',
								'payment_by'=>$Username,
								'payment_date'=>$dateTime
						);
					}
					$ArrID_Detail[] = $detail_id[$x];
					$this->Pembayaran_periodik_model->DataUpdate('tr_pengajuan_rutin_detail',$data,array('id'=>$detail_id[$x]));
					//jurnal ppn
					$nilai_ppn_dtl=str_replace(',','',$ppn[$x]);
					if($nilai_ppn_dtl>0){
						$det_Jurnaltes[]= array(
						  'nomor'         => $kodejurnal.$no_doc,
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'BUK',
						  'no_perkiraan'  => $jurnal_coa['ppn'],
						  'keterangan'    => 'Ppn '.$keterangan[$x],
						  'no_reff'       => $no_doc,
						  'debet'         => $nilai_ppn_dtl,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'pembayaran periodik',
						  'no_request'    => $detail_id[$x],
						);
					}
					//jurnal pph
					$nilai_pph_dtl=str_replace(',','',$pph[$x]);
					if($nilai_pph_dtl>0){
						$det_Jurnaltes[] = array(
						  'nomor'         => $kodejurnal.$no_doc,
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'BUK',
						  'no_perkiraan'  => $jurnal_coa['pph'],
						  'keterangan'    => 'Pph '.$keterangan[$x],
						  'no_reff'       => $no_doc,
						  'debet'         => 0,
						  'kredit'        => $nilai_pph_dtl,
						  'jenis_jurnal'  => 'pembayaran periodik',
						  'no_request'    => $detail_id[$x],
						);
					}
					//jurnal biaya
					$det_Jurnaltes[] = array(
					  'nomor'         => $kodejurnal.$no_doc,
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'BUK',
					  'no_perkiraan'  => $coa[$x],
					  'keterangan'    => $keterangan[$x],
					  'no_reff'       => $no_doc,
					  'debet'         => $NILAI_BAYAR,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'pembayaran periodik',
					  'no_request'    => $detail_id[$x],
					);
				}
			}

			for ($x = 0; $x < count($detail_id_coa); $x++) {
				$datadtlcoa = array(
						'modul'=>$modul,
						'no_doc'=>$no_doc,
						'keterangan'=>$keterangancoa[$x],
						'coa'=>$detail_coa[$x],
						'kredit'=>$kredit[$x],
						'debit'=>$debit[$x],
				);
				//jurnal lainnya
				$det_Jurnaltes[] = array(
				  'nomor'         => $kodejurnal.$no_doc,
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'BUK',
				  'no_perkiraan'  => $detail_coa[$x],
				  'keterangan'    => $keterangancoa[$x],
				  'no_reff'       => $no_doc,
				  'debet'         => $debit[$x],
				  'kredit'        => $kredit[$x],
				  'jenis_jurnal'  => 'pembayaran periodik',
				  'no_request'    => $detail_id_coa[$x],
				);
				$this->Pembayaran_periodik_model->DataSave('tr_coa_payment',$datadtlcoa);
			}

			//Jurnal ERP
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes);

			$this->db->trans_complete();
            if($this->db->trans_status()) {
                $keterangan     = "SUKSES, tambah data ";
                $status         = 1;
                $result         = TRUE;
				history("Pembayaran periodik ".$id);
				$this->db->trans_commit();
            } else {
                $keterangan     = "GAGAL, tambah data ";
                $status         = 0;
                $result = FALSE;
				$this->db->trans_rollback();
            }
			$param = array(
					'save' => $result
					);
			echo json_encode($param);
    }

	public function periodik_jurnal_list()
	{
		$controller			= 'pembayaran_periodik/periodik_jurnal_list';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$menu_akses	= $this->master_model->getMenu();
		$results    = $this->db->query("SELECT nomor,tanggal,tipe,no_reff,stspos,sum(kredit) as total FROM " . DBERP . ".jurnaltras where jenis_jurnal='pembayaran periodik' group by nomor order by nomor desc")->result();
	
		$data = array(
			'title'			=> 'Index Of Jurnal',
			'action'		=> 'index',
			'results'		=> $results,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Payment Jurnal');
		$this->load->view('Pembayaran_periodik/list_jurnal',$data);
	}
	public function view_jurnal($id) {
		$controller			= 'pembayaran_periodik/periodik_jurnal_list'; 
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_session			= $this->session->userdata;
		$data = $this->db->query("select * from " . DBERP . ".jurnaltras where nomor='" . $id . "' order by kredit,debet,no_perkiraan")->result();
		$data_coa = $this->All_model->GetCoaCombo();

		$data = array(
			'title'			=> 'Submit Jurnal',
			'action'		=> 'index',
			'data'	    	=> $data,
			'datacoa'	    => $data_coa,
			'status'	    => 'view',
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Pembayaran_periodik/form_jurnal',$data);
	}
	public function edit_jurnal($id)
	{
		$controller			= 'pembayaran_periodik/periodik_jurnal_list'; 
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_session			= $this->session->userdata;
		$data = $this->db->query("select * from " . DBERP . ".jurnaltras where nomor='" . $id . "' order by kredit,debet,no_perkiraan")->result();
		$data_coa = $this->All_model->GetCoaCombo();

		$data = array(
			'title'			=> 'Submit Jurnal',
			'action'		=> 'index',
			'data'	    	=> $data,
			'datacoa'	    => $data_coa,
			'status'	    => 'edit',
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Pembayaran_periodik/form_jurnal',$data);
	}
	public function jurnal_save()
	{
		$id = $this->input->post("id");
		$no_perkiraan = $this->input->post("no_perkiraan");
		$keterangan = $this->input->post("keterangan");
		$debet = $this->input->post("debet");
		$kredit = $this->input->post("kredit");

//		$tanggal		= $this->input->post('tanggal');
		$tipe			= $this->input->post('tipe');
		$no_reff        = $this->input->post('no_reff');
		$no_request		= $this->input->post('no_request');
		$jenis_jurnal	= $this->input->post('jenis_jurnal');
		$nocust         = $this->input->post('nocust');
		$total			= 0;
		$total_po		= $this->input->post('total_po');
		$tanggal		= date("Y-m-d");
		$Bln 			= substr($tanggal, 5, 2);
		$Thn 			= substr($tanggal, 0, 4);
		$Nomor_JV = $this->Jurnal_model->get_no_buk('101');
		$session = $this->session->userdata('app_session');

		$data_session			= $this->session->userdata;
		$dateTime = date('Y-m-d H:i:s');
		$UserName = $data_session['ORI_User']['id_user'];

		$this->db->trans_begin();
		for ($i = 0; $i < count($id); $i++) {
			$dataheader =  array(
				'stspos' => "1",
				'no_perkiraan' => $no_perkiraan[$i],
				'keterangan' => $keterangan[$i],
				'debet' => $debet[$i],
				'kredit' => $kredit[$i]
			);
			$total = ($total + $debet[$i]);
			$this->All_model->DataUpdate(DBERP . '.jurnaltras', $dataheader, array('id' => $id[$i]));

			$datadetail = array(
				'tipe'        	=> $tipe,
				'nomor'       	=> $Nomor_JV,
				'tanggal'     	=> $tanggal,
				'no_reff'     	=> $no_reff,
				'no_perkiraan'	=> $no_perkiraan[$i],
				'keterangan' 	=> $keterangan[$i],
				'debet' 		=> $debet[$i],
				'kredit' 		=> $kredit[$i]
			);
			$this->db->insert(DBACC . '.jurnal', $datadetail);
		}

		$keterangan	= 'Pembayaran periodik ';
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tanggal,
			'jml'	            => $total,
			'kdcab'				=> '101',
			'jenis_reff'	    => 'BUK',
			'no_reff' 		    => $no_reff,
			'jenis_ap'			=> 'V',
			'note'				=> $keterangan,
			'user_id'			=> $UserName,
			'ho_valid'			=> '',
			'batal'			    => '0'
		);
		$this->db->insert(DBACC . '.japh', $dataJVhead);
		$Qry_Update_Cabang_acc	 = "UPDATE " . DBACC . ".pastibisa_tb_cabang SET nobuk=nobuk + 1 WHERE nocab='101'";
		$this->db->query($Qry_Update_Cabang_acc);

		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			$this->db->trans_commit();
			$result         = TRUE;
		} else {
			$this->db->trans_rollback();
			$result = FALSE;
		}
		$param = array(
			'save' => $result
		);
		echo json_encode($param);
	}
	function change_jurnal_date(){
		die();
		$table='japh';
		$fields='jenis_reff';
		$idf='nomor';
		$datajurnal	= $this->db->query("SELECT * FROM " . DBACC . ".".$table." where ".$fields."='BUK' order by ".$idf." desc")->result();

		$this->db->trans_begin();
		foreach($datajurnal AS $record){
			$tgl=explode("-",$record->tgl);
			$this->db->query("update " . DBACC . ".".$table." set tgl='".$tgl[0].'-05-'.$tgl[2]."' where ".$fields."='BUK' and ".$idf."='".$record->nomor."'");			
		}
		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			echo "ok";
			$this->db->trans_commit();
		} else {
			echo "error";
			$this->db->trans_rollback();
		}		
	}
}
?>