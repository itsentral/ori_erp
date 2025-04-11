<?php
class Sales_order_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT create_by, create_date FROM table_sales_order ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		$ListIPP 			= $this->db->query("SELECT * FROM so_bf_header ORDER BY no_ipp ASC")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Sales Order',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'ListIPP'		=> $ListIPP
		);
		history('View Data Sales Order');
		$this->load->view('Sales_order/index',$data);
	}
	
	public function modal_detail_so(){
		$id_bq = $this->uri->segment(3);
		
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qMatr 		= "	SELECT
							a.id_bq,
							a.id_milik,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							(b.price_total / c.qty) * a.qty AS cost
						FROM
							so_bf_detail_header a 
								LEFT JOIN cost_project_detail b ON a.id_milik=b.caregory_sub
								LEFT JOIN bq_detail_header c ON a.id_milik=c.id
						WHERE
							a.id_bq = '".$id_bq."' AND a.id_category <> 'product kosong'";					
		$getDetail	= $this->db->query($qMatr)->result_array();

		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.sts_so = 'Y' ORDER BY a.id ASC ";
		$getEngCost	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 AND b.sts_so = 'Y' ORDER BY a.id ASC ";
		$getPackCost	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 AND b.sts_so = 'Y' ORDER BY a.urut ASC ";
		$getTruck	= $this->db->query($gTruck)->result_array();

		$engCPCV 	= "SELECT
							b.*,
							c.* 
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
						WHERE
							 b.category = 'lokal' 
							AND b.id_bq = '".$id_bq."' 
							AND b.price_total <> 0
							AND b.sts_so = 'Y'
						ORDER BY
							b.id ASC ";
		$getVia	= $this->db->query($engCPCV)->result_array();
		
		$sql_non_frp 	= "	SELECT
								a.id,
								a.id_milik,
								a.id_bq,
								a.id_material,
								a.qty,
								a.category,
								a.satuan,
								a.berat,
								b.price_total,
								b.qty AS qty_costing,
								b.weight AS weight_costing
							FROM
								so_bf_acc_and_mat a
								LEFT JOIN cost_project_detail b ON a.id_material = b.caregory_sub AND a.id_milik = b.id_milik
							WHERE
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND a.id_bq = '".$id_bq."' 
								AND b.id_bq = '".$id_bq."'";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.* 
							FROM 
								cost_project_detail a
								LEFT JOIN so_bf_acc_and_mat b ON a.caregory_sub = b.id_material AND a.id_milik = b.id_milik
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ORDER BY a.caregory_sub";
		$material		= $this->db->query($sql_material)->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'id_bq' => $id_bq,
			'ipp' => $ipp,
			'getDetail' => $getDetail,
			'getEngCost' => $getEngCost,
			'getPackCost' => $getPackCost,
			'getTruck' => $getTruck,
			'getVia' => $getVia,
			'otherArray' => $otherArray,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		
		$this->load->view('Sales_order/modalViewSo', $data);
	}
	
	public function modal_deal_so(){
		$id_bq 	= $this->uri->segment(3);
		$Imp	= explode('-', $id_bq);

		$qBQ 	= "	SELECT * FROM so_bf_header WHERE id_bq = '".$id_bq."' ";
		$row	= $this->db->query($qBQ)->result_array();

		$qBQdetailHeader 	= "SELECT
									a.*,
									(b.price_total / c.qty) * a.qty AS cost
								FROM
									so_bf_detail_header a 
										LEFT JOIN cost_project_detail b ON a.id_milik=b.caregory_sub
										LEFT JOIN bq_detail_header c ON a.id_milik=c.id
								WHERE
									a.id_bq = '".$id_bq."'";
		$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
		$NumBaris			= $this->db->query($qBQdetailHeader)->num_rows();

		$sql_material 	= "	SELECT
								a.id,
								a.id_milik,
								a.id_bq,
								a.id_material,
								a.qty,
								a.unit_price,
								(b.price_total / c.qty) * a.qty AS price_total
							FROM
								so_bf_acc_and_mat a
								LEFT JOIN cost_project_detail b ON a.id_material = b.caregory_sub AND a.id_milik = b.id_milik
								LEFT JOIN bq_acc_and_mat c ON a.id_milik=c.id
							WHERE
								a.category = 'mat'
								AND a.id_bq = '".$id_bq."' 
								AND b.id_bq = '".$id_bq."'";
		$rest_material 	= $this->db->query($sql_material)->result_array();
		$NumBaris0		= $this->db->query($sql_material)->num_rows();
		
		$sql_acc 	= "	SELECT
								a.id,
								a.id_milik,
								a.id_bq,
								a.id_material,
								a.qty,
								a.category,
								a.satuan,
								a.berat,
								a.unit_price,
								(b.price_total / c.qty) * a.qty AS price_total
							FROM
								so_bf_acc_and_mat a
								LEFT JOIN cost_project_detail b ON a.id_material = b.caregory_sub and a.id_milik=b.id_milik
								LEFT JOIN bq_acc_and_mat c ON a.id_milik=c.id
							WHERE
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND a.id_bq = '".$id_bq."' 
								AND b.id_bq = '".$id_bq."'";
		$rest_acc 	= $this->db->query($sql_acc)->result_array();
		$NumBaris1	= $this->db->query($sql_acc)->num_rows();

		$checkSO 	= "	SELECT * FROM production WHERE no_ipp = '".$Imp[1]."' AND `status`='WAITING SALES ORDER' ";
		$restChkSO	= $this->db->query($checkSO)->num_rows();
		
		//tambahan new
		$data_eng	= $this->db->get_where('cost_project_detail', array('category'=>'engine','id_bq'=>$id_bq,'option_type'=>'Y'))->result_array();
		$data_pack	= $this->db->get_where('cost_project_detail', array('category'=>'packing','id_bq'=>$id_bq,'price_total != '=> 0))->result_array();

		$sql_ship 	= "	(SELECT b.* FROM cost_project_detail b WHERE b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0)
						UNION
						(SELECT b.* FROM cost_project_detail b WHERE b.category = 'lokal' AND b.id_bq = '".$id_bq."' AND b.price_total != 0)
						";
		$data_ship	= $this->db->query($sql_ship)->result_array();

		// echo $checkSO;
		$data = array(
			'id_bq' 		=> $id_bq,
			'row' 			=> $row,
			'qBQdetailRest' => $qBQdetailRest,
			'NumBaris' 		=> $NumBaris + $NumBaris0 + $NumBaris1 + count($data_eng) + count($data_pack) + count($data_ship),
			'rest_material' => $rest_material,
			'rest_acc' 		=> $rest_acc,
			'restChkSO' 	=> $restChkSO,
			'data_eng' 		=> $data_eng,
			'data_pack' 	=> $data_pack,
			'data_ship' 	=> $data_ship
		);
		
		$this->load->view('Sales_order/modalAppCost', $data);
	}
	
	public function delete_sebagian_so(){
		$id 		= $this->uri->segment(3);
		$id_milik 	= $this->uri->segment(4);
		$id_bq 		= $this->uri->segment(5);
		$id_bq_header 		= $this->uri->segment(6);

		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'so_sts' => 'N',
			'so_by' => $data_session['ORI_User']['username'],
			'so_date' => date('Y-m-d H:i:s')
		);
		
		// print_r($ArrDetalHd); 
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id_milik);
			$this->db->update('bq_detail_header', $Arr_Edit);
			
			$this->db->where('id', $id);
			$this->db->delete('so_bf_detail_header');
			
			$this->db->where('id_bq_header', $id_bq_header);
			$this->db->delete('so_bf_detail_detail');

			$this->db->where('id_milik', $id_milik);
			$this->db->delete('billing_so_product');
			
			history('Delete sebagian SO with BQ : '.$id_bq.' / '.$id_milik);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq' => $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1,
				'id_bq' => $id_bq
			);				
			
		}
		echo json_encode($Arr_Data);
	}
	
	public function delete_sebagian_so_mat(){
		$id 		= $this->uri->segment(3);
		$id_milik 	= $this->uri->segment(4);
		$id_bq 		= $this->uri->segment(5);

		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'so_sts' => NULL,
			'so_by' => $data_session['ORI_User']['username'],
			'so_date' => date('Y-m-d H:i:s')
		);
		
		// print_r($ArrDetalHd); 
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id_milik);
			$this->db->update('bq_acc_and_mat', $Arr_Edit);
			
			$this->db->where('id', $id);
			$this->db->delete('so_bf_acc_and_mat');
			
			history('Delete sebagian Material SO with : '.$id_bq.' / '.$id_milik.' / '.$id);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq' => $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1,
				'id_bq' => $id_bq
			);				
			
		}
		echo json_encode($Arr_Data);
	}
	
	public function delete_sebagian_so_eng_pack_trans(){
		$id 		= $this->input->post('id');
		$id_bq 		= $this->input->post('id_bq');

		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'sts_so' => 'N',
			'so_by' => $data_session['ORI_User']['username'],
			'so_date' => date('Y-m-d H:i:s')
		);
		
		// print_r($ArrDetalHd); 
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('cost_project_detail', $Arr_Edit);
			
			history('Delete sebagian [eng/packing/trucking] SO with : '.$id_bq.' / '.$id);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq' => $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1,
				'id_bq' => $id_bq
			);				
			
		}
		echo json_encode($Arr_Data);
	}

	public function add_sebagian_so_eng_pack_trans(){
		$id 		= $this->input->post('id');
		$id_bq 		= $this->input->post('id_bq');

		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'sts_so' => 'Y',
			'so_by' => $data_session['ORI_User']['username'],
			'so_date' => date('Y-m-d H:i:s')
		);
		
		// print_r($ArrDetalHd); 
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('cost_project_detail', $Arr_Edit);
			
			history('Add sebagian [eng/packing/trucking] SO with : '.$id_bq.' / '.$id);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq' => $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1,
				'id_bq' => $id_bq
			);				
			
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_qty_so(){
		$data 			= $this->input->post();
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		if(!empty($data['UpQtySo'])){
			$UpQtySo		= $data['UpQtySo'];
		}
		if(!empty($data['UpQtySoMat'])){
			$UpQtySoMat		= $data['UpQtySoMat'];
		}
		$lopp2 = 0;
		$ArrDetalHd = array();
		$detailData2 = array();
		if(!empty($data['UpQtySo'])){
			foreach($UpQtySo AS $val => $valx){
				$QTY = str_replace(',','',$valx['qty']);
				$ArrDetalHd[$val]['id'] 	= $valx['id'];
				$ArrDetalHd[$val]['qty'] 	= $QTY;
				for($no=1; $no <= $QTY; $no++){
					$lopp2++;
					$detailData2[$lopp2]['id_bq'] 			= $id_bq;
					$detailData2[$lopp2]['id_bq_header'] 	= $valx['id_bq_header'];
					$detailData2[$lopp2]['id_delivery'] 	= $valx['id_delivery'];
					$detailData2[$lopp2]['series'] 			= $valx['series'];
					$detailData2[$lopp2]['sub_delivery'] 	= $valx['sub_delivery'];
					$detailData2[$lopp2]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData2[$lopp2]['id_category'] 	= $valx['id_category'];
					$detailData2[$lopp2]['diameter_1'] 		= $valx['diameter_1'];
					$detailData2[$lopp2]['diameter_2'] 		= $valx['diameter_2'];
					$detailData2[$lopp2]['length'] 			= $valx['length'];
					$detailData2[$lopp2]['thickness'] 		= $valx['thickness'];
					$detailData2[$lopp2]['sudut'] 			= $valx['sudut'];
					$detailData2[$lopp2]['id_standard'] 	= $valx['id_standard'];
					$detailData2[$lopp2]['type'] 			= $valx['type'];
					$detailData2[$lopp2]['qty'] 			= $QTY;
					$detailData2[$lopp2]['product_ke'] 		= $no;
				}
			}
		}
		
		$ArrDetalHd2 = array();
		if(!empty($data['UpQtySoMat'])){
			foreach($UpQtySoMat AS $val => $valx){
				$QTY = str_replace(',','',$valx['qty']);
				$ArrDetalHd2[$val]['id'] 	= $valx['id'];
				$ArrDetalHd2[$val]['qty'] 	= $QTY;
			}
		}
		// print_r($ArrDetalHd); 
		// exit;
		$this->db->trans_start();
			if(!empty($ArrDetalHd)){
				$this->db->update_batch('so_bf_detail_header', $ArrDetalHd, 'id');
			}
			if(!empty($ArrDetalHd2)){
				$this->db->update_batch('so_bf_acc_and_mat', $ArrDetalHd2, 'id');
			}
			if(!empty($detailData2)){
				$this->db->where('id_bq', $id_bq);
				$this->db->delete('so_bf_detail_detail');
			
				$this->db->insert_batch('so_bf_detail_detail', $detailData2);
			}
			
			history('Update QTY in SO with BQ : '.$id_bq);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq' => $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1,
				'id_bq' => $id_bq
			);				
			
		}
		echo json_encode($Arr_Data);
	}
	
	public function ajukan_so(){
		$id_bq 		= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		$data_session	= $this->session->userdata;
		$username 		= $data_session['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		
		$Arr_Edit	= array(
			'aju_approved' => 'Y',
			'aju_approved_by' => $username,
			'aju_approved_date' => $datetime
		);

		$ArrMonitoring	= array(
			'so_release_by' => $username,
			'so_release_date' => $datetime
		);
		
		$Arr_Edit2	= array(
			'status' => 'WAITING APPROVE SO'
		);
		// print_r($ArrDetalHd); 
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $Imp[1]);
			$this->db->update('production', $Arr_Edit2);

			$this->db->where('no_ipp', $Imp[1]);
			$this->db->update('monitoring_ipp', $ArrMonitoring);
				
			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_bf_header', $Arr_Edit);
			
			history('Mengajukan Sales Order : '.$id_bq);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);				
			
		}
		echo json_encode($Arr_Data);
	}
	
	function insert_sales_order(){
		$data = $this->input->post();
		$where = "";
		if(!empty($data['no_ipp_filter'])){
			$ListIPP = $data['no_ipp_filter'];
			$dtListArray = array();
			foreach($ListIPP AS $val => $valx){
				$dtListArray[$val] = $valx;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			// echo $dtImplode;
			$where = " WHERE a.no_ipp IN ".$dtImplode."";
		}
		
		// exit;
		history('Try insert batch sales order');
		$sqlUpdate = "SELECT
					a.*,
					a.no_ipp,
					b.id_customer,
					b.nm_customer,
					b.project,
					b.ref_quo,
					b.status,
					b.sts_price_quo,
					c.total_deal_usd,
					c.no_po
					FROM
						so_bf_header a 
						LEFT JOIN production b ON a.no_ipp = b.no_ipp
						LEFT JOIN billing_so c ON c.no_ipp = b.no_ipp ".$where." ORDER BY a.created_date DESC";
		// echo $sqlUpdate; exit;
		$restUpdate = $this->db->query($sqlUpdate)->result_array();
		$ArrUpdate = array();
		foreach($restUpdate AS $val => $valx){
			$no_ipp = str_replace('BQ-','',$valx['id_bq']);
			$dataRevisi 	= $this->db->select('MAX(revised_no) AS nomor_revisi')->get_where('laporan_costing_header',array('id_bq'=>'BQ-'.$no_ipp))->result();
			$revisiNumber 	= $dataRevisi[0]->nomor_revisi;
			$BERAT_MATERIAL = 0;

			$dataArrayRev	= $this->db->get_where('laporan_costing_detail',array('id_bq'=>'BQ-'.$no_ipp, 'revised_no'=>$revisiNumber))->result_array();
			$DataCheckBerat	= array();
			foreach ($dataArrayRev as $key => $value) {
				$DataCheckBerat[$value['id_milik']] = $value['est_material'] / $value['qty'];
			}

			$restUpdate 	= $this->db->get_where('billing_so_product',array('no_ipp'=>$no_ipp))->result_array();
			
			if(!empty($DataCheckBerat) AND !empty($restUpdate)){
				foreach($restUpdate AS $val2 => $valx2){
					$BERAT_QTY = (!empty($DataCheckBerat[$valx2['id_milik']]))?$DataCheckBerat[$valx2['id_milik']] * $valx2['qty'] : 0 ;
					$BERAT_MATERIAL += $BERAT_QTY;
				}
			}	

			$ArrUpdate[$val]['id_bq'] 			= $valx['id_bq'];
			$ArrUpdate[$val]['no_ipp'] 			= $valx['no_ipp'];
			$ArrUpdate[$val]['id_customer'] 	= $valx['id_customer'];
			$ArrUpdate[$val]['nm_customer'] 	= $valx['nm_customer'];
			$ArrUpdate[$val]['project'] 		= $valx['project'];
			$ArrUpdate[$val]['no_po'] 			= $valx['no_po'];
			$ArrUpdate[$val]['ref_quo'] 		= $valx['ref_quo'];
			$ArrUpdate[$val]['sum_sales_order'] = (!empty($valx['total_deal_usd']))?$valx['total_deal_usd']:0;
			$ArrUpdate[$val]['sum_material_so'] = $BERAT_MATERIAL;
			$ArrUpdate[$val]['sum_quotation'] 	= 0;
			$ArrUpdate[$val]['sum_final_drawing'] 	= 0;
			$ArrUpdate[$val]['status'] 			= $valx['status'];
			$ArrUpdate[$val]['sts_price_quo'] 	= $valx['sts_price_quo'];
			$ArrUpdate[$val]['aju_approved'] 	= $valx['aju_approved'];
			$ArrUpdate[$val]['aju_approved_est'] 	= $valx['aju_approved_est'];
			$ArrUpdate[$val]['approved_est'] 	= $valx['approved_est'];
			$ArrUpdate[$val]['approved'] 		= $valx['approved'];
			$ArrUpdate[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrUpdate[$val]['create_date'] 	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			$this->db->truncate('table_sales_order');
			$this->db->insert_batch('table_sales_order', $ArrUpdate);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);				
			history('Success insert batch sales order');
		}
		echo json_encode($Arr_Data);
		
	}
	
	function insert_sales_order_manual(){
		// $data = $this->input->post();
		$where = "";
		// if(!empty($data['no_ipp_filter'])){
			// $ListIPP = $data['no_ipp_filter'];
			// $dtListArray = array();
			// foreach($ListIPP AS $val => $valx){
				// $dtListArray[$val] = $valx;
			// }
			// $dtImplode	= "('".implode("','", $dtListArray)."')";
			// echo $dtImplode;
			// $where = " WHERE a.no_ipp IN ".$dtImplode."";
		// }
		
		// exit;
		history('Try insert batch sales order');
		$sqlUpdate = "SELECT
					a.*,
					a.no_ipp,
					b.id_customer,
					b.nm_customer,
					b.project,
					b.ref_quo,
					b.status,
					b.sts_price_quo 
					FROM
						so_bf_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp ".$where."";
		// echo $sqlUpdate; exit;
		$restUpdate = $this->db->query($sqlUpdate)->result_array();
		$ArrUpdate = array();
		foreach($restUpdate AS $val => $valx){
			$ArrUpdate[$val]['id_bq'] 			= $valx['id_bq'];
			$ArrUpdate[$val]['no_ipp'] 			= $valx['no_ipp'];
			$ArrUpdate[$val]['id_customer'] 	= $valx['id_customer'];
			$ArrUpdate[$val]['nm_customer'] 	= $valx['nm_customer'];
			$ArrUpdate[$val]['project'] 		= $valx['project'];
			$ArrUpdate[$val]['ref_quo'] 		= $valx['ref_quo'];
			$ArrUpdate[$val]['sum_sales_order'] = SUM_SO_ALL($valx['id_bq']);
			$ArrUpdate[$val]['sum_material_so'] = SUM_SO_MATERIAL_WEIGHT($valx['id_bq']);
			$ArrUpdate[$val]['sum_quotation'] 	= 0;
			$ArrUpdate[$val]['sum_final_drawing'] 	= 0;
			$ArrUpdate[$val]['status'] 			= $valx['status'];
			$ArrUpdate[$val]['sts_price_quo'] 	= $valx['sts_price_quo'];
			$ArrUpdate[$val]['aju_approved'] 	= $valx['aju_approved'];
			$ArrUpdate[$val]['aju_approved_est']= $valx['aju_approved_est'];
			$ArrUpdate[$val]['approved_est'] 	= $valx['approved_est'];
			$ArrUpdate[$val]['approved'] 		= $valx['approved'];
			$ArrUpdate[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrUpdate[$val]['create_date'] 	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			// $this->db->truncate('table_sales_order');
			$this->db->insert_batch('table_sales_order', $ArrUpdate);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);				
			history('Success insert batch sales order');
		}
		echo json_encode($Arr_Data);
		
	}
	
	
	
	//SERVER SIDE
	public function get_data_json_so(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_so(
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
				$produ = (!empty($row['so_number']))?$row['so_number']:$row['no_ipp'];
			$nestedData[]	= "<div align='left'>".$produ."</div>";
				$date_so = (get_name('billing_so','updated_date','no_ipp',$row['no_ipp']) <> '-')?date('d-M-Y', strtotime(get_name('billing_so','updated_date','no_ipp',$row['no_ipp']))):'-';
			$nestedData[]	= "<div align='right'>".$date_so."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_ipp']."</div>";
				$no_po = (!empty($row['no_po']))?$row['no_po']:'-';
			$nestedData[]	= "<div align='left'>".strtoupper($no_po)."</div>";
			if($row['canceled_so']=='Y'){
				$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			}else{
				$nestedData[]	= "<div align='left'><a class='active change_customer' style='cursor:pointer;' data-no_ipp='".$row['no_ipp']."' title='Change Customer'>".$row['nm_customer']."</a></div>";
			}
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = 'BQ-".$row['no_ipp']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".strtoupper(strtolower($row['ref_quo']))."</span></div>";
			
			$nestedData[]	= "<div align='right'>".number_format($row['sum_sales_order'],2)."</div>";
			if($row['canceled_so']=='Y'){
				$nestedData[]	= "<div align='right'>".number_format($row['sum_material_so'],3)."</div>";
			}else{
				$nestedData[]	= "<div align='right'>".number_format($row['sum_material_so'],3)."<br><a class='active udate_berat' style='cursor:pointer;' title='Update Berat' data-no_ipp='".$row['no_ipp']."'>Update</a></div>";
			}
			$warna = Color_status($row['status']);
			if($row['status']=='PARTIAL PROCESS'){
				if($Arr_Akses['approve']=='1'){
					$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$warna."'>".$row['status']."</span><br><a class='active closeso' style='cursor:pointer;' data-no_ipp='".$row['no_ipp']."' title='CLOSE SO'>CLOSE SO</a></div>";
				}else{
					$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$warna."'>".$row['status']."</span>";
				}
			}else{
				if($row['status']=='CLOSE'){
					$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."' title='Close date : ".$row['canceled_so_date'].". By ".$row['canceled_so_by'].".'>".$row['status']."</span></div>";
				}else{
					$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['status']."</span></div>";
				}
			}
					$deal	= "<a href='".base_url('sales_order/deal_project/'.$row['id_bq'])."' class='btn btn-sm btn-primary'  title='Deal Project' ><i class='fa fa-edit'></i></a>";
					$top	= "";
					$approve= "";
					$print	= "";
					$print2	= "";
					$print3	= "";
					$cancel_so	= "<a href='".base_url('sales_order/cancel_so/'.$row['no_ipp'])."' class='btn btn-sm btn-danger' title='Cancel detail SO' ><i class='fa fa-check-square-o'></i></a>";

					$view	= "<button class='btn btn-sm btn-warning detail' title='Look Data' data-id_bq='BQ-".$row['no_ipp']."'><i class='fa fa-eye'></i></button>";
					$print	= "<a href='".base_url('sales_order/print_sales_order_usd/'.$row['no_ipp'])."' target='_blank' class='btn btn-sm btn-info'  title='Print Sales Order USD' ><i class='fa fa-print'></i></a>";
					if($row['status'] == 'WAITING SALES ORDER'){
						if($Arr_Akses['approve']=='1'){
							$approve	= "<button class='btn btn-sm btn-success deal_so' title='Approve To Final Drawing' data-id_bq='BQ-".$row['no_ipp']."'><i class='fa fa-check'></i></button>";
						}
					}
					
					$num_so = $this->db->query("SELECT * FROM billing_so WHERE no_ipp = '".$row['no_ipp']."' ")->num_rows();
					if($num_so > 0){
						$print2	= "<a href='".base_url('sales_order/print_sales_order/'.$row['no_ipp'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order IDR' ><i class='fa fa-print'></i></a>";
						$print3	= "<a href='".base_url('sales_order/print_sales_order_ex_price/'.$row['no_ipp'])."' target='_blank' class='btn btn-sm btn-primary'  title='Print Sales Order Deal Tanpa Harga' ><i class='fa fa-print'></i></a>";
					
					}
			if($row['canceled_so']=='Y'){
				$nestedData[]	= "<div align='left'>".$view."</div>";
			}else{
				$nestedData[]	= "<div align='left'>
										".$view."
										".$approve."
										".$deal."
										".$top."
										".$print."
										".$print2."
										".$print3."
										".$cancel_so."
										</div>";
			}
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

	public function query_data_so($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.so_number
			FROM
				table_sales_order a LEFT JOIN so_bf_header b ON a.no_ipp=b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 1=1
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'so_number',
			3 => 'no_ipp',
			4 => 'no_po',
			5 => 'nm_customer',
			6 => 'project'
		);

		$sql .= " ORDER BY b.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	//==========================================================================================================================
	//=================================================APPROVE SALES ORDER======================================================
	//==========================================================================================================================
	
	public function approve_so(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approve_so";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT create_by, create_date FROM table_sales_order_approve ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Approve Sales Order',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View Data Approve Sales Order');
		$this->load->view('Sales_order/approve_so',$data);
	}
	
	public function modal_approve_so(){
		$id_bq = $this->uri->segment(3);
		$no_ipp	= str_replace('BQ-','', $id_bq);

		$qBQ 	= "	SELECT * FROM so_bf_header WHERE id_bq = '".$id_bq."' ";
		$row	= $this->db->query($qBQ)->result_array();

		$qBQdetailHeader 		= "	SELECT
										a.id_bq,
										a.id_milik,
										a.id_category,
										a.qty,
										a.diameter_1,
										a.diameter_2,
										a.series,
										a.id_product,
										(b.price_total / c.qty) * a.qty AS cost
									FROM
										so_bf_detail_header a 
											LEFT JOIN cost_project_detail b ON a.id_milik=b.caregory_sub
											LEFT JOIN bq_detail_header c ON a.id_milik=c.id
									WHERE
										a.id_bq = '".$id_bq."' AND a.id_category <> 'product kosong'";
		$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
		$NumBaris		= $this->db->query($qBQdetailHeader)->num_rows();

		$checkSO 	= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' AND `status`='WAITING APPROVE SO' ";
		$restChkSO	= $this->db->query($checkSO)->num_rows();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price
							FROM 
								cost_project_detail a
								LEFT JOIN so_bf_acc_and_mat b ON a.caregory_sub = b.id_material 
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.* 
							FROM 
								cost_project_detail a
								LEFT JOIN so_bf_acc_and_mat b ON a.caregory_sub = b.id_material 
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		
		//NEW APPROVED
		$get_revisi_max = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		$revised_no = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

		$get_resin_pipa = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','pipa')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrResinPipa[] = $value['nm_material'];
		}
		$ArrHargaPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrHargaPipa[] = $value['price_mat'];
		}
		$resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrResinPipa):'#';
		$harga_resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrHargaPipa):'#';
		
		$get_resin_flange = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','flange')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrResinFlange[] = $value['nm_material'];
		}
		$ArrHargaFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrHargaFlange[] = $value['price_mat'];
		}
		$resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrResinFlange):'#';
		$harga_resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrHargaFlange):'#';
		
		$get_resin_fitting = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing',NULL)
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrResinFitting[] = $value['nm_material'];
		}
		$ArrHargaFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrHargaFitting[] = $value['price_mat'];
		}
		$resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrResinFitting):'#';
		$harga_resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrHargaFitting):'#';
		
		$get_resin_bw = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','bw')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrResinBW[] = $value['nm_material'];
		}
		$ArrHargaBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrHargaBW[] = $value['price_mat'];
		}
		$resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrResinBW):'#';
		$harga_resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrHargaBW):'#';
		
		$get_resin_field = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','field')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrResinField[] = $value['nm_material'];
		}
		$ArrHargaField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrHargaField[] = $value['price_mat'];
		}
		$resin_field = (!empty($get_resin_field))?implode('<br>',$ArrResinField):'#';
		$harga_resin_field = (!empty($get_resin_field))?implode('<br>',$ArrHargaField):'#';

		$get_deal = $this->db->select('total_deal_usd')->get_where('billing_so',array('no_ipp'=>$no_ipp))->result();
		$deal_usd = (!empty($get_deal))?$get_deal[0]->total_deal_usd:0;

		$data = array(
			'id_bq'			=> $id_bq,
			'row'			=> $row,
			'qBQdetailRest'	=> $qBQdetailRest,
			'NumBaris'		=> $NumBaris,
			'restChkSO'		=> $restChkSO,
			'non_frp'		=> $non_frp,
			'material'		=> $material,

			'revised_no'	=> $revised_no,
			'resin_pipa'			=> $resin_pipa,
			'harga_resin_pipa'		=> $harga_resin_pipa,
			'resin_flange'			=> $resin_flange,
			'harga_resin_flange'	=> $harga_resin_flange,
			'resin_fitting'			=> $resin_fitting,
			'harga_resin_fitting'	=> $harga_resin_fitting,
			'resin_bw'				=> $resin_bw,
			'harga_resin_bw'		=> $harga_resin_bw,
			'resin_field'			=> $resin_field,
			'harga_resin_field'		=> $harga_resin_field,
			'deal_usd'				=> $deal_usd
		);
		
		$this->load->view('Sales_order/modalAppSo', $data);
	}
	
	public function modal_approve_so_new(){
		$id_bq = $this->uri->segment(3);
		$no_ipp	= str_replace('BQ-','', $id_bq);

		$qBQ 	= "	SELECT * FROM so_bf_header WHERE id_bq = '".$id_bq."' ";
		$row	= $this->db->query($qBQ)->result_array();

		$qBQdetailHeader 		= "	SELECT
										a.id_bq,
										a.id_milik,
										a.id_category,
										a.qty,
										a.diameter_1,
										a.diameter_2,
										a.series,
										a.id_product,
										b.total_deal_usd AS cost,
										b.customer_item
									FROM
										so_bf_detail_header a 
											LEFT JOIN billing_so_product b ON a.id_milik=b.id_milik
									WHERE
										a.id_bq = '".$id_bq."' 
										AND a.id_category <> 'product kosong'";
		$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();

		$checkSO 	= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' AND `status`='WAITING APPROVE SO' ";
		$restChkSO	= $this->db->query($checkSO)->num_rows();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								c.qty AS qty_so,
								c.total_deal_usd,
								c.satuan AS satuan_so
							FROM 
								cost_project_detail a
								LEFT JOIN so_bf_acc_and_mat b ON a.caregory_sub = b.id_material 
								LEFT JOIN billing_so_add c ON b.id = c.id_milik
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								c.qty AS qty_so,
								c.total_deal_usd,
								c.satuan AS satuan_so
							FROM 
								cost_project_detail a
								LEFT JOIN so_bf_acc_and_mat b ON a.caregory_sub = b.id_material
								LEFT JOIN billing_so_add c ON b.id = c.id_milik
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		
		//NEW APPROVED
		$get_revisi_max = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		$revised_no = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

		$get_resin_pipa = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','pipa')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrResinPipa[] = $value['nm_material'];
		}
		$ArrHargaPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrHargaPipa[] = $value['price_mat'];
		}
		$resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrResinPipa):'#';
		$harga_resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrHargaPipa):'#';
		
		$get_resin_flange = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','flange')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrResinFlange[] = $value['nm_material'];
		}
		$ArrHargaFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrHargaFlange[] = $value['price_mat'];
		}
		$resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrResinFlange):'#';
		$harga_resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrHargaFlange):'#';
		
		$get_resin_fitting = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing',NULL)
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrResinFitting[] = $value['nm_material'];
		}
		$ArrHargaFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrHargaFitting[] = $value['price_mat'];
		}
		$resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrResinFitting):'#';
		$harga_resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrHargaFitting):'#';
		
		$get_resin_bw = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','bw')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrResinBW[] = $value['nm_material'];
		}
		$ArrHargaBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrHargaBW[] = $value['price_mat'];
		}
		$resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrResinBW):'#';
		$harga_resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrHargaBW):'#';
		
		$get_resin_field = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','field')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrResinField[] = $value['nm_material'];
		}
		$ArrHargaField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrHargaField[] = $value['price_mat'];
		}
		$resin_field = (!empty($get_resin_field))?implode('<br>',$ArrResinField):'#';
		$harga_resin_field = (!empty($get_resin_field))?implode('<br>',$ArrHargaField):'#';

		$get_deal = $this->db->select('total_deal_usd')->get_where('billing_so',array('no_ipp'=>$no_ipp))->result();
		$deal_usd = (!empty($get_deal))?$get_deal[0]->total_deal_usd:0;

		$data = array(
			'id_bq'			=> $id_bq,
			'row'			=> $row,
			'qBQdetailRest'	=> $qBQdetailRest,
			'restChkSO'		=> $restChkSO,
			'non_frp'		=> $non_frp,
			'material'		=> $material,

			'revised_no'	=> $revised_no,
			'resin_pipa'			=> $resin_pipa,
			'harga_resin_pipa'		=> $harga_resin_pipa,
			'resin_flange'			=> $resin_flange,
			'harga_resin_flange'	=> $harga_resin_flange,
			'resin_fitting'			=> $resin_fitting,
			'harga_resin_fitting'	=> $harga_resin_fitting,
			'resin_bw'				=> $resin_bw,
			'harga_resin_bw'		=> $harga_resin_bw,
			'resin_field'			=> $resin_field,
			'harga_resin_field'		=> $harga_resin_field,
			'deal_usd'				=> $deal_usd
		);
		
		$this->load->view('Sales_order/modalAppSoNew', $data);
	}

	public function process_approve_so(){
		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		$data_session	= $this->session->userdata;
		$username 		= $data_session['ORI_User']['username'];
		$datetime		= date('Y-m-d H:i:s');
		$stsX			= ($this->input->post('status') == 'Y')?"WAITING FINAL DRAWING":"WAITING SALES ORDER";
		
		$checkPro 	= "SELECT * FROM so_header WHERE id_bq='".$id_bq."'";
		$numPro 	= $this->db->query($checkPro)->num_rows();
		// echo $numPro; exit;
		if($numPro < 1){
			
			if($this->input->post('status') == 'N'){
				$Arr_Edit	= array(
					'aju_approved' => 'N',
					'aju_approved_est' => 'N'
				);
				
				$Arr_Edit2	= array(
					'status' => $stsX,
					'quo_reason' => $this->input->post('approve_reason'),
					'quo_by' => $username,
					'quo_date' => $datetime
				);
			}
			
			if($this->input->post('status') == 'Y'){
				$Arr_Edit	= array(
					'approved' 		=> 'Y',
					'approved_by' 	=> $username,
					'approved_date' => $datetime
				);

				$ArrMonitoring	= array(
					'app_so_by' 	=> $username,
					'app_so_date' => $datetime
				);
				
				$Arr_Edit2	= array(
					'mp' => 'Y',
					'mp_by' => $username,
					'mp_date' => $datetime,
					'status' => $stsX,
					'quo_reason' => $this->input->post('approve_reason'),
					'quo_by' => $username,
					'quo_date' => $datetime
				);
				
				//Duplicate Bq Header
				$getBq_Header = $this->db->query("SELECT * FROM so_bf_header WHERE id_bq='".$id_bq."' ")->result();
				$ArrBqHeader = array(
					'id_bq' => $getBq_Header[0]->id_bq,
					'no_ipp'  => $getBq_Header[0]->no_ipp,
					'series'  => $getBq_Header[0]->series,
					'order_type'  => $getBq_Header[0]->order_type,
					'ket' => $getBq_Header[0]->ket,
					'estimasi'  => 'N',
					'rev' => '0',
					'created_by'  => $username,
					'created_date'  => $datetime,
					'modified_by' => $getBq_Header[0]->modified_by,
					'modified_date' => $getBq_Header[0]->modified_date,
					'est_by'  => $getBq_Header[0]->est_by,
					'est_date'  => $getBq_Header[0]->est_date,
				);
				
				$spool = (strtolower($getBq_Header[0]->order_type) == 'spool' OR strtolower($getBq_Header[0]->order_type) == 'spool & loose')?'spool':'loose';
				
				$ArrSchedule = array(
					'no_ipp'  		=> $getBq_Header[0]->no_ipp,
					'so_number'  	=> get_nomor_so($getBq_Header[0]->no_ipp),
					'order_type'	=> $spool,
					'status' 		=> '6',
					'progress' 		=> '0',
					'created_by'  	=> $username,
					'created_date'  => $datetime
				);
				
				//Duplicate Bq Detail Header
				$getBq_DetailHeader = $this->db->query("SELECT * FROM so_bf_detail_header WHERE id_bq='".$id_bq."' ")->result_array();
				$ArrBqDetailHeader = array();
				foreach($getBq_DetailHeader AS $val => $valx){
					$ArrBqDetailHeader[$val]['id_milik'] = $valx['id'];
					$ArrBqDetailHeader[$val]['id_bq'] = $valx['id_bq'];
					$ArrBqDetailHeader[$val]['id_bq_header'] = $valx['id_bq_header'];
					$ArrBqDetailHeader[$val]['id_delivery'] = $valx['id_delivery'];
					$ArrBqDetailHeader[$val]['series'] = $valx['series'];
					$ArrBqDetailHeader[$val]['sub_delivery'] = $valx['sub_delivery'];
					$ArrBqDetailHeader[$val]['no_komponen'] = $valx['no_komponen'];
					$ArrBqDetailHeader[$val]['sts_delivery'] = $valx['sts_delivery'];
					$ArrBqDetailHeader[$val]['id_category'] = $valx['id_category'];
					$ArrBqDetailHeader[$val]['qty'] = $valx['qty'];
					$ArrBqDetailHeader[$val]['diameter_1'] = $valx['diameter_1'];
					$ArrBqDetailHeader[$val]['diameter_2'] = $valx['diameter_2'];
					$ArrBqDetailHeader[$val]['length'] = $valx['length'];
					$ArrBqDetailHeader[$val]['thickness'] = $valx['thickness'];
					$ArrBqDetailHeader[$val]['sudut'] = $valx['sudut'];
					$ArrBqDetailHeader[$val]['id_standard'] = $valx['id_standard'];
					$ArrBqDetailHeader[$val]['type'] = $valx['type'];
					$ArrBqDetailHeader[$val]['id_product'] = $valx['id_product'];

					$ArrBqDetailHeader[$val]['man_power'] = $valx['man_power'];
					$ArrBqDetailHeader[$val]['id_mesin'] = $valx['id_mesin'];
					$ArrBqDetailHeader[$val]['total_time'] = $valx['total_time'];
					$ArrBqDetailHeader[$val]['man_hours'] = $valx['man_hours'];

					$ArrBqDetailHeader[$val]['pe_direct_labour'] 			= $valx['pe_direct_labour'];
					$ArrBqDetailHeader[$val]['pe_indirect_labour'] 		= $valx['pe_indirect_labour'];
					$ArrBqDetailHeader[$val]['pe_machine'] 				= $valx['pe_machine'];
					$ArrBqDetailHeader[$val]['pe_mould_mandrill'] 			= $valx['pe_mould_mandrill'];
					$ArrBqDetailHeader[$val]['pe_consumable'] 				= $valx['pe_consumable'];
					$ArrBqDetailHeader[$val]['pe_foh_consumable'] 			= $valx['pe_foh_consumable'];
					$ArrBqDetailHeader[$val]['pe_foh_depresiasi'] 			= $valx['pe_foh_depresiasi'];
					$ArrBqDetailHeader[$val]['pe_biaya_gaji_non_produksi'] = $valx['pe_biaya_gaji_non_produksi'];
					$ArrBqDetailHeader[$val]['pe_biaya_non_produksi'] 		= $valx['pe_biaya_non_produksi'];
					$ArrBqDetailHeader[$val]['pe_biaya_rutin_bulanan'] 	= $valx['pe_biaya_rutin_bulanan'];
				}
				
				$ArrDetailSpool 		= array();
				foreach($getBq_DetailHeader AS $val => $valx){
					if($valx['qty'] > 0){
						$length_sudut 	= ($valx['id_category'] == 'pipe')?$valx['length']:$valx['sudut'];
						$sr_lr 			= ($valx['type'] == '0')?NULL:$valx['type'];
						$ArrDetailSpool[$val]['no_ipp'] 		= $getBq_Header[0]->no_ipp;
						$ArrDetailSpool[$val]['spool'] 			= $valx['id_delivery'];
						$ArrDetailSpool[$val]['id_spool'] 		= strtoupper($valx['no_komponen']);
						$ArrDetailSpool[$val]['id_product'] 	= strtolower($valx['id_category']);
						$ArrDetailSpool[$val]['nm_product'] 	= strtolower($valx['id_category']);
						$ArrDetailSpool[$val]['d1'] 			= str_replace(',','',$valx['diameter_1']);
						$ArrDetailSpool[$val]['d2'] 			= str_replace(',','',$valx['diameter_2']);
						$ArrDetailSpool[$val]['thickness'] 		= str_replace(',','',$valx['thickness']);
						$ArrDetailSpool[$val]['length_sudut']	= str_replace(',','',$length_sudut);
						$ArrDetailSpool[$val]['sr_lr'] 			= $sr_lr;
						$ArrDetailSpool[$val]['delivery_date'] 	= NULL;
						$ArrDetailSpool[$val]['keterangan'] 	= NULL;
						$ArrDetailSpool[$val]['created_by'] 	= $username;
						$ArrDetailSpool[$val]['created_date'] 	= $datetime;	
					}
				}
				
				//Duplicate Bq Detail Detail
				$getBq_DetailDetail = $this->db->query("SELECT * FROM so_bf_detail_detail WHERE id_bq='".$id_bq."' ")->result_array();
				$ArrBqDetailDetail = array();
				foreach($getBq_DetailDetail AS $val => $valx){
					$ArrBqDetailDetail[$val]['id_milik'] = $valx['id'];
					$ArrBqDetailDetail[$val]['id_bq'] = $valx['id_bq'];
					$ArrBqDetailDetail[$val]['id_bq_header'] = $valx['id_bq_header'];
					$ArrBqDetailDetail[$val]['id_delivery'] = $valx['id_delivery'];
					$ArrBqDetailDetail[$val]['series'] = $valx['series'];
					$ArrBqDetailDetail[$val]['sub_delivery'] = $valx['sub_delivery'];
					$ArrBqDetailDetail[$val]['sts_delivery'] = $valx['sts_delivery'];
					$ArrBqDetailDetail[$val]['id_category'] = $valx['id_category'];
					$ArrBqDetailDetail[$val]['diameter_1'] = $valx['diameter_1'];
					$ArrBqDetailDetail[$val]['diameter_2'] = $valx['diameter_2'];
					$ArrBqDetailDetail[$val]['length'] = $valx['length'];
					$ArrBqDetailDetail[$val]['thickness'] = $valx['thickness'];
					$ArrBqDetailDetail[$val]['sudut'] = $valx['sudut'];
					$ArrBqDetailDetail[$val]['id_standard'] = $valx['id_standard'];
					$ArrBqDetailDetail[$val]['type'] = $valx['type'];
					$ArrBqDetailDetail[$val]['qty'] = $valx['qty'];
					$ArrBqDetailDetail[$val]['product_ke'] = $valx['product_ke'];
				}
				
				$ArrMatNonFrp = array();
				$get_bq_nonfrp = $this->db->query("SELECT * FROM so_bf_acc_and_mat WHERE id_bq='".$id_bq."'")->result_array();
				if(!empty($get_bq_nonfrp)){
					foreach($get_bq_nonfrp AS $val => $valx){
						$ArrMatNonFrp[$val]['id_milik'] 	= $valx['id'];
						$ArrMatNonFrp[$val]['id_bq'] 		= $valx['id_bq'];
						$ArrMatNonFrp[$val]['category'] 	= $valx['category'];
						$ArrMatNonFrp[$val]['id_material'] 	= $valx['id_material'];
						$ArrMatNonFrp[$val]['id_material2'] 	= $valx['id_material2'];
						$ArrMatNonFrp[$val]['qty'] 			= $valx['qty'];
						$ArrMatNonFrp[$val]['satuan'] 		= $valx['satuan'];
						$ArrMatNonFrp[$val]['note'] 		= $valx['note'];
						$ArrMatNonFrp[$val]['unit_price'] 	= $valx['unit_price'];
						$ArrMatNonFrp[$val]['total_price'] 	= $valx['total_price'];
						$ArrMatNonFrp[$val]['updated_by'] 	= $data_session['ORI_User']['username'];
						$ArrMatNonFrp[$val]['updated_date'] = date('Y-m-d H:i:s');
						$ArrMatNonFrp[$val]['lebar'] 		= $valx['lebar'];
						$ArrMatNonFrp[$val]['panjang'] 		= $valx['panjang'];
						$ArrMatNonFrp[$val]['berat'] 		= $valx['berat'];
						$ArrMatNonFrp[$val]['sheet'] 		= $valx['sheet'];
					}
				}
			}
			
			// print_r($ArrDetalHd); 
			// exit;
			$this->db->trans_start();
				$this->db->where('no_ipp', $Imp[1]);
				$this->db->update('production', $Arr_Edit2);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('so_bf_header', $Arr_Edit);			
					
				if($this->input->post('status') == 'Y'){
					$this->db->insert('scheduling_produksi', $ArrSchedule);
					$this->db->insert('so_header', $ArrBqHeader);
					$this->db->insert_batch('so_detail_header', $ArrBqDetailHeader);
					$this->db->insert_batch('so_detail_detail', $ArrBqDetailDetail);

					$this->db->where('no_ipp', $Imp[1]);
					$this->db->update('monitoring_ipp', $ArrMonitoring);
					
					if(!empty($ArrMatNonFrp)){
						$this->db->insert_batch('so_acc_and_mat', $ArrMatNonFrp);
					}
					
					if(!empty($ArrDetailSpool)){
						$this->db->insert_batch('master_spool', $ArrDetailSpool);
					}
					
					history('Approved SO with BQ : '.$id_bq);
				}
				
				if($this->input->post('status') == 'N'){
					history('Reject Approved SO with BQ : '.$id_bq);	
				}
				
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Process failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Process success. Thanks ...',
					'status'	=> 1
				);				
				
			}
		}
		else{
			$Arr_Data	= array(
				'pesan'		=>'Data already final drawing. Please refresh page ...',
				'status'	=> 0
			);	
		}
		echo json_encode($Arr_Data);
	}
	
	function insert_sales_order_approve(){
		history('Try insert batch sales order approve');
		$sqlUpdate = "SELECT
					a.*,
					a.no_ipp,
					b.id_customer,
					b.nm_customer,
					b.project,
					b.ref_quo,
					b.status,
					b.sts_price_quo 
					FROM
						so_bf_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp WHERE b.status='WAITING APPROVE SO' ";
		$restUpdate = $this->db->query($sqlUpdate)->result_array();
		$ArrUpdate = array(); 
		foreach($restUpdate AS $val => $valx){
			
			$get_project_quo = $this->db
									->select('price_project')
									->order_by('insert_date','desc')
									->limit(1)
									->get_where('laporan_revised_header', array('id_bq'=>$valx['id_bq']))
									->result();
			$nilai_quo = (!empty($get_project_quo)) ? $get_project_quo[0]->price_project : 0;

			$get_so_deal = $this->db->select('total_deal_usd')->get_where('billing_so', array('no_ipp'=>$valx['no_ipp']))->result();
			$nilai_so = (!empty($get_so_deal)) ? $get_so_deal[0]->total_deal_usd : 0;
			
			$ArrUpdate[$val]['id_bq'] 			= $valx['id_bq'];
			$ArrUpdate[$val]['no_ipp'] 			= $valx['no_ipp'];
			$ArrUpdate[$val]['id_customer'] 	= $valx['id_customer'];
			$ArrUpdate[$val]['nm_customer'] 	= $valx['nm_customer'];
			$ArrUpdate[$val]['project'] 		= $valx['project'];
			$ArrUpdate[$val]['ref_quo'] 		= $valx['ref_quo'];
			// $ArrUpdate[$val]['sum_sales_order'] 	= SUM_SO_ALL_FAST($valx['id_bq']);
			// $ArrUpdate[$val]['sum_quotation'] 		= SUM_QUO_ALL_FAST($valx['id_bq']);
			// $ArrUpdate[$val]['sum_final_drawing'] 	= SUM_FD_ALL($valx['id_bq']);
			
			
			
			$ArrUpdate[$val]['sum_sales_order'] 	= $nilai_so;
			$ArrUpdate[$val]['sum_quotation'] 		= $nilai_quo;
			$ArrUpdate[$val]['sum_final_drawing'] 	= 0;
			$ArrUpdate[$val]['status'] 			= $valx['status'];
			$ArrUpdate[$val]['sts_price_quo'] 	= $valx['sts_price_quo'];
			$ArrUpdate[$val]['aju_approved'] 	= $valx['aju_approved'];
			$ArrUpdate[$val]['aju_approved_est'] 	= $valx['aju_approved_est'];
			$ArrUpdate[$val]['approved_est'] 	= $valx['approved_est'];
			$ArrUpdate[$val]['approved'] 		= $valx['approved'];
			$ArrUpdate[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrUpdate[$val]['create_date'] 	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
			$this->db->truncate('table_sales_order_approve');
			if(!empty($ArrUpdate)){
				$this->db->insert_batch('table_sales_order_approve', $ArrUpdate);
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);				
			history('Success insert batch sales order approve');
		}
		echo json_encode($Arr_Data);
		
	}
	
	public function get_data_json_so_app(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approve_so";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->quary_data_so_app(
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
			$nestedData[]	= "<div align='left'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".strtoupper(strtolower($row['ref_quo']))."</span></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['sum_sales_order'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['sum_quotation'],2)."</div>";
			$warna = Color_status($row['status']);
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['status']."</span></div>";
					$ApprvX	= "";
					$approve_new	= "";
					$Print	= "";
					
					// $viewX	= "&nbsp;<button class='btn btn-sm btn-warning detail_so' title='View Sales Order' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					$viewX2	= "&nbsp;<button class='btn btn-sm btn-info detail_quo' title='View Quotation' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					
					if($row['status'] == 'WAITING APPROVE SO'){
						if($Arr_Akses['approve']=='1'){
							// $ApprvX			= "&nbsp;<button class='btn btn-sm btn-success' id='ApproveDT' title='Approve Go Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
							$ApprvX	= "&nbsp;<button class='btn btn-sm btn-success approved_new' title='Approve Go Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						}
					}

					
					$num_so = $this->db->query("SELECT * FROM billing_so WHERE no_ipp = '".$row['no_ipp']."' ")->num_rows();
					if($num_so > 0){
						$Print	= "&nbsp;<a href='".base_url('sales_order/print_sales_order_usd/'.$row['no_ipp'])."' target='_blank' class='btn btn-sm btn-warning'  title='Print SO DEAL USD' ><i class='fa fa-print'></i></a>";
					}

					// <button class='btn btn-sm btn-primary' id='detailBQ'  title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
			$nestedData[]	= "<div align='left'>
									".$viewX2."
									".$ApprvX."
									".$Print."
									".$approve_new."
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

	public function quary_data_so_app($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				table_sales_order_approve a,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.aju_approved = 'Y' AND a.approved = 'N'
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer',
			3 => 'project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	public function deal_project(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			
			if(empty($data['detail_po'])){
				$Arr_Kembali	= array(
					'pesan'		=> 'TOP belum diisi. Digunakan untuk penagihan ...',
					'status'	=> 0
				);
				
				echo json_encode($Arr_Kembali);
				return false;
			}

			if(!empty($data['detail_product'])){
				$detail_product = $data['detail_product'];
			}

			if(!empty($data['detail_material'])){
				$detail_material = $data['detail_material'];
			}

			if(!empty($data['detail_aksesoris'])){
				$detail_aksesoris = $data['detail_aksesoris'];
			}

			if(!empty($data['detail_engine'])){
				$detail_engine = $data['detail_engine'];
			}

			if(!empty($data['detail_packing'])){
				$detail_packing = $data['detail_packing'];
			}

			if(!empty($data['detail_shipping'])){
				$detail_shipping = $data['detail_shipping'];
			}

			if(!empty($data['detail_other'])){
				$detail_other = $data['detail_other'];
			}

			// print_r($detail_material);
			// print_r($detail_aksesoris);
			// print_r($detail_engine);
			// print_r($detail_packing);
			// print_r($detail_shipping);
			// exit;

			$detail_total 	= $data['detail_total'];
			
			if(!empty($data['detail_po'])){
			$detail_po 		= $data['detail_po'];
			}

			if(!empty($data['detail_delivery'])){
			$detail_delivery= $data['detail_delivery'];
			}

			// print_r($data);
			// exit;
			
			$ArrHeader = array(
				'no_ipp' 			=> $data['no_ipp'],
				'no_po' 			=> strtoupper($data['no_po']),
				'tgl_po' 			=> $data['tgl_po'],
				'base_cur' 			=> $data['base_cur'],
				'project' 			=> strtoupper($data['project']),
				'kode_customer' 	=> get_name('production','id_customer','no_ipp',$data['no_ipp']),
				'nm_customer' 		=> get_name('production','nm_customer','no_ipp',$data['no_ipp']),
				'total_price' 		=> str_replace(',','',$data['sum_all_usd_awal']),
				'total_deal_usd' 	=> str_replace(',','',$data['sum_all_usd_val']),
				'total_deal_idr' 	=> str_replace(',','',$data['sum_all_idr_val']),
				'diskon' 			=> str_replace(',','',$data['diskon']),
				'kurs_usd_db' 		=> str_replace(',','',$data['kurs_usd_default']),
				'kurs_usd_dipakai' 	=> str_replace(',','',$data['kurs_usd']),
				'kurs_idr' 			=> str_replace(',','',$data['kurs_idr']),
				'catatan' 			=> strtolower($data['catatan']),
				'alamat_pengiriman' => strtolower($data['alamat_pengiriman']),
				'updated_by' 		=> $data_session['ORI_User']['username'],
				'updated_date' 		=> $dateTime
			);
			
			$ArrDetailProduct = array();
			if(!empty($data['detail_product'])){
				foreach($detail_product AS $val => $valx){
					$ArrDetailProduct[$val]['no_ipp'] 			= $data['no_ipp'];
					$ArrDetailProduct[$val]['id_milik'] 		= $valx['id'];
					$ArrDetailProduct[$val]['product'] 			= $valx['product'];
					$ArrDetailProduct[$val]['customer_item'] 	= strtolower($valx['customer_item']);
					$ArrDetailProduct[$val]['desc'] 			= strtolower($valx['desc']);
					$ArrDetailProduct[$val]['dim1'] 			= $valx['dim1'];
					$ArrDetailProduct[$val]['dim2'] 			= $valx['dim2'];
					$ArrDetailProduct[$val]['liner'] 			= $valx['liner'];
					$ArrDetailProduct[$val]['pressure'] 		= $valx['pressure'];
					$ArrDetailProduct[$val]['spec'] 			= $valx['spec'];
					$ArrDetailProduct[$val]['qty'] 				= $valx['qty'];
					$ArrDetailProduct[$val]['unit'] 			= $valx['unit'];
					$ArrDetailProduct[$val]['price_satuan'] 	= str_replace(',','',$valx['price_satuan']);
					$ArrDetailProduct[$val]['total_price'] 		= str_replace(',','',$valx['total_price']);
					$ArrDetailProduct[$val]['total_deal_usd'] 	= str_replace(',','',$valx['total_deal_usd']);
					$ArrDetailProduct[$val]['total_deal_idr'] 	= str_replace(',','',$valx['total_deal_idr']);
				}
			}

			$ArrDetailMaterial = array();
			if(!empty($data['detail_material'])){
			foreach($detail_material AS $val => $valx){
				$dataX = $this->db->query("SELECT * FROM so_bf_acc_and_mat WHERE id='".$valx['id']."'")->result();
				$ArrDetailMaterial[$val]['no_ipp'] 			= $data['no_ipp'];
				$ArrDetailMaterial[$val]['id_milik'] 		= $valx['id'];
				$ArrDetailMaterial[$val]['category'] 		= 'mat';
				$ArrDetailMaterial[$val]['id_material'] 	= $dataX[0]->id_material;
				$ArrDetailMaterial[$val]['nm_material'] 	= get_name('raw_materials','nm_material','id_material',$dataX[0]->id_material);
				$ArrDetailMaterial[$val]['spec'] 			= NULL;
				$ArrDetailMaterial[$val]['qty'] 			= $dataX[0]->qty;
				$ArrDetailMaterial[$val]['satuan'] 			= strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$dataX[0]->satuan));
				$ArrDetailMaterial[$val]['unit_price'] 	= str_replace(',','',$valx['price_satuan']);
				$ArrDetailMaterial[$val]['total_price'] 	= str_replace(',','',$valx['total_price']);
				$ArrDetailMaterial[$val]['total_deal_usd'] 	= str_replace(',','',$valx['total_deal_usd']);
				$ArrDetailMaterial[$val]['total_deal_idr'] 	= str_replace(',','',$valx['total_deal_idr']);
				$ArrDetailMaterial[$val]['customer_item'] 	= strtolower($valx['customer_item']);
				$ArrDetailMaterial[$val]['desc'] 			= strtolower($valx['desc']);				
			}
			}

			$ArrDetailAksesoris = array();
			if(!empty($data['detail_aksesoris'])){
			foreach($detail_aksesoris AS $val => $valx){
				$dataX = $this->db->query("SELECT * FROM so_bf_acc_and_mat WHERE id='".$valx['id']."'")->result();
				
				$qty = $dataX[0]->qty;
				$satuan = $dataX[0]->satuan;
				if($valx['category'] == 'plate'){
					$qty = $dataX[0]->berat;
					$satuan = '1';
				}
			
				$ArrDetailAksesoris[$val]['no_ipp'] 		= $data['no_ipp'];
				$ArrDetailAksesoris[$val]['id_milik'] 		= $valx['id'];
				$ArrDetailAksesoris[$val]['category'] 		= $valx['category'];
				$ArrDetailAksesoris[$val]['desc'] 			= $valx['desc'];
				$ArrDetailAksesoris[$val]['id_material'] 	= $dataX[0]->id_material;
				$ArrDetailAksesoris[$val]['nm_material'] 	= get_name('con_nonmat_new','material_name','code_group',$dataX[0]->id_material);
				$ArrDetailAksesoris[$val]['spec'] 			= get_name('con_nonmat_new','spec','code_group',$dataX[0]->id_material);
				$ArrDetailAksesoris[$val]['qty'] 			= $qty;
				$ArrDetailAksesoris[$val]['satuan'] 		= strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$satuan));
				$ArrDetailAksesoris[$val]['unit_price'] 	= str_replace(',','',$valx['price_satuan']);
				$ArrDetailAksesoris[$val]['total_price'] 	= str_replace(',','',$valx['total_price']);
				$ArrDetailAksesoris[$val]['total_deal_usd'] = str_replace(',','',$valx['total_deal_usd']);
				$ArrDetailAksesoris[$val]['total_deal_idr'] = str_replace(',','',$valx['total_deal_idr']);
			}
			}

			$ArrDetailEngine = array();
			if(!empty($data['detail_engine'])){
			foreach($detail_engine AS $val => $valx){
				$ArrDetailEngine[$val]['no_ipp'] 		= $data['no_ipp'];
				$ArrDetailEngine[$val]['id_milik'] 		= $valx['id'];
				$ArrDetailEngine[$val]['category'] 		= 'eng';
				$ArrDetailEngine[$val]['qty'] 			= $valx['qty'];
				$ArrDetailEngine[$val]['satuan'] 		= strtolower($valx['satuan']);
				$ArrDetailEngine[$val]['unit_price'] 	= str_replace(',','',$valx['price_satuan']);
				$ArrDetailEngine[$val]['total_price'] 	= str_replace(',','',$valx['total_price']);
				$ArrDetailEngine[$val]['total_deal_usd'] = str_replace(',','',$valx['total_deal_usd']);
				$ArrDetailEngine[$val]['total_deal_idr'] = str_replace(',','',$valx['total_deal_idr']);
			}
			}

			$ArrDetailPacking = array();
			if(!empty($data['detail_packing'])){
			foreach($detail_packing AS $val => $valx){
				$ArrDetailPacking[$val]['no_ipp'] 		= $data['no_ipp'];
				$ArrDetailPacking[$val]['id_milik'] 	= $valx['id'];
				$ArrDetailPacking[$val]['desc'] 		= $valx['desc'];
				$ArrDetailPacking[$val]['category'] 	= 'pack';
				$ArrDetailPacking[$val]['satuan'] 		= strtolower($valx['satuan']);
				$ArrDetailPacking[$val]['total_price'] 	= str_replace(',','',$valx['total_price']);
				$ArrDetailPacking[$val]['total_deal_usd'] = str_replace(',','',$valx['total_deal_usd']);
				$ArrDetailPacking[$val]['total_deal_idr'] = str_replace(',','',$valx['total_deal_idr']);
			}
			}

			$ArrDetailShipping = array();
			if(!empty($data['detail_shipping'])){
			foreach($detail_shipping AS $val => $valx){
				$ArrDetailShipping[$val]['no_ipp'] 		= $data['no_ipp'];
				$ArrDetailShipping[$val]['id_milik'] 	= $valx['id'];
				$ArrDetailShipping[$val]['desc'] 		= $valx['desc'];
				$ArrDetailShipping[$val]['category'] 	= 'ship';
				$ArrDetailShipping[$val]['qty'] 		= $valx['qty'];
				$ArrDetailShipping[$val]['unit_price'] 	= str_replace(',','',$valx['price_satuan']);
				$ArrDetailShipping[$val]['total_price'] 	= str_replace(',','',$valx['total_price']);
				$ArrDetailShipping[$val]['total_deal_usd'] 	= str_replace(',','',$valx['total_deal_usd']);
				$ArrDetailShipping[$val]['total_deal_idr'] 	= str_replace(',','',$valx['total_deal_idr']);
			}
			}

			$ArrDetailOther = array();
			if(!empty($data['detail_other'])){
			foreach($data['detail_other'] AS $val => $valx){
				$ArrDetailOther[$val]['no_ipp'] 		= $data['no_ipp'];
				$ArrDetailOther[$val]['id_milik'] 		= $valx['id'];
				$ArrDetailOther[$val]['desc'] 			= $valx['desc'];
				$ArrDetailOther[$val]['category'] 		= 'other';
				$ArrDetailOther[$val]['qty'] 			= str_replace(',','',$valx['qty']);
				$ArrDetailOther[$val]['unit_price'] 	= str_replace(',','',$valx['price_satuan']);
				$ArrDetailOther[$val]['total_price'] 	= str_replace(',','',$valx['total_price']);
				$ArrDetailOther[$val]['total_deal_usd'] = str_replace(',','',$valx['total_deal_usd']);
				$ArrDetailOther[$val]['total_deal_idr'] = str_replace(',','',$valx['total_deal_idr']);
			}
			}
			
			$ArrDetailTotal = array();
			foreach($detail_total AS $val => $valx){
				$ArrDetailTotal[$val]['no_ipp'] 		= $data['no_ipp'];
				$ArrDetailTotal[$val]['product_awal'] 	= str_replace(',','',$valx['product_usd_awal']);
				$ArrDetailTotal[$val]['product_usd'] 	= str_replace(',','',$valx['product_usd']);
				$ArrDetailTotal[$val]['product_idr'] 	= str_replace(',','',$valx['product_idr']);
				$ArrDetailTotal[$val]['eng_awal'] 		= str_replace(',','',$valx['eng_usd_awal']);
				$ArrDetailTotal[$val]['eng_usd'] 		= str_replace(',','',$valx['eng_usd']);
				$ArrDetailTotal[$val]['eng_idr'] 		= str_replace(',','',$valx['eng_idr']);
				$ArrDetailTotal[$val]['pack_awal'] 		= str_replace(',','',$valx['pack_usd_awal']);
				$ArrDetailTotal[$val]['pack_usd'] 		= str_replace(',','',$valx['pack_usd']);
				$ArrDetailTotal[$val]['pack_idr'] 		= str_replace(',','',$valx['pack_idr']);
				$ArrDetailTotal[$val]['ship_awal'] 		= str_replace(',','',$valx['ship_usd_awal']);
				$ArrDetailTotal[$val]['ship_usd'] 		= str_replace(',','',$valx['ship_usd']);
				$ArrDetailTotal[$val]['ship_idr'] 		= str_replace(',','',$valx['ship_idr']);
				$ArrDetailTotal[$val]['mat_awal'] 		= str_replace(',','',$valx['material_usd_awal']);
				$ArrDetailTotal[$val]['mat_usd'] 		= str_replace(',','',$valx['material_usd']);
				$ArrDetailTotal[$val]['mat_idr'] 		= str_replace(',','',$valx['material_idr']);
				$ArrDetailTotal[$val]['acc_awal'] 		= str_replace(',','',$valx['acc_usd_awal']);
				$ArrDetailTotal[$val]['acc_usd'] 		= str_replace(',','',$valx['acc_usd']);
				$ArrDetailTotal[$val]['acc_idr'] 		= str_replace(',','',$valx['acc_idr']);
			}
			
			$ArrDetailTOP = array();
			if(!empty($data['detail_po'])){
				$term = 0;
				foreach($detail_po AS $val => $valx){ $term++;
					if($valx['group_top'] <> $valx['group_top']){
						$term = 1;
					}
					$ArrDetailTOP[$val]['no_po'] 			= $data['no_ipp'];
					$ArrDetailTOP[$val]['category'] 		= 'penjualan';
					$ArrDetailTOP[$val]['group_top'] 		= $valx['group_top'];
					$ArrDetailTOP[$val]['term'] 			= $term;
					$ArrDetailTOP[$val]['progress'] 		= str_replace(',','',$valx['progress']);
					$ArrDetailTOP[$val]['value_usd'] 		= str_replace(',','',$valx['value_usd']);
					$ArrDetailTOP[$val]['value_idr'] 		= str_replace(',','',$valx['value_idr']);
					$ArrDetailTOP[$val]['keterangan'] 		= strtolower($valx['keterangan']);
					$ArrDetailTOP[$val]['jatuh_tempo'] 		= $valx['jatuh_tempo'];
					$ArrDetailTOP[$val]['syarat'] 			= strtolower($valx['syarat']);
					$ArrDetailTOP[$val]['created_by'] 		= $data_session['ORI_User']['username'];
					$ArrDetailTOP[$val]['created_date'] 	= $dateTime;
				}
			}
			
			$ArrDeliveryDate = array();
			if(!empty($data['detail_delivery'])){
				foreach($detail_delivery AS $val => $valx){
					foreach($valx['detail'] AS $val2 => $valx2){
						$ArrDeliveryDate[$val.$val2]['no_ipp'] 			= $data['no_ipp'];
						$ArrDeliveryDate[$val.$val2]['id_milik'] 		= $valx['id'];
						$ArrDeliveryDate[$val.$val2]['qty_delivery'] 	= str_replace(',','',$valx2['qty_delivery']);
						$ArrDeliveryDate[$val.$val2]['delivery_date'] 	= (!empty($valx2['delivery_date']))?$valx2['delivery_date']:NULL;
						$ArrDeliveryDate[$val.$val2]['created_by'] 		= $data_session['ORI_User']['username'];
						$ArrDeliveryDate[$val.$val2]['created_date'] 	= $dateTime;
					}
				}
			}
			
			// print_r($ArrHeader);
			// print_r($ArrDetailProduct);
			// print_r($ArrDetailMaterial);
			// print_r($ArrDetailAksesoris);
			// print_r($ArrDetailEngine);
			// print_r($ArrDetailPacking);
			// print_r($ArrDetailShipping);
			// print_r($ArrDetailTotal);
			// print_r($ArrDetailTOP);
			// print_r($ArrDeliveryDate);
			// print_r($ArrDetailOther);
			// exit;
			$this->db->trans_start();
			$billing_so	= $this->db->query("select * from billing_so where no_ipp='".$data['no_ipp']."'")->row();
			if(empty($billing_so)){
				$this->db->delete('billing_so', array('no_ipp' => $data['no_ipp']));
				$this->db->insert('billing_so', $ArrHeader);
			}else{
				if($billing_so->status>0){
					// batalkan karena sudah ada invoice
					$Arr_Kembali	= array(
						'pesan'		=> 'Edit Gagal, Karena sudah ada invoice atas SO ini ...',
						'status'	=> 0
					);
					echo json_encode($Arr_Kembali);
					die();
				}
				$this->db->where('no_ipp', $data['no_ipp']);
				$this->db->update('billing_so', $ArrHeader);
			}
				$this->db->delete('billing_so_product', array('no_ipp' => $data['no_ipp']));
				$this->db->delete('billing_so_add', array('no_ipp' => $data['no_ipp']));
				$this->db->delete('billing_so_total', array('no_ipp' => $data['no_ipp']));
				$this->db->delete('billing_top', array('no_po' => $data['no_ipp'],'category'=>'penjualan'));
				$this->db->delete('scheduling_master', array('no_ipp' => $data['no_ipp']));
				
				if(!empty($ArrHeader)){
				}
				if(!empty($ArrDetailProduct)){
					$this->db->insert_batch('billing_so_product', $ArrDetailProduct);
				}
				if(!empty($ArrDetailMaterial)){
					$this->db->insert_batch('billing_so_add', $ArrDetailMaterial);
				}
				if(!empty($ArrDetailAksesoris)){
					$this->db->insert_batch('billing_so_add', $ArrDetailAksesoris);
				}
				if(!empty($ArrDetailEngine)){
					$this->db->insert_batch('billing_so_add', $ArrDetailEngine);
				}
				if(!empty($ArrDetailPacking)){
					$this->db->insert_batch('billing_so_add', $ArrDetailPacking);
				}
				if(!empty($ArrDetailShipping)){
					$this->db->insert_batch('billing_so_add', $ArrDetailShipping);
				}
				if(!empty($ArrDetailOther)){
					$this->db->insert_batch('billing_so_add', $ArrDetailOther);
				}
				if(!empty($ArrDetailTotal)){
					$this->db->insert_batch('billing_so_total', $ArrDetailTotal);
				}
				if(!empty($ArrDetailTOP)){
					$this->db->insert_batch('billing_top', $ArrDetailTOP);
				}
				if(!empty($ArrDeliveryDate)){
					$this->db->insert_batch('scheduling_master', $ArrDeliveryDate);
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
				history('Create deal SO & TOP '.$data['no_ipp']);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$id_bq	= $this->uri->segment(3);
			$getEx	= explode('-', $id_bq);
			$ipp	= $getEx[1];

			$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
			$row		= $this->db->query($qSupplier)->result();
			
			$product 	= "	SELECT
								a.id_bq,
								a.id_milik,
								a.id_category,
								a.qty,
								a.diameter_1,
								a.diameter_2,
								a.series,
								a.id_product,
								(b.price_total / c.qty) * a.qty AS cost,
								d.total_deal_usd AS deal_usd
							FROM
								so_bf_detail_header a 
									LEFT JOIN cost_project_detail b ON a.id_milik=b.caregory_sub
									LEFT JOIN bq_detail_header c ON a.id_milik=c.id
									LEFT JOIN billing_so_product d ON a.id_milik=d.id_milik
							WHERE
								a.id_bq = '".$id_bq."'";			
			$rest_product	= $this->db->query($product)->result_array();
			// echo $product;
			$eng 		= "SELECT SUM(b.price_total) AS total_price FROM cost_project_detail b WHERE b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y'";
			$sum_eng	= $this->db->query($eng)->result();

			$eng 		= "SELECT SUM(b.price_total) AS total_price FROM cost_project_detail b WHERE b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ";
			$sum_pack	= $this->db->query($eng)->result();
			// echo $engCPC;
			$ship 		= "SELECT SUM(b.price_total) AS total_price FROM cost_project_detail b WHERE (b.category = 'export' OR b.category = 'lokal') AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0";
			$sum_ship	= $this->db->query($ship)->result();
			
			$sql_non_frp 	= "SELECT a.*, SUM(a.price_total) AS total_price FROM cost_project_detail a WHERE a.category='nonfrp' AND a.id_bq='".$id_bq."'";
			$sum_non_frp	= $this->db->query($sql_non_frp)->result_array();
			
			$sql_material 	= "SELECT a.*, SUM(a.price_total) AS total_price FROM cost_project_detail a WHERE a.category='aksesoris' AND a.id_bq='".$id_bq."'";
			$sum_material	= $this->db->query($sql_material)->result_array();

			$sql_non_frp2 	= "	SELECT 
									a.*,
									b.id AS id_milik2,
									b.qty AS qty_so
								FROM cost_project_detail a 
									LEFT JOIN so_bf_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id_milik
								WHERE 
									(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
									AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."'";
			$data_non_frp2	= $this->db->query($sql_non_frp2)->result_array();
			
			$sql_material2 	= "	SELECT 
									a.*,
									b.id AS id_milik,
									b.qty AS qty_so
								FROM cost_project_detail a 
									LEFT JOIN so_bf_acc_and_mat b ON a.caregory_sub = b.id_material AND a.id_milik = b.id_milik
								WHERE 
									b.category = 'mat'
									AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ORDER BY a.caregory_sub ASC";
			$data_material2	= $this->db->query($sql_material2)->result_array();
			
			$sql_eng 	= "SELECT b.* FROM cost_project_detail b WHERE b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND sts_so = 'Y'";
			$data_eng	= $this->db->query($sql_eng)->result_array();

			$sql_eng 	= "SELECT b.* FROM cost_project_detail b WHERE b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 AND sts_so = 'Y' ";
			$data_pack	= $this->db->query($sql_eng)->result_array();
			
			$sql_ship 	= "(SELECT b.* FROM cost_project_detail b WHERE b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 AND sts_so = 'Y')
							UNION
							( SELECT b.* FROM cost_project_detail b WHERE b.category = 'lokal' AND b.id_bq = '".$id_bq."' AND b.price_total != 0  AND sts_so = 'Y')
							";
			$data_ship	= $this->db->query($sql_ship)->result_array();

			$SQL_Other 	= "SELECT b.* FROM cost_project_detail b WHERE b.category = 'other' AND b.id_bq='".$id_bq."' AND b.price_total > 0 ";
			$dataOther	= $this->db->query($SQL_Other)->result_array();

			$payment = $this->db->query("SELECT name FROM list_help WHERE group_by = 'top'")->result_array();

			$qSO 		= "SELECT * FROM billing_so WHERE no_ipp = '".$ipp."' ";
			$restSO		= $this->db->query($qSO)->result();

			$data_top = $this->db->query("SELECT * FROM billing_top WHERE category = 'penjualan' AND no_po = '".$ipp."' ")->result_array();
			
			$non_frp_delivery = $this->db->order_by('category','asc')->get_where('so_bf_acc_and_mat', array('id_bq'=>$id_bq))->result_array();
			$data = array(
				'title'			=> 'Deal Project',
				'action'		=> 'deal',
				'getHeader'		=> $row,
				'id_bq' 		=> $id_bq,
				'ipp' 			=> $ipp,
				'payment' 		=> $payment,
				'product' 		=> $rest_product,
				'restSO' 		=> $restSO,
				'data_top' 		=> $data_top,
				'data_non_frp' 	=> $data_non_frp2,
				'data_material' => $data_material2,
				'data_eng' 		=> $data_eng,
				'data_pack' 	=> $data_pack,
				'data_ship' 	=> $data_ship,
				'data_other' 	=> $dataOther,
				'non_frp_delivery' 	=> $non_frp_delivery,
				'sum_eng' 		=> (!empty($sum_eng[0]->total_price))?$sum_eng[0]->total_price:0,
				'sum_pack' 		=> (!empty($sum_pack[0]->total_price))?$sum_pack[0]->total_price:0,
				'sum_ship' 		=> (!empty($sum_ship[0]->total_price))?$sum_ship[0]->total_price:0,
				'sum_non_frp'	=> (!empty($sum_non_frp[0]['total_price']))?$sum_non_frp[0]['total_price']:0,
				'sum_material'	=> (!empty($sum_material[0]['total_price']))?$sum_material[0]['total_price']:0
			);
			
			$this->load->view('Sales_order/deal_project',$data);
		}
		
	}
	
	public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;
		
		$payment = $this->db->query("SELECT name FROM list_help WHERE group_by = 'top'")->result_array();

		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
			$d_Header .= "<select name='detail_po[".$id."][group_top]' class='form-control text-left chosen_select' value='".$id."'>";
				// $d_Header .= "<option value='0'>Select Group TOP</option>";
				foreach($payment AS $val => $valx){
					$selx = ($valx['name'] == 'progress')?'selected':'';
					$d_Header .= "<option value='".$valx['name']."' ".$selx.">".strtoupper($valx['name'])."</option>";
				}
			$d_Header .= "</select>";
			$d_Header .= "</td>";
			// $d_Header .= "<td align='left'><input type='text' name='detail_po[".$id."][term]' class='form-control text-center input-md' value='".$id."'></td>";
			$d_Header .= "<td align='left'><input type='text' id='progress_".$id."' name='detail_po[".$id."][progress]' class='form-control input-md text-center maskM progress_term' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			$d_Header .= "<td align='left'><input type='text' id='usd_".$id."' name='detail_po[".$id."][value_usd]' class='form-control input-md text-right maskM sum_tot_usd' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' readonly></td>";
			$d_Header .= "<td align='left'><input type='text' id='idr_".$id."' name='detail_po[".$id."][value_idr]' class='form-control input-md text-right maskM sum_tot_idr' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' readonly></td>";
			$d_Header .= "<td align='left'><input type='text' id='total_harga_".$id."' name='detail_po[".$id."][keterangan]' class='form-control input-md text-left'></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail_po[".$id."][jatuh_tempo]' class='form-control input-md text-center datepicker' readonly></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail_po[".$id."][syarat]' class='form-control input-md'></td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";


		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left'><button type='button' class='btn btn-sm btn-warning addPart' title='Add TOP'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add TOP</button></td>";
			$d_Header .= "<td align='center' colspan='7'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}
	
	public function print_sales_order(){
		$no_ipp     = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$sql_iden		= "SELECT a.* FROM identitas a LIMIT 1";
		$data_iden		= $this->db->query($sql_iden)->result_array();
		
		$sql_header		= "SELECT a.* FROM billing_so a WHERE no_ipp='".$no_ipp."' LIMIT 1";
		$data_header	= $this->db->query($sql_header)->result_array();

		$WHERE_CUSTOM = "AND product <> 'product kosong'";
		if($no_ipp == 'IPP230483L'){
			$WHERE_CUSTOM = "";
		}
		
		$sql_product	= "SELECT a.* FROM billing_so_product a WHERE no_ipp='".$no_ipp."' ".$WHERE_CUSTOM;
		$data_product	= $this->db->query($sql_product)->result_array();
		
		$sql_total		= "SELECT a.* FROM billing_so_total a WHERE no_ipp='".$no_ipp."' LIMIT 1";
		$data_total		= $this->db->query($sql_total)->result_array();
		
		$sql_top		= "SELECT a.* FROM billing_top a WHERE no_po='".$no_ipp."' AND a.category='penjualan'";
		$data_top		= $this->db->query($sql_top)->result_array();
		
		$sql_material	= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND a.category='mat'";
		$data_material	= $this->db->query($sql_material)->result_array();
		
		$sql_nonfrp		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='acc' OR a.category='baut' OR a.category='plate' OR a.category='gasket' OR a.category='lainnya')";
		$data_nonfrp	= $this->db->query($sql_nonfrp)->result_array();
		
		$sql_shipping		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='ship')";
		$data_shipping	= $this->db->query($sql_shipping)->result_array();
		
		$min_delivery = $this->db->select('MIN(delivery_date) AS delivery')->get_where('scheduling_master', array('no_ipp'=>$no_ipp))->result();
		$delivery_d = (!empty($min_delivery))?$min_delivery[0]->delivery:'belum di set';

		$sql_nonfrp_deli		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='mat' OR a.category='baut' OR a.category='plate' OR a.category='gasket' OR a.category='lainnya')";
		$data_nonfrp_delivery	= $this->db->query($sql_nonfrp_deli)->result_array();

		$sql_packing		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='pack')";
		$data_packing	= $this->db->query($sql_packing)->result_array();

		$data_other = $this->db->get_where('billing_so_add',array('no_ipp'=>$no_ipp,'category'=>'other'))->result_array();

		$data = array(
			'data_nonfrp_delivery' => $data_nonfrp_delivery,
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'no_ipp' => $no_ipp,
			'data_iden' => $data_iden,
			'data_header' => $data_header,
			'data_product' => $data_product,
			'data_total' => $data_total,
			'data_top' => $data_top,
			'data_material' => $data_material,
			'data_nonfrp' => $data_nonfrp,
			'date_delivery' => $delivery_d,
			'data_packing' => $data_packing,
			'data_other' => $data_other,
			'data_shipping' => $data_shipping
		);
		
		history('Print Sales Order '.$no_ipp);
		$this->load->view('Print/print_sales_order', $data);
	}
	
	public function print_sales_order_ex_price(){
		$no_ipp     = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$sql_iden		= "SELECT a.* FROM identitas a LIMIT 1";
		$data_iden		= $this->db->query($sql_iden)->result_array();
		
		$sql_header		= "SELECT a.* FROM billing_so a WHERE no_ipp='".$no_ipp."' LIMIT 1";
		$data_header	= $this->db->query($sql_header)->result_array();

		$WHERE_CUSTOM = "AND product <> 'product kosong'";
		if($no_ipp == 'IPP230483L'){
			$WHERE_CUSTOM = "";
		}
		
		$sql_product	= "SELECT a.* FROM billing_so_product a WHERE no_ipp='".$no_ipp."' ".$WHERE_CUSTOM;// AND total_deal_usd > 0 
		$data_product	= $this->db->query($sql_product)->result_array();
		
		$sql_total		= "SELECT a.* FROM billing_so_total a WHERE no_ipp='".$no_ipp."' LIMIT 1";
		$data_total		= $this->db->query($sql_total)->result_array();
		
		$sql_top		= "SELECT a.* FROM billing_top a WHERE no_po='".$no_ipp."' AND a.category='penjualan'";
		$data_top		= $this->db->query($sql_top)->result_array();
		
		$sql_material	= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND a.category='mat'";
		$data_material	= $this->db->query($sql_material)->result_array();
		
		$sql_shipping		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='ship')";
		$data_shipping	= $this->db->query($sql_shipping)->result_array();
		
		$sql_nonfrp	= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='acc' OR a.category='baut' OR a.category='plate' OR a.category='gasket' OR a.category='lainnya')";
		$data_nonfrp	= $this->db->query($sql_nonfrp)->result_array();
		// echo $sql_nonfrp;
		$min_delivery = $this->db->select('MIN(delivery_date) AS delivery')->get_where('scheduling_master', array('no_ipp'=>$no_ipp))->result();
		$delivery_d = (!empty($min_delivery))?$min_delivery[0]->delivery:'belum di set';
		
		$sql_nonfrp_deli		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='mat' OR a.category='baut' OR a.category='plate' OR a.category='gasket' OR a.category='lainnya')";
		$data_nonfrp_delivery	= $this->db->query($sql_nonfrp_deli)->result_array();

		$sql_packing		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='pack')";
		$data_packing	= $this->db->query($sql_packing)->result_array();

		$data_other = $this->db->get_where('billing_so_add',array('no_ipp'=>$no_ipp,'category'=>'other'))->result_array();

		$data = array(
			'data_nonfrp_delivery' => $data_nonfrp_delivery,
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'no_ipp' => $no_ipp,
			'data_other' => $data_other,
			'data_packing' => $data_packing,
			'data_iden' => $data_iden,
			'data_header' => $data_header,
			'data_product' => $data_product,
			'data_total' => $data_total,
			'data_top' => $data_top,
			'data_material' => $data_material,
			'data_nonfrp' => $data_nonfrp,
			'data_shipping' => $data_shipping,
			'date_delivery' => $delivery_d
		);
		
		history('Print Sales Order Tanpa Harga '.$no_ipp);
		$this->load->view('Print/print_sales_order_ex_price', $data);
	}
	
	public function print_sales_order_usd(){
		$no_ipp     = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$sql_iden		= "SELECT a.* FROM identitas a LIMIT 1";
		$data_iden		= $this->db->query($sql_iden)->result_array();
		
		$sql_header		= "SELECT a.* FROM billing_so a WHERE no_ipp='".$no_ipp."' LIMIT 1";
		$data_header	= $this->db->query($sql_header)->result_array();

		$WHERE_CUSTOM = "AND product <> 'product kosong'";
		if($no_ipp == 'IPP230483L'){
			$WHERE_CUSTOM = "";
		}
		
		$sql_product	= "SELECT a.* FROM billing_so_product a WHERE no_ipp='".$no_ipp."' ".$WHERE_CUSTOM;
		$data_product	= $this->db->query($sql_product)->result_array();
		
		$sql_total		= "SELECT a.* FROM billing_so_total a WHERE no_ipp='".$no_ipp."' LIMIT 1";
		$data_total		= $this->db->query($sql_total)->result_array();
		
		$sql_top		= "SELECT a.* FROM billing_top a WHERE no_po='".$no_ipp."' AND a.category='penjualan'";
		$data_top		= $this->db->query($sql_top)->result_array();
		
		$sql_material	= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND a.category='mat'";
		$data_material	= $this->db->query($sql_material)->result_array();
		
		$sql_nonfrp	= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='acc' OR a.category='baut' OR a.category='plate' OR a.category='gasket' OR a.category='lainnya')";
		$data_nonfrp	= $this->db->query($sql_nonfrp)->result_array();
		
		$sql_shipping		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='ship')";
		$data_shipping	= $this->db->query($sql_shipping)->result_array();
		
		$min_delivery = $this->db->select('MIN(delivery_date) AS delivery')->get_where('scheduling_master', array('no_ipp'=>$no_ipp))->result();
		$delivery_d = (!empty($min_delivery))?$min_delivery[0]->delivery:'belum di set';
		
		$sql_nonfrp_deli		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='mat' OR a.category='baut' OR a.category='plate' OR a.category='gasket' OR a.category='lainnya')";
		$data_nonfrp_delivery	= $this->db->query($sql_nonfrp_deli)->result_array();

		$sql_packing		= "SELECT a.* FROM billing_so_add a WHERE no_ipp='".$no_ipp."' AND (a.category='pack')";
		$data_packing	= $this->db->query($sql_packing)->result_array();

		$data_other = $this->db->get_where('billing_so_add',array('no_ipp'=>$no_ipp,'category'=>'other'))->result_array();

		$data = array(
			'data_nonfrp_delivery' => $data_nonfrp_delivery,
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'no_ipp' => $no_ipp,
			'data_iden' => $data_iden,
			'data_header' => $data_header,
			'data_product' => $data_product,
			'data_total' => $data_total,
			'data_top' => $data_top,
			'data_material' => $data_material,
			'data_nonfrp' => $data_nonfrp,
			'date_delivery' => $delivery_d,
			'data_packing' => $data_packing,
			'data_other' => $data_other,
			'data_shipping' => $data_shipping
		);
		
		history('Print Sales Order USD '.$no_ipp);
		$this->load->view('Print/print_sales_order_usd', $data);
	}
	
	//DEPARTMENT
	public function get_json_sales_invoice(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_sales_invoice(
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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['so_number']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['name_customer']))."</div>";
			$nestedData[]	= "<div align='left'>".number_format($row['nilai'],2)."</div>";
			$value = "Active";
			$color = "bg-green";
			if($row['status'] == 'N'){
				$value = "Not Active";
				$color = "bg-red";
			}
			$nestedData[]	= "<div align='center'><span class='badge ".$color." '>".$value."</span></div>";

			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($last_date))."</div>";

                $detail		= "";
                $edit		= "";
                $delete		= "";

                if($Arr_Akses['delete']=='1'){
                    $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id']."'><i class='fa fa-trash'></i></button>";
                }

                if($Arr_Akses['update']=='1'){
                    $edit	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['id']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button>";
                }
                $nestedData[]	= "<div align='left'>
                                    ".$edit."
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

	public function get_query_json_sales_invoice($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                sales_invoice a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.updated_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.name_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'so_number'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }
	
}