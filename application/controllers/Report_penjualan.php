<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_penjualan extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('report_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	//IN OUT MATERIAL
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$category			= $this->db->get('con_nonmat_category_awal')->result_array();
		$data = array(
			'title'			=> 'Report Penjualan',
			'action'		=> 'add',
			'category'		=> $category
		);
		$this->load->view('Report/penjualan',$data);
	}

	public function show_history(){
		$data 		= $this->input->post();
		$bulan 	= $data['bulan'];
		$tahun 	= $data['tahun'];

		$BlnThn = $tahun.'-'.$bulan;
		if($bulan == '0'){
			$BlnThn = $tahun.'-';
		}

		$result 	= $this->db
							->select('id_bq, MAX(revised_no) AS no_revisi')
							->group_by('id_bq')
							->like('insert_date',$BlnThn,'both')
							->get('laporan_revised_header')
							->result_array();


		$dataArr = [
			'result' => $result
		];

		$data_html = $this->load->view('Report/show_history_penjualan', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

    public function download_excel(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$bulan	= $this->uri->segment(3);
		$tahun	= $this->uri->segment(4);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
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
		
		$BlnThn = $tahun.'-'.$bulan;
		if($bulan == '0'){
			$BlnThn = $tahun.'-';
		}

		$result 	= $this->db
							->select('id_bq, MAX(revised_no) AS no_revisi')
							->group_by('id_bq')
							->like('insert_date',$BlnThn,'both')
							->get('laporan_revised_header')
							->result_array();

		// echo '<pre>';
		// print_r($result);
		// exit;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'REPORT PENJUALAN');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'INDUK PRODUK');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'LOKAL/EKSPORT');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'IPP');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'ID MILIK');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'PRODUCT');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'SPEC');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'REVENUE');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'MATERIAL');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$sheet->setCellValue('J'.$NewRow, 'DIRECT');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(10);

		$sheet->setCellValue('K'.$NewRow, 'INDIRECT');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(10);

		$sheet->setCellValue('L'.$NewRow, 'CONSUMABLE');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(10);

		$sheet->setCellValue('M'.$NewRow, 'MACHINE');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(10);

		$sheet->setCellValue('N'.$NewRow, 'MOULD MANDRILL');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(10);

		$sheet->setCellValue('O'.$NewRow, 'FOH');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setWidth(10);

		$sheet->setCellValue('P'.$NewRow, 'PROFIT');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setWidth(10);

		$sheet->setCellValue('Q'.$NewRow, 'ALLOWANCE');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setWidth(10);

		$sheet->setCellValue('R'.$NewRow, 'DATED');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setWidth(10);

		
		// echo '<pre>';	
		// print_r($GET_GUDANG); 
		// echo $GET_GUDANG['16JSON'];
		// exit;

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $value){
				
				$Tipe = substr($value['id_bq'], -1, 1);
                $dataDetail = $this->db->get_where('laporan_revised_detail',array('id_bq'=>$value['id_bq'],'revised_no'=>$value['no_revisi']))->result_array();
				foreach ($dataDetail as $key2 => $value2) { $no++;
					$awal_row++;
					$awal_col	= 0;

					$EKPIMP = ($Tipe == 'L')?'LOKAL':'EKSPORT';
					$type_induk = ($value2['product_parent'] == 'pipe')?'PIPE':'FITTING';

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $no);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $type_induk);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $EKPIMP);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$id_bq		= str_replace('BQ-','',$value2['id_bq']);
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $id_bq);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$id_milik		= $value2['id_milik'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $id_milik);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$product_parent		= $value2['product_parent'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $product_parent);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$spec		= spec_bq($id_milik);
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $spec);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$revenue    = $value2['total_price_last'];
                    $costmaterial    = $value2['est_harga'];
                    $direct    = $value2['direct_labour'];
                    $indirect    = $value2['indirect_labour'];
                    $consumable    = $value2['consumable'];
                    $machine    = $value2['machine'];
                    $mouldmandrill     = $value2['mould_mandrill'];
                    $foh    = $value2['foh_consumable'] + $value2['foh_depresiasi'] + $value2['biaya_gaji_non_produksi'] + $value2['biaya_non_produksi'] + $value2['biaya_rutin_bulanan'];
                    $profit     = ($value2['unit_price'] * $value2['qty']) * $value2['profit'] / 100;
                    $allowance     = ($value2['total_price']) * $value2['allowance'] / 100;

					$awal_col++;
					$id_milik		= $value2['id_milik'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $revenue);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $costmaterial);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $direct);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $indirect);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $consumable);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $machine);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $mouldmandrill);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $foh);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $profit);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $allowance);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$insert_date		= $value2['insert_date'];
					$Cols		= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $insert_date);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				}

				
			}
		}


		$sheet->setTitle('REPORT PURCHASING');
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
		header('Content-Disposition: attachment;filename="report-purchasing.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}
?>