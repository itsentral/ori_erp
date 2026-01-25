<?php
class Purchase_order_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function index_po(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' ")->result_array();
		$data = array(
			'title'			=> 'Pembelian Material >> Request For Quotation',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View RFQ Material');
		$this->load->view('Purchase_order/material_purchase',$data);
	}
	
	public function modal_detail_po(){
		$no_rfq 	= $this->uri->segment(3);

		$result		= $this->db->get_where('tran_material_rfq_detail', array('no_rfq'=>$no_rfq, 'deleted'=>'N'))->result_array();
		$num_rows	= $this->db->group_by('id_supplier')->get_where('tran_material_rfq_detail', array('no_rfq'=>$no_rfq, 'deleted'=>'N'))->num_rows();
		
		$data = array(
			'result' => $result,
			'num_rows' => $num_rows
		);
		
		$this->load->view('Purchase_order/modal_detail_po', $data);
	}
	
	public function modal_edit_po(){
		$no_rfq = $this->uri->segment(3);

		$sql 		= "	SELECT 
							a.*,
							b.moq,
							b.tanggal,
							b.no_pr,
							b.created_date AS tgl_pr
						FROM 
							tran_material_rfq_detail a 
							LEFT JOIN tran_material_pr_detail b ON a.no_rfq=b.no_rfq
						WHERE 
							a.id_material = b.id_material
							AND a.no_rfq='".$no_rfq."' 
							AND a.deleted='N' 
						GROUP BY 
							a.id_material ORDER BY id DESC";
		$result		= $this->db->query($sql)->result_array();
		
		$query = "SELECT id_supplier, nm_supplier FROM supplier WHERE sts_aktif='aktif' ORDER BY nm_supplier ASC ";
		$restQuery = $this->db->query($query)->result_array();
		
		$qDetailSup = "SELECT id_supplier FROM tran_material_rfq_header WHERE no_rfq='".$no_rfq."' AND deleted='N'";
		$restDetSup = $this->db->query($qDetailSup)->result_array();

		$supplierx = '';
		if(!empty($restDetSup)){
			$ArrData1 = array();
			foreach($restDetSup as $vaS => $vaA){
				 $ArrData1[] = $vaA['id_supplier'];
			}
			$ArrData1 = implode("," ,$ArrData1);
			$supplierx = explode("," ,$ArrData1);
		}
		
		$data = array(
			'result' => $result,
			'supList' => $restQuery,
			'supplierx' => $supplierx,
			'no_rfq' => $no_rfq
		);

		$this->load->view('Purchase_order/modal_edit_po', $data);
	}
	
	public function modal_add_po(){
		$query = "SELECT id_supplier, nm_supplier FROM supplier WHERE sts_aktif='aktif' ORDER BY nm_supplier ASC ";
		$restQuery = $this->db->query($query)->result_array();
		$data = array(
			'supList' => $restQuery
		);
		$this->load->view('Purchase_order/modal_add_po', $data);
	}
	
	public function print_po(){
		$no_po		= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'no_po' => $no_po
		);
		history('Print Purchase Order '.$no_po);
		$this->load->view('Print/print_po_new', $data); 
	}
	
	public function save_po(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;

		$Ym				= date('ym');

		//pengurutan kode
		$srcMtr			= "SELECT MAX(no_rfq) as maxP FROM tran_material_rfq_header WHERE no_rfq LIKE 'RFQ".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$no_rfq			= "RFQ".$Ym.$urut2;

		$id_supplier	= $data['id_supplier'];
		$check			= $data['check'];
		$ArrList 		= array();
		foreach($check AS $vaxl){
			$ArrList[$vaxl] = $vaxl;
		}
		$dtImplode		= "('".implode("','", $ArrList)."')";
		
		// print_r($id_supplier);
		
		//nm supplier
		


		// $qList 		= "SELECT * FROM warehouse_planning_detail WHERE no_pr IN ".$dtImplode."  ";
		// $restList 	= $this->db->query($qList)->result_array();
		
		$qListPRD 		= "SELECT * FROM tran_material_pr_detail WHERE id IN ".$dtImplode."  ";
		$restListPRD 	= $this->db->query($qListPRD)->result_array();
		
		// $qListPRH 		= "SELECT * FROM tran_material_pr_header WHERE no_pr IN ".$dtImplode."  ";
		// $restListPRH 	= $this->db->query($qListPRH)->result_array();

		$qListG 	= "SELECT id,tanggal, id_material, idmaterial, nm_material, SUM(qty_revisi) AS purchase, category FROM tran_material_pr_detail WHERE id IN ".$dtImplode." GROUP BY id_material";
		$restListG 	= $this->db->query($qListG)->result_array();

		//insert detail
		
		
		
		$ArrDetail = array();
		$ArrHeader = array();
		$no = 0;
		foreach($id_supplier AS $sup => $supx){
			$qSupplier			= "SELECT * FROM supplier WHERE id_supplier ='".$supx."' LIMIT 1 ";
			$restSupplier		= $this->db->query($qSupplier)->result();
			$SUM_MAT = 0;
			
			$no++;
			$num = sprintf('%03s',$no);
			foreach($restListG AS $val => $valx){
				
				$SUM_MAT += $valx['purchase'];
				$ArrDetail[$sup.$val]['no_rfq'] 	 = $no_rfq;
				$ArrDetail[$sup.$val]['hub_rfq'] 	 = $no_rfq.'-'.$num;
				$ArrDetail[$sup.$val]['category'] 	= $valx['category'];
				$ArrDetail[$sup.$val]['id_material'] = $valx['id_material'];
				$ArrDetail[$sup.$val]['idmaterial']  = $valx['idmaterial'];
				$ArrDetail[$sup.$val]['nm_material'] = $valx['nm_material'];
				$ArrDetail[$sup.$val]['id_supplier'] = $supx;
				$ArrDetail[$sup.$val]['tgl_dibutuhkan'] = $valx['tanggal'];
				$ArrDetail[$sup.$val]['nm_supplier'] = $restSupplier[0]->nm_supplier;
				$ArrDetail[$sup.$val]['qty'] 		 = $valx['purchase'];
				$ArrDetail[$sup.$val]['created_by'] 		 = $data_session['ORI_User']['username'];
				$ArrDetail[$sup.$val]['created_date'] 		 = date('Y-m-d H:i:s');
			}
			
			$ArrHeader[$sup]['no_rfq'] 			= $no_rfq;
			$ArrHeader[$sup]['hub_rfq'] 		= $no_rfq.'-'.$num;
			$ArrHeader[$sup]['id_supplier'] 	= $supx;
			$ArrHeader[$sup]['nm_supplier'] 	= $restSupplier[0]->nm_supplier;
			$ArrHeader[$sup]['total_request'] 	= $SUM_MAT;
			$ArrHeader[$sup]['created_by'] 		= $data_session['ORI_User']['username'];
			$ArrHeader[$sup]['created_date'] 	= date('Y-m-d H:i:s');
			$ArrHeader[$sup]['updated_by'] 		= $data_session['ORI_User']['username'];
			$ArrHeader[$sup]['updated_date'] 	= date('Y-m-d H:i:s');

		}

		//update detail
		// $ArrDetailUpdate = array();
		// foreach($restList AS $val => $valx){
			// $ArrDetailUpdate[$val]['id'] 	= $valx['id'];
			// $ArrDetailUpdate[$val]['no_rfq'] = $no_rfq;
		// }
		
		$ArrDetailUpdatePRD = array();
		foreach($restListPRD AS $val => $valx){
			$ArrDetailUpdatePRD[$val]['id'] 	= $valx['id'];
			$ArrDetailUpdatePRD[$val]['no_rfq'] = $no_rfq;
		}
		
		// $ArrDetailUpdatePRH = array();
		// foreach($restListPRH AS $val => $valx){
			// $ArrDetailUpdatePRH[$val]['no_pr'] 	= $valx['no_pr'];
			// $ArrDetailUpdatePRH[$val]['no_rfq'] = $no_rfq;
			// $ArrDetailUpdatePRH[$val]['sts_ajuan'] = 'PRS';
		// }

		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit;

		$this->db->trans_start();
			$this->db->insert_batch('tran_material_rfq_header', $ArrHeader);
			$this->db->insert_batch('tran_material_rfq_detail', $ArrDetail);
			// $this->db->update_batch('warehouse_planning_detail', $ArrDetailUpdate, 'id');
			$this->db->update_batch('tran_material_pr_detail', $ArrDetailUpdatePRD, 'id');
			// $this->db->update_batch('tran_material_pr_header', $ArrDetailUpdatePRH, 'no_pr');
			
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Insert purchase order data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Insert purchase order data success. Thanks ...',
				'status'	=> 1
			);
			history('Create RFQ Purchase Order '.$no_rfq);
		}
		echo json_encode($Arr_Kembali);

	}
	
	public function update_po(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$no_rfq			= $data['no_rfq'];
		$id_supplier	= $data['id_supplier'];
		
		$check			= $data['check'];
		$ArrList 		= array();
		foreach($check AS $vaxl){
			$ArrList[$vaxl] = $vaxl;
		}
		$dtImplode		= "('".implode("','", $ArrList)."')";
		
		$qListG 	= "SELECT id, id_material, idmaterial, nm_material, SUM(qty_revisi) AS purchase, category FROM tran_material_pr_detail WHERE no_pr IN ".$dtImplode." GROUP BY id_material";
		$restListG 	= $this->db->query($qListG)->result_array();

		$ArrDetail = array();
		$ArrHeader = array();
		$no = 0;
		foreach($id_supplier AS $sup => $supx){
			$qSupplier			= "SELECT * FROM supplier WHERE id_supplier ='".$supx."' LIMIT 1 ";
			$restSupplier		= $this->db->query($qSupplier)->result();
			$SUM_MAT = 0;
			
			$no++;
			$num = sprintf('%03s',$no);
			foreach($restListG AS $val => $valx){
				
				$SUM_MAT += $valx['purchase'];
				$ArrDetail[$sup.$val]['no_rfq'] 	 = $no_rfq;
				$ArrDetail[$sup.$val]['hub_rfq'] 	 = $no_rfq.'-'.$num;
				$ArrDetail[$sup.$val]['id_material'] = $valx['id_material'];
				$ArrDetail[$sup.$val]['idmaterial']  = $valx['idmaterial'];
				$ArrDetail[$sup.$val]['nm_material'] = $valx['nm_material'];
				$ArrDetail[$sup.$val]['category'] 	 = $valx['category'];
				$ArrDetail[$sup.$val]['id_supplier'] = $supx;
				$ArrDetail[$sup.$val]['nm_supplier'] = $restSupplier[0]->nm_supplier;
				$ArrDetail[$sup.$val]['qty'] 		 = $valx['purchase'];
				$ArrDetail[$sup.$val]['created_by'] 		 = $data_session['ORI_User']['username'];
				$ArrDetail[$sup.$val]['created_date'] 		 = date('Y-m-d H:i:s');
			}
			
			$ArrHeader[$sup]['no_rfq'] 			= $no_rfq;
			$ArrHeader[$sup]['hub_rfq'] 		= $no_rfq.'-'.$num;
			$ArrHeader[$sup]['id_supplier'] 	= $supx;
			$ArrHeader[$sup]['nm_supplier'] 	= $restSupplier[0]->nm_supplier;
			$ArrHeader[$sup]['total_request'] 	= $SUM_MAT;
			$ArrHeader[$sup]['created_by'] 		= $data_session['ORI_User']['username'];
			$ArrHeader[$sup]['created_date'] 	= date('Y-m-d H:i:s');
			$ArrHeader[$sup]['updated_by'] 		= $data_session['ORI_User']['username'];
			$ArrHeader[$sup]['updated_date'] 	= date('Y-m-d H:i:s');

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
			history('Update Material RFQ '.$no_rfq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function cancel_po(){
		$data_session	= $this->session->userdata;
		$no_po			= $this->uri->segment(3);
		// echo $no_po;
		// exit;

		$ArrUpdateH = array(
			'sts_ajuan' => 'CNC',
			'cancel_by' => $data_session['ORI_User']['username'],
			'cancel_date' => date('Y-m-d H:i:s')
		);
		$ArrUpdateDetail = array(
			'deleted' => 'Y',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		);

		$ArrUpdateD = array(
			'no_po' => NULL
		);

		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_po', $no_po);
			$this->db->update('tran_material_purchase_header', $ArrUpdateH);

			$this->db->where('no_po', $no_po);
			$this->db->update('tran_material_purchase_detail', $ArrUpdateDetail);

			$this->db->where('no_po', $no_po);
			$this->db->update('warehouse_planning_detail', $ArrUpdateD);
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
			history('Cancel Purchasing Order '.$no_po);
		}
		echo json_encode($Arr_Data);
	}
	
	public function cancel_sebagian_po(){
		$data_session	= $this->session->userdata;
		$no_pr			= $this->uri->segment(3);
		$id_material	= $this->uri->segment(4);
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

		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_rfq', $no_rfq);
			$this->db->where('id_material', $id_material);
			$this->db->update('tran_material_rfq_detail', $ArrUpdateDetail);

			$this->db->where('no_rfq', $no_rfq);
			$this->db->where('no_pr', $no_pr);
			$this->db->where('id_material', $id_material);
			$this->db->update('warehouse_planning_detail', $ArrUpdateD);
			
			$this->db->where('no_rfq', $no_rfq);
			$this->db->where('no_pr', $no_pr);
			$this->db->where('id_material', $id_material);
			$this->db->update('tran_material_pr_detail', $ArrUpdateD);
			
			$this->db->where('no_rfq', $no_rfq);
			$this->db->where('no_pr', $no_pr);
			$this->db->update('tran_material_pr_header', $ArrUpdateD);
			
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0,
				'no_po'		=> $no_rfq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1,
				'no_po'		=> $no_rfq
			);
			history('Cancel Sebagian Material RFQ '.$no_rfq.'/'.$id_material.'/'.$no_pr);
		}
		echo json_encode($Arr_Data);
	}
	
	public function spk_po(){
		$id_bq		= $this->uri->segment(3);
		// echo $id_bq; exit;
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/controllers/plusPrintPlanning.php";
		
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print Material Purchase '.$id_bq);

		PrintSPKPurchase($Nama_Beda, $id_bq, $koneksi, $printby);
	}
	
	public function pengajuan_rfq(){
		$data_session	= $this->session->userdata;
		$no_rfq			= $this->uri->segment(3);
		// echo $no_po;
		// exit;

		$ArrUpdateH = array(
			'sts_ajuan' 	=> 'AJU',
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_rfq', $no_rfq);
			$this->db->update('tran_material_rfq_header', $ArrUpdateH);
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
			history('Pengajuan Purchasing RFQ '.$no_rfq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function print_rfq(){
		$no_rfq		= $this->uri->segment(3);
		// echo $id_bq; exit;
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
//		include $sroot."/application/views/Print/print_rfq.php";
		require_once(APPPATH.'views/Print/print_rfq.php');
		
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print Request From Quotation '.$no_rfq);

		print_rfq($Nama_Beda, $no_rfq, $koneksi, $printby);
	}
	
	public function modal_edit_rfq(){
		if($this->input->post()){
			$data_session	= $this->session->userdata;
			$data	= $this->input->post();
			
			$ArrHeader = array(
				'incoterms' 	=> strtolower($data['incoterms']),
				'top' 			=> strtolower($data['top']),
				'remarks' 		=> strtolower($data['remarks']),
				'updated_print_by' 	=> $data_session['ORI_User']['username'],
				'updated_print_date' 	=> date('Y-m-d H:i:s')
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
			
			$this->load->view('Purchase_order/modal_edit_rfq', $data);
		}
	}
	
	//PERBANDINGAN
	public function index_perbandingan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' ")->result_array();
		$data = array(
			'title'			=> 'Pembelian Material >> Table Perbandingan',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View Purchase Order Table Perbandingan');
		$this->load->view('Purchase_order/perbandingan',$data);
	}
	
	public function add_perbandingan(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$no_rfq 		= $data['no_rfq'];
			$header 		= $data['Header'];
			$detail 		= $data['Detail'];
			// print_r($data);
			
			$ArrHeader = array();
			$ArrDetail = array();
			foreach($header AS $val => $valx){
				$ArrHeader[$val]['id'] 				= $valx['id'];
				$ArrHeader[$val]['lokasi'] 			= $valx['lokasi'];
				$ArrHeader[$val]['alamat_supplier'] = $valx['alamat'];
				$ArrHeader[$val]['currency'] 		= $valx['currency'];
				$ArrHeader[$val]['kurs'] 			= str_replace(',','',$valx['kurs']);
				$ArrHeader[$val]['sts_ajuan'] 		= 'PRS';
				$ArrHeader[$val]['sts_process'] 	= 'Y';
				$ArrHeader[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
				$ArrHeader[$val]['updated_date'] 	= date('Y-m-d H:i:s');
			}
			
			foreach($detail AS $val => $valx){
				foreach($valx['detail'] AS $val2 => $valx2){
					$ArrDetail[$val.$val2]['id'] 				= $valx2['id'];
					$ArrDetail[$val.$val2]['price_ref_sup'] 	= str_replace(',','',$valx2['price_ref_sup']);
					$ArrDetail[$val.$val2]['moq'] 				= str_replace(',','',$valx2['moq']);
					$ArrDetail[$val.$val2]['lead_time'] 		= str_replace(',','',$valx2['lead_time']);
					$ArrDetail[$val.$val2]['price_ref'] 		= str_replace(',','',$valx2['price_ref']);
					$ArrDetail[$val.$val2]['harga_idr'] 		= str_replace(',','',$valx2['harga_idr']);
					$ArrDetail[$val.$val2]['total_harga'] 		= str_replace(',','',$valx2['total_harga']);
					$ArrDetail[$val.$val2]['qty'] 				= str_replace(',','',$valx2['qty']);
					$ArrDetail[$val.$val2]['tgl_dibutuhkan'] 	= $valx2['tgl_dibutuhkan'];
					$ArrDetail[$val.$val2]['satuan'] 			= $valx2['satuan'];
				}
			}
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// exit;

			$this->db->trans_start();
				$this->db->update_batch('tran_material_rfq_header', $ArrHeader, 'id');
				$this->db->update_batch('tran_material_rfq_detail', $ArrDetail, 'id');
				
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('Create Table Perbandingan '.$no_rfq);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$no_rfq = $this->uri->segment(3);
			$result = $this->db->order_by('id','ASC')->get_where('tran_material_rfq_header', array('no_rfq'=>$no_rfq))->result_array();
			$list_satuan = $this->db->order_by('kode_satuan','asc')->get_where('raw_pieces',array('delete'=>'N'))->result_array();
			$currency = $this->db->get_where('currency',array('flag'=>1))->result_array();
			$data = array(
				'title'			=> 'Add Table Perbandingan',
				'action'		=> 'index',
				'result' 		=> $result,
				'currency' 		=> $currency,
				'list_satuan' 	=> $list_satuan
			);
			$this->load->view('Purchase_order/add_perbandingan', $data);
		}
	}
	
	public function modal_detail_perbandingan(){
		$no_rfq 	= $this->uri->segment(3);

		$sql 		= "	SELECT 
							a.*,
							b.alamat_supplier,
							b.lokasi,
							b.currency,
							b.kurs
						FROM 
							tran_material_rfq_detail a 
							LEFT JOIN tran_material_rfq_header b ON a.no_rfq=b.no_rfq
						WHERE 
							a.no_rfq='".$no_rfq."'
							AND a.hub_rfq=b.hub_rfq
							AND a.deleted='N' ORDER BY a.hub_rfq ASC
						";
							
		$result		= $this->db->query($sql)->result_array();
		
		$num_rows	= $this->db->group_by('id_supplier')->get_where('tran_material_rfq_detail', array('no_rfq'=>$no_rfq, 'deleted'=>'N'))->num_rows();					
		
		$data = array(
			'result' => $result,
			'num_rows' => $num_rows
		);
		
		$this->load->view('Purchase_order/modal_detail_perbandingan', $data);
	}
	
	public function index_pengajuan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' ")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Table Pengajuan',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View Purchase Order Table Pengajuan');
		$this->load->view('Purchase_order/pengajuan',$data);
	}
	
	public function modal_detail_pengajuan(){
		$no_rfq 	= $this->uri->segment(3);

		$sql 		= "	SELECT 
							a.*,
							b.alamat_supplier,
							b.lokasi
						FROM 
							tran_material_rfq_detail a 
							LEFT JOIN tran_material_rfq_header b ON a.no_rfq=b.no_rfq
						WHERE 
							a.no_rfq='".$no_rfq."'
							AND a.hub_rfq=b.hub_rfq
							AND a.deleted='N' ORDER BY a.hub_rfq ASC
						";
							
		$result		= $this->db->query($sql)->result_array();
		
		$sql2 		= "	SELECT 
							a.*
						FROM 
							tran_material_rfq_detail a 
						WHERE 
							a.no_rfq='".$no_rfq."' 
							AND a.deleted='N' GROUP BY id_supplier
						";
							
		$num_rows		= $this->db->query($sql2)->num_rows();
		
		$data = array(
			'result' => $result,
			'num_rows' => $num_rows
		);
		
		$this->load->view('Purchase_order/modal_detail_pengajuan', $data);
	}
	
	public function modal_pemilihan(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$no_rfq 		= $data['no_rfq'];
			if(!empty($data['check'])){
			$detail 		= $data['check'];
			}
			$username 		= $data_session['ORI_User']['username'];
			$datetime 		= date('Y-m-d H:i:s');
			// print_r($data);

			$ArrDetail = array();
			if(!empty($data['check'])){
				foreach($detail AS $val){
					$ArrDetail[$val]['id'] 			= $val;		
					$ArrDetail[$val]['status'] 		= 'SETUJU';
					$ArrDetail[$val]['setuju_by'] 	= $username;
					$ArrDetail[$val]['setuju_date'] = $datetime;
				}
			}
			
			$ArrHeader = array(
				'sts_ajuan' => 'APV'
			);
			
			// print_r($ArrDetail);
			// exit;
			
			$this->db->trans_start();
				$this->db->update_batch('tran_material_rfq_detail', $ArrDetail, 'id');
				
				$this->db->where(array('no_rfq'=>$no_rfq));
				$this->db->update('tran_material_rfq_header', $ArrHeader);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert purchase order data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert purchase order data success. Thanks ...',
					'status'	=> 1
				);
				history('Create Pemilihan Supplier '.$no_rfq);
			}
			echo json_encode($Arr_Kembali);
		}
		else{	
			$no_rfq 	= $this->uri->segment(3);

			$result		= $this->db
							->select('a.*, b.currency')
							->order_by('a.nm_supplier, a.id','ASC')
							->join('tran_material_rfq_header b','a.hub_rfq=b.hub_rfq','left')
							->get_where('tran_material_rfq_detail a',array(
								'a.no_rfq' => $no_rfq,
								'a.deleted' => 'N'
							))
							->result_array();
			
			$resultNew		= 	$this->db->select('a.*')->group_by('a.id_material')->get_where('tran_material_rfq_detail a',array(
								'a.no_rfq' => $no_rfq,
								'a.deleted' => 'N',
							))
							->result_array();
			$resultSup		= 	$this->db->select('a.*')->group_by('a.hub_rfq')->get_where('tran_material_rfq_header a',array(
									'a.no_rfq' => $no_rfq
								))
								->result_array();
			$ArraySerach = [];
			foreach ($result as $key => $value) {
				$UNIQ = $value['id_material'].'-'.$value['hub_rfq'];
				$ArraySerach[$UNIQ]['moq'] = $value['moq'];
				$ArraySerach[$UNIQ]['lead_time'] = $value['lead_time'];
				$ArraySerach[$UNIQ]['harga_idr'] = $value['harga_idr'];
				$ArraySerach[$UNIQ]['total_harga'] = $value['harga_idr']*$value['qty'];
				$ArraySerach[$UNIQ]['id'] = $value['id'];
			}
			
			$data = array(
				'resultNew' 	=> $resultNew,
				'resultSup' 	=> $resultSup,
				'ArraySerach' 	=> $ArraySerach,
				'result' 	=> $result,
				'no_rfq' 	=> $no_rfq
			);
			
			$this->load->view('Purchase_order/modal_pemilihan', $data);
		}
	}
	
	public function print_hasil_pemilihan(){
		$no_rfq			= $this->uri->segment(3);
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		//		include $sroot."/application/controllers/plusPurchaseOrder.php";
		require_once(APPPATH.'controllers/plusPurchaseOrder.php');

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");
		
		history('Print Hasil Pemilihan RFQ '.$no_rfq);
		
		print_pemilihan_rfq($Nama_Beda, $no_rfq, $koneksi, $printby);
	}
	
	public function index_approval(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' ")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Approval RFQ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View Approval RFQ');
		$this->load->view('Purchase_order/approval',$data);
	}
	
	public function modal_detail_approve(){
		$no_rfq 	= $this->uri->segment(3);

		$sql 		= "	SELECT 
							a.*,
							b.alamat_supplier,
							b.lokasi,
							b.currency
						FROM 
							tran_material_rfq_detail a 
							LEFT JOIN tran_material_rfq_header b ON a.no_rfq=b.no_rfq
						WHERE 
							a.no_rfq='".$no_rfq."'
							AND a.hub_rfq=b.hub_rfq
							AND (a.status='SETUJU' OR a.status='CLOSE')
							AND (b.sts_ajuan = 'APV' OR b.sts_ajuan = 'CLS')
							AND a.deleted='N' ORDER BY a.hub_rfq ASC
						";
							
		$result		= $this->db->query($sql)->result_array();
		$num_rows	= $this->db->query($sql)->num_rows();
		
		$data = array(
			'result' => $result,
			'num_rows' => $num_rows
		);
		
		$this->load->view('Purchase_order/modal_detail_approve', $data);
	}
	
	public function modal_approve(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$no_rfq 		= $data['no_rfq'];
			if(!empty($data['check'])){
			$detail 		= $data['check'];
			}
			$username 		= $data_session['ORI_User']['username'];
			$datetime 		= date('Y-m-d H:i:s');

			$ArrDetail = array();
			if(!empty($data['check'])){
				foreach($detail AS $val){
					$ArrDetail[$val]['id'] 			= $val;		
					$ArrDetail[$val]['status_apv'] 		= 'SETUJU';
					$ArrDetail[$val]['close_by'] 	= $username;
					$ArrDetail[$val]['close_date'] = $datetime;
				}
			}
			
			$ArrUpd2 = array(
				'sts_ajuan' => 'CLS'
			);

			// print_r($ArrDetail);
			// exit;
			
			$this->db->trans_start();
				$this->db->update_batch('tran_material_rfq_detail', $ArrDetail, 'id');

				$this->db->where(array('no_rfq'=>$no_rfq));
				$this->db->update('tran_material_rfq_header', $ArrUpd2);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('CLOSE RFQ '.$no_rfq);
			}
			echo json_encode($Arr_Kembali);
		}
		else{	
			$no_rfq 	= $this->uri->segment(3);

			$result		= $this->db
							->select('a.*, b.currency')
							->order_by('a.nm_supplier, a.id','ASC')
							->join('tran_material_rfq_header b','a.hub_rfq=b.hub_rfq','left')
							->get_where('tran_material_rfq_detail a',array(
								'a.no_rfq' => $no_rfq,
								'a.deleted' => 'N',
								// 'a.status' => 'SETUJU'
							))
							->result_array();

			$resultNew		= 	$this->db->select('a.*')->group_by('a.id_material')->get_where('tran_material_rfq_detail a',array(
								'a.no_rfq' => $no_rfq,
								'a.deleted' => 'N',
							))
							->result_array();
			$resultSup		= 	$this->db->select('a.*')->group_by('a.hub_rfq')->get_where('tran_material_rfq_header a',array(
									'a.no_rfq' => $no_rfq
								))
								->result_array();
			$ArraySerach = [];
			foreach ($result as $key => $value) {
				$UNIQ = $value['id_material'].'-'.$value['hub_rfq'];
				$ArraySerach[$UNIQ]['moq'] = $value['moq'];
				$ArraySerach[$UNIQ]['lead_time'] = $value['lead_time'];
				$ArraySerach[$UNIQ]['harga_idr'] = $value['harga_idr'];
				$ArraySerach[$UNIQ]['total_harga'] = $value['harga_idr']*$value['qty'];
				$ArraySerach[$UNIQ]['id'] = $value['id'];
				$ArraySerach[$UNIQ]['status'] = $value['status'];
			}
			
			$data = array(
				'resultNew' 	=> $resultNew,
				'resultSup' 	=> $resultSup,
				'ArraySerach' 	=> $ArraySerach,
				'result' 	=> $result,
				'no_rfq' 	=> $no_rfq
			);
			
			$this->load->view('Purchase_order/modal_approve', $data);
		}
	}
	
	
	public function modal_approvex(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$tanda 			= $this->uri->segment(3);
			echo $tanda;
			exit;
		}
		else{
			$no_rfq 	= $this->uri->segment(3);

			$sql 		= "	SELECT 
								a.*,
								b.alamat_supplier,
								b.lokasi
							FROM 
								tran_material_rfq_detail a 
								LEFT JOIN tran_material_rfq_header b ON a.no_rfq=b.no_rfq
							WHERE 
								a.no_rfq='".$no_rfq."'
								AND a.hub_rfq=b.hub_rfq
								AND a.status='SETUJU'
								AND (b.sts_ajuan = 'APV' OR b.sts_ajuan = 'CLS')
								AND a.deleted='N' ORDER BY a.hub_rfq ASC
							";
								
			$result		= $this->db->query($sql)->result_array();
			
			$sql2 		= "	SELECT 
								a.*
							FROM 
								tran_material_rfq_detail a 
								LEFT JOIN tran_material_rfq_header b ON a.no_rfq=b.no_rfq
							WHERE 
								a.no_rfq='".$no_rfq."' 
								AND a.hub_rfq=b.hub_rfq
								AND (b.sts_ajuan = 'APV' OR b.sts_ajuan = 'CLS')
								AND a.status='SETUJU'
								AND a.deleted='N' GROUP BY id_supplier
							";
								
			$num_rows		= $this->db->query($sql2)->num_rows();
			
			$data = array(
				'result' => $result,
				'num_rows' => $num_rows
			);
			
			$this->load->view('Purchase_order/modal_approve', $data);
		}
	}
	
	
	public function index_purchase_order(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Pembelian Material >> List Purchase Order',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Purchase Order');
		$this->load->view('Purchase_order/purchase_order',$data);
	}
	
	public function modal_detail_purchase_order(){
			$no_po 		= $this->uri->segment(3);
			$result		= $this->db->get_where('tran_material_po_header', array('no_po'=>$no_po))->result();
			$data_kurs 	= $this->db->limit(1)->get_where('kurs', array('kode_dari'=>'USD'))->result();
			$sql_detail = "SELECT a.*, b.nm_supplier, b.mata_uang AS currency FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='".$no_po."' AND a.deleted='N'";
			$result_det		= $this->db->query($sql_detail)->result_array();
			$data_top		= $this->db->get_where('billing_top', array('no_po'=>$no_po))->result_array();			
			$payment = $this->db->get_where('list_help', array('group_by'=>'top'))->result_array();			
			$data = array(
				'data' 		=> $result,
				'data_kurs' => $data_kurs,
				'data_top' => $data_top,
				'payment' => $payment,
				'result' => $result_det
			);
			$this->load->view('Purchase_order/modal_detail_purchase_order', $data);
	}
	
	public function edit_po_qty(){
		if($this->input->post()){
			$data_session	= $this->session->userdata;
			$Username 		= $this->session->userdata['ORI_User']['username'];
			$dateTime		= date('Y-m-d H:i:s');
			$data	= $this->input->post();
			
			$detail = $data['detail'];
			$no_po = $data['no_po'];
			$tgl_dibutuhkan	= date('Y-m-d',strtotime($data['tanggal_dibutuhkan']));
			$total_po		= str_replace(',','',$data['total_po']);
			$discount		= str_replace(',','',$data['discount']);
			$net_price		= str_replace(',','',$data['net_price']);
			$tax			= str_replace(',','',$data['tax']);
			$net_plus_tax	= str_replace(',','',$data['net_plus_tax']);
			$delivery_cost	= str_replace(',','',$data['delivery_cost']);
			$grand_total	= str_replace(',','',$data['grand_total']);

			$ArrEdit = [];
			$SUM_MAT = 0;
			$nilai_ppn=0;
			$nilai_total=0;
			$nilai_plus_ppn=0;
			foreach($detail AS $val => $valx){
				$qty_po = str_replace(',','',$valx['qty_purchase']);
				$SUM_MAT += $qty_po;

				$ArrEdit[$val]['id'] 			= $valx['id'];
				$ArrEdit[$val]['total_price'] 	= $valx['total_price'];
				$ArrEdit[$val]['nm_material'] 	= $valx['nm_material'];
				$ArrEdit[$val]['qty_purchase'] 	= $qty_po;
				$ArrEdit[$val]['created_by'] 	= $Username;
				$ArrEdit[$val]['created_date'] 	= $dateTime;

				$price_ref_sup=$valx['price_ref_sup'];
				$nilai_total=($nilai_total+($price_ref_sup*$qty_po));
			}
			$nilai_ppn=($nilai_total*$tax/100);
			$nilai_plus_ppn=($nilai_ppn+$nilai_total);

			$ArrHeader['no_po'] 			= $no_po;
			$ArrHeader['total_material'] 	= $SUM_MAT;
			$ArrHeader['total_price'] 		= $grand_total;
			$ArrHeader['tax'] 				= $tax;
			$ArrHeader['total_po'] 			= $total_po;
			$ArrHeader['discount'] 			= $discount;
			$ArrHeader['net_price'] 		= $net_price;
			$ArrHeader['net_plus_tax'] 		= $net_plus_tax;
			$ArrHeader['delivery_cost'] 	= $delivery_cost;
			$ArrHeader['tgl_dibutuhkan'] 	= $tgl_dibutuhkan;
			$ArrHeader['updated_by'] 		= $Username;
			$ArrHeader['updated_date'] 		= $dateTime;

			$ArrHeader['nilai_total']		= $nilai_total;
			$ArrHeader['nilai_ppn']			= $nilai_ppn;
			$ArrHeader['nilai_plus_ppn']	= $nilai_plus_ppn;
			// print_r($ArrEdit);
			// exit;
			$this->db->trans_start();
				$this->db->update_batch('tran_material_po_detail', $ArrEdit, 'id');

				$this->db->where('no_po', $no_po);
				$this->db->update('tran_material_po_header', $ArrHeader);
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
				history('Edit qty PO : '.$no_po);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$no_po 	= $this->uri->segment(3);
			$get_status = $this->db->get_where('tran_material_po_header', array('no_po'=>$no_po))->result_array();
			
			$WHERE = [
				'a.no_po' => $no_po
			];
			if($get_status[0]['status'] != 'DELETED'){
				$WHERE = [
					'a.no_po' => $no_po,
					'a.deleted' => 'N'
				];
			}	
								
			$result		= $this->db
								->select('	a.*,
											b.nm_supplier,
											b.mata_uang AS currency,
											b.tgl_dibutuhkan AS tgl_butuh')
								->join('tran_material_po_header b','a.no_po=b.no_po','left')
								->get_where('tran_material_po_detail a', $WHERE)
								->result_array();
			
			$data = array(
				'result' => $result,
				'header' => $get_status
			);
			
			$this->load->view('Purchase_order/edit_po_qty', $data);
		}
	}
	
	public function delete_sebagian_po(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$detail_ch = $data['checked'];
		$no_po = $data['no_po'];
		
		$header_po 	= $this->db->get_where('tran_material_po_header', array('no_po'=>$no_po))->result();
		$detail 	= $this->db->select('*')->from('tran_material_po_detail')->where_in('id',$detail_ch)->where('no_po',$no_po)->where('deleted','N')->get()->result_array();
		
		//Urutab PR
		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_material_pr_header WHERE no_pr LIKE 'PR".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2); 
		$no_pr			= "PR".$Ym.$urut2;
		
		//Urutab RFQ
		$srcMtr			= "SELECT MAX(no_rfq) as maxP FROM tran_material_rfq_header WHERE no_rfq LIKE 'RFQ".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$no_rfq			= "RFQ".$Ym.$urut2;
		
		$ArrEdit = [];
		$ArrAddPR = [];
		$ArrAddRFQ = [];
		
		$SUM_MAT = 0;
		if(!empty($detail)){
			foreach($detail AS $val => $valx){
				$ArrEdit[$val]['id'] 			= $valx['id'];
				$ArrEdit[$val]['deleted'] 		= 'Y';
				$ArrEdit[$val]['deleted_by'] 	= $data_session['ORI_User']['username'];
				$ArrEdit[$val]['deleted_date'] = date('Y-m-d H:i:s');
				
				$SUM_MAT += $valx['qty_purchase'];
				
				$ArrAddPR[$val]['no_pr'] 		= $no_pr;
				$ArrAddPR[$val]['no_rfq'] 		= $no_rfq;
				$ArrAddPR[$val]['category'] 	= $valx['category'];
				$ArrAddPR[$val]['id_material'] 	= $valx['id_material'];
				$ArrAddPR[$val]['idmaterial'] 	= $valx['idmaterial'];
				$ArrAddPR[$val]['nm_material'] 	= $valx['nm_material'];
				$ArrAddPR[$val]['qty_request'] 	= $valx['qty_purchase'];
				$ArrAddPR[$val]['qty_revisi'] 	= $valx['qty_purchase'];
				$ArrAddPR[$val]['moq'] 			= $valx['moq'];
				$ArrAddPR[$val]['tanggal'] 		= $valx['tgl_dibutuhkan'];
				$ArrAddPR[$val]['created_by'] 	= $data_session['ORI_User']['username'];
				$ArrAddPR[$val]['created_date'] = date('Y-m-d H:i:s');
				
				$ArrAddRFQ[$val]['no_rfq'] 		= $no_rfq;
				$ArrAddRFQ[$val]['hub_rfq'] 	= $no_rfq."-001";
				$ArrAddRFQ[$val]['category'] 	= $valx['category'];
				$ArrAddRFQ[$val]['id_material'] = $valx['id_material'];
				$ArrAddRFQ[$val]['idmaterial'] 	= $valx['idmaterial'];
				$ArrAddRFQ[$val]['nm_material'] = $valx['nm_material'];
				$ArrAddRFQ[$val]['id_supplier'] = $header_po[0]->id_supplier;
				$ArrAddRFQ[$val]['nm_supplier'] = $header_po[0]->nm_supplier;
				$ArrAddRFQ[$val]['qty'] 		= $valx['qty_purchase'];
				$ArrAddRFQ[$val]['moq'] 			= $valx['moq'];
				$ArrAddRFQ[$val]['tgl_dibutuhkan'] 	= $valx['tgl_dibutuhkan'];
				$ArrAddRFQ[$val]['created_by'] 		= $data_session['ORI_User']['username'];
				$ArrAddRFQ[$val]['created_date'] 	= date('Y-m-d H:i:s');
			}
		}
		
		$ArrAddPRHeader = [
			'no_pr' => $no_pr,
			'total_material' => $SUM_MAT,
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		];
		
		$ArrAddRFQHeader = [
			'no_rfq' => $no_rfq,
			'hub_rfq' => $no_rfq."-001",
			'id_supplier' => $header_po[0]->id_supplier,
			'nm_supplier' => $header_po[0]->nm_supplier,
			'total_request' => $SUM_MAT,
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s'),
			'updated_by' => $data_session['ORI_User']['username'],
			'updated_date' => date('Y-m-d H:i:s')
		];
		
		// $ArrEditHeader = [
			// 'status' => 'DELETED',
			// 'deleted' => 'Y',
			// 'deleted_by' => $data_session['ORI_User']['username'],
			// 'deleted_date' => date('Y-m-d H:i:s')
		// ];
		
		// print_r($ArrAddPRHeader);
		// print_r($ArrAddPR);
		
		// print_r($ArrAddRFQHeader);
		// print_r($ArrAddRFQ);
		
		// print_r($ArrEditHeader);
		// print_r($ArrEdit);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrEdit)){
				$this->db->update_batch('tran_material_po_detail', $ArrEdit, 'id');
			}
			
			if(!empty($ArrAddPR)){
				$this->db->insert_batch('tran_material_pr_detail', $ArrAddPR);
				$this->db->insert('tran_material_pr_header', $ArrAddPRHeader);
			}
			
			if(!empty($ArrAddRFQ)){
				$this->db->insert_batch('tran_material_rfq_detail', $ArrAddRFQ);
				$this->db->insert('tran_material_rfq_header', $ArrAddRFQHeader);
			}
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
			history('Delete sebagian PO : '.$no_po);
		}
		echo json_encode($Arr_Data);
	}
	
	public function delete_sebagian_po_new(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$no_po 		= $data['no_po'];
		$id 		= $data['id'];
		
		$detail 	= $this->db->select('*')->from('tran_material_po_detail')->where('id',$id)->where('no_po',$no_po)->where('deleted','N')->get()->result_array();
		
		//Urutab PR
		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_material_pr_header WHERE no_pr LIKE 'PR".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2); 
		$no_pr			= "PR".$Ym.$urut2;
		
		$ArrEdit = [];
		$ArrAddPR = [];
		
		$SUM_MAT = 0;
		if(!empty($detail)){
			foreach($detail AS $val => $valx){
				$ArrEdit[$val]['id'] 			= $valx['id'];
				$ArrEdit[$val]['deleted'] 		= 'Y';
				$ArrEdit[$val]['deleted_by'] 	= $data_session['ORI_User']['username'];
				$ArrEdit[$val]['deleted_date'] = date('Y-m-d H:i:s');
				
				$SUM_MAT += $valx['qty_purchase'];
				
				$ArrAddPR[$val]['no_pr'] 		= $no_pr;
				$ArrAddPR[$val]['category'] 	= $valx['category'];
				$ArrAddPR[$val]['id_material'] 	= $valx['id_material'];
				$ArrAddPR[$val]['idmaterial'] 	= $valx['idmaterial'];
				$ArrAddPR[$val]['nm_material'] 	= $valx['nm_material'];
				$ArrAddPR[$val]['qty_request'] 	= $valx['qty_purchase'];
				$ArrAddPR[$val]['qty_revisi'] 	= $valx['qty_purchase'];
				$ArrAddPR[$val]['moq'] 			= $valx['moq'];
				$ArrAddPR[$val]['tanggal'] 		= $valx['tgl_dibutuhkan'];
				$ArrAddPR[$val]['created_by'] 	= $data_session['ORI_User']['username'];
				$ArrAddPR[$val]['created_date'] = date('Y-m-d H:i:s');
			}
		}
		
		$ArrAddPRHeader = [
			'no_pr' => $no_pr,
			'total_material' => $SUM_MAT,
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		];
		
		// print_r($ArrAddPRHeader);
		// print_r($ArrAddPR);
		
		// print_r($ArrEdit);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrEdit)){
				$this->db->update_batch('tran_material_po_detail', $ArrEdit, 'id');
			}
			
			if(!empty($ArrAddPR)){
				$this->db->insert_batch('tran_material_pr_detail', $ArrAddPR);
				$this->db->insert('tran_material_pr_header', $ArrAddPRHeader);
			}
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
			history('Delete sebagian PO : '.$no_po.' / '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	public function delete_semua_po(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$no_po = $data['no_po'];
		
		$header_po 	= $this->db->get_where('tran_material_po_header', array('no_po'=>$no_po))->result();
		$detail 	= $this->db->get_where('tran_material_po_detail', array('no_po'=>$no_po,'deleted'=>'N'))->result_array();
		
		//Urutab PR
		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_material_pr_header WHERE no_pr LIKE 'PR".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2); 
		$no_pr			= "PR".$Ym.$urut2;
		
		//Urutab RFQ
		// $srcMtr			= "SELECT MAX(no_rfq) as maxP FROM tran_material_rfq_header WHERE no_rfq LIKE 'RFQ".$Ym."%' ";
		// $numrowMtr		= $this->db->query($srcMtr)->num_rows();
		// $resultMtr		= $this->db->query($srcMtr)->result_array();
		// $angkaUrut2		= $resultMtr[0]['maxP'];
		// $urutan2		= (int)substr($angkaUrut2, 7, 4);
		// $urutan2++;
		// $urut2			= sprintf('%04s',$urutan2);
		// $no_rfq			= "RFQ".$Ym.$urut2;
		
		$ArrEdit = [];
		$ArrAddPR = [];
		$ArrAddRFQ = [];
		
		$SUM_MAT = 0;
		if(!empty($detail)){
			foreach($detail AS $val => $valx){
				$ArrEdit[$val]['id'] 			= $valx['id'];
				$ArrEdit[$val]['deleted'] 		= 'Y';
				$ArrEdit[$val]['deleted_by'] 	= $data_session['ORI_User']['username'];
				$ArrEdit[$val]['deleted_date'] = date('Y-m-d H:i:s');
				
				$SUM_MAT += $valx['qty_purchase'];
				
				$ArrAddPR[$val]['no_pr'] 		= $no_pr;
				// $ArrAddPR[$val]['no_rfq'] 		= $no_rfq;
				$ArrAddPR[$val]['category'] 	= $valx['category'];
				$ArrAddPR[$val]['id_material'] 	= $valx['id_material'];
				$ArrAddPR[$val]['idmaterial'] 	= $valx['idmaterial'];
				$ArrAddPR[$val]['nm_material'] 	= $valx['nm_material'];
				$ArrAddPR[$val]['qty_request'] 	= $valx['qty_purchase'];
				$ArrAddPR[$val]['qty_revisi'] 	= $valx['qty_purchase'];
				$ArrAddPR[$val]['moq'] 			= $valx['moq'];
				$ArrAddPR[$val]['tanggal'] 		= $valx['tgl_dibutuhkan'];
				$ArrAddPR[$val]['created_by'] 	= $data_session['ORI_User']['username'];
				$ArrAddPR[$val]['created_date'] = date('Y-m-d H:i:s');
				
				// $ArrAddRFQ[$val]['no_rfq'] 		= $no_rfq;
				// $ArrAddRFQ[$val]['hub_rfq'] 	= $no_rfq."-001";
				// $ArrAddRFQ[$val]['category'] 	= $valx['category'];
				// $ArrAddRFQ[$val]['id_material'] = $valx['id_material'];
				// $ArrAddRFQ[$val]['idmaterial'] 	= $valx['idmaterial'];
				// $ArrAddRFQ[$val]['nm_material'] = $valx['nm_material'];
				// $ArrAddRFQ[$val]['id_supplier'] = $header_po[0]->id_supplier;
				// $ArrAddRFQ[$val]['nm_supplier'] = $header_po[0]->nm_supplier;
				// $ArrAddRFQ[$val]['qty'] 		= $valx['qty_purchase'];
				// $ArrAddRFQ[$val]['moq'] 			= $valx['moq'];
				// $ArrAddRFQ[$val]['tgl_dibutuhkan'] 	= $valx['tgl_dibutuhkan'];
				// $ArrAddRFQ[$val]['created_by'] 		= $data_session['ORI_User']['username'];
				// $ArrAddRFQ[$val]['created_date'] 	= date('Y-m-d H:i:s');
			}
		}
		
		$ArrAddPRHeader = [
			'no_pr' => $no_pr,
			'total_material' => $SUM_MAT,
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		];
		
		// $ArrAddRFQHeader = [
			// 'no_rfq' => $no_rfq,
			// 'hub_rfq' => $no_rfq."-001",
			// 'id_supplier' => $header_po[0]->id_supplier,
			// 'nm_supplier' => $header_po[0]->nm_supplier,
			// 'total_request' => $SUM_MAT,
			// 'created_by' => $data_session['ORI_User']['username'],
			// 'created_date' => date('Y-m-d H:i:s'),
			// 'updated_by' => $data_session['ORI_User']['username'],
			// 'updated_date' => date('Y-m-d H:i:s')
		// ];
		
		$ArrEditHeader = [
			'status' => 'DELETED',
			'deleted' => 'Y',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		];
		
		// print_r($ArrAddPRHeader);
		// print_r($ArrAddPR);
		
		// print_r($ArrAddRFQHeader);
		// print_r($ArrAddRFQ);
		
		// print_r($ArrEditHeader);
		// print_r($ArrEdit);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_po',$no_po);
			$this->db->update('tran_material_po_header', $ArrEditHeader);
			
			if(!empty($ArrEdit)){
				$this->db->update_batch('tran_material_po_detail', $ArrEdit, 'id');
			}
			
			if(!empty($ArrAddPR)){
				$this->db->insert_batch('tran_material_pr_detail', $ArrAddPR);
				$this->db->insert('tran_material_pr_header', $ArrAddPRHeader);
			}
			
			// if(!empty($ArrAddRFQ)){
				// $this->db->insert_batch('tran_material_rfq_detail', $ArrAddRFQ);
				// $this->db->insert('tran_material_rfq_header', $ArrAddRFQHeader);
			// }
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
			history('Delete semua PO : '.$no_po.' / '.$no_pr);
		}
		echo json_encode($Arr_Data);
	}
	
	public function modal_edit_purchase_order(){
		if($this->input->post()){
			$data_session	= $this->session->userdata;
			$data	= $this->input->post();

			$no_po 		= $data['no_po'];
			$detail 	= $data['detail'];
			if(!empty($data['detail_po'])){
				$detail_po 	= $data['detail_po'];
			}

			$REQ_DATE = (!empty($data['request_date']))?date('Y-m-d',strtotime($data['request_date'])):NULL;
			
			$ArrHeader = array(
				'incoterms' 	=> strtolower($data['incoterms']),
				'tgl_dibutuhkan' 	=> $REQ_DATE,
				'tax' 			=> str_replace(',','',$data['tax']),
				'total_po' 		=> str_replace(',','',$data['total_po']),
				'discount' 		=> str_replace(',','',$data['discount']),
				'net_price' 	=> str_replace(',','',$data['net_price']),
				'net_plus_tax' 	=> str_replace(',','',$data['net_plus_tax']),
				'delivery_cost' => str_replace(',','',$data['delivery_cost']),
				'total_price' 	=> str_replace(',','',$data['grand_total']),
				'id_supplier' 	=> $data['id_supplier'],
				'nm_supplier' 	=> get_name('supplier','nm_supplier','id_supplier',$data['id_supplier']),
				'top' 			=> strtolower($data['top']),
				'remarks' 		=> strtolower($data['remarks']),
				'buyer' 		=> strtolower($data['buyer']),
				'mata_uang' 	=> $data['current'],
				'amount_words' 	=> $data['amount_words'],
				'updated_by' 	=> $data_session['ORI_User']['username'],
				'updated_date' 	=> date('Y-m-d H:i:s')
			);

			$ArrEdit = array();
			foreach($detail AS $val => $valx){
				$QTY = str_replace(',','',$valx['qty']);
				$PRICE = str_replace(',','',$valx['price']);

				$ArrEdit[$val]['id'] = $valx['id'];
				$ArrEdit[$val]['nm_material'] = $valx['nm_material'];
				$ArrEdit[$val]['qty_purchase'] = $QTY;
				$ArrEdit[$val]['price_ref_sup'] = $PRICE;
				$ArrEdit[$val]['net_price'] = $PRICE;
				$ArrEdit[$val]['total_price'] = $PRICE * $QTY;
			}
			
			$ArrEditPO = array();
			$no =0;
			if(!empty($data['detail_po'])){
				foreach($detail_po AS $val => $valx){ $no++;
					if(!empty($valx['progress'])){
						$ArrEditPO[$val]['no_po'] 		= $no_po;
						$ArrEditPO[$val]['category'] 	= 'pembelian material';
						$ArrEditPO[$val]['term'] 		= $no;
						$ArrEditPO[$val]['group_top'] 	= $valx['group_top'];
						$ArrEditPO[$val]['progress'] 	= str_replace(',','',$valx['progress']);
						$ArrEditPO[$val]['value_usd'] 	= str_replace(',','',$valx['value_usd']);
						$ArrEditPO[$val]['value_idr'] 	= str_replace(',','',$valx['value_idr']);
						$ArrEditPO[$val]['keterangan'] 	= strtolower($valx['keterangan']);
						$ArrEditPO[$val]['jatuh_tempo'] = $valx['jatuh_tempo'];
						$ArrEditPO[$val]['syarat'] 		= strtolower($valx['syarat']);
						$ArrEditPO[$val]['created_by'] 	= $data_session['ORI_User']['username'];
						$ArrEditPO[$val]['created_date']= date('Y-m-d H:i:s');
					}
				}
			}
			
			$hist_top 		= $this->db->query("SELECT * FROM billing_top WHERE no_po='".$no_po."'")->result_array();
			$ArrEditPOHist 	= array();
			if(!empty($hist_top)){
				foreach($hist_top AS $val => $valx){
					$ArrEditPOHist[$val]['no_po'] 		= $valx['no_po'];
					$ArrEditPOHist[$val]['category'] 	= $valx['category'];
					$ArrEditPOHist[$val]['term'] 		= $valx['term'];
					$ArrEditPOHist[$val]['progress'] 	= $valx['progress'];
					$ArrEditPOHist[$val]['value_usd'] 	= $valx['value_usd'];
					$ArrEditPOHist[$val]['value_idr'] 	= $valx['value_idr'];
					$ArrEditPOHist[$val]['keterangan'] 	= $valx['keterangan'];
					$ArrEditPOHist[$val]['jatuh_tempo'] = $valx['jatuh_tempo'];
					$ArrEditPOHist[$val]['syarat'] 		= $valx['syarat'];
					$ArrEditPOHist[$val]['created_by'] 	= $valx['created_by'];
					$ArrEditPOHist[$val]['created_date']= $valx['created_date'];
					$ArrEditPOHist[$val]['hist_by'] 	= $data_session['ORI_User']['username'];
					$ArrEditPOHist[$val]['hist_date']	= date('Y-m-d H:i:s');
				}
			}
			
			// print_r($ArrHeader);
			// print_r($ArrEdit);
			// print_r($ArrEditPO);
			// print_r($ArrEditPOHist);
			// exit;
			
			$this->db->trans_start();
				$this->db->where('no_po', $data['no_po']);
				$this->db->update('tran_material_po_header', $ArrHeader);

				$this->db->update_batch('tran_material_po_detail', $ArrEdit, 'id');
				
				$this->db->where('no_po', $data['no_po']);
				$this->db->delete('billing_top');
				
				if(!empty($ArrEditPO)){
					$this->db->insert_batch('billing_top', $ArrEditPO);
				}
				
				if(!empty($ArrEditPOHist)){
					$this->db->insert_batch('hist_billing_top', $ArrEditPOHist);
				}
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
				history('Edit PO custom TOP : '.$data['no_po']);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$no_po 		= $this->uri->segment(3);
			$result		= $this->db->get_where('tran_material_po_header', array('no_po'=>$no_po))->result();
			$data_kurs 	= $this->db->limit(1)->get_where('kurs', array('kode_dari'=>'USD'))->result();
			$get_RFQ = get_name('tran_material_rfq_detail','no_rfq','no_po',$no_po);
			$result_RFQ	= $this->db->get_where('tran_material_rfq_header', array('no_rfq'=>$get_RFQ))->result();

			$sql_detail = "SELECT a.*, b.nm_supplier, b.mata_uang AS currency FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='".$no_po."'";
			
			if($result[0]->status != 'DELETED'){
				$sql_detail = "SELECT a.*, b.nm_supplier, b.mata_uang AS currency FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='".$no_po."' AND a.deleted='N'";
			}
			$result_det		= $this->db->query($sql_detail)->result_array();

			$data_top		= $this->db->get_where('billing_top', array('no_po'=>$no_po))->result_array();
			
			$payment = $this->db->get_where('list_help', array('group_by'=>'top'))->result_array();
			$listPPN = $this->db->get_where('list_help',array('group_by'=>'ppn'))->result_array();
			$listSupplier = $this->db->order_by('nm_supplier','asc')->get_where('supplier', array('deleted'=>'0'))->result_array();
			
			
			$data = array(
				'data' 		=> $result,
				'listSupplier' => $listSupplier,
				'listPPN' => $listPPN,
				'data_rfq' 	=> $result_RFQ,
				'data_kurs' => $data_kurs,
				'data_top' => $data_top,
				'payment' => $payment,
				'result' => $result_det
			);
			
			$this->load->view('Purchase_order/modal_edit_purchase_order', $data);
		}
	}
	
	public function modal_hasil_pengajuan(){
		$no_rfq 	= $this->uri->segment(3);

		// $sql 		= "SELECT a.* FROM tran_material_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' ORDER BY a.nm_material, a.id ASC";
		$result		= $this->db
							->select('a.*, b.currency')
							->order_by('a.nm_material, a.id','ASC')
							->join('tran_material_rfq_header b','a.hub_rfq=b.hub_rfq','left')
							->get_where('tran_material_rfq_detail a',array(
								'a.no_rfq' => $no_rfq,
								'a.deleted' => 'N'
							))
							->result_array();
		
		$data = array(
			'result' 	=> $result,
			'no_rfq' 	=> $no_rfq
		);
		
		$this->load->view('Purchase_order/modal_hasil_pengajuan', $data);
	}
	
	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================

	public function get_data_json_po(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/material_purchase";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_po(
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
			
			// $list_supplier		= $this->db->query("SELECT nm_supplier FROM tran_material_rfq_header WHERE no_rfq='".$row['no_rfq']."' AND deleted='N'")->result_array();
			// $arr_sup = array();
			// foreach($list_supplier AS $val => $valx){
			// 	$arr_sup[$val] = $valx['nm_supplier'];
			// }
			// $dt_sup	= implode("<br>", $arr_sup);
			
			// $list_material		= $this->db->query("SELECT id_material, idmaterial, nm_material, qty, category FROM tran_material_rfq_detail WHERE no_rfq='".$row['no_rfq']."' AND deleted='N' GROUP BY id_material ORDER BY id DESC")->result_array();
			// $arr_mat = array();
			// foreach($list_material AS $val => $valx){
			// 	if($valx['category'] == 'mat'){
			// 		$arr_mat[$val] = $valx['nm_material'];
			// 	}
			// 	if($valx['category'] == 'acc'){
			// 		$arr_mat[$val] = get_name_acc($valx['id_material']);
			// 		if(empty($valx['idmaterial'])){
			// 			$arr_mat[$val] = $valx['nm_material'];
			// 		}
			// 	}
				
			// }
			// $dt_mat	= implode("<br>", $arr_mat);
			
			// $arr_qty = array();
			// foreach($list_material AS $val => $valx){
			// 	$arr_qty[$val] = number_format($valx['qty']);
			// }
			// $dt_qty	= implode("<br>", $arr_qty);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_rfq']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_barang_group']."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i', strtotime($row['created_date']))."</div>";
			$alasan_reject 	= (!empty($row['alasan_reject']))?"<br><span class='badge bg-danger'>".$row['alasan_reject']."</span>":"";
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color: ".color_status_purchase($row['sts_ajuan'])['color']."'>".color_status_purchase($row['sts_ajuan'])['status']."</span>".$alasan_reject."</div>";
				$create	= "";
				$edit	= "";
				$edit_rfq	= "";
				$edit_rfq2	= "";
				$booking	= "";
				$spk_ambil_mat	= "";
				$cancel	= "";
				if($row['sts_ajuan']=='OPN' AND $row['sts_process']=='N'){
					$edit_rfq	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit_po' title='Edit RFQ Print' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-edit'></i></button>";
					$edit_rfq2	= "&nbsp;<button type='button' class='btn btn-sm btn-success edit_po2' title='Edit Supplier' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-edit'></i></button>";
					$spk_ambil_mat	= "&nbsp;<a href='".base_url('warehouse/print_rfq/'.$row['no_rfq'])."' target='_blank' class='btn btn-sm btn-info' title='Print SPK Purchase Order' data-role='qtip'><i class='fa fa-print'></i></a>";
					if($Arr_Akses['update']=='1'){
						// masih ada error cek lagi agus						
						// $edit			= "&nbsp;<button type='button' class='btn btn-sm btn-success editMat' title='Edit Material Purchase' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-edit'></i></button>";
					}
					if($Arr_Akses['delete']=='1'){
						// $cancel			= "&nbsp;<button type='button' class='btn btn-sm btn-danger cancelPO' title='Cancel Material Purchase' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-close'></i></button>";
					}
				}
			$nestedData[]	= "<div align='left'>
                                    <button type='button' class='btn btn-sm btn-warning detailMat' title='Total Material Purchase' data-no_rfq='".$row['no_rfq']."' data-status='".$row['sts_ajuan']."'><i class='fa fa-eye'></i></button>
                                    ".$create."
									".$edit."
									".$booking."
									".$spk_ambil_mat."
									".$edit_rfq2."
									".$edit_rfq."
									".$cancel."
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

	public function query_data_json_po($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				GROUP_CONCAT(DISTINCT a.nm_supplier ORDER BY b.id ASC SEPARATOR '<br>') AS nm_supplier,
				GROUP_CONCAT(DISTINCT CONCAT(b.nm_material,', <b>(',b.qty,')</b>') ORDER BY b.id ASC SEPARATOR '<br>') AS nm_barang_group
			FROM
				tran_material_rfq_detail b
				LEFT JOIN tran_material_rfq_header a on a.no_rfq=b.no_rfq,
				(SELECT @row:=0) r
		    WHERE a.deleted_date IS NULL AND  (
				a.no_rfq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.no_rfq
		";
		// echo $sql; exit; 
		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_rfq',
			2 => 'nm_supplier'
		);

		$sql .= " ORDER BY a.updated_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_list_pr(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_list_pr(
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
			
			$nm_material 	= $row['nm_material'];
			$category 		= $row['nm_category'];
			if($row['category'] == 'acc'){
				$nm_material = get_name_acc($row['id_material']);
				$category = strtoupper(get_name('accessories_category', 'category', 'id', $row['idmaterial']));
			}
			if(empty($row['idmaterial'])){
				$nm_material = strtoupper($row['nm_material']);
				$category = strtoupper(get_name('cost_project_detail', 'category', 'id', $row['id_material']).' - '.str_replace('BQ-','',get_name('cost_project_detail', 'id_bq', 'id', $row['id_material'])));
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'><center><input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['id']."'></center></div>";
			$nestedData[]	= "<div align='center'>".$row['no_pr']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='left'>".$nm_material."</div>";
			$nestedData[]	= "<div align='left'>".$category."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['moq'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_revisi'],2)."</div>";
			$TANGGAL_DIBUTUHKAN = (!empty($row['tanggal'])AND $row['tanggal'] != '0000-00-00')?date('d-m-Y', strtotime($row['tanggal'])):'';
			$nestedData[]	= "<div align='center'>".$TANGGAL_DIBUTUHKAN."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
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

	public function query_data_json_list_pr($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.nm_category AS nm_category
			FROM
				tran_material_pr_detail a 
				LEFT JOIN tran_material_pr_header c ON a.no_pr = c.no_pr
				LEFT JOIN raw_materials b ON a.id_material=b.id_material
		    WHERE 1=1
				AND c.sts_ajuan <> 'REJ'
				AND a.no_rfq IS NULL
			AND (
				a.no_pr LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_pr',
			2 => 'nm_material'
		);

		$sql .= " ORDER BY  ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function get_data_json_perbandingan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/perbandingan";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_perbandingan(
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
			
			// $list_supplier		= $this->db->query("SELECT nm_supplier FROM tran_material_rfq_header WHERE no_rfq='".$row['no_rfq']."' AND deleted='N'")->result_array();
			// $arr_sup = array();
			// foreach($list_supplier AS $val => $valx){
			// 	$arr_sup[$val] = $valx['nm_supplier'];
			// }
			// $dt_sup	= implode("<br>", $arr_sup);
			
			// $list_material		= $this->db->query("SELECT nm_material, qty, price_ref, price_ref_sup, category, id_material FROM tran_material_rfq_detail WHERE no_rfq='".$row['no_rfq']."' AND deleted='N' GROUP BY id_material ORDER BY id DESC")->result_array();
			// $arr_mat = array();
			// foreach($list_material AS $val => $valx){
			// 	if($valx['category'] == 'mat'){
			// 		$arr_mat[$val] = $valx['nm_material'];
			// 	}
			// 	if($valx['category'] == 'acc'){
			// 		$arr_mat[$val] = get_name_acc($valx['id_material']);
			// 		if(empty($valx['idmaterial'])){
			// 			$arr_mat[$val] = $valx['nm_material'];
			// 		}
			// 	}
			// }
			// $dt_mat	= implode("<br>", $arr_mat);
			
			// $arr_qty = array();
			// foreach($list_material AS $val => $valx){
			// 	$arr_qty[$val] = number_format($valx['qty']);
			// }
			// $dt_qty	= implode("<br>", $arr_qty);
			
			// $arr_price = array();
			// foreach($list_material AS $val => $valx){
			// 	$arr_price[$val] = number_format($valx['price_ref']);
			// }
			// $dt_price	= implode("<br>", $arr_price);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_rfq']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_barang_group']."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_price."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i', strtotime($row['created_date']))."</div>";
			$alasan_reject 	= (!empty($row['alasan_reject']))?"<br><span class='badge bg-danger'>".$row['alasan_reject']."</span>":"";
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color: ".color_status_purchase($row['sts_ajuan'])['color']."'>".color_status_purchase($row['sts_ajuan'])['status']."</span>".$alasan_reject."</div>";
				$create	= "";
				$edit	= "";
				$booking	= "";
				$spk_ambil_mat	= "";
				$ajukan	= "";
				if($row['sts_ajuan']=='OPN' AND $row['sts_process']=='N'){
					$create	= "&nbsp;<a href='".base_url('purchase/add_perbandingan/'.$row['no_rfq'])."' target='_blank' class='btn btn-sm btn-info' title='Add Perbandingan' data-role='qtip'><i class='fa fa-plus'></i></a>";
				}
				if($row['sts_ajuan']=='PRS' AND $row['sts_process']=='Y'){
					if($Arr_Akses['update']=='1'){
						$edit = "&nbsp;<a href='".base_url('purchase/add_perbandingan/'.$row['no_rfq'])."' class='btn btn-sm btn-success editMat' title='Edit Material Purchase' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-edit'></i></a>";
					}
					if($Arr_Akses['approve']=='1'){
						$ajukan	= "&nbsp;<button type='button' class='btn btn-sm btn-primary ajukan' title='Ajukan Perbandingan' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-check'></i></button>";
					}
				}
			$nestedData[]	= "<div align='left'>
                                    <button type='button' class='btn btn-sm btn-warning detailMat' title='Total Material Purchase' data-no_rfq='".$row['no_rfq']."' data-status='".$row['sts_ajuan']."'><i class='fa fa-eye'></i></button>
                                    ".$create."
									".$edit."
									".$booking."
									".$spk_ambil_mat."
									".$ajukan."
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

	public function query_data_json_perbandingan($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				GROUP_CONCAT(DISTINCT a.nm_supplier ORDER BY b.id ASC SEPARATOR '<br>') AS nm_supplier,
				GROUP_CONCAT(DISTINCT CONCAT(b.nm_material,', <b>(',b.qty,')</b>') ORDER BY b.id ASC SEPARATOR '<br>') AS nm_barang_group
			FROM
				tran_material_rfq_detail b
				LEFT JOIN tran_material_rfq_header a on a.no_rfq=b.no_rfq,
				(SELECT @row:=0) r
		    WHERE  a.deleted = 'N' AND (
				a.no_rfq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.no_rfq
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_rfq',
			2 => 'nm_supplier'
		);

		$sql .= " ORDER BY a.updated_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_pengajuan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/pengajuan";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_pengajuan(
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
			
			$list_supplier		= $this->db->query("SELECT nm_supplier FROM tran_material_rfq_header WHERE no_rfq='".$row['no_rfq']."' AND deleted='N'")->result_array();
			$arr_sup = array();
			foreach($list_supplier AS $val => $valx){
				$arr_sup[$val] = $valx['nm_supplier'];
			}
			$dt_sup	= implode("<br>", $arr_sup);
			
			$list_material		= $this->db->query("SELECT nm_material, qty, price_ref, price_ref_sup, category, id_material FROM tran_material_rfq_detail WHERE no_rfq='".$row['no_rfq']."' AND deleted='N' GROUP BY id_material ORDER BY id DESC")->result_array();
			$arr_mat = array();
			foreach($list_material AS $val => $valx){
				if($valx['category'] == 'mat'){
					$arr_mat[$val] = $valx['nm_material'];
				}
				if($valx['category'] == 'acc'){
					$arr_mat[$val] = get_name_acc($valx['id_material']);
					if(empty($valx['idmaterial'])){
						$arr_mat[$val] = $valx['nm_material'];
					}
				}
			}
			$dt_mat	= implode("<br>", $arr_mat);
			
			$arr_qty = array();
			foreach($list_material AS $val => $valx){
				$arr_qty[$val] = number_format($valx['qty'],2);
			}
			$dt_qty	= implode("<br>", $arr_qty);
			
			$arr_price = array();
			foreach($list_material AS $val => $valx){
				$arr_price[$val] = number_format($valx['price_ref'],2);
			}
			$dt_price	= implode("<br>", $arr_price);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_rfq']."</div>";
			$nestedData[]	= "<div align='left'>".$dt_sup."</div>";
			$nestedData[]	= "<div align='left'>".$dt_mat."</div>";
			$nestedData[]	= "<div align='right'>".$dt_price."</div>";
			$nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d F Y', strtotime($row['created_date']))."</div>";
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color: ".color_status_purchase($row['sts_ajuan'])['color']."'>".color_status_purchase($row['sts_ajuan'])['status']."</span></div>";
				$ajukan	= "";
				$print	= "";
				$hasil_ajukan	= "";
				if($row['sts_ajuan']=='AJU' AND $row['sts_process']=='Y'){

					if($Arr_Akses['approve']=='1'){
						$ajukan	= "&nbsp;<button type='button' class='btn btn-sm btn-info ajukan' title='Ajukan Perbandingan' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-check'></i></button>";
					}
				}
				if(($row['sts_ajuan']=='APV' OR $row['sts_ajuan']=='CLS') AND $row['sts_process']=='Y'){
					$ajukan	= "&nbsp;<button type='button' class='btn btn-sm btn-success hasil_ajukan' title='Hasil Perbandingan' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-eye'></i></button>";
					$print	= "&nbsp;<a href='".base_url('purchase/print_hasil_pemilihan/'.$row['no_rfq'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Hasil Perbandingan'><i class='fa fa-print'></i></a>";
				
				}
			$nestedData[]	= "<div align='left'>
                                    <button type='button' class='btn btn-sm btn-primary detailMat' title='Total Material Purchase' data-no_rfq='".$row['no_rfq']."' data-status='".$row['sts_ajuan']."'><i class='fa fa-eye'></i></button>
                                   ".$ajukan."
								   ".$hasil_ajukan."
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

	public function query_data_json_pengajuan($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_status = " (a.sts_ajuan='AJU' OR a.sts_ajuan='CLS' OR a.sts_ajuan='APV') ";
		if($status != '0'){
			$where_status = " a.sts_ajuan='".$status."' ";
		}

		$sql = "
			SELECT
				a.*
			FROM
				tran_material_rfq_header a
		    WHERE  
				".$where_status." AND a.deleted_date IS NULL
			AND (
				a.no_rfq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.no_rfq
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_rfq',
			2 => 'nm_supplier'
		);

		$sql .= " ORDER BY a.updated_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_approval(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approval";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_approval(
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
			
			$list_supplier		= $this->db->query("SELECT nm_supplier FROM tran_material_rfq_header WHERE no_rfq='".$row['no_rfq']."' AND deleted='N'")->result_array();
			$arr_sup = array();
			foreach($list_supplier AS $val => $valx){
				$arr_sup[$val] = $valx['nm_supplier'];
			}
			$dt_sup	= implode("<br>", $arr_sup);
			
			$list_material		= $this->db->query("SELECT nm_material, qty, price_ref, price_ref_sup, category, id_material FROM tran_material_rfq_detail WHERE no_rfq='".$row['no_rfq']."' AND deleted='N' GROUP BY id_material ORDER BY id DESC")->result_array();
			$arr_mat = array();
			foreach($list_material AS $val => $valx){
				if($valx['category'] == 'mat'){
					$arr_mat[$val] = $valx['nm_material'];
				}
				if($valx['category'] == 'acc'){
					$arr_mat[$val] = get_name_acc($valx['id_material']);
					if(empty($valx['idmaterial'])){
						$arr_mat[$val] = $valx['nm_material'];
					}
				}
			}
			$dt_mat	= implode("<br>", $arr_mat);
			
			$arr_qty = array();
			foreach($list_material AS $val => $valx){
				$arr_qty[$val] = number_format($valx['qty']);
			}
			$dt_qty	= implode("<br>", $arr_qty);
			
			$arr_price = array();
			foreach($list_material AS $val => $valx){
				$arr_price[$val] = number_format($valx['price_ref']);
			}
			$dt_price	= implode("<br>", $arr_price);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_rfq']."</div>";
			$nestedData[]	= "<div align='left'>".$dt_sup."</div>";
			$nestedData[]	= "<div align='left'>".$dt_mat."</div>";
			$nestedData[]	= "<div align='right'>".$dt_price."</div>";
			$nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d F Y', strtotime($row['created_date']))."</div>";
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color: ".color_status_purchase($row['sts_ajuan'])['color']."'>".color_status_purchase($row['sts_ajuan'])['status']."</span></div>";
				$ajukan	= "";
				$hasil	= "";
				if($row['sts_ajuan']=='CLS'){
					$hasil	= "&nbsp;<button type='button' class='btn btn-sm btn-success hasil_ajukan' title='Hasil Perbandingan' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-eye'></i></button>";
				}
				if($row['sts_ajuan']=='APV' AND $row['sts_process']=='Y'){
					if($Arr_Akses['approve']=='1'){
						$ajukan	= "&nbsp;<button type='button' class='btn btn-sm btn-info approved' title='Approve' data-no_rfq='".$row['no_rfq']."'><i class='fa fa-check'></i></button>";
					}
				}
			$nestedData[]	= "<div align='left'>
                                    <button type='button' class='btn btn-sm btn-primary detailMat' title='Total Material Purchase' data-no_rfq='".$row['no_rfq']."' data-status='".$row['sts_ajuan']."'><i class='fa fa-eye'></i></button>
                                   ".$hasil."
                                   ".$ajukan."
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

	public function query_data_json_approval($status,$like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_status = " (a.sts_ajuan='CLS' OR a.sts_ajuan='APV') ";
		if($status != '0'){
			$where_status = " a.sts_ajuan='".$status."' ";
		}

		$sql = "
			SELECT
				a.*
			FROM
				tran_material_rfq_header a
		    WHERE  
				".$where_status." AND a.deleted_date IS NULL
			AND (
				a.no_rfq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.no_rfq
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_rfq',
			2 => 'nm_supplier'
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_purchase_order(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/purchase_order";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_purchase_order(
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
			
			// $list_supplier		= $this->db->query("SELECT nm_supplier FROM tran_material_po_header WHERE no_po='".$row['no_po']."'")->result_array();
			// $arr_sup = array();
			// foreach($list_supplier AS $val => $valx){
			// 	$arr_sup[$val] = $valx['nm_supplier'];
			// }
			// $dt_sup	= implode("<br>", $arr_sup);
			
			// $list_material		= $this->db->query("SELECT nm_material, qty_purchase, price_ref, price_ref_sup, net_price FROM tran_material_po_detail WHERE no_po='".$row['no_po']."' GROUP BY id_material")->result_array();
			// if($row['status'] != 'DELETED'){
			// 	$list_material	= $this->db->query("SELECT nm_material, qty_purchase, price_ref, price_ref_sup, net_price FROM tran_material_po_detail WHERE no_po='".$row['no_po']."' AND deleted='N' GROUP BY id_material")->result_array();
			// }
			// $arr_mat = array();
			// foreach($list_material AS $val => $valx){
			// 	$arr_mat[$val] = $valx['nm_material'];
			// }
			// $dt_mat	= implode("<br>", $arr_mat);
			
			// $arr_qty = array();
			// foreach($list_material AS $val => $valx){
			// 	$arr_qty[$val] = number_format($valx['qty_purchase']);
			// }
			// $dt_qty	= implode("<br>", $arr_qty);

			$list_inv		= $this->db
									->select('a.invoice_no')
									->get_where('billing_top a',array('a.no_po'=>$row['no_po']))
									->result_array();
			$arr_inv = array();

			foreach($list_inv AS $val1 => $val1x){
					$arr_inv[$val1] = $val1x['invoice_no'];
			}

			$arr_inv = array_unique($arr_inv);
			$dt_inv	= implode("<br>", $arr_inv);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_po']."</div>";
			$nestedData[]	= "<div align='center'>".$dt_inv."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_barang_group']."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price'],2)."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
			$status_po=$row['status_po'];
			if($row['status'] == 'COMPLETE'){
				$warna = 'bg-green';
				$status = $row['status'];
			}
			else if($row['status'] == 'WAITING IN'){
				$warna = 'bg-blue';
				$status = $row['status'];
			}
			else if($row['status'] == 'IN PARSIAL'){
				$warna = 'bg-purple';
				$status = $row['status'];
			}
			else{
				$warna = 'bg-red';
				$status = $row['status'];
			}

			$span_bg = "<span class='badge ".$warna."'>".$status."</span>";

			if(($row['status1'] == 'N' OR $row['status2'] == 'N') AND $row['deleted'] == 'N' AND $row['status'] == 'WAITING IN'){
				if($row['status1'] == 'N'){
					$warna = 'bg-yellow';
					$status = 'Waiting Approval';
				}
				else{
					$warna = 'bg-green';
					$status = 'Approved 1';
				}

				if($row['status2'] == 'N'){
					$warna2 = 'bg-yellow';
					$status2 = 'Waiting Approval 2';
				}
				else{
					$warna2 = 'bg-green';
					$status2 = 'Approved 2';
				}	
				// $span_bg = "<span class='badge ".$warna."'>".$status."</span><br><span class='badge ".$warna2."'>".$status2."</span>";
				$span_bg = "<span class='badge ".$warna."'>".$status."</span>";
			}
			if($status_po=="CLS") $span_bg = "<span class='badge ".$warna."'>CLOSE</span>";
			$nestedData[]	= "<div align='left'>".$span_bg."</div>";
			$edit_print = "";
			$edit_po = "";
			$print_po = "";
			$delete_po = "";
			$close_po = "";
			$request_payment = "";
			$repeat_po = "";		
			$print_old	= "&nbsp;<a href='".base_url('purchase/print_po/'.$row['no_po'])."' target='_blank' class='btn btn-sm btn-info' title='Print PO' data-role='qtip'><i class='fa fa-print'></i></a>";
			$print_tnc="&nbsp;<a href='".base_url('purchase/print_po_tnc/'.$row['no_po'])."' target='_blank' class='btn btn-sm btn-default' title='Print T&C' data-role='qtip'><b>Print T&C</b></a>";

			if($status_po==""){ 
				if($row['status'] == 'WAITING IN' AND $row['status1'] == 'Y' AND $row['status2'] == 'Y'){
					$edit_print	= "&nbsp;<button type='button' class='btn btn-sm btn-warning edit_po' title='Create PO' data-no_po='".$row['no_po']."'><i class='fa fa-pencil'></i></button>";
					$print_po = $print_old."&nbsp;<a href='".base_url('purchase/print_po3/'.$row['no_po'])."' target='_blank' class='btn btn-sm btn-default' title='Print PO' data-role='qtip'><b>Print Nilai PO</b></a>";				
					// $edit_po	= "&nbsp;<button type='button' class='btn btn-sm btn-success edit_po_qty' title='Edit PO' data-no_po='".$row['no_po']."'><i class='fa fa-edit'></i></button>";
					// $delete_po	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete_po' title='Delete PO' data-no_po='".$row['no_po']."'><i class='fa fa-trash'></i></button>";
				}
				if($row['status'] == 'WAITING IN' AND $row['status1'] == 'N' AND $row['status2'] == 'N'){
					$edit_print	= "&nbsp;<button type='button' class='btn btn-sm btn-warning edit_po' title='Create PO' data-no_po='".$row['no_po']."'><i class='fa fa-pencil'></i></button>";
					// $edit_po	= "&nbsp;<button type='button' class='btn btn-sm btn-success edit_po_qty' title='Edit PO' data-no_po='".$row['no_po']."'><i class='fa fa-edit'></i></button>";
					$delete_po	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete_po' title='Delete PO' data-no_po='".$row['no_po']."'><i class='fa fa-trash'></i></button>";
					//$request_payment = "&nbsp;<button type='button' class='btn btn-sm btn-primary request_payment' title='Request Payment' data-no_po='".$row['no_po']."'><i class='fa fa-money'></i></button>";
					//$close_po = "&nbsp;<button type='button' class='btn btn-sm btn-danger close_po' title='Close PO' data-no_po='".$row['no_po']."'><i class='fa fa-check'></i></button>";
					$print_po = $print_old."&nbsp;<a href='".base_url('purchase/print_po3/'.$row['no_po'])."' target='_blank' class='btn btn-sm btn-default' title='Print PO' data-role='qtip'><b>Print Nilai PO</b></a>";
				}
				if($status_po=="" and $row['status1'] == 'Y') {
					$request_payment = "&nbsp;<button type='button' class='btn btn-sm btn-primary request_payment' title='Request Payment' data-no_po='".$row['no_po']."'><i class='fa fa-money'></i></button>";
					$close_po = "&nbsp;<button type='button' class='btn btn-sm btn-danger close_po' title='Close PO' data-no_po='".$row['no_po']."'><i class='fa fa-check'></i></button>";
					$print_po = $print_old."&nbsp;<a href='".base_url('purchase/print_po3/'.$row['no_po'])."' target='_blank' class='btn btn-sm btn-default' title='Print PO' data-role='qtip'><b>Print Nilai PO</b></a>";
				}
			}

			if(!empty($row['valid_date']) AND $row['valid_date'] >= date('Y-m-d')){
				$repeat_po	= "&nbsp;<button type='button' class='btn btn-sm bg-purple repeat_po' title='Repeat PO' data-no_po='".$row['no_po']."'><i class='fa fa-retweet'></i></button>";
			}
			$nestedData[]	= "	<div align='left'>
                                    <button type='button' class='btn btn-sm btn-default detailMat' title='Detail PO' data-no_po='".$row['no_po']."'><i class='fa fa-eye'></i></button>
									".$edit_po."
									".$edit_print."
									".$print_po."
									".$delete_po."
									".$request_payment."
									".$close_po."
									".$repeat_po."
									".$print_tnc."
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

	public function query_data_json_purchase_order($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					GROUP_CONCAT(DISTINCT a.nm_supplier ORDER BY b.id ASC SEPARATOR '<br>') AS nm_supplier,
					GROUP_CONCAT(CONCAT(b.nm_material,', <b>(',b.qty_purchase,')</b>') ORDER BY b.id ASC SEPARATOR '<br>') AS nm_barang_group
				FROM
					tran_material_po_detail b
					LEFT JOIN tran_material_po_header a ON a.no_po = b.no_po,
					(SELECT @row:=0) r
				WHERE 1=1 AND a.deleted = 'N' AND a.repeat_po IS NULL AND a.status_id = '1'
				AND (
					a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY b.no_po
			";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_po',
			2 => 'nm_supplier'
		);

		$sql .= " ORDER BY a.updated_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql); 
		return $data;
	}
	
	//NEW
	public function modal_po(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$Username 		= $this->session->userdata['ORI_User']['username'];
			$dateTime		= date('Y-m-d H:i:s');

			$Ym = date('ym');
			//pengurutan kode
			$srcMtr			= "SELECT MAX(no_po) as maxP FROM tran_material_po_header WHERE no_po LIKE 'PO".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 6, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$no_po			= "PO".$Ym.$urut2;
			// echo $no_po; exit;

			$check			= $data['check'];
			$tgl_dibutuhkan	= date('Y-m-d',strtotime($data['tanggal_dibutuhkan']));
			$valid_date		= (!empty($data['valid_date']))?date('Y-m-d',strtotime($data['valid_date'])):NULL;
			$total_po		= str_replace(',','',$data['total_po']);
			$discount		= str_replace(',','',$data['discount']);
			$net_price		= str_replace(',','',$data['net_price']);
			$tax			= str_replace(',','',$data['tax']);
			$net_plus_tax	= str_replace(',','',$data['net_plus_tax']);
			$delivery_cost	= str_replace(',','',$data['delivery_cost']);
			$grand_total	= str_replace(',','',$data['grand_total']);

			$ArrList 		= array();
			foreach($check AS $vaxl){
				$ArrList[$vaxl] = $vaxl;
			}
			$dtImplode		= "('".implode("','", $ArrList)."')";
			
			$qListPRD 		= "SELECT * FROM tran_material_rfq_detail WHERE id IN ".$dtImplode."  ";
			$detail 	= $this->db->query($qListPRD)->result_array();
			
			$ArrHeader = array();
			$ArrDetail = array();
			$ArrUpdate = array();
			
			$SUM_MAT = 0;
			$nilai_ppn=0;
			$nilai_total=0;
			$nilai_plus_ppn=0;
			$no_rfq='';
			foreach($detail AS $val2 => $valx22){
				$no_rfq=$valx22['no_rfq'];
				$qty_po = str_replace(',','',$data['purchase_'.$valx22['id']]);
				$net_pricedtl = str_replace(',','',$data['harga_idr_'.$valx22['id']]);
				$total_pricedtl = str_replace(',','',$data['total_harga_'.$valx22['id']]);
				
				$nm_material = $valx22['nm_material'];
				$idmaterial = $valx22['idmaterial'];
				if($valx22['category'] == 'acc'){
					$nm_material = get_name_acc($valx22['id_material']);
					$idmaterial = $valx22['idmaterial'];
				}
				$SUM_MAT += $qty_po;

				$ArrDetail[$val2]['no_po'] 			= $no_po;
				$ArrDetail[$val2]['id_material'] 	= $valx22['id_material'];
				$ArrDetail[$val2]['category'] 		= $valx22['category'];
				$ArrDetail[$val2]['idmaterial'] 	= $idmaterial;
				$ArrDetail[$val2]['nm_material'] 	= $nm_material;
				$ArrDetail[$val2]['qty_purchase'] 	= $qty_po;
				$ArrDetail[$val2]['net_price'] 		= $net_pricedtl;
				$ArrDetail[$val2]['total_price'] 	= $total_pricedtl;
				$ArrDetail[$val2]['price_ref'] 		= $valx22['price_ref'];
				$ArrDetail[$val2]['price_ref_sup'] 	= $valx22['price_ref_sup'];
				$ArrDetail[$val2]['moq'] 			= $valx22['moq'];
				$ArrDetail[$val2]['tgl_dibutuhkan'] = $valx22['tgl_dibutuhkan'];
				$ArrDetail[$val2]['lead_time'] 		= $valx22['lead_time'];
				$ArrDetail[$val2]['created_by'] 	= $Username;
				$ArrDetail[$val2]['created_date'] 	= $dateTime;
				
				$ArrUpdate[$val2]['id'] 			= $valx22['id'];
				$ArrUpdate[$val2]['no_po'] 			= $no_po;
				$ArrUpdate[$val2]['qty_po'] 		= $valx22['qty_po'] + $qty_po;
				$nilai_total=($nilai_total+($valx22['price_ref_sup']*$qty_po));
			}
			$nilai_ppn=($nilai_total*$tax/100);
			$nilai_plus_ppn=($nilai_ppn+$nilai_total);
			// get rfq_header
			$sqlrfqh 		= "SELECT * FROM tran_material_rfq_header WHERE no_rfq ='".$no_rfq."' and id_supplier='".$valx22['id_supplier']."'";
			$rfqheader 	= $this->db->query($sqlrfqh)->row();

			$ArrHeader['no_po'] 			= $no_po;
			$ArrHeader['id_supplier'] 		= $valx22['id_supplier'];
			$ArrHeader['nm_supplier'] 		= get_name('supplier', 'nm_supplier', 'id_supplier', $valx22['id_supplier']);
			$ArrHeader['total_material'] 	= $SUM_MAT;
			$ArrHeader['total_price'] 		= $grand_total;
			$ArrHeader['tax'] 				= $tax;
			$ArrHeader['total_po'] 			= $total_po;
			$ArrHeader['discount'] 			= $discount;
			$ArrHeader['net_price'] 		= $net_price;
			$ArrHeader['net_plus_tax'] 		= $net_plus_tax;
			$ArrHeader['delivery_cost'] 	= $delivery_cost;
			$ArrHeader['tgl_dibutuhkan'] 	= $tgl_dibutuhkan;
			$ArrHeader['valid_date'] 		= $valid_date;
			$ArrHeader['npwp'] 		= '01.081.598.3-431.000';
			$ArrHeader['phone'] 	= '021-8972193';

			$ArrHeader['incoterms'] 		= $rfqheader->incoterms;
			$ArrHeader['top'] 				= $rfqheader->top;
			$ArrHeader['remarks'] 			= $rfqheader->remarks;
			$ArrHeader['mata_uang'] 		= $rfqheader->currency;

			$ArrHeader['created_by'] 		= $Username;
			$ArrHeader['created_date'] 		= $dateTime;
			$ArrHeader['updated_by'] 		= $Username;
			$ArrHeader['updated_date'] 		= $dateTime;

			$ArrHeader['nilai_total']		= $nilai_total;
			$ArrHeader['nilai_ppn']			= $nilai_ppn;
			$ArrHeader['nilai_plus_ppn']	= $nilai_plus_ppn;
			
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// print_r($ArrUpdate);
			// exit;
			
			$this->db->trans_start();
				$this->db->insert('tran_material_po_header', $ArrHeader);
				$this->db->insert_batch('tran_material_po_detail', $ArrDetail);
				
				$this->db->update_batch('tran_material_rfq_detail', $ArrUpdate, 'id');
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('Create PO '.$no_po.'/'.$valx22['hub_rfq'].'/'.$valx22['id']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$query 		= "	SELECT 
								a.id_supplier, 
								b.nm_supplier 
							FROM 
								tran_material_rfq_detail a
								LEFT JOIN supplier b ON a.id_supplier = b.id_supplier
							WHERE 
								a.status_apv = 'SETUJU' 
								AND a.qty_po < a.qty 
							GROUP BY 
								a.id_supplier 
							ORDER BY
								b.nm_supplier ASC ";
			$restQuery 	= $this->db->query($query)->result_array();

			$listPPN = $this->db->get_where('list_help',array('group_by'=>'ppn'))->result_array();

			$data = array(
				'supList' => $restQuery,
				'listPPN' => $listPPN,
			);
			$this->load->view('Purchase_order/modal_po', $data);
		}
	}

	public function get_data_json_list_rfq(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_list_rfq(
			$requestData['id_supplier'],
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
			
			$nm_material = $row['nm_material'];
			if($row['category'] == 'acc'){
				$nm_material = get_name_acc($row['id_material']);
				if(empty($row['idmaterial'])){
					$nm_material = $row['nm_material'];
				}
			}

			$PRICE_REF = $row['price_ref_sup'];
			$QTY_PO = $row['qty'] - $row['qty_po'];
			$PRICE_TOTAL = $QTY_PO * $row['price_ref_sup'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[".$row['id']."]' class='chk_personal check_pr' data-nomor='".$row['id']."' value='".$row['id']."'></div>";
			$nestedData[]	= "<div align='center'>".$row['no_rfq']."
									<input type='hidden' name='harga_idr_".$row['id']."' value='".$PRICE_REF."' class='harga_idr_val'>
									<input type='hidden' name='total_harga_".$row['id']."' value='".$PRICE_TOTAL."' class='total_harga_val'></div>";
			$nestedData[]	= "<div align='left'>".$nm_material."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['moq'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_po'],2)."</div>";
			$tgl_dibutuhkan = (!empty($row['tgl_dibutuhkan']) AND $row['tgl_dibutuhkan'] != '0000-00-00')?date('d/m/Y', strtotime($row['tgl_dibutuhkan'])):'-';
			$nestedData[]	= "<div align='center'>".$tgl_dibutuhkan."</div>";
			$nestedData[]	= "<div align='center'>".strtolower(get_name('users','nm_lengkap','username',$row['created_by']))."</div>";
			$nestedData[]	= "<div align='center'>".date('d/m/Y', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='right' class='harga_idr'>".number_format($PRICE_REF,2)."</div>";
			$nestedData[]	= "<div align='left' class='text-primary'><b>".$row['currency']."</b></div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='purchase_".$row['id']."' id='purchase_".$row['id']."' value='".$QTY_PO."' class='form-control input-md text-right maskM qty_po' style='width:100%;'>
								</div><script type='text/javascript'>$('.maskM').autoNumeric('init', {mDec: '2', aPad: false});</script>";
			$nestedData[]	= "<div align='right' class='total_harga'>".number_format($PRICE_TOTAL,2)."</div>";

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

	public function query_data_json_list_rfq($id_supplier, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		if($id_supplier <> '0'){
			$where = " AND a.id_supplier = '".$id_supplier."'";
		}
		$sql = "
			SELECT
				a.*,
				b.nm_category AS nm_category,
				c.currency
			FROM
				tran_material_rfq_detail a 
				LEFT JOIN raw_materials b ON a.id_material=b.id_material
				LEFT JOIN tran_material_rfq_header c ON a.hub_rfq=c.hub_rfq
		    WHERE 1=1 ".$where."
				AND a.status_apv = 'SETUJU'
				AND a.qty_po < a.qty
			AND (
				a.no_rfq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_rfq',
			2 => 'close_date',
			3 => 'nm_material'
		);

		$sql .= " ORDER BY  ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//approve
	public function get_data_json_purchase_order_approve(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approval_po";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_purchase_order_apporve(
			$requestData['id'],
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

		$ID = $requestData['id'];
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
			
			$list_supplier		= $this->db->query("SELECT nm_supplier FROM tran_material_po_header WHERE no_po='".$row['no_po']."'")->result_array();
			$arr_sup = array();
			foreach($list_supplier AS $val => $valx){
				$arr_sup[$val] = $valx['nm_supplier'];
			}
			$dt_sup	= implode("<br>", $arr_sup);
			
			$list_material		= $this->db->query("SELECT nm_material, qty_purchase, price_ref, price_ref_sup, net_price, total_price FROM tran_material_po_detail WHERE no_po='".$row['no_po']."' GROUP BY id_material")->result_array();
			if($row['status'] != 'DELETED'){
				$list_material	= $this->db->query("SELECT nm_material, qty_purchase, price_ref, price_ref_sup, net_price, total_price FROM tran_material_po_detail WHERE no_po='".$row['no_po']."' AND deleted='N' GROUP BY id_material")->result_array();
			}
			$arr_mat = array();
			foreach($list_material AS $val => $valx){
				$arr_mat[$val] = $valx['nm_material'];
			}
			$dt_mat	= implode("<br>", $arr_mat);
			
			$arr_qty = array();
			foreach($list_material AS $val => $valx){
				$arr_qty[$val] = number_format($valx['qty_purchase']);
			}
			$dt_qty	= implode("<br>", $arr_qty);
			
			$arr_pur = array();
			foreach($list_material AS $val => $valx){
				$arr_pur[$val] = number_format($valx['net_price'],2)." <b class='text-primary'>".strtoupper($row['mata_uang'])."</b>";
			}
			$dt_pur	= implode("<br>", $arr_pur);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_po']."</div>";
			$nestedData[]	= "<div align='left'>".$dt_sup."</div>";
			$nestedData[]	= "<div align='left'>".$dt_mat."</div>";
			$nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='right'>".$dt_pur."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price'],2)."</div>";
			
			$app1 = "<button type='button' class='btn btn-sm btn-success approved' title='Approval' data-id='".$ID."' data-no_po='".$row['no_po']."'><i class='fa fa-check'></i></button>";
			
			$nestedData[]	= "	<div align='center'>
                                    ".$app1."
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

	public function query_data_json_purchase_order_apporve($id, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$WHERE = "";
		if($id == '2'){
			$WHERE = "AND (a.total_price * 14000) > 50000000";
		}

		$sql = "SELECT
					a.*
				FROM
					tran_material_po_header a
				WHERE 1=1
					AND status$id = 'N'
					".$WHERE."
					AND a.deleted = 'N'
					AND (
						a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_po',
			2 => 'nm_supplier'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	function po_top(){
		$no_po 		= $this->uri->segment(3);
		$result		= $this->db->get_where('tran_material_po_header', array('no_po'=>$no_po))->result();
		$data_kurs 	= $this->db->limit(1)->get_where('kurs', array('kode_dari'=>'USD'))->result();
		$get_RFQ = get_name('tran_material_rfq_detail','no_rfq','no_po',$no_po);
		$result_RFQ	= $this->db->get_where('tran_material_rfq_header', array('no_rfq'=>$get_RFQ))->result();

		$sql_detail = "SELECT a.*, b.nm_supplier FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='".$no_po."'";

		if($result[0]->status != 'DELETED'){
			$sql_detail = "SELECT a.*, b.nm_supplier FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='".$no_po."' AND a.deleted='N'";
		}
		$result_det		= $this->db->query($sql_detail)->result_array();

		$data_top		= $this->db->get_where('billing_top', array('no_po'=>$no_po))->result_array();

		$payment = $this->db->get_where('list_help', array('group_by'=>'top'))->result_array();

		$data = array(
			'data' 		=> $result,
			'data_rfq' 	=> $result_RFQ,
			'data_kurs' => $data_kurs,
			'data_top' => $data_top,
			'payment' => $payment,
			'result' => $result_det
		);

		$this->load->view('Purchase_order/form_top', $data);
	}

	function save_po_top(){
			$ArrEditPO = array();
			$data = $this->input->post();
			$no_po=$data['no_po'];
			$detail_po=$data['detail_po'];
			$data_session	= $this->session->userdata;
			$no =0;
			if(!empty($data['detail_po'])){
				foreach($detail_po AS $val => $valx){ $no++;
					if(!empty($valx['progress'])){
						$ArrEditPO[$val]['no_po'] 		= $no_po;
						$ArrEditPO[$val]['category'] 	= 'pembelian material';
						$ArrEditPO[$val]['term'] 		= $no;
						$ArrEditPO[$val]['group_top'] 	= $valx['group_top'];
						$ArrEditPO[$val]['progress'] 	= str_replace(',','',$valx['progress']);
						$ArrEditPO[$val]['value_usd'] 	= str_replace(',','',$valx['value_usd']);
						$ArrEditPO[$val]['value_idr'] 	= str_replace(',','',$valx['value_idr']);
						$ArrEditPO[$val]['keterangan'] 	= strtolower($valx['keterangan']);
						$ArrEditPO[$val]['jatuh_tempo'] = $valx['jatuh_tempo'];
						$ArrEditPO[$val]['syarat'] 		= strtolower($valx['syarat']);
						$ArrEditPO[$val]['created_by'] 	= $data_session['ORI_User']['username'];
						$ArrEditPO[$val]['created_date']= date('Y-m-d H:i:s');
						$ArrEditPO[$val]['kurs_receive_invoice'] = 1; 
					}
				}
			}

			$hist_top 		= $this->db->query("SELECT * FROM billing_top WHERE no_po='".$no_po."'")->result_array();
			$ArrEditPOHist 	= array();
			if(!empty($hist_top)){
				foreach($hist_top AS $val => $valx){
					$ArrEditPOHist[$val]['no_po'] 		= $valx['no_po'];
					$ArrEditPOHist[$val]['category'] 	= $valx['category'];
					$ArrEditPOHist[$val]['term'] 		= $valx['term'];
					$ArrEditPOHist[$val]['progress'] 	= $valx['progress'];
					$ArrEditPOHist[$val]['value_usd'] 	= $valx['value_usd'];
					$ArrEditPOHist[$val]['value_idr'] 	= $valx['value_idr'];
					$ArrEditPOHist[$val]['keterangan'] 	= $valx['keterangan'];
					$ArrEditPOHist[$val]['jatuh_tempo'] = $valx['jatuh_tempo'];
					$ArrEditPOHist[$val]['syarat'] 		= $valx['syarat'];
					$ArrEditPOHist[$val]['created_by'] 	= $valx['created_by'];
					$ArrEditPOHist[$val]['created_date']= $valx['created_date'];
					$ArrEditPOHist[$val]['hist_by'] 	= $data_session['ORI_User']['username'];
					$ArrEditPOHist[$val]['hist_date']	= date('Y-m-d H:i:s');
				}
			}
			$this->db->trans_start();
				$this->db->where('no_po', $data['no_po']);
				$this->db->where('proses_inv', '0');
				$this->db->delete('billing_top');

				if(!empty($ArrEditPO)){
					$this->db->insert_batch('billing_top', $ArrEditPO);
				}

				if(!empty($ArrEditPOHist)){
					$this->db->insert_batch('hist_billing_top', $ArrEditPOHist);
				}
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
				history('Edit PO custom TOP : '.$data['no_po']);
			}
			echo json_encode($Arr_Data);
	}
// SYAMSUDIN 23-12-2024
	public function index_purchase_order_ap(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Pembelian Material >> List AP ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View AP');
		$this->load->view('Purchase_order/purchase_order_ap',$data);
	}

	public function get_data_json_purchase_order_ap(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/purchase_order";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_purchase_order_ap(
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
            
			if($row['mata_uang']=='IDR'){
			   $total  =	$row['total_price'];
			   $dp     =	$row['nilai_dp'];
			   $unbill =	$row['total_terima_barang_idr'];
			   $hutang =	$row['sisa_hutang_idr'];
			   $bayar  =	$row['bayar_idr'];
			}
			else{
				$total  =	$row['total_price'];
				$dp     =	$row['nilai_dp'];
				$unbill =	$row['nilai_terima_barang_kurs'];
				$hutang =	$row['sisa_hutang_kurs'];
				$bayar  =	$row['bayar_kurs'];
			 }
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_po']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='right'>".$row['mata_uang']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($total,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($dp,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($unbill,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($hutang,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($bayar,2)."</div>";
			
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

	public function query_data_json_purchase_order_ap($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					GROUP_CONCAT(DISTINCT a.nm_supplier ORDER BY b.id ASC SEPARATOR '<br>') AS nm_supplier,
					GROUP_CONCAT(CONCAT(b.nm_material,', <b>(',b.qty_purchase,')</b>') ORDER BY b.id ASC SEPARATOR '<br>') AS nm_barang_group
				FROM
					tran_material_po_detail b
					LEFT JOIN tran_material_po_header a ON a.no_po = b.no_po,
					(SELECT @row:=0) r
				WHERE 1=1 AND a.deleted = 'N' AND a.repeat_po IS NULL AND a.status_id = '1'
				AND (
					a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY b.no_po
			";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_po',
			2 => 'nm_supplier'
		);

		$sql .= " ORDER BY a.updated_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
}