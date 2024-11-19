<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profit_per_produk_fg extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('report_model');

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
		$sales_order = $this->db
                            ->select('a.no_ipp, b.so_number')
                            ->group_by('a.no_ipp')
                            ->order_by('b.so_number','desc')
                            ->join('so_number b',"CONCAT('BQ-',a.no_ipp) = b.id_bq",'left')
                            ->get('billing_so_product a')
                            ->result_array();
		$data = array(
			'title'			=> 'Report Gross Profit Per Produk FG',
			'action'		=> 'add',
			'sales_order'	=> $sales_order
		);
		history('View data profit per produk fg');
		$this->load->view('Profit_per_produk_fg/index',$data);
	}

    public function show_history(){
		$data = $this->input->post();
        $GET_IPP = get_detail_ipp();
		$sales_order = $data['sales_order'];
        $no_so = (!empty($GET_IPP[$sales_order]['so_number']))?$GET_IPP[$sales_order]['so_number']:0;
		
        $result = $this->db
                        ->select('  a.product,
                                    a.spec,
                                    a.qty,
                                    (a.total_deal_usd * IF(d.kurs_usd_dipakai > 0, d.kurs_usd_dipakai,d.kurs_usd_db)) AS total_deal_idr,
                                    c.id AS id_milik_so
                                    ')
                        ->join('so_bf_detail_header c',"a.id_milik = c.id_milik",'left')
                        ->join('billing_so d',"a.no_ipp = d.no_ipp",'left')
                        ->get_where('billing_so_product a',array('a.no_ipp'=>$sales_order))->result_array();
        $SQL = "SELECT
                    a.id_category AS product_name,
                    b.id_milik AS id_milik_so,
                    SUM( a.qty_akhir - a.qty_awal + 1 ) AS qty,
                    SUM( 
                        a.real_harga_rp +
                        a.direct_labour * a.kurs +
                        a.indirect_labour * a.kurs +
                        a.machine * a.kurs +
                        a.mould_mandrill * a.kurs +
                        a.consumable * a.kurs +
                        a.foh_consumable * a.kurs +
                        a.foh_depresiasi * a.kurs +
                        -- a.biaya_gaji_non_produksi * a.kurs +
                        -- a.biaya_non_produksi * a.kurs +
                        a.biaya_rutin_bulanan * a.kurs
                    ) AS price_idr 
                FROM
                    laporan_per_hari a
                    LEFT JOIN so_detail_header b ON a.id_milik = b.id
                    LEFT JOIN production_detail c ON a.id_production_detail = c.id
                WHERE
                    a.id_produksi = 'PRO-".$sales_order."' 
                    AND c.fg_date IS NOT NULL
                GROUP BY
                    a.id_milik 
                ORDER BY
                    a.id_milik";
        $resultData = $this->db->query($SQL)->result_array();
        $ArrData = [];
        foreach ($resultData as $key => $value) {
            $ArrData[$value['id_milik_so']]['qty'] = $value['qty'];
            $ArrData[$value['id_milik_so']]['price_idr'] = $value['price_idr'];
        }

		$dataArr = [
			'result' => $result,
			'no_so' => $no_so,
			'ArrData' => $ArrData,
		];

		$data_html = $this->load->view('Profit_per_produk_fg/detail', $dataArr, TRUE);

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
		$sales_order	= $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
        $sheet          = $objPHPExcel->getActiveSheet();

		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'REPORT PROFIT GROSS PER PRODUK FINISH GOOD');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'SALES ORDER');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'PRODUK');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'SPEC');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'QTY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'HARGA PER PCS (IDR)');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'TOTAL NILAI PENJUALAN (IDR)');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'QTY FG');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'NILAI PER PCS FG (IDR)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$sheet->setCellValue('J'.$NewRow, 'TOTAL NILAI PRODUK (IDR)');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(10);

		$sheet->setCellValue('K'.$NewRow, 'PROFIT PER PRODUK (IDR)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(10);

        $GET_IPP    = get_detail_ipp();
        $no_so      = (!empty($GET_IPP[$sales_order]['so_number']))?$GET_IPP[$sales_order]['so_number']:0;
		
        $result = $this->db
                        ->select('  a.product,
                                    a.spec,
                                    a.qty,
                                    (a.total_deal_usd * IF(d.kurs_usd_dipakai > 0, d.kurs_usd_dipakai,d.kurs_usd_db)) AS total_deal_idr,
                                    c.id AS id_milik_so
                                    ')
                        ->join('so_bf_detail_header c',"a.id_milik = c.id_milik",'left')
						->join('billing_so d',"a.no_ipp = d.no_ipp",'left')
                        ->get_where('billing_so_product a',array('a.no_ipp'=>$sales_order))->result_array();
        $SQL = "SELECT
                    a.id_category AS product_name,
                    b.id_milik AS id_milik_so,
                    SUM( a.qty_akhir - a.qty_awal + 1 ) AS qty,
                    SUM( 
                        a.real_harga_rp +
                        a.direct_labour * a.kurs +
                        a.indirect_labour * a.kurs +
                        a.machine * a.kurs +
                        a.mould_mandrill * a.kurs +
                        a.consumable * a.kurs +
                        a.foh_consumable * a.kurs +
                        a.foh_depresiasi * a.kurs +
                        -- a.biaya_gaji_non_produksi * a.kurs +
                        -- a.biaya_non_produksi * a.kurs +
                        a.biaya_rutin_bulanan * a.kurs
                    ) AS price_idr
                FROM
                    laporan_per_hari a
                    LEFT JOIN so_detail_header b ON a.id_milik = b.id 
                    LEFT JOIN production_detail c ON a.id_production_detail = c.id
                WHERE
                    a.id_produksi = 'PRO-".$sales_order."' 
                    AND c.fg_date IS NOT NULL
                GROUP BY
                    a.id_milik 
                ORDER BY
                    a.id_milik";
        $resultData = $this->db->query($SQL)->result_array();
        $ArrData = [];
        foreach ($resultData as $key => $value) {
            $ArrData[$value['id_milik_so']]['qty'] = $value['qty'];
            $ArrData[$value['id_milik_so']]['price_idr'] = $value['price_idr'];
        }

        $no_so = (!empty($GET_IPP[$sales_order]['so_number']))?$GET_IPP[$sales_order]['so_number']:0;


		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

                $QTY_FG = (!empty($ArrData[$row_Cek['id_milik_so']]['qty']))?$ArrData[$row_Cek['id_milik_so']]['qty']:0;
                $price_idr = (!empty($ArrData[$row_Cek['id_milik_so']]['price_idr']))?$ArrData[$row_Cek['id_milik_so']]['price_idr']:0;

                $HRG_PER_PCS = 0;
                if($row_Cek['qty'] > 0 AND $row_Cek['total_deal_idr'] > 0){
                    $HRG_PER_PCS = $row_Cek['total_deal_idr'] / $row_Cek['qty'];
                }

                $HRG_PER_PCS_FG = 0;
                if($QTY_FG > 0 AND $price_idr > 0){
                    $HRG_PER_PCS_FG = $price_idr / $QTY_FG;
                }

                $PROFIT = $HRG_PER_PCS-$HRG_PER_PCS_FG;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$product	= strtoupper($row_Cek['product']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$spec	= strtoupper($row_Cek['spec']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty	= $row_Cek['qty'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $HRG_PER_PCS);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$total_deal_idr		= $row_Cek['total_deal_idr'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $total_deal_idr);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_FG);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $HRG_PER_PCS_FG);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $price_idr);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $PROFIT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
			}
		}


		$sheet->setTitle('PROFIT GROSS PER PRODUK FG');
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
		header('Content-Disposition: attachment;filename="profit-gross-per-produk-finish-good-'.$no_so.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}


}