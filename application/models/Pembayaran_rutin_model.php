<?php
class Pembayaran_rutin_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('coa_model');
	}
	
	//================================================================================================================
	//===========================================BUDGET RUTIN=========================================================
	//================================================================================================================
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/master_rutin"; 
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$data = array(
			'title'			=> 'Indeks Of Master Pembayaran Rutin',
			'action'		=> 'index',
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Master Pembayaran Rutin');
		$this->load->view('Pembayaran/Rutin/index',$data);
	}
	
	public function add_master_pembayaran_rutin(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			$dept 			= $data['uri'];
			$Detail 		= $data['detail'];
			$Ym				= date('ym');
			
			$ArrDetail	= array();
			$ArrUpdate	= array();
			foreach($Detail AS $val => $valx){
					if(!empty($valx['id'])){
						$biaya		= str_replace(',','',$valx['biaya']);
						$ArrUpdate[$val]['id'] 					= $valx['id'];
						$ArrUpdate[$val]['department'] 			= $dept;
						$ArrUpdate[$val]['post_coa'] 			= $valx['post_coa'];
						$ArrUpdate[$val]['nama_barang'] 		= strtolower($valx['nama_barang']);
						$ArrUpdate[$val]['type_bayar'] 			= $valx['type_bayar'];
						$ArrUpdate[$val]['jadwal_bayar_bulan'] 	= $valx['jadwal_bayar_bulan'];
						$ArrUpdate[$val]['jadwal_bayar_tahun'] 	= $valx['jadwal_bayar_tahun'];
						$ArrUpdate[$val]['biaya']				= $biaya;
						$ArrUpdate[$val]['baseline'] 			= strtolower($valx['baseline']);
						$ArrUpdate[$val]['created_by'] 			= $data_session['ORI_User']['username'];
						$ArrUpdate[$val]['created_date'] 		= $dateTime;
					}
					if(empty($valx['id'])){
						$biaya		= str_replace(',','',$valx['biaya']);
						$ArrDetail[$val]['department'] 			= $dept;
						$ArrDetail[$val]['post_coa'] 			= $valx['post_coa'];
						$ArrDetail[$val]['nama_barang'] 		= strtolower($valx['nama_barang']);
						$ArrDetail[$val]['type_bayar'] 			= $valx['type_bayar'];
						$ArrDetail[$val]['jadwal_bayar_bulan'] 	= $valx['jadwal_bayar_bulan'];
						$ArrDetail[$val]['jadwal_bayar_tahun'] 	= $valx['jadwal_bayar_tahun'];
						$ArrDetail[$val]['biaya']				= $biaya;
						$ArrDetail[$val]['baseline'] 			= strtolower($valx['baseline']);
						$ArrDetail[$val]['created_by'] 			= $data_session['ORI_User']['username'];
						$ArrDetail[$val]['created_date'] 		= $dateTime;
					}
			}

			// print_r($ArrDetail);
			// print_r($ArrUpdate);
			// exit;
			
			$this->db->trans_start();
				if(!empty($ArrDetail)){
					$this->db->insert_batch('ms_budget_rutin', $ArrDetail);
				}
				if(!empty($ArrUpdate)){
					$this->db->update_batch('ms_budget_rutin', $ArrUpdate,'id');
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('Insert Master Budget Pembayaran '.$dept);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$dept = $this->uri->segment(3);
			$view = $this->uri->segment(4);
			
			$data_result = $this->db->query("SELECT * FROM ms_budget_rutin WHERE departement='".$dept."' AND deleted='N'")->result_array();
			$type_bayar = $this->db->query("SELECT * FROM list_help WHERE group_by='type_bayar'")->result_array();
			$datacoa 	= $this->db->query("SELECT a.coa, b.nama FROM sentralsistem.coa_category a LEFT JOIN gl.COA b ON a.coa = b.no_perkiraan WHERE a.tipe='RUTIN' ")->result_array();
		
		
			$data = array(
				'title'		=> 'Add Master Pembayaran Rutin '.get_name('department','nm_dept','id',$dept),
				'action'	=> 'index',
				'uri'		=> $dept,
				'type_bayar'=> $type_bayar,
				'datacoa'	=> $datacoa,
				'view'		=> $view,
				'data'		=> $data_result
			);
			$this->load->view('Pembayaran/Rutin/add_master_pembayaran_rutin',$data);
		}
	}
	
	public function get_add(){
		$id 		= $this->uri->segment(3);
		$dept 		= $this->input->post('dept');
		
		$type_bayar = $this->db->query("SELECT * FROM list_help WHERE group_by='type_bayar'")->result_array();
		$datacoa 	= $this->db->query("SELECT a.coa, b.nama FROM sentralsistem.coa_category a LEFT JOIN gl.COA b ON a.coa = b.no_perkiraan WHERE a.tipe='RUTIN' ")->result_array();
		
		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][post_coa]' data-no='".$id."' class='chosen_select form-control input-sm'>";
				foreach($datacoa AS $val => $valx){
					$d_Header .= "<option value='".$valx['coa']."'>".strtoupper($valx['coa'])." - ".strtoupper($valx['nama'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='detail[".$id."][nama_barang]' id='spec_".$id."' class='form-control input-md' placeholder='Nama Barang/Jasa'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][type_bayar]' data-no='".$id."' class='chosen_select form-control input-sm chType'>";
				foreach($type_bayar AS $val => $valx){
					$d_Header .= "<option value='".$valx['name']."'>".strtoupper($valx['name'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][jadwal_bayar_bulan]' id='jadwal_bayar_bulan_".$id."' class='chosen_select form-control input-sm'>";
				for($a=1;$a<=28;$a++){
					$d_Header .= "<option value='".$a."'>".strtoupper($a)."</option>";
				}
				$d_Header .= "</select>";
				$d_Header .= "<input type='text' name='detail[".$id."][jadwal_bayar_tahun]' id='jadwal_bayar_tahun_".$id."' class='form-control text-center input-md datepicker' readonly placeholder='Select Date'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='detail[".$id."][biaya]' class='form-control text-right input-md maskMoney' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='detail[".$id."][baseline]' id='baseline_".$id."' class='form-control input-md' placeholder='Baseline'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left' colspan='7'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'=> $d_Header,
				'id'	=> $id
		 ));
	}
	
	public function get_data_json_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/master_rutin";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_rutin(
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
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('department','nm_dept','id',$row['department']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['post_coa'].' - '.$row['nama'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nama_barang'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya'])."</div>";
				
				$edit	= "&nbsp;<a href='".site_url($this->uri->segment(1)).'/add_master_pembayaran_rutin/'.$row['department']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
			$nestedData[]	= "	<div align='left'>
                                    <a href='".site_url($this->uri->segment(1)).'/add_master_pembayaran_rutin/'.$row['department']."/view' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></a>
									".$edit."
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

	public function query_data_json_rutin($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nama
			FROM
				ms_budget_rutin a LEFT JOIN gl.COA b ON a.post_coa=b.no_perkiraan,
				(SELECT @row:=0) r
		    WHERE deleted='N' AND (
				a.departement LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.post_coa LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'departement',
			2 => 'post_coa',
			3 => 'nama',
			4 => 'biaya'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function delete_permanent(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		// print_r($data); exit;
		//header
		// $id 			= $data['id'];
		// $uri 			= $data['uri'];
		
		$id 			= $this->uri->segment(3);
		$uri 			= $this->uri->segment(4);
		// echo $uri; exit;
		$ArrDelete	= array(
			'deleted' => 'Y',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		);
		

		// print_r($ArrDelete);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id',$id);
			$this->db->update('ms_budget_rutin', $ArrDelete);
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Insert data failed. Please try again later ...',
				'status'	=> 0,
				'uri'		=> $uri
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Insert data success. Thanks ...',
				'status'	=> 1,
				'uri'		=> $uri
			);
			history('Delete master budget rutin pembayaran '.$id);
		}
		echo json_encode($Arr_Kembali);
	}
	
	//================================================================================================================
	//================================PERMINTAAN PEMBAYARAN RUTIN=====================================================
	//================================================================================================================
	public function payment_request_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/payment_request_rutin"; 
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$list_dept = $this->db->query("SELECT department AS id FROM view_department_payment_rutin GROUP BY department")->result_array();
		$approve = $this->uri->segment(3);
		$data = array(
			'title'			=> 'Indeks Of Permintaan Pembayaran Rutin',
			'action'		=> 'index',
			'list_dept'		=> $list_dept,
			'akses_menu'	=> $Arr_Akses,
			'approve'		=> $approve
		);
		history('View Data Permintaan Pembayaran Rutin');
		$this->load->view('Pembayaran/Rutin/Permintaan/payment_request_rutin',$data);
	}
	
	public function get_data_json_request_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/payment_request_rutin";
		$Arr_Akses			= getAcccesmenu($controller);
		$approve 			= $this->uri->segment(3);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request_rutin(
			
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
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['doc_number'])."</div>";
			$nestedData[]	= "<div align='left'>".date('d-M-Y', strtotime($row['doc_date']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('department','nm_dept','id',$row['department']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_approve'])."</div>";
			$last_by 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($last_by))."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($last_date))."</div>";
			
			$class = Color_status_custom($row['status'], 'bayar rutin');
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['status']."</span></div>";
			$ajukan = "";
			$view = "";
			$edit = "";
			$approvex = "";
			$payment = "";
			
			if($approve != 'payment'){
				$view = "<a href='".site_url($this->uri->segment(1)).'/add_request_pembayaran_rutin/'.$row['doc_number']."/view' class='btn btn-sm btn-warning' title='Look Data' data-role='qtip'><i class='fa fa-eye'></i></a>";
			}
			if($row['status'] == 'WAITING SUBMITTED'){
				$ajukan	= "&nbsp;<button type='button' class='btn btn-sm btn-success ajukan_request' title='Ajukan' data-doc_number='".$row['doc_number']."'><i class='fa fa-paper-plane-o'></i></a>";
				$edit	= "&nbsp;<a href='".site_url($this->uri->segment(1)).'/add_request_pembayaran_rutin/'.$row['doc_number']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}
			if($approve == 'approve'){
				$view = '';
				$approvex	= "&nbsp;<a href='".site_url($this->uri->segment(1)).'/add_request_pembayaran_rutin/'.$row['doc_number']."/approve' class='btn btn-sm btn-info' title='Approve Data' data-role='qtip'><i class='fa fa-check'></i></a>";
			}
			if($approve == 'payment'){
				$icon = 'fa-credit-card';
				$warna = 'info';
				if($row['payment'] == 'Y'){
					$icon = 'fa-eye';
					$warna = 'warning';
				}
				
				$payment	= "&nbsp;<a href='".site_url($this->uri->segment(1)).'/payment_pembayaran_rutin/'.$row['doc_number']."' class='btn btn-sm btn-".$warna."' title='Payment' data-role='qtip'><i class='fa ".$icon."'></i></a>";
			}
			$nestedData[]	= "	<div align='left'>
									".$view."
                                    ".$edit."
									".$ajukan."
									".$approvex."
									".$payment."
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

	public function query_data_json_request_rutin($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$approve 			= $this->uri->segment(3);
		$where = '';
		if($approve == 'approve'){
			$where = " AND a.status = 'WAITING APPROVE'";
		}
		if($approve == 'payment'){
			$where = " AND (a.status = 'WAITING PAYMENT' OR a.status = 'CLOSE')";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				payment_submission a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." AND  (
				a.doc_number LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'doc_number',
			2 => 'department',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add_request_pembayaran_rutin(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			$doc_number 	= $data['doc_number'];
			
			$status 		= (!empty($data['status']))?$data['status']:'';
			$reason 		= (!empty($data['reason']))?$data['reason']:'';
			$urix 			= (!empty($data['status']))?'approve':'';
			
			$statusX		= ($status == 'Y')?'WAITING PAYMENT':'REJECTED';
			
			$tanda 			= $data['tanda'];
			$dept 			= $data['uri'];
			$Detail 		= $data['detail'];
			$Ym				= date('ym');
			$tanda_gabung	= (!empty($data['status']))?'Approved':'Edit';
			if($tanda != 'RPY'){
				$srcMtr			= "SELECT MAX(doc_number) as maxP FROM payment_submission WHERE doc_number LIKE 'RPY".$Ym."%' ";
				$numrowMtr		= $this->db->query($srcMtr)->num_rows();
				$resultMtr		= $this->db->query($srcMtr)->result_array();
				$angkaUrut2		= $resultMtr[0]['maxP'];
				$urutan2		= (int)substr($angkaUrut2, 7, 3);
				$urutan2++;
				$urut2			= sprintf('%03s',$urutan2);
				$doc_number	= 	"RPY".$Ym.$urut2;
				
				$tanda_gabung	= (!empty($data['status']))?'Approved':'Insert';
			}
			
			$ArrInsert	= array();
			$ArrUpdate	= array();
			$ArrApprove	= array();
			$nilai_total = 0;
			foreach($Detail AS $val => $valx){
				$nilai_total += str_replace(',','',$valx['biaya']);
				if(!empty($valx['id'])){
					$nilai_bayar	= str_replace(',','',$valx['biaya']);
					$ArrUpdate[$val]['id'] 			= $valx['id'];
					$ArrUpdate[$val]['id_budget'] 			= $valx['id_budget'];
					$ArrUpdate[$val]['doc_number'] 			= $doc_number;
					$ArrUpdate[$val]['post_coa'] 			= get_name('ms_budget_rutin','post_coa','id',$valx['id_budget']);
					$ArrUpdate[$val]['nama_barang'] 		= strtolower($valx['nama_barang']);
					$ArrUpdate[$val]['jadwal_bayar'] 		= $valx['jadwal_bayar'];
					$ArrUpdate[$val]['biaya']				= get_name('ms_budget_rutin','biaya','id',$valx['id_budget']);
					$ArrUpdate[$val]['nilai_bayar']			= $nilai_bayar;
					$ArrUpdate[$val]['keterangan'] 			= strtolower($valx['keterangan']);
				}
				
				if(empty($valx['id'])){
					$nilai_bayar	= str_replace(',','',$valx['biaya']);
					$ArrInsert[$val]['id_budget'] 			= $valx['id_budget'];
					$ArrInsert[$val]['doc_number'] 			= $doc_number;
					$ArrInsert[$val]['post_coa'] 			= get_name('ms_budget_rutin','post_coa','id',$valx['id_budget']);
					$ArrInsert[$val]['nama_barang'] 		= strtolower($valx['nama_barang']);
					$ArrInsert[$val]['jadwal_bayar'] 		= $valx['jadwal_bayar'];
					$ArrInsert[$val]['biaya']				= get_name('ms_budget_rutin','biaya','id',$valx['id_budget']);
					$ArrInsert[$val]['nilai_bayar']			= $nilai_bayar;
					$ArrInsert[$val]['keterangan'] 			= strtolower($valx['keterangan']);
				}
				
				if(!empty($valx['id'])){
					$nilai_bayar	= str_replace(',','',$valx['biaya']);
					$ArrApprove[$val]['id'] 			= $valx['id'];
					$ArrApprove[$val]['nilai_bayar']	= $nilai_bayar;
				}
			}
			
			$ArrHeader = array(
				'tipe' => 'rutin',
				'department' => $dept,
				'doc_number' => $doc_number,
				'doc_date' => date('Y-m-d'),
				'coa_bank' => NULL,
				'total_nilai' => $nilai_total,
				'coa_ppn' => NULL,
				'nilai_ppn' => NULL,
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => $dateTime
			);
			
			$ArrHeader2 = array(
				'status' 		=> $statusX,
				'reason' 		=> $reason,
				'total_nilai_approve' => ($status == 'Y')?$nilai_total:0,
				'approved_by' 	=> $data_session['ORI_User']['username'],
				'approved_date' => $dateTime
			);
			
			// print_r($ArrHeader);
			// print_r($ArrInsert);
			// print_r($ArrUpdate);
			// exit;
			
			$this->db->trans_start();
				if(empty($data['status'])){
					if($tanda != 'RPY'){
						$this->db->insert('payment_submission', $ArrHeader);
					}
					if($tanda == 'RPY'){
						$this->db->where('doc_number', $doc_number);
						$this->db->update('payment_submission', $ArrHeader);
					}
					
					if(!empty($ArrInsert)){
						$this->db->insert_batch('payment_submission_detail', $ArrInsert);
					}
					if(!empty($ArrUpdate)){
						$this->db->update_batch('payment_submission_detail', $ArrUpdate,'id');
					}
				}
				if(!empty($data['status'])){
					$this->db->where('doc_number', $doc_number);
					$this->db->update('payment_submission', $ArrHeader2);
					
					if(!empty($ArrApprove)){
						$this->db->update_batch('payment_submission_detail', $ArrApprove,'id');
					}
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data failed. Please try again later ...',
					'status'	=> 0,
					'tanda'		=> $urix
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data success. Thanks ...',
					'status'	=> 1,
					'tanda'		=> $urix
				);
				history($tanda_gabung.' pengajuan pembayaran rutin '.$doc_number);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$dept 		= $this->uri->segment(3);
			$view 		= $this->uri->segment(4);
			$approve 	= $this->uri->segment(4);
			
			$tanda = substr($dept,0,3);
			// echo $tanda; exit;
			if($tanda == 'RPY'){
				$sql 			= "SELECT * FROM payment_submission_detail WHERE doc_number='".$dept."'";
				$data_result 	= $this->db->query($sql)->result_array();
				$doc_number 	= $dept;
				$judul 		= $dept;
				$department = get_name('payment_submission','department','doc_number',$dept);
			}
			if($tanda != 'RPY'){
				$sql 			= "SELECT * FROM ms_budget_rutin WHERE departement='".$dept."' AND (type_bayar='bulan' OR MONTH(jadwal_bayar_tahun) = MONTH(NOW()) )";
				$data_result 	= $this->db->query($sql)->result_array();
				$judul 			= get_name('department','nm_dept','id',$dept);
				$department = $dept;
				$doc_number = '';
			}
			// echo $sql;
			// print_r($data_result);
		
			$data = array(
				'title'		=> 'Add Request Pembayaran Rutin '.$judul,
				'action'	=> 'index',
				'uri'		=> $department,
				'tanda'		=> $tanda,
				'view'		=> $view,
				'doc_number'		=> $doc_number,
				'data'		=> $data_result,
				'approve'	=> $approve
			);
			
			$this->load->view('Pembayaran/Rutin/Permintaan/add_request_pembayaran_rutin',$data);
		}
	}
	
	public function ajukan_pembayaran(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		
		$id 			= $this->uri->segment(3);
		$ArrDelete	= array(
			'status' => 'WAITING APPROVE',
			'ajukan_by' => $data_session['ORI_User']['username'],
			'ajukan_date' => date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
			$this->db->where('doc_number',$id);
			$this->db->update('payment_submission', $ArrDelete);
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
			history('Mengajukan pembayaran rutin '.$id);
		}
		echo json_encode($Arr_Kembali);
	}
	
	public function payment_pembayaran_rutin(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			$doc_number 	= $data['doc_number'];
			
			// print_r($data);
			// exit;
			
			$detail 		= $data['detail'];
			if(!empty($data['detail_add'])){
				$detail_add 	= $data['detail_add'];
			}
			$total_payment	= str_replace(',','',$data['total_payment']);
			$ppn			= str_replace(',','',$data['ppn']);
			$sub_payment	= str_replace(',','',$data['sub_payment']);
			$ppn_type 		= ($data['ppn_type'] == 'ppn')?10:0;
			$coa 			= $data['coa'];
			
			$ArrInsert	= array();
			$ArrUpdate	= array();
			$nilai_total = 0;
			foreach($detail AS $val => $valx){
				$payment		= str_replace(',','',$valx['payment']);
				$nilai_total 	+= $payment;
				$ArrUpdate[$val]['id'] 				= $valx['id'];
				$ArrUpdate[$val]['nilai_payment']	= $payment;
			}
			
			if(!empty($data['detail_add'])){
				foreach($detail_add AS $val => $valx){
					$ArrInsert[$val]['doc_number'] 		= $doc_number;
					$ArrInsert[$val]['post_coa'] 		= $valx['post_coa'];
					$ArrInsert[$val]['debit'] 			= str_replace(',','',$valx['debit']);
					$ArrInsert[$val]['kredit'] 			= str_replace(',','',$valx['kredit']);
					$ArrInsert[$val]['keterangan'] 		= strtolower($valx['keterangan']);
				}
			}
			
			$ArrHeader = array(
				'coa_bank' 			=> $coa,
				'coa_ppn' 			=> $ppn_type,
				'nilai_ppn' 		=> $ppn,
				'total_payment_ppn' => $sub_payment,
				'total_payment' 	=> $total_payment,
				'status'			=> 'CLOSE',
				'payment'			=> 'Y',
				'payment_by' 		=> $data_session['ORI_User']['username'],
				'payment_date' 		=> $dateTime
			);
			
			// print_r($ArrHeader);
			// print_r($ArrInsert);
			// print_r($ArrUpdate);
			// exit;
			
			$this->db->trans_start();
				$this->db->where('doc_number', $doc_number);
				$this->db->update('payment_submission', $ArrHeader);
				
				if(!empty($ArrInsert)){
					$this->db->insert_batch('payment_submission_detail_add', $ArrInsert);
				}
				if(!empty($ArrUpdate)){
					$this->db->update_batch('payment_submission_detail', $ArrUpdate,'id');
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('Pembayaran rutin '.$doc_number);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$dept 		= $this->uri->segment(3);
			
			$sql_h 			= "SELECT * FROM payment_submission WHERE doc_number='".$dept."' LIMIT 1";
			$data_header 	= $this->db->query($sql_h)->result();
			
			$sql 			= "SELECT * FROM payment_submission_detail WHERE doc_number='".$dept."'";
			$data_result 	= $this->db->query($sql)->result_array();
			
			$sql_a 			= "SELECT * FROM payment_submission_detail_add WHERE doc_number='".$dept."'";
			$data_add 		= $this->db->query($sql_a)->result_array();
			$doc_number 	= $dept;
			$judul 			= $dept;
			$department 	= get_name('payment_submission','department','doc_number',$dept);
			$data_coa		= $this->coa_model->GetCoa();
			
			// echo $sql;
			// print_r($data_result);
		
			$data = array(
				'title'			=> 'Pembayaran Rutin <b>'.$judul.' - '.get_name('department','nm_dept','id',$department).'</b>',
				'action'		=> 'index',
				'uri'			=> $department,
				'doc_number'	=> $doc_number,
				'data'			=> $data_result,
				'data_header'	=> $data_header,
				'data_add'		=> $data_add,
				'data_coa'		=> $data_coa
			);
			
			$this->load->view('Pembayaran/Rutin/payment_pembayaran_rutin',$data);
		}
	}
	
	public function get_add_payment(){
		$id 		= $this->uri->segment(3);
		$data_coa		= $this->coa_model->GetCoa();
		
		$d_Header = "";
		$d_Header .= "<tr class='header2_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_add[".$id."][post_coa]' data-no='".$id."' class='chosen_select form-control input-sm'>";
				$d_Header .= "<option value='0'>Select COA</option>";
				foreach($data_coa AS $val => $valx){
					$d_Header .= "<option value='".$valx['no_perkiraan']."'>".strtoupper($valx['nama_perkiraan'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='detail_add[".$id."][debit]' id='debit_".$id."' class='form-control text-right input-md maskMoney debit' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='detail_add[".$id."][kredit]' id='kredit_".$id."' class='form-control text-right input-md maskMoney kredit' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='detail_add[".$id."][keterangan]' id='keterangan2_".$id."' class='form-control input-md' placeholder='Keterangan'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left' colspan='5'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'=> $d_Header,
				'id'	=> $id
		 ));
	}
	
	
	
}