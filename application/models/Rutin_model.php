<?php

class Rutin_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		// Your own constructor code
	}

  //===============================================================================================================================
  //=============================================RUTIN=============================================================================
  //===============================================================================================================================
	
	public function index_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$inventory = $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ORDER BY category ASC")->result_array();
		$data = array(
			'title'			=> 'Barang Stok',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'inventory'		=> $inventory,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Rutin');
		$this->load->view('Rutin/index',$data);
	}
	
	public function get_json_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_rutin(
			$requestData['inventory'],
			$requestData['status'],
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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['code_group']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['categoryb']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['kode_excel']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['kode_item']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_accurate']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material_name']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['trade_name']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['spec']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['brand']))."</div>";
				$class	= 'bg-green';
				$status	= 'Active';
				if($row['status'] == 0){
					$class	= 'bg-red';
					$status	= 'Not Active';
				}
			$nestedData[]	= "<div align='center'><span class='badge $class'>".$status."</span></div>";
					$update	= "";
					$delete	= "";
					if($Arr_Akses['update']=='1'){
						$update	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/add_new/'.$row['code_group'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($Arr_Akses['delete']=='1'){
						$delete	= "&nbsp;<button class='btn btn-sm btn-danger deleted' title='Delete' data-code_group='".$row['code_group']."'><i class='fa fa-trash'></i></button>";
					}
			$nestedData[]	= "<div align='left'>
									<button type='button' class='btn btn-sm btn-warning detail' title='Detail' data-code_group='".$row['code_group']."'><i class='fa fa-eye'></i></button>
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

	public function get_query_json_rutin($inventory, $status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_inventory = "";
		if(!empty($inventory)){
			$where_inventory = " AND a.category_awal = '".$inventory."' ";
		}

		$where_status = "";
		if($status != 'X'){
			$where_status = " AND a.status = '".$status."' ";
		}
		
		$sql = "
			SELECT
				a.*,
				b.category AS categoryb
			FROM
				con_nonmat_new a 
				LEFT JOIN con_nonmat_category_awal b ON a.category_awal = b.id
		    WHERE 1=1 AND 
				a.code_group LIKE 'CN%' 
				AND b.id <> 9
				AND a.deleted='N' 
				".$where_inventory."
				".$where_status."
			AND (
				a.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.brand LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.order_point LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.lead_time LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'code_group',
			2 => 'material_name',
			3 => 'categoryb',
			4 => 'spec',
			5 => 'brand',
			6 => 'order_point',
			7 => 'lead_time'
		);

		$sql .= " ORDER BY a.id, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	//===============================================================================================================================
	//========================================APPROVE RUTIN==============================================================================
	//===============================================================================================================================
  
	public function index_approval_pr_rutin(){
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
		$this->load->view('Rutin/approval_pr_rutin',$data);
	}
	
	public function modal_detail_pr_rutin(){
		$no_ipp 	= $this->uri->segment(3);
		$sts_app 	= $this->uri->segment(4);
		$tanda 		= $this->uri->segment(5);
		$id_user 	= $this->input->post('id_user');
		$pengajuangroup 	= $this->input->post('pengajuangroup');
		$user 		= get_name('users','username','id_user',$id_user);
		// echo $id_user."<br>";
		$where = " AND a.category_awal = '2' ";
		if($tanda == ''){
		//			$where = " AND a.category_awal in ('1','2')  ";
			$where = " AND a.category_awal <> '2'  ";
		}

		$where = "";
		
		// $sql		= "SELECT a.*, b.sts_app FROM rutin_planning_detail a LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan WHERE b.book_date LIKE '".$no_ipp."%' AND b.created_by='".$user."' AND a.purchase > 0 AND a.sts_app = '".$sts_app."' ".$where."  ORDER BY a.nm_material ASC";
		$sql		= "SELECT a.*, b.sts_app FROM rutin_planning_detail a LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan WHERE b.no_pengajuan_group='".$pengajuangroup."' AND a.purchase > 0 ".$where."  ORDER BY a.nm_material ASC";
		$result = $this->db->query($sql)->result_array();

		$data = array(
		  'GET_COMSUMABLE'	=> get_detail_consumable(),
		  'GET_KEBUTUHAN_PER_MONTH' => get_kebutuhanPerMonthGudang(null),
		  'no_ipp'			=> $no_ipp,
		  'result'			=> $result
		);
		$this->load->view('Rutin/modal_detail_pr',$data);
	}

	public function print_detail_pr_rutin(){
		$no_ipp = $this->uri->segment(4);
		$sts_app = $this->uri->segment(5);
		$tanda = $this->uri->segment(7);
		$id_user = $this->uri->segment(3);
		$pengajuangroup = $this->uri->segment(6);
		$user = get_name('users','username','id_user',$id_user);
		// echo $id_user."<br>";
		$where = " AND a.category_awal = '2' ";
		if($tanda == ''){
			$where = " AND a.category_awal in ('1','2','8') ";
		}

		$where = "";
		
		// $sql		= "SELECT a.*, b.sts_app FROM rutin_planning_detail a LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan WHERE b.book_date LIKE '".$no_ipp."%' AND b.created_by='".$user."' AND a.purchase > 0 AND a.sts_app = '".$sts_app."' ".$where."  ORDER BY a.nm_material ASC";
		$sql		= "SELECT a.*, b.sts_app, b.no_pengajuan_group, b.no_pengajuan,b.no_pr, b.created_date FROM rutin_planning_detail a LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan WHERE b.no_pengajuan_group='".$pengajuangroup."' AND a.purchase > 0 AND a.sts_app = '".$sts_app."' ".$where."  ORDER BY a.nm_material ASC";
		$result = $this->db->query($sql)->result_array();

		$data = array(
		  'no_ipp'		=> $no_ipp,
		  'result'		=> $result
		);
		$this->load->view('Print/print_detail_pr',$data);
	}
	
	public function modal_edit_pr(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			
			$ArrHeader		= array(
				'purchase'		=> str_replace(',','',$data['purchase']),
				'updated_by'	=> $data_session['ORI_User']['username'],
				'updated_date'	=> $dateTime,
				'book_by'	=> $data_session['ORI_User']['username'],
				'book_date'	=> $dateTime
			);
			
			$ArrDetail		= array(
				'id_material'	=> $data['id_material'],
				'category_awal'	=> get_name('con_nonmat_new', 'category_awal', 'code_group', $data['id_material']),
				'spec'			=> get_name('con_nonmat_new', 'spec', 'code_group', $data['id_material']),
				'satuan'		=> $data['satuan'],
				'tanggal'		=> $data['tanggal'],
				'nm_material'	=> get_name('con_nonmat_new', 'material_name', 'code_group', $data['id_material']),
				'purchase'		=> str_replace(',','',$data['purchase'])
			);
			
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// exit;
			$this->db->trans_start();
				if(!empty($ArrHeader)){
					$this->db->where('no_pengajuan' , $data['no_pengajuan']);
					$this->db->update('rutin_planning_header', $ArrHeader);
					
					$this->db->where('no_pengajuan' , $data['no_pengajuan']);
					$this->db->update('rutin_planning_detail', $ArrDetail);
				}
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
				history('Edit pengajuan rutin '.$data['no_pengajuan']);
			}
			echo json_encode($Arr_Kembali);
			
		}
		else{
			$no_trans 	= $this->uri->segment(3);
			$view 		= $this->uri->segment(4);
			$sql		= "SELECT a.*, SUM(a.purchase) AS qty_request, MAX(a.moq) AS moq_m FROM rutin_planning_detail a LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan WHERE a.no_pengajuan LIKE '".$no_trans."%'";
			$result 	= $this->db->query($sql)->result();
			$data = array(
			  'no_trans'	=> $no_trans,
			  'view'		=> $view,
			  'result'		=> $result
			);
			$this->load->view('Rutin/modal_edit_pr',$data);
		}
	}
	
	public function modal_approve_pr_rutin(){
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
							b.sts_app,
							d.stock AS stock							
						FROM 
							rutin_planning_detail a 
							LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan
							LEFT JOIN warehouse_rutin_stock d ON a.id_material = d.code_group AND d.gudang='10'
						WHERE 
							b.no_pengajuan_group='$pengajuangroup' 
							AND a.purchase > 0 
							AND a.sts_app = 'N' ".$where." ORDER BY a.nm_material ASC";
		$result = $this->db->query($sql)->result_array();

		$data = array(
			'GET_COMSUMABLE'	=> get_detail_consumable(),
		  'GET_KEBUTUHAN_PER_MONTH' => get_kebutuhanPerMonthGudang(null),
		  'no_ipp'		=> $no_ipp,
		  'tanda'		=> $tanda,
		  'id_user'		=> $id_user,
		  'result'		=> $result,
		);
		$this->load->view('Rutin/modal_approve_pr',$data);
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
			'sts_app_date'	=> $dateTime
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
			history('Reject sebagian pengajuan rutin '.$data['no_pengajuan']);
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
			'sts_app_date'	=> $dateTime
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
			history('Reject all pengajuan rutin');
		}
		echo json_encode($Arr_Kembali);
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
			// $ArrHeader[$val]['no_pr'] 			= $kodeP.$urut2;
			// $ArrHeader[$val]['no_pr_group'] 	= $no_pr_group;
			$ArrHeader[$val]['sts_app'] 		= 'Y';
			$ArrHeader[$val]['sts_pr'] 			= 'Y';
			$ArrHeader[$val]['purchase_rev'] 	= str_replace(',','',$valx['qty_revisi']);
			$ArrHeader[$val]['sts_app_by'] 		= $UserName;
			$ArrHeader[$val]['sts_app_date'] 	= $dateTime;
			
			
			$ArrDetail[$val]['no_pengajuan'] 	= $valx['no_pengajuan'];
			// $ArrDetail[$val]['no_pr'] 			= $kodeP.$urut2;
			// $ArrDetail[$val]['no_pr_group'] 	= $no_pr_group;
			$ArrDetail[$val]['sts_app'] 		= 'Y';
			$ArrDetail[$val]['purchase_rev'] 	= str_replace(',','',$valx['qty_revisi']);
			$ArrDetail[$val]['sts_app_by'] 		= $UserName;
			$ArrDetail[$val]['sts_app_date'] 	= $dateTime;
			
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
			// if(!empty($ArrPR)){
			// 	$this->db->insert_batch('tran_pr_header', $ArrPR2);
			// 	$this->db->insert_batch('tran_pr_detail', $ArrPR);
			// }
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
			history('Approve pengajuan rutin '.$data['no_ipp']);
		}
		echo json_encode($Arr_Kembali);
	}
	
	public function get_data_json_app_pr_rutin(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

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
			// $status = "WAITING APPROVAL";
			// $warna = "green";
			// if($row['sts_app'] == 'Y'){
			// 	$status = "CLOSE";
			// 	$warna = "red";
			// 	if(!empty($row['no_pr'])){
			// 		$status = "WAITING PO";
			// 		$warna = "blue";
			// 	}
				
			// }
			// if($row['sts_app'] == 'D'){
			// 	$status = "REJECT";
			// 	$warna = "red";
			// }
			// $nestedData[]	= "<div align='left' class='badge bg-".$warna."'>".$status."</div>";
			
			$save			= "";
			$view			= "<button type='button'class='btn btn-sm btn-warning view_pr' title='View PR' data-sts_app='".$row['sts_app']."' data-tanda='".$tandax."' data-no_ipp='".date('Y-m-d',strtotime($row['book_date']))."' data-no='".$nomor."' data-user='".$id_user."' data-pengajuangroup='".$row['no_pengajuan_group']."'><i class='fa fa-eye'></i></button>";
			if($row['sts_app'] == 'N'){
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
		    WHERE 1=1 ".$where." AND a.sts_app = 'N'
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
	
  //===============================================================================================================================
  //========================================WAREHOUSE RUTIN==============================================================================
  //===============================================================================================================================
	
	public function warehouse_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/pr_rutin';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$inventory 			= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ORDER BY category ASC")->result_array();

		$GetTotal = $this->db->select('SUM(total_price_pr) AS total_price')->get('con_nonmat_new')->result_array();
		$GetBudget = $this->db->select('SUM(kebutuhan_month*price_from_supplier) AS total_price')->get('budget_rutin_detail')->result_array();

		$TotalPR 		= (!empty($GetTotal[0]['total_price']))?$GetTotal[0]['total_price']:0;
		$TotalBudget 	= (!empty($GetBudget[0]['total_price']))?$GetBudget[0]['total_price']:0;
		$data = array(
			'title'			=> 'Indeks Of Add PR Stock',
			'action'		=> 'index',
			'inventory'		=> $inventory,
			'TotalPR'		=> $TotalPR,
			'TotalBudget'		=> $TotalBudget,
			'akses_menu'	=> $Arr_Akses
		);
		// $tgl_now = date('Y-m-d');
		// $tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
		// echo $tgl_next_month;
		history('View Data Add PR Warehouse Rutin');
		$this->load->view('Rutin/warehouse_rutin',$data);
	}
	
	public function pr_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/pr_rutin';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$inventory 			= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ORDER BY category ASC")->result_array();
		$data = array(
			'title'			=> 'Indeks Of PR Stock',
			'action'		=> 'index',
			'inventory'		=> $inventory,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data PR Warehouse Rutin');
		$this->load->view('Rutin/pr_rutin',$data);
	}

	public function pr_rutin_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/pr_rutin';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$tanda 				= $this->uri->segment(3);

		$data = array(
		  'title'			=> 'Indeks Of PR Stock',
		  'action'			=> 'index',
		  'row_group'		=> $data_Group,
		  'tanda'			=> $tanda,
		  'akses_menu'		=> $Arr_Akses
		);
		history('View Data PR Warehouse Rutin');
		$this->load->view('Rutin/pr_rutin_new',$data);
	}

	public function get_data_json_pr_rutin_new(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_pr_rutin_new(
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

		$GET_NAMA_LENGKAP = get_detail_user();
		
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

			if($row['category_awal'] == '2'){
				$lab = " ATK";
			}
			if($row['category_awal'] == '1'){
				$lab = " Non-ATK";
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
			$nestedData[]	= "<div align='center'>".$row['no_pr_group']."</div>";
			$nestedData[]	= "<div align='left'>".$kebutuhan."</div>";
			$username = (!empty($GET_NAMA_LENGKAP[strtolower($row['created_by'])]['nm_lengkap']))?$GET_NAMA_LENGKAP[strtolower($row['created_by'])]['nm_lengkap']:'';
			$nestedData[]	= "<div align='left'>".ucwords($username)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
			$id_user = get_name('users','id_user','username',$row['created_by']);
			$status = "WAITING APPROVAL";
			$warna = "green";
			if($row['sts_app'] == 'Y'){
				$status = "CLOSE";
				$warna = "red";
				if(!empty($row['no_pr'])){
					$status = "WAITING PO";
					$warna = "blue";
				}
				
			}
			if($row['sts_app'] == 'D'){
				$status = "REJECT";
				$warna = "red";
			}
			$nestedData[]	= "<div align='left' class='badge bg-".$warna."'>".$status."</div>";
			
			$save			= "";
			$view			= "<button type='button'class='btn btn-sm btn-warning view_pr' title='View PR' data-sts_app='".$row['sts_app']."' data-tanda='".$tandax."' data-no_ipp='".date('Y-m-d',strtotime($row['book_date']))."' data-no='".$nomor."' data-user='".$id_user."' data-pengajuangroup='".$row['no_pengajuan_group']."'><i class='fa fa-eye'></i></button>";
			$print			= "&nbsp;<button type='button'class='btn btn-sm btn-info print_pr' title='Print PR' data-sts_app='".$row['sts_app']."' data-tanda='".$tandax."' data-no_ipp='".date('Y-m-d',strtotime($row['book_date']))."' data-no='".$nomor."' data-user='".$id_user."' data-pengajuangroup='".$row['no_pengajuan_group']."'><i class='fa fa-print'></i></button>";
			if($row['sts_app'] == 'N'){
				$save			= "&nbsp;<button type='button'class='btn btn-sm btn-primary edit_pr' title='Edit PR' data-sts_app='".$row['sts_app']."' data-tanda='".$tandax."' data-no_ipp='".date('Y-m-d',strtotime($row['book_date']))."' data-no='".$nomor."' data-user='".$id_user."' data-pengajuangroup='".$row['no_pengajuan_group']."'><i class='fa fa-edit'></i></button>";
			}
			// $delete			= "&nbsp;<button type='button'class='btn btn-sm btn-danger app_pr' title='Delete PR' data-status='reject' data-id_material='".$row['no_ipp']."' data-no='".$nomor."'><i class='fa fa-close'></i></button>";
			$delete			= "";
			$nestedData[]	= "<div align='left'>".$view.$print.$save."</div>";
			
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

	public function query_data_json_pr_rutin_new($tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		// $where = " AND b.category_awal <> '2' ";
		// if(!empty($tanda)){
		// 	$where = " AND b.category_awal = '2' ";
		// }
		
		$sql = "SELECT
					a.*,
					b.category_awal
				FROM
					rutin_planning_detail b
					LEFT JOIN rutin_planning_header a ON a.no_pengajuan=b.no_pengajuan
				WHERE b.purchase > 0
					AND (
					a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_pengajuan_group LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_pr_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.no_pengajuan_group
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_pengajuan',
			3 => 'no_pr_group'
		);

		$sql .= " ORDER BY a.created_date desc, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add_pr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/warehouse_rutin';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$id_barang = $this->uri->segment(3);
		$result 			= $this->db->query("SELECT
													a.*,
													b.category AS categoryb,
													d.spec AS spec,
													d.material_name,
													c.nm_gudang AS nm_gudang
												FROM
													warehouse_rutin_stock a 
													LEFT JOIN con_nonmat_category_awal b ON a.category_awal = b.id
													LEFT JOIN con_nonmat_new d ON a.code_group = d.code_group
													LEFT JOIN warehouse c ON a.gudang = c.id
												WHERE a.code_group='".$id_barang."' "
												)->result();
		$sum_kebutuhan = $this->db->query("SELECT SUM(kebutuhan_month) AS sum_keb FROM budget_rutin_detail WHERE id_barang='".$result[0]->code_group."' ")->result();
		$satuan				= $this->db->query("SELECT * FROM unit WHERE deleted='N' ORDER BY unit ASC ")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Add PR Rutin',
			'action'		=> 'index',
			'data'			=> $result,
			'sum_kebutuhan'	=> $sum_kebutuhan,
			'satuan'		=> $satuan,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Rutin/add_pr',$data);
	}
	
	public function save_rutin(){
		$data = $this->input->post();
		
		$id_material 	= $data['id_material'];
		$purchase 		= $data['purchase'];
		$tanggal 		= $data['tanggal'];
		$satuan 		= $data['satuan'];
		
		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_pengajuan) as maxP FROM rutin_planning_header WHERE no_pengajuan LIKE 'R".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 5, 5);
		$urutan2++;
		$urut2			= sprintf('%05s',$urutan2);
		$kodeP			= "R".$Ym.$urut2;
		
		$get_material = $this->db->query("SELECT * FROM con_nonmat_new WHERE code_group='".$id_material."' LIMIT 1")->result();
		
		$ArrHeader = array(
			'no_pengajuan' 	=> $kodeP,
			'purchase' 		=> $purchase,
			'book_by' 		=> $this->session->userdata['ORI_User']['username'],
			'book_date' 	=> date('Y-m-d H:i:s'),
			'created_by' 	=> $this->session->userdata['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrDetail = array(
			'no_pengajuan' 	=> $kodeP,
			
			'id_material' 	=> $get_material[0]->code_group,
			'spec' 			=> $get_material[0]->spec,
			'nm_material' 	=> $get_material[0]->material_name,
			'category_awal' => $get_material[0]->category_awal,
			'satuan' 		=> $satuan,
			'purchase' 		=> $purchase,
			'tanggal' 		=> $tanggal
		);
		
		
		// echo $kodeP."<br>";
		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
  			$this->db->insert('rutin_planning_header', $ArrHeader);
  			$this->db->insert('rutin_planning_detail', $ArrDetail);
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
  			history('Create List PR Rutin '.$kodeP);
  		}
  		echo json_encode($Arr_Data);
	}

	public function save_rutin_change(){
		$data = $this->input->post();
		
		$id_material 	= $data['id_material'];
		$purchase 		= $data['purchase'];
		$tanggal 		= $data['tanggal'];
		$satuan 		= $data['satuan'];
		$spec 			= $data['spec'];
		$info 			= $data['info'];
		$inventory 		= $data['inventory'];
		$price 			= str_replace(',','',$data['price']);
		$total_price_pr = $price*$purchase;
		
		
		$ArrHeader = array(
			'price_pr' 	=> $price,
			'total_price_pr' 	=> $total_price_pr,
			'spec_pr' 	=> $spec,
			'info_pr' 	=> $info,
			'request' 	=> $purchase,
			'tgl_dibutuhkan' => $tanggal
		);
		
		$this->db->trans_start();
  			$this->db->where('code_group', $id_material);
  			$this->db->update('con_nonmat_new', $ArrHeader);
  		$this->db->trans_complete();

  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				'totalpr' 	=> 0
  			);
  		}
  		else{
  			$this->db->trans_commit();
			if($inventory != '0'){
				$GetTotal = $this->db->select('SUM(total_price_pr) AS total_price')->get_where('con_nonmat_new',['category_awal'=>$inventory])->result_array();
			}
			else{
				$GetTotal = $this->db->select('SUM(total_price_pr) AS total_price')->get('con_nonmat_new')->result_array();
			}

			$TotalPR = (!empty($GetTotal[0]['total_price']))?$GetTotal[0]['total_price']:0;

			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				'totalpr' 	=> number_format($TotalPR)
  			);

  			history('Change request rutin '.$id_material.' / '.$purchase.' / '.$tanggal);
  		}
  		echo json_encode($Arr_Data);
	}

	public function save_rutin_change_date(){
		$data = $this->input->post();
		
		$tanggal 		= $data['tanggal'];
		$get_materials 	= $this->db->get('con_nonmat_new')->result_array();
		
		foreach ($get_materials as $key => $value) {
			$ArrUpdate[$key]['code_group'] = $value['code_group'];
			$ArrUpdate[$key]['tgl_dibutuhkan'] = $tanggal;
		}
		
		$this->db->trans_start();
  			$this->db->update_batch('con_nonmat_new', $ArrUpdate,'code_group');
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
  			history('Change propose request rutin date all '.$tanggal);
  		}
  		echo json_encode($Arr_Data);
	}

	public function auto_update_rutin(){
		$data = $this->input->post();
		$category_awal = $this->uri->segment(3);
		$gudang = $this->uri->segment(4);
		$tgl_now = date('Y-m-d');
		$tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
		$get_rutin 	= $this->db->get_where('con_nonmat_new',array('category_awal'=>$category_awal))->result_array();
		$ArrUpdate = [];

		foreach ($get_rutin as $key => $value) {
			$get_kebutuhan 	= $this->db->select('SUM(kebutuhan_month) AS sum_keb')->get_where('budget_rutin_detail',array('id_barang'=>$value['code_group']))->result();
			$get_stock 		= $this->db->select('stock')->get_where('warehouse_rutin_stock',array('code_group'=>$value['code_group']))->result();
			$get_price 		= $this->db->select('price_supplier')->get_where('price_ref',array('code_group'=>$value['code_group'],'deleted_date'=>NULL))->result();

			$stock_oke 	= (!empty($get_stock[0]->stock))?$get_stock[0]->stock:0;
			$purchase 	= ($get_kebutuhan[0]->sum_keb * 1.5) - $stock_oke;
			$purchase2 	= ($purchase > 0)?ceil($purchase):0;
			$price_ref 	= (!empty($get_price[0]->price_supplier))?$get_price[0]->price_supplier:0;
			$MOQ 		= $value['min_order'];
			if($purchase2 < $MOQ){
				$purchase2 = $MOQ;
			}

			$ArrUpdate[$key]['id'] = $value['id'];
			$ArrUpdate[$key]['request'] = $purchase2;
			$ArrUpdate[$key]['tgl_dibutuhkan'] = $tgl_next_month;
			$ArrUpdate[$key]['price_pr'] = $price_ref;
			$ArrUpdate[$key]['total_price_pr'] = $price_ref * $purchase2;
		}
		
		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch('con_nonmat_new', $ArrUpdate,'id');
			}
  		$this->db->trans_complete();

  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				'inventory' => $category_awal,
				'gudang' => $gudang,
				'totalpr' 	=> 0
  			);
  		}
  		else{
  			$this->db->trans_commit();
			if($category_awal != '0'){
				$GetTotal = $this->db->select('SUM(total_price_pr) AS total_price')->get_where('con_nonmat_new',['category_awal'=>$category_awal])->result_array();
			}
			else{
				$GetTotal = $this->db->select('SUM(total_price_pr) AS total_price')->get('con_nonmat_new')->result_array();
			}

			$TotalPR = (!empty($GetTotal[0]['total_price']))?$GetTotal[0]['total_price']:0;
  			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				'inventory' => $category_awal,
				'gudang' => $gudang,
				'totalpr' 	=> number_format($TotalPR)
  			);
  			history('Update auto rutin pr');
  		}
  		echo json_encode($Arr_Data);
	}

	public function clear_update_rutin(){
		$data = $this->input->post();
		$category_awal = $this->uri->segment(3);
		$gudang = $this->uri->segment(4);
		$tgl_now = date('Y-m-d');
		$tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
		// $get_rutin 	= $this->db->get_where('con_nonmat_new',array('category_awal'=>$category_awal))->result_array();
		$get_rutin 	= $this->db->get('con_nonmat_new')->result_array();
		$ArrUpdate = [];

		foreach ($get_rutin as $key => $value) {
			$ArrUpdate[$key]['id'] = $value['id'];
			$ArrUpdate[$key]['request'] = 0;
			$ArrUpdate[$key]['tgl_dibutuhkan'] = $tgl_next_month;
			$ArrUpdate[$key]['total_price_pr'] = 0;
		}
		
		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch('con_nonmat_new', $ArrUpdate,'id');
			}
  		$this->db->trans_complete();

  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				'inventory' => $category_awal,
				'gudang' => $gudang,
				'totalpr' 	=> 0
  			);
  		}
  		else{
  			$this->db->trans_commit();
			if($category_awal != '0'){
				$GetTotal = $this->db->select('SUM(total_price_pr) AS total_price')->get_where('con_nonmat_new',['category_awal'=>$category_awal])->result_array();
			}
			else{
				$GetTotal = $this->db->select('SUM(total_price_pr) AS total_price')->get('con_nonmat_new')->result_array();
			}

			$TotalPR = (!empty($GetTotal[0]['total_price']))?$GetTotal[0]['total_price']:0;
  			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				'inventory' => $category_awal,
				'gudang' => $gudang,
				'totalpr' 	=> number_format($TotalPR)
  			);
  			history('Clear all rutin pr');
  		}
  		echo json_encode($Arr_Data);
	}

	public function save_pr_rutin_all(){
		$data = $this->input->post();
		$category_awal = $this->uri->segment(3);
		$UserName = $this->session->userdata['ORI_User']['username'];
		$DateTime = date('Y-m-d H:i:s');
		$in_gudang			= $data['in_gudang'];

		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_pengajuan) as maxP FROM rutin_planning_header WHERE no_pengajuan LIKE 'R".$Ym."%' ";
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 5, 5);

		$get_rutin 	= $this->db->get_where('con_nonmat_new',array('request >'=>0,'category_awal'=>$category_awal))->result_array();
		$ArrSaveHeader = [];
		$ArrSaveDetail = [];

		//GROUP PNGAJUAN
		$QUERY			= "SELECT MAX(no_pengajuan_group) as maxP FROM rutin_planning_header WHERE no_pengajuan_group LIKE 'R".$Ym."%' ";
		$RESULT			= $this->db->query($QUERY)->result_array();
		$ANGKAURUT		= $RESULT[0]['maxP'];
		$URUTANN		= (int)substr($ANGKAURUT, 5, 5);
		$URUTANN++;
		$URUT			= sprintf('%05s',$URUTANN);
		$KODE_GROUP		= "R".$Ym.$URUT;

		foreach ($get_rutin as $key => $value) {
			$urutan2++;
			$urut2			= sprintf('%05s',$urutan2);
			$kodeP			= "R".$Ym.$urut2;

			$ArrSaveHeader[] = array(
				'no_pengajuan_group' 	=> $KODE_GROUP,
				'no_pengajuan' 	=> $kodeP,
				'purchase' 		=> $value['request'],
				'book_by' 		=> $UserName,
				'book_date' 	=> $DateTime,
				'created_by' 	=> $UserName,
				'created_date' 	=> $DateTime
			);
			
			$ArrSaveDetail[] = array(
				'no_pengajuan' 	=> $kodeP,
				'id_material' 	=> $value['code_group'],
				'spec' 			=> $value['spec'],
				'nm_material' 	=> $value['material_name'],
				'category_awal' => $value['category_awal'],
				'satuan' 		=> $value['satuan'],
				'purchase' 		=> $value['request'],
				'tanggal' 		=> $value['tgl_dibutuhkan'],
				'spec_pr' 		=> $value['spec_pr'],
				'info_pr' 		=> $value['info_pr'],
				'price_from_supplier' 		=> $value['price_pr'],
				'in_gudang' 		=> $in_gudang,
			);
		}

		// print_r($ArrSaveHeader);
		// print_r($ArrSaveDetail);
		// exit;
		
		$this->db->trans_start();
		if(!empty($ArrSaveHeader)){
			$this->db->insert_batch('rutin_planning_header', $ArrSaveHeader);
			$this->db->insert_batch('rutin_planning_detail', $ArrSaveDetail);
		}
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
  			history('Save pengajuan rutin pr');
  		}
  		echo json_encode($Arr_Data);
	}

	public function get_json_warehouse_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_warehouse_rutin(
			$requestData['inventory'],
			$requestData['in_gudang'],
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

		$id_gudang = getGudangIndirect();
		$id_gudang_P = null;
		if($requestData['in_gudang'] == 'project'){
			$id_gudang = getGudangProject();
			$id_gudang_P = $id_gudang;
		}

		$GET_KEBUTUHAN_PER_MONTH = get_kebutuhanPerMonthGudang($id_gudang_P);
		$GET_WAREHOUSE_STOCK = get_warehouseStockProjectExclude($id_gudang_P);
		
		foreach($query->result_array() as $row){
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if($asc_desc == 'asc'){
				$nomor = $urut1 + $start_dari;
			}
			if($asc_desc == 'desc'){
				$nomor = ($total_data - $start_dari) - $urut2;
			}
			
			$tgl_now = date('Y-m-d');
			// $tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));

			$tgl_next_month = (!empty($row['tgl_dibutuhkan']))?$row['tgl_dibutuhkan']:date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
			$spec_pr = (!empty($row['spec_pr']))?$row['spec_pr']:'';
			$info_pr = (!empty($row['info_pr']))?$row['info_pr']:'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['code_group'].' - '.$row['material_name']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['spec']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['category_type']))."</div>";
			$STOCK_WRH		= (!empty($GET_WAREHOUSE_STOCK[$row['code_group']]))?$GET_WAREHOUSE_STOCK[$row['code_group']]:0;
			$stock_oke 		= (!empty($STOCK_WRH))?number_format($STOCK_WRH):'-';
			$stock_oke2 	= (!empty($STOCK_WRH))?$STOCK_WRH:0;
			$nestedData[]	= "<div align='right'><span id='stockNow_".$nomor."'>".$stock_oke."</span></div>";
			
			$kebutuhnMonth 	= (!empty($GET_KEBUTUHAN_PER_MONTH[$row['code_group']]['kebutuhan']))?$GET_KEBUTUHAN_PER_MONTH[$row['code_group']]['kebutuhan']:0;
			$nestedData[]	= "<div align='right'>".number_format($kebutuhnMonth)."</div>";
			$nestedData[]	= "<div align='right'><span id='maxstockNow_".$nomor."'>".number_format(($kebutuhnMonth * 1.5))."</span></div>";
			$nestedData[]	= "<div align='right'><span id='moqNow_".$nomor."'>".number_format($row['min_order'])."</span></div>";
			$purchase = ($kebutuhnMonth * 1.5) - $stock_oke2;
			$purchase2x = ($purchase < 0)?0:$purchase;
			$purchase2 = (!empty($row['request']))?$row['request']:$purchase2x;

			$price_ref = (!empty($row['price_pr']))?$row['price_pr']:$row['price_supplier'];
			$total_price = $price_ref * $purchase2;
			
			$nestedData[]	= "<div align='right'>
									<input type='text' name='purchase_".$nomor."' id='purchase_".$nomor."' value='".number_format($purchase2,2)."' data-code_group='".$row['code_group']."' data-no='".$nomor."' class='form-control input-md text-right maskM changeSave' style='width:100%;'>
									<b><span class='text-danger' id='noted_".$nomor."'></span></b>
								</div><script type='text/javascript'>$('.maskM').autoNumeric('init', {mDec: '2', aPad: false});</script>";
			$nestedData[]	= "<div align='left'>
									<select id='satuan_".$nomor."' class='chosen_select form-control input-md'><option value='".$row['satuan']."'>".get_name('raw_pieces','kode_satuan','id_satuan',$row['satuan'])."</option></select>	
									<input type='hidden' name='tanggal_".$nomor."' id='tanggal_".$nomor."' data-code_group='".$row['code_group']."' data-no='".$nomor."' class='form-control input-md tgl changeSave' style='width:100%;' readonly value='".$tgl_next_month."'></div>";	
			$nestedData[]	= "<div align='left'><input type='text' name='spec_".$nomor."' id='spec_".$nomor."' data-code_group='".$row['code_group']."' data-no='".$nomor."' class='form-control input-md changeSave' style='width:100%;' placeholder='Spec' value='".$spec_pr."'></div>";	
			$nestedData[]	= "<div align='left'>
									<input type='text' name='info_".$nomor."' id='info_".$nomor."' data-code_group='".$row['code_group']."' data-no='".$nomor."' class='form-control input-md changeSave' style='width:100%;' placeholder='Info' value='".$info_pr."'></div>
									";	
			$nestedData[]	= "<div align='left'><input type='text' name='price_".$nomor."' id='price_".$nomor."' data-code_group='".$row['code_group']."' data-no='".$nomor."' readonly class='form-control input-md text-right' style='width:100%;' placeholder='Price Ref.' value='".number_format($price_ref)."'></div>";	
			$nestedData[]	= "<div align='left'><input type='text' name='tprice_".$nomor."' id='tprice_".$nomor."' data-code_group='".$row['code_group']."' data-no='".$nomor."' readonly class='form-control input-md text-right' style='width:100%;' placeholder='Total Price' value='".number_format($total_price)."'></div>
									<style>.tgl{cursor:pointer;}</style>
									<script type='text/javascript'>
									$('.chosen_select').chosen({width: '100%'});
									$('.tgl').datepicker({
										dateFormat : 'yy-mm-dd',
										changeMonth: true, 
										changeYear: true,
										minDate : 0
									});
									</script>";	
			
			// $approve 		= "&nbsp;<button type='button' class='btn btn-sm btn-success save_pr' title='Save PR'  data-code_group='".$row['code_group']."' data-no='".$nomor."'><i class='fa fa-check'></i></button>";
			// $nestedData[]	= 	"<div align='center'>
			// 					".$approve."
			// 					</div>";
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

	public function get_query_json_warehouse_rutin($inventory, $in_gudang, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_inventory = "";
		if(!empty($inventory)){
			$where_inventory = " AND a.category_awal = '".$inventory."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_type,
				z.price_supplier
			FROM
				con_nonmat_new a  
				LEFT JOIN con_nonmat_category_awal b ON a.category_awal = b.id
				LEFT JOIN price_ref z ON a.code_group=z.code_group AND z.deleted_date IS NULL,
				(SELECT @row:=0) r
		    WHERE 1=1  
				AND a.code_group LIKE 'CN%' 
				AND b.id <> 9
				AND a.deleted='N'
				AND a.status='1'
				".$where_inventory."
			AND (
				a.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'material_name',
			2 => 'spec',
			3 => 'category'
		);

		$sql .= " ORDER BY a.id ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_pr_rutin(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_pr_rutin(
			$requestData['range'],
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
			$nestedData[]	= "<div align='center'>".$row['no_pengajuan']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_pr_group']."</div>";
			$nestedData[]	= "<div align='right' style='padding-right: 20px;'>".date('d F Y', strtotime($row['created_date']))."</div>";
			
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			
			$sql_pr = $this->db->select('SUM(qty) AS qty_pr')->get_where('tran_pr_detail', array('id_barang'=>$row['id_material']))->result();
			$qty_pr = (!empty($sql_pr))?$sql_pr[0]->qty_pr:0;
			
			$sql_pr = $this->db->select('SUM(qty_po) AS qty_po, SUM(qty_in) AS qty_in')->get_where('tran_po_detail', array('id_barang'=>$row['id_material']))->result();
			$qty_po = (!empty($sql_pr[0]->qty_po))?$sql_pr[0]->qty_po:0;
			$qty_in = (!empty($sql_pr[0]->qty_in))?$sql_pr[0]->qty_in:0;
			
			$nestedData[]	= "<div align='right'>".number_format($qty_pr,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($qty_po,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($qty_in,2)."</div>";
			
			
			$last_create 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$last_date 		= (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($last_create))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";
			$status 	= "WAITING APPROVAL";
			$warna 		= "green";
			if($row['sts_app'] == 'Y'){
				$status = "CLOSE";
				$warna = "red";
				if(!empty($row['no_pr'])){
					$status = "WAITING PO";
					$warna = "blue";
				}
			}
			if($row['sts_app'] == 'D'){
				$status = "REJECT";
				$warna = "red";
			}
			$nestedData[]	= "<div align='left' class='badge bg-".$warna."'>".$status."</div>";
			
			$edit			= "";
			$view			= "<button type='button'class='btn btn-sm btn-warning view_pr' title='Detail' data-kode='".$row['no_pengajuan']."' data-no='".$nomor."'><i class='fa fa-eye'></i></button>";
			if($row['sts_app'] == 'N'){
				$edit			= "&nbsp;<button type='button'class='btn btn-sm btn-primary edit_pr' title='Edit' data-kode='".$row['no_pengajuan']."' data-no='".$nomor."'><i class='fa fa-edit'></i></button>";
			}
			// $delete		= "&nbsp;<button type='button'class='btn btn-sm btn-danger app_pr' title='Delete PR' data-status='reject' data-id_material='".$row['no_ipp']."' data-no='".$nomor."'><i class='fa fa-close'></i></button>";
			$delete			= "";
			$nestedData[]	= "<div align='left'>".$view.$edit.$delete."</div>";
			
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

	public function query_data_json_pr_rutin($range, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_range = "";
        if($range > 0){
			$exP = explode(' - ', $range);
			$date_awal = date('Y-m-d', strtotime($exP[0]));
			$date_akhir = date('Y-m-d', strtotime($exP[1]));
			// echo $exP[0];exit;
            $where_range = "AND ((DATE(a.updated_date) BETWEEN '".$date_awal."' AND '".$date_akhir."') OR (DATE(a.created_date) BETWEEN '".$date_awal."' AND '".$date_akhir."')) ";
        }
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_material,
				b.spec,
				b.id_material
			FROM
				rutin_planning_header a
				LEFT JOIN rutin_planning_detail b ON a.no_pengajuan = b.no_pengajuan
		    WHERE 1=1 ".$where_range."
				AND (
				a.no_pengajuan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_pengajuan'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function pdf_report(){
		$tgl_awal 	= $this->uri->segment(3);
		$tgl_akhir 	= $this->uri->segment(4);
		
		$where_range = "";
        if($tgl_awal > 0){
			$date_awal = date('Y-m-d', strtotime($tgl_awal));
			$date_akhir = date('Y-m-d', strtotime($tgl_akhir));
			$where_range = "AND ((DATE(a.updated_date) BETWEEN '".$date_awal."' AND '".$date_akhir."') OR (DATE(a.created_date) BETWEEN '".$date_awal."' AND '".$date_akhir."')) ";
        }
		
		$sql = "SELECT 
					b.nm_material, 
					b.spec, 
					b.satuan, 
					b.tanggal, 
					SUM(b.purchase) AS purchase,
					c.stock,
					c.code_group
				FROM 
					rutin_planning_header a 
					LEFT JOIN rutin_planning_detail b ON a.no_pengajuan = b.no_pengajuan
					LEFT JOIN warehouse_rutin_stock c ON b.id_material = c.code_group 
				WHERE 1=1 ".$where_range." 
				
				GROUP BY id_material
				";
		
		$result = $this->db->query($sql)->result_array();
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' 	=> $printby,
			'tgl_awal'	=> $tgl_awal,
			'tgl_akhir'	=> $tgl_akhir,
			'result'	=> $result
		);
		
		history('Print approve pr material '.$tgl_awal.' / '.$tgl_akhir);
		$this->load->view('Print/print_pr_rutin_group', $data);
	}

}
