<?php
class Penagihan_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function index($delivery=''){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$tblpenagihan="penagihan";
		if($delivery!='') $tblpenagihan="penagihan";

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$list_so = $this->db->query("SELECT
										a.id as id_penagihan,
										b.so_number
									FROM
										".$tblpenagihan." a
										left join billing_so c ON a.no_po=c.no_po
										LEFT JOIN so_bf_header b ON c.no_ipp=b.no_ipp")->result_array();
		$list_cust = $this->db->query("SELECT
										a.kode_customer as id_customer,
										a.customer as nm_customer
									FROM
										".$tblpenagihan." a group by a.kode_customer, a.customer")->result_array();
		
		$data = array(
			'title'			=> 'Indeks Of Billing',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_so'		=> $list_so,
			'delivery'		=> $delivery,
			'list_cust'		=> $list_cust
		);
		history('View data plan penagihan');
		$this->load->view('Penagihan/index',$data);
	}
	
	public function server_side_penagihan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_penagihan(
			$requestData['customer'],
			$requestData['no_so'],
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
			$total_data    = $totalData;
            $start_dari    = $requestData['start'];
            $asc_desc      = $requestData['order'][0]['dir'];
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
			
			$get_so = $this->db->group_by('no_po')->select('no_po')->get_where('billing_top', array('id_penagihan'=>$row['id']))->result_array();
			$arr_so = array();
			foreach($get_so AS $val => $valx){
				$arr_so[$val] = get_name('so_number','so_number','id_bq',"BQ-".$valx['no_po']);
			}
			$dt_so	= implode("<br>", $arr_so);
			
			$nestedData[]	= "<div align='left'>".strtoupper($dt_so)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_po'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['customer'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y', strtotime($row['plan_tagih_date']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['plan_tagih_usd'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['plan_tagih_idr'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['type'])."</div>";
			$class 	= Color_status_custom2($row['status'], 'penagihan');
			$status = Status_status_custom2($row['status'], 'penagihan');
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$status."</span></div>";
				$create_progress	= "";
				$create_um	= "";
				$create_retensi	= "";
				
				if($row['type'] == 'progress'){
					if($row['status'] == '10' OR $row['status'] == '11'){
						$create_progress	= "<a href='".base_url('penagihan/create_progress/'.$row['id'])."' class='btn btn-sm btn-success'><i class='fa fa-pencil'></i></a>";
					}
				}
				
				if($row['type'] == 'uang muka'){
					if($row['status'] == '10'){
						$create_um	= "<a href='".base_url('penagihan/create_um/'.$row['id'])."' class='btn btn-sm btn-primary'><i class='fa fa-pencil'></i></a>";
					}
				}
				
				if($row['type'] == 'retensi'){
					if($row['status'] == '10'){
						$create_retensi	= "<a href='".base_url('penagihan/create_retensi/'.$row['id'])."' class='btn btn-sm btn-info'><i class='fa fa-pencil'></i></a>";
					}
				}
			$nestedData[]	= "<div align='center'>
                                    ".$create_progress."
									".$create_um."
									".$create_retensi."
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

	public function query_data_penagihan($customer, $no_so, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_customer = '';
		if($customer != '0'){
			$where_customer = " AND a.kode_customer='".$customer."'";
		}
		$where_no_so = '';
		if($no_so != '0'){
			$where_no_so = " AND a.id like '%".$no_so."%'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				penagihan a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_customer." ".$where_no_so." AND  (
				a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_so',
			2 => 'no_po',
			3 => 'project',
			4 => 'customer',
			5 => 'keterangan',
			6 => 'plan_tagih_date',
			7 => 'plan_tagih_usd',
			8 => 'plan_tagih_idr',
			9 => 'type'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			
			$check = $data['check'];
			
			$dtListArray = [];
			if(!empty($check)){
				foreach($check AS $val => $valx){
					$dtListArray[$val] = $valx;
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";
				$dtImplode2	= implode(",", $dtListArray);
			}

			$result_data 	= $this->db->query("SELECT * FROM billing_top WHERE id IN ".$dtImplode." ORDER BY jatuh_tempo DESC ")->result_array();
			
			
			$max_num 		= $this->db->select('MAX(id) AS nomor_max')->get('penagihan')->result();
			$id_tagih 		= $max_num[0]->nomor_max + 1;
			
			$SUM_USD = 0;
			$SUM_IDR = 0;
			$Update_b = [];
			foreach($result_data AS $val => $valx){
				$SUM_USD += $valx['value_usd'];
				$SUM_IDR += $valx['value_idr'];
				$no_ipp = str_replace('BQ-','',$valx['no_po']);
				
				$Update_b[$val]['id'] = $valx['id'];
				$Update_b[$val]['id_penagihan'] = $id_tagih;
			}
			
			
			$header = [
				'no_so' => $dtImplode2,
				'no_po' => $data['no_po'],
				'project' => NULL,
				'kode_customer' => $data['customer'],
				'customer' => get_name('customer','nm_customer','id_customer',$data['customer']),
				'keterangan' => NULL,
				'plan_tagih_date' => $valx['jatuh_tempo'],
				'plan_tagih_usd' => $SUM_USD,
				'plan_tagih_idr' => $SUM_IDR,
				'type' => $data['type'],
				'status' => 10,
				'type_lc' => $data['type_lc'],
				'etd' => $data['etd'],
				'eta' => $data['eta'],
				'consignee' => $data['consignee'],
				'notify_party' => $data['notify_party'],
				'port_of_loading' => $data['port_of_loading'],
				'port_of_discharges' => $data['port_of_discharges'],
				'flight_airway_no' => $data['flight_airway_no'],
				'ship_via' => $data['ship_via'],
				'saliling' => $data['saliling'],
				'vessel_flight' => $data['vessel_flight'],
				'created_by' => $this->session->userdata['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')

			];
			
			// print_r($header);
			// print_r($Update_b);
			// exit;
			
			$this->db->trans_start();
				$this->db->insert('penagihan', $header);
				$this->db->update_batch('billing_top', $Update_b, 'id');
				
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Process data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Process data success. Thanks ...',
					'status'	=> 1
				);
				history('Create penagihan '.$id_tagih);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))); 
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['read'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}

			$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
			$customer = $this->db->order_by('nm_customer','asc')->group_by('kode_customer')->get('billing_so')->result();
			$no_po = $this->db->order_by('no_po','asc')->group_by('no_po')->get_where('billing_so', array('no_po <>'=> NULL, 'no_po <>'=> '0'))->result();
			$dataDV = $this->db->query("SELECT * FROM delivery_product")->result();

			
			$data = array(
				'title'			=> 'Indeks Of Add Billing',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'customer'		=> $customer,
				'no_po'			=> $no_po,
				'dataDv'		=> $dataDV
			);
			
			$this->load->view('Penagihan/add',$data);
		}
	}
	
	public function server_side_penagihan_add(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_penagihan_add(
			$requestData['customer'],
			$requestData['type'],
			$requestData['no_po'],
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
			$total_data    = $totalData;
            $start_dari    = $requestData['start'];
            $asc_desc      = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'desc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['id']."'></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['so_number'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_pox'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['keterangan'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y', strtotime($row['plan_tagih_date']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['plan_tagih_usd'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['plan_tagih_idr'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['type'])."</div>";
			
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

	public function query_data_penagihan_add($customer, $type, $no_po, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_customer = '';
		if($customer != '0'){
			$where_customer = " AND b.kode_customer='".$customer."'";
		}
		$where_type = '';
		if($type != '0'){
			$where_type = " AND a.group_top='".$type."'";
		}
		$where_no_po = '';
		if($no_po != '0'){
			$where_no_po = " AND b.no_po='".$no_po."'";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id,
				a.no_po AS no_ipp,
				a.group_top AS type,
				a.value_usd AS plan_tagih_usd,
				a.value_idr AS plan_tagih_idr,
				a.jatuh_tempo AS plan_tagih_date,
				a.keterangan,
				b.project,
				b.kode_customer,
				b.nm_customer AS customer,
				b.no_po AS no_pox,
				c.so_number
			FROM
				billing_top a
				LEFT JOIN billing_so b ON a.no_po = b.no_ipp
				LEFT JOIN so_number c ON replace(c.id_bq,'BQ-','') = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE a.category = 'penjualan' ".$where_customer." ".$where_type." ".$where_no_po." AND (a.id_penagihan = '' OR a.id_penagihan IS NULL) AND (
				c.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.group_top LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.value_usd LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.value_idr LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.jatuh_tempo LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'so_number',
			2 => 'no_pox',
			3 => 'project',
			4 => 'customer',
			5 => 'keterangan',
			6 => 'plan_tagih_date',
			7 => 'plan_tagih_usd',
			8 => 'plan_tagih_idr',
			9 => 'type'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_po(){
		$id 	= $this->input->post('id');
		$result	= $this->db->order_by('no_po','asc')->group_by('no_po')->get_where('billing_so_gabung',array('kode_customer'=>$id, 'no_po <>'=> NULL, 'no_po <>'=> '0'))->result_array();
		
		$option	= "";
		$option	.= "<option value='0'>Select PO Number</option>";
		foreach($result AS $val => $valx){
			$option .= "<option value='".$valx['no_po']."'>".strtoupper($valx['no_po'])."</option>";
		}
		if(empty($result)){
			$option	= "<option value='0'>List Empty</option>";
		}
		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function create_progress(){
		if($this->input->post()){
			$data_session			= $this->session->userdata;
			
			$id 					= $this->input->post('id');
			$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
			$nomor_id 	= explode(",",$penagihan[0]->no_so); 
			// echo $nomor_id;exit;
			$getBq 		= $this->db->select('no_po')->where_in('id',$nomor_id)->get('billing_top')->result_array();
			
			$in_ipp = [];
			$in_bq = [];
			
			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= get_nomor_so($valx['no_po']);
			}
			
			
			$Tgl_Invoice			= $this->input->post('tgl_inv');
			$Bulan_Invoice			= date('n',strtotime($Tgl_Invoice));
			$Tahun_Invoice			= date('Y',strtotime($Tgl_Invoice));
			$data_session 		    = $this->session->userdata;
			
			$no_ipp                 = $this->input->post('no_ipp');
			$no_so                 	= $this->input->post('no_so');
			$no_invoice 			= gen_invoice($no_ipp);
			$id_customer			= $this->input->post('id_customer');
			$nm_customer			= $this->input->post('nm_customer');
			$no_bq                  = 'BQ-'.$no_ipp;
			$kurs                   = str_replace(',','',$this->input->post('kurs'));
			$jenis_invoice 			= strtolower($this->input->post('type'));
			$total_invoice          = $this->input->post('total_invoice_hidden');
			$total_invoice_idr      = $this->input->post('total_invoice_hidden')*$kurs;
			$total_um               = $this->input->post('down_payment_hidden');
			$total_um_idr           = $this->input->post('down_payment_hidden')*$kurs;
			$um_persen				= str_replace(',','',$this->input->post('um_persen'));
			$um_persen2				= $this->input->post('um_persen2');
			$umpersen				= $this->input->post('umpersen');
			$total_gab_product      = ($this->input->post('tot_product_hidden'))+($this->input->post('total_material_hidden'))+($this->input->post('total_bq_nf_hidden'));
			$total_gab_product_idr  = ($this->input->post('tot_product_hidden')*$kurs)+($this->input->post('total_material_hidden')*$kurs)+($this->input->post('total_bq_nf_hidden')*$kurs);
			
			$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi_hidden'));
			$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi_hidden2'));
			
			$diskon = (!empty($this->input->post('diskon_hidden')))?$this->input->post('diskon_hidden'):str_replace(',','',$this->input->post('diskon'));
			
			$retensi_FIX = 0;
			
			if($retensi_non_ppn <= 0 ){
				$retensi_FIX = $retensi_ppn;
			}
			
			if($retensi_ppn <= 0 ){
				$retensi_FIX = $retensi_non_ppn;
			}

			$retensi				=  $retensi_FIX;
			$retensi_idr		    =  $retensi_FIX * $kurs;
			
			if($jenis_invoice=='uang muka'){
				$progress = $um_persen;
			}
			elseif($jenis_invoice=='progress'){
				$progress = $umpersen;
			}
			else{
				$progress = 100;
			}
			
			$totaluangmuka2 = 0;
			$totaluangmuka2_idr = 0;
			
			if($jenis_invoice=='uang muka' && $um_persen2 != 0){
				$totaluangmuka2 = $this->input->post('grand_total_hidden') - $this->input->post('down_payment_hidden');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			if($jenis_invoice=='progress' && $um_persen2 != 0){
				$totaluangmuka2 = $this->input->post('down_payment_hidden2');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			
			//INSERT DATABASE TR INVOICE HEADER
			$headerinv = array(
				'id_penagihan'				=> $id,
				'id_bq' 		     		=> $no_bq,
				'no_ipp' 		     		=> $no_ipp,
				'so_number' 		     	=> $no_so,
				'no_invoice' 		     	=> $no_invoice,
				'tgl_invoice'      		    => $Tgl_Invoice,
				'id_customer'	 	      	=> $id_customer,
				'nm_customer' 		      	=> $nm_customer,
				'jenis_invoice' 		    => $jenis_invoice,
				'persentase' 		        => $progress,
				'total_product'	         	=> $this->input->post('tot_product_hidden'),
				'total_product_idr'	        => $this->input->post('tot_product_hidden')*$kurs,
				'total_gab_product'	        => $total_gab_product,
				'total_gab_product_idr'	    => $total_gab_product_idr,
				'total_material'	        => $this->input->post('total_material_hidden'),
				'total_material_idr'	    => $this->input->post('total_material_hidden')*$kurs,
				'total_bq'	                => $this->input->post('total_bq_nf_hidden'),
				'total_bq_idr'	            => $this->input->post('total_bq_nf_hidden')*$kurs,
				'total_enginering'	        => $this->input->post('total_enginering_hidden'),
				'total_enginering_idr'	    => $this->input->post('total_enginering_hidden')*$kurs,
				'total_packing'	            => $this->input->post('total_packing_hidden'),
				'total_packing_idr'	        => $this->input->post('total_packing_hidden')*$kurs,
				'total_trucking'	        => $this->input->post('total_trucking_hidden'),
				'total_trucking_idr'	    => $this->input->post('total_trucking_hidden')*$kurs,
				'total_dpp_usd'	            => $this->input->post('grand_total_hidden'),
				'total_dpp_rp'	            => $this->input->post('grand_total_hidden')*$kurs,
				'total_diskon'	            => $diskon,
				'total_diskon_idr'	        => $diskon * $kurs,
				'total_retensi'	            => $this->input->post('potongan_retensi_hidden2'),
				'total_retensi_idr'	        => $this->input->post('potongan_retensi_hidden2')*$kurs,
				'total_ppn'	                => $this->input->post('ppn_hidden'),
				'total_ppn_idr'	            => $this->input->post('ppn_hidden')*$kurs,
				'total_invoice'	            => $this->input->post('total_invoice_hidden'),
				'total_invoice_idr'	        => $this->input->post('total_invoice_hidden')*$kurs,
				'total_um'	                => $this->input->post('down_payment_hidden'),
				'total_um_idr'	            => $this->input->post('down_payment_hidden')*$kurs,
				'kurs_jual'	                => $kurs,
				'no_po'	                    => $this->input->post('nomor_po'),
				'no_faktur'	                => $this->input->post('nomor_faktur'),
				'no_pajak'	                => $this->input->post('nomor_pajak'),
				'payment_term'	            => $this->input->post('top'),
				'created_by' 	            => $data_session['ORI_User']['username'],
				'created_date' 	            => date('Y-m-d H:i:s'),
				'total_um2'	                => $totaluangmuka2,
				'total_um_idr2'	            => $totaluangmuka2_idr,
				'id_top'	            	=> $id
			);
			

			if($jenis_invoice!='retensi'){
				$detailInv1 = [];
				if(!empty($_POST['data1'])){
					foreach($_POST['data1'] as $val => $d1){
						$nm_material          = $d1['material_name1'];
						$product_cust         = $d1['product_cust'];
						$product_desc         = $d1['product_desc'];
						$diameter_1           = $d1['diameter_1'];
						$diameter_2      	  = $d1['diameter_2'];
						$liner                = $d1['liner'];
						$pressure             = $d1['pressure'];
						$id_milik             = $d1['id_milik'];
						$harga_sat_hidden     = $d1['harga_sat_hidden'];
						$qty                  = $d1['qty'];
						$unit1                = $d1['unit1'];
						$harga_tot_hidden     = $d1['harga_tot_hidden'];
				
						$detailInv1[$val]['id_penagihan']		= $id;
						$detailInv1[$val]['id_bq'] 		     	= $no_bq;
						$detailInv1[$val]['no_ipp'] 		    = $no_ipp;
						$detailInv1[$val]['so_number'] 		    = $no_so;
						$detailInv1[$val]['no_invoice'] 		= $no_invoice;
						$detailInv1[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv1[$val]['id_customer']	 	= $id_customer;
						$detailInv1[$val]['nm_customer'] 		= $nm_customer;
						$detailInv1[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv1[$val]['nm_material']	    = $nm_material;
						$detailInv1[$val]['product_cust']	    = $product_cust;
						$detailInv1[$val]['desc']	    		= $product_desc;
						$detailInv1[$val]['dim_1']	            = $diameter_1;
						$detailInv1[$val]['dim_2']	            = $diameter_2;
						$detailInv1[$val]['liner']	            = $liner;
						$detailInv1[$val]['pressure']	        = $pressure;
						$detailInv1[$val]['spesifikasi']	    = $id_milik;
						$detailInv1[$val]['unit']	            = $unit1;
						$detailInv1[$val]['harga_satuan']	    = $harga_sat_hidden;
						$detailInv1[$val]['harga_satuan_idr']	= $harga_sat_hidden*$kurs;
						$detailInv1[$val]['qty']	            = $qty;
						$detailInv1[$val]['harga_total']	    = $harga_tot_hidden;
						$detailInv1[$val]['harga_total_idr']	= $harga_tot_hidden*$kurs;
						$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
						$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
					}
				}
				
				
				$detailInv2 = [];
				if(!empty($_POST['data2'])){
					foreach($_POST['data2'] as $val => $d2){
						$material_name2       = $d2['material_name2'];
						$harga_sat2_hidden    = $d2['harga_sat2_hidden'];
						$qty2                 = $d2['qty2'];
						$unit2                = $d2['unit2'];
						$harga_tot2_hidden    = $d2['harga_tot2_hidden'];
				
						$detailInv2[$val]['id_penagihan']		= $id;
						$detailInv2[$val]['id_bq'] 		     	= $no_bq;
						$detailInv2[$val]['no_ipp'] 		    = $no_ipp;
						$detailInv2[$val]['so_number'] 		    = $no_so;
						$detailInv2[$val]['no_invoice'] 		= $no_invoice;
						$detailInv2[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv2[$val]['id_customer']	 	= $id_customer;
						$detailInv2[$val]['nm_customer'] 		= $nm_customer;
						$detailInv2[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv2[$val]['nm_material']	    = $material_name2;
						$detailInv2[$val]['unit']	            = $unit2;
						$detailInv2[$val]['harga_satuan']	    = $harga_sat2_hidden;
						$detailInv2[$val]['harga_satuan_idr']	= $harga_sat2_hidden*$kurs;
						$detailInv2[$val]['qty']	            = $qty2;
						$detailInv2[$val]['harga_total']	    = $harga_tot2_hidden;
						$detailInv2[$val]['harga_total_idr']	= $harga_tot2_hidden*$kurs;
						$detailInv2[$val]['kategori_detail']	= 'BQ';
						$detailInv2[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv2[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						
					}
				} 
				
				$detailInv3 = [];
				if(!empty($_POST['data3'])){
					foreach($_POST['data3'] as $val => $d3){
						$material_name3          = $d3['material_name3'];
						$harga_sat3_hidden       = $d3['harga_sat3_hidden'];
						$qty3                    = $d3['qty3'];
						$unit3                   = $d3['unit3'];
						$harga_tot3_hidden       = $d3['harga_tot3_hidden'];
				
						$detailInv3[$val]['id_penagihan']		= $id;
						$detailInv3[$val]['id_bq'] 		     	= $no_bq;
						$detailInv3[$val]['no_ipp'] 		    = $no_ipp;
						$detailInv3[$val]['so_number'] 		    = $no_so;
						$detailInv3[$val]['no_invoice'] 		= $no_invoice;
						$detailInv3[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv3[$val]['id_customer']	 	= $id_customer;
						$detailInv3[$val]['nm_customer'] 		= $nm_customer;
						$detailInv3[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv3[$val]['nm_material']	    = $material_name3;
						$detailInv3[$val]['unit']	            = $unit3;
						$detailInv3[$val]['harga_satuan']	    = $harga_sat3_hidden;
						$detailInv3[$val]['harga_satuan_idr']	= $harga_sat3_hidden*$kurs;
						$detailInv3[$val]['qty']	            = $qty3;
						$detailInv3[$val]['harga_total']	    = $harga_tot3_hidden;
						$detailInv3[$val]['harga_total_idr']	= $harga_tot3_hidden*$kurs;
						$detailInv3[$val]['kategori_detail']	= 'MATERIAL';
						$detailInv3[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv3[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						
					}
				}
				
				$detailInv4 = [];
				if(!empty($_POST['data4'])){
					foreach($_POST['data4'] as $val => $d4){
						$material_name4          = $d4['material_name4'];
						$harga_sat4_hidden       = 0;
						$qty4                    = 0;
						$unit4                   = $d4['unit4'];
						$harga_tot4_hidden       = $d4['harga_tot4_hidden'];
				
						$detailInv4[$val]['id_penagihan']		= $id;
						$detailInv4[$val]['id_bq'] 		     	= $no_bq;
						$detailInv4[$val]['no_ipp'] 		    = $no_ipp;
						$detailInv4[$val]['so_number'] 		    = $no_so;
						$detailInv4[$val]['no_invoice'] 		= $no_invoice;
						$detailInv4[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv4[$val]['id_customer']	 	= $id_customer;
						$detailInv4[$val]['nm_customer'] 		= $nm_customer;
						$detailInv4[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv4[$val]['nm_material']	    = $material_name4;
						$detailInv4[$val]['unit']	            = $unit4;
						$detailInv4[$val]['harga_satuan']	    = 0;
						$detailInv4[$val]['harga_satuan_idr']	= 0;
						$detailInv4[$val]['qty']	            = 0;
						$detailInv4[$val]['harga_total']	    = $harga_tot4_hidden;
						$detailInv4[$val]['harga_total_idr']	= $harga_tot4_hidden*$kurs;
						$detailInv4[$val]['kategori_detail']	= 'ENGINERING';
						$detailInv4[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv4[$val]['created_date'] 	    = date('Y-m-d H:i:s');					
					}
				}
				
				// print_r($_POST['data5']); exit;
				$detailInv5 = [];
				if(!empty($_POST['data5'])){
					foreach($_POST['data5'] as $val => $d5){
						$material_name5          = $d5['material_name5'];
						$unit5                   = $d5['unit5'];
						$harga_tot5_hidden       = $d5['harga_tot5_hidden'];
				
						$detailInv5[$val]['id_penagihan']			= $id;
						$detailInv5[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv5[$val]['no_ipp'] 		     	= $no_ipp;
						$detailInv5[$val]['so_number'] 		     	= $no_so;
						$detailInv5[$val]['no_invoice'] 		    = $no_invoice;
						$detailInv5[$val]['tgl_invoice']      		= $Tgl_Invoice;
						$detailInv5[$val]['id_customer']	 	    = $id_customer;
						$detailInv5[$val]['nm_customer'] 		    = $nm_customer;
						$detailInv5[$val]['jenis_invoice'] 		    = $jenis_invoice;
						$detailInv5[$val]['nm_material']	        = $material_name5;
						$detailInv5[$val]['unit']	                = $unit5;
						$detailInv5[$val]['harga_satuan']	        = 0;
						$detailInv5[$val]['harga_satuan_idr']	    = 0;
						$detailInv5[$val]['qty']	                = 0;		
						$detailInv5[$val]['harga_total']	        = $harga_tot5_hidden;
						$detailInv5[$val]['harga_total_idr']	    = $harga_tot5_hidden*$kurs;
						$detailInv5[$val]['kategori_detail']	    = 'PACKING';
						$detailInv5[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv5[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						
					}
				}
				
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$harga_sat6_hidden       = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6_hidden       = $d6['harga_tot6_hidden'];
				
						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp;
						$detailInv6[$val]['so_number'] 		     	= $no_so;
						$detailInv6[$val]['no_invoice'] 		    = $no_invoice;
						$detailInv6[$val]['tgl_invoice']      		= $Tgl_Invoice;
						$detailInv6[$val]['id_customer']	 	    = $id_customer;
						$detailInv6[$val]['nm_customer'] 		    = $nm_customer;
						$detailInv6[$val]['jenis_invoice'] 		    = $jenis_invoice;
						$detailInv6[$val]['nm_material']	        = $material_name6;
						$detailInv6[$val]['unit']	                = $unit6;
						$detailInv6[$val]['harga_satuan']	        = 0;
						$detailInv6[$val]['harga_satuan_idr']	    = 0;
						$detailInv6[$val]['qty']	                = 0;
						$detailInv6[$val]['harga_total']	        = $harga_tot6_hidden;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6_hidden*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
					}
				}
			}
			
			if($jenis_invoice=='retensi'){
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$harga_sat6_hidden       = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6_hidden       = $d6['harga_tot6_hidden'];
				
						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp;
						$detailInv6[$val]['so_number'] 		     	= $no_so;
						$detailInv6[$val]['no_invoice'] 		    = $no_invoice;
						$detailInv6[$val]['tgl_invoice']      		= $Tgl_Invoice;
						$detailInv6[$val]['id_customer']	 	    = $id_customer;
						$detailInv6[$val]['nm_customer'] 		    = $nm_customer;
						$detailInv6[$val]['jenis_invoice'] 		    = $jenis_invoice;
						$detailInv6[$val]['nm_material']	        = $material_name6;
						$detailInv6[$val]['unit']	                = $unit6;
						$detailInv6[$val]['harga_satuan']	        = 0;
						$detailInv6[$val]['harga_satuan_idr']	    = 0;
						$detailInv6[$val]['qty']	                = 0;
						$detailInv6[$val]['harga_total']	        = $harga_tot6_hidden;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6_hidden*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
					}
				}
			}
		
			$get_bill_so = $this->db->select('
							SUM(uang_muka_persen) AS uang_muka_persen,
							SUM(uang_muka) AS uang_muka,
							SUM(uang_muka_idr) AS uang_muka_idr,
							SUM(uang_muka_invoice) AS uang_muka_invoice,
							SUM(uang_muka_invoice_idr) AS uang_muka_invoice_idr,
							SUM(retensi) AS retensi,
							SUM(retensi_idr) AS retensi_idr,
							SUM(retensi_um) AS retensi_um,
							SUM(retensi_um_idr) AS retensi_um_idr,
							SUM(uang_muka_persen2) AS uang_muka_persen2,
							SUM(uang_muka2) AS uang_muka2,
							SUM(uang_muka_idr2) AS uang_muka_idr2
							')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
			
			if($jenis_invoice=='uang muka' && $um_persen2 < 1){ 
				$ArrBillSO = [
					'uang_muka_persen' => $get_bill_so[0]->uang_muka_persen + $um_persen,
					'uang_muka' => $get_bill_so[0]->uang_muka + $total_invoice,
					'uang_muka_idr' => $get_bill_so[0]->uang_muka_idr + $total_invoice_idr
				];
			}
			
			if($jenis_invoice=='uang muka' && $um_persen2 > 0){ 
				$ArrBillSO = [
					'uang_muka_persen2' => $get_bill_so[0]->uang_muka_persen2 + $um_persen2,
					'uang_muka2' => $get_bill_so[0]->uang_muka2 + $total_invoice,
					'uang_muka_idr2' => $get_bill_so[0]->uang_muka_idr2 + $total_invoice_idr,
					'retensi' => $get_bill_so[0]->retensi + $retensi,
					'retensi_idr' => $get_bill_so[0]->retensi_idr + $retensi_idr,
					'retensi_um' => $get_bill_so[0]->retensi_um + $retensi,
					'retensi_um_idr' => $get_bill_so[0]->retensi_um_idr + $retensi_idr
				];
			}
			
			if($jenis_invoice=='progress'){
				$ArrBillSO = [
					'uang_muka' => $get_bill_so[0]->uang_muka + $total_um,
					'uang_muka_idr' => $get_bill_so[0]->uang_muka_idr + $total_um_idr,
					'uang_muka_invoice' => $get_bill_so[0]->uang_muka_invoice + $total_um,
					'uang_muka_invoice_idr' => $get_bill_so[0]->uang_muka_invoice_idr + $total_um_idr,
					'persentase_progress' => $umpersen,
					'retensi' => $get_bill_so[0]->retensi + $retensi,
					'retensi_idr' => $get_bill_so[0]->retensi_idr + $retensi_idr
				];
			}
			
			$ArrUM = [
				'proses_inv' => '1'
			];
			
			if($jenis_invoice == 'progress'){
				$stsx = (($umpersen + $penagihan[0]->progress_persen) < 100)?11:12;
			}
			if($jenis_invoice == 'uang muka' OR $jenis_invoice == 'retensi'){
				$stsx = 12;
			}
			
			$progress_pex = $umpersen + $penagihan[0]->progress_persen;
			if($jenis_invoice == 'retensi'){
				$progress_pex = 100;
			}
			
			$ArrPERSEN = [
				'progress_persen' => $progress_pex,
				'status'	=> $stsx,
				'real_tagih_usd'	=> $this->input->post('total_invoice_hidden') ,
				'real_tagih_idr'	=> $this->input->post('total_invoice_hidden') * $kurs
			];
			
			//ARWANT
			$ArrUpdateQty = array();
			$nox = 0;
			if($jenis_invoice == 'progress'){
				if(!empty($_POST['data1'])){	
					foreach($_POST['data1'] as $d1){ $nox++;
						$ArrUpdateQty[$nox]['id']		= $d1['id'];
						$ArrUpdateQty[$nox]['qty_inv']	= $d1['qty_sudah'] + $d1['qty'];
					}
				}
			}
			
			// print_r($headerinv);
			// print_r($detailInv1);
			// print_r($detailInv2);
			// print_r($detailInv3);
			// print_r($detailInv4);
			// print_r($detailInv5);
			// print_r($detailInv6);
			
			// print_r($ArrBillSO);
			// print_r($ArrUM);
			// echo $um_persen;
			// print_r($ArrPERSEN);
			// exit;
			
			$this->db->trans_start();
				$this->db->insert('tr_invoice_header',$headerinv);
				if(!empty($detailInv1)){
					$this->db->insert_batch('tr_invoice_detail',$detailInv1);
				}
				if(!empty($detailInv2)){
					$this->db->insert_batch('tr_invoice_detail',$detailInv2);
				}
				if(!empty($detailInv3)){
					$this->db->insert_batch('tr_invoice_detail',$detailInv3);
				}
				if(!empty($detailInv4)){
					$this->db->insert_batch('tr_invoice_detail',$detailInv4);
				}
				if(!empty($detailInv5)){
					$this->db->insert_batch('tr_invoice_detail',$detailInv5);
				}
				if(!empty($detailInv6)){
					$this->db->insert_batch('tr_invoice_detail',$detailInv6);
				}
				
				if(!empty($ArrBillSO)){
					$this->db->where('no_ipp',$no_ipp);
					$this->db->update('billing_so',$ArrBillSO);
				}
				
				
				// $this->db->where('id',$id);
				// $this->db->update('billing_top',$ArrUM);
				if(!empty($ArrPERSEN)){
					$this->db->where('id',$id);
					$this->db->update('penagihan',$ArrPERSEN);
				}
				
				if(!empty($ArrUpdateQty)){
					$this->db->update_batch('billing_so_product', $ArrUpdateQty, 'id');
				}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				 $this->db->trans_rollback();
				 $Arr_Return		= array(
						'status'		=> 2,
						'pesan'			=> 'Save Process Failed. Please Try Again...'
				   );
			}else{
				$this->db->trans_commit();
				$Arr_Return		= array(
					'status'		=> 1,
					'pesan'			=> 'Save Process Success. Thank You & Have A Nice Day...'
				);
				history('Create invoice '.$jenis_invoice.' '.$id);
			}
			echo json_encode($Arr_Return);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['read'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}

			$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
			
			$id    		= $this->uri->segment(3);
			$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
			$nomor_id 	= explode(",",$penagihan[0]->no_so); 
			// echo $nomor_id;exit;
			$getBq 		= $this->db->select('no_po')->where_in('id',$nomor_id)->get('billing_top')->result_array();
			
			$in_ipp = [];
			$in_bq = [];
			
			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= get_nomor_so($valx['no_po']);
			}

			$jenis  	= 'progress';
			
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();		
			$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
			$getEngCost	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
			$getPackCost= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
			$getTruck	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
			$non_frp	= $this->db->select('*')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
			$material	= $this->db->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
			$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
			
			$get_kurs	= $this->db->select('MAX(kurs_usd_dipakai) AS kurs, SUM(uang_muka_persen) AS uang_muka_persen, SUM(uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
			
			$get_tagih		= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
			$uang_muka_persen = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
			$down_payment = (!empty($get_tagih))?$get_tagih[0]->real_tagih_usd:0;
			
			$uang_muka_persen2 = 0;
			$down_payment2 = 0;
			if(count($get_tagih) > 1){
				$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->real_tagih_usd:0;
			}
			
			$data2 = array(
				'title'			=> 'Indeks Of Create Invoice Progress',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'getHeader'		=> $getHeader,
				'getDetail' 	=> $getDetail,
				'getEngCost' 	=> $getEngCost,
				'getPackCost' 	=> $getPackCost,
				'getTruck' 		=> $getTruck,
				'non_frp'		=> $non_frp,
				'material'		=> $material,
				'list_top'		=> $list_top,
				'in_ipp'		=> implode(', ',$in_ipp),
				'in_bq'			=> implode(', ',$in_bq),
				'in_so'			=> implode(', ',$in_so),
				'arr_in_ipp'	=> $in_ipp,
				'penagihan'		=> $penagihan,
				'kurs'			=> $get_kurs[0]->kurs,
				'uang_muka_persen'	=> $uang_muka_persen,
				'uang_muka_persen2'	=> $uang_muka_persen2,
				'down_payment'	=> $down_payment,
				'down_payment2'	=> $down_payment2,
				'id'			=> $id
			);
			$this->load->view('Penagihan/create_progress',$data2);
		}
	}
	
	public function create_um(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		
		$id    		= $this->uri->segment(3);
		$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
		$nomor_id 	= explode(",",$penagihan[0]->no_so); 
		// echo $nomor_id;exit;
		$getBq 		= $this->db->select('no_po')->where_in('id',$nomor_id)->get('billing_top')->result_array();
		
		$in_ipp = [];
		$in_bq = [];
		
		foreach($getBq AS $val => $valx){
			$in_ipp[$val] 	= $valx['no_po'];
			$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
			$in_so[$val] 	= get_nomor_so($valx['no_po']);
		}

		$jenis  	= 'progress';
		
		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();		
		$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
		$getEngCost	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
		$getPackCost= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
		$getTruck	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
		$non_frp	= $this->db->select('*')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
		$material	= $this->db->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
		
		$get_kurs	= $this->db->select('MAX(kurs_usd_dipakai) AS kurs, SUM(uang_muka_persen) AS uang_muka_persen, SUM(uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
		
		$data = array(
			'title'			=> 'Indeks Of Create Invoice Uang Muka',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'getHeader'		=> $getHeader,
			'getDetail' 	=> $getDetail,
			'getEngCost' 	=> $getEngCost,
			'getPackCost' 	=> $getPackCost,
			'getTruck' 		=> $getTruck,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'list_top'		=> $list_top,
			'in_ipp'		=> implode(', ',$in_ipp),
			'in_bq'			=> implode(', ',$in_bq),
			'in_so'			=> implode(', ',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'uang_muka_persen'	=> $get_kurs[0]->uang_muka_persen,
			'uang_muka_persen2'	=> $get_kurs[0]->uang_muka_persen2,
			'id'			=> $id
		);
		$this->load->view('Penagihan/create_um',$data);
	}
	
	public function create_retensi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		
		$id    		= $this->uri->segment(3);
		$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
		$nomor_id 	= explode(",",$penagihan[0]->no_so); 
		// echo $nomor_id;exit;
		$getBq 		= $this->db->where_in('id',$nomor_id)->get('billing_top')->result_array();
		
		$in_ipp = [];
		$in_bq = [];
		
		foreach($getBq AS $val => $valx){
			$in_ipp[$val] 	= $valx['no_po'];
			$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
			$in_so[$val] 	= get_nomor_so($valx['no_po']);
		}

		$jenis  	= 'progress';
		
		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();		
		$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
		$getEngCost	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
		$getPackCost= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
		$getTruck	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
		$non_frp	= $this->db->select('*')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
		$material	= $this->db->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
		
		$get_kurs	= $this->db->select('MAX(kurs_usd_dipakai) AS kurs, SUM(uang_muka_persen) AS uang_muka_persen, SUM(uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
		
		$data = array(
			'title'			=> 'Indeks Of Create Invoice Retensi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_retensi'	=> $getBq,
			'getHeader'		=> $getHeader,
			'getDetail' 	=> $getDetail,
			'getEngCost' 	=> $getEngCost,
			'getPackCost' 	=> $getPackCost,
			'getTruck' 		=> $getTruck,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'list_top'		=> $list_top,
			'in_ipp'		=> implode(', ',$in_ipp),
			'in_bq'			=> implode(', ',$in_bq),
			'in_so'			=> implode(', ',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'uang_muka_persen'	=> $get_kurs[0]->uang_muka_persen,
			'uang_muka_persen2'	=> $get_kurs[0]->uang_muka_persen2,
			'id'			=> $id
		);
		$this->load->view('Penagihan/create_retensi',$data);
	}
	
}