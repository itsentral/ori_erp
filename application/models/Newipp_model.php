<?php
class Newipp_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	//INDEX
	public function index_new_ipp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of New IPP',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data New IPP');
		$this->load->view('Machine/new_ipp',$data);
	}
	
	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	
	public function get_data_json_new_ipp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_new_ipp(
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['no_ipp']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_customer']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
				$dataModif = (!empty($row['ref_ke']))?$row['modified_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($dataModif))."</div>";
				$dataModifx = (!empty($row['ref_ke']))?$row['modified_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($dataModifx))."</div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='BQ-".$row['no_ipp']."' AND (id_product= '' OR id_product  is null) ")->num_rows();
				
				$class = Color_status($row['status']);
				
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$class."'>".$row['status']."</span></div>";
				$add_bq = "&nbsp;<a href='".site_url('machine/bq2/'.$row['no_ipp'])."' target='_blank' class='btn btn-sm btn-success' title='Add BQ' data-role='qtip'><i class='fa fa-plus'></i></a>";
				$reject = "&nbsp;<button type='button' data-no_ipp='".$row['no_ipp']."' class='btn btn-sm btn-danger reject' title='Reject IPP' data-role='qtip'><i class='fa fa-reply'></i></button>"; 
			$nestedData[]	= "<div align='left'> 
									<button type='button' data-no_ipp='".$row['no_ipp']."' class='btn btn-sm btn-warning detail' title='View IPP' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$add_bq."
									".$reject."
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

	public function query_data_json_new_ipp($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				production a 
				LEFT JOIN bq_header b ON a.no_ipp = b.no_ipp
				LEFT JOIN draf_bq_header c ON a.no_ipp = c.no_ipp
		    WHERE 
				a.deleted = 'N' 
				AND b.no_ipp IS NULL
				AND c.no_ipp IS NULL
				AND a.status='WAITING STRUCTURE BQ' 
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer'
			
		);

		$sql .= " ORDER BY modified_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}
