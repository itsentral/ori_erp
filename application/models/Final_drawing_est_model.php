<?php
class Final_drawing_est_model extends CI_Model {

	public function __construct() {
		parent::__construct();
    }
	
	public function index_estimasi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Project Estimation Final Drawing',
			'action'		=> 'fd_estimasi',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Project Estimation Final Drawing');
		$this->load->view('FinalDrawing/fd_estimasi',$data);
	}
	
	public function view_data(){
		$id_bq 		= $this->uri->segment(3);
		$sql_detail = "SELECT a.*, b.sum_mat FROM so_detail_header a LEFT JOIN so_estimasi_cost_and_mat b ON a.id=b.id_milik WHERE a.id_bq = '".$id_bq."' AND b.parent_product <> 'pipe slongsong' ORDER BY a.id_bq_header ASC";
		$detail		= $this->db->query($sql_detail)->result_array();

		$detail1 	= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='acc'")->result_array();
		$detail2 	= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='mat'")->result_array();
		$detail3 		= $this->db->get_where('so_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'baut'))->result_array();
		$detail4 		= $this->db->get_where('so_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'plate'))->result_array();
		$detail4g 		= $this->db->get_where('so_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'gasket'))->result_array();
		$detail5 		= $this->db->get_where('so_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'lainnya'))->result_array();
		
		$data = array(
			'id_bq'		=> $id_bq,
			'detail'	=> $detail,
			'detail1'	=> $detail1,
			'detail2'	=> $detail2,
			'detail3'	=> $detail3,
			'detail4'	=> $detail4,
			'detail4g'	=> $detail4g,
			'detail5'	=> $detail5
		);
		history('View Result Material Estimasi Final Drawing BQ: '.$id_bq);
		$this->load->view('FinalDrawing/modalViewDT', $data);
	}
	
	public function modal_detail_material(){
		$id_product = $this->uri->segment(3);
		$id_milik 	= $this->uri->segment(4);
		$qty 		= floatval($this->uri->segment(5));
		// echo $id_product;
		$qHeader		= "SELECT * FROM so_component_header WHERE id_product='".$id_product."' AND id_milik='".$id_milik."'";
		$restHeader		= $this->db->query($qHeader)->result_array();
		
		$qDetail1		= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		$qDetail2		= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		if ($restHeader[0]['parent_product']=='branch joint' || $restHeader[0]['parent_product']=='field joint' || $restHeader[0]['parent_product']=='shop joint')
		{
			$qDetail1		= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='GLASS' AND a.id_material <> 'MTL-1903000'";
			$qDetail2		= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000' ";
			$qDetail2Add	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000' ";
		}
		$qDetail3		= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		$detailResin1	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin3	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$qDetailPlus1	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
		$qDetailPlus2	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailPlus3	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailPlus4	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd1	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd2	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd3	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd4	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
		// echo $qDetail2;
		//tambahan flange mould /slongsong
		$qDetail2N1		= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		$qDetail2N2		= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		$qDetailPlus2N1	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000'";
		$qDetailPlus2N2	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd2N1	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd2N2	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000'";
		$detailResin2N1	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_category ='TYP-0001' AND a.id_material <> 'MTL-1903000' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2N2	= "SELECT a.*, b.price_ref_estimation FROM so_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_category ='TYP-0001' AND a.id_material <> 'MTL-1903000' ORDER BY a.id_detail DESC LIMIT 1 ";
		
		
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail2Add	= [];
		if ($restHeader[0]['parent_product']=='branch joint' || $restHeader[0]['parent_product']=='field joint' || $restHeader[0]['parent_product']=='shop joint')
		{
		$restDetail2Add	= $this->db->query($qDetail2Add)->result_array();
		}
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$numRows3		= $this->db->query($qDetail3)->num_rows();
		$restResin1			= $this->db->query($detailResin1)->result_array();
		$restResin2			= $this->db->query($detailResin2)->result_array();
		$restResin3			= $this->db->query($detailResin3)->result_array();
		$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
		$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
		$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
		$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
		$NumDetailPlus4		= $this->db->query($qDetailPlus4)->num_rows();
		$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
		$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
		$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
		$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
		$NumDetailAdd1		= $this->db->query($qDetailAdd1)->num_rows();
		$NumDetailAdd2		= $this->db->query($qDetailAdd2)->num_rows();
		$NumDetailAdd3		= $this->db->query($qDetailAdd3)->num_rows();
		$NumDetailAdd4		= $this->db->query($qDetailAdd4)->num_rows();
		
		//tambahan flange mould /slongsong
		$restDetail2N1		= $this->db->query($qDetail2N1)->result_array();
		$restDetail2N2		= $this->db->query($qDetail2N2)->result_array();
		$restDetailPlus2N1	= $this->db->query($qDetailPlus2N1)->result_array();
		$restDetailPlus2N2	= $this->db->query($qDetailPlus2N2)->result_array();
		$restDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->result_array();
		$restDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->result_array();
		$NumDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->num_rows();
		$NumDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->num_rows();
		$restResin2N1		= $this->db->query($detailResin2N1)->result_array();
		$restResin2N2		= $this->db->query($detailResin2N2)->result_array();
	
		
		$data = array(
			'id_milik'			=> $id_milik,
			'qty'				=> $qty,
			'restHeader'		=> $restHeader,
			'restDetail1'	    => $restDetail1,
			'restDetail2'	    => $restDetail2,
			'restDetail2Add'	    => $restDetail2Add,
			'restDetail3'	    => $restDetail3,
			'numRows3'	        => $numRows3,
			'restResin1'		=> $restResin1,
			'restResin2'		=> $restResin2,
			'restResin3'		=> $restResin3,
			'restDetailPlus1'	=> $restDetailPlus1,
			'restDetailPlus2'	=> $restDetailPlus2,
			'restDetailPlus3'	=> $restDetailPlus3,
			'restDetailPlus4'	=> $restDetailPlus4,
			'NumDetailPlus4'	=> $NumDetailPlus4,
			'restDetailAdd1'	=> $restDetailAdd1,
			'restDetailAdd2'	=> $restDetailAdd2,
			'restDetailAdd3'	=> $restDetailAdd3,
			'restDetailAdd4'	=> $restDetailAdd4,
			'NumDetailAdd1'		=> $NumDetailAdd1,
			'NumDetailAdd2'		=> $NumDetailAdd2,
			'NumDetailAdd3'		=> $NumDetailAdd3,
			'NumDetailAdd4'		=> $NumDetailAdd4,
			'restDetail2N1'		=> $restDetail2N1,
			'restDetail2N2'		=> $restDetail2N2,
			'restDetailPlus2N1'	=> $restDetailPlus2N1,
			'restDetailPlus2N2'	=> $restDetailPlus2N2,
			'restDetailAdd2N1'	=> $restDetailAdd2N1,
			'restDetailAdd2N2'	=> $restDetailAdd2N2,
			'NumDetailAdd2N1'	=> $NumDetailAdd2N1,
			'NumDetailAdd2N2'	=> $NumDetailAdd2N2,
			'restResin2N1'		=> $restResin2N1,
			'restResin2N2'		=> $restResin2N2
		);
		
		$this->load->view('FinalDrawing/modal_detail_material', $data);
	}
	
	public function modal_est_bq(){
		$id_bq = $this->uri->segment(3);
		$app_est = $this->uri->segment(4);

		$row	= $this->db->get_where('so_header',array('id_bq'=>$id_bq))->result_array(); 

		$qBQdetailHeader 	= "SELECT 
									a.*, 
									a.series, 
									b.no_ipp,
									c.id_milik AS id_milik_bq
								FROM so_detail_header a 
								LEFT JOIN so_header b ON a.id_bq=b.id_bq
								LEFT JOIN so_bf_detail_header c ON a.id_milik=c.id
								WHERE a.id_bq = '".$id_bq."' AND a.id_category <> 'pipe slongsong' ORDER BY a.id_bq_header ASC";
		$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
		
		$detail 		= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='acc'")->result_array();
		$detail2 		= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='mat'")->result_array();
		$satuan			= $this->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY nama_satuan ASC ")->result_array();
		$raw_material	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND `delete`='N' ORDER BY nm_material ASC ")->result_array();
		$jenis_barang	= $this->db->query("SELECT * FROM con_nonmat_new WHERE `deleted`='N' AND category_awal='7' ORDER BY material_name ASC ")->result_array();
		
		//COUNT & CHECK MATERIAL
		$sqlResin = "(SELECT id_material, nm_material, id_category  FROM so_component_detail WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)
			 UNION
			(SELECT id_material, nm_material, id_category  FROM so_component_detail_plus WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)";
		$ListBQipp		= $this->db->query($sqlResin)->result_array();
		$dtListArrayResin = array();
		$dtListArrayVeil = array();
		$dtListArrayCsm = array();
		$dtListArrayWR = array();
		$dtListArrayRooving = array();
		$dtListArrayCatalys = array();
		$dtListArrayPigment = array();

		$dtListArrayResinID = array();
		$dtListArrayVeilID = array();
		$dtListArrayCsmID = array();
		$dtListArrayWRID = array();
		$dtListArrayRoovingID = array();
		$dtListArrayCatalysID = array();
		$dtListArrayPigmentID = array();
		foreach($ListBQipp AS $val => $valx){
			if($valx['id_category'] == 'TYP-0001'){
				$dtListArrayResin[$val] = $valx['nm_material'];
				$dtListArrayResinID[$val] = $valx['id_material'];
			}
			if($valx['id_category'] == 'TYP-0003'){
				$dtListArrayVeil[$val] = $valx['nm_material'];
				$dtListArrayVeilID[$val] = $valx['id_material'];
			}
			if($valx['id_category'] == 'TYP-0004'){
				$dtListArrayCsm[$val] = $valx['nm_material'];
				$dtListArrayCsmID[$val] = $valx['id_material'];
			}
			if($valx['id_category'] == 'TYP-0006'){
				$dtListArrayWR[$val] = $valx['nm_material'];
				$dtListArrayWRID[$val] = $valx['id_material'];
			}
			if($valx['id_category'] == 'TYP-0005'){
				$dtListArrayRooving[$val] = $valx['nm_material'];
				$dtListArrayRoovingID[$val] = $valx['id_material'];
			}
			if($valx['id_category'] == 'TYP-0002'){
				$dtListArrayCatalys[$val] = $valx['nm_material'];
				$dtListArrayCatalysID[$val] = $valx['id_material'];
			}
			if($valx['id_category'] == 'TYP-0007'){
				$dtListArrayPigment[$val] = $valx['nm_material'];
				$dtListArrayPigmentID[$val] = $valx['id_material'];
			}
		}
		$dtImplodeResin	= "".implode("  ---  ", $dtListArrayResin)."";
		$dtImplodeVeil	= "".implode("  ---  ", $dtListArrayVeil)."";
		$dtImplodeCsm	= "".implode("  ---  ", $dtListArrayCsm)."";
		$dtImplodeWR	= "".implode("  ---  ", $dtListArrayWR)."";
		$dtImplodeRooving	= "".implode("  ---  ", $dtListArrayRooving)."";
		$dtImplodeCatalys	= "".implode("  ---  ", $dtListArrayCatalys)."";
		$dtImplodePigment	= "".implode("  ---  ", $dtListArrayPigment)."";


		$dtImplodeResinID	= "".implode(",", $dtListArrayResinID)."";
		$dtImplodeVeilID	= "".implode(",", $dtListArrayVeilID)."";
		$dtImplodeCsmID	= "".implode(",", $dtListArrayCsmID)."";
		$dtImplodeWRID	= "".implode(",", $dtListArrayWRID)."";
		$dtImplodeRoovingID	= "".implode(",", $dtListArrayRoovingID)."";
		$dtImplodeCatalysID	= "".implode(",", $dtListArrayCatalysID)."";
		$dtImplodePigmentID	= "".implode(",", $dtListArrayPigmentID)."";
		
		$detail3 		= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='baut'")->result_array();
		$detail4 		= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='plate'")->result_array();
		$detail4g 		= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='gasket'")->result_array();
		$detail5 		= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND category='lainnya'")->result_array();	

		$jenis_baut	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='1' ORDER BY nama ASC ")->result_array();
		$jenis_plate	= $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='2' ORDER BY nama ASC ")->result_array();
		$jenis_gasket	= $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='3' ORDER BY nama ASC ")->result_array();
		$jenis_part	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='4' ORDER BY nama ASC ")->result_array();


		//Category
		$arrWhereIn = array('TYP-0001','TYP-0003','TYP-0004','TYP-0005','TYP-0006','TYP-0002','TYP-0007');
		$ListCategory = $this->db->select('id_category, category')->from('raw_categories')->where_in('id_category',$arrWhereIn)->get()->result_array();

		$ArrBQProduct = array();
		foreach($ListCategory AS $val => $valx){
			$ArrBQProduct[$valx['id_category']] = strtoupper($valx['category']);
		}

		//List Resin
		$dataResin		= $this->db->select('id_material, nm_material')->order_by('nm_material','asc')->get_where('raw_materials',array('id_category'=>'TYP-0001'))->result_array();

		$ArrResin = array();
		foreach($dataResin AS $val => $valx){
			$ArrResin[$valx['id_material']] = strtoupper($valx['nm_material']);
		}
		$ArrResin[0]	= 'Select Material';

		$resultHistory = $this->db->select('a.*, b.type AS typeMaterial')->order_by('a.change_date','ASC')->join('change_material_help b','a.type=b.type_material','left')->get_where('change_material_hist a',array('a.no_ipp'=>$id_bq))->result_array();

		$data = array(
			'id_bq'			=> $id_bq,
			'app_est'		=> $app_est,
			'ArrBQProduct' 	=> $ArrBQProduct,
			'row'			=> $row,
			'qBQdetailRest'	=> $qBQdetailRest,
			'detail'		=> $detail,
			'detail2'		=> $detail2,
			'satuan'		=> $satuan,
			'raw_material'	=> $raw_material,
			'jenis_barang'	=> $jenis_barang,
			'ArrResin'		=> $ArrResin,
			'detail3'		=> $detail3,
			'detail4'		=> $detail4,
			'detail4g'		=> $detail4g,
			'detail5'		=> $detail5,
			'jenis_baut'	=> $jenis_baut,
			'jenis_plate'	=> $jenis_plate,
			'jenis_gasket'	=> $jenis_gasket,
			'jenis_part'	=> $jenis_part,
			'resultHistory' => $resultHistory,

			'listResin' => $dtImplodeResin,
			'countResin' => $dtListArrayResin,
			'listVeil' => $dtImplodeVeil,
			'countVeil' => $dtListArrayVeil,
			'listCsm' => $dtImplodeCsm,
			'countCsm' => $dtListArrayCsm,
			'listWR' => $dtImplodeWR,
			'countWR' => $dtListArrayWR,
			'listRooving' => $dtImplodeRooving,
			'countRooving' => $dtListArrayRooving,
			'listCatalys' => $dtImplodeCatalys,
			'countCatalys' => $dtListArrayCatalys,
			'listPigment' => $dtImplodePigment,
			'countPigment' => $dtListArrayPigment,

			'dtImplodeResinID' => $dtImplodeResinID,
			'dtImplodeVeilID' => $dtImplodeVeilID,
			'dtImplodeCsmID' => $dtImplodeCsmID,
			'dtImplodeWRID' => $dtImplodeWRID,
			'dtImplodeRoovingID' => $dtImplodeRoovingID,
			'dtImplodeCatalysID' => $dtImplodeCatalysID,
			'dtImplodePigmentID' => $dtImplodePigmentID,
			'GET_MATERIAL' => get_detail_material(),
			'GET_PRODUCT' => get_detail_final_drawing()
		);
		history('View Estimasi Final Drawing BQ: '.$id_bq);
		
		$this->load->view('FinalDrawing/modal_est_bq', $data);
	}
	
	public function update_est_get_last(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		
		$id_bq			= "BQ-".$data['no_ipp'];
		$no_ipp			= $data['no_ipp'];
		$pembeda		= $data['pembeda'];
		
		$chOri			= $data['check'];
		$check 			= $chOri;
		$detailBQ		= $data['detailBQ'];
		$Arr0 = array();
		foreach($check AS $vaxl){
			$valG = explode('-', $vaxl);
			$Arr0[$vaxl] = $valG[0];
			$Arr1[$vaxl] = $valG[1];
		}
		$dtImplode		= "('".implode("','", $Arr0)."')";
		$dtImplode2		= "('".implode("','", $Arr1)."')";

		// echo $dtImplode2;
		// exit;
		
		$dtListArray 	= array();
		foreach($check AS $valT ){
			foreach($detailBQ AS $val => $valx){
				$valG = explode('-', $valT);
				if($valx['id'] == $valG[0]){
					$dtListArray[$val]['id'] = $valx['id'];
					$dtListArray[$val]['id_milik'] = $valG[1];
					$dtListArray[$val]['panjang'] = $valx['panjang'];
					$dtListArray[$val]['id_productx'] = $valx['id_productx'];
				}
			}
		}
		
		$ArrDetBq		= array();
		foreach($dtListArray AS $val => $valx){
			if(!empty($valx["id_productx"]))
			{
				$ArrDetBq[$val]['id']	= $valx['id'];
				$ArrDetBq[$val]['id_milik']	= $valx['id_milik'];
				$ArrDetBq[$val]['id_product']	= $valx['id_productx'];
				$ArrDetBq[$val]['panjang']	= $valx['panjang'];
			}
		}

		// print_r($ArrDetBq);
		// exit;

		$ArrDetBq2		= array();
		foreach($dtListArray AS $val => $valx){
			if(!empty($valx["id_productx"]))
			{
				$ArrDetBq2[$val]['id']	= $valx['id'];
				$ArrDetBq2[$val]['id_product']	= $valx['id_productx'];
			}
		}

		// print_r($ArrDetBq);
		// print_r($ArrDetBq2);
		// exit;
		// $ArrHeader 		= array_unique(array_column($ArrDetBq, "id_product"));
		// echo "<pre>";

		$ArrBqHeader			= array();
		$ArrBqDetail			= array();
		$ArrBqDetailPlus		= array();
		$ArrBqDetailAdd			= array();
		$ArrBqFooter			= array();
		$ArrBqHeaderHist		= array();
		$ArrBqDetailHist		= array();
		$ArrBqDetailPlusHist	= array();
		$ArrBqDetailAddHist		= array();
		$ArrBqFooterHist		= array();
		$ArrBqDefault			= array();
		$ArrBqDefaultHist		= array();

		$LoopDetail = 0;
		$LoopDetailLam = 0;
		$LoopDetailPlus = 0;
		$LoopDetailAdd = 0;
		$LoopFooter = 0;
		foreach($ArrDetBq AS $val => $valx){
			$getPanjang = $this->db->get_where('so_detail_header',array('id'=>$valx['id']))->result();
			//Component Header
			$qHeader	= $this->db->query("SELECT * FROM bq_component_header WHERE id_product='".$valx['id_product']."' AND id_milik='".$valx['id_milik']."' LIMIT 1 ")->result();
			$PANJANG_BEF = $qHeader[0]->panjang;
			$ArrBqHeader[$val]['id_product']			= $valx['id_product'];
			$ArrBqHeader[$val]['id_bq']					= $id_bq;
			$ArrBqHeader[$val]['id_milik']				= $valx['id'];
			$ArrBqHeader[$val]['parent_product']		= $qHeader[0]->parent_product;
			$ArrBqHeader[$val]['nm_product']			= $qHeader[0]->nm_product;
			$ArrBqHeader[$val]['standart_code']			= $qHeader[0]->standart_code;
			$ArrBqHeader[$val]['series']				= $qHeader[0]->series;
			$ArrBqHeader[$val]['resin_sistem']			= $qHeader[0]->resin_sistem;
			$ArrBqHeader[$val]['pressure']				= $qHeader[0]->pressure;
			$ArrBqHeader[$val]['diameter']				= $qHeader[0]->diameter;
			$ArrBqHeader[$val]['liner']					= $qHeader[0]->liner;
			$ArrBqHeader[$val]['aplikasi_product']		= $qHeader[0]->aplikasi_product;
			$ArrBqHeader[$val]['criminal_barier']		= $qHeader[0]->criminal_barier;
			$ArrBqHeader[$val]['vacum_rate']			= $qHeader[0]->vacum_rate;
			$ArrBqHeader[$val]['stiffness']				= $qHeader[0]->stiffness;
			$ArrBqHeader[$val]['design_life']			= $qHeader[0]->design_life;
			$ArrBqHeader[$val]['standart_by']			= $qHeader[0]->standart_by;
			$ArrBqHeader[$val]['standart_toleransi']	= $qHeader[0]->standart_toleransi;
			$ArrBqHeader[$val]['diameter2']				= $qHeader[0]->diameter2;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader[$val]['panjang']			= floatval($valx['panjang']) + 400;
			}
			else{
				$ArrBqHeader[$val]['panjang']			= $qHeader[0]->panjang;
			}
			$ArrBqHeader[$val]['radius']				= $qHeader[0]->radius;
			$ArrBqHeader[$val]['type_elbow']			= $qHeader[0]->type_elbow;
			$ArrBqHeader[$val]['angle']					= $qHeader[0]->angle;
			$ArrBqHeader[$val]['design']				= $qHeader[0]->design;
			$ArrBqHeader[$val]['est']					= $qHeader[0]->est;
			$ArrBqHeader[$val]['min_toleransi']			= $qHeader[0]->min_toleransi;
			$ArrBqHeader[$val]['max_toleransi']			= $qHeader[0]->max_toleransi;
			$ArrBqHeader[$val]['waste']					= $qHeader[0]->waste;
			$ArrBqHeader[$val]['area']				= $qHeader[0]->area;
			$ArrBqHeader[$val]['wrap_length']		= $qHeader[0]->wrap_length;
			$ArrBqHeader[$val]['wrap_length2']		= $qHeader[0]->wrap_length2;
			$ArrBqHeader[$val]['high']				= $qHeader[0]->high;
			$ArrBqHeader[$val]['area2']				= $qHeader[0]->area2;
			$ArrBqHeader[$val]['panjang_neck_1']	= $qHeader[0]->panjang_neck_1;
			$ArrBqHeader[$val]['panjang_neck_2']	= $qHeader[0]->panjang_neck_2;
			$ArrBqHeader[$val]['design_neck_1']		= $qHeader[0]->design_neck_1;
			$ArrBqHeader[$val]['design_neck_2']		= $qHeader[0]->design_neck_2;
			$ArrBqHeader[$val]['est_neck_1']		= $qHeader[0]->est_neck_1;
			$ArrBqHeader[$val]['est_neck_2']		= $qHeader[0]->est_neck_2;
			$ArrBqHeader[$val]['area_neck_1']		= $qHeader[0]->area_neck_1;
			$ArrBqHeader[$val]['area_neck_2']		= $qHeader[0]->area_neck_2;
			$ArrBqHeader[$val]['flange_od']			= $qHeader[0]->flange_od;
			$ArrBqHeader[$val]['flange_bcd']		= $qHeader[0]->flange_bcd;
			$ArrBqHeader[$val]['flange_n']			= $qHeader[0]->flange_n;
			$ArrBqHeader[$val]['flange_oh']			= $qHeader[0]->flange_oh;
			$ArrBqHeader[$val]['rev']				= $qHeader[0]->rev;
			$ArrBqHeader[$val]['status']			= $qHeader[0]->status;
			$ArrBqHeader[$val]['approve_by']		= $qHeader[0]->approve_by;
			$ArrBqHeader[$val]['approve_date']		= $qHeader[0]->approve_date;
			$ArrBqHeader[$val]['approve_reason']	= $qHeader[0]->approve_reason;
			$ArrBqHeader[$val]['sts_price']			= $qHeader[0]->sts_price;
			$ArrBqHeader[$val]['sts_price_by']		= $qHeader[0]->sts_price_by;
			$ArrBqHeader[$val]['sts_price_date']	= $qHeader[0]->sts_price_date;
			$ArrBqHeader[$val]['sts_price_reason']	= $qHeader[0]->sts_price_reason;
			$ArrBqHeader[$val]['created_by']		= $qHeader[0]->created_by;
			$ArrBqHeader[$val]['created_date']		= $qHeader[0]->created_date;
			$ArrBqHeader[$val]['deleted']			= $qHeader[0]->deleted;
			$ArrBqHeader[$val]['deleted_by']		= $qHeader[0]->deleted_by;
			$ArrBqHeader[$val]['deleted_date']		= $qHeader[0]->deleted_date;
			//
			$ArrBqHeader[$val]['pipe_thickness']		= $qHeader[0]->pipe_thickness;
			$ArrBqHeader[$val]['joint_thickness']		= $qHeader[0]->joint_thickness;
			$ArrBqHeader[$val]['factor_thickness']		= $qHeader[0]->factor_thickness;
			$ArrBqHeader[$val]['factor']			= $qHeader[0]->factor;
			
			
			//================================================================================================================
			//============================================DEFAULT BY ARWANT===================================================
			//================================================================================================================
			if(!empty($qHeader[0]->standart_code)){
				$getDefVal		= $this->db->query("SELECT * FROM bq_component_default WHERE id_product='".$valx['id_product']."' AND id_milik='".$valx['id_milik']."' LIMIT 1 ")->result();
				if(!empty($getDefVal)){
					$ArrBqDefault[$val]['id_product']				= $valx['id_product'];
					$ArrBqDefault[$val]['id_bq']					= $id_bq;
					$ArrBqDefault[$val]['id_milik']					= $valx['id'];
					$ArrBqDefault[$val]['product_parent']			= $getDefVal[0]->product_parent;
					$ArrBqDefault[$val]['kd_cust']					= $getDefVal[0]->kd_cust;
					$ArrBqDefault[$val]['customer']					= $getDefVal[0]->customer;
					$ArrBqDefault[$val]['standart_code']			= $getDefVal[0]->standart_code;
					$ArrBqDefault[$val]['diameter']					= $getDefVal[0]->diameter;
					$ArrBqDefault[$val]['diameter2']				= $getDefVal[0]->diameter2;
					$ArrBqDefault[$val]['liner']					= $getDefVal[0]->liner;
					$ArrBqDefault[$val]['pn']						= $getDefVal[0]->pn;
					$ArrBqDefault[$val]['overlap']					= $getDefVal[0]->overlap;
					$ArrBqDefault[$val]['waste']					= $getDefVal[0]->waste;
					$ArrBqDefault[$val]['waste_n1']					= $getDefVal[0]->waste_n1;
					$ArrBqDefault[$val]['waste_n2']					= $getDefVal[0]->waste_n2;
					$ArrBqDefault[$val]['max']						= $getDefVal[0]->max;
					$ArrBqDefault[$val]['min']						= $getDefVal[0]->min;
					$ArrBqDefault[$val]['plastic_film']				= $getDefVal[0]->plastic_film;
					$ArrBqDefault[$val]['lin_resin_veil_a']			= $getDefVal[0]->lin_resin_veil_a;
					$ArrBqDefault[$val]['lin_resin_veil_b']			= $getDefVal[0]->lin_resin_veil_b;
					$ArrBqDefault[$val]['lin_resin_veil']			= $getDefVal[0]->lin_resin_veil;
					$ArrBqDefault[$val]['lin_resin_veil_add_a']		= $getDefVal[0]->lin_resin_veil_add_a;
					$ArrBqDefault[$val]['lin_resin_veil_add_b']		= $getDefVal[0]->lin_resin_veil_add_b;
					$ArrBqDefault[$val]['lin_resin_veil_add']		= $getDefVal[0]->lin_resin_veil_add;
					$ArrBqDefault[$val]['lin_resin_csm_a']			= $getDefVal[0]->lin_resin_csm_a;
					$ArrBqDefault[$val]['lin_resin_csm_b']			= $getDefVal[0]->lin_resin_csm_b;
					$ArrBqDefault[$val]['lin_resin_csm']			= $getDefVal[0]->lin_resin_csm;
					$ArrBqDefault[$val]['lin_resin_csm_add_a']		= $getDefVal[0]->lin_resin_csm_add_a;
					$ArrBqDefault[$val]['lin_resin_csm_add_b']		= $getDefVal[0]->lin_resin_csm_add_b;
					$ArrBqDefault[$val]['lin_resin_csm_add']		= $getDefVal[0]->lin_resin_csm_add;
					$ArrBqDefault[$val]['lin_faktor_veil']			= $getDefVal[0]->lin_faktor_veil;
					$ArrBqDefault[$val]['lin_faktor_veil_add']		= $getDefVal[0]->lin_faktor_veil_add;
					$ArrBqDefault[$val]['lin_faktor_csm']			= $getDefVal[0]->lin_faktor_csm;
					$ArrBqDefault[$val]['lin_faktor_csm_add']		= $getDefVal[0]->lin_faktor_csm_add;
					$ArrBqDefault[$val]['lin_resin']				= $getDefVal[0]->lin_resin;
					$ArrBqDefault[$val]['lin_resin_thickness']		= $getDefVal[0]->lin_resin_thickness;
					$ArrBqDefault[$val]['str_resin_csm_a']			= $getDefVal[0]->str_resin_csm_a;
					$ArrBqDefault[$val]['str_resin_csm_b']			= $getDefVal[0]->str_resin_csm_b;
					$ArrBqDefault[$val]['str_resin_csm']			= $getDefVal[0]->str_resin_csm;
					$ArrBqDefault[$val]['str_resin_csm_add_a']		= $getDefVal[0]->str_resin_csm_add_a;
					$ArrBqDefault[$val]['str_resin_csm_add_b']		= $getDefVal[0]->str_resin_csm_add_b;
					$ArrBqDefault[$val]['str_resin_csm_add']		= $getDefVal[0]->str_resin_csm_add;
					$ArrBqDefault[$val]['str_resin_wr_a']			= $getDefVal[0]->str_resin_wr_a;
					$ArrBqDefault[$val]['str_resin_wr_b']			= $getDefVal[0]->str_resin_wr_b;
					$ArrBqDefault[$val]['str_resin_wr']				= $getDefVal[0]->str_resin_wr;
					$ArrBqDefault[$val]['str_resin_wr_add_a']		= $getDefVal[0]->str_resin_wr_add_a;
					$ArrBqDefault[$val]['str_resin_wr_add_b']		= $getDefVal[0]->str_resin_wr_add_b;
					$ArrBqDefault[$val]['str_resin_wr_add']			= $getDefVal[0]->str_resin_wr_add;
					$ArrBqDefault[$val]['str_resin_rv_a']			= $getDefVal[0]->str_resin_rv_a;
					$ArrBqDefault[$val]['str_resin_rv_b']			= $getDefVal[0]->str_resin_rv_b;
					$ArrBqDefault[$val]['str_resin_rv']				= $getDefVal[0]->str_resin_rv;
					$ArrBqDefault[$val]['str_resin_rv_add_a']		= $getDefVal[0]->str_resin_rv_add_a;
					$ArrBqDefault[$val]['str_resin_rv_add_b']		= $getDefVal[0]->str_resin_rv_add_b;
					$ArrBqDefault[$val]['str_resin_rv_add']			= $getDefVal[0]->str_resin_rv_add;
					$ArrBqDefault[$val]['str_faktor_csm']			= $getDefVal[0]->str_faktor_csm;
					$ArrBqDefault[$val]['str_faktor_csm_add']		= $getDefVal[0]->str_faktor_csm_add;
					$ArrBqDefault[$val]['str_faktor_wr']			= $getDefVal[0]->str_faktor_wr;
					$ArrBqDefault[$val]['str_faktor_wr_add']		= $getDefVal[0]->str_faktor_wr_add;
					$ArrBqDefault[$val]['str_faktor_rv']			= $getDefVal[0]->str_faktor_rv;
					$ArrBqDefault[$val]['str_faktor_rv_bw']			= $getDefVal[0]->str_faktor_rv_bw;
					$ArrBqDefault[$val]['str_faktor_rv_jb']			= $getDefVal[0]->str_faktor_rv_jb;
					$ArrBqDefault[$val]['str_faktor_rv_add']		= $getDefVal[0]->str_faktor_rv_add;
					$ArrBqDefault[$val]['str_faktor_rv_add_bw']		= $getDefVal[0]->str_faktor_rv_add_bw;
					$ArrBqDefault[$val]['str_faktor_rv_add_jb']		= $getDefVal[0]->str_faktor_rv_add_jb;
					$ArrBqDefault[$val]['str_resin']				= $getDefVal[0]->str_resin;
					$ArrBqDefault[$val]['str_resin_thickness']		= $getDefVal[0]->str_resin_thickness;
					$ArrBqDefault[$val]['eks_resin_veil_a']			= $getDefVal[0]->eks_resin_veil_a;
					$ArrBqDefault[$val]['eks_resin_veil_b']			= $getDefVal[0]->eks_resin_veil_b;
					$ArrBqDefault[$val]['eks_resin_veil']			= $getDefVal[0]->eks_resin_veil;
					$ArrBqDefault[$val]['eks_resin_veil_add_a']		= $getDefVal[0]->eks_resin_veil_add_a;
					$ArrBqDefault[$val]['eks_resin_veil_add_b']		= $getDefVal[0]->eks_resin_veil_add_b;
					$ArrBqDefault[$val]['eks_resin_veil_add']		= $getDefVal[0]->eks_resin_veil_add;
					$ArrBqDefault[$val]['eks_resin_csm_a']			= $getDefVal[0]->eks_resin_csm_a;
					$ArrBqDefault[$val]['eks_resin_csm_b']			= $getDefVal[0]->eks_resin_csm_b;
					$ArrBqDefault[$val]['eks_resin_csm']			= $getDefVal[0]->eks_resin_csm;
					$ArrBqDefault[$val]['eks_resin_csm_add_a']		= $getDefVal[0]->eks_resin_csm_add_a;
					$ArrBqDefault[$val]['eks_resin_csm_add_b']		= $getDefVal[0]->eks_resin_csm_add_b;
					$ArrBqDefault[$val]['eks_resin_csm_add']		= $getDefVal[0]->eks_resin_csm_add;
					$ArrBqDefault[$val]['eks_faktor_veil']			= $getDefVal[0]->eks_faktor_veil;
					$ArrBqDefault[$val]['eks_faktor_veil_add']		= $getDefVal[0]->eks_faktor_veil_add;
					$ArrBqDefault[$val]['eks_faktor_csm']			= $getDefVal[0]->eks_faktor_csm;
					$ArrBqDefault[$val]['eks_faktor_csm_add']		= $getDefVal[0]->eks_faktor_csm_add;
					$ArrBqDefault[$val]['eks_resin']				= $getDefVal[0]->eks_resin;
					$ArrBqDefault[$val]['eks_resin_thickness']		= $getDefVal[0]->eks_resin_thickness;
					$ArrBqDefault[$val]['topcoat_resin']			= $getDefVal[0]->topcoat_resin;
					$ArrBqDefault[$val]['str_n1_resin_csm_a']		= $getDefVal[0]->str_n1_resin_csm_a;
					$ArrBqDefault[$val]['str_n1_resin_csm_b']		= $getDefVal[0]->str_n1_resin_csm_b;
					$ArrBqDefault[$val]['str_n1_resin_csm']			= $getDefVal[0]->str_n1_resin_csm;
					$ArrBqDefault[$val]['str_n1_resin_csm_add_a']	= $getDefVal[0]->str_n1_resin_csm_add_a;
					$ArrBqDefault[$val]['str_n1_resin_csm_add_b']	= $getDefVal[0]->str_n1_resin_csm_add_b;
					$ArrBqDefault[$val]['str_n1_resin_csm_add']		= $getDefVal[0]->str_n1_resin_csm_add;
					$ArrBqDefault[$val]['str_n1_resin_wr_a']		= $getDefVal[0]->str_n1_resin_wr_a;
					$ArrBqDefault[$val]['str_n1_resin_wr_b']		= $getDefVal[0]->str_n1_resin_wr_b;
					$ArrBqDefault[$val]['str_n1_resin_wr']			= $getDefVal[0]->str_n1_resin_wr;
					$ArrBqDefault[$val]['str_n1_resin_wr_add_a']	= $getDefVal[0]->str_n1_resin_wr_add_a;
					$ArrBqDefault[$val]['str_n1_resin_wr_add_b']	= $getDefVal[0]->str_n1_resin_wr_add_b;
					$ArrBqDefault[$val]['str_n1_resin_wr_add']		= $getDefVal[0]->str_n1_resin_wr_add;
					$ArrBqDefault[$val]['str_n1_resin_rv_a']		= $getDefVal[0]->str_n1_resin_rv_a;
					$ArrBqDefault[$val]['str_n1_resin_rv_b']		= $getDefVal[0]->str_n1_resin_rv_b;
					$ArrBqDefault[$val]['str_n1_resin_rv']			= $getDefVal[0]->str_n1_resin_rv;
					$ArrBqDefault[$val]['str_n1_resin_rv_add_a']	= $getDefVal[0]->str_n1_resin_rv_add_a;
					$ArrBqDefault[$val]['str_n1_resin_rv_add_b']	= $getDefVal[0]->str_n1_resin_rv_add_b;
					$ArrBqDefault[$val]['str_n1_resin_rv_add']		= $getDefVal[0]->str_n1_resin_rv_add;
					$ArrBqDefault[$val]['str_n1_faktor_csm']		= $getDefVal[0]->str_n1_faktor_csm;
					$ArrBqDefault[$val]['str_n1_faktor_csm_add']	= $getDefVal[0]->str_n1_faktor_csm_add;
					$ArrBqDefault[$val]['str_n1_faktor_wr']			= $getDefVal[0]->str_n1_faktor_wr;
					$ArrBqDefault[$val]['str_n1_faktor_wr_add']		= $getDefVal[0]->str_n1_faktor_wr_add;
					$ArrBqDefault[$val]['str_n1_faktor_rv']			= $getDefVal[0]->str_n1_faktor_rv;
					$ArrBqDefault[$val]['str_n1_faktor_rv_bw']		= $getDefVal[0]->str_n1_faktor_rv_bw;
					$ArrBqDefault[$val]['str_n1_faktor_rv_jb']		= $getDefVal[0]->str_n1_faktor_rv_jb;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add']		= $getDefVal[0]->str_n1_faktor_rv_add;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add_bw']	= $getDefVal[0]->str_n1_faktor_rv_add_bw;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add_jb']	= $getDefVal[0]->str_n1_faktor_rv_add_jb;
					$ArrBqDefault[$val]['str_n1_resin']				= $getDefVal[0]->str_n1_resin;
					$ArrBqDefault[$val]['str_n1_resin_thickness']	= $getDefVal[0]->str_n1_resin_thickness;
					$ArrBqDefault[$val]['str_n2_resin_csm_a']		= $getDefVal[0]->str_n2_resin_csm_a;
					$ArrBqDefault[$val]['str_n2_resin_csm_b']		= $getDefVal[0]->str_n2_resin_csm_b;
					$ArrBqDefault[$val]['str_n2_resin_csm']			= $getDefVal[0]->str_n2_resin_csm;
					$ArrBqDefault[$val]['str_n2_resin_csm_add_a']	= $getDefVal[0]->str_n2_resin_csm_add_a;
					$ArrBqDefault[$val]['str_n2_resin_csm_add_b']	= $getDefVal[0]->str_n2_resin_csm_add_b;
					$ArrBqDefault[$val]['str_n2_resin_csm_add']		= $getDefVal[0]->str_n2_resin_csm_add;
					$ArrBqDefault[$val]['str_n2_resin_wr_a']		= $getDefVal[0]->str_n2_resin_wr_a;
					$ArrBqDefault[$val]['str_n2_resin_wr_b']		= $getDefVal[0]->str_n2_resin_wr_b;
					$ArrBqDefault[$val]['str_n2_resin_wr']			= $getDefVal[0]->str_n2_resin_wr;
					$ArrBqDefault[$val]['str_n2_resin_wr_add_a']	= $getDefVal[0]->str_n2_resin_wr_add_a;
					$ArrBqDefault[$val]['str_n2_resin_wr_add_b']	= $getDefVal[0]->str_n2_resin_wr_add_b;
					$ArrBqDefault[$val]['str_n2_resin_wr_add']		= $getDefVal[0]->str_n2_resin_wr_add;
					$ArrBqDefault[$val]['str_n2_faktor_csm']		= $getDefVal[0]->str_n2_faktor_csm;
					$ArrBqDefault[$val]['str_n2_faktor_csm_add']	= $getDefVal[0]->str_n2_faktor_csm_add;
					$ArrBqDefault[$val]['str_n2_faktor_wr']			= $getDefVal[0]->str_n2_faktor_wr;
					$ArrBqDefault[$val]['str_n2_faktor_wr_add']		= $getDefVal[0]->str_n2_faktor_wr_add;
					$ArrBqDefault[$val]['str_n2_resin']				= $getDefVal[0]->str_n2_resin;
					$ArrBqDefault[$val]['str_n2_resin_thickness']	= $getDefVal[0]->str_n2_resin_thickness;
					$ArrBqDefault[$val]['created_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefault[$val]['created_date']				= date('Y-m-d H:i:s');
				}
			}
			
			//Insert Component Header To Hist
			$qHeaderHistDef		= $this->db->query("SELECT * FROM so_component_default WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qHeaderHistDef)){
				foreach($qHeaderHistDef AS $val2HistADef => $valx2HistADef){
					$ArrBqDefaultHist[$val2HistADef]['id_product']				= $valx2HistADef['id_product'];
					$ArrBqDefaultHist[$val2HistADef]['id_milik']				= $valx2HistADef['id_milik'];
					$ArrBqDefaultHist[$val2HistADef]['id_bq']					= $valx2HistADef['id_bq'];
					$ArrBqDefaultHist[$val2HistADef]['product_parent']			= $valx2HistADef['product_parent'];
					$ArrBqDefaultHist[$val2HistADef]['kd_cust']					= $valx2HistADef['kd_cust'];
					$ArrBqDefaultHist[$val2HistADef]['customer']				= $valx2HistADef['customer'];
					$ArrBqDefaultHist[$val2HistADef]['standart_code']			= $valx2HistADef['standart_code'];
					$ArrBqDefaultHist[$val2HistADef]['diameter']				= $valx2HistADef['diameter'];
					$ArrBqDefaultHist[$val2HistADef]['diameter2']				= $valx2HistADef['diameter2'];
					$ArrBqDefaultHist[$val2HistADef]['liner']					= $valx2HistADef['liner'];
					$ArrBqDefaultHist[$val2HistADef]['pn']						= $valx2HistADef['pn'];
					$ArrBqDefaultHist[$val2HistADef]['overlap']					= $valx2HistADef['overlap'];
					$ArrBqDefaultHist[$val2HistADef]['waste']					= $valx2HistADef['waste'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n1']				= $valx2HistADef['waste_n1'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n2']				= $valx2HistADef['waste_n2'];
					$ArrBqDefaultHist[$val2HistADef]['max']						= $valx2HistADef['max'];
					$ArrBqDefaultHist[$val2HistADef]['min']						= $valx2HistADef['min'];
					$ArrBqDefaultHist[$val2HistADef]['plastic_film']			= $valx2HistADef['plastic_film'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_a']		= $valx2HistADef['lin_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_b']		= $valx2HistADef['lin_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil']			= $valx2HistADef['lin_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_a']	= $valx2HistADef['lin_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_b']	= $valx2HistADef['lin_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add']		= $valx2HistADef['lin_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_a']			= $valx2HistADef['lin_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_b']			= $valx2HistADef['lin_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm']			= $valx2HistADef['lin_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_a']		= $valx2HistADef['lin_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_b']		= $valx2HistADef['lin_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add']		= $valx2HistADef['lin_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil']			= $valx2HistADef['lin_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil_add']		= $valx2HistADef['lin_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm']			= $valx2HistADef['lin_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm_add']		= $valx2HistADef['lin_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin']				= $valx2HistADef['lin_resin'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_thickness']		= $valx2HistADef['lin_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_a']			= $valx2HistADef['str_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_b']			= $valx2HistADef['str_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm']			= $valx2HistADef['str_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_a']		= $valx2HistADef['str_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_b']		= $valx2HistADef['str_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add']		= $valx2HistADef['str_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_a']			= $valx2HistADef['str_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_b']			= $valx2HistADef['str_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr']			= $valx2HistADef['str_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_a']		= $valx2HistADef['str_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_b']		= $valx2HistADef['str_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add']		= $valx2HistADef['str_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_a']			= $valx2HistADef['str_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_b']			= $valx2HistADef['str_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv']			= $valx2HistADef['str_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_a']		= $valx2HistADef['str_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_b']		= $valx2HistADef['str_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add']		= $valx2HistADef['str_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm']			= $valx2HistADef['str_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm_add']		= $valx2HistADef['str_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr']			= $valx2HistADef['str_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr_add']		= $valx2HistADef['str_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv']			= $valx2HistADef['str_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_bw']		= $valx2HistADef['str_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_jb']		= $valx2HistADef['str_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add']		= $valx2HistADef['str_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_bw']	= $valx2HistADef['str_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_jb']	= $valx2HistADef['str_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin']				= $valx2HistADef['str_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_thickness']		= $valx2HistADef['str_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_a']		= $valx2HistADef['eks_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_b']		= $valx2HistADef['eks_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil']			= $valx2HistADef['eks_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_a']	= $valx2HistADef['eks_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_b']	= $valx2HistADef['eks_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add']		= $valx2HistADef['eks_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_a']			= $valx2HistADef['eks_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_b']			= $valx2HistADef['eks_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm']			= $valx2HistADef['eks_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_a']		= $valx2HistADef['eks_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_b']		= $valx2HistADef['eks_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add']		= $valx2HistADef['eks_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil']			= $valx2HistADef['eks_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil_add']		= $valx2HistADef['eks_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm']			= $valx2HistADef['eks_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm_add']		= $valx2HistADef['eks_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin']				= $valx2HistADef['eks_resin'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_thickness']		= $valx2HistADef['eks_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['topcoat_resin']			= $valx2HistADef['topcoat_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_a']		= $valx2HistADef['str_n1_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_b']		= $valx2HistADef['str_n1_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm']		= $valx2HistADef['str_n1_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_a']	= $valx2HistADef['str_n1_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_b']	= $valx2HistADef['str_n1_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add']	= $valx2HistADef['str_n1_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_a']		= $valx2HistADef['str_n1_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_b']		= $valx2HistADef['str_n1_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr']			= $valx2HistADef['str_n1_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_a']	= $valx2HistADef['str_n1_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_b']	= $valx2HistADef['str_n1_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add']		= $valx2HistADef['str_n1_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_a']		= $valx2HistADef['str_n1_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_b']		= $valx2HistADef['str_n1_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv']			= $valx2HistADef['str_n1_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_a']	= $valx2HistADef['str_n1_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_b']	= $valx2HistADef['str_n1_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add']		= $valx2HistADef['str_n1_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm']		= $valx2HistADef['str_n1_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm_add']	= $valx2HistADef['str_n1_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr']		= $valx2HistADef['str_n1_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr_add']	= $valx2HistADef['str_n1_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv']		= $valx2HistADef['str_n1_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_bw']		= $valx2HistADef['str_n1_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_jb']		= $valx2HistADef['str_n1_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add']	= $valx2HistADef['str_n1_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_bw']	= $valx2HistADef['str_n1_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_jb']	= $valx2HistADef['str_n1_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin']			= $valx2HistADef['str_n1_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_thickness']	= $valx2HistADef['str_n1_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_a']		= $valx2HistADef['str_n2_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_b']		= $valx2HistADef['str_n2_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm']		= $valx2HistADef['str_n2_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_a']	= $valx2HistADef['str_n2_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_b']	= $valx2HistADef['str_n2_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add']	= $valx2HistADef['str_n2_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_a']		= $valx2HistADef['str_n2_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_b']		= $valx2HistADef['str_n2_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr']			= $valx2HistADef['str_n2_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_a']	= $valx2HistADef['str_n2_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_b']	= $valx2HistADef['str_n2_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add']		= $valx2HistADef['str_n2_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm']		= $valx2HistADef['str_n2_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm_add']	= $valx2HistADef['str_n2_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr']		= $valx2HistADef['str_n2_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr_add']	= $valx2HistADef['str_n2_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin']			= $valx2HistADef['str_n2_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_thickness']	= $valx2HistADef['str_n2_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['created_by']				= $valx2HistADef['created_by'];
					$ArrBqDefaultHist[$val2HistADef]['created_date']			= $valx2HistADef['created_date'];
					$ArrBqDefaultHist[$val2HistADef]['modified_by']				= $valx2HistADef['modified_by'];
					$ArrBqDefaultHist[$val2HistADef]['modified_date']			= $valx2HistADef['modified_date'];
					$ArrBqDefaultHist[$val2HistADef]['hist_by']					= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefaultHist[$val2HistADef]['hist_date']				= date('Y-m-d H:i:s');
					
					
				}
			}
			//================================================================================================================
			//================================================================================================================
			//================================================================================================================
			
			//Insert Component Header To Hist
			$qHeaderHist	= $this->db->query("SELECT * FROM so_component_header WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qHeaderHist)){
				foreach($qHeaderHist AS $val2HistA => $valx2HistA){
					$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
					$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
					$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
					$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
					$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
					$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
					$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
					$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
					$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
					$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
					$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
					$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
					$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
					$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
					$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
					$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
					$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
					$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
					$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
					$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
					$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
					$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
					$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
					$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
					$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length']			= $valx2HistA['wrap_length'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length2']		= $valx2HistA['wrap_length2'];
					$ArrBqHeaderHist[$val2HistA]['high']				= $valx2HistA['high'];
					$ArrBqHeaderHist[$val2HistA]['area2']				= $valx2HistA['area2'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
					$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
					$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
					$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
					$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
					$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
					$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
					$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
					$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
					$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
					$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
					$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
					$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
					$ArrBqHeaderHist[$val2HistA]['deleted_by']			= $valx2HistA['deleted_by'];
					$ArrBqHeaderHist[$val2HistA]['deleted_date']		= $valx2HistA['deleted_date'];
					$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
					
				}
			}

			//Component Detail
			$qDetail	= $this->db->query("SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$valx['id_product']."' AND a.id_milik='".$valx['id_milik']."' ")->result_array();
			foreach($qDetail AS $val2 => $valx2){
				$LoopDetail++;
				$ArrBqDetail[$LoopDetail]['id_product']		= $valx['id_product'];
				$ArrBqDetail[$LoopDetail]['id_bq']			= $id_bq;
				$ArrBqDetail[$LoopDetail]['id_milik']		= $valx['id'];
				$ArrBqDetail[$LoopDetail]['detail_name']	= $valx2['detail_name'];
				$ArrBqDetail[$LoopDetail]['acuhan']			= $valx2['acuhan'];
				$ArrBqDetail[$LoopDetail]['id_ori']			= $valx2['id_ori'];
				$ArrBqDetail[$LoopDetail]['id_ori2']		= $valx2['id_ori2'];
				$ArrBqDetail[$LoopDetail]['id_category']	= $valx2['id_category'];
				$ArrBqDetail[$LoopDetail]['nm_category']	= $valx2['nm_category'];
				$ArrBqDetail[$LoopDetail]['id_material']	= $valx2['id_material'];
				$ArrBqDetail[$LoopDetail]['nm_material']	= $valx2['nm_material'];
				$ArrBqDetail[$LoopDetail]['value']			= $valx2['value'];
				$ArrBqDetail[$LoopDetail]['thickness']		= $valx2['thickness'];
				$ArrBqDetail[$LoopDetail]['fak_pengali']	= $valx2['fak_pengali'];
				$ArrBqDetail[$LoopDetail]['bw']				= $valx2['bw'];
				$ArrBqDetail[$LoopDetail]['jumlah']			= $valx2['jumlah'];
				$ArrBqDetail[$LoopDetail]['layer']			= $valx2['layer'];
				$ArrBqDetail[$LoopDetail]['containing']		= $valx2['containing'];
				$ArrBqDetail[$LoopDetail]['total_thickness']	= $valx2['total_thickness'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / floatval($PANJANG_BEF))* (floatval($valx['panjang']) + 400);
				}
				elseif ($qHeader[0]->parent_product == 'branch joint' OR $qHeader[0]->parent_product == 'field joint' OR $qHeader[0]->parent_product == 'shop joint') {
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['material_weight'];
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / $valx['panjang']) * floatval($valx['panjang']);
				}
				else{
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['last_cost'];
				}
				$ArrBqDetail[$LoopDetail]['rev']				= $qHeader[0]->rev;
				//
				$ArrBqDetail[$LoopDetail]['area_weight']		= $valx2['area_weight'];
				$ArrBqDetail[$LoopDetail]['material_weight']	= $valx2['material_weight'];
				$ArrBqDetail[$LoopDetail]['percentage']			= $valx2['percentage'];
				$ArrBqDetail[$LoopDetail]['resin_content']		= $valx2['resin_content'];

				$ArrBqDetail[$LoopDetail]['price_mat']		= $valx2['price_mat'];
			}

			//Component Lamination
			$qDetailLam	= $this->db->query("SELECT * FROM bq_component_lamination WHERE id_product='".$valx['id_product']."' AND id_milik='".$valx['id_milik']."' ")->result_array();
			foreach($qDetailLam AS $val2 => $valx2){
				$LoopDetailLam++;
				$ArrBqDetailLam[$LoopDetailLam]['id_product']		= $valx['id_product'];
				$ArrBqDetailLam[$LoopDetailLam]['id_bq']				= $id_bq;
				$ArrBqDetailLam[$LoopDetailLam]['id_milik']			= $valx['id'];
				$ArrBqDetailLam[$LoopDetailLam]['detail_name']		= $valx2['detail_name'];
				$ArrBqDetailLam[$LoopDetailLam]['lapisan']			= $valx2['lapisan'];
				$ArrBqDetailLam[$LoopDetailLam]['std_glass']			= $valx2['std_glass'];
				$ArrBqDetailLam[$LoopDetailLam]['width']			= $valx2['width'];
				$ArrBqDetailLam[$LoopDetailLam]['stage']		= $valx2['stage'];
				$ArrBqDetailLam[$LoopDetailLam]['glass']		= $valx2['glass'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_1']		= $valx2['thickness_1'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_2']		= $valx2['thickness_2'];
				$ArrBqDetailLam[$LoopDetailLam]['glass_length']			= $valx2['glass_length'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_veil']		= $valx2['weight_veil'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_csm']		= $valx2['weight_csm'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_wr']				= $valx2['weight_wr'];
			}

			//Insert Component Detail To Hist
			$qDetailHist	= $this->db->query("SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailHist)){
				foreach($qDetailHist AS $val2Hist => $valx2Hist){
					$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
					$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
					$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
					$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
					$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
					$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
					$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
					$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
					$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
					$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
					$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
					$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
					$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
					$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
					$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
					$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
					$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
					$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
					$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
					$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
					$ArrBqDetailHist[$val2Hist]['rev']				= $valx2Hist['rev'];
					$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
					$ArrBqDetailHist[$val2Hist]['hist_by']			= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Plus
			$qDetailPlus	= $this->db->query("SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$valx['id_product']."' AND a.id_milik='".$valx['id_milik']."' ")->result_array();
			foreach($qDetailPlus AS $val3 => $valx3){
				$LoopDetailPlus++;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_product']		= $valx['id_product'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_bq']			= $id_bq;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_milik']			= $valx['id'];
				$ArrBqDetailPlus[$LoopDetailPlus]['detail_name']		= $valx3['detail_name'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori']			= $valx3['id_ori'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori2']			= $valx3['id_ori2'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_category']		= $valx3['id_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_category']		= $valx3['nm_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_material']		= $valx3['id_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_material']		= $valx3['nm_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['containing']		= $valx3['containing'];
				$ArrBqDetailPlus[$LoopDetailPlus]['perse']			= $valx3['perse'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($PANJANG_BEF)) * (floatval($valx['panjang']) + 400);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($PANJANG_BEF)) * (floatval($valx['panjang']) + 400);
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($valx['panjang'])) * floatval($valx['panjang']);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($valx['panjang'])) * floatval($valx['panjang']);
				}
				else{
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= $valx3['last_full'];
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= $valx3['last_cost'];
				}
				$ArrBqDetailPlus[$LoopDetailPlus]['rev']				= $qHeader[0]->rev;
				$ArrBqDetailPlus[$LoopDetailPlus]['price_mat']		= $valx3['price_mat'];
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailPlusHist)){
				foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
					$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
					$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
					$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
					$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
					$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
					$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
					$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
					$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
					$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
					$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
					$ArrBqDetailPlusHist[$val3Hist]['rev']			= $valx3Hist['rev'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
					$ArrBqDetailPlusHist[$val3Hist]['price_mat']		= $valx3Hist['price_mat'];
				}
			}

			//Component Detail Add
			$qDetailAdd		= $this->db->query("SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$valx['id_product']."' AND a.id_milik='".$valx['id_milik']."' ")->result_array();
			if(!empty($qDetailAdd)){
				foreach($qDetailAdd AS $val4 => $valx4){
					$LoopDetailAdd++;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_product']		= $valx['id_product'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_bq']				= $id_bq;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_milik']			= $valx['id'];
					$ArrBqDetailAdd[$LoopDetailAdd]['detail_name']		= $valx4['detail_name'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_category']		= $valx4['id_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_category']		= $valx4['nm_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_material']		= $valx4['id_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_material']		= $valx4['nm_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['containing']		= $valx4['containing'];
					$ArrBqDetailAdd[$LoopDetailAdd]['perse']			= $valx4['perse'];
					if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= (floatval($valx4['last_full']) / floatval($PANJANG_BEF)) * (floatval($valx['panjang']) + 400);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= (floatval($valx4['last_cost']) / floatval($PANJANG_BEF)) * (floatval($valx['panjang']) + 400);
					}
					elseif($qHeader[0]->parent_product == 'frp pipe'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= (floatval($valx4['last_full']) / floatval($valx['panjang'])) * floatval($valx['panjang']);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= (floatval($valx4['last_cost']) / floatval($valx['panjang'])) * floatval($valx['panjang']);
					}
					else{
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= $valx4['last_full'];
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= $valx4['last_cost'];
					}
					$ArrBqDetailAdd[$LoopDetailAdd]['rev']				= $qHeader[0]->rev;
					$ArrBqDetailAdd[$LoopDetailAdd]['price_mat']		= $valx4['price_mat'];
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailAddHist)){
				foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
					$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
					$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
					$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
					$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
					$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
					$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
					$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
					$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
					$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
					$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
					$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
					$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
					$ArrBqDetailAddHist[$val4Hist]['rev']			= $valx4Hist['rev'];
					$ArrBqDetailAddHist[$val4Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailAddHist[$val4Hist]['hist_date']		= date('Y-m-d H:i:s');
					$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				}
			}

			//Component Footer
			$qDetailFooter	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_product='".$valx['id_product']."' AND id_milik='".$valx['id_milik']."' ")->result_array();
			if(!empty($qDetailFooter)){
				foreach($qDetailFooter AS $val5 => $valx5){
					$LoopFooter++;
					$ArrBqFooter[$LoopFooter]['id_product']	= $valx['id_product'];
					$ArrBqFooter[$LoopFooter]['id_bq']		= $id_bq;
					$ArrBqFooter[$LoopFooter]['id_milik']		= $valx['id'];
					$ArrBqFooter[$LoopFooter]['detail_name']	= $valx5['detail_name'];
					$ArrBqFooter[$LoopFooter]['total']		= $valx5['total'];
					$ArrBqFooter[$LoopFooter]['min']			= $valx5['min'];
					$ArrBqFooter[$LoopFooter]['max']			= $valx5['max'];
					$ArrBqFooter[$LoopFooter]['hasil']		= $valx5['hasil'];
					$ArrBqFooter[$LoopFooter]['rev']			= $qHeader[0]->rev;
				}
			}
			//Insert Component Footer To Hist
			$qDetailFooterHist		= $this->db->query("SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailFooterHist)){
				foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
					$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
					$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
					$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
					$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
					$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
					$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
					$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
					$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
					$ArrBqFooterHist[$val5Hist]['rev']			= $valx5Hist['rev'];
					$ArrBqFooterHist[$val5Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}
			
		} 

		// print_r($ArrBqDefault);
		// print_r($ArrBqDetail);
		// print_r($ArrBqDetailPlus);
		// print_r($ArrBqDetailAdd);
		// print_r($ArrBqFooter);
		// echo $qDetailAddNum2;
		// echo $qDetailHeaderNum2;
		// echo $qDetailDetailNum2;
		// echo $qDetailDetailPlusNum2;
		// echo $qDetailDetailAddNum2;
		// echo $qDetailDetailFooterNum2;
		// echo "</pre>";
		// exit;

		$UpdateBQ	= array(
			'estimasi'	=> 'Y',
			'est_by'	=> $this->session->userdata['ORI_User']['username'],
			'est_date'	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->update_batch('so_detail_header', $ArrDetBq2, 'id');

			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_so_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_so_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(count($ArrBqFooterHist)>0){
			// 	$this->db->insert_batch('hist_so_component_footer', $ArrBqFooterHist);
			// }
			// if(!empty($ArrBqDefaultHist)){
			// 	$this->db->insert_batch('hist_so_component_default', $ArrBqDefaultHist);
			// }

			//Delete BQ Component
			foreach($dtListArray AS $val => $valx){ 
				$this->db->delete('so_component_header', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_detail', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_lamination', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_detail_plus', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_detail_add', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_footer', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_default', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
			}
			
			// $this->db->delete('so_component_header', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_detail', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_lamination', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_detail_plus', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_detail_add', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_footer', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_default', array('id_bq' => $id_bq));

			//Insert BQ Component
			if(!empty($ArrBqHeader)){
				$this->db->insert_batch('so_component_header', $ArrBqHeader);
			}
			if(!empty($ArrBqDetail)){
				$this->db->insert_batch('so_component_detail', $ArrBqDetail);
			}
			if(!empty($ArrBqDetailLam)){
				$this->db->insert_batch('so_component_lamination', $ArrBqDetailLam);
			}
			if(!empty($ArrBqDetailPlus)){
				$this->db->insert_batch('so_component_detail_plus', $ArrBqDetailPlus);
			}
			if(!empty($ArrBqDetailAdd)){
				$this->db->insert_batch('so_component_detail_add', $ArrBqDetailAdd);
			}
			if(!empty($ArrBqFooter)){
				$this->db->insert_batch('so_component_footer', $ArrBqFooter);
			}
			if(!empty($ArrBqDefault)){
				$this->db->insert_batch('so_component_default', $ArrBqDefault);
			}

			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $UpdateBQ);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $pembeda,
				'pesan'		=>'Estimation structure bq data success. Thanks ...',
				'status'	=> 1
			);
			history('Estimation Structure BQ (tarik bq) in Final Drawing with code : '.$id_bq.'/'.$no_ipp);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function update_est_get_master(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		
		$id_bq			= "BQ-".$data['no_ipp'];
		$no_ipp			= $data['no_ipp'];
		$pembeda		= $data['pembeda'];
		
		$chOri			= $data['check'];
		$check 			= $chOri;
		$detailBQ		= $data['detailBQ'];
		$Arr0 = array();
		foreach($check AS $vaxl){
			$valG = explode('-', $vaxl);
			$Arr0[$vaxl] = $valG[0];
			$Arr1[$vaxl] = $valG[1];
		}
		$dtImplode		= "('".implode("','", $Arr0)."')";
		// $dtImplode2		= "('".implode("','", $Arr1)."')";
		// print_r($check);
		// echo $dtImplode;
		// echo $dtImplode2;
		// exit;

		$dtListArray 	= array();
		foreach($check AS $valT ){
			foreach($detailBQ AS $val => $valx){
				$valG = explode('-', $valT);
				if($valx['id'] == $valG[0]){
					$dtListArray[$val]['id'] = $valx['id'];
					$dtListArray[$val]['panjang'] = $valx['panjang'];
					$dtListArray[$val]['id_productx'] = $valx['id_productx'];
				}
			}
		}
		// print_r($dtListArray);
		// exit;
		
		$ArrDetBq		= array();
		foreach($dtListArray AS $val => $valx){
			if(!empty($valx["id_productx"]))
			{
				$ArrDetBq[$val]['id']	= $valx['id'];
				$ArrDetBq[$val]['id_product']	= $valx['id_productx'];
				$ArrDetBq[$val]['panjang']	= $valx['panjang'];
			}
		}

		$ArrDetBq2		= array();
		foreach($dtListArray AS $val => $valx){
			if(!empty($valx["id_productx"]))
			{
				$ArrDetBq2[$val]['id']	= $valx['id'];
				$ArrDetBq2[$val]['id_product']	= $valx['id_productx'];
			}
		}

		$ArrBqHeader			= array();
		$ArrBqDetail			= array();
		$ArrBqDetailPlus		= array();
		$ArrBqDetailAdd			= array();
		$ArrBqFooter			= array();
		$ArrBqHeaderHist		= array();
		$ArrBqDetailHist		= array();
		$ArrBqDetailPlusHist	= array();
		$ArrBqDetailAddHist		= array();
		$ArrBqFooterHist		= array();
		$ArrBqDefault			= array();
		$ArrBqDefaultHist		= array();

		$LoopDetail = 0;
		$LoopDetailLam = 0;
		$LoopDetailPlus = 0;
		$LoopDetailAdd = 0;
		$LoopFooter = 0;
		foreach($ArrDetBq AS $val => $valx){
			//Component Header
			$qHeader	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$valx['id_product']."' LIMIT 1 ")->result();
			$ArrBqHeader[$val]['id_product']			= $valx['id_product'];
			$ArrBqHeader[$val]['id_bq']					= $id_bq;
			$ArrBqHeader[$val]['id_milik']				= $valx['id'];
			$ArrBqHeader[$val]['parent_product']		= $qHeader[0]->parent_product;
			$ArrBqHeader[$val]['nm_product']			= $qHeader[0]->nm_product;
			$ArrBqHeader[$val]['standart_code']			= $qHeader[0]->standart_code;
			$ArrBqHeader[$val]['series']				= $qHeader[0]->series;
			$ArrBqHeader[$val]['resin_sistem']			= $qHeader[0]->resin_sistem;
			$ArrBqHeader[$val]['pressure']				= $qHeader[0]->pressure;
			$ArrBqHeader[$val]['diameter']				= $qHeader[0]->diameter;
			$ArrBqHeader[$val]['liner']					= $qHeader[0]->liner;
			$ArrBqHeader[$val]['aplikasi_product']		= $qHeader[0]->aplikasi_product;
			$ArrBqHeader[$val]['criminal_barier']		= $qHeader[0]->criminal_barier;
			$ArrBqHeader[$val]['vacum_rate']			= $qHeader[0]->vacum_rate;
			$ArrBqHeader[$val]['stiffness']				= $qHeader[0]->stiffness;
			$ArrBqHeader[$val]['design_life']			= $qHeader[0]->design_life;
			$ArrBqHeader[$val]['standart_by']			= $qHeader[0]->standart_by;
			$ArrBqHeader[$val]['standart_toleransi']	= $qHeader[0]->standart_toleransi;
			$ArrBqHeader[$val]['diameter2']				= $qHeader[0]->diameter2;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader[$val]['panjang']			= floatval($valx['panjang']) + 400;
			}
			else{
				$ArrBqHeader[$val]['panjang']			= $qHeader[0]->panjang;
			}
			$ArrBqHeader[$val]['radius']				= $qHeader[0]->radius;
			$ArrBqHeader[$val]['type_elbow']			= $qHeader[0]->type_elbow;
			$ArrBqHeader[$val]['angle']					= $qHeader[0]->angle;
			$ArrBqHeader[$val]['design']				= $qHeader[0]->design;
			$ArrBqHeader[$val]['est']					= $qHeader[0]->est;
			$ArrBqHeader[$val]['min_toleransi']			= $qHeader[0]->min_toleransi;
			$ArrBqHeader[$val]['max_toleransi']			= $qHeader[0]->max_toleransi;
			$ArrBqHeader[$val]['waste']					= $qHeader[0]->waste;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader[$val]['area']				= (floatval($qHeader[0]->area) / floatval($qHeader[0]->panjang)) * (floatval($valx['panjang']) + 400);
			}
			else{
				$ArrBqHeader[$val]['area']				= $qHeader[0]->area;
			}
			$ArrBqHeader[$val]['wrap_length']		= $qHeader[0]->wrap_length;
			$ArrBqHeader[$val]['wrap_length2']		= $qHeader[0]->wrap_length2;
			$ArrBqHeader[$val]['high']				= $qHeader[0]->high;
			$ArrBqHeader[$val]['area2']				= $qHeader[0]->area2;
			$ArrBqHeader[$val]['panjang_neck_1']	= $qHeader[0]->panjang_neck_1;
			$ArrBqHeader[$val]['panjang_neck_2']	= $qHeader[0]->panjang_neck_2;
			$ArrBqHeader[$val]['design_neck_1']		= $qHeader[0]->design_neck_1;
			$ArrBqHeader[$val]['design_neck_2']		= $qHeader[0]->design_neck_2;
			$ArrBqHeader[$val]['est_neck_1']		= $qHeader[0]->est_neck_1;
			$ArrBqHeader[$val]['est_neck_2']		= $qHeader[0]->est_neck_2;
			$ArrBqHeader[$val]['area_neck_1']		= $qHeader[0]->area_neck_1;
			$ArrBqHeader[$val]['area_neck_2']		= $qHeader[0]->area_neck_2;
			$ArrBqHeader[$val]['flange_od']			= $qHeader[0]->flange_od;
			$ArrBqHeader[$val]['flange_bcd']		= $qHeader[0]->flange_bcd;
			$ArrBqHeader[$val]['flange_n']			= $qHeader[0]->flange_n;
			$ArrBqHeader[$val]['flange_oh']			= $qHeader[0]->flange_oh;
			$ArrBqHeader[$val]['rev']				= $qHeader[0]->rev;
			$ArrBqHeader[$val]['status']			= $qHeader[0]->status;
			$ArrBqHeader[$val]['approve_by']		= $qHeader[0]->approve_by;
			$ArrBqHeader[$val]['approve_date']		= $qHeader[0]->approve_date;
			$ArrBqHeader[$val]['approve_reason']	= $qHeader[0]->approve_reason;
			$ArrBqHeader[$val]['sts_price']			= $qHeader[0]->sts_price;
			$ArrBqHeader[$val]['sts_price_by']		= $qHeader[0]->sts_price_by;
			$ArrBqHeader[$val]['sts_price_date']	= $qHeader[0]->sts_price_date;
			$ArrBqHeader[$val]['sts_price_reason']	= $qHeader[0]->sts_price_reason;
			$ArrBqHeader[$val]['created_by']		= $qHeader[0]->created_by;
			$ArrBqHeader[$val]['created_date']		= $qHeader[0]->created_date;
			$ArrBqHeader[$val]['deleted']			= $qHeader[0]->deleted;
			$ArrBqHeader[$val]['deleted_by']		= $qHeader[0]->deleted_by;
			$ArrBqHeader[$val]['deleted_date']		= $qHeader[0]->deleted_date;
			//
			$ArrBqHeader[$val]['pipe_thickness']		= $qHeader[0]->pipe_thickness;
			$ArrBqHeader[$val]['joint_thickness']		= $qHeader[0]->joint_thickness;
			$ArrBqHeader[$val]['factor_thickness']		= $qHeader[0]->factor_thickness;
			$ArrBqHeader[$val]['factor']			= $qHeader[0]->factor;
			
			
			//================================================================================================================
			//============================================DEFAULT BY ARWANT===================================================
			//================================================================================================================
			if(!empty($qHeader[0]->standart_code)){
				$plusSQL = "";
				if($qHeader[0]->parent_product == 'concentric reducer' OR $qHeader[0]->parent_product == 'reducer tee mould' OR $qHeader[0]->parent_product == 'eccentric reducer' OR $qHeader[0]->parent_product == 'reducer tee slongsong' OR $qHeader[0]->parent_product == 'branch joint'){
					$plusSQL = " AND diameter2='".$qHeader[0]->diameter2."'";
				}
				$getDefVal		= $this->db->query("SELECT * FROM help_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." LIMIT 1 ")->result();
				if(!empty($getDefVal)){
					$ArrBqDefault[$val]['id_product']				= $valx['id_product'];
					$ArrBqDefault[$val]['id_bq']					= $id_bq;
					$ArrBqDefault[$val]['id_milik']					= $valx['id'];
					$ArrBqDefault[$val]['product_parent']			= $getDefVal[0]->product_parent;
					$ArrBqDefault[$val]['kd_cust']					= $getDefVal[0]->kd_cust;
					$ArrBqDefault[$val]['customer']					= $getDefVal[0]->customer;
					$ArrBqDefault[$val]['standart_code']			= $getDefVal[0]->standart_code;
					$ArrBqDefault[$val]['diameter']					= $getDefVal[0]->diameter;
					$ArrBqDefault[$val]['diameter2']				= $getDefVal[0]->diameter2;
					$ArrBqDefault[$val]['liner']					= $getDefVal[0]->liner;
					$ArrBqDefault[$val]['pn']						= $getDefVal[0]->pn;
					$ArrBqDefault[$val]['overlap']					= $getDefVal[0]->overlap;
					$ArrBqDefault[$val]['waste']					= $getDefVal[0]->waste;
					$ArrBqDefault[$val]['waste_n1']					= $getDefVal[0]->waste_n1;
					$ArrBqDefault[$val]['waste_n2']					= $getDefVal[0]->waste_n2;
					$ArrBqDefault[$val]['max']						= $getDefVal[0]->max;
					$ArrBqDefault[$val]['min']						= $getDefVal[0]->min;
					$ArrBqDefault[$val]['plastic_film']				= $getDefVal[0]->plastic_film;
					$ArrBqDefault[$val]['lin_resin_veil_a']			= $getDefVal[0]->lin_resin_veil_a;
					$ArrBqDefault[$val]['lin_resin_veil_b']			= $getDefVal[0]->lin_resin_veil_b;
					$ArrBqDefault[$val]['lin_resin_veil']			= $getDefVal[0]->lin_resin_veil;
					$ArrBqDefault[$val]['lin_resin_veil_add_a']		= $getDefVal[0]->lin_resin_veil_add_a;
					$ArrBqDefault[$val]['lin_resin_veil_add_b']		= $getDefVal[0]->lin_resin_veil_add_b;
					$ArrBqDefault[$val]['lin_resin_veil_add']		= $getDefVal[0]->lin_resin_veil_add;
					$ArrBqDefault[$val]['lin_resin_csm_a']			= $getDefVal[0]->lin_resin_csm_a;
					$ArrBqDefault[$val]['lin_resin_csm_b']			= $getDefVal[0]->lin_resin_csm_b;
					$ArrBqDefault[$val]['lin_resin_csm']			= $getDefVal[0]->lin_resin_csm;
					$ArrBqDefault[$val]['lin_resin_csm_add_a']		= $getDefVal[0]->lin_resin_csm_add_a;
					$ArrBqDefault[$val]['lin_resin_csm_add_b']		= $getDefVal[0]->lin_resin_csm_add_b;
					$ArrBqDefault[$val]['lin_resin_csm_add']		= $getDefVal[0]->lin_resin_csm_add;
					$ArrBqDefault[$val]['lin_faktor_veil']			= $getDefVal[0]->lin_faktor_veil;
					$ArrBqDefault[$val]['lin_faktor_veil_add']		= $getDefVal[0]->lin_faktor_veil_add;
					$ArrBqDefault[$val]['lin_faktor_csm']			= $getDefVal[0]->lin_faktor_csm;
					$ArrBqDefault[$val]['lin_faktor_csm_add']		= $getDefVal[0]->lin_faktor_csm_add;
					$ArrBqDefault[$val]['lin_resin']				= $getDefVal[0]->lin_resin;
					$ArrBqDefault[$val]['lin_resin_thickness']		= $getDefVal[0]->lin_resin_thickness;
					$ArrBqDefault[$val]['str_resin_csm_a']			= $getDefVal[0]->str_resin_csm_a;
					$ArrBqDefault[$val]['str_resin_csm_b']			= $getDefVal[0]->str_resin_csm_b;
					$ArrBqDefault[$val]['str_resin_csm']			= $getDefVal[0]->str_resin_csm;
					$ArrBqDefault[$val]['str_resin_csm_add_a']		= $getDefVal[0]->str_resin_csm_add_a;
					$ArrBqDefault[$val]['str_resin_csm_add_b']		= $getDefVal[0]->str_resin_csm_add_b;
					$ArrBqDefault[$val]['str_resin_csm_add']		= $getDefVal[0]->str_resin_csm_add;
					$ArrBqDefault[$val]['str_resin_wr_a']			= $getDefVal[0]->str_resin_wr_a;
					$ArrBqDefault[$val]['str_resin_wr_b']			= $getDefVal[0]->str_resin_wr_b;
					$ArrBqDefault[$val]['str_resin_wr']				= $getDefVal[0]->str_resin_wr;
					$ArrBqDefault[$val]['str_resin_wr_add_a']		= $getDefVal[0]->str_resin_wr_add_a;
					$ArrBqDefault[$val]['str_resin_wr_add_b']		= $getDefVal[0]->str_resin_wr_add_b;
					$ArrBqDefault[$val]['str_resin_wr_add']			= $getDefVal[0]->str_resin_wr_add;
					$ArrBqDefault[$val]['str_resin_rv_a']			= $getDefVal[0]->str_resin_rv_a;
					$ArrBqDefault[$val]['str_resin_rv_b']			= $getDefVal[0]->str_resin_rv_b;
					$ArrBqDefault[$val]['str_resin_rv']				= $getDefVal[0]->str_resin_rv;
					$ArrBqDefault[$val]['str_resin_rv_add_a']		= $getDefVal[0]->str_resin_rv_add_a;
					$ArrBqDefault[$val]['str_resin_rv_add_b']		= $getDefVal[0]->str_resin_rv_add_b;
					$ArrBqDefault[$val]['str_resin_rv_add']			= $getDefVal[0]->str_resin_rv_add;
					$ArrBqDefault[$val]['str_faktor_csm']			= $getDefVal[0]->str_faktor_csm;
					$ArrBqDefault[$val]['str_faktor_csm_add']		= $getDefVal[0]->str_faktor_csm_add;
					$ArrBqDefault[$val]['str_faktor_wr']			= $getDefVal[0]->str_faktor_wr;
					$ArrBqDefault[$val]['str_faktor_wr_add']		= $getDefVal[0]->str_faktor_wr_add;
					$ArrBqDefault[$val]['str_faktor_rv']			= $getDefVal[0]->str_faktor_rv;
					$ArrBqDefault[$val]['str_faktor_rv_bw']			= $getDefVal[0]->str_faktor_rv_bw;
					$ArrBqDefault[$val]['str_faktor_rv_jb']			= $getDefVal[0]->str_faktor_rv_jb;
					$ArrBqDefault[$val]['str_faktor_rv_add']		= $getDefVal[0]->str_faktor_rv_add;
					$ArrBqDefault[$val]['str_faktor_rv_add_bw']		= $getDefVal[0]->str_faktor_rv_add_bw;
					$ArrBqDefault[$val]['str_faktor_rv_add_jb']		= $getDefVal[0]->str_faktor_rv_add_jb;
					$ArrBqDefault[$val]['str_resin']				= $getDefVal[0]->str_resin;
					$ArrBqDefault[$val]['str_resin_thickness']		= $getDefVal[0]->str_resin_thickness;
					$ArrBqDefault[$val]['eks_resin_veil_a']			= $getDefVal[0]->eks_resin_veil_a;
					$ArrBqDefault[$val]['eks_resin_veil_b']			= $getDefVal[0]->eks_resin_veil_b;
					$ArrBqDefault[$val]['eks_resin_veil']			= $getDefVal[0]->eks_resin_veil;
					$ArrBqDefault[$val]['eks_resin_veil_add_a']		= $getDefVal[0]->eks_resin_veil_add_a;
					$ArrBqDefault[$val]['eks_resin_veil_add_b']		= $getDefVal[0]->eks_resin_veil_add_b;
					$ArrBqDefault[$val]['eks_resin_veil_add']		= $getDefVal[0]->eks_resin_veil_add;
					$ArrBqDefault[$val]['eks_resin_csm_a']			= $getDefVal[0]->eks_resin_csm_a;
					$ArrBqDefault[$val]['eks_resin_csm_b']			= $getDefVal[0]->eks_resin_csm_b;
					$ArrBqDefault[$val]['eks_resin_csm']			= $getDefVal[0]->eks_resin_csm;
					$ArrBqDefault[$val]['eks_resin_csm_add_a']		= $getDefVal[0]->eks_resin_csm_add_a;
					$ArrBqDefault[$val]['eks_resin_csm_add_b']		= $getDefVal[0]->eks_resin_csm_add_b;
					$ArrBqDefault[$val]['eks_resin_csm_add']		= $getDefVal[0]->eks_resin_csm_add;
					$ArrBqDefault[$val]['eks_faktor_veil']			= $getDefVal[0]->eks_faktor_veil;
					$ArrBqDefault[$val]['eks_faktor_veil_add']		= $getDefVal[0]->eks_faktor_veil_add;
					$ArrBqDefault[$val]['eks_faktor_csm']			= $getDefVal[0]->eks_faktor_csm;
					$ArrBqDefault[$val]['eks_faktor_csm_add']		= $getDefVal[0]->eks_faktor_csm_add;
					$ArrBqDefault[$val]['eks_resin']				= $getDefVal[0]->eks_resin;
					$ArrBqDefault[$val]['eks_resin_thickness']		= $getDefVal[0]->eks_resin_thickness;
					$ArrBqDefault[$val]['topcoat_resin']			= $getDefVal[0]->topcoat_resin;
					$ArrBqDefault[$val]['str_n1_resin_csm_a']		= $getDefVal[0]->str_n1_resin_csm_a;
					$ArrBqDefault[$val]['str_n1_resin_csm_b']		= $getDefVal[0]->str_n1_resin_csm_b;
					$ArrBqDefault[$val]['str_n1_resin_csm']			= $getDefVal[0]->str_n1_resin_csm;
					$ArrBqDefault[$val]['str_n1_resin_csm_add_a']	= $getDefVal[0]->str_n1_resin_csm_add_a;
					$ArrBqDefault[$val]['str_n1_resin_csm_add_b']	= $getDefVal[0]->str_n1_resin_csm_add_b;
					$ArrBqDefault[$val]['str_n1_resin_csm_add']		= $getDefVal[0]->str_n1_resin_csm_add;
					$ArrBqDefault[$val]['str_n1_resin_wr_a']		= $getDefVal[0]->str_n1_resin_wr_a;
					$ArrBqDefault[$val]['str_n1_resin_wr_b']		= $getDefVal[0]->str_n1_resin_wr_b;
					$ArrBqDefault[$val]['str_n1_resin_wr']			= $getDefVal[0]->str_n1_resin_wr;
					$ArrBqDefault[$val]['str_n1_resin_wr_add_a']	= $getDefVal[0]->str_n1_resin_wr_add_a;
					$ArrBqDefault[$val]['str_n1_resin_wr_add_b']	= $getDefVal[0]->str_n1_resin_wr_add_b;
					$ArrBqDefault[$val]['str_n1_resin_wr_add']		= $getDefVal[0]->str_n1_resin_wr_add;
					$ArrBqDefault[$val]['str_n1_resin_rv_a']		= $getDefVal[0]->str_n1_resin_rv_a;
					$ArrBqDefault[$val]['str_n1_resin_rv_b']		= $getDefVal[0]->str_n1_resin_rv_b;
					$ArrBqDefault[$val]['str_n1_resin_rv']			= $getDefVal[0]->str_n1_resin_rv;
					$ArrBqDefault[$val]['str_n1_resin_rv_add_a']	= $getDefVal[0]->str_n1_resin_rv_add_a;
					$ArrBqDefault[$val]['str_n1_resin_rv_add_b']	= $getDefVal[0]->str_n1_resin_rv_add_b;
					$ArrBqDefault[$val]['str_n1_resin_rv_add']		= $getDefVal[0]->str_n1_resin_rv_add;
					$ArrBqDefault[$val]['str_n1_faktor_csm']		= $getDefVal[0]->str_n1_faktor_csm;
					$ArrBqDefault[$val]['str_n1_faktor_csm_add']	= $getDefVal[0]->str_n1_faktor_csm_add;
					$ArrBqDefault[$val]['str_n1_faktor_wr']			= $getDefVal[0]->str_n1_faktor_wr;
					$ArrBqDefault[$val]['str_n1_faktor_wr_add']		= $getDefVal[0]->str_n1_faktor_wr_add;
					$ArrBqDefault[$val]['str_n1_faktor_rv']			= $getDefVal[0]->str_n1_faktor_rv;
					$ArrBqDefault[$val]['str_n1_faktor_rv_bw']		= $getDefVal[0]->str_n1_faktor_rv_bw;
					$ArrBqDefault[$val]['str_n1_faktor_rv_jb']		= $getDefVal[0]->str_n1_faktor_rv_jb;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add']		= $getDefVal[0]->str_n1_faktor_rv_add;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add_bw']	= $getDefVal[0]->str_n1_faktor_rv_add_bw;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add_jb']	= $getDefVal[0]->str_n1_faktor_rv_add_jb;
					$ArrBqDefault[$val]['str_n1_resin']				= $getDefVal[0]->str_n1_resin;
					$ArrBqDefault[$val]['str_n1_resin_thickness']	= $getDefVal[0]->str_n1_resin_thickness;
					$ArrBqDefault[$val]['str_n2_resin_csm_a']		= $getDefVal[0]->str_n2_resin_csm_a;
					$ArrBqDefault[$val]['str_n2_resin_csm_b']		= $getDefVal[0]->str_n2_resin_csm_b;
					$ArrBqDefault[$val]['str_n2_resin_csm']			= $getDefVal[0]->str_n2_resin_csm;
					$ArrBqDefault[$val]['str_n2_resin_csm_add_a']	= $getDefVal[0]->str_n2_resin_csm_add_a;
					$ArrBqDefault[$val]['str_n2_resin_csm_add_b']	= $getDefVal[0]->str_n2_resin_csm_add_b;
					$ArrBqDefault[$val]['str_n2_resin_csm_add']		= $getDefVal[0]->str_n2_resin_csm_add;
					$ArrBqDefault[$val]['str_n2_resin_wr_a']		= $getDefVal[0]->str_n2_resin_wr_a;
					$ArrBqDefault[$val]['str_n2_resin_wr_b']		= $getDefVal[0]->str_n2_resin_wr_b;
					$ArrBqDefault[$val]['str_n2_resin_wr']			= $getDefVal[0]->str_n2_resin_wr;
					$ArrBqDefault[$val]['str_n2_resin_wr_add_a']	= $getDefVal[0]->str_n2_resin_wr_add_a;
					$ArrBqDefault[$val]['str_n2_resin_wr_add_b']	= $getDefVal[0]->str_n2_resin_wr_add_b;
					$ArrBqDefault[$val]['str_n2_resin_wr_add']		= $getDefVal[0]->str_n2_resin_wr_add;
					$ArrBqDefault[$val]['str_n2_faktor_csm']		= $getDefVal[0]->str_n2_faktor_csm;
					$ArrBqDefault[$val]['str_n2_faktor_csm_add']	= $getDefVal[0]->str_n2_faktor_csm_add;
					$ArrBqDefault[$val]['str_n2_faktor_wr']			= $getDefVal[0]->str_n2_faktor_wr;
					$ArrBqDefault[$val]['str_n2_faktor_wr_add']		= $getDefVal[0]->str_n2_faktor_wr_add;
					$ArrBqDefault[$val]['str_n2_resin']				= $getDefVal[0]->str_n2_resin;
					$ArrBqDefault[$val]['str_n2_resin_thickness']	= $getDefVal[0]->str_n2_resin_thickness;
					$ArrBqDefault[$val]['created_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefault[$val]['created_date']				= date('Y-m-d H:i:s');
				}
			}
			
			//Insert Component Header To Hist
			$qHeaderHistDef		= $this->db->query("SELECT * FROM so_component_default WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qHeaderHistDef)){
				foreach($qHeaderHistDef AS $val2HistADef => $valx2HistADef){
					$ArrBqDefaultHist[$val2HistADef]['id_product']				= $valx2HistADef['id_product'];
					$ArrBqDefaultHist[$val2HistADef]['id_milik']				= $valx2HistADef['id_milik'];
					$ArrBqDefaultHist[$val2HistADef]['id_bq']					= $valx2HistADef['id_bq'];
					$ArrBqDefaultHist[$val2HistADef]['product_parent']			= $valx2HistADef['product_parent'];
					$ArrBqDefaultHist[$val2HistADef]['kd_cust']					= $valx2HistADef['kd_cust'];
					$ArrBqDefaultHist[$val2HistADef]['customer']				= $valx2HistADef['customer'];
					$ArrBqDefaultHist[$val2HistADef]['standart_code']			= $valx2HistADef['standart_code'];
					$ArrBqDefaultHist[$val2HistADef]['diameter']				= $valx2HistADef['diameter'];
					$ArrBqDefaultHist[$val2HistADef]['diameter2']				= $valx2HistADef['diameter2'];
					$ArrBqDefaultHist[$val2HistADef]['liner']					= $valx2HistADef['liner'];
					$ArrBqDefaultHist[$val2HistADef]['pn']						= $valx2HistADef['pn'];
					$ArrBqDefaultHist[$val2HistADef]['overlap']					= $valx2HistADef['overlap'];
					$ArrBqDefaultHist[$val2HistADef]['waste']					= $valx2HistADef['waste'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n1']				= $valx2HistADef['waste_n1'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n2']				= $valx2HistADef['waste_n2'];
					$ArrBqDefaultHist[$val2HistADef]['max']						= $valx2HistADef['max'];
					$ArrBqDefaultHist[$val2HistADef]['min']						= $valx2HistADef['min'];
					$ArrBqDefaultHist[$val2HistADef]['plastic_film']			= $valx2HistADef['plastic_film'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_a']		= $valx2HistADef['lin_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_b']		= $valx2HistADef['lin_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil']			= $valx2HistADef['lin_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_a']	= $valx2HistADef['lin_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_b']	= $valx2HistADef['lin_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add']		= $valx2HistADef['lin_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_a']			= $valx2HistADef['lin_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_b']			= $valx2HistADef['lin_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm']			= $valx2HistADef['lin_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_a']		= $valx2HistADef['lin_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_b']		= $valx2HistADef['lin_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add']		= $valx2HistADef['lin_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil']			= $valx2HistADef['lin_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil_add']		= $valx2HistADef['lin_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm']			= $valx2HistADef['lin_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm_add']		= $valx2HistADef['lin_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin']				= $valx2HistADef['lin_resin'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_thickness']		= $valx2HistADef['lin_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_a']			= $valx2HistADef['str_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_b']			= $valx2HistADef['str_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm']			= $valx2HistADef['str_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_a']		= $valx2HistADef['str_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_b']		= $valx2HistADef['str_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add']		= $valx2HistADef['str_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_a']			= $valx2HistADef['str_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_b']			= $valx2HistADef['str_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr']			= $valx2HistADef['str_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_a']		= $valx2HistADef['str_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_b']		= $valx2HistADef['str_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add']		= $valx2HistADef['str_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_a']			= $valx2HistADef['str_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_b']			= $valx2HistADef['str_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv']			= $valx2HistADef['str_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_a']		= $valx2HistADef['str_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_b']		= $valx2HistADef['str_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add']		= $valx2HistADef['str_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm']			= $valx2HistADef['str_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm_add']		= $valx2HistADef['str_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr']			= $valx2HistADef['str_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr_add']		= $valx2HistADef['str_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv']			= $valx2HistADef['str_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_bw']		= $valx2HistADef['str_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_jb']		= $valx2HistADef['str_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add']		= $valx2HistADef['str_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_bw']	= $valx2HistADef['str_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_jb']	= $valx2HistADef['str_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin']				= $valx2HistADef['str_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_thickness']		= $valx2HistADef['str_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_a']		= $valx2HistADef['eks_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_b']		= $valx2HistADef['eks_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil']			= $valx2HistADef['eks_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_a']	= $valx2HistADef['eks_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_b']	= $valx2HistADef['eks_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add']		= $valx2HistADef['eks_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_a']			= $valx2HistADef['eks_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_b']			= $valx2HistADef['eks_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm']			= $valx2HistADef['eks_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_a']		= $valx2HistADef['eks_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_b']		= $valx2HistADef['eks_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add']		= $valx2HistADef['eks_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil']			= $valx2HistADef['eks_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil_add']		= $valx2HistADef['eks_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm']			= $valx2HistADef['eks_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm_add']		= $valx2HistADef['eks_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin']				= $valx2HistADef['eks_resin'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_thickness']		= $valx2HistADef['eks_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['topcoat_resin']			= $valx2HistADef['topcoat_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_a']		= $valx2HistADef['str_n1_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_b']		= $valx2HistADef['str_n1_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm']		= $valx2HistADef['str_n1_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_a']	= $valx2HistADef['str_n1_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_b']	= $valx2HistADef['str_n1_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add']	= $valx2HistADef['str_n1_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_a']		= $valx2HistADef['str_n1_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_b']		= $valx2HistADef['str_n1_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr']			= $valx2HistADef['str_n1_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_a']	= $valx2HistADef['str_n1_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_b']	= $valx2HistADef['str_n1_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add']		= $valx2HistADef['str_n1_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_a']		= $valx2HistADef['str_n1_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_b']		= $valx2HistADef['str_n1_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv']			= $valx2HistADef['str_n1_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_a']	= $valx2HistADef['str_n1_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_b']	= $valx2HistADef['str_n1_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add']		= $valx2HistADef['str_n1_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm']		= $valx2HistADef['str_n1_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm_add']	= $valx2HistADef['str_n1_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr']		= $valx2HistADef['str_n1_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr_add']	= $valx2HistADef['str_n1_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv']		= $valx2HistADef['str_n1_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_bw']		= $valx2HistADef['str_n1_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_jb']		= $valx2HistADef['str_n1_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add']	= $valx2HistADef['str_n1_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_bw']	= $valx2HistADef['str_n1_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_jb']	= $valx2HistADef['str_n1_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin']			= $valx2HistADef['str_n1_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_thickness']	= $valx2HistADef['str_n1_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_a']		= $valx2HistADef['str_n2_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_b']		= $valx2HistADef['str_n2_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm']		= $valx2HistADef['str_n2_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_a']	= $valx2HistADef['str_n2_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_b']	= $valx2HistADef['str_n2_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add']	= $valx2HistADef['str_n2_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_a']		= $valx2HistADef['str_n2_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_b']		= $valx2HistADef['str_n2_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr']			= $valx2HistADef['str_n2_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_a']	= $valx2HistADef['str_n2_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_b']	= $valx2HistADef['str_n2_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add']		= $valx2HistADef['str_n2_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm']		= $valx2HistADef['str_n2_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm_add']	= $valx2HistADef['str_n2_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr']		= $valx2HistADef['str_n2_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr_add']	= $valx2HistADef['str_n2_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin']			= $valx2HistADef['str_n2_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_thickness']	= $valx2HistADef['str_n2_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['created_by']				= $valx2HistADef['created_by'];
					$ArrBqDefaultHist[$val2HistADef]['created_date']			= $valx2HistADef['created_date'];
					$ArrBqDefaultHist[$val2HistADef]['modified_by']				= $valx2HistADef['modified_by'];
					$ArrBqDefaultHist[$val2HistADef]['modified_date']			= $valx2HistADef['modified_date'];
					$ArrBqDefaultHist[$val2HistADef]['hist_by']					= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefaultHist[$val2HistADef]['hist_date']				= date('Y-m-d H:i:s');
					
					
				}
			}
			//================================================================================================================
			//================================================================================================================
			//================================================================================================================
			
			//Insert Component Header To Hist
			$qHeaderHist	= $this->db->query("SELECT * FROM so_component_header WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qHeaderHist)){
				foreach($qHeaderHist AS $val2HistA => $valx2HistA){
					$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
					$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
					$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
					$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
					$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
					$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
					$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
					$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
					$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
					$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
					$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
					$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
					$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
					$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
					$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
					$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
					$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
					$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
					$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
					$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
					$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
					$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
					$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
					$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
					$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length']			= $valx2HistA['wrap_length'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length2']		= $valx2HistA['wrap_length2'];
					$ArrBqHeaderHist[$val2HistA]['high']				= $valx2HistA['high'];
					$ArrBqHeaderHist[$val2HistA]['area2']				= $valx2HistA['area2'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
					$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
					$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
					$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
					$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
					$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
					$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
					$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
					$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
					$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
					$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
					$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
					$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
					$ArrBqHeaderHist[$val2HistA]['deleted_by']			= $valx2HistA['deleted_by'];
					$ArrBqHeaderHist[$val2HistA]['deleted_date']		= $valx2HistA['deleted_date'];
					$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
					
				}
			}

			//Component Detail
			$qDetail	= $this->db->query("SELECT a.*, b.panjang FROM component_detail a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$valx['id_product']."' ")->result_array();
			foreach($qDetail AS $val2 => $valx2){
				$LoopDetail++;

				$ArrBqDetail[$LoopDetail]['id_product']		= $valx['id_product'];
				$ArrBqDetail[$LoopDetail]['id_bq']				= $id_bq;
				$ArrBqDetail[$LoopDetail]['id_milik']			= $valx['id'];
				$ArrBqDetail[$LoopDetail]['detail_name']		= $valx2['detail_name'];
				$ArrBqDetail[$LoopDetail]['acuhan']			= $valx2['acuhan'];
				$ArrBqDetail[$LoopDetail]['id_ori']			= $valx2['id_ori'];
				$ArrBqDetail[$LoopDetail]['id_ori2']			= $valx2['id_ori2'];
				$ArrBqDetail[$LoopDetail]['id_category']		= $valx2['id_category'];
				$ArrBqDetail[$LoopDetail]['nm_category']		= $valx2['nm_category'];
				$ArrBqDetail[$LoopDetail]['id_material']		= $valx2['id_material'];
				$ArrBqDetail[$LoopDetail]['nm_material']		= $valx2['nm_material'];
				$ArrBqDetail[$LoopDetail]['value']			= $valx2['value'];
				$ArrBqDetail[$LoopDetail]['thickness']		= $valx2['thickness'];
				$ArrBqDetail[$LoopDetail]['fak_pengali']		= $valx2['fak_pengali'];
				$ArrBqDetail[$LoopDetail]['bw']				= $valx2['bw'];
				$ArrBqDetail[$LoopDetail]['jumlah']			= $valx2['jumlah'];
				$ArrBqDetail[$LoopDetail]['layer']			= $valx2['layer'];
				$ArrBqDetail[$LoopDetail]['containing']		= $valx2['containing'];
				$ArrBqDetail[$LoopDetail]['total_thickness']	= $valx2['total_thickness'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / floatval($valx2['panjang']))* (floatval($valx['panjang']) + 400);
				}
				elseif ($qHeader[0]->parent_product == 'branch joint' OR $qHeader[0]->parent_product == 'field joint' OR $qHeader[0]->parent_product == 'shop joint') {
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['material_weight'];
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / 1000) * floatval($valx['panjang']);
				}
				else{
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['last_cost'];
				}
				$ArrBqDetail[$LoopDetail]['rev']				= $qHeader[0]->rev;
				//
				$ArrBqDetail[$LoopDetail]['area_weight']				= $valx2['area_weight'];
				$ArrBqDetail[$LoopDetail]['material_weight']				= $valx2['material_weight'];
				$ArrBqDetail[$LoopDetail]['percentage']				= $valx2['percentage'];
				$ArrBqDetail[$LoopDetail]['resin_content']				= $valx2['resin_content'];

				$ArrBqDetail[$LoopDetail]['price_mat']				= get_price_ref($valx2['id_material']);
			}

			//Component Lamination
			$qDetailLam	= $this->db->query("SELECT * FROM component_lamination WHERE id_product='".$valx['id_product']."' ")->result_array();
			foreach($qDetailLam AS $val2 => $valx2){
				$LoopDetailLam++;
				$ArrBqDetailLam[$LoopDetailLam]['id_product']		= $valx['id_product'];
				$ArrBqDetailLam[$LoopDetailLam]['id_bq']				= $id_bq;
				$ArrBqDetailLam[$LoopDetailLam]['id_milik']			= $valx['id'];
				$ArrBqDetailLam[$LoopDetailLam]['detail_name']		= $valx2['detail_name'];
				$ArrBqDetailLam[$LoopDetailLam]['lapisan']			= $valx2['lapisan'];
				$ArrBqDetailLam[$LoopDetailLam]['std_glass']			= $valx2['std_glass'];
				$ArrBqDetailLam[$LoopDetailLam]['width']			= $valx2['width'];
				$ArrBqDetailLam[$LoopDetailLam]['stage']		= $valx2['stage'];
				$ArrBqDetailLam[$LoopDetailLam]['glass']		= $valx2['glass'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_1']		= $valx2['thickness_1'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_2']		= $valx2['thickness_2'];
				$ArrBqDetailLam[$LoopDetailLam]['glass_length']			= $valx2['glass_length'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_veil']		= $valx2['weight_veil'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_csm']		= $valx2['weight_csm'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_wr']				= $valx2['weight_wr'];
			}

			//Insert Component Detail To Hist
			$qDetailHist	= $this->db->query("SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailHist)){
				foreach($qDetailHist AS $val2Hist => $valx2Hist){
					$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
					$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
					$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
					$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
					$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
					$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
					$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
					$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
					$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
					$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
					$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
					$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
					$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
					$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
					$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
					$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
					$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
					$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
					$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
					$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
					$ArrBqDetailHist[$val2Hist]['rev']				= $valx2Hist['rev'];
					$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
					$ArrBqDetailHist[$val2Hist]['hist_by']			= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Plus
			$qDetailPlus	= $this->db->query("SELECT a.*, b.panjang FROM component_detail_plus a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$valx['id_product']."' ")->result_array();
			foreach($qDetailPlus AS $val3 => $valx3){
				$LoopDetailPlus++;

				$ArrBqDetailPlus[$LoopDetailPlus]['id_product']		= $valx['id_product'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_bq']			= $id_bq;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_milik']			= $valx['id'];
				$ArrBqDetailPlus[$LoopDetailPlus]['detail_name']		= $valx3['detail_name'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori']			= $valx3['id_ori'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori2']			= $valx3['id_ori2'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_category']		= $valx3['id_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_category']		= $valx3['nm_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_material']		= $valx3['id_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_material']		= $valx3['nm_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['containing']		= $valx3['containing'];
				$ArrBqDetailPlus[$LoopDetailPlus]['perse']			= $valx3['perse'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($valx3['panjang'])) * (floatval($valx['panjang']) + 400);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($valx3['panjang'])) * (floatval($valx['panjang']) + 400);
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / 1000) * floatval($valx['panjang']);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / 1000) * floatval($valx['panjang']);
				}
				else{
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= $valx3['last_full'];
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= $valx3['last_cost'];
				}
				$ArrBqDetailPlus[$LoopDetailPlus]['rev']				= $qHeader[0]->rev;
				$ArrBqDetailPlus[$LoopDetailPlus]['price_mat']				= get_price_ref($valx3['id_material']);
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailPlusHist)){
				foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
					$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
					$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
					$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
					$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
					$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
					$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
					$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
					$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
					$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
					$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
					$ArrBqDetailPlusHist[$val3Hist]['rev']			= $valx3Hist['rev'];
					$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Add
			$qDetailAdd		= $this->db->query("SELECT a.*, b.panjang FROM component_detail_add a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$valx['id_product']."' ")->result_array();
			if(!empty($qDetailAdd)){
				foreach($qDetailAdd AS $val4 => $valx4){
					$LoopDetailAdd++;
					// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx2['id_material']."' LIMIT 1";
					// $restPrice = $this->db->query($sqlPrice)->result();
					$ArrBqDetailAdd[$LoopDetailAdd]['id_product']		= $valx['id_product'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_bq']				= $id_bq;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_milik']			= $valx['id'];
					$ArrBqDetailAdd[$LoopDetailAdd]['detail_name']		= $valx4['detail_name'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_category']		= $valx4['id_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_category']		= $valx4['nm_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_material']		= $valx4['id_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_material']		= $valx4['nm_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['containing']		= $valx4['containing'];
					$ArrBqDetailAdd[$LoopDetailAdd]['perse']				= $valx4['perse'];
					if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= (floatval($valx4['last_full']) / floatval($valx4['panjang'])) * (floatval($valx['panjang']) + 400);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= (floatval($valx4['last_cost']) / floatval($valx4['panjang'])) * (floatval($valx['panjang']) + 400);
					}
					elseif($qHeader[0]->parent_product == 'frp pipe'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= (floatval($valx4['last_full']) / 1000) * floatval($valx['panjang']);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= (floatval($valx4['last_cost']) / 1000) * floatval($valx['panjang']);
					}
					else{
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= $valx4['last_full'];
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= $valx4['last_cost'];
					}
					$ArrBqDetailAdd[$LoopDetailAdd]['rev']				= $qHeader[0]->rev;
					$ArrBqDetailAdd[$LoopDetailAdd]['price_mat']		= get_price_ref($valx4['id_material']);
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailAddHist)){
				foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
					
					$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
					$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
					$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
					$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
					$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
					$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
					$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
					$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
					$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
					$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
					$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
					$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
					$ArrBqDetailAddHist[$val4Hist]['rev']			= $valx4Hist['rev'];
					$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
					$ArrBqDetailAddHist[$val4Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailAddHist[$val4Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Footer
			$qDetailFooter	= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$valx['id_product']."' ")->result_array();
			if(!empty($qDetailFooter)){
				foreach($qDetailFooter AS $val5 => $valx5){
					$LoopFooter++;
					$ArrBqFooter[$LoopFooter]['id_product']	= $valx['id_product'];
					$ArrBqFooter[$LoopFooter]['id_bq']		= $id_bq;
					$ArrBqFooter[$LoopFooter]['id_milik']		= $valx['id'];
					$ArrBqFooter[$LoopFooter]['detail_name']	= $valx5['detail_name'];
					$ArrBqFooter[$LoopFooter]['total']		= $valx5['total'];
					$ArrBqFooter[$LoopFooter]['min']			= $valx5['min'];
					$ArrBqFooter[$LoopFooter]['max']			= $valx5['max'];
					$ArrBqFooter[$LoopFooter]['hasil']		= $valx5['hasil'];
					$ArrBqFooter[$LoopFooter]['rev']			= $qHeader[0]->rev;
				}
			}
			//Insert Component Footer To Hist
			$qDetailFooterHist		= $this->db->query("SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' ")->result_array();
			if(!empty($qDetailFooterHist)){
				foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
					$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
					$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
					$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
					$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
					$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
					$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
					$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
					$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
					$ArrBqFooterHist[$val5Hist]['rev']			= $valx5Hist['rev'];
					$ArrBqFooterHist[$val5Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}
		}

		// print_r($ArrBqDefault);
		// print_r($ArrBqDetail);
		// print_r($ArrBqDetailPlus);
		// print_r($ArrBqDetailAdd);
		// print_r($ArrBqFooter); ArrBqDefaultHist
		// echo $qDetailAddNum2;
		// echo $qDetailHeaderNum2;
		// echo $qDetailDetailNum2;
		// echo $qDetailDetailPlusNum2;
		// echo $qDetailDetailAddNum2;
		// echo $qDetailDetailFooterNum2;
		// echo "</pre>";
		// exit;

		$UpdateBQ	= array(
			'estimasi'	=> 'Y',
			'est_by'	=> $this->session->userdata['ORI_User']['username'],
			'est_date'	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->update_batch('so_detail_header', $ArrDetBq2, 'id');

			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_so_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_so_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(count($ArrBqFooterHist)>0){
			// 	$this->db->insert_batch('hist_so_component_footer', $ArrBqFooterHist);
			// }
			// if(!empty($ArrBqDefaultHist)){
			// 	$this->db->insert_batch('hist_so_component_default', $ArrBqDefaultHist);
			// }

			//Delete BQ Component
			foreach($dtListArray AS $val => $valx){ 
				$this->db->delete('so_component_header', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_detail', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_lamination', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_detail_plus', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_detail_add', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_footer', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
				$this->db->delete('so_component_default', array('id_bq' => $id_bq, 'id_milik' => $valx['id']));
			}
			
			// $this->db->delete('so_component_header', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_detail', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_lamination', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_detail_plus', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_detail_add', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_footer', array('id_bq' => $id_bq));
			// $this->db->delete('so_component_default', array('id_bq' => $id_bq));

			//Insert BQ Component
			if(!empty($ArrBqHeader)){
				$this->db->insert_batch('so_component_header', $ArrBqHeader);
			}
			if(!empty($ArrBqDetail)){
				$this->db->insert_batch('so_component_detail', $ArrBqDetail);
			}
			if(!empty($ArrBqDetailLam)){
				$this->db->insert_batch('so_component_lamination', $ArrBqDetailLam);
			}
			if(!empty($ArrBqDetailPlus)){
				$this->db->insert_batch('so_component_detail_plus', $ArrBqDetailPlus);
			}
			if(!empty($ArrBqDetailAdd)){
				$this->db->insert_batch('so_component_detail_add', $ArrBqDetailAdd);
			}
			if(!empty($ArrBqFooter)){
				$this->db->insert_batch('so_component_footer', $ArrBqFooter);
			}
			if(!empty($ArrBqDefault)){
				$this->db->insert_batch('so_component_default', $ArrBqDefault);
			}

			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $UpdateBQ);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $pembeda,
				'pesan'		=>'Estimation structure bq data success. Thanks ...',
				'status'	=> 1
			);
			history('Estimation Structure BQ (tarik baru) in Final Drawing with code : '.$id_bq.'/'.$no_ipp);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function update_mat_acc(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$id_bq			= "BQ-".$data['no_ipp'];
		$pembeda		= $data['pembeda'];
		
		$detail_acc		= array();
		if(!empty($data['detail'])){
			$detailAcc		= $data['detail'];
			//DETAIL
			foreach($detailAcc AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$qty_m = str_replace(',','',$valx['qty']);
					$price = get_price_rutin($valx['id_material'], $valx['satuan']);
					$detail_acc[$val]['id_bq']			= $id_bq;
					$detail_acc[$val]['category']		= 'acc';
					$detail_acc[$val]['id_material']	= $valx['id_material'];
					$detail_acc[$val]['qty']			= $qty_m;
					$detail_acc[$val]['satuan']			= $valx['satuan'];
					$detail_acc[$val]['unit_price']		= $price;
					$detail_acc[$val]['total_price']	= $qty_m * $price;
					$detail_acc[$val]['updated_by']		= $this->session->userdata['ORI_User']['username'];
					$detail_acc[$val]['updated_date']	= date('Y-m-d H:i:s');
				}
			}
		}
		$detail_mat		= array();
		if(!empty($data['detail_material'])){
			$detailMat		= $data['detail_material'];
			//DETAIL MATERIAL 
			foreach($detailMat AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$qty_m = str_replace(',','',$valx['qty']);
					$price = get_price_ref($valx['id_material']);
					$detail_mat[$val]['id_bq']			= $id_bq;
					$detail_mat[$val]['category']		= 'mat';
					$detail_mat[$val]['id_material']	= $valx['id_material'];
					$detail_mat[$val]['qty']			= $qty_m;
					$detail_mat[$val]['satuan']			= '1';
					$detail_mat[$val]['unit_price']		= $price;
					$detail_mat[$val]['total_price']	= $qty_m * $price;
					$detail_mat[$val]['updated_by']		= $this->session->userdata['ORI_User']['username'];
					$detail_mat[$val]['updated_date']	= date('Y-m-d H:i:s');
					
				}
			}
		}
		
		$detail_baut		= array();
		if(!empty($data['detail_baut'])){
			$detailBaut		= $data['detail_baut'];
			foreach($detailBaut AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$get_detail = $this->db->select('harga')->get_where('accessories', array('id'=>$valx['id_material']))->result();
					$qty_m 		= str_replace(',','',$valx['qty']);
					$price 		= $get_detail[0]->harga;
					$detail_baut[$val]['id_bq']			= $id_bq;
					$detail_baut[$val]['category']		= 'baut';
					$detail_baut[$val]['id_material']	= $valx['id_material'];
					// $detail_baut[$val]['id_material2']	= $valx['id_material2'];
					$detail_baut[$val]['qty']			= $qty_m;
					$detail_baut[$val]['satuan']		= $valx['satuan'];
					$detail_baut[$val]['unit_price']	= $price;
					$detail_baut[$val]['total_price']	= $qty_m * $price;
					$detail_baut[$val]['note']			= strtolower($valx['note']);
					$detail_baut[$val]['updated_by']	= $this->session->userdata['ORI_User']['username'];
					$detail_baut[$val]['updated_date']	= date('Y-m-d H:i:s');
					
				}
			}
		}

		$detail_plate		= array();
		if(!empty($data['detail_plate'])){
			$detailPlate		= $data['detail_plate'];
			foreach($detailPlate AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$get_detail = $this->db->select('harga, satuan')->get_where('accessories', array('id'=>$valx['id_material']))->result();
					$price 		= $get_detail[0]->harga;
					$qty_m 		= str_replace(',','',$valx['qty']);
					$detail_plate[$val]['id_bq']		= $id_bq;
					$detail_plate[$val]['category']		= 'plate';
					$detail_plate[$val]['id_material']	= $valx['id_material'];
					$detail_plate[$val]['qty']			= $qty_m;
					$detail_plate[$val]['satuan']		= $get_detail[0]->satuan;
					$detail_plate[$val]['lebar']		= str_replace(',','',$valx['lebar']);
					$detail_plate[$val]['panjang']		= str_replace(',','',$valx['panjang']);
					$detail_plate[$val]['berat']		= str_replace(',','',$valx['berat']);
					$detail_plate[$val]['unit_price']	= $price;
					$detail_plate[$val]['total_price']	= str_replace(',','',$valx['berat']) * $price;
					$detail_plate[$val]['note']			= strtolower($valx['note']);
					$detail_plate[$val]['updated_by']	= $this->session->userdata['ORI_User']['username'];
					$detail_plate[$val]['updated_date']	= date('Y-m-d H:i:s');
					
				}
			}
		}
		
		$detail_gasket		= array();
		if(!empty($data['detail_gasket'])){
			$detailGasket		= $data['detail_gasket'];
			foreach($detailGasket AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$get_detail = $this->db->select('harga, satuan')->get_where('accessories', array('id'=>$valx['id_material']))->result();
					$price 		= $get_detail[0]->harga;
					$qty_m 		= str_replace(',','',$valx['qty']);
					$detail_gasket[$val]['id_bq']		= $id_bq;
					$detail_gasket[$val]['category']		= 'gasket';
					$detail_gasket[$val]['id_material']	= $valx['id_material'];
					$detail_gasket[$val]['qty']			= $qty_m;
					$detail_gasket[$val]['satuan']		= $valx['satuan'];
					$detail_gasket[$val]['lebar']		= str_replace(',','',$valx['lebar']);
					$detail_gasket[$val]['panjang']		= str_replace(',','',$valx['panjang']);
					$detail_gasket[$val]['unit_price']	= $price;
					$detail_gasket[$val]['total_price']	= $qty_m * $price;
					$detail_gasket[$val]['note']			= strtolower($valx['note']);
					$detail_gasket[$val]['updated_by']	= $this->session->userdata['ORI_User']['username'];
					$detail_gasket[$val]['updated_date']	= date('Y-m-d H:i:s');
					
				}
			}
		}

		$detail_lainnya		= array();
		if(!empty($data['detail_lainnya'])){
			$detailLainnya		= $data['detail_lainnya'];
			foreach($detailLainnya AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$get_detail = $this->db->select('harga')->get_where('accessories', array('id'=>$valx['id_material']))->result();
					$qty_m 		= str_replace(',','',$valx['qty']);
					$price 		= $get_detail[0]->harga;
					$detail_lainnya[$val]['id_bq']			= $id_bq;
					$detail_lainnya[$val]['category']		= 'lainnya';
					$detail_lainnya[$val]['id_material']	= $valx['id_material'];
					$detail_lainnya[$val]['qty']			= $qty_m;
					$detail_lainnya[$val]['satuan']			= $valx['satuan'];
					$detail_lainnya[$val]['unit_price']		= $price;
					$detail_lainnya[$val]['total_price']	= $qty_m * $price;
					$detail_lainnya[$val]['note']			= strtolower($valx['note']);
					$detail_lainnya[$val]['updated_by']		= $this->session->userdata['ORI_User']['username'];
					$detail_lainnya[$val]['updated_date']	= date('Y-m-d H:i:s');
					
				}
			}
		}
		// print_r($detail_acc);
		// print_r($detail_mat);
		// exit;

		$this->db->trans_start();
			if(!empty($detail_acc)){
				$this->db->where('approve','N');
				$this->db->where('category','acc');
				$this->db->delete('so_acc_and_mat', array('id_bq' => $id_bq));
				
				$this->db->insert_batch('so_acc_and_mat', $detail_acc);
			}
			if(!empty($detail_mat)){
				$this->db->where('approve','N');
				$this->db->where('category','mat');
				$this->db->delete('so_acc_and_mat', array('id_bq' => $id_bq));
				
				$this->db->insert_batch('so_acc_and_mat', $detail_mat);
			}
			if(!empty($detail_baut)){
				$this->db->where('approve','N');
				$this->db->where('category','baut');
				$this->db->delete('so_acc_and_mat', array('id_bq' => $id_bq));
				
				$this->db->insert_batch('so_acc_and_mat', $detail_baut);
			}
			if(!empty($detail_plate)){
				$this->db->where('approve','N');
				$this->db->where('category','plate');
				$this->db->delete('so_acc_and_mat', array('id_bq' => $id_bq));
				
				$this->db->insert_batch('so_acc_and_mat', $detail_plate);
			}
			if(!empty($detail_gasket)){
				$this->db->where('approve','N');
				$this->db->where('category','gasket');
				$this->db->delete('so_acc_and_mat', array('id_bq' => $id_bq));
				
				$this->db->insert_batch('so_acc_and_mat', $detail_gasket);
			}
			if(!empty($detail_lainnya)){
				$this->db->where('approve','N');
				$this->db->where('category','lainnya');
				$this->db->delete('so_acc_and_mat', array('id_bq' => $id_bq));
				
				$this->db->insert_batch('so_acc_and_mat', $detail_lainnya);
			}

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Save data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $pembeda,
				'pesan'		=>'Savedata success. Thanks ...',
				'status'	=> 1
			);
			history('Save rutin & material final drawaing : '.$id_bq);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function modal_detail_product_est(){
		$this->load->view('FinalDrawing/modalDetail');
	}
	
	public function update_satuan_est_master(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
	
		$id_bq			= $this->uri->segment(3);
		$id_milik		= $this->uri->segment(4);
		$panjang		= $this->uri->segment(5);
		$product		= $this->uri->segment(6);

		$ArrBqHeader			= array();
		$ArrBqDetail			= array();
		$ArrBqDetailPlus		= array();
		$ArrBqDetailAdd			= array();
		$ArrBqFooter			= array();
		$ArrBqHeaderHist		= array();
		$ArrBqDetailHist		= array();
		$ArrBqDetailPlusHist	= array();
		$ArrBqDetailAddHist		= array();
		$ArrBqFooterHist		= array();
		$ArrBqDefault			= array();
		$ArrBqDefaultHist		= array();

		$LoopDetail = 0;
		$LoopDetailLam = 0;
		$LoopDetailPlus = 0;
		$LoopDetailAdd = 0;
		$LoopFooter = 0;
		
			//Component Header
			$qHeader	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$product."' LIMIT 1 ")->result();
			$ArrBqHeader['id_product']			= $product;
			$ArrBqHeader['id_bq']					= $id_bq;
			$ArrBqHeader['id_milik']				= $id_milik;
			$ArrBqHeader['parent_product']		= $qHeader[0]->parent_product;
			$ArrBqHeader['nm_product']			= $qHeader[0]->nm_product;
			$ArrBqHeader['standart_code']			= $qHeader[0]->standart_code;
			$ArrBqHeader['series']				= $qHeader[0]->series;
			$ArrBqHeader['resin_sistem']			= $qHeader[0]->resin_sistem;
			$ArrBqHeader['pressure']				= $qHeader[0]->pressure;
			$ArrBqHeader['diameter']				= $qHeader[0]->diameter;
			$ArrBqHeader['liner']					= $qHeader[0]->liner;
			$ArrBqHeader['aplikasi_product']		= $qHeader[0]->aplikasi_product;
			$ArrBqHeader['criminal_barier']		= $qHeader[0]->criminal_barier;
			$ArrBqHeader['vacum_rate']			= $qHeader[0]->vacum_rate;
			$ArrBqHeader['stiffness']				= $qHeader[0]->stiffness;
			$ArrBqHeader['design_life']			= $qHeader[0]->design_life; 
			$ArrBqHeader['standart_by']			= $qHeader[0]->standart_by;
			$ArrBqHeader['standart_toleransi']	= $qHeader[0]->standart_toleransi;
			$ArrBqHeader['diameter2']				= $qHeader[0]->diameter2;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader['panjang']			= floatval($panjang) + 400;
			}
			else{
				$ArrBqHeader['panjang']			= $qHeader[0]->panjang;
			}
			$ArrBqHeader['radius']				= $qHeader[0]->radius;
			$ArrBqHeader['type_elbow']			= $qHeader[0]->type_elbow;
			$ArrBqHeader['angle']					= $qHeader[0]->angle;
			$ArrBqHeader['design']				= $qHeader[0]->design;
			$ArrBqHeader['est']					= $qHeader[0]->est;
			$ArrBqHeader['min_toleransi']			= $qHeader[0]->min_toleransi;
			$ArrBqHeader['max_toleransi']			= $qHeader[0]->max_toleransi;
			$ArrBqHeader['waste']					= $qHeader[0]->waste;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader['area']				= (floatval($qHeader[0]->area) / floatval($qHeader[0]->panjang)) * (floatval($panjang) + 400);
			}
			else{
				$ArrBqHeader['area']				= $qHeader[0]->area;
			}
			$ArrBqHeader['wrap_length']		= $qHeader[0]->wrap_length;
			$ArrBqHeader['wrap_length2']		= $qHeader[0]->wrap_length2;
			$ArrBqHeader['high']				= $qHeader[0]->high;
			$ArrBqHeader['area2']				= $qHeader[0]->area2;
			$ArrBqHeader['panjang_neck_1']	= $qHeader[0]->panjang_neck_1;
			$ArrBqHeader['panjang_neck_2']	= $qHeader[0]->panjang_neck_2;
			$ArrBqHeader['design_neck_1']		= $qHeader[0]->design_neck_1;
			$ArrBqHeader['design_neck_2']		= $qHeader[0]->design_neck_2;
			$ArrBqHeader['est_neck_1']		= $qHeader[0]->est_neck_1;
			$ArrBqHeader['est_neck_2']		= $qHeader[0]->est_neck_2;
			$ArrBqHeader['area_neck_1']		= $qHeader[0]->area_neck_1;
			$ArrBqHeader['area_neck_2']		= $qHeader[0]->area_neck_2;
			$ArrBqHeader['flange_od']			= $qHeader[0]->flange_od;
			$ArrBqHeader['flange_bcd']		= $qHeader[0]->flange_bcd;
			$ArrBqHeader['flange_n']			= $qHeader[0]->flange_n;
			$ArrBqHeader['flange_oh']			= $qHeader[0]->flange_oh;
			$ArrBqHeader['rev']				= $qHeader[0]->rev;
			$ArrBqHeader['status']			= $qHeader[0]->status;
			$ArrBqHeader['approve_by']		= $qHeader[0]->approve_by;
			$ArrBqHeader['approve_date']		= $qHeader[0]->approve_date;
			$ArrBqHeader['approve_reason']	= $qHeader[0]->approve_reason;
			$ArrBqHeader['sts_price']			= $qHeader[0]->sts_price;
			$ArrBqHeader['sts_price_by']		= $qHeader[0]->sts_price_by;
			$ArrBqHeader['sts_price_date']	= $qHeader[0]->sts_price_date;
			$ArrBqHeader['sts_price_reason']	= $qHeader[0]->sts_price_reason;
			$ArrBqHeader['created_by']		= $qHeader[0]->created_by;
			$ArrBqHeader['created_date']		= $qHeader[0]->created_date;
			$ArrBqHeader['deleted']			= $qHeader[0]->deleted;
			$ArrBqHeader['deleted_by']		= $qHeader[0]->deleted_by;
			$ArrBqHeader['deleted_date']		= $qHeader[0]->deleted_date;
			//
			$ArrBqHeader['pipe_thickness']	= $qHeader[0]->pipe_thickness;
			$ArrBqHeader['joint_thickness']	= $qHeader[0]->joint_thickness;
			$ArrBqHeader['factor_thickness']	= $qHeader[0]->factor_thickness;
			$ArrBqHeader['factor']			= $qHeader[0]->factor;
			
			// print_r($ArrBqHeader);
			// exit;
			//================================================================================================================
			//============================================DEFAULT BY ARWANT===================================================
			//================================================================================================================
			if(!empty($qHeader[0]->standart_code)){
				$plusSQL = "";
				if($qHeader[0]->parent_product == 'concentric reducer' OR $qHeader[0]->parent_product == 'reducer tee mould' OR $qHeader[0]->parent_product == 'eccentric reducer' OR $qHeader[0]->parent_product == 'reducer tee slongsong' OR $qHeader[0]->parent_product == 'branch joint'){
					$plusSQL = " AND diameter2='".$qHeader[0]->diameter2."'";
				}
				$getDefVal		= $this->db->query("SELECT * FROM help_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." LIMIT 1 ")->result();
				$getDefValNum	= $this->db->query("SELECT * FROM help_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." LIMIT 1 ")->num_rows();
				if($getDefValNum > 0){
					$ArrBqDefault['id_product']				= $product;
					$ArrBqDefault['id_bq']					= $id_bq;
					$ArrBqDefault['id_milik']					= $id_milik;
					$ArrBqDefault['product_parent']			= $getDefVal[0]->product_parent;
					$ArrBqDefault['kd_cust']					= $getDefVal[0]->kd_cust;
					$ArrBqDefault['customer']					= $getDefVal[0]->customer;
					$ArrBqDefault['standart_code']			= $getDefVal[0]->standart_code;
					$ArrBqDefault['diameter']					= $getDefVal[0]->diameter;
					$ArrBqDefault['diameter2']				= $getDefVal[0]->diameter2;
					$ArrBqDefault['liner']					= $getDefVal[0]->liner;
					$ArrBqDefault['pn']						= $getDefVal[0]->pn;
					$ArrBqDefault['overlap']					= $getDefVal[0]->overlap;
					$ArrBqDefault['waste']					= $getDefVal[0]->waste;
					$ArrBqDefault['waste_n1']					= $getDefVal[0]->waste_n1;
					$ArrBqDefault['waste_n2']					= $getDefVal[0]->waste_n2;
					$ArrBqDefault['max']						= $getDefVal[0]->max;
					$ArrBqDefault['min']						= $getDefVal[0]->min;
					$ArrBqDefault['plastic_film']				= $getDefVal[0]->plastic_film;
					$ArrBqDefault['lin_resin_veil_a']			= $getDefVal[0]->lin_resin_veil_a;
					$ArrBqDefault['lin_resin_veil_b']			= $getDefVal[0]->lin_resin_veil_b;
					$ArrBqDefault['lin_resin_veil']			= $getDefVal[0]->lin_resin_veil;
					$ArrBqDefault['lin_resin_veil_add_a']		= $getDefVal[0]->lin_resin_veil_add_a;
					$ArrBqDefault['lin_resin_veil_add_b']		= $getDefVal[0]->lin_resin_veil_add_b;
					$ArrBqDefault['lin_resin_veil_add']		= $getDefVal[0]->lin_resin_veil_add;
					$ArrBqDefault['lin_resin_csm_a']			= $getDefVal[0]->lin_resin_csm_a;
					$ArrBqDefault['lin_resin_csm_b']			= $getDefVal[0]->lin_resin_csm_b;
					$ArrBqDefault['lin_resin_csm']			= $getDefVal[0]->lin_resin_csm;
					$ArrBqDefault['lin_resin_csm_add_a']		= $getDefVal[0]->lin_resin_csm_add_a;
					$ArrBqDefault['lin_resin_csm_add_b']		= $getDefVal[0]->lin_resin_csm_add_b;
					$ArrBqDefault['lin_resin_csm_add']		= $getDefVal[0]->lin_resin_csm_add;
					$ArrBqDefault['lin_faktor_veil']			= $getDefVal[0]->lin_faktor_veil;
					$ArrBqDefault['lin_faktor_veil_add']		= $getDefVal[0]->lin_faktor_veil_add;
					$ArrBqDefault['lin_faktor_csm']			= $getDefVal[0]->lin_faktor_csm;
					$ArrBqDefault['lin_faktor_csm_add']		= $getDefVal[0]->lin_faktor_csm_add;
					$ArrBqDefault['lin_resin']				= $getDefVal[0]->lin_resin;
					$ArrBqDefault['lin_resin_thickness']		= $getDefVal[0]->lin_resin_thickness;
					$ArrBqDefault['str_resin_csm_a']			= $getDefVal[0]->str_resin_csm_a;
					$ArrBqDefault['str_resin_csm_b']			= $getDefVal[0]->str_resin_csm_b;
					$ArrBqDefault['str_resin_csm']			= $getDefVal[0]->str_resin_csm;
					$ArrBqDefault['str_resin_csm_add_a']		= $getDefVal[0]->str_resin_csm_add_a;
					$ArrBqDefault['str_resin_csm_add_b']		= $getDefVal[0]->str_resin_csm_add_b;
					$ArrBqDefault['str_resin_csm_add']		= $getDefVal[0]->str_resin_csm_add;
					$ArrBqDefault['str_resin_wr_a']			= $getDefVal[0]->str_resin_wr_a;
					$ArrBqDefault['str_resin_wr_b']			= $getDefVal[0]->str_resin_wr_b;
					$ArrBqDefault['str_resin_wr']				= $getDefVal[0]->str_resin_wr;
					$ArrBqDefault['str_resin_wr_add_a']		= $getDefVal[0]->str_resin_wr_add_a;
					$ArrBqDefault['str_resin_wr_add_b']		= $getDefVal[0]->str_resin_wr_add_b;
					$ArrBqDefault['str_resin_wr_add']			= $getDefVal[0]->str_resin_wr_add;
					$ArrBqDefault['str_resin_rv_a']			= $getDefVal[0]->str_resin_rv_a;
					$ArrBqDefault['str_resin_rv_b']			= $getDefVal[0]->str_resin_rv_b;
					$ArrBqDefault['str_resin_rv']				= $getDefVal[0]->str_resin_rv;
					$ArrBqDefault['str_resin_rv_add_a']		= $getDefVal[0]->str_resin_rv_add_a;
					$ArrBqDefault['str_resin_rv_add_b']		= $getDefVal[0]->str_resin_rv_add_b;
					$ArrBqDefault['str_resin_rv_add']			= $getDefVal[0]->str_resin_rv_add;
					$ArrBqDefault['str_faktor_csm']			= $getDefVal[0]->str_faktor_csm;
					$ArrBqDefault['str_faktor_csm_add']		= $getDefVal[0]->str_faktor_csm_add;
					$ArrBqDefault['str_faktor_wr']			= $getDefVal[0]->str_faktor_wr;
					$ArrBqDefault['str_faktor_wr_add']		= $getDefVal[0]->str_faktor_wr_add;
					$ArrBqDefault['str_faktor_rv']			= $getDefVal[0]->str_faktor_rv;
					$ArrBqDefault['str_faktor_rv_bw']			= $getDefVal[0]->str_faktor_rv_bw;
					$ArrBqDefault['str_faktor_rv_jb']			= $getDefVal[0]->str_faktor_rv_jb;
					$ArrBqDefault['str_faktor_rv_add']		= $getDefVal[0]->str_faktor_rv_add;
					$ArrBqDefault['str_faktor_rv_add_bw']		= $getDefVal[0]->str_faktor_rv_add_bw;
					$ArrBqDefault['str_faktor_rv_add_jb']		= $getDefVal[0]->str_faktor_rv_add_jb;
					$ArrBqDefault['str_resin']				= $getDefVal[0]->str_resin;
					$ArrBqDefault['str_resin_thickness']		= $getDefVal[0]->str_resin_thickness;
					$ArrBqDefault['eks_resin_veil_a']			= $getDefVal[0]->eks_resin_veil_a;
					$ArrBqDefault['eks_resin_veil_b']			= $getDefVal[0]->eks_resin_veil_b;
					$ArrBqDefault['eks_resin_veil']			= $getDefVal[0]->eks_resin_veil;
					$ArrBqDefault['eks_resin_veil_add_a']		= $getDefVal[0]->eks_resin_veil_add_a;
					$ArrBqDefault['eks_resin_veil_add_b']		= $getDefVal[0]->eks_resin_veil_add_b;
					$ArrBqDefault['eks_resin_veil_add']		= $getDefVal[0]->eks_resin_veil_add;
					$ArrBqDefault['eks_resin_csm_a']			= $getDefVal[0]->eks_resin_csm_a;
					$ArrBqDefault['eks_resin_csm_b']			= $getDefVal[0]->eks_resin_csm_b;
					$ArrBqDefault['eks_resin_csm']			= $getDefVal[0]->eks_resin_csm;
					$ArrBqDefault['eks_resin_csm_add_a']		= $getDefVal[0]->eks_resin_csm_add_a;
					$ArrBqDefault['eks_resin_csm_add_b']		= $getDefVal[0]->eks_resin_csm_add_b;
					$ArrBqDefault['eks_resin_csm_add']		= $getDefVal[0]->eks_resin_csm_add;
					$ArrBqDefault['eks_faktor_veil']			= $getDefVal[0]->eks_faktor_veil;
					$ArrBqDefault['eks_faktor_veil_add']		= $getDefVal[0]->eks_faktor_veil_add;
					$ArrBqDefault['eks_faktor_csm']			= $getDefVal[0]->eks_faktor_csm;
					$ArrBqDefault['eks_faktor_csm_add']		= $getDefVal[0]->eks_faktor_csm_add;
					$ArrBqDefault['eks_resin']				= $getDefVal[0]->eks_resin;
					$ArrBqDefault['eks_resin_thickness']		= $getDefVal[0]->eks_resin_thickness;
					$ArrBqDefault['topcoat_resin']			= $getDefVal[0]->topcoat_resin;
					$ArrBqDefault['str_n1_resin_csm_a']		= $getDefVal[0]->str_n1_resin_csm_a;
					$ArrBqDefault['str_n1_resin_csm_b']		= $getDefVal[0]->str_n1_resin_csm_b;
					$ArrBqDefault['str_n1_resin_csm']			= $getDefVal[0]->str_n1_resin_csm;
					$ArrBqDefault['str_n1_resin_csm_add_a']	= $getDefVal[0]->str_n1_resin_csm_add_a;
					$ArrBqDefault['str_n1_resin_csm_add_b']	= $getDefVal[0]->str_n1_resin_csm_add_b;
					$ArrBqDefault['str_n1_resin_csm_add']		= $getDefVal[0]->str_n1_resin_csm_add;
					$ArrBqDefault['str_n1_resin_wr_a']		= $getDefVal[0]->str_n1_resin_wr_a;
					$ArrBqDefault['str_n1_resin_wr_b']		= $getDefVal[0]->str_n1_resin_wr_b;
					$ArrBqDefault['str_n1_resin_wr']			= $getDefVal[0]->str_n1_resin_wr;
					$ArrBqDefault['str_n1_resin_wr_add_a']	= $getDefVal[0]->str_n1_resin_wr_add_a;
					$ArrBqDefault['str_n1_resin_wr_add_b']	= $getDefVal[0]->str_n1_resin_wr_add_b;
					$ArrBqDefault['str_n1_resin_wr_add']		= $getDefVal[0]->str_n1_resin_wr_add;
					$ArrBqDefault['str_n1_resin_rv_a']		= $getDefVal[0]->str_n1_resin_rv_a;
					$ArrBqDefault['str_n1_resin_rv_b']		= $getDefVal[0]->str_n1_resin_rv_b;
					$ArrBqDefault['str_n1_resin_rv']			= $getDefVal[0]->str_n1_resin_rv;
					$ArrBqDefault['str_n1_resin_rv_add_a']	= $getDefVal[0]->str_n1_resin_rv_add_a;
					$ArrBqDefault['str_n1_resin_rv_add_b']	= $getDefVal[0]->str_n1_resin_rv_add_b;
					$ArrBqDefault['str_n1_resin_rv_add']		= $getDefVal[0]->str_n1_resin_rv_add;
					$ArrBqDefault['str_n1_faktor_csm']		= $getDefVal[0]->str_n1_faktor_csm;
					$ArrBqDefault['str_n1_faktor_csm_add']	= $getDefVal[0]->str_n1_faktor_csm_add;
					$ArrBqDefault['str_n1_faktor_wr']			= $getDefVal[0]->str_n1_faktor_wr;
					$ArrBqDefault['str_n1_faktor_wr_add']		= $getDefVal[0]->str_n1_faktor_wr_add;
					$ArrBqDefault['str_n1_faktor_rv']			= $getDefVal[0]->str_n1_faktor_rv;
					$ArrBqDefault['str_n1_faktor_rv_bw']		= $getDefVal[0]->str_n1_faktor_rv_bw;
					$ArrBqDefault['str_n1_faktor_rv_jb']		= $getDefVal[0]->str_n1_faktor_rv_jb;
					$ArrBqDefault['str_n1_faktor_rv_add']		= $getDefVal[0]->str_n1_faktor_rv_add;
					$ArrBqDefault['str_n1_faktor_rv_add_bw']	= $getDefVal[0]->str_n1_faktor_rv_add_bw;
					$ArrBqDefault['str_n1_faktor_rv_add_jb']	= $getDefVal[0]->str_n1_faktor_rv_add_jb;
					$ArrBqDefault['str_n1_resin']				= $getDefVal[0]->str_n1_resin;
					$ArrBqDefault['str_n1_resin_thickness']	= $getDefVal[0]->str_n1_resin_thickness;
					$ArrBqDefault['str_n2_resin_csm_a']		= $getDefVal[0]->str_n2_resin_csm_a;
					$ArrBqDefault['str_n2_resin_csm_b']		= $getDefVal[0]->str_n2_resin_csm_b;
					$ArrBqDefault['str_n2_resin_csm']			= $getDefVal[0]->str_n2_resin_csm;
					$ArrBqDefault['str_n2_resin_csm_add_a']	= $getDefVal[0]->str_n2_resin_csm_add_a;
					$ArrBqDefault['str_n2_resin_csm_add_b']	= $getDefVal[0]->str_n2_resin_csm_add_b;
					$ArrBqDefault['str_n2_resin_csm_add']		= $getDefVal[0]->str_n2_resin_csm_add;
					$ArrBqDefault['str_n2_resin_wr_a']		= $getDefVal[0]->str_n2_resin_wr_a;
					$ArrBqDefault['str_n2_resin_wr_b']		= $getDefVal[0]->str_n2_resin_wr_b;
					$ArrBqDefault['str_n2_resin_wr']			= $getDefVal[0]->str_n2_resin_wr;
					$ArrBqDefault['str_n2_resin_wr_add_a']	= $getDefVal[0]->str_n2_resin_wr_add_a;
					$ArrBqDefault['str_n2_resin_wr_add_b']	= $getDefVal[0]->str_n2_resin_wr_add_b;
					$ArrBqDefault['str_n2_resin_wr_add']		= $getDefVal[0]->str_n2_resin_wr_add;
					$ArrBqDefault['str_n2_faktor_csm']		= $getDefVal[0]->str_n2_faktor_csm;
					$ArrBqDefault['str_n2_faktor_csm_add']	= $getDefVal[0]->str_n2_faktor_csm_add;
					$ArrBqDefault['str_n2_faktor_wr']			= $getDefVal[0]->str_n2_faktor_wr;
					$ArrBqDefault['str_n2_faktor_wr_add']		= $getDefVal[0]->str_n2_faktor_wr_add;
					$ArrBqDefault['str_n2_resin']				= $getDefVal[0]->str_n2_resin;
					$ArrBqDefault['str_n2_resin_thickness']	= $getDefVal[0]->str_n2_resin_thickness;
					$ArrBqDefault['created_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefault['created_date']				= date('Y-m-d H:i:s');
				}
			}
			
			//Insert Component Header To Hist
			$qHeaderHistDef		= $this->db->query("SELECT * FROM so_component_default WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qHeaderHistDef)){
				foreach($qHeaderHistDef AS $val2HistADef => $valx2HistADef){
					$ArrBqDefaultHist[$val2HistADef]['id_product']				= $valx2HistADef['id_product'];
					$ArrBqDefaultHist[$val2HistADef]['id_milik']				= $valx2HistADef['id_milik'];
					$ArrBqDefaultHist[$val2HistADef]['id_bq']					= $valx2HistADef['id_bq'];
					$ArrBqDefaultHist[$val2HistADef]['product_parent']			= $valx2HistADef['product_parent'];
					$ArrBqDefaultHist[$val2HistADef]['kd_cust']					= $valx2HistADef['kd_cust'];
					$ArrBqDefaultHist[$val2HistADef]['customer']				= $valx2HistADef['customer'];
					$ArrBqDefaultHist[$val2HistADef]['standart_code']			= $valx2HistADef['standart_code'];
					$ArrBqDefaultHist[$val2HistADef]['diameter']				= $valx2HistADef['diameter'];
					$ArrBqDefaultHist[$val2HistADef]['diameter2']				= $valx2HistADef['diameter2'];
					$ArrBqDefaultHist[$val2HistADef]['liner']					= $valx2HistADef['liner'];
					$ArrBqDefaultHist[$val2HistADef]['pn']						= $valx2HistADef['pn'];
					$ArrBqDefaultHist[$val2HistADef]['overlap']					= $valx2HistADef['overlap'];
					$ArrBqDefaultHist[$val2HistADef]['waste']					= $valx2HistADef['waste'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n1']				= $valx2HistADef['waste_n1'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n2']				= $valx2HistADef['waste_n2'];
					$ArrBqDefaultHist[$val2HistADef]['max']						= $valx2HistADef['max'];
					$ArrBqDefaultHist[$val2HistADef]['min']						= $valx2HistADef['min'];
					$ArrBqDefaultHist[$val2HistADef]['plastic_film']			= $valx2HistADef['plastic_film'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_a']		= $valx2HistADef['lin_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_b']		= $valx2HistADef['lin_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil']			= $valx2HistADef['lin_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_a']	= $valx2HistADef['lin_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_b']	= $valx2HistADef['lin_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add']		= $valx2HistADef['lin_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_a']			= $valx2HistADef['lin_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_b']			= $valx2HistADef['lin_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm']			= $valx2HistADef['lin_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_a']		= $valx2HistADef['lin_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_b']		= $valx2HistADef['lin_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add']		= $valx2HistADef['lin_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil']			= $valx2HistADef['lin_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil_add']		= $valx2HistADef['lin_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm']			= $valx2HistADef['lin_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm_add']		= $valx2HistADef['lin_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin']				= $valx2HistADef['lin_resin'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_thickness']		= $valx2HistADef['lin_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_a']			= $valx2HistADef['str_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_b']			= $valx2HistADef['str_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm']			= $valx2HistADef['str_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_a']		= $valx2HistADef['str_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_b']		= $valx2HistADef['str_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add']		= $valx2HistADef['str_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_a']			= $valx2HistADef['str_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_b']			= $valx2HistADef['str_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr']			= $valx2HistADef['str_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_a']		= $valx2HistADef['str_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_b']		= $valx2HistADef['str_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add']		= $valx2HistADef['str_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_a']			= $valx2HistADef['str_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_b']			= $valx2HistADef['str_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv']			= $valx2HistADef['str_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_a']		= $valx2HistADef['str_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_b']		= $valx2HistADef['str_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add']		= $valx2HistADef['str_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm']			= $valx2HistADef['str_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm_add']		= $valx2HistADef['str_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr']			= $valx2HistADef['str_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr_add']		= $valx2HistADef['str_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv']			= $valx2HistADef['str_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_bw']		= $valx2HistADef['str_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_jb']		= $valx2HistADef['str_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add']		= $valx2HistADef['str_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_bw']	= $valx2HistADef['str_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_jb']	= $valx2HistADef['str_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin']				= $valx2HistADef['str_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_thickness']		= $valx2HistADef['str_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_a']		= $valx2HistADef['eks_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_b']		= $valx2HistADef['eks_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil']			= $valx2HistADef['eks_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_a']	= $valx2HistADef['eks_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_b']	= $valx2HistADef['eks_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add']		= $valx2HistADef['eks_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_a']			= $valx2HistADef['eks_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_b']			= $valx2HistADef['eks_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm']			= $valx2HistADef['eks_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_a']		= $valx2HistADef['eks_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_b']		= $valx2HistADef['eks_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add']		= $valx2HistADef['eks_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil']			= $valx2HistADef['eks_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil_add']		= $valx2HistADef['eks_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm']			= $valx2HistADef['eks_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm_add']		= $valx2HistADef['eks_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin']				= $valx2HistADef['eks_resin'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_thickness']		= $valx2HistADef['eks_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['topcoat_resin']			= $valx2HistADef['topcoat_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_a']		= $valx2HistADef['str_n1_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_b']		= $valx2HistADef['str_n1_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm']		= $valx2HistADef['str_n1_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_a']	= $valx2HistADef['str_n1_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_b']	= $valx2HistADef['str_n1_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add']	= $valx2HistADef['str_n1_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_a']		= $valx2HistADef['str_n1_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_b']		= $valx2HistADef['str_n1_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr']			= $valx2HistADef['str_n1_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_a']	= $valx2HistADef['str_n1_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_b']	= $valx2HistADef['str_n1_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add']		= $valx2HistADef['str_n1_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_a']		= $valx2HistADef['str_n1_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_b']		= $valx2HistADef['str_n1_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv']			= $valx2HistADef['str_n1_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_a']	= $valx2HistADef['str_n1_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_b']	= $valx2HistADef['str_n1_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add']		= $valx2HistADef['str_n1_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm']		= $valx2HistADef['str_n1_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm_add']	= $valx2HistADef['str_n1_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr']		= $valx2HistADef['str_n1_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr_add']	= $valx2HistADef['str_n1_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv']		= $valx2HistADef['str_n1_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_bw']		= $valx2HistADef['str_n1_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_jb']		= $valx2HistADef['str_n1_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add']	= $valx2HistADef['str_n1_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_bw']	= $valx2HistADef['str_n1_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_jb']	= $valx2HistADef['str_n1_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin']			= $valx2HistADef['str_n1_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_thickness']	= $valx2HistADef['str_n1_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_a']		= $valx2HistADef['str_n2_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_b']		= $valx2HistADef['str_n2_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm']		= $valx2HistADef['str_n2_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_a']	= $valx2HistADef['str_n2_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_b']	= $valx2HistADef['str_n2_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add']	= $valx2HistADef['str_n2_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_a']		= $valx2HistADef['str_n2_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_b']		= $valx2HistADef['str_n2_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr']			= $valx2HistADef['str_n2_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_a']	= $valx2HistADef['str_n2_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_b']	= $valx2HistADef['str_n2_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add']		= $valx2HistADef['str_n2_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm']		= $valx2HistADef['str_n2_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm_add']	= $valx2HistADef['str_n2_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr']		= $valx2HistADef['str_n2_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr_add']	= $valx2HistADef['str_n2_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin']			= $valx2HistADef['str_n2_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_thickness']	= $valx2HistADef['str_n2_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['created_by']				= $valx2HistADef['created_by'];
					$ArrBqDefaultHist[$val2HistADef]['created_date']			= $valx2HistADef['created_date'];
					$ArrBqDefaultHist[$val2HistADef]['modified_by']				= $valx2HistADef['modified_by'];
					$ArrBqDefaultHist[$val2HistADef]['modified_date']			= $valx2HistADef['modified_date'];
					$ArrBqDefaultHist[$val2HistADef]['hist_by']					= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefaultHist[$val2HistADef]['hist_date']				= date('Y-m-d H:i:s');
					
					
				}
			}
			//================================================================================================================
			//================================================================================================================
			//================================================================================================================
			
			//Insert Component Header To Hist
			$qHeaderHist	= $this->db->query("SELECT * FROM so_component_header WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qHeaderHist)){
				foreach($qHeaderHist AS $val2HistA => $valx2HistA){
					$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
					$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
					$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
					$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
					$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
					$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
					$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
					$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
					$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
					$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
					$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
					$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
					$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
					$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
					$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
					$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
					$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
					$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
					$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
					$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
					$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
					$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
					$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
					$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
					$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length']			= $valx2HistA['wrap_length'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length2']		= $valx2HistA['wrap_length2'];
					$ArrBqHeaderHist[$val2HistA]['high']				= $valx2HistA['high'];
					$ArrBqHeaderHist[$val2HistA]['area2']				= $valx2HistA['area2'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
					$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
					$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
					$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
					$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
					$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
					$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
					$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
					$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
					$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
					$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
					$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
					$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
					$ArrBqHeaderHist[$val2HistA]['deleted_by']			= $valx2HistA['deleted_by'];
					$ArrBqHeaderHist[$val2HistA]['deleted_date']		= $valx2HistA['deleted_date'];
					$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
					
				}
			}

			//Component Detail
			$qDetail	= $this->db->query("SELECT a.*, b.panjang FROM component_detail a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$product."' ")->result_array();
			foreach($qDetail AS $val2 => $valx2){
				$LoopDetail++;
				// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx2['id_material']."' LIMIT 1";
				// $restPrice = $this->db->query($sqlPrice)->result();
				$ArrBqDetail[$LoopDetail]['id_product']		= $product;
				$ArrBqDetail[$LoopDetail]['id_bq']			= $id_bq;
				$ArrBqDetail[$LoopDetail]['id_milik']		= $id_milik;
				$ArrBqDetail[$LoopDetail]['detail_name']	= $valx2['detail_name'];
				$ArrBqDetail[$LoopDetail]['acuhan']			= $valx2['acuhan'];
				$ArrBqDetail[$LoopDetail]['id_ori']			= $valx2['id_ori'];
				$ArrBqDetail[$LoopDetail]['id_ori2']		= $valx2['id_ori2'];
				$ArrBqDetail[$LoopDetail]['id_category']	= $valx2['id_category'];
				$ArrBqDetail[$LoopDetail]['nm_category']	= $valx2['nm_category'];
				$ArrBqDetail[$LoopDetail]['id_material']	= $valx2['id_material'];
				$ArrBqDetail[$LoopDetail]['nm_material']	= $valx2['nm_material'];
				$ArrBqDetail[$LoopDetail]['value']			= $valx2['value'];
				$ArrBqDetail[$LoopDetail]['thickness']		= $valx2['thickness'];
				$ArrBqDetail[$LoopDetail]['fak_pengali']	= $valx2['fak_pengali'];
				$ArrBqDetail[$LoopDetail]['bw']				= $valx2['bw'];
				$ArrBqDetail[$LoopDetail]['jumlah']			= $valx2['jumlah'];
				$ArrBqDetail[$LoopDetail]['layer']			= $valx2['layer'];
				$ArrBqDetail[$LoopDetail]['containing']		= $valx2['containing'];
				$ArrBqDetail[$LoopDetail]['total_thickness']	= $valx2['total_thickness'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / floatval($valx2['panjang']))* (floatval($panjang) + 400);
				}
				elseif ($qHeader[0]->parent_product == 'branch joint' OR $qHeader[0]->parent_product == 'field joint' OR $qHeader[0]->parent_product == 'shop joint') {
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['material_weight'];
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / 1000) * floatval($panjang);
				}
				else{
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['last_cost'];
				}
				$ArrBqDetail[$LoopDetail]['rev']				= $qHeader[0]->rev;
				//
				$ArrBqDetail[$LoopDetail]['area_weight']		= $valx2['area_weight'];
				$ArrBqDetail[$LoopDetail]['material_weight']	= $valx2['material_weight'];
				$ArrBqDetail[$LoopDetail]['percentage']			= $valx2['percentage'];
				$ArrBqDetail[$LoopDetail]['resin_content']		= $valx2['resin_content'];

				$ArrBqDetail[$LoopDetail]['price_mat']				= get_price_ref($valx2['id_material']);
			}

			//Component Lamination
			$qDetailLam	= $this->db->query("SELECT * FROM component_lamination WHERE id_product='".$product."' ")->result_array();
			foreach($qDetailLam AS $val2 => $valx2){
				$LoopDetailLam++;
				$ArrBqDetailLam[$LoopDetailLam]['id_product']	= $product;
				$ArrBqDetailLam[$LoopDetailLam]['id_bq']		= $id_bq;
				$ArrBqDetailLam[$LoopDetailLam]['id_milik']		= $id_milik;
				$ArrBqDetailLam[$LoopDetailLam]['detail_name']	= $valx2['detail_name'];
				$ArrBqDetailLam[$LoopDetailLam]['lapisan']		= $valx2['lapisan'];
				$ArrBqDetailLam[$LoopDetailLam]['std_glass']	= $valx2['std_glass'];
				$ArrBqDetailLam[$LoopDetailLam]['width']		= $valx2['width'];
				$ArrBqDetailLam[$LoopDetailLam]['stage']		= $valx2['stage'];
				$ArrBqDetailLam[$LoopDetailLam]['glass']		= $valx2['glass'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_1']	= $valx2['thickness_1'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_2']	= $valx2['thickness_2'];
				$ArrBqDetailLam[$LoopDetailLam]['glass_length']	= $valx2['glass_length'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_veil']	= $valx2['weight_veil'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_csm']	= $valx2['weight_csm'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_wr']	= $valx2['weight_wr'];
			}

			//Insert Component Detail To Hist
			$qDetailHist	= $this->db->query("SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailHist)){
				foreach($qDetailHist AS $val2Hist => $valx2Hist){
					$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
					$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
					$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
					$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
					$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
					$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
					$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
					$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
					$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
					$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
					$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
					$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
					$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
					$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
					$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
					$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
					$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
					$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
					$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
					$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
					$ArrBqDetailHist[$val2Hist]['rev']				= $valx2Hist['rev'];
					$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
					$ArrBqDetailHist[$val2Hist]['hist_by']			= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Plus
			$qDetailPlus	= $this->db->query("SELECT a.*, b.panjang FROM component_detail_plus a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$product."' ")->result_array();
			foreach($qDetailPlus AS $val3 => $valx3){
				$LoopDetailPlus++;
				// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx2['id_material']."' LIMIT 1";
				// $restPrice = $this->db->query($sqlPrice)->result();
				$ArrBqDetailPlus[$LoopDetailPlus]['id_product']		= $product;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_bq']			= $id_bq;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_milik']		= $id_milik;
				$ArrBqDetailPlus[$LoopDetailPlus]['detail_name']	= $valx3['detail_name'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori']			= $valx3['id_ori'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori2']		= $valx3['id_ori2'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_category']	= $valx3['id_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_category']	= $valx3['nm_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_material']	= $valx3['id_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_material']	= $valx3['nm_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['containing']		= $valx3['containing'];
				$ArrBqDetailPlus[$LoopDetailPlus]['perse']			= $valx3['perse'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($valx3['panjang'])) * (floatval($panjang) + 400);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($valx3['panjang'])) * (floatval($panjang) + 400);
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / 1000) * floatval($panjang);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / 1000) * floatval($panjang);
				}
				else{
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= $valx3['last_full'];
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= $valx3['last_cost'];
				}
				$ArrBqDetailPlus[$LoopDetailPlus]['rev']			= $qHeader[0]->rev;
				$ArrBqDetailPlus[$LoopDetailPlus]['price_mat']			= get_price_ref($valx3['id_material']);
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailPlusHist)){
				foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
					$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
					$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
					$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
					$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
					$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
					$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
					$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
					$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
					$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
					$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
					$ArrBqDetailPlusHist[$val3Hist]['rev']			= $valx3Hist['rev'];
					$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Add
			$qDetailAdd		= $this->db->query("SELECT a.*, b.panjang FROM component_detail_add a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$product."' ")->result_array();
			if(!empty($qDetailAdd)){
				foreach($qDetailAdd AS $val4 => $valx4){
					$LoopDetailAdd++;
					// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx2['id_material']."' LIMIT 1";
					// $restPrice = $this->db->query($sqlPrice)->result();
					$ArrBqDetailAdd[$LoopDetailAdd]['id_product']		= $product;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_bq']			= $id_bq;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_milik']			= $id_milik;
					$ArrBqDetailAdd[$LoopDetailAdd]['detail_name']		= $valx4['detail_name'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_category']		= $valx4['id_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_category']		= $valx4['nm_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_material']		= $valx4['id_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_material']		= $valx4['nm_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['containing']		= $valx4['containing'];
					$ArrBqDetailAdd[$LoopDetailAdd]['perse']			= $valx4['perse'];
					if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= (floatval($valx4['last_full']) / floatval($valx4['panjang'])) * (floatval($panjang) + 400);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= (floatval($valx4['last_cost']) / floatval($valx4['panjang'])) * (floatval($panjang) + 400);
					}
					elseif($qHeader[0]->parent_product == 'frp pipe'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= (floatval($valx4['last_full']) / 1000) * floatval($panjang);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= (floatval($valx4['last_cost']) / 1000) * floatval($panjang);
					}
					else{
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= $valx4['last_full'];
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= $valx4['last_cost'];
					}
					$ArrBqDetailAdd[$LoopDetailAdd]['rev']				= $qHeader[0]->rev;
					$ArrBqDetailAdd[$LoopDetailAdd]['price_mat']		= get_price_ref($valx4['id_material']);
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailAddHist)){
				foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
					$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
					$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
					$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
					$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
					$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
					$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
					$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
					$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
					$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
					$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
					$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
					$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
					$ArrBqDetailAddHist[$val4Hist]['rev']			= $valx4Hist['rev'];
					$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
					$ArrBqDetailAddHist[$val4Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailAddHist[$val4Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Footer
			$qDetailFooter	= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$product."' ")->result_array();
			if (count($qDetailFooter)>0)
			{
				foreach($qDetailFooter AS $val5 => $valx5){
					$LoopFooter++;
					$ArrBqFooter[$LoopFooter]['id_product']		= $product;
					$ArrBqFooter[$LoopFooter]['id_bq']			= $id_bq;
					$ArrBqFooter[$LoopFooter]['id_milik']		= $id_milik;
					$ArrBqFooter[$LoopFooter]['detail_name']	= $valx5['detail_name'];
					$ArrBqFooter[$LoopFooter]['total']			= $valx5['total'];
					$ArrBqFooter[$LoopFooter]['min']			= $valx5['min'];
					$ArrBqFooter[$LoopFooter]['max']			= $valx5['max'];
					$ArrBqFooter[$LoopFooter]['hasil']			= $valx5['hasil'];
					$ArrBqFooter[$LoopFooter]['rev']			= $qHeader[0]->rev;
				}
			}
			//Insert Component Footer To Hist
			$qDetailFooterHist		= $this->db->query("SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailFooterHist)){
				foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
					$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
					$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
					$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
					$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
					$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
					$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
					$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
					$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
					$ArrBqFooterHist[$val5Hist]['rev']			= $valx5Hist['rev'];
					$ArrBqFooterHist[$val5Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}
		

		// print_r($ArrBqHeader);
		// print_r($ArrBqDefault);
		// echo "</pre>";
		// exit;

		$UpdateBQ	= array(
			'estimasi'	=> 'Y',
			'est_by'	=> $this->session->userdata['ORI_User']['username'],
			'est_date'	=> date('Y-m-d H:i:s')
		);
		
		$ArrDetBq2	= array(
			'id_product'	=> $product
		);

		$this->db->trans_start();
			$this->db->where('id', $id_milik);
			$this->db->update('so_detail_header', $ArrDetBq2);

			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_so_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_so_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(count($ArrBqFooterHist)>0){
			// 	$this->db->insert_batch('hist_so_component_footer', $ArrBqFooterHist);
			// }
			// if(!empty($ArrBqDefaultHist)){
			// 	$this->db->insert_batch('hist_so_component_default', $ArrBqDefaultHist);
			// }

			//Delete BQ Component
			$this->db->delete('so_component_header', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_detail', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_lamination', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_detail_plus', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_detail_add', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_footer', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_default', array('id_bq' => $id_bq, 'id_milik' => $id_milik));

			//Insert BQ Component
			if(!empty($ArrBqHeader)){
				$this->db->insert('so_component_header', $ArrBqHeader);
			}
			if(!empty($ArrBqDetail)){
				$this->db->insert_batch('so_component_detail', $ArrBqDetail);
			}
			if(!empty($ArrBqDetailLam)){
				$this->db->insert_batch('so_component_lamination', $ArrBqDetailLam);
			}
			if(!empty($ArrBqDetailPlus)){
				$this->db->insert_batch('so_component_detail_plus', $ArrBqDetailPlus);
			}
			if(!empty($ArrBqDetailAdd)){
				$this->db->insert_batch('so_component_detail_add', $ArrBqDetailAdd);
			}
			if(!empty($ArrBqFooter)){
				$this->db->insert_batch('so_component_footer', $ArrBqFooter);
			}
			if(!empty($ArrBqDefault)){
				$this->db->insert('so_component_default', $ArrBqDefault);
			}

			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $UpdateBQ);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pesan'		=>'Estimation structure bq data success. Thanks ...',
				'status'	=> 1
			);
			history('Estimation Sebagian Structure BQ in Final Drawing with code : '.$id_bq.' / '.$id_milik.' / '.$product);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function update_satuan_est_bq(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
	
		$id_bq			= $this->uri->segment(3);
		$id_milik		= $this->uri->segment(4);
		$panjang		= $this->uri->segment(5);
		$product		= $this->uri->segment(6);
		$id_milik_bq	= $this->uri->segment(7);

		$ArrBqHeader			= array();
		$ArrBqDetail			= array();
		$ArrBqDetailPlus		= array();
		$ArrBqDetailAdd			= array();
		$ArrBqFooter			= array();
		$ArrBqHeaderHist		= array();
		$ArrBqDetailHist		= array();
		$ArrBqDetailPlusHist	= array();
		$ArrBqDetailAddHist		= array();
		$ArrBqFooterHist		= array();
		$ArrBqDefault			= array();
		$ArrBqDefaultHist		= array();

		$LoopDetail = 0;
		$LoopDetailLam = 0;
		$LoopDetailPlus = 0;
		$LoopDetailAdd = 0;
		$LoopFooter = 0;
		
		//Component Header
		$qHeader	= $this->db->query("SELECT * FROM bq_component_header WHERE id_product='".$product."' AND id_milik='".$id_milik_bq."' LIMIT 1 ")->result();
		
		if(!empty($qHeader)){
			$PANJANG_BEF = $qHeader[0]->panjang;
			$ArrBqHeader['id_product']			= $product;
			$ArrBqHeader['id_bq']					= $id_bq;
			$ArrBqHeader['id_milik']				= $id_milik;
			$ArrBqHeader['parent_product']		= $qHeader[0]->parent_product;
			$ArrBqHeader['nm_product']			= $qHeader[0]->nm_product;
			$ArrBqHeader['standart_code']			= $qHeader[0]->standart_code;
			$ArrBqHeader['series']				= $qHeader[0]->series;
			$ArrBqHeader['resin_sistem']			= $qHeader[0]->resin_sistem;
			$ArrBqHeader['pressure']				= $qHeader[0]->pressure;
			$ArrBqHeader['diameter']				= $qHeader[0]->diameter;
			$ArrBqHeader['liner']					= $qHeader[0]->liner;
			$ArrBqHeader['aplikasi_product']		= $qHeader[0]->aplikasi_product;
			$ArrBqHeader['criminal_barier']		= $qHeader[0]->criminal_barier;
			$ArrBqHeader['vacum_rate']			= $qHeader[0]->vacum_rate;
			$ArrBqHeader['stiffness']				= $qHeader[0]->stiffness;
			$ArrBqHeader['design_life']			= $qHeader[0]->design_life; 
			$ArrBqHeader['standart_by']			= $qHeader[0]->standart_by;
			$ArrBqHeader['standart_toleransi']	= $qHeader[0]->standart_toleransi;
			$ArrBqHeader['diameter2']				= $qHeader[0]->diameter2;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader['panjang']			= floatval($panjang) + 400;
			}
			else{
				$ArrBqHeader['panjang']			= $qHeader[0]->panjang;
			}
			$ArrBqHeader['radius']				= $qHeader[0]->radius;
			$ArrBqHeader['type_elbow']			= $qHeader[0]->type_elbow;
			$ArrBqHeader['angle']				= $qHeader[0]->angle;
			$ArrBqHeader['design']				= $qHeader[0]->design;
			$ArrBqHeader['est']					= $qHeader[0]->est;
			$ArrBqHeader['min_toleransi']		= $qHeader[0]->min_toleransi;
			$ArrBqHeader['max_toleransi']		= $qHeader[0]->max_toleransi;
			$ArrBqHeader['waste']				= $qHeader[0]->waste;
			$ArrBqHeader['area']				= $qHeader[0]->area;
			$ArrBqHeader['wrap_length']		= $qHeader[0]->wrap_length;
			$ArrBqHeader['wrap_length2']		= $qHeader[0]->wrap_length2;
			$ArrBqHeader['high']				= $qHeader[0]->high;
			$ArrBqHeader['area2']				= $qHeader[0]->area2;
			$ArrBqHeader['panjang_neck_1']	= $qHeader[0]->panjang_neck_1;
			$ArrBqHeader['panjang_neck_2']	= $qHeader[0]->panjang_neck_2;
			$ArrBqHeader['design_neck_1']		= $qHeader[0]->design_neck_1;
			$ArrBqHeader['design_neck_2']		= $qHeader[0]->design_neck_2;
			$ArrBqHeader['est_neck_1']		= $qHeader[0]->est_neck_1;
			$ArrBqHeader['est_neck_2']		= $qHeader[0]->est_neck_2;
			$ArrBqHeader['area_neck_1']		= $qHeader[0]->area_neck_1;
			$ArrBqHeader['area_neck_2']		= $qHeader[0]->area_neck_2;
			$ArrBqHeader['flange_od']			= $qHeader[0]->flange_od;
			$ArrBqHeader['flange_bcd']		= $qHeader[0]->flange_bcd;
			$ArrBqHeader['flange_n']			= $qHeader[0]->flange_n;
			$ArrBqHeader['flange_oh']			= $qHeader[0]->flange_oh;
			$ArrBqHeader['rev']				= $qHeader[0]->rev;
			$ArrBqHeader['status']			= $qHeader[0]->status;
			$ArrBqHeader['approve_by']		= $qHeader[0]->approve_by;
			$ArrBqHeader['approve_date']		= $qHeader[0]->approve_date;
			$ArrBqHeader['approve_reason']	= $qHeader[0]->approve_reason;
			$ArrBqHeader['sts_price']			= $qHeader[0]->sts_price;
			$ArrBqHeader['sts_price_by']		= $qHeader[0]->sts_price_by;
			$ArrBqHeader['sts_price_date']	= $qHeader[0]->sts_price_date;
			$ArrBqHeader['sts_price_reason']	= $qHeader[0]->sts_price_reason;
			$ArrBqHeader['created_by']		= $qHeader[0]->created_by;
			$ArrBqHeader['created_date']		= $qHeader[0]->created_date;
			$ArrBqHeader['deleted']			= $qHeader[0]->deleted;
			$ArrBqHeader['deleted_by']		= $qHeader[0]->deleted_by;
			$ArrBqHeader['deleted_date']		= $qHeader[0]->deleted_date;
			//
			$ArrBqHeader['pipe_thickness']	= $qHeader[0]->pipe_thickness;
			$ArrBqHeader['joint_thickness']	= $qHeader[0]->joint_thickness;
			$ArrBqHeader['factor_thickness']	= $qHeader[0]->factor_thickness;
			$ArrBqHeader['factor']			= $qHeader[0]->factor;
			
			// print_r($ArrBqHeader);
			// exit;
			//================================================================================================================
			//============================================DEFAULT BY ARWANT===================================================
			//================================================================================================================
			if(!empty($qHeader[0]->standart_code)){
				$plusSQL = "";
				if($qHeader[0]->parent_product == 'concentric reducer' OR $qHeader[0]->parent_product == 'reducer tee mould' OR $qHeader[0]->parent_product == 'eccentric reducer' OR $qHeader[0]->parent_product == 'reducer tee slongsong' OR $qHeader[0]->parent_product == 'branch joint'){
					$plusSQL = " AND diameter2='".$qHeader[0]->diameter2."'";
				}
				$getDefVal		= $this->db->query("SELECT * FROM bq_component_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." AND id_milik='".$id_milik_bq."' LIMIT 1 ")->result();
				if(!empty($getDefVal)){
					$ArrBqDefault['id_product']				= $product;
					$ArrBqDefault['id_bq']					= $id_bq;
					$ArrBqDefault['id_milik']					= $id_milik;
					$ArrBqDefault['product_parent']			= $getDefVal[0]->product_parent;
					$ArrBqDefault['kd_cust']					= $getDefVal[0]->kd_cust;
					$ArrBqDefault['customer']					= $getDefVal[0]->customer;
					$ArrBqDefault['standart_code']			= $getDefVal[0]->standart_code;
					$ArrBqDefault['diameter']					= $getDefVal[0]->diameter;
					$ArrBqDefault['diameter2']				= $getDefVal[0]->diameter2;
					$ArrBqDefault['liner']					= $getDefVal[0]->liner;
					$ArrBqDefault['pn']						= $getDefVal[0]->pn;
					$ArrBqDefault['overlap']					= $getDefVal[0]->overlap;
					$ArrBqDefault['waste']					= $getDefVal[0]->waste;
					$ArrBqDefault['waste_n1']					= $getDefVal[0]->waste_n1;
					$ArrBqDefault['waste_n2']					= $getDefVal[0]->waste_n2;
					$ArrBqDefault['max']						= $getDefVal[0]->max;
					$ArrBqDefault['min']						= $getDefVal[0]->min;
					$ArrBqDefault['plastic_film']				= $getDefVal[0]->plastic_film;
					$ArrBqDefault['lin_resin_veil_a']			= $getDefVal[0]->lin_resin_veil_a;
					$ArrBqDefault['lin_resin_veil_b']			= $getDefVal[0]->lin_resin_veil_b;
					$ArrBqDefault['lin_resin_veil']			= $getDefVal[0]->lin_resin_veil;
					$ArrBqDefault['lin_resin_veil_add_a']		= $getDefVal[0]->lin_resin_veil_add_a;
					$ArrBqDefault['lin_resin_veil_add_b']		= $getDefVal[0]->lin_resin_veil_add_b;
					$ArrBqDefault['lin_resin_veil_add']		= $getDefVal[0]->lin_resin_veil_add;
					$ArrBqDefault['lin_resin_csm_a']			= $getDefVal[0]->lin_resin_csm_a;
					$ArrBqDefault['lin_resin_csm_b']			= $getDefVal[0]->lin_resin_csm_b;
					$ArrBqDefault['lin_resin_csm']			= $getDefVal[0]->lin_resin_csm;
					$ArrBqDefault['lin_resin_csm_add_a']		= $getDefVal[0]->lin_resin_csm_add_a;
					$ArrBqDefault['lin_resin_csm_add_b']		= $getDefVal[0]->lin_resin_csm_add_b;
					$ArrBqDefault['lin_resin_csm_add']		= $getDefVal[0]->lin_resin_csm_add;
					$ArrBqDefault['lin_faktor_veil']			= $getDefVal[0]->lin_faktor_veil;
					$ArrBqDefault['lin_faktor_veil_add']		= $getDefVal[0]->lin_faktor_veil_add;
					$ArrBqDefault['lin_faktor_csm']			= $getDefVal[0]->lin_faktor_csm;
					$ArrBqDefault['lin_faktor_csm_add']		= $getDefVal[0]->lin_faktor_csm_add;
					$ArrBqDefault['lin_resin']				= $getDefVal[0]->lin_resin;
					$ArrBqDefault['lin_resin_thickness']		= $getDefVal[0]->lin_resin_thickness;
					$ArrBqDefault['str_resin_csm_a']			= $getDefVal[0]->str_resin_csm_a;
					$ArrBqDefault['str_resin_csm_b']			= $getDefVal[0]->str_resin_csm_b;
					$ArrBqDefault['str_resin_csm']			= $getDefVal[0]->str_resin_csm;
					$ArrBqDefault['str_resin_csm_add_a']		= $getDefVal[0]->str_resin_csm_add_a;
					$ArrBqDefault['str_resin_csm_add_b']		= $getDefVal[0]->str_resin_csm_add_b;
					$ArrBqDefault['str_resin_csm_add']		= $getDefVal[0]->str_resin_csm_add;
					$ArrBqDefault['str_resin_wr_a']			= $getDefVal[0]->str_resin_wr_a;
					$ArrBqDefault['str_resin_wr_b']			= $getDefVal[0]->str_resin_wr_b;
					$ArrBqDefault['str_resin_wr']				= $getDefVal[0]->str_resin_wr;
					$ArrBqDefault['str_resin_wr_add_a']		= $getDefVal[0]->str_resin_wr_add_a;
					$ArrBqDefault['str_resin_wr_add_b']		= $getDefVal[0]->str_resin_wr_add_b;
					$ArrBqDefault['str_resin_wr_add']			= $getDefVal[0]->str_resin_wr_add;
					$ArrBqDefault['str_resin_rv_a']			= $getDefVal[0]->str_resin_rv_a;
					$ArrBqDefault['str_resin_rv_b']			= $getDefVal[0]->str_resin_rv_b;
					$ArrBqDefault['str_resin_rv']				= $getDefVal[0]->str_resin_rv;
					$ArrBqDefault['str_resin_rv_add_a']		= $getDefVal[0]->str_resin_rv_add_a;
					$ArrBqDefault['str_resin_rv_add_b']		= $getDefVal[0]->str_resin_rv_add_b;
					$ArrBqDefault['str_resin_rv_add']			= $getDefVal[0]->str_resin_rv_add;
					$ArrBqDefault['str_faktor_csm']			= $getDefVal[0]->str_faktor_csm;
					$ArrBqDefault['str_faktor_csm_add']		= $getDefVal[0]->str_faktor_csm_add;
					$ArrBqDefault['str_faktor_wr']			= $getDefVal[0]->str_faktor_wr;
					$ArrBqDefault['str_faktor_wr_add']		= $getDefVal[0]->str_faktor_wr_add;
					$ArrBqDefault['str_faktor_rv']			= $getDefVal[0]->str_faktor_rv;
					$ArrBqDefault['str_faktor_rv_bw']			= $getDefVal[0]->str_faktor_rv_bw;
					$ArrBqDefault['str_faktor_rv_jb']			= $getDefVal[0]->str_faktor_rv_jb;
					$ArrBqDefault['str_faktor_rv_add']		= $getDefVal[0]->str_faktor_rv_add;
					$ArrBqDefault['str_faktor_rv_add_bw']		= $getDefVal[0]->str_faktor_rv_add_bw;
					$ArrBqDefault['str_faktor_rv_add_jb']		= $getDefVal[0]->str_faktor_rv_add_jb;
					$ArrBqDefault['str_resin']				= $getDefVal[0]->str_resin;
					$ArrBqDefault['str_resin_thickness']		= $getDefVal[0]->str_resin_thickness;
					$ArrBqDefault['eks_resin_veil_a']			= $getDefVal[0]->eks_resin_veil_a;
					$ArrBqDefault['eks_resin_veil_b']			= $getDefVal[0]->eks_resin_veil_b;
					$ArrBqDefault['eks_resin_veil']			= $getDefVal[0]->eks_resin_veil;
					$ArrBqDefault['eks_resin_veil_add_a']		= $getDefVal[0]->eks_resin_veil_add_a;
					$ArrBqDefault['eks_resin_veil_add_b']		= $getDefVal[0]->eks_resin_veil_add_b;
					$ArrBqDefault['eks_resin_veil_add']		= $getDefVal[0]->eks_resin_veil_add;
					$ArrBqDefault['eks_resin_csm_a']			= $getDefVal[0]->eks_resin_csm_a;
					$ArrBqDefault['eks_resin_csm_b']			= $getDefVal[0]->eks_resin_csm_b;
					$ArrBqDefault['eks_resin_csm']			= $getDefVal[0]->eks_resin_csm;
					$ArrBqDefault['eks_resin_csm_add_a']		= $getDefVal[0]->eks_resin_csm_add_a;
					$ArrBqDefault['eks_resin_csm_add_b']		= $getDefVal[0]->eks_resin_csm_add_b;
					$ArrBqDefault['eks_resin_csm_add']		= $getDefVal[0]->eks_resin_csm_add;
					$ArrBqDefault['eks_faktor_veil']			= $getDefVal[0]->eks_faktor_veil;
					$ArrBqDefault['eks_faktor_veil_add']		= $getDefVal[0]->eks_faktor_veil_add;
					$ArrBqDefault['eks_faktor_csm']			= $getDefVal[0]->eks_faktor_csm;
					$ArrBqDefault['eks_faktor_csm_add']		= $getDefVal[0]->eks_faktor_csm_add;
					$ArrBqDefault['eks_resin']				= $getDefVal[0]->eks_resin;
					$ArrBqDefault['eks_resin_thickness']		= $getDefVal[0]->eks_resin_thickness;
					$ArrBqDefault['topcoat_resin']			= $getDefVal[0]->topcoat_resin;
					$ArrBqDefault['str_n1_resin_csm_a']		= $getDefVal[0]->str_n1_resin_csm_a;
					$ArrBqDefault['str_n1_resin_csm_b']		= $getDefVal[0]->str_n1_resin_csm_b;
					$ArrBqDefault['str_n1_resin_csm']			= $getDefVal[0]->str_n1_resin_csm;
					$ArrBqDefault['str_n1_resin_csm_add_a']	= $getDefVal[0]->str_n1_resin_csm_add_a;
					$ArrBqDefault['str_n1_resin_csm_add_b']	= $getDefVal[0]->str_n1_resin_csm_add_b;
					$ArrBqDefault['str_n1_resin_csm_add']		= $getDefVal[0]->str_n1_resin_csm_add;
					$ArrBqDefault['str_n1_resin_wr_a']		= $getDefVal[0]->str_n1_resin_wr_a;
					$ArrBqDefault['str_n1_resin_wr_b']		= $getDefVal[0]->str_n1_resin_wr_b;
					$ArrBqDefault['str_n1_resin_wr']			= $getDefVal[0]->str_n1_resin_wr;
					$ArrBqDefault['str_n1_resin_wr_add_a']	= $getDefVal[0]->str_n1_resin_wr_add_a;
					$ArrBqDefault['str_n1_resin_wr_add_b']	= $getDefVal[0]->str_n1_resin_wr_add_b;
					$ArrBqDefault['str_n1_resin_wr_add']		= $getDefVal[0]->str_n1_resin_wr_add;
					$ArrBqDefault['str_n1_resin_rv_a']		= $getDefVal[0]->str_n1_resin_rv_a;
					$ArrBqDefault['str_n1_resin_rv_b']		= $getDefVal[0]->str_n1_resin_rv_b;
					$ArrBqDefault['str_n1_resin_rv']			= $getDefVal[0]->str_n1_resin_rv;
					$ArrBqDefault['str_n1_resin_rv_add_a']	= $getDefVal[0]->str_n1_resin_rv_add_a;
					$ArrBqDefault['str_n1_resin_rv_add_b']	= $getDefVal[0]->str_n1_resin_rv_add_b;
					$ArrBqDefault['str_n1_resin_rv_add']		= $getDefVal[0]->str_n1_resin_rv_add;
					$ArrBqDefault['str_n1_faktor_csm']		= $getDefVal[0]->str_n1_faktor_csm;
					$ArrBqDefault['str_n1_faktor_csm_add']	= $getDefVal[0]->str_n1_faktor_csm_add;
					$ArrBqDefault['str_n1_faktor_wr']			= $getDefVal[0]->str_n1_faktor_wr;
					$ArrBqDefault['str_n1_faktor_wr_add']		= $getDefVal[0]->str_n1_faktor_wr_add;
					$ArrBqDefault['str_n1_faktor_rv']			= $getDefVal[0]->str_n1_faktor_rv;
					$ArrBqDefault['str_n1_faktor_rv_bw']		= $getDefVal[0]->str_n1_faktor_rv_bw;
					$ArrBqDefault['str_n1_faktor_rv_jb']		= $getDefVal[0]->str_n1_faktor_rv_jb;
					$ArrBqDefault['str_n1_faktor_rv_add']		= $getDefVal[0]->str_n1_faktor_rv_add;
					$ArrBqDefault['str_n1_faktor_rv_add_bw']	= $getDefVal[0]->str_n1_faktor_rv_add_bw;
					$ArrBqDefault['str_n1_faktor_rv_add_jb']	= $getDefVal[0]->str_n1_faktor_rv_add_jb;
					$ArrBqDefault['str_n1_resin']				= $getDefVal[0]->str_n1_resin;
					$ArrBqDefault['str_n1_resin_thickness']	= $getDefVal[0]->str_n1_resin_thickness;
					$ArrBqDefault['str_n2_resin_csm_a']		= $getDefVal[0]->str_n2_resin_csm_a;
					$ArrBqDefault['str_n2_resin_csm_b']		= $getDefVal[0]->str_n2_resin_csm_b;
					$ArrBqDefault['str_n2_resin_csm']			= $getDefVal[0]->str_n2_resin_csm;
					$ArrBqDefault['str_n2_resin_csm_add_a']	= $getDefVal[0]->str_n2_resin_csm_add_a;
					$ArrBqDefault['str_n2_resin_csm_add_b']	= $getDefVal[0]->str_n2_resin_csm_add_b;
					$ArrBqDefault['str_n2_resin_csm_add']		= $getDefVal[0]->str_n2_resin_csm_add;
					$ArrBqDefault['str_n2_resin_wr_a']		= $getDefVal[0]->str_n2_resin_wr_a;
					$ArrBqDefault['str_n2_resin_wr_b']		= $getDefVal[0]->str_n2_resin_wr_b;
					$ArrBqDefault['str_n2_resin_wr']			= $getDefVal[0]->str_n2_resin_wr;
					$ArrBqDefault['str_n2_resin_wr_add_a']	= $getDefVal[0]->str_n2_resin_wr_add_a;
					$ArrBqDefault['str_n2_resin_wr_add_b']	= $getDefVal[0]->str_n2_resin_wr_add_b;
					$ArrBqDefault['str_n2_resin_wr_add']		= $getDefVal[0]->str_n2_resin_wr_add;
					$ArrBqDefault['str_n2_faktor_csm']		= $getDefVal[0]->str_n2_faktor_csm;
					$ArrBqDefault['str_n2_faktor_csm_add']	= $getDefVal[0]->str_n2_faktor_csm_add;
					$ArrBqDefault['str_n2_faktor_wr']			= $getDefVal[0]->str_n2_faktor_wr;
					$ArrBqDefault['str_n2_faktor_wr_add']		= $getDefVal[0]->str_n2_faktor_wr_add;
					$ArrBqDefault['str_n2_resin']				= $getDefVal[0]->str_n2_resin;
					$ArrBqDefault['str_n2_resin_thickness']	= $getDefVal[0]->str_n2_resin_thickness;
					$ArrBqDefault['created_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefault['created_date']				= date('Y-m-d H:i:s');
				}
			}
			
			//Insert Component Header To Hist
			$qHeaderHistDef		= $this->db->query("SELECT * FROM so_component_default WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qHeaderHistDef)){
				foreach($qHeaderHistDef AS $val2HistADef => $valx2HistADef){
					$ArrBqDefaultHist[$val2HistADef]['id_product']				= $valx2HistADef['id_product'];
					$ArrBqDefaultHist[$val2HistADef]['id_milik']				= $valx2HistADef['id_milik'];
					$ArrBqDefaultHist[$val2HistADef]['id_bq']					= $valx2HistADef['id_bq'];
					$ArrBqDefaultHist[$val2HistADef]['product_parent']			= $valx2HistADef['product_parent'];
					$ArrBqDefaultHist[$val2HistADef]['kd_cust']					= $valx2HistADef['kd_cust'];
					$ArrBqDefaultHist[$val2HistADef]['customer']				= $valx2HistADef['customer'];
					$ArrBqDefaultHist[$val2HistADef]['standart_code']			= $valx2HistADef['standart_code'];
					$ArrBqDefaultHist[$val2HistADef]['diameter']				= $valx2HistADef['diameter'];
					$ArrBqDefaultHist[$val2HistADef]['diameter2']				= $valx2HistADef['diameter2'];
					$ArrBqDefaultHist[$val2HistADef]['liner']					= $valx2HistADef['liner'];
					$ArrBqDefaultHist[$val2HistADef]['pn']						= $valx2HistADef['pn'];
					$ArrBqDefaultHist[$val2HistADef]['overlap']					= $valx2HistADef['overlap'];
					$ArrBqDefaultHist[$val2HistADef]['waste']					= $valx2HistADef['waste'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n1']				= $valx2HistADef['waste_n1'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n2']				= $valx2HistADef['waste_n2'];
					$ArrBqDefaultHist[$val2HistADef]['max']						= $valx2HistADef['max'];
					$ArrBqDefaultHist[$val2HistADef]['min']						= $valx2HistADef['min'];
					$ArrBqDefaultHist[$val2HistADef]['plastic_film']			= $valx2HistADef['plastic_film'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_a']		= $valx2HistADef['lin_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_b']		= $valx2HistADef['lin_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil']			= $valx2HistADef['lin_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_a']	= $valx2HistADef['lin_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_b']	= $valx2HistADef['lin_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add']		= $valx2HistADef['lin_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_a']			= $valx2HistADef['lin_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_b']			= $valx2HistADef['lin_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm']			= $valx2HistADef['lin_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_a']		= $valx2HistADef['lin_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_b']		= $valx2HistADef['lin_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add']		= $valx2HistADef['lin_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil']			= $valx2HistADef['lin_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil_add']		= $valx2HistADef['lin_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm']			= $valx2HistADef['lin_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm_add']		= $valx2HistADef['lin_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin']				= $valx2HistADef['lin_resin'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_thickness']		= $valx2HistADef['lin_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_a']			= $valx2HistADef['str_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_b']			= $valx2HistADef['str_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm']			= $valx2HistADef['str_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_a']		= $valx2HistADef['str_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_b']		= $valx2HistADef['str_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add']		= $valx2HistADef['str_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_a']			= $valx2HistADef['str_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_b']			= $valx2HistADef['str_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr']			= $valx2HistADef['str_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_a']		= $valx2HistADef['str_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_b']		= $valx2HistADef['str_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add']		= $valx2HistADef['str_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_a']			= $valx2HistADef['str_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_b']			= $valx2HistADef['str_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv']			= $valx2HistADef['str_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_a']		= $valx2HistADef['str_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_b']		= $valx2HistADef['str_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add']		= $valx2HistADef['str_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm']			= $valx2HistADef['str_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm_add']		= $valx2HistADef['str_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr']			= $valx2HistADef['str_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr_add']		= $valx2HistADef['str_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv']			= $valx2HistADef['str_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_bw']		= $valx2HistADef['str_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_jb']		= $valx2HistADef['str_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add']		= $valx2HistADef['str_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_bw']	= $valx2HistADef['str_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_jb']	= $valx2HistADef['str_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin']				= $valx2HistADef['str_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_thickness']		= $valx2HistADef['str_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_a']		= $valx2HistADef['eks_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_b']		= $valx2HistADef['eks_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil']			= $valx2HistADef['eks_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_a']	= $valx2HistADef['eks_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_b']	= $valx2HistADef['eks_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add']		= $valx2HistADef['eks_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_a']			= $valx2HistADef['eks_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_b']			= $valx2HistADef['eks_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm']			= $valx2HistADef['eks_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_a']		= $valx2HistADef['eks_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_b']		= $valx2HistADef['eks_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add']		= $valx2HistADef['eks_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil']			= $valx2HistADef['eks_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil_add']		= $valx2HistADef['eks_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm']			= $valx2HistADef['eks_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm_add']		= $valx2HistADef['eks_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin']				= $valx2HistADef['eks_resin'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_thickness']		= $valx2HistADef['eks_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['topcoat_resin']			= $valx2HistADef['topcoat_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_a']		= $valx2HistADef['str_n1_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_b']		= $valx2HistADef['str_n1_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm']		= $valx2HistADef['str_n1_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_a']	= $valx2HistADef['str_n1_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_b']	= $valx2HistADef['str_n1_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add']	= $valx2HistADef['str_n1_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_a']		= $valx2HistADef['str_n1_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_b']		= $valx2HistADef['str_n1_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr']			= $valx2HistADef['str_n1_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_a']	= $valx2HistADef['str_n1_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_b']	= $valx2HistADef['str_n1_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add']		= $valx2HistADef['str_n1_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_a']		= $valx2HistADef['str_n1_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_b']		= $valx2HistADef['str_n1_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv']			= $valx2HistADef['str_n1_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_a']	= $valx2HistADef['str_n1_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_b']	= $valx2HistADef['str_n1_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add']		= $valx2HistADef['str_n1_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm']		= $valx2HistADef['str_n1_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm_add']	= $valx2HistADef['str_n1_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr']		= $valx2HistADef['str_n1_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr_add']	= $valx2HistADef['str_n1_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv']		= $valx2HistADef['str_n1_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_bw']		= $valx2HistADef['str_n1_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_jb']		= $valx2HistADef['str_n1_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add']	= $valx2HistADef['str_n1_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_bw']	= $valx2HistADef['str_n1_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_jb']	= $valx2HistADef['str_n1_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin']			= $valx2HistADef['str_n1_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_thickness']	= $valx2HistADef['str_n1_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_a']		= $valx2HistADef['str_n2_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_b']		= $valx2HistADef['str_n2_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm']		= $valx2HistADef['str_n2_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_a']	= $valx2HistADef['str_n2_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_b']	= $valx2HistADef['str_n2_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add']	= $valx2HistADef['str_n2_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_a']		= $valx2HistADef['str_n2_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_b']		= $valx2HistADef['str_n2_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr']			= $valx2HistADef['str_n2_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_a']	= $valx2HistADef['str_n2_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_b']	= $valx2HistADef['str_n2_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add']		= $valx2HistADef['str_n2_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm']		= $valx2HistADef['str_n2_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm_add']	= $valx2HistADef['str_n2_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr']		= $valx2HistADef['str_n2_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr_add']	= $valx2HistADef['str_n2_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin']			= $valx2HistADef['str_n2_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_thickness']	= $valx2HistADef['str_n2_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['created_by']				= $valx2HistADef['created_by'];
					$ArrBqDefaultHist[$val2HistADef]['created_date']			= $valx2HistADef['created_date'];
					$ArrBqDefaultHist[$val2HistADef]['modified_by']				= $valx2HistADef['modified_by'];
					$ArrBqDefaultHist[$val2HistADef]['modified_date']			= $valx2HistADef['modified_date'];
					$ArrBqDefaultHist[$val2HistADef]['hist_by']					= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefaultHist[$val2HistADef]['hist_date']				= date('Y-m-d H:i:s');
					
					
				}
			}
			//================================================================================================================
			//================================================================================================================
			//================================================================================================================
			
			//Insert Component Header To Hist
			$qHeaderHist	= $this->db->query("SELECT * FROM so_component_header WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qHeaderHist)){
				foreach($qHeaderHist AS $val2HistA => $valx2HistA){
					$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
					$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
					$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
					$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
					$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
					$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
					$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
					$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
					$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
					$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
					$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
					$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
					$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
					$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
					$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
					$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
					$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
					$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
					$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
					$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
					$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
					$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
					$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
					$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
					$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length']			= $valx2HistA['wrap_length'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length2']		= $valx2HistA['wrap_length2'];
					$ArrBqHeaderHist[$val2HistA]['high']				= $valx2HistA['high'];
					$ArrBqHeaderHist[$val2HistA]['area2']				= $valx2HistA['area2'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
					$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
					$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
					$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
					$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
					$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
					$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
					$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
					$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
					$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
					$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
					$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
					$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
					$ArrBqHeaderHist[$val2HistA]['deleted_by']			= $valx2HistA['deleted_by'];
					$ArrBqHeaderHist[$val2HistA]['deleted_date']		= $valx2HistA['deleted_date'];
					$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
					
				}
			}

			//Component Detail
			$qDetail	= $this->db->query("SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$product."' AND a.id_milik='".$id_milik_bq."' ")->result_array();
			foreach($qDetail AS $val2 => $valx2){
				$LoopDetail++;
				$ArrBqDetail[$LoopDetail]['id_product']		= $product;
				$ArrBqDetail[$LoopDetail]['id_bq']			= $id_bq;
				$ArrBqDetail[$LoopDetail]['id_milik']		= $id_milik;
				$ArrBqDetail[$LoopDetail]['detail_name']	= $valx2['detail_name'];
				$ArrBqDetail[$LoopDetail]['acuhan']			= $valx2['acuhan'];
				$ArrBqDetail[$LoopDetail]['id_ori']			= $valx2['id_ori'];
				$ArrBqDetail[$LoopDetail]['id_ori2']		= $valx2['id_ori2'];
				$ArrBqDetail[$LoopDetail]['id_category']	= $valx2['id_category'];
				$ArrBqDetail[$LoopDetail]['nm_category']	= $valx2['nm_category'];
				$ArrBqDetail[$LoopDetail]['id_material']	= $valx2['id_material'];
				$ArrBqDetail[$LoopDetail]['nm_material']	= $valx2['nm_material'];
				$ArrBqDetail[$LoopDetail]['value']			= $valx2['value'];
				$ArrBqDetail[$LoopDetail]['thickness']		= $valx2['thickness'];
				$ArrBqDetail[$LoopDetail]['fak_pengali']	= $valx2['fak_pengali'];
				$ArrBqDetail[$LoopDetail]['bw']				= $valx2['bw'];
				$ArrBqDetail[$LoopDetail]['jumlah']			= $valx2['jumlah'];
				$ArrBqDetail[$LoopDetail]['layer']			= $valx2['layer'];
				$ArrBqDetail[$LoopDetail]['containing']		= $valx2['containing'];
				$ArrBqDetail[$LoopDetail]['total_thickness']	= $valx2['total_thickness'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / floatval($PANJANG_BEF))* (floatval($panjang) + 400);
				}
				elseif ($qHeader[0]->parent_product == 'branch joint' OR $qHeader[0]->parent_product == 'field joint' OR $qHeader[0]->parent_product == 'shop joint') {
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['material_weight'];
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / floatval($panjang)) * floatval($panjang);
				}
				else{
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['last_cost'];
				}
				$ArrBqDetail[$LoopDetail]['rev']				= $qHeader[0]->rev;
				//
				$ArrBqDetail[$LoopDetail]['area_weight']		= $valx2['area_weight'];
				$ArrBqDetail[$LoopDetail]['material_weight']	= $valx2['material_weight'];
				$ArrBqDetail[$LoopDetail]['percentage']			= $valx2['percentage'];
				$ArrBqDetail[$LoopDetail]['resin_content']		= $valx2['resin_content'];

				$ArrBqDetail[$LoopDetail]['price_mat']		= $valx2['price_mat'];
			}

			//Component Lamination
			$qDetailLam	= $this->db->query("SELECT * FROM bq_component_lamination WHERE id_product='".$product."' AND id_milik='".$id_milik_bq."' ")->result_array();
			foreach($qDetailLam AS $val2 => $valx2){
				$LoopDetailLam++;
				$ArrBqDetailLam[$LoopDetailLam]['id_product']	= $product;
				$ArrBqDetailLam[$LoopDetailLam]['id_bq']		= $id_bq;
				$ArrBqDetailLam[$LoopDetailLam]['id_milik']		= $id_milik;
				$ArrBqDetailLam[$LoopDetailLam]['detail_name']	= $valx2['detail_name'];
				$ArrBqDetailLam[$LoopDetailLam]['lapisan']		= $valx2['lapisan'];
				$ArrBqDetailLam[$LoopDetailLam]['std_glass']	= $valx2['std_glass'];
				$ArrBqDetailLam[$LoopDetailLam]['width']		= $valx2['width'];
				$ArrBqDetailLam[$LoopDetailLam]['stage']		= $valx2['stage'];
				$ArrBqDetailLam[$LoopDetailLam]['glass']		= $valx2['glass'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_1']	= $valx2['thickness_1'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_2']	= $valx2['thickness_2'];
				$ArrBqDetailLam[$LoopDetailLam]['glass_length']	= $valx2['glass_length'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_veil']	= $valx2['weight_veil'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_csm']	= $valx2['weight_csm'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_wr']	= $valx2['weight_wr'];
			}

			//Insert Component Detail To Hist
			$qDetailHist	= $this->db->query("SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailHist)){
				foreach($qDetailHist AS $val2Hist => $valx2Hist){
					$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
					$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
					$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
					$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
					$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
					$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
					$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
					$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
					$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
					$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
					$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
					$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
					$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
					$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
					$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
					$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
					$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
					$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
					$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
					$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
					$ArrBqDetailHist[$val2Hist]['rev']				= $valx2Hist['rev'];
					$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
					$ArrBqDetailHist[$val2Hist]['hist_by']			= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Plus
			$qDetailPlus	= $this->db->query("SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$product."' AND a.id_milik='".$id_milik_bq."' ")->result_array();
			foreach($qDetailPlus AS $val3 => $valx3){
				$LoopDetailPlus++;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_product']		= $product;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_bq']			= $id_bq;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_milik']		= $id_milik;
				$ArrBqDetailPlus[$LoopDetailPlus]['detail_name']	= $valx3['detail_name'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori']			= $valx3['id_ori'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori2']		= $valx3['id_ori2'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_category']	= $valx3['id_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_category']	= $valx3['nm_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_material']	= $valx3['id_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_material']	= $valx3['nm_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['containing']		= $valx3['containing'];
				$ArrBqDetailPlus[$LoopDetailPlus]['perse']			= $valx3['perse'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($PANJANG_BEF)) * (floatval($panjang) + 400);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($PANJANG_BEF)) * (floatval($panjang) + 400);
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($panjang)) * floatval($panjang);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($panjang)) * floatval($panjang);
				}
				else{
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= $valx3['last_full'];
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= $valx3['last_cost'];
				}
				$ArrBqDetailPlus[$LoopDetailPlus]['rev']			= $qHeader[0]->rev;
				$ArrBqDetailPlus[$LoopDetailPlus]['price_mat']		= $valx3['price_mat'];
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailPlusHist)){
				foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
					$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
					$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
					$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
					$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
					$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
					$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
					$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
					$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
					$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
					$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
					$ArrBqDetailPlusHist[$val3Hist]['rev']			= $valx3Hist['rev'];
					$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Add
			$qDetailAdd		= $this->db->query("SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$product."' AND a.id_milik='".$id_milik_bq."' ")->result_array();
			if(!empty($qDetailAdd)){
				foreach($qDetailAdd AS $val4 => $valx4){
					$LoopDetailAdd++;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_product']		= $product;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_bq']			= $id_bq;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_milik']			= $id_milik;
					$ArrBqDetailAdd[$LoopDetailAdd]['detail_name']		= $valx4['detail_name'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_category']		= $valx4['id_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_category']		= $valx4['nm_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_material']		= $valx4['id_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_material']		= $valx4['nm_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['containing']		= $valx4['containing'];
					$ArrBqDetailAdd[$LoopDetailAdd]['perse']			= $valx4['perse'];
					if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= (floatval($valx4['last_full']) / floatval($PANJANG_BEF)) * (floatval($panjang) + 400);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= (floatval($valx4['last_cost']) / floatval($PANJANG_BEF)) * (floatval($panjang) + 400);
					}
					elseif($qHeader[0]->parent_product == 'frp pipe'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= (floatval($valx4['last_full']) / floatval($panjang)) * floatval($panjang);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= (floatval($valx4['last_cost']) / floatval($panjang)) * floatval($panjang);
					}
					else{
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']		= $valx4['last_full'];
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']		= $valx4['last_cost'];
					}
					$ArrBqDetailAdd[$LoopDetailAdd]['rev']				= $qHeader[0]->rev;
					$ArrBqDetailAdd[$LoopDetailAdd]['price_mat']	= $valx4['price_mat'];
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailAddHist)){
				foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
					$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
					$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
					$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
					$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
					$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
					$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
					$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
					$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
					$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
					$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
					$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
					$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
					$ArrBqDetailAddHist[$val4Hist]['rev']			= $valx4Hist['rev'];
					$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
					$ArrBqDetailAddHist[$val4Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailAddHist[$val4Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Footer
			$qDetailFooter	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_product='".$product."' AND id_milik='".$id_milik_bq."' ")->result_array();
			if (count($qDetailFooter)>0)
			{
				foreach($qDetailFooter AS $val5 => $valx5){
					$LoopFooter++;
					$ArrBqFooter[$LoopFooter]['id_product']		= $product;
					$ArrBqFooter[$LoopFooter]['id_bq']			= $id_bq;
					$ArrBqFooter[$LoopFooter]['id_milik']		= $id_milik;
					$ArrBqFooter[$LoopFooter]['detail_name']	= $valx5['detail_name'];
					$ArrBqFooter[$LoopFooter]['total']			= $valx5['total'];
					$ArrBqFooter[$LoopFooter]['min']			= $valx5['min'];
					$ArrBqFooter[$LoopFooter]['max']			= $valx5['max'];
					$ArrBqFooter[$LoopFooter]['hasil']			= $valx5['hasil'];
					$ArrBqFooter[$LoopFooter]['rev']			= $qHeader[0]->rev;
				}
			}
			//Insert Component Footer To Hist
			$qDetailFooterHist		= $this->db->query("SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailFooterHist)){
				foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
					$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
					$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
					$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
					$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
					$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
					$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
					$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
					$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
					$ArrBqFooterHist[$val5Hist]['rev']			= $valx5Hist['rev'];
					$ArrBqFooterHist[$val5Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}
			

			// print_r($ArrBqHeader);
			// print_r($ArrBqDefault);
			// echo "</pre>";
			// exit;

			$UpdateBQ	= array(
				'estimasi'	=> 'Y',
				'est_by'	=> $this->session->userdata['ORI_User']['username'],
				'est_date'	=> date('Y-m-d H:i:s')
			);
			
			$ArrDetBq2	= array(
				'id_product'	=> $product
			);

			$this->db->trans_start();
				$this->db->where('id', $id_milik);
				$this->db->update('so_detail_header', $ArrDetBq2);

				//Insert Batch Histories
				// if(!empty($ArrBqHeaderHist)){
				// 	$this->db->insert_batch('hist_so_component_header', $ArrBqHeaderHist);
				// }
				// if(!empty($ArrBqDetailHist)){
				// 	$this->db->insert_batch('hist_so_component_detail', $ArrBqDetailHist);
				// }
				// if(!empty($ArrBqDetailPlusHist)){
				// 	$this->db->insert_batch('hist_so_component_detail_plus', $ArrBqDetailPlusHist);
				// }
				// if(!empty($ArrBqDetailAddHist)){
				// 	$this->db->insert_batch('hist_so_component_detail_add', $ArrBqDetailAddHist);
				// }
				// if(count($ArrBqFooterHist)>0){
				// 	$this->db->insert_batch('hist_so_component_footer', $ArrBqFooterHist);
				// }
				// if(!empty($ArrBqDefaultHist)){
				// 	$this->db->insert_batch('hist_so_component_default', $ArrBqDefaultHist);
				// }

				//Delete BQ Component
				$this->db->delete('so_component_header', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
				$this->db->delete('so_component_detail', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
				$this->db->delete('so_component_lamination', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
				$this->db->delete('so_component_detail_plus', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
				$this->db->delete('so_component_detail_add', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
				$this->db->delete('so_component_footer', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
				$this->db->delete('so_component_default', array('id_bq' => $id_bq, 'id_milik' => $id_milik));

				//Insert BQ Component
				if(!empty($ArrBqHeader)){
					$this->db->insert('so_component_header', $ArrBqHeader);
				}
				if(!empty($ArrBqDetail)){
					$this->db->insert_batch('so_component_detail', $ArrBqDetail);
				}
				if(!empty($ArrBqDetailLam)){
					$this->db->insert_batch('so_component_lamination', $ArrBqDetailLam);
				}
				if(!empty($ArrBqDetailPlus)){
					$this->db->insert_batch('so_component_detail_plus', $ArrBqDetailPlus);
				}
				if(!empty($ArrBqDetailAdd)){
					$this->db->insert_batch('so_component_detail_add', $ArrBqDetailAdd);
				}
				if(!empty($ArrBqFooter)){
					$this->db->insert_batch('so_component_footer', $ArrBqFooter);
				}
				if(!empty($ArrBqDefault)){
					$this->db->insert('so_component_default', $ArrBqDefault);
				}

				$this->db->where('id_bq', $id_bq);
				$this->db->update('so_header', $UpdateBQ);

			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'id_bqx'	=> $id_bq,
					'pesan'		=>'Estimation structure bq data success. Thanks ...',
					'status'	=> 1
				);
				history('Estimation Sebagian Structure BQ (Tarik dari Est Sebelumnya) in Final Drawing with code : '.$id_bq.' / '.$id_milik.' / '.$product);
			}
		}
		elseif($check_num < 1){
			$Arr_Kembali	= array(
				'pesan'		=>'Product berbeda dari yang sebelumnnya... ',
				'status'	=> 0
			);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function back_to_fd_est_bq(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'aju_approved' 		=> 'N',
			'aju_approved_by' 	=> $data_session['ORI_User']['username'],
			'aju_approved_date' => date('Y-m-d H:i:s'),
			'approved' 			=> 'N',
			'approved_by' 		=> $data_session['ORI_User']['username'],
			'approved_date' 	=> date('Y-m-d H:i:s')
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $Arr_Edit);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Back process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Back process success. Thanks ...',
				'status'	=> 1
			);				
			history('Proses back structure bq (final drawing) with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function ajukan_fd_parsial(){
		$id_bq = $this->uri->segment(3);

		$qBQdetailHeader 	= "SELECT 
									a.*, 
									a.series,
									b.sum_mat,
									c.id_milik AS id_milik_bq
								FROM so_detail_header a 
								LEFT JOIN so_estimasi_cost_and_mat_fast b ON a.id=b.id_milik
								LEFT JOIN so_bf_detail_header c ON a.id_milik=c.id
								WHERE a.id_bq = '".$id_bq."' AND a.id_category <> 'pipe slongsong' ORDER BY a.id_bq_header ASC";
		$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
		
		$detail_num 	= $this->db->get_where('so_detail_header', array('id_bq'=>$id_bq,'approve'=>'N'))->num_rows();
		$detail2 		= $this->db->get_where('so_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'mat'))->result_array();
		$detail_num2 	= $this->db->get_where('so_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'mat','approve'=>'N'))->num_rows();
		
		$detail3 	= $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND (category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->result_array();
		$detail_num3 = $this->db->query("SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND (approve = 'N') AND (category='baut' OR category='plate' OR category='gasket' OR category='lainnya') ")->num_rows();
		
		
		$data = array(
			'id_bq'			=> $id_bq,
			'qBQdetailRest'	=> $qBQdetailRest,
			'detail2'	=> $detail2,
			'number' => $detail_num,
			'number2' => $detail_num2,
			'number3' => $detail_num3,
			'detail3' => $detail3
		);
		
		history('View Approve Partial Estimasi Final Drawing BQ: '.$id_bq);
		
		$this->load->view('FinalDrawing/ajukan_fd_parsial', $data);
	}
	
	public function ajukan_satuan_product(){
		$data_session	= $this->session->userdata;

		$id_bq 			= $this->input->post('bq');
		$id_milik 		= $this->input->post('id_milik');
		$cutting 		= $this->input->post('cutting');
		$qtyrelease 	= str_replace(',','',$this->input->post('qtyrelease'));
		$id_bq_header	= get_name('so_detail_header','id_bq_header','id',$id_milik);

		$array1	= array(
			'approve' 			=> 'Y',
			'approve_by' 		=> $data_session['ORI_User']['username'],
			'approve_date' 	=> date('Y-m-d H:i:s')
		);

		$array2 = [];
		if($cutting == 'Y'){
			$array2	= array(
				'cutting' 		=> 'Y',
				'cutting_by' 	=> $data_session['ORI_User']['username'],
				'cutting_date' 	=> date('Y-m-d H:i:s')
			);
		}

		$Arr_Edit = array_merge($array1, $array2);

		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id_milik);
			$this->db->update('so_detail_header', $Arr_Edit);

			$this->db->query("UPDATE 
									so_detail_detail
								SET
									approve='Y', 
									approve_by='".$data_session['ORI_User']['username']."', 
									approve_date='".date('Y-m-d H:i:s')."'
								WHERE 
									id_bq_header='".$id_bq_header."'
									AND id_bq='".$id_bq."'
									AND approve = 'N'
								ORDER BY 
									id ASC 
								LIMIT $qtyrelease");
											
			check_approve($id_bq);
			check_status($id_bq_header);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Request process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Request process success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);				
			history('Proses request approve sebagian est (final drawing) with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function ajukan_all_product(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		$id_bq			= $data['id_bq'];
		$chOri			= $data['check'];
		$check 			= $chOri;
		$ArrUpdate	= array();
		
		
		$nomor = 0;
		foreach($check AS $vaxl){ $nomor++;
			$valG = explode('-', $vaxl);
			$ArrUpdate[$nomor]['id'] 			= $valG[0];
			$ArrUpdate[$nomor]['approve'] 		= 'Y';
			$ArrUpdate[$nomor]['approve_by'] 	= $data_session['ORI_User']['username'];
			$ArrUpdate[$nomor]['approve_date']	= date('Y-m-d H:i:s');
			if(!empty($data['cut_'.$valG[0]]) AND $data['cut_'.$valG[0]] == 'Y'){
				$ArrUpdate[$nomor]['cutting'] 		= 'Y';
				$ArrUpdate[$nomor]['cutting_by'] 	= $data_session['ORI_User']['username'];
				$ArrUpdate[$nomor]['cutting_date']	= date('Y-m-d H:i:s');
			}
			
			if($data['berat_'.$valG[0]] <= 0 OR $data['berat_'.$valG[0]] == ''){
				$Arr_Data	= array(
					'pesan'		=>'Salah satu product berat materialnya kosong. Check again ...',
					'status'	=> 0,
					'id_bq'		=> $id_bq
				);
				echo json_encode($Arr_Data);
				return;
			}
			if($data['qtyrelease_'.$valG[0]] <= 0 OR $data['qtyrelease_'.$valG[0]] == ''){
				$Arr_Data	= array(
					'pesan'		=>'Salah satu qty release kosong. Check again ...',
					'status'	=> 0,
					'id_bq'		=> $id_bq
				);
				echo json_encode($Arr_Data);
				return;
			}
		}
		// echo 'hayo';
		// exit;
		
		$this->db->trans_start();
			$this->db->update_batch('so_detail_header', $ArrUpdate, 'id');

			foreach($chOri AS $vaxl){
				$valG = explode('-', $vaxl);
				$qtyrelease 	= $data['qtyrelease_'.$valG[0]];
				$id_bq_header	= get_name('so_detail_header','id_bq_header','id',$valG[0]);
				$this->db->query("UPDATE 
									so_detail_detail
								SET
									approve='Y', 
									approve_by='".$data_session['ORI_User']['username']."', 
									approve_date='".date('Y-m-d H:i:s')."'
								WHERE 
									id_bq_header='".$id_bq_header."'
									AND id_bq='".$id_bq."'
									AND approve = 'N'
								ORDER BY 
									id ASC 
								LIMIT $qtyrelease");
			}

			check_approve($id_bq);
			check_status_all($id_bq,$chOri);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Request process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Request process success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);				
			history('Proses request approve sebagian checklist est (final drawing) with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function ajukan_all_material(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		$id_bq			= $data['id_bq'];
		$chOri			= $data['check'];
		$check 			= $chOri;
		$ArrUpdate	= array();
		
		
		$nomor = 0;
		foreach($check AS $vaxl){ $nomor++;
			$ArrUpdate[$nomor]['id'] 			= $vaxl;
			$ArrUpdate[$nomor]['approve'] 		= 'Y';
			$ArrUpdate[$nomor]['approve_by'] 	= $data_session['ORI_User']['username'];
			$ArrUpdate[$nomor]['approve_date']	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrUpdate);
		// exit;
		
		$this->db->trans_start();
			$this->db->update_batch('so_acc_and_mat', $ArrUpdate, 'id');
			check_approve($id_bq);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Request process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Request process success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);				
			history('Proses request approve sebagian checklist material (final drawing) with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function ajukan_all_acc(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		$id_bq			= $data['id_bq'];
		$chOri			= $data['check2'];
		$check 			= $chOri;
		$ArrUpdate	= array();
		
		
		$nomor = 0;
		foreach($check AS $vaxl){ $nomor++;
			$ArrUpdate[$nomor]['id'] 			= $vaxl;
			$ArrUpdate[$nomor]['approve'] 		= 'Y';
			$ArrUpdate[$nomor]['approve_by'] 	= $data_session['ORI_User']['username'];
			$ArrUpdate[$nomor]['approve_date']	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrUpdate);
		// exit;
		
		$this->db->trans_start();
			$this->db->update_batch('so_acc_and_mat', $ArrUpdate, 'id');
			check_approve($id_bq);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Request process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Request process success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);				
			history('Proses request approve sebagian checklist acc (final drawing) with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function ajukan_satuan_material(){
		$id_bq 			= $this->uri->segment(3);
		$id_milik 		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'approve' 			=> 'Y',
			'approve_by' 		=> $data_session['ORI_User']['username'],
			'approve_date' 	=> date('Y-m-d H:i:s')
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id_milik);
			$this->db->update('so_acc_and_mat', $Arr_Edit);
			check_approve($id_bq);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Request process failed. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Request process success. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);				
			history('Proses request approve sebagian material (final drawing) with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	//SERVER SIDE
	public function get_data_json_fd_est(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_fd_est(
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
			$nestedData[]	= "<div align='left'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['rev']))."</span></div>";

				$warna = Color_status($row['sts_ipp']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					$detX	= "";
					$app	= "";
					$bcBQ	= "";
					$app_new	= "";
					$close_parsial	= "";
					
					if($row['estimasi']=='Y'){
						$detX	= "&nbsp;<button type='button' class='btn btn-sm btn-success view_data' title='View Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					}

					$check_produksi = $this->db->get_where('production_header', array('no_ipp'=>str_replace('BQ-','',$row['id_bq'])))->result();
					
					if($Arr_Akses['update']=='1'){
						if($row['approved_est'] == 'N' AND $row['approved'] == 'Y'){
							if($row['sts_ipp'] == 'WAITING FINAL DRAWING' OR $row['sts_ipp'] == 'PARTIAL PROCESS'){
								$updX	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit_est' title='Estimation BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-edit'></i></button>";
								$bcBQ	= "&nbsp;<button type='button' class='btn btn-sm btn-danger back_to_fd_est_bq' title='Back Structure BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-reply'></i></button>";
								$app_new	= "&nbsp;<button type='button' class='btn btn-sm btn-info ajukan_final_drawing' title='Ajukan Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
								if($row['sts_ipp'] == 'PARTIAL PROCESS' AND !empty($check_produksi)){
								$close_parsial= "&nbsp;<button type='button' class='btn btn-sm close_parsial' style='background-color:coral; color:white;' title='Close Parsial Status' data-id_bq='".$row['id_bq']."'><i class='fa fa-gavel'></i></button>";
								}
							}
						}
					}
					

			$nestedData[]	= "<div align='left'>
									<button type='button' class='btn btn-sm btn-warning detail' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$priX."
									".$delX."
									".$detX."
									".$updX."
									".$bcBQ."
									".$app."
									".$app_new."
									".$close_parsial."
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

	public function query_data_fd_est($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.project,
				b.status AS sts_ipp
			FROM
				so_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 
				a.approved = 'Y'
				AND b.status <> 'FINISH'
				AND b.status <> 'WAITING PRODUCTION'
				AND b.status <> 'PROCESS PRODUCTION'
				AND b.sts_hide = 'N'
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'b.nm_customer',
			3 => '.project'
		);

		$sql .= " ORDER BY b.status DESC, a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function update_resin(){
		$data  	= $this->input->post();
		$resin 	= $this->uri->segment(3);
		$tanda 	= $this->uri->segment(4);
		$beda 	= $data['pembeda'];
		$id_bq 	= $data['id_bq'];

		$qListResin = "SELECT id_material, nm_material FROM raw_materials WHERE id_material='".$resin."' LIMIT 1 ";
		$dataResin	= $this->db->query($qListResin)->result();
		$resinNew	= $dataResin[0]->nm_material;

		if($tanda == 'liner'){
			$layer = "(detail_name = 'LINER THIKNESS / CB')";
			$table = "so_component_detail";
		}
		if($tanda == 'str'){
			$layer = "(detail_name = 'STRUKTUR THICKNESS' OR detail_name = 'STRUKTUR NECK 1' OR detail_name = 'STRUKTUR NECK 2')";
			$table = "so_component_detail";
		}
		if($tanda == 'eks'){
			$layer = "(detail_name = 'EXTERNAL LAYER THICKNESS')";
			$table = "so_component_detail";
		}
		if($tanda == 'tc'){
			$layer = "(detail_name = 'TOPCOAT')";
			$table = "so_component_detail_plus";
		}

		if($tanda == 'liner' OR $tanda == 'str' OR $tanda == 'eks'){
			$sqlUpdate 	= "SELECT * FROM ".$table." WHERE id_bq='".$id_bq."' AND id_category = 'TYP-0001' AND ".$layer." ";
		}
		if($tanda == 'tc'){
			$sqlUpdate 	= "SELECT * FROM ".$table." WHERE id_bq='".$id_bq."' AND id_category = 'TYP-0001' AND ".$layer." ";
		}

		$restUpdate = $this->db->query($sqlUpdate)->result_array();

		$ArrUpdate 	= array();
		foreach($restUpdate AS $val => $valx){
			$ArrUpdate[$val]['id_detail'] 	= $valx['id_detail'];
			$ArrUpdate[$val]['id_material'] = $resin;
			$ArrUpdate[$val]['nm_material'] = $resinNew;
			$ArrUpdate[$val]['price_mat'] 	= get_price_ref($resin);
		}

		
		//Update Joint
		$sqlv = "";
		if($tanda == 'liner'){
			$sqlv_tc = "SELECT id_detail AS id_detail FROM help_update_joint_tc_so WHERE id_bq='".$id_bq."' AND (nm_category='RESIN INSIDE' OR nm_category='RESIN CARBOSIL')";
			$ListBQipp		= $this->db->query($sqlv_tc)->result_array();

			$dtListArray = array();
			foreach($ListBQipp AS $val => $valx){
				$dtListArray[$val] = $valx['id_detail'];
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";

			$sqlv = "SELECT id_detail FROM so_component_detail WHERE detail_name = 'RESIN AND ADD' AND id_category = 'TYP-0001' AND id_bq='".$id_bq."' AND id_detail IN ".$dtImplode." ";
		}
		if($tanda == 'str'){
			$sqlv_tc = "SELECT id_detail AS id_detail FROM help_update_joint_tc_so WHERE id_bq='".$id_bq."' AND (nm_category='RESIN OUTSIDE' OR nm_category='RESIN')";
			$ListBQipp		= $this->db->query($sqlv_tc)->result_array();

			$dtListArray = array();
			foreach($ListBQipp AS $val => $valx){
				$dtListArray[$val] = $valx['id_detail'];
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";

			$sqlv = "SELECT id_detail FROM so_component_detail WHERE detail_name = 'RESIN AND ADD' AND id_category = 'TYP-0001' AND id_bq='".$id_bq."' AND id_detail IN ".$dtImplode." ";
		}
		if($tanda == 'tc'){
			$sqlv = "SELECT id_detail AS id_detail FROM help_update_joint_tc_so WHERE nm_category='RESIN TOPCOAT' AND id_bq='".$id_bq."' GROUP BY id_milik";
		}
		// echo $sqlv; exit;
		$ArrUpdate2 	= array();
		if($tanda == 'str' OR $tanda == 'tc' OR $tanda == 'liner'){
			$restUpdateJoint = $this->db->query($sqlv)->result_array();
			foreach($restUpdateJoint AS $val => $valx){
				$ArrUpdate2[$val]['id_detail'] 		= $valx['id_detail'];
				$ArrUpdate2[$val]['id_material'] 	= $resin;
				$ArrUpdate2[$val]['nm_material'] 	= $resinNew;
				$ArrUpdate2[$val]['price_mat'] 		= get_price_ref($resin);
			}
		}

		// print_r($ArrUpdate);
		// print_r($ArrUpdate2);
		// exit;

		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch($table, $ArrUpdate, 'id_detail');
			}
			if(!empty($ArrUpdate2)){
				$this->db->update_batch("so_component_detail", $ArrUpdate2, 'id_detail');
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update failed. Please try again later ...',
				'status'	=> 0,
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $beda
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update success. Thanks ...',
				'status'	=> 1,
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $beda
			);
			history("Update all resin layer (final drawing) ".$tanda." / ".$id_bq." / ".$resin);
		}
		echo json_encode($Arr_Data);
	}
	
	//PRODUKSI
	public function produksi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Final Drawing in Production',
			'action'		=> 'produksi',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data final drawing status produksi');
		$this->load->view('FinalDrawing/produksi',$data);
	}
	
	public function get_data_json_fd_est_produksi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_fd_est_produksi(
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
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['rev']))."</span></div>";

				$warna = Color_status($row['sts_ipp']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					$detX	= "";
					$app	= "";
					$bcBQ	= "";
					$app_new	= "";
					
					if($row['estimasi']=='Y'){
						$detX	= "&nbsp;<button type='button' class='btn btn-sm btn-success view_data' title='View Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					}
					
					if($Arr_Akses['update']=='1'){
						if($row['approved_est'] == 'N' AND $row['approved'] == 'Y'){
							if($row['sts_ipp'] == 'WAITING FINAL DRAWING' OR $row['sts_ipp'] == 'PARTIAL PROCESS'){
								$updX	= "&nbsp;<button type='button' class='btn btn-sm btn-primary edit_est' title='Estimation BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-edit'></i></button>";
								$bcBQ	= "&nbsp;<button type='button' class='btn btn-sm btn-danger back_to_fd_est_bq' title='Back Structure BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-reply'></i></button>";
								$app_new	= "&nbsp;<button type='button' class='btn btn-sm btn-info ajukan_final_drawing' title='Ajukan Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
							}
						}
					}
					

			$nestedData[]	= "<div align='left' style='padding-left: 20px;'>
									<button type='button' class='btn btn-sm btn-warning detail' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$priX."
									".$delX."
									".$detX."
									".$updX."
									".$bcBQ."
									".$app."
									".$app_new."
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

	public function query_data_fd_est_produksi($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.status AS sts_ipp
			FROM
				so_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 
				a.approved = 'Y'
				AND (
					b.status = 'FINISH'
					OR b.status = 'WAITING PRODUCTION'
					OR b.status = 'PROCESS PRODUCTION'
				)
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'nm_customer',
			3 => 'order_type'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function close_parsial(){
		$id_bq 			= $this->uri->segment(3);
		$no_ipp 		= str_replace('BQ-','',$id_bq);
		$data_session	= $this->session->userdata;
		
		$Arr_Edit2	= array(
			'status' => 'PROCESS PRODUCTION',
			'quo_reason' => '',
			'quo_by' => $data_session['ORI_User']['username'],
			'quo_date' => date('Y-m-d H:i:s'),
			'mp' => 'Y',
			'mp_by' => $data_session['ORI_User']['username'],
			'mp_date' => date('Y-m-d H:i:s')
		);
		$Arr_Edit	= array(
			'approved_est' 		=> 'Y',
			'approved_est_by' 	=> $data_session['ORI_User']['username'],
			'approved_est_date' => date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $Arr_Edit);

			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $Arr_Edit2);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Back process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Back process success. Thanks ...',
				'status'	=> 1
			);				
			history('Close parsial status : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	
}