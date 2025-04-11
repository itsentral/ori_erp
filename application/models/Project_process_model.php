<?php
class Project_process_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function index_on_progress(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/on_progress';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT create_by, create_date FROM group_project ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		$data_uri = $this->uri->segment(3);
		$data = array(
			'title'			=> 'Indeks Of Cost Control On Progress',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'data_uri'		=> $data_uri
		);
		history('View Cost Control On Progress Project Price');
		$this->load->view('Cost_control/on_progress',$data);
	}
	
	public function on_progress_satuan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		// if($Arr_Akses['update'] !='1'){
			// $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			// redirect(site_url('users'));
		// }
		$id_produksi 	= $this->uri->segment(3);
		$no_ipp = str_replace('PRO-','',$id_produksi);
		$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();
		
		
		$data = array(
			'title'		=> 'Progress '.$no_ipp,
			'action'	=> 'updateReal',
			'row'		=> $row
		);
		$this->load->view('Cost_control/priceReal2',$data);
	}


	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	public function get_data_json_process(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/on_progress';
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_process(
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."/".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = 'BQ-".$row['no_ipp']."' GROUP BY series")->result_array();
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
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='BQ-".$row['no_ipp']."' AND (id_product= '' OR id_product  is null) ")->num_rows();
				
				$warna = Color_status($row['sts_ipp']);
				
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$updXCost	= "";
					$ApprvX	= "";
					$ApprvX2	= "";
					$viewX	= "";
					if($row['estimasi']=='Y'){
						$viewX	= "<button class='btn btn-sm btn-primary view_data' title='View Data' data-id_bq='BQ-".$row['no_ipp']."' data-cost_control='cost_control'><i class='fa fa-eye'></i></button>";
					}
					
					if($Arr_Akses['update']=='1'){
						if(!empty($row['no_ipp'])){
							$updX	= "&nbsp;<a href='".base_url('cost_control/priceReal2/PRO-'.$row['no_ipp'])."' class='btn btn-sm btn-success' title='Detail Material Price ' data-role='qtip'><i class='fa fa-money'></i></a>";
						}
					}
					
					if($uri_code == 'cost_control'){
						$updXCost	= "&nbsp;<button class='btn btn-sm btn-warning total_material' title='Total All Material' data-id_bq='BQ-".$row['no_ipp']."'><i class='fa fa-money'></i></button>";
					}
					//<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
			$nestedData[]	= "<div align='center'>
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

	public function query_data_json_process($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_wait_est = "";
		if($status == 'FINISH'){
			$where_wait_est = " AND (a.persenx <= 100 AND a.persenx >= 90) ";
		}
		
		if($status == 'FINISH 2'){
			$where_wait_est = " AND a.persenx < 90 ";
		}
		
		if($status == 'OVER BUDGET'){ 
			$where_wait_est = " AND a.persenx > 100 "; 
		} 
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.real_end_produksi,
				c.so_number
			FROM
				group_project a
				INNER JOIN production_header b ON a.no_ipp = b.no_ipp
				LEFT JOIN so_number c ON a.no_ipp = REPLACE(c.id_bq,'BQ-',''),
				(SELECT @row:=0) r
		    WHERE 
				1=1
				AND (sts_ipp='PROCESS PRODUCTION' OR sts_ipp = 'PARTIAL PROCESS')
				".$where_wait_est."
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// $sql = "
		// 	SELECT
		// 		a.*
		// 	FROM
		// 		group_project a
		//     WHERE 
		// 		1=1
		// 		AND (sts_ipp='PROCESS PRODUCTION' OR sts_ipp = 'PARTIAL PROCESS')
		// 		AND (
		// 		a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 		OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	    //     )
		// ";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}
