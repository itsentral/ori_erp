<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost extends CI_Controller {
	public function __construct(){
        parent::__construct(); 
		$this->load->model('master_model');
		$this->load->database();
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	public function modalDetail1(){
		$this->load->view('Cost/modalDetail1');
	}
	
	public function modalEdit1(){
		$this->load->view('Cost/modalEdit1');
	}

	public function process(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/process';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->master_model->getData('cost_process','category','pipe fitting');
		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT update_by, update_on FROM cost_process_auto LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Process Cost',
			'action'		=> 'process',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View Data Cost Process');
		$this->load->view('Cost/process',$data);
	}

	public function process_tanki(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/process_tanki';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->master_model->getData('cost_process','category','tanki');
		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT update_by, update_on FROM cost_process_auto LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Process Cost Tanki',
			'action'		=> 'process',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View Data Cost Process tanki');
		$this->load->view('Cost/process_tanki',$data);
	}
	
	public function foh(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/foh';;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->master_model->getData('cost_foh','category','pipe fitting');
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of FOH Cost',
			'action'		=> 'foh',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data FOH Cost');
		$this->load->view('Cost/foh',$data);
	}
	
	public function add_foh(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			

			$item_cost		= $data['item_cost'];
			$std_rate		= $data['std_rate'];
			$std_hitung		= $data['std_hitung'];
			
			// echo $numType; exit;
			$data	= array(
				'item_cost' 	=> $item_cost,
				'std_rate' 		=> $std_rate,
				'std_hitung' 	=> $std_hitung,
				'satuan' 		=> '%',
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			// print_r($data);
			// exit;
			
			$this->db->trans_start();
				$this->db->insert('cost_foh', $data);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Add FOH Cost. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Add FOH Cost. Thanks ...',
					'status'		=> 1
					
				);
				history('Add FOH Cost'); 
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
			
			$data = array(
				'title'			=> 'Add FOH Cost',
				'action'		=> 'add_foh'
			);
			$this->load->view('Cost/add_foh',$data);
		}
	}

	public function foh_tanki(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/foh_tanki';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->master_model->getData('cost_foh','category','tanki');
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Tanki FOH Cost',
			'action'		=> 'foh',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data tanki FOH Cost');
		$this->load->view('Cost/foh_tanki',$data);
	}
	
	public function add_process(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			

			$item_cost		= $data['item_cost'];
			$std_rate		= $data['std_rate'];
			$std_hitung		= $data['std_hitung'];
			
			// echo $numType; exit;
			$data	= array(
				'item_cost' 	=> $item_cost,
				'std_rate' 		=> $std_rate,
				'std_hitung' 	=> $std_hitung,
				'satuan' 		=> 'USD',
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			// print_r($data);
			// exit;
			
			$this->db->trans_start();
				$this->db->insert('cost_process', $data);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Add Process Cost. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Add Process Cost. Thanks ...',
					'status'		=> 1
					
				);
				history('Add Process Cost'); 
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
			
			$data = array(
				'title'			=> 'Add Process Cost',
				'action'		=> 'add_process'
			);
			$this->load->view('Cost/add_process',$data);
		}
	}
	
	public function hapus(){
		$id 	= $this->uri->segment(3);
		$tanda 	= $this->uri->segment(4);
		$data_session			= $this->session->userdata;	
		// echo $id_mesin; exit;
		
		if($tanda == 'foh'){
			$table	= 'cost_foh';
			$alert	= 'FOH Cost';
		}
		elseif($tanda == 'process'){
			$table	= 'cost_process';
			$alert	= 'Process Cost';
		}
		
		$Arr_Update = array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
		);
		// echo "<pre>"; print_r($Arr_Update);
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update($table, $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete '.$alert.' data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete '.$alert.' data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete '.$alert.' with Kode/Id : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	public function edit(){
		$tanda 	= $this->uri->segment(3);
		$data					= $this->input->post();
		$data_session			= $this->session->userdata;	
		// echo $tanda; exit;
		$id				= $data['id'];
		$item_cost		= $data['item_cost'];
		$std_rate		= $data['std_rate'];
		$std_hitung		= $data['std_hitung'];
		
		if($tanda == 'foh'){
			$table	= 'cost_foh';
			$alert	= 'FOH Cost';
		}
		if($tanda == 'foh_tanki'){
			$table	= 'cost_foh';
			$alert	= 'FOH Cost Tanki';
		}
		if($tanda == 'process'){
			$table	= 'cost_process';
			$alert	= 'Process Cost';
		}
		if($tanda == 'process_tanki'){
			$table	= 'cost_process';
			$alert	= 'Process Cost Tanki';
		}
		 
		$Arr_Update = array(
			'item_cost' 	=> $item_cost,
			'std_rate' 		=> $std_rate,
			'std_hitung' 	=> $std_hitung,
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);
		// echo "<pre>"; print_r($Arr_Update);
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update($table, $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update '.$alert.' data failed. Please try again later ...',
				'status'	=> 0,
				'tanda'		=> $tanda
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update '.$alert.' data success. Thanks ...',
				'status'	=> 1,
				'tanda'		=> $tanda
			);				
			history('Update '.$alert.' with Kode/Id : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	public function update_cost(){
		$data_session = $this->session->userdata;

		$this->db->trans_start(); 
			$this->db->truncate('cost_process_auto');
			
			$sqlUpdate = "
				INSERT INTO cost_process_auto ( id, kode, standard_code, product_parent, diameter, diameter2, pn, liner, direct_labour, indirect_labour, machine, mould_mandrill, total, update_by, update_on ) SELECT
					a.id,
					a.kode,
					a.standard_code,
					a.product_parent,
					a.diameter,
					IFNULL(a.diameter2, 0) AS diameter2,
					a.pn,
					a.liner,
					a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '1' ) AS direct_labour,
					a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '2' ) AS indirect_labour,
					IFNULL((a.total_time * (SELECT x.machine_cost_per_hour FROM machine x WHERE x.no_mesin=a.id_mesin LIMIT 1)),0) AS machine,
					IFNULL((SELECT y.biaya_per_pcs FROM mould_mandrill y WHERE y.product_parent=a.product_parent AND y.diameter=a.diameter AND y.diameter2=IFNULL(a.diameter2, 0) LIMIT 1),0) AS mould_mandrill,
					((a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '1' ))+
					(a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '2' ))+
					(IFNULL((a.total_time * (SELECT x.machine_cost_per_hour FROM machine x WHERE x.no_mesin=a.id_mesin LIMIT 1)),0))+
					(IFNULL((SELECT y.biaya_per_pcs FROM mould_mandrill y WHERE y.product_parent=a.product_parent AND y.diameter=a.diameter AND y.diameter2=IFNULL(a.diameter2, 0) LIMIT 1),0))) AS total,
					'".$data_session['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
				FROM cycletime_default a";
			
			$this->db->query($sqlUpdate);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Process Failed Updated. Please try again ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Process Suucess Updated. Thanks ...',
				'status'	=> 1
			);				
			history('Update Cost Process by Costing'); 
		}
		echo json_encode($Arr_Data);
	}
	
	public function profit(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/profit';;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Rate Profit',
			'action'		=> 'profit',
			'listkomponen'	=> $getKomp,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Rate Profit');
		$this->load->view('Cost/profit',$data);
	}
	
	public function getDataJSON(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/profit';
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
			$nestedData[]	= "<div align='center'>".floatval($row['profit'])." %</div>";
			$nestedData[]	= "<div align='center'>".$updated_by."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($updated_date))."</div>";
			
			$update = "";
			$delete = "";
			if($Arr_Akses['update']=='1'){
				$update = "<a href='".site_url('cost/edit_profit/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}
			if($Arr_Akses['delete']=='1'){
				$delete = "&nbsp;<a id='deleteID' data-id='".$row['id']."' data-product='".$row['product_parent']."' data-d1='".$row['diameter']."' data-d2='".$row['diameter2']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
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
				cost_profit a,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_komponen."
				AND a.deleted ='N' 
				AND (
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
	
	public function add_profit(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;

			$product_parent	= $data['product_parent'];
			$standard_code		= $data['standard_code'];
			$diameter		= $data['diameter'];
			$diameter2		= (!empty($data['diameter2']))?$data['diameter2']:0;
			$profit			= $data['profit'];
			
			$sqlNum		= "SELECT * FROM cost_profit WHERE product_parent='".$product_parent."' AND diameter='".$diameter."' AND diameter2='".$diameter2."' ";
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
					$this->db->insert('cost_profit', $data);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'			=> 'Failed Add Rate Profit. Please try again later ...',
						'status'		=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'			=> 'Success Add Rate Profit. Thanks ...',
						'status'		=> 1
						
					);
					history('Add Rate Profit'); 
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
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/profit';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			
			$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
			
			$data = array(
				'title'			=> 'Add Rate Profit',
				'action'		=> 'add_profit',
				'listkomponen'	=> $getKomp
			);
			$this->load->view('Cost/add_profit',$data);
		}
	}
	
	public function hapus_profit(){
		$id 	= $this->uri->segment(3);
		$parent = $this->uri->segment(4);
		$d1 	= $this->uri->segment(5);
		$d2 	= $this->uri->segment(6);
		
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->delete('cost_profit');
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
			history('Delete rate profit with Kode/Id : '.$id.' | '.$parent.' | '.$d1.' | '.$d2);
		}
		echo json_encode($Arr_Data);
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
			
			$sqlNum		= "SELECT * FROM cost_profit WHERE product_parent='".$product_parent."' AND diameter='".$diameter."' AND diameter2='".$diameter2."' ";
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
					$this->db->update('cost_profit', $data);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'			=> 'Failed Edit Rate Profit. Please try again later ...',
						'status'		=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'			=> 'Success Edit Rate Profit. Thanks ...',
						'status'		=> 1
						
					);
					history("Edit Rate Profit ".$product_parent." | ".$diameter." | ".$diameter2); 
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
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/profit';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$id = $this->uri->segment(3);
			$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
			$getData		= $this->db->query("SELECT * FROM cost_profit WHERE id='".$id."' ORDER BY product_parent ASC")->result_array();
			
			$data = array(
				'title'			=> 'Edit Rate Profit',
				'action'		=> 'add_profit',
				'listkomponen'	=> $getKomp,
				'data'			=> $getData
			);
			$this->load->view('Cost/add_profit',$data);
		}
	}

	public function material_planning(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of View Material',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Material Planning in Costing');
		$this->load->view('Cost/material_planning',$data);
	}

	public function getDataJSONMatPlan(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONMatPlan(
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
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['no_ipp'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$class = Color_status($row['status']);
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['status']."</span></div>";
			$nestedData[]	= "<div align='center'>
                                    &nbsp;<button class='btn btn-sm btn-primary detailMat' title='Total Material' data-id_bq='".$row['no_ipp']."'><i class='fa fa-eye'></i></button>
									&nbsp;<a href=".site_url('cost/excel_material/BQ-'.$row['no_ipp'])." target='_blank' class='btn btn-sm btn-success'><i class='fa fa-file-excel-o'></i></a>
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

	public function queryDataJSONMatPlan($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				production a 
			WHERE status != 'CANCELED'
				AND status != 'WAITING STRUCTURE BQ'
				AND status != 'WAITING APPROVE STRUCTURE BQ'
				AND status != 'WAITING ESTIMATION PROJECT'
				AND status != 'WAITING APPROVE EST PROJECT'
			AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer',
			3 => 'project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalTotalMat(){
		$id_bq = $this->uri->segment(3);

		// $query 	= "SELECT * FROM estimasi_total_material WHERE id_bq='BQ-".$id_bq."' AND id_material <> 'MTL-1903000' ORDER BY nm_material ASC ";
		// $result		= $this->db->query($query)->result_array();
		
		$result		= $this->db->order_by('nm_material','ASC')->get_where('estimasi_total_material',array('id_bq'=>'BQ-'.$id_bq, 'id_material <>'=>'MTL-1903000'))->result_array();

		$data = array(
			'detail'		=> $result
		);
		history('View Detail Material Planning in Costing '.$id_bq);
		$this->load->view('Cost/modalTotalMat', $data);
	}

	public function excel_material(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq = $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(5);
		$sheet->setCellValue('A'.$Row, 'VIEW MATERIAL '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Material Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
        $sheet->getColumnDimension('B')->setWidth(70);
        
        $sheet->setCellValue('C'.$NewRow, 'Est Mat');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(16);
		
		$sheet->setCellValue('D'.$NewRow, 'Price /kg');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Total Price');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
        $sheet->getColumnDimension('E')->setWidth(16);
		
		$qSupplier 		= "SELECT * FROM estimasi_total_material WHERE id_bq='".$id_bq."' ORDER BY nm_material ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
                
                $awal_col++;
				$est_harga	= $row_Cek['last_cost_qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga	= $row_Cek['cost_est'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga	= $row_Cek['last_cost_qty'] * $row_Cek['cost_est'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
			}
		}
		
		
		$sheet->setTitle('View Material');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="View Material '.$id_bq.''.'_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	//CONSUMABLE
	//==========================================================================================================================
	//================================================CONSUMABLE================================================================
	//==========================================================================================================================

	public function consumable(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/consumable';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');

		$data = array(
			'title'			=> 'Indeks Of Rutin Price Reference',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Rutin Price Reference');
		$this->load->view('Cost/consumable',$data);
	}

	public function getDataJSONConsumable(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/consumable';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONConsumable(
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
		$GET_COSTBOOK = get_costbook();
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

	 		$COSTBOOK = (!empty($GET_COSTBOOK[$row['code_group']]))?$GET_COSTBOOK[$row['code_group']]:0;
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['code_group']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material_name']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['spec']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['brand']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['unit_material']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kurs']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['rate'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($COSTBOOK,2)."</div>";
					$update	= "";
					$delete	= "";
					$history	= "";
					if($Arr_Akses['update']=='1'){
						$update	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/add_consumable/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($Arr_Akses['delete']=='1'){
						// $delete	= "<button class='btn btn-sm btn-danger deleted' title='Permanent Delete' data-id='".$row['id']."'><i class='fa fa-trash'></i></button>";
					}
					if($Arr_Akses['update']=='1'){
						$history= "<button class='btn btn-sm btn-default history' title='History Costbook' data-id='".$row['code_group']."'><i class='fa fa-history'></i></button>";
					}
			$nestedData[]	= "<div align='center'>
									".$update."
									".$delete."
									".$history."
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

	public function queryDataJSONConsumable($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.id,
				a.code_group,
				b.material_name,
				b.spec,
				b.brand,
				a.unit_material,
				a.kurs,
				a.rate
			FROM
				price_ref a LEFT JOIN con_nonmat_new b
					ON a.code_group=b.code_group
		   WHERE 1=1 AND a.category = 'consumable' AND a.sts_price='N' AND a.deleted='N' AND b.status='1' AND (
				b.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.brand LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.unit_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kurs LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.rate LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'code_group',
			2 => 'material_name',
			3 => 'spec',
			4 => 'brand',
			5 => 'unit_material',
			6 => 'kurs',
			7 => 'rate'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function add_consumable(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;
			$id			= strtolower($data['id']);
			$tanda_edit		= strtolower($data['tanda_edit']);
			$code_group		= $data['code_group'];
			$kurs		= $data['kurs'];
			$unit_material= strtolower(trim($data['unit_material']));
			$rate		= str_replace(',', '', $data['rate']);

			$ArrHeader = array(
				'category' => 'consumable',
				'code_group' => $code_group,
				'id_unit' => $unit_material,
				'unit_material' => get_name('raw_pieces','kode_satuan','id_satuan',$unit_material),
				'kurs' => $kurs,
				'region' => 'all region',
				'rate' => $rate,
				'updated_by' => $data_session['ORI_User']['username'],
				'updated_date' => $dateTime
			);

			$ArrUpdate = array(
				'sts_price' => 'Y',
			);

			if(empty($tanda_edit)){
				$Hist = "Add ";
			}

			if(!empty($tanda_edit)){
				$Hist = "Update ";

				$qHist		= "SELECT * FROM price_ref WHERE code_group='".$code_group."' AND id_unit='".$unit_material."'";
				$restHist	= $this->db->query($qHist)->result();

				$ArrHist = array(
					'category' 			=> $restHist[0]->category,
					'code_group' 		=> $restHist[0]->code_group,
					'id_unit' 			=> $restHist[0]->id_unit,
					'unit_material' 	=> $restHist[0]->unit_material,
					'kurs'					=> $restHist[0]->kurs,
					'region' 				=> $restHist[0]->region,
					'rate' 					=> $restHist[0]->rate,
					'sts_price' 		=> $restHist[0]->sts_price,
					'updated_by' 		=> $restHist[0]->updated_by,
					'updated_date' 	=> $restHist[0]->updated_date,
					'hist_by' 			=> $data_session['ORI_User']['username'],
					'hist_date' 		=> date('Y-m-d H:i:s')
				);
			}

			$this->db->trans_start();
				if(empty($tanda_edit)){
					$this->db->insert('price_ref', $ArrHeader);

					$this->db->where('code_group', $code_group);
					$this->db->where('unit_material', $unit_material);
					$this->db->update('con_nonmat_new_konversi', $ArrUpdate);
				}

				if(!empty($tanda_edit)){
					$this->db->insert('hist_price_ref', $ArrHist);

					$this->db->where('id', $id);
					$this->db->update('price_ref', $ArrHeader);

					$this->db->where('code_group', $code_group);
					$this->db->where('unit_material', $unit_material);
					$this->db->update('con_nonmat_new_konversi', $ArrUpdate);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.'data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.'data success. Thanks ...',
					'status'	=> 1
				);
				history($Hist.'Rutin '.$code_group.', id = '.$id);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/consumable';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$id = $this->uri->segment(3);

			$tanda1 = 'Add';
			if(!empty($id)){
				$tanda1 = 'Edit';
			}

			$qConsumable 		= "	SELECT
										b.code_group,
										c.category,
										b.material_name,
										b.spec,
										d.id 
									FROM
										con_nonmat_new b
										LEFT JOIN con_nonmat_category_awal c ON b.category_awal = c.id
										LEFT JOIN price_ref d ON b.code_group = d.code_group 
									WHERE
										b.deleted = 'N' 
										AND ( d.id IS NULL OR d.deleted = 'Y' ) 
									GROUP BY
										b.code_group 
									ORDER BY
										c.category ASC,
										b.material_name ASC";
			$restConsumable = $this->db->query($qConsumable)->result_array();

			$qCurrency		= "SELECT * FROM currency ORDER BY mata_uang ASC, negara ASC";
			$restCurrency = $this->db->query($qCurrency)->result_array();

			$qHeader 		= "SELECT a.*, b.spec, b.material_name, b.brand, b.category AS cty FROM price_ref a LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group WHERE a.id='".$id."'";
			$restHeader = $this->db->query($qHeader)->result();

			$restUnit = array();
			if(!empty($restHeader)){
				$qUnit		= "SELECT * FROM con_nonmat_new WHERE code_group = '".$restHeader[0]->code_group."' ";
				$restUnit = $this->db->query($qUnit)->result_array();
			}
			$data = array(
				'title'			=> $tanda1.' Price Reference',
				'action'		=> 'add',
				'consumable'=> $restConsumable,
				'currency'	=> $restCurrency,
				'header'		=> $restHeader,
				'unit'		=> $restUnit
			);
			$this->load->view('Cost/add_consumable',$data);
		}
	}

	public function hapus_consumable(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$qGet = "SELECT unit_material, code_group FROM price_ref WHERE id='".$id."' LIMIT 1";
		$restGet = $this->db->query($qGet)->result();

		$ArrUpdate = array(
			'deleted' => 'Y',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => $dateTime
		);

		$ArrUpdate2 = array(
			'sts_price' => 'N',
			'updated_by' => $data_session['ORI_User']['username'],
			'updated_date' => $dateTime
		);

		$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('price_ref', $ArrUpdate);

				$this->db->where('code_group', $restGet[0]->code_group);
				$this->db->where('unit_material', $restGet[0]->unit_material);
				$this->db->update('con_nonmat_new_konversi', $ArrUpdate2);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Delete data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Rutin Price Reference '.$restGet[0]->code_group.' / '.$restGet[0]->unit_material.', id = '.$id);
		}

		echo json_encode($Arr_Kembali);


	}

	public function ExcelMasterDownload(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'MASTER CONSUMABLE NON MATERIAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'Category');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'Spesification');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'Java');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'Sumatra');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'Kalimantan');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'Sulawesi');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'East Indonesia');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$qManPower	= "SELECT * FROM view_con_nonmat";
		$row		= $this->db->query($qManPower)->result_array();

		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$category	= $row_Cek['category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$spec	= $row_Cek['spec'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$rate	= $row_Cek['jawa'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $rate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$rate	= $row_Cek['sumatra'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $rate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$rate	= $row_Cek['kalimantan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $rate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$rate	= $row_Cek['sulawesi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $rate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$rate	= $row_Cek['indonesia_timur'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $rate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);


			}
		}

		$sheet->setTitle('Consumable Non Material Master');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="master_consumable_non_mataterial_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function get_detailxxx(){
		$code_group = $this->uri->segment(3);
		$code = substr($code_group, 0,2);
		// echo $code;

		if($code == 'CN'){
			$table = "con_nonmat_new";

			$qList	= "SELECT unit_material FROM con_nonmat_new_konversi WHERE code_group='".$code_group."' AND sts_price='N' AND deleted='N' GROUP BY unit_material ORDER BY unit_material ASC ";
			$list		= $this->db->query($qList)->result();

			$option 	= "";
			foreach($list as $row)	{
					$option .= "<option value='".$row->unit_material."'>".strtoupper($row->unit_material)."</option>";
			}
		}

		// exit;
		$query	= "SELECT * FROM $table WHERE code_group='".$code_group."' LIMIT 1";
		$data		= $this->db->query($query)->result();

		if($code == 'MP'){
			$brand = (!empty($data[0]->note))?strtoupper($data[0]->note):'';
		}
		else{
			$brand = (!empty($data[0]->brand))?strtoupper($data[0]->brand):'';
		}


		echo json_encode(array(
			'spec' => strtoupper($data[0]->spec),
			'brand' => $brand,
			'option' => $option
		));
	}
	
	public function get_detail(){
		$code_group = $this->uri->segment(3);
		$code = substr($code_group, 0,2);
		// echo $code;

		if($code == 'CN'){
			$table = "con_nonmat_new";

			$qList	= "SELECT satuan FROM con_nonmat_new WHERE code_group='".$code_group."' LIMIT 1 ";
			$list		= $this->db->query($qList)->result();

			$option 	= "";
			foreach($list as $row)	{
					$option .= "<option value='".$row->satuan."'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$row->satuan))."</option>";
			}
		}

		// exit;
		$query	= "SELECT * FROM $table WHERE code_group='".$code_group."' LIMIT 1";
		$data		= $this->db->query($query)->result();

		if($code == 'MP'){
			$brand = (!empty($data[0]->note))?strtoupper($data[0]->note):'';
		}
		else{
			$brand = (!empty($data[0]->brand))?strtoupper($data[0]->brand):'';
		}


		echo json_encode(array(
			'spec' => strtoupper($data[0]->spec),
			'brand' => $brand,
			'option' => $option
		));
	}
	
	//================================================
	//==========PRICE REFERENCE MATERIAL==============
	//================================================
	
	public function material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$id_uri = $this->uri->segment(3);
		$type = $this->uri->segment(4);
		$where = "";
		if($id_uri == 'approve'){
			$where = " AND app_price_sup = 'Y' ";
		}
		
		$get_Data			= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' ".$where." ORDER BY nm_material ASC")->result();
		$menu_akses			= $this->master_model->getMenu();

		$data_session	= $this->session->userdata;
		$get_tab = $this->db->select('keterangan')->order_by('id', 'DESC')->limit('1')->get_where('laporan_status', array('insert_by'=>$data_session['ORI_User']['username'],'category'=>'tab price ref'))->result();
        $value1_head = (!empty($get_tab))?$get_tab[0]->keterangan:'material';
		if(!empty($type)){
			$value1_head = $type;
		}
		// echo $value1_head;
		$data_acc = $this->db
						->select('a.*, b.category AS category_acc')
						->from('accessories a')
						->join('accessories_category b', 'a.category=b.id', 'left')
						->where("deleted = 'N' ".$where)
						->get()
						->result();
		
		$get_tab2 = $this->db->select('keterangan')->order_by('id', 'DESC')->limit('1')->get_where('laporan_status', array('insert_by'=>$data_session['ORI_User']['username'],'category'=>'tab accessories'))->result();
		$value1 = (!empty($get_tab2))?$get_tab2[0]->keterangan:'bolt nut';
		
		$name_baut 		= $this->db->group_by('nama')->get_where('accessories', array('category'=>'1','deleted'=>'N'))->result_array();
		$brand_baut 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'1','deleted'=>'N'))->result_array();
		
		$name_plate 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'2','deleted'=>'N'))->result_array();
		$brand_plate 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'2','deleted'=>'N'))->result_array();
		
		$name_gasket 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'3','deleted'=>'N'))->result_array();
		$brand_gasket 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'3','deleted'=>'N'))->result_array();
		
		$name_lainnya 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'4','deleted'=>'N'))->result_array();
		$brand_lainnya 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'4','deleted'=>'N'))->result_array();
		
		$transport_export = $this->db
									->select('a.*, b.country_name')
									->from('cost_export_trans a')
									->join('country b','a.country_code=b.country_code','left')
									->where("a.deleted = 'N' ".$where)
									->get()
									->result_array();
		$category = $this->db->select('category')->group_by('category')->order_by('category','asc')->get('cost_trucking')->result_array();
		$area = $this->db->select('area')->group_by('area')->order_by('area','asc')->get('cost_trucking')->result_array();
		$dest = $this->db->select('tujuan')->group_by('tujuan')->order_by('tujuan','asc')->get('cost_trucking')->result_array();
		$truck = $this->db->select('id, nama_truck')->order_by('nama_truck','asc')->get('truck')->result_array();

		$rutin = $this->db
					->select('	a.id,
								a.code_group,
								b.material_name,
								b.spec,
								b.brand,
								a.unit_material,
								a.kurs,
								a.expired_supplier,
								a.expired_purchase,
								a.expired,
								a.app_price_sup,
								a.price_supplier,
								a.price_purchase,
								a.rate,
								a.reject_ket,
								a.rate')
					->from('price_ref a')
					->join('con_nonmat_new b','a.code_group=b.code_group','left')
					->where('a.category','consumable')
					->where('a.sts_price','N')
					->where("a.deleted = 'N' ".$where)
					->get()
					->result_array();
		$SQL_COST_BOOK = "	SELECT a.id_material, a.price_book
							FROM price_book a 
							LEFT JOIN price_book b ON (a.id_material = b.id_material AND a.id < b.id)
							WHERE b.id IS NULL";
		$REST_COST_BOOK = $this->db->query($SQL_COST_BOOK)->result_array();
		$GetCostBook = [];
		foreach ($REST_COST_BOOK as $key => $value) {
			$GetCostBook[$value['id_material']] = $value['price_book'];
		}
		// print_r($GetCostBook);
		// echo $GetCostBook['MTL-1903003'];
		// exit;

		$data = array(
			'title'			=> 'Indeks Of Price Reference Material',
			'action'		=> 'material', 
			'GetCostBook'	=> $GetCostBook,
			'row'			=> $get_Data,
			'value1_head'	=> $value1_head,
			'data_acc'		=> $data_acc,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'value1'		=> $value1,
			'name_baut'		=> $name_baut,
			'brand_baut'		=> $brand_baut,
			'name_plate'		=> $name_plate,
			'brand_plate'		=> $brand_plate,
			'name_gasket'		=> $name_gasket,
			'brand_gasket'		=> $brand_gasket,
			'name_lainnya'		=> $name_lainnya,
			'brand_lainnya'		=> $brand_lainnya,
			'id_uri' 			=> $id_uri,
			'transport_export'	=> $transport_export,
			'category'			=> $category,
			'area'				=> $area,
			'dest'				=> $dest,
			'truck'				=> $truck,
			'rutin'				=> $rutin
		);
		history('View Data Price Reference Material');
		$this->load->view('Cost/material',$data);
	}
	
	public function edit_material(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id_material']);
			$price_ref_estimation	= $data['price_ref_estimation'];
			$price_ref_purchase		= $data['price_ref_purchase'];
			$price_from_supplier	= $data['price_from_supplier'];
			
			$exp_price_ref_est		= $data['exp_price_ref_est'];
			$exp_price_ref_pur		= $data['exp_price_ref_pur'];
			$exp_price_ref_sup		= $data['exp_price_ref_sup'];
			$ket_price				= strtolower($data['ket_price']);
			
			$Arr_Update	= array(
				'price_ref_estimation' 	=> str_replace(',', '', $price_ref_estimation),
				'price_ref_purchase' 	=> str_replace(',', '', $price_from_supplier),
				'exp_price_ref_est' 	=> $exp_price_ref_est,
				'exp_price_ref_pur' 	=> $exp_price_ref_sup,
				'exp_price_ref_sup' 	=> $exp_price_ref_sup,
				'ket_price' 			=> $ket_price,
				'reject_reason' 		=> NULL,
				'app_price_sup' 		=> 'N',
				'modified_by' 			=> $data_session['ORI_User']['username'],
				'modified_date' 		=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
				$this->db->query("INSERT hist_raw_materials (
										id_material,idmaterial,nm_material,nm_dagang,nm_international,id_category,
										nm_category,id_satuan,cost_satuan,satuan_kg,saldo_kg,nilai_konversi,price_ref_estimation,
										price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,exp_price_ref_sup,flag_active,descr,modified_by,modified_date 
									) 
									SELECT
										id_material,idmaterial,nm_material,nm_dagang,nm_international,id_category,
										nm_category,id_satuan,cost_satuan,satuan_kg,saldo_kg,nilai_konversi,price_ref_estimation,
										price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,exp_price_ref_sup,flag_active,descr,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										raw_materials 
									WHERE
										id_material = '".$id_material."'");
									
				$this->db->where('id_material', $id_material);
				$this->db->update('raw_materials', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Price Reference Material NEW (APPROVAL) '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$id = $this->uri->segment(3);
			
			$detail			= $this->db->query("SELECT * FROM raw_materials WHERE id_material = '".$id."' ")->result_array();
			
			$data = array(
				'title'			=> 'Edit Price Reference Material',
				'action'		=> 'edit_material',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_material',$data);
		}
	}

	public function reject_material(){
		$data			= $this->input->post();
		$Arr_Kembali	= array();
		$data_session	= $this->session->userdata;
		
		$id_material			= strtoupper($data['id_material']);
		$reject_reason			= strtolower($data['reject_reason']);
		
		$Arr_Update	= array(
			'reject_reason' 		=> $reject_reason,
			'app_price_sup' 		=> 'N',
			'modified_by' 			=> $data_session['ORI_User']['username'],
			'modified_date' 		=> date('Y-m-d H:i:s')
		);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id_material', $id_material);
			$this->db->update('raw_materials', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{ 
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1
			);
			history('Reject Price Reference Material NEW (APPROVAL) '.$id_material);
		}
		// print_r($Arr_Data); exit; 
		echo json_encode($Arr_Data);
	}
	
	//================================================
	//==========PRICE FROM SUPPLIER==============
	//================================================
	
	public function supplier(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_session	= $this->session->userdata;
		$get_tab = $this->db->select('keterangan')->order_by('id', 'DESC')->limit('1')->get_where('laporan_status', array('insert_by'=>$data_session['ORI_User']['username'],'category'=>'tab price ref'))->result();
        $value1_head = (!empty($get_tab))?$get_tab[0]->keterangan:'material';
		
		// $get_Data			= $this->master_model->getData('raw_materials');
		$get_Data			= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' ORDER BY nm_material ASC")->result();
		$menu_akses			= $this->master_model->getMenu();

		$data_acc = $this->db
						->select('a.*, b.category AS category_acc')
						->from('accessories a')
						->join('accessories_category b', 'a.category=b.id', 'left')
						->where('deleted','N')
						->get()
						->result();
		
		$get_tab2 = $this->db->select('keterangan')->order_by('id', 'DESC')->limit('1')->get_where('laporan_status', array('insert_by'=>$data_session['ORI_User']['username'],'category'=>'tab accessories'))->result();
		$value1 = (!empty($get_tab2))?$get_tab2[0]->keterangan:'bolt nut';
		
		$name_baut 		= $this->db->group_by('nama')->get_where('accessories', array('category'=>'1','deleted'=>'N'))->result_array();
		$brand_baut 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'1','deleted'=>'N'))->result_array();
		
		$name_plate 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'2','deleted'=>'N'))->result_array();
		$brand_plate 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'2','deleted'=>'N'))->result_array();
		
		$name_gasket 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'3','deleted'=>'N'))->result_array();
		$brand_gasket 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'3','deleted'=>'N'))->result_array();
		
		$name_lainnya 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'4','deleted'=>'N'))->result_array();
		$brand_lainnya 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'4','deleted'=>'N'))->result_array();

		$name_tanki 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'5','deleted'=>'N'))->result_array();
		$brand_tanki 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'5','deleted'=>'N'))->result_array();
	
		$transport_export = $this->db
									->select('a.*, b.country_name')
									->from('cost_export_trans a')
									->join('country b','a.country_code=b.country_code','left')
									->where('a.deleted','N')
									->get()
									->result_array();
		$category = $this->db->select('category')->group_by('category')->order_by('category','asc')->get('cost_trucking')->result_array();
		$area = $this->db->select('area')->group_by('area')->order_by('area','asc')->get('cost_trucking')->result_array();
		$dest = $this->db->select('tujuan')->group_by('tujuan')->order_by('tujuan','asc')->get('cost_trucking')->result_array();
		$truck = $this->db->select('id, nama_truck')->order_by('nama_truck','asc')->get('truck')->result_array();

		$rutin = $this->db
					->select('	a.id,
								a.code_group,
								b.material_name,
								b.spec,
								b.brand,
								a.unit_material,
								a.kurs,
								a.expired_supplier,
								a.app_price_sup,
								a.price_supplier,
								a.reject_ket,
								a.reject_reason,
								a.rate')
					->from('price_ref a')
					->join('con_nonmat_new b','a.code_group=b.code_group','left')
					->where('a.category','consumable')
					->where('a.sts_price','N')
					->where('a.deleted','N')
					->get()
					->result_array();

		$data = array(
			'title'			=> 'Indeks Of Price From Supplier',
			'action'		=> 'material', 
			'value1_head' 		=> $value1_head,
			'row'			=> $get_Data,
			'data_acc'		=> $data_acc,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'value1'		=> $value1,
			'name_baut'		=> $name_baut,
			'brand_baut'		=> $brand_baut,
			'name_plate'		=> $name_plate,
			'brand_plate'		=> $brand_plate,
			'name_gasket'		=> $name_gasket,
			'brand_gasket'		=> $brand_gasket,
			'name_lainnya'		=> $name_lainnya,
			'brand_lainnya'		=> $brand_lainnya,
			'name_tanki'		=> $name_tanki,
			'brand_tanki'		=> $brand_tanki,
			'transport_export'	=> $transport_export,
			'category'			=> $category,
			'area'				=> $area,
			'dest'				=> $dest,
			'truck'				=> $truck,
			'rutin'				=> $rutin
		);
		history('View Data Price From Supplier');
		$this->load->view('Cost/supplier',$data);
	}
	
	public function edit_supplier(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id_material']);
			$price_from_supplier	= $data['price_from_supplier'];
			$exp_price_ref_sup		= $data['exp_price_ref_sup'];
			$ket_price_sup				= strtolower($data['ket_price_sup']);
			
			$Arr_Update	= array(
				'price_from_supplier' 	=> str_replace(',', '', $price_from_supplier),
				'exp_price_ref_sup' 	=> $exp_price_ref_sup,
				'ket_price_sup' 		=> $ket_price_sup,
				'app_price_sup' 		=> 'Y',
				'modified_by' 			=> $data_session['ORI_User']['username'],
				'modified_date' 		=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
				$this->db->query("INSERT hist_raw_materials (
										id_material,idmaterial,nm_material,nm_dagang,nm_international,id_category,
										nm_category,id_satuan,cost_satuan,satuan_kg,saldo_kg,nilai_konversi,price_ref_estimation,
										price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,exp_price_ref_sup,flag_active,descr,modified_by,modified_date 
									) 
									SELECT
										id_material,idmaterial,nm_material,nm_dagang,nm_international,id_category,
										nm_category,id_satuan,cost_satuan,satuan_kg,saldo_kg,nilai_konversi,price_ref_estimation,
										price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,exp_price_ref_sup,flag_active,descr,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										raw_materials 
									WHERE
										id_material = '".$id_material."'");
									
				$this->db->where('id_material', $id_material);
				$this->db->update('raw_materials', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Price From Supplier '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM raw_materials WHERE id_material = '".$id."' ")->result_array();
			$last_hist		= $this->db->query("SELECT * FROM hist_raw_materials WHERE id_material = '".$id."' ORDER BY id DESC LIMIT 1 ")->result();
			$data_session	= $this->session->userdata;
			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'material'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Edit Price From Supplier',
				'action'		=> 'edit_supplier',
				'row'			=> $detail,
				'last_price'	=> (!empty($last_hist))?$last_hist[0]->price_from_supplier:0
			);
			
			$this->load->view('Cost/edit_supplier',$data);
		}
	}

	//================================================
	//==========PRICE REFERENCE Accessories===========
	//================================================
	
	public function edit_accessories(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_ref_estimation	= $data['price_ref_estimation'];
			$price_ref_purchase		= $data['price_ref_purchase'];
			$price_from_supplier	= $data['price_from_supplier'];
			
			$exp_price_ref_est		= $data['exp_price_ref_est'];
			$exp_price_ref_pur		= $data['exp_price_ref_pur'];
			$exp_price_ref_sup		= $data['exp_price_ref_sup'];
			$ket_price				= strtolower($data['ket_price']);
			
			$Arr_Update	= array(
				'harga' 				=> str_replace(',', '', $price_ref_estimation),
				'price_ref_purchase' 	=> str_replace(',', '', $price_from_supplier),
				'exp_price_ref_est' 	=> $exp_price_ref_est,
				'exp_price_ref_pur' 	=> $exp_price_ref_sup,
				'note' 					=> $ket_price,
				'reject_reason' 		=> NULL,
				'app_price_sup' 		=> 'N',
				'updated_by' 			=> $data_session['ORI_User']['username'],
				'updated_date' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
			$this->db->query("INSERT hist_accessories (
									id,category,nama,diameter,panjang,thickness,profit,note,note_sup,created_by,created_date,updated_by,deleted,deleted_by,
									radius,density,dimensi,spesifikasi,material,satuan,standart,exp_price_ref_sup,app_price_sup,updated_date,deleted_date,
									ukuran_standart,keterangan,harga,price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,hist_by,hist_date 
								) 
								SELECT
									id,category,nama,diameter,panjang,thickness,profit,note,note_sup,created_by,created_date,updated_by,deleted,deleted_by,
									radius,density,dimensi,spesifikasi,material,satuan,standart,exp_price_ref_sup,app_price_sup,updated_date,deleted_date,
									ukuran_standart,keterangan,harga,price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
								FROM
									accessories 
								WHERE
									id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('accessories', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price reference accessories new (APPROVAL) '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM accessories WHERE id = '".$id."' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'accessories'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Edit Price Reference Material',
				'action'		=> 'edit_accessories',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_material_acc',$data);
		}
	}

	public function reject_accessories(){
		$data			= $this->input->post();
		$Arr_Kembali	= array();
		$data_session	= $this->session->userdata;
		
		$id_material			= strtoupper($data['id']);
		$reject_reason			= strtolower($data['reject_reason']);
		
		$Arr_Update	= array(
			'reject_reason' 	=> $reject_reason,
			'app_price_sup' 	=> 'N',
			'updated_by' 		=> $data_session['ORI_User']['username'],
			'updated_date' 		=> date('Y-m-d H:i:s')
		);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id', $id_material);
			$this->db->update('accessories', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{ 
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1
			);
			history('Reject price reference accessories new (APPROVAL) '.$id_material);
		}
		echo json_encode($Arr_Data);
	}
	
	//================================================
	//==========PRICE FROM SUPPLIER Accessories=======
	//================================================
	
	public function edit_supplier_acc(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_from_supplier	= $data['price_from_supplier'];
			$exp_price_ref_sup		= $data['exp_price_ref_sup'];
			$note_sup				= strtolower($data['note_sup']);
			
			$Arr_Update	= array(
				'price_from_supplier' 	=> str_replace(',', '', $price_from_supplier),
				'exp_price_ref_sup' 	=> $exp_price_ref_sup,
				'note_sup' 				=> $note_sup,
				'app_price_sup' 		=> 'Y',
				'updated_by' 			=> $data_session['ORI_User']['username'],
				'updated_date' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
				$this->db->query("INSERT hist_accessories (
										id,category,nama,diameter,panjang,thickness,profit,note,note_sup,created_by,created_date,updated_by,deleted,deleted_by,
										radius,density,dimensi,spesifikasi,material,satuan,standart,exp_price_ref_sup,app_price_sup,updated_date,deleted_date,
										ukuran_standart,keterangan,harga,price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,hist_by,hist_date 
									) 
									SELECT
										id,category,nama,diameter,panjang,thickness,profit,note,note_sup,created_by,created_date,updated_by,deleted,deleted_by,
										radius,density,dimensi,spesifikasi,material,satuan,standart,exp_price_ref_sup,app_price_sup,updated_date,deleted_date,
										ukuran_standart,keterangan,harga,price_ref_purchase,price_from_supplier,exp_price_ref_est,exp_price_ref_pur,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										accessories 
									WHERE
										id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('accessories', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price from supplier accessories '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM accessories WHERE id = '".$id."' ")->result_array();
			$satuan			= $this->db->query("SELECT * FROM raw_pieces WHERE `delete` = 'N' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'accessories'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Edit Price From Supplier',
				'action'		=> 'edit_acc',
				'row'			=> $detail,
				'satuan'			=> $satuan
			);
			
			$this->load->view('Cost/edit_supplier_acc',$data);
		}
	}

	public function tab_last(){
        $value1 = $this->input->post('value1');
        $data_session = $this->session->userdata;
        
        $arr_last = array(
			'date' => date('Y-m-d'),
			'category' => 'tab accessories',
			'status' => 'SUCCESS',
			'insert_by' => $data_session['ORI_User']['username'],
			'insert_date' => date('Y-m-d H:i:s'),
			'keterangan' => $value1
		);
		
		$this->db->trans_start();
            $this->db->insert('laporan_status', $arr_last);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);				
			// history('Tab last accessories : '.$value1);
		}
		echo json_encode($Arr_Data);
    }

	public function tab_last_header(){
        $value1 = $this->input->post('value1');
        $data_session = $this->session->userdata;
        
        $arr_last = array(
			'date' => date('Y-m-d'),
			'category' => 'tab price ref',
			'status' => 'SUCCESS',
			'insert_by' => $data_session['ORI_User']['username'],
			'insert_date' => date('Y-m-d H:i:s'),
			'keterangan' => $value1
		);
		
		$this->db->trans_start();
            $this->db->insert('laporan_status', $arr_last);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);				
			// history('Tab last accessories : '.$value1);
		}
		echo json_encode($Arr_Data);
    }
	//Bolt & Nut
	public function data_side_bold_nut(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_bold_nut(
			$requestData['nama'],
			$requestData['brand'],
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['id']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['panjang'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['radius'],2)."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_sup'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$waiting_app = '';
			if($row['app_price_sup'] == 'Y'){
				$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
			}
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
			}

			if(empty($date_exp)){
				$status2="Not Set";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}
			
			$PRE = (!empty($row['price_from_supplier']))?$row['price_from_supplier']:0;
			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reject_reason']))."</div>";

				$edit	= '';
				if($Arr_Akses['update']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_supplier_acc/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_bold_nut($nama, $brand, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '1' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'diameter',
			5 => 'panjang',
			6 => 'standart',
			7 => 'radius',
			8 => 'kode_satuan',
			9 => 'keterangan',
			10 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	//Plate
	public function data_side_plate(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_plate(
			$requestData['nama'],
			$requestData['brand'],
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['id']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['thickness'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['density'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_sup'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$waiting_app = '';
			if($row['app_price_sup'] == 'Y'){
				$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
			}
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
			}

			if(empty($date_exp)){
				$status2="Not Set";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}
			
			$PRE = (!empty($row['price_from_supplier']))?$row['price_from_supplier']:0;
			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reject_reason']))."</div>";
				$edit	= '';
				if($Arr_Akses['update']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_supplier_acc/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_plate($nama, $brand, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '2' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'thickness',
			5 => 'density',
			6 => 'ukuran_standart',
			7 => 'standart',
			8 => 'kode_satuan',
			9 => 'keterangan',
			10 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	//Gasket
	public function data_side_gasket(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_gasket(
			$requestData['nama'],
			$requestData['brand'],
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['id']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']." ".$row['dimensi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['thickness'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_sup'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$waiting_app = '';
			if($row['app_price_sup'] == 'Y'){
				$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
			}
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
			}

			if(empty($date_exp)){
				$status2="Not Set";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}
			
			$PRE = (!empty($row['price_from_supplier']))?$row['price_from_supplier']:0;
			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reject_reason']))."</div>";
				$edit	= '';
				if($Arr_Akses['update']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_supplier_acc/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_gasket($nama, $brand, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '3' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'thickness',
			5 => 'ukuran_standart',
			6 => 'standart',
			7 => 'kode_satuan',
			8 => 'keterangan',
			9 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	//Lainnya
	public function data_side_lainnya(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_lainnya(
			$requestData['nama'],
			$requestData['brand'],
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['id']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['dimensi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['spesifikasi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_sup'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$waiting_app = '';
			if($row['app_price_sup'] == 'Y'){
				$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
			}
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
			}

			if(empty($date_exp)){
				$status2="Not Set";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}
			
			$PRE = (!empty($row['price_from_supplier']))?$row['price_from_supplier']:0;
			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reject_reason']))."</div>";
				$edit	= '';
				if($Arr_Akses['update']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_supplier_acc/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_lainnya($nama, $brand, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '4' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'dimensi',
			5 => 'spesifikasi',
			6 => 'ukuran_standart',
			7 => 'standart',
			8 => 'kode_satuan',
			9 => 'keterangan',
			10 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//Lainnya
	public function data_side_tanki(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_tanki(
			$requestData['nama'],
			$requestData['brand'],
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['id']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['dimensi'].' '.$row['spesifikasi']))."</div>";
			// $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['spesifikasi']))."</div>";
			// $nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_sup'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$waiting_app = '';
			if($row['app_price_sup'] == 'Y'){
				$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
			}
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
			}

			if(empty($date_exp)){
				$status2="Not Set";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}
			
			$PRE = (!empty($row['price_from_supplier']))?$row['price_from_supplier']:0;
			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reject_reason']))."</div>";
				$edit	= '';
				if($Arr_Akses['update']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_supplier_acc/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_tanki($nama, $brand, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '5' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'dimensi',
			5 => 'spesifikasi',
			6 => 'ukuran_standart',
			7 => 'standart',
			8 => 'kode_satuan',
			9 => 'keterangan',
			10 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//APPROVE
	//Bolt & Nut
	public function data_side_bold_nut_app(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_bold_nut_app(
			$requestData['nama'],
			$requestData['brand'],
			$requestData['uri_app'],
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
			$nestedData[]	= "<div align='center'>".$row['id']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['panjang'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['radius'],2)."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";

			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_est'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			//purchase
			$date_now2 	= date('Y-m-d');
			$date_exp2 	= $row['exp_price_ref_pur'];

			$tgl1x2 = new DateTime($date_now2);
			$tgl2x2 = new DateTime($date_exp2);
			$selisihx2 = $tgl2x2->diff($tgl1x2)->days + 1;

			$date_expv2 	= date('d M Y', strtotime($date_exp2));
			$date_min2 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp2)));
			
			$tambahan2 = "No Set";
			if($tgl2x2 < $tgl1x2){
				$status22="Expired price";
				$tambahan2 = "<span class='badge bg-red'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 <= 7){
				$status22="Less one week expired price";
				$tambahan2 = "<span class='badge bg-blue'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 > 7){
				$tambahan2 = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			$PRE = (!empty($row['harga']))?$row['harga']:0;
			$PRP = (!empty($row['price_ref_purchase']))?$row['price_ref_purchase']:0;
			
			$tit_lab = 'Approve';
			$dis_lab = '';
			$col_lab = 'success';
			if($row['app_price_sup'] == 'N'){
				$tit_lab= "Tidak ada pengajuan price";
				$dis_lab = 'disabled';
				$col_lab = 'danger';
			}

			if(empty($date_exp)){
				$status2	= "Not Set";
				$tambahan 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}

			if(empty($date_exp2)){
				$status2	= "Not Set";
				$tambahan2 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv2 = 'Not setting';
			}

			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRP,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv2."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan2."</div>";
				$edit	= '';
				if($Arr_Akses['approve']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_accessories/'.$row['id'])."' class='btn btn-sm btn-".$col_lab."' ".$dis_lab." title='".$tit_lab."' data-role='qtip'><i class='fa fa-check'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_bold_nut_app($nama, $brand, $uri_app, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '1' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}

		$where_uri_app = "";
		if($uri_app == 'approve'){
			$where_uri_app = " AND a.app_price_sup='Y'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand." ".$where_uri_app."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'diameter',
			5 => 'panjang',
			6 => 'standart',
			7 => 'radius',
			8 => 'kode_satuan',
			9 => 'keterangan',
			10 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	//Plate
	public function data_side_plate_app(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_plate_app(
			$requestData['nama'],
			$requestData['brand'],
			$requestData['uri_app'],
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
			$nestedData[]	= "<div align='center'>".$row['id']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['thickness'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['density'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_est'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			//purchase
			$date_now2 	= date('Y-m-d');
			$date_exp2 	= $row['exp_price_ref_pur'];

			$tgl1x2 = new DateTime($date_now2);
			$tgl2x2 = new DateTime($date_exp2);
			$selisihx2 = $tgl2x2->diff($tgl1x2)->days + 1;

			$date_expv2 	= date('d M Y', strtotime($date_exp2));
			$date_min2 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp2)));
			
			$tambahan2 = "No Set";
			if($tgl2x2 < $tgl1x2){
				$status22="Expired price";
				$tambahan2 = "<span class='badge bg-red'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 <= 7){
				$status22="Less one week expired price";
				$tambahan2 = "<span class='badge bg-blue'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 > 7){
				$tambahan2 = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			$PRE = (!empty($row['harga']))?$row['harga']:0;
			$PRP = (!empty($row['price_ref_purchase']))?$row['price_ref_purchase']:0;
			
			$tit_lab = 'Approve';
			$dis_lab = '';
			$col_lab = 'success';
			if($row['app_price_sup'] == 'N'){
				$tit_lab= "Tidak ada pengajuan price";
				$dis_lab = 'disabled';
				$col_lab = 'danger';
			}

			if(empty($date_exp)){
				$status2	= "Not Set";
				$tambahan 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}

			if(empty($date_exp2)){
				$status2	= "Not Set";
				$tambahan2 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv2 = 'Not setting';
			}

			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRP,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv2."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan2."</div>";

				$edit	= '';
				if($Arr_Akses['approve']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_accessories/'.$row['id'])."' class='btn btn-sm btn-".$col_lab."' ".$dis_lab." title='".$tit_lab."' data-role='qtip'><i class='fa fa-check'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_plate_app($nama, $brand, $uri_app, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '2' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}

		$where_uri_app = "";
		if($uri_app == 'approve'){
			$where_uri_app = " AND a.app_price_sup='Y'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand." ".$where_uri_app."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'thickness',
			5 => 'density',
			6 => 'ukuran_standart',
			7 => 'standart',
			8 => 'kode_satuan',
			9 => 'keterangan',
			10 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	//Gasket
	public function data_side_gasket_app(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_gasket_app(
			$requestData['nama'],
			$requestData['brand'],
			$requestData['uri_app'],
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
			$nestedData[]	= "<div align='center'>".$row['id']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']." ".$row['dimensi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['thickness'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_est'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			//purchase
			$date_now2 	= date('Y-m-d');
			$date_exp2 	= $row['exp_price_ref_pur'];

			$tgl1x2 = new DateTime($date_now2);
			$tgl2x2 = new DateTime($date_exp2);
			$selisihx2 = $tgl2x2->diff($tgl1x2)->days + 1;

			$date_expv2 	= date('d M Y', strtotime($date_exp2));
			$date_min2 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp2)));
			
			$tambahan2 = "No Set";
			if($tgl2x2 < $tgl1x2){
				$status22="Expired price";
				$tambahan2 = "<span class='badge bg-red'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 <= 7){
				$status22="Less one week expired price";
				$tambahan2 = "<span class='badge bg-blue'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 > 7){
				$tambahan2 = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			$PRE = (!empty($row['harga']))?$row['harga']:0;
			$PRP = (!empty($row['price_ref_purchase']))?$row['price_ref_purchase']:0;
			
			$tit_lab = 'Approve';
			$dis_lab = '';
			$col_lab = 'success';
			if($row['app_price_sup'] == 'N'){
				$tit_lab= "Tidak ada pengajuan price";
				$dis_lab = 'disabled';
				$col_lab = 'danger';
			}

			if(empty($date_exp)){
				$status2	= "Not Set";
				$tambahan 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}

			if(empty($date_exp2)){
				$status2	= "Not Set";
				$tambahan2 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv2 = 'Not setting';
			}

			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRP,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv2."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan2."</div>";

				$edit	= '';
				if($Arr_Akses['approve']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_accessories/'.$row['id'])."' class='btn btn-sm btn-".$col_lab."' ".$dis_lab." title='".$tit_lab."' data-role='qtip'><i class='fa fa-check'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_gasket_app($nama, $brand, $uri_app, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_category = " AND a.category = '3' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}

		$where_uri_app = "";
		if($uri_app == 'approve'){
			$where_uri_app = " AND a.app_price_sup='Y'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand." ".$where_uri_app."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'thickness',
			5 => 'ukuran_standart',
			6 => 'standart',
			7 => 'kode_satuan',
			8 => 'keterangan',
			9 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	//Lainnya
	public function data_side_lainnya_app(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_lainnya_app(
			$requestData['nama'],
			$requestData['brand'],
			$requestData['uri_app'],
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
			$nestedData[]	= "<div align='center'>".$row['id']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['dimensi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['spesifikasi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			// $nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['exp_price_ref_est'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			//purchase
			$date_now2 	= date('Y-m-d');
			$date_exp2 	= $row['exp_price_ref_pur'];

			$tgl1x2 = new DateTime($date_now2);
			$tgl2x2 = new DateTime($date_exp2);
			$selisihx2 = $tgl2x2->diff($tgl1x2)->days + 1;

			$date_expv2 	= date('d M Y', strtotime($date_exp2));
			$date_min2 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp2)));
			
			$tambahan2 = "No Set";
			if($tgl2x2 < $tgl1x2){
				$status22="Expired price";
				$tambahan2 = "<span class='badge bg-red'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 <= 7){
				$status22="Less one week expired price";
				$tambahan2 = "<span class='badge bg-blue'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 > 7){
				$tambahan2 = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			$PRE = (!empty($row['harga']))?$row['harga']:0;
			$PRP = (!empty($row['price_ref_purchase']))?$row['price_ref_purchase']:0;
			
			$tit_lab = 'Approve';
			$dis_lab = '';
			$col_lab = 'success';
			if($row['app_price_sup'] == 'N'){
				$tit_lab= "Tidak ada pengajuan price";
				$dis_lab = 'disabled';
				$col_lab = 'danger';
			}

			if(empty($date_exp)){
				$status2	= "Not Set";
				$tambahan 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}

			if(empty($date_exp2)){
				$status2	= "Not Set";
				$tambahan2 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv2 = 'Not setting';
			}

			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRP,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv2."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan2."</div>";

				$edit	= '';
				if($Arr_Akses['approve']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_accessories/'.$row['id'])."' class='btn btn-sm btn-".$col_lab."' ".$dis_lab." title='".$tit_lab."' data-role='qtip'><i class='fa fa-check'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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

	public function get_query_json_lainnya_app($nama, $brand, $uri_app, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_category = " AND a.category = '4' ";
		
		$where_nama = "";
		if($nama != '0'){
			$where_nama = " AND a.nama='".$nama."'";
		}
		$where_brand = "";
		if($brand != '0'){
			$where_brand = " AND a.material='".$brand."'";
		}

		$where_uri_app = "";
		if($uri_app == 'approve'){
			$where_uri_app = " AND a.app_price_sup='Y'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.category AS category_,
				c.kode_satuan
			FROM
				accessories a 
				LEFT JOIN accessories_category b ON a.category = b.id
				LEFT JOIN raw_pieces c ON a.satuan = c.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 AND 
				a.deleted='N' 
				".$where_category." ".$where_nama." ".$where_brand." ".$where_uri_app."
			AND (
				a.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spesifikasi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_material',
			2 => 'nama',
			3 => 'material',
			4 => 'dimensi',
			5 => 'spesifikasi',
			6 => 'ukuran_standart',
			7 => 'standart',
			8 => 'kode_satuan',
			9 => 'keterangan',
			10 => 'harga'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}



	//PRICE FROM SUPPLIER RUTIN, EXPORT DAN LOKAL
	public function data_side_trucking(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_trucking(
			$requestData['category'],
			$requestData['area'],
			$requestData['dest'],
			$requestData['truck'],
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
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['expired_supplier'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$waiting_app = '';
			if($row['app_price_sup'] == 'Y'){
				$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
			}
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
			}

			if(empty($date_exp)){
				$status2="Not Set";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}

			$PRE = (!empty($row['price_supplier']))?$row['price_supplier']:0;

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['category'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['area'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['tujuan'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nama_truck'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRE)."</div>";
			$nestedData[]	= "<div align='center'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($row['reject_reason']))."</div>";
			$edit	= '';
				if($Arr_Akses['update']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_supplier_lokal/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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
  
	public function get_query_json_trucking($category, $area, $dest, $truck, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
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

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nama_truck
			FROM
				cost_trucking a 
				LEFT JOIN truck b ON a.id_truck=b.id,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_category."
				".$where_area."
				".$where_dest."
				".$where_truck."
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

	public function edit_supplier_rutin(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_supplier			= $data['price_supplier'];
			$expired_supplier		= $data['expired_supplier'];
			$note_sup				= strtolower($data['note_sup']);
			
			$Arr_Update	= array(
				'price_supplier' 		=> str_replace(',', '', $price_supplier),
				'expired_supplier' 		=> $expired_supplier,
				'note_sup' 				=> $note_sup,
				'app_price_sup' 		=> 'Y',
				'updated_by' 			=> $data_session['ORI_User']['username'],
				'updated_date' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
				$this->db->query("INSERT hist_price_ref (
										category,code_group,id_unit,unit_material,kurs,region,rate,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
										price_purchase,expired_purchase,sts_price,updated_by,updated_date,hist_by,hist_date 
									) 
									SELECT
										category,code_group,id_unit,unit_material,kurs,region,rate,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
										price_purchase,expired_purchase,sts_price,updated_by,updated_date,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										price_ref 
									WHERE
										id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('price_ref', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price from supplier rutin '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM price_ref WHERE id = '".$id."' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'rutin'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Edit Price From Supplier Rutin',
				'action'		=> 'edit_supplier_rutin',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_supplier_rutin',$data);
		}
	}

	public function edit_supplier_lokal(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_supplier			= $data['price_supplier'];
			$expired_supplier		= $data['expired_supplier'];
			$note_sup				= strtolower($data['note_sup']);
			
			$Arr_Update	= array(
				'price_supplier' 		=> str_replace(',', '', $price_supplier),
				'expired_supplier' 		=> $expired_supplier,
				'note_sup' 				=> $note_sup,
				'app_price_sup' 		=> 'Y',
				'updated_by' 			=> $data_session['ORI_User']['username'],
				'updated_date' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
				$this->db->query("INSERT hist_cost_trucking (
										id,category,area,tujuan,id_truck,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
										price_purchase,expired_purchase,created_by,created_date,updated_by,updated_date,hist_by,hist_date 
									) 
									SELECT
										id,category,area,tujuan,id_truck,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
										price_purchase,expired_purchase,created_by,created_date,updated_by,updated_date,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
									cost_trucking 
									WHERE
										id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('cost_trucking', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price from supplier rutin '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM cost_trucking WHERE id = '".$id."' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'transport'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Edit Price From Supplier Lokal',
				'action'		=> 'edit_supplier_lokal',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_supplier_lokal',$data);
		}
	}

	public function edit_supplier_eksport(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_supplier			= $data['price_supplier'];
			$expired_supplier		= $data['expired_supplier'];
			$note_sup				= strtolower($data['note_sup']);
			
			$Arr_Update	= array(
				'price_supplier' 		=> str_replace(',', '', $price_supplier),
				'expired_supplier' 		=> $expired_supplier,
				'note_sup' 				=> $note_sup,
				'app_price_sup' 		=> 'Y',
				'modified_by' 			=> $data_session['ORI_User']['username'],
				'modified_on' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
				$this->db->query("INSERT hist_cost_export_trans (
										id,country_code,shipping_name,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
										price_purchase,expired_purchase,created_by,created_on,modified_by,modified_on,hist_by,hist_date 
									) 
									SELECT
										id,country_code,shipping_name,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
										price_purchase,expired_purchase,created_by,created_on,modified_by,modified_on,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										cost_export_trans 
									WHERE
										id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('cost_export_trans', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price from supplier rutin '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM cost_export_trans WHERE id = '".$id."' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'transport'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Edit Price From Supplier Eksport',
				'action'		=> 'edit_supplier_eksport',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_supplier_eksport',$data);
		}
	}

	//APPROVAL RUTIN, EXPORT DAN LOKAL
	public function data_side_trucking_app(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_trucking_app(
			$requestData['category'],
			$requestData['area'],
			$requestData['dest'],
			$requestData['truck'],
			$requestData['uri_app'],
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
			
			//estimation
			$date_now 	= date('Y-m-d');
			$date_exp 	= $row['expired'];

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			// $selisih	= $date_expv->diff($date_now)->days;
			
			$tambahan = "No Set";
			if($tgl2x < $tgl1x){
				$status2="Expired price";
				$tambahan = "<span class='badge bg-red'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$status2="Less one week expired price";
				$tambahan = "<span class='badge bg-blue'>$status2</span>";
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$tambahan = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			//purchase
			$date_now2 	= date('Y-m-d');
			$date_exp2 	= $row['expired_purchase'];

			$tgl1x2 = new DateTime($date_now2);
			$tgl2x2 = new DateTime($date_exp2);
			$selisihx2 = $tgl2x2->diff($tgl1x2)->days + 1;

			$date_expv2 	= date('d M Y', strtotime($date_exp2));
			$date_min2 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp2)));
			
			$tambahan2 = "No Set";
			if($tgl2x2 < $tgl1x2){
				$status22="Expired price";
				$tambahan2 = "<span class='badge bg-red'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 <= 7){
				$status22="Less one week expired price";
				$tambahan2 = "<span class='badge bg-blue'>$status22</span>";
			}
			if($tgl2x2 >= $tgl1x2 AND $selisihx2 > 7){
				$tambahan2 = "<span class='badge bg-green'>Price Oke</span>";
			}
			
			$PRE = (!empty($row['price']))?$row['price']:0;
			$PRP = (!empty($row['price_purchase']))?$row['price_purchase']:0;
			
			$tit_lab = 'Approve';
			$dis_lab = '';
			$col_lab = 'success';
			if($row['app_price_sup'] == 'N'){
				$tit_lab= "Tidak ada pengajuan price";
				$dis_lab = 'disabled';
				$col_lab = 'danger';
			}

			if(empty($date_exp)){
				$status2	= "Not Set";
				$tambahan 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv 	= 'Not setting';
			}

			if(empty($date_exp2)){
				$status2	= "Not Set";
				$tambahan2 	= "<span class='badge bg-red'>$status2</span>";
				$date_expv2 = 'Not setting';
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['category'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['area'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['tujuan'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nama_truck'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRE,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRP,2)."</div>";
			$nestedData[]	= "<div align='right'>".$date_expv2."</div>";
			$nestedData[]	= "<div align='left'>".$tambahan2."</div>";
			$edit	= '';
				if($Arr_Akses['approve']=='1'){
					$edit	= "<a href='".site_url($this->uri->segment(1).'/edit_lokal/'.$row['id'])."' class='btn btn-sm btn-".$col_lab."' ".$dis_lab." title='".$tit_lab."' data-role='qtip'><i class='fa fa-check'></i></a>";
				}
			$nestedData[]	= "<div align='center'>".$edit."</div>";
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
  
	public function get_query_json_trucking_app($category, $area, $dest, $truck, $uri_app, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
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

		$where_uri_app = "";
		if($uri_app == 'approve'){
			$where_uri_app = " AND a.app_price_sup='Y'";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nama_truck
			FROM
				cost_trucking a 
				LEFT JOIN truck b ON a.id_truck=b.id,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_category."
				".$where_area."
				".$where_dest."
				".$where_truck."
				".$where_uri_app."
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

	public function edit_rutin(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_ref_estimation	= $data['price_ref_estimation'];
			$price_ref_purchase		= $data['price_ref_purchase'];
			$price_from_supplier	= $data['price_from_supplier'];
			
			$exp_price_ref_est		= $data['exp_price_ref_est'];
			$exp_price_ref_pur		= $data['exp_price_ref_pur'];
			$exp_price_ref_sup		= $data['exp_price_ref_sup'];
			$ket_price				= strtolower($data['ket_price']);
			
			$Arr_Update	= array(
				'rate' 				=> str_replace(',', '', $price_ref_estimation),
				'price_purchase' 		=> str_replace(',', '', $price_from_supplier),
				'expired' 				=> $exp_price_ref_est,
				'expired_purchase' 		=> $exp_price_ref_sup,
				'note' 					=> $ket_price,
				'reject_reason' 		=> NULL,
				'app_price_sup' 		=> 'N',
				'updated_by' 			=> $data_session['ORI_User']['username'],
				'updated_date' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
			$this->db->query("INSERT hist_price_ref (
									category,code_group,id_unit,unit_material,kurs,region,rate,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
									price_purchase,expired_purchase,sts_price,updated_by,updated_date,hist_by,hist_date 
								) 
								SELECT
									category,code_group,id_unit,unit_material,kurs,region,rate,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
									price_purchase,expired_purchase,sts_price,updated_by,updated_date,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
								FROM
									price_ref 
								WHERE
									id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('price_ref', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price reference rutin new (APPROVAL) '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM price_ref WHERE id = '".$id."' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'rutin'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Approval Price Rutin',
				'action'		=> 'edit_rutin',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_material_rutin',$data);
		}
	}

	public function reject_rutin(){
		$data			= $this->input->post();
		$Arr_Kembali	= array();
		$data_session	= $this->session->userdata;
		
		$id_material			= strtoupper($data['id']);
		$reject_reason			= strtolower($data['reject_reason']);
		
		$Arr_Update	= array(
			'reject_reason' 	=> $reject_reason,
			'app_price_sup' 	=> 'N',
			'updated_by' 		=> $data_session['ORI_User']['username'],
			'updated_date' 		=> date('Y-m-d H:i:s')
		);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id', $id_material);
			$this->db->update('price_ref', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{ 
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1
			);
			history('Reject price reference rutin new (APPROVAL) '.$id_material);
		}
		echo json_encode($Arr_Data);
	}

	public function edit_eksport(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_ref_estimation	= $data['price_ref_estimation'];
			$price_ref_purchase		= $data['price_ref_purchase'];
			$price_from_supplier	= $data['price_from_supplier'];
			
			$exp_price_ref_est		= $data['exp_price_ref_est'];
			$exp_price_ref_pur		= $data['exp_price_ref_pur'];
			$exp_price_ref_sup		= $data['exp_price_ref_sup'];
			$ket_price				= strtolower($data['ket_price']);
			
			$Arr_Update	= array(
				'price' 				=> str_replace(',', '', $price_ref_estimation),
				'price_purchase' 		=> str_replace(',', '', $price_from_supplier),
				'expired' 				=> $exp_price_ref_est,
				'expired_purchase' 		=> $exp_price_ref_sup,
				'note' 					=> $ket_price,
				'reject_reason' 		=> NULL,
				'app_price_sup' 		=> 'N',
				'modified_by' 			=> $data_session['ORI_User']['username'],
				'modified_on' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
			$this->db->query("INSERT hist_cost_export_trans (
									id,country_code,shipping_name,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
									price_purchase,expired_purchase,created_by,created_on,modified_by,modified_on,hist_by,hist_date 
								) 
								SELECT
									id,country_code,shipping_name,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
									price_purchase,expired_purchase,created_by,created_on,modified_by,modified_on,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
								FROM
									cost_export_trans 
								WHERE
									id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('cost_export_trans', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price reference transport export : '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM cost_export_trans WHERE id = '".$id."' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'transport'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Approval Price Transport Eksport',
				'action'		=> 'edit_transpoer',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_material_eksport',$data);
		}
	}

	public function reject_eksport(){
		$data			= $this->input->post();
		$Arr_Kembali	= array();
		$data_session	= $this->session->userdata;
		
		$id_material			= strtoupper($data['id']);
		$reject_reason			= strtolower($data['reject_reason']);
		
		$Arr_Update	= array(
			'reject_reason' 	=> $reject_reason,
			'app_price_sup' 	=> 'N',
			'modified_by' 		=> $data_session['ORI_User']['username'],
			'modified_on' 		=> date('Y-m-d H:i:s')
		);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id', $id_material);
			$this->db->update('cost_export_trans', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{ 
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1
			);
			history('Reject price reference export transport : '.$id_material);
		}
		echo json_encode($Arr_Data);
	}

	public function edit_lokal(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data			= $this->input->post();
			$Arr_Kembali	= array();
			$data_session	= $this->session->userdata;
			
			$id_material			= strtoupper($data['id']);
			$price_ref_estimation	= $data['price_ref_estimation'];
			$price_ref_purchase		= $data['price_ref_purchase'];
			$price_from_supplier	= $data['price_from_supplier'];
			
			$exp_price_ref_est		= $data['exp_price_ref_est'];
			$exp_price_ref_pur		= $data['exp_price_ref_pur'];
			$exp_price_ref_sup		= $data['exp_price_ref_sup'];
			$ket_price				= strtolower($data['ket_price']);
			
			$Arr_Update	= array(
				'price' 				=> str_replace(',', '', $price_ref_estimation),
				'price_purchase' 		=> str_replace(',', '', $price_from_supplier),
				'expired' 				=> $exp_price_ref_est,
				'expired_purchase' 		=> $exp_price_ref_sup,
				'note' 					=> $ket_price,
				'reject_reason' 		=> NULL,
				'app_price_sup' 		=> 'N',
				'updated_by' 			=> $data_session['ORI_User']['username'],
				'updated_date' 			=> date('Y-m-d H:i:s')
			);
			// exit;
			
			$this->db->trans_start();
			$this->db->query("INSERT hist_cost_trucking (
									id,category,area,tujuan,id_truck,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
									price_purchase,expired_purchase,created_by,created_date,updated_by,updated_date,hist_by,hist_date 
								) 
								SELECT
									id,category,area,tujuan,id_truck,price,expired,price_supplier,expired_supplier,app_price_sup,reject_ket,note_sup,
									price_purchase,expired_purchase,created_by,created_date,updated_by,updated_date,'".$data_session['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
								FROM
								cost_trucking 
								WHERE
									id = '".$id_material."'");
									
				$this->db->where('id', $id_material);
				$this->db->update('cost_trucking', $Arr_Update);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{ 
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update price reference transport lokal : '.$id_material);
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			
			$data_session	= $this->session->userdata;
			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM cost_trucking WHERE id = '".$id."' ")->result_array();

			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab price ref',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'transport'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'			=> 'Approval Price Transport Lokal',
				'action'		=> 'edit_lokal',
				'row'			=> $detail
			);
			
			$this->load->view('Cost/edit_material_lokal',$data);
		}
	}

	public function reject_lokal(){
		$data			= $this->input->post();
		$Arr_Kembali	= array();
		$data_session	= $this->session->userdata;
		
		$id_material			= strtoupper($data['id']);
		$reject_reason			= strtolower($data['reject_reason']);
		
		$Arr_Update	= array(
			'reject_reason' 	=> $reject_reason,
			'app_price_sup' 	=> 'N',
			'updated_by' 		=> $data_session['ORI_User']['username'],
			'updated_date' 		=> date('Y-m-d H:i:s')
		);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id', $id_material);
			$this->db->update('cost_trucking', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{ 
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update data success. Thanks ...',
				'status'	=> 1
			);
			history('Reject price reference lokal transport : '.$id_material);
		}
		echo json_encode($Arr_Data);
	}

	public function excel_price_ref_material(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'MASTER PRICE REFERENCE MATERIAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'CATGEORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'PRICE REF (USD)');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'EXPIRED');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'PRICE PURCHASE (USD)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);
		
		$sheet->setCellValue('I'.$NewRow, 'EXPIRED');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'COSTBOOK (IDR)');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'STATUS');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$row		= $this->db->get_where('raw_materials',array('delete'=>'N'))->result_array();
		$GET_COSTBOOK = get_costbook();
		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$idmaterial	= $row_Cek['idmaterial'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$price_ref_estimation	= $row_Cek['price_ref_estimation'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_estimation);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_est	= $row_Cek['exp_price_ref_est'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_est);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$price_ref_purchase	= $row_Cek['price_ref_purchase'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_purchase);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_pur	= $row_Cek['exp_price_ref_pur'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_pur);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$COSTBOOK = (!empty($GET_COSTBOOK[$row_Cek['id_material']]))?$GET_COSTBOOK[$row_Cek['id_material']]:0;
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $COSTBOOK);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$status	= 'ACTIVE';
				if($row_Cek['flag_active'] == 'N'){
					$status	= 'NOT-ACTIVE';
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);


			}
		}

		$sheet->setTitle('Material Price Reference');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="material-price-reference.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_price_ref_supplier(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'MASTER PRICE SUPPLIER MATERIAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'CATGEORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'PRICE SUPPLIER (USD)');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'EXPIRED');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'STATUS');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);
		
		// $sheet->setCellValue('I'.$NewRow, 'EXPIRED');
		// $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		// $sheet->getColumnDimension('I')->setWidth(20);

		// $sheet->setCellValue('J'.$NewRow, 'COSTBOOK (IDR)');
		// $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		// $sheet->getColumnDimension('J')->setWidth(20);

		// $sheet->setCellValue('K'.$NewRow, 'STATUS');
		// $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		// $sheet->getColumnDimension('K')->setWidth(20);

		$row		= $this->db->get_where('raw_materials',array('delete'=>'N'))->result_array();
		$GET_COSTBOOK = get_costbook();
		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$idmaterial	= $row_Cek['idmaterial'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				// $awal_col++;
				// $price_ref_estimation	= $row_Cek['price_ref_estimation'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $price_ref_estimation);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				// $awal_col++;
				// $exp_price_ref_est	= $row_Cek['exp_price_ref_est'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $exp_price_ref_est);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$price_ref_purchase	= $row_Cek['price_ref_purchase'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_purchase);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_pur	= $row_Cek['exp_price_ref_pur'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_pur);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				// $COSTBOOK = (!empty($GET_COSTBOOK[$row_Cek['id_material']]))?$GET_COSTBOOK[$row_Cek['id_material']]:0;
				// $awal_col++;
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $COSTBOOK);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$status	= 'ACTIVE';
				if($row_Cek['flag_active'] == 'N'){
					$status	= 'NOT-ACTIVE';
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);


			}
		}

		$sheet->setTitle('Material Price Supplier');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="material-price-supplier.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_price_ref_stok(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'MASTER PRICE REFERENCE STOK');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'EXCEL CODE');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'CATGEORY');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'PRICE REF (IDR)');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'EXPIRED');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);
		
		$sheet->setCellValue('I'.$NewRow, 'PRICE PURCHASE (IDR)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'EXPIRED');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'COSTBOOK (IDR)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$row = $this->db
					->select('	a.id,
								a.code_group,
								b.material_name,
								b.spec,
								b.brand,
								b.kode_item,
								b.kode_excel,
								b.category_awal,
								a.unit_material,
								a.kurs,
								a.expired_supplier,
								a.expired_purchase,
								a.expired,
								a.app_price_sup,
								a.price_supplier,
								a.price_purchase,
								a.rate,
								a.reject_ket,
								a.rate')
					->from('price_ref a')
					->join('con_nonmat_new b','a.code_group=b.code_group','left')
					->where('a.category','consumable')
					->where('a.sts_price','N')
					->where("a.deleted = 'N'")
					->where("b.status = '1'")
					->get()
					->result_array();
		$GET_COSTBOOK = get_costbook();
		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$code_group	= $row_Cek['code_group'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_item	= $row_Cek['kode_item'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_item);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_excel	= $row_Cek['kode_excel'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_excel);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material_name	= strtoupper($row_Cek['material_name']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$GETCATEGORY = get_name('con_nonmat_category_awal','category','id',$row_Cek['category_awal']);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, strtoupper($GETCATEGORY));
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$PRE = (!empty($row_Cek['rate']))?$row_Cek['rate']:0;
				$PRP = (!empty($row_Cek['price_purchase']))?$row_Cek['price_purchase']:0;

				$awal_col++;
				$price_ref_estimation	= $PRE;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_estimation);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_est	= $row_Cek['expired'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_est);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$price_ref_purchase	= $PRP;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_purchase);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_pur	= $row_Cek['expired_purchase'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_pur);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$COSTBOOK = (!empty($GET_COSTBOOK[$row_Cek['code_group']]))?$GET_COSTBOOK[$row_Cek['code_group']]:0;
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $COSTBOOK);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		$sheet->setTitle('Stok Price Reference');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="stok-price-reference.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_price_sup_stok(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'MASTER PRICE SUPPLIER STOK');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'EXCEL CODE');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'CATGEORY');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'PRICE PURCHASE (IDR)');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'EXPIRED');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);
		
		// $sheet->setCellValue('I'.$NewRow, 'PRICE PURCHASE (IDR)');
		// $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		// $sheet->getColumnDimension('I')->setWidth(20);

		// $sheet->setCellValue('J'.$NewRow, 'EXPIRED');
		// $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		// $sheet->getColumnDimension('J')->setWidth(20);

		// $sheet->setCellValue('K'.$NewRow, 'COSTBOOK (IDR)');
		// $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		// $sheet->getColumnDimension('K')->setWidth(20);

		$row = $this->db
					->select('	a.id,
								a.code_group,
								b.material_name,
								b.spec,
								b.brand,
								b.kode_item,
								b.kode_excel,
								b.category_awal,
								a.unit_material,
								a.kurs,
								a.expired_supplier,
								a.expired_purchase,
								a.expired,
								a.app_price_sup,
								a.price_supplier,
								a.price_purchase,
								a.rate,
								a.reject_ket,
								a.rate')
					->from('price_ref a')
					->join('con_nonmat_new b','a.code_group=b.code_group','left')
					->where('a.category','consumable')
					->where('a.sts_price','N')
					->where("a.deleted = 'N'")
					->get()
					->result_array();
		$GET_COSTBOOK = get_costbook();
		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$code_group	= $row_Cek['code_group'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_item	= $row_Cek['kode_item'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_item);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_excel	= $row_Cek['kode_excel'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_excel);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material_name	= strtoupper($row_Cek['material_name']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$GETCATEGORY = get_name('con_nonmat_category_awal','category','id',$row_Cek['category_awal']);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, strtoupper($GETCATEGORY));
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$PRE = (!empty($row_Cek['rate']))?$row_Cek['rate']:0;
				$PRP = (!empty($row_Cek['price_purchase']))?$row_Cek['price_purchase']:0;

				// $awal_col++;
				// $price_ref_estimation	= $PRE;
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $price_ref_estimation);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				// $awal_col++;
				// $exp_price_ref_est	= $row_Cek['expired'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $exp_price_ref_est);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$price_ref_purchase	= $PRP;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_purchase);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_pur	= $row_Cek['expired_purchase'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_pur);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				// $COSTBOOK = (!empty($GET_COSTBOOK[$row_Cek['code_group']]))?$GET_COSTBOOK[$row_Cek['code_group']]:0;
				// $awal_col++;
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $COSTBOOK);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}

		$sheet->setTitle('Stok Price Supplier');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="stok-price-supplier.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_price_ref_aksesoris(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'MASTER PRICE REFERENCE AKSESORIS');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'CATGEORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'DIAMETER');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'PANJANG');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'THICKNESS');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);
		
		$sheet->setCellValue('I'.$NewRow, 'RADIUS');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'DIMENSI');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'SPESIFIKASI');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'MATERIAL');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('M'.$NewRow, 'SATUAN');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(20);

		$sheet->setCellValue('N'.$NewRow, 'STANDART');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(20);

		$sheet->setCellValue('O'.$NewRow, 'UKURAN STANDART');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setWidth(20);

		$sheet->setCellValue('P'.$NewRow, 'KETERANGAN');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setWidth(20);

		$sheet->setCellValue('Q'.$NewRow, 'PRICE REF (USD)');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setWidth(20);

		$sheet->setCellValue('R'.$NewRow, 'EXPIRED');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setWidth(20);

		$sheet->setCellValue('S'.$NewRow, 'PRICE PURCHASE (USD)');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setWidth(20);

		$sheet->setCellValue('T'.$NewRow, 'EXPIRED');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
		$sheet->getColumnDimension('T')->setWidth(20);

		// $sheet->setCellValue('U'.$NewRow, 'COSTBOOK (IDR)');
		// $sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		// $sheet->getColumnDimension('U')->setWidth(20);

		$row		= $this->db
		  					->select('
									a.id, 
									a.id_material, 
									a.nama, 
									a.diameter, 
									a.panjang, 
									a.thickness, 
									a.radius, 
									a.dimensi, 
									a.spesifikasi, 
									a.material, 
									a.standart, 
									a.ukuran_standart, 
									a.keterangan, 
									a.price_ref_purchase, 
									a.price_from_supplier, 
									a.exp_price_ref_pur, 
									a.exp_price_ref_sup,
									a.harga,
									a.exp_price_ref_est,
									b.category AS nm_category, 
									c.kode_satuan
								')
							->join('accessories_category b','a.category=b.id','left')
							->join('raw_pieces c','a.satuan=c.id_satuan','left')
							->get_where('accessories a',array('a.deleted_date'=>NULL))->result_array();
		$GET_COSTBOOK = get_costbook();
		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id	= $row_Cek['id'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nama	= $row_Cek['nama'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nama);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$diameter	= $row_Cek['diameter'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$panjang	= $row_Cek['panjang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $panjang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$thickness	= $row_Cek['thickness'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $thickness);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$radius	= $row_Cek['radius'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $radius);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$dimensi	= $row_Cek['dimensi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $dimensi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$spesifikasi	= $row_Cek['spesifikasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spesifikasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material	= $row_Cek['material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_satuan	= $row_Cek['kode_satuan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_satuan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$standart	= $row_Cek['standart'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $standart);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$ukuran_standart	= $row_Cek['ukuran_standart'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ukuran_standart);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$keterangan	= $row_Cek['keterangan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $keterangan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$harga	= $row_Cek['harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_est	= $row_Cek['exp_price_ref_est'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_est);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$price_ref_purchase	= $row_Cek['price_ref_purchase'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_purchase);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_pur	= $row_Cek['exp_price_ref_pur'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_pur);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				// $COSTBOOK = (!empty($GET_COSTBOOK[$row_Cek['id']]))?$GET_COSTBOOK[$row_Cek['id']]:0;
				// $awal_col++;
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $COSTBOOK);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);


			}
		}

		$sheet->setTitle('Aksesoris Price Reference');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="aksesoris-price-reference.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_price_sup_aksesoris(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'MASTER PRICE SUPPLIER AKSESORIS');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'CATGEORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'DIAMETER');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'PANJANG');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'THICKNESS');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);
		
		$sheet->setCellValue('I'.$NewRow, 'RADIUS');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'DIMENSI');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'SPESIFIKASI');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'MATERIAL');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('M'.$NewRow, 'SATUAN');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(20);

		$sheet->setCellValue('N'.$NewRow, 'STANDART');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(20);

		$sheet->setCellValue('O'.$NewRow, 'UKURAN STANDART');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setWidth(20);

		$sheet->setCellValue('P'.$NewRow, 'KETERANGAN');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setWidth(20);

		$sheet->setCellValue('Q'.$NewRow, 'PRICE PURCHASE (USD)');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setWidth(20);

		$sheet->setCellValue('R'.$NewRow, 'EXPIRED');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setWidth(20);

		// $sheet->setCellValue('S'.$NewRow, 'PRICE PURCHASE (USD)');
		// $sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		// $sheet->getColumnDimension('S')->setWidth(20);

		// $sheet->setCellValue('T'.$NewRow, 'EXPIRED');
		// $sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
		// $sheet->getColumnDimension('T')->setWidth(20);

		// $sheet->setCellValue('U'.$NewRow, 'COSTBOOK (IDR)');
		// $sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		// $sheet->getColumnDimension('U')->setWidth(20);

		$row		= $this->db
		  					->select('
									a.id, 
									a.id_material, 
									a.nama, 
									a.diameter, 
									a.panjang, 
									a.thickness, 
									a.radius, 
									a.dimensi, 
									a.spesifikasi, 
									a.material, 
									a.standart, 
									a.ukuran_standart, 
									a.keterangan, 
									a.price_ref_purchase, 
									a.price_from_supplier, 
									a.exp_price_ref_pur, 
									a.exp_price_ref_sup,
									a.harga,
									a.exp_price_ref_est,
									b.category AS nm_category, 
									c.kode_satuan
								')
							->join('accessories_category b','a.category=b.id','left')
							->join('raw_pieces c','a.satuan=c.id_satuan','left')
							->get_where('accessories a',array('a.deleted_date'=>NULL))->result_array();
		$GET_COSTBOOK = get_costbook();
		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id	= $row_Cek['id'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nama	= $row_Cek['nama'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nama);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$diameter	= $row_Cek['diameter'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$panjang	= $row_Cek['panjang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $panjang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$thickness	= $row_Cek['thickness'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $thickness);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$radius	= $row_Cek['radius'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $radius);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$dimensi	= $row_Cek['dimensi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $dimensi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$spesifikasi	= $row_Cek['spesifikasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spesifikasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material	= $row_Cek['material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_satuan	= $row_Cek['kode_satuan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_satuan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$standart	= $row_Cek['standart'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $standart);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$ukuran_standart	= $row_Cek['ukuran_standart'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ukuran_standart);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$keterangan	= $row_Cek['keterangan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $keterangan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				// $awal_col++;
				// $harga	= $row_Cek['harga'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $harga);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				// $awal_col++;
				// $exp_price_ref_est	= $row_Cek['exp_price_ref_est'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $exp_price_ref_est);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$price_ref_purchase	= $row_Cek['price_ref_purchase'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_ref_purchase);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$exp_price_ref_pur	= $row_Cek['exp_price_ref_pur'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $exp_price_ref_pur);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				// $COSTBOOK = (!empty($GET_COSTBOOK[$row_Cek['id']]))?$GET_COSTBOOK[$row_Cek['id']]:0;
				// $awal_col++;
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $COSTBOOK);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);


			}
		}

		$sheet->setTitle('Aksesoris Price Supplier');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="aksesoris-price-supplier.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function modalHistoryCostBook($ID){

		$SQL = "SELECT
					a.id_material,
					a.price_book,
					a.updated_date,
					b.material_name
				FROM
					price_book a
					LEFT JOIN con_nonmat_new b ON a.id_material=b.code_group
				WHERE
					a.updated_date >= '2023-05-11 21:24:48' 
					-- AND a.id_material LIKE 'CN%' 
					AND a.id_material = '$ID' 
					-- AND DATE( a.updated_date ) <= '2023-11-05' 
				ORDER BY
					a.updated_date ASC";
		$detail = $this->db->query($SQL)->result_array();

		$data = [
			'detail' => $detail
		];

		$this->load->view('Cost/modalHistoryCostBook',$data);
	}

	public function get_history_costbook_rutin(){
		$tanggal = $this->uri->segment(3);

		$dateFilter = (!empty($tanggal))?$tanggal:date('Y-m-d');
		
		$SQL = "SELECT * FROM con_nonmat_new WHERE category_awal='1' AND `status`='1' AND deleted_date IS NULL";
		$result = $this->db->query($SQL)->result_array();

		$SQLPriceBook = "	SELECT
								MAX( a.id ) AS id,
								a.id_material 
							FROM
								price_book a 
							WHERE
								a.updated_date >= '2023-05-11 21:24:48' 
								AND DATE( a.updated_date ) <= '$dateFilter' 
								AND a.id_material NOT LIKE 'MTL%'
							GROUP BY
								a.id_material";
		$resultPriceBook = $this->db->query($SQLPriceBook)->result_array();
		
		$GET_PRICE_BOOK = $this->getArrayPriceBook();
		$ArrPriceBook = [];
		foreach ($resultPriceBook as $key => $value) {
			$priceBook = (!empty($GET_PRICE_BOOK[$value['id']]))?$GET_PRICE_BOOK[$value['id']]:0;
			$ArrPriceBook[$value['id_material']] = $priceBook;
		}

		$option = "";
		$option .= "<table class='table table-bordered' width='100%'>";
			$option .= "<tr class='bg-blue'>";
				$option .= "<th>#</th>";
				$option .= "<th>Code Excel</th>";
				$option .= "<th>Stok Name</th>";
				$option .= "<th class='text-right'>Price Book</th>";
			$option .= "</tr>";
			foreach ($result as $key => $value) { $key++;
				$priceBook = (!empty($ArrPriceBook[$value['code_group']]))?$ArrPriceBook[$value['code_group']]:0;
				$option .= "<tr>";
					$option .= "<td>".$key."</td>";
					$option .= "<td>".$value['kode_excel']."</td>";
					$option .= "<td>".strtoupper($value['material_name'])."</td>";
					$option .= "<td align='right'>".number_format($priceBook)."</td>";
				$option .= "</tr>";
			}
		$option .= "</table>";



		echo json_encode(array(
			'option' => $option
		));
	}

	public function getArrayPriceBook(){
		$result = $this->db->get('price_book')->result_array();
		$ArrResult = [];
		foreach ($result as $key => $value) {
			$ArrResult[$value['id']] = $value['price_book'];
		}

		return $ArrResult;
	}

}