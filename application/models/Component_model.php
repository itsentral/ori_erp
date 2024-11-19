<?php
class Component_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function index_component(){
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


		$data = array(
			'title'			=> 'Indeks Of Estimation',
			'action'		=> 'index',
			'listseries'	=> $getSeries,
			'listkomponen'	=> $getKomp,
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history("View Master ".$productN);
		$this->load->view('Component/index',$data);
	}
	

	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	
	public function get_data_json_component(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_component(
			$requestData['series'],
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

			$nestedData 	= array();
				$detail = "";
				if(strtolower($row['parent_product']) == 'pipe'){
					$detail = "(".$row['diameter']." x ".$row['panjang']." x ".floatval($row['design']).")";
				}
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
				$kode1 = substr($row['id_product'], 0,3);
				$kode2 = substr($row['id_product'], 8,6);
			$nestedData[]	= "<div align='left'>".$kode1.$row['series'].$kode2."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['parent_product']))."</div>";
			$nestedData[]	= "<div align='left'>".spec_master($row['id_product'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['stiffness']."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['criminal_barier']))."</div>";
			//$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['vacum_rate']))."</div>";
			$nestedData[]	= "<div align='center'>".ucfirst(strtolower($row['created_by']))."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['rev']."</span></div>";
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['created_date']))."</div>";
			// $beratnya =  get_weight_comp($row['id_product'], $row['series'], $row['parent_product'], $row['diameter'], $row['diameter2'])['weight'];
			
			$WEIGHT = (!empty($row['berat'])) ? $row['berat'] : get_berat_est($row['id_product']);
			$WEIGHT_JOINT_RESIN = 0;
			
			// if($row['parent_product'] == 'shop joint' OR $row['parent_product'] == 'branch joint' OR $row['parent_product'] == 'field joint'){
			// 	$get_joint = $this->db->select('SUM(material_weight) AS berat')->get_where('component_detail', array('id_product'=>$row['id_product'], 'id_category'=>'TYP-0001', 'id_material <>'=>'MTL-1903000'))->result();
			// 	$get_joint2 = $this->db->select('MAX(material_weight) AS berat')->get_where('component_detail', array('id_product'=>$row['id_product'], 'id_category'=>'TYP-0001', 'id_material <>'=>'MTL-1903000'))->result();
			// 	$WEIGHT_JOINT_RESIN = $get_joint[0]->berat - $get_joint2[0]->berat;
			// }
			
			
			$nestedData[]	= "<div align='right'>".number_format($WEIGHT + $WEIGHT_JOINT_RESIN,3)."</div>";
			// $nestedData[]	= "<div align='right'>".get_weight_comp($row['id_product'], $row['series'], $row['parent_product'], $row['diameter'], $row['diameter2'])['sql']."</div>";
			
				if($row['status'] == 'WAITING APPROVAL'){
					$class	= 'bg-orange';
				}
				elseif($row['status'] == 'APPROVED'){
					$class	= 'bg-green';
				}
				else{
					$class	= 'bg-red';
				}
				$check_default = $this->db->query("SELECT * FROM component_default WHERE id_product = '".$row['id_product']."' ")->num_rows();
				$cust_default 	= $this->db->query("SELECT * FROM product_parent WHERE default_set = 'setting' AND product_parent = '".$row['parent_product']."' ")->num_rows();
				$sts_plus = "";
				if($check_default < 1 AND $cust_default > 0){
					$sts_plus = "<br><span class='badge bg-red'>Please Save Default</span>";
				}
				
			$nestedData[]	= "<div align='left'><span class='badge ".$class."'>".ucwords(strtolower($row['status']))."</span>".$sts_plus."</div>";
				
				$Upd = "";
				$Upd2 = "";
				$Del = "";
				if($Arr_Akses['update']=='1'){
					if($row['parent_product'] == 'pipe'){
						$Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/pipe_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'end cap'){
						$Upd = "&nbsp;<a href='".site_url('edit_standart/end_cap_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'blind flange'){
						$Upd = "&nbsp;<a href='".site_url('edit_standart/blind_flange_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'pipe slongsong'){
						$Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/pipe_slongsong_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'elbow mould'){
						$Upd = "&nbsp;<a href='".site_url('edit_standart/elbow_mould_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'elbow mitter'){
						// $Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/elbow_mitter_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
						$Upd = "&nbsp;<a href='".site_url('edit_standart/elbow_mitter_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'eccentric reducer'){
						// $Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/eccentric_reducer_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
						$Upd2 = "&nbsp;<a href='".site_url('edit_standart/eccentric_reducer_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'concentric reducer'){
						// $Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/concentric_reducer_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
						$Upd2 = "&nbsp;<a href='".site_url('edit_standart/concentric_reducer_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'equal tee mould'){
						$Upd = "&nbsp;<a href='".site_url('edit_standart/equal_tee_mould_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'equal tee slongsong'){
						$Upd = "&nbsp;<a href='".site_url('edit_standart/equal_tee_slongsong_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'reducer tee mould'){
						$Upd = "&nbsp;<a href='".site_url('edit_standart/reducer_tee_mould_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'reducer tee slongsong'){
						$Upd = "&nbsp;<a href='".site_url('edit_standart/reducer_tee_slongsong_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'flange mould' AND  $row['created_date'] >= '2019-08-22 09:32:52'){
						// $Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/flange_mould_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
						$Upd2 = "&nbsp;<a href='".site_url('edit_standart/flange_mould_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'flange slongsong' AND  $row['created_date'] >= '2019-08-22 09:32:52'){
						// $Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/flange_slongsong_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
						$Upd2 = "&nbsp;<a href='".site_url('edit_standart/flange_slongsong_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'colar'){
						$Upd2 = "&nbsp;<a href='".site_url('edit_standart/colar_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'colar slongsong'){
						$Upd2 = "&nbsp;<a href='".site_url('edit_standart/colar_slongsong_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($row['parent_product'] == 'branch joint' OR $row['parent_product'] == 'shop joint' OR $row['parent_product'] == 'field joint'){
						// $Upd = "&nbsp;<a href='".site_url($this->uri->segment(1).'/flange_slongsong_edit/'.$row['id_product'])."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
						$Upd2 = "&nbsp;<a href='".site_url('edit_joint/edit/'.$row['id_product'].'/standart')."' class='btn btn-sm btn-primary' title='Edit Estimation' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
				}
				if($Arr_Akses['delete']=='1'){
					if($row['status'] == 'WAITING APPROVAL'){
						$Del = "&nbsp;<button id='del_type' data-idcategory='".$row['id_product']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></button>";
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

	public function query_data_json_component($series, $komponen, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		// echo $series."<br>";
		// echo $komponen."<br>";
		$where_series = "";
		if(!empty($series)){
			$where_series = " AND a.series = '".$series."' ";
		}

		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND a.parent_product = '".$komponen."' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*, 
				b.nm_customer
			FROM
				component_header a
				LEFT JOIN customer b ON b.id_customer=a.standart_by,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_series."
				".$where_komponen."
				AND a.deleted ='N' AND (a.cust IS NULL OR a.cust = '') 
			AND (
				a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
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
			2 => 'series',
			3 => 'nm_product',
			4 => 'standart_toleransi',
			5 => 'aplikasi_product',
			6 => 'created_by',
			7 => 'rev'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
}
