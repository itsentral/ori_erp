<?php
class Total_value_stok extends CI_Controller {

	public function __construct() {
		parent::__construct();
        $this->load->model('master_model');;
	}

    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();
		$data = array(
			'title'			=> 'Warehouse Stok >> Total Value',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		$this->load->view('Total_value/stok',$data);
	}
	
	public function get_data_json_stock(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_stock(
			$requestData['gudang'],
			$requestData['date_filter'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$stock			= $fetch['stock'];
		$rusak			= $fetch['rusak'];
		$query			= $fetch['query'];

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		$dateFilter = (!empty($requestData['date_filter']))?$requestData['date_filter']:date('Y-m-d');
        $GET_PRICEBOOK = getPriceBookByDate($dateFilter);
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

            $PRICEBOOK = (!empty($GET_PRICEBOOK[$row['code_group']]))?$GET_PRICEBOOK[$row['code_group']]:0;

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['code_group'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_item'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_excel'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_accurate'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nama_master'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('con_nonmat_category_awal', 'category', 'id', $row['category_awal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('warehouse', 'nm_gudang', 'id', $row['gudang']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['stock'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK*$row['stock'],2)."</div>";
			
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data,
			"recordsStock"		=> $stock,
			"recordsRusak"		=> $rusak
		);

		echo json_encode($json_data);
	}

	public function query_data_json_stock($gudang, $date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$table = "warehouse_rutin_stock";
		$where_gudang ='';
		$where_date ='';

		if($gudang != '0'){
			$where_gudang = " AND b.category_awal = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			if($gudang != '0'){
				$where_gudang = " AND b.category_awal = '".$gudang."' ";
			}
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_rutin_stock_per_day";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.material_name AS nama_master,
				b.spec,
				b.code_group,
				b.kode_item,
				b.kode_excel,
				b.category_awal,
				b.id_accurate
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.gudang IN ('".getGudangIndirect()."','".getGudangHouseHold()."') ".$where_gudang." ".$where_date." AND b.status='1' AND b.deleted = 'N' AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_item LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_excel LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		$Query_Sum = "
			SELECT
				SUM(a.stock) AS stock,
				SUM(a.rusak) AS rusak
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group
		    WHERE 1=1 AND a.gudang IN ('".getGudangIndirect()."','".getGudangHouseHold()."') ".$where_gudang." ".$where_date." AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_item LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_excel LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$stock = $rusak = 0;
		$Hasil_SUM		   = $this->db->query($Query_Sum)->result_array();
		if($Hasil_SUM){
			$stock		= $Hasil_SUM[0]['stock'];
			$rusak	= $Hasil_SUM[0]['rusak'];
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$data['stock'] 	= $stock;
		$data['rusak'] = $rusak;
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'code_group',
			2 => 'kode_item',
			3 => 'kode_excel',
			4 => 'material_name',
			5 => 'spec',
			6 => 'b.category_awal',
			7 => 'stock',
			8 => 'rusak'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function ExcelGudangStok(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$gudang			= $this->uri->segment(3);
		$date_filter	= $this->uri->segment(4);

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

		$table = "warehouse_rutin_stock";
		$where_gudang ='';
		$where_date ='';

		if($gudang != '0'){
			$where_gudang = " AND b.category_awal = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			if($gudang != '0'){
				$where_gudang = " AND b.category_awal = '".$gudang."' ";
			}
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_rutin_stock_per_day";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.material_name AS nama_master,
				b.spec,
				b.code_group,
				b.kode_item,
				b.kode_excel,
				b.category_awal AS category,
				b.id_accurate
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.gudang IN ('".getGudangIndirect()."','".getGudangHouseHold()."') ".$where_gudang." ".$where_date." AND b.status='1' AND b.deleted = 'N'
		";
		$restDetail1	= $this->db->query($sql)->result_array();
		
		$tanggal_update = (!empty($date_filter))?" (".date('d F Y', strtotime($date_filter)).")":" (".date('d F Y').")";
		$tanggal_update2 = (!empty($date_filter))?date('Y-m-d', strtotime($date_filter)):date('Y-m-d');

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'TOTAL VALUE GUDANG STOCK - '.$tanggal_update);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'CODE PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'CODE ITEM');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'KODE EXCEL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'ID ACCURATE');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'CATEGORY');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'SPEC');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'STOCK');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

        $sheet->setCellValue('J'.$NewRow, 'PRICE BOOK');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'TOTAL VALUE');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);


        $GET_PRICEBOOK = getPriceBookByDate($tanggal_update2);
		// echo $qDetail1; exit;

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
				$code_group	= $row_Cek['code_group'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$kode_item	= strtoupper($row_Cek['kode_item']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_item);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$kode_excel	= $row_Cek['kode_excel'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_excel);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_accurate	= $row_Cek['id_accurate'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_accurate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$CATEGORY = get_name('con_nonmat_category_awal','category','id',$row_Cek['category']);
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $CATEGORY);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$material_name	= strtoupper($row_Cek['nama_master']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$spec	= strtoupper($row_Cek['spec']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$stock	= $row_Cek['stock'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $PRICEBOOK = (!empty($GET_PRICEBOOK[$row_Cek['code_group']]))?$GET_PRICEBOOK[$row_Cek['code_group']]:0;
                $TOTAL_VALUE = $stock * $PRICEBOOK;

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $PRICEBOOK);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $TOTAL_VALUE);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('Total Value Stok');
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
		header('Content-Disposition: attachment;filename="total-value-gudang-stok.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}