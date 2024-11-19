<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends CI_Controller {
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

		// $get_Data			= $this->master_model->getData('supplier');
		$get_Data			= $this->db->query("SELECT a.*, b.country_name FROM supplier a INNER JOIN country b ON a.id_negara=b.country_code WHERE a.deleted = '0' ORDER BY a.nm_supplier ASC")->result();
		$menu_akses			= $this->master_model->getMenu();

		$data = array(
			'title'			=> 'Indeks Of Supplier',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Supplier');
		$this->load->view('Supplier/index',$data);
	}
	public function add(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;

			$nm_supplier	= trim(strtoupper($data['nm_supplier']));
			$id_negara		= trim($data['id_negara']);
			$id_prov		= trim($data['id_prov']);
			$id_kab			= trim($data['id_kab']);
			$mata_uang		= trim($data['mata_uang']);
			$alamat			= trim($data['alamat']);
			$telpon			= trim($data['telpon']);
			$telpon2		= trim($data['telpon2']);
			$telpon3		= trim($data['telpon3']);
			$fax			= trim($data['fax']);
			$email			= trim($data['email']);
			$email2			= trim($data['email2']);
			$email3			= trim($data['email3']);
			$cp				= trim($data['cp']);
			$hp_cp			= trim($data['hp_cp']);
			$id_webchat		= trim($data['id_webchat']);
			$npwp			= trim($data['npwp']);
			$alamat_npwp	= trim($data['alamat_npwp']);
			$keterangan		= trim($data['keterangan']);
			$data_bank		= trim($data['data_bank']);			
			$Ym				= date('ym');

			//pengurutan kode
			$srcMtr			= "SELECT MAX(id_supplier) as maxP FROM supplier WHERE id_supplier LIKE '".$id_negara."-".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 8, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$id_supplier	= $id_negara."-".$Ym.$urut2;

			// echo $id_supplier; exit;

			//check name supplier
			$qNmSUp	= "SELECT * FROM supplier WHERE nm_supplier = '".$nm_supplier."' ";
			$numSup	= $this->db->query($qNmSUp)->num_rows();

			//check email supplier
//agus 
//			$qSupEm	= "SELECT * FROM supplier WHERE email = '".$email."' ";
//			$numEm	= $this->db->query($qSupEm)->num_rows();
			$numEm=0;

			// echo $numType; exit;
			$Arr_Insert	= array(
				'id_supplier' 	=> $id_supplier,
				'nm_supplier' 	=> $nm_supplier,
				'id_negara' 	=> $id_negara,
				'id_prov' 		=> $id_prov,
				'id_kab' 		=> $id_kab,
				'mata_uang' 	=> $mata_uang,
				'alamat' 		=> $alamat,
				'telpon' 		=> $telpon,
				'telpon2' 		=> $telpon2,
				'telpon3' 		=> $telpon3,
				'fax' 			=> $fax,
				'email' 		=> $email,
				'email2' 		=> $email2,
				'email3' 		=> $email3,
				'cp' 			=> strtoupper($cp),
				'hp_cp' 		=> $hp_cp,
				'id_webchat' 	=> $id_webchat,
				'npwp' 			=> $npwp,
				'alamat_npwp' 	=> $alamat_npwp,
				'keterangan' 	=> $keterangan,
				'sts_aktif' 	=> 'aktif',
				'data_bank' 	=> $data_bank,
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_on' 	=> date('Y-m-d H:i:s')
			);

			// echo "<pre>"; print_r($Arr_Insert);
			// exit;
			if($numSup > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Supplier name already exists. Please check back ...'
				);
			}
			elseif($numEm > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Email has been used, possibly the supplier already exists. Please check back ...'
				);
			}
			else{
				$this->db->trans_start();
				$this->db->insert('supplier', $Arr_Insert);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Save Supplier data failed. Please try again later ...',
						'status'		=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Save Supplier data success. Thanks ...',
						'status'		=> 1
					);
					history('Add Supplier with username : '.$id_supplier);
				}
			}
			echo json_encode($Arr_Kembali);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$arr_Where			= array('flag_active'=>'1');
			$get_Data			= $this->master_model->getMenu($arr_Where);
			$id_negara			= $this->db->query("SELECT*FROM country ORDER BY country_name ASC")->result_array();
			$id_prov			= $this->db->query("SELECT*FROM provinsi ORDER BY nama ASC")->result_array();
			$id_kab				= $this->db->query("SELECT*FROM kabupaten ORDER BY nama ASC")->result_array();
			$mata_uang			= $this->db->query("SELECT a.kode, a.mata_uang, a.negara FROM currency a ORDER BY a.mata_uang ASC")->result_array();
			$data = array(
				'title'			=> 'Add Supplier',
				'action'		=> 'add',
				'id_negara'		=> $id_negara,
				'id_prov'		=> $id_prov,
				'id_kab'		=> $id_kab,
				'mata_uang'		=> $mata_uang
			);
			$this->load->view('Supplier/add',$data);
		}
	}

	public function getProvince(){
		$id_negara 	= $this->input->post('id_negara');
		$sqlProv	= "SELECT * FROM provinsi WHERE country_code='".$id_negara."' ORDER BY nama ASC";
		$restProv	= $this->db->query($sqlProv)->result_array();
		$NumProv	= $this->db->query($sqlProv)->num_rows();

		$option	= "<option value='0'>Select An Province</option>";
		foreach($restProv AS $val => $valx){
			$option .= "<option value='".$valx['id_prov']."'>".$valx['nama']."</option>";
		}
		if($NumProv == 0){
			$option .= "<option value=''>Data is empty, skip this input</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getDistrict(){
		$id_Dist 	= $this->input->post('id_prov');
		$sqlDist	= "SELECT * FROM kabupaten WHERE id_prov='".$id_Dist."' ORDER BY nama ASC";
		$restDist	= $this->db->query($sqlDist)->result_array();
		$NumDist	= $this->db->query($sqlDist)->num_rows();

		$option	= "<option value='0'>Select An Province</option>";
		foreach($restDist AS $val => $valx){
			$option .= "<option value='".$valx['id_kab']."'>".$valx['nama']."</option>";
		}
		if($NumDist == 0){
			$option .= "<option value=''>Data is empty, skip this input</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function edit(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;

			$id_supplier	= trim($data['id_supplier']);
			$nm_supplier	= trim(strtoupper($data['nm_supplier']));
			$id_negara		= trim($data['id_negara2']);
			$id_prov		= trim($data['id_prov']);
			$id_kab			= (!empty($data['id_kab']))?$data['id_kab']:NULL;
			$mata_uang		= trim($data['mata_uang']);
			$alamat			= trim($data['alamat']);
			$telpon			= trim($data['telpon']);
			$telpon2		= trim($data['telpon2']);
			$telpon3		= trim($data['telpon3']);
			$fax			= trim($data['fax']);
			$email			= trim($data['email']);
			$email2			= trim($data['email2']);
			$email3			= trim($data['email3']);
			$cp				= trim($data['cp']);
			$hp_cp			= trim($data['hp_cp']);
			$id_webchat		= trim($data['id_webchat']);
			$npwp			= trim($data['npwp']);
			$alamat_npwp	= trim($data['alamat_npwp']);
			$keterangan		= trim($data['keterangan']);
			$sts_aktif		= trim($data['sts_aktif']);
			$data_bank		= trim($data['data_bank']);

			// echo $numType; exit;
			$Arr_Update	= array(
				'nm_supplier' 	=> $nm_supplier,
				'id_negara' 	=> $id_negara,
				'id_prov' 		=> $id_prov,
				'id_kab' 		=> $id_kab,
				'mata_uang' 	=> $mata_uang,
				'alamat' 		=> $alamat,
				'telpon' 		=> $telpon,
				'telpon2' 		=> $telpon2,
				'telpon3' 		=> $telpon3,
				'fax' 			=> $fax,
				'email' 		=> $email,
				'email2' 		=> $email2,
				'email3' 		=> $email3,
				'cp' 			=> $cp,
				'hp_cp' 		=> $hp_cp,
				'id_webchat' 	=> $id_webchat,
				'npwp' 			=> $npwp,
				'alamat_npwp' 	=> $alamat_npwp,
				'keterangan' 	=> $keterangan,
				'sts_aktif' 	=> $sts_aktif,
				'data_bank' 	=> $data_bank,
				'modified_by' 	=> $data_session['ORI_User']['username'],
				'modified_on' 	=> date('Y-m-d H:i:s')
			);
			// echo "<pre>"; print_r($Arr_Update);
			// exit;
			// if($numSup > 1){
				// $Arr_Data		= array(
					// 'status'	=> 3,
					// 'pesan'		=> 'Supplier name already exists. Please check back ...'
				// );
			// }
			// elseif($numEm > 0){
				// $Arr_Data	= array(
					// 'status'	=> 3,
					// 'pesan'		=> 'Email has been used, possibly the supplier already exists. Please check back ...'
				// );
			// }
			// else{
				$this->db->trans_start();
				$this->db->where('id_supplier', $id_supplier);
				$this->db->update('supplier', $Arr_Update);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Data	= array(
						'pesan'		=>'Update data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Data	= array(
						'pesan'		=>'Update data success. Thanks ...',
						'status'	=> 1
					);
					history('Update Type Material '.$id_supplier.' with username : '.$data_session['ORI_User']['username']);
				}
			// }
			// print_r($Arr_Data); exit;
			echo json_encode($Arr_Data);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}

			$id = $this->uri->segment(3);

			// $detail			= $this->master_model->getData('raw_categories','id_category',$id);
			$detail				= $this->db->query("SELECT * FROM supplier WHERE id_supplier = '".$id."' ")->result_array();
			$id_negara			= $this->db->query("SELECT*FROM country ORDER BY country_name ASC")->result_array();
			$id_prov			= $this->db->query("SELECT*FROM provinsi WHERE country_code='".$detail[0]['id_negara']."' ORDER BY nama ASC")->result_array();
			$id_kab				= $this->db->query("SELECT*FROM kabupaten WHERE id_prov='".$detail[0]['id_prov']."' ORDER BY nama ASC")->result_array();
			$mata_uang			= $this->db->query("SELECT a.kode, a.mata_uang, a.negara FROM currency a ORDER BY a.mata_uang ASC")->result_array();
			$data = array(
				'title'			=> 'Edit Supplier',
				'action'		=> 'edit',
				'row'			=> $detail,
				'id_negara'		=> $id_negara,
				'id_prov'		=> $id_prov,
				'id_kab'		=> $id_kab,
				'mata_uang'		=> $mata_uang
			);

			$this->load->view('Supplier/edit',$data);
		}
	}

	function hapus(){
		$id_supplier 	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$Arr_Edit	= array(
			'deleted' => '1',
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
		$this->db->where('id_supplier', $id_supplier);
		$this->db->update('supplier', $Arr_Edit);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete supplier data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete supplier data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Supplier with ID : '.$id_supplier);
		}
		echo json_encode($Arr_Data);
	}

	public function modalDetail(){
		$this->load->view('Supplier/modalDetail');
	}

	public function modalUpload(){
		$this->load->view('Supplier/modalUpload');
	}

	 public function temp_format(){
        //membuat objek PHPExcel
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $kode_Budget    ='';
        if($this->input->post()){
            $kode_Budget    = $this->input->post('kode_budget');
        }
        $this->load->library("PHPExcel");
        //$this->load->library("PHPExcel/Writer/Excel2007");
        $objPHPExcel    = new PHPExcel();

        $style_header = array(
            'borders' => array(
                'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('rgb'=>'1006A3')
                  )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb'=>'E1E0F7'),
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
                'color' => array('rgb'=>'E1E0F7'),
            ),
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $styleArray = array(
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
              )
          );
        $styleArray3 = array(
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
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
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
              )
          );

        $Arr_Bulan  = array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $sheet      = $objPHPExcel->getActiveSheet();

        $dateX	= date('Y-m-d H:i:s');
        $Row        = 1;
        $NewRow     = $Row+1;
        $Col_Akhir  = $Cols = getColsChar(18);
        $sheet->setCellValue('A'.$Row, "DAFTAR DATA SUPPLIER (Waktu Download : ".date('d F Y H:i:s', strtotime($dateX)).")");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow +1;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, 'No');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'Supplier Name');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'Country Code');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'Currency of Purchase');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'Address');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Telephone');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Telephone 2');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Telephone 3');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'Fax');
        $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

		$sheet ->getColumnDimension("J")->setAutoSize(true);
		$sheet->setCellValue('J'.$NewRow, 'Email');
        $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);

		$sheet ->getColumnDimension("K")->setAutoSize(true);
		$sheet->setCellValue('K'.$NewRow, 'Email 2');
        $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);

		$sheet ->getColumnDimension("L")->setAutoSize(true);
		$sheet->setCellValue('L'.$NewRow, 'Email 3');
        $sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('L'.$NewRow.':L'.$NextRow);

		$sheet ->getColumnDimension("M")->setAutoSize(true);
		$sheet->setCellValue('M'.$NewRow, 'Contact Person');
        $sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('M'.$NewRow.':M'.$NextRow);

		$sheet ->getColumnDimension("N")->setAutoSize(true);
		$sheet->setCellValue('N'.$NewRow, 'Contact Number');
        $sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('N'.$NewRow.':N'.$NextRow);

		$sheet ->getColumnDimension("O")->setAutoSize(true);
		$sheet->setCellValue('O'.$NewRow, 'ID WebChat');
        $sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('O'.$NewRow.':O'.$NextRow);

		$sheet ->getColumnDimension("P")->setAutoSize(true);
		$sheet->setCellValue('P'.$NewRow, 'NPWP');
        $sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('P'.$NewRow.':P'.$NextRow);

		$sheet ->getColumnDimension("Q")->setAutoSize(true);
		$sheet->setCellValue('Q'.$NewRow, 'Address NPWP');
        $sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);

		$sheet ->getColumnDimension("R")->setAutoSize(true);
		$sheet->setCellValue('R'.$NewRow, 'Information');
        $sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
        $sheet->mergeCells('R'.$NewRow.':R'.$NextRow);

		$qSupplier   	= "SELECT * FROM supplier";
		$restSupplier   = $this->db->query($qSupplier);

		$Num_Cek    = $restSupplier->num_rows();
		if($Num_Cek > 0){
			$data_Det   = $restSupplier->result_array();
		}

		if($data_Det){
			$awal_row   = $NextRow;
			 $no = 0;
			foreach($data_Det as $key=>$vals){
				$no++;
				$awal_row++;
				$awal_col   = 0;

				$awal_col++;
				$no   = $no;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$nm_supplier   = strtoupper((isset($row_Cek[0]['nm_supplier']) && $row_Cek[0]['nm_supplier'])?$row_Cek[0]['nm_supplier']:$vals['nm_supplier']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_supplier);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$id_negara   = strtoupper((isset($row_Cek[0]['id_negara']) && $row_Cek[0]['id_negara'])?$row_Cek[0]['id_negara']:$vals['id_negara']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_negara);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$mata_uang   = (isset($row_Cek[0]['mata_uang']) && $row_Cek[0]['mata_uang'])?$row_Cek[0]['mata_uang']:$vals['mata_uang'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mata_uang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$alamat   = strtoupper((isset($row_Cek[0]['alamat']) && $row_Cek[0]['alamat'])?$row_Cek[0]['alamat']:$vals['alamat']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $alamat);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$telpon   = (isset($row_Cek[0]['telpon']) && $row_Cek[0]['telpon'])?$row_Cek[0]['telpon']:$vals['telpon'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $telpon);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$telpon2   = (isset($row_Cek[0]['telpon2']) && $row_Cek[0]['telpon2'])?$row_Cek[0]['telpon2']:$vals['telpon2'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $telpon2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$telpon3   = (isset($row_Cek[0]['telpon3']) && $row_Cek[0]['telpon3'])?$row_Cek[0]['telpon3']:$vals['telpon3'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $telpon3);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$fax   = (isset($row_Cek[0]['fax']) && $row_Cek[0]['fax'])?$row_Cek[0]['fax']:$vals['fax'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $fax);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$email   = strtolower((isset($row_Cek[0]['email']) && $row_Cek[0]['email'])?$row_Cek[0]['email']:$vals['email']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $email);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$email2   = strtolower((isset($row_Cek[0]['email2']) && $row_Cek[0]['email2'])?$row_Cek[0]['email2']:$vals['email2']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $email2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$email3   = strtolower((isset($row_Cek[0]['email3']) && $row_Cek[0]['email3'])?$row_Cek[0]['email3']:$vals['email3']);
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $email3);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$cp   = ucwords(strtolower((isset($row_Cek[0]['cp']) && $row_Cek[0]['cp'])?$row_Cek[0]['cp']:$vals['cp']));
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$hp_cp   = (isset($row_Cek[0]['hp_cp']) && $row_Cek[0]['hp_cp'])?$row_Cek[0]['hp_cp']:$vals['hp_cp'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $hp_cp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$id_webchat   = (isset($row_Cek[0]['id_webchat']) && $row_Cek[0]['id_webchat'])?$row_Cek[0]['id_webchat']:$vals['id_webchat'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_webchat);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$npwp   = (isset($row_Cek[0]['npwp']) && $row_Cek[0]['npwp'])?$row_Cek[0]['npwp']:$vals['npwp'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $npwp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$alamat_npwp   = (isset($row_Cek[0]['alamat_npwp']) && $row_Cek[0]['alamat_npwp'])?$row_Cek[0]['alamat_npwp']:$vals['alamat_npwp'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $alamat_npwp);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);

				$awal_col++;
				$keterangan   = (isset($row_Cek[0]['keterangan']) && $row_Cek[0]['keterangan'])?$row_Cek[0]['keterangan']:$vals['keterangan'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $keterangan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
			}
		}

		history('Download Template Excell Supplier');
        $sheet->setTitle('Supplier');
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
        header('Content-Disposition: attachment;filename="Supplier_templete_'.date('YmdHis').'.xls"');
        //unduh file
        $objWriter->save("php://output");
    }

	public function importData(){
        if($this->input->post()){
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
					$config['upload_path'] = './assets/file/';
					$config['file_name'] = $fileName;
					$config['allowed_types'] = 'xls|xlsx';
					$config['max_size'] = 10000;

					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('excel_file')) {
						$error = array('error' => $this->upload->display_errors());
						$Arr_Kembali		= array(
							'status'		=> 3,
							'pesan'			=> 'An Error occured, please try again later ...'
						);
					}
					else{
						$media = $this->upload->data();
						$inputFileName = './assets/file/'.$media['file_name'];

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

						for ($row = 6; $row <= $highestRow; $row++)
						{
							$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
							//echo "<pre>";print_r($rowData);exit;
							$Urut++;

							//Kode =>  Kolom 1
							$nm_supplier	= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:'';
							$Arr_Detail[$Urut]['nm_supplier']  = $nm_supplier;

							$id_negara		= (isset($rowData[0][2]) && $rowData[0][2])?$rowData[0][2]:'';
							$Arr_Detail[$Urut]['id_negara']  = $id_negara;

							$mata_uang		= (isset($rowData[0][3]) && $rowData[0][3])?$rowData[0][3]:'-';
							$Arr_Detail[$Urut]['mata_uang']  = $mata_uang;

							$alamat			= (isset($rowData[0][4]) && $rowData[0][4])?$rowData[0][4]:'-';
							$Arr_Detail[$Urut]['alamat']  = $alamat;

							$telpon			= (isset($rowData[0][5]) && $rowData[0][5])?$rowData[0][5]:'-';
							$Arr_Detail[$Urut]['telpon']  = $telpon;

							$telpon2			= (isset($rowData[0][6]) && $rowData[0][6])?$rowData[0][6]:'-';
							$Arr_Detail[$Urut]['telpon2']  = $telpon2;

							$telpon3			= (isset($rowData[0][7]) && $rowData[0][7])?$rowData[0][7]:'-';
							$Arr_Detail[$Urut]['telpon3']  = $telpon3;

							$fax			= (isset($rowData[0][8]) && $rowData[0][8])?$rowData[0][8]:'-';
							$Arr_Detail[$Urut]['fax']  = $fax;

							$email			= (isset($rowData[0][9]) && $rowData[0][9])?$rowData[0][9]:'';
							$Arr_Detail[$Urut]['email']  = $email;

							$email2			= (isset($rowData[0][10]) && $rowData[0][10])?$rowData[0][10]:'';
							$Arr_Detail[$Urut]['email2']  = $email2;

							$email3			= (isset($rowData[0][11]) && $rowData[0][11])?$rowData[0][11]:'';
							$Arr_Detail[$Urut]['email3']  = $email3;

							$cp				= (isset($rowData[0][12]) && $rowData[0][12])?$rowData[0][12]:'-';
							$Arr_Detail[$Urut]['cp']  = $cp;

							$hp_cp			= (isset($rowData[0][13]) && $rowData[0][13])?$rowData[0][13]:'-';
							$Arr_Detail[$Urut]['hp_cp']  = $hp_cp;

							$id_webchat		= (isset($rowData[0][14]) && $rowData[0][14])?$rowData[0][14]:'-';
							$Arr_Detail[$Urut]['id_webchat']  = $id_webchat;

							$npwp			= (isset($rowData[0][15]) && $rowData[0][15])?$rowData[0][15]:'-';
							$Arr_Detail[$Urut]['npwp']  = $npwp;

							$alamat_npwp	= (isset($rowData[0][16]) && $rowData[0][16])?$rowData[0][16]:'-';
							$Arr_Detail[$Urut]['alamat_npwp']  = $alamat_npwp;

							$keterangan		= (isset($rowData[0][17]) && $rowData[0][17])?$rowData[0][17]:'-';
							$Arr_Detail[$Urut]['keterangan']  = $keterangan;

							$Arr_Detail[$Urut]['created_by']    = $Create_By;
							$Arr_Detail[$Urut]['created_date']  = $Create_Date;

							if($Arr_Detail[$Urut]['id_negara'] == '' || $Arr_Detail[$Urut]['id_negara'] == '-' || $Arr_Detail[$Urut]['id_negara'] == ' ')
							{
								$intError++;
								$status		= 3;
								$pesan		= "Country ID number ".$Urut." is empty. Please check back ...";

							}
							if($Arr_Detail[$Urut]['nm_supplier'] == '' || $Arr_Detail[$Urut]['nm_supplier'] == '-' || $Arr_Detail[$Urut]['nm_supplier'] == ' ')
							{
								$intError++;
								$status		= 3;
								$pesan		= "Supplier Name number ".$Urut." is empty. Please check back ...";

							}
							if($Arr_Detail[$Urut]['email'] == '' || $Arr_Detail[$Urut]['email'] == '-' || $Arr_Detail[$Urut]['email'] == ' ')
							{
								$intError++;
								$status		= 3;
								$pesan		= "Supplier Email number ".$Urut." is empty. Please check back ...";
							}

						} //akhir perulangan

						if($intError > 0){
							$Arr_Kembali	= array(
								'pesan'		=> $pesan,
								'status'	=> $status
							);
						}
						else{
							$dt = array();
							$dtx = array();
							foreach($Arr_Detail AS $val => $valx){
								$dt['nm_supplier']	= trim(strtoupper($valx['nm_supplier']));
								$dt['id_negara']	= trim(strtoupper($valx['id_negara']));
								$dt['mata_uang']	= trim($valx['mata_uang']);
								$dt['alamat']		= trim(strtoupper($valx['alamat']));
								$dt['telpon']		= trim($valx['telpon']);
								$dt['telpon2']		= trim($valx['telpon2']);
								$dt['telpon3']		= trim($valx['telpon3']);
								$dt['fax']			= trim($valx['fax']);
								$dt['email']		= trim(strtolower($valx['email']));
								$dt['email2']		= trim(strtolower($valx['email2']));
								$dt['email3']		= trim(strtolower($valx['email3']));
								$dt['cp']			= trim(ucwords(strtolower($valx['cp'])));
								$dt['hp_cp']		= trim($valx['hp_cp']);
								$dt['id_webchat']	= trim($valx['id_webchat']);
								$dt['npwp']			= trim($valx['npwp']);
								$dt['alamat_npwp']	= trim($valx['alamat_npwp']);
								$dt['keterangan']	= trim($valx['keterangan']);

								$sql_Nums	= "SELECT * FROM supplier WHERE email='".$valx['email']."' ";
								$q_Nums 	= $this->db->query($sql_Nums);
								$num_Rows 	= $q_Nums->num_rows();

								if($num_Rows < 1){
									$Ym				= date('ym');
									//pengurutan kode untuk insert data
									$srcMtr			= "SELECT MAX(id_supplier) as maxP FROM supplier WHERE id_supplier LIKE '".strtoupper($valx['id_negara'])."-".$Ym."%' ";
									$numrowMtr		= $this->db->query($srcMtr)->num_rows();
									$resultMtr		= $this->db->query($srcMtr)->result_array();
									$angkaUrut2		= $resultMtr[0]['maxP'];
									$urutan2		= (int)substr($angkaUrut2, 8, 3);
									$urutan2++;
									$urut2			= sprintf('%03s',$urutan2);
									$id_supplier	= strtoupper($valx['id_negara'])."-".$Ym.$urut2;

									$dt['created_by']   = $valx['created_by'];
									$dt['created_on'] 	= $valx['created_date'];
									$dt['id_supplier']	= $id_supplier;
								}
								if($num_Rows > 0){
									$dt['modified_by']  = $valx['created_by'];
									$dt['modified_on'] 	= $valx['created_date'];
								}

								if($num_Rows > 0){
									$this->db->trans_strict(FALSE);
									$this->db->trans_start();
									$update_v = $this->db
										->where('email', $valx['email'])
										->update('supplier', $dt);
									$this->db->trans_complete();
								}
								if($num_Rows < 1){
									$this->db->trans_strict(FALSE);
									$this->db->trans_start();
									$this->db->insert('supplier', $dt);
									$this->db->trans_complete();
								}
							} //akhir perulangan

							// exit;
							if ($this->db->trans_status() === FALSE){
								$this->db->trans_rollback();
								$Arr_Kembali	= array(
									'pesan'		=>'Upload Excell Supplier Failed. Please try again later ...',
									'status'	=> 2
								);
							}
							else{
								$this->db->trans_commit();
								$Arr_Kembali	= array(
									'pesan'		=>'Upload Excell Supplier Success. Thanks ...',
									'status'	=> 1
								);
								history('Upload Excell Supplier');
							}
						}
					}
				}
			}
			//penutup data array
			echo json_encode($Arr_Kembali);
		}
	}

	//===========================================================================
	//============================SUPPLIER MATERIAL==============================
	//===========================================================================

	public function supplier_material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier_material';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		// $get_Data			= $this->master_model->getData('raw_materials');
		$get_Data			= $this->db->query("SELECT*FROM raw_materials WHERE `delete` = 'N' ORDER BY nm_material ASC")->result();
		$menu_akses			= $this->master_model->getMenu();

		$data = array(
			'title'			=> 'Indeks Of Supplier Material',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Supplier Material');
		$this->load->view('Supplier/supplier_material',$data);
	}

	public function supplier_material_edit(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;

			$id_material	= $data['id_material'];
			$nm_material	= $data['nm_material'];
			$Detail	= $data['Detail'];
			$kdYMH	= date('ymd');
			$ArrSup	= array();
			$ArrSupLog	= array();
			$no = 0;
			if(!empty($Detail)){
				foreach($Detail AS $val => $valx){
					$no++;
					$flag = 'N';
						if(!empty($valx['flag_active'])){
							$flag = 'Y';
						}

					$sqlUrutan		= "SELECT MAX(id_supplier_material) AS maxEX FROM raw_material_supplier WHERE id_material='".$id_material."' ";
					$urutanEncode	= $this->db->query($sqlUrutan)->result_array();
					$urutaN			= $urutanEncode[0]['maxEX'];
					$ListUrt		= explode("/", $urutaN);

					$urutX	= ($urutaN == null || $urutaN == '')?$no:($ListUrt[2] + 1);
					// print_r($urutanEncode);
					// echo $urutX; exit;

					$ChNmSUpp	= $this->db->query("SELECT * FROM supplier WHERE id_supplier='".$valx['id_supplier']."' LIMIT 1")->result_array();
					$ArrSup[$val]['id_supplier_material'] 	= $id_material."/".$valx['id_supplier']."/".$urutX."/".$kdYMH;
					$ArrSup[$val]['id_material'] 	= $id_material;
					$ArrSup[$val]['nm_material'] 	= $nm_material;
					$ArrSup[$val]['id_supplier'] 	= $valx['id_supplier'];
					$ArrSup[$val]['nm_supplier'] 	= $ChNmSUpp[0]['nm_supplier'];
					$ArrSup[$val]['price'] 			= str_replace(',', '', $valx['price']);
					$ArrSup[$val]['valid_until'] 	= $valx['valid_until'];
					$ArrSup[$val]['descr'] 			= $valx['descr'];
					$ArrSup[$val]['flag_active'] 	= $valx['flag_active'];
					$ArrSup[$val]['unit'] 			= $valx['unit'];
					$ArrSup[$val]['moq'] 			= str_replace(',', '', $valx['moq']);
					$ArrSup[$val]['lead_time_order'] = str_replace(',', '', $valx['lead_time_order']);
					$ArrSup[$val]['created_by'] 	= $data_session['ORI_User']['username'];
					$ArrSup[$val]['created_date'] 	= date('Y-m-d H:i:s');

					$ArrSupLog[$val]['id_supplier_material'] 	= $id_material."/".$valx['id_supplier']."/".$urutX."/".$kdYMH;
					$ArrSupLog[$val]['id_material'] 			= $id_material;
					$ArrSupLog[$val]['nm_material'] 			= $nm_material;
					$ArrSupLog[$val]['id_supplier'] 			= $valx['id_supplier'];
					$ArrSupLog[$val]['nm_supplier'] 			= $ChNmSUpp[0]['nm_supplier'];
					$ArrSupLog[$val]['price'] 					= str_replace(',', '', $valx['price']);
					$ArrSupLog[$val]['valid_until'] 			= $valx['valid_until'];
					$ArrSupLog[$val]['unit'] 					= $valx['unit'];
					$ArrSupLog[$val]['moq'] 					= str_replace(',', '', $valx['moq']);
					$ArrSupLog[$val]['created_by'] 				= $data_session['ORI_User']['username'];
					$ArrSupLog[$val]['created_date'] 			= date('Y-m-d H:i:s');
				}

				// print_r($ArrSup);
				// print_r($ArrSupLog);
				// exit;

				$this->db->trans_start();
					$this->db->delete('raw_material_supplier', array('id_material' => $id_material));

					$this->db->insert_batch('raw_material_supplier_log', $ArrSupLog);
					$this->db->insert_batch('raw_material_supplier', $ArrSup);
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Data	= array(
						'pesan'		=>'Update data failed. Please try again later ...',
						'status'	=> 0
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Data	= array(
						'pesan'		=>'Update data success. Thanks ...',
						'status'	=> 1
					);
					history('Update Material Supplier '.$id_material);
				}

				// print_r($Arr_Data); exit;
			}
			else{
				$Arr_Data	= array(
					'pesan'		=>'Data empty ...',
					'status'	=> 0
				);
			}

			echo json_encode($Arr_Data);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/supplier_material';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}

			$id = $this->uri->segment(3);
			$detail			= $this->db->query("SELECT * FROM raw_materials WHERE id_material = '".$id."' ")->result_array();
			$getPiece		= $this->db->query("SELECT * FROM raw_pieces ORDER BY nama_satuan ASC")->result_array();
			$ListSup		= $this->db->query("SELECT * FROM supplier")->result_array();
			$Supply			= $this->db->query("SELECT * FROM raw_material_supplier WHERE id_material = '".$id."' ")->result_array();

			$data = array(
				'title'			=> 'Edit Supplier Material',
				'action'		=> 'edit',
				'row'			=> $detail,
				'ListSup'		=> $ListSup,
				'Supply'		=> $Supply,
				'getPiece'		=> $getPiece
			);

			$this->load->view('Supplier/supplier_material_edit',$data);
		}
	}

	public function getSupplierED(){
		$materialID = $this->input->post('id_material');

		$qSupDipakai		= "SELECT id_supplier FROM raw_material_supplier WHERE id_material = '".$materialID."'";
		$dataSupDipakai	= $this->db->query($qSupDipakai)->result_array();
		$dtListArray = array();
		foreach($dataSupDipakai AS $val => $valx){
			$dtListArray[$val] = $valx['id_supplier'];
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$sqlSup	= "SELECT * FROM supplier WHERE id_supplier NOT IN ".$dtImplode." AND sts_aktif='aktif' ORDER BY nm_supplier ASC";

		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Supplier</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_supplier']."'>".$valx['nm_supplier']."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

		$supplier	= $this->db->query("SELECT * FROM supplier WHERE sts_aktif='aktif' ORDER BY nm_supplier ASC")->result_array();
		$unit	= $this->db->query("SELECT * FROM raw_pieces WHERE flag_active='Y' AND `delete`='N' ORDER BY nama_satuan ASC")->result_array();
		// echo $qListResin; exit;
		$d_Header = "";
		// $d_Header .= "<tr>";
			$d_Header .= "<tr class='header_".$id."'>";
				$d_Header .= "<td align='center'>".$id."</td>";
				$d_Header .= "<td align='left'>";
					$d_Header .= "<select name='Detail[".$id."][id_supplier]' id='cost_".$id."'data-id='".$id."' class='chosen_select form-control input-sm inline-blockd'>";
					$d_Header .= "<option value='0'>Select Supplier</option>";
					foreach($supplier AS $val => $valx){
					  $d_Header .= "<option value='".$valx['id_supplier']."'>".strtoupper($valx['nm_supplier'])."</option>";
					}
					$d_Header .= 		"</select>";
				$d_Header .= "</td>";
				$d_Header .= "<td align='left'><input type='text' class='form-control text-right input-md maskM' name='Detail[".$id."][price]'></td>";
				$d_Header .= "<td align='center'><input type='text' class='form-control text-center input-md tgl' name='Detail[".$id."][valid_until]' readonly></td>";
				$d_Header .= "<td align='center'><input type='text' class='form-control text-center input-md maskM' name='Detail[".$id."][moq]' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
				$d_Header .= "<td align='left'>";
					$d_Header .= "<select name='Detail[".$id."][unit]' class='chosen_select form-control input-sm inline-blockd'>";
					$d_Header .= "<option value='0'>Select Unit</option>";
					foreach($unit AS $val => $valx){
					  $d_Header .= "<option value='".$valx['kode_satuan']."'>".strtoupper($valx['nama_satuan'])."</option>";
					}
					$d_Header .= 		"</select>";
				$d_Header .= "</td>";
				$d_Header .= "<td align='center'><input type='text' class='form-control text-center input-md maskM' name='Detail[".$id."][lead_time_order]' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
					$d_Header .= "<td align='center'><input type='text' class='form-control input-md' name='Detail[".$id."][descr]'></td>";
				$d_Header .= "<td align='left'>";
					$d_Header .= "<select name='Detail[".$id."][flag_active]' class='chosen_select form-control input-sm inline-blockd'>";
					$d_Header .= "<option value='Y'>Active</option>";
					$d_Header .= "<option value='N'>Non-Active</option>";
					$d_Header .= 		"</select>";
				$d_Header .= "</td>";
				$d_Header .= "<td align='center'>";
					$d_Header .= "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
				$d_Header .= "</td>";
			$d_Header .= "</tr>";
			//add part
			$d_Header .= "<tr class='add_".$id."'>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='left'><button type='button' class='btn btn-sm btn-warning addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Supplier</button></td>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='center'></td>";
			$d_Header .= "</tr>";

  		 echo json_encode(array(
  				'header'			=> $d_Header,
  		 ));
	}

}
