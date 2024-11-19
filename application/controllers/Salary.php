<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Salary extends CI_Controller{

    public function __construct(){
        parent::__construct();

		$this->load->model('salary_model');
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
		$controller			= 'salary/list_jurnal';
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
		$this->load->view('Salary/form_jurnal',$data);
	}
	public function edit_jurnal($id)
	{
		$controller			= 'salary/list_jurnal';
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
		$this->load->view('Salary/form_jurnal',$data);
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

		$keterangan		= 'Salary';
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
		$controller			= 'salary/list_jurnal';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$jenis_jurnal='SALARY';
		$menu_akses	= $this->master_model->getMenu();
		$results = $this->db->query("SELECT nomor,tanggal,tipe,no_reff,stspos,sum(kredit) as total FROM " . DBERP . ".jurnaltras where jenis_jurnal='".$jenis_jurnal."' group by nomor order by nomor desc")->result();

		$data = array(
			'title'			=> 'Index Of Salary Jurnal',
			'action'		=> 'index',
			'results'		=> $results,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Salary Jurnal');
		$this->load->view('Salary/list_jurnal',$data);
	}
	public function jurnal(){
        $controller			= 'salary';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$bulan=$this->input->post('bulan');
		$tahun=$this->input->post('tahun');
		$bulan=($bulan!=''?$bulan:date("m"));
		$tahun=($tahun!=''?$tahun:date("Y"));
		$result=$this->db->query("select * from amortisasi_generate WHERE bulan='".$bulan."' and tahun='".$tahun."' and kd_asset in (select kd_asset from Salary where status=1)")->result();
		$data = array(
			'title'			=> 'Indeks Of Jurnal Salary',
			'action'		=> 'salary/jurnal',
			'akses_menu'	=> $Arr_Akses,
			'tahun'			=> $tahun,
			'bulan'			=> $bulan,
			'data' 			=> $result,
		);
        history("View Jurnal Salary");
        $this->load->view('Salary/jurnal', $data);
	}
	function jurnal_generate(){
		$bulan=$this->input->post('bulan');
		$tahun=$this->input->post('tahun');
		$nomor=$this->input->post('nomor');
		if(is_array($nomor)){
			$this->db->trans_start();
			$det_Jurnaltes1=array();
			$jenis_jurnal = 'SALARY';
			$nomor_jurnal = $jenis_jurnal . $tahun.$bulan . rand(100, 999);
			$payment_date=date("Y-m-d");
			foreach($nomor as $val){
				$dt_key=explode("#",$val);
				$result=$this->db->query("select a.*,b.coa as cat_coa from Salary a left join salary_category b on a.category=b.id  WHERE kd_asset='".$dt_key[1]."'")->row();
				
				$det_Jurnaltes1[] = array(
					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $result->coa, 'keterangan' => 'Salary ' . $result->nm_asset.','.$tahun.'-'.$bulan, 'no_request' => $result->kd_asset, 'debet' => $result->value , 'kredit' =>0 , 'no_reff' => $result->kd_asset, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => ''
				);
				$det_Jurnaltes1[] = array(
					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $result->cat_coa, 'keterangan' => 'Salary ' . $result->nm_asset.','.$tahun.'-'.$bulan, 'no_request' => $result->kd_asset, 'debet' => 0 , 'kredit' =>$result->value , 'no_reff' => $result->kd_asset, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => ''
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
				history("Jurnal Salary ".$tahun.'-'.$bulan);
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
        $controller			= 'salary';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data = array(
			'title'			=> 'Indeks Of Salary',
			'action'		=> 'salary',
			'akses_menu'	=> $Arr_Akses,
		);
        history("View index Salary");
        $this->load->view('Salary/index', $data);
    }
	public function data_side(){
		$this->salary_model->getDataJSON();
	}
	public function view() {
        $controller			= 'salary';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$id = $this->uri->segment(3);
		$result = $this->db->query("SELECT * FROM salary_data WHERE id='".$id."'")->row();
		$data = array(
			'title'			=> 'View Salary',
			'action'		=> 'salary',
			'status'		=> 'view',
			'akses_menu'	=> $Arr_Akses,
			'data'			=> $result,
			'dataaset' 		=> $this->Acc_model->GetCoaCombo(),
			'list_catg' => $this->salary_model->getList('salary_category')
		);
        history("View Salary");
        $this->load->view('Salary/create', $data);
    }
	public function approve(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
		$this->db->query("update salary_data set status=1 WHERE id='".$id."' and status=0");
			
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
			history('Update Salary Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	public function hapus(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
			$result = $this->db->query("SELECT * FROM salary_data WHERE id='".$id."' and status=0")->row();
            $this->db->where('id', $id);
            $this->db->where('status', '0');
            $this->db->delete('salary_data');
			if(!$result) $this->db->query("delete FROM salary_data_detail WHERE nomor='".$id."'");
			
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
			history('Delete Salary Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	public function add() {
        $controller			= 'salary';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_detail=$this->db->query("select a.*,b.nm_category, 0 as debet, 0 as credit,c.nama from salary_coa a left join salary_category b on a.category=b.id join ".DBACC.".coa_master c on a.coa=c.no_perkiraan order by b.urutan,a.coa ")->result();
		$data = array(
			'title'			=> 'Input Salary',
			'action'		=> 'salary',
			'status'		=> 'add',
			'akses_menu'	=> $Arr_Akses,
			'data_detail' 	=> $data_detail
		);
        history("New Salary");
        $this->load->view('Salary/create', $data);
    }
	public function saved(){

		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$id				= $this->input->post('idamt');
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		$UserName 		= $data_session['ORI_User']['id_user'];
		$nmCategory		= $this->salary_model->getWhere('salary_category', 'id', $data['category']);
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
		$qQuery			= "SELECT max(id) as maxP FROM salary_data ";
		$restQuery		= $this->db->query($qQuery)->result_array();

		// AST-1011908-02-0001
		$category		= $data['category'];

		$KdCategory		= sprintf('%02s',$category);
		$angkaUrut2		= $restQuery[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 17, 3);
		$urutan2++;
		$urut2			= sprintf('%03s',$urutan2);
		$kode_Amortisasis	= "AMT-".$Ym."-".$KdCategory."-".$urut2;
		$detailDataDash	= array();

		$lopp 	= 0;
		$lopp2 	= 0;
		for($no=1; $no <= $data['qty']; $no++){
			$Nomor	= sprintf('%02s',$no);
			$lopp++;
			$detailData[$lopp]['kd_asset'] 		= $kode_Amortisasis.$Nomor;
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
				$detailDataDash[$lopp2]['kd_asset'] 	= $kode_Amortisasis.$Nomor;
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
				'pesan'		=>'Salary gagal disimpan ...',
				'status'	=> 0
			);
		} else {
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Salary berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
		}
		echo json_encode($Arr_Data);
	}
	function category(){
		$controller			= 'salary/category';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Index Of Salary Category',
			'action'		=> 'category_list',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Salary Category List');
		$this->load->view('Salary/category_list',$data);
	}
	function data_side_category(){
		$controller		= 'salary/category';
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
			$nestedData[]	= "<div align='left'>".$row['urutan']."</div>";
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
			FROM salary_category a,
                (SELECT @row:=0) r
            WHERE
                1=1 AND (
                a.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_category',
			2 => 'urutan',
		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." LIMIT ".$limit_start." ,".$limit_length." ";
		$data['query'] = $this->db->query($sql);
		return $data;
    }
	public function add_data_category(){
		$controller			= 'salary/category';
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
                    'urutan' 		=> ($data['urutan']),
					'created_date'	=> date('Y-m-d h:i:s'),
					'created_by'	=> $data_session['ORI_User']['id_user']
                );
                $TandaI = "Insert";
			}
			if(!empty($id)){
                $ArrHeader = array(
                    'nm_category'	=> ($data['nm_category']),
                    'urutan' 		=> ($data['urutan']),
					'updated_date'	=> date('Y-m-d h:i:s'),
					'updated_by'	=> $data_session['ORI_User']['id_user']
                );
                $TandaI = "Update";
            }
            $this->db->trans_start();
                if(empty($id)) $this->db->insert('salary_category', $ArrHeader);
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('salary_category', $ArrHeader);
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
				history($TandaI.' Salary Category '.$id.' / '.$data['nm_category']);
			}
			echo json_encode($Arr_Kembali);
		} else{
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM salary_category WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();
			$data = array(
				'title'		=> 'Data Salary Category',
                'action'	=> 'add',
                'data'      => $result
			);
			$this->load->view('Salary/category_form',$data);
		}
	}
	public function hapus_data_category(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->delete('salary_category');
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
			history('Delete Salary Category Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}

	function coa(){
		$controller			= 'salary/coa';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Index Of Salary COA',
			'action'		=> 'coa_list',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Salary COA List');
		$this->load->view('Salary/coa_list',$data);
	}
	function data_side_coa(){
		$controller		= 'salary/coa';
		$Arr_Akses		= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_coa(
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
			$nestedData[]	= "<div align='left'>".$row['coa']."</div>";
			$nestedData[]	= "<div align='left'>".$row['category']."</div>";
			$nestedData[]	= "<div align='left'>".$row['keterangan']."</div>";
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
	public function get_query_json_coa($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
            SELECT (@row:=@row+1) AS nomor, a.*
			FROM salary_coa a,
                (SELECT @row:=0) r
            WHERE
                1=1 AND (
                a.coa LIKE '%".$this->db->escape_like_str($like_value)."%'
                or a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'coa',
			2 => 'keterangan',
		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." LIMIT ".$limit_start." ,".$limit_length." ";
		$data['query'] = $this->db->query($sql);
		return $data;
    }
	public function add_data_coa(){
		$controller			= 'salary/coa';
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
                    'coa'			=> ($data['coa']),
                    'category' 		=> ($data['category']),
                    'keterangan'	=> ($data['keterangan']),
					'created_date'	=> date('Y-m-d h:i:s'),
					'created_by'	=> $data_session['ORI_User']['id_user']
                );
                $TandaI = "Insert";
			}
			if(!empty($id)){
                $ArrHeader = array(
                    'coa'			=> ($data['coa']),
                    'category' 		=> ($data['category']),
                    'keterangan'	=> ($data['keterangan']),
					'updated_date'	=> date('Y-m-d h:i:s'),
					'updated_by'	=> $data_session['ORI_User']['id_user']
                );
                $TandaI = "Update";
            }
            $this->db->trans_start();
                if(empty($id)) $this->db->insert('salary_coa', $ArrHeader);
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('salary_coa', $ArrHeader);
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
				history($TandaI.' Salary COA '.$id.' / '.$data['coa']);
			}
			echo json_encode($Arr_Kembali);
		} else{
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM salary_coa WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();
			$data = array(
				'title'		=> 'Data Salary COA',
                'action'	=> 'add',
				'datacoa'	=> $this->Acc_model->GetCoaCombo(),
				'datacategory'	=> $this->db->query("select * from salary_category order by nm_category")->result(),
                'data'      => $result
			);
			$this->load->view('Salary/coa_form',$data);
		}
	}
	public function hapus_data_coa(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->delete('salary_coa');
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
			history('Delete Salary COA Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}

}
