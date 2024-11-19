<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_stock extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
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
		$data_category		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();
        $data_gudang          = $this->db->order_by('id','desc')->get_where('warehouse',array('category'=>'project'))->result_array();
		$data = array(
			'title'			=> 'Warehouse Project >> Stock',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_category'	=> $data_category,
			'data_gudang'	=> $data_gudang,
		);
		history('View project stock');
		$this->load->view('Project/stock/index',$data);
	}

    public function server_side_stock(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_stock(
			$requestData['id_category'],
			$requestData['id_gudang'],
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
		$booking		= $fetch['booking'];
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

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['code_group'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_item'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_excel'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('con_nonmat_category_awal', 'category', 'id', $row['category_awal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nama_master'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['brand'])."</div>";
			// $nestedData[]	= "<div align='left'>".strtoupper(get_name('warehouse', 'nm_gudang', 'id', $row['gudang']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['stock'])."</div>";
			$nestedData[]	= "<div align='right'><span class='detailBooking text-bold text-primary' style='cursor:pointer;' data-id_material='".$row['code_group']."' data-nm_material='".$row['nama_master']."' data-id_gudang='".$row['gudang']."'>".number_format($row['booking'],4)."</span></div>";
				
			// $nestedData[]	= "<div align='right'>".number_format($row['booking'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['rusak'])."</div>";
			
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
			"recordsRusak"		=> $rusak,
			"recordsBooking"	=> $booking,
		);

		echo json_encode($json_data);
	}

	public function query_data_json_stock($id_category, $id_gudang, $date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$table = "warehouse_rutin_stock";
		$where_category ='';
		$where_gudang=" AND a.gudang='".$id_gudang."' ";
		$where_date ='';

		if($id_category != '0'){
			$where_category = " AND b.category_awal = '".$id_category."' ";
		}

		if(!empty($date_filter)){
			if($id_category != '0'){
				$where_category = " AND b.category_awal = '".$id_category."' ";
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
				b.brand,
				b.code_group,
				b.kode_item,
				b.kode_excel,
				b.category_awal
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_category." ".$where_gudang." ".$where_date." AND b.deleted = 'N' AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_item LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_excel LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.brand LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		$Query_Sum = "
			SELECT
				SUM(a.stock) AS stock,
				SUM(a.booking) AS booking,
				SUM(a.rusak) AS rusak
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group
		    WHERE 1=1 ".$where_category." ".$where_gudang." ".$where_date." AND b.deleted = 'N' AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_item LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_excel LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.brand LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$stock = $rusak = 0;
		$Hasil_SUM		   = $this->db->query($Query_Sum)->result_array();
		if($Hasil_SUM){
			$stock		= $Hasil_SUM[0]['stock'];
			$rusak	= $Hasil_SUM[0]['rusak'];
			$booking	= $Hasil_SUM[0]['booking'];
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$data['stock'] 	= $stock;
		$data['rusak'] = $rusak;
		$data['booking'] = $booking;
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'code_group',
			2 => 'kode_item',
			3 => 'kode_excel',
			4 => 'b.category_awal',
			5 => 'material_name',
			6 => 'spec',
			7 => 'brand',
			8 => 'stock',
			9 => 'rusak'
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
		$id_category	= $this->uri->segment(3);
		$id_gudang		= $this->uri->segment(4);
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

		$table = "warehouse_rutin_stock";
		$where_category ='';
		$where_gudang=" AND a.gudang='".$id_gudang."' ";
		$where_date ='';

		if($id_category != '0'){
			$where_category = " AND b.category_awal = '".$id_category."' ";
		}

		if(!empty($date_filter)){
			if($id_category != '0'){
				$where_category = " AND b.category_awal = '".$id_category."' ";
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
				b.category_awal AS category
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_category." ".$where_gudang." ".$where_date." AND b.deleted = 'N'
		";
		$restDetail1	= $this->db->query($sql)->result_array();
		
		$tanggal_update = (!empty($date_filter))?" (".date('d F Y', strtotime($date_filter)).")":" (".date('d F Y').")";

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'Stock Gudang Project - '.$tanggal_update);
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

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);
		
		$sheet->setCellValue('G'.$NewRow, 'SPEC');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'STOCK');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'STOCK NG');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);


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

				$CATEGORY = get_name('con_nonmat_category_awal','category','id',$row_Cek['category']);
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $CATEGORY);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$material_name	= $row_Cek['nama_master'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$spec	= $row_Cek['spec'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$stock	= $row_Cek['stock'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$rusak	= $row_Cek['rusak'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $rusak);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

			}
		}


		$sheet->setTitle('Project');
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
		header('Content-Disposition: attachment;filename="stok-warehouse-project.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function modal_history_booking(){
		$id_material 	= $this->uri->segment(3);
		$id_gudang 		= $this->uri->segment(4);

		$gudang_after = getSubGudangProject();

		$SQL = "SELECT
					* 
				FROM
					warehouse_rutin_history
				WHERE
					code_group= '$id_material' 
					AND id_gudang= '$id_gudang' 
					AND (gudang_ke= 'BOOKING' OR id_gudang_ke = '".$gudang_after."' )
					AND (qty_booking_awal != qty_booking_akhir OR (qty_booking_awal IS NULL AND qty_booking_akhir IS NOT NULL))
					AND update_date > '2024-07-01 00:00:00' AND ket NOT LIKE '%IPP%'";
		$result		= $this->db->query($SQL)->result_array();

		$SQL2 		= $SQL." AND no_trans LIKE '%IPP%' GROUP BY no_trans";
		$result2	= $this->db->query($SQL2)->result_array();

		$material	= $this->db->get_where('con_nonmat_new', array('code_group'=>$id_material))->result_array();

		$data = array(
			'id_material' => $id_material,
			'id_gudang' => $id_gudang,
			'result' => $result,
			'listSO' => $result2,
			'GET_SO' => get_detail_ipp(),
			'material' => $material
		);

		$this->load->view('Project/stock/modal_history_booking', $data);
	}

	public function show_history_booking(){
		$data 			= $this->input->post();
		$no_ipp 		= $data['no_ipp'];
		$id_material 		= $data['id_material'];
		$id_gudang 		= $data['id_gudang'];

		$result		= $this->db
							->select('a.*')
							->from('warehouse_rutin_history a')
							->or_group_start()
								->where("a.no_trans = '".$no_ipp."' OR a.ket LIKE '%".$no_ipp."%' ")
							->group_end()
							->where('a.code_group',$id_material)
							->where('a.id_gudang',$id_gudang)
							->where('a.update_date > ','2024-07-01 00:00:00')
							->or_group_start()
								->where('a.gudang_ke','BOOKING')
								->where('a.gudang_dari','BOOKING')
							->group_end()
							->get()
							->result_array();

		$data_html = "";
		$data_html .= "<tr>";
			$data_html .= "<th>#</th>";
			$data_html .= "<th>Gudang Dari</th>";
			$data_html .= "<th>Gudang Ke</th>";
			$data_html .= "<th class='text-right'>Qty Booking</th>";
			$data_html .= "<th class='text-right'>Booking Awal</th>";
			$data_html .= "<th class='text-right'>Booking Akhir</th>";
			$data_html .= "<th>Keterangan</th>";
			$data_html .= "<th class='text-center'>Tanggal</th>";
		$data_html .= "</tr>";
		$No=0;
		$QTY_PLUS = 0;
		$QTY_AKHIR = 0;
		foreach ($result as $key => $value) { $key--;
			$No++;
			$bold = '';
			$bold2 = '';
			$color = 'text-blue';
			
			$gudang_dari 	= get_name('warehouse','nm_gudang','id',$value['id_gudang_dari']);
			$dari_gudang 	= (!empty($gudang_dari))?$gudang_dari:$value['gudang_dari'];
			$ke_gudang 		= $value['gudang_ke'];

			$QTY 			= $value['jumlah_qty'];
			// $QTY_SEBELUM 	= (!empty($result[$key]['jumlah_mat']))?$result[$key]['jumlah_mat']:0;
			// $QTY_AWAL 		= (!empty($result[$key]['jumlah_mat']))?$result[$key]['jumlah_mat'] + $QTY:0;
			$QTY_AWAL 		= $QTY_PLUS;
			$QTY_AKHIR = 0;
			
			if($ke_gudang == 'BOOKING'){
				$bold2 = 'text-bold';

				$QTY_AKHIR 	= $QTY_AWAL + $QTY;
			}

			if($dari_gudang != 'BOOKING'){
				$bold = 'text-bold';
				$color = 'text-red';

				$QTY_AKHIR 	= $QTY_AWAL - $QTY;
			}

			if($No == 1){
				$QTY_AKHIR 	= $QTY;
			}
			if($No == 1){
				$QTY_AWAL 	= 0;
			}

			$data_html .= "<tr>";
				$data_html .= "<td>".$No."</td>";
				$data_html .= "<td class='text-left ".$bold."'>".$dari_gudang."</td>";
				$data_html .= "<td class='text-left ".$bold2."'>".$ke_gudang."</td>";
				$data_html .= "<td class='text-right ".$color."'>".number_format($QTY,4)."</td>";
				$data_html .= "<td class='text-right ".$color."'>".number_format($QTY_AWAL,4)."</td>";
				$data_html .= "<td class='text-right ".$color."'>".number_format($QTY_AKHIR,4)."</td>";
				$data_html .= "<td>".strtoupper($value['ket'])."</td>";
				$data_html .= "<td class='text-center'>".date('d-M-Y H:i:s', strtotime($value['update_date']))."</td>";
			$data_html .= "</tr>";

			$QTY_PLUS = $QTY_AKHIR;
		}
		$data_html .= "<tr>";
			$data_html .= "<td></td>";
			$data_html .= "<td colspan='4' class='text-bold'>SISA BOOKING</td>";
			$data_html .= "<td class='text-right text-bold'>".number_format($QTY_AKHIR,4)."</td>";
			$data_html .= "<td colspan='2'></td>";
		$data_html .= "</tr>";
		

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

}