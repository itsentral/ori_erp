<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Est_modifikasi_deadstok extends CI_Controller {

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
			'title'			=> 'Estimasi Modifikasi Deadstok',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View estimasi deadsok modif');
		$this->load->view('Deadstok_modif/estimasi',$data);
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
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['type']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_spec']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['estimasi_by']."</div>";
			$estimasi_date = (!empty($row['estimasi_date']))?date('d-M-Y H:i',strtotime($row['estimasi_date'])):'';
			$nestedData[]	= "<div align='center'>".$estimasi_date."</div>";

            $status = "";
            if($row['status'] == 'WAITING'){
                $status = "<span class='badge bg-blue'>Waiting Estimasi</span>";
            }
			if($row['status'] == 'ESTIMASI'){
                $status = "<span class='badge bg-yellow'>Estimasi</span>";
            }
			if($row['status'] == 'WAITING APPROVE'){
                $status = "<span class='badge bg-purple'>Waiting Approve</span>";
            }
			if($row['status'] == 'APPROVED'){
                $status = "<span class='badge bg-green'>Approved</span>";
            }

            $nestedData[]	= "<div align='left'>".$status."</div>";
			$nestedData[]	= "<div align='left'>".$row['reason_reject']."</div>";

			$view	= "";
			$approve	= "";
			$delete	= "";
			$update	= "";
			if($Arr_Akses['delete']=='1' AND $row['status'] == 'ESTIMASI'){
				$delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete Estimasi' data-id='".$row['kode']."'><i class='fa fa-trash'></i></button>";
			}
			if($row['status'] == 'ESTIMASI' OR $row['status'] == 'WAITING APPROVE' OR $row['status'] == 'APPROVED'){
				$view	= "<button type='button' class='btn btn-sm btn-warning detail' title='Detail Estimasi' data-id='".$row['kode']."'><i class='fa fa-eye'></i></button>";
			}
			if($row['status'] == 'ESTIMASI'){
				$approve	= "&nbsp;<button type='button' class='btn btn-sm btn-success approve' title='Ajukan Estimasi' data-id='".$row['kode']."'><i class='fa fa-check'></i></button>";
			}
			if($Arr_Akses['update']=='1' AND $row['status'] == 'WAITING'){
				$update	= "&nbsp;<a href='".base_url('est_modifikasi_deadstok/estimasi/'.$row['kode'].'')."' class='btn btn-sm btn-primary' title='Estimasi'><i class='fa fa-edit'></i></a>";
			}
			if($Arr_Akses['update']=='1' AND $row['status'] == 'ESTIMASI'){
				$update	= "&nbsp;<a href='".base_url('est_modifikasi_deadstok/estimasi_edit/'.$row['kode'].'')."' class='btn btn-sm btn-primary' title='Edit Estimasi'><i class='fa fa-edit'></i></a>";
			}
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
					b.no_so,
					b.no_spk,
					COUNT(a.id) AS qty
				FROM
					deadstok_modif a
                    LEFT JOIN deadstok b ON a.id_deadstok = b.id,
					(SELECT @row:=0) r
				WHERE a.deleted_date IS NULL AND a.status != 'REJECTED' AND(
					b.type LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
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

    public function estimasi($kode=null){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');

			$id_deadstok		= $data['id_deadstok'];

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
		
				// echo "Masuk Save";
				// exit;

				$ArrHeader	= array(
					'status'			=> 'ESTIMASI',
					'estimasi_by'		=> $data_session['ORI_User']['username'],
					'estimasi_date'		=> date('Y-m-d H:i:s')
				);
				
				// print_r($ArrHeader);
				// exit;

				// Detail1
				$ArrDetail1	= array();
				foreach($ListDetail AS $val => $valx){
					$IDMat1			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat1			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

					$ArrDetail1[$val]['kode'] 			= $id_deadstok;
					$ArrDetail1[$val]['category'] 		= 'utama';
					$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail1[$val]['id_material'] 	= $IDMat1;
					$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDetail1[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
				}
				// print_r($ArrDetail1);
				// exit;
				//Detail2
				$ArrDetail2	= array();
				foreach($ListDetail2 AS $val => $valx){
					$IDMat2			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat2			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

					$ArrDetail2[$val]['kode'] 			= $id_deadstok;
					$ArrDetail2[$val]['category'] 		= 'utama';
					$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail2[$val]['id_material'] 	= $IDMat2;
					$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDetail2[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
				}
				// print_r($ArrDetail2);
				// exit;
				//Detail3
				$ArrDetail13	= array();
				foreach($ListDetail3 AS $val => $valx){
					$IDMat3			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$IDMat3			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

					$ArrDetail13[$val]['kode'] 			= $id_deadstok;
					$ArrDetail13[$val]['category'] 		= 'utama';
					$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetail13[$val]['id_material'] 	= $IDMat3;
					$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDetail13[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
				// print_r($ArrDetail13);
				// exit;
				$ArrDetailPlus1	= array();
				foreach($ListDetailPlus AS $val => $valx){
					$id_material			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$id_material			= "MTL-1903000";
					}
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$id_material."' LIMIT 1")->result_array();

					$ArrDetailPlus1[$val]['kode'] 			= $id_deadstok;
					$ArrDetailPlus1[$val]['category'] 		= 'plus';
					$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
					$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus1[$val]['id_material'] 	= $id_material;
					$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDetailPlus1[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
				}
				// print_r($ArrDetailPlus1);
				// exit;
				$ArrDetailPlus2	= array();
				foreach($ListDetailPlus2 AS $val => $valx){
					$id_material			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$id_material			= "MTL-1903000";
					}

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$id_material."' LIMIT 1")->result_array();

					$ArrDetailPlus2[$val]['kode'] 			= $id_deadstok;
					$ArrDetailPlus2[$val]['category'] 		= 'plus';
					$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
					$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus2[$val]['id_material'] 	= $id_material;
					$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDetailPlus2[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
				}
				// print_r($ArrDetailPlus2);
				// exit;
				$ArrDetailPlus3	= array();
				foreach($ListDetailPlus3 AS $val => $valx){
					$id_material			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$id_material			= "MTL-1903000";
					}

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$id_material."' LIMIT 1")->result_array();

					$ArrDetailPlus3[$val]['kode'] 			= $id_deadstok;
					$ArrDetailPlus3[$val]['category'] 		= 'plus';
					$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
					$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus3[$val]['id_material'] 	= $id_material;
					$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDetailPlus3[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
				}
				// print_r($ArrDetailPlus3);

				$ArrDetailPlus4	= array();
				foreach($ListDetailPlus4 AS $val => $valx){
					$id_material			= $valx['id_material'];
					if($valx['id_material'] == null || $valx['id_material'] == ''){
						$id_material			= "MTL-1903000";
					}

					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$id_material."' LIMIT 1")->result_array();

					$ArrDetailPlus4[$val]['kode'] 	= $id_deadstok;
					$ArrDetailPlus4[$val]['category'] 		= 'plus';
					$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
					$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
					$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDetailPlus4[$val]['id_material'] 	= $id_material;
					$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDetailPlus4[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
				}
				// print_r($ArrDetailPlus4);

				$ArrDataAdd1 = array();
				if(!empty($data['ListDetailAdd_Liner'])){
					foreach($data['ListDetailAdd_Liner'] AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd1[$val]['kode'] 			= $id_deadstok;
						$ArrDataAdd1[$val]['category'] 		= 'add';
						$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
						$ArrDataAdd1[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$ArrDataAdd1[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
					}
				}
				$ArrDataAdd2 = array();
				if(!empty($data['ListDetailAdd_Strukture'])){
					foreach($data['ListDetailAdd_Strukture'] AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd2[$val]['kode'] 			= $id_deadstok;
						$ArrDataAdd2[$val]['category'] 		= 'add';
						$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
						$ArrDataAdd2[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$ArrDataAdd2[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
					}
				}
				$ArrDataAdd3 = array();
				if(!empty($data['ListDetailAdd_External'])){
					foreach($data['ListDetailAdd_External'] AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd3[$val]['kode'] 			= $id_deadstok;
						$ArrDataAdd3[$val]['category'] 		= 'add';
						$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
						$ArrDataAdd3[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$ArrDataAdd3[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
					}
				}
				$ArrDataAdd4 = array();
				if(!empty($data['ListDetailAdd_TopCoat'])){
					foreach($data['ListDetailAdd_TopCoat'] AS $val => $valx){
						$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

						$ArrDataAdd4[$val]['kode'] 			= $id_deadstok;
						$ArrDataAdd4[$val]['category'] 		= 'add';
						$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
						$ArrDataAdd4[$val]['id_category'] 	= $valx['id_category'];
						$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
						$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
						$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
						$ArrDataAdd4[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
					}
				}

				// echo "<pre>";
				// print_r($ArrHeader);
				// print_r($ArrDetail1);
				// print_r($ArrDetail2);
				// print_r($ArrDetail13);
				// print_r($ArrDetailPlus1);
				// print_r($ArrDetailPlus2);
				// print_r($ArrDetailPlus3);
				// print_r($ArrDetailPlus4);
				// print_r($ArrDataAdd1);
				// print_r($ArrDataAdd2);
				// print_r($ArrDataAdd3);
				// print_r($ArrDataAdd4);
				// exit;

				$this->db->trans_start();
					$this->db->where('kode', $id_deadstok);
					$this->db->update('deadstok_modif', $ArrHeader);

					$this->db->insert_batch('deadstok_estimasi', $ArrDetail1);
					$this->db->insert_batch('deadstok_estimasi', $ArrDetail2);
					$this->db->insert_batch('deadstok_estimasi', $ArrDetail13);
					$this->db->insert_batch('deadstok_estimasi', $ArrDetailPlus1);
					$this->db->insert_batch('deadstok_estimasi', $ArrDetailPlus2);
					$this->db->insert_batch('deadstok_estimasi', $ArrDetailPlus3);
					$this->db->insert_batch('deadstok_estimasi', $ArrDetailPlus4);

					if(!empty($ArrDataAdd1)){
						$this->db->insert_batch('deadstok_estimasi', $ArrDataAdd1);
					}
					if(!empty($ArrDataAdd2)){
						$this->db->insert_batch('deadstok_estimasi', $ArrDataAdd2);
					}
					if(!empty($ArrDataAdd3)){
						$this->db->insert_batch('deadstok_estimasi', $ArrDataAdd3);
					}
					if(!empty($ArrDataAdd4)){
						$this->db->insert_batch('deadstok_estimasi', $ArrDataAdd4);
					}
				$this->db->trans_complete();

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
						'status'	=> 1
					);
					history('Add estimation deadstok : '.$id_deadstok);
				}
			


			echo json_encode($Arr_Kembali);
		}
		else{
			//List Dropdown
			$List_Realese		= List_Realese();
			$List_PlasticFirm	= List_PlasticFirm();
			$List_Veil			= List_Veil();
			$List_Resin			= List_Resin();
			$List_MatCsm		= List_MatCsm();
			$List_MatKatalis	= List_MatKatalis();
			$List_MatSm			= List_MatSm();
			$List_MatCobalt		= List_MatCobalt();
			$List_MatDma		= List_MatDma();
			$List_MatHydo		= List_MatHydo();
			$List_MatMethanol	= List_MatMethanol();
			$List_MatAdditive	= List_MatAdditive();
			$List_MatWR			= List_MatWR();
			$List_MatRooving	= List_MatRooving();
			$List_MatColor		= List_MatColor();
			$List_MatTinuvin	= List_MatTinuvin();
			$List_MatChl		= List_MatChl();
			$List_MatWax		= List_MatWax();
			$List_MatMchl		= List_MatMchl();

			//DETAIL DEADASTOK
			$HeaderDeadstok = $this->db
										->select('b.no_so, b.no_ipp, b.no_spk, a.proses, b.product_name, b.product_spec, COUNT(a.id) AS qty')
										->group_by('a.kode')
										->join('deadstok b','a.id_deadstok=b.id','left')
										->get_where('deadstok_modif a',array('kode'=>$kode))
										->result_array();

			$data = array(
				'title'					=> 'Estimasi Deadstok',
				'action'				=> 'index',
				'kode'				    => $kode,
				'HeaderDeadstok'		=> $HeaderDeadstok,

				'ListRealise'			=> $List_Realese,
				'ListPlastic'			=> $List_PlasticFirm,
				'ListVeil'				=> $List_Veil,
				'ListResin'				=> $List_Resin,
				'ListMatCsm'			=> $List_MatCsm,
				'ListMatKatalis'		=> $List_MatKatalis,
				'ListMatSm'				=> $List_MatSm,
				'ListMatCobalt'			=> $List_MatCobalt,
				'ListMatDma'			=> $List_MatDma,
				'ListMatHydo'			=> $List_MatHydo,
				'ListMatMethanol'		=> $List_MatMethanol,
				'ListMatAdditive'		=> $List_MatAdditive,
				'ListMatWR'				=> $List_MatWR,
				'ListMatRooving'		=> $List_MatRooving,
				'ListMatColor'			=> $List_MatColor,
				'ListMatTinuvin'		=> $List_MatTinuvin,
				'ListMatChl'			=> $List_MatChl,
				'ListMatStery'			=> $List_MatSm,
				'ListMatWax'			=> $List_MatWax,
				'ListMatMchl'			=> $List_MatMchl
			);

			$this->load->view('Deadstok_modif/estimasi_material', $data); 
		}
	}

	public function estimasi_edit($kode=null){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$mY				=  date('ym');
			
			$id_deadstok		= $data['id_deadstok'];

			$ListDetail			= $data['ListDetail'];
			$ListDetail2		= $data['ListDetail2'];
			$ListDetail3		= $data['ListDetail3'];
			$ListDetailPlus		= $data['ListDetailPlus'];
			$ListDetailPlus2	= $data['ListDetailPlus2'];
			$ListDetailPlus3	= $data['ListDetailPlus3'];
			$ListDetailPlus4	= $data['ListDetailPlus4'];
			
			if(!empty($data['ListDetailAdd'])){
				$ListDetailAdd1	= $data['ListDetailAdd'];
			}
			if(!empty($data['ListDetailAdd2'])){
				$ListDetailAdd2	= $data['ListDetailAdd2'];
			}
			if(!empty($data['ListDetailAdd3'])){
				$ListDetailAdd3	= $data['ListDetailAdd3'];
			}
			if(!empty($data['ListDetailAdd4'])){
				$ListDetailAdd4	= $data['ListDetailAdd4'];
			}
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$ListDetailAdd_Liner	= $data['ListDetailAdd_Liner'];
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$ListDetailAdd_Strukture	= $data['ListDetailAdd_Strukture'];
			}
			if(!empty($data['ListDetailAdd_External'])){
				$ListDetailAdd_External	= $data['ListDetailAdd_External'];
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$ListDetailAdd_TopCoat	= $data['ListDetailAdd_TopCoat'];
			}
			
			
			$ArrHeader	= array(
				'estimasi_by'			=> $data_session['ORI_User']['username'],
				'estimasi_date'			=> date('Y-m-d H:i:s')
			);
			
			
			// print_r($ArrHeader); exit;
		
			// Detail1
			$ArrDetail1	= array();
			foreach($ListDetail AS $val => $valx){
				$IDMat1			= $valx['id_material'];
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

				$ArrDetail1[$val]['id'] 			= $valx['id'];
				$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail1[$val]['id_material'] 	= $IDMat1;
				$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrDetail1[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
			}
			// print_r($ArrDetail1);
			// exit;
			//Detail2
			$ArrDetail2	= array();
			foreach($ListDetail2 AS $val => $valx){
				$IDMat2			= $valx['id_material'];
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

				$ArrDetail2[$val]['id'] 			= $valx['id'];
				$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail2[$val]['id_material'] 	= $IDMat2;
				$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrDetail2[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
			}
			// print_r($ArrDetail2);
			// exit;
			//Detail3
			$ArrDetail13	= array();
			foreach($ListDetail3 AS $val => $valx){
				$IDMat3			= $valx['id_material'];
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

				$ArrDetail13[$val]['id'] 			= $valx['id'];
				$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetail13[$val]['id_material'] 	= $IDMat3;
				$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrDetail13[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
			}
			// print_r($ArrDetail13);

			$ArrDetailPlus1	= array();
			foreach($ListDetailPlus AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus1[$val]['id'] 			= $valx['id'];
				$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrDetailPlus1[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
			}
			// print_r($ArrDetailPlus1);

			$ArrDetailPlus2	= array();
			foreach($ListDetailPlus2 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus2[$val]['id'] 			= $valx['id'];
				$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrDetailPlus2[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
			}
			// print_r($ArrDetailPlus2);

			$ArrDetailPlus3	= array();
			foreach($ListDetailPlus3 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus3[$val]['id'] 			= $valx['id'];
				$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrDetailPlus3[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
			}
			// print_r($ArrDetailPlus3);

			$ArrDetailPlus4	= array();
			foreach($ListDetailPlus4 AS $val => $valx){

				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDetailPlus4[$val]['id'] 			= $valx['id'];
				$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
				$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$ArrDetailPlus4[$val]['last_cost'] 		= str_replace(',','',$valx['last_cost']);
			}
			// print_r($ArrDetailPlus4);
			// exit;
			
			$ArrDataAdd1 = array();
			if(!empty($data['ListDetailAdd'])){
				foreach($data['ListDetailAdd'] AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd1[$val]['id'] 	= $valx['id'];
					$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
					$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDataAdd1[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			$ArrDataAdd2 = array();
			if(!empty($data['ListDetailAdd2'])){
				foreach($data['ListDetailAdd2'] AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd2[$val]['id'] 	= $valx['id'];
					$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDataAdd2[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			$ArrDataAdd3 = array();
			if(!empty($data['ListDetailAdd3'])){
				foreach($data['ListDetailAdd3'] AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd3[$val]['id'] 	= $valx['id'];
					$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDataAdd3[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			$ArrDataAdd4 = array();
			if(!empty($data['ListDetailAdd4'])){
				foreach($data['ListDetailAdd4'] AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ArrDataAdd4[$val]['id'] 	= $valx['id'];
					$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
					$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ArrDataAdd4[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			
			//ADD TEMP
			$ListDetailAdd_Liner1 = array();
			if(!empty($data['ListDetailAdd_Liner'])){
				foreach($ListDetailAdd_Liner AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Liner1[$val]['kode'] 	= $id_deadstok;
					$ListDetailAdd_Liner1[$val]['category'] 	= 'add';
					$ListDetailAdd_Liner1[$val]['detail_name'] 	= $data['detail_name'];
					$ListDetailAdd_Liner1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
					$ListDetailAdd_Liner1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Liner1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Liner1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ListDetailAdd_Liner1[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			$ListDetailAdd_Strukture1 = array();
			if(!empty($data['ListDetailAdd_Strukture'])){
				foreach($ListDetailAdd_Strukture AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_Strukture1[$val]['kode'] 	= $id_deadstok;
					$ListDetailAdd_Strukture1[$val]['category'] 	= 'add';
					$ListDetailAdd_Strukture1[$val]['detail_name'] 	= $data['detail_name2'];
					$ListDetailAdd_Strukture1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_Strukture1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_Strukture1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_Strukture1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ListDetailAdd_Strukture1[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			$ListDetailAdd_External1 = array();
			if(!empty($data['ListDetailAdd_External'])){
				foreach($ListDetailAdd_External AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_External1[$val]['kode'] 	= $id_deadstok;
					$ListDetailAdd_External1[$val]['category'] 	= 'add';
					$ListDetailAdd_External1[$val]['detail_name'] 	= $data['detail_name3'];
					$ListDetailAdd_External1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_External1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_External1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_External1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ListDetailAdd_External1[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			$ListDetailAdd_TopCoat1 = array();
			if(!empty($data['ListDetailAdd_TopCoat'])){
				foreach($ListDetailAdd_TopCoat AS $val => $valx){
					$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

					$ListDetailAdd_TopCoat1[$val]['kode'] 			= $id_deadstok;
					$ListDetailAdd_TopCoat1[$val]['category'] 	= 'add';
					$ListDetailAdd_TopCoat1[$val]['detail_name'] 	= $data['detail_name4'];
					$ListDetailAdd_TopCoat1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
					$ListDetailAdd_TopCoat1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
					$ListDetailAdd_TopCoat1[$val]['id_material'] 	= $valx['id_material'];
					$ListDetailAdd_TopCoat1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$ListDetailAdd_TopCoat1[$val]['last_cost'] 	= str_replace(',','',$valx['last_cost']);
				}
			}
			
			
			
			// echo "<pre>";
			// print_r($ArrHeader);
			// print_r($ArrDetail1);
			// print_r($ArrDetail2);
			// print_r($ArrDetail13);

			// print_r($ArrDetailPlus1);
			// print_r($ArrDetailPlus2);
			// print_r($ArrDetailPlus3);
			// print_r($ArrDetailPlus4);

			// print_r($ArrDataAdd1);
			// print_r($ArrDataAdd2);
			// print_r($ArrDataAdd3);
			// print_r($ArrDataAdd4);
			
			// print_r($ListDetailAdd_Liner1);
			// print_r($ListDetailAdd_Strukture1);
			// print_r($ListDetailAdd_External1);
			// print_r($ListDetailAdd_TopCoat1);
			// exit;
			
			$this->db->trans_start();
				$this->db->where('kode', $id_deadstok);
				$this->db->update('deadstok_modif', $ArrHeader);


				$this->db->update_batch('deadstok_estimasi', $ArrDetail1, 'id');
				$this->db->update_batch('deadstok_estimasi', $ArrDetail2, 'id');
				$this->db->update_batch('deadstok_estimasi', $ArrDetail13, 'id');
				$this->db->update_batch('deadstok_estimasi', $ArrDetailPlus1, 'id');
				$this->db->update_batch('deadstok_estimasi', $ArrDetailPlus2, 'id');
				$this->db->update_batch('deadstok_estimasi', $ArrDetailPlus3, 'id');
				$this->db->update_batch('deadstok_estimasi', $ArrDetailPlus4, 'id');

				if(!empty($ArrDataAdd1)){
					$this->db->update_batch('deadstok_estimasi', $ArrDataAdd1, 'id');
				}
				if(!empty($ArrDataAdd2)){
					$this->db->update_batch('deadstok_estimasi', $ArrDataAdd2, 'id');
				}
				if(!empty($ArrDataAdd3)){
					$this->db->update_batch('deadstok_estimasi', $ArrDataAdd3, 'id');
				}
				if(!empty($ArrDataAdd4)){
					$this->db->update_batch('deadstok_estimasi', $ArrDataAdd4, 'id');
				}
				
				if(!empty($ListDetailAdd_Liner1)){
					$this->db->insert_batch('deadstok_estimasi', $ListDetailAdd_Liner1);
				}
				if(!empty($ListDetailAdd_Strukture1)){
					$this->db->insert_batch('deadstok_estimasi', $ListDetailAdd_Strukture1);
				}
				if(!empty($ListDetailAdd_External1)){
					$this->db->insert_batch('deadstok_estimasi', $ListDetailAdd_External1);
				}
				if(!empty($ListDetailAdd_TopCoat1)){
					$this->db->insert_batch('deadstok_estimasi', $ListDetailAdd_TopCoat1);
				}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add Estimation failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Add Estimation Success. Thank you & have a nice day ...',
					'status'	=> 1
				);
				history('Edit Estimasi Deadstok : '.$id_deadstok);
			}
			
			echo json_encode($Arr_Kembali);
		}
		else{
			
			$ComponentDetailLiner			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'utama'))->result_array();
			$ComponentDetailLinerPlus		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'plus'))->result_array();
			$ComponentDetailLinerAdd		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'LINER THIKNESS / CB','category'=>'add'))->result_array();
			
			$ComponentDetailStructure		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'utama'))->result_array();
			$ComponentDetailStructurePlus	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'plus'))->result_array();
			$ComponentDetailStructureAdd	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'STRUKTUR THICKNESS','category'=>'add'))->result_array();
			
			$ComponentDetailEksternal		= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'utama'))->result_array();
			$ComponentDetailEksternalPlus	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'plus'))->result_array();
			$ComponentDetailEksternalAdd	= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'EXTERNAL LAYER THICKNESS','category'=>'add'))->result_array();
			
			$ComponentDetailTopPlus			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'TOPCOAT','category'=>'plus'))->result_array();
			$ComponentDetailTopAdd			= $this->db->get_where('deadstok_estimasi',array('kode'=>$kode, 'detail_name'=>'TOPCOAT','category'=>'add'))->result_array();
			
			$List_Realese		= List_Realese();
			$List_PlasticFirm	= List_PlasticFirm();
			$List_Veil			= List_Veil();
			$List_Resin			= List_Resin();
			$List_MatCsm		= List_MatCsm();
			$List_MatKatalis	= List_MatKatalis();
			$List_MatSm			= List_MatSm();
			$List_MatCobalt		= List_MatCobalt();
			$List_MatDma		= List_MatDma();
			$List_MatHydo		= List_MatHydo();
			$List_MatMethanol	= List_MatMethanol();
			$List_MatAdditive	= List_MatAdditive();
			$List_MatWR			= List_MatWR();
			$List_MatRooving	= List_MatRooving();
			$List_MatColor		= List_MatColor();
			$List_MatTinuvin	= List_MatTinuvin();
			$List_MatChl		= List_MatChl();
			$List_MatWax		= List_MatWax();
			$List_MatMchl		= List_MatMchl();

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
				
				'ListRealise'	=> $List_Realese,
				'ListPlastic'	=> $List_PlasticFirm,
				'ListVeil'		=> $List_Veil,
				'ListResin'		=> $List_Resin,
				'ListMatCsm'	=> $List_MatCsm,
				'ListMatKatalis'	=> $List_MatKatalis,
				'ListMatSm'			=> $List_MatSm,
				'ListMatCobalt'		=> $List_MatCobalt,
				'ListMatDma'		=> $List_MatDma,
				'ListMatHydo'		=> $List_MatHydo,
				'ListMatMethanol'	=> $List_MatMethanol,
				'ListMatAdditive'	=> $List_MatAdditive,
				'ListMatWR'			=> $List_MatWR,
				'ListMatRooving'	=> $List_MatRooving,
				'ListMatColor'		=> $List_MatColor,
				'ListMatTinuvin'	=> $List_MatTinuvin,
				'ListMatChl'		=> $List_MatChl,
				'ListMatStery'		=> $List_MatSm,
				'ListMatWax'		=> $List_MatWax,
				'ListMatMchl'		=> $List_MatMchl,
				
				
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
				
			$this->load->view('Deadstok_modif/estimasi_material_edit', $data);
		}
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

	public function delete(){
		$id 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$username = $data_session['ORI_User']['username'];
		$dateTime = date('Y-m-d H:i:s');
		
		$ArrPlant		= array(
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
		);

		$getdata = $this->db->get_where('deadstok_modif',array('kode'=>$id))->result_array();
		$ArrUpdate = [];
		$ArrUpdate2 = [];
		$ArrInsertToWIP = [];
		foreach ($getdata as $key => $value) {
			$ArrUpdate[$key]['id'] = $value['id_deadstok'];
			$ArrUpdate[$key]['id_booking'] = NULL;
			$ArrUpdate[$key]['process_next'] = NULL;
			$ArrUpdate[$key]['id_milik'] = NULL;
			$ArrUpdate[$key]['no_so'] = NULL;
			$ArrUpdate[$key]['no_spk'] = NULL;
			$ArrUpdate[$key]['no_ipp'] = NULL;

			$getdataPro = $this->db->get_where('production_detail',array('id_deadstok_dipakai'=>$value['id_deadstok']))->result_array();

			$ArrUpdate2[$key]['id'] = $getdataPro[0]['id'];
			$ArrUpdate2[$key]['id_deadstok_dipakai'] = NULL;
			$ArrUpdate2[$key]['lock_deadstok'] = 0;
			$ArrUpdate2[$key]['no_spk'] = NULL;
			$ArrUpdate2[$key]['product_code'] = NULL;
			$ArrUpdate2[$key]['upload_real'] = 'N';
			$ArrUpdate2[$key]['upload_real2'] = 'N';
			$ArrUpdate2[$key]['kode_spk'] = NULL;
			$ArrUpdate2[$key]['fg_date'] = NULL;
			$ArrUpdate2[$key]['closing_produksi_date'] = NULL;


			$getDataFG = $this->db->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro_det'=>$value['id_deadstok'],'jenis'=>'in deadstok'))->result_array();
			if(!empty($getDataFG)){
				$ArrInsertToWIP[$key]['tanggal'] = date('Y-m-d');
				$ArrInsertToWIP[$key]['keterangan'] = 'Finish Good to Deadstock';
				$ArrInsertToWIP[$key]['no_so'] = $getDataFG[0]['no_so'];
				$ArrInsertToWIP[$key]['product'] = $getDataFG[0]['product'];
				$ArrInsertToWIP[$key]['no_spk'] = $getDataFG[0]['no_spk'];
				$ArrInsertToWIP[$key]['kode_trans'] = 'deadstok';
				$ArrInsertToWIP[$key]['id_pro_det'] = $getDataFG[0]['id_pro_det'];
				$ArrInsertToWIP[$key]['qty'] = 1;
				$ArrInsertToWIP[$key]['nilai_wip'] = $getDataFG[0]['nilai_wip'];
				$ArrInsertToWIP[$key]['nilai_unit'] = $getDataFG[0]['nilai_unit'];
				$ArrInsertToWIP[$key]['material'] = 0;
				$ArrInsertToWIP[$key]['wip_direct'] =  0;
				$ArrInsertToWIP[$key]['wip_indirect'] =  0;
				$ArrInsertToWIP[$key]['wip_consumable'] =  0;
				$ArrInsertToWIP[$key]['wip_foh'] =  0;
				$ArrInsertToWIP[$key]['created_by'] = $username;
				$ArrInsertToWIP[$key]['created_date'] = $dateTime;
				$ArrInsertToWIP[$key]['id_trans'] =  $getDataFG[0]['id_trans'];
				$ArrInsertToWIP[$key]['id_pro'] =  $getDataFG[0]['id_pro'];
				$ArrInsertToWIP[$key]['jenis'] =  'out deadstok';
			}
		}

		// print_r($ArrUpdate);
		// print_r($ArrUpdate2);
		// print_r($ArrInsertToWIP);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('kode', $id); 
			$this->db->update('deadstok_modif', $ArrPlant); 

			if(!empty($ArrUpdate)){
				$this->db->update_batch('deadstok', $ArrUpdate, 'id');
			}
			if(!empty($ArrUpdate2)){
				$this->db->update_batch('production_detail', $ArrUpdate2, 'id');
			}
			if(!empty($ArrInsertToWIP)){
				$this->db->insert_batch('data_erp_fg',$ArrInsertToWIP);
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
			history('Delete deadstok modify : '.$id);
		}
		echo json_encode($Arr_Data);
	}

	public function approve(){
		$id 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$ArrPlant		= array(
			'status'		 	=> 'WAITING APPROVE',
			'approved_by' 		=> $data_session['ORI_User']['username'],
			'approved_date' 	=> date('Y-m-d H:i:s')
		);
		
		$this->db->trans_start();
			$this->db->where('kode', $id); 
			$this->db->update('deadstok_modif', $ArrPlant);
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
			history('Mengajukan deadstok modify : '.$id);
		}
		echo json_encode($Arr_Data);
	}

}