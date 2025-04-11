<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Progress_po extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

    // COST CONTROL
    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$po_material		= $this->db->select('a.no_po, b.nm_supplier')->group_by('a.no_po')->join('tran_material_po_header b','a.no_po=b.no_po')->get('tran_material_po_detail a')->result_array();
		$po_stok		    = $this->db->select('a.no_po, b.nm_supplier')->group_by('a.no_po')->join('tran_po_header b','a.no_po=b.no_po')->get('tran_po_detail a')->result_array();

		$data = array(
			'title'			=> 'Progress PO',
			'action'		=> 'add',
			'sales_order'	=> array_merge($po_material,$po_stok)
		);
		$this->load->view('Progress/po/index',$data);
	}

    public function show_history(){
		$data 			= $this->input->post();
		$no_po 	        = $data['sales_order'];
		$table 	        = ($data['type'] == 'material')?'tran_material_po_detail':'tran_po_detail';

		$SQL = "( SELECT
					a.id AS id,
					a.no_po AS no_po,
					z.nm_supplier AS nm_supplier,
					'material' AS category,
					a.nm_material AS nm_material,
					a.created_date AS po_create,
					a.created_by AS created_by,
					c.qty_revisi AS qty_pr,
					b.qty AS qty_rfq,
					a.qty_purchase AS qty_po,(
						b.qty - b.qty_po 
					) AS outstanding_po,
					a.qty_in AS qty_incoming,(
						a.qty_purchase - a.qty_in 
					) AS outstanding_incoming 
					FROM
						(((
									tran_material_po_detail a
									LEFT JOIN tran_material_po_header z ON ((
											a.no_po = z.no_po 
										)))
								LEFT JOIN tran_material_rfq_detail b ON (((
											a.no_po = b.no_po 
											) 
									AND ( a.id_material = b.id_material ))))
							LEFT JOIN tran_material_pr_detail c ON (((
										b.no_rfq = c.no_rfq 
										) 
								AND ( b.id_material = c.id_material )))) WHERE a.no_po = '".$no_po."') UNION
					(
					SELECT
						a.id AS id,
						a.no_po AS no_po,
						z.nm_supplier AS nm_supplier,
						z.category AS category,
						a.nm_barang AS nm_material,
						a.created_date AS po_create,
						a.created_by AS created_by,
						c.qty AS qty_pr,
						b.qty AS qty_rfq,
						a.qty_po AS qty_po,(
							b.qty - b.qty_po 
						) AS outstanding_po,
						a.qty_in AS qty_incoming,(
							a.qty_purchase - a.qty_in 
						) AS outstanding_incoming 
					FROM
						(((
									tran_po_detail a
									LEFT JOIN tran_po_header z ON ((
											a.no_po = z.no_po 
										)))
								LEFT JOIN tran_rfq_detail b ON (((
											a.no_po = b.no_po 
											) 
									AND ( a.id_barang = b.id_barang ))))
							LEFT JOIN tran_pr_detail c ON (((
										b.no_rfq = c.no_rfq 
									) 
					AND ( b.id_barang = c.id_barang )))) WHERE a.no_po = '".$no_po."')
                ";
		$result 		= $this->db->query($SQL)->result_array();

		$dataArr = [
			'result' => $result
		];

		$data_html = $this->load->view('Progress/po/detail', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function download_excel($no_po){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

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

		
		$SQL = "( SELECT
					a.id AS id,
					a.no_po AS no_po,
					z.nm_supplier AS nm_supplier,
					'material' AS category,
					a.nm_material AS nm_material,
					a.created_date AS po_create,
					a.created_by AS created_by,
					c.qty_revisi AS qty_pr,
					b.qty AS qty_rfq,
					a.qty_purchase AS qty_po,(
						b.qty - b.qty_po 
					) AS outstanding_po,
					a.qty_in AS qty_incoming,(
						a.qty_purchase - a.qty_in 
					) AS outstanding_incoming 
					FROM
						(((
									tran_material_po_detail a
									LEFT JOIN tran_material_po_header z ON ((
											a.no_po = z.no_po 
										)))
								LEFT JOIN tran_material_rfq_detail b ON (((
											a.no_po = b.no_po 
											) 
									AND ( a.id_material = b.id_material ))))
							LEFT JOIN tran_material_pr_detail c ON (((
										b.no_rfq = c.no_rfq 
										) 
								AND ( b.id_material = c.id_material )))) WHERE a.no_po = '".$no_po."') UNION
					(
					SELECT
						a.id AS id,
						a.no_po AS no_po,
						z.nm_supplier AS nm_supplier,
						z.category AS category,
						a.nm_barang AS nm_material,
						a.created_date AS po_create,
						a.created_by AS created_by,
						c.qty AS qty_pr,
						b.qty AS qty_rfq,
						a.qty_po AS qty_po,(
							b.qty - b.qty_po 
						) AS outstanding_po,
						a.qty_in AS qty_incoming,(
							a.qty_purchase - a.qty_in 
						) AS outstanding_incoming 
					FROM
						(((
									tran_po_detail a
									LEFT JOIN tran_po_header z ON ((
											a.no_po = z.no_po 
										)))
								LEFT JOIN tran_rfq_detail b ON (((
											a.no_po = b.no_po 
											) 
									AND ( a.id_barang = b.id_barang ))))
							LEFT JOIN tran_pr_detail c ON (((
										b.no_rfq = c.no_rfq 
									) 
					AND ( b.id_barang = c.id_barang )))) WHERE a.no_po = '".$no_po."')
                ";
		// echo $SQL;
		// exit;
		$result 	= $this->db->query($SQL)->result_array();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'PROGRESS PO '.$no_po);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow1= $NewRow + 1;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'No PO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow1);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'Supplier');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow1);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow1);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Qty PR');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow1);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'Qty RFQ');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow1);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'PO');
		$sheet->getStyle('G'.$NewRow.':H'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':H'.$NextRow1);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'INCOMING');
		$sheet->getStyle('I'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('G'.$NewRow, 'PO');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Outstanding');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'IN');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Outstanding');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $value){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$QTY_PR    = $value['qty_pr'];
                $QTY_RFQ   = $value['qty_rfq'];

                $qty_po                 = ($value['qty_po'] > 0)?number_format($value['qty_po'],2):'-';
                $outstanding_po         = ($value['outstanding_po'] > 0)?number_format($value['outstanding_po'],2):'-';
                $qty_incoming           = ($value['qty_incoming'] > 0)?number_format($value['qty_incoming'],2):'-';
                $outstanding_incoming   = ($value['outstanding_incoming'] > 0)?number_format($value['outstanding_incoming'],2):'-';

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_po 		= $value['no_po'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_po);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_supplier	= strtoupper($value['nm_supplier']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_supplier);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= strtoupper($value['nm_material']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_PR);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_RFQ);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $value['qty_po']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $value['outstanding_po']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $value['qty_incoming']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $value['outstanding_incoming']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('PO');
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
		header('Content-Disposition: attachment;filename="report-progress-'.$no_po.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}