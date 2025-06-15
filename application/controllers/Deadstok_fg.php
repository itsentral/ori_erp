<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deadstok_fg extends CI_Controller {

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
			'title'			=> 'Finish Good Deadstok',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data FG Deadstok');
		$this->load->view('Deadstok/index_fg',$data);
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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

			if($requestData['date_filter'] == ''){
				$length     = ($row['length'] > 0)?' x '.$row['length']:'';
				$type_std   = (!empty($row['type_std']))?$row['type_std'].', ':'';
				$resin      = (!empty($row['resin']))?$row['resin'].', ':'';
				$spec = $type_std.$resin.$row['product_spec'].$length;
			}
			else{
				$spec = $row['spec'];
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_so'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_spk'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";  
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty_stock'])."</div>";
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

	public function queryDataJSON($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		if($date_filter == ''){
			$sql = "SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						COUNT(a.qty) AS qty_stock,
						b.nm_customer AS nm_customer,
						b.project AS project
					FROM
						deadstok a
						INNER JOIN production b ON a.no_ipp=b.no_ipp,
						(SELECT @row:=0) r
					WHERE  
						a.deleted_date IS NULL 
						AND a.kode_delivery IS NULL 
						AND a.id_booking IS NOT NULL 
						AND a.process_next = 1
						AND qc_date IS NOT NULL
						AND(
							a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
						)
					GROUP BY a.id_product
			";
		}
		else{
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					a.nm_project AS project,
					a.product AS product_name,
					COUNT(a.id) AS qty_stock
				FROM
					stock_barang_jadi_per_day a,
					(SELECT @row:=0) r
				WHERE 1=1
					AND DATE(a.hist_date) = '".$date_filter."'
					AND a.category = 'deadstok'
					AND (
						a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				GROUP BY a.qty_order
			";
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_so',
			2 => 'no_spk',
			3 => 'b.nm_customer',
			4 => 'b.project',
			5 => 'product_name',
			6 => 'product_spec'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function download_excel($date_filter=null){
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
        $Col_Akhir  = $Cols = getColsChar(8);
        $sheet->setCellValue('A'.$Row, "DAFTAR FG DEADSTOK");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'NO SO');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'NO SPK');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'Customer');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'Project');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Product');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Spec');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Qty');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($whiteCenterBold);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		if($date_filter == ''){
			$SQL = "SELECT
						a.*,
						COUNT(qty) AS qty_stock
					FROM
						deadstok a
					WHERE  
						a.deleted_date IS NULL
						AND id_booking is not null 
						and process_next = 1 
						and qc_date is not null 
						and kode_delivery is null
					GROUP BY 
						a.id_product";
		}
		else{
			$SQL = "SELECT
						a.*,
						a.nm_project AS project,
						a.product AS product_name,
						COUNT(a.id) AS qty_stock
					FROM
						stock_barang_jadi_per_day a
					WHERE 1=1
						AND DATE(a.hist_date) = '".$date_filter."'
						AND a.category = 'deadstok'
					GROUP BY a.qty_order
			";
		}
		$dataResult   = $this->db->query($SQL)->result_array();

		if($dataResult){
			$awal_row   = $NextRow;
			 $no = 0;
			foreach($dataResult as $key=>$vals){
				$no++;
				$awal_row++;
				$awal_col   = 0;

				if($date_filter == ''){
					$length     = ($vals['length'] > 0)?' x '.$vals['length']:'';
					$type_std   = (!empty($vals['type_std']))?$vals['type_std'].', ':'';
					$resin      = (!empty($vals['resin']))?$vals['resin'].', ':'';
					$spec = $type_std.$resin.$vals['product_spec'].$length;
				}
				else{
					$spec = $vals['spec'];
				}

				$awal_col++;
				$no   = $no;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_so   = $vals['no_so'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk   = $vals['no_spk'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
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
				$product_name   = $vals['product_name'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_stock   = $vals['qty_stock'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

			}
		}

		history('Download finish good deadstock');
        $sheet->setTitle('FG Deadstok');
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
        header('Content-Disposition: attachment;filename="fg-deadstok.xls"');
        //unduh file
        $objWriter->save("php://output");
    }

	public function getDataJSON_modif(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON_modif(
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

			if($requestData['date_filter'] == ''){
				$length     = ($row['length'] > 0)?' x '.$row['length']:'';
				$type_std   = (!empty($row['type_std']))?$row['type_std'].', ':'';
				$resin      = (!empty($row['resin']))?$row['resin'].', ':'';
				$spec = $type_std.$resin.$row['product_spec'].$length;
				$spec = $row['product_spec'];
			}
			else{
				$spec = $row['spec'];
			}

			$nm_customer 	= (!empty($row['nm_customer']))?$row['nm_customer']:$row['customer_tanki'];
			$project 		= (!empty($row['project']))?$row['project']:$row['project_tanki'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_so'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_spk'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($project)."</div>";  
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty_stock'])."</div>";

			if ($row['cutting'] == 'N' AND $requestData['date_filter'] == '' AND $row['type'] == 'pipe') {
				$nestedData[]	= "<div align='center'><input type='checkbox' name='check[" . $row['id_modif'] . "]' class='chk_personal' value='" . $row['id_modif'] . "'></div>";
			} else {
				$nestedData[]	= "<div align='center'></div>";
			}
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

	public function queryDataJSON_modif($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		if($date_filter == ''){
			$sql = "SELECT
						(@row:=@row+1) AS nomor,
						a.*,
						COUNT(a.qty) AS qty_stock,
						b.nm_customer AS nm_customer,
						b.project AS project,
						z.cutting,
						z.id AS id_modif,
						d.customer AS customer_tanki,
						d.project AS project_tanki
					FROM
						deadstok_modif z
						LEFT JOIN deadstok a ON z.id_deadstok = a.id AND a.deleted_date IS NULL
						LEFT JOIN production b ON a.no_ipp=b.no_ipp
						LEFT JOIN planning_tanki d ON a.no_ipp=d.no_ipp,
						(SELECT @row:=0) r
					WHERE  
						a.deleted_date IS NULL 
						AND a.kode_delivery IS NULL
						AND z.qc_date IS NOT NULL
						AND(
							a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
							OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
						)
					GROUP BY z.id
					
			";
		}
		else{
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					a.nm_project AS project,
					a.product AS product_name,
					COUNT(a.id) AS qty_stock
				FROM
					stock_barang_jadi_per_day a,
					(SELECT @row:=0) r
				WHERE 1=1
					AND DATE(a.hist_date) = '".$date_filter."'
					AND a.category = 'deadstok'
					AND (
						a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				GROUP BY a.id
			";
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.no_so',
			2 => 'a.no_spk',
			3 => 'b.nm_customer',
			4 => 'b.project',
			5 => 'a.product_name',
			6 => 'a.product_spec'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_cutting()
	{
		$data 		= $this->input->post();

		$check 			= $data['check'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$get_detail_produksi = $this->db
			->select('*')
			->from('deadstok_modif')
			->where_in('id', $check)
			->where('cutting', 'N')
			->get()
			->result_array();

		$ArrUpdate = [];
		$ArrCutting = [];
		$ArrHistFG = [];
		foreach ($get_detail_produksi as $value => $valx) {
			$ArrUpdate[$value]['id'] 			= $valx['id'];;
			$ArrUpdate[$value]['cutting'] 	= 'Y';

			$ArrCutting[$value]['id_milik'] 	= $valx['id'];

			$get_det = $this->db->get_where('deadstok', array('id' => $valx['id_deadstok']))->result();

			$ArrCutting[$value]['id_bq'] 		= 'BQ-'.$get_det[0]->no_ipp;
			$ArrCutting[$value]['id_category'] 	= $get_det[0]->product_name.', '.$get_det[0]->product_spec;
			$ArrCutting[$value]['qty'] 			= 1;
			$ArrCutting[$value]['qty_ke'] 		= $get_det[0]->qty_ke;


			$ArrCutting[$value]['id_deadstok'] 	= $valx['id_deadstok'];
			$ArrCutting[$value]['diameter_1'] 	= NULL;
			$ArrCutting[$value]['diameter_2'] 	= NULL;
			$ArrCutting[$value]['length'] 		= $get_det[0]->length;
			$ArrCutting[$value]['thickness'] 	= 999;
			$ArrCutting[$value]['created_by'] 	= $username;
			$ArrCutting[$value]['created_date'] = $datetime;

			$ArrHistFG[$value]['tipe_product'] = 'pipe deadstok modif';
			$ArrHistFG[$value]['id_product'] = $valx['id'];
			$ArrHistFG[$value]['id_milik'] = $valx['id_deadstok'];
			$ArrHistFG[$value]['tipe'] = 'out';
			$ArrHistFG[$value]['kode'] = $valx['kode'];
			$ArrHistFG[$value]['tanggal'] = date('Y-m-d');
			$ArrHistFG[$value]['keterangan'] = 'pipe deadstok modif to cutting';
			$ArrHistFG[$value]['hist_by'] = $username;
			$ArrHistFG[$value]['hist_date'] = $datetime;
		}
		// print_r($ArrUpdate);
		// print_r($ArrCutting);
		// print_r($ArrHistFG);
		// exit;

		$this->db->trans_start();
		if (!empty($ArrUpdate)) {
			$this->db->update_batch('deadstok_modif', $ArrUpdate, 'id');
		}
		if (!empty($ArrCutting)) {
			$this->db->insert_batch('so_cutting_header', $ArrCutting);
		}
		if (!empty($ArrHistFG)) {
			$this->db->insert_batch('history_product_fg', $ArrHistFG);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1
			);
			history('Create cutting via finish good delivery :' . json_encode($check));
		}

		echo json_encode($Arr_Kembali);
	}

}
