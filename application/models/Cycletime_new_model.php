<?php
class Cycletime_new_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function get_list_order($table, $order_by){
		$query = $this->db->query("SELECT * FROM $table ORDER BY $order_by ASC")->result_array();
		return $query;
	}

	public function get_list_where_order($table, $field_where, $value_where, $order_by){
		$query = $this->db->query("SELECT * FROM $table WHERE $field_where = '".$value_where."' ORDER BY $order_by ASC")->result_array();
		return $query;
	}
	
	//==========================================================================================================================
	//=======================================================CYCLETIME==========================================================
	//==========================================================================================================================
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Cycletime',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Master Cycletime');
		$this->load->view('Cycletime_new/index',$data);
    }
	
	
	public function view_cycletime2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('users'));
		}
		
		$uri 	= $this->uri->segment(3);

		$get 	= $this->db->query("SELECT * FROM cycletime WHERE id='".$uri."' ")->result();
		$sql	= "SELECT * FROM cycletime WHERE id_costcenter='".$get[0]->id_costcenter."' AND product='".$get[0]->product."' AND liner='".$get[0]->liner."' AND pn='".$get[0]->pn."' AND deleted = 'N' ORDER BY dn1 ASC, dn2 ASC, sudut ASC, srlr ASC";
		$data 	= $this->db->query($sql)->result_array();
		// echo $sql;
		$costcenter	= $this->db->query("SELECT * FROM costcenter WHERE deleted ='N' ORDER BY nm_costcenter ASC")->result_array();
	
		$data = array(
			'title'		=> 'View Cycletime',
			'action'	=> 'view_cycletime',
			'costcenter'=> $costcenter,
			'get'		=> $get,
			'data'		=> $data
		);
		$this->load->view('Cycletime_new/view_cycletime2',$data);
	}
	
	public function add_cycletime2(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			//header
			$id_costcenter 	= $data['id_costcenter'];
			$Detail 		= $data['detail'];
			$Ym				= date('ym');

			$ArrDetail	= array();
			$ArrInsert	= array();
			foreach($Detail AS $val => $valx){
				if(!empty($valx['product'])){
					if($valx['product'] != '0' AND !empty($valx['dn1']) AND $valx['pn'] != '0' AND $valx['liner'] != '0' ){
						$man_power		= str_replace(',','',$valx['man_power']);
						$time_process	= str_replace(',','',$valx['time_process']);
						$curing_time	= str_replace(',','',$valx['curing_time']);
						$total_time		= $time_process + $curing_time;
						
						if(!empty($valx['id'])){
							$ArrDetail[$val]['id'] 				= $valx['id'];
							$ArrDetail[$val]['id_costcenter'] 	= $id_costcenter;
							$ArrDetail[$val]['product'] 		= $valx['product'];
							$ArrDetail[$val]['dn1'] 			= str_replace(',','',$valx['dn1']);
							$ArrDetail[$val]['dn2'] 			= str_replace(',','',$valx['dn2']);
							$ArrDetail[$val]['sudut'] 			= str_replace(',','',$valx['sudut']);
							$ArrDetail[$val]['srlr'] 			= $valx['srlr'];
							$ArrDetail[$val]['pn'] 				= $valx['pn'];
							$ArrDetail[$val]['liner'] 			= $valx['liner'];
							$ArrDetail[$val]['man_power'] 		= $man_power;
							$ArrDetail[$val]['mesin'] 			= $valx['mesin'];
							$ArrDetail[$val]['time_process']	= $time_process;
							$ArrDetail[$val]['curing_time'] 	= $curing_time;
							$ArrDetail[$val]['total_time'] 		= $total_time;
							$ArrDetail[$val]['man_hours'] 		= $total_time * $man_power;
							$ArrDetail[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
							$ArrDetail[$val]['updated_date'] 	= $dateTime;
						}
						
						if(empty($valx['id'])){
							$ArrInsert[$val]['id_costcenter'] 	= $id_costcenter;
							$ArrInsert[$val]['product'] 		= $valx['product'];
							$ArrInsert[$val]['dn1'] 			= str_replace(',','',$valx['dn1']);
							$ArrInsert[$val]['dn2'] 			= str_replace(',','',$valx['dn2']);
							$ArrInsert[$val]['sudut'] 			= str_replace(',','',$valx['sudut']);
							$ArrInsert[$val]['srlr'] 			= $valx['srlr'];
							$ArrInsert[$val]['pn'] 				= $valx['pn'];
							$ArrInsert[$val]['liner'] 			= $valx['liner'];
							$ArrInsert[$val]['man_power'] 		= $man_power;
							$ArrInsert[$val]['mesin'] 			= $valx['mesin'];
							$ArrInsert[$val]['time_process']	= $time_process;
							$ArrInsert[$val]['curing_time'] 	= $curing_time;
							$ArrInsert[$val]['total_time'] 		= $total_time;
							$ArrInsert[$val]['man_hours'] 		= $total_time * $man_power;
							$ArrInsert[$val]['created_by'] 		= $data_session['ORI_User']['username'];
							$ArrInsert[$val]['created_date'] 	= $dateTime;
						}
						
					}
				}
			}
			
			// print_r($ArrInsert);
			// print_r($ArrDetail);
			// exit;
			
			$this->db->trans_start();
				if(!empty($ArrDetail)){
					$this->db->update_batch('cycletime', $ArrDetail, 'id');
				}
				if(!empty($ArrInsert)){
					$this->db->insert_batch('cycletime', $ArrInsert);
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('Insert cycletime '.$id_costcenter);
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
			
			$uri 	= $this->uri->segment(3);
			$view 	= $this->uri->segment(4);
			$get 	= NULL;
			$data 	= NULL;
			if(!empty($uri)){
				$get 	= $this->db->query("SELECT * FROM cycletime WHERE id='".$uri."' ")->result();
				$sql	= "SELECT * FROM cycletime WHERE id_costcenter='".$get[0]->id_costcenter."' AND product='".$get[0]->product."' AND liner='".$get[0]->liner."' AND pn='".$get[0]->pn."' AND deleted = 'N' ORDER BY dn1 ASC, dn2 ASC, sudut ASC, srlr ASC";
				$data 	= $this->db->query($sql)->result_array();
			}
			// echo $sql;
			$costcenter	= $this->db->query("SELECT * FROM costcenter WHERE deleted ='N' ORDER BY nm_costcenter ASC")->result_array();
			$product	= $this->cycletime_new_model->get_list_order('product_parent','product_parent');
			$pn			= $this->cycletime_new_model->get_list_where_order('list_help','group_by','pressure','urut');
			$liner		= $this->cycletime_new_model->get_list_where_order('list_help','group_by','liner','urut');
			$mesin		= $this->cycletime_new_model->get_list_where_order('machine','sts_mesin','Y','no_mesin');
		
			$data = array(
				'title'		=> 'Add Cycletime',
				'action'	=> 'add_cycletime',
				'costcenter'=> $costcenter,
				'get'		=> $get,
				'data'		=> $data,
				'product'	=> $product,
				'pn'		=> $pn,
				'liner'		=> $liner,
				'mesin'		=> $mesin,
				'uri'		=> $uri,
				'view'		=> $view
			);
			$this->load->view('Cycletime_new/add_cycletime2',$data);
		}
	}
	
	public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

		$product	= $this->cycletime_new_model->get_list_order('product_parent','product_parent');
		$pn			= $this->cycletime_new_model->get_list_where_order('list_help','group_by','pressure','urut');
		$liner		= $this->cycletime_new_model->get_list_where_order('list_help','group_by','liner','urut');
		$mesin		= $this->cycletime_new_model->get_list_where_order('machine','sts_mesin','Y','no_mesin');
    	
		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][product]' class='chosen_select form-control input-sm inline-blockd'>";
				$d_Header .= "<option value='0'>Select Product</option>";
				foreach($product AS $val => $valx){
				  $d_Header .= "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][dn1]' class='form-control input-md text-right maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][dn2]' class='form-control input-md text-right maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][sudut]' class='form-control input-md text-center autoNumeric'></td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][srlr]' class='chosen_select form-control input-sm inline-blockd'>";
				$d_Header .= "<option value='0'>Select</option>";
				$d_Header .= "<option value='SR'>SR</option>";
				$d_Header .= "<option value='LR'>LR</option>";
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][pn]' class='chosen_select form-control input-sm inline-blockd'>";
				$d_Header .= "<option value='0'>Select</option>";
				foreach($pn AS $val => $valx){
				  $d_Header .= "<option value='".$valx['name']."'>PN ".strtoupper($valx['name'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][liner]' class='chosen_select form-control input-sm inline-blockd'>";
				$d_Header .= "<option value='0'>Select</option>";
				foreach($liner AS $val => $valx){
				  $d_Header .= "<option value='".$valx['name']."'>".strtoupper($valx['name'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][man_power]' class='form-control input-md text-center maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][mesin]' class='chosen_select form-control input-sm inline-blockd'>";
				$d_Header .= "<option value='0'>Select</option>";
				foreach($mesin AS $val => $valx){
				  $d_Header .= "<option value='".$valx['no_mesin']."'>".strtoupper($valx['nm_mesin'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][time_process]' class='form-control input-sm text-right autoNumeric'></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][curing_time]' class='form-control input-sm text-right autoNumeric'></td>";
			$d_Header .= "<td align='center'>";
				$d_Header .= "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left'><button type='button' class='btn btn-sm btn-warning addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Product</button></td>";
			$d_Header .= "<td align='center' colspan='11'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}
	
	public function delete_permanent(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		// print_r($data); exit;
		//header
		$id 			= $data['id'];
		$id_costcenter 	= $data['id_costcenter'];
		$liner 			= $data['liner'];
		$pn 			= $data['pn'];
		$product 		= $data['product'];
		
		$sql	= "SELECT * FROM cycletime WHERE id_costcenter='".$id_costcenter."' AND product='".$product."' AND liner='".$liner."' AND pn='".$pn."' LIMIT 1";
		$data2 	= $this->db->query($sql)->result();
		$uri	= (!empty($data2))?$data2[0]->id:'';
		
		$ArrDelete	= array(
			'deleted' => 'Y',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		);
		

		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id',$id);
			$this->db->update('cycletime', $ArrDelete);
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Insert data failed. Please try again later ...',
				'status'	=> 0,
				'uri'		=> $uri
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Insert data success. Thanks ...',
				'status'	=> 1,
				'uri'		=> $uri
			);
			history('Delete cycletime '.$id);
		}
		echo json_encode($Arr_Kembali);
	}
	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================

	//PROCESS
	public function get_json_process(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_process(
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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_process']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['keterangan']))."</div>";

			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($last_date))."</div>";

                $detail		= "";
                $edit		= "";
                $delete		= "";

                if($Arr_Akses['delete']=='1'){
                    $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['code_process']."'><i class='fa fa-trash'></i></button>";
                }

                if($Arr_Akses['update']=='1'){
                    $edit	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['code_process']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button>";
                }
                $nestedData[]	= "<div align='left'>
                                    ".$edit."
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

	public function get_query_json_process($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                process a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.code_process LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_process LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_process',
			2 => 'keterangan'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }
    
    //COSTCENTER
    public function get_json_costcenter(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/costcenter';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_costcenter(
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

            $s1 = ($row['shift1'] == 'Y')?'blue':'red';
            $s2 = ($row['shift2'] == 'Y')?'blue':'red';
            $s3 = ($row['shift3'] == 'Y')?'blue':'red';

            $sx1 = ($row['shift1'] == 'Y')?'Yes':'No';
            $sx2 = ($row['shift2'] == 'Y')?'Yes':'No';
            $sx3 = ($row['shift3'] == 'Y')?'Yes':'No';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_costcenter']))."</div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-".$s1."'>".strtoupper(strtolower($row['mp_1']))."</span></div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-".$s2."'>".strtoupper(strtolower($row['mp_2']))."</span></div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-".$s3."'>".strtoupper(strtolower($row['mp_3']))."</span></div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-".$s1."'>".strtoupper(strtolower($sx1))."</span></div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-".$s2."'>".strtoupper(strtolower($sx2))."</span></div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-".$s3."'>".strtoupper(strtolower($sx3))."</span></div>";


			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($last_date))."</div>";

                $detail		= "";
                $edit		= "";
                $delete		= "";

                if($Arr_Akses['delete']=='1'){
                    $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id_costcenter']."'><i class='fa fa-trash'></i></button>";
                }

                if($Arr_Akses['update']=='1'){
                    $edit	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['id_costcenter']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button>";
                }
                $nestedData[]	= "<div align='left'>
                                    ".$edit."
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

	public function get_query_json_costcenter($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                costcenter a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.nm_costcenter LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.updated_by LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_costcenter',
            2 => 'mp_1',
            3 => 'mp_2',
            4 => 'mp_3',
            5 => 'shift1',
            6 => 'shift2',
            7 => 'shift3'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//SHIFT
    public function get_json_shift(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_shift(
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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['day']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_type']))."</div>";
			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['start_work']))."</div>";
			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['done_work']))."</div>";

			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['start_break_1']))."</div>";
			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['done_break_1']))."</div>";
			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['start_break_2']))."</div>";
			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['done_break_2']))."</div>";
			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['start_break_3']))."</div>";
			$nestedData[]	= "<div align='center'>".date('H:i',strtotime($row['done_break_3']))."</div>";

			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($last_date))."</div>";

                $detail		= "";
                $edit		= "";
                $delete		= "";

                if($Arr_Akses['delete']=='1'){
                    $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id_shift']."'><i class='fa fa-trash'></i></button>";
                }

                if($Arr_Akses['update']=='1'){
                    $edit	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['id_shift']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button>";
                }
                $nestedData[]	= "<div align='left'>
                                    ".$edit."
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

	public function get_query_json_shift($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                shift a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.nm_shift LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.updated_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.day LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'day',
            2 => 'nm_type',
            3 => 'start_work',
            4 => 'done_work'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//CYCLETIME
    public function get_json_cycletime(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_cycletime(
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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_costcenter']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['product']))."</div>";
			$nestedData[]	= "<div align='center'>PN ".strtoupper(strtolower($row['pn']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['liner']))."</div>";
			
			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";
				
				$detail		= "";
                $delete		= "";
                $edit		= "";
                $detail	= "&nbsp;<a href=".base_url('cycletime_new/view_cycletime2/'.$row['id'])." class='btn btn-sm btn-warning' title='Edit Data' data-role='qtip'><i class='fa fa-eye'></i></a>";
                
                if($Arr_Akses['delete']=='1'){
                    // $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-id='".$row['id']."'><i class='fa fa-trash'></i></button>";
                }

                if($Arr_Akses['update']=='1'){
                    $edit	= "&nbsp;<a href=".base_url('cycletime_new/add_cycletime2/'.$row['id'])." class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
                }
				$nestedData[]	= "<div align='center'>
									".$detail."
                                    ".$edit."
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

	public function get_query_json_cycletime($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				b.nm_costcenter
			FROM
                cycletime a LEFT JOIN costcenter b ON a.id_costcenter = b.id,
                (SELECT @row:=0) r 
            WHERE 
                1=1 
				AND a.deleted='N' 
				AND (
					a.id_costcenter LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.pn LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.liner LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_costcenter LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
			GROUP BY
				a.liner,
				a.pn,
				a.product,
				a.id_costcenter
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_costcenter',
			2 => 'product',
			3 => 'pn',
			4 => 'liner'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//DEPARTMENT
	public function get_json_department(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/department';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_department(
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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_dept']))."</div>";
			$value = "Active";
			$color = "bg-green";
			if($row['status'] == 'N'){
				$value = "Not Active";
				$color = "bg-red";
			}
			$nestedData[]	= "<div align='center'><span class='badge ".$color." '>".$value."</span></div>";

			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($last_date))."</div>";

                $detail		= "";
                $edit		= "";
                $delete		= "";

                if($Arr_Akses['delete']=='1'){
                    $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id']."'><i class='fa fa-trash'></i></button>";
                }

                if($Arr_Akses['update']=='1'){
                    $edit	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit' data-code='".$row['id']."' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></button>";
                }
                $nestedData[]	= "<div align='left'>
                                    ".$edit."
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

	public function get_query_json_department($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                department a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.nm_dept LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.updated_by LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_dept'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }

}
