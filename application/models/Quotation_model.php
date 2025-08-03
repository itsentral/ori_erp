<?php
class Quotation_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	//INDEX
	public function index_quotation(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Quotation',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Quotation');
		$this->load->view('Sales/quotation',$data);
	}
	
	public function modal_detail_quotation(){
		$id_bq = $this->uri->segment(3);
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();
		

		$getEx	= explode('-', $id_bq);
				$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$getHeader	= $this->db->query($qSupplier)->result();

		$qMatr 		= "	SELECT
							a.id_bq,
							a.id AS id_milik,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							c.total_price_last AS cost,
							c.est_material
						FROM
							bq_detail_header a 
							LEFT JOIN cost_project_detail b ON a.id=b.caregory_sub
							LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
						WHERE
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."' AND a.id_category != 'product kosong' ORDER BY a.id ASC";					
		$getDetail	= $this->db->query($qMatr)->result_array();

		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
		$getEngCost	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
		$getPackCost	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
		$getTruck	= $this->db->query($gTruck)->result_array();

		$engCPCV 	= "SELECT
							b.*,
							c.* 
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
						WHERE
							 b.category = 'lokal' 
							AND b.id_bq = '".$id_bq."' 
							AND b.price_total <> 0
						ORDER BY
							b.id ASC ";
		$getVia	= $this->db->query($engCPCV)->result_array();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material AND a.id_milik = b.id
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.* 
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material AND a.id_milik = b.id
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'getHeader'		=> $getHeader,
			'getDetail'		=> $getDetail,
			'getEngCost'	=> $getEngCost,
			'getPackCost'	=> $getPackCost,
			'getTruck'		=> $getTruck,
			'otherArray'	=> $otherArray,
			'getVia'		=> $getVia,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		$this->load->view('Sales/modalDetailBQ',$data);
	}
	
	public function modal_approve_quotation(){
		$id_bq = $this->uri->segment(3); 
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();
		
		// $qBQdetailHeader 	= "SELECT a.* FROM bq_detail_header a WHERE a.id_bq = '".$id_bq."' ORDER BY a.id ASC";
		$qBQdetailHeader 	= "	SELECT 
									a.id,
									a.qty,
									a.id_delivery,
									a.id_category,
									a.sts_delivery,
									a.so_sts,
									a.id_product,
									c.est_material,
									c.total_price_last
								FROM 
									bq_detail_header a
									LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
								WHERE 
									a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."'
								ORDER BY 
									a.id ASC";
		$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();

		$qnoRev 			= "SELECT MAX(revised_no) AS revised_no FROM laporan_revised_header WHERE id_bq = '".$id_bq."' LIMIT 1";
		$restnoRev			= $this->db->query($qnoRev)->result_array();
		
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$getHeader	= $this->db->query($qSupplier)->result();

		// $qMatr 		= SQL_Quo_Edit($id_bq);	
		$qMatr 		= "	SELECT
							a.id_bq,
							a.id,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							b.price_total AS cost,
							c.est_material
						FROM
							bq_detail_header a 
								LEFT JOIN cost_project_detail b ON a.id=b.caregory_sub
								LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
						WHERE
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."'";			
		$getDetail	= $this->db->query($qMatr)->result_array();

		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
		$getEngCost	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
		$getPackCost	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
		$getTruck	= $this->db->query($gTruck)->result_array();

		$engCPCV 	= "SELECT
							b.*,
							c.* 
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
						WHERE
							 b.category = 'lokal' 
							AND b.id_bq = '".$id_bq."' 
							AND b.price_total <> 0
						ORDER BY
							b.id ASC ";
		$getVia	= $this->db->query($engCPCV)->result_array();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price,
								b.id AS id2,
								b.so_sts
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."'";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.*,
								b.id AS id2,
								b.so_sts		
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material  AND a.id_milik = b.id
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."'";
		$material		= $this->db->query($sql_material)->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'id_bq'			=> $id_bq,
			'otherArray'	=> $otherArray,
			'getHeader'		=> $getHeader,
			'getDetail'		=> $getDetail,
			'getEngCost'	=> $getEngCost,
			'getPackCost'	=> $getPackCost,
			'getTruck'		=> $getTruck,
			'getVia'		=> $getVia,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'qBQdetailRest'	=> $qBQdetailRest,
			'restnoRev'		=> $restnoRev
		);

		$this->load->view('Sales/modal_approve_quotation', $data);
	}
	
	public function modal_approve_quotation2(){
		$id_bq = $this->uri->segment(3); 
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();

		//NEW APPROVED
		$get_revisi_max = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		$revised_no = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

		$get_resin_pipa = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','pipa')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrResinPipa[] = $value['nm_material'];
		}
		$ArrHargaPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrHargaPipa[] = $value['price_mat'];
		}
		$resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrResinPipa):'#';
		$harga_resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrHargaPipa):'#';
		// $resin_pipa = (!empty($get_resin_pipa))?$get_resin_pipa[0]->nm_material:'';
		// $harga_resin_pipa = (!empty($get_resin_pipa))?$get_resin_pipa[0]->price_mat:0;

		$get_resin_flange = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','flange')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrResinFlange[] = $value['nm_material'];
		}
		$ArrHargaFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrHargaFlange[] = $value['price_mat'];
		}
		$resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrResinFlange):'#';
		$harga_resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrHargaFlange):'#';
		// $resin_flange = (!empty($get_resin_flange))?$get_resin_flange[0]->nm_material:'';
		// $harga_resin_flange = (!empty($get_resin_flange))?$get_resin_flange[0]->price_mat:0;

		$get_resin_fitting = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing',NULL)
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrResinFitting[] = $value['nm_material'];
		}
		$ArrHargaFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrHargaFitting[] = $value['price_mat'];
		}
		$resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrResinFitting):'#';
		$harga_resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrHargaFitting):'#';
		// $resin_fitting = (!empty($get_resin_fitting))?$get_resin_fitting[0]->nm_material:'';
		// $harga_resin_fitting = (!empty($get_resin_fitting))?$get_resin_fitting[0]->price_mat:0;

		$get_resin_bw = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','bw')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrResinBW[] = $value['nm_material'];
		}
		$ArrHargaBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrHargaBW[] = $value['price_mat'];
		}
		$resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrResinBW):'#';
		$harga_resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrHargaBW):'#';
		// $resin_bw = (!empty($get_resin_bw))?$get_resin_bw[0]->nm_material:'';
		// $harga_resin_bw = (!empty($get_resin_bw))?$get_resin_bw[0]->price_mat:0;

		$get_resin_field = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','field')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrResinField[] = $value['nm_material'];
		}
		$ArrHargaField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrHargaField[] = $value['price_mat'];
		}
		$resin_field = (!empty($get_resin_field))?implode('<br>',$ArrResinField):'#';
		$harga_resin_field = (!empty($get_resin_field))?implode('<br>',$ArrHargaField):'#';
		// $resin_field 		= (!empty($get_resin_field))?$get_resin_field[0]->nm_material:'';
		// $harga_resin_field 	= (!empty($get_resin_field))?$get_resin_field[0]->price_mat:0;
		
		$data = array(
			'id_bq'			=> $id_bq,
			'revised_no'	=> $revised_no,
			// 'getHeader'		=> $getHeader,
			// 'getDetail'		=> $getDetail,
			// 'getEngCost'	=> $getEngCost,
			// 'getPackCost'	=> $getPackCost,
			// 'getTruck'		=> $getTruck,
			// 'getVia'		=> $getVia,
			// 'non_frp'		=> $non_frp,
			// 'material'		=> $material,
			// 'qBQdetailRest'	=> $qBQdetailRest,
			// 'restnoRev'		=> $restnoRev,

			'resin_pipa'			=> $resin_pipa,
			'harga_resin_pipa'		=> $harga_resin_pipa,
			'resin_flange'			=> $resin_flange,
			'harga_resin_flange'	=> $harga_resin_flange,
			'resin_fitting'			=> $resin_fitting,
			'harga_resin_fitting'	=> $harga_resin_fitting,
			'resin_bw'				=> $resin_bw,
			'harga_resin_bw'		=> $harga_resin_bw,
			'resin_field'			=> $resin_field,
			'harga_resin_field'		=> $harga_resin_field
		);

		$this->load->view('Sales/modal_approve_quotation2', $data);
	}
	
	public function modal_view_material(){
		$id_bq = $this->uri->segment(3);

		// $query 	= "SELECT * FROM estimasi_total_material WHERE id_bq='".$id_bq."' AND id_material <> 'MTL-1903000' ORDER BY nm_material ASC ";
		// $result		= $this->db->query($query)->result_array();
		
		$result		= $this->db->order_by('nm_material','ASC')->get_where('estimasi_total_material',array('id_bq'=>$id_bq, 'id_material <>'=>'MTL-1903000', 'id_material <>'=>'MTL-2105003'))->result_array();
		
		$sql_non_frp 	= "	SELECT 
								a.*,
								b.unit_price,
								b.id AS id2,
								b.so_sts
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.id_milik = b.id 
							WHERE 
								(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."'";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "	SELECT 
								a.*,
								b.*,
								b.id AS id2,
								b.so_sts		
							FROM 
								cost_project_detail a
								LEFT JOIN bq_acc_and_mat b ON a.id_milik = b.id 
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."'";
		$material		= $this->db->query($sql_material)->result_array();
		
		$data = array(
			'detail'		=> $result,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		history('View detail material from sales, '.$id_bq);
		$this->load->view('Sales/modal_view_material', $data);
	}
	
	public function approve_quotation(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $get_Data			= $this->master_model->getData('raw_categories');\
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Indeks Of Approve Quotation',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data approve quotation');
		$this->load->view('Sales/approve_quotation',$data);
	}
	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	
	public function get_data_json_quotation(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/quotation";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_quotation(
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'><a class='active change_customer' style='cursor:pointer;' data-no_ipp='".$row['no_ipp']."' title='Change Customer'>".$row['nm_customer']."</a></div>";
			$nestedData[]	= "<div align='left'>".$row['quo_number']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(",<br>", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			
			$nestedData[]	= "<div align='right'>".number_format(get_total_by_revised($row['id_bq'])['weight'],3)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(get_total_by_revised($row['id_bq'])['total_project'],2)."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".strtoupper(strtolower($row['ref_quo']))."</span></div>";
	
				$warna = Color_status($row['sts_ipp']);

			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$approve_old	= "";
					$approve_new	= "";
					$print_product	= "";
					$print_cetak	= "";
					$print_cetak_eng	= "";
					$print_cetak_usd	= "";
					$history_bq		= "";
					$edit_penawaran = "";
					$view_material	= "&nbsp;<button type='button' style='margin-bottom:7px;' class='btn btn-sm btn-info detail_material' title='View Material' data-id_bq='".$row['id_bq']."'><i class='fa fa-cogs'></i></button>";
					
					if($row['app_quo'] == 'Y'){
						if($row['sts_price_quo'] == 'Y'){
							if($row['sts_ipp'] != 'WAITING PRODUCTION' AND $row['sts_ipp'] != 'PROCESS PRODUCTION' AND $row['sts_ipp'] != 'FINISH'){
								if($Arr_Akses['update']=='1'){
									$edit_penawaran	= "&nbsp;<a href='".base_url('penawaran/edit_penawaran_sales/'.$row['id_bq'])."' style='margin-bottom:7px;' class='btn btn-sm btn-warning'  title='Edit Penawaran' ><i class='fa fa-edit'></i></a>";
								}
							}
							if($Arr_Akses['download']=='1'){
								$print_product	= "&nbsp;<a href='".base_url('penawaran/print_penawaran2/'.$row['id_bq'])."' style='margin-bottom:7px;' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran' ><i class='fa fa-print'></i></a>";
								// $print_cetak	= "&nbsp;<a href='".base_url('penawaran/print_cetak/'.$row['id_bq'])."' target='_blank' class='btn btn-sm' style='background-color:#9640b4; color: white; margin-bottom:7px;'  title='Print Penawaran' ><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;<b>IDN IDR</b></a>";
								$print_cetak	= "<div class='btn-group' style='margin-bottom:3px;'>";
								$print_cetak	.= "<button type='button' class='btn btn-default'><b>Print</b></button>";
								$print_cetak	.= "<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>";
								$print_cetak	.= "	<span class='caret'></span>";
								$print_cetak	.= "	<span class='sr-only'>Toggle Dropdown</span>";
								$print_cetak	.= "</button>";
								$print_cetak	.= "<ul class='dropdown-menu' role='menu'>";
								$print_cetak	.= "	<li><a href='".base_url('penawaran/print_cetak/'.$row['id_bq'])."' target='_blank'>IDR (Indonesia)</a></li>";
								$print_cetak	.= "	<li><a href='".base_url('penawaran/print_cetak_new/'.$row['id_bq'])."' target='_blank'>IDR (Indonesia) (New)</a></li>";
								$print_cetak	.= "	<li><a href='".base_url('penawaran/print_cetak_eng/'.$row['id_bq'])."' target='_blank'>USD (Indonesia)</a></li>";
								$print_cetak	.= "	<li><a href='".base_url('penawaran/print_cetak_usd/'.$row['id_bq'])."' target='_blank'>USD (English)</a></li>";
								$print_cetak	.= "</ul>";
								$print_cetak	.= "</div>";
								$print_cetak_eng= "&nbsp;<a href='".base_url('penawaran/print_cetak_eng/'.$row['id_bq'])."' target='_blank' class='btn btn-sm' style='background-color:#d94c4c; color: white; margin-bottom:7px;'  title='Print Penawaran' ><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;<b>ENG</b></a>";
								$print_cetak_usd= "&nbsp;<a href='".base_url('penawaran/print_cetak_usd/'.$row['id_bq'])."' target='_blank' class='btn btn-sm' style='background-color:#959719; color: white; margin-bottom:7px;'  title='Print Penawaran' ><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;<b>IDN USD</b></a>";
								
							}
						}
					
						if($row['sts_ipp'] == 'ALREADY ESTIMATED PRICE' OR $row['sts_ipp'] == 'WAITING SALES ORDER' OR $row['sts_ipp'] == 'ALREADY SALES ORDER'){
							// $approve_old	 = "&nbsp;<button type='button' class='btn btn-sm btn-success' id='ApproveDT' title='Approve Project' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
							if($Arr_Akses['approve']=='1'){
								$approve_new = "&nbsp;<button type='button' class='btn btn-sm btn-success ApproveDTNew' style='margin-bottom:7px;' title='Approve Project (NEW)' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
							}
						}
					}
					// $NumHist = $this->db->query("SELECT * FROM hist_bq_header WHERE id_bq='BQ-".$row['no_ipp']."' ")->num_rows();
					// if($NumHist > 0){
						// $history_bq = "&nbsp;<button class='btn btn-sm' id='modal_hist' style='background-color: #b0cc19; border-color: #96af0f; color: white;' title='History BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-history'></i></button>";
					// }
			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-primary detail' style='margin-bottom:7px;' title='Detail' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$view_material."
									".$approve_old."
									".$approve_new."
									".$history_bq."
									".$edit_penawaran."
									".$print_product."
									".$print_cetak."
									".$print_cetak_usd."
									".$print_cetak_eng."
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

	public function query_data_json_quotation($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.project,
				b.ref_quo,
				b.status AS sts_ipp,
				b.sts_price_quo,
				c.quo_number
			FROM
				bq_header a 
				LEFT JOIN production b ON a.no_ipp = b.no_ipp
				LEFT JOIN cost_project_header_sales c ON a.no_ipp=REPLACE( c.id_bq, 'BQ-', '' ),
				(SELECT @row:=0) r
		    WHERE (b.status = 'ALREADY ESTIMATED PRICE'
					OR b.status = 'WAITING SALES ORDER'
					OR b.status = 'WAITING APPROVE SO'
					)
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.quo_number LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer',
			3 => 'c.quo_number',
			4 => 'project',
			5 => 'order_type'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_quotation_app(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approve_quotation";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_quotation_app(
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

			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['releasex']))."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(",<br>", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			
			$nestedData[]	= "<div align='right'>".number_format(get_total_by_revised($row['id_bq'])['weight'],3)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(get_total_by_revised($row['id_bq'])['total_project'],2)."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".strtoupper(strtolower($row['ref_quo']))."</span></div>";
					$approve_old	= "";
					$approve_new	= "";
					$print_product	= "";
					$print_cetak	= "";
					$print_cetak_eng	= "";
					$print_cetak_usd	= "";
					$history_bq		= "";
					$edit_penawaran = "";
					$view_material	= "&nbsp;<button type='button' style='margin-bottom:7px;' class='btn btn-sm btn-info detail_material' title='View Material' data-id_bq='".$row['id_bq']."'><i class='fa fa-cogs'></i></button>";
					
					if($row['sts_price_quo'] == 'Y'){
						// if($row['sts_ipp'] != 'WAITING PRODUCTION' AND $row['sts_ipp'] != 'PROCESS PRODUCTION' AND $row['sts_ipp'] != 'FINISH'){
							// if($Arr_Akses['update']=='1'){
								// $edit_penawaran	= "&nbsp;<a href='".base_url('penawaran/edit_penawaran_sales/'.$row['id_bq'])."' style='margin-bottom:7px;' class='btn btn-sm btn-warning'  title='Edit Penawaran' ><i class='fa fa-edit'></i></a>";
							// }
						// }
						// if($Arr_Akses['download']=='1'){
							// $print_product	= "&nbsp;<a href='".base_url('penawaran/print_penawaran2/'.$row['id_bq'])."' style='margin-bottom:7px;' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran' ><i class='fa fa-print'></i></a>";
							// $print_cetak	= "&nbsp;<a href='".base_url('penawaran/print_cetak/'.$row['id_bq'])."' target='_blank' class='btn btn-sm' style='background-color:#9640b4; color: white; margin-bottom:7px;'  title='Print Penawaran' ><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;<b>IDN IDR</b></a>";
							// $print_cetak_eng= "&nbsp;<a href='".base_url('penawaran/print_cetak_eng/'.$row['id_bq'])."' target='_blank' class='btn btn-sm' style='background-color:#d94c4c; color: white; margin-bottom:7px;'  title='Print Penawaran' ><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;<b>ENG</b></a>";
							// $print_cetak_usd= "&nbsp;<a href='".base_url('penawaran/print_cetak_usd/'.$row['id_bq'])."' target='_blank' class='btn btn-sm' style='background-color:#959719; color: white; margin-bottom:7px;'  title='Print Penawaran' ><i class='fa fa-print'></i>&nbsp;&nbsp;&nbsp;<b>IDN USD</b></a>";
							
						// }
					} 
					
					if($row['sts_ipp'] == 'ALREADY ESTIMATED PRICE' OR $row['sts_ipp'] == 'WAITING SALES ORDER' OR $row['sts_ipp'] == 'ALREADY SALES ORDER'){
						// $approve_old	 = "&nbsp;<button type='button' class='btn btn-sm btn-success' id='ApproveDT' title='Approve Project' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						if($Arr_Akses['approve']=='1'){
							$approve_new = "&nbsp;<button type='button' class='btn btn-sm btn-success ApproveDTNew' style='margin-bottom:7px;' title='Approve Project (NEW)' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						}
					}
					// $NumHist = $this->db->query("SELECT * FROM hist_bq_header WHERE id_bq='BQ-".$row['no_ipp']."' ")->num_rows();
					// if($NumHist > 0){
						// $history_bq = "&nbsp;<button class='btn btn-sm' id='modal_hist' style='background-color: #b0cc19; border-color: #96af0f; color: white;' title='History BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-history'></i></button>";
					// }
			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-primary detail' style='margin-bottom:7px;' title='Detail' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$view_material."
									".$approve_old."
									".$approve_new."
									".$history_bq."
									".$edit_penawaran."
									".$print_product."
									".$print_cetak."
									".$print_cetak_usd."
									".$print_cetak_eng."
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

	public function query_data_json_quotation_app($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.project,
				b.ref_quo,
				b.status AS sts_ipp,
				b.sts_price_quo,
				c.created_date AS releasex
			FROM
				bq_header a 
				LEFT JOIN production b ON a.no_ipp = b.no_ipp
				LEFT JOIN bq_price_project c ON a.id_bq = c.id_bq,
				(SELECT @row:=0) r
		    WHERE (b.status = 'ALREADY ESTIMATED PRICE') AND a.app_quo = 'N'
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'releasex',
			2 => 'no_ipp',
			3 => 'nm_customer',
			4 => 'project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}
