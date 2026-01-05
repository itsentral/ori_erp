<?php
class Warehouse_stock_tras extends CI_Controller {

	public function __construct() {
		parent::__construct();
        $this->load->model('master_model');
		$this->folder		= 'Stock_tras/';
		$this->accounting	= $this->load->database('gl', TRUE);
		// $controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)).'/'.strtolower($this->uri->segment(3)));
		// $this->Arr_Akses	= getAcccesmenu($controller);
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$this->arr_bulan	= array(1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		
	
	}
	
	function index(){
		
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$Query_COA			= "SELECT coa_1 FROM warehouse WHERE NOT(coa_1 IS NULL OR coa_1 = '' OR coa_1 ='-') GROUP BY coa_1";
		$rows_COA			= $this->db->query($Query_COA)->result();
		
		$data = array(
			'title'			=> 'DAFTAR STOCK',
			'action'		=> 'index',
			'rows_coa'		=> $rows_COA,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view($this->folder.'v_warehouse_stock',$data);
		
	}
	
	function GetTablePrice($Jenis_Gudang = ''){
		$Table_Gudang		= 'price_book';
		if(strtolower($Jenis_Gudang) == 'subgudang'){
			$Table_Gudang		= 'price_book_subgudang';
		}else if(strtolower($Jenis_Gudang) == 'produksi'){
			$Table_Gudang		= 'price_book_produksi';
		}else if(strtolower($Jenis_Gudang) == 'project'){
			$Table_Gudang		= 'price_book_project';
		}
		
		return $Table_Gudang;
	}
	
	function SubQueryStock($WHERE_Sub = ""){
		$WHERE_Find_Sub	= "1=1";
		if($WHERE_Sub){
			if(!empty($WHERE_Find_Sub))$WHERE_Find_Sub	.=" AND ";
			$WHERE_Find_Sub	.=$WHERE_Sub;
		}
		
		$Query_Sub	= "SELECT
							tras_stock.id_material,
							tras_stock.id_gudang,
							MAX(tras_stock.id) AS last_kode,
							head_whr.coa_1,
							head_whr.nm_gudang,
							head_whr.category AS category_gudang
						FROM
							tran_warehouse_jurnal_detail tras_stock
							LEFT JOIN warehouse head_whr ON tras_stock.id_gudang=head_whr.id
						WHERE
							".$WHERE_Find_Sub."
						GROUP BY
							tras_stock.id_material,
							tras_stock.id_gudang";
		return	$Query_Sub;
		
	}
	
	public function display_data(){		
        
		$rows_Gudang	= $this->master_model->getArray('warehouse',array(),'id','category');
        $Sub_Find		= "NOT(head_whr.coa_1 IS NULL OR head_whr.coa_1 ='' OR head_whr.coa_1 ='-')";	
		$WHERE			= "1=1";
		$requestData	= $_REQUEST;
		
		$Coa_Cari		= trim($this->input->post('no_perkiraan'));
		$Date_Find		= trim($this->input->post('tanggal'));
		
		
		
		if(empty($Date_Find) || $$Date_Find == '-'){			
			$Date_Find	= date('Y-m-d');
		}
		
		if($Date_Find){
			if(!empty($Sub_Find))$Sub_Find	.=" AND ";
			$Sub_Find	.="DATE(tras_stock.tgl_trans ) <= '".$Date_Find."'";
		}
		
		if($Coa_Cari){
			if(!empty($Sub_Find))$Sub_Find	.=" AND ";
			$Sub_Find	.="head_whr.coa_1 IN('".$Coa_Cari."')";
		}
		
		
		$Query_Sub_Find		= $this->SubQueryStock($Sub_Find);
		
		
		//echo"<pre>";print_r($Arr_Akses);
		
		$like_value     = $requestData['search']['value'];
        $column_order   = $requestData['order'][0]['column'];
        $column_dir     = $requestData['order'][0]['dir'];
        $limit_start    = $requestData['start'];
        $limit_length   = $requestData['length'];
		
		$columns_order_by = array(
			1 => 'head_mstr.idmaterial',
			2 => 'head_mstr.nm_material',
			3 => 'head_mstr.nm_category',
			4 => 'det_stock.nm_gudang',
			5 => 'head_stock.qty_stock_akhir',
			6 => 'head_stock.harga',
			7 => 'head_stock.nilai_akhir_rp'
		);
		
		
		
		
		
		if($like_value){
			if(!empty($WHERE))$WHERE	.=" AND ";
			$WHERE	.="(
						head_mstr.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR head_mstr.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR det_stock.nm_gudang LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR head_mstr.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR head_stock.qty_stock_akhir LIKE '%".$this->db->escape_like_str($like_value)."%'
						/*
						OR head_stock.harga LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR head_stock.nilai_akhir_rp LIKE '%".$this->db->escape_like_str($like_value)."%'
						*/
						)";
		}
		
		
		
		
		$sql = "SELECT
					head_mstr.idmaterial,
					head_mstr.id_material,
					head_mstr.nm_material,
					head_mstr.nm_category,
					head_stock.qty_stock_awal,
					head_stock.qty_in,
					head_stock.qty_out,
					head_stock.qty_stock_akhir,
					head_stock.harga,
					head_stock.harga_bm,
					head_stock.nilai_akhir_rp,
					head_stock.nilai_awal_rp,
					head_stock.nilai_trans_rp,
					det_stock.id_gudang,
					det_stock.nm_gudang,
					(@row:=@row+1) AS urut
				FROM
					tran_warehouse_jurnal_detail head_stock
				INNER JOIN (
					".$Query_Sub_Find."
				)det_stock ON head_stock.id=det_stock.last_kode
				LEFT JOIN raw_materials head_mstr ON head_stock.id_material = head_mstr.id_material,
				(SELECT @row:=0) r 
				WHERE ".$WHERE;
		//print_r($sql);exit();
		$fetch['totalData'] 	= $this->db->query($sql)->num_rows();
		$fetch['totalFiltered']	= $this->db->query($sql)->num_rows();

		

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		if($limit_length > 0){
			$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		}

		$fetch['query'] = $this->db->query($sql);
		
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		
		$data		= array();
        $urut1  	= 1;
        $urut2  	= 0;
		$Periode_Now= date('Y-m');
		$Tahun_Now	= date('Y');
		
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
			
			
			$Code_Material		= $row['id_material'];
			$Code_MaterialReal	= $row['idmaterial'];
			$Name_Material		= $row['nm_material'];
			$Cat_Material		= $row['nm_category'];
			$Code_Gudang		= $row['id_gudang'];
			$Name_Gudang		= $row['nm_gudang'];
			
			$Nilai_HPP			= (!empty($row['harga']) && floatval($row['harga']) !== 0)?$row['harga']:0;
			$SaldoAwal_HPP		= (!empty($row['nilai_awal_rp']) && floatval($row['nilai_awal_rp']) !== 0)?$row['nilai_awal_rp']:0;
			$SaldoAkhir_HPP		= (!empty($row['nilai_akhir_rp']) && floatval($row['nilai_akhir_rp']) !== 0)?$row['nilai_akhir_rp']:0;
			$Total_Trans		= (!empty($row['nilai_trans_rp']) && floatval($row['nilai_trans_rp']) !== 0)?$row['nilai_trans_rp']:0;
			$Qty_Awal			= (!empty($row['qty_stock_awal']) && floatval($row['qty_stock_awal']) !== 0)?$row['qty_stock_awal']:0;
			$Qty_In				= (!empty($row['qty_in']) && floatval($row['qty_in']) !== 0)?$row['qty_in']:0;
			$Qty_Out			= (!empty($row['qty_out']) && floatval($row['qty_out']) !== 0)?$row['qty_out']:0;
			$Qty_Akhir			= (!empty($row['qty_stock_akhir']) && floatval($row['qty_stock_akhir']) !== 0)?$row['qty_stock_akhir']:0;
			
			$Code_Unik			= $Code_Material.'^_^'.$Code_Gudang.'^_^'.$Date_Find;
			
			$Template_Material	= '<a href="#" class="text-red" onClick = "ActionPreviewDetail({code:\''.$Code_Unik.'\',action :\'preview_detail_stock\',title:\'VIEW DETAIL STOCK\'});" title="VIEW DETAIL STOCK"> '.$Code_MaterialReal.' </a>';
			
			$Jenis_Gudang		= '';			
			if(isset($rows_Gudang[$Code_Gudang]) && !empty($rows_Gudang[$Code_Gudang])){
				$Jenis_Gudang	= $rows_Gudang[$Code_Gudang];
			}
			
			$Harga_HPP			= $Nilai_HPP;
			// if((floatval($Qty_Akhir) > 0 || floatval($Qty_Akhir) < 0) && (floatval($SaldoAkhir_HPP) > 0 || floatval($SaldoAkhir_HPP) < 0)){
				// $Harga_HPP		= $SaldoAkhir_HPP / $Qty_Akhir;
			// }
			
			//echo"<br> Qty : ".$Qty_Akhir." Harga : ".$Harga_HPP." Total : ".$SaldoAkhir_HPP;
			
			
			$nestedData 	= array(); 
			$nestedData[]	= $nomor;
			$nestedData[]	= $Template_Material;
			$nestedData[]	= $Name_Material;
			$nestedData[]	= $Cat_Material;
			$nestedData[]	= $Name_Gudang;
			$nestedData[]	= number_format($Qty_Akhir,4);
			$nestedData[]	= number_format($Harga_HPP,2);
			$nestedData[]	= number_format($SaldoAkhir_HPP,2);		
			$nestedData[]	= "<button type='button' class='btn btn-sm btn-warning look_history' title='History' data-nm_material='".strtoupper($row['nm_material'])."' data-id_material='".$row['id_material']."' data-id_gudang='".$row['id_gudang']."'><i class='fa fa-history'></i></button>";			
			
			
			$data[] 		= $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),  
			"recordsTotal"    => intval( $totalData ),  
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data
		);

