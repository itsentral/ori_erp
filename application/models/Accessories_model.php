<?php

class Accessories_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		// Your own constructor code
	}

	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$data_session	= $this->session->userdata;
		$get_tab = $this->db->select('keterangan')->order_by('id', 'DESC')->limit('1')->get_where('laporan_status', array('insert_by'=>$data_session['ORI_User']['username']))->result();
        $value1 = (!empty($get_tab))?$get_tab[0]->keterangan:'bolt nut';
		
		$name_baut 		= $this->db->group_by('nama')->get_where('accessories', array('category'=>'1','deleted'=>'N'))->result_array();
		$brand_baut 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'1','deleted'=>'N','material <>'=>''))->result_array();
		
		$name_plate 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'2','deleted'=>'N'))->result_array();
		$brand_plate 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'2','deleted'=>'N','material <>'=>''))->result_array();
		
		$name_gasket 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'3','deleted'=>'N'))->result_array();
		$brand_gasket 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'3','deleted'=>'N','material <>'=>''))->result_array();
		
		$name_lainnya 	= $this->db->group_by('nama')->get_where('accessories', array('category'=>'4','deleted'=>'N'))->result_array();
		$brand_lainnya 	= $this->db->group_by('material')->get_where('accessories', array('category'=>'4','deleted'=>'N','material <>'=>''))->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Accessories',
			'action'		=> 'index',
			'data_session'	=> $data_session,
			'value1'		=> $value1,
			'akses_menu'	=> $Arr_Akses,
			'name_baut'		=> $name_baut,
			'brand_baut'		=> $brand_baut,
			'name_plate'		=> $name_plate,
			'brand_plate'		=> $brand_plate,
			'name_gasket'		=> $name_gasket,
			'brand_gasket'		=> $brand_gasket,
			'name_lainnya'		=> $name_lainnya,
			'brand_lainnya'		=> $brand_lainnya
		);
		history('View data accessories');
		$this->load->view('Accessories/index',$data);
	}
	
	
	//Bolt & Nut
	public function get_json_bold_nut(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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

		$SesFilter = array(
			'nama' => $requestData['nama'],
			'brand' => $requestData['brand']
		);
		$_SESSION['JSON_Filter_BOLD'] = $SesFilter;

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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['panjang'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['radius'],2)."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['harga'],2)."</div>";
			// 	$last_by 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			// 	$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			// $nestedData[]	= "<div align='center'>".strtolower($last_by)."</div>";
			// $nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";


				$edit	= "";
				$delete	= "";
				// if($Arr_Akses['update']=='1'){
				// 	$edit	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/add_bold_nut/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				// }
				if($Arr_Akses['delete']=='1'){
					$delete	= "&nbsp;<button class='btn btn-sm btn-danger deleted' title='Delete Data' data-id='".$row['id']."'><i class='fa fa-trash'></i></button>";
				}
			$nestedData[]	= "<div align='center'>".$edit.$delete."</div>";
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
	
	public function add_bold_nut(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;
			
			$id 				= $data['id'];
			$category 			= $data['category'];
			$nama 				= strtolower(trim($data['nama']));
			$id_material 		= strtolower(trim($data['id_material']));
			$diameter 			= str_replace(',','',$data['diameter']);
			$panjang 			= str_replace(',','',$data['panjang']);
			$radius 			= str_replace(',','',$data['radius']);
			$material 			= strtolower(trim($data['material']));
			$satuan 			= $data['satuan'];
			$standart 			= strtolower(trim($data['standart']));
			$keterangan 		= strtolower(trim($data['keterangan']));
			// $harga 				= str_replace(',','',$data['harga']);
			
			$tanda_edit 		= $data['tanda_edit'];
			
			if(empty($tanda_edit)){
				$Hist = 'Add ';
				$create = 'created_by';
				$times = 'created_date';
			}
			
			if(!empty($tanda_edit)){
				$Hist = 'Edit ';
				$create = 'updated_by';
				$times = 'updated_date';
			}
			
			
			$ArrInsert = array(
				'category' => $category,
				'nama' => $nama,
				'id_material' => $id_material,
				'diameter' => $diameter,
				'panjang' => $panjang,
				'radius' => $radius,
				'material' => $material,
				'satuan' => $satuan,
				'standart' => $standart,
				'keterangan' => $keterangan,
				// 'harga' => $harga,
				$create => $data_session['ORI_User']['username'],
				$times => $dateTime
			);
			
			$get_id = $this->db->select('MAX(id) AS id_max')->get('accessories')->result();
			
			$ArrStock = array(
				'id_acc' 	=> $get_id[0]->id_max + 1,
				'gudang' 	=> '11',
				'stock' 	=> 0,
				'rusak' 	=> 0,
				'update_by' => $data_session['ORI_User']['username'],
				'update_date' => $dateTime
			);
			// $this->db->insert('warehouse_acc_stock', $ArrStock);
			
			$this->db->trans_start();
				if(empty($tanda_edit)){
					$this->db->insert('accessories', $ArrInsert);
					$this->db->insert('warehouse_acc_stock', $ArrStock);
				}
				
				if(!empty($tanda_edit)){
					$this->db->where('id', $id);
					$this->db->update('accessories', $ArrInsert);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data success. Thanks ...',
					'status'	=> 1
				);
				history($Hist.'accessories bolt & nut '.$id);
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

			$tanda1 = 'Add';
			if(!empty($id)){
				$tanda1 = 'Edit';
			}

			$header 	= $this->db->get_where('accessories', array('id'=>$id))->result();
			$satuan		= $this->db->order_by('kode_satuan','ASC')->get_where('raw_pieces', array('flag_active'=>'Y','delete'=>'N'))->result_array();
			$category 	= $this->db->get_where('accessories_category', array('id'=>'1'))->result_array();
			
			$data_session	= $this->session->userdata;
			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab accessories',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'bolt nut'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			
			$data = array(
				'title'		=> $tanda1.' Bolt & Nut',
				'action'	=> 'add',
				'header'	=> $header,
				'category_l'=> $category,
				'satuan_l'	=> $satuan
			);
			$this->load->view('Accessories/add_bold_nut',$data);
		}
	}
	
	//Plate
	public function get_json_plate(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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

		$SesFilter = array(
			'nama' => $requestData['nama'],
			'brand' => $requestData['brand']
		);
		$_SESSION['JSON_Filter_PLATE'] = $SesFilter;

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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['thickness'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['density'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['harga'],2)."</div>";
			// 	$last_by 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			// 	$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			// $nestedData[]	= "<div align='center'>".strtolower($last_by)."</div>";
			// $nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";


				$edit	= "";
				$delete	= "";
				// if($Arr_Akses['update']=='1'){
				// 	$edit	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/add_plate/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				// }
				if($Arr_Akses['delete']=='1'){
					$delete	= "&nbsp;<button class='btn btn-sm btn-danger deleted' title='Delete Data' data-id='".$row['id']."'><i class='fa fa-trash'></i></button>";
				}
			$nestedData[]	= "<div align='center'>".$edit.$delete."</div>";
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
	
	public function add_plate(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;
			
			$id 				= $data['id'];
			$category 			= $data['category'];
			$id_material 		= strtolower(trim($data['id_material']));
			$nama 				= strtolower(trim($data['nama']));
			$thickness 			= str_replace(',','',$data['thickness']);
			$density 			= str_replace(',','',$data['density']);
			$material 			= strtolower(trim($data['material']));
			$satuan 			= $data['satuan'];
			$standart 			= strtolower(trim($data['standart']));
			$ukuran_standart 	= strtolower(trim($data['ukuran_standart']));
			$keterangan 		= strtolower(trim($data['keterangan']));
			// $harga 				= str_replace(',','',$data['harga']);
			
			$tanda_edit 		= $data['tanda_edit'];
			
			if(empty($tanda_edit)){
				$Hist = 'Add ';
				$create = 'created_by';
				$times = 'created_date';
			}
			
			if(!empty($tanda_edit)){
				$Hist = 'Edit ';
				$create = 'updated_by';
				$times = 'updated_date';
			}
			
			
			$ArrInsert = array(
				'category' => $category,
				'nama' => $nama,
				'id_material' => $id_material,
				'thickness' => $thickness,
				'density' => $density,
				'ukuran_standart' => $ukuran_standart,
				'material' => $material,
				'satuan' => $satuan,
				'standart' => $standart,
				'keterangan' => $keterangan,
				// 'harga' => $harga,
				$create => $data_session['ORI_User']['username'],
				$times => $dateTime
			);
			
			$get_id = $this->db->select('MAX(id) AS id_max')->get('accessories')->result();
			
			$ArrStock = array(
				'id_acc' 	=> $get_id[0]->id_max + 1,
				'gudang' 	=> '11',
				'stock' 	=> 0,
				'rusak' 	=> 0,
				'update_by' => $data_session['ORI_User']['username'],
				'update_date' => $dateTime
			);
			// $this->db->insert('warehouse_acc_stock', $ArrStock);
			
			$this->db->trans_start();
				if(empty($tanda_edit)){
					$this->db->insert('accessories', $ArrInsert);
					$this->db->insert('warehouse_acc_stock', $ArrStock);
				}
				
				if(!empty($tanda_edit)){
					$this->db->where('id', $id);
					$this->db->update('accessories', $ArrInsert);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data success. Thanks ...',
					'status'	=> 1
				);
				history($Hist.'accessories plate '.$id);
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

			$tanda1 = 'Add';
			if(!empty($id)){
				$tanda1 = 'Edit';
			}

			$header 	= $this->db->get_where('accessories', array('id'=>$id))->result();
			$satuan		= $this->db->order_by('kode_satuan','ASC')->or_where(array('id_satuan'=>'3'))->or_where(array('id_satuan'=>'20'))->or_where(array('id_satuan'=>'1'))->get_where('raw_pieces', array('flag_active'=>'Y','delete'=>'N'))->result_array();
			$category 	= $this->db->get_where('accessories_category', array('id'=>'2'))->result_array();
			
			$data_session	= $this->session->userdata;
			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab accessories',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'plate'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'		=> $tanda1.' Plate',
				'action'	=> 'add',
				'header'	=> $header,
				'category_l'=> $category,
				'satuan_l'	=> $satuan
			);
			$this->load->view('Accessories/add_plate',$data);
		}
	}
	
	//Gasket
	public function get_json_gasket(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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

		$SesFilter = array(
			'nama' => $requestData['nama'],
			'brand' => $requestData['brand']
		);
		$_SESSION['JSON_Filter_GASKET'] = $SesFilter;

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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']." ".$row['dimensi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['thickness'],2)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['harga'],2)."</div>";
			// 	$last_by 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			// 	$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			// $nestedData[]	= "<div align='center'>".strtolower($last_by)."</div>";
			// $nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";


				$edit	= "";
				$delete	= "";
				// if($Arr_Akses['update']=='1'){
				// 	$edit	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/add_gasket/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				// }
				if($Arr_Akses['delete']=='1'){
					$delete	= "&nbsp;<button class='btn btn-sm btn-danger deleted' title='Delete Data' data-id='".$row['id']."'><i class='fa fa-trash'></i></button>";
				}
			$nestedData[]	= "<div align='center'>".$edit.$delete."</div>";
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
	
	public function add_gasket(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;
			
			$id 				= $data['id'];
			$category 			= $data['category'];
			$id_material 		= strtolower(trim($data['id_material']));
			$nama 				= strtolower(trim($data['nama']));
			$dimensi 			= strtolower(trim($data['dimensi']));
			$thickness 			= str_replace(',','',$data['thickness']);
			$material 			= strtolower(trim($data['material']));
			$satuan 			= $data['satuan'];
			$standart 			= strtolower(trim($data['standart']));
			$ukuran_standart 	= strtolower(trim($data['ukuran_standart']));
			$keterangan 		= strtolower(trim($data['keterangan']));
			// $harga 				= str_replace(',','',$data['harga']);
			
			$tanda_edit 		= $data['tanda_edit'];
			
			if(empty($tanda_edit)){
				$Hist = 'Add ';
				$create = 'created_by';
				$times = 'created_date';
			}
			
			if(!empty($tanda_edit)){
				$Hist = 'Edit ';
				$create = 'updated_by';
				$times = 'updated_date';
			}
			
			
			$ArrInsert = array(
				'category' => $category,
				'nama' => $nama,
				'id_material' => $id_material,
				'dimensi' => $dimensi,
				'thickness' => $thickness,
				'ukuran_standart' => $ukuran_standart,
				'material' => $material,
				'satuan' => $satuan,
				'standart' => $standart,
				'keterangan' => $keterangan,
				// 'harga' => $harga,
				$create => $data_session['ORI_User']['username'],
				$times => $dateTime
			);
			
			$get_id = $this->db->select('MAX(id) AS id_max')->get('accessories')->result();
			
			$ArrStock = array(
				'id_acc' 	=> $get_id[0]->id_max + 1,
				'gudang' 	=> '11',
				'stock' 	=> 0,
				'rusak' 	=> 0,
				'update_by' => $data_session['ORI_User']['username'],
				'update_date' => $dateTime
			);
			// $this->db->insert('warehouse_acc_stock', $ArrStock);
			
			$this->db->trans_start();
				if(empty($tanda_edit)){
					$this->db->insert('accessories', $ArrInsert);
					$this->db->insert('warehouse_acc_stock', $ArrStock);
				}
				
				if(!empty($tanda_edit)){
					$this->db->where('id', $id);
					$this->db->update('accessories', $ArrInsert);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data success. Thanks ...',
					'status'	=> 1
				);
				history($Hist.'accessories gasket '.$id);
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

			$tanda1 = 'Add';
			if(!empty($id)){
				$tanda1 = 'Edit';
			}

			$header 	= $this->db->get_where('accessories', array('id'=>$id))->result();
			$satuan		= $this->db->order_by('kode_satuan','ASC')->or_where(array('id_satuan'=>'3'))->or_where(array('id_satuan'=>'20'))->get_where('raw_pieces', array('flag_active'=>'Y','delete'=>'N'))->result_array();
			$category 	= $this->db->get_where('accessories_category', array('id'=>'3'))->result_array();
			
			$data_session	= $this->session->userdata;
			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab accessories',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'gasket'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'		=> $tanda1.' Gasket',
				'action'	=> 'add',
				'header'	=> $header,
				'category_l'=> $category,
				'satuan_l'	=> $satuan
			);
			$this->load->view('Accessories/add_gasket',$data);
		}
	}
	
	//Lainnya
	public function get_json_lainnya(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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

		$SesFilter = array(
			'nama' => $requestData['nama'],
			'brand' => $requestData['brand']
		);
		$_SESSION['JSON_Filter_LAINNYA'] = $SesFilter;

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
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nama']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['material']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['dimensi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['spesifikasi']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['ukuran_standart']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['standart']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode_satuan']))."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['keterangan']))."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['harga'],2)."</div>";
			// 	$last_by 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			// 	$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			// $nestedData[]	= "<div align='center'>".strtolower($last_by)."</div>";
			// $nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";


				$edit	= "";
				$delete	= "";
				// if($Arr_Akses['update']=='1'){
				// 	$edit	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/add_lainnya/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
				// }
				if($Arr_Akses['delete']=='1'){
					$delete	= "&nbsp;<button class='btn btn-sm btn-danger deleted' title='Delete Data' data-id='".$row['id']."'><i class='fa fa-trash'></i></button>";
				}
			$nestedData[]	= "<div align='center'>".$edit.$delete."</div>";
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
	        )
		";
		// echo $sql; exit; 

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nama',
			2 => 'id_material',
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
	
	public function add_lainnya(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;
			
			$id 				= $data['id'];
			$category 			= $data['category'];
			$id_material 		= strtolower(trim($data['id_material']));
			$nama 				= strtolower(trim($data['nama']));
			$material 			= strtolower(trim($data['material']));
			$dimensi 			= strtolower(trim($data['dimensi']));
			$spesifikasi 		= strtolower(trim($data['spesifikasi']));
			$satuan 			= $data['satuan'];
			$standart 			= strtolower(trim($data['standart']));
			$ukuran_standart 	= strtolower(trim($data['ukuran_standart']));
			$keterangan 		= strtolower(trim($data['keterangan']));
			// $harga 				= str_replace(',','',$data['harga']);
			
			$tanda_edit 		= $data['tanda_edit'];
			
			if(empty($tanda_edit)){
				$Hist = 'Add ';
				$create = 'created_by';
				$times = 'created_date';
			}
			
			if(!empty($tanda_edit)){
				$Hist = 'Edit ';
				$create = 'updated_by';
				$times = 'updated_date';
			}
			
			
			$ArrInsert = array(
				'category' => $category,
				'nama' => $nama,
				'id_material' => $id_material,
				'dimensi' => $dimensi,
				'spesifikasi' => $spesifikasi,
				'ukuran_standart' => $ukuran_standart,
				'material' => $material,
				'satuan' => $satuan,
				'standart' => $standart,
				'keterangan' => $keterangan,
				// 'harga' => $harga,
				$create => $data_session['ORI_User']['username'],
				$times => $dateTime
			);
			
			$get_id = $this->db->select('MAX(id) AS id_max')->get('accessories')->result();
			
			$ArrStock = array(
				'id_acc' 	=> $get_id[0]->id_max + 1,
				'gudang' 	=> '11',
				'stock' 	=> 0,
				'rusak' 	=> 0,
				'update_by' => $data_session['ORI_User']['username'],
				'update_date' => $dateTime
			);
			// $this->db->insert('warehouse_acc_stock', $ArrStock);
			
			$this->db->trans_start();
				if(empty($tanda_edit)){
					$this->db->insert('accessories', $ArrInsert);
					$this->db->insert('warehouse_acc_stock', $ArrStock);
				}
				
				if(!empty($tanda_edit)){
					$this->db->where('id', $id);
					$this->db->update('accessories', $ArrInsert);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data success. Thanks ...',
					'status'	=> 1
				);
				history($Hist.'accessories lainnya '.$id);
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

			$tanda1 = 'Add';
			if(!empty($id)){
				$tanda1 = 'Edit';
			}

			$header 	= $this->db->get_where('accessories', array('id'=>$id))->result();
			$satuan		= $this->db->order_by('kode_satuan','ASC')->get_where('raw_pieces', array('flag_active'=>'Y','delete'=>'N'))->result_array();
			$category 	= $this->db->get_where('accessories_category', array('id'=>'4'))->result_array();
			
			$data_session	= $this->session->userdata;
			$arr_last = array(
				'date' => date('Y-m-d'),
				'category' => 'tab accessories',
				'status' => 'SUCCESS',
				'insert_by' => $data_session['ORI_User']['username'],
				'insert_date' => date('Y-m-d H:i:s'),
				'keterangan' => 'lainnya'
			);
			
			$this->db->insert('laporan_status', $arr_last);
			
			$data = array(
				'title'		=> $tanda1.' Lainnya',
				'action'	=> 'add',
				'header'	=> $header,
				'category_l'=> $category,
				'satuan_l'	=> $satuan
			);
			$this->load->view('Accessories/add_lainnya',$data);
		}
	}
	
	
	public function hapus(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		// print_r($data);
		// exit;
		$id 				= $this->uri->segment(3);
	
		$ArrInsert = array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> $dateTime
		);
		
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('accessories', $ArrInsert);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete data accessories '.$id);
		}
		echo json_encode($Arr_Kembali);
	
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
}