<?php

class Employee_model extends CI_Model{
	var $API ="";
    public function __construct()
    {
        parent::__construct();
		// $this->API="http://103.228.117.98/hrori/assets/api/api_karyawan.php";
		// $this->load->library('curl');
    }

	public function getList($table){
		$queryList = $this->db->where('status','Y')->get($table)->result_array();
		return $queryList;
	}

	public function getWhere($table, $flied, $value){
		$queryList = $this->db->get_where($table, array($flied => $value))->result_array();
		return $queryList;
	}

	public function saveData($table, $dataArr){

		$this->db->trans_start();
			$this->db->insert($table, $dataArr);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Asset gagal disimpan ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Asset berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
		}

		return $Arr_Data;
	}

	public function get_json_employee(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_employee(
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
		// $karyawan = json_decode($this->curl->simple_get($this->API));
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
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nik']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_karyawan']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['tgl_lahir']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['gender']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['agama']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_ponsel']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_ponsel']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_ponsel']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_ponsel']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_ponsel']))."</div>";

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

	public function get_query_json_employee($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                employee a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.nik LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.nm_karyawan LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.updated_by LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nik'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }
	
	
	public function get_json_employeeXXX(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_employeeXXX(
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
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nik']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_karyawan']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['tgl_lahir']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['gender']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['agama']))."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_ponsel']))."</div>";
			$value = "Active";
			$color = "bg-green";
			if($row['status'] == 'N'){
				$value = "Not Active";
				$color = "bg-red";
			}
			$nestedData[]	= "<div align='center'><span class='badge ".$color." '>".$value."</span></div>";

			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";

                $detail		= "<button type='button' class='btn btn-sm btn-warning detail' title='Delete data' data-id='".$row['id']."'><i class='fa fa-eye'></i></button>";
                $edit		= "";
                $delete		= "";

                if($Arr_Akses['delete']=='1'){
                    $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-id='".$row['id']."'><i class='fa fa-trash'></i></button>";
                }

                if($Arr_Akses['update']=='1'){
                    $edit	= "&nbsp;<a href=".base_url('employee/add/'.$row['id'])." class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
                }
				$nestedData[]	= "<div align='left'>
									".$detail."
                                    ".$edit."
                                    ".$delete."
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

	public function get_query_json_employeeXXX($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                employee a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.nik LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.nm_karyawan LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.updated_by LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nik'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }

}
