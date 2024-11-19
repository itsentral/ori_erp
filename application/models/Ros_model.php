<?php
class Ros_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function index_ros(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
//		$data_ros		= $this->db->query("SELECT * FROM report_of_shipment")->result_array();
		$no_po		= $this->db->query("SELECT no_po,id_supplier,nm_supplier FROM tran_material_po_header WHERE (status='WAITING IN' OR status='IN PARSIAL') and status1='Y' ORDER BY no_po ASC ")->result_array();
		$no_po_nm		= $this->db->query("SELECT no_po,id_supplier,nm_supplier FROM tran_po_header WHERE (status='WAITING IN' OR status='IN PARSIAL') and status1='Y' ORDER BY no_po ASC ")->result_array();
		$data = array(
			'title'			=> 'Indeks Of ROS',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
//			'data_ros'		=> $data_ros,
			'no_po'			=> $no_po,
			'no_po_nm'			=> $no_po_nm
		);
		history('View ROS');
		$this->load->view('Ros/index',$data);
	}
	public function add_ros($no_po,$tipetrans){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		if($tipetrans=="2"){
			$data = $this->db->query("SELECT * FROM tran_po_header WHERE no_po='".$no_po."'")->row();
			$datadetail = $this->db->query("SELECT *, nm_barang as nm_material, id_barang as id_material FROM tran_po_detail WHERE no_po='".$no_po."'")->result();
		}else{
			$data = $this->db->query("SELECT * FROM tran_material_po_header WHERE no_po='".$no_po."'")->row();
			$datadetail = $this->db->query("SELECT * FROM tran_material_po_detail WHERE no_po='".$no_po."'")->result();
		}
		$data_supplier = $this->db->query("SELECT id_supplier, nm_supplier FROM supplier WHERE sts_aktif='aktif' ORDER BY nm_supplier ASC ")->result_array();
		$data = array(
			'title'			=> 'Indeks Of ROS',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data'			=> $data,
			'datadetail'	=> $datadetail,
			'data_supplier'	=> $data_supplier,
			'tipe'			=> 'add',
			'tipetrans'		=> $tipetrans
		);
		history('Create ROS');
		$this->load->view('Ros/form',$data);
	}

	public function detail_ros($id_ros,$tipe,$tipetrans){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		if($tipetrans=="2"){
			$data	= $this->db->query("SELECT a.*, b.nm_supplier FROM report_of_shipment a left join tran_po_header b on a.no_po=b.no_po WHERE a.id='".$id_ros."'")->row();
		}else{
			$data	= $this->db->query("SELECT a.*, b.nm_supplier FROM report_of_shipment a left join tran_material_po_header b on a.no_po=b.no_po WHERE a.id='".$id_ros."'")->row();
		}
		$forward	= $this->db->query("SELECT * FROM report_of_shipment_forward WHERE id_ros='".$id_ros."'")->result();
		$fcCostItem	= $this->db->query("SELECT * FROM report_of_shipment_forward_details WHERE id_ros='".$id_ros."'")->result();
		$datadetail	= $this->db->query("SELECT * FROM report_of_shipment_product WHERE id_ros='".$id_ros."'")->result();
		$data_supplier = $this->db->query("SELECT id_supplier, nm_supplier FROM supplier WHERE sts_aktif='aktif' ORDER BY nm_supplier ASC ")->result_array();
        $itemFccost = [];
        foreach ($fcCostItem as $it => $itemfc) {
            $itemFccost[$itemfc->id_forwarder][$it] = $itemfc;
        }
		$data = array(
			'title'			=> 'Indeks Of ROS',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data'			=> $data,
			'datadetail'	=> $datadetail,
			'forward'		=> $forward,
			'itemFccost'	=> $itemFccost,
			'data_supplier'	=> $data_supplier,
			'tipe'			=> $tipe,
			'tipetrans'		=> $tipetrans
		);
		history('Detail ROS');
		$this->load->view('Ros/form',$data);
	}

	public function get_data_json_ros(){
		$controller	= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses	= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch	= $this->query_data_json_ros(
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
			$updX='';
			$ajukan='';
			$delX='';
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ros']."/".$row['id']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_po']."</div>";
			$nestedData[]	= "<div align='center'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='center'>".$row['date']."</div>";
			$nestedData[]	= "<div align='center'>".$row['status']."</div>";
			//$class = Color_status($row['status']);
			if($row['status'] == 'OPN'){
				if($Arr_Akses['update']=='1'){
					$updX	= "<button type='button' class='btn btn-sm btn-primary edited' title='Edit ROS' data-ros='".$row['id']."' data-tipetrans='".$row['tipetrans']."'><i class='fa fa-edit'></i></button>";
					$ajukan	= "<button type='button' class='btn btn-sm btn-info updated' title='Update ROS' data-ros='".$row['id']."' data-tipetrans='".$row['tipetrans']."'><i class='fa fa-check'></i></button>";
				}
				if($Arr_Akses['delete']=='1'){
					if($row['status_rg_check']=='OPEN'){
						$delX	= "<button type='button' class='btn btn-sm btn-danger deleted' title='Delete ROS' data-ros='".$row['id']."' data-tipetrans='".$row['tipetrans']."'><i class='fa fa-close'></i></button>";
					}
				}
			}
			$nestedData[]	= "<div align='left'>
									<button type='button' data-ros='".$row['id']."' data-tipetrans='".$row['tipetrans']."' class='btn btn-sm btn-warning viewed' title='View ROS' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$updX."
									".$ajukan."
									".$delX."
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

	public function query_data_json_ros($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*, b.nm_supplier, b.tipetrans
			FROM
				report_of_shipment a left join (select no_po,nm_supplier,1 as tipetrans from tran_material_po_header union select no_po,nm_supplier,2 as tipetrans from tran_po_header) b on a.no_po=b.no_po,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." AND (
				a.no_ros LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ros',
			2 => 'no_po',
			3 => 'nm_supplier'		
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function index_jurnal_incoming(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data = array(
			'title'			=> 'Indeks Of Jurnal Incoming Material',
			'action'		=> 'index',
			'akses_menu'	=> $Arr_Akses,
		);
		history('View Jurnal Incoming');
		$this->load->view('Ros/index_jurnal_incoming',$data);
	}

	public function get_data_json_jurnal_incoming() {
        $requestData = $_REQUEST;
		$statusdata = array();
        $fetch = $this->queryDataJSONJurnal('JV032', $requestData['search']['value'], $requestData['order'][0]['column'], $requestData['order'][0]['dir'], $requestData['start'], $requestData['length']);
        $totalData = $fetch['totalData'];
        $totalFiltered = $fetch['totalFiltered'];
        $query = $fetch['query'];
        $data = array();
        $urut1 = 1;
        $urut2 = 0;
        foreach ($query->result_array() as $row) {
            $total_data = $totalData;
            $start_dari = $requestData['start'];
            $asc_desc = $requestData['order'][0]['dir'];
            if ($asc_desc == 'asc') {
                $nomor = $urut1 + $start_dari;
            }
            if ($asc_desc == 'desc') {
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            $nestedData = array();
            $detail = "";
            $nestedData[] = "<div align='center'>" . $nomor . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['no_request']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['no_reff']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['tanggal']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['stspos']) . "</div>";
			if($row['stspos']!=1){
				$nestedData[] = 
				  "
				  <a class='btn btn-sm btn-default viewed' href='javascript:void(0)' title='View Jurnal Incoming' data-id='" . $row['nomor'] . "'><i class='fa fa-search'></i>
				  </a>
				   <a class='btn btn-warning btn-sm edited' href='javascript:void(0)' title='Edit Jurnal Incoming' data-id='" . $row['nomor'] . "'><i class='fa fa-check'></i>
				  </a>
				  ";
			} else {
				$nestedData[] = "
				  <a class='btn btn-default btn-sm viewed' href='javascript:void(0)' title='View Jurnal Incoming' data-id='" . $row['nomor'] . "'><i class='fa fa-search'></i>
				  </a>
				  ";
			}
            $data[] = $nestedData;
            $urut1++;
            $urut2++;
        }
        $json_data = array("draw" => intval($requestData['draw']), "recordsTotal" => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $data);
        echo json_encode($json_data);
    } 
	
	public function index_jurnal_ros(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data = array(
			'title'			=> 'Indeks Of Jurnal ROS',
			'action'		=> 'index',
			'akses_menu'	=> $Arr_Akses,
		);
		history('View Jurnal ROS');
		$this->load->view('Ros/index_jurnal_ros',$data);
	}

    public function queryDataJSONJurnal($type, $like_value = NULL, $column_order = '', $column_dir = NULL, $limit_start = NULL, $limit_length = NULL) {
            $sql = "SELECT a.nomor, a.no_request, a.no_reff, a.tanggal, a.tipe, a.jenis_jurnal, a.stspos FROM jurnaltras a			
			WHERE jenis_jurnal='".$type."' and 
			(
			a.tanggal LIKE '%" . $this->db->escape_like_str($like_value) . "%'
			OR
			a.no_reff LIKE '%" . $this->db->escape_like_str($like_value) . "%'
			OR 
			a.no_request LIKE '%" . $this->db->escape_like_str($like_value) . "%'
			)
			group by a.nomor,a.no_request, a.no_reff, a.tanggal, a.tipe, a.jenis_jurnal, a.stspos
			";
        $data['totalData'] = $this->db->query($sql)->num_rows();
        $data['totalFiltered'] = $this->db->query($sql)->num_rows();
        $columns_order_by = array(0 => 'no_request', 1 => 'no_reff', 2 => 'tanggal', 3 => 'stspos'); 
        if($column_order!='') $sql.= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
        $sql.= " LIMIT " . $limit_start . " ," . $limit_length . " ";
        $data['query'] = $this->db->query($sql);
        return $data;
    }

	public function get_data_json_jurnal_ros() {
        $requestData = $_REQUEST;
		$statusdata = array();
        $fetch = $this->queryDataJSONJurnal('JV040', $requestData['search']['value'], $requestData['order'][0]['column'], $requestData['order'][0]['dir'], $requestData['start'], $requestData['length']);
        $totalData = $fetch['totalData'];
        $totalFiltered = $fetch['totalFiltered'];
        $query = $fetch['query'];
        $data = array();
        $urut1 = 1;
        $urut2 = 0;
        foreach ($query->result_array() as $row) {
            $total_data = $totalData;
            $start_dari = $requestData['start'];
            $asc_desc = $requestData['order'][0]['dir'];
            if ($asc_desc == 'asc') {
                $nomor = $urut1 + $start_dari;
            }
            if ($asc_desc == 'desc') {
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            $nestedData = array();
            $detail = "";
            $nestedData[] = "<div align='center'>" . $nomor . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['no_request']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['no_reff']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['tanggal']) . "</div>";
            $nestedData[] = "<div align='left'>" . ($row['stspos']) . "</div>";
			if($row['stspos']!=1){
				$nestedData[] = 
				  "
				  <a class='btn btn-sm btn-default viewed' href='javascript:void(0)' title='View Jurnal ROS' data-id='" . $row['nomor'] . "'><i class='fa fa-search'></i>
				  </a>
				   <a class='btn btn-warning btn-sm edited' href='javascript:void(0)' title='Edit Jurnal ROS' data-id='" . $row['nomor'] . "'><i class='fa fa-check'></i>
				  </a>
				  ";
			} else {
				$nestedData[] = "
				  <a class='btn btn-warning btn-sm viewed' href='javascript:void(0)' title='View Jurnal ROS' data-id='" . $row['nomor'] . "'><i class='fa fa-eye'></i>
				  </a>
				  ";
			}
            $data[] = $nestedData;
            $urut1++;
            $urut2++;
        }
        $json_data = array("draw" => intval($requestData['draw']), "recordsTotal" => intval($totalData), "recordsFiltered" => intval($totalFiltered), "data" => $data);
        echo json_encode($json_data);
    } 
	
}
