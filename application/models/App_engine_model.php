<?php
class App_engine_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	//STRUCTURE BQ
	public function index_app_bq(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Approved Structure BQ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Approved Structure BQ');
		$this->load->view('Machine/approve_bq',$data);
	}
	
	public function approve_bq_modal(){ 
		$id_bq = $this->uri->segment(3);

		$sql 	= "SELECT * FROM bq_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id ASC";
		$result		= $this->db->query($sql)->result_array();
		
		$data = array(
			'id_bq' => $id_bq,
			'result' => $result
		);
		$this->load->view('Machine/approve_bq_modal', $data);
	}
	
	//ESTIMASI
	public function index_app_est(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Approval Estimasi Project',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Approved Est Structure BQ');
		$this->load->view('Machine/approve_est',$data);
	}
	
	public function approve_est_modal(){
		$id_bq 		= $this->uri->segment(3);

		$sql 		= "SELECT a.*, b.sum_mat FROM bq_detail_header a INNER JOIN estimasi_cost_and_mat_fast b ON a.id=b.id_milik WHERE a.id_bq = '".$id_bq."' AND b.id_bq = '".$id_bq."' ORDER BY a.id ASC";
		// echo $sql;
		$result		= $this->db->query($sql)->result_array();

		$detail 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'acc'))->result_array();
		$detail2 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'mat'))->result_array();
		$detail3 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'baut'))->result_array();
		$detail4 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'plate'))->result_array();
		$detail4g 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'gasket'))->result_array();
		$detail5 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'lainnya'))->result_array();
		
		$data = array(
			'id_bq' => $id_bq,
			'result' => $result,
			'detail' => $detail,
			'detail2' => $detail2,
			'detail3' => $detail3,
			'detail4' => $detail4,
			'detail4g' => $detail4g,
			'detail5' => $detail5,
		);

		$this->load->view('Machine/approve_est_modal', $data);
	}
	
	public function approve_est_excel(){
  		//membuat objek PHPExcel
  		set_time_limit(0);
  		ini_set('memory_limit','1024M');

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
  			'fill' => array(
  				'type' => PHPExcel_Style_Fill::FILL_SOLID,
  				'color' => array('rgb'=>'e0e0e0'),
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
  				'color' => array('rgb'=>'e0e0e0'),
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
			
			$id_bq = $this->uri->segment(3);
			
			$detail_data = $this->db->get_where('laporan_excel_est_bq', array('id_bq'=>$id_bq))->result_array();
			

    		$Row		= 1;
    		$NewRow		= $Row+1;
    		$Col_Akhir	= $Cols	= getColsChar(33);
    		$sheet->setCellValue('A'.$Row, 'CHECK APPROVAL ESTIMASI '.$id_bq);
    		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
    		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

    		$NewRow	= $NewRow +2;
    		$NextRow= $NewRow +1;

    		$sheet->setCellValue('A'.$NewRow, 'No');
    		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
    		$sheet->getColumnDimension('A')->setAutoSize(true);

    		$sheet->setCellValue('B'.$NewRow, 'Component');
    		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
    		$sheet->getColumnDimension('B')->setAutoSize(true);

			$sheet->setCellValue('C'.$NewRow, 'No Component');
    		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
    		$sheet->getColumnDimension('C')->setAutoSize(true);

			$sheet->setCellValue('D'.$NewRow, 'Diameter 1');
    		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
    		$sheet->getColumnDimension('D')->setAutoSize(true);
			
			$sheet->setCellValue('E'.$NewRow, 'Diameter 2');
    		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
    		$sheet->getColumnDimension('E')->setAutoSize(true);
			
			$sheet->setCellValue('F'.$NewRow, 'Length');
    		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
    		$sheet->getColumnDimension('F')->setAutoSize(true);
			
			$sheet->setCellValue('G'.$NewRow, 'Thickness');
    		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
    		$sheet->getColumnDimension('G')->setAutoSize(true);
			
			$sheet->setCellValue('H'.$NewRow, 'LR/SR');
    		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
    		$sheet->getColumnDimension('H')->setAutoSize(true);
			
			$sheet->setCellValue('I'.$NewRow, 'Sudut');
    		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
    		$sheet->getColumnDimension('I')->setAutoSize(true);
			
			$sheet->setCellValue('J'.$NewRow, 'Qty');
    		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
    		$sheet->getColumnDimension('J')->setAutoSize(true);
			
			$sheet->setCellValue('K'.$NewRow, 'Product ID');
    		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
    		$sheet->getColumnDimension('K')->setAutoSize(true);
			
			$sheet->setCellValue('L'.$NewRow, 'LIN RESIN');
    		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
    		$sheet->getColumnDimension('L')->setAutoSize(true);
			
			$sheet->setCellValue('M'.$NewRow, 'LIN VEIL');
    		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
    		$sheet->getColumnDimension('M')->setAutoSize(true);
			
			$sheet->setCellValue('N'.$NewRow, 'LIN CSM');
    		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
    		$sheet->getColumnDimension('N')->setAutoSize(true);
			
			$sheet->setCellValue('O'.$NewRow, 'STR RESIN');
    		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
    		$sheet->getColumnDimension('O')->setAutoSize(true);
			
			$sheet->setCellValue('P'.$NewRow, 'STR CSM');
    		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
    		$sheet->getColumnDimension('P')->setAutoSize(true);
			
			$sheet->setCellValue('Q'.$NewRow, 'STR WR');
    		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
    		$sheet->getColumnDimension('Q')->setAutoSize(true);
			
			$sheet->setCellValue('R'.$NewRow, 'STR ROOVING');
    		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
    		$sheet->getColumnDimension('R')->setAutoSize(true);
			
			$sheet->setCellValue('S'.$NewRow, 'STR N1 RESIN');
    		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
    		$sheet->getColumnDimension('S')->setAutoSize(true);
			
			$sheet->setCellValue('T'.$NewRow, 'STR N1 CSM');
    		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
    		$sheet->getColumnDimension('T')->setAutoSize(true);
			
			$sheet->setCellValue('U'.$NewRow, 'STR N1 WR');
    		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
    		$sheet->getColumnDimension('U')->setAutoSize(true);
			
			$sheet->setCellValue('V'.$NewRow, 'STR N1 ROOVING');
    		$sheet->getStyle('V'.$NewRow.':V'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow);
    		$sheet->getColumnDimension('V')->setAutoSize(true);
			
			$sheet->setCellValue('W'.$NewRow, 'STR N2 RESIN');
    		$sheet->getStyle('W'.$NewRow.':W'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow);
    		$sheet->getColumnDimension('W')->setAutoSize(true);
			
			$sheet->setCellValue('X'.$NewRow, 'STR N2 CSM');
    		$sheet->getStyle('X'.$NewRow.':X'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow);
    		$sheet->getColumnDimension('X')->setAutoSize(true);
			
			$sheet->setCellValue('Y'.$NewRow, 'STR N2 WR');
    		$sheet->getStyle('Y'.$NewRow.':Y'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('Y'.$NewRow.':Y'.$NextRow);
    		$sheet->getColumnDimension('Y')->setAutoSize(true);
			
			$sheet->setCellValue('Z'.$NewRow, 'EXT RESIN');
    		$sheet->getStyle('Z'.$NewRow.':Z'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('Z'.$NewRow.':Z'.$NextRow);
    		$sheet->getColumnDimension('Z')->setAutoSize(true);
			
			$sheet->setCellValue('AA'.$NewRow, 'EXT VEIL');
    		$sheet->getStyle('AA'.$NewRow.':AA'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('AA'.$NewRow.':AA'.$NextRow);
    		$sheet->getColumnDimension('AA')->setAutoSize(true);
			
			$sheet->setCellValue('AB'.$NewRow, 'EXT CSM');
    		$sheet->getStyle('AB'.$NewRow.':AB'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('AB'.$NewRow.':AB'.$NextRow);
    		$sheet->getColumnDimension('AB')->setAutoSize(true);
			
			$sheet->setCellValue('AC'.$NewRow, 'TC RESIN');
    		$sheet->getStyle('AC'.$NewRow.':AC'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('AC'.$NewRow.':AC'.$NextRow);
    		$sheet->getColumnDimension('AC')->setAutoSize(true);
			
			$sheet->setCellValue('AD'.$NewRow, 'GLASS VEIL');
    		$sheet->getStyle('AD'.$NewRow.':AD'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('AD'.$NewRow.':AD'.$NextRow);
    		$sheet->getColumnDimension('AD')->setAutoSize(true);
			
			$sheet->setCellValue('AE'.$NewRow, 'GLASS WR');
    		$sheet->getStyle('AE'.$NewRow.':AE'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('AE'.$NewRow.':AE'.$NextRow);
    		$sheet->getColumnDimension('AE')->setAutoSize(true);
			
			$sheet->setCellValue('AF'.$NewRow, 'GLASS CSM');
    		$sheet->getStyle('AF'.$NewRow.':AF'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('AF'.$NewRow.':AF'.$NextRow);
    		$sheet->getColumnDimension('AF')->setAutoSize(true);
			
			$sheet->setCellValue('AG'.$NewRow, 'JOINT RESIN');
    		$sheet->getStyle('AG'.$NewRow.':AG'.$NextRow)->applyFromArray($style_header);
    		$sheet->mergeCells('AG'.$NewRow.':AG'.$NextRow);
    		$sheet->getColumnDimension('AG')->setAutoSize(true);
			

  		if($detail_data){
  			$awal_row	= $NextRow;
  			$no=0;
  			foreach($detail_data as $key => $valx){
  				$no++;
  				$awal_row++;
  				$awal_col	= 0;

  				$awal_col++;
  				$nomor	= $no;
  				$Cols	= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $nomor);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

  				$awal_col++;
  				$id_category	= strtoupper($valx['id_category']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $id_category);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

  				$awal_col++;
  				$no_komponen	= $valx['no_komponen'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $no_komponen);
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
  				$length	= $valx['length'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $length);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$thickness	= $valx['thickness'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $thickness);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$type	= ($valx['type'] <> '0')?$valx['type']:'';
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $type);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$sudut	= ($valx['sudut'] <> 0)?$valx['sudut']:'';
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $sudut);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$qty	= $valx['qty'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $qty);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$id_product	= $valx['id_product'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $id_product);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$liner_resin	= $valx['liner_resin'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $liner_resin);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$liner_veil	= $valx['liner_veil'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $liner_veil);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$liner_csm	= $valx['liner_csm'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $liner_csm);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structure_resin	= $valx['structure_resin'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structure_resin);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structure_csm	= $valx['structure_csm'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structure_csm);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structure_wr	= $valx['structure_wr'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structure_wr);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structure_rooving	= $valx['structure_rooving'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structure_rooving);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structuren1_resin	= $valx['structuren1_resin'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structuren1_resin);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structuren1_csm	= $valx['structuren1_csm'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structuren1_csm);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structuren1_wr	= $valx['structuren1_wr'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structuren1_wr);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structuren1_rooving	= $valx['structuren1_rooving'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structuren1_rooving);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structuren2_resin	= $valx['structuren2_resin'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structuren2_resin);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structuren2_csm	= $valx['structuren2_csm'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structuren2_csm);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$structuren2_wr	= $valx['structuren2_wr'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $structuren2_wr);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$external_resin	= $valx['external_resin'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $external_resin);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$external_veil	= $valx['external_veil'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $external_veil);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$external_csm	= $valx['external_csm'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $external_csm);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$topcoat_resin	= $valx['topcoat_resin'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $topcoat_resin);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$joint_glass_veil	= $valx['joint_glass_veil'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $joint_glass_veil);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$joint_glass_wr	= $valx['joint_glass_wr'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $joint_glass_wr);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$joint_glass_csm	= $valx['joint_glass_csm'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $joint_glass_csm);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
  				$joint_resin	= $valx['joint_resin'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $joint_resin);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

  			}
  		}

		history('Download excel approve est '.$id_bq);
		
  		$sheet->setTitle($id_bq);
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
  		header('Content-Disposition: attachment;filename="check approval est '.$id_bq.' - '.date('YmdHis').'.xls"');
  		//unduh file
  		$objWriter->save("php://output");
  	}
	
	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	public function get_json_app_est(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approve_est";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_app_est(
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
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['rev']))."</span></div>";
				$class = Color_status($row['sts_ipp']);
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$class."'>".$row['sts_ipp']."</span></div>";
					$view		= "";
					$approve	= "";
					$excel		= "";
					
					if($Arr_Akses['read']=='1'){
						// if($row['estimasi'] == 'Y' AND $Check > 0){
							if($row['sts_ipp'] == 'WAITING APPROVE EST PROJECT'){
								$view	= "&nbsp;<button class='btn btn-sm btn-primary editBQ' title='Estimation BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
							}
						// }
					}
					
					if($Arr_Akses['approve']=='1'){
						if($row['approved_est'] == 'N'){
							// $app	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='ajuAppBQ' title='Approve BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
							$approve	= "&nbsp;<button type='button' class='btn btn-sm btn-success ajuAppBQNew' title='Approve BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						
						}
					}
					
					$check_excel = $this->db->get_where('laporan_excel_est_bq', array('id_bq'=>$row['id_bq']))->num_rows();
					if($check_excel > 0){
						$excel	= "&nbsp;<a href='".site_url($this->uri->segment(1)).'/approve_est_excel/'.$row['id_bq']."' class='btn btn-sm btn-info' title='Excel Approve Est' data-role='qtip'><i class='fa fa-file-excel-o'></i></a>";
					}

			$nestedData[]	= "<div align='left'>
									".$view."
									".$approve."
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

	public function get_query_json_app_est($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
 
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.project,
				b.status AS sts_ipp
			FROM
				bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp,
				(SELECT @row:=0) r  
		    WHERE a.approved_est = 'N' AND a.aju_approved_est = 'Y' AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer',
			3 => 'project',
			4 => 'order_type'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_json_app_bq(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/approve_bq";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_app_bq(
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
			$nestedData[]	= "<div align='center'>".$row['id_bq']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['rev']))."</span></div>";
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($row['created_by']))."</div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND id_category <> 'pipe slongsong' AND (id_product= '' OR id_product  is null) ")->num_rows();
				$class = Color_status($row['sts_ipp']);

			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['sts_ipp']."</span></div>";
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					// if($Arr_Akses['delete']=='1'){
						// $delX	= "&nbsp;<button class='btn btn-sm btn-danger' id='batalProduksi' title='Cancel Production' data-id_bq='".$row['id_bq']."'><i class='fa fa-trash'></i></button>";
					// }
					$app = "";
					$app2 = "";
					if($Arr_Akses['approve']=='1'){
						if($row['approved'] == 'N'){
							// $app	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='ajuAppBQ' title='Approve BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
							$app2	= "&nbsp;<button type='button' class='btn btn-sm btn-success ajuAppBQNew' title='Approve BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						
						}
					}
					
					
			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-warning detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$app."
									".$app2."
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

	public function get_query_json_app_bq($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.nm_customer,
				b.project,
				b.status AS sts_ipp
			FROM
				bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
		    WHERE a.aju_approved = 'Y' AND a.approved = 'N'
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'no_ipp'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
    
}
