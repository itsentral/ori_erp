<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload_custom extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('component_custom_model');
		
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
		
		$productN		= $this->uri->segment(3);
		$get_Data		= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.deleted ='N' ORDER BY a.status DESC")->result();
		$menu_akses		= $this->master_model->getMenu();
		$getSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted = 'N' AND `status` = 'Y' ORDER BY pressure ASC, resin_system ASC, liner ASC")->result_array();
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		$ListCustomer	= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
		
		$diameter	= $this->db->query("SELECT value_d FROM product GROUP BY value_d ORDER BY value_d ASC")->result_array();
		$diameter2	= $this->db->query("SELECT value_d2 FROM product WHERE value_d2 <> NULL OR value_d2 <> '0' GROUP BY value_d2 ORDER BY value_d2 ASC")->result_array();

		$data = array(
			'title'			=> 'Upload Custom',
			'action'		=> 'index',
			'listseries'	=> $getSeries,
			'listkomponen'	=> $getKomp,
			'cust'			=> $ListCustomer,
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'diameter'		=> $diameter,
			'diameter2'		=> $diameter2
		);

		$this->load->view('Upload_custom/index',$data);
	}

    public function getDataJSON2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON2(
			$requestData['series'],
			$requestData['komponen'], 
			$requestData['cust'], 
			$requestData['diameter'],
			$requestData['diameter2'],
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
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>"; 
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_customer']))."</div>";
			$nestedData[]	= "<div align='left'>".ucwords($row['parent_product'])."</div>";	
			$nestedData[]	= "<div align='left'>".spec_master($row['id_product'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['stiffness']."</div>";  
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($row['created_by']))."</div>";   
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['rev']."</span></div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['created_date']))."</div>";
			
			$WEIGHT = (!empty($row['berat'])) ? $row['berat'] : get_berat_est($row['id_product']);
			$WEIGHT_JOINT_RESIN = 0;
			
			$nestedData[]	= "<div align='right'>".number_format($WEIGHT + $WEIGHT_JOINT_RESIN,3)."</div>";
            $nestedData[]	= "<div align='right'>Status</div>";
				$Upd    = "";
				$Upd2   = "";
				$Del    = "";
				if($Arr_Akses['update']=='1'){
					$Upd2 = "&nbsp;<a href='".site_url('cust_component/custom_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
				} 
				if($Arr_Akses['delete']=='1'){
					if($row['status'] == 'WAITING APPROVAL'){
						if($row['parent_product'] != 'product kosong'){
						$Del = "&nbsp;<button id='del_type' data-idcategory='".$row['id_product']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></button>";
						}
					}
				}
			$nestedData[]	= "<div align='left'>
									<button type='button' data-id_product='".$row['id_product']."' data-nm_product='".$row['nm_product']."' class='btn btn-sm btn-success MatDetail' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>
									&nbsp;<button type='button' data-id_product='".$row['id_product']."' class='btn btn-sm btn-info mat_weight' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$Upd."
									".$Upd2."
									".$Del."
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

	public function queryDataJSON2($series, $komponen, $cust, $diameter, $diameter2, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_series = "";
		if(!empty($series)){
			$where_series = " AND a.series = '".$series."' ";
		}
		
		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND a.parent_product = '".$komponen."' ";
		}
		
		$where_cust = "";
		if(!empty($cust)){
			$where_cust = " AND a.cust = '".$cust."' ";
		}
		
		$where_dim = "";
		if($diameter <> '0'){
			$where_dim = " AND a.diameter = '".$diameter."' ";
		}
		
		$where_dim2 = "";
		if($diameter2 <> '0'){
			$where_dim2 = " AND a.diameter2 = '".$diameter2."' ";
		}
		
		$sql = "
			SELECT 
				(@row:=@row+1) AS nomor,
				a.*, 
				b.nm_customer,
				c.id_product
			FROM 
				upload_component_header a 
				LEFT JOIN component_header c ON a.id_product=c.id_product
				LEFT JOIN customer b ON b.id_customer=a.cust,
				(SELECT @row:=0) r  
			WHERE 1=1 
				".$where_series."
				".$where_komponen."
				".$where_cust."
				".$where_dim."
				".$where_dim2."
				AND a.deleted ='N' AND a.cust IS NOT NULL AND a.cust != '' 
			AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.diameter LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.diameter2 LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.radius LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.type_elbow LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.angle LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.parent_product LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		
		// echo $sql;  

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'nm_customer',
			3 => 'parent_product',
			5 => 'stiffness',
			6 => 'created_by',
			8 => 'rev'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function upload(){	
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('upload_custom'));
		}
		$arr_Where			= array('flag_active'=>'1');
		$get_Data			= $this->master_model->getMenu($arr_Where);
		$data = array(
			'title'			=> 'Upload Custom',
			'action'		=> 'add',
			'data_menu'		=> $get_Data
		);
		$this->load->view('Upload_custom/add',$data);
		
	}

	public function upload_temp_nonfrp(){
		$data = $this->input->post();

		//Upload Data
		set_time_limit(0);
        ini_set('memory_limit','2048M');
		$ArrHeader 		= array();
		$ArrDetail 		= array();
		$ArrDetailPlus 	= array();
		$ArrDetailAdd 	= array();
		//HEADER
		if($_FILES['upload_header']['name']){
			$exts   = getExtension($_FILES['upload_header']['name']);
			if(!in_array($exts,array(1=>'xls','xlsx')))
			{
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Invalid file type, Please Upload the Excel format ...'
				);
			}
			else{
				
				$fileName = $_FILES['upload_header']['name'];
				$this->load->library(array('PHPExcel'));
				$config['upload_path'] = './assets/file/'; 
				$config['file_name'] = $fileName;
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = 10000;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				// echo $fileName; exit;

				if (!$this->upload->do_upload('upload_header')) {
					$error = array('error' => $this->upload->display_errors());
					$Arr_Kembali		= array(
						'status'		=> 3,
						'pesan'			=> $error['error']
					);
				}
				else{
					// echo 'success!';
					$media = $this->upload->data();
					$inputFileName = './assets/file/'.$media['file_name'];
					
					$data_session	= $this->session->userdata;
					$Create_By      = $data_session['ORI_User']['username'];
					$Create_Date    = date('Y-m-d H:i:s');
						
					try{
						$inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
						$objReader      = PHPExcel_IOFactory::createReader($inputFileType); 
						$objReader->setReadDataOnly(true);                               
						$objPHPExcel    = $objReader->load($inputFileName);
							
					}catch(Exception $e){
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());                               
					}

					$sheet = $objPHPExcel->getSheet(0);
					$highestRow     = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();
					$Error      = 0;
					$Arr_Keys   = array();
					$Loop       = 0;
					$Total      = 0;
					$Message    = "";
					$Urut       = 0;
					
					
					$intL 		= 0;
					$intError 	= 0;
					$pesan 		= '';
					$status		= '';

					for ($row = 5; $row <= $highestRow; $row++){ 
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
						
						$Urut++;

						$id_part							= (isset($rowData[0][0]) && $rowData[0][0])?$rowData[0][0]:'';
						$ArrHeader[$Urut]['id_part']  		= trim($id_part);

						$id_category						= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:'';
						$ArrHeader[$Urut]['id_category']  	= $id_category;

						
					}
					// print_r($ArrHeader);
					// exit;
				}
			}
		}

		//DETAIL
		if($_FILES['upload_detail']['name']){
			$exts   = getExtension($_FILES['upload_detail']['name']);
			if(!in_array($exts,array(1=>'xls','xlsx')))
			{
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Invalid file type, Please Upload the Excel format ...'
				);
			}
			else{
				
				$fileName = $_FILES['upload_detail']['name'];
				$this->load->library(array('PHPExcel'));
				$config['upload_path'] = './assets/file/'; 
				$config['file_name'] = $fileName;
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = 10000;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				// echo $fileName; exit;

				if (!$this->upload->do_upload('upload_detail')) {
					$error = array('error' => $this->upload->display_errors());
					$Arr_Kembali		= array(
						'status'		=> 3,
						'pesan'			=> $error['error']
					);
				}
				else{
					// echo 'success!';
					$media = $this->upload->data();
					$inputFileName = './assets/file/'.$media['file_name'];
					
					$data_session	= $this->session->userdata;
					$Create_By      = $data_session['ORI_User']['username'];
					$Create_Date    = date('Y-m-d H:i:s');
						
					try{
						$inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
						$objReader      = PHPExcel_IOFactory::createReader($inputFileType); 
						$objReader->setReadDataOnly(true);                               
						$objPHPExcel    = $objReader->load($inputFileName);
							
					}catch(Exception $e){
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());                               
					}

					$sheet = $objPHPExcel->getSheet(0);
					$highestRow     = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();
					$Error      = 0;
					$Arr_Keys   = array();
					$Loop       = 0;
					$Total      = 0;
					$Message    = "";
					$Urut       = 0;
					
					
					$intL 		= 0;
					$intError 	= 0;
					$pesan 		= '';
					$status		= '';

					for ($row = 5; $row <= $highestRow; $row++){ 
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
						
						$Urut++;

						$id_part							= (isset($rowData[0][0]) && $rowData[0][0])?$rowData[0][0]:'';
						$ArrDetail[$Urut]['id_part']  		= trim($id_part);

						$id_category						= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:'';
						$ArrDetail[$Urut]['id_category']  	= $id_category;

						
					}
					// print_r($ArrDetail);
					// exit;
				}
			}
		}

		//DETAIL PLUS
		if($_FILES['upload_plus']['name']){
			$exts   = getExtension($_FILES['upload_plus']['name']);
			if(!in_array($exts,array(1=>'xls','xlsx')))
			{
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Invalid file type, Please Upload the Excel format ...'
				);
			}
			else{
				
				$fileName = $_FILES['upload_plus']['name'];
				$this->load->library(array('PHPExcel'));
				$config['upload_path'] = './assets/file/'; 
				$config['file_name'] = $fileName;
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = 10000;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				// echo $fileName; exit;

				if (!$this->upload->do_upload('upload_plus')) {
					$error = array('error' => $this->upload->display_errors());
					$Arr_Kembali		= array(
						'status'		=> 3,
						'pesan'			=> $error['error']
					);
				}
				else{
					// echo 'success!';
					$media = $this->upload->data();
					$inputFileName = './assets/file/'.$media['file_name'];
					
					$data_session	= $this->session->userdata;
					$Create_By      = $data_session['ORI_User']['username'];
					$Create_Date    = date('Y-m-d H:i:s');
						
					try{
						$inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
						$objReader      = PHPExcel_IOFactory::createReader($inputFileType); 
						$objReader->setReadDataOnly(true);                               
						$objPHPExcel    = $objReader->load($inputFileName);
							
					}catch(Exception $e){
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());                               
					}

					$sheet = $objPHPExcel->getSheet(0);
					$highestRow     = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();
					$Error      = 0;
					$Arr_Keys   = array();
					$Loop       = 0;
					$Total      = 0;
					$Message    = "";
					$Urut       = 0;
					
					
					$intL 		= 0;
					$intError 	= 0;
					$pesan 		= '';
					$status		= '';

					for ($row = 5; $row <= $highestRow; $row++){ 
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
						
						$Urut++;

						$id_part							= (isset($rowData[0][0]) && $rowData[0][0])?$rowData[0][0]:'';
						$ArrDetailPlus[$Urut]['id_part']  		= trim($id_part);

						$id_category						= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:'';
						$ArrDetailPlus[$Urut]['id_category']  	= $id_category;

						
					}
					// print_r($ArrDetailPlus);
					// exit;
				}
			}
		}

		//DETAIL ADD
		if($_FILES['upload_add']['name']){
			$exts   = getExtension($_FILES['upload_add']['name']);
			if(!in_array($exts,array(1=>'xls','xlsx')))
			{
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Invalid file type, Please Upload the Excel format ...'
				);
			}
			else{
				
				$fileName = $_FILES['upload_add']['name'];
				$this->load->library(array('PHPExcel'));
				$config['upload_path'] = './assets/file/'; 
				$config['file_name'] = $fileName;
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = 10000;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				// echo $fileName; exit;

				if (!$this->upload->do_upload('upload_add')) {
					$error = array('error' => $this->upload->display_errors());
					$Arr_Kembali		= array(
						'status'		=> 3,
						'pesan'			=> $error['error']
					);
				}
				else{
					// echo 'success!';
					$media = $this->upload->data();
					$inputFileName = './assets/file/'.$media['file_name'];
					
					$data_session	= $this->session->userdata;
					$Create_By      = $data_session['ORI_User']['username'];
					$Create_Date    = date('Y-m-d H:i:s');
						
					try{
						$inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
						$objReader      = PHPExcel_IOFactory::createReader($inputFileType); 
						$objReader->setReadDataOnly(true);                               
						$objPHPExcel    = $objReader->load($inputFileName);
							
					}catch(Exception $e){
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());                               
					}

					$sheet = $objPHPExcel->getSheet(0);
					$highestRow     = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();
					$Error      = 0;
					$Arr_Keys   = array();
					$Loop       = 0;
					$Total      = 0;
					$Message    = "";
					$Urut       = 0;
					
					
					$intL 		= 0;
					$intError 	= 0;
					$pesan 		= '';
					$status		= '';

					for ($row = 5; $row <= $highestRow; $row++){ 
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
						
						$Urut++;

						$id_part							= (isset($rowData[0][0]) && $rowData[0][0])?$rowData[0][0]:'';
						$ArrDetailAdd[$Urut]['id_part']  		= trim($id_part);

						$id_category						= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:'';
						$ArrDetailAdd[$Urut]['id_category']  	= $id_category;

						
					}
					// print_r($ArrDetailAdd);
					// exit;
				}
			}
		}

		print_r($ArrHeader);
		print_r($ArrDetail);
		print_r($ArrDetailPlus);
		print_r($ArrDetailAdd);
		exit;

		$this->db->trans_start();
			if(!empty($ArrHeader)){
				$this->db->delete('upload_component_header');
				$this->db->delete('upload_component_detail');
				$this->db->delete('upload_component_detail_plus');
				$this->db->delete('upload_component_detail_add');

				$this->db->insert_batch('upload_component_header', $ArrHeader);
			}
			if(!empty($ArrDetail)){
				$this->db->insert_batch('upload_component_detail', $ArrDetail);
			}
			if(!empty($ArrDetailPlus)){
				$this->db->insert_batch('upload_component_detail_plus', $ArrDetailPlus);
			}
			if(!empty($ArrDetailAdd)){
				$this->db->insert_batch('upload_component_detail_add', $ArrDetailAdd);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 2,
				'ipp'	=> $no_ipp.'/edit'
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1,
				'ipp'	=> $no_ipp.'/edit'
			);
			history('Upload NON FRP : '.$id_header);
		}

		echo json_encode($Arr_Kembali);
	}

}
?>