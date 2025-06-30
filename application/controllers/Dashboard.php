<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {	
	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('dashboard_model');
		$this->load->model('api_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
		/*if (!$this->session->userdata('2fa_verified')) {
            redirect('login/verify_2fa');*/
        }		
	}	
	public function index() {
		//$this->load->view('include/header', array('title'=>'Dashboard')); ALREADY ESTIMATED PRICE
		// history('View Dashboard');
		$query      = "SELECT * FROM production WHERE status <> 'CANCELED' AND status <> 'WAITING IPP RELEASE' ";
		$qty_ipp = $this->db->query($query)->num_rows();
		
		$query      = "SELECT * FROM production WHERE (
													status <> 'WAITING STRUCTURE BQ' 
													AND status <> 'CANCELED' 
													AND status <> 'WAITING ESTIMATION PROJECT'
													AND status <> 'WAITING APPROVE STRUCTURE BQ' 
													AND status <> 'WAITING APPROVE EST PROJECT'
													AND status <> 'WAITING IPP RELEASE' 
													)";
		$qty_eng = $this->db->query($query)->num_rows();
		
		$query      = "SELECT * FROM production WHERE status = 'WAITING EST PRICE PROJECT'";
		$qty_cost = $this->db->query($query)->num_rows();
		
		$query      = "SELECT a.* FROM production a LEFT JOIN color_status b ON a.status=b.status WHERE urut >= '6' AND a.status <> 'CANCELED'";
		$qty_quo = $this->db->query($query)->num_rows();
		
		$query2      = "SELECT a.* FROM production a WHERE a.status = 'ALREADY ESTIMATED PRICE'";
		$qty_quo2 = $this->db->query($query2)->num_rows();
		
		$query      = "SELECT * FROM late_project";
		$qty_late = $this->db->query($query)->num_rows();
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard',
			'qty_ipp'	=> $qty_ipp,
			'qty_eng'	=> $qty_eng,
			'qty_cost' 	=> $qty_cost,
			'qty_quo' 	=> $qty_quo,
			'qty_quo2' 	=> $qty_quo2,
			'qty_late' 	=> $qty_late
		);
		
		$this->load->view('dashboard',$data);
		
	}
	
	public function dashboard_project() {
		//$this->load->view('include/header', array('title'=>'Dashboard'));
		// history('View Dashboard');
		$query      = "	SELECT
							a.`status`,
							( SELECT COUNT( b.`status` ) AS qt FROM production b WHERE b.`status` = a.`status` AND created_by <> 'json' ) AS qty 
						FROM
							color_status a 
						WHERE
							a.`status` <> 'CANCELED' 
							AND a.`status` <> 'PENDING' 
							AND a.`status` <> 'TEST' 
							AND a.`status` <> 'ALREADY FINAL DRAWING' 
							AND a.`status` <> 'WAITING MATERIAL PLANNING' 
							AND a.`status` <> 'ALREADY SALES ORDER'
						ORDER BY
							a.urut ASC";
		$rest_data 		= $this->db->query($query)->result_array();
		
		$finish 		= $this->db->query("SELECT SUM(real_material) AS real_material, SUM(real_harga) AS real_harga, SUM(est_mat) AS est_mat, SUM(est_harga) AS est_harga, MAX(create_date) AS created_date FROM group_project WHERE sts_ipp = 'FINISH'")->result();
		$overbudget 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project WHERE sts_ipp = 'FINISH' AND persenx >= 100")->result();
		$goodbudget1 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project WHERE sts_ipp = 'FINISH' AND persenx < 100 AND persenx >= 90")->result();
		$goodbudget2 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project WHERE sts_ipp = 'FINISH' AND persenx < 90")->result();
		
		$finish2 		= $this->db->query("SELECT SUM(real_material) AS real_material, SUM(real_harga) AS real_harga, SUM(est_mat) AS est_mat, SUM(est_harga) AS est_harga, MAX(create_date) AS created_date FROM group_project WHERE sts_ipp='PROCESS PRODUCTION' OR sts_ipp = 'PARTIAL PROCESS'")->result();
		$overbudget2 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project WHERE (sts_ipp='PROCESS PRODUCTION' OR sts_ipp = 'PARTIAL PROCESS') AND persenx >= 100")->result();
		$goodbudget12 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project WHERE (sts_ipp='PROCESS PRODUCTION' OR sts_ipp = 'PARTIAL PROCESS') AND persenx < 100 AND persenx >= 90")->result();
		$goodbudget22 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project WHERE (sts_ipp='PROCESS PRODUCTION' OR sts_ipp = 'PARTIAL PROCESS') AND persenx < 90")->result();
		 
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard Project',
			'data'		=> $rest_data,
			'finish'	=> $finish,
			'overbudget' => $overbudget,
			'goodbudget1' => $goodbudget1,
			'goodbudget2' => $goodbudget2,
			'finish2'	=> $finish2,
			'overbudget2' => $overbudget2,
			'goodbudget12' => $goodbudget12,
			'goodbudget22' => $goodbudget22
		);
		history('Look dashboard project');
		$this->load->view('dashboard_project',$data);
		
	}
	
	public function dashboard_progress() {
		//$this->load->view('include/header', array('title'=>'Dashboard'));
		// history('View Dashboard');

		
		$finish 		= $this->db->query("SELECT SUM(a.real_material) AS real_material, SUM(a.real_harga) AS real_harga, SUM(a.est_mat) AS est_mat, SUM(a.est_harga) AS est_harga, MAX(a.create_date) AS created_date FROM group_project a INNER JOIN production_header b ON a.no_ipp = b.no_ipp WHERE  (a.sts_ipp='PROCESS PRODUCTION' OR a.sts_ipp = 'PARTIAL PROCESS')")->result();
		$overbudget 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project a INNER JOIN production_header b ON a.no_ipp = b.no_ipp WHERE (a.sts_ipp='PROCESS PRODUCTION' OR a.sts_ipp = 'PARTIAL PROCESS') AND a.persenx >= 100")->result();
		$goodbudget1 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project a INNER JOIN production_header b ON a.no_ipp = b.no_ipp WHERE (a.sts_ipp='PROCESS PRODUCTION' OR a.sts_ipp = 'PARTIAL PROCESS') AND a.persenx < 100 AND a.persenx >= 90")->result();
		$goodbudget2 	= $this->db->query("SELECT COUNT(*) AS jumlah FROM group_project a INNER JOIN production_header b ON a.no_ipp = b.no_ipp WHERE (a.sts_ipp='PROCESS PRODUCTION' OR a.sts_ipp = 'PARTIAL PROCESS') AND a.persenx < 90")->result();
		 
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard Project On Progress',
			'finish'	=> $finish,
			'overbudget' => $overbudget,
			'goodbudget1' => $goodbudget1,
			'goodbudget2' => $goodbudget2
		);
		history('Look dashboard project on progress');
		$this->load->view('dashboard_progress',$data);
		
	}
	
	public function logout() {
		$this->session->sess_destroy();
		history('Logout');
		$this->session->set_userdata(array());
		redirect('login');		
	}
	
	public function qry_report_revenue_chart(){
	
		$thn = date('Y');
		
		$sql = "
			SELECT
				sum(if(month(`date`)='1' ,real_material,0)) as JAN_MAT,
				sum(if(month(`date`)='2' ,real_material,0)) as FEB_MAT,
				sum(if(month(`date`)='3' ,real_material,0)) as MAR_MAT,
				sum(if(month(`date`)='4' ,real_material,0)) as APR_MAT,
				sum(if(month(`date`)='5' ,real_material,0)) as MEI_MAT,
				sum(if(month(`date`)='6' ,real_material,0)) as JUN_MAT,
				sum(if(month(`date`)='7' ,real_material,0)) as JUL_MAT,
				sum(if(month(`date`)='8' ,real_material,0)) as AGU_MAT,
				sum(if(month(`date`)='9' ,real_material,0)) as SEP_MAT,
				sum(if(month(`date`)='10' ,real_material,0)) as OKT_MAT,
				sum(if(month(`date`)='11' ,real_material,0)) as NOV_MAT,
				sum(if(month(`date`)='12' ,real_material,0)) as DES_MAT,
				sum(real_material) as SUM_MAT,
				sum(if(month(`date`)='1' ,direct_labour,0)) AS JAN_DIR,
				sum(if(month(`date`)='2' ,direct_labour,0)) AS FEB_DIR,
				sum(if(month(`date`)='3' ,direct_labour,0)) AS MAR_DIR,
				sum(if(month(`date`)='4' ,direct_labour,0)) AS APR_DIR,
				sum(if(month(`date`)='5' ,direct_labour,0)) AS MEI_DIR,
				sum(if(month(`date`)='6' ,direct_labour,0)) AS JUN_DIR,
				sum(if(month(`date`)='7' ,direct_labour,0)) AS JUL_DIR,
				sum(if(month(`date`)='8' ,direct_labour,0)) AS AGU_DIR,
				sum(if(month(`date`)='9' ,direct_labour,0)) AS SEP_DIR,
				sum(if(month(`date`)='10' ,direct_labour,0)) AS OKT_DIR,
				sum(if(month(`date`)='11' ,direct_labour,0)) AS NOV_DIR,
				sum(if(month(`date`)='12' ,direct_labour,0)) AS DES_DIR,
				sum(direct_labour) as SUM_DIR,
				sum(if(month(`date`)='1' ,indirect_labour,0)) AS JAN_IND,
				sum(if(month(`date`)='2' ,indirect_labour,0)) AS FEB_IND,
				sum(if(month(`date`)='3' ,indirect_labour,0)) AS MAR_IND,
				sum(if(month(`date`)='4' ,indirect_labour,0)) AS APR_IND,
				sum(if(month(`date`)='5' ,indirect_labour,0)) AS MEI_IND,
				sum(if(month(`date`)='6' ,indirect_labour,0)) AS JUN_IND,
				sum(if(month(`date`)='7' ,indirect_labour,0)) AS JUL_IND,
				sum(if(month(`date`)='8' ,indirect_labour,0)) AS AGU_IND,
				sum(if(month(`date`)='9' ,indirect_labour,0)) AS SEP_IND,
				sum(if(month(`date`)='10' ,indirect_labour,0)) AS OKT_IND,
				sum(if(month(`date`)='11' ,indirect_labour,0)) AS NOV_IND,
				sum(if(month(`date`)='12' ,indirect_labour,0)) AS DES_IND,
				sum(indirect_labour) as SUM_IDN,
				sum(if(month(`date`)='1' ,consumable,0)) AS JAN_CON,
				sum(if(month(`date`)='2' ,consumable,0)) AS FEB_CON,
				sum(if(month(`date`)='3' ,consumable,0)) AS MAR_CON,
				sum(if(month(`date`)='4' ,consumable,0)) AS APR_CON,
				sum(if(month(`date`)='5' ,consumable,0)) AS MEI_CON,
				sum(if(month(`date`)='6' ,consumable,0)) AS JUN_CON,
				sum(if(month(`date`)='7' ,consumable,0)) AS JUL_CON,
				sum(if(month(`date`)='8' ,consumable,0)) AS AGU_CON,
				sum(if(month(`date`)='9' ,consumable,0)) AS SEP_CON,
				sum(if(month(`date`)='10' ,consumable,0)) AS OKT_CON,
				sum(if(month(`date`)='11' ,consumable,0)) AS NOV_CON,
				sum(if(month(`date`)='12' ,consumable,0)) AS DES_CON,
				sum(consumable) as SUM_CON,
				sum(if(month(`date`)='1' ,machine,0)) AS JAN_MCH,
				sum(if(month(`date`)='2' ,machine,0)) AS FEB_MCH,
				sum(if(month(`date`)='3' ,machine,0)) AS MAR_MCH,
				sum(if(month(`date`)='4' ,machine,0)) AS APR_MCH,
				sum(if(month(`date`)='5' ,machine,0)) AS MEI_MCH,
				sum(if(month(`date`)='6' ,machine,0)) AS JUN_MCH,
				sum(if(month(`date`)='7' ,machine,0)) AS JUL_MCH,
				sum(if(month(`date`)='8' ,machine,0)) AS AGU_MCH,
				sum(if(month(`date`)='9' ,machine,0)) AS SEP_MCH,
				sum(if(month(`date`)='10' ,machine,0)) AS OKT_MCH,
				sum(if(month(`date`)='11' ,machine,0)) AS NOV_MCH,
				sum(if(month(`date`)='12' ,machine,0)) AS DES_MCH,
				sum(machine) as SUM_MCH,
				sum(if(month(`date`)='1' ,mould_mandrill,0)) AS JAN_MM,
				sum(if(month(`date`)='2' ,mould_mandrill,0)) AS FEB_MM,
				sum(if(month(`date`)='3' ,mould_mandrill,0)) AS MAR_MM,
				sum(if(month(`date`)='4' ,mould_mandrill,0)) AS APR_MM,
				sum(if(month(`date`)='5' ,mould_mandrill,0)) AS MEI_MM,
				sum(if(month(`date`)='6' ,mould_mandrill,0)) AS JUN_MM,
				sum(if(month(`date`)='7' ,mould_mandrill,0)) AS JUL_MM,
				sum(if(month(`date`)='8' ,mould_mandrill,0)) AS AGU_MM,
				sum(if(month(`date`)='9' ,mould_mandrill,0)) AS SEP_MM,
				sum(if(month(`date`)='10' ,mould_mandrill,0)) AS OKT_MM,
				sum(if(month(`date`)='11' ,mould_mandrill,0)) AS NOV_MM,
				sum(if(month(`date`)='12' ,mould_mandrill,0)) AS DES_MM,
				sum(mould_mandrill) as SUM_MM,
				sum(if(month(`date`)='1' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS JAN_FOH,
				sum(if(month(`date`)='2' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS FEB_FOH,
				sum(if(month(`date`)='3' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS MAR_FOH,
				sum(if(month(`date`)='4' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS APR_FOH,
				sum(if(month(`date`)='5' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS MEI_FOH,
				sum(if(month(`date`)='6' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS JUN_FOH,
				sum(if(month(`date`)='7' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS JUL_FOH,
				sum(if(month(`date`)='8' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS AGU_FOH,
				sum(if(month(`date`)='9' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS SEP_FOH,
				sum(if(month(`date`)='10' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS OKT_FOH,
				sum(if(month(`date`)='11' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS NOV_FOH,
				sum(if(month(`date`)='12' ,foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan,0)) AS DES_FOH,
				sum(foh_consumable + foh_depresiasi + biaya_gaji_non_produksi + biaya_non_produksi + biaya_rutin_bulanan) AS SUM_FOH
			FROM
				laporan_per_bulan
			WHERE 
				YEAR(`date`)='".$thn."'
		";
		//echo $sql;
		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function rpt_report_revenue_json_chart1(){
		// print_r($_POST); exit;
		$requestData	= $_REQUEST;
		$fetch			= $this->qry_report_revenue_chart();
		// $fetch			= $this->qry_report_revenue_chart();
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		
		$data			= array();
		$urut1  = 1;
        $urut2  = 0;
		$Data_Header	= $query->result_array();
		// print_r($Data_Header); exit;
		$Arr_Label		= array('Month');
		$Arr_Nilai		= array();
		
		$Arr_Bulan		= array(1=>'JAN','FEB','MAR','APR','MEI','JUN','JUL','AGU','SEP','OKT','NOV','DES');
		foreach($Data_Header as $keyH=>$valH){
			$Actual		= "Actual Material (".number_format($valH['SUM_MAT']).") _____________";
			$Direct		= "Direct (".number_format($valH['SUM_DIR']).")";
			$Indirect	= "Indirect (".number_format($valH['SUM_IDN']).")";
			$Consumable	= "Consumable (".number_format($valH['SUM_CON']).")";
			$Machine	= "Machine (".number_format($valH['SUM_MCH']).")";
			$MM			= "Mould & Mandril (".number_format($valH['SUM_MM']).")";
			$FOH	= "FOH (".number_format($valH['SUM_FOH']).")";

			array_push($Arr_Label, $Actual, $Direct, $Indirect, $Consumable, $Machine, $MM, $FOH);
			//$Arr_Label[]	= $Tujuan;
			foreach($Arr_Bulan as $keyB=>$valB){
				$Arr_Nilai[$keyB][0]	= $valB;
				$Arr_Nilai[$keyB][]	= floatval($valH[$valB."_MAT"]); 
				$Arr_Nilai[$keyB][]	= floatval($valH[$valB."_DIR"]);
				$Arr_Nilai[$keyB][]	= floatval($valH[$valB."_IND"]);
				$Arr_Nilai[$keyB][]	= floatval($valH[$valB."_CON"]);
				$Arr_Nilai[$keyB][]	= floatval($valH[$valB."_MCH"]);
				$Arr_Nilai[$keyB][]	= floatval($valH[$valB."_MM"]);
				$Arr_Nilai[$keyB][]	= floatval($valH[$valB."_FOH"]);
			}
		}
		// print_r($Arr_Nilai); exit;
		$Arr_Akhir			= array();
		$Arr_Akhir[0]		= $Arr_Label;
		// print_r($Arr_Akhir); exit;
		foreach($Arr_Nilai as $keyN=>$valN){
			array_push($Arr_Akhir, $valN);
			//$Arr_Akhir[]		= $valN;
		}
		// print_r($Arr_Akhir); exit;
		echo json_encode($Arr_Akhir);
		
	}
	
	public function excel_late_project(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

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
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'LATE PROJECT');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'IPP Date');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'IPP Finish');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Late Day');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Status');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		

		$sql 	= "SELECT a.* FROM late_project a ORDER BY a.nm_customer ASC, a.no_ipp ASC";
		$result	= $this->db->query($sql)->result_array();
		// echo $qMatr; exit;
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
			
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_ipp		= $row_Cek['no_ipp'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_customer	= $row_Cek['nm_customer'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$project	= strtoupper($row_Cek['project']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$ipp_date	= $row_Cek['ipp_date'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ipp_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$finish_date	= $row_Cek['finish_date'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $finish_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$selisih	= $row_Cek['selisih'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $selisih);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$status	= $row_Cek['status'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
			
			
			
		}
		
		
		$sheet->setTitle('Late Project');
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
		header('Content-Disposition: attachment;filename="Late_project_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function excel_ipp_by_sales(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

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
		$Col_Akhir	= $Cols	= getColsChar(6);
		$sheet->setCellValue('A'.$Row, 'IPP TERBIT DARI SALES');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'IPP Date');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Status');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sql 	= "SELECT a.* FROM production a WHERE a.status <> 'CANCELED' AND status <> 'WAITING IPP RELEASE'  ORDER BY a.nm_customer ASC, a.no_ipp ASC";
		$result	= $this->db->query($sql)->result_array();
		// echo $qMatr; exit;
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
			
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_ipp		= $row_Cek['no_ipp'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_customer	= $row_Cek['nm_customer'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$project	= strtoupper($row_Cek['project']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$ipp_date	= date('d-m-Y', strtotime($row_Cek['created_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ipp_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$status	= $row_Cek['status'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
			
			
			
		}
		
		
		$sheet->setTitle('IPP Terbit Sales');
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
		header('Content-Disposition: attachment;filename="Ipp_terbit_by_sales_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function excel_ipp_est_by_eng(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

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
		$Col_Akhir	= $Cols	= getColsChar(6);
		$sheet->setCellValue('A'.$Row, 'IPP SUDAH DIBUAT ESTIMASI OLEH ENGINEERING');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'IPP Date');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Status');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sql 	= "SELECT a.* FROM production a WHERE (
													status <> 'WAITING STRUCTURE BQ' 
													AND status <> 'CANCELED' 
													AND status <> 'WAITING ESTIMATION PROJECT'
													AND status <> 'WAITING APPROVE STRUCTURE BQ' 
													AND status <> 'WAITING APPROVE EST PROJECT' 
													AND status <> 'WAITING IPP RELEASE' 
													) ORDER BY a.nm_customer ASC, a.no_ipp ASC";
		$result	= $this->db->query($sql)->result_array();
		// echo $qMatr; exit;
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
			
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_ipp		= $row_Cek['no_ipp'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_customer	= $row_Cek['nm_customer'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$project	= strtoupper($row_Cek['project']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$ipp_date	= date('d-m-Y', strtotime($row_Cek['created_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ipp_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$status	= $row_Cek['status'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
			
			
			
		}
		
		
		$sheet->setTitle('IPP sudah estimasi oleh Eng');
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
		header('Content-Disposition: attachment;filename="Ipp_est_eng_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function excel_ipp_costing(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

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
		$Col_Akhir	= $Cols	= getColsChar(6);
		$sheet->setCellValue('A'.$Row, 'IPP PROCESS COSTING');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'IPP Date');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Status');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sql 	= "SELECT a.* FROM production a WHERE a.status = 'WAITING EST PRICE PROJECT' ORDER BY a.nm_customer ASC, a.no_ipp ASC";
		$result	= $this->db->query($sql)->result_array();
		// echo $qMatr; exit;
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
			
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_ipp		= $row_Cek['no_ipp'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_customer	= $row_Cek['nm_customer'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$project	= strtoupper($row_Cek['project']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$ipp_date	= date('d-m-Y', strtotime($row_Cek['created_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ipp_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$status	= $row_Cek['status'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
			
			
			
		}
		
		
		$sheet->setTitle('IPP Process Costing');
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
		header('Content-Disposition: attachment;filename="Ipp_process_costing_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function excel_ipp_terbit_ke_sales(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

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
		$Col_Akhir	= $Cols	= getColsChar(6);
		$sheet->setCellValue('A'.$Row, 'IPP TERBIT KE SALES');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'IPP Date');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Status');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sql 	= "SELECT a.* FROM production a LEFT JOIN color_status b ON a.status=b.status WHERE urut >= '6' AND a.status <> 'CANCELED' ORDER BY a.nm_customer ASC, a.no_ipp ASC";
		$result	= $this->db->query($sql)->result_array();
		// echo $qMatr; exit;
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
			
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_ipp		= $row_Cek['no_ipp'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_customer	= $row_Cek['nm_customer'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$project	= strtoupper($row_Cek['project']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$ipp_date	= date('d-m-Y', strtotime($row_Cek['created_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ipp_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$status	= $row_Cek['status'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
			
			
			
		}
		
		
		$sheet->setTitle('IPP Terbit ke Sales');
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
		header('Content-Disposition: attachment;filename="Ipp_terbit_ke_sales_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function excel_ipp_quotation(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

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
		$Col_Akhir	= $Cols	= getColsChar(6);
		$sheet->setCellValue('A'.$Row, 'IPP BELUM DIBUAT QUOTATION');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'IPP Date');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Status');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sql 	= "SELECT a.* FROM production a WHERE a.status = 'ALREADY ESTIMATED PRICE' ORDER BY a.nm_customer ASC, a.no_ipp ASC";
		$result	= $this->db->query($sql)->result_array();
		// echo $qMatr; exit;
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
			
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_ipp		= $row_Cek['no_ipp'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$nm_customer	= $row_Cek['nm_customer'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$project	= strtoupper($row_Cek['project']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$ipp_date	= date('d-m-Y', strtotime($row_Cek['created_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ipp_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$status	= $row_Cek['status'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
			
			
			
		}
		
		
		$sheet->setTitle('IPP belum Quotation');
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
		header('Content-Disposition: attachment;filename="Ipp_belum_quotation_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function price_reference() {
		//$this->load->view('include/header', array('title'=>'Dashboard'));
		// history('View Dashboard');

		$sum_material 		= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND id_material NOT IN ('MTL-2009001','MTL-2009002','MTL-2105003','MTL-1903000') ")->num_rows();
		$waiting_approval 	= $this->db->query("SELECT * FROM raw_materials WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data 		= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND id_material NOT IN ('MTL-2009001','MTL-2009002','MTL-2105003','MTL-1903000') ")->result();
		
		$expired = [];
		$hampir_exp = [];
		$price_oke = [];
			
		foreach($filter_data as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->exp_price_ref_est;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke[] = $datas->id_material;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp[] = $datas->id_material;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired[] = $datas->id_material;
			}
		}

		//Aksesoris
		$sum_material_acc 		= $this->db->query("SELECT * FROM accessories WHERE `deleted` = 'N' ")->num_rows();
		$waiting_approval_acc 	= $this->db->query("SELECT * FROM accessories WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_acc 		= $this->db->query("SELECT * FROM accessories WHERE `deleted` = 'N' ")->result();
		
		$expired_acc = [];
		$hampir_exp_acc = [];
		$price_oke_acc = [];
			
		foreach($filter_data_acc as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->exp_price_ref_est;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_acc[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_acc[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_acc[] = $datas->id;
			}
		}
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard Price Reference',
			'sum_material'		=> $sum_material,
			'waiting_approval' 	=> $waiting_approval,
			'expired'			=> COUNT($expired),
			'hampir_exp'		=> COUNT($hampir_exp),
			'price_oke'			=> COUNT($price_oke),
			'sum_material_acc'		=> $sum_material_acc,
			'waiting_approval_acc' 	=> $waiting_approval_acc,
			'expired_acc'			=> COUNT($expired_acc),
			'hampir_exp_acc'		=> COUNT($hampir_exp_acc),
			'price_oke_acc'			=> COUNT($price_oke_acc)
		);
		history('Look dashboard price reference');
		$this->load->view('dashboard_price_ref',$data);
		
	}
	
	public function print_price_ref(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$sql_d 			= "SELECT * FROM raw_materials WHERE `delete`='N' AND flag_active = 'Y' ORDER BY id_category, nm_material ";
		$rest_d			= $this->db->query($sql_d)->result_array();
		
		$sts_val = "";
		if($status == 'all'){
			$sts_val = "ALL MATERIAL";
		}
		if($status == 'oke'){
			$sts_val = "OKE";
		}
		if($status == 'less'){
			$sts_val = "LESS ONE WEEK";
		}
		if($status == 'expired'){
			$sts_val = "EXPIRED";
		}
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'status' => $status,
			'rest_d' => $rest_d,
			'sts_val' => $sts_val,
			'status' => $status
		);
		history('Print price reference by dashboard status '.$status); 
		$this->load->view('Print/print_price_ref', $data);
	}

	public function print_price_ref_acc(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$sql_d 			= "SELECT a.*, UPPER(b.category) AS nm_category FROM accessories a LEFT JOIN accessories_category b ON a.category=b.id WHERE a.deleted='N' ORDER BY b.category ASC, a.nama ASC";
		$rest_d			= $this->db->query($sql_d)->result_array();
		
		$sts_val = "";
		if($status == 'all'){
			$sts_val = "ALL MATERIAL";
		}
		if($status == 'oke'){
			$sts_val = "OKE";
		}
		if($status == 'less'){
			$sts_val = "LESS ONE WEEK";
		}
		if($status == 'expired'){
			$sts_val = "EXPIRED";
		}
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'status' => $status,
			'rest_d' => $rest_d,
			'sts_val' => $sts_val,
			'status' => $status
		);
		history('Print price reference accessories by dashboard status '.$status); 
		$this->load->view('Print/print_price_ref_acc', $data);
	}

	public function print_price_ref_trans(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$sql_d 			= "	SELECT
								a.*,
								b.nama_truck
							FROM
								cost_trucking a 
								LEFT JOIN truck b ON a.id_truck=b.id";
		$rest_d			= $this->db->query($sql_d)->result_array();
		
		$transport_export = $this->db
									->select('a.*, b.country_name')
									->from('cost_export_trans a')
									->join('country b','a.country_code=b.country_code','left')
									->where("a.deleted = 'N' ".$where)
									->get()
									->result_array();

		$sts_val = "";
		if($status == 'all'){
			$sts_val = "ALL EXPORT & LOCAL";
		}
		if($status == 'oke'){
			$sts_val = "OKE";
		}
		if($status == 'less'){
			$sts_val = "LESS ONE WEEK";
		}
		if($status == 'expired'){
			$sts_val = "EXPIRED";
		}
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'status' => $status,
			'rest_d' => $rest_d,
			'transport_export' => $transport_export,
			'sts_val' => $sts_val,
			'status' => $status
		);
		history('Print price reference transport by dashboard status '.$status); 
		$this->load->view('Print/print_price_ref_trans', $data);
	}

	public function print_price_ref_rutin(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$rest_d = $this->db
					->select('	a.id,
								a.code_group,
								b.material_name,
								b.spec,
								b.brand,
								a.unit_material AS unit,
								a.kurs,
								a.expired_supplier,
								a.expired_purchase,
								a.expired,
								a.app_price_sup,
								a.price_supplier,
								a.price_purchase,
								a.rate,
								a.reject_ket,
								a.updated_date,
								a.rate')
					->from('con_nonmat_new b')
					->join('price_ref a','a.code_group=b.code_group','left')
					->where('b.code_group LIKE','CN%')
					->where('b.category_awal !=','9')
					->where('a.category','consumable')
					->where('a.sts_price','N')
					->where("a.deleted = 'N' ")
					->order_by('b.material_name','asc')
					->order_by('b.spec','asc')
					->order_by('b.brand','asc')
					->get()
					->result_array();
		
		$sts_val = "";
		if($status == 'all'){
			$sts_val = "ALL MATERIAL RUTIN";
		}
		if($status == 'oke'){
			$sts_val = "OKE";
		}
		if($status == 'less'){
			$sts_val = "LESS ONE WEEK";
		}
		if($status == 'expired'){
			$sts_val = "EXPIRED";
		}
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'status' => $status,
			'rest_d' => $rest_d,
			'sts_val' => $sts_val,
			'status' => $status
		);
		history('Print price reference rutin by dashboard status '.$status); 
		$this->load->view('Print/print_price_ref_rutin', $data);
	}

	//DASHBOARD PROCESS
	public function dashboard_process() {
		//FINANCE
		$sum_material 		= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND flag_active = 'Y' ")->num_rows();
		$waiting_approval 	= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND flag_active = 'Y' AND app_price_sup = 'Y'")->num_rows();
		$filter_data 		= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND flag_active = 'Y' ")->result();
		
		$expired = [];
		$hampir_exp = [];
		$price_oke = [];
			
		foreach($filter_data as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->exp_price_ref_est;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke[] = $datas->id_material;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp[] = $datas->id_material;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired[] = $datas->id_material;
			}
		}

		//Aksesoris
		$sum_material_acc 		= $this->db->query("SELECT * FROM accessories WHERE `deleted` = 'N' ")->num_rows();
		$waiting_approval_acc 	= $this->db->query("SELECT * FROM accessories WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_acc 		= $this->db->query("SELECT * FROM accessories WHERE `deleted` = 'N' ")->result();
		
		$expired_acc = [];
		$hampir_exp_acc = [];
		$price_oke_acc = [];
			
		foreach($filter_data_acc as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->exp_price_ref_est;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_acc[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_acc[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_acc[] = $datas->id;
			}
		}

		//Transport
		$sum_material_trans 		= $this->db->query("SELECT * FROM cost_trucking ")->num_rows();
		$waiting_approval_trans 	= $this->db->query("SELECT * FROM cost_trucking WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_trans 		= $this->db->query("SELECT * FROM cost_trucking ")->result();
		
		$expired_trans = [];
		$hampir_exp_trans = [];
		$price_oke_trans = [];
			
		foreach($filter_data_trans as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->expired;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_trans[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_trans[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_trans[] = $datas->id;
			}
		}

		//eksport
		$sum_material_trans2 		= $this->db->query("SELECT * FROM cost_export_trans WHERE deleted='N' ")->num_rows();
		$waiting_approval_trans2 	= $this->db->query("SELECT * FROM cost_export_trans WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_trans2 		= $this->db->query("SELECT * FROM cost_export_trans WHERE deleted='N' ")->result();
		
		$expired_trans2 = [];
		$hampir_exp_trans2 = [];
		$price_oke_trans2 = [];
			
		foreach($filter_data_trans2 as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->expired;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_trans2[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_trans2[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_trans2[] = $datas->id;
			}
		}

		//Rutin
		$sum_material_rutin 		= $this->db->query("SELECT * FROM price_ref WHERE `deleted` = 'N' ")->num_rows();
		$waiting_approval_rutin 	= $this->db->query("SELECT * FROM price_ref WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_rutin 		= $this->db->query("SELECT * FROM price_ref WHERE `deleted` = 'N' ")->result();
		
		$expired_rutin = [];
		$hampir_exp_rutin = [];
		$price_oke_rutin = [];
			
		foreach($filter_data_rutin as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->expired;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_rutin[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_rutin[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_rutin[] = $datas->id;
			}
		}

		$list_expired = $this->db
								->select('
									a.nm_material,
									SUM(a.qty_stock) AS stock,
									a.expired,
									a.id_gudang
									')
								->from('warehouse_stock_expired a')
								->join('raw_materials b', 'a.id_material=b.id_material','join')
								->where('b.id_category','TYP-0001')
								->where('a.id_material <>','MTL-2105003')
								->where('a.qty_stock >',0)
								->group_by('a.id_material, a.expired, a.id_gudang')
								->order_by('a.nm_material','asc')
								->order_by('a.expired','asc')
								->get()
								->result_array();

		$ttl_inv = $this->db->query("SELECT sum(total_invoice) ttl_inv from ( SELECT count(id_invoice) total_invoice FROM tr_invoice_header where year(tgl_invoice)='".date("Y")."'
		union
		select count(id_invoice) from tr_invoice_np_header where status=1 and year(tgl_invoice)='".date("Y")."'
		) ttl_inv")->row(); 
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard Process',

			'ttl_inv'		=> $ttl_inv,
			'sum_material'		=> $sum_material,
			'waiting_approval' 	=> $waiting_approval,
			'expired'			=> COUNT($expired),
			'hampir_exp'		=> COUNT($hampir_exp),
			'price_oke'			=> COUNT($price_oke),

			'sum_material_acc'		=> $sum_material_acc,
			'waiting_approval_acc' 	=> $waiting_approval_acc,
			'expired_acc'			=> COUNT($expired_acc),
			'hampir_exp_acc'		=> COUNT($hampir_exp_acc),
			'price_oke_acc'			=> COUNT($price_oke_acc),

			'sum_material_trans'		=> $sum_material_trans + $sum_material_trans2,
			'waiting_approval_trans' 	=> $waiting_approval_trans + $waiting_approval_trans2,
			'expired_trans'				=> COUNT($expired_trans) + COUNT($expired_trans2),
			'hampir_exp_trans'			=> COUNT($hampir_exp_trans) + COUNT($hampir_exp_trans2),
			'price_oke_trans'			=> COUNT($price_oke_trans) + COUNT($price_oke_trans2),

			'sum_material_rutin'		=> $sum_material_rutin,
			'waiting_approval_rutin' 	=> $waiting_approval_rutin,
			'expired_rutin'				=> COUNT($expired_rutin),
			'hampir_exp_rutin'			=> COUNT($hampir_exp_rutin),
			'price_oke_rutin'			=> COUNT($price_oke_rutin),


			'late_enggenering'	=> $this->api_model->api_late_enginnering_count(),
			'late_costing'		=> $this->api_model->api_late_costing_count(),
			'late_quotation'	=> $this->api_model->api_late_quotation_count(),
			'total_quotation'	=> $this->api_model->api_total_quotation_count(),
			'total_so'			=> $this->api_model->api_total_so_count(),
			'api_app_bq'		=> COUNT($this->api_model->api_app_bq()),
			'api_app_est'		=> COUNT($this->api_model->api_app_est()),
			'api_app_est_fd'	=> COUNT($this->api_model->api_app_est_fd()),
			'api_app_est_fd_parsial'	=> COUNT($this->api_model->api_app_est_fd_parsial()),
			'list_category'		=> $this->db->get_where('raw_categories', array('flag_active'=>'Y'))->result_array(),
			'list_expired'	=> $list_expired
		);
		history('Look dashboard process');
		$this->load->view('dashboard_process',$data);
		
	}

	public function print_late_eng(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'data' => $this->api_model->api_late_enginnering()
		);
		history('Print late enginnering'); 
		$this->load->view('Print/print_late_eng', $data);
	}

	public function print_late_cos(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'data' => $this->api_model->api_late_costing()
		);
		history('Print late costing'); 
		$this->load->view('Print/print_late_cos', $data);
	}

	public function print_total_quotation(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result = $this->db
                    ->select('  a.no_ipp, 
                                a.nm_customer, 
                                a.project, 
                                a.status, 
                                b.app_quo_date
                                ')
                    ->from('production a')
                    ->join('bq_header b','a.no_ipp = b.no_ipp AND b.app_quo = "Y"','join')
                    ->where('a.sts_hide','N')
                    ->where('YEAR(b.app_quo_date)',date('Y'))
                    ->order_by('b.app_quo_date','asc')
                    ->get()
                    ->result_array();
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'result' => $result
		);

		$this->load->view('Print/print_total_quotation', $data);
	}

	public function print_total_sales_order(){
		$status			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result = $this->db
                    ->select('a.total_deal_usd, a.no_ipp, a.project, a.nm_customer, b.approved_date')
                    ->from('billing_so a')
					->join('so_bf_header b','a.no_ipp=b.no_ipp AND b.approved = "Y"','join')
                    ->where('YEAR(b.approved_date)',date('Y'))
					->order_by('b.approved_date','asc')
                    ->get()
                    ->result_array();
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'result' => $result
		);

		$this->load->view('Print/print_total_sales_order', $data);
	}
	

	//PURCAHSE
	public function purchase() {
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard Purchase'
		);
		
		$this->load->view('dashboard_purchase',$data);
		
	}

	public function getPurchase() {
		$Arr_Kembali	= array();

		//OUTSTANDING PR
		$dataOutandingPRMaterial 	= $this->db->get_where('approval_pr', array('sts_app'=>'N','no_pr'=>null))->result_array();
		$dataOutandingPRStok 		= $this->db->group_by('no_pengajuan_group')->get_where('rutin_planning_header', array('sts_app'=>'N'))->result_array();
		$dataOutandingPRDepartment 	= $this->db->get_where('rutin_non_planning_header', array('sts_app'=>'N'))->result_array();
		$dataOutandingPRAsset 		= $this->db->get_where('asset_planning', array('status'=>'N'))->result_array();
		//PR APPROVED WAITING PROCESS
		$dataAppWaitPrsPRMaterial 	= $this->db->group_by('no_pr')->get_where('tran_material_pr_detail', array('no_rfq'=>NULL,'category'=>'mat'))->result_array();
		$dataAppWaitPrsPRStok 		= $this->db->group_by('no_pr_group')->get_where('tran_pr_detail', array('no_rfq'=>NULL,'category'=>'rutin'))->result_array();
		$dataAppWaitPrsPRDepartment = $this->db->group_by('no_pr_group')->get_where('tran_pr_detail', array('no_rfq'=>NULL,'category'=>'non rutin'))->result_array();
		$dataAppWaitPrsPRAsset 		= $this->db->get_where('asset_planning', array('no_pr'=>NULL,'status'=>'Y'))->result_array();
		//COMPARISON SUPPLIER PROCESS
		$dataCompSupPrsPRMaterial 	= $this->db->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','c.sts_ajuan'=>'PRS'))->result_array();
		$dataCompSupPrsPRStok 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','c.sts_ajuan'=>'PRS'))->result_array();
		$dataCompSupPrsPRDepartment = $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','c.sts_ajuan'=>'PRS'))->result_array();
		$dataCompSupPrsPRAsset 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','c.sts_ajuan'=>'PRS'))->result_array();
		//OUTANDING COMPARIOSN APPROVAL
		$dataOutCompAppPRMaterial 	= $this->db->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat'))->result_array();
		$dataOutCompAppPRStok 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin'))->result_array();
		$dataOutCompAppPRDepartment = $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin'))->result_array();
		$dataOutCompAppPRAsset 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset'))->result_array();
		//OUTANDING PROCESS PO
		$dataOutPrsPOPRMaterial 	= $this->db->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.no_po'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		$dataOutPrsPOPRStok 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.no_po'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		$dataOutPrsPOPRDepartment 	= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.no_po'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		$dataOutPrsPOPRAsset 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.no_po'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		//WAITING APPROVAL PO
		$dataWaitAppPOPRMaterial 	= $this->db->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_po_header c','b.no_po=c.no_po','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		$dataWaitAppPOPRStok 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		$dataWaitAppPOPRDepartment 	= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		$dataWaitAppPOPRAsset 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		//RELEASE PO
		$dataReleasePOPRMaterial 	= $this->db->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_po_header c','b.no_po=c.no_po','left')->join('tran_material_po_detail d','b.id_material=d.id_material','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		$dataReleasePOPRStok 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		$dataReleasePOPRDepartment 	= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		$dataReleasePOPRAsset 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		//INCOMING PO
		$dataIncomingPOPRMaterial 	= $this->db->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_po_header c','b.no_po=c.no_po','left')->join('tran_material_po_detail d','b.id_material=d.id_material','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		$dataIncomingPOPRStok 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		$dataIncomingPOPRDepartment = $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		$dataIncomingPOPRAsset 		= $this->db->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		
		$dashboard = "";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>Outstanding Approval PR</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialOutAppPR' data-type='material'>".number_format(COUNT($dataOutandingPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialOutAppPR' data-type='stok'>".number_format(COUNT($dataOutandingPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialOutAppPR' data-type='department'>".number_format(COUNT($dataOutandingPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialOutAppPR' data-type='asset'>".number_format(COUNT($dataOutandingPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>PR Approved, Waiting Process</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialPRApp' data-type='material'>".number_format(COUNT($dataAppWaitPrsPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialPRApp' data-type='stok'>".number_format(COUNT($dataAppWaitPrsPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialPRApp' data-type='department'>".number_format(COUNT($dataAppWaitPrsPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialPRApp' data-type='asset'>".number_format(COUNT($dataAppWaitPrsPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>Comparison Supplier Process</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialCompSup' data-type='material'>".number_format(COUNT($dataCompSupPrsPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialCompSup' data-type='stok'>".number_format(COUNT($dataCompSupPrsPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialCompSup' data-type='department'>".number_format(COUNT($dataCompSupPrsPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detialCompSup' data-type='asset'>".number_format(COUNT($dataCompSupPrsPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>Outstanding Comparison Approval</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutCompApp' data-type='material'>".number_format(COUNT($dataOutCompAppPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutCompApp' data-type='stok'>".number_format(COUNT($dataOutCompAppPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutCompApp' data-type='department'>".number_format(COUNT($dataOutCompAppPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutCompApp' data-type='asset'>".number_format(COUNT($dataOutCompAppPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>Outstanding Process PO</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutPrsPO' data-type='material'>".number_format(COUNT($dataOutPrsPOPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutPrsPO' data-type='stok'>".number_format(COUNT($dataOutPrsPOPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutPrsPO' data-type='department'>".number_format(COUNT($dataOutPrsPOPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailOutPrsPO' data-type='asset'>".number_format(COUNT($dataOutPrsPOPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>Waiting Approval PO</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailWaitAppPO' data-type='material'>".number_format(COUNT($dataWaitAppPOPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailWaitAppPO' data-type='stok'>".number_format(COUNT($dataWaitAppPOPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailWaitAppPO' data-type='department'>".number_format(COUNT($dataWaitAppPOPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailWaitAppPO' data-type='asset'>".number_format(COUNT($dataWaitAppPOPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>Release PO</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailReleasePO' data-type='material'>".number_format(COUNT($dataReleasePOPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailReleasePO' data-type='stok'>".number_format(COUNT($dataReleasePOPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailReleasePO' data-type='department'>".number_format(COUNT($dataReleasePOPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailReleasePO' data-type='asset'>".number_format(COUNT($dataReleasePOPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		$dashboard .= "<tr>";
			$dashboard .= "<td class='text-left'>Incoming PO</td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailIncomingPO' data-type='material''>".number_format(COUNT($dataIncomingPOPRMaterial))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailIncomingPO' data-type='stok''>".number_format(COUNT($dataIncomingPOPRStok))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailIncomingPO' data-type='department''>".number_format(COUNT($dataIncomingPOPRDepartment))."</span></td>";
			$dashboard .= "<td class='text-right'><span class='text-primary text-bold detailIncomingPO' data-type='asset''>".number_format(COUNT($dataIncomingPOPRAsset))."</span></td>";
		$dashboard .= "</tr>";
		// echo $dashboard; exit;
		$Arr_Kembali	= array(
			'status'	=> 1,
			'dashboard'	=> $dashboard
		);
		echo json_encode($Arr_Kembali);
	}

	public function detialOutAppPR($type){
		if($type == 'material'){
			$detail 	= $this->db->select('no_ipp AS no_pr')->get_where('approval_pr', array('sts_app'=>'N','no_pr'=>null))->result_array();
		}
		if($type == 'stok'){
			$detail 	= $this->db->select('no_pengajuan_group AS no_pr')->group_by('no_pengajuan_group')->get_where('rutin_planning_header', array('sts_app'=>'N'))->result_array();
		}
		if($type == 'department'){
			$detail 	= $this->db->select('no_pengajuan AS no_pr')->get_where('rutin_non_planning_header', array('sts_app'=>'N'))->result_array();
		}
		if($type == 'asset'){
			$detail 	= $this->db->select('code_plan AS no_pr')->get_where('asset_planning', array('status'=>'N'))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function detialPRApp($type){
		if($type == 'material'){
			$detail 	= $this->db->select('no_pr AS no_pr')->group_by('no_pr')->get_where('tran_material_pr_detail', array('no_rfq'=>NULL,'category'=>'mat'))->result_array();
		}
		if($type == 'stok'){
			$detail 	= $this->db->select('no_pr_group AS no_pr')->group_by('no_pr_group')->get_where('tran_pr_detail', array('no_rfq'=>NULL,'category'=>'rutin'))->result_array();
		}
		if($type == 'department'){
			$detail 	= $this->db->select('no_pr_group AS no_pr')->group_by('no_pr_group')->get_where('tran_pr_detail', array('no_rfq'=>NULL,'category'=>'non rutin'))->result_array();
		}
		if($type == 'asset'){
			$detail 	= $this->db->select('code_plan AS no_pr')->get_where('asset_planning', array('no_pr'=>NULL,'status'=>'Y'))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function detialCompSup($type){
		if($type == 'material'){
			$detail 	= $this->db->select('a.no_pr AS no_pr')->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','c.sts_ajuan'=>'PRS'))->result_array();
		}
		if($type == 'stok'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','c.sts_ajuan'=>'PRS'))->result_array();
		}
		if($type == 'department'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','c.sts_ajuan'=>'PRS'))->result_array();
		}
		if($type == 'asset'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','c.sts_ajuan'=>'PRS'))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function detailOutCompApp($type){
		if($type == 'material'){
			$detail 	= $this->db->select('a.no_pr AS no_pr')->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat'))->result_array();
		}
		if($type == 'stok'){
			$detail 		= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin'))->result_array();
		}
		if($type == 'department'){
			$detail = $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin'))->result_array();
		}
		if($type == 'asset'){
			$detail 		= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('AJU','APV'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset'))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function detailOutPrsPO($type){
		if($type == 'material'){
			$detail = $this->db->select('a.no_pr AS no_pr')->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material AND b.no_po IS NULL','left')->join('tran_material_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		}
		if($type == 'stok'){
			$detail = $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang AND b.no_po IS NULL','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		}
		if($type == 'department'){
			$detail = $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang AND b.no_po IS NULL','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		}
		if($type == 'asset'){
			$detail = $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang AND b.no_po IS NULL','left')->join('tran_rfq_header c','c.no_rfq=b.no_rfq','left')->where_in('c.sts_ajuan',array('CLS'))->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.status'=>'SETUJU','b.status_apv'=>'SETUJU'))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function detailWaitAppPO($type){
		
		if($type == 'material'){
			$detail 	= $this->db->select('a.no_pr AS no_pr')->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_po_header c','b.no_po=c.no_po','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		}
		if($type == 'stok'){
			$detail 		= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		}
		if($type == 'department'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		}
		if($type == 'asset'){
			$detail 		= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'N','c.status2'=>'N'))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function detailReleasePO($type){
		
		if($type == 'material'){
			$detail 	= $this->db->select('a.no_pr AS no_pr')->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_po_header c','b.no_po=c.no_po','left')->join('tran_material_po_detail d','b.id_material=d.id_material','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		}
		if($type == 'stok'){
			$detail 		= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		}
		if($type == 'department'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		}
		if($type == 'asset'){
			$detail 		= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in <='=>0))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function detailIncomingPO($type){
		
		if($type == 'material'){
			$detail 	= $this->db->select('a.no_pr AS no_pr')->group_by('a.no_pr')->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')->join('tran_material_po_header c','b.no_po=c.no_po','left')->join('tran_material_po_detail d','b.id_material=d.id_material','left')->get_where('tran_material_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'mat','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		}
		if($type == 'stok'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		}
		if($type == 'department'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'non rutin','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		}
		if($type == 'asset'){
			$detail 	= $this->db->select('a.no_pr_group AS no_pr')->group_by('a.no_pr_group')->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')->join('tran_po_header c','b.no_po=c.no_po','left')->join('tran_po_detail d','b.id_barang=d.id_barang','left')->get_where('tran_pr_detail a', array('a.no_rfq <>'=>NULL,'a.category'=>'asset','b.no_po <>'=>NULL,'b.status'=>'SETUJU','b.status_apv'=>'SETUJU','c.status1'=>'Y','c.status2'=>'Y','d.qty_in >'=>0))->result_array();
		}
		$data = [
			'detail' => $detail
		];
		$this->load->view('Dashboard/detialOutAppPR', $data);
	}

	public function menu_session($id) {
		// $_SESSION["ses_level3"] = 0;	
		// $_SESSION["ses_level2"] = 0;	
		// $_SESSION["ses_level1"] = 0;

		$SESSION_LV3 = $id;
		$LV2 = get_menu('menus','parent_id','id',$id);
		$SESSION_LV2 = $LV2;
		$LV1 = get_menu('menus','parent_id','id',$LV2);
		$SESSION_LV1 = $LV1;

		$_SESSION["ses_level3"] = $SESSION_LV3;	
		$_SESSION["ses_level2"] = $SESSION_LV2;	
		$_SESSION["ses_level1"] = $SESSION_LV1;	

		$Arr_Return		= array(
			'status' => 'success',
			'ses_level1' => $SESSION_LV1,
			'ses_level2' => $SESSION_LV2,
			'ses_level3' => $SESSION_LV3,
		);
		echo json_encode($Arr_Return);
	}

	function list_invoice_dash(){
		$ttl_inv = $this->db->query("select * from (SELECT no_invoice,tgl_invoice,nm_customer,base_cur,total_invoice total_invoice_usd,total_invoice_idr, so_number,no_ipp, 'prod' jenis FROM tr_invoice_header where year(tgl_invoice)='".date("Y")."'
		union
		select no_invoice,tgl_invoice,nm_customer,base_cur,total_invoice_usd,total_invoice_idr,'NON PRODUCT' so_number,'NON PRODUCT' no_ipp,'nonp' jenis from tr_invoice_np_header where status=1 and year(tgl_invoice)='".date("Y")."')
		as list_invoice order by tgl_invoice
		")->result(); $i=0;
		echo "<table class='table'><tr><th>No</th><th>No Invoice</th><th>Tanggal Invoice</th><th>Customer</th><th>Nilai Invoice</th><th>No SO</th><th>No IPP</th></tr>";
		foreach ($ttl_inv as $keys=>$val){ $i++;
			echo "<tr><td>".$i."</td><td>".$val->no_invoice."</td><td>".date("d-m-Y", strtotime($val->tgl_invoice))."</td><td>".$val->nm_customer."</td>";
			if($val->base_cur=='IDR'){
				echo "<td align='right'>".$val->base_cur." ".number_format($val->total_invoice_idr)."</td>";
			}else{
				echo "<td align='right'>".$val->base_cur." ".number_format($val->total_invoice_usd)."</td>";
			}
			if($val->jenis=='prod'){
				echo "<td>".$val->so_number."</td>";
				echo "<td>".$val->no_ipp."</td>";
			}else{
				echo "<td colspan=2>".$val->so_number."</td>";
			}
			echo "</tr>";
		}
		echo "</table>
		<script>
			$(document).ready(function(){
				swal.close();
			});
		</script>
		";
		die();
	}
}
