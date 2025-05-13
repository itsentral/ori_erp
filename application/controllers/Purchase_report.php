<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_report extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('sales_model');
		$this->load->model('quotation_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}
	
	//==========================================================================================================================
	//======================================================= IPP ==============================================================
	//==========================================================================================================================
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$query	 			= "SELECT category FROM data_pr GROUP BY category";
		$status				= $this->db->query($query)->result();
		
		$data = array(
			'title'			=> 'Progress PR',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'status'		=> $status,
			'akses_menu'	=> $Arr_Akses
		);
		history('View progress pr dashboard');
		$this->load->view('Purchase_report/index',$data);
	}
	
	public function server_side_progress_pr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_progress_pr(
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

			if($row['category'] == 'asset'){
				$warna = '#a9179e';
			}
			elseif($row['category'] == 'rutin'){
				$warna = '#a19012';
			}
			else{
				$warna = '#1bb885';
			}

			$category = $row['category'];
			if($category == 'rutin'){
				$category = 'stok';
			}

			if($category == 'non rutin'){
				$category = 'departemen';
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['no_pr_group'].'/'.$row['no_pr']))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tgl_pr']))."</div>";
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color: ".$warna.";'>".strtoupper($category)."</span></div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['app_by']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['app_date']))."</div>";
			$nestedData[]	= "<div align='center'><div class='col-md-4 badge'>".($row['status']!=""?$row['status']:"-")."</div><div class='col-md-8'><span data-no_pr='".$row['no_pr_group']."' data-category='".$row['category']."' class='text-bold detail_pr' style='color: ".$warna."' title='Detail PR' data-role='qtip'><u>DETAIL PR</u></span></div></div>";
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

	public function query_data_json_progress_pr($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($status <> '0'){
			$where = " AND a.`category`='".$status."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,  b.status
			FROM
				data_pr a join 
				(SELECT @row:=0) r
				left join 
				(
				select no_pr_group,category,GROUP_CONCAT( DISTINCT  (sts_ajuan) SEPARATOR ',') status from data_pr_detail
				group by no_pr_group,category
				union
				SELECT
					a.no_pr AS no_pr_group,
					'material' AS category,
					GROUP_CONCAT( DISTINCT  (c.sts_ajuan) SEPARATOR ',') status
				FROM
					tran_material_pr_detail a
					LEFT JOIN tran_material_rfq_detail b ON a.no_rfq = b.no_rfq AND a.id_material = b.id_material
					LEFT JOIN tran_material_rfq_header c ON b.hub_rfq = c.hub_rfq
					group by a.no_pr,a.category

				) b on a.category=b.category and a.no_pr_group=b.no_pr_group
		    WHERE 1=1 ".$where." AND (
				a.no_pr_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.category LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_pr_group',
			2 => 'category'
			
		);

		$sql .= " ORDER BY a.app_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modal_detail_pr(){
		$no_pr = $this->uri->segment(3);
		$category = $this->uri->segment(4);

		if($category != 'material'){
			$getData = $this->db->order_by('tgl_pr,no_rfq,nm_barang')->get_where('data_pr_detail',array('no_pr_group'=>$no_pr))->result_array();
		}
		else{
			$QUERY = "SELECT
						a.no_pr AS no_pr_group,
						a.no_pr AS no_pr,
						DATE(a.created_date) AS tgl_pr,
						a.category AS category,
						'po' AS jenis_pembelian,
						a.no_rfq AS no_rfq,
						a.id_material AS id_barang,
						a.nm_material AS nm_barang,
						a.qty_revisi AS qty_pr,
						b.no_po AS no_po,
						b.nm_supplier AS nm_supplier,
						b.qty AS qty_rfq,
						b.qty_po AS qty_po,
						b.`status` AS `status`,
						b.status_apv AS status_apv,
						b.harga_idr AS harga_idr,
						c.sts_ajuan AS sts_ajuan 
					FROM
						tran_material_pr_detail a
						LEFT JOIN tran_material_rfq_detail b ON a.no_rfq = b.no_rfq AND a.id_material = b.id_material
						LEFT JOIN tran_material_rfq_header c ON b.hub_rfq = c.hub_rfq 
					WHERE
						a.no_pr = '$no_pr'
					ORDER BY
						a.no_pr,
						a.no_rfq,
						b.no_po";
			$getData = $this->db->query($QUERY)->result_array();
		}
		$ArrData = [
			'no_pr' => $no_pr,
			'getData' => $getData
		];
		$this->load->view('Purchase_report/modal_detail_pr',$ArrData);
	}
}