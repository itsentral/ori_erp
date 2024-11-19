<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Amortisasi extends CI_Controller{

    public function __construct(){
        parent::__construct();

		$this->load->model('amortisasi_model');
		$this->load->model('master_model');
		$this->load->model('Acc_model');
		$this->load->model('All_model');
		$this->load->model('Jurnal_model');

		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	public function view_jurnal($id)
	{
		$controller			= 'amortisasi/list_jurnal';
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
		$this->load->view('Amortisasi/form_jurnal',$data);
	}
	public function edit_jurnal($id)
	{
		$controller			= 'amortisasi/list_jurnal';
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
		$this->load->view('Amortisasi/form_jurnal',$data);
	}
	public function jurnal_save(){
		$id = $this->input->post("id");
		$no_perkiraan = $this->input->post("no_perkiraan");
		$keterangan = $this->input->post("keterangan");
		$debet = $this->input->post("debet");
		$kredit = $this->input->post("kredit");

		$tanggal		= $this->input->post('tanggal');
		$tipe			= $this->input->post('tipe');
		$no_reff        = $this->input->post('no_reff');
		$no_request		= $this->input->post('no_request');
		$jenis_jurnal	= $this->input->post('jenis_jurnal');
		$nocust         = $this->input->post('nocust');
		$total			= 0;
		$total_po		= $this->input->post('total_po');

		$Bln 			= substr($tanggal,5,2);
		$Thn 			= substr($tanggal,0,4);
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);

        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;

		$this->db->trans_begin();
        for($i=0;$i < count($id);$i++){
			$dataheader =  array(
				'stspos' => "1",
				'no_perkiraan' => $no_perkiraan[$i],
				'keterangan' => $keterangan[$i],
				'debet' => $debet[$i],
				'kredit' => $kredit[$i]
			);
			$total=($total+$debet[$i]);
			$this->All_model->DataUpdate('jurnaltras', $dataheader, array('id' => $id[$i]));
			
			if($debet[$i]==0 && $kredit[$i]==0){
			}else{
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
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
		}

		$keterangan		= 'Amortisasi';
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tanggal,
			'jml'	            => $total,
			'bulan'	            => $Bln,
			'tahun'	            => $Thn,
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan'		=> $keterangan,
			'user_id'			=> $session['username'],
			'ho_valid'			=> '',
		);
		if($tipe=='JV') {
			$this->db->insert(DBACC . '.javh', $dataJVhead);
			$Qry_Update_Cabang_acc = "UPDATE " . DBACC . ".pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
			$this->db->query($Qry_Update_Cabang_acc);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			$this->db->trans_commit();
			$result         = TRUE;
			history('Save Jurnal');
		} else {
			$this->db->trans_rollback();
			$result = FALSE;
		}
		$param = array(
			'save' => $result
		);
		echo json_encode($param);
	}
	public function list_jurnal()
	{
		$controller			= 'amortisasi/list_jurnal';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$jenis_jurnal='AMORTISASI';
		$menu_akses	= $this->master_model->getMenu();
		$results = $this->db->query("SELECT nomor,tanggal,tipe,no_reff,stspos,sum(kredit) as total FROM " . DBERP . ".jurnaltras where jenis_jurnal='".$jenis_jurnal."' group by nomor order by nomor desc")->result();

		$data = array(
			'title'			=> 'Index Of Amortisasi Jurnal',
			'action'		=> 'index',
			'results'		=> $results,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Amortisasi Jurnal');
		$this->load->view('Amortisasi/list_jurnal',$data);
	}
	public function jurnal(){
        $controller			= 'amortisasi';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$bulan=$this->input->post('bulan');
		$tahun=$this->input->post('tahun');
		$bulan=($bulan!=''?$bulan:date("m"));
		$tahun=($tahun!=''?$tahun:date("Y"));
		$result=$this->db->query("select * from amortisasi_generate WHERE bulan='".$bulan."' and tahun='".$tahun."' and kd_asset in (select kd_asset from amortisasi where status=1)")->result();
		$data = array(
			'title'			=> 'Indeks Of Jurnal Amortisasi',
			'action'		=> 'amortisasi/jurnal',
			'akses_menu'	=> $Arr_Akses,
			'tahun'			=> $tahun,
			'bulan'			=> $bulan,
			'data' 			=> $result,
		);
        history("View Jurnal amortisasi");
        $this->load->view('Amortisasi/jurnal', $data);
	}
	function jurnal_generate(){
		$bulan=$this->input->post('bulan');
		$tahun=$this->input->post('tahun');
		$nomor=$this->input->post('nomor');
		if(is_array($nomor)){
			$this->db->trans_start();
			$det_Jurnaltes1=array();
			$jenis_jurnal = 'AMORTISASI';
			$nomor_jurnal = $jenis_jurnal . $tahun.$bulan . rand(100, 999);
			$payment_date=date("Y-m-d");
			foreach($nomor as $val){
				$dt_key=explode("#",$val);
				$result=$this->db->query("select a.*,b.coa as cat_coa from amortisasi a left join amortisasi_category b on a.category=b.id  WHERE kd_asset='".$dt_key[1]."'")->row();
				
				$det_Jurnaltes1[] = array(
					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $result->coa, 'keterangan' => 'Amortisasi ' . $result->nm_asset.','.$tahun.'-'.$bulan, 'no_request' => $result->kd_asset, 'debet' => $result->value , 'kredit' =>0 , 'no_reff' => $result->kd_asset, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => ''
				);
				$det_Jurnaltes1[] = array(
					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $result->cat_coa, 'keterangan' => 'Amortisasi ' . $result->nm_asset.','.$tahun.'-'.$bulan, 'no_request' => $result->kd_asset, 'debet' => 0 , 'kredit' =>$result->value , 'no_reff' => $result->kd_asset, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => ''
				);
				$this->db->query("update amortisasi_generate set flag='Y' WHERE kd_asset='".$dt_key[1]."' and nomor='".$dt_key[0]."'");
			}
			$this->db->insert_batch('jurnaltras', $det_Jurnaltes1);
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);
				history("Jurnal Amortisasi ".$tahun.'-'.$bulan);
			}
		}else{
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		echo json_encode($Arr_Data);
	}
    public function index(){
        $controller			= 'amortisasi';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data = array(
			'title'			=> 'Indeks Of Amortisasi',
			'action'		=> 'amortisasi',
			'akses_menu'	=> $Arr_Akses,
			'kategori' 		=> $this->amortisasi_model->getList('amortisasi_category'),
		);
        history("View index amortisasi");
        $this->load->view('Amortisasi/index', $data);
    }
	public function data_side(){
		$this->amortisasi_model->getDataJSON();
	}
	public function view() {
        $controller			= 'amortisasi';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$id = $this->uri->segment(3);
		$result = $this->db->query("SELECT * FROM amortisasi WHERE id='".$id."'")->row();
		$data = array(
			'title'			=> 'View Amortisasi',
			'action'		=> 'amortisasi',
			'status'		=> 'view',
			'akses_menu'	=> $Arr_Akses,
			'data'			=> $result,
			'dataaset' 		=> $this->Acc_model->GetCoaCombo(),
			'list_catg' => $this->amortisasi_model->getList('amortisasi_category')
		);
        history("View amortisasi");
        $this->load->view('Amortisasi/create', $data);
    }
	
	public function edit() {
        $controller			= 'amortisasi';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$id = $this->uri->segment(3);
		$result = $this->db->query("SELECT * FROM amortisasi WHERE id='".$id."'")->row();
		$data = array(
			'title'			=> 'Edit Amortisasi',
			'action'		=> 'amortisasi',
			'status'		=> 'edit',
			'akses_menu'	=> $Arr_Akses,
			'data'			=> $result,
			'dataaset' 		=> $this->Acc_model->GetCoaCombo(),
			'list_catg' => $this->amortisasi_model->getList('amortisasi_category')
		);
        history("Edit amortisasi");
        $this->load->view('Amortisasi/edit', $data);
    }
	public function approve(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
		$this->db->query("update amortisasi set status=1 WHERE id='".$id."' and status=0");
			
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1
			);
			history('Update Amortisasi Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	public function hapus(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
			$result = $this->db->query("SELECT * FROM amortisasi WHERE id='".$id."' and status=0")->row();
            $this->db->where('id', $id);
            $this->db->where('status', '0');
            $this->db->delete('amortisasi');
			if(!$result) $this->db->query("delete FROM amortisasi_generate WHERE kd_asset='".$result->kd_asset."'");
			
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Amortisasi Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	public function add() {
        $controller			= 'amortisasi';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data = array(
			'title'			=> 'Input Amortisasi',
			'action'		=> 'amortisasi',
			'status'		=> 'add',
			'akses_menu'	=> $Arr_Akses,
			'dataaset' 		=> $this->Acc_model->GetCoaCombo(),
			'list_catg' 	=> $this->amortisasi_model->getList('amortisasi_category')
		);
        history("New amortisasi");
        $this->load->view('Amortisasi/create', $data);
    }
	public function saved(){

		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$id				= $this->input->post('idamt');
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		$UserName 		= $data_session['ORI_User']['id_user'];
		$nmCategory		= $this->amortisasi_model->getWhere('amortisasi_category', 'id', $data['category']);
		$category		= $data['category'];
		$KdCategory		= sprintf('%02s',$category);
		$Ym				= date('Ym');
		$tgl_oleh		= date('Y-m-d');

		if(!empty($data['tanggal'])){
			$Year			= substr($data['tanggal'], 0,4);
			$Month			= substr($data['tanggal'], 5,2);
			$Ym				= $Year.$Month;
			$tgl_oleh		= $data['tanggal'];
		}
		$kode_Amortisasis	= $this->All_model->GetAutoGenerate('format_amortisasi');
		$detailDataDash	= array();

		$lopp 	= 0;
		$lopp2 	= 0;
		for($no=1; $no <= $data['qty']; $no++){
			$Nomor	= sprintf('%02s',$no);
			$lopp++;
			$detailData[$lopp]['kd_asset'] 		= $kode_Amortisasis;
			$detailData[$lopp]['nm_asset'] 		= $data['nm_asset'];
			$detailData[$lopp]['tgl_perolehan'] = $tgl_oleh;
			$detailData[$lopp]['category'] 		= $data['category'];
			$detailData[$lopp]['nm_category'] 	= strtoupper($nmCategory[0]['nm_category']);
			$detailData[$lopp]['nilai_asset'] 	= $data['nilai_asset'];
			$detailData[$lopp]['qty'] 			= $data['qty'];
			$detailData[$lopp]['status'] 		= 0;
			$detailData[$lopp]['depresiasi'] 	= $data['depresiasi'];
			$detailData[$lopp]['value'] 		= $data['value'];
			$detailData[$lopp]['kdcab'] 		= "";
			$detailData[$lopp]['lokasi_asset'] 	= "";
			$detailData[$lopp]['created_by'] 	= $data_session['ORI_User']['id_user'];
			$detailData[$lopp]['created_date'] 	= date('Y-m-d h:i:s');
			$detailData[$lopp]['coa'] 	        = $data['coa'];

			$jmlx   	= $data['depresiasi'];
			$date_now 	= date('Y-m-d');
			if(!empty($data['tanggal'])){
				$date_now 	= $data['tanggal'];
			}

			for($x=1; $x <= $jmlx; $x++){
				$lopp2 += $x;
				$Tanggal 	= date('Y-m', mktime(0,0,0,substr($date_now,5,2)+ $x,0,substr($date_now,0,4)));
				$detailDataDash[$lopp2]['kd_asset'] 	= $kode_Amortisasis;
				$detailDataDash[$lopp2]['nm_asset'] 	= $data['nm_asset'];
				$detailDataDash[$lopp2]['category'] 	= $data['category'];
				$detailDataDash[$lopp2]['nm_category'] 	= strtoupper($nmCategory[0]['nm_category']);
				$detailDataDash[$lopp2]['bulan'] 		= substr($Tanggal, 5,2);
				$detailDataDash[$lopp2]['tahun'] 		= substr($Tanggal, 0,4);
				$detailDataDash[$lopp2]['nilai_susut'] 	= $data['value'];
				$detailDataDash[$lopp2]['kdcab'] 		= "";
			}
		}
		$this->db->trans_start();
			$this->db->insert_batch('amortisasi', $detailData);
			$this->db->insert_batch('amortisasi_generate', $detailDataDash);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Amortisasi gagal disimpan ...',
				'status'	=> 0
			);
		} else {
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Amortisasi berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
		}
		echo json_encode($Arr_Data);
	}
	
	public function edited(){

		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$id				= $this->input->post('idamt');
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		$UserName 		= $data_session['ORI_User']['id_user'];
		$nmCategory		= $this->amortisasi_model->getWhere('amortisasi_category', 'id', $data['category']);
		$category		= $data['category'];
		$coa		    = $data['coa'];
		$KdCategory		= sprintf('%02s',$category);
		$Ym				= date('Ym');
		$tgl_oleh		= date('Y-m-d');
		$nm = strtoupper($nmCategory[0]['nm_category']);
		
		$this->db->trans_start();
		$this->db->query("update amortisasi set category='$category',nm_category='$nm',coa='$coa' WHERE id='".$id."'");
			
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1
			);
			history('Update Amortisasi Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	function cektanggal() {
		$tanggal = $this->input->post("tanggal");
		$bulan = $this->input->post("bulan");
		$enddate=date("Y-m-d",strtotime("+".($bulan-1)." months", strtotime($tanggal)));
		echo $enddate;die();
	}
	function category_list(){
		$controller			= 'amortisasi/category_list';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Index Of Amortisasi Category',
			'action'		=> 'category_list',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Amortisasi Category List');
		$this->load->view('Amortisasi/category_list',$data);
	}
	function data_side_category(){
		$controller		= 'amortisasi/category_list';
		$Arr_Akses		= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_category(
			$requestData['search']['value'], $requestData['order'][0]['column'], $requestData['order'][0]['dir'], $requestData['start'], $requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		foreach($query->result_array() as $row) {
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc') $nomor = $urut1 + $start_dari;
            if($asc_desc == 'desc') $nomor = ($total_data - $start_dari) - $urut2;
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_category']."</div>";
			$nestedData[]	= "<div align='left'>".$row['coa']."</div>";
			$detail		= "";
			$edit		= "";
			$delete		= "";
			if($Arr_Akses['delete']=='1'){
				$delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id']."'><i class='fa fa-trash'></i></button>";
			}
			if($Arr_Akses['update']=='1'){
				$edit	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['id']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button>";
			}
			$nestedData[]	= "<div align='left'> ".$edit." ".$delete." </div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}
		$json_data = array(
			"draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ), "recordsFiltered" => intval( $totalFiltered ), "data" => $data
		);
		echo json_encode($json_data);
	}
	public function get_query_json_category($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
            SELECT (@row:=@row+1) AS nomor, a.*
			FROM amortisasi_category a,
                (SELECT @row:=0) r
            WHERE
                1=1 AND (
                a.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.coa LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_category',
			2 => 'coa',
		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." LIMIT ".$limit_start." ,".$limit_length." ";
		$data['query'] = $this->db->query($sql);
		return $data;
    }
	public function add_data_category(){
		$controller			= 'amortisasi/category_list';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('users'));
		}
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			$id 		    = $data['id'];
			if(empty($id)){
                $ArrHeader = array(
                    'nm_category'	=> ($data['nm_category']),
                    'coa' 		=> ($data['coa']),
                    'tipe'		=> 'TIDAK BERGERAK',                   
                );
                $TandaI = "Insert";
			}
			if(!empty($id)){
                $ArrHeader = array(
                    'nm_category'	=> ($data['nm_category']),
                    'coa' 		=> ($data['coa']),
                );
                $TandaI = "Update";
            }
            $this->db->trans_start();
                if(empty($id)) $this->db->insert('amortisasi_category', $ArrHeader);
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('amortisasi_category', $ArrHeader);
                }
            $this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 0
				);
			} else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success.',
					'status'	=> 1
				);
				history($TandaI.' Amortisasi Category '.$id.' / '.$data['nm_category']);
			}
			echo json_encode($Arr_Kembali);
		} else{
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM amortisasi_category WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();
			$data = array(
				'title'		=> 'Data Amortisasi Category',
                'action'	=> 'add',
				'dataaset'	=> $this->Acc_model->GetCoaCombo(),
                'data'      => $result
			);
			$this->load->view('Amortisasi/category_form',$data);
		}
	}
	public function hapus_data_category(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->delete('amortisasi_category');
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Amortisasi Category Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
}
