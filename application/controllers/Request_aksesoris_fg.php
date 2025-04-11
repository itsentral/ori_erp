<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_aksesoris_fg extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Gudang FG Aksesoris',
		  'action'		    => 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	    => $Arr_Akses
		);
		history('View finish good aksesoris');
		$this->load->view('Request_aksesoris/finish_good',$data);
	}

    public function server_side_request(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request(
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
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".get_name_acc($row['id_material'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty_fg'])."</div>";

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

	public function query_data_json_request($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.kode,
				a.no_ipp,
                a.created_by,
                a.created_date,
				b.so_number,
                c.nm_customer,
                c.project,
                d.id_material,
                SUM(a.qty_out-a.qty_delivery) AS qty_fg
			FROM
				request_accessories a
                LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp) = b.id_bq
                LEFT JOIN production c ON a.no_ipp = c.no_ipp
                LEFT JOIN so_acc_and_mat d ON a.id_milik = d.id
                LEFT JOIN accessories e ON d.id_material = e.id,
				(SELECT @row:=0) r
		    WHERE a.deleted_date IS NULL AND a.qty_out > 0
                AND (
                    a.kode LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.project LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR e.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
                )
            GROUP BY d.id_material, a.no_ipp
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.so_number',
			2 => 'c.nm_customer',
			3 => 'c.project',
			4 => 'e.nama',
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}