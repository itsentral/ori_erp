<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang_wip_value extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	//GUDANG WIP
    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Gudang WIP Value',
			'action'		=> 'index',
			'row_group'		=> $data_Group
		);
		$this->load->view('Gudang_history/index_wip',$data);
	}

	public function server_side_wip(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_wip(
			$requestData['status'],
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

		$FLAG = str_replace('_',' ',$requestData['status']);
		$date_filter = $requestData['date_filter'];
		$GET_IPP = get_detail_ipp();

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

			$update_spk_1 = "";
			$update_spk_2 = "";
			$print_spk = "";
			$view_spk = "";

			if($date_filter==''){
				$customer 	= (!empty($GET_IPP[$row['no_ipp']]['nm_customer']))?$GET_IPP[$row['no_ipp']]['nm_customer']:'';
				$project 	= (!empty($GET_IPP[$row['no_ipp']]['nm_project']))?$GET_IPP[$row['no_ipp']]['nm_project']:'';
				$NOMOR_SO 	= (!empty($GET_IPP[$row['no_ipp']]['so_number']))?$GET_IPP[$row['no_ipp']]['so_number']:'';
				$product 	= $row['id_category'];
				$spec 		= spec_bq2($row['id_milik']);
				$spool_induk 	= $row['spool_induk'];
				$no_drawing 	= $row['no_drawing'];
			}
			else{
				$customer 	= $row['nm_customer'];
				$project 	= $row['nm_project'];
				$NOMOR_SO 	= $row['no_so'];
				$product 	= $row['product'];
				$spec 		= $row['spec'];
				$spool_induk 	= $row['spool_induk'];
				$no_drawing 	= $row['no_drawing'];
			}

			if($FLAG == 'tanki'){
				$customer 	= $this->tanki_model->get_ipp_detail($row['no_ipp'])['customer'];
            	$project 	= $this->tanki_model->get_ipp_detail($row['no_ipp'])['nm_project'];
            	$NOMOR_SO 	= $this->tanki_model->get_ipp_detail($row['no_ipp'])['no_so'];
				$product 	= $row['id_product'];
				$spec 		= $this->tanki_model->get_spec($row['id_milik']);
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($product)."</div>";
			$nestedData[]	= "<div align='center'>".$NOMOR_SO."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($project)."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='center'><b>".number_format($row['qtyTotal'])."</b></div>";

			if($date_filter=='' AND $FLAG != 'spool' AND $FLAG != 'field joint'){
				$GetQtyTurun 	= $this->db->get_where('production_detail',array('id_milik'=>$row['id_milik'],'sts_produksi'=>'Y'))->result_array();
				$GetQtyQC 		= $this->db->get_where('production_detail',array('id_milik'=>$row['id_milik'],'sts_produksi'=>'Y','fg_date !='=>NULL))->result_array();

				$nestedData[]	= "<div align='center'><b class='text-primary'>".number_format(COUNT($GetQtyTurun))."</b></div>";
				$nestedData[]	= "<div align='center'><b class='text-success'>".number_format(COUNT($GetQtyQC))."</b></div>";
			}
			else{
				$nestedData[]	= "<div align='center'>-</div>";
				$nestedData[]	= "<div align='center'>-</div>";
			}

			$totalValue = $row['nilai_value'] * $row['qtyTotal'];
			$nestedData[]	= "<div align='right'>".number_format($row['nilai_value'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($totalValue,2)."</div>";
			$nestedData[]	= "<div align='left'>".$spool_induk."</div>";
			$nestedData[]	= "<div align='left'>".$no_drawing."</div>";
			
			
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

	public function query_data_wip($status, $date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		$status = str_replace('_',' ',$status);
		if($date_filter == ''){
			$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
			$LEFT_JOIN = "";
			$FIELD_CUTTING = "";
			if($status == 'pipe'){
				$where = " AND a.sts_cutting != 'Y' AND c.id_category='pipe' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";
				$GROUP_BY = 'a.id_milik';
			}
			if($status == 'cutting'){
				$where = " AND d.id IS NOT NULL AND c.id_category='pipe' AND d.app = 'Y' AND a.sts_cutting = 'Y' ";
				$LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header,";
				$FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke,'.' ,f.cutting_ke)";
				$GROUP_BY = 'a.id';
			}
			if($status == 'fitting'){
				$where = " AND a.sts_cutting != 'Y' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";
				$GROUP_BY = 'a.id_milik';
			}
			if($status == 'tanki'){
				$where = " AND a.product_code_cut = 'tanki' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "a.product_code";
				$GROUP_BY = 'a.id_milik';
			}

			if($status == 'pipe' OR $status == 'cutting' OR $status == 'fitting'){
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					COUNT(a.id_milik) AS qtyTotal,
					REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
					c.diameter_1,
					c.thickness,
					".$FIELD_CUTTING."
					e.qty AS tot_qty,
					AVG(a.finish_good) AS nilai_value
				FROM
					production_detail a
					LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
					LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
					LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke".$LEFT_JOIN."
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.upload_real = 'Y'
					AND a.upload_real2 = 'Y'
					AND a.kode_spk IS NOT NULL 
					AND a.kode_delivery IS NULL
					AND a.kode_spool IS NULL
					AND a.closing_produksi_date IS NOT NULL
					AND a.release_to_costing_date IS NULL
					AND a.fg_date IS NULL
					AND a.id_category NOT IN ".DirectFinishGood()."
					".$where."
					".$where2."
					AND (
						a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR ".$LIKE_PRODUCT_CODE." LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
					GROUP BY ".$GROUP_BY."
			";
					}

			if($status == 'spool'){
				$sql = "
					SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
						COUNT(a.id_milik) AS qtyTotal,
						AVG(a.nilai_fg) AS nilai_value
					FROM
						spool_group_all a,
						(SELECT @row:=0) r
					WHERE 1=1 
						AND a.release_spool_date IS NULL 
						AND a.lock_spool_date IS NOT NULL 
						".$where2."
						AND (
							a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
						)
					GROUP BY
						a.spool_induk, a.no_spk
				";
			}

			if(str_replace('_',' ',$status) == 'field joint'){
				$sql = "
					SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						a.qty AS qtyTotal,
						z.id_category,
						(a.nilai_value/a.qty) AS nilai_value,
						'' AS spool_induk,
						'' AS no_drawing
					FROM
						outgoing_field_joint a
						LEFT JOIN so_detail_header z ON a.id_milik = z.id
						LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp)=b.id_bq,
						(SELECT @row:=0) r
					WHERE 1=1
						AND a.qc_date IS NULL
						AND a.deleted_date IS NULL
						AND (
							a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						)
				";
			}

		}
		else{

			$where = " AND a.category='".$status."'";

			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					a.product AS id_category,
					COUNT(a.id) AS qtyTotal,
					AVG(a.nilai_value) AS nilai_value
				FROM
					stock_barang_wip_per_day a,
					(SELECT @row:=0) r
				WHERE 1=1
					AND DATE(a.hist_date) = '".$date_filter."'
					AND a.product NOT IN ".DirectFinishGood()."
					".$where."
					AND (
						a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				GROUP BY a.no_spk
			";
		}
		

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_spk',
			2 => 'id_category',
			3 => 'kode_spk'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function excel_gudang_wip($date_filter,$status){
		$status = str_replace('_',' ',$status);
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
			$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
			$LEFT_JOIN = "";
			$FIELD_CUTTING = "";
			if($status == 'pipe'){
				$where = " AND a.sts_cutting != 'Y' AND c.id_category='pipe' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";
				$GROUP_BY = 'a.id_milik';
			}
			if($status == 'cutting'){
				$where = " AND d.id IS NOT NULL AND c.id_category='pipe' AND d.app = 'Y' AND a.sts_cutting = 'Y' ";
				$LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header,";
				$FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke,'.' ,f.cutting_ke)";
				$GROUP_BY = 'a.id';
			}
			if($status == 'fitting'){
				$where = " AND a.sts_cutting != 'Y' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";
				$GROUP_BY = 'a.id_milik';
			}
			if($status == 'tanki'){
				$where = " AND a.product_code_cut = 'tanki' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "a.product_code";
				$GROUP_BY = 'a.id_milik';
			}

			

			if($status == 'pipe' OR $status == 'cutting' OR $status == 'fitting'){

			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					COUNT(a.id_milik) AS qtyTotal,
					b.spk1_cost,
					b.spk2_cost,
					b.no_ipp,
					c.diameter_1,
					c.thickness,
					".$FIELD_CUTTING."
					e.qty AS tot_qty,
					AVG(a.finish_good) AS nilai_value
				FROM
				production_detail a
					LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
					LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
					LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke".$LEFT_JOIN."
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.upload_real = 'Y'
					AND a.upload_real2 = 'Y'
					AND a.kode_spk IS NOT NULL 
					AND a.kode_delivery IS NULL
					AND a.kode_spool IS NULL
					AND a.closing_produksi_date IS NOT NULL
					AND a.release_to_costing_date IS NULL
					AND a.fg_date IS NULL
					AND a.id_category NOT IN ".DirectFinishGood()."
					".$where."
					".$where2."
					GROUP BY ".$GROUP_BY."
			";
			}

			if($status == 'spool'){
				$sql = "
					SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
						COUNT(a.id_milik) AS qtyTotal,
						AVG(a.nilai_fg) AS nilai_value
					FROM
						spool_group_all a,
						(SELECT @row:=0) r
					WHERE 1=1 
						AND a.release_spool_date IS NULL 
						AND a.lock_spool_date IS NOT NULL 
						".$where2."
						AND (
							a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
						)
					GROUP BY
					a.spool_induk, a.no_spk
				";
			}

			if(str_replace('_',' ',$status) == 'field joint'){
				$sql = "
					SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						a.qty AS qtyTotal,
						z.id_category,
						(a.nilai_value/a.qty) AS nilai_value,
						'' AS spool_induk,
						'' AS no_drawing
					FROM
						outgoing_field_joint a
						LEFT JOIN so_detail_header z ON a.id_milik = z.id
						LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp)=b.id_bq,
						(SELECT @row:=0) r
					WHERE 1=1
						AND a.qc_date IS NULL
						AND a.deleted_date IS NULL
						AND (
							a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						)
				";
			}
		}
		else{

			$where = " AND a.category='".$status."'";

			$sql = "
				SELECT
					a.*,
					a.product AS id_category,
					COUNT(a.id) AS qtyTotal,
					AVG(a.nilai_value) AS nilai_value
				FROM
					stock_barang_wip_per_day a
				WHERE 1=1
					AND DATE(a.hist_date) = '".$date_filter."'
					AND a.product NOT IN ".DirectFinishGood()."
					".$where."
				GROUP BY a.no_spk ORDER BY no_so ASC
			";
		}
		// ECHO $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();


		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(12);
		$sheet->setCellValue('A'.$Row, 'DATA WIP '.strtoupper($status));
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

		$sheet->setCellValue('H'.$NewRow, 'QTY WIP');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'QTY ORDER');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'QTY PASS');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Value');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'Total Value');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$sheet->setCellValue('M'.$NewRow, 'Kode Spool');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(20);

		$sheet->setCellValue('N'.$NewRow, 'No Drawing');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setWidth(20);

		$GET_IPP = get_detail_ipp();
		// echo $qDetail1; exit;

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				if($date_filter=='0'){
					$customer 	= (!empty($GET_IPP[$row_Cek['no_ipp']]['nm_customer']))?$GET_IPP[$row_Cek['no_ipp']]['nm_customer']:'';
					$project 	= (!empty($GET_IPP[$row_Cek['no_ipp']]['nm_project']))?$GET_IPP[$row_Cek['no_ipp']]['nm_project']:'';
					$NOMOR_SO 	= (!empty($GET_IPP[$row_Cek['no_ipp']]['so_number']))?$GET_IPP[$row_Cek['no_ipp']]['so_number']:'';
					$product 	= $row_Cek['id_category'];
					$spec 		= spec_bq2($row_Cek['id_milik']);
					$spool_induk 	= $row_Cek['spool_induk'];
					$no_drawing 	= $row_Cek['no_drawing'];
				}
				else{
					$customer 	= $row_Cek['nm_customer'];
					$project 	= $row_Cek['nm_project'];
					$NOMOR_SO 	= $row_Cek['no_so'];
					$product 	= $row_Cek['product'];
					$spec 		= $row_Cek['spec'];
					$spool_induk 	= $row_Cek['spool_induk'];
					$no_drawing 	= $row_Cek['no_drawing'];
				}

				if($status == 'tanki'){
					$customer 	= $this->tanki_model->get_ipp_detail($row_Cek['no_ipp'])['customer'];
					$project 	= $this->tanki_model->get_ipp_detail($row_Cek['no_ipp'])['nm_project'];
					$NOMOR_SO 	= $this->tanki_model->get_ipp_detail($row_Cek['no_ipp'])['no_so'];
					$product 	= $row_Cek['id_product'];
					$spec 		= $this->tanki_model->get_spec($row_Cek['id_milik']);
				}

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NOMOR_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk	= strtoupper($row_Cek['no_spk']);
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
				$sheet->setCellValue($Cols.$awal_row, $customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qtyTotal		= (float)$row_Cek['qtyTotal'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qtyTotal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				if($date_filter=='0'  AND $status != 'spool' AND $status != 'field joint'){
					$GetQtyTurun 	= $this->db->get_where('production_detail',array('id_milik'=>$row_Cek['id_milik'],'sts_produksi'=>'Y'))->result_array();
					$GetQtyQC 		= $this->db->get_where('production_detail',array('id_milik'=>$row_Cek['id_milik'],'sts_produksi'=>'Y','fg_date !='=>NULL))->result_array();
	
					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, COUNT($GetQtyTurun));
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, COUNT($GetQtyQC));
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				}
				else{
					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '-');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '-');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				}
	
				$totalValue = $row_Cek['nilai_value'] * $qtyTotal;

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $row_Cek['nilai_value']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $totalValue);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spool_induk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_drawing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

			}
		}


		$sheet->setTitle('GUDANG WIP');
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
		header('Content-Disposition: attachment;filename="gudang-wip-'.strtolower($status).'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	//GUDANG FINISH GOOD
	public function finish_good(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/finish_good';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Gudang Finish Good Value',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
		);
		$this->load->view('Gudang_history/index_fg',$data);
	}

	public function server_side_fg(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/finish_good';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_fg(
			$requestData['status'],
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

		$DETAIL_IPP = get_detail_ipp();

		$FLAG = $requestData['status'];
		$date_filter = $requestData['date_filter'];
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

			$update_spk_1 = "";
			$update_spk_2 = "";
			$print_spk = "";
			$view_spk = "";

			if($date_filter == ''){
				$customer 	= (!empty($DETAIL_IPP[$row['no_ipp']]['nm_customer']))?$DETAIL_IPP[$row['no_ipp']]['nm_customer']:'';
				$project 	= (!empty($DETAIL_IPP[$row['no_ipp']]['nm_project']))?$DETAIL_IPP[$row['no_ipp']]['nm_project']:'';
				$EXPLODE 	= explode('-',$row['product_code']);
				$NOMOR_SO 	= $EXPLODE[0];
				$product 	= $row['id_category'];
				$spec 		= spec_bq2($row['id_milik']);
				$spool_induk 	= $row['spool_induk'];
				$no_drawing 	= $row['no_drawing'];
			}
			else{
				$customer 	= $row['nm_customer'];
				$project 	= $row['nm_project'];
				$NOMOR_SO 	= $row['no_so'];
				$product 	= $row['product'];
				$spec 		= $row['spec'];
				$spool_induk 	= $row['spool_induk'];
				$no_drawing 	= $row['no_drawing'];
			}

			if($FLAG == 'tanki'){
				$customer 	= $this->tanki_model->get_ipp_detail($row['no_ipp'])['customer'];
            	$project 	= $this->tanki_model->get_ipp_detail($row['no_ipp'])['project'];
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$NOMOR_SO."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($product)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($project)."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qtyTotal'])."</div>";

			$totalValue = $row['nilai_value'] * $row['qtyTotal'];
			$nestedData[]	= "<div align='right'>".number_format($row['nilai_value'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($totalValue,2)."</div>";

			$nestedData[]	= "<div align='left'>".$spool_induk."</div>";
			$nestedData[]	= "<div align='left'>".$no_drawing."</div>";
			
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

	public function query_data_fg($status, $date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$status = str_replace('_',' ',$status);
		$where = "";
		if($date_filter == ''){
			$where2 		= " AND a.id_produksi NOT IN ".filter_not_in()." ";
			$LEFT_JOIN 		= "";
			$FIELD_CUTTING 	= "";
			$LIKE_PRODUCT_CODE 	= "";
			if($status == 'pipe'){
				$where = " AND (a.sts_cutting='N' OR a.sts_cutting IS NULL) AND c.id_category='pipe' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";

			}
			if($status == 'cutting'){
				$where = " AND d.id IS NOT NULL AND c.id_category='pipe' AND d.app = 'Y' AND (f.lock_delivery_date IS NULL AND f.spool_induk IS NULL) ";
				$LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header,";
				$FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke,'.' ,f.cutting_ke)";

			}
			if($status == 'fitting'){
				$where = " AND c.cutting='N' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
				$LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";

			}

			if($status == 'pipe' OR $status == 'cutting' OR $status == 'fitting'){
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					a.product_code AS nomor_so,
					COUNT(a.id_milik) AS qtyTotal,
					b.spk1_cost,
					b.spk2_cost,
					b.no_ipp,
					c.diameter_1,
					c.thickness,
					".$FIELD_CUTTING."
					e.qty AS tot_qty,
					AVG(a.finish_good) AS nilai_value

				FROM
					production_detail a
					LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
					LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
					LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke".$LEFT_JOIN."
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.upload_real = 'Y'
					AND a.upload_real2 = 'Y' 
					AND b.spk1_cost = 'Y' 
					AND b.spk2_cost = 'Y' 
					AND a.kode_spk IS NOT NULL 
					AND a.kode_delivery IS NULL
					AND a.kode_spool IS NULL
					AND a.release_to_costing_date IS NOT NULL
					AND a.fg_date IS NOT NULL
					AND a.lock_delivery_date IS NULL
					".$where."
					".$where2."
					AND (
						a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR ".$LIKE_PRODUCT_CODE." LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				GROUP BY a.id_milik, a.no_spk
			";
					}

			if($status == 'spool'){
				$sql = "
						SELECT
							(@row:=@row+1) AS nomor,
							a.*,
							REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
							COUNT(a.id_milik) AS qtyTotal,
							AVG(a.nilai_fg) AS nilai_value
						FROM
							spool_group_release a,
							(SELECT @row:=0) r
						WHERE 1=1 " . $where . "
							AND a.kode_delivery IS NULL
							AND (
								a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
								OR a.kode_spool LIKE '%" . $this->db->escape_like_str($like_value) . "%'
								OR a.spool_induk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							)
						GROUP BY
						a.spool_induk, a.no_spk
					";
			}

			if(str_replace('_',' ',$status) == 'field joint'){
				$sql = "
					SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						a.qty AS qtyTotal,
						z.id_category,
						(a.nilai_value/a.qty) AS nilai_value,
						'' AS spool_induk,
						'' AS no_drawing,
						b.so_number AS product_code
					FROM
						outgoing_field_joint a
						LEFT JOIN so_detail_header z ON a.id_milik = z.id
						LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp)=b.id_bq,
						(SELECT @row:=0) r
					WHERE 1=1
						AND a.qc_date IS NOT NULL AND a.delivery_date IS NULL AND a.deleted_date IS NULL
						AND (
							a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						)
				";
			}
		}
		else{

			$where = " AND a.category='".$status."'";

			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					a.product AS id_category,
					COUNT(a.id) AS qtyTotal,
					AVG(a.nilai_value) AS nilai_value
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
					)
				GROUP BY a.no_spk
			";
		}

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_milik',
			2 => 'id_milik',
			3 => 'id_milik'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function excel_gudang_fg($date_filter,$status){
		$status = str_replace('_',' ',$status);
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
			$where2 		= " AND a.id_produksi NOT IN ".filter_not_in()." ";
			$LEFT_JOIN 		= "";
			$FIELD_CUTTING 	= "";
			if($status == 'pipe'){
				$where = " AND (a.sts_cutting='N' OR a.sts_cutting IS NULL) AND c.id_category='pipe' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
			}
			if($status == 'cutting'){
				$where = " AND d.id IS NOT NULL AND c.id_category='pipe' AND d.app = 'Y' AND (f.lock_delivery_date IS NULL AND f.spool_induk IS NULL) ";
				$LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header,";
				$FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,";
			}
			if($status == 'fitting'){
				$where = " AND c.cutting='N' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
			}

			if($status == 'spool'){
				$sql = "
						SELECT
							(@row:=@row+1) AS nomor,
							a.*,
							REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
							COUNT(a.id_milik) AS qtyTotal,
							AVG(a.nilai_fg) AS nilai_value
						FROM
							spool_group_release a,
							(SELECT @row:=0) r
						WHERE 1=1 " . $where . "
							AND a.kode_delivery IS NULL
							AND (
								a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
								OR a.kode_spool LIKE '%" . $this->db->escape_like_str($like_value) . "%'
								OR a.spool_induk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							)
						GROUP BY
						a.spool_induk, a.no_spk
					";
			}
			if($status == 'pipe' OR $status == 'cutting' OR $status == 'fitting'){

			$sql = "
				SELECT
					a.*,
					a.product_code AS nomor_so,
					COUNT(a.id_milik) AS qtyTotal,
					b.spk1_cost,
					b.spk2_cost,
					b.no_ipp,
					c.diameter_1,
					c.thickness,
					".$FIELD_CUTTING."
					e.qty AS tot_qty,
					AVG(a.finish_good) AS nilai_value
				FROM
					production_detail a
					LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
					LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
					LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke".$LEFT_JOIN."
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.upload_real = 'Y'
					AND a.upload_real2 = 'Y' 
					AND b.spk1_cost = 'Y' 
					AND b.spk2_cost = 'Y' 
					AND a.kode_spk IS NOT NULL 
					AND a.kode_delivery IS NULL
					AND a.kode_spool IS NULL
					AND a.release_to_costing_date IS NOT NULL
					AND a.fg_date IS NOT NULL
					AND a.lock_delivery_date IS NULL
					".$where."
					".$where2."
				GROUP BY a.id_milik, a.no_spk
			";
			}

			if(str_replace('_',' ',$status) == 'field joint'){
				$sql = "
					SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						a.qty AS qtyTotal,
						z.id_category,
						(a.nilai_value/a.qty) AS nilai_value,
						'' AS spool_induk,
						'' AS no_drawing,
						b.so_number AS product_code
					FROM
						outgoing_field_joint a
						LEFT JOIN so_detail_header z ON a.id_milik = z.id
						LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp)=b.id_bq,
						(SELECT @row:=0) r
					WHERE 1=1
						AND a.qc_date IS NOT NULL AND a.delivery_date IS NULL AND a.deleted_date IS NULL
						AND (
							a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
							OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						)
				";
			}
		}
		else{

			$where = " AND a.category='".$status."'";

			$sql = "
				SELECT
					a.*,
					a.product AS id_category,
					COUNT(a.id) AS qtyTotal,
					AVG(a.nilai_value) AS nilai_value
				FROM
					stock_barang_jadi_per_day a
				WHERE 1=1
					AND DATE(a.hist_date) = '".$date_filter."'
					".$where."
				GROUP BY a.no_spk ORDER BY no_so ASC
			";
		}
		// ECHO $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();


		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'DATA FINISH GOOD '.strtoupper($status));
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

		$sheet->setCellValue('I'.$NewRow, 'Value');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Total Value');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Kode Spool');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->setCellValue('L'.$NewRow, 'No Drawing');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(20);

		$GET_IPP = get_detail_ipp();
		// echo $qDetail1; exit;

		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				if($date_filter=='0'){
					$customer 	= (!empty($GET_IPP[$row_Cek['no_ipp']]['nm_customer']))?$GET_IPP[$row_Cek['no_ipp']]['nm_customer']:'';
					$project 	= (!empty($GET_IPP[$row_Cek['no_ipp']]['nm_project']))?$GET_IPP[$row_Cek['no_ipp']]['nm_project']:'';
					$NOMOR_SO 	= (!empty($GET_IPP[$row_Cek['no_ipp']]['so_number']))?$GET_IPP[$row_Cek['no_ipp']]['so_number']:'';
					$product 	= $row_Cek['id_category'];
					$spec 		= spec_bq2($row_Cek['id_milik']);
					$spool_induk 	= $row_Cek['spool_induk'];
					$no_drawing 	= $row_Cek['no_drawing'];
				}
				else{
					$customer 	= $row_Cek['nm_customer'];
					$project 	= $row_Cek['nm_project'];
					$NOMOR_SO 	= $row_Cek['no_so'];
					$product 	= $row_Cek['product'];
					$spec 		= $row_Cek['spec'];
					$spool_induk 	= $row_Cek['spool_induk'];
					$no_drawing 	= $row_Cek['no_drawing'];
				}

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NOMOR_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk	= strtoupper($row_Cek['no_spk']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qtyTotal	= $row_Cek['qtyTotal'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qtyTotal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$totalValue = $row_Cek['nilai_value'] * $qtyTotal;

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $row_Cek['nilai_value']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $totalValue);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spool_induk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_drawing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

			}
		}


		$sheet->setTitle('GUDANG FINISH GOOD');
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
		header('Content-Disposition: attachment;filename="gudang-finish-good-'.strtolower($status).'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

































	
	public function detail_berat($kode_spk, $ID_PRO, $category, $qty, $status, $length=null, $length_awal=null, $qty_product=null){
		$get_ID_PRO = $this->db->select('id, upload_date')->get_where('production_detail', array('id'=>$ID_PRO))->result();
		$CAT_PRO = $kode_spk.'/'.$get_ID_PRO[0]->upload_date;

		$table_detail =  'tmp_production_real_detail';
		$table_plus =  'tmp_production_real_detail_plus';
		$table_add =  'tmp_production_real_detail_add';
		if($category == 2){
			$table_detail =  'production_real_detail';
			$table_plus =  'production_real_detail_plus';
			$table_add =  'production_real_detail_add';
		}

		$SUM_DETAIL = $this->db->select('batch_number, SUM(material_terpakai) AS berat')->group_by('batch_number')->get_where($table_detail, array('catatan_programmer'=>$CAT_PRO,'id_production_detail'=>$ID_PRO))->result_array();
		$SUM_PLUS 	= $this->db->select('batch_number, SUM(material_terpakai) AS berat')->group_by('batch_number')->get_where($table_plus, array('catatan_programmer'=>$CAT_PRO,'id_production_detail'=>$ID_PRO))->result_array();
		$SUM_ADD 	= $this->db->select('batch_number, SUM(material_terpakai) AS berat')->group_by('batch_number')->get_where($table_add, array('catatan_programmer'=>$CAT_PRO,'id_production_detail'=>$ID_PRO))->result_array();
		$ArrMerge = array_merge($SUM_DETAIL, $SUM_PLUS, $SUM_ADD);
		$temp = [];
		foreach($ArrMerge as $val => $value) {
			if(!array_key_exists($value['batch_number'], $temp)) {
				$temp[$value['batch_number']] = 0;
			}
			$temp[$value['batch_number']] += $value['berat'];
		}
		// echo "<pre>";
		// print_r($temp);
		// echo "</pre>";
		$data = array(
			'ArrMerge'	=> $temp,
			'status'	=> $status,
			'length'	=> $length,
			'length_awal'=> $length_awal,
			'qty'		=> $qty,
			'qty_product' => $qty_product
		);
		$this->load->view('Gudang_wg/detail_berat',$data);
	
	}

	public function spool(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Gudang WIP Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group
			// 'akses_menu'	=> $Arr_Akses
		);
		history('View data gudang wip spool');
		$this->load->view('Gudang_wg/spool',$data);
	}

	public function server_side_spool(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spool(
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

		$DATEFILTER = $requestData['date_filter'];
		$GET_NO_SPK = get_detail_final_drawing();
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

			$ArrNo_Spool    = [];
			$ArrNo_IPP      = [];
			$ArrNo_SPK      = [];
			$ArrNo_Drawing  = [];
            if(empty($DATEFILTER)){
                $get_split_ipp  = $this->db->select('id_produksi, id_milik, kode_spool, product_code, no_drawing')->get_where('spool_group_all',array('spool_induk'=>$row['spool_induk']))->result_array();
                $get_split_ipp2  = $this->db->select('kode_spool, product_code, id_category as product, COUNT(id) AS qty, sts, id_milik')->group_by('sts,kode_spool,id_milik')->get_where('spool_group_all',array('spool_induk'=>$row['spool_induk']))->result_array();
                foreach ($get_split_ipp as $key => $value) { $key++;
                    $ArrNo_Spool[]  = $value['kode_spool'];
                    $ArrNo_IPP[]    = str_replace('PRO-','',$value['id_produksi']);
                    $ArrNo_Drawing[]= $value['no_drawing'];
                }
                foreach ($get_split_ipp2 as $key => $value) { $key++;
					$no_spk         = (!empty($GET_NO_SPK[$value['id_milik']]['no_spk']))?$GET_NO_SPK[$value['id_milik']]['no_spk']:'not set';
                    $IMPLODE        = explode('-', $value['product_code']);
                    $ArrNo_SPK[]    = $key.'. '.$value['kode_spool'].'/'.$IMPLODE[0].'/'.strtoupper($value['product']).' <b class="text-blue">['.$value['qty'].' PCS]</b>/'.$no_spk.'/'.strtoupper($value['sts']);
                }
            }
            else{
                $get_split_ipp  = $this->db->select('no_ipp, id_milik, kode_spool, no_drawing')->get_where('stock_barang_wip_per_day',array('spool_induk'=>$row['spool_induk'],'category'=>'spool'))->result_array();
                $get_split_ipp2  = $this->db->select('kode_spool, product, COUNT(id) AS qty, sts, no_so, id_milik')->group_by('sts,kode_spool,id_milik')->get_where('stock_barang_wip_per_day',array('spool_induk'=>$row['spool_induk'],'category'=>'spool'))->result_array();
                foreach ($get_split_ipp as $key => $value) { $key++;
                    $ArrNo_Spool[]  = $value['kode_spool'];
                    $ArrNo_IPP[]    = $value['no_ipp'];
                    $ArrNo_Drawing[]= $value['no_drawing'];
                }
                foreach ($get_split_ipp2 as $key => $value) { $key++;
					$no_spk         = (!empty($GET_NO_SPK[$value['id_milik']]['no_spk']))?$GET_NO_SPK[$value['id_milik']]['no_spk']:'not set';
                    $ArrNo_SPK[]    = $key.'. '.$value['kode_spool'].'/'.$value['no_so'].'/'.strtoupper($value['product']).' <b class="text-blue">['.$value['qty'].' PCS]</b>/'.$no_spk.'/'.strtoupper($value['sts']);
                }
            }
			
			$explode_spo     = implode('<br>',array_unique($ArrNo_Spool));
			$explode_ipp    = implode('<br>',array_unique($ArrNo_IPP));
			$explode_spk    = implode('<br>',$ArrNo_SPK);
			$explode_drawing= implode('<br>',array_unique($ArrNo_Drawing));

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['spool_induk']."</div>";
			$nestedData[]	= "<div align='center'>".$explode_spo."</div>";
			$nestedData[]	= "<div align='left'>".$explode_drawing."</div>";
			$nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>".$explode_spk."</div>";
			// $nestedData[]	= "<div align='center'>".$row['spool_by']."</div>";
			// $nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['spool_date']))."</div>";
			
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

	public function query_data_spool($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		if($date_filter == ''){
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				spool_group_all a,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.release_spool_date IS NULL 
				AND a.lock_spool_date IS NOT NULL 
	 			".$where2."
				AND (
					a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
			GROUP BY
				a.spool_induk
		";
		}
		else{
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*
				FROM
					stock_barang_wip_per_day a,
					(SELECT @row:=0) r
				WHERE 1=1
					AND a.category = 'spool'
					AND DATE(a.hist_date) = '".$date_filter."'
					AND (
						a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				GROUP BY
					a.spool_induk
			";
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_spool',
			2 => 'kode_spk',
			3 => 'kode_spk',
			4 => 'kode_spk'
		);

		$sql .= " ORDER BY a.spool_induk DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function ExcelWIP(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$status		= $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'D9D9D9'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		
		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		$LEFT_JOIN = "";
		$FIELD_CUTTING = "";
		if($status == 'pipe'){
			$where = " AND c.cutting='N' AND c.id_category='pipe' ";
			$LEFT_JOIN = "";
			$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";

		}
        if($status == 'cutting'){
			$where = " AND d.id IS NOT NULL AND c.id_category='pipe' AND d.app = 'Y' ";
			$LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header";
			$FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,";
		}
        if($status == 'fitting'){
			$where = " AND c.cutting='N' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' ";
			$LEFT_JOIN = "";
			$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
		}

		$sql = "
			SELECT
				a.*,
				COUNT(a.id_milik) AS qtyTotal,
                b.spk1_cost,
                b.spk2_cost,
                b.no_ipp,
                c.diameter_1,
                c.thickness,
				".$FIELD_CUTTING."
				e.qty AS tot_qty
			FROM
				production_detail a
                LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
				LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
                LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
                LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke".$LEFT_JOIN."
		    WHERE 1=1 
                AND a.upload_real = 'Y'
                AND a.upload_real2 = 'Y'
                AND a.kode_spk IS NOT NULL 
				AND a.kode_delivery IS NULL
				AND a.kode_spool IS NULL
				AND a.release_to_costing_date IS NULL
				AND a.fg_date IS NULL
                ".$where."
                ".$where2."
				GROUP BY a.id_milik
		";

		// ECHO $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();


		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'DATA WIP '.strtoupper($status));
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'No SPK');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'Product');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'No SO');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Customer');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'Project');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spec');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Qty');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);


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
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$NOMOR_SO = explode('-',$row_Cek['product_code']);

				$customer = get_name('production','nm_customer','no_ipp', $row_Cek['no_ipp']);
				$project = get_name('production','project','no_ipp', $row_Cek['no_ipp']);

				$awal_col++;
				$no_spk	= $row_Cek['no_spk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_category	= strtoupper($row_Cek['id_category']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$NO_SO	= $NOMOR_SO[0];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NO_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$customer	= $customer;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$project	= $project;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$diameter_1	= spec_bq2($row_Cek['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter_1);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$qtyTotal	= $row_Cek['qtyTotal'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qtyTotal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
		}


		$sheet->setTitle('WIP');
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
		header('Content-Disposition: attachment;filename="Data Product WIP '.strtoupper($status).' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	

	

	public function ExcelWIPSpool($date_filter){
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

		if($date_filter == '0'){
		$sql = "SELECT
					a.*,
					COUNT(no_spk) AS qty_product
				FROM
					spool_group_all a
				WHERE 1=1 
					AND a.release_spool_date IS NULL 
					AND a.lock_spool_date IS  NOT NULL 
					AND a.id_produksi NOT IN ".filter_not_in()."
					GROUP BY a.sts, a.no_spk, a.spool_induk
					ORDER BY a.spool_induk DESC
			";

		}
		else{
				$sql = "
					SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						COUNT(no_spk) AS qty_product
					FROM
						stock_barang_wip_per_day a,
						(SELECT @row:=0) r
					WHERE 1=1
						AND a.category = 'spool'
						AND DATE(a.hist_date) = '".$date_filter."'
					GROUP BY a.sts, a.no_spk, a.spool_induk
				";
			}

		// echo $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		$DATE_JUDUL = ($date_filter == '0')?'':' ('.$date_filter.')';

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(11);
		$sheet->setCellValue('A'.$Row, 'DATA WIP SPOOL'.$DATE_JUDUL);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'Kode');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'Kode Spool');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'No Drawing');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'IPP');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'No SO');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Product');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Spec');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'No SPK');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Qty');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Category');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);


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
				$spool_induk	= $row_Cek['spool_induk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spool_induk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$kode_spool	= strtoupper($row_Cek['kode_spool']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_spool);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_drawing	= $row_Cek['no_drawing'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_drawing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				if($date_filter == '0'){
					$id_produksi	= str_replace('PRO-','',$row_Cek['id_produksi']);
				}else{
					$id_produksi	= $row_Cek['no_ipp'];
				}
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				if($date_filter == '0'){
					$IMPLODE = explode('.', $row_Cek['product_code']);
					$no_so	= $IMPLODE[0];
				}else{
					$no_so	= $row_Cek['no_so'];
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				if($date_filter == '0'){
					$id_category	= strtoupper($row_Cek['id_category']);
				}else{
					$id_category	= $row_Cek['product'];
				}
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				if($date_filter == '0'){
					$length_cutting = (!empty($row_Cek['length_cutting']))?$row_Cek['length_cutting']:'';
					$spec	= spec_bq2($row_Cek['id_milik'])." / length cutting: ".$length_cutting;
					if($row_Cek['sts'] == 'loose'){
						$spec	= spec_bq2($row_Cek['id_milik']);
					}
				}
				else{
					$spec	= $row_Cek['spec'];
				}
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk	= $row_Cek['no_spk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_product	= $row_Cek['qty_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$sts	= strtoupper($row_Cek['sts']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sts);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

			}
		}


		$sheet->setTitle('WIP Spool');
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
		header('Content-Disposition: attachment;filename="data-wip-spool.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	//GUDANG WG FIELD JOINT
	public function field_joint()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/field_joint';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Gudang Finish Good Field Joint',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data gudang fg field joint');
		$this->load->view('Gudang_wg/field_joint', $data);
	}

	public function server_side_field_joint()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/field_joint';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_field_joint(
			$requestData['status'],
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

		$FLAG = $requestData['status'];
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

			$check	= "<button type='button' class='btn btn-sm btn-default check_real' title='Detail Material' data-kode_trans='" . $row['kode_trans'] . "'><i class='fa fa-eye'></i></button>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['so_number'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_ipp'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['qty'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['qc_by'] . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y H:i',strtotime($row['qc_date'])) . "</div>";
			$nestedData[]	= "<div align='center'>
									" . $check . "
								</div>";

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

	public function query_data_field_joint($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		$where2 = "";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.so_number
			FROM
				outgoing_field_joint a
				LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp)=b.id_bq,
				(SELECT @row:=0) r
			WHERE 1=1 " . $where . " " . $where2 . "
				AND a.qc_date IS NOT NULL AND a.delivery_date IS NULL AND a.deleted_date IS NULL
				AND (
					a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.so_number',
			2 => 'no_ipp',
			3 => 'no_spk',
			4 => 'qty'
		);

		$sql .= " ORDER BY a.qc_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function detail_field_joint()
	{
		$kode_trans 	= $this->uri->segment(3);
		$header = $this->db->get_where('outgoing_field_joint', array('kode_trans' => $kode_trans))->result_array();
		$result = $this->db->get_where('outgoing_field_joint_detail', array('kode_trans' => $kode_trans))->result_array();
		$data = [
			'kode_trans' => $kode_trans,
			'header' => $header,
			'result' => $result,
		];
		$this->load->view('Gudang_wg/detail_field_joint', $data);
	}

	public function excel_field_joint($date_filter=null){
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $this->load->library("PHPExcel");

        $objPHPExcel    = new PHPExcel();
		
		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

        $Arr_Bulan  = array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $sheet      = $objPHPExcel->getActiveSheet();

        $dateX	= date('Y-m-d H:i:s');
        $Row        = 1;
        $NewRow     = $Row+1;
        $Col_Akhir  = $Cols = getColsChar(9);
        $sheet->setCellValue('A'.$Row, "DAFTAR FG FIELD JOINT");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'NO IPP');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'NO SO');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'NO SPK');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'Kode Trans');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Customer');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Project');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Material');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'Qty');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

		// if($date_filter == ''){
			$SQL = "SELECT
						a.kode_trans,
						a.no_ipp,
						b.so_number,
						a.no_spk,
						c.nm_customer,
						c.project,
						z.nm_material,
						z.qty
					FROM
						outgoing_field_joint a
						LEFT JOIN outgoing_field_joint_detail z ON a.kode_trans=z.kode_trans
						LEFT JOIN so_number b ON CONCAT( 'BQ-', a.no_ipp )= b.id_bq
						LEFT JOIN production c ON a.no_ipp = c.no_ipp
					WHERE
						1 = 1 
						AND a.qc_date IS NOT NULL 
						AND a.delivery_date IS NULL 
						AND a.deleted_date IS NULL 
					ORDER BY
						a.qc_date DESC";
		// }
		// else{
		// 	$SQL = "SELECT
		// 				a.*,
		// 				a.nm_project AS project,
		// 				a.product AS product_name,
		// 				COUNT(a.id) AS qty_stock
		// 			FROM
		// 				stock_barang_jadi_per_day a
		// 			WHERE 1=1
		// 				AND DATE(a.hist_date) = '".$date_filter."'
		// 				AND a.category = 'deadstok'
		// 			GROUP BY a.qty_order
		// 	";
		// }
		$dataResult   = $this->db->query($SQL)->result_array();

		if($dataResult){
			$awal_row   = $NextRow;
			 $no = 0;
			foreach($dataResult as $key=>$vals){
				$no++;
				$awal_row++;
				$awal_col   = 0;

				$awal_col++;
				$no   = $no;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_ipp   = $vals['no_ipp'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_ipp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$so_number   = $vals['so_number'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $so_number);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk   = $vals['no_spk'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$kode_trans   = $vals['kode_trans'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_customer   = $vals['nm_customer'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$project   = $vals['project'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material   = $vals['nm_material'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty   = $vals['qty'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}

		history('Download finish good field joint');
        $sheet->setTitle('FG Field Joint');
        //mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
        $objWriter      = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        //sesuaikan headernya
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //ubah nama file saat diunduh
        header('Content-Disposition: attachment;filename="fg-field-joint.xls"');
        //unduh file
        $objWriter->save("php://output");
    }

}