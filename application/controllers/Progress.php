<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Progress extends CI_Controller {

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

		$sales_order		= $this->db
                                ->select('a.*, b.so_number, c.nm_customer, c.project, c.no_ipp')
                                ->order_by('c.no_ipp','desc')
                                ->join('so_number b','a.id_bq=b.id_bq','inner')
                                ->join('production c','REPLACE(a.id_bq,"BQ-","")=c.no_ipp','left')
                                ->get('so_header a')
                                ->result_array();
		$data = array(
			'title'			=> 'Progress SO',
			'action'		=> 'add',
			'sales_order'	=> $sales_order
		);
		$this->load->view('Progress/sales_order/index',$data);
	}

    public function get_no_spk(){
		$data       = $this->input->post();
		$no_so   	= $data['no_so'];

        $option = '';
        $get_spk = $this->db->order_by('no_spk')->get_where('so_detail_header',array('id_bq'=>'BQ-'.$no_so,'no_spk !='=>NULL))->result_array();
		if(!empty($get_spk)){
			$option	.= "<option value='0'>ALL SPK</option>";
			foreach ($get_spk as $key => $value) {
				$option	.= "<option value='".$value['id']."/".$value['no_spk']."'>".strtoupper($value['no_spk'])."</option>";
			}
		}
		else{
			$option	.= "<option value='0'>NO SPK BELUM DIBUAT</option>";
		}

        $Arr_Kembali	= array(
            'option' => $option
        );
        echo json_encode($Arr_Kembali);
	}

    public function show_history(){
		$data 			= $this->input->post();
		$sales_order 	= $data['sales_order'];

		$SQL = "SELECT 
                    b.*,
                    c.so_number,
                    d.nm_customer
                FROM 
                    so_header a
                    LEFT JOIN so_detail_header b ON a.id_bq = b.id_bq
                    LEFT JOIN so_number c ON a.id_bq = c.id_bq AND c.so_number IS NOT NULL
                    LEFT JOIN production d ON REPLACE(a.id_bq,'BQ-','')=d.no_ipp
                WHERE 
                    a.id_bq = 'BQ-".$sales_order."'
                ";
		$result 		= $this->db->query($SQL)->result_array();

		$dataArr = [
			'result' => $result
		];

		$data_html = $this->load->view('Progress/sales_order/detail', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function download_excel($no_ipp){
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

		
		$SQL = "SELECT 
                    b.*,
                    c.so_number,
                    d.nm_customer,
					d.project
                FROM 
                    so_header a
                    LEFT JOIN so_detail_header b ON a.id_bq = b.id_bq
                    LEFT JOIN so_number c ON a.id_bq = c.id_bq AND c.so_number IS NOT NULL
                    LEFT JOIN production d ON REPLACE(a.id_bq,'BQ-','')=d.no_ipp
                WHERE 
                    a.id_bq = 'BQ-".$no_ipp."'
                ";
		// echo $SQL;
		// exit;
		$result 	= $this->db->query($SQL)->result_array();
		$no_so	 	= $result[0]['so_number'];

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(17);
		$sheet->setCellValue('A'.$Row, 'PROGRESS '.$no_so);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow1= $NewRow + 1;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'No SO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow1);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow1);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow1);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Product');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow1);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'No SPK');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow1);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Qty SO');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow1);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'SPK');
		$sheet->getStyle('H'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'PRODUKSI');
		$sheet->getStyle('J'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'FG');
		$sheet->getStyle('L'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('N'.$NewRow, 'IN TRANSIT');
		$sheet->getStyle('N'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(20);

		$sheet->setCellValue('P'.$NewRow, 'CUSTOMER');
		$sheet->getStyle('P'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('P')->setWidth(20);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('H'.$NewRow, 'R');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'O');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'R');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'O');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'R');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('M'.$NewRow, 'O');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(20);

		$sheet->setCellValue('N'.$NewRow, 'R');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(20);

		$sheet->setCellValue('O'.$NewRow, 'O');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setWidth(20);

		$sheet->setCellValue('P'.$NewRow, 'R');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setWidth(20);

		$sheet->setCellValue('Q'.$NewRow, 'O');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setWidth(20);

		// $GET_DET_IPP = get_detail_ipp();
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $value){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$getSPK         = $this->db->get_where('production_detail',array('id_milik' => $value['id'],'kode_spk !=' => NULL))->result_array();
                $getLap         = $this->db->get_where('production_detail',array('id_milik' => $value['id'],'closing_produksi_date !=' => NULL))->result_array();
                $getFg          = $this->db->get_where('production_detail',array('id_milik' => $value['id'],'fg_date !=' => NULL))->result_array();
                $getInTrans     = $this->db->get_where('production_detail',array('id_milik' => $value['id'],'lock_delivery_date !=' => NULL))->result_array();
                $getGdCust      = $this->db->get_where('production_detail',array('id_milik' => $value['id'],'release_delivery_date !=' => NULL))->result_array();

                $QTY_SO             = $value['qty'];

                $QTY_SPK            = COUNT($getSPK);
                $QTY_SPK_OUT        = ($QTY_SO - $QTY_SPK > 0)?$QTY_SO - $QTY_SPK:'-';

                $QTY_LAP_PRO        = COUNT($getLap);
                $QTY_LAP_PRO_OUT    = ($QTY_SPK - $QTY_LAP_PRO > 0)?$QTY_SPK - $QTY_LAP_PRO:'-';

                $QTY_FG             = COUNT($getFg);
                $QTY_FG_OUT         = ($QTY_LAP_PRO - $QTY_FG > 0)?$QTY_LAP_PRO - $QTY_FG:'-';

                $QTY_INTRAN         = COUNT($getInTrans);
                $QTY_INTRAN_OUT     = ($QTY_FG - $QTY_INTRAN > 0)?$QTY_FG - $QTY_INTRAN:'-';

                $QTY_CUST           = COUNT($getGdCust);
                $QTY_CUST_OUT       = ($QTY_INTRAN - $QTY_CUST > 0)?$QTY_INTRAN - $QTY_CUST:'-';


                $Label_QTY_SPK            = ($QTY_SPK > 0)?$QTY_SPK:'-';
                $Label_QTY_LAP_PRO        = ($QTY_LAP_PRO > 0)?$QTY_LAP_PRO:'-';
                $Label_QTY_FG             = ($QTY_FG > 0)?$QTY_FG:'-';
                $Label_QTY_INTRAN         = ($QTY_INTRAN > 0)?$QTY_INTRAN:'-';
                $Label_QTY_CUST           = ($QTY_CUST > 0)?$QTY_CUST:'-';

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_so 		= $value['so_number'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_customer	= strtoupper($value['nm_customer']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$project	= strtoupper($value['project']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_category	= strtoupper($value['id_category']).', '.spec_bq2($value['id']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk			= strtoupper($value['no_spk']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Label_QTY_SPK);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_SPK_OUT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Label_QTY_LAP_PRO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_LAP_PRO_OUT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Label_QTY_FG);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_FG_OUT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Label_QTY_INTRAN);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_INTRAN_OUT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Label_QTY_CUST);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY_CUST_OUT);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('Sales Order');
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
		header('Content-Disposition: attachment;filename="report-progress-'.$no_so.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}