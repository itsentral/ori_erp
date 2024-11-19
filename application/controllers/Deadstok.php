<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deadstok extends CI_Controller {

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
			'title'			=> 'Indeks Of Deadstok',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Deadstok');
		$this->load->view('Deadstok/index',$data);
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'><b class='text-primary'>".$row['id_product']."</b></div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['type']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_barang']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$row['type_std']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_spec']."</div>";
			$nestedData[]	= "<div align='left'>".$row['resin']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['length'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty_stock'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty_booking'])."</div>";

			$delete	= "";
			$update	= "";
			if($Arr_Akses['delete']=='1'){
				$delete	= "<button type='button' class='btn btn-sm btn-danger delete' title='Delete Product' data-id='".$row['id_product']."'><i class='fa fa-trash'></i></button>";
			}
			if($Arr_Akses['update']=='1' AND $row['qty_booking'] > 0 ){
				$update	= "&nbsp;<a href='".base_url('deadstok/manage_booking/'.$row['id_product'].'')."' class='btn btn-sm btn-primary' title='Manage Booking'><i class='fa fa-hand-paper-o'></i></a>";
			}
			$nestedData[]	= "<div align='center'>".$delete.$update."</div>";
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
 
		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					COUNT(a.qty) AS qty_stock,
					(SELECT COUNT(z.id) FROM production_detail z WHERE a.id_product=z.id_product_deadstok AND id_deadstok_dipakai IS NULL) AS qty_booking
				FROM
					deadstok a,
					(SELECT @row:=0) r
				WHERE  a.deleted_date IS NULL AND a.kode_delivery IS NULL AND a.id_booking IS NULL AND(
					a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_barang LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.id_product
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_product',
			2 => 'type',
			3 => 'no_barang',
			4 => 'product_name',
			5 => 'type_std',
			6 => 'product_spec',
			7 => 'resin',
			8 => 'length',
			9 => 'qty',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function delete(){
		$id 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$ArrPlant		= array(
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
			$this->db->where('id_product', $id); 
			$this->db->update('deadstok', $ArrPlant); 
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete deadstok id_product : '.$id);
		}
		echo json_encode($Arr_Data);
	}

	public function download_excel(){
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
        $sheet->setCellValue('A'.$Row, "DAFTAR FINISH GOOD DEADSTOK");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'Category');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'No Barang');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'Product Name');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'Type');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Spec');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Resin');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Length');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'Qty');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

		$SQL = "SELECT
					a.*,
					COUNT(qty) AS qty_stock
				FROM
					deadstok a
				WHERE  
					a.deleted_date IS NULL AND a.kode_delivery IS NULL AND a.id_booking IS NULL
				GROUP BY 
					a.id_product";
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
				$type   = $vals['type'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $type);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_barang   = $vals['no_barang'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_barang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$product_name   = $vals['product_name'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$type_std   = $vals['type_std'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $type_std);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$product_spec   = $vals['product_spec'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product_spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$resin   = $vals['resin'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $resin);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$length   = $vals['length'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $length);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty_stock   = $vals['qty_stock'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

			}
		}

		history('Download deadstock');
        $sheet->setTitle('Deadstok');
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
        header('Content-Disposition: attachment;filename="gudang-deadstok.xls"');
        //unduh file
        $objWriter->save("php://output");
    }

	public function download_template(){
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
        $sheet->setCellValue('A'.$Row, "TEMPLATE UPLOAD DEADSTOK");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'Category');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'No Barang');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'Product Name');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'Type');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Spec');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Resin');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Length');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'Qty');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);


		history('Download template deadstock');
        $sheet->setTitle('Template Deadstok');
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
        header('Content-Disposition: attachment;filename="template-deadstok.xls"');
        //unduh file
        $objWriter->save("php://output");
    }

	public function modalUpload(){
		$this->load->view('Deadstok/modalUpload');
	}

	public function upload_deadstok(){
		set_time_limit(0);
		ini_set('memory_limit','2048M');

		if($_FILES['excel_file']['name']){
			$exts   = getExtension($_FILES['excel_file']['name']);
			if(!in_array($exts,array(1=>'xls','xlsx')))
			{
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Invalid file type, Please Upload the Excel format ...'
				);
			}
			else{
				$fileName = $_FILES['excel_file']['name'];
				$this->load->library(array('PHPExcel'));
				$config['upload_path'] = './assets/foto/';
				$config['file_name'] = $fileName;
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = 10000;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('excel_file')) {
					$error = array('error' => $this->upload->display_errors());
					$Arr_Kembali		= array(
						'status'		=> 3,
						'pesan'			=> $this->upload->display_errors()
					);
				}
				else{
					$media = $this->upload->data();
					$inputFileName = './assets/foto/'.$media['file_name'];

					$data_session	= $this->session->userdata;
					$Create_By      = $data_session['ORI_User']['username'];
					$Create_Date    = date('Y-m-d H:i:s');

					try{
						$inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
						$objReader      = PHPExcel_IOFactory::createReader($inputFileType);
						$objReader->setReadDataOnly(true);
						$objPHPExcel    = $objReader->load($inputFileName);

					}catch(Exception $e){
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
					}

					$sheet = $objPHPExcel->getSheet(0);
					$highestRow     = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();
					$Error      = 0;
					$Arr_Keys   = array();
					$Loop       = 0;
					$Total      = 0;
					$Message    = "";
					$Urut       = 0;
					$Arr_Summary= array();
					$Arr_Detail = array();

					$intL 		= 0;
					$intError 	= 0;
					$pesan 		= '';
					$status		= '';

					$getMaxProduct = $this->db->select('MAX(id_product) AS id_product')->get('deadstok')->result();
					$MaxProduct = $getMaxProduct[0]->id_product;

					for ($row = 5; $row <= $highestRow; $row++)
					{
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
						//echo "<pre>";print_r($rowData);exit;
						$Urut++;
						$MaxProduct++;

						
						$type			= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:'';
						$no_barang		= (isset($rowData[0][2]) && $rowData[0][2])?$rowData[0][2]:'';
						$product_name	= (isset($rowData[0][3]) && $rowData[0][3])?$rowData[0][3]:'';
						$type_std		= (isset($rowData[0][4]) && $rowData[0][4])?$rowData[0][4]:'';
						$product_spec	= (isset($rowData[0][5]) && $rowData[0][5])?$rowData[0][5]:'';
						$resin			= (isset($rowData[0][6]) && $rowData[0][6])?$rowData[0][6]:'';
						$length			= (isset($rowData[0][7]) && $rowData[0][7])?$rowData[0][7]:0;
						$qty			= (isset($rowData[0][8]) && $rowData[0][8])?$rowData[0][8]:0;
						$price_book		= (isset($rowData[0][9]) && $rowData[0][9])?$rowData[0][9]:0;

						for ($i=1; $i <= $qty; $i++) { 
							$KEY = $Urut.'-'.$i;
							$Arr_Detail[$KEY]['id_product']  	= $MaxProduct;
							$Arr_Detail[$KEY]['type']  			= strtolower($type);
							$Arr_Detail[$KEY]['no_barang']  	= $no_barang;
							$Arr_Detail[$KEY]['product_name']  	= $product_name;
							$Arr_Detail[$KEY]['type_std']  		= $type_std;
							$Arr_Detail[$KEY]['product_spec']  	= $product_spec;
							$Arr_Detail[$KEY]['resin']  		= $resin;
							$Arr_Detail[$KEY]['length']  		= str_replace(',','',$length);
							$Arr_Detail[$KEY]['qty']  			= str_replace(',','',$qty);
							$Arr_Detail[$KEY]['qty_ke']  		= $i;
							$Arr_Detail[$KEY]['upload_by']  	= $Create_By;
							$Arr_Detail[$KEY]['upload_date']  	= $Create_Date;
							$Arr_Detail[$KEY]['price_book']  	= $price_book;
						}
						

						

					} //akhir perulangan

					// echo '<pre>';
					// print_r($Arr_Detail);
					// exit;

					$this->db->trans_start();
						if(!empty($Arr_Detail)){
							$this->db->insert_batch('deadstok', $Arr_Detail); 
						}
					$this->db->trans_complete();

					if ($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$Arr_Kembali	= array(
							'pesan'		=>'Upload Deadstok Failed. Please try again later ...',
							'status'	=> 2
						);
					}
					else{
						$this->db->trans_commit();
						$Arr_Kembali	= array(
							'pesan'		=>'Upload Deadstok Success. Thanks ...',
							'status'	=> 1
						);
						history('Upload deadstok');
					}
				}
			}
		}
		//penutup data array
		echo json_encode($Arr_Kembali);
	}

	public function manage_booking($id_product){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$listBooking = $this->db->get_where('production_detail',array('id_product_deadstok'=>$id_product,'lock_deadstok'=>'0'))->result_array();

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Manage Booking Deadstok',
			'action'		=> 'index',
			'id_product'	=> $id_product,
			'row_group'		=> $data_Group,
			'listBooking'	=> $listBooking,
			'akses_menu'	=> $Arr_Akses,
			'GET_NO_SPK'	=> get_detail_final_drawing(),
			'GET_DET_IPP'	=> get_detail_ipp()
		);

		$this->load->view('Deadstok/manage_booking',$data);
	}

	public function check_product(){
		$data = $this->input->post();
		// print_r($data);
		// exit;
		$id_product = $data['id_product'];

		$getDetail = $this->db->select('COUNT(a.id) AS qty_product, a.*')->group_by('id_product')->get_where('deadstok a',array('id_product'=>$id_product,'id_booking'=>NULL,'deleted_date'=>NULL))->result_array();

		$ArrKembali = array(
			'status'			=> (!empty($getDetail[0]['product_name']))?1:0,
			'product_name'		=> (!empty($getDetail[0]['product_name']))?$getDetail[0]['product_name'].', '.$getDetail[0]['type_std'].'/'.$getDetail[0]['resin']:'',
			'product_spec'		=> (!empty($getDetail[0]['product_spec']))?$getDetail[0]['product_spec'].' x '.number_format($getDetail[0]['length']):'',
			'qty_product'		=> (!empty($getDetail[0]['qty_product']))?$getDetail[0]['qty_product']:'',
		);

		echo json_encode($ArrKembali);
	}

	public function booking_deadstok(){
		$data = $this->input->post();

		$data_session	= $this->session->userdata;
		$username = $data_session['ORI_User']['username'];
		$dateTime = date('Y-m-d H:i:s');
		
		$detail = (!empty($data['detail']))?$data['detail']:[];
		$GET_DET_IPP = get_detail_ipp();
		$GET_DET_FD = get_detail_final_drawing();

		$YM	= date('ym');
		$srcPlant		= "SELECT MAX(kode) as maxP FROM deadstok_modif WHERE kode LIKE 'DMF" . $YM . "%' ";
		$resultPlant	= $this->db->query($srcPlant)->result_array();
		$angkaUrut2		= $resultPlant[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 3);
		$urutan2++;
		$urut2			= sprintf('%03s', $urutan2);
		$kode_modif		= "DMF" . $YM . $urut2;
		// echo  $kode_modif; exit;
		$ArrUpdate = [];
		$ArrInsertCutting = [];
		$ArrInsertModif = [];
		$ArrInsertToFG = [];
		if(!empty($detail)){
			foreach ($detail as $key => $value) {
				$getDetail = $this->db->get_where('deadstok',array('id_product'=>$value['id_product'],'id_booking'=>NULL,'deleted_date'=>NULL))->result_array();
				$deadstokDipakai = (!empty($getDetail[0]['id']))?$getDetail[0]['id']:NULL;
				$deadstokDipakaiName = (!empty($getDetail[0]['product_name']))?$getDetail[0]['product_name']:NULL;
				$deadstokDipakaiValue = (!empty($getDetail[0]['price_book']))?$getDetail[0]['price_book']:0;

				if($deadstokDipakai != NULL){
					$getDetailPro 	= $this->db->select('id_milik, id_produksi, product_ke, id_category')->get_where('production_detail',array('id'=>$value['id']))->result_array();
					$nm_product 	= (!empty($getDetailPro[0]['id_category']))?$getDetailPro[0]['id_category']:NULL;
					$id_milik 		= (!empty($getDetailPro[0]['id_milik']))?$getDetailPro[0]['id_milik']:NULL;
					$no_ipp 		= (!empty($getDetailPro[0]['id_produksi']))?str_replace('PRO-','',$getDetailPro[0]['id_produksi']):NULL;
					$so_number		= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:NULL;
					$no_spk			= (!empty($GET_DET_FD[$id_milik]['no_spk']))?$GET_DET_FD[$id_milik]['no_spk']:NULL;

					$ArrUpdate[$key]['id'] = $value['id'];
					$ArrUpdate[$key]['id_product_deadstok'] = $value['id_product'];
					$ArrUpdate[$key]['id_deadstok_dipakai'] = $deadstokDipakai;
					$ArrUpdate[$key]['lock_deadstok'] = 1;
					$ArrUpdate[$key]['no_spk'] = $no_spk;
					$ArrUpdate[$key]['product_code'] = $so_number;

					$ArrUpdate[$key]['upload_real'] = 'Y';
					$ArrUpdate[$key]['upload_real2'] = 'Y';
					$ArrUpdate[$key]['kode_spk'] = 'deadstok';
					$ArrUpdate[$key]['fg_date'] = NULL;
					$ArrUpdate[$key]['closing_produksi_date'] = $dateTime;
					$ArrUpdate[$key]['sts_cutting'] = ($value['proses_next'] == '3')?'Y':'N';

					if($value['proses_next'] == '1'){
						$qty = 1;
						$ArrInsertToFG[$key]['tanggal'] = date('Y-m-d');
						$ArrInsertToFG[$key]['keterangan'] = 'Deadstok to Finish Good';
						$ArrInsertToFG[$key]['no_so'] 	= $so_number;
						$ArrInsertToFG[$key]['product'] = $deadstokDipakaiName;
						$ArrInsertToFG[$key]['no_spk'] = $no_spk;
						$ArrInsertToFG[$key]['kode_trans'] = null;
						$ArrInsertToFG[$key]['id_pro_det'] = $deadstokDipakai;
						$ArrInsertToFG[$key]['qty'] = $qty;
		
						$ArrInsertToFG[$key]['nilai_wip'] = $deadstokDipakaiValue * $qty;
						$ArrInsertToFG[$key]['material'] = null;
						$ArrInsertToFG[$key]['wip_direct'] =  null;
						$ArrInsertToFG[$key]['wip_indirect'] =  null;
						$ArrInsertToFG[$key]['wip_consumable'] =  null;
						$ArrInsertToFG[$key]['wip_foh'] =  null;
						$ArrInsertToFG[$key]['created_by'] = $username;
						$ArrInsertToFG[$key]['created_date'] = $dateTime;
						$ArrInsertToFG[$key]['id_trans'] = null;
		
						//tambahan finish good
						$ArrInsertToFG[$key]['id_pro'] = $value['id'];
						$ArrInsertToFG[$key]['qty_ke'] = $getDetailPro[0]['product_ke'];
						$ArrInsertToFG[$key]['nilai_unit'] = $deadstokDipakaiValue;
						$ArrInsertToFG[$key]['id_material'] = $id_milik;
						$ArrInsertToFG[$key]['nm_material'] = 'pengganti: '.$nm_product;
						$ArrInsertToFG[$key]['qty_mat'] = $qty;
						$ArrInsertToFG[$key]['cost_book'] = $deadstokDipakaiValue;
					}

					$this->db->where('id',$deadstokDipakai);
					$this->db->update('deadstok',array(
						'id_booking' => $value['id'],
						'process_next' => $value['proses_next'],
						'id_milik' => $id_milik,
						'no_so' => $so_number,
						'no_spk' => $no_spk,
						'no_ipp' => $no_ipp,
						'sts_cutting' => ($value['proses_next'] == '3')?'Y':'N'
					));

					//JIKA CUTTING
					$getDetailDead 	= $this->db->get_where('deadstok',array('id'=>$deadstokDipakai))->result_array();
					if($value['proses_next'] == '3'){
						$ArrInsertCutting[$key]['id_milik'] 	= $id_milik;
						$ArrInsertCutting[$key]['id_category'] 	= $getDetailDead[0]['product_name'].' '.$getDetailDead[0]['type_std'].' '.$getDetailDead[0]['resin'].', '.$getDetailDead[0]['product_spec'];
						$ArrInsertCutting[$key]['id_deadstok'] 	= $deadstokDipakai;
						$ArrInsertCutting[$key]['id_bq'] 		= 'BQ-'.$no_ipp;
						$ArrInsertCutting[$key]['qty'] 			= 1;
						$ArrInsertCutting[$key]['qty_ke'] 		= $getDetailPro[0]['product_ke'];
						$ArrInsertCutting[$key]['diameter_1'] 	= 0;
						$ArrInsertCutting[$key]['diameter_2'] 	= 0;
						$ArrInsertCutting[$key]['length'] 		= $getDetailDead[0]['length'];
						$ArrInsertCutting[$key]['created_by'] 	= $username;
						$ArrInsertCutting[$key]['created_date'] = $dateTime;
					}

					if($value['proses_next'] == '4'){
						$ArrInsertModif[$key]['kode'] 			= $kode_modif;
						$ArrInsertModif[$key]['id_deadstok'] 	= $deadstokDipakai;
						$ArrInsertModif[$key]['proses'] 		= $value['proses'];
						$ArrInsertModif[$key]['created_by'] 	= $username;
						$ArrInsertModif[$key]['created_date'] 	= $dateTime;
					}
				}

			}
		}

		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_detail', $ArrUpdate, 'id');
			}

			if(!empty($ArrInsertCutting)){
				$this->db->insert_batch('so_cutting_header', $ArrInsertCutting);
			}
			if(!empty($ArrInsertModif)){
				$this->db->insert_batch('deadstok_modif', $ArrInsertModif);
			}
			if(!empty($ArrInsertToFG)){
				$this->db->insert_batch('data_erp_fg',$ArrInsertToFG);
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'status'	=> 0,
				'pesan'		=> 'Failed !!!',
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'status'	=> 1,
				'pesan'		=> 'Success !!!',
			);
		}
		echo json_encode($Arr_Kembali);
	}

	public function spk_print(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');

		$QUERY = "	SELECT
						REPLACE(a.id_produksi,'PRO-','') AS no_ipp,
						b.so_number,
						a.id_milik,
						a.no_spk,
						a.id_category AS product,
						a.kode_booking_deadstok AS booking_code,
						COUNT( a.id ) AS qty_booking,
						a.booking_by,
						a.booking_date 
					FROM
						production_detail a 
						LEFT JOIN so_number b ON REPLACE(a.id_produksi,'PRO-','BQ-') = b.id_bq
					WHERE
						a.kode_booking_deadstok IS NOT NULL 
					GROUP BY
						a.kode_booking_deadstok
					ORDER BY a.booking_date DESC";
		$result = $this->db->query($QUERY)->result_array();

		$data = array(
			'title'			=> 'SPK Deadstok On Progress',
			'action'		=> 'index',
			'result'		=> $result,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Deadstok spk');
		$this->load->view('Deadstok/spk_print',$data);
	}

}
