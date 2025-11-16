<?php
class Produksi_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function index(){
		$uri_help = (!empty($this->uri->segment(3)))?'/'.$this->uri->segment(2).'/'.$this->uri->segment(3):'';
		$menu_baru = ($this->uri->segment(3) != '')?1:0;
		if($menu_baru == 0){
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3);
		}
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Production',
			'action'		=> 'index',
			'uri_help'		=> $uri_help,
			'menu_baru'		=> $menu_baru,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Production');
		$this->load->view('Production/index',$data);
	}
	
	public function modal_detail_spk(){
		$id_produksi= $this->uri->segment(3);
		$menu_baru	= $this->uri->segment(4);
		$id_bq 		= "BQ-".str_replace('PRO-','',$id_produksi);
		$no_ipp = str_replace('PRO-','',$id_produksi);
		$row		= $this->db->get_where('production_header', array('id_produksi'=>$id_produksi))->result_array();
		$JALUR = (!empty($row[0]['jalur']))?$row[0]['jalur']:'';
		$HelpDet 	= "bq_detail_header";
		$help2 = "";
		if(!empty($JALUR)){
			if($JALUR == 'FD'){
				$HelpDet = "so_detail_header";
				$help2 = " b.id_milik AS id_milik2,";
			}
		}

		$Disb 	= "";
		if(!empty($row[0]['sts_produksi'])){
			if($row[0]['sts_produksi'] == 'FINISH'){
				$Disb = "disabled";
			}
		}

		$qDetail	= "	SELECT
							a.*,
							b.no_komponen,
							b.id_category AS comp,
							
							b.id AS id_uniq
						FROM
							production_detail a
							LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
						WHERE
							a.id_produksi = '".$id_produksi."'
						GROUP BY
							b.no_komponen,
							a.sts_delivery,
							a.id_product
						ORDER BY
							b.id_bq_header ASC";
		// echo $qDetail;
		$rowD		= $this->db->query($qDetail)->result_array();
		
		$rest_mat 	= $this->db->get_where('production_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'mat'))->result_array(); 
		$rest_acc 	= $this->db->get_where('production_acc_and_mat', array('id_bq'=>$id_bq, 'category <>'=>'mat'))->result_array(); 
		
		$data = array(
			'no_ipp' 		=> $no_ipp,
			'tandaT' 		=> substr($no_ipp,0,4),
			'id_produksi' 	=> $id_produksi,
			'id_bq' 		=> $id_bq,
			'rest_mat' 		=> $rest_mat,
			'rest_acc' 		=> $rest_acc,
			'menu_baru' 	=> $menu_baru,
			'row' 			=> $row,
			'rowD' 			=> $rowD,
			'HelpDet'		=> $HelpDet,
			'Disb'			=> $Disb,
			'jalur'			=> $JALUR
		);
		
		$this->load->view('Production/modalDetail', $data);
	}
	
	public function get_data_json_spk_produksi(){
		$requestData	= $_REQUEST;
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		if($requestData['menu_baru'] == '1'){
			$controller			= ucfirst(strtolower($this->uri->segment(1).'/index/new'));
		}
		$Arr_Akses			= getAcccesmenu($controller);
		
		$fetch			= $this->query_data_spk_produksi(
			$requestData['menu_baru'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$menu_baru = $requestData['menu_baru'];
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

			$tandaT = substr($row['no_ipp'],0,4);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
				$so_number = (!empty($row['so_number2']))?$row['so_number2']:$row['no_ipp'];
			$nestedData[]	= "<div align='center'>".$so_number."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";

			$class = Color_status($row['sts_produksi']);
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
			if($row['canceled_so']=="Y"){
				$nestedData[]	= "<div align='left'><span class='badge bg-red'>CLOSE</span></div>";
			}else{
				$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$class."'>".$row['sts_produksi']."</span></div>";
			}
					
					$create 		= "";
					$update_spk1	= "";
					$update_spk2	= "";
					$start_produksi	= "";
					$finish 		= "";
					$close_book 		= "";
					if($Arr_Akses['update']=='1' AND $row['sts_produksi'] == 'PROCESS PRODUCTION' AND $menu_baru == 0){
						$update_spk1	= "&nbsp;<a href='".base_url('production/updateRealNew2/'.$row['id_produksi'])."' class='btn btn-sm' style='background-color: #ce005f; border-color: #ce005f; color: white;' title='Update SPK 1 (NEW)' data-role='qtip'><i class='fa fa-edit'></i></a>";
						$update_spk2	= "&nbsp;<a href='".base_url('production/updateRealNew3/'.$row['id_produksi'])."' class='btn btn-sm' style='background-color: #d25e0c; border-color: #d25e0c; color: white;' title='Update SPK Mixing (NEW)' data-role='qtip'><i class='fa fa-edit'></i></a>";
					}
					if($Arr_Akses['update']=='1' AND $row['sts_produksi'] == 'PROCESS PRODUCTION' AND $menu_baru == 1 AND $tandaT != 'IPPT'){
						$finish			= "&nbsp;<button class='btn btn-sm btn-success close_produksi' title='Close Produksi' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-check'></i> Close</button>";
					}
					if($Arr_Akses['update']=='1' AND $row['sts_produksi'] == 'WAITING PRODUCTION'){
						$start_produksi	= "&nbsp;<button class='btn btn-sm btn-info start_produksi' title='Start Produksi ?' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-check'></i></button>";
					}
					if($Arr_Akses['create']=='1'){
						$create 		= "<button class='btn btn-sm btn-primary detail_spk' title='Detail Production' data-menu_baru='".$menu_baru."' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-eye'></i></button>";
					}

					if($Arr_Akses['update']=='1' AND $row['sts_booking_close'] == 'N'  AND $row['sts_booking'] == 'Y'){
						$close_book 		= "<button class='btn btn-sm btn-warning close_booking_mat' title='Close Booking Material' data-no_ipp='".str_replace('PRO-','',$row['id_produksi'])."'><i class='fa fa-check'></i> Close Booking SO</button>";
					}
			if($row['canceled_so']=="Y"){
				$nestedData[]	= "<div align='left'><span class='badge bg-red'>CLOSE</span></div>";
			}else{
				$nestedData[]	= "<div align='left'>
										".$create."
										".$update_spk1."
										".$update_spk2."
										".$start_produksi."
										".$finish."
										".$close_book."
										</div>";
			}
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

	public function query_data_spk_produksi($menu_baru, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.no_ipp,
				a.sts_produksi,
				a.created_date,
				a.id_produksi,
				a.project,
				a.so_number2,
				d.sts_booking,
				d.sts_booking_close,
				e.canceled_so
			FROM
				production_view a
				left join table_sales_order e on a.no_ipp = e.no_ipp 
				LEFT JOIN warehouse_planning_header d ON a.no_ipp = d.no_ipp,
                (SELECT @row:=0) r
		    WHERE 1=1
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.so_number2 LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'so_number2',
			3 => 'project',
			4 => 'created_date'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	
	//========================================================================================================================
	//=================================================PROGRESS PRODUKSI======================================================
	//========================================================================================================================
	public function progress_produksi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/progress_produksi';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$GET_DELIVERY_DATE = get_delivery_date();
		// echo '<pre>';
		// print_r($GET_DELIVERY_DATE);
		// exit;

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Progress Production',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Progress Production');
		$this->load->view('Production/progress_produksi',$data);
	}
	
	public function get_data_json_spk_produksi_progress(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/progress_produksi';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spk_produksi_progress(
			$requestData['tgl_awal'],
			$requestData['tgl_akhir'],
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
		$GET_DELIVERY_DATE = get_delivery_date();
		// print_r($GET_DELIVERY_DATE);
		// exit;
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
				$so_number = (!empty($row['so_number2']))?$row['so_number2']:'';
			$nestedData[]	= "<div align='center'>".$so_number."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";

			$DELIVERY_DATE = (!empty($GET_DELIVERY_DATE[$row['no_ipp']]))?$GET_DELIVERY_DATE[$row['no_ipp']]:array();
			$DELIVERY_DATE_ = '';
			if(!empty($DELIVERY_DATE)){
				$DELIVERY_DATE_UNIQ = array_unique($DELIVERY_DATE);
				$DELIVERY_DATE_ = implode('<br>',$DELIVERY_DATE_UNIQ);
			}

			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$DELIVERY_DATE_."</div>";
			$nestedData[]	= "<div align='center'>".number_format(persen_progress_produksi($row['id_produksi']))." %</div>";
			
			$class = Color_status($row['sts_produksi']);
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$class."'>".$row['sts_produksi']."</span></div>";
				$create = "";
				$excel = "";
				if($Arr_Akses['read']=='1'){
					$create = "<button type='button' class='btn btn-sm btn-success detail_spk' title='Detail Production' data-id_produksi='".$row['id_produksi']."'  data-no_so='".$so_number."'><i class='fa fa-eye'></i></button>";
				}
				if($Arr_Akses['download']=='1'){
					$excel = "<button type='button' class='btn btn-sm btn-info download_excel' title='Download' data-id_produksi='".$row['id_produksi']."'  data-no_so='".$so_number."'><i class='fa fa-file-excel-o'></i></button>";
				}
			$nestedData[]	= "<div align='center'>
									".$create."
									".$excel."
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

	public function query_data_spk_produksi_progress($tgl_awal,$tgl_akhir,$like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$WHERE_IPP = '';
		if($tgl_awal != '0'){
			$GET_DELIVERY_DATE_RANGE = get_delivery_date_between($tgl_awal,$tgl_akhir);
			// echo '<pre>';
			// print_r($GET_DELIVERY_DATE_RANGE);
			// exit;
			$ArrDeliv = [];
			if(!empty($GET_DELIVERY_DATE_RANGE)){
				$ArrDeliv = $GET_DELIVERY_DATE_RANGE;
				$DELIVERY_IMP = implode("','",$ArrDeliv);
				$WHERE_IPP = "AND b.no_ipp IN ('".$DELIVERY_IMP."')";
			}
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.no_ipp,
				a.id_produksi,
				a.sts_produksi,
				a.created_date,
				b.project,
				c.so_number AS so_number2
			FROM
				production_header a 
				LEFT JOIN production b ON a.no_ipp = b.no_ipp
				LEFT JOIN so_number c ON a.no_ipp = REPLACE(c.id_bq, 'BQ-', ''),
                (SELECT @row:=0) r
		    WHERE a.deleted = 'N' AND a.sts_produksi != 'FINISH' $WHERE_IPP
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'c.so_number',
			3 => 'b.project',
			4 => 'created_date'
		);

		$sql .= " ORDER BY a.created_date DESC,  ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modal_detail_progress(){
		$id_produksi= $this->uri->segment(3);
		$id_bq 		= "BQ-".str_replace('PRO-','',$id_produksi);
		
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		$help2 = "";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
			$help2 = " b.id_milik AS id_milik2,";
		}

		$Disb 	= "";
		if($row[0]['sts_produksi'] == 'FINISH'){
			$Disb = "disabled";
		}

		$qDetail	= "	SELECT
							a.*,
							b.no_komponen,
							b.id_category AS comp,
							c.type AS typeProduct,
							b.id AS id_uniq
						FROM
							production_detail a
							LEFT JOIN product_parent c ON a.id_category = c.product_parent
							LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
						WHERE
							a.id_produksi = '".$id_produksi."'
						GROUP BY
							b.no_komponen,
							a.sts_delivery,
							a.id_product
						ORDER BY
							b.id_bq_header ASC";
		// echo $qDetail;
		$rowD		= $this->db->query($qDetail)->result_array();
		
		$sql_mat 	= "SELECT * FROM production_acc_and_mat WHERE id_bq='".$id_bq."' AND category='mat' ";
		$rest_mat 	= $this->db->query($sql_mat)->result_array(); 
		// echo $sql_mat;
		$sql_acc 	= "SELECT * FROM production_acc_and_mat WHERE id_bq='".$id_bq."' AND category='acc' ";
		$rest_acc 	= $this->db->query($sql_acc)->result_array(); 
		
		$data = array(
			'id_produksi' 	=> $id_produksi,
			'id_bq' 		=> $id_bq,
			'rest_mat' 		=> $rest_mat,
			'rest_acc' 		=> $rest_acc,
			'row' 			=> $row,
			'rowD' 			=> $rowD,
			'HelpDet'		=> $HelpDet,
			'Disb'			=> $Disb,
			'jalur'			=> $row[0]['jalur']
		);
		
		$this->load->view('Production/modal_detail_progress', $data);
	}
	
}
?>