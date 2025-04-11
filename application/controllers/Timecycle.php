<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Timecycle extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->load->model('master_model');

    $this->load->database();
    // $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
      redirect('login');
    }
  }

  public function step(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/step";
  		$Arr_Akses			= getAcccesmenu($controller);
		// print_r($Arr_Akses);
  		if($Arr_Akses['read'] !='1'){
  			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
  			redirect(site_url('dashboard'));
  		}

  		$productN		= $this->uri->segment(3);
  		$menu_akses		= $this->master_model->getMenu();

  		$data = array(
  			'title'			=> 'Indeks Of Master Step',
  			'action'		=> 'index',
  			'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses
  		);
  		history("View Master Step ".$productN);
  		$this->load->view('Timecycle/step',$data);
	}
	
	public function time(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/time";
  		$Arr_Akses			= getAcccesmenu($controller);
		// print_r($Arr_Akses);
  		if($Arr_Akses['read'] !='1'){
  			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
  			redirect(site_url('dashboard'));
  		}

  		$productN		= $this->uri->segment(3);
  		$menu_akses		= $this->master_model->getMenu();

  		$data = array(
  			'title'			=> 'Indeks Of Master Time',
  			'action'		=> 'index',
  			'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses
  		);
  		history("View Master Time ".$productN);
  		$this->load->view('Timecycle/step',$data);
	}
	
	//JSON Master
	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/step";
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
			$nestedData[]	= "<div align='left'>".strtoupper($row['parent_product'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['standart_code']."</div>";
			$nestedData[]	= "<div align='center'>".$row['jumlah']."</div>";
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($row['created_by']))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($row['created_date']))."</div>";

			$update = "";
			$delete = "";
			if($Arr_Akses['update']=='1'){
				$update	= "<a id='editM' data-parent_product='".str_replace(' ', '_', $row['parent_product'])."' data-standart_code='".str_replace(' ', '_', $row['standart_code'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}if($Arr_Akses['update']=='1'){
				$delete	= "<a id='deleteM' data-parent_product='".str_replace(' ', '_', $row['parent_product'])."' data-standart_code='".str_replace(' ', '_', $row['standart_code'])."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
			}

			$nestedData[]	= "<div align='center'>
									<a id='viewM' data-parent_product='".str_replace(' ', '_', $row['parent_product'])."' data-standart_code='".str_replace(' ', '_', $row['standart_code'])."' class='btn btn-sm btn-success' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></a>
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_sts = "";
		// if(!empty($sts_mesin)){
			// $where_sts = " AND status = 'Y' ";
		// }

		$sql = "
			SELECT
				*,
				COUNT(*) AS jumlah 
			FROM
				cycle_time_step
			WHERE 1=1
				".$where_sts."
				AND `delete`='N' AND(
				parent_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR standart_code LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id',
			1 => 'parent_product',
			2 => 'standart_code'
		);

		$sql .= " GROUP BY parent_product, standart_code ORDER BY id ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add_step(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$dataDet	= $data['ListStep'];
			$ArrData	= array();
			$no = 0;
			
			foreach($dataDet AS $val => $valx){
				$no++;
				$ArrData[$val]['parent_product'] 	= $data['product_parent'];
				$ArrData[$val]['standart_code'] 	= $data['standart_code'];
				$ArrData[$val]['urutan']			= $no;
				$ArrData[$val]['step'] 				= $valx['step'];
				$ArrData[$val]['created_by'] 		= $data_session['ORI_User']['username'];
				$ArrData[$val]['created_date'] 		= date('Y-m-d H:i:s');
			}
			
			// print_r($ArrData); exit;
			
			$this->db->trans_start();
				$this->db->insert_batch('cycle_time_step', $ArrData);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Add Step. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Add Step. Thanks ...',
					'status'		=> 1
					
				);
				history('Add New Step : '.$data['product_parent']."/".$data['standart_code']); 
			}
			echo json_encode($Arr_Kembali);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)))."/step";
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			
			$get_Data			= $this->db->query("SELECT * FROM product_parent WHERE estimasi='Y' ORDER BY product_parent ASC")->result_array();
			$get_Std			= $this->db->query("SELECT * FROM help_default_name ORDER BY nm_default ASC")->result_array();
			$data = array(
				'title'			=> 'Add Step',
				'action'		=> 'add_step',
				'product'		=> $get_Data,
				'standart'		=> $get_Std
			);
			$this->load->view('Timecycle/add_step',$data);
		}
	}
	
	public function edit_step(){
		$Arr_Kembali			= array();			
		$data					= $this->input->post();
		$data_session			= $this->session->userdata;
		
		$dataDet	= $data['ListStep'];
		$ArrData	= array();
		$no = 0;
		
		foreach($dataDet AS $val => $valx){
			$no++;
			$ArrData[$val]['parent_product'] 	= $data['product_parent'];
			$ArrData[$val]['standart_code'] 	= $data['standart_code'];
			$ArrData[$val]['urutan']			= $no;
			$ArrData[$val]['step'] 				= $valx['step'];
			$ArrData[$val]['created_by'] 		= $data_session['ORI_User']['username'];
			$ArrData[$val]['created_date'] 		= date('Y-m-d H:i:s');
			$ArrData[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
			$ArrData[$val]['updated_date'] 		= date('Y-m-d H:i:s');
		}
		
		$Arr_Update	= array(
			'delete' 			=> 'Y',
			'updated_by' 		=> $data_session['ORI_User']['username'],
			'updated_date' 		=> date('Y-m-d H:i:s'),
			'deleted_by' 		=> $data_session['ORI_User']['username'],
			'deleted_date' 		=> date('Y-m-d H:i:s')
		);
		
		$ArrWhere	= array(
			'parent_product' 	=> $data['product_parent'],
			'standart_code' 	=> $data['standart_code']
		);
		
		// print_r($ArrData); exit;
		
		$this->db->trans_start();
			$this->db->where($ArrWhere);
			$this->db->update('cycle_time_step', $Arr_Update);
			
			$this->db->insert_batch('cycle_time_step', $ArrData);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'			=> 'Failed Add Step. Please try again later ...',
				'status'		=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=> 'Success Add Step. Thanks ...',
				'status'		=> 1
				
			);
			history('Edit Step : '.$data['product_parent']."/".$data['standart_code']); 
		}
		echo json_encode($Arr_Kembali);
	}
	
	public function modalAddStep_Master(){
		$this->load->view('Timecycle/modalAddList');
	}
	
	public function addStepSave_Master(){
  		$data				= $this->input->post();

  		$insertData	= array(
  			'step_name'	=> strtoupper($data['step_name'])
  		);

  		$getNum	= $this->db->query("SELECT * FROM cycletime_step WHERE step_name='".strtoupper($data['step_name'])."' ")->num_rows();

  		if($getNum < 1){
  			$this->db->trans_start();
  				$this->db->insert('cycletime_step', $insertData);
  			$this->db->trans_complete();

  			if($this->db->trans_status() === FALSE){
  				$this->db->trans_rollback();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Failed Add Default. Please try again later ...',
  					'status'	=> 0
  				);
  			}
  			else{
  				$this->db->trans_commit();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Success Add Default. Thanks ...',
  					'status'	=> 1
  				);
  				history('Add List Standart Step');
  			}
  		}
  		else{
  			$Arr_Kembali	= array(
  					'pesan'		=>'Default Name Already exists',
  					'status'	=> 0
  				);
  		}

  		echo json_encode($Arr_Kembali);
	}
	
	
	
	function hapus_step(){
		$parent_product 	= str_replace('_', ' ',$this->uri->segment(3));
		$standart_code 		= str_replace('_', ' ',$this->uri->segment(4));
		$data_session			= $this->session->userdata;	
		// echo $id_mesin; exit;
		$Arr_Update	= array(
			'delete' 			=> 'Y',
			'deleted_by' 		=> $data_session['ORI_User']['username'],
			'deleted_date' 		=> date('Y-m-d H:i:s')
		);
		
		$ArrWhere	= array(
			'parent_product' 	=> $parent_product,
			'standart_code' 	=> $standart_code,
			'delete' 			=> 'N'
		);
		
		// echo "<pre>"; print_r($Arr_Update);
		// echo "<pre>"; print_r($ArrWhere);
		// exit;
		
		$this->db->trans_start();
			$this->db->where($ArrWhere);
			$this->db->update('cycle_time_step', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete step data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete step data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Step with Kode/Id : '.$parent_product.' '.$standart_code);
		}
		echo json_encode($Arr_Data);
	}
	
	public function modalDetailStep(){
		$this->load->view('Timecycle/modalDetailStep');
	}
	
	public function modalEditStep(){
		$this->load->view('Timecycle/modalEditStep');
	}

	public function edit(){	 	

		$Arr_Kembali			= array();			
		$data					= $this->input->post();
		$data_session			= $this->session->userdata;
		
		$id				= $data['id'];
		$product_parent	= $data['product_parent'];
		$diameter		= $data['diameter'];
		$diameter2		= $data['diameter2'];
		$dimensi		= $data['dimensi'];
		$harga			= $data['harga'];
		$est_pakai		= $data['est_pakai'];
		$biaya_per_pcs	= $data['biaya_per_pcs'];
		
		// echo $numType; exit;
		$Arr_Update	= array(
			'product_parent' 	=> $product_parent,
			'diameter' 			=> $diameter,
			'diameter2' 		=> $diameter2,
			'dimensi' 			=> $dimensi,
			'harga' 			=> $harga,
			'est_pakai'			=> $est_pakai,
			'biaya_per_pcs' 	=> $biaya_per_pcs,
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);
		
		// print_r($data);
		// exit;
		
		
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('mould_mandrill', $Arr_Update);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'			=> 'Failed Edit Mould Mandrill. Please try again later ...',
				'status'		=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=> 'Success Edit Mould Mandrill. Thanks ...',
				'status'		=> 1
				
			);
			history('Edit Mould Mandrill : '.$product_parent."/".$diameter."/".$diameter2."/".$dimensi); 
		}
		echo json_encode($Arr_Kembali);
		
		
	}
	
	public function getCategory(){
		$sqlSup		= "SELECT * FROM cycletime_step ORDER BY step_name ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Step</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['step_name']."'>".$valx['step_name']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
}

?>
