<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_packing extends CI_Controller {
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
		
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Packing Cost',
			'action'		=> 'profit',
			'listkomponen'	=> $getKomp,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Packing Cost');
		$this->load->view('Cost_packing/index',$data);
	}
	
	public function add_profit(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;

			$product_parent	= $data['product_parent'];
			$standard_code	= $data['standard_code'];
			$diameter		= $data['diameter'];
			$diameter2		= (!empty($data['diameter2']))?$data['diameter2']:0;
			$profit			= $data['profit'];
			
			$sqlNum		= "SELECT * FROM cost_packing WHERE product_parent='".$product_parent."' AND diameter='".$diameter."' AND diameter2='".$diameter2."' ";
			$NumSql		= $this->db->query($sqlNum)->num_rows();
			
			$data	= array(
				'product_parent' 	=> $product_parent,
				'standard_code' 	=> $standard_code,
				'diameter' 		=> $diameter,
				'diameter2' 	=> $diameter2,
				'profit' 		=> $profit,
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_on' 	=> date('Y-m-d H:i:s')
			);
			// print_r($data);
			// exit;
			if($NumSql < 1){
			
				$this->db->trans_start();
					$this->db->insert('cost_packing', $data);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'			=> 'Failed Add Packing Cost. Please try again later ...',
						'status'		=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'			=> 'Success Add Packing Cost. Thanks ...',
						'status'		=> 1
						
					);
					history('Add Packing Cost'); 
				}
			}else{
				$Arr_Kembali	= array(
					'pesan'			=> 'Specifications Already Exist...',
					'status'		=> 0
				);
			}
			echo json_encode($Arr_Kembali);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			
			$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
			
			$data = array(
				'title'			=> 'Add Enggenering Cost',
				'action'		=> 'add_profit',
				'listkomponen'	=> $getKomp
			);
			$this->load->view('Cost_packing/add_profit',$data);
		}
	}
	
	public function edit_profit(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$id				= $data['id'];
			$product_parent	= $data['product_parent'];
			$diameter		= $data['diameter'];
			$diameter2		= (!empty($data['diameter2']))?$data['diameter2']:0;
			$profit			= $data['profit'];
			
			$sqlNum		= "SELECT * FROM cost_packing WHERE product_parent='".$product_parent."' AND diameter='".$diameter."' AND diameter2='".$diameter2."' ";
			$NumSql		= $this->db->query($sqlNum)->num_rows();
			
			$data	= array(
				'product_parent' 	=> $product_parent,
				'diameter' 		=> $diameter,
				'diameter2' 	=> $diameter2,
				'profit' 		=> $profit,
				'modified_by' 	=> $data_session['ORI_User']['username'],
				'modified_on' 	=> date('Y-m-d H:i:s')
			);
			// print_r($data);
			// exit;
			// if($NumSql < 1){
			
				$this->db->trans_start();
					$this->db->where('id', $id);
					$this->db->update('cost_packing', $data);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'			=> 'Failed Edit Packing Cost. Please try again later ...',
						'status'		=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'			=> 'Success Edit Packing Cost. Thanks ...',
						'status'		=> 1
						
					);
					history("Edit Packing Cost ".$product_parent." | ".$diameter." | ".$diameter2); 
				}
			// }else{
				// $Arr_Kembali	= array(
					// 'pesan'			=> 'Specifications Already Exist...',
					// 'status'		=> 0
				// );
			// }
			echo json_encode($Arr_Kembali);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$id = $this->uri->segment(3);
			$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
			$getData		= $this->db->query("SELECT * FROM cost_packing WHERE id='".$id."' ORDER BY product_parent ASC")->result_array();
			
			$data = array(
				'title'			=> 'Edit Packing Cost',
				'action'		=> 'add_profit',
				'listkomponen'	=> $getKomp,
				'data'			=> $getData
			);
			$this->load->view('Cost_packing/add_profit',$data);
		}
	}
	
	public function hapus_profit(){
		$id 	= $this->uri->segment(3);
		$parent = $this->uri->segment(4);
		$d1 	= $this->uri->segment(5);
		$d2 	= $this->uri->segment(6);
		
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->delete('cost_packing');
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Packing Cost with Kode/Id : '.$id.' | '.$parent.' | '.$d1.' | '.$d2);
		}
		echo json_encode($Arr_Data);
	}
	
	public function getDataJSON(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1))); 
  		$Arr_Akses			= getAcccesmenu($controller);
  		$requestData	= $_REQUEST;
  		$fetch			= $this->queryDataJSON(
  			$requestData['komponen'],
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
			  
			$updated_by = (!empty($row['modified_by']))?ucwords(strtolower($row['modified_by'])):ucwords(strtolower($row['created_by']));
			$updated_date = (!empty($row['modified_on']))?$row['modified_on']:$row['created_on'];

  			$nestedData 	= array();
  			$nestedData[]	= "<div align='center'>".$nomor."</div>";
  			$nestedData[]	= "<div align='left'>".strtoupper($row['product_parent'])."</div>";
  			$nestedData[]	= "<div align='center'>".number_format($row['diameter'])."</div>";
  			$nestedData[]	= "<div align='center'>".number_format($row['diameter2'])."</div>";
			$nestedData[]	= "<div align='center'>".floatval($row['profit'])."</div>";
			$nestedData[]	= "<div align='center'>".$updated_by."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($updated_date))."</div>";
			$nestedData[]	= "<div align='center' >
									<a href='".site_url('cost_packing/edit_profit/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>
									&nbsp;<a id='deleteID' data-id='".$row['id']."' data-product='".$row['product_parent']."' data-d1='".$row['diameter']."' data-d2='".$row['diameter2']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>
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
	
	
	public function queryDataJSON($komponen, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND product_parent = '".$komponen."' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				cost_packing a,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_komponen."
				AND a.deleted ='N' AND (
				a.standard_code LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'product_parent',
			2 => 'diameter',
			3 => 'diameter2'
		);

		$sql .= " ORDER BY id ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	//============EXPORT TRANSPOER====================
	
	public function export(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/export';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Export Transport',
			'action'		=> 'export',
			'listkomponen'	=> $getKomp,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Export Transport');
		$this->load->view('Cost_packing/export',$data);
	}
	
	public function add_export(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;

			$country_code	= $data['country_code'];
			$shipping_name	= $data['shipping_name'];
			$price			= $data['price'];
			
			$data	= array(
				'country_code' 	=> $country_code,
				'shipping_name' => $shipping_name,
				'price' 		=> $price,
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_on' 	=> date('Y-m-d H:i:s')
			);
			// print_r($data);
			// exit;
			
			
			$this->db->trans_start();
				$this->db->insert('cost_export_trans', $data);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Add Export Cost. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Add Export Cost. Thanks ...',
					'status'		=> 1
					
				);
				history('Add Export Transport Cost'); 
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/export';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			
			//country
			$qCountry	= "SELECT * FROM country ORDER BY country_name ASC";
			$restContry	= $this->db->query($qCountry)->result_array();
			//shipping
			$qShipping	= "SELECT * FROM list_shipping WHERE flag = 'Y'";
			$restShip	= $this->db->query($qShipping)->result_array();
			
			$data = array(
				'title'			=> 'Add Export Cost',
				'action'		=> 'add_export',
				'CountryName'	=> $restContry,
				'ShippingName'	=> $restShip
			);
			$this->load->view('Cost_packing/add_export',$data);
		}
	}
	
	public function edit_export(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$id				= $data['id'];
			$country_code	= $data['country_code'];
			$shipping_name	= $data['shipping_name'];
			$price			= $data['price'];
			
			$data	= array(
				'country_code' 	=> $country_code,
				'shipping_name' => $shipping_name,
				'price' 		=> $price,
				'modified_by' 	=> $data_session['ORI_User']['username'],
				'modified_on' 	=> date('Y-m-d H:i:s')
			);
			// print_r($data);
			// exit;
			// if($NumSql < 1){
			
				$this->db->trans_start();
					$this->db->where('id', $id);
					$this->db->update('cost_export_trans', $data);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'			=> 'Failed Edit Export Cost. Please try again later ...',
						'status'		=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'			=> 'Success Edit Export Cost. Thanks ...',
						'status'		=> 1
						
					);
					history("Edit Export Cost ".$country_code." | ".$shipping_name); 
				}
			// }else{
				// $Arr_Kembali	= array(
					// 'pesan'			=> 'Specifications Already Exist...',
					// 'status'		=> 0
				// );
			// }
			echo json_encode($Arr_Kembali);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/export';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$id = $this->uri->segment(3);
			$getData		= $this->db->query("SELECT * FROM cost_export_trans WHERE id='".$id."'")->result_array();
			
			//country
			$qCountry	= "SELECT * FROM country ORDER BY country_name ASC";
			$restContry	= $this->db->query($qCountry)->result_array();
			//shipping
			$qShipping	= "SELECT * FROM list_shipping WHERE flag = 'Y'";
			$restShip	= $this->db->query($qShipping)->result_array();
			
			$data = array(
				'title'			=> 'Edit Export Cost',
				'action'		=> 'add_export',
				'CountryName'	=> $restContry,
				'ShippingName'	=> $restShip,
				'data'			=> $getData
			);
			$this->load->view('Cost_packing/add_export',$data);
		}
	}
	
	public function modalAddCountry(){
		$this->load->view('Cost_packing/modalAddCountry');
	}
	
	public function addCountry(){
		$data				= $this->input->post();
		
		$getNum	= $this->db->query("SELECT * FROM country WHERE country_code='".strtoupper($data['country'])."' ")->num_rows();
		$getCountry	= $this->db->query("SELECT * FROM country_all WHERE iso3='".strtoupper($data['country'])."' ")->result_array();
		
		$insertData	= array(
			'country_code'	=> strtoupper($data['country']),
			'country_name'	=> $getCountry[0]['name']
		);
		
		if($getNum < 1){
			$this->db->trans_start();
				$this->db->insert('country', $insertData);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Failed Add Country. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Success Add Country. Thanks ...',
					'status'	=> 1
				);
				history('Add Country Data by Costing'); 
			}
		}
		else{
			$Arr_Kembali	= array(
					'pesan'		=>'Country Name Already exists',
					'status'	=> 0
				);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function getDataJSONExport(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/export';
  		$Arr_Akses			= getAcccesmenu($controller);
  		$requestData	= $_REQUEST;
  		$fetch			= $this->queryDataJSONExport(
  			$requestData['komponen'],
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
			  
			$updated_by = (!empty($row['modified_by']))?ucwords(strtolower($row['modified_by'])):ucwords(strtolower($row['created_by']));
			$updated_date = (!empty($row['modified_on']))?$row['modified_on']:$row['created_on'];

  			$nestedData 	= array();
  			$nestedData[]	= "<div align='center'>".$nomor."</div>";
  			$nestedData[]	= "<div align='left'>".strtoupper($row['country_name'])."</div>";
  			$nestedData[]	= "<div align='center'>".strtoupper($row['shipping_name'])."</div>";
			$nestedData[]	= "<div align='center'>".floatval($row['price'])."</div>";
			$nestedData[]	= "<div align='center'>".$updated_by."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($updated_date))."</div>";

			$update = "";
			$delete = "";
			if($Arr_Akses['update']=='1'){
				$update = "<a href='".site_url('cost_packing/edit_export/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}
			if($Arr_Akses['delete']=='1'){
				$delete = "&nbsp;<a id='deleteID' data-id='".$row['id']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
			}

			$nestedData[]	= "<div align='center' > 
								".$update."	
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
	
	public function queryDataJSONExport($komponen, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_komponen = "";
		// if(!empty($komponen)){
			// $where_komponen = " AND product_parent = '".$komponen."' ";
		// }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.country_name
			FROM
				cost_export_trans a 
				LEFT JOIN country b ON a.country_code=b.country_code,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_komponen."
				AND a.deleted ='N' AND (
				a.country_code LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.shipping_name LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'country_code',
			2 => 'shipping_name'
		);

		$sql .= " ORDER BY a.id ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function hapus_export(){
		$id 	= $this->uri->segment(3);
		$data					= $this->input->post();
		$data_session			= $this->session->userdata;
		$data	= array(
			'deleted'		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('cost_export_trans', $data);
		$this->db->trans_complete();
		
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Export Cost with Kode/Id : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	//============TRUCKING LOCAL====================
	public function trucking(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/export';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$menu_akses= $this->master_model->getMenu();

		$category = $this->db->select('category')->group_by('category')->order_by('category','asc')->get('cost_trucking')->result_array();
		$area = $this->db->select('area')->group_by('area')->order_by('area','asc')->get('cost_trucking')->result_array();
		$dest = $this->db->select('tujuan')->group_by('tujuan')->order_by('tujuan','asc')->get('cost_trucking')->result_array();
		$truck = $this->db->select('id, nama_truck')->order_by('nama_truck','asc')->get('truck')->result_array();
		$provinsi 		= $this->db->get('provinsi')->result_array();
		$kabupaten 		= $this->db->get('kabupaten')->result_array();
		
		
		$data = array(
			'title'			=> 'Indeks Of Trucking Local',
			'action'		=> 'trucking',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'category'	=> $category,
			'area'	=> $area,
			'provinsi'	=> $provinsi,
			'kabupaten'	=> $kabupaten,
			'dest'	=> $dest,
			'truck'	=> $truck
		);
		history('View data price trucking local');
		$this->load->view('Cost_packing/trucking',$data);
	}

	public function add_trucking(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;

			$id			= $data['id'];
			$tanda		= $data['tanda'];
			$category	= $data['category'];
			$id_truck	= $data['id_truck'];
			$area		= $data['area'];
			$tujuan		= $data['tujuan'];
			$prov		= $data['prov'];
			$kota		= $data['kota'];
			$price		= str_replace(',','',$data['price']);
			
			if(empty($id)){
				$by 	= 'created_by';
				$cdate 	= 'created_date';
			}
			if(!empty($id)){
				$by 	= 'updated_by';
				$cdate 	= 'updated_date';
			}

			$data = array(
				'category' 		=> $category,
				'id_truck' 		=> $id_truck,
				'area' 			=> $area,
				'tujuan' 		=> $tujuan,
				'prov' 			=> $prov,
				'kota' 			=> $kota,
				'price' 		=> $price,
				$by 	=> $data_session['ORI_User']['username'],
				$cdate 	=> date('Y-m-d H:i:s')
			);

			//History
			if(!empty($id)){
				$history = $this->db->get_where('cost_trucking',array('id'=>$id))->result();
				$hist = array(
					'id' 			=> $history[0]->id,
					'category' 		=> $history[0]->category,
					'id_truck' 		=> $history[0]->id_truck,
					'area' 			=> $history[0]->area,
					'tujuan' 		=> $history[0]->tujuan,
					'prov' 			=> $history[0]->prov,
					'kota' 			=> $history[0]->kota,
					'price' 		=> $history[0]->price,
					'created_by' 	=> $history[0]->created_by,
					'created_date' 	=> $history[0]->created_date,
					'updated_by' 	=> $history[0]->updated_by,
					'updated_date' 	=> $history[0]->updated_date,
					'hist_by' 		=> $data_session['ORI_User']['username'],
					'hist_date' 	=> date('Y-m-d H:i:s')
				);
			}
			// print_r($data);
			// exit;
			
			$this->db->trans_start();
				if(empty($id)){
					$this->db->insert('cost_trucking', $data);
				}
				if(!empty($id)){
					$this->db->where('id', $id);
					$this->db->update('cost_trucking', $data);

					$this->db->insert('hist_cost_trucking', $hist);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed process. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success process. Thanks ...',
					'status'		=> 1
					
				);
				history($tanda.' local transport'); 
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/trucking';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}

			$id 			= $this->uri->segment(3);
			$tanda 			= (!empty($id))?'Edit':'Add';
			$list_truck		= $this->db->get('truck')->result_array();
			$list_area		= $this->db->group_by('area')->get('cost_trucking')->result_array();
			$data_detail	= $this->db->get_where('cost_trucking', array('id'=>$id))->result_array();
			$provinsi 		= $this->db->get('provinsi')->result_array();
			
			$data = array(
				'title'			=> 'Add Trucking Local',
				'action'		=> 'add_trucking',
				'tanda'			=> $tanda,
				'list_truck'	=> $list_truck,
				'provinsi'		=> $provinsi,
				'list_area'		=> $list_area,
				'data'			=> $data_detail
			);

			$this->load->view('Cost_packing/add_trucking',$data);
		}
	}

	public function get_kota(){
		$id = $this->uri->segment(3);
		$cs = $this->uri->segment(4);
		$query	 	= "SELECT * FROM kabupaten WHERE id_prov='".$id."'";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select An City</option>";
		foreach($Q_result as $row)	{
			$selx = ($row->id_kab == $cs)?'selected':'';
			$option .= "<option value='".$row->id_kab."' ".$selx.">".strtoupper($row->nama)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

	public function getDataJSONTrucking(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/trucking';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONTrucking(
			$requestData['category'],
			$requestData['area'],
			$requestData['dest'],
			$requestData['truck'],
			$requestData['prov'],
			$requestData['kota'],
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
			
			$updated_by 	= ucwords(strtolower($row['created_by']));
			$updated_date = $row['created_date'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['category'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['area'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['tujuan'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_provinsi'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_kota'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nama_truck'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['price'])."</div>";
			// $nestedData[]	= "<div align='center'>".$updated_by."</div>";
			// $nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($updated_date))."</div>";

			$update = "";
			$delete = "";
			if($Arr_Akses['update']=='1'){
				$update = "<a href='".site_url('cost_packing/add_trucking/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}
		//   if($Arr_Akses['delete']=='1'){
		// 	  $delete = "&nbsp;<a id='deleteID' data-id='".$row['id']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
		//   }

		  	$nestedData[]	= "<div align='center' > 
							  ".$update."	
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
  
	public function queryDataJSONTrucking($category, $area, $dest, $truck, $prov, $kota, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_category = "";
		if($category <> '0'){
			$where_category = " AND a.category = '".$category."' ";
		}

		$where_area = "";
		if($area <> '0'){
			$where_area = " AND a.area = '".$area."' ";
		}

		$where_dest = "";
		if($dest <> '0'){
			$where_dest = " AND a.tujuan = '".$dest."' ";
		}

		$where_truck = "";
		if($truck <> '0'){
			$where_truck = " AND a.id_truck = '".$truck."' ";
		}

		$where_prov = "";
		if($prov <> '0'){
			$where_prov = " AND a.prov = '".$prov."' ";
		}

		$where_kota = "";
		if($kota <> '0'){
			$where_kota = " AND a.kota = '".$kota."' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nama_truck,
				c.nama AS nm_provinsi,
				d.nama AS nm_kota
			FROM
				cost_trucking a 
				LEFT JOIN truck b ON a.id_truck=b.id
				LEFT JOIN provinsi c ON a.prov=c.id_prov
				LEFT JOIN kabupaten d ON a.kota=d.id_kab,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_category."
				".$where_area."
				".$where_dest."
				".$where_truck."
				".$where_prov."
				".$where_kota."
				AND (
				a.area LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.tujuan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nama_truck LIKE '%".$this->db->escape_like_str($like_value)."%'
			)
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'category',
			2 => 'area',
			3 => 'tujuan',
			4 => 'nama_truck'
		);

		$sql .= " ORDER BY  ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalAddTruck(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session = $this->session->userdata;
			$getNum	= $this->db->query("SELECT * FROM truck WHERE LOWER(nama_truck)='".strtolower($data['nama_truck'])."' ")->num_rows();
			$insertData	= array(
				'nama_truck'	=> strtolower($data['nama_truck']),
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			if($getNum < 1){
				$this->db->trans_start();
					$this->db->insert('truck', $insertData);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Add Country. Please try again later ...',
						'status'	=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Success Add Country. Thanks ...',
						'status'	=> 1
					);
					history('Add data truck '.strtolower($data['nama_truck'])); 
				}
			}
			else{
				$Arr_Kembali	= array(
					'pesan'		=>'Truck sudah terdaftar',
					'status'	=> 0
				);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$this->load->view('Cost_packing/modalAddTruck');
		}
	}

}