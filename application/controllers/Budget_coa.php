<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budget_coa extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('All_model');
		$this->load->model('Budget_coa_model');
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

        $tahun=$this->input->get("tahun");
		if($tahun=='') $tahun=date("Y");

		$data = $this->Budget_coa_model->GetBudget($tahun);

		$data = array(
			'title'			=> 'Master Budget',
			'action'		=> 'index',
			'results'		=> $data,
			'tahun'		    => $tahun,
			'listtahun'		=> $this->listtahun
		);
		history('View data master budget');
		$this->load->view('Budget_coa/index',$data);
	}

    public function detail($tahun) {
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $query="( b.no_perkiraan like '4%' or b.no_perkiraan like '5%' or b.no_perkiraan like '6%' or b.no_perkiraan like '7%' or b.no_perkiraan like '8%' )";
        $data	= $this->Budget_coa_model->GetBudget($tahun,'all',$query);
        if(!$data) {
            $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">Invalid Budget</div>");
			redirect('budget_coa');
        }

		$data = $this->Budget_coa_model->GetBudget($tahun);

		$data = array(
			'title'		=> 'Detail Budget',
			'action'	=> 'index',
			'type'		=> 'edit',
			'data'		   => $data
		);
		history('View detail data master budget');
		$this->load->view('Budget_coa/budget_detail_form',$data);
	}

    public function detail_bulan() {
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $bulan=$this->input->post("bulan");
		$tahun=$this->input->post("tahun");
		$query="( b.no_perkiraan like '4%' or b.no_perkiraan like '5%' or b.no_perkiraan like '6%' or b.no_perkiraan like '7%' or b.no_perkiraan like '8%' )";
        $data	= $this->Budget_coa_model->GetBudget($tahun,'all',$query);
        if(!$data) {
            $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">Invalid Budget</div>");
			redirect('budget_coa');
        }

		$data = array(
			'title'		=> 'Detail Budget Bulanan',
			'action'	=> 'index',
			'type'		=> 'edit',
			'data'		=> $data,
			'bulan'		=> $bulan,
			'tahun'		=> $tahun
		);
		history('View detail bulan master budget');
		$this->load->view('Budget_coa/budget_detail_bulan_form',$data);
	}

    public function edit($tahun) {
/*
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
*/
        $query="( b.no_perkiraan like '4%' or b.no_perkiraan like '5%' or b.no_perkiraan like '6%' or b.no_perkiraan like '7%' or b.no_perkiraan like '8%' )";
        $data	= $this->Budget_coa_model->GetBudget($tahun,'all',$query);
        if(!$data) {
            $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">Invalid Budget</div>");
			redirect('budget_coa');
        }

		$datadept	    = $this->master_model->GetDeptCombo();
		$datacategory	= $this->master_model->GetCategory();

		$data = array(
			'title'		    => 'Edit Budget',
			'action'	    => 'index',
			'type'		    => 'edit',
			'datakategori'  => $datacategory,
			'datadept'		=> $datadept,
			'data'		    => $data
		);

		$this->load->view('Budget_coa/budget_form',$data);
	}

    public function create() {
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $query="( a.no_perkiraan like '4%' or a.no_perkiraan like '5%' or a.no_perkiraan like '6%' or a.no_perkiraan like '7%' or a.no_perkiraan like '8%' )";
        $datcoa	= $this->Budget_coa_model->GetCoa('5',$query);

		$datadept	    = $this->master_model->GetDeptCombo();
		$datacategory	= $this->master_model->GetCategory();

		$data = array(
			'title'		    => 'Edit Budget',
			'action'	    => 'index',
			'type'		    => 'add',
			'datakategori'  => $datacategory,
			'datadept'		=> $datadept,
			'data'		    => $datcoa
		);

		$this->load->view('Budget_coa/budget_form',$data);
	}

    public function save_data(){
        $id		        = $this->input->post("id");
        $type           = $this->input->post("type");
		$tahun  		= $this->input->post("tahun");
        $coa       		= $this->input->post("coa");
        $total			= $this->input->post("total");
        $info      		= $this->input->post("info");
        $divisi			= $this->input->post("divisi");
        $definisi		= $this->input->post("definisi");

        $kategori		= $this->input->post("kategori");
        $finance_bulan	= $this->input->post("finance_bulan");
        $finance_tahun	= $this->input->post("finance_tahun");

		$this->db->trans_start();
        if($type=="edit") {
			for ($x = 0; $x < count($coa); $x++) {
				if($finance_tahun[$x]=='') $finance_tahun[$x]=0;
				if($finance_bulan[$x]=='') $finance_bulan[$x]=0;

				if($id[$x]!='') {
					$data = array(
							array(
								'id'=>$id[$x],
								'tahun'=>$tahun,
								'coa'=>$coa[$x],
								'info'=>$info[$x],
								'divisi'=>$divisi[$x],
								'definisi'=>$definisi[$x],
								'kategori'=>$kategori[$x],
								'finance_bulan'=>$finance_bulan[$x],
								'finance_tahun'=>str_replace(',','',$finance_tahun[$x]),

							)
						);
					$this->Budget_coa_model->updateBatch($data,'id');
				}else{
					$data =  array(
								'tahun'=>$tahun,
								'coa'=>$coa[$x],
								'info'=>$info[$x],
								'divisi'=>$divisi[$x],
								'definisi'=>$definisi[$x],
								'kategori'=>$kategori[$x],
								'finance_bulan'=>$finance_bulan[$x],
								'finance_tahun'=>str_replace(',','',$finance_tahun[$x]),

							);
					$this->Budget_coa_model->insertData($data);
				}

			}
			$keterangan     = "SUKSES, Edit data ";
            history('Update budget NewData - '.$tahun);

			$result			= TRUE;
        } else {
			for ($x = 0; $x < count($coa); $x++) {
				if($finance_tahun[$x]=='') $finance_tahun[$x]=0;
				if($finance_bulan[$x]=='') $finance_bulan[$x]=0;

				$data =  array(
							'tahun'=>$tahun,
							'coa'=>$coa[$x],
							'info'=>$info[$x],
							'divisi'=>$divisi[$x],
							'definisi'=>$definisi[$x],
							'kategori'=>$kategori[$x],
							'finance_bulan'=>$finance_bulan[$x],
							'finance_tahun'=>str_replace(',','',$finance_tahun[$x]),

						);
				$this->Budget_coa_model->insertData($data);

			}
            if($this->db->trans_status()) {
                $keterangan     = "SUKSES, tambah data ".$tahun;
                $status         = 1;
                $kode_universal = 'NewData';
                $jumlah         = $x;
                $result         = TRUE;
            } else {
                $keterangan     = "GAGAL, tambah data Budget ".$tahun;
                $status         = 0;
                $kode_universal = 'NewData';
                $jumlah         = $x;
                $result = FALSE;
            }
            history('Update budget '.$kode_universal.'-'.$keterangan.'-'.$jumlah);
        }
		$this->db->trans_complete();
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
    }

    function hapus_data($tahun){

        if($tahun!=''){
            $result = $this->master_model->dataDelete('ms_budget',array('tahun'=>$tahun));
            history("SUKSES, Delete data Budget ".$tahun);
			$result=1;
        } else {
            history("GAGAL, Delete data Budget ".$tahun);
            $result = 0;
        }
        $param = array(
                'delete' => $result,
                'idx'=>$tahun
                );
        echo json_encode($param);
    }

	//BUDGET NON RUTIN
	public function non_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $tahun=$this->input->get("tahun");
		if($tahun=='') $tahun=date("Y");

		$data = $this->Budget_coa_model->GetListBudgetDept('NON RUTIN');
		$datadept	    = $this->master_model->GetDeptCombo();
		$datacategory	= $this->master_model->GetCategory();

		$data = array(
			'title'			=> 'Budget Departemen',
			'action'		=> 'index',
			'tipek'			=> 'NON RUTIN',
			'results'		=> $data,
			'tahun'		    => $tahun,
			'datakategori'  => $datacategory,
			'datadept'		=> $datadept,
			'listtahun'		=> $this->listtahun
		);
		history('View data non rutin budget');
		$this->load->view('Budget_coa/non_rutin',$data);
	}

	public function create_budget_category(){
/*
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
*/
        $kategori		= $this->input->post("kategori");
        $tahun			= $this->input->post("tahun");
		$divisi			= $this->input->post("divisi");
		$dataset		= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
        $datcoa			= $this->Budget_coa_model->GetBudgetCategory($dataset);
		$datacategory	= $this->master_model->GetCategory();
		$datadept	    = $this->master_model->GetDeptCombo();

		if(empty($datcoa)){
			echo 'Data tidak ditemukan <a href="'.base_url('budget_coa/non_rutin').'">Kembali</a>';die();
			////$this->template->render('list_nr');
		}else{
			$data = array(
				'title'			=> 'Input Budget Detail',
				'action'		=> 'create_budget_category',
				'dataset'		=> $dataset,
				'data'		    => $datcoa,
				'datakategori'  => $datacategory,
				'datadept'		=> $datadept,
				'listtahun'		=> $this->listtahun
			);
			$this->load->view('Budget_coa/budget_form_category',$data);
		}
	}

	function save_data_category(){
		$data_session	= $this->session->userdata;
        $id		        = $this->input->post("id");
        $type           = $this->input->post("type");
		$tahun  		= $this->input->post("tahun");
        $coa       		= $this->input->post("coa");
        $total			= $this->input->post("total");
        $info      		= $this->input->post("info");
        $divisi			= $this->input->post("divisi");
		for($i=1;$i<=12;$i++){
			${"bulan_".$i} = str_replace(',','',$this->input->post('bulan_'.$i));
		}
		$this->db->trans_start();
        if($type=="edit") {
			for ($x = 0; $x < count($coa); $x++) {
//			  if($total[$x]>0){
				if($id[$x]!='') {
					$data =  array(
						'bulan_1'=>$bulan_1[$x], 'bulan_2'=>$bulan_2[$x], 'bulan_3'=>$bulan_3[$x],'bulan_4'=>$bulan_4[$x],
						'bulan_5'=>$bulan_5[$x],'bulan_6'=>$bulan_6[$x], 'bulan_7'=>$bulan_7[$x], 'bulan_8'=>$bulan_8[$x],
						'bulan_9'=>$bulan_9[$x], 'bulan_10'=>$bulan_10[$x],'bulan_11'=>$bulan_11[$x],
						'bulan_12'=>$bulan_12[$x], 
						'total'=>($bulan_1[$x]+$bulan_2[$x]+$bulan_3[$x]+$bulan_4[$x]+$bulan_5[$x]+$bulan_6[$x]+$bulan_7[$x]+$bulan_8[$x]+
						$bulan_9[$x]+$bulan_10[$x]+$bulan_11[$x]+$bulan_12[$x]),
						'status'=>'2',
						'created_by_dept'=>$data_session['ORI_User']['username'],
						'created_on_dept'=>date('Y-m-d H:i:s')
					);
					$this->master_model->dataUpdate('ms_budget',$data,array('id'=>$id[$x]));
				}
//			  }
			}
			history('Add new budget non rutin '.$tahun.' '.$divisi);
			$result			= TRUE;
        }
		$this->db->trans_complete();
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
    }

	function revisi_data_category(){
		$data_session	= $this->session->userdata;
        $kategori		= $this->input->post("kategori");
        $tahun			= $this->input->post("tahun");
		$divisi			= $this->input->post("divisi");
		$revisi			= $this->input->post("revisi");
		$dataset		= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
		$result 		= $this->master_model->dataUpdate('ms_budget',array('status'=>'2','revisi'=>($revisi+1), 'revision_by'=>$data_session['ORI_User']['username'],'revision_on'=>date('Y-m-d H:i:s')),$dataset);
		history('Revisi data category '.$tahun.'/'.$divisi);
		$result=1;
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
	}

	function approve_data_category(){
		$data_session	= $this->session->userdata;
        $kategori		= $this->input->post("kategori");
        $tahun			= $this->input->post("tahun");
		$divisi			= $this->input->post("divisi");
		$dataset		= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
		$result 		= $this->master_model->dataUpdate('ms_budget',array('status'=>'3','created_by_dept'=>$data_session['ORI_User']['username'],'created_on_dept'=>date('Y-m-d H:i:s')),$dataset);
		history('Approve data category '.$tahun.'/'.$divisi);
		$result=1;
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
	}

	//BUDGET UMUM
	public function budget_umum(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $tahun=$this->input->get("tahun");
		if($tahun=='') $tahun=date("Y");

		$data = $this->Budget_coa_model->GetListBudgetDept('UMUM');
		$datadept	    = $this->master_model->GetDeptCombo();
		$datacategory	= $this->master_model->GetCategory();
		$datacoa		= $this->master_model->GetCoaCombo();

		$data = array(
			'title'			=> 'Budget Umum',
			'action'		=> 'index',
			'tipek'			=> 'UMUM',
			'results'		=> $data,
			'tahun'		    => $tahun,
			'datacoa'		=> $datacoa,
			'datakategori'  => $datacategory,
			'datadept'		=> $datadept,
			'listtahun'		=> $this->listtahun
		);
		history('View data budget umum');
		$this->load->view('Budget_coa/budget_umum',$data);
	}

	public function create_budget_umum(){
/*
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
*/
        $kategori		= $this->input->post("kategori");
        $tahun			= $this->input->post("tahun");
		$divisi			= $this->input->post("divisi");
		$dataset		= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
        $datcoa			= $this->Budget_coa_model->GetBudgetCategory($dataset);
		$datacategory	= $this->master_model->GetCategory();
		$datadept	    = $this->master_model->GetDeptCombo();
		$datajenis		= $this->master_model->GetJenis();
		$datacoa		= $this->master_model->GetCoaCombo();

		if(empty($datcoa)){
			echo 'Data tidak ditemukan <a href="'.base_url('budget_coa/budget_umum').'">Kembali</a>';die();
			////$this->template->render('list_nr');
		}else{
			$data = array(
				'title'			=> 'Input Budget Detail',
				'action'		=> 'create_budget_category',
				'dataset'		=> $dataset,
				'data'		    => $datcoa,
				'datacoa'		=> $datacoa,
				'datakategori'  => $datacategory,
				'datajenis'  	=> $datajenis,
				'datadept'		=> $datadept,
				'listtahun'		=> $this->listtahun
			);
			$this->load->view('Budget_coa/budget_form_umum',$data);
		}
	}

	function save_data_umum(){
		$data_session	= $this->session->userdata;
        $id		        = $this->input->post("id");
        $type       	= $this->input->post("type");
        $jenis       	= $this->input->post("jenis");
        $nilai			= $this->input->post("nilai");
        $variabel_coa	= $this->input->post("variabel_coa");
		// print_r($variabel_coa);
		// exit;
		for($i=1;$i<=12;$i++){
			${"bulan_".$i} = str_replace(',','',$this->input->post('bulan_'.$i));
		}
		$this->db->trans_start();
        if($type=="edit") {
			for ($x = 0; $x < count($id); $x++) {
			  if($jenis[$x]=='FIX COST BULANAN') {
				$data =  array(
					'jenis'=>$jenis[$x], 'nilai'=>0, 'variabel_coa'=>'','status'=>'2',
					'bulan_1'=>$bulan_1[$x], 'bulan_2'=>$bulan_2[$x], 'bulan_3'=>$bulan_3[$x],'bulan_4'=>$bulan_4[$x],
					'bulan_5'=>$bulan_5[$x],'bulan_6'=>$bulan_6[$x], 'bulan_7'=>$bulan_7[$x], 'bulan_8'=>$bulan_8[$x],
					'bulan_9'=>$bulan_9[$x], 'bulan_10'=>$bulan_10[$x], 'bulan_11'=>$bulan_11[$x], 'bulan_12'=>$bulan_12[$x],
					'total'=>($bulan_1[$x]+$bulan_2[$x]+$bulan_3[$x]+$bulan_4[$x]+$bulan_5[$x]+$bulan_6[$x]+$bulan_7[$x]+$bulan_8[$x]+
					$bulan_9[$x]+$bulan_10[$x]+$bulan_11[$x]+$bulan_12[$x]),
					'created_by_dept'=>$data_session['ORI_User']['username'],
					'created_on_dept'=>date('Y-m-d H:i:s')
				);
			  }else{
				$data =  array(
					'jenis'=>$jenis[$x],
					'nilai'=>str_replace(',','',$nilai[$x]),
					'variabel_coa'=>(!empty($variabel_coa[$x]))?$variabel_coa[$x]:0,
					'status'=>'2',
					'created_by_dept'=>$data_session['ORI_User']['username'],
					'created_on_dept'=>date('Y-m-d H:i:s')
				);
			  }
			  $this->master_model->dataUpdate('ms_budget',$data,array('id'=>$id[$x]));
			}
			history("Save data budget umum ".$type);
			$result	= TRUE;
        }
		$this->db->trans_complete();
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
    }

	function approve_data_umum(){
		$data_session	= $this->session->userdata;
        $kategori	= $this->input->post("kategori");
        $tahun		= $this->input->post("tahun");
		$divisi		= $this->input->post("divisi");
		$dataset	= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
		$result = $this->master_model->dataUpdate('ms_budget',array('status'=>'3','created_by_dept'=>$data_session['ORI_User']['username'],'created_on_dept'=>date('Y-m-d H:i:s')),$dataset);
		history("Approve data budget umum ".$tahun);
		$result=1;
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
	}

	function revisi_data_umum(){
		$data_session	= $this->session->userdata;
        $kategori	= $this->input->post("kategori");
        $tahun		= $this->input->post("tahun");
		$divisi		= $this->input->post("divisi");
		$revisi		= $this->input->post("revisi");
		$dataset	= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
		$result = $this->master_model->dataUpdate('ms_budget',array('status'=>'2','revisi'=>($revisi+1), 'revision_by'=>$data_session['ORI_User']['username'],'revision_on'=>date('Y-m-d H:i:s')),$dataset);
		history("Revisi data budget umum ".$tahun);
		$result=1;
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
	}

	function proses_budget_umum(){
		$query = $this->db->query("SELECT * from ms_budget WHERE kategori='UMUM' and tahun='".date("Y")."' order by jenis,coa")->result();
		if($query){
			foreach ($query as $records) {
				$nilai_tahun=0;
				$id=$records->id;
				$jenis=$records->jenis;
				$nilai=$records->nilai;
				$variabel_coa=$records->variabel_coa;
				$totalbulan=0;
				for($bln=1;$bln<=12;$bln++){
					${"nilai_bulan".$bln}=0;
				}
				if($jenis=='FIX COST BULANAN'){
					$totalbulan=0;
					for($bln=1;$bln<=12;$bln++){
						${"nilai_bulan".$bln}=$records->{"bulan_".$bln};
						$totalbulan=($totalbulan+$records->{"bulan_".$bln});
					}
					$nilai_tahun=$totalbulan;
				}
				if($jenis=='FIX COST TAHUNAN'){
					$nilai_tahun=($nilai);
					for($bln=1;$bln<=12;$bln++){
						${"nilai_bulan".$bln}=round($nilai/12);
					}
				}
				if($jenis=='VARIABLE'){
					$nilai_bulan_coa=0;
					$nilai_tahun_coa=0;
					$totalbulan=0;
					$querycoa = $this->db->query("SELECT * from ms_budget WHERE kategori='UMUM' and tahun='".date("Y")."' and coa='".$variabel_coa."' limit 1")->row();
					if($querycoa->jenis=='FIX COST BULANAN'){
						$totalbulan=0;
						for($bln=1;$bln<=12;$bln++){
							${"nilai_bulan".$bln}=($querycoa->{"bulan_".$bln}*$nilai/100);
							$totalbulan=($totalbulan+($querycoa->{"bulan_".$bln}*$nilai/100));
						}
						$nilai_tahun=$totalbulan;
					}
					if($querycoa->jenis=='FIX COST TAHUNAN'){
						for($bln=1;$bln<=12;$bln++){
							${"nilai_bulan".$bln}=round(($querycoa->nilai/12)*$nilai/100);
						}
						$nilai_tahun_coa=($querycoa->nilai);
						$nilai_tahun=round($nilai_tahun_coa*$nilai/100);
					}
				}
				$data =  array(
					'bulan_1'=>$nilai_bulan1, 'bulan_2'=>$nilai_bulan2, 'bulan_3'=>$nilai_bulan3,'bulan_4'=>$nilai_bulan4,
					'bulan_5'=>$nilai_bulan5,'bulan_6'=>$nilai_bulan6, 'bulan_7'=>$nilai_bulan7, 'bulan_8'=>$nilai_bulan8,
					'bulan_9'=>$nilai_bulan9, 'bulan_10'=>$nilai_bulan10,'bulan_11'=>$nilai_bulan11,
					'bulan_12'=>$nilai_bulan12, 'total'=>$nilai_tahun,
				);
				$this->master_model->dataUpdate('ms_budget',$data,array('id'=>$id));

			}
		}
		$result=1;
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
	}

	//BUDGET EXPENSE
	public function budget_expense(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $tahun=$this->input->get("tahun");
		if($tahun=='') $tahun=date("Y");

		$data = $this->Budget_coa_model->GetListBudgetDept('EXPENSE');
		$datadept	    = $this->master_model->GetDeptCombo();
		$datacategory	= $this->master_model->GetCategory();

		$data = array(
			'title'			=> 'Budget Expense',
			'action'		=> 'index',
			'tipek'			=> 'EXPENSE',
			'results'		=> $data,
			'tahun'		    => $tahun,
			'datakategori'  => $datacategory,
			'datadept'		=> $datadept,
			'listtahun'		=> $this->listtahun
		);
		history('View data budget expense');
		$this->load->view('Budget_coa/budget_expense',$data);
	}

	public function create_budget_expense(){
/*
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
*/
        $kategori		= $this->input->post("kategori");
        $tahun			= $this->input->post("tahun");
		$divisi			= $this->input->post("divisi");
		$dataset		= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
        $datcoa			= $this->Budget_coa_model->GetBudgetCategory($dataset);
		$datacategory	= $this->master_model->GetCategory();
		$datadept	    = $this->master_model->GetDeptCombo();

		if(empty($datcoa)){
			echo 'Data tidak ditemukan <a href="'.base_url('budget_coa/budget_expense').'">Kembali</a>';die();
			////$this->template->render('list_nr');
		}else{
			$data = array(
				'title'			=> 'Input Budget Detail',
				'action'		=> 'create_budget_expense',
				'dataset'		=> $dataset,
				'data'		    => $datcoa,
				'datakategori'  => $datacategory,
				'datadept'		=> $datadept,
				'listtahun'		=> $this->listtahun
			);
			$this->load->view('Budget_coa/budget_form_expense',$data);
		}
	}

	function save_data_expense(){
		$data_session	= $this->session->userdata;
        $id		        = $this->input->post("id");
        $type           = $this->input->post("type");
		$tahun  		= $this->input->post("tahun");
        $coa       		= $this->input->post("coa");
        $total			= $this->input->post("total");
        $info      		= $this->input->post("info");
        $divisi			= $this->input->post("divisi");
		for($i=1;$i<=12;$i++){
			${"bulan_".$i} = str_replace(',','',$this->input->post('bulan_'.$i));
		}
		$this->db->trans_start();
        if($type=="edit") {
			for ($x = 0; $x < count($coa); $x++) {
			  if($total[$x]>0){
				if($id[$x]!='') {
					$data =  array(
						'bulan_1'=>$bulan_1[$x], 'bulan_2'=>$bulan_2[$x], 'bulan_3'=>$bulan_3[$x],'bulan_4'=>$bulan_4[$x],
						'bulan_5'=>$bulan_5[$x],'bulan_6'=>$bulan_6[$x], 'bulan_7'=>$bulan_7[$x], 'bulan_8'=>$bulan_8[$x],
						'bulan_9'=>$bulan_9[$x], 'bulan_10'=>$bulan_10[$x],'bulan_11'=>$bulan_11[$x],
						'bulan_12'=>$bulan_12[$x], 'total'=>$total[$x],'status'=>'2',
						'created_by_dept'=>$data_session['ORI_User']['username'],
						'created_on_dept'=>date('Y-m-d H:i:s')
					);
					$this->master_model->dataUpdate('ms_budget',$data,array('id'=>$id[$x]));
				}
			  }
			}
			history('Add new budget expense '.$tahun.' '.$divisi);
			$result			= TRUE;
        }
		$this->db->trans_complete();
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
    }

	function revisi_data_expense(){
		$data_session	= $this->session->userdata;
        $kategori		= $this->input->post("kategori");
        $tahun			= $this->input->post("tahun");
		$divisi			= $this->input->post("divisi");
		$revisi			= $this->input->post("revisi");
		$dataset		= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
		$result 		= $this->master_model->dataUpdate('ms_budget',array('status'=>'2','revisi'=>($revisi+1), 'revision_by'=>$data_session['ORI_User']['username'],'revision_on'=>date('Y-m-d H:i:s')),$dataset);
		history('Revisi data expense '.$tahun.'/'.$divisi);
		$result=1;
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
	}

	function approve_data_expense(){
		$data_session	= $this->session->userdata;
        $kategori		= $this->input->post("kategori");
        $tahun			= $this->input->post("tahun");
		$divisi			= $this->input->post("divisi");
		$dataset		= array('tahun'=>$tahun,'divisi'=>$divisi,'kategori'=>$kategori);
		$result 		= $this->master_model->dataUpdate('ms_budget',array('status'=>'3','created_by_dept'=>$data_session['ORI_User']['username'],'created_on_dept'=>date('Y-m-d H:i:s')),$dataset);
		history('Approve data expense '.$tahun.'/'.$divisi);
		$result=1;
        $param = array(
                'save' => $result
                );
        echo json_encode($param);
	}

	//BUDGET PERIODIK
	public function budget_periodik(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $tahun=$this->input->get("tahun");
		if($tahun=='') $tahun=date("Y");

//		$data 		= $this->Budget_coa_model->GetBudgetRutin();
		$datadept	= $this->master_model->GetDeptCombo();

		$data = array(
			'title'			=> 'Budget Periodik',
			'action'		=> 'index',
//			'results'		=> $data,
			'datadept'		=> $datadept,
			'listtahun'		=> $this->listtahun
		);
		history('View data budget periodik');
		$this->load->view('Budget_coa/budget_periodik',$data);
	}

	public function create_periodik($key) {
/*
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

*/
        $datdept  	= $this->master_model->GetDeptCombo($key);
		$datcoa		= $this->master_model->GetBudgetComboCategory('',date("Y"),'');
        $data		= $this->Budget_coa_model->GetBudgetRutin($key);
		$callcoa	= $this->master_model->GetComboBudget();

		$data = array(
			'title'			=> 'Input Master Pembayaran Periodik',
			'action'		=> 'index',
			'datdept'		=> $datdept,
			'callcoa'		=> $callcoa,
			'data_detail'	=> $data,
			'datcoa'		=> $datcoa,
			'waktu'			=> $this->waktu
		);

		$this->load->view('Budget_coa/create_periodik',$data);
    }

	public function edit_periodik($key) {
/*
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

*/
        $datdept  	= $this->master_model->GetDeptCombo($key);
		$datcoa		= $this->master_model->GetBudgetComboCategory('PEMBAYARAN PERIODIK',date("Y"),$key);
        $data		= $this->Budget_coa_model->GetBudgetRutin($key);
		$callcoa	= $this->master_model->GetComboBudget();

		$data = array(
			'title'			=> 'Edit Master Pembayaran Periodik',
			'action'		=> 'index',
			'datdept'		=> $datdept,
			'callcoa'		=> $callcoa,
			'data_detail'	=> $data,
			'datcoa'		=> $datcoa,
			'waktu'			=> $this->waktu
		);

		$this->load->view('Budget_coa/create_periodik',$data);
    }

	public function save_data_periodik(){
		$data_session	= $this->session->userdata;
		$detail_id		= $this->input->post("detail_id");
        $coa       		= $this->input->post("coa");
        $nama           = $this->input->post("nama");
		$tipe  			= $this->input->post("tipe");
		$bln  			= $this->input->post("bln");
		$thn  			= $this->input->post("thn");
        $nilai			= $this->input->post("nilai");
        $keterangan		= $this->input->post("keterangan");
        $departement	= $this->input->post("departement");
        $kode_id	= $this->input->post("kode_id");

			$this->db->trans_begin();

			$delid=implode("','",$detail_id);
			$budgetcoa=array();
            $this->master_model->dataDelete('ms_budget_periodik',"id not in ('".$delid."') and departement='".$departement."'");
			if(count($detail_id)>0){
				$this->db->query("update ms_budget set bulan_1=0,bulan_2=0, bulan_3=0, bulan_4=0, bulan_5=0, bulan_6=0, bulan_7=0, bulan_8=0, bulan_9=0, bulan_10=0, bulan_11=0, bulan_12=0,total=0 WHERE tahun='".date("Y")."' and coa in ('".implode("','",$coa)."') and divisi='".$departement."' and kategori='PEMBAYARAN PERIODIK'");
			}
			for ($x = 0; $x < count($detail_id); $x++) {
				if($tipe[$x]=='bulan'){
					$tanggal=$bln[$x];
				}else{
					$tanggal=$thn[$x];
				}
				if($detail_id[$x]!='') {
					$data = array(
							array(
								'id'=>$detail_id[$x],
								'nama'=>$nama[$x],
								'coa'=>$coa[$x],
								'tipe'=>$tipe[$x],
								'tanggal'=>$tanggal,
								'nilai'=>$nilai[$x],
								'keterangan'=>$keterangan[$x],
								'kode_id'=>$kode_id[$x],
								'departement'=>$departement,
							)
						);
					$this->Budget_coa_model->updateBatchPeriodik($data,'id');
				}else{
					$data =  array(
								'nama'=>$nama[$x],
								'coa'=>$coa[$x],
								'tipe'=>$tipe[$x],
								'tanggal'=>$tanggal,
								'nilai'=>$nilai[$x],
								'keterangan'=>$keterangan[$x],
								'kode_id'=>$kode_id[$x],
								'departement'=>$departement,
							);
					$this->Budget_coa_model->insertDataPeriodik($data);
				}
				$databudget['tipe']=$tipe[$x];
				$databudget['coa']=$coa[$x];
				$databudget['tanggal']=$tanggal;
				$databudget['nilai']=$nilai[$x];
				$databudget['departement']=$departement;
				$databudget['tahun']=date("Y");
				$this->Budget_coa_model->updatebudget($databudget);
			}

			//alokasi
			$detail_alokasi	= $this->input->post("detail_alokasi");
			$kode_detail	= $this->input->post("kode_detail");
			$coa_alokasi	= $this->input->post("coa_alokasi");
			$nilai_alokasi	= $this->input->post("nilai_alokasi");
            $this->master_model->dataDelete('ms_budget_rutin_alokasi',array('departement'=>$departement));
			for ($x = 0; $x < count($kode_detail); $x++) {
				$dataalokasi =  array(
						'kode'=>$kode_detail[$x],
						'coa'=>$coa_alokasi[$x],
						'nilai'=>$nilai_alokasi[$x],
						'departement'=>$departement,
						'created_by'=>$data_session['ORI_User']['username'],
						'created_on'=>date('Y-m-d H:i:s')
					);
				$this->master_model->dataSave('ms_budget_rutin_alokasi',$dataalokasi);
			}

            if($this->db->trans_status()) {
                $keterangan     = "SUKSES, tambah data ";
                $status         = 1;
                $result         = TRUE;
            } else {
                $keterangan     = "GAGAL, tambah data ";
                $status         = 0;
                $result = FALSE;
            }
            history('Add budget periodik '.$departement);
			$this->db->trans_complete();
			$param = array(
					'save' => $result
					);
			echo json_encode($param);
    }

	function hapus_data_periodik($id){
/*
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['delete'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
*/
        if($id!=''){
			$data	= $this->master_model->GetOneData('ms_budget_periodik',array('id'=>$id));
            $this->master_model->dataDelete('ms_budget_rutin_alokasi',array('kode'=>$data->kode_id));
            $result = $this->master_model->dataDelete('ms_budget_periodik',array('id'=>$id));
			$result = 1;
			history('Delete data periodik '.$id);
        } else {
            $result = 0;
        }

        $param = array(
                'delete' => $result,
                'idx'=>$id
                );
        echo json_encode($param);
    }

    public function coa_category()
    {
		$controller			= 'budget_coa/coa_category';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

        $data = $this->db->query("select a.*,b.nama as nama_perkiraan from coa_category a left join ".DBACC.".coa_master b on a.coa=b.no_perkiraan order by tipe,a.coa")->result();
        $datcoa = $this->All_model->GetCoaCombo();	
		$this->datatipe=array('ASET'=>'ASET','NONSTOK'=>'NONSTOK','STOK'=>'STOK','NONRUTIN'=>'NONRUTIN');		
		$data = array(
			'title'			=> 'COA Category',
			'action'		=> 'index',
			'datcoa'		=> $datcoa,
			'datatipe'		=> $this->datatipe,
			'results'		=> $data
		);
		history('View data COA Category');
		$this->load->view('Budget_coa/coa_category',$data);

    }

    //Save data ajax
    public function save_category(){
        $id = $this->input->post("id");
        $nama = $this->input->post("nama");
        $coa = $this->input->post("coa");
        $tipe = $this->input->post("tipe");
		$data_session	= $this->session->userdata;
        if($id!="") {
			$data = array(
						'id'=>$id,
						'nama'=>$nama,
						'coa'=>$coa,
						'tipe'=>$tipe,
						'updated_by'=> $data_session['ORI_User']['username'],
						'updated_date'=>date("Y-m-d h:i:s"),
					);
			//Update data
			$result = $this->master_model->dataUpdate('coa_category',$data,array('id'=>$id));
        } else {
            $data = array(
						'nama'=>$nama,
						'coa'=>$coa,
						'tipe'=>$tipe,
						'created_by'=>$data_session['ORI_User']['username'],
						'created_date'=>date('Y-m-d H:i:s')
					);
            //Add Data
			$this->master_model->dataSave('coa_category',$data);
			
        }
        $param = array(
                'save' => true
                );
        echo json_encode($param);
    }

    function hapus_category() {
        $id = $this->uri->segment(3);
		$result=0;
        if($id!=''){
			$this->master_model->dataDelete('coa_category',array('id'=>$id));
			$result=1;
		}
        $param = array(
                'delete' => $result,
                'idx'=>$id
                );
        echo json_encode($param);
    }
}
