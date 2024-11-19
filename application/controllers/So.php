<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class So extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
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
			'title'			=> 'Indeks Of SO',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data SO');
		$this->load->view('So/index',$data);
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_ipp']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_po']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['no_so']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_customer']))."</div>";
			$nestedData[]	= "<div align='center'>".date('d M Y', strtotime($row['tgl_rilis']))."</div>";
			$nestedData[]	= "<div align='center'>".date('d M Y', strtotime($row['tgl_akhir']))."</div>";
				if($row['status'] == 'PENDING'){
					$class	= 'bg-orange';
					$status	= 'PENDING';
				}
				if($row['status'] == 'PROCESS'){
					$class	= 'bg-blue';
					$status	= 'PROCESS';
				}
				if($row['status'] == 'CLOSE'){
					$class	= 'bg-green';
					$status	= 'CLOSE';
				}
				if($row['status'] == 'CANCELED'){
					$class	= 'bg-red';
					$status	= 'CANCELED';
				}
			$nestedData[]	= "<div align='center'><span class='badge ".$class."'>".$status."</span></div>";
						$updX = "";
						$delX	= "";
					// $updX	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/edit/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit SO' data-role='qtip'><i class='fa fa-edit'></i></a>";
					if($Arr_Akses['delete']=='1'){
						$delX	= "<button class='btn btn-sm btn-danger' id='deleteSO' title='Permanent Delete SO' data-id_so='".$row['id']."'><i class='fa fa-trash'></i></button>";
					}
			$nestedData[]	= "<div align='center'>
									<button type='button' id='detailSO' data-id='".$row['id']."' data-no_so='".$row['no_so']."' class='btn btn-sm btn-warning' title='View Data SO' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$updX."
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				*
			FROM
				so_header
		    WHERE deleted = 'N' AND (
				no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'no_po',
			3 => 'no_so',
			4 => 'nm_customer',
			5 => 'tgl_rilis',
			5 => 'tgl_akhir'
			
		);

		$sql .= " ORDER BY tgl_akhir ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function add(){
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			
			//Pencarian data yang sudah ada 
			$qCust		= "SELECT nm_customer FROM customer WHERE id_customer='".$data['id_customer']."' LIMIT 1";
			$NmCust		= $this->db->query($qCust)->result_array();
			
			$Data_Insert			= array(
				'no_ipp'			=> ucfirst(strtolower($data['no_ipp'])),
				'no_po'				=> ucfirst(strtolower($data['no_po'])),
				'no_so'				=> ucfirst(strtolower($data['no_so'])),
				'tgl_rilis'			=> $data['tgl_rilis'],
				'tgl_akhir'			=> $data['tgl_akhir'],
				'id_customer'		=> $data['id_customer'],
				'nm_customer'		=> $NmCust[0]['nm_customer'],
				'project'			=> $data['project'],
				'ket'				=> $data['ket'],
				'created_by'		=> $this->session->userdata['ORI_User']['username'],
				'created_date'		=> date('Y-m-d H:i:s')
			);
			
			// echo "<pre>"; print_r($Data_Insert);
			// exit;
		
			$this->db->trans_start();
			$this->db->insert('so_header', $Data_Insert);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add SO data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Add SO data success. Thanks ...',
					'status'	=> 1
				);
				history('Add SO Data');
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$dataType	= "SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC";
			$restType	= $this->db->query($dataType)->result_array();
			$data = array(
				'title'			=> 'Add SO',
				'action'		=> 'add',
				'CustList'		=> $restType
			);
			$this->load->view('So/add',$data);
		}
	}

	public function edit() {
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');
			
			$paret_product	= $data['parent_product'];
			$diameter		= $data['value_d'];
			$id				= $data['id'];
			
			//Pencarian data yang sudah ada 
			$ValueProduct	= "SELECT * FROM product WHERE parent_product='".$paret_product."' AND value_d='".$diameter."' LIMIT 1";
			$NumProduct		= $this->db->query($ValueProduct)->num_rows();
			
			// echo $ValueProduct."<br>";
			// echo $NumProduct;
			
			if($NumProduct > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Diameter of the product already exists. Please input different ...'
				);
			}
			else{
				$Arr_Update			= array(
					'nm_product'		=> ucfirst(strtolower($data['nm_product'])),
					'parent_product'	=> $data['parent_product'],
					'value_d'			=> $data['value_d'],
					'ket'				=> $data['ket'],
					'modified_by'		=> $this->session->userdata['ORI_User']['username'],
					'modified_date'		=> date('Y-m-d H:i:s')
				);
				
				// echo "<pre>"; print_r($Arr_Update);
				// exit;
			
				$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('product', $Arr_Update);
				$this->db->trans_complete();
				
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Edit type product data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Edit type product data success. Thanks ...',
						'status'	=> 1
					);
					history('Edit Type Product kode: '.$id);
				}	
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			
			$id = $this->uri->segment(3);
			
			$qProduct		= "SELECT * FROM product WHERE id='".$id."' LIMIT 1 ";
			$dataProduct	= $this->db->query($qProduct)->result_array();
			
			$dataType		= "SELECT * FROM product_parent ORDER BY product_parent ASC";
			$restType		= $this->db->query($dataType)->result_array();
			$data = array(
				'title'		=> 'Add So',
				'action'	=> 'add',
				'data'		=> $dataProduct,
				'type'		=> $restType
			);
			
			$this->load->view('So/edit',$data);
		}
	}

	function hapus(){
		$id 		= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);
		
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('so_header', $ArrPlant);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete SO data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete SO data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete SO with Kode/Id : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	public function modalDetail(){
		$this->load->view('So/modalDetail');
	}

}
