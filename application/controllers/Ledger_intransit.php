<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_intransit extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Ledger_intransit_model');
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

		$menu_akses	= $this->master_model->getMenu();
		$data = array(
			'title'			=> 'Laporan Ledger In Transit',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);

		$this->load->view('Report_new/Ledger_intransit/index', $data);
	}

	public function get_data_json(){
		$controller		= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses		= getAcccesmenu($controller);

		$bulan	= $this->input->get('bulan') ? $this->input->get('bulan') : date('m');
		$tahun	= $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');

		$fetch = $this->Ledger_intransit_model->get_ledger_data($bulan, $tahun);

		echo json_encode($fetch);
	}

	public function excel_ledger_intransit(){
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$bulan	= $this->uri->segment(3);
		$tahun	= $this->uri->segment(4);

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

		$fetch = $this->Ledger_intransit_model->get_ledger_data($bulan, $tahun);

		$Row = 1;
		$Col_Akhir = getColsChar(7);
		$sheet->setCellValue('A'.$Row, 'LAPORAN LEDGER IN TRANSIT');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row++;
		$sheet->setCellValue('A'.$Row, 'Periode : '.$Arr_Bulan[(int)$bulan].' '.$tahun);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row += 2;
		$sheet->setCellValue('A'.$Row, 'Keterangan');
		$sheet->setCellValue('B'.$Row, 'Tanggal Bukti');
		$sheet->setCellValue('C'.$Row, 'Nomor Bukti');
		$sheet->setCellValue('D'.$Row, 'SM');
		$sheet->setCellValue('E'.$Row, 'In');
		$sheet->setCellValue('F'.$Row, 'Out');
		$sheet->setCellValue('G'.$Row, 'Saldo');

		$sheet->getStyle('A'.$Row.':G'.$Row)->applyFromArray($tableHeader);
		$sheet->getColumnDimension('A')->setWidth(40);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(22);

		$Row++;

		if(!empty($fetch['data'])){
			$totalIn = 0;
			$totalOut = 0;
			foreach($fetch['data'] as $group){
				// Header row saldo awal
				$sheet->setCellValue('A'.$Row, $group['nama']);
				$sheet->setCellValue('D'.$Row, 'Saldo Awal ->');
				$sheet->setCellValue('G'.$Row, $group['saldo_awal']);
				$sheet->getStyle('A'.$Row.':G'.$Row)->applyFromArray($tableBodyLeft);
				$sheet->getStyle('A'.$Row)->getFont()->setBold(true);
				$Row++;

				if(!empty($group['detail'])){
					foreach($group['detail'] as $det){
						$totalIn += $det['in'];
						$totalOut += $det['out'];
						$sheet->setCellValue('A'.$Row, $det['keterangan']);
						$sheet->setCellValue('B'.$Row, $det['tanggal']);
						$sheet->setCellValue('C'.$Row, $det['nomor_bukti']);
						$sheet->setCellValue('D'.$Row, $det['sm']);
						$sheet->setCellValue('E'.$Row, $det['in']);
						$sheet->setCellValue('F'.$Row, $det['out']);
						$sheet->setCellValue('G'.$Row, $det['saldo']);
						$sheet->getStyle('A'.$Row.':G'.$Row)->applyFromArray($tableBodyRight);
						$sheet->getStyle('A'.$Row.':D'.$Row)->applyFromArray($tableBodyLeft);
						$Row++;
					}
				}
			}
			// Total row
			$sheet->setCellValue('D'.$Row, 'TOTAL');
			$sheet->setCellValue('E'.$Row, $totalIn);
			$sheet->setCellValue('F'.$Row, $totalOut);
			$sheet->setCellValue('G'.$Row, $totalIn - $totalOut);
			$sheet->getStyle('A'.$Row.':G'.$Row)->applyFromArray($tableHeader);
			$Row++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Ledger In Transit');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Ledger_InTransit_'.$Arr_Bulan[(int)$bulan].'_'.$tahun.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
}
