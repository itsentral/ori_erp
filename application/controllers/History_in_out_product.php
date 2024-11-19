<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History_in_out_product extends CI_Controller {

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
		$material			= $this->db->order_by('nm_material','asc')->get_where('raw_materials',array('delete_date'=>NULL))->result_array();
		$data = array(
			'title'			=> 'History Product',
			'action'		=> 'index',
			'material'	    => $material
		);
		$this->load->view('History/history_in_out_product',$data);
	}

    public function show_history_summary_gudang(){
		$data       = $this->input->post();
		$warehouse  = $data['warehouse'];
		$tgl_awal   = date('Y-m-d',strtotime($data['tgl_awal']));
		$tgl_akhir  = date('Y-m-d',strtotime($data['tgl_akhir']));

		$WHERE = "AND a.tipe_product='cutting'";
		$TABLE = "so_cutting_detail";
		$WHERE2 = '';
		if($warehouse != 'cutting'){
			$WHERE = " AND (a.tipe_product='$warehouse' OR a.tipe_product='pipe fitting') AND b.kode_spk != 'deadstok'";
			$TABLE = "production_detail";

			$WHERE2 = "AND b.id_category != 'pipe'";
			if($warehouse == 'pipe'){
				$WHERE2 = "AND b.id_category = 'pipe'";
			}
		}

		$SQL = "SELECT 
					b.id_category AS product 
				FROM 
					history_product_fg a
					LEFT JOIN $TABLE b ON a.id_product = b.id
				WHERE
					DATE(a.hist_date) >= '$tgl_awal'
					AND DATE(a.hist_date) <= '$tgl_akhir'
					$WHERE
					$WHERE2
				GROUP BY b.id_category
				";
		// echo $SQL;
		// exit;
		$result		= $this->db->query($SQL)->result_array();
		// TOTAL TRANSAKSI
		$SQL1 = "SELECT 
					COUNT(a.id) AS qty,
					b.id_category 
				FROM 
					history_product_fg a
					LEFT JOIN $TABLE b ON a.id_product = b.id
				WHERE
					DATE(a.hist_date) >= '$tgl_awal'
					AND DATE(a.hist_date) <= '$tgl_akhir'
					AND a.tipe = 'in'
					$WHERE
					$WHERE2
				GROUP BY b.id_category
				";
		$result_in	=  $this->db->query($SQL1)->result_array();
		$ArrSumMaterial_IN = [];
		foreach ($result_in as $key => $value) {
			$ArrSumMaterial_IN[$value['id_category']] = $value['qty'];
		}
		$SQL2 = "SELECT 
					COUNT(a.id) AS qty,
					b.id_category 
				FROM 
					history_product_fg a
					LEFT JOIN $TABLE b ON a.id_product = b.id
				WHERE
					DATE(a.hist_date) >= '$tgl_awal'
					AND DATE(a.hist_date) <= '$tgl_akhir'
					AND a.tipe = 'out'
					$WHERE
					$WHERE2
				GROUP BY b.id_category
				";
		$result_out	= $this->db->query($SQL2)->result_array();
		$ArrSumMaterial_OUT = [];
		foreach ($result_out as $key => $value) {
			$ArrSumMaterial_OUT[$value['id_category']] = $value['qty'];
		}

		$dataArr = [
			'result' 			=> $result,
			'get_in_material' 	=> $ArrSumMaterial_IN,
			'get_out_material' 	=> $ArrSumMaterial_OUT
		];

		$data_html = $this->load->view('History/show_history_summary_gudang', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_history_summary_gudang_detail(){
		$data       = $this->input->post();
		// print_r($data);
		// echo $data['tanda'];
		// exit;
		$tanda  	= $data['tanda'];
		$warehouse  = $data['warehouse'];
		$material  	= $data['material'];
		$tgl_awal   = date('Y-m-d',strtotime($data['tgl_awal']));
		$tgl_akhir  = date('Y-m-d',strtotime($data['tgl_akhir']));

		$WHERE = "AND a.tipe_product='cutting'";
		$TABLE = "so_cutting_detail";
		$WHERE2 = '';
		$WHERE3 = '';
		$FIELD1 = 'id_bq';
		$FIELD2 = 'b.length_split, "" AS daycode,';
		$RPC1 = 'BQ-';
		if($warehouse != 'cutting'){
			$WHERE = " AND (a.tipe_product='$warehouse' OR a.tipe_product='pipe fitting')";
			$TABLE = "production_detail";
			$FIELD1 = 'id_produksi';
			$FIELD2 = '"" AS length_split, b.daycode,';
			$RPC1 = 'PRO-';
			$WHERE3 = "AND b.status='1' ";

			$WHERE2 = "AND b.id_category != 'pipe'";
			if($warehouse == 'pipe'){
				$WHERE2 = "AND b.id_category = 'pipe'";
			}
		}

		if($tanda == 'out'){
			$SQL = "SELECT 
						a.*,
						b.id_category,
						$FIELD2
						REPLACE(b.".$FIELD1.",'".$RPC1."','') AS no_ipp
					FROM 
						history_product_fg a
						LEFT JOIN $TABLE b ON a.id_product = b.id
					WHERE
						DATE(a.hist_date) >= '$tgl_awal'
						AND DATE(a.hist_date) <= '$tgl_akhir'
						AND a.tipe = 'out'
						$WHERE
						$WHERE2
						$WHERE3
					";
			$transaksi	= $this->db->query($SQL)->result_array();
		}
		else{
			$SQL = "SELECT 
						a.*,
						b.id_category,
						$FIELD2
						REPLACE(b.".$FIELD1.",'".$RPC1."','') AS no_ipp
					FROM 
						history_product_fg a
						LEFT JOIN $TABLE b ON a.id_product = b.id
					WHERE
						DATE(a.hist_date) >= '$tgl_awal'
						AND DATE(a.hist_date) <= '$tgl_akhir'
						AND a.tipe = 'in'
						$WHERE
						$WHERE2
						$WHERE3
					";
			$transaksi	= $this->db->query($SQL)->result_array();
		}
		$ArrTrans_IN = [];
		foreach ($transaksi as $key => $value) {
			$ArrTrans_IN[$value['id_category']][] = $value;
		}
		$dataArr = [
			'get_in_trans' 	=> $ArrTrans_IN,
			'material' 		=> $material,
			'GET_DETAIL'	=> get_detail_ipp(),
			'GET_DETAIL_FD'	=> get_detail_final_drawing()
		];

		$data_html = $this->load->view('History/show_history_summary_gudang_detail', $dataArr, TRUE);
		// print_r($ArrTrans_IN);
		// echo $data_html;
		// exit;
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function download_excel_summary(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$warehouse	= $this->uri->segment(3);
		$tgl_awal	= date('Y-m-d',strtotime($this->uri->segment(4)));
		$tgl_akhir	= date('Y-m-d',strtotime($this->uri->segment(5)));

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

		// echo '<pre>';
		// print_r($result);
		// exit;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(13);
		$sheet->setCellValue('A'.$Row, 'REPORT SUMMARY PRODUCT');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NM PRODUCT');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'IN');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'OUT');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'No SO');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'Product Name');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, 'SPEC');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);

		$sheet->setCellValue('H'.$NewRow, 'No SPK');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$sheet->setCellValue('I'.$NewRow, 'Tipe');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);

		$sheet->setCellValue('J'.$NewRow, 'Keterangan');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);

		$sheet->setCellValue('K'.$NewRow, 'Kode');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);

		$sheet->setCellValue('L'.$NewRow, 'By');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);

		$sheet->setCellValue('M'.$NewRow, 'Dated');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);

		$WHERE = "AND a.tipe_product='cutting'";
		$TABLE = "so_cutting_detail";
		$WHERE2 = '';
		if($warehouse != 'cutting'){
			$WHERE = " AND (a.tipe_product='$warehouse' OR a.tipe_product='pipe fitting') AND b.kode_spk != 'deadstok'";
			$TABLE = "production_detail";

			$WHERE2 = "AND b.id_category != 'pipe'";
			if($warehouse == 'pipe'){
				$WHERE2 = "AND b.id_category = 'pipe'";
			}
		}

		$SQL = "SELECT 
					b.id_category AS product 
				FROM 
					history_product_fg a
					LEFT JOIN $TABLE b ON a.id_product = b.id
				WHERE
					DATE(a.hist_date) >= '$tgl_awal'
					AND DATE(a.hist_date) <= '$tgl_akhir'
					$WHERE
					$WHERE2
				GROUP BY b.id_category
				";
		// echo $SQL;
		// exit;
		$result		= $this->db->query($SQL)->result_array();
		// TOTAL TRANSAKSI
		$SQL1 = "SELECT 
					COUNT(a.id) AS qty,
					b.id_category 
				FROM 
					history_product_fg a
					LEFT JOIN $TABLE b ON a.id_product = b.id
				WHERE
					DATE(a.hist_date) >= '$tgl_awal'
					AND DATE(a.hist_date) <= '$tgl_akhir'
					AND a.tipe = 'in'
					$WHERE
					$WHERE2
				GROUP BY b.id_category
				";
		$result_in	=  $this->db->query($SQL1)->result_array();
		$ArrSumMaterial_IN = [];
		foreach ($result_in as $key => $value) {
			$ArrSumMaterial_IN[$value['id_category']] = $value['qty'];
		}
		$SQL2 = "SELECT 
					COUNT(a.id) AS qty,
					b.id_category 
				FROM 
					history_product_fg a
					LEFT JOIN $TABLE b ON a.id_product = b.id
				WHERE
					DATE(a.hist_date) >= '$tgl_awal'
					AND DATE(a.hist_date) <= '$tgl_akhir'
					AND a.tipe = 'out'
					$WHERE
					$WHERE2
				GROUP BY b.id_category
				";
		$result_out	= $this->db->query($SQL2)->result_array();
		$ArrSumMaterial_OUT = [];
		foreach ($result_out as $key => $value) {
			$ArrSumMaterial_OUT[$value['id_category']] = $value['qty'];
		}

		$GET_IN_MATERIAL = $ArrSumMaterial_IN;
		$GET_OUT_MATERIAL = $ArrSumMaterial_OUT;
		$GET_WAREHOUSE = get_detail_warehouse();
		$GET_MATERIAL = get_detail_material();
		$GET_IPP = get_detail_ipp();
		$GET_DETAIL	= get_detail_ipp();
		$GET_DETAIL_FD	= get_detail_final_drawing();

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $value){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$IN_MATERIAL    = (!empty($GET_IN_MATERIAL[$value['product']]))?number_format($GET_IN_MATERIAL[$value['product']]):'-';
                $OUT_MATERIAL   = (!empty($GET_OUT_MATERIAL[$value['product']]))?number_format($GET_OUT_MATERIAL[$value['product']]):'-';
				
				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, strtoupper($value['product']));
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $IN_MATERIAL);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $OUT_MATERIAL);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$WHERE = "AND a.tipe_product='cutting'";
				$TABLE = "so_cutting_detail";
				$WHERE2 = '';
				$FIELD1 = 'id_bq';
				$FIELD2 = 'b.length_split,';
				$RPC1 = 'BQ-';
				if($warehouse != 'cutting'){
					$WHERE = " AND (a.tipe_product='$warehouse' OR a.tipe_product='pipe fitting')";
					$TABLE = "production_detail";
					$FIELD1 = 'id_produksi';
					$FIELD2 = '"" AS length_split,';
					$RPC1 = 'PRO-';

					$WHERE2 = "AND b.id_category != 'pipe'";
					if($warehouse == 'pipe'){
						$WHERE2 = "AND b.id_category = 'pipe'";
					}
				}
				
				$SQL = "SELECT 
						a.*,
						b.id_category,
						$FIELD2
						REPLACE(b.".$FIELD1.",'".$RPC1."','') AS no_ipp
					FROM 
						history_product_fg a
						LEFT JOIN $TABLE b ON a.id_product = b.id
					WHERE
						DATE(a.hist_date) >= '$tgl_awal'
						AND DATE(a.hist_date) <= '$tgl_akhir'
						AND a.tipe = 'out'
						AND b.id_category = '".$value['product']."'
						$WHERE
						$WHERE2
					";
				$transaksi_out	= $this->db->query($SQL)->result_array();

				$SQL = "SELECT 
						a.*,
						b.id_category,
						$FIELD2
						REPLACE(b.".$FIELD1.",'".$RPC1."','') AS no_ipp
					FROM 
						history_product_fg a
						LEFT JOIN $TABLE b ON a.id_product = b.id
					WHERE
						DATE(a.hist_date) >= '$tgl_awal'
						AND DATE(a.hist_date) <= '$tgl_akhir'
						AND a.tipe = 'in'
						AND b.id_category = '".$value['product']."'
						$WHERE
						$WHERE2
					";
				$transaksi_in	= $this->db->query($SQL)->result_array();

				$transaksi = array_merge($transaksi_out,$transaksi_in);

				foreach ($transaksi as $key2 => $value2) {
					$awal_row++;
					$awal_col	= 0;

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$NO_SO = (!empty($GET_DETAIL[$value2['no_ipp']]['so_number']))?$GET_DETAIL[$value2['no_ipp']]['so_number']:'';
                	$NO_SPK = (!empty($GET_DETAIL_FD[$value2['id_milik']]['no_spk']))?$GET_DETAIL_FD[$value2['id_milik']]['no_spk']:'';
					$LENGTH_CUT = (!empty($value2['length_split']))?', cut: '.number_format($value2['length_split']):'';
					
					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $NO_SO);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, strtoupper($value2['id_category']));
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, spec_bq2($value2['id_milik']).$LENGTH_CUT);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $NO_SPK);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$tipe	= strtoupper($value2['tipe']);
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $tipe);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$keterangan	= $value2['keterangan'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $keterangan);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$kode	= $value2['kode'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $kode);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$update_by	= $value2['hist_by'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $update_by);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$update_date	= $value2['hist_date'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $update_date);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				}
				
			

			}
		}


		$sheet->setTitle('SUMMARY');
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
		header('Content-Disposition: attachment;filename="summary-product.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}