<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang_wip_deadstok extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		// Your own constructor code
		if (!$this->session->userdata('isORIlogin')) {
			redirect('login');
		}
	}

	public function index()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Gudang WIP Deadstok',
			'action'		=> 'index',
			'row_group'		=> $data_Group
		);
		history('View data wip deadstok');
		$this->load->view('Deadstok_modif/wip', $data);
	}

	public function server_side_gudang_tanki()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_gudang_tanki(
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

		$FLAG = '';
		$GET_DET_IPP = get_detail_ipp();
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$nm_customer 	= (!empty($GET_DET_IPP[$row['no_ipp']]['nm_customer']))?$GET_DET_IPP[$row['no_ipp']]['nm_customer']:'';
			$nm_project 	= (!empty($GET_DET_IPP[$row['no_ipp']]['nm_project']))?$GET_DET_IPP[$row['no_ipp']]['nm_project']:'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_project)."</div>";
			$nestedData[]	= "<div align='left'>".$row['spec']."</div>";
			$nestedData[]	= "<div align='center'>".$row['qty']."</div>";
			
			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_gudang_tanki($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				z.product_name AS product,
				z.product_spec AS spec,
				z.no_so,
				z.no_spk,
				z.no_ipp,
				COUNT(a.id) AS qty
			FROM
				deadstok_modif a
				LEFT JOIN deadstok z ON a.id_deadstok = z.id,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.status_close_produksi = 'Y'
				AND a.qc_date IS NULL
				AND (
					z.no_so LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.product_name LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.product_spec LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.kode
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'z.no_spk',
			2 => 'z.product_name',
			3 => 'z.no_so'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

}