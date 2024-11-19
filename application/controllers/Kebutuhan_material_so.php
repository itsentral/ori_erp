<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kebutuhan_material_so extends CI_Controller {

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
		$arr_Where			= array('flag_active'=>'1');
		$get_Data			= $this->master_model->getMenu($arr_Where);
		$sales_order		= $this->db
                                    ->select("REPLACE(a.id_produksi,'PRO-','') AS no_ipp, b.so_number")
                                    ->group_by('a.id_produksi')
                                    ->order_by('a.id_produksi','desc')
                                    ->join('so_number b',"REPLACE(a.id_produksi,'PRO-','BQ-') = b.id_bq")
                                    ->get('production_detail a')->result_array();
		$data = array(
			'title'			=> 'Kebutuhan Material Per SO',
			'action'		=> 'add',
			'sales_order'	=> $sales_order
		);
		$this->load->view('Kebutuhan_material_so/index',$data);
	}

    public function show_history(){
		$data 			= $this->input->post();
		$no_ipp 	= 'PRO-'.$data['sales_order'];

		$result		= $this->db
							->select('a.*')
							->from('production_detail a')
							->where('a.id_produksi',$no_ipp)
                            ->group_by('id_milik')
							->get()
							->result_array();

		$dataArr = [
			'result' => $result,
			'GET_IPP_DET' => get_detail_ipp(),
            'GET_MATERIAL' => get_detail_material()
		];

		$data_html = $this->load->view('Kebutuhan_material_so/show_detail', $dataArr, TRUE);

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
		$objPHPExcel	= new PHPExcel();

		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

		$GET_IPP_DET = get_detail_ipp();
		$GET_MATERIAL = get_detail_material();

		$NO_SO = (!empty($GET_IPP_DET[$sales_order]['so_number']))?$GET_IPP_DET[$sales_order]['so_number']:'';

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'KEBUTUHAN MATERIAL PER SO');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No. SO.');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableBodyLeft);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $NO_SO);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($tableBodyLeft);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NO SALES ORDER');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'NAMA PROJECT');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'NO SPK');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'PRODUCT');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'SPEC');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'QTY EST');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'EST BERAT');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$no_ipp 	= 'PRO-'.$sales_order;
		$result		= $this->db
							->select('a.*')
							->from('production_detail a')
							->where('a.id_produksi',$no_ipp)
                            ->group_by('id_milik')
							->get()
							->result_array();

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no_ipp = str_replace('PRO-','',$row_Cek['id_produksi']);

                $NO_SO = (!empty($GET_IPP_DET[$no_ipp]['so_number']))?$GET_IPP_DET[$no_ipp]['so_number']:'';
                $NM_PROJECT = (!empty($GET_IPP_DET[$no_ipp]['nm_project']))?$GET_IPP_DET[$no_ipp]['nm_project']:'';

                $detail_estimasi = get_estimasi_material_per_spk_detail($row_Cek['id_milik']);

				if(!empty($detail_estimasi)){
					foreach ($detail_estimasi as $key2 => $value2) {
						$no++;
						$awal_row++;
						$awal_col	= 0;
						

						$awal_col++;
						$detail_name	= $no;
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $detail_name);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $NO_SO);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $NM_PROJECT);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$no_spk		= strtoupper($row_Cek['no_spk']);
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $no_spk);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$id_category	= strtoupper($row_Cek['id_category']);
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $id_category);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$id_milik	= spec_bq2($row_Cek['id_milik']);
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $id_milik);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$qty		= $row_Cek['qty'];
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $qty);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

						$NM_MATERIAL = (!empty($GET_MATERIAL[$value2['id_material']]['nm_material']))?$GET_MATERIAL[$value2['id_material']]['nm_material']:'';
						$berat = $value2['berat'] * $row_Cek['qty'];

						$awal_col++;
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $NM_MATERIAL);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols			= getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $berat);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
					}
				}
			}
		}


		$sheet->setTitle('Kebutuhan SO');
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
		header('Content-Disposition: attachment;filename="kebutuhan-material-'.$NO_SO.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}