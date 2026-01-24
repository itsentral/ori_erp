<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$data_ppn = array();
class Purchase extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('purchase_order_model');
		$this->load->model('All_model');
		$this->load->model('Jurnal_model');		
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
		$this->data_ppn = $this->All_model->Comboppn();
    }

	//==========================================================================================================================
	//===================================================MATERIAL PLANNING======================================================
	//==========================================================================================================================

	public function perbandingan(){
		$this->purchase_order_model->index_perbandingan();
	}
	
	public function server_side_perbandingan(){
		$this->purchase_order_model->get_data_json_perbandingan();
	}
	
	public function add_perbandingan(){
		$this->purchase_order_model->add_perbandingan();
	}

	public function modal_add_currency(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$Username 		= $this->session->userdata['ORI_User']['username'];
			$dateTime		= date('Y-m-d H:i:s');

			$currency		= (!empty($data['currency']))?$data['currency']:[];
			$ArrUpdate 		= array(
				'flag' => '1'
			);

			// $ArrInsert = [];
			// if(!empty($currency)){
			// 	foreach ($currency as $key => $value) {
			// 		$ArrInsert[$key] => 
			// 	}
			// }
			
			$this->db->trans_start();
				if(!empty($currency)){
					$this->db->where_in('id', $currency);
					$this->db->update('currency', $ArrUpdate);
				}
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
				if(!empty($currency)){
				history('Menambahkan currency di add perbandingan');
				}
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$currency = $this->db->get_where('currency',array('flag'=>'0'))->result_array();
			$data = array(
				'currency' => $currency
			);
			$this->load->view('Purchase_order/modal_add_currency', $data);
		}
	}
	
	public function modal_detail_perbandingan(){
		$this->purchase_order_model->modal_detail_perbandingan();
	}
	
	public function pengajuan_rfq(){
		$this->purchase_order_model->pengajuan_rfq();
	}
	
	
	public function pengajuan(){
		$this->purchase_order_model->index_pengajuan();
	}
	
	public function server_side_pengajuan(){
		$this->purchase_order_model->get_data_json_pengajuan();
	}
	
	public function modal_detail_pengajuan(){
		$this->purchase_order_model->modal_detail_pengajuan();
	}
	
	public function modal_pemilihan(){
		$this->purchase_order_model->modal_pemilihan();
	}

	public function modal_pemilihan_reject(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$no_rfq 		= $data['no_rfq'];
		$alasan_reject 		= $data['alasan_reject'];
		// print_r($data);
		
		$ArrHeader = array(
			'sts_ajuan' => 'OPN',
			'sts_process' => 'N',
			'alasan_reject' => $alasan_reject
		);

		$ArrDetail = array(
			'status' => 'BELUM SETUJU',
			'status_apv' => 'BELUM SETUJU',
			'setuju_by' => NULL,
			'setuju_date' => NULL,
			'close_by' => NULL,
			'close_date' => NULL
		);
		
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
			$this->db->where(array('no_rfq'=>$no_rfq));
			$this->db->update('tran_material_rfq_header', $ArrHeader);

			$this->db->where(array('no_rfq'=>$no_rfq));
			$this->db->update('tran_material_rfq_detail', $ArrDetail);
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
			history('Reject Pemilihan Supplier '.$no_rfq);
		}
		echo json_encode($Arr_Kembali);
		
	}
	
	public function modal_hasil_pengajuan(){
		$this->purchase_order_model->modal_hasil_pengajuan();
	}
	
	public function print_hasil_pemilihan(){
		$this->purchase_order_model->print_hasil_pemilihan();
	}
	
	
	public function approval(){
		$this->purchase_order_model->index_approval();
	}
	
	public function server_side_approval(){
		$this->purchase_order_model->get_data_json_approval();
	}
	
	public function modal_detail_approve(){
		$this->purchase_order_model->modal_detail_approve(); 
	}
	
	public function modal_approve(){
		$this->purchase_order_model->modal_approve();
	}

	public function modal_approve_reject(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$no_rfq 		= $data['no_rfq'];
		$alasan_reject 		= $data['alasan_reject'];
		// print_r($data);
		
		$ArrHeader = array(
			'sts_ajuan' => 'OPN',
			'sts_process' => 'N',
			'alasan_reject' => $alasan_reject
		);

		$ArrDetail = array(
			'status' => 'BELUM SETUJU',
			'status_apv' => 'BELUM SETUJU',
			'setuju_by' => NULL,
			'setuju_date' => NULL,
			'close_by' => NULL,
			'close_date' => NULL
		);
		
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
			$this->db->where(array('no_rfq'=>$no_rfq));
			$this->db->update('tran_material_rfq_header', $ArrHeader);

			$this->db->where(array('no_rfq'=>$no_rfq));
			$this->db->update('tran_material_rfq_detail', $ArrDetail);
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
			history('Reject Approval Supplier '.$no_rfq);
		}
		echo json_encode($Arr_Kembali);
		
	}
	
	
	public function purchase_order(){
		$this->purchase_order_model->index_purchase_order();
	}
	
	public function server_side_purchase_order(){
		$this->purchase_order_model->get_data_json_purchase_order();
	}
	
	public function modal_detail_purchase_order(){
		$this->purchase_order_model->modal_detail_purchase_order();
	}
	
	public function modal_edit_purchase_order(){
		$this->purchase_order_model->modal_edit_purchase_order();
	}
	
	public function print_poxxx(){
		$this->purchase_order_model->print_po();
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
		$this->load->view('Print/print_po_dotmatrik', $data); 
	}

	public function print_po3(){
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
		$this->load->view('Print/print_po_dotmatrik_revisi1', $data); 
	}

	public function print_po_tnc(){
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
		$this->load->view('Print/print_po_terms_condition', $data); 
	}
	
	public function modal_po(){
		$this->purchase_order_model->modal_po();
	}

	public function server_side_list_rfq(){
		$this->purchase_order_model->get_data_json_list_rfq();
	}
	
	public function edit_po_qty(){
		$this->purchase_order_model->edit_po_qty();
	}
	
	public function delete_sebagian_po(){
		$this->purchase_order_model->delete_sebagian_po();
	}
	
	public function delete_semua_po(){
		$this->purchase_order_model->delete_semua_po();
	}

	public function repeat_po_process(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$Username 		= $this->session->userdata['ORI_User']['username'];
		$dateTime		= date('Y-m-d H:i:s');

		$po_repeat 		= $data['no_po'];

		$Ym = date('ym');
		//pengurutan kode
		$srcMtr			= "SELECT MAX(no_po) as maxP FROM tran_material_po_header WHERE no_po LIKE 'PO".$Ym."%' ";
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$no_po			= "PO".$Ym.$urut2;

		$getHeader = $this->db->get_where('tran_material_po_header',array('no_po'=>$po_repeat))->result();
		$getDetail = $this->db->get_where('tran_material_po_detail',array('no_po'=>$po_repeat))->result_array();

		$ArrHeader['no_po'] 			= $no_po;
		$ArrHeader['id_supplier'] 		= $getHeader[0]->id_supplier;
		$ArrHeader['nm_supplier'] 		= $getHeader[0]->nm_supplier;
		$ArrHeader['total_material'] 	= $getHeader[0]->total_material;
		$ArrHeader['total_price'] 		= $getHeader[0]->total_price;
		$ArrHeader['tax'] 				= $getHeader[0]->tax;
		$ArrHeader['total_po'] 			= $getHeader[0]->total_po;
		$ArrHeader['discount'] 			= $getHeader[0]->discount;
		$ArrHeader['net_price'] 		= $getHeader[0]->net_price;
		$ArrHeader['net_plus_tax'] 		= $getHeader[0]->net_plus_tax;
		$ArrHeader['delivery_cost'] 	= $getHeader[0]->delivery_cost;
		$ArrHeader['tgl_dibutuhkan'] 	= $getHeader[0]->tgl_dibutuhkan;
		$ArrHeader['repeat_po'] 		= $po_repeat;
		$ArrHeader['npwp'] 				= '01.081.598.3-431.000';
		$ArrHeader['phone'] 			= '021-8972193';
		$ArrHeader['created_by'] 		= $Username;
		$ArrHeader['created_date'] 		= $dateTime;
		$ArrHeader['updated_by'] 		= $Username;
		$ArrHeader['updated_date'] 		= $dateTime;

		$ArrDetail = [];
		foreach ($getDetail as $key => $value) {
			$ArrDetail[$key]['no_po'] 			= $no_po;
			$ArrDetail[$key]['id_header'] 		= $value['id'];
			$ArrDetail[$key]['category'] 		= $value['category'];
			$ArrDetail[$key]['id_material'] 	= $value['id_material'];
			$ArrDetail[$key]['idmaterial'] 		= $value['idmaterial'];
			$ArrDetail[$key]['nm_material'] 	= $value['nm_material'];
			$ArrDetail[$key]['qty_purchase'] 	= $value['qty_purchase'];
			$ArrDetail[$key]['price_ref'] 		= $value['price_ref'];
			$ArrDetail[$key]['price_ref_sup'] 	= $value['price_ref_sup'];
			$ArrDetail[$key]['moq'] 			= $value['moq'];
			$ArrDetail[$key]['satuan'] 			= $value['satuan'];
			$ArrDetail[$key]['tgl_dibutuhkan'] 	= $value['tgl_dibutuhkan'];
			$ArrDetail[$key]['lead_time'] 		= $value['lead_time'];
			$ArrDetail[$key]['net_price'] 		= $value['net_price'];
			$ArrDetail[$key]['total_price'] 	= $value['total_price'];
			$ArrDetail[$key]['created_by'] 		= $Username;
			$ArrDetail[$key]['created_date'] 	= $dateTime;
		}

		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit();
		$this->db->trans_start();
			if(!empty($ArrDetail)){
				$this->db->insert('tran_material_po_header', $ArrHeader);
				$this->db->insert_batch('tran_material_po_detail', $ArrDetail);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1
			);
			history('Repeat PO '.$po_repeat.', new po number '.$no_po);
		}
		echo json_encode($Arr_Kembali);
	}

	public function repeat_po(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Pembelian Material >> Repeat PO',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data repeat po material');
		$this->load->view('Purchase_order/repeat_po',$data);
	}

	public function server_side_repeat_po(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/repeat_po";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_repeat_po(
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
			
			$list_supplier		= $this->db->query("SELECT nm_supplier FROM tran_material_po_header WHERE no_po='".$row['no_po']."'")->result_array();
			$arr_sup = array();
			foreach($list_supplier AS $val => $valx){
				$arr_sup[$val] = $valx['nm_supplier'];
			}
			$dt_sup	= implode("<br>", $arr_sup);
			
			if($row['status'] != 'DELETED'){
				$list_material	= $this->db->query("SELECT nm_material, qty_purchase, price_ref, price_ref_sup, net_price FROM tran_material_po_detail WHERE no_po='".$row['no_po']."' AND deleted='N' GROUP BY id_material")->result_array();
			}
			else{
				$list_material		= $this->db->query("SELECT nm_material, qty_purchase, price_ref, price_ref_sup, net_price FROM tran_material_po_detail WHERE no_po='".$row['no_po']."' GROUP BY id_material")->result_array();
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
				$arr_pur[$val] = number_format($valx['net_price'],2);
			}
			$dt_pur	= implode("<br>", $arr_pur);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_po']."</div>";
			$nestedData[]	= "<div align='left'>".$dt_sup."</div>";
			$nestedData[]	= "<div align='left'>".$dt_mat."</div>";
			$nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price'],2)."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
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

			$nestedData[]	= "<div align='left'>".$span_bg."</div>";
			$edit_print = "";
			$edit_po = "";
			$print_po = "";
			$delete_po = "";
			if($row['status'] == 'WAITING IN' AND $row['status1'] == 'Y' AND $row['status2'] == 'Y'){
				$edit_print	= "&nbsp;<button type='button' class='btn btn-sm btn-warning edit_po' title='Edit Print PO' data-no_po='".$row['no_po']."'><i class='fa fa-pencil'></i></button>";
				$print_po	= "&nbsp;<a href='".base_url('purchase/print_po/'.$row['no_po'])."' target='_blank' class='btn btn-sm btn-info' title='Print PO' data-role='qtip'><i class='fa fa-print'></i></a>";
			}
			if($row['status'] == 'WAITING IN' AND $row['status1'] == 'N' AND $row['status2'] == 'N'){
				$edit_po	= "&nbsp;<button type='button' class='btn btn-sm btn-success edit_po_qty' title='Edit PO' data-no_po='".$row['no_po']."'><i class='fa fa-edit'></i></button>";
				// $delete_po	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete_po' title='Delete PO' data-no_po='".$row['no_po']."'><i class='fa fa-trash'></i></button>";
			}
			
			$nestedData[]	= "	<div align='left'>
                                    <button type='button' class='btn btn-sm btn-primary detailMat' title='Detail PO' data-no_po='".$row['no_po']."'><i class='fa fa-eye'></i></button>
									".$edit_po."
									".$edit_print."
									".$print_po."
									".$delete_po."
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

	public function query_data_json_repeat_po($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				tran_material_po_header a,
				(SELECT @row:=0) r
			WHERE 1=1 AND a.repeat_po IS NOT NULL AND a.deleted = 'N'
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

		$sql .= " ORDER BY a.updated_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function delete_sebagian_po_new(){
		$this->purchase_order_model->delete_sebagian_po_new();
	}

	public function delete_sebagian_po_new_repeat(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$no_po 		= $data['no_po'];
		$id 		= $data['id'];
		
		$detail 	= $this->db->select('*')->from('tran_material_po_detail')->where('id',$id)->where('no_po',$no_po)->where('deleted','N')->get()->result_array();
		
		$ArrEdit = [];
		$SUM_MAT = 0;
		if(!empty($detail)){
			foreach($detail AS $val => $valx){
				$ArrEdit[$val]['id'] 			= $valx['id'];
				$ArrEdit[$val]['deleted'] 		= 'Y';
				$ArrEdit[$val]['deleted_by'] 	= $data_session['ORI_User']['username'];
				$ArrEdit[$val]['deleted_date'] = date('Y-m-d H:i:s');
				
				$SUM_MAT += $valx['qty_purchase'];
			}
		}
		// print_r($ArrEdit);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrEdit)){
				$this->db->update_batch('tran_material_po_detail', $ArrEdit, 'id');
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save data failed. Please try again later ...',
				'status'	=> 0,
				'no_po' 	=> $no_po
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save data success. Thanks ...',
				'status'	=> 1,
				'no_po' 	=> $no_po
			);				
			history('Delete sebagian PO Repeat : '.$no_po.' / '.$id);
		}
		echo json_encode($Arr_Data);
	}

	//Approval PO
	public function approval_po($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approval_po/".$id;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Pembelian Material >> Approval PO',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'id'			=> $id
		);
		history('View approval po '.$id);
		$this->load->view('Purchase_order/approval_po',$data);
	}

	public function server_side_purchase_order_approve(){
		$this->purchase_order_model->get_data_json_purchase_order_approve();
	}

	public function modal_approval_po(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$Username 		= $this->session->userdata['ORI_User']['username'];
			$dateTime		= date('Y-m-d H:i:s');

			$id				= $data['id'];
            $no_po			= $data['no_po'];
            $status 		= $data['status'];
            $approve_reason = $data['approve_reason'];
            $nilai_po 		= $data['nilai_po'] * 14000;

			$created 	= ($id == '1') ? 'approval1_by':'approval2_by';
			$dated 		= ($id == '1') ? 'approval1_date':'approval2_date';
			$reason 	= ($id == '1') ? 'reason1':'reason2';
			$statusx 	= ($id == '1') ? 'status1':'status2';

			// $ArrUpdate = [
			// 	$statusx => $status,
			// 	$reason => $approve_reason,
			// 	$created => $Username,
			// 	$dated => $dateTime
			// ];

			// if($nilai_po <= 50000000){
				$ArrUpdate = [
					'status1' => $status,
					'reason1' => $approve_reason,
					'approval1_by' => $Username,
					'approval1_date' => $dateTime,
					'status2' => $status,
					'reason2' => $approve_reason,
					'approval2_by' => $Username,
					'approval2_date' => $dateTime
				];
			// }

			$this->db->trans_start();
				$this->db->where('no_po',$no_po);
				$this->db->update('tran_material_po_header', $ArrUpdate);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Process data failed. Please try again later ...',
					'status'	=> 2,
					'id'		=> $id
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Process data success. Thanks ...',
					'status'	=> 1,
					'id'		=> $id
				);
				history('Approval '.$id.' / '.$no_po.' / '.$status);
			}
			echo json_encode($Arr_Kembali);

		}
		else{
			$id 	= $this->uri->segment(4);
			$no_po 	= $this->uri->segment(3);

			$result	= $this->db
								->select('a.*, b.nm_supplier, b.total_price AS total_price2, b.net_plus_tax, b.delivery_cost, b.net_price AS net_price2, b.tax, b.total_po, b.discount, b.tgl_dibutuhkan AS tgl_butuh, b.mata_uang')
								->from('tran_material_po_detail a')
								->join('tran_material_po_header b','ON a.no_po=b.no_po', 'left')
								->where('a.no_po', $no_po)
								->get()
								->result_array();
			
			$data = array(
				'result' 	=> $result,
				'id' 		=> $id,
				'no_po' 	=> $no_po,
				'nilai_po'	=> $result[0]['total_price2']
			);
			
			$this->load->view('Purchase_order/modal_approval_po', $data);
		}
	}
	
	public function po_top($id_po) {
		$this->purchase_order_model->po_top();
	}
	public function save_po_top() {
		$this->purchase_order_model->save_po_top();
	}
	public function invoice_receive($id) {
		$controller			= 'purchase/purchase_order';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$info_payterm 	= $this->db->query("select * from billing_top where id='".$id."'")->row();
		if($info_payterm->invoice_no!=""){
			$dt_incoming=$this->db->query("select a.*, sum(b.harga*b.check_qty_oke) as total from warehouse_adjustment a 
			inner join warehouse_adjustment_detail b on a.kode_trans = b.kode_trans
			where a.id_invoice='".$id."' and a.no_ipp='".$info_payterm->no_po."'")->result();
		}else{
			$dt_incoming=$this->db->query("select a.*, sum(b.harga*b.check_qty_oke) as total from warehouse_adjustment a 
			inner join warehouse_adjustment_detail b on a.kode_trans = b.kode_trans where a.no_ipp='".$info_payterm->no_po."' and (a.id_invoice is null or a.id_invoice = '')")->result();
		}
        
		$nilai_po 	= $this->db->query("select * from tran_material_po_header where no_po='".$info_payterm->no_po."'")->row();
        $total_price = $nilai_po->net_price;
		$mata_uang   = $nilai_po->mata_uang;
		$total_harga = $nilai_po->total_price;
		$tax = $nilai_po->tax;

		$info_dp 	= $this->db->query("select sum(value_idr) as total_dp from billing_top where group_top ='uang muka' AND no_po='".$info_payterm->no_po."'")->row();
     

		$data = array(
			'title'			=> 'Receive Invoice',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'results'		=> $info_payterm,
			'akses_menu'	=> $Arr_Akses,
			'dt_incoming'	=> $dt_incoming,
			'total_price'	=> $total_price,
			'total_harga'	=> $total_harga,
			'mata_uang'		=> $mata_uang,
			'tax'		    => $tax,
			'dp'		    => $info_dp->total_dp,
			'id'			=> $id
		);
		history('View receive invoice '.$id);
		$this->load->view('Purchase_order/form_receive_invoice',$data);		
	}
	function receive_invoice_save(){
		$data = $this->input->post();
		$data_session	= $this->session->userdata;
		$Username 		= $this->session->userdata['ORI_User']['username'];
		$dateTime		= date('Y-m-d H:i:s');

		$id				= $data['id_top'];
		$ArrUpdate = [
			'invoice_no' => $data['invoice_no'],
			'nilai_ppn' => $data['nilai_ppn'],
			'invoice_total' => $data['invoice_total'],
			'faktur_pajak' => $data['faktur_pajak'],
			'surat_jalan' => $data['surat_jalan'],
			'lainnya' => $data['lainnya'],
			'tgl_terima' => $data['tgl_terima'],
			'kurs_receive_invoice' => $data['kurs'],
			'matauang_receive_invoice' => $data['matauang2'],
			'created_date_invoice' => $dateTime,
//			'invoice_dokumen' => $data['invoice_dokumen'],
			'created_by_invoice' => $Username,
			'nilai_po' => $data['nilai_po'],
			'net'      => $data['nilai_net'],
			'dpp'      => $data['nilai_dpp'],
			
			
		];
		$total= $data['invoice_total'];
		$totalunbill=0;
		$totalap=0;
		$coaunbill='';
		$coaap='';
		$this->db->trans_start();
		$no_po=$data['no_po'];
		$no_perkiraan='';
		$datapo = $this->db->query("select * from tran_material_po_header where no_po='".$no_po."'")->row();

		if($data['group_top']=='progress'){
			$ArrUpdate = [
				'potong_um'      => $data['potong_um'],	
			];

		}
       
		if($data['group_top']=='uang muka'){			
				$jenis_jurnal='JV053';
			}else{
				$jenis_jurnal='JV041';
			}

			$datajurnal1 = $this->db->query("select * from ".DBACC.".master_oto_jurnal_detail where kode_master_jurnal='".$jenis_jurnal."' order by parameter_no")->result();
			$nomor_jurnal=$jenis_jurnal.$no_po.rand(100,999);
			$payment_date=$data['tgl_terima']; // date("Y-m-d")
			$det_Jurnaltes1=array();
			if($total!=0) {
			  foreach ($datajurnal1 as $rec) { 
				if($rec->parameter_no=="1"){
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'PO '.$datapo->no_po, 'no_request' => $datapo->no_po, 'debet' => $data['invoice_total']-$data['nilai_ppn'], 'kredit' => 0, 'no_reff' => $data['invoice_no'], 'jenis_jurnal'=>$jenis_jurnal, 'nocust'=>$datapo->id_supplier, 'stspos' => '1'
					);
					$totalunbill=$data['invoice_total']-$data['nilai_ppn'];
					$coaunbill=$rec->no_perkiraan;	
				}
				if($rec->parameter_no=="2"){
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'PO '.$datapo->no_po, 'no_request' => $datapo->no_po, 'debet' => 0, 'kredit' => $data['invoice_total'], 'no_reff' => $data['invoice_no'], 'jenis_jurnal'=>$jenis_jurnal, 'nocust'=>$datapo->id_supplier, 'stspos' => '1'
					);
					$no_perkiraan= $rec->no_perkiraan;
					$totalap=$data['invoice_total'];
				}
				if($rec->parameter_no=="3"){
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'PPN PO '.$datapo->no_po, 'no_request' => $datapo->no_po, 'debet' => $data['nilai_ppn'], 'kredit' => 0, 'no_reff' => $data['invoice_no'], 'jenis_jurnal'=>$jenis_jurnal, 'nocust'=>$datapo->id_supplier, 'stspos' => '1'
					);
				}
			  }
			  $this->db->insert_batch('jurnaltras', $det_Jurnaltes1);
				//auto jurnal

				$tanggal = $data['tgl_terima'];
				$Bln	= substr($tanggal,5,2);
				$Thn	= substr($tanggal,0,4);
				$total	= 0;
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);
				foreach ($det_Jurnaltes1 as $vals) {
					$datadetail = array(
						'tipe'			=> 'JV',
						'nomor'			=> $Nomor_JV,
						'tanggal'		=> $tanggal,
						'no_perkiraan'	=> $vals['no_perkiraan'],
						'keterangan'	=> $vals['keterangan'],
						'no_reff'		=> $vals['no_reff'],
						'debet'			=> $vals['debet'],
						'kredit'		=> $vals['kredit'],
						'created_on' 	=> $dateTime,
						'created_by' 	=> $Username,
						);
					$total=($total+$vals['debet']);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				}
				$keterangan		= 'Receive Invoice '.$data['invoice_no'];
				$dataJVhead = array(
					'nomor' 	    	=> $Nomor_JV,
					'tgl'	         	=> $tanggal,
					'jml'	            => $total,
					'bulan'	            => $Bln,
					'tahun'	            => $Thn,
					'kdcab'				=> '101',
					'jenis'			    => 'JV',
					'keterangan'		=> $keterangan,
					'user_id'			=> $Username,
					'ho_valid'			=> '',
				);
				$this->db->insert(DBACC . '.javh', $dataJVhead);
				$datahutang = array(
					'tipe'       	 => 'JV',
					'nomor'       	 => $Nomor_JV,
					'tanggal'        => $tanggal,
					'no_perkiraan'   => $coaunbill,
					'keterangan'     => $keterangan,
					'no_reff'     	 => $no_po,
					'kredit'      	 => 0,
					'debet'          => $totalunbill,
					'id_supplier'    => $datapo->id_supplier,
					'nama_supplier'  => $datapo->nm_supplier,
					'no_request'     => $data['invoice_no'],
				);
				$this->db->insert('tr_kartu_hutang',$datahutang);	
				$datahutang = array(
					'tipe'       	 => 'JV',
					'nomor'       	 => $Nomor_JV,
					'tanggal'        => $tanggal,
					'no_perkiraan'   => $no_perkiraan,
					'keterangan'     => $keterangan,
					'no_reff'     	 => $no_po,
					'kredit'         => $data['invoice_total'],
					'debet'      	 => 0,
					'id_supplier'    => $datapo->id_supplier,
					'nama_supplier'  => $datapo->nm_supplier,
					'no_request'     => $data['invoice_no'],
					'debet_usd'		 => 0,
					'kredit_usd'	 => 0,
				);
				$this->db->insert('tr_kartu_hutang',$datahutang);
				//end auto jurnal
			}
		
		
		$this->db->where('id',$id);
		$this->db->update('billing_top', $ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 2,
				'id'		=> $id
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1,
				'id'		=> $id
			);
			history('Reeceive Invoice '.$id);
		}
		echo json_encode($Arr_Kembali);
	}

	public function request_payment($id_top){
		$info_payterm 	= $this->db->query("select * from billing_top where id='".$id_top."'")->row();
		$datapoh = $this->db->query("select a.*,b.data_bank from tran_material_po_header a left join supplier b on a.id_supplier=b.id_supplier where a.no_po='".$info_payterm->no_po."'")->row();
		$no_po=$datapoh->no_po;
		$curency  = $this->db->limit(1)->get_where('kurs', array('kode_dari'=>'USD'))->result();
		$payterm  = $this->db->query("select data2,name from list_help where group_by='top' and name='".$info_payterm->group_top."'")->row();		
		$datapod=array();
		$data_payterm=array();
		if(!empty($datapoh)){
			$datapod = $this->db->query("SELECT a.*, b.nm_supplier FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='".$no_po."' AND a.deleted='N'")->result();
			$data_payterm 	= $this->db->query("select * from billing_top where no_po='".$info_payterm->no_po."'")->result();
			$def_ppn=(object)array('info'=>$datapoh->tax);
		}else{
			$def_ppn=$this->All_model->getppn(); 
		}
		$def_pph=$this->All_model->getpph();
		$controller			= "";
		$Arr_Akses			= getAcccesmenu($controller);
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$combo_coa_pph=$this->All_model->Getcomboparamcoa('pph_pembelian');
		$data = array(
			'title'			=> 'Request Payment',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'def_ppn' 		=> $def_ppn,
			'def_pph' 		=> $def_pph,
			'datapoh' 		=> $datapoh,
			'info_payterm' 	=> $info_payterm,
			'payterm'		=> $payterm,
			'data_payterm'	=> $data_payterm,
			'datapod'		=> $datapod,
			'data_ppn'		=> $this->data_ppn,
			'curency'		=> $curency,
			'datapo'		=> $datapoh,
			'combo_coa_pph'	=> $combo_coa_pph,
		);		
		$this->load->view('Purchase_order/form_request', $data);
	}
	public function request_payment_save() {
		$id_req = $this->input->post("id_req");
		$request_date = $this->input->post("request_date");
		$no_po = $this->input->post("no_po");
		$id_supplier = $this->input->post("id_supplier");
		$nilai_ppn = $this->input->post("nilai_ppn");
		$curs_header = $this->input->post("curs_header");
		$nilai_total = $this->input->post("nilai_total");
		$total_bayar = $this->input->post("total_bayar");
		$po_belum_dibayar = $this->input->post("po_belum_dibayar");
		$sisa_dp = $this->input->post("sisa_dp");
		$tipe = $this->input->post("tipe");
		$no_request = $this->input->post("no_request");
		$no_invoice = $this->input->post("no_invoice");
		$nilai_invoice = $this->input->post("nilai_invoice");
		$keterangan = $this->input->post("keterangan");
		$potongan_dp = $this->input->post("potongan_dp");
		$potongan_claim = $this->input->post("potongan_claim");
		$keterangan_potongan = $this->input->post("keterangan_potongan");
		$request_payment = $this->input->post("request_payment");
		$invoice_ppn = $this->input->post("invoice_ppn");
		$payfor = $this->input->post("payfor");
		$nilai_po_invoice = $this->input->post("nilai_po_invoice");
		$nilai_pph_invoice = $this->input->post("nilai_pph_invoice");
		$coa_pph = $this->input->post("coa_pph");
		$id_top = $this->input->post("id_top");
		$nilai_po = $this->input->post("nilai_po");
		$req_payment_date= $this->input->post("req_payment_date");
		$bank_transfer= $this->input->post("bank_transfer");
		

		$data_session	= $this->session->userdata;
		$Username 		= $this->session->userdata['ORI_User']['username'];
		$dateTime		= date('Y-m-d H:i:s');

		$this->db->trans_begin();
		if ($id_req == '') {
			$no_request = $this->All_model->GenerateAutoNumber_YM('request_payment');
			$id_po = $no_po;
			$dataheader =  array(
				'id_top' => $id_top,
				'no_request' => $no_request,
				'request_date' => $request_date,
				'no_po' => $no_po,
				'id_supplier' => $id_supplier,
				'nilai_po' => $nilai_po,
				'nilai_ppn' => $nilai_ppn,
				'tipe' => $tipe,
				'coa_pph' => $coa_pph,
				'req_payment_date'=>$req_payment_date,
				'status' => '0',
				'curs_header' => $curs_header,
				'nilai_total' => $nilai_total,
				'total_bayar' => $total_bayar,
				'po_belum_dibayar' => $po_belum_dibayar,
				'sisa_dp' => $sisa_dp,
				'nilai_po_invoice' => $nilai_po_invoice,
				'nilai_pph_invoice' => $nilai_pph_invoice,
				'no_invoice' => $no_invoice,
				'nilai_invoice' => $nilai_invoice,
				'invoice_ppn' => $invoice_ppn,
				'keterangan' => $keterangan,
				'potongan_dp' => $potongan_dp,
				'potongan_claim' => $potongan_claim,
				'keterangan_potongan' => $keterangan_potongan,
				'request_payment' => $request_payment,
				'bank_transfer' => $bank_transfer,
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => $Username
			);
			$idreq=$this->All_model->DataSave('purchase_order_request_payment', $dataheader);
			if($id_top!='') $this->All_model->DataUpdate('billing_top', array('proses_inv'=>'1','id_penagihan'=>$idreq),array('id'=>$id_top));
			if(!empty($payfor)){
				foreach($payfor as $val){
					if($val!="") $this->All_model->DataUpdate('tran_material_po_detail', array('status_pay'=>$no_request), array('id' => $val) );
				}
			}
		} else {
			$dataheader =  array(
				'id_top' => $id_top,
				'request_date' => $request_date,
				'curs_header' => $curs_header,
				'no_invoice' => $no_invoice,
				'nilai_invoice' => $nilai_invoice,
				'keterangan' => $keterangan,
				'potongan_dp' => $potongan_dp,
				'potongan_claim' => $potongan_claim,
				'keterangan_potongan' => $keterangan_potongan,
				'invoice_ppn' => $invoice_ppn,
				'tipe' => $tipe,
				'coa_pph' => $coa_pph,
				'nilai_po_invoice' => $nilai_po_invoice,
				'nilai_pph_invoice' => $nilai_pph_invoice,
				'nilai_po' => $nilai_po,
				'request_payment' => $request_payment,
				'bank_transfer' => $bank_transfer,
				'modified_on' => date('Y-m-d H:i:s'),
				'modified_by' => $Username
			);
			$this->All_model->DataUpdate('purchase_order_request_payment', $dataheader, array('id' => $id_req));
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
				'status'	=> 1,
				'id_request'=>$id_top
			);				
			history('Add Request payment PO : '.$no_po);
		}
		echo json_encode($Arr_Data);
	}
	function print_request($id_request){
		$datapo = $this->db->query("select * from purchase_order_request_payment where id_top='".$id_request."'")->row();
		$data = array(
			'datapo' 		=> $datapo,
		);		
		$this->load->view('Purchase_order/print_request', $data);
	}
	function close_po($no_po){

		$datapo = $this->db->query("select * from tran_material_po_header where no_po='".$no_po."'")->row();
		$curency  = $this->db->limit(1)->get_where('kurs', array('kode_dari'=>'USD'))->result();
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Close PO',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'datapo' 		=> $datapo,
			'curency'		=> $curency,
		);		
		$this->load->view('Purchase_order/close_po', $data);
	}
	function save_close_po(){
		$no_po = $this->input->post("no_po");
		$this->db->trans_begin();
/*
		$data = $this->db->query("select * from tran_material_po_header where no_po='".$no_po."'")->row();
		if($data->total_terima_barang_idr!=$data->total_bayar_rupiah){
			$jenis_jurnal='JV034';
			$datajurnal1 = $this->db->query("select * from ".DBACC.".master_oto_jurnal_detail where kode_master_jurnal='".$jenis_jurnal."' order by parameter_no")->result();
			$nomor_jurnal=$jenis_jurnal.$no_po.rand(100,999);
			$payment_date=date("Y-m-d");
			$det_Jurnaltes1=array();
			$selisih=($data->total_terima_barang_idr-$data->total_bayar_rupiah);
			if($selisih!=0) {
			  foreach ($datajurnal1 as $rec) {
				if($rec->parameter_no=="1"){
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Inventory '.$data->no_po, 'no_request' => $data->no_po, 'debet' => (($selisih>0)?abs($selisih):0), 'kredit' => (($selisih>0)?0:abs($selisih)), 'no_reff' => $data->no_po, 'jenis_jurnal'=>$jenis_jurnal, 'nocust'=>$data->id_supplier
					);
				}
				if($rec->parameter_no=="2"){
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Inventory '.$data->no_po, 'no_request' => $data->no_po, 'debet' => (($selisih>0)?0:abs($selisih)), 'kredit' => (($selisih>0)?abs($selisih):0), 'no_reff' => $data->no_po, 'jenis_jurnal'=>$jenis_jurnal, 'nocust'=>$data->id_supplier
					);
				}
			  }
			}
			$this->db->insert_batch('jurnaltras', $det_Jurnaltes1);
		}
*/
		$this->db->query("update tran_material_po_header set status_po='CLS',status='COMPLETE' where no_po='".$no_po."'");	
		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			$this->db->trans_commit();
			$result         = TRUE;
			history('Close PO : '.$no_po);
		} else {
			$this->db->trans_rollback();
			$result = FALSE;
		}
		$param = array(
			'save' => $result
		);
		echo json_encode($param);
	}
	function jurnal_invoice(){
		$controller			= 'purchase/jurnal_invoice';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data = array(
			'title'			=> 'Jurnal Penerimaan Invoice',
			'action'		=> 'index',
			'akses_menu'	=> $Arr_Akses,
		);
		history('View Jurnal Penerimaan Invoice');
		$this->load->view('Purchase_order/index_jurnal_invoice',$data);
	}

	public function server_side_jurnal_invoice() {
        $requestData = $_REQUEST;
		$statusdata = array();
        $fetch = $this->queryDataJSONJurnal("'JV041','JV053'", $requestData['search']['value'], $requestData['order'][0]['column'], $requestData['order'][0]['dir'], $requestData['start'], $requestData['length']);
        $totalData = $fetch['totalData'];
        $totalFiltered = $fetch['totalFiltered'];
        $query = $fetch['query'];
        $data = array();
        $urut1 = 1;
        $urut2 = 0;
        foreach ($query->result_array() as $row) {
            $total_data = $totalData;
            $start_dari = $requestData['start'];
            $asc_desc = $requestData['order'][0]['dir'];
            if ($asc_desc == 'asc') {
                $nomor = $urut1 + $start_dari;
            }
            if ($asc_desc == 'desc') {
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            $nestedData = array();
            $detail = "";
            $nestedData[] = "<div align='center'>" . $nomor . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['no_request']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['no_reff']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['tanggal']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['stspos']) . "</div>";
			if($row['stspos']!=1){
				$nestedData[] = 
				  "
				  <a class='btn btn-sm btn-default viewed' href='javascript:void(0)' title='View Jurnal Incoming' data-id='" . $row['nomor'] . "'><i class='fa fa-search'></i>
				  </a>
				   <a class='btn btn-warning btn-sm edited' href='javascript:void(0)' title='Edit Jurnal Incoming' data-id='" . $row['nomor'] . "'><i class='fa fa-check'></i>
				  </a>
				  ";
			} else {
				$nestedData[] = "
				  <a class='btn btn-warning btn-sm viewed' href='javascript:void(0)' title='View Jurnal Incoming' data-id='" . $row['nomor'] . "'><i class='fa fa-eye'></i>
				  </a>
				  ";
			}
            $data[] = $nestedData;
            $urut1++;
            $urut2++;
        }
        $json_data = array("draw" => intval($requestData['draw']), "recordsTotal" => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $data);
        echo json_encode($json_data);
    }
   public function queryDataJSONJurnal($type, $like_value = NULL, $column_order = '', $column_dir = NULL, $limit_start = NULL, $limit_length = NULL) {
            $sql = "SELECT a.nomor, a.no_request, a.no_reff, a.tanggal, a.tipe, a.jenis_jurnal, a.stspos FROM jurnaltras a			
			WHERE jenis_jurnal in (".$type.") and 
			(
			a.tanggal LIKE '%" . $this->db->escape_like_str($like_value) . "%'
			OR
			a.no_reff LIKE '%" . $this->db->escape_like_str($like_value) . "%'
			OR 
			a.no_request LIKE '%" . $this->db->escape_like_str($like_value) . "%'
			)
			group by a.nomor,a.no_request, a.no_reff, a.tanggal, a.tipe, a.jenis_jurnal, a.stspos
			";
        $data['totalData'] = $this->db->query($sql)->num_rows();
        $data['totalFiltered'] = $this->db->query($sql)->num_rows();
        $columns_order_by = array(0 => 'no_request', 1 => 'no_reff', 2 => 'tanggal', 3 => 'stspos'); 
        if($column_order!='') $sql.= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
        $sql.= " LIMIT " . $limit_start . " ," . $limit_length . " ";
        $data['query'] = $this->db->query($sql);
        return $data;
    }
	public function view_jurnal($id){
		$controller			= 'purchase/jurnal_invoice';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data = $this->db->query("select * from jurnaltras where nomor='".$id."' order by kredit,debet,no_perkiraan")->result();
        $datacoa	= $this->All_model->GetCoaCombo();
		$datapayterm  = $this->db->query("select data2,name from list_help where group_by='top' order by urut")->result();
		$payterm=array();
		foreach($datapayterm as $key=>$val){
			$payterm[$val->data2]=$val->name;
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'View Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'payterm'		=> $payterm,
			'datacoa'		=> $datacoa,
			'data'			=> $data,
			'status'		=> "view",
		);
		history('View Jurnal');
		$this->load->view('Purchase_order/form_jurnal',$data);
	}
	public function edit_jurnal($id){

		$controller			= 'purchase/jurnal_invoice';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data = $this->db->query("select * from jurnaltras where nomor='".$id."' order by kredit,debet,no_perkiraan")->result();
        $datacoa	= $this->All_model->GetCoaCombo();
		$datapayterm  = $this->db->query("select data2,name from list_help where group_by='top' order by urut")->result();
		$payterm=array();
		foreach($datapayterm as $key=>$val){
			$payterm[$val->data2]=$val->name;
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Edit Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'payterm'		=> $payterm,
			'datacoa'		=> $datacoa,
			'data'			=> $data,
		);
		history('Edit Jurnal');
		$this->load->view('Purchase_order/form_jurnal',$data);
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
		$data_vendor 	= $this->db->query("select * from supplier where id_supplier='".$nocust."'")->row();
		$nama_vendor =$data_vendor->nm_supplier;
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
		$keterangan		= 'Receive Invoice';
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

	public function purchase_order_ap(){
		$this->purchase_order_model->index_purchase_order_ap();
	}

	public function server_side_purchase_order_ap(){
		$this->purchase_order_model->get_data_json_purchase_order_ap();
	}
}
