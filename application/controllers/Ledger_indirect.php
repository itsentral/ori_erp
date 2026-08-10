<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_indirect extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Ledger_indirect_model');
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
		$list_material	= $this->Ledger_indirect_model->get_list_material();
		$data = array(
			'title'			=> 'Laporan Ledger Indirect',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'list_material'	=> $list_material,
			'akses_menu'	=> $Arr_Akses
		);

		$this->load->view('Report_new/Ledger_indirect/index', $data);
	}

	public function get_data_json(){
		$controller		= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses		= getAcccesmenu($controller);

		$bulan		= $this->input->get('bulan') ? $this->input->get('bulan') : date('m');
		$tahun		= $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');
		$code_group	= $this->input->get('code_group') ? $this->input->get('code_group') : '';

		$fetch = $this->Ledger_indirect_model->get_ledger_data($bulan, $tahun, $code_group);

		echo json_encode($fetch);
	}

	public function excel_ledger_indirect(){
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$bulan		= $this->uri->segment(3);
		$tahun		= $this->uri->segment(4);
		$code_group	= $this->uri->segment(5);

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

		$fetch = $this->Ledger_indirect_model->get_ledger_data($bulan, $tahun, $code_group);

		$Row = 1;
		$Col_Akhir = getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'LAPORAN LEDGER INDIRECT');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row++;
		$nm_material = (!empty($code_group)) ? $code_group : 'Semua Material';
		$sheet->setCellValue('A'.$Row, 'Periode : '.$Arr_Bulan[(int)$bulan].' '.$tahun.' | Material : '.$nm_material);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row += 2;
		$sheet->setCellValue('A'.$Row, 'Code Group');
		$sheet->setCellValue('B'.$Row, 'Material');
		$sheet->setCellValue('C'.$Row, 'Tanggal');
		$sheet->setCellValue('D'.$Row, 'No Trans');
		$sheet->setCellValue('E'.$Row, 'Keterangan');
		$sheet->setCellValue('F'.$Row, 'Gudang Dari');
		$sheet->setCellValue('G'.$Row, 'Gudang Ke');
		$sheet->setCellValue('H'.$Row, 'In');
		$sheet->setCellValue('I'.$Row, 'Out');
		$sheet->setCellValue('J'.$Row, 'Saldo');

		$sheet->getStyle('A'.$Row.':J'.$Row)->applyFromArray($tableHeader);
		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(18);
		$sheet->getColumnDimension('I')->setWidth(18);
		$sheet->getColumnDimension('J')->setWidth(22);

		$Row++;

		// Row saldo awal
		if(isset($fetch['saldo_awal']) && $fetch['saldo_awal'] != 0){
			$sheet->setCellValue('A'.$Row, 'SALDO AWAL');
			$sheet->mergeCells('A'.$Row.':G'.$Row);
			$sheet->setCellValue('H'.$Row, 0);
			$sheet->setCellValue('I'.$Row, 0);
			$sheet->setCellValue('J'.$Row, $fetch['saldo_awal']);
			$sheet->getStyle('A'.$Row.':J'.$Row)->applyFromArray($tableHeader);
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
				$sheet->setCellValue('A'.$Row, $det['code_group']);
				$sheet->setCellValue('B'.$Row, $det['material_name']);
				$sheet->setCellValue('C'.$Row, $det['tanggal']);
				$sheet->setCellValue('D'.$Row, $det['no_trans']);
				$sheet->setCellValue('E'.$Row, $det['keterangan']);
				$sheet->setCellValue('F'.$Row, $det['gudang_dari']);
				$sheet->setCellValue('G'.$Row, $det['gudang_ke']);
				$sheet->setCellValue('H'.$Row, $det['in']);
				$sheet->setCellValue('I'.$Row, $det['out']);
				$sheet->setCellValue('J'.$Row, $det['saldo']);
				$sheet->getStyle('A'.$Row.':J'.$Row)->applyFromArray($tableBodyRight);
				$sheet->getStyle('A'.$Row.':G'.$Row)->applyFromArray($tableBodyLeft);
				$Row++;
			}
			// Total row
			$sheet->setCellValue('G'.$Row, 'TOTAL');
			$sheet->setCellValue('H'.$Row, $totalIn);
			$sheet->setCellValue('I'.$Row, $totalOut);
			$sheet->setCellValue('J'.$Row, $lastSaldo);
			$sheet->getStyle('A'.$Row.':J'.$Row)->applyFromArray($tableHeader);
			$Row++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Ledger Indirect');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Ledger_Indirect_'.$Arr_Bulan[(int)$bulan].'_'.$tahun.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
}
