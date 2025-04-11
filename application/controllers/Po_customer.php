<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Po_customer extends CI_Controller{

    public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	public function index()
	{
		$controller			= 'po_customer';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Index Of PO Customer',
			'action'		=> 'po_customer',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data PO Customer');
		$this->load->view('Master_Customer/po_customer_list',$data);
	}
	public function add_data_po_customer(){
		$controller			= 'po_customer';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('users'));
		}
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			$id 		    = $data['id'];
            $customer = $this->db->query("SELECT nm_customer FROM customer WHERE id_customer ='".$data['id_customer']."' LIMIT 1 ")->row();
			if(empty($id)){
                $ArrHeader = array(
                    'nomor_po'		=> trim($data['nomor_po']),
                    'keterangan'	=> ($data['keterangan']),
                    'id_customer'	=> ($data['id_customer']),
                    'nm_customer'	=> ($customer->nm_customer),
                    'tanggal_po'	=> ($data['tanggal_po']),
                );
                $TandaI = "Insert";
			}
			if(!empty($id)){
                $ArrHeader = array(
                    'nomor_po'		=> trim($data['nomor_po']),
                    'keterangan'	=> ($data['keterangan']),
                    'id_customer'	=> ($data['id_customer']),
                    'nm_customer'	=> ($customer->nm_customer),
                    'tanggal_po'	=> ($data['tanggal_po']),
                );
                $TandaI = "Update";
            }
			$bypass="";
            $this->db->trans_start();
			if(empty($id)) {
				$nomorpo = $this->db->query("SELECT nomor_po FROM tr_kartu_po_customer WHERE nomor_po ='".trim($data['nomor_po'])."'")->row();
				if(empty($nomorpo)){
					$this->db->insert('tr_kartu_po_customer', $ArrHeader);
				}else{
					$bypass="error";
				}
			}
			if(!empty($id)) {
				$nomorpo = $this->db->query("SELECT nomor_po FROM tr_kartu_po_customer WHERE nomor_po ='".trim($data['nomor_po'])."' and id!= '".$id."'")->row();
				if(empty($nomorpo)){
					$this->db->update('tr_kartu_po_customer', $ArrHeader,array('id' => $id, 'status'=>'0'));
				}else{
					$bypass="error";
				}
			}
			if($bypass==""){
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=> $TandaI.' data failed. Please try again later ...',
						'status'	=> 0
					);
				} else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=> $TandaI.' data success.',
						'status'	=> 1
					);
					history($TandaI.' PO Customer '.$id);
				}
			}else{
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			echo json_encode($Arr_Kembali);
		} else{
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM tr_kartu_po_customer WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();
			$customer = $this->db->order_by('nm_customer','asc')->get('customer')->result();
			$data = array(
				'title'		=> 'Data PO Customer',
                'action'	=> 'add',
                'customer'	=> $customer,
                'data'      => $result
			);
			$this->load->view('Master_Customer/po_customer_form',$data);
		}
	}
	public function hapus_data_po_customer(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
            $this->db->where(array('id' => $id, 'status'=>'0'));
            $this->db->delete('tr_kartu_po_customer');
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete PO Customer Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	public function lock_data_po_customer(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
		$this->db->update('tr_kartu_po_customer', array('status'=>'1'),array('id' => $id, 'status'=>'0'));
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Lock data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Lock data success. Thanks ...',
				'status'	=> 1
			);
			history('Lock PO Customer Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}

	function data_side_po_customer(){
		$controller		= 'po_customer';
		$Arr_Akses		= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_po_customer(
			$requestData['search']['value'], $requestData['order'][0]['column'], $requestData['order'][0]['dir'], $requestData['start'], $requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		foreach($query->result_array() as $row) {
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc') $nomor = $urut1 + $start_dari;
            if($asc_desc == 'desc') $nomor = ($total_data - $start_dari) - $urut2;
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['nomor_po']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".$row['keterangan']."</div>";
			$detail		= "";
			$edit		= "";
			$delete		= "";
		if($row['status']==0) {
			if($Arr_Akses['delete']=='1'){
				$delete	= "&nbsp; <button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id']."'><i class='fa fa-trash'></i></button>";
			}
			if($Arr_Akses['update']=='1'){
				$edit	= "&nbsp; <button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['id']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button> &nbsp; <button type='button' class='btn btn-sm btn-success lock' data-code='".$row['id']."' title='Lock Data'><i class='fa fa-check-square-o'></i></button>";
			}
		}
			$nestedData[]	= "<div align='left'> ".$edit." ".$delete." </div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}
		$json_data = array(
			"draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ), "recordsFiltered" => intval( $totalFiltered ), "data" => $data
		);
		echo json_encode($json_data);
	}
	public function get_query_json_po_customer($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
            SELECT (@row:=@row+1) AS nomor, a.*
			FROM tr_kartu_po_customer a,
                (SELECT @row:=0) r
            WHERE
                1=1 AND (
                a.nomor_po LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id',
			1 => 'nomor_po',
			2 => 'nm_customer',
			3 => 'keterangan',
		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." LIMIT ".$limit_start." ,".$limit_length." ";
		$data['query'] = $this->db->query($sql);
		return $data;
    }
}
