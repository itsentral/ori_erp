<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_control_report extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

    // COST CONTROL
    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$arr_Where			= array('flag_active'=>'1');
		$get_Data			= $this->master_model->getMenu($arr_Where);
		$sales_order			= $this->db->order_by('id_bq','desc')->get_where('so_number',array('id_bq <>'=>'x'))->result_array();
		$material			= $this->db->order_by('nm_material','asc')->get_where('raw_materials',array('delete_date'=>NULL))->result_array();
		$pusat		= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();
		$data = array(
			'title'			=> 'Kebutuhan Pengeluaran Gudang terlihat per SPK',
			'action'		=> 'add',
			'sales_order'	=> $sales_order,
			'pusat'			=> $pusat,
			'material'	=> $material
		);
		$this->load->view('Cost_control_report/index',$data);
	}

	public function get_no_spk(){
		$data       = $this->input->post();
		$no_so   	= $data['no_so'];

        $option = '';
        $get_spk = $this->db->order_by('no_spk')->get_where('so_detail_header',array('id_bq'=>'BQ-'.$no_so,'no_spk !='=>NULL))->result_array();
		if(!empty($get_spk)){
			$option	.= "<option value='0'>PILIH NO SPK</option>";
			foreach ($get_spk as $key => $value) {
				$option	.= "<option value='".$value['id']."/".$value['no_spk']."'>".strtoupper($value['no_spk'])."</option>";
			}
		}
		else{
			$option	.= "<option value='0'>NO SPK BELUM DIBUAT</option>";
		}

        $Arr_Kembali	= array(
            'option' => $option
        );
        echo json_encode($Arr_Kembali);
	}

	public function download_excel(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$sales_order	= $this->uri->segment(3);
		$id_milik		= $this->uri->segment(4);
		$no_spk			= str_replace('-','.',$this->uri->segment(5));
		$tgl_awal		= date('Y-m-d',strtotime($this->uri->segment(6)));
		$tgl_akhir		= date('Y-m-d',strtotime($this->uri->segment(7)));

		$tgl_awal_label		= date('d M Y',strtotime($this->uri->segment(6)));
		$tgl_akhir_label	= date('d M Y',strtotime($this->uri->segment(7)));

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

		$GET_DETAIL_IPP = get_detail_ipp();
		$NO_SO = (!empty($GET_DETAIL_IPP[$sales_order]['so_number']))?$GET_DETAIL_IPP[$sales_order]['so_number']:'';

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'PENGELUARAN GUDANG ('.$tgl_awal_label.' - '.$tgl_akhir_label.')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No. SO.');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $NO_SO);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No. SPK.');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $no_spk);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'TANGGAL');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $tgl_awal_label.' - '.$tgl_akhir_label);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NO TRANSAKSI');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'DATE TRANSAKSI');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'GUDANG DARI');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'GUDANG KE');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'MATERIAL REQUEST');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'MATERIAL AKTUAL');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'ESTIMASI');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'AKTUAL');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$warehousex			= $this->db->order_by('urut','asc')->get('warehouse')->result_array();
		$GET_GUDANG = array();
		foreach ($warehousex as $key => $value) {
			$GET_GUDANG[$value['id']] = strtoupper($value['nm_gudang']);
		}

		$GET_MATERIAL = get_detail_material();

		$result1		= $this->db
							->select('
										a.id_material_req, 
										a.id_material, 
										a.qty_oke, 
										a.qty_est,
										a.check_qty_oke,
										b.id_gudang_dari,
										b.id_gudang_ke,
										b.kode_trans,
										b.created_date
									')
							->from('warehouse_adjustment_detail a')
							->join('warehouse_adjustment b','a.kode_trans=b.kode_trans','left')
							->where('b.no_ipp',$sales_order)
							->where('b.no_spk',$no_spk)
							->where('a.qty_oke >',0)
							->where('DATE(b.checked_date) >=',$tgl_awal)
							->where('DATE(b.checked_date) <=',$tgl_akhir)
							->order_by('a.kode_trans','asc')
							->order_by('a.nm_material','asc')
							->get()
							->result_array();

		$result2		= $this->db->select('
										c.id_material_req, 
										c.id_material, 
										c.qty_oke, 
										c.qty_est, 
										c.check_qty_oke,
										b.id_gudang_dari,
										b.id_gudang_ke,
										b.kode_trans,
										b.created_date
									')
							->from('production_spk a')
							->join('warehouse_adjustment b','a.kode_spk=b.kode_spk','left')
							->join('warehouse_adjustment_detail c','c.kode_trans=b.kode_trans','left')
							->where('a.id_milik',$id_milik)
							->where('c.qty_oke >',0)
							->where('DATE(b.checked_date) >=',$tgl_awal)
							->where('DATE(b.checked_date) <=',$tgl_akhir)
							->order_by('c.kode_trans','asc')
							->order_by('c.nm_material','asc')
							->get()
							->result_array();

		$result3	= $this->db
							->select('
								a.actual_type AS id_material_req, 
								a.actual_type AS id_material, 
								0 AS qty_oke, 
								0 AS qty_est, 
								a.terpakai AS check_qty_oke,
								b.id_gudang_dari,
								b.id_gudang_ke,
								a.kode_trans,
								a.created_date
								')
							->from('production_spk_add_hist a')
							->join('warehouse_adjustment b','a.kode_trans=b.kode_trans','left')
							->join('production_spk c',"a.kode_spk=c.kode_spk AND c.id_milik='".$id_milik."'  AND c.no_ipp='".$sales_order."' ",'left')
							->where('c.no_ipp',$sales_order)
							->where('c.id_milik',$id_milik)
							->where('a.terpakai >',0)
							->where('DATE(a.created_date) >=',$tgl_awal)
							->where('DATE(a.created_date) <=',$tgl_akhir)
							->order_by('a.actual_type','asc')
							->get()
							->result_array();

		$transaksi	= array_merge($result1,$result2,$result3);
		$result = [];
		foreach ($transaksi as $key => $value) {
			$key_uniq = $value['id_material_req'].'-'.$value['kode_trans'];
			$qty_est = (!empty($value['qty_est']))?$value['qty_est']:$value['qty_oke'];
			if(!array_key_exists($key_uniq, $result)) {
				$result[$key_uniq]['qty_oke'] = 0;
				$result[$key_uniq]['check_qty_oke'] = 0;
				$result[$key_uniq]['allowance'] = 0;
			}
			$result[$key_uniq]['id_gudang_dari'] 	= $value['id_gudang_dari'];
			$result[$key_uniq]['id_gudang_ke'] 		= $value['id_gudang_ke'];
			$result[$key_uniq]['kode_trans'] 		= $value['kode_trans'];
			$result[$key_uniq]['created_date'] 		= date('d-M-Y',strtotime($value['created_date']));
			$result[$key_uniq]['id_material_req'] 	= $value['id_material_req'];
			$result[$key_uniq]['id_material']    	= $value['id_material'];
			$result[$key_uniq]['qty_oke']      		+= $qty_est;
			$result[$key_uniq]['check_qty_oke']   	+= $value['check_qty_oke'];
		}


		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$kode_trans		= strtoupper($row_Cek['kode_trans']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$created_date	= strtoupper($row_Cek['created_date']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $created_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$gudang_dari	= (!empty($row_Cek['id_gudang_dari']))?$GET_GUDANG[$row_Cek['id_gudang_dari']]:$row_Cek['kd_gudang_dari'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang_dari);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$gudang_ke		= (!empty($row_Cek['id_gudang_ke']))?$GET_GUDANG[$row_Cek['id_gudang_ke']]:$row_Cek['kd_gudang_ke'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang_ke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_material_req	= $GET_MATERIAL[$row_Cek['id_material_req']]['nm_material'];
				$Cols				= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material_req);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_material	= $GET_MATERIAL[$row_Cek['id_material']]['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$qty_oke		= $row_Cek['qty_oke'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_oke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$check_qty_oke	= $row_Cek['check_qty_oke'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $check_qty_oke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}


		$sheet->setTitle('PENGELUARAN GUDANG');
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
		header('Content-Disposition: attachment;filename="report-costcontrol-pengeluaran-gudang.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	public function download_excel_rekap(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$sales_order	= $this->uri->segment(3);
		$id_milik		= $this->uri->segment(4);
		$no_spk			= str_replace('-','.',$this->uri->segment(5));
		$tgl_awal		= date('Y-m-d',strtotime($this->uri->segment(6)));
		$tgl_akhir		= date('Y-m-d',strtotime($this->uri->segment(7)));

		$tgl_awal_label		= date('d M Y',strtotime($this->uri->segment(6)));
		$tgl_akhir_label	= date('d M Y',strtotime($this->uri->segment(7)));

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

		$GET_DETAIL_IPP = get_detail_ipp();
		$NO_SO = (!empty($GET_DETAIL_IPP[$sales_order]['so_number']))?$GET_DETAIL_IPP[$sales_order]['so_number']:'';

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'PENGELUARAN GUDANG SUMMARY ('.$tgl_awal_label.' - '.$tgl_akhir_label.')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No. SO.');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $NO_SO);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No. SPK.');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $no_spk);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'TANGGAL');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $tgl_awal_label.' - '.$tgl_akhir_label);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'MATERIAL ESTIMASI');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ESTIMASI');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'MATERIAL AKTUAL');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'AKTUAL');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$warehousex			= $this->db->order_by('urut','asc')->get('warehouse')->result_array();
		$GET_GUDANG = array();
		foreach ($warehousex as $key => $value) {
			$GET_GUDANG[$value['id']] = strtoupper($value['nm_gudang']);
		}

		$GET_MATERIAL = get_detail_material();
		$sql="select a.id_material_req AS id_material_req, a.id_material, sum(a.check_qty_oke) check_qty_oke, sum(a.qty_oke) qty_oke, (sum(a.qty_est)/count(a.qty_est)) qty_est from warehouse_adjustment_detail a
		left join warehouse_adjustment b on a.kode_trans=b.kode_trans
		where b.no_ipp ='".$sales_order."' and b.no_spk ='".$no_spk."' and a.qty_oke > 0 and DATE(b.checked_date) >= '".$tgl_awal."'
		and DATE(b.checked_date) <='".$tgl_akhir."'
		group by a.id_material";
		$result1		=$this->db->query($sql)->result_array();

		$sql="select c.id_material_req AS id_material_req, c.id_material, sum(c.check_qty_oke) check_qty_oke, sum(c.qty_oke) qty_oke, sum(c.qty_est) qty_est from production_spk a
		left join warehouse_adjustment b on a.kode_spk=b.kode_spk
		left join warehouse_adjustment_detail c on c.kode_trans=b.kode_trans
		where a.id_milik ='".$id_milik."' and c.qty_oke > 0 and DATE(b.checked_date) >= '".$tgl_awal."'
		and DATE(b.checked_date) <='".$tgl_akhir."'
		group by c.id_material";
		$result2		=$this->db->query($sql)->result_array();

		$sql="select a.actual_type AS id_material_req, a.actual_type AS id_material, sum(a.terpakai) AS check_qty_oke, 0 AS qty_oke, 0 AS qty_est from production_spk_add_hist a
		left join warehouse_adjustment b on a.kode_trans=b.kode_trans
		left join production_spk c on a.kode_spk=c.kode_spk AND c.id_milik='".$id_milik."'  AND c.no_ipp='".$sales_order."'
		where c.no_ipp ='".$sales_order."' and c.id_milik ='".$id_milik."' and a.terpakai > 0 and DATE(a.created_date) >= '".$tgl_awal."' and DATE(a.created_date) <='".$tgl_akhir."'
		group by a.actual_type ";
		$result3		=$this->db->query($sql)->result_array();

		$transaksi	= array_merge($result1,$result2,$result3);
		$result = [];
		foreach ($transaksi as $key => $value) {
			$key_uniq = $value['id_material'];
			$qty_est = (!empty($value['qty_est']))?$value['qty_est']:$value['qty_oke'];
			if(!array_key_exists($key_uniq, $result)) {
				$result[$key_uniq]['check_oke'] = 0;
				$result[$key_uniq]['check_qty_oke'] = 0;
			}
			$result[$key_uniq]['id_material']    	= $value['id_material'];
			$result[$key_uniq]['id_material_req']   = $value['id_material_req'];
			$result[$key_uniq]['check_oke']   		= $qty_est;
			$result[$key_uniq]['check_qty_oke']   	+= $value['check_qty_oke'];
		}

		$GET_EST_SPK = get_estimasi_material_per_spk($id_milik);
		$GET_QTY_SPK = get_detail_final_drawing();
		$QTY_SPK = (!empty($GET_QTY_SPK[$id_milik]['qty']))?$GET_QTY_SPK[$id_milik]['qty']:0;

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$id_material	= $GET_MATERIAL[$row_Cek['id_material_req']]['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray1);

				$awal_col++;
				$check_oke		= (!empty($GET_EST_SPK[$row_Cek['id_material_req']]))?$GET_EST_SPK[$row_Cek['id_material_req']]:0;
				$est_mat = $QTY_SPK * $check_oke;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_mat);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$id_material	= $GET_MATERIAL[$row_Cek['id_material']]['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray1);

				$awal_col++;
				$check_qty_oke	= $row_Cek['check_qty_oke'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $check_qty_oke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}


		$sheet->setTitle('PENGELUARAN GUDANG SUMMARY');
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
		header('Content-Disposition: attachment;filename="report-costcontrol-pengeluaran-gudang-summary.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function show_log(){
		$data			= $this->input->post();
		$gudang 		= $data['gudang'];
		$bulan 			= $data['bulan'];
		$tahun 			= $data['tahun'];
		$sales_order 	= $data['sales_order'];
		$no_spk 		= $data['no_spk'];
		$no_trans 		= $data['no_trans'];

		$WHERE_SO = '';
		if($sales_order != '0'){
			$WHERE_SO = "AND (a.no_ipp = '".$sales_order."' OR d.no_ipp = '".$sales_order."' OR a.no_ipp = 'BQ-".$sales_order."')";
		}

		$WHERE_SPK = '';
		if($no_spk != '0'){
			$EXPLODE = explode('/',$no_spk);
			$WHERE_SPK = "AND (a.no_spk = '".$EXPLODE[1]."' OR d.no_spk = '".$EXPLODE[1]."')";
		}

		$FILTER_NO_TRANS = '';
		if($no_trans != ''){
			$FILTER_NO_TRANS = "AND a.kode_trans LIKE '%".$no_trans."%' ";
		}

		$SQL = "SELECT 
					a.*,
					b.check_qty_oke,
					b.id_material,
					b.nm_material,
					d.no_ipp AS no_ipp_mixing,
					d.no_spk AS no_spk_mixing
				FROM warehouse_adjustment a
				JOIN warehouse_adjustment_detail b on a.kode_trans=b.kode_trans 
				LEFT JOIN production_spk d on a.kode_spk = d.kode_spk
				WHERE 
					a.status_id = '1' 
					AND b.check_qty_oke>0 
					AND a.id_gudang_dari='".$gudang."' 
					AND month(a.checked_date)='".$bulan."' 
					AND year(a.checked_date)='".$tahun."' 
					".$WHERE_SO." 
					".$WHERE_SPK." 
					".$FILTER_NO_TRANS."
				ORDER BY 
					a.no_ipp,a.no_spk,a.checked_date";
		// echo $SQL;
		// exit;
		$result	= $this->db->query($SQL)->result_array();

		$dataArr = [
			'result' => $result,
		];

		$data_html = $this->load->view('Cost_control_report/show_log', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_history(){
		$data 			= $this->input->post();
		$sales_order 	= $data['sales_order'];
		$EXPLODE 		= explode('/',$data['no_spk']);
		$id_milik 		= $EXPLODE[0];
		$no_spk 		= $EXPLODE[1];
		$tgl_awal 		= date('Y-m-d',strtotime($data['tgl_awal']));
		$tgl_akhir 		= date('Y-m-d',strtotime($data['tgl_akhir']));

		$result1		= $this->db
							->select('a.*')
							->from('warehouse_adjustment a')
							->where('a.no_ipp',$sales_order)
							->where('a.no_spk',$no_spk)
							->where('DATE(a.checked_date) >=',$tgl_awal)
							->where('DATE(a.checked_date) <=',$tgl_akhir)
							->get()
							->result_array();

		$result2		= $this->db
							->select('b.*')
							->from('production_spk a')
							->join('warehouse_adjustment b','a.kode_spk=b.kode_spk','left')
							->where('a.id_milik',$id_milik)
							->where('DATE(b.checked_date) >=',$tgl_awal)
							->where('DATE(b.checked_date) <=',$tgl_akhir)
							->get()
							->result_array();

		$result 		= array_merge($result1,$result2);

		$warehousex			= $this->db->order_by('urut','asc')->get('warehouse')->result_array();
		$GET_GUDANG = array();
		foreach ($warehousex as $key => $value) {
			$GET_GUDANG[$value['id']] = strtoupper($value['nm_gudang']);
		}

		$dataArr = [
			'result' => $result,
			'GET_GUDANG' => $GET_GUDANG,
		];

		$data_html = $this->load->view('Cost_control_report/show_transaksi', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_detail_transaksi(){
		$data       = $this->input->post();
		// print_r($data);
		// echo $data['tanda'];
		// exit;
		$kode_trans = $data['kode_trans'];
		$tgl_awal   = date('Y-m-d',strtotime($data['tgl_awal']));
		$tgl_akhir  = date('Y-m-d',strtotime($data['tgl_akhir']));

		// DETAIL TRANSAKSI
		$transaksi1	= $this->db
							->select('
								a.id_material_req,
								a.qty_est,
								a.qty_oke,
								a.id_material,
								a.check_qty_oke
								')
							->from('warehouse_adjustment_detail a')
							->where('a.kode_trans',$kode_trans)
							->where('a.qty_oke >',0)
							// ->where('DATE(a.update_date) >=',$tgl_awal)
							// ->where('DATE(a.update_date) <=',$tgl_akhir)
							->order_by('a.nm_material','asc')
							->get()
							->result_array();
		$transaksi2	= $this->db
							->select('
								a.actual_type AS id_material_req,
								0 AS qty_est,
								0 AS qty_oke,
								a.actual_type AS id_material,
								a.terpakai AS check_qty_oke
								')
							->from('production_spk_add_hist a')
							->where('a.kode_trans',$kode_trans)
							->where('a.terpakai >',0)
							->order_by('a.actual_type','asc')
							->get()
							->result_array();
		$transaksi = array_merge($transaksi1,$transaksi2);
		$temp = [];
		foreach ($transaksi as $key => $value) {
			$key_uniq = $value['id_material_req'];
			$qty_est = (!empty($value['qty_est']))?$value['qty_est']:$value['qty_oke'];
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq]['qty_oke'] = 0;
				$temp[$key_uniq]['check_qty_oke'] = 0;
				$temp[$key_uniq]['allowance'] = 0;
			}
			$temp[$key_uniq]['id_material_req'] = $value['id_material_req'];
			$temp[$key_uniq]['id_material']    	= $value['id_material'];
			$temp[$key_uniq]['qty_oke']      	+= $qty_est;
			$temp[$key_uniq]['check_qty_oke']   += $value['check_qty_oke'];
		}
		$dataArr = [
			'transaksi' 	=> $temp,
			'GET_MATERIAL' 	=> get_detail_material()
		];

		$data_html = $this->load->view('Cost_control_report/show_transaksi_detail', $dataArr, TRUE);
		// print_r($ArrTrans_IN);
		// echo $data_html;
		// exit;
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

    // SUMMARY GUDANG
    public function summary_gudang(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/summary_gudang';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$arr_Where			= array('flag_active'=>'1');
		$get_Data			= $this->master_model->getMenu($arr_Where);
		$warehouse			= $this->db->order_by('urut','asc')->get_where('warehouse',array('status'=>'Y'))->result_array();
		$material			= $this->db->order_by('nm_material','asc')->get_where('raw_materials',array('delete_date'=>NULL))->result_array();
		$data = array(
			'title'			=> 'Warehouse Material >> Summary Material Gudang',
			'action'		=> 'add',
			'warehouse'		=> $warehouse,
			'material'	    => $material
		);
		$this->load->view('Cost_control_report/summary_gudang',$data);
	}

	public function download_excel_summary_gudang(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$warehouse	= $this->uri->segment(3);
		$tgl_awal	= date('Y-m-d',strtotime($this->uri->segment(4)));
		$tgl_akhir	= date('Y-m-d',strtotime($this->uri->segment(5)));

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

		// echo '<pre>';
		// print_r($result);
		// exit;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(12);
		$sheet->setCellValue('A'.$Row, 'REPORT SUMMARY MATERIAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NM MATERIAL');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'IN');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'OUT');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Type');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'Kode Transaksi');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, 'Qty');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);

		$sheet->setCellValue('H'.$NewRow, 'Keterangan');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);

		$sheet->setCellValue('I'.$NewRow, 'Incoming From');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);

		$sheet->setCellValue('J'.$NewRow, 'Outgoing To');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);

		$sheet->setCellValue('K'.$NewRow, 'By');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);

		$sheet->setCellValue('L'.$NewRow, 'Date');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);

		$sheet->setCellValue('M'.$NewRow, 'Sales Order');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);

		$sheet->setCellValue('N'.$NewRow, 'No SPK');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);

		$sheet->setCellValue('O'.$NewRow, 'Product');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);

		$sheet->setCellValue('P'.$NewRow, 'Spec');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);

		$result		= $this->db->group_by('a.id_material')->order_by('a.nm_material')->select('a.*')->get_where('warehouse_history a', array('a.id_gudang'=>$warehouse, 'DATE(a.update_date) >='=>$tgl_awal, 'DATE(a.update_date) <='=>$tgl_akhir))->result_array();
		// TOTAL TRANSAKSI
		$result_in	= $this->db
							->select('SUM(a.jumlah_mat) AS jumlah_material, id_material')
							->from('warehouse_history a')
							->where('a.id_gudang',$warehouse)
							->where('a.jumlah_mat > ',0)
							->where('DATE(a.update_date) >=',$tgl_awal)
							->where('DATE(a.update_date) <=',$tgl_akhir)
							->where('a.kd_gudang_dari <>','BOOKING')
							->group_start()
								->like('a.ket', 'penambahan')
								->or_like('a.ket', 'incoming')
							->group_end()
							->group_by('a.id_material')
							->get()
							->result_array();
		$ArrSumMaterial_IN = [];
		foreach ($result_in as $key => $value) {
			$ArrSumMaterial_IN[$value['id_material']] = $value['jumlah_material'];
		}
		$result_out	= $this->db
							->select('SUM(a.jumlah_mat) AS jumlah_material, id_material')
							->from('warehouse_history a')
							->where('a.id_gudang',$warehouse)
							->where('a.jumlah_mat > ',0)
							->where('DATE(a.update_date) >=',$tgl_awal)
							->where('DATE(a.update_date) <=',$tgl_akhir)
							->where('a.kd_gudang_dari <>','BOOKING')
							->like('a.ket', 'pengurangan')
							->group_by('a.id_material')
							->get()
							->result_array();
		$ArrSumMaterial_OUT = [];
		foreach ($result_out as $key => $value) {
			$ArrSumMaterial_OUT[$value['id_material']] = $value['jumlah_material'];
		}

		$GET_IN_MATERIAL = $ArrSumMaterial_IN;
		$GET_OUT_MATERIAL = $ArrSumMaterial_OUT;
		$GET_WAREHOUSE = get_detail_warehouse();
		$GET_MATERIAL = get_detail_material();
		$GET_IPP = get_detail_ipp();

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $value){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$IN_MATERIAL    = (!empty($GET_IN_MATERIAL[$value['id_material']]))?$GET_IN_MATERIAL[$value['id_material']]:'-';
                $OUT_MATERIAL   = (!empty($GET_OUT_MATERIAL[$value['id_material']]))?$GET_OUT_MATERIAL[$value['id_material']]:'-';
				$NM_MATERIAL   = (!empty($GET_MATERIAL[$value['id_material']]['nm_material']))?$GET_MATERIAL[$value['id_material']]['nm_material']:'-';

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NM_MATERIAL);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $IN_MATERIAL);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $OUT_MATERIAL);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				// for ($i=0; $i < 6; $i++) { 
				// 	$awal_col++;
				// 	$Cols			= getColsChar($awal_col);
				// 	$sheet->setCellValue($Cols.$awal_row, '');
				// 	$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				// }
				
				$material = $value['id_material'];
				$transaksi_out	= $this->db
								->select('SUM(a.jumlah_mat) AS jumlah_material, no_ipp AS kode_trans, id_material, nm_material, update_by, update_date, ket, "OUT" AS type, id_gudang_dari, kd_gudang_dari, id_gudang_ke, kd_gudang_ke')
								->from('warehouse_history a')
								->where('a.id_gudang',$warehouse)
								->where('a.id_material',$material)
								->where('a.jumlah_mat > ',0)
								->where('DATE(a.update_date) >=',$tgl_awal)
								->where('DATE(a.update_date) <=',$tgl_akhir)
								->where('a.kd_gudang_dari <>','BOOKING')
								->like('a.ket', 'pengurangan')
								->group_by('a.id_material')
								->group_by('a.no_ipp')
								->get()
								->result_array();
		
				$transaksi_in	= $this->db
								->select('SUM(a.jumlah_mat) AS jumlah_material, no_ipp AS kode_trans, id_material, nm_material, update_by, update_date, ket, "IN" AS type, id_gudang_dari, kd_gudang_dari, id_gudang_ke, kd_gudang_ke')
								->from('warehouse_history a')
								->where('a.id_gudang',$warehouse)
								->where('a.id_material',$material)
								->where('a.jumlah_mat > ',0)
								->where('DATE(a.update_date) >=',$tgl_awal)
								->where('DATE(a.update_date) <=',$tgl_akhir)
								->where('a.kd_gudang_dari <>','BOOKING')
								->group_start()
								->like('a.ket', 'penambahan')
								->or_like('a.ket', 'incoming')
								->group_end()
								->group_by('a.id_material')
								->group_by('a.no_ipp')
								->get()
								->result_array();

				$transaksi = array_merge($transaksi_out,$transaksi_in);

				foreach ($transaksi as $key2 => $value2) {
					$awal_row++;
					$awal_col	= 0;

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$NM_MATERIAL   = (!empty($GET_MATERIAL[$value2['id_material']]['nm_material']))?$GET_MATERIAL[$value2['id_material']]['nm_material']:'-';

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $NM_MATERIAL);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$type	= strtoupper($value2['type']);
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $type);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$kode_trans	= strtoupper($value2['kode_trans']);
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $kode_trans);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$jumlah_material	= $value2['jumlah_material'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $jumlah_material);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

					$awal_col++;
					$ket	= $value2['ket'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $ket);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$KD_GUDANG_DARI = $value2['kd_gudang_dari'];
					if(!empty($value2['id_gudang_dari'])){
						$KD_GUDANG_DARI = (!empty($GET_WAREHOUSE[$value2['id_gudang_dari']]['nm_gudang']))?$GET_WAREHOUSE[$value2['id_gudang_dari']]['nm_gudang']:'';
					}

					$KD_GUDANG_KE = $value2['kd_gudang_ke'];
					if(!empty($value2['id_gudang_ke'])){
						$KD_GUDANG_KE = (!empty($GET_WAREHOUSE[$value2['id_gudang_ke']]['nm_gudang']))?$GET_WAREHOUSE[$value2['id_gudang_ke']]['nm_gudang']:'';
					}

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, strtoupper($KD_GUDANG_DARI));
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, strtoupper($KD_GUDANG_KE));
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$update_by	= $value2['update_by'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $update_by);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$update_date	= $value2['update_date'];
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $update_date);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$kode_spk_explode = explode('/',$value2['kode_trans']);
					$getTransaction = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$value2['kode_trans']))->result_array();
					$no_ipp 	= (!empty($getTransaction[0]['no_ipp']))?str_replace('BQ-','',$getTransaction[0]['no_ipp']):'-';
					$no_spk 	= (!empty($getTransaction[0]['no_spk']))?$getTransaction[0]['no_spk']:'-';
					$kode_spk 	= (!empty($getTransaction[0]['kode_spk']))?$getTransaction[0]['kode_spk']:$kode_spk_explode[0];

					$getKodeSPK = $this->db->get_where('production_spk',array('kode_spk'=>$kode_spk))->result_array();
					$no_spkKPK	= (!empty($getKodeSPK[0]['no_spk']))?$getKodeSPK[0]['no_spk']:'-';
					$no_ippKPK	= (!empty($getKodeSPK[0]['no_ipp']))?$getKodeSPK[0]['no_ipp']:'-';

					$no_spk_fix = (!empty($getTransaction[0]['no_spk']))?$getTransaction[0]['no_spk']:$no_spkKPK;
					$no_ipp_fix = (!empty($getTransaction[0]['no_ipp']) AND $getTransaction[0]['no_ipp'] != 'resin mixing')?$no_ipp:$no_ippKPK;

					$no_so    	= (!empty($GET_IPP[$no_ipp_fix]['so_number']))?$GET_IPP[$no_ipp_fix]['so_number']:$no_ipp_fix;

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $no_so);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $no_spk_fix);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$getProduct 	= $this->db->get_where('so_detail_header',array('no_spk'=>$no_spk_fix))->result_array();
					$product_name 	= (!empty($getProduct[0]['id_category']))?$getProduct[0]['id_category']:'-';
					$spec 			= (!empty($getProduct[0]['id']))?spec_bq2($getProduct[0]['id']):'-';

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $product_name);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols			= getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $spec);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);


				}
				
			

			}
		}


		$sheet->setTitle('SUMMARY');
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
		header('Content-Disposition: attachment;filename="summary-material.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function show_history_summary_gudang(){
		$data       = $this->input->post();
		$warehouse  = $data['warehouse'];
		$tgl_awal   = date('Y-m-d',strtotime($data['tgl_awal']));
		$tgl_akhir  = date('Y-m-d',strtotime($data['tgl_akhir']));

		$result		= $this->db->group_by('a.id_material')->order_by('a.nm_material')->select('a.*')->get_where('warehouse_history a', array('a.id_gudang'=>$warehouse, 'DATE(a.update_date) >='=>$tgl_awal, 'DATE(a.update_date) <='=>$tgl_akhir))->result_array();
		// TOTAL TRANSAKSI
		$result_in	= $this->db
							->select('SUM(a.jumlah_mat) AS jumlah_material, id_material')
							->from('warehouse_history a')
							->where('a.id_gudang',$warehouse)
							->where('DATE(a.update_date) >=',$tgl_awal)
							->where('DATE(a.update_date) <=',$tgl_akhir)
							->where('a.kd_gudang_dari <>','BOOKING')
							->group_start()
							->like('a.ket', 'penambahan')
							->or_like('a.ket', 'incoming')
							->group_end()
							->group_by('a.id_material')
							->get()
							->result_array();
		$ArrSumMaterial_IN = [];
		foreach ($result_in as $key => $value) {
			$ArrSumMaterial_IN[$value['id_material']] = $value['jumlah_material'];
		}
		$result_out	= $this->db
							->select('SUM(a.jumlah_mat) AS jumlah_material, id_material')
							->from('warehouse_history a')
							->where('a.id_gudang',$warehouse)
							->where('DATE(a.update_date) >=',$tgl_awal)
							->where('DATE(a.update_date) <=',$tgl_akhir)
							->where('a.kd_gudang_dari <>','BOOKING')
							->like('a.ket', 'pengurangan')
							->group_by('a.id_material')
							->get()
							->result_array();
		$ArrSumMaterial_OUT = [];
		foreach ($result_out as $key => $value) {
			$ArrSumMaterial_OUT[$value['id_material']] = $value['jumlah_material'];
		}

		$dataArr = [
			'result' 			=> $result,
			'get_in_material' 	=> $ArrSumMaterial_IN,
			'get_out_material' 	=> $ArrSumMaterial_OUT
		];

		$data_html = $this->load->view('Cost_control_report/show_history_summary_gudang', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_history_summary_gudang_detail(){
		$data       = $this->input->post();
		// print_r($data);
		// echo $data['tanda'];
		// exit;
		$tanda  	= $data['tanda'];
		$warehouse  = $data['warehouse'];
		$material  	= $data['material'];
		$tgl_awal   = date('Y-m-d',strtotime($data['tgl_awal']));
		$tgl_akhir  = date('Y-m-d',strtotime($data['tgl_akhir']));

		$like1 = 'penambahan';
		$like2 = 'incoming';
		if($tanda == 'out'){
			$like1 = 'pengurangan';
			$like2 = 'pengurangan';
			$transaksi	= $this->db
							->select('SUM(a.jumlah_mat) AS jumlah_material, no_ipp AS kode_trans, id_material, nm_material, update_by, update_date, ket')
							->from('warehouse_history a')
							->where('a.id_gudang',$warehouse)
							->where('a.id_material',$material)
							->where('DATE(a.update_date) >=',$tgl_awal)
							->where('DATE(a.update_date) <=',$tgl_akhir)
							->where('a.kd_gudang_dari <>','BOOKING')
							->like('a.ket', $like1)
							->group_by('a.id_material')
							->group_by('a.no_ipp')
							->get()
							->result_array();
		}
		else{
			$transaksi	= $this->db
							->select('SUM(a.jumlah_mat) AS jumlah_material, no_ipp AS kode_trans, id_material, nm_material, update_by, update_date, ket')
							->from('warehouse_history a')
							->where('a.id_gudang',$warehouse)
							->where('a.id_material',$material)
							->where('DATE(a.update_date) >=',$tgl_awal)
							->where('DATE(a.update_date) <=',$tgl_akhir)
							->where('a.kd_gudang_dari <>','BOOKING')
							->group_start()
							->like('a.ket', $like1)
							->or_like('a.ket', $like2)
							->group_end()
							->group_by('a.id_material')
							->group_by('a.no_ipp')
							->get()
							->result_array();
		}

		// DETAIL TRANSAKSI
		// $transaksi	= $this->db
		// 					->select('SUM(a.jumlah_mat) AS jumlah_material, no_ipp AS kode_trans, id_material, nm_material, update_by, update_date, ket')
		// 					->from('warehouse_history a')
		// 					->where('a.id_gudang',$warehouse)
		// 					->where('a.id_material',$material)
		// 					->where('DATE(a.update_date) >=',$tgl_awal)
		// 					->where('DATE(a.update_date) <=',$tgl_akhir)
		// 					->like('a.ket', $like1)
		// 					->or_like('a.ket', $like2)
		// 					->group_by('a.id_material')
		// 					->group_by('a.no_ipp')
		// 					->get()
		// 					->result_array();
		$ArrTrans_IN = [];
		foreach ($transaksi as $key => $value) {
			$ArrTrans_IN[$value['id_material']][] = $value;
		}
		$dataArr = [
			'get_in_trans' 	=> $ArrTrans_IN,
			'material' 		=> $material
		];

		$data_html = $this->load->view('Cost_control_report/show_history_summary_gudang_detail', $dataArr, TRUE);
		// print_r($ArrTrans_IN);
		// echo $data_html;
		// exit;
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function download_excel_sub(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$sales_order	= $this->uri->segment(3);
		$id_milik		= $this->uri->segment(4);
		$no_spk			= str_replace('-','.',$this->uri->segment(5));
		$bulan		= sprintf('%02s',$this->uri->segment(6));
		$tahun		= date('Y',strtotime($this->uri->segment(7)));

		echo $this->uri->segment(6).'/';
		// echo $bulan;
		// exit;

		$periode		= date('M-Y',strtotime($tahun.'-'.$bulan.'-01'));

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

		$GET_DETAIL_IPP = get_detail_ipp();
		$NO_SO = (!empty($GET_DETAIL_IPP[$sales_order]['so_number']))?$GET_DETAIL_IPP[$sales_order]['so_number']:'';

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'PENGELUARAN GUDANG SUB GUDANG');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No. SO.');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $NO_SO);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'No. SPK.');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $no_spk);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, 'Periode');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, $periode);
		$sheet->getStyle('B'.$NewRow.':C'.$NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(10);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NO SALES ORDER');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'NO SPK');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'NO TRANSAKSI');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'DATE INPUT');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'MATERIAL');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'ESTIMASI');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'AKTUAL');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		// $sheet->setCellValue('I'.$NewRow, 'AKTUAL');
		// $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		// $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		// $sheet->getColumnDimension('I')->setWidth(10);

		$warehousex			= $this->db->order_by('urut','asc')->get('warehouse')->result_array();
		$GET_GUDANG = array();
		foreach ($warehousex as $key => $value) {
			$GET_GUDANG[$value['id']] = strtoupper($value['nm_gudang']);
		}

		$GET_MATERIAL = get_detail_material();

		$WHERE_SO = '';
		if($sales_order != '0'){
			$WHERE_SO = "AND (a.no_ipp = '".$sales_order."' OR d.no_ipp = '".$sales_order."' OR a.no_ipp = 'BQ-".$sales_order."')";
		}

		$WHERE_SPK = '';
		if($no_spk != '0'){
			$EXPLODE = str_replace('/','.',$no_spk);
			$WHERE_SPK = "AND (a.no_spk = '".$EXPLODE."' OR d.no_spk = '".$EXPLODE."')";
		}

		$FILTER_NO_TRANS = '';
		// if($no_trans != ''){
		// 	$FILTER_NO_TRANS = "AND a.kode_trans LIKE '%".$no_trans."%' ";
		// }

		$SQL = "SELECT 
					a.*,
					b.qty_order AS qty_est,
					b.check_qty_oke,
					b.id_material,
					b.nm_material,
					d.no_ipp AS no_ipp_mixing,
					d.no_spk AS no_spk_mixing
				FROM warehouse_adjustment a
				JOIN warehouse_adjustment_detail b on a.kode_trans=b.kode_trans 
				LEFT JOIN production_spk d on a.kode_spk = d.kode_spk
				WHERE 
					a.status_id = '1' 
					AND b.check_qty_oke>0 
					AND month(a.checked_date)='".$bulan."' 
					AND year(a.checked_date)='".$tahun."' 
					".$WHERE_SO." 
					".$WHERE_SPK." 
					".$FILTER_NO_TRANS."
				ORDER BY 
					a.no_ipp,a.no_spk,a.checked_date";
		// echo $SQL;
		// exit;
		$result	= $this->db->query($SQL)->result_array();

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $NO_SO);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$EXPLODE = str_replace('/','.',$no_spk);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $EXPLODE);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_trans		= strtoupper($row_Cek['kode_trans']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$created_date	= strtoupper($row_Cek['created_date']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $created_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray);

				$awal_col++;
				$nm_material	= strtoupper($row_Cek['nm_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$qty_est		= $row_Cek['qty_est'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_est);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$check_qty_oke	= $row_Cek['check_qty_oke'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $check_qty_oke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}


		$sheet->setTitle('PENGELUARAN SUB GUDANG');
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
		header('Content-Disposition: attachment;filename="pengeluaran-sub-gudang-'.$NO_SO.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}