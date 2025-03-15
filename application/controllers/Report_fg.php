<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_fg extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}

		$this->gudang = "1,2";
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
			'title'			=> 'Report Finish Good',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);

		$this->load->view('Report_new/Report_fg/index',$data);
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

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='left'>".str_replace(',','<br>',str_replace(',,',',',$row['no_spk']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['product']."</div>";
			$nestedData[]	= "<div align='center'>".$row['jenis']."</div>";
			$nestedData[]	= "<div align='center'>".$row['id_trans']."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>";
			$QTY = (!empty($row['id_material']))?'':1;
			$nestedData[]	= "<div align='right'>".$QTY."</div>";

			$qty = $row['qty'];

			$nilai_wip	= 0;
			// if($row['nilai_wip'] > 0 AND $qty > 0){
				$nilai_wip	= $row['nilai_wip'];
			// }
			$material	= 0;
			// if($row['material'] > 0 AND $qty > 0){
				$material	= $row['material'];
			// }
			$wip_direct	= 0;
			// if($row['wip_direct'] > 0 AND $qty > 0){
				$wip_direct	= $row['wip_direct'];
			// }
			$wip_indirect	= 0;
			// if($row['wip_indirect'] > 0 AND $qty > 0){
				$wip_indirect	= $row['wip_indirect'];
			// }
			$wip_consumable	= 0;
			// if($row['wip_consumable'] > 0 AND $qty > 0){
				$wip_consumable	= $row['wip_consumable'];
			// }
			$wip_foh	= 0;
			// if($row['wip_foh'] > 0 AND $qty > 0){
				$wip_foh	= $row['wip_foh'];
			// }
			$material = (empty($row['id_material']))?number_format($material,2):'';
			$wip_direct = (empty($row['id_material']))?number_format($wip_direct,2):'';
			$wip_indirect = (empty($row['id_material']))?number_format($wip_indirect,2):'';
			$wip_consumable = (empty($row['id_material']))?number_format($wip_consumable,2):'';
			$wip_foh = (empty($row['id_material']))?number_format($wip_foh,2):'';

			$nestedData[]	= "<div align='right'>".number_format($nilai_wip,2)."</div>";
			$nestedData[]	= "<div align='right'>".$material."</div>";
			$nestedData[]	= "<div align='right'>".$wip_direct."</div>";
			$nestedData[]	= "<div align='right'>".$wip_indirect."</div>";
			$nestedData[]	= "<div align='right'>".$wip_consumable."</div>";
			$nestedData[]	= "<div align='right'>".$wip_foh."</div>";

			$QTY = (!empty($row['id_material']))?number_format($row['qty_mat'],4):'';
			$cost_book = (!empty($row['id_material']))?number_format($row['cost_book'],2):'';
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='right'>".$QTY."</div>";
			$nestedData[]	= "<div align='right'>".$cost_book."</div>";
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
                    data_erp_fg a,
                    (SELECT @row:=0) r
                WHERE 1=1 ".$WHERE_DATE." 
                    AND (
                        a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.id_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
				    )";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.tanggal'
		);

		$sql .= " ORDER BY a.id DESC,  ".$columns_order_by[$column_order]." ".$column_dir." ";
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

        $WHERE_DATE = "AND a.tanggal LIKE '".date('Y')."-".date('m')."%' ";
		if($tgl_awal != '0'){
			$WHERE_DATE = "AND (DATE( a.tanggal ) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' )";
		}

        $sql = "SELECT a.* FROM data_erp_fg a WHERE 1=1 ".$WHERE_DATE;
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(14);
		$sheet->setCellValue('A'.$Row, 'REPORT FINISH GOOD');
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

		$sheet->setCellValue('C'.$NewRow, 'NO SO');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'PRODUCT');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'ID TRANS');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'NO TRANS');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'QTY');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

        $sheet->setCellValue('H'.$NewRow, 'NO SPK');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

        $sheet->setCellValue('I'.$NewRow, 'Nilai FG');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

        $sheet->setCellValue('J'.$NewRow, 'Material');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Direct Labour PC');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'Indirect Labour PC');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('M'.$NewRow, 'Consumable PC');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(20);

		$sheet->setCellValue('N'.$NewRow, 'FOH PC');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(20);

		$sheet->setCellValue('O'.$NewRow, 'JENIS');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setWidth(20);

		$sheet->setCellValue('P'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setWidth(20);

		$sheet->setCellValue('Q'.$NewRow, 'NM MATERIAL');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setWidth(20);

		$sheet->setCellValue('R'.$NewRow, 'WEIGHT');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setWidth(20);

		$sheet->setCellValue('S'.$NewRow, 'COSTBOOK');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
		$sheet->getColumnDimension('S')->setWidth(20);


		// echo $qDetail1; exit;
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row){
				$no++;
				$awal_row++;
				$awal_col	= 0;

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
				$no_so	= $row['no_so'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$product	= $row['product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

                $awal_col++;
				$id_trans	= $row['id_trans'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$kode_trans	= $row['kode_trans'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$QTY = (!empty($row['id_material']))?'':1;
                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $QTY);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				// $awal_col++;
				// $nilai_unit	= $row['nilai_unit'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $nilai_unit);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$no_spk	= $row['no_spk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);


				$qty	= $row['qty'];

                $awal_col++;
				// $nilai_wip	= 0;
				// if($row['nilai_wip'] > 0 AND $qty > 0){
					$nilai_wip	= $row['nilai_wip'];
				// }
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nilai_wip);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				// $material	= 0;
				// if($row['material'] > 0 AND $qty > 0){
					$material	= $row['material'];
				// }
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				// $wip_direct	= 0;
				// if($row['wip_direct'] > 0 AND $qty > 0){
					$wip_direct	= $row['wip_direct'];
				// }
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $wip_direct);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				// $wip_indirect	= 0;
				// if($row['wip_indirect'] > 0 AND $qty > 0){
					$wip_indirect	= $row['wip_indirect'];
				// }
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $wip_indirect);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				// $wip_consumable	= 0;
				// if($row['wip_consumable'] > 0 AND $qty > 0){
					$wip_consumable	= $row['wip_consumable'];
				// }
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $wip_consumable);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				// $wip_foh	= 0;
				// if($row['wip_foh'] > 0 AND $qty > 0){
					$wip_foh	= $row['wip_foh'];
				// }
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $wip_foh);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$jenis	= $row['jenis'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $jenis);
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
				$qty_mat	= $row['qty_mat'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_mat);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$cost_book	= $row['cost_book'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost_book);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('Report FG');
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
		header('Content-Disposition: attachment;filename="report-finish good.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}