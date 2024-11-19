<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deadstok_value extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Deadstok Price Book',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Deadstok Value');
		$this->load->view('Deadstok/index_value',$data);
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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
			$nestedData[]	= "<div align='center'><b class='text-primary'>".$row['id_product']."</b></div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['type']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_barang']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$row['type_std']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_spec']."</div>";
			$nestedData[]	= "<div align='left'>".$row['resin']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['length'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty_stock'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['price_book'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty_stock'] * $row['price_book'])."</div>";
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

	public function queryDataJSON($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		if($date_filter == ''){
			$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					COUNT(a.qty) AS qty_stock,
					(SELECT COUNT(z.id) FROM production_detail z WHERE a.id_product=z.id_product_deadstok AND id_deadstok_dipakai IS NULL) AS qty_booking
				FROM
					deadstok a,
					(SELECT @row:=0) r
				WHERE  a.deleted_date IS NULL AND a.kode_delivery IS NULL AND a.id_booking IS NULL AND(
					a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_barang LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.id_product";
			}
			else{
				$sql = "SELECT
							(@row:=@row+1) AS nomor,
							a.*,
							a.qty AS qty_stock
						FROM
							deadstok_per_day a,
							(SELECT @row:=0) r
						WHERE DATE(a.hist_date) = '".$date_filter."' AND(
							a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.no_barang LIKE '%".$this->db->escape_like_str($like_value)."%'
						)";
			}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'type',
			3 => 'no_barang',
			4 => 'product_name',
			5 => 'type_std',
			6 => 'product_spec',
			7 => 'resin',
			8 => 'length',
			9 => 'qty',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function download_excel($date_filter=null){
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $this->load->library("PHPExcel");

        $objPHPExcel    = new PHPExcel();

        $whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

        $Arr_Bulan  = array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $sheet      = $objPHPExcel->getActiveSheet();

        $dateX	= date('Y-m-d H:i:s');
        $Row        = 1;
        $NewRow     = $Row+1;
        $Col_Akhir  = $Cols = getColsChar(11);
        $sheet->setCellValue('A'.$Row, "FINISH GOOD DEADSTOK (VALUE) ".$date_filter);
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'Category');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'No Barang');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'Product Name');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'Type');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Spec');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Resin');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Length');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'Qty');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

        $sheet ->getColumnDimension("J")->setAutoSize(true);
		$sheet->setCellValue('J'.$NewRow, 'Price Book');
        $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);

        $sheet ->getColumnDimension("K")->setAutoSize(true);
		$sheet->setCellValue('K'.$NewRow, 'Total Price Book');
        $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);

		if($date_filter == ''){
		$SQL = "SELECT
					a.*,
					COUNT(qty) AS qty_stock
				FROM
					deadstok a
				WHERE  
					a.deleted_date IS NULL AND a.kode_delivery IS NULL AND a.id_booking IS NULL
				GROUP BY 
					a.id_product";
		}
		else{
			$SQL = "SELECT
					a.*,
					qty AS qty_stock
				FROM
					deadstok_per_day a
				WHERE  
					DATE(a.hist_date) = '".$date_filter."'";
		}
		$dataResult   = $this->db->query($SQL)->result_array();

		if($dataResult){
			$awal_row   = $NextRow;
			 $no = 0;
			foreach($dataResult as $key=>$vals){
				$no++;
				$awal_row++;
				$awal_col   = 0;

				$awal_col++;
				$no   = $no;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$type   = $vals['type'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $type);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_barang   = $vals['no_barang'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$product_name   = $vals['product_name'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$type_std   = $vals['type_std'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $type_std);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$product_spec   = $vals['product_spec'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product_spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$resin   = $vals['resin'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $resin);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$length   = $vals['length'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $length);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_stock   = $vals['qty_stock'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$price_book   = $vals['price_book'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_book);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$total_price   = $qty_stock * $price_book;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $total_price);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}

		history('Download deadstock value '.$date_filter);
        $sheet->setTitle('Deadstok');
        //mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
        $objWriter      = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        //sesuaikan headernya
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //ubah nama file saat diunduh
        header('Content-Disposition: attachment;filename="deadstok-value-'.$date_filter.'.xls"');
        //unduh file
        $objWriter->save("php://output");
    }

}
