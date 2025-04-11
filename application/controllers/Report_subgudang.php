<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_subgudang extends CI_Controller {

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
			'title'			=> 'Report Subgudang',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);

		$this->load->view('Report_new/Report_subgudang/index',$data);
	}
	
	public function get_data_json_spk_produksi_progress(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spk_produksi_progress(
			$requestData['tgl_awal'],
			$requestData['tgl_akhir'],
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
		$GET_DET_IPP = get_detail_ipp();
		// print_r($GET_DELIVERY_DATE);
		// exit;
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

            $nomor_ipp = $row['no_ipp'];
            $tipe = 'non-mixing';
            if($row['no_ipp'] == 'resin mixing'){
                $tipe = 'mixing';
                $nomor_ipp = $row['no_ipp2'];
            }

			$TipeInOut = 'IN';
			if($row['id_gudang_dari'] == '3' OR $row['id_gudang_dari'] == '4'){
                $TipeInOut = 'OUT';
            }

            $nomor_so = (!empty($GET_DET_IPP[$nomor_ipp]['so_number']))?$GET_DET_IPP[$nomor_ipp]['so_number']:'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			// $nestedData[]	= "<div align='left'>".$tipe."</div>";
			$nestedData[]	= "<div align='center'>".$TipeInOut."</div>";
			// $nestedData[]	= "<div align='left'>".$row['category']."</div>";
			$nestedData[]	= "<div align='center'>".$nomor_so."</div>";
            $no_spk = (!empty($row['no_spk']))?$row['no_spk']:$row['no_spk2'];
			$nestedData[]	= "<div align='center'>".$no_spk."</div>";

            $product = (!empty($row['product']))?$row['product']:$row['product2'];

			$nestedData[]	= "<div align='left'>".strtoupper($product)."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty'],4)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['cost_book'],2)."</div>";
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

	public function query_data_spk_produksi_progress($tgl_awal,$tgl_akhir,$like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$WHERE_DATE = "AND a.update_date LIKE '".date('Y')."-".date('m')."%' ";
		if($tgl_awal != '0'){
			$WHERE_DATE = "AND (DATE( a.update_date ) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' )";
		}

        $sql = "SELECT 
                    (@row:=@row+1) AS nomor,
                    DATE( a.update_date ) AS tanggal,
                    b.no_ipp,
                    'Subgudang to Gd. Produksi' AS category,
                    c.no_ipp AS no_ipp2,
                    c.product_code AS no_so,
                    c.product AS product,
	                d.id_category AS product2,
                    c.no_spk AS no_spk,
	                b.no_spk AS no_spk2,
                    a.kode_trans,
                    a.id_material,
                    a.nm_material,
                    a.qty_oke AS qty,
					b.id_gudang_dari,
					b.id_gudang_ke,
                    (SELECT z.price_book FROM price_book_subgudang z WHERE z.id_material=a.id_material and z.updated_date <= a.update_date ORDER BY z.id DESC LIMIT 1) AS cost_book 
                FROM
                    warehouse_adjustment_check a 
                    LEFT JOIN warehouse_adjustment b ON a.kode_trans=b.kode_trans
                    LEFT JOIN production_spk c ON b.kode_spk=c.kode_spk
                    LEFT JOIN so_detail_header d ON b.no_spk = d.no_spk,
                    (SELECT @row:=0) r
                WHERE 1=1 ".$WHERE_DATE."
                    AND (b.id_gudang_dari IN (3,4) OR b.id_gudang_ke IN (3,4))
                    AND a.qty_oke > 0
                    AND DATE(a.update_date) > '2023-10-30'
                    AND (
                        a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				    )";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);

		$sql .= " ORDER BY a.id DESC,  ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function excel_report_subgudang(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$tgl_awal		= $this->uri->segment(3);
		$tgl_akhir		= $this->uri->segment(4);

		$this->load->library("PHPExcel");
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

        $GET_DET_IPP = get_detail_ipp();

		$WHERE_DATE = "AND a.update_date LIKE '".date('Y')."-".date('m')."%' ";
		if($tgl_awal != '0'){
			$WHERE_DATE = "AND (DATE( a.update_date ) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' )";
		}

        $sql = "SELECT 
                    DATE( a.update_date ) AS tanggal,
                    b.no_ipp,
                    'Subgudang to Gd. Produksi' AS category,
                    c.no_ipp AS no_ipp2,
                    c.product_code AS no_so,
                    c.product AS product,
	                d.id_category AS product2,
                    c.no_spk AS no_spk,
	                b.no_spk AS no_spk2,
                    a.kode_trans,
                    a.id_material,
                    a.nm_material,
                    a.qty_oke AS qty,
					b.id_gudang_dari,
					b.id_gudang_ke,
                    (SELECT z.price_book FROM price_book_subgudang z WHERE z.id_material=a.id_material and z.updated_date <= a.update_date ORDER BY z.id DESC LIMIT 1) AS cost_book 
                FROM
                    warehouse_adjustment_check a 
                    LEFT JOIN warehouse_adjustment b ON a.kode_trans=b.kode_trans
                    LEFT JOIN production_spk c ON b.kode_spk=c.kode_spk
                    LEFT JOIN so_detail_header d ON b.no_spk = d.no_spk
                WHERE 1=1 ".$WHERE_DATE."
                    AND (b.id_gudang_dari IN (3,4) OR b.id_gudang_ke IN (3,4))
                    AND a.qty_oke > 0
                    AND DATE(a.update_date) > '2022-12-31'
                ORDER BY a.id DESC";
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'REPORT SUBGUDANG');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'TANGGAL');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'TYPE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'NO SO');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'NO SPK');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'PRODUCT');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'NO TRANS');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

        $sheet->setCellValue('H'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

        $sheet->setCellValue('I'.$NewRow, 'NM MATERIAL');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

        $sheet->setCellValue('J'.$NewRow, 'QTY');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'COST BOOK');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);


		// echo $qDetail1; exit;
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row){
				$no++;
				$awal_row++;
				$awal_col	= 0;

                $nomor_ipp = $row['no_ipp'];
                $tipe = 'non-mixing';
                if($row['no_ipp'] == 'resin mixing'){
                    $tipe = 'mixing';
                    $nomor_ipp = $row['no_ipp2'];
                }

                $nomor_so = (!empty($GET_DET_IPP[$nomor_ipp]['so_number']))?$GET_DET_IPP[$nomor_ipp]['so_number']:'';
                $product = (!empty($row['product']))?$row['product']:$row['product2'];
                $no_spk = (!empty($row['no_spk']))?$row['no_spk']:$row['no_spk2'];

				$TipeInOut = 'IN';
				if($row['id_gudang_dari'] == '3' OR $row['id_gudang_dari'] == '4'){
					$TipeInOut = 'OUT';
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$tanggal	= $row['tanggal'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tanggal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $TipeInOut);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				// $awal_col++;
				// $category	= $row['category'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $category);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$kode_trans	= $row['kode_trans'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$id_material	= $row['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= $row['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$qty	= $row['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$cost_book	= $row['cost_book'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost_book);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('Report SubGudang');
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
		header('Content-Disposition: attachment;filename="report-subgudang.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

    //GROUP
    public function get_data_json_spk_produksi_progress2(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spk_produksi_progress2(
			$requestData['tgl_awal'],
			$requestData['tgl_akhir'],
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
		$GET_DET_IPP = get_detail_ipp();
		// print_r($GET_DELIVERY_DATE);
		// exit;
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

            $nomor_ipp = $row['no_ipp'];
            $nomor_so = (!empty($GET_DET_IPP[$nomor_ipp]['so_number']))?$GET_DET_IPP[$nomor_ipp]['so_number']:$nomor_ipp;

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			// $nestedData[]	= "<div align='left'>".$tipe."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['tipe'])."</div>";
			$nestedData[]	= "<div align='center'>".$nomor_so."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty'],4)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['cost_book'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty']*$row['cost_book'],2)."</div>";
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

	public function query_data_spk_produksi_progress2($tgl_awal,$tgl_akhir,$like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$WHERE_DATE = "AND a.tanggal LIKE '".date('Y')."-".date('m')."%' ";
		if($tgl_awal != '0'){
			$WHERE_DATE = "AND (DATE( a.tanggal ) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' )";
		}

        $sql = "SELECT 
                    (@row:=@row+1) AS nomor,
                    a.*
                FROM
                    erp_data_subgudang a,
                    (SELECT @row:=0) r
                WHERE 1=1 ".$WHERE_DATE." AND a.gudang IN (3,4)
					AND DATE(a.tanggal) > '2023-10-30'
                    AND (
                        a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				    )";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);

		$sql .= " ORDER BY a.tanggal DESC,  ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function excel_report_subgudang2(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$tgl_awal		= $this->uri->segment(3);
		$tgl_akhir		= $this->uri->segment(4);

		$this->load->library("PHPExcel");
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

        $GET_DET_IPP = get_detail_ipp();

		$WHERE_DATE = "AND a.tanggal LIKE '".date('Y')."-".date('m')."%' ";
		if($tgl_awal != '0'){
			$WHERE_DATE = "AND (DATE( a.tanggal ) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' )";
		}

        $sql = "SELECT 
                    (@row:=@row+1) AS nomor,
                    a.*,
                    'Subgudang to Gd. Produksi' AS category
                FROM
                    erp_data_subgudang a,
                    (SELECT @row:=0) r
                WHERE 1=1 ".$WHERE_DATE." AND a.gudang IN (3,4)
                    ORDER BY a.tanggal DESC";

		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(12);
		$sheet->setCellValue('A'.$Row, 'REPORT SUBGUDANG GROUP');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'TANGGAL');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'TYPE');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'NO SO');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'NO SPK');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'PRODUCT');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'NO TRANS');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

        $sheet->setCellValue('H'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

        $sheet->setCellValue('I'.$NewRow, 'NM MATERIAL');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

        $sheet->setCellValue('J'.$NewRow, 'QTY');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'COST BOOK');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'TOTAL PRICE');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);


		// echo $qDetail1; exit;
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row){
				$no++;
				$awal_row++;
				$awal_col	= 0;

                $nomor_ipp = $row['no_ipp'];
           	 	$nomor_so = (!empty($GET_DET_IPP[$nomor_ipp]['so_number']))?$GET_DET_IPP[$nomor_ipp]['so_number']:$nomor_ipp;

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$tanggal	= $row['tanggal'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tanggal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$tipe	= $row['tipe'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tipe);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				// $awal_col++;
				// $category	= $row['category'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $category);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $row['no_spk']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, strtoupper($row['product']));
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$kode_trans	= $row['kode_trans'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$id_material	= $row['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= $row['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$qty	= $row['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$cost_book	= $row['cost_book'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost_book);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$price_book	= $row['cost_book'] * $row['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_book);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('Report SubGudang');
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
		header('Content-Disposition: attachment;filename="report-subgudang-group.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}