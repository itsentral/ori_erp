<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang_spool extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
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
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Gudang Finish Good Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data gudang spool');
		$this->load->view('Gudang_spool/index',$data);
	}

	public function server_side_spool(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spool(
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
        $DATEFILTER = $requestData['date_filter'];
		$GET_NO_SPK = get_detail_final_drawing();
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


			
			$view = "<a href='".base_url('gudang_spool/detail/'.$row['spool_induk'].'/'.$row['tanda'])."' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";
            $ArrNo_Spool    = [];
			$ArrNo_IPP      = [];
			$ArrNo_SPK      = [];
			$ArrNo_Drawing  = [];
            if(empty($DATEFILTER)){
                $get_split_ipp = $this->db->select('id_produksi, id_milik, kode_spool, product_code, product_ke, cutting_ke, no_drawing, id_category AS nm_product, no_spk, COUNT(id) AS qty, sts, length, status_tanki, nm_tanki')->group_by('sts, id_milik')->order_by('id','asc')->get_where('spool_group_all',array('spool_induk'=>$row['spool_induk']))->result_array();
				$ArrNo_Spool = [];
				$ArrNo_IPP = [];
				$ArrNo_Drawing = [];
				$ArrNo_SPK = [];
				foreach ($get_split_ipp as $key => $value) { $key++;

					$no_spk 		= $value['no_spk'];
					$ArrNo_IPP[] 	= str_replace('PRO-','',$value['id_produksi']);
					$ArrNo_Spool[] 	= $value['kode_spool'];

					$ArrNo_Drawing[] = $value['no_drawing'];
					
					$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
					
					$IMPLODE = explode('-', $value['kode_spool']);

					$sts = $value['sts'];

					$product 	= strtoupper($value['nm_product']).', '. spec_bq2($value['id_milik']);
					if($sts == 'cut'){
						$product 	= strtoupper($value['nm_product']).', '. spec_bq2($value['id_milik']).', cut '.number_format($value['length']);
					}
					if($value['status_tanki'] == 'tanki'){
						$product 	= strtoupper($value['nm_tanki']);
					}

					$no = sprintf('%02s', $key);

					$ArrNo_SPK[] = $no.'. <span class="text-bold text-primary">['.$IMPLODE[0].'/'.$no_spk.']</span> <span class="text-bold text-success">'.strtoupper($sts).'</span><span class="text-bold"> ['.$value['qty'].' pcs]</span> '.$product;
				}
            }
            else{
                $get_split_ipp  = $this->db->select('no_ipp, id_milik, kode_spool, no_drawing')->get_where('stock_spool_per_day',array('spool_induk'=>$row['spool_induk']))->result_array();
                $get_split_ipp2  = $this->db->select('kode_spool, product, COUNT(id) AS qty, sts, no_so, id_milik')->group_by('sts,kode_spool,id_milik')->get_where('stock_spool_per_day',array('spool_induk'=>$row['spool_induk']))->result_array();
                foreach ($get_split_ipp as $key => $value) { $key++;
                    $ArrNo_Spool[]  = $value['kode_spool'];
                    $ArrNo_IPP[]    = $value['no_ipp'];
                    $ArrNo_Drawing[]= $value['no_drawing'];
                }
                foreach ($get_split_ipp2 as $key => $value) { $key++;
					$no_spk         = (!empty($GET_NO_SPK[$value['id_milik']]['no_spk']))?$GET_NO_SPK[$value['id_milik']]['no_spk']:'not set';
                    $ArrNo_SPK[]    = $key.'. '.$value['kode_spool'].'/'.$value['no_so'].'/'.strtoupper($value['product']).' <b class="text-blue">['.$value['qty'].' PCS]</b>/'.$no_spk.'/'.strtoupper($value['sts']);
                }
            }
			
			$explode_spo     = implode('<br>',array_unique($ArrNo_Spool));
			$explode_ipp    = implode('<br>',array_unique($ArrNo_IPP));
			$explode_spk    = implode('<br>',$ArrNo_SPK);
			$explode_drawing= implode('<br>',array_unique($ArrNo_Drawing));

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['spool_induk']."</div>";
			$nestedData[]	= "<div align='center'>".$explode_spo."</div>";
			$nestedData[]	= "<div align='left'>".$explode_drawing."</div>";
			$nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>".$explode_spk."</div>";
            $DATED = (empty($DATEFILTER))?$row['spool_date']:$row['hist_date'];
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($DATED))."</div>";
			// $nestedData[]	= "<div align='center'>".$view."</div>";
			
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

	public function query_data_spool($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		if($date_filter == ''){
            $sql = "
                SELECT
                    (@row:=@row+1) AS nomor,
                    a.*,
                    0 as tanda
                FROM
                    spool_group_release a,
                    (SELECT @row:=0) r
                WHERE 1=1
                    AND a.kode_delivery IS NULL
                    AND (
                        a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
                    )
                GROUP BY
                    a.spool_induk
            ";
        }
        else{
            $sql = "
                SELECT
                    (@row:=@row+1) AS nomor,
                    a.*,
                    1 AS tanda
                FROM
                    stock_spool_per_day a,
                    (SELECT @row:=0) r
                WHERE 1=1
                    AND DATE(a.hist_date) = '".$date_filter."'
                    AND (
                        a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
                        OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
                    )
                GROUP BY
                    a.spool_induk
            ";
        }
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_spool',
			2 => 'kode_spk',
			3 => 'kode_spk',
			4 => 'kode_spk'
		);

		$sql .= " ORDER BY a.spool_induk DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function detail($kode_spool){
	
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$result = $this->db->group_by('kode_spool')->order_by('kode_spool','asc')->get_where('spool_group_release', array('spool_induk'=>$kode_spool))->result_array();
		$data = array(
			'title'			=> 'Detail Spool',
			'action'		=> 'index',
			'result'		=> $result,
			'spool_induk'		=> $kode_spool,
		);
		$this->load->view('Gudang_spool/detail',$data);
	}

	public function download_excel($date_filter){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

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

		
		$where = "";
		if($date_filter == '0'){
			$sql = "SELECT
						a.spool_induk AS spool_induk,
						a.kode_spool AS kode_spool,
						a.product_code AS no_so,
						a.no_spk AS no_spk,
						a.id_category AS product,
						b.nm_customer AS customer,
						b.project AS project,
						a.id_milik AS id_milik,
						COUNT(a.id) AS qty,
						a.no_drawing AS no_drawing,
						a.product_code,
						a.product_ke,
						a.length,
						a.sts,
						a.kode_spk,
						c.customer AS customer_tanki,
						c.project AS project_tanki,
						d.id_product AS product_tanki
					FROM
						spool_group_release a
						LEFT JOIN production b ON REPLACE(a.id_produksi,'PRO-','')=b.no_ipp
						LEFT JOIN planning_tanki c ON REPLACE(a.id_produksi,'PRO-','')=c.no_ipp
						LEFT JOIN production_detail d ON d.id=a.id_pro
					WHERE 1=1
						AND a.kode_delivery IS NULL
					GROUP BY
						a.sts, a.spool_induk, a.id_milik, a.length ORDER BY a.spool_induk DESC
            ";
		}
		else{

			$sql = "SELECT
						a.spool_induk AS spool_induk,
						a.kode_spool AS kode_spool,
						a.no_so AS no_so,
						a.no_spk AS no_spk,
						a.product AS product,
						a.nm_customer AS customer,
						a.nm_project AS project,
						a.spec AS spec,
						a.id_milik AS id_milik,
						COUNT(a.id) AS qty,
						a.no_drawing AS no_drawing,
						a.product_code,
						a.product_ke,
						a.length,
						a.sts,
						a.kode_spk,
						c.customer AS customer_tanki,
						c.project AS project_tanki,
						d.id_product AS product_tanki
					FROM
						stock_spool_per_day a
						LEFT JOIN planning_tanki c ON a.no_ipp=c.no_ipp
						LEFT JOIN production_detail d ON d.id=a.id_pro
					WHERE 1=1
						AND DATE(a.hist_date) = '".$date_filter."'
					GROUP BY
						a.sts, a.spool_induk, a.id_milik, a.length ORDER BY a.spool_induk DESC
            ";
		}
		// ECHO $sql; exit;
		$restDetail1	= $this->db->query($sql)->result_array();

		$DATE_JUDUL = ($date_filter == '0')?'':' ('.$date_filter.')';

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A'.$Row, 'DATA GUDANG SPOOL '.$DATE_JUDUL);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'No Spool');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'No SO');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'No SPK');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'Product');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'Customer');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Project');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Spec');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'QTY');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'No Drawing');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$DETAIL_IPP = get_detail_ipp();
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
				$spool_induk	= strtoupper($row_Cek['spool_induk']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spool_induk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_so	= strtoupper($row_Cek['no_so']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_so);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk	= strtoupper($row_Cek['no_spk']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$FLAG_TANKI = substr($no_spk,0,3);
				$product	= strtoupper($row_Cek['product']);
				$customer	= strtoupper($row_Cek['customer']);
				$project	= strtoupper($row_Cek['project']);
				if($FLAG_TANKI == '90T'){
					$product	= strtoupper($row_Cek['product_tanki']);
					$customer	= strtoupper($row_Cek['customer_tanki']);
					$project	= strtoupper($row_Cek['project_tanki']);
				}



				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $customer);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $project);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$result3 = $this->db->get_where('so_detail_header', array('id'=>$row_Cek['id_milik']))->result();

				$IMPLODE = explode('.', $row_Cek['product_code']);
				$product_code = $IMPLODE[0].'.'.$row_Cek['product_ke'];
				$length3 = (!empty($result3[0]->length))?$result3[0]->length:0;
				$LENGTH = (!empty($row_Cek['length']))?$row_Cek['length']:$length3;

				$SPEC = (!empty(spec_bq3($row_Cek['id_milik'])))?spec_bq3($row_Cek['id_milik']):'';
				if($row_Cek['sts'] == 'deadstok'){
					$SPEC = $value['kode_spk'].' x '.$row_Cek['length'];
				}
				$spec	= $SPEC." x ".number_format($LENGTH);
				if($FLAG_TANKI == '90T'){
					$spec	= $this->tanki_model->get_spec($row_Cek['id_milik']);
				}

				$awal_col++;
				
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty	= $row_Cek['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_drawing	= $row_Cek['no_drawing'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_drawing);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

			}
		}


		$sheet->setTitle('GUDANG SPOOL');
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
		header('Content-Disposition: attachment;filename="gudang-spool.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}
?>