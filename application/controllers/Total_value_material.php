<?php
class Total_value_material extends CI_Controller {

	public function __construct() {
		parent::__construct();
        $this->load->model('master_model');

		$this->gudang_produksi = getGudangProduksi();
	}

    public function material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)).'/'.strtolower($this->uri->segment(3)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' AND category='".strtolower($this->uri->segment(3))."' ORDER BY urut ASC ")->result_array();
		if($this->uri->segment(3) == 'origa'){
			$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' AND id='23' ")->result_array();
			$judul = "Warehouse Material >> Gudang Origa >>Total Value Stock";
		}
		elseif($this->uri->segment(3) == 'pusat'){
			$judul = "Warehouse Material >> Gudang Pusat >> Total Value Stock";
		}
		elseif($this->uri->segment(3) == 'subgudang'){
			$judul = "Warehouse Material >> Sub Gudang >> Total Value Stock";
		}
		else{
			$judul = "Warehouse Material >> Gudang Produksi >> Total Value Stock";
		}
		$data = array(
			'title'			=> $judul,
			'action'		=> 'index',
			'category'		=> $this->uri->segment(3),
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		$this->load->view('Total_value/material',$data);
	}

    public function server_side_material_stock(){
		$requestData	= $_REQUEST;
		
		// print_r($requestData['gudang']);
		 //exit;

		$fetch			= $this->query_data_json_material_stock(
			$requestData['gudang'], 
			$requestData['date_filter'],
			$requestData['category'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$qty_stock		= $fetch['qty_stock'];
		$qty_booking	= $fetch['qty_booking'];
		$qty_rusak		= $fetch['qty_rusak'];

		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$requestData['gudang']))->result();

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		$dateFilter = (!empty($requestData['date_filter']))?$requestData['date_filter']:date('Y-m-d');
		$GET_PRICEBOOK = getPriceBookByDate2($dateFilter);
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

			$PRICEBOOK = (!empty($GET_PRICEBOOK[$row['id_material']]))?$GET_PRICEBOOK[$row['id_material']]:0;
			//$PRICEBOOK = ($row['costbook']==0)?((!empty($GET_PRICEBOOK[$row['id_material']]))?$GET_PRICEBOOK[$row['id_material']]:0):$row['costbook'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['idmaterial'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_category'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_gudang'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],4)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK*$row['qty_stock'],2)."</div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data,
			"category"          => $get_category[0]->category,
			"recordsStock"		=> $qty_stock,
			"recordsBooking"	=> $qty_booking,
			"recordsRusak"		=> $qty_rusak
		);

		echo json_encode($json_data);
	}
	
	public function server_side_material_stock_subgudang(){
		$requestData	= $_REQUEST;
		
		// print_r($requestData['gudang']);
		// exit;

		$fetch			= $this->query_data_json_material_stock_subgudang(
			$requestData['gudang'], 
			$requestData['date_filter'],
			$requestData['category'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$qty_stock		= $fetch['qty_stock'];
		$qty_booking	= $fetch['qty_booking'];
		$qty_rusak		= $fetch['qty_rusak'];

		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$requestData['gudang']))->result();

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		$dateFilter = (!empty($requestData['date_filter']))?$requestData['date_filter']:date('Y-m-d');
		$GET_PRICEBOOK = getPriceBookByDatesubgudang2($dateFilter);
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

		$PRICEBOOK = (!empty($GET_PRICEBOOK[$row['id_material']]))?$GET_PRICEBOOK[$row['id_material']]:0;
			//$PRICEBOOK = ($row['costbook']==0)?((!empty($GET_PRICEBOOK[$row['id_material']]))?$GET_PRICEBOOK[$row['id_material']]:0):$row['costbook'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['idmaterial'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_category'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_gudang'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],4)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK*$row['qty_stock'],2)."</div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data,
			"category"          => $get_category[0]->category,
			"recordsStock"		=> $qty_stock,
			"recordsBooking"	=> $qty_booking,
			"recordsRusak"		=> $qty_rusak
		);

		echo json_encode($json_data);
	}
	
	public function server_side_material_stock_produksi(){
		$requestData	= $_REQUEST;
		
		// print_r($requestData['gudang']);
		// exit;

		$fetch			= $this->query_data_json_material_stock(
			$requestData['gudang'], 
			$requestData['date_filter'],
			$requestData['category'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$qty_stock		= $fetch['qty_stock'];
		$qty_booking	= $fetch['qty_booking'];
		$qty_rusak		= $fetch['qty_rusak'];

		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$requestData['gudang']))->result();

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		$dateFilter = (!empty($requestData['date_filter']))?$requestData['date_filter']:date('Y-m-d');
		$GET_PRICEBOOK = getPriceBookByDateproduksi2($dateFilter);
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

			$PRICEBOOK = (!empty($GET_PRICEBOOK[$row['id_material']]))?$GET_PRICEBOOK[$row['id_material']]:0;
			//$PRICEBOOK = ($row['costbook']==0)?((!empty($GET_PRICEBOOK[$row['id_material']]))?$GET_PRICEBOOK[$row['id_material']]:0):$row['costbook'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['idmaterial'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_category'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_gudang'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],4)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($PRICEBOOK*$row['qty_stock'],2)."</div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data,
			"category"          => $requestData['category'],
			"recordsStock"		=> $qty_stock,
			"recordsBooking"	=> $qty_booking,
			"recordsRusak"		=> $qty_rusak
		);

		echo json_encode($json_data);
	}

	public function query_data_json_material_stock($gudang, $date_filter, $category, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$table = "warehouse_stock";
		$where_gudang ='';
		$where_date ='';
		$field_add = "0 AS costbook, 0 AS total_value,";
		$group_by = '';
		$fieldStock = 'a.qty_stock, a.qty_booking,a.qty_rusak, a.id_gudang,b.nm_gudang,';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_stock_per_day";
			$field_add = "a.costbook, a.total_value,";
		}

		if($gudang == '0'){
			$where_gudang = " AND a.id_gudang IN (".$this->gudang_produksi.") ";
			$group_by = ' GROUP BY c.id_material ';
			$fieldStock = 'SUM(a.qty_stock) AS qty_stock, SUM(a.qty_booking) AS qty_booking, SUM(a.qty_rusak) AS qty_rusak, "0" AS id_gudang, "Gudang Produksi" AS nm_gudang,';
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				c.idmaterial,
				c.id_material,
				c.nm_material,
				".$fieldStock."
				".$field_add."
				c.nm_category
			FROM
				".$table." a
				LEFT JOIN warehouse b ON a.id_gudang=b.id
				LEFT JOIN raw_materials c ON a.id_material = c.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date." AND (
				c.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_gudang LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		".$group_by;

		$Query_Sum	= "SELECT
					SUM(a.qty_stock) AS qty_stock,
					SUM(a.qty_booking) AS qty_booking,
					SUM(a.qty_rusak) AS qty_rusak
				FROM
					".$table." a
					LEFT JOIN warehouse b ON a.kd_gudang=b.kd_gudang
					LEFT JOIN raw_materials c ON a.id_material = c.id_material,
					(SELECT @row:=0) r
				WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date." AND (
					c.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_gudang LIKE '%".$this->db->escape_like_str($like_value)."%'
				)".$group_by;
		$qty_stock = $qty_booking = $qty_rusak	= 0;
		$Hasil_SUM		   = $this->db->query($Query_Sum)->result_array();
		if($Hasil_SUM){
			$qty_stock		= $Hasil_SUM[0]['qty_stock'];
			$qty_booking	= $Hasil_SUM[0]['qty_booking'];
			$qty_rusak		= $Hasil_SUM[0]['qty_rusak'];
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['qty_stock'] 	= $qty_stock;
		$data['qty_booking'] = $qty_booking;
		$data['qty_rusak'] 	= $qty_rusak;
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'idmaterial',
			2 => 'nm_material',
			3 => 'nm_category',
			4 => 'nm_gudang',
			5 => 'nm_material',
			6 => 'qty_stock'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	public function query_data_json_material_stock_subgudang($gudang, $date_filter, $category, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$table = "warehouse_stock";
		$where_gudang ='';
		$where_date ='';
		$field_add = "0 AS costbook, 0 AS total_value,";
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_stock_per_day";
			$field_add = "a.costbook, a.total_value,";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				c.idmaterial,
				c.id_material,
				c.nm_material,
				c.nm_category,
				a.qty_stock,
				a.qty_booking,
				a.qty_rusak,
				a.id_gudang,
				".$field_add."
				b.nm_gudang
			FROM
				".$table." a
				LEFT JOIN warehouse b ON a.id_gudang=b.id
				LEFT JOIN raw_materials c ON a.id_material = c.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date." AND (
				c.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_gudang LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		$Query_Sum	= "SELECT
					SUM(a.qty_stock) AS qty_stock,
					SUM(a.qty_booking) AS qty_booking,
					SUM(a.qty_rusak) AS qty_rusak
				FROM
					".$table." a
					LEFT JOIN warehouse b ON a.kd_gudang=b.kd_gudang
					LEFT JOIN raw_materials c ON a.id_material = c.id_material,
					(SELECT @row:=0) r
				WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date." AND (
					c.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_gudang LIKE '%".$this->db->escape_like_str($like_value)."%'
				)";
		$qty_stock = $qty_booking = $qty_rusak	= 0;
		$Hasil_SUM		   = $this->db->query($Query_Sum)->result_array();
		if($Hasil_SUM){
			$qty_stock		= $Hasil_SUM[0]['qty_stock'];
			$qty_booking	= $Hasil_SUM[0]['qty_booking'];
			$qty_rusak		= $Hasil_SUM[0]['qty_rusak'];
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['qty_stock'] 	= $qty_stock;
		$data['qty_booking'] = $qty_booking;
		$data['qty_rusak'] 	= $qty_rusak;
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'idmaterial',
			2 => 'nm_material',
			3 => 'nm_category',
			4 => 'nm_gudang',
			5 => 'nm_material',
			6 => 'qty_stock'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function ExcelGudang(){
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

		$table = "warehouse_stock";
		$where_gudang ='';
		$where_date ='';
		$field_add = "0 AS costbook, 0 AS total_value,";
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_stock_per_day";
			$field_add = "a.costbook, a.total_value,";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				c.idmaterial,
				c.id_material,
				c.nm_material,
				c.nm_category,
				a.qty_stock,
				a.qty_booking,
				a.qty_rusak,
				a.id_gudang,
				".$field_add."
				b.nm_gudang
			FROM
				".$table." a 
				LEFT JOIN warehouse b ON a.kd_gudang=b.kd_gudang
				LEFT JOIN raw_materials c ON a.id_material = c.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date."
		";
		$restDetail1	= $this->db->query($sql)->result_array();
		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$gudang))->result();
		$nm_gudang = strtoupper(get_name('warehouse','nm_gudang','id',$gudang));
		
		$tanggal_update = (!empty($date_filter))?" (".date('d F Y', strtotime($date_filter)).")":" (".date('d F Y').")";
		$tanggal_update2 = $date_filter;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'TOTAL VALUE STOCK - '.$nm_gudang.$tanggal_update);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'ID PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'WAREHOUSE');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'STOCK');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'PRICE BOOK');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'TOTAL VALUE');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);


		// echo $qDetail1; exit;
		$GET_PRICEBOOK = getPriceBookByDate2($tanggal_update2);

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
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$idmaterial	= $row_Cek['idmaterial'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= strtoupper($row_Cek['nm_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_gudang	= $row_Cek['nm_gudang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_gudang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_stock	= $row_Cek['qty_stock'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				//$PRICEBOOK = (!empty($GET_PRICEBOOK[$row_Cek['id_material']]))?$GET_PRICEBOOK[$row_Cek['id_material']]:0;
				$PRICEBOOK = ($row_Cek['costbook']==0)?((!empty($GET_PRICEBOOK[$row_Cek['id_material']]))?$GET_PRICEBOOK[$row_Cek['id_material']]:0):$row_Cek['costbook'];
                $TOTAL_VALUE = $qty_stock * $PRICEBOOK;

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

		$LABEL_TITLE = strtolower('total-value-material-'.$tanggal_update2);
		$sheet->setTitle('Total Value');
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$LABEL_TITLE.'.xls"');
		$objWriter->save("php://output");
	}
	
	
	public function ExcelGudangSubgudang(){
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

		$table = "warehouse_stock";
		$where_gudang ='';
		$where_date ='';
		$field_add = "0 AS costbook, 0 AS total_value,";
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_stock_per_day";
			$field_add = "a.costbook, a.total_value,";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				c.idmaterial,
				c.id_material,
				c.nm_material,
				c.nm_category,
				a.qty_stock,
				a.qty_booking,
				a.qty_rusak,
				a.id_gudang,
				".$field_add."
				b.nm_gudang
			FROM
				".$table." a 
				LEFT JOIN warehouse b ON a.kd_gudang=b.kd_gudang
				LEFT JOIN raw_materials c ON a.id_material = c.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date."
		";
		$restDetail1	= $this->db->query($sql)->result_array();
		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$gudang))->result();
		$nm_gudang = strtoupper(get_name('warehouse','nm_gudang','id',$gudang));
		
		$tanggal_update = (!empty($date_filter))?" (".date('d F Y', strtotime($date_filter)).")":" (".date('d F Y').")";
		$tanggal_update2 = (!empty($date_filter))?date('Y-m-d', strtotime($date_filter)):date('Y-m-d');

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'TOTAL VALUE STOCK - '.$nm_gudang.$tanggal_update);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'ID PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'WAREHOUSE');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'STOCK');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'PRICE BOOK');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'TOTAL VALUE');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);


		
		$GET_PRICEBOOK1 = getPriceBookByDatesubgudang2($tanggal_update2);
		
		//print_r ($restDetail1); exit;

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
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$idmaterial	= $row_Cek['idmaterial'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= strtoupper($row_Cek['nm_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_gudang	= $row_Cek['nm_gudang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_gudang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_stock	= $row_Cek['qty_stock'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				//$PRICEBOOK = (!empty($GET_PRICEBOOK[$row_Cek['id_material']]))?$GET_PRICEBOOK[$row_Cek['id_material']]:0;
				$PRICEBOOK = ($GET_PRICEBOOK1[$row_Cek['id_material']]==0)?((!empty($GET_PRICEBOOK1[$row_Cek['id_material']]))?$GET_PRICEBOOK1[$row_Cek['id_material']]:0):$row_Cek['costbook'];
                $TOTAL_VALUE = $qty_stock * $PRICEBOOK;

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

		$LABEL_TITLE = strtolower('total-value-material-subgudang-'.$tanggal_update2);
		$sheet->setTitle('Total Value');
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$LABEL_TITLE.'.xls"');
		$objWriter->save("php://output");
	}
	
	public function ExcelGudangProduksi(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$gudang			= $this->uri->segment(3);
		$category	= $this->uri->segment(4);
		$date_filter	= $this->uri->segment(5);

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

		$table = "warehouse_stock";
		$where_gudang ='';
		$where_date ='';
		$field_add = "0 AS costbook, 0 AS total_value,";
		$group_by = '';
		$fieldStock = 'a.qty_stock, a.qty_booking,a.qty_rusak, a.id_gudang,b.nm_gudang,';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_stock_per_day";
			$field_add = "a.costbook, a.total_value,";
		}

		if($gudang == '0'){
			$where_gudang = " AND a.id_gudang IN (".$this->gudang_produksi.") ";
			$group_by = ' GROUP BY c.id_material ';
			$fieldStock = 'SUM(a.qty_stock) AS qty_stock, SUM(a.qty_booking) AS qty_booking, SUM(a.qty_rusak) AS qty_rusak, "0" AS id_gudang, "Gudang Produksi" AS nm_gudang,';
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				c.idmaterial,
				c.id_material,
				c.nm_material,
				".$fieldStock."
				".$field_add."
				c.nm_category
			FROM
				".$table." a 
				LEFT JOIN warehouse b ON a.kd_gudang=b.kd_gudang
				LEFT JOIN raw_materials c ON a.id_material = c.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date."
		".$group_by;
		$restDetail1	= $this->db->query($sql)->result_array();
		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$gudang))->result();
		$nm_gudang = strtoupper(get_name('warehouse','nm_gudang','id',$gudang));
		
		$tanggal_update = (!empty($date_filter))?" (".date('d F Y', strtotime($date_filter)).")":" (".date('d F Y').")";
		$tanggal_update2 = (!empty($date_filter))?date('Y-m-d', strtotime($date_filter)):date('Y-m-d');

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'TOTAL VALUE STOCK - '.$nm_gudang.$tanggal_update);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'ID PROGRAM');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ID MATERIAL');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'WAREHOUSE');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'STOCK');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'PRICE BOOK');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'TOTAL VALUE');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);


		// echo $qDetail1; exit;
		$GET_PRICEBOOK = getPriceBookByDateproduksi2($tanggal_update2);

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
				$id_material	= $row_Cek['id_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$idmaterial	= $row_Cek['idmaterial'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_material	= strtoupper($row_Cek['nm_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_category	= $row_Cek['nm_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$nm_gudang	= $row_Cek['nm_gudang'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_gudang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_stock	= $row_Cek['qty_stock'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				//$PRICEBOOK = (!empty($GET_PRICEBOOK[$row_Cek['id_material']]))?$GET_PRICEBOOK[$row_Cek['id_material']]:0;
				$PRICEBOOK = ($row_Cek['costbook']==0)?((!empty($GET_PRICEBOOK[$row_Cek['id_material']]))?$GET_PRICEBOOK[$row_Cek['id_material']]:0):$row_Cek['costbook'];
                $TOTAL_VALUE = $qty_stock * $PRICEBOOK;

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

		$LABEL_TITLE = strtolower('total-value-material-produksi-'.$tanggal_update2);
		$sheet->setTitle('Total Value');
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$LABEL_TITLE.'.xls"');
		$objWriter->save("php://output");
	}

}
