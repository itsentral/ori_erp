<?php
class Report_invoicing_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

    public function invoicing(){
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
		$list_so = $this->db->query("SELECT
										b.so_number
									FROM
										tr_invoice_header b GROUP BY b.so_number")->result_array();
		$list_cust = $this->db->query("SELECT
										c.id_customer,
										c.nm_customer
									FROM
										tr_invoice_header c GROUP BY c.id_customer")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Invoice',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'ListIPP'		=> $ListIPP,
			'list_so'		=> $list_so,
			'list_cust'		=> $list_cust
		);
		history('View Data Invoice');
		$this->load->view('Invoicing/report_invoice',$data);
	}

    public function get_data_json_inv(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_inv(
			$requestData['no_so'],
			$requestData['customer'],
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
			$nestedData[]	= "<div align='right'>".date('d-M-Y', strtotime($row['tgl_invoice']))."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_invoice']."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='left'>".$row['spesifikasi']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['harga_total'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['harga_total_idr'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['jenis_invoice'])." (".number_format($row['qty'])."%)</div>";
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

	public function query_data_inv($no_so, $customer, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_no_so = "";
		if($no_so <> '0'){
			$where_no_so = " AND a.so_number='".$no_so."' ";
		}

		$where_cust = "";
		if($customer <> '0'){
			$where_cust = " AND a.id_customer='".$customer."' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				tr_invoice_detail a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_no_so." ".$where_cust."
				AND (
				a.no_invoice LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'tgl_invoice',
			2 => 'no_invoice',
			3 => 'so_number',
			4 => 'nm_customer',
			5 => 'harga_total',
			6 => 'harga_total_idr'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


	   public function cogs(){
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
		$list_so = $this->db->query("SELECT
										b.so_number
									FROM
										tr_invoice_header b GROUP BY b.so_number")->result_array();
		$list_cust = $this->db->query("SELECT
										c.id_customer,
										c.nm_customer
									FROM
										tr_invoice_header c GROUP BY c.id_customer")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Invoice',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'ListIPP'		=> $ListIPP,
			'list_so'		=> $list_so,
			'list_cust'		=> $list_cust
		);
		history('View Data Cogs');
		$this->load->view('Invoicing/report_cogs',$data);
	}

    public function get_data_json_inv_cogs(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_inv_cogs(
			$requestData['no_so'],
			$requestData['customer'],
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
			$dv             = $row['delivery_no'];
			$cogs           = $this->db->query("SELECT sum(material)as material, sum(wip_direct)as wip_direct, sum(wip_indirect)as wip_indirect, sum(wip_consumable)as wip_consumable, sum(wip_foh)as wip_foh FROM view_incustomer_cogs 
							WHERE kode_delivery IN ('$dv')")->row();
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
			$nestedData[]	= "<div align='right'>".date('d-M-Y', strtotime($row['tgl_invoice']))."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_invoice']."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_invoice_idr'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_cogs'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_invoice_idr']-$row['total_cogs'])."</div>";
            $nestedData[]	= "<div align='right'>".number_format($cogs->material)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($cogs->wip_direct)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($cogs->wip_indirect)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($cogs->wip_consumable)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($cogs->wip_foh)."</div>";
			$nestedData[]	= "<div align='left'>".$row['delivery_no']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['jenis_invoice'])." (".number_format($row['persentase'])."%)</div>";
            
				$print_jurnal	= "";
				$print_idr		= "";
				$create 		= "";

				

				$detail		= "<button class='btn btn-sm btn-warning detail' title='View History' data-no_invoice='".$row['delivery_no']."'><i class='fa fa-eye'></i></button>";
				
			$nestedData[]	= "<div align='left'>
									".$detail."
									".$print_jurnal."
									".$create."
									".$print_idr."
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

	public function query_data_inv_cogs($no_so, $customer, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

			$where_no_so = "";
		if($no_so <> '0'){
			$where_no_so = " AND a.so_number='".$no_so."' ";
		}

		$where_cust = "";
		if($customer <> '0'){
			$where_cust = " AND a.id_customer='".$customer."' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*, b.delivery_no, b.total_cogs
			FROM
				tr_invoice_header a 
				INNER JOIN penagihan b ON b.id=a.id_penagihan,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_no_so." ".$where_cust." AND a.jenis_invoice='progress'
				AND (
				a.no_invoice LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.delivery_no LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'tgl_invoice',
			2 => 'no_invoice',
			3 => 'so_number',
			4 => 'nm_customer',
			5 => 'total_invoice',
			6 => 'total_invoice_idr'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_detail_invoice(){
		$id = $this->uri->segment(3);
		
		$getDetail 		= $this->db->query("SELECT no_spk,id_trans,kode_trans,kode_delivery,nilai_unit, product,  material,  wip_direct, wip_indirect, wip_consumable,  wip_foh FROM view_incustomer_cogs_detail 
							WHERE kode_delivery IN ('$id')")->result_array();

		$data = array(
			'getDetail'		=> $getDetail
			
		);
		
			$this->load->view('Invoicing/view_detail_delivery', $data);
		
	}

}
?>