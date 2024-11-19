<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

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
			'title'			=> 'Upload',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Upload/index',$data);
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
        $Col_Akhir  = $Cols = getColsChar(12);
        $sheet->setCellValue('A'.$Row, "TEMPLATE UPLOAD");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'Category (pipe/fitting/field joint/spool)');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'No IPP');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'No SO');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'No SPK');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Id Milik');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Product');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Spec');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'Id Customer');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

		$sheet ->getColumnDimension("J")->setAutoSize(true);
		$sheet->setCellValue('J'.$NewRow, 'Nm Customer');
        $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);

		$sheet ->getColumnDimension("K")->setAutoSize(true);
		$sheet->setCellValue('K'.$NewRow, 'Project');
        $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);

		$sheet ->getColumnDimension("L")->setAutoSize(true);
		$sheet->setCellValue('L'.$NewRow, 'Qty SO');
        $sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('L'.$NewRow.':L'.$NextRow);

		$sheet ->getColumnDimension("M")->setAutoSize(true);
		$sheet->setCellValue('M'.$NewRow, 'Qty');
        $sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('M'.$NewRow.':M'.$NextRow);

		$sheet ->getColumnDimension("N")->setAutoSize(true);
		$sheet->setCellValue('N'.$NewRow, 'Value');
        $sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('N'.$NewRow.':N'.$NextRow);

		$sheet ->getColumnDimension("O")->setAutoSize(true);
		$sheet->setCellValue('O'.$NewRow, 'Kode Spool');
        $sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('O'.$NewRow.':O'.$NextRow);

		$sheet ->getColumnDimension("P")->setAutoSize(true);
		$sheet->setCellValue('P'.$NewRow, 'Kode Spool Child');
        $sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('P'.$NewRow.':P'.$NextRow);

		$sheet ->getColumnDimension("Q")->setAutoSize(true);
		$sheet->setCellValue('Q'.$NewRow, 'No Drawing');
        $sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);

		$NewRow = $NewRow + 1;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'pipe');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'IPP210918E');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'SOE220016');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, '20P.22.0042');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, '5120');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'pipe');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, '40 x 3000 x 4.3');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'C100-1906003');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

		$sheet ->getColumnDimension("J")->setAutoSize(true);
		$sheet->setCellValue('J'.$NewRow, 'NOV FIBERGLASS SYSTEM');
        $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);

		$sheet ->getColumnDimension("K")->setAutoSize(true);
		$sheet->setCellValue('K'.$NewRow, 'CHEMICAL PIPELINE TO GRVE-PTTGC');
        $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);

		$sheet ->getColumnDimension("L")->setAutoSize(true);
		$sheet->setCellValue('L'.$NewRow, '50');
        $sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('L'.$NewRow.':L'.$NextRow);

		$sheet ->getColumnDimension("M")->setAutoSize(true);
		$sheet->setCellValue('M'.$NewRow, '5');
        $sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('M'.$NewRow.':M'.$NextRow);

		$sheet ->getColumnDimension("N")->setAutoSize(true);
		$sheet->setCellValue('N'.$NewRow, '100000');
        $sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('N'.$NewRow.':N'.$NextRow);

		$sheet ->getColumnDimension("O")->setAutoSize(true);
		$sheet->setCellValue('O'.$NewRow, 'SP-240001');
        $sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('O'.$NewRow.':O'.$NextRow);

		$sheet ->getColumnDimension("P")->setAutoSize(true);
		$sheet->setCellValue('P'.$NewRow, 'SOE220016-SP-01');
        $sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('P'.$NewRow.':P'.$NextRow);

		$sheet ->getColumnDimension("Q")->setAutoSize(true);
		$sheet->setCellValue('Q'.$NewRow, 'SP-JSON-001');
        $sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableBodyLeft);
        $sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);

        $sheet->setTitle('Template Upload');
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
        header('Content-Disposition: attachment;filename="template-upload-hist.xls"');
        //unduh file
        $objWriter->save("php://output");
    }

	public function modalUpload(){
		$this->load->view('Deadstok/modalUpload');
	}

	public function upload(){
		set_time_limit(0);
		ini_set('memory_limit','2048M');

		$data 			= $this->input->post();
		$tanggal 		= $data['tanggal'].' '.date('H:i:s');
		$tanggal_where 	= $data['tanggal'];
		$table 			= $data['tipe'];
		$product_where 	= $data['product'];

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

					for ($row = 5; $row <= $highestRow; $row++)
					{
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
						//echo "<pre>";print_r($rowData);exit;
						$Urut++;

						$category		= (isset($rowData[0][1]) && $rowData[0][1])?strtolower($rowData[0][1]):NULL;
						$no_ipp			= (isset($rowData[0][2]) && $rowData[0][2])?$rowData[0][2]:NULL;
						$no_so			= (isset($rowData[0][3]) && $rowData[0][3])?$rowData[0][3]:NULL;
						$no_spk			= (isset($rowData[0][4]) && $rowData[0][4])?$rowData[0][4]:NULL;
						$id_milik		= (isset($rowData[0][5]) && $rowData[0][5])?$rowData[0][5]:NULL;
						$product		= (isset($rowData[0][6]) && $rowData[0][6])?$rowData[0][6]:NULL;
						$spec			= (isset($rowData[0][7]) && $rowData[0][7])?$rowData[0][7]:NULL;
						$id_customer	= (isset($rowData[0][8]) && $rowData[0][8])?$rowData[0][8]:NULL;
						$nm_customer	= (isset($rowData[0][9]) && $rowData[0][9])?$rowData[0][9]:NULL;
						$nm_project		= (isset($rowData[0][10]) && $rowData[0][10])?$rowData[0][10]:NULL;
						$qty_order		= (isset($rowData[0][11]) && $rowData[0][11])?str_replace(',','',$rowData[0][11]):0;
						$qty_group_spk	= (isset($rowData[0][12]) && $rowData[0][12])?str_replace(',','',$rowData[0][12]):0;
						$nilai_value	= (isset($rowData[0][13]) && $rowData[0][13])?str_replace(',','',$rowData[0][13]):0;
						$spool_induk	= (isset($rowData[0][14]) && $rowData[0][14])?$rowData[0][14]:NULL;
						$kode_spool		= (isset($rowData[0][15]) && $rowData[0][15])?$rowData[0][15]:NULL;
						$no_drawing		= (isset($rowData[0][16]) && $rowData[0][16])?$rowData[0][16]:NULL;

						if($product_where == $category){
							for ($i=1; $i <= $qty_group_spk; $i++) { 
								$KEY = $Urut.'-'.$i;
								$Arr_Detail[$KEY]['category']  		= $category;
								$Arr_Detail[$KEY]['no_ipp']  		= $no_ipp;
								$Arr_Detail[$KEY]['no_so']  		= $no_so;
								$Arr_Detail[$KEY]['no_spk']  		= $no_spk;
								$Arr_Detail[$KEY]['id_milik']  		= $id_milik;
								$Arr_Detail[$KEY]['product']  		= $product;
								$Arr_Detail[$KEY]['spec']  			= $spec;
								$Arr_Detail[$KEY]['id_customer']  	= $id_customer;
								$Arr_Detail[$KEY]['nm_customer']  	= $nm_customer;
								$Arr_Detail[$KEY]['nm_project']  	= $nm_project;
								$Arr_Detail[$KEY]['qty_order']  	= $qty_order;
								$Arr_Detail[$KEY]['qty_group_spk']  = $qty_group_spk;
								$Arr_Detail[$KEY]['nilai_value']  	= $nilai_value;
								$Arr_Detail[$KEY]['hist_date']  	= $tanggal;
								$Arr_Detail[$KEY]['spool_induk']  	= $spool_induk;
								$Arr_Detail[$KEY]['kode_spool']  	= $kode_spool;
								$Arr_Detail[$KEY]['no_drawing']  	= $no_drawing;
							}
						}
					} //akhir perulangan

					// echo '<pre>';
					// print_r($Arr_Detail);
					// exit;

					$this->db->trans_start();
						if(!empty($Arr_Detail)){
							$this->db->where('category',$product_where); 
							$this->db->like('hist_date', $tanggal_where, 'after');
							$this->db->delete($table); 

							$this->db->insert_batch($table, $Arr_Detail); 
						}
					$this->db->trans_complete();

					if ($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$Arr_Kembali	= array(
							'pesan'		=>'Upload History Failed. Please try later ...',
							'status'	=> 2
						);
					}
					else{
						$this->db->trans_commit();
						$Arr_Kembali	= array(
							'pesan'		=>'Upload History Success. Thanks ...',
							'status'	=> 1
						);
					}
				}
			}
		}
		//penutup data array
		echo json_encode($Arr_Kembali);
	}

}
