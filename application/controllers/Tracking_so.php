<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking_so extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Tracking_so_model');

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

		$list_customer = $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();

		$data = array(
			'title'		=> 'Tracking SO',
			'action'	=> 'index',
			'list_customer' => $list_customer
		);
		$this->load->view('Tracking_so/index', $data);
	}

	public function get_po(){
		$id_customer = $this->input->post('id_customer');
		$result = $this->db->order_by('no_po','asc')
					->group_by('no_po')
					->get_where('billing_so', array('kode_customer'=>$id_customer, 'no_po <>'=>NULL, 'no_po <>'=>'0'))
					->result_array();

		$option = "<option value='0'>-- Select No. PO --</option>";
		foreach($result as $row){
			$option .= "<option value='".$row['no_po']."'>".$row['no_po']."</option>";
		}

		echo json_encode(array('status'=>1, 'option'=>$option));
	}

	public function get_so(){
		$no_po = $this->input->post('no_po');
		$result = $this->db->query("
			SELECT DISTINCT c.so_number, c.id_bq
			FROM billing_so a
			LEFT JOIN so_number c ON REPLACE(c.id_bq,'BQ-','') = a.no_ipp
			WHERE a.no_po = '".$this->db->escape_str($no_po)."'
			AND c.so_number IS NOT NULL
			ORDER BY c.so_number ASC
		")->result_array();

		$option = "<option value='0'>-- Select No. SO --</option>";
		if(!empty($result)){
			foreach($result as $row){
				$option .= "<option value='".$row['id_bq']."'>".$row['so_number']."</option>";
			}
		}

		echo json_encode(array('status'=>1, 'option'=>$option));
	}

	public function show_tracking(){
		$id_bq		= $this->input->post('id_bq');
		$no_po		= $this->input->post('no_po');

		$result = $this->Tracking_so_model->get_tracking_data($id_bq, $no_po);

		$dataArr = array(
			'result'	=> $result,
			'no_po'		=> $no_po
		);

		$data_html = $this->load->view('Tracking_so/show_tracking', $dataArr, TRUE);

		echo json_encode(array(
			'status'	=> 1,
			'data_html'	=> $data_html
		));
	}

	public function download_excel(){
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$id_bq	= $this->uri->segment(3);
		$no_po	= urldecode($this->uri->segment(4));

		$result = $this->Tracking_so_model->get_tracking_data($id_bq, $no_po);

		$this->load->library("PHPExcel");
		$objPHPExcel = new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'4472C4'),
			),
			'font' => array(
				'bold' => true,
				'color' => array('rgb'=>'FFFFFF'),
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => true
			)
		);

		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArrayLeft = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArrayRight = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$sheet = $objPHPExcel->getActiveSheet();

		// Title
		$sheet->setCellValue('A1', 'TRACKING SO - PO: '.$no_po);
		$sheet->mergeCells('A1:U1');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

		// Header Row 1
		$row = 3;
		$sheet->setCellValue('A'.$row, 'No. PO');
		$sheet->mergeCells('A'.$row.':A'.($row+1));
		$sheet->setCellValue('B'.$row, 'SO No');
		$sheet->mergeCells('B'.$row.':B'.($row+1));
		$sheet->setCellValue('C'.$row, 'No SPK');
		$sheet->mergeCells('C'.$row.':C'.($row+1));
		$sheet->setCellValue('D'.$row, 'Produk');
		$sheet->mergeCells('D'.$row.':D'.($row+1));
		$sheet->setCellValue('E'.$row, 'Spesifikasi');
		$sheet->mergeCells('E'.$row.':E'.($row+1));
		$sheet->setCellValue('F'.$row, 'Qty SO');
		$sheet->mergeCells('F'.$row.':F'.($row+1));
		$sheet->setCellValue('G'.$row, 'SPK');
		$sheet->mergeCells('G'.$row.':H'.$row);
		$sheet->setCellValue('I'.$row, 'PRODUKSI');
		$sheet->mergeCells('I'.$row.':K'.$row);
		$sheet->setCellValue('L'.$row, 'FG');
		$sheet->mergeCells('L'.$row.':M'.$row);
		$sheet->setCellValue('N'.$row, 'IN TRANSIT');
		$sheet->mergeCells('N'.$row.':P'.$row);
		$sheet->setCellValue('Q'.$row, 'CUSTOMER');
		$sheet->mergeCells('Q'.$row.':R'.$row);
		$sheet->setCellValue('S'.$row, 'Invoice');
		$sheet->mergeCells('S'.$row.':U'.$row);

		// Header Row 2
		$row2 = 4;
		$sheet->setCellValue('G'.$row2, 'R');
		$sheet->setCellValue('H'.$row2, 'O');
		$sheet->setCellValue('I'.$row2, 'R');
		$sheet->setCellValue('J'.$row2, 'O');
		$sheet->setCellValue('K'.$row2, 'D');
		$sheet->setCellValue('L'.$row2, 'R');
		$sheet->setCellValue('M'.$row2, 'O');
		$sheet->setCellValue('N'.$row2, 'R');
		$sheet->setCellValue('O'.$row2, 'O');
		$sheet->setCellValue('P'.$row2, 'No. Delivery');
		$sheet->setCellValue('Q'.$row2, 'R');
		$sheet->setCellValue('R'.$row2, 'O');
		$sheet->setCellValue('S'.$row2, 'No. Invoice');
		$sheet->setCellValue('T'.$row2, 'R');
		$sheet->setCellValue('U'.$row2, 'Nilai');

		$sheet->getStyle('A'.$row.':U'.$row2)->applyFromArray($style_header);

		// Data
		$dataRow = 5;
		if(!empty($result)){
			foreach($result as $item){
				$sheet->setCellValue('A'.$dataRow, $item['no_po']);
				$sheet->setCellValue('B'.$dataRow, $item['so_number']);
				$sheet->setCellValue('C'.$dataRow, $item['no_spk']);
				$sheet->setCellValue('D'.$dataRow, $item['product']);
				$sheet->setCellValue('E'.$dataRow, $item['spesifikasi']);
				$sheet->setCellValue('F'.$dataRow, $item['qty_so']);
				$sheet->setCellValue('G'.$dataRow, $item['spk_r']);
				$sheet->setCellValue('H'.$dataRow, $item['spk_o']);
				$sheet->setCellValue('I'.$dataRow, $item['prod_r']);
				$sheet->setCellValue('J'.$dataRow, $item['prod_o']);
				$sheet->setCellValue('K'.$dataRow, $item['prod_d']);
				$sheet->setCellValue('L'.$dataRow, $item['fg_r']);
				$sheet->setCellValue('M'.$dataRow, $item['fg_o']);
				$sheet->setCellValue('N'.$dataRow, $item['transit_r']);
				$sheet->setCellValue('O'.$dataRow, $item['transit_o']);
				$sheet->setCellValue('P'.$dataRow, $item['no_delivery']);
				$sheet->setCellValue('Q'.$dataRow, $item['cust_r']);
				$sheet->setCellValue('R'.$dataRow, $item['cust_o']);
				$sheet->setCellValue('S'.$dataRow, $item['no_invoice']);
				$sheet->setCellValue('T'.$dataRow, $item['inv_r']);
				$sheet->setCellValue('U'.$dataRow, $item['inv_nilai']);

				$sheet->getStyle('A'.$dataRow.':F'.$dataRow)->applyFromArray($styleArray);
				$sheet->getStyle('G'.$dataRow.':U'.$dataRow)->applyFromArray($styleArray);
				$dataRow++;
			}
		}

		// Auto size columns
		foreach(range('A','U') as $col){
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		$sheet->setTitle('Tracking SO');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="TRACKING_SO_'.date('YmdHis').'.xls"');
		$objWriter->save("php://output");
	}
}
?>
