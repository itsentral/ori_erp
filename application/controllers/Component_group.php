<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Component_group extends CI_Controller {
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
		
		$get_Data			= $this->master_model->getData('component_group');
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Type Series Component',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Component Group');
		$this->load->view('Component_group/index',$data);
	}
	public function add(){		
		if($this->input->post()){
			$Arr_Data		= array();			
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			
			$resin_sistem	= $data['resin_sistem'];
			$liner			= $data['liner'];
			$pressure		= $data['pressure'];
			
			$KdResinSystem	= ($resin_sistem == 'ISO THALIC')?'I':'V';
			$KdPressure		= sprintf('%02s',$pressure);
			$KdLiner		= $liner;
			
			$kode_product	= $KdResinSystem."PN".$KdPressure."-".$KdLiner;
			
			$srcType		= "SELECT kode_group FROM component_group WHERE kode_group='".$kode_product."' ";
			$NumRow			= $this->db->query($srcType)->num_rows();
			
			
			// echo $this->VacumKomp($pressure)."<br>";
			// echo $this->StiffnessKomp($pressure)."<br>";
			// echo $this->FluidaKomp($resin_sistem, $liner)."<br>";
			// echo $this->AppKomp($this->StiffnessKomp($pressure))."<br>";
			// echo $kode_product;
			// exit;
			
			if($NumRow > 0){
				$Arr_Data	= array(
					'pesan'		=>'Series Sudah Terdaftar ...',
					'status'	=> 3
				);
			}
			else{
				$ArrInsert	= array(
					'kode_group' 		=> $kode_product,
					'resin_system' 		=> $resin_sistem,
					'pressure' 			=> $pressure,
					'liner' 			=> $liner,
					'criminal_barier' 	=> $this->FluidaKomp($resin_sistem, $liner),
					'vacum_rate' 		=> $this->VacumKomp($pressure),
					'stiffness' 		=> $this->StiffnessKomp($pressure),
					'aplikasi' 			=> $this->AppKomp($this->StiffnessKomp($pressure)),
					'created_by' 		=> $data_session['ORI_User']['username'],
					'created_date' 		=> date('Y-m-d H:i:s')
				);	
				
				// echo "<pre>";
				// print_r($ArrInsert);
				// exit;
				
				$this->db->trans_start();
				$this->db->insert('component_group', $ArrInsert);
				$this->db->trans_complete();
				
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Data	= array(
						'pesan'		=>'Insert series data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Data	= array(
						'pesan'		=>'Insert series data success. Thanks ...',
						'status'	=> 1
					);
					history('Insert series '.$kode_product);
				}
			}
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			
			$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$data = array(
				'title'			=> 'Add Series Component',
				'action'		=> 'add',
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'liner'			=> $ListLiner
			);
			$this->load->view('Component_group/add',$data);
		}
	}
	public function edit(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data					= $this->input->post();
			$Arr_Kembali			= array();
			$id_satuan				= $this->input->post('id_satuan');
			$kode_satuan			= strtoupper($this->input->post('kode_satuan'));
			$nama_satuan			= strtoupper($this->input->post('nama_satuan'));
			$flag_active			= ($this->input->post('flag_active') == 'Y')?'Y':'N';
			$descr					= $this->input->post('descr');
			$data_session			= $this->session->userdata;			
			
			//check kode satuan
			$qCodeSatu	= "SELECT * FROM raw_pieces WHERE kode_satuan = '".$kode_satuan."' ";
			$numCdSt	= $this->db->query($qCodeSatu)->num_rows();
			
			//check kode satuan
			$qNmSatu	= "SELECT * FROM raw_pieces WHERE nama_satuan = '".$nama_satuan."' ";
			$numNmSt	= $this->db->query($qNmSatu)->num_rows();
			
			$Arr_Update = array(
				'kode_satuan' 		=> $kode_satuan,
				'nama_satuan' 		=> $nama_satuan,
				'descr' 			=> $descr,
				'flag_active' 		=> $flag_active,
				'modified_by' 		=> $data_session['ORI_User']['username'],
				'modified_date' 	=> date('Y-m-d H:i:s')
			);
			// echo "<pre>"; print_r($Arr_Update);
			// exit;
			$this->db->trans_start();
			$this->db->where('id_satuan', $id_satuan);
			$this->db->update('raw_pieces', $Arr_Update);
			$this->db->trans_complete();
			if($numCdSt > 0){
				$Arr_Data		= array(
					'status'		=> 3,
					'pesan'			=> 'Pieces code already exists. Please check back ...'
				);
			}
			elseif($numNmSt > 0){
				$Arr_Data		= array(
					'status'		=> 4,
					'pesan'			=> 'Pieces name already exists. Please check back ...'
				);
			}
			else{
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Data	= array(
						'pesan'		=>'Update type material data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Data	= array(
						'pesan'		=>'Update type material data success. Thanks ...',
						'status'	=> 1
					);
					history('Update Type Material ['.$id_satuan.'] with username : '.$data_session['ORI_User']['username']);
				}
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$id = $this->uri->segment(3);
			
			// $detail				= $this->master_model->getData('raw_pieces','id_category',$id);  
			$detail		= $this->db->query("SELECT * FROM raw_pieces WHERE id_satuan = '".$id."' ")->result_array();
			$data = array(
				'title'			=> 'Edit Pieces',
				'action'		=> 'edit',
				'row'			=> $detail
			);
			
			$this->load->view('Component_group/edit',$data);   
		}
	}

	function hapus(){
		$idCategory = $this->uri->segment(3);
		// echo $idCategory; exit;
		//nm series yang dihapus untuk history
		$qNmStuan	= "SELECT * FROM component_group WHERE id='".$idCategory."' ";
		$restDtSt	= $this->db->query($qNmStuan)->result_array();
		$kd_group	= $restDtSt[0]['kode_group'];
		
		$this->db->trans_start();
		$this->db->where('id', $idCategory);
		$this->db->delete('component_group');
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete series data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete series data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Series : '.$kd_group);
		}
		echo json_encode($Arr_Data);
	}
	
	//Rate
	function VacumKomp($pressure){
		//PRESSURE PN6 & PN8 & PN10
		if($pressure == 6 OR $pressure == 8 OR $pressure == 10){
			$Vacum	= 'NON VACCUM';	
		}
		//PRESSURE PN12
		if($pressure == 12 ){
			$Vacum	= 'HALF VACCUM';	
		}
		//PRESSURE PN14 & PN16 & PN18 & PN20
		if($pressure == 14 OR $pressure == 16 OR $pressure == 18 OR $pressure == 20){
			$Vacum	= 'FULL VACCUM';	
		}
		
		return $Vacum;
	}
	
	function StiffnessKomp($pressure){
		//PRESSURE PN6 & PN8
		if($pressure == 6 OR $pressure == 8 ){
			$Stiffness	= 'SN1250';
		}
		//PRESSURE PN10 & PN12
		if($pressure == 10 OR $pressure == 12){
			$Stiffness	= 'SN2500';
		}
		//PRESSURE PN14
		if($pressure == 14){
			$Stiffness	= 'SN5000';
		}
		//PRESSURE PN16 PN18 PN20
		if($pressure == 16 OR $pressure == 18 OR $pressure == 20){
			$Stiffness	= 'SN10000';
		}
		
		return $Stiffness;
	}
	
	function FluidaKomp($resin_sistem, $liner){
		//RESIN SISTEM ISO THALIC 
		if($resin_sistem == 'ISO THALIC'){
			if($liner == 0.5){
				$Fluida	= 'LOW CORROSION';
			}
			else{
				$Fluida	= 'MIDDLE CORROSION';
			}
		}
		//RESIN SISTEM VINYLESTER 
		if($resin_sistem == 'VINYLESTER'){
			if($liner == 0.5){
				$Fluida	= 'MIDDLE CORROSION';
			}
			else{
				$Fluida	= 'HIGH CORROSION';
			}
		}
		return $Fluida;
	}
	
	public function AppKomp($stiffness){
		if($stiffness == 'SN1250'){
			$App	= 'ABOVE GROUND';
		}
		else{
			$App	= 'UNDER GROUND';
		}
		
		return $App;
	}
	
	
	public function cycletime(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/cycletime";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->master_model->getData('component_group');
		$menu_akses			= $this->master_model->getMenu();
		$ListResinSystem	= $this->db->query("SELECT * FROM product_parent WHERE deleted ='N' ORDER BY product_parent")->result_array();
		$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
		$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Cycletime Product',
			'action'		=> 'index',
			'product_parent'=> $ListResinSystem,
			'pressure'		=> $ListPressure,
			'liner'			=> $ListLiner,
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View component cycletime value');
		$this->load->view('Component_group/cycletime',$data);
	}
	
	public function server_side_cycletime(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_cycletime(
			$requestData['product_parent'],
			$requestData['pn'],
			$requestData['liner'],
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['kode']))."</div>";
			// $nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['standard_code']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['product_parent']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter2'])."</div>";
			$nestedData[]	= "<div align='center'>PN ".strtoupper(strtolower($row['pn']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['liner']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['man_power']))."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper(strtolower($row['total_time']))."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper(strtolower($row['man_hours']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_mesin']))."</div>";
				$dataModif = (!empty($row['modified_by']))?$row['modified_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($dataModif))."</div>";
				$dataModifx = (!empty($row['modified_date']))?$row['modified_date']:$row['created_on'];
			$nestedData[]	= "<div align='right'>".date('d-M-y H:i:s', strtotime($dataModifx))."</div>";
				$update = "";
				
				if($Arr_Akses['update']=='1'){
					$update	= "<a href='".site_url($this->uri->segment(1).'/add_cycletime/'.$row['id'])."' class='btn btn-sm btn-success' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>";
				}
			$nestedData[]	= 	"<div align='center'>
								".$update."
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

	public function query_data_json_cycletime($product_parent, $pn, $liner, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_product = "";
		if($product_parent <> '0'){
			$where_product = "AND a.product_parent = '".$product_parent."'";
		}
		
		$where_pn = "";
		if($pn <> '0'){
			$where_pn = "AND a.pn = '".$pn."'";
		}
		
		$where_liner = "";
		if($liner <> '0'){
			$where_liner = "AND a.liner = '".$liner."'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				cycletime_default a,
        		(SELECT @row:=0) r
		    WHERE 1=1 ".$where_product." ".$where_pn." ".$where_liner." AND a.deleted = 'N' AND (
				a.kode LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.diameter LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.diameter2 LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode',
			2 => 'product_parent',
			3 => 'diameter',
			4 => 'diameter2',
			5 => 'pn',
			6 => 'liner',
			7 => 'man_power',
			8 => 'total_time',
			9 => 'man_hours',
			10 => 'id_mesin',
			11 => 'created_by',
			12 => 'created_on'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add_cycletime(){		
		if($this->input->post()){
			$Arr_Data		= array();			
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$Ym				= date('ym');
			
			$id				= $data['id'];
			$kode			= $data['kode'];
			
			$product_parent	= (!empty($data['product_parent']))?$data['product_parent']:'';
			$diameter		= (!empty($data['diameter']))?str_replace(',','',$data['diameter']):'0';
			$diameter2		= (!empty($data['diameter2']))?str_replace(',','',$data['diameter2']):'0';
			$pn				= (!empty($data['pn']))?$data['pn']:'';
			$liner			= (!empty($data['liner']))?$data['liner']:'';
			$id_mesin		= $data['id_mesin'];
			$standard_length= str_replace(',','',$data['standard_length']);
			$man_power		= str_replace(',','',$data['man_power']);
			$total_time		= str_replace(',','',$data['total_time']);
			$man_hours		= $man_power * $total_time;
			
			//check ct apakah masih ada
			if(empty($id)){
				$tanda = 'Insert';
				$srcType	= "	SELECT 
									* 
								FROM 
									cycletime_default 
								WHERE 
									product_parent='".$product_parent."' 
									AND diameter='".$diameter."' 
									AND diameter2='".$diameter2."' 
									AND pn='".$pn."' 
									AND liner='".$liner."'
									AND deleted='N' 
									";
				$NumRow		= $this->db->query($srcType)->num_rows();
				
				if($NumRow > 0){
					$Arr_Data	= array(
						'pesan'		=>'Cyletime Sudah Ada ...',
						'status'	=> 3
					);
					echo json_encode($Arr_Data);
					return false;
				}
			
				//pengurutan kode
				$srcMtr			= "SELECT MAX(kode) as maxP FROM cycletime_default WHERE kode LIKE '".$Ym."%' ";
				$numrowMtr		= $this->db->query($srcMtr)->num_rows();
				$resultMtr		= $this->db->query($srcMtr)->result_array();
				$angkaUrut2		= $resultMtr[0]['maxP'];
				$urutan2		= (int)substr($angkaUrut2, 4, 5);
				$urutan2++;
				$urut2			= sprintf('%05s',$urutan2);
				$kode			= $Ym.$urut2;
			
				$ArrInsert	= array(
					'kode' 				=> $kode,
					'standard_code' 	=> 'PRODUCT-ORI',
					'product_parent' 	=> $product_parent,
					'diameter' 			=> $diameter,
					'diameter2' 		=> $diameter2,
					'pn' 				=> $pn,
					'liner' 			=> $liner,
					'standard_length' 	=> $standard_length,
					'man_power' 		=> $man_power,
					'id_mesin' 			=> $id_mesin,
					'total_time' 		=> $total_time,
					'man_hours' 		=> $man_hours,
					'created_by' 		=> $data_session['ORI_User']['username'],
					'created_on' 		=> date('Y-m-d H:i:s')
				);
			}
			if(!empty($id)){
				$tanda = 'Edit';
				$ArrInsert	= array(
					'standard_length' 	=> $standard_length,
					'man_power' 		=> $man_power,
					'id_mesin' 			=> $id_mesin,
					'total_time' 		=> $total_time,
					'man_hours' 		=> $man_hours,
					'modified_by' 		=> $data_session['ORI_User']['username'],
					'modified_on' 		=> date('Y-m-d H:i:s')
				);
			}
			
			// echo "<pre>";
			// print_r($ArrInsert);
			// exit;
			
			$this->db->trans_start();
			if(empty($id)){
				$this->db->insert('cycletime_default', $ArrInsert);
			}
			if(!empty($id)){
				$this->db->where('id',$id);
				$this->db->update('cycletime_default', $ArrInsert);
			}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Process data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Process data success. Thanks ...',
					'status'	=> 1
				);
				history($tanda.' cycletime value '.$kode);
			}
			
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			
			$id 				= $this->uri->segment(3);
			$data 				= $this->db->query("SELECT * FROM cycletime_default WHERE id ='".$id."'")->result();
			$ListResinSystem	= $this->db->query("SELECT * FROM product_parent WHERE deleted ='N' ORDER BY product_parent")->result_array();
			$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();
			$ListMachine		= $this->db->query("SELECT * FROM machine WHERE sts_mesin ='Y'")->result_array();

			$data = array(
				'title'			=> 'Add Cycletime',
				'action'		=> 'add',
				'resin_system'	=> $ListResinSystem,
				'pressure'		=> $ListPressure,
				'machine'		=> $ListMachine,
				'liner'			=> $ListLiner,
				'id'			=> $id,
				'data'			=> $data
			);
			$this->load->view('Component_group/add_cycletime',$data);
		}
	}
	
}