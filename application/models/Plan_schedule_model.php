<?php
class Plan_schedule_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function so(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/so';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$engine = $this->uri->segment(3);
		$data = array(
			'title'			=> 'List Scheduling Sales Order ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'engine'		=> $engine,
			'akses_menu'	=> $Arr_Akses
		);
		history('View planning sales order');
		$this->load->view('Plan_schedule/so',$data);
	}
	
	public function server_side_schedule_so(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)))."/so";
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->quary_data_schedule_so(
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		
		$tanda = $this->uri->segment(3);
		
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$order_type 	= (!empty($row['order_type']))?$row['order_type']:'-';
			$nestedData[]	= "<div align='center'>".strtoupper($order_type)."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['rev_master_spool']."</span></div>";
			$nestedData[]	= "<div align='left'></div>";
			
			$class = get_name('color_status_umum','warna','id',$row['status']);
			$status = get_name('color_status_umum','status','id',$row['status']);
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$status."</span></div>";
			
			$choose 		= "";
			$edit_choose 	= "";
			$proses_jadwal 	= "";
			$proses_split 	= "";
			$proses_cc 	= "";
			$jadwal 	= "";
			$detail 	= "";
			$new_fd 	= "";
			
			if(!empty($tanda)){
				$detail	= "<button type='button' class='btn btn-sm btn-warning detail_so' title='Detail' data-id_bq='".$row['no_ipp']."' style='margin-top:2px;'><i class='fa fa-eye'></i></button>";
			}
			
			if($row['status'] == '6'){
				if(!empty($tanda)){
					$choose	= "&nbsp;<button type='button' class='btn btn-sm btn-primary choose_so' title='Choose' data-id_bq='".$row['no_ipp']."' style='margin-top:2px;'><i class='fa fa-hand-pointer-o'></i></button>";
				}
			}

			if($row['status'] == '7'){
				if(!empty($tanda)){
					if($row['order_type'] == 'spool'){
						$edit_choose	= "&nbsp;<button type='button' class='btn btn-sm btn-success edit_choose_so' title='Edit Choose' data-id_bq='".$row['no_ipp']."' style='margin-top:2px;'><i class='fa fa-edit'></i></button>";
						$proses_split	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/proses_split/'.$row['no_ipp'].'/'.$tanda)."' class='btn btn-sm btn-primary' title='Breaking Estimasi' data-role='qtip' style='margin-top:2px;'><i class='fa fa-puzzle-piece'></i></a>";
					}
				}
				
				if(empty($tanda)){
					if($row['order_type'] == 'spool'){
						$proses_jadwal	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/proses_jadwal/'.$row['no_ipp'])."' class='btn btn-sm btn-info' title='Proses Scheduling' data-role='qtip' style='margin-top:2px;'><i class='fa fa-calendar'></i></a>";
					}
					$proses_cc		= "&nbsp;<a href='".site_url($this->uri->segment(1).'/proses_costcenter/'.$row['no_ipp'])."' class='btn btn-sm' style='background-color:#d02ded; color:white; margin-top:2px;' title='Selected Costcenter' data-role='qtip'><i class='fa fa-calendar-check-o'></i></a>";
				}
			}

			// if($row['status'] == '8'){
				// $jadwal		= "&nbsp;<a href='".site_url($this->uri->segment(1).'/proses_scheduling/'.$row['no_ipp'])."' class='btn btn-sm' style='background-color:#f7119c; color:white; margin-top:2px;' title='Penjadwalan' data-role='qtip'><i class='fa fa-line-chart'></i></a>";
			// }

			// $get_check = $this->db->get_where('so_detail_header', array('id_bq'=>'BQ-'.$row['no_ipp'], 'id_milik'=>NULL))->num_rows();
			// if($get_check > 0){
				// $new_fd		= "&nbsp;<a href='".site_url($this->uri->segment(1).'/fd_plus/'.$row['no_ipp'])."' class='btn btn-sm' style='background-color:#f7119c; color:white; margin-top:2px;' title='New Final Drawing' data-role='qtip'><i class='fa fa-plus'></i></a>";
			// }
					
			$nestedData[]	= "<div align='left'>".$detail.$choose.$edit_choose.$proses_split.$proses_jadwal.$proses_cc.$jadwal.$new_fd."</div>";
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

	public function quary_data_schedule_so($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.project,
				b.nm_customer
			FROM
				scheduling_produksi a LEFT JOIN production b ON a.no_ipp=b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'so_number',
			3 => 'nm_customer',
			4 => 'project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modal_detail_so(){
		$id_bq = 'BQ-'.$this->uri->segment(3);

		$sql_product 	= "SELECT a.* FROM so_bf_detail_header a WHERE a.id_bq = '".$id_bq."' AND a.id_category <> 'pipe slongsong' ORDER BY a.id_bq_header ASC";
		$rest_product		= $this->db->query($sql_product)->result_array();

		$detail 		= $this->db->query("SELECT * FROM so_bf_acc_and_mat WHERE id_bq='".$id_bq."' AND category='acc'")->result_array();
		$detail2 		= $this->db->query("SELECT * FROM so_bf_acc_and_mat WHERE id_bq='".$id_bq."' AND category='mat'")->result_array();
		
		$data = array(
			'id_bq'				=> $id_bq,
			'detail_product'	=> $rest_product,
			'detail'			=> $detail,
			'detail2'			=> $detail2
		);
		
		$this->load->view('Plan_schedule/modal_detail_so', $data);
	}
	
	public function modal_choose_so(){
		$no_ipp 	= $this->uri->segment(3);
		$detail 	= $this->db->query("SELECT * FROM master_spool WHERE no_ipp='".$no_ipp."'")->result_array();
		
		
		$header 	= $this->db->query("SELECT * FROM scheduling_produksi WHERE no_ipp='".$no_ipp."'")->result();
		$checked	= ($header[0]->order_type == 'spool')?'checked':'';
		$product 	= $this->db->get_where('product_parent', array('deleted'=>'N'))->result_array();
		
		$data = array(
			'no_ipp'	=> $no_ipp,
			'checked'	=> $checked,
			'header'	=> $header,
			'detail'	=> $detail,
			'product'	=> $product
		);
		
		$this->load->view('Plan_schedule/modal_choose_so', $data);
	}
	
	public function temp_format(){
        //membuat objek PHPExcel
        set_time_limit(0);
        ini_set('memory_limit','1024M');
		
        $no_ipp 	= $this->uri->segment(3);
		
        $this->load->library("PHPExcel");
        //$this->load->library("PHPExcel/Writer/Excel2007");
        $objPHPExcel    = new PHPExcel();
         
        $style_header = array(
  			'borders' => array(
  				'allborders' => array(
  					  'style' => PHPExcel_Style_Border::BORDER_THIN,
  					  'color' => array('rgb'=>'000000')
  				  )
  			),
  			'fill' => array(
  				'type' => PHPExcel_Style_Fill::FILL_SOLID,
  				'color' => array('rgb'=>'e0e0e0'),
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
  				'color' => array('rgb'=>'e0e0e0'),
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
         
		$master_spool	= "SELECT a.* FROM master_spool a WHERE a.no_ipp = '".$no_ipp."' AND a.status = 'N' ";
		$data_spool   = $this->db->query($master_spool)->result_array();
		
		$no_so 		= get_nomor_so($no_ipp);
		$customer 	= strtoupper(get_name('production','nm_customer','no_ipp',$no_ipp));
		$project 	= strtoupper(get_name('production','project','no_ipp',$no_ipp));
		
        $Arr_Bulan  = array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
        $sheet      = $objPHPExcel->getActiveSheet();
		
        $dateX	= date('Y-m-d H:i:s');
        $Row        = 1;
        $NewRow     = $Row+1;
        $Col_Akhir  = $Cols = getColsChar(11);
        $sheet->setCellValue('A'.$Row, "MASTER SPOOL ".$no_ipp."");
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
         
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'NO IPP');
		$sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('C'.$NewRow, $no_ipp);
		$sheet->getStyle('C'.$NewRow.':K'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':K'.$NewRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'NO SO');
		$sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('C'.$NewRow, $no_so);
		$sheet->getStyle('C'.$NewRow.':K'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':K'.$NewRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'CUSTOMER');
		$sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('C'.$NewRow, $customer);
		$sheet->getStyle('C'.$NewRow.':K'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':K'.$NewRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'PROJECT');
		$sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('C'.$NewRow, $project);
		$sheet->getStyle('C'.$NewRow.':K'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':K'.$NewRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('J'.$NewRow, '*delivery date wajib format DATE');
		$sheet->getStyle('J'.$NewRow.':J'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('J'.$NewRow.':J'.$NewRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
        
		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, 'SPOOL');
        $sheet->getStyle('A'.$NewRow.':A'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('A'.$NewRow.':A'.$NewRow);
        
		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'ID PART');
        $sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('B'.$NewRow.':B'.$NewRow);
		
		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'ID PRODUCT CUSTOMER');
        $sheet->getStyle('C'.$NewRow.':C'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('C'.$NewRow.':C'.$NewRow);
        
		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'NAMA PRODUCT');
        $sheet->getStyle('D'.$NewRow.':D'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('D'.$NewRow.':D'.$NewRow);
        
		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'D1');
        $sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('E'.$NewRow.':E'.$NewRow);
		
		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'D2');
        $sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('F'.$NewRow.':F'.$NewRow);
		
		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'THICKNESS');
        $sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('G'.$NewRow.':G'.$NewRow);
		
		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'LENGTH/SUDUT');
        $sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('H'.$NewRow.':H'.$NewRow);
		
		$sheet ->getColumnDimension("I")->setAutoSize(true);
		$sheet->setCellValue('I'.$NewRow, 'SR/LR');
        $sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('I'.$NewRow.':I'.$NewRow);
		
		$sheet ->getColumnDimension("J")->setAutoSize(true);
		$sheet->setCellValue('J'.$NewRow, 'DELIVERY DATE');
        $sheet->getStyle('J'.$NewRow.':J'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('J'.$NewRow.':J'.$NewRow);
		
		$sheet ->getColumnDimension("K")->setAutoSize(true);
		$sheet->setCellValue('K'.$NewRow, 'KETERANGAN');
        $sheet->getStyle('K'.$NewRow.':K'.$NewRow)->applyFromArray($style_header);
        $sheet->mergeCells('K'.$NewRow.':K'.$NewRow);
		 
		if($data_spool){
			$awal_row   = $NextRow;
			 $no = 0;
			foreach($data_spool as $key=>$vals){
				$no++;
				$awal_row++;
				$awal_col   = 0;
				 
				$awal_col++;
				$spool   = $vals['spool'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spool);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$id_spool   = $vals['id_spool'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_spool);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$id_product   = $vals['id_product'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$nm_product   = $vals['nm_product'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$d1   = $vals['d1'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $d1);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$d2   = $vals['d2'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $d2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$thickness   = $vals['thickness'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $thickness);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$length_sudut   = $vals['length_sudut'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $length_sudut);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$sr_lr   = $vals['sr_lr'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $sr_lr);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$delivery_date   = date('d/m/Y', strtotime($vals['delivery_date']));
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $delivery_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				$awal_col++;
				$keterangan   = $vals['keterangan'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $keterangan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray2);
				
				
			}
		}
        
		history('Download templete upload master spool '.$no_ipp);
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
        header('Content-Disposition: attachment;filename="TEMP_SPOOL_'.$no_ipp.'_'.date('YmdHis').'.xls"');
        //unduh file
        $objWriter->save("php://output"); 
    }
	
	public function import_data(){
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
						
						for ($row = 10; $row <= $highestRow; $row++)
						{                              
							$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
							// echo "<pre>";print_r($rowData);exit;
							$Urut++;
							 
							//Kode =>  Kolom 1
							$Arr_Detail[$Urut]['no_ipp']  		= $this->input->post('no_ipp');
							
							$spool								= (isset($rowData[0][0]) && $rowData[0][0])?$rowData[0][0]:NULL;
							$Arr_Detail[$Urut]['spool']  		= $spool;
							
							$id_spool							= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:NULL;
							$Arr_Detail[$Urut]['id_spool']  	= strtoupper($id_spool);
							
							$id_product							= (isset($rowData[0][2]) && $rowData[0][2])?$rowData[0][2]:NULL;
							$Arr_Detail[$Urut]['id_product'] 	= $id_product;
							
							$nm_product							= (isset($rowData[0][3]) && $rowData[0][3])?$rowData[0][3]:NULL;
							$Arr_Detail[$Urut]['nm_product']  	= strtolower($nm_product);
							
							$d1									= (isset($rowData[0][4]) && $rowData[0][4])?$rowData[0][4]:NULL;
							$Arr_Detail[$Urut]['d1']  			= $d1;
							
							$d2									= (isset($rowData[0][5]) && $rowData[0][5])?$rowData[0][5]:NULL;
							$Arr_Detail[$Urut]['d2']  			= $d2;
							
							$thickness							= (isset($rowData[0][6]) && $rowData[0][6])?$rowData[0][6]:NULL;
							$Arr_Detail[$Urut]['thickness']  	= $thickness;
							
							$length_sudut						= (isset($rowData[0][7]) && $rowData[0][7])?$rowData[0][7]:NULL;
							$Arr_Detail[$Urut]['length_sudut']  = $length_sudut;
							
							$sr_lr								= (isset($rowData[0][8]) && $rowData[0][8])?$rowData[0][8]:NULL;
							$Arr_Detail[$Urut]['sr_lr']  		= $sr_lr;
							
							$delivery_date						= (isset($rowData[0][9]) && $rowData[0][9])?$rowData[0][9]:NULL;
							$unix_date 		= ($delivery_date - 25569) * 86400;
							$delivery_date 	= 25569 + ($unix_date / 86400);
							$unix_date 		= ($delivery_date - 25569) * 86400;
							$Arr_Detail[$Urut]['delivery_date'] = gmdate("Y-m-d", $unix_date);;
							
							$keterangan							= (isset($rowData[0][10]) && $rowData[0][10])?$rowData[0][10]:NULL;
							$Arr_Detail[$Urut]['keterangan']  	= strtolower($keterangan);
							
							$Arr_Detail[$Urut]['created_by']    = $Create_By;
							$Arr_Detail[$Urut]['created_date']  = $Create_Date; 
							
						} //akhir perulangan
						
						// echo "<pre>";
						// print_r($Arr_Detail);
						// exit;
						
						if($intError > 0){
							$Arr_Kembali	= array(
								'pesan'		=> $pesan,
								'status'	=> $status
							);
						}
						else{
							
							$ArrDetail 		= array();
							foreach($Arr_Detail AS $val => $valx){
							
								$ArrDetail[$val]['no_ipp'] 			= $valx['no_ipp'];
								$ArrDetail[$val]['spool'] 			= $valx['spool'];
								$ArrDetail[$val]['id_spool'] 		= $valx['id_spool'];
								$ArrDetail[$val]['id_product'] 		= $valx['id_product'];
								$ArrDetail[$val]['nm_product'] 		= $valx['nm_product'];
								$ArrDetail[$val]['d1'] 				= $valx['d1'];
								$ArrDetail[$val]['d2'] 				= $valx['d2'];
								$ArrDetail[$val]['thickness'] 		= $valx['thickness'];
								$ArrDetail[$val]['length_sudut']	= $valx['length_sudut'];
								$ArrDetail[$val]['sr_lr'] 			= $valx['sr_lr'];
								$ArrDetail[$val]['delivery_date'] 	= $valx['delivery_date'];
								$ArrDetail[$val]['keterangan'] 		= $valx['keterangan'];
								$ArrDetail[$val]['created_by'] 		= $valx['created_by'];
								$ArrDetail[$val]['created_date'] 	= $valx['created_date'];	
							}
							
							// echo "<pre>";
							// print_r($ArrDetail);
							// exit;
							
							$this->db->trans_start();
								if(!empty($ArrDetail)){
									$this->db->delete('master_spool', array('no_ipp'=>$this->input->post('no_ipp'),'status'=>'N'));
									$this->db->insert_batch('master_spool', $ArrDetail);
								}
							$this->db->trans_complete();

							if ($this->db->trans_status() === FALSE){
								$this->db->trans_rollback();
								$Arr_Kembali	= array(
									'pesan'		=>'Upload Excell Failed. Please try again later ...',
									'status'	=> 2,
									'engine'	=> $this->input->post('engine')
								);
							}
							else{
								$this->db->trans_commit();
								$Arr_Kembali	= array(
									'pesan'		=>'Upload Excell Success. Thanks ...',
									'status'	=> 1,
									'engine'	=> $this->input->post('engine')
								);
								history('Upload master spool '.$this->input->post('no_ipp'));
							}
								
							
						}
					}
				}
			} 
			//penutup data array
			echo json_encode($Arr_Kembali);
		}
	} 
	
	public function import_data2(){
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
						
						for ($row = 10; $row <= $highestRow; $row++)
						{                              
							$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL,TRUE,FALSE);
							// echo "<pre>";print_r($rowData);exit;
							$Urut++;
							 
							//Kode =>  Kolom 1
							$Arr_Detail[$Urut]['no_ipp']  		= $this->input->post('no_ipp');
							
							$spool								= (isset($rowData[0][0]) && $rowData[0][0])?$rowData[0][0]:NULL;
							$Arr_Detail[$Urut]['spool']  		= $spool;
							
							$id_spool							= (isset($rowData[0][1]) && $rowData[0][1])?$rowData[0][1]:NULL;
							$Arr_Detail[$Urut]['id_spool']  	= strtoupper($id_spool);
							
							$id_product							= (isset($rowData[0][2]) && $rowData[0][2])?$rowData[0][2]:NULL;
							$Arr_Detail[$Urut]['id_product'] 	= $id_product;
							
							$nm_product							= (isset($rowData[0][3]) && $rowData[0][3])?$rowData[0][3]:NULL;
							$Arr_Detail[$Urut]['nm_product']  	= strtolower($nm_product);
							
							$d1									= (isset($rowData[0][4]) && $rowData[0][4])?$rowData[0][4]:NULL;
							$Arr_Detail[$Urut]['d1']  			= $d1;
							
							$d2									= (isset($rowData[0][5]) && $rowData[0][5])?$rowData[0][5]:NULL;
							$Arr_Detail[$Urut]['d2']  			= $d2;
							
							$thickness							= (isset($rowData[0][6]) && $rowData[0][6])?$rowData[0][6]:NULL;
							$Arr_Detail[$Urut]['thickness']  	= $thickness;
							
							$length_sudut						= (isset($rowData[0][7]) && $rowData[0][7])?$rowData[0][7]:NULL;
							$Arr_Detail[$Urut]['length_sudut']  = $length_sudut;
							
							$sr_lr								= (isset($rowData[0][8]) && $rowData[0][8])?$rowData[0][8]:NULL;
							$Arr_Detail[$Urut]['sr_lr']  		= $sr_lr;
							
							$delivery_date						= (isset($rowData[0][9]) && $rowData[0][9])?$rowData[0][9]:NULL;
							$unix_date 		= ($delivery_date - 25569) * 86400;
							$delivery_date 	= 25569 + ($unix_date / 86400);
							$unix_date 		= ($delivery_date - 25569) * 86400;
							$Arr_Detail[$Urut]['delivery_date'] = gmdate("Y-m-d", $unix_date);
							
							$keterangan							= (isset($rowData[0][10]) && $rowData[0][10])?$rowData[0][10]:NULL;
							$Arr_Detail[$Urut]['keterangan']  	= strtolower($keterangan);
							
							$Arr_Detail[$Urut]['created_by']    = $Create_By;
							$Arr_Detail[$Urut]['created_date']  = $Create_Date; 
							
						} //akhir perulangan
						
						// echo "<pre>";
						// print_r($Arr_Detail);
						// exit;
						
						if($intError > 0){
							$Arr_Kembali	= array(
								'pesan'		=> $pesan,
								'status'	=> $status
							);
						}
						else{
							
							$no_ipp = $this->input->post('no_ipp');
							
							$ArrDetail 		= array();
							foreach($Arr_Detail AS $val => $valx){
							
								$ArrDetail[$val]['no_ipp'] 			= $valx['no_ipp'];
								$ArrDetail[$val]['spool'] 			= $valx['spool'];
								$ArrDetail[$val]['id_spool'] 		= $valx['id_spool'];
								$ArrDetail[$val]['id_product'] 		= $valx['id_product'];
								$ArrDetail[$val]['nm_product'] 		= $valx['nm_product'];
								$ArrDetail[$val]['d1'] 				= $valx['d1'];
								$ArrDetail[$val]['d2'] 				= $valx['d2'];
								$ArrDetail[$val]['thickness'] 		= $valx['thickness'];
								$ArrDetail[$val]['length_sudut']	= $valx['length_sudut'];
								$ArrDetail[$val]['sr_lr'] 			= $valx['sr_lr'];
								$ArrDetail[$val]['delivery_date'] 	= $valx['delivery_date'];
								$ArrDetail[$val]['keterangan'] 		= $valx['keterangan'];
								$ArrDetail[$val]['created_by'] 		= $valx['created_by'];
								$ArrDetail[$val]['created_date'] 	= $valx['created_date'];	
							}
							
							$get_rev 	= $this->db->query("SELECT * FROM scheduling_produksi WHERE no_ipp='".$no_ipp."'")->result();
							$rev_no		= $get_rev[0]->rev_master_spool;
							
							$ArrEdit = array(
								'rev_master_spool' => $rev_no + 1
							);
							
							//HISTORT SPOOL
							$rest_hist 	= $this->db->query("SELECT * FROM master_spool WHERE no_ipp='".$no_ipp."'")->result_array();
							$ArrHist 	= array();
							foreach($rest_hist AS $val => $valx){
							
								$ArrHist[$val]['no_ipp'] 			= $valx['no_ipp'];
								$ArrHist[$val]['spool'] 			= $valx['spool'];
								$ArrHist[$val]['id_spool'] 			= $valx['id_spool'];
								$ArrHist[$val]['id_product'] 		= $valx['id_product'];
								$ArrHist[$val]['nm_product'] 		= $valx['nm_product'];
								$ArrHist[$val]['d1'] 				= $valx['d1'];
								$ArrHist[$val]['d2'] 				= $valx['d2'];
								$ArrHist[$val]['thickness'] 		= $valx['thickness'];
								$ArrHist[$val]['length_sudut']		= $valx['length_sudut'];
								$ArrHist[$val]['sr_lr'] 			= $valx['sr_lr'];
								$ArrHist[$val]['delivery_date'] 	= $valx['delivery_date'];
								$ArrHist[$val]['keterangan'] 		= $valx['keterangan'];
								$ArrHist[$val]['status'] 			= $valx['status'];
								$ArrHist[$val]['id_use'] 			= $valx['id_use'];
								$ArrHist[$val]['created_by'] 		= $valx['created_by'];
								$ArrHist[$val]['created_date'] 		= $valx['created_date'];
								$ArrHist[$val]['updated_by'] 		= $valx['updated_by'];
								$ArrHist[$val]['updated_date'] 		= $valx['updated_date'];
								$ArrHist[$val]['deleted'] 			= $valx['deleted'];
								$ArrHist[$val]['deleted_by'] 		= $valx['deleted_by'];
								$ArrHist[$val]['deleted_date'] 		= $valx['deleted_date'];
								$ArrHist[$val]['rev'] 				= $rev_no;
								$ArrHist[$val]['hist_by'] 			= $Create_By;
								$ArrHist[$val]['hist_date'] 		= $Create_Date;								
							}
							
							
							// echo "<pre>";
							// print_r($ArrDetail);
							// print_r($ArrHist);
							// exit;
							
							$this->db->trans_start();
								if(!empty($ArrHist)){
									$this->db->insert_batch('hist_master_spool', $ArrHist);
								}
								if(!empty($ArrDetail)){
									$this->db->delete('master_spool', array('no_ipp'=>$no_ipp,'status'=>'N'));
									$this->db->insert_batch('master_spool', $ArrDetail);
									
									$this->db->where('no_ipp',$no_ipp);
									$this->db->update('scheduling_produksi', $ArrEdit);
								}
							$this->db->trans_complete();

							if ($this->db->trans_status() === FALSE){
								$this->db->trans_rollback();
								$Arr_Kembali	= array(
									'pesan'		=>'Upload Excell Failed. Please try again later ...',
									'status'	=> 2,
									'engine'	=> $this->input->post('engine')
								);
							}
							else{
								$this->db->trans_commit();
								$Arr_Kembali	= array(
									'pesan'		=>'Upload Excell Success. Thanks ...',
									'status'	=> 1,
									'engine'	=> $this->input->post('engine')
								);
								history('Upload ulang master spool '.$no_ipp);
							}
								
							
						}
					}
				}
			} 
			//penutup data array
			echo json_encode($Arr_Kembali);
		}
	} 
	
	public function update_spool(){
        $data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		
		$Arr_Detail 	= $data['detail'];
							
		$ArrDetail 		= array();
		foreach($Arr_Detail AS $val => $valx){
			$ArrDetail[$val]['id'] 				= str_replace(',','',$valx['id']);
			$ArrDetail[$val]['spool'] 			= strtoupper($valx['spool']);
			$ArrDetail[$val]['id_spool'] 		= strtoupper($valx['id_spool']);
			$ArrDetail[$val]['id_product'] 		= strtolower($valx['id_product']);
			$ArrDetail[$val]['nm_product'] 		= strtolower($valx['nm_product']);
			$ArrDetail[$val]['d1'] 				= str_replace(',','',$valx['d1']);
			$ArrDetail[$val]['d2'] 				= str_replace(',','',$valx['d2']);
			$ArrDetail[$val]['thickness'] 		= str_replace(',','',$valx['thickness']);
			$ArrDetail[$val]['length_sudut']	= str_replace(',','',$valx['length_sudut']);
			$ArrDetail[$val]['sr_lr'] 			= $valx['sr_lr'];
			$ArrDetail[$val]['delivery_date'] 	= $valx['delivery_date'];
			$ArrDetail[$val]['keterangan'] 		= strtolower($valx['keterangan']);
			$ArrDetail[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
			$ArrDetail[$val]['updated_date'] 	= $dateTime;	
		}
		
		// echo "<pre>";
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
			if(!empty($ArrDetail)){
				$this->db->update_batch('master_spool',$ArrDetail,'id');
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'engine'	=> $this->input->post('engine')
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1,
				'engine'	=> $this->input->post('engine')
			);
			history('Update master spool '.$this->input->post('no_ipp'));
		}

		echo json_encode($Arr_Kembali);
	} 
	
	public function save_category(){
        $data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		
		$no_ipp 		= $data['no_ipp'];
		$spool 			= (!empty($data['spool']))?'spool':'loose';
		
		$ArrUpdate = array(
			'order_type'	=> $spool,
			'status'		=> 7,
			'choise_by'		=> $data_session['ORI_User']['username'],
			'choise_date'	=> $dateTime
		);
		
		$sql_detail = "SELECT
							b.id,
							b.id_category,
							b.diameter_1,
							b.id_product,
							b.length,
							b.id_milik,
							b.diameter_2,
							b.sudut,
							b.type
						FROM
							so_bf_detail_detail a
							LEFT JOIN so_bf_detail_header b ON a.id_bq_header = b.id_bq_header 
						WHERE
							a.id_bq = 'BQ-".$no_ipp."'";
		$sql_result = $this->db->query($sql_detail)->result_array();
		$ArrList = array();
		
		foreach($sql_result AS $val => $valx){
			$ArrList[$val]['id_milik'] 		= $valx['id_milik'];
			$ArrList[$val]['no_ipp'] 		= $no_ipp;
			$ArrList[$val]['product'] 		= $valx['id_category'];
			$ArrList[$val]['id_product']	= $valx['id_product'];
			$ArrList[$val]['diameter'] 		= $valx['diameter_1'];
			$ArrList[$val]['diameter2'] 	= $valx['diameter_2'];
			$ArrList[$val]['sudut'] 		= $valx['sudut'];
			$ArrList[$val]['sr_lr'] 		= $valx['type'];
			$ArrList[$val]['length'] 		= $valx['length'];
			$ArrList[$val]['created_by'] 	= $data_session['ORI_User']['username'];
			$ArrList[$val]['created_date'] 	= $dateTime;
		}
		
							
		// print_r($ArrList);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('no_ipp',$no_ipp);
			$this->db->update('scheduling_produksi',$ArrUpdate);
			
			$this->db->insert_batch('scheduling_dropdown_estimasi',$ArrList);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'engine'	=> $this->input->post('engine')
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1,
				'engine'	=> $this->input->post('engine')
			);
			history('Choise category schedule '.$no_ipp);
		}

		echo json_encode($Arr_Kembali);
	} 
	
	public function modal_edit_choose_so(){
		$no_ipp 	= $this->uri->segment(3);
		$detail 	= $this->db->query("SELECT * FROM master_spool WHERE no_ipp='".$no_ipp."' ORDER BY spool ASC, id_spool ASC")->result_array();
		$header 	= $this->db->query("SELECT * FROM scheduling_produksi WHERE no_ipp='".$no_ipp."'")->result();
		
		$data = array(
			'no_ipp'	=> $no_ipp,
			'header'	=> $header,
			'detail'	=> $detail
		);
		
		$this->load->view('Plan_schedule/modal_edit_choose_so', $data);
	}
	
	public function save_new_spool(){
        $data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$no_ipp 		= $data['no_ipp'];
		$Arr_Detail 	= $data['detail_add'];
							
		$ArrDetail 		= array();
		foreach($Arr_Detail AS $val => $valx){
			$ArrDetail[$val]['no_ipp'] 			= $no_ipp;
			$ArrDetail[$val]['spool'] 			= $valx['spool'];
			$ArrDetail[$val]['id_spool'] 		= strtoupper($valx['id_spool']);
			$ArrDetail[$val]['id_product'] 		= strtolower($valx['id_product']);
			$ArrDetail[$val]['nm_product'] 		= strtolower($valx['nm_product']);
			$ArrDetail[$val]['d1'] 				= str_replace(',','',$valx['d1']);
			$ArrDetail[$val]['d2'] 				= str_replace(',','',$valx['d2']);
			$ArrDetail[$val]['thickness'] 		= str_replace(',','',$valx['thickness']);
			$ArrDetail[$val]['length_sudut']	= str_replace(',','',$valx['length_sudut']);
			$ArrDetail[$val]['sr_lr'] 			= $valx['sr_lr'];
			$ArrDetail[$val]['delivery_date'] 	= $valx['delivery_date'];
			$ArrDetail[$val]['keterangan'] 		= strtolower($valx['keterangan']);
			$ArrDetail[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
			$ArrDetail[$val]['updated_date'] 	= $dateTime;	
		}
		
		// echo "<pre>";
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
			if(!empty($ArrDetail)){
				$this->db->insert_batch('master_spool',$ArrDetail);
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'engine'	=> $this->input->post('engine')
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1,
				'engine'	=> $this->input->post('engine')
			);
			history('Update add master spool '.$no_ipp);
		}

		echo json_encode($Arr_Kembali);
	} 
	
	public function proses_jadwal(){
		$no_ipp 	= $this->uri->segment(3);
		$detail 	= $this->db->query("SELECT * FROM master_spool WHERE no_ipp='".$no_ipp."' GROUP BY spool")->result_array();
		
		$data = array(
			'title'		=> 'Scheduling Process',
			'action'	=> 'proses_jadwal',
			'no_ipp'	=> $no_ipp,
			'detail'	=> $detail
		);
		
		$this->load->view('Plan_schedule/proses_jadwal', $data);
	}
	
	public function dropdown_estimasi(){
		$data = $this->input->post();
		
		$no_ipp 	= $this->uri->segment(3);
		
		// $estimasti 	= '';
		// if(!empty($data['q'])){
			$estimasti 		= strtoupper($data['q']);
			$nm_product 	= $data['nm_product'];
			$dim1 			= $data['dim1'];
			$dim2 			= $data['dim2'];
			$sr_lr 			= $data['sr_lr'];
		// }
		
		$result_query = $this->db->query("SELECT * FROM scheduling_dropdown_estimasi WHERE product='".$nm_product."' AND sr_lr='".$sr_lr."' AND diameter2='".$dim2."' AND diameter='".$dim1."' AND no_ipp='".$no_ipp."' AND status = 'N' AND id_product LIKE '%".$estimasti."%' ")->result_array();
		
		$ArrResult = array();
		foreach($result_query AS $val => $valx){
			$plus = "";
			if($nm_product == 'pipe'){
				$plus = " LENGTH-".number_format($valx['length']);
			}
			$ArrResult[$val]['id'] 		= $valx['id'];
			$ArrResult[$val]['text'] 	= $valx['id_product'].$plus;
		}
		
		$ArrJSON = array(
			'items' => $ArrResult
		);
		
		echo json_encode($ArrJSON);
	}
	
	public function dropdown_estimasi_pipe(){
		$data = $this->input->post();
		
		$no_ipp 	= $this->uri->segment(3);
		
		// $estimasti 	= '';
		// if(!empty($data['q'])){
			$estimasti 		= strtoupper($data['q']);
			$nm_product 	= $data['nm_product'];
			$dim1 	= $data['dim1'];
		// }
		
		$result_query = $this->db->query("SELECT * FROM scheduling_dropdown_estimasi WHERE product='".$nm_product."' AND diameter='".$dim1."' AND no_ipp='".$no_ipp."' AND status = 'N' AND list_pipe='Y' AND id_product LIKE '%".$estimasti."%' ")->result_array();
		
		$ArrResult = array();
		foreach($result_query AS $val => $valx){
			$plus = "";
			if($nm_product == 'pipe'){
				$plus = " LENGTH-".number_format($valx['length']);
			}
			$ArrResult[$val]['id'] 		= $valx['id'];
			$ArrResult[$val]['text'] 	= $valx['id_product'].$plus;
		}
		
		$ArrJSON = array(
			'items' => $ArrResult
		);
		
		echo json_encode($ArrJSON);
	}
	
	public function get_update_estimasi(){
		$data = $this->input->post();
		$data_session	= $this->session->userdata;
		
		$id_spool 	= $data['id_val'];
		$id_est 	= $data['data']['id'];
		
		$rest_est 	= $this->db->get_where('master_spool', array('id'=>$id_spool))->result();
		$val_length = (!empty($rest_est[0]->max_length))?$rest_est[0]->max_length:0;
		$dim2 		= (!empty($rest_est[0]->d1))?$rest_est[0]->d1:0;
		
		$rest_milik 	= $this->db->get_where('scheduling_dropdown_estimasi', array('id'=>$id_est))->result();
		$id_milik = $rest_milik[0]->id_milik;
		
		$length2x =  0;
		if($rest_est[0]->nm_product == 'pipe'){
			$length2x 	= (!empty($rest_est[0]->length_sudut))?$rest_est[0]->length_sudut:0;
		}
		// print_r($id_est); exit;
		
		$ArrInsert = array(
			'id_spool' => $id_spool,
			'id_milik' => $id_milik,
			'id_use' => $id_est,
			'id_product' => get_name('scheduling_dropdown_estimasi','id_product','id',$id_est),
			'length' => get_name('scheduling_dropdown_estimasi','length','id',$id_est),
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		);
		
		$dim1 = get_name('scheduling_dropdown_estimasi','diameter','id',$id_est);
		$max_dim =  $dim1;
		
		$length = get_name('scheduling_dropdown_estimasi','length','id',$id_est);
		
		$max_length =  0;
		if($rest_est[0]->nm_product == 'pipe'){
			$max_length =  $length + $val_length;
		}
		// print_r($ArrInsert);
		// echo $max_dim."<br>";
		// echo $val_max;
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id',$id_spool);
			$this->db->update('master_spool', array('max_dim'=>$dim1,'max_length'=>$max_length));
			
			$this->db->insert('master_spool_use', $ArrInsert);
			
			$this->db->where('id',$id_est);
			$this->db->update('scheduling_dropdown_estimasi', array('status'=>'Y'));
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=>'Process Success. Thanks ...',
				'status'		=> 1,
				'id_spool'		=> $id_spool,
				'max_dim'		=> number_format($max_dim),
				'max_dim2'		=> $max_dim,
				'dim2x'			=> (int)$dim2,
				'dim1x'			=> (int)$dim1,
				'max_length'	=> number_format($max_length),
				'max_length2'	=> (int)$max_length,
				'length'		=> (int)$length2x
				
			);
			history('Choose estimasi in scheduling '.$id_est.', spool id '.$id_spool);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function get_update_estimasi_auto(){
		$data = $this->input->post();
		$data_session	= $this->session->userdata;
		
		$id_spool 	= $data['id_val'];
		
		$check_use = $this->db->get_where('master_spool_use',array('id_spool'=>$id_spool))->result();
		
		if(empty($check_use)){
			$rest_est 	= $this->db->get_where('master_spool', array('id'=>$id_spool))->result();
			if($rest_est[0]->nm_product != 'pipe'){
				$val_length = (!empty($rest_est[0]->max_length))?$rest_est[0]->max_length:0;
				$dim2 		= (!empty($rest_est[0]->d1))?$rest_est[0]->d1:0;
				
				$get_id_est = $this->db->get_where('scheduling_dropdown_estimasi',array('status'=>'N','product'=>$rest_est[0]->nm_product,'diameter'=>$rest_est[0]->d1,'diameter2'=>$rest_est[0]->d2,'sr_lr'=>$rest_est[0]->sr_lr))->result();
				if(!empty($get_id_est)){
					$id_est 	= $get_id_est[0]->id;
					$id_milik = $get_id_est[0]->id_milik;
					
					$length2x =  0;
					if($rest_est[0]->nm_product == 'pipe'){
						$length2x 	= (!empty($rest_est[0]->length_sudut))?$rest_est[0]->length_sudut:0;
					}
					
					$ArrInsert = array(
						'id_spool' => $id_spool,
						'id_milik' => $id_milik,
						'id_use' => $id_est,
						'id_product' => $get_id_est[0]->id_product,
						'length' => $get_id_est[0]->length,
						'created_by' => $data_session['ORI_User']['username'],
						'created_date' => date('Y-m-d H:i:s')
					);
					
					$dim1 		= $get_id_est[0]->diameter;
					$max_dim 	=  $dim1;
					
					$length 	= $get_id_est[0]->length;
					
					$max_length =  0;
					
					// print_r($ArrInsert);
					
					$this->db->trans_start();
						$this->db->where('id',$id_spool);
						$this->db->update('master_spool', array('max_dim'=>$dim1,'max_length'=>$max_length));
						
						$this->db->insert('master_spool_use', $ArrInsert);
						
						$this->db->where('id',$id_est);
						$this->db->update('scheduling_dropdown_estimasi', array('status'=>'Y'));
					$this->db->trans_complete();
					
					if ($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$Arr_Kembali	= array(
							'pesan'		=>'Process Failed. Please try again later ...',
							'status'	=> 2
						);
					}
					else{
						$this->db->trans_commit();
						$Arr_Kembali	= array(
							'pesan'			=>'Process Success. Thanks ...',
							'status'		=> 1,
							'id_spool'		=> $id_spool,
							'max_dim'		=> number_format($max_dim),
							'max_dim2'		=> $max_dim,
							'dim2x'			=> (int)$dim2,
							'dim1x'			=> (int)$dim1,
							'max_length'	=> number_format($max_length),
							'max_length2'	=> (int)$max_length,
							'length'		=> (int)$length2x
							
						);
						// history('Choose estimasi in scheduling '.$id_est.', spool id '.$id_spool);
					}

					echo json_encode($Arr_Kembali);
				}
			}
			
			if($rest_est[0]->nm_product = 'pipe'){
				$val_length = (!empty($rest_est[0]->max_length))?$rest_est[0]->max_length:0;
				$dim2 		= (!empty($rest_est[0]->d1))?$rest_est[0]->d1:0;
				// echo $id_spool;
				$get_id_est = $this->db->get_where('scheduling_dropdown_estimasi',array('status'=>'N','list_pipe'=>'Y','product'=>$rest_est[0]->nm_product,'diameter'=>$rest_est[0]->d1,'length'=>$rest_est[0]->length_sudut))->result();
				if(!empty($get_id_est)){
					$id_est 	= $get_id_est[0]->id;
					$id_milik = $get_id_est[0]->id_milik;
					// echo $id_spool;
					$length2x =  0;
					if($rest_est[0]->nm_product == 'pipe'){
						$length2x 	= (!empty($rest_est[0]->length_sudut))?$rest_est[0]->length_sudut:0;
					}
					
					$ArrInsert = array(
						'id_spool' => $id_spool,
						'id_milik' => $id_milik,
						'id_use' => $id_est,
						'id_product' => $get_id_est[0]->id_product,
						'length' => $get_id_est[0]->length,
						'created_by' => $data_session['ORI_User']['username'],
						'created_date' => date('Y-m-d H:i:s')
					);
					
					$dim1 		= $get_id_est[0]->diameter;
					$max_dim 	=  $dim1;
					
					$length 	= $get_id_est[0]->length;
					
					$max_length =  0;
					if($rest_est[0]->nm_product == 'pipe'){
						$max_length =  $length + $val_length;
					}
					
					// print_r($ArrInsert);
					
					$this->db->trans_start();
						$this->db->where('id',$id_spool);
						$this->db->update('master_spool', array('max_dim'=>$dim1,'max_length'=>$max_length));
						
						$this->db->insert('master_spool_use', $ArrInsert);
						
						$this->db->where('id',$id_est);
						$this->db->update('scheduling_dropdown_estimasi', array('status'=>'Y'));
					$this->db->trans_complete();
					
					if ($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$Arr_Kembali	= array(
							'pesan'		=>'Process Failed. Please try again later ...',
							'status'	=> 2
						);
					}
					else{
						$this->db->trans_commit();
						$Arr_Kembali	= array(
							'pesan'			=>'Process Success. Thanks ...',
							'status'		=> 1,
							'id_spool'		=> $id_spool,
							'max_dim'		=> number_format($max_dim),
							'max_dim2'		=> $max_dim,
							'dim2x'			=> (int)$dim2,
							'dim1x'			=> (int)$dim1,
							'max_length'	=> number_format($max_length),
							'max_length2'	=> (int)$max_length,
							'length'		=> (int)$length2x
							
						);
						// history('Choose estimasi in scheduling '.$id_est.', spool id '.$id_spool);
					}

					echo json_encode($Arr_Kembali);
				}
			}
		}
	}
	
	public function get_remove_estimasi(){
		$data = $this->input->post();
		
		$id_spool 	= $data['id_val'];
		
		$id_estx 	= $data['data'];
		
		$rest_est 	= $this->db->get_where('master_spool_use', array('id_spool'=>$id_spool))->result_array();
		// $id_est 	= $rest_est[0]->id_use;
		$ArrUpdate = array();
		foreach($rest_est AS $val => $valx){
			$ArrUpdate[$val]['id'] 	= $valx['id_use'];
			$ArrUpdate[$val]['status'] = 'N';
		}
		
		// print_r($id_estx); exit;
		$this->db->trans_start();
			$this->db->where('id',$id_spool);
			$this->db->update('master_spool', array('max_dim'=>0,'max_length'=>0));
			
			$this->db->where('id_spool',$id_spool);
			$this->db->delete('master_spool_use');
			
			// $this->db->where('id',$id_est);
			$this->db->update_batch('scheduling_dropdown_estimasi', $ArrUpdate, 'id');
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1,
				'id_spool'	=> $id_spool,
				'max_dim'	=> 0
			);
			history('Remove estimasi in scheduling, spool id '.$id_spool); 
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function get_product(){
		$product 	= $this->db->get_where('product_parent', array('deleted'=>'N'))->result_array();
		
		$option = "";
		$option .= "<option value='0'>Select Product</option>";
		foreach($product AS $val => $valx){
			$option .= "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>";
		}
		
		$data = array(
			'option' => $option
		);
		
		echo json_encode($data);
	}
	
	public function save_schedule(){
        $data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		if(!empty($data['detail'])){
			$Arr_Detail 	= $data['detail'];
		}
		$no_ipp 		= $data['no_ipp'];
							
		$ArrDetail 		= array();
		if(!empty($data['detail'])){
			foreach($Arr_Detail AS $val => $valx){
				$ArrDetail[$val]['id'] 				= $valx['id'];
				$ArrDetail[$val]['must_finish'] 	= (!empty($valx['must_finish']))?$valx['must_finish']:NULL;
				$ArrDetail[$val]['d1'] 				= str_replace(',','',$valx['d1']);
				$ArrDetail[$val]['d2'] 				= str_replace(',','',$valx['d2']);
				$ArrDetail[$val]['thickness'] 		= str_replace(',','',$valx['thickness']);
				$ArrDetail[$val]['length_sudut'] 	= str_replace(',','',$valx['length_sudut']);
			}
		}
		
		// echo "<pre>";
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
			if(!empty($ArrDetail)){
				$this->db->update_batch('master_spool',$ArrDetail,'id');
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1
			);
			history('Save scheduling master '.$no_ipp);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function proses_split(){
		if($this->input->post()){
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			
			$no_ipp 		= $data['no_ipp'];
			$engine 		= $data['engine'];
			$detail 		= $data['detail'];
			
			// print_r($data); exit;
			
			$ArrDetail = array();
			$ArrDetail2 = array();
			if(!empty($data['detail'])){
				foreach($detail AS $val => $valx){
					foreach($valx['detail'] AS $val2 => $valx2){
						$length = str_replace(',','',$valx2['length']);
						if(!empty($length)){
							$ArrDetail[$val.$val2]['no_ipp'] 			= $data['no_ipp'];
							$ArrDetail[$val.$val2]['id_list'] 			= $valx['id_list'];
							$ArrDetail[$val.$val2]['id_milik'] 			= $valx['id'];
							$ArrDetail[$val.$val2]['product'] 			= $valx['product'];
							$ArrDetail[$val.$val2]['id_product'] 		= $valx['id_product'];
							$ArrDetail[$val.$val2]['diameter'] 			= $valx['diameter'];
							$ArrDetail[$val.$val2]['length'] 			= $length;
							$ArrDetail[$val.$val2]['created_by'] 		= $data_session['ORI_User']['username'];
							$ArrDetail[$val.$val2]['created_date'] 		= $dateTime;
							
							$ArrDetail2[$val.$val2]['no_ipp'] 			= $data['no_ipp'];
							$ArrDetail2[$val.$val2]['id_milik'] 		= $valx['id'];
							$ArrDetail2[$val.$val2]['product'] 			= $valx['product'];
							$ArrDetail2[$val.$val2]['id_product'] 		= $valx['id_product'];
							$ArrDetail2[$val.$val2]['diameter'] 		= $valx['diameter'];
							$ArrDetail2[$val.$val2]['list_pipe'] 		= 'Y';
							$ArrDetail2[$val.$val2]['length'] 			= $length;
							$ArrDetail2[$val.$val2]['created_by'] 		= $data_session['ORI_User']['username'];
							$ArrDetail2[$val.$val2]['created_date'] 	= $dateTime;
						}
					}
				}
			}
			
			$get_pipe = $this->db->get_where('master_spool',array('nm_product'=>'pipe','no_ipp'=>$no_ipp,'status'=>'N'))->result_array();
			$ArrUpdate = array();
			if(!empty($get_pipe)){
				foreach($get_pipe AS $val => $valx){
					$ArrUpdate[$val]['id']			= $valx['id'];
					$ArrUpdate[$val]['max_dim']		= 0;
					$ArrUpdate[$val]['max_length']	= 0;
				}
			
			
				$dtListArray = array();
				foreach($get_pipe AS $val => $valx){
					$dtListArray[$val] = $valx['id'];
				}
				$dtImplode	= "(".implode(",", $dtListArray).")";
			
			}
			// echo $dtImplode."<br>";
			// print_r($ArrUpdate); 
			// exit;
			
			// print_r($ArrDetail);
			// print_r($ArrDetail2);
			// exit;
			
			$this->db->trans_start();
				$this->db->where(array('no_ipp'=>$no_ipp, 'sts_plan'=>'N'));
				$this->db->delete('scheduling_dropdown_split');
				
				$this->db->where(array('no_ipp'=>$no_ipp, 'list_pipe'=>'Y', 'sts_plan'=>'N'));
				$this->db->delete('scheduling_dropdown_estimasi');
				
				if(!empty($ArrDetail2)){
					$this->db->insert_batch('scheduling_dropdown_estimasi',$ArrDetail2);
				}
				if(!empty($ArrDetail)){
					$this->db->insert_batch('scheduling_dropdown_split',$ArrDetail);
				}
				
				if(!empty($ArrUpdate)){
					$this->db->update_batch('master_spool',$ArrUpdate,'id');
					$this->db->query("DELETE FROM master_spool_use WHERE id_spool IN ".$dtImplode." AND sts_plan='N' ");
				}
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Failed. Please try again later ...',
					'status'	=> 2,
					'engine'	=> $engine
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Success. Thanks ...',
					'status'	=> 1,
					'engine'	=> $engine
				);
				history('Split list estimasi pipe '.$no_ipp);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$no_ipp 	= $this->uri->segment(3);
			$detail 	= $this->db->query("SELECT 
												a.*,
												b.thickness
											FROM 
												scheduling_dropdown_estimasi a LEFT JOIN so_bf_detail_header b ON a.id_milik = b.id
											WHERE 
												a.no_ipp='".$no_ipp."' 
												AND a.product='pipe'
												AND a.list_pipe='N'
												")->result_array();
			
			$data = array(
				'title'		=> 'Split Process',
				'action'	=> 'proses_split',
				'no_ipp'	=> $no_ipp,
				'detail'	=> $detail
			);
			
			$this->load->view('Plan_schedule/proses_split', $data);
		}
	}
	
	public function delete_spool_satuan(){
        
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		
		$id 		= $this->uri->segment(3);
		$no_ipp 	= $this->uri->segment(4);

		
		// echo "<pre>";
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('id',$id);
			$this->db->delete('master_spool');
			
			$this->db->where('id_spool',$id);
			$this->db->delete('master_spool_use');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1,
				'no_ipp'	=> $no_ipp
			);
			history('Delete permanent spool '.$id.' / '.$no_ipp);
		}

		echo json_encode($Arr_Kembali);
	} 
	
	public function proses_costcenter(){
		if($this->input->post()){
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			
			$tanda_finish	= $this->uri->segment(3);
			$no_ipp 		= $data['no_ipp'];
			if(!empty($data['detail'])){
				$detail 		= $data['detail'];
			}
			// echo $tanda_finish;
			// print_r($data); exit;
			$ArrDetail = array();
			if(!empty($data['detail'])){
				foreach($detail AS $val => $valx){
					if(!empty($valx['costcenter'])){
						foreach($valx['costcenter'] AS $val2 => $valx2){
							$ArrDetail[$val.$val2]['no_ipp'] 			= $data['no_ipp'];
							$ArrDetail[$val.$val2]['id_milik'] 			= $valx['id_milik'];
							$ArrDetail[$val.$val2]['no_komponen'] 		= $valx['no_komponen'];
							$ArrDetail[$val.$val2]['product'] 			= $valx['product'];
							$ArrDetail[$val.$val2]['id_product'] 		= $valx['id_product'];
							$ArrDetail[$val.$val2]['dimensi'] 			= $valx['dimensi'];
							$ArrDetail[$val.$val2]['qty'] 				= $valx['qty'];
							$ArrDetail[$val.$val2]['id_spool'] 			= $valx['id_spool'];
							$ArrDetail[$val.$val2]['must_finish'] 		= $valx['must_finish'];
							$ArrDetail[$val.$val2]['costcenter'] 		= $valx2;
							$ArrDetail[$val.$val2]['updated_by'] 		= $data_session['ORI_User']['username'];
							$ArrDetail[$val.$val2]['updated_date'] 		= $dateTime;
						}
					}
				}
				foreach($detail AS $val => $valx){
					if(empty($valx['costcenter'])){
						$ArrDetail["000".$val]['no_ipp'] 			= $data['no_ipp'];
						$ArrDetail["000".$val]['id_milik'] 		= $valx['id_milik'];
						$ArrDetail["000".$val]['no_komponen'] 	= $valx['no_komponen'];
						$ArrDetail["000".$val]['product'] 		= $valx['product'];
						$ArrDetail["000".$val]['id_product'] 		= $valx['id_product'];
						$ArrDetail["000".$val]['dimensi'] 		= $valx['dimensi'];
						$ArrDetail["000".$val]['qty'] 			= $valx['qty'];
						$ArrDetail["000".$val]['id_spool'] 		= $valx['id_spool'];
						$ArrDetail["000".$val]['must_finish'] 	= $valx['must_finish'];
						$ArrDetail["000".$val]['costcenter'] 		= NULL;
						$ArrDetail["000".$val]['updated_by'] 		= $data_session['ORI_User']['username'];
						$ArrDetail["000".$val]['updated_date'] 	= $dateTime;
					}
				}
			}
			
			$ArrUpdate = array(
				'status'			=> '8',
				'scheduling_by'		=> $data_session['ORI_User']['username'],
				'scheduling_date'	=> $dateTime
			);
			
			// print_r($ArrDetail);
			// print_r($ArrUpdate);
			// exit;
			
			$this->db->trans_start();
				$this->db->where(array('no_ipp'=>$no_ipp,'sts_plan'=>'N'));
				$this->db->delete('scheduling_data');
				
				if(!empty($ArrDetail)){
					$this->db->insert_batch('scheduling_data',$ArrDetail);
				}
				
				if(!empty($tanda_finish)){
					$this->db->where('no_ipp',$no_ipp);
					$this->db->update('scheduling_produksi', $ArrUpdate);
				}
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Success. Thanks ...',
					'status'	=> 1
				);
				history('Choose costcenter setting '.$no_ipp);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$no_ipp 	= $this->uri->segment(3);
			$sql_detail = "SELECT a.*, MIN(b.must_finish) AS must_finish FROM so_bf_detail_header a LEFT JOIN scheduling_data b ON a.id_milik=b.id_milik WHERE a.id_bq = 'BQ-".$no_ipp."' AND a.id_category <> 'pipe slongsong' GROUP BY a.id_milik ORDER BY a.id_bq_header ASC";
			$detail		= $this->db->query($sql_detail)->result_array();
			
			
			$data = array(
				'title'		=> 'Selected Costcenter',
				'action'	=> 'proses_costcenter',
				'no_ipp'	=> $no_ipp,
				'detail'	=> $detail
			);
			
			$this->load->view('Plan_schedule/proses_costcenter', $data);
		}
	}
	
	public function dropdown_costcenter(){
		$data = $this->input->post();
		$hris = $this->load->database('hris', TRUE);
		$result_query = $hris->select('id, name')->order_by('name', 'ASC')->get_where('departments',array('division_id'=>'DIV009'))->result_array();
		// print_r($result_query);exit;
		$ArrResult = array();
		foreach($result_query AS $val => $valx){
			$ArrResult[$val]['id'] 		= $valx['id'];
			$ArrResult[$val]['text'] 	= strtoupper($valx['name']);
		}
		
		$ArrJSON = array(
			'items' => $ArrResult
		);
		
		echo json_encode($ArrJSON);
	}
	
	
	public function order_produksi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/order_produksi';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$hris = $this->load->database('hris', TRUE);
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$result_query 		= $hris->select('id, name')->order_by('name', 'ASC')->get_where('departments',array('division_id'=>'DIV009'))->result_array();
		
		$sql_ipp 	= "SELECT b.no_ipp, c.project FROM scheduling_data b LEFT JOIN production c ON b.no_ipp=c.no_ipp GROUP BY b.no_ipp ORDER BY b.no_ipp ASC";
		$list_ipp	= $this->db->query($sql_ipp)->result_array(); 
			
		$data = array(
			'title'			=> 'Indeks Of Order Produksi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_ipp'		=> $list_ipp,
			'coctcenter'	=> $result_query
		);
		history('View planning order produksi');
		$this->load->view('Plan_schedule/order_produksi',$data);
	}
	
	public function server_side_order_produksi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/order_produksi";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->quary_data_order_produksi(
			$requestData['costcenter'],
			$requestData['range'],
			$requestData['no_ipp'],
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
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['must_finish']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_ipp'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['dimensi'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['qty'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_spool'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_costcenter'])."</div>";
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

	public function quary_data_order_produksi($costcenter, $range, $no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_cc = "";
        if($costcenter <> '0'){
            $where_cc = "AND a.costcenter = '".$costcenter."' ";
        }
		
		$where_range = "";
        if($range > 0){
			$exP = explode(' - ', $range);
			$date_awal = date('Y-m-d', strtotime($exP[0]));
			$date_akhir = date('Y-m-d', strtotime($exP[1]));
			// echo $exP[0];exit;
            $where_range = "AND DATE(a.must_finish) BETWEEN '".$date_awal."' AND '".$date_akhir."' ";
        }
		
		$where_no_ipp = "";
        if($no_ipp <> '0'){
            $where_no_ipp = "AND a.no_ipp = '".$no_ipp."' ";
        }
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.name AS nm_costcenter
			FROM
				scheduling_data a LEFT JOIN hris.departments b ON a.costcenter=b.id,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_cc." ".$where_range." ".$where_no_ipp."
				AND (
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.name LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'must_finish',
			2 => 'no_ipp',
			3 => 'product',
			4 => 'dimensi',
			5 => 'qty',
			6 => 'id_spool',
			7 => 'costcenter'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function print_order_produksi(){
		$dept     		= $this->uri->segment(3);
		$tgl_awal     	= $this->uri->segment(4);
		$tgl_akhir     	= $this->uri->segment(5);
		$no_ipp     	= $this->uri->segment(6);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$where_cc = "";
        if($dept <> '0'){
            $where_cc = "AND a.costcenter = '".$dept."' ";
        }
		
		$where_range = "";
        if($tgl_awal > 0){
            $where_range = "AND DATE(a.must_finish) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ";
        }
		
		$where_no_ipp = "";
        if($no_ipp <> '0'){
            $where_no_ipp = "AND a.no_ipp = '".$no_ipp."' ";
        }
		
		$sql = "
				SELECT
					a.*,
					b.name AS nm_costcenter
				FROM
					scheduling_data a 
					LEFT JOIN hris.departments b ON a.costcenter=b.id
				WHERE 1=1 
					".$where_cc." ".$where_range." ".$where_no_ipp." ";
		$detail = $this->db->query($sql)->result_array();
		
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'dept' => $dept,
			'tgl_awal' => $tgl_awal,
			'tgl_akhir' => $tgl_akhir,
			'no_ipp' => $no_ipp,
			'detail' => $detail
		);
		
		history('Print Sales Order '.$no_ipp);
		$this->load->view('Print/print_scheduling_request', $data);
	}
	
	public function approve_satuan_product(){
        
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		
		$id_milik 		= $this->uri->segment(3);
		$no_ipp 		= $this->uri->segment(4);
		
		$ArrUpdate = array(
			'sts_plan' => 'Y',
			'plan_by' => $data_session['ORI_User']['username'],
			'plan_date' => $dateTime
		);
		
		//check spool
		$check_spool = $this->db->select('id_spool')->get_where('master_spool_use', array('id_milik'=>$id_milik))->result_array();
		// print_r ($check_spool);
		
		$ArrUpdateSpool = array();
		if(!empty($check_spool)){
			foreach($check_spool AS $val => $valx){
				$ArrUpdateSpool[$val]['id'] = $valx['id_spool'];
				$ArrUpdateSpool[$val]['status'] = 'Y';
			}
		}
		
		// echo $id_milik."<br>";
		// print_r($ArrUpdateSpool);
		// print_r($ArrUpdate);
		// exit;
		
		$this->db->trans_start();
			$this->db->update_batch('master_spool', $ArrUpdateSpool,'id');
			
			$this->db->where('id_milik',$id_milik);
			$this->db->update('so_bf_detail_header', $ArrUpdate);
			
			$this->db->where('id_milik',$id_milik);
			$this->db->update('scheduling_dropdown_estimasi', $ArrUpdate);
			
			$this->db->where('id_milik',$id_milik);
			$this->db->update('scheduling_dropdown_split', $ArrUpdate);
			
			$this->db->where('id_milik',$id_milik);
			$this->db->update('master_spool_use', $ArrUpdate);
			
			$this->db->where('id_milik',$id_milik);
			$this->db->update('scheduling_data', $ArrUpdate);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1,
				'no_ipp'	=> $no_ipp
			);
			history('Approve planning sebagian '.$id_milik);
		}

		echo json_encode($Arr_Kembali);
	} 
	
	public function fd_plus(){
		if($this->input->post()){
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			
			// $no_ipp 		= $data['no_ipp'];
			// $engine 		= $data['engine'];
			// $detail 		= $data['detail'];
			
			print_r($data); exit;
			
			$ArrDetail = array();
			$ArrDetail2 = array();
			if(!empty($data['detail'])){
				foreach($detail AS $val => $valx){
					foreach($valx['detail'] AS $val2 => $valx2){
						$length = str_replace(',','',$valx2['length']);
						if(!empty($length)){
							$ArrDetail[$val.$val2]['no_ipp'] 			= $data['no_ipp'];
							$ArrDetail[$val.$val2]['id_list'] 			= $valx['id_list'];
							$ArrDetail[$val.$val2]['id_milik'] 			= $valx['id'];
							$ArrDetail[$val.$val2]['product'] 			= $valx['product'];
							$ArrDetail[$val.$val2]['id_product'] 		= $valx['id_product'];
							$ArrDetail[$val.$val2]['diameter'] 			= $valx['diameter'];
							$ArrDetail[$val.$val2]['length'] 			= $length;
							$ArrDetail[$val.$val2]['created_by'] 		= $data_session['ORI_User']['username'];
							$ArrDetail[$val.$val2]['created_date'] 		= $dateTime;
							
							$ArrDetail2[$val.$val2]['no_ipp'] 			= $data['no_ipp'];
							$ArrDetail2[$val.$val2]['id_milik'] 		= $valx['id'];
							$ArrDetail2[$val.$val2]['product'] 			= $valx['product'];
							$ArrDetail2[$val.$val2]['id_product'] 		= $valx['id_product'];
							$ArrDetail2[$val.$val2]['diameter'] 		= $valx['diameter'];
							$ArrDetail2[$val.$val2]['list_pipe'] 		= 'Y';
							$ArrDetail2[$val.$val2]['length'] 			= $length;
							$ArrDetail2[$val.$val2]['created_by'] 		= $data_session['ORI_User']['username'];
							$ArrDetail2[$val.$val2]['created_date'] 	= $dateTime;
						}
					}
				}
			}
			
			$get_pipe = $this->db->get_where('master_spool',array('nm_product'=>'pipe','no_ipp'=>$no_ipp,'status'=>'N'))->result_array();
			$ArrUpdate = array();
			if(!empty($get_pipe)){
				foreach($get_pipe AS $val => $valx){
					$ArrUpdate[$val]['id']			= $valx['id'];
					$ArrUpdate[$val]['max_dim']		= 0;
					$ArrUpdate[$val]['max_length']	= 0;
				}
			
			
				$dtListArray = array();
				foreach($get_pipe AS $val => $valx){
					$dtListArray[$val] = $valx['id'];
				}
				$dtImplode	= "(".implode(",", $dtListArray).")";
			
			}
			// echo $dtImplode."<br>";
			// print_r($ArrUpdate); 
			// exit;
			
			// print_r($ArrDetail);
			// print_r($ArrDetail2);
			// exit;
			
			$this->db->trans_start();
				$this->db->where(array('no_ipp'=>$no_ipp, 'sts_plan'=>'N'));
				$this->db->delete('scheduling_dropdown_split');
				
				$this->db->where(array('no_ipp'=>$no_ipp, 'list_pipe'=>'Y', 'sts_plan'=>'N'));
				$this->db->delete('scheduling_dropdown_estimasi');
				
				if(!empty($ArrDetail2)){
					$this->db->insert_batch('scheduling_dropdown_estimasi',$ArrDetail2);
				}
				if(!empty($ArrDetail)){
					$this->db->insert_batch('scheduling_dropdown_split',$ArrDetail);
				}
				
				if(!empty($ArrUpdate)){
					$this->db->update_batch('master_spool',$ArrUpdate,'id');
					$this->db->query("DELETE FROM master_spool_use WHERE id_spool IN ".$dtImplode." AND sts_plan='N' ");
				}
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Failed. Please try again later ...',
					'status'	=> 2,
					'engine'	=> $engine
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Success. Thanks ...',
					'status'	=> 1,
					'engine'	=> $engine
				);
				history('Split list estimasi pipe '.$no_ipp);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$no_ipp 	= $this->uri->segment(3);
			$detail 	= $this->db->query("SELECT 
												a.*,
												b.thickness
											FROM 
												scheduling_dropdown_estimasi a LEFT JOIN so_bf_detail_header b ON a.id_milik = b.id
											WHERE 
												a.no_ipp='".$no_ipp."' 
												AND a.product='pipe'
												AND a.list_pipe='N'
												")->result_array();
			
			$data = array(
				'title'		=> 'Split Process',
				'action'	=> 'proses_split',
				'no_ipp'	=> $no_ipp,
				'detail'	=> $detail
			);
			
			$this->load->view('Plan_schedule/fd_plus', $data);
		}
	}
}