<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budget_so extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('tanki_model');
		$this->tanki = $this->load->database("tanki",TRUE);

		// Your own constructor code
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
		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT create_by, create_date FROM budget_so_new ORDER BY create_date DESC LIMIT 1";
		$ListIPP1 			= $this->db->query("SELECT no_ipp FROM billing_so ORDER BY no_ipp ASC")->result_array();
		$ListIPP2 			= $this->db->query("SELECT no_ipp FROM planning_tanki ORDER BY no_ipp ASC")->result_array();
		$ListIPP 			= array_merge($ListIPP1, $ListIPP2);
		// $ListIPP             = $this->db->query("SELECT * FROM so_header ORDER BY no_ipp ASC")->result_array();
		$restgetBy			= $this->db->query($getBy)->result_array();
		// echo '<pre>';
		// print_r(get_CountMaterialDealSOTotal());
		// exit;
		$data = array(
			'title'			=> 'Indeks Of Budget Sales Order',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'ListIPP'		=> $ListIPP
		);
		history('View Data Budget Project');
		$this->load->view('Budget_so/index',$data);
	}
	
	public function getDataJSONQuo(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONQuo(
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
			
			$tanda = substr($row['id_bq'],0,4);

			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_mat'],3)." Kg</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_cost'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['direct_labour']+$row['indirect_labour']+$row['machine']+$row['mould_mandrill']+$row['consumable'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['depresiasi_foh']+$row['consumable_foh']+$row['gaji_non_produksi']+$row['biaya_admin']+$row['biaya_bulanan'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['profit'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['allowance'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['packing'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['enggenering'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['truck_export']+$row['truck_lokal'],2)."</div>";

					
					$view_data	= "&nbsp;<button type='button' class='btn btn-sm btn-warning ViewDT' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					$view_so	= "&nbsp;<button type='button' class='btn btn-sm btn-success ViewSO' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					$excel_deal	= "&nbsp;<button type='button' class='btn btn-sm btn-info download_excel' title='Download Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-file-excel-o'></i></button>";
					$excel_est	= "&nbsp;<button type='button' class='btn btn-sm btn-default download_excel_est' title='Download Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-file-excel-o'></i></button>";
					$excel_sum	= "&nbsp;<a href='".base_url('report_costing/excel_report_costing/'.$row['id_bq'])."' class='btn btn-sm btn-primary' title='Summary Costing'><i class='fa fa-file-excel-o'></i></a>";
					
					// if($row['sts_ipp'] == 'WAITING SALES ORDER'){
						// $ApprvX	= "&nbsp;<button class='btn btn-sm btn-success' id='ApproveDT' title='Approve To Final Drawing' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
					// }

					if($tanda == 'IPPT'){
						$view_data	= "";
						$view_so	= "";
						$excel_deal	= "&nbsp;<button type='button' class='btn btn-sm btn-info download_excel_tanki' title='Download Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-file-excel-o'></i></button>";
						$excel_est	= "";
						$excel_sum	= "";
					}

					// <button class='btn btn-sm btn-primary' id='detailBQ'  title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
			$nestedData[]	= "<div align='left'>
									".$view_data."
									".$view_so."
									".$excel_deal."
									".$excel_est."
									".$excel_sum."
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

	public function queryDataJSONQuo($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				budget_so_new a
		    WHERE 1=1
				AND (
				a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modalViewSo(){
		$this->load->view('Budget_so/modalViewSo');
	}
	
	public function modalViewDetail(){
		$this->load->view('Budget_so/modalViewDetail');
	}

	public function modalDetailGroup(){
		$id_milik 	= $this->uri->segment(3);
		$id_bq 		= $this->uri->segment(4); 	
		$qty 		= $this->uri->segment(5); 
		// echo $id_bq;
		$qHeader	= "SELECT * FROM bq_component_header WHERE id_milik='".$id_milik."' AND id_bq='".$id_bq."'";
		$restHeader	= $this->db->query($qHeader)->result_array();

		$qDet		= "SELECT * FROM estimasi_total_component WHERE id_milik='".$id_milik."' AND id_bq='".$id_bq."'";
		$restDet	= $this->db->query($qDet)->result_array();

		$data = array(
			'restHeader' => $restHeader,
			'detail' => $restDet,
			'qty' => $qty
		);

		$this->load->view('Budget_so/modalDetailGroup', $data);
	}
	
	function insert_select_budget_so(){ 
		$data = $this->input->post();
		$where2 = "";
		if(!empty($data['no_ipp_filter'])){
			$ListIPP = $data['no_ipp_filter'];
			$dtListArray = array();
			foreach($ListIPP AS $val => $valx){
				$dtListArray[$val] = $valx;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			// echo $dtImplode;
			$where2 = " WHERE a.id_bq IN ".$dtImplode."";
		}

		history('Try update budget SO');
		$sql_ = "SELECT a.id_bq FROM budget_so_1 a ".$where2."";
		// echo $sql_; exit;
		$sqlCheck = $this->db->query($sql_)->result_array();
		$ArrBudget3 = array();
		foreach($sqlCheck AS $val => $valx){
			$ArrBudget3[$val]['id_bq'] = $valx['id_bq'];
			$ArrBudget3[$val]['profit'] = Profit($valx['id_bq']);
			$ArrBudget3[$val]['allowance'] = Allowance($valx['id_bq']);
		}
		
		// print_r($ArrBudget3);
		// exit;
		$this->db->trans_start();
			$this->db->truncate('budget_so_3');
			$this->db->insert_batch('budget_so_3',$ArrBudget3);
	
			$this->db->truncate('budget_so');
			//Check sudah input		
			$sqlUpdate = "
				INSERT INTO budget_so ( id_bq, 
										id_customer, 
										nm_customer, 
										project, 
										est_mat, 
										est_cost, 
										direct_labour, 
										indirect_labour, 
										machine, 
										mould_mandrill, 
										consumable, 
										depresiasi_foh, 
										consumable_foh, 
										gaji_non_produksi, 
										biaya_admin, 
										biaya_bulanan,
										profit,
										allowance,
										packing,													
										enggenering,
										truck_export,
										truck_lokal,
										create_by,
										create_date ) 
				SELECT
					a.id_bq, 
					(SELECT id_customer FROM production WHERE no_ipp=REPLACE(a.id_bq, 'BQ-', '')) AS id_customer, 
					(SELECT nm_customer FROM production WHERE no_ipp=REPLACE(a.id_bq, 'BQ-', '')) AS nm_customer, 
					(SELECT project FROM production WHERE no_ipp=REPLACE(a.id_bq, 'BQ-', '')) AS project, 
					a.sum_mat, 
					a.est_harga,
					a.direct_labour, 
					a.indirect_labour, 
					a.machine, 
					a.mould_mandrill,
					a.consumable, 
					a.foh_depresiasi, 
					a.foh_consumable, 
					a.biaya_gaji_non_produksi, 
					a.biaya_non_produksi, 
					a.biaya_rutin_bulanan,
					c.profit,
					c.allowance,
					b.packing,													
					b.engine_,
					b.export,
					b.lokal,
					'".$this->session->userdata['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
				FROM
					budget_so_1 a 
						LEFT JOIN budget_so_2 b ON a.id_bq=b.id_bq
						LEFT JOIN budget_so_3 c ON a.id_bq=c.id_bq ".$where2." ";
			
			
			$this->db->query($sqlUpdate);
			history('Success insert budget so');
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
			history('Success insert budget so');
		}
		echo json_encode($Arr_Data);
	}
	
	function insert_select_budget_so2(){ 
		$data = $this->input->post();
		$where2 = "";
		if(!empty($data['no_ipp_filter'])){
			$ListIPP = $data['no_ipp_filter'];
			$dtListArray = array();
			foreach($ListIPP AS $val => $valx){
				$dtListArray[$val] = $valx;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			// echo $dtImplode;
			$where2 = " WHERE a.id_bq IN ".$dtImplode."";
		}

		history('Try update budget SO');
		
		$sql_ 		= "SELECT a.* FROM budget_so_1 a ".$where2."";
		$sqlCheck 	= $this->db->query($sql_)->result_array();
	
		$ArrBudget3 = array();
		foreach($sqlCheck AS $val => $valx){
			
			$get_acc_mat = $this->db->select('SUM(qty) AS qty')->get_where('so_bf_acc_and_mat', array('id_bq'=>$valx['id_bq'], 'category'=>'mat'))->result();
			$sum_mat = (!empty($get_acc_mat))?$get_acc_mat[0]->qty : 0;
			
			$get_acc_mat_price = $this->db->select('SUM(price_total) AS price_total')->get_where('cost_project_detail', array('id_bq'=>$valx['id_bq'], 'category'=>'aksesoris'))->result();
			$sum_mat_price = (!empty($get_acc_mat_price))?$get_acc_mat_price[0]->price_total : 0;
			
			$get_acc_mat_price_acc = $this->db
											->select('SUM(price_total) AS price_total')
											->from('cost_project_detail')
											->where("id_bq = '".$valx['id_bq']."' AND (category = 'baut' OR category = 'gasket' OR category = 'plate' OR category = 'lainnya') ")
											->get()
											->result();
			$sum_mat_price_acc = (!empty($get_acc_mat_price_acc))?$get_acc_mat_price_acc[0]->price_total : 0;
			
			
			$ArrBudget3[$val]['id_bq'] 				= $valx['id_bq'];
			$ArrBudget3[$val]['id_customer'] 		= get_name('production','id_customer','no_ipp',str_replace('BQ-','',$valx['id_bq']));
			$ArrBudget3[$val]['nm_customer'] 		= get_name('production','nm_customer','no_ipp',str_replace('BQ-','',$valx['id_bq']));
			$ArrBudget3[$val]['project'] 			= get_name('production','project','no_ipp',str_replace('BQ-','',$valx['id_bq']));
			
			$ArrBudget3[$val]['est_mat'] 			= $valx['sum_mat'] + $sum_mat;
			$ArrBudget3[$val]['est_cost'] 			= $valx['est_harga'] + $sum_mat_price + $sum_mat_price_acc;
			$ArrBudget3[$val]['direct_labour'] 		= $valx['direct_labour'];
			$ArrBudget3[$val]['indirect_labour'] 	= $valx['indirect_labour'];
			$ArrBudget3[$val]['machine'] 			= $valx['machine'];
			$ArrBudget3[$val]['mould_mandrill'] 	= $valx['mould_mandrill'];
			$ArrBudget3[$val]['consumable'] 		= $valx['consumable'];
			$ArrBudget3[$val]['depresiasi_foh'] 	= $valx['foh_depresiasi'];
			$ArrBudget3[$val]['consumable_foh'] 	= $valx['foh_consumable'];
			$ArrBudget3[$val]['gaji_non_produksi'] 	= $valx['biaya_gaji_non_produksi'];
			$ArrBudget3[$val]['biaya_admin'] 		= $valx['biaya_non_produksi'];
			$ArrBudget3[$val]['biaya_bulanan'] 		= $valx['biaya_rutin_bulanan'];
			
			$ArrBudget3[$val]['profit'] 		= Profit($valx['id_bq']);
			$ArrBudget3[$val]['allowance'] 		= Allowance($valx['id_bq']);
			$ArrBudget3[$val]['packing'] 		= manual_packing_cost($valx['id_bq']);
			$ArrBudget3[$val]['enggenering'] 	= manual_eng_cost($valx['id_bq']);
			$ArrBudget3[$val]['truck_export'] 	= manual_export_cost($valx['id_bq']);
			$ArrBudget3[$val]['truck_lokal'] 	= manual_lokal_cost($valx['id_bq']);
			$ArrBudget3[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrBudget3[$val]['create_date'] 	= date('Y-m-d H:i:s');
		}
		
		// print_r($ArrBudget3);
		// exit;
		$this->db->trans_start();
		
			$this->db->truncate('budget_so');
			$this->db->insert_batch('budget_so',$ArrBudget3);
			
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
			history('Success insert budget so');
		}
		echo json_encode($Arr_Data);
	}
	
	public function ExcelBudgetSo(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);
		$ipp 		= str_replace('BQ-','',$id_bq);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(28);
		$sheet->setCellValue('A'.$Row, 'DETAIL BUDGET SO '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'SO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Project');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Liner');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'PN');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spesifikasi');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Qty');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'Est Mat (Kg)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'Est Mat');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Direct Labour');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Machine');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'Mould Mandrill');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'Consumable');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'Consumable FOH');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue('Q'.$NewRow, 'Depresiasi FOH');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'Gaji Non Produksi');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'Biaya Admin');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sheet->setCellValue('T'.$NewRow, 'Biaya Bulanan');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
		$sheet->getColumnDimension('T')->setAutoSize(true);
		
		$sheet->setCellValue('U'.$NewRow, 'Profit');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setAutoSize(true);
		
		$sheet->setCellValue('V'.$NewRow, 'Allowance');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow);
		$sheet->getColumnDimension('V')->setAutoSize(true);
		
		$sheet->setCellValue('W'.$NewRow, 'Packing');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow);
		$sheet->getColumnDimension('W')->setAutoSize(true); 
		
		$sheet->setCellValue('X'.$NewRow, 'Enggenering');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow);
		$sheet->getColumnDimension('X')->setAutoSize(true);
		
		$sheet->setCellValue('Y'.$NewRow, 'Trucking');
		$sheet->getStyle('Y'.$NewRow.':Y'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Y'.$NewRow.':Y'.$NextRow);
		$sheet->getColumnDimension('Y')->setAutoSize(true);

		$sheet->setCellValue('Z'.$NewRow, 'Budget');
		$sheet->mergeCells('Z'.$NewRow.':AB'.$NewRow);
		$sheet->getStyle('Z'.$NewRow.':Z'.$NextRow)->applyFromArray($style_header);
		$sheet->setCellValue('Z'.$NextRow, 'Packing');
		$sheet->getColumnDimension('Z')->setAutoSize(true);

		$sheet->getStyle('AA'.$NewRow.':AA'.$NextRow)->applyFromArray($style_header);
		$sheet->setCellValue('AA'.$NextRow, 'Enggenering');
		$sheet->getColumnDimension('AA')->setAutoSize(true);

		$sheet->getStyle('AB'.$NewRow.':AB'.$NextRow)->applyFromArray($style_header);
		$sheet->setCellValue('AB'.$NextRow, 'Trucking');
		$sheet->getColumnDimension('AB')->setAutoSize(true);

		$GET_DET_IPP 	= get_detail_ipp();
		$GET_SPEC 		= get_detail_sales_order();
		$GET_NONFRP		= get_detail_consumable();
		$NO_IPP 		= str_replace('BQ-', '', $id_bq);
		$GET_MAX_REVISI = get_MaxRevisedSellingPrice()[$id_bq];
		$restDetail1	= get_CountMaterialDealSO($id_bq,$GET_MAX_REVISI);
		$GetDealTotal 	= $this->db->get_where('billing_so_total',array('no_ipp'=>$NO_IPP))->result_array();
		$non_frp		= get_DealSONonFRP($id_bq);
		$material		= get_DealSOMaterial($id_bq);
		
		$awal_row	= $NextRow;
		$no=0;

		if($restDetail1){
			
			$SUM = 0;
			$SumEstHarga = 0;
			$SumTot2 = 0;
			
			$EstMatKg = 0;
			$EstMat = 0;
			
			$Direct = 0;
			$Indirect = 0;
			$Machi = 0;
			$MouldM = 0;
			$Consumab = 0;
			
			$ConsFOH = 0;
			$DepFOH = 0;
			$GjNonP = 0;
			$ByAdmin = 0;
			$ByBulanan = 0;
			
			$Profits = 0;
			$Allowancex = 0;

			
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$EstMatKg 	+= $row_Cek['est_selling'];
				$EstMat 	+= $row_Cek['price_mat'];
				
				$Direct 	+= $row_Cek['direct_labour'];
				$Indirect 	+= $row_Cek['indirect_labour'];
				$Machi 		+= $row_Cek['machine'];
				$MouldM 	+= $row_Cek['mould_mandrill'];
				$Consumab 	+= $row_Cek['consumable'];
				
				$ConsFOH 	+= $row_Cek['foh_consumable'];
				$DepFOH 	+= $row_Cek['foh_depresiasi'];
				$GjNonP 	+= $row_Cek['biaya_gaji_non_produksi'];
				$ByAdmin 	+= $row_Cek['biaya_non_produksi'];
				$ByBulanan 	+= $row_Cek['biaya_rutin_bulanan'];
				
				$Profits 	+= $row_Cek['profit'];
				$Allowancex += $row_Cek['allowance'];
				$SUM	 	+= $row_Cek['total_deal'];
				
				$PROJECT 	= (!empty($GET_DET_IPP[$NO_IPP]['nm_project']))?$GET_DET_IPP[$NO_IPP]['nm_project']:'';
				$NO_SO 		= (!empty($GET_DET_IPP[$NO_IPP]['so_number']))?$GET_DET_IPP[$NO_IPP]['so_number']:'';

				$ID_MILIK 	= $row_Cek['id_milik'];
				$SERIES 	= (!empty($GET_SPEC[$ID_MILIK]['series']))?$GET_SPEC[$ID_MILIK]['series']:'';
				$SERIES_EX	= explode('-',$SERIES);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NO_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $PROJECT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_category	= $row_Cek['product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$liner	= $SERIES_EX[1];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $liner);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$pressure	= $SERIES_EX[0];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $pressure);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$spesifik	= spec_bq($row_Cek['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spesifik);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty	= $row_Cek['qty_deal'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$sum_mat2	= $row_Cek['est_selling'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sum_mat2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga2	= $row_Cek['price_mat'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				// $profit	= $est_harga ; 
				$profit	= $row_Cek['profit'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $profit);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$allowance	= $row_Cek['allowance'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $allowance);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$packing	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $packing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$enggenering	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $enggenering);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$trucking	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $trucking);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				// AGUS PLANNING DATA
				$awal_col++;
				$packing_a	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $packing_a);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$enggenering_a	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $enggenering_a);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$trucking_a	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $trucking_a);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			}
			// echo $no;exit;
			$Colsw = floatval($no) +6;
			
			// echo $Colsw."-".$Colse; exit;
			
			$sheet->setCellValue("A".$Colsw."", 'SUM');
			$sheet->getStyle("A".$Colsw.":H".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$Colsw.":H".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			
			$sheet->setCellValue("I".$Colsw."", $EstMatKg);
			$sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("I".$Colsw.":I".$Colsw."");
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue("J".$Colsw."", $EstMat);
			$sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("J".$Colsw.":J".$Colsw."");
			$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue("K".$Colsw."", $Direct );
			$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
			$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue("L".$Colsw."", $Indirect);
			$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
			$sheet->getColumnDimension('L')->setAutoSize(true);
			
			$sheet->setCellValue("M".$Colsw."", $Machi);
			$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$Colsw."", $MouldM);
			$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$Colsw."", $Consumab);
			$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$Colsw."", $ConsFOH);
			$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$Colsw."", $DepFOH);
			$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$Colsw."", $GjNonP);
			$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$Colsw."", $ByAdmin);
			$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
			
			$sheet->setCellValue("T".$Colsw."", $ByBulanan);
			$sheet->getStyle("T".$Colsw.":T".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("T".$Colsw.":T".$Colsw."");
			$sheet->getColumnDimension('T')->setAutoSize(true);
			
			$sheet->setCellValue("U".$Colsw."", $Profits);
			$sheet->getStyle("U".$Colsw.":U".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("U".$Colsw.":U".$Colsw."");
			$sheet->getColumnDimension('U')->setAutoSize(true);
			
			$sheet->setCellValue("V".$Colsw."", $Allowancex);
			$sheet->getStyle("V".$Colsw.":V".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("V".$Colsw.":V".$Colsw."");
			$sheet->getColumnDimension('V')->setAutoSize(true);
			
			$sheet->setCellValue("W".$Colsw."", $GetDealTotal[0]['pack_usd']);
			$sheet->getStyle("W".$Colsw.":W".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("W".$Colsw.":W".$Colsw."");
			$sheet->getColumnDimension('W')->setAutoSize(true);
			
			$sheet->setCellValue("X".$Colsw."", $GetDealTotal[0]['eng_usd']);
			$sheet->getStyle("X".$Colsw.":X".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("X".$Colsw.":X".$Colsw."");
			$sheet->getColumnDimension('X')->setAutoSize(true);
			
			$sheet->setCellValue("Y".$Colsw."", $GetDealTotal[0]['ship_usd']);
			$sheet->getStyle("Y".$Colsw.":Y".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Y".$Colsw.":Y".$Colsw."");
			$sheet->getColumnDimension('Y')->setAutoSize(true);
			// AGUS PLANNING DATA
			$sheet->setCellValue("Z".$Colsw."", $GetDealTotal[0]['pack_awal']);
			$sheet->getStyle("Z".$Colsw.":Z".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Z".$Colsw.":Z".$Colsw."");
			$sheet->getColumnDimension('Z')->setAutoSize(true);

			$sheet->setCellValue("AA".$Colsw."", $GetDealTotal[0]['eng_awal']);
			$sheet->getStyle("AA".$Colsw.":AA".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("AA".$Colsw.":AA".$Colsw."");
			$sheet->getColumnDimension('AA')->setAutoSize(true);

			$sheet->setCellValue("AB".$Colsw."", $GetDealTotal[0]['ship_awal']);
			$sheet->getStyle("AB".$Colsw.":AB".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("AB".$Colsw.":AB".$Colsw."");
			$sheet->getColumnDimension('AB')->setAutoSize(true);

			// $awal_col+1;
			// $SumNox	= $SumNo;
			// $Cols			= getColsChar($awal_col+1);
			// $sheet->setCellValue($Cols.$awal_row, $SumNox);
			// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			
			
			
		}

		

		if(!empty($material) or !empty($non_frp)){

			$Colsw = floatval($no) +8;
				
			$sheet->setCellValue("A".$Colsw."", 'No');
			$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			$sheet->setCellValue('B'.$Colsw, 'No SO');
			$sheet->getStyle('B'.$Colsw.':B'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('B'.$Colsw.':B'.$Colsw);
			$sheet->getColumnDimension('B')->setAutoSize(true);
			
			$sheet->setCellValue('C'.$Colsw, 'Material Name');
			$sheet->getStyle('C'.$Colsw.':C'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('C'.$Colsw.':C'.$Colsw);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			
			$sheet->setCellValue('D'.$Colsw, 'Qty');
			$sheet->getStyle('D'.$Colsw.':D'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('D'.$Colsw.':D'.$Colsw);
			$sheet->getColumnDimension('D')->setAutoSize(true);
			
			$sheet->setCellValue('E'.$Colsw, 'Unit');
			$sheet->getStyle('E'.$Colsw.':E'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('E'.$Colsw.':E'.$Colsw);
			$sheet->getColumnDimension('E')->setAutoSize(true);
			
			$sheet->setCellValue('F'.$Colsw, 'Price');
			$sheet->getStyle('F'.$Colsw.':F'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('F'.$Colsw.':F'.$Colsw);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			
			$sheet->setCellValue('G'.$Colsw, 'Profit');
			$sheet->getStyle('G'.$Colsw.':G'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('G'.$Colsw.':G'.$Colsw);
			$sheet->getColumnDimension('G')->setAutoSize(true);
			
			$sheet->setCellValue('H'.$Colsw, 'Allowance');
			$sheet->getStyle('H'.$Colsw.':H'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('H'.$Colsw.':H'.$Colsw);
			$sheet->getColumnDimension('H')->setAutoSize(true);
			
			$sheet->setCellValue('I'.$Colsw, 'Total Price');
			$sheet->getStyle('I'.$Colsw.':I'.$Colsw)->applyFromArray($style_header);
			$sheet->mergeCells('I'.$Colsw.':I'.$Colsw);
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$no = 0;
			foreach($material as $key => $valx){
				$no++;
				$Colsw++;
				$awal_col	= 0;

				$NO_SO 		= (!empty($GET_DET_IPP[$NO_IPP]['so_number']))?$GET_DET_IPP[$NO_IPP]['so_number']:'';
				
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $nomorx);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $NO_SO);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material		= strtoupper($valx['nm_material']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $nm_material);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty_deal		= $valx['qty_deal'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $qty_deal);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$satuan		= strtoupper($valx['satuan']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $satuan);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$price_unit		= $valx['price_unit'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $price_unit);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$profit		= $valx['profit'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $profit);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$allowance		= $valx['allowance'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $allowance);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$total_deal		= $valx['total_deal'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $total_deal);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
			}	
			
			foreach($non_frp as $key => $valx){
				$no++;
				$Colsw++;
				$awal_col	= 0;
				
				$NO_SO 		= (!empty($GET_DET_IPP[$NO_IPP]['so_number']))?$GET_DET_IPP[$NO_IPP]['so_number']:'';
				$MAT_NON_FRP = get_name_acc($valx['nm_material']);
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $nomorx);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $NO_SO);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_material		= strtoupper($MAT_NON_FRP);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $nm_material);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty_deal		= $valx['qty_deal'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $qty_deal);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$satuan		= strtoupper($valx['satuan']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $satuan);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$price_unit		= $valx['price_unit'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $price_unit);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$profit		= $valx['profit'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $profit);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$allowance		= $valx['allowance'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $allowance);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				
				$awal_col++;
				$total_deal		= $valx['total_deal'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $total_deal);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
			}	
		}
		
		
		$sheet->setTitle('Excel Budget So');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="budget-so-'.$ipp.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function modalDetailCost(){
		$this->load->view('Budget_so/modalDetailCost');
	}
	
	public function printCostControl(){ 
		$id_product	= $this->uri->segment(3);
		$id_milik	= $this->uri->segment(4);
		$id_bq		= $this->uri->segment(5);
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();
		
		include 'plusPrintPrice.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		printCostControl($Nama_Beda, $id_product, $koneksi, $printby, $id_milik, $id_bq);
	}

	function insert_select_budget_so_deal_project(){ 
		$data = $this->input->post();
		$where2 = "";
		if(!empty($data['no_ipp_filter'])){
			$ListIPP = $data['no_ipp_filter'];
			$dtListArray = array();
			$ListIPP2 = array();
			foreach($ListIPP AS $val => $valx){
				$dtListArray[$val] = str_replace('BQ-','',$valx);
				$ListIPP2[] = str_replace('BQ-','',$valx);
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			// echo $dtImplode;
			$where2 = " WHERE a.no_ipp IN ".$dtImplode."";
		}

		history('Try update budget SO');
		
		$sql_ 		= "SELECT a.* FROM billing_so_total a ".$where2."";
		$sqlCheck 	= $this->db->query($sql_)->result_array();
	
		$ArrBudget3 = array();
		$GET_DET_IPP = get_detail_ipp();
		$GET_MAX_REVISI = get_MaxRevisedSellingPrice();
		$GET_MATERIAL_DEAL = get_CountMaterialDealSOTotal($ListIPP,$ListIPP2);
		if(!empty($sqlCheck)){
			foreach($sqlCheck AS $val => $valx){
				$NO_IPP = $valx['no_ipp'];
				$ID_BQ = 'BQ-'.$NO_IPP;

				$ID_CUSTOMER 	= (!empty($GET_DET_IPP[$NO_IPP]['id_customer']))?$GET_DET_IPP[$NO_IPP]['id_customer']:NULL;
				$NM_CUSTOMER 	= (!empty($GET_DET_IPP[$NO_IPP]['nm_customer']))?$GET_DET_IPP[$NO_IPP]['nm_customer']:NULL;
				$PROJECT 		= (!empty($GET_DET_IPP[$NO_IPP]['nm_project']))?$GET_DET_IPP[$NO_IPP]['nm_project']:NULL;
				
				//GET MAX REVISED
				$REVISI 		= (!empty($GET_MAX_REVISI[$ID_BQ]))?$GET_MAX_REVISI[$ID_BQ]:0;
				$UNIQ_DEAL_MAT 	= $NO_IPP.'-'.$REVISI;
				$MATERIAL_DEAL 	= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['est_selling']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['est_selling']:0;
				$PROFIT 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['profit']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['profit']:0;
				$ALLOWANCE 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['allowance']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['allowance']:0;
				$PRICE_MAT 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['price_mat']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['price_mat']:0;
				$direct_labour 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['direct_labour']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['direct_labour']:0;
				$indirect_labour 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['indirect_labour']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['indirect_labour']:0;
				$machine 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['machine']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['machine']:0;
				$mould_mandrill 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['mould_mandrill']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['mould_mandrill']:0;
				$consumable 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['consumable']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['consumable']:0;
				$foh_consumable 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['foh_consumable']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['foh_consumable']:0;
				$foh_depresiasi 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['foh_depresiasi']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['foh_depresiasi']:0;
				$biaya_gaji_non_produksi 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['biaya_gaji_non_produksi']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['biaya_gaji_non_produksi']:0;
				$biaya_non_produksi 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['biaya_non_produksi']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['biaya_non_produksi']:0;
				$biaya_rutin_bulanan 		= (!empty($GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['biaya_rutin_bulanan']))?$GET_MATERIAL_DEAL[$UNIQ_DEAL_MAT]['biaya_rutin_bulanan']:0;

				$ArrBudget3[$val]['id_bq'] 				= $ID_BQ;
				$ArrBudget3[$val]['id_customer'] 		= $ID_CUSTOMER;
				$ArrBudget3[$val]['nm_customer'] 		= $NM_CUSTOMER;
				$ArrBudget3[$val]['project'] 			= $PROJECT;
				
				$ArrBudget3[$val]['est_mat'] 			= $MATERIAL_DEAL;
				$ArrBudget3[$val]['est_cost'] 			= $PRICE_MAT;
				$ArrBudget3[$val]['direct_labour'] 		= $direct_labour;
				$ArrBudget3[$val]['indirect_labour'] 	= $indirect_labour;
				$ArrBudget3[$val]['machine'] 			= $machine;
				$ArrBudget3[$val]['mould_mandrill'] 	= $mould_mandrill;
				$ArrBudget3[$val]['consumable'] 		= $consumable;
				$ArrBudget3[$val]['depresiasi_foh'] 	= $foh_consumable;
				$ArrBudget3[$val]['consumable_foh'] 	= $foh_depresiasi;
				$ArrBudget3[$val]['gaji_non_produksi'] 	= $biaya_gaji_non_produksi;
				$ArrBudget3[$val]['biaya_admin'] 		= $biaya_non_produksi;
				$ArrBudget3[$val]['biaya_bulanan'] 		= $biaya_rutin_bulanan;
				
				$ArrBudget3[$val]['profit'] 		= $PROFIT;
				$ArrBudget3[$val]['allowance'] 		= $ALLOWANCE;
				$ArrBudget3[$val]['packing'] 		= ($valx['pack_usd'] > 0)?$valx['pack_usd']:$valx['pack_awal'];
				$ArrBudget3[$val]['enggenering'] 	= $valx['eng_usd'];
				$ArrBudget3[$val]['truck_export'] 	= 0;
				$ArrBudget3[$val]['truck_lokal'] 	= $valx['ship_usd'];
				$ArrBudget3[$val]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
				$ArrBudget3[$val]['create_date'] 	= date('Y-m-d H:i:s');
			}
		}

		//Tanki
		$sql_ 		= "SELECT a.* FROM planning_tanki a ".$where2."";
		$sqlCheck 	= $this->db->query($sql_)->result_array();
	
		
		$UNIQ = 999;
		if(!empty($sqlCheck)){
			foreach($sqlCheck AS $val => $valx){
				$NO_IPP = $valx['no_ipp'];
				$ID_BQ 	= 'BQ-'.$NO_IPP;

				$GET_DET_IPP 		= $this->tanki_model->get_ipp_detail($NO_IPP);
				$GET_MATERIAL_DEAL 	= $this->tanki_model->get_budget_so($NO_IPP);

				$ID_CUSTOMER 		= (!empty($GET_DET_IPP['id_customer']))?$GET_DET_IPP['id_customer']:NULL;
				$NM_CUSTOMER 		= (!empty($GET_DET_IPP['customer']))?$GET_DET_IPP['customer']:NULL;
				$PROJECT 			= (!empty($GET_DET_IPP['nm_project']))?$GET_DET_IPP['nm_project']:NULL;
				
				//GET MAX REVISED
				$REVISI 		= 0;
				$MATERIAL_DEAL 		= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['sum_mat_cost']))?$GET_MATERIAL_DEAL[$NO_IPP]['sum_mat_cost']:0;
				$PROFIT 			= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['tot_profit']))?$GET_MATERIAL_DEAL[$NO_IPP]['tot_profit']:0;
				$ALLOWANCE 			= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['tot_allowance']))?$GET_MATERIAL_DEAL[$NO_IPP]['tot_allowance']:0;
				$PRICE_MAT 			= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['sum_mat_cost']))?$GET_MATERIAL_DEAL[$NO_IPP]['sum_mat_cost']:0;
				$direct_labour 		= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['sum_mp']))?$GET_MATERIAL_DEAL[$NO_IPP]['sum_mp']:0;
				$indirect_labour 	= 0;
				$machine 			= 0;
				$mould_mandrill 	= 0;
				$consumable 		= 0;
				$foh_consumable 			= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['sum_foh']))?$GET_MATERIAL_DEAL[$NO_IPP]['sum_foh']:0;
				$foh_depresiasi 			= 0;
				$biaya_gaji_non_produksi 	= 0;
				$biaya_non_produksi 		= 0;
				$biaya_rutin_bulanan 		= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['sum_admin']))?$GET_MATERIAL_DEAL[$NO_IPP]['sum_admin']:0;
				$tot_packing 		= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['tot_packing']))?$GET_MATERIAL_DEAL[$NO_IPP]['tot_packing']:0;
				$tot_eksport 		= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['tot_eksport']))?$GET_MATERIAL_DEAL[$NO_IPP]['tot_eksport']:0;
				$tot_lokal 		= (!empty($GET_MATERIAL_DEAL[$NO_IPP]['tot_lokal']))?$GET_MATERIAL_DEAL[$NO_IPP]['tot_lokal']:0;

				$ArrBudget3[$val.$UNIQ]['id_bq'] 				= $NO_IPP;
				$ArrBudget3[$val.$UNIQ]['id_customer'] 		= $ID_CUSTOMER;
				$ArrBudget3[$val.$UNIQ]['nm_customer'] 		= $NM_CUSTOMER;
				$ArrBudget3[$val.$UNIQ]['project'] 			= $PROJECT;
				
				$ArrBudget3[$val.$UNIQ]['est_mat'] 			= $MATERIAL_DEAL;
				$ArrBudget3[$val.$UNIQ]['est_cost'] 			= $PRICE_MAT;
				$ArrBudget3[$val.$UNIQ]['direct_labour'] 		= $direct_labour;
				$ArrBudget3[$val.$UNIQ]['indirect_labour'] 	= $indirect_labour;
				$ArrBudget3[$val.$UNIQ]['machine'] 			= $machine;
				$ArrBudget3[$val.$UNIQ]['mould_mandrill'] 	= $mould_mandrill;
				$ArrBudget3[$val.$UNIQ]['consumable'] 		= $consumable;
				$ArrBudget3[$val.$UNIQ]['depresiasi_foh'] 	= $foh_consumable;
				$ArrBudget3[$val.$UNIQ]['consumable_foh'] 	= $foh_depresiasi;
				$ArrBudget3[$val.$UNIQ]['gaji_non_produksi'] 	= $biaya_gaji_non_produksi;
				$ArrBudget3[$val.$UNIQ]['biaya_admin'] 		= $biaya_non_produksi;
				$ArrBudget3[$val.$UNIQ]['biaya_bulanan'] 		= $biaya_rutin_bulanan;
				
				$ArrBudget3[$val.$UNIQ]['profit'] 		= $PROFIT;
				$ArrBudget3[$val.$UNIQ]['allowance'] 		= $ALLOWANCE;
				$ArrBudget3[$val.$UNIQ]['packing'] 		= $tot_packing;
				$ArrBudget3[$val.$UNIQ]['enggenering'] 	= 0;
				$ArrBudget3[$val.$UNIQ]['truck_export'] 	= $tot_eksport;
				$ArrBudget3[$val.$UNIQ]['truck_lokal'] 	= $tot_lokal;
				$ArrBudget3[$val.$UNIQ]['create_by'] 		= $this->session->userdata['ORI_User']['username'];
				$ArrBudget3[$val.$UNIQ]['create_date'] 	= date('Y-m-d H:i:s');
			}
		}
		
		
		// print_r($ArrBudget3);
		// exit;
		$this->db->trans_start();
			$this->db->truncate('budget_so_new');
			$this->db->insert_batch('budget_so_new',$ArrBudget3);
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
			history('Success insert budget so');
		}
		echo json_encode($Arr_Data);
	}

	//SO PROJECT COSTCONTROL
	public function modal_detail_so(){
		$id_bq = $this->uri->segment(3);
		
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];
				
		$get_product	= $this->db->get_where('billing_so_product',array('no_ipp'=>$ipp,'product <>'=>'product kosong'))->result_array();

		$sql_engginering 	= "SELECT b.* FROM billing_so_add b WHERE b.category='eng' AND b.no_ipp = '".$ipp."'";
		$get_engginering	= $this->db->query($sql_engginering)->result_array();

		$sql_packing 	= "SELECT b.* FROM billing_so_add b WHERE b.category='pack' AND b.no_ipp = '".$ipp."'";
		$get_packing	= $this->db->query($sql_packing)->result_array();
	
		$sql_shipping 	= "SELECT b.* FROM billing_so_add b WHERE b.category='ship' AND b.no_ipp = '".$ipp."'";
		$get_shipping	= $this->db->query($sql_shipping)->result_array();
		
		$sql_non_frp 	= "SELECT b.* FROM billing_so_add b WHERE (b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya') AND b.no_ipp = '".$ipp."'";
		$get_non_frp	= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material 	= "SELECT b.* FROM billing_so_add b WHERE b.category='mat' AND b.no_ipp = '".$ipp."'";
		$get_material	= $this->db->query($sql_material)->result_array();
		
		$data = array(
			'id_bq' => $id_bq,
			'ipp' => $ipp,
			'getDetail' => $get_product,
			'GET_DET_SO' => get_detail_sales_order(),
			'GET_TRUCKING' => get_detail_selling(),
			'getEngCost' => $get_engginering,
			'getPackCost' => $get_packing,
			'getTruck' => $get_shipping,
			'non_frp'		=> $get_non_frp,
			'material'		=> $get_material
		);
		
		$this->load->view('Budget_so/modal_detail_so', $data);
	}

	public function ExcelBudgetSoEstimasi(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);
		$ipp 		= str_replace('BQ-','',$id_bq);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(25);
		$sheet->setCellValue('A'.$Row, 'DETAIL BUDGET SO ESTIMASI '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'SO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Project');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Liner');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'PN');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spesifikasi');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Qty');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'Est Mat (Kg)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'Est Mat');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Direct Labour');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Machine');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'Mould Mandrill');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'Consumable');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'Consumable FOH');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue('Q'.$NewRow, 'Depresiasi FOH');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'Gaji Non Produksi');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'Biaya Admin');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sheet->setCellValue('T'.$NewRow, 'Biaya Bulanan');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
		$sheet->getColumnDimension('T')->setAutoSize(true);
		
		$sheet->setCellValue('U'.$NewRow, 'Profit');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setAutoSize(true);
		
		$sheet->setCellValue('V'.$NewRow, 'Allowance');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow);
		$sheet->getColumnDimension('V')->setAutoSize(true);
		
		$sheet->setCellValue('W'.$NewRow, 'Packing');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow);
		$sheet->getColumnDimension('W')->setAutoSize(true); 
		
		$sheet->setCellValue('X'.$NewRow, 'Enggenering');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow);
		$sheet->getColumnDimension('X')->setAutoSize(true);
		
		$sheet->setCellValue('Y'.$NewRow, 'Trucking');
		$sheet->getStyle('Y'.$NewRow.':Y'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Y'.$NewRow.':Y'.$NextRow);
		$sheet->getColumnDimension('Y')->setAutoSize(true);
		
		$GET_DET_IPP 	= get_detail_ipp();
		$GET_SPEC 		= get_detail_sales_order();
		$GET_NONFRP		= get_detail_consumable();
		$NO_IPP 		= str_replace('BQ-', '', $id_bq);
		$GET_MAX_REVISI = get_MaxRevisedSellingPrice()[$id_bq];
		$restDetail1	= get_CountMaterialDealSOEstimasi($id_bq,$GET_MAX_REVISI);
		$GetDealTotal 	= $this->db->get_where('billing_so_total',array('no_ipp'=>$NO_IPP))->result_array();
		$non_frp		= get_DealSONonFRP($id_bq);
		$material		= get_DealSOMaterial($id_bq);
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			$SUM = 0;
			$SumEstHarga = 0;
			$SumTot2 = 0;
			
			$EstMatKg = 0;
			$EstMat = 0;
			
			$Direct = 0;
			$Indirect = 0;
			$Machi = 0;
			$MouldM = 0;
			$Consumab = 0;
			
			$ConsFOH = 0;
			$DepFOH = 0;
			$GjNonP = 0;
			$ByAdmin = 0;
			$ByBulanan = 0;
			
			$Profits = 0;
			$Allowancex = 0;

			
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$EstMatKg 	+= $row_Cek['est_selling'];
				$EstMat 	+= $row_Cek['price_mat'];
				
				$Direct 	+= $row_Cek['direct_labour'];
				$Indirect 	+= $row_Cek['indirect_labour'];
				$Machi 		+= $row_Cek['machine'];
				$MouldM 	+= $row_Cek['mould_mandrill'];
				$Consumab 	+= $row_Cek['consumable'];
				
				$ConsFOH 	+= $row_Cek['foh_consumable'];
				$DepFOH 	+= $row_Cek['foh_depresiasi'];
				$GjNonP 	+= $row_Cek['biaya_gaji_non_produksi'];
				$ByAdmin 	+= $row_Cek['biaya_non_produksi'];
				$ByBulanan 	+= $row_Cek['biaya_rutin_bulanan'];
				
				$Profits 	+= $row_Cek['profit'];
				$Allowancex += $row_Cek['allowance'];
				$SUM	 	+= $row_Cek['total_deal'];
				
				$PROJECT 	= (!empty($GET_DET_IPP[$NO_IPP]['nm_project']))?$GET_DET_IPP[$NO_IPP]['nm_project']:'';
				$NO_SO 		= (!empty($GET_DET_IPP[$NO_IPP]['so_number']))?$GET_DET_IPP[$NO_IPP]['so_number']:'';

				$ID_MILIK 	= $row_Cek['id_milik'];
				$SERIES 	= (!empty($GET_SPEC[$ID_MILIK]['series']))?$GET_SPEC[$ID_MILIK]['series']:'';
				$SERIES_EX	= explode('-',$SERIES);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NO_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $PROJECT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_category	= $row_Cek['product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$liner	= $SERIES_EX[1];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $liner);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$pressure	= $SERIES_EX[0];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $pressure);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$spesifik	= spec_bq($row_Cek['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spesifik);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty	= $row_Cek['qty_deal'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);
				
				$awal_col++;
				$sum_mat2	= $row_Cek['est_selling'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sum_mat2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_harga2	= $row_Cek['price_mat'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				// $profit	= $est_harga ; 
				$profit	= $row_Cek['profit'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $profit);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$allowance	= $row_Cek['allowance'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $allowance);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$packing	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $packing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$enggenering	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $enggenering);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$trucking	= '-';
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $trucking);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
			// echo $no;exit;
			$Colsw = floatval($no) +6;
			
			// echo $Colsw."-".$Colse; exit;
			
			$sheet->setCellValue("A".$Colsw."", 'SUM');
			$sheet->getStyle("A".$Colsw.":H".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$Colsw.":H".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			
			$sheet->setCellValue("I".$Colsw."", $EstMatKg);
			$sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("I".$Colsw.":I".$Colsw."");
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue("J".$Colsw."", $EstMat);
			$sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("J".$Colsw.":J".$Colsw."");
			$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue("K".$Colsw."", $Direct );
			$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
			$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue("L".$Colsw."", $Indirect);
			$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
			$sheet->getColumnDimension('L')->setAutoSize(true);
			
			$sheet->setCellValue("M".$Colsw."", $Machi);
			$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$Colsw."", $MouldM);
			$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$Colsw."", $Consumab);
			$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$Colsw."", $ConsFOH);
			$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$Colsw."", $DepFOH);
			$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$Colsw."", $GjNonP);
			$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$Colsw."", $ByAdmin);
			$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
			
			$sheet->setCellValue("T".$Colsw."", $ByBulanan);
			$sheet->getStyle("T".$Colsw.":T".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("T".$Colsw.":T".$Colsw."");
			$sheet->getColumnDimension('T')->setAutoSize(true);
			
			$sheet->setCellValue("U".$Colsw."", $Profits);
			$sheet->getStyle("U".$Colsw.":U".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("U".$Colsw.":U".$Colsw."");
			$sheet->getColumnDimension('U')->setAutoSize(true);
			
			$sheet->setCellValue("V".$Colsw."", $Allowancex);
			$sheet->getStyle("V".$Colsw.":V".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("V".$Colsw.":V".$Colsw."");
			$sheet->getColumnDimension('V')->setAutoSize(true);
			
			$sheet->setCellValue("W".$Colsw."", $GetDealTotal[0]['pack_usd']);
			$sheet->getStyle("W".$Colsw.":W".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("W".$Colsw.":W".$Colsw."");
			$sheet->getColumnDimension('W')->setAutoSize(true);
			
			$sheet->setCellValue("X".$Colsw."", $GetDealTotal[0]['eng_usd']);
			$sheet->getStyle("X".$Colsw.":X".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("X".$Colsw.":X".$Colsw."");
			$sheet->getColumnDimension('X')->setAutoSize(true);
			
			$sheet->setCellValue("Y".$Colsw."", $GetDealTotal[0]['ship_usd']);
			$sheet->getStyle("Y".$Colsw.":Y".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Y".$Colsw.":Y".$Colsw."");
			$sheet->getColumnDimension('Y')->setAutoSize(true);
				
			// $awal_col+1;
			// $SumNox	= $SumNo;
			// $Cols			= getColsChar($awal_col+1);
			// $sheet->setCellValue($Cols.$awal_row, $SumNox);
			// $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			
			$Colsw = floatval($no) +8;
			
			if(!empty($material) or !empty($non_frp)){
				
				$sheet->setCellValue("A".$Colsw."", 'No');
				$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
				$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
				$sheet->getColumnDimension('A')->setAutoSize(true);
				
				$sheet->setCellValue('B'.$Colsw, 'No SO');
				$sheet->getStyle('B'.$Colsw.':B'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('B'.$Colsw.':B'.$Colsw);
				$sheet->getColumnDimension('B')->setAutoSize(true);
				
				$sheet->setCellValue('C'.$Colsw, 'Material Name');
				$sheet->getStyle('C'.$Colsw.':C'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('C'.$Colsw.':C'.$Colsw);
				$sheet->getColumnDimension('C')->setAutoSize(true);
				
				$sheet->setCellValue('D'.$Colsw, 'Qty');
				$sheet->getStyle('D'.$Colsw.':D'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('D'.$Colsw.':D'.$Colsw);
				$sheet->getColumnDimension('D')->setAutoSize(true);
				
				$sheet->setCellValue('E'.$Colsw, 'Unit');
				$sheet->getStyle('E'.$Colsw.':E'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('E'.$Colsw.':E'.$Colsw);
				$sheet->getColumnDimension('E')->setAutoSize(true);
				
				$sheet->setCellValue('F'.$Colsw, 'Price');
				$sheet->getStyle('F'.$Colsw.':F'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('F'.$Colsw.':F'.$Colsw);
				$sheet->getColumnDimension('F')->setAutoSize(true);
				
				$sheet->setCellValue('G'.$Colsw, 'Profit');
				$sheet->getStyle('G'.$Colsw.':G'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('G'.$Colsw.':G'.$Colsw);
				$sheet->getColumnDimension('G')->setAutoSize(true);
				
				$sheet->setCellValue('H'.$Colsw, 'Allowance');
				$sheet->getStyle('H'.$Colsw.':H'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('H'.$Colsw.':H'.$Colsw);
				$sheet->getColumnDimension('H')->setAutoSize(true);
				
				$sheet->setCellValue('I'.$Colsw, 'Total Price');
				$sheet->getStyle('I'.$Colsw.':I'.$Colsw)->applyFromArray($style_header);
				$sheet->mergeCells('I'.$Colsw.':I'.$Colsw);
				$sheet->getColumnDimension('I')->setAutoSize(true);
				
				$no = 0;
				foreach($material as $key => $valx){
					$no++;
					$Colsw++;
					$awal_col	= 0;

					$NO_SO 		= (!empty($GET_DET_IPP[$NO_IPP]['so_number']))?$GET_DET_IPP[$NO_IPP]['so_number']:'';
					
					$awal_col++;
					$nomorx		= $no;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $nomorx);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
					
					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $NO_SO);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
					
					$awal_col++;
					$nm_material		= strtoupper($valx['nm_material']);
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $nm_material);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
					
					$awal_col++;
					$qty_deal		= $valx['qty_deal'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $qty_deal);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$satuan		= strtoupper($valx['satuan']);
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $satuan);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$price_unit		= $valx['price_unit'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $price_unit);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$profit		= $valx['profit'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $profit);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$allowance		= $valx['allowance'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $allowance);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$total_deal		= $valx['total_deal'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $total_deal);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				}	
				
				foreach($non_frp as $key => $valx){
					$no++;
					$Colsw++;
					$awal_col	= 0;
					
					$NO_SO 		= (!empty($GET_DET_IPP[$NO_IPP]['so_number']))?$GET_DET_IPP[$NO_IPP]['so_number']:'';
					$MAT_NON_FRP = get_name_acc($valx['nm_material']);
					$awal_col++;
					$nomorx		= $no;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $nomorx);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
					
					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $NO_SO);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
					
					$awal_col++;
					$nm_material		= strtoupper($MAT_NON_FRP);
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $nm_material);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray3);
					
					$awal_col++;
					$qty_deal		= $valx['qty_deal'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $qty_deal);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$satuan		= strtoupper($valx['satuan']);
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $satuan);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$price_unit		= $valx['price_unit'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $price_unit);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$profit		= $valx['profit'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $profit);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$allowance		= $valx['allowance'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $allowance);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
					
					$awal_col++;
					$total_deal		= $valx['total_deal'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$Colsw, $total_deal);
					$sheet->getStyle($Cols.$Colsw)->applyFromArray($styleArray4);
				}	
			}
			
			
		}
		
		
		$sheet->setTitle('Excel Budget So Estimasi');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="budget-so-estimasi-'.$ipp.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function ExcelBudgetSoTanki(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);
		$ipp 		= str_replace('BQ-','',$id_bq);

		$this->load->library("PHPExcel");
		$objPHPExcel	= new PHPExcel();
		
		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(28);
		$sheet->setCellValue('A'.$Row, 'DETAIL BUDGET SO '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'SO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Tanki No');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Dim');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Panjang');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spesifikasi');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Qty');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'Est Mat (Kg)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'Est Mat');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Direct Labour');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Machine');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'Mould Mandrill');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'Consumable');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'Consumable FOH');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue('Q'.$NewRow, 'Depresiasi FOH');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'Gaji Non Produksi');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'Biaya Admin');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sheet->setCellValue('T'.$NewRow, 'Biaya Bulanan');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
		$sheet->getColumnDimension('T')->setAutoSize(true);
		
		$sheet->setCellValue('U'.$NewRow, 'Profit');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setAutoSize(true);
		
		$sheet->setCellValue('V'.$NewRow, 'Allowance');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow);
		$sheet->getColumnDimension('V')->setAutoSize(true);
		
		$sheet->setCellValue('W'.$NewRow, 'Packing');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow);
		$sheet->getColumnDimension('W')->setAutoSize(true); 
		
		$sheet->setCellValue('X'.$NewRow, 'Enggenering');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow);
		$sheet->getColumnDimension('X')->setAutoSize(true);
		
		$sheet->setCellValue('Y'.$NewRow, 'Trucking');
		$sheet->getStyle('Y'.$NewRow.':Y'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Y'.$NewRow.':Y'.$NextRow);
		$sheet->getColumnDimension('Y')->setAutoSize(true);

		$sheet->setCellValue('Z'.$NewRow, 'Budget');
		$sheet->mergeCells('Z'.$NewRow.':AB'.$NewRow);
		$sheet->getStyle('Z'.$NewRow.':Z'.$NextRow)->applyFromArray($tableHeader);
		$sheet->setCellValue('Z'.$NextRow, 'Packing');
		$sheet->getColumnDimension('Z')->setAutoSize(true);

		$sheet->getStyle('AA'.$NewRow.':AA'.$NextRow)->applyFromArray($tableHeader);
		$sheet->setCellValue('AA'.$NextRow, 'Enggenering');
		$sheet->getColumnDimension('AA')->setAutoSize(true);

		$sheet->getStyle('AB'.$NewRow.':AB'.$NextRow)->applyFromArray($tableHeader);
		$sheet->setCellValue('AB'.$NextRow, 'Trucking');
		$sheet->getColumnDimension('AB')->setAutoSize(true);

		$NO_IPP 		= str_replace('BQ-', '', $id_bq);
		$restDetail1	= $this->tanki
								->select('
									b.id,
									a.id_header, 
									b.bagian, 
									c.nm_part,
									b.t_dsg AS thickness_design,
									b.jml AS qty,
									b.dia_lebar AS diameter,
									b.panjang AS panjang
									')
								->join('bq_detail_detail b','a.id_header=b.id_header','left')
								->join('ms_category_part c','b.id_category=c.id','left')
								->get_where('bq_selling_price a',
									array(
										'a.no_ipp'=>$NO_IPP, 
										'a.qty_deal >'=>0,
										'b.category'=>'frp'
									))
								->result_array();
		// echo $this->db->last_query();
		// exit;
		$GetDealTotal 	= $this->db->get_where('billing_so_total',array('no_ipp'=>$NO_IPP))->result_array();
		$non_frp		= $this->tanki
											->select('
												b.id,
												a.id_header, 
												b.bagian,
												b.qty AS qty,
												b.id_unit AS unit,
												b.unit_price
												')
											->join('bq_detail_detail b','a.id_header=b.id_header','left')
											->get_where('bq_selling_price a',
												array(
													'a.no_ipp'=>$NO_IPP, 
													'a.qty_deal >'=>0,
													'b.category'=>'non frp'
												))
											->result_array();
		$material		= [];
		
		$awal_row	= $NextRow;
		$no=0;
		if($restDetail1){
			
			$SUM = 0;
			$SumEstHarga = 0;
			$SumTot2 = 0;
			
			$EstMatKg = 0;
			$EstMat = 0;
			
			$Direct = 0;
			$Indirect = 0;
			$Machi = 0;
			$MouldM = 0;
			$Consumab = 0;
			
			$ConsFOH = 0;
			$DepFOH = 0;
			$GjNonP = 0;
			$ByAdmin = 0;
			$ByBulanan = 0;
			
			$Profits = 0;
			$Allowancex = 0;

			
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$getMaterial = $this->tanki
									->select('	SUM(a.berat) AS berat,
												SUM(a.berat*a.price) AS price
											')
									->group_by('a.id_det')
									->get_where('bq_detail_material_new a',
											array(
												'a.id_det' => $row_Cek['id'],
												'a.id_material !=' => 'MTL-1903000'
											)
										)
									->result_array();
				$est_selling = (!empty($getMaterial[0]['berat']))?$getMaterial[0]['berat']:0;
				$price_mat = (!empty($getMaterial[0]['price']))?$getMaterial[0]['price']:0;

				$SQL_COST 		= " SELECT
									a.man_hours AS man_hours,
									(a.man_hours * a.pe_direct_labour) AS direct_labour,
									(a.man_hours * a.pe_indirect_labour) AS indirect_labour,
									(a.t_time * a.pe_machine) AS machine,
									0 AS mould_mandrill,
									($est_selling * a.pe_consumable) AS consumable,
									(
											((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_selling * a.pe_consumable))+ $price_mat 
									) * ( a.pe_foh_consumable / 100 ) AS foh_consumable,
									(
											((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_selling * a.pe_consumable))+ $price_mat 
									) * ( a.pe_foh_depresiasi / 100 ) AS foh_depresiasi,
									(
											((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_selling * a.pe_consumable))+ $price_mat 
									) * ( a.pe_biaya_gaji_non_produksi / 100 ) AS biaya_gaji_non_produksi,
									(
											((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_selling * a.pe_consumable))+ $price_mat 
									) * ( a.pe_biaya_non_produksi / 100 ) AS biaya_non_produksi,
									(
											(((a.man_hours * a.pe_direct_labour))+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_selling * a.pe_consumable))+ $price_mat 
									) * ( a.pe_biaya_rutin_bulanan / 100 ) AS biaya_rutin_bulanan 
								FROM
										bq_detail_detail a
								WHERE a.id='".$row_Cek['id']."'";
				$resultTanki = $this->tanki->query($SQL_COST)->result_array();
				
				$EstMatKg 	+= $est_selling;
				$EstMat 	+= $price_mat;

				$direct_labour 				= (!empty($resultTanki[0]['direct_labour']))?$resultTanki[0]['direct_labour']:0;
				$indirect_labour 			= (!empty($resultTanki[0]['indirect_labour']))?$resultTanki[0]['indirect_labour']:0;
				$machine 					= (!empty($resultTanki[0]['machine']))?$resultTanki[0]['machine']:0;
				$mould_mandrill 			= (!empty($resultTanki[0]['mould_mandrill']))?$resultTanki[0]['mould_mandrill']:0;
				$consumable 				= (!empty($resultTanki[0]['consumable']))?$resultTanki[0]['consumable']:0;
				$foh_consumable 			= (!empty($resultTanki[0]['foh_consumable']))?$resultTanki[0]['foh_consumable']:0;
				$foh_depresiasi 			= (!empty($resultTanki[0]['foh_depresiasi']))?$resultTanki[0]['foh_depresiasi']:0;
				$biaya_gaji_non_produksi 	= (!empty($resultTanki[0]['biaya_gaji_non_produksi']))?$resultTanki[0]['biaya_gaji_non_produksi']:0;
				$biaya_non_produksi 		= (!empty($resultTanki[0]['biaya_non_produksi']))?$resultTanki[0]['biaya_non_produksi']:0;
				$biaya_rutin_bulanan 		= (!empty($resultTanki[0]['biaya_rutin_bulanan']))?$resultTanki[0]['biaya_rutin_bulanan']:0;
				
				$Direct 	+= $direct_labour;
				$Indirect 	+= $indirect_labour;
				$Machi 		+= $machine;
				$MouldM 	+= $mould_mandrill;
				$Consumab 	+= $consumable;
				
				$ConsFOH 	+= $foh_consumable;
				$DepFOH 	+= $foh_depresiasi;
				$GjNonP 	+= $biaya_gaji_non_produksi;
				$ByAdmin 	+= $biaya_non_produksi;
				$ByBulanan 	+= $biaya_rutin_bulanan;
				
				// $Profits 	+= $row_Cek['profit'];
				// $Allowancex += $row_Cek['allowance'];
				// $SUM	 	+= $row_Cek['total_deal'];

				$GET_DET_IPP 		= $this->tanki_model->get_ipp_detail($NO_IPP);
				
				$PROJECT 	= (!empty($GET_DET_IPP['nm_project']))?$GET_DET_IPP['nm_project']:'';
				$NO_SO 		= (!empty($GET_DET_IPP['no_so']))?$GET_DET_IPP['no_so']:'';
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NO_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $row_Cek['id_header']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_category	= $row_Cek['nm_part'].' - '.$row_Cek['bagian'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$diameter	= number_format($row_Cek['diameter'],2);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$panjang	= number_format($row_Cek['panjang'],2);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $panjang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$spesifik	= $diameter." x ".$panjang." x ".number_format($row_Cek['thickness_design'],2);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spesifik);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$qty	= $row_Cek['qty'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$sum_mat2	= $est_selling;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sum_mat2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$est_harga2	= $price_mat;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				// $awal_col++;
				// // $profit	= $est_harga ; 
				// $profit	= $row_Cek['profit'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $profit);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				// $awal_col++;
				// $allowance	= $row_Cek['allowance'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $allowance);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				// $awal_col++;
				// $packing	= '-';
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $packing);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				// $awal_col++;
				// $enggenering	= '-';
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $enggenering);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				// $awal_col++;
				// $trucking	= '-';
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $trucking);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				// // AGUS PLANNING DATA
				// $awal_col++;
				// $packing_a	= '-';
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $packing_a);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				// $awal_col++;
				// $enggenering_a	= '-';
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $enggenering_a);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				// $awal_col++;
				// $trucking_a	= '-';
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $trucking_a);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
			// echo $no;exit;
			$Colsw = floatval($no) +6;
			
			// echo $Colsw."-".$Colse; exit;
			
			$sheet->setCellValue("A".$Colsw."", 'SUM');
			$sheet->getStyle("A".$Colsw.":H".$Colsw."")->applyFromArray($tableHeader);
			$sheet->mergeCells("A".$Colsw.":H".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			
			$sheet->setCellValue("I".$Colsw."", $EstMatKg);
			$sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("I".$Colsw.":I".$Colsw."");
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue("J".$Colsw."", $EstMat);
			$sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("J".$Colsw.":J".$Colsw."");
			$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue("K".$Colsw."", $Direct );
			$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
			$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue("L".$Colsw."", $Indirect);
			$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
			$sheet->getColumnDimension('L')->setAutoSize(true);
			
			$sheet->setCellValue("M".$Colsw."", $Machi);
			$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$Colsw."", $MouldM);
			$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$Colsw."", $Consumab);
			$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$Colsw."", $ConsFOH);
			$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$Colsw."", $DepFOH);
			$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$Colsw."", $GjNonP);
			$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$Colsw."", $ByAdmin);
			$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
			
			$sheet->setCellValue("T".$Colsw."", $ByBulanan);
			$sheet->getStyle("T".$Colsw.":T".$Colsw."")->applyFromArray($tableBodyRight);
			$sheet->mergeCells("T".$Colsw.":T".$Colsw."");
			$sheet->getColumnDimension('T')->setAutoSize(true);
			
			// $sheet->setCellValue("U".$Colsw."", $Profits);
			// $sheet->getStyle("U".$Colsw.":U".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("U".$Colsw.":U".$Colsw."");
			// $sheet->getColumnDimension('U')->setAutoSize(true);
			
			// $sheet->setCellValue("V".$Colsw."", $Allowancex);
			// $sheet->getStyle("V".$Colsw.":V".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("V".$Colsw.":V".$Colsw."");
			// $sheet->getColumnDimension('V')->setAutoSize(true);
			
			// $sheet->setCellValue("W".$Colsw."", $GetDealTotal[0]['pack_usd']);
			// $sheet->getStyle("W".$Colsw.":W".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("W".$Colsw.":W".$Colsw."");
			// $sheet->getColumnDimension('W')->setAutoSize(true);
			
			// $sheet->setCellValue("X".$Colsw."", $GetDealTotal[0]['eng_usd']);
			// $sheet->getStyle("X".$Colsw.":X".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("X".$Colsw.":X".$Colsw."");
			// $sheet->getColumnDimension('X')->setAutoSize(true);
			
			// $sheet->setCellValue("Y".$Colsw."", $GetDealTotal[0]['ship_usd']);
			// $sheet->getStyle("Y".$Colsw.":Y".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("Y".$Colsw.":Y".$Colsw."");
			// $sheet->getColumnDimension('Y')->setAutoSize(true);
			// // AGUS PLANNING DATA
			// $sheet->setCellValue("Z".$Colsw."", $GetDealTotal[0]['pack_awal']);
			// $sheet->getStyle("Z".$Colsw.":Z".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("Z".$Colsw.":Z".$Colsw."");
			// $sheet->getColumnDimension('Z')->setAutoSize(true);

			// $sheet->setCellValue("AA".$Colsw."", $GetDealTotal[0]['eng_awal']);
			// $sheet->getStyle("AA".$Colsw.":AA".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("AA".$Colsw.":AA".$Colsw."");
			// $sheet->getColumnDimension('AA')->setAutoSize(true);

			// $sheet->setCellValue("AB".$Colsw."", $GetDealTotal[0]['ship_awal']);
			// $sheet->getStyle("AB".$Colsw.":AB".$Colsw."")->applyFromArray($tableBodyRight);
			// $sheet->mergeCells("AB".$Colsw.":AB".$Colsw."");
			// $sheet->getColumnDimension('AB')->setAutoSize(true);

			
			
			
			
		}

		if(!empty($material) or !empty($non_frp)){

			$Colsw = floatval($no) +8;
				
			$sheet->setCellValue("A".$Colsw."", 'No');
			$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($tableHeader);
			$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			$sheet->setCellValue('B'.$Colsw, 'No SO');
			$sheet->getStyle('B'.$Colsw.':B'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('B'.$Colsw.':B'.$Colsw);
			$sheet->getColumnDimension('B')->setAutoSize(true);
			
			$sheet->setCellValue('C'.$Colsw, 'Material Name');
			$sheet->getStyle('C'.$Colsw.':C'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('C'.$Colsw.':C'.$Colsw);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			
			$sheet->setCellValue('D'.$Colsw, 'Qty');
			$sheet->getStyle('D'.$Colsw.':D'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('D'.$Colsw.':D'.$Colsw);
			$sheet->getColumnDimension('D')->setAutoSize(true);
			
			$sheet->setCellValue('E'.$Colsw, 'Unit');
			$sheet->getStyle('E'.$Colsw.':E'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('E'.$Colsw.':E'.$Colsw);
			$sheet->getColumnDimension('E')->setAutoSize(true);
			
			$sheet->setCellValue('F'.$Colsw, 'Price');
			$sheet->getStyle('F'.$Colsw.':F'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('F'.$Colsw.':F'.$Colsw);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			
			$sheet->setCellValue('G'.$Colsw, 'Profit');
			$sheet->getStyle('G'.$Colsw.':G'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('G'.$Colsw.':G'.$Colsw);
			$sheet->getColumnDimension('G')->setAutoSize(true);
			
			$sheet->setCellValue('H'.$Colsw, 'Allowance');
			$sheet->getStyle('H'.$Colsw.':H'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('H'.$Colsw.':H'.$Colsw);
			$sheet->getColumnDimension('H')->setAutoSize(true);
			
			$sheet->setCellValue('I'.$Colsw, 'Total Price');
			$sheet->getStyle('I'.$Colsw.':I'.$Colsw)->applyFromArray($tableHeader);
			$sheet->mergeCells('I'.$Colsw.':I'.$Colsw);
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$no = 0;
			// foreach($material as $key => $valx){
			// 	$no++;
			// 	$Colsw++;
			// 	$awal_col	= 0;

			// 	$NO_SO 		= (!empty($GET_DET_IPP[$NO_IPP]['so_number']))?$GET_DET_IPP[$NO_IPP]['so_number']:'';
				
			// 	$awal_col++;
			// 	$nomorx		= $no;
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $nomorx);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyLeft);
				
			// 	$awal_col++;
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $NO_SO);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyLeft);
				
			// 	$awal_col++;
			// 	$nm_material		= strtoupper($valx['nm_material']);
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $nm_material);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyLeft);
				
			// 	$awal_col++;
			// 	$qty_deal		= $valx['qty_deal'];
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $qty_deal);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
			// 	$awal_col++;
			// 	$satuan		= strtoupper($valx['satuan']);
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $satuan);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
			// 	$awal_col++;
			// 	$price_unit		= $valx['price_unit'];
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $price_unit);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
			// 	$awal_col++;
			// 	$profit		= $valx['profit'];
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $profit);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
			// 	$awal_col++;
			// 	$allowance		= $valx['allowance'];
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $allowance);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
			// 	$awal_col++;
			// 	$total_deal		= $valx['total_deal'];
			// 	$Cols		= getColsChar($awal_col);
			// 	$sheet->setCellValue($Cols.$Colsw, $total_deal);
			// 	$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
			// }	
			
			foreach($non_frp as $key => $valx){
				$no++;
				$Colsw++;
				$awal_col	= 0;

				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $nomorx);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $NO_SO.' x '.$valx['id_header']);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$nm_material		= $valx['bagian'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $nm_material);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$qty_deal		= $valx['qty'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $qty_deal);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$satuan		= strtoupper($valx['unit']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $satuan);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$price_unit		= $valx['unit_price'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $price_unit);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$profit		= '';
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $profit);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$allowance		= '';
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $allowance);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$total_deal		= $qty_deal * $price_unit;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$Colsw, $total_deal);
				$sheet->getStyle($Cols.$Colsw)->applyFromArray($tableBodyRight);
			}	
		}
		
		
		$sheet->setTitle('Excel Budget So');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="budget-so-'.$ipp.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}
