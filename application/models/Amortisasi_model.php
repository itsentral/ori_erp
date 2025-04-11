<?php

class Amortisasi_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }

	public function getList($table){
		$queryList = $this->db->get($table)->result_array();
		return $queryList;
	}

	function getListTable($table='',$where=''){
		$this->db->select('a.*');
		$this->db->from($table.' a');
		if($where!=''){
		$this->db->where($where);
		}
		$this->db->order_by('a.id', 'desc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function getWhere($table, $flied, $value){
		$queryList = $this->db->get_where($table, array($flied => $value))->result_array();
		return $queryList;
	}

	public function getDataJSON(){
		$controller		= ucfirst(strtolower($this->uri->segment(1)));
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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
		$sumx	= 0;
		foreach($query->result_array() as $row)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc') {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'desc') {
                $nomor = ($total_data - $start_dari) - $urut2;
            }
			$idaset  = $row['kd_asset'];
			$bulan='';
			$tahun='';
			$status='';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_asset']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_category']))."</div>";
			$nestedData[]	= "<div align='center'>".$row['depresiasi']." Bulan</div>";
			$nestedData[]	= number_format($row['nilai_asset']);
			$nestedData[]	= number_format($row['value']);
			$nestedData[]	= "<div align='center'>".date("m-Y",strtotime($row['tgl_perolehan']))." / ".date("m-Y",strtotime("+".($row['depresiasi']-1)." months", strtotime($row['tgl_perolehan'])))."</div>";
			$updX = "";
			$delX = "";
			if($row['status']=="0"){
				$updX = ' <a class="btn btn-info btn-sm approve" href="javascript:void(0)" title="Update" data-id='.$row['id'].'><i class="fa fa-check-square-o"></i></a>';
				$delX = ' <a class="btn btn-danger btn-sm delete" href="javascript:void(0)" title="Hapus" data-id='.$row['id'].'><i class="fa fa-trash"></i></a>';
			}
			$PrintX	= "";
			$nestedData[]	= "<div align='center'>
									<a class='btn btn-default btn-sm view' href='".base_url('amortisasi/view/'.$row['id'])."' title='View'><i class='fa fa-eye'></i></a> 
									<a class='btn btn-default btn-sm edit' href='".base_url('amortisasi/edit/'.$row['id'])."' title='Edit'><i class='fa fa-edit'></i></a> 
									
									".$delX."
									".$updX."
									</div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}
		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data,
		);
		echo json_encode($json_data);
	}

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
			SELECT
				a.*
			FROM
				amortisasi a LEFT JOIN amortisasi_nilai b ON a.kd_asset = b.kd_asset
			WHERE 1=1
				AND a.deleted = 'N'
				AND (
				a.nm_asset LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData'] 	= $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id',
			1 => 'kd_asset',
			2 => 'nm_asset',
			3 => 'nm_category',
			4 => 'depresiasi',
			5 => 'nilai_asset',
			6 => 'value',
			7 => 'sisa_nilai'

		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	function GetAsetCombo(){
		$aMenu		= array();
		$this->db->select('a.kd_amortisasi, a.nm_amortisasi');
		$this->db->from('amortisasi a');
		$this->db->where('a.deleted','N');
		$this->db->order_by('a.nm_amortisasi', 'asc');
		$query = $this->db->get();
		$results	= $query->result_array();
		if($results){
			foreach($results as $key=>$vals){
				$aMenu[$vals['kd_amortisasi']]	= $vals['kd_amortisasi'].' - '.$vals['nm_amortisasi'];
			}
		}
		return $aMenu;
	}
}
