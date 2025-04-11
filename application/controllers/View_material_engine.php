<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class View_material_engine extends CI_Controller {
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
		$data = array(
			'title'			=> 'View Material',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('Index view material enginerring');
		$this->load->view('Machine/view_material/index',$data);
	}

	public function server_side_view_material(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_view_material(
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
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['no_ipp'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$class = Color_status($row['status']);
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['status']."</span></div>";
			$nestedData[]	= "<div align='center'>
                                    &nbsp;<button class='btn btn-sm btn-primary detailMat' title='Total Material' data-id_bq='".$row['no_ipp']."'><i class='fa fa-eye'></i></button>
									&nbsp;<a href=".site_url('view_material_engine/excel_material/BQ-'.$row['no_ipp'])." target='_blank' class='btn btn-sm btn-success'><i class='fa fa-file-excel-o'></i></a>
									</div>";
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

	public function query_server_side_view_material($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				production a 
			WHERE status != 'CANCELED'
				AND status != 'WAITING STRUCTURE BQ'
				AND status != 'WAITING APPROVE STRUCTURE BQ'
				AND status != 'WAITING ESTIMATION PROJECT'
				AND status != 'WAITING APPROVE EST PROJECT'
			AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer',
			3 => 'project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function detail($id_bq){
		$result		= $this->db->order_by('nm_material','ASC')->get_where('estimasi_total_material',array('id_bq'=>'BQ-'.$id_bq, 'id_material <>'=>'MTL-1903000'))->result_array();

		$data = array(
			'detail'		=> $result
		);
		$this->load->view('Machine/view_material/detail', $data);
	}

	public function excel_material($id_bq){
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
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(3);
		$sheet->setCellValue('A'.$Row, 'VIEW MATERIAL '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Material Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
        $sheet->getColumnDimension('B')->setWidth(70);
        
        $sheet->setCellValue('C'.$NewRow, 'Est Mat');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(16);
		
		$result	= $this->db->order_by('nm_material','ASC')->get_where('estimasi_total_material',array('id_bq'=>$id_bq, 'id_material <>'=>'MTL-1903000'))->result_array();
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$est_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
                
                $awal_col++;
				$est_harga	= $row_Cek['last_cost_qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
			}
		}
		
		
		$sheet->setTitle('View Material');
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
		header('Content-Disposition: attachment;filename="view-material-engine-'.$id_bq.'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}