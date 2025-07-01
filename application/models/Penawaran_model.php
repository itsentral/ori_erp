<?php
class Penawaran_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->sroot = $_SERVER['DOCUMENT_ROOT'];
		// $this->sroot = $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
	}
	
	public function add_penawaran(){
		$id_bq		= $this->uri->segment(3);
		$getEx		= explode('-', $id_bq);
		$ipp		= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		$qMatr 		= SQL_Quo($id_bq);
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT * FROM cost_engine ORDER BY id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		
		$engCPC 	= "SELECT * FROM list_help WHERE group_by = 'pack cost' ORDER BY id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		$gTruck 	= "SELECT
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') AND c.deleted='N')  as price
						FROM
							list_shipping a
						WHERE
							a.flag = 'Y' 
						ORDER BY
							a.urut ASC";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$gTruckP 	= "SELECT * FROM list_packing WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruckP	= $this->db->query($gTruckP)->result_array();
		
		$engCPCV 	= "SELECT * FROM list_help WHERE group_by = 'via' ORDER BY urut	 ASC ";
		$rowengCPCV	= $this->db->query($engCPCV)->result_array();
		
		$qOpt 		= "SELECT * FROM list_help WHERE group_by = 'opt' ORDER BY id DESC ";
		$getOpt		= $this->db->query($qOpt)->result_array();
		
		$qOptPl 	= "SELECT * FROM list_help WHERE group_by = 'opt' OR group_by = 'opt plus' ORDER BY id DESC ";
		$getOptPl	= $this->db->query($qOptPl)->result_array();
		
		$qArea		= "SELECT area FROM cost_trucking WHERE category='darat' GROUP BY area ORDER BY area ASC ";
		$getArea	= $this->db->query($qArea)->result_array();
		
		$qAreaL		= "SELECT area FROM cost_trucking WHERE category='laut' GROUP BY area ORDER BY area ASC ";
		$getAreaL	= $this->db->query($qAreaL)->result_array();
		
		$sql_non_frp	= "SELECT * FROM bq_acc_and_mat WHERE category='acc' AND id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material	= "SELECT * FROM bq_acc_and_mat WHERE category='mat' AND id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		
		$data = array(
			'title'			=> 'Offer Structure New',
			'action'		=> 'index',
			'getHeader'		=> $row,
			'getDetail'		=> $rowDet,
			'getEngCost'	=> $rowengC,
			'getPackCost'	=> $rowengCPC,
			'getPackP'		=> $rowgTruckP,
			'getTruck'		=> $rowgTruck,
			'getVia'		=> $rowengCPCV,
			'getOpt'		=> $getOpt,
			'getOptP'		=> $getOptPl,
			'getArea'		=> $getArea,
			'getAreaL'		=> $getAreaL,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		$this->load->view('Penawaran/add_penawaran',$data);
	}
	
	public function save_penawaran(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$MatCost	= $data['MatCost'];
		$EngCost	= $data['EngCost'];
		$PackCost	= $data['PackCost'];
		$ExportCost	= $data['ExportCost'];
		$LokalCost	= $data['LokalCost'];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'price_project' => $data['total_all'],
			'created_by' 	=> $data_session['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrMatCost = array();
		foreach($MatCost AS $val => $valx){
			$ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			$ArrMatCost[$val]['category']		= $valx['category'];
			$ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			$ArrMatCost[$val]['extra']			= str_replace(',','',$valx['extra']);
			$ArrMatCost[$val]['persen']			= str_replace(',','',$valx['persen']);
			$ArrMatCost[$val]['fumigasi']		= $valx['harga'];
			$ArrMatCost[$val]['price']			= $valx['harga_total1'];
			$ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= (!empty($valx['unit']))?$valx['unit']:'-';
			$ArrEngCost[$val]['price']			= $valx['price'];
			$ArrEngCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrPackCost = array();
		foreach($PackCost AS $val => $valx){
			$ArrPackCost[$val]['id_bq']			= $data['id_bq'];
			$ArrPackCost[$val]['category']		= $valx['category'];
			$ArrPackCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrPackCost[$val]['option_type']	= $valx['option_type'];
			$ArrPackCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrExportCost = array();
		foreach($ExportCost AS $val => $valx){
			$ArrExportCost[$val]['id_bq']			= $data['id_bq'];
			$ArrExportCost[$val]['category']		= $valx['category'];
			$ArrExportCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrExportCost[$val]['option_type']		= $valx['option_type'];
			$ArrExportCost[$val]['qty']				= $valx['qty'];
			// $ArrExportCost[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrExportCost[$val]['price']			= $valx['price'];
			$ArrExportCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrLokalCost = array();
		foreach($LokalCost AS $val => $valx){
			$ArrLokalCost[$val]['id_bq']			= $data['id_bq'];
			$ArrLokalCost[$val]['category']			= $valx['category'];
			$ArrLokalCost[$val]['caregory_sub']		= $valx['caregory_sub'];
			$ArrLokalCost[$val]['area']				= $valx['area'];
			$ArrLokalCost[$val]['tujuan']			= $valx['tujuan'];
			$ArrLokalCost[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrLokalCost[$val]['qty']				= $valx['qty'];
			$ArrLokalCost[$val]['price']			= $valx['price'];
			$ArrLokalCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrEditCost = array(
			'sts_price_quo'			=> 'Y',
			'sts_price_quo_by' 		=> $data_session['ORI_User']['username'],
			'sts_price_quo_date' 	=> date('Y-m-d H:i:s')
		);
		
		//NONFRP
		$ArrNonFrp = array();
		if(!empty($data['nonfrp'])){
			$nonfrp	= $data['nonfrp'];
			foreach($nonfrp AS $val => $valx){
				$ArrNonFrp[$val]['id_bq']			= $data['id_bq'];
				$ArrNonFrp[$val]['category']		= $valx['category'];
				$ArrNonFrp[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrNonFrp[$val]['option_type']		= $valx['option_type'];
				$ArrNonFrp[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrNonFrp[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrNonFrp[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrNonFrp[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrNonFrp[$val]['price']			= $valx['price'];
				$ArrNonFrp[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		//NONFRP
		$ArrMaterial = array();
		if(!empty($data['material'])){
			$material	= $data['material'];
			foreach($material AS $val => $valx){
				$ArrMaterial[$val]['id_bq']			= $data['id_bq'];
				$ArrMaterial[$val]['category']		= $valx['category'];
				$ArrMaterial[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrMaterial[$val]['option_type']	= $valx['option_type'];
				$ArrMaterial[$val]['weight']		= str_replace(',','',$valx['qty']);
				$ArrMaterial[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrMaterial[$val]['persen']		= str_replace(',','',$valx['persen']);
				$ArrMaterial[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrMaterial[$val]['price']			= $valx['price'];
				$ArrMaterial[$val]['price_total']	= $valx['price_total'];
			}
		}
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		
		// print_r($ArrNonFrp); 
		// print_r($ArrMaterial);
		
		// exit;
		
		$this->db->trans_start();
			$this->db->insert('cost_project_header', $ArrHeader);
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
			
			if(!empty($ArrNonFrp)){
				$this->db->insert_batch('cost_project_detail', $ArrNonFrp);
			}
			if(!empty($ArrMaterial)){
				$this->db->insert_batch('cost_project_detail', $ArrMaterial);
			}
			
			$this->db->where('no_ipp', $data['no_ipp']);
			$this->db->update('production', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation success. Thanks ...',
				'status'	=> 1
			);				
			history('Cost Quotation new with : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	public function edit_penawaran_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/edit_penawaran_new';
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		
		$qMatr 		= SQL_Quo_Edit($id_bq);
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		// echo $engC;
		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		$gTruck 	= "SELECT
							e.*,
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') AND c.deleted='N')  as price
						FROM
							list_shipping a
								INNER JOIN cost_project_detail e ON CONCAT_WS(' ',a.shipping_name, a.type)=e.caregory_sub
						WHERE
							a.flag = 'Y' AND e.category = 'export' AND e.id_bq='".$id_bq."'
						ORDER BY
							a.urut ASC";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$gTruckP 	= "SELECT * FROM list_packing WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruckP	= $this->db->query($gTruckP)->result_array();
		
		$engCPCV 	= "SELECT
							b.*,
							c.* 
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
						WHERE
							 b.category = 'lokal' 
							AND b.id_bq = '".$id_bq."' 
						ORDER BY
							b.id ASC ";
		$rowengCPCV	= $this->db->query($engCPCV)->result_array();
		
		$qOpt 		= "SELECT * FROM list_help WHERE group_by = 'opt' ORDER BY id DESC ";
		$getOpt		= $this->db->query($qOpt)->result_array();
		
		$qOptPl 	= "SELECT * FROM list_help WHERE group_by = 'opt' OR group_by = 'opt plus' ORDER BY id DESC ";
		$getOptPl	= $this->db->query($qOptPl)->result_array();
		
		$qArea		= "SELECT area FROM cost_trucking WHERE category='darat' GROUP BY area ORDER BY area ASC ";
		$getArea	= $this->db->query($qArea)->result_array();
		
		$qAreaL		= "SELECT area FROM cost_trucking WHERE category='laut' GROUP BY area ORDER BY area ASC ";
		$getAreaL	= $this->db->query($qAreaL)->result_array();
		
		$sql_non_frp	= "	SELECT 
								b.* 
							FROM 
								bq_acc_and_mat b
							WHERE 
								b.category='acc'
								AND b.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material	= "	SELECT 
								b.* 
							FROM
								bq_acc_and_mat b
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		// echo $sql_non_frp;
		
		$data = array(
			'title'		=> 'Offer Structure',
			'action'	=> 'updateReal',
			'id_bq'			=> $id_bq,
			'getHeader'		=> $row,
			'getDetail'		=> $rowDet,
			'getEngCost'	=> $rowengC,
			'getPackCost'	=> $rowengCPC,
			'getPackP'	=> $rowgTruckP,
			'getTruck'		=> $rowgTruck,
			'getVia'		=> $rowengCPCV,
			'getOpt'		=> $getOpt,
			'getOptP'		=> $getOptPl,
			'getArea'		=> $getArea,
			'getAreaL'		=> $getAreaL,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		$this->load->view('Penawaran/edit_penawaran_new',$data);
	}
	
	public function save_edit_penawaran_new(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$MatCost	= $data['MatCost'];
		$EngCost	= $data['EngCost'];
		$PackCost	= $data['PackCost'];
		$ExportCost	= $data['ExportCost'];
		$LokalCost	= $data['LokalCost'];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'price_project' => $data['total_all'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrMatCost = array();
		foreach($MatCost AS $val => $valx){
			$ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			$ArrMatCost[$val]['category']		= $valx['category'];
			$ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			$ArrMatCost[$val]['persen']			= $valx['persen'];
			$ArrMatCost[$val]['extra']			= $valx['extra'];
			$ArrMatCost[$val]['fumigasi']		= $valx['harga'];
			$ArrMatCost[$val]['price']			= $valx['harga_total1'];
			$ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= (!empty($valx['unit']))?$valx['unit']:'-';
			$ArrEngCost[$val]['price']			= $valx['price'];
			$ArrEngCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrPackCost = array();
		foreach($PackCost AS $val => $valx){
			$ArrPackCost[$val]['id_bq']			= $data['id_bq'];
			$ArrPackCost[$val]['category']		= $valx['category'];
			$ArrPackCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrPackCost[$val]['option_type']	= $valx['option_type'];
			$ArrPackCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrExportCost = array();
		foreach($ExportCost AS $val => $valx){
			$ArrExportCost[$val]['id_bq']			= $data['id_bq'];
			$ArrExportCost[$val]['category']		= $valx['category'];
			$ArrExportCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrExportCost[$val]['option_type']		= $valx['option_type'];
			$ArrExportCost[$val]['qty']				= $valx['qty'];
			// $ArrExportCost[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrExportCost[$val]['price']			= $valx['price'];
			$ArrExportCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrLokalCost = array();
		foreach($LokalCost AS $val => $valx){
			$ArrLokalCost[$val]['id_bq']			= $data['id_bq'];
			$ArrLokalCost[$val]['category']			= $valx['category'];
			$ArrLokalCost[$val]['caregory_sub']		= $valx['caregory_sub'];
			$ArrLokalCost[$val]['area']				= $valx['area'];
			$ArrLokalCost[$val]['tujuan']			= $valx['tujuan'];
			$ArrLokalCost[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrLokalCost[$val]['qty']				= $valx['qty'];
			$ArrLokalCost[$val]['price']			= $valx['price'];
			$ArrLokalCost[$val]['price_total']		= $valx['price_total'];
		}
		
		// $restHistHeader = $this->db->query("SELECT * FROM cost_project_header WHERE id_bq='".$data['id_bq']."' ")->result_array();
		$restHistHeader = $this->db->get_where('cost_project_header', array('id_bq' => $data['id_bq']))->result_array();
		$ArrHeaderHist = array();
		foreach($restHistHeader AS $val => $valx){
			$ArrHeaderHist[$val]['id_bq']			= $data['id_bq'];
			$ArrHeaderHist[$val]['project']			= $valx['project'];
			$ArrHeaderHist[$val]['customer']		= $valx['customer'];
			$ArrHeaderHist[$val]['job_number']		= $valx['job_number'];
			$ArrHeaderHist[$val]['delivery']		= $valx['delivery'];
			$ArrHeaderHist[$val]['delivery_point']	= $valx['delivery_point'];
			$ArrHeaderHist[$val]['price_project']	= $valx['price_project'];
			$ArrHeaderHist[$val]['created_by']		= $valx['created_by'];
			$ArrHeaderHist[$val]['created_date']	= $valx['created_date'];
			$ArrHeaderHist[$val]['updated_by']		= $valx['updated_by'];
			$ArrHeaderHist[$val]['updated_date']	= $valx['updated_date'];
			
			$ArrHeaderHist[$val]['hist_by']			= $data_session['ORI_User']['username'];
			$ArrHeaderHist[$val]['hist_date']		= date('Y-m-d H:i:s');
		}
		
		// $restHistDetail = $this->db->query("SELECT * FROM cost_project_detail WHERE id_bq='".$data['id_bq']."' ")->result_array();
		$restHistDetail = $this->db->get_where('cost_project_detail', array('id_bq' => $data['id_bq']))->result_array();
		$ArrDetailHist = array();
		foreach($restHistDetail AS $val => $valx){
			$ArrDetailHist[$val]['id_bq']			= $data['id_bq'];
			$ArrDetailHist[$val]['category']		= $valx['category'];
			$ArrDetailHist[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrDetailHist[$val]['option_type']		= $valx['option_type'];
			$ArrDetailHist[$val]['area']			= $valx['area'];
			$ArrDetailHist[$val]['tujuan']			= $valx['tujuan'];
			$ArrDetailHist[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrDetailHist[$val]['unit']			= $valx['unit'];
			$ArrDetailHist[$val]['qty']				= $valx['qty'];
			$ArrDetailHist[$val]['extra']			= $valx['extra'];
			$ArrDetailHist[$val]['persen']			= $valx['persen'];
			$ArrDetailHist[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrDetailHist[$val]['price']			= $valx['price'];
			$ArrDetailHist[$val]['price_total']		= $valx['price_total'];
			
			$ArrDetailHist[$val]['hist_by']		= $data_session['ORI_User']['username'];
			$ArrDetailHist[$val]['hist_date']		= date('Y-m-d H:i:s');
		}
		
		//NONFRP
		$ArrNonFrp = array();
		if(!empty($data['nonfrp'])){
			$nonfrp	= $data['nonfrp'];
			foreach($nonfrp AS $val => $valx){
				$ArrNonFrp[$val]['id_bq']			= $data['id_bq'];
				$ArrNonFrp[$val]['category']		= $valx['category'];
				$ArrNonFrp[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrNonFrp[$val]['option_type']		= $valx['option_type'];
				$ArrNonFrp[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrNonFrp[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrNonFrp[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrNonFrp[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrNonFrp[$val]['price']			= $valx['price'];
				$ArrNonFrp[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		//NONFRP
		$ArrMaterial = array();
		if(!empty($data['material'])){
			$material	= $data['material'];
			foreach($material AS $val => $valx){
				$ArrMaterial[$val]['id_bq']			= $data['id_bq'];
				$ArrMaterial[$val]['category']		= $valx['category'];
				$ArrMaterial[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrMaterial[$val]['option_type']	= $valx['option_type'];
				$ArrMaterial[$val]['weight']		= str_replace(',','',$valx['qty']);
				$ArrMaterial[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrMaterial[$val]['persen']		= str_replace(',','',$valx['persen']);
				$ArrMaterial[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrMaterial[$val]['price']			= $valx['price'];
				$ArrMaterial[$val]['price_total']	= $valx['price_total'];
			}
		}
		
		
		//ToHistory
		
		
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		// print_r($ArrExportCost);
		// print_r($LokalCost); 
		
		
		// exit;
		
		$this->db->trans_start();
			$this->db->insert_batch('hist_cost_project_header', $ArrHeaderHist);
			$this->db->insert_batch('hist_cost_project_detail', $ArrDetailHist);
			
			$this->db->delete('cost_project_header', array('id_bq' => $data['id_bq'])); 
			
			$this->db->delete('cost_project_detail', array('id_bq' => $data['id_bq']));  
			
			$this->db->insert('cost_project_header', $ArrHeader);
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
			
			if(!empty($ArrNonFrp)){
				$this->db->insert_batch('cost_project_detail', $ArrNonFrp);
			}
			if(!empty($ArrMaterial)){
				$this->db->insert_batch('cost_project_detail', $ArrMaterial);
			}

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation success. Thanks ...',
				'status'	=> 1
			);				
			history('Edit Cost Quotation new with : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	//BARU
	public function add_penawaran2(){
		$id_bq		= $this->uri->segment(3);
		$getEx		= explode('-', $id_bq);
		$ipp		= $getEx[1];

		$row		= $this->db->get_where('production', array('no_ipp'=>$ipp))->result();
		
		// $qMatr 		= SQL_Quo($id_bq);
		$qMatr 		= "	SELECT 
							a.id,
							a.id_category,
							a.length,
							a.id_product,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.qty,
							a.man_power AS man_power,
							a.id_mesin AS id_mesin,
							a.total_time AS total_time,
							a.man_hours AS man_hours,
							a.pe_direct_labour,
							a.pe_indirect_labour,
							a.pe_machine,
							ifnull( a.pe_mould_mandrill, 0 ) AS pe_mould_mandrill,
							a.pe_consumable,
							a.pe_foh_consumable,
							a.pe_foh_depresiasi,
							a.pe_biaya_gaji_non_produksi,
							a.pe_biaya_non_produksi,
							a.pe_biaya_rutin_bulanan

						FROM 
							bq_detail_header a 
						WHERE 
							a.id_category <> 'pipe slongsong' 
							AND a.id_category <> 'product kosong' 
							AND a.id_bq = '$id_bq' 
						ORDER BY 
							a.id ASC";
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$rowengC	= $this->db->order_by('id','ASC')->get('cost_engine')->result_array();
		$rowengCPC	= $this->db->order_by('id','ASC')->get_where('list_help',array('group_by'=>'pack cost'))->result_array();
		
		$gTruck 	= "SELECT
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') AND c.deleted='N')  as price
						FROM
							list_shipping a
						WHERE
							a.flag = 'Y' 
						ORDER BY
							a.urut ASC";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$rowgTruckP	= $this->db->order_by('urut','ASC')->get_where('list_packing',array('flag'=>'Y'))->result_array();
		$rowengCPCV	= $this->db->order_by('urut','ASC')->get_where('list_help',array('group_by'=>'via'))->result_array();
		$getOpt		= $this->db->order_by('id','DESC')->get_where('list_help',array('group_by'=>'opt'))->result_array();
		$getOptPl	= $this->db->order_by('id','ASC')->where(array('group_by'=>'opt'))->or_where(array('group_by'=>'opt plus'))->get('list_help')->result_array();
		$area_darat	= $this->db->select('area')->group_by('area')->order_by('area','ASC')->get_where('cost_trucking', array('category'=>'darat'))->result_array();
		$area_laut	= $this->db->select('area')->group_by('area')->order_by('area','ASC')->get_where('cost_trucking', array('category'=>'laut'))->result_array();
		$non_frp	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'acc'))->result_array();
		$material	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'mat'))->result_array();
		$baut		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'baut'))->result_array();
		$plate		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'plate'))->result_array();
		$gasket		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'gasket'))->result_array();
		$lainnya	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'lainnya'))->result_array();
		
		$data = array(
			'title'			=> 'Offer Structure New2',
			'action'		=> 'index',
			'getHeader'		=> $row,
			'getDetail'		=> $rowDet,
			'getEngCost'	=> $rowengC,
			'getPackCost'	=> $rowengCPC,
			'getPackP'		=> $rowgTruckP,
			'getTruck'		=> $rowgTruck,
			'getVia'		=> $rowengCPCV,
			'getOpt'		=> $getOpt,
			'getOptP'		=> $getOptPl,
			'area_darat'	=> $area_darat,
			'area_laut'		=> $area_laut,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'baut'			=> $baut,
			'plate'			=> $plate,
			'gasket'		=> $gasket,
			'lainnya'		=> $lainnya,
			'GET_DET_ACC' => get_detail_accessories()
		);
		$this->load->view('Penawaran/add_penawaran2',$data);
	}
	
	public function save_penawaran2(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$MatCost	= (!empty($data['MatCost']))?$data['MatCost']:[];
		$EngCost	= (!empty($data['EngCost']))?$data['EngCost']:[];
		$PackCost	= (!empty($data['PackCost']))?$data['PackCost']:[];
		$ExportCost	= (!empty($data['ExportCost']))?$data['ExportCost']:[];
		$LokalCost	= (!empty($data['LokalCost']))?$data['LokalCost']:[];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'price_project' => $data['total_all'],
			'created_by' 	=> $data_session['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrMatCost = array();
		foreach($MatCost AS $val => $valx){
			$ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			$ArrMatCost[$val]['category']		= $valx['category'];
			$ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			$ArrMatCost[$val]['extra']			= str_replace(',','',$valx['extra']);
			$ArrMatCost[$val]['persen']			= str_replace(',','',$valx['persen']);
			$ArrMatCost[$val]['fumigasi']		= $valx['harga'];
			$ArrMatCost[$val]['price']			= $valx['harga_total1'];
			$ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= (!empty($valx['unit']))?$valx['unit']:'-';
			$ArrEngCost[$val]['price']			= $valx['price'];
			$ArrEngCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrPackCost = array();
		foreach($PackCost AS $val => $valx){
			$ArrPackCost[$val]['id_bq']			= $data['id_bq'];
			$ArrPackCost[$val]['category']		= $valx['category'];
			$ArrPackCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrPackCost[$val]['option_type']	= $valx['option_type'];
			$ArrPackCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrExportCost = array();
		foreach($ExportCost AS $val => $valx){
			$ArrExportCost[$val]['id_bq']			= $data['id_bq'];
			$ArrExportCost[$val]['category']		= $valx['category'];
			$ArrExportCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrExportCost[$val]['option_type']		= $valx['option_type'];
			$ArrExportCost[$val]['qty']				= $valx['qty'];
			// $ArrExportCost[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrExportCost[$val]['price']			= $valx['price'];
			$ArrExportCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrLokalCost = array();
		foreach($LokalCost AS $val => $valx){
			$ArrLokalCost[$val]['id_bq']			= $data['id_bq'];
			$ArrLokalCost[$val]['category']			= $valx['category'];
			$ArrLokalCost[$val]['caregory_sub']		= $valx['caregory_sub'];
			$ArrLokalCost[$val]['area']				= $valx['area'];
			$ArrLokalCost[$val]['tujuan']			= $valx['tujuan'];
			$ArrLokalCost[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrLokalCost[$val]['qty']				= $valx['qty'];
			$ArrLokalCost[$val]['price']			= $valx['price'];
			$ArrLokalCost[$val]['price_total']		= $valx['price_total'];
		}

		$ArrOther = array();
		if(!empty($data['DetailOther'])){
			foreach($data['DetailOther'] AS $val => $valx){
				$ArrOther[$val]['id_bq']		= $data['id_bq'];
				$ArrOther[$val]['category']		= 'other';
				$ArrOther[$val]['caregory_sub']	= $valx['desc'];
				$ArrOther[$val]['qty']			= $valx['qty'];
				$ArrOther[$val]['price']		= $valx['unit_price'];
				$ArrOther[$val]['price_total']	= $valx['total_price'];
			}
		}
		
		$ArrEditCost = array(
			'sts_price_quo'			=> 'Y',
			'sts_price_quo_by' 		=> $data_session['ORI_User']['username'],
			'sts_price_quo_date' 	=> date('Y-m-d H:i:s')
		);
		
		//NONFRP
		$ArrNonFrp = array();
		if(!empty($data['nonfrp'])){
			$nonfrp	= $data['nonfrp'];
			foreach($nonfrp AS $val => $valx){
				$ArrNonFrp[$val]['id_bq']			= $data['id_bq'];
				$ArrNonFrp[$val]['category']		= $valx['category'];
				$ArrNonFrp[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrNonFrp[$val]['option_type']		= $valx['option_type'];
				$ArrNonFrp[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrNonFrp[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrNonFrp[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrNonFrp[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrNonFrp[$val]['price']			= $valx['price'];
				$ArrNonFrp[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		$ArrMaterial = array();
		if(!empty($data['material'])){
			$material	= $data['material'];
			foreach($material AS $val => $valx){
				$ArrMaterial[$val]['id_bq']			= $data['id_bq'];
				$ArrMaterial[$val]['category']		= $valx['category'];
				$ArrMaterial[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrMaterial[$val]['option_type']	= $valx['option_type'];
				$ArrMaterial[$val]['id_milik']		= $valx['id_milik'];
				$ArrMaterial[$val]['weight']		= str_replace(',','',$valx['qty']);
				$ArrMaterial[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrMaterial[$val]['persen']		= str_replace(',','',$valx['persen']);
				$ArrMaterial[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrMaterial[$val]['price']			= $valx['price'];
				$ArrMaterial[$val]['price_total']	= $valx['price_total'];
			}
		}
		
		$ArrBaut = array();
		if(!empty($data['baut'])){
			$baut	= $data['baut'];
			foreach($baut AS $val => $valx){
				$ArrBaut[$val]['id_bq']			= $data['id_bq'];
				$ArrBaut[$val]['category']		= $valx['category'];
				$ArrBaut[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrBaut[$val]['option_type']		= $valx['option_type'];
				$ArrBaut[$val]['id_milik']		= $valx['id_milik'];
				$ArrBaut[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrBaut[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrBaut[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrBaut[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrBaut[$val]['price']			= $valx['price'];
				$ArrBaut[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		$ArrPlate = array();
		if(!empty($data['plate'])){
			$plate	= $data['plate'];
			foreach($plate AS $val => $valx){
				$ArrPlate[$val]['id_bq']			= $data['id_bq'];
				$ArrPlate[$val]['category']		= $valx['category'];
				$ArrPlate[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrPlate[$val]['option_type']		= $valx['option_type'];
				$ArrPlate[$val]['id_milik']		= $valx['id_milik'];
				$ArrPlate[$val]['weight']				= str_replace(',','',$valx['qty']);
				$ArrPlate[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrPlate[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrPlate[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrPlate[$val]['price']			= $valx['price'];
				$ArrPlate[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		$ArrGasket = array();
		if(!empty($data['gasket'])){
			$gasket	= $data['gasket'];
			foreach($gasket AS $val => $valx){
				$ArrGasket[$val]['id_bq']			= $data['id_bq'];
				$ArrGasket[$val]['category']		= $valx['category'];
				$ArrGasket[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrGasket[$val]['option_type']		= $valx['option_type'];
				$ArrGasket[$val]['id_milik']		= $valx['id_milik'];
				$ArrGasket[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrGasket[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrGasket[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrGasket[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrGasket[$val]['price']			= $valx['price'];
				$ArrGasket[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		$ArrLainnya = array();
		if(!empty($data['lainnya'])){
			$lainnya	= $data['lainnya'];
			foreach($lainnya AS $val => $valx){
				$ArrLainnya[$val]['id_bq']			= $data['id_bq'];
				$ArrLainnya[$val]['category']		= $valx['category'];
				$ArrLainnya[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrLainnya[$val]['option_type']	= $valx['option_type'];
				$ArrLainnya[$val]['id_milik']		= $valx['id_milik'];
				$ArrLainnya[$val]['qty']			= str_replace(',','',$valx['qty']);
				$ArrLainnya[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrLainnya[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrLainnya[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrLainnya[$val]['price']			= $valx['price'];
				$ArrLainnya[$val]['price_total']	= $valx['price_total'];
			}
		}
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		
		// print_r($ArrNonFrp); 
		// print_r($ArrMaterial);
		
		// print_r($ArrBaut); 
		// print_r($ArrPlate);
		// print_r($ArrGasket); 
		// print_r($ArrLainnya);
		// exit;
		
		$this->db->trans_start();
			$this->db->delete('cost_project_header', array('id_bq' => $data['id_bq'])); 
			
			$this->db->delete('cost_project_detail', array('id_bq' => $data['id_bq'])); 
			
			$this->db->insert('cost_project_header', $ArrHeader);
			if(!empty($ArrMatCost)){
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			}
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
			if(!empty($ArrOther)){
				$this->db->insert_batch('cost_project_detail', $ArrOther);
			}
			
			if(!empty($ArrNonFrp)){
				$this->db->insert_batch('cost_project_detail', $ArrNonFrp);
			}
			if(!empty($ArrMaterial)){
				$this->db->insert_batch('cost_project_detail', $ArrMaterial);
			}
			if(!empty($ArrBaut)){
				$this->db->insert_batch('cost_project_detail', $ArrBaut);
			}
			if(!empty($ArrPlate)){
				$this->db->insert_batch('cost_project_detail', $ArrPlate);
			}
			if(!empty($ArrGasket)){
				$this->db->insert_batch('cost_project_detail', $ArrGasket);
			}
			if(!empty($ArrLainnya)){
				$this->db->insert_batch('cost_project_detail', $ArrLainnya);
			}
			
			$this->db->where('no_ipp', $data['no_ipp']);
			$this->db->update('production', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation success. Thanks ...',
				'status'	=> 1
			);				
			history('Cost Quotation new with : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	public function edit_penawaran_new2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/edit_penawaran_new2';
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		
		// $qMatr 		= SQL_Quo_Edit($id_bq);
		$qMatr 		= "	SELECT 
							a.id,
							a.id_category,
							a.length,
							a.id_product,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.qty,
							a.man_power AS man_power,
							a.id_mesin AS id_mesin,
							a.total_time AS total_time,
							a.man_hours AS man_hours,
							a.pe_direct_labour,
							a.pe_indirect_labour,
							a.pe_machine,
							ifnull( a.pe_mould_mandrill, 0 ) AS pe_mould_mandrill,
							a.pe_consumable,
							a.pe_foh_consumable,
							a.pe_foh_depresiasi,
							a.pe_biaya_gaji_non_produksi,
							a.pe_biaya_non_produksi,
							a.pe_biaya_rutin_bulanan

						FROM 
							bq_detail_header a 
						WHERE 
							a.id_category <> 'pipe slongsong' 
							AND a.id_category <> 'product kosong' 
							AND a.id_bq = '$id_bq' 
						ORDER BY 
							a.id ASC";
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		// echo $engC;
		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		$gTruck 	= "SELECT
							e.*,
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".$ipp."') AND c.deleted='N')  as price
						FROM
							list_shipping a
								INNER JOIN cost_project_detail e ON CONCAT_WS(' ',a.shipping_name, a.type)=e.caregory_sub
						WHERE
							a.flag = 'Y' AND e.category = 'export' AND e.id_bq='".$id_bq."'
						ORDER BY
							a.urut ASC";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$gTruckP 	= "SELECT * FROM list_packing WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruckP	= $this->db->query($gTruckP)->result_array();
		
		$engCPCV 	= "SELECT
							b.*,
							c.* 
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
						WHERE
							 b.category = 'lokal' 
							AND b.id_bq = '".$id_bq."' 
						ORDER BY
							b.id ASC ";
		$rowengCPCV	= $this->db->query($engCPCV)->result_array();
		
		$qOpt 		= "SELECT * FROM list_help WHERE group_by = 'opt' ORDER BY id DESC ";
		$getOpt		= $this->db->query($qOpt)->result_array();
		
		$qOptPl 	= "SELECT * FROM list_help WHERE group_by = 'opt' OR group_by = 'opt plus' ORDER BY id DESC ";
		$getOptPl	= $this->db->query($qOptPl)->result_array();
		
		$area_darat	= $this->db->select('area')->group_by('area')->order_by('area','ASC')->get_where('cost_trucking', array('category'=>'darat'))->result_array();
		$area_laut	= $this->db->select('area')->group_by('area')->order_by('area','ASC')->get_where('cost_trucking', array('category'=>'laut'))->result_array();
		
		
		$non_frp	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'acc'))->result_array();
		$material	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'mat'))->result_array();
		$baut		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'baut'))->result_array();
		$plate		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'plate'))->result_array();
		$gasket		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'gasket'))->result_array();
		$lainnya	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'lainnya'))->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'title'		=> 'Offer Structure',
			'action'	=> 'updateReal',
			'id_bq'			=> $id_bq,
			'getHeader'		=> $row,
			'getDetail'		=> $rowDet,
			'getEngCost'	=> $rowengC,
			'getPackCost'	=> $rowengCPC,
			'getPackP'		=> $rowgTruckP,
			'getTruck'		=> $rowgTruck,
			'getVia'		=> $rowengCPCV,
			'getOpt'		=> $getOpt,
			'getOptP'		=> $getOptPl,
			'area_darat'	=> $area_darat,
			'area_laut'		=> $area_laut,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'baut'			=> $baut,
			'plate'			=> $plate,
			'gasket'		=> $gasket,
			'lainnya'		=> $lainnya,
			'otherArray'	=> $otherArray,
			'GET_DET_ACC' => get_detail_accessories()
		);
		$this->load->view('Penawaran/edit_penawaran_new2',$data);
	}
	
	public function save_edit_penawaran_new2(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$MatCost	= (!empty($data['MatCost']))?$data['MatCost']:[];
		$EngCost	= (!empty($data['EngCost']))?$data['EngCost']:[];
		$PackCost	= (!empty($data['PackCost']))?$data['PackCost']:[];
		$ExportCost	= (!empty($data['ExportCost']))?$data['ExportCost']:[];
		$LokalCost	= (!empty( $data['LokalCost']))? $data['LokalCost']:[];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'price_project' => $data['total_all'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrMatCost = array();
		foreach($MatCost AS $val => $valx){
			$ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			$ArrMatCost[$val]['category']		= $valx['category'];
			$ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			$ArrMatCost[$val]['persen']			= $valx['persen'];
			$ArrMatCost[$val]['extra']			= $valx['extra'];
			$ArrMatCost[$val]['fumigasi']		= $valx['harga'];
			$ArrMatCost[$val]['price']			= $valx['harga_total1'];
			$ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= (!empty($valx['unit']))?$valx['unit']:'-';
			$ArrEngCost[$val]['price']			= $valx['price'];
			$ArrEngCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrPackCost = array();
		foreach($PackCost AS $val => $valx){
			$ArrPackCost[$val]['id_bq']			= $data['id_bq'];
			$ArrPackCost[$val]['category']		= $valx['category'];
			$ArrPackCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrPackCost[$val]['option_type']	= $valx['option_type'];
			$ArrPackCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrExportCost = array();
		foreach($ExportCost AS $val => $valx){
			$ArrExportCost[$val]['id_bq']			= $data['id_bq'];
			$ArrExportCost[$val]['category']		= $valx['category'];
			$ArrExportCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrExportCost[$val]['option_type']		= $valx['option_type'];
			$ArrExportCost[$val]['qty']				= $valx['qty'];
			// $ArrExportCost[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrExportCost[$val]['price']			= $valx['price'];
			$ArrExportCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrLokalCost = array();
		foreach($LokalCost AS $val => $valx){
			$ArrLokalCost[$val]['id_bq']			= $data['id_bq'];
			$ArrLokalCost[$val]['category']			= $valx['category'];
			$ArrLokalCost[$val]['caregory_sub']		= $valx['caregory_sub'];
			$ArrLokalCost[$val]['area']				= $valx['area'];
			$ArrLokalCost[$val]['tujuan']			= $valx['tujuan'];
			$ArrLokalCost[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrLokalCost[$val]['qty']				= $valx['qty'];
			$ArrLokalCost[$val]['price']			= $valx['price'];
			$ArrLokalCost[$val]['price_total']		= $valx['price_total'];
		}
		
		// $restHistHeader = $this->db->query("SELECT * FROM cost_project_header WHERE id_bq='".$data['id_bq']."' ")->result_array();
		$restHistHeader = $this->db->get_where('cost_project_header', array('id_bq' => $data['id_bq']))->result_array();
		$ArrHeaderHist = array();
		foreach($restHistHeader AS $val => $valx){
			$ArrHeaderHist[$val]['id_bq']			= $data['id_bq'];
			$ArrHeaderHist[$val]['project']			= $valx['project'];
			$ArrHeaderHist[$val]['customer']		= $valx['customer'];
			$ArrHeaderHist[$val]['job_number']		= $valx['job_number'];
			$ArrHeaderHist[$val]['delivery']		= $valx['delivery'];
			$ArrHeaderHist[$val]['delivery_point']	= $valx['delivery_point'];
			$ArrHeaderHist[$val]['price_project']	= $valx['price_project'];
			$ArrHeaderHist[$val]['created_by']		= $valx['created_by'];
			$ArrHeaderHist[$val]['created_date']	= $valx['created_date'];
			$ArrHeaderHist[$val]['updated_by']		= $valx['updated_by'];
			$ArrHeaderHist[$val]['updated_date']	= $valx['updated_date'];
			
			$ArrHeaderHist[$val]['hist_by']			= $data_session['ORI_User']['username'];
			$ArrHeaderHist[$val]['hist_date']		= date('Y-m-d H:i:s');
		}
		
		// $restHistDetail = $this->db->query("SELECT * FROM cost_project_detail WHERE id_bq='".$data['id_bq']."' ")->result_array();
		$restHistDetail = $this->db->get_where('cost_project_detail', array('id_bq' => $data['id_bq']))->result_array();
		$ArrDetailHist = array();
		foreach($restHistDetail AS $val => $valx){
			$ArrDetailHist[$val]['id_bq']			= $data['id_bq'];
			$ArrDetailHist[$val]['category']		= $valx['category'];
			$ArrDetailHist[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrDetailHist[$val]['option_type']		= $valx['option_type'];
			$ArrDetailHist[$val]['area']			= $valx['area'];
			$ArrDetailHist[$val]['tujuan']			= $valx['tujuan'];
			$ArrDetailHist[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrDetailHist[$val]['unit']			= $valx['unit'];
			$ArrDetailHist[$val]['qty']				= $valx['qty'];
			$ArrDetailHist[$val]['extra']			= $valx['extra'];
			$ArrDetailHist[$val]['persen']			= $valx['persen'];
			$ArrDetailHist[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrDetailHist[$val]['price']			= $valx['price'];
			$ArrDetailHist[$val]['price_total']		= $valx['price_total'];
			
			$ArrDetailHist[$val]['hist_by']		= $data_session['ORI_User']['username'];
			$ArrDetailHist[$val]['hist_date']		= date('Y-m-d H:i:s');
		}
		
		//NONFRP
		$ArrNonFrp = array();
		if(!empty($data['nonfrp'])){
			$nonfrp	= $data['nonfrp'];
			foreach($nonfrp AS $val => $valx){
				$ArrNonFrp[$val]['id_bq']			= $data['id_bq'];
				$ArrNonFrp[$val]['category']		= $valx['category'];
				$ArrNonFrp[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrNonFrp[$val]['option_type']		= $valx['option_type'];
				$ArrNonFrp[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrNonFrp[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrNonFrp[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrNonFrp[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrNonFrp[$val]['price']			= $valx['price'];
				$ArrNonFrp[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		//NONFRP
		$ArrMaterial = array();
		if(!empty($data['material'])){
			$material	= $data['material'];
			foreach($material AS $val => $valx){
				$ArrMaterial[$val]['id_bq']			= $data['id_bq'];
				$ArrMaterial[$val]['category']		= $valx['category'];
				$ArrMaterial[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrMaterial[$val]['option_type']	= $valx['option_type'];
				$ArrMaterial[$val]['id_milik']		= $valx['id_milik'];
				$ArrMaterial[$val]['weight']		= str_replace(',','',$valx['qty']);
				$ArrMaterial[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrMaterial[$val]['persen']		= str_replace(',','',$valx['persen']);
				$ArrMaterial[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrMaterial[$val]['price']			= $valx['price'];
				$ArrMaterial[$val]['price_total']	= $valx['price_total'];
			}
		}
		
		$ArrBaut = array();
		if(!empty($data['baut'])){
			$baut	= $data['baut'];
			foreach($baut AS $val => $valx){
				$ArrBaut[$val]['id_bq']			= $data['id_bq'];
				$ArrBaut[$val]['category']		= $valx['category'];
				$ArrBaut[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrBaut[$val]['option_type']		= $valx['option_type'];
				$ArrBaut[$val]['id_milik']		= $valx['id_milik'];
				$ArrBaut[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrBaut[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrBaut[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrBaut[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrBaut[$val]['price']			= $valx['price'];
				$ArrBaut[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		$ArrPlate = array();
		if(!empty($data['plate'])){
			$plate	= $data['plate'];
			foreach($plate AS $val => $valx){
				$ArrPlate[$val]['id_bq']			= $data['id_bq'];
				$ArrPlate[$val]['category']		= $valx['category'];
				$ArrPlate[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrPlate[$val]['option_type']		= $valx['option_type'];
				$ArrPlate[$val]['id_milik']		= $valx['id_milik'];
				$ArrPlate[$val]['weight']				= str_replace(',','',$valx['qty']);
				$ArrPlate[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrPlate[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrPlate[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrPlate[$val]['price']			= $valx['price'];
				$ArrPlate[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		$ArrGasket = array();
		if(!empty($data['gasket'])){
			$gasket	= $data['gasket'];
			foreach($gasket AS $val => $valx){
				$ArrGasket[$val]['id_bq']			= $data['id_bq'];
				$ArrGasket[$val]['category']		= $valx['category'];
				$ArrGasket[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrGasket[$val]['option_type']		= $valx['option_type'];
				$ArrGasket[$val]['id_milik']		= $valx['id_milik'];
				$ArrGasket[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrGasket[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrGasket[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrGasket[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrGasket[$val]['price']			= $valx['price'];
				$ArrGasket[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		$ArrLainnya = array();
		if(!empty($data['lainnya'])){
			$lainnya	= $data['lainnya'];
			foreach($lainnya AS $val => $valx){
				$ArrLainnya[$val]['id_bq']			= $data['id_bq'];
				$ArrLainnya[$val]['category']		= $valx['category'];
				$ArrLainnya[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrLainnya[$val]['option_type']	= $valx['option_type'];
				$ArrLainnya[$val]['id_milik']		= $valx['id_milik'];
				$ArrLainnya[$val]['qty']			= str_replace(',','',$valx['qty']);
				$ArrLainnya[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrLainnya[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrLainnya[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrLainnya[$val]['price']			= $valx['price'];
				$ArrLainnya[$val]['price_total']	= $valx['price_total'];
			}
		}

		$ArrOther = array();
		if(!empty($data['DetailOther'])){
			foreach($data['DetailOther'] AS $val => $valx){
				$ArrOther[$val]['id_bq']		= $data['id_bq'];
				$ArrOther[$val]['category']		= 'other';
				$ArrOther[$val]['caregory_sub']	= $valx['desc'];
				$ArrOther[$val]['qty']			= $valx['qty'];
				$ArrOther[$val]['price']		= $valx['unit_price'];
				$ArrOther[$val]['price_total']	= $valx['total_price'];
			}
		}
		
		
		//ToHistory
		
		
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		// print_r($ArrExportCost);
		// print_r($LokalCost); 
		
		
		// exit;
		
		$this->db->trans_start();
			$this->db->insert_batch('hist_cost_project_header', $ArrHeaderHist);
			$this->db->insert_batch('hist_cost_project_detail', $ArrDetailHist);
			
			$this->db->delete('cost_project_header', array('id_bq' => $data['id_bq'])); 
			
			$this->db->delete('cost_project_detail', array('id_bq' => $data['id_bq']));  
			
			$this->db->insert('cost_project_header', $ArrHeader);
			if(!empty($ArrMatCost)){
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			}
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
			if(!empty($ArrOther)){
				$this->db->insert_batch('cost_project_detail', $ArrOther);
			}
			
			if(!empty($ArrNonFrp)){
				$this->db->insert_batch('cost_project_detail', $ArrNonFrp);
			}
			if(!empty($ArrMaterial)){
				$this->db->insert_batch('cost_project_detail', $ArrMaterial);
			}
			if(!empty($ArrBaut)){
				$this->db->insert_batch('cost_project_detail', $ArrBaut);
			}
			if(!empty($ArrPlate)){
				$this->db->insert_batch('cost_project_detail', $ArrPlate);
			}
			if(!empty($ArrGasket)){
				$this->db->insert_batch('cost_project_detail', $ArrGasket);
			}
			if(!empty($ArrLainnya)){
				$this->db->insert_batch('cost_project_detail', $ArrLainnya);
			}

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation success. Thanks ...',
				'status'	=> 1
			);				
			history('Edit Cost Quotation new with : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	//==========================================================================================================
	//========================================PENAWARAN SALES===================================================
	//==========================================================================================================
	
	public function edit_penawaran_sales(){
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();
		

		$sql_header 	= "SELECT 
								a.*, 
								b.job_number, 
								b.quo_number, 
								b.subject, 
								b.product, 
								b.pengiriman, 
								b.sales, 
								b.jangka_waktu_penawaran, 
								b.garansi_porduct, 
								b.tahap_pembayaran,
								b.waktu_pengiriman,
								b.attn,
								b.kurs								
							FROM production a LEFT JOIN cost_project_header_sales b ON a.no_ipp=REPLACE( b.id_bq, 'BQ-', '' )  WHERE a.no_ipp = '".$ipp."' LIMIT 1";
		$rest_header	= $this->db->query($sql_header)->result();
							
		$sql_detail 	= "	SELECT
							a.id_bq,
							a.id AS id_milik,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							b.price_total AS cost,
							c.est_material
						FROM
							bq_detail_header a 
							LEFT JOIN cost_project_detail b ON a.id=b.caregory_sub
							LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
						WHERE
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."' AND a.id_category <> 'product kosong'";		
		$rest_detail	= $this->db->query($sql_detail)->result_array();
		
		$ListBQipp		= $this->db->query("SELECT series FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series")->result_array();
		$dtListArray = array();
		foreach($ListBQipp AS $val => $valx){
			$dtListArray[$val] = $valx['series'];
		}
		$dtImplode	= "".implode(", ", $dtListArray)."";
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price,
								b.id AS id2,
								b.so_sts
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material 
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."'";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.*,
								b.id AS id2,
								b.so_sts		
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material 
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."'";
		$material		= $this->db->query($sql_material)->result_array();

		$data = array(
			'title'			=> 'Offer Structure',
			'action'		=> 'updateReal',
			'getHeader'		=> $rest_header,
			'getDetail'		=> $rest_detail,
			'series'		=> $dtImplode,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		$this->load->view('Penawaran/edit_penawaran_sales',$data);
	}
	
	public function save_edit_penawaran_sales(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		// $MatCost	= $data['MatCost'];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'job_number' 	=> $data['job_number'],
			'quo_number' 	=> $data['quo_number'],
			'price_project' => 0,
			
			'subject' 				=> strtolower($data['subject']),
			'kurs' 				=> str_replace(',','',$data['kurs']),
			'product' 				=> strtolower($data['product']),
			'pengiriman' 			=> strtolower($data['pengiriman']),
			'sales' 				=> strtolower($data['sales']),
			'jangka_waktu_penawaran'=> strtolower($data['jangka_waktu_penawaran']),
			'garansi_porduct' 		=> strtolower($data['garansi_porduct']),
			'tahap_pembayaran' 		=> strtolower($data['tahap_pembayaran']),
			'waktu_pengiriman' 		=> strtolower($data['waktu_pengiriman']),
			'attn' 					=> strtolower($data['attn']),
			
			'created_by' 	=> $data_session['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		// $ArrMatCost = array();
		// foreach($MatCost AS $val => $valx){
			// $ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			// $ArrMatCost[$val]['category']		= $valx['category'];
			// $ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			// $ArrMatCost[$val]['nego']			= $valx['nego'];
			// $ArrMatCost[$val]['price']			= $valx['harga'];
			// $ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		// }
		
		// print_r($ArrMatCost);
		// exit;
		
		$this->db->trans_start();
			$this->db->delete('cost_project_header_sales', array('id_bq' => $data['id_bq']));  
			// $this->db->delete('cost_project_detail_sales', array('id_bq' => $data['id_bq']));  
			
			$this->db->insert('cost_project_header_sales', $ArrHeader);
			// $this->db->insert_batch('cost_project_detail_sales', $ArrMatCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1
			);				
			history('Cost Quotation Sales with bq : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	public function print_cetak(){
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/libraries/MPDF57/mpdf.php";
	  
		$data_session	= $this->session->userdata;
		$id_bq   		= $this->uri->segment(3);
		$no_ipp   		= str_replace('BQ-','',$id_bq);

		$get_id_cust	= get_name('production','id_customer','no_ipp',$no_ipp);
		$alamat_cust	= get_name('customer','alamat','id_customer',$get_id_cust);
		$telephone		= get_name('customer','telpon','id_customer',$get_id_cust);
		
		$mpdf= new mPDF('utf-8','A4');
		$mpdf->SetImportUse();
		
		$get_header = $this->db->get_where('cost_project_header_sales', array('id_bq'=>$id_bq))->result();
		
		$sqlResin = "(SELECT id_material, nm_material  FROM bq_component_detail WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)
					 UNION
					(SELECT id_material, nm_material  FROM bq_component_detail_plus WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)";
		$ListBQipp		= $this->db->query($sqlResin)->result_array();
		$dtListArray = array();
		foreach($ListBQipp AS $val => $valx){
			$dtListArray[$val] = $valx['nm_material'];
		}
		$dtImplode	= "".implode(",  ", $dtListArray)."";
			
		$customer = (!empty($get_header[0]->customer))?strtoupper($get_header[0]->customer):'-';
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();
		$sql_detail 	= "	SELECT
							a.id_bq,
							a.id AS id_milik,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							c.total_price_last AS cost,
							c.est_material
						FROM
							bq_detail_header a 
							LEFT JOIN cost_project_detail b ON a.id=b.caregory_sub
							LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
						WHERE
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."' AND a.id_category != 'product kosong'";		
		$rest_detail	= $this->db->query($sql_detail)->result_array();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.*,
								b.qty AS qty_berat
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		
		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
		$enggenering	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
		$packing	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
		$export	= $this->db->query($gTruck)->result_array();

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
						ORDER BY
							b.id ASC ";
		$local	= $this->db->query($engCPCV)->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'header'				=> $get_header,
			'sroot'					=> $sroot,
			'id_bq'					=> $id_bq,
			'subject'				=> (!empty($get_header[0]->subject))?ucfirst($get_header[0]->subject):'-',
			'product'				=> (!empty($get_header[0]->product))?strtoupper($get_header[0]->product):'-',
			'pengiriman'			=> (!empty($get_header[0]->pengiriman))?strtoupper($get_header[0]->pengiriman):'-',
			'sales'					=> (!empty($get_header[0]->sales))?ucwords($get_header[0]->sales):'-',
			'jangka_waktu_penawaran'=> (!empty($get_header[0]->jangka_waktu_penawaran))?ucfirst($get_header[0]->jangka_waktu_penawaran):'-',
			'garansi_porduct'		=> (!empty($get_header[0]->garansi_porduct))?ucfirst($get_header[0]->garansi_porduct):'-',
			'tahap_pembayaran'		=> (!empty($get_header[0]->tahap_pembayaran))?ucfirst($get_header[0]->tahap_pembayaran):'-',
			'waktu_pengiriman'		=> (!empty($get_header[0]->waktu_pengiriman))?ucfirst($get_header[0]->waktu_pengiriman):'-',
			'customer'				=> $customer,
			'resin'					=> strtoupper($dtImplode),
			'quo_number'			=> (!empty($get_header[0]->quo_number))?strtoupper($get_header[0]->quo_number):'-',
			'job_number'			=> (!empty($get_header[0]->job_number))?strtoupper($get_header[0]->job_number):'-',
			'attn'					=> (!empty($get_header[0]->attn))?ucfirst($get_header[0]->attn):'-',
			'detail_product'		=> $rest_detail,
			'otherArray'		=> $otherArray,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'enggenering'	=> $enggenering,
			'packing'		=> $packing,
			'export'		=> $export,
			'local'			=> $local,
			'alamat_cust'	=> $alamat_cust,
			'telephone'		=> $telephone,
			'kurs'			=> $get_header[0]->kurs
		);
        
		
		$header2 	= $this->load->view('Print/print_quo_cetak_header', $data, TRUE);
        $body 		= $this->load->view('Print/print_quo_cetak_body', $data, TRUE);
		$footer2 	= "<img src='".$sroot."/assets/images/footer_quo.png' alt='' width='100%'>"; 
		// echo $body ;
		// exit;
		$header = array (
			'odd' => array (
				'C' => array (
					'content' => $header2
				),
				'line' => 0,
			),
			'even' => array ()
		);
		
		$footer = array (
			'odd' => array (
				'C' => array (
					'content' => $footer2,
					'width' => '100%'
				),
				'line' => 0,
			),
			'even' => array ()
		);
		
        $mpdf->SetHeader($header);
		$mpdf->SetFooter($footer);
		
		$mpdf->defaultheaderline = 0;
		
        $mpdf->AddPageByArray([
			'orientation' => 'P',
			'margin-top' => 40,
			'margin-bottom' => 15,
			'margin-left' => 0,
			'margin-right' => 0,
			'margin-header' => 0,
			'margin-footer' => 0,
			'line' => 0
		]);
		
		history('Print new quotation indonesia version / '.str_replace('BQ-','',$id_bq));
		
		$mpdf->SetTitle($id_bq);
        $mpdf->WriteHTML($body);
        $mpdf->Output(str_replace('BQ-','',$id_bq)." ".date('dmYHis').".pdf" ,'I');
	}
	
	public function print_cetak_usd(){
		$sroot 		= $this->sroot;
		include $sroot."/application/libraries/MPDF57/mpdf.php";
	  
		$data_session	= $this->session->userdata;
		$id_bq   		= $this->uri->segment(3);
		$no_ipp   		= str_replace('BQ-','',$id_bq);

		$get_id_cust	= get_name('production','id_customer','no_ipp',$no_ipp);
		$alamat_cust	= get_name('customer','alamat','id_customer',$get_id_cust);
		$telephone		= get_name('customer','telpon','id_customer',$get_id_cust);
		
		$mpdf= new mPDF('utf-8','A4');
		$mpdf->SetImportUse();
		
		$get_header = $this->db->get_where('cost_project_header_sales', array('id_bq'=>$id_bq))->result();
		
		$sqlResin = "(SELECT id_material, nm_material  FROM bq_component_detail WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)
					 UNION
					(SELECT id_material, nm_material  FROM bq_component_detail_plus WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)";
		$ListBQipp		= $this->db->query($sqlResin)->result_array();
		$dtListArray = array();
		foreach($ListBQipp AS $val => $valx){
			$dtListArray[$val] = $valx['nm_material'];
		}
		$dtImplode	= "".implode(",  ", $dtListArray)."";
			
		$customer = (!empty($get_header[0]->customer))?strtoupper($get_header[0]->customer):'-';
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();
		$sql_detail 	= "	SELECT
							a.id_bq,
							a.id AS id_milik,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							c.total_price_last AS cost,
							c.est_material
						FROM
							bq_detail_header a 
							LEFT JOIN cost_project_detail b ON a.id=b.caregory_sub
							LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
						WHERE
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."' AND a.id_category != 'product kosong'";		
		$rest_detail	= $this->db->query($sql_detail)->result_array();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.* ,
								b.qty AS qty_berat
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material AND a.id_milik = b.id
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		
		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
		$enggenering	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
		$packing	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
		$export	= $this->db->query($gTruck)->result_array();

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
						ORDER BY
							b.id ASC ";
		$local	= $this->db->query($engCPCV)->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'header'				=> $get_header,
			'sroot'					=> $sroot,
			'id_bq'					=> $id_bq,
			'subject'				=> (!empty($get_header[0]->subject))?ucfirst($get_header[0]->subject):'-',
			'product'				=> (!empty($get_header[0]->product))?strtoupper($get_header[0]->product):'-',
			'pengiriman'			=> (!empty($get_header[0]->pengiriman))?strtoupper($get_header[0]->pengiriman):'-',
			'sales'					=> (!empty($get_header[0]->sales))?ucwords($get_header[0]->sales):'-',
			'jangka_waktu_penawaran'=> (!empty($get_header[0]->jangka_waktu_penawaran))?ucfirst($get_header[0]->jangka_waktu_penawaran):'-',
			'garansi_porduct'		=> (!empty($get_header[0]->garansi_porduct))?ucfirst($get_header[0]->garansi_porduct):'-',
			'tahap_pembayaran'		=> (!empty($get_header[0]->tahap_pembayaran))?ucfirst($get_header[0]->tahap_pembayaran):'-',
			'waktu_pengiriman'		=> (!empty($get_header[0]->waktu_pengiriman))?ucfirst($get_header[0]->waktu_pengiriman):'-',
			'customer'				=> $customer,
			'resin'					=> strtoupper($dtImplode),
			'quo_number'			=> (!empty($get_header[0]->quo_number))?strtoupper($get_header[0]->quo_number):'-',
			'job_number'			=> (!empty($get_header[0]->job_number))?strtoupper($get_header[0]->job_number):'-',
			'attn'					=> (!empty($get_header[0]->attn))?ucfirst($get_header[0]->attn):'-',
			'detail_product'		=> $rest_detail,
			'otherArray'		=> $otherArray,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'enggenering'	=> $enggenering,
			'packing'		=> $packing,
			'export'		=> $export,
			'local'			=> $local,
			'alamat_cust'	=> $alamat_cust,
			'telephone'		=> $telephone,
			'kurs'			=> $get_header[0]->kurs
		);
        
		
		$header2 	= $this->load->view('Print/print_quo_cetak_header', $data, TRUE);
        $body 		= $this->load->view('Print/print_quo_cetak_body_usd', $data, TRUE);
		$footer2 	= "<img src='".$sroot."/assets/images/footer_quo.png' alt='' width='100%'>"; 
		
		$header = array (
			'odd' => array (
				'C' => array (
					'content' => $header2
				),
				'line' => 0,
			),
			'even' => array ()
		);
		
		$footer = array (
			'odd' => array (
				'C' => array (
					'content' => $footer2,
					'width' => '100%'
				),
				'line' => 0,
			),
			'even' => array ()
		);
		
        $mpdf->SetHeader($header);
		$mpdf->SetFooter($footer);
		
		$mpdf->defaultheaderline = 0;
		
        $mpdf->AddPageByArray([
			'orientation' => 'P',
			'margin-top' => 40,
			'margin-bottom' => 15,
			'margin-left' => 0,
			'margin-right' => 0,
			'margin-header' => 0,
			'margin-footer' => 0,
			'line' => 0
		]);
		
		history('Print new quotation indonesia version / '.str_replace('BQ-','',$id_bq));
		
		$mpdf->SetTitle($id_bq);
        $mpdf->WriteHTML($body);
        $mpdf->Output(str_replace('BQ-','',$id_bq)." ".date('dmYHis').".pdf" ,'I');
	}
	
	public function print_cetak_eng(){
		$sroot 		= $this->sroot;
		include $sroot."/application/libraries/MPDF57/mpdf.php";
	  
		$data_session	= $this->session->userdata;
		$id_bq   		= $this->uri->segment(3);
		$no_ipp   		= str_replace('BQ-','',$id_bq);
		
		$get_id_cust	= get_name('production','id_customer','no_ipp',$no_ipp);
		$alamat_cust	= get_name('customer','alamat','id_customer',$get_id_cust);
		$telephone		= get_name('customer','telpon','id_customer',$get_id_cust);
		
		$mpdf= new mPDF('utf-8','A4');
		$mpdf->SetImportUse();
		
		$get_header = $this->db->get_where('cost_project_header_sales', array('id_bq'=>$id_bq))->result();
		
		$sqlResin = "(SELECT id_material, nm_material  FROM bq_component_detail WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)
					 UNION
					(SELECT id_material, nm_material  FROM bq_component_detail_plus WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)";
		$ListBQipp		= $this->db->query($sqlResin)->result_array();
		$dtListArray = array();
		foreach($ListBQipp AS $val => $valx){
			$dtListArray[$val] = $valx['nm_material'];
		}
		$dtImplode	= "".implode(",  ", $dtListArray)."";
			
		$customer = (!empty($get_header[0]->customer))?str_replace('Pt', 'PT', ucwords(strtolower($get_header[0]->customer))):'-';
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();
		$sql_detail 	= "	SELECT
							a.id_bq,
							a.id AS id_milik,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							c.total_price_last AS cost,
							c.est_material
						FROM
							bq_detail_header a 
							LEFT JOIN cost_project_detail b ON a.id=b.caregory_sub
							LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
						WHERE
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."' AND a.id_category != 'product kosong'";		
		// echo $sql_detail;
		// exit;
		$rest_detail	= $this->db->query($sql_detail)->result_array();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.*,
								b.qty AS qty_berat
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		
		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
		$enggenering	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
		$packing	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
		$export	= $this->db->query($gTruck)->result_array();

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
						ORDER BY
							b.id ASC ";
		$local	= $this->db->query($engCPCV)->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'header'				=> $get_header,
			'sroot'					=> $sroot,
			'id_bq'					=> $id_bq,
			'subject'				=> (!empty($get_header[0]->subject))?ucfirst($get_header[0]->subject):'-',
			'product'				=> (!empty($get_header[0]->product))?strtoupper($get_header[0]->product):'-',
			'pengiriman'			=> (!empty($get_header[0]->pengiriman))?strtoupper($get_header[0]->pengiriman):'-',
			'sales'					=> (!empty($get_header[0]->sales))?ucwords($get_header[0]->sales):'-',
			'jangka_waktu_penawaran'=> (!empty($get_header[0]->jangka_waktu_penawaran))?ucfirst($get_header[0]->jangka_waktu_penawaran):'-',
			'garansi_porduct'		=> (!empty($get_header[0]->garansi_porduct))?ucfirst($get_header[0]->garansi_porduct):'-',
			'tahap_pembayaran'		=> (!empty($get_header[0]->tahap_pembayaran))?ucfirst($get_header[0]->tahap_pembayaran):'-',
			'waktu_pengiriman'		=> (!empty($get_header[0]->waktu_pengiriman))?ucfirst($get_header[0]->waktu_pengiriman):'-',
			'customer'				=> $customer,
			'resin'					=> strtoupper($dtImplode),
			'quo_number'			=> (!empty($get_header[0]->quo_number))?strtoupper($get_header[0]->quo_number):'-',
			'job_number'			=> (!empty($get_header[0]->job_number))?strtoupper($get_header[0]->job_number):'-',
			'attn'					=> (!empty($get_header[0]->attn))?ucfirst($get_header[0]->attn):'-',
			'detail_product'		=> $rest_detail,
			'otherArray'		=> $otherArray,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'enggenering'	=> $enggenering,
			'packing'		=> $packing,
			'export'		=> $export,
			'local'			=> $local,
			'alamat_cust'	=> $alamat_cust,
			'telephone'		=> $telephone
		);
        
		
		$header2 	= $this->load->view('Print/print_quo_cetak_header', $data, TRUE);
        $body 		= $this->load->view('Print/print_quo_cetak_body_eng', $data, TRUE);
		$footer2 	= "<img src='".$sroot."/assets/images/footer_quo.png' alt='' width='100%'>"; 
		
		$header = array (
			'odd' => array (
				'C' => array (
					'content' => $header2
				),
				'line' => 0,
			),
			'even' => array ()
		);
		
		$footer = array (
			'odd' => array (
				'C' => array (
					'content' => $footer2,
					'width' => '100%'
				),
				'line' => 0,
			),
			'even' => array ()
		);
		
        $mpdf->SetHeader($header);
		$mpdf->SetFooter($footer);
		
		$mpdf->defaultheaderline = 0;
		
        $mpdf->AddPageByArray([
			'orientation' => 'P',
			'margin-top' => 40,
			'margin-bottom' => 15,
			'margin-left' => 0,
			'margin-right' => 0,
			'margin-header' => 0,
			'margin-footer' => 0,
			'line' => 0
		]);
		
		history('Print new quotation english version / '.str_replace('BQ-','',$id_bq));
			
		$mpdf->SetTitle($id_bq);
        $mpdf->WriteHTML($body);
        $mpdf->Output(str_replace('BQ-','',$id_bq)." ".date('dmYHis').".pdf" ,'I');
	}
	
}
