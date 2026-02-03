<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penagihan extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('penagihan_model');
		$this->load->model('Acc_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	public function index(){
		$this->penagihan_model->index();
	}
	public function delivery(){
		$this->penagihan_model->index('delivery');
	}
	public function instalasi(){
		$this->penagihan_model->index('instalasi');
	}
	public function server_side_penagihan(){
		$this->penagihan_model->server_side_penagihan();
	}

	public function add(){
		$this->penagihan_model->add();
	}

	public function server_side_penagihan_add(){
		$this->penagihan_model->server_side_penagihan_add();
	}

	public function get_po(){
		$this->penagihan_model->get_po();
	}

	public function create_progress(){
		$this->penagihan_model->create_progress();
	}

	public function create_um(){
		$this->penagihan_model->create_um();
	}

	public function create_retensi(){
		$this->penagihan_model->create_retensi();
	}

	public function add_new(){
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
            $dv    = implode(",", $data['dv']);
			$result_data 	= $this->db->query("SELECT * FROM billing_so_gabung WHERE id IN ".$dtImplode." ORDER BY id ")->result_array();

			$max_num 		= $this->db->select('MAX(id) AS nomor_max')->get('penagihan')->result();
			$id_tagih 		= $max_num[0]->nomor_max + 1;

			$SUM_USD = 0;
			$SUM_IDR = 0;
			$Update_b = [];

		$this->db->trans_start();
			foreach($result_data AS $val => $valx){
				$SUM_USD += $valx['total_deal_usd'];
				$SUM_IDR += $valx['total_deal_idr']; 
				$no_ipp = str_replace('BQ-','',$valx['no_ipp']);

				$Update_b[$val]['id'] = $valx['id'];
				$Update_b[$val]['id_penagihan'] = $id_tagih;
				if($valx['jenis']=='pipa'){
					$this->db->query("update billing_so set status='1' WHERE id ='".$valx['id']."' and status='0' ");
				}else{
					$this->db->query("update ".DBTANKI.".ipp_header set status_inv='1' WHERE no_ipp ='".$valx['id']."' and status_inv='0' ");
				}

				$base_cur = $valx['base_cur'];
			}
			$header = [
				'no_so' => $dtImplode2,
				'no_po' => $data['no_po'],
				'no_ipp' => $no_ipp,
				'project' => NULL,
				'kode_customer' => $data['customer'],
				'customer' => get_name('customer','nm_customer','id_customer',$data['customer']),
				'keterangan' => NULL,
				'plan_tagih_date' => date("Y-m-d"),
				'plan_tagih_usd' => $SUM_USD,
				'plan_tagih_idr' => $SUM_IDR,
				'type' => $data['type'],
				'base_cur' => $base_cur,
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
				'delivery_no' => $dv,
				'term_delivery' => $data['term_delivery'],
				'created_by' => $this->session->userdata['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')

			];

			// print_r($header);
			// print_r($Update_b);
			// exit;
				$this->db->insert('penagihan', $header);
				//$this->db->update_batch('billing_top', $Update_b, 'id');

				// update billing so status
//				$this->db->query("update billing_so set status='1' WHERE id IN ".$dtImplode." and status='0' ");

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
			$customer = $this->db->order_by('nm_customer','asc')->group_by('kode_customer')->get('billing_so_gabung')->result();
			$no_po = $this->db->order_by('no_po','asc')->group_by('no_po')->get_where('billing_so_gabung', array('no_po <>'=> NULL, 'no_po <>'=> '0'))->result();
			$dataDV = $this->db->query("SELECT * FROM delivery_product")->result();

			
			$data = array(
				'title'			=> 'Indeks Of Add Billing',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'customer'		=> $customer,
				'no_po'			=> $no_po,
				'dataDV'		=> $dataDV
			);

			$this->load->view('Penagihan/add_new',$data);
		}
	}

	public function server_side_penagihan_add_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		if($requestData['no_po']=='0') die();
		$fetch			= $this->query_data_penagihan_add_new(
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
	public function query_data_penagihan_add_new($customer, $type, $no_po, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_customer = '';
		if($customer != '0'){
		}
			$where_customer = " AND b.kode_customer='".$customer."'";

		$where_no_po = '';
		if($no_po != '0'){
		}
			$where_no_po = " AND b.no_po='".$no_po."'";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				b.id,
				b.project,
				b.kode_customer,
				b.nm_customer AS customer,
				b.no_po AS no_pox,
				IFNULL(c.so_number,b.no_so) so_number
			FROM
				billing_so_gabung b
				LEFT JOIN so_number c ON replace(c.id_bq,'BQ-','') = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE (status=1 or status=0) ".$where_customer." ".$where_no_po." AND (
				IFNULL(c.so_number,b.no_so) LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'so_number',
			2 => 'no_pox',
			3 => 'project',
			4 => 'customer',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_um_new_delivery(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		
		$id    		= $this->uri->segment(3);
		$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
		$penagihan_detail = $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->result();
		$nomor_id 	= explode(",",$penagihan[0]->no_so);
		
			
		
		$getBq 		= $this->db->select('no_ipp as no_po, base_cur')->where_in('id',$nomor_id)->get('billing_so')->result_array();
		// print_r($getBq);
		// exit;
		
		$in_ipp = [];
		$in_bq = [];
		foreach($getBq AS $val => $valx){
			$in_ipp[$val]	= $valx['no_po'];
			$in_bq[$val]	= 'BQ-'.$valx['no_po'];
			$in_so[$val]	= get_nomor_so($valx['no_po']); 
			$base_cur		= $valx['base_cur'];
		}
		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
		$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
		$getEngCost	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
		$getPackCost= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
		$getTruck	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
		$non_frp	= $this->db->select('*')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya' OR category='other')")->where_in('no_ipp',$in_ipp)->get()->result_array();
		$material	= $this->db->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
		if($penagihan[0]->kurs_jual==0){
			$get_kurs	= $this->db->select("(kurs_usd_dipakai) AS kurs, 0 AS uang_muka_persen, '0' AS uang_muka_persen2")->where_in("no_ipp",$in_ipp)->get("billing_so")->result();
		}else{
			$get_kurs	= $this->db->select("(kurs_jual) AS kurs, persentase AS uang_muka_persen, '0' AS uang_muka_persen2")->where("id",$id)->get("penagihan")->result();
		}

		$approval	= $this->uri->segment(4);
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
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'uang_muka_persen'	=> $get_kurs[0]->uang_muka_persen,
			'uang_muka_persen2'	=> $get_kurs[0]->uang_muka_persen2,
			'id'			=> $id,
			'approval'		=> $approval
		);
		$this->load->view('Penagihan/create_um_new_delivery',$data);
	}
	public function create_um_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$base_cur='';
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$id    		= $this->uri->segment(3);
		$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
		$penagihan_detail = $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->result();
		$nomor_id 	= explode(",",$penagihan[0]->no_so);
		$getBq 		= $this->db->select('no_ipp as no_po, base_cur, no_so')->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();
		$in_ipp = [];
		$in_bq = [];
		foreach($getBq AS $val => $valx){
			$in_ipp[$val]	= $valx['no_po'];
			$in_bq[$val]	= 'BQ-'.$valx['no_po'];
			$in_so[$val]	= ($valx['no_so']==''?get_nomor_so($valx['no_po']):$valx['no_so']);
			$base_cur		= ($base_cur==''?$penagihan[0]->base_cur:$base_cur);//$valx['base_cur'];
		}
		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
		$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
		$getEngCost	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
		$getPackCost= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
		$getTruck	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
		$non_frp	= $this->db->select('*')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
		$material	= $this->db->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();

		$getTanki	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get(DBTANKI.'.billing_product')->result_array();

		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
		
		if($penagihan[0]->kurs_jual==0){
			$get_kurs	= $this->db->select("(kurs_usd_dipakai) AS kurs, 0 AS uang_muka_persen, '0' AS uang_muka_persen2")->where_in("no_ipp",$in_ipp)->get("billing_so_gabung")->result();
		}else{
			$get_kurs	= $this->db->select("(kurs_jual) AS kurs, persentase AS uang_muka_persen, '0' AS uang_muka_persen2")->where("id",$id)->get("penagihan")->result();
		}
        

		$approval	= $this->uri->segment(4);
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
			'getTanki' 		=> $getTanki,
			'material'		=> $material,
			'list_top'		=> $list_top,
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'uang_muka_persen'	=> $get_kurs[0]->uang_muka_persen,
			'uang_muka_persen2'	=> $get_kurs[0]->uang_muka_persen2,
			'id'			=> $id,
			'approval'		=> $approval
		);
		$this->load->view('Penagihan/create_um_new',$data);

	}

	public function create_progress_new(){
		if($this->input->post()){
			$data_session	= $this->session->userdata;

			$id			= $this->input->post('id');
			$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
			$nomor_id 	= explode(",",$penagihan[0]->no_so);
			$getBq 		= $this->db->select('no_ipp as no_po')->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();

			$in_ipp = [];
			$in_bq = [];

			$dtdelivery_no='';
			if(isset($_POST['delivery_no'])){
				$dtdelivery_no	= implode(",", $_POST['delivery_no']);
			}
			$sqldev_product="";
			$dt_cogs=0;
			if($dtdelivery_no!=""){
				$dt_delivery = $this->db->query("SELECT nilai_cogs FROM delivery_product_detail where kode_delivery in ('".implode("','", $_POST['delivery_no'])."')")->result();
				foreach ($dt_delivery as $val => $valx) {
					$dt_cogs=($valx->nilai_cogs +$dt_cogs);
				}
//				$sqldev_product="update delivery_product set st_cogs='1' where kode_delivery in ('".implode("','", $_POST['delivery_no'])."')";
			}

			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				//$in_so[$val] 	= get_nomor_so($valx['no_po']);
			}

			$Tgl_Invoice			= $this->input->post('tgl_inv');
			$Bulan_Invoice			= date('n',strtotime($Tgl_Invoice));
			$Tahun_Invoice			= date('Y',strtotime($Tgl_Invoice));
			$data_session 		    = $this->session->userdata;

			$no_ipp                 = $this->input->post('no_ipp');
			$so_number				= $this->input->post('no_so');
// sementara diganti
//			$no_invoice 			= gen_invoice($no_ipp);
			$no_invoice 			= $this->input->post('nomor_faktur');
			$id_customer			= $this->input->post('id_customer');
			$nm_customer			= $this->input->post('nm_customer');
			$no_bq                  = 'BQ-'.$no_ipp;
			$kurs                   = str_replace(',','',$this->input->post('kurs'));
			$jenis_invoice 			= strtolower($this->input->post('type'));
			$base_cur				= $this->input->post('base_cur');
			$um_persen2				= str_replace(',','',$this->input->post('um_persen2'));
			$umpersen				= str_replace(',','',$this->input->post('umpersen'));
			$grand_total          	= str_replace(',','',$this->input->post('grand_total'));
			$ppnselect				= $this->input->post('ppnselect');
			$progressx				= $this->input->post('progressx');
			$persen_retensi2		= $this->input->post('persen_retensi2');
			$persen_retensi			= $this->input->post('persen_retensi');
if($base_cur=='USD'){
			$total_invoice          = $this->input->post('total_invoice');
			$total_invoice_idr      = $this->input->post('total_invoice')*$kurs;
			$total_um               = $this->input->post('down_payment');
			$total_um_idr           = $this->input->post('down_payment')*$kurs;
			$um_persen				= str_replace(',','',$this->input->post('um_persen'));
			$total_gab_product      = ($this->input->post('tot_product'))+($this->input->post('total_material'))+($this->input->post('total_bq_nf'));
			$total_gab_product_idr  = ($this->input->post('tot_product')*$kurs)+($this->input->post('total_material')*$kurs)+($this->input->post('total_bq_nf')*$kurs);

			$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi'));
			$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi2'));

			$diskon = (!empty($this->input->post('diskon')))?$this->input->post('diskon'):str_replace(',','',$this->input->post('diskon'));
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
				$totaluangmuka2 = $this->input->post('grand_total') - $this->input->post('down_payment');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			if($jenis_invoice=='progress' && $um_persen2 != 0){
				$totaluangmuka2 = $this->input->post('down_payment2');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			//INSERT DATABASE TR INVOICE HEADER

			$headerinv = [
				'keterangan'				=> $this->input->post('keterangan'),
				'ppnselect' 		     	=> $ppnselect,
				'progressx' 		     	=> $progressx,
				'persen_retensi2'			=> $persen_retensi2,
				'persen_retensi'			=> $persen_retensi,
				'no_invoice' 		     	=> $no_invoice,
				'tgl_invoice'      		    => $Tgl_Invoice,
				'kode_customer'	 	      	=> $id_customer,
				'nm_customer' 		      	=> $nm_customer,
				'persentase' 		        => $progress,
				'progress_persen' 			=> $this->input->post('persen'),
				'total_product'	         	=> $this->input->post('tot_product'),
				'total_product_idr'	        => $this->input->post('tot_product')*$kurs,
				'total_gab_product'	        => $total_gab_product,
				'total_gab_product_idr'	    => $total_gab_product_idr,
				'total_material'	        => $this->input->post('total_material'),
				'total_material_idr'	    => $this->input->post('total_material')*$kurs,
				'total_bq'	                => $this->input->post('total_bq_nf'),
				'total_bq_idr'	            => $this->input->post('total_bq_nf')*$kurs,
				'total_enginering'	        => $this->input->post('total_enginering'),
				'total_enginering_idr'	    => $this->input->post('total_enginering')*$kurs,
				'total_packing'	            => $this->input->post('total_packing'),
				'total_packing_idr'	        => $this->input->post('total_packing')*$kurs,
				'total_trucking'	        => $this->input->post('total_trucking'),
				'total_trucking_idr'	    => $this->input->post('total_trucking')*$kurs,
				'total_dpp_usd'	            => $this->input->post('grand_total'),
				'total_dpp_rp'	            => $this->input->post('grand_total')*$kurs,
				'total_diskon'	            => $diskon,
				'total_diskon_idr'	        => $diskon * $kurs,
				'total_retensi'	            => $this->input->post('potongan_retensi'),
				'total_retensi_idr'	        => $this->input->post('potongan_retensi')*$kurs,
				'total_ppn'	                => $this->input->post('ppn'),
				'total_ppn_idr'	            => $this->input->post('ppn')*$kurs,
				'total_invoice'	            => $this->input->post('total_invoice'),
				'total_invoice_idr'	        => $this->input->post('total_invoice')*$kurs,
				'total_um'	                => $this->input->post('down_payment'),
				'total_um_idr'	            => $this->input->post('down_payment')*$kurs,
				'kurs_jual'	                => $kurs,
				'no_po'	                    => $this->input->post('nomor_po'),
				'no_faktur'	                => $this->input->post('nomor_faktur'),
				'no_pajak'	                => $this->input->post('nomor_pajak'),
				'payment_term'	            => $this->input->post('top'),
				'updated_by' 	            => $data_session['ORI_User']['username'],
				'updated_date' 	            => date('Y-m-d H:i:s'),
				'total_um2'	                => $totaluangmuka2,
				'total_um_idr2'	            => $totaluangmuka2_idr,
				'id_top'	            	=> $id,
				'base_cur'					=> $base_cur,
				'total_retensi2'			=> $retensi_ppn,
				'total_retensi2_idr'		=> $retensi_ppn*$kurs,
				'sisa_invoice'	        	=> $this->input->post('total_invoice'),
				'sisa_invoice_idr'	        => $this->input->post('total_invoice')*$kurs,
				'so_number'					=> $so_number
			];

			if($jenis_invoice=='progress'){
				$detailInv1 = [];
				if(!empty($_POST['data1'])){
					foreach($_POST['data1'] as $val => $d1){
						$nm_material	= $d1['material_name1'];
						$product_cust	= $d1['product_cust'];
						$product_desc	= $d1['product_desc'];
						$diameter_1	= $d1['diameter_1'];
						$diameter_2	= $d1['diameter_2'];
						$liner		= $d1['liner'];
						$pressure	= $d1['pressure'];
						$id_milik	= $d1['id_milik'];
						$spesifikasi	= $d1['spesifikasi'];
						$harga_sat	= $d1['harga_sat'];
						$qty=0;
						$checked='';
						if(isset($d1['qty'])){
							$qty	= $d1['qty'];
							$checked='1';
						}
						$unit1		= $d1['unit1'];
						$harga_tot	= $d1['harga_tot'];
						$no_ippdtl	= $d1['no_ipp'];
						$no_sodtl	= $d1['no_so'];
						$qty_ori	= $d1['qty_ori'];
						$qty_belum	= $d1['qty_belum'];

						$detailInv1[$val]['id_penagihan']		= $id;
						$detailInv1[$val]['id_bq'] 		     	= $no_bq;
						$detailInv1[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv1[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv1[$val]['spesifikasi']	    = $spesifikasi;
						$detailInv1[$val]['unit']	            = $unit1;
						$detailInv1[$val]['harga_satuan']	    = $harga_sat;
						$detailInv1[$val]['harga_satuan_idr']	= $harga_sat*$kurs;
						$detailInv1[$val]['qty']	            = $qty;
						$detailInv1[$val]['harga_total']	    = $harga_tot;
						$detailInv1[$val]['harga_total_idr']	= $harga_tot*$kurs;
						$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
						$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv1[$val]['qty_total']			= $qty_ori;
						$detailInv1[$val]['qty_sisa']			= $qty_belum;
						$detailInv1[$val]['checked']			= $checked;
						$detailInv1[$val]['id_milik']	    	= $d1['id_milik'];
						$detailInv1[$val]['cogs']	    		= $d1['cogs'];
					}
				}

				$detailInv2 = [];
				if(!empty($_POST['data2'])){
					foreach($_POST['data2'] as $val => $d2){
						$material_name2	= $d2['material_name2'];
						$material_desc2	= $d2['material_desc2'];
						$harga_sat2    = $d2['harga_sat2'];
						$qty2=0;$checked='';
						if(isset($d2['qty2'])){
							$qty2	= $d2['qty2'];
							if($qty2>0) $checked='1';
						}
						$unit2		= $d2['unit2'];
						$harga_tot2	= $d2['harga_tot2'];
						$no_ippdtl	= $d2['no_ipp'];
						$no_sodtl	= $d2['no_so'];
						$qty2_ori	= $d2['qty2_ori'];
						$qty2_belum	= $d2['qty2_belum'];

						$detailInv2[$val]['id_penagihan']		= $id;
						$detailInv2[$val]['id_bq'] 		     	= $no_bq;
						$detailInv2[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv2[$val]['so_number'] 		    = $no_sodtl;
						$detailInv2[$val]['no_invoice'] 		= $no_invoice;
						$detailInv2[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv2[$val]['id_customer']	 	= $id_customer;
						$detailInv2[$val]['nm_customer'] 		= $nm_customer;
						$detailInv2[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv2[$val]['nm_material']	    = $material_name2." ".$material_desc2;
						$detailInv2[$val]['unit']	            = $unit2;
						$detailInv2[$val]['harga_satuan']	    = $harga_sat2;
						$detailInv2[$val]['harga_satuan_idr']	= $harga_sat2*$kurs;
						$detailInv2[$val]['qty']	            = $qty2;
						$detailInv2[$val]['harga_total']	    = $harga_tot2;
						$detailInv2[$val]['harga_total_idr']	= $harga_tot2*$kurs;
						$detailInv2[$val]['kategori_detail']	= 'BQ';
						$detailInv2[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv2[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv2[$val]['desc']	    		= $material_desc2;
						$detailInv2[$val]['qty_total']			= $qty2_ori;
						$detailInv2[$val]['qty_sisa']			= $qty2_belum;
						$detailInv2[$val]['checked']			= $checked;
						$detailInv2[$val]['id_milik']	    	= $d2['id_milik'];
					}
				}

				$detailInv3 = [];
				if(!empty($_POST['data3'])){
					foreach($_POST['data3'] as $val => $d3){
						$material_name3	= $d3['material_name3'];
						$harga_sat3		= $d3['harga_sat3'];
						$qty3=0;$checked='';
						if(isset($d3['qty3'])){
							$qty3	= $d3['qty3'];
							if($qty3>0) $checked='1';
						}
						$unit3			= $d3['unit3'];
						$harga_tot3		= $d3['harga_tot3'];
						$no_ippdtl		= $d3['no_ipp'];
						$no_sodtl		= $d3['no_so'];
						$product_cust	= $d3['product_cust'];
						$product_desc	= $d3['product_desc'];
						$qty3_ori		= $d3['qty3_ori'];
						$qty3_belum		= $d3['qty3_belum'];

						$detailInv3[$val]['id_penagihan']		= $id;
						$detailInv3[$val]['id_bq'] 		     	= $no_bq;
						$detailInv3[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv3[$val]['so_number'] 		    = $no_sodtl;
						$detailInv3[$val]['no_invoice'] 		= $no_invoice;
						$detailInv3[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv3[$val]['id_customer']	 	= $id_customer;
						$detailInv3[$val]['nm_customer'] 		= $nm_customer;
						$detailInv3[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv3[$val]['nm_material']	    = $material_name3;
						$detailInv3[$val]['unit']	            = $unit3;
						$detailInv3[$val]['harga_satuan']	    = $harga_sat3;
						$detailInv3[$val]['harga_satuan_idr']	= $harga_sat3*$kurs;
						$detailInv3[$val]['qty']	            = $qty3;
						$detailInv3[$val]['harga_total']	    = $harga_tot3;
						$detailInv3[$val]['harga_total_idr']	= $harga_tot3*$kurs;
						$detailInv3[$val]['kategori_detail']	= 'MATERIAL';
						$detailInv3[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv3[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv3[$val]['product_cust']	    = $product_cust;
						$detailInv3[$val]['desc']	    		= $product_desc;
						$detailInv3[$val]['qty_total']			= $qty3_ori;
						$detailInv3[$val]['qty_sisa']			= $qty3_belum;
						$detailInv3[$val]['checked']			= $checked;
						$detailInv3[$val]['id_milik']	    	= $d3['id_milik'];
					}
				}

				$detailInv4 = [];
				if(!empty($_POST['data4'])){
					foreach($_POST['data4'] as $val => $d4){
						$material_name4	= $d4['material_name4'];
						$harga_sat4		= 0;
						$qty4			= 0;
						$unit4			= $d4['unit4'];
						$harga_tot4		=0;$checked='';
						if(isset($d4['harga_tot4'])){
							$harga_tot4	= $d4['harga_tot4'];
							if($harga_tot4>0) $checked='1';
						}
						$no_ippdtl		= $d4['no_ipp'];
						$no_sodtl		= $d4['no_so'];
						$harga_tot4_ori	= $d4['harga_tot4_ori'];
						$harga_tot4_sisa= $d4['harga_tot4_sisa'];

						$detailInv4[$val]['id_penagihan']		= $id;
						$detailInv4[$val]['id_bq'] 		     	= $no_bq;
						$detailInv4[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv4[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv4[$val]['harga_total']	    = $harga_tot4;
						$detailInv4[$val]['harga_total_idr']	= $harga_tot4*$kurs;
						$detailInv4[$val]['kategori_detail']	= 'ENGINERING';
						$detailInv4[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv4[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv4[$val]['desc']	    		= $material_name4;
						$detailInv4[$val]['harga_total_so']		= $harga_tot4_ori;
						$detailInv4[$val]['harga_sisa_so']		= $harga_tot4_sisa;
						$detailInv4[$val]['checked']			= $checked;
						$detailInv4[$val]['id_milik']	    	= $d4['id_milik'];
					}
				}

				$detailInv5 = [];
				if(!empty($_POST['data5'])){
					foreach($_POST['data5'] as $val => $d5){
						$material_name5          = $d5['material_name5'];
						$unit5                   = $d5['unit5'];
						$harga_tot5=0;$checked='';
						if(isset($d5['harga_tot5'])){
							$harga_tot5   = $d5['harga_tot5'];
							if($harga_tot5>0) $checked='1';
						}
						$no_ippdtl		= $d5['no_ipp'];
						$no_sodtl		= $d5['no_so'];
						$harga_tot5_ori	= $d5['harga_tot5_ori'];
						$harga_tot5_sisa= $d5['harga_tot5_sisa'];

						$detailInv5[$val]['id_penagihan']			= $id;
						$detailInv5[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv5[$val]['no_ipp'] 		     	= $no_ippdtl;
						$detailInv5[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv5[$val]['harga_total']	        = $harga_tot5;
						$detailInv5[$val]['harga_total_idr']	    = $harga_tot5*$kurs;
						$detailInv5[$val]['kategori_detail']	    = 'PACKING';
						$detailInv5[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv5[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv5[$val]['desc']	    			= $material_name5;
						$detailInv5[$val]['harga_total_so']			= $harga_tot5_ori;
						$detailInv5[$val]['harga_sisa_so']			= $harga_tot5_sisa;
						$detailInv5[$val]['checked']				= $checked;
						$detailInv5[$val]['id_milik']	    		= $d5['id_milik'];
					}
				}

				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6	= $d6['material_name6'];
						$harga_sat6		= 0;
						$qty6			= 0;
						$unit6			= $d6['unit6'];
						$harga_tot6		=0;$checked='';
						if(isset($d6['harga_tot6'])){
							$harga_tot6	= $d6['harga_tot6'];
							if($harga_tot6>0) $checked='1';
						}
						$no_ippdtl			= $d6['no_ipp'];
						$no_sodtl			= $d6['no_so'];
						$harga_tot6_ori		= $d6['harga_tot6_ori'];
						$harga_tot6_sisa	= $d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ippdtl;
						$detailInv6[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];


					}
				}

				$detailInv9 = [];
				if(!empty($_POST['data9'])){
					foreach($_POST['data9'] as $val => $d){
						$material_name9	= $d9['material_name9'];
						$harga_sat9		= $d9['harga_sat9'];
						$qty9=0;$checked='';
						if(isset($d9['qty9'])){
							$qty9	= $d9['qty9'];
							if($qty9>0) $checked='1';
						}
						$unit9			= $d9['unit9'];
						$harga_tot9		= $d9['harga_tot9'];
						$no_ippdtl		= $d9['no_ipp'];
						$no_sodtl		= $d9['no_so'];
						$product_cust	= $d9['product_cust'];
						$product_desc	= $d9['product_desc'];
						$qty9_ori		= $d9['qty9_ori'];
						$qty9_belum		= $d9['qty9_belum'];

						$detailInv9[$val]['id_penagihan']		= $id;
						$detailInv9[$val]['id_bq'] 		     	= $no_bq;
						$detailInv9[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv9[$val]['so_number'] 		    = $no_sodtl;
						$detailInv9[$val]['no_invoice'] 		= $no_invoice;
						$detailInv9[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv9[$val]['id_customer']	 	= $id_customer;
						$detailInv9[$val]['nm_customer'] 		= $nm_customer;
						$detailInv9[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv9[$val]['nm_material']	    = $material_name9;
						$detailInv9[$val]['unit']	            = $unit9;
						$detailInv9[$val]['harga_satuan']	    = $harga_sat9;
						$detailInv9[$val]['harga_satuan_idr']	= $harga_sat9*$kurs;
						$detailInv9[$val]['qty']	            = $qty9;
						$detailInv9[$val]['harga_total']	    = $harga_tot9;
						$detailInv9[$val]['harga_total_idr']	= $harga_tot9*$kurs;
						$detailInv9[$val]['kategori_detail']	= 'OTHER';
						$detailInv9[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv9[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv9[$val]['product_cust']	    = $product_cust;
						$detailInv9[$val]['desc']	    		= $product_desc;
						$detailInv9[$val]['qty_total']			= $qty9_ori;
						$detailInv9[$val]['qty_sisa']			= $qty9_belum;
						$detailInv9[$val]['checked']			= $checked;
						$detailInv9[$val]['id_milik']	    	= $d9['id_milik'];
					}
				}

			}

			if($jenis_invoice=='retensi'){
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$no_ipp_dtl		         = $d6['no_ipp'];
						$no_so_dtl		         = $d6['no_so'];
						$harga_sat6       		 = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6       		 = $d6['harga_tot6'];
						$harga_tot6_ori			 = $harga_tot6;//$d6['harga_tot6_ori'];
						$harga_tot6_sisa		 = 0;//$d6['harga_tot6_sisa'];
						if($harga_tot6>0) $checked='1';

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = 'BQ-'.$no_ipp_dtl;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp_dtl;
						$detailInv6[$val]['so_number'] 		     	= $no_so_dtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						//$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];
					}
				}
			}
/*
			$get_bill_so = $this->db->query("select * from billing_so_gabung where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr;
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka' && $um_persen2 < 1){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen']=$valx->uang_muka_persen + $um_persen;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka + ($grand_total*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr + ($grand_total*$perseninv*$kurs);
				}
			}

			if($jenis_invoice=='uang muka' && $um_persen2 > 0){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen2']=$valx->uang_muka_persen2 + $um_persen2;
					$ArrBillSO[$nox]['uang_muka2']=$valx->uang_muka2 + ($total_invoice*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr2']=$valx->uang_muka_idr2 + ($total_invoice_idr*$kurs*$perseninv);
//					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_idr*$perseninv);
//					$ArrBillSO[$nox]['retensi_um']=$valx->retensi_um + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_um_idr + ($retensi_idr*$perseninv);
				}
			}

			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka - $total_um;
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr - ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice']=$valx->uang_muka_invoice + ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice_idr']=$valx->uang_muka_invoice_idr + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['persentase_progress']=$umpersen;
					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi_non_ppn*$perseninv);
					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_non_ppn*$kurs*$perseninv);
					$ArrBillSO[$nox]['retensi_um']=$valx->retensi + ($retensi_ppn*$perseninv);
					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_idr + ($retensi_ppn*$kurs*$perseninv);
				}
			}
*/
			$ArrUM = [
				'proses_inv' => '1'
			];

			if($jenis_invoice == 'progress'){
				$stsx = 12;
			}
			if($jenis_invoice == 'uang muka' OR $jenis_invoice == 'retensi'){
				$stsx = 12;
			}

			$progress_pex = $umpersen + $penagihan[0]->progress_persen;
			if($jenis_invoice == 'retensi'){
				$progress_pex = 100;
			}

			$ArrPERSEN = [
				'delivery_no' => $dtdelivery_no,
				'total_cogs' => $dt_cogs,
			];
}else{
//	idr
			$total_invoice          = $this->input->post('total_invoice')/$kurs;
			$total_invoice_idr      = $this->input->post('total_invoice');
			$total_um               = $this->input->post('down_payment')/$kurs;
			$total_um_idr           = $this->input->post('down_payment');
			$um_persen				= str_replace(',','',$this->input->post('um_persen'));
			$total_gab_product      = ($this->input->post('tot_product')/$kurs)+($this->input->post('total_material')/$kurs)+($this->input->post('total_bq_nf')/$kurs);
			$total_gab_product_idr  = ($this->input->post('tot_product'))+($this->input->post('total_material'))+($this->input->post('total_bq_nf'));

			$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi'));
			$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi2'));

			$diskon = (!empty($this->input->post('diskon')))?$this->input->post('diskon'):str_replace(',','',$this->input->post('diskon'));

			$retensi_FIX = 0;

			if($retensi_non_ppn <= 0 ){
				$retensi_FIX = $retensi_ppn;
			}

			if($retensi_ppn <= 0 ){
				$retensi_FIX = $retensi_non_ppn;
			}

			$retensi				=  $retensi_FIX/$kurs;
			$retensi_idr		    =  $retensi_FIX;

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
				$totaluangmuka2_idr = $this->input->post('grand_total') - $this->input->post('down_payment');
				$totaluangmuka2 = $totaluangmuka2/$kurs;
			}
			if($jenis_invoice=='progress' && $um_persen2 != 0){
				$totaluangmuka2_idr = $this->input->post('down_payment2');
				$totaluangmuka2 = $totaluangmuka2/$kurs;
			}

			//INSERT DATABASE TR INVOICE HEADER
			$headerinv = [
				'keterangan'				=> $this->input->post('keterangan'),
				'ppnselect' 		     	=> $ppnselect,
				'progressx' 		     	=> $progressx,
				'persen_retensi2'			=> $persen_retensi2,
				'persen_retensi'			=> $persen_retensi,
				'no_invoice' 		     	=> $no_invoice,
				'tgl_invoice'      		    => $Tgl_Invoice,
				'kode_customer'	 	      	=> $id_customer,
				'nm_customer' 		      	=> $nm_customer,
				'persentase' 		        => $progress,
				'progress_persen' 			=> $this->input->post('persen'),
				'total_product'	         	=> $this->input->post('tot_product')/$kurs,
				'total_product_idr'	        => $this->input->post('tot_product'),
				'total_gab_product'	        => $total_gab_product,
				'total_gab_product_idr'	    => $total_gab_product_idr,
				'total_material'	        => $this->input->post('total_material')/$kurs,
				'total_material_idr'	    => $this->input->post('total_material'),
				'total_bq'	                => $this->input->post('total_bq_nf')/$kurs,
				'total_bq_idr'	            => $this->input->post('total_bq_nf'),
				'total_enginering'	        => $this->input->post('total_enginering')/$kurs,
				'total_enginering_idr'	    => $this->input->post('total_enginering'),
				'total_packing'	            => $this->input->post('total_packing')/$kurs,
				'total_packing_idr'	        => $this->input->post('total_packing'),
				'total_trucking'	        => $this->input->post('total_trucking')/$kurs,
				'total_trucking_idr'	    => $this->input->post('total_trucking'),
				'total_dpp_usd'	            => $this->input->post('grand_total')/$kurs,
				'total_dpp_rp'	            => $this->input->post('grand_total'),
				'total_diskon'	            => $diskon/$kurs,
				'total_diskon_idr'	        => $diskon,
				'total_retensi'	            => $this->input->post('potongan_retensi')/$kurs,
				'total_retensi_idr'	        => $this->input->post('potongan_retensi'),
				'total_ppn'	                => $this->input->post('ppn')/$kurs,
				'total_ppn_idr'	            => $this->input->post('ppn'),
				'total_invoice'	            => $this->input->post('total_invoice')/$kurs,
				'total_invoice_idr'	        => $this->input->post('total_invoice'),
				'total_um'	                => $this->input->post('down_payment')/$kurs,
				'total_um_idr'	            => $this->input->post('down_payment'),
				'kurs_jual'	                => $kurs,
				'no_po'	                    => $this->input->post('nomor_po'),
				'no_faktur'	                => $this->input->post('nomor_faktur'),
				'no_pajak'	                => $this->input->post('nomor_pajak'),
				'payment_term'	            => $this->input->post('top'),
				'updated_by' 	            => $data_session['ORI_User']['username'],
				'updated_date' 	            => date('Y-m-d H:i:s'),
				'total_um2'	                => $totaluangmuka2,
				'total_um_idr2'	            => $totaluangmuka2_idr,
				'id_top'	            	=> $id,
				'base_cur'					=> $base_cur,
				'total_retensi2'			=> $retensi_ppn/$kurs,
				'total_retensi2_idr'		=> $retensi_ppn,
				'sisa_invoice'	        	=> $this->input->post('total_invoice')/$kurs,
				'sisa_invoice_idr'	        => $this->input->post('total_invoice'),
				'so_number'					=> $so_number
			];

			if($jenis_invoice=='progress'){
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
						$spesifikasi		  = $d1['spesifikasi'];
						$harga_sat     		  = $d1['harga_sat'];
						$qty=0;$checked='';
						if(isset($d1['qty'])){
							$qty              = $d1['qty'];$checked='1';
						}
						$unit1                = $d1['unit1'];
						$harga_tot     		  = $d1['harga_tot'];
						$no_ippdtl     		  = $d1['no_ipp'];
						$no_sodtl		      = $d1['no_so'];
						$qty_ori			  = $d1['qty_ori'];
						$qty_belum			  = $d1['qty_belum'];

						$detailInv1[$val]['id_penagihan']		= $id;
						$detailInv1[$val]['id_bq'] 		     	= $no_bq;
						$detailInv1[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv1[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv1[$val]['spesifikasi']	    = $spesifikasi;
						$detailInv1[$val]['unit']	            = $unit1;
						$detailInv1[$val]['harga_satuan']	    = $harga_sat/$kurs;
						$detailInv1[$val]['harga_satuan_idr']	= $harga_sat;
						$detailInv1[$val]['qty']	            = $qty;
						$detailInv1[$val]['harga_total']	    = $harga_tot/$kurs;
						$detailInv1[$val]['harga_total_idr']	= $harga_tot;
						$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
						$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv1[$val]['qty_total']			= $qty_ori;
						$detailInv1[$val]['qty_sisa']			= $qty_belum;
						$detailInv1[$val]['checked']			= $checked;
						$detailInv1[$val]['id_milik']	    	= $d1['id_milik'];
						$detailInv1[$val]['cogs']	    		= $d1['cogs'];

					}
				}

				$detailInv2 = [];
				if(!empty($_POST['data2'])){
					foreach($_POST['data2'] as $val => $d2){
						$material_name2	= $d2['material_name2'];
						$material_desc2		= $d2['material_desc2'];
						$harga_sat2		= $d2['harga_sat2'];
						$qty2=0;$checked='';
						if(isset($d2['qty2'])){
							$qty2		= $d2['qty2'];
							if($qty2>0) $checked='1';
						}
						$unit2			= $d2['unit2'];
						$harga_tot2		= $d2['harga_tot2'];
						$no_ippdtl		= $d2['no_ipp'];
						$no_sodtl		= $d2['no_so'];
						$qty2_ori		= $d2['qty2_ori'];
						$qty2_belum		= $d2['qty2_belum'];

						$detailInv2[$val]['id_penagihan']		= $id;
						$detailInv2[$val]['id_bq'] 		     	= $no_bq;
						$detailInv2[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv2[$val]['so_number'] 		    = $no_sodtl;
						$detailInv2[$val]['no_invoice'] 		= $no_invoice;
						$detailInv2[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv2[$val]['id_customer']	 	= $id_customer;
						$detailInv2[$val]['nm_customer'] 		= $nm_customer;
						$detailInv2[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv2[$val]['nm_material']	    = $material_name2." ".$material_desc2;
						$detailInv2[$val]['unit']	            = $unit2;
						$detailInv2[$val]['harga_satuan']	    = $harga_sat2/$kurs;
						$detailInv2[$val]['harga_satuan_idr']	= $harga_sat2;
						$detailInv2[$val]['qty']	            = $qty2;
						$detailInv2[$val]['harga_total']	    = $harga_tot2/$kurs;
						$detailInv2[$val]['harga_total_idr']	= $harga_tot2;
						$detailInv2[$val]['kategori_detail']	= 'BQ';
						$detailInv2[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv2[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv2[$val]['desc']	    		= $material_desc2;
						$detailInv2[$val]['qty_total']			= $qty2_ori;
						$detailInv2[$val]['qty_sisa']			= $qty2_belum;
						$detailInv2[$val]['checked']			= $checked;
						$detailInv2[$val]['id_milik']	    	= $d2['id_milik'];
					}
				}

				$detailInv3 = [];
				if(!empty($_POST['data3'])){
					foreach($_POST['data3'] as $val => $d3){
						$material_name3	= $d3['material_name3'];
						$harga_sat3		= $d3['harga_sat3'];
						$qty3=0;$checked='';
						if(isset($d3['qty3'])){
							$qty3                = $d3['qty3'];
							if($qty3>0) $checked='1';
						}
						$unit3                   = $d3['unit3'];
						$harga_tot3      		 = $d3['harga_tot3'];
						$no_ippdtl     		  	 = $d3['no_ipp'];
						$no_sodtl		      	 = $d3['no_so'];
						$product_cust         	 = $d3['product_cust'];
						$product_desc         	 = $d3['product_desc'];
						$qty3_ori				 = $d3['qty3_ori'];
						$qty3_belum				 = $d3['qty3_belum'];

						$detailInv3[$val]['id_penagihan']		= $id;
						$detailInv3[$val]['id_bq'] 		     	= $no_bq;
						$detailInv3[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv3[$val]['so_number'] 		    = $no_sodtl;
						$detailInv3[$val]['no_invoice'] 		= $no_invoice;
						$detailInv3[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv3[$val]['id_customer']	 	= $id_customer;
						$detailInv3[$val]['nm_customer'] 		= $nm_customer;
						$detailInv3[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv3[$val]['nm_material']	    = $material_name3;
						$detailInv3[$val]['product_cust']	    = $product_cust;
						$detailInv3[$val]['desc']	    		= $product_desc;
						$detailInv3[$val]['unit']	            = $unit3;
						$detailInv3[$val]['harga_satuan']	    = $harga_sat3/$kurs;
						$detailInv3[$val]['harga_satuan_idr']	= $harga_sat3;
						$detailInv3[$val]['qty']	            = $qty3;
						$detailInv3[$val]['harga_total']	    = $harga_tot3/$kurs;
						$detailInv3[$val]['harga_total_idr']	= $harga_tot3;
						$detailInv3[$val]['kategori_detail']	= 'MATERIAL';
						$detailInv3[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv3[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv3[$val]['qty_total']			= $qty3_ori;
						$detailInv3[$val]['qty_sisa']			= $qty3_belum;
						$detailInv3[$val]['checked']			= $checked;
						$detailInv3[$val]['id_milik']	    	= $d3['id_milik'];

					}
				}

				$detailInv4 = [];
				if(!empty($_POST['data4'])){
					foreach($_POST['data4'] as $val => $d4){
						$material_name4          = $d4['material_name4'];
						$harga_sat4       		 = 0;
						$qty4                    = 0;
						$unit4                   = $d4['unit4'];
						$harga_tot4=0;$checked='';
						if(isset($d4['harga_tot4'])){
							$harga_tot4       	 = $d4['harga_tot4'];
							if($harga_tot4>0) $checked='1';
						}
						$no_ippdtl     		  	 = $d4['no_ipp'];
						$no_sodtl		      	 = $d4['no_so'];
						$harga_tot4_ori			 = $d4['harga_tot4_ori'];
						$harga_tot4_sisa		 = $d4['harga_tot4_sisa'];

						$detailInv4[$val]['id_penagihan']		= $id;
						$detailInv4[$val]['id_bq'] 		     	= $no_bq;
						$detailInv4[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv4[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv4[$val]['harga_total']	    = $harga_tot4/$kurs;
						$detailInv4[$val]['harga_total_idr']	= $harga_tot4;
						$detailInv4[$val]['kategori_detail']	= 'ENGINERING';
						$detailInv4[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv4[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv4[$val]['desc']	    		= $material_name4;
						$detailInv4[$val]['harga_total_so']		= $harga_tot4_ori;
						$detailInv4[$val]['harga_sisa_so']		= $harga_tot4_sisa;
						$detailInv4[$val]['checked']			= $checked;
						$detailInv4[$val]['id_milik']	    	= $d4['id_milik'];

					}
				}

				$detailInv5 = [];
				if(!empty($_POST['data5'])){
					foreach($_POST['data5'] as $val => $d5){
						$material_name5          = $d5['material_name5'];
						$unit5                   = $d5['unit5'];
						$harga_tot5=0;$checked='';
						if(isset($d5['harga_tot5'])){
							$harga_tot5   = $d5['harga_tot5'];
							if($harga_tot5>0) $checked='1';
						}
						$no_ippdtl     		  	 = $d5['no_ipp'];
						$no_sodtl		      	 = $d5['no_so'];
						$harga_tot5_ori			 = $d5['harga_tot5_ori'];
						$harga_tot5_sisa	     = $d5['harga_tot5_sisa'];

						$detailInv5[$val]['id_penagihan']			= $id;
						$detailInv5[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv5[$val]['no_ipp'] 		     	= $no_ippdtl;
						$detailInv5[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv5[$val]['harga_total']	        = $harga_tot5/$kurs;
						$detailInv5[$val]['harga_total_idr']	    = $harga_tot5;
						$detailInv5[$val]['kategori_detail']	    = 'PACKING';
						$detailInv5[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv5[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv5[$val]['desc']	    			= $material_name5;
						$detailInv5[$val]['harga_total_so']			= $harga_tot5_ori;
						$detailInv5[$val]['harga_sisa_so']			= $harga_tot5_sisa;
						$detailInv5[$val]['checked']			 	= $checked;
						$detailInv5[$val]['id_milik']	    		= $d5['id_milik'];
					}
				}

				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$harga_sat6       		 = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6=0;$checked='';
						if(isset($d6['harga_tot6'])){
							$harga_tot6   		= $d6['harga_tot6'];
							if($harga_tot6>0) $checked='1';
						}
						$no_ippdtl				= $d6['no_ipp'];
						$no_sodtl				= $d6['no_so'];
						$harga_tot6_ori			= $d6['harga_tot6_ori'];
						$harga_tot6_sisa		= $d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ippdtl;
						$detailInv6[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6/$kurs;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];
					}
				}
				$detailInv9 = [];
				if(!empty($_POST['data9'])){
					foreach($_POST['data9'] as $val => $d){
						$material_name9	= $d9['material_name9'];
						$harga_sat9		= $d9['harga_sat9'];
						$qty9=0;$checked='';
						if(isset($d9['qty9'])){
							$qty9	= $d9['qty9'];
							if($qty9>0) $checked='1';
						}
						$unit9			= $d9['unit9'];
						$harga_tot9		= $d9['harga_tot9'];
						$no_ippdtl		= $d9['no_ipp'];
						$no_sodtl		= $d9['no_so'];
						$product_cust	= $d9['product_cust'];
						$product_desc	= $d9['product_desc'];
						$qty9_ori		= $d9['qty9_ori'];
						$qty9_belum		= $d9['qty9_belum'];

						$detailInv9[$val]['id_penagihan']		= $id;
						$detailInv9[$val]['id_bq'] 		     	= $no_bq;
						$detailInv9[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv9[$val]['so_number'] 		    = $no_sodtl;
						$detailInv9[$val]['no_invoice'] 		= $no_invoice;
						$detailInv9[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv9[$val]['id_customer']	 	= $id_customer;
						$detailInv9[$val]['nm_customer'] 		= $nm_customer;
						$detailInv9[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv9[$val]['nm_material']	    = $material_name9;
						$detailInv9[$val]['unit']	            = $unit9;
						$detailInv9[$val]['harga_satuan']	    = $harga_sat9;
						$detailInv9[$val]['harga_satuan_idr']	= $harga_sat9*$kurs;
						$detailInv9[$val]['qty']	            = $qty9;
						$detailInv9[$val]['harga_total']	    = $harga_tot9;
						$detailInv9[$val]['harga_total_idr']	= $harga_tot9*$kurs;
						$detailInv9[$val]['kategori_detail']	= 'OTHER';
						$detailInv9[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv9[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv9[$val]['product_cust']	    = $product_cust;
						$detailInv9[$val]['desc']	    		= $product_desc;
						$detailInv9[$val]['qty_total']			= $qty9_ori;
						$detailInv9[$val]['qty_sisa']			= $qty9_belum;
						$detailInv9[$val]['checked']			= $checked;
						$detailInv9[$val]['id_milik']	    	= $d9['id_milik'];
					}
				}
			}

			if($jenis_invoice=='retensi'){
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6		= $d6['material_name6'];
						$no_ipp_dtl			= $d6['no_ipp'];
						$no_so_dtl			= $d6['no_so'];
						$harga_sat6       	= 0;
						$qty6				= 0;$checked='1';
						$unit6				= $d6['unit6'];
						$harga_tot6       	= $d6['harga_tot6'];
						$harga_tot6_ori		= $harga_tot6;//$d6['harga_tot6_ori'];
						$harga_tot6_sisa	= 0;//$d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = 'BQ-'.$no_ipp_dtl;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp_dtl;
						$detailInv6[$val]['so_number'] 		     	= $no_so_dtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6/$kurs;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						//$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];
					}
				}
			}
/*
			$get_bill_so = $this->db->query("select * from billing_so_gabung where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr;
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka' && $um_persen2 < 1){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen']=$valx->uang_muka_persen + $um_persen;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka + ($grand_total*$perseninv/$kurs);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr + ($grand_total*$perseninv);
				}
			}

			if($jenis_invoice=='uang muka' && $um_persen2 > 0){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen2']=$valx->uang_muka_persen2 + $um_persen2;
					$ArrBillSO[$nox]['uang_muka2']=$valx->uang_muka2 + ($total_invoice*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr2']=$valx->uang_muka_idr2 + ($total_invoice_idr*$perseninv);
//					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_idr*$perseninv);
//					$ArrBillSO[$nox]['retensi_um']=$valx->retensi_um + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_um_idr + ($retensi_idr*$perseninv);
				}
			}

			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka_idr - ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr - ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice']=$valx->uang_muka_invoice + ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice_idr']=$valx->uang_muka_invoice_idr + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['persentase_progress']=$umpersen;
					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi/$kurs*$perseninv);
					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_idr*$perseninv);
					$ArrBillSO[$nox]['retensi_um']=$valx->retensi + ($retensi_ppn/$kurs*$perseninv);
					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_idr + ($retensi_ppn*$perseninv);
				}
			}
*/
			$ArrUM = [
				'proses_inv' => '1'
			];

			if($jenis_invoice == 'progress'){
				$stsx = 12;
			}
			if($jenis_invoice == 'uang muka' OR $jenis_invoice == 'retensi'){
				$stsx = 12;
			}

			$progress_pex = $umpersen + $penagihan[0]->progress_persen;
			if($jenis_invoice == 'retensi'){
				$progress_pex = 100;
			}

			$ArrPERSEN = [
				'delivery_no' => $dtdelivery_no,
				'total_cogs' => $dt_cogs,
			];
}
			$this->db->trans_start();
				if(!empty($ArrPERSEN)){
					$this->db->where('id',$id);
					$this->db->update('penagihan',$ArrPERSEN);
				}
				$this->db->where('id', $id);
				$this->db->update('penagihan', $headerinv);
				$this->db->query("delete from penagihan_detail where id_penagihan='".$id."'");
				if(!empty($detailInv1)){
					$this->db->insert_batch('penagihan_detail',$detailInv1);
				}
				if(!empty($detailInv2)){
					$this->db->insert_batch('penagihan_detail',$detailInv2);
				}
				if(!empty($detailInv3)){
					$this->db->insert_batch('penagihan_detail',$detailInv3);
				}
				if(!empty($detailInv4)){
					$this->db->insert_batch('penagihan_detail',$detailInv4);
				}
				if(!empty($detailInv5)){
					$this->db->insert_batch('penagihan_detail',$detailInv5);
				}
				if(!empty($detailInv6)){
					$this->db->insert_batch('penagihan_detail',$detailInv6);
				}
				if(!empty($detailInv9)){
					$this->db->insert_batch('penagihan_detail',$detailInv9);
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
				history('Update Penagihan '.$jenis_invoice.' '.$id);
			}
			echo json_encode($Arr_Return);
		} else{
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
			$getBq 		= $this->db->select('no_ipp as no_po, base_cur, no_so')->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();

			$in_ipp = [];
			$in_bq = [];
			$base_cur='';
			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= ($valx['no_so']==''?get_nomor_so($valx['no_po']):$valx['no_so']);
				$base_cur		= ($base_cur==''?$valx['base_cur']:$base_cur);
			}

			$jenis  	= 'progress';
			if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}

			$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
			$noipp=implode("','",$in_ipp);
			$id_produksi=implode("','PRO-",$in_ipp);
			$id_bq=implode("','BQ-",$in_ipp);
			$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
			if(!empty($penagihan_detail)){
				$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
				from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
				$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
				$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
				$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
				$non_frp	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
				$material	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();

				$get_kurs	= $this->db->select(' (kurs_jual) AS kurs,  (progress_persen) AS uang_muka_persen,  0 AS uang_muka_persen2')->where('id',$id)->get('penagihan')->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
				$getTankiproduct=array();
				$getTankipacking=array();
				$getTankishipping=array();
			}else{


				$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
//				$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
				$getDetail  = $this->db->query("select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,0 qty_delivery,0 as cogs from billing_so_product a where no_ipp in ('".$noipp."')")->result_array();
				$getEngCost	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
				$getPackCost= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
				$getTruck	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
				$non_frp	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
				$material	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();

				$getTankiproduct	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','product')->get(DBTANKI.'.billing_product')->result_array();
				$getTankipacking	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs,0 total_delivery ')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','packing')->get(DBTANKI.'.billing_product')->result_array();
				$getTankishipping	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs,0 total_delivery ')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','shipping')->get(DBTANKI.'.billing_product')->result_array();

				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
//				$get_kurs	= $this->db->select(' (kurs_usd_dipakai) AS kurs,  (uang_muka_persen) AS uang_muka_persen,  (uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();

				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
			}
			$approval	= $this->uri->segment(4);
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
				'getTankiproduct'	=> $getTankiproduct,
				'getTankipacking'	=> $getTankipacking,
				'getTankishipping'	=> $getTankishipping,
				'non_frp'		=> $non_frp,
				'material'		=> $material,
				'list_top'		=> $list_top,
				'base_cur'		=> $base_cur,
				'in_ipp'		=> implode(',',$in_ipp),
				'in_bq'			=> implode(',',$in_bq),
				'in_so'			=> implode(',',$in_so),
				'arr_in_ipp'	=> $in_ipp,
				'penagihan'		=> $penagihan,
				'kurs'			=> $get_kurs[0]->kurs,
				'uang_muka_persen'	=> $get_kurs[0]->uang_muka_persen,
				'uang_muka_persen2'	=> 0,
				'down_payment'	=> 0,
				'down_payment2'	=> 0,
				'id'			=> $id,
				'approval'		=> $approval
			);
			$this->load->view('Penagihan/create_progress_new',$data2);
		}
	}

	public function server_side_penagihan_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_penagihan_new(
			$requestData['customer'],
			$requestData['no_so'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length'],
			$requestData['delivery']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		$delivery=$requestData['delivery'];
		if($delivery!="") $delivery="_".$delivery;
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
			$nestedData[]	= "<div align='center'>".$row['id']."</div>";
			$noso=str_ireplace(",","','",$row['no_so']);

//			$get_so 		= $this->db->select('no_ipp')->where_in('id',$row['no_so'])->get('billing_so')->result_array();
			if($delivery!=""){
				$get_so	= $this->db->query("select * from billing_so_gabung where id in ('".$noso."')")->result_array();
			}else{
				$get_so	= $this->db->query("select * from billing_so_gabung where id in ('".$noso."')")->result_array();
			}
			$arr_so = array();
			foreach($get_so AS $val => $valx){
				$arr_so[$val] = ($valx['no_so']==''?get_name('so_number','so_number','id_bq',"BQ-".$valx['no_ipp']):$valx['no_so']);
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
				$approve="";
				$create_progress	= "";
				$create_um	= "";
				$create_retensi	= "";

				if($row['type'] == 'progress'){
					if($row['status'] == '10' OR $row['status'] == '11'){
						$create_progress = "<a href='".base_url('penagihan/add_new_invoice'.$delivery.'/'.$row['id'])."' class='btn btn-sm btn-success'><i class='fa fa-pencil' title='Edit'></i></a>";
						if($row['no_faktur']!="") $approve="<a href='".base_url('penagihan/create_progress_new'.$delivery.'_approval/'.$row['id'])."/approve' class='btn btn-sm btn-warning' title='Approve'><i class='fa fa-check-square-o'></i></a>";
					}
				}

				if($row['type'] == 'uang muka'){
					if($row['status'] == '10'){
						$create_um	= "<a href='".base_url('penagihan/create_um_new'.$delivery.'/'.$row['id'])."' class='btn btn-sm btn-primary'><i class='fa fa-pencil' title='Edit'></i></a>";
						if($row['no_faktur']!="") $approve="<a href='".base_url('penagihan/create_um_new'.$delivery.'/'.$row['id'])."/approve' class='btn btn-sm btn-warning' title='Approve'><i class='fa fa-check-square-o'></i></a>";
					}
				}

				if($row['type'] == 'retensi'){
					if($row['status'] == '10'){
						$create_retensi	= "<a href='".base_url('penagihan/create_retensi_new'.$delivery.'/'.$row['id'])."' class='btn btn-sm btn-info'><i class='fa fa-pencil' title='Edit'></i></a>";
						if($row['no_faktur']!="") $approve="<a href='".base_url('penagihan/create_retensi_new'.$delivery.'/'.$row['id'])."/approve' class='btn btn-sm btn-warning' title='Approve'><i class='fa fa-check-square-o'></i></a>";
					}
				}
			$nestedData[]	= "<div align='center'>
                                    ".$create_progress."
									".$create_um."
									".$create_retensi."
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

	public function query_data_penagihan_new($customer, $no_so, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL, $delivery = NULL){

		$where_customer = '';
		if($customer != '0'){
			$where_customer = " AND a.kode_customer='".$customer."'";
		}
		$where_no_so = '';
		if($no_so != '0'){
			$where_no_so = " AND a.id='".$no_so."'";
		}
		$tblpenagihan="penagihan";
		if($delivery!='') $tblpenagihan="penagihan";
		if($delivery == 'instalasi'){
			$where_no_so .= " AND a.instalasi ='1'";
		}else{
			$where_no_so .= " AND a.instalasi is null";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				".$tblpenagihan." a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_customer." ".$where_no_so." AND  (
				a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id',
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

	public function create_retensi_new(){
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
		$getBq 		= $this->db->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();

		$in_ipp = [];
		$in_bq = [];
		$base_cur='IDR';

		foreach($getBq AS $val => $valx){
			$in_ipp[$val] 	= $valx['no_ipp'];
			$in_bq[$val] 	= 'BQ-'.$valx['no_ipp'];
			$in_so[$val]	= ($valx['no_so']==''?get_nomor_so($valx['no_ipp']):$valx['no_so']);
			$base_cur		= ($base_cur==''?$valx['base_cur']:$base_cur);
		}

		$jenis  	= 'progress';
		$penagihan_detail = $this->db->select('*, no_ipp as nomor_po, harga_total as total_retensi2, harga_total_idr as total_retensi2_idr')->get_where('penagihan_detail', array('id_penagihan'=>$id))->result_array();
		if(!empty($penagihan_detail)) {
			$getBq=$penagihan_detail;
		}else{
			$getBq=$this->db->where_in('nomor_po',$penagihan[0]->no_po)->get('tr_kartu_po_customer')->result_array();
		}
		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
		$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
		$kurs=0;
		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
		if($penagihan[0]->kurs_jual==0) {
			$dtkurs	= $this->db->select('MAX(kurs_usd_dipakai) AS kurs, SUM(uang_muka_persen) AS uang_muka_persen, SUM(uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so_gabung')->row();
			$kurs=$dtkurs->kurs;
		}else{
			$kurs	= $penagihan[0]->kurs_jual;
		}

		

		$approval	= $this->uri->segment(4);
		$data = array(
			'title'			=> 'Indeks Of Create Invoice Retensi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'getHeader'		=> $getHeader,
			'getDetail' 	=> $getDetail,
			'get_retensi'	=> $getBq,
			'list_top'		=> $list_top,
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $kurs,
			'id'			=> $id,
			'approval'		=> $approval
		);
		$this->load->view('Penagihan/create_retensi_new',$data); 
	}
	public function create_retensi_new_delivery(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');

		$id    		= $this->uri->segment(3);
		$approval	= $this->uri->segment(4);
		$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
		$nomor_id 	= explode(",",$penagihan[0]->no_so);
		// echo $nomor_id;exit;
		$getBq 		= $this->db->where_in('id',$nomor_id)->get('billing_so')->result_array();

		$in_ipp = [];
		$in_bq = [];
		$base_cur='USD';

		foreach($getBq AS $val => $valx){
			$in_ipp[$val] 	= $valx['no_ipp'];
			$in_bq[$val] 	= 'BQ-'.$valx['no_ipp'];
			$in_so[$val] 	= get_nomor_so($valx['no_ipp']);
			$base_cur		= $valx['base_cur'];
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
		if($penagihan[0]->kurs_jual==0) {
			$get_kurs	= $this->db->select('MAX(kurs_usd_dipakai) AS kurs, SUM(uang_muka_persen) AS uang_muka_persen, SUM(uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
		}else{
			$get_kurs	= $this->db->query("select kurs_jual as kurs from penagihan where id='".$id."'")->result();
		}

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
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'id'			=> $id,
			'approval'      => $approval
		);
		$this->load->view('Penagihan/create_retensi_new_delivery',$data);
	}

	public function add_new_progress(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			
			// print_r($data);
			// exit;
			
			$data_session	= $this->session->userdata;
            $max_num 		= $this->db->select('MAX(id) AS nomor_max')->get('penagihan')->result();
			$id_tagih 		= $max_num[0]->nomor_max + 1;
			$check = $data['check'];
			$idso = $data['id'];
			$dtdelivery_no='';
			$dtListArray = [];
			if(!empty($check)){
				
				if($data['type']!='progress'){
					foreach($idso AS $val => $valx){
						$dtListArray[$val] = $valx;
					}
				}
				else {
					foreach($check AS $val => $valx){
						$dtListArray[$val] = $valx;
					}
					
				}
				
				$dtImplode	= "('".implode("','", $dtListArray)."')";
				$dtImplode2	= implode(",", $dtListArray); 
				
				
			
				$updDelivery="";
				$updDeliveryHeader="";
				if($data['type']!='progress'){
					$result_data 	= $this->db->query("SELECT * FROM billing_so WHERE id IN ".$dtImplode." ORDER BY id ")->result_array();
					$totalcogs =0;

				}else{
					
					$cogs = $this->db->query("SELECT sum(nilai_unit) as totalcogs FROM data_erp_in_customer WHERE kode_delivery IN ".$dtImplode." ")->row();
					$totalcogs =$cogs->totalcogs;
					$updDelivery="update delivery_product_detail set sts_invoice='1' WHERE kode_delivery IN ".$dtImplode." ";
					$updDeliveryHeader="update delivery_product set st_cogs='1' WHERE kode_delivery IN ".$dtImplode." ";
					$dtdelivery_no=$dtImplode2;
					$dtdelivery_no1=$dtImplode;
					$getipp 	= $this->db->query("SELECT replace(id_produksi,'PRO-','') id_produksi FROM delivery_product_detail WHERE kode_delivery IN ".$dtImplode." group BY id_produksi")->result();
					$dtListipp = [];
					foreach($getipp AS $val => $valx ){
						$dtListipp[]=$valx->id_produksi;
					}
					$dtImplode	= "('".implode("','", $dtListipp)."')";
					$result_data 	= $this->db->query("SELECT * FROM billing_so WHERE no_ipp IN ".$dtImplode." ORDER BY id ")->result_array();
					
					
					$dtListIDipp = [];
					foreach($result_data AS $val => $valx ){
						$dtListIDipp[] = $valx['id'];
					}
					$dtImplode	= "('".implode("','", $dtListIDipp)."')"; 
					$dtImplode2	= implode(",", $dtListIDipp);

                    //$getDelivery= $this->db->query("SELECT * FROM view_plan_tagih WHERE kode_delivery IN ".$dtdelivery_no1." ORDER BY id ")->result_array();
					
					/*$detailInv1 = [];
                    if(!empty($getDelivery)){						
					foreach($getDelivery AS $val => $d1){
							$nm_material          = $d1['product_so'];
							$product_cust         = $d1['product_delivery'];
							$product_desc         = $d1['deskripsi_so'];
							$diameter_1           = $d1['dim1'];
							$diameter_2      	  = $d1['dim2'];
							$liner                = $d1['liner'];
							$pressure             = $d1['pressure'];
							$id_milik             = $d1['id_milik'];
							$spesifikasi		  = $d1['spec'];
							$harga_sat     		  = $d1['idr_nilai_so'];
							$qty=0;$checked='';
							if($d1['qty_berat'] > 0){
								$qty              = $d1['qty_berat'];
								$checked='1';
							}else {
								$qty              = $d1['qty_delivery'];
								$checked='1';								
							}
							$unit1                = $d1['unit'];
							$harga_tot     		  = $d1['idr_nilai_delivery'];
							$no_ippdtl     		  = $d1['kode_delivery'];
							$no_sodtl		      = $d1['no_so'];
							$qty_ori			  = $d1['qty_so'];
							$qty_belum			  = $d1['qty_inv'];
							$no_bq                = $d1['id_produksi'];

							$detailInv1[$val]['id_penagihan']		= $id_tagih;
							$detailInv1[$val]['id_bq'] 		     	= $no_bq;
							$detailInv1[$val]['no_ipp'] 		    = $no_ippdtl;
							$detailInv1[$val]['so_number'] 		    = $no_sodtl;
							$detailInv1[$val]['no_invoice'] 		= '-';
							$detailInv1[$val]['tgl_invoice']      	= date('Y-m-d H:i:s');
							$detailInv1[$val]['id_customer']	 	= $data['customer'];
							$detailInv1[$val]['nm_customer'] 		= get_name('customer','nm_customer','id_customer',$data['customer']);
							$detailInv1[$val]['jenis_invoice'] 		= 'progress';
							$detailInv1[$val]['nm_material']	    = $nm_material;
							$detailInv1[$val]['product_cust']	    = $product_cust;
							$detailInv1[$val]['desc']	    		= $product_desc;
							$detailInv1[$val]['dim_1']	            = $diameter_1;
							$detailInv1[$val]['dim_2']	            = $diameter_2;
							$detailInv1[$val]['liner']	            = $liner;
							$detailInv1[$val]['pressure']	        = $pressure;
							$detailInv1[$val]['spesifikasi']	    = $spesifikasi;
							$detailInv1[$val]['unit']	            = $unit1;
							$detailInv1[$val]['harga_satuan']	    = $harga_sat;
							$detailInv1[$val]['harga_satuan_idr']	= $harga_sat;
							$detailInv1[$val]['qty']	            = $qty;
							$detailInv1[$val]['harga_total']	    = $harga_tot;
							$detailInv1[$val]['harga_total_idr']	= $harga_tot;
							$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
							$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
							$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
							$detailInv1[$val]['qty_total']			= $qty_ori;
							$detailInv1[$val]['qty_sisa']			= $qty_belum;
							$detailInv1[$val]['checked']			= $checked;
							$detailInv1[$val]['id_milik']	    	= $d1['id_milik'];
							$detailInv1[$val]['cogs']	    		= 0;

						}
					}*/

					
						
					//print_r($detailInv1);
			        //exit;
				}
				
				
				
								
			}else{
				$Arr_Kembali	= array(
					'pesan'		=>'Process data failed. Please check input ...',
					'status'	=> 2
				);
				echo json_encode($Arr_Kembali);
				die();
			}
		

		
			$SUM_USD = 0;
			$SUM_IDR = 0;
			$Update_b = [];
			foreach($result_data AS $val => $valx){
				$SUM_USD += $valx['total_deal_usd'];
				$SUM_IDR += $valx['total_deal_idr'];
				$no_ipp = str_replace('BQ-','',$valx['no_ipp']);

				$Update_b[$val]['id'] = $valx['id'];
				$Update_b[$val]['id_penagihan'] = $id_tagih;

				$base_cur = $valx['base_cur'];
			}
			
			
					
			$header = [
				'delivery_no' => $dtdelivery_no,
				'no_so' => $dtImplode2,
				'no_ipp' => $no_ipp,
				'no_po' => $data['no_po'],
				'project' => NULL,
				'kode_customer' => $data['customer'],
				'customer' => get_name('customer','nm_customer','id_customer',$data['customer']),
				'keterangan' => NULL,
				'plan_tagih_date' => date("Y-m-d"),
				'plan_tagih_usd' => $SUM_USD,
				'plan_tagih_idr' => $SUM_IDR,
				'type' => $data['type'],
				'base_cur' => $base_cur,
				'status' => 10,
				'type_lc' => $data['type_lc'],
				'etd' => $data['etd'],
				'eta' => $data['eta'],
				'consignee' => $data['consignee'],
				'total_cogs' => $totalcogs,
				'notify_party' => $data['notify_party'],
				'port_of_loading' => $data['port_of_loading'],
				'port_of_discharges' => $data['port_of_discharges'],
				'flight_airway_no' => $data['flight_airway_no'],
				'ship_via' => $data['ship_via'],
				'saliling' => $data['saliling'],
				'vessel_flight' => $data['vessel_flight'],
				'term_delivery' => $data['term_delivery'],
				'created_by' => $this->session->userdata['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')

			];

			$this->db->trans_start();
				$this->db->insert('penagihan', $header);
				//$this->db->update_batch('billing_top', $Update_b, 'id');

				// update billing so status
				$this->db->query("update billing_so set status='1' WHERE id IN ".$dtImplode." and status='0' ");

				// update Delivery
				if($updDelivery!="") {
					$this->db->query($updDelivery);
					$this->db->query($updDeliveryHeader);
				}


				/*if(!empty($detailInv1)){
					$this->db->insert_batch('penagihan_detail',$detailInv1);
					}*/

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

			$data = array(
				'title'			=> 'Indeks Of Add Billing',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'customer'		=> $customer,
				'no_po'			=> $no_po
			);

			$this->load->view('Penagihan/add_new_progress',$data);
		}
	}

	public function server_side_penagihan_add_new_progress(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		if($requestData['no_po']=='0') die();
		$fetch			= $this->query_data_penagihan_add_new_progress(
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
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['kode_delivery']."'>
			<input type='hidden' name='id[$nomor]' value='".$row['id']."' />
			<input type='hidden' name='ipp[$nomor]' value='".$row['no_ipp']."' />
			<input type='hidden' name='delivery_".$row['id']."' value='".$row['no_ipp']."' />
			</div>";
			$nestedData[]	= "<div align='left'><input type='input' name='so_number[$nomor]' class='form-control' value='".$row['so_number']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='no_pox[$nomor]' class='form-control' value='".$row['no_pox']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='project[$nomor]' class='form-control' value='".$row['project']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='customer2[$nomor]' class='form-control' value='".$row['customer']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='kode_delivery[$nomor]' class='form-control' value='".$row['kode_delivery']."'></div>";

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
	public function query_data_penagihan_add_new_progress($customer, $type, $no_po, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_customer = '';
if($type!='progress'){
		if($customer != '0'){
		}
			$where_customer = " AND b.kode_customer='".$customer."'";

		$where_no_po = '';
		if($no_po != '0'){
		}
			$where_no_po = " AND b.no_po='".$no_po."'";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				b.id,
				b.project,
				b.kode_customer,
				b.nm_customer AS customer,
				b.no_po AS no_pox,
				c.so_number,
				'' kode_delivery , b.no_ipp
			FROM
				billing_so_gabung b
				LEFT JOIN so_number c ON replace(c.id_bq,'BQ-','') = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE (status=1 or status=0) ".$where_customer." ".$where_no_po." AND (
				c.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_po = '".$this->db->escape_like_str($like_value)."'
	        )
		";
}
else{
		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.id, c.so_number,c.no_po no_pox,c.project,c.nm_customer customer,a.kode_delivery , c.no_ipp
				FROM
					delivery_product a
					LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery
					left join (select m.no_ipp, so_number, no_po, project, id_customer, nm_customer from table_sales_order m LEFT JOIN so_bf_header n ON m.no_ipp=n.no_ipp) c on replace(b.id_produksi,'PRO-','')=c.no_ipp,
					(SELECT @row:=0) r
				WHERE b.sts_invoice=0
					AND b.posisi = 'CUSTOMER'
					AND (
						 c.no_po like '%".$no_po."%'
						)
				GROUP BY
					a.kode_delivery,c.so_number,c.no_po,c.project,c.nm_customer
		";		//c.id_customer='".$customer."' and
}
		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'so_number',
			2 => 'no_pox',
			3 => 'project',
			4 => 'customer',
			5 => 'kode_delivery',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_progress_new_delivery(){
		if($this->input->post()){
			$data_session	= $this->session->userdata;

			$id			= $this->input->post('id');
			$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
			$nomor_id 	= explode(",",$penagihan[0]->no_so);
			$getBq 		= $this->db->select('no_ipp as no_po')->where_in('id',$nomor_id)->get('billing_so')->result_array();

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
			$so_number				= $this->input->post('no_so');
// sementara diganti
//			$no_invoice 			= gen_invoice($no_ipp);
			$no_invoice 			= $this->input->post('nomor_faktur');
			$id_customer			= $this->input->post('id_customer');
			$nm_customer			= $this->input->post('nm_customer');
			$no_bq                  = 'BQ-'.$no_ipp;
			$kurs                   = str_replace(',','',$this->input->post('kurs'));
			$jenis_invoice 			= strtolower($this->input->post('type'));
			$base_cur				= $this->input->post('base_cur');
			$um_persen2				= str_replace(',','',$this->input->post('um_persen2'));
			$umpersen				= str_replace(',','',$this->input->post('umpersen'));
			$grand_total          	= str_replace(',','',$this->input->post('grand_total'));
			$ppnselect				= $this->input->post('ppnselect');
			$progressx				= $this->input->post('progressx');
			$persen_retensi2		= $this->input->post('persen_retensi2');
			$persen_retensi			= $this->input->post('persen_retensi');
			
			
if($base_cur=='USD'){
			$total_invoice          = $this->input->post('total_invoice');
			$total_invoice_idr      = $this->input->post('total_invoice')*$kurs;
			$total_um               = $this->input->post('down_payment');
			$total_um_idr           = $this->input->post('down_payment')*$kurs;
			$um_persen				= str_replace(',','',$this->input->post('um_persen'));
			$total_gab_product      = ($this->input->post('tot_product'))+($this->input->post('total_material'))+($this->input->post('total_bq_nf'));
			$total_gab_product_idr  = ($this->input->post('tot_product')*$kurs)+($this->input->post('total_material')*$kurs)+($this->input->post('total_bq_nf')*$kurs);

			$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi'));
			$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi2'));

			$diskon = (!empty($this->input->post('diskon')))?$this->input->post('diskon'):str_replace(',','',$this->input->post('diskon'));
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
				$totaluangmuka2 = $this->input->post('grand_total') - $this->input->post('down_payment');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			if($jenis_invoice=='progress' && $um_persen2 != 0){
				$totaluangmuka2 = $this->input->post('down_payment2');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			
			
			//INSERT DATABASE TR INVOICE HEADER
			$headerinv = [
				'keterangan'				=> $this->input->post('keterangan'),
				'ppnselect' 		     	=> $ppnselect,
				'progressx' 		     	=> $progressx,
				'persen_retensi2'			=> $persen_retensi2,
				'persen_retensi'			=> $persen_retensi,
				'no_invoice' 		     	=> $no_invoice,
				'tgl_invoice'      		    => $Tgl_Invoice,
				'kode_customer'	 	      	=> $id_customer,
				'nm_customer' 		      	=> $nm_customer,
				'persentase' 		        => $progress,
				'progress_persen' 			=> $this->input->post('persen'),
				'total_product'	         	=> $this->input->post('tot_product'),
				'total_product_idr'	        => $this->input->post('tot_product')*$kurs,
				'total_gab_product'	        => $total_gab_product,
				'total_gab_product_idr'	    => $total_gab_product_idr,
				'total_material'	        => $this->input->post('total_material'),
				'total_material_idr'	    => $this->input->post('total_material')*$kurs,
				'total_bq'	                => $this->input->post('total_bq_nf'),
				'total_bq_idr'	            => $this->input->post('total_bq_nf')*$kurs,
				'total_enginering'	        => $this->input->post('total_enginering'),
				'total_enginering_idr'	    => $this->input->post('total_enginering')*$kurs,
				'total_packing'	            => $this->input->post('total_packing'),
				'total_packing_idr'	        => $this->input->post('total_packing')*$kurs,
				'total_trucking'	        => $this->input->post('total_trucking'),
				'total_trucking_idr'	    => $this->input->post('total_trucking')*$kurs,
				'total_dpp_usd'	            => $this->input->post('grand_total'),
				'total_dpp_rp'	            => $this->input->post('grand_total')*$kurs,
				'total_diskon'	            => $diskon,
				'total_diskon_idr'	        => $diskon * $kurs,
				'total_retensi'	            => $this->input->post('potongan_retensi'),
				'total_retensi_idr'	        => $this->input->post('potongan_retensi')*$kurs,
				'total_ppn'	                => $this->input->post('ppn'),
				'total_ppn_idr'	            => $this->input->post('ppn')*$kurs,
				'total_invoice'	            => $this->input->post('total_invoice'),
				'total_invoice_idr'	        => $this->input->post('total_invoice')*$kurs,
				'total_um'	                => $this->input->post('down_payment'),
				'total_um_idr'	            => $this->input->post('down_payment')*$kurs,
				'kurs_jual'	                => $kurs,
				'no_po'	                    => $this->input->post('nomor_po'),
				'no_faktur'	                => $this->input->post('nomor_faktur'),
				'no_pajak'	                => $this->input->post('nomor_pajak'),
				'payment_term'	            => $this->input->post('top'),
				'updated_by' 	            => $data_session['ORI_User']['username'],
				'updated_date' 	            => date('Y-m-d H:i:s'),
				'total_um2'	                => $totaluangmuka2,
				'total_um_idr2'	            => $totaluangmuka2_idr,
				'id_top'	            	=> $id,
				'base_cur'					=> $base_cur,
				'total_retensi2'			=> $retensi_ppn,
				'total_retensi2_idr'		=> $retensi_ppn*$kurs,
				'sisa_invoice'	        	=> $this->input->post('total_invoice'),
				'sisa_invoice_idr'	        => $this->input->post('total_invoice')*$kurs,
				'so_number'					=> $so_number
			];
			

			if($jenis_invoice=='progress'){
				$detailInv1 = [];
				if(!empty($_POST['data1'])){
					foreach($_POST['data1'] as $val => $d1){
						$nm_material	= $d1['material_name1'];
						$product_cust	= $d1['product_cust'];
						$product_desc	= $d1['product_desc'];
						$diameter_1	= $d1['diameter_1'];
						$diameter_2	= $d1['diameter_2'];
						$liner		= $d1['liner'];
						$pressure	= $d1['pressure'];
						$id_milik	= $d1['id_milik'];
						$spesifikasi	= $d1['spesifikasi'];
						$harga_sat	= $d1['harga_sat'];
						$qty=0;
						$checked='';
						if(isset($d1['qty'])){
							$qty	= $d1['qty'];
							$checked='1';
						}
						$unit1		= $d1['unit1'];
						$harga_tot	= $d1['harga_tot'];
						$no_ippdtl	= $d1['no_ipp'];
						$no_sodtl	= $d1['no_so'];
						$qty_ori	= $d1['qty_ori'];
						$qty_belum	= $d1['qty_belum'];

						$detailInv1[$val]['id_penagihan']		= $id;
						$detailInv1[$val]['id_bq'] 		     	= $no_bq;
						$detailInv1[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv1[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv1[$val]['spesifikasi']	    = $spesifikasi;
						$detailInv1[$val]['unit']	            = $unit1;
						$detailInv1[$val]['harga_satuan']	    = $harga_sat;
						$detailInv1[$val]['harga_satuan_idr']	= $harga_sat*$kurs;
						$detailInv1[$val]['qty']	            = $qty;
						$detailInv1[$val]['harga_total']	    = $harga_tot;
						$detailInv1[$val]['harga_total_idr']	= $harga_tot*$kurs;
						$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
						$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv1[$val]['qty_total']			= $qty_ori;
						$detailInv1[$val]['qty_sisa']			= $qty_belum;
						$detailInv1[$val]['checked']			= $checked;
						$detailInv1[$val]['id_milik']	    	= $d1['id_milik'];
						$detailInv1[$val]['cogs']	    		= $d1['cogs'];
					}
				}

				$detailInv2 = [];
				if(!empty($_POST['data2'])){
					foreach($_POST['data2'] as $val => $d2){
						$material_name2	= $d2['material_name2'];
						$material_desc2	= $d2['material_desc2'];
						$harga_sat2    = $d2['harga_sat2'];
						$qty2=0;$checked='';
						if(isset($d2['qty2'])){
							$qty2	= $d2['qty2'];
							if($qty2>0) $checked='1';
						}
						$unit2		= $d2['unit2'];
						$harga_tot2	= $d2['harga_tot2'];
						$no_ippdtl	= $d2['no_ipp'];
						$no_sodtl	= $d2['no_so'];
						$qty2_ori	= $d2['qty2_ori'];
						$qty2_belum	= $d2['qty2_belum'];

						$detailInv2[$val]['id_penagihan']		= $id;
						$detailInv2[$val]['id_bq'] 		     	= $no_bq;
						$detailInv2[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv2[$val]['so_number'] 		    = $no_sodtl;
						$detailInv2[$val]['no_invoice'] 		= $no_invoice;
						$detailInv2[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv2[$val]['id_customer']	 	= $id_customer;
						$detailInv2[$val]['nm_customer'] 		= $nm_customer;
						$detailInv2[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv2[$val]['nm_material']	    = $material_name2." ".$material_desc2;
						$detailInv2[$val]['unit']	            = $unit2;
						$detailInv2[$val]['harga_satuan']	    = $harga_sat2;
						$detailInv2[$val]['harga_satuan_idr']	= $harga_sat2*$kurs;
						$detailInv2[$val]['qty']	            = $qty2;
						$detailInv2[$val]['harga_total']	    = $harga_tot2;
						$detailInv2[$val]['harga_total_idr']	= $harga_tot2*$kurs;
						$detailInv2[$val]['kategori_detail']	= 'BQ';
						$detailInv2[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv2[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv2[$val]['desc']	    		= $material_desc2;
						$detailInv2[$val]['qty_total']			= $qty2_ori;
						$detailInv2[$val]['qty_sisa']			= $qty2_belum;
						$detailInv2[$val]['checked']			= $checked;
						$detailInv2[$val]['id_milik']	    	= $d2['id_milik'];
					}
				}

				$detailInv3 = [];
				if(!empty($_POST['data3'])){
					foreach($_POST['data3'] as $val => $d3){
						$material_name3	= $d3['material_name3'];
						$harga_sat3		= $d3['harga_sat3'];
						$qty3=0;$checked='';
						if(isset($d3['qty3'])){
							$qty3	= $d3['qty3'];
							if($qty3>0) $checked='1';
						}
						$unit3			= $d3['unit3'];
						$harga_tot3		= $d3['harga_tot3'];
						$no_ippdtl		= $d3['no_ipp'];
						$no_sodtl		= $d3['no_so'];
						$product_cust	= $d3['product_cust'];
						$product_desc	= $d3['product_desc'];
						$qty3_ori		= $d3['qty3_ori'];
						$qty3_belum		= $d3['qty3_belum'];

						$detailInv3[$val]['id_penagihan']		= $id;
						$detailInv3[$val]['id_bq'] 		     	= $no_bq;
						$detailInv3[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv3[$val]['so_number'] 		    = $no_sodtl;
						$detailInv3[$val]['no_invoice'] 		= $no_invoice;
						$detailInv3[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv3[$val]['id_customer']	 	= $id_customer;
						$detailInv3[$val]['nm_customer'] 		= $nm_customer;
						$detailInv3[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv3[$val]['nm_material']	    = $material_name3;
						$detailInv3[$val]['unit']	            = $unit3;
						$detailInv3[$val]['harga_satuan']	    = $harga_sat3;
						$detailInv3[$val]['harga_satuan_idr']	= $harga_sat3*$kurs;
						$detailInv3[$val]['qty']	            = $qty3;
						$detailInv3[$val]['harga_total']	    = $harga_tot3;
						$detailInv3[$val]['harga_total_idr']	= $harga_tot3*$kurs;
						$detailInv3[$val]['kategori_detail']	= 'MATERIAL';
						$detailInv3[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv3[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv3[$val]['product_cust']	    = $product_cust;
						$detailInv3[$val]['desc']	    		= $product_desc;
						$detailInv3[$val]['qty_total']			= $qty3_ori;
						$detailInv3[$val]['qty_sisa']			= $qty3_belum;
						$detailInv3[$val]['checked']			= $checked;
						$detailInv3[$val]['id_milik']	    	= $d3['id_milik'];
					}
				}

				$detailInv4 = [];
				if(!empty($_POST['data4'])){
					foreach($_POST['data4'] as $val => $d4){
						$material_name4	= $d4['material_name4'];
						$harga_sat4		= 0;
						$qty4			= 0;
						$unit4			= $d4['unit4'];
						$harga_tot4		=0;$checked='';
						if(isset($d4['harga_tot4'])){
							$harga_tot4	= $d4['harga_tot4'];
							if($harga_tot4>0) $checked='1';
						}
						$no_ippdtl		= $d4['no_ipp'];
						$no_sodtl		= $d4['no_so'];
						$harga_tot4_ori	= $d4['harga_tot4_ori'];
						$harga_tot4_sisa= $d4['harga_tot4_sisa'];

						$detailInv4[$val]['id_penagihan']		= $id;
						$detailInv4[$val]['id_bq'] 		     	= $no_bq;
						$detailInv4[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv4[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv4[$val]['harga_total']	    = $harga_tot4;
						$detailInv4[$val]['harga_total_idr']	= $harga_tot4*$kurs;
						$detailInv4[$val]['kategori_detail']	= 'ENGINERING';
						$detailInv4[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv4[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv4[$val]['desc']	    		= $material_name4;
						$detailInv4[$val]['harga_total_so']		= $harga_tot4_ori;
						$detailInv4[$val]['harga_sisa_so']		= $harga_tot4_sisa;
						$detailInv4[$val]['checked']			= $checked;
						$detailInv4[$val]['id_milik']	    	= $d4['id_milik'];
					}
				}

				$detailInv5 = [];
				if(!empty($_POST['data5'])){
					foreach($_POST['data5'] as $val => $d5){
						$material_name5          = $d5['material_name5'];
						$unit5                   = $d5['unit5'];
						$harga_tot5=0;$checked='';
						if(isset($d5['harga_tot5'])){
							$harga_tot5   = $d5['harga_tot5'];
							if($harga_tot5>0) $checked='1';
						}
						$no_ippdtl		= $d5['no_ipp'];
						$no_sodtl		= $d5['no_so'];
						$harga_tot5_ori	= $d5['harga_tot5_ori'];
						$harga_tot5_sisa= $d5['harga_tot5_sisa'];

						$detailInv5[$val]['id_penagihan']			= $id;
						$detailInv5[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv5[$val]['no_ipp'] 		     	= $no_ippdtl;
						$detailInv5[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv5[$val]['harga_total']	        = $harga_tot5;
						$detailInv5[$val]['harga_total_idr']	    = $harga_tot5*$kurs;
						$detailInv5[$val]['kategori_detail']	    = 'PACKING';
						$detailInv5[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv5[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv5[$val]['desc']	    			= $material_name5;
						$detailInv5[$val]['harga_total_so']			= $harga_tot5_ori;
						$detailInv5[$val]['harga_sisa_so']			= $harga_tot5_sisa;
						$detailInv5[$val]['checked']				= $checked;
						$detailInv5[$val]['id_milik']	    		= $d5['id_milik'];
					}
				}

				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6	= $d6['material_name6'];
						$harga_sat6		= 0;
						$qty6			= 0;
						$unit6			= $d6['unit6'];
						$harga_tot6		=0;$checked='';
						if(isset($d6['harga_tot6'])){
							$harga_tot6	= $d6['harga_tot6'];
							if($harga_tot6>0) $checked='1';
						}
						$no_ippdtl			= $d6['no_ipp'];
						$no_sodtl			= $d6['no_so'];
						$harga_tot6_ori		= $d6['harga_tot6_ori'];
						$harga_tot6_sisa	= $d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ippdtl;
						$detailInv6[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];


					}
				}
				$detailInv9 = [];
				if(!empty($_POST['data9'])){
					foreach($_POST['data9'] as $val => $d9){
						$material_name9	= $d9['material_name9'];
						$harga_sat9		= $d9['harga_sat9'];
						$qty9=0;$checked='';
						if(isset($d9['qty9'])){
							$qty9	= $d9['qty9'];
							if($qty9>0) $checked='1';
						}

						
						$unit9			= $d9['unit9'];
						$harga_tot9		= $d9['harga_tot9'];
						$no_ippdtl		= $d9['no_ipp'];
						$no_sodtl		= $d9['no_so'];
						$product_cust	= $d9['material_name9'];
						$product_desc	= $d9['material_desc9'];
						$qty9_ori		= $d9['qty9_ori'];
						$qty9_belum		= $d9['qty9_belum'];

						$detailInv9[$val]['id_penagihan']		= $id;
						$detailInv9[$val]['id_bq'] 		     	= $no_bq;
						$detailInv9[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv9[$val]['so_number'] 		    = $no_sodtl;
						$detailInv9[$val]['no_invoice'] 		= $no_invoice;
						$detailInv9[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv9[$val]['id_customer']	 	= $id_customer;
						$detailInv9[$val]['nm_customer'] 		= $nm_customer;
						$detailInv9[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv9[$val]['nm_material']	    = $material_name9;
						$detailInv9[$val]['unit']	            = $unit9;
						$detailInv9[$val]['harga_satuan']	    = $harga_sat9;
						$detailInv9[$val]['harga_satuan_idr']	= $harga_sat9*$kurs;
						$detailInv9[$val]['qty']	            = $qty9;
						$detailInv9[$val]['harga_total']	    = $harga_tot9;
						$detailInv9[$val]['harga_total_idr']	= $harga_tot9*$kurs;
						$detailInv9[$val]['kategori_detail']	= 'OTHER';
						$detailInv9[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv9[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv9[$val]['product_cust']	    = $product_cust;
						$detailInv9[$val]['desc']	    		= $product_desc;
						$detailInv9[$val]['qty_total']			= $qty9_ori;
						$detailInv9[$val]['qty_sisa']			= $qty9_belum;
						$detailInv9[$val]['checked']			= $checked;
						$detailInv9[$val]['id_milik']	    	= $d9['id_milik'];
					}
				}
			}

			if($jenis_invoice=='retensi'){
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$no_ipp_dtl		         = $d6['no_ipp'];
						$no_so_dtl		         = $d6['no_so'];
						$harga_sat6       		 = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6       		 = $d6['harga_tot6'];
						$harga_tot6_ori			 = $harga_tot6;//$d6['harga_tot6_ori'];
						$harga_tot6_sisa		 = 0;//$d6['harga_tot6_sisa'];
						if($harga_tot6>0) $checked='1';

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = 'BQ-'.$no_ipp_dtl;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp_dtl;
						$detailInv6[$val]['so_number'] 		     	= $no_so_dtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						//$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];
					}
				}
			}

			$get_bill_so = $this->db->query("select * from billing_so where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr;
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka' && $um_persen2 < 1){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen']=$valx->uang_muka_persen + $um_persen;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka + ($grand_total*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr + ($grand_total*$perseninv*$kurs);
				}
			}

			if($jenis_invoice=='uang muka' && $um_persen2 > 0){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen2']=$valx->uang_muka_persen2 + $um_persen2;
					$ArrBillSO[$nox]['uang_muka2']=$valx->uang_muka2 + ($total_invoice*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr2']=$valx->uang_muka_idr2 + ($total_invoice_idr*$kurs*$perseninv);
//					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_idr*$perseninv);
//					$ArrBillSO[$nox]['retensi_um']=$valx->retensi_um + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_um_idr + ($retensi_idr*$perseninv);
				}
			}

			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka - $total_um;
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr - ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice']=$valx->uang_muka_invoice + ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice_idr']=$valx->uang_muka_invoice_idr + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['persentase_progress']=$umpersen;
					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi_non_ppn*$perseninv);
					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_non_ppn*$kurs*$perseninv);
					$ArrBillSO[$nox]['retensi_um']=$valx->retensi + ($retensi_ppn*$perseninv);
					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_idr + ($retensi_ppn*$kurs*$perseninv);
				}
			}
			$ArrUM = [
				'proses_inv' => '1'
			];

			if($jenis_invoice == 'progress'){
				$stsx = 12;
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
				'grand_total' => $grand_total,
				'status'	=> $stsx,
				'real_tagih_usd'	=> $this->input->post('total_invoice') ,
				'real_tagih_idr'	=> $this->input->post('total_invoice') * $kurs
			];
}
else
{
//	BASE_CUR=IDR
			$total_invoice          = $this->input->post('total_invoice')/$kurs;
			$total_invoice_idr      = $this->input->post('total_invoice');
			$total_um               = $this->input->post('down_payment')/$kurs;
			$total_um_idr           = $this->input->post('down_payment');
			$um_persen				= str_replace(',','',$this->input->post('um_persen'));
			$total_gab_product      = ($this->input->post('tot_product')/$kurs)+($this->input->post('total_material')/$kurs)+($this->input->post('total_bq_nf')/$kurs);
			$total_gab_product_idr  = ($this->input->post('tot_product'))+($this->input->post('total_material'))+($this->input->post('total_bq_nf'));

			$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi'));
			$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi2'));

			$diskon = (!empty($this->input->post('diskon')))?$this->input->post('diskon'):str_replace(',','',$this->input->post('diskon'));

			$retensi_FIX = 0;

			if($retensi_non_ppn <= 0 ){
				$retensi_FIX = $retensi_ppn;
			}

			if($retensi_ppn <= 0 ){
				$retensi_FIX = $retensi_non_ppn;
			}

			$retensi				=  $retensi_FIX/$kurs;
			$retensi_idr		    =  $retensi_FIX;

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
				$totaluangmuka2_idr = $this->input->post('grand_total') - $this->input->post('down_payment');
				$totaluangmuka2 = $totaluangmuka2/$kurs;
			}
			if($jenis_invoice=='progress' && $um_persen2 != 0){
				$totaluangmuka2_idr = $this->input->post('down_payment2');
				$totaluangmuka2 = $totaluangmuka2/$kurs;
			}

			//INSERT DATABASE TR INVOICE HEADER
			$headerinv = [
				'keterangan'				=> $this->input->post('keterangan'),
				'ppnselect' 		     	=> $ppnselect,
				'progressx' 		     	=> $progressx,
				'persen_retensi2'			=> $persen_retensi2,
				'persen_retensi'			=> $persen_retensi,
				'no_invoice' 		     	=> $no_invoice,
				'tgl_invoice'      		    => $Tgl_Invoice,
				'kode_customer'	 	      	=> $id_customer,
				'nm_customer' 		      	=> $nm_customer,
				'persentase' 		        => $progress,
				'progress_persen' 			=> $this->input->post('persen'),
				'total_product'	         	=> $this->input->post('tot_product')/$kurs,
				'total_product_idr'	        => $this->input->post('tot_product'),
				'total_gab_product'	        => $total_gab_product,
				'total_gab_product_idr'	    => $total_gab_product_idr,
				'total_material'	        => $this->input->post('total_material')/$kurs,
				'total_material_idr'	    => $this->input->post('total_material'),
				'total_bq'	                => $this->input->post('total_bq_nf')/$kurs,
				'total_bq_idr'	            => $this->input->post('total_bq_nf'),
				'total_enginering'	        => $this->input->post('total_enginering')/$kurs,
				'total_enginering_idr'	    => $this->input->post('total_enginering'),
				'total_packing'	            => $this->input->post('total_packing')/$kurs,
				'total_packing_idr'	        => $this->input->post('total_packing'),
				'total_trucking'	        => $this->input->post('total_trucking')/$kurs,
				'total_trucking_idr'	    => $this->input->post('total_trucking'),
				'total_dpp_usd'	            => $this->input->post('grand_total')/$kurs,
				'total_dpp_rp'	            => $this->input->post('grand_total'),
				'total_diskon'	            => $diskon/$kurs,
				'total_diskon_idr'	        => $diskon,
				'total_retensi'	            => $this->input->post('potongan_retensi')/$kurs,
				'total_retensi_idr'	        => $this->input->post('potongan_retensi'),
				'total_ppn'	                => $this->input->post('ppn')/$kurs,
				'total_ppn_idr'	            => $this->input->post('ppn'),
				'total_invoice'	            => $this->input->post('total_invoice')/$kurs,
				'total_invoice_idr'	        => $this->input->post('total_invoice'),
				'total_um'	                => $this->input->post('down_payment')/$kurs,
				'total_um_idr'	            => $this->input->post('down_payment'),
				'kurs_jual'	                => $kurs,
				'no_po'	                    => $this->input->post('nomor_po'),
				'no_faktur'	                => $this->input->post('nomor_faktur'),
				'no_pajak'	                => $this->input->post('nomor_pajak'),
				'payment_term'	            => $this->input->post('top'),
				'updated_by' 	            => $data_session['ORI_User']['username'],
				'updated_date' 	            => date('Y-m-d H:i:s'),
				'total_um2'	                => $totaluangmuka2,
				'total_um_idr2'	            => $totaluangmuka2_idr,
				'id_top'	            	=> $id,
				'base_cur'					=> $base_cur,
				'total_retensi2'			=> $retensi_ppn/$kurs,
				'total_retensi2_idr'		=> $retensi_ppn,
				'sisa_invoice'	        	=> $this->input->post('total_invoice')/$kurs,
				'sisa_invoice_idr'	        => $this->input->post('total_invoice'),
				'so_number'					=> $so_number
			];

			if($jenis_invoice=='progress'){
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
						$spesifikasi		  = $d1['spesifikasi'];
						$harga_sat     		  = $d1['harga_sat'];
						$qty=0;$checked='';
						if(isset($d1['qty'])){
							$qty              = $d1['qty'];$checked='1';
						}
						$unit1                = $d1['unit1'];
						$harga_tot     		  = $d1['harga_tot'];
						$no_ippdtl     		  = $d1['no_ipp'];
						$no_sodtl		      = $d1['no_so'];
						$qty_ori			  = $d1['qty_ori'];
						$qty_belum			  = $d1['qty_belum'];

						$detailInv1[$val]['id_penagihan']		= $id;
						$detailInv1[$val]['id_bq'] 		     	= $no_bq;
						$detailInv1[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv1[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv1[$val]['spesifikasi']	    = $spesifikasi;
						$detailInv1[$val]['unit']	            = $unit1;
						$detailInv1[$val]['harga_satuan']	    = $harga_sat/$kurs;
						$detailInv1[$val]['harga_satuan_idr']	= $harga_sat;
						$detailInv1[$val]['qty']	            = $qty;
						$detailInv1[$val]['harga_total']	    = $harga_tot/$kurs;
						$detailInv1[$val]['harga_total_idr']	= $harga_tot;
						$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
						$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv1[$val]['qty_total']			= $qty_ori;
						$detailInv1[$val]['qty_sisa']			= $qty_belum;
						$detailInv1[$val]['checked']			= $checked;
						$detailInv1[$val]['id_milik']	    	= $d1['id_milik'];
						$detailInv1[$val]['cogs']	    		= $d1['cogs'];

					}
				}

				$detailInv2 = [];
				if(!empty($_POST['data2'])){
					foreach($_POST['data2'] as $val => $d2){
						$material_name2	= $d2['material_name2'];
						$material_desc2		= $d2['material_desc2'];
						$harga_sat2		= $d2['harga_sat2'];
						$qty2=0;$checked='';
						if(isset($d2['qty2'])){
							$qty2		= $d2['qty2'];
							if($qty2>0) $checked='1';
						}
						$unit2			= $d2['unit2'];
						$harga_tot2		= $d2['harga_tot2'];
						$no_ippdtl		= $d2['no_ipp'];
						$no_sodtl		= $d2['no_so'];
						$qty2_ori		= $d2['qty2_ori'];
						$qty2_belum		= $d2['qty2_belum'];

						$detailInv2[$val]['id_penagihan']		= $id;
						$detailInv2[$val]['id_bq'] 		     	= $no_bq;
						$detailInv2[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv2[$val]['so_number'] 		    = $no_sodtl;
						$detailInv2[$val]['no_invoice'] 		= $no_invoice;
						$detailInv2[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv2[$val]['id_customer']	 	= $id_customer;
						$detailInv2[$val]['nm_customer'] 		= $nm_customer;
						$detailInv2[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv2[$val]['nm_material']	    = $material_name2." ".$material_desc2;
						$detailInv2[$val]['unit']	            = $unit2;
						$detailInv2[$val]['harga_satuan']	    = $harga_sat2/$kurs;
						$detailInv2[$val]['harga_satuan_idr']	= $harga_sat2;
						$detailInv2[$val]['qty']	            = $qty2;
						$detailInv2[$val]['harga_total']	    = $harga_tot2/$kurs;
						$detailInv2[$val]['harga_total_idr']	= $harga_tot2;
						$detailInv2[$val]['kategori_detail']	= 'BQ';
						$detailInv2[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv2[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv2[$val]['desc']	    		= $material_desc2;
						$detailInv2[$val]['qty_total']			= $qty2_ori;
						$detailInv2[$val]['qty_sisa']			= $qty2_belum;
						$detailInv2[$val]['checked']			= $checked;
						$detailInv2[$val]['id_milik']	    	= $d2['id_milik'];
					}
				}

				$detailInv3 = [];
				if(!empty($_POST['data3'])){
					foreach($_POST['data3'] as $val => $d3){
						$material_name3	= $d3['material_name3'];
						$harga_sat3		= $d3['harga_sat3'];
						$qty3=0;$checked='';
						if(isset($d3['qty3'])){
							$qty3                = $d3['qty3'];
							if($qty3>0) $checked='1';
						}
						$unit3                   = $d3['unit3'];
						$harga_tot3      		 = $d3['harga_tot3'];
						$no_ippdtl     		  	 = $d3['no_ipp'];
						$no_sodtl		      	 = $d3['no_so'];
						$product_cust         	 = $d3['product_cust'];
						$product_desc         	 = $d3['product_desc'];
						$qty3_ori				 = $d3['qty3_ori'];
						$qty3_belum				 = $d3['qty3_belum'];

						$detailInv3[$val]['id_penagihan']		= $id;
						$detailInv3[$val]['id_bq'] 		     	= $no_bq;
						$detailInv3[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv3[$val]['so_number'] 		    = $no_sodtl;
						$detailInv3[$val]['no_invoice'] 		= $no_invoice;
						$detailInv3[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv3[$val]['id_customer']	 	= $id_customer;
						$detailInv3[$val]['nm_customer'] 		= $nm_customer;
						$detailInv3[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv3[$val]['nm_material']	    = $material_name3;
						$detailInv3[$val]['product_cust']	    = $product_cust;
						$detailInv3[$val]['desc']	    		= $product_desc;
						$detailInv3[$val]['unit']	            = $unit3;
						$detailInv3[$val]['harga_satuan']	    = $harga_sat3/$kurs;
						$detailInv3[$val]['harga_satuan_idr']	= $harga_sat3;
						$detailInv3[$val]['qty']	            = $qty3;
						$detailInv3[$val]['harga_total']	    = $harga_tot3/$kurs;
						$detailInv3[$val]['harga_total_idr']	= $harga_tot3;
						$detailInv3[$val]['kategori_detail']	= 'MATERIAL';
						$detailInv3[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv3[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv3[$val]['qty_total']			= $qty3_ori;
						$detailInv3[$val]['qty_sisa']			= $qty3_belum;
						$detailInv3[$val]['checked']			= $checked;
						$detailInv3[$val]['id_milik']	    	= $d3['id_milik'];

					}
				}

				$detailInv4 = [];
				if(!empty($_POST['data4'])){
					foreach($_POST['data4'] as $val => $d4){
						$material_name4          = $d4['material_name4'];
						$harga_sat4       		 = 0;
						$qty4                    = 0;
						$unit4                   = $d4['unit4'];
						$harga_tot4=0;$checked='';
						if(isset($d4['harga_tot4'])){
							$harga_tot4       	 = $d4['harga_tot4'];
							if($harga_tot4>0) $checked='1';
						}
						$no_ippdtl     		  	 = $d4['no_ipp'];
						$no_sodtl		      	 = $d4['no_so'];
						$harga_tot4_ori			 = $d4['harga_tot4_ori'];
						$harga_tot4_sisa		 = $d4['harga_tot4_sisa'];

						$detailInv4[$val]['id_penagihan']		= $id;
						$detailInv4[$val]['id_bq'] 		     	= $no_bq;
						$detailInv4[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv4[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv4[$val]['harga_total']	    = $harga_tot4/$kurs;
						$detailInv4[$val]['harga_total_idr']	= $harga_tot4;
						$detailInv4[$val]['kategori_detail']	= 'ENGINERING';
						$detailInv4[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv4[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv4[$val]['desc']	    		= $material_name4;
						$detailInv4[$val]['harga_total_so']		= $harga_tot4_ori;
						$detailInv4[$val]['harga_sisa_so']		= $harga_tot4_sisa;
						$detailInv4[$val]['checked']			= $checked;
						$detailInv4[$val]['id_milik']	    	= $d4['id_milik'];

					}
				}

				$detailInv5 = [];
				if(!empty($_POST['data5'])){
					foreach($_POST['data5'] as $val => $d5){
						$material_name5          = $d5['material_name5'];
						$unit5                   = $d5['unit5'];
						$harga_tot5=0;$checked='';
						if(isset($d5['harga_tot5'])){
							$harga_tot5   = $d5['harga_tot5'];
							if($harga_tot5>0) $checked='1';
						}
						$no_ippdtl     		  	 = $d5['no_ipp'];
						$no_sodtl		      	 = $d5['no_so'];
						$harga_tot5_ori			 = $d5['harga_tot5_ori'];
						$harga_tot5_sisa	     = $d5['harga_tot5_sisa'];

						$detailInv5[$val]['id_penagihan']			= $id;
						$detailInv5[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv5[$val]['no_ipp'] 		     	= $no_ippdtl;
						$detailInv5[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv5[$val]['harga_total']	        = $harga_tot5/$kurs;
						$detailInv5[$val]['harga_total_idr']	    = $harga_tot5;
						$detailInv5[$val]['kategori_detail']	    = 'PACKING';
						$detailInv5[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv5[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv5[$val]['desc']	    			= $material_name5;
						$detailInv5[$val]['harga_total_so']			= $harga_tot5_ori;
						$detailInv5[$val]['harga_sisa_so']			= $harga_tot5_sisa;
						$detailInv5[$val]['checked']			 	= $checked;
						$detailInv5[$val]['id_milik']	    		= $d5['id_milik'];
					}
				}

				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$harga_sat6       		 = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6=0;$checked='';
						if(isset($d6['harga_tot6'])){
							$harga_tot6   		= $d6['harga_tot6'];
							if($harga_tot6>0) $checked='1';
						}
						$no_ippdtl				= $d6['no_ipp'];
						$no_sodtl				= $d6['no_so'];
						$harga_tot6_ori			= $d6['harga_tot6_ori'];
						$harga_tot6_sisa		= $d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ippdtl;
						$detailInv6[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6/$kurs;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];
					}
				}

				$detailInv9 = [];
				if(!empty($_POST['data9'])){
					foreach($_POST['data9'] as $val => $d){
						$material_name9	= $d9['material_name9'];
						$harga_sat9		= $d9['harga_sat9'];
						$qty9=0;$checked='';
						if(isset($d9['qty9'])){
							$qty9	= $d9['qty9'];
							if($qty9>0) $checked='1';
						}
						$unit9			= $d9['unit9'];
						$harga_tot9		= $d9['harga_tot9'];
						$no_ippdtl		= $d9['no_ipp'];
						$no_sodtl		= $d9['no_so'];
						$product_cust	= $d9['product_cust'];
						$product_desc	= $d9['product_desc'];
						$qty9_ori		= $d9['qty9_ori'];
						$qty9_belum		= $d9['qty9_belum'];

						$detailInv9[$val]['id_penagihan']		= $id;
						$detailInv9[$val]['id_bq'] 		     	= $no_bq;
						$detailInv9[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv9[$val]['so_number'] 		    = $no_sodtl;
						$detailInv9[$val]['no_invoice'] 		= $no_invoice;
						$detailInv9[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv9[$val]['id_customer']	 	= $id_customer;
						$detailInv9[$val]['nm_customer'] 		= $nm_customer;
						$detailInv9[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv9[$val]['nm_material']	    = $material_name9;
						$detailInv9[$val]['unit']	            = $unit9;
						$detailInv9[$val]['harga_satuan']	    = $harga_sat9;
						$detailInv9[$val]['harga_satuan_idr']	= $harga_sat9*$kurs;
						$detailInv9[$val]['qty']	            = $qty9;
						$detailInv9[$val]['harga_total']	    = $harga_tot9;
						$detailInv9[$val]['harga_total_idr']	= $harga_tot9*$kurs;
						$detailInv9[$val]['kategori_detail']	= 'OTHER';
						$detailInv9[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv9[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv9[$val]['product_cust']	    = $product_cust;
						$detailInv9[$val]['desc']	    		= $product_desc;
						$detailInv9[$val]['qty_total']			= $qty9_ori;
						$detailInv9[$val]['qty_sisa']			= $qty9_belum;
						$detailInv9[$val]['checked']			= $checked;
						$detailInv9[$val]['id_milik']	    	= $d9['id_milik'];
					}
				}
			}

			if($jenis_invoice=='retensi'){
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6		= $d6['material_name6'];
						$no_ipp_dtl			= $d6['no_ipp'];
						$no_so_dtl			= $d6['no_so'];
						$harga_sat6       	= 0;
						$qty6				= 0;$checked='1';
						$unit6				= $d6['unit6'];
						$harga_tot6       	= $d6['harga_tot6'];
						$harga_tot6_ori		= $harga_tot6;//$d6['harga_tot6_ori'];
						$harga_tot6_sisa	= 0;//$d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = 'BQ-'.$no_ipp_dtl;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp_dtl;
						$detailInv6[$val]['so_number'] 		     	= $no_so_dtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6/$kurs;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						//$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];
					}
				}
			}

			$get_bill_so = $this->db->query("select * from billing_so where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr;
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka' && $um_persen2 < 1){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen']=$valx->uang_muka_persen + $um_persen;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka + ($grand_total*$perseninv/$kurs);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr + ($grand_total*$perseninv);
				}
			}

			if($jenis_invoice=='uang muka' && $um_persen2 > 0){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen2']=$valx->uang_muka_persen2 + $um_persen2;
					$ArrBillSO[$nox]['uang_muka2']=$valx->uang_muka2 + ($total_invoice*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr2']=$valx->uang_muka_idr2 + ($total_invoice_idr*$perseninv);
//					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_idr*$perseninv);
//					$ArrBillSO[$nox]['retensi_um']=$valx->retensi_um + ($retensi*$perseninv);
//					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_um_idr + ($retensi_idr*$perseninv);
				}
			}

			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka_idr - ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr - ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice']=$valx->uang_muka_invoice + ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice_idr']=$valx->uang_muka_invoice_idr + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['persentase_progress']=$umpersen;
					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi/$kurs*$perseninv);
					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_idr*$perseninv);
					$ArrBillSO[$nox]['retensi_um']=$valx->retensi + ($retensi_ppn/$kurs*$perseninv);
					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_idr + ($retensi_ppn*$perseninv);
				}
			}

			$ArrUM = [
				'proses_inv' => '1'
			];

			if($jenis_invoice == 'progress'){
				$stsx = 12;
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
				'grand_total' => $grand_total,
				'status'	=> $stsx,
				'real_tagih_usd'	=> $this->input->post('total_invoice') /$kurs,
				'real_tagih_idr'	=> $this->input->post('total_invoice')
			];
}
			$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('penagihan', $headerinv);
				$this->db->query("delete from penagihan_detail where id_penagihan='".$id."'");
				if(!empty($detailInv1)){
					$this->db->insert_batch('penagihan_detail',$detailInv1);
				}
				if(!empty($detailInv2)){
					$this->db->insert_batch('penagihan_detail',$detailInv2);
				}
				if(!empty($detailInv3)){
					$this->db->insert_batch('penagihan_detail',$detailInv3);
				}
				if(!empty($detailInv4)){
					$this->db->insert_batch('penagihan_detail',$detailInv4);
				}
				if(!empty($detailInv5)){
					$this->db->insert_batch('penagihan_detail',$detailInv5);
				}
				if(!empty($detailInv6)){
					$this->db->insert_batch('penagihan_detail',$detailInv6);
				}
				if(!empty($detailInv9)){
					$this->db->insert_batch('penagihan_detail',$detailInv9);
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
				history('Update Penagihan '.$jenis_invoice.' '.$id);
			}
			echo json_encode($Arr_Return);
	} 
	
	}

	
	public function create_progress_new_delivery_approval(){
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
			$base_cur		= $penagihan[0]->base_cur;
			$approval	= $this->uri->segment(4);
			//print_r($approval);exit;
			$getBq 		= $this->db->select('no_ipp as no_po, base_cur')->where_in('id',$nomor_id)->get('billing_so')->result_array();

			$in_ipp = [];
			$in_bq = [];

			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= get_nomor_so($valx['no_po']);
				//$base_cur		= $valx['base_cur'];
			}
			if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}
			$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
			$noipp=implode("','",$in_ipp);
			$id_produksi=implode("','PRO-",$in_ipp);
			$id_bq=implode("','BQ-",$in_ipp);
			$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
			if(!empty($penagihan_detail)){
				$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
				from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
				$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
				$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
				$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
				$getOther  	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','OTHER')->get('penagihan_detail')->result_array();
				$non_frp	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
				$material	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
				
				$get_kurs1	= $this->db->select(' (kurs_jual) AS kurs,  (progress_persen) AS uang_muka_persen,  0 AS uang_muka_persen2')->where('id',$id)->get('penagihan')->result();
				
				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs,sisa_um AS sisa_um,sisa_um_idr AS sisa_um_idr from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
				$sisa_um   = $get_kurs[0]->sisa_um;
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				$sisa_um_idr   = $get_kurs[0]->sisa_um_idr;

				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
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
			'other' 		=> $getOther,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'list_top'		=> $list_top,
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs1[0]->kurs,
			'uang_muka_persen'	=> $uang_muka_persen,
			'uang_muka_persen2'	=> 0,
			'down_payment'	=> $down_payment,
			'sisa_um'	    => $sisa_um,
			'sisa_um_idr'	    => $sisa_um_idr,
			'down_payment2'	=> $down_payment2,
			'id'			=> $id,
			'approval'		=> $approval
			);
			$this->load->view('Penagihan/create_progress_new_delivery_approval',$data2);
		
	}

	public function create_invoice(){ 
		$db2 			= $this->load->database('accounting', TRUE);
		$data_session 	= $this->session->userdata;
		$id   			= $this->uri->segment(3);
		$nomordoc 		= get_name('penagihan','no_invoice','id',$id);

		$gethd 			= $this->db->query("SELECT * FROM penagihan WHERE id='$id'")->row();
		$tgl       		= $gethd->tgl_invoice;
		$Jml_Ttl   		= $gethd->total_invoice_idr;
		$Id_klien     	= $gethd->kode_customer;
		$Nama_klien   	= $gethd->nm_customer;
		$jenis_invoice  = $gethd->type;
		$Bln 			= substr($tgl,5,2);
		$Thn 			= substr($tgl,0,4);
		$tot_retensi    = $gethd->total_retensi_idr;
		$tot_um         = $gethd->total_um_idr;
		$isppn			= $gethd->total_ppn_idr;
		$total_retensi2_idr	= $gethd->total_retensi2_idr;
		$base_cur		= $gethd->base_cur;
		$no_po			= $gethd->no_po;
		$created_on      = date('Y-m-d H:i:s');
		$created_by     = $data_session['ORI_User']['username'];
		$no_delivery	= $gethd->delivery_no;
		$kode_delivery  ="'" . str_replace(",", "','", $no_delivery) . "'"; 
		$instalasi      = $gethd->instalasi;

		$this->db->trans_begin();
		$db2->trans_begin();

		$dt_no_ipp 	= explode(",",$gethd->no_ipp);
		if($jenis_invoice=='progress'){
			if($base_cur=='IDR' || $base_cur==''){
				$kodejurnal1		= 'JV061';
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set
				total_invoice_idr=(total_invoice_idr+".$gethd->total_dpp_rp."), 
				total_retensi_idr=(total_retensi_idr+".$gethd->total_retensi_idr."), 
				total_retensi2_idr=(total_retensi2_idr+".$gethd->total_retensi2_idr."), 
				sisa_um_idr=(sisa_um_idr-".$gethd->total_um_idr.") WHERE nomor_po='$no_po'");
			}else{
				$kodejurnal1		= 'JV064';
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set
				total_invoice=(total_invoice+".$gethd->total_dpp_usd."), 
				total_invoice_idr=(total_invoice_idr+".$gethd->total_dpp_rp."), 
				total_retensi=(total_retensi+".$gethd->total_retensi."), 
				total_retensi_idr=(total_retensi_idr+".$gethd->total_retensi_idr."), 
				total_retensi2=(total_retensi2+".$gethd->total_retensi2."), 
				total_retensi2_idr=(total_retensi2_idr+".$gethd->total_retensi2_idr."), 
				sisa_um=(sisa_um+".$gethd->total_um."), 
				sisa_um_idr=(sisa_um_idr+".$gethd->total_um_idr.") WHERE nomor_po='$no_po'");
			}
			$Keterangan_INV1	= 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				$get_billing_so = $this->db->query("SELECT * FROM billing_so WHERE no_ipp='$keys'")->row();
				$getdtlinv = $this->db->query("SELECT sum(harga_total_idr) as total_dpp_rp FROM penagihan_detail WHERE id_penagihan='".$gethd->id."' and no_ipp='$keys' and checked=1")->row();
				$total_dpp_rp=0;
				$persentase=0;
				if(!empty($getdtlinv)){
					$total_dpp_rp=$getdtlinv->total_dpp_rp;
					if($total_dpp_rp=="") $total_dpp_rp=0;
					//$persentase=round(($total_dpp_rp/$get_billing_so->total_deal_idr*100),2);
					//$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$persentase."), total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					$this->db->query("update billing_so set total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
				}
			}
		}
		elseif($jenis_invoice=='uang muka'){
			if($base_cur=='IDR' || $base_cur==''){
				$kodejurnal1		= 'JV050';
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set 
				persen_um = (persen_um+".$gethd->persentase."),
				total_um_idr=(total_um_idr+".$gethd->total_dpp_rp."), 
				sisa_um_idr=(sisa_um_idr+".$gethd->total_dpp_rp.")
				WHERE nomor_po='$no_po'");
			}else{
				$kodejurnal1		= 'JV065';
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set 
				persen_um = (persen_um+".$gethd->persentase."),
				total_um=(total_um+".$gethd->total_dpp_usd."), 
				total_um_idr=(total_um_idr+".$gethd->total_dpp_rp."), 
				sisa_um=(sisa_um+".$gethd->total_dpp_usd."), 
				sisa_um_idr=(sisa_um_idr+".$gethd->total_dpp_rp.")
				WHERE nomor_po='$no_po'");
				$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
			}
			$Keterangan_INV1	= 'UANG MUKA PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				//$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$gethd->persentase."), total_invoice=(total_invoice+".$gethd->total_dpp_rp.") WHERE no_ipp='$keys'");
				$this->db->query("update billing_so set total_invoice=(total_invoice+".$gethd->total_dpp_rp.") WHERE no_ipp='$keys'");
			}
		}
		elseif($jenis_invoice=='retensi'){
			if($isppn>0){
				if($base_cur=='IDR' || $base_cur==''){
					$kodejurnal1	= 'JV052';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set 
					total_retensi2_idr=(total_retensi2_idr-".$gethd->total_dpp_rp.") 
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}else{
					// tidak jadi dipakai
					$kodejurnal1	= 'JV066';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set  
					total_retensi2=(total_retensi2-".$gethd->total_dpp_usd."), 
					total_retensi2_idr=(total_retensi2_idr-".$gethd->total_dpp_rp.")
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}				
			}else{
				if($base_cur=='IDR' || $base_cur==''){
					$kodejurnal1	= 'JV054';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set 
					persen_um = (persen_um+".$gethd->persentase."),
					total_retensi_idr=(total_retensi_idr-".$gethd->total_dpp_rp.") 
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}else{
					$kodejurnal1	= 'JV067';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set 
					total_retensi=(total_retensi-".$gethd->total_dpp_usd.") 
					total_retensi_idr=(total_retensi_idr-".$gethd->total_dpp_rp.") 
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}
			}
			$Keterangan_INV1	= 'RETENSI PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				$get_billing_so = $this->db->query("SELECT * FROM billing_so WHERE no_ipp='$keys'")->row();
				$getdtlinv = $this->db->query("SELECT sum(harga_total_idr) as total_dpp_rp FROM penagihan_detail WHERE id_penagihan='".$gethd->id."' and no_ipp='$keys' and checked='1'")->row();
				$total_dpp_rp=0;
				$persentase=0;
				if(!empty($getdtlinv)){
					$total_dpp_rp=$getdtlinv->total_dpp_rp;
					if($total_dpp_rp=="") $total_dpp_rp=0;
					//$persentase=round(($total_dpp_rp/$get_billing_so->total_deal_idr*100),2);
					//$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$persentase."), total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					$this->db->query("update billing_so set total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
				}
			}
		}

		//insert invoice
		$this->db->query("insert into tr_invoice_header (`id_penagihan`, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `id_customer`, `nm_customer`, `jenis_invoice`, `kurs_jual`, `persentase`, `total_product`, `total_product_idr`, `total_gab_product`, `total_gab_product_idr`, `total_material`, `total_material_idr`, `total_bq`, `total_bq_idr`, `total_enginering`, `total_enginering_idr`, `total_packing`, `total_packing_idr`, `total_trucking`, `total_trucking_idr`, `total_dpp_usd`, `total_dpp_rp`, `total_diskon`,  `total_diskon_idr`, `total_retensi`, `total_retensi_idr`, `total_ppn`, `total_ppn_idr`, `total_invoice`, `total_invoice_idr`,  `total_um`, `total_um_idr`, `total_bayar`, `total_bayar_idr`,  `created_by`, `created_date`, `no_po`, `no_faktur`, `no_pajak`, `payment_term`, `proses_print`, `approved`, `approved_by`, `approved_date`, `total_um2`, `total_um_idr2`, `id_top`, `base_cur`, `total_retensi2`, `total_retensi2_idr`, `printed_on`, `sisa_invoice_idr`, `sisa_invoice`, sisa_invoice_retensi2, sisa_invoice_retensi2_idr) 
		select $id, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `kode_customer`, `nm_customer`, `type`, `kurs_jual`, `persentase`, `total_product`, `total_product_idr`, `total_gab_product`, `total_gab_product_idr`, `total_material`, `total_material_idr`, `total_bq`, `total_bq_idr`, `total_enginering`, `total_enginering_idr`, `total_packing`,  `total_packing_idr`, `total_trucking`, `total_trucking_idr`, `total_dpp_usd`, `total_dpp_rp`, `total_diskon`, `total_diskon_idr`, `total_retensi`, `total_retensi_idr`, `total_ppn`, `total_ppn_idr`, `total_invoice`, `total_invoice_idr`, `total_um`, `total_um_idr`, `total_bayar`, `total_bayar_idr`, `created_by`, `created_date`, `no_po`, `no_faktur`, `no_pajak`, `payment_term`, '1', 'Y', '".$data_session['ORI_User']['username']."', now(), `total_um2`, `total_um_idr2`, `id_top`, `base_cur`,  `total_retensi2`, `total_retensi2_idr`, '1', `sisa_invoice_idr`, `sisa_invoice`, `total_retensi2`, `total_retensi2_idr` from penagihan WHERE id='$id'");
		//insert invoice detail
		$this->db->query("INSERT INTO `tr_invoice_detail` (`id_penagihan`, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `id_customer`, `nm_customer`, `jenis_invoice`, `nm_material`, `product_cust`, `desc`, `dim_1`, `dim_2`, `liner`, `pressure`, `spesifikasi`, `unit`, `harga_satuan`, `harga_satuan_idr`, `qty`, `harga_total`, `harga_total_idr`, `kategori_detail`, `created_by`, `created_date`, `id_billing_dtl`) select `id_penagihan`, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `id_customer`, `nm_customer`, `jenis_invoice`, `nm_material`, `product_cust`, `desc`, `dim_1`, `dim_2`, `liner`, `pressure`, `spesifikasi`, `unit`, `harga_satuan`, `harga_satuan_idr`, `qty`, `harga_total`, `harga_total_idr`, `kategori_detail`, '".$data_session['ORI_User']['username']."', now(), `id_billing_dtl` from penagihan_detail where checked='1' and id_penagihan=$id");

//		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_Sales('101',$tgl);
		$Nomor_JV				= get_generate_jurnal('GJ',date('y-m-d'));
		$Keterangan_INV		    = 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
		
		$invoice 			= $this->db->query("SELECT * FROM tr_invoice_header WHERE id_penagihan='$id'")->row();
		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY
		$datajurnal1  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal1);
		$nilaitotaljurnal=0;
		foreach($datajurnal1 AS $rec){
			$tabel1  		= $rec->menu;
			$posisi1 		= $rec->posisi;
			$field1  		= $rec->field;
			$param1  		= 'no_invoice';
			$value_param1  	= $nomordoc;
			$val1 			= $this->Acc_model->GetData($tabel1,$field1,$param1,$value_param1);
			$nilaibayar1 	= $val1[0]->$field1;
			$nokir1  = $rec->no_perkiraan;
			
			if($nokir1=='1102-01-02' OR $nokir1=='2102-01-03' ){

				if ($nilaibayar1 > 1) {
                $total_invoice = $invoice->total_invoice;
				$kurs_jual     = $invoice->kurs_jual;
				}else{
				$total_invoice = 0;
				$kurs_jual     = 0;
				}
			} 
			else{
				$total_invoice = 0;
				$kurs_jual     = 0;
			}
			
			if ($posisi1=='D'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $nilaibayar1,
					'kredit'        => 0,
					'nilai_valas_debet'  => $total_invoice,
					'nilai_valas_kredit' => 0,
					'kurs_transaksi'     => $kurs_jual,
					'created_on'         => $created_on,
					'created_by'         => $created_by
					//'jenis_jurnal'  => 'invoicing'
				);
				$nilaitotaljurnal=($nilaitotaljurnal+$nilaibayar1);
			}
			elseif ($posisi1=='K'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $nilaibayar1,
					'nilai_valas_debet'  => 0,
					'nilai_valas_kredit' => 0,
					'kurs_transaksi'     => 0,
					'created_on'         => $created_on,
					'created_by'         => $created_by
					//'jenis_jurnal'  => 'invoicing'
				);
			}
		}
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tgl,
			'jml'	            => $nilaitotaljurnal,
			'koreksi_no'		=> '-',
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan' 		=> $Keterangan_INV1,
			'bulan'				=> $Bln,
			'tahun'				=> $Thn,
			'user_id'			=> $data_session['ORI_User']['username'],
			'memo'			    => $nomordoc,
			'tgl_jvkoreksi'	    => $tgl,
			'ho_valid'			=> ''
		);

		if($jenis_invoice=='uang muka'){
			if($base_cur=='IDR' || $base_cur==''){
				$coa_uangmuka='2102-01-01';
				$coa_piutang='1102-01-01';
			}else{
				$coa_uangmuka='2102-01-03';
				$coa_piutang='1102-01-02';
			}
			//uang muka
			$datapiutang = array(
				'tipe'       	=> 'JV',
				'nomor'       	=> $Nomor_JV,
				'tanggal'       => $tgl,
				'no_perkiraan'  => $coa_uangmuka,
				'keterangan'    => 'UM PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc,
				'no_reff'       => $nomordoc,
				'debet'         => 0,
				'kredit'        => $gethd->total_dpp_rp,
				'debet_usd'		=> 0,
				'kredit_usd'    => $gethd->total_dpp_usd,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);
			// piutang
			$datapiutang = array(
				'tipe'       	=> 'JV',
				'nomor'       	=> $Nomor_JV,
				'tanggal'       => $tgl,
				'no_perkiraan'  => $coa_piutang,
				'keterangan'    => $Keterangan_INV1,
				'no_reff'       => $nomordoc,
				'debet'         => $gethd->total_invoice_idr,
				'kredit'        => 0,
				'debet_usd'		=> $gethd->total_invoice,
				'kredit_usd'	=> 0,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);

		}

		if($jenis_invoice=='progress'){
			if($base_cur=='IDR' || $base_cur==''){
				$coa_uangmuka='2102-01-01';
				$coa_piutang='1102-01-01';
				$coa_uninvoicing='1102-01-03';
			}else{
				$coa_uangmuka='2102-01-03';
				$coa_piutang='1102-01-02';
				$coa_uninvoicing='1102-01-04';
			}
			//uang muka
			if($gethd->total_um_idr<>0){
				$datapiutang = array(
					'tipe'       	=> 'JV',
					'nomor'       	=> $Nomor_JV,
					'tanggal'       => $tgl,
					'no_perkiraan'  => $coa_uangmuka,
					'keterangan'    => 'POTONG UM PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc,
					'no_reff'       => $no_po,
					'debet'         => $gethd->total_um_idr,
					'kredit'        => 0,
					'debet_usd'		=> $gethd->total_um,
					'kredit_usd'    => 0,
					'id_supplier'   => $Id_klien,
					'nama_supplier' => $Nama_klien,
				);
				$this->db->insert('tr_kartu_piutang',$datapiutang);
			}
			//uninvoicing
			if($gethd->total_retensi2_idr<>0){
				$datapiutang = array(
					'tipe'       	=> 'JV',
					'nomor'       	=> $Nomor_JV,
					'tanggal'       => $tgl,
					'no_perkiraan'  => $coa_uninvoicing,
					'keterangan'    => 'UN INVOICING PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc,
					'no_reff'       => $nomordoc,
					'debet'         => $gethd->total_retensi2_idr,
					'kredit'        => 0,
					'debet_usd'		=> $gethd->total_retensi2,
					'kredit_usd'    => 0,
					'id_supplier'   => $Id_klien,
					'nama_supplier' => $Nama_klien,
				);
				$this->db->insert('tr_kartu_piutang',$datapiutang);
			}
			//piutang
			$datapiutang = array(
				'tipe'       	=> 'JV',
				'nomor'       	=> $Nomor_JV,
				'tanggal'       => $tgl,
				'no_perkiraan'  => $coa_piutang,
				'keterangan'    => $Keterangan_INV1,
				'no_reff'       => $nomordoc,
				'debet'         => $gethd->total_invoice_idr,
				'kredit'        => 0,
				'debet_usd'		=> $gethd->total_invoice,
				'kredit_usd'    => 0,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);

			
		}

		if($jenis_invoice=='retensi'){
			if($isppn>0){
				if($base_cur=='IDR' || $base_cur==''){
					$coa_piutang='1102-01-01';
				}else{
					$coa_piutang='1102-01-02';
				}
				//piutang
				$datapiutang = array(
					'tipe'       	=> 'JV',
					'nomor'       	=> $Nomor_JV,
					'tanggal'       => $tgl,
					'no_perkiraan'  => $coa_piutang,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $gethd->total_invoice_idr,
					'kredit'        => 0,
					'debet_usd'		=> $gethd->total_invoice,
					'kredit_usd'    => 0,
					'id_supplier'   => $Id_klien,
					'nama_supplier' => $Nama_klien,
				);
				$this->db->insert('tr_kartu_piutang',$datapiutang);
			}else{
				if($base_cur=='IDR' || $base_cur==''){
				}else{
				}
			}
		}
			
			$Qry_Update_Cabang_acc	 = "UPDATE pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
			$db2->query($Qry_Update_Cabang_acc);

			$nomor_id 	= explode(",",$gethd->no_so);
			$getBq 		= $this->db->select('no_ipp as no_po')->where_in('id',$nomor_id)->get('billing_so')->result_array();

			$in_ipp = [];
			$in_bq = [];

			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= get_nomor_so($valx['no_po']);
			}
			$get_bill_so = $this->db->query("select * from billing_so where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr;
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv1=($valx->total_deal_usd/$totalinvoice);
					if($perseninv1 < 1){
                    $perseninv = 0;
					}else{
                    $perseninv = $perseninv1;
					}
					$this->db->query("update billing_so set
					uang_muka_persen=(uang_muka_persen+".$gethd->persentase."),
					uang_muka=(uang_muka+".($gethd->total_dpp_usd*$perseninv)."),
					uang_muka_idr=(uang_muka_idr+".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");
				}
			}
			if($jenis_invoice=='retensi'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
				  if($gethd->ppnselect==0){
					$this->db->query("update billing_so set
					retensi=(retensi-".($gethd->total_dpp_usd*$perseninv)."),
					retensi_idr=(retensi_idr-".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");
				  }else{
					$this->db->query("update billing_so set
					retensi_um=(retensi_um-".($gethd->total_dpp_usd*$perseninv)."),
					retensi_um_idr=(retensi_um_idr-".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");
				  }
				}
			}
			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$this->db->query("update billing_so set
					uang_muka_invoice=(uang_muka_invoice+".($gethd->total_um*$perseninv)."),
					uang_muka_invoice_idr=(uang_muka_invoice_idr+".$gethd->total_um_idr*$perseninv."),
					persentase_progress='".$gethd->persentase*$perseninv."',
					retensi=(retensi+'".$gethd->total_retensi*$perseninv."'),
					retensi_idr=(retensi_idr+'".$gethd->total_retensi_idr*$perseninv."'),
					retensi_um=(retensi_um+'".$gethd->total_retensi2*$perseninv."'),
					retensi_um_idr=(retensi_um_idr+'".$gethd->total_retensi2_idr*$perseninv."'),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");
				}

				$getdetail	= $this->db->query("SELECT * FROM penagihan_detail WHERE id_penagihan='$id' and checked='1'")->result();
				$nox = 0;
				if(!empty($getdetail)){
				  foreach($getdetail as $valx){
					 $nox++;
					 if($valx->kategori_detail=='PRODUCT'){
						 $this->db->query("update billing_so_product set qty_inv=(qty_inv+".$valx->qty.") WHERE id_milik='".$valx->id_milik."'");
					 }else{
						$this->db->query("update billing_so_add set qty_inv=(qty_inv+".$valx->qty."), total_deal_inv=(total_deal_inv+".$valx->harga_total."), total_deal_inv_idr=(total_deal_inv_idr+".$valx->harga_total_idr.") WHERE id_milik='".$valx->id_milik."'");
					 }

				  }
				}
			}
			$this->db->query("update penagihan set status='12' WHERE id='$id'");
			if(!empty($ArrBillSO)){
				$this->db->update_batch('billing_so', $ArrBillSO, 'id');
			}


		if($this->db->trans_status() === FALSE or $db2->trans_status()=== FALSE){
		 $this->db->trans_rollback();
		 $db2->trans_rollback();
		 $Arr_Return		= array(
				'status'		=> 2,
				'pesan'			=> 'Error Process Failed. Please Try Again...'
		   );
		}else{
			$db2->insert('javh',$dataJVhead);
			$db2->insert_batch('jurnal',$det_Jurnaltes1);
			if($kodejurnal1		= 'JV061'){
            $tgl1       		= $gethd->tgl_invoice;

		  	if($instalasi !='1')
			{

				$this->db->query("INSERT INTO data_erp_in_customer (tanggal,keterangan,no_so,product,no_spk,kode_trans,id_pro_det,qty,nilai_unit,created_by,created_date,id_trans,id_pro,qty_ke,kode_delivery,jenis,id_material,nm_material,qty_mat,cost_book,gudang,kode_spool)			
				SELECT '".$tgl1."','In customer-COGS',no_so,product,no_spk,kode_trans,id_pro_det,qty,nilai_unit,created_by,created_date,id_trans,id_pro,qty_ke,kode_delivery,'out',id_material,nm_material,qty_mat,cost_book,gudang,kode_spool FROM data_erp_in_customer WHERE kode_delivery IN (".$kode_delivery.")");

				$this->db->query("INSERT INTO data_erp_cogs (tanggal,keterangan,no_so,product,no_spk,kode_trans,id_pro_det,qty,nilai_unit,created_by,created_date,id_trans,id_pro,qty_ke,kode_delivery,jenis,id_material,nm_material,qty_mat,cost_book,gudang,kode_spool)			
				SELECT '".$tgl1."','In customer-COGS',no_so,product,no_spk,kode_trans,id_pro_det,qty,nilai_unit,created_by,created_date,id_trans,id_pro,qty_ke,kode_delivery,'in',id_material,nm_material,qty_mat,cost_book,gudang,kode_spool FROM data_erp_in_customer WHERE kode_delivery IN (".$kode_delivery.")");

				$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,kode_trans,qty,id_trans, nilai_unit as finishgood  FROM data_erp_in_transit WHERE kode_delivery IN (".$kode_delivery.") AND jenis = 'out'")->result();
				

				foreach($wip AS $data){
						$nm_material = $data->product;	
						$tgl_voucher = $data->tanggal;	
						$spasi       = ',';
						$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
						$kode_trans = $data->kode_trans; 
						$nospk      = $data->no_spk;
						$noso      = $data->no_so;
						$qty1        = $data->qty;

						if ($qty1==null){
						$qty=1;	
						}else{
						$qty=$qty1;	
						}
						
						if (!empty($nm_material)){
						$this->db->query("UPDATE  warehouse_stock_intransit SET qty = qty-1  WHERE no_so ='".$noso."' AND kode_trans ='".$kode_trans."'  AND no_spk ='".$nospk."' AND product ='".$nm_material."'");
						}

				}



				$wipgroup = $this->db->query("SELECT * FROM data_erp_cogs WHERE kode_trans ='".$kode_trans."' AND tanggal='".$tgl1."' AND product IS NOT NULL limit 1")->row();	
				$kodetrans = $wipgroup->kode_trans;
				$Date      = $wipgroup->tanggal;
				$so        = $wipgroup->no_so;
				$spk       = $wipgroup->no_spk;
				$product   = $wipgroup->product;

				$stokfg = $this->db->query("SELECT
											`data_erp_in_customer`.`id` AS `id`,
											`data_erp_in_customer`.`tanggal` AS `tanggal`,
											`data_erp_in_customer`.`keterangan` AS `keterangan`,
											`data_erp_in_customer`.`no_so` AS `no_so`,
											`data_erp_in_customer`.`product` AS `product`,
											`data_erp_in_customer`.`no_spk` AS `no_spk`,
											`data_erp_in_customer`.`kode_trans` AS `kode_trans`,
											`data_erp_in_customer`.`id_pro_det` AS `id_pro_det`,
											sum(`data_erp_in_customer`.`qty`) AS `total`,
											`data_erp_in_customer`.`nilai_unit` AS `nilai_wip`,
											`data_erp_in_customer`.`created_by` AS `created_by`,
											`data_erp_in_customer`.`created_date` AS `created_date`,
											`data_erp_in_customer`.`id_trans` AS `id_trans`,
											`data_erp_in_customer`.`jenis` AS `jenis`,
											`data_erp_in_customer`.`id_material` AS `id_material`,
											`data_erp_in_customer`.`nm_material` AS `nm_material`,
											`data_erp_in_customer`.`qty_mat` AS `qty_mat`,
											`data_erp_in_customer`.`cost_book` AS `cost_book`,
											`data_erp_in_customer`.`gudang` AS `gudang`,
											`data_erp_in_customer`.`kode_spool` AS `kode_spool` 
											FROM
											`data_erp_in_customer` 
											WHERE
											(`data_erp_in_customer`.`kode_trans` = '".$kodetrans."') 
											AND (`data_erp_in_customer`.`jenis`='out')
											AND (`data_erp_in_customer`.`tanggal` = '".$Date."')
											GROUP BY kode_trans,no_spk,product,no_so")->result();

				$cekstok = $this->db->query("SELECT * FROM warehouse_stock_cogs WHERE kode_trans ='".$kodetrans."' 
				AND no_so ='".$so."' AND no_spk ='".$spk."' AND product ='".$product."'")->row();

				if(!empty($cekstok)){
					foreach ($stokfg as $vals) {
					$qty = 	$vals->total;
					$this->db->query("UPDATE  warehouse_stock_cogs SET qty = qty+1  WHERE no_so ='".$so."' AND kode_trans ='".$kodetrans."'  AND no_spk ='".$spk."' AND product ='".$product."' ");
					}
				}else{
				$datastokfg=array();
					foreach ($stokfg as $vals) {
					$datastokfg = array(
								'tanggal' => $tgl_voucher,
								'keterangan' => 'Incustomer To Cogs',
								'no_so' => $vals->no_so,
								'product' => $vals->product,
								'no_spk' => $vals->no_spk,
								'kode_trans' => $vals->kode_trans,
								'id_pro_det' => $vals->id_pro_det,
								'qty' => 1,
								'nilai_wip' => $vals->nilai_wip,
								'created_by' => $vals->created_by,
								'created_date' => $vals->created_date,
								'id_trans' => $vals->id_trans,
								);

					$this->db->insert('warehouse_stock_cogs',$datastokfg);
					}

				}
		
			}
			
		
		}
			

			$this->db->trans_commit();
			$db2->trans_commit();
		    $Arr_Return		= array(
			 'status'		=> 1,
			 'pesan'		=> 'Update Process Success. Thank You & Have A Nice Day...'
			 );
		}
		echo json_encode($Arr_Return);

	}


	public function add_new_instalasi(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;

			$check = $data['check'];
			$dtdelivery_no='';
			$dtListArray = [];
			if(!empty($check)){
				foreach($check AS $val => $valx){
					$dtListArray[$val] = $valx;
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";
				$dtImplode2	= implode(",", $dtListArray);
				$result_data 	= $this->db->query("SELECT * FROM billing_so WHERE id IN ".$dtImplode." ORDER BY id ")->result_array();
			}else{
				$Arr_Kembali	= array(
					'pesan'		=>'Process data failed. Please check input ...',
					'status'	=> 2
				);
				echo json_encode($Arr_Kembali);
				die();
			}
			$max_num 		= $this->db->select('MAX(id) AS nomor_max')->get('penagihan')->result();
			$id_tagih 		= $max_num[0]->nomor_max + 1;

			$SUM_USD = 0;
			$SUM_IDR = 0;
			$Update_b = [];
			foreach($result_data AS $val => $valx){
				$SUM_USD += $valx['total_deal_usd'];
				$SUM_IDR += $valx['total_deal_idr'];
				$no_ipp = str_replace('BQ-','',$valx['no_ipp']);

				$Update_b[$val]['id'] = $valx['id'];
				$Update_b[$val]['id_penagihan'] = $id_tagih;
				$base_cur = $valx['base_cur'];
			}
			$header = [
				'delivery_no' => $dtdelivery_no,
				'no_so' => $dtImplode2,
				'no_ipp' => $no_ipp,
				'no_po' => $data['no_po'],
				'project' => NULL,
				'kode_customer' => $data['customer'],
				'customer' => get_name('customer','nm_customer','id_customer',$data['customer']),
				'keterangan' => NULL,
				'plan_tagih_date' => date("Y-m-d"),
				'plan_tagih_usd' => $SUM_USD,
				'plan_tagih_idr' => $SUM_IDR,
				'type' => $data['type'],
				'base_cur' => $base_cur,
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
				'term_delivery' => $data['term_delivery'],
				'instalasi' => "1",
				'created_by' => $this->session->userdata['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			];
			$this->db->trans_start();
				$this->db->insert('penagihan', $header);
				$this->db->query("update billing_so set status='1' WHERE id IN ".$dtImplode." and status='0' ");
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

			$data = array(
				'title'			=> 'Indeks Of Add Billing',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'customer'		=> $customer,
				'no_po'			=> $no_po
			);

			$this->load->view('Penagihan/add_new_instalasi',$data);
		}
	}
	public function create_um_new_instalasi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$id    		= $this->uri->segment(3);
		$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
		$penagihan_detail = $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->result();
		$nomor_id 	= explode(",",$penagihan[0]->no_so);
		$getBq 		= $this->db->select('no_ipp as no_po, base_cur')->where_in('id',$nomor_id)->get('billing_so')->result_array();
		$in_ipp = [];
		$in_bq = [];
		foreach($getBq AS $val => $valx){
			$in_ipp[$val]	= $valx['no_po'];
			$in_bq[$val]	= 'BQ-'.$valx['no_po'];
			$in_so[$val]	= get_nomor_so($valx['no_po']);
			$base_cur		= $valx['base_cur'];
		}
		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
		$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
		$getEngCost	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
		$getPackCost= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
		$getTruck	= $this->db->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
		$non_frp	= $this->db->select('*')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
		$material	= $this->db->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
		if($penagihan[0]->kurs_jual==0){
			$get_kurs	= $this->db->select("(kurs_usd_dipakai) AS kurs, 0 AS uang_muka_persen, '0' AS uang_muka_persen2")->where_in("no_ipp",$in_ipp)->get("billing_so")->result();
		}else{
			$get_kurs	= $this->db->select("(kurs_jual) AS kurs, persentase AS uang_muka_persen, '0' AS uang_muka_persen2")->where("id",$id)->get("penagihan")->result();
		}

		$approval	= $this->uri->segment(4);
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
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'uang_muka_persen'	=> $get_kurs[0]->uang_muka_persen,
			'uang_muka_persen2'	=> $get_kurs[0]->uang_muka_persen2,
			'id'			=> $id,
			'approval'		=> $approval
		);
		$this->load->view('Penagihan/create_um_new_instalasi',$data);
	}

	public function create_progress_new_instalasi()
	{
		if($this->input->post()){
			$data_session	= $this->session->userdata;

			$id			= $this->input->post('id');
			$penagihan 	= $this->db->get_where('penagihan', array('id'=>$id))->result();
			$nomor_id 	= explode(",",$penagihan[0]->no_so);
			$getBq 		= $this->db->select('no_ipp as no_po')->where_in('id',$nomor_id)->get('billing_so')->result_array();

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
			$so_number				= $this->input->post('no_so');
			// sementara diganti
			// $no_invoice 			= gen_invoice($no_ipp);
			$no_invoice 			= $this->input->post('nomor_faktur');
			$id_customer			= $this->input->post('id_customer');
			$nm_customer			= $this->input->post('nm_customer');
			$no_bq                  = 'BQ-'.$no_ipp;
			$kurs                   = str_replace(',','',$this->input->post('kurs'));
			$jenis_invoice 			= strtolower($this->input->post('type'));
			$base_cur				= $this->input->post('base_cur');
			$um_persen2				= str_replace(',','',$this->input->post('um_persen2'));
			$umpersen				= str_replace(',','',$this->input->post('umpersen'));
			$grand_total          	= str_replace(',','',$this->input->post('grand_total'));
			$ppnselect				= $this->input->post('ppnselect');
			$progressx				= $this->input->post('progressx');
			$persen_retensi2		= $this->input->post('persen_retensi2');
			$persen_retensi			= $this->input->post('persen_retensi');
	if($base_cur=='USD'){
			$total_invoice          = $this->input->post('total_invoice');
			$total_invoice_idr      = $this->input->post('total_invoice')*$kurs;
			$total_um               = $this->input->post('down_payment');
			$total_um_idr           = $this->input->post('down_payment')*$kurs;
			$um_persen				= str_replace(',','',$this->input->post('um_persen'));
			$total_gab_product      = ($this->input->post('tot_product'))+($this->input->post('total_material'))+($this->input->post('total_bq_nf'));
			$total_gab_product_idr  = ($this->input->post('tot_product')*$kurs)+($this->input->post('total_material')*$kurs)+($this->input->post('total_bq_nf')*$kurs);

			$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi'));
			$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi2'));

			$diskon = (!empty($this->input->post('diskon')))?$this->input->post('diskon'):str_replace(',','',$this->input->post('diskon'));
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
				$totaluangmuka2 = $this->input->post('grand_total') - $this->input->post('down_payment');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			if($jenis_invoice=='progress' && $um_persen2 != 0){
				$totaluangmuka2 = $this->input->post('down_payment2');
				$totaluangmuka2_idr = $totaluangmuka2*$kurs;
			}
			//INSERT DATABASE TR INVOICE HEADER
			$headerinv = [
				'keterangan'				=> $this->input->post('keterangan'),
				'ppnselect' 		     	=> $ppnselect,
				'progressx' 		     	=> $progressx,
				'persen_retensi2'			=> $persen_retensi2,
				'persen_retensi'			=> $persen_retensi,
				'no_invoice' 		     	=> $no_invoice,
				'tgl_invoice'      		    => $Tgl_Invoice,
				'kode_customer'	 	      	=> $id_customer,
				'nm_customer' 		      	=> $nm_customer,
				'persentase' 		        => $progress,
				'progress_persen' 			=> $this->input->post('persen'),
				'total_product'	         	=> $this->input->post('tot_product'),
				'total_product_idr'	        => $this->input->post('tot_product')*$kurs,
				'total_gab_product'	        => $total_gab_product,
				'total_gab_product_idr'	    => $total_gab_product_idr,
				'total_material'	        => $this->input->post('total_material'),
				'total_material_idr'	    => $this->input->post('total_material')*$kurs,
				'total_bq'	                => $this->input->post('total_bq_nf'),
				'total_bq_idr'	            => $this->input->post('total_bq_nf')*$kurs,
				'total_enginering'	        => $this->input->post('total_enginering'),
				'total_enginering_idr'	    => $this->input->post('total_enginering')*$kurs,
				'total_packing'	            => $this->input->post('total_packing'),
				'total_packing_idr'	        => $this->input->post('total_packing')*$kurs,
				'total_trucking'	        => $this->input->post('total_trucking'),
				'total_trucking_idr'	    => $this->input->post('total_trucking')*$kurs,
				'total_dpp_usd'	            => $this->input->post('grand_total'),
				'total_dpp_rp'	            => $this->input->post('grand_total')*$kurs,
				'total_diskon'	            => $diskon,
				'total_diskon_idr'	        => $diskon * $kurs,
				'total_retensi'	            => $this->input->post('potongan_retensi'),
				'total_retensi_idr'	        => $this->input->post('potongan_retensi')*$kurs,
				'total_ppn'	                => $this->input->post('ppn'),
				'total_ppn_idr'	            => $this->input->post('ppn')*$kurs,
				'total_invoice'	            => $this->input->post('total_invoice'),
				'total_invoice_idr'	        => $this->input->post('total_invoice')*$kurs,
				'total_um'	                => $this->input->post('down_payment'),
				'total_um_idr'	            => $this->input->post('down_payment')*$kurs,
				'kurs_jual'	                => $kurs,
				'no_po'	                    => $this->input->post('nomor_po'),
				'no_faktur'	                => $this->input->post('nomor_faktur'),
				'no_pajak'	                => $this->input->post('nomor_pajak'),
				'payment_term'	            => $this->input->post('top'),
				'updated_by' 	            => $data_session['ORI_User']['username'],
				'updated_date' 	            => date('Y-m-d H:i:s'),
				'total_um2'	                => $totaluangmuka2,
				'total_um_idr2'	            => $totaluangmuka2_idr,
				'id_top'	            	=> $id,
				'base_cur'					=> $base_cur,
				'total_retensi2'			=> $retensi_ppn,
				'total_retensi2_idr'		=> $retensi_ppn*$kurs,
				'sisa_invoice'	        	=> $this->input->post('total_invoice'),
				'sisa_invoice_idr'	        => $this->input->post('total_invoice')*$kurs,
				'so_number'					=> $so_number
			];

			if($jenis_invoice=='progress'){
				$detailInv1 = [];
				if(!empty($_POST['data1'])){
					foreach($_POST['data1'] as $val => $d1){
						$nm_material	= $d1['material_name1'];
						$product_cust	= $d1['product_cust'];
						$product_desc	= $d1['product_desc'];
						$diameter_1	= $d1['diameter_1'];
						$diameter_2	= $d1['diameter_2'];
						$liner		= $d1['liner'];
						$pressure	= $d1['pressure'];
						$id_milik	= $d1['id_milik'];
						$spesifikasi	= $d1['spesifikasi'];
						$harga_sat	= $d1['harga_sat'];
						$qty=0;
						$checked='';
						if(isset($d1['qty'])){
							$qty	= $d1['qty'];
							$checked='1';
						}
						$unit1		= $d1['unit1'];
						$harga_tot	= $d1['harga_tot'];
						$no_ippdtl	= $d1['no_ipp'];
						$no_sodtl	= $d1['no_so'];
						$qty_ori	= $d1['qty_ori'];
						$qty_belum	= $d1['qty_belum'];

						$detailInv1[$val]['id_penagihan']		= $id;
						$detailInv1[$val]['id_bq'] 		     	= $no_bq;
						$detailInv1[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv1[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv1[$val]['spesifikasi']	    = $spesifikasi;
						$detailInv1[$val]['unit']	            = $unit1;
						$detailInv1[$val]['harga_satuan']	    = $harga_sat;
						$detailInv1[$val]['harga_satuan_idr']	= $harga_sat*$kurs;
						$detailInv1[$val]['qty']	            = $qty;
						$detailInv1[$val]['harga_total']	    = $harga_tot;
						$detailInv1[$val]['harga_total_idr']	= $harga_tot*$kurs;
						$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
						$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv1[$val]['qty_total']			= $qty_ori;
						$detailInv1[$val]['qty_sisa']			= $qty_belum;
						$detailInv1[$val]['checked']			= $checked;
						$detailInv1[$val]['id_milik']	    	= $d1['id_milik'];
						$detailInv1[$val]['cogs']	    		= $d1['cogs'];
					}
				}

				$detailInv2 = [];
				if(!empty($_POST['data2'])){
					foreach($_POST['data2'] as $val => $d2){
						$material_name2	= $d2['material_name2'];
						$material_desc2	= $d2['material_desc2'];
						$harga_sat2    	= $d2['harga_sat2'];
						$qty2=0;$checked='';
						if(isset($d2['qty2'])){
							$qty2	= $d2['qty2'];
							if($qty2>0) $checked='1';
						}
						$unit2		= $d2['unit2'];
						$harga_tot2	= $d2['harga_tot2'];
						$no_ippdtl	= $d2['no_ipp'];
						$no_sodtl	= $d2['no_so'];
						$qty2_ori	= $d2['qty2_ori'];
						$qty2_belum	= $d2['qty2_belum'];

						$detailInv2[$val]['id_penagihan']		= $id;
						$detailInv2[$val]['id_bq'] 		     	= $no_bq;
						$detailInv2[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv2[$val]['so_number'] 		    = $no_sodtl;
						$detailInv2[$val]['no_invoice'] 		= $no_invoice;
						$detailInv2[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv2[$val]['id_customer']	 	= $id_customer;
						$detailInv2[$val]['nm_customer'] 		= $nm_customer;
						$detailInv2[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv2[$val]['nm_material']	    = $material_name2." ".$material_desc2;
						$detailInv2[$val]['unit']	            = $unit2;
						$detailInv2[$val]['harga_satuan']	    = $harga_sat2;
						$detailInv2[$val]['harga_satuan_idr']	= $harga_sat2*$kurs;
						$detailInv2[$val]['qty']	            = $qty2;
						$detailInv2[$val]['harga_total']	    = $harga_tot2;
						$detailInv2[$val]['harga_total_idr']	= $harga_tot2*$kurs;
						$detailInv2[$val]['kategori_detail']	= 'BQ';
						$detailInv2[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv2[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv2[$val]['desc']	    		= $material_desc2;
						$detailInv2[$val]['qty_total']			= $qty2_ori;
						$detailInv2[$val]['qty_sisa']			= $qty2_belum;
						$detailInv2[$val]['checked']			= $checked;
						$detailInv2[$val]['id_milik']	    	= $d2['id_milik'];
					}
				}

				$detailInv3 = [];
				if(!empty($_POST['data3'])){
					foreach($_POST['data3'] as $val => $d3){
						$material_name3	= $d3['material_name3'];
						$harga_sat3		= $d3['harga_sat3'];
						$qty3=0;$checked='';
						if(isset($d3['qty3'])){
							$qty3	= $d3['qty3'];
							if($qty3>0) $checked='1';
						}
						$unit3			= $d3['unit3'];
						$harga_tot3		= $d3['harga_tot3'];
						$no_ippdtl		= $d3['no_ipp'];
						$no_sodtl		= $d3['no_so'];
						$product_cust	= $d3['product_cust'];
						$product_desc	= $d3['product_desc'];
						$qty3_ori		= $d3['qty3_ori'];
						$qty3_belum		= $d3['qty3_belum'];

						$detailInv3[$val]['id_penagihan']		= $id;
						$detailInv3[$val]['id_bq'] 		     	= $no_bq;
						$detailInv3[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv3[$val]['so_number'] 		    = $no_sodtl;
						$detailInv3[$val]['no_invoice'] 		= $no_invoice;
						$detailInv3[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv3[$val]['id_customer']	 	= $id_customer;
						$detailInv3[$val]['nm_customer'] 		= $nm_customer;
						$detailInv3[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv3[$val]['nm_material']	    = $material_name3;
						$detailInv3[$val]['unit']	            = $unit3;
						$detailInv3[$val]['harga_satuan']	    = $harga_sat3;
						$detailInv3[$val]['harga_satuan_idr']	= $harga_sat3*$kurs;
						$detailInv3[$val]['qty']	            = $qty3;
						$detailInv3[$val]['harga_total']	    = $harga_tot3;
						$detailInv3[$val]['harga_total_idr']	= $harga_tot3*$kurs;
						$detailInv3[$val]['kategori_detail']	= 'MATERIAL';
						$detailInv3[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv3[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv3[$val]['product_cust']	    = $product_cust;
						$detailInv3[$val]['desc']	    		= $product_desc;
						$detailInv3[$val]['qty_total']			= $qty3_ori;
						$detailInv3[$val]['qty_sisa']			= $qty3_belum;
						$detailInv3[$val]['checked']			= $checked;
						$detailInv3[$val]['id_milik']	    	= $d3['id_milik'];
					}
				}

				$detailInv4 = [];
				if(!empty($_POST['data4'])){
					foreach($_POST['data4'] as $val => $d4){
						$material_name4	= $d4['material_name4'];
						$harga_sat4		= 0;
						$qty4			= 0;
						$unit4			= $d4['unit4'];
						$harga_tot4		=0;$checked='';
						if(isset($d4['harga_tot4'])){
							$harga_tot4	= $d4['harga_tot4'];
							if($harga_tot4>0) $checked='1';
						}
						$no_ippdtl		= $d4['no_ipp'];
						$no_sodtl		= $d4['no_so'];
						$harga_tot4_ori	= $d4['harga_tot4_ori'];
						$harga_tot4_sisa= $d4['harga_tot4_sisa'];

						$detailInv4[$val]['id_penagihan']		= $id;
						$detailInv4[$val]['id_bq'] 		     	= $no_bq;
						$detailInv4[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv4[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv4[$val]['harga_total']	    = $harga_tot4;
						$detailInv4[$val]['harga_total_idr']	= $harga_tot4*$kurs;
						$detailInv4[$val]['kategori_detail']	= 'ENGINERING';
						$detailInv4[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv4[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv4[$val]['desc']	    		= $material_name4;
						$detailInv4[$val]['harga_total_so']		= $harga_tot4_ori;
						$detailInv4[$val]['harga_sisa_so']		= $harga_tot4_sisa;
						$detailInv4[$val]['checked']			= $checked;
						$detailInv4[$val]['id_milik']	    	= $d4['id_milik'];
					}
				}

				$detailInv5 = [];
				if(!empty($_POST['data5'])){
					foreach($_POST['data5'] as $val => $d5){
						$material_name5          = $d5['material_name5'];
						$unit5                   = $d5['unit5'];
						$harga_tot5=0;$checked='';
						if(isset($d5['harga_tot5'])){
							$harga_tot5   = $d5['harga_tot5'];
							if($harga_tot5>0) $checked='1';
						}
						$no_ippdtl		= $d5['no_ipp'];
						$no_sodtl		= $d5['no_so'];
						$harga_tot5_ori	= $d5['harga_tot5_ori'];
						$harga_tot5_sisa= $d5['harga_tot5_sisa'];

						$detailInv5[$val]['id_penagihan']			= $id;
						$detailInv5[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv5[$val]['no_ipp'] 		     	= $no_ippdtl;
						$detailInv5[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv5[$val]['harga_total']	        = $harga_tot5;
						$detailInv5[$val]['harga_total_idr']	    = $harga_tot5*$kurs;
						$detailInv5[$val]['kategori_detail']	    = 'PACKING';
						$detailInv5[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv5[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv5[$val]['desc']	    			= $material_name5;
						$detailInv5[$val]['harga_total_so']			= $harga_tot5_ori;
						$detailInv5[$val]['harga_sisa_so']			= $harga_tot5_sisa;
						$detailInv5[$val]['checked']				= $checked;
						$detailInv5[$val]['id_milik']	    		= $d5['id_milik'];
					}
				}

				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6	= $d6['material_name6'];
						$harga_sat6		= 0;
						$qty6			= 0;
						$unit6			= $d6['unit6'];
						$harga_tot6		=0;$checked='';
						if(isset($d6['harga_tot6'])){
							$harga_tot6	= $d6['harga_tot6'];
							if($harga_tot6>0) $checked='1';
						}
						$no_ippdtl			= $d6['no_ipp'];
						$no_sodtl			= $d6['no_so'];
						$harga_tot6_ori		= $d6['harga_tot6_ori'];
						$harga_tot6_sisa	= $d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ippdtl;
						$detailInv6[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];


					}
				}
			}

			if($jenis_invoice=='retensi'){
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$no_ipp_dtl		         = $d6['no_ipp'];
						$no_so_dtl		         = $d6['no_so'];
						$harga_sat6       		 = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6       		 = $d6['harga_tot6'];
						$harga_tot6_ori			 = $harga_tot6;//$d6['harga_tot6_ori'];
						$harga_tot6_sisa		 = 0;//$d6['harga_tot6_sisa'];
						if($harga_tot6>0) $checked='1';

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = 'BQ-'.$no_ipp_dtl;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp_dtl;
						$detailInv6[$val]['so_number'] 		     	= $no_so_dtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6*$kurs;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						//$detailInv6[$val]['id_milik']	    		= 0;//$d6['id_milik'];
					}
				}
			}

			$get_bill_so = $this->db->query("select * from billing_so where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr;
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka' && $um_persen2 < 1){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen']=$valx->uang_muka_persen + $um_persen;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka + ($grand_total*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr + ($grand_total*$perseninv*$kurs);
				}
			}

			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka + $total_um;
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice']=$valx->uang_muka_invoice + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice_idr']=$valx->uang_muka_invoice_idr + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['persentase_progress']=$umpersen;
					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi*$perseninv);
					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_idr*$perseninv);
					$ArrBillSO[$nox]['retensi_um']=$valx->retensi + ($retensi_non_ppn*$perseninv);
					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_idr + ($retensi_non_ppn*$kurs*$perseninv);
				}
			}
			$ArrUM = [
				'proses_inv' => '1'
			];

			if($jenis_invoice == 'progress'){
				$stsx = 12;
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
				'grand_total' => $grand_total,
				'status'	=> $stsx,
				'real_tagih_usd'	=> $this->input->post('total_invoice') ,
				'real_tagih_idr'	=> $this->input->post('total_invoice') * $kurs
			];
}else{
//	idr
			$total_invoice          = $this->input->post('total_invoice')/$kurs;
			$total_invoice_idr      = $this->input->post('total_invoice');
			$total_um               = $this->input->post('down_payment')/$kurs;
			$total_um_idr           = $this->input->post('down_payment');
			$um_persen				= str_replace(',','',$this->input->post('um_persen'));
			$total_gab_product      = ($this->input->post('tot_product')/$kurs)+($this->input->post('total_material')/$kurs)+($this->input->post('total_bq_nf')/$kurs);
			$total_gab_product_idr  = ($this->input->post('tot_product'))+($this->input->post('total_material'))+($this->input->post('total_bq_nf'));

			$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi'));
			$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi2'));

			$diskon = (!empty($this->input->post('diskon')))?$this->input->post('diskon'):str_replace(',','',$this->input->post('diskon'));

			$retensi_FIX = 0;

			if($retensi_non_ppn <= 0 ){
				$retensi_FIX = $retensi_ppn;
			}

			if($retensi_ppn <= 0 ){
				$retensi_FIX = $retensi_non_ppn;
			}

			$retensi				=  $retensi_FIX/$kurs;
			$retensi_idr		    =  $retensi_FIX;

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
				$totaluangmuka2_idr = $this->input->post('grand_total') - $this->input->post('down_payment');
				$totaluangmuka2 = $totaluangmuka2/$kurs;
			}
			if($jenis_invoice=='progress' && $um_persen2 != 0){
				$totaluangmuka2_idr = $this->input->post('down_payment2');
				$totaluangmuka2 = $totaluangmuka2/$kurs;
			}

			//INSERT DATABASE TR INVOICE HEADER
			$headerinv = [
				'keterangan'				=> $this->input->post('keterangan'),
				'ppnselect' 		     	=> $ppnselect,
				'progressx' 		     	=> $progressx,
				'persen_retensi2'			=> $persen_retensi2,
				'persen_retensi'			=> $persen_retensi,
				'no_invoice' 		     	=> $no_invoice,
				'tgl_invoice'      		    => $Tgl_Invoice,
				'kode_customer'	 	      	=> $id_customer,
				'nm_customer' 		      	=> $nm_customer,
				'persentase' 		        => $progress,
				'progress_persen' 			=> $this->input->post('persen'),
				'total_product'	         	=> $this->input->post('tot_product')/$kurs,
				'total_product_idr'	        => $this->input->post('tot_product'),
				'total_gab_product'	        => $total_gab_product,
				'total_gab_product_idr'	    => $total_gab_product_idr,
				'total_material'	        => $this->input->post('total_material')/$kurs,
				'total_material_idr'	    => $this->input->post('total_material'),
				'total_bq'	                => $this->input->post('total_bq_nf')/$kurs,
				'total_bq_idr'	            => $this->input->post('total_bq_nf'),
				'total_enginering'	        => $this->input->post('total_enginering')/$kurs,
				'total_enginering_idr'	    => $this->input->post('total_enginering'),
				'total_packing'	            => $this->input->post('total_packing')/$kurs,
				'total_packing_idr'	        => $this->input->post('total_packing'),
				'total_trucking'	        => $this->input->post('total_trucking')/$kurs,
				'total_trucking_idr'	    => $this->input->post('total_trucking'),
				'total_dpp_usd'	            => $this->input->post('grand_total')/$kurs,
				'total_dpp_rp'	            => $this->input->post('grand_total'),
				'total_diskon'	            => $diskon/$kurs,
				'total_diskon_idr'	        => $diskon,
				'total_retensi'	            => $this->input->post('potongan_retensi')/$kurs,
				'total_retensi_idr'	        => $this->input->post('potongan_retensi'),
				'total_ppn'	                => $this->input->post('ppn')/$kurs,
				'total_ppn_idr'	            => $this->input->post('ppn'),
				'total_invoice'	            => $this->input->post('total_invoice')/$kurs,
				'total_invoice_idr'	        => $this->input->post('total_invoice'),
				'total_um'	                => $this->input->post('down_payment')/$kurs,
				'total_um_idr'	            => $this->input->post('down_payment'),
				'kurs_jual'	                => $kurs,
				'no_po'	                    => $this->input->post('nomor_po'),
				'no_faktur'	                => $this->input->post('nomor_faktur'),
				'no_pajak'	                => $this->input->post('nomor_pajak'),
				'payment_term'	            => $this->input->post('top'),
				'updated_by' 	            => $data_session['ORI_User']['username'],
				'updated_date' 	            => date('Y-m-d H:i:s'),
				'total_um2'	                => $totaluangmuka2,
				'total_um_idr2'	            => $totaluangmuka2_idr,
				'id_top'	            	=> $id,
				'base_cur'					=> $base_cur,
				'total_retensi2'			=> $retensi_ppn/$kurs,
				'total_retensi2_idr'		=> $retensi_ppn,
				'sisa_invoice'	        	=> $this->input->post('total_invoice')/$kurs,
				'sisa_invoice_idr'	        => $this->input->post('total_invoice'),
				'so_number'					=> $so_number
			];

			if($jenis_invoice=='progress'){
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
						$spesifikasi		  = $d1['spesifikasi'];
						$harga_sat     		  = $d1['harga_sat'];
						$qty=0;$checked='';
						if(isset($d1['qty'])){
							$qty              = $d1['qty'];$checked='1';
						}
						$unit1                = $d1['unit1'];
						$harga_tot     		  = $d1['harga_tot'];
						$no_ippdtl     		  = $d1['no_ipp'];
						$no_sodtl		      = $d1['no_so'];
						$qty_ori			  = $d1['qty_ori'];
						$qty_belum			  = $d1['qty_belum'];

						$detailInv1[$val]['id_penagihan']		= $id;
						$detailInv1[$val]['id_bq'] 		     	= $no_bq;
						$detailInv1[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv1[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv1[$val]['spesifikasi']	    = $spesifikasi;
						$detailInv1[$val]['unit']	            = $unit1;
						$detailInv1[$val]['harga_satuan']	    = $harga_sat/$kurs;
						$detailInv1[$val]['harga_satuan_idr']	= $harga_sat;
						$detailInv1[$val]['qty']	            = $qty;
						$detailInv1[$val]['harga_total']	    = $harga_tot/$kurs;
						$detailInv1[$val]['harga_total_idr']	= $harga_tot;
						$detailInv1[$val]['kategori_detail']	= 'PRODUCT';
						$detailInv1[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv1[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv1[$val]['qty_total']			= $qty_ori;
						$detailInv1[$val]['qty_sisa']			= $qty_belum;
						$detailInv1[$val]['checked']			= $checked;
						$detailInv1[$val]['id_milik']	    	= $d1['id_milik'];
						$detailInv1[$val]['cogs']	    		= $d1['cogs'];

					}
				}

				$detailInv2 = [];
				if(!empty($_POST['data2'])){
					foreach($_POST['data2'] as $val => $d2){
						$material_name2	= $d2['material_name2'];
						$material_desc2	= $d2['material_desc2'];
						$harga_sat2		= $d2['harga_sat2'];
						$qty2=0;$checked='';
						if(isset($d2['qty2'])){
							$qty2		= $d2['qty2'];
							if($qty2>0) $checked='1';
						}
						$unit2			= $d2['unit2'];
						$harga_tot2		= $d2['harga_tot2'];
						$no_ippdtl		= $d2['no_ipp'];
						$no_sodtl		= $d2['no_so'];
						$qty2_ori		= $d2['qty2_ori'];
						$qty2_belum		= $d2['qty2_belum'];

						$detailInv2[$val]['id_penagihan']		= $id;
						$detailInv2[$val]['id_bq'] 		     	= $no_bq;
						$detailInv2[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv2[$val]['so_number'] 		    = $no_sodtl;
						$detailInv2[$val]['no_invoice'] 		= $no_invoice;
						$detailInv2[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv2[$val]['id_customer']	 	= $id_customer;
						$detailInv2[$val]['nm_customer'] 		= $nm_customer;
						$detailInv2[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv2[$val]['nm_material']	    = $material_name2." ".$material_desc2;
						$detailInv2[$val]['unit']	            = $unit2;
						$detailInv2[$val]['harga_satuan']	    = $harga_sat2/$kurs;
						$detailInv2[$val]['harga_satuan_idr']	= $harga_sat2;
						$detailInv2[$val]['qty']	            = $qty2;
						$detailInv2[$val]['harga_total']	    = $harga_tot2/$kurs;
						$detailInv2[$val]['harga_total_idr']	= $harga_tot2;
						$detailInv2[$val]['kategori_detail']	= 'BQ';
						$detailInv2[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv2[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv2[$val]['desc']	    		= $material_desc2;
						$detailInv2[$val]['qty_total']			= $qty2_ori;
						$detailInv2[$val]['qty_sisa']			= $qty2_belum;
						$detailInv2[$val]['checked']			= $checked;
						$detailInv2[$val]['id_milik']	    	= $d2['id_milik'];
					}
				}

				$detailInv3 = [];
				if(!empty($_POST['data3'])){
					foreach($_POST['data3'] as $val => $d3){
						$material_name3	= $d3['material_name3'];
						$harga_sat3		= $d3['harga_sat3'];
						$qty3=0;$checked='';
						if(isset($d3['qty3'])){
							$qty3                = $d3['qty3'];
							if($qty3>0) $checked='1';
						}
						$unit3                   = $d3['unit3'];
						$harga_tot3      		 = $d3['harga_tot3'];
						$no_ippdtl     		  	 = $d3['no_ipp'];
						$no_sodtl		      	 = $d3['no_so'];
						$product_cust         	 = $d3['product_cust'];
						$product_desc         	 = $d3['product_desc'];
						$qty3_ori				 = $d3['qty3_ori'];
						$qty3_belum				 = $d3['qty3_belum'];

						$detailInv3[$val]['id_penagihan']		= $id;
						$detailInv3[$val]['id_bq'] 		     	= $no_bq;
						$detailInv3[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv3[$val]['so_number'] 		    = $no_sodtl;
						$detailInv3[$val]['no_invoice'] 		= $no_invoice;
						$detailInv3[$val]['tgl_invoice']      	= $Tgl_Invoice;
						$detailInv3[$val]['id_customer']	 	= $id_customer;
						$detailInv3[$val]['nm_customer'] 		= $nm_customer;
						$detailInv3[$val]['jenis_invoice'] 		= $jenis_invoice;
						$detailInv3[$val]['nm_material']	    = $material_name3;
						$detailInv3[$val]['product_cust']	    = $product_cust;
						$detailInv3[$val]['desc']	    		= $product_desc;
						$detailInv3[$val]['unit']	            = $unit3;
						$detailInv3[$val]['harga_satuan']	    = $harga_sat3/$kurs;
						$detailInv3[$val]['harga_satuan_idr']	= $harga_sat3;
						$detailInv3[$val]['qty']	            = $qty3;
						$detailInv3[$val]['harga_total']	    = $harga_tot3/$kurs;
						$detailInv3[$val]['harga_total_idr']	= $harga_tot3;
						$detailInv3[$val]['kategori_detail']	= 'MATERIAL';
						$detailInv3[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv3[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv3[$val]['qty_total']			= $qty3_ori;
						$detailInv3[$val]['qty_sisa']			= $qty3_belum;
						$detailInv3[$val]['checked']			= $checked;
						$detailInv3[$val]['id_milik']	    	= $d3['id_milik'];

					}
				}

				$detailInv4 = [];
				if(!empty($_POST['data4'])){
					foreach($_POST['data4'] as $val => $d4){
						$material_name4          = $d4['material_name4'];
						$harga_sat4       		 = 0;
						$qty4                    = 0;
						$unit4                   = $d4['unit4'];
						$harga_tot4=0;$checked='';
						if(isset($d4['harga_tot4'])){
							$harga_tot4       	 = $d4['harga_tot4'];
							if($harga_tot4>0) $checked='1';
						}
						$no_ippdtl     		  	 = $d4['no_ipp'];
						$no_sodtl		      	 = $d4['no_so'];
						$harga_tot4_ori			 = $d4['harga_tot4_ori'];
						$harga_tot4_sisa		 = $d4['harga_tot4_sisa'];

						$detailInv4[$val]['id_penagihan']		= $id;
						$detailInv4[$val]['id_bq'] 		     	= $no_bq;
						$detailInv4[$val]['no_ipp'] 		    = $no_ippdtl;
						$detailInv4[$val]['so_number'] 		    = $no_sodtl;
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
						$detailInv4[$val]['harga_total']	    = $harga_tot4/$kurs;
						$detailInv4[$val]['harga_total_idr']	= $harga_tot4;
						$detailInv4[$val]['kategori_detail']	= 'ENGINERING';
						$detailInv4[$val]['created_by'] 	    = $data_session['ORI_User']['username'];
						$detailInv4[$val]['created_date'] 	    = date('Y-m-d H:i:s');
						$detailInv4[$val]['desc']	    		= $material_name4;
						$detailInv4[$val]['harga_total_so']		= $harga_tot4_ori;
						$detailInv4[$val]['harga_sisa_so']		= $harga_tot4_sisa;
						$detailInv4[$val]['checked']			= $checked;
						$detailInv4[$val]['id_milik']	    	= $d4['id_milik'];

					}
				}

				$detailInv5 = [];
				if(!empty($_POST['data5'])){
					foreach($_POST['data5'] as $val => $d5){
						$material_name5          = $d5['material_name5'];
						$unit5                   = $d5['unit5'];
						$harga_tot5=0;$checked='';
						if(isset($d5['harga_tot5'])){
							$harga_tot5   = $d5['harga_tot5'];
							if($harga_tot5>0) $checked='1';
						}
						$no_ippdtl     		  	 = $d5['no_ipp'];
						$no_sodtl		      	 = $d5['no_so'];
						$harga_tot5_ori			 = $d5['harga_tot5_ori'];
						$harga_tot5_sisa	     = $d5['harga_tot5_sisa'];

						$detailInv5[$val]['id_penagihan']			= $id;
						$detailInv5[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv5[$val]['no_ipp'] 		     	= $no_ippdtl;
						$detailInv5[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv5[$val]['harga_total']	        = $harga_tot5/$kurs;
						$detailInv5[$val]['harga_total_idr']	    = $harga_tot5;
						$detailInv5[$val]['kategori_detail']	    = 'PACKING';
						$detailInv5[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv5[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv5[$val]['desc']	    			= $material_name5;
						$detailInv5[$val]['harga_total_so']			= $harga_tot5_ori;
						$detailInv5[$val]['harga_sisa_so']			= $harga_tot5_sisa;
						$detailInv5[$val]['checked']			 	= $checked;
						$detailInv5[$val]['id_milik']	    		= $d5['id_milik'];
					}
				}

				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6          = $d6['material_name6'];
						$harga_sat6       		 = 0;
						$qty6                    = 0;
						$unit6                   = $d6['unit6'];
						$harga_tot6=0;$checked='';
						if(isset($d6['harga_tot6'])){
							$harga_tot6   		= $d6['harga_tot6'];
							if($harga_tot6>0) $checked='1';
						}
						$no_ippdtl				= $d6['no_ipp'];
						$no_sodtl				= $d6['no_so'];
						$harga_tot6_ori			= $d6['harga_tot6_ori'];
						$harga_tot6_sisa		= $d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv6[$val]['no_ipp']		     	    = $no_ippdtl;
						$detailInv6[$val]['so_number'] 		     	= $no_sodtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6/$kurs;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						$detailInv6[$val]['id_milik']	    		= $d6['id_milik'];
					}

				}

					$detailInv9 = [];
				if(!empty($_POST['data9'])){
					foreach($_POST['data9'] as $val => $d9){
						$material_name9          = $d9['material_name9'];
						$harga_sat9       		 = 0;
						$qty9                    = 0;
						$unit9                   = $d9['unit9'];
						$harga_tot9=0;$checked='';
						if(isset($d9['harga_tot9'])){
							$harga_tot9   		= $d9['harga_tot9'];
							if($harga_tot9>0) $checked='1';
						}
						$no_ippdtl				= $d9['no_ipp'];
						$no_sodtl				= $d9['no_so'];
						$harga_tot9_ori			= $d9['harga_tot9_ori'];
						$harga_tot9_sisa		= $d9['harga_tot9_sisa'];

						$detailInv9[$val]['id_penagihan']			= $id;
						$detailInv9[$val]['id_bq'] 		     	    = $no_bq;
						$detailInv9[$val]['no_ipp']		     	    = $no_ippdtl;
						$detailInv9[$val]['so_number'] 		     	= $no_sodtl;
						$detailInv9[$val]['no_invoice'] 		    = $no_invoice;
						$detailInv9[$val]['tgl_invoice']      		= $Tgl_Invoice;
						$detailInv9[$val]['id_customer']	 	    = $id_customer;
						$detailInv9[$val]['nm_customer'] 		    = $nm_customer;
						$detailInv9[$val]['jenis_invoice'] 		    = $jenis_invoice;
						$detailInv9[$val]['nm_material']	        = $material_name9;
						$detailInv9[$val]['unit']	                = $unit9;
						$detailInv9[$val]['harga_satuan']	        = 0;
						$detailInv9[$val]['harga_satuan_idr']	    = 0;
						$detailInv9[$val]['qty']	                = 0;
						$detailInv9[$val]['harga_total']	        = $harga_tot9/$kurs;
						$detailInv9[$val]['harga_total_idr']	    = $harga_tot9;
						$detailInv9[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv9[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv9[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv9[$val]['desc']	    			= $material_name9;
						$detailInv9[$val]['harga_total_so']			= $harga_tot9_ori;
						$detailInv9[$val]['harga_sisa_so']			= $harga_tot9_sisa;
						$detailInv9[$val]['checked']				= $checked;
						$detailInv9[$val]['id_milik']	    		= $d9['id_milik'];
					}
				}
				
			}
		

			if($jenis_invoice=='retensi'){
				$detailInv6 = [];
				if(!empty($_POST['data6'])){
					foreach($_POST['data6'] as $val => $d6){
						$material_name6		= $d6['material_name6'];
						$no_ipp_dtl			= $d6['no_ipp'];
						$no_so_dtl			= $d6['no_so'];
						$harga_sat6       	= 0;
						$qty6				= 0;$checked='1';
						$unit6				= $d6['unit6'];
						$harga_tot6       	= $d6['harga_tot6'];
						$harga_tot6_ori		= $harga_tot6;//$d6['harga_tot6_ori'];
						$harga_tot6_sisa	= 0;//$d6['harga_tot6_sisa'];

						$detailInv6[$val]['id_penagihan']			= $id;
						$detailInv6[$val]['id_bq'] 		     	    = 'BQ-'.$no_ipp_dtl;
						$detailInv6[$val]['no_ipp']		     	    = $no_ipp_dtl;
						$detailInv6[$val]['so_number'] 		     	= $no_so_dtl;
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
						$detailInv6[$val]['harga_total']	        = $harga_tot6/$kurs;
						$detailInv6[$val]['harga_total_idr']	    = $harga_tot6;
						$detailInv6[$val]['kategori_detail']	    = 'TRUCKING';
						$detailInv6[$val]['created_by'] 	        = $data_session['ORI_User']['username'];
						$detailInv6[$val]['created_date'] 	        = date('Y-m-d H:i:s');
						$detailInv6[$val]['desc']	    			= $material_name6;
						$detailInv6[$val]['harga_total_so']			= $harga_tot6_ori;
						$detailInv6[$val]['harga_sisa_so']			= $harga_tot6_sisa;
						$detailInv6[$val]['checked']				= $checked;
						$detailInv6[$val]['id_milik']	    		= 0;//$d6['id_milik'];
					}
				}
			}

			$get_bill_so = $this->db->query("select * from billing_so where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr;
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka' && $um_persen2 < 1){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka_persen']=$valx->uang_muka_persen + $um_persen;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka + ($grand_total*$perseninv/$kurs);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr + ($grand_total*$perseninv);
				}
			}
			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
					$perseninv=($valx->total_deal_usd/$totalinvoice);
					$ArrBillSO[$nox]['id']=$valx->id;
					$ArrBillSO[$nox]['uang_muka']=$valx->uang_muka_idr - ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_idr']=$valx->uang_muka_idr - ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice']=$valx->uang_muka_invoice + ($total_um*$perseninv);
					$ArrBillSO[$nox]['uang_muka_invoice_idr']=$valx->uang_muka_invoice_idr + ($total_um_idr*$perseninv);
					$ArrBillSO[$nox]['persentase_progress']=$umpersen;
					$ArrBillSO[$nox]['retensi']=$valx->retensi + ($retensi_non_ppn*$perseninv);
					$ArrBillSO[$nox]['retensi_idr']=$valx->retensi_idr + ($retensi_non_ppn*$perseninv);
					$ArrBillSO[$nox]['retensi_um']=$valx->retensi + ($retensi_ppn/$kurs*$perseninv);
					$ArrBillSO[$nox]['retensi_um_idr']=$valx->retensi_idr + ($retensi_ppn*$perseninv);
				}
			}

			$ArrUM = [
				'proses_inv' => '1'
			];

			if($jenis_invoice == 'progress'){
				$stsx = 12;
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
				'grand_total' => $grand_total,
				'status'	=> $stsx,
				'real_tagih_usd'	=> $this->input->post('total_invoice') /$kurs,
				'real_tagih_idr'	=> $this->input->post('total_invoice')
			];
}
			$this->db->trans_start();
				$this->db->where('id', $id);
				$this->db->update('penagihan', $headerinv);
				$this->db->query("delete from penagihan_detail where id_penagihan='".$id."'");
				if(!empty($detailInv1)){
					$this->db->insert_batch('penagihan_detail',$detailInv1);
				}
				if(!empty($detailInv2)){
					$this->db->insert_batch('penagihan_detail',$detailInv2);
				}
				if(!empty($detailInv3)){
					$this->db->insert_batch('penagihan_detail',$detailInv3);
				}
				if(!empty($detailInv4)){
					$this->db->insert_batch('penagihan_detail',$detailInv4);
				}
				if(!empty($detailInv5)){
					$this->db->insert_batch('penagihan_detail',$detailInv5);
				}
				if(!empty($detailInv6)){
					$this->db->insert_batch('penagihan_detail',$detailInv6);
				}
				if(!empty($detailInv9)){
					$this->db->insert_batch('penagihan_detail',$detailInv9);
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
				history('Update Penagihan '.$jenis_invoice.' '.$id);
			}
			echo json_encode($Arr_Return);
		} 
		else {
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
			$approval	= $this->uri->segment(4);
			// echo $nomor_id;exit;
			$getBq 		= $this->db->select('no_ipp as no_po, base_cur')->where_in('id',$nomor_id)->get('billing_so')->result_array();

			$in_ipp = [];
			$in_bq = [];

			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= get_nomor_so($valx['no_po']);
				$base_cur		= $valx['base_cur'];
			}
			if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}
			$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
			$noipp=implode("','",$in_ipp);
			$id_produksi=implode("','PRO-",$in_ipp);
			$id_bq=implode("','BQ-",$in_ipp);
			$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
			if(!empty($penagihan_detail)){
				$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
				from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
				$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
				$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
				$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
				$non_frp	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
				$material	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
				$get_kurs	= $this->db->select(' (kurs_jual) AS kurs,  (progress_persen) AS uang_muka_persen,  0 AS uang_muka_persen2')->where('id',$id)->get('penagihan')->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
			}else{
// old
				/*
				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,d.qty_delivery,d.cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik join (
				SELECT id_product, count(qty) as qty_delivery, sum(finish_good) as cogs FROM production_detail
				where id_produksi in ('PRO-".$id_produksi."') group by id_product
				) d on b.id_product=d.id_product
				where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";
				*/
				
				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,0 qty_delivery,0 cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik
				where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";

//				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,0 qty_delivery,0 cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";
                
				$getDetail	= $this->db->query($sqlDetail)->result_array();
//				$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
				$getEngCost	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
				$getPackCost= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
				$getTruck	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
				$non_frp	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
				$material	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
                $other   	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='other')")->where_in('no_ipp',$in_ipp)->get()->result_array();
			
//				$get_kurs	= $this->db->select(' (kurs_usd_dipakai) AS kurs,  (uang_muka_persen) AS uang_muka_persen,  (uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();

				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
//				$uang_muka_persen = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
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
				'other'		    => $other,
				'material'		=> $material,
				'list_top'		=> $list_top,
				'base_cur'		=> $base_cur,
				'in_ipp'		=> implode(',',$in_ipp),
				'in_bq'			=> implode(',',$in_bq),
				'in_so'			=> implode(',',$in_so),
				'arr_in_ipp'	=> $in_ipp,
				'penagihan'		=> $penagihan,
				'kurs'			=> $get_kurs[0]->kurs,
				'uang_muka_persen'	=> $uang_muka_persen,
				'uang_muka_persen2'	=> 0,
				'down_payment'	=> $down_payment,
				'down_payment2'	=> 0,
				'id'			=> $id,
				'approval'		=> $approval
			);
			$this->load->view('Penagihan/create_progress_new_instalasi',$data2);
		}
	}
	public function create_retensi_new_instalasi(){
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
		$getBq 		= $this->db->where_in('id',$nomor_id)->get('billing_so')->result_array();

		$in_ipp = [];
		$in_bq = [];
		$base_cur='USD';

		foreach($getBq AS $val => $valx){
			$in_ipp[$val] 	= $valx['no_ipp'];
			$in_bq[$val] 	= 'BQ-'.$valx['no_ipp'];
			$in_so[$val] 	= get_nomor_so($valx['no_ipp']);
			$base_cur		= $valx['base_cur'];
		}

		$jenis  	= 'retensi';

		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
		$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();

		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();

		$get_kurs	= $this->db->select('MAX(kurs_usd_dipakai) AS kurs, SUM(uang_muka_persen) AS uang_muka_persen, SUM(uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();

		$penagihan_detail 	= $this->db->select('*,harga_total as retensi, harga_total_idr as retensi_idr')->order_by('id','asc')->where_in('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
		if(!empty($penagihan_detail)){
			$getBq	= $penagihan_detail;
		}

		$approval	= $this->uri->segment(4);
		$data = array(
			'title'			=> 'Indeks Of Create Invoice Instalasi Retensi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'get_retensi'	=> $getBq,
			'getHeader'		=> $getHeader,
			'getDetail' 	=> $getDetail,
			'list_top'		=> $list_top,
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'uang_muka_persen'	=> $get_kurs[0]->uang_muka_persen,
			'uang_muka_persen2'	=> $get_kurs[0]->uang_muka_persen2,
			'approval'		=> $approval,
			'id'			=> $id
		);
		$this->load->view('Penagihan/create_retensi_new_instalasi',$data);
	}
	public function create_invoice_new(){
		$db2 			= $this->load->database('accounting', TRUE);
		$data_session 	= $this->session->userdata;
		$id   			= $this->uri->segment(3);
		$nomordoc 		= get_name('penagihan','no_invoice','id',$id);

		$gethd 			= $this->db->query("SELECT * FROM penagihan WHERE id='$id'")->row();
		$tgl       		= $gethd->tgl_invoice;
		$Jml_Ttl   		= $gethd->total_invoice_idr;
		$Id_klien     	= $gethd->kode_customer;
		$Nama_klien   	= $gethd->nm_customer;
		$jenis_invoice  = $gethd->type;
		$Bln 			= substr($tgl,5,2);
		$Thn 			= substr($tgl,0,4);
		$tot_retensi    = $gethd->total_retensi_idr;
		$tot_um         = $gethd->total_um_idr;
		$isppn			= $gethd->total_ppn_idr;
		$total_retensi2_idr	= $gethd->total_retensi2_idr;
		$base_cur		= $gethd->base_cur;
		$no_po			= $gethd->no_po;

		$this->db->trans_begin();
		$db2->trans_begin();

		$dt_no_ipp 	= explode(",",$gethd->no_ipp);
		if($jenis_invoice=='progress'){
			if($base_cur=='IDR'||$base_cur==''){				
				$kodejurnal1		= 'JV061';
				if($gethd->instalasi=='1') {
					$kodejurnal1		= 'JV068';
				}
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set
				total_invoice_idr=(total_invoice_idr+".$gethd->total_dpp_rp."), 
				total_retensi_idr=(total_retensi_idr+".$gethd->total_retensi_idr."), 
				total_retensi2_idr=(total_retensi2_idr+".$gethd->total_retensi2_idr."), 
				sisa_um_idr=(sisa_um_idr-".$gethd->total_um_idr.") WHERE nomor_po='$no_po'");
			}else{
				if($gethd->instalasi=='1') {
					$kodejurnal1		= 'JV069';
				}else{
					if($gethd->type_lc=='non lc') {
						$kodejurnal1		= 'JV064';
					}else{
						$kodejurnal1		= 'JV070';
					}
				}
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set
				total_invoice=(total_invoice+".$gethd->total_dpp_usd."), 
				total_invoice_idr=(total_invoice_idr+".$gethd->total_dpp_rp."), 
				total_retensi=(total_retensi+".$gethd->total_retensi."), 
				total_retensi_idr=(total_retensi_idr+".$gethd->total_retensi_idr."), 
				total_retensi2=(total_retensi2+".$gethd->total_retensi2."), 
				total_retensi2_idr=(total_retensi2_idr+".$gethd->total_retensi2_idr."), 
				sisa_um=(sisa_um+".$gethd->total_um."), 
				sisa_um_idr=(sisa_um_idr+".$gethd->total_um_idr.") WHERE nomor_po='$no_po'");
			}
			$Keterangan_INV1	= 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				$get_billing_so = $this->db->query("SELECT * FROM billing_so_gabung WHERE no_ipp='$keys'")->row();
				$getdtlinv = $this->db->query("SELECT sum(harga_total_idr) as total_dpp_rp FROM penagihan_detail WHERE id_penagihan='".$gethd->id."' and no_ipp='$keys' and checked=1")->row();
				$total_dpp_rp=0;
				$persentase=0;
				if(!empty($getdtlinv)){
					$total_dpp_rp=$getdtlinv->total_dpp_rp;
					if($total_dpp_rp=="") $total_dpp_rp=0;
//					$persentase=round(($total_dpp_rp/$get_billing_so->total_deal_idr*100),2);
//					$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$persentase."), total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					$this->db->query("update billing_so set total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					$this->db->query("update ".DBTANKI.".ipp_header set total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");

				}
			}
		}
		elseif($jenis_invoice=='uang muka'){
			if($base_cur=='IDR'){
				$kodejurnal1		= 'JV050';
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set 
				persen_um = (persen_um+".$gethd->persentase."),
				total_um_idr=(total_um_idr+".$gethd->total_dpp_rp."), 
				sisa_um_idr=(sisa_um_idr+".$gethd->total_dpp_rp.")
				WHERE nomor_po='$no_po'");
			}else{
				$kodejurnal1		= 'JV065';
				// update kartu po customer uang muka
				$this->db->query("update tr_kartu_po_customer set 
				persen_um = (persen_um+".$gethd->persentase."),
				total_um=(total_um+".$gethd->total_dpp_usd."), 
				total_um_idr=(total_um_idr+".$gethd->total_dpp_rp."), 
				sisa_um=(sisa_um+".$gethd->total_dpp_usd."), 
				sisa_um_idr=(sisa_um_idr+".$gethd->total_dpp_rp.")
				WHERE nomor_po='$no_po'");
				$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
			}
			$Keterangan_INV1	= 'UANG MUKA PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				//$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$gethd->persentase."), total_invoice=(total_invoice+".$gethd->total_dpp_rp.") WHERE no_ipp='$keys'");
				$this->db->query("update billing_so set total_invoice=(total_invoice+".$gethd->total_dpp_rp.") WHERE no_ipp='$keys'");
				$this->db->query("update ".DBTANKI.".ipp_header set total_invoice=(total_invoice+".$gethd->total_dpp_rp.") WHERE no_ipp='$keys'");
			}
		}
		elseif($jenis_invoice=='retensi'){
			if($isppn>0){
				if($base_cur=='IDR'||$base_cur==''){
					$kodejurnal1	= 'JV052';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set 
					total_retensi2_idr=(total_retensi2_idr-".$gethd->total_dpp_rp.") 
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}else{
					// tidak jadi dipakai
					$kodejurnal1	= 'JV066';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set  
					total_retensi2=(total_retensi2-".$gethd->total_dpp_usd."), 
					total_retensi2_idr=(total_retensi2_idr-".$gethd->total_dpp_rp.")
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}				
			}else{
				if($base_cur=='IDR'||$base_cur==''){
					$kodejurnal1	= 'JV054';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set 
					total_retensi_idr=(total_retensi_idr-".$gethd->total_dpp_rp.") 
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}else{
					$kodejurnal1	= 'JV067';
					// update kartu po customer uang muka
					$this->db->query("update tr_kartu_po_customer set 
					total_retensi=(total_retensi-".$gethd->total_dpp_usd.") 
					total_retensi_idr=(total_retensi_idr-".$gethd->total_dpp_rp.") 
					WHERE nomor_po='$no_po'");
					$this->db->query("update tr_kartu_po_customer set kurs_um=ROUND((total_um_idr/total_um),0) WHERE nomor_po='$no_po'");
				}
			}
			$Keterangan_INV1	= 'RETENSI PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				$get_billing_so = $this->db->query("SELECT * FROM billing_so_gabung WHERE no_ipp='$keys'")->row();
				$getdtlinv = $this->db->query("SELECT sum(harga_total_idr) as total_dpp_rp FROM penagihan_detail WHERE id_penagihan='".$gethd->id."' and no_ipp='$keys' and checked='1'")->row();
				$total_dpp_rp=0;
				$persentase=0;
				if(!empty($getdtlinv)){
					$total_dpp_rp=$getdtlinv->total_dpp_rp;
					if($total_dpp_rp=="") $total_dpp_rp=0;
					//$persentase=round(($total_dpp_rp/$get_billing_so->total_deal_idr*100),2);
					//$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$persentase."), total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					$this->db->query("update billing_so set total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					$this->db->query("update ".DBTANKI.".ipp_header set total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
				}
			}
		}

		//insert invoice
		$this->db->query("insert into tr_invoice_header (`id_penagihan`, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `id_customer`, `nm_customer`, `jenis_invoice`, `kurs_jual`, `persentase`, `total_product`, `total_product_idr`, `total_gab_product`, `total_gab_product_idr`, `total_material`, `total_material_idr`, `total_bq`, `total_bq_idr`, `total_enginering`, `total_enginering_idr`, `total_packing`, `total_packing_idr`, `total_trucking`, `total_trucking_idr`, `total_dpp_usd`, `total_dpp_rp`, `total_diskon`,  `total_diskon_idr`, `total_retensi`, `total_retensi_idr`, `total_ppn`, `total_ppn_idr`, `total_invoice`, `total_invoice_idr`,  `total_um`, `total_um_idr`, `total_bayar`, `total_bayar_idr`,  `created_by`, `created_date`, `no_po`, `no_faktur`, `no_pajak`, `payment_term`, `proses_print`, `approved`, `approved_by`, `approved_date`, `total_um2`, `total_um_idr2`, `id_top`, `base_cur`, `total_retensi2`, `total_retensi2_idr`, `printed_on`, `sisa_invoice_idr`, `sisa_invoice`, sisa_invoice_retensi2, sisa_invoice_retensi2_idr)
		select $id, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `kode_customer`, `nm_customer`, `type`, `kurs_jual`, `persentase`, `total_product`, `total_product_idr`, `total_gab_product`, `total_gab_product_idr`, `total_material`, `total_material_idr`, `total_bq`, `total_bq_idr`, `total_enginering`, `total_enginering_idr`, `total_packing`,  `total_packing_idr`, `total_trucking`, `total_trucking_idr`, `total_dpp_usd`, `total_dpp_rp`, `total_diskon`, `total_diskon_idr`, `total_retensi`, `total_retensi_idr`, `total_ppn`, `total_ppn_idr`, `total_invoice`, `total_invoice_idr`, `total_um`, `total_um_idr`, `total_bayar`, `total_bayar_idr`, `created_by`, `created_date`, `no_po`, `no_faktur`, `no_pajak`, `payment_term`, '1', 'Y', '".$data_session['ORI_User']['username']."', now(), `total_um2`, `total_um_idr2`, `id_top`, `base_cur`,  `total_retensi2`, `total_retensi2_idr`, '1', `sisa_invoice_idr`, `sisa_invoice`, `total_retensi2`, `total_retensi2_idr` from penagihan WHERE id='$id'");
		//insert invoice detail
		$this->db->query("INSERT INTO `tr_invoice_detail` (`id_penagihan`, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `id_customer`, `nm_customer`, `jenis_invoice`, `nm_material`, `product_cust`, `desc`, `dim_1`, `dim_2`, `liner`, `pressure`, `spesifikasi`, `unit`, `harga_satuan`, `harga_satuan_idr`, `qty`, `harga_total`, `harga_total_idr`, `kategori_detail`, `created_by`, `created_date`, `id_billing_dtl`) select `id_penagihan`, `id_bq`, `no_ipp`, `so_number`, `no_invoice`, `tgl_invoice`, `id_customer`, `nm_customer`, `jenis_invoice`, `nm_material`, `product_cust`, `desc`, `dim_1`, `dim_2`, `liner`, `pressure`, `spesifikasi`, `unit`, `harga_satuan`, `harga_satuan_idr`, `qty`, `harga_total`, `harga_total_idr`, `kategori_detail`, '".$data_session['ORI_User']['username']."', now(), `id_billing_dtl` from penagihan_detail where checked='1' and id_penagihan=$id");

//		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_Sales('101',$tgl);
		$Nomor_JV				= get_generate_jurnal('GJ',date('y-m-d'));
		$Keterangan_INV		    = 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY
		$datajurnal1  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal1);
		$nilaitotaljurnal=0;
		foreach($datajurnal1 AS $rec){
			$tabel1  		= $rec->menu;
			$posisi1 		= $rec->posisi;
			$field1  		= $rec->field;
			$param1  		= 'no_invoice';
			$value_param1  	= $nomordoc;
			$val1 			= $this->Acc_model->GetData($tabel1,$field1,$param1,$value_param1);
			$nilaibayar1 	= $val1[0]->$field1;
			$nokir1  = $rec->no_perkiraan;
			if ($posisi1=='D'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $nilaibayar1,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				$nilaitotaljurnal=($nilaitotaljurnal+$nilaibayar1);
			}
			elseif ($posisi1=='K'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $nilaibayar1
					//'jenis_jurnal'  => 'invoicing'
				);
			}
		}
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tgl,
			'jml'	            => $nilaitotaljurnal,
			'koreksi_no'		=> '-',
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan' 		=> $Keterangan_INV1,
			'bulan'				=> $Bln,
			'tahun'				=> $Thn,
			'user_id'			=> $data_session['ORI_User']['username'],
			'memo'			    => $nomordoc,
			'tgl_jvkoreksi'	    => $tgl,
			'ho_valid'			=> ''
		);

		if($jenis_invoice=='uang muka'){
			if($base_cur=='IDR'){
				$coa_uangmuka='2102-01-01';
				$coa_piutang='1102-01-01';
			}else{
				$coa_uangmuka='2102-01-03';
				$coa_piutang='1102-01-02';
			}
			//uang muka
			$datapiutang = array(
				'tipe'       	=> 'JV',
				'nomor'       	=> $Nomor_JV,
				'tanggal'       => $tgl,
				'no_perkiraan'  => $coa_uangmuka,
				'keterangan'    => 'UM PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc,
				'no_reff'       => $no_po,
				'debet'         => 0,
				'kredit'        => $gethd->total_dpp_rp,
				'debet_usd'		=> 0,
				'kredit_usd'    => $gethd->total_dpp_usd,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);
			// piutang
			$datapiutang = array(
				'tipe'       	=> 'JV',
				'nomor'       	=> $Nomor_JV,
				'tanggal'       => $tgl,
				'no_perkiraan'  => $coa_piutang,
				'keterangan'    => $Keterangan_INV1,
				'no_reff'       => $no_po,
				'debet'         => $gethd->total_invoice_idr,
				'kredit'        => 0,
				'debet_usd'		=> $gethd->total_invoice,
				'kredit_usd'	=> 0,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);

		}

		if($jenis_invoice=='progress'){
			if($base_cur=='IDR'){
				$coa_uangmuka='2102-01-01';
				$coa_piutang='1102-01-01';
				$coa_uninvoicing='1102-01-03';
			}else{
				$coa_uangmuka='2102-01-03';
				$coa_piutang='1102-01-02';
				$coa_uninvoicing='1102-01-04';
			}
			//uang muka
			if($gethd->total_um_idr<>0){
				$datapiutang = array(
					'tipe'       	=> 'JV',
					'nomor'       	=> $Nomor_JV,
					'tanggal'       => $tgl,
					'no_perkiraan'  => $coa_uninvoicing,
					'keterangan'    => 'POTONG UM PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc,
					'no_reff'       => $no_po,
					'debet'         => $gethd->total_um_idr,
					'kredit'        => 0,
					'debet_usd'		=> $gethd->total_um,
					'kredit_usd'    => 0,
					'id_supplier'   => $Id_klien,
					'nama_supplier' => $Nama_klien,
				);
				$this->db->insert('tr_kartu_piutang',$datapiutang);
			}
			//uninvoicing
			if($gethd->total_retensi2_idr<>0){
				$datapiutang = array(
					'tipe'       	=> 'JV',
					'nomor'       	=> $Nomor_JV,
					'tanggal'       => $tgl,
					'no_perkiraan'  => $coa_piutang,
					'keterangan'    => 'UN INVOICING PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc,
					'no_reff'       => $no_po,
					'debet'         => $gethd->total_retensi2_idr,
					'kredit'        => 0,
					'debet_usd'		=> $gethd->total_retensi2,
					'kredit_usd'    => 0,
					'id_supplier'   => $Id_klien,
					'nama_supplier' => $Nama_klien,
				);
				$this->db->insert('tr_kartu_piutang',$datapiutang);
			}
			//piutang
			$datapiutang = array(
				'tipe'       	=> 'JV',
				'nomor'       	=> $Nomor_JV,
				'tanggal'       => $tgl,
				'no_perkiraan'  => $coa_piutang,
				'keterangan'    => $Keterangan_INV1,
				'no_reff'       => $no_po,
				'debet'         => $gethd->total_invoice_idr,
				'kredit'        => 0,
				'debet_usd'		=> $gethd->total_invoice,
				'kredit_usd'    => 0,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);
		}

		if($jenis_invoice=='retensi'){
			if($isppn>0){
				if($base_cur=='IDR'){
					$coa_piutang='1102-01-01';
				}else{
					$coa_piutang='1102-01-02';
				}
				//piutang
				$datapiutang = array(
					'tipe'       	=> 'JV',
					'nomor'       	=> $Nomor_JV,
					'tanggal'       => $tgl,
					'no_perkiraan'  => $coa_piutang,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $no_po,
					'debet'         => $gethd->total_invoice_idr,
					'kredit'        => 0,
					'debet_usd'		=> $gethd->total_invoice,
					'kredit_usd'    => 0,
					'id_supplier'   => $Id_klien,
					'nama_supplier' => $Nama_klien,
				);
				$this->db->insert('tr_kartu_piutang',$datapiutang);
			}else{
				if($base_cur=='IDR'){
				}else{
				}
			}
		}
			
			$Qry_Update_Cabang_acc	 = "UPDATE pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
			$db2->query($Qry_Update_Cabang_acc);

			$nomor_id 	= explode(",",$gethd->no_so);
			$getBq 		= $this->db->select('no_ipp as no_po')->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();

			$in_ipp = [];
//			$in_bq = [];

			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
//				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
//				$in_so[$val] 	= get_nomor_so($valx['no_po']);
			}
			$get_bill_so = $this->db->query("select * from billing_so_gabung where no_ipp in ('".implode("','",$in_ipp)."')")->result();
			$totalinvoice=0;
			$totalinvoice_idr=0;
			foreach($get_bill_so AS $valx){
				$totalinvoice+=$valx->total_deal_usd;
				$totalinvoice_idr+=$valx->total_deal_idr; 
			}
			$ArrBillSO = array();
			$nox = 0;
			if($jenis_invoice=='uang muka'){
				foreach($get_bill_so AS $valx){$nox++;
				 if($valx->jenis=='pipa'){
					if($valx->total_deal_usd < 1 || $totalinvoice < 1){
						$perseninv=0;
					}else{
						$perseninv=($valx->total_deal_usd/$totalinvoice);
					}
					
					$this->db->query("update billing_so set
					uang_muka_persen=(uang_muka_persen+".$gethd->persentase."),
					uang_muka=(uang_muka+".($gethd->total_dpp_usd*$perseninv)."),
					uang_muka_idr=(uang_muka_idr+".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");
				 }else{
					$this->db->query("update ".DBTANKI.".ipp_header set
					uang_muka_persen=(uang_muka_persen+".$gethd->persentase."),
					uang_muka=(uang_muka+".($gethd->total_dpp_usd*$perseninv)."),
					uang_muka_idr=(uang_muka_idr+".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE no_ipp='".$valx->id."'");
				 }
				}
			}
			if($jenis_invoice=='retensi'){
				foreach($get_bill_so AS $valx){$nox++;
				  $perseninv=($valx->total_deal_usd/$totalinvoice);
				  if($gethd->ppnselect==0){
					$this->db->query("update billing_so set
					retensi=(retensi-".($gethd->total_dpp_usd*$perseninv)."),
					retensi_idr=(retensi_idr-".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");

					$this->db->query("update ".DBTANKI.".ipp_header set
					retensi=(retensi-".($gethd->total_dpp_usd*$perseninv)."),
					retensi_idr=(retensi_idr-".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE no_ipp='".$valx->id."'");
				  }else{
					$this->db->query("update billing_so set
					retensi_um=(retensi_um-".($gethd->total_dpp_usd*$perseninv)."),
					retensi_um_idr=(retensi_um_idr-".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");

					$this->db->query("update ".DBTANKI.".ipp_header set
					retensi_um=(retensi_um-".($gethd->total_dpp_usd*$perseninv)."),
					retensi_um_idr=(retensi_um_idr-".($gethd->total_dpp_rp*$perseninv)."),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE no_ipp='".$valx->id."'");
				  }
				}
			}
			if($jenis_invoice=='progress'){
				foreach($get_bill_so AS $valx){$nox++;
				 if($valx->jenis=='pipa'){

					if($base_cur=='USD'){

						if($valx->total_deal_usd < 1 || $totalinvoice < 1){
							$perseninv=0;
						}else{
							$perseninv=($valx->total_deal_usd/$totalinvoice);
						}

					} elseif($base_cur=='IDR'||$base_cur==''){

						if($valx->total_deal_idr < 1 || $totalinvoice < 1){
							$perseninv=0;
						}else{
							$perseninv=($valx->total_deal_idr/$totalinvoice);
						}

					}

					

					$this->db->query("update billing_so set
					uang_muka_invoice=(uang_muka_invoice+".($gethd->total_um*$perseninv)."),
					uang_muka_invoice_idr=(uang_muka_invoice_idr+".$gethd->total_um_idr*$perseninv."),
					persentase_progress='".$gethd->persentase*$perseninv."',
					retensi=(retensi+'".$gethd->total_retensi*$perseninv."'),
					retensi_idr=(retensi_idr+'".$gethd->total_retensi_idr*$perseninv."'),
					retensi_um=(retensi_um+'".$gethd->total_retensi2*$perseninv."'),
					retensi_um_idr=(retensi_um_idr+'".$gethd->total_retensi2_idr*$perseninv."'),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE id='".$valx->id."'");
				 }else{

					if($base_cur=='USD'){

						if($valx->total_deal_usd < 1 || $totalinvoice < 1){
							$perseninv=0;
						}else{
							$perseninv=($valx->total_deal_usd/$totalinvoice);
						}

					} elseif($base_cur=='IDR'||$base_cur==''){

						if($valx->total_deal_idr < 1 || $totalinvoice < 1){
							$perseninv=0;
						}else{
							$perseninv=($valx->total_deal_idr/$totalinvoice);
						}

					}

					$this->db->query("update ".DBTANKI.".ipp_header set
					uang_muka_invoice=(uang_muka_invoice+".($gethd->total_um*$perseninv)."),
					uang_muka_invoice_idr=(uang_muka_invoice_idr+".$gethd->total_um_idr*$perseninv."),
					persentase_progress='".$gethd->persentase*$perseninv."',
					retensi=(retensi+'".$gethd->total_retensi*$perseninv."'),
					retensi_idr=(retensi_idr+'".$gethd->total_retensi_idr*$perseninv."'),
					retensi_um=(retensi_um+'".$gethd->total_retensi2*$perseninv."'),
					retensi_um_idr=(retensi_um_idr+'".$gethd->total_retensi2_idr*$perseninv."'),
					status_total=(status_total+'".$gethd->total_invoice*$perseninv."')
					WHERE no_ipp='".$valx->id."'");
				 }
				}

				$getdetail	= $this->db->query("SELECT * FROM penagihan_detail WHERE id_penagihan='$id' and checked='1'")->result();
				$nox = 0;
				if(!empty($getdetail)){
				  foreach($getdetail as $valx){
					 $nox++;
					 if($valx->kategori_detail=='PRODUCT'){
						 $this->db->query("update billing_so_product set qty_inv=(qty_inv+".$valx->qty.") WHERE id_milik='".$valx->id_milik."' and no_ipp='".$valx->no_ipp."'");

						 $this->db->query("update ".DBTANKI.".billing_product set qty_inv=(qty_inv+".$valx->qty.") WHERE id_milik='".$valx->id_milik."' and no_ipp='".$valx->no_ipp."' ");
					 }else{
						$this->db->query("update billing_so_add set qty_inv=(qty_inv+".$valx->qty."), total_deal_inv=(total_deal_inv+".$valx->harga_total."), total_deal_inv_idr=(total_deal_inv_idr+".$valx->harga_total_idr.") WHERE id_milik='".$valx->id_milik."' and no_ipp='".$valx->no_ipp."'");

						$this->db->query("update ".DBTANKI.".billing_product set total_deal_inv=(total_deal_inv+".$valx->harga_total."), total_deal_inv_idr=(total_deal_inv_idr+".$valx->harga_total_idr.") WHERE id_milik='".$valx->id_milik."' and no_ipp='".$valx->no_ipp."'");
					 }
				  }
				}
				$this->db->query("update delivery_product set st_cogs='1' where kode_delivery in ('".str_ireplace(",","','",$gethd->delivery_no)."')");
			}
			$this->db->query("update penagihan set status='12' WHERE id='$id'");
			if(!empty($ArrBillSO)){
			//	$this->db->update_batch('billing_so', $ArrBillSO, 'id');
			}


		if($this->db->trans_status() === FALSE or $db2->trans_status()=== FALSE){
		 $this->db->trans_rollback();
		 $db2->trans_rollback();
		 $Arr_Return		= array(
				'status'		=> 2,
				'pesan'			=> 'Error Process Failed. Please Try Again...'
		   );
		}else{
			$db2->insert('javh',$dataJVhead);
			$db2->insert_batch('jurnal',$det_Jurnaltes1);

			$this->db->trans_commit();
			$db2->trans_commit();
		    $Arr_Return		= array(
			 'status'		=> 1,
			 'pesan'		=> 'Update Process Success. Thank You & Have A Nice Day...'
			 );
		}
		echo json_encode($Arr_Return);

	}
	
	public function create_progress_new_approval(){
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
			$getBq 		= $this->db->select('no_ipp as no_po, base_cur, no_so')->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();

			$in_ipp = [];
			$in_bq = [];
			$base_cur='';
			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= ($valx['no_so']==''?get_nomor_so($valx['no_po']):$valx['no_so']);
				$base_cur		= ($base_cur==''?$valx['base_cur']:$base_cur);
			}

			$jenis  	= 'progress';
			if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}

			$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
			$noipp=implode("','",$in_ipp);
			$id_produksi=implode("','PRO-",$in_ipp);
			$id_bq=implode("','BQ-",$in_ipp);
			$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
			if(!empty($penagihan_detail)){
				$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
				from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
				$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
				$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
				$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
				$non_frp	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
				$material	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();

				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs,sisa_um AS sisa_um,sisa_um_idr AS sisa_um_idr from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
				$sisa_um   = (!empty($get_kurs))?$get_kurs[0]->sisa_um:0;
				$uang_muka_persen = (!empty($get_kurs))?$get_kurs[0]->uang_muka_persen:0; 
				$sisa_um_idr   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				$down_payment   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
				$getTankiproduct=array();
				$getTankipacking=array();
				$getTankishipping=array();
			}else{


				$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
//				$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
				$getDetail  = $this->db->query("select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,0 qty_delivery,0 as cogs from billing_so_product a where no_ipp in ('".$noipp."')")->result_array();
				$getEngCost	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
				$getPackCost= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
				$getTruck	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
				$non_frp	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
				$material	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();

				$getTankiproduct	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','product')->get(DBTANKI.'.billing_product')->result_array();
				$getTankipacking	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs,0 total_delivery ')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','packing')->get(DBTANKI.'.billing_product')->result_array();
				$getTankishipping	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs,0 total_delivery ')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','shipping')->get(DBTANKI.'.billing_product')->result_array();

				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
//				$get_kurs	= $this->db->select(' (kurs_usd_dipakai) AS kurs,  (uang_muka_persen) AS uang_muka_persen,  (uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs,sisa_um AS sisa_um,sisa_um_idr AS sisa_um_idr from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
				$sisa_um   = (!empty($get_kurs))?$get_kurs[0]->sisa_um:0;
				$uang_muka_persen = (!empty($get_kurs))?$get_kurs[0]->uang_muka_persen:0; 
				$sisa_um_idr   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				$down_payment   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				
				
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
			}

			// print_r($get_kurs[0]->uang_muka_persen);
			// exit;
			$approval	= $this->uri->segment(4);
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
				'getTankiproduct'	=> $getTankiproduct,
				'getTankipacking'	=> $getTankipacking,
				'getTankishipping'	=> $getTankishipping,
				'non_frp'		=> $non_frp,
				'material'		=> $material,
				'list_top'		=> $list_top,
				'base_cur'		=> $base_cur,
				'in_ipp'		=> implode(',',$in_ipp),
				'in_bq'			=> implode(',',$in_bq),
				'in_so'			=> implode(',',$in_so),
				'arr_in_ipp'	=> $in_ipp,
				'penagihan'		=> $penagihan,
				'kurs'			=> isset($get_kurs[0]->kurs),
				'uang_muka_persen'	=> $uang_muka_persen,
				'uang_muka_persen2'	=> 0,
				'down_payment'	=> $down_payment,
				'sisa_um'	    => $sisa_um,
				'sisa_um_idr'	    => $sisa_um_idr,
				'down_payment2'	=> $down_payment2,
				'id'			=> $id,
				'approval'		=> $approval
			);
			$this->load->view('Penagihan/create_progress_new',$data2);
		
		
	}
	
	public function add_new_invoice(){
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
			$getBq 		= $this->db->select('no_ipp as no_po, base_cur, no_so')->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();

			$in_ipp = [];
			$in_bq = [];
			$base_cur='';
			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= ($valx['no_so']==''?get_nomor_so($valx['no_po']):$valx['no_so']);
				$base_cur		= ($base_cur==''?$valx['base_cur']:$base_cur);
			}

			$jenis  	= 'progress';
			if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}

			$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
			$noipp=implode("','",$in_ipp);
			$id_produksi=implode("','PRO-",$in_ipp);
			$id_bq=implode("','BQ-",$in_ipp);
			$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
			if(!empty($penagihan_detail)){
				$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
				from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
				$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
				$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
				$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
				$non_frp	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
				$material	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();

				/*$get_kurs	= $this->db->select(' (kurs_jual) AS kurs,  (progress_persen) AS uang_muka_persen,  0 AS uang_muka_persen2')->where('id',$id)->get('penagihan')->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}*/
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				/*if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}*/

				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs,sisa_um AS sisa_um,sisa_um_idr AS sisa_um_idr from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
				$sisa_um   = (!empty($get_kurs))?$get_kurs[0]->sisa_um:0;
				$uang_muka_persen = (!empty($get_kurs))?$get_kurs[0]->uang_muka_persen:0; 
				$sisa_um_idr   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				$down_payment   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				
				$getTankiproduct=array();
				$getTankipacking=array();
				$getTankishipping=array();
			}else{


				$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
//				$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
				$getDetail  = $this->db->query("select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,0 qty_delivery,0 as cogs from billing_so_product a where no_ipp in ('".$noipp."')")->result_array();
				$getEngCost	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
				$getPackCost= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
				$getTruck	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
				$non_frp	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya'OR category='other')")->where_in('no_ipp',$in_ipp)->get()->result_array();
				$material	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
				
				$getTankiproduct	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','product')->get(DBTANKI.'.billing_product')->result_array();
				$getTankipacking	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs,0 total_delivery ')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','packing')->get(DBTANKI.'.billing_product')->result_array();
				$getTankishipping	= $this->db->select('*,item_no customer_item,po_desc desc,product_name product,deal_usd total_deal_usd, deal_idr total_deal_idr,qty as qty_total,(qty-qty_inv) qty_inv,0 qty_delivery,0 as cogs,0 total_delivery ')->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->where('category','shipping')->get(DBTANKI.'.billing_product')->result_array();

				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
//				$get_kurs	= $this->db->select(' (kurs_usd_dipakai) AS kurs,  (uang_muka_persen) AS uang_muka_persen,  (uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
				/*$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();

				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}*/
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				/*if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}*/

				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs,sisa_um AS sisa_um,sisa_um_idr AS sisa_um_idr from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
					$sisa_um   = (!empty($get_kurs))?$get_kurs[0]->sisa_um:0;
				$uang_muka_persen = (!empty($get_kurs))?$get_kurs[0]->uang_muka_persen:0; 
				$sisa_um_idr   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				$down_payment   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
			}
			// print_r($uang_muka_persen);
			// exit;

			
			$approval	= $this->uri->segment(4);
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
				'getTankiproduct'	=> $getTankiproduct,
				'getTankipacking'	=> $getTankipacking,
				'getTankishipping'	=> $getTankishipping,
				'non_frp'		=> $non_frp,
				'material'		=> $material,
				'list_top'		=> $list_top,
				'base_cur'		=> $base_cur,
				'in_ipp'		=> implode(',',$in_ipp),
				'in_bq'			=> implode(',',$in_bq),
				'in_so'			=> implode(',',$in_so), 
				'arr_in_ipp'	=> $in_ipp,
				'penagihan'		=> $penagihan,
				'kurs'			=> (!empty($get_kurs))?$get_kurs[0]->kurs:0,
				'uang_muka_persen'	=> $uang_muka_persen,//(!empty($get_kurs))?$get_kurs[0]->uang_muka_persen:0,
				'uang_muka_persen2'	=> 0,
				'down_payment'	=> $down_payment,
				'down_payment2'	=> 0,
				'sisa_um'	    => $sisa_um,
				'sisa_um_idr'	    => $sisa_um_idr,
				'id'			=> $id,
				'approval'		=> $approval
			);
			$this->load->view('Penagihan/create_progress_new',$data2);
		
		
	}
	
	
	public function add_new_invoice_delivery(){
		
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
		$approval	= $this->uri->segment(4);
		$base_cur   = $penagihan[0]->base_cur;
		// print_r($penagihan);exit;
		$getBq 		= $this->db->select('no_ipp as no_po, base_cur')->where_in('id',$nomor_id)->get('billing_so_gabung')->result_array();
		
		// print_r($getBq);
		// exit;
		

		$in_ipp = [];
		$in_bq = [];

		foreach($getBq AS $val => $valx){
			$in_ipp[$val] 	= $valx['no_po'];
			$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
			$in_so[$val] 	= get_nomor_so_po($valx['no_po']);
			//$base_cur		= $valx['base_cur'];
		}
		
		if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}
		$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
		$noipp=implode("','",$in_ipp);
		$id_produksi=implode("','PRO-",$in_ipp);
		$id_bq=implode("','BQ-",$in_ipp);
		$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
		$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
		if(!empty($penagihan_detail)){
			$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
			from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
			$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
			$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
			$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
			$getOther  	= $this->db->select('*, harga_satuan as harga_satuan_usd, unit as satuan, qty as qty_delivery,qty_sisa as qty_inv')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','OTHER')->get('penagihan_detail')->result_array();
			$non_frp	= $this->db->select('*, unit as satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
			$material	= $this->db->select('*, unit as satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
			$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
			$getDetailcut	='';
			//$get_kurs	= $this->db->select(' (kurs_jual) AS kurs,  (progress_persen) AS uang_muka_persen,  0 AS uang_muka_persen2')->where('id',$id)->get('penagihan')->result();
			$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs,sisa_um AS sisa_um,sisa_um_idr AS sisa_um_idr from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
			    $sisa_um   = (!empty($get_kurs))?$get_kurs[0]->sisa_um:0;
				$uang_muka_persen = (!empty($get_kurs))?$get_kurs[0]->uang_muka_persen:0; 
				$sisa_um_idr   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				$down_payment   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;

			$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
			//$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
			if($base_cur=='USD'){
				$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
			}else{
				$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
			}
			$uang_muka_persen2 = 0;
			$down_payment2 = 0;
			if(count($get_tagih) > 1){
				$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				if($base_cur=='USD'){
					$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
			}
		}else{
			$this->db->query("delete from penagihan_product_temp where id_penagihan='".$id."'");
			$ada_data_bf='';
//loose
			$sql="select count(a.id_uniq) as qty, sum(a.nilai_cogs) as cogs, b.id_milik, b.id_product, b.id_produksi from delivery_product_detail a join production_detail b on a.id_uniq=b.id where a.kode_delivery in ('".$kode_delivery."') and a.sts='loose' group by b.id_milik, b.id_product, b.id_produksi ";
			$delivery_loose	= $this->db->query($sql)->result_array();
			if(!empty($delivery_loose)){
				foreach ($delivery_loose as $keys=>$vals){
					$this->db->query("insert into penagihan_product_temp (id_penagihan,id_milik,no_ipp,qty,sts_do,cogs,id_product) VALUES ('".$id."','".$vals['id_milik']."','".str_ireplace("PRO-","",$vals['id_produksi'])."','".$vals['qty']."','loose','".$vals['cogs']."','".$vals['id_product']."') ");
				}
				$ada_data_bf='so_detail_header';
			}
			
//loose_dead_modif	
			$sql="select count(a.id_uniq) as qty, sum(a.nilai_cogs) as cogs, b.id_milik, b.id_product, b.id_produksi from delivery_product_detail a join production_detail b on a.id_milik=b.id_deadstok_dipakai  where a.kode_delivery in ('".$kode_delivery."') and a.sts='loose_dead_modif' group by b.id_milik, b.id_product, b.id_produksi ";
			$delivery_loose	= $this->db->query($sql)->result_array();
			if(!empty($delivery_loose)){
				foreach ($delivery_loose as $keys=>$vals){
					$this->db->query("insert into penagihan_product_temp (id_penagihan,id_milik,no_ipp,qty,sts_do,cogs,id_product) VALUES ('".$id."','".$vals['id_milik']."','".str_ireplace("PRO-","",$vals['id_produksi'])."','".$vals['qty']."','loose_dead_modif','".$vals['cogs']."','".$vals['id_product']."') ");
				}
				
			}
			
//loose_dead	
			$sql="select count(a.id_uniq) as qty, sum(a.nilai_cogs) as cogs, b.id_milik, b.id_product, b.id_produksi from delivery_product_detail a join production_detail b on a.id_milik=b.id_deadstok_dipakai where a.kode_delivery in ('".$kode_delivery."') and a.sts='loose_dead' group by b.id_milik, b.id_product, b.id_produksi ";
			$delivery_loose	= $this->db->query($sql)->result_array();
			if(!empty($delivery_loose)){
				foreach ($delivery_loose as $keys=>$vals){
					$this->db->query("insert into penagihan_product_temp (id_penagihan,id_milik,no_ipp,qty,sts_do,cogs,id_product) VALUES ('".$id."','".$vals['id_milik']."','".str_ireplace("PRO-","",$vals['id_produksi'])."','".$vals['qty']."','loose_dead','".$vals['cogs']."','".$vals['id_product']."') ");
				}
				
			}
		
			// $sql="select count(a.id_uniq) as qty, sum(a.nilai_cogs) as cogs, a.id_milik, a.product as id_product, a.id_produksi from delivery_product_detail a where a.kode_delivery in ('".$kode_delivery."') and a.sts='loose_dead_modif' ";
			// $delivery_loose	= $this->db->query($sql)->result_array();
							
			// if(!empty($delivery_loose)){
				// foreach ($delivery_loose as $keys=>$vals){
					// $this->db->query("insert into penagihan_product_temp (id_penagihan,id_milik,no_ipp,qty,sts_do,cogs,id_product) VALUES ('".$id."','".$vals['id_milik']."','".$vals['id_produksi']."','".$vals['qty']."','loose_dead_modif','".$vals['cogs']."','".$vals['id_product']."') ");
				// }
				
			// }
// field join
			$sql="select sum(a.berat) as qty, sum(a.nilai_cogs) as cogs, a.id_milik, b.id_product, b.id_produksi from delivery_product_detail a join (select id_milik,id_product,id_produksi from production_detail group by id_milik,id_product,id_produksi) b on a.id_milik=b.id_milik where a.kode_delivery in ('".$kode_delivery."') and a.sts_product='field joint' group by a.id_milik, b.id_product, b.id_produksi ";
			$delivery_loose	= $this->db->query($sql)->result_array();			
			if(!empty($delivery_loose)){
				foreach ($delivery_loose as $keys=>$vals){
					$this->db->query("insert into penagihan_product_temp (id_penagihan,id_milik,no_ipp,qty,sts_do,cogs,id_product) VALUES ('".$id."','".$vals['id_milik']."','".str_ireplace("PRO-","",$vals['id_produksi'])."','".$vals['qty']."','field join','".$vals['cogs']."','".$vals['id_product']."') ");
				}
			}
// cutting
			$sql="select SUM(round(c.length_split/c.length,2)) as qty, sum(a.nilai_cogs) as cogs, a.id_milik, b.id_product, b.id_produksi from delivery_product_detail a join so_cutting_detail c on a.id_uniq=c.id and a.kode_delivery=c.kode_delivery join (select id_milik,kode_delivery,id_product,id_produksi,qty as qty_total from production_detail group by id_milik,id_product,id_produksi,qty) b on a.id_milik=b.id_milik where a.kode_delivery in ('".$kode_delivery."') and b.kode_delivery in ('".$kode_delivery."') and a.sts='cut' group by a.id_milik, b.id_product, b.id_produksi ";
			$delivery_loose	= $this->db->query($sql)->result_array();
			if(!empty($delivery_loose)){
				foreach ($delivery_loose as $keys=>$vals){
					$this->db->query("insert into penagihan_product_temp (id_penagihan,id_milik,no_ipp,qty,sts_do,cogs,id_product) VALUES ('".$id."','".$vals['id_milik']."','".str_ireplace("PRO-","",$vals['id_produksi'])."','".$vals['qty']."','cut','".$vals['cogs']."','".$vals['id_product']."') ");
				}
			}

// cutting tidak diproduksi
			$sql="select round(c.length_split/c.length,2) as qty, a.nilai_cogs as cogs, a.id_milik, a.product from delivery_product_detail a join so_cutting_detail c on a.id_uniq=c.id and a.kode_delivery=c.kode_delivery where a.kode_delivery in ('".$kode_delivery."') and c.kode_delivery in ('".$kode_delivery."') and a.sts='cut' and ISNULL(a.id_produksi)";
			$delivery_loose	= $this->db->query($sql)->result_array();
			if(!empty($delivery_loose)){
				foreach ($delivery_loose as $keys=>$vals){
					$this->db->query("insert into penagihan_product_temp (id_penagihan,id_milik,no_ipp,qty,sts_do,cogs,id_product) VALUES ('".$id."','".$vals['id_milik']."','-','".$vals['qty']."','cut non produksi','".$vals['cogs']."','".$vals['product']."') ");
				}
			}

			$getDetail	= $this->db->query("select a.*, a.qty as qty_total, (a.qty-a.qty_inv) as qty_inv, c.qty as qty_delivery, c.cogs, c.sts_do from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik join ( select sum(x.qty) as qty, sum(x.cogs) as cogs, x.no_ipp,x.id_product, CONCAT('BQ-',x.no_ipp) as id_bq, x.id_penagihan, y.id_milik, x.sts_do	from penagihan_product_temp x join so_detail_header y on x.id_milik=y.id WHERE
			x.id_penagihan='".$id."' group by x.no_ipp,x.id_product,y.id_milik) c on b.id=c.id_milik and b.id_bq=c.id_bq and a.no_ipp=c.no_ipp")->result_array();
			
			$getDetailcut	= $this->db->query("select a.* FROM penagihan_product_temp a WHERE a.sts_do='cut non produksi' AND a.id_penagihan='".$id."'")->result_array();
			
			$getidmilik   = $this->db->query("SELECT sum(x.qty) AS qty,sum(x.cogs) AS cogs, x.no_ipp, x.id_product, CONCAT('BQ-', x.no_ipp) AS id_bq, x.id_penagihan, y.id_milik,  x.sts_do  FROM  penagihan_product_temp x JOIN so_detail_header y ON x.id_milik = y.id WHERE x.id_penagihan='".$id."' AND y.id_milik IS NULL  GROUP BY x.no_ipp, x.id_product, y.id_milik")->result_array();
			


			if($ada_data_bf!=''){
				if(empty($getDetail)) { 
					$getDetail	= $this->db->query("
					select a.*, a.qty as qty_total, (a.qty-a.qty_inv) as qty_inv, c.qty as qty_delivery, c.cogs, c.sts_do from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik join 
					( select sum(x.qty) as qty, sum(x.cogs) as cogs, x.no_ipp,x.id_product, CONCAT('BQ-',x.no_ipp) as id_bq, x.id_penagihan, y.id_milik, x.sts_do from penagihan_product_temp x join so_detail_header y on x.id_milik=y.id where x.id_penagihan='".$id."' group by x.no_ipp,x.id_product,y.id_milik) c on b.id=c.id_milik and b.id_bq=c.id_bq and a.no_ipp=c.no_ipp and b.id=c.id_milik")->result_array();
				}
			}
			
			// print_r($getDetail);
			// exit;
			
//				$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
			$getEngCost	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
			$getPackCost= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
			$getTruck	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
			$getAcc  	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','lainnya')->get('billing_so_add')->result_array();
			$getOther  	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','other')->get('billing_so_add')->result_array();
			
			//$non_frp	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
			//$non_frp    = $this->db->order_by('a.id', 'asc')->group_by('a.product')->select('SUM(a.berat) AS qty_product, a.*, "aksesoris" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts' => 'aksesoris'))->result_array();
			$non_frp      = $this->db->query("SELECT sum( a.berat ) AS qty_delivery, a.*,'aksesoris' AS type_product,b.no_ipp,b.satuan,b.total_deal_idr,b.id_material,b.qty as qty_total,(b.qty-b.qty_inv) as qty_inv,b.total_deal_usd FROM delivery_product_detail a JOIN billing_so_add b on a.product=b.id_material AND a.id_produksi = b.no_ipp WHERE ( a.berat > 0 OR a.berat IS NOT NULL )  AND a.kode_delivery IN ('$kode_delivery')  AND a.sts = 'aksesoris'  GROUP BY a.product  ORDER BY a.id ASC")->result_array();
			
			$material	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,(qty-qty_inv) qty_delivery ')->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
			$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();

//				$get_kurs	= $this->db->select(' (kurs_usd_dipakai) AS kurs,  (uang_muka_persen) AS uang_muka_persen,  (uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();
			$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs,sisa_um AS sisa_um, sisa_um_idr AS sisa_um_idr from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
			$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
//				$uang_muka_persen = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				$sisa_um   = (!empty($get_kurs))?$get_kurs[0]->sisa_um:0;
				$uang_muka_persen = (!empty($get_kurs))?$get_kurs[0]->uang_muka_persen:0; 
				$sisa_um_idr   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
				$down_payment   = (!empty($get_kurs))?$get_kurs[0]->sisa_um_idr:0;
			if($base_cur=='USD'){
				$down_payment = (!empty($get_tagih))?$get_tagih[0]->total_invoice:0;
			}else{
				$down_payment = (!empty($get_tagih))?$get_tagih[0]->total_invoice:0;
			}
			$uang_muka_persen2 = 0;
			$down_payment2 = 0;
			if(count($get_tagih) > 1){
				$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				if($base_cur=='USD'){
					$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->total_invoice:0;
				}else{
					$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->total_invoice:0;
				}
			}
		}
		
		if(!empty($getidmilik)){
			$getidmilik2 = $getidmilik;
		} else {
			$getidmilik2 = 0;
		}
		
		$data2 = array(
			'title'			=> 'Indeks Of Create Invoice Progress',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'getHeader'		=> $getHeader,
			'getDetail' 	=> $getDetail,
			'getDetailcut' 	=> $getDetailcut,
			'getEngCost' 	=> $getEngCost,
			'getPackCost' 	=> $getPackCost,
			'getTruck' 		=> $getTruck,
			'other' 		=> $getOther,
			'getNonid'   	=> $getidmilik2, 
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'list_top'		=> $list_top,
			'base_cur'		=> $base_cur,
			'in_ipp'		=> implode(',',$in_ipp),
			'in_bq'			=> implode(',',$in_bq),
			'in_so'			=> implode(',',$in_so),
			'arr_in_ipp'	=> $in_ipp,
			'penagihan'		=> $penagihan,
			'kurs'			=> $get_kurs[0]->kurs,
			'uang_muka_persen'	=> $uang_muka_persen, 
			'uang_muka_persen2'	=> 0,
			'down_payment'	=> $down_payment,
			'sisa_um'	    => $sisa_um,
			'sisa_um_idr'	    => $sisa_um_idr, 
			'down_payment2'	=> $down_payment2,
			'id'			=> $id,
			'approval'		=> $approval
		);
		$this->load->view('Penagihan/add_new_invoice_delivery',$data2);
	
}

	
	public function add_new_invoice_instalasi(){ 
		
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
			$approval	= $this->uri->segment(4);
			// echo $nomor_id;exit;
			$getBq 		= $this->db->select('no_ipp as no_po, base_cur')->where_in('id',$nomor_id)->get('billing_so')->result_array();

			$in_ipp = [];
			$in_bq = [];

			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= get_nomor_so($valx['no_po']);
				$base_cur		= $valx['base_cur'];
			}
			if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}
			$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
			$noipp=implode("','",$in_ipp);
			$id_produksi=implode("','PRO-",$in_ipp);
			$id_bq=implode("','BQ-",$in_ipp);
			$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
			if(!empty($penagihan_detail)){
				$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
				from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
				$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
				$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
				$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
				$non_frp	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
				$other  	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='OTHER')")->where('id_penagihan',$id)->get()->result_array();
				
				$material	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
				
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
				$get_kurs	= $this->db->select(' (kurs_jual) AS kurs,  (progress_persen) AS uang_muka_persen,  0 AS uang_muka_persen2')->where('id',$id)->get('penagihan')->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
			}else{
// old
				/*
				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,d.qty_delivery,d.cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik join (
				SELECT id_product, count(qty) as qty_delivery, sum(finish_good) as cogs FROM production_detail
				where id_produksi in ('PRO-".$id_produksi."') group by id_product
				) d on b.id_product=d.id_product
				where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";
				*/
				
				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,(a.qty-a.qty_inv) qty_delivery,0 cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik
				where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";

//				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,(a.qty-a.qty_inv) qty_delivery,0 cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";
                
				$getDetail	= $this->db->query($sqlDetail)->result_array();
//				$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
				$getEngCost	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
				$getPackCost= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
				$getTruck	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
				$non_frp	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
				$material	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
                $other   	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='other')")->where_in('no_ipp',$in_ipp)->get()->result_array();
			
//				$get_kurs	= $this->db->select(' (kurs_usd_dipakai) AS kurs,  (uang_muka_persen) AS uang_muka_persen,  (uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();

				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
//				$uang_muka_persen = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
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
				'other'		    => $other,
				'material'		=> $material,
				'list_top'		=> $list_top,
				'base_cur'		=> $base_cur,
				'in_ipp'		=> implode(',',$in_ipp),
				'in_bq'			=> implode(',',$in_bq),
				'in_so'			=> implode(',',$in_so),
				'arr_in_ipp'	=> $in_ipp,
				'penagihan'		=> $penagihan,
				'kurs'			=> $get_kurs[0]->kurs,
				'uang_muka_persen'	=> $uang_muka_persen, 
				'uang_muka_persen2'	=> 0,
				'down_payment'	=> $down_payment,
				'down_payment2'	=> 0,
				'id'			=> $id,
				'approval'		=> $approval
			);
			$this->load->view('Penagihan/add_new_invoice_instalasi',$data2);
		
	}
	
	public function create_progress_new_instalasi_approval(){
		
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
			$approval	= $this->uri->segment(4);
			// echo $nomor_id;exit;
			$getBq 		= $this->db->select('no_ipp as no_po, base_cur')->where_in('id',$nomor_id)->get('billing_so')->result_array();

			$in_ipp = [];
			$in_bq = [];

			foreach($getBq AS $val => $valx){
				$in_ipp[$val] 	= $valx['no_po'];
				$in_bq[$val] 	= 'BQ-'.$valx['no_po'];
				$in_so[$val] 	= get_nomor_so($valx['no_po']);
				$base_cur		= $valx['base_cur'];
			}
			if(empty($in_ipp)) {echo 'Nomor SO kosong';die();}
			$penagihan_detail 	= $this->db->get_where('penagihan_detail', array('id_penagihan'=>$id))->row();
			$noipp=implode("','",$in_ipp);
			$id_produksi=implode("','PRO-",$in_ipp);
			$id_bq=implode("','BQ-",$in_ipp);
			$kode_delivery=str_ireplace(",","','",$penagihan[0]->delivery_no);
			$getHeader	= $this->db->where_in('no_ipp',$in_ipp)->get('production')->result();
			if(!empty($penagihan_detail)){
				$getDetail	= $this->db->query("select *,harga_total as total_deal_usd, dim_1 as dim1,dim_2 as dim2, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item
				from penagihan_detail where kategori_detail='PRODUCT' and id_penagihan='".$id."'")->result_array();
				$getEngCost	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','ENGINERING')->get('penagihan_detail')->result_array();
				$getPackCost= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','PACKING')->get('penagihan_detail')->result_array();
				$getTruck	= $this->db->select('*')->order_by('id','asc')->where('id_penagihan',$id)->where('kategori_detail','TRUCKING')->get('penagihan_detail')->result_array();
				$non_frp	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='BQ')")->where('id_penagihan',$id)->get()->result_array();
				$other  	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->from('penagihan_detail')->where("(kategori_detail='OTHER')")->where('id_penagihan',$id)->get()->result_array();
				
				$material	= $this->db->select('*, unit satuan, qty as qty_delivery,qty_sisa as qty_inv, nm_material as product, product_cust as customer_item')->where('id_penagihan',$id)->get_where('penagihan_detail',array('kategori_detail'=>'MATERIAL'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
				$get_kurs	= $this->db->select(' (kurs_jual) AS kurs,  (progress_persen) AS uang_muka_persen,  0 AS uang_muka_persen2')->where('id',$id)->get('penagihan')->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
			}else{
// old
				/*
				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,d.qty_delivery,d.cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik join (
				SELECT id_product, count(qty) as qty_delivery, sum(finish_good) as cogs FROM production_detail
				where id_produksi in ('PRO-".$id_produksi."') group by id_product
				) d on b.id_product=d.id_product
				where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";
				*/
				
				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,0 qty_delivery,0 cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik
				where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";

//				$sqlDetail="select a.*,a.qty as qty_total,(a.qty-a.qty_inv) qty_inv,0 qty_delivery,0 cogs from billing_so_product a join so_bf_detail_header b on a.id_milik=b.id_milik where b.id_bq in ('BQ-".$id_bq."') order by a.id_milik";
                
				$getDetail	= $this->db->query($sqlDetail)->result_array();
//				$getDetail	= $this->db->order_by('id_milik','asc')->where_in('no_ipp',$in_ipp)->get('billing_so_product')->result_array();
				$getEngCost	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','eng')->get('billing_so_add')->result_array();
				$getPackCost= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','pack')->get('billing_so_add')->result_array();
				$getTruck	= $this->db->select('*,0 total_delivery ')->order_by('id','asc')->where_in('no_ipp',$in_ipp)->where('category','ship')->get('billing_so_add')->result_array();
				$non_frp	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='baut' OR category='plate' OR category='gasket' OR category='lainnya')")->where_in('no_ipp',$in_ipp)->get()->result_array();
				$material	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->where_in('no_ipp',$in_ipp)->get_where('billing_so_add',array('category'=>'mat'))->result_array();
				$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();
                $other   	= $this->db->select('*,qty as qty_total, (qty-qty_inv) qty_inv,0 qty_delivery ')->from('billing_so_add')->where("(category='other')")->where_in('no_ipp',$in_ipp)->get()->result_array();
			
//				$get_kurs	= $this->db->select(' (kurs_usd_dipakai) AS kurs,  (uang_muka_persen) AS uang_muka_persen,  (uang_muka_persen2) AS uang_muka_persen2')->where_in('no_ipp',$in_ipp)->get('billing_so')->result();

				$get_kurs  = $this->db->query("select persen_um as uang_muka_persen,kurs_um as kurs from tr_kartu_po_customer where nomor_po ='".$penagihan[0]->no_po."'")->result();
				$get_tagih	= $this->db->order_by('id','ASC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
//				$uang_muka_persen = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
				$uang_muka_persen = $get_kurs[0]->uang_muka_persen;
				if($base_cur=='USD'){
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}else{
					$down_payment = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
				}
				$uang_muka_persen2 = 0;
				$down_payment2 = 0;
				if(count($get_tagih) > 1){
					$get_tagih		= $this->db->order_by('id','DESC')->get_where('penagihan',array('no_po'=>$penagihan[0]->no_po,'type'=>'uang muka'))->result();
					$uang_muka_persen2 = (!empty($get_tagih))?$get_tagih[0]->progress_persen:0;
					if($base_cur=='USD'){
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}else{
						$down_payment2 = (!empty($get_tagih))?$get_tagih[0]->grand_total:0;
					}
				}
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
				'other'		    => $other,
				'material'		=> $material,
				'list_top'		=> $list_top,
				'base_cur'		=> $base_cur,
				'in_ipp'		=> implode(',',$in_ipp),
				'in_bq'			=> implode(',',$in_bq),
				'in_so'			=> implode(',',$in_so),
				'arr_in_ipp'	=> $in_ipp,
				'penagihan'		=> $penagihan,
				'kurs'			=> $get_kurs[0]->kurs,
				'uang_muka_persen'	=> $uang_muka_persen,
				'uang_muka_persen2'	=> 0,
				'down_payment'	=> $down_payment,
				'down_payment2'	=> 0,
				'id'			=> $id,
				'approval'		=> $approval
			);
			$this->load->view('Penagihan/create_progress_new_instalasi_approval',$data2);
		
		
	}
	
	public function add_new_progress_tanki(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			
			// print_r($data);
			// exit;
			
			$data_session	= $this->session->userdata;

			$check = $data['check'];
			$idso = $data['id'];
			$dtdelivery_no='';
			$dtListArray = [];
			if(!empty($check)){
				
				if($data['type']!='progress'){
					foreach($idso AS $val => $valx){
						$dtListArray[$val] = $valx;
					}
				}
				else {
					foreach($check AS $val => $valx){
						$dtListArray[$val] = $valx;
					}
					
				}
				
				$dtImplode	= "('".implode("','", $dtListArray)."')";
				$dtImplode2	= implode(",", $dtListArray); 
				
				
			
				$updDelivery="";
				$updDeliveryHeader="";
				if($data['type']!='progress'){
					$result_data 	= $this->db->query("SELECT * FROM billing_so WHERE id IN ".$dtImplode." ORDER BY id ")->result_array();

				}else{
					
					$updDelivery="update delivery_product_detail set sts_invoice='1' WHERE kode_delivery IN ".$dtImplode." ";
					$updDeliveryHeader="update delivery_product set st_cogs='1' WHERE kode_delivery IN ".$dtImplode." ";
					$dtdelivery_no=$dtImplode2;
					$getipp 	= $this->db->query("SELECT replace(id_produksi,'PRO-','') id_produksi FROM delivery_product_detail WHERE kode_delivery IN ".$dtImplode." group BY id_produksi")->result();
					$dtListipp = [];
					foreach($getipp AS $val => $valx ){
						$dtListipp[]=$valx->id_produksi;
					}
					$dtImplode	= "('".implode("','", $dtListipp)."')";
					$result_data 	= $this->db->query("SELECT * FROM view_ipp_header WHERE no_ipp IN ".$dtImplode." ORDER BY no_ipp ")->result_array();
					
					
					$dtListIDipp = [];
					foreach($result_data AS $val => $valx ){
						$dtListIDipp[$val['id']] = $valx['id'];
					}
					$dtImplode	= "('".implode("','", $dtListIDipp)."')"; 
					$dtImplode2	= implode(",", $dtListIDipp);
					
					// print_r($dtListIDipp);
			        // exit;
				}
				
				
				
								
			}else{
				$Arr_Kembali	= array(
					'pesan'		=>'Process data failed. Please check input ...',
					'status'	=> 2
				);
				echo json_encode($Arr_Kembali);
				die();
			}
			$max_num 		= $this->db->select('MAX(id) AS nomor_max')->get('penagihan')->result();
			$id_tagih 		= $max_num[0]->nomor_max + 1;

		
			$SUM_USD = 0;
			$SUM_IDR = 0;
			$Update_b = [];
			foreach($result_data AS $val => $valx){
				$SUM_USD += $valx['total_deal_usd'];
				$SUM_IDR += $valx['total_deal_idr'];
				$no_ipp = str_replace('BQ-','',$valx['no_ipp']);

				$Update_b[$val]['id'] = $valx['id'];
				$Update_b[$val]['id_penagihan'] = $id_tagih;
			}
			
			
					
			$header = [
				'delivery_no' => $dtdelivery_no,
				'no_so' => $dtImplode2,
				'no_ipp' => $no_ipp,
				'no_po' => $data['no_po'],
				'project' => NULL,
				'kode_customer' => $data['customer'],
				'customer' => get_name('customer','nm_customer','id_customer',$data['customer']),
				'keterangan' => NULL,
				'plan_tagih_date' => date("Y-m-d"),
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
				'term_delivery' => $data['term_delivery'],
				'created_by' => $this->session->userdata['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')

			];

			$this->db->trans_start();
				$this->db->insert('penagihan', $header);
				//$this->db->update_batch('billing_top', $Update_b, 'id');

				// update billing so status
				$this->db->query("update billing_so set status='1' WHERE id IN ".$dtImplode." and status='0' ");

				// update Delivery
				if($updDelivery!="") {
					$this->db->query($updDelivery);
					$this->db->query($updDeliveryHeader);
				}

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

			$data = array(
				'title'			=> 'Indeks Of Add Billing',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'customer'		=> $customer,
				'no_po'			=> $no_po
			);

			$this->load->view('Penagihan/add_new_progress_tanki',$data);
		}
	}
	
	public function server_side_penagihan_add_new_progress_tanki(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		if($requestData['no_po']=='0') die();
		$fetch			= $this->query_data_penagihan_add_new_progress_tanki(
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
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['kode_delivery']."'>
			<input type='hidden' name='id[$nomor]' value='".$row['id']."' />
			<input type='hidden' name='ipp[$nomor]' value='".$row['no_ipp']."' />
			<input type='hidden' name='delivery_".$row['id']."' value='".$row['no_ipp']."' />
			</div>";
			$nestedData[]	= "<div align='left'><input type='input' name='so_number[$nomor]' class='form-control' value='".$row['so_number']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='no_pox[$nomor]' class='form-control' value='".$row['no_pox']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='project[$nomor]' class='form-control' value='".$row['project']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='customer2[$nomor]' class='form-control' value='".$row['customer']."'></div>";
			$nestedData[]	= "<div align='left'><input type='input' name='kode_delivery[$nomor]' class='form-control' value='".$row['kode_delivery']."'></div>";

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
	public function query_data_penagihan_add_new_progress_tanki($customer, $type, $no_po, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_customer = '';
if($type!='progress'){
		if($customer != '0'){
		}
			$where_customer = " AND b.id_customer='".$customer."'";

		$where_no_po = '';
		if($no_po != '0'){
		}
			$where_no_po = " AND b.no_po='".$no_po."'";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				b.no_ipp as id,
				b.project,
				b.id_customer as kode_customer,
				b.nm_customer AS customer,
				b.no_po AS no_pox,
			    b.no_so AS so_number,
				'' kode_delivery , b.no_ipp
			FROM
				view_ipp_header b,
				(SELECT @row:=0) r
		    WHERE (status=1 or status=0) ".$where_customer." ".$where_no_po." AND (
			    b.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.id_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_po = '".$this->db->escape_like_str($like_value)."'
	        )
		";
}
else{
		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.id, c.so_number,c.no_po as no_pox,c.project,c.nm_customer as customer,a.kode_delivery , c.no_ipp
				FROM
					delivery_product a
					LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery
					left join (select no_ipp, no_so as so_number, no_po, project, id_customer as kode_customer, nm_customer from view_ipp_header) c on replace(b.id_produksi,'PRO-','')=c.no_ipp,
					(SELECT @row:=0) r
				WHERE b.sts_invoice=0
					AND b.posisi = 'CUSTOMER'
					AND (
						 c.no_po like '%".$no_po."%'
						)
				GROUP BY
					a.kode_delivery,c.so_number,c.no_po,c.project,c.nm_customer
		";		//c.id_customer='".$customer."' and
}
		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'so_number',
			2 => 'no_pox',
			3 => 'project',
			4 => 'customer',
			5 => 'kode_delivery',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	

	
}