<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_tracking extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('report_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	//IN OUT MATERIAL
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$list_ipp			= $this->db
									->select('a.no_ipp, b.project, b.nm_customer')
									->order_by('a.no_ipp','desc')
									->join('production b','a.no_ipp=b.no_ipp','join')
									->get_where('so_header a',array('a.cancel_date'=>NULL))->result_array();
		$data = array(
			'title'			=> 'Report Tracking',
			'action'		=> 'index',
			'list_ipp'		=> $list_ipp
		);
		$this->load->view('Report_tracking/index',$data);
	}

	public function download_excel(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$warehouse	= $this->uri->segment(3);
		$material	= $this->uri->segment(4);
		$tgl_awal	= date('Y-m-d',strtotime($this->uri->segment(5)));
		$tgl_akhir	= date('Y-m-d',strtotime($this->uri->segment(6)));

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

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		if($material != '0' AND $warehouse != '0'){
			$result		= $this->db->get_where('warehouse_history', array('id_material'=>$material, 'id_gudang'=>$warehouse, 'DATE(update_date) >='=>$tgl_awal, 'DATE(update_date) <='=>$tgl_akhir))->result_array();
		}
		if($material != '0' AND $warehouse == '0'){
			$result		= $this->db->get_where('warehouse_history', array('id_material'=>$material, 'DATE(update_date) >='=>$tgl_awal, 'DATE(update_date) <='=>$tgl_akhir))->result_array();
		}
		if($material == '0' AND $warehouse != '0'){
			$result		= $this->db->get_where('warehouse_history', array('id_gudang'=>$warehouse, 'DATE(update_date) >='=>$tgl_awal, 'DATE(update_date) <='=>$tgl_akhir))->result_array();
		}
		if($material == '0' AND $warehouse == '0'){
			$result		= $this->db->get_where('warehouse_history', array('DATE(update_date) >='=>$tgl_awal, 'DATE(update_date) <='=>$tgl_akhir))->result_array();
		}

		// echo '<pre>';
		// print_r($result);
		// exit;

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(13);
		$sheet->setCellValue('A'.$Row, 'REPORT IN-OUT MATERIAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'NO DOKUMEN');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ID PROGRAM');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'ID BARANG');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'NM MATERIAL');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'GUDANG');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'GUDANG DARI');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'GUDANG KE');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'QTY');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$sheet->setCellValue('J'.$NewRow, 'STOK AWAL');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(10);

		$sheet->setCellValue('K'.$NewRow, 'STOK AKHIR');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(10);

		$sheet->setCellValue('L'.$NewRow, 'KETERANGAN');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(10);

		$sheet->setCellValue('M'.$NewRow, 'TANGGAL');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setWidth(10);

		$warehousex			= $this->db->order_by('urut','asc')->get('warehouse')->result_array();
		$GET_GUDANG = array();
		foreach ($warehousex as $key => $value) {
			$GET_GUDANG[$value['id']] = strtoupper($value['nm_gudang']);
		}	
		// echo '<pre>';	
		// print_r($GET_GUDANG); 
		// echo $GET_GUDANG['16JSON'];
		// exit;

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
				$NO_TRANSX = $row_Cek['no_ipp'];
				$EXPLODE = explode('/',$row_Cek['no_ipp']);
				if(!empty($EXPLODE[1])){
				$GET_KODE = $this->db->get_where('warehouse_adjustment',array('kode_spk'=>$EXPLODE[0],'created_date'=>$EXPLODE[1]))->result();
				$NO_TRANSX = (!empty($GET_KODE[0]->kode_trans))?$GET_KODE[0]->kode_trans:$row_Cek['no_ipp'];
				}
				$no_trans	=$NO_TRANSX;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_trans);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_material	= strtoupper($row_Cek['id_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$idmaterial	= strtoupper($row_Cek['idmaterial']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $idmaterial);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_material	= strtoupper($row_Cek['nm_material']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_material);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$gudang		= $GET_GUDANG[$row_Cek['id_gudang']];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$gudang_dari= (!empty($row_Cek['id_gudang_dari']))?$GET_GUDANG[$row_Cek['id_gudang_dari']]:$row_Cek['kd_gudang_dari'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang_dari);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$gudang_ke		= (!empty($row_Cek['id_gudang_ke']))?$GET_GUDANG[$row_Cek['id_gudang_ke']]:$row_Cek['kd_gudang_ke'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $gudang_ke);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$jumlah_mat		= $row_Cek['jumlah_mat'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $jumlah_mat);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$qty_stock_awal		= $row_Cek['qty_stock_awal'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock_awal);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$qty_stock_akhir		= $row_Cek['qty_stock_akhir'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty_stock_akhir);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$ket		= $row_Cek['ket'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $ket);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$update_date		= $row_Cek['update_date'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $update_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}


		$sheet->setTitle('HISTORY IN OUT');
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
		header('Content-Disposition: attachment;filename="HISTORY IN OUT MATERIAL '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function show_history(){
		$data = $this->input->post();
		$no_ipp = 'BQ-'.$data['no_ipp'];
		
		$result		= $this->db
							->select('a.*,
								b.no_po,
								c.so_number AS no_so,
								d.total_deal_idr AS deal_idr,
								f.kode_spk,
								f.qty AS qty_mix,
								g.qty AS qty_parsial,
								g.created_date AS mixing_uniq,
								h.material AS wip_material,
								h.wip_direct AS wip_direct,
								h.wip_indirect AS wip_indirect,
								h.wip_consumable AS wip_consumable,
								h.wip_foh AS wip_foh,
								h.nilai_wip AS wip_total,
								i.est_material,
								i.real_material,
								j.nilai_unit AS nilai_fg,
								k.nilai_unit AS nilai_intransit,
								l.nilai_unit AS nilai_incustomer,
								k.kode_delivery
								')
							->order_by('a.id','asc')
							->join('billing_so b','REPLACE(a.id_bq,"BQ-","")=b.no_ipp','left')
							->join('so_number c','a.id_bq=c.id_bq','left')
							->join('so_bf_detail_header e','a.id_milik=e.id','left')
							->join('billing_so_product d','e.id_milik=d.id_milik','left')
							->join('production_spk f','a.id=f.id_milik','left')
							->join('production_spk_parsial g','f.id=g.id_spk AND g.spk="1"','left')
							->join('data_erp_wip_group h','CONCAT(f.kode_spk,"/",g.created_date)=h.kode_trans AND h.jenis="in"','left')
							->join('laporan_wip_per_hari_action i','CONCAT(f.kode_spk,"/",g.created_date)=i.kode_trans','left')
							->join('data_erp_fg j','h.id_trans=j.id_trans AND h.jenis="in" AND j.jenis="in"','left')
							->join('data_erp_in_transit k','j.id_trans=k.id_trans AND j.jenis="in" AND k.jenis="in" AND j.id_pro=k.id_pro_det','left')
							->join('data_erp_in_customer l','l.id_trans=k.id_trans AND l.jenis="in" AND k.jenis="in" AND l.id_pro=k.id_pro_det','left')
							->get_where('so_detail_header a', array('a.id_bq'=>$no_ipp))
							->result_array();

		$dataArr = [
			'result' => $result
		];

		$data_html = $this->load->view('Report_tracking/show_history', $dataArr, TRUE);

		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}


}
?>