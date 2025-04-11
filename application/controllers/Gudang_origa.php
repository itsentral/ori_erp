<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang_origa extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		// Your own constructor code
		if (!$this->session->userdata('isORIlogin')) {
			redirect('login');
		}
	}

	public function index()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Gudang Origa',
			'action'		=> 'index',
			'row_group'		=> $data_Group
		);
		history('View data gudang origa');
		$this->load->view('Gudang_origa/index', $data);
	}

	public function server_side_gudang_origa()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_gudang_origa(
			$requestData['date_filter'],
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

		$FLAG = '';
		$GET_DET_IPP = get_detail_ipp();
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_barang']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_barang']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_origa'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_ftackle'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['cost_book'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_origa'] * $row['cost_book'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_ftackle'] * $row['cost_book'],2)."</div>";
			
			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_gudang_origa($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		if($date_filter == ''){
		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*
				FROM
					gudang_origa a,
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.deleted_date IS NULL
					AND (
						a.no_barang LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.nm_barang LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
			";
		}
		// else{
		// 	$where = " AND a.category='produksi'";

		// 	$sql = "
		// 		SELECT
		// 			(@row:=@row+1) AS nomor,
		// 			a.no_ipp,
		// 			a.id_milik,
		// 			a.product AS id_category,
		// 			a.no_spk,
		// 			a.sts,
		// 			COUNT(a.id) AS qty_product
		// 		FROM
		// 			stock_barang_jadi_per_day a,
		// 			(SELECT @row:=0) r
		// 		WHERE 1=1
		// 			AND DATE(a.hist_date) = '".$date_filter."'
		// 			".$where."
		// 			AND (
		// 				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 				OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 				OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 				OR a.nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			)
		// 		GROUP BY a.sts, a.no_spk, a.spool_induk
		// 	";
		// }
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_barang',
			2 => 'nm_barang',
			3 => 'qty_origa',
			4 => 'qty_ftackle',
			5 => 'price_origa',
			6 => 'price_ftackle'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function ExcelGudangOriga($date_filter){
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

		
		$where = "";
		// if($date_filter == '0'){
			$sql = "SELECT
					a.*
				FROM
					gudang_origa a
				WHERE 1=1 
					AND a.deleted_date IS NULL
			";
		// }
		// else{

		// 	$where = " AND a.category='produksi'";

		// 	$sql = "
		// 		SELECT
		// 			a.no_ipp,
		// 			a.id_milik,
		// 			a.no_spk,
		// 			a.sts,
		// 			a.product AS id_category,
		// 			COUNT(a.id) AS qty_product
		// 		FROM
		// 			stock_barang_jadi_per_day a,
		// 			(SELECT @row:=0) r
		// 		WHERE 1=1
		// 			AND DATE(a.hist_date) = '".$date_filter."'
		// 			".$where."
		// 		GROUP BY a.sts, a.no_spk, a.spool_induk
		// 	";
		// }
		// ECHO $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		$tanggal = ($date_filter != '0')?date('d-M-Y',strtotime($date_filter)):'';

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'GUDANG ORIGA '.$tanggal);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NO BARANG');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'DESC BARANG');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'GUDANG PVC ORIGA');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'RM LOKAL F-TACKLE');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'COST BOOK');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'TOTAL PRICE GUDANG PVC ORIGA');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'TOTAL PRICE RM LOKAL F-TACKLE');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

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
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_barang			= strtoupper($row_Cek['no_barang']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_barang			= strtoupper($row_Cek['nm_barang']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_origa			= $row_Cek['qty_origa'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_origa);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$qty_ftackle			= $row_Cek['qty_ftackle'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_ftackle);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$cost_book			= $row_Cek['cost_book'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost_book);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$no_spk			= $qty_origa * $cost_book;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$no_spk			= $qty_ftackle * $cost_book;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				

			}
		}


		$sheet->setTitle('GUDANG ORIGA');
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
		header('Content-Disposition: attachment;filename="gudang-origa.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}