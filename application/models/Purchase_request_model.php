<?php
class Purchase_request_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	//APPROVAL PR
	public function index_approval_pr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Indeks Of Approval PR',
		  'action'			=> 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'		=> $Arr_Akses
		);
		history('View Approval PR');
		$this->load->view('Purchase_request/approve_pr',$data);
	}
	
	public function index_approval_pr_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$GET_SO_NUMBER = $this->db->get('so_number')->result_array();
		$ArrGetSO = [];
		foreach($GET_SO_NUMBER AS $val => $value){
			$ArrGetSO[$value['id_bq']] = $value['so_number'];
		}

		// print_r($ArrGetSO);

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_pr 			= $this->db->order_by('created_date','desc')->get_where('approval_pr', array('sts_app'=>'N'))->result_array();
		$data = array(
		  'title'			=> 'Indeks Of Approval PR',
		  'action'			=> 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'		=> $Arr_Akses,
		  'data_pr'			=> $data_pr,
		  'ArrGetSO'		=> $ArrGetSO
		);
		history('View Approval PR');
		$this->load->view('Purchase_request/approve_pr_new',$data);
	}
	
	public function modal_detail_pr(){
		$no_ipp = $this->uri->segment(3);
		$tanda = $this->uri->segment(4);
		
		$id_user = $this->input->post('id_user');
		$user = get_name('users','username','id_user',$id_user);
		
		$no_ipp2 	= "Based On Project ".$no_ipp;
		$kebutuhan 	= "Project";
		$sql		= "SELECT *, SUM(purchase) AS qty_request, MAX(moq) AS moq_m FROM warehouse_planning_detail WHERE no_ipp='".$no_ipp."' AND purchase > 0 GROUP BY id_material";
		if($tanda == 'P'){
			$no_ipp2 = "Re-Order Point ".date('d-m-Y', strtotime($no_ipp));
			$kebutuhan = "Pemenuhan Stock Material";
			$sql		= "	SELECT 
								a.*, 
								SUM(a.purchase) AS qty_request, 
								MAX(a.moq) AS moq_m 
							FROM 
								warehouse_planning_detail a 
								LEFT JOIN warehouse_planning_header b ON a.no_ipp=b.no_ipp 
							WHERE 
								a.no_ipp LIKE '".$tanda."%' 
								AND DATE(b.created_date) = '".date('Y-m-d', strtotime($no_ipp))."' 
								AND b.created_by='".$user."' 
								AND a.purchase > 0 
							GROUP BY a.id_material, a.sts_app ";
		}
		$result = $this->db->query($sql)->result_array();
		
		$non_frp = $this->db->get_where('warehouse_planning_detail_acc', array('no_ipp'=>$no_ipp,'purchase >'=>0))->result_array();

		$data = array(
		  'no_ipp'		=> $no_ipp2,
		  'kebutuhan'	=> $kebutuhan,
		  'non_frp'		=> $non_frp,
		  'result'		=> $result
		);
		$this->load->view('Purchase_request/modal_detail_pr',$data);
	}
	
	public function print_detail_pr(){
		$no_ipp = $this->uri->segment(3);
		$tanda = $this->uri->segment(4);
		
		$id_user = $this->uri->segment(5);
		$user = get_name('users','username','id_user',$id_user);
		
		$no_ipp2 	= "Based On Project ".$no_ipp;
		$kebutuhan 	= "Project";
		// $sql		= "SELECT *, SUM(purchase) AS qty_request, MAX(moq) AS moq_m FROM warehouse_planning_detail WHERE no_ipp='".$no_ipp."' AND purchase > 0 GROUP BY id_material";
		$sql		= "	SELECT 
							a.*, 
							SUM(a.purchase) AS qty_request, 
							MAX(a.moq) AS moq_m,
							c.qty_stock,
							c.qty_booking
						FROM 
							warehouse_planning_detail a
							LEFT JOIN warehouse_stock c ON a.id_material = c.id_material
						WHERE 
							a.no_ipp='".$no_ipp."' 
							AND a.purchase > 0 
							AND a.sts_app = 'N' 
							AND c.id_gudang = '2'
						GROUP BY 
							a.id_material";
		if($tanda == 'P'){
			$no_ipp2 = "Re-Order Point ".date('d-m-Y', strtotime($no_ipp));
			$kebutuhan = "Pemenuhan Stock Material";
			$sql		= "	SELECT 
								a.*, 
								SUM(a.purchase) AS qty_request, 
								MAX(a.moq) AS moq_m,
								c.qty_stock,
								c.qty_booking
							FROM 
								warehouse_planning_detail a 
								LEFT JOIN warehouse_planning_header b ON a.no_ipp=b.no_ipp 
								LEFT JOIN warehouse_stock c ON a.id_material = c.id_material
							WHERE 
								a.no_ipp LIKE '".$tanda."%' 
								AND DATE(b.created_date) = '".date('Y-m-d', strtotime($no_ipp))."' 
								AND b.created_by='".$user."' 
								AND a.purchase > 0 
								AND a.sts_app != 'D'
								AND c.id_gudang = '2'
							GROUP BY a.id_material ";
		}
		// echo $sql; exit;
		$result = $this->db->query($sql)->result_array();
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$non_frp = $this->db->get_where('warehouse_planning_detail_acc', array('no_ipp'=>$no_ipp,'purchase >'=>0))->result_array();

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'no_ipp'		=> $no_ipp2,
			'kebutuhan'	=> $kebutuhan,
			'result'		=> $result,
			'non_frp'		=> $non_frp
		);
		
		history('Print approve pr material '.$no_ipp.' / '.$tanda);
		$this->load->view('Print/print_pr_approve', $data);
	}
	
	public function modal_approve_pr(){
		$no_ipp = $this->uri->segment(3);
		$tanda = $this->uri->segment(4);
		
		$id_user = $this->input->post('id_user');
		$user = get_name('users','username','id_user',$id_user);
		
		$no_ipp2 	= "Based On Project ".$no_ipp;
		$kebutuhan 	= "Project";
		$sql		= "	SELECT 
							a.*, 
							SUM(a.purchase) AS qty_request, 
							MAX(a.moq) AS moq_m,
							c.qty_stock,
							c.qty_booking
						FROM 
							warehouse_planning_detail a
							LEFT JOIN warehouse_stock c ON a.id_material = c.id_material
						WHERE 
							a.no_ipp='".$no_ipp."' 
							AND a.purchase > 0 
							AND a.sts_app = 'N' 
							AND (c.id_gudang = '1' OR c.id_gudang = '2')
						GROUP BY 
							a.id_material";
		if($tanda == 'P'){
			$no_ipp2 = "Re-Order Point ".date('d-m-Y', strtotime($no_ipp));
			$kebutuhan = "Pemenuhan Stock Material";
			$sql		= "	SELECT 
								a.*, 
								SUM(a.purchase) AS qty_request, 
								MAX(a.moq) AS moq_m,
								c.qty_stock,
								c.qty_booking
							FROM 
								warehouse_planning_detail a 
								LEFT JOIN warehouse_planning_header b ON a.no_ipp=b.no_ipp 
								LEFT JOIN warehouse_stock c ON a.id_material = c.id_material
							WHERE 
								a.no_ipp LIKE '".$tanda."%' 
								AND DATE(b.created_date) = '".date('Y-m-d', strtotime($no_ipp))."' 
								AND b.created_by='".$user."' 
								AND a.purchase > 0 
								AND a.sts_app = 'N' 
								AND (c.id_gudang = '1' OR c.id_gudang = '2')
							GROUP BY a.id_material ";
		}
		// echo $sql;
		$result = $this->db->query($sql)->result_array();
		$non_frp = $this->db->select('*, SUM(purchase) AS qty_request')->group_by('id_material','ASC')->get_where('warehouse_planning_detail_acc', array('no_ipp'=>$no_ipp,'purchase >'=>0,'sts_app'=>'N'))->result_array();

		$tgl_butuh = '';
		if(!empty($result) OR !empty($non_frp)){
			if(!empty($result)){
				$tgl_butuh = $result[0]['tanggal'];
			}
			if(!empty($non_frp)){
				$tgl_butuh = $non_frp[0]['tanggal'];
			}
		}

		$data = array(
		  'no_ipp'		=> $no_ipp2,
		  'no_ipp2'		=> $no_ipp,
		  'kebutuhan'	=> $kebutuhan,
		  'result'		=> $result,
		  'non_frp'		=> $non_frp,
		  'tanda'		=> $tanda,
		  'project'		=> get_name('production','project','no_ipp',$no_ipp),
		  'tgl_butuh'	=> $tgl_butuh,
		  'id_user'	=> $id_user
		);
		$this->load->view('Purchase_request/modal_approve_pr',$data);
	}
	
	public function save_approve_pr(){
		$data = $this->input->post();
		$id_material 	= $data['id_material'];
		$qty_request 	= $data['qty_request'];
		$qty_revisi 	= $data['qty_revisi'];
		$tanggal 		= $data['tanggal'];
		$moq 			= $data['moq'];
		$reorder_point 	= $data['reorder_point'];
		$sisa_avl 		= $data['sisa_avl'];
		$book_per_month = $data['book_per_month'];
		$status 		= $data['status'];
		
		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_material_pr_header WHERE no_pr LIKE 'PR".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2); 
		$kodeP			= "PR".$Ym.$urut2;
		
		$get_material = $this->db->query("SELECT * FROM raw_materials WHERE id_material='".$id_material."' LIMIT 1")->result();
		
		$ArrHeader = array(
			'no_pr' 		=> $kodeP,
			'sts_ajuan'		=> ($status == 'approve')?'OPN':'REJ',
			'total_material'=> (!empty($qty_revisi))?$qty_revisi:$qty_request,
			'created_by' 	=> $this->session->userdata['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrDetail = array(
			'no_pr' 		=> $kodeP,
			'id_material' 	=> $get_material[0]->id_material,
			'idmaterial' 	=> $get_material[0]->idmaterial,
			'nm_material' 	=> $get_material[0]->nm_material,
			'qty_request' 	=> $qty_request,
			'qty_revisi' 	=> (!empty($qty_revisi))?$qty_revisi:$qty_request,
			'moq' 			=> $moq,
			'reorder_point' => $reorder_point,
			'sisa_avl' 		=> $sisa_avl,
			'book_per_month'=> $book_per_month,
			'tanggal' 		=> $tanggal,
			'created_by' 	=> $this->session->userdata['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrUpdate = array(
			'no_pr' 		=> $kodeP,
			'sts_app'		=> ($status == 'approve')?'Y':'D',
			'sts_app_by' 	=> $this->session->userdata['ORI_User']['username'],
			'sts_app_date' 	=> date('Y-m-d H:i:s')
		);
		
		
		// echo $kodeP."<br>";
		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit; 
		
		$this->db->trans_start();
  			$this->db->insert('tran_material_pr_header', $ArrHeader);
  			$this->db->insert('tran_material_pr_detail', $ArrDetail);
			
			$this->db->where('id_material', $id_material);
			$this->db->update('warehouse_planning_detail', $ArrUpdate);
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
  			history('Approve PR '.$kodeP);
  		}
  		echo json_encode($Arr_Data);
	}
	
	public function reject_sebagian_pr_new(){
		$data 			= $this->input->post();
		$id 			= $data['id'];
		$id_material 	= $data['id_material'];
		$no_ipp 		= $data['no_ipp'];
		$tanda 			= $data['tanda'];
		$id_user 		= $data['id_user'];
		
		$detail	= $this->db->query("SELECT no_ipp, created_date FROM warehouse_planning_detail WHERE id='".$id."'")->result();
		$tanda 		= substr($detail[0]->no_ipp,0,1);
		$no_ipp 	= $detail[0]->no_ipp;
		if($tanda == 'P'){
			$no_ipp = date('Y-m-d', strtotime($detail[0]->created_date));
		}
		
		$ArrUpdate = array(
			'sts_app'		=> 'D',
			'sts_app_by' 	=> $this->session->userdata['ORI_User']['username'],
			'sts_app_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('warehouse_planning_detail', $ArrUpdate);
			if(get_status_approve_pr($id) < 1){
				if($tanda == 'P'){
					$this->db->where("no_ipp LIKE '".$tanda."%' AND DATE(created_date) = ", $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdate);
				}
				if($tanda != 'P'){
					$this->db->where('no_ipp', $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdate);
				}
			}
  		$this->db->trans_complete();
  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  		}
  		else{
  			$this->db->trans_commit();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  			history('Reject sebagian Production Planning '.$id.' / '.$id_material);
  		}
  		echo json_encode($Arr_Data);
	}
	
	public function reject_sebagian_pr_new_acc(){
		$data 			= $this->input->post();
		$id 			= $data['id'];
		$id_material 	= $data['id_material'];
		$no_ipp 		= $data['no_ipp'];
		$tanda 			= $data['tanda'];
		$id_user 		= $data['id_user'];
		
		$detail	= $this->db->query("SELECT no_ipp, created_date FROM warehouse_planning_detail_acc WHERE id='".$id."'")->result();
		$tanda 		= substr($detail[0]->no_ipp,0,1);
		$no_ipp 	= $detail[0]->no_ipp;
		if($tanda == 'P'){
			$no_ipp = date('Y-m-d', strtotime($detail[0]->created_date));
		}
		
		$ArrUpdate = array(
			'sts_app'		=> 'D',
			'sts_app_by' 	=> $this->session->userdata['ORI_User']['username'],
			'sts_app_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('warehouse_planning_detail_acc', $ArrUpdate);
			if(get_status_approve_pr_acc($id) < 1){
				if($tanda == 'P'){
					$this->db->where("no_ipp LIKE '".$tanda."%' AND DATE(created_date)", $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdate);
				}
				if($tanda != 'P'){
					$this->db->where('no_ipp', $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdate);
				}
			}
  		$this->db->trans_complete();
  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  		}
  		else{
  			$this->db->trans_commit();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  			history('Reject accessories sebagian PR '.$id.' / '.$id_material);
  		}
  		echo json_encode($Arr_Data);
	}
	
	public function approve_sebagian_pr_new(){
		$data 			= $this->input->post();
		$id 			= $data['id'];
		$id_material 	= $data['id_material'];
		$no_ipp 		= $data['no_ipp'];
		$tanda 			= $data['tanda'];
		$id_user 		= $data['id_user'];
		$qty_revisi 	= str_replace(',','',$data['qty_revisi']);
		$status 		= 'approve';

		//UPDATE MATERIAL PLANNING
		$detail		= $this->db->query("SELECT no_ipp, created_date FROM warehouse_planning_detail WHERE id='".$id."'")->result();
		$tanda 		= substr($detail[0]->no_ipp,0,1);
		$no_ipp 	= $detail[0]->no_ipp;

		// echo $tanda.'<br>';
		// echo $no_ipp.'<br>';
		// print_r($detail);
		// exit;
		$ch_pr_header = $this->db->get_where('warehouse_planning_header', array('no_ipp'=>$no_ipp))->result();

		if($tanda == 'P'){
			$no_ipp = date('Y-m-d', strtotime($detail[0]->created_date));
			$ch_pr_header = $this->db->get_where('warehouse_planning_header', array('DATE(created_date)'=>$no_ipp))->result();
		}

		//INSERT KE PR
		$NO_PR = $ch_pr_header[0]->no_pr;
		if(empty($ch_pr_header[0]->no_pr)){
			$Ym = date('ym');
			$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_material_pr_header WHERE no_pr LIKE 'PR".$Ym."%' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 6, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2); 
			$NO_PR			= "PR".$Ym.$urut2;
		}
		// echo $NO_PR; exit;
		//detail pr
		$val = 0;
		$get_detail_plan = $this->db->get_where('warehouse_planning_detail', array('id'=>$id))->result();

		$qty_pr_app = (!empty($qty_revisi))?$qty_revisi:$get_detail_plan[0]->purchase;
		$ArrDetail[$val]['no_pr'] 			= $NO_PR;
		$ArrDetail[$val]['category'] 		= 'mat';
		$ArrDetail[$val]['id_material'] 	= $get_detail_plan[0]->id_material;
		$ArrDetail[$val]['idmaterial'] 		= $get_detail_plan[0]->idmaterial;
		$ArrDetail[$val]['nm_material'] 	= $get_detail_plan[0]->nm_material;
		$ArrDetail[$val]['qty_request'] 	= $get_detail_plan[0]->purchase;
		$ArrDetail[$val]['qty_revisi'] 		= $qty_pr_app;
		$ArrDetail[$val]['moq'] 			= $get_detail_plan[0]->moq;
		$ArrDetail[$val]['reorder_point'] 	= $get_detail_plan[0]->reorder_point;
		$ArrDetail[$val]['sisa_avl'] 		= $get_detail_plan[0]->sisa_avl;
		$ArrDetail[$val]['book_per_month']	= $get_detail_plan[0]->book_per_month;
		$ArrDetail[$val]['tanggal'] 		= $get_detail_plan[0]->tanggal;
		$ArrDetail[$val]['created_by'] 		= $this->session->userdata['ORI_User']['username'];
		$ArrDetail[$val]['created_date'] 	= date('Y-m-d H:i:s');

		//insert atau update header
		$get_header_PR = $this->db->get_where('tran_material_pr_header', array('no_pr'=>$NO_PR))->result();
		$total_pr_bef = (!empty($get_header_PR))?$get_header_PR[0]->total_material:0;

		$ArrHeaderPR = array(
			'total_material' 	=> $total_pr_bef + $qty_pr_app
		);

		if(empty($ch_pr_header[0]->no_pr)){
			$ArrHeaderPR = array(
				'no_pr' 			=> $NO_PR,
				'sts_ajuan' 		=> ($status == 'approve')?'OPN':'REJ',
				'total_material' 	=> $qty_pr_app,
				'created_by' 		=> $this->session->userdata['ORI_User']['username'],
				'created_date' 		=> date('Y-m-d H:i:s'),
			);
		}

		//update planning
		$ArrUpdate = array(
			'no_pr'			=> $NO_PR,
			'sts_app'		=> 'Y',
			'sts_app_by' 	=> $this->session->userdata['ORI_User']['username'],
			'sts_app_date' 	=> date('Y-m-d H:i:s')
		);

		$ArrUpdateHEad = array(
			'no_pr'			=> $NO_PR
		);

		// exit;
		$this->db->trans_start();
			//purchase request
			if(empty($ch_pr_header[0]->no_pr)){
				$this->db->insert('tran_material_pr_header', $ArrHeaderPR);
			}
			else{
				$this->db->where('no_pr',$NO_PR);
				$this->db->update('tran_material_pr_header', $ArrHeaderPR);
			}

			if(!empty($ArrDetail)){
				$this->db->insert_batch('tran_material_pr_detail', $ArrDetail);
			}

			//material planning
			$this->db->where('id', $id);
			$this->db->update('warehouse_planning_detail', $ArrUpdate);
			// if(get_status_approve_pr($id) < 1){
				if($tanda == 'P'){
					$this->db->where("no_ipp LIKE '".$tanda."%' AND DATE(created_date) = ", $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdateHEad);
				}
				if($tanda != 'P'){
					$this->db->where('no_ipp', $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdateHEad);
				}
			// }
  		$this->db->trans_complete();
  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  		}
  		else{
  			$this->db->trans_commit();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  			history('Approve sebagian Production Planning '.$id.' / '.$id_material);
  		}
  		echo json_encode($Arr_Data);
	}
	
	public function approve_sebagian_pr_new_acc(){
		$data 			= $this->input->post();
		$id 			= $data['id'];
		$id_material 	= $data['id_material'];
		$no_ipp 		= $data['no_ipp'];
		$tanda 			= $data['tanda'];
		$id_user 		= $data['id_user'];
		$qty_revisi 	= str_replace(',','',$data['qty_revisi']);
		$status 		= 'approve';
		// echo $qty_revisi; exit;
		//UPDATE MATERIAL PLANNING
		
		$detail	= $this->db->query("SELECT no_ipp, created_date FROM warehouse_planning_detail_acc WHERE id='".$id."'")->result();
		$tanda 		= substr($detail[0]->no_ipp,0,1);
		$no_ipp 	= $detail[0]->no_ipp;
		if($tanda == 'P'){
			$no_ipp = date('Y-m-d', strtotime($detail[0]->created_date));
		}
		
		//INSERT KE PR
		$ch_pr_header = $this->db->get_where('warehouse_planning_header', array('no_ipp'=>$no_ipp))->result();
		
		$NO_PR = $ch_pr_header[0]->no_pr;
		if(empty($ch_pr_header[0]->no_pr)){
			$Ym = date('ym');
			$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_material_pr_header WHERE no_pr LIKE 'PR".$Ym."%' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 6, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2); 
			$NO_PR			= "PR".$Ym.$urut2;
		}

		//detail pr
		$val = 0;
		$get_detail_plan = $this->db->get_where('warehouse_planning_detail_acc', array('id'=>$id))->result();

		$qty_pr_app = (!empty($qty_revisi))?$qty_revisi:$get_detail_plan[0]->purchase;

		$ArrDetail_acc[$val]['no_pr'] 			= $NO_PR;
		$ArrDetail_acc[$val]['category'] 		= 'acc';
		$ArrDetail_acc[$val]['id_material'] 	= $get_detail_plan[0]->id_material;
		$ArrDetail_acc[$val]['idmaterial'] 		= $get_detail_plan[0]->idmaterial;
		$ArrDetail_acc[$val]['nm_material'] 	= $get_detail_plan[0]->nm_material;
		$ArrDetail_acc[$val]['qty_request'] 	= $get_detail_plan[0]->purchase;
		$ArrDetail_acc[$val]['qty_revisi'] 		= $qty_pr_app;
		$ArrDetail_acc[$val]['moq'] 			= $get_detail_plan[0]->moq;
		$ArrDetail_acc[$val]['reorder_point'] 	= $get_detail_plan[0]->reorder_point;
		$ArrDetail_acc[$val]['sisa_avl'] 		= $get_detail_plan[0]->sisa_avl;
		$ArrDetail_acc[$val]['book_per_month']	= $get_detail_plan[0]->book_per_month;
		$ArrDetail_acc[$val]['tanggal'] 		= $get_detail_plan[0]->tanggal;
		$ArrDetail_acc[$val]['created_by'] 		= $this->session->userdata['ORI_User']['username'];
		$ArrDetail_acc[$val]['created_date'] 	= date('Y-m-d H:i:s');

		//insert atau update header
		$get_header_PR = $this->db->get_where('tran_material_pr_header', array('no_pr'=>$NO_PR))->result();
		$total_pr_bef = (!empty($get_header_PR))?$get_header_PR[0]->total_material:0;

		$ArrHeaderPR = array(
			'total_material' 	=> $total_pr_bef + $qty_pr_app
		);

		if(empty($ch_pr_header[0]->no_pr)){
			$ArrHeaderPR = array(
				'no_pr' 			=> $NO_PR,
				'sts_ajuan' 		=> ($status == 'approve')?'OPN':'REJ',
				'total_material' 	=> $qty_pr_app,
				'created_by' 		=> $this->session->userdata['ORI_User']['username'],
				'created_date' 		=> date('Y-m-d H:i:s'),
			);
		}

		//update planning
		$ArrUpdate = array(
			'no_pr'			=> $NO_PR,
			'sts_app'		=> 'Y',
			'sts_app_by' 	=> $this->session->userdata['ORI_User']['username'],
			'sts_app_date' 	=> date('Y-m-d H:i:s')
		);

		$ArrUpdateHEad = array(
			'no_pr'			=> $NO_PR
		);

		// exit;
		$this->db->trans_start();
			//purchase request
			if(empty($ch_pr_header[0]->no_pr)){
				$this->db->insert('tran_material_pr_header', $ArrHeaderPR);
			}
			else{
				$this->db->where('no_pr',$NO_PR);
				$this->db->update('tran_material_pr_header', $ArrHeaderPR);
			}

			if(!empty($ArrDetail_acc)){
				$this->db->insert_batch('tran_material_pr_detail', $ArrDetail_acc);
			}

			//material planning
			$this->db->where('id', $id);
			$this->db->update('warehouse_planning_detail_acc', $ArrUpdate);
			// if(get_status_approve_pr_acc($id) < 1){
				if($tanda == 'P'){
					$this->db->where("no_ipp LIKE '".$tanda."%' AND DATE(created_date)", $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdateHEad);
				}
				if($tanda != 'P'){
					$this->db->where('no_ipp', $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrUpdateHEad);
				}
			// }
  		$this->db->trans_complete();
  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  		}
  		else{
  			$this->db->trans_commit();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				  'tanda' 	=> $tanda,
				  'no_ipp' 	=> $no_ipp,
				  'id_user'	=> $id_user
  			);
  			history('Approve accessories sebagian PR '.$id.' / '.$id_material);
  		}
  		echo json_encode($Arr_Data);
	}
	
	public function save_approve_pr_new(){
		$data = $this->input->post();
		$no_ipp 		= $data['no_ipp'];
		$tanda 			= $data['tanda'];
		$id_user 		= $data['id_user'];
		$mat_atau_acc	= $this->uri->segment(3);
		$status 		= 'approve';

		if(!empty($data['detail'])){
			$detail 	= $data['detail'];
		}
		if(!empty($data['detail_acc'])){
			$detail_acc = $data['detail_acc'];
		}

		//UPDATE MATERIAL PLANNING
		// $detail		= $this->db->query("SELECT no_ipp, created_date FROM warehouse_planning_detail WHERE id='".$id."'")->result();
		// $tanda 		= substr($detail[0]->no_ipp,0,1);
		// $no_ipp 	= $detail[0]->no_ipp;

		// echo $tanda.'<br>';
		// echo $no_ipp.'<br>';
		// print_r($detail);
		// exit;
		$ch_pr_header = $this->db->get_where('warehouse_planning_header', array('no_ipp'=>$no_ipp))->result();

		if($tanda == 'P'){
			$no_ippX = date('Y-m-d', strtotime($no_ipp));
			$ch_pr_header = $this->db->get_where('warehouse_planning_header', array('DATE(created_date)'=>$no_ippX))->result();
		}
		
		$NO_PR = $ch_pr_header[0]->no_pr;
		if(empty($ch_pr_header[0]->no_pr)){
			$Ym = date('ym');
			$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_material_pr_header WHERE no_pr LIKE 'PR".$Ym."%' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 6, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2); 
			$NO_PR			= "PR".$Ym.$urut2;
		}
		
		// print_r($detail);
		// exit;
		
		$ArrDetail 		= array();
		$ArrUpDetail 	= array();
		$SUM = 0;
		if(empty($mat_atau_acc)){
			if(!empty($data['detail'])){
				foreach($detail AS $val => $valx){
					$get_material = $this->db->query("SELECT * FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result();
					
					$qty_revisi = str_replace(',','', $valx['qty_revisi']);
					$SUM 		+= (!empty($qty_revisi))?$qty_revisi:$valx['qty_request'];
					$ArrDetail[$val]['no_pr'] 			= $NO_PR;
					$ArrDetail[$val]['category'] 		= 'mat';
					$ArrDetail[$val]['id_material'] 	= $get_material[0]->id_material;
					$ArrDetail[$val]['idmaterial'] 		= $get_material[0]->idmaterial;
					$ArrDetail[$val]['nm_material'] 	= $get_material[0]->nm_material;
					$ArrDetail[$val]['qty_request'] 	= $valx['qty_request'];
					$ArrDetail[$val]['qty_revisi'] 		= (!empty($qty_revisi))?$qty_revisi:$valx['qty_request'];
					$ArrDetail[$val]['moq'] 			= $valx['moq'];
					$ArrDetail[$val]['reorder_point'] 	= $valx['reorder_point'];
					$ArrDetail[$val]['sisa_avl'] 		= $valx['sisa_avl'];
					$ArrDetail[$val]['book_per_month']	= $valx['book_per_month'];
					$ArrDetail[$val]['tanggal'] 		= $valx['tanggal'];
					$ArrDetail[$val]['created_by'] 		= $this->session->userdata['ORI_User']['username'];
					$ArrDetail[$val]['created_date'] 	= date('Y-m-d H:i:s');
					
					$ArrUpDetail[$val]['id_material'] 	= $get_material[0]->id_material;
					$ArrUpDetail[$val]['no_pr'] 		= $NO_PR;
					$ArrUpDetail[$val]['sts_app'] 		= ($status == 'approve')?'Y':'D';
					$ArrUpDetail[$val]['sts_app_by'] 	= $this->session->userdata['ORI_User']['username'];
					$ArrUpDetail[$val]['sts_app_date'] 	= date('Y-m-d H:i:s');
				}
			}
		}
		
		$ArrDetail_acc 		= array();
		$ArrUpDetail_acc 	= array();
		if($mat_atau_acc == 'acc'){
			if(!empty($data['detail_acc'])){
				foreach($detail_acc AS $val => $valx){
//agus
/*
					$get_material = $this->db->query("SELECT * FROM accessories WHERE id='".$valx['id_material']."' LIMIT 1")->result();
					
					$qty_revisi = str_replace(',','', $valx['qty_revisi']);
					$SUM 		+= (!empty($qty_revisi))?$qty_revisi:$valx['qty_request'];
					$ArrDetail_acc[$val]['no_pr'] 			= $NO_PR;
					$ArrDetail_acc[$val]['category'] 		= 'acc';
					$ArrDetail_acc[$val]['id_material'] 	= (!empty($get_material[0]->id))?$get_material[0]->id:$valx['id_material'];
					$ArrDetail_acc[$val]['idmaterial'] 		= (!empty($get_material[0]->category))?$get_material[0]->category:'';
					$ArrDetail_acc[$val]['nm_material'] 	= (!empty($get_material[0]->nama))?$get_material[0]->nama:$valx['nm_material'];
					$ArrDetail_acc[$val]['qty_request'] 	= $valx['qty_request'];
					$ArrDetail_acc[$val]['qty_revisi'] 		= (!empty($qty_revisi))?$qty_revisi:$valx['qty_request'];
					$ArrDetail_acc[$val]['moq'] 			= $valx['moq'];
					$ArrDetail_acc[$val]['reorder_point'] 	= $valx['reorder_point'];
					$ArrDetail_acc[$val]['sisa_avl'] 		= $valx['sisa_avl'];
					$ArrDetail_acc[$val]['book_per_month']	= $valx['book_per_month'];
					$ArrDetail_acc[$val]['tanggal'] 		= $valx['tanggal'];
					$ArrDetail_acc[$val]['created_by'] 		= $this->session->userdata['ORI_User']['username'];
					$ArrDetail_acc[$val]['created_date'] 	= date('Y-m-d H:i:s');
*/
//agus					
					$ArrUpDetail_acc[$val]['id'] 			= $valx['id'];
					$ArrUpDetail_acc[$val]['no_pr'] 		= $NO_PR;
					$ArrUpDetail_acc[$val]['sts_app'] 		= ($status == 'approve')?'Y':'D';
					$ArrUpDetail_acc[$val]['sts_app_by'] 	= $this->session->userdata['ORI_User']['username'];
					$ArrUpDetail_acc[$val]['sts_app_date'] 	= date('Y-m-d H:i:s');
				}
			}
		}

		//insert atau update header
		$get_header_PR = $this->db->get_where('tran_material_pr_header', array('no_pr'=>$NO_PR))->result();
		$total_pr_bef = (!empty($get_header_PR))?$get_header_PR[0]->total_material:0;

		$ArrHeaderPR = array(
			'total_material' 	=> $total_pr_bef + $SUM
		);

		if(empty($ch_pr_header[0]->no_pr)){
			$ArrHeaderPR = array(
				'no_pr' 			=> $NO_PR,
				'sts_ajuan' 		=> ($status == 'approve')?'OPN':'REJ',
				'total_material' 	=> $SUM,
				'created_by' 		=> $this->session->userdata['ORI_User']['username'],
				'created_date' 		=> date('Y-m-d H:i:s'),
			);
		}
		
		//update planning
		$ArrUpdateHEad = array(
			'no_pr'			=> $NO_PR
		);
		
		// echo $NO_PR."<br>";
		// print_r($ArrHeaderPR);
		// if(empty($mat_atau_acc)){
		// 	print_r($ArrDetail);
		// 	print_r($ArrUpDetail);
		// }
		// if($mat_atau_acc == 'acc'){
		// 	print_r($ArrDetail_acc);
		// 	print_r($ArrUpDetail_acc);
		// }
		// exit; 
		
		$this->db->trans_start();
  			//purchase request
			if(empty($ch_pr_header[0]->no_pr)){
				$this->db->insert('tran_material_pr_header', $ArrHeaderPR);
			}
			else{
				$this->db->where('no_pr',$NO_PR);
				$this->db->update('tran_material_pr_header', $ArrHeaderPR);
			}

			if(empty($mat_atau_acc)){
				if(!empty($ArrDetail)){
					$this->db->insert_batch('tran_material_pr_detail', $ArrDetail);
				}
			}

// 			if($mat_atau_acc == 'acc'){
// 				if(!empty($detail_acc)){
// //agus
// 					$data = $this->input->post();
// 					$UserName = $this->session->userdata['ORI_User']['username'];
// 					$DateTime = date('Y-m-d H:i:s');

// 					$Ym = date('ym');
// 					$qIPP			= "SELECT MAX(no_pengajuan) as maxP FROM rutin_planning_header WHERE no_pengajuan LIKE 'R".$Ym."%' ";
// 					$resultIPP		= $this->db->query($qIPP)->result_array();
// 					$angkaUrut2		= $resultIPP[0]['maxP'];
// 					$urutan2		= (int)substr($angkaUrut2, 5, 5);

// 					$get_rutin 	= $this->db->get_where('con_nonmat_new',array('request >'=>0))->result_array();
// 					$ArrSaveHeader = [];
// 					$ArrSaveDetail = [];

// 					//GROUP PNGAJUAN
// 					$QUERY			= "SELECT MAX(no_pengajuan_group) as maxP FROM rutin_planning_header WHERE no_pengajuan_group LIKE 'R".$Ym."%' ";
// 					$RESULT			= $this->db->query($QUERY)->result_array();
// 					$ANGKAURUT		= $RESULT[0]['maxP'];
// 					$URUTANN		= (int)substr($ANGKAURUT, 5, 5);
// 					$URUTANN++;
// 					$URUT			= sprintf('%05s',$URUTANN);
// 					$KODE_GROUP		= "R".$Ym.$URUT;

// 					foreach($detail_acc AS $val => $valx){
// 						$get_material = $this->db->query("SELECT * FROM accessories WHERE id='".$valx['id_material']."' LIMIT 1")->result();
// 						$urutan2++;
// 						$urut2			= sprintf('%05s',$urutan2);
// 						$kodeP			= "R".$Ym.$urut2;

// 						$ArrSaveHeader[] = array(
// 							'no_pengajuan_group'	=> $KODE_GROUP,
// 							'no_pengajuan' 	=> $kodeP,
// 							'purchase' 		=> (!empty($qty_revisi))?$qty_revisi:$valx['qty_request'],
// 							'book_by' 		=> $this->session->userdata['ORI_User']['username'],
// 							'book_date' 	=> date('Y-m-d H:i:s'),
// 							'created_by' 	=> $this->session->userdata['ORI_User']['username'],
// 							'created_date' 	=> date('Y-m-d H:i:s')
// 						);

// 						$ArrSaveDetail[] = array(
// 							'no_pengajuan' 	=> $kodeP,
// 							'id_material' 	=> (!empty($get_material[0]->id))?$get_material[0]->id_material:$valx['id_material'],
// 							'nm_material' 	=> (!empty($get_material[0]->nama))?$get_material[0]->nama:$valx['nm_material'],
// 							'tanggal' 		=> $valx['tanggal'],
// 							'category_awal' => '9', //(!empty($get_material[0]->category))?$get_material[0]->category:"",
// 							'satuan' 		=> (!empty($get_material[0]->satuan))?$get_material[0]->satuan:"",
// 							'purchase' 		=> (!empty($qty_revisi))?$qty_revisi:$valx['qty_request'],
// 							'spec' 			=> (!empty($get_material[0]->spesifikasi))?$get_material[0]->spesifikasi:"",
// 							'spec_pr' 		=> "",
// 							'info_pr' 		=> "",
// 						);
// 					}
// 					$this->db->insert_batch('rutin_planning_header', $ArrSaveHeader);
// 					$this->db->insert_batch('rutin_planning_detail', $ArrSaveDetail);
// // agus


// //					$this->db->insert_batch('tran_material_pr_detail', $ArrDetail_acc);
// 				}
// 			}




			if($tanda == 'I'){
				$this->db->where('no_ipp', $no_ipp);
				$this->db->update('warehouse_planning_header', $ArrUpdateHEad);
				if(empty($mat_atau_acc)){
					if(!empty($ArrUpDetail)){
						$this->db->where('no_ipp', $no_ipp);
						$this->db->update_batch('warehouse_planning_detail', $ArrUpDetail, 'id_material');
					}
				}

				// if($mat_atau_acc == 'acc'){
				// 	if(!empty($ArrUpDetail_acc)){
				// 		$this->db->where('no_ipp', $no_ipp);
				// 		$this->db->update_batch('warehouse_planning_detail_acc', $ArrUpDetail_acc, 'id');
				// 	}
				// }
			}
			
			if($tanda == 'P'){
				$this->db->where('DATE(created_date)', date('Y-m-d', strtotime($no_ipp)));
				$this->db->update('warehouse_planning_header', $ArrUpdateHEad);
				
				$this->db->where('DATE(created_date)', date('Y-m-d', strtotime($no_ipp)));
				$this->db->update_batch('warehouse_planning_detail', $ArrUpDetail, 'id_material');
			}
			
  		$this->db->trans_complete();
  		if($this->db->trans_status() === FALSE){
  			$this->db->trans_rollback();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process failed. Please try again later ...',
  				'status'	=> 0,
				'tanda' 	=> $tanda,
				'no_ipp' 	=> $no_ipp,
				'id_user'	=> $id_user
  			);
  		}
  		else{
  			$this->db->trans_commit();
  			$Arr_Data	= array(
  				'pesan'		=>'Save process success. Thanks ...',
  				'status'	=> 1,
				'tanda' 	=> $tanda,
				'no_ipp' 	=> $no_ipp,
				'id_user'	=> $id_user
  			);
  			history('Approve PR '.$NO_PR);
  		}
  		echo json_encode($Arr_Data);
	}
	
	//PROGRESS PR
	public function index_progress_pr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Pembelian Material >> Progress PR Material',
		  'action'			=> 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'		=> $Arr_Akses
		);
		history('View Progress PR');
		$this->load->view('Purchase_request/progress_pr',$data);
	}
	
	public function modal_detail_progress_pr(){
		$no_pr = $this->uri->segment(3);
		$tanggal  = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
		$bulan    = ltrim(date('m', strtotime($tanggal)),'0');
		$tahun    = date('Y', strtotime($tanggal));

		$sql		= "	SELECT
							a.*,
							a.created_date AS tgl_approve,
							e.*,
							a.no_pr AS pr_ord,
							b.qty_stock,
							b.qty_booking,
							c.moq,
							(SELECT d.purchase FROM check_book_per_month d WHERE d.id_material=e.id_material AND d.tahun='".$tahun."' AND d.bulan='".$bulan."') AS book_per_month
						FROM
							tran_material_pr_header a
							LEFT JOIN tran_material_pr_detail e ON a.no_pr = e.no_pr
							LEFT JOIN warehouse_stock b ON e.id_material = b.id_material
							LEFT JOIN moq_material c ON e.id_material = c.id_material
						WHERE 1=1
							AND (b.id_gudang = '1' OR b.id_gudang = '2') AND a.no_pr = '".$no_pr."' ";
		$result = $this->db->query($sql)->result_array();
		
		$sql_non_frp= "	SELECT
							a.sts_ajuan,
							b.no_po,
							b.id_material,
							b.idmaterial,
							b.qty_request,
							b.qty_revisi,
							b.tanggal,
							b.keterangan,
							b.nm_material,
							c.satuan
						FROM
							tran_material_pr_header a
							LEFT JOIN tran_material_pr_detail b ON a.no_pr = b.no_pr
							LEFT JOIN accessories c ON b.id_material = c.id
						WHERE 1=1
							AND b.category = 'acc'
							AND a.no_pr = '".$no_pr."' 
						ORDER BY b.id ASC";
		$non_frp = $this->db->query($sql_non_frp)->result_array();

		$data = array(
		  'no_pr'		=> $no_pr,
		  'result'		=> $result,
		  'non_frp'		=> $non_frp
		);
		$this->load->view('Purchase_request/modal_detail_progress_pr',$data);
	}

	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	
	public function get_data_json_app_pr(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_app_pr(
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
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_category']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_booking'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'] - $row['qty_booking'],2)."</div>";
			
			$bookpermonth 	= number_format($row['book_per_month']);
			$leadtime 		= number_format(get_max_field('raw_material_supplier', 'lead_time_order', 'id_material', $row['id_material']));
			$safetystock 	= number_format(get_max_field('raw_materials', 'safety_stock', 'id_material', $row['id_material']));
			$reorder 		= ($bookpermonth*($safetystock/30))+($leadtime*($bookpermonth/30));
			$sisa_avl 		= $row['qty_stock'] - $row['qty_booking'];
			$nestedData[]	= "<div align='right'>".number_format($reorder,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['moq'],2)."</div>";
			
			$nestedData[]	= "<div align='right'>".number_format($row['tot_pur'],2)."</div>";
			$nestedData[]	= "<div align='right'>".date('d-m-Y', strtotime($row['tgl_butuh']))."</div>";
			$nestedData[]	= "<div align='right'>
									<input type='text' name='tot_rev' id='tot_rev_".$nomor."' class='form-control input-sm text-right maskM' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
									<input type='hidden' name='moq' id='moq_".$nomor."' class='form-control input-sm text-right' value='".$row['moq']."'>
									<input type='hidden' name='reorder_point' id='reorder_point_".$nomor."' class='form-control input-sm text-right' value='".$reorder."'>
									<input type='hidden' name='sisa_avl' id='sisa_avl_".$nomor."' class='form-control input-sm text-right' value='".$sisa_avl."'>
									<input type='hidden' name='book_per_month' id='book_per_month_".$nomor."' class='form-control input-sm text-right' value='".$bookpermonth."'>
									<input type='hidden' name='tgl_butuh' id='tgl_butuh_".$nomor."' class='form-control input-sm text-right' value='".$row['tgl_butuh']."'>
									<input type='hidden' name='tot_pur' id='tot_pur_".$nomor."' class='form-control input-sm text-right' value='".$row['tot_pur']."'>
									
									
								</div><script type='text/javascript'>$('.maskM').maskMoney();</script>";
			
			$save			= "<button type='button'class='btn btn-sm btn-info app_pr' title='Approve PR' data-status='approve' data-id_material='".$row['id_material']."' data-no='".$nomor."'><i class='fa fa-check'></i></button>";
			$delete			= "&nbsp;<button type='button'class='btn btn-sm btn-danger app_pr' title='Delete PR' data-status='reject' data-id_material='".$row['id_material']."' data-no='".$nomor."'><i class='fa fa-close'></i></button>";
			
			$nestedData[]	= "<div align='center'>".$save.$delete."</div>";
			
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

	public function query_data_json_app_pr($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$tanggal  = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
		$bulan    = ltrim(date('m', strtotime($tanggal)),'0');
		$tahun    = date('Y', strtotime($tanggal));
		
		$sql = "
			SELECT
				a.*,
				SUM(a.purchase) AS tot_pur,
				MIN(a.tanggal) AS tgl_butuh,
				b.nm_category,
				b.qty_stock,
				b.qty_booking,
				c.moq,
				(SELECT d.purchase FROM check_book_per_month d WHERE d.id_material=a.id_material AND d.tahun='".$tahun."' AND d.bulan='".$bulan."') AS book_per_month
			FROM
				warehouse_planning_detail a
				LEFT JOIN warehouse_stock b ON a.id_material = b.id_material
				LEFT JOIN moq_material c ON a.id_material = c.id_material
		    WHERE 1=1
				AND (b.kd_gudang = 'OPC1' OR b.kd_gudang = 'OPC2')
				AND a.no_pr IS NULL
				AND a.sts_app <> 'D'
				AND (
				a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.id_material
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nm_material'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	public function get_data_json_app_pr_new(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_app_pr_new(
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
			$tanda = substr($row['no_ipp'],0,1);
			$no_ipp = $row['no_ipp'];
			$no_ipp2 = $row['no_ipp'];
			$no_ipp3 = $row['no_ipp'];
			$kebutuhan = "Project ".strtoupper(get_name('production', 'project', 'no_ipp', $row['no_ipp']));
			if($tanda == 'P'){
				$no_ipp = "Re-Order Point ".date('d-m-Y', strtotime($row['created_date']));
				$no_ipp2 = date('d-m-Y', strtotime($row['created_date']));
				$no_ipp3 = date('Y-m-d', strtotime($row['created_date']));
				$kebutuhan = "Pemenuhan Stock Material";
			}

			if(check_atatus_pr($tanda, $no_ipp3, $row['created_by']) > 0){
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
					$nestedData[]	= "<div align='left'>".$no_ipp."</div>";
					$nestedData[]	= "<div align='left'>".$kebutuhan."</div>";
					$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
					$nestedData[]	= "<div align='left'>".date('d F Y', strtotime($row['created_date']))."</div>";
					$id_user = get_name('users','id_user','username',$row['created_by']);
					
					$save			= "";
					$view			= "<button type='button'class='btn btn-sm btn-warning view_pr' title='View PR' data-tanda='".$tanda."' data-no_ipp='".$no_ipp2."' data-no='".$nomor."' data-user='".$id_user."'><i class='fa fa-eye'></i></button>";
					if($row['sts_app'] == 'N'){
						$save			= "&nbsp;<button type='button'class='btn btn-sm btn-info app_pr' title='Approve PR' data-tanda='".$tanda."' data-no_ipp='".$no_ipp2."' data-no='".$nomor."' data-user='".$id_user."'><i class='fa fa-check'></i></button>";
					}
					// $delete			= "&nbsp;<button type='button'class='btn btn-sm btn-danger app_pr' title='Delete PR' data-status='reject' data-id_material='".$row['no_ipp']."' data-no='".$nomor."'><i class='fa fa-close'></i></button>";
					$delete			= "";
					$print			= "&nbsp;<a href='".site_url($this->uri->segment(1).'/print_detail_pr/'.$no_ipp2.'/'.$tanda.'/'.$id_user)."' class='btn btn-sm btn-success' target='_blank' title='Print PR' data-role='qtip'><i class='fa fa-print'></i></a>";
						
					$nestedData[]	= "<div align='left'>".$view.$save.$delete.$print."</div>";
				
				$data[] = $nestedData;
				$urut1++;
				$urut2++;
			}
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_json_app_pr_new($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$sql = "
			SELECT
				a.*
			FROM
				approval_pr a
		    WHERE 1=1 AND sts_app = 'N'
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	public function get_data_json_progress_pr(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_progress_pr(
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
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            if($asc_desc == 'desc')
            {
                
				$nomor = $urut1 + $start_dari;
            }

			$nestedData 	= array();
			
			$tanda = substr($row['no_ipp'],0,1);
			$no_ipp = $row['no_ipp'];
			$no_ipp2 = $row['no_ipp'];
			$kebutuhan = "Project ".strtoupper(get_name('production', 'project', 'no_ipp', $row['no_ipp']));
			if($tanda == 'P'){
				$no_ipp = "Re-Order Point ".date('d-m-Y', strtotime($row['tgl_ipp']));
				$no_ipp2 = date('d-m-Y', strtotime($row['tgl_ipp']));
				$kebutuhan = "Pemenuhan Stock Material";
			}
			$id_user = get_name('users','id_user','username',$row['created_by']);
			
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_pr']."</div>";
			$nestedData[]	= "<div align='left'>".$no_ipp."</div>";
			$nestedData[]	= "<div align='left'>".$kebutuhan."</div>";

			$print			= "&nbsp;<a href='".site_url($this->uri->segment(1).'/print_detail_pr/'.$no_ipp2.'/'.$tanda.'/'.$id_user)."' class='btn btn-sm btn-success' target='_blank' title='Print PR' data-role='qtip'><i class='fa fa-print'></i></a>";
			$print2			= "&nbsp;<a href='".site_url($this->uri->segment(1).'/print_detail_pr_new/'.$row['no_pr'])."' class='btn btn-sm btn-success' target='_blank' title='Print PR' data-role='qtip'><i class='fa fa-print'></i></a>";
			
			$nestedData[]	= "<div align='center'>
								<button type='button' class='btn btn-sm btn-primary detailPR title='Detail Purchase Request' data-no_pr='".$row['no_pr']."'><i class='fa fa-eye'></i></button>
								".$print2."
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

	public function query_data_json_progress_pr($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
			SELECT
				a.*,
				b.no_ipp,
				b.created_date AS tgl_ipp
			FROM
				tran_material_pr_header a
				LEFT JOIN approval_pr b ON a.no_pr = b.no_pr
		    WHERE 1=1
				AND (
				a.no_pr LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_pr'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_progress_pr_old(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_progress_pr(
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
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            if($asc_desc == 'desc')
            {
                
				$nomor = $urut1 + $start_dari;
            }

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['tgl_approve']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_pr']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'] - $row['qty_booking'],2)."</div>";
			
			$bookpermonth 	= number_format($row['book_per_month']);
			$leadtime 		= number_format(get_max_field('raw_material_supplier', 'lead_time_order', 'id_material', $row['id_material']));
			$safetystock 	= number_format(get_max_field('raw_materials', 'safety_stock', 'id_material', $row['id_material']));
			$reorder 		= ($bookpermonth*($safetystock/30))+($leadtime*($bookpermonth/30));
			$sisa_avl 		= $row['qty_stock'] - $row['qty_booking'];
			$nestedData[]	= "<div align='right'>".number_format($reorder,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['moq'],2)."</div>";
			
			$nestedData[]	= "<div align='right'>".number_format($row['qty_request'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_revisi'],2)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['tanggal']))."</div>";
			if($row['sts_ajuan'] == 'REJ'){
				$sts_name = 'PR Rejected';
				$warna	= 'red';
			}
			else{
				if($row['qty_request'] == $row['qty_revisi']){
					$sts_name = 'PR Approved';
					$warna	= 'green';
					if(!empty($row['no_po'])){
						$sts_name = 'PR Approved, by '.$row['no_po'];
						$warna	= 'green';
					}
					
				}
				elseif($row['qty_request'] <> $row['qty_revisi']){
					$sts_name = 'PR Approved Rev Qty';
					$warna	= 'blue';
					if(!empty($row['no_po'])){
						$sts_name = 'PR Approved Rev Qty, by '.$row['no_po'];
						$warna	= 'blue';
					}
				}
			}
			
			$nestedData[]	= "<div align='left'><span class='badge bg-".$warna."'>".$sts_name."</span></div>";
			
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

	public function query_data_json_progress_pr_old($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$tanggal  = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
		$bulan    = ltrim(date('m', strtotime($tanggal)),'0');
		$tahun    = date('Y', strtotime($tanggal));
		
		$sql = "
			SELECT
				a.*,
				a.created_date AS tgl_approve,
				e.*,
				a.no_pr AS pr_ord,
				b.qty_stock,
				b.qty_booking,
				c.moq,
				(SELECT d.purchase FROM check_book_per_month d WHERE d.id_material=e.id_material AND d.tahun='".$tahun."' AND d.bulan='".$bulan."') AS book_per_month
			FROM
				tran_material_pr_header a
				LEFT JOIN tran_material_pr_detail e ON a.no_pr = e.no_pr
				LEFT JOIN warehouse_stock b ON e.id_material = b.id_material
				LEFT JOIN moq_material c ON e.id_material = c.id_material
		    WHERE 1=1
				AND (b.kd_gudang = 'OPC1' OR b.kd_gudang = 'OPC2')
				AND (
				e.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR e.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR e.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'tgl_approve',
			2 => 'no_pr',
			3 => 'nm_material'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}
