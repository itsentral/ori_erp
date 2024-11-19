<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('product_model');

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
			'title'			=> 'Indeks Of Product',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Product');
		$this->load->view('Product/index',$data);
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
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
			}
			
			$d1 = ' '.$row['value_d'].' mm';
			$d2 = '';
			if(!empty($row['value_d2'])){
				$d2 = ' x '.$row['value_d2'].' mm';
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['parent_product'])).",".$d1.$d2."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['parent_product']))."</div>";
			$nestedData[]	= "<div align='right' style='padding-right: 15px;'>".$row['value_d']." mm</div>";
			$nestedData[]	= "<div align='right' style='padding-right: 15px;'>".$row['value_d2']." mm</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['ket']))."</div>";
				$last_by 	= (!empty($row['modified_by']))?$row['modified_by']:$row['created_by'];
				$last_date = (!empty($row['modified_date']))?$row['modified_date']:$row['created_date'];
			
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($last_by ))."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($last_date))."</div>";

					$updX	= "";
					$delX	= "";
					if($Arr_Akses['update']=='1'){
						$updX	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/edit/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($Arr_Akses['delete']=='1'){
						$delX	= "<button class='btn btn-sm btn-danger' id='deletePlant' title='Permanent Company Plant' data-id_plant='".$row['id']."'><i class='fa fa-trash'></i></button>";
					}
			$nestedData[]	= "<div align='center'>
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
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				product a,
				(SELECT @row:=0) r
		    WHERE a.deleted = 'N' AND (
				a.nm_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.parent_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.value_d LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.value_d2 LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_product',
			2 => 'parent_product',
			3 => 'value_d',
			4 => 'value_d2',
			5 => 'ket',
			6 => 'created_by',
			7 => 'created_date'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function add(){
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');
			
			$paret_product		= $data['parent_product'];
			$diameter			= $data['value_d'];
			$diameter2			= $data['value_d2'];
			
			//Pencarian data yang sudah ada 
			$ValueProduct	= "SELECT * FROM product WHERE parent_product='".$paret_product."' AND value_d='".$diameter."' AND value_d2='".$diameter2."' LIMIT 1";
			$NumProduct		= $this->db->query($ValueProduct)->num_rows();
			
			// echo $ValueProduct."<br>";
			// echo $NumProduct;
			
			if($NumProduct > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Spesifikasi product sudah digunakan. Input spesifikasi lain ...' 
				);
			}
			else{
				$Data_Insert			= array(
					'nm_product'		=> ucfirst(strtolower($data['nm_product'])),
					'parent_product'	=> $data['parent_product'],
					'value_d'			=> $data['value_d'],
					'value_d2'			=> (!empty($data['value_d2']))?$data['value_d2']:0,
					'ket'				=> $data['ket'],
					'created_by'		=> $this->session->userdata['ORI_User']['username'],
					'created_date'		=> date('Y-m-d H:i:s')
				);
				
				// echo "<pre>"; print_r($Data_Insert);
				// exit;
			
				$this->db->trans_start();
				$this->db->insert('product', $Data_Insert);
				$this->db->trans_complete();
				
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add type product data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add type product data success. Thanks ...',
						'status'	=> 1
					);
					history('Add Type Product');
				}	
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

			$dataType	= "SELECT * FROM product_parent ORDER BY product_parent ASC";
			$restType	= $this->db->query($dataType)->result_array();
			$data = array(
				'title'			=> 'Add Product',
				'action'		=> 'add',
				'type'			=> $restType
			);
			$this->load->view('Product/add',$data);
		}
	}

	public function edit() {
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');
			
			$paret_product	= $data['parent_product'];
			$diameter		= $data['value_d'];
			$diameter2		= $data['value_d2'];
			$id				= $data['id'];
			
			//Pencarian data yang sudah ada 
			$ValueProduct	= "SELECT * FROM product WHERE parent_product='".$paret_product."' AND value_d='".$diameter."' LIMIT 1";
			$NumProduct		= $this->db->query($ValueProduct)->num_rows();
			
			// echo $ValueProduct."<br>";
			// echo $NumProduct;
			
			// if($NumProduct > 0){
				// $Arr_Kembali		= array(
					// 'status'		=> 3,
					// 'pesan'			=> 'Diameter of the product already exists. Please input different ...'
				// );
			// }
			// else{
				$Arr_Update			= array(
					'nm_product'		=> ucfirst(strtolower($data['nm_product'])),
					'parent_product'	=> $data['parent_product'],
					'value_d'			=> $data['value_d'],
					'value_d2'			=> $data['value_d2'],
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
			// }
			
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
				'title'		=> 'Add Product',
				'action'	=> 'add',
				'data'		=> $dataProduct,
				'type'		=> $restType
			);
			
			$this->load->view('Product/edit',$data);
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
		// $this->db->where('id', $id);
		// $this->db->update('product', $ArrPlant);
		$this->db->delete('product', array('id' => $id)); 
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete category product with Kode/Id : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	public function modalDetail(){
		$this->load->view('Company_plants/modalDetail');
	}

	//================================================================================================
	//=========================================TYPE===================================================
	//================================================================================================

	public function type(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Product Type',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Product Type');
		$this->load->view('Product/type',$data);
	}

	public function data_side_type(){
		$this->product_model->get_json_type();
	}

	public function add_type(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			// print_r($data);exit;
			$product_parent	= preg_replace('/\s+/',' ',trim(strtolower($data['product_parent'])));
			$type			= $data['type'];
			$type2			= $data['type2'];
			$code			= trim(strtoupper($data['code']));
			$ket			= $data['ket'];
			$estimasi		= $data['estimasi'];
			$spec1			= trim(strtolower($data['spec1']));
			$spec2			= trim(strtolower($data['spec2']));
			
			//Pencarian data yang sudah ada 
			$sql_parent		= "SELECT * FROM product_parent WHERE product_parent='".$product_parent."' LIMIT 1";
			$rest_parent	= $this->db->query($sql_parent)->result();
			
			$sql_code		= "SELECT * FROM product_parent WHERE kode='".$code."' LIMIT 1";
			$rest_code		= $this->db->query($sql_code)->result();

			if(!empty($rest_parent)){
				$Arr_Kembali		= array(
					'status'		=> 2,
					'pesan'			=> 'Sudah digunakan oleh product '.strtoupper($rest_parent[0]->product_parent).'. Input nama yang lain ...' 
				);
				echo json_encode($Arr_Kembali);
				return false;
			}
			elseif(!empty($rest_code)){
				$Arr_Kembali		= array(
					'status'		=> 2,
					'pesan'			=> 'Sudah digunakan oleh product '.strtoupper($rest_code[0]->product_parent).'. Input kode yang lain ...' 
				);
				echo json_encode($Arr_Kembali);
				return false;
			}
			else{
				$Data_Insert	= array(
					'product_parent'=> $product_parent,
					'type'			=> $type,
					'type2'			=> $type2,
					'kode'			=> $code,
					'ket'			=> $ket,
					'estimasi'		=> $estimasi,
					'spec1'			=> $spec1,
					'spec2'			=> $spec2,
					'created_by'	=> $this->session->userdata['ORI_User']['username'],
					'created_date'	=> date('Y-m-d H:i:s')
				);
				
				// print_r($Data_Insert);
				// exit;
			
				$this->db->trans_start();
				$this->db->insert('product_parent', $Data_Insert);
				$this->db->trans_complete();
				
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add type product data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add type product data success. Thanks ...',
						'status'	=> 1
					);
					history('Add type product parent '.$product_parent);
				}	
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

			$dataType	= "SELECT * FROM parent_type WHERE type='fitting' OR type='field' ORDER BY type DESC";
			$restType	= $this->db->query($dataType)->result_array();
			$data = array(
				'title'			=> 'Add Product Type',
				'action'		=> 'add',
				'type'			=> $restType
			);
			$this->load->view('Product/add_type',$data);
		}
	}
	
	public function get_custom_product(){
		$product_parent	= str_replace('0_0', ' ', $this->uri->segment(3));
		
		$sql_parent		= "SELECT * FROM product_parent WHERE product_parent='".$product_parent."' LIMIT 1";
		$rest_parent	= $this->db->query($sql_parent)->result();
		$nomor = 0;
		if(!empty($rest_parent[0]->spec2)){
			$nomor = 1;
		}
		$spec1 = ucwords(strtolower($rest_parent[0]->spec1));
		$spec2 = ucwords(strtolower($rest_parent[0]->spec2));
			
		echo json_encode(array(
			'nomor' => $nomor,
			'spec1' => $spec1,
			'spec2' => $spec2
		));
   }
   
	public function costcenter_urut(){
		$this->product_model->costcenter_urut();
	}
	
	public function get_add(){
		$this->product_model->get_add();
	}
	
	public function delete_permanent(){
		$this->product_model->delete_permanent();
	}

}
