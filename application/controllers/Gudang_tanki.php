<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang_tanki extends CI_Controller
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
			'title'			=> 'Finish Good Tanki',
			'action'		=> 'index',
			'row_group'		=> $data_Group
		);
		history('View data finish good tanki');
		$this->load->view('Tanki/index_fg', $data);
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

			$GET_DET_IPP = $this->tanki_model->get_ipp_detail($row['no_ipp']);
			$spec = $this->tanki_model->get_spec($row['id_milik']);

			$nm_customer = (!empty($GET_DET_IPP['customer']))?$GET_DET_IPP['customer']:'';
			$nm_project = (!empty($GET_DET_IPP['nm_project']))?$GET_DET_IPP['nm_project']:'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_product'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_project)."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='center'>".$row['qty_product']."</div>";
			
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
		$where = "AND a.product_code_cut = 'tanki'";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				z.no_so,
				z.keterangan AS no_ipp,
				COUNT(a.id) AS qty_product
			FROM
				production_detail a
				LEFT JOIN warehouse_adjustment z ON a.kode_spk = z.kode_spk,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.release_to_costing_date IS NOT NULL
				AND a.fg_date IS NOT NULL
				AND a.kode_delivery IS NULL
				AND a.spool_induk IS NULL
                " . $where . "
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.id_milik
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_spk',
			2 => 'id_category',
			3 => 'kode_spk'
		);

		$sql .= " ORDER BY a.release_to_costing_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

}