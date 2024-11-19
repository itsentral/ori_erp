<?php
class Product_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function costcenter_urut(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			//header
			$id_product 	= $data['id'];
			$product_parent = get_name('product_parent','product_parent','id',$id_product);
			
			$Detail 		= $data['detail'];
			$Ym				= date('ym');

			$ArrDetail	= array();
			$ArrInsert	= array();
			foreach($Detail AS $val => $valx){
				if($valx['costcenter'] <> '0'){
					$costcenter_name	= $this->db->query("SELECT name FROM hris.departments WHERE id='".$valx['costcenter']."' LIMIT 1")->result();
					if(!empty($valx['id'])){
						$ArrDetail[$val]['id'] 				= $valx['id'];
						$ArrDetail[$val]['id_product'] 		= $id_product;
						$ArrDetail[$val]['product_parent'] 	= $product_parent;
						$ArrDetail[$val]['costcenter'] 		= $valx['costcenter'];
						$ArrDetail[$val]['nm_costcenter'] 	= strtolower($costcenter_name[0]->name);
						$ArrDetail[$val]['urut'] 			= str_replace(',','',$valx['urut']);
						$ArrDetail[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
						$ArrDetail[$val]['updated_date'] 	= $dateTime;
					}
					
					if(empty($valx['id'])){
						$ArrInsert[$val]['id_product'] 		= $id_product;
						$ArrInsert[$val]['product_parent'] 	= $product_parent;
						$ArrInsert[$val]['costcenter'] 		= $valx['costcenter'];
						$ArrInsert[$val]['nm_costcenter'] 	= strtolower($costcenter_name[0]->name);
						$ArrInsert[$val]['urut'] 			= str_replace(',','',$valx['urut']);
						$ArrInsert[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
						$ArrInsert[$val]['updated_date'] 	= $dateTime;
					}
				}
			}
			
			// print_r($ArrInsert);
			// print_r($ArrDetail);
			// exit;
			
			$this->db->trans_start();
				if(!empty($ArrDetail)){
					$this->db->update_batch('product_parent_costcenter', $ArrDetail, 'id');
				}
				if(!empty($ArrInsert)){
					$this->db->insert_batch('product_parent_costcenter', $ArrInsert);
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
				history('Update urutan product '.$product_parent);
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
			
			$id = $this->uri->segment(3);
			$product	= $this->db->query("SELECT * FROM hris.departments WHERE division_id='DIV009' ORDER BY name")->result_array();
			$data2 	= NULL;
			if(!empty($id)){
				$sql	= "SELECT * FROM product_parent_costcenter WHERE id_product='".$id."' AND deleted='N'";
				// echo $sql;
				$data2 	= $this->db->query($sql)->result_array();
			}
			
			$data = array(
				'title'		=> 'Edit Urutan <b>'.ucwords(get_name('product_parent','product_parent','id',$id)).'</b>',
				'action'	=> 'add',
				'product'	=> $product,
				'id'		=> $id,
				'data'		=> $data2
			);
			$this->load->view('Product/costcenter_urut',$data);
		}
	}
	
	public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

		$product	= $this->db->query("SELECT * FROM hris.departments WHERE division_id='DIV009' ORDER BY name")->result_array();

		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][costcenter]' class='chosen_select form-control input-sm inline-blockd'>";
				$d_Header .= "<option value='0'>Select Costcenter</option>";
				foreach($product AS $val => $valx){
				  $d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['name'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			
			
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][urut]' class='form-control input-sm text-center maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			$d_Header .= "<td align='center'>";
				$d_Header .= "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left' colspan='3'><button type='button' class='btn btn-sm btn-warning addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Costcenter</button></td>";
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
		$id 			= $this->uri->segment(3);
		$product 		= $this->uri->segment(4);
		
		$ArrDelete	= array(
			'deleted' => 'Y',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		);
		

		// print_r($ArrDelete);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id',$id);
			$this->db->update('product_parent_costcenter', $ArrDelete);
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Insert data failed. Please try again later ...',
				'status'	=> 0,
				'uri'		=> $product
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Insert data success. Thanks ...',
				'status'	=> 1,
				'uri'		=> $product
			);
			history('Delete coctenter urutan '.$id);
		}
		echo json_encode($Arr_Kembali);
	}
	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================

	//PRODUCT
	public function get_json_product(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_product(
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
			$nestedData[]	= "<div align='left'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($last_date))."</div>";

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

	public function get_query_json_product($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

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

    //PRODUCT
	public function get_json_type(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_type(
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
			
			$numQTY = $this->db->query("SELECT * FROM product_parent_costcenter WHERE id_product='".$row['id']."' AND deleted='N'")->num_rows();

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
            $nestedData[]	= "<div align='left'>".ucwords(strtolower($row['product_parent']))."</div>";
            $nestedData[]	= "<div align='left'>".ucwords(strtolower($row['type2']))."</div>";

			$TYPE_ = ($row['type'] == 'field')?'material only':$row['type'];

            $nestedData[]	= "<div align='left'>".ucwords(strtolower($TYPE_))."</div>";
            $nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode']))."</div>";
            $value2 = "Product Slongsong";
			$color2 = "bg-green";
			if($row['ket'] == 'Y'){
				$value2 = "Product";
				$color2 = "bg-blue";
			}
			// $nestedData[]	= "<div align='center'><span class='badge ".$color2." '>".$value2."</span></div>";
			$value = "Yes";
			$color = "bg-green";
			if($row['estimasi'] == 'N'){
				$value = "No";
				$color = "bg-red";
			}
			// $nestedData[]	= "<div align='center'><span class='badge ".$color." '>".$value."</span></div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['spec1']))."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['spec2']))."</div>";
			$nestedData[]	= "<div align='center'>".$numQTY."</div>";
			$last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$nestedData[]	= "<div align='left'>".strtolower($last_create)."</div>";

			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			$nestedData[]	= "<div align='right'>".date('d-F-Y H:i:s', strtotime($last_date))."</div>";

			$edit	= "";

			if($Arr_Akses['update']=='1' AND $row['type2'] == 'custom'){
				$edit	= "<a href='".site_url($this->uri->segment(1)).'/costcenter_urut/'.$row['id']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}
			$nestedData[]	= "<div align='center'>
								".$edit."
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

	public function get_query_json_type($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
                product_parent a,
                (SELECT @row:=0) r 
            WHERE 
                1=1 AND a.deleted='N' AND (
                a.product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.type2 LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'product_parent',
            2 => 'type2',
            3 => 'type',
            4 => 'kode',
            5 => 'ket',
            6 => 'est'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }

}
