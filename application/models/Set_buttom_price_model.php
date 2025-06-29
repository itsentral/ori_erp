<?php
class Set_buttom_price_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function index_project(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/project';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Selling Price',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View index selling price (Menu Costing)');
		$this->load->view('Cost_quotation/project',$data);
	}
	
	public function modal_approve(){
		$id_bq = $this->uri->segment(3);

		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$getHeader	= $this->db->query($qSupplier)->result();

		// $qMatr 		= SQL_Quo_Edit($id_bq);		
		$qMatr 		= "	SELECT 
							a.id,
							a.id_category,
							a.length,
							a.id_product,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.qty,
							a.man_power AS man_power,
							a.id_mesin AS id_mesin,
							a.total_time AS total_time,
							a.man_hours AS man_hours,
							a.pe_direct_labour,
							a.pe_indirect_labour,
							a.pe_machine,
							ifnull( a.pe_mould_mandrill, 0 ) AS pe_mould_mandrill,
							a.pe_consumable,
							a.pe_foh_consumable,
							a.pe_foh_depresiasi,
							a.pe_biaya_gaji_non_produksi,
							a.pe_biaya_non_produksi,
							a.pe_biaya_rutin_bulanan

						FROM 
							bq_detail_header a 
						WHERE 
							a.id_category <> 'pipe slongsong' 
							AND a.id_category <> 'product kosong' 
							AND a.id_bq = '$id_bq' 
						ORDER BY 
							a.id ASC";			
		$getDetail	= $this->db->query($qMatr)->result_array();

		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
		$getEngCost	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
		$getPackCost	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
		$getTruck	= $this->db->query($gTruck)->result_array();

		$engCPCV 	= "SELECT
							b.*,
							c.* 
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
						WHERE
							 b.category = 'lokal' 
							AND b.id_bq = '".$id_bq."' 
							AND b.price_total <> 0
						ORDER BY
							b.id ASC ";
		$getVia	= $this->db->query($engCPCV)->result_array();

		$rest_mat		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'mat'))->result_array();
		$rest_baut		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'baut'))->result_array();
		$rest_plate		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'plate'))->result_array();
		$rest_gasket	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'gasket'))->result_array();
		$rest_lainnya	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'lainnya'))->result_array();

		$otherArray	= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
		
		$data = array(
			'id_bq'			=> $id_bq,
			'otherArray'	=> $otherArray,
			'getHeader'		=> $getHeader,
			'getDetail'		=> $getDetail,
			'getEngCost'	=> $getEngCost,
			'getPackCost'	=> $getPackCost,
			'getTruck'		=> $getTruck,
			'getVia'		=> $getVia,
			'material'		=> $rest_mat,
			'rest_baut'		=> $rest_baut,
			'rest_plate'	=> $rest_plate,
			'rest_gasket'	=> $rest_gasket,
			'rest_lainnya'	=> $rest_lainnya
		);
		
		$this->load->view('Cost_quotation/modalAppCost', $data);
	}
	
	public function get_data_set_buttom_price(){
		$controller		= ucfirst(strtolower($this->uri->segment(1))).'/project';
		$uri_code		= $this->uri->segment(3);
		$Arr_Akses		= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_json_set_buttom_price(
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";

			$get_rev_cos = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_revised_header',array('id_bq'=>$row['id_bq']))->result();
			$rev_cos = (!empty($get_rev_cos[0]->revised))?$get_rev_cos[0]->revised:0;
			
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#ce9021'>".$rev_cos."</span></div>";
				$class = Color_status($row['status']);
				
			// $nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['status']."</span></div>";
				
				$add		= "";
				$edit		= "";
				$print		= "";
				$approve	= "";
				
				if($row['sts_price_quo'] == 'Y'){
					if($Arr_Akses['update']=='1'){
						$edit	= "&nbsp;<a href='".base_url('penawaran/edit_penawaran_new2/'.$row['id_bq'])."' class='btn btn-sm btn-warning'  title='Edit Selling Price' ><i class='fa fa-edit'></i></a>";
					}
					if($Arr_Akses['approve']=='1'){
						$approve= "&nbsp;<button class='btn btn-sm btn-success approve' title='Approve' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
					}
					if($Arr_Akses['download']=='1'){
						$print	= "&nbsp;<a href='".base_url('cost_quotation/print_penawaran3/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-info'  title='Print Selling Price' ><i class='fa fa-print'></i></a>";
					}
				}
				if($row['sts_price_quo'] == 'N'){
					if($Arr_Akses['create']=='1'){
						$add	= "&nbsp;<a a href='".base_url('penawaran/add_penawaran2/'.$row['id_bq'])."' class='btn btn-sm btn-primary'  title='Set Selling Price' ><i class='fa fa-exchange'></i></a>";
					}
				}
					
			$nestedData[]	= "<div align='left'>
								".$add."
								".$edit."
								".$print."
								".$approve."
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

	public function query_json_set_buttom_price($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id_bq,
				a.no_ipp,
				a.estimasi,
				a.rev,
				a.order_type,
				b.status,
				b.nm_customer,
				b.project,
				b.status AS sts_ipp,
				b.sts_price_quo
			FROM
				bq_header a 
				LEFT JOIN production b ON a.no_ipp = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 
				1=1 
				AND a.approved_est = 'Y' 
				AND a.estimasi='Y' 
				AND b.status IN 
					('WAITING EST PRICE PROJECT')
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
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
	
}