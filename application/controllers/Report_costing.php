<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_costing extends CI_Controller { 
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		
		$this->load->database();
		// $this->load->library('Mpdf');
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

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$getBy				= "SELECT create_by, create_date FROM table_report_costing ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		
		$data = array(
			'title'			=> 'Report Costing',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy
		);
		history('View index report costing');
		$this->load->view('Report_costing/index',$data);
	}
	
	function insert_select(){ 
		$this->db->trans_start();
			$this->db->truncate('table_report_costing');
				
			$sqlUpdate = "
				INSERT INTO table_report_costing ( id_bq, no_ipp, estimasi, rev, order_type, nm_customer, sts_ipp, qty, est_harga, est_mat, direct_labour, indirect_labour, machine, mould_mandrill, consumable, process_cost, foh_consumable, foh_depresiasi, biaya_gaji_non_produksi, biaya_non_produksi, biaya_rutin_bulanan, project, create_by, create_date ) SELECT
					a.id_bq,
					a.no_ipp,
					a.estimasi,
					a.rev,
					a.order_type,
					a.nm_customer,
					a.sts_ipp,
					a.qty,
					a.est_harga,
					a.est_mat,
					a.direct_labour,
					a.indirect_labour,
					a.machine,
					a.mould_mandrill,
					a.consumable,
					a.process_cost,
					a.foh_consumable,
					a.foh_depresiasi,
					a.biaya_gaji_non_produksi,
					a.biaya_non_produksi,
					a.biaya_rutin_bulanan,
					a.project,
					'".$this->session->userdata['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
					FROM
						group_cost_project a";
			
			$this->db->query($sqlUpdate);
			
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
			history('Success insert select report costing');
		}
		echo json_encode($Arr_Data);
	}
	
	
	public function server_side_report_costing(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $uri_code	= $this->uri->segment(3);
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_report_costing(
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
			
			$get_est_kg 	= $this->db->select('est_material')->get_where('laporan_revised_header',array('id_bq'=>$row['id_bq'],'revised_no'=>$row['max_revisi']))->result();
			$get_est_cost 	= $this->db->select('est_harga')->get_where('laporan_revised_header',array('id_bq'=>$row['id_bq'],'revised_no'=>$row['max_revisi']))->result();
			$get_frp 		= $this->db->select('(est_harga+direct_labour+indirect_labour+machine+mould_mandrill+consumable+foh_consumable+foh_depresiasi+biaya_gaji_non_produksi+biaya_non_produksi+biaya_rutin_bulanan) AS cost_frp')->get_where('laporan_revised_header',array('id_bq'=>$row['id_bq'],'revised_no'=>$row['max_revisi']))->result();
			$get_non_frp 	= $this->db->select('SUM(price_total) AS cost_non_frp')->from('laporan_revised_etc')->where("id_bq='".$row['id_bq']."' AND revised_no='".$row['max_revisi']."' AND (category='aksesoris' OR category='baut' OR category='plate' OR category='gasket' OR category='lainnya') ")->get()->result();
			$get_packing 	= $this->db->select('SUM(price_total) AS cost_packing')->get_where('laporan_revised_etc',array('id_bq'=>$row['id_bq'],'revised_no'=>$row['max_revisi'],'category'=>'packing'))->result();
			$get_trucking 	= $this->db->select('SUM(price_total) AS cost_trucking')->from('laporan_revised_etc')->where("id_bq='".$row['id_bq']."' AND revised_no='".$row['max_revisi']."' AND (category='export' OR category='lokal') ")->get()->result();
			
			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_project'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($get_est_kg[0]->est_material, 3)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($get_est_cost[0]->est_harga, 2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($get_frp[0]->cost_frp, 2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($get_non_frp[0]->cost_non_frp, 2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($get_packing[0]->cost_packing, 2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($get_trucking[0]->cost_trucking, 2)."</div>";
				
				$summary_material	= "";
				$detail_material	= "";

				$summary_cost 		= "<a href='".site_url('report_costing/excel_summary_cost/'.$row['id_bq'])."' target='_blank' data-no_ipp='".$row['id_bq']."' class='btn btn-sm btn-warning' title='Detail Cost' data-role='qtip' style='margin-bottom:3px;'><i class='fa fa-file-excel-o'></i></a>";
				$detail_cost 		= "&nbsp;<a href='".site_url('report_costing/excel_report_costing/'.$row['id_bq'])."' target='_blank' data-no_ipp='".$row['id_bq']."' class='btn btn-sm btn-primary' title='Summary Costing' data-role='qtip' style='margin-bottom:3px;'><i class='fa fa-file-excel-o'></i></a>";
				// $summary_material	= "<button type='button' data-no_ipp='".$row['id_bq']."' class='btn btn-sm btn-success' title='Summary Material' data-role='qtip' style='margin-bottom:3px;'>Summary Material</button><br>";
				// $detail_material	= "<button type='button' data-no_ipp='".$row['id_bq']."' class='btn btn-sm btn-danger' title='Detail Material' data-role='qtip'>Detail Material</button><br>";
			$nestedData[]	= "<div align='center'>".$summary_cost.$detail_cost.$summary_material.$detail_material."</div>";
									
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

	public function query_report_costing($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id_bq,
				a.nm_customer,
				a.nm_project,
				MAX(a.revised_no) AS max_revisi
			FROM
				laporan_revised_header a,
				(SELECT @row:=0) r
		    WHERE 
				1=1
				AND (
				a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.id_bq
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'nm_customer',
			3 => 'nm_project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function excel_summary_cost(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$userID			= $data_session['ORI_User']['username'];

		$this->load->library("PHPExcel");
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
		
		$style_header3 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
		$Col_Akhir	= $Cols	= getColsChar(22);
		$sheet->setCellValue('A'.$Row, 'SUMMARY COST PROJECT '.str_replace('BQ-','',$id_bq));
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow2= $NewRow +1;
		
		$sheet->setCellValue("A".$NewRow."", 'PRODUCT');
		$sheet->getStyle("A".$NewRow.":V".$NextRow."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$NewRow.":V".$NextRow."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		$NextRow2= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'DAFTAR PRODUK');
		$sheet->getStyle('A'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'BREAKDOWN BERAT ESTIMASI');
		$sheet->getStyle('G'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'BREAKDOWN TOTAL COST');
		$sheet->getStyle('K'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'PROFIT ($)');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow2)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow2);
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue('Q'.$NewRow, 'TOTAL SELLING BEFORE ED ($)');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow2)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow2);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'ALLOWANCE ($)');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow2)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow2);
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'ED ($)');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow2)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow2);
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sheet->setCellValue('T'.$NewRow, 'INT & DISKONTO ($)');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow2)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow2);
		$sheet->getColumnDimension('T')->setAutoSize(true);
		
		$sheet->setCellValue('U'.$NewRow, 'TOTAL SELLING AFTER ED ($)');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow2)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow2);
		$sheet->getColumnDimension('U')->setAutoSize(true);
		
		$sheet->setCellValue('V'.$NewRow, 'SELLING PER KG ($)');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow2)->applyFromArray($style_header);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow2);
		$sheet->getColumnDimension('V')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'NO');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'PRODUCT');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'DIM 1');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'DIM 2');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'SPEC');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'QTY');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'RESIN (KG)');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'GLASS (KG)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'ADDITIVE (KG)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'TOTAL MATERIAL (KG)');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'RM ($)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'MP & UTILITIES ($)');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'FOH ($)');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'SALES & GA ($)');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'TOTAL COST ($)');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		
		$get_max_revisi = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		
		$sql 		= "	SELECT
							a.id_milik,
							a.product_parent AS product,
							a.qty,
							a.diameter AS diameter_1,
							a.diameter2 AS diameter_2,
							a.est_harga AS rm,
							(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS mp,
							(a.foh_consumable + a.foh_depresiasi) AS foh,
							(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS sales_ga,
							a.total_price AS total_profit,
							a.total_price_last AS total_allowance
						FROM
							laporan_revised_detail a
						WHERE
							a.product_parent <> 'pipe slongsong' 
							AND a.id_bq = '".$id_bq."' 
							AND a.revised_no = '".$get_max_revisi[0]->revised_no."' 
						ORDER BY a.id_milik ASC";
		$result		= $this->db->query($sql)->result_array();
		
		insert_est_bq_material($id_bq);
		
		if($result){
			$awal_row	= $NextRow;
			$no = 0;
			
			$SUM_RESIN = 0;
			$SUM_GLASS = 0;
			$SUM_ADDTIVE = 0;
			$SUM_MAT = 0;
			
			$SUM_RM = 0;
			$SUM_MP = 0;
			$SUM_FOH = 0;
			$SUM_SALES_GA = 0;
			$SUM_COST = 0;
			
			$SUM_PROFIT = 0;
			$SUM_SELL_BED = 0;
			$SUM_ALLOW = 0;
			$SUM_ED = 0;
			$SUM_INT_DISKON = 0;
			$SUM_SELL_AED= 0;
			$SUM_SELL_PERKG = 0;
			
			foreach($result as $key => $valx){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$product		= $valx['product'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$diameter_1	= $valx['diameter_1'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter_1);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$diameter_2	= $valx['diameter_2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter_2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$id_milik	= spec_bq($valx['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_milik);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty	= $valx['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$sum_resin = $this->db
								->select('SUM(last_cost) AS resin')
								->get_where('est_bq_material', array('id_bq'=>$id_bq,'id_milik'=>$valx['id_milik'],'id_category'=>'TYP-0001','hist_by'=>$userID))
								->result();
				$jumlah_resin = 0;
				if(!empty($sum_resin)){
					$jumlah_resin = $sum_resin[0]->resin;
				}
				
				$sum_glass = $this->db
								->select('SUM(last_cost) AS glass')
								->from('est_bq_material')
								->where("id_bq = '".$id_bq."' AND id_milik = '".$valx['id_milik']."' AND hist_by = '".$userID."' ")
								->where("id_category IN ('TYP-0003','TYP-0004','TYP-0005','TYP-0006')")
								->get()
								->result();
				$jumlah_glass = 0;
				if(!empty($sum_glass)){
					$jumlah_glass = $sum_glass[0]->glass;
				}
				
				$sum_addtivex = $this->db
								->select('SUM(last_cost) AS additive')
								->from('est_bq_material')
								->where("id_bq = '".$id_bq."' AND id_milik = '".$valx['id_milik']."' AND hist_by = '".$userID."' ")
								->where("id_category NOT IN ('TYP-0001','TYP-0003','TYP-0004','TYP-0005','TYP-0006')")
								->get()
								->result();
				$jumlah_addtive = 0;
				if(!empty($sum_addtivex)){
					$jumlah_addtive = $sum_addtivex[0]->additive;
				}
				
				$SUM_RESIN += $jumlah_resin * $valx['qty'];
				
				$awal_col++;
				$material_qty	= $jumlah_resin * $valx['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_GLASS += $jumlah_glass * $valx['qty'];
				
				$awal_col++;
				$cogs	= $jumlah_glass * $valx['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cogs);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_ADDTIVE += $jumlah_addtive * $valx['qty'];
				
				$awal_col++;
				$material_harga	= $jumlah_addtive * $valx['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$material_harga	= ($jumlah_resin * $valx['qty']) + ($jumlah_glass * $valx['qty']) + ($jumlah_addtive * $valx['qty']);
				$SUM_MAT += $material_harga;
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_RM += $valx['rm'];
				
				$awal_col++;
				$direct_labour	= $valx['rm'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_MP += $valx['mp'];
				
				$awal_col++;
				$indirect_labour	= $valx['mp'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_FOH += $valx['foh'];
				
				$awal_col++;
				$consumable	= $valx['foh'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_SALES_GA += $valx['sales_ga'];
				
				$awal_col++;
				$consumable	= $valx['sales_ga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$cost_process = $valx['rm'] + $valx['mp'] + $valx['foh'] + $valx['sales_ga'];
				$SUM_COST += $cost_process;
				
				$awal_col++;
				$machine	= $cost_process;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_PROFIT += $valx['total_profit'] - ($cost_process);
				
				$awal_col++;
				$foh_consumable	= $valx['total_profit'] - ($cost_process);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_SELL_BED += $valx['total_profit'];
				
				$awal_col++;
				$foh_depresiasi	= $valx['total_profit'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_ALLOW += $valx['total_allowance'] - $valx['total_profit'];
				
				$awal_col++;
				$biaya_gaji_non_produksi	= $valx['total_allowance'] - $valx['total_profit'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_ED += 0;
				
				$awal_col++;
				$biaya_non_produksi	= 0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_INT_DISKON += 0;
				
				$awal_col++;
				$biaya_rutin_bulanan	= 0;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$SUM_SELL_AED += $valx['total_allowance'];
				
				$awal_col++;
				$biaya_non_produksi	= $valx['total_allowance'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$sellingKg = 0;
				if($biaya_non_produksi > 0 AND $material_harga > 0){
					$sellingKg	= $biaya_non_produksi / $material_harga;
				}
				$SUM_SELL_PERKG += $sellingKg;
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sellingKg);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
			// echo $no;exit;
			$Colsw = floatval($no) +7;
			
			// echo $Colsw."-".$Colse; exit;
			
			$sheet->setCellValue("A".$Colsw."", '');
			$sheet->getStyle("A".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$Colsw.":F".$Colsw."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			$sheet->setCellValue("G".$Colsw."", $SUM_RESIN);
			$sheet->getStyle("G".$Colsw.":G".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("G".$Colsw.":G".$Colsw."");
			$sheet->getColumnDimension('G')->setAutoSize(true);
			
			$sheet->setCellValue("H".$Colsw."", $SUM_GLASS);
			$sheet->getStyle("H".$Colsw.":H".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("H".$Colsw.":H".$Colsw."");
			$sheet->getColumnDimension('H')->setAutoSize(true);
			
			$sheet->setCellValue("I".$Colsw."", $SUM_ADDTIVE);
			$sheet->getStyle("I".$Colsw.":I".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("I".$Colsw.":I".$Colsw."");
			$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue("J".$Colsw."", $SUM_MAT);
			$sheet->getStyle("J".$Colsw.":J".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("J".$Colsw.":J".$Colsw."");
			$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue("K".$Colsw."", $SUM_RM );
			$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
			$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue("L".$Colsw."", $SUM_MP);
			$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
			$sheet->getColumnDimension('L')->setAutoSize(true);
			
			$sheet->setCellValue("M".$Colsw."", $SUM_FOH);
			$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$Colsw."", $SUM_SALES_GA);
			$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$Colsw."", $SUM_COST);
			$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$Colsw."", $SUM_PROFIT);
			$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$Colsw."", $SUM_SELL_BED);
			$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$Colsw."", $SUM_ALLOW);
			$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$Colsw."", $SUM_ED);
			$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
			
			$sheet->setCellValue("T".$Colsw."", $SUM_INT_DISKON);
			$sheet->getStyle("T".$Colsw.":T".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("T".$Colsw.":T".$Colsw."");
			$sheet->getColumnDimension('T')->setAutoSize(true);
			
			$sheet->setCellValue("U".$Colsw."", $SUM_SELL_AED);
			$sheet->getStyle("U".$Colsw.":U".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("U".$Colsw.":U".$Colsw."");
			$sheet->getColumnDimension('U')->setAutoSize(true);
			
			$sheet->setCellValue("V".$Colsw."", $SUM_SELL_AED/$SUM_MAT);
			$sheet->getStyle("V".$Colsw.":V".$Colsw."")->applyFromArray($styleArray4);
			$sheet->mergeCells("V".$Colsw.":V".$Colsw."");
			$sheet->getColumnDimension('V')->setAutoSize(true);

		}
		
		$Colsw = floatval($no) + 9;
		
		$sheet->setCellValue("A".$Colsw."", 'NON FRP');
		$sheet->getStyle("A".$Colsw.":S".$Colsw."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$Colsw = floatval($no) + 10;
		
		$sheet->setCellValue("A".$Colsw."", 'NO');
		$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue("B".$Colsw."", 'NAMA BARANG');
		$sheet->getStyle("B".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("B".$Colsw.":F".$Colsw."");
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue("G".$Colsw."", 'CATEGORY');
		$sheet->getStyle("G".$Colsw.":J".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("G".$Colsw.":J".$Colsw."");
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue("K".$Colsw."", 'QTY');
		$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue("L".$Colsw."", 'SATUAN');
		$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue("M".$Colsw."", 'HARGA');
		$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue("N".$Colsw."", 'PROFIT');
		$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue("O".$Colsw."", 'TOTAL SELLING BEFORE ED');
		$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue("P".$Colsw."", 'ALLOWANCE');
		$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue("Q".$Colsw."", 'ED');
		$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue("R".$Colsw."", 'INT & DISKONTO');
		$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue("S".$Colsw."", 'TOTAL SELLIG AFTER ED');
		$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sql 		= "	SELECT
							a.*
						FROM
							laporan_revised_etc a
						WHERE
							a.id_bq = '".$id_bq."' 
							AND a.revised_no = '".$get_max_revisi[0]->revised_no."' 
							AND (a.category = 'plate' OR a.category = 'baut' OR a.category = 'gasket' OR a.category = 'lainnya')
						ORDER BY a.caregory_sub ASC";
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $Colsw;
			$noX = 0;
			
			$SUM_COST = 0;
			$SUM_PROFIT = 0;
			$SUM_SELL_BED = 0;
			$SUM_ALLOW = 0;
			$SUM_ED = 0;
			$SUM_INT_DISKON = 0;
			$SUM_SELL_AED= 0;
			
			foreach($result as $key => $valx){
				$noX++;
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				
				$awal_col++;
				$nomorx		= $noX;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$product		= get_name_acc($valx['caregory_sub']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle("B".$awal_row.":F".$awal_row."")->applyFromArray($styleArray3);
				$sheet->mergeCells("B".$awal_row.":F".$awal_row."");
				
				$awal_col++;
				$product		= strtoupper($valx['category']);
				$sheet->setCellValue("G".$awal_row."", $product);
				$sheet->getStyle("G".$awal_row.":J".$awal_row."")->applyFromArray($styleArray3);
				$sheet->mergeCells("G".$awal_row.":J".$awal_row."");
				
				$awal_col++;
				$product		= $valx['qty'];
				$sheet->setCellValue("K".$awal_row."", $product);
				$sheet->getStyle("K".$awal_row.":K".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("K".$awal_row.":K".$awal_row."");
				
				$awal_col++;
				$product		= strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']));
				$sheet->setCellValue("L".$awal_row."", $product);
				$sheet->getStyle("L".$awal_row.":L".$awal_row."")->applyFromArray($styleArray3);
				$sheet->mergeCells("L".$awal_row.":L".$awal_row."");
				
				$SUM_COST += $valx['fumigasi'];
				
				$awal_col++;
				$product		= $valx['fumigasi'];
				$sheet->setCellValue("M".$awal_row."", $product);
				$sheet->getStyle("M".$awal_row.":M".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("M".$awal_row.":M".$awal_row."");
				
				$SUM_PROFIT += $valx['price'] - $valx['fumigasi'];
				
				$awal_col++;
				$product		= $valx['price'] - $valx['fumigasi'];
				$sheet->setCellValue("N".$awal_row."", $product);
				$sheet->getStyle("N".$awal_row.":N".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("N".$awal_row.":N".$awal_row."");
				
				$SUM_SELL_BED += $valx['price'];
				
				$awal_col++;
				$product		= $valx['price'];
				$sheet->setCellValue("O".$awal_row."", $product);
				$sheet->getStyle("O".$awal_row.":O".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("O".$awal_row.":O".$awal_row."");
				
				$SUM_ALLOW += $valx['price_total'] - $valx['price'];
				
				$awal_col++;
				$product		= $valx['price_total'] - $valx['price'];
				$sheet->setCellValue("P".$awal_row."", $product);
				$sheet->getStyle("P".$awal_row.":P".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("P".$awal_row.":P".$awal_row."");
				
				$SUM_ED += 0;
				
				$awal_col++;
				$product		= 0;
				$sheet->setCellValue("Q".$awal_row."", $product);
				$sheet->getStyle("Q".$awal_row.":Q".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("Q".$awal_row.":Q".$awal_row."");
				
				$SUM_INT_DISKON += 0;
				
				$awal_col++;
				$product		= 0;
				$sheet->setCellValue("R".$awal_row."", $product);
				$sheet->getStyle("R".$awal_row.":R".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("R".$awal_row.":R".$awal_row."");
				
				$SUM_SELL_AED += $valx['price_total'];
				
				$awal_col++;
				$product		= $valx['price_total'];
				$sheet->setCellValue("S".$awal_row."", $product);
				$sheet->getStyle("S".$awal_row.":S".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("S".$awal_row.":S".$awal_row."");
				
			}
			
			$awal_row = floatval($no) +11;
			
			$sheet->setCellValue("A".$awal_row."", '');
			$sheet->getStyle("A".$awal_row.":L".$awal_row."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$awal_row.":L".$awal_row."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			$sheet->setCellValue("M".$awal_row."", $SUM_COST);
			$sheet->getStyle("M".$awal_row.":M".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("M".$awal_row.":M".$awal_row."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$awal_row."", $SUM_PROFIT);
			$sheet->getStyle("N".$awal_row.":N".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("N".$awal_row.":N".$awal_row."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$awal_row."", $SUM_SELL_BED);
			$sheet->getStyle("O".$awal_row.":O".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("O".$awal_row.":O".$awal_row."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$awal_row."", $SUM_ALLOW);
			$sheet->getStyle("P".$awal_row.":P".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("P".$awal_row.":P".$awal_row."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$awal_row."", $SUM_ED);
			$sheet->getStyle("Q".$awal_row.":Q".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Q".$awal_row.":Q".$awal_row."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$awal_row."", $SUM_INT_DISKON);
			$sheet->getStyle("R".$awal_row.":R".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("R".$awal_row.":R".$awal_row."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$awal_row."", $SUM_SELL_AED);
			$sheet->getStyle("S".$awal_row.":S".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("S".$awal_row.":S".$awal_row."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
			
		}
		
		$Colsw = floatval($no) + 13;
		
		$sheet->setCellValue("A".$Colsw."", 'MATERIAL');
		$sheet->getStyle("A".$Colsw.":S".$Colsw."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$Colsw = floatval($no) + 14;
		
		$sheet->setCellValue("A".$Colsw."", 'NO');
		$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue("B".$Colsw."", 'NAMA BARANG');
		$sheet->getStyle("B".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("B".$Colsw.":F".$Colsw."");
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue("G".$Colsw."", 'TYPE');
		$sheet->getStyle("G".$Colsw.":J".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("G".$Colsw.":J".$Colsw."");
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue("K".$Colsw."", 'QTY');
		$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue("L".$Colsw."", 'SATUAN');
		$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue("M".$Colsw."", 'HARGA');
		$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue("N".$Colsw."", 'PROFIT');
		$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue("O".$Colsw."", 'TOTAL SELLING BEFORE ED');
		$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue("P".$Colsw."", 'ALLOWANCE');
		$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue("Q".$Colsw."", 'ED');
		$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue("R".$Colsw."", 'INT & DISKONTO');
		$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue("S".$Colsw."", 'TOTAL SELLIG AFTER ED');
		$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sql 		= "	SELECT
							a.*
						FROM
							laporan_revised_etc a
						WHERE
							a.id_bq = '".$id_bq."' 
							AND a.revised_no = '".$get_max_revisi[0]->revised_no."' 
							AND a.category = 'aksesoris'
						ORDER BY a.caregory_sub ASC";
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $Colsw;
			$noX = 0;
			
			$SUM_COST = 0;
			$SUM_PROFIT = 0;
			$SUM_SELL_BED = 0;
			$SUM_ALLOW = 0;
			$SUM_ED = 0;
			$SUM_INT_DISKON = 0;
			$SUM_SELL_AED= 0;
			
			foreach($result as $key => $valx){
				$noX++;
				$awal_row++;
				$awal_col	= 0;
				$no++;
				
				$awal_col++;
				$nomorx		= $noX;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$product		= get_name('raw_materials','nm_material','id_material',$valx['caregory_sub']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle("B".$awal_row.":F".$awal_row."")->applyFromArray($styleArray3);
				$sheet->mergeCells("B".$awal_row.":F".$awal_row."");
				
				$awal_col++;
				$product		= get_name('raw_materials','nm_category','id_material',$valx['caregory_sub']);
				$sheet->setCellValue("G".$awal_row."", $product);
				$sheet->getStyle("G".$awal_row.":J".$awal_row."")->applyFromArray($styleArray3);
				$sheet->mergeCells("G".$awal_row.":J".$awal_row."");
				
				$awal_col++;
				$product		= $valx['weight'];
				$sheet->setCellValue("K".$awal_row."", $product);
				$sheet->getStyle("K".$awal_row.":K".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("K".$awal_row.":K".$awal_row."");
				
				$awal_col++;
				$product		= strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']));
				$sheet->setCellValue("L".$awal_row."", $product);
				$sheet->getStyle("L".$awal_row.":L".$awal_row."")->applyFromArray($styleArray3);
				$sheet->mergeCells("L".$awal_row.":L".$awal_row."");
				
				$SUM_COST += $valx['fumigasi'];
				
				$awal_col++;
				$product		= $valx['fumigasi'];
				$sheet->setCellValue("M".$awal_row."", $product);
				$sheet->getStyle("M".$awal_row.":M".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("M".$awal_row.":M".$awal_row."");
				
				$SUM_PROFIT += $valx['price'] - $valx['fumigasi'];
				
				$awal_col++;
				$product		= $valx['price'] - $valx['fumigasi'];
				$sheet->setCellValue("N".$awal_row."", $product);
				$sheet->getStyle("N".$awal_row.":N".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("N".$awal_row.":N".$awal_row."");
				
				$SUM_SELL_BED += $valx['price'];
				
				$awal_col++;
				$product		= $valx['price'];
				$sheet->setCellValue("O".$awal_row."", $product);
				$sheet->getStyle("O".$awal_row.":O".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("O".$awal_row.":O".$awal_row."");
				
				$SUM_ALLOW += $valx['price_total'] - $valx['price'];
				
				$awal_col++;
				$product		= $valx['price_total'] - $valx['price'];
				$sheet->setCellValue("P".$awal_row."", $product);
				$sheet->getStyle("P".$awal_row.":P".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("P".$awal_row.":P".$awal_row."");
				
				$SUM_ED += 0;
				
				$awal_col++;
				$product		= 0;
				$sheet->setCellValue("Q".$awal_row."", $product);
				$sheet->getStyle("Q".$awal_row.":Q".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("Q".$awal_row.":Q".$awal_row."");
				
				$SUM_INT_DISKON += 0;
				
				$awal_col++;
				$product		= 0;
				$sheet->setCellValue("R".$awal_row."", $product);
				$sheet->getStyle("R".$awal_row.":R".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("R".$awal_row.":R".$awal_row."");
				
				$SUM_SELL_AED += $valx['price_total'];
				
				$awal_col++;
				$product		= $valx['price_total'];
				$sheet->setCellValue("S".$awal_row."", $product);
				$sheet->getStyle("S".$awal_row.":S".$awal_row."")->applyFromArray($styleArray4);
				$sheet->mergeCells("S".$awal_row.":S".$awal_row."");
				
			}
			
			$awal_row = floatval($no) +15;
			
			$sheet->setCellValue("A".$awal_row."", '');
			$sheet->getStyle("A".$awal_row.":L".$awal_row."")->applyFromArray($style_header);
			$sheet->mergeCells("A".$awal_row.":L".$awal_row."");
			$sheet->getColumnDimension('A')->setAutoSize(true);
			
			$sheet->setCellValue("M".$awal_row."", $SUM_COST);
			$sheet->getStyle("M".$awal_row.":M".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("M".$awal_row.":M".$awal_row."");
			$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue("N".$awal_row."", $SUM_PROFIT);
			$sheet->getStyle("N".$awal_row.":N".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("N".$awal_row.":N".$awal_row."");
			$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue("O".$awal_row."", $SUM_SELL_BED);
			$sheet->getStyle("O".$awal_row.":O".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("O".$awal_row.":O".$awal_row."");
			$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue("P".$awal_row."", $SUM_ALLOW);
			$sheet->getStyle("P".$awal_row.":P".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("P".$awal_row.":P".$awal_row."");
			$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue("Q".$awal_row."", $SUM_ED);
			$sheet->getStyle("Q".$awal_row.":Q".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("Q".$awal_row.":Q".$awal_row."");
			$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue("R".$awal_row."", $SUM_INT_DISKON);
			$sheet->getStyle("R".$awal_row.":R".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("R".$awal_row.":R".$awal_row."");
			$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue("S".$awal_row."", $SUM_SELL_AED);
			$sheet->getStyle("S".$awal_row.":S".$awal_row."")->applyFromArray($styleArray4);
			$sheet->mergeCells("S".$awal_row.":S".$awal_row."");
			$sheet->getColumnDimension('S')->setAutoSize(true);
		}
		
		$Colsw = floatval($no) + 17;
		
		$sheet->setCellValue("A".$Colsw."", 'ENGGENERING');
		$sheet->getStyle("A".$Colsw.":S".$Colsw."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$Colsw = floatval($no) + 18;
		
		$sheet->setCellValue("A".$Colsw."", 'NO');
		$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue("B".$Colsw."", 'NAMA BARANG');
		$sheet->getStyle("B".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("B".$Colsw.":F".$Colsw."");
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue("G".$Colsw."", 'SPESIFIKASI');
		$sheet->getStyle("G".$Colsw.":J".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("G".$Colsw.":J".$Colsw."");
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue("K".$Colsw."", 'QTY');
		$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue("L".$Colsw."", 'SATUAN');
		$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue("M".$Colsw."", 'HARGA');
		$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue("N".$Colsw."", 'PROFIT');
		$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue("O".$Colsw."", 'TOTAL SELLING BEFORE ED');
		$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue("P".$Colsw."", 'ALLOWANCE');
		$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue("Q".$Colsw."", 'ED');
		$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue("R".$Colsw."", 'INT & DISKONTO');
		$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue("S".$Colsw."", 'TOTAL SELLIG AFTER ED');
		$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sql 		= "	SELECT
							a.*
						FROM
							laporan_revised_etc a
						WHERE
							a.id_bq = '".$id_bq."' 
							AND a.revised_no = '".$get_max_revisi[0]->revised_no."' 
							AND a.category = 'engine'
						ORDER BY a.caregory_sub ASC";
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $Colsw;
			$noX = 0;
			
			$SUM_COST = 0;
			$SUM_PROFIT = 0;
			$SUM_SELL_BED = 0;
			$SUM_ALLOW = 0;
			$SUM_ED = 0;
			$SUM_INT_DISKON = 0;
			$SUM_SELL_AED= 0;
			
			foreach($result as $key => $valx){
				$noX++;
				$awal_row++;
				$awal_col	= 0;
				$no++;
				
				$awal_col++;
				$nomorx		= $noX;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$product		= $valx['caregory_sub'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
			}
			
			$awal_row = floatval($no) +19;
		}
		
		
		$Colsw = floatval($no) + 21;
		
		$sheet->setCellValue("A".$Colsw."", 'PACKING');
		$sheet->getStyle("A".$Colsw.":S".$Colsw."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$Colsw = floatval($no) + 22;
		
		$sheet->setCellValue("A".$Colsw."", 'NO');
		$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue("B".$Colsw."", 'NAMA BARANG');
		$sheet->getStyle("B".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("B".$Colsw.":F".$Colsw."");
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue("G".$Colsw."", 'SPESIFIKASI');
		$sheet->getStyle("G".$Colsw.":J".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("G".$Colsw.":J".$Colsw."");
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue("K".$Colsw."", 'QTY');
		$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue("L".$Colsw."", 'SATUAN');
		$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue("M".$Colsw."", 'HARGA');
		$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue("N".$Colsw."", 'PROFIT');
		$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue("O".$Colsw."", 'TOTAL SELLING BEFORE ED');
		$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue("P".$Colsw."", 'ALLOWANCE');
		$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue("Q".$Colsw."", 'ED');
		$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue("R".$Colsw."", 'INT & DISKONTO');
		$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue("S".$Colsw."", 'TOTAL SELLIG AFTER ED');
		$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sql 		= "	SELECT
							a.*
						FROM
							laporan_revised_etc a
						WHERE
							a.id_bq = '".$id_bq."' 
							AND a.revised_no = '".$get_max_revisi[0]->revised_no."' 
							AND a.category = 'packing'
						ORDER BY a.caregory_sub ASC";
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $Colsw;
			$noX = 0;
			$SUM_COST = 0;
			$SUM_PROFIT = 0;
			$SUM_SELL_BED = 0;
			$SUM_ALLOW = 0;
			$SUM_ED = 0;
			$SUM_INT_DISKON = 0;
			$SUM_SELL_AED= 0;
			
			foreach($result as $key => $valx){
				$noX++;
				$awal_row++;
				$awal_col	= 0;
				$no++;
				
				
				$awal_col++;
				$nomorx		= $noX;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$product		= $valx['caregory_sub'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
			}
			
			$awal_row = floatval($no) +23;
		}
		
		$Colsw = floatval($no) + 25;
		
		$sheet->setCellValue("A".$Colsw."", 'TRUCKING EXPORT');
		$sheet->getStyle("A".$Colsw.":S".$Colsw."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$Colsw = floatval($no) + 26;
		
		$sheet->setCellValue("A".$Colsw."", 'NO');
		$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue("B".$Colsw."", 'NAMA BARANG');
		$sheet->getStyle("B".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("B".$Colsw.":F".$Colsw."");
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue("G".$Colsw."", 'SPESIFIKASI');
		$sheet->getStyle("G".$Colsw.":J".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("G".$Colsw.":J".$Colsw."");
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue("K".$Colsw."", 'QTY');
		$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue("L".$Colsw."", 'SATUAN');
		$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue("M".$Colsw."", 'HARGA');
		$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue("N".$Colsw."", 'PROFIT');
		$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue("O".$Colsw."", 'TOTAL SELLING BEFORE ED');
		$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue("P".$Colsw."", 'ALLOWANCE');
		$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue("Q".$Colsw."", 'ED');
		$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue("R".$Colsw."", 'INT & DISKONTO');
		$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue("S".$Colsw."", 'TOTAL SELLIG AFTER ED');
		$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sql 		= "	SELECT
							a.*
						FROM
							laporan_revised_etc a
						WHERE
							a.id_bq = '".$id_bq."' 
							AND a.revised_no = '".$get_max_revisi[0]->revised_no."' 
							AND a.category = 'export'
						ORDER BY a.caregory_sub ASC";
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $Colsw;
			$noX = 0;
			
			$SUM_COST = 0;
			$SUM_PROFIT = 0;
			$SUM_SELL_BED = 0;
			$SUM_ALLOW = 0;
			$SUM_ED = 0;
			$SUM_INT_DISKON = 0;
			$SUM_SELL_AED= 0;
			
			foreach($result as $key => $valx){
				$noX++;
				$awal_row++;
				$awal_col	= 0;
				$no++;
				
				$awal_col++;
				$nomorx		= $noX;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$product		= $valx['caregory_sub'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
			}
			$awal_row = floatval($no) +27;
		}
		
		
		$Colsw = floatval($no) + 29;
		
		$sheet->setCellValue("A".$Colsw."", 'TRUCKING LOKAL');
		$sheet->getStyle("A".$Colsw.":S".$Colsw."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$Colsw = floatval($no) + 30;
		
		$sheet->setCellValue("A".$Colsw."", 'NO');
		$sheet->getStyle("A".$Colsw.":A".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("A".$Colsw.":A".$Colsw."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue("B".$Colsw."", 'NAMA BARANG');
		$sheet->getStyle("B".$Colsw.":F".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("B".$Colsw.":F".$Colsw."");
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue("G".$Colsw."", 'SPESIFIKASI');
		$sheet->getStyle("G".$Colsw.":J".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("G".$Colsw.":J".$Colsw."");
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue("K".$Colsw."", 'QTY');
		$sheet->getStyle("K".$Colsw.":K".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("K".$Colsw.":K".$Colsw."");
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue("L".$Colsw."", 'SATUAN');
		$sheet->getStyle("L".$Colsw.":L".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("L".$Colsw.":L".$Colsw."");
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue("M".$Colsw."", 'HARGA');
		$sheet->getStyle("M".$Colsw.":M".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("M".$Colsw.":M".$Colsw."");
		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue("N".$Colsw."", 'PROFIT');
		$sheet->getStyle("N".$Colsw.":N".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("N".$Colsw.":N".$Colsw."");
		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue("O".$Colsw."", 'TOTAL SELLING BEFORE ED');
		$sheet->getStyle("O".$Colsw.":O".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("O".$Colsw.":O".$Colsw."");
		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue("P".$Colsw."", 'ALLOWANCE');
		$sheet->getStyle("P".$Colsw.":P".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("P".$Colsw.":P".$Colsw."");
		$sheet->getColumnDimension('P')->setAutoSize(true);
		
		$sheet->setCellValue("Q".$Colsw."", 'ED');
		$sheet->getStyle("Q".$Colsw.":Q".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("Q".$Colsw.":Q".$Colsw."");
		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue("R".$Colsw."", 'INT & DISKONTO');
		$sheet->getStyle("R".$Colsw.":R".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("R".$Colsw.":R".$Colsw."");
		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue("S".$Colsw."", 'TOTAL SELLIG AFTER ED');
		$sheet->getStyle("S".$Colsw.":S".$Colsw."")->applyFromArray($style_header);
		$sheet->mergeCells("S".$Colsw.":S".$Colsw."");
		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sql 		= "	SELECT
							a.*
						FROM
							laporan_revised_etc a
						WHERE
							a.id_bq = '".$id_bq."' 
							AND a.revised_no = '".$get_max_revisi[0]->revised_no."' 
							AND a.category = 'lokal'
						ORDER BY a.caregory_sub ASC";
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $Colsw;
			$noX = 0;
			
			$SUM_COST = 0;
			$SUM_PROFIT = 0;
			$SUM_SELL_BED = 0;
			$SUM_ALLOW = 0;
			$SUM_ED = 0;
			$SUM_INT_DISKON = 0;
			$SUM_SELL_AED= 0;
			
			foreach($result as $key => $valx){
				$noX++;
				$awal_row++;
				$awal_col	= 0;
				$no++;
				
				$awal_col++;
				$nomorx		= $noX;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$product		= $valx['caregory_sub'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
			}
			$awal_row = floatval($no) +31;
		}
		
		history('Download excel summary cost '.str_replace('BQ-','',$id_bq));
		
		$sheet->setTitle(str_replace('BQ-','',$id_bq));
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
		header('Content-Disposition: attachment;filename="SUMMARY COST '.str_replace('BQ-','',$id_bq).' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_report_costing(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

		$get_revisi_max = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		$revised_no = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

		$this->load->library("PHPExcel");
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
		
		$style_header3 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header4 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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
		$sheet->setCellValue('A'.$Row, 'REPORT COSTING PROJECT '.str_replace('BQ-','',$id_bq));
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue("A".$NewRow."", 'A. FRP');
		$sheet->getStyle("A".$NewRow.":H".$NextRow."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$NewRow.":H".$NextRow."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Item');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Deskripsi');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Pipe');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Flange');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Fitting');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'B&W');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Field Joint');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Total');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'Nama Resin');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Resin Yang digunakan');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
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
						->result();
		$resin_pipa = (!empty($get_resin_pipa))?$get_resin_pipa[0]->nm_material:'';
		$harga_resin_pipa = (!empty($get_resin_pipa))?$get_resin_pipa[0]->price_mat:'';

		$sheet->setCellValue('C'.$NewRow, $resin_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
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
						->result();
		$resin_flange = (!empty($get_resin_flange))?$get_resin_flange[0]->nm_material:'';
		$harga_resin_flange = (!empty($get_resin_flange))?$get_resin_flange[0]->price_mat:'';

		$sheet->setCellValue('D'.$NewRow, $resin_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
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
						->result();
		$resin_fitting = (!empty($get_resin_fitting))?$get_resin_fitting[0]->nm_material:'';
		$harga_resin_fitting = (!empty($get_resin_fitting))?$get_resin_fitting[0]->price_mat:'';

		$sheet->setCellValue('E'.$NewRow, $resin_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$get_resin_bw = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','bw')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result();
		$resin_bw = (!empty($get_resin_bw))?$get_resin_bw[0]->nm_material:'';
		$harga_resin_bw = (!empty($get_resin_bw))?$get_resin_bw[0]->price_mat:'';

		$sheet->setCellValue('F'.$NewRow, $resin_bw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$get_resin_field = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','field')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result();
		$resin_field = (!empty($get_resin_field))?$get_resin_field[0]->nm_material:'';
		$harga_resin_field = (!empty($get_resin_field))?$get_resin_field[0]->price_mat:'';

		$sheet->setCellValue('G'.$NewRow, $resin_field);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, '#');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Harga Resin/Kg');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Harga resin yang digunakan untuk estimasi.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $harga_resin_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $harga_resin_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $harga_resin_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $harga_resin_bw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $harga_resin_field);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, '#');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Berat Material');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		//berat pipa
		$data_berat_pipa = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_pipa, 
							  SUM(a.est_harga) AS biaya_pipa,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_pipa_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_pipa_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_pipa_ga,
							  SUM(a.unit_price * a.qty) AS biaya_pipa_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_pipa_profit,
							  SUM(a.total_price) AS biaya_pipa_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_pipa_allow,
							  SUM(a.total_price_last) AS biaya_pipa_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','pipa')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->berat_pipa:0;
		$biaya_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa:0;
		$biaya_pipa_mp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_mp:0;
		$biaya_pipa_foh = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_foh:0;
		$biaya_pipa_ga = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_ga:0;
		$biaya_pipa_dasar = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_dasar:0;
		$biaya_pipa_profit = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_profit:0;
		$biaya_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp:0;
		$kg_pipa_bp = 0;
		if($berat_pipa <> 0){
			$kg_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp/$data_berat_pipa[0]->berat_pipa:0;
		}
		$biaya_pipa_allow = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_allow:0;
		$biaya_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp:0;
		$kg_pipa_sp = 0;
		if($berat_pipa <> 0){
			$kg_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp/$data_berat_pipa[0]->berat_pipa:0;
		}

		$sheet->setCellValue('C'.$NewRow, $berat_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		//berat flange
		$data_berat_flange = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_flange, 
							  SUM(a.est_harga) AS biaya_flange,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_flange_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_flange_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_flange_ga,
							  SUM(a.unit_price * a.qty) AS biaya_flange_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_flange_profit,
							  SUM(a.total_price) AS biaya_flange_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_flange_allow,
							  SUM(a.total_price_last) AS biaya_flange_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','flange')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->berat_flange:0;
		$biaya_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange:0;
		$biaya_flange_mp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_mp:0;
		$biaya_flange_foh = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_foh:0;
		$biaya_flange_ga = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_ga:0;
		$biaya_flange_dasar = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_dasar:0;
		$biaya_flange_profit = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_profit:0;
		$biaya_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp:0;
		$kg_flange_bp = 0;
		if($berat_flange <> 0){
			$kg_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp/$data_berat_flange[0]->berat_flange:0;
		}
		$biaya_flange_allow = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_allow:0;
		$biaya_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp:0;
		$kg_flange_sp = 0;
		if($berat_flange <> 0){
			$kg_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp/$data_berat_flange[0]->berat_flange:0;
		}

		$sheet->setCellValue('D'.$NewRow, $berat_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		//berat fitting
		$data_berat_fitting = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_fitting, 
							  SUM(a.est_harga) AS biaya_fitting,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_fitting_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_fitting_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_fitting_ga,
							  SUM(a.unit_price * a.qty) AS biaya_fitting_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_fitting_profit,
							  SUM(a.total_price) AS biaya_fitting_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_fitting_allow,
							  SUM(a.total_price_last) AS biaya_fitting_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing',NULL)
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->berat_fitting:0;
		$biaya_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting:0;
		$biaya_fitting_mp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_mp:0;
		$biaya_fitting_foh = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_foh:0;
		$biaya_fitting_ga = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_ga:0;
		$biaya_fitting_dasar = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_dasar:0;
		$biaya_fitting_profit = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_profit:0;
		$biaya_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp:0;
		$kg_fitting_bp = 0;
		if($berat_fitting <> 0){
			$kg_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp/$data_berat_fitting[0]->berat_fitting:0;
		}
		$biaya_fitting_allow = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_allow:0;
		$biaya_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp:0;
		$kg_fitting_sp = 0;
		if($berat_fitting <> 0){
			$kg_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp/$data_berat_fitting[0]->berat_fitting:0;
		}

		$sheet->setCellValue('E'.$NewRow, $berat_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		//berat field joint
		$data_berat_bnw = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_bnw, 
							  SUM(a.est_harga) AS biaya_bnw,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_bnw_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_bnw_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_bnw_ga,
							  SUM(a.unit_price * a.qty) AS biaya_bnw_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_bnw_profit,
							  SUM(a.total_price) AS biaya_bnw_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_bnw_allow,
							  SUM(a.total_price_last) AS biaya_bnw_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','bw')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->berat_bnw:0;
		$biaya_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw:0;
		$biaya_bnw_mp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_mp:0;
		$biaya_bnw_foh = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_foh:0;
		$biaya_bnw_ga = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_ga:0;
		$biaya_bnw_dasar = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_dasar:0;
		$biaya_bnw_profit = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_profit:0;
		$biaya_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp:0;
		$kg_bnw_bp = 0;
		if($berat_bnw <> 0){
			$kg_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp/$data_berat_bnw[0]->berat_bnw:0;
		}
		$biaya_bnw_allow = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_allow:0;
		$biaya_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp:0;
		$kg_bnw_sp = 0;
		if($berat_bnw <> 0){
			$kg_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp/$data_berat_bnw[0]->berat_bnw:0;
		}

		$sheet->setCellValue('F'.$NewRow, $berat_bnw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		//berat field joint
		$data_berat_field = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_field, 
							  SUM(a.est_harga) AS biaya_field,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_field_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_field_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_field_ga,
							  SUM(a.unit_price * a.qty) AS biaya_field_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_field_profit,
							  SUM(a.total_price) AS biaya_field_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_field_allow,
							  SUM(a.total_price_last) AS biaya_field_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','field')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_field = (!empty($data_berat_field))?$data_berat_field[0]->berat_field:0;
		$biaya_field = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field:0;
		$biaya_field_mp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_mp:0;
		$biaya_field_foh = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_foh:0;
		$biaya_field_ga = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_ga:0;
		$biaya_field_dasar = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_dasar:0;
		$biaya_field_profit = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_profit:0;
		$biaya_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp:0;
	
		$kg_field_bp = 0;
		if($berat_field <> 0){
			$kg_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp/$data_berat_field[0]->berat_field:0;
		}
		$biaya_field_allow = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_allow:0;
		$biaya_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp:0;
		
		$kg_field_sp = 0;
		if($berat_field <> 0){
			$kg_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp/$data_berat_field[0]->berat_field:0;
		}
	
		$berat_total = $berat_pipa + $berat_flange + $berat_fitting + $berat_bnw + $berat_field;
		$biaya_total = $biaya_pipa + $biaya_flange + $biaya_fitting + $biaya_bnw + $biaya_field;
		$biaya_total_mp = $biaya_pipa_mp + $biaya_flange_mp + $biaya_fitting_mp + $biaya_bnw_mp + $biaya_field_mp;
		$biaya_total_foh = $biaya_pipa_foh + $biaya_flange_foh + $biaya_fitting_foh + $biaya_bnw_foh + $biaya_field_foh;
		$biaya_total_ga = $biaya_pipa_ga + $biaya_flange_ga + $biaya_fitting_ga + $biaya_bnw_ga + $biaya_field_ga;
		$biaya_total_dasar = $biaya_pipa_dasar + $biaya_flange_dasar + $biaya_fitting_dasar + $biaya_bnw_dasar + $biaya_field_dasar;
		$biaya_total_profit = $biaya_pipa_profit + $biaya_flange_profit + $biaya_fitting_profit + $biaya_bnw_profit + $biaya_field_profit;
		$biaya_total_bp = $biaya_pipa_bp + $biaya_flange_bp + $biaya_fitting_bp + $biaya_bnw_bp + $biaya_field_bp;
		$kg_total_bp = 0;
		if($berat_total <> 0){
		$kg_total_bp = $biaya_total_bp / $berat_total;
		}
		$biaya_total_allow = $biaya_pipa_allow + $biaya_flange_allow + $biaya_fitting_allow + $biaya_bnw_allow + $biaya_field_allow;
		$biaya_total_sp = $biaya_pipa_sp + $biaya_flange_sp + $biaya_fitting_sp + $biaya_bnw_sp + $biaya_field_sp;
		$kg_total_sp = 0;
		if($berat_total <> 0){
		$kg_total_sp = $biaya_total_sp / $berat_total;
		}

		$sheet->setCellValue('H'.$NewRow, $berat_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya Material');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya material sesuai dengan total kebutuhan material yang diestimasi engineering.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya MP & Utilities');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya direct labour dan indirect labour, depresiasi mesin, biaya mold mandrill dan consumable produksi');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_mp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_mp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_mp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_mp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_mp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_mp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya FOH');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya depresiasi FOH dan consumable FOH');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_foh);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_foh);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_foh);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_foh);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_foh);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_foh);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya General Admin');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya gaji non produksi, tagihan rutin(listrik, air, telp, internet dll), sales dan general admin.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_ga);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_ga);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_ga);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_ga);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_ga);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_ga);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya Dasar');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Total biaya.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_dasar);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_dasar);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_dasar);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_dasar);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_dasar);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_dasar);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Profit');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Nilai Profit');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_profit);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_profit);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_profit);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_profit);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_profit);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_profit);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Bottom Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya Dasar + Profit');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_bp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_bp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_bp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_bp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_bp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_bp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, '$/Kg (dari Bottom Price)');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Bottom price / Berat material');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $kg_pipa_bp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $kg_flange_bp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $kg_fitting_bp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $kg_bnw_bp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $kg_field_bp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $kg_total_bp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Allowance');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Persentase untuk ruang negosiasi sales.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_allow);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_allow);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_allow);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_allow);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_allow);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_allow);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Selling Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Bottom price + Allowance');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_sp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_sp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_sp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_sp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_sp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_sp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, '$/Kg (Selling Price)');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Selling Price / Berat material');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $kg_pipa_sp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $kg_flange_sp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $kg_fitting_sp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $kg_bnw_sp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $kg_field_sp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $kg_total_sp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		//NON FRP
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue("A".$NewRow."", 'B. NON FRP');
		$sheet->getStyle("A".$NewRow.":H".$NextRow."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$NewRow.":H".$NextRow."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Item');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Deskripsi');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Bolt & Nut');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Gasket');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Plate');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Lainnya');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Total');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Harga Costing');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Harga yang didapat dari supplier');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		//BAUT
		$data_berat_baut = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_baut, 
							  SUM(a.price - a.fumigasi) AS profit_baut,
							  SUM(a.price) AS bp_baut,
							  SUM(a.price_total - a.price) AS allow_baut,
							  SUM(a.price_total) AS sp_baut
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','baut')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->harga_baut:'';
		$profit_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->profit_baut:'';
		$bp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->bp_baut:'';
		$allow_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->allow_baut:'';
		$sp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->sp_baut:'';

		$sheet->setCellValue('D'.$NewRow, $harga_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		//GASKET
		$data_berat_gasket = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_gasket, 
							  SUM(a.price - a.fumigasi) AS profit_gasket,
							  SUM(a.price) AS bp_gasket,
							  SUM(a.price_total - a.price) AS allow_gasket,
							  SUM(a.price_total) AS sp_gasket
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','gasket')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->harga_gasket:'';
		$profit_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->profit_gasket:'';
		$bp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->bp_gasket:'';
		$allow_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->allow_gasket:'';
		$sp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->sp_gasket:'';

		$sheet->setCellValue('E'.$NewRow, $harga_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		//PLATE
		$data_berat_plate = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_plate, 
							  SUM(a.price - a.fumigasi) AS profit_plate,
							  SUM(a.price) AS bp_plate,
							  SUM(a.price_total - a.price) AS allow_plate,
							  SUM(a.price_total) AS sp_plate
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','plate')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->harga_plate:'';
		$profit_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->profit_plate:'';
		$bp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->bp_plate:'';
		$allow_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->allow_plate:'';
		$sp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->sp_plate:'';

		$sheet->setCellValue('F'.$NewRow, $harga_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		//LAINNYA
		$data_berat_lainnya = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_lainnya, 
							  SUM(a.price - a.fumigasi) AS profit_lainnya,
							  SUM(a.price) AS bp_lainnya,
							  SUM(a.price_total - a.price) AS allow_lainnya,
							  SUM(a.price_total) AS sp_lainnya
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','lainnya')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->harga_lainnya:'';
		$profit_lainnya = (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->profit_lainnya:'';
		$bp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->bp_lainnya:'';
		$allow_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->allow_lainnya:'';
		$sp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->sp_lainnya:'';

		$sheet->setCellValue('G'.$NewRow, $harga_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$harga_total 	= $harga_baut + $harga_gasket + $harga_plate + $harga_lainnya;
		$profit_total 	= $profit_baut + $profit_gasket + $profit_plate + $profit_lainnya;
		$bp_total 		= $bp_baut + $bp_gasket + $bp_plate + $bp_lainnya;
		$allow_total 	= $allow_baut + $allow_gasket + $allow_plate + $allow_lainnya;
		$sp_total 		= $sp_baut + $sp_gasket + $sp_plate + $sp_lainnya;

		$sheet->setCellValue('H'.$NewRow, $harga_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Profit');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Nilai Profit');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $profit_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $profit_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $profit_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $profit_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $profit_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Bottom Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya Dasar + Profit');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $bp_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $bp_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $bp_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $bp_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $bp_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Allowance');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Persentase untuk ruang negosiasi sales.');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $allow_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $allow_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $allow_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $allow_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $allow_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Selling Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Bottom price + Allowance');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $sp_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $sp_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $sp_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $sp_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $sp_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);


		//LAINNYA
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue("A".$NewRow."", 'C. PACKING & TRANSPORT');
		$sheet->getStyle("A".$NewRow.":H".$NextRow."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$NewRow.":H".$NextRow."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Kategori');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Total');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$data_packing = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_packing
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','packing')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_packing = (!empty($data_packing))?$data_packing[0]->harga_packing:0;

		$sheet->setCellValue('A'.$NewRow, 'Packing');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_packing);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$data_transport = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_transport
							  ')
							->from('laporan_revised_etc a')
							->where("(a.category = 'export' OR a.category = 'lokal')")
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_transport = (!empty($data_transport))?$data_transport[0]->harga_transport:0;

		$sheet->setCellValue('A'.$NewRow, 'Transportasi');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_transport);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$data_engine = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_engine
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','engine')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_engine = (!empty($data_engine))?$data_engine[0]->harga_engine:0;

		$sheet->setCellValue('A'.$NewRow, 'Engineering');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_engine);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		//SUM
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$harga_penawaran 	= $biaya_total_sp + $sp_total + $harga_packing + $harga_transport + $harga_engine;
		$harga_cost			= $biaya_total_dasar + $harga_total + $harga_packing + $harga_transport + $harga_engine;
		$net_profit			= $harga_penawaran - $harga_cost;
		$net_persent		= $net_profit / $harga_penawaran * 100;
		
		$sheet->setCellValue('A'.$NewRow, 'HARGA PENAWARAN');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, '');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_penawaran);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'TOTAL COST');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, '');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_cost);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'NET PROFIT');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, $net_persent);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $net_profit);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		


		history('Download excel report costing '.str_replace('BQ-','',$id_bq));
		
		$sheet->setTitle(str_replace('BQ-','',$id_bq));
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
		header('Content-Disposition: attachment;filename="SUMMARY COST '.str_replace('BQ-','',$id_bq).' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_report_costing_so(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq		= $this->uri->segment(3);

		$get_revisi_max = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		$revised_no = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

		$this->load->library("PHPExcel");
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
		
		$style_header3 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header4 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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
		$sheet->setCellValue('A'.$Row, 'REPORT COSTING PROJECT SO '.str_replace('BQ-','',$id_bq));
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue("A".$NewRow."", 'A. FRP');
		$sheet->getStyle("A".$NewRow.":H".$NextRow."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$NewRow.":H".$NextRow."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Item');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Deskripsi');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Pipe');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Flange');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Fitting');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'B&W');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Field Joint');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Total');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'Nama Resin');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Resin Yang digunakan');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
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
						->result();
		$resin_pipa = (!empty($get_resin_pipa))?$get_resin_pipa[0]->nm_material:'';
		$harga_resin_pipa = (!empty($get_resin_pipa))?$get_resin_pipa[0]->price_mat:'';

		$sheet->setCellValue('C'.$NewRow, $resin_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
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
						->result();
		$resin_flange = (!empty($get_resin_flange))?$get_resin_flange[0]->nm_material:'';
		$harga_resin_flange = (!empty($get_resin_flange))?$get_resin_flange[0]->price_mat:'';

		$sheet->setCellValue('D'.$NewRow, $resin_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
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
						->result();
		$resin_fitting = (!empty($get_resin_fitting))?$get_resin_fitting[0]->nm_material:'';
		$harga_resin_fitting = (!empty($get_resin_fitting))?$get_resin_fitting[0]->price_mat:'';

		$sheet->setCellValue('E'.$NewRow, $resin_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$get_resin_bw = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','bw')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result();
		$resin_bw = (!empty($get_resin_bw))?$get_resin_bw[0]->nm_material:'';
		$harga_resin_bw = (!empty($get_resin_bw))?$get_resin_bw[0]->price_mat:'';

		$sheet->setCellValue('F'.$NewRow, $resin_bw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$get_resin_field = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','field')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result();
		$resin_field = (!empty($get_resin_field))?$get_resin_field[0]->nm_material:'';
		$harga_resin_field = (!empty($get_resin_field))?$get_resin_field[0]->price_mat:'';

		$sheet->setCellValue('G'.$NewRow, $resin_field);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, '#');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Harga Resin/Kg');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Harga resin yang digunakan untuk estimasi.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $harga_resin_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $harga_resin_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $harga_resin_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $harga_resin_bw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $harga_resin_field);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, '#');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Berat Material');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		//berat pipa
		$data_berat_pipa = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_pipa, 
							  SUM(a.est_harga) AS biaya_pipa,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_pipa_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_pipa_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_pipa_ga,
							  SUM(a.unit_price * a.qty) AS biaya_pipa_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_pipa_profit,
							  SUM(a.total_price) AS biaya_pipa_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_pipa_allow,
							  SUM(a.total_price_last) AS biaya_pipa_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','pipa')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->berat_pipa:0;
		$biaya_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa:0;
		$biaya_pipa_mp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_mp:0;
		$biaya_pipa_foh = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_foh:0;
		$biaya_pipa_ga = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_ga:0;
		$biaya_pipa_dasar = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_dasar:0;
		$biaya_pipa_profit = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_profit:0;
		$biaya_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp:0;
		$kg_pipa_bp = 0;
		if($berat_pipa <> 0){
			$kg_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp/$data_berat_pipa[0]->berat_pipa:0;
		}
		$biaya_pipa_allow = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_allow:0;
		$biaya_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp:0;
		$kg_pipa_sp = 0;
		if($berat_pipa <> 0){
			$kg_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp/$data_berat_pipa[0]->berat_pipa:0;
		}

		$sheet->setCellValue('C'.$NewRow, $berat_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		//berat flange
		$data_berat_flange = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_flange, 
							  SUM(a.est_harga) AS biaya_flange,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_flange_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_flange_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_flange_ga,
							  SUM(a.unit_price * a.qty) AS biaya_flange_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_flange_profit,
							  SUM(a.total_price) AS biaya_flange_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_flange_allow,
							  SUM(a.total_price_last) AS biaya_flange_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','flange')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->berat_flange:0;
		$biaya_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange:0;
		$biaya_flange_mp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_mp:0;
		$biaya_flange_foh = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_foh:0;
		$biaya_flange_ga = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_ga:0;
		$biaya_flange_dasar = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_dasar:0;
		$biaya_flange_profit = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_profit:0;
		$biaya_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp:0;
		$kg_flange_bp = 0;
		if($berat_flange <> 0){
			$kg_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp/$data_berat_flange[0]->berat_flange:0;
		}
		$biaya_flange_allow = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_allow:0;
		$biaya_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp:0;
		$kg_flange_sp = 0;
		if($berat_flange <> 0){
			$kg_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp/$data_berat_flange[0]->berat_flange:0;
		}

		$sheet->setCellValue('D'.$NewRow, $berat_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		//berat fitting
		$data_berat_fitting = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_fitting, 
							  SUM(a.est_harga) AS biaya_fitting,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_fitting_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_fitting_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_fitting_ga,
							  SUM(a.unit_price * a.qty) AS biaya_fitting_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_fitting_profit,
							  SUM(a.total_price) AS biaya_fitting_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_fitting_allow,
							  SUM(a.total_price_last) AS biaya_fitting_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing',NULL)
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->berat_fitting:0;
		$biaya_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting:0;
		$biaya_fitting_mp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_mp:0;
		$biaya_fitting_foh = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_foh:0;
		$biaya_fitting_ga = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_ga:0;
		$biaya_fitting_dasar = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_dasar:0;
		$biaya_fitting_profit = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_profit:0;
		$biaya_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp:0;
		$kg_fitting_bp = 0;
		if($berat_fitting <> 0){
			$kg_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp/$data_berat_fitting[0]->berat_fitting:0;
		}
		$biaya_fitting_allow = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_allow:0;
		$biaya_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp:0;
		$kg_fitting_sp = 0;
		if($berat_fitting <> 0){
			$kg_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp/$data_berat_fitting[0]->berat_fitting:0;
		}

		$sheet->setCellValue('E'.$NewRow, $berat_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		//berat field joint
		$data_berat_bnw = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_bnw, 
							  SUM(a.est_harga) AS biaya_bnw,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_bnw_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_bnw_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_bnw_ga,
							  SUM(a.unit_price * a.qty) AS biaya_bnw_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_bnw_profit,
							  SUM(a.total_price) AS biaya_bnw_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_bnw_allow,
							  SUM(a.total_price_last) AS biaya_bnw_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','bw')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->berat_bnw:0;
		$biaya_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw:0;
		$biaya_bnw_mp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_mp:0;
		$biaya_bnw_foh = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_foh:0;
		$biaya_bnw_ga = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_ga:0;
		$biaya_bnw_dasar = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_dasar:0;
		$biaya_bnw_profit = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_profit:0;
		$biaya_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp:0;
		$kg_bnw_bp = 0;
		if($berat_bnw <> 0){
			$kg_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp/$data_berat_bnw[0]->berat_bnw:0;
		}
		$biaya_bnw_allow = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_allow:0;
		$biaya_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp:0;
		$kg_bnw_sp = 0;
		if($berat_bnw <> 0){
			$kg_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp/$data_berat_bnw[0]->berat_bnw:0;
		}

		$sheet->setCellValue('F'.$NewRow, $berat_bnw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		//berat field joint
		$data_berat_field = $this->db
		  					->select('
							  SUM(a.est_material) AS berat_field, 
							  SUM(a.est_harga) AS biaya_field,
							  SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_field_mp,
							  SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_field_foh,
							  SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_field_ga,
							  SUM(a.unit_price * a.qty) AS biaya_field_dasar,
							  SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_field_profit,
							  SUM(a.total_price) AS biaya_field_bp,
							  SUM(a.total_price_last - a.total_price) AS biaya_field_allow,
							  SUM(a.total_price_last) AS biaya_field_sp
							  ')
							->from('laporan_revised_detail a')
							->join('product_parent b','a.product_parent=b.product_parent','left')
							->where('b.type_costing','field')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$berat_field = (!empty($data_berat_field))?$data_berat_field[0]->berat_field:0;
		$biaya_field = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field:0;
		$biaya_field_mp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_mp:0;
		$biaya_field_foh = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_foh:0;
		$biaya_field_ga = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_ga:0;
		$biaya_field_dasar = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_dasar:0;
		$biaya_field_profit = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_profit:0;
		$biaya_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp:0;
	
		$kg_field_bp = 0;
		if($berat_field <> 0){
			$kg_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp/$data_berat_field[0]->berat_field:0;
		}
		$biaya_field_allow = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_allow:0;
		$biaya_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp:0;
		
		$kg_field_sp = 0;
		if($berat_field <> 0){
			$kg_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp/$data_berat_field[0]->berat_field:0;
		}
	
		$berat_total = $berat_pipa + $berat_flange + $berat_fitting + $berat_bnw + $berat_field;
		$biaya_total = $biaya_pipa + $biaya_flange + $biaya_fitting + $biaya_bnw + $biaya_field;
		$biaya_total_mp = $biaya_pipa_mp + $biaya_flange_mp + $biaya_fitting_mp + $biaya_bnw_mp + $biaya_field_mp;
		$biaya_total_foh = $biaya_pipa_foh + $biaya_flange_foh + $biaya_fitting_foh + $biaya_bnw_foh + $biaya_field_foh;
		$biaya_total_ga = $biaya_pipa_ga + $biaya_flange_ga + $biaya_fitting_ga + $biaya_bnw_ga + $biaya_field_ga;
		$biaya_total_dasar = $biaya_pipa_dasar + $biaya_flange_dasar + $biaya_fitting_dasar + $biaya_bnw_dasar + $biaya_field_dasar;
		$biaya_total_profit = $biaya_pipa_profit + $biaya_flange_profit + $biaya_fitting_profit + $biaya_bnw_profit + $biaya_field_profit;
		$biaya_total_bp = $biaya_pipa_bp + $biaya_flange_bp + $biaya_fitting_bp + $biaya_bnw_bp + $biaya_field_bp;
		$kg_total_bp = 0;
		if($berat_total <> 0){
		$kg_total_bp = $biaya_total_bp / $berat_total;
		}
		$biaya_total_allow = $biaya_pipa_allow + $biaya_flange_allow + $biaya_fitting_allow + $biaya_bnw_allow + $biaya_field_allow;
		$biaya_total_sp = $biaya_pipa_sp + $biaya_flange_sp + $biaya_fitting_sp + $biaya_bnw_sp + $biaya_field_sp;
		$kg_total_sp = 0;
		if($berat_total <> 0){
		$kg_total_sp = $biaya_total_sp / $berat_total;
		}

		$sheet->setCellValue('H'.$NewRow, $berat_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya Material');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya material sesuai dengan total kebutuhan material yang diestimasi engineering.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya MP & Utilities');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya direct labour dan indirect labour, depresiasi mesin, biaya mold mandrill dan consumable produksi');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_mp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_mp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_mp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_mp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_mp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_mp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya FOH');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya depresiasi FOH dan consumable FOH');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_foh);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_foh);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_foh);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_foh);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_foh);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_foh);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya General Admin');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya gaji non produksi, tagihan rutin(listrik, air, telp, internet dll), sales dan general admin.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_ga);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_ga);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_ga);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_ga);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_ga);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_ga);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Biaya Dasar');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Total biaya.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_dasar);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_dasar);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_dasar);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_dasar);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_dasar);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_dasar);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Profit');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Nilai Profit');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_profit);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_profit);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_profit);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_profit);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_profit);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_profit);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Bottom Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya Dasar + Profit');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_bp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_bp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_bp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_bp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_bp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_bp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, '$/Kg (dari Bottom Price)');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Bottom price / Berat material');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $kg_pipa_bp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $kg_flange_bp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $kg_fitting_bp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $kg_bnw_bp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $kg_field_bp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $kg_total_bp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Allowance');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Persentase untuk ruang negosiasi sales.');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_allow);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_allow);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_allow);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_allow);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_allow);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_allow);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Selling Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Bottom price + Allowance');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $biaya_pipa_sp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $biaya_flange_sp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $biaya_fitting_sp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $biaya_bnw_sp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $biaya_field_sp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $biaya_total_sp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, '$/Kg (Selling Price)');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Selling Price / Berat material');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, $kg_pipa_sp);
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $kg_flange_sp);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $kg_fitting_sp);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $kg_bnw_sp);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $kg_field_sp);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $kg_total_sp);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		//NON FRP
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue("A".$NewRow."", 'B. NON FRP');
		$sheet->getStyle("A".$NewRow.":H".$NextRow."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$NewRow.":H".$NextRow."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Item');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Deskripsi');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Bolt & Nut');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Gasket');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Plate');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Lainnya');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Total');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Harga Costing');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Harga yang didapat dari supplier');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		//BAUT
		$data_berat_baut = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_baut, 
							  SUM(a.price - a.fumigasi) AS profit_baut,
							  SUM(a.price) AS bp_baut,
							  SUM(a.price_total - a.price) AS allow_baut,
							  SUM(a.price_total) AS sp_baut
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','baut')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->harga_baut:'';
		$profit_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->profit_baut:'';
		$bp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->bp_baut:'';
		$allow_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->allow_baut:'';
		$sp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->sp_baut:'';

		$sheet->setCellValue('D'.$NewRow, $harga_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		//GASKET
		$data_berat_gasket = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_gasket, 
							  SUM(a.price - a.fumigasi) AS profit_gasket,
							  SUM(a.price) AS bp_gasket,
							  SUM(a.price_total - a.price) AS allow_gasket,
							  SUM(a.price_total) AS sp_gasket
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','gasket')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->harga_gasket:'';
		$profit_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->profit_gasket:'';
		$bp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->bp_gasket:'';
		$allow_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->allow_gasket:'';
		$sp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->sp_gasket:'';

		$sheet->setCellValue('E'.$NewRow, $harga_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		//PLATE
		$data_berat_plate = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_plate, 
							  SUM(a.price - a.fumigasi) AS profit_plate,
							  SUM(a.price) AS bp_plate,
							  SUM(a.price_total - a.price) AS allow_plate,
							  SUM(a.price_total) AS sp_plate
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','plate')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->harga_plate:'';
		$profit_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->profit_plate:'';
		$bp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->bp_plate:'';
		$allow_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->allow_plate:'';
		$sp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->sp_plate:'';

		$sheet->setCellValue('F'.$NewRow, $harga_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		//LAINNYA
		$data_berat_lainnya = $this->db
		  					->select('
							  SUM(a.fumigasi) AS harga_lainnya, 
							  SUM(a.price - a.fumigasi) AS profit_lainnya,
							  SUM(a.price) AS bp_lainnya,
							  SUM(a.price_total - a.price) AS allow_lainnya,
							  SUM(a.price_total) AS sp_lainnya
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','lainnya')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->harga_lainnya:'';
		$profit_lainnya = (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->profit_lainnya:'';
		$bp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->bp_lainnya:'';
		$allow_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->allow_lainnya:'';
		$sp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->sp_lainnya:'';

		$sheet->setCellValue('G'.$NewRow, $harga_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$harga_total 	= $harga_baut + $harga_gasket + $harga_plate + $harga_lainnya;
		$profit_total 	= $profit_baut + $profit_gasket + $profit_plate + $profit_lainnya;
		$bp_total 		= $bp_baut + $bp_gasket + $bp_plate + $bp_lainnya;
		$allow_total 	= $allow_baut + $allow_gasket + $allow_plate + $allow_lainnya;
		$sp_total 		= $sp_baut + $sp_gasket + $sp_plate + $sp_lainnya;

		$sheet->setCellValue('H'.$NewRow, $harga_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Profit');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Nilai Profit');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $profit_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $profit_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $profit_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $profit_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $profit_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Bottom Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Biaya Dasar + Profit');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $bp_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $bp_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $bp_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $bp_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $bp_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Allowance');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Persentase untuk ruang negosiasi sales.');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $allow_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $allow_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $allow_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $allow_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $allow_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Selling Price');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Bottom price + Allowance');
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, $sp_baut);
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, $sp_gasket);
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, $sp_plate);
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, $sp_lainnya);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $sp_total);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);


		//LAINNYA
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue("A".$NewRow."", 'C. PACKING & TRANSPORT');
		$sheet->getStyle("A".$NewRow.":H".$NextRow."")->applyFromArray($style_header3);
		$sheet->mergeCells("A".$NewRow.":H".$NextRow."");
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'Kategori');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Total');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$data_packing = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_packing
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','packing')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_packing = (!empty($data_packing))?$data_packing[0]->harga_packing:0;

		$sheet->setCellValue('A'.$NewRow, 'Packing');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_packing);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$data_transport = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_transport
							  ')
							->from('laporan_revised_etc a')
							->where("(a.category = 'export' OR a.category = 'lokal')")
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_transport = (!empty($data_transport))?$data_transport[0]->harga_transport:0;

		$sheet->setCellValue('A'.$NewRow, 'Transportasi');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_transport);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$data_engine = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_engine
							  ')
							->from('laporan_revised_etc a')
							->where('a.category','engine')
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
		$harga_engine = (!empty($data_engine))?$data_engine[0]->harga_engine:0;

		$sheet->setCellValue('A'.$NewRow, 'Engineering');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_engine);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		//SUM
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$harga_penawaran 	= $biaya_total_sp + $sp_total + $harga_packing + $harga_transport + $harga_engine;
		$harga_cost			= $biaya_total_dasar + $harga_total + $harga_packing + $harga_transport + $harga_engine;
		$net_profit			= $harga_penawaran - $harga_cost;
		$net_persent = 0;
		if($harga_penawaran <> 0 AND $net_profit <> 0 ){
			$net_persent		= $net_profit / $harga_penawaran * 100;
		}

		$get_deal = $this->db->select('total_deal_usd')->get_where('billing_so',array('no_ipp'=>str_replace('BQ-','',$id_bq)))->result();
		$deal_usd = (!empty($get_deal))?$get_deal[0]->total_deal_usd:0;

		$deal_so            = $deal_usd;
		$est_net_profit		= $deal_usd - $harga_cost;
		$est_net_persent = 0;
		if($deal_usd <> 0 AND $est_net_profit <> 0 ){
			$est_net_persent	= $est_net_profit / $harga_penawaran * 100;
		}


		$sheet->setCellValue('A'.$NewRow, 'HARGA PENAWARAN');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, '');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_penawaran);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'TOTAL COST');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, '');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $harga_cost);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'NET PROFIT');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, $net_persent);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $net_profit);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'DEAL SO');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, '');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $deal_so);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'ESTIMASI NET PROFIT');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, '');
		$sheet->getStyle('B'.$NewRow.':F'.$NextRow)->applyFromArray($style_header3);
		$sheet->mergeCells('B'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, $est_net_persent);
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, $est_net_profit);
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header4);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		


		history('Download excel report costing so '.str_replace('BQ-','',$id_bq));
		
		$sheet->setTitle(str_replace('BQ-','',$id_bq));
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
		header('Content-Disposition: attachment;filename="SUMMARY COST SO '.str_replace('BQ-','',$id_bq).' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
}

?>