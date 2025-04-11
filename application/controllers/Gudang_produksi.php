<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang_produksi extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		// Your own constructor code
		if (!$this->session->userdata('isORIlogin')) {
			redirect('login');
		}
	}

	public function index()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Product On Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group
		);
		history('View data product on spool');
		$this->load->view('Gudang_produksi/index', $data);
	}

	public function server_side_gudang_produksi()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_gudang_produksi(
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

		$FLAG = '';
		$GET_DET_IPP = get_detail_ipp();
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$no_ipp 		= $row['no_ipp'];
			$tandaTanki = substr($no_ipp,0,4);
			if($tandaTanki != 'IPPT'){
				$spec 			= spec_bq2($row['id_milik']);
				$nm_customer 	= (!empty($GET_DET_IPP[$no_ipp]['nm_customer']))?$GET_DET_IPP[$no_ipp]['nm_customer']:'';
				$nm_project 	= (!empty($GET_DET_IPP[$no_ipp]['nm_project']))?$GET_DET_IPP[$no_ipp]['nm_project']:'';
				$no_so 			= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'';
				$nm_product		= $row['id_category'];
			}
			else{
				$spec 			= $this->tanki_model->get_spec($row['id_milik']);
				$GET_DET_TANKI	= $this->tanki_model->get_ipp_detail($no_ipp);
				$nm_customer 	= (!empty($GET_DET_TANKI['customer']))?$GET_DET_TANKI['customer']:'';
				$nm_project 	= (!empty($GET_DET_TANKI['nm_project']))?$GET_DET_TANKI['nm_project']:'';
				$no_so 			= (!empty($GET_DET_TANKI['no_so']))?$GET_DET_TANKI['no_so']:'';
				$nm_product		= $row['nm_tanki'];
			}

			$Spool_create = (!empty($row['created_spool_date']))?date('d-M-Y',strtotime($row['created_spool_date'])):'';
			$Spool_Lock = (!empty($row['created_spoolLock_date']))?date('d-M-Y',strtotime($row['created_spoolLock_date'])):'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$Spool_create."</div>";
			$nestedData[]	= "<div align='center'>".$Spool_Lock."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['spool_induk'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_product)."</div>";
			$nestedData[]	= "<div align='center'>".$no_so."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_project)."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='center'>".$row['qty_product']."</div>";
			$nestedData[]	= "<div align='center'>".$row['sts']."</div>";
			
			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_gudang_produksi($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		if($date_filter == ''){
		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					REPLACE(a.id_produksi,'PRO-','') AS no_ipp,
					COUNT(a.id) AS qty_product,
					MAX(a.spool_date) AS created_spool_date,
					MAX(a.lock_spool_date) AS created_spoolLock_date
				FROM
					spool_group_all a,
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.spool_induk IS NOT NULL
					-- AND (a.lock_spool_date IS NULL OR a.lock_spool_date IS NULL)
					AND a.release_spool_date IS NULL
					AND (
						a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.spool_induk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
				GROUP BY a.sts, a.no_spk, a.spool_induk
			";
		}
		else{
			$where = " AND a.category='produksi'";

			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.no_ipp,
					a.id_milik,
					a.product AS id_category,
					a.no_spk,
					a.sts,
					a.spool_induk,
					a.product AS nm_tanki,
					COUNT(a.id) AS qty_product,
					MAX(a.spool_in) AS created_spool_date,
					MAX(a.spool_out) AS created_spoolLock_date
				FROM
					stock_barang_jadi_per_day a,
					(SELECT @row:=0) r
				WHERE 1=1
					AND DATE(a.hist_date) = '".$date_filter."'
					".$where."
					AND (
						a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				GROUP BY a.sts, a.no_spk, a.spool_induk
			";
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'spool_induk',
			2 => 'no_spk'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function ExcelGudangProduksi($date_filter){
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

		
		$where = "";
		if($date_filter == '0'){
			$sql = "SELECT
					a.*,
					REPLACE(a.id_produksi,'PRO-','') AS no_ipp,
					COUNT(a.id) AS qty_product,
					MAX(a.spool_date) AS created_spool_date,
					MAX(a.lock_spool_date) AS created_spoolLock_date
				FROM
					spool_group_all a
				WHERE 1=1 
					AND a.spool_induk IS NOT NULL
					-- AND (a.lock_spool_date IS NULL OR a.lock_spool_date IS NULL)
					AND a.release_spool_date IS NULL
				GROUP BY a.sts, a.no_spk, a.spool_induk
			";
		}
		else{

			$where = " AND a.category='produksi'";

			$sql = "
				SELECT
					a.no_ipp,
					a.id_milik,
					a.no_spk,
					a.sts,
					a.product AS id_category,
					a.product AS nm_tanki,
					a.spool_induk,
					COUNT(a.id) AS qty_product,
					MAX(a.spool_in) AS created_spool_date,
					MAX(a.spool_out) AS created_spoolLock_date
				FROM
					stock_barang_jadi_per_day a,
					(SELECT @row:=0) r
				WHERE 1=1
					AND DATE(a.hist_date) = '".$date_filter."'
					".$where."
				GROUP BY a.sts, a.no_spk, a.spool_induk
			";
		}
		// ECHO $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		$tanggal = ($date_filter != '0')?date('d-M-Y',strtotime($date_filter)):'';

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(12);
		$sheet->setCellValue('A'.$Row, 'GUDANG PRODUKSI '.$tanggal);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'No SO');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'No SPK');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Customer');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'Project');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spec');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'QTY');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'Type');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Kode Spool');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Tgl. In');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'Tgl. Out');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$DETAIL_IPP = get_detail_ipp();
		// echo $qDetail1; exit;

		$GET_DET_IPP = get_detail_ipp();
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$no_ipp 		= $row_Cek['no_ipp'];
				$tandaTanki = substr($no_ipp,0,4);
				if($tandaTanki != 'IPPT'){

					$product 		= $row_Cek['id_category'];
					$spec 			= spec_bq2($row_Cek['id_milik']);
					$nm_customer 	= (!empty($GET_DET_IPP[$no_ipp]['nm_customer']))?$GET_DET_IPP[$no_ipp]['nm_customer']:'';
					$nm_project 	= (!empty($GET_DET_IPP[$no_ipp]['nm_project']))?$GET_DET_IPP[$no_ipp]['nm_project']:'';
					$no_so 			= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'';
				}
				else{
					$spec 			= $this->tanki_model->get_spec($row_Cek['id_milik']);
					$GET_DET_TANKI	= $this->tanki_model->get_ipp_detail($no_ipp);
					$nm_customer 	= (!empty($GET_DET_TANKI['customer']))?$GET_DET_TANKI['customer']:'';
					$nm_project 	= (!empty($GET_DET_TANKI['nm_project']))?$GET_DET_TANKI['nm_project']:'';
					$no_so 			= (!empty($GET_DET_TANKI['no_so']))?$GET_DET_TANKI['no_so']:'';
					$product		= $row_Cek['nm_tanki'];
				}
				$Spool_create 	= (!empty($row_Cek['created_spool_date']))?date('d-M-Y',strtotime($row_Cek['created_spool_date'])):'';
				$Spool_Lock 		= (!empty($row_Cek['created_spoolLock_date']))?date('d-M-Y',strtotime($row_Cek['created_spoolLock_date'])):'';

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
				$no_spk			= strtoupper($row_Cek['no_spk']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$id_category	= strtoupper($product);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qtyTotal	= $row_Cek['qty_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qtyTotal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$sts	= $row_Cek['sts'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sts);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$spool_induk	= $row_Cek['spool_induk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spool_induk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Spool_create);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $Spool_Lock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

			}
		}


		$sheet->setTitle('Product On Spool');
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
		header('Content-Disposition: attachment;filename="product-on-spool.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}