<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}

        $this->get_ipp_release = get_ipp_release();
        $this->get_ipp_engine = get_ipp_enginerring();
        $this->get_ipp_costing = get_ipp_costing();
        $this->get_ipp_quotation = get_ipp_quotation();
        $this->get_ipp_final_drawing = get_final_drawing();
        $this->get_ipp_release_spk = get_ipp_spk();
	}

    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$query	 	= "SELECT * FROM color_status WHERE `status_aktif` = 'Y' ORDER BY urut ASC";
		$status		= $this->db->query($query)->result();
		
		$data = array(
			'title'			=> 'Monitoring Timeline Program Costing',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'status'		=> $status,
			'akses_menu'	=> $Arr_Akses
		);
		history('View dashboard monitoring');
		$this->load->view('Monitoring/index',$data);
	}

    public function server_side_sales_ipp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_sales_ipp(
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

        $GET_RELEASE_IPP = $this->get_ipp_release;
        $GET_ENGINE = $this->get_ipp_engine;
        $GET_SPK = $this->get_ipp_release_spk;
        $GET_QUOTATION = $this->get_ipp_quotation;
        $GET_FINAL_DRAWING = $this->get_ipp_final_drawing;


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
            $class = Color_status($row['status']);

            $ipp_release_date       = (!empty($GET_RELEASE_IPP[$row['no_ipp']]['ipp_release_date']))?$GET_RELEASE_IPP[$row['no_ipp']]['ipp_release_date']:'-';
            $so_number              = (!empty($GET_RELEASE_IPP[$row['no_ipp']]['so_number']))?$GET_RELEASE_IPP[$row['no_ipp']]['so_number']:'-';
            $bq_release_date        = (!empty($GET_ENGINE[$row['no_ipp']]['bq_release_date']))?$GET_ENGINE[$row['no_ipp']]['bq_release_date']:'-';
            $app_bq_date            = (!empty($GET_ENGINE[$row['no_ipp']]['app_bq_date']))?$GET_ENGINE[$row['no_ipp']]['app_bq_date']:'-';
            $est_release_date       = (!empty($GET_ENGINE[$row['no_ipp']]['est_release_date']))?$GET_ENGINE[$row['no_ipp']]['est_release_date']:'-';
            $app_est_date           = (!empty($GET_ENGINE[$row['no_ipp']]['app_est_date']))?$GET_ENGINE[$row['no_ipp']]['app_est_date']:'-';
            $est_price_release_date = (!empty($GET_RELEASE_IPP[$row['no_ipp']]['est_price_release_date']))?$GET_RELEASE_IPP[$row['no_ipp']]['est_price_release_date']:'-';
            $app_quotation_date     = (!empty($GET_ENGINE[$row['no_ipp']]['app_quotation_date']))?$GET_ENGINE[$row['no_ipp']]['app_quotation_date']:'-';
            $so_release_date        = (!empty($GET_RELEASE_IPP[$row['no_ipp']]['so_release_date']))?$GET_RELEASE_IPP[$row['no_ipp']]['so_release_date']:'-';
            $app_so_date            = (!empty($GET_RELEASE_IPP[$row['no_ipp']]['app_so_date']))?$GET_RELEASE_IPP[$row['no_ipp']]['app_so_date']:'-';
            $quotation_release_date = (!empty($GET_QUOTATION[$row['no_ipp']]['quotation_release_date']))?$GET_QUOTATION[$row['no_ipp']]['quotation_release_date']:'-';
            $fd_release_date 		= (!empty($GET_FINAL_DRAWING[$row['no_ipp']]['fd_release_date']))?$GET_FINAL_DRAWING[$row['no_ipp']]['fd_release_date']:'-';
            $app_fd_date 			= (!empty($GET_FINAL_DRAWING[$row['no_ipp']]['app_fd_date']))?$GET_FINAL_DRAWING[$row['no_ipp']]['app_fd_date']:'-';
            $spk_release_date 		= (!empty($GET_SPK[$row['no_ipp']]['spk_release_date']))?$GET_SPK[$row['no_ipp']]['spk_release_date']:'-';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'><span class='detail text-primary' style='cursor:pointer;' data-no_ipp='".$row['no_ipp']."'>".strtoupper(strtolower($row['no_ipp']))."</span></div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_customer']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>"; 
			// $nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['status']."</span></div>";
            $nestedData[]	= "<div align='center'>".$ipp_release_date."</div>";
            $nestedData[]	= "<div align='center'>".$bq_release_date."</div>";
            $nestedData[]	= "<div align='center'>".$app_bq_date."</div>";
            $nestedData[]	= "<div align='center'>".$est_release_date."</div>";
            $nestedData[]	= "<div align='center'>".$app_est_date."</div>";
            $nestedData[]	= "<div align='center'>".$est_price_release_date."</div>";
            $nestedData[]	= "<div align='center'>".$app_quotation_date."</div>";
            $nestedData[]	= "<div align='center'>".$quotation_release_date."</div>";
            $nestedData[]	= "<div align='center'>".$so_number."</div>";
            $nestedData[]	= "<div align='center'>".$so_release_date."</div>";
            $nestedData[]	= "<div align='center'>".$app_so_date."</div>";
            $nestedData[]	= "<div align='center'>".$fd_release_date."</div>";
            $nestedData[]	= "<div align='center'>".$app_fd_date."</div>";
            $nestedData[]	= "<div align='center'>".$spk_release_date."</div>";

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

	public function query_data_json_sales_ipp($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($status <> '0'){
			$where = " AND a.`status`='".$status."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				production a,
				(SELECT @row:=0) r
		    WHERE a.deleted = 'N' ".$where." AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
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

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function detail(){
		$no_ipp = $this->uri->segment(3);
		$GET_RELEASE_IPP = $this->get_ipp_release;
		$result		= $this->db
							->get_where('so_detail_header',
								array(
									'id_bq'=>'BQ-'.$no_ipp
									)
								)
							->result_array();
		
		$data = array(
			'GET_RELEASE_IPP' => $GET_RELEASE_IPP,
			'result' => $result,
		);
		$this->load->view('Monitoring/detail', $data);
	}


}