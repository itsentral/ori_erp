<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_control extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('project_finish_model');
		$this->load->model('project_process_model');
		
		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	//==========================================================================================================================
	//======================================================FINISH PROJECT=========================================================
	//==========================================================================================================================
	
	public function cost_control(){
		$this->project_finish_model->index_cost_control();
	}
	
	public function server_side_cost_control(){
		$this->project_finish_model->get_data_json_cost_control();
	}
	
	public function view_modal_view_dt(){
		$this->project_finish_model->view_dt_cost_control();
	}
	
	public function view_modal_total_material(){
		$this->project_finish_model->view_total_material_cost_control();
	}
	
	public function print_total_material(){
		$this->project_finish_model->print_total_material();
	}
	
	public function modal_detail_cost(){
		$this->project_finish_model->view_detail_cost_control();
	}
	
	public function printCostControl(){ 
		$this->project_finish_model->print_finish_per_product();
	}
	
	public function print_hasil_finish_project(){ 
		$this->project_finish_model->print_hasil_finish_project();
	}
	
	function insert_select_finish(){
		$this->project_finish_model->insert_select_finish();
	}
	
	//==========================================================================================================================
	//==================================================END FINISH PROJECT======================================================
	//==========================================================================================================================
	
	//==========================================================================================================================
	//======================================================ON PROGRESS=========================================================
	//==========================================================================================================================
	
	public function on_progress(){
		$this->project_process_model->index_on_progress();
	}
	
	public function server_side_on_progress(){
		$this->project_process_model->get_data_json_process();
	}
	
	public function priceReal2(){
		$this->project_process_model->on_progress_satuan();
	}
	
	//==========================================================================================================================
	//=====================================================END ON PROGRESS======================================================
	//==========================================================================================================================
	
	public function index(){ 
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$id_price		 	= $this->uri->segment(3);
		$get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.parent_product='".$id_price."' AND a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Estimation Product Price',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Estimation Product Price');
		$this->load->view('Cost_control/index',$data);
	}
	
	public function ajukan_price(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$id_price		 	= $this->uri->segment(3);
		$get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		$getProduct			= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Estimation Submit',
			'action'		=> 'index',
			'listparent'	=> $getProduct,
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Pengajuan Product Price');
		$this->load->view('Cost_control/ajukan_price',$data);
	}
	
	public function ajukan_price2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$id_price		 	= $this->uri->segment(3);
		// $get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM product_header a LEFT JOIN customer b ON b.id_customer=a.id_customer WHERE a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		$getProduct			= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Estimation Submit',
			'action'		=> 'index',
			'listparent'	=> $getProduct,
			// 'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		// history('View Pengajuan Product Price');
		$this->load->view('Cost_control/ajukan_price2',$data);
	}
	
	public function app_mat(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.sts_price='WAITING APPROVE' AND a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Waiting Approval perMaterial',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Approval per Material');
		$this->load->view('Cost_control/app_mat',$data);
	}
	
	
	public function modalDetailProcess(){
		$this->load->view('Cost_control/modalDetailProcess');
	}
	
	public function modalDetail(){
		$this->load->view('Cost_control/modalDetail');
	}
	
	public function modalDetailMat(){
		$this->load->view('Cost_control/modalDetailMat');
	}
	
	
	
	public function modalPrice(){
		$this->load->view('Cost_control/modalPrice');
	}
	
	public function modalAppMat(){
		$this->load->view('Cost_control/modalAppMat');
	}
	
	public function modalAppCost(){
		$this->load->view('Cost_control/modalAppCost');
	}
	
	public function AppCost(){
		$id_bq 			= $this->uri->segment(3);
		$Imp			= explode('-', $id_bq);
		
		$data_session	= $this->session->userdata;
		
		$stsX			= ($this->input->post('status') == 'Y')?'ALREADY ESTIMATED PRICE':'WAITING EST PRICE PROJECT';
		
		$Arr_Edit	= array(
			'status' => $stsX,
			'sts_price_reason' => $this->input->post('approve_reason'),
			'sts_price_by' => $data_session['ORI_User']['username'],
			'sts_price_date' => date('Y-m-d H:i:s')
		);
		
		$ArrInsert	= array(
			'id_bq' => $id_bq,
			'cost_material' => str_replace(',', '', $this->input->post('total_kg')),
			'cost_total' => str_replace(',', '', $this->input->post('total_cost')),
			'status' => 'Y',
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		);
		
		//insert semua total harga di price
		
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $Imp[1]);
			$this->db->update('production', $Arr_Edit);
			
			$this->db->insert('bq_price_project', $ArrInsert);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve success. Thanks ...',
				'status'	=> 1
			);				
			history('Approve/Reject Project Price with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function approveMat(){
		$id_produk 	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$stsX	= ($this->input->post('status') == 'Y')?'REGISTERED':'UNREGISTERED';
		
		$sqlDet2		= "SELECT * FROM component_header WHERE id_product = '".$id_produk."' ";
		$restDet2	= $this->db->query($sqlDet2)->result();
		
		$Arr_Edit	= array(
			// 'rev'		=> $restDet2[0]->rev + 1,
			'sts_price' => $stsX,
			'sts_price_reason' => $this->input->post('approve_reason'),
			'sts_price_by' => $data_session['ORI_User']['username'],
			'sts_price_date' => date('Y-m-d H:i:s')
		);
		$Arr_Price	= array(
			'status' => $this->input->post('status'),
			'approve_by' => $data_session['ORI_User']['username'],
			'approve_date' => date('Y-m-d H:i:s')
		);
		// print_r($Arr_Edit);
		// print_r($Arr_Price);
		// exit;
		$this->db->trans_start();
		$this->db->where('id_product', $id_produk);
		$this->db->update('component_header', $Arr_Edit);
		
		$this->db->where('id_product', $id_produk);
		$this->db->update('component_price', $Arr_Price);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve success. Thanks ...',
				'status'	=> 1
			);				
			history('Approve/Reject Price Comp with Kode : '.$id_produk);
		}
		echo json_encode($Arr_Data);
	}
	
	public function printSPK(){
		$kode_product	= $this->uri->segment(3);
		// $kodeSPJ		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
	
		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		PrintSPKOri($Nama_Beda, $kode_product, $koneksi, $printby);
	}
	
	public function project(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Estimation Project Price',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Estimation Project Price');
		$this->load->view('Cost_control/project',$data);
	}
	
	
	
	
	
	public function getDataJSON2x(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/cost_control';
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON2x(
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_mat'], 3)." Kg</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_harga'], 2)."</div>";
			
			if($uri_code == 'cost_control'){
				$nestedData[]	= "<div align='right'>".number_format($row['real_material'], 3)." Kg</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['real_harga'], 2)."</div>";
			}
			
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#ce9021'>".$row['rev']."</span></div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND (id_product= '' OR id_product  is null) ")->num_rows();
				
				$warna = Color_status($row['sts_ipp']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$updXCost	= "";
					$ApprvX	= "";
					$ApprvX2	= "";
					$viewX	= "";
					if($row['estimasi']=='Y'){
						$viewX	= "<button class='btn btn-sm btn-primary' id='viewDT' title='View Data' data-id_bq='".$row['id_bq']."' data-cost_control='cost_control'><i class='fa fa-eye'></i></button>";
					}
					
					if($Arr_Akses['update']=='1'){
						if(!empty($row['id_produksi'])){
							$updX	= "&nbsp;<a href='".base_url('cost_control/priceReal2/'.$row['id_produksi'])."' class='btn btn-sm btn-success' title='Detail Material Price ' data-role='qtip'><i class='fa fa-money'></i></a>";
						}
					}
					
					if($uri_code == 'cost_control'){
						$updXCost	= "&nbsp;<button class='btn btn-sm btn-warning' id='TotalCost' title='Total All Material' data-id_bq='".$row['id_bq']."'><i class='fa fa-money'></i></button>";
					}
					//<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
			$nestedData[]	= "<div align='left'>
									".$priX."
									".$viewX."
									".$updX."
									".$ApprvX."
									".$ApprvX2."
									".$updXCost."
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

	public function queryDataJSON2x($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$sql = "
			SELECT
				a.*
			FROM
				group_cost_project_process_table a
		    WHERE 
				1=1
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'nm_customer'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function priceReal(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		// if($Arr_Akses['update'] !='1'){
			// $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			// redirect(site_url('users'));
		// }
		
		$id_produksi	= $this->uri->segment(3);
		$id_produksi = $this->uri->segment(3);

		$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row	= $this->db->query($qSupplier)->result_array();

		$qDetail	= "	SELECT 
							a.*, 
							b.nm_product,
							c.delivery_name
						FROM 
							production_detail a 
							LEFT JOIN bq_component_header b ON a.id_milik=b.id_milik 
							LEFT JOIN delivery c ON a.id_delivery=c.id_delivery 
						WHERE 
							a.id_produksi = '".$id_produksi."' AND a.id_category <> 'pipe slongsong' ";
		$rowD	= $this->db->query($qDetail)->result_array();
		
		$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN bq_component_header b ON a.id_milik=b.id_milik WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
		$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();
		
		$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN bq_component_header b ON a.id_milik=b.id_milik WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
		$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
		// echo $qDetailBtn;
		$data = array(
			'title'		=> 'Price Estimasi Detail',
			'action'	=> 'updateReal',
			'row'		=> $row,
			'rowD'		=> $rowD,
			'numB'		=> $rowDBtn,
			'numB2'		=> $rowDBtn2
		);
		$this->load->view('Cost_control/priceReal',$data);
	}
	
	public function priceProcessCost(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('users'));
		}
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		$qMatr 		= "SELECT a.*, b.* FROM estimasi_cost_and_mat a INNER JOIN bq_detail_header b ON a.id_milik=b.id WHERE a.id_bq = '".$id_bq."' ";
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT * FROM list_help WHERE group_by = 'eng cost' ORDER BY id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		
		$engCPC 	= "SELECT * FROM list_help WHERE group_by = 'pack cost' ORDER BY id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		$gTruck 	= "SELECT * FROM list_shipping WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$gTruckP 	= "SELECT * FROM list_packing WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruckP	= $this->db->query($gTruckP)->result_array();
		
		$engCPCV 	= "SELECT * FROM list_help WHERE group_by = 'via' ORDER BY id ASC ";
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
		$this->load->view('Cost_control/priceProcessCost',$data);
	}
	
	public function modalDetailPrice(){
		$this->load->view('Cost_control/modalDetailPrice');
	}
	
	public function modalDetailPriceDetail(){
		$this->load->view('Cost_control/modalDetailPriceDetail');
	}
	
	public function modalDetailBQ(){
		$this->load->view('Cost_control/modalDetailBQ');
	}
	
	
	
	
	
	public function modalDetailDT(){
		$this->load->view('Cost_control/modalDetailDT');
	}
	
	public function printPriceperMat(){ 
		$kode_produksi	= $this->uri->segment(3);
		$kode_product	= $this->uri->segment(4);
		$product_to		= $this->uri->segment(5);
		$id_delivery	= $this->uri->segment(6);
		$id				= $this->uri->segment(7);
		$id_milik		= $this->uri->segment(8);
		$qty_awal		= $this->uri->segment(9);
		$qty_akhir		= $this->uri->segment(10);

		$qty_total = ($qty_akhir - $qty_awal) + 1;
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");
		// echo $data_url;
		// exit;
		
		// $sqlUpdate = "UPDATE production_detail SET print_to=print_to +1 WHERE id='".$id."'";
		// $this->db->query($sqlUpdate);
		// echo $sqlUpdate; exit; 
		// history('Print SPK Production '.$kode_produksi.'/'.$kode_product); 
		
		PrintPricePerComp($Nama_Beda, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery, $id, $id_milik, $qty_total, $qty_awal, $qty_akhir);
	}
	
	public function PrintHasilProjectPerBQ(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Perbandingan Project BQ '.$id_bq); 
		
		PrintHasilProjectPerBQFinish($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function PrintTotalMaterial(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Total Material Project BQ '.$id_bq); 
		
		PrintTotalMaterial($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function updatePrice(){
		$ArrKembali = array();
		$data		= $this->input->post();
		$dataID		= $data['id_product'];
		$priceMat	= str_replace(',', '', $data['product_price']);
		
		$sqlDet		= "SELECT * FROM component_price WHERE id_product = '".$dataID."' ";
		$restDet	= $this->db->query($sqlDet)->result();
		$numDet		= $this->db->query($sqlDet)->num_rows();
		
		$sqlDet2		= "SELECT * FROM component_header WHERE id_product = '".$dataID."' ";
		$restDet2	= $this->db->query($sqlDet2)->result();
		
		// echo $numDet;
		
		$dataInsert = array (
			'id_product'	=> $dataID,
			'product_price' => $priceMat,
			'revisi_ke'		=> 0,
			'modified_by'	=> $this->session->userdata['ORI_User']['username'],
			'modified_date'	=> date('Y-m-d H:i:s')
		);
		if($numDet > 0){
			$dataUpdate = array (
				'id_product'	=> $dataID,
				'product_price' => $priceMat,
				'revisi_ke'		=> $restDet[0]->revisi_ke + 1,
				'modified_by'	=> $this->session->userdata['ORI_User']['username'],
				'modified_date'	=> date('Y-m-d H:i:s')
			);
		}
		
		$ArrUpdate	= array(
			
			'sts_price'		=> 'WAITING APPROVE',
			'sts_price_by'	=> $this->session->userdata['ORI_User']['username'],
			'sts_price_date'	=> date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
			if($numDet == 0){
				$this->db->insert('component_price', $dataInsert);
				$this->db->insert('hist_component_price', $dataInsert);
			}
			else{
				$this->db->insert('hist_component_price', $dataUpdate);
				
				$this->db->where('id_product', $dataID);
				$this->db->update('component_price', $dataUpdate);
				
				$this->db->where('id_product', $dataID);
				$this->db->update('component_header', $ArrUpdate);
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Add price data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Add price data success. Thanks ...',
				'status'	=> 1
			);
			history("Add Price ID ".$dataID);
		}
		
		echo json_encode($Arr_Kembali);
	}
	
	//Pengajuan Harga Material Per Komponent
	public function getDataJSONKomp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONKomp(
			$requestData['product'], 
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
				$detail = "";
				if(strtolower($row['parent_product']) == 'pipe'){
					$detail = "(".$row['diameter']." x ".$row['panjang']." x ".$row['design'].")";
				}
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>"; 
			$nestedData[]	= "<div align='left'>".$row['nm_product']." ".$detail."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['standart_toleransi']."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['aplikasi_product']."</div>"; 
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($row['created_by']))."</div>";   
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['rev']."</span></div>";
				if($row['sts_price'] == 'REGISTERED'){
					$warna = 'bg-green';
				}
				elseif($row['sts_price'] == 'UNREGISTERED'){
					$warna = 'bg-red';
				}
				else{
					$warna = 'bg-blue';
				}
			$nestedData[]	= "<div align='left'><span class='badge ".$warna."'>".$row['sts_price']."</span></div>";
					$delX	= "";
					$updX	= "";
					$priX	= "";
					if($row['sts_price'] == 'UNREGISTERED'){
						if($Arr_Akses['update']=='1'){
							$updX	= "&nbsp;<button type='button' id='MatPrice' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-success' title='Registered Now' data-role='qtip'><i class='fa fa-edit'></i></button>";
						}
					}
			$nestedData[]	= "<div align='left'>
									<button type='button' id='MatDetail' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$priX."
									".$updX."
									".$delX."
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

	public function queryDataJSONKomp($product, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_product = "";
		if(!empty($product)){
			$where_product = " AND a.parent_product = '".$product."' ";
		}
		
		$sql = "
			SELECT 
				a.*, b.nm_customer 
			FROM 
				component_header a 
				LEFT JOIN customer b ON b.id_customer=a.standart_by  
			WHERE 1=1 
				".$where_product."
				AND a.status='APPROVED' AND a.deleted ='N' AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_product LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'nm_product',
			3 => 'standart_toleransi',
			4 => 'aplikasi_product',
			5 => 'created_by',
			6 => 'rev'
		);

		$sql .= " ORDER BY a.sts_price DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	
	//======================================================EXCEL===============================================================
	public function ExcelPerbandinganBefore(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_product		= $this->uri->segment(3);
		$id_produksi	= $this->uri->segment(4);
		$id_delivery	= $this->uri->segment(5);
		$id_production	= $this->uri->segment(6);
		$produk_ke		= $this->uri->segment(7);
		$nama_produk	= $this->uri->segment(8);
		$id_milik		= $this->uri->segment(9);
		$qty_awal 	= floatval($this->uri->segment(10));
		$qty_akhir 	= floatval($this->uri->segment(11));

		$qty_total = ($qty_akhir - $qty_awal) + 1;

		$qChMix	= "	SELECT a.* FROM update_real_list_mixing a
					WHERE a.id_milik = '".$id_milik."' 
					AND (('".$qty_awal."' BETWEEN a.qty_awal AND a.qty_akhir )
					OR ('".$qty_akhir."' BETWEEN a.qty_awal AND a.qty_akhir ))
					";
		// echo $qChMix;
		$rowMix		= $this->db->query($qChMix)->result_array();
		$qty_awal2 	= floatval($rowMix[0]['qty_awal']);
		$qty_akhir2 = floatval($rowMix[0]['qty_akhir']);
		$qty_total2 = ($qty_akhir2 - $qty_awal2) + 1;
		$id_mixing 	= $rowMix[0]['id'];

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
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'PERBANDINGAN ESTIMASI VS ACTUAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Layer');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Category Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Material Name');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Price (USD)');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Est Total');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Est Sub (USD)');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Real Total');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Real Sub (USD)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);
		
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_component_header";
		$HelpDet2 	= "bq_component_detail";
		$HelpDet3 	= "bq_component_detail_plus";
		$HelpDet4 	= "bq_component_detail_add";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet 	= "so_component_header";
			$HelpDet2 	= "so_component_detail";
			$HelpDet3 	= "so_component_detail_plus";
			$HelpDet4 	= "so_component_detail_add";
		}
		$qHeader		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."'";
		$restHeader		= $this->db->query($qHeader)->result_array();
		
		$qDetail1		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$qDetail1		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='GLASS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail";
		}
		$qDetail2		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$qDetail2		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail";
		}
		$qDetail2N1		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
		$qDetail2N2		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
		$qDetail3		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
		$detailResin1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
		$detailResin2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
		$detailResin2N1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
		$detailResin2N2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
		$detailResin3	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
		$qDetailPlus1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail";
		$qDetailPlus2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
		$qDetailPlus2N1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
		$qDetailPlus2N2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
		$qDetailPlus3	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
		$qDetailPlus4	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
		$qDetailAdd1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
		$qDetailAdd2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
		$qDetailAdd2N1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
		$qDetailAdd2N2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
		$qDetailAdd3	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
		$qDetailAdd4	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
		
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail2N1	= $this->db->query($qDetail2N1)->result_array();
		$restDetail2N2	= $this->db->query($qDetail2N2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$numRows3		= $this->db->query($qDetail3)->num_rows();
		$numRows2N1		= $this->db->query($qDetail2N1)->num_rows();
		$numRows2N2		= $this->db->query($qDetail2N2)->num_rows();
		$restResin1			= $this->db->query($detailResin1)->result_array();
		$restResin2			= $this->db->query($detailResin2)->result_array();
		$restResin2N1		= $this->db->query($detailResin2N1)->result_array();
		$restResin2N2		= $this->db->query($detailResin2N2)->result_array();
		$restResin3			= $this->db->query($detailResin3)->result_array();
		$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
		$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
		$restDetailPlus2N1	= $this->db->query($qDetailPlus2N1)->result_array();
		$restDetailPlus2N2	= $this->db->query($qDetailPlus2N2)->result_array();
		$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
		$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
		$NumDetailPlus4		= $this->db->query($qDetailPlus4)->num_rows();
		$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
		$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
		$restDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->result_array();
		$restDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->result_array();
		$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
		$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
		//Liner Detail
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Liner Resin
		if($restResin1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Liner Plus
		if($restDetailPlus1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailPlus1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Liner Add
		if($restDetailAdd1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailAdd1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N1 Detail
		if($restDetail2N1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail2N1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Structure N1 Resin
		if($restResin2N1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin2N1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N1 Plus
		if($restDetailPlus2N1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailPlus2N1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N1 Add
		if($restDetailAdd2N1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailAdd2N1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N2 Detail
		if($restDetail2N2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail2N2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Structure N2 Resin
		if($restResin2N2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin2N2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N2 Plus
		if($restDetailPlus2N2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailPlus2N2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N2 Add
		if($restDetailAdd2N2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailAdd2N2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure Detail
		if($restDetail2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Structure Resin
		if($restResin2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure Plus
		if($restDetailPlus2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailPlus2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure Add
		if($restDetailAdd2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailAdd2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Eksternal Detail
		if($restDetail3){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail3 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Eksternal Resin
		if($restResin3){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin3 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1 = (!empty($row_Cek['material_terpakai'])?str_replace(',','.',$row_Cek['material_terpakai']):0);
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Eksternal Plus
		if($restDetailPlus3){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailPlus3 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Eksternal Add
		if($restDetailAdd3){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailAdd3 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//TopCoat Plus
		if($restDetailPlus4){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailPlus4 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//TopCoat Add
		if($restDetailAdd4){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetailAdd4 as $key => $row_Cek){
				$no++;
				$awal_row++; 
				$awal_col	= 0;

				$mat_terpakai1Bf	= (!empty($row_Cek['material_terpakai']))? str_replace(',','.',$row_Cek['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $row_Cek['last_cost'] * $row_Cek['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $row_Cek['price_mat'];
				$priceMat	= $row_Cek['price_mat'];
				$LastCost	= $row_Cek['last_cost'];
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$cost		= $priceMat;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $LastCost;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga		= $TotPrice;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_material	= $mat_terpakai1;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$real_harga		= $TotPrice2;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		
		
		$sheet->setTitle('Perbandingan');
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
		header('Content-Disposition: attachment;filename="PERBANDINGAN_'.str_replace('PRO-','',$id_produksi).'_'.strtoupper($rowMix[0]['id_category']).'_'.$nama_produk.'_('.$qty_awal.'-'.$qty_akhir.')_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function ExcelPerbandingan(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_produksi	= $this->uri->segment(3);
		$no_ipp			= str_replace('PRO-','',$id_produksi);
		$id_milik		= $this->uri->segment(4);
		$qty_awal 		= floatval($this->uri->segment(5));
		$qty_akhir 		= floatval($this->uri->segment(6));
		$qty_total		= $this->uri->segment(7);
		$nm_product		= $this->uri->segment(8);

		$qty 			= ($qty_akhir - $qty_awal) + 1;

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
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'PERBANDINGAN ESTIMASI VS ACTUAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Layer');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Category Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Material Name');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Price (USD)');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Est Total');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Est Sub (USD)');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Real Total');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Real Sub (USD)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);
		
		$qDetail1		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'LINER THIKNESS / CB' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'LINER THIKNESS / CB' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail2		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail3		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail4		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail5		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail6		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
							";
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$restDetail4	= $this->db->query($qDetail4)->result_array();
		$restDetail5	= $this->db->query($qDetail5)->result_array();
		$restDetail6	= $this->db->query($qDetail6)->result_array();
		$restDetail		= array_merge($restDetail1,$restDetail2,$restDetail3, $restDetail4, $restDetail5, $restDetail6);
		
		if($restDetail){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$estHarga = $est_material * $est_harga;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estHarga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$real_material	= 0;
				if($row_Cek['real_material'] > 0){
					$real_material	= (!empty($row_Cek['real_material']))?($row_Cek['real_material']/$qty_total) * $qty:0;
				}
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$estHargaReal = $real_material * $est_harga;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estHargaReal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				// $awal_col++;
				// $selisih = 0;
				// if($est_material > 0 AND $real_material > 0){
				// 	$selisih = $real_material / $est_material * 100;
				// }
				// $Cols = getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, number_format($selisih,2));
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
			}
		}
		
		
		$sheet->setTitle('Perbandingan');
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
		header('Content-Disposition: attachment;filename="perbandingan-'.$nm_product.'-('.$qty.').xls"');
		//unduh file
		$objWriter->save("php://output");
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

		$sqlSup		= "SELECT price FROM cost_trucking WHERE area='".$data1."' AND tujuan='".$data2."' AND id_truck='".$data3."' LIMIT 1";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$ArrJson	= array(
			'price' => $restSup[0]['price']
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
			$ArrMatCost[$val]['persen']			= $valx['persen'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= $valx['unit'];
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
		
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		// print_r($ArrExportCost);
		// print_r($LokalCost); 
		
		
		exit;
		
		$this->db->trans_start();
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
			history('Cost Quotation with bq : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//DUPLIKAT
	public function modalDetail2(){
		$this->load->view('Cost_control/modalDetail2');
	}
	
	public function getDataJSONKomp2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONKomp2(
			// $requestData['product'], 
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
				$detail = "";
				if(strtolower($row['parent_product']) == 'pipe'){
					$detail = "(".$row['diameter']." x ".$row['panjang']." x ".$row['design'].")";
				}
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['id_product']."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['nm_product']." ".$detail."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['standart_toleransi']."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['aplikasi_product']."</div>"; 
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($row['created_by']))."</div>";   
				
					$delX	= "";
					$updX	= "";
					$priX	= "";
					
			$nestedData[]	= "<div align='center'>
									<button type='button' id='MatDetail' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$priX."
									".$updX."
									".$delX."
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

	public function queryDataJSONKomp2($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		
		$sql = "
			SELECT 
				a.*, b.nm_customer 
			FROM 
				product_header a 
				LEFT JOIN customer b ON b.id_customer=a.id_customer  
			WHERE id_product='PDK-19050030' OR id_product='PDK-19050031' OR id_product='PDK-19050032'
				
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'nm_product',
			3 => 'standart_toleransi',
			4 => 'aplikasi_product',
			5 => 'created_by',
		);

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	
	function insert_select_process(){
		// exit;
		// $this->db->truncate('table_project_process');
		$this->db->truncate('table_project_process_so');
		
		$sqlUpdate2 = "
			INSERT INTO table_project_process_so ( id_bq, no_ipp, estimasi, rev, order_type, nm_customer, sts_ipp, id_produksi, est_mat, est_harga, real_material, real_harga, persenx, create_by, create_date) SELECT
				a.id_bq,
				a.no_ipp,
				a.estimasi,
				a.rev,
				a.order_type,
				a.nm_customer,
				a.sts_ipp,
				a.id_produksi,
				a.est_mat,
				a.est_harga,
				a.real_material,
				a.real_harga,
				a.persenx,
				'".$this->session->userdata['ORI_User']['username']."',
				'".date('Y-m-d H:i:s')."'
				FROM
					group_so_cost_project_process a";
		
		$this->db->query($sqlUpdate2);
		
		// $sqlUpdate = "
		// 	INSERT INTO table_project_process ( id_bq, no_ipp, estimasi, rev, order_type, nm_customer, sts_ipp, id_produksi, est_mat, est_harga, real_material, real_harga, persenx, create_by, create_date) SELECT
		// 		a.id_bq,
		// 		a.no_ipp,
		// 		a.estimasi,
		// 		a.rev,
		// 		a.order_type,
		// 		a.nm_customer,
		// 		a.sts_ipp,
		// 		a.id_produksi,
		// 		a.est_mat,
		// 		a.est_harga,
		// 		a.real_material,
		// 		a.real_harga,
		// 		a.persenx,
		// 		'".$this->session->userdata['ORI_User']['username']."',
		// 		'".date('Y-m-d H:i:s')."'
		// 		FROM
		// 			group_cost_project_process a";
		
		// $this->db->query($sqlUpdate);

		$qSupplier	= "	SELECT * FROM production_header WHERE sts_produksi <> 'FINISH' ORDER BY no_ipp ASC";
		$row		= $this->db->query($qSupplier)->result_array();

		$ArrInsert = array();
		foreach($row AS $val => $valx){
			if($valx['jalur'] == 'FD'){
				$HelpDet 	= "table_project_process_so";
			}
			if($valx['jalur'] != 'FD'){
				$HelpDet 	= "table_project_process"; 
			}
			$qData	= "	SELECT * FROM ".$HelpDet." WHERE id_produksi = '".$valx['id_produksi']."' LIMIT 1 ";
			$data	= $this->db->query($qData)->result_array();
			foreach($data AS $val2 => $valx2){
				$ArrInsert[$val]['id_bq'] = 'BQ-'.$valx['no_ipp'];
				$ArrInsert[$val]['no_ipp'] = $valx['no_ipp'];
				$ArrInsert[$val]['estimasi'] = $valx2['estimasi'];
				$ArrInsert[$val]['rev'] =  $valx2['rev'];
				$ArrInsert[$val]['order_type'] =  $valx2['order_type'];
				$ArrInsert[$val]['nm_customer'] =  $valx2['nm_customer'];
				$ArrInsert[$val]['sts_ipp'] =  $valx2['sts_ipp'];
				$ArrInsert[$val]['id_produksi'] =  $valx2['id_produksi'];
				$ArrInsert[$val]['est_mat'] =  $valx2['est_mat'];
				$ArrInsert[$val]['est_harga'] =  $valx2['est_harga'];
				$ArrInsert[$val]['real_material'] =  $valx2['real_material'];
				$ArrInsert[$val]['real_harga'] =   $valx2['real_harga'];
				$ArrInsert[$val]['persenx'] =  $valx2['persenx'];
				$ArrInsert[$val]['create_by'] = $this->session->userdata['ORI_User']['username'];
				$ArrInsert[$val]['create_date'] = date('Y-m-d H:i:s');
			}
		}

		// print_r($ArrInsert);
		// exit;
		$this->db->trans_start();
			$this->db->truncate('group_cost_project_process_table');
			$this->db->insert_batch('group_cost_project_process_table', $ArrInsert);
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
			history('Success insert select group cost project on process');
		}
		echo json_encode($Arr_Data);
		
	}

	function insert_select_process_manual(){

		$qSupplier	= "	SELECT * FROM production_header ORDER BY no_ipp ASC";
		$row		= $this->db->query($qSupplier)->result_array();

		$ArrInsert = array();
		foreach($row AS $val => $valx){
			if($valx['jalur'] == 'FD'){
				$HelpDet 	= "table_project_process_so";
			}
			if($valx['jalur'] != 'FD'){
				$HelpDet 	= "table_project_process"; 
			}
			$qData	= "	SELECT * FROM ".$HelpDet." WHERE id_produksi = '".$valx['id_produksi']."' LIMIT 1 ";
			$data	= $this->db->query($qData)->result_array();
			foreach($data AS $val2 => $valx2){
				$ArrInsert[$val]['id_bq'] = 'BQ-'.$valx['no_ipp'];
				$ArrInsert[$val]['no_ipp'] = $valx['no_ipp'];
				$ArrInsert[$val]['estimasi'] = $valx2['estimasi'];
				$ArrInsert[$val]['rev'] =  $valx2['rev'];
				$ArrInsert[$val]['order_type'] =  $valx2['order_type'];
				$ArrInsert[$val]['nm_customer'] =  $valx2['nm_customer'];
				$ArrInsert[$val]['sts_ipp'] =  $valx2['sts_ipp'];
				$ArrInsert[$val]['id_produksi'] =  $valx2['id_produksi'];
				$ArrInsert[$val]['est_mat'] =  $valx2['est_mat'];
				$ArrInsert[$val]['est_harga'] =  $valx2['est_harga'];
				$ArrInsert[$val]['real_material'] =  $valx2['real_material'];
				$ArrInsert[$val]['real_harga'] =   $valx2['real_harga'];
				$ArrInsert[$val]['persenx'] =  $valx2['persenx'];
				$ArrInsert[$val]['create_by'] = $this->session->userdata['ORI_User']['username'];
				$ArrInsert[$val]['create_date'] = date('Y-m-d H:i:s');
			}
		}

		print_r($ArrInsert);
		exit;
		$this->db->trans_start();
			$this->db->truncate('group_cost_project_process_table');
			$this->db->insert_batch('group_cost_project_process_table', $ArrInsert);
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
			history('Success insert select group cost project on process');
		}
		echo json_encode($Arr_Data);
		
	}

	public function getDataJSONUP2(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONUP2(
			$requestData['id_produksi'],
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
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_komponen'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_category'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			if($row['qty_awal'] <> $row['qty_akhir']){
				$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['qty_awal']." - ".$row['qty_akhir']."</span></div>";
			}
			if($row['qty_awal'] == $row['qty_akhir']){
				$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['product_ke']."</span></div>";
			}
			

					$btn1	= "";
					$btn2	= "";
					$btn3	= "";
					$btn4	= "";
					$btn5	= "";
					$btn6	= "";
					$btn7	= "";
					if($row['sts_produksi'] == 'Y'){
						$jumlah = $row['upload_real'];
						if($jumlah == 'N'){
							$btn6	= "&nbsp;<button type='button' class='btn btn-sm btn-danger' title='Data belum di update'><i class='fa fa-close'></i></button>";
						}
						else{
							$btn2	= "&nbsp;<button type='button' class='btn btn-sm btn-warning' id='MatDetail' title='Detail Price' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_milik='".$row['id_milik']."' data-id_producktion='".$row['id']."' data-qty_awal='".$row['qty_awal']."' data-qty_akhir='".$row['qty_akhir']."' data-qty='".$row['qty']."'><i class='fa fa-eye'></i></button>";
						}
					}
					else{
						$btn5	= "&nbsp;<button type='button' class='btn btn-sm btn-danger' title='SPK belum turun !!!'><i class='fa fa-close'></i></button>";
					}
					
					if($row['upload_real'] == 'Y' AND $row['upload_real2'] == 'Y'){
						// $btn7	= "<a href='".site_url($this->uri->segment(1).'/printPriceperMat/'.$row['id_produksi'].'/'.$row['id_product'].'/'.$row['product_ke'].'/'.$row['id_delivery'].'/'.$row['id'].'/'.$row['id_milik'].'/'.$row['qty_awal'].'/'.$row['qty_akhir'])."' class='btn btn-sm btn-primary' target='_blank' title='Print' data-role='qtip'><i class='fa fa-print'></i></a>";
						$btn3	= "&nbsp;<button type='button' class='btn btn-sm btn-success btn_download' title='Download Excel' data-id_produksi='".$row['id_produksi']."' data-id_milik='".$row['id_milik']."' data-qty_awal='".$row['qty_awal']."' data-qty_akhir='".$row['qty_akhir']."' data-qty='".$row['qty']."' data-nm_product='".str_replace(' ','-',$row['id_category'])."'><i class='fa fa-file-excel-o'></i></button>";
					}
					
			$nestedData[]	= "<div align='left'>
									".$btn1."
									".$btn6."
									".$btn2."
									
									
									".$btn4."
									".$btn5."
									".$btn7."
									".$btn3."
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

	public function queryDataJSONUP2($id_produksi, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_komponen
			FROM
				update_real_list a LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id,
				(SELECT @row:=0) r
			WHERE
				a.id_produksi = '".$id_produksi."'
				AND b.id_category <> 'pipe slongsong'
				AND (
				b.no_komponen LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_komponen',
			2 => 'id_category',
			3 => 'id_product'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function excel_report_finish(){
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
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'REPORT PROJECT FINISH');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'SO');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Customer');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Project');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'EST Material');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'EST Cost');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'ACTUAL Material');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);

		$sheet->setCellValue('I'.$NewRow, 'ACTUAL Cost');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(16);

		$sheet->setCellValue('J'.$NewRow, 'Selisih');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(16);

		$sheet->setCellValue('K'.$NewRow, 'Status');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(16);
		
		$QUERY	= "	(SELECT a.* FROM group_cost_project_finish_fast_table a) UNION (SELECT a.* FROM group_so_cost_project_finish_fast_table a)";
		$result_data		= $this->db->query($QUERY)->result_array();

		$SEARCH_IPP = get_detail_ipp();

		if($result_data){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result_data as $key => $row_Cek){
				if($row_Cek['est_mat'] > 0){
					$no++;
					$awal_row++;
					$awal_col	= 0;
					
					$awal_col++;
					$Cols = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $no);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
					
					$awal_col++;
					$no_ipp	= $row_Cek['no_ipp'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $no_ipp);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
					
					$awal_col++;
					$so_number	= $SEARCH_IPP[$row_Cek['no_ipp']]['so_number'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $so_number);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

					$awal_col++;
					$nm_customer	= $SEARCH_IPP[$row_Cek['no_ipp']]['nm_customer'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $nm_customer);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

					$awal_col++;
					$nm_project	= $SEARCH_IPP[$row_Cek['no_ipp']]['nm_project'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $nm_project);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

					$awal_col++;
					$est_mat	= $row_Cek['est_mat'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $est_mat);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

					$awal_col++;
					$est_harga	= $row_Cek['est_harga'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $est_harga);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

					$awal_col++;
					$real_material	= $row_Cek['real_material'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $real_material);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

					$awal_col++;
					$real_harga	= $row_Cek['real_harga'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $real_harga);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

					$awal_col++;
					$persenx	= $row_Cek['persenx'];
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $persenx);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

					if($row_Cek['estimasi'] == 'Y'){
						if($row_Cek['sts_ipp'] == 'PROCESS PRODUCTION'){
							$status	= "PRODUCTION";
						}
						else if($row_Cek['sts_ipp'] == 'FINISH'){
							if($row_Cek['persenx'] > 100){
								$status	= "OVER BUDGET ".number_format($row_Cek['persenx'])." %";
								$warna = 'bisque';
							}
							if($row_Cek['persenx'] <= 100){
								$status	= "FINISH ".number_format($row_Cek['persenx'])." %";
							}
						}
					}

					$awal_col++;
					$Cols	= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $status);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				}
			}
		}
		
		$sheet->setTitle('Report');
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
		header('Content-Disposition: attachment;filename="report-finish-project.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_report_product(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$NO_IPP			= str_replace('PRO-','',$this->uri->segment(3));

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
		$Col_Akhir	= $Cols	= getColsChar(14);
		$sheet->setCellValue('A'.$Row, 'DETAIL PROJECT FINISH '.$NO_IPP);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'SO');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Customer');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Project');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Product');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Spec');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Qty');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);

		$sheet->setCellValue('I'.$NewRow, 'Product ID');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(16);

		$sheet->setCellValue('J'.$NewRow, 'Est Material');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(16);

		$sheet->setCellValue('K'.$NewRow, 'Estimasi Cost');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(16);

		$sheet->setCellValue('L'.$NewRow, 'Aktual Material');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(16);

		$sheet->setCellValue('M'.$NewRow, 'Aktual Cost');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(16);

		$sheet->setCellValue('N'.$NewRow, 'Selisih (%)');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(16);
		

		$id_bq = 'BQ-'.$NO_IPP;
		$qSupplier	= "	SELECT * FROM production_header WHERE no_ipp = '".str_replace('BQ-','',$id_bq)."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet2 	= "bq_detail_header";
		$HelpDet1 	= "spec_bq";
		
		$HelpTable1 = "bq_component_detail";
		$HelpTable2 = "bq_component_detail_plus";
		$HelpTable3 = "bq_component_detail_add";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet2 	= "so_detail_header";
			$HelpDet1 	= "spec_bq2";
			
			$HelpTable1 = "so_component_detail";
			$HelpTable2 = "so_component_detail_plus";
			$HelpTable3 = "so_component_detail_add";
		}
		
		$data_session	= $this->session->userdata;
		$this->db->delete('hasil_banding_group_product_table', array('created' => $data_session['ORI_User']['username']));
		
		
		$sql_banding_mat_detail = "	SELECT
										a.id_bq AS id_bq,
										a.id_product AS id_product,
										a.id_milik AS id_milik,
										a.id_category AS type_category,
										a.nm_category AS nm_category,
										a.id_material AS id_material,
										a.nm_material AS nm_material,
										a.detail_name AS detail_name,
										c.id_production_detail AS id_detail,
										a.price_mat AS cost,
										a.last_cost AS est_material,
										round( ( a.price_mat * a.last_cost ), 2 ) AS est_harga,
										c.material_terpakai AS real_material,
										round( ( c.material_terpakai * a.price_mat ), 2 ) AS real_harga,
										round( ( ( ( c.material_terpakai / a.last_cost ) * 100 ) - 100 ), 2 ) AS selisih,
										c.batch_number AS batch_number,
										c.actual_type AS actual_type,
										c.benang AS benang,
										c.bw AS bw,
										c.layer AS layer_real,
										a.layer AS layer_est 
									FROM
										( ".$HelpTable1." a JOIN production_real_detail c ON ( ( a.id_detail = c.id_detail ) ) ) 
									WHERE
										a.id_bq = '".$id_bq."' 
									GROUP BY
										c.id_production_detail,
										c.id_detail 
									ORDER BY
										a.id_category";
		$result_banding_mat_detail = $this->db->query($sql_banding_mat_detail)->result_array();
		$ArrBMD = [];
		foreach($result_banding_mat_detail AS $val => $valx){
			$ArrBMD[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMD[$val]['id_product'] = $valx['id_product'];
			$ArrBMD[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMD[$val]['type_category'] = $valx['type_category'];
			$ArrBMD[$val]['nm_category'] = $valx['nm_category'];
			$ArrBMD[$val]['id_material'] = $valx['id_material'];
			$ArrBMD[$val]['nm_material'] = $valx['nm_material'];
			$ArrBMD[$val]['detail_name'] = $valx['detail_name'];
			$ArrBMD[$val]['id_detail'] = $valx['id_detail'];
			$ArrBMD[$val]['cost'] = $valx['cost'];
			$ArrBMD[$val]['est_material'] = $valx['est_material'];
			$ArrBMD[$val]['est_harga'] = $valx['est_harga'];
			$ArrBMD[$val]['real_material'] = $valx['real_material'];
			$ArrBMD[$val]['real_harga'] = $valx['real_harga'];
			$ArrBMD[$val]['selisih'] = $valx['selisih'];
			$ArrBMD[$val]['batch_number'] = $valx['batch_number'];
			$ArrBMD[$val]['actual_type'] = $valx['actual_type'];
			$ArrBMD[$val]['benang'] = $valx['benang'];
			$ArrBMD[$val]['bw'] = $valx['bw'];
			$ArrBMD[$val]['layer_real'] = $valx['layer_real'];
			$ArrBMD[$val]['layer_est'] = $valx['layer_est'];
			$ArrBMD[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMD[$val]['hist_date'] = date('Y-m-d H:i:s');
		}
		if(!empty($ArrBMD)){
		$this->db->delete('tb_banding_mat_detail', array('hist_by'=>$data_session['ORI_User']['username']));
		$this->db->insert_batch('tb_banding_mat_detail', $ArrBMD);
		}
		
		
		$sql_banding_mat_plus = "	SELECT
										a.id_bq AS id_bq,
										a.id_product AS id_product,
										a.id_milik AS id_milik,
										a.id_category AS type_category,
										a.nm_category AS nm_category,
										a.id_material AS id_material,
										a.nm_material AS nm_material,
										a.detail_name AS detail_name,
										c.id_production_detail AS id_detail,
										a.price_mat AS cost,
										a.last_cost AS est_material,
										round( ( a.price_mat * a.last_cost ), 2 ) AS est_harga,
										c.material_terpakai AS real_material,
										round( ( c.material_terpakai * a.price_mat ), 2 ) AS real_harga,
										round( ( ( ( a.last_cost / c.material_terpakai ) * 100 ) - 100 ), 2 ) AS selisih,
										c.batch_number AS batch_number,
										c.actual_type AS actual_type 
									FROM
										( ".$HelpTable2." a JOIN production_real_detail_plus c ON ( ( a.id_detail = c.id_detail ) ) ) 
									WHERE
										a.id_bq = '".$id_bq."'
									ORDER BY
										a.id_category";
		$result_banding_mat_plus = $this->db->query($sql_banding_mat_plus)->result_array();
		$ArrBMP = [];
		foreach($result_banding_mat_detail AS $val => $valx){
			$ArrBMP[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMP[$val]['id_product'] = $valx['id_product'];
			$ArrBMP[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMP[$val]['type_category'] = $valx['type_category'];
			$ArrBMP[$val]['nm_category'] = $valx['nm_category'];
			$ArrBMP[$val]['id_material'] = $valx['id_material'];
			$ArrBMP[$val]['nm_material'] = $valx['nm_material'];
			$ArrBMP[$val]['detail_name'] = $valx['detail_name'];
			$ArrBMP[$val]['id_detail'] = $valx['id_detail'];
			$ArrBMP[$val]['cost'] = $valx['cost'];
			$ArrBMP[$val]['est_material'] = $valx['est_material'];
			$ArrBMP[$val]['est_harga'] = $valx['est_harga'];
			$ArrBMP[$val]['real_material'] = $valx['real_material'];
			$ArrBMP[$val]['real_harga'] = $valx['real_harga'];
			$ArrBMP[$val]['selisih'] = $valx['selisih'];
			$ArrBMP[$val]['batch_number'] = $valx['batch_number'];
			$ArrBMP[$val]['actual_type'] = $valx['actual_type'];
			// $ArrBMP[$val]['benang'] = $valx['benang'];
			// $ArrBMP[$val]['bw'] = $valx['bw'];
			// $ArrBMP[$val]['layer_real'] = $valx['layer_real'];
			// $ArrBMP[$val]['layer_est'] = $valx['layer_est'];
			$ArrBMP[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMP[$val]['hist_date'] = date('Y-m-d H:i:s');
		}
		if(!empty($ArrBMP)){
		$this->db->delete('tb_banding_mat_plus', array('hist_by'=>$data_session['ORI_User']['username']));
		$this->db->insert_batch('tb_banding_mat_plus', $ArrBMP);
		}
		
		
		$sql_banding_mat_add = "	SELECT
										a.id_bq AS id_bq,
										a.id_product AS id_product,
										a.id_milik AS id_milik,
										a.id_category AS type_category,
										a.nm_category AS nm_category,
										a.id_material AS id_material,
										a.nm_material AS nm_material,
										a.detail_name AS detail_name,
										c.id_production_detail AS id_detail,
										a.price_mat AS cost,
										a.last_cost AS est_material,
										round( ( a.price_mat * a.last_cost ), 2 ) AS est_harga,
										c.material_terpakai AS real_material,
										round( ( c.material_terpakai * a.price_mat ), 2 ) AS real_harga,
										round( ( ( ( a.last_cost / c.material_terpakai ) * 100 ) - 100 ), 2 ) AS selisih,
										c.batch_number AS batch_number,
										c.actual_type AS actual_type 
									FROM
										( ".$HelpTable3." a JOIN production_real_detail_plus c ON ( ( a.id_detail = c.id_detail ) ) ) 
									WHERE
										a.id_bq = '".$id_bq."'
									ORDER BY
										a.id_category";
		$result_banding_mat_add = $this->db->query($sql_banding_mat_add)->result_array();
		$ArrBMA = [];
		foreach($result_banding_mat_add AS $val => $valx){
			$ArrBMA[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMA[$val]['id_product'] = $valx['id_product'];
			$ArrBMA[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMA[$val]['type_category'] = $valx['type_category'];
			$ArrBMA[$val]['nm_category'] = $valx['nm_category'];
			$ArrBMA[$val]['id_material'] = $valx['id_material'];
			$ArrBMA[$val]['nm_material'] = $valx['nm_material'];
			$ArrBMA[$val]['detail_name'] = $valx['detail_name'];
			$ArrBMA[$val]['id_detail'] = $valx['id_detail'];
			$ArrBMA[$val]['cost'] = $valx['cost'];
			$ArrBMA[$val]['est_material'] = $valx['est_material'];
			$ArrBMA[$val]['est_harga'] = $valx['est_harga'];
			$ArrBMA[$val]['real_material'] = $valx['real_material'];
			$ArrBMA[$val]['real_harga'] = $valx['real_harga'];
			$ArrBMA[$val]['selisih'] = $valx['selisih'];
			$ArrBMA[$val]['batch_number'] = $valx['batch_number'];
			$ArrBMA[$val]['actual_type'] = $valx['actual_type'];
			// $ArrBMA[$val]['benang'] = $valx['benang'];
			// $ArrBMA[$val]['bw'] = $valx['bw'];
			// $ArrBMA[$val]['layer_real'] = $valx['layer_real'];
			// $ArrBMA[$val]['layer_est'] = $valx['layer_est'];
			$ArrBMA[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMA[$val]['hist_date'] = date('Y-m-d H:i:s');
		}
		if(!empty($ArrBMA)){
		$this->db->delete('tb_banding_mat_add', array('hist_by'=>$data_session['ORI_User']['username']));
		$this->db->insert_batch('tb_banding_mat_add', $ArrBMA);
		}
		
		$sql_union = " SELECT a.* FROM tb_banding_mat_detail a UNION SELECT b.* FROM tb_banding_mat_plus b UNION SELECT c.* FROM tb_banding_mat_add c ";
		$sql_union_result = $this->db->query($sql_union)->result_array();
		$ArrBMUNION = [];
		foreach($sql_union_result AS $val => $valx){
			$ArrBMUNION[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMUNION[$val]['id_product'] = $valx['id_product'];
			$ArrBMUNION[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMUNION[$val]['type_category'] = $valx['type_category'];
			$ArrBMUNION[$val]['nm_category'] = $valx['nm_category'];
			$ArrBMUNION[$val]['id_material'] = $valx['id_material'];
			$ArrBMUNION[$val]['nm_material'] = $valx['nm_material'];
			$ArrBMUNION[$val]['detail_name'] = $valx['detail_name'];
			$ArrBMUNION[$val]['id_detail'] = $valx['id_detail'];
			$ArrBMUNION[$val]['cost'] = $valx['cost'];
			$ArrBMUNION[$val]['est_material'] = $valx['est_material'];
			$ArrBMUNION[$val]['est_harga'] = $valx['est_harga'];
			$ArrBMUNION[$val]['real_material'] = $valx['real_material'];
			$ArrBMUNION[$val]['real_harga'] = $valx['real_harga'];
			$ArrBMUNION[$val]['selisih'] = $valx['selisih'];
			$ArrBMUNION[$val]['batch_number'] = $valx['batch_number'];
			$ArrBMUNION[$val]['actual_type'] = $valx['actual_type'];
			$ArrBMUNION[$val]['benang'] = $valx['benang'];
			$ArrBMUNION[$val]['bw'] = $valx['bw'];
			$ArrBMUNION[$val]['layer_real'] = $valx['layer_real'];
			$ArrBMUNION[$val]['layer_est'] = $valx['layer_est'];
			$ArrBMUNION[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMUNION[$val]['hist_date'] = date('Y-m-d H:i:s');
		}
		if(!empty($ArrBMUNION)){
		$this->db->delete('tb_banding_mat', array('hist_by'=>$data_session['ORI_User']['username']));
		$this->db->insert_batch('tb_banding_mat', $ArrBMUNION);
		}
		
		$sql_banding_group_product = "	SELECT
											a.id_bq AS id_bq,
											a.id_milik AS id_milik,
											a.id_product AS id_product,
											round( sum( a.est_material ), 5 ) AS est_material,
											round( sum( a.est_harga ), 5 ) AS est_harga,
											round( sum( a.real_material ), 5 ) AS real_material,
											round( sum( a.real_harga ), 5 ) AS real_harga 
										FROM
											( tb_banding_mat a LEFT JOIN bq_component_header b ON ( ( a.id_milik = b.id_milik ) ) ) 
										WHERE
											a.id_bq = '".$id_bq."'
										GROUP BY
											a.id_milik,
											a.id_bq";
		$sql_banding_group_product_result = $this->db->query($sql_banding_group_product)->result_array();
		$ArrBMGROUP = [];
		foreach($sql_banding_group_product_result AS $val => $valx){
			$ArrBMGROUP[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMGROUP[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMGROUP[$val]['id_product'] = $valx['id_product'];
			$ArrBMGROUP[$val]['est_material'] = $valx['est_material'];
			$ArrBMGROUP[$val]['est_harga'] = $valx['est_harga'];
			$ArrBMGROUP[$val]['real_material'] = $valx['real_material'];
			$ArrBMGROUP[$val]['real_harga'] = $valx['real_harga'];
			$ArrBMGROUP[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMGROUP[$val]['hist_date'] = date('Y-m-d H:i:s');
		}
		if(!empty($ArrBMGROUP)){
		$this->db->delete('tb_banding_group_product', array('hist_by'=>$data_session['ORI_User']['username']));
		$this->db->insert_batch('tb_banding_group_product', $ArrBMGROUP);
		}
					
		$sqlUpdate = "
			INSERT INTO 
				hasil_banding_group_product_table 
					(
						id_bq, 
						id_milik, 
						id_product, 
						est_material, 
						est_harga, 
						real_material, 
						real_harga, 
						created
					) 
				SELECT
					a.id_bq,
					a.id_milik,
					a.id_product,
					a.est_material,
					a.est_harga,
					a.real_material,
					a.real_harga,
					'".$data_session['ORI_User']['username']."'
				FROM
					tb_banding_group_product a
				WHERE 
					a.id_bq='".$id_bq."' ";

		$this->db->query($sqlUpdate);

		$sql 	= "	SELECT 
						a.*, 
						c.est_material,
						c.est_harga,
						c.real_material,
						c.real_harga,
						((c.real_harga / (c.est_harga)) * 100) AS persenx
					FROM 
						".$HelpDet2." a
						LEFT JOIN hasil_banding_group_product_table c
							ON a.id = c.id_milik
					WHERE 
						a.id_bq = '".$id_bq."' 
						AND a.id_category <> 'pipe slongsong'
						AND c.created = '".$data_session['ORI_User']['username']."'					
					ORDER BY 
						c.id_milik ASC";
		// echo $sql; exit;
		$result_data		= $this->db->query($sql)->result_array();

		$SEARCH_IPP = get_detail_ipp();

		if($result_data){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result_data as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$Cols = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_ipp	= $NO_IPP;
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$so_number	= $SEARCH_IPP[$NO_IPP]['so_number'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $so_number);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_customer	= $SEARCH_IPP[$NO_IPP]['nm_customer'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_project	= $SEARCH_IPP[$NO_IPP]['nm_project'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_category	= $row_Cek['id_category'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$spec	= spec_bq2($row_Cek['id']);
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$qty	= $row_Cek['qty'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$id_product	= $row_Cek['id_product'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$est_mat	= $row_Cek['est_material'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_mat);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= $row_Cek['est_harga'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= $row_Cek['real_material'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= $row_Cek['real_harga'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$persenx	= $row_Cek['persenx'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $persenx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		
		
		$sheet->setTitle($NO_IPP);
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
		header('Content-Disposition: attachment;filename="report-finish-project-'.$NO_IPP.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_detail_material_old(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_product 	= $this->uri->segment(3);
		$id_milik 		= $this->uri->segment(4); 
		$no_ipp 		= str_replace('BQ-','',$this->uri->segment(5));
		$qty 			= $this->uri->segment(6);

		$checkProduct = $this->db->get_where('so_detail_header',array('id'=>$id_milik))->result();
		$product_name = (!empty($checkProduct[0]->id_category))?$checkProduct[0]->id_category:0;

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
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'PERBANDINGAN ESTIMASI VS ACTUAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Layer');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Category Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Material Name');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Est Material');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Est Cost');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Aktual Material');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Aktual Cost');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Persentase Act vs Est (%)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);
		
		$qSupplier	= "	SELECT * FROM production_header WHERE no_ipp = '".$no_ipp."' ";
		$row		= $this->db->query($qSupplier)->result_array(); 

		$HelpDet 	= "bq_component_header";
		$HelpDet2 	= "banding_mat";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_component_header";
			$HelpDet2 	= "fast_banding_so_mat";
		}

		
		$qDetail1		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		$qDetail2		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		$qDetail3		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		$qDetail4		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='TOPCOAT' GROUP BY a.id_material";
		
		$detailResin1	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin3	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		// echo $detailResin2; 
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$restDetail4	= $this->db->query($qDetail4)->result_array();
		$restResin1		= $this->db->query($detailResin1)->result_array();
		$restResin2		= $this->db->query($detailResin2)->result_array();
		$restResin3		= $this->db->query($detailResin3)->result_array();
		
		//tambahan flange mould /slongsong
		$restDetail2N1	= [];
		$restDetail2N2	= [];
		$restResin2N1	= [];
		$restResin2N2	= [];
		if($product_name == 'flange mould' OR $product_name == 'flange slongsong' OR $product_name == 'colar mould' OR $product_name == 'colar slongsong'){
			$qDetail2N1		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
			$qDetail2N2		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
			
			$detailResin2N1	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
			$detailResin2N2	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih, a.detail_name FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
			
			$restDetail2N1	= $this->db->query($qDetail2N1)->result_array();
			$restDetail2N2	= $this->db->query($qDetail2N2)->result_array();
			
			$restResin2N1	= $this->db->query($detailResin2N1)->result_array();
			$restResin2N2	= $this->db->query($detailResin2N2)->result_array();
		}
		//Liner Detail
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Liner Resin
		if($restResin1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N1 Detail
		if($restDetail2N1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail2N1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Structure N1 Resin
		if($restResin2N1){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin2N1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure N2 Detail
		if($restDetail2N2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail2N2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Structure N2 Resin
		if($restResin2N2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin2N2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Structure Detail
		if($restDetail2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Structure Resin
		if($restResin2){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin2 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//Eksternal Detail
		if($restDetail3){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail3 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		//Eksternal Resin
		if($restResin3){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restResin3 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		//TopCoat Plus
		if($restDetail4){
			$awal_row	= $awal_row;
			$no=0;
			foreach($restDetail4 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$real_harga	= (!empty($row_Cek['real_harga']))?$row_Cek['real_harga']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$selisih	= (!empty($row_Cek['selisih']))?$row_Cek['selisih']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		
		
		$sheet->setTitle('Report Finish Material');
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
		header('Content-Disposition: attachment;filename="report-project-finish-material.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_detail_material(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_product 	= $this->uri->segment(3);
		$id_milik 		= $this->uri->segment(4); 
		$no_ipp 		= str_replace('BQ-','',$this->uri->segment(5));
		$qty 			= $this->uri->segment(6);

		$checkProduct = $this->db->get_where('so_detail_header',array('id'=>$id_milik))->result();
		$product_name = (!empty($checkProduct[0]->id_category))?$checkProduct[0]->id_category:0;

		$this->load->library("PHPExcel");
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
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'PERBANDINGAN ESTIMASI VS ACTUAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Layer');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Category Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Material Name');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Est Material');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Est Cost');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Aktual Material');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Aktual Cost');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Persentase Act vs Est (%)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);
		
		$qDetail1		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'LINER THIKNESS / CB' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'LINER THIKNESS / CB' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail2		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail3		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail4		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail5		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail6		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
							";
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$restDetail4	= $this->db->query($qDetail4)->result_array();
		$restDetail5	= $this->db->query($qDetail5)->result_array();
		$restDetail6	= $this->db->query($qDetail6)->result_array();
		$restDetail		= array_merge($restDetail1,$restDetail2,$restDetail3, $restDetail4, $restDetail5, $restDetail6);
		if($restDetail){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$detail_name	= $row_Cek['detail_name'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$est_harga	= (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
				$estHarga = $est_material * $est_harga;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estHarga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$estHargaReal = $real_material * $est_harga;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estHargaReal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$selisih = 0;
				if($est_material > 0 AND $real_material > 0){
					$selisih = $real_material / $est_material * 100;
				}
				$Cols = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, number_format($selisih,2));
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
			}
		}

		$sheet->setTitle('Report Finish Material');
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
		header('Content-Disposition: attachment;filename="report-project-'.$no_ipp.'-'.str_replace(' ','-',$product_name).'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
}