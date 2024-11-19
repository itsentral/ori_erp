<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_purchase extends CI_Controller {

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
			'title'			=> 'Report Purchasing',
			'action'		=> 'add',
			'category'		=> $category
		);
		$this->load->view('Report/purchase',$data);
	}

	public function show_history(){
		$data = $this->input->post();
		$category = $data['category'];
		$tgl_awal = date('Y-m-d',strtotime($data['tgl_awal']));
		$tgl_akhir = date('Y-m-d',strtotime($data['tgl_akhir']));

		if($category == '99'){
            $kategory = 'MATERIAL';
            $result 	= $this->db
                                ->select('
                                    a.id_material AS id_barang, 
                                    a.nm_material AS nm_barang,
                                    a.no_pr AS no_pr,
                                    c.no_po AS no_po,
                                    e.kode_trans AS kode_trans,
                                    a.created_date
                                    ')
                                ->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')
                                ->join('tran_material_po_header c','b.no_po=c.no_po','left')
                                ->join('tran_material_po_detail d','b.id_material=d.id_material','left')
                                ->join('warehouse_adjustment_detail e','d.id=e.id_po_detail AND d.no_po=e.no_ipp','left')
                                ->get_where('tran_material_pr_detail a', 
                                    array(
                                        'DATE(a.created_date) >='=> $tgl_awal,
                                        'DATE(a.created_date) <='=> $tgl_akhir,
                                        'a.category'=>'mat'
                                        // 'b.no_po <>'=>NULL,
                                        // 'b.status'=>'SETUJU',
                                        // 'b.status_apv'=>'SETUJU',
                                        // 'c.status1'=>'Y',
                                        // 'c.status2'=>'Y',
                                        // 'd.qty_in >'=>0
                                        )
                                    )
                                ->result_array();
		}
        else{
            $getCategory= $this->db->get_where('con_nonmat_category_awal',array('id'=>$category))->result_array();
            $kategory   = strtoupper($getCategory[0]['category']);
            $result 	= $this->db
                                ->select('
                                    a.id_barang AS id_barang, 
                                    a.nm_barang AS nm_barang,
                                    a.no_pr_group AS no_pr,
                                    c.no_po AS no_po,
                                    e.kode_trans AS kode_trans,
                                    a.created_date
                                    ')
                                ->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')
                                ->join('tran_po_header c','b.no_po=c.no_po','left')
                                ->join('tran_po_detail d','b.id_barang=d.id_barang','left')
                                ->join('warehouse_adjustment_detail e','d.id=e.id_po_detail AND d.no_po=e.no_ipp','left')
                                ->join('con_nonmat_new f','a.id_barang=f.code_group','left')
                                ->get_where('tran_pr_detail a', 
                                    array(
                                        'DATE(a.created_date) >='=> $tgl_awal,
                                        'DATE(a.created_date) <='=> $tgl_akhir,
                                        'a.category'=>'rutin',
                                        'f.category_awal'=> $category
                                        // 'b.status'=>'SETUJU',
                                        // 'b.status_apv'=>'SETUJU',
                                        // 'c.status1'=>'Y',
                                        // 'c.status2'=>'Y',
                                        // 'd.qty_in >'=>0
                                        )
                                    )
                                ->result_array();
        }


		$dataArr = [
			'result' => $result,
            'kategory' => $kategory,
            'GET_MATERIAL' => get_detail_material(),
            'GET_STOK' => get_detail_consumable()
		];

		$data_html = $this->load->view('Report/show_history_purchase', $dataArr, TRUE);

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
		$category	= $this->uri->segment(3);
		$tgl_awal	= date('Y-m-d',strtotime($this->uri->segment(4)));
		$tgl_akhir	= date('Y-m-d',strtotime($this->uri->segment(5)));

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
		
		if($category == '99'){
            $kategory = 'MATERIAL';
            $result 	= $this->db
                                ->select('
                                    a.id_material AS id_barang, 
                                    a.nm_material AS nm_barang,
                                    a.no_pr AS no_pr,
                                    c.no_po AS no_po,
                                    e.kode_trans AS kode_trans,
                                    a.created_date
                                    ')
                                ->join('tran_material_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_material = b.id_material','left')
                                ->join('tran_material_po_header c','b.no_po=c.no_po','left')
                                ->join('tran_material_po_detail d','b.id_material=d.id_material','left')
                                ->join('warehouse_adjustment_detail e','d.id=e.id_po_detail AND d.no_po=e.no_ipp','left')
                                ->get_where('tran_material_pr_detail a', 
                                    array(
                                        'DATE(a.created_date) >='=> $tgl_awal,
                                        'DATE(a.created_date) <='=> $tgl_akhir,
                                        'a.category'=>'mat'
                                        // 'b.no_po <>'=>NULL,
                                        // 'b.status'=>'SETUJU',
                                        // 'b.status_apv'=>'SETUJU',
                                        // 'c.status1'=>'Y',
                                        // 'c.status2'=>'Y',
                                        // 'd.qty_in >'=>0
                                        )
                                    )
                                ->result_array();
		}
        else{
            $getCategory= $this->db->get_where('con_nonmat_category_awal',array('id'=>$category))->result_array();
            $kategory   = strtoupper($getCategory[0]['category']);
            $result 	= $this->db
                                ->select('
                                    a.id_barang AS id_barang, 
                                    a.nm_barang AS nm_barang,
                                    a.no_pr_group AS no_pr,
                                    c.no_po AS no_po,
                                    e.kode_trans AS kode_trans,
                                    a.created_date
                                    ')
                                ->join('tran_rfq_detail b','a.no_rfq=b.no_rfq AND a.id_barang = b.id_barang','left')
                                ->join('tran_po_header c','b.no_po=c.no_po','left')
                                ->join('tran_po_detail d','b.id_barang=d.id_barang','left')
                                ->join('warehouse_adjustment_detail e','d.id=e.id_po_detail AND d.no_po=e.no_ipp','left')
                                ->join('con_nonmat_new f','a.id_barang=f.code_group','left')
                                ->get_where('tran_pr_detail a', 
                                    array(
                                        'DATE(a.created_date) >='=> $tgl_awal,
                                        'DATE(a.created_date) <='=> $tgl_akhir,
                                        'a.category'=>'rutin',
                                        'f.category_awal'=> $category
                                        // 'b.no_po <>'=>NULL,
                                        // 'b.status'=>'SETUJU',
                                        // 'b.status_apv'=>'SETUJU',
                                        // 'c.status1'=>'Y',
                                        // 'c.status2'=>'Y',
                                        // 'd.qty_in >'=>0
                                        )
                                    )
                                ->result_array();
        }

        $result = $result;
        $kategory = $kategory;
        $GET_MATERIAL = get_detail_material();
        $GET_STOK = get_detail_consumable();

		// echo '<pre>';
		// print_r($result);
		// exit;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'REPORT PURCHASING ('.$tgl_awal.' - '.$tgl_akhir.')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'CATEGORY');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'KD BARANG');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'ACCURATE');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'NAMA BARANG');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'PR');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'PO');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'INCOMING');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'DATED');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		// $sheet->setCellValue('J'.$NewRow, 'STOK AWAL');
		// $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		// $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		// $sheet->getColumnDimension('J')->setWidth(10);

		// $sheet->setCellValue('K'.$NewRow, 'STOK AKHIR');
		// $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		// $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		// $sheet->getColumnDimension('K')->setWidth(10);

		// $sheet->setCellValue('L'.$NewRow, 'KETERANGAN');
		// $sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		// $sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		// $sheet->getColumnDimension('L')->setWidth(10);

		// $sheet->setCellValue('M'.$NewRow, 'TANGGAL');
		// $sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		// $sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		// $sheet->getColumnDimension('M')->setWidth(10);

		
		// echo '<pre>';	
		// print_r($GET_GUDANG); 
		// echo $GET_GUDANG['16JSON'];
		// exit;

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

                if($kategory == 'MATERIAL'){
                    $KD_ACCURATE = (!empty($GET_MATERIAL[$row_Cek['id_barang']]['id_accurate']))?$GET_MATERIAL[$row_Cek['id_barang']]['id_accurate']:'';
                }
                else{
                    $KD_ACCURATE = (!empty($GET_STOK[$row_Cek['id_barang']]['id_accurate']))?$GET_STOK[$row_Cek['id_barang']]['id_accurate']:'';
                }

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kategory);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_barang	= strtoupper($row_Cek['id_barang']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $KD_ACCURATE);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_barang		= $row_Cek['nm_barang'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_pr		= $row_Cek['no_pr'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_pr);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$no_po		= $row_Cek['no_po'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_po);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$kode_trans		= $row_Cek['kode_trans'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$created_date		= date('d-M-Y H:i:s',strtotime($row_Cek['created_date']));
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $created_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
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