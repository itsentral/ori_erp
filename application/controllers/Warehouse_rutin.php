<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse_rutin extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');;
		$this->load->model('warehouse_rutin_model');
		$this->load->model('adjustment_rutin_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

	//==========================================================================================================================
	//========================================================INCOMING==========================================================
	//==========================================================================================================================
	
	public function incoming(){
		$this->warehouse_rutin_model->incoming();
	}

	public function incoming_consumable(){
		$this->warehouse_rutin_model->incoming_consumable();
	}

	public function incoming_household(){
		$this->warehouse_rutin_model->incoming_household();
	}
	
	public function server_side_incoming(){
		$this->warehouse_rutin_model->get_data_json_incoming();
	}
	
	public function modal_incoming(){
		$data = $this->input->post();
		
		$gudang_before 	= $data['gudang_before'];
		$no_po 			= $data['no_po'];
		$pic 			= strtolower($data['pic']);
		$note 			= strtolower($data['note']);
		$no_ros			= $data['no_ros'];
		$tanggal_trans	= $data['tanggal_trans'];
		$category		= $data['category'];

		$sql 	= "	SELECT 
						a.* 
					FROM 
						tran_po_detail a 
						LEFT JOIN con_nonmat_new b ON a.id_barang = b.code_group
					WHERE 
						a.no_po='".$no_po."' 
						AND b.category_awal IN ($category)
						AND a.qty_in < a.qty_po";
		$result	= $this->db->query($sql)->result_array();
		
		$data = array(
			'no_po' => $no_po,
			'tanggal_trans'=> $tanggal_trans,
			'gudang'=> $gudang_before,
			'pic' 	=> $pic,
			'note' 	=> $note,
			'no_ros'=> $no_ros,
			'result'=> $result
		);
		
		$this->load->view('Warehouse_rutin/modal_incoming', $data);
	}
	
	public function process_incoming(){
		$this->warehouse_rutin_model->process_incoming();
	}
	
	public function modal_detail_adjustment(){
		$this->warehouse_rutin_model->modal_detail_adjustment();
	}
	
	
	//==========================================================================================================================
	//======================================================STCCK==============================================================
	//==========================================================================================================================
	
	public function stock(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/stock';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();
		$data = array(
			'title'			=> 'Warehouse Stok >> Stock',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View Consumable Stock');
		$this->load->view('Warehouse_rutin/stock',$data);
	}
	
	public function server_side_stock(){
		$this->warehouse_rutin_model->get_data_json_stock();
	}
	
	//==========================================================================================================================
	//==============================================CHECKING MATERIAL===========================================================
	//==========================================================================================================================
	
	public function checking(){
		$this->warehouse_rutin_model->checking();
	}
	
	public function server_side_checking(){
		$this->warehouse_rutin_model->get_data_json_checking();
	}
	
	public function modal_incoming_check(){
		$this->warehouse_rutin_model->modal_incoming_check();
	}
	
	public function process_checking(){
		$this->warehouse_rutin_model->process_checking();
	}
	
	public function print_incoming_check(){
		$this->warehouse_rutin_model->print_incoming_check();
	}
	
	//==========================================================================================================================
	//================================================== OUTGOING ==============================================================
	//==========================================================================================================================
	
	public function outgoing(){
		$this->warehouse_rutin_model->outgoing();
	}
	
	public function server_side_outgoing(){
		$this->warehouse_rutin_model->get_data_json_outgoing();
	}
	
	public function modal_outgoing(){
		$this->warehouse_rutin_model->modal_outgoing();
	}
	
	public function server_side_modal_outgoing(){
		$this->warehouse_rutin_model->get_data_json_modal_outgoing();
	}
	
	public function process_outgoing(){
		$this->warehouse_rutin_model->process_outgoing();
	}
	
	public function print_outgoing_rutin(){
		$this->warehouse_rutin_model->print_outgoing_rutin();
	}
	
	public function save_temp_mutasi(){
		$this->warehouse_rutin_model->save_temp_mutasi();
	}
	
	//==========================================================================================================================
	//===============================================ADJUSTMENT MATERIAL========================================================
	//==========================================================================================================================
	
	public function adjustment(){
		$this->adjustment_rutin_model->adjustment();
	}
	
	public function server_side_adjustment(){
		$this->adjustment_rutin_model->get_data_json_adjustment();
	}
	
	public function add_adjustment(){
		$this->adjustment_rutin_model->add_adjustment();
	}
	
	public function excel_adjustment(){
		$this->adjustment_rutin_model->excel_adjustment();
	}
	
	//==========================================================================================================================
	//================================================SUMMARY CONSUMABLE========================================================
	//==========================================================================================================================
	
	public function summary(){
		$this->warehouse_rutin_model->summary();
	}
	
	public function server_side_summary(){
		$this->warehouse_rutin_model->get_data_json_summary();
	}
	
	//==========================================================================================================================
	//==================================================DETAIL TRANSAKSI========================================================
	//==========================================================================================================================
	
	public function detil_transaction(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/stock';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();

		$data_material = $this->db->group_by('code_group')->get_where('con_nonmat_new',array('deleted_date'=>NULL))->result_array();
		$data = array(
			'title'			=> 'Warehouse Stok >> Detail Transaction',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang,
			'data_material'=> $data_material
		);
		history('View consumable detil_transaction');
		$this->load->view('Warehouse_rutin/detil_transaction',$data);
	}
	
	public function server_side_detil_transaction(){
		$this->warehouse_rutin_model->get_data_json_detil_transaction();
	}

	public function download_excel_stok(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$material	= $this->uri->segment(3);
		$tgl_awal	= $this->uri->segment(4);
		$tgl_akhir	= $this->uri->segment(5);

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
		
		$where_gudang ='';
		if(!empty($material)){
			$where_gudang = " AND a.code_group = '".$material."' ";
		}

		$where_daterange ='';
		if($tgl_awal != '0'){
			$where_daterange = " AND DATE(a.update_date) BETWEEN '".date('Y-m-d',strtotime($tgl_awal))."' AND '".date('Y-m-d',strtotime($tgl_akhir))."' ";
		}
		$SQL = "SELECT a.*, b.material_name AS material_name_new FROM warehouse_rutin_history a LEFT JOIN con_nonmat_new b ON a.code_group = b.code_group AND b.deleted_date IS NULL WHERE 1=1 ".$where_gudang." ".$where_daterange." ORDER BY a.id DESC";
		$result = $this->db->query($SQL)->result_array();
		// echo '<pre>';
		// print_r($result);
		// exit;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'REPORT HISTORY TRANSAKSI STOK');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'TYPE');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'TANGGAL');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'NO TRANS');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'COSTCENTER');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'CATEGORY');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'NM BARANG');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'SPESIFIKASI');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$sheet->setCellValue('J'.$NewRow, 'QTY');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(10);

		$sheet->setCellValue('K'.$NewRow, 'KETERANGAN');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(10);

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$ket		= strtoupper($row_Cek['ket']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ket);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$update_date	= date('d-M-Y H:i:s', strtotime($row_Cek['update_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $update_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$no_trans	= strtoupper($row_Cek['no_trans']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$gudang_ke		= strtoupper($row_Cek['gudang_ke']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang_ke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$Category_awal = get_name('con_nonmat_new', 'category_awal', 'code_group', $row_Cek['code_group']);
				$Nm_Category = get_name('con_nonmat_category_awal', 'category', 'id', $Category_awal);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Nm_Category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$code_group		= $row_Cek['code_group'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material_name		= $row_Cek['material_name_new'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$code_group		= strtoupper(get_name('con_nonmat_new', 'spec', 'code_group', $row_Cek['code_group']));
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$jumlah_qty		= $row_Cek['jumlah_qty'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $jumlah_qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$get_ket = $this->db->select('keterangan')->get_where('warehouse_adjustment_detail', array('kode_trans'=>$row_Cek['no_trans'],'id_material'=>$row_Cek['code_group']))->result();
				$ket = (!empty($get_ket[0]->keterangan))?$get_ket[0]->keterangan:'';

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ket);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
			}
		}


		$sheet->setTitle('HISTORY TRANSACTION');
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
		header('Content-Disposition: attachment;filename="history-transaksi-stok.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function show_history(){
		$data = $this->input->post();
		$material = $data['material'];
		$tgl_awal = $data['tgl_awal'];
		$tgl_akhir = $data['tgl_akhir'];

		$where_gudang ='';
		if(!empty($material)){
			$where_gudang = " AND a.code_group = '".$material."' ";
		}

		$where_daterange ='';
		if($tgl_awal != '0'){
			$where_daterange = " AND DATE(a.update_date) BETWEEN '".date('Y-m-d',strtotime($tgl_awal))."' AND '".date('Y-m-d',strtotime($tgl_akhir))."' ";
		}
		$SQL = "SELECT a.*, b.material_name AS material_name_new FROM warehouse_rutin_history a LEFT JOIN con_nonmat_new b ON a.code_group = b.code_group AND b.deleted_date IS NULL WHERE 1=1 ".$where_gudang." ".$where_daterange." ORDER BY a.id DESC";
		$result = $this->db->query($SQL)->result_array();

		$dataArr = [
			'result' => $result
		];

		$data_html = $this->load->view('Warehouse_rutin/show_history', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function download_excel_stok_hist(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$material	= $this->uri->segment(3);
		$tgl_awal	= $this->uri->segment(4);
		$tgl_akhir	= $this->uri->segment(5);

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
		
		$where_gudang ='';
		if(!empty($material)){
			$where_gudang = " AND a.code_group = '".$material."' ";
		}

		$where_daterange ='';
		if($tgl_awal != '0'){
			$where_daterange = " AND DATE(a.update_date) BETWEEN '".date('Y-m-d',strtotime($tgl_awal))."' AND '".date('Y-m-d',strtotime($tgl_akhir))."' ";
		}
		$SQL = "SELECT a.*, b.material_name AS material_name_new FROM warehouse_rutin_history a LEFT JOIN con_nonmat_new b ON a.code_group = b.code_group AND b.deleted_date IS NULL WHERE 1=1 ".$where_gudang." ".$where_daterange." ORDER BY a.id DESC";
		$result = $this->db->query($SQL)->result_array();
		// echo '<pre>';
		// print_r($result);
		// exit;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'REPORT HISTORY TRANSAKSI STOK IN OUT');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NO DOCUMENT');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'NM MATERIAL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'GUDANG DARI');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'GUDANG KE');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'QTY');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'STOK AWAL');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'STOK AKHIR');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$sheet->setCellValue('J'.$NewRow, 'KETERANGAN');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(10);

		$sheet->setCellValue('K'.$NewRow, 'DATED');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(10);

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$no_trans		= strtoupper($row_Cek['no_trans']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$code_group		= strtoupper($row_Cek['code_group']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material_name		= $row_Cek['material_name_new'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$gudang_dari	= strtoupper($row_Cek['gudang_dari']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang_dari);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$gudang_ke	= strtoupper($row_Cek['gudang_ke']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang_ke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$jumlah_qty		= $row_Cek['jumlah_qty'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $jumlah_qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$qty_stock_awal		= $row_Cek['qty_stock_awal'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock_awal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$qty_stock_akhir		= $row_Cek['qty_stock_akhir'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock_akhir);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$get_ket = $this->db->select('keterangan')->get_where('warehouse_adjustment_detail', array('kode_trans'=>$row_Cek['no_trans'],'id_material'=>$row_Cek['code_group']))->result();
				$ket = (!empty($get_ket[0]->keterangan))?$get_ket[0]->keterangan:'';

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ket);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$update_date	= date('d-M-Y H:i:s', strtotime($row_Cek['update_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $update_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
			}
		}


		$sheet->setTitle('HISTORY IN OUT');
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
		header('Content-Disposition: attachment;filename="history-transaksi-stok-in-out.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function ExcelGudangStok(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$gudang			= $this->uri->segment(3);
		$date_filter	= $this->uri->segment(4);

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

		$table = "warehouse_rutin_stock";
		$where_gudang ='';
		$where_date ='';

		if($gudang != '0'){
			$where_gudang = " AND b.category_awal = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			if($gudang != '0'){
				$where_gudang = " AND b.category_awal = '".$gudang."' ";
			}
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_rutin_stock_per_day";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.material_name AS nama_master,
				b.spec,
				b.code_group,
				b.kode_item,
				b.kode_excel,
				b.category_awal AS category
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.gudang IN ('".getGudangIndirect()."','".getGudangHouseHold()."') ".$where_gudang." ".$where_date." AND b.status='1' AND b.deleted = 'N'
		";
		$restDetail1	= $this->db->query($sql)->result_array();
		
		$tanggal_update = (!empty($date_filter))?" (".date('d F Y', strtotime($date_filter)).")":" (".date('d F Y').")";

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'STOCK - '.$tanggal_update);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE ITEM');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'KODE EXCEL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'SPEC');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'STOCK');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'STOCK NG');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'GUDANG');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);


		// echo $qDetail1; exit;

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$code_group	= $row_Cek['code_group'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_item	= strtoupper($row_Cek['kode_item']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_item);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_excel	= $row_Cek['kode_excel'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_excel);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$CATEGORY = get_name('con_nonmat_category_awal','category','id',$row_Cek['category']);
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $CATEGORY);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material_name	= strtoupper($row_Cek['nama_master']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$spec	= strtoupper($row_Cek['spec']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$stock	= $row_Cek['stock'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$rusak	= $row_Cek['rusak'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $rusak);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$gudang = strtoupper(get_name('warehouse', 'nm_gudang', 'id', $row_Cek['gudang']));
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
			}
		}


		$sheet->setTitle('Stock');
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
		header('Content-Disposition: attachment;filename="stok-warehouse-stock.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
}

?>