		echo json_encode($json_data);
    }
	
	function preview_detail_stock(){
		$rows_Header = $rows_Detail = $rows_Price = array();
		$Code_Material	= $Code_Gudang = $Date_Find = '';
		if($this->input->post()){  
			$Code_Split			= explode('^_^',$this->input->post('code'));
			$Code_Material		= $Code_Split[0];
			$Code_Gudang		= $Code_Split[1];
			$Date_Find			= $Code_Split[2];
			
			$Sub_Find			= "NOT(head_whr.coa_1 IS NULL OR head_whr.coa_1 ='' OR head_whr.coa_1 ='-')";
			$WHERE_Hist			= "1=1";
			
			
			if(empty($Date_Find)){
				$Date_Find	= date('Y-m-d');
			}
			
			if($Date_Find){
				if(!empty($Sub_Find))$Sub_Find	.=" AND ";
				$Sub_Find	.="DATE(tras_stock.tgl_trans ) <= '".$Date_Find."'";
			}
			
			
			
			/*
			if(!empty($WHERE_Hist))$WHERE_Hist	.=" AND ";
			$WHERE_Hist	.="DATE(tgl_trans) <= '".$Date_Find."'";
			*/
			
			if($Code_Gudang){
				if(!empty($Sub_Find))$Sub_Find	.=" AND ";
				$Sub_Find	.="tras_stock.id_gudang = '".$Code_Gudang."'";
				
				if(!empty($WHERE_Hist))$WHERE_Hist	.=" AND ";
				$WHERE_Hist	.="id_gudang = '".$Code_Gudang."'";
			}
			
			if($Code_Material){
				if(!empty($Sub_Find))$Sub_Find	.=" AND ";
				$Sub_Find	.="tras_stock.id_material = '".$Code_Material."'";
				
				if(!empty($WHERE_Hist))$WHERE_Hist	.=" AND ";
				$WHERE_Hist	.="id_material = '".$Code_Material."'";
			}
			
			$Query_Sub_Find		= $this->SubQueryStock($Sub_Find);
			
			$Query_Stock		= "SELECT
										head_mstr.idmaterial,
										head_mstr.id_material,
										head_mstr.nm_material,
										head_mstr.nm_category,
										head_stock.qty_stock_awal,
										head_stock.qty_in,
										head_stock.qty_out,
										head_stock.qty_stock_akhir,
										head_stock.harga,
										head_stock.harga_bm,
										head_stock.nilai_akhir_rp,
										head_stock.nilai_awal_rp,
										head_stock.nilai_trans_rp,
										det_stock.id_gudang,
										det_stock.nm_gudang,
										det_stock.category_gudang
									FROM
										tran_warehouse_jurnal_detail head_stock
									INNER JOIN (
										".$Query_Sub_Find."
									)det_stock ON head_stock.id=det_stock.last_kode
									LEFT JOIN raw_materials head_mstr ON head_stock.id_material = head_mstr.id_material";
			
			$rows_Header		= $this->db->query($Query_Stock)->row();
			
			$Query_Hist			= "SELECT * FROM tran_warehouse_jurnal_detail WHERE ".$WHERE_Hist." ORDER BY tgl_trans DESC";
			$rows_Detail		= $this->db->query($Query_Hist)->result();
			
			
			
		}
		
		$data			= array(
			'title'			=> 'PREVIEW DETAIL STOCK',
			'action'		=> 'preview_detail_stock',
			'category'		=> 'view',
			'rows_header'	=> $rows_Header,
			'rows_detail'	=> $rows_Detail,
			'code_material'	=> $Code_Material,
			'code_gudang'	=> $Code_Gudang,
			'tgl_cari'		=> $Date_Find,
			'rows_price'	=> $rows_Price
		);		
		$this->load->view($this->folder.'v_warehouse_stock_preview', $data);
	}
	
	function preview_detail_stock_trans(){
		$rows_Header	= $rows_Detail	= array();
		$Name_View		= 'v_warehouse_stock_reff';
		$Title_Jurnal	= 'PREVIEW DETAIL HISTORI STOCK - TRANSAKSI';
		if($this->input->post()){
			$Code_Find	= $this->input->post('code');
			$Split_Find	= explode('^_^',$Code_Find);
			
			$Nomor_Trans	= $Split_Find[0];
			$Code_Gudang	= $Split_Find[1];
			
			$rows_Header	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$Nomor_Trans))->row();
			
			$Query_Detail	= "SELECT * FROM tran_warehouse_jurnal_detail WHERE kode_trans = '".$Nomor_Trans."' AND id_gudang = '".$Code_Gudang."'";
			
			$rows_Detail	= $this->db->query($Query_Detail)->result_array();
		}
		
		$data		= array(
			'title'			=> $Title_Jurnal,
			'rows_header'	=> $rows_Header,
			'rows_detail'	=> $rows_Detail
		);
		
		$this->load->view($this->folder.$Name_View, $data);
	}
	
	function preview_detail_jurnal_trans(){
		$rows_Header	= $rows_Detail	= array();
		$Name_View		= 'v_warehouse_stock_jurnal';
		$Title_Jurnal	= 'PREVIEW DETAIL JURNAL MATERIAL';
		$Tipe_Jurnal	= 'jv';
		if($this->input->post()){
			
			$Nomor_Jurnal	= $this->input->post('code');
			$rows_Detail	= $this->accounting->get_where('jurnal',array('nomor'=>$Nomor_Jurnal))->result_array();
			if($rows_Detail){
				$Tipe_Jurnal	= $rows_Detail[0]['tipe'];
			}
			
			
			
			if(strtolower($Tipe_Jurnal) == 'jv'){
				$rows_Header	= $this->accounting->get_where('javh',array('nomor'=>$Nomor_Jurnal))->row();
				$Title_Jurnal	= 'PREVIEW DETAIL JURNAL VOUCHER';
			}else if(strtolower($Tipe_Jurnal) == 'bum'){
				$rows_Header	= $this->accounting->get_where('jarh',array('nomor'=>$Nomor_Jurnal))->row();
				$Title_Jurnal	= 'PREVIEW DETAIL JURNAL BUM';
			}else if(strtolower($Tipe_Jurnal) == 'buk'){
				$rows_Header	= $this->accounting->get_where('japh',array('nomor'=>$Nomor_Jurnal))->row();
				$Title_Jurnal	= 'PREVIEW DETAIL JURNAL BUK';
			}
		}
		
		$data		= array(
			'title'			=> $Title_Jurnal,
			'rows_header'	=> $rows_Header,
			'rows_detail'	=> $rows_Detail,
			'arr_month'		=> $this->arr_bulan,
			'tipe_jurnal'	=> $Tipe_Jurnal
		);
		
		$this->load->view($this->folder.$Name_View, $data);
	}
    
    public function ExcelGudangStok(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		
		$rows_Gudang	= $this->master_model->getArray('warehouse',array(),'id','category');
        $Sub_Find		= "NOT(head_whr.coa_1 IS NULL OR head_whr.coa_1 ='' OR head_whr.coa_1 ='-')";	
		$WHERE			= "1=1";	
		
		$Coa_Find		= urldecode($this->input->get('coa'));
		$Date_Find		= urldecode($this->input->get('tgl'));
		$Categori_Find	= urldecode($this->input->get('category'));
		
		// print_r($Date_Find);
		// exit;
		
		
		$Judul			= 'REPORT MATERIAL STOCK - TRAS';
		$Arr_Bulan		= array(1=>'January','February','March','April','May','June','July','August','September','October','November','December');
		
		
		if(!empty($Date_Find)){			
			$Date_Find	= date('Y-m-d');
		}
		
		if($Date_Find){
			if(!empty($Sub_Find))$Sub_Find	.=" AND ";
			$Sub_Find	.="DATE(tras_stock.tgl_trans ) <= '".$Date_Find."'";
		}
		
		$Periode_Cari		= date('d-m-Y',strtotime($Date_Find));
		$Coa_Cari			= 'All COA Gudang';
		
		if($Coa_Find){
			if(!empty($Sub_Find))$Sub_Find	.=" AND ";
			$Sub_Find	.="head_whr.coa_1 IN('".$Coa_Find."')";
			
			$Query_COA		= "SELECT nama FROM COA WHERE no_perkiraan = '".$Coa_Find."' ORDER BY id DESC LIMIT 1";
			$rows_COA		= $this->accounting->query($Query_COA)->row();
			if($rows_COA){
				$Coa_Cari	=$Coa_Find.' | '.$rows_COA->nama;
			}
		}
		
		$Query_Sub_Find		= $this->SubQueryStock($Sub_Find);
		
		$sql = "SELECT
					head_mstr.idmaterial,
					head_mstr.id_material,
					head_mstr.nm_material,
					head_mstr.nm_category,
					head_stock.qty_stock_awal,
					head_stock.qty_in,
					head_stock.qty_out,
					head_stock.qty_stock_akhir,
					head_stock.harga,
					head_stock.harga_bm,
					head_stock.nilai_akhir_rp,
					head_stock.nilai_awal_rp,
					head_stock.nilai_trans_rp,
					det_stock.id_gudang,
					det_stock.nm_gudang
				FROM
					tran_warehouse_jurnal_detail head_stock
				INNER JOIN (
					".$Query_Sub_Find."
				)det_stock ON head_stock.id=det_stock.last_kode
				LEFT JOIN raw_materials head_mstr ON head_stock.id_material = head_mstr.id_material
				ORDER BY head_mstr.nm_material ASC
				";
		
		
		
		$record			= $this->db->query($sql)->result_array();
		
		
		$Title				= 'Material Stock';
		
		
		$this->load->library("PHPExcel");
        $objPHPExcel = new PHPExcel();
		
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
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
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
		 
		$sheet 		= $objPHPExcel->getActiveSheet();
		 
		$Arr_Judul	= array(
			'No',
			'ID Material',
			'Nama Material',
			'Categori',
			'Warehouse',
			'Qty Stock',
			'Price Book',
			'Total Value'
		);
		
		$Judul_Length	= count($Arr_Judul);
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= getColsChar($Judul_Length);
		$sheet->setCellValue('A'.$Row, $Judul);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		## PERIODE ##
		$NewRow	= $NewRow +2;
		$sheet->setCellValue('A'.$NewRow, 'Periode Stock');
		$sheet->getStyle('A'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('B'.$NewRow, ':');
		$sheet->getStyle('B'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('C'.$NewRow, $Periode_Cari);
		$sheet->getStyle('C'.$NewRow)->applyFromArray($styleArray3);
		
		## NO PERKIRAAN ##
		$NewRow++;
		$sheet->setCellValue('A'.$NewRow, 'No Perkiraan');
		$sheet->getStyle('A'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('B'.$NewRow, ':');
		$sheet->getStyle('B'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('C'.$NewRow, $Coa_Cari);
		$sheet->getStyle('C'.$NewRow)->applyFromArray($styleArray3);
		
		
		
		
		$NextRow= $NewRow +1;
		$Mula_Col = 0;
		foreach($Arr_Judul as $keyJ=>$valJ){
			$Mula_Col++;
			$Col_Name	= getColsChar($Mula_Col);
			$sheet->setCellValue($Col_Name.$NextRow, $valJ);
			$sheet->getStyle($Col_Name.$NextRow)->applyFromArray($style_header);
		}
		
		$Grand_Total	= $Total_Qty;			
		if($record){
			$Next_Row		= $NextRow;
			$intL			= 0;
			foreach($record as $keys=>$row){
				$Next_Row++;
				$intL++;
				$Mula_Col = 0;
				
				$Code_Material		= $row['id_material'];
				$Code_MaterialReal	= $row['idmaterial'];
				$Name_Material		= $row['nm_material'];
				$Cat_Material		= $row['nm_category'];
				$Code_Gudang		= $row['id_gudang'];
				$Name_Gudang		= $row['nm_gudang'];
				
				$Nilai_HPP			= (!empty($row['harga']) && floatval($row['harga']) !== 0)?$row['harga']:0;
				$SaldoAwal_HPP		= (!empty($row['nilai_awal_rp']) && floatval($row['nilai_awal_rp']) !== 0)?$row['nilai_awal_rp']:0;
				$SaldoAkhir_HPP		= (!empty($row['nilai_akhir_rp']) && floatval($row['nilai_akhir_rp']) !== 0)?$row['nilai_akhir_rp']:0;
				$Total_Trans		= (!empty($row['nilai_trans_rp']) && floatval($row['nilai_trans_rp']) !== 0)?$row['nilai_trans_rp']:0;
				$Qty_Awal			= (!empty($row['qty_stock_awal']) && floatval($row['qty_stock_awal']) !== 0)?$row['qty_stock_awal']:0;
				$Qty_In				= (!empty($row['qty_in']) && floatval($row['qty_in']) !== 0)?$row['qty_in']:0;
				$Qty_Out			= (!empty($row['qty_out']) && floatval($row['qty_out']) !== 0)?$row['qty_out']:0;
				$Qty_Akhir			= (!empty($row['qty_stock_akhir']) && floatval($row['qty_stock_akhir']) !== 0)?$row['qty_stock_akhir']:0;
				
				
				$Jenis_Gudang		= '';			
				if(isset($rows_Gudang[$Code_Gudang]) && !empty($rows_Gudang[$Code_Gudang])){
					$Jenis_Gudang	= $rows_Gudang[$Code_Gudang];
				}
				
				$Harga_HPP			= $Nilai_HPP;
				// if((floatval($Qty_Akhir) > 0 || floatval($Qty_Akhir) < 0) && (floatval($SaldoAkhir_HPP) > 0 || floatval($SaldoAkhir_HPP) < 0)){
					// $Harga_HPP		= $SaldoAkhir_HPP / $Qty_Akhir;
				// }
				
				$Temp_Loop			= array($intL,$Code_Material,$Name_Material,$Cat_Material,$Name_Gudang,number_format($Qty_Akhir,4),number_format($Harga_HPP,2),number_format($SaldoAkhir_HPP,2));
				
				foreach($Temp_Loop as $KeyLoop=>$valLoop){
					$Mula_Col++;				
					$Cols		= getColsChar($Mula_Col);
					$sheet->setCellValue($Cols.$Next_Row, $valLoop);
					$sheet->getStyle($Cols.$Next_Row)->applyFromArray($styleArray2);
				}
				
				$Grand_Total	+=$SaldoAkhir_HPP;
				$Total_Qty		+=$Qty_Akhir;
				
			}
			
			$Next_Row++;
			$sheet->setCellValue('A'.$Next_Row, 'Grand Total');
			$sheet->getStyle('A'.$Next_Row.':G'.$Next_Row)->applyFromArray($style_header);
			$sheet->mergeCells('A'.$Next_Row.':G'.$Next_Row);
			
			
			$sheet->setCellValue('H'.$Next_Row, number_format($Grand_Total,2));
			$sheet->getStyle('H'.$Next_Row)->applyFromArray($style_header);
			
			
			
		}
		
		
		
		$sheet->setTitle($Title);
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
		header('Content-Disposition: attachment;filename="Report_Stock_Material_Tras_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
		exit;
	}
	
	function ExcelStockCompare(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		
		$rows_Gudang	= $this->master_model->getArray('warehouse',array(),'id','category');
        $Sub_Find		= "NOT(head_whr.coa_1 IS NULL OR head_whr.coa_1 ='' OR head_whr.coa_1 ='-')";	
		$WHERE			= "1=1";	
		$WHERE2			= "NOT(head_whr.coa_1 IS NULL OR head_whr.coa_1 ='' OR head_whr.coa_1 ='-') AND head_stock.qty_stock <> 0";
		
		$Coa_Find		= urldecode($this->input->get('coa'));
		$Date_Find		= urldecode($this->input->get('tgl'));
		$Categori_Find	= urldecode($this->input->get('category'));
		
			
		$Judul			= 'REPORT MATERIAL STOCK';
		$Arr_Bulan		= array(1=>'January','February','March','April','May','June','July','August','September','October','November','December');
		
		if(empty($Date_Find)){			
			$Date_Find	= date('Y-m-d');
		}
		
		$Table_Stock	= "warehouse_stock head_stock";
		if($Date_Find < date('Y-m-d')){
			$Table_Stock	= "warehouse_stock_per_day head_stock";
			
			if(!empty($WHERE2))$WHERE2	.=" AND ";
			$WHERE2	.="DATE(head_stock.hist_date) = '".$Date_Find."'";
		}
		
		
		if($Date_Find){
			if(!empty($Sub_Find))$Sub_Find	.=" AND ";
			$Sub_Find	.="DATE(tras_stock.tgl_trans ) <= '".$Date_Find."'";
		}
		
		$Periode_Cari		= date('d-m-Y',strtotime($Date_Find));
		$Coa_Cari			= 'All COA Gudang';
		
		if($Coa_Find){
			if(!empty($Sub_Find))$Sub_Find	.=" AND ";
			$Sub_Find	.="head_whr.coa_1 IN('".$Coa_Find."')";
			
			$Query_COA		= "SELECT nama FROM COA WHERE no_perkiraan = '".$Coa_Find."' ORDER BY id DESC LIMIT 1";
			$rows_COA		= $this->accounting->query($Query_COA)->row();
			if($rows_COA){
				$Coa_Cari	=$Coa_Find.' | '.$rows_COA->nama;
			}
			
			if(!empty($WHERE2))$WHERE2	.=" AND ";
			$WHERE2	.="head_whr.coa_1 IN('".$Coa_Find."')";
		}
		
		$Temp_Compare		= array();
		$Query_Compare 		= "SELECT
									head_mstr.idmaterial,
									head_mstr.id_material,
									head_mstr.nm_material,
									head_mstr.nm_category,
									head_stock.qty_stock,
									head_stock.qty_booking,
									head_stock.qty_rusak,
									head_stock.id_gudang,
									head_whr.nm_gudang
								FROM
									".$Table_Stock."
								LEFT JOIN warehouse head_whr ON head_stock.id_gudang=head_whr.id
								LEFT JOIN raw_materials head_mstr ON head_stock.id_material = head_mstr.id_material
								WHERE ".$WHERE2."
								ORDER BY head_stock.nm_material ASC
								";
		$rows_Compare		= $this->db->query($Query_Compare)->result_array();
		if($rows_Compare){
			foreach($rows_Compare as $keyComp=>$valComp){
				$Code_WHComp	= $valComp['id_gudang'];
				$Code_MatComp	= $valComp['id_material'];
				$Code_UnitComp	= $Code_WHComp.'^_^'.$Code_MatComp;
				$Temp_Compare[$Code_UnitComp]	= $valComp;
			}
			
			unset($rows_Compare);
		}
		
		//echo"<pre>";print_r($Query_Compare);exit;
		
		$Query_Sub_Find		= $this->SubQueryStock($Sub_Find);
		
		$sql = "SELECT
					head_mstr.idmaterial,
					head_mstr.id_material,
					head_mstr.nm_material,
					head_mstr.nm_category,
					head_stock.qty_stock_awal,
					head_stock.qty_in,
					head_stock.qty_out,
					head_stock.qty_stock_akhir,
					head_stock.harga,
					head_stock.harga_bm,
					head_stock.nilai_akhir_rp,
					head_stock.nilai_awal_rp,
					head_stock.nilai_trans_rp,
					det_stock.id_gudang,
					det_stock.nm_gudang
				FROM
					tran_warehouse_jurnal_detail head_stock
				INNER JOIN (
					".$Query_Sub_Find."
				)det_stock ON head_stock.id=det_stock.last_kode
				LEFT JOIN raw_materials head_mstr ON head_stock.id_material = head_mstr.id_material
				ORDER BY head_mstr.nm_material ASC
				";
		
		
		
		$record			= $this->db->query($sql)->result_array();
		
		
		$Title				= 'Material Stock';
		
		
		$this->load->library("PHPExcel");
        $objPHPExcel = new PHPExcel();
		
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
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
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
		  
		  $styleArray4 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  ),
			  'font'  => array(
					'color' => array('rgb' => 'FF0000')
			  )
		  );
		 
		$sheet 		= $objPHPExcel->getActiveSheet();
		 
		$Arr_Judul	= array(
			'No',
			'ID Material',
			'Nama Material',
			'Categori',
			'Warehouse'
		);
		
		$Judul_Length	= count($Arr_Judul) + 8;
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= getColsChar($Judul_Length);
		$sheet->setCellValue('A'.$Row, $Judul);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		## PERIODE ##
		$NewRow	= $NewRow +2;
		$sheet->setCellValue('A'.$NewRow, 'Periode Stock');
		$sheet->getStyle('A'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('B'.$NewRow, ':');
		$sheet->getStyle('B'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('C'.$NewRow, $Periode_Cari);
		$sheet->getStyle('C'.$NewRow)->applyFromArray($styleArray3);
		
		## NO PERKIRAAN ##
		$NewRow++;
		$sheet->setCellValue('A'.$NewRow, 'No Perkiraan');
		$sheet->getStyle('A'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('B'.$NewRow, ':');
		$sheet->getStyle('B'.$NewRow)->applyFromArray($styleArray3);
		
		$sheet->setCellValue('C'.$NewRow, $Coa_Cari);
		$sheet->getStyle('C'.$NewRow)->applyFromArray($styleArray3);
		
		
		
		
		$NextRow	= $NewRow +1;
		$NextRow2	= $NextRow +1;
		$Mula_Col = 0;
		foreach($Arr_Judul as $keyJ=>$valJ){
			$Mula_Col++;
			$Col_Name	= getColsChar($Mula_Col);
			$sheet->setCellValue($Col_Name.$NextRow, $valJ);
			$sheet->getStyle($Col_Name.$NextRow.':'.$Col_Name.$NextRow2)->applyFromArray($style_header);
			$sheet->mergeCells($Col_Name.$NextRow.':'.$Col_Name.$NextRow2);
		}
		
		$Mulai_Next		= $Mula_Col + 1;
		$Mulai_Next2 	= $Mulai_Next + 2;
		
		$Col_Name	= getColsChar($Mulai_Next);
		$Col_Name2	= getColsChar($Mulai_Next2);
		
			
		$Mulai_Next++;
		
		
		
		$sheet->setCellValue($Col_Name.$NextRow, 'Stock');
		$sheet->getStyle($Col_Name.$NextRow.':'.$Col_Name2.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells($Col_Name.$NextRow.':'.$Col_Name2.$NextRow);
		
		$sheet->setCellValue($Col_Name.$NextRow2, 'Qty');
		$sheet->getStyle($Col_Name.$NextRow2)->applyFromArray($style_header);


		$Mulai_Next++;
		$Col_Name	= getColsChar($Mulai_Next);
		$sheet->setCellValue($Col_Name.$NextRow2, 'Harga');
		$sheet->getStyle($Col_Name.$NextRow2)->applyFromArray($style_header);
		
		
		$Col_Name	= getColsChar($Mulai_Next);
		$sheet->setCellValue($Col_Name.$NextRow2, 'Total');
		$sheet->getStyle($Col_Name.$NextRow2)->applyFromArray($style_header);
		
	
		
		
		
		
		
		
		$Grand_Total	= $Total_Qty;			
		if($record){
			$Next_Row		= $NextRow2;
			$intL			= 0;
			foreach($record as $keys=>$row){
				$Next_Row++;
				$intL++;
				$Mula_Col = 0;
				
				$Code_Material		= $row['id_material'];
				$Code_MaterialReal	= $row['idmaterial'];
				$Name_Material		= $row['nm_material'];
				$Cat_Material		= $row['nm_category'];
				$Code_Gudang		= $row['id_gudang'];
				$Name_Gudang		= $row['nm_gudang'];
				
				$Nilai_HPP			= (!empty($row['harga']) && floatval($row['harga']) !== 0)?$row['harga']:0;
				$SaldoAwal_HPP		= (!empty($row['nilai_awal_rp']) && floatval($row['nilai_awal_rp']) !== 0)?$row['nilai_awal_rp']:0;
				$SaldoAkhir_HPP		= (!empty($row['nilai_akhir_rp']) && floatval($row['nilai_akhir_rp']) !== 0)?$row['nilai_akhir_rp']:0;
				$Total_Trans		= (!empty($row['nilai_trans_rp']) && floatval($row['nilai_trans_rp']) !== 0)?$row['nilai_trans_rp']:0;
				$Qty_Awal			= (!empty($row['qty_stock_awal']) && floatval($row['qty_stock_awal']) !== 0)?$row['qty_stock_awal']:0;
				$Qty_In				= (!empty($row['qty_in']) && floatval($row['qty_in']) !== 0)?$row['qty_in']:0;
				$Qty_Out			= (!empty($row['qty_out']) && floatval($row['qty_out']) !== 0)?$row['qty_out']:0;
				$Qty_Akhir			= (!empty($row['qty_stock_akhir']) && floatval($row['qty_stock_akhir']) !== 0)?$row['qty_stock_akhir']:0;
				
				
				$Jenis_Gudang		= '';			
				if(isset($rows_Gudang[$Code_Gudang]) && !empty($rows_Gudang[$Code_Gudang])){
					$Jenis_Gudang	= $rows_Gudang[$Code_Gudang];
				}
				
				$Harga_HPP			= $Nilai_HPP;
				// if((floatval($Qty_Akhir) > 0 || floatval($Qty_Akhir) < 0) && (floatval($SaldoAkhir_HPP) > 0 || floatval($SaldoAkhir_HPP) < 0)){
					// $Harga_HPP		= $SaldoAkhir_HPP / $Qty_Akhir;
				// }
				
				$Code_UnitFind		= $Code_Gudang.'^_^'.$Code_Material;
				$Qty_Temp	= $Harga_Temp = $Total_Temp = 0;
				if(isset($Temp_Compare[$Code_UnitFind]) && !empty($Temp_Compare[$Code_UnitFind])){
					$Qty_Temp	= $Temp_Compare[$Code_UnitFind]['qty_stock'];
					
					## AMBIL HARGA ##
					$Jenis_Gudang		= '';			
					if(isset($rows_Gudang[$Code_Gudang]) && !empty($rows_Gudang[$Code_Gudang])){
						$Jenis_Gudang	= $rows_Gudang[$Code_Gudang];
					}
					
					$Table_Price		= $this->GetTablePrice($Jenis_Gudang);
					$Query_Price		= "SELECT * FROM ".$Table_Price." WHERE id_material = '".$Code_Material."' AND DATE(updated_date) <= '".$Date_Find."' ORDER BY id DESC LIMIT 1";
					$rows_Price			= $this->db->query($Query_Price)->row();
					if($rows_Price){
						if(empty($rows_Price->price_book) || floatval($rows_Price->price_book) > 0){
							$Harga_Temp		= $rows_Price->price_book;
						}
						
					}
					
					$Total_Temp = $Harga_HPP * $Qty_Temp;
					
					unset($Temp_Compare[$Code_UnitFind]);
				}
				
				$Selisih_Qty		= $Qty_Akhir - $Qty_Temp; //$Qty_Akhir - $Qty_Temp;
				$Selisih_Total		= $SaldoAkhir_HPP - $Total_Temp; //$SaldoAkhir_HPP - $Total_Temp;
				
				$Fix_Style			= $styleArray2;
				if($Selisih_Qty > 0 || $Selisih_Qty < 0  || $Selisih_Total > 0 || $Selisih_Total < 0){
					$Fix_Style			= $styleArray4;
				}
				
				
				$Temp_Loop			= array($intL,$Code_Material,$Name_Material,$Cat_Material,$Name_Gudang,$Qty_Temp,$Harga_HPP,$Total_Temp);
				
				foreach($Temp_Loop as $KeyLoop=>$valLoop){
					$Mula_Col++;				
					$Cols		= getColsChar($Mula_Col);
					$sheet->setCellValue($Cols.$Next_Row, $valLoop);
					$sheet->getStyle($Cols.$Next_Row)->applyFromArray($Fix_Style);
				}
				
				$Grand_Total	+=$SaldoAkhir_HPP;
				$Total_Qty		+=$Qty_Akhir;

				$Grand_Total2	+=$Total_Temp;
				$Total_Qty2		+=$Qty_Temp;
				
			}
			
			## ANTISIPASI JIKA DI TRAS TIDAK ADA TAPI DI STOCK ADA ##
		/*	if($Temp_Compare){
				foreach($Temp_Compare as $keySisa=>$valSisa){
					$Next_Row++;
					$intL++;
					$Mula_Col = 0;
					
					$Code_Material		= $valSisa['id_material'];
					$Code_MaterialReal	= $valSisa['idmaterial'];
					$Name_Material		= $valSisa['nm_material'];
					$Cat_Material		= $valSisa['nm_category'];
					$Code_Gudang		= $valSisa['id_gudang'];
					$Name_Gudang		= $valSisa['nm_gudang'];
					$Qty_Temp			= $valSisa['qty_stock'];
					
					$Qty_Akhir = $Harga_HPP	= $SaldoAkhir_HPP = $Harga_Temp = $Total_Temp = 0;
					
					## AMBIL HARGA ##
					$Jenis_Gudang		= '';			
					if(isset($rows_Gudang[$Code_Gudang]) && !empty($rows_Gudang[$Code_Gudang])){
						$Jenis_Gudang	= $rows_Gudang[$Code_Gudang];
					}
					
					$Table_Price		= $this->GetTablePrice($Jenis_Gudang);
					$Query_Price		= "SELECT * FROM ".$Table_Price." WHERE id_material = '".$Code_Material."' AND DATE(updated_date) <= '".$Date_Find."' ORDER BY id DESC LIMIT 1";
					$rows_Price			= $this->db->query($Query_Price)->row();
					if($rows_Price){
						if(empty($rows_Price->price_book) || floatval($rows_Price->price_book) > 0){
							$Harga_Temp		= $rows_Price->price_book;
						}
						
					}
					
					$Total_Temp = $Harga_Temp * $Qty_Temp;
					
					$Selisih_Qty		= $Qty_Akhir - $Qty_Temp;
					$Selisih_Total		= $SaldoAkhir_HPP - $Total_Temp;
					
					$Fix_Style			= $styleArray2;
					if($Selisih_Qty > 0 || $Selisih_Qty < 0  || $Selisih_Total > 0 || $Selisih_Total < 0){
						$Fix_Style			= $styleArray4;
					}
					
					
					$Temp_Loop			= array($intL,$Code_Material,$Name_Material,$Cat_Material,$Name_Gudang,$Qty_Akhir,$Harga_HPP,$SaldoAkhir_HPP,$Qty_Temp,$Harga_Temp,$Total_Temp,$Selisih_Qty,$Selisih_Total);
					
					foreach($Temp_Loop as $KeyLoop=>$valLoop){
						$Mula_Col++;				
						$Cols		= getColsChar($Mula_Col);
						$sheet->setCellValue($Cols.$Next_Row, $valLoop);
						$sheet->getStyle($Cols.$Next_Row)->applyFromArray($Fix_Style);
					}
					
					$Grand_Total	+=$SaldoAkhir_HPP;
					$Total_Qty		+=$Qty_Akhir;
					
					
				}
			}*/
			
			/*
			$Next_Row++;
			$sheet->setCellValue('A'.$Next_Row, 'Grand Total');
			$sheet->getStyle('A'.$Next_Row.':G'.$Next_Row)->applyFromArray($style_header);
			$sheet->mergeCells('A'.$Next_Row.':G'.$Next_Row);
			
			
			$sheet->setCellValue('H'.$Next_Row, number_format($Grand_Total,2));
			$sheet->getStyle('H'.$Next_Row)->applyFromArray($style_header);
			
			
			*/
		}
		
		
		
		$sheet->setTitle($Title);
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Report_Stock_Material_Tras_vs_Stock'.date('YmdHis').'.xlsx"');
		//unduh file
		$objWriter->save("php://output");
		exit;
	}
	
	public function modal_history(){
		$id_material 	= $this->uri->segment(3);
		$id_gudang 		= $this->uri->segment(4);

		$result		= $this->db->get_where('tran_warehouse_jurnal_detail', array('id_material'=>$id_material, 'id_gudang'=>$id_gudang))->result_array();
		$material	= $this->db->get_where('raw_materials', array('id_material'=>$id_material))->result_array();

		$data = array(
			'result' => $result,
			'material' => $material,
			'id_gudang' => $id_gudang
		);

		$this->load->view('Stock_tras/modal_history', $data);
	}

}
