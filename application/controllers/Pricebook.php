<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pricebook extends CI_Controller { 
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		
		$this->load->database();
		// $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		// print_r($Arr_Akses); exit; 
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$id_price		= $this->uri->segment(3);
		$get_Data		= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.parent_product='".$id_price."' AND a.status='APPROVED' AND a.deleted ='N' ORDER BY a.sts_price DESC")->result();
		$menu_akses		= $this->master_model->getMenu();
		$getSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted = 'N' AND `status` = 'Y' ORDER BY pressure ASC, resin_system ASC, liner ASC")->result_array();
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		$ListCustomer	= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();
		
		$getBy				= "SELECT updated_date FROM table_product_list ORDER BY updated_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result();
		
		$list_gudang = $this->db->query("SELECT
										b.id, b.category, b.nm_gudang
									FROM
										warehouse b")->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Pricebook',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'listseries'	=> $getSeries,
			'listkomponen'	=> $getKomp,
			'cust'			=> $ListCustomer,
			'list_gudang'		=> $list_gudang,
			'last_by'		=> 'system',
			'last_date'		=> (!empty($restgetBy[0]->updated_date))?$restgetBy[0]->updated_date:date('Y-m-d')
		);
		history('View Data Pricebook');
		$this->load->view('Price/pricebook',$data);
	}
	
	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/pricebook";
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		
		$gudang   = $requestData['gudang'];
		$warehouse   = $this->db->query("SELECT * FROM warehouse WHERE id = '$gudang'")->row();
		$gudang1     = $warehouse->category;
		
		
		$fetch			= $this->queryDataJSON(
			$requestData['status'],
			$requestData['gudang'],
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
		$gudang   = $requestData['gudang'];
		foreach($query->result_array() as $row)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
			
			// $material = $row['id_material'];
			// $hargaCostbook   = $this->db->query("SELECT harga FROM tran_warehouse_jurnal_detail WHERE id_gudang = '$gudang' AND id_material= '$material' ORDER BY tgl_trans DESC limit 1")->row();
			
			// print_r($hargaCostbook);
			// exit;
			
			//$PRICEBOOK       = $hargaCostbook->harga;
			
			
            if($asc_desc == 'asc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'desc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }
			$nestedData 	= array(); 
			
			$nestedData[]	= "<div align='center'>".$row['id_material']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['harga'],2)."</div>";
			$nestedData[]	= "<div align='center'>".$row['tgl_trans']."</div>";
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

	public function queryDataJSON($status,$gudang, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
	   $sql = "
			SELECT DISTINCT
				a.id_material,
				a.harga,
				a.tgl_trans,
				b.nm_material
				
			FROM
				tran_warehouse_jurnal_detail a
				INNER JOIN raw_materials b ON b.id_material=a.id_material
		    WHERE 
				1=1 AND a.id_gudang='$gudang' AND
				(
				a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.harga LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.tgl_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%')				
				ORDER BY a.tgl_trans DESC 
				";
	   
	   // if($gudang=='pusat'){
		// $sql = "
			// SELECT DISTINCT
				// a.id_material,
				// a.price_book,
				// a.updated_date,
				// b.nm_material
				
			// FROM
				// price_book a
				// INNER JOIN raw_materials b ON b.id_material=a.id_material
		    // WHERE 
				// 1=1 AND
				// (
				// a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR a.price_book LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR a.updated_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%')				
				// ORDER BY a.updated_date DESC 
				// ";
	   // }elseif($gudang=='subgudang') {
		   
		   // $sql = "
			// SELECT DISTINCT
				// a.id_material,
				// a.price_book,
				// a.updated_date,
				// b.nm_material
				
			// FROM
				// price_book_subgudang a
				// INNER JOIN raw_materials b ON b.id_material=a.id_material
		    // WHERE 
				// 1=1 AND
				// (
				// a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR a.price_book LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR a.updated_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%')				
				// ORDER BY a.updated_date DESC 
				// ";
		   
	   // }elseif($gudang=='produksi') {
		   // $sql = "
			// SELECT DISTINCT
				// a.id_material,
				// a.price_book,
				// a.updated_date,
				// b.nm_material
				
			// FROM
				// price_book_produksi a
				// INNER JOIN raw_materials b ON b.id_material=a.id_material
		    // WHERE 
				// 1=1 AND
				// (
				// a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR a.price_book LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR a.updated_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				// OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%')				
				// ORDER BY a.updated_date DESC 
				// ";
		   
	   // }
		
		//echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		// $sql .= " GROUP BY x.id_bq ORDER BY x.".$columns_order_by[$column_order]." ".$column_dir." ";
		// $sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}

}