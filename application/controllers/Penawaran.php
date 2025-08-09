<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penawaran extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('penawaran_model');
		
		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	public function add_penawaran(){
		$this->penawaran_model->add_penawaran();
	}
	
	public function add_penawaran2(){
		$this->penawaran_model->add_penawaran2();
	}
	
	public function save_penawaran(){
		$this->penawaran_model->save_penawaran();
	}
	
	public function save_penawaran2(){
		$this->penawaran_model->save_penawaran2();
	}
	
	public function edit_penawaran_new(){
		$this->penawaran_model->edit_penawaran_new();
	}
	
	public function edit_penawaran_new2(){
		$this->penawaran_model->edit_penawaran_new2();
	}
	
	public function save_edit_penawaran_new(){
		$this->penawaran_model->save_edit_penawaran_new();
	}
	
	public function save_edit_penawaran_new2(){
		$this->penawaran_model->save_edit_penawaran_new2();
	}
	
	//==========================================================================================================
	//========================================PENAWARAN SALES===================================================
	//==========================================================================================================
	
	public function edit_penawaran_sales(){
		$this->penawaran_model->edit_penawaran_sales();
	}
	
	public function save_edit_penawaran_sales(){
		$this->penawaran_model->save_edit_penawaran_sales();
	}
	
	public function print_cetak(){
		$this->penawaran_model->print_cetak();
	}

	public function print_cetak_new(){
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
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."' AND a.id_category != 'product kosong' ORDER BY a.id ASC";		
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
        $body 		= $this->load->view('Print/print_quo_cetak_bodynew', $data, TRUE);
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
	
	public function print_cetak_eng(){
		$this->penawaran_model->print_cetak_eng();
	}
	
	public function print_cetak_usd(){
		$this->penawaran_model->print_cetak_usd();
	}
	
	
	
	
	
	public function priceProcessCost(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/priceProcessCost';
		// $Arr_Akses			= getAcccesmenu($controller);
		// if($Arr_Akses['update'] !='1'){
			// $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			// redirect(site_url('users'));
		// }
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		$qMatr 		= SQL_Quo($id_bq);
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT * FROM cost_engine ORDER BY id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		
		$engCPC 	= "SELECT * FROM list_help WHERE group_by = 'pack cost' ORDER BY id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		// $gTruck 	= "SELECT * FROM list_shipping WHERE flag = 'Y' ORDER BY urut ASC ";
		$gTruck 	= "SELECT
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') AND c.deleted='N')  as price
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
		
		
		$data = array(
			'title'		=> 'Offer Structure',
			'action'	=> 'updateReal',
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
			'getAreaL'		=> $getAreaL
		);
		$this->load->view('Penawaran/priceProcessCost',$data);
	}
	
	public function edit_penawaran(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/edit_penawaran';
		// $Arr_Akses			= getAcccesmenu($controller);
		// if($Arr_Akses['update'] !='1'){
			// $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			// redirect(site_url('users'));
		// }
		
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
		
		// $gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' ORDER BY a.urut ASC ";
		$gTruck 	= "SELECT
							e.*,
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') AND c.deleted='N')  as price
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
		
		
		$data = array(
			'title'		=> 'Offer Structure',
			'action'	=> 'updateReal',
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
			'getAreaL'		=> $getAreaL
		);
		$this->load->view('Penawaran/edit_penawaran',$data);
	}
	
	
	
	public function getFumigasi(){
		$category 	= $this->input->post('category');
		$type 		= $this->input->post('type');
		
		echo $category."-".$type;
		exit;
		
		$sqlSup		= "SELECT * FROM list_shipping WHERE shipping_name='N' AND  ORDER BY category ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Category</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_category']."'>".$valx['category']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getTujuan(){
		$data1 		= $this->input->post('data1');

		$sqlSup		= "SELECT tujuan FROM cost_trucking WHERE area='".$data1."' GROUP BY tujuan ORDER BY tujuan ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Destination</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['tujuan']."'>".strtoupper($valx['tujuan'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	
	public function getTruck(){
		$data1 		= $this->input->post('data1');
		$data2 		= $this->input->post('data2');

		$sqlSup		= "SELECT a.id_truck, b.nama_truck FROM cost_trucking a INNER JOIN truck b ON a.id_truck = b.id WHERE a.area='".$data1."' AND a.tujuan='".$data2."' GROUP BY id_truck ORDER BY b.nama_truck ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Truck</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_truck']."'>".strtoupper($valx['nama_truck'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getPriceTruck(){
		$data1 		= $this->input->post('data1');
		$data2 		= $this->input->post('data2');
		$data3 		= $this->input->post('data3');
		
		$qCur		= "SELECT price FROM cost_convert WHERE cur_1 = 'USD' AND cur_2 = 'IDR' LIMIT 1 ";
		$getCur		= $this->db->query($qCur)->result_array();

		$sqlSup		= "SELECT price FROM cost_trucking WHERE area='".$data1."' AND tujuan='".$data2."' AND id_truck='".$data3."' LIMIT 1";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$ArrJson	= array(
			'price' => ($restSup[0]['price'] / $getCur[0]['price'])
		);
		echo json_encode($ArrJson);
	}
	
	public function save_cost_project(){
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
			$ArrMatCost[$val]['extra']			= $valx['extra'];
			$ArrMatCost[$val]['persen']			= $valx['persen'];
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
		
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		// print_r($ArrExportCost);
		// print_r($LokalCost); 
		
		
		// exit;
		
		$this->db->trans_start();
			$this->db->insert('cost_project_header', $ArrHeader);
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
			
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
			history('Cost Quotation with bq : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	public function edit_cost_project(){
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
		
		$restHistHeader = $this->db->query("SELECT * FROM cost_project_header WHERE id_bq='".$data['id_bq']."' ")->result_array();
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
		
		$restHistDetail = $this->db->query("SELECT * FROM cost_project_detail WHERE id_bq='".$data['id_bq']."' ")->result_array();
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
			
			$this->db->where("category <> 'nonfrp'");
			$this->db->where("category <> 'aksesoris'");
			$this->db->delete('cost_project_detail', array('id_bq' => $data['id_bq']));  
			
			$this->db->insert('cost_project_header', $ArrHeader);
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);

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
			history('Edit Cost Quotation with bq : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	
	
	
	public function print_penawaran(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPenawaran.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Penawaran Sales Project BQ '.$id_bq); 
		
		PrintHasilPenawaran($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function print_penawaran2(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPenawaran.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Penawaran Sales Project BQ '.$id_bq); 
		
		PrintHasilPenawaran2($Nama_Beda, $koneksi, $printby, $id_bq);
	}

	// public function print_penawaran3(){
		// $id_bq	= $this->uri->segment(3);
		// $rev	= $this->uri->segment(4);
		// $data_session	= $this->session->userdata;
		// $printby		= $data_session['ORI_User']['username'];
		// $koneksi		= akses_server_side();
		
		// include 'plusPrintPenawaran.php';
		// $data_url		= base_url();
		// $Split_Beda		= explode('/',$data_url);
		// $Jum_Beda		= count($Split_Beda);
		// $Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		// history('Print Hasil Penawaran Revised '.$rev.' Project BQ '.$id_bq); 
		
		// PrintHasilPenawaran3($Nama_Beda, $koneksi, $printby, $id_bq, $rev); 
	// }
	
	public function print_penawaran3(){
		$id_bq	= $this->uri->segment(3);
		$rev	= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id_bq' => $id_bq,
			'rev'	=> $rev
		);
		history('Print Hasil Penawaran Set Buttom Price '.$id_bq);
		$this->load->view('Print/print_hist_set_buttom_price', $data);
		
		
	}
	
	public function print_penawaran4(){
		$id_bq			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id_bq' => $id_bq
		);
		// history('Print Hasil Penawaran Sales Project BQ '.$id_bq);
		$this->load->view('Print/print_penawaran', $data);
	}

	public function print_sales_order(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPenawaran.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Sales Order BQ '.$id_bq); 
		
		PrintSalesOrder($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function set_a(){
		$this->load->view('Penawaran/set_engine/set_a');
	}
	
	public function set_b(){
		$this->load->view('Penawaran/set_engine/set_b');
	}
	
	public function set_c(){
		$this->load->view('Penawaran/set_engine/set_c');
	}
	
	public function set_d(){
		$this->load->view('Penawaran/set_engine/set_d');
	}
	
	public function set_e(){
		$this->load->view('Penawaran/set_engine/set_e');
	}
	
	public function set_f(){
		$this->load->view('Penawaran/set_engine/set_f');
	}
	
	public function set_g(){
		$this->load->view('Penawaran/set_engine/set_g');
	}
	
	public function update_set_a(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$ArrEditCost = array(
			'value1'		=> $data['value1'],
			'value2'		=> $data['value2'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', '1');
			$this->db->update('cost_engine', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $data['idbq']
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $data['idbq']
			);				
			history('Update Value axial tensile strength tests ');
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_set_b(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$ArrEditCost = array(
			'value1'		=> $data['value1'],
			'value2'		=> $data['value2'],
			'value3'		=> $data['value1x'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', '2');
			$this->db->update('cost_engine', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $data['idbq']
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $data['idbq']
			);				
			history('Update Value flexural properties ');
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_set_c(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$ArrEditCost = array(
			'value1'		=> $data['value1'],
			'value2'		=> $data['value2'],
			'value3'		=> $data['value1x'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', '3');
			$this->db->update('cost_engine', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $data['idbq']
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $data['idbq']
			);				
			history('Update Value weld joint test, hoop tensile strength tests, fitt ');
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_set_d(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$ArrEditCost = array(
			'value1'		=> $data['value1'],
			'value2'		=> $data['value2'],
			'value3'		=> $data['value1x'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', '4');
			$this->db->update('cost_engine', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $data['idbq']
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $data['idbq']
			);				
			history('Update Value proof test ');
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_set_e(){ 
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$ArrEditCost = array(
			'value1'		=> $data['value1'],
			'value2'		=> $data['value2'],
			'value3'		=> $data['value1x'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', '5');
			$this->db->update('cost_engine', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $data['idbq']
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $data['idbq']
			);				
			history('Update Value beam strengt test ');
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_set_f(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$ArrEditCost = array(
			'value1'		=> $data['value1'],
			'value2'		=> $data['value2'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', '6');
			$this->db->update('cost_engine', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $data['idbq']
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $data['idbq']
			);				
			history('Update Value manufacturing data report ');
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_set_g(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$ArrEditCost = array(
			'value1'		=> $data['value1'],
			'value2'		=> $data['value2'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id', '7');
			$this->db->update('cost_engine', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $data['idbq']
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $data['idbq']
			);				
			history('Update Value stress analisys ');
		}
		echo json_encode($Arr_Data);
	}

	public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left' colspan='9'>";
				$d_Header .= "<input type='text' name='DetailOther[".$id."][desc]' class='form-control input-md' placeholder='Description'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='DetailOther[".$id."][unit_price]' class='form-control input-md text-right autoNumeric4 changeOther otherUnitPrice' placeholder='Unit Price'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='DetailOther[".$id."][qty]' class='form-control input-md text-center autoNumeric4 changeOther otherQty' placeholder='Qty'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input type='text' name='DetailOther[".$id."][total_price]' class='form-control input-md text-right autoNumeric4 otherTotalPrice' readonly>";
			$d_Header .= "</td>";
		
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='left' colspan='9'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-warning addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}
	
}