<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History_pembelian extends CI_Controller {

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
			'title'			=> 'Pembelian Non-Material & Jasa >> History Pembelian Non-Material',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data History pembelian non material');
		$this->load->view('History/history_pembelian_non_material',$data);
	}

	public function material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Pembelian Material >> History Pembelian Material',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data History pembelian material');
		$this->load->view('History/history_pembelian_material',$data);
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
			$requestData['range'],
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

            $TGL_DIBUTUHKAN = (!empty($row['tgl_dibutuhkan']) AND $row['tgl_dibutuhkan'] != '0000-00-00')?date('d-M-Y', strtotime($row['tgl_dibutuhkan'])):'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$NAMA = $row['category'];
			if($row['category'] == 'non rutin'){
				$NAMA = 'department';
			}
			if($row['category'] == 'rutin'){
				$NAMA = 'stok';
			}
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($NAMA))."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_pr']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_po']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['tgl_po']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-no_po='".$row['no_po']."'>DETAIL</span></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price'])."</div>";
			$nestedData[]	= "<div align='center'>".$TGL_DIBUTUHKAN."</div>";

			$QUERY = "	SELECT 
							c.tanggal 
						FROM 
							tran_po_detail a
							LEFT JOIN warehouse_adjustment_detail b ON a.id=b.id_po_detail AND a.id_barang=b.id_material
							LEFT JOIN warehouse_adjustment c ON b.kode_trans=c.kode_trans
						WHERE a.no_po = '".$row['no_po']."' AND c.tanggal IS NOT NULL";
			$RESULT = $this->db->query($QUERY)->result_array();
			$dataArr = [];
			if(!empty($RESULT)){
				foreach ($RESULT as $key => $value) {
					$dataArr[] = date('d-M-Y',strtotime($value['tanggal']));
				}
			}
			$DATE_INCOMING = array_unique($dataArr);
			$IMPLODE = implode("<br>", $DATE_INCOMING);

			$nestedData[]	= "<div align='center'>".$IMPLODE."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
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

	public function queryDataJSON($range=null, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_range = "";
        if($range > 0){
			$exP = explode(' - ', $range);
			$date_awal = date('Y-m-d', strtotime($exP[0]));
			$date_akhir = date('Y-m-d', strtotime($exP[1]));
			// echo $exP[0];exit;
            $where_range = "AND DATE(z.created_date) BETWEEN '".$date_awal."' AND '".$date_akhir."' ";
        }

		$sql = "	SELECT
                        (@row:=@row+1) AS nomor,
                        z.category,
                        c.no_pr_group AS no_pr,
                        a.no_po,
                        DATE(z.created_date) AS tgl_po,
						z.created_by,
                        z.nm_supplier,
                        a.tgl_dibutuhkan,
                        a.id_barang,
                        a.nm_barang,
                        a.qty_po,
                        a.qty_in,
                        a.net_price AS unit_price,
                        SUM(a.total_price) AS total_price
                    FROM
                        tran_po_detail a
                        LEFT JOIN tran_po_header z ON a.no_po = z.no_po
                        LEFT JOIN tran_rfq_detail b ON a.no_po = b.no_po  AND a.id_barang = b.id_barang
                        LEFT JOIN tran_pr_detail c ON b.no_rfq = c.no_rfq  AND b.id_barang = c.id_barang,
                        (SELECT @row:=0) r
                    WHERE
                        a.deleted = 'N' ".$where_range ."
                        AND b.deleted = 'N'
                        AND c.app_status = 'Y'
                        AND (
                            z.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
                            OR z.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
                            OR c.no_pr_group LIKE '%".$this->db->escape_like_str($like_value)."%'
                            OR a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
                        )
                    GROUP BY
                        a.no_po
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'z.category',
			2 => 'c.no_pr_group',
			3 => 'a.no_po',
			4 => 'z.created_date',
			5 => 'z.nm_supplier'
		);

		$sql .= " ORDER BY a.no_po DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function getDataJSONMaterial(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONMaterial(
			$requestData['range'],
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

            $TGL_DIBUTUHKAN = (!empty($row['tgl_dibutuhkan']) AND $row['tgl_dibutuhkan'] != '0000-00-00')?date('d-M-Y', strtotime($row['tgl_dibutuhkan'])):'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_pr']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_po']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['tgl_po']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-no_po='".$row['no_po']."'>DETAIL</span></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price'])."</div>";
			$nestedData[]	= "<div align='center'>".$TGL_DIBUTUHKAN."</div>";

			$QUERY = "	SELECT 
							(c.checked_date) AS tanggal 
						FROM 
							tran_material_po_detail a
							LEFT JOIN warehouse_adjustment_detail b ON a.id=b.id_po_detail AND a.id_material=b.id_material
							LEFT JOIN warehouse_adjustment c ON b.kode_trans=c.kode_trans
						WHERE a.no_po = '".$row['no_po']."' AND c.checked_date IS NOT NULL";
			$RESULT = $this->db->query($QUERY)->result_array();
			$dataArr = [];
			if(!empty($RESULT)){
				foreach ($RESULT as $key => $value) {
					$dataArr[] = date('d-M-Y',strtotime($value['tanggal']));
				}
			}
			$DATE_INCOMING = array_unique($dataArr);
			$IMPLODE = implode("<br>", $DATE_INCOMING);

			$nestedData[]	= "<div align='center'>".$IMPLODE."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
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

	public function queryDataJSONMaterial($range=null, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_range = "";
        if($range > 0){
			$exP = explode(' - ', $range);
			$date_awal = date('Y-m-d', strtotime($exP[0]));
			$date_akhir = date('Y-m-d', strtotime($exP[1]));
			// echo $exP[0];exit;
            $where_range = "AND DATE(z.created_date) BETWEEN '".$date_awal."' AND '".$date_akhir."' ";
        }

		$sql = "	SELECT
                        (@row:=@row+1) AS nomor,
                        c.no_pr AS no_pr,
                        a.no_po,
                        DATE(z.created_date) AS tgl_po,
						z.created_by,
                        z.nm_supplier,
                        a.tgl_dibutuhkan,
                        a.id_material AS id_barang,
                        a.nm_material AS nm_barang,
                        a.qty_purchase AS qty_po,
                        a.qty_in,
                        a.net_price AS unit_price,
                        SUM(a.total_price) AS total_price
                    FROM
                        tran_material_po_detail a
                        LEFT JOIN tran_material_po_header z ON a.no_po = z.no_po
                        LEFT JOIN tran_material_rfq_detail b ON a.no_po = b.no_po  AND a.id_material = b.id_material
                        LEFT JOIN tran_material_pr_detail c ON b.no_rfq = c.no_rfq  AND b.id_material = c.id_material,
                        (SELECT @row:=0) r
                    WHERE
                        a.deleted = 'N' 
						".$where_range."
                        AND b.deleted = 'N'
                        AND c.deleted_date IS NULL
                        AND (
                            z.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
                            OR z.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
                            OR c.no_pr LIKE '%".$this->db->escape_like_str($like_value)."%'
                            OR a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
                        )
                    GROUP BY
                        a.no_po
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'c.no_pr',
			2 => 'a.no_po',
			3 => 'z.created_date',
			4 => 'z.nm_supplier'
		);

		$sql .= " ORDER BY a.no_po DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_detail($no_po){

		$SQL = "SELECT
					z.category,
					c.no_pr_group AS no_pr,
					a.no_po,
					DATE(z.created_date) AS tgl_po,
					z.nm_supplier,
					a.tgl_dibutuhkan,
					a.id_barang,
					a.nm_barang,
					a.qty_po,
					a.qty_in,
					a.net_price AS unit_price,
					a.total_price AS total_price
				FROM
					tran_po_detail a
					LEFT JOIN tran_po_header z ON a.no_po = z.no_po
					LEFT JOIN tran_rfq_detail b ON a.no_po = b.no_po  AND a.id_barang = b.id_barang
					LEFT JOIN tran_pr_detail c ON b.no_rfq = c.no_rfq  AND b.id_barang = c.id_barang
				WHERE
					a.deleted = 'N' 
					AND b.deleted = 'N'
					AND c.app_status = 'Y'
					AND a.no_po = '$no_po'
					";
		$list_material		= $this->db->query($SQL)->result_array();

		$data = array(
			'result' 	=> $list_material

		);
		$this->load->view('History/modal_detail', $data);
	}

	public function modal_detail_material($no_po){

		$SQL = "SELECT
					c.no_pr AS no_pr,
					a.no_po,
					DATE(z.created_date) AS tgl_po,
					z.nm_supplier,
					a.tgl_dibutuhkan,
					a.id_material AS id_barang,
					a.nm_material AS nm_barang,
					a.qty_purchase AS qty_po,
					a.qty_in,
					a.net_price AS unit_price,
					a.total_price AS total_price
				FROM
					tran_material_po_detail a
					LEFT JOIN tran_material_po_header z ON a.no_po = z.no_po
					LEFT JOIN tran_material_rfq_detail b ON a.no_po = b.no_po  AND a.id_material = b.id_material
					LEFT JOIN tran_material_pr_detail c ON b.no_rfq = c.no_rfq  AND b.id_material = c.id_material
				WHERE
					a.deleted = 'N' 
					AND b.deleted = 'N'
					AND c.deleted_date IS NULL
					AND a.no_po = '$no_po'
					";
		$list_material		= $this->db->query($SQL)->result_array();

		$data = array(
			'result' 	=> $list_material

		);
		$this->load->view('History/modal_detail', $data);
	}

	public function excel_non_material($tgl_awal, $tgl_akhir){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

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

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'HISTORY PEMBELIAN NON-MATERIAL ('.$tgl_awal.' sd '.$tgl_akhir.')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'Category');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'No PR');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'No PO');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Tgl PO');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'Supplier');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'Tgl Permintaan');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Aktual Kedatangan');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'Created');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Department');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Kode');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'Nama Barang');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('M'.$NewRow, 'Qty');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(20);

		$sheet->setCellValue('N'.$NewRow, 'Unit Price');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(20);

		$sheet->setCellValue('O'.$NewRow, 'Total Price');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setWidth(20);

		// $sheet->setCellValue('P'.$NewRow, 'Total Price');
		// $sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableHeader);
		// $sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		// $sheet->getColumnDimension('P')->setWidth(20);


		$sql = "	SELECT
                        z.category,
                        c.no_pr_group AS no_pr,
						c.no_pr AS no_pr_planning,
                        a.no_po,
                        DATE(z.created_date) AS tgl_po,
						z.created_by,
                        z.nm_supplier,
                        a.tgl_dibutuhkan,
                        a.id_barang,
                        a.nm_barang,
                        a.qty_po,
                        a.qty_in,
                        a.net_price AS unit_price,
                        a.total_price AS total_price
                    FROM
                        tran_po_detail a
                        LEFT JOIN tran_po_header z ON a.no_po = z.no_po
                        LEFT JOIN tran_rfq_detail b ON a.no_po = b.no_po  AND a.id_barang = b.id_barang
                        LEFT JOIN tran_pr_detail c ON b.no_rfq = c.no_rfq  AND b.id_barang = c.id_barang
                    WHERE
                        a.deleted = 'N' 
                        AND b.deleted = 'N'
                        AND c.app_status = 'Y'
						AND DATE(z.created_date) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
		";
		// echo $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$TGL_DIBUTUHKAN = (!empty($row['tgl_dibutuhkan']) AND $row['tgl_dibutuhkan'] != '0000-00-00')?date('d-M-Y', strtotime($row['tgl_dibutuhkan'])):'';

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$NAMA = $row['category'];
				if($row['category'] == 'non rutin'){
					$NAMA = 'department';
				}
				if($row['category'] == 'rutin'){
					$NAMA = 'stok';
				}
				$category		= $NAMA;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_pr		= $row['no_pr'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_pr);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_po		= $row['no_po'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_po);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$tgl_po		= date('d-M-Y', strtotime($row['tgl_po']));
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tgl_po);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_supplier	= $row['nm_supplier'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_supplier);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $TGL_DIBUTUHKAN);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$QUERY = "	SELECT 
							c.tanggal 
						FROM 
							tran_po_detail a
							LEFT JOIN warehouse_adjustment_detail b ON a.id=b.id_po_detail AND a.id_barang=b.id_material
							LEFT JOIN warehouse_adjustment c ON b.kode_trans=c.kode_trans
						WHERE a.no_po = '".$row['no_po']."' AND c.tanggal IS NOT NULL";
				$RESULT = $this->db->query($QUERY)->result_array();
				$dataArr = [];
				if(!empty($RESULT)){
					foreach ($RESULT as $key => $value) {
						$dataArr[] = date('d-M-Y',strtotime($value['tanggal']));
					}
				}
				$DATE_INCOMING = array_unique($dataArr);
				$IMPLODE = implode(", ", $DATE_INCOMING);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $IMPLODE);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$created_by	= $row['created_by'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $created_by);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$getPRDept = $this->db->get_where('rutin_non_planning_header',array('no_pr'=>$row['no_pr_planning']))->result_array();
				$id_dept = (!empty($getPRDept[0]['id_dept']))?$getPRDept[0]['id_dept']:0;
				$nm_dept = get_name('department','nm_dept','id',$id_dept);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_dept);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_barang	= $row['id_barang'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_barang	= $row['nm_barang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_po	= $row['qty_po'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_po);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$unit_price	= $row['unit_price'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $unit_price);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$total_price	= $row['total_price'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $total_price);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);


			}
		}


		$sheet->setTitle('NON-MATERIAL');
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
		header('Content-Disposition: attachment;filename="history-pembelian-non-material.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_material($tgl_awal, $tgl_akhir){
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

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'HISTORY PEMBELIAN MATERIAL ('.$tgl_awal.' sd '.$tgl_akhir.')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'No PR');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'No PO');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'Tgl PO');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Supplier');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'Tgl Permintaan');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'Aktual Kedatangan');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Created By');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'Kode');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Nm Material');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Qty');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'Unit Price');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('M'.$NewRow, 'Total Price');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(20);


		$sql = "	SELECT
                        c.no_pr AS no_pr,
                        a.no_po,
                        DATE(z.created_date) AS tgl_po,
						z.created_by,
                        z.nm_supplier,
                        a.tgl_dibutuhkan,
                        a.id_material AS id_barang,
                        a.nm_material AS nm_barang,
                        a.qty_purchase AS qty_po,
                        a.qty_in,
                        a.net_price AS unit_price,
                        a.total_price AS total_price
                    FROM
                        tran_material_po_detail a
                        LEFT JOIN tran_material_po_header z ON a.no_po = z.no_po
                        LEFT JOIN tran_material_rfq_detail b ON a.no_po = b.no_po  AND a.id_material = b.id_material
                        LEFT JOIN tran_material_pr_detail c ON b.no_rfq = c.no_rfq  AND b.id_material = c.id_material
                    WHERE
                        a.deleted = 'N' 
                        AND b.deleted = 'N'
                        AND c.deleted_date IS NULL
						AND DATE(z.created_date) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
		";
		$restDetail1	= $this->db->query($sql)->result_array();

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$TGL_DIBUTUHKAN = (!empty($row['tgl_dibutuhkan']) AND $row['tgl_dibutuhkan'] != '0000-00-00')?date('d-M-Y', strtotime($row['tgl_dibutuhkan'])):'';

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_pr		= $row['no_pr'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_pr);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_po		= $row['no_po'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_po);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$tgl_po		= date('d-M-Y', strtotime($row['tgl_po']));
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tgl_po);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_supplier	= $row['nm_supplier'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_supplier);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $TGL_DIBUTUHKAN);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				

				$QUERY = "	SELECT 
							(c.checked_date) AS tanggal 
						FROM 
							tran_material_po_detail a
							LEFT JOIN warehouse_adjustment_detail b ON a.id=b.id_po_detail AND a.id_material=b.id_material
							LEFT JOIN warehouse_adjustment c ON b.kode_trans=c.kode_trans
						WHERE a.no_po = '".$row['no_po']."' AND c.checked_date IS NOT NULL";
				$RESULT = $this->db->query($QUERY)->result_array();
				$dataArr = [];
				if(!empty($RESULT)){
					foreach ($RESULT as $key => $value) {
						$dataArr[] = date('d-M-Y',strtotime($value['tanggal']));
					}
				}
				$DATE_INCOMING = array_unique($dataArr);
				$IMPLODE = implode(", ", $DATE_INCOMING);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $IMPLODE);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$created_by	= $row['created_by'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $created_by);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				
				$awal_col++;
				$id_material	= $row['id_barang'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= $row['nm_barang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_purchase	= $row['qty_po'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_purchase);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$unit_price	= $row['unit_price'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $unit_price);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$total_price	= $row['total_price'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $total_price);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('MATERIAL');
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
		header('Content-Disposition: attachment;filename="history-pembelian-material.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
}
