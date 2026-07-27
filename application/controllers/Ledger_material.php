<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_material extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Ledger_material_model');
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
		$list_gudang	= $this->Ledger_material_model->get_list_gudang();
		$data = array(
			'title'			=> 'Laporan Ledger Material',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'list_gudang'	=> $list_gudang,
			'akses_menu'	=> $Arr_Akses
		);

		$this->load->view('Report_new/Ledger_material/index', $data);
	}

	public function get_data_json(){
		$controller		= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses		= getAcccesmenu($controller);

		$bulan		= $this->input->get('bulan') ? $this->input->get('bulan') : date('m');
		$tahun		= $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');
		$id_gudang	= $this->input->get('id_gudang') ? $this->input->get('id_gudang') : '';

		$fetch = $this->Ledger_material_model->get_ledger_data($bulan, $tahun, $id_gudang);

		echo json_encode($fetch);
	}

	public function excel_ledger_material(){
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$bulan		= $this->uri->segment(3);
		$tahun		= $this->uri->segment(4);
		$id_gudang	= $this->uri->segment(5);

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

		$fetch = $this->Ledger_material_model->get_ledger_data($bulan, $tahun, $id_gudang);

		$Row = 1;
		$Col_Akhir = getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'LAPORAN LEDGER MATERIAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row++;
		$sheet->setCellValue('A'.$Row, 'Periode : '.$Arr_Bulan[(int)$bulan].' '.$tahun.' | Gudang : '.$id_gudang);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$Row)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$Row);

		$Row += 2;
		$sheet->setCellValue('A'.$Row, 'Material');
		$sheet->setCellValue('B'.$Row, 'Kategori');
		$sheet->setCellValue('C'.$Row, 'Tanggal');
		$sheet->setCellValue('D'.$Row, 'Kode Trans');
		$sheet->setCellValue('E'.$Row, 'Keterangan');
		$sheet->setCellValue('F'.$Row, 'Harga');
		$sheet->setCellValue('G'.$Row, 'In');
		$sheet->setCellValue('H'.$Row, 'Out');
		$sheet->setCellValue('I'.$Row, 'Saldo');

		$sheet->getStyle('A'.$Row.':I'.$Row)->applyFromArray($tableHeader);
		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(18);
		$sheet->getColumnDimension('G')->setWidth(18);
		$sheet->getColumnDimension('H')->setWidth(18);
		$sheet->getColumnDimension('I')->setWidth(22);

		$Row++;

		if(!empty($fetch['data'])){
			$totalIn = 0;
			$totalOut = 0;
			foreach($fetch['data'] as $det){
				$totalIn += $det['in'];
				$totalOut += $det['out'];
				$sheet->setCellValue('A'.$Row, $det['nm_material']);
				$sheet->setCellValue('B'.$Row, $det['nm_category']);
				$sheet->setCellValue('C'.$Row, $det['tanggal']);
				$sheet->setCellValue('D'.$Row, $det['kode_trans']);
				$sheet->setCellValue('E'.$Row, $det['keterangan']);
				$sheet->setCellValue('F'.$Row, $det['harga']);
				$sheet->setCellValue('G'.$Row, $det['in']);
				$sheet->setCellValue('H'.$Row, $det['out']);
				$sheet->setCellValue('I'.$Row, $det['saldo']);
				$sheet->getStyle('A'.$Row.':I'.$Row)->applyFromArray($tableBodyRight);
				$sheet->getStyle('A'.$Row.':E'.$Row)->applyFromArray($tableBodyLeft);
				$Row++;
			}
			// Total row
			$sheet->setCellValue('F'.$Row, 'TOTAL');
			$sheet->setCellValue('G'.$Row, $totalIn);
			$sheet->setCellValue('H'.$Row, $totalOut);
			$sheet->setCellValue('I'.$Row, $totalIn - $totalOut);
			$sheet->getStyle('A'.$Row.':I'.$Row)->applyFromArray($tableHeader);
			$Row++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Ledger Material');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Ledger_Material_'.$Arr_Bulan[(int)$bulan].'_'.$tahun.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
}
