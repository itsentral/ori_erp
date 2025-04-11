<?php
class Project_finish_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function index_cost_control(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/cost_control';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT create_by, create_date FROM group_project ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		$data_uri = $this->uri->segment(3);
		$data = array(
			'title'			=> 'Indeks Of Cost Control Finish',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'data_uri'		=> $data_uri
		);
		history('View Cost Control Project Finish');
		$this->load->view('Cost_control/cost_control',$data);
	}
	
	public function view_dt_cost_control(){
		$id_bq 		= $this->uri->segment(3);
		$tanda_cost = $this->uri->segment(4);

		$qSupplier	= "	SELECT * FROM production_header WHERE no_ipp = '".str_replace('BQ-','',$id_bq)."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$data_session	= $this->session->userdata;

		$HelpDet1 	= "spec_bq";
		$HelpDet21 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet21 	= "so_detail_header";
			$HelpDet1 	= "spec_bq2";
		}

		$result		= $this->db->get_where($HelpDet21,array('id_bq'=>$id_bq,'id_category <>'=>'pipe slongsong'))->result_array();
		
		$data = array(
			'id_bq'			=> $id_bq,
			'tanda_cost'	=> $tanda_cost,
			'result'	=> $result,
			'get_dist' => $HelpDet1
		);
		
		$this->load->view('Cost_control/modal_view_dt',$data);
	}
	
	public function view_detail_cost_control(){
		$id_product 	= $this->uri->segment(3);
		$id_milik 		= $this->uri->segment(4); 
		$no_ipp 		= $this->uri->segment(5);
		$qty 			= $this->uri->segment(6);

		$qSupplier	= "	SELECT * FROM production_header WHERE no_ipp = '".$no_ipp."' ";
		$row		= $this->db->query($qSupplier)->result_array(); 

		$HelpDet 	= "bq_component_header";
		$HelpDet2 	= "banding_mat";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_component_header";
			$HelpDet2 	= "banding_so_mat";
		}

		
		$qHeader		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."'";
		$qDetail1		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		$qDetail2		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		$qDetail3		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		$qDetail4		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='TOPCOAT' GROUP BY a.id_material";
		
		$detailResin1	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin3	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		// echo $detailResin2; 
		$restHeader		= $this->db->query($qHeader)->result_array();
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$restDetail4	= $this->db->query($qDetail4)->result_array();
		$restResin1		= $this->db->query($detailResin1)->result_array();
		$restResin2		= $this->db->query($detailResin2)->result_array();
		$restResin3		= $this->db->query($detailResin3)->result_array();
		
		//tambahan flange mould /slongsong
		$qDetail2N1		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		$qDetail2N2		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
		
		$detailResin2N1	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2N2	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		
		$restDetail2N1	= $this->db->query($qDetail2N1)->result_array();
		$restDetail2N2	= $this->db->query($qDetail2N2)->result_array();
		
		$restResin2N1	= $this->db->query($detailResin2N1)->result_array();
		$restResin2N2	= $this->db->query($detailResin2N2)->result_array();
		
		$data = array(
			'restHeader'	=> $restHeader,
			'restDetail1'	=> $restDetail1,
			'restDetail2'	=> $restDetail2,
			'restDetail3'	=> $restDetail3,
			'restDetail4'	=> $restDetail4,
			'restResin1'	=> $restResin1,
			'restResin2'	=> $restResin2,
			'restResin3'	=> $restResin3,
			'restDetail2N1'	=> $restDetail2N1,
			'restDetail2N2'	=> $restDetail2N2,
			'restResin2N1'	=> $restResin2N1,
			'restResin2N2' 	=> $restResin2N2
		);
		
		$this->load->view('Cost_control/modal_detail_cost',$data);
	}
	
	public function view_total_material_cost_control(){
		$id_bq = $this->uri->segment(3);

		$qSupplier	= "	SELECT * FROM production_header WHERE no_ipp = '".str_replace('BQ-','', $id_bq)."' ";
		$row		= $this->db->query($qSupplier)->result_array();
		// echo $qSupplier;
		
		$HelpDet2 	= "hasil_material_project";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet2 	= "hasil_so_material_project";
		}

		$data_session	= $this->session->userdata;

		$this->db->delete('hasil_material_project_table', array('created' => $data_session['ORI_User']['username']));
					
		$sqlUpdate = "
			INSERT INTO 
				hasil_material_project_table 
					(
						id_bq, 
						nm_category, 
						id_material, 
						nm_material, 
						est_material, 
						est_harga, 
						real_material, 
						real_harga, 
						created
					) 
				SELECT
					a.id_bq,
					a.nm_category,
					a.id_material,
					a.nm_material,
					a.est_material,
					a.est_harga,
					a.real_material,
					a.real_harga,
					'".$data_session['ORI_User']['username']."'
				FROM
					".$HelpDet2." a
				WHERE 
					a.id_bq='".$id_bq."' ";

		$this->db->query($sqlUpdate);

		$sql 	= "SELECT * FROM hasil_material_project_table WHERE id_bq='".$id_bq."' AND created='".$data_session['ORI_User']['username']."' ORDER BY nm_category ASC ";

		$result		= $this->db->query($sql)->result_array();
		
		$data = array(
			'id_bq' => $id_bq,
			'result' => $result
		);

		$this->load->view('Cost_control/modal_total_material', $data);
	}
	
	public function print_total_material(){
		$id_bq	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/controllers/plusPrintPrice.php";
		
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Total Material Project BQ '.$id_bq); 
		
		PrintTotalMaterial($Nama_Beda, $koneksi, $printby, $id_bq);
	}
	
	public function print_finish_per_product(){
		$id_product	= $this->uri->segment(3);
		$id_milik	= $this->uri->segment(4);
		$id_bq	= $this->uri->segment(5);
		$qty	= $this->uri->segment(6);
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/controllers/plusPrintPrice.php";
		
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Total Per Product Project '.$id_bq.' / '.$id_product); 
		
		printCostControl($Nama_Beda, $id_product, $koneksi, $printby, $id_milik, $id_bq, $qty);
	}
	
	public function print_hasil_finish_project(){ 
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/controllers/plusPrintPrice.php";
		
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		history('Print Hasil Perbandingan Project'); 
		
		PrintHasilProject($Nama_Beda, $koneksi, $printby);
	}
	
	function insert_select_finish(){
		$sql 	= "SELECT
						a.* 
					FROM
						group_cost_project_finish_fast a
						LEFT JOIN group_cost_project_finish_fast_table b ON a.no_ipp = b.no_ipp 
					WHERE
						b.no_ipp IS NULL 
					ORDER BY
						a.no_ipp ASC";
		$rest 	= $this->db->query($sql)->result_array();
		
		$ArrInsert = array();
		foreach($rest AS $val => $valx){
			$ArrInsert[$val]['no_ipp'] 			= $valx['no_ipp'];
			$ArrInsert[$val]['estimasi'] 		= $valx['estimasi'];
			$ArrInsert[$val]['rev'] 			= $valx['rev'];
			$ArrInsert[$val]['order_type'] 		= $valx['order_type'];
			$ArrInsert[$val]['nm_customer'] 	= $valx['nm_customer'];
			$ArrInsert[$val]['sts_ipp'] 		= $valx['sts_ipp'];
			
			$ArrInsert[$val]['est_mat'] 		= (!empty($valx['est_mat']))?$valx['est_mat']:0;
			$ArrInsert[$val]['est_harga'] 		= (!empty($valx['est_harga']))?$valx['est_harga']:0;
			$ArrInsert[$val]['real_material'] 	= (!empty($valx['real_material']))?$valx['real_material']:0;
			$ArrInsert[$val]['real_harga'] 		= (!empty($valx['real_harga']))?$valx['real_harga']:0;
			$ArrInsert[$val]['persenx'] 		= (!empty($valx['persenx']))?$valx['persenx']:0;
			
			$ArrInsert[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrInsert[$val]['create_date'] 	= date('Y-m-d H:i:s');
		}
		
		$this->db->trans_start();
			$this->db->insert_batch('group_cost_project_finish_fast_table',$ArrInsert);
			// $this->db->truncate('group_cost_project_finish_fast_table');
			// $sqlUpdate = "
				// INSERT INTO 
						// group_cost_project_finish_fast_table 
						// ( 
							// no_ipp, 
							// estimasi, 
							// rev, 
							// order_type, 
							// nm_customer, 
							// sts_ipp, 
							// est_mat, 
							// est_harga, 
							// real_material, 
							// real_harga, 
							// persenx, 
							// create_by, 
							// create_date
						// ) 
						// SELECT
							// a.no_ipp,
							// a.estimasi,
							// a.rev,
							// a.order_type,
							// a.nm_customer,
							// a.sts_ipp,
							// IF(a.est_mat IS NULL, 0, a.est_mat) AS est_mat,
							// IF(a.est_mat IS NULL, 0, a.est_harga) AS est_harga,
							// IF(a.est_mat IS NULL, 0, a.real_material) AS real_material,
							// IF(a.est_mat IS NULL, 0, a.real_harga) AS real_harga,
							// IF(a.est_mat IS NULL, 0, a.persenx) AS persenx,
							// '".$this->session->userdata['ORI_User']['username']."',
							// '".date('Y-m-d H:i:s')."'
						// FROM
							// group_cost_project_finish_fast a";
			
			// $this->db->query($sqlUpdate);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);				
			history('Success insert select group cost project finish');
		}
		echo json_encode($Arr_Data);
		
	}

	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	public function get_data_json_cost_control(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/cost_control';
		$uri_code	= $this->uri->segment(3);
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_cost_control(
			$requestData['status'],
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
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$realend = (!empty($row['real_end_produksi']))?date('d-m-Y', strtotime($row['real_end_produksi'])):'-';
			$nestedData[]	= "<div align='center'>".$realend."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = 'BQ-".$row['no_ipp']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_mat'], 3)." Kg</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_harga'], 2)."</div>";
			
			if($uri_code == 'cost_control'){
				$nestedData[]	= "<div align='right'>".number_format($row['real_material'], 3)." Kg</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['real_harga'], 2)."</div>";
			}
			
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#ce9021'>".$row['rev']."</span></div>";
				
				if($row['estimasi'] == 'Y' ){
					if($row['sts_ipp'] == 'FINISH'){
						if($row['persenx'] <= 100){
							$status	= "FINISH ".number_format($row['persenx'])." %";
							$class  = '#30b305'; 
						} 
						if($row['persenx'] > 100){ 
							$status	= "OVER BUDGET ".number_format($row['persenx'])." %";
							$class  = '#b30bae';
						}
					}
				}
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$status."</span></div>";
					$priX	= "";
					$excel	= "";
					$updXCost	= "";
					$ApprvX	= "";
					$ApprvX2	= "";
					$viewX	= "";
					
					if($row['estimasi']=='Y'){
						$viewX	= "<button class='btn btn-sm btn-primary view_data' title='View Data' data-id_bq='BQ-".$row['no_ipp']."' data-cost_control='cost_control'><i class='fa fa-eye'></i></button>";
					}
					
					//ditabambahkan ini ya AND $Check == '0'
					if($row['estimasi'] == 'Y' AND $row['sts_ipp'] == 'WAITING EST PRICE PROJECT'){
						$ApprvX	= "&nbsp;<button class='btn btn-sm btn-success' id='ApproveDT' title='Approve Project Price' data-id_bq='BQ-".$row['no_ipp']."'><i class='fa fa-check'></i></button>";
						$ApprvX2	= "&nbsp;<a a href='".base_url('cost_control/priceProcessCost/BQ-'.$row['no_ipp'])."' class='btn btn-sm btn-danger'  title='Approve Project Price' ><i class='fa fa-close'></i></a>";
					}
					
					if($Arr_Akses['download']=='1'){
						$excel	= "&nbsp;<a href='".base_url('cost_control/excel_report_product/PRO-'.$row['no_ipp'])."' target='_blank' class='btn btn-sm btn-info' title='Download Excel ' data-role='qtip'><i class='fa fa-file-excel-o'></i></a>";
					}
					
					if($uri_code == 'cost_control'){
						$updXCost	= "&nbsp;<button class='btn btn-sm btn-warning view_total_cost' title='Total All Material' data-id_bq='BQ-".$row['no_ipp']."'><i class='fa fa-money'></i></button>";
					}
			
			$nestedData[]	= "<div align='center'>
									".$priX."
									".$viewX."
									".$ApprvX."
									".$ApprvX2."
									".$updXCost."
									".$excel."
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

	public function query_data_json_cost_control($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_wait_est = "";
		if($status == 'FINISH'){
			$where_wait_est = " AND (a.persenx <= 100 AND a.persenx >= 90) ";
		}
		
		if($status == 'FINISH 2'){
			$where_wait_est = " AND a.persenx < 90 ";
		}
		
		if($status == 'OVER BUDGET'){ 
			$where_wait_est = " AND a.persenx > 100 "; 
		} 
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.real_end_produksi,
				c.so_number
			FROM
				group_project a
				LEFT JOIN production_header b ON a.no_ipp = b.no_ipp
				LEFT JOIN so_number c ON a.no_ipp = REPLACE(c.id_bq,'BQ-',''),
				(SELECT @row:=0) r
		    WHERE 
				1=1
				AND sts_ipp = 'FINISH'
				".$where_wait_est."
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'real_end_produksi',
			3 => 'nm_customer'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}
