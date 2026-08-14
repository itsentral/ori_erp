<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_produksi extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Ledger_produksi_model');
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	public function index(){
		$controller		= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses		= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$menu_akses		= $this->master_model->getMenu();
		$data = array(
			'title'			=> 'Laporan Ledger Produksi',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);

		$this->load->view('Report_new/Ledger_produksi/index', $data);
	}

	public function get_data_json(){
		$bulan		= $this->input->get('bulan') ? $this->input->get('bulan') : date('m');
		$tahun		= $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');

		$fetch = $this->Ledger_produksi_model->get_ledger_data($bulan, $tahun);

		echo json_encode($fetch);
	}

	public function excel_ledger_produksi(){
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$bulan		= $this->uri->segment(3);
		$tahun		= $this->uri->segment(4);

		$this->load->library("PHPExcel");
		$objPHPExcel = new PHPExcel();

		$whiteCenterBold	= whiteCenterBold();
		$whiteRightBold		= whiteRightBold();
		$whiteCenter		= whiteCenter();
		$mainTitle			= mainTitle();
		$tableHeader		= tableHeader();
		$tableBodyCenter	= tableBodyCenter();
		$tableBodyLeft		= tableBodyLeft();
		$tableBodyRight		= tableBodyRight();

		$Arr_Bulan = array(1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$sheet = $objPHPExcel->getActiveSheet();

		$fetch = $this->Ledger_produksi_model->get_ledger_data($bulan, $tahun);

		$Row = 1;
		$Col_Akhir = getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'LAPORAN LEDGER PRODUKSI');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row++;
		$sheet->setCellValue('A'.$Row, 'Periode : '.$Arr_Bulan[(int)$bulan].' '.$tahun);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row += 2;
		$sheet->setCellValue('A'.$Row, 'ID Material');
		$sheet->setCellValue('B'.$Row, 'Material');
		$sheet->setCellValue('C'.$Row, 'Kategori');
		$sheet->setCellValue('D'.$Row, 'Qty');
		$sheet->setCellValue('E'.$Row, 'Tanggal');
		$sheet->setCellValue('F'.$Row, 'Kode Trans');
		$sheet->setCellValue('G'.$Row, 'Keterangan');
		$sheet->setCellValue('H'.$Row, 'Harga');
		$sheet->setCellValue('I'.$Row, 'In');
		$sheet->setCellValue('J'.$Row, 'Out');
		$sheet->setCellValue('K'.$Row, 'Saldo');

		$sheet->getStyle('A'.$Row.':K'.$Row)->applyFromArray($tableHeader);
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(12);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(25);
		$sheet->getColumnDimension('G')->setWidth(30);
		$sheet->getColumnDimension('H')->setWidth(18);
		$sheet->getColumnDimension('I')->setWidth(18);
		$sheet->getColumnDimension('J')->setWidth(18);
		$sheet->getColumnDimension('K')->setWidth(22);

		$Row++;

		// Row saldo awal
		if(isset($fetch['saldo_awal']) && $fetch['saldo_awal'] != 0){
			$sheet->setCellValue('A'.$Row, 'SALDO AWAL');
			$sheet->mergeCells('A'.$Row.':G'.$Row);
			$sheet->setCellValue('H'.$Row, 0);
			$sheet->setCellValue('I'.$Row, 0);
			$sheet->setCellValue('J'.$Row, 0);
			$sheet->setCellValue('K'.$Row, $fetch['saldo_awal']);
			$sheet->getStyle('A'.$Row.':K'.$Row)->applyFromArray($tableHeader);
			$Row++;
		}

		if(!empty($fetch['data'])){
			$totalIn = 0;
			$totalOut = 0;
			$lastSaldo = 0;
			foreach($fetch['data'] as $det){
				$totalIn += $det['in'];
				$totalOut += $det['out'];
				$lastSaldo = $det['saldo'];
				$sheet->setCellValue('A'.$Row, $det['id_material']);
				$sheet->setCellValue('B'.$Row, $det['nm_material']);
				$sheet->setCellValue('C'.$Row, $det['nm_category']);
				$sheet->setCellValue('D'.$Row, $det['qty']);
				$sheet->setCellValue('E'.$Row, $det['tanggal']);
				$sheet->setCellValue('F'.$Row, $det['kode_trans']);
				$sheet->setCellValue('G'.$Row, $det['keterangan']);
				$sheet->setCellValue('H'.$Row, $det['harga']);
				$sheet->setCellValue('I'.$Row, $det['in']);
				$sheet->setCellValue('J'.$Row, $det['out']);
				$sheet->setCellValue('K'.$Row, $det['saldo']);
				$sheet->getStyle('A'.$Row.':K'.$Row)->applyFromArray($tableBodyRight);
				$sheet->getStyle('A'.$Row.':G'.$Row)->applyFromArray($tableBodyLeft);
				$Row++;
			}
			// Total row
			$sheet->setCellValue('H'.$Row, 'TOTAL');
			$sheet->setCellValue('I'.$Row, $totalIn);
			$sheet->setCellValue('J'.$Row, $totalOut);
			$sheet->setCellValue('K'.$Row, $lastSaldo);
			$sheet->getStyle('A'.$Row.':K'.$Row)->applyFromArray($tableHeader);
			$Row++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Ledger Produksi');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Ledger_Produksi_'.$Arr_Bulan[(int)$bulan].'_'.$tahun.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
}
