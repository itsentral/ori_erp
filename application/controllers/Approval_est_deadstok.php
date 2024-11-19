<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_est_deadstok extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

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

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Approval Est. Deadstok',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View approval estimasi deadsok modif');
		$this->load->view('Deadstok_modif/estimasi_approve',$data);
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_spec']."</div>";

			$view	= "";
			$approve	= "";
			$delete	= "";
			$update	= "";
			if($Arr_Akses['delete']=='1' AND $row['status'] == 'ESTIMASI'){
				$delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete Estimasi' data-id='".$row['kode']."'><i class='fa fa-trash'></i></button>";
			}
			$view	= "<button type='button' class='btn btn-sm btn-warning detail' title='Detail Estimasi' data-id='".$row['kode']."'><i class='fa fa-eye'></i></button>";
			$approve	= "&nbsp;<button type='button' class='btn btn-sm btn-success approve' title='Ajukan Estimasi' data-id='".$row['kode']."'><i class='fa fa-check'></i></button>";
			
			$nestedData[]	= "<div align='left'>".$view.$update.$delete.$approve."</div>";
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
 
		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
                    b.type,
                    b.product_name,
                    b.product_spec,
					COUNT(a.id) AS qty,
                    b.no_ipp,
                    b.no_so,
                    c.nm_customer,
                    c.project
				FROM
					deadstok_modif a
                    LEFT JOIN deadstok b ON a.id_deadstok = b.id
                    LEFT JOIN production c ON b.no_ipp=c.no_ipp,
					(SELECT @row:=0) r
				WHERE a.deleted_date IS NULL AND a.status='WAITING APPROVE' AND(
					b.type LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.created_date
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.product_name',
			2 => 'b.product_spec',
            3 => 'nomor',
			4 => 'a.created_by',
			5 => 'a.created_date',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function estimasi_detail($kode=null){
			
		$ComponentDetailLiner			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'utama','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailLinerPlus		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailLinerAdd		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		$ComponentDetailStructure		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'utama','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailStructurePlus	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailStructureAdd	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		$ComponentDetailEksternal		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'utama','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailEksternalPlus	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailEksternalAdd	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		$ComponentDetailTopPlus			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'TOPCOAT','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailTopAdd			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'TOPCOAT','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		//DETAIL DEADASTOK
		$HeaderDeadstok = $this->db
									->select('b.no_so, b.no_ipp, b.no_spk, a.proses, b.product_name, b.product_spec, COUNT(a.id) AS qty')
									->group_by('a.kode')
									->join('deadstok b','a.id_deadstok=b.id','left')
									->get_where('deadstok_modif a',array('kode'=>$kode))
									->result_array();

		$data = array(
			'title'			=> 'Edit Estimasi Deadstok',
			'action'		=> 'index',
			'kode'			=> $kode,
			'HeaderDeadstok'		=> $HeaderDeadstok,
			'detLiner'			=> $ComponentDetailLiner,
			'detLinerPlus'		=> $ComponentDetailLinerPlus,
			'detLinerAdd'		=> $ComponentDetailLinerAdd,
			'detStructure'			=> $ComponentDetailStructure,
			'detStructurePlus'		=> $ComponentDetailStructurePlus,
			'detStructureAdd'		=> $ComponentDetailStructureAdd,
			'detEksternal'			=> $ComponentDetailEksternal,
			'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
			'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
			'detTopPlus'	=> $ComponentDetailTopPlus,
			'detTopAdd'		=> $ComponentDetailTopAdd,
		);
			
		$this->load->view('Deadstok_modif/estimasi_material_detail', $data);
	}

	public function estimasi_approve_action($kode=null){
			
		$ComponentDetailLiner			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'utama','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailLinerPlus		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailLinerAdd		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		$ComponentDetailStructure		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'utama','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailStructurePlus	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailStructureAdd	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		$ComponentDetailEksternal		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'utama','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailEksternalPlus	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailEksternalAdd	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		$ComponentDetailTopPlus			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'TOPCOAT','category'=>'plus','id_material <>'=>'MTL-1903000'))->result_array();
		$ComponentDetailTopAdd			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'TOPCOAT','category'=>'add','id_material <>'=>'MTL-1903000'))->result_array();
		
		//DETAIL DEADASTOK
		$HeaderDeadstok = $this->db
									->select('b.no_so, b.no_ipp, b.no_spk, a.proses, b.product_name, b.product_spec, COUNT(a.id) AS qty')
									->group_by('a.kode')
									->join('deadstok b','a.id_deadstok=b.id','left')
									->get_where('deadstok_modif a',array('kode'=>$kode))
									->result_array();

		$data = array(
			'title'			=> 'Edit Estimasi Deadstok',
			'action'		=> 'index',
			'kode'			=> $kode,
			'HeaderDeadstok'		=> $HeaderDeadstok,
			'detLiner'			=> $ComponentDetailLiner,
			'detLinerPlus'		=> $ComponentDetailLinerPlus,
			'detLinerAdd'		=> $ComponentDetailLinerAdd,
			'detStructure'			=> $ComponentDetailStructure,
			'detStructurePlus'		=> $ComponentDetailStructurePlus,
			'detStructureAdd'		=> $ComponentDetailStructureAdd,
			'detEksternal'			=> $ComponentDetailEksternal,
			'detEksternalPlus'		=> $ComponentDetailEksternalPlus,
			'detEksternalAdd'		=> $ComponentDetailEksternalAdd,
			'detTopPlus'	=> $ComponentDetailTopPlus,
			'detTopAdd'		=> $ComponentDetailTopAdd,
		);
			
		$this->load->view('Deadstok_modif/estimasi_approve_action', $data);
	}

	public function process_approval(){
		$data 	= $this->input->post();
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$id 	= $data['kode'];
		$status = ($data['action'] == 'Y')?'APPROVED':'ESTIMASI';

		$InsertKode = [];
		if($data['action'] == 'Y'){
			$YM	= 'D'.date('ym');
			$srcPlant		= "SELECT MAX(kode_spk) as maxP FROM production_spk WHERE kode_spk LIKE '".$YM."%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_spk		= $YM.$urut2;

			$HeaderDeadstok = $this->db
									->select('a.id, b.no_so, b.no_ipp, b.no_spk, a.proses, b.product_name, b.product_spec, COUNT(a.id) AS qty, b.id_milik')
									->group_by('a.kode')
									->join('deadstok b','a.id_deadstok=b.id','left')
									->get_where('deadstok_modif a',array('kode'=>$id))
									->result_array();

			$key = 0;
			$InsertKode[$key]['kode_spk'] 		= $kode_spk;
			$InsertKode[$key]['id_milik'] 		= $HeaderDeadstok[0]['id'];
			$InsertKode[$key]['product'] 		= $HeaderDeadstok[0]['product_name'];
			$InsertKode[$key]['id_product'] 	= 'deadstok';
			$InsertKode[$key]['no_spk'] 		= $HeaderDeadstok[0]['no_spk'];
			$InsertKode[$key]['no_ipp'] 		= $HeaderDeadstok[0]['no_ipp'];
			$InsertKode[$key]['qty'] 			= $HeaderDeadstok[0]['qty'];
			$InsertKode[$key]['created_by'] 	= $username;
			$InsertKode[$key]['created_date'] 	= $datetime;
			$InsertKode[$key]['product_code_cut'] 	= $id;
			$InsertKode[$key]['product_code'] 	= $HeaderDeadstok[0]['no_so'];
			$InsertKode[$key]['urut_product'] 	= $HeaderDeadstok[0]['id_milik'];
		}
		
		$ArrPlant = array(
			'kode_spk'		 	=> ($data['action'] == 'Y')?$kode_spk:null,
			'status'		 	=> $status,
			'reason_reject'		=> (!empty($data['reason']))?$data['reason']:null,
			'approved_by' 		=> $username,
			'approved_date' 	=> $datetime
		);

		// print_r($InsertKode);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('kode', $id); 
			$this->db->update('deadstok_modif', $ArrPlant);

			if(!empty($InsertKode)){
				$this->db->insert_batch('production_spk', $InsertKode);
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1
			);				
			history('Process approval deadstok modify : '.$id);
		}
		echo json_encode($Arr_Data);
	}

}