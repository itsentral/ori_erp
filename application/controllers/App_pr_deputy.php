<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_pr_deputy extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	//Approval PR ATK & NON ATK
	public function approval_pr_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$tanda 				= $this->uri->segment(3);
		$lab = 'PR Rutin Non-ATK';
		if(!empty($tanda)){
			$lab = 'PR Rutin ATK';
		}
		
		$data = array(
		  'title'			=> 'Indeks Of Approval '.$lab,
		  'action'			=> 'index',
		  'row_group'		=> $data_Group,
		  'tanda'			=> $tanda,
		  'akses_menu'		=> $Arr_Akses
		);
		history('View Approval '.$lab);
		$this->load->view('App_pr_deputy/approval_pr_rutin',$data);
	}

	public function server_side_app_pr_rutin(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_app_pr_rutin(
			$requestData['tanda'],
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
		
		$tandax = $requestData['tanda'];
		$lab = " Non-ATK";
		if(!empty($tandax)){
			$lab = " ATK";
		}
		$GET_USERNAME = get_detail_user();
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
			
			$tanda = substr($row['no_pengajuan'],0,1);
			$no_ipp = $row['no_pengajuan'];
			$kebutuhan = "Project ".strtoupper(get_name('production', 'project', 'no_ipp', $row['no_pengajuan']));
			if($tanda == 'R'){
				$no_ipp = "Indirect Rutin ".date('d-M-Y', strtotime($row['book_date']));
				$kebutuhan = "Pemenuhan Stock Rutin".$lab;
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_pengajuan_group']."</div>";
			$nestedData[]	= "<div align='left'>".$no_ipp."</div>";
			$nestedData[]	= "<div align='left'>".$kebutuhan."</div>";
			$NM_LENGKAP = (!empty($GET_USERNAME[$row['created_by']]['nm_lengkap']))?$GET_USERNAME[$row['created_by']]['nm_lengkap']:$row['created_by'];
			$nestedData[]	= "<div align='left'>".strtoupper($NM_LENGKAP)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$id_user = get_name('users','id_user','username',$row['created_by']);
			
			$save			= "";
			$view			= "<button type='button'class='btn btn-sm btn-warning view_pr' title='View PR' data-sts_app='".$row['sts_app2']."' data-tanda='".$tandax."' data-no_ipp='".date('Y-m-d',strtotime($row['book_date']))."' data-no='".$nomor."' data-user='".$id_user."' data-pengajuangroup='".$row['no_pengajuan_group']."'><i class='fa fa-eye'></i></button>";
			if($row['sts_app2'] == 'N'){
				$save			= "&nbsp;<button type='button'class='btn btn-sm btn-info app_pr' title='Approve PR' data-no_ipp='".date('Y-m-d',strtotime($row['book_date']))."' data-no='".$nomor."' data-tanda='".$tandax."' data-user='".$id_user."' data-pengajuangroup='".$row['no_pengajuan_group']."' data-category_awal='".$row['category_awal']."'><i class='fa fa-check'></i></button>";
			}
			// $delete			= "&nbsp;<button type='button'class='btn btn-sm btn-danger app_pr' title='Delete PR' data-status='reject' data-id_material='".$row['no_ipp']."' data-no='".$nomor."'><i class='fa fa-close'></i></button>";
			$delete			= "";
			$nestedData[]	= "<div align='left'>".$view.$save.$delete."</div>";
			
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

	public function query_data_json_app_pr_rutin($tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = " AND b.category_awal <> '2' ";
		if(!empty($tanda)){
			$where = " AND b.category_awal = '2' ";
		}
		
		$sql = "
			SELECT
				a.*,
				b.category_awal
			FROM
				rutin_planning_detail b
				LEFT JOIN rutin_planning_header a ON a.no_pengajuan=b.no_pengajuan
		    WHERE 1=1 ".$where." AND a.sts_app = 'Y' AND a.sts_app2 = 'N' AND a.no_pr_group IS NULL
				AND (
				a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_pengajuan_group LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.no_pengajuan_group
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id',
			1 => 'no_pengajuan',
			2 => 'no_pengajuan',
			3 => '',
			4 => 'created_by',
			5 => 'created_date'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_detail_pr(){
		$no_ipp 	= $this->uri->segment(3);
		$sts_app 	= $this->uri->segment(4);
		$tanda 		= $this->uri->segment(5);
		$id_user 	= $this->input->post('id_user');
		$pengajuangroup 	= $this->input->post('pengajuangroup');
		$user 		= get_name('users','username','id_user',$id_user);
		// echo $id_user."<br>";
		$where = " AND a.category_awal = '2' ";
		if($tanda == ''){
			$where = " AND a.category_awal <> '2'  ";
		}

		$where = "";
		
		$sql		= "SELECT a.*, b.sts_app2 AS sts_app FROM rutin_planning_detail a LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan WHERE b.no_pengajuan_group='".$pengajuangroup."' AND a.purchase > 0 ".$where." AND a.sts_app = 'Y'  ORDER BY a.nm_material ASC";
		$result = $this->db->query($sql)->result_array();

		$data = array(
		  'GET_COMSUMABLE'	=> get_detail_consumable(),
		  'GET_KEBUTUHAN_PER_MONTH' => get_kebutuhanPerMonthGudang(null),
		  'no_ipp'			=> $no_ipp,
		  'result'			=> $result
		);
		$this->load->view('App_pr_deputy/modal_detail_pr',$data);
	}

	public function modal_approve_pr(){
		$no_ipp 	= $this->uri->segment(3);
		$tanda 		= $this->uri->segment(4);
		
		$id_user 	= $this->input->post('id_user');
		$pengajuangroup 	= $this->input->post('pengajuangroup');
		$category_awal 	= $this->input->post('category_awal');
		$user 		= get_name('users','username','id_user',$id_user);
		
		$where = " AND a.category_awal = '2' ";
		if($tanda == ''){
			$where = " AND a.category_awal <> '2' ";
		}
		
		$sql		= "	SELECT 
							a.*, 
							b.sts_app2 AS sts_app,
							d.stock AS stock							
						FROM 
							rutin_planning_detail a 
							LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan
							LEFT JOIN warehouse_rutin_stock d ON a.id_material = d.code_group AND d.gudang='10'
						WHERE 
							b.no_pengajuan_group='$pengajuangroup' 
							AND a.purchase > 0 
							AND a.sts_app = 'Y' 
							AND a.sts_app2 = 'N' 
							".$where." ORDER BY a.nm_material ASC";
		$result = $this->db->query($sql)->result_array();

		$data = array(
			'GET_COMSUMABLE'=> get_detail_consumable(),
		  	'GET_KEBUTUHAN_PER_MONTH' => get_kebutuhanPerMonthGudang(null),
		  	'no_ipp'		=> $no_ipp,
		  	'tanda'			=> $tanda,
		  	'id_user'		=> $id_user,
		  	'result'		=> $result,
		);
		$this->load->view('App_pr_deputy/modal_approve_pr',$data);
	}

	public function approve_pr_rutin(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$tanda			= (!empty($this->uri->segment(3)))?$this->uri->segment(3):'';
		$dateTime		= date('Y-m-d H:i:s');
		$UserName		= $data_session['ORI_User']['username'];
		$detail 		= $data['detail'];
		$Ym				= date('ym');
		// print_r($detail); exit;
		
		$qIPPX			= "SELECT MAX(no_pr_group) as maxP FROM tran_pr_header WHERE no_pr_group LIKE 'PR".$Ym."%' ";
		$numrowIPPX		= $this->db->query($qIPPX)->num_rows();
		$resultIPPX		= $this->db->query($qIPPX)->result_array();
		$angkaUrut2X		= $resultIPPX[0]['maxP'];
		$urutan2X		= (int)substr($angkaUrut2X, 6, 4);
		$urutan2X++;
		$urut2X			= sprintf('%04s',$urutan2X);
		$no_pr_group	= "PR".$Ym.$urut2X;
		
		
		$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_pr_detail WHERE no_pr LIKE 'PR".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 4);
		$urutan2++;
		$kodeP			= "PR".$Ym;
		$urut2			= sprintf('%04s',$urutan2);
		$ArrHeader 	= array();
		$ArrDetail 	= array();
		$ArrPR 		= array();
		$ArrPR2 	= array();
		foreach($detail AS $val => $valx){
			
			
			$ArrHeader[$val]['no_pengajuan'] 	= $valx['no_pengajuan'];
			$ArrHeader[$val]['no_pr'] 			= $kodeP.$urut2;
			$ArrHeader[$val]['no_pr_group'] 	= $no_pr_group;
			$ArrHeader[$val]['sts_app2'] 		= 'Y';
			$ArrHeader[$val]['sts_pr'] 			= 'Y';
			$ArrHeader[$val]['purchase_rev'] 	= str_replace(',','',$valx['qty_revisi']);
			$ArrHeader[$val]['sts_app_by2'] 		= $UserName;
			$ArrHeader[$val]['sts_app_date2'] 	= $dateTime;
			
			
			$ArrDetail[$val]['no_pengajuan'] 	= $valx['no_pengajuan'];
			$ArrDetail[$val]['no_pr'] 			= $kodeP.$urut2;
			$ArrDetail[$val]['no_pr_group'] 	= $no_pr_group;
			$ArrDetail[$val]['sts_app2'] 		= 'Y';
			$ArrDetail[$val]['purchase_rev'] 	= str_replace(',','',$valx['qty_revisi']);
			$ArrDetail[$val]['sts_app_by2'] 		= $UserName;
			$ArrDetail[$val]['sts_app_date2'] 	= $dateTime;
			
			$dt_rutin 	= $this->db->query("SELECT * FROM rutin_planning_detail WHERE no_pengajuan = '".$valx['no_pengajuan']."' LIMIT 1 ")->result();
			$id_barang 	= (!empty($dt_rutin))?$dt_rutin[0]->id_material:'not found';
			$nm_barang 	= (!empty($dt_rutin))?$dt_rutin[0]->nm_material." - ".$dt_rutin[0]->spec:'not found';
			$tanggal 	= (!empty($dt_rutin))?$dt_rutin[0]->tanggal:NULL;
			$spec 		= (!empty($dt_rutin))?$dt_rutin[0]->spec_pr:NULL;
			$info 		= (!empty($dt_rutin))?$dt_rutin[0]->info_pr:NULL;
			$satuan 	= (!empty($dt_rutin))?$dt_rutin[0]->satuan:NULL;
			
			$ArrPR2[$val]['no_pr'] 			= $kodeP.$urut2;
			$ArrPR2[$val]['no_pr_group'] 	= $no_pr_group;
			$ArrPR2[$val]['category'] 		= 'rutin';
			$ArrPR2[$val]['tgl_pr'] 		= date('Y-m-d');
			$ArrPR2[$val]['created_by'] 	= $UserName;
			$ArrPR2[$val]['created_date'] 	= $dateTime;
			$ArrPR2[$val]['app_status'] 	= 'Y';
			$ArrPR2[$val]['app_reason'] 	= strtolower($valx['keterangan']);
			$ArrPR2[$val]['app_by'] 		= $UserName;
			$ArrPR2[$val]['app_date'] 		= $dateTime;
			
			$ArrPR[$val]['no_pr'] 			= $kodeP.$urut2;
			$ArrPR[$val]['no_pr_group'] 	= $no_pr_group;
			$ArrPR[$val]['category'] 		= 'rutin';
			$ArrPR[$val]['tgl_pr'] 			= date('Y-m-d');
			$ArrPR[$val]['id_barang'] 		= $id_barang;
			$ArrPR[$val]['nm_barang'] 		= strtolower($nm_barang);
			$ArrPR[$val]['spec'] 			= strtolower($spec);
			$ArrPR[$val]['info'] 			= strtolower($info);
			$ArrPR[$val]['tgl_dibutuhkan'] 	= $tanggal;
			$ArrPR[$val]['satuan'] 			= $satuan;
			$ArrPR[$val]['qty'] 			= str_replace(',','',$valx['qty_revisi']);
			$ArrPR[$val]['created_by'] 		= $UserName;
			$ArrPR[$val]['created_date'] 	= $dateTime;
			$ArrPR[$val]['app_status'] 		= 'Y';
			$ArrPR[$val]['app_reason'] 		= strtolower($valx['keterangan']);
			$ArrPR[$val]['in_gudang'] 		= $valx['in_gudang'];
			$ArrPR[$val]['app_by'] 			= $UserName;
			$ArrPR[$val]['app_date'] 		= $dateTime;
			
			$urut2++;
			$urut2			= sprintf('%04s',$urut2);
		}
		
		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// print_r($ArrPR);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrHeader)){
				$this->db->update_batch('rutin_planning_header', $ArrHeader, 'no_pengajuan');
			}
			if(!empty($ArrDetail)){
				$this->db->update_batch('rutin_planning_detail', $ArrDetail, 'no_pengajuan');
			}
			if(!empty($ArrPR)){
				$this->db->insert_batch('tran_pr_header', $ArrPR2);
				$this->db->insert_batch('tran_pr_detail', $ArrPR);
			}
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data failed. Please try again later ...',
				'status'	=> 0,
				'tanda'		=> $tanda
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data success. Thanks ...',
				'status'	=> 1,
				'tanda'		=> $tanda
			);
			history('Approve deputy pengajuan rutin '.$data['no_ipp']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function reject_sebagian_pr_rutin(){
		$data = $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$no_ipp = $data['no_ipp'];
		$tanda = $data['tanda'];
		$id_user = $data['id_user'];
		
		$ArrHeader		= array(
			'sts_app'		=> 'D',
			'sts_app_by'	=> $data_session['ORI_User']['username'],
			'sts_app_date'	=> $dateTime,
			'sts_app2'		=> 'D',
			'sts_app_by2'	=> $data_session['ORI_User']['username'],
			'sts_app_date2'	=> $dateTime
		);
		
		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrHeader)){
				$this->db->where('no_pengajuan' , $data['no_pengajuan']);
				$this->db->update('rutin_planning_detail', $ArrHeader);
				
				$this->db->where('no_pengajuan' , $data['no_pengajuan']);
				$this->db->update('rutin_planning_header', $ArrHeader);
			}
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data failed. Please try again later ...',
				'status'	=> 0,
				'no_ipp'	=> $no_ipp,
				'tanda'		=> $tanda,
				'id_user'	=> $id_user
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data success. Thanks ...',
				'status'	=> 1,
				'no_ipp'	=> $no_ipp,
				'tanda'		=> $tanda,
				'id_user'	=> $id_user
			);
			history('Reject deputy sebagian pengajuan rutin '.$data['no_pengajuan']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function reject_all_pr_rutin(){
		$data = $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$check		= $data['check'];
		$no_ipp 	= $data['no_ipp'];
		$tanda 		= $data['tanda'];
		$id_user 	= $data['id_user'];
		
		$ArrHeader		= array(
			'sts_app'		=> 'D',
			'sts_app_by'	=> $data_session['ORI_User']['username'],
			'sts_app_date'	=> $dateTime,
			'sts_app2'		=> 'D',
			'sts_app_by2'	=> $data_session['ORI_User']['username'],
			'sts_app_date2'	=> $dateTime
		);
		
		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit;
		$this->db->trans_start();
			if(!empty($check)){
				$this->db->where_in('no_pengajuan', $check);
				$this->db->update('rutin_planning_detail', $ArrHeader);
				
				$this->db->where_in('no_pengajuan', $check);
				$this->db->update('rutin_planning_header', $ArrHeader);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data failed. Please try again later ...',
				'status'	=> 0,
				'no_ipp'	=> $no_ipp,
				'tanda'		=> $tanda,
				'id_user'	=> $id_user
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data success. Thanks ...',
				'status'	=> 1,
				'no_ipp'	=> $no_ipp,
				'tanda'		=> $tanda,
				'id_user'	=> $id_user
			);
			history('Reject deputy all pengajuan rutin');
		}
		echo json_encode($Arr_Kembali);
	}

	//Approval Department
	public function approval_pr_department(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$tanda				= $this->uri->segment(2);
		$data = array(
			'title'			=> 'Approval Deputy PR Departemen',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'tanda'			=> $tanda
		);
		history('View approval deputy pr department (non-rutin)');
		$this->load->view('App_pr_deputy/approval_pr_department',$data);
	}

	public function server_side_non_rutin(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_non_rutin(
			$requestData['tanda'],
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
			
			$tanda = $requestData['tanda'];
			
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$no_pr = (!empty($row['no_pr']))?$row['no_pr']:"<span class='text-red' title='No Pengajuan'>".$row['no_pengajuan']."</span>";
			$nestedData[]	= "<div align='center'>".$no_pr."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_dept'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_barang_group'])."</div>";
			$last_by 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			
			if($row['sts_app2'] == 'N'){
				$warna 	= 'blue';
				$sts 	= 'WAITING APPROVAL';
			}
			elseif($row['sts_app2'] == 'Y'){
				$warna 	= 'green';
				$sts 	= 'APPROVED';
			}
			else{
				$warna 	= 'red';
				$sts 	= 'REJECTED';
			}
			
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color: ".$warna.";'>".$sts."</span></div>";
				$view		= "<a href='".base_url($this->uri->segment(1).'/add_approval_pr_department/'.$row['no_pengajuan'].'/view')."' class='btn btn-sm btn-warning' title='View' data-role='qtip'><i class='fa fa-eye'></i></a>";
				$edit		= "";
				$approve	= "";
				$cancel		= "";
				$print	= "&nbsp;<a href='".base_url('non_rutin/print_pengajuan_non_rutin/'.$row['no_pengajuan'])."' target='_blank' class='btn btn-sm btn-success' title='Print'><i class='fa fa-print'></i></a>";

				if($tanda == 'approval'){
					$view		= "";
					// if($Arr_Akses['approve']=='1'){
						if($row['sts_app2'] == 'N'){
							$approve	= "&nbsp;<a href='".base_url($this->uri->segment(1).'/add_approval_pr_department/'.$row['no_pengajuan'])."' class='btn btn-sm btn-info' title='Approve' data-role='qtip'><i class='fa fa-check'></i></a>";
						}
					// }
				}
			$nestedData[]	= "<div align='left'>
									".$view."
                                    ".$edit."
									".$approve."
									".$cancel."
									".$print."
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

	public function query_data_json_non_rutin($tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($tanda == 'approval'){
			$where = "AND a.sts_app = 'Y' AND a.sts_app2 = 'N' AND (z.no_pr IS NULL AND a.no_pr IS NULL)";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_dept,
				GROUP_CONCAT(CONCAT(z.nm_barang,', ',z.spec,' <b>(',z.qty,' ',LOWER(y.kode_satuan),')</b>, ',z.tanggal,', ',LOWER(z.keterangan)) ORDER BY z.id ASC SEPARATOR '<br>') AS nm_barang_group
			FROM
				rutin_non_planning_detail z
				LEFT JOIN rutin_non_planning_header a ON z.no_pengajuan=a.no_pengajuan
				LEFT JOIN department b ON a.id_dept=b.id
				LEFT JOIN raw_pieces y ON z.satuan=y.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." AND a.status_id = 1 AND (
				a.no_pengajuan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.tanggal LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_pr LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_dept LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR z.nm_barang LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR z.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR z.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY z.no_pengajuan
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_pr',
			2 => 'b.nm_dept'
		);

		$sql .= " ORDER BY id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function add_approval_pr_department(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			$code_plan  	= $data['id'];
			$code_planx  	= $data['id'];
			$tanda        	= $data['tanda'];
			$approve        = $data['approve'];
			$no_so        	= (!empty($data['no_so']))?$data['no_so']:NULL; 
			$project_name   = (!empty($data['project_name']))?$data['project_name']:NULL; 
			$id_dept 		= (!empty($data['id_dept']))?$data['id_dept']:NULL;
			$id_costcenter 	= (!empty($data['id_costcenter']))?$data['id_costcenter']:NULL;
			$coa 			= (!empty($data['coa']))?$data['coa']:NULL;
			$budget 		= str_replace(',','',$data['budget']);
			$sisa_budget 	= str_replace(',','',$data['sisa_budget']);
			
			$detail 		= $data['detail'];
			
			//approve
			$sts_app        = (!empty($data['sts_app']))?$data['sts_app']:'';
			$reason        	= (!empty($data['reason']))?$data['reason']:'';
			
			$ym = date('ym');
			
			
			$SUM_QTY = 0;
			$SUM_HARGA = 0;
			
			//header approve
			$ArrDetail = array();
			$ArrDetailPR = array();
			
			$Ym = date('ym');
			$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_pr_header WHERE no_pr LIKE 'PRN".$Ym."%' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$no_pr			= "PRN".$Ym.$urut2;
			
			

			$Ym = date('ym');
			$qIPPX			= "SELECT MAX(no_pr_group) as maxP FROM tran_pr_header WHERE no_pr_group LIKE 'PR".$Ym."%' ";
			$numrowIPPX		= $this->db->query($qIPPX)->num_rows();
			$resultIPPX		= $this->db->query($qIPPX)->result_array();
			$angkaUrut2X	= $resultIPPX[0]['maxP'];
			$urutan2X		= (int)substr($angkaUrut2X, 6, 4);
			$urutan2X++;
			$urut2X			= sprintf('%04s',$urutan2X); 
			$no_pr_group	= "PR".$Ym.$urut2X;
			
			$ArrHeaderPR = array(
				'no_pr' => $no_pr,
				'no_pr_group' => $no_pr_group,
				'category' => 'non rutin',
				'tgl_pr'	=> date('Y-m-d'),
				'created_by' => $this->session->userdata['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			);
			
			$SUM_QTY = 0;
			$SUM_HARGA = 0;
			if(!empty($detail)){
				foreach($detail AS $val => $valx){
					$qty 	= str_replace(',','',$valx['qty']);
					$harga 	= str_replace(',','',$valx['harga']);
					
					$SUM_QTY 	+= $qty;
					$SUM_HARGA 	+= $harga * $qty;
					
					$ArrDetail[$val]['id'] 			= $valx['id'];
					$ArrDetail[$val]['no_pr'] 		= $no_pr;
					$ArrDetail[$val]['qty_rev'] 	= $qty;
					$ArrDetail[$val]['harga_rev'] 	= $harga;
					$ArrDetail[$val]['sts_app2'] 	= $sts_app;
					$ArrDetail[$val]['sts_app_by2'] 	= $data_session['ORI_User']['username'];
					$ArrDetail[$val]['sts_app_date2']= $dateTime;
					
					
					$ArrDetailPR[$val]['no_pr'] 		= $no_pr;
					$ArrDetailPR[$val]['no_pr_group'] 	= $no_pr_group;
					$ArrDetailPR[$val]['category'] 		= 'non rutin';
					$ArrDetailPR[$val]['tgl_pr'] 		= date('Y-m-d');
					$ArrDetailPR[$val]['id_barang'] 	= $valx['id'];
					$ArrDetailPR[$val]['nm_barang'] 	= strtolower($valx['nm_barang'].' - '.$valx['spec']);
					$ArrDetailPR[$val]['qty'] 			= $qty;
					$ArrDetailPR[$val]['nilai_pr'] 		= $harga;
					$ArrDetailPR[$val]['tgl_dibutuhkan']= $valx['tanggal'];
					$ArrDetailPR[$val]['satuan']		= $valx['satuan'];
					$ArrDetailPR[$val]['spec']		= $valx['spec'];
					$ArrDetailPR[$val]['info']		= $valx['keterangan'];
					$ArrDetailPR[$val]['app_status'] 	= 'Y';
					$ArrDetailPR[$val]['app_reason']	= strtolower($valx['keterangan']);
					$ArrDetailPR[$val]['app_by'] = $data_session['ORI_User']['username'];
					$ArrDetailPR[$val]['app_date']= $dateTime;
					$ArrDetailPR[$val]['created_by'] 	= $data_session['ORI_User']['username'];
					$ArrDetailPR[$val]['created_date'] 	= $dateTime;
				}
			}
		
			$ArrHeader		= array(
				'qty_rev' 		=> $SUM_QTY,
				'harga_rev' 	=> $SUM_HARGA,
				'no_pr' 		=> $no_pr,
				'sts_app2' 		=> $sts_app,
				'reason' 		=> $reason,
				'sts_app_by2'	=> $data_session['ORI_User']['username'],
				'sts_app_date2'	=> $dateTime
			);
			// print_r($ArrHeaderPR);
			// print_r($ArrDetailPR);
			// exit;
			
			$this->db->trans_start();
				$this->db->where(array('no_pengajuan' => $code_planx));
				$this->db->update('rutin_non_planning_header', $ArrHeader);
				
				$this->db->update_batch('rutin_non_planning_detail', $ArrDetail, 'id');
				
				$this->db->insert('tran_pr_header', $ArrHeaderPR);
				$this->db->insert_batch('tran_pr_detail', $ArrDetailPR);
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Process data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Process data success. Thanks ...',
					'status'	=> 1
				);
				history('Approval deputy pengajuan budget non rutin '.$code_plan);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			// $controller			= ucfirst(strtolower($this->uri->segment(1)));
			// $Arr_Akses			= getAcccesmenu($controller);
			// if($Arr_Akses['read'] !='1'){
			// 	$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			// 	redirect(site_url('dashboard'));
			// }
			
			$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
			$id 		= $this->uri->segment(3);
			$approve 	= 'approval';
			$header 	= $this->db->query("SELECT * FROM rutin_non_planning_header WHERE no_pengajuan='".$id."' ")->result();
			$detail 	= $this->db->query("SELECT * FROM rutin_non_planning_detail WHERE no_pengajuan='".$id."' ")->result_array();
			$datacoa 	= $this->db->query("SELECT a.coa,b.nama FROM coa_category a join ".DBACC.".coa_master b on a.coa=b.no_perkiraan WHERE a.tipe='NONRUTIN' order by a.coa")->result_array();
			$satuan		= $this->db->get_where('raw_pieces',array('delete'=>'N'))->result_array();
			$tanda 		= (!empty($header))?'Edit':'Add';
			if(!empty($approve)){
				$tanda 		= ($approve == 'view')?'View':'Approve';
			}
			$data = array(
				'title'				=> $tanda.' Deputy PR Departemen',
					'action'		=> strtolower($tanda),
					// 'akses_menu'	=> $Arr_Akses,
					'header'		=> $header,
					'detail'		=> $detail,
					'datacoa'		=> $datacoa,
					'satuan'		=> $satuan,
					'approve'		=> $approve,
					'id'			=> $id 
			);
			
			$this->load->view('App_pr_deputy/add_approval_pr_department',$data);
		}
	}
	
}
