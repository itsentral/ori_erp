<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('material_planning_model');
		$this->load->model('purchase_request_model');
		$this->load->model('purchase_order_model');
		$this->load->model('warehouse_model');
		$this->load->model('adjustment_material_model');
		$this->load->model('Jurnal_model');
		$this->load->model('tanki_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}

		$this->gudang_produksi = getGudangProduksi();
    }

	//==========================================================================================================================
	//===================================================MATERIAL PLANNING======================================================
	//==========================================================================================================================

	public function material_planing(){
		$this->material_planning_model->index_material_planning();
	}

	public function server_side_material_planning(){
		$this->material_planning_model->get_data_json_material_planning();
	}

	public function modal_detail_material_planning(){
		$this->material_planning_model->detail_material_planning();
	}

	public function modal_add_material_planning(){
		$this->material_planning_model->add_get_query_material_planning();
	}

	public function modal_edit_material_planning(){
		$this->material_planning_model->edit_get_query_material_planning();
	}

	public function save_material_planning(){
		$this->material_planning_model->add_get_query_material_planning();
	}

	public function edit_material_planning(){
		$this->material_planning_model->edit_get_query_material_planning();
	}

	public function booking_material(){
		$this->material_planning_model->process_booking_material_planning();
	}

	public function spk_material(){
		$this->material_planning_model->print_material_planning();
	}
	
	//Reorder Poin
	public function reorder_point(){
		$this->material_planning_model->index_reorder_point();
	}
	
	public function server_side_reorder_point(){
		$this->material_planning_model->get_data_json_reorder_point();
	}
	
	public function save_reorder_point(){
		$this->material_planning_model->save_reorder_point();
	}

	public function save_reorder_change(){
		$this->material_planning_model->save_reorder_change();
	}

	public function save_reorder_change_date(){
		$this->material_planning_model->save_reorder_change_date();
	}

	public function clear_update_reorder(){
		$this->material_planning_model->clear_update_reorder();
	}

	public function save_reorder_all(){
		$this->material_planning_model->save_reorder_all();
	}

	//==========================================================================================================================
	//=================================================END MATERIAL PLANNING====================================================
	//==========================================================================================================================
	
	//==========================================================================================================================
	//====================================================PURCHASE REQUEST======================================================
	//==========================================================================================================================
	
	//Approval PR
	public function approval_pr(){
		$this->purchase_request_model->index_approval_pr();
	}
	public function list_pr_new(){
		$controller			= 'warehouse/list_pr_new';
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
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data_pr	= $this->db->group_by('a.no_pr')->order_by('a.created_date','desc')->join('warehouse_planning_header a','a.no_ipp=b.no_ipp')->get('warehouse_planning_detail b')->result_array();
		$data = array(
		  'title'			=> 'Indeks Of PR Material',
		  'action'			=> 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'		=> $Arr_Akses,
		  'data_pr'			=> $data_pr,
		  'ArrGetSO'		=> $ArrGetSO
		);
		history('View PR Material');
		$this->load->view('Purchase_request/list_pr_new',$data);
	}	
	public function approval_pr_new(){
		$this->purchase_request_model->index_approval_pr_new();
	}
	
	public function server_side_app_pr(){
		$this->purchase_request_model->get_data_json_app_pr();
	}
	
	public function server_side_app_pr_new(){
		$this->purchase_request_model->get_data_json_app_pr_new();
	}
	
	public function save_approve_pr(){
		$this->purchase_request_model->save_approve_pr();
	}
	
	public function save_approve_pr_new(){
		$this->purchase_request_model->save_approve_pr_new();
	}
	
	public function modal_detail_pr(){
		$this->purchase_request_model->modal_detail_pr();
	}
	
	public function modal_approve_pr(){
		$this->purchase_request_model->modal_approve_pr();
	}
	
	public function print_detail_pr(){
		$this->purchase_request_model->print_detail_pr();
	}

	public function print_detail_pr_new(){
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

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'no_pr'		=> $no_pr,
			'result'		=> $result,
			'non_frp'		=> $non_frp
		);
		
		history('Print approve pr material '.$no_pr);
		$this->load->view('Print/print_pr_approve_new', $data);
	}

	public function save_approve_pr_new_aksesoris(){
		$data = $this->input->post();
		$no_ipp 		= $data['no_ipp'];
		$tanda 			= $data['tanda'];
		$id_user 		= $data['id_user'];
		$tgl_butuh 		= (!empty($data['tgl_butuh'] AND $data['tgl_butuh'] != '0000-00-00'))?$data['tgl_butuh']:NULL;
		$mat_atau_acc	= $this->uri->segment(3);
		$status 		= 'approve';
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		
		if(!empty($data['detail_acc'])){
			$detail_acc = $data['detail_acc'];
		}

		$ch_pr_header = $this->db->get_where('warehouse_planning_header', array('no_ipp'=>$no_ipp))->result();

		if($tanda == 'P'){
			$no_ippX = date('Y-m-d', strtotime($no_ipp));
			$ch_pr_header = $this->db->get_where('warehouse_planning_header', array('DATE(created_date)'=>$no_ippX))->result();
		}
		
		//NEW
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
		if($mat_atau_acc == 'acc'){
			if(!empty($data['detail_acc'])){
				foreach($detail_acc AS $val => $valx){
					$get_material = $this->db->query("SELECT * FROM accessories WHERE id='".$valx['id_material']."' LIMIT 1")->result();
					
					$qty_revisi = str_replace(',','', $valx['qty_revisi']);

					$qty 	= $qty_revisi;
					$harga 	= 0;
					
					$SUM_QTY 	+= $qty;
					$SUM_HARGA 	+= $harga * $qty;
					
					$ArrDetailPR[$val]['no_pr'] 		= $no_pr;
					$ArrDetailPR[$val]['no_pr_group'] 	= $no_pr_group;
					$ArrDetailPR[$val]['category'] 		= 'rutin';
					$ArrDetailPR[$val]['tgl_pr'] 		= date('Y-m-d');
					$ArrDetailPR[$val]['id_barang'] 	= (!empty($get_material[0]->id_material))?$get_material[0]->id_material:$valx['id_material'];;
					$ArrDetailPR[$val]['nm_barang'] 	= get_name_acc($valx['id_material']);
					$ArrDetailPR[$val]['qty'] 			= (!empty($qty_revisi))?$qty_revisi:$valx['qty_request'];
					$ArrDetailPR[$val]['nilai_pr'] 		= $harga;
					$ArrDetailPR[$val]['tgl_dibutuhkan']= $tgl_butuh;
					$ArrDetailPR[$val]['satuan']		= $valx['satuan'];
					$ArrDetailPR[$val]['app_status'] 	= 'Y';
					$ArrDetailPR[$val]['app_reason']	= NULL;
					$ArrDetailPR[$val]['app_by'] 		= $data_session['ORI_User']['username'];
					$ArrDetailPR[$val]['app_date']		= $dateTime;
					$ArrDetailPR[$val]['created_by'] 	= $data_session['ORI_User']['username'];
					$ArrDetailPR[$val]['created_date'] 	= $dateTime;
					$ArrDetailPR[$val]['in_gudang']    	= 'project';

					$ArrUpDetail_acc[$val]['id'] 			= $valx['id'];
					$ArrUpDetail_acc[$val]['no_pr'] 		= $no_pr_group;
					$ArrUpDetail_acc[$val]['sts_app'] 		= ($status == 'approve')?'Y':'D';
					$ArrUpDetail_acc[$val]['sts_app_by'] 	= $this->session->userdata['ORI_User']['username'];
					$ArrUpDetail_acc[$val]['sts_app_date'] 	= date('Y-m-d H:i:s');
				}
			}
		}
		
		//update planning
		$ArrUpdateHEad = array(
			'no_pr'			=> $no_pr_group
		);
		
		$this->db->trans_start();
			if($mat_atau_acc == 'acc'){
				if(!empty($ArrDetailPR)){
					$this->db->insert('tran_pr_header', $ArrHeaderPR);
					$this->db->insert_batch('tran_pr_detail', $ArrDetailPR);
				}
			}

			if($tanda == 'I'){
				$this->db->where('no_ipp', $no_ipp);
				$this->db->update('warehouse_planning_header', $ArrUpdateHEad);

				if($mat_atau_acc == 'acc'){
					if(!empty($ArrUpDetail_acc)){
						$this->db->where('no_ipp', $no_ipp);
						$this->db->update_batch('warehouse_planning_detail_acc', $ArrUpDetail_acc, 'id');
					}
				}
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
  			history('Approve PR '.$no_pr_group);
  		}
  		echo json_encode($Arr_Data);
	}
	
	
	//Progress PR
	public function progress_pr(){
		$this->purchase_request_model->index_progress_pr();
	}
	
	public function modal_detail_progress_pr(){
		$this->purchase_request_model->modal_detail_progress_pr();
	}
	
	public function server_side_progress_pr(){
		$this->purchase_request_model->get_data_json_progress_pr();
	}
	
	public function reject_sebagian_pr_new(){
		$this->purchase_request_model->reject_sebagian_pr_new(); 
	}
	
	public function reject_sebagian_pr_new_acc(){
		$this->purchase_request_model->reject_sebagian_pr_new_acc(); 
	}

	public function approve_sebagian_pr_new(){
		$this->purchase_request_model->approve_sebagian_pr_new(); 
	}
	
	public function approve_sebagian_pr_new_acc(){
		$this->purchase_request_model->approve_sebagian_pr_new_acc(); 
	}
	
	//==========================================================================================================================
	//==================================================END PURCHASE REQUEST====================================================
	//==========================================================================================================================
	
	//==========================================================================================================================
	//====================================================PURCHASE ORDER========================================================
	//==========================================================================================================================
	
	public function material_purchase(){
		$this->purchase_order_model->index_po();
	}
	
	public function server_side_po(){
		$this->purchase_order_model->get_data_json_po();
	}
	
	public function modal_detail_po(){
		$this->purchase_order_model->modal_detail_po();
	}
	
	public function modal_edit_po(){
		$this->purchase_order_model->modal_edit_po();
	}
	
	public function modal_add_po(){
		$this->purchase_order_model->modal_add_po();
	}
	
	public function server_side_list_pr(){
		$this->purchase_order_model->get_data_json_list_pr();
	}
	
	public function save_po(){
		$this->purchase_order_model->save_po();
	}
	
	public function update_po(){
		$this->purchase_order_model->update_po();
	}
	
	public function cancel_po(){
		$this->purchase_order_model->cancel_po();
	}
	
	public function cancel_sebagian_po(){
		$this->purchase_order_model->cancel_sebagian_po();
	}
	
	public function spk_po(){
		$this->purchase_order_model->spk_po();
	}
	
	public function print_rfq(){
		$this->purchase_order_model->print_rfq();
	}
	
	public function modal_edit_rfq(){
		$no_rfq = $this->uri->segment(3);
		$result		= $this->db->select('
									a.no_rfq,
									a.id_material as id_barang,
									a.qty,
									a.nm_material as nm_barang,
									"" AS spec,
									b.category,
									b.tanggal AS tgl_dibutuhkan,
									b.no_pr,
									b.created_date AS tgl_pr
									')
								->from('tran_material_rfq_detail a')
								->join('tran_material_pr_detail b','a.no_rfq=b.no_rfq','left')
								->where('a.id_material=b.id_material')
								->where('a.no_rfq',$no_rfq)
								->where('a.deleted','N')
								->group_by('a.id_material')
								->get()
								->result_array();
		// print_r($result); exit;
		$RestSupplierList 		= $this->db->select('id_supplier, nm_supplier')->order_by('nm_supplier','asc')->get_where('supplier',array('sts_aktif'=>'aktif'))->result_array();
		$RestCheckedSupplier 	= $this->db->select('id_supplier')->get_where('tran_material_rfq_header',array('no_rfq'=>$no_rfq,'deleted'=>'N'))->result_array();

		$ArrSupChecked = '';
		if(!empty($RestCheckedSupplier)){
			$ArrData1 = array();
			foreach($RestCheckedSupplier as $vaS => $vaA){
				 $ArrData1[] = $vaA['id_supplier'];
			}
			$ArrData1 		= implode("," ,$ArrData1);
			$ArrSupChecked 	= explode("," ,$ArrData1);
		}
		
		$data = array(
			'result' 			=> $result,
			'supplierList' 		=> $RestSupplierList,
			'supplierChecked' 	=> $ArrSupChecked,
			'no_rfq' 			=> $no_rfq
		);

		$this->load->view('Purchase_order/modal_edit_rfq', $data);
	}
	
	public function update_rfq_supplier(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$no_rfq			= $data['no_rfq'];
		$category		= $data['category'];
		$id_supplier	= $data['id_supplier'];
		$dateTime		= date('Y-m-d H:i:s');
		$UserName		= $data_session['ORI_User']['username'];
		
		$check			= $data['check'];
		$ArrList 		= array();
		foreach($check AS $vaxl){
			$ArrList[$vaxl] = $vaxl;
		}
		$dtImplode		= "('".implode("','", $ArrList)."')";
		
		$qListG 	= "SELECT id, id_material, idmaterial, nm_material, SUM(qty_revisi) AS purchase, tanggal AS tgl_dibutuhkan, category FROM tran_material_pr_detail WHERE no_pr IN ".$dtImplode." AND no_rfq = '".$no_rfq."' GROUP BY id_material";
		$restListG 	= $this->db->query($qListG)->result_array();

		$ArrDetail = array();
		$ArrHeader = array();
		$no = 0;
		foreach($id_supplier AS $sup => $supx){
			$restSupplier	= $this->db->limit(1)->get_where('supplier',array('id_supplier'=>$supx))->result();
			$SUM_MAT 		= 0;
			
			$no++;
			$num = sprintf('%03s',$no);
			foreach($restListG AS $val => $valx){
				$SUM_MAT += $valx['purchase'];
				
				$ArrDetail[$sup.$val]['no_rfq'] 		= $no_rfq;
				$ArrDetail[$sup.$val]['hub_rfq'] 		= $no_rfq.'-'.$num;
				$ArrDetail[$sup.$val]['category'] 		= $valx['category'];
				$ArrDetail[$sup.$val]['id_material'] 	= $valx['id_material'];
				$ArrDetail[$sup.$val]['idmaterial'] 	= $valx['idmaterial'];
				$ArrDetail[$sup.$val]['nm_material'] 	= $valx['nm_material'];
				$ArrDetail[$sup.$val]['id_supplier'] 	= $supx;
				$ArrDetail[$sup.$val]['nm_supplier'] 	= $restSupplier[0]->nm_supplier;
				$ArrDetail[$sup.$val]['qty'] 		 	= $valx['purchase'];
				$ArrDetail[$sup.$val]['tgl_dibutuhkan'] = $valx['tgl_dibutuhkan'];
				$ArrDetail[$sup.$val]['created_by'] 	= $UserName;
				$ArrDetail[$sup.$val]['created_date'] 	= $dateTime;
			}
			
			$ArrHeader[$sup]['no_rfq'] 			= $no_rfq;
			$ArrHeader[$sup]['hub_rfq'] 		= $no_rfq.'-'.$num;
			$ArrHeader[$sup]['id_supplier'] 	= $supx;
			$ArrHeader[$sup]['nm_supplier'] 	= $restSupplier[0]->nm_supplier;
			$ArrHeader[$sup]['total_request'] 	= $SUM_MAT;
			$ArrHeader[$sup]['created_by'] 		= $UserName;
			$ArrHeader[$sup]['created_date'] 	= $dateTime;
			$ArrHeader[$sup]['updated_by'] 		= $UserName;
			$ArrHeader[$sup]['updated_date'] 	= $dateTime;

		}

		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit;
		$this->db->trans_start();
			$this->db->delete('tran_material_rfq_header', array('no_rfq' => $no_rfq));
			$this->db->delete('tran_material_rfq_detail', array('no_rfq' => $no_rfq));
			$this->db->insert_batch('tran_material_rfq_header', $ArrHeader);
			$this->db->insert_batch('tran_material_rfq_detail', $ArrDetail);
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
			history('Update RFQ Supplier : '.$no_rfq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function cancel_sebagian_rfq(){
		$data_session	= $this->session->userdata;
		$no_pr			= $this->uri->segment(3);
		$id_barang	= $this->uri->segment(4);
		$no_rfq			= $this->uri->segment(5);

		// echo $id."<br>";
		// echo $no_po."<br>";
		// echo $id_material."<br>";
		// exit;

		$ArrUpdateDetail = array(
			'deleted' => 'Y',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		);

		$ArrUpdateD = array(
			'no_rfq' => NULL
		);

		// print_r($ArrUpdateD);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_rfq', $no_rfq);
			$this->db->where('id_material', $id_barang);
			$this->db->update('tran_material_rfq_detail', $ArrUpdateDetail);
			
			$this->db->where('no_rfq', $no_rfq);
			$this->db->where('no_pr', $no_pr);
			$this->db->where('id_material', $id_barang);
			$this->db->update('tran_material_pr_detail', $ArrUpdateD);
			
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0,
				'no_rfq'		=> $no_rfq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1,
				'no_rfq'		=> $no_rfq
			);
			history('Cancel Sebagian RFQ Material : '.$no_rfq.'/'.$id_barang.'/'.$no_pr);
		}
		echo json_encode($Arr_Data);
	}

	public function modal_edit_rfq_print(){
		if($this->input->post()){
			$data_session	= $this->session->userdata;
			$data	= $this->input->post();
			
			$ArrHeader = array(
				'incoterms' 		=> strtolower($data['incoterms']),
				'top' 				=> strtolower($data['top']),
				'remarks' 			=> strtolower($data['remarks']),
				'updated_print_by' 	=> $data_session['ORI_User']['username'],
				'updated_print_date'=> date('Y-m-d H:i:s')
			);
			
			// exit;
			
			$this->db->trans_start();
				$this->db->where('no_rfq', $data['no_rfq']);
				$this->db->update('tran_material_rfq_header', $ArrHeader);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save data failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save data success. Thanks ...',
					'status'	=> 1
				);				
				history('Edit RFQ print custom : '.$data['no_rfq']);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$no_rfq 	= $this->uri->segment(3);

			$sql 		= "	SELECT 
								a.*
							FROM 
								tran_material_rfq_header a
							WHERE 
								a.no_rfq='".$no_rfq."'
							";
								
			$result		= $this->db->query($sql)->result();
			
			$data = array(
				'data' => $result
			);
			
			$this->load->view('Purchase_order/modal_edit_rfq_print', $data);
		}
	}

	public function delete_rfq(){
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$no_rfq			= $this->uri->segment(3);

		$ArrUpdateH = array(
			'sts_ajuan' => 'CNC',
			'cancel_by' => $UserName,
			'cancel_date' => $DateTime
		);

		$ArrUpdateD = array(
			'no_rfq' => NULL
		);

		$this->db->trans_start();
			$this->db->where('no_rfq', $no_rfq);
			$this->db->update('tran_material_rfq_header', $ArrUpdateH);

			// $this->db->where('no_rfq', $no_rfq);
			// $this->db->update('tran_material_pr_detail', $ArrUpdateD);
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
			history('Delete RFQ '.$no_rfq);
		}
		echo json_encode($Arr_Data);
	}
	
	//==========================================================================================================================
	//================================================END PURCHASE ORDER========================================================
	//==========================================================================================================================
	
	
	//==========================================================================================================================
	//====================================================WAREHOUSE=============================================================
	//==========================================================================================================================
	//MATERIAL STOCK
	public function material_stock(){
		$this->warehouse_model->index_material_stock();
	}
	
	public function server_side_material_stock(){
		$this->warehouse_model->get_data_json_material_stock();
	}

	public function modal_history(){
		$this->warehouse_model->modal_history();
	}

	public function modal_history_booking(){
		$this->warehouse_model->modal_history_booking();
	}
	
	//MATERIAL ADJUSTMENT
	public function incoming_material(){
		$this->warehouse_model->index_incoming_material();
	}
	
	public function incoming_check(){
		$this->warehouse_model->index_incoming_check();
	}
	
	public function modal_incoming_check(){
		$this->warehouse_model->modal_incoming_check();
	}
	
	public function server_side_incoming_material(){
		$this->warehouse_model->get_data_json_incoming_material();
	}
	
	public function server_side_check_material(){
		$this->warehouse_model->get_data_json_check_material();
	}
	
	public function modal_detail_adjustment(){
		$this->warehouse_model->modal_detail_adjustment();
	}
	
	public function modal_incoming_material(){
		$this->warehouse_model->modal_incoming_material();
	}
	
	public function process_in_material(){
		$this->warehouse_model->process_in_material();
	}
	
	public function process_check_material(){
		$this->warehouse_model->process_check_material();
	}
	
	public function process_adjustment(){
		$this->warehouse_model->process_adjustment();
	}
	
	public function modal_move_gudang(){
		$this->warehouse_model->modal_move_gudang();
	}
	
	public function move_material(){
		$this->warehouse_model->move_material();
	}
	
	public function server_side_move_gudang(){
		$this->warehouse_model->get_data_json_move_gudang();
	}
	
	public function print_incoming(){
		$this->warehouse_model->print_incoming();
	}
	
	public function print_incoming2(){
		$this->warehouse_model->print_incoming2();
	}
	
	//REQUEST SUB GUDANG
	public function request_subgudang(){
		$this->warehouse_model->index_request_material();
	}
	
	public function server_side_request_material(){
		$this->warehouse_model->get_data_json_request_material();
	}
	
	public function modal_request_material(){
		$this->warehouse_model->modal_request_material();
	}
	
	public function server_side_modal_request_material(){
		$this->warehouse_model->get_data_json_modal_request_material();
	}
	
	public function process_request_material(){
		$this->warehouse_model->process_request_material();
	}
	
	public function print_request(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$GET_SO_NUMBER = $this->db->get('so_number')->result_array();
		$ArrGetSO = [];
		foreach($GET_SO_NUMBER AS $val => $value){
			$ArrGetSO[$value['id_bq']] = $value['so_number'];
		}

		$GET_SPK_NUMBER = $this->db->get_where('so_detail_header',array('no_spk <>'=>NULL))->result_array();
		$ArrGetSPK = [];
		$ArrGetIPP = [];
		foreach($GET_SPK_NUMBER AS $val => $value){
			$ArrGetSPK[$value['id']] = $value['no_spk'];
			$ArrGetIPP[$value['id']] = $value['id_bq'];
		}

		$data = array(
			'ArrGetSO' => $ArrGetSO,
			'ArrGetSPK' => $ArrGetSPK,
			'ArrGetIPP' => $ArrGetIPP,
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'check' => $check,
			'tanki'	=> $this->tanki_model,
		);

		history('Print Request Material '.$kode_trans);
		$this->load->view('Print/print_list_subgudang', $data);
	}
	
	public function print_surat_jalan(){
		$this->warehouse_model->print_surat_jalan();
	}

	public function print_request_sub(){
		$this->warehouse_model->print_request_sub();
	}
	
	public function modal_request_check(){
		$this->warehouse_model->modal_request_check();
	}

	public function modal_request_edit(){
		$this->warehouse_model->modal_request_edit();
	}
	
	public function get_list_exp(){
		$this->warehouse_model->get_list_exp();
	}
	
	public function save_temp_mutasi(){
		$this->warehouse_model->save_temp_mutasi();
	}

	public function cancel_request(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;

		$kode_trans			= $data['kode_trans'];
		$filter_pusat		= $data['filter_pusat'];
		$filter_subgudang	= $data['filter_subgudang'];
		$filter_uri_tanda	= $data['filter_uri_tanda'];
		

		$ArrDeleted = array(
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('kode_trans', $kode_trans);
			$this->db->update('warehouse_adjustment', $ArrDeleted);
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0,
				'filter_pusat'	=> $filter_pusat,
				'filter_subgudang'	=> $filter_subgudang,
				'filter_uri_tanda'	=> $filter_uri_tanda,
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1,
				'filter_pusat'	=> $filter_pusat,
				'filter_subgudang'	=> $filter_subgudang,
				'filter_uri_tanda'	=> $filter_uri_tanda,
			);
			history("Cancel request : ".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}
	
	//REQUEST PRODUKSI
	public function request_produksi(){
		$tanda = $this->uri->segment(3);
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$judul = "Warehouse Material >> Gudang Produksi >> Request Produksi";
		if(!empty($tanda)){
			$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)).'/'.$tanda);
			$judul = "Warehouse Material >> Sub Gudang >> Request List";
		}
		// echo $controller.'<br>';
		// exit;
		$Arr_Akses			= getAcccesmenu($controller);
		// print_r($Arr_Akses);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();
		$subgudang			= $this->db->query("SELECT * FROM warehouse WHERE category='produksi' ORDER BY urut ASC")->result_array();
		$no_ipp				= $this->db->query("SELECT
													a.no_ipp,
													b.so_number,
													a.id_product
												FROM
													production_spk a
													LEFT JOIN so_number b ON a.no_ipp=REPLACE(b.id_bq, 'BQ-', '')
												WHERE 1=1
													-- a.spk2_cost = 'N'
													AND a.created_date >= '2022-02-01'
												GROUP BY a.no_ipp")->result_array();
		$no_ipp_deadstok 	= $this->db
								->select('product_code_cut AS code_est,no_ipp,product_code AS no_so')
								->get_where('production_spk',array('id_product'=>'deadstok'))
								->result_array();
		$list_ipp_req		= $this->db->query("SELECT no_ipp FROM warehouse_adjustment WHERE no_ipp LIKE 'IPP%' GROUP BY no_ipp")->result_array();
		$uri_tanda			= $this->uri->segment(3);
		$data = array(
			'title'			=> $judul,
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'pusat'			=> $pusat,
			'subgudang'		=> $subgudang,
			'no_ipp'		=> $no_ipp,
			'no_ipp_deadstok'		=> $no_ipp_deadstok,
			'list_ipp_req'	=> $list_ipp_req,
			'uri_tanda'		=> $uri_tanda,
			'tanki'			=> $this->tanki_model,
		);
		history('View Request Produksi');
		$this->load->view('Warehouse/request_produksi',$data);
	}
	
	public function server_side_request_produksi(){
		$this->warehouse_model->get_data_json_request_produksi();
	}
	
	public function modal_request_produksi(){
		$this->warehouse_model->modal_request_produksi();
	}
	
	public function server_side_modal_request_produksi(){
		$this->warehouse_model->get_data_json_modal_request_produksi();
	}
	
	public function process_request_produksi(){
		$this->warehouse_model->process_request_produksi();
	}

	public function request_mat_resin(){
		$this->warehouse_model->request_mat_resin();
	}

	public function save_update_produksi_2_new(){ 
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');
		
		$UserName = $this->session->userdata['ORI_User']['username'];
		$DateTime = date('Y-m-d H:i:s');
		

		$id				= $data['id'];
		$kode_trans		= $data['kode_trans'];
		$kode_spk 		= $data['kode_spk'];
		$hist_produksi	= $data['hist_produksi'];
		$id_gudang 		= $data['id_gudang_from'];
		$id_gudang_wip 	= $data['id_gudang'];
		
		
		
		
		$no_request 	= $data['no_request'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$requesta_add 	= (!empty($data['requesta_add']))?$data['requesta_add']:array();
		$edit_add 		= (!empty($data['edit_add']))?$data['edit_add']:array();
		$GET_MATERIAL	= get_detail_material();
		$GET_PERCENT	= get_persent_by_subgudang_filter($kode_trans);
		$xyz_no_spk 	= $data['xyz_no_spk'];
		$xyz_no_so 		= $data['xyz_no_so'];
		$xyz_product 	= $data['xyz_product'];

		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		$ArrEndChange = [];
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_req_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}

			$ArrEndChange = array(
				'file_eng_change' 	=> $file_name
			);
		}

		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
			}
		}
		$ArrUpdateStock = [];
		//ADD MATERIAL
		$ArrRequestHist = [];
		$ArrRequest = [];
		$nomor = 999;
		if(!empty($requesta_add)){
			foreach ($requesta_add as $key => $value) { $nomor++;
				$TERPAKAI = str_replace(',','',$value['terpakai']);
				$ArrRequest[$key]['kode_spk'] = $kode_spk;
				$ArrRequest[$key]['kode_trans'] = $kode_trans;
				$ArrRequest[$key]['hist_produksi'] = $hist_produksi;
				$ArrRequest[$key]['id_utama'] = $id;
				$ArrRequest[$key]['id_material'] = $value['id_material'];
				$ArrRequest[$key]['actual_type'] = $value['actual_type'];
				$ArrRequest[$key]['layer'] = $value['layer'];
				$ArrRequest[$key]['persen'] = $value['persen'];
				$ArrRequest[$key]['terpakai'] = $TERPAKAI;
				$ArrRequest[$key]['created_by'] = $username;
				$ArrRequest[$key]['created_date'] = $datetime;

				$ArrUpdateStock[$nomor]['id'] 	= $value['actual_type'];
				$ArrUpdateStock[$nomor]['qty'] 	= $TERPAKAI;

				$ArrRequestHist[$key]['kode_spk'] = $kode_spk;
				$ArrRequestHist[$key]['kode_trans'] = $kode_trans;
				$ArrRequestHist[$key]['hist_produksi'] = $hist_produksi;
				$ArrRequestHist[$key]['id_utama'] = $id;
				$ArrRequestHist[$key]['id_material'] = $value['id_material'];
				$ArrRequestHist[$key]['actual_type'] = $value['actual_type'];
				$ArrRequestHist[$key]['layer'] = $value['layer'];
				$ArrRequestHist[$key]['persen'] = $value['persen'];
				$ArrRequestHist[$key]['terpakai'] = $TERPAKAI;
				$ArrRequestHist[$key]['created_by'] = $username;
				$ArrRequestHist[$key]['created_date'] = $datetime;
			}
		}

		//ADD MATERIAL
		$ArrEditAdd = [];
		$nomor = 999;
		if(!empty($edit_add)){
			foreach ($edit_add as $key => $value) { $nomor++;
				$TERPAKAI = str_replace(',','',$value['terpakai']);
				if($TERPAKAI > 0){
					$getLastQty = $this->db->get_where('production_spk_add',array('id'=>$value['id']))->result();
					$QTY_ADD = (!empty($getLastQty[0]->terpakai))?$getLastQty[0]->terpakai:0;
					$ArrEditAdd[$key]['id'] 			= $value['id'];
					$ArrEditAdd[$key]['terpakai'] 		= $TERPAKAI + $QTY_ADD;
					$ArrEditAdd[$key]['created_by'] 	= $username;
					$ArrEditAdd[$key]['created_date'] 	= $datetime;

					$ArrUpdateStock[$nomor]['id'] 	= $value['actual_type2'];
					$ArrUpdateStock[$nomor]['qty'] 	= $TERPAKAI;
					
					$UNIQ = '9999'.$key;
					$ArrRequestHist[$UNIQ]['kode_spk'] = $kode_spk;
					$ArrRequestHist[$UNIQ]['kode_trans'] = $kode_trans;
					$ArrRequestHist[$UNIQ]['hist_produksi'] = $hist_produksi;
					$ArrRequestHist[$UNIQ]['id_utama'] = $id;
					$ArrRequestHist[$UNIQ]['id_material'] = $getLastQty[0]->id_material;
					$ArrRequestHist[$UNIQ]['actual_type'] = $getLastQty[0]->actual_type;
					$ArrRequestHist[$UNIQ]['layer'] = $getLastQty[0]->layer;
					$ArrRequestHist[$UNIQ]['persen'] = $getLastQty[0]->persen;
					$ArrRequestHist[$UNIQ]['terpakai'] = $TERPAKAI;
					$ArrRequestHist[$UNIQ]['created_by'] = $username;
					$ArrRequestHist[$UNIQ]['created_date'] = $datetime;
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_joint','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat','resin_and_add'];
		if(!empty($ArrWhereIN_)){
			$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

			// print_r($get_detail_spk);
			// exit;
			$ArrGroup = [];
			$ArrAktualResin = [];
			$ArrAktualPlus = [];
			$ArrAktualAdd = [];
			$ArrUpdate = [];
			
			$ArrDeatil = [];
			$ArrDeatilAdj = [];
			$ArrUpdateRequest = [];
			$nomor = 0;
			$SUM_MAT = 0;
			foreach ($get_detail_spk as $key => $value) {
				foreach ($ArrLooping as $valueX) {
					if(!empty($data[$valueX])){
						if($valueX == 'detail_liner'){
							$DETAIL_NAME = 'LINER THIKNESS / CB';
						}
						if($valueX == 'detail_joint'){
							$DETAIL_NAME = 'RESIN AND ADD';
						}
						if($valueX == 'detail_strn1'){
							$DETAIL_NAME = 'STRUKTUR NECK 1';
						}
						if($valueX == 'detail_strn2'){
							$DETAIL_NAME = 'STRUKTUR NECK 2';
						}
						if($valueX == 'detail_str'){
							$DETAIL_NAME = 'STRUKTUR THICKNESS';
						}
						if($valueX == 'detail_ext'){
							$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
						}
						if($valueX == 'detail_topcoat'){
							$DETAIL_NAME = 'TOPCOAT';
						}
						if($valueX == 'resin_and_add'){
							$DETAIL_NAME = 'RESIN AND ADD';
						}
						$detailX = $data[$valueX];
						// print_r($detailX);

						if($value['id_product'] != 'deadstok'){
							$get_produksi 	= $this->db->limit(1)->select('id, id_category')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk']))->result();
							//,'print_merge_date'=>$dateCreated
							if(empty($get_produksi)){
								$Arr_Kembali	= array(
									'pesan'		=>'Error data proccess, please contact administrator !!! ErrorCode: id_ml:'.$value['id_milik'].'&spk:'.$value['kode_spk'].'&tm:'.$dateCreated,
									'status'	=> 2
								);
								// cari di 	production_detail bedasarkan id milik dan kode spk, ganti waktu seperti di alert
								echo json_encode($Arr_Kembali);
								return false;
							}
						}

						foreach ($detailX as $key2 => $value2) {
							$get_liner 		= $this->db->select('id, id_material, qty_order AS berat, key_gudang, check_qty_oke')->get_where('warehouse_adjustment_detail', array('kode_trans'=>$kode_trans,'keterangan'=>$DETAIL_NAME))->result_array();
							// print_r($get_liner);
							// exit;
							if(!empty($get_liner)){
								foreach ($get_liner as $key3 => $value3) {
									if($value2['id_key'] == $value3['key_gudang']){ 
										$nomor 		= $value2['id_key'];
										$ACTUAL_MAT = $value2['actual_type'];
										$QTY_INP	= (!empty($WHERE_KEY_QTY[$value['id']]))?$WHERE_KEY_QTY[$value['id']]:0;
										
										$total_est  = 0;
										$total_act  = 0;
										$BERAT_UNIT  = 0;

										if($value3['berat'] > 0 AND $QTY_INP > 0){
										$BERAT_UNIT = $value3['berat'] / $QTY_INP;
										}
										if($BERAT_UNIT > 0 AND $QTY_INP > 0){
										$total_est 	= $BERAT_UNIT * $QTY_INP;
										}

										$kebutuhan = (!empty($value2['kebutuhan']))?(float)str_replace(',','',$value2['kebutuhan']):0;
										if($kebutuhan > 0){
											$total_act = 0;
											if($total_est > 0 AND $kebutuhan > 0){
												$total_act 	= ($total_est / $kebutuhan) * (float) str_replace(',','',$value2['terpakai']);
											}
										}
										$SUM_MAT 	+= $total_act;

										$unit_act 	= 0;
										if($total_act > 0 AND $QTY_INP > 0){
											$unit_act 	= $total_act / $QTY_INP;
										}
										$PERSEN 	= str_replace(',','',$value2['persen']);
										if(!empty($PERSEN) AND $PERSEN > 0){
											$PERSEN 	= str_replace(',','',$value2['persen']);
										}
										else{
											$KEY        = $kode_trans.'-'.$value2['id_key'];
											$PERSEN 	= (!empty($GET_PERCENT[$KEY]['persen']) AND $GET_PERCENT[$KEY]['persen'] > 0)?$GET_PERCENT[$KEY]['persen']:'';;
										}
										//UPDATE FLAG SPK 2
										$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
										$ArrUpdate[$key.$key2.$key3.$nomor]['spk2'] 		= 'Y';
										$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_by'] 		= $username;
										$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_date']	= $datetime;
										// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

										//ARRAY STOCK
										$ArrUpdateStock[$nomor]['id'] 	= $ACTUAL_MAT;
										$ArrUpdateStock[$nomor]['qty'] 	= $total_act;
										//UPDATE ADJUSTMENT DETAIL
										$ArrDeatil[$key.$key2.$key3.$nomor]['id'] 			    = $value3['id'];
										$ArrDeatil[$key.$key2.$key3.$nomor]['id_material'] 		= $ACTUAL_MAT;
										$ArrDeatil[$key.$key2.$key3.$nomor]['qty_rusak'] 		= $PERSEN;
										$ArrDeatil[$key.$key2.$key3.$nomor]['check_qty_oke'] 	= $total_act + $value3['check_qty_oke'];
										$ArrDeatil[$key.$key2.$key3.$nomor]['check_qty_rusak']	= $BERAT_UNIT;
										$ArrDeatil[$key.$key2.$key3.$nomor]['check_keterangan']	= $DETAIL_NAME;
										$ArrDeatil[$key.$key2.$key3.$nomor]['update_by'] 		= $username;
										$ArrDeatil[$key.$key2.$key3.$nomor]['update_date'] 		= $datetime;
										//INSERT ADJUSTMENT CHECK
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['kode_trans'] 	= $kode_trans;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['no_ipp'] 		= $no_request;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material'] 	= $ACTUAL_MAT;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_material'] 	= $GET_MATERIAL[$ACTUAL_MAT]['nm_material'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_category'] 	= $GET_MATERIAL[$ACTUAL_MAT]['id_category'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_category'] 	= $GET_MATERIAL[$ACTUAL_MAT]['nm_category'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_order'] 	= $value3['berat'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_oke'] 		= $total_act;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_rusak'] 	= $PERSEN;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['expired_date'] 	= NULL;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['keterangan'] 	= $DETAIL_NAME;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_by'] 	= $username;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_date'] 	= $datetime;

										//UPDATE REQUEST
										$ArrUpdateRequest[$key.$key2.$key3.$nomor]['id_key'] 	= $value3['key_gudang'];
										$ArrUpdateRequest[$key.$key2.$key3.$nomor]['aktual'] 	= $total_act;
									}
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		$tempx = [];
		$grouping_temp = [];
		foreach($ArrUpdateStock as $value) {
			if($value['qty'] > 0){
				if(!array_key_exists($value['id'], $temp)) {
					$temp[$value['id']] = 0;
				}
				$temp[$value['id']] += $value['qty'];
			}
		}
		
		//Mengurangi Booking
		$getDetailSPK 	= $this->db->get_where('production_spk',array('kode_spk'=>$kode_spk))->result();
		$no_ipp 		= (!empty($getDetailSPK[0]->no_ipp))?$getDetailSPK[0]->no_ipp:0;

		$id_gudang_booking = 2;
		$GETS_STOCK = get_warehouseStockAllMaterial();
		$CHECK_BOOK = get_CheckBooking($no_ipp);
		$ArrUpdate 		= [];
		$ArrUpdateHist 	= [];
		// print_r($temp);
		if(!empty($temp)){
			if($CHECK_BOOK === TRUE AND $no_ipp != 0){
				// echo 'Masuk';
				foreach ($temp as $material => $qty) {
					$KEY 		= $material.'-'.$id_gudang_booking;
					$booking 	= (!empty($GETS_STOCK[$KEY]['booking']))?$GETS_STOCK[$KEY]['booking']:0;
					$stock 		= (!empty($GETS_STOCK[$KEY]['stock']))?$GETS_STOCK[$KEY]['stock']:0;
					$rusak 		= (!empty($GETS_STOCK[$KEY]['rusak']))?$GETS_STOCK[$KEY]['rusak']:0;
					$id_stock 	= (!empty($GETS_STOCK[$KEY]['id']))?$GETS_STOCK[$KEY]['id']:null;
					$idmaterial 	= (!empty($GETS_STOCK[$KEY]['idmaterial']))?$GETS_STOCK[$KEY]['idmaterial']:null;
					$nm_material 	= (!empty($GETS_STOCK[$KEY]['nm_material']))?$GETS_STOCK[$KEY]['nm_material']:null;
					$id_category 	= (!empty($GETS_STOCK[$KEY]['id_category']))?$GETS_STOCK[$KEY]['id_category']:null;
					$nm_category 	= (!empty($GETS_STOCK[$KEY]['nm_category']))?$GETS_STOCK[$KEY]['nm_category']:null;
					// echo 'ID:'.$id_stock;
					if(!empty($id_stock)){
						$ArrUpdate[$material]['id'] = $id_stock;
						$ArrUpdate[$material]['qty_booking'] = $booking - $qty;

						$ArrUpdateHist[$material]['id_material'] 	= $material;
						$ArrUpdateHist[$material]['idmaterial'] 	= $idmaterial;
						$ArrUpdateHist[$material]['nm_material'] 	= $nm_material;
						$ArrUpdateHist[$material]['id_category'] 	= $id_category;
						$ArrUpdateHist[$material]['nm_category'] 	= $nm_category;
						$ArrUpdateHist[$material]['id_gudang'] 		= $id_gudang_booking;
						$ArrUpdateHist[$material]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_booking);
						$ArrUpdateHist[$material]['id_gudang_dari'] = NULL;
						$ArrUpdateHist[$material]['kd_gudang_dari'] = 'BOOKING';
						$ArrUpdateHist[$material]['id_gudang_ke'] 	= NULL;
						$ArrUpdateHist[$material]['kd_gudang_ke'] 	= 'PENGURANG';
						$ArrUpdateHist[$material]['qty_stock_awal'] 	= $stock;
						$ArrUpdateHist[$material]['qty_stock_akhir'] 	= $stock;
						$ArrUpdateHist[$material]['qty_booking_awal'] 	= $booking;
						$ArrUpdateHist[$material]['qty_booking_akhir'] 	= $booking - $qty;
						$ArrUpdateHist[$material]['qty_rusak_awal'] 	= $rusak;
						$ArrUpdateHist[$material]['qty_rusak_akhir'] 	= $rusak;
						$ArrUpdateHist[$material]['no_ipp'] 			= $no_ipp;
						$ArrUpdateHist[$material]['jumlah_mat'] 		= $qty;
						$ArrUpdateHist[$material]['ket'] 				= 'pengurangan booking '.$kode_trans;
						$ArrUpdateHist[$material]['update_by'] 			= $username;
						$ArrUpdateHist[$material]['update_date'] 		= $datetime;
					}
				}
			}
		}

		// print_r($ArrUpdate);
		// print_r($ArrUpdateHist);
		// exit;

		//ENd Mengurangi Booking
		if(!empty($ArrUpdateStock)){
			foreach($ArrUpdateStock as $value) {
				if(!array_key_exists($value['id'], $tempx)) {
					$tempx[$value['id']]['good'] = 0;
				}
				$tempx[$value['id']]['good'] += $value['qty'];

				$grouping_temp[$value['id']]['id'] 			= $value['id'];
				$grouping_temp[$value['id']]['qty_good'] 	= $tempx[$value['id']]['good'];
				
				
				$id_material = $value['id'];

					$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang))->row();
					$coa_gudang = $coa_1->coa_1;
					$kategori_gudang = $coa_1->category;				 
						
						$stokjurnalakhir=0;
					$nilaijurnalakhir=0;
					$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
					
					$tanggal		= date('Y-m-d');
					$Bln 			= substr($tanggal,5,2);
					$Thn 			= substr($tanggal,0,4);
					$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);

					$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir)) $PRICE=$harga_jurnal_akhir->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir)) $PRICE=$harga_jurnal_akhir->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir)) $PRICE=$harga_jurnal_akhir->harga;			
					
				}
					
					$QTY_OKE  = $tempx[$value['id']]['good'];
					$ACTUAL_MAT = $id_material;
					
					$ArrJurnalNew[$value['id']]['id_material'] 		= $ACTUAL_MAT;
					$ArrJurnalNew[$value['id']]['idmaterial'] 		= $GET_MATERIAL[$ACTUAL_MAT]['idmaterial'];
					$ArrJurnalNew[$value['id']]['nm_material'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_material'];
					$ArrJurnalNew[$value['id']]['id_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['id_category'];
					$ArrJurnalNew[$value['id']]['nm_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_category'];
					$ArrJurnalNew[$value['id']]['id_gudang'] 			= $id_gudang;
					$ArrJurnalNew[$value['id']]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
					$ArrJurnalNew[$value['id']]['id_gudang_dari'] 	    = $id_gudang;
					$ArrJurnalNew[$value['id']]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
					$ArrJurnalNew[$value['id']]['id_gudang_ke'] 		= $id_gudang_wip;
					$ArrJurnalNew[$value['id']]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
					$ArrJurnalNew[$value['id']]['qty_stock_awal'] 		= $stokjurnalakhir;
					$ArrJurnalNew[$value['id']]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
					$ArrJurnalNew[$value['id']]['kode_trans'] 			= $kode_trans;
					$ArrJurnalNew[$value['id']]['tgl_trans'] 			= $DateTime;
					$ArrJurnalNew[$value['id']]['qty_out'] 			= $QTY_OKE;
					$ArrJurnalNew[$value['id']]['ket'] 				= 'transfer subgudang - produksi out';
					$ArrJurnalNew[$value['id']]['harga'] 			= $PRICE;
					$ArrJurnalNew[$value['id']]['harga_bm'] 		= 0;
					$ArrJurnalNew[$value['id']]['nilai_awal_rp']	= $nilaijurnalakhir;
					$ArrJurnalNew[$value['id']]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
					$ArrJurnalNew[$value['id']]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
					$ArrJurnalNew[$value['id']]['update_by'] 		= $UserName;
					$ArrJurnalNew[$value['id']]['update_date'] 		= $DateTime;
					$ArrJurnalNew[$value['id']]['no_jurnal'] 		= $Nojurnal;
					$ArrJurnalNew[$value['id']]['coa_gudang'] 		= $coa_gudang;
					
					
					$stokjurnalakhir2=0;
					$nilaijurnalakhir2=0;
					$stok_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir2)) $stokjurnalakhir2=$stok_jurnal_akhir2->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir2)) $nilaijurnalakhir2=$stok_jurnal_akhir2->nilai_akhir_rp;
					
					$coa_2    = $this->db->get_where('warehouse', array('id'=>$id_gudang_wip))->row();
					$coa_gudang2 = $coa_2->coa_1;
					$kategori_gudang2 = $coa_2->category;	
					
					
					$GudangFrom2 = $kategori_gudang2;
				if($GudangFrom2 == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir)) $PRICE2=$harga_jurnal_akhir->harga;


				}elseif($GudangFrom2 == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir)) $PRICE2=$harga_jurnal_akhir->harga;
		
				}elseif($GudangFrom2 == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir)) $PRICE2=$harga_jurnal_akhir->harga;			
					
				}
					
					$stok_akhir = $nilaijurnalakhir2+($PRICE*$QTY_OKE);
					
					if($stok_akhir==0){
						$PRICENEW = 0;
					} else{
					$PRICENEW = $stok_akhir/($QTY_OKE+$stokjurnalakhir2);
					}
					
					
					
					
					$ArrJurnalNew2[$value['id']]['id_material'] 		= $ACTUAL_MAT;
					$ArrJurnalNew2[$value['id']]['idmaterial'] 		    = $GET_MATERIAL[$ACTUAL_MAT]['idmaterial'];
					$ArrJurnalNew2[$value['id']]['nm_material'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_material'];
					$ArrJurnalNew2[$value['id']]['id_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['id_category'];
					$ArrJurnalNew2[$value['id']]['nm_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_category'];
					$ArrJurnalNew2[$value['id']]['id_gudang'] 			= $id_gudang_wip;
					$ArrJurnalNew2[$value['id']]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
					$ArrJurnalNew2[$value['id']]['id_gudang_dari'] 	    = $id_gudang;
					$ArrJurnalNew2[$value['id']]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
					$ArrJurnalNew2[$value['id']]['id_gudang_ke'] 		= $id_gudang_wip;
					$ArrJurnalNew2[$value['id']]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
					$ArrJurnalNew2[$value['id']]['qty_stock_awal']     	= $stokjurnalakhir2;
					$ArrJurnalNew2[$value['id']]['qty_stock_akhir'] 	= $stokjurnalakhir2+$QTY_OKE;
					$ArrJurnalNew2[$value['id']]['kode_trans'] 		    = $kode_trans;
					$ArrJurnalNew2[$value['id']]['tgl_trans'] 			= $DateTime;
					$ArrJurnalNew2[$value['id']]['qty_in'] 			    = $QTY_OKE;
					$ArrJurnalNew2[$value['id']]['ket'] 				= 'transfer subgudang - produksi in';
					$ArrJurnalNew2[$value['id']]['harga'] 				= $PRICENEW;
					$ArrJurnalNew2[$value['id']]['harga_bm'] 			= 0;
					$ArrJurnalNew2[$value['id']]['nilai_awal_rp']		= $nilaijurnalakhir2;
					$ArrJurnalNew2[$value['id']]['nilai_trans_rp']		= $PRICE*$QTY_OKE;
					$ArrJurnalNew2[$value['id']]['nilai_akhir_rp']		= $nilaijurnalakhir2+($PRICE*$QTY_OKE);
					$ArrJurnalNew2[$value['id']]['update_by'] 			= $UserName;
					$ArrJurnalNew2[$value['id']]['update_date'] 		= $DateTime;
					$ArrJurnalNew2[$value['id']]['no_jurnal'] 			= '-';
					$ArrJurnalNew2[$value['id']]['coa_gudang'] 		    = $coa_gudang2;
			}

			move_warehouse($ArrUpdateStock,$id_gudang,$id_gudang_wip,$kode_trans);
		}
		//UPDATE NOMOR SURAT JALAN
		$monthYear 		= date('/m/Y');
		$kode_gudang 	= get_name('warehouse', 'kode', 'id', $id_gudang);

		$getDetAjust 	= $this->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();

		$qIPP			= "SELECT MAX(no_surat_jalan) as maxP FROM warehouse_adjustment WHERE no_surat_jalan LIKE '%/IA".$kode_gudang.$monthYear."' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 0, 3);
		$urutan2++;
		$urut2			= sprintf('%03s',$urutan2);
		$no_surat_jalan	= $urut2."/IA".$kode_gudang.$monthYear;

		$ArrUpdateHeadAjudtment2 = array(
			'jumlah_mat_check' => $getDetAjust[0]->jumlah_mat_check + $SUM_MAT,
			'no_surat_jalan' => $no_surat_jalan,
			'checked_by' => $username,
			'checked_date' => $datetime
		);

		$ArrUpdateHeadAjudtment = array_merge($ArrEndChange,$ArrUpdateHeadAjudtment2);

		$UpdateRealFlag = array(
			'upload_real2' => "Y",
			'upload_by2' =>  $username,
			'upload_date2' => $datetime
		);

		$UpdatePrintHeader = array(
			'aktual_by' =>  $username,
			'aktual_date' => $datetime
		);

		$this->db->trans_start();

			if(!empty($grouping_temp)){
				insert_jurnal($grouping_temp,$id_gudang,$id_gudang_wip,$kode_trans,'transfer subgudang - produksi','pengurangan subgudang','penambahan gudang produksi');
			}

			$this->db->where('kode_trans', $kode_trans);
			$this->db->update('warehouse_adjustment', $ArrUpdateHeadAjudtment);
			
			$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew);
				 
			 if(!empty($ArrJurnalNew2)){
				 $this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew2);
			}

			if(!empty($ArrDeatil)){
				$this->db->update_batch('warehouse_adjustment_detail', $ArrDeatil, 'id');
			}

			if(!empty($ArrDeatilAdj)){
				$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilAdj);
			}

			if(!empty($ArrUpdate)){
				$this->db->update_batch('warehouse_stock', $ArrUpdate, 'id');
				$this->db->insert_batch('warehouse_history', $ArrUpdateHist);
			}

			if(!empty($ArrRequest)){
				$this->db->insert_batch('production_spk_add',$ArrRequest);
			}

			if(!empty($ArrEditAdd)){
				$this->db->update_batch('production_spk_add',$ArrEditAdd,'id');
			}

			if(!empty($ArrRequestHist)){
				$this->db->insert_batch('production_spk_add_hist',$ArrRequestHist);
			}

			if(!empty($ArrUpdateRequest)){
				$this->db->where('kode_uniq', $no_request);
				$this->db->update_batch('print_detail',$ArrUpdateRequest,'id_key');
			}
			$this->db->where('kode_uniq', $no_request);
			$this->db->update('print_header', $UpdatePrintHeader);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $id
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $id
			);
			if(!empty($ArrUpdateStock)){
			insertDataGroupReport($ArrUpdateStock,$id_gudang,$id_gudang_wip,$kode_trans,$xyz_no_so,$xyz_no_spk,$xyz_product);
			}
			history('Approve request producksi '.$kode_spk.'/'.$kode_trans.'/'.$id);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_2_new_close(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$id				= $data['id'];
		$kode_trans		= $data['kode_trans'];
		$kode_spk 		= $data['kode_spk'];
		$hist_produksi	= $data['hist_produksi'];
		$id_gudang 		= $data['id_gudang_from'];
		$id_gudang_wip 	= $data['id_gudang'];
		$no_request 	= $data['no_request'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$requesta_add 	= (!empty($data['requesta_add']))?$data['requesta_add']:array();
		$edit_add 		= (!empty($data['edit_add']))?$data['edit_add']:array();
		$GET_MATERIAL	= get_detail_material();
		$xyz_no_spk 	= $data['xyz_no_spk'];
		$xyz_no_so 		= $data['xyz_no_so'];
		$xyz_product 	= $data['xyz_product'];

		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		$ArrEndChange = [];
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_req_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}

			$ArrEndChange = array(
				'file_eng_change' 	=> $file_name
			);
		}

		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
			}
		}
		$ArrUpdateStock = [];
		//ADD MATERIAL
		$ArrRequestHist = [];
		$ArrRequest = [];
		$nomor = 999;
		if(!empty($requesta_add)){
			foreach ($requesta_add as $key => $value) { $nomor++;
				$TERPAKAI = str_replace(',','',$value['terpakai']);
				$ArrRequest[$key]['kode_spk'] = $kode_spk;
				$ArrRequest[$key]['kode_trans'] = $kode_trans;
				$ArrRequest[$key]['hist_produksi'] = $hist_produksi;
				$ArrRequest[$key]['id_utama'] = $id;
				$ArrRequest[$key]['id_material'] = $value['id_material'];
				$ArrRequest[$key]['actual_type'] = $value['actual_type'];
				$ArrRequest[$key]['layer'] = $value['layer'];
				$ArrRequest[$key]['persen'] = $value['persen'];
				$ArrRequest[$key]['terpakai'] = $TERPAKAI;
				$ArrRequest[$key]['created_by'] = $username;
				$ArrRequest[$key]['created_date'] = $datetime;

				$ArrUpdateStock[$nomor]['id'] 	= $value['actual_type'];
				$ArrUpdateStock[$nomor]['qty'] 	= $TERPAKAI;

				$ArrRequestHist[$key]['kode_spk'] = $kode_spk;
				$ArrRequestHist[$key]['kode_trans'] = $kode_trans;
				$ArrRequestHist[$key]['hist_produksi'] = $hist_produksi;
				$ArrRequestHist[$key]['id_utama'] = $id;
				$ArrRequestHist[$key]['id_material'] = $value['id_material'];
				$ArrRequestHist[$key]['actual_type'] = $value['actual_type'];
				$ArrRequestHist[$key]['layer'] = $value['layer'];
				$ArrRequestHist[$key]['persen'] = $value['persen'];
				$ArrRequestHist[$key]['terpakai'] = $TERPAKAI;
				$ArrRequestHist[$key]['created_by'] = $username;
				$ArrRequestHist[$key]['created_date'] = $datetime;
			}
		}

		//ADD MATERIAL
		$ArrEditAdd = [];
		$nomor = 999;
		if(!empty($edit_add)){
			foreach ($edit_add as $key => $value) { $nomor++;
				$TERPAKAI = str_replace(',','',$value['terpakai']);
				if($TERPAKAI > 0){
					$getLastQty = $this->db->get_where('production_spk_add',array('id'=>$value['id']))->result();
					$QTY_ADD = (!empty($getLastQty[0]->terpakai))?$getLastQty[0]->terpakai:0;
					$ArrEditAdd[$key]['id'] 			= $value['id'];
					$ArrEditAdd[$key]['terpakai'] 		= $TERPAKAI + $QTY_ADD;
					$ArrEditAdd[$key]['created_by'] 	= $username;
					$ArrEditAdd[$key]['created_date'] 	= $datetime;

					$ArrUpdateStock[$nomor]['id'] 	= $value['actual_type2'];
					$ArrUpdateStock[$nomor]['qty'] 	= $TERPAKAI;

					$UNIQ = '9999'.$key;
					$ArrRequestHist[$UNIQ]['kode_spk'] = $kode_spk;
					$ArrRequestHist[$UNIQ]['kode_trans'] = $kode_trans;
					$ArrRequestHist[$UNIQ]['hist_produksi'] = $hist_produksi;
					$ArrRequestHist[$UNIQ]['id_utama'] = $id;
					$ArrRequestHist[$UNIQ]['id_material'] = $getLastQty[0]->id_material;
					$ArrRequestHist[$UNIQ]['actual_type'] = $getLastQty[0]->actual_type;
					$ArrRequestHist[$UNIQ]['layer'] = $getLastQty[0]->layer;
					$ArrRequestHist[$UNIQ]['persen'] = $getLastQty[0]->persen;
					$ArrRequestHist[$UNIQ]['terpakai'] = $TERPAKAI;
					$ArrRequestHist[$UNIQ]['created_by'] = $username;
					$ArrRequestHist[$UNIQ]['created_date'] = $datetime;
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_joint','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat','resin_and_add'];

		if(!empty($ArrWhereIN_)){
			$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

			// print_r($get_detail_spk);
			// exit;
			$ArrGroup = [];
			$ArrAktualResin = [];
			$ArrAktualPlus = [];
			$ArrAktualAdd = [];
			$ArrUpdate = [];
			
			$ArrDeatil = [];
			$ArrDeatilAdj = [];
			$ArrUpdateRequest = [];
			$nomor = 0;
			$SUM_MAT = 0;
			foreach ($get_detail_spk as $key => $value) {
				foreach ($ArrLooping as $valueX) {
					if(!empty($data[$valueX])){
						if($valueX == 'detail_liner'){
							$DETAIL_NAME = 'LINER THIKNESS / CB';
						}
						if($valueX == 'detail_joint'){
							$DETAIL_NAME = 'RESIN AND ADD';
						}
						if($valueX == 'detail_strn1'){
							$DETAIL_NAME = 'STRUKTUR NECK 1';
						}
						if($valueX == 'detail_strn2'){
							$DETAIL_NAME = 'STRUKTUR NECK 2';
						}
						if($valueX == 'detail_str'){
							$DETAIL_NAME = 'STRUKTUR THICKNESS';
						}
						if($valueX == 'detail_ext'){
							$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
						}
						if($valueX == 'detail_topcoat'){
							$DETAIL_NAME = 'TOPCOAT';
						}
						if($valueX == 'resin_and_add'){
							$DETAIL_NAME = 'RESIN AND ADD';
						}
						$detailX = $data[$valueX];
						// print_r($detailX);
						if($value['id_product'] != 'deadstok'){
							$get_produksi 	= $this->db->limit(1)->select('id, id_category')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk']))->result();
							//,'print_merge_date'=>$dateCreated
							if(empty($get_produksi)){
								$Arr_Kembali	= array(
									'pesan'		=>'Error data proccess, please contact administrator !!! ErrorCode: id_ml:'.$value['id_milik'].'&spk:'.$value['kode_spk'].'&tm:'.$dateCreated,
									'status'	=> 2
								);
								echo json_encode($Arr_Kembali);
								return false;
							}
						}
						
						foreach ($detailX as $key2 => $value2) {
							//RESIN
							$get_liner 		= $this->db->select('id, id_material, qty_order AS berat, key_gudang, check_qty_oke')->get_where('warehouse_adjustment_detail', array('kode_trans'=>$kode_trans,'keterangan'=>$DETAIL_NAME))->result_array();
							// print_r($get_liner);
							// exit;
							if(!empty($get_liner)){
								foreach ($get_liner as $key3 => $value3) {
									if($value2['id_key'] == $value3['key_gudang']){ 
										$nomor 		= $value2['id_key'];
										$ACTUAL_MAT = $value2['actual_type'];
										$QTY_INP	= (!empty($WHERE_KEY_QTY[$value['id']]))?$WHERE_KEY_QTY[$value['id']]:0;
										
										$total_est  = 0;
										$total_act  = 0;
										$BERAT_UNIT  = 0;

										if($value3['berat'] > 0 AND $QTY_INP > 0){
										$BERAT_UNIT = $value3['berat'] / $QTY_INP;
										}
										if($BERAT_UNIT > 0 AND $QTY_INP > 0){
										$total_est 	= $BERAT_UNIT * $QTY_INP;
										}

										$kebutuhan = (!empty($value2['kebutuhan']))?(float)str_replace(',','',$value2['kebutuhan']):0;
										if($kebutuhan > 0){
											$total_act = 0;
											if($total_est > 0 AND $kebutuhan > 0){
												$total_act 	= ($total_est / $kebutuhan) * (float)str_replace(',','',$value2['terpakai']);
											}
										}
										$SUM_MAT 	+= $total_act;
										$unit_act 	= 0;
										if($total_act > 0 AND $QTY_INP > 0){
											$unit_act 	= $total_act / $QTY_INP;
										}
										$PERSEN = str_replace(',','',$value2['persen']);
										//UPDATE FLAG SPK 2
										$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
										$ArrUpdate[$key.$key2.$key3.$nomor]['spk2'] 		= 'Y';
										$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_by'] 		= $username;
										$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_date']	= $datetime;
										// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

										//ARRAY STOCK
										$ArrUpdateStock[$nomor]['id'] 	= $ACTUAL_MAT;
										$ArrUpdateStock[$nomor]['qty'] 	= $total_act;
										//UPDATE ADJUSTMENT DETAIL
										$ArrDeatil[$key.$key2.$key3.$nomor]['id'] 			    = $value3['id'];
										$ArrDeatil[$key.$key2.$key3.$nomor]['id_material'] 		= $ACTUAL_MAT;
										$ArrDeatil[$key.$key2.$key3.$nomor]['check_qty_oke'] 	= $total_act + $value3['check_qty_oke'];
										$ArrDeatil[$key.$key2.$key3.$nomor]['check_qty_rusak']	= $BERAT_UNIT;
										$ArrDeatil[$key.$key2.$key3.$nomor]['check_keterangan']	= $DETAIL_NAME;
										$ArrDeatil[$key.$key2.$key3.$nomor]['update_by'] 		= $username;
										$ArrDeatil[$key.$key2.$key3.$nomor]['update_date'] 		= $datetime;
										//INSERT ADJUSTMENT CHECK
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['kode_trans'] 	= $kode_trans;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['no_ipp'] 		= $no_request;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material'] 	= $ACTUAL_MAT;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_material'] 	= $GET_MATERIAL[$ACTUAL_MAT]['nm_material'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_category'] 	= $GET_MATERIAL[$ACTUAL_MAT]['id_category'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_category'] 	= $GET_MATERIAL[$ACTUAL_MAT]['nm_category'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_order'] 	= $value3['berat'];
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_oke'] 		= $total_act;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_rusak'] 	= 0;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['expired_date'] 	= NULL;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['keterangan'] 	= $DETAIL_NAME;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_by'] 	= $username;
										$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_date'] 	= $datetime;
										
										//UPDATE REQUEST
										$ArrUpdateRequest[$key.$key2.$key3.$nomor]['id_key'] 	= $value3['key_gudang'];
										$ArrUpdateRequest[$key.$key2.$key3.$nomor]['aktual'] 	= $total_act;
									}
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		$tempx = [];
		$grouping_temp = [];
		if(!empty($ArrUpdateStock)){
			foreach($ArrUpdateStock as $value) {
				if($value['qty'] > 0){
					if(!array_key_exists($value['id'], $temp)) {
						$temp[$value['id']] = 0;
					}
					$temp[$value['id']] += $value['qty'];
				}
			}
		}
		
		//Mengurangi Booking
		$getDetailSPK 	= $this->db->get_where('production_spk',array('kode_spk'=>$kode_spk))->result();
		$no_ipp 		= (!empty($getDetailSPK[0]->no_ipp))?$getDetailSPK[0]->no_ipp:0;

		$id_gudang_booking = 2;
		$GETS_STOCK = get_warehouseStockAllMaterial();
		$CHECK_BOOK = get_CheckBooking($no_ipp);
		$ArrUpdate 		= [];
		$ArrUpdateHist 	= [];
		// print_r($temp);
		if(!empty($temp)){
			if($CHECK_BOOK === TRUE AND $no_ipp != 0){
				// echo 'Masuk';
				foreach ($temp as $material => $qty) {
					$KEY 		= $material.'-'.$id_gudang_booking;
					$booking 	= (!empty($GETS_STOCK[$KEY]['booking']))?$GETS_STOCK[$KEY]['booking']:0;
					$stock 		= (!empty($GETS_STOCK[$KEY]['stock']))?$GETS_STOCK[$KEY]['stock']:0;
					$rusak 		= (!empty($GETS_STOCK[$KEY]['rusak']))?$GETS_STOCK[$KEY]['rusak']:0;
					$id_stock 	= (!empty($GETS_STOCK[$KEY]['id']))?$GETS_STOCK[$KEY]['id']:null;
					$idmaterial 	= (!empty($GETS_STOCK[$KEY]['idmaterial']))?$GETS_STOCK[$KEY]['idmaterial']:null;
					$nm_material 	= (!empty($GETS_STOCK[$KEY]['nm_material']))?$GETS_STOCK[$KEY]['nm_material']:null;
					$id_category 	= (!empty($GETS_STOCK[$KEY]['id_category']))?$GETS_STOCK[$KEY]['id_category']:null;
					$nm_category 	= (!empty($GETS_STOCK[$KEY]['nm_category']))?$GETS_STOCK[$KEY]['nm_category']:null;
					// echo 'ID:'.$id_stock;
					if(!empty($id_stock)){
						$ArrUpdate[$material]['id'] = $id_stock;
						$ArrUpdate[$material]['qty_booking'] = $booking - $qty;

						$ArrUpdateHist[$material]['id_material'] 	= $material;
						$ArrUpdateHist[$material]['idmaterial'] 	= $idmaterial;
						$ArrUpdateHist[$material]['nm_material'] 	= $nm_material;
						$ArrUpdateHist[$material]['id_category'] 	= $id_category;
						$ArrUpdateHist[$material]['nm_category'] 	= $nm_category;
						$ArrUpdateHist[$material]['id_gudang'] 		= $id_gudang_booking;
						$ArrUpdateHist[$material]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_booking);
						$ArrUpdateHist[$material]['id_gudang_dari'] = NULL;
						$ArrUpdateHist[$material]['kd_gudang_dari'] = 'BOOKING';
						$ArrUpdateHist[$material]['id_gudang_ke'] 	= NULL;
						$ArrUpdateHist[$material]['kd_gudang_ke'] 	= 'PENGURANG';
						$ArrUpdateHist[$material]['qty_stock_awal'] 	= $stock;
						$ArrUpdateHist[$material]['qty_stock_akhir'] 	= $stock;
						$ArrUpdateHist[$material]['qty_booking_awal'] 	= $booking;
						$ArrUpdateHist[$material]['qty_booking_akhir'] 	= $booking - $qty;
						$ArrUpdateHist[$material]['qty_rusak_awal'] 	= $rusak;
						$ArrUpdateHist[$material]['qty_rusak_akhir'] 	= $rusak;
						$ArrUpdateHist[$material]['no_ipp'] 			= $no_ipp;
						$ArrUpdateHist[$material]['jumlah_mat'] 		= $qty;
						$ArrUpdateHist[$material]['ket'] 				= 'pengurangan booking '.$kode_trans.' close';
						$ArrUpdateHist[$material]['update_by'] 			= $username;
						$ArrUpdateHist[$material]['update_date'] 		= $datetime;
					}
				}
			}
		}

		// print_r($ArrUpdate);
		// print_r($ArrUpdateHist);
		// exit;

		//ENd Mengurangi Booking
		if(!empty($ArrUpdateStock)){
			foreach($ArrUpdateStock as $value) {
				if(!array_key_exists($value['id'], $tempx)) {
					$tempx[$value['id']]['good'] = 0;
				}
				$tempx[$value['id']]['good'] += $value['qty'];

				$grouping_temp[$value['id']]['id'] 			= $value['id'];
				$grouping_temp[$value['id']]['qty_good'] 	= $tempx[$value['id']]['good'];
			}

			move_warehouse($ArrUpdateStock,$id_gudang,$id_gudang_wip,$kode_trans);
		}
		//UPDATE NOMOR SURAT JALAN
		$monthYear 		= date('/m/Y');
		$kode_gudang 	= get_name('warehouse', 'kode', 'id', $id_gudang);

		$getDetAjust 	= $this->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();

		$qIPP			= "SELECT MAX(no_surat_jalan) as maxP FROM warehouse_adjustment WHERE no_surat_jalan LIKE '%/IA".$kode_gudang.$monthYear."' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 0, 3);
		$urutan2++;
		$urut2			= sprintf('%03s',$urutan2);
		$no_surat_jalan	= $urut2."/IA".$kode_gudang.$monthYear;

		$ArrUpdateHeadAjudtment2 = array(
			'checked' => 'Y',
			'jumlah_mat_check' => $getDetAjust[0]->jumlah_mat_check + $SUM_MAT,
			'no_surat_jalan' => $no_surat_jalan,
			'checked_by' => $username,
			'checked_date' => $datetime
		);

		$ArrUpdateHeadAjudtment = array_merge($ArrEndChange,$ArrUpdateHeadAjudtment2);

		$UpdateRealFlag = array(
			'upload_real2' => "Y",
			'upload_by2' =>  $username,
			'upload_date2' => $datetime
		);

		$UpdatePrintHeader = array(
			'aktual_by' =>  $username,
			'aktual_date' => $datetime
		);

		$this->db->trans_start();

			if(!empty($grouping_temp)){
				insert_jurnal($grouping_temp,$id_gudang,$id_gudang_wip,$kode_trans,'transfer subgudang - produksi','pengurangan subgudang','penambahan gudang produksi');
			}

			$this->db->where('kode_trans', $kode_trans);
			$this->db->update('warehouse_adjustment', $ArrUpdateHeadAjudtment);

			if(!empty($ArrDeatil)){
				$this->db->update_batch('warehouse_adjustment_detail', $ArrDeatil, 'id');
			}

			if(!empty($ArrDeatilAdj)){
				$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilAdj);
			}

			if(!empty($ArrRequest)){
				$this->db->insert_batch('production_spk_add',$ArrRequest);
			}

			if(!empty($ArrUpdate)){
				$this->db->update_batch('warehouse_stock', $ArrUpdate, 'id');
				$this->db->insert_batch('warehouse_history', $ArrUpdateHist);
			}

			if(!empty($ArrEditAdd)){
				$this->db->update_batch('production_spk_add',$ArrEditAdd,'id');
			}

			if(!empty($ArrRequestHist)){
				$this->db->insert_batch('production_spk_add_hist',$ArrRequestHist);
			}

			if(!empty($ArrUpdateRequest)){
				$this->db->where('kode_uniq', $no_request);
				$this->db->update_batch('print_detail',$ArrUpdateRequest,'id_key');
			}
			$this->db->where('kode_uniq', $no_request);
			$this->db->update('print_header', $UpdatePrintHeader);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $id
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $id
			);
			if(!empty($ArrUpdateStock)){
			insertDataGroupReport($ArrUpdateStock,$id_gudang,$id_gudang_wip,$kode_trans,$xyz_no_so,$xyz_no_spk,$xyz_product);
			}
			history('Close request produksi '.$kode_spk.'/'.$kode_trans.'/'.$id);
		}
		echo json_encode($Arr_Kembali);
	}
	
	//==========================================================================================================================
	//===============================================ADJUSTMENT MATERIAL========================================================
	//==========================================================================================================================
	
	public function adjustment(){
		$this->adjustment_material_model->adjustment();
	}
	
	public function server_side_adjustment(){
		$this->adjustment_material_model->get_data_json_adjustment();
	}
	
	public function add_adjustment(){
		$this->adjustment_material_model->add_adjustment();
	}
	
	public function excel_adjustment(){
		$this->adjustment_material_model->excel_adjustment();
	}
	
	
	//==========================================================================================================================
	//==================================================END WAREHOUSE===========================================================
	//==========================================================================================================================


	public function list_ipp(){
	 	$adjust		= $this->input->post('adjust');
		$wherField 	= ($adjust == 'IN')?'status':'sts_close';
		$wherTable 	= ($adjust == 'IN')?'tran_material_po_header':'warehouse_planning_header';
		$wherIsi 	= ($adjust == 'IN')?'WAITING IN':'N';
		$wherF 		= ($adjust == 'IN')?'no_po':'no_ipp';
		$wherSelct 	= ($adjust == 'IN')?'PO Number':'IPP';
		
		$tambahan 	= ($adjust == 'IN')?"OR ".$wherField." = 'IN PARSIAL'":'';

		$query	 	= "SELECT ".$wherF." FROM ".$wherTable." WHERE (".$wherField." = '".$wherIsi."' ".$tambahan.") ORDER BY ".$wherF." ASC";
		
		$Q_result	= $this->db->query($query)->result();
		$Opt 		= (!empty($Q_result))?'Select An '.$wherSelct:'List Empty';
		$option 	= "<option value='0'>".$Opt."</option>";
		foreach($Q_result as $row){
			$option .= "<option value='".$row->$wherF."'>".$row->$wherF."</option>";
		}
	 	echo json_encode(array(
	 		'option' => $option
	 	));
	}

	public function list_warehouse(){
		$adjust	= $this->input->post('adjust');

		$wherField = " sts_2 = 'N' ";
		if($adjust == 'IN'){
			$wherField = " status = 'Y' AND urut2 = '1' ";
		}
		if($adjust == 'MOVE'){
			$wherField = " status = 'Y' ";
		}

		$Opt 	= ($adjust == 'IN' OR $adjust == 'MOVE')?'Select An Warehouse':'List Empty';


	   	$query	 	= "SELECT id, kd_gudang, nm_gudang FROM warehouse WHERE ".$wherField." ORDER BY urut ASC";
	  	$Q_result	= $this->db->query($query)->result();
	  	$option = "<option value='0'>".$Opt."</option>";
	  	foreach($Q_result as $row)
	   	{
		   $option .= "<option value='".$row->id."'>".$row->nm_gudang."</option>";
	   	}
		echo json_encode(array(
			'option' => $option
		));
   }

	public function list_warehouse_ipp(){
		$no_ipp		= $this->input->post('no_ipp');
		$tanda = substr($no_ipp, 0,2);
		if($no_ipp <> '0'){
			$queryIpp	= "SELECT a.kd_gudang_ke, b.urut2 FROM warehouse_adjustment a LEFT JOIN warehouse b ON a.kd_gudang_ke=b.kd_gudang WHERE a.no_ipp = '".$no_ipp."' AND a.kd_gudang_dari <> 'PURCHASE' AND b.urut2 >= 2 ORDER BY a.created_date DESC LIMIT 1";
			$restIpp	= $this->db->query($queryIpp)->result();

			$urutX		= (!empty($restIpp[0]->urut2))?$restIpp[0]->urut2:2;

			$Opt 		= (!empty($restIpp[0]->urut2))?'Select An Warehouse':'Select An Warehouse';

			if($tanda == 'PO'){
				$query	 	= "SELECT id, kd_gudang, nm_gudang FROM warehouse WHERE `status` = 'Y' AND urut2 = '1' ORDER BY urut ASC";
			}
			if($tanda <> 'PO'){
				$query	 	= "SELECT id, kd_gudang, nm_gudang FROM warehouse WHERE urut2 = ".$urutX." ORDER BY urut ASC";
			}
			// echo $query;
			$Q_result	= $this->db->query($query)->result();
		}
		if($no_ipp == '0'){
			$Opt = 'List Empty';
		}
		$option = "<option value='0'>".$Opt."</option>";
		if($no_ipp <> '0'){
			foreach($Q_result as $row)
			{
				$option .= "<option value='".$row->id."'>".$row->nm_gudang."</option>";
			}
		}
		echo json_encode(array(
			'option' => $option
		));
	}
	
	public function list_gudang_ke(){
		$gudang		= $this->input->post('gudang');
		$tandax		= $this->input->post('tandax');

		if($gudang <> '0'){
			$queryIpp	= "SELECT b.urut2 FROM  warehouse b WHERE b.id = '".$gudang."' LIMIT 1";
			$restIpp	= $this->db->query($queryIpp)->result();

			if($tandax == 'MOVE'){
				$whLef = " id != '".$gudang."' AND status = 'Y' ";
			}
			else{
				$whLef = " urut2 > ".$restIpp[0]->urut2;
			}

			$query	 	= "SELECT id, kd_gudang, nm_gudang FROM warehouse WHERE ".$whLef." ORDER BY urut ASC";
			// echo $query;
			$Q_result	= $this->db->query($query)->result();

			$Opt 		= (!empty($Q_result))?'Select An Warehouse':'List Empty - Not Found';
		}
		if($gudang == '0'){
			$Opt = 'List Empty';
		}

		$option = "<option value='0'>".$Opt."</option>";
		if($gudang <> '0'){
		foreach($Q_result as $row)
			{
				$option .= "<option value='".$row->id."'>".$row->nm_gudang."</option>";
			}
		}
		echo json_encode(array(
			'option' => $option
		));
	}
	
	public function list_material(){
		
		$query	 	= "SELECT id_material, nm_material FROM raw_materials WHERE `delete` = 'N' ORDER BY nm_material ASC";
		$Q_result	= $this->db->query($query)->result();
		$option = "<option value='0'>Select Material</option>";
		foreach($Q_result as $row)
		{
			$option .= "<option value='".$row->id_material."'>".$row->id_material." - ".$row->nm_material."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}
	
	public function list_material_stock(){
		$gudang		= $this->input->post('gudang');
		$query	 	= "SELECT id_material, nm_material FROM warehouse_stock WHERE id_gudang = '".$gudang."' ORDER BY nm_material ASC";
		$Q_result	= $this->db->query($query)->result();
		$option = "<option value='0'>Select Material</option>";
		foreach($Q_result as $row)
		{
			$option .= "<option value='".$row->id_material."'>".strtoupper($row->nm_material)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}
	
	public function list_expired_date(){
		$id_gudang_ke		= $this->input->post('id_gudang_ke');
		$id_material		= $this->input->post('id_material');
		
		$query	 	= "SELECT expired FROM warehouse_stock_expired WHERE id_gudang = '".$id_gudang_ke."' AND id_material = '".$id_material."' GROUP BY expired ORDER BY expired ASC";
		$Q_result	= $this->db->query($query)->result();
		// echo $query;
		if(!empty($Q_result)){
			$option = "<option value='0'>Select Expired</option>";
			foreach($Q_result as $row){
				if($row->expired <> NULL AND $row->expired <> '0000-00-00'){
					$option .= "<option value='".$row->expired."'>".date('d-M-Y', strtotime($row->expired))."</option>";
				}
				// if($row->expired == NULL OR $row->expired == '0000-00-00'){
					// $option .= "<option value='0'>Expired Empty</option>";
				// }
			}
		}
		
		if(empty($Q_result)){
			$option = "<option value='0'>Expired Not Found</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

	public function list_warehouse_dest(){
		$gudang		= $this->input->post('gudang');
		$tandax		= $this->input->post('tandax');

		if($gudang <> '0'){
			$queryIpp	= "SELECT b.urut2 FROM  warehouse b WHERE b.kd_gudang = '".$gudang."' LIMIT 1";
			$restIpp	= $this->db->query($queryIpp)->result();

			if($tandax == 'MOVE'){
				$whLef = " kd_gudang != '".$gudang."' AND status = 'Y' ";
			}
			else{
				$whLef = " urut2 > ".$restIpp[0]->urut2;
			}

			$query	 	= "SELECT id, kd_gudang, nm_gudang FROM warehouse WHERE ".$whLef." ORDER BY urut ASC";
			// echo $query;
			$Q_result	= $this->db->query($query)->result();

			$Opt 		= (!empty($Q_result))?'Select An Warehouse':'List Empty - Not Found';
		}
		if($gudang == '0'){
			$Opt = 'List Empty';
		}

		$option = "<option value='0'>".$Opt."</option>";
		if($gudang <> '0'){
		foreach($Q_result as $row)
			{
				$option .= "<option value='".$row->id."'>".$row->nm_gudang."</option>";
			}
		}
		echo json_encode(array(
			'option' => $option
		));
	}
	
	public function testing_booking(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_bq			= "IPP20761L";
		// echo $id_bq;
		// exit;
		$sqlWhDetail	= "	SELECT
								a.*,
								b.id AS id2,
								b.qty_booking,
								b.kd_gudang,
								b.id_gudang,
								b.idmaterial,
								b.nm_material,
								b.id_category,
								b.nm_category,
								b.qty_stock
							FROM
								warehouse_planning_detail a
								LEFT JOIN warehouse_stock b
									ON a.id_material=b.id_material
							WHERE
								a.no_ipp = '".$id_bq."'
								AND a.id_material <> 'MTL-1903000'
								AND (b.id_gudang = '1' OR b.id_gudang = '2')
							";
		$restWhDetail	= $this->db->query($sqlWhDetail)->result_array();

		$ArrDeatil		 = array();
		$ArrHist		 = array();
		foreach($restWhDetail AS $val => $valx){
			$ArrDeatil[$val]['id'] 			= $valx['id2'];
			$ArrDeatil[$val]['id_material'] = $valx['id_material'];
			$ArrDeatil[$val]['id_gudang'] 	= $valx['id_gudang'];
			$ArrDeatil[$val]['qty_booking'] = $valx['qty_booking'] + $valx['use_stock'];
		}

		foreach($restWhDetail AS $val => $valx){
			$ArrHist[$val]['id_material'] 		= $valx['id_material'];
			$ArrHist[$val]['idmaterial'] 		= $valx['idmaterial'];
			$ArrHist[$val]['nm_material'] 		= $valx['nm_material'];
			$ArrHist[$val]['id_category'] 		= $valx['id_category'];
			$ArrHist[$val]['nm_category'] 		= $valx['nm_category'];
			$ArrHist[$val]['id_gudang_dari'] 	= $valx['id_gudang'];
			$ArrHist[$val]['kd_gudang_dari'] 	= $valx['kd_gudang'];
			$ArrHist[$val]['kd_gudang_ke'] 		= 'BOOKING';
			$ArrHist[$val]['qty_stock_awal'] 	= $valx['qty_stock'];
			$ArrHist[$val]['qty_stock_akhir'] 	= $valx['qty_stock'];
			$ArrHist[$val]['qty_booking_awal'] 	= $valx['qty_booking'];
			$ArrHist[$val]['qty_booking_akhir'] = $valx['qty_booking'] + $valx['use_stock'];
			$ArrHist[$val]['no_ipp'] 			= $id_bq;
			$ArrHist[$val]['jumlah_mat'] 		= $valx['use_stock'];
			$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
			$ArrHist[$val]['update_date'] 		= date('Y-m-d H:i:s');
		}

		$ArrHeader = array(
			'sts_booking' => 'Y',
			'book_by' => $data_session['ORI_User']['username'],
			'book_date' => date('Y-m-d H:i:s')
		);
		
		echo "<pre>";
		print_r($ArrDeatil);
		print_r($ArrHist);
		print_r($ArrHeader);
		exit;
		
	}

	public function ExcelGudang(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$gudang			= $this->uri->segment(3);
		$category	= $this->uri->segment(4);
		$date_filter	= $this->uri->segment(5);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$table = "warehouse_stock";
		$where_gudang ='';
		$where_date ='';
		$field_add = '';
		$group_by = '';
		$fieldStock = 'a.qty_stock, a.qty_booking,a.qty_rusak, a.id_gudang,b.nm_gudang,';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_stock_per_day";
			$field_add = "a.costbook, a.total_value,";
		}

		if($gudang == '0'){
			$where_gudang = " AND a.id_gudang IN (".$this->gudang_produksi.") ";
			$group_by = ' GROUP BY c.id_material ';
			$fieldStock = 'SUM(a.qty_stock) AS qty_stock, SUM(a.qty_booking) AS qty_booking, SUM(a.qty_rusak) AS qty_rusak, "0" AS id_gudang, "Gudang Produksi" AS nm_gudang,';
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				c.idmaterial,
				c.id_material,
				c.nm_material,
				".$fieldStock."
				".$field_add."
				c.nm_category
			FROM
				".$table." a 
				LEFT JOIN warehouse b ON a.kd_gudang=b.kd_gudang
				LEFT JOIN raw_materials c ON a.id_material = c.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.id_material <> 'MTL-1903000' AND c.delete = 'N' ".$where_gudang." ".$where_date."
		".$group_by;
		$restDetail1	= $this->db->query($sql)->result_array();
		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$gudang))->result();
		$nm_gudang = strtoupper(get_name('warehouse','nm_gudang','id',$gudang));
		
		$tanggal_update = (!empty($date_filter))?" (".date('d F Y', strtotime($date_filter)).")":" (".date('d F Y').")";
		$tanggal_update2 = (!empty($date_filter))?date('Y-m-d', strtotime($date_filter)):date('Y-m-d');

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(7);
		$sheet->setCellValue('A'.$Row, 'STOCK - '.$nm_gudang.$tanggal_update);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'ID PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'WAREHOUSE');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'STOCK');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);
		
		if($category != 'produksi'){
			$sheet->setCellValue('H'.$NewRow, 'BOOKING');
			$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
			$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
			$sheet->getColumnDimension('H')->setWidth(20);

			$sheet->setCellValue('I'.$NewRow, 'AVAILABLE');
			$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
			$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
			$sheet->getColumnDimension('I')->setWidth(20);

			if($category == 'pusat'){
				$sheet->setCellValue('J'.$NewRow, 'DEMAGED');
				$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
				$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
				$sheet->getColumnDimension('J')->setWidth(20);
			}
		}


		// echo $qDetail1; exit;
		$GET_COSTBOOK = get_costbook();

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				if(empty($date_filter)) {
					if(!empty($GET_COSTBOOK[$row_Cek['id_material']])) {
						$COSTBOOK =$GET_COSTBOOK[$row_Cek['id_material']];
					}else{
						$COSTBOOK =0;
					}
				}else{
					$COSTBOOK =$row_Cek['costbook'];
				}
				$COSTBOOK_TOTAL = $COSTBOOK * $row_Cek['qty_stock'];
				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$idmaterial	= $row_Cek['idmaterial'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= strtoupper($row_Cek['nm_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_gudang	= $row_Cek['nm_gudang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_gudang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_stock	= $row_Cek['qty_stock'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				if($category != 'produksi'){
					$awal_col++;
					$qty_booking	= $row_Cek['qty_booking'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $qty_booking);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$qty_avl	= $row_Cek['qty_stock'] - $row_Cek['qty_booking'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $qty_avl);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					if($category == 'pusat'){
						$awal_col++;
						$qty_rusak	= $row_Cek['qty_rusak'];
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $qty_rusak);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
					}
				}
			}
		}

		$LABEL_TITLE = strtolower('STOCK'.'-'.$tanggal_update2);
		$sheet->setTitle('Stock');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
//		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Type: vnd.ms-excel');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="'.$LABEL_TITLE.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function download_excel($id_bq=null,$type=null){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT

			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(5);
		$sheet->setCellValue('A'.$Row, 'MATERIAL PLANNING '.str_replace('BQ-','',$id_bq));
		$sheet->getStyle('A'.$Row.':E'.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':E'.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'MATERIAL');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'BERAT');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'UNIT');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);
		
		if($type == 'pipe'){
			$query	= "	SELECT
							a.id_bq AS id_bq,
							a.id_material AS id_material,
							a.nm_material AS nm_material,
							round( sum( ( a.last_cost * b.qty ) ), 3 ) AS last_cost 
						FROM
							( estimasi_total a LEFT JOIN so_bf_detail_header b ON ( ( a.id_milik = b.id_milik ) ) ) 
						WHERE
							( a.id_material <> '0' )  
							AND a.id_bq='".$id_bq."'
						GROUP BY
							a.id_material,
							a.id_bq 
						ORDER BY
							a.nm_material";
			$result		= $this->db->query($query)->result_array();

			$non_frp		= $this->db->get_where('so_acc_and_mat', array('category <>'=>'mat', 'id_bq'=>$id_bq))->result_array();
			$material		= $this->db->get_where('so_acc_and_mat', array('category'=>'mat', 'id_bq'=>$id_bq))->result_array();
		}
		else{
			$query	= "		SELECT
								a.no_ipp AS id_bq,
								a.id_material AS id_material,
								b.nm_material AS nm_material,
								round( sum( ( a.berat ) ), 3 ) AS last_cost 
							FROM
								( planning_tanki_detail a LEFT JOIN raw_materials b ON ( ( a.id_material = b.id_material ) ) ) 
							WHERE
								( a.id_material <> '0' AND a.id_material <> 'MTL-1903000')  
								AND a.no_ipp='$id_bq'
								AND a.category='mat'
							GROUP BY
								a.id_material,
								a.no_ipp 
							ORDER BY
								b.nm_material";
			$result		= $this->db->query($query)->result_array();

			$sql_non_frp 	= "	SELECT
										a.id,
										a.id AS id_milik,
										a.no_ipp AS id_bq,
										c.id AS id_material,
										SUM(a.berat) AS qty,
										'tanki' AS category,
										'3' AS satuan,
										SUM(a.berat) AS berat,
										b.stock
									FROM
										planning_tanki_detail a
										LEFT JOIN accessories c ON a.id_material=c.id_acc_tanki AND c.category = 5
										LEFT JOIN warehouse_acc_stock b ON c.id = b.id_acc
									WHERE
										a.category = 'acc'
										AND a.no_ipp='$id_bq'
									GROUP BY a.id_material ";
			// echo $sql_non_frp;
			$non_frp		= $this->db->query($sql_non_frp)->result_array();
			$material		= array();
		}

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$last_cost	= $row_Cek['last_cost'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $last_cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$unit	= 'KG';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $unit);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
			
			}
				
		}

		if($non_frp){
			foreach($non_frp as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$qty = $row_Cek['qty'];
				$satuan = $row_Cek['satuan'];
				if($row_Cek['category'] == 'plate'){
					$qty = $row_Cek['berat'];
					$satuan = '1';
				}

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$nm_material	= get_name_acc($row_Cek['id_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$last_cost	= $qty;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $last_cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$unit	= strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $unit);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
			
			}
				
		}

		if($material){
			foreach($material as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$nm_material	= strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $row_Cek['id_material']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$last_cost	= $row_Cek['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $last_cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$unit	= 'KG';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $unit);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
			
			}
				
		}


		$sheet->setTitle(str_replace('BQ-','',$id_bq));
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Material Planning -  '.str_replace('BQ-','',$id_bq).' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function get_detail_spk(){
		$data 			= $this->input->post();
		$spk 			= $data['no_spk'];
		$category_mat 	= $data['category_mat'];
		$no_ipp 		= $data['no_ipp'];
		$get_detail 	= $this->db->get_where('production_detail',array('no_spk'=>$spk))->result();
		if(empty($get_detail)){
			$Arr_Kembali    = array(
				'pesan'        =>'SPK belum masuk produksi !!!',
				'status'    => 2
			);
			echo json_encode($Arr_Kembali);
			return false;
		}
		$id_milik 		= $get_detail[0]->id_milik;
		$qty_spk 		= $get_detail[0]->qty;
		$product_name 	= get_name('so_detail_header','id_category','id',$id_milik);
		$qty 			= $get_detail[0]->qty;
		$WHEREIN 		= ['TYP-0003','TYP-0004','TYP-0005','TYP-0006'];
		if($category_mat != '0'){
			$WHEREIN 		= [$category_mat];
		}

		$get_material 	= $this->db->select('a.*, SUM(a.last_cost) AS total_req')->group_by('a.id_material')->order_by('nm_material','asc')->where_in('a.id_category',$WHEREIN)->get_where('so_component_detail a',array('a.id_milik'=>$id_milik,'a.id_material !='=>'MTL-1903000'))->result_array();
		
		$HTML = '';
		if(!empty($get_material)){
			foreach ($get_material as $key => $value) { $key++;
				$get_planning = $this->db->select('a.*')->from('planning_detail a')->where('a.no_ipp',$no_ipp)->where('a.id_material',$value['id_material'])->get()->result();
				$QTY_REQ 	= $value['total_req'] * $qty;
				if(!empty($get_planning)){
					
					//SISA REQUEST
					$get_sisa_reqx 	= $this->db->get_where('planning_detail_spk',array('no_ipp'=>$no_ipp,'id_milik'=>$id_milik,'id_material'=>$value['id_material']))->result();
					$jumlah_req 	= (!empty($get_sisa_reqx))?$get_sisa_reqx[0]->total_request:0;
					$jumlah_aktual 	= (!empty($get_sisa_reqx))?$get_sisa_reqx[0]->total_aktual:0;

					$QTY_REQUEST 	= $jumlah_req;

					$sisa_req 		= $QTY_REQ - $QTY_REQUEST;

					$color = 'text-green text-bold';
					$disabled = '';
					if($sisa_req <= 0){
						$color = 'text-red text-bold';
					}
					if($sisa_req > 0){
						$HTML .= "<tr>";
							$HTML .= "<td class='text-center'>".$key."
										<input type='hidden' name='detail2[999".$key."][id]' value='".$get_planning[0]->id."'>
										<input type='hidden' name='detail2[999".$key."][berat_est]' value='".$QTY_REQ."'>
										<input type='hidden' name='detail2[999".$key."][qty_sisa]' value='".$sisa_req."'>
										<input type='hidden' name='detail2[999".$key."][qty_total_req]' value='".$QTY_REQUEST."'>
									</td>";
							$HTML .= "<td>".strtoupper($value['nm_material'])."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQ,3)."</td>";
							$HTML .= "<td class='text-right ".$color." sisaRequest'>".number_format($sisa_req,3)."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQUEST,3)."</td>";
							$HTML .= "<td><input type='text' style='width:100%' name='detail2[999".$key."][sudah_request]' data-no='".$key."' class='form-control text-bold input-sm text-right autoNumeric requestBlock' placeholder='Request (kg)'></td>";
							$HTML .= "<td><input type='text' style='width:100%' name='detail2[999".$key."][ket_request]' data-no='".$key."' class='form-control input-sm text-left' placeholder='Keterangan'></td>";
						$HTML .= "</tr>";
					}
					else{
						$HTML .= "<tr>";
							$HTML .= "<td class='text-center'>".$key."</td>";
							$HTML .= "<td>".strtoupper($value['nm_material'])."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQ,3)."</td>";
							$HTML .= "<td class='text-right ".$color."'>".number_format($sisa_req,3)."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQUEST,3)."</td>";
							$HTML .= "<td colspan='2' class='text-red'><b>Request melebihi limit !!!</b></td>";
						$HTML .= "</tr>";
					}
				}
				else{
					$HTML .= "<tr>";
						$HTML .= "<td class='text-center'>".$key."</td>";
						$HTML .= "<td>".strtoupper($value['nm_material'])."</td>";
						$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQ,3)."</td>";
						$HTML .= "<td class='text-center'>-</td>";
						$HTML .= "<td class='text-center'>-</td>";
						$HTML .= "<td colspan='2' class='text-red'><b>Buat request terlebih dahulu !!!</b></td>";
					$HTML .= "</tr>";
				}
			}
		}
		else{
			$HTML .= "<tr>";
				$HTML .= "<td colspan='7' class='text-red'><b>Material tidak ditemukan !!!</b></td>";
			$HTML .= "</tr>";
		}

		echo json_encode(array(
			'option' => $HTML,
			'id_milik' => $id_milik,
			'qty_spk' => $qty_spk,
			'product_name' => strtoupper($product_name)
		));
	}

	public function get_detail_spk_tanki(){
		$data 			= $this->input->post();
		$spk 			= $data['no_spk'];
		$category_mat 	= $data['category_mat'];
		$no_ipp 		= $data['no_ipp'];
		$get_detail 	= $this->db->get_where('production_detail',array('no_spk'=>$spk,'id_produksi'=>'PRO-'.$no_ipp))->result();
		if(empty($get_detail)){
			$Arr_Kembali    = array(
				'pesan'        =>'SPK belum masuk produksi !!!',
				'status'    => 2
			);
			echo json_encode($Arr_Kembali);
			return false;
		}
		$id_milik 		= $get_detail[0]->id_milik;
		$qty_spk 		= $get_detail[0]->qty;
		$product_name 	= $get_detail[0]->id_product;
		$qty 			= $get_detail[0]->qty;
		$WHEREIN 		= ['TYP-0003','TYP-0004','TYP-0005','TYP-0006'];
		if($category_mat != '0'){
			$WHEREIN 		= [$category_mat];
		}

		$get_material 	= $this->db
								->select('a.*, SUM(a.berat) AS total_req, b.nm_material')
								->group_by('a.id_material')
								->order_by('b.nm_material','asc')
								->where_in('b.id_category',$WHEREIN)
								->join('raw_materials b','a.id_material=b.id_material','left')
								->get_where('est_material_tanki a',array('a.id_det'=>$id_milik,'a.id_material !='=>'MTL-1903000','a.no_ipp'=>$no_ipp))->result_array();
		
		$HTML = '';
		if(!empty($get_material)){
			foreach ($get_material as $key => $value) { $key++;
				$get_planning = $this->db->select('a.*')->from('planning_detail a')->where('a.no_ipp',$no_ipp)->where('a.id_material',$value['id_material'])->get()->result();
				$QTY_REQ 	= $value['total_req'] * $qty;
				if(!empty($get_planning)){
					
					//SISA REQUEST
					$get_sisa_reqx 	= $this->db->get_where('planning_detail_spk',array('no_ipp'=>$no_ipp,'id_milik'=>$id_milik,'id_material'=>$value['id_material']))->result();
					$jumlah_req 	= (!empty($get_sisa_reqx))?$get_sisa_reqx[0]->total_request:0;
					$jumlah_aktual 	= (!empty($get_sisa_reqx))?$get_sisa_reqx[0]->total_aktual:0;

					$QTY_REQUEST 	= $jumlah_req;

					$sisa_req 		= $QTY_REQ - $QTY_REQUEST;

					$color = 'text-green text-bold';
					$disabled = '';
					if($sisa_req <= 0){
						$color = 'text-red text-bold';
					}
					if($sisa_req > 0){
						$HTML .= "<tr>";
							$HTML .= "<td class='text-center'>".$key."
										<input type='hidden' name='detail2[999".$key."][id]' value='".$get_planning[0]->id."'>
										<input type='hidden' name='detail2[999".$key."][berat_est]' value='".$QTY_REQ."'>
										<input type='hidden' name='detail2[999".$key."][qty_sisa]' value='".$sisa_req."'>
										<input type='hidden' name='detail2[999".$key."][qty_total_req]' value='".$QTY_REQUEST."'>
									</td>";
							$HTML .= "<td>".strtoupper($value['nm_material'])."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQ,3)."</td>";
							$HTML .= "<td class='text-right ".$color." sisaRequest'>".number_format($sisa_req,3)."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQUEST,3)."</td>";
							$HTML .= "<td><input type='text' style='width:100%' name='detail2[999".$key."][sudah_request]' data-no='".$key."' class='form-control text-bold input-sm text-right autoNumeric requestBlock' placeholder='Request (kg)'></td>";
							$HTML .= "<td><input type='text' style='width:100%' name='detail2[999".$key."][ket_request]' data-no='".$key."' class='form-control input-sm text-left' placeholder='Keterangan'></td>";
						$HTML .= "</tr>";
					}
					else{
						$HTML .= "<tr>";
							$HTML .= "<td class='text-center'>".$key."</td>";
							$HTML .= "<td>".strtoupper($value['nm_material'])."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQ,3)."</td>";
							$HTML .= "<td class='text-right ".$color."'>".number_format($sisa_req,3)."</td>";
							$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQUEST,3)."</td>";
							$HTML .= "<td colspan='2' class='text-red'><b>Request melebihi limit !!!</b></td>";
						$HTML .= "</tr>";
					}
				}
				else{
					$HTML .= "<tr>";
						$HTML .= "<td class='text-center'>".$key."</td>";
						$HTML .= "<td>".strtoupper($value['nm_material'])."</td>";
						$HTML .= "<td class='text-right text-bold'>".number_format($QTY_REQ,3)."</td>";
						$HTML .= "<td class='text-center'>-</td>";
						$HTML .= "<td class='text-center'>-</td>";
						$HTML .= "<td colspan='2' class='text-red'><b>Buat request terlebih dahulu !!!</b></td>";
					$HTML .= "</tr>";
				}
			}
		}
		else{
			$HTML .= "<tr>";
				$HTML .= "<td colspan='7' class='text-red'><b>Material tidak ditemukan !!!</b></td>";
			$HTML .= "</tr>";
		}

		echo json_encode(array(
			'option' => $HTML,
			'id_milik' => $id_milik,
			'qty_spk' => $qty_spk,
			'product_name' => strtoupper($product_name)
		));
	}

	//new buat request
	public function process_buat_request($no_ipp){
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$DMF_TANDA = substr($no_ipp,0,3);
		$DMF_TANDA_TANKI = substr($no_ipp,0,4);
		// echo $DMF_TANDA_TANKI;
		// exit;
		$query		= "	SELECT
							a.id_bq AS id_bq,
							a.id_material AS id_material,
							a.nm_material AS nm_material,
							round( sum( ( a.last_cost * d.qty ) ), 3 ) AS last_cost
						FROM
							so_estimasi_total a 
							LEFT JOIN so_detail_header d ON a.id_milik = d.id
						WHERE
							a.id_material <> '0'
							AND a.id_bq='BQ-".$no_ipp."'
							AND a.id_material <> 'MTL-1903000'
						GROUP BY
							a.id_material,
							a.id_bq 
						ORDER BY
							a.nm_material";
		if($DMF_TANDA == 'DMF'){
			$query		= "SELECT
								a.kode AS id_bq,
								a.id_material AS id_material,
								a.nm_material AS nm_material,
								round( sum( ( a.last_cost * d.qty ) ), 3 ) AS last_cost
							FROM
								deadstok_estimasi a 
								LEFT JOIN production_spk d ON a.kode = d.product_code_cut
							WHERE
								a.id_material <> '0'
								AND a.kode='$no_ipp'
								AND a.id_material <> 'MTL-1903000'
								AND a.category = 'utama'
								AND a.id_category != 'TYP-0001'
							GROUP BY
								a.id_material
							ORDER BY
								a.nm_material";
		}
		if($DMF_TANDA_TANKI == 'IPPT'){
			$query		= "SELECT
								a.no_ipp AS id_bq,
								a.id_material AS id_material,
								b.nm_material AS nm_material,
								round( sum( ( a.berat * d.qty ) ), 3 ) AS last_cost
							FROM
								est_material_tanki a 
								LEFT JOIN raw_materials b ON a.id_material=b.id_material
								LEFT JOIN production_spk d ON a.id_det = d.id_milik AND a.no_ipp=d.no_ipp
							WHERE
								a.id_material <> '0'
								AND a.no_ipp='$no_ipp'
								AND a.id_material <> 'MTL-1903000'
								AND a.jenis_spk = 'non mix'
							GROUP BY
								a.id_material
							ORDER BY
								b.nm_material";
		}
		// echo $query; exit;
		$result		 = $this->db->query($query)->result_array();
		// echo "<pre>";
		// print_r($result);
		// exit;
		$ArrHeader = [
			'no_ipp' => $no_ipp,
			'created_by' => $UserName,
			'created_date' => $DateTime
		];

		$ArrDetail = [];
		foreach ($result as $key => $value) {
			$ArrDetail[$key]['no_ipp'] 			= $no_ipp;
			$ArrDetail[$key]['id_material'] 	= $value['id_material'];
			$ArrDetail[$key]['nm_material'] 	= $value['nm_material'];
			$ArrDetail[$key]['berat'] 			= (!empty($value['last_cost']))?$value['last_cost']:0;
			$ArrDetail[$key]['total_request'] 	= 0;
		}

		$this->db->trans_start();
			$this->db->insert('planning_header', $ArrHeader);
			if(!empty($ArrDetail)){
				$this->db->insert_batch('planning_detail', $ArrDetail);
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
			history('Create request for penampungan request subgudang : '.$no_ipp);
		}
		echo json_encode($Arr_Data);

	}

	public function get_ros($id_po){
		$resData	= $this->db->query("select id,no_ros from report_of_shipment where id_po='".$id_po."' and status='APV'")->result_array();
		$option	= "<option value=''>No ROS</option>";
		foreach($resData AS $val => $valx){
			$option .= "<option value='".$valx['id']."'>".($valx['no_ros'])."</option>";
		}
		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function modal_history_subgudang(){
		$kode_trans = $this->uri->segment(3);
		$tanda     	= $this->uri->segment(4);

		$result			= $this->db->group_by('update_date')->select('update_by, update_date, SUM(qty_oke) AS qty_aktual, no_ipp')->get_where('warehouse_adjustment_check',array('kode_trans'=>$kode_trans))->result_array();
		$result_header	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();

		$data = array(
			'result' 	=> $result,
			'tanda' 	=> $tanda,
			'checked' 	=> $result_header[0]->checked,
			'kode_trans'=> $result_header[0]->kode_trans,
			'no_po' 	=> $result_header[0]->no_ipp,
			'no_ipp' 	=> $result_header[0]->no_ipp,
			'qty_spk' 	=> $result_header[0]->qty_spk,
			'file_eng_change' 	=> $result_header[0]->file_eng_change,
			'tanggal' 	=> (!empty($result_header[0]->tanggal))?date('d-M-Y',strtotime($result_header[0]->tanggal)):'',
			'id_milik' 	=> get_name('production_detail','id_milik','no_spk',$result_header[0]->no_spk),
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 		=> date('d F Y', strtotime($result_header[0]->created_date)),
			'GET_USERNAME' => get_detail_user(),
			'DETAIL_MATERIAL' => get_detailAktualAdjustmentCheck()

		);

		$this->load->view('Warehouse/modal_history_subgudang', $data);
	}

	public function print_request_check(){
		$post 			= $this->input->post();
		// $kode_trans     = $post['kode_trans'];
		// $update_by     	= $post['update_by'];
		// $update_date    = $post['update_date'];
		$kode_trans     = $this->uri->segment(3);
		$update_by     	= get_name('users','username','id_user',$this->uri->segment(4));
		$update_date    = date('Y-m-d H:i:s',strtotime($this->uri->segment(5)));
		$no_request     = $this->uri->segment(6);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$GET_SO_NUMBER = $this->db->get('so_number')->result_array();
		$ArrGetSO = [];
		foreach($GET_SO_NUMBER AS $val => $value){
			$ArrGetSO[$value['id_bq']] = $value['so_number'];
		}

		$GET_SPK_NUMBER = $this->db->get_where('so_detail_header',array('no_spk <>'=>NULL))->result_array();
		$ArrGetSPK = [];
		$ArrGetIPP = [];
		foreach($GET_SPK_NUMBER AS $val => $value){
			$ArrGetSPK[$value['id']] = $value['no_spk'];
			$ArrGetIPP[$value['id']] = $value['id_bq'];
		}

		$rest_data 	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans,'status_id'=>'1'))->result_array();
		$KeGudang = get_name('warehouse', 'nm_gudang', 'id', $rest_data[0]['id_gudang_ke']);
		$tgl_planning = '';
		if(!empty($no_request)){
			$rest_req 	= $this->db->get_where('print_header',array('kode_uniq'=>$no_request))->result_array();
			if(!empty($rest_req[0]['id_gudang'])){
				$KeGudang = get_name('warehouse', 'nm_gudang', 'id', $rest_req[0]['id_gudang']);
			}
			if(!empty($rest_req[0]['tgl_planning']) AND $rest_req[0]['tgl_planning'] != '0000-00-00'){
				$tgl_planning = date('d F Y',strtotime($rest_req[0]['tgl_planning']));
			}
		}

		$data = array(
			'rest_data' => $rest_data,
			'tgl_planning' => $tgl_planning,
			'KeGudang' => $KeGudang,
			'no_request' => ' / '.$no_request,
			'ArrGetSO' => $ArrGetSO,
			'ArrGetSPK' => $ArrGetSPK,
			'ArrGetIPP' => $ArrGetIPP,
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'update_by' => $update_by,
			'update_date' => $update_date
		);

		history('Print Request Material '.$kode_trans.', '.$update_date);
		$this->load->view('Print/print_list_subgudang_check', $data);
	}
	function print_qrcode($id){
		$detail=str_replace("~","','",$id);
		$qDetail1 = " SELECT a.* FROM warehouse_adjustment_detail a WHERE a.id in ('".$id."') ORDER BY a.id";
		$restDetail1 = $this->db->query($qDetail1)->result_array();		
		$data = array(
            'detail'	=> $restDetail1,
		);
		history('Print Qrcode'); 
		$this->load->view('Warehouse/print_qrcode', $data);
	}

	public function show_history_booking(){
		$data 			= $this->input->post();
		$no_ipp 		= $data['no_ipp'];
		$id_material 		= $data['id_material'];
		$id_gudang 		= $data['id_gudang'];

		$result		= $this->db
							->select('a.*')
							->from('warehouse_history a')
							->where('a.no_ipp',$no_ipp)
							->where('a.id_material',$id_material)
							->where('a.id_gudang',$id_gudang)
							->where('a.update_date > ','2023-12-15 00:00:00')
							->or_group_start()
								->where('a.kd_gudang_ke','BOOKING')
								->where('a.kd_gudang_dari','BOOKING')
							->group_end()
							->get()
							->result_array();

		$data_html = "";
		$data_html .= "<tr>";
			$data_html .= "<th>#</th>";
			$data_html .= "<th>Gudang Dari</th>";
			$data_html .= "<th>Gudang Ke</th>";
			$data_html .= "<th class='text-right'>Qty Booking</th>";
			$data_html .= "<th class='text-right'>Booking Awal</th>";
			$data_html .= "<th class='text-right'>Booking Akhir</th>";
			$data_html .= "<th>Keterangan</th>";
			$data_html .= "<th class='text-center'>Tanggal</th>";
		$data_html .= "</tr>";
		$No=0;
		$QTY_PLUS = 0;
		foreach ($result as $key => $value) { $key--;
			$No++;
			$bold = '';
			$bold2 = '';
			$color = 'text-blue';
			
			$gudang_dari 	= get_name('warehouse','nm_gudang','id',$value['id_gudang_dari']);
			$dari_gudang 	= (!empty($gudang_dari))?$gudang_dari:$value['kd_gudang_dari'];
			$ke_gudang 		= $value['kd_gudang_ke'];

			$QTY 			= $value['jumlah_mat'];
			// $QTY_SEBELUM 	= (!empty($result[$key]['jumlah_mat']))?$result[$key]['jumlah_mat']:0;
			// $QTY_AWAL 		= (!empty($result[$key]['jumlah_mat']))?$result[$key]['jumlah_mat'] + $QTY:0;
			$QTY_AWAL 		= $QTY_PLUS;
			
			if($dari_gudang == 'BOOKING'){
				$bold = 'text-bold';
				$color = 'text-red';

				$QTY_AKHIR 	= $QTY_AWAL - $QTY;
			}
			if($ke_gudang == 'BOOKING'){
				$bold2 = 'text-bold';

				$QTY_AKHIR 	= $QTY_AWAL + $QTY;
			}

			if($No == 1){
				$QTY_AKHIR 	= $QTY;
			}
			if($No == 1){
				$QTY_AWAL 	= 0;
			}

			$data_html .= "<tr>";
				$data_html .= "<td>".$No."</td>";
				$data_html .= "<td class='text-left ".$bold."'>".$dari_gudang."</td>";
				$data_html .= "<td class='text-left ".$bold2."'>".$ke_gudang."</td>";
				$data_html .= "<td class='text-right ".$color."'>".number_format($QTY,4)."</td>";
				$data_html .= "<td class='text-right ".$color."'>".number_format($QTY_AWAL,4)."</td>";
				$data_html .= "<td class='text-right ".$color."'>".number_format($QTY_AKHIR,4)."</td>";
				$data_html .= "<td>".strtoupper($value['ket'])."</td>";
				$data_html .= "<td class='text-center'>".date('d-M-Y H:i:s', strtotime($value['update_date']))."</td>";
			$data_html .= "</tr>";

			$QTY_PLUS = $QTY_AKHIR;
		}
		$data_html .= "<tr>";
			$data_html .= "<td></td>";
			$data_html .= "<td colspan='4' class='text-bold'>SISA BOOKING</td>";
			$data_html .= "<td class='text-right text-bold'>".number_format($QTY_AKHIR,4)."</td>";
			$data_html .= "<td colspan='2'></td>";
		$data_html .= "</tr>";
		

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	function close_booking_material(){
		$no_ipp 	= $this->input->post('no_ipp');
		$id_gudang 	= 2;
		$data_session	= $this->session->userdata;

		$SQL 	= "SELECT * FROM warehouse_history WHERE no_ipp = '$no_ipp' AND kd_gudang_ke='BOOKING' AND update_date > '2023-12-15 00:00:00' GROUP BY id_material";
		$result = $this->db->query($SQL)->result_array();

		$ArrDetail = [];
		foreach ($result as $key => $value) {
			$id_material = $value['id_material'];
			$No=0;
			$QTY_PLUS = 0;
			$resultHist		= $this->db
							->select('a.*')
							->from('warehouse_history a')
							->where('a.no_ipp',$no_ipp)
							->where('a.id_material',$id_material)
							->where('a.id_gudang',$id_gudang)
							->where('a.update_date > ','2023-12-15 00:00:00')
							->or_group_start()
								->where('a.kd_gudang_ke','BOOKING')
								->where('a.kd_gudang_dari','BOOKING')
							->group_end()
							->get()
							->result_array();
			foreach ($resultHist as $key2 => $value2) {
				$No++;

				$gudang_dari 	= get_name('warehouse','nm_gudang','id',$value2['id_gudang_dari']);
				$dari_gudang 	= (!empty($gudang_dari))?$gudang_dari:$value2['kd_gudang_dari'];
				$ke_gudang 		= $value2['kd_gudang_ke'];

				$QTY 			= $value2['jumlah_mat'];
				$QTY_AWAL 		= $QTY_PLUS;
				
				if($dari_gudang == 'BOOKING'){
					$QTY_AKHIR 	= $QTY_AWAL - $QTY;
				}
				if($ke_gudang == 'BOOKING'){
					$QTY_AKHIR 	= $QTY_AWAL + $QTY;
				}

				if($No == 1){
					$QTY_AKHIR 	= $QTY;
				}
				if($No == 1){
					$QTY_AWAL 	= 0;
				}

				$QTY_PLUS = $QTY_AKHIR;
			}

			$ArrDetail[$key]['id_material'] = $id_material;
			$ArrDetail[$key]['nm_material'] = $value['nm_material'];
			$ArrDetail[$key]['sisa'] = $QTY_AKHIR;
		}

		$ArrDeatil = array();
		$ArrHist = array();
		foreach($ArrDetail AS $val => $valx){
			$sqlWhDetail	= "	SELECT
									a.*
								FROM
									warehouse_stock a
								WHERE
									a.id_material = '".$valx['id_material']."'
									AND a.id_material <> 'MTL-1903000'
									AND (a.id_gudang = '2')
								";
			$restWhDetail	= $this->db->query($sqlWhDetail)->result_array();

			$ArrHist[$val]['id_material'] 		= $restWhDetail[0]['id_material'];
			$ArrHist[$val]['idmaterial'] 		= $restWhDetail[0]['idmaterial'];
			$ArrHist[$val]['nm_material'] 		= $restWhDetail[0]['nm_material'];
			$ArrHist[$val]['id_category'] 		= $restWhDetail[0]['id_category'];
			$ArrHist[$val]['nm_category'] 		= $restWhDetail[0]['nm_category'];
			$ArrHist[$val]['id_gudang'] 		= $restWhDetail[0]['id_gudang'];
			$ArrHist[$val]['kd_gudang'] 		= $restWhDetail[0]['kd_gudang'];
			$ArrHist[$val]['kd_gudang_dari'] 	= 'BOOKING';
			$ArrHist[$val]['kd_gudang_ke'] 		= 'BOOK CLOSE';
			$ArrHist[$val]['qty_stock_awal'] 	= $restWhDetail[0]['qty_stock'];
			$ArrHist[$val]['qty_stock_akhir'] 	= $restWhDetail[0]['qty_stock'];
			$ArrHist[$val]['qty_booking_awal'] 	= $restWhDetail[0]['qty_booking'];
			if($valx['sisa'] >= 0){
			$ArrHist[$val]['qty_booking_akhir'] = $restWhDetail[0]['qty_booking'] - $valx['sisa'];
			}
			else{
			$ArrHist[$val]['qty_booking_akhir'] = $restWhDetail[0]['qty_booking'] + $valx['sisa'];
			}
			$ArrHist[$val]['no_ipp'] 			= $no_ipp;
			$ArrHist[$val]['jumlah_mat'] 		= $valx['sisa'];
			$ArrHist[$val]['ket'] 				= 'booking material close';
			$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
			$ArrHist[$val]['update_date'] 		= date('Y-m-d H:i:s');



			$ArrDeatil[$val]['id'] 				= $restWhDetail[0]['id'];
			$ArrDeatil[$val]['id_material'] 	= $restWhDetail[0]['id_material'];
			$ArrDeatil[$val]['id_gudang'] 		= $restWhDetail[0]['id_gudang'];
			if($valx['sisa'] >= 0){
			$ArrDeatil[$val]['qty_booking'] = $restWhDetail[0]['qty_booking'] - $valx['sisa'];
			}
			else{
			$ArrDeatil[$val]['qty_booking'] = $restWhDetail[0]['qty_booking'] + $valx['sisa'];
			}
			$ArrDeatil[$val]['update_by'] 		= $data_session['ORI_User']['username'];
			$ArrDeatil[$val]['update_date'] 	= date('Y-m-d H:i:s');
		}

		$ArrUpdate = [
			'sts_booking_close' => 'Y'
		];

		// echo "<pre>";
		// print_r($ArrDetail);
		// print_r($ArrHist);
		$this->db->trans_start();
			if(!empty($ArrDeatil)){
				$this->db->update_batch('warehouse_stock', $ArrDeatil, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}

			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('warehouse_planning_header', $ArrUpdate);
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
			history('Booking Material Close '.$no_ipp);
		}
		echo json_encode($Arr_Data);
		//create history close booking
	}

	public function modal_incoming_check_new(){
		$kode_trans     = $this->uri->segment(3);
		
		$sql_header	= "SELECT a.*,b.id as id_ros, b.no_ros FROM warehouse_adjustment a left join report_of_shipment b on a.no_ros=b.id WHERE a.kode_trans='".$kode_trans."' ";
		$result_header		= $this->db->query($sql_header)->result();
		
		$pembeda = substr($result_header[0]->no_ipp,0,1);

		if($pembeda == 'P'){
			$sql 	= "	SELECT
							a.*,
							b.qty_purchase,
							b.qty_in,
							b.satuan,
							b.id AS id2,
							z.nm_material AS nm_material2,
							z.nilai_konversi,
							z.id_satuan,
							z.id_packing
						FROM
							warehouse_adjustment_detail a
							LEFT JOIN tran_material_po_detail b ON a.no_ipp=b.no_po AND a.id_po_detail = b.id
							LEFT JOIN raw_materials z ON a.id_material=z.id_material
						WHERE
							a.id_material = b.id_material
							AND a.kode_trans='".$kode_trans."' ";
		}
		if($pembeda == 'N'){
			$sql 	= "	SELECT
							a.*,
							b.qty_purchase,
							b.qty_in,
							b.id AS id2,
							z.nm_material AS nm_material2,
							z.nilai_konversi,
							z.id_satuan,
							z.id_packing
						FROM
							warehouse_adjustment_detail a
							LEFT JOIN tran_material_non_po_detail b ON a.no_ipp=b.no_non_po AND a.id_po_detail = b.id
							LEFT JOIN raw_materials z ON a.id_material=z.id_material
						WHERE
							a.id_material = b.id_material
							AND a.kode_trans='".$kode_trans."' ";
		}
		// echo $sql;
		$result			= $this->db->query($sql)->result_array();

		$data = array(
			'result' 	=> $result,
			'no_po' 	=> $result_header[0]->no_ipp,
			'dokumen_file' 	=> $result_header[0]->doc,
			'kode_trans' 	=> $result_header[0]->kode_trans,
			'id_header' 	=> $result_header[0]->id,
			'gudang_tujuan' 	=> $result_header[0]->kd_gudang_ke,
			'id_tujuan' 	=> $result_header[0]->id_gudang_ke,
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 	=> date('d F Y', strtotime($result_header[0]->created_date)),
			'id_ros'	=> $result_header[0]->id_ros,
			'no_ros'	=> $result_header[0]->no_ros,
			'total_freight'	=> $result_header[0]->total_freight,
		);

		$this->load->view('Warehouse/modal_incoming_check_new', $data);
	}

	public function modal_detail_qr(){
		$kode_trans = $this->uri->segment(3);
		$tanda     	= $this->uri->segment(4);

		$result			= $this->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();
		$result_header	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();

		$data = array(
			'result' 	=> $result,
			'tanda' 	=> $tanda,
			'dokumen_file' 	=> $result_header[0]->doc,
			'checked' 	=> $result_header[0]->checked,
			'kode_trans'=> $result_header[0]->kode_trans,
			'no_po' 	=> $result_header[0]->no_ipp,
			'no_ipp' 	=> $result_header[0]->no_ipp,
			'qty_spk' 	=> $result_header[0]->qty_spk,
			'no_ros' 	=> $result_header[0]->no_ros,
			'tanggal' 	=> (!empty($result_header[0]->tanggal))?date('d-M-Y',strtotime($result_header[0]->tanggal)):'',
			'id_milik' 	=> get_name('production_detail','id_milik','no_spk',$result_header[0]->no_spk),
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 		=> date('d F Y', strtotime($result_header[0]->created_date))

		);

		$this->load->view('Warehouse/modal_detail_qr', $data);
	}

	public function save_download_qr(){
		$group_id = implode('-', $this->input->post('checkboxx'));
		echo json_encode(['id' => $group_id]);
	}

	public function download_incoming_checked_qr($id)
	{
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/', $data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		//update status qc
		$explode = explode("-", $id);
		$this->db->select('a.*, b.nm_lengkap');
		$this->db->from('warehouse_adjustment_check a');
		$this->db->join('users b', 'b.username = a.update_by', 'left');
		$this->db->where_in('a.id', explode('-', $id));
		$getData = $this->db->get()->result_array();

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'detail' => $getData,
		);

		$this->load->view('Warehouse/download_incoming_checked_qr', $data);
	}

	public function modal_history_lot(){
		$id_material 	= $this->uri->segment(3);
		$id_gudang 		= $this->uri->segment(4);

		$result		= $this->db
							->select('a.*, b.id_satuan, b.id_packing')
							->join('raw_materials b','a.id_material=b.id_material','left')
							->get_where('warehouse_adjustment_check a', array('a.id_material'=>$id_material, 'a.qr'=>'1'))->result_array();
		$material	= $this->db->get_where('raw_materials', array('id_material'=>$id_material))->result_array();

		$data = array(
			'result' => $result,
			'material' => $material,
			'id_gudang' => $id_gudang
		);

		$this->load->view('Warehouse/modal_history_lot', $data);
	}

	public function modal_create_spk_req(){
		if($this->input->post()){
			$post = $this->input->post();
			$data_session	= $this->session->userdata;
			$check 		= $post['id_lot'];
			$kode_trans = $post['kode_trans'];

			$ArrInsert = [];
			$ArrUpdate = [];
			foreach ($check as $key => $value) {
				$qty_request = str_replace(',','',$post['request_'.$value]);
				$konversi = $post['konversi_'.$value];

				$ArrInsert[$key]['kode_trans'] = $kode_trans;
				$ArrInsert[$key]['id_lot'] = $value;
				$ArrInsert[$key]['qty_pack'] = $qty_request;
				$ArrInsert[$key]['konversi'] = $konversi;
				$ArrInsert[$key]['qty_unit'] = $qty_request * $konversi;
				$ArrInsert[$key]['id_material'] = $post['id_material_'.$value];
				$ArrInsert[$key]['id_satuan'] = $post['id_satuan_'.$value];
				$ArrInsert[$key]['id_packing'] = $post['id_packing_'.$value];
				$ArrInsert[$key]['created_by'] = $data_session['ORI_User']['username'];
				$ArrInsert[$key]['created_date'] = date('Y-m-d H:i:s');

				$getQtyBooking = $this->db->get_where('warehouse_adjustment_check',array('id'=>$value))->result_array();
				$qtyBooking = (!empty($getQtyBooking[0]['qty_booking']))?$getQtyBooking[0]['qty_booking']:0;

				$ArrUpdate[$key]['id'] 			= $value;
				$ArrUpdate[$key]['qty_booking'] = $qtyBooking + ($qty_request * $konversi);
			}

			$this->db->trans_start();
				if(!empty($ArrUpdate)){
					$this->db->update_batch('warehouse_adjustment_check', $ArrUpdate, 'id');
				}
				if(!empty($ArrInsert)){
					$this->db->insert_batch('warehouse_adjustment_spk', $ArrInsert);
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
				history('Create SPK Outgoing '.$kode_trans);
			}
			echo json_encode($Arr_Data);

		}
		else{
			$kode_trans     = $this->uri->segment(3);

			$result_header	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
			$listMaterial	= $this->db
								->select('a.id_material, SUM(qty_order) AS qty, b.id_satuan, b.id_packing, b.nilai_konversi AS konversi, b.nm_material')
								->group_by('a.id_material')
								->join('raw_materials b','a.id_material=b.id_material','left')
								->get_where('warehouse_adjustment_detail a',array('a.kode_trans'=>$kode_trans))
								->result_array();
			$ArrMat = [];
			$ArrMatQty = [];
			$ArrRequestMaterial = [];
			foreach ($listMaterial as $key => $value) {
				$ArrMat[] = $value['id_material'];
				$ArrMatQty[$value['id_material']] = $value['qty'];
				$ArrRequestMaterial[$key]['id_material'] = $value['id_material'];
				$ArrRequestMaterial[$key]['qty'] = $value['qty'];
				$ArrRequestMaterial[$key]['id_satuan'] = $value['id_satuan'];
				$ArrRequestMaterial[$key]['id_packing'] = $value['id_packing'];
				$ArrRequestMaterial[$key]['konversi'] = $value['konversi'];
				$ArrRequestMaterial[$key]['nm_material'] = $value['nm_material'];
			}

			$listLotMaterial = $this->db
									->select('
										a.id,
										a.id_material,
										a.expired_date,
										a.qty_oke,
										a.qty_out,
										a.qty_booking,
										a.keterangan,
										b.nm_material,
										b.id_satuan,
										b.id_packing,
										b.nilai_konversi AS konversi,
										a.update_by,
										a.update_date
									')
									->where_in('a.id_material',$ArrMat)
									->where('a.qty_oke > a.qty_out')
									->join('raw_materials b','a.id_material=b.id_material','left')
									->get_where('warehouse_adjustment_check a',array('a.qr'=>'1'))
									->result_array();


			$data = array(
				'ArrRequestMaterial' 	=> $ArrRequestMaterial,
				'listLotMaterial' 	=> $listLotMaterial,
				'ArrMatQty' 		=> $ArrMatQty,
				'checked' 		=> $result_header[0]->checked,
				'gudang_before' => $result_header[0]->id_gudang_dari,
				'gudang_after' 	=> $result_header[0]->id_gudang_ke,
				'kode_trans'	=> $result_header[0]->kode_trans,
				'no_ipp'		=> $result_header[0]->no_ipp,
				'dated' 		=> date('ymdhis', strtotime($result_header[0]->created_date)),
				'resv' 			=> date('d F Y', strtotime($result_header[0]->created_date)),
				'createdBy' 	=> get_name('users','nm_lengkap','username',$result_header[0]->created_by),
			);

			$this->load->view('Warehouse/modal_create_spk_req', $data);
		}
	}

	public function print_surat_jalan_spk(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'check' => $check
		);
		history('Print SJ SPK '.$kode_trans);
		$this->load->view('Print/print_sj_material_spk', $data);
	}

	public function print_surat_jalan_spk_confirm(){
        $kode_trans     = $this->uri->segment(3);
        $check             = $this->uri->segment(4);
        $data_session    = $this->session->userdata;
        $printby        = $data_session['ORI_User']['username'];

        $data_url        = base_url();
        $Split_Beda        = explode('/',$data_url);
        $Jum_Beda        = count($Split_Beda);
        $Nama_Beda        = $Split_Beda[$Jum_Beda - 2];

        $data = array(
            'Nama_Beda' => $Nama_Beda,
            'printby' => $printby,
            'kode_trans' => $kode_trans,
            'check' => $check
        );
        history('Print Cofirm SJ SPK '.$kode_trans);
        $this->load->view('Print/print_sj_material_spk_confirm', $data);
    }

	public function modal_request_edit_new(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;

			$detail			= $data['detail'];
			$kode_trans		= $data['kode_trans'];
			$Ym 			= date('ym');
			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;

			$ArrDeatil		 	= array();
			$SUM_MAT = 0;
			foreach ($detail as $key => $value) {
				$qty_awal 	= str_replace(',','',$value['edit_qty_before']);
				$qty_revisi = str_replace(',','',$value['edit_qty']);
				$konversi 	= str_replace(',','',$value['konversi']);

				$SUM_MAT += $qty_revisi * $konversi;
				$ArrDeatil[$key]['id'] 			= $value['id'];
				$ArrDeatil[$key]['qty_order'] 	= $qty_revisi * $konversi;
				$ArrDeatil[$key]['qty_oke'] 	= $qty_revisi * $konversi;
				$ArrDeatil[$key]['keterangan'] 	= $value['keterangan'];
			}

			$ArrUpdate = array(
				'jumlah_mat' => $SUM_MAT,
				'created_by' => $UserName,
				'created_date' => $DateTime
			);

			// exit;
			$this->db->trans_start();
				$this->db->where('kode_trans', $kode_trans);
				$this->db->update('warehouse_adjustment', $ArrUpdate);

				$this->db->update_batch('warehouse_adjustment_detail',$ArrDeatil,'id');
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
				history("Update request material (gudang produksi) : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$kode_trans     = $this->uri->segment(3);

			$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
			$result_header		= $this->db->query($sql_header)->result();

			$sql 		= "	SELECT
								a.*,
								b.qty_stock AS stock,
								c.id_satuan,
								c.id_packing,
								c.nilai_konversi AS konversi
							FROM
								warehouse_adjustment_detail a
								LEFT JOIN warehouse_stock b ON a.id_material = b.id_material
								LEFT JOIN raw_materials c ON a.id_material=c.id_material
							WHERE
								a.kode_trans='".$kode_trans."'
								AND b.id_gudang = '".$result_header[0]->id_gudang_dari."'
							";
			$result		= $this->db->query($sql)->result_array();

			$data = array(
				'result' 		=> $result,
				'checked' 		=> $result_header[0]->checked,
				'gudang_before' => $result_header[0]->id_gudang_dari,
				'gudang_after' 	=> $result_header[0]->id_gudang_ke,
				'kode_trans'	=> $result_header[0]->kode_trans,
				'no_ipp'		=> $result_header[0]->no_ipp,
				'dated' 		=> date('ymdhis', strtotime($result_header[0]->created_date)),
				'resv' 			=> date('d F Y', strtotime($result_header[0]->created_date))

			);

			$this->load->view('Warehouse/modal_request_edit_new', $data);
		}
	}

	public function auto_update_pr_material(){
		$data = $this->input->post();
		$category_awal = $this->uri->segment(3);
		$tgl_now = date('Y-m-d');
		$tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
		$get_rutin 	= $this->db->get_where('raw_materials',array('delete'=>'N'))->result_array();
		$ArrUpdate = [];

		foreach ($get_rutin as $key => $value) {
			$get_stock 		= $this->db->select('qty_stock AS stock')->where_in('id_gudang',array(1,2))->get_where('warehouse_stock',array('id_material'=>$value['id_material']))->result();
			$qtypr 		= get_qty_pr($value['id_material']);
			$stock_oke 	= (!empty($get_stock[0]->stock))?$get_stock[0]->stock:0;
			$purchase 	= (($value['max_stock']/30) * $value['kg_per_bulan']) - $stock_oke - $qtypr;
			$purchase2 	= ($purchase < 0)?0:ceil($purchase);

			$ArrUpdate[$key]['id_material'] = $value['id_material'];
			$ArrUpdate[$key]['request'] = $purchase2;
			$ArrUpdate[$key]['tgl_dibutuhkan'] = $tgl_next_month;
		}
		
		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch('raw_materials', $ArrUpdate,'id_material');
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
  			history('Update auto material pr');
  		}
  		echo json_encode($Arr_Data);
	}

	//MATERIAL STOCK TRAS
	public function material_stock_tras(){
		$this->warehouse_model->index_material_stock_tras();
	}
	
	public function server_side_material_stock_tras(){
		$this->warehouse_model->get_data_json_material_stock_tras();
	}

	public function modal_history_tras(){
		$this->warehouse_model->modal_history_tras();
	}

	public function modal_history_booking_tras(){
		$this->warehouse_model->modal_history_booking_tras();
	}

}
