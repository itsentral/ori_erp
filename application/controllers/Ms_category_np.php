<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_category_np extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		if(!$this->session->userdata('isORIlogin')) redirect('login');
	}
	public function index(){
		$controller			= 'ms_category_np';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Index Of Category Invoice Non Product',
			'action'		=> 'ms_category_np',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Category Invoice Non Product');
		$this->load->view('Ms_category_np/index',$data);
	}
	public function data_side(){
		$controller		= 'ms_category_np';
		$Arr_Akses		= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json(
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
		foreach($query->result_array() as $row) {
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc') $nomor = $row['id'];
            if($asc_desc == 'desc') $nomor = $row['id'];
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['nama']."</div>";
			$detail		= "";
			$edit		= "";
			$delete		= "";
			if($Arr_Akses['delete']=='1'){
				$delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id']."'><i class='fa fa-trash'></i></button>";
			}
			if($Arr_Akses['update']=='1'){
				$edit	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['id']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button>";
			}
			$nestedData[]	= "<div align='left'> ".$edit." ".$delete." </div>";
			$data[] = $nestedData;
		}
		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data
		);
		echo json_encode($json_data);
	}
	public function get_query_json($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
            SELECT               
				a.*
			FROM
                ms_inv_category a
            WHERE
                1=1 AND (
                a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id',
			1 => 'nama',
		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		$data['query'] = $this->db->query($sql);
		return $data;
    }
	public function add_data(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			$id 		    = $data['id'];
			$nama			= $data['nama'];
			if(empty($id)){
                $ArrHeader = array(
                    'nama' 			=> $nama,
                );
                $TandaI = "Insert";
			}
			if(!empty($id)){
                $ArrHeader = array(
                    'nama' 			=> $nama,
                );
                $TandaI = "Update";
            }
            $this->db->trans_start();
                if(empty($id)) $this->db->insert('ms_inv_category', $ArrHeader);
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('ms_inv_category', $ArrHeader);
                }
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
				history($TandaI.' Category Invoice NP '.$id.' / '.$nama);
			}

			echo json_encode($Arr_Kembali);
		} else{
			$controller			= 'ms_category_np';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM ms_inv_category WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();
			$data = array(
				'title'		=> 'Data Category Invoice Non Product',
                'action'	=> 'add',
                'data'      => $result
			);
			$this->load->view('Ms_category_np/form',$data);
		}
	}
	public function hapus_data(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->delete('ms_inv_category');
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
			history('Delete Category Invoice Non Product Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
}
