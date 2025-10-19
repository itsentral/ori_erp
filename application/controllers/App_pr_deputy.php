<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_pr_deputy extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('serverside_model');
		$this->load->model('rutin_model');

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

	
}
