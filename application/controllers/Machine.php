<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('newipp_model');
		$this->load->model('app_engine_model');
		$this->load->model('bq_estimasi_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){ 
			redirect('login');
		}
	}
	
	//==========================================================================================================================
	//========================================================NEW IPP===========================================================
	//==========================================================================================================================
	
	public function new_ipp(){
		$this->newipp_model->index_new_ipp();
	}
	
	public function server_side_new_ipp(){
		$this->newipp_model->get_data_json_new_ipp();
	}
	
	//==========================================================================================================================
	//======================================================END NEW IPP=========================================================
	//==========================================================================================================================
	
	//==========================================================================================================================
	//======================================================APPROVE ENGINE======================================================
	//==========================================================================================================================
	//STRUCTURE BQ
	public function approve_bq(){
		$this->app_engine_model->index_app_bq();
	}
	
	public function server_side_app_bq(){
		$this->app_engine_model->get_json_app_bq();
	}
	
	public function approve_bq_modal(){
		$this->app_engine_model->approve_bq_modal();
	}
	
	//ESTIMASI
	public function approve_est(){
		$this->app_engine_model->index_app_est();
	}
	
	public function server_side_app_est(){
		$this->app_engine_model->get_json_app_est();
	}
	
	public function approve_est_modal(){
		$this->app_engine_model->approve_est_modal();
	}
	
	public function approve_est_excel(){
		$this->app_engine_model->approve_est_excel();
	}
	
	//==========================================================================================================================
	//======================================================END APPROVE ENGINE==================================================
	//==========================================================================================================================
	
	public function get_add(){
		$this->bq_estimasi_model->get_add();
	}

	public function get_add2(){
		$this->bq_estimasi_model->get_add2();
	}
	
	public function get_add3(){
		$this->bq_estimasi_model->get_add3();
	}

	public function get_add4(){
		$this->bq_estimasi_model->get_add4();
	}
	
	public function get_add4g(){
		$this->bq_estimasi_model->get_add4g();
	}

	public function get_add5(){
		$this->bq_estimasi_model->get_add5();
	}

	public function get_detail_lainnya(){
		$this->bq_estimasi_model->get_detail_lainnya();
	}

	public function get_detail_plate(){
		$this->bq_estimasi_model->get_detail_plate();
	}
	
	public function get_detail_baut(){
		$this->bq_estimasi_model->get_detail_baut();
	}
	
	public function get_detail_gasket(){
		$this->bq_estimasi_model->get_detail_gasket();
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
			'title'			=> 'Indeks Of Structure BQ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Structure BQ');
		$this->load->view('Machine/index',$data);
	}

	public function revisi_quo(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Structure BQ (Revised Quotation)',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Revisi Quotation BQ');
		$this->load->view('Machine/revisi_quo',$data);
	}

	public function ipp(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of IPP Machine',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data IPP Machine');
		$this->load->view('Machine/ipp',$data);
	}

	public function draf(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Draf Structure BQ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Draf Structure BQ');
		$this->load->view('Machine/draf',$data);
	}

	public function getDataJSONIPP(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONIPP(
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
			$nestedData[]	= "<div align='center'>".strtoupper(strtolower($row['no_ipp']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['nm_customer']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['ref_ke']))."</span></div>";
				$dataModif = (!empty($row['ref_ke']))?$row['modified_by']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($dataModif))."</div>";
				$warna = Color_status($row['status']);
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['status']."</span></div>";
				$updX = "";
				$delX = "";
				$PrintX	= "";
				// if($row['status'] == 'WAITING STRUCTURE BQ'){
					// if($Arr_Akses['update']=='1'){
						// $updX	= "<button class='btn btn-sm btn-primary' id='EditIPP' title='Edit IPP' data-no_ipp='".$row['no_ipp']."'><i class='fa fa-edit'></i></button>";
					// }
				// }
				if($Arr_Akses['download']=='1'){
					$PrintX	= "<a href='".site_url($this->uri->segment(1).'/printIPP/'.$row['no_ipp'])."' class='btn btn-sm btn-success' target='_blank' title='Print IPP' data-role='qtip'><i class='fa fa-print'></i></a>";
				}
			$nestedData[]	= "<div align='left'>
									<button type='button' id='detailSO' data-no_ipp='".$row['no_ipp']."' class='btn btn-sm btn-warning' title='View IPP' data-role='qtip'><i class='fa fa-eye'></i></button>
									".$PrintX."
									".$updX."
									".$delX."

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

	public function queryDataJSONIPP($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				*
			FROM
				production
		    WHERE deleted = 'N' AND (
				no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer'

		);

		$sql .= " ORDER BY no_ipp ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalDetailX(){
		$this->load->view('Machine/modalDetail');
	}

	public function modalEditIPP(){
		$this->load->view('Machine/modalEdit');
	}

	public function modalDetailMat(){
		$this->load->view('Machine/modalDetailMat2');
	}
	
	public function modalEditEstDefault(){
		$this->load->view('Machine_modal/modalEditEstDefault');
	}

	public function revisi(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$YM				= date('ym');

		$no_ipp			= $data['no_ipp'];
		$DetailSp		= $data['ListDetailEdit'];

		// print_r($DetailSp);
		// exit;

		$Data_Insert			= array(
			'project'			=> $data['project'],
			'note'				=> $data['note'],
			'ref_ke'			=> $data['ref_ke'] + 1,
			'modified_by'		=> $this->session->userdata['ORI_User']['username'],
			'modified_date'		=> date('Y-m-d H:i:s')
		);

		$Data_Shipping			= array(
			'date_delivery'		=> $data['date_delivery'],
			'address_delivery'	=> $data['address_delivery'],
			'country_code'		=> $data['country_code'],
			'metode_delivery'	=> $data['metode_delivery'],
			'alat_berat'		=> $data['alat_berat'],
			'isntalasi_by'		=> $data['isntalasi_by'],
			'packing'			=> $data['packing'],
			'garansi'			=> $data['garansi']
		);

		$ArrDetailPre	= array();
		foreach($DetailSp AS $val => $valx){
			//Fluida
			$qFluida			= "SELECT * FROM list_fluida WHERE id_fluida='".$valx['id_fluida']."' LIMIT 1";
			$NmLiner			= $this->db->query($qFluida)->result_array();

			$ArrDetailPre[$val]['id'] 			= $valx['id'];
			$ArrDetailPre[$val]['no_ipp'] 		= $no_ipp;
			$ArrDetailPre[$val]['product'] 		= $valx['product'];
			$ArrDetailPre[$val]['type_resin'] 	= $valx['type_resin'];
			$ArrDetailPre[$val]['time_life'] 	= $valx['time_life'];
			$ArrDetailPre[$val]['id_fluida'] 	= $valx['id_fluida'];
			$ArrDetailPre[$val]['liner_thick'] 	= $NmLiner[0]['liner_thick'];
			$ArrDetailPre[$val]['stifness'] 	= $valx['stifness'];
			$ArrDetailPre[$val]['aplikasi'] 	= $valx['aplikasi'];
			$ArrDetailPre[$val]['pressure'] 	= $valx['pressure'];
			$ArrDetailPre[$val]['vacum_rate'] 	= $valx['vacum_rate'];
			$ArrDetailPre[$val]['note'] 		= $valx['note'];
			$ArrDetailPre[$val]['std_asme'] 	= (!empty($valx['std_asme']))?'Y':'N';
			$ArrDetailPre[$val]['std_ansi'] 	= (!empty($valx['std_ansi']))?'Y':'N';
			$ArrDetailPre[$val]['std_astm'] 	= (!empty($valx['std_astm']))?'Y':'N';
			$ArrDetailPre[$val]['std_awwa'] 	= (!empty($valx['std_awwa']))?'Y':'N';
			$ArrDetailPre[$val]['std_bsi'] 		= (!empty($valx['std_bsi']))?'Y':'N';
			$ArrDetailPre[$val]['std_jis'] 		= (!empty($valx['std_jis']))?'Y':'N';
			$ArrDetailPre[$val]['std_sni'] 		= (!empty($valx['std_sni']))?'Y':'N';
			$ArrDetailPre[$val]['std_etc'] 		= (!empty($valx['std_etc']))?'Y':'N';
			$ArrDetailPre[$val]['etc_1'] 		= (!empty($valx['std_etc']))?$valx['etc_1']:'';
			$ArrDetailPre[$val]['etc_2'] 		= (!empty($valx['std_etc']))?$valx['etc_2']:'';
			$ArrDetailPre[$val]['etc_3'] 		= (!empty($valx['std_etc']))?$valx['etc_3']:'';
			$ArrDetailPre[$val]['etc_4'] 		= (!empty($valx['std_etc']))?$valx['etc_4']:'';
			$ArrDetailPre[$val]['document'] 	= (!empty($valx['document']))?'Y':'N';
			$ArrDetailPre[$val]['document_1'] 	= (!empty($valx['document']))?$valx['document_1']:'';
			$ArrDetailPre[$val]['document_2'] 	= (!empty($valx['document']))?$valx['document_2']:'';
			$ArrDetailPre[$val]['document_3'] 	= (!empty($valx['document']))?$valx['document_3']:'';
			$ArrDetailPre[$val]['document_4'] 	= (!empty($valx['document']))?$valx['document_4']:'';
			$ArrDetailPre[$val]['color'] 		= (!empty($valx['color']))?'Y':'N';
			$ArrDetailPre[$val]['color_liner'] 		= (!empty($valx['color']))?$valx['color_liner']:'';
			$ArrDetailPre[$val]['color_structure'] 	= (!empty($valx['color']))?$valx['color_structure']:'';
			$ArrDetailPre[$val]['color_external'] 	= (!empty($valx['color']))?$valx['color_external']:'';
			$ArrDetailPre[$val]['color_topcoat'] 	= (!empty($valx['color']))?$valx['color_topcoat']:'';
			$ArrDetailPre[$val]['test'] 			= (!empty($valx['test']))?'Y':'N';
			$ArrDetailPre[$val]['test_1'] 			= (!empty($valx['test']))?$valx['test_1']:'';
			$ArrDetailPre[$val]['test_2'] 			= (!empty($valx['test']))?$valx['test_2']:'';
			$ArrDetailPre[$val]['test_3'] 			= (!empty($valx['test']))?$valx['test_3']:'';
			$ArrDetailPre[$val]['test_4'] 			= (!empty($valx['test']))?$valx['test_4']:'';
			$ArrDetailPre[$val]['sertifikat'] 		= (!empty($valx['sertifikat']))?'Y':'N';
			$ArrDetailPre[$val]['sertifikat_1'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_1']:'';
			$ArrDetailPre[$val]['sertifikat_2'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_2']:'';
			$ArrDetailPre[$val]['sertifikat_3'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_3']:'';
			$ArrDetailPre[$val]['sertifikat_4'] 	= (!empty($valx['sertifikat']))?$valx['sertifikat_4']:'';
			$ArrDetailPre[$val]['abrasi'] 			= (!empty($valx['abrasi']))?'Y':'N';
			$ArrDetailPre[$val]['konduksi_liner'] 		= (!empty($valx['konduksi_liner']))?'Y':'N';
			$ArrDetailPre[$val]['konduksi_structure'] 	= (!empty($valx['konduksi_structure']))?'Y':'N';
			$ArrDetailPre[$val]['konduksi_eksternal'] 	= (!empty($valx['konduksi_eksternal']))?'Y':'N';
			$ArrDetailPre[$val]['konduksi_topcoat'] 	= (!empty($valx['konduksi_topcoat']))?'Y':'N';
			$ArrDetailPre[$val]['tahan_api_liner'] 		= (!empty($valx['tahan_api_liner']))?'Y':'N';
			$ArrDetailPre[$val]['tahan_api_structure'] 	= (!empty($valx['tahan_api_structure']))?'Y':'N';
			$ArrDetailPre[$val]['tahan_api_eksternal'] 	= (!empty($valx['tahan_api_eksternal']))?'Y':'N';
			$ArrDetailPre[$val]['tahan_api_topcoat'] 	= (!empty($valx['tahan_api_topcoat']))?'Y':'N';
			$ArrDetailPre[$val]['modified_by'] 			= $this->session->userdata['ORI_User']['username'];
			$ArrDetailPre[$val]['modified_date'] 		= date('Y-m-d H:i:s');
		}

		// print_r($ArrDetailPre);
		// exit;

		// echo "<pre>"; print_r($Data_Insert); print_r($Data_Shipping); print_r($ArrDetailPre);
		// exit;

		$this->db->trans_start();
		// $this->db->insert('production', $Data_Insert);
		// $this->db->insert('production_delivery', $Data_Shipping);
		// $this->db->insert_batch('production_req_sp', $ArrDetailPre);
		$this->db->query("INSERT hist_production (
										no_ipp,id_customer,nm_customer,project,note,status,status_reason,ref_ke,modified_by,modified_date
									)
									SELECT
										no_ipp,id_customer,nm_customer,project,note,status,status_reason,ref_ke,'".$this->session->userdata['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										production
									WHERE
										no_ipp = '".$no_ipp."'");

		$this->db->query("INSERT hist_production_delivery (
										no_ipp,date_delivery,address_delivery,country_code,metode_delivery,truck,
										vendor,qty_truck,metode_lain,alat_berat,isntalasi_by,garansi,
										packing,packing_fitting_qty,packing_dg_qty,packing_pipa_qty,
										modified_by,modified_date
									)
									SELECT
										no_ipp,date_delivery,address_delivery,country_code,metode_delivery,truck,
										vendor,qty_truck,metode_lain,alat_berat,isntalasi_by,garansi,
										packing,packing_fitting_qty,packing_dg_qty,packing_pipa_qty,
										'".$this->session->userdata['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									FROM
										production_delivery
									WHERE
										no_ipp = '".$no_ipp."'");
		$this->db->insert_batch('hist_production_req_sp', $ArrDetailPre);

		$this->db->where('no_ipp', $no_ipp)->update('production', $Data_Insert);
		$this->db->where('no_ipp', $no_ipp)->update('production_delivery', $Data_Shipping);
		$this->db->update_batch('production_req_sp', $ArrDetailPre, 'id');

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit request data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit request data success. Thanks ...',
				'status'	=> 1
			);
			history("Edit Request IPP ".$no_ipp." by ENG");
		}

		echo json_encode($Arr_Kembali);
	}

	public function printIPP(){
		$no_ipp			= $this->uri->segment(3);

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();

		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print IPP Production by Machine '.$no_ipp);

		PrintIPP($Nama_Beda, $no_ipp, $koneksi, $printby);
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
			$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND id_category <> 'pipe slongsong' AND (id_product= '' OR id_product  is null) ")->num_rows();

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
			$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			if($row['sts_ipp'] != 'WAITING STRUCTURE BQ'){ 
				$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			}
			else{
				$nestedData[]	= "<div align='left'><a id='edit_series' data-id_bq='".$row['id_bq']."' style='cursor: pointer;' title='Change Series'>".$dtImplode."</a></div>";
			}
			$get_rev_est = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_costing_header',array('id_bq'=>$row['id_bq']))->result();
			$rev_est = (!empty($get_rev_est[0]->revised))?$get_rev_est[0]->revised:0;
			
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".$rev_est."</span></div>";
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($row['created_by']))."</div>";
				$dataModifx = (!empty($row['modified_date']))?$row['modified_date']:$row['created_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($dataModifx))."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reason_approved']))."</div>";

				$class = Color_status($row['sts_ipp']);

			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					$app	= "";
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					// if($Arr_Akses['delete']=='1'){
						// $delX	= "&nbsp;<button class='btn btn-sm btn-danger' id='batalProduksi' title='Cancel Production' data-id_bq='".$row['id_bq']."'><i class='fa fa-trash'></i></button>";
					// }
					
					
					if($row['aju_approved'] == 'N'){
						$app	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='ajuAppBQ' title='Ajukan BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						
						if($Arr_Akses['update']=='1'){
							if($row['sts_ipp'] == 'WAITING STRUCTURE BQ'){
								$updX	= "&nbsp;<button class='btn btn-sm btn-primary' id='editBQ' title='Edit BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-edit'></i></button>";
							}
						}
					}
			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$priX."
									".$updX."
									".$delX."
									".$app."
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.nm_customer,
				b.project,
				b.status AS sts_ipp
			FROM
				bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
		    WHERE b.ref_quo = 0 AND  (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'no_ipp'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function ajukanAppBQ(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$no_ipp 		= str_replace('BQ-', '', $id_bq);
		
		$Arr_Edit	= array(
			'aju_approved' 		=> 'Y',
			'aju_approved_by' 	=> $data_session['ORI_User']['username'],
			'aju_approved_date' => date('Y-m-d H:i:s')
		);
		$Arr_Edit2	= array(
			'status' => "WAITING APPROVE STRUCTURE BQ"
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $Arr_Edit2);
			
			$this->db->where('id_bq', $id_bq);
			$this->db->update('bq_header', $Arr_Edit);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve success. Thanks ...',
				'status'	=> 1
			);				
			history('Ajukan Structure BQ with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function AppBQ(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$no_ipp 		= str_replace('BQ-', '', $id_bq);
		
		$Arr_Edit	= array(
			'approved' 		=> 'Y',
			'approved_by' 	=> $data_session['ORI_User']['username'],
			'approved_date' => date('Y-m-d H:i:s')
		);
		$Arr_Edit2	= array(
			'status' => "WAITING ESTIMATION PROJECT"
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $Arr_Edit2);
			
			$this->db->where('id_bq', $id_bq);
			$this->db->update('bq_header', $Arr_Edit);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve success. Thanks ...',
				'status'	=> 1
			);				
			history('Approve Structure BQ with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function AppBQNew(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$no_ipp 		= str_replace('BQ-', '', $id_bq);
		
		$status		= $this->input->post('status');
		$reason		= $this->input->post('approve_reason');
		
		if($status == 'Y'){
			$Arr_Edit2	= array(
				'status' => "WAITING ESTIMATION PROJECT"
			);
			$Arr_Edit	= array(
				'approved' 		=> 'Y',
				'approved_by' 	=> $data_session['ORI_User']['username'],
				'approved_date' => date('Y-m-d H:i:s')
			);
			$HistReason	= 'Approve Structure BQ with BQ : '.$id_bq;
		}
		
		if($status == 'N'){
			$Arr_Edit2	= array(
				'status' => "WAITING STRUCTURE BQ"
			);
			$Arr_Edit	= array(
				'reason_approved'	=> $reason,
				'aju_approved' 		=> 'N',
				'aju_approved_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_date' => date('Y-m-d H:i:s')
			);
			$HistReason	= 'Reject Structure BQ with BQ : '.$id_bq;
		}
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $Arr_Edit2);
			
			$this->db->where('id_bq', $id_bq);
			$this->db->update('bq_header', $Arr_Edit);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve process success. Thanks ...',
				'status'	=> 1
			);				
			history($HistReason);
		}
		echo json_encode($Arr_Data);
	}
	
	public function ajukanAppBQEst(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$no_ipp 		= str_replace('BQ-', '', $id_bq);
		
		// $sql = "SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND (man_power <= 0 OR total_time <= 0 OR man_hours <= 0 OR man_power IS NULL OR total_time IS NULL OR man_hours IS NULL ) ";
		// $cek = $this->db->query($sql)->num_rows();
		// if($cek < 1){
			$Arr_Edit	= array(
				'aju_approved_est' 		=> 'Y',
				'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_est_date' => date('Y-m-d H:i:s')
			);
			
			$Arr_Edit2	= array(
				'status' => "WAITING APPROVE EST PROJECT"
			);
			// print_r($Arr_Edit);
			// exit;
			$this->db->trans_start();
				$this->db->where('no_ipp', $no_ipp);
				$this->db->update('production', $Arr_Edit2);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Approve failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Approve success. Thanks ...',
					'status'	=> 1
				);				
				history('Ajukan Estimasi BQ with BQ : '.$id_bq);
			}
		// }
		// else{
			// $Arr_Data	= array(
				// 'pesan'		=>'Cyletime atau Man Power tidak terisi !!!',
				// 'status'	=> 0
			// );
		// }
		echo json_encode($Arr_Data);
	}
	
	public function AppBQEst(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'approved_est' 		=> 'Y',
			'approved_est_by' 	=> $data_session['ORI_User']['username'],
			'approved_est_date' => date('Y-m-d H:i:s')
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('id_bq', $id_bq);
			$this->db->update('bq_header', $Arr_Edit);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Approve failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Approve success. Thanks ...',
				'status'	=> 1
			);				
			history('Approve Estimasi BQ with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	public function AppBQEstNew(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$no_ipp 		= str_replace('BQ-', '', $id_bq);
		
			
			$status		= $this->input->post('status');
			$reason		= $this->input->post('approve_reason');
			
			if($status == 'Y'){
				$sql = "SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id_category <> 'product kosong' AND (man_power <= 0 OR man_hours <= 0 OR man_power IS NULL OR man_hours IS NULL ) ";
				$cek = $this->db->query($sql)->num_rows();
				// echo $cek; exit;
				if($cek < 1){
					$Arr_Edit	= array(
						'approved_est' 		=> 'Y',
						'approved_est_by' 	=> $data_session['ORI_User']['username'],
						'approved_est_date' => date('Y-m-d H:i:s')
					);
					$Arr_Edit2	= array(
						'status' => "WAITING EST PRICE PROJECT"
					);
					$HistReason	= 'Approve Estimation BQ with BQ : '.$id_bq;
					
					//Add Tambahan Revisi
					$sqlNoRev 	= "SELECT revised_no FROM laporan_costing_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
					$restNoRev 	= $this->db->query($sqlNoRev)->result_array();
					$restNumRev = $this->db->query($sqlNoRev)->num_rows();

					if($restNumRev > 0){
						$revised_no = $restNoRev[0]['revised_no'] + 1;
					}
					else{
						$revised_no = 0;
					}
					
					$sqlRevised 	= SQL_Revised_Costing($id_bq);
					$restRevised 	= $this->db->query($sqlRevised)->result_array();
					$ArrDetRevised 	= array();
					$SUM_est_material 				= 0;
					$SUM_est_harga 					= 0;
					$SUM_direct_labour 				= 0;
					$SUM_indirect_labour 			= 0;
					$SUM_machine 					= 0;
					$SUM_mould_mandrill 			= 0;
					$SUM_consumable	 				= 0;
					$SUM_foh_consumable 			= 0;
					$SUM_foh_depresiasi 			= 0;
					$SUM_biaya_gaji_non_produksi 	= 0;
					$SUM_biaya_non_produksi 		= 0;
					$SUM_biaya_rutin_bulanan 		= 0;
					$SUM_PROJECT 					= 0;
					if(!empty($restRevised)){
						foreach($restRevised AS $val => $valx){
							$SUM_est_material 				+= $valx['est_material'];
							$SUM_est_harga 					+= $valx['est_harga'];
							$SUM_direct_labour 				+= $valx['direct_labour'];
							$SUM_indirect_labour 			+= $valx['indirect_labour'];
							$SUM_machine 					+= $valx['machine'];
							$SUM_mould_mandrill 			+= $valx['mould_mandrill'];
							$SUM_consumable 				+= $valx['consumable'];
							$SUM_foh_consumable 			+= $valx['foh_consumable'];
							$SUM_foh_depresiasi 			+= $valx['foh_depresiasi'];
							$SUM_biaya_gaji_non_produksi 	+= $valx['biaya_gaji_non_produksi'];
							$SUM_biaya_non_produksi 		+= $valx['biaya_non_produksi'];
							$SUM_biaya_rutin_bulanan 		+= $valx['biaya_rutin_bulanan'];

							$sqlTambahan 	= "SELECT `length`, thickness, sudut, id_standard, `type` FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id = '".$valx['id_milik']."' LIMIT 1 ";
							$restTambahan 	= $this->db->query($sqlTambahan)->result_array();

							$ArrDetRevised[$val]['id_bq'] = $valx['id_bq'];
							$ArrDetRevised[$val]['id_milik'] = $valx['id_milik'];
							$ArrDetRevised[$val]['product_parent'] = $valx['parent_product'];
							$ArrDetRevised[$val]['id_product'] = $valx['id_product'];
							$ArrDetRevised[$val]['series'] = $valx['series'];
							$ArrDetRevised[$val]['diameter'] = $valx['diameter'];
							$ArrDetRevised[$val]['diameter2'] = $valx['diameter2'];
							$ArrDetRevised[$val]['length'] = $restTambahan[0]['length'];
							$ArrDetRevised[$val]['thickness'] = $restTambahan[0]['thickness'];
							$ArrDetRevised[$val]['sudut'] = $restTambahan[0]['sudut'];
							$ArrDetRevised[$val]['id_standard'] = $restTambahan[0]['id_standard'];
							$ArrDetRevised[$val]['type'] = $restTambahan[0]['type'];
							$ArrDetRevised[$val]['pressure'] = $valx['pressure'];
							$ArrDetRevised[$val]['liner'] = $valx['liner'];
							$ArrDetRevised[$val]['qty'] = $valx['qty'];
							$ArrDetRevised[$val]['est_material'] = $valx['est_material'];
							$ArrDetRevised[$val]['est_harga'] = $valx['est_harga'];
							$ArrDetRevised[$val]['direct_labour'] = $valx['direct_labour'];
							$ArrDetRevised[$val]['indirect_labour'] = $valx['indirect_labour'];
							$ArrDetRevised[$val]['machine'] = $valx['machine'];
							$ArrDetRevised[$val]['mould_mandrill'] = $valx['mould_mandrill'];
							$ArrDetRevised[$val]['consumable'] = $valx['consumable'];
							$ArrDetRevised[$val]['foh_consumable'] = $valx['foh_consumable'];
							$ArrDetRevised[$val]['foh_depresiasi'] = $valx['foh_depresiasi'];
							$ArrDetRevised[$val]['biaya_gaji_non_produksi'] = $valx['biaya_gaji_non_produksi'];
							$ArrDetRevised[$val]['biaya_non_produksi'] = $valx['biaya_non_produksi'];
							$ArrDetRevised[$val]['biaya_rutin_bulanan'] = $valx['biaya_rutin_bulanan'];
								$unitPriceX = ($valx['est_harga']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan']) / $valx['qty'];
							$ArrDetRevised[$val]['unit_price'] = $unitPriceX;
							$ArrDetRevised[$val]['profit'] = 0;
							$ArrDetRevised[$val]['total_price'] = 0;
							$ArrDetRevised[$val]['allowance'] = 0;
								$SUM_PROJECT += $unitPriceX * $valx['qty'];
							$ArrDetRevised[$val]['total_price_last'] = $unitPriceX * $valx['qty'];
							$ArrDetRevised[$val]['man_power'] = $valx['man_power'];
							$ArrDetRevised[$val]['id_mesin'] = $valx['id_mesin'];
							$ArrDetRevised[$val]['total_time'] = $valx['total_time'];
							$ArrDetRevised[$val]['man_hours'] = $valx['man_hours'];
							$ArrDetRevised[$val]['pe_direct_labour'] = $valx['pe_direct_labour'];
							$ArrDetRevised[$val]['pe_indirect_labour'] = $valx['pe_indirect_labour'];
							$ArrDetRevised[$val]['pe_machine'] = $valx['pe_machine'];
							$ArrDetRevised[$val]['pe_mould_mandrill'] = $valx['pe_mould_mandrill'];
							$ArrDetRevised[$val]['pe_consumable'] = $valx['pe_consumable'];
							$ArrDetRevised[$val]['pe_foh_consumable'] = $valx['pe_foh_consumable'];
							$ArrDetRevised[$val]['pe_foh_depresiasi'] = $valx['pe_foh_depresiasi'];
							$ArrDetRevised[$val]['pe_biaya_gaji_non_produksi'] = $valx['pe_biaya_gaji_non_produksi'];
							$ArrDetRevised[$val]['pe_biaya_non_produksi'] = $valx['pe_biaya_non_produksi'];
							$ArrDetRevised[$val]['pe_biaya_rutin_bulanan'] = $valx['pe_biaya_rutin_bulanan'];
							$ArrDetRevised[$val]['revised_no'] = $revised_no;
							$ArrDetRevised[$val]['insert_by'] = $data_session['ORI_User']['username'];
							$ArrDetRevised[$val]['insert_date'] = date('Y-m-d H:i:s');
						}
					}
					
					//Insert Header Report Etc
					$restRevisedEtc 	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq))->result_array();
					$ArrEtcRevised = array();
					if(!empty($restRevisedEtc)){
						foreach($restRevisedEtc AS $val => $valx){
							$SUM_PROJECT += $valx['total_price'];
							$ArrEtcRevised[$val]['id_bq'] = $valx['id_bq'];
							$ArrEtcRevised[$val]['category'] = $valx['category'];
							$ArrEtcRevised[$val]['id_material'] = $valx['id_material'];
							$ArrEtcRevised[$val]['note'] = $valx['note'];
							$ArrEtcRevised[$val]['lebar'] = $valx['lebar'];
							$ArrEtcRevised[$val]['panjang'] = $valx['panjang'];
							$ArrEtcRevised[$val]['berat'] = $valx['berat'];
							$ArrEtcRevised[$val]['unit'] = $valx['satuan'];
							$ArrEtcRevised[$val]['qty'] = $valx['qty'];
							$ArrEtcRevised[$val]['sheet'] = $valx['sheet'];
							$ArrEtcRevised[$val]['price'] = $valx['unit_price'];
							$ArrEtcRevised[$val]['price_total'] = $valx['total_price'];
							$ArrEtcRevised[$val]['revised_no'] = $revised_no;
							$ArrEtcRevised[$val]['insert_by'] = $data_session['ORI_User']['username'];
							$ArrEtcRevised[$val]['insert_date'] = date('Y-m-d H:i:s');
						}
					}
					
					//Insert Header Report Revised
					$restRevisedHead 	= $this->db->select('id_customer, nm_customer, project')->get_where('production', array('no_ipp'=>str_replace('BQ-','',$id_bq)))->result_array();
					
					$ArrHeadRevised = array(
						'id_bq' => $id_bq,
						'id_customer' => $restRevisedHead[0]['id_customer'],
						'nm_customer' => $restRevisedHead[0]['nm_customer'],
						'nm_project' => $restRevisedHead[0]['project'],
						'revised_no' => $revised_no,
						'perubahan' => strtolower($this->input->post('perubahan')),
						'price_project' => $SUM_PROJECT,
						'est_material' => $SUM_est_material,
						'est_harga' => $SUM_est_harga,
						'direct_labour' => $SUM_direct_labour,
						'indirect_labour' => $SUM_indirect_labour,
						'machine' => $SUM_machine,
						'mould_mandrill' => $SUM_mould_mandrill,
						'consumable' => $SUM_consumable,
						'foh_consumable' => $SUM_foh_consumable,
						'foh_depresiasi' => $SUM_foh_depresiasi,
						'biaya_gaji_non_produksi' => $SUM_biaya_gaji_non_produksi,
						'biaya_non_produksi' => $SUM_biaya_non_produksi,
						'biaya_rutin_bulanan' => $SUM_biaya_rutin_bulanan,
						'insert_by' => $data_session['ORI_User']['username'],
						'insert_date' => date('Y-m-d H:i:s')
					);
					// echo "<pre>";
					// print_r($ArrHeadRevised);
					// print_r($ArrDetRevised);
					// print_r($ArrEtcRevised);
					// exit;
					
					
				}
				elseif($cek > 0){
					$Arr_Data	= array(
						'pesan'		=>'Cyletime atau Man Power tidak terisi !!!',
						'status'	=> 0
					);
					echo json_encode($Arr_Data);
					return false;
				}
			}
			
			if($status == 'N'){
				$Arr_Edit	= array(
					'reason_approved_est'	=> $reason,
					'approved_est' 		=> 'N',
					'approved_est_by' 	=> $data_session['ORI_User']['username'],
					'approved_est_date' => date('Y-m-d H:i:s'),
					'aju_approved_est' 		=> 'N',
					'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
					'aju_approved_est_date' => date('Y-m-d H:i:s')
				);
				$Arr_Edit2	= array(
					'status' => "WAITING ESTIMATION PROJECT"
				);
				$HistReason	= 'Reject Estimation BQ To Est with BQ : '.$id_bq;
			}
			
			if($status == 'M'){
				$Arr_Edit	= array(
					'reason_approved_est'	=> $reason,
					'aju_approved_est' 		=> 'N',
					'aju_approved' 			=> 'N',
					'approved_est' 			=> 'N',
					'approved' 				=> 'N',
					'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
					'aju_approved_est_date' => date('Y-m-d H:i:s')
				);
				$Arr_Edit2	= array(
					'status' => "WAITING STRUCTURE BQ"
				);
				$HistReason	= 'Reject Estimation BQ To Bq with BQ : '.$id_bq;
			}
			// print_r($Arr_Edit);
			// exit;
			$this->db->trans_start();
				$this->db->where('no_ipp', $no_ipp);
				$this->db->update('production', $Arr_Edit2);
				
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $Arr_Edit);
				
				if($status == 'Y'){
					$this->db->insert('laporan_costing_header', $ArrHeadRevised);
					if(!empty($ArrDetRevised)){
						$this->db->insert_batch('laporan_costing_detail', $ArrDetRevised);
					}
					if(!empty($ArrEtcRevised)){
						$this->db->insert_batch('laporan_costing_etc', $ArrEtcRevised);
					}
				}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Approve process failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Approve process success. Thanks ...', 
					'status'	=> 1
				);				
				history($HistReason);
			}
		
		echo json_encode($Arr_Data);
	}

	public function getDataJSONRev(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/revisi_quo";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONRev(
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
			
			$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND id_category <> 'pipe slongsong' AND (id_product= '' OR id_product  is null) ")->num_rows();


			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
			
			$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			// $nestedData[]	= "<div align='left'><a id='edit_series' data-id_bq='".$row['id_bq']."' style='cursor: pointer;'>".$dtImplode."</a></div>";
			// if($row['estimasi'] == 'Y'){
					if($row['sts_ipp'] != 'WAITING STRUCTURE BQ'){
						$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
					}
					else{
						$nestedData[]	= "<div align='left'><a id='edit_series' data-id_bq='".$row['id_bq']."' style='cursor: pointer;' title='Change Series'>".$dtImplode."</a></div>";
					}
			// }
			// else if($row['estimasi'] == 'Y' AND $Check > 0){
				// $nestedData[]	= "<div align='left'><a id='edit_series' data-id_bq='".$row['id_bq']."' style='cursor: pointer;' title='Change Series'>".$dtImplode."</a></div>";
			// }
			// else{
				// $nestedData[]	= "<div align='left'>".$dtImplode."</div>";
				// $nestedData[]	= "<div align='left'><a id='edit_series' data-id_bq='".$row['id_bq']."' style='cursor: pointer;' title='Change Series'>".$dtImplode."</a></div>";
			// }
			$get_rev_est = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_costing_header',array('id_bq'=>$row['id_bq']))->result();
			$rev_est = (!empty($get_rev_est[0]->revised))?$get_rev_est[0]->revised:0;
			
			
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".$rev_est."</span></div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['created_by']))."</div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['quo_reason']))."</div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND (id_product= '' OR id_product  is null) ")->num_rows();
				
				$warna = Color_status($row['sts_ipp']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					$app	= "";
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					// if($Arr_Akses['delete']=='1'){
						// $delX	= "&nbsp;<button class='btn btn-sm btn-danger' id='batalProduksi' title='Cancel Production' data-id_bq='".$row['id_bq']."'><i class='fa fa-trash'></i></button>";
					// }
					
					
					if($row['aju_approved'] == 'N'){
						if($Arr_Akses['approve']=='1'){
							$app	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='ajuAppBQ' title='Ajukan BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						}
						if($Arr_Akses['update']=='1'){
							if($row['sts_ipp'] == 'WAITING STRUCTURE BQ'){
								$updX	= "&nbsp;<button class='btn btn-sm btn-primary' id='editBQ' title='Edit BQ' data-id_bq='".$row['id_bq']."' data-ciri='revisi_quo'><i class='fa fa-edit'></i></button>";
							}
						}
					}
			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$priX."
									".$updX."
									".$delX."
									".$app."
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

	public function queryDataJSONRev($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.nm_customer,
				b.project,
				b.ref_quo,
				b.quo_reason,
				b.status AS sts_ipp
			FROM
				bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
		    WHERE b.ref_quo > 0 AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'no_ipp'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//Draf
	public function getDataJSONDraf(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/draf";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDraf(
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
			$nestedData[]	= "<div align='center'>".$row['id_bq']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['project']))."</div>";
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($row['created_by']))."</div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					// if($Arr_Akses['delete']=='1'){
						// $delX	= "&nbsp;<button class='btn btn-sm btn-danger' id='batalProduksi' title='Cancel Production' data-id_bq='".$row['id_bq']."'><i class='fa fa-trash'></i></button>";
					// }
					$count_bq = $this->db->query("SELECT * FROM bq_header WHERE id_bq='".$row['id_bq']."'")->num_rows();
					if($count_bq < 1){
						if($Arr_Akses['update']=='1'){
							$updX	= "&nbsp;<button class='btn btn-sm btn-success' id='editBQ' title='Edit BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-edit'></i></button>";
						}
					}
			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$priX."
									".$updX."
									".$delX."
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

	public function queryDataJSONDraf($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.nm_customer,
				b.project
			FROM
				draf_bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
		    WHERE (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'no_ipp'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalSeries(){
		$this->load->view('Machine/modalSeries');
	}

	public function modalDetailEst(){
		$this->load->view('Machine/modalDetailEst');
	}

	public function modalEditBQ(){
		$this->load->view('Machine/modalEditBQ');
	}

	public function modalEditBQDraf(){
		$this->load->view('Machine/modalEditBQDraf');
	}

	public function modalEstBQ(){
		$id_bq = $this->uri->segment(3);
		$app_est = $this->uri->segment(4);
		
		$sqlResin = "(SELECT id_material, nm_material, id_category  FROM bq_component_detail WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)
			 UNION
			(SELECT id_material, nm_material, id_category  FROM bq_component_detail_plus WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)";
		$ListBQipp		= $this->db->query($sqlResin)->result_array();
		$dtListArrayResin = array();
		$dtListArrayVeil = array();
		$dtListArrayCsm = array();
		$dtListArrayWR = array();
		$dtListArrayRooving = array();
		$dtListArrayCatalys = array();
		$dtListArrayPigment = array();
		foreach($ListBQipp AS $val => $valx){
			if($valx['id_category'] == 'TYP-0001'){
				$dtListArrayResin[$val] = $valx['nm_material'];
			}
			if($valx['id_category'] == 'TYP-0003'){
				$dtListArrayVeil[$val] = $valx['nm_material'];
			}
			if($valx['id_category'] == 'TYP-0004'){
				$dtListArrayCsm[$val] = $valx['nm_material'];
			}
			if($valx['id_category'] == 'TYP-0006'){
				$dtListArrayWR[$val] = $valx['nm_material'];
			}
			if($valx['id_category'] == 'TYP-0005'){
				$dtListArrayRooving[$val] = $valx['nm_material'];
			}
			if($valx['id_category'] == 'TYP-0002'){
				$dtListArrayCatalys[$val] = $valx['nm_material'];
			}
			if($valx['id_category'] == 'TYP-0007'){
				$dtListArrayPigment[$val] = $valx['nm_material'];
			}
		}
		$dtImplodeResin	= "".implode("  ---  ", $dtListArrayResin)."";
		$dtImplodeVeil	= "".implode("  ---  ", $dtListArrayVeil)."";
		$dtImplodeCsm	= "".implode("  ---  ", $dtListArrayCsm)."";
		$dtImplodeWR	= "".implode("  ---  ", $dtListArrayWR)."";
		$dtImplodeRooving	= "".implode("  ---  ", $dtListArrayRooving)."";
		$dtImplodeCatalys	= "".implode("  ---  ", $dtListArrayCatalys)."";
		$dtImplodePigment	= "".implode("  ---  ", $dtListArrayPigment)."";
		
		$arrWhereIn = array('TYP-0001','TYP-0003','TYP-0004','TYP-0005','TYP-0006','TYP-0002','TYP-0007');
		$ListCategory = $this->db->select('id_category, category')->from('raw_categories')->where_in('id_category',$arrWhereIn)->get()->result_array();

		$data = [
			'id_bq' => $id_bq,
			'app_est' => $app_est,
			'ListCategory' => $ListCategory,
			'listResin' => $dtImplodeResin,
			'countResin' => $dtListArrayResin,
			'listVeil' => $dtImplodeVeil,
			'countVeil' => $dtListArrayVeil,
			'listCsm' => $dtImplodeCsm,
			'countCsm' => $dtListArrayCsm,
			
			'listWR' => $dtImplodeWR,
			'countWR' => $dtListArrayWR,
			
			'listRooving' => $dtImplodeRooving,
			'countRooving' => $dtListArrayRooving,
			
			'listCatalys' => $dtImplodeCatalys,
			'countCatalys' => $dtListArrayCatalys,
			
			'listPigment' => $dtImplodePigment,
			'countPigment' => $dtListArrayPigment,
		];

		$this->load->view('Machine/modalEstBQ',$data);
	}
	
	public function get_material($id){
		$sqlSup		= "SELECT id_material, nm_material FROM raw_materials WHERE `delete` ='N' AND id_category='".$id."' AND flag_active = 'Y' ORDER BY nm_material ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select Material</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option, 
			'nama' => get_name('raw_categories','category','id_category',$id)
		);
		echo json_encode($ArrJson);
	}

	public function modalDetailBQ(){
		$id_bq = $this->uri->segment(3);

		$sql 	= "SELECT * FROM bq_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id ASC";
		$result		= $this->db->query($sql)->result_array();
		
		$data = array(
			'result' => $result
		);
		$this->load->view('Machine/modalDetailBQ', $data);
	}

	public function modalAppBQ(){
		$this->load->view('Machine/modalApprove_bq');
	}

	public function modalDetailBQDraf(){
		$this->load->view('Machine/modalDetailBQDraf');
	}

	public function modalviewDT(){
		$this->load->view('Machine/modalviewDT');
	}

	public function modalDetailDT(){
		$this->load->view('Machine/modalDetailDT');
	}

	public function modalEst(){
		$this->load->view('Machine/modalEst');
	}
	
	//NEWADD
	public function updateBQNew(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$detailBQ		= $data['detailBQ'];
		
		

		// $id_bq			= $data['id_bq'];
		$id_bq			= "BQ-".$data['no_ipp'];
		$no_ipp			= $data['no_ipp'];
		$pembeda		= $data['pembeda'];
		
		// print_r($no_ipp);
		// exit;
		
		$ArrDetBq		= array();
		foreach($detailBQ AS $val => $valx){
			if(!empty($valx["id_productx"]))
			{
				$ArrDetBq[$val]['id']	= $valx['id'];
				$ArrDetBq[$val]['id_product']	= $valx['id_productx'];
				$ArrDetBq[$val]['panjang']	= $valx['panjang'];
			}
		}

		$ArrDetBq2		= array();
		foreach($detailBQ AS $val => $valx){
			if(!empty($valx["id_productx"]))
			{
				$ArrDetBq2[$val]['id']	= $valx['id'];
				$ArrDetBq2[$val]['id_product']	= $valx['id_productx'];
			}
		}

		//print_r($valx);
		// print_r($ArrDetBq2);
		//exit;
		// $ArrHeader 		= array_unique(array_column($ArrDetBq, "id_product"));
		// echo "<pre>";

		$ArrBqHeader			= array();
		$ArrBqDetail			= array();
		$ArrBqDetailPlus		= array();
		$ArrBqDetailAdd			= array();
		$ArrBqFooter			= array();
		$ArrBqHeaderHist		= array();
		$ArrBqDetailHist		= array();
		$ArrBqDetailPlusHist	= array();
		$ArrBqDetailAddHist		= array();
		$ArrBqFooterHist		= array();
		$ArrBqDefault			= array();
		$ArrBqDefaultHist		= array();

		// print_r($ArrBqFooter);
		// exit;
		$LoopDetail = 0;
		$LoopDetailLam = 0;
		$LoopDetailPlus = 0;
		$LoopDetailAdd = 0;
		$LoopFooter = 0;
		foreach($ArrDetBq AS $val => $valx){
			//Component Header
			$qHeader	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$valx['id_product']."' LIMIT 1 ")->result();
			$ArrBqHeader[$val]['id_product']			= $valx['id_product'];
			$ArrBqHeader[$val]['id_bq']					= $id_bq;
			$ArrBqHeader[$val]['id_milik']				= $valx['id'];
			$ArrBqHeader[$val]['parent_product']		= $qHeader[0]->parent_product;
			$ArrBqHeader[$val]['nm_product']			= $qHeader[0]->nm_product;
			$ArrBqHeader[$val]['standart_code']			= $qHeader[0]->standart_code;
			$ArrBqHeader[$val]['series']				= $qHeader[0]->series;
			$ArrBqHeader[$val]['resin_sistem']			= $qHeader[0]->resin_sistem;
			$ArrBqHeader[$val]['pressure']				= $qHeader[0]->pressure;
			$ArrBqHeader[$val]['diameter']				= $qHeader[0]->diameter;
			$ArrBqHeader[$val]['liner']					= $qHeader[0]->liner;
			$ArrBqHeader[$val]['aplikasi_product']		= $qHeader[0]->aplikasi_product;
			$ArrBqHeader[$val]['criminal_barier']		= $qHeader[0]->criminal_barier;
			$ArrBqHeader[$val]['vacum_rate']			= $qHeader[0]->vacum_rate;
			$ArrBqHeader[$val]['stiffness']				= $qHeader[0]->stiffness;
			$ArrBqHeader[$val]['design_life']			= $qHeader[0]->design_life;
			$ArrBqHeader[$val]['standart_by']			= $qHeader[0]->standart_by;
			$ArrBqHeader[$val]['standart_toleransi']	= $qHeader[0]->standart_toleransi;
			$ArrBqHeader[$val]['diameter2']				= $qHeader[0]->diameter2;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader[$val]['panjang']			= floatval($valx['panjang']) + 400;
			}
			else{
				$ArrBqHeader[$val]['panjang']			= $qHeader[0]->panjang;
			}
			$ArrBqHeader[$val]['radius']				= $qHeader[0]->radius;
			$ArrBqHeader[$val]['type_elbow']			= $qHeader[0]->type_elbow;
			$ArrBqHeader[$val]['angle']					= $qHeader[0]->angle;
			$ArrBqHeader[$val]['design']				= $qHeader[0]->design;
			$ArrBqHeader[$val]['est']					= $qHeader[0]->est;
			$ArrBqHeader[$val]['min_toleransi']			= $qHeader[0]->min_toleransi;
			$ArrBqHeader[$val]['max_toleransi']			= $qHeader[0]->max_toleransi;
			$ArrBqHeader[$val]['waste']					= $qHeader[0]->waste;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader[$val]['area']				= (floatval($qHeader[0]->area) / floatval($qHeader[0]->panjang)) * (floatval($valx['panjang']) + 400);
			}
			else{
				$ArrBqHeader[$val]['area']				= $qHeader[0]->area;
			}
			$ArrBqHeader[$val]['wrap_length']		= $qHeader[0]->wrap_length;
			$ArrBqHeader[$val]['wrap_length2']		= $qHeader[0]->wrap_length2;
			$ArrBqHeader[$val]['high']				= $qHeader[0]->high;
			$ArrBqHeader[$val]['area2']				= $qHeader[0]->area2;
			$ArrBqHeader[$val]['panjang_neck_1']	= $qHeader[0]->panjang_neck_1;
			$ArrBqHeader[$val]['panjang_neck_2']	= $qHeader[0]->panjang_neck_2;
			$ArrBqHeader[$val]['design_neck_1']		= $qHeader[0]->design_neck_1;
			$ArrBqHeader[$val]['design_neck_2']		= $qHeader[0]->design_neck_2;
			$ArrBqHeader[$val]['est_neck_1']		= $qHeader[0]->est_neck_1;
			$ArrBqHeader[$val]['est_neck_2']		= $qHeader[0]->est_neck_2;
			$ArrBqHeader[$val]['area_neck_1']		= $qHeader[0]->area_neck_1;
			$ArrBqHeader[$val]['area_neck_2']		= $qHeader[0]->area_neck_2;
			$ArrBqHeader[$val]['flange_od']			= $qHeader[0]->flange_od;
			$ArrBqHeader[$val]['flange_bcd']		= $qHeader[0]->flange_bcd;
			$ArrBqHeader[$val]['flange_n']			= $qHeader[0]->flange_n;
			$ArrBqHeader[$val]['flange_oh']			= $qHeader[0]->flange_oh;
			$ArrBqHeader[$val]['rev']				= $qHeader[0]->rev;
			$ArrBqHeader[$val]['status']			= $qHeader[0]->status;
			$ArrBqHeader[$val]['approve_by']		= $qHeader[0]->approve_by;
			$ArrBqHeader[$val]['approve_date']		= $qHeader[0]->approve_date;
			$ArrBqHeader[$val]['approve_reason']	= $qHeader[0]->approve_reason;
			$ArrBqHeader[$val]['sts_price']			= $qHeader[0]->sts_price;
			$ArrBqHeader[$val]['sts_price_by']		= $qHeader[0]->sts_price_by;
			$ArrBqHeader[$val]['sts_price_date']	= $qHeader[0]->sts_price_date;
			$ArrBqHeader[$val]['sts_price_reason']	= $qHeader[0]->sts_price_reason;
			$ArrBqHeader[$val]['created_by']		= $qHeader[0]->created_by;
			$ArrBqHeader[$val]['created_date']		= $qHeader[0]->created_date;
			$ArrBqHeader[$val]['deleted']			= $qHeader[0]->deleted;
			$ArrBqHeader[$val]['deleted_by']		= $qHeader[0]->deleted_by;
			$ArrBqHeader[$val]['deleted_date']		= $qHeader[0]->deleted_date;
			//
			$ArrBqHeader[$val]['pipe_thickness']		= $qHeader[0]->pipe_thickness;
			$ArrBqHeader[$val]['joint_thickness']		= $qHeader[0]->joint_thickness;
			$ArrBqHeader[$val]['factor_thickness']		= $qHeader[0]->factor_thickness;
			$ArrBqHeader[$val]['factor']			= $qHeader[0]->factor;
			
			
			//================================================================================================================
			//============================================DEFAULT BY ARWANT===================================================
			//================================================================================================================
			if(!empty($qHeader[0]->standart_code)){
				$plusSQL = "";
				if($qHeader[0]->parent_product == 'concentric reducer' OR $qHeader[0]->parent_product == 'reducer tee mould' OR $qHeader[0]->parent_product == 'eccentric reducer' OR $qHeader[0]->parent_product == 'reducer tee slongsong' OR $qHeader[0]->parent_product == 'branch joint'){
					$plusSQL = " AND diameter2='".$qHeader[0]->diameter2."'";
				}
				$getDefVal		= $this->db->query("SELECT * FROM help_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." LIMIT 1 ")->result();
				$getDefValNum	= $this->db->query("SELECT * FROM help_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." LIMIT 1 ")->num_rows();
				if($getDefValNum > 0){
					$ArrBqDefault[$val]['id_product']				= $valx['id_product'];
					$ArrBqDefault[$val]['id_bq']					= $id_bq;
					$ArrBqDefault[$val]['id_milik']					= $valx['id'];
					$ArrBqDefault[$val]['product_parent']			= $getDefVal[0]->product_parent;
					$ArrBqDefault[$val]['kd_cust']					= $getDefVal[0]->kd_cust;
					$ArrBqDefault[$val]['customer']					= $getDefVal[0]->customer;
					$ArrBqDefault[$val]['standart_code']			= $getDefVal[0]->standart_code;
					$ArrBqDefault[$val]['diameter']					= $getDefVal[0]->diameter;
					$ArrBqDefault[$val]['diameter2']				= $getDefVal[0]->diameter2;
					$ArrBqDefault[$val]['liner']					= $getDefVal[0]->liner;
					$ArrBqDefault[$val]['pn']						= $getDefVal[0]->pn;
					$ArrBqDefault[$val]['overlap']					= $getDefVal[0]->overlap;
					$ArrBqDefault[$val]['waste']					= $getDefVal[0]->waste;
					$ArrBqDefault[$val]['waste_n1']					= $getDefVal[0]->waste_n1;
					$ArrBqDefault[$val]['waste_n2']					= $getDefVal[0]->waste_n2;
					$ArrBqDefault[$val]['max']						= $getDefVal[0]->max;
					$ArrBqDefault[$val]['min']						= $getDefVal[0]->min;
					$ArrBqDefault[$val]['plastic_film']				= $getDefVal[0]->plastic_film;
					$ArrBqDefault[$val]['lin_resin_veil_a']			= $getDefVal[0]->lin_resin_veil_a;
					$ArrBqDefault[$val]['lin_resin_veil_b']			= $getDefVal[0]->lin_resin_veil_b;
					$ArrBqDefault[$val]['lin_resin_veil']			= $getDefVal[0]->lin_resin_veil;
					$ArrBqDefault[$val]['lin_resin_veil_add_a']		= $getDefVal[0]->lin_resin_veil_add_a;
					$ArrBqDefault[$val]['lin_resin_veil_add_b']		= $getDefVal[0]->lin_resin_veil_add_b;
					$ArrBqDefault[$val]['lin_resin_veil_add']		= $getDefVal[0]->lin_resin_veil_add;
					$ArrBqDefault[$val]['lin_resin_csm_a']			= $getDefVal[0]->lin_resin_csm_a;
					$ArrBqDefault[$val]['lin_resin_csm_b']			= $getDefVal[0]->lin_resin_csm_b;
					$ArrBqDefault[$val]['lin_resin_csm']			= $getDefVal[0]->lin_resin_csm;
					$ArrBqDefault[$val]['lin_resin_csm_add_a']		= $getDefVal[0]->lin_resin_csm_add_a;
					$ArrBqDefault[$val]['lin_resin_csm_add_b']		= $getDefVal[0]->lin_resin_csm_add_b;
					$ArrBqDefault[$val]['lin_resin_csm_add']		= $getDefVal[0]->lin_resin_csm_add;
					$ArrBqDefault[$val]['lin_faktor_veil']			= $getDefVal[0]->lin_faktor_veil;
					$ArrBqDefault[$val]['lin_faktor_veil_add']		= $getDefVal[0]->lin_faktor_veil_add;
					$ArrBqDefault[$val]['lin_faktor_csm']			= $getDefVal[0]->lin_faktor_csm;
					$ArrBqDefault[$val]['lin_faktor_csm_add']		= $getDefVal[0]->lin_faktor_csm_add;
					$ArrBqDefault[$val]['lin_resin']				= $getDefVal[0]->lin_resin;
					$ArrBqDefault[$val]['lin_resin_thickness']		= $getDefVal[0]->lin_resin_thickness;
					$ArrBqDefault[$val]['str_resin_csm_a']			= $getDefVal[0]->str_resin_csm_a;
					$ArrBqDefault[$val]['str_resin_csm_b']			= $getDefVal[0]->str_resin_csm_b;
					$ArrBqDefault[$val]['str_resin_csm']			= $getDefVal[0]->str_resin_csm;
					$ArrBqDefault[$val]['str_resin_csm_add_a']		= $getDefVal[0]->str_resin_csm_add_a;
					$ArrBqDefault[$val]['str_resin_csm_add_b']		= $getDefVal[0]->str_resin_csm_add_b;
					$ArrBqDefault[$val]['str_resin_csm_add']		= $getDefVal[0]->str_resin_csm_add;
					$ArrBqDefault[$val]['str_resin_wr_a']			= $getDefVal[0]->str_resin_wr_a;
					$ArrBqDefault[$val]['str_resin_wr_b']			= $getDefVal[0]->str_resin_wr_b;
					$ArrBqDefault[$val]['str_resin_wr']				= $getDefVal[0]->str_resin_wr;
					$ArrBqDefault[$val]['str_resin_wr_add_a']		= $getDefVal[0]->str_resin_wr_add_a;
					$ArrBqDefault[$val]['str_resin_wr_add_b']		= $getDefVal[0]->str_resin_wr_add_b;
					$ArrBqDefault[$val]['str_resin_wr_add']			= $getDefVal[0]->str_resin_wr_add;
					$ArrBqDefault[$val]['str_resin_rv_a']			= $getDefVal[0]->str_resin_rv_a;
					$ArrBqDefault[$val]['str_resin_rv_b']			= $getDefVal[0]->str_resin_rv_b;
					$ArrBqDefault[$val]['str_resin_rv']				= $getDefVal[0]->str_resin_rv;
					$ArrBqDefault[$val]['str_resin_rv_add_a']		= $getDefVal[0]->str_resin_rv_add_a;
					$ArrBqDefault[$val]['str_resin_rv_add_b']		= $getDefVal[0]->str_resin_rv_add_b;
					$ArrBqDefault[$val]['str_resin_rv_add']			= $getDefVal[0]->str_resin_rv_add;
					$ArrBqDefault[$val]['str_faktor_csm']			= $getDefVal[0]->str_faktor_csm;
					$ArrBqDefault[$val]['str_faktor_csm_add']		= $getDefVal[0]->str_faktor_csm_add;
					$ArrBqDefault[$val]['str_faktor_wr']			= $getDefVal[0]->str_faktor_wr;
					$ArrBqDefault[$val]['str_faktor_wr_add']		= $getDefVal[0]->str_faktor_wr_add;
					$ArrBqDefault[$val]['str_faktor_rv']			= $getDefVal[0]->str_faktor_rv;
					$ArrBqDefault[$val]['str_faktor_rv_bw']			= $getDefVal[0]->str_faktor_rv_bw;
					$ArrBqDefault[$val]['str_faktor_rv_jb']			= $getDefVal[0]->str_faktor_rv_jb;
					$ArrBqDefault[$val]['str_faktor_rv_add']		= $getDefVal[0]->str_faktor_rv_add;
					$ArrBqDefault[$val]['str_faktor_rv_add_bw']		= $getDefVal[0]->str_faktor_rv_add_bw;
					$ArrBqDefault[$val]['str_faktor_rv_add_jb']		= $getDefVal[0]->str_faktor_rv_add_jb;
					$ArrBqDefault[$val]['str_resin']				= $getDefVal[0]->str_resin;
					$ArrBqDefault[$val]['str_resin_thickness']		= $getDefVal[0]->str_resin_thickness;
					$ArrBqDefault[$val]['eks_resin_veil_a']			= $getDefVal[0]->eks_resin_veil_a;
					$ArrBqDefault[$val]['eks_resin_veil_b']			= $getDefVal[0]->eks_resin_veil_b;
					$ArrBqDefault[$val]['eks_resin_veil']			= $getDefVal[0]->eks_resin_veil;
					$ArrBqDefault[$val]['eks_resin_veil_add_a']		= $getDefVal[0]->eks_resin_veil_add_a;
					$ArrBqDefault[$val]['eks_resin_veil_add_b']		= $getDefVal[0]->eks_resin_veil_add_b;
					$ArrBqDefault[$val]['eks_resin_veil_add']		= $getDefVal[0]->eks_resin_veil_add;
					$ArrBqDefault[$val]['eks_resin_csm_a']			= $getDefVal[0]->eks_resin_csm_a;
					$ArrBqDefault[$val]['eks_resin_csm_b']			= $getDefVal[0]->eks_resin_csm_b;
					$ArrBqDefault[$val]['eks_resin_csm']			= $getDefVal[0]->eks_resin_csm;
					$ArrBqDefault[$val]['eks_resin_csm_add_a']		= $getDefVal[0]->eks_resin_csm_add_a;
					$ArrBqDefault[$val]['eks_resin_csm_add_b']		= $getDefVal[0]->eks_resin_csm_add_b;
					$ArrBqDefault[$val]['eks_resin_csm_add']		= $getDefVal[0]->eks_resin_csm_add;
					$ArrBqDefault[$val]['eks_faktor_veil']			= $getDefVal[0]->eks_faktor_veil;
					$ArrBqDefault[$val]['eks_faktor_veil_add']		= $getDefVal[0]->eks_faktor_veil_add;
					$ArrBqDefault[$val]['eks_faktor_csm']			= $getDefVal[0]->eks_faktor_csm;
					$ArrBqDefault[$val]['eks_faktor_csm_add']		= $getDefVal[0]->eks_faktor_csm_add;
					$ArrBqDefault[$val]['eks_resin']				= $getDefVal[0]->eks_resin;
					$ArrBqDefault[$val]['eks_resin_thickness']		= $getDefVal[0]->eks_resin_thickness;
					$ArrBqDefault[$val]['topcoat_resin']			= $getDefVal[0]->topcoat_resin;
					$ArrBqDefault[$val]['str_n1_resin_csm_a']		= $getDefVal[0]->str_n1_resin_csm_a;
					$ArrBqDefault[$val]['str_n1_resin_csm_b']		= $getDefVal[0]->str_n1_resin_csm_b;
					$ArrBqDefault[$val]['str_n1_resin_csm']			= $getDefVal[0]->str_n1_resin_csm;
					$ArrBqDefault[$val]['str_n1_resin_csm_add_a']	= $getDefVal[0]->str_n1_resin_csm_add_a;
					$ArrBqDefault[$val]['str_n1_resin_csm_add_b']	= $getDefVal[0]->str_n1_resin_csm_add_b;
					$ArrBqDefault[$val]['str_n1_resin_csm_add']		= $getDefVal[0]->str_n1_resin_csm_add;
					$ArrBqDefault[$val]['str_n1_resin_wr_a']		= $getDefVal[0]->str_n1_resin_wr_a;
					$ArrBqDefault[$val]['str_n1_resin_wr_b']		= $getDefVal[0]->str_n1_resin_wr_b;
					$ArrBqDefault[$val]['str_n1_resin_wr']			= $getDefVal[0]->str_n1_resin_wr;
					$ArrBqDefault[$val]['str_n1_resin_wr_add_a']	= $getDefVal[0]->str_n1_resin_wr_add_a;
					$ArrBqDefault[$val]['str_n1_resin_wr_add_b']	= $getDefVal[0]->str_n1_resin_wr_add_b;
					$ArrBqDefault[$val]['str_n1_resin_wr_add']		= $getDefVal[0]->str_n1_resin_wr_add;
					$ArrBqDefault[$val]['str_n1_resin_rv_a']		= $getDefVal[0]->str_n1_resin_rv_a;
					$ArrBqDefault[$val]['str_n1_resin_rv_b']		= $getDefVal[0]->str_n1_resin_rv_b;
					$ArrBqDefault[$val]['str_n1_resin_rv']			= $getDefVal[0]->str_n1_resin_rv;
					$ArrBqDefault[$val]['str_n1_resin_rv_add_a']	= $getDefVal[0]->str_n1_resin_rv_add_a;
					$ArrBqDefault[$val]['str_n1_resin_rv_add_b']	= $getDefVal[0]->str_n1_resin_rv_add_b;
					$ArrBqDefault[$val]['str_n1_resin_rv_add']		= $getDefVal[0]->str_n1_resin_rv_add;
					$ArrBqDefault[$val]['str_n1_faktor_csm']		= $getDefVal[0]->str_n1_faktor_csm;
					$ArrBqDefault[$val]['str_n1_faktor_csm_add']	= $getDefVal[0]->str_n1_faktor_csm_add;
					$ArrBqDefault[$val]['str_n1_faktor_wr']			= $getDefVal[0]->str_n1_faktor_wr;
					$ArrBqDefault[$val]['str_n1_faktor_wr_add']		= $getDefVal[0]->str_n1_faktor_wr_add;
					$ArrBqDefault[$val]['str_n1_faktor_rv']			= $getDefVal[0]->str_n1_faktor_rv;
					$ArrBqDefault[$val]['str_n1_faktor_rv_bw']		= $getDefVal[0]->str_n1_faktor_rv_bw;
					$ArrBqDefault[$val]['str_n1_faktor_rv_jb']		= $getDefVal[0]->str_n1_faktor_rv_jb;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add']		= $getDefVal[0]->str_n1_faktor_rv_add;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add_bw']	= $getDefVal[0]->str_n1_faktor_rv_add_bw;
					$ArrBqDefault[$val]['str_n1_faktor_rv_add_jb']	= $getDefVal[0]->str_n1_faktor_rv_add_jb;
					$ArrBqDefault[$val]['str_n1_resin']				= $getDefVal[0]->str_n1_resin;
					$ArrBqDefault[$val]['str_n1_resin_thickness']	= $getDefVal[0]->str_n1_resin_thickness;
					$ArrBqDefault[$val]['str_n2_resin_csm_a']		= $getDefVal[0]->str_n2_resin_csm_a;
					$ArrBqDefault[$val]['str_n2_resin_csm_b']		= $getDefVal[0]->str_n2_resin_csm_b;
					$ArrBqDefault[$val]['str_n2_resin_csm']			= $getDefVal[0]->str_n2_resin_csm;
					$ArrBqDefault[$val]['str_n2_resin_csm_add_a']	= $getDefVal[0]->str_n2_resin_csm_add_a;
					$ArrBqDefault[$val]['str_n2_resin_csm_add_b']	= $getDefVal[0]->str_n2_resin_csm_add_b;
					$ArrBqDefault[$val]['str_n2_resin_csm_add']		= $getDefVal[0]->str_n2_resin_csm_add;
					$ArrBqDefault[$val]['str_n2_resin_wr_a']		= $getDefVal[0]->str_n2_resin_wr_a;
					$ArrBqDefault[$val]['str_n2_resin_wr_b']		= $getDefVal[0]->str_n2_resin_wr_b;
					$ArrBqDefault[$val]['str_n2_resin_wr']			= $getDefVal[0]->str_n2_resin_wr;
					$ArrBqDefault[$val]['str_n2_resin_wr_add_a']	= $getDefVal[0]->str_n2_resin_wr_add_a;
					$ArrBqDefault[$val]['str_n2_resin_wr_add_b']	= $getDefVal[0]->str_n2_resin_wr_add_b;
					$ArrBqDefault[$val]['str_n2_resin_wr_add']		= $getDefVal[0]->str_n2_resin_wr_add;
					$ArrBqDefault[$val]['str_n2_faktor_csm']		= $getDefVal[0]->str_n2_faktor_csm;
					$ArrBqDefault[$val]['str_n2_faktor_csm_add']	= $getDefVal[0]->str_n2_faktor_csm_add;
					$ArrBqDefault[$val]['str_n2_faktor_wr']			= $getDefVal[0]->str_n2_faktor_wr;
					$ArrBqDefault[$val]['str_n2_faktor_wr_add']		= $getDefVal[0]->str_n2_faktor_wr_add;
					$ArrBqDefault[$val]['str_n2_resin']				= $getDefVal[0]->str_n2_resin;
					$ArrBqDefault[$val]['str_n2_resin_thickness']	= $getDefVal[0]->str_n2_resin_thickness;
					$ArrBqDefault[$val]['created_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefault[$val]['created_date']				= date('Y-m-d H:i:s');
				}
			}
			
			//Insert Component Header To Hist
			$qHeaderHistDef		= $this->db->query("SELECT * FROM bq_component_default WHERE id_bq='".$id_bq."' ")->result_array();
			$qHeaderHistNumDef	= $this->db->query("SELECT * FROM bq_component_default WHERE id_bq='".$id_bq."' ")->num_rows();
			if($qHeaderHistNumDef > 0){
				foreach($qHeaderHistDef AS $val2HistADef => $valx2HistADef){
					$ArrBqDefaultHist[$val2HistADef]['id_product']				= $valx2HistADef['id_product'];
					$ArrBqDefaultHist[$val2HistADef]['id_milik']				= $valx2HistADef['id_milik'];
					$ArrBqDefaultHist[$val2HistADef]['id_bq']					= $valx2HistADef['id_bq'];
					$ArrBqDefaultHist[$val2HistADef]['product_parent']			= $valx2HistADef['product_parent'];
					$ArrBqDefaultHist[$val2HistADef]['kd_cust']					= $valx2HistADef['kd_cust'];
					$ArrBqDefaultHist[$val2HistADef]['customer']				= $valx2HistADef['customer'];
					$ArrBqDefaultHist[$val2HistADef]['standart_code']			= $valx2HistADef['standart_code'];
					$ArrBqDefaultHist[$val2HistADef]['diameter']				= $valx2HistADef['diameter'];
					$ArrBqDefaultHist[$val2HistADef]['diameter2']				= $valx2HistADef['diameter2'];
					$ArrBqDefaultHist[$val2HistADef]['liner']					= $valx2HistADef['liner'];
					$ArrBqDefaultHist[$val2HistADef]['pn']						= $valx2HistADef['pn'];
					$ArrBqDefaultHist[$val2HistADef]['overlap']					= $valx2HistADef['overlap'];
					$ArrBqDefaultHist[$val2HistADef]['waste']					= $valx2HistADef['waste'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n1']				= $valx2HistADef['waste_n1'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n2']				= $valx2HistADef['waste_n2'];
					$ArrBqDefaultHist[$val2HistADef]['max']						= $valx2HistADef['max'];
					$ArrBqDefaultHist[$val2HistADef]['min']						= $valx2HistADef['min'];
					$ArrBqDefaultHist[$val2HistADef]['plastic_film']			= $valx2HistADef['plastic_film'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_a']		= $valx2HistADef['lin_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_b']		= $valx2HistADef['lin_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil']			= $valx2HistADef['lin_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_a']	= $valx2HistADef['lin_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_b']	= $valx2HistADef['lin_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add']		= $valx2HistADef['lin_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_a']			= $valx2HistADef['lin_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_b']			= $valx2HistADef['lin_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm']			= $valx2HistADef['lin_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_a']		= $valx2HistADef['lin_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_b']		= $valx2HistADef['lin_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add']		= $valx2HistADef['lin_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil']			= $valx2HistADef['lin_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil_add']		= $valx2HistADef['lin_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm']			= $valx2HistADef['lin_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm_add']		= $valx2HistADef['lin_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin']				= $valx2HistADef['lin_resin'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_thickness']		= $valx2HistADef['lin_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_a']			= $valx2HistADef['str_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_b']			= $valx2HistADef['str_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm']			= $valx2HistADef['str_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_a']		= $valx2HistADef['str_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_b']		= $valx2HistADef['str_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add']		= $valx2HistADef['str_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_a']			= $valx2HistADef['str_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_b']			= $valx2HistADef['str_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr']			= $valx2HistADef['str_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_a']		= $valx2HistADef['str_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_b']		= $valx2HistADef['str_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add']		= $valx2HistADef['str_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_a']			= $valx2HistADef['str_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_b']			= $valx2HistADef['str_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv']			= $valx2HistADef['str_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_a']		= $valx2HistADef['str_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_b']		= $valx2HistADef['str_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add']		= $valx2HistADef['str_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm']			= $valx2HistADef['str_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm_add']		= $valx2HistADef['str_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr']			= $valx2HistADef['str_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr_add']		= $valx2HistADef['str_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv']			= $valx2HistADef['str_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_bw']		= $valx2HistADef['str_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_jb']		= $valx2HistADef['str_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add']		= $valx2HistADef['str_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_bw']	= $valx2HistADef['str_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_jb']	= $valx2HistADef['str_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin']				= $valx2HistADef['str_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_thickness']		= $valx2HistADef['str_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_a']		= $valx2HistADef['eks_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_b']		= $valx2HistADef['eks_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil']			= $valx2HistADef['eks_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_a']	= $valx2HistADef['eks_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_b']	= $valx2HistADef['eks_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add']		= $valx2HistADef['eks_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_a']			= $valx2HistADef['eks_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_b']			= $valx2HistADef['eks_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm']			= $valx2HistADef['eks_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_a']		= $valx2HistADef['eks_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_b']		= $valx2HistADef['eks_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add']		= $valx2HistADef['eks_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil']			= $valx2HistADef['eks_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil_add']		= $valx2HistADef['eks_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm']			= $valx2HistADef['eks_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm_add']		= $valx2HistADef['eks_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin']				= $valx2HistADef['eks_resin'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_thickness']		= $valx2HistADef['eks_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['topcoat_resin']			= $valx2HistADef['topcoat_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_a']		= $valx2HistADef['str_n1_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_b']		= $valx2HistADef['str_n1_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm']		= $valx2HistADef['str_n1_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_a']	= $valx2HistADef['str_n1_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_b']	= $valx2HistADef['str_n1_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add']	= $valx2HistADef['str_n1_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_a']		= $valx2HistADef['str_n1_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_b']		= $valx2HistADef['str_n1_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr']			= $valx2HistADef['str_n1_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_a']	= $valx2HistADef['str_n1_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_b']	= $valx2HistADef['str_n1_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add']		= $valx2HistADef['str_n1_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_a']		= $valx2HistADef['str_n1_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_b']		= $valx2HistADef['str_n1_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv']			= $valx2HistADef['str_n1_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_a']	= $valx2HistADef['str_n1_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_b']	= $valx2HistADef['str_n1_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add']		= $valx2HistADef['str_n1_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm']		= $valx2HistADef['str_n1_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm_add']	= $valx2HistADef['str_n1_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr']		= $valx2HistADef['str_n1_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr_add']	= $valx2HistADef['str_n1_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv']		= $valx2HistADef['str_n1_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_bw']		= $valx2HistADef['str_n1_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_jb']		= $valx2HistADef['str_n1_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add']	= $valx2HistADef['str_n1_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_bw']	= $valx2HistADef['str_n1_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_jb']	= $valx2HistADef['str_n1_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin']			= $valx2HistADef['str_n1_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_thickness']	= $valx2HistADef['str_n1_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_a']		= $valx2HistADef['str_n2_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_b']		= $valx2HistADef['str_n2_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm']		= $valx2HistADef['str_n2_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_a']	= $valx2HistADef['str_n2_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_b']	= $valx2HistADef['str_n2_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add']	= $valx2HistADef['str_n2_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_a']		= $valx2HistADef['str_n2_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_b']		= $valx2HistADef['str_n2_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr']			= $valx2HistADef['str_n2_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_a']	= $valx2HistADef['str_n2_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_b']	= $valx2HistADef['str_n2_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add']		= $valx2HistADef['str_n2_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm']		= $valx2HistADef['str_n2_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm_add']	= $valx2HistADef['str_n2_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr']		= $valx2HistADef['str_n2_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr_add']	= $valx2HistADef['str_n2_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin']			= $valx2HistADef['str_n2_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_thickness']	= $valx2HistADef['str_n2_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['created_by']				= $valx2HistADef['created_by'];
					$ArrBqDefaultHist[$val2HistADef]['created_date']			= $valx2HistADef['created_date'];
					$ArrBqDefaultHist[$val2HistADef]['modified_by']				= $valx2HistADef['modified_by'];
					$ArrBqDefaultHist[$val2HistADef]['modified_date']			= $valx2HistADef['modified_date'];
					$ArrBqDefaultHist[$val2HistADef]['hist_by']					= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefaultHist[$val2HistADef]['hist_date']				= date('Y-m-d H:i:s');
					
					
				}
			}
			//================================================================================================================
			//================================================================================================================
			//================================================================================================================
			
			//Insert Component Header To Hist
			$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$id_bq."' ")->result_array();
			$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$id_bq."' ")->num_rows();
			if($qHeaderHistNum > 0){
				foreach($qHeaderHist AS $val2HistA => $valx2HistA){
					$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
					$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
					$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
					$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
					$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
					$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
					$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
					$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
					$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
					$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
					$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
					$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
					$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
					$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
					$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
					$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
					$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
					$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
					$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
					$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
					$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
					$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
					$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
					$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
					$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length']			= $valx2HistA['wrap_length'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length2']		= $valx2HistA['wrap_length2'];
					$ArrBqHeaderHist[$val2HistA]['high']				= $valx2HistA['high'];
					$ArrBqHeaderHist[$val2HistA]['area2']				= $valx2HistA['area2'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
					$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
					$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
					$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
					$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
					$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
					$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
					$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
					$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
					$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
					$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
					$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
					$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
					$ArrBqHeaderHist[$val2HistA]['deleted_by']			= $valx2HistA['deleted_by'];
					$ArrBqHeaderHist[$val2HistA]['deleted_date']		= $valx2HistA['deleted_date'];
					$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
					
				}
			}

			//Component Detail
			$qDetail	= $this->db->query("SELECT a.*, b.panjang FROM component_detail a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$valx['id_product']."' ")->result_array();
			foreach($qDetail AS $val2 => $valx2){
				$LoopDetail++;

				// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx2['id_material']."' LIMIT 1";
				// $restPrice = $this->db->query($sqlPrice)->result();

				$ArrBqDetail[$LoopDetail]['id_product']		= $valx['id_product'];
				$ArrBqDetail[$LoopDetail]['id_bq']				= $id_bq;
				$ArrBqDetail[$LoopDetail]['id_milik']			= $valx['id'];
				$ArrBqDetail[$LoopDetail]['detail_name']		= $valx2['detail_name'];
				$ArrBqDetail[$LoopDetail]['acuhan']			= $valx2['acuhan'];
				$ArrBqDetail[$LoopDetail]['id_ori']			= $valx2['id_ori'];
				$ArrBqDetail[$LoopDetail]['id_ori2']			= $valx2['id_ori2'];
				$ArrBqDetail[$LoopDetail]['id_category']		= $valx2['id_category'];
				$ArrBqDetail[$LoopDetail]['nm_category']		= $valx2['nm_category'];
				$ArrBqDetail[$LoopDetail]['id_material']		= $valx2['id_material'];
				$ArrBqDetail[$LoopDetail]['nm_material']		= $valx2['nm_material'];
				$ArrBqDetail[$LoopDetail]['value']			= $valx2['value'];
				$ArrBqDetail[$LoopDetail]['thickness']		= $valx2['thickness'];
				$ArrBqDetail[$LoopDetail]['fak_pengali']		= $valx2['fak_pengali'];
				$ArrBqDetail[$LoopDetail]['bw']				= $valx2['bw'];
				$ArrBqDetail[$LoopDetail]['jumlah']			= $valx2['jumlah'];
				$ArrBqDetail[$LoopDetail]['layer']			= $valx2['layer'];
				$ArrBqDetail[$LoopDetail]['containing']		= $valx2['containing'];
				$ArrBqDetail[$LoopDetail]['total_thickness']	= $valx2['total_thickness'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / floatval($valx2['panjang']))* (floatval($valx['panjang']) + 400);
				}
				elseif ($qHeader[0]->parent_product == 'branch joint' OR $qHeader[0]->parent_product == 'field joint' OR $qHeader[0]->parent_product == 'shop joint') {
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['material_weight'];
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / 1000) * floatval($valx['panjang']);
				}
				else{
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['last_cost'];
				}
				$ArrBqDetail[$LoopDetail]['rev']				= $qHeader[0]->rev;
				//
				$ArrBqDetail[$LoopDetail]['area_weight']				= $valx2['area_weight'];
				$ArrBqDetail[$LoopDetail]['material_weight']				= $valx2['material_weight'];
				$ArrBqDetail[$LoopDetail]['percentage']				= $valx2['percentage'];
				$ArrBqDetail[$LoopDetail]['resin_content']				= $valx2['resin_content'];

				$ArrBqDetail[$LoopDetail]['price_mat']				= get_price_ref($valx2['id_material']);
			}

			//Component Lamination
			$qDetailLam	= $this->db->query("SELECT * FROM component_lamination WHERE id_product='".$valx['id_product']."' ")->result_array();
			foreach($qDetailLam AS $val2 => $valx2){
				$LoopDetailLam++;
				$ArrBqDetailLam[$LoopDetailLam]['id_product']		= $valx['id_product'];
				$ArrBqDetailLam[$LoopDetailLam]['id_bq']				= $id_bq;
				$ArrBqDetailLam[$LoopDetailLam]['id_milik']			= $valx['id'];
				$ArrBqDetailLam[$LoopDetailLam]['detail_name']		= $valx2['detail_name'];
				$ArrBqDetailLam[$LoopDetailLam]['lapisan']			= $valx2['lapisan'];
				$ArrBqDetailLam[$LoopDetailLam]['std_glass']			= $valx2['std_glass'];
				$ArrBqDetailLam[$LoopDetailLam]['width']			= $valx2['width'];
				$ArrBqDetailLam[$LoopDetailLam]['stage']		= $valx2['stage'];
				$ArrBqDetailLam[$LoopDetailLam]['glass']		= $valx2['glass'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_1']		= $valx2['thickness_1'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_2']		= $valx2['thickness_2'];
				$ArrBqDetailLam[$LoopDetailLam]['glass_length']			= $valx2['glass_length'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_veil']		= $valx2['weight_veil'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_csm']		= $valx2['weight_csm'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_wr']				= $valx2['weight_wr'];
			}

			//Insert Component Detail To Hist
			$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' ")->result_array();
			$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' ")->num_rows();
			if($qDetailHistNum > 0){
				foreach($qDetailHist AS $val2Hist => $valx2Hist){
					$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
					$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
					$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
					$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
					$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
					$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
					$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
					$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
					$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
					$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
					$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
					$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
					$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
					$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
					$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
					$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
					$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
					$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
					$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
					$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
					$ArrBqDetailHist[$val2Hist]['rev']				= $valx2Hist['rev'];
					$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
					$ArrBqDetailHist[$val2Hist]['hist_by']			= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Plus
			$qDetailPlus	= $this->db->query("SELECT a.*, b.panjang FROM component_detail_plus a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$valx['id_product']."' ")->result_array();
			foreach($qDetailPlus AS $val3 => $valx3){
				$LoopDetailPlus++;

				// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx3['id_material']."' LIMIT 1";
				// $restPrice = $this->db->query($sqlPrice)->result();

				$ArrBqDetailPlus[$LoopDetailPlus]['id_product']		= $valx['id_product'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_bq']			= $id_bq;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_milik']			= $valx['id'];
				$ArrBqDetailPlus[$LoopDetailPlus]['detail_name']		= $valx3['detail_name'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori']			= $valx3['id_ori'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori2']			= $valx3['id_ori2'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_category']		= $valx3['id_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_category']		= $valx3['nm_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_material']		= $valx3['id_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_material']		= $valx3['nm_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['containing']		= $valx3['containing'];
				$ArrBqDetailPlus[$LoopDetailPlus]['perse']			= $valx3['perse'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($valx3['panjang'])) * (floatval($valx['panjang']) + 400);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($valx3['panjang'])) * (floatval($valx['panjang']) + 400);
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / 1000) * floatval($valx['panjang']);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / 1000) * floatval($valx['panjang']);
				}
				else{
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= $valx3['last_full'];
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= $valx3['last_cost'];
				}
				$ArrBqDetailPlus[$LoopDetailPlus]['rev']				= $qHeader[0]->rev;
				$ArrBqDetailPlus[$LoopDetailPlus]['price_mat']			= get_price_ref($valx3['id_material']);
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' ")->result_array();
			$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' ")->num_rows();
			if($qDetailPlusHistNum > 0){
				foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
					$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
					$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
					$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
					$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
					$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
					$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
					$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
					$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
					$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
					$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
					$ArrBqDetailPlusHist[$val3Hist]['rev']			= $valx3Hist['rev'];
					$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Add
			$qDetailAdd		= $this->db->query("SELECT a.*, b.panjang FROM component_detail_add a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$valx['id_product']."' ")->result_array();
			$qDetailAddNum	= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$valx['id_product']."' ")->num_rows();
			if($qDetailAddNum > 0){
				foreach($qDetailAdd AS $val4 => $valx4){
					$LoopDetailAdd++;

					// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx4['id_material']."' LIMIT 1";
					// $restPrice = $this->db->query($sqlPrice)->result();

					$ArrBqDetailAdd[$LoopDetailAdd]['id_product']		= $valx['id_product'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_bq']				= $id_bq;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_milik']			= $valx['id'];
					$ArrBqDetailAdd[$LoopDetailAdd]['detail_name']		= $valx4['detail_name'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_category']		= $valx4['id_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_category']		= $valx4['nm_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_material']		= $valx4['id_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_material']		= $valx4['nm_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['containing']		= $valx4['containing'];
					$ArrBqDetailAdd[$LoopDetailAdd]['perse']			= $valx4['perse'];
					if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= (floatval($valx4['last_full']) / floatval($valx4['panjang'])) * (floatval($valx['panjang']) + 400);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= (floatval($valx4['last_cost']) / floatval($valx4['panjang'])) * (floatval($valx['panjang']) + 400);
					}
					elseif($qHeader[0]->parent_product == 'frp pipe'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= (floatval($valx4['last_full']) / 1000) * floatval($valx['panjang']);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= (floatval($valx4['last_cost']) / 1000) * floatval($valx['panjang']);
					}
					else{
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= $valx4['last_full'];
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= $valx4['last_cost'];
					}
					$ArrBqDetailAdd[$LoopDetailAdd]['rev']				= $qHeader[0]->rev;
					$ArrBqDetailAdd[$LoopDetailAdd]['price_mat']		= get_price_ref($valx4['id_material']);
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' ")->result_array();
			$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' ")->num_rows();
			if($qDetailAddNumHist > 0){
				foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
					$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
					$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
					$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
					$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
					$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
					$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
					$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
					$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
					$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
					$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
					$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
					$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
					$ArrBqDetailAddHist[$val4Hist]['rev']			= $valx4Hist['rev'];
					$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
					$ArrBqDetailAddHist[$val4Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailAddHist[$val4Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Footer
			$qDetailFooter	= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$valx['id_product']."' ")->result_array();
			if (count($qDetailFooter)>0)
			{
				foreach($qDetailFooter AS $val5 => $valx5){
					$LoopFooter++;
					$ArrBqFooter[$LoopFooter]['id_product']	= $valx['id_product'];
					$ArrBqFooter[$LoopFooter]['id_bq']		= $id_bq;
					$ArrBqFooter[$LoopFooter]['id_milik']		= $valx['id'];
					$ArrBqFooter[$LoopFooter]['detail_name']	= $valx5['detail_name'];
					$ArrBqFooter[$LoopFooter]['total']		= $valx5['total'];
					$ArrBqFooter[$LoopFooter]['min']			= $valx5['min'];
					$ArrBqFooter[$LoopFooter]['max']			= $valx5['max'];
					$ArrBqFooter[$LoopFooter]['hasil']		= $valx5['hasil'];
					$ArrBqFooter[$LoopFooter]['rev']			= $qHeader[0]->rev;
				}
			}
			//Insert Component Footer To Hist
			$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' ")->result_array();
			$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' ")->num_rows();
			if($qDetailFooterHistNum > 0){
				foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
					$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
					$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
					$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
					$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
					$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
					$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
					$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
					$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
					$ArrBqFooterHist[$val5Hist]['rev']			= $valx5Hist['rev'];
					$ArrBqFooterHist[$val5Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}

			$qDetailAddNum2				= $this->db->query("SELECT * FROM component_detail_add WHERE id_product='".$valx['id_product']."' ")->num_rows();

			$qDetailHeaderNum2			= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$id_bq."' ")->num_rows();
			$qDetailDetailNum2			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' ")->num_rows();
			$qDetailDetailPlusNum2		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' ")->num_rows();
			$qDetailDetailAddNum2		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' ")->num_rows();
			$qDetailDetailFooterNum2	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' ")->num_rows();
			// echo $qDetailHeaderNum2;
			
		}

		// echo "SELECT * FROM component_detail_add WHERE id_product='".$valx['id_product']."'";
		// print_r($ArrBqDefault);
		// print_r($ArrBqDetail);
		// print_r($ArrBqDetailPlus);
		// print_r($ArrBqDetailAdd);
		// print_r($ArrBqFooter); ArrBqDefaultHist
		// echo $qDetailAddNum2;
		// echo $qDetailHeaderNum2;
		// echo $qDetailDetailNum2;
		// echo $qDetailDetailPlusNum2;
		// echo $qDetailDetailAddNum2;
		// echo $qDetailDetailFooterNum2;
		// echo "</pre>";
		// exit;

		$UpdateIPP	= array(
			'status'	=> 'WAITING EST PRICE PROJECT'
		);

		$UpdateBQ	= array(
			'estimasi'	=> 'Y',
			'est_by'	=> $this->session->userdata['ORI_User']['username'],
			'est_date'	=> date('Y-m-d H:i:s')
		);


      
		$this->db->trans_start();
			$this->db->update_batch('bq_detail_header', $ArrDetBq2, 'id');

			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(count($ArrBqDetailPlusHist)>0){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(count($ArrBqDetailAddHist)>0){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(count($ArrBqFooterHist)>0){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }
			// if(!empty($ArrBqDefaultHist)){
			// 	$this->db->insert_batch('hist_bq_component_default', $ArrBqDefaultHist);
			// }

			//Delete BQ Component
			$this->db->delete('bq_component_header', array('id_bq' => $id_bq));
			$this->db->delete('bq_component_detail', array('id_bq' => $id_bq));
			$this->db->delete('bq_component_lamination', array('id_bq' => $id_bq));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $id_bq));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $id_bq));
			$this->db->delete('bq_component_footer', array('id_bq' => $id_bq));
			$this->db->delete('bq_component_default', array('id_bq' => $id_bq));

			//Insert BQ Component
			if(!empty($ArrBqHeader)){
				$this->db->insert_batch('bq_component_header', $ArrBqHeader);
			}

			if(!empty($ArrBqDetail)){
				$this->db->insert_batch('bq_component_detail', $ArrBqDetail);
			}
			if(!empty($ArrBqDetailLam)){
				$this->db->insert_batch('bq_component_lamination', $ArrBqDetailLam);
			}

			if(!empty($ArrBqDetailPlus)){
				$this->db->insert_batch('bq_component_detail_plus', $ArrBqDetailPlus);
			}
			if(!empty($ArrBqDetailAdd)){
				$this->db->insert_batch('bq_component_detail_add', $ArrBqDetailAdd);
			}
			if(!empty($ArrBqFooter)){
				$this->db->insert_batch('bq_component_footer', $ArrBqFooter);
			}
			if(!empty($ArrBqDefault)){
				$this->db->insert_batch('bq_component_default', $ArrBqDefault);
			}

			// $this->db->where('no_ipp', $no_ipp);
			// $this->db->update('production', $UpdateIPP);

			$this->db->where('id_bq', $id_bq);
			$this->db->update('bq_header', $UpdateBQ);

				// $this->db->query("
									// INSERT hist_bq_detail_header
										// (id_bq, id_delivery, sub_delivery, sts_delivery, id_category, qty,
										 // diameter_1, diameter_2, length, thickness, sudut, id_standard,type,id_product, updated, updated_time)
									// SELECT
										// id_bq, id_delivery, sub_delivery, sts_delivery, id_category, qty,
										// diameter_1, diameter_2, length, thickness, sudut, id_standard,type,
										// id_product, '".$this->session->userdata['ORI_User']['username']."','".date('Y-m-d H:i:s')."'
									// FROM
										// bq_detail_header
									// WHERE
										// id_bq = '".$id_bq."'
								// ");

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $pembeda,
				'pesan'		=>'Estimation structure bq data success. Thanks ...',
				'status'	=> 1
			);
			history('Estimation Structure BQ with code : '.$id_bq.'/'.$no_ipp);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function save_mat_acc(){
		$this->bq_estimasi_model->save_rutin_material();
	}
	
	public function index_estimasi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Project Estimation',
			'action'		=> 'index_estimasi',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Project Estimation');
		$this->load->view('Machine/index_estimasi',$data);
	}

	public function revisi_est(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Project Estimation (Revised Quotation)',
			'action'		=> 'index_estimasi',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Revised Estimation');
		$this->load->view('Machine/revisi_est',$data);
	}

	public function getDataJSONEst(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONEst(
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
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			$get_rev_est = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_costing_header',array('id_bq'=>$row['id_bq']))->result();
			$rev_est = (!empty($get_rev_est[0]->revised))?$get_rev_est[0]->revised:0;
			
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".$rev_est."</span></div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reason_approved_est']))."</div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND id_category <> 'pipe slongsong' AND (id_product= '' OR id_product  is null) ")->num_rows();
			
				$class = Color_status($row['sts_ipp']);
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$class."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					$detX	= "";
					$app	= "";
					$bcbq	= "";
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					// if($Arr_Akses['delete']=='1'){
						// $delX	= "&nbsp;<button class='btn btn-sm btn-danger' id='batalProduksi' title='Cancel Production' data-id_bq='".$row['id_bq']."'><i class='fa fa-trash'></i></button>";
					// }
					
					if($row['estimasi']=='Y'){
						$detX	= "&nbsp;<button class='btn btn-sm btn-success detail_est' title='View Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					}
					
					if($Arr_Akses['update']=='1'){
						if($row['sts_ipp'] == 'WAITING ESTIMATION PROJECT'){
							$updX	= "&nbsp;<button class='btn btn-sm btn-primary' id='editBQ' title='Estimation BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-edit'></i></button>";
							$bcbq	= "&nbsp;<button type='button' class='btn btn-sm btn-danger back_to_bq' title='Back Structure BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-reply'></i></button>";
								
							if($row['aju_approved_est'] == 'N' AND $row['approved'] == 'Y' AND $Check == '0'){
								$app	= "&nbsp;<button type='button' class='btn btn-sm btn-info ajuAppBQ' title='Ajukan BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
								}
						}
					}
					

			$nestedData[]	= "<div align='left' style='padding-left: 20px;'>
									<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$priX."
									
									".$delX."
									".$detX."
									".$updX."
									".$bcbq."
									".$app."
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

	public function queryDataJSONEst($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					a.*,
					b.nm_customer,
					b.project,
					b.status AS sts_ipp
				FROM
					bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
				WHERE b.ref_quo = 0 AND a.approved = 'Y' AND b.sts_hide = 'N' AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'b.nm_customer',
			3 => 'b.project'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function getDataJSONEstRev(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONEstRev(
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
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			$get_rev_est = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_costing_header',array('id_bq'=>$row['id_bq']))->result();
			$rev_est = (!empty($get_rev_est[0]->revised))?$get_rev_est[0]->revised:0;
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".$rev_est."</span></div>";
			$nestedData[]	= "<div align='left'>".ucfirst(strtolower($row['reason_approved_est']))."</div>";
				$Check = $this->db->query("SELECT id_product FROM bq_detail_header WHERE id_bq='".$row['id_bq']."' AND id_category <> 'pipe slongsong' AND (id_product= '' OR id_product  is null) ")->num_rows();

				$warna = Color_status($row['sts_ipp']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					$detX	= "";
					$app	= "";
					$bcbq	= "";
					
					// $priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
					// if($Arr_Akses['delete']=='1'){
						// $delX	= "&nbsp;<button class='btn btn-sm btn-danger' id='batalProduksi' title='Cancel Production' data-id_bq='".$row['id_bq']."'><i class='fa fa-trash'></i></button>";
					// }
					
					if($row['estimasi']=='Y'){
						$detX	= "&nbsp;<button class='btn btn-sm btn-success' id='viewDT' title='View Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					}
					
					if($row['aju_approved_est'] == 'N' AND $row['approved'] == 'Y'){
						$app	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='ajuAppBQ' title='Ajukan BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						if($Arr_Akses['update']=='1'){
							// if($row['estimasi'] == 'Y' AND $Check > 0){
								if($row['sts_ipp'] == 'WAITING ESTIMATION PROJECT'){
									$updX	= "&nbsp;<button class='btn btn-sm btn-primary' id='editBQ' title='Estimation BQ' data-id_bq='".$row['id_bq']."' data-ciri='revisi_est'><i class='fa fa-edit'></i></button>";
									$bcbq	= "&nbsp;<button type='button' class='btn btn-sm btn-danger back_to_bq' title='Back Structure BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-reply'></i></button>";
							
								}
							// }
						}
					}
			$nestedData[]	= "<div align='left' style='padding-left: 20px;'>
									<button class='btn btn-sm btn-warning' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
									".$priX."
									
									".$delX."
									".$detX."
									".$updX."
									".$bcbq."
									".$app."
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

	public function queryDataJSONEstRev($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					a.*,
					b.nm_customer,
					b.project,
					b.ref_quo,
					b.status AS sts_ipp
				FROM
					bq_header a 
					LEFT JOIN production b ON a.no_ipp = b.no_ipp
				WHERE b.ref_quo > 0 AND a.approved = 'Y' AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
			";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'no_ipp'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function bq(){
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');

			$ipp_no = $data['no_ipp'];

			if(!empty($data['ListDetailKomp'])){
				$productDetKomp = $data['ListDetailKomp'];
			}
			if(!empty($data['ListDetailKompSub'])){
				$productDetKomSub = $data['ListDetailKompSub'];
			}
			if(!empty($data['ListDetailKompSingle'])){
				$productDetKomSigle = $data['ListDetailKompSingle'];
			}
			if(!empty($data['ListDetailKompSub2'])){
				$productDetKomSub2 = $data['ListDetailKompSub2'];
			}

			$kode_bq	= "BQ-".$ipp_no;

			$Data_Insert		= array(
				'id_bq'			=> $kode_bq,
				'no_ipp'		=> $data['no_ipp'],
				'series'		=> "",
				'order_type'	=> $data['order_type'],
				'created_date'	=> date('Y-m-d H:i:s'),
				'created_by'	=> $this->session->userdata['ORI_User']['username']
			);
			// echo "<pre>";
			// print_r($Data_Insert);

			// if(!empty($data['ListDetailKomp'])){
				// print_r($productDetKomp);
			// }
			// if(!empty($data['ListDetailKompSub'])){
				// print_r($productDetKomSub);
			// }
			// if(!empty($data['ListDetailKompSingle'])){
				// print_r($productDetKomSigle);
			// }
			// if(!empty($data['ListDetailKompSub2'])){
				// print_r($productDetKomSub2);
			// }
			// exit;

			if(!empty($data['ListDetailKomp'])){
				$detailData	= array();
				$lopp = 0;
				foreach($productDetKomp AS $val => $valx){
					for($no=1; $no <= $valx['qty']; $no++){
						$lopp++;
						$detailData[$lopp]['id_bq'] 		= $kode_bq;
							$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData[$lopp]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData[$lopp]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData[$lopp]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData[$lopp]['series'] 		= $valx['series'];
						$detailData[$lopp]['id_category'] 	= $valx['id_category'];
						$detailData[$lopp]['diameter_1'] 	= $valx['diameter_1'];
						$detailData[$lopp]['diameter_2'] 	= $valx['diameter_2'];
						$detailData[$lopp]['length'] 		= $valx['length'];
						$detailData[$lopp]['thickness'] 	= $valx['thickness'];
						$detailData[$lopp]['sudut'] 		= $valx['sudut'];
						$detailData[$lopp]['id_standard'] 	= $valx['id_standard'];
						$detailData[$lopp]['type'] 			= $valx['type'];
						$detailData[$lopp]['qty'] 			= $valx['qty'];
						$detailData[$lopp]['product_ke'] 	= $no;
					}
				}

				$detailDataHeader	= array();
				$a1	= 0;
				foreach($productDetKomp AS $val => $valx){
					$a1++;
					$dataKR4 = sprintf('%02s',$a1);
					$detailDataHeader[$val]['id_bq'] 			= $kode_bq;
						$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailDataHeader[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailDataHeader[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailDataHeader[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailDataHeader[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailDataHeader[$val]['id_category'] 		= $valx['id_category'];
					$detailDataHeader[$val]['series'] 			= $valx['series'];
					$detailDataHeader[$val]['qty'] 				= $valx['qty'];
					$detailDataHeader[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailDataHeader[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailDataHeader[$val]['length'] 			= $valx['length'];
					$detailDataHeader[$val]['thickness'] 		= $valx['thickness'];
					$detailDataHeader[$val]['sudut'] 			= $valx['sudut'];
					$detailDataHeader[$val]['id_standard'] 	= $valx['id_standard'];
					$detailDataHeader[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailData);
				// print_r($detailDataHeader);
			}

			if(!empty($data['ListDetailKompSub'])){
				$detailData2	= array();
				$lopp2 = 0;
				foreach($productDetKomSub AS $val => $valx){
					for($no=1; $no <= $valx['qty']; $no++){
						$lopp2++;
						$detailData2[$lopp2]['id_bq'] 			= $kode_bq;
						$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData2[$lopp2]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData2[$lopp2]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData2[$lopp2]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData2[$lopp2]['id_category'] 	= $valx['id_category'];
						$detailData2[$lopp2]['series'] 		= $valx['series'];
						$detailData2[$lopp2]['diameter_1'] 		= $valx['diameter_1'];
						$detailData2[$lopp2]['diameter_2'] 		= $valx['diameter_2'];
						$detailData2[$lopp2]['length'] 			= $valx['length'];
						$detailData2[$lopp2]['thickness'] 		= $valx['thickness'];
						$detailData2[$lopp2]['sudut'] 			= $valx['sudut'];
						$detailData2[$lopp2]['id_standard'] 	= $valx['id_standard'];
						$detailData2[$lopp2]['type'] 			= $valx['type'];
						$detailData2[$lopp2]['qty'] 			= $valx['qty'];
						$detailData2[$lopp2]['product_ke'] 		= $no;
					}
				}

				$detailData2Header	= array();
				$a2	= 0;
				foreach($productDetKomSub AS $val => $valx){
					$a2++;
					$dataKR4 = sprintf('%02s',$a2);
					$detailData2Header[$val]['id_bq'] 			= $kode_bq;
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData2Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData2Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData2Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData2Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData2Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData2Header[$val]['series'] 			= $valx['series'];
					$detailData2Header[$val]['qty'] 			= $valx['qty'];
					$detailData2Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData2Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData2Header[$val]['length'] 			= $valx['length'];
					$detailData2Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData2Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData2Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData2Header[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailData2);
				// print_r($detailData2Header);
			}

			if(!empty($data['ListDetailKompSingle'])){
				$detailData3	= array();
				$lopp3 = 0;
				foreach($productDetKomSigle AS $val => $valx){
					for($no=1; $no <= $valx['qty']; $no++){
						$lopp3++;
						$detailData3[$lopp3]['id_bq'] 			= $kode_bq;
						$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData3[$lopp3]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData3[$lopp3]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData3[$lopp3]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData3[$lopp3]['id_category'] 	= $valx['id_category'];
						$detailData3[$lopp3]['series'] 			= $valx['series'];
						$detailData3[$lopp3]['diameter_1'] 		= $valx['diameter_1'];
						$detailData3[$lopp3]['diameter_2'] 		= $valx['diameter_2'];
						$detailData3[$lopp3]['length'] 			= $valx['length'];
						$detailData3[$lopp3]['thickness'] 		= $valx['thickness'];
						$detailData3[$lopp3]['sudut'] 			= $valx['sudut'];
						$detailData3[$lopp3]['id_standard'] 	= $valx['id_standard'];
						$detailData3[$lopp3]['type'] 			= $valx['type'];
						$detailData3[$lopp3]['qty'] 			= $valx['qty'];
						$detailData3[$lopp3]['product_ke'] 		= $no;
					}
				}

				$detailData3Header	= array();
				$a3	= 0;
				foreach($productDetKomSigle AS $val => $valx){
					$a3++;
					$dataKR4 = sprintf('%02s',$a3);
					$detailData3Header[$val]['id_bq'] 			= $kode_bq;
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData3Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData3Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData3Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData3Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData3Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData3Header[$val]['series'] 			= $valx['series'];
					$detailData3Header[$val]['qty'] 			= $valx['qty'];
					$detailData3Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData3Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData3Header[$val]['length'] 			= $valx['length'];
					$detailData3Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData3Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData3Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData3Header[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailData3);
				// print_r($detailData3Header);
			}

			if(!empty($data['ListDetailKompSub2'])){
				$detailData4	= array();
				$lopp4 = 0;
				foreach($productDetKomSub2 AS $val => $valx){
					for($no=1; $no <= $valx['qty']; $no++){
						$lopp4++;
						$detailData4[$lopp4]['id_bq'] 			= $kode_bq;
						$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData4[$lopp4]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData4[$lopp4]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData4[$lopp4]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData4[$lopp4]['id_category'] 	= $valx['id_category'];
						$detailData4[$lopp4]['series'] 			= $valx['series'];
						$detailData4[$lopp4]['diameter_1'] 		= $valx['diameter_1'];
						$detailData4[$lopp4]['diameter_2'] 		= $valx['diameter_2'];
						$detailData4[$lopp4]['length'] 			= $valx['length'];
						$detailData4[$lopp4]['thickness'] 		= $valx['thickness'];
						$detailData4[$lopp4]['sudut'] 			= $valx['sudut'];
						$detailData4[$lopp4]['id_standard'] 	= $valx['id_standard'];
						$detailData4[$lopp4]['type'] 			= $valx['type'];
						$detailData4[$lopp4]['qty'] 			= $valx['qty'];
						$detailData4[$lopp4]['product_ke'] 		= $no;
					}
				}

				$detailData4Header	= array();
				$a4	= 0;
				foreach($productDetKomSigle AS $val => $valx){
					$a4++;
					$dataKR4 = sprintf('%02s',$a4);
					$detailData4Header[$val]['id_bq'] 			= $kode_bq;
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData4Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData4Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData4Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData4Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData4Header[$val]['series'] 			= $valx['series'];
					$detailData4Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData4Header[$val]['qty'] 			= $valx['qty'];
					$detailData4Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData4Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData4Header[$val]['length'] 			= $valx['length'];
					$detailData4Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData4Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData4Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData4Header[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailData4);
				// print_r($detailData4Header);
			}

			// exit;

			$IPPNum	= $data['no_ipp'];
			$UpdateIPP	= array(
				'status'	=> 'WAITING STRUCTURE BQ'
			);

			$this->db->trans_start();
				$this->db->insert('bq_header', $Data_Insert);
				if(!empty($data['ListDetailKomp'])){
					$this->db->insert_batch('bq_detail_detail', $detailData);
					$this->db->insert_batch('bq_detail_header', $detailDataHeader);
				}
				if(!empty($data['ListDetailKompSub'])){
					$this->db->insert_batch('bq_detail_detail', $detailData2);
					$this->db->insert_batch('bq_detail_header', $detailData2Header);
				}
				if(!empty($data['ListDetailKompSingle'])){
					$this->db->insert_batch('bq_detail_detail', $detailData3);
					$this->db->insert_batch('bq_detail_header', $detailData3Header);
				}
				if(!empty($data['ListDetailKompSub2'])){
					$this->db->insert_batch('bq_detail_detail', $detailData4);
					$this->db->insert_batch('bq_detail_header', $detailData4Header);
				}

				$this->db->where('no_ipp', $IPPNum);
				$this->db->update('production', $UpdateIPP);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add structure bq data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Add structure bq data success. Thanks ...',
					'status'	=> 1
				);
				history('Add Structure BQ with code : '.$kode_bq);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$ListBQipp		= $this->db->query("SELECT no_ipp FROM bq_header")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['no_ipp'];
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

			$ListIPP		= $this->db->query("SELECT no_ipp FROM production WHERE deleted='N' AND status='WAITING STRUCTURE BQ' AND no_ipp NOT IN ".$dtImplode." ORDER BY no_ipp ASC")->result_array();
			$ListSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC")->result_array();
			$ListHelp		= $this->db->query("SELECT name FROM list_help WHERE sts='Y' AND group_by='order type'")->result_array();

			$data = array(
				'title'			=> 'New Structure BQ',
				'action'		=> 'bq',
				'ListIPP'		=> $ListIPP,
				'ListSeries'		=> $ListSeries,
				'ListOrder'		=> $ListHelp
			);
			$this->load->view('Machine/bq',$data);
		}
	}

	public function bq2(){
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');

			$ipp_no = $data['no_ipp'];

			if(!empty($data['ListDetailKomp'])){
				$productDetKomp = $data['ListDetailKomp'];
			}
			if(!empty($data['ListDetailKompSub'])){
				$productDetKomSub = $data['ListDetailKompSub'];
			}
			if(!empty($data['ListDetailKompSingle'])){
				$productDetKomSigle = $data['ListDetailKompSingle'];
			}
			if(!empty($data['ListDetailKompSub2'])){
				$productDetKomSub2 = $data['ListDetailKompSub2'];
			}

			$kode_bq	= "BQ-".$ipp_no;

			$Data_Insert		= array(
				'id_bq'			=> $kode_bq,
				'no_ipp'		=> $data['no_ipp'],
				'series'		=> "",
				'order_type'	=> $data['order_type'],
				'created_date'	=> date('Y-m-d H:i:s'),
				'created_by'	=> $this->session->userdata['ORI_User']['username']
			);
			// echo "<pre>";
			// print_r($Data_Insert);

			// if(!empty($data['ListDetailKomp'])){
				// print_r($productDetKomp);
			// }
			// if(!empty($data['ListDetailKompSub'])){
				// print_r($productDetKomSub);
			// }
			// if(!empty($data['ListDetailKompSingle'])){
				// print_r($productDetKomSigle);
			// }
			// if(!empty($data['ListDetailKompSub2'])){
				// print_r($productDetKomSub2);
			// }
			// exit;

			if(!empty($data['ListDetailKomp'])){
				$detailDataHeader	= array();
				$a1					= 0;
				$detailData	= array();
				$lopp 		= 0;
				foreach($productDetKomp AS $val => $valx){
					$a1++;

					$wherePN = floatval(substr($valx['series'], 3,2));
					$whereLN = floatval(substr($valx['series'], 6,3));
					
					$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
					if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
						$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
					}
					if($valx['id_category'] == 'branch joint'){
						$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
					}
					$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
					$restSer = $this->db->query($qSeries)->result();
					// echo $qSeries."<br>";

					$dataKR4 = sprintf('%03s',$a1);
					$idDetail = sprintf('%03s',$a1);
					$detailDataHeader[$val]['id_bq'] 			= $kode_bq;
					$detailDataHeader[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail."-SP";
						$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailDataHeader[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailDataHeader[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailDataHeader[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailDataHeader[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailDataHeader[$val]['id_category'] 		= $valx['id_category'];
					$detailDataHeader[$val]['series'] 			= $valx['series'];
					$detailDataHeader[$val]['qty'] 				= $valx['qty'];
					$detailDataHeader[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailDataHeader[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailDataHeader[$val]['length'] 			= $valx['length'];
					$detailDataHeader[$val]['thickness'] 		= $valx['thickness'];
					$detailDataHeader[$val]['sudut'] 			= $valx['sudut'];
					$detailDataHeader[$val]['id_standard'] 	= $valx['id_standard'];
					$detailDataHeader[$val]['type'] 			= $valx['type'];

					$detailDataHeader[$val]['man_power'] 		= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
					$detailDataHeader[$val]['id_mesin'] 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
					$detailDataHeader[$val]['total_time'] 		= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$detailDataHeader[$val]['man_hours'] 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

					$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:''; 
					$detailDataHeader[$val]['pe_direct_labour'] 			= pe_direct_labour();
					$detailDataHeader[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
					$detailDataHeader[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
					$detailDataHeader[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
					$detailDataHeader[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
					$detailDataHeader[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
					$detailDataHeader[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
					$detailDataHeader[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
					$detailDataHeader[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
					$detailDataHeader[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();

					for($no=1; $no <= $valx['qty']; $no++){
						$lopp++;
						$detailData[$lopp]['id_bq'] 		= $kode_bq;
						$detailData[$lopp]['id_bq_header'] 	= $kode_bq."-".$idDetail."-SP";
							$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData[$lopp]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData[$lopp]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData[$lopp]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData[$lopp]['series'] 		= $valx['series'];
						$detailData[$lopp]['id_category'] 	= $valx['id_category'];
						$detailData[$lopp]['diameter_1'] 	= $valx['diameter_1'];
						$detailData[$lopp]['diameter_2'] 	= $valx['diameter_2'];
						$detailData[$lopp]['length'] 		= $valx['length'];
						$detailData[$lopp]['thickness'] 	= $valx['thickness'];
						$detailData[$lopp]['sudut'] 		= $valx['sudut'];
						$detailData[$lopp]['id_standard'] 	= $valx['id_standard'];
						$detailData[$lopp]['type'] 			= $valx['type'];
						$detailData[$lopp]['qty'] 			= $valx['qty'];
						$detailData[$lopp]['product_ke'] 	= $no;
					}
				}

				// print_r($detailData);
				// print_r($detailDataHeader);
			}
			// exit;
			if(!empty($data['ListDetailKompSub'])){
				$detailData2	= array();
				$lopp2 = 0;
				$detailData2Header	= array();
				$a2	= 0;

				foreach($productDetKomSub AS $val => $valx){
					$a2++;

					$wherePN = floatval(substr($valx['series'], 3,2));
					$whereLN = floatval(substr($valx['series'], 6,3));
					
					// $wherePlus = '';
					// if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
					// 	$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
					// }
					// $qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
					// $restSer = $this->db->query($qSeries)->result();
					$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
					if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
						$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
					}
					if($valx['id_category'] == 'branch joint'){
						$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
					}
					$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
					$restSer = $this->db->query($qSeries)->result();
					// echo $qSeries."<br>";

					$dataKR4 = sprintf('%03s',$a2);
					$idDetail = sprintf('%03s',$a2);
					$detailData2Header[$val]['id_bq'] 			= $kode_bq;
					$detailData2Header[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail."-SPSL";
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData2Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData2Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData2Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData2Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData2Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData2Header[$val]['series'] 			= $valx['series'];
					$detailData2Header[$val]['qty'] 			= $valx['qty'];
					$detailData2Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData2Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData2Header[$val]['length'] 			= $valx['length'];
					$detailData2Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData2Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData2Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData2Header[$val]['type'] 			= $valx['type'];

					$detailData2Header[$val]['man_power'] 		= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
					$detailData2Header[$val]['id_mesin'] 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
					$detailData2Header[$val]['total_time'] 		= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$detailData2Header[$val]['man_hours'] 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

					$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
					$detailData2Header[$val]['pe_direct_labour'] 			= pe_direct_labour();
					$detailData2Header[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
					$detailData2Header[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
					$detailData2Header[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
					$detailData2Header[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
					$detailData2Header[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
					$detailData2Header[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
					$detailData2Header[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
					$detailData2Header[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
					$detailData2Header[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();

					for($no=1; $no <= $valx['qty']; $no++){
						$lopp2++;
						$detailData2[$lopp2]['id_bq'] 			= $kode_bq;
						$detailData2[$lopp2]['id_bq_header'] 	= $kode_bq."-".$idDetail."-SPSL";
						$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData2[$lopp2]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData2[$lopp2]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData2[$lopp2]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData2[$lopp2]['id_category'] 	= $valx['id_category'];
						$detailData2[$lopp2]['series'] 		= $valx['series'];
						$detailData2[$lopp2]['diameter_1'] 		= $valx['diameter_1'];
						$detailData2[$lopp2]['diameter_2'] 		= $valx['diameter_2'];
						$detailData2[$lopp2]['length'] 			= $valx['length'];
						$detailData2[$lopp2]['thickness'] 		= $valx['thickness'];
						$detailData2[$lopp2]['sudut'] 			= $valx['sudut'];
						$detailData2[$lopp2]['id_standard'] 	= $valx['id_standard'];
						$detailData2[$lopp2]['type'] 			= $valx['type'];
						$detailData2[$lopp2]['qty'] 			= $valx['qty'];
						$detailData2[$lopp2]['product_ke'] 		= $no;
					}
				}
				// print_r($detailData2);
				// print_r($detailData2Header);
			}

			if(!empty($data['ListDetailKompSingle'])){
				$detailData3	= array();
				$lopp3 = 0;
				$detailData3Header	= array();
				$a3	= 0;

				foreach($productDetKomSigle AS $val => $valx){
					$a3++;

					$wherePN = floatval(substr($valx['series'], 3,2));
					$whereLN = floatval(substr($valx['series'], 6,3));
					
					// $wherePlus = '';
					// if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
					// 	$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
					// }
					// $qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
					// $restSer = $this->db->query($qSeries)->result();
					$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
					if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
						$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
					}
					if($valx['id_category'] == 'branch joint'){
						$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
					}
					$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
					$restSer = $this->db->query($qSeries)->result();
					// echo $qSeries."<br>";

					$dataKR4 = sprintf('%03s',$a3);
					$idDetail = sprintf('%03s',$a3);
					$detailData3Header[$val]['id_bq'] 			= $kode_bq;
					$detailData3Header[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail."-CP";
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData3Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData3Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData3Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData3Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData3Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData3Header[$val]['series'] 			= $valx['series'];
					$detailData3Header[$val]['qty'] 			= $valx['qty'];
					$detailData3Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData3Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData3Header[$val]['length'] 			= $valx['length'];
					$detailData3Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData3Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData3Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData3Header[$val]['type'] 			= $valx['type'];

					$detailData3Header[$val]['man_power'] 		= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
					$detailData3Header[$val]['id_mesin'] 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
					$detailData3Header[$val]['total_time'] 		= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$detailData3Header[$val]['man_hours'] 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

					$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
					$detailData3Header[$val]['pe_direct_labour'] 			= pe_direct_labour();
					$detailData3Header[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
					$detailData3Header[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
					$detailData3Header[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
					$detailData3Header[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
					$detailData3Header[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
					$detailData3Header[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
					$detailData3Header[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
					$detailData3Header[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
					$detailData3Header[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();

					for($no=1; $no <= $valx['qty']; $no++){
						$lopp3++;
						$detailData3[$lopp3]['id_bq'] 			= $kode_bq;
						$detailData3[$lopp3]['id_bq_header'] 	= $kode_bq."-".$idDetail."-CP";
						$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData3[$lopp3]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData3[$lopp3]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData3[$lopp3]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData3[$lopp3]['id_category'] 	= $valx['id_category'];
						$detailData3[$lopp3]['series'] 			= $valx['series'];
						$detailData3[$lopp3]['diameter_1'] 		= $valx['diameter_1'];
						$detailData3[$lopp3]['diameter_2'] 		= $valx['diameter_2'];
						$detailData3[$lopp3]['length'] 			= $valx['length'];
						$detailData3[$lopp3]['thickness'] 		= $valx['thickness'];
						$detailData3[$lopp3]['sudut'] 			= $valx['sudut'];
						$detailData3[$lopp3]['id_standard'] 	= $valx['id_standard'];
						$detailData3[$lopp3]['type'] 			= $valx['type'];
						$detailData3[$lopp3]['qty'] 			= $valx['qty'];
						$detailData3[$lopp3]['product_ke'] 		= $no;
					}
				}
				// print_r($detailData3);
				// print_r($detailData3Header);
			}

			if(!empty($data['ListDetailKompSub2'])){
				$detailData4	= array();
				$lopp4 = 0;
				$detailData4Header	= array();
				$a4	= 0;

				foreach($productDetKomSub2 AS $val => $valx){
					$a4++;

					$wherePN = floatval(substr($valx['series'], 3,2));
					$whereLN = floatval(substr($valx['series'], 6,3));
					
					// $wherePlus = '';
					// if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
					// 	$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
					// }
					// $qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
					// $restSer = $this->db->query($qSeries)->result();
					$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
					if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
						$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
					}
					if($valx['id_category'] == 'branch joint'){
						$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
					}
					$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
					$restSer = $this->db->query($qSeries)->result();
					// echo $qSeries."<br>";

					$dataKR4 = sprintf('%03s',$a4);
					$idDetail = sprintf('%03s',$a4);
					$detailData4Header[$val]['id_bq'] 			= $kode_bq;
					$detailData4Header[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail."-CPSL";
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData4Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData4Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData4Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData4Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData4Header[$val]['series'] 			= $valx['series'];
					$detailData4Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData4Header[$val]['qty'] 			= $valx['qty'];
					$detailData4Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData4Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData4Header[$val]['length'] 			= $valx['length'];
					$detailData4Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData4Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData4Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData4Header[$val]['type'] 			= $valx['type'];

					$detailData4Header[$val]['man_power'] 		= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
					$detailData4Header[$val]['id_mesin'] 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
					$detailData4Header[$val]['total_time'] 		= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$detailData4Header[$val]['man_hours'] 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

					$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
					$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
					$detailData4Header[$val]['pe_direct_labour'] 			= pe_direct_labour();
					$detailData4Header[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
					$detailData4Header[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
					$detailData4Header[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
					$detailData4Header[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
					$detailData4Header[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
					$detailData4Header[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
					$detailData4Header[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
					$detailData4Header[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
					$detailData4Header[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();

					for($no=1; $no <= $valx['qty']; $no++){
						$lopp4++;
						$detailData4[$lopp4]['id_bq'] 			= $kode_bq;
						$detailData4[$lopp4]['id_bq_header'] 	= $kode_bq."-".$idDetail."-CPSL";
						$dtEN		= explode('-', $valx['id_delivery']);
							$dataID1	= $dtEN[0];
							$dataID2	= sprintf('%02s',$dtEN[1]);
						$detailData4[$lopp4]['id_delivery'] 	= $dataID1."-".$dataID2;
							$dtEX		= explode('-', $valx['sub_delivery']);
							$dataKR1	= $dtEX[0];
							$dataKR2	= sprintf('%02s',$dtEX[1]);
							$dataKR3	= sprintf('%02s',$dtEX[2]);
						$detailData4[$lopp4]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
						$detailData4[$lopp4]['sts_delivery'] 	= $valx['sts_delivery'];
						$detailData4[$lopp4]['id_category'] 	= $valx['id_category'];
						$detailData4[$lopp4]['series'] 			= $valx['series'];
						$detailData4[$lopp4]['diameter_1'] 		= $valx['diameter_1'];
						$detailData4[$lopp4]['diameter_2'] 		= $valx['diameter_2'];
						$detailData4[$lopp4]['length'] 			= $valx['length'];
						$detailData4[$lopp4]['thickness'] 		= $valx['thickness'];
						$detailData4[$lopp4]['sudut'] 			= $valx['sudut'];
						$detailData4[$lopp4]['id_standard'] 	= $valx['id_standard'];
						$detailData4[$lopp4]['type'] 			= $valx['type'];
						$detailData4[$lopp4]['qty'] 			= $valx['qty'];
						$detailData4[$lopp4]['product_ke'] 		= $no;
					}
				}
				// print_r($detailData4);
				// print_r($detailData4Header);
			}

			// exit;

			$IPPNum	= $data['no_ipp'];
			$UpdateIPP	= array(
				'status'	=> 'WAITING ESTIMATION PROJECT'
			);

			$this->db->trans_start();
				$this->db->insert('bq_header', $Data_Insert);
				if(!empty($data['ListDetailKomp'])){

					$this->db->insert_batch('bq_detail_header', $detailDataHeader);
					$this->db->insert_batch('bq_detail_detail', $detailData);
				}
				if(!empty($data['ListDetailKompSub'])){

					$this->db->insert_batch('bq_detail_header', $detailData2Header);
					$this->db->insert_batch('bq_detail_detail', $detailData2);
				}
				if(!empty($data['ListDetailKompSingle'])){

					$this->db->insert_batch('bq_detail_header', $detailData3Header);
					$this->db->insert_batch('bq_detail_detail', $detailData3);
				}
				if(!empty($data['ListDetailKompSub2'])){

					$this->db->insert_batch('bq_detail_header', $detailData4Header);
					$this->db->insert_batch('bq_detail_detail', $detailData4);
				}

				// $this->db->where('no_ipp', $IPPNum);
				// $this->db->update('production', $UpdateIPP);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add structure bq data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Add structure bq data success. Thanks ...',
					'status'	=> 1
				);
				history('Add Structure BQ with code : '.$kode_bq);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			
			$no_ipp = $this->uri->segment(3);

			$ListBQipp		= $this->db->query("SELECT no_ipp FROM bq_header UNION SELECT no_ipp FROM draf_bq_header")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['no_ipp'];
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

			$ListIPP		= $this->db->query("SELECT no_ipp, nm_customer, project FROM production WHERE deleted='N' AND status='WAITING STRUCTURE BQ' AND no_ipp NOT IN ".$dtImplode." ORDER BY no_ipp ASC")->result_array();
			$ListSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC")->result_array();
			$ListHelp		= $this->db->query("SELECT name FROM list_help WHERE sts='Y' AND group_by='order type'")->result_array();

			$data = array(
				'title'			=> 'New Structure BQ',
				'action'		=> 'bq',
				'ListIPP'		=> $ListIPP,
				'no_ipp' 		=> $no_ipp,
				'ListSeries'	=> $ListSeries,
				'ListOrder'		=> $ListHelp
			);
			$this->load->view('Machine/bq2',$data);
		}
	}

	//SaveDraf
	public function saveDraf(){
		if($this->input->post()){
			$Arr_Kembali		= array();
			$data				= $this->input->post();
			$YM	= date('ym');

			$ipp_no = $data['no_ipp'];

			if(!empty($data['ListDetailKomp'])){
				$productDetKomp = $data['ListDetailKomp'];
			}
			if(!empty($data['ListDetailKompSub'])){
				$productDetKomSub = $data['ListDetailKompSub'];
			}
			if(!empty($data['ListDetailKompSingle'])){
				$productDetKomSigle = $data['ListDetailKompSingle'];
			}
			if(!empty($data['ListDetailKompSub2'])){
				$productDetKomSub2 = $data['ListDetailKompSub2'];
			}

			$kode_bq	= "BQ-".$ipp_no;

			$Data_Insert		= array(
				'id_bq'			=> $kode_bq,
				'no_ipp'		=> $data['no_ipp'],
				'series'		=> "",
				'order_type'	=> $data['order_type'],
				'created_date'	=> date('Y-m-d H:i:s'),
				'created_by'	=> $this->session->userdata['ORI_User']['username']
			);
			// echo "<pre>";
			// print_r($Data_Insert);

			// if(!empty($data['ListDetailKomp'])){
				// print_r($productDetKomp);
			// }
			// if(!empty($data['ListDetailKompSub'])){
				// print_r($productDetKomSub);
			// }
			// if(!empty($data['ListDetailKompSingle'])){
				// print_r($productDetKomSigle);
			// }
			// if(!empty($data['ListDetailKompSub2'])){
				// print_r($productDetKomSub2);
			// }
			// exit;

			if(!empty($data['ListDetailKomp'])){
				$detailDataHeader	= array();
				$a1					= 0;
				foreach($productDetKomp AS $val => $valx){
					$a1++;
					$dataKR4 = sprintf('%03s',$a1);
					$idDetail = sprintf('%03s',$a1);
					$detailDataHeader[$val]['id_bq'] 			= $kode_bq;
					$detailDataHeader[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail;
						$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailDataHeader[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailDataHeader[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailDataHeader[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailDataHeader[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailDataHeader[$val]['id_category'] 		= $valx['id_category'];
					$detailDataHeader[$val]['series'] 			= $valx['series'];
					$detailDataHeader[$val]['qty'] 				= $valx['qty'];
					$detailDataHeader[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailDataHeader[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailDataHeader[$val]['length'] 			= $valx['length'];
					$detailDataHeader[$val]['thickness'] 		= $valx['thickness'];
					$detailDataHeader[$val]['sudut'] 			= $valx['sudut'];
					$detailDataHeader[$val]['id_standard'] 	= $valx['id_standard'];
					$detailDataHeader[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailDataHeader);
			}
			// exit;
			if(!empty($data['ListDetailKompSub'])){
				$detailData2Header	= array();
				$a2	= 0;

				foreach($productDetKomSub AS $val => $valx){
					$a2++;
					$dataKR4 = sprintf('%03s',$a2);
					$idDetail = sprintf('%03s',$a2);
					$detailData2Header[$val]['id_bq'] 			= $kode_bq;
					$detailData2Header[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail;
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData2Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData2Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData2Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData2Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData2Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData2Header[$val]['series'] 			= $valx['series'];
					$detailData2Header[$val]['qty'] 			= $valx['qty'];
					$detailData2Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData2Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData2Header[$val]['length'] 			= $valx['length'];
					$detailData2Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData2Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData2Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData2Header[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailData2Header);
			}

			if(!empty($data['ListDetailKompSingle'])){
				$detailData3Header	= array();
				$a3	= 0;

				foreach($productDetKomSigle AS $val => $valx){
					$a3++;
					$dataKR4 = sprintf('%03s',$a3);
					$idDetail = sprintf('%03s',$a3);
					$detailData3Header[$val]['id_bq'] 			= $kode_bq;
					$detailData3Header[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail;
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData3Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData3Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData3Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData3Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData3Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData3Header[$val]['series'] 			= $valx['series'];
					$detailData3Header[$val]['qty'] 			= $valx['qty'];
					$detailData3Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData3Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData3Header[$val]['length'] 			= $valx['length'];
					$detailData3Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData3Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData3Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData3Header[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailData3Header);
			}

			if(!empty($data['ListDetailKompSub2'])){
				$detailData4Header	= array();
				$a4	= 0;
				foreach($productDetKomSub2 AS $val => $valx){
					$a4++;
					$dataKR4 = sprintf('%03s',$a4);
					$idDetail = sprintf('%03s',$a4);
					$detailData4Header[$val]['id_bq'] 			= $kode_bq;
					$detailData4Header[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail;
					$dtEN		= explode('-', $valx['id_delivery']);
						$dataID1	= $dtEN[0];
						$dataID2	= sprintf('%02s',$dtEN[1]);
					$detailData4Header[$val]['id_delivery'] 	= $dataID1."-".$dataID2;
						$dtEX		= explode('-', $valx['sub_delivery']);
						$dataKR1	= $dtEX[0];
						$dataKR2	= sprintf('%02s',$dtEX[1]);
						$dataKR3	= sprintf('%02s',$dtEX[2]);
					$detailData4Header[$val]['sub_delivery'] 	= $dataKR1."-".$dataKR2."-".$dataKR3;
					$detailData4Header[$val]['no_komponen'] 		= $dataKR1."-".$dataKR2."-".$dataKR3."/".$dataKR4;
					$detailData4Header[$val]['sts_delivery'] 	= $valx['sts_delivery'];
					$detailData4Header[$val]['series'] 			= $valx['series'];
					$detailData4Header[$val]['id_category'] 	= $valx['id_category'];
					$detailData4Header[$val]['qty'] 			= $valx['qty'];
					$detailData4Header[$val]['diameter_1'] 		= $valx['diameter_1'];
					$detailData4Header[$val]['diameter_2'] 		= $valx['diameter_2'];
					$detailData4Header[$val]['length'] 			= $valx['length'];
					$detailData4Header[$val]['thickness'] 		= $valx['thickness'];
					$detailData4Header[$val]['sudut'] 			= $valx['sudut'];
					$detailData4Header[$val]['id_standard'] 	= $valx['id_standard'];
					$detailData4Header[$val]['type'] 			= $valx['type'];
				}
				// print_r($detailData4Header);
			}

			// exit;

			$IPPNum	= $data['no_ipp'];
			$UpdateIPP	= array(
				'status'	=> 'WAITING ESTIMATION PROJECT'
			);

			$this->db->trans_start();
				$this->db->insert('draf_bq_header', $Data_Insert);
				if(!empty($data['ListDetailKomp'])){
					$this->db->insert_batch('draf_bq_detail_header', $detailDataHeader);
				}
				if(!empty($data['ListDetailKompSub'])){
					$this->db->insert_batch('draf_bq_detail_header', $detailData2Header);
				}
				if(!empty($data['ListDetailKompSingle'])){
					$this->db->insert_batch('draf_bq_detail_header', $detailData3Header);
				}
				if(!empty($data['ListDetailKompSub2'])){
					$this->db->insert_batch('draf_bq_detail_header', $detailData4Header);
				}

			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Add structure bq to Draf data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Add structure bq to Draf data success. Thanks ...',
					'status'	=> 1
				);
				history('Add Draf Structure BQ with code : '.$kode_bq);
			}

			echo json_encode($Arr_Kembali);
		}
	}

	//SavedDraf
	public function SavedDraf(){
		$Arr_Kembali		= array();
		$data				= $this->input->post();
		$kode_bq			= $data['id_bq'];

		$data1 = array();
		if(!empty($data['DetailBq'])){
			$data1 = $data['DetailBq'];
		}
		$data2 = array();
		if(!empty($data['ListDetail'])){
			$data2 = $data['ListDetail'];
		}

		$dataAll = array_merge($data1, $data2);

		// print_r($dataAll);
		// exit;

		$ArrDetAll 	= array();
		$no 		= 0;
		foreach($dataAll AS $val => $valx){
			$no++;
			$dataKR4 	= sprintf('%03s',$no);
			$idDetail 	= sprintf('%03s',$no);
			$ArrDetAll[$val]['id_bq'] 			= $kode_bq;
			$ArrDetAll[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail;
			$ArrDetAll[$val]['id_delivery'] 	= $valx['id_delivery'];
			$ArrDetAll[$val]['sub_delivery'] 	= $valx['sub_delivery'];
			$ArrDetAll[$val]['no_komponen'] 	= $valx['sub_delivery']."/".$dataKR4;
			$ArrDetAll[$val]['sts_delivery'] 	= $valx['sts_delivery'];
			$ArrDetAll[$val]['series'] 			= $valx['series'];
			$ArrDetAll[$val]['id_category'] 	= $valx['id_category'];
			$ArrDetAll[$val]['qty'] 			= $valx['qty'];
			$ArrDetAll[$val]['diameter_1'] 		= $valx['diameter_1'];
			$ArrDetAll[$val]['diameter_2'] 		= $valx['diameter_2'];
			$ArrDetAll[$val]['length'] 			= $valx['length'];
			$ArrDetAll[$val]['thickness'] 		= $valx['thickness'];
			$ArrDetAll[$val]['sudut'] 			= $valx['sudut'];
			$ArrDetAll[$val]['id_standard'] 	= $valx['id_standard'];
			$ArrDetAll[$val]['type'] 			= $valx['type'];
		}

		// print_r($ArrDetAll);

		// exit;



		$this->db->trans_start();
			$this->db->delete('draf_bq_detail_header', array('id_bq' => $kode_bq));
			$this->db->insert_batch('draf_bq_detail_header', $ArrDetAll);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Save structure bq to Draf data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Save structure bq to Draf data success. Thanks ...',
				'status'	=> 1
			);
			history('Save Draf Structure BQ with code : '.$kode_bq);
		}

		echo json_encode($Arr_Kembali);

	}

	public function SavedBQ(){
		$Arr_Kembali		= array();
		$data				= $this->input->post();
		$kode_bq			= $data['id_bq'];

		$exKode				= explode('-', $kode_bq);
		$IPPNum				= $exKode[1];


		$data1 = array();
		if(!empty($data['DetailBq'])){
			$data1 = $data['DetailBq'];
		}
		$data2 = array();
		if(!empty($data['ListDetail'])){
			$data2 = $data['ListDetail'];
		}

		$dataAll = array_merge($data1, $data2);

		// print_r($dataAll);
		// exit;

		$ArrDetAll 	= array();
		$noX 		= 0;
		$ArrDetDet	= array();
		$lopp = 0;
		foreach($dataAll AS $val => $valx){
			$noX++;

			$wherePN = floatval(substr($valx['series'], 3,2));
			$whereLN = floatval(substr($valx['series'], 6,3));
			
			// $wherePlus = '';
			// if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
			// 	$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
			// }
			// $qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
			// $restSer = $this->db->query($qSeries)->result();
			$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
			if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
				$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
			}
			if($valx['id_category'] == 'branch joint'){
				$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
			}
			$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
			$restSer = $this->db->query($qSeries)->result();
			// echo $qSeries."<br>";
			
			$dataKR4 	= sprintf('%03s',$noX);
			$idDetail 	= sprintf('%03s',$noX);
			$ArrDetAll[$val]['id_bq'] 			= $kode_bq;
			$ArrDetAll[$val]['id_bq_header'] 	= $kode_bq."-".$idDetail;
			$ArrDetAll[$val]['id_delivery'] 	= $valx['id_delivery'];
			$ArrDetAll[$val]['sub_delivery'] 	= $valx['sub_delivery'];
			$ArrDetAll[$val]['no_komponen'] 	= $valx['sub_delivery']."/".$dataKR4;
			$ArrDetAll[$val]['sts_delivery'] 	= $valx['sts_delivery'];
			$ArrDetAll[$val]['series'] 			= $valx['series'];
			$ArrDetAll[$val]['id_category'] 	= $valx['id_category'];
			$ArrDetAll[$val]['qty'] 			= $valx['qty'];
			$ArrDetAll[$val]['diameter_1'] 		= $valx['diameter_1'];
			$ArrDetAll[$val]['diameter_2'] 		= $valx['diameter_2'];
			$ArrDetAll[$val]['length'] 			= $valx['length'];
			$ArrDetAll[$val]['thickness'] 		= $valx['thickness'];
			$ArrDetAll[$val]['sudut'] 			= $valx['sudut'];
			$ArrDetAll[$val]['id_standard'] 	= $valx['id_standard'];
			$ArrDetAll[$val]['type'] 			= $valx['type'];

			$ArrDetAll[$val]['man_power'] 		= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
			$ArrDetAll[$val]['id_mesin'] 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
			$ArrDetAll[$val]['total_time'] 		= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
			$ArrDetAll[$val]['man_hours'] 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

			$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
			$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
			$ArrDetAll[$val]['pe_direct_labour'] 			= pe_direct_labour();
			$ArrDetAll[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
			$ArrDetAll[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
			$ArrDetAll[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
			$ArrDetAll[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
			$ArrDetAll[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
			$ArrDetAll[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
			$ArrDetAll[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
			$ArrDetAll[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
			$ArrDetAll[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();
			
			for($no=1; $no <= $valx['qty']; $no++){
				$lopp++;
				$ArrDetDet[$lopp]['id_bq'] 			= $kode_bq;
				$ArrDetDet[$lopp]['id_bq_header'] 	= $kode_bq."-".$idDetail;
				$ArrDetDet[$lopp]['id_delivery'] 	= $valx['id_delivery'];
				$ArrDetDet[$lopp]['sub_delivery'] 	= $valx['sub_delivery'];
				$ArrDetDet[$lopp]['sts_delivery'] 	= $valx['sts_delivery'];
				$ArrDetDet[$lopp]['id_category'] 	= $valx['id_category'];
				$ArrDetDet[$lopp]['series'] 		= $valx['series'];
				$ArrDetDet[$lopp]['diameter_1'] 	= $valx['diameter_1'];
				$ArrDetDet[$lopp]['diameter_2'] 	= $valx['diameter_2'];
				$ArrDetDet[$lopp]['length'] 		= $valx['length'];
				$ArrDetDet[$lopp]['thickness'] 		= $valx['thickness'];
				$ArrDetDet[$lopp]['sudut'] 			= $valx['sudut'];
				$ArrDetDet[$lopp]['id_standard'] 	= $valx['id_standard'];
				$ArrDetDet[$lopp]['type'] 			= $valx['type'];
				$ArrDetDet[$lopp]['qty'] 			= $valx['qty'];
				$ArrDetDet[$lopp]['product_ke'] 	= $no;
			}
		}

		$UpdateIPP	= array(
			'status'	=> 'WAITING ESTIMATION PROJECT'
		);

		$this->db->trans_start();
			$qDrafToBq = "	INSERT INTO
								bq_header
							(id_bq, no_ipp, series, order_type, ket, estimasi, rev, created_by, created_date, modified_by, modified_date, cancel_by, cancel_date, est_by, est_date)
							SELECT
								id_bq, no_ipp, series, order_type, ket, estimasi, rev, created_by, created_date, modified_by, modified_date, cancel_by, cancel_date, est_by, est_date
							FROM
								draf_bq_header
							WHERE id_bq = '".$kode_bq."' LIMIT 1
							";
			$this->db->query($qDrafToBq);
			$this->db->insert_batch('bq_detail_header', $ArrDetAll);
			$this->db->insert_batch('bq_detail_detail', $ArrDetDet);
			$this->db->delete('draf_bq_header', array('id_bq' => $kode_bq));
			$this->db->delete('draf_bq_detail_header', array('id_bq' => $kode_bq));

			// $this->db->where('no_ipp', $IPPNum);
			// $this->db->update('production', $UpdateIPP);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Save structure bq data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Save structure bqdata success. Thanks ...',
				'status'	=> 1
			);
			history('Save Structure BQ by Draf with code : '.$kode_bq);
		}

		echo json_encode($Arr_Kembali);

	}

	public function getSeries(){
		$sqlSup		= "SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Series</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['kode_group']."'>".strtoupper($valx['kode_group'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getSeriesL(){
		$dataL = $this->input->post('series');

		$dataEx = explode("," , $dataL);

		// $ListBQipp		= $this->db->query("SELECT no_ipp FROM bq_header")->result_array();
				$dtListArray = array();
				foreach($dataEx AS $val){
					$dtListArray[$val] = $val;
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

		// echo $dtImplode;
		// print_r($dataEx);
		// exit;

		$sqlSup		= "SELECT * FROM draf_bq_detail_header WHERE series IN ".$dtImplode." GROUP BY series";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Series</option>";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['series']."'>".strtoupper($val['series'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getSeriesM(){
		$dataL = $this->input->post('series');

		$dataEx = explode("," , $dataL);

				$dtListArray = array();
				foreach($dataEx AS $val){
					$dtListArray[$val] = $val;
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

		$sqlSup		= "SELECT * FROM bq_detail_header WHERE series IN ".$dtImplode." GROUP BY series";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Series</option>";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['series']."'>".strtoupper($val['series'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getDelivery(){
		$dataL = $this->input->post('series');

		$id_bq = $this->input->post('id_bq');

		$sqlSup		= "SELECT id_delivery FROM draf_bq_detail_header WHERE series = '".$dataL."' AND id_bq = '".$id_bq."' GROUP BY id_delivery";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option>Iso M</option>";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['id_delivery']."'>".strtoupper($val['id_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getDeliveryM(){
		$dataL = $this->input->post('series');
		$id_bq = $this->input->post('id_bq');
		$sqlSup		= "SELECT id_delivery FROM bq_detail_header WHERE series = '".$dataL."'  AND id_bq = '".$id_bq."' GROUP BY id_delivery";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option>Iso M</option>";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['id_delivery']."'>".strtoupper($val['id_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getDeliveryX(){
		$dataL = $this->input->post('series');
		$id_bq = $this->input->post('id_bq');
		$sqlSup		= "SELECT id_delivery FROM draf_bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY id_delivery";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option>Iso M</option>";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['id_delivery']."'>".strtoupper($val['id_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getDeliveryMX(){
		$dataL = $this->input->post('series');
		$id_bq = $this->input->post('id_bq');
		$sqlSup		= "SELECT id_delivery FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY id_delivery";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option>Iso M</option>";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['id_delivery']."'>".strtoupper($val['id_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getSubDelivery(){
		$dataL = $this->input->post('series');
		$dataM = $this->input->post('id_delivery');
		$id_bq = $this->input->post('id_bq');

		$sqlSup		= "SELECT sub_delivery FROM draf_bq_detail_header WHERE series = '".$dataL."' AND id_delivery = '".$dataM."' AND id_bq = '".$id_bq."'  GROUP BY sub_delivery";
		// echo $sqlSup; exit;
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['sub_delivery']."'>".strtoupper($val['sub_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getSubDeliveryM(){
		$dataL = $this->input->post('series');
		$dataM = $this->input->post('id_delivery');
		$id_bq = $this->input->post('id_bq');

		$sqlSup		= "SELECT sub_delivery FROM bq_detail_header WHERE series = '".$dataL."' AND id_delivery = '".$dataM."' AND id_bq = '".$id_bq."'  GROUP BY sub_delivery";
		// echo $sqlSup; exit;
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['sub_delivery']."'>".strtoupper($val['sub_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getSubDeliveryX(){
		$dataL = $this->input->post('series');
		$dataM = $this->input->post('id_delivery');
		$id_bq = $this->input->post('id_bq');

		$sqlSup		= "SELECT sub_delivery FROM draf_bq_detail_header WHERE id_bq = '".$id_bq."'  GROUP BY sub_delivery";
		// echo $sqlSup; exit;
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['sub_delivery']."'>".strtoupper($val['sub_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}
	
	public function getSubDeliveryMX(){
		$dataL = $this->input->post('series');
		$dataM = $this->input->post('id_delivery');
		$id_bq = $this->input->post('id_bq');

		$sqlSup		= "SELECT sub_delivery FROM bq_detail_header WHERE id_bq = '".$id_bq."'  GROUP BY sub_delivery";
		// echo $sqlSup; exit;
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "";
		foreach($restSup AS $val){
			$option .= "<option value='".$val['sub_delivery']."'>".strtoupper($val['sub_delivery'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function BqUpdated(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_bq			= $data['id_bq'];
		
		if(!empty($data['DetailBq'])){
			$DataBQ			= $data['DetailBq'];
		}
		
		if(!empty($data['ListDetail'])){
			$data2 = $data['ListDetail'];
		}

		$ToHistBqHeader		= $this->db->query("SELECT * FROM bq_header WHERE id_bq='".$id_bq."' ")->result_array();
		$ToHistBqDetHeader	= $this->db->query("SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' ")->result_array();
		$ToHistBqDetDetail	= $this->db->query("SELECT * FROM bq_detail_detail WHERE id_bq='".$id_bq."' ")->result_array();

		// echo "SELECT * FROM bq_detail_detail WHERE id_bq='".$id_bq."'";
		// print_r($DataBQ);
		// print_r($data2);
		// exit();

		if(!empty($data['ListDetail'])){
			$DataHeader = $this->db->query("SELECT MAX(id_bq_header) AS maximalA FROM bq_detail_header WHERE id_bq = '".$id_bq."' ")->result();
			
			$numX 		= 0;
			if(!empty($DataHeader)){
				$nst		= explode('-', $DataHeader[0]->maximalA);
				$numX 		= ltrim($nst[2], '0');
			}
			
			// echo $numX; exit;
			$ArrInsertNew	= array();
			$Loop = 0;

			$ArrInsertDetDetail = array();
			foreach($data2 AS $val => $valx){
				$numX++;

				$wherePN = floatval(substr($valx['series'], 3,2));
				$whereLN = floatval(substr($valx['series'], 6,3));
				
				// $wherePlus = '';
				// if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
				// 	$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
				// }
				// $qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
				// $restSer = $this->db->query($qSeries)->result();
				$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
				if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
					$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
				}
				if($valx['id_category'] == 'branch joint'){
					$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
				}
				$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
				$restSer = $this->db->query($qSeries)->result();
				// echo $qSeries."<br>";

				$dataKR4 	= sprintf('%03s',$numX);
				
				$sub_deivery = (!empty($valx['sub_delivery']))?$valx['sub_delivery']:'CP-01-01';
				
				$ArrInsertNew[$val]['id_bq'] 			= $id_bq;
				$ArrInsertNew[$val]['id_bq_header'] 	= $id_bq."-".$dataKR4;
				$ArrInsertNew[$val]['id_delivery'] 		= $valx['id_delivery'];
				$ArrInsertNew[$val]['sub_delivery'] 	= $sub_deivery;
				$ArrInsertNew[$val]['no_komponen'] 		= $sub_deivery."/".$dataKR4;
				$ArrInsertNew[$val]['sts_delivery'] 	= $valx['sts_delivery'];
				$ArrInsertNew[$val]['series'] 			= $valx['series'];

				$ArrInsertNew[$val]['id_category']	= $valx['id_category'];
				$ArrInsertNew[$val]['diameter_1']	= $valx['diameter_1'];
				$ArrInsertNew[$val]['diameter_2'] 	= $valx['diameter_2'];
				$ArrInsertNew[$val]['length'] 		= $valx['length'];
				$ArrInsertNew[$val]['thickness'] 	= $valx['thickness'];
				$ArrInsertNew[$val]['sudut'] 		= $valx['sudut'];
				$ArrInsertNew[$val]['id_standard'] 	= $valx['id_standard'];
				$ArrInsertNew[$val]['type'] 		= $valx['type'];
				$ArrInsertNew[$val]['qty'] 			= $valx['qty'];

				$ArrInsertNew[$val]['man_power'] 		= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
				$ArrInsertNew[$val]['id_mesin'] 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
				$ArrInsertNew[$val]['total_time'] 		= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
				$ArrInsertNew[$val]['man_hours'] 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

				$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
				$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
				$ArrInsertNew[$val]['pe_direct_labour'] 			= pe_direct_labour();
				$ArrInsertNew[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
				$ArrInsertNew[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
				$ArrInsertNew[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
				$ArrInsertNew[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
				$ArrInsertNew[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
				$ArrInsertNew[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
				$ArrInsertNew[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
				$ArrInsertNew[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
				$ArrInsertNew[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();
				

				for($no=1; $no <= $valx['qty']; $no++){
					$Loop++;
					$ArrInsertDetDetail[$Loop]['id_bq'] 		= $id_bq;

					$ArrInsertDetDetail[$Loop]['id_bq_header'] 	= $id_bq."-".$dataKR4;
					$ArrInsertDetDetail[$Loop]['id_delivery'] 	= $valx['id_delivery'];
					$ArrInsertDetDetail[$Loop]['sub_delivery'] 	= $sub_deivery;
					$ArrInsertDetDetail[$Loop]['sts_delivery'] 	= $valx['sts_delivery'];
					$ArrInsertDetDetail[$Loop]['series'] 		= $valx['series'];

					$ArrInsertDetDetail[$Loop]['id_category'] 	= $valx['id_category'];
					$ArrInsertDetDetail[$Loop]['diameter_1'] 	= $valx['diameter_1'];
					$ArrInsertDetDetail[$Loop]['diameter_2'] 	= $valx['diameter_2'];
					$ArrInsertDetDetail[$Loop]['length'] 		= $valx['length'];
					$ArrInsertDetDetail[$Loop]['thickness'] 	= $valx['thickness'];
					$ArrInsertDetDetail[$Loop]['sudut'] 		= $valx['sudut'];
					$ArrInsertDetDetail[$Loop]['id_standard'] 	= $valx['id_standard'];
					$ArrInsertDetDetail[$Loop]['type'] 			= $valx['type'];
					$ArrInsertDetDetail[$Loop]['qty'] 			= $valx['qty'];
					$ArrInsertDetDetail[$Loop]['product_ke'] 	= $no;
				}

			}

		}

		// print_r($ArrInsertNew);
		// print_r($ArrInsertDetDetail);
		// exit;


		$ArrUpdateBq	= array();
		$Loop = 0;
		$ArrDetDetail = array();
		if(!empty($data['DetailBq'])){
			foreach($DataBQ AS $val => $valx){
				$wherePN = floatval(substr($valx['series'], 3,2));
				$whereLN = floatval(substr($valx['series'], 6,3));
				
				// $wherePlus = '';
				// if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
				// 	$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
				// }
				// $qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
				// $restSer = $this->db->query($qSeries)->result();
				$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
				if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
					$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
				}
				if($valx['id_category'] == 'branch joint'){
					$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
				}
				$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
				$restSer = $this->db->query($qSeries)->result();
				// echo $qSeries."<br>";
				
				$ArrUpdateBq[$val]['id']			= $valx['id'];
				$ArrUpdateBq[$val]['id_category']	= $valx['id_category'];
				$ArrUpdateBq[$val]['sts_delivery']	= $valx['sts_delivery'];
				$ArrUpdateBq[$val]['sub_delivery']	= $valx['sub_delivery'];
				$ArrUpdateBq[$val]['id_delivery']	= $valx['id_delivery'];
				$ArrUpdateBq[$val]['series']		= $valx['series'];
				$ArrUpdateBq[$val]['diameter_1']	= $valx['diameter_1'];
				$ArrUpdateBq[$val]['diameter_2'] 	= $valx['diameter_2'];
				$ArrUpdateBq[$val]['length'] 		= $valx['length'];
				$ArrUpdateBq[$val]['thickness'] 	= $valx['thickness'];
				$ArrUpdateBq[$val]['sudut'] 		= $valx['sudut'];
				$ArrUpdateBq[$val]['id_standard'] 	= $valx['id_standard'];
				$ArrUpdateBq[$val]['type'] 			= $valx['type'];
				$ArrUpdateBq[$val]['qty'] 			= $valx['qty'];

				$ArrUpdateBq[$val]['man_power'] 	= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
				$ArrUpdateBq[$val]['id_mesin'] 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
				$ArrUpdateBq[$val]['total_time'] 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
				$ArrUpdateBq[$val]['man_hours'] 	= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

				$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
				$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
				$ArrUpdateBq[$val]['pe_direct_labour'] 				= pe_direct_labour();
				$ArrUpdateBq[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
				$ArrUpdateBq[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
				$ArrUpdateBq[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
				$ArrUpdateBq[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
				$ArrUpdateBq[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
				$ArrUpdateBq[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
				$ArrUpdateBq[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
				$ArrUpdateBq[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
				$ArrUpdateBq[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();

				$DataHeader = $this->db->query("SELECT id_bq_header, id_delivery, sub_delivery, sts_delivery, series FROM bq_detail_header WHERE id = '".$valx['id']."' ")->result();

				for($no=1; $no <= $valx['qty']; $no++){
					$Loop++;
					$ArrDetDetail[$Loop]['id_bq'] 			= $id_bq;

					$ArrDetDetail[$Loop]['id_bq_header'] 	= $DataHeader[0]->id_bq_header;
					$ArrDetDetail[$Loop]['id_delivery'] 	= $DataHeader[0]->id_delivery;
					$ArrDetDetail[$Loop]['sub_delivery'] 	= $DataHeader[0]->sub_delivery;
					$ArrDetDetail[$Loop]['sts_delivery'] 	= $DataHeader[0]->sts_delivery;
					$ArrDetDetail[$Loop]['series'] 			= $DataHeader[0]->series;

					$ArrDetDetail[$Loop]['id_category'] 	= $valx['id_category'];
					$ArrDetDetail[$Loop]['diameter_1'] 		= $valx['diameter_1'];
					$ArrDetDetail[$Loop]['diameter_2'] 		= $valx['diameter_2'];
					$ArrDetDetail[$Loop]['length'] 			= $valx['length'];
					$ArrDetDetail[$Loop]['thickness'] 		= $valx['thickness'];
					$ArrDetDetail[$Loop]['sudut'] 			= $valx['sudut'];
					$ArrDetDetail[$Loop]['id_standard'] 	= $valx['id_standard'];
					$ArrDetDetail[$Loop]['type'] 			= $valx['type'];
					$ArrDetDetail[$Loop]['qty'] 			= $valx['qty'];
					$ArrDetDetail[$Loop]['product_ke'] 		= $no;
				}

			}
		}
		// print_r($ArrUpdateBq);
		// print_r($ArrDetDetail);
		// exit;

		$ArrToHistHeader = array();
		foreach($ToHistBqHeader AS $val => $valx){
			$ArrToHistHeader[$val]['id_bq']			= $valx['id_bq'];
			$ArrToHistHeader[$val]['no_ipp']		= $valx['no_ipp'];
			$ArrToHistHeader[$val]['order_type']	= $valx['order_type'];
			$ArrToHistHeader[$val]['ket'] 			= $valx['ket'];
			$ArrToHistHeader[$val]['estimasi'] 		= $valx['estimasi'];
			$ArrToHistHeader[$val]['rev'] 			= $valx['rev'];
			$ArrToHistHeader[$val]['created_by'] 	= $valx['created_by'];
			$ArrToHistHeader[$val]['created_date'] 	= $valx['created_date'];
			$ArrToHistHeader[$val]['modified_by'] 	= $valx['modified_by'];
			$ArrToHistHeader[$val]['modified_date'] = $valx['modified_date'];
			$ArrToHistHeader[$val]['hist_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrToHistHeader[$val]['hist_date'] 	= date('Y-m-d H:i:s');
		}

		$ArrToHistDetHeader = array();
		foreach($ToHistBqDetHeader AS $val => $valx){
			$ArrToHistDetHeader[$val]['id_bq']			= $valx['id_bq'];
			$ArrToHistDetHeader[$val]['id_bq_header']	= $valx['id_bq_header'];
			$ArrToHistDetHeader[$val]['id_delivery']	= $valx['id_delivery'];
			$ArrToHistDetHeader[$val]['sub_delivery'] 	= $valx['sub_delivery'];
			$ArrToHistDetHeader[$val]['sts_delivery'] 	= $valx['sts_delivery'];
			$ArrToHistDetHeader[$val]['series'] 		= $valx['series'];
			$ArrToHistDetHeader[$val]['no_komponen'] 	= $valx['no_komponen'];
			$ArrToHistDetHeader[$val]['id_category'] 	= $valx['id_category'];
			$ArrToHistDetHeader[$val]['qty'] 			= $valx['qty'];
			$ArrToHistDetHeader[$val]['diameter_1'] 	= $valx['diameter_1'];
			$ArrToHistDetHeader[$val]['diameter_2'] 	= $valx['diameter_2'];
			$ArrToHistDetHeader[$val]['length']			= $valx['length'];
			$ArrToHistDetHeader[$val]['thickness']		= $valx['thickness'];
			$ArrToHistDetHeader[$val]['sudut']			= $valx['sudut'];
			$ArrToHistDetHeader[$val]['id_standard'] 	= $valx['id_standard'];
			$ArrToHistDetHeader[$val]['type'] 			= $valx['type'];
			$ArrToHistDetHeader[$val]['id_product'] 	= $valx['id_product'];
			$ArrToHistDetHeader[$val]['hist_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrToHistDetHeader[$val]['hist_date'] 		= date('Y-m-d H:i:s');

			$ArrToHistDetHeader[$val]['man_power']		= $valx['man_power'];
			$ArrToHistDetHeader[$val]['id_mesin'] 		= $valx['id_mesin'];
			$ArrToHistDetHeader[$val]['total_time'] 	= $valx['total_time'];
			$ArrToHistDetHeader[$val]['man_hours'] 		= $valx['man_hours'];

			$ArrToHistDetHeader[$val]['pe_direct_labour'] 			= $valx['pe_direct_labour'];
			$ArrToHistDetHeader[$val]['pe_indirect_labour'] 		= $valx['pe_indirect_labour'];
			$ArrToHistDetHeader[$val]['pe_machine'] 				= $valx['pe_machine'];
			$ArrToHistDetHeader[$val]['pe_mould_mandrill'] 			= $valx['pe_mould_mandrill'];
			$ArrToHistDetHeader[$val]['pe_consumable'] 				= $valx['pe_consumable'];
			$ArrToHistDetHeader[$val]['pe_foh_consumable'] 			= $valx['pe_foh_consumable'];
			$ArrToHistDetHeader[$val]['pe_foh_depresiasi'] 			= $valx['pe_foh_depresiasi'];
			$ArrToHistDetHeader[$val]['pe_biaya_gaji_non_produksi'] = $valx['pe_biaya_gaji_non_produksi'];
			$ArrToHistDetHeader[$val]['pe_biaya_non_produksi'] 		= $valx['pe_biaya_non_produksi'];
			$ArrToHistDetHeader[$val]['pe_biaya_rutin_bulanan'] 	= $valx['pe_biaya_rutin_bulanan'];
		}

		$ArrToHistDetDetail = array();
		foreach($ToHistBqDetDetail AS $val => $valx){
			$ArrToHistDetDetail[$val]['id_bq']			= $valx['id_bq'];
			$ArrToHistDetDetail[$val]['id_bq_header']	= $valx['id_bq_header'];
			$ArrToHistDetDetail[$val]['id_delivery']	= $valx['id_delivery'];
			$ArrToHistDetDetail[$val]['sub_delivery'] 	= $valx['sub_delivery'];
			$ArrToHistDetDetail[$val]['sts_delivery'] 	= $valx['sts_delivery'];
			$ArrToHistDetDetail[$val]['id_category'] 	= $valx['id_category'];
			$ArrToHistDetDetail[$val]['qty'] 			= $valx['qty'];
			$ArrToHistDetDetail[$val]['diameter_1'] 	= $valx['diameter_1'];
			$ArrToHistDetDetail[$val]['diameter_2'] 	= $valx['diameter_2'];
			$ArrToHistDetDetail[$val]['length']			= $valx['length'];
			$ArrToHistDetDetail[$val]['thickness']		= $valx['thickness'];
			$ArrToHistDetDetail[$val]['sudut']			= $valx['sudut'];
			$ArrToHistDetDetail[$val]['id_standard'] 	= $valx['id_standard'];
			$ArrToHistDetDetail[$val]['type'] 			= $valx['type'];
			$ArrToHistDetDetail[$val]['product_ke'] 	= $valx['product_ke'];
			$ArrToHistDetDetail[$val]['hist_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrToHistDetDetail[$val]['hist_date'] 		= date('Y-m-d H:i:s');
		}

		$UpdateModif	= array(
			'rev'	=> $ToHistBqHeader[0]['rev'] + 1 ,
			'modified_by'	=> $this->session->userdata['ORI_User']['username'],
			'modified_date'	=> date('Y-m-d H:i:s')
		);

		// if(!empty($ArrToHistDetDetail)){
				// echo "ADa";
			// }
			
		// print_r($ArrUpdateBq);
		// print_r($ArrDetDetail);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrToHistHeader)){
				$this->db->insert_batch('hist_bq_header', $ArrToHistHeader);
			}
			if(!empty($ArrToHistDetHeader)){
				$this->db->insert_batch('hist_bq_detail_header', $ArrToHistDetHeader);
			}
			// if(!empty($ArrToHistDetDetail)){
			// 	$this->db->insert_batch('hist_bq_detail_detail', $ArrToHistDetDetail);
			// }
			if(!empty($UpdateModif)){
				$this->db->where('id_bq', $id_bq);
				$this->db->update('bq_header', $UpdateModif);
			}
			if(!empty($ArrUpdateBq)){
				$this->db->update_batch('bq_detail_header', $ArrUpdateBq, 'id');
			}
			$this->db->delete('bq_detail_detail', array('id_bq' => $id_bq));
			
			if(!empty($ArrDetDetail)){
				$this->db->insert_batch('bq_detail_detail', $ArrDetDetail);
			}
			if(!empty($data['ListDetail'])){
				$this->db->insert_batch('bq_detail_header', $ArrInsertNew);
				$this->db->insert_batch('bq_detail_detail', $ArrInsertDetDetail);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit structure bq data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit structure bq data success. Thanks ...',
				'status'	=> 1
			);
			history('Edit Structure BQ with code : '.$id_bq);
		}

		echo json_encode($Arr_Kembali);

	}




	public function history(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of History Production',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data History Production');
		$this->load->view('Production/history',$data);
	}

	public function estimasi(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$id_produksi	= $data['id_produksi'];

			$dataUpdate = array(
				'plan_start_produksi' => $data['plan_start_produksi'],
				'plan_end_produksi' => $data['plan_end_produksi'],
				'sts_produksi' => 'FINISH',
				'modified_by' => $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
			);

			$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi)->update('production_header', $dataUpdate);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Failed. Please try again later ...',
					'status'	=> 2,
					'produksi'	=> $id_produksi
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Success. Thank you & have a nice day ...',
					'status'	=> 1,
					'produksi'	=> $id_produksi
				);
				history('Finish Production code : '.$id_produksi);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$id_produksi	= $this->uri->segment(3);
			$id_produksi = $this->uri->segment(3);

			$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
			$row	= $this->db->query($qSupplier)->result_array();

			$qDetail	= "	SELECT
								a.*,
								b.nm_product,
								c.delivery_name
							FROM
								production_detail a
								LEFT JOIN product_header b ON a.id_product=b.id_product
								LEFT JOIN delivery c ON a.id_delivery=c.id_delivery
							WHERE
								a.id_produksi = '".$id_produksi."' ";
			$rowD	= $this->db->query($qDetail)->result_array();

			$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN product_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
			$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();

			$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN product_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
			$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
			// echo $qDetailBtn;
			$data = array(
				'title'		=> 'Upload Production',
				'action'	=> 'updateReal',
				'row'		=> $row,
				'rowD'		=> $rowD,
				'numB'		=> $rowDBtn,
				'numB2'		=> $rowDBtn2
			);
			$this->load->view('Machine/estimasi',$data);
		}
	}

	public function edit() {
		if($this->input->post()){
			$Arr_Data	= array();
			$id_plant 	= $this->uri->segment(3);
			$data		= $this->input->post();

			$ArrUpdate	= array(
				'kdcab' => $data['kdcab'],
				'address' => $data['address'],
				'province' => $data['province'],
				'phone' => $data['phone'],
				'fax' => $data['fax'],
				'email' => $data['email'],
				);
			// echo "<pre>";
			// print_r($ArrUpdate);
			// exit;

			$this->db->trans_start();
			$this->db->where('id_plant', $id_plant);
			$this->db->update('company_plants', $ArrUpdate);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update company plant data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update company plant data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Company Plant : '.$id_plant);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$id_plant	= $this->uri->segment(3);
			$dataPlant 	= $this->db->query("SELECT * FROM company_plants WHERE id_plant='".$id_plant."' ")->result_array();

			$det_Province		= $this->master_model->getArray('provinsi',array(),'nama','nama');
			$det_branch			= $this->master_model->getArray('branch',array(),'nocab','cabang');
			$data = array(
				'title'			=> 'Edit Company Plants',
				'action'		=> 'edit',
				'rows_province'	=> $det_Province,
				'branch'		=> $det_branch,
				'row'			=> $dataPlant
			);
			$this->load->view('Company_plants/edit',$data);
		}
	}

	public function cancel(){
		$id_produksi 	= $this->input->post('id_produksi');
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'sts_produksi' 	=> 'CANCELED',
			'ket_status' 	=> $this->input->post('ket_status'),
			'cancel_by' 	=> $data_session['ORI_User']['username'],
			'cancel_date' 	=> date('Y-m-d H:i:s')
			);

		$this->db->trans_start();
		$this->db->where('id_produksi', $id_produksi);
		$this->db->update('production_header', $ArrPlant);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cancel production failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cancel production data success. Thanks ...',
				'status'	=> 1
			);
			history('Cancel production with Kode/Id : '.$id_produksi);
		}
		echo json_encode($Arr_Data);
	}

	public function modalBatal(){
		$this->load->view('Production/modalBatal');
	}

	public function modalEditX(){
		history('View edit product estimasi pipe');
		$this->load->view('Machine_modal/pipe');
	}

	public function modalEditX_end_cap(){
		history('View edit product estimasi end cap');
		$this->load->view('Machine_modal/end_cap');
	}

	public function modalEditX_blindflange(){
		history('View edit product estimasi blind flange');
		$this->load->view('Machine_modal/blind_flange');
	}

	public function modalEditX_pipeslongsong(){
		history('View edit product estimasi pipe slongsong');
		$this->load->view('Machine_modal/pipe_slongsong');
	}

	public function modalEditX_elbowmould(){
		history('View edit product estimasi elbow mould');
		$this->load->view('Machine_modal/elbow_mould');
	}

	public function modalEditX_elbowmitter(){
		history('View edit product estimasi elbow mitter');
		$this->load->view('Machine_modal/elbow_mitter');
	}

	public function modalEditX_eccentric_reducer(){
		history('View edit product estimasi eccentric reducer');
		$this->load->view('Machine_modal/eccentric_reducer');
	}

	public function modalEditX_concentric_reducer(){
		history('View edit product estimasi concentric reducer');
		$this->load->view('Machine_modal/concentric_reducer');
	}

	public function modalEditX_equal_tee_mould(){
		history('View edit product estimasi equal tee mould');
		$this->load->view('Machine_modal/equal_tee_mould');
	}

	public function modalEditX_reducer_tee_mould(){
		history('View edit product estimasi reducer tee mould');
		$this->load->view('Machine_modal/reducer_tee_mould');
	}

	public function modalEditX_equal_tee_slongsong(){
		history('View edit product estimasi equal tee slongsong');
		$this->load->view('Machine_modal/equal_tee_slongsong');
	}

	public function modalEditX_reducer_tee_slongsong(){
		history('View edit product estimasi reducer tee slongsong');
		$this->load->view('Machine_modal/reducer_tee_slongsong');
	}

	public function modalEditX_flange_mould(){
		history('View edit product estimasi flange mould');
		$this->load->view('Machine_modal/flange_mould');
	}
	
	public function modalEditX_colar(){
		history('View edit product estimasi colar');
		$this->load->view('Machine_modal/colar');
	}
	
	public function modalEditX_colar_slongsong(){
		history('View edit product estimasi colar slongsong');
		$this->load->view('Machine_modal/colar_slongsong');
	}

	public function modalEditX_flange_slongsong(){
		history('View edit product estimasi flange slongsong');
		$this->load->view('Machine_modal/flange_slongsong');
	}

	public function modalEditX_field_joint(){
		history('View edit product estimasi field joint');
		$this->load->view('Machine_modal/field_joint');
	}

	public function modalEditX_shop_joint(){
		history('View edit product estimasi shop joint');
		$this->load->view('Machine_modal/shop_joint');
	}

	public function modalEditX_branch_joint(){
		history('View edit product estimasi branch joint');
		$this->load->view('Machine_modal/branch_joint'); 
	}

	public function modalEdit(){
		$this->load->view('Production/modalEdit');
	}

	public function modalPrint(){
		$this->load->view('Production/modalPrint');
	}

	public function modalReal(){
		$this->load->view('Production/modalReal');
	}

	public function modalReal1(){
		$this->load->view('Production/modalReal1');
	}

	public function modalReal2(){
		$this->load->view('Production/modalReal2');
	}

	public function modalPerbandingan(){
		$this->load->view('Production/modalPerbandingan');
	}

	public function UpdateRealMat(){

		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$id_production_detail 	= $data['id_production_detail'];

		if(!empty($data['DetailUtama'])){
			$DetailUtama	= $data['DetailUtama'];
		}

		if(!empty($data['DetailUtama2'])){
			$DetailUtama2	= $data['DetailUtama2'];
		}

		if(!empty($data['DetailUtama3'])){
			$DetailUtama3	= $data['DetailUtama3'];
		}

		if(!empty($data['DetailResin'])){
			$DetailResin	= $data['DetailResin'];
		}

		if(!empty($data['DetailResin2'])){
			$DetailResin2	= $data['DetailResin2'];
		}

		if(!empty($data['DetailResin3'])){
			$DetailResin3	= $data['DetailResin3'];
		}

		if(!empty($data['DetailPlus'])){
			$DetailPlus		= $data['DetailPlus'];
		}

		if(!empty($data['DetailPlus2'])){
			$DetailPlus2	= $data['DetailPlus2'];
		}

		if(!empty($data['DetailPlus3'])){
			$DetailPlus3	= $data['DetailPlus3'];
		}

		if(!empty($data['DetailPlus4'])){
			$DetailPlus4	= $data['DetailPlus4'];
		}

		if(!empty($data['DetailAdd'])){
			$DetailAdd		= $data['DetailAdd'];
		}

		if(!empty($data['DetailAdd2'])){
			$DetailAdd2		= $data['DetailAdd2'];
		}

		if(!empty($data['DetailAdd3'])){
			$DetailAdd3		= $data['DetailAdd3'];
		}

		if(!empty($data['DetailAdd4'])){
			$DetailAdd4		= $data['DetailAdd4'];
		}

		// echo "<pre>";
		if(!empty($data['DetailUtama'])){
			$ArrDetailUtama	= array();
			foreach($DetailUtama AS $val => $valx){
				$ArrDetailUtama[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama);
		}

		if(!empty($data['DetailUtama2'])){
			$ArrDetailUtama2	= array();
			foreach($DetailUtama2 AS $val => $valx){
				$ArrDetailUtama2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama2[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama2);
		}

		if(!empty($data['DetailUtama3'])){
			$ArrDetailUtama3	= array();
			foreach($DetailUtama3 AS $val => $valx){
				$ArrDetailUtama3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama3[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama3);
		}

		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus4);
		}

		if(!empty($data['DetailAdd'])){
			$ArrDetailAdd	= array();
			foreach($DetailAdd AS $val => $valx){
				$ArrDetailAdd[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd);
		}

		if(!empty($data['DetailAdd2'])){
			$ArrDetailAdd2	= array();
			foreach($DetailAdd2 AS $val => $valx){
				$ArrDetailAdd2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd2);
		}

		if(!empty($data['DetailAdd3'])){
			$ArrDetailAdd3	= array();
			foreach($DetailAdd3 AS $val => $valx){
				$ArrDetailAdd3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd3);
		}

		if(!empty($data['DetailAdd4'])){
			$ArrDetailAdd4	= array();
			foreach($DetailAdd4 AS $val => $valx){
				$ArrDetailAdd4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd4);
		}

		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'upload_real' => "Y",
			'upload_by' => $data_session['ORI_User']['username'],
			'upload_date' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);
			//Utama
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama);
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama2);
			if(!empty($data['DetailUtama3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailUtama3);
			}
			//Resin
			$this->db->insert_batch('production_real_detail', $ArrDetailResin);
			$this->db->insert_batch('production_real_detail', $ArrDetailResin2);
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailResin3);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus);
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus4);
			}
			//Detail Add
			if(!empty($data['DetailAdd'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd);
			}
			if(!empty($data['DetailAdd2'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd2);
			}
			if(!empty($data['DetailAdd3'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd3);
			}
			if(!empty($data['DetailAdd4'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Failed. Please try again later ...',
				'status'	=> 2,
				'produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'produksi'	=> $id_produksi
			);
			history('Add Real Production '.$id_produksi.'/'.$product);
		}
		echo json_encode($Arr_Kembali);
	}

	//Modal1
	public function UpdateRealMat1(){

		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$id_production_detail 	= $data['id_production_detail'];

		if(!empty($data['DetailUtama'])){
			$DetailUtama	= $data['DetailUtama'];
		}

		if(!empty($data['DetailUtama2'])){
			$DetailUtama2	= $data['DetailUtama2'];
		}

		if(!empty($data['DetailUtama3'])){
			$DetailUtama3	= $data['DetailUtama3'];
		}

		if(!empty($data['DetailResin'])){
			$DetailResin	= $data['DetailResin'];
		}

		if(!empty($data['DetailResin2'])){
			$DetailResin2	= $data['DetailResin2'];
		}

		if(!empty($data['DetailResin3'])){
			$DetailResin3	= $data['DetailResin3'];
		}

		if(!empty($data['DetailPlus'])){
			$DetailPlus		= $data['DetailPlus'];
		}

		if(!empty($data['DetailPlus2'])){
			$DetailPlus2	= $data['DetailPlus2'];
		}

		if(!empty($data['DetailPlus3'])){
			$DetailPlus3	= $data['DetailPlus3'];
		}

		if(!empty($data['DetailPlus4'])){
			$DetailPlus4	= $data['DetailPlus4'];
		}

		// echo "<pre>";
		if(!empty($data['DetailUtama'])){
			$ArrDetailUtama	= array();
			foreach($DetailUtama AS $val => $valx){
				$ArrDetailUtama[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama);
		}

		if(!empty($data['DetailUtama2'])){
			$ArrDetailUtama2	= array();
			foreach($DetailUtama2 AS $val => $valx){
				$ArrDetailUtama2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama2[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama2);
		}

		if(!empty($data['DetailUtama3'])){
			$ArrDetailUtama3	= array();
			foreach($DetailUtama3 AS $val => $valx){
				$ArrDetailUtama3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama3[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailUtama3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailUtama3);
		}

		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus4);
		}

		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'upload_real' => "Y",
			'upload_by' => $data_session['ORI_User']['username'],
			'upload_date' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);
			//Utama
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama);
			$this->db->insert_batch('production_real_detail', $ArrDetailUtama2);
			if(!empty($data['DetailUtama3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailUtama3);
			}
			//Resin
			$this->db->insert_batch('production_real_detail', $ArrDetailResin);
			$this->db->insert_batch('production_real_detail', $ArrDetailResin2);
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailResin3);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus);
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus4);
			}

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Failed. Please try again later ...',
				'status'	=> 2,
				'produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'produksi'	=> $id_produksi
			);
			history('Add Real Production 1 '.$id_produksi.'/'.$product);
		}
		echo json_encode($Arr_Kembali);
	}

	//Modal 2
	public function UpdateRealMat2(){

		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$id_production_detail 	= $data['id_production_detail'];

		if(!empty($data['DetailResin'])){
			$DetailResin	= $data['DetailResin'];
		}

		if(!empty($data['DetailResin2'])){
			$DetailResin2	= $data['DetailResin2'];
		}

		if(!empty($data['DetailResin3'])){
			$DetailResin3	= $data['DetailResin3'];
		}

		if(!empty($data['DetailPlus'])){
			$DetailPlus		= $data['DetailPlus'];
		}

		if(!empty($data['DetailPlus2'])){
			$DetailPlus2	= $data['DetailPlus2'];
		}

		if(!empty($data['DetailPlus3'])){
			$DetailPlus3	= $data['DetailPlus3'];
		}

		if(!empty($data['DetailPlus4'])){
			$DetailPlus4	= $data['DetailPlus4'];
		}

		if(!empty($data['DetailAdd'])){
			$DetailAdd		= $data['DetailAdd'];
		}

		if(!empty($data['DetailAdd2'])){
			$DetailAdd2		= $data['DetailAdd2'];
		}

		if(!empty($data['DetailAdd3'])){
			$DetailAdd3		= $data['DetailAdd3'];
		}

		if(!empty($data['DetailAdd4'])){
			$DetailAdd4		= $data['DetailAdd4'];
		}

		// echo "<pre>";

		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailPlus4);
		}

		if(!empty($data['DetailAdd'])){
			$ArrDetailAdd	= array();
			foreach($DetailAdd AS $val => $valx){
				$ArrDetailAdd[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd);
		}

		if(!empty($data['DetailAdd2'])){
			$ArrDetailAdd2	= array();
			foreach($DetailAdd2 AS $val => $valx){
				$ArrDetailAdd2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd2[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd2[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd2);
		}

		if(!empty($data['DetailAdd3'])){
			$ArrDetailAdd3	= array();
			foreach($DetailAdd3 AS $val => $valx){
				$ArrDetailAdd3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd3[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd3[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd3);
		}

		if(!empty($data['DetailAdd4'])){
			$ArrDetailAdd4	= array();
			foreach($DetailAdd4 AS $val => $valx){
				$ArrDetailAdd4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd4[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetailAdd4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd4[$val]['status_date'] = date('Y-m-d H:i:s');
			}
			// print_r($ArrDetailAdd4);
		}

		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'upload_real2' => "Y",
			'upload_by2' => $data_session['ORI_User']['username'],
			'upload_date2' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);

			//Resin
			$this->db->insert_batch('production_real_detail', $ArrDetailResin);
			$this->db->insert_batch('production_real_detail', $ArrDetailResin2);
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('production_real_detail', $ArrDetailResin3);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus);
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('production_real_detail_plus', $ArrDetailPlus4);
			}
			//Detail Add
			if(!empty($data['DetailAdd'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd);
			}
			if(!empty($data['DetailAdd2'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd2);
			}
			if(!empty($data['DetailAdd3'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd3);
			}
			if(!empty($data['DetailAdd4'])){
				$this->db->insert_batch('production_real_detail_add', $ArrDetailAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Failed. Please try again later ...',
				'status'	=> 2,
				'produksi'	=> $id_produksi
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Upload Real Production Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'produksi'	=> $id_produksi
			);
			history('Add Real Production '.$id_produksi.'/'.$product);
		}
		echo json_encode($Arr_Kembali);
	}

	public function getTypeProduct(){
		// $sqlSup		= "SELECT * FROM product_parent WHERE product_parent NOT LIKE '%slongsong%' AND product_parent NOT LIKE '%mitter%' ORDER BY product_parent ASC";
		// $sqlSup		= "SELECT * FROM product_parent WHERE product_parent <> 'pipe slongsong' ORDER BY product_parent ASC";
		$sqlSup		= "SELECT * FROM product_parent ORDER BY product_parent ASC";

		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Type Product</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getTypeProductSub(){
		// $sqlSup		= "SELECT * FROM product_parent WHERE product_parent LIKE '%slongsong%' OR product_parent LIKE '%mitter%' ORDER BY product_parent ASC";
		$sqlSup		= "SELECT * FROM product_parent WHERE product_parent = 'pipe slongsong' ORDER BY product_parent ASC";

		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Select An Type Product</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getStandard(){
		$sqlSup		= "SELECT * FROM list_standard ORDER BY urut ASC";
		$restSup	= $this->db->query($sqlSup)->result_array();

		$option	= "<option value='0'>Standard</option>";
		foreach($restSup AS $val => $valx){
			$option .= "<option value='".$valx['id_standard']."'>".strtoupper($valx['nm_standard'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getTypeDelivery(){
		$sqlDel		= "SELECT * FROM delivery ORDER BY id_delivery ASC";
		$restDel	= $this->db->query($sqlDel)->result_array();

		$option	= "<option value='0'>Select An Delivery</option>";
		foreach($restDel AS $val => $valx){
			$option .= "<option value='".$valx['id_delivery']."'>".strtoupper($valx['delivery_name'])."</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function getProduct(){
		$category = $this->input->post('category');
		$sqlSup		= "SELECT * FROM product_header WHERE parent_product='".$category."' AND deleted <> 'Y' ORDER BY diameter ASC";
		// echo $category."<br>";
		$restSup	= $this->db->query($sqlSup)->result_array();
		$dataNum	= $this->db->query($sqlSup)->num_rows();
		// echo $dataNum;
		// $option = "";
		if($dataNum > 0 ){
			$option	= "<option value='0'>Select An Product</option>";
			foreach($restSup AS $val => $valx){
				$option .= "<option value='".$valx['id_product']."'>".strtoupper($valx['nm_product'])." [".$valx['diameter']." x ".$valx['panjang']." x ".$valx['design']."]</option>";
			}
		}
		elseif($category != 0 AND $dataNum == 0){
			$option	= "<option value='0'>Empty Product</option>";
		}
		elseif($category == 0 AND $dataNum == 0){
			$option	= "<option value='0'>Empty List</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function updateReal(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$id_produksi	= $data['id_produksi'];

			$dataUpdate = array(
				'plan_start_produksi' => $data['plan_start_produksi'],
				'plan_end_produksi' => $data['plan_end_produksi'],
				'sts_produksi' => 'FINISH',
				'modified_by' => $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
			);

			$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi)->update('production_header', $dataUpdate);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Failed. Please try again later ...',
					'status'	=> 2,
					'produksi'	=> $id_produksi
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Edit Production Success. Thank you & have a nice day ...',
					'status'	=> 1,
					'produksi'	=> $id_produksi
				);
				history('Finish Production code : '.$id_produksi);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$id_produksi	= $this->uri->segment(3);
			$id_produksi = $this->uri->segment(3);

			$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
			$row	= $this->db->query($qSupplier)->result_array();

			$qDetail	= "	SELECT
								a.*,
								b.nm_product,
								c.delivery_name
							FROM
								production_detail a
								LEFT JOIN product_header b ON a.id_product=b.id_product
								LEFT JOIN delivery c ON a.id_delivery=c.id_delivery
							WHERE
								a.id_produksi = '".$id_produksi."' ";
			$rowD	= $this->db->query($qDetail)->result_array();

			$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN product_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
			$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();

			$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN product_header b ON a.id_product=b.id_product WHERE a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
			$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
			// echo $qDetailBtn;
			$data = array(
				'title'		=> 'Upload Production',
				'action'	=> 'updateReal',
				'row'		=> $row,
				'rowD'		=> $rowD,
				'numB'		=> $rowDBtn,
				'numB2'		=> $rowDBtn2
			);
			$this->load->view('Production/updateReal',$data);
		}
	}

	public function printSPK(){
		$kode_produksi	= $this->uri->segment(3);
		$kode_product	= $this->uri->segment(4);
		$product_to		= $this->uri->segment(5);
		$id_delivery	= $this->uri->segment(6);

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();

		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print SPK Production '.$kode_produksi.'/'.$kode_product);

		PrintSPKOri($Nama_Beda, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery);
	}

	public function printSPK1(){
		$kode_produksi	= $this->uri->segment(3);
		$kode_product	= $this->uri->segment(4);
		$product_to		= $this->uri->segment(5);
		$id_delivery	= $this->uri->segment(6);

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();

		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print SPK Production '.$kode_produksi.'/'.$kode_product);

		PrintSPK1($Nama_Beda, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery);
	}

	public function printSPK2(){
		$kode_produksi	= $this->uri->segment(3);
		$kode_product	= $this->uri->segment(4);
		$product_to		= $this->uri->segment(5);
		$id_delivery	= $this->uri->segment(6);

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();

		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print SPK Production '.$kode_produksi.'/'.$kode_product);

		PrintSPK2($Nama_Beda, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery);
	}

	public function printRealProduction(){
		$kode_produksi			= $this->uri->segment(3);
		$kode_product			= $this->uri->segment(4);
		$product_to				= $this->uri->segment(5);
		$id_production_detail	= $this->uri->segment(6);
		$id_delivery			= $this->uri->segment(7);
		$data_session			= $this->session->userdata;
		$printby				= $data_session['ORI_User']['username'];
		$koneksi				= akses_server_side();

		include 'plusPrint.php';
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print Real Production '.$kode_produksi.'/'.$kode_product);

		PrintSPKRealOri($Nama_Beda, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_production_detail, $id_delivery);
	}

	public function getDataJSON2(){

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON2(
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
			$nestedData[]	= "<div align='left'>".$row['id_produksi']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";

			if($row['sts_produksi'] == 'FINISH'){
				$class='bg-green';
			}
			if($row['sts_produksi'] == 'CANCELED'){
				$class='bg-red';
			}
			if($row['sts_produksi'] == 'PENDING'){
				$class='bg-yellow';
			}

			$nestedData[]	= "<div align='center'><span class='badge ".$class."'>".$row['sts_produksi']."</span></div>";
					$priX	= "&nbsp;<button class='btn btn-sm btn-success' id='printSPK' title='Print SPK' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-print'></i></button>";
			$nestedData[]	= "<div align='center'>
									<button class='btn btn-sm btn-warning' id='detailPlant' title='Detail Production' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-eye'></i></button>
									".$priX."
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

	public function queryDataJSON2($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				production_header a
		    WHERE sts_produksi <> 'ON PROCESS' AND (
				a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_produksi',
			2 => 'nm_customer'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	function DeleteEdit(){
		$id_bqdet 		= $this->uri->segment(3);
		$id_bqdet_et 	= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$ExpTy			= explode('-', $id_bqdet_et);

		$id_bq =  $ExpTy[0]."-". $ExpTy[1];
		// echo  $id_bq;
		// exit;

		$ToHistBqDetDetail	= $this->db->query("SELECT * FROM bq_detail_detail WHERE id_bq_header='".$id_bqdet_et."' ")->result_array();
		$sqlToHistHead		= "	INSERT INTO hist_bq_detail_header
									(id_bq, id_bq_header, id_delivery, sub_delivery, series, no_komponen, sts_delivery, id_category, qty, diameter_1, diameter_2, length, thickness, sudut, id_standard, type, id_product, hist_by, hist_date)
								SELECT
									id_bq, id_bq_header, id_delivery, sub_delivery, series, no_komponen, sts_delivery, id_category, qty, diameter_1, diameter_2, length, thickness, sudut, id_standard, type, id_product, '".$this->session->userdata['ORI_User']['username']."', '".date('Y-m-d H:i:s')."'
								FROM bq_detail_header
								WHERE id = '".$id_bqdet."'
								";
		// echo "SELECT * FROM bq_detail_detail WHERE id_bq_header='".$id_bqdet_et."'";
		$ArrToHistDetDetail = array();
		foreach($ToHistBqDetDetail AS $val => $valx){
			$ArrToHistDetDetail[$val]['id_bq']			= $valx['id_bq'];
			$ArrToHistDetDetail[$val]['id_bq_header']	= $valx['id_bq_header'];
			$ArrToHistDetDetail[$val]['id_delivery']	= $valx['id_delivery'];
			$ArrToHistDetDetail[$val]['sub_delivery'] 	= $valx['sub_delivery'];
			$ArrToHistDetDetail[$val]['sts_delivery'] 	= $valx['sts_delivery'];
			$ArrToHistDetDetail[$val]['id_category'] 	= $valx['id_category'];
			$ArrToHistDetDetail[$val]['qty'] 			= $valx['qty'];
			$ArrToHistDetDetail[$val]['diameter_1'] 	= $valx['diameter_1'];
			$ArrToHistDetDetail[$val]['diameter_2'] 	= $valx['diameter_2'];
			$ArrToHistDetDetail[$val]['length']			= $valx['length'];
			$ArrToHistDetDetail[$val]['thickness']		= $valx['thickness'];
			$ArrToHistDetDetail[$val]['sudut']			= $valx['sudut'];
			$ArrToHistDetDetail[$val]['id_standard'] 	= $valx['id_standard'];
			$ArrToHistDetDetail[$val]['type'] 			= $valx['type'];
			$ArrToHistDetDetail[$val]['product_ke'] 	= $valx['product_ke'];
			$ArrToHistDetDetail[$val]['hist_by'] 		= $this->session->userdata['ORI_User']['username'];
			$ArrToHistDetDetail[$val]['hist_date'] 		= date('Y-m-d H:i:s');
		}
		// print_r($ArrToHistDetDetail);
		// exit;

		$this->db->trans_start();
			$this->db->query($sqlToHistHead);
			// if(!empty($ArrToHistDetDetail)){
			// 	$this->db->insert_batch('hist_bq_detail_detail', $ArrToHistDetDetail);
			// }
			$this->db->query("DELETE FROM bq_detail_header WHERE id='".$id_bqdet."' AND id_bq_header='".$id_bqdet_et."' ");
			$this->db->query("DELETE FROM bq_detail_detail WHERE id_bq_header='".$id_bqdet_et."' ");


			$this->db->query("DELETE FROM bq_component_header WHERE id_bq = '".$id_bq."' AND id_milik = '".$id_bqdet."' ");
			$this->db->query("DELETE FROM bq_component_detail WHERE id_bq = '".$id_bq."' AND id_milik = '".$id_bqdet."' ");
			$this->db->query("DELETE FROM bq_component_detail_plus WHERE id_bq = '".$id_bq."' AND id_milik = '".$id_bqdet."' ");
			$this->db->query("DELETE FROM bq_component_detail_add WHERE id_bq = '".$id_bq."' AND id_milik = '".$id_bqdet."' ");
			$this->db->query("DELETE FROM bq_component_footer WHERE id_bq = '".$id_bq."' AND id_milik = '".$id_bqdet."' ");
			$this->db->query("DELETE FROM bq_component_default WHERE id_bq = '".$id_bq."' AND id_milik = '".$id_bqdet."' ");
			$this->db->query("DELETE FROM bq_component_lamination WHERE id_bq = '".$id_bq."' AND id_milik = '".$id_bqdet."' ");
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Hapus sebagian BQ gagal. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Hapus sebagian BQ berhasil. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);
			history('Delete Sebagian BQ with ID : '.$id_bqdet_et);
		}

		// print_r($Arr_Data);
		echo json_encode($Arr_Data);
	}

	//SAVED
	public function pipe_edit_bq(){

		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

		$ListDetailPlus		= $data['ListDetailPlus'];
		$ListDetailPlus2	= $data['ListDetailPlus2'];
		$ListDetailPlus3	= $data['ListDetailPlus3'];
		$ListDetailPlus4	= $data['ListDetailPlus4'];

		if(!empty($data['ListDetailAdd_Liner'])){
			$ListDetailAdd_Liner1	= $data['ListDetailAdd_Liner'];
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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'pipe',
			'nm_product'			=> $data['top_type'],
			'standart_code'			=> $data['standart_code2'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader);
		
		//================================================================================================================
		//============================================DEFAULT BY ARWANT===================================================
		//================================================================================================================
		$ArrBqDefault	= array();
		if(!empty($data['standart_code2'])){
			
			$qDefvalNum 	= "SELECT * FROM bq_component_default WHERE id_milik='".$data['id_milik']."' ";
			$getDefValNum	= $this->db->query($qDefvalNum)->num_rows();
			if($getDefValNum < 1){
			$qDefval 		= "SELECT * FROM help_default WHERE product_parent='".$data['parent_product']."' AND standart_code='".$data['standart_code2']."' AND diameter='".$data['diameter']."' LIMIT 1 ";
			}
			else{
			$qDefval 		= "SELECT * FROM bq_component_default WHERE id_milik='".$data['id_milik']."' LIMIT 1 ";
			}
			// echo $qDefval;
			$getDefVal		= $this->db->query($qDefval)->result();

			$ArrBqDefault['id_product']				= $data['id_product'];
			$ArrBqDefault['id_bq']					= $data['id_bq'];
			$ArrBqDefault['id_milik']					= $data['id_milik'];
			$ArrBqDefault['product_parent']			= $getDefVal[0]->product_parent;
			$ArrBqDefault['kd_cust']					= $getDefVal[0]->kd_cust;
			$ArrBqDefault['customer']					= $getDefVal[0]->customer;
			$ArrBqDefault['standart_code']			= $getDefVal[0]->standart_code;
			$ArrBqDefault['diameter']					= $getDefVal[0]->diameter;
			$ArrBqDefault['diameter2']				= $getDefVal[0]->diameter2;
			$ArrBqDefault['liner']					= $getDefVal[0]->liner;
			$ArrBqDefault['pn']						= $getDefVal[0]->pn;
			$ArrBqDefault['overlap']					= $getDefVal[0]->overlap;
			$ArrBqDefault['waste']					= $getDefVal[0]->waste;
			$ArrBqDefault['waste_n1']					= $getDefVal[0]->waste_n1;
			$ArrBqDefault['waste_n2']					= $getDefVal[0]->waste_n2;
			$ArrBqDefault['max']						= $getDefVal[0]->max;
			$ArrBqDefault['min']						= $getDefVal[0]->min;
			$ArrBqDefault['plastic_film']				= $getDefVal[0]->plastic_film;
			$ArrBqDefault['lin_resin_veil_a']			= $getDefVal[0]->lin_resin_veil_a;
			$ArrBqDefault['lin_resin_veil_b']			= $getDefVal[0]->lin_resin_veil_b;
			$ArrBqDefault['lin_resin_veil']			= $getDefVal[0]->lin_resin_veil;
			$ArrBqDefault['lin_resin_veil_add_a']		= $getDefVal[0]->lin_resin_veil_add_a;
			$ArrBqDefault['lin_resin_veil_add_b']		= $getDefVal[0]->lin_resin_veil_add_b;
			$ArrBqDefault['lin_resin_veil_add']		= $getDefVal[0]->lin_resin_veil_add;
			$ArrBqDefault['lin_resin_csm_a']			= $getDefVal[0]->lin_resin_csm_a;
			$ArrBqDefault['lin_resin_csm_b']			= $getDefVal[0]->lin_resin_csm_b;
			$ArrBqDefault['lin_resin_csm']			= $getDefVal[0]->lin_resin_csm;
			$ArrBqDefault['lin_resin_csm_add_a']		= $getDefVal[0]->lin_resin_csm_add_a;
			$ArrBqDefault['lin_resin_csm_add_b']		= $getDefVal[0]->lin_resin_csm_add_b;
			$ArrBqDefault['lin_resin_csm_add']		= $getDefVal[0]->lin_resin_csm_add;
			$ArrBqDefault['lin_faktor_veil']			= $getDefVal[0]->lin_faktor_veil;
			$ArrBqDefault['lin_faktor_veil_add']		= $getDefVal[0]->lin_faktor_veil_add;
			$ArrBqDefault['lin_faktor_csm']			= $getDefVal[0]->lin_faktor_csm;
			$ArrBqDefault['lin_faktor_csm_add']		= $getDefVal[0]->lin_faktor_csm_add;
			$ArrBqDefault['lin_resin']				= $getDefVal[0]->lin_resin;
			$ArrBqDefault['lin_resin_thickness']		= $getDefVal[0]->lin_resin_thickness;
			$ArrBqDefault['str_resin_csm_a']			= $getDefVal[0]->str_resin_csm_a;
			$ArrBqDefault['str_resin_csm_b']			= $getDefVal[0]->str_resin_csm_b;
			$ArrBqDefault['str_resin_csm']			= $getDefVal[0]->str_resin_csm;
			$ArrBqDefault['str_resin_csm_add_a']		= $getDefVal[0]->str_resin_csm_add_a;
			$ArrBqDefault['str_resin_csm_add_b']		= $getDefVal[0]->str_resin_csm_add_b;
			$ArrBqDefault['str_resin_csm_add']		= $getDefVal[0]->str_resin_csm_add;
			$ArrBqDefault['str_resin_wr_a']			= $getDefVal[0]->str_resin_wr_a;
			$ArrBqDefault['str_resin_wr_b']			= $getDefVal[0]->str_resin_wr_b;
			$ArrBqDefault['str_resin_wr']				= $getDefVal[0]->str_resin_wr;
			$ArrBqDefault['str_resin_wr_add_a']		= $getDefVal[0]->str_resin_wr_add_a;
			$ArrBqDefault['str_resin_wr_add_b']		= $getDefVal[0]->str_resin_wr_add_b;
			$ArrBqDefault['str_resin_wr_add']			= $getDefVal[0]->str_resin_wr_add;
			$ArrBqDefault['str_resin_rv_a']			= $getDefVal[0]->str_resin_rv_a;
			$ArrBqDefault['str_resin_rv_b']			= $getDefVal[0]->str_resin_rv_b;
			$ArrBqDefault['str_resin_rv']				= $getDefVal[0]->str_resin_rv;
			$ArrBqDefault['str_resin_rv_add_a']		= $getDefVal[0]->str_resin_rv_add_a;
			$ArrBqDefault['str_resin_rv_add_b']		= $getDefVal[0]->str_resin_rv_add_b;
			$ArrBqDefault['str_resin_rv_add']			= $getDefVal[0]->str_resin_rv_add;
			$ArrBqDefault['str_faktor_csm']			= $getDefVal[0]->str_faktor_csm;
			$ArrBqDefault['str_faktor_csm_add']		= $getDefVal[0]->str_faktor_csm_add;
			$ArrBqDefault['str_faktor_wr']			= $getDefVal[0]->str_faktor_wr;
			$ArrBqDefault['str_faktor_wr_add']		= $getDefVal[0]->str_faktor_wr_add;
			$ArrBqDefault['str_faktor_rv']			= $getDefVal[0]->str_faktor_rv;
			$ArrBqDefault['str_faktor_rv_bw']			= $getDefVal[0]->str_faktor_rv_bw;
			$ArrBqDefault['str_faktor_rv_jb']			= $getDefVal[0]->str_faktor_rv_jb;
			$ArrBqDefault['str_faktor_rv_add']		= $getDefVal[0]->str_faktor_rv_add;
			$ArrBqDefault['str_faktor_rv_add_bw']		= $getDefVal[0]->str_faktor_rv_add_bw;
			$ArrBqDefault['str_faktor_rv_add_jb']		= $getDefVal[0]->str_faktor_rv_add_jb;
			$ArrBqDefault['str_resin']				= $getDefVal[0]->str_resin;
			$ArrBqDefault['str_resin_thickness']		= $getDefVal[0]->str_resin_thickness;
			$ArrBqDefault['eks_resin_veil_a']			= $getDefVal[0]->eks_resin_veil_a;
			$ArrBqDefault['eks_resin_veil_b']			= $getDefVal[0]->eks_resin_veil_b;
			$ArrBqDefault['eks_resin_veil']			= $getDefVal[0]->eks_resin_veil;
			$ArrBqDefault['eks_resin_veil_add_a']		= $getDefVal[0]->eks_resin_veil_add_a;
			$ArrBqDefault['eks_resin_veil_add_b']		= $getDefVal[0]->eks_resin_veil_add_b;
			$ArrBqDefault['eks_resin_veil_add']		= $getDefVal[0]->eks_resin_veil_add;
			$ArrBqDefault['eks_resin_csm_a']			= $getDefVal[0]->eks_resin_csm_a;
			$ArrBqDefault['eks_resin_csm_b']			= $getDefVal[0]->eks_resin_csm_b;
			$ArrBqDefault['eks_resin_csm']			= $getDefVal[0]->eks_resin_csm;
			$ArrBqDefault['eks_resin_csm_add_a']		= $getDefVal[0]->eks_resin_csm_add_a;
			$ArrBqDefault['eks_resin_csm_add_b']		= $getDefVal[0]->eks_resin_csm_add_b;
			$ArrBqDefault['eks_resin_csm_add']		= $getDefVal[0]->eks_resin_csm_add;
			$ArrBqDefault['eks_faktor_veil']			= $getDefVal[0]->eks_faktor_veil;
			$ArrBqDefault['eks_faktor_veil_add']		= $getDefVal[0]->eks_faktor_veil_add;
			$ArrBqDefault['eks_faktor_csm']			= $getDefVal[0]->eks_faktor_csm;
			$ArrBqDefault['eks_faktor_csm_add']		= $getDefVal[0]->eks_faktor_csm_add;
			$ArrBqDefault['eks_resin']				= $getDefVal[0]->eks_resin;
			$ArrBqDefault['eks_resin_thickness']		= $getDefVal[0]->eks_resin_thickness;
			$ArrBqDefault['topcoat_resin']			= $getDefVal[0]->topcoat_resin;
			$ArrBqDefault['str_n1_resin_csm_a']		= $getDefVal[0]->str_n1_resin_csm_a;
			$ArrBqDefault['str_n1_resin_csm_b']		= $getDefVal[0]->str_n1_resin_csm_b;
			$ArrBqDefault['str_n1_resin_csm']			= $getDefVal[0]->str_n1_resin_csm;
			$ArrBqDefault['str_n1_resin_csm_add_a']	= $getDefVal[0]->str_n1_resin_csm_add_a;
			$ArrBqDefault['str_n1_resin_csm_add_b']	= $getDefVal[0]->str_n1_resin_csm_add_b;
			$ArrBqDefault['str_n1_resin_csm_add']		= $getDefVal[0]->str_n1_resin_csm_add;
			$ArrBqDefault['str_n1_resin_wr_a']		= $getDefVal[0]->str_n1_resin_wr_a;
			$ArrBqDefault['str_n1_resin_wr_b']		= $getDefVal[0]->str_n1_resin_wr_b;
			$ArrBqDefault['str_n1_resin_wr']			= $getDefVal[0]->str_n1_resin_wr;
			$ArrBqDefault['str_n1_resin_wr_add_a']	= $getDefVal[0]->str_n1_resin_wr_add_a;
			$ArrBqDefault['str_n1_resin_wr_add_b']	= $getDefVal[0]->str_n1_resin_wr_add_b;
			$ArrBqDefault['str_n1_resin_wr_add']		= $getDefVal[0]->str_n1_resin_wr_add;
			$ArrBqDefault['str_n1_resin_rv_a']		= $getDefVal[0]->str_n1_resin_rv_a;
			$ArrBqDefault['str_n1_resin_rv_b']		= $getDefVal[0]->str_n1_resin_rv_b;
			$ArrBqDefault['str_n1_resin_rv']			= $getDefVal[0]->str_n1_resin_rv;
			$ArrBqDefault['str_n1_resin_rv_add_a']	= $getDefVal[0]->str_n1_resin_rv_add_a;
			$ArrBqDefault['str_n1_resin_rv_add_b']	= $getDefVal[0]->str_n1_resin_rv_add_b;
			$ArrBqDefault['str_n1_resin_rv_add']		= $getDefVal[0]->str_n1_resin_rv_add;
			$ArrBqDefault['str_n1_faktor_csm']		= $getDefVal[0]->str_n1_faktor_csm;
			$ArrBqDefault['str_n1_faktor_csm_add']	= $getDefVal[0]->str_n1_faktor_csm_add;
			$ArrBqDefault['str_n1_faktor_wr']			= $getDefVal[0]->str_n1_faktor_wr;
			$ArrBqDefault['str_n1_faktor_wr_add']		= $getDefVal[0]->str_n1_faktor_wr_add;
			$ArrBqDefault['str_n1_faktor_rv']			= $getDefVal[0]->str_n1_faktor_rv;
			$ArrBqDefault['str_n1_faktor_rv_bw']		= $getDefVal[0]->str_n1_faktor_rv_bw;
			$ArrBqDefault['str_n1_faktor_rv_jb']		= $getDefVal[0]->str_n1_faktor_rv_jb;
			$ArrBqDefault['str_n1_faktor_rv_add']		= $getDefVal[0]->str_n1_faktor_rv_add;
			$ArrBqDefault['str_n1_faktor_rv_add_bw']	= $getDefVal[0]->str_n1_faktor_rv_add_bw;
			$ArrBqDefault['str_n1_faktor_rv_add_jb']	= $getDefVal[0]->str_n1_faktor_rv_add_jb;
			$ArrBqDefault['str_n1_resin']				= $getDefVal[0]->str_n1_resin;
			$ArrBqDefault['str_n1_resin_thickness']	= $getDefVal[0]->str_n1_resin_thickness;
			$ArrBqDefault['str_n2_resin_csm_a']		= $getDefVal[0]->str_n2_resin_csm_a;
			$ArrBqDefault['str_n2_resin_csm_b']		= $getDefVal[0]->str_n2_resin_csm_b;
			$ArrBqDefault['str_n2_resin_csm']			= $getDefVal[0]->str_n2_resin_csm;
			$ArrBqDefault['str_n2_resin_csm_add_a']	= $getDefVal[0]->str_n2_resin_csm_add_a;
			$ArrBqDefault['str_n2_resin_csm_add_b']	= $getDefVal[0]->str_n2_resin_csm_add_b;
			$ArrBqDefault['str_n2_resin_csm_add']		= $getDefVal[0]->str_n2_resin_csm_add;
			$ArrBqDefault['str_n2_resin_wr_a']		= $getDefVal[0]->str_n2_resin_wr_a;
			$ArrBqDefault['str_n2_resin_wr_b']		= $getDefVal[0]->str_n2_resin_wr_b;
			$ArrBqDefault['str_n2_resin_wr']			= $getDefVal[0]->str_n2_resin_wr;
			$ArrBqDefault['str_n2_resin_wr_add_a']	= $getDefVal[0]->str_n2_resin_wr_add_a;
			$ArrBqDefault['str_n2_resin_wr_add_b']	= $getDefVal[0]->str_n2_resin_wr_add_b;
			$ArrBqDefault['str_n2_resin_wr_add']		= $getDefVal[0]->str_n2_resin_wr_add;
			$ArrBqDefault['str_n2_faktor_csm']		= $getDefVal[0]->str_n2_faktor_csm;
			$ArrBqDefault['str_n2_faktor_csm_add']	= $getDefVal[0]->str_n2_faktor_csm_add;
			$ArrBqDefault['str_n2_faktor_wr']			= $getDefVal[0]->str_n2_faktor_wr;
			$ArrBqDefault['str_n2_faktor_wr_add']		= $getDefVal[0]->str_n2_faktor_wr_add;
			$ArrBqDefault['str_n2_resin']				= $getDefVal[0]->str_n2_resin;
			$ArrBqDefault['str_n2_resin_thickness']	= $getDefVal[0]->str_n2_resin_thickness;
			$ArrBqDefault['created_by']				= $this->session->userdata['ORI_User']['username'];
			$ArrBqDefault['created_date']				= date('Y-m-d H:i:s');
		}

		//Insert Component Header To Hist
		$ArrBqDefaultHist	= array();
		$qHeaderHistDef		= $this->db->query("SELECT * FROM bq_component_default WHERE id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNumDef	= $this->db->query("SELECT * FROM bq_component_default WHERE id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNumDef > 0){
			foreach($qHeaderHistDef AS $val2HistADef => $valx2HistADef){
				$ArrBqDefaultHist[$val2HistADef]['id_product']				= $valx2HistADef['id_product'];
				$ArrBqDefaultHist[$val2HistADef]['id_milik']				= $valx2HistADef['id_milik'];
				$ArrBqDefaultHist[$val2HistADef]['id_bq']					= $valx2HistADef['id_bq'];
				$ArrBqDefaultHist[$val2HistADef]['product_parent']			= $valx2HistADef['product_parent'];
				$ArrBqDefaultHist[$val2HistADef]['kd_cust']					= $valx2HistADef['kd_cust'];
				$ArrBqDefaultHist[$val2HistADef]['customer']				= $valx2HistADef['customer'];
				$ArrBqDefaultHist[$val2HistADef]['standart_code']			= $valx2HistADef['standart_code'];
				$ArrBqDefaultHist[$val2HistADef]['diameter']				= $valx2HistADef['diameter'];
				$ArrBqDefaultHist[$val2HistADef]['diameter2']				= $valx2HistADef['diameter2'];
				$ArrBqDefaultHist[$val2HistADef]['liner']					= $valx2HistADef['liner'];
				$ArrBqDefaultHist[$val2HistADef]['pn']						= $valx2HistADef['pn'];
				$ArrBqDefaultHist[$val2HistADef]['overlap']					= $valx2HistADef['overlap'];
				$ArrBqDefaultHist[$val2HistADef]['waste']					= $valx2HistADef['waste'];
				$ArrBqDefaultHist[$val2HistADef]['waste_n1']				= $valx2HistADef['waste_n1'];
				$ArrBqDefaultHist[$val2HistADef]['waste_n2']				= $valx2HistADef['waste_n2'];
				$ArrBqDefaultHist[$val2HistADef]['max']						= $valx2HistADef['max'];
				$ArrBqDefaultHist[$val2HistADef]['min']						= $valx2HistADef['min'];
				$ArrBqDefaultHist[$val2HistADef]['plastic_film']			= $valx2HistADef['plastic_film'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_a']		= $valx2HistADef['lin_resin_veil_a'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_b']		= $valx2HistADef['lin_resin_veil_b'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil']			= $valx2HistADef['lin_resin_veil'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_a']	= $valx2HistADef['lin_resin_veil_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_b']	= $valx2HistADef['lin_resin_veil_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add']		= $valx2HistADef['lin_resin_veil_add'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_a']			= $valx2HistADef['lin_resin_csm_a'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_b']			= $valx2HistADef['lin_resin_csm_b'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm']			= $valx2HistADef['lin_resin_csm'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_a']		= $valx2HistADef['lin_resin_csm_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_b']		= $valx2HistADef['lin_resin_csm_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add']		= $valx2HistADef['lin_resin_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil']			= $valx2HistADef['lin_faktor_veil'];
				$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil_add']		= $valx2HistADef['lin_faktor_veil_add'];
				$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm']			= $valx2HistADef['lin_faktor_csm'];
				$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm_add']		= $valx2HistADef['lin_faktor_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin']				= $valx2HistADef['lin_resin'];
				$ArrBqDefaultHist[$val2HistADef]['lin_resin_thickness']		= $valx2HistADef['lin_resin_thickness'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_a']			= $valx2HistADef['str_resin_csm_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_b']			= $valx2HistADef['str_resin_csm_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_csm']			= $valx2HistADef['str_resin_csm'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_a']		= $valx2HistADef['str_resin_csm_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_b']		= $valx2HistADef['str_resin_csm_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add']		= $valx2HistADef['str_resin_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_a']			= $valx2HistADef['str_resin_wr_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_b']			= $valx2HistADef['str_resin_wr_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_wr']			= $valx2HistADef['str_resin_wr'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_a']		= $valx2HistADef['str_resin_wr_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_b']		= $valx2HistADef['str_resin_wr_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add']		= $valx2HistADef['str_resin_wr_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_a']			= $valx2HistADef['str_resin_rv_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_b']			= $valx2HistADef['str_resin_rv_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_rv']			= $valx2HistADef['str_resin_rv'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_a']		= $valx2HistADef['str_resin_rv_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_b']		= $valx2HistADef['str_resin_rv_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add']		= $valx2HistADef['str_resin_rv_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm']			= $valx2HistADef['str_faktor_csm'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm_add']		= $valx2HistADef['str_faktor_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr']			= $valx2HistADef['str_faktor_wr'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr_add']		= $valx2HistADef['str_faktor_wr_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv']			= $valx2HistADef['str_faktor_rv'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_bw']		= $valx2HistADef['str_faktor_rv_bw'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_jb']		= $valx2HistADef['str_faktor_rv_jb'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add']		= $valx2HistADef['str_faktor_rv_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_bw']	= $valx2HistADef['str_faktor_rv_add_bw'];
				$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_jb']	= $valx2HistADef['str_faktor_rv_add_jb'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin']				= $valx2HistADef['str_resin'];
				$ArrBqDefaultHist[$val2HistADef]['str_resin_thickness']		= $valx2HistADef['str_resin_thickness'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_a']		= $valx2HistADef['eks_resin_veil_a'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_b']		= $valx2HistADef['eks_resin_veil_b'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil']			= $valx2HistADef['eks_resin_veil'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_a']	= $valx2HistADef['eks_resin_veil_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_b']	= $valx2HistADef['eks_resin_veil_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add']		= $valx2HistADef['eks_resin_veil_add'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_a']			= $valx2HistADef['eks_resin_csm_a'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_b']			= $valx2HistADef['eks_resin_csm_b'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm']			= $valx2HistADef['eks_resin_csm'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_a']		= $valx2HistADef['eks_resin_csm_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_b']		= $valx2HistADef['eks_resin_csm_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add']		= $valx2HistADef['eks_resin_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil']			= $valx2HistADef['eks_faktor_veil'];
				$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil_add']		= $valx2HistADef['eks_faktor_veil_add'];
				$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm']			= $valx2HistADef['eks_faktor_csm'];
				$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm_add']		= $valx2HistADef['eks_faktor_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin']				= $valx2HistADef['eks_resin'];
				$ArrBqDefaultHist[$val2HistADef]['eks_resin_thickness']		= $valx2HistADef['eks_resin_thickness'];
				$ArrBqDefaultHist[$val2HistADef]['topcoat_resin']			= $valx2HistADef['topcoat_resin'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_a']		= $valx2HistADef['str_n1_resin_csm_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_b']		= $valx2HistADef['str_n1_resin_csm_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm']		= $valx2HistADef['str_n1_resin_csm'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_a']	= $valx2HistADef['str_n1_resin_csm_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_b']	= $valx2HistADef['str_n1_resin_csm_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add']	= $valx2HistADef['str_n1_resin_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_a']		= $valx2HistADef['str_n1_resin_wr_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_b']		= $valx2HistADef['str_n1_resin_wr_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr']			= $valx2HistADef['str_n1_resin_wr'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_a']	= $valx2HistADef['str_n1_resin_wr_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_b']	= $valx2HistADef['str_n1_resin_wr_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add']		= $valx2HistADef['str_n1_resin_wr_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_a']		= $valx2HistADef['str_n1_resin_rv_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_b']		= $valx2HistADef['str_n1_resin_rv_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv']			= $valx2HistADef['str_n1_resin_rv'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_a']	= $valx2HistADef['str_n1_resin_rv_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_b']	= $valx2HistADef['str_n1_resin_rv_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add']		= $valx2HistADef['str_n1_resin_rv_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm']		= $valx2HistADef['str_n1_faktor_csm'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm_add']	= $valx2HistADef['str_n1_faktor_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr']		= $valx2HistADef['str_n1_faktor_wr'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr_add']	= $valx2HistADef['str_n1_faktor_wr_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv']		= $valx2HistADef['str_n1_faktor_rv'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_bw']		= $valx2HistADef['str_n1_faktor_rv_bw'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_jb']		= $valx2HistADef['str_n1_faktor_rv_jb'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add']	= $valx2HistADef['str_n1_faktor_rv_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_bw']	= $valx2HistADef['str_n1_faktor_rv_add_bw'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_jb']	= $valx2HistADef['str_n1_faktor_rv_add_jb'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin']			= $valx2HistADef['str_n1_resin'];
				$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_thickness']	= $valx2HistADef['str_n1_resin_thickness'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_a']		= $valx2HistADef['str_n2_resin_csm_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_b']		= $valx2HistADef['str_n2_resin_csm_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm']		= $valx2HistADef['str_n2_resin_csm'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_a']	= $valx2HistADef['str_n2_resin_csm_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_b']	= $valx2HistADef['str_n2_resin_csm_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add']	= $valx2HistADef['str_n2_resin_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_a']		= $valx2HistADef['str_n2_resin_wr_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_b']		= $valx2HistADef['str_n2_resin_wr_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr']			= $valx2HistADef['str_n2_resin_wr'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_a']	= $valx2HistADef['str_n2_resin_wr_add_a'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_b']	= $valx2HistADef['str_n2_resin_wr_add_b'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add']		= $valx2HistADef['str_n2_resin_wr_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm']		= $valx2HistADef['str_n2_faktor_csm'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm_add']	= $valx2HistADef['str_n2_faktor_csm_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr']		= $valx2HistADef['str_n2_faktor_wr'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr_add']	= $valx2HistADef['str_n2_faktor_wr_add'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin']			= $valx2HistADef['str_n2_resin'];
				$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_thickness']	= $valx2HistADef['str_n2_resin_thickness'];
				$ArrBqDefaultHist[$val2HistADef]['created_by']				= $valx2HistADef['created_by'];
				$ArrBqDefaultHist[$val2HistADef]['created_date']			= $valx2HistADef['created_date'];
				$ArrBqDefaultHist[$val2HistADef]['modified_by']				= $valx2HistADef['modified_by'];
				$ArrBqDefaultHist[$val2HistADef]['modified_date']			= $valx2HistADef['modified_date'];
				$ArrBqDefaultHist[$val2HistADef]['hist_by']					= $this->session->userdata['ORI_User']['username'];
				$ArrBqDefaultHist[$val2HistADef]['hist_date']				= date('Y-m-d H:i:s');
				
				
			}
		}
		//================================================================================================================
		//================================================================================================================
		//================================================================================================================

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		
		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1Bef = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1Bef[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1Bef[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1Bef[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1Bef[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd1Bef[$val]['last_full'] 	= '';
				$ArrDataAdd1Bef[$val]['last_cost'] 	= $valx['last_cost']; 
			}
			// print_r($ArrDataAdd1Bef);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2Bef = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2Bef[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2Bef[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2Bef[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2Bef[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd2Bef[$val]['last_full'] 	= '';
				$ArrDataAdd2Bef[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3Bef = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3Bef[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3Bef[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3Bef[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3Bef[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd3Bef[$val]['last_full'] 	= '';
				$ArrDataAdd3Bef[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4Bef = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4Bef[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4Bef[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4Bef[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4Bef[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4Bef[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4Bef[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4Bef[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4Bef[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4Bef[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4Bef[$val]['perse'] 		= $perseM;
				$ArrDataAdd4Bef[$val]['last_full'] 	= '';
				$ArrDataAdd4Bef[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		
		
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd_Liner'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd_Liner1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd_Strukture'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd_Strukture AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd_External'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd_External AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd_TopCoat'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd_TopCoat AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Time To Hist
		// $qDetailTimeHist	= $this->db->query("SELECT * FROM component_time WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		// $qDetailTimeHistNum	= $this->db->query("SELECT * FROM component_time WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		// if($qDetailTimeHistNum > 0){
			// foreach($qDetailTimeHist AS $val6Hist => $valx5Hist){
				// $ArrBqTimeHist[$val6Hist]['id_product']	= $valx5Hist['id_product'];
				// $ArrBqTimeHist[$val6Hist]['process']		= $valx5Hist['detail_name'];
				// $ArrBqTimeHist[$val6Hist]['sub_process']	= $valx5Hist['total'];
				// $ArrBqTimeHist[$val6Hist]['time_process']	= $valx5Hist['min'];
				// $ArrBqTimeHist[$val6Hist]['man_power']	= $valx5Hist['max'];
				// $ArrBqTimeHist[$val6Hist]['man_hours']	= $valx5Hist['hasil'];
				// $ArrBqTimeHist[$val6Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				// $ArrBqTimeHist[$val6Hist]['hist_date']	= date('Y-m-d H:i:s');
			// }
		// }

		// echo "<pre>";
		// print_r($ArrHeader);
		// print_r($ArrDetail1);
		// print_r($ArrDetail2);
		// print_r($ArrDetail13);
		// print_r($ArrDetailPlus1);
		// print_r($ArrDetailPlus2);
		// print_r($ArrDetailPlus3);
		// print_r($ArrDetailPlus4);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrBqDefault);

		// if(!empty($data['ListDetailAdd_Liner'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd_Strukture'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd_External'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd_TopCoat'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }
			// if(!empty($ArrBqDefaultHist)){
			// 	$this->db->insert_batch('hist_bq_component_default', $ArrBqDefaultHist);
			// }
			// if($qDetailTimeHistNum > 0){
				// $this->db->insert_batch('hist_bq_component_time', $ArrBqTimeHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			
			$this->db->delete('bq_component_default', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1Bef);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2Bef);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3Bef);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4Bef);
			}
			
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			
			if(!empty($data['ListDetailAdd_Liner'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd_Strukture'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd_External'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd_TopCoat'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
			
			$this->db->insert('bq_component_default', $ArrBqDefault);
			
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function pipe_slongsong_edit_bq(){
		// echo "Tahan";
		// exit;
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];

		$ListDetailPlus		= $data['ListDetailPlus'];
		$ListDetailPlus2	= $data['ListDetailPlus2'];

		if(!empty($data['ListDetailAdd'])){
			$ListDetailAdd1	= $data['ListDetailAdd'];
		}
		if(!empty($data['ListDetailAdd2'])){
			$ListDetailAdd2	= $data['ListDetailAdd2'];
		}

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'pipe slongsong',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail2);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Time To Hist
		// $qDetailTimeHist	= $this->db->query("SELECT * FROM component_time WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		// $qDetailTimeHistNum	= $this->db->query("SELECT * FROM component_time WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		// if($qDetailTimeHistNum > 0){
			// foreach($qDetailTimeHist AS $val6Hist => $valx5Hist){
				// $ArrBqTimeHist[$val6Hist]['id_product']	= $valx5Hist['id_product'];
				// $ArrBqTimeHist[$val6Hist]['process']		= $valx5Hist['detail_name'];
				// $ArrBqTimeHist[$val6Hist]['sub_process']	= $valx5Hist['total'];
				// $ArrBqTimeHist[$val6Hist]['time_process']	= $valx5Hist['min'];
				// $ArrBqTimeHist[$val6Hist]['man_power']	= $valx5Hist['max'];
				// $ArrBqTimeHist[$val6Hist]['man_hours']	= $valx5Hist['hasil'];
				// $ArrBqTimeHist[$val6Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				// $ArrBqTimeHist[$val6Hist]['hist_date']	= date('Y-m-d H:i:s');
			// }
		// }

		// echo "<pre>";
		// print_r($ArrHeader);
		// print_r($ArrDetail1);
		// print_r($ArrDetail2);
		// print_r($ArrDetail13);
		// print_r($ArrDetailPlus1);
		// print_r($ArrDetailPlus2);
		// print_r($ArrDetailPlus3);
		// print_r($ArrDetailPlus4);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }
			// if($qDetailTimeHistNum > 0){
				// $this->db->insert_batch('hist_bq_component_time', $ArrBqTimeHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function end_cap_edit_bq(){

		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'end cap',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'radius'				=> $data['radius'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']	= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function blindflange_edit_bq(){
		// echo "Pembatas";
		// exit;

		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'blind flange',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'flange_od'				=> $data['flange_od'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function elbow_mould_edit_bq(){

		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'elbow mould',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'type_elbow'			=> $data['type_elbow'],
			'angle'					=> $data['angle'],
			'radius'				=> $data['radius'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function eccentric_reducer_edit_bq(){

		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'eccentric reducer',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'diameter2'				=> $data['diameter2'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function concentric_reducer_edit_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'concentric reducer',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'diameter2'				=> $data['diameter2'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function equal_tee_mould_edit_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'equal tee mould',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'area2'					=> $data['area2'],
			'wrap_length'			=> $data['wrap_length'],
			'high'					=> $data['high'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']		= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function equal_tee_slongsong_edit_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'equal tee mould',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'area2'					=> $data['area2'],
			'wrap_length'			=> $data['wrap_length'],
			'high'					=> $data['high'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function reducer_tee_mould_edit_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'equal tee mould',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'diameter2'				=> $data['diameter2'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'area2'					=> $data['area2'],
			'wrap_length'			=> $data['wrap_length'],
			'wrap_length2'			=> $data['wrap_length2'],
			'high'					=> $data['high'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function flange_mould_edit_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail2N1	= $data['ListDetail2N1'];
		$ListDetail2N2	= $data['ListDetail2N2'];
		$ListDetail3	= $data['ListDetail3'];

		$ListDetailPlus		= $data['ListDetailPlus'];
		$ListDetailPlus2	= $data['ListDetailPlus2'];
		$ListDetailPlus2N1	= $data['ListDetailPlus2N1'];
		$ListDetailPlus2N2	= $data['ListDetailPlus2N2'];
		$ListDetailPlus3	= $data['ListDetailPlus3'];
		$ListDetailPlus4	= $data['ListDetailPlus4'];

		if(!empty($data['ListDetailAdd'])){
			$ListDetailAdd1	= $data['ListDetailAdd'];
		}
		if(!empty($data['ListDetailAdd2'])){
			$ListDetailAdd2	= $data['ListDetailAdd2'];
		}
		if(!empty($data['ListDetailAdd2N1'])){
			$ListDetailAdd2N1	= $data['ListDetailAdd2N1'];
		}
		if(!empty($data['ListDetailAdd2N2'])){
			$ListDetailAdd2N2	= $data['ListDetailAdd2N2'];
		}
		if(!empty($data['ListDetailAdd3'])){
			$ListDetailAdd3	= $data['ListDetailAdd3'];
		}
		if(!empty($data['ListDetailAdd4'])){
			$ListDetailAdd4	= $data['ListDetailAdd4'];
		}

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'flange mould',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'design'				=> $data['design'],
			'area'					=> $data['area'],

			'panjang_neck_1'		=> $data['panjang_neck_1'],
			'panjang_neck_2'		=> $data['panjang_neck_2'],
			'design_neck_1'			=> $data['design_neck_1'],
			'design_neck_2'			=> $data['design_neck_2'],
			'est_neck_1'			=> $data['est_neck_1'],
			'est_neck_2'			=> $data['est_neck_2'],
			'area_neck_1'			=> $data['area_neck_1'],
			'area_neck_2'			=> $data['area_neck_2'],
			'flange_od'				=> $data['flange_od'],
			'flange_bcd'			=> $data['flange_bcd'],
			'flange_n'				=> $data['flange_n'],
			'flange_oh'				=> $data['flange_oh'],

			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail2);
		// exit;

		//Detail2 N1
		$ArrDetail2N1	= array();
		foreach($ListDetail2N1 AS $val => $valx){
			$IDMat2			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat2			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

			$ArrDetail2N1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2N1[$val]['id_milik'] 	= $data['id_milik'];
			$ArrDetail2N1[$val]['id_bq'] 		= $data['id_bq'];
			$ArrDetail2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
			$ArrDetail2N1[$val]['acuhan'] 		= $data['ThStrN1'];
			$ArrDetail2N1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2N1[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2N1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2N1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2N1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2N1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2N1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2N1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2N1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2N1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2N1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2N1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail2N1);
		// exit;

		//Detail2
		$ArrDetail2N2	= array();
		foreach($ListDetail2N2 AS $val => $valx){
			$IDMat2			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat2			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

			$ArrDetail2N2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2N2[$val]['id_milik'] 	= $data['id_milik'];
			$ArrDetail2N2[$val]['id_bq'] 		= $data['id_bq'];
			$ArrDetail2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
			$ArrDetail2N2[$val]['acuhan'] 		= $data['ThStrN2'];
			$ArrDetail2N2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2N2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2N2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2N2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2N2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2N2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2N2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2N2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2N2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2N2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2N2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2N2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail2N2);
		// exit;

		//Detail3
		$ArrDetail13	= array();
		foreach($ListDetail3 AS $val => $valx){
			$IDMat3			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat3			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus2N1	= array();
		foreach($ListDetailPlus2N1 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2N1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2N1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2N1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
			$ArrDetailPlus2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2N1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2N1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2N1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2N1[$val]['perse'] = $perseM;
			$ArrDetailPlus2N1[$val]['last_full'] 		= '';
			$ArrDetailPlus2N1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2N1);

		$ArrDetailPlus2N2	= array();
		foreach($ListDetailPlus2N2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2N2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2N2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2N2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
			$ArrDetailPlus2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2N2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2N2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2N2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2N2[$val]['perse'] = $perseM;
			$ArrDetailPlus2N2[$val]['last_full'] 		= '';
			$ArrDetailPlus2N2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2N2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter2N1	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2N1'],
				'total'			=> $data['thickStrN1'],
				'min'			=> $data['minStrN1'],
				'max'			=> $data['maxStrN1'],
				'hasil'			=> $data['hasilStrN1']
		);

		$ArrFooter2N2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2N2'],
				'total'			=> $data['thickStrN2'],
				'min'			=> $data['minStrN2'],
				'max'			=> $data['maxStrN2'],
				'hasil'			=> $data['hasilStrN2']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd2N1'])){
			$ArrDataAdd2N1 = array();
			foreach($ListDetailAdd2N1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2N1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2N1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2N1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDataAdd2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2N1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2N1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2N1[$val]['perse'] 		= $perseM;
				$ArrDataAdd2N1[$val]['last_full'] 	= '';
				$ArrDataAdd2N1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2N1);
		}
		if(!empty($data['ListDetailAdd2N2'])){
			$ArrDataAdd2N2 = array();
			foreach($ListDetailAdd2N2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2N2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2N2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2N2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDataAdd2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2N2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2N2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2N2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2N2[$val]['last_full'] 	= '';
				$ArrDataAdd2N2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2N2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']		= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		// echo "<pre>";
		// print_r($ArrHeader);
		// print_r($ArrDetail1);
		// print_r($ArrDetail2);
		// print_r($ArrDetail2N1);
		// print_r($ArrDetail2N2);
		// print_r($ArrDetail13);
		// print_r($ArrDetailPlus1);
		// print_r($ArrDetailPlus2);
		// print_r($ArrDetailPlus2N1);
		// print_r($ArrDetailPlus2N2);
		// print_r($ArrDetailPlus3);
		// print_r($ArrDetailPlus4);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter2N1);
		// print_r($ArrFooter2N2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd2N1'])){
			// print_r($ArrDataAdd2N1);
		// }
		// if(!empty($data['ListDetailAdd2N2'])){
			// print_r($ArrDataAdd2N2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2N1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2N2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2N1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2N2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter2N1);
			$this->db->insert('bq_component_footer', $ArrFooter2N2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd2N1'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2N1);
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2N2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation in BQ code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function flange_slongsong_edit_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail2N1	= $data['ListDetail2N1'];
		$ListDetail2N2	= $data['ListDetail2N2'];
		$ListDetail3	= $data['ListDetail3'];

		$ListDetailPlus		= $data['ListDetailPlus'];
		$ListDetailPlus2	= $data['ListDetailPlus2'];
		$ListDetailPlus2N1	= $data['ListDetailPlus2N1'];
		$ListDetailPlus2N2	= $data['ListDetailPlus2N2'];
		$ListDetailPlus3	= $data['ListDetailPlus3'];
		$ListDetailPlus4	= $data['ListDetailPlus4'];

		if(!empty($data['ListDetailAdd'])){
			$ListDetailAdd1	= $data['ListDetailAdd'];
		}
		if(!empty($data['ListDetailAdd2'])){
			$ListDetailAdd2	= $data['ListDetailAdd2'];
		}
		if(!empty($data['ListDetailAdd2N1'])){
			$ListDetailAdd2N1	= $data['ListDetailAdd2N1'];
		}
		if(!empty($data['ListDetailAdd2N2'])){
			$ListDetailAdd2N2	= $data['ListDetailAdd2N2'];
		}
		if(!empty($data['ListDetailAdd3'])){
			$ListDetailAdd3	= $data['ListDetailAdd3'];
		}
		if(!empty($data['ListDetailAdd4'])){
			$ListDetailAdd4	= $data['ListDetailAdd4'];
		}

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'flange slongsong',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'design'				=> $data['design'],
			'area'					=> $data['area'],

			'panjang_neck_1'		=> $data['panjang_neck_1'],
			'panjang_neck_2'		=> $data['panjang_neck_2'],
			'design_neck_1'			=> $data['design_neck_1'],
			'design_neck_2'			=> $data['design_neck_2'],
			'est_neck_1'			=> $data['est_neck_1'],
			'est_neck_2'			=> $data['est_neck_2'],
			'area_neck_1'			=> $data['area_neck_1'],
			'area_neck_2'			=> $data['area_neck_2'],
			'flange_od'				=> $data['flange_od'],
			'flange_bcd'			=> $data['flange_bcd'],
			'flange_n'				=> $data['flange_n'],
			'flange_oh'				=> $data['flange_oh'],

			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail2);
		// exit;

		//Detail2 N1
		$ArrDetail2N1	= array();
		foreach($ListDetail2N1 AS $val => $valx){
			$IDMat2			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat2			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

			$ArrDetail2N1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2N1[$val]['id_milik'] 	= $data['id_milik'];
			$ArrDetail2N1[$val]['id_bq'] 		= $data['id_bq'];
			$ArrDetail2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
			$ArrDetail2N1[$val]['acuhan'] 		= $data['ThStrN1'];
			$ArrDetail2N1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2N1[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2N1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2N1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2N1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2N1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2N1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2N1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2N1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2N1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2N1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2N1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail2N1);
		// exit;

		//Detail2
		$ArrDetail2N2	= array();
		foreach($ListDetail2N2 AS $val => $valx){
			$IDMat2			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat2			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat2."' LIMIT 1")->result_array();

			$ArrDetail2N2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2N2[$val]['id_milik'] 	= $data['id_milik'];
			$ArrDetail2N2[$val]['id_bq'] 		= $data['id_bq'];
			$ArrDetail2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
			$ArrDetail2N2[$val]['acuhan'] 		= $data['ThStrN2'];
			$ArrDetail2N2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2N2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2N2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2N2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2N2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2N2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2N2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2N2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2N2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2N2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2N2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2N2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail2N2);
		// exit;

		//Detail3
		$ArrDetail13	= array();
		foreach($ListDetail3 AS $val => $valx){
			$IDMat3			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat3			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat3."' LIMIT 1")->result_array();

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus2N1	= array();
		foreach($ListDetailPlus2N1 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2N1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2N1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2N1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
			$ArrDetailPlus2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2N1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2N1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2N1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2N1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2N1[$val]['perse'] = $perseM;
			$ArrDetailPlus2N1[$val]['last_full'] 		= '';
			$ArrDetailPlus2N1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2N1);

		$ArrDetailPlus2N2	= array();
		foreach($ListDetailPlus2N2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2N2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2N2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2N2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
			$ArrDetailPlus2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2N2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2N2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2N2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2N2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2N2[$val]['perse'] = $perseM;
			$ArrDetailPlus2N2[$val]['last_full'] 		= '';
			$ArrDetailPlus2N2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2N2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter2N1	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2N1'],
				'total'			=> $data['thickStrN1'],
				'min'			=> $data['minStrN1'],
				'max'			=> $data['maxStrN1'],
				'hasil'			=> $data['hasilStrN1']
		);

		$ArrFooter2N2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2N2'],
				'total'			=> $data['thickStrN2'],
				'min'			=> $data['minStrN2'],
				'max'			=> $data['maxStrN2'],
				'hasil'			=> $data['hasilStrN2']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd2N1'])){
			$ArrDataAdd2N1 = array();
			foreach($ListDetailAdd2N1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2N1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2N1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2N1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2N1[$val]['detail_name'] 	= $data['detail_name2N1'];
				$ArrDataAdd2N1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2N1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2N1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2N1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2N1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2N1[$val]['perse'] 		= $perseM;
				$ArrDataAdd2N1[$val]['last_full'] 	= '';
				$ArrDataAdd2N1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2N1);
		}
		if(!empty($data['ListDetailAdd2N2'])){
			$ArrDataAdd2N2 = array();
			foreach($ListDetailAdd2N2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2N2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2N2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2N2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2N2[$val]['detail_name'] 	= $data['detail_name2N2'];
				$ArrDataAdd2N2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2N2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2N2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2N2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2N2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2N2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2N2[$val]['last_full'] 	= '';
				$ArrDataAdd2N2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2N2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		// echo "<pre>";
		// print_r($ArrHeader);
		// print_r($ArrDetail1);
		// print_r($ArrDetail2);
		// print_r($ArrDetail2N1);
		// print_r($ArrDetail2N2);
		// print_r($ArrDetail13);
		// print_r($ArrDetailPlus1);
		// print_r($ArrDetailPlus2);
		// print_r($ArrDetailPlus2N1);
		// print_r($ArrDetailPlus2N2);
		// print_r($ArrDetailPlus3);
		// print_r($ArrDetailPlus4);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter2N1);
		// print_r($ArrFooter2N2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd2N1'])){
			// print_r($ArrDataAdd2N1);
		// }
		// if(!empty($data['ListDetailAdd2N2'])){
			// print_r($ArrDataAdd2N2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2N1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2N2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2N1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2N2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter2N1);
			$this->db->insert('bq_component_footer', $ArrFooter2N2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd2N1'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2N1);
			}
			if(!empty($data['ListDetailAdd2N2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2N2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation in BQ code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function reducer_tee_slongsong_edit_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'equal tee mould',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'diameter2'				=> $data['diameter2'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'area'					=> $data['area'],
			'area2'					=> $data['area2'],
			'wrap_length'			=> $data['wrap_length'],
			'wrap_length2'			=> $data['wrap_length2'],
			'high'					=> $data['high'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function elbow_mitter_edit_bq(){

		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$mY				=  date('ym');

		$ListDetail		= $data['ListDetail'];
		$ListDetail2	= $data['ListDetail2'];
		$ListDetail3	= $data['ListDetail3'];

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

		$DataSeries2	= $this->db->query("SELECT * FROM component_group WHERE kode_group='".$data['series']."' LIMIT 1 ")->result_array();

		$resin_sistem	= $DataSeries2[0]['resin_system'];
		$liner			= $DataSeries2[0]['liner'];
		$pressure		= $DataSeries2[0]['pressure'];
		$diameter		= $data['diameter'];

		$DataCust		= $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$data['toleransi']."' LIMIT 1 ")->result_array();
		$DataVacumRate	= $this->db->query("SELECT * FROM list_help WHERE name='".$data['vacum_rate']."' LIMIT 1 ")->result_array();
		$DataApp		= $this->db->query("SELECT * FROM list_help WHERE name='".$data['aplikasi_product']."' LIMIT 1 ")->result_array();

		$ArrHeader	= array(
			'id_product'			=> $data['id_product'],
			'id_milik'				=> $data['id_milik'],
			'id_bq'					=> $data['id_bq'],
			'parent_product'		=> 'elbow mitter',
			'nm_product'			=> $data['top_type'],
			'resin_sistem'			=> $resin_sistem,
			'pressure'				=> $pressure,
			'diameter'				=> $data['diameter'],
			'series'				=> $data['series'],
			'liner'					=> $data['ThLin'],
			'aplikasi_product'		=> $data['top_app'],
			'criminal_barier'		=> $data['criminal_barier'],
			'vacum_rate'			=> $data['vacum_rate'],
			'stiffness'				=> $DataApp[0]['data2'],
			'design_life'			=> $data['design_life'],
			'standart_by'			=> $data['toleransi'],
			'status'				=> $data['status'],
			'sts_price'				=> $data['sts_price'],
			'rev'					=> $data['rev'] + 1,
			'standart_toleransi'	=> $DataCust[0]['nm_customer'],

			'panjang'				=> str_replace(',', '', $data['length']),
			'design'				=> $data['design'],
			'type_elbow'			=> $data['type_elbow'],
			'angle'					=> $data['angle'],
			'radius'				=> $data['radius'],
			'area'					=> $data['area'],
			'est'					=> $data['estimasi'],
			'min_toleransi'			=> $data['min_toleran'],
			'max_toleransi'			=> $data['max_toleran'],
			'waste'					=> $data['waste'],
			'created_by'			=> $data_session['ORI_User']['username'],
			'created_date'			=> date('Y-m-d H:i:s')
		);


		// print_r($ArrHeader); exit;

		// Detail1
		$ArrDetail1	= array();
		foreach($ListDetail AS $val => $valx){
			$IDMat1			= $valx['id_material'];
			if($valx['id_material'] == null || $valx['id_material'] == ''){
				$IDMat1			= "MTL-1903000";
			}
			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$IDMat1."' LIMIT 1")->result_array();

			$ArrDetail1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetail1[$val]['acuhan'] 		= $data['ThLin'];
			$ArrDetail1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail1[$val]['id_material'] 	= $IDMat1;
			$ArrDetail1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail1[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail1[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail1[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail1[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail1[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail1[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail1[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail1[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail1[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail1[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetail2[$val]['acuhan'] 		= $data['ThStr'];
			$ArrDetail2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail2[$val]['id_material'] 	= $IDMat2;
			$ArrDetail2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail2[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail2[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail2[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail2[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail2[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail2[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail2[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail2[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail2[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail2[$val]['last_cost'] 		= $valx['last_cost'];
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

			$ArrDetail13[$val]['id_product'] 	= $data['id_product'];
			$ArrDetail13[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetail13[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetail13[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetail13[$val]['acuhan'] 		= $data['ThEks'];
			$ArrDetail13[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetail13[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetail13[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetail13[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetail13[$val]['id_material'] 	= $IDMat3;
			$ArrDetail13[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$valueM							= (!empty($valx['value']))?$valx['value']:'';
			$ArrDetail13[$val]['value'] 			= $valueM;
				$thicknessM						= (!empty($valx['thickness']))?$valx['thickness']:'';
			$ArrDetail13[$val]['thickness'] 		= $thicknessM;
				$pengaliM						= (!empty($valx['fak_pengali']))?$valx['fak_pengali']:'';
			$ArrDetail13[$val]['fak_pengali'] 	= $pengaliM;
				$bwM							= (!empty($valx['bw']))?$valx['bw']:'';
			$ArrDetail13[$val]['bw'] 			= $bwM;
				$jumlahM						= (!empty($valx['jumlah']))?$valx['jumlah']:'';
			$ArrDetail13[$val]['jumlah'] 		= $jumlahM;
				$layerM							= (!empty($valx['layer']))?$valx['layer']:'';
			$ArrDetail13[$val]['layer'] 			= $layerM;;
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetail13[$val]['containing'] 	= $containingM;
				$total_thicknessM				= (!empty($valx['total_thickness']))?$valx['total_thickness']:'';
			$ArrDetail13[$val]['total_thickness'] = $total_thicknessM;
			$ArrDetail13[$val]['last_full'] 		= $valx['last_full'];
			$ArrDetail13[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetail13);
		// exit;

		$ArrDetailPlus1	= array();
		foreach($ListDetailPlus AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus1[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus1[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus1[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus1[$val]['detail_name'] 	= $data['detail_name'];
			$ArrDetailPlus1[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus1[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus1[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus1[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus1[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus1[$val]['perse'] = $perseM;
			$ArrDetailPlus1[$val]['last_full'] 		= '';
			$ArrDetailPlus1[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus1);

		$ArrDetailPlus2	= array();
		foreach($ListDetailPlus2 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus2[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus2[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus2[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus2[$val]['detail_name'] 	= $data['detail_name2'];
			$ArrDetailPlus2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus2[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus2[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus2[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus2[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus2[$val]['perse'] = $perseM;
			$ArrDetailPlus2[$val]['last_full'] 		= '';
			$ArrDetailPlus2[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus2);

		$ArrDetailPlus3	= array();
		foreach($ListDetailPlus3 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus3[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus3[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus3[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus3[$val]['detail_name'] 	= $data['detail_name3'];
			$ArrDetailPlus3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus3[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus3[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus3[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus3[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus3[$val]['perse'] = $perseM;
			$ArrDetailPlus3[$val]['last_full'] 		= '';
			$ArrDetailPlus3[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus3);

		$ArrDetailPlus4	= array();
		foreach($ListDetailPlus4 AS $val => $valx){

			$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

			$ArrDetailPlus4[$val]['id_product'] 	= $data['id_product'];
			$ArrDetailPlus4[$val]['id_milik'] 		= $data['id_milik'];
			$ArrDetailPlus4[$val]['id_bq'] 			= $data['id_bq'];
			$ArrDetailPlus4[$val]['detail_name'] 	= $data['detail_name4'];
			$ArrDetailPlus4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
			$ArrDetailPlus4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
			$ArrDetailPlus4[$val]['id_material'] 	= $valx['id_material'];
			$ArrDetailPlus4[$val]['id_ori'] 		= $valx['id_ori'];
			$ArrDetailPlus4[$val]['id_ori2'] 		= $valx['id_ori2'];
			$ArrDetailPlus4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
				$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
			$ArrDetailPlus4[$val]['containing'] 	= $containingM;
				$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
			$ArrDetailPlus4[$val]['perse'] = $perseM;
			$ArrDetailPlus4[$val]['last_full'] 		= '';
			$ArrDetailPlus4[$val]['last_cost'] 		= $valx['last_cost'];
		}
		// print_r($ArrDetailPlus4);
		// exit;
		$ArrFooter	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name'],
				'total'			=> $data['thickLin'],
				'min'			=> $data['minLin'],
				'max'			=> $data['maxLin'],
				'hasil'			=> $data['hasilLin']
		);

		$ArrFooter2	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name2'],
				'total'			=> $data['thickStr'],
				'min'			=> $data['minStr'],
				'max'			=> $data['maxStr'],
				'hasil'			=> $data['hasilStr']
		);

		$ArrFooter3	= array(
				'id_product'	=> $data['id_product'],
				'id_milik'		=> $data['id_milik'],
				'id_bq'			=> $data['id_bq'],
				'detail_name'	=> $data['detail_name3'],
				'total'			=> $data['thickEks'],
				'min'			=> $data['minEks'],
				'max'			=> $data['maxEks'],
				'hasil'			=> $data['hasilEks']
		);
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);
		// exit;

		if(!empty($data['ListDetailAdd'])){
			$ArrDataAdd1 = array();
			foreach($ListDetailAdd1 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd1[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd1[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd1[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd1[$val]['detail_name'] 	= $data['detail_name'];
				$ArrDataAdd1[$val]['id_category'] 	=  $dataMaterial[0]['id_category'];
				$ArrDataAdd1[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd1[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd1[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd1[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd1[$val]['perse'] 		= $perseM;
				$ArrDataAdd1[$val]['last_full'] 	= '';
				$ArrDataAdd1[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd1);
		}
		if(!empty($data['ListDetailAdd2'])){
			$ArrDataAdd2 = array();
			foreach($ListDetailAdd2 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd2[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd2[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd2[$val]['id_bq'] 			= $data['id_bq'];
				$ArrDataAdd2[$val]['detail_name'] 	= $data['detail_name2'];
				$ArrDataAdd2[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd2[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd2[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd2[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd2[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd2[$val]['perse'] 		= $perseM;
				$ArrDataAdd2[$val]['last_full'] 	= '';
				$ArrDataAdd2[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd2);
		}
		if(!empty($data['ListDetailAdd3'])){
			$ArrDataAdd3 = array();
			foreach($ListDetailAdd3 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd3[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd3[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd3[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd3[$val]['detail_name'] 	= $data['detail_name3'];
				$ArrDataAdd3[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd3[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd3[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd3[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd3[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd3[$val]['perse'] 		= $perseM;
				$ArrDataAdd3[$val]['last_full'] 	= '';
				$ArrDataAdd3[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd3);
		}
		if(!empty($data['ListDetailAdd4'])){
			$ArrDataAdd4 = array();
			foreach($ListDetailAdd4 AS $val => $valx){
				$dataMaterial	= $this->db->query("SELECT nm_material, id_category, nm_category FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1")->result_array();

				$ArrDataAdd4[$val]['id_product'] 	= $data['id_product'];
				$ArrDataAdd4[$val]['id_milik'] 		= $data['id_milik'];
				$ArrDataAdd4[$val]['id_bq'] 		= $data['id_bq'];
				$ArrDataAdd4[$val]['detail_name'] 	= $data['detail_name4'];
				$ArrDataAdd4[$val]['id_category'] 	= $dataMaterial[0]['id_category'];
				$ArrDataAdd4[$val]['nm_category'] 	= $dataMaterial[0]['nm_category'];
				$ArrDataAdd4[$val]['id_material'] 	= $valx['id_material'];
				$ArrDataAdd4[$val]['nm_material'] 	= $dataMaterial[0]['nm_material'];
					$containingM					= (!empty($valx['containing']))?$valx['containing']:'';
				$ArrDataAdd4[$val]['containing'] 	= $containingM;
					$perseM				= (!empty($valx['perse']))?$valx['perse']:'';
				$ArrDataAdd4[$val]['perse'] 		= $perseM;
				$ArrDataAdd4[$val]['last_full'] 	= '';
				$ArrDataAdd4[$val]['last_cost'] 	= $valx['last_cost'];
			}
			// print_r($ArrDataAdd4);
		}
		// echo "Hay";
		// exit;
		$ArrBqHeaderHist = array();
		$ArrBqDetailHist = array();
		$ArrBqDetailPlusHist = array();
		$ArrBqDetailAddHist = array();
		$ArrBqFooterHist = array();
		$ArrBqTimeHist = array();

		//Insert Component Header To Hist
		$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qHeaderHistNum	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qHeaderHistNum > 0){
			foreach($qHeaderHist AS $val2HistA => $valx2HistA){
				$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
				$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
				$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
				$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
				$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
				$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
				$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
				$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
				$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
				$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
				$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
				$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
				$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
				$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
				$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
				$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
				$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
				$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
				$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
				$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
				$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
				$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
				$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
				$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
				$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
				$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
				$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
				$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
				$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
				$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
				$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
				$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
				$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
				$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
				$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
				$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
				$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
				$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
				$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
				$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
				$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
				$ArrBqHeaderHist[$val2HistA]['hist_by']			= $this->session->userdata['ORI_User']['username'];
				$ArrBqHeaderHist[$val2HistA]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail To Hist
		$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailHistNum	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailHistNum > 0){
			foreach($qDetailHist AS $val2Hist => $valx2Hist){
				$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
				$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
				$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
				$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
				$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
				$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
				$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
				$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
				$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
				$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
				$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
				$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
				$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
				$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
				$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
				$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
				$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
				$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
				$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
				$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
				$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
				$ArrBqDetailHist[$val2Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Plus To Hist
		$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailPlusHistNum	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailPlusHistNum > 0){
			foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
				$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
				$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
				$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
				$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
				$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
				$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
				$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
				$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
				$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
				$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
				$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
				$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
				$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Detail Add To Hist
		$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailAddNumHist	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailAddNumHist > 0){
			foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
				$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
				$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
				$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
				$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
				$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
				$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
				$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
				$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
				$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
				$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
				$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
				$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
				$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
				$ArrBqDetailAddHist[$val4Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqDetailAddHist[$val4Hist]['hist_date']	= date('Y-m-d H:i:s');
			}
		}

		//Insert Component Footer To Hist
		$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->result_array();
		$qDetailFooterHistNum	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$data['id_bq']."' AND id_milik='".$data['id_milik']."' ")->num_rows();
		if($qDetailFooterHistNum > 0){
			foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
				$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
				$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
				$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
				$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
				$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
				$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
				$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
				$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
				$ArrBqFooterHist[$val5Hist]['hist_by']	= $this->session->userdata['ORI_User']['username'];
				$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
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
		// print_r($ArrFooter);
		// print_r($ArrFooter2);
		// print_r($ArrFooter3);

		// if(!empty($data['ListDetailAdd'])){
			// print_r($ArrDataAdd1);
		// }
		// if(!empty($data['ListDetailAdd2'])){
			// print_r($ArrDataAdd2);
		// }
		// if(!empty($data['ListDetailAdd3'])){
			// print_r($ArrDataAdd3);
		// }
		// if(!empty($data['ListDetailAdd4'])){
			// print_r($ArrDataAdd4);
		// }
		// echo "<br>Ingat SAVEDNNYA WHEREnya LHO !!!";
		// exit;

		$this->db->trans_start();
			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(!empty($ArrBqFooterHist)){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }

			//Delete
			$this->db->delete('bq_component_header', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			$this->db->delete('bq_component_footer', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));
			// $this->db->delete('bq_component_time', array('id_bq' => $data['id_bq'], 'id_milik' => $data['id_milik']));

			$this->db->insert('bq_component_header', $ArrHeader);
			$this->db->insert_batch('bq_component_detail', $ArrDetail1);
			$this->db->insert_batch('bq_component_detail', $ArrDetail2);
			$this->db->insert_batch('bq_component_detail', $ArrDetail13);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus1);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus2);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus3);
			$this->db->insert_batch('bq_component_detail_plus', $ArrDetailPlus4);
			$this->db->insert('bq_component_footer', $ArrFooter);
			$this->db->insert('bq_component_footer', $ArrFooter2);
			$this->db->insert('bq_component_footer', $ArrFooter3);
			if(!empty($data['ListDetailAdd'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd1);
			}
			if(!empty($data['ListDetailAdd2'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd2);
			}
			if(!empty($data['ListDetailAdd3'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd3);
			}
			if(!empty($data['ListDetailAdd4'])){
				$this->db->insert_batch('bq_component_detail_add', $ArrDataAdd4);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit Estimation failed. Please try again later ...',
				'status'	=> 2,
				'id_bqJ'	=> $data['id_bq']
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bq'		=> $data['id_bq'],
				'pembeda'	=> $data['url_help'],
				'pesan'		=>'Edit Estimation Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'id_bqJ'	=> $data['id_bq']
			);
			history('Edit Estimation code : '.$data['id_bq'].' to '.$data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function getMaterialx(){
		$id_material	= $this->input->post('id_material');
		$diameter 		= $this->input->post('diameter');
		$resin			= $this->input->post('resin');
		$id_category	= $this->input->post('id_ori');
		$resinOri		= $this->input->post('resinOri');

		// echo $id_material;
		// exit;

		//TYPE RELEASE AGENT $id_category == 'TYP-0030'
		if($id_category == 'TYP-0008'){
			$nm_standard 	= 'thickness';
			$sqlMaterial	= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='".$nm_standard."' LIMIT 1 ";
			$restMicron		= $this->db->query($sqlMaterial)->result();
			if(!empty($restMicron[0]->nilai_standard)){
				if($diameter < 40){$micron	= 0;}
				else{
					if($diameter < 400){$micron	= $restMicron[0]->nilai_standard/1000000;}
					else{$micron	= $restMicron[0]->nilai_standard/1000000;}
				}
			}
			else{$micron = 0;}

			$ArrJson	= array(
				'weight' => $micron
			);
		}
		//TYPE VEIL
		if($id_category == 'TYP-0003' OR $id_category == 'TYP-0004' OR $id_category == 'TYP-0001'){
			$nm_standard 	= 'area weight';

			$sqlMaterial	= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='".$nm_standard."' LIMIT 1 ";
			$restMicron		= $this->db->query($sqlMaterial)->result();
			$NumMic			= $this->db->query($sqlMaterial)->num_rows();

			$micron			= 0;
			$thickness		= 0;
			if($NumMic != 0){
				$micron		=  $restMicron[0]->nilai_standard;
				$thickness	= ($micron/1000/2.56)+($micron/1000/1.2*$resin);
			}

			$resin = $resinOri;
			$LayerR	= "";
			if($id_material == 'MTL-1903000'){
				$resin = "MTL-1903000";
				$LayerR	= 0;
			}

			//resinAkhir
			$sqlResin	= $this->db->query("SELECT id_category FROM raw_materials WHERE id_material ='".$id_material."' LIMIT 1 ")->result();

			$resinAkhir	= "N";
			if($sqlResin[0]->id_category == 'TYP-0001'){
				$resinAkhir	= "Y";
			}

			$ArrJson		= array(
				'weight' 	=> $micron,
				'thickness'	=> $thickness,
				'resin'		=> $resin,
				'resinUt'	=> $id_material,
				'layer'		=> $LayerR,
				'resinAk'	=> $resinAkhir
			);
		}

		if($id_category == 'TYP-0005'){

			$sqlMicron		= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
			$restMicron		= $this->db->query($sqlMicron)->result_array();
			$NumMic			= $this->db->query($sqlMicron)->num_rows();

			// echo $sqlMicron."<br>";

			$weight			= 0;
			$bw				= 0;
			$jumRoov		= 0;
			$thickness		= 0;
			if($NumMic != 0){
				$weight		=  floatval($restMicron[0]['nilai_standard']);
				if($weight != 0 OR $weight != null OR $weight != ''){
					// $bw			= floatval(($weight >= '2200')?'160':(($weight < '2000')?'100':'0'));
					// $jumRoov	= floatval(($weight >= '2200')?'54':(($weight < '2000')?'52':'0'));
					$bw			= floatval($this->input->post('bw'));
					$jumRoov		= floatval($this->input->post('jumlah')); 
					if($bw != 0){
						$thickness	= (($weight/1000)/ $bw * $jumRoov * (2 / 2.56)) + (($weight/1000)/ $bw * $jumRoov * (2 / 1.2) * $resin);
					}
				}
			}

			// echo $weight;
			$resinX = "";
			$LayerR	= "";
			if($id_material == 'MTL-1903000'){
				$resinX = 'MTL-1903000';
				$LayerR	= 0;
			}

			//resinAkhir
			$sqlResin	= $this->db->query("SELECT id_category FROM raw_materials WHERE id_material ='".$id_material."' LIMIT 1 ")->result();

			$resinAkhir	= "N";
			if($sqlResin[0]->id_category == 'TYP-0001'){
				$resinAkhir	= "Y";
			}

			$ArrJson		= array(
				'weight' 	=> $weight,
				'bw' 		=> $bw,
				'jumRoov' 	=> $jumRoov,
				'resin'		=> $resinX,
				'thickness'	=> $thickness,
				'layer'		=> $LayerR,
				'resinUt'	=> $id_material,
				'resinAk'	=> $resinAkhir
			);
		}

		if($id_category == 'TYP-0006'){

			$sqlWrW			= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='area weight' LIMIT 1 ";
			$restWrW		= $this->db->query($sqlWrW)->result_array();
			$NumWrW			= $this->db->query($sqlWrW)->num_rows();

			$sqlWrT			= "SELECT * FROM raw_material_bq_standard WHERE id_material ='".$id_material."' AND nm_standard='thickness' LIMIT 1 ";
			$restWrT		= $this->db->query($sqlWrT)->result_array();
			$NumWrT			= $this->db->query($sqlWrT)->num_rows();

			$weight			= 0;
			$thickness		= 0;
			if($NumWrW != 0){
				$weight		=  $restWrW[0]['nilai_standard'];
			}
			if($NumWrT != 0){
				$thickness	=  $restWrT[0]['nilai_standard'];
			}

			$resinX = "";
			$LayerR	= "";
			if($id_material == 'MTL-1903000'){
				$resinX = 'MTL-1903000';
				$LayerR	= 0;
			}

			//resinAkhir
			$sqlResin	= $this->db->query("SELECT id_category FROM raw_materials WHERE id_material ='".$id_material."' LIMIT 1 ")->result();

			$resinAkhir	= "N";
			if($sqlResin[0]->id_category == 'TYP-0001'){
				$resinAkhir	= "Y";
			}

			$ArrJson		= array(
				'weight' 	=> $weight,
				'resin'		=> $resinX,
				'thickness'	=> $thickness,
				'layer'		=> $LayerR,
				'resinUt'	=> $id_material,
				'resinAk'	=> $resinAkhir
			);
		}

		echo json_encode($ArrJson);
	}

	public function EditSeries(){
		$id_bq 		= $this->uri->segment(3);
		$series 	= $this->uri->segment(4);
		$id 		= $this->uri->segment(5);
		$to_back 	= $this->uri->segment(7);
		$asaltanda 	= $this->uri->segment(6);
		$series_new	= $this->input->post('series_new_'.$id);

		// echo $id_bq."-".$series."-".$series_new;
		// exit;

		$ArrHeader		= array(
			'series' 	=> $series_new
		);

		//Tambahan merubah cycle time
		$qUbah = "SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND series='".$series."' ";
		$restUbah = $this->db->query($qUbah)->result_array();
		$ArrUpdateAll = array();
		foreach($restUbah AS $val=>$valx){
			$wherePN = floatval(substr($series_new, 3,2));
			$whereLN = floatval(substr($series_new, 6,3));
			
			// $wherePlus = ''; 
			// if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
			// 	$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
			// }
			// $qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
			// $restSer = $this->db->query($qSeries)->result();
			$wherePlus = " AND diameter='".$valx['diameter_1']."' ";
			if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
				$wherePlus = " AND diameter='".$valx['diameter_1']."' AND diameter2 = '".$valx['diameter_2']."' ";
			}
			if($valx['id_category'] == 'branch joint'){
				$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
			}
			$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
			$restSer = $this->db->query($qSeries)->result();
			
			$ArrUpdateAll[$val]['id'] = $valx['id'];
			$ArrUpdateAll[$val]['series'] = $series_new;

			$ArrUpdateAll[$val]['man_power'] 	= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
			$ArrUpdateAll[$val]['id_mesin'] 	= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
			$ArrUpdateAll[$val]['total_time'] 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
			$ArrUpdateAll[$val]['man_hours'] 	= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';
			
			// $total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
			// $id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
			// $ArrUpdateAll[$val]['pe_direct_labour'] 			= pe_direct_labour();
			// $ArrUpdateAll[$val]['pe_indirect_labour'] 			= pe_indirect_labour();
			// $ArrUpdateAll[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
			// $ArrUpdateAll[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
			// $ArrUpdateAll[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);
			// $ArrUpdateAll[$val]['pe_foh_consumable'] 			= pe_foh_consumable();
			// $ArrUpdateAll[$val]['pe_foh_depresiasi'] 			= pe_foh_depresiasi();
			// $ArrUpdateAll[$val]['pe_biaya_gaji_non_produksi'] 	= pe_biaya_gaji_non_produksi();
			// $ArrUpdateAll[$val]['pe_biaya_non_produksi'] 		= pe_biaya_non_produksi();
			// $ArrUpdateAll[$val]['pe_biaya_rutin_bulanan'] 		= pe_biaya_rutin_bulanan();
		}
		//end rubah
		// print_r($ArrUpdateAll);
		// exit;
		
		if($asaltanda=='rev'){
			$tandax = 'revisi_quo';
		}
		else{
			$tandax = '';
		}

		$this->db->trans_start();
		$this->db->where(array('id_bq' => $id_bq, 'series' => $series));
		$this->db->update('bq_detail_header', $ArrHeader);

		$this->db->update_batch('bq_detail_header', $ArrUpdateAll, 'id');

		$this->db->where(array('id_bq' => $id_bq, 'series' => $series));
		$this->db->update('bq_detail_detail', $ArrHeader);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update series failed. Please try again later ...',
				'status'	=> 0,
				'to_back'	=> $to_back,
				'tandax'	=> $tandax
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update series success. Thanks ...',
				'status'	=> 1,
				'to_back'	=> $to_back,
				'tandax'	=> $tandax
			);
			history("Change series bq ".$id_bq." : ".$series." to ".$series_new);
		}
		echo json_encode($Arr_Data);
	}
	
	public function updateEstSatuan(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		
		$id_bq			= $this->uri->segment(3);
		$id_milik		= $this->uri->segment(4);
		$panjang		= $this->uri->segment(5);
		$product		= $this->uri->segment(6);
		$pembeda		= $this->uri->segment(7);
		
		// echo $id_bq."<br>";
		// echo $id_milik."<br>";
		// echo $panjang."<br>";
		// echo $product."<br>";
		// echo $pembeda."<br>";
		// exit;

		$ArrBqHeader			= array();
		$ArrBqDetail			= array();
		$ArrBqDetailPlus		= array();
		$ArrBqDetailAdd			= array();
		$ArrBqFooter			= array();
		$ArrBqHeaderHist		= array();
		$ArrBqDetailHist		= array();
		$ArrBqDetailPlusHist	= array();
		$ArrBqDetailAddHist		= array();
		$ArrBqFooterHist		= array();
		$ArrBqDefault			= array();
		$ArrBqDefaultHist		= array();

		$LoopDetail = 0;
		$LoopDetailLam = 0;
		$LoopDetailPlus = 0;
		$LoopDetailAdd = 0;
		$LoopFooter = 0;
		
			//Component Header
			$qHeader	= $this->db->query("SELECT * FROM component_header WHERE id_product='".$product."' LIMIT 1 ")->result();
			$ArrBqHeader['id_product']			= $product;
			$ArrBqHeader['id_bq']					= $id_bq;
			$ArrBqHeader['id_milik']				= $id_milik;
			$ArrBqHeader['parent_product']		= $qHeader[0]->parent_product;
			$ArrBqHeader['nm_product']			= $qHeader[0]->nm_product;
			$ArrBqHeader['standart_code']			= $qHeader[0]->standart_code;
			$ArrBqHeader['series']				= $qHeader[0]->series;
			$ArrBqHeader['resin_sistem']			= $qHeader[0]->resin_sistem;
			$ArrBqHeader['pressure']				= $qHeader[0]->pressure;
			$ArrBqHeader['diameter']				= $qHeader[0]->diameter;
			$ArrBqHeader['liner']					= $qHeader[0]->liner;
			$ArrBqHeader['aplikasi_product']		= $qHeader[0]->aplikasi_product;
			$ArrBqHeader['criminal_barier']		= $qHeader[0]->criminal_barier;
			$ArrBqHeader['vacum_rate']			= $qHeader[0]->vacum_rate;
			$ArrBqHeader['stiffness']				= $qHeader[0]->stiffness;
			$ArrBqHeader['design_life']			= $qHeader[0]->design_life; 
			$ArrBqHeader['standart_by']			= $qHeader[0]->standart_by;
			$ArrBqHeader['standart_toleransi']	= $qHeader[0]->standart_toleransi;
			$ArrBqHeader['diameter2']				= $qHeader[0]->diameter2;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader['panjang']			= floatval($panjang) + 400;
			}
			else{
				$ArrBqHeader['panjang']			= $qHeader[0]->panjang;
			}
			$ArrBqHeader['radius']				= $qHeader[0]->radius;
			$ArrBqHeader['type_elbow']			= $qHeader[0]->type_elbow;
			$ArrBqHeader['angle']					= $qHeader[0]->angle;
			$ArrBqHeader['design']				= $qHeader[0]->design;
			$ArrBqHeader['est']					= $qHeader[0]->est;
			$ArrBqHeader['min_toleransi']			= $qHeader[0]->min_toleransi;
			$ArrBqHeader['max_toleransi']			= $qHeader[0]->max_toleransi;
			$ArrBqHeader['waste']					= $qHeader[0]->waste;
			if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
				$ArrBqHeader['area']				= (floatval($qHeader[0]->area) / floatval($qHeader[0]->panjang)) * (floatval($panjang) + 400);
			}
			else{
				$ArrBqHeader['area']				= $qHeader[0]->area;
			}
			$ArrBqHeader['wrap_length']		= $qHeader[0]->wrap_length;
			$ArrBqHeader['wrap_length2']		= $qHeader[0]->wrap_length2;
			$ArrBqHeader['high']				= $qHeader[0]->high;
			$ArrBqHeader['area2']				= $qHeader[0]->area2;
			$ArrBqHeader['panjang_neck_1']	= $qHeader[0]->panjang_neck_1;
			$ArrBqHeader['panjang_neck_2']	= $qHeader[0]->panjang_neck_2;
			$ArrBqHeader['design_neck_1']		= $qHeader[0]->design_neck_1;
			$ArrBqHeader['design_neck_2']		= $qHeader[0]->design_neck_2;
			$ArrBqHeader['est_neck_1']		= $qHeader[0]->est_neck_1;
			$ArrBqHeader['est_neck_2']		= $qHeader[0]->est_neck_2;
			$ArrBqHeader['area_neck_1']		= $qHeader[0]->area_neck_1;
			$ArrBqHeader['area_neck_2']		= $qHeader[0]->area_neck_2;
			$ArrBqHeader['flange_od']			= $qHeader[0]->flange_od;
			$ArrBqHeader['flange_bcd']		= $qHeader[0]->flange_bcd;
			$ArrBqHeader['flange_n']			= $qHeader[0]->flange_n;
			$ArrBqHeader['flange_oh']			= $qHeader[0]->flange_oh;
			$ArrBqHeader['rev']				= $qHeader[0]->rev;
			$ArrBqHeader['status']			= $qHeader[0]->status;
			$ArrBqHeader['approve_by']		= $qHeader[0]->approve_by;
			$ArrBqHeader['approve_date']		= $qHeader[0]->approve_date;
			$ArrBqHeader['approve_reason']	= $qHeader[0]->approve_reason;
			$ArrBqHeader['sts_price']			= $qHeader[0]->sts_price;
			$ArrBqHeader['sts_price_by']		= $qHeader[0]->sts_price_by;
			$ArrBqHeader['sts_price_date']	= $qHeader[0]->sts_price_date;
			$ArrBqHeader['sts_price_reason']	= $qHeader[0]->sts_price_reason;
			$ArrBqHeader['created_by']		= $qHeader[0]->created_by;
			$ArrBqHeader['created_date']		= $qHeader[0]->created_date;
			$ArrBqHeader['deleted']			= $qHeader[0]->deleted;
			$ArrBqHeader['deleted_by']		= $qHeader[0]->deleted_by;
			$ArrBqHeader['deleted_date']		= $qHeader[0]->deleted_date;
			//
			$ArrBqHeader['pipe_thickness']	= $qHeader[0]->pipe_thickness;
			$ArrBqHeader['joint_thickness']	= $qHeader[0]->joint_thickness;
			$ArrBqHeader['factor_thickness']	= $qHeader[0]->factor_thickness;
			$ArrBqHeader['factor']			= $qHeader[0]->factor;
			
			// print_r($ArrBqHeader);
			// exit;
			//================================================================================================================
			//============================================DEFAULT BY ARWANT===================================================
			//================================================================================================================
			if(!empty($qHeader[0]->standart_code)){
				$plusSQL = "";
				if($qHeader[0]->parent_product == 'concentric reducer' OR $qHeader[0]->parent_product == 'reducer tee mould' OR $qHeader[0]->parent_product == 'eccentric reducer' OR $qHeader[0]->parent_product == 'reducer tee slongsong' OR $qHeader[0]->parent_product == 'branch joint'){
					$plusSQL = " AND diameter2='".$qHeader[0]->diameter2."'";
				}
				$getDefVal		= $this->db->query("SELECT * FROM help_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." LIMIT 1 ")->result();
				$getDefValNum	= $this->db->query("SELECT * FROM help_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." LIMIT 1 ")->num_rows();
				if($getDefValNum > 0){
					$ArrBqDefault['id_product']				= $product;
					$ArrBqDefault['id_bq']					= $id_bq;
					$ArrBqDefault['id_milik']					= $id_milik;
					$ArrBqDefault['product_parent']			= $getDefVal[0]->product_parent;
					$ArrBqDefault['kd_cust']					= $getDefVal[0]->kd_cust;
					$ArrBqDefault['customer']					= $getDefVal[0]->customer;
					$ArrBqDefault['standart_code']			= $getDefVal[0]->standart_code;
					$ArrBqDefault['diameter']					= $getDefVal[0]->diameter;
					$ArrBqDefault['diameter2']				= $getDefVal[0]->diameter2;
					$ArrBqDefault['liner']					= $getDefVal[0]->liner;
					$ArrBqDefault['pn']						= $getDefVal[0]->pn;
					$ArrBqDefault['overlap']					= $getDefVal[0]->overlap;
					$ArrBqDefault['waste']					= $getDefVal[0]->waste;
					$ArrBqDefault['waste_n1']					= $getDefVal[0]->waste_n1;
					$ArrBqDefault['waste_n2']					= $getDefVal[0]->waste_n2;
					$ArrBqDefault['max']						= $getDefVal[0]->max;
					$ArrBqDefault['min']						= $getDefVal[0]->min;
					$ArrBqDefault['plastic_film']				= $getDefVal[0]->plastic_film;
					$ArrBqDefault['lin_resin_veil_a']			= $getDefVal[0]->lin_resin_veil_a;
					$ArrBqDefault['lin_resin_veil_b']			= $getDefVal[0]->lin_resin_veil_b;
					$ArrBqDefault['lin_resin_veil']			= $getDefVal[0]->lin_resin_veil;
					$ArrBqDefault['lin_resin_veil_add_a']		= $getDefVal[0]->lin_resin_veil_add_a;
					$ArrBqDefault['lin_resin_veil_add_b']		= $getDefVal[0]->lin_resin_veil_add_b;
					$ArrBqDefault['lin_resin_veil_add']		= $getDefVal[0]->lin_resin_veil_add;
					$ArrBqDefault['lin_resin_csm_a']			= $getDefVal[0]->lin_resin_csm_a;
					$ArrBqDefault['lin_resin_csm_b']			= $getDefVal[0]->lin_resin_csm_b;
					$ArrBqDefault['lin_resin_csm']			= $getDefVal[0]->lin_resin_csm;
					$ArrBqDefault['lin_resin_csm_add_a']		= $getDefVal[0]->lin_resin_csm_add_a;
					$ArrBqDefault['lin_resin_csm_add_b']		= $getDefVal[0]->lin_resin_csm_add_b;
					$ArrBqDefault['lin_resin_csm_add']		= $getDefVal[0]->lin_resin_csm_add;
					$ArrBqDefault['lin_faktor_veil']			= $getDefVal[0]->lin_faktor_veil;
					$ArrBqDefault['lin_faktor_veil_add']		= $getDefVal[0]->lin_faktor_veil_add;
					$ArrBqDefault['lin_faktor_csm']			= $getDefVal[0]->lin_faktor_csm;
					$ArrBqDefault['lin_faktor_csm_add']		= $getDefVal[0]->lin_faktor_csm_add;
					$ArrBqDefault['lin_resin']				= $getDefVal[0]->lin_resin;
					$ArrBqDefault['lin_resin_thickness']		= $getDefVal[0]->lin_resin_thickness;
					$ArrBqDefault['str_resin_csm_a']			= $getDefVal[0]->str_resin_csm_a;
					$ArrBqDefault['str_resin_csm_b']			= $getDefVal[0]->str_resin_csm_b;
					$ArrBqDefault['str_resin_csm']			= $getDefVal[0]->str_resin_csm;
					$ArrBqDefault['str_resin_csm_add_a']		= $getDefVal[0]->str_resin_csm_add_a;
					$ArrBqDefault['str_resin_csm_add_b']		= $getDefVal[0]->str_resin_csm_add_b;
					$ArrBqDefault['str_resin_csm_add']		= $getDefVal[0]->str_resin_csm_add;
					$ArrBqDefault['str_resin_wr_a']			= $getDefVal[0]->str_resin_wr_a;
					$ArrBqDefault['str_resin_wr_b']			= $getDefVal[0]->str_resin_wr_b;
					$ArrBqDefault['str_resin_wr']				= $getDefVal[0]->str_resin_wr;
					$ArrBqDefault['str_resin_wr_add_a']		= $getDefVal[0]->str_resin_wr_add_a;
					$ArrBqDefault['str_resin_wr_add_b']		= $getDefVal[0]->str_resin_wr_add_b;
					$ArrBqDefault['str_resin_wr_add']			= $getDefVal[0]->str_resin_wr_add;
					$ArrBqDefault['str_resin_rv_a']			= $getDefVal[0]->str_resin_rv_a;
					$ArrBqDefault['str_resin_rv_b']			= $getDefVal[0]->str_resin_rv_b;
					$ArrBqDefault['str_resin_rv']				= $getDefVal[0]->str_resin_rv;
					$ArrBqDefault['str_resin_rv_add_a']		= $getDefVal[0]->str_resin_rv_add_a;
					$ArrBqDefault['str_resin_rv_add_b']		= $getDefVal[0]->str_resin_rv_add_b;
					$ArrBqDefault['str_resin_rv_add']			= $getDefVal[0]->str_resin_rv_add;
					$ArrBqDefault['str_faktor_csm']			= $getDefVal[0]->str_faktor_csm;
					$ArrBqDefault['str_faktor_csm_add']		= $getDefVal[0]->str_faktor_csm_add;
					$ArrBqDefault['str_faktor_wr']			= $getDefVal[0]->str_faktor_wr;
					$ArrBqDefault['str_faktor_wr_add']		= $getDefVal[0]->str_faktor_wr_add;
					$ArrBqDefault['str_faktor_rv']			= $getDefVal[0]->str_faktor_rv;
					$ArrBqDefault['str_faktor_rv_bw']			= $getDefVal[0]->str_faktor_rv_bw;
					$ArrBqDefault['str_faktor_rv_jb']			= $getDefVal[0]->str_faktor_rv_jb;
					$ArrBqDefault['str_faktor_rv_add']		= $getDefVal[0]->str_faktor_rv_add;
					$ArrBqDefault['str_faktor_rv_add_bw']		= $getDefVal[0]->str_faktor_rv_add_bw;
					$ArrBqDefault['str_faktor_rv_add_jb']		= $getDefVal[0]->str_faktor_rv_add_jb;
					$ArrBqDefault['str_resin']				= $getDefVal[0]->str_resin;
					$ArrBqDefault['str_resin_thickness']		= $getDefVal[0]->str_resin_thickness;
					$ArrBqDefault['eks_resin_veil_a']			= $getDefVal[0]->eks_resin_veil_a;
					$ArrBqDefault['eks_resin_veil_b']			= $getDefVal[0]->eks_resin_veil_b;
					$ArrBqDefault['eks_resin_veil']			= $getDefVal[0]->eks_resin_veil;
					$ArrBqDefault['eks_resin_veil_add_a']		= $getDefVal[0]->eks_resin_veil_add_a;
					$ArrBqDefault['eks_resin_veil_add_b']		= $getDefVal[0]->eks_resin_veil_add_b;
					$ArrBqDefault['eks_resin_veil_add']		= $getDefVal[0]->eks_resin_veil_add;
					$ArrBqDefault['eks_resin_csm_a']			= $getDefVal[0]->eks_resin_csm_a;
					$ArrBqDefault['eks_resin_csm_b']			= $getDefVal[0]->eks_resin_csm_b;
					$ArrBqDefault['eks_resin_csm']			= $getDefVal[0]->eks_resin_csm;
					$ArrBqDefault['eks_resin_csm_add_a']		= $getDefVal[0]->eks_resin_csm_add_a;
					$ArrBqDefault['eks_resin_csm_add_b']		= $getDefVal[0]->eks_resin_csm_add_b;
					$ArrBqDefault['eks_resin_csm_add']		= $getDefVal[0]->eks_resin_csm_add;
					$ArrBqDefault['eks_faktor_veil']			= $getDefVal[0]->eks_faktor_veil;
					$ArrBqDefault['eks_faktor_veil_add']		= $getDefVal[0]->eks_faktor_veil_add;
					$ArrBqDefault['eks_faktor_csm']			= $getDefVal[0]->eks_faktor_csm;
					$ArrBqDefault['eks_faktor_csm_add']		= $getDefVal[0]->eks_faktor_csm_add;
					$ArrBqDefault['eks_resin']				= $getDefVal[0]->eks_resin;
					$ArrBqDefault['eks_resin_thickness']		= $getDefVal[0]->eks_resin_thickness;
					$ArrBqDefault['topcoat_resin']			= $getDefVal[0]->topcoat_resin;
					$ArrBqDefault['str_n1_resin_csm_a']		= $getDefVal[0]->str_n1_resin_csm_a;
					$ArrBqDefault['str_n1_resin_csm_b']		= $getDefVal[0]->str_n1_resin_csm_b;
					$ArrBqDefault['str_n1_resin_csm']			= $getDefVal[0]->str_n1_resin_csm;
					$ArrBqDefault['str_n1_resin_csm_add_a']	= $getDefVal[0]->str_n1_resin_csm_add_a;
					$ArrBqDefault['str_n1_resin_csm_add_b']	= $getDefVal[0]->str_n1_resin_csm_add_b;
					$ArrBqDefault['str_n1_resin_csm_add']		= $getDefVal[0]->str_n1_resin_csm_add;
					$ArrBqDefault['str_n1_resin_wr_a']		= $getDefVal[0]->str_n1_resin_wr_a;
					$ArrBqDefault['str_n1_resin_wr_b']		= $getDefVal[0]->str_n1_resin_wr_b;
					$ArrBqDefault['str_n1_resin_wr']			= $getDefVal[0]->str_n1_resin_wr;
					$ArrBqDefault['str_n1_resin_wr_add_a']	= $getDefVal[0]->str_n1_resin_wr_add_a;
					$ArrBqDefault['str_n1_resin_wr_add_b']	= $getDefVal[0]->str_n1_resin_wr_add_b;
					$ArrBqDefault['str_n1_resin_wr_add']		= $getDefVal[0]->str_n1_resin_wr_add;
					$ArrBqDefault['str_n1_resin_rv_a']		= $getDefVal[0]->str_n1_resin_rv_a;
					$ArrBqDefault['str_n1_resin_rv_b']		= $getDefVal[0]->str_n1_resin_rv_b;
					$ArrBqDefault['str_n1_resin_rv']			= $getDefVal[0]->str_n1_resin_rv;
					$ArrBqDefault['str_n1_resin_rv_add_a']	= $getDefVal[0]->str_n1_resin_rv_add_a;
					$ArrBqDefault['str_n1_resin_rv_add_b']	= $getDefVal[0]->str_n1_resin_rv_add_b;
					$ArrBqDefault['str_n1_resin_rv_add']		= $getDefVal[0]->str_n1_resin_rv_add;
					$ArrBqDefault['str_n1_faktor_csm']		= $getDefVal[0]->str_n1_faktor_csm;
					$ArrBqDefault['str_n1_faktor_csm_add']	= $getDefVal[0]->str_n1_faktor_csm_add;
					$ArrBqDefault['str_n1_faktor_wr']			= $getDefVal[0]->str_n1_faktor_wr;
					$ArrBqDefault['str_n1_faktor_wr_add']		= $getDefVal[0]->str_n1_faktor_wr_add;
					$ArrBqDefault['str_n1_faktor_rv']			= $getDefVal[0]->str_n1_faktor_rv;
					$ArrBqDefault['str_n1_faktor_rv_bw']		= $getDefVal[0]->str_n1_faktor_rv_bw;
					$ArrBqDefault['str_n1_faktor_rv_jb']		= $getDefVal[0]->str_n1_faktor_rv_jb;
					$ArrBqDefault['str_n1_faktor_rv_add']		= $getDefVal[0]->str_n1_faktor_rv_add;
					$ArrBqDefault['str_n1_faktor_rv_add_bw']	= $getDefVal[0]->str_n1_faktor_rv_add_bw;
					$ArrBqDefault['str_n1_faktor_rv_add_jb']	= $getDefVal[0]->str_n1_faktor_rv_add_jb;
					$ArrBqDefault['str_n1_resin']				= $getDefVal[0]->str_n1_resin;
					$ArrBqDefault['str_n1_resin_thickness']	= $getDefVal[0]->str_n1_resin_thickness;
					$ArrBqDefault['str_n2_resin_csm_a']		= $getDefVal[0]->str_n2_resin_csm_a;
					$ArrBqDefault['str_n2_resin_csm_b']		= $getDefVal[0]->str_n2_resin_csm_b;
					$ArrBqDefault['str_n2_resin_csm']			= $getDefVal[0]->str_n2_resin_csm;
					$ArrBqDefault['str_n2_resin_csm_add_a']	= $getDefVal[0]->str_n2_resin_csm_add_a;
					$ArrBqDefault['str_n2_resin_csm_add_b']	= $getDefVal[0]->str_n2_resin_csm_add_b;
					$ArrBqDefault['str_n2_resin_csm_add']		= $getDefVal[0]->str_n2_resin_csm_add;
					$ArrBqDefault['str_n2_resin_wr_a']		= $getDefVal[0]->str_n2_resin_wr_a;
					$ArrBqDefault['str_n2_resin_wr_b']		= $getDefVal[0]->str_n2_resin_wr_b;
					$ArrBqDefault['str_n2_resin_wr']			= $getDefVal[0]->str_n2_resin_wr;
					$ArrBqDefault['str_n2_resin_wr_add_a']	= $getDefVal[0]->str_n2_resin_wr_add_a;
					$ArrBqDefault['str_n2_resin_wr_add_b']	= $getDefVal[0]->str_n2_resin_wr_add_b;
					$ArrBqDefault['str_n2_resin_wr_add']		= $getDefVal[0]->str_n2_resin_wr_add;
					$ArrBqDefault['str_n2_faktor_csm']		= $getDefVal[0]->str_n2_faktor_csm;
					$ArrBqDefault['str_n2_faktor_csm_add']	= $getDefVal[0]->str_n2_faktor_csm_add;
					$ArrBqDefault['str_n2_faktor_wr']			= $getDefVal[0]->str_n2_faktor_wr;
					$ArrBqDefault['str_n2_faktor_wr_add']		= $getDefVal[0]->str_n2_faktor_wr_add;
					$ArrBqDefault['str_n2_resin']				= $getDefVal[0]->str_n2_resin;
					$ArrBqDefault['str_n2_resin_thickness']	= $getDefVal[0]->str_n2_resin_thickness;
					$ArrBqDefault['created_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefault['created_date']				= date('Y-m-d H:i:s');
				}
			}
			
			//Insert Component Header To Hist
			$qHeaderHistDef		= $this->db->query("SELECT * FROM bq_component_default WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qHeaderHistDef)){
				foreach($qHeaderHistDef AS $val2HistADef => $valx2HistADef){
					$ArrBqDefaultHist[$val2HistADef]['id_product']				= $valx2HistADef['id_product'];
					$ArrBqDefaultHist[$val2HistADef]['id_milik']				= $valx2HistADef['id_milik'];
					$ArrBqDefaultHist[$val2HistADef]['id_bq']					= $valx2HistADef['id_bq'];
					$ArrBqDefaultHist[$val2HistADef]['product_parent']			= $valx2HistADef['product_parent'];
					$ArrBqDefaultHist[$val2HistADef]['kd_cust']					= $valx2HistADef['kd_cust'];
					$ArrBqDefaultHist[$val2HistADef]['customer']				= $valx2HistADef['customer'];
					$ArrBqDefaultHist[$val2HistADef]['standart_code']			= $valx2HistADef['standart_code'];
					$ArrBqDefaultHist[$val2HistADef]['diameter']				= $valx2HistADef['diameter'];
					$ArrBqDefaultHist[$val2HistADef]['diameter2']				= $valx2HistADef['diameter2'];
					$ArrBqDefaultHist[$val2HistADef]['liner']					= $valx2HistADef['liner'];
					$ArrBqDefaultHist[$val2HistADef]['pn']						= $valx2HistADef['pn'];
					$ArrBqDefaultHist[$val2HistADef]['overlap']					= $valx2HistADef['overlap'];
					$ArrBqDefaultHist[$val2HistADef]['waste']					= $valx2HistADef['waste'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n1']				= $valx2HistADef['waste_n1'];
					$ArrBqDefaultHist[$val2HistADef]['waste_n2']				= $valx2HistADef['waste_n2'];
					$ArrBqDefaultHist[$val2HistADef]['max']						= $valx2HistADef['max'];
					$ArrBqDefaultHist[$val2HistADef]['min']						= $valx2HistADef['min'];
					$ArrBqDefaultHist[$val2HistADef]['plastic_film']			= $valx2HistADef['plastic_film'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_a']		= $valx2HistADef['lin_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_b']		= $valx2HistADef['lin_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil']			= $valx2HistADef['lin_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_a']	= $valx2HistADef['lin_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add_b']	= $valx2HistADef['lin_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_veil_add']		= $valx2HistADef['lin_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_a']			= $valx2HistADef['lin_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_b']			= $valx2HistADef['lin_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm']			= $valx2HistADef['lin_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_a']		= $valx2HistADef['lin_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add_b']		= $valx2HistADef['lin_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_csm_add']		= $valx2HistADef['lin_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil']			= $valx2HistADef['lin_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_veil_add']		= $valx2HistADef['lin_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm']			= $valx2HistADef['lin_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['lin_faktor_csm_add']		= $valx2HistADef['lin_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin']				= $valx2HistADef['lin_resin'];
					$ArrBqDefaultHist[$val2HistADef]['lin_resin_thickness']		= $valx2HistADef['lin_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_a']			= $valx2HistADef['str_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_b']			= $valx2HistADef['str_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm']			= $valx2HistADef['str_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_a']		= $valx2HistADef['str_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add_b']		= $valx2HistADef['str_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_csm_add']		= $valx2HistADef['str_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_a']			= $valx2HistADef['str_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_b']			= $valx2HistADef['str_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr']			= $valx2HistADef['str_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_a']		= $valx2HistADef['str_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add_b']		= $valx2HistADef['str_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_wr_add']		= $valx2HistADef['str_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_a']			= $valx2HistADef['str_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_b']			= $valx2HistADef['str_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv']			= $valx2HistADef['str_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_a']		= $valx2HistADef['str_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add_b']		= $valx2HistADef['str_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_rv_add']		= $valx2HistADef['str_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm']			= $valx2HistADef['str_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_csm_add']		= $valx2HistADef['str_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr']			= $valx2HistADef['str_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_wr_add']		= $valx2HistADef['str_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv']			= $valx2HistADef['str_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_bw']		= $valx2HistADef['str_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_jb']		= $valx2HistADef['str_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add']		= $valx2HistADef['str_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_bw']	= $valx2HistADef['str_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_faktor_rv_add_jb']	= $valx2HistADef['str_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin']				= $valx2HistADef['str_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_resin_thickness']		= $valx2HistADef['str_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_a']		= $valx2HistADef['eks_resin_veil_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_b']		= $valx2HistADef['eks_resin_veil_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil']			= $valx2HistADef['eks_resin_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_a']	= $valx2HistADef['eks_resin_veil_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add_b']	= $valx2HistADef['eks_resin_veil_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_veil_add']		= $valx2HistADef['eks_resin_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_a']			= $valx2HistADef['eks_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_b']			= $valx2HistADef['eks_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm']			= $valx2HistADef['eks_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_a']		= $valx2HistADef['eks_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add_b']		= $valx2HistADef['eks_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_csm_add']		= $valx2HistADef['eks_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil']			= $valx2HistADef['eks_faktor_veil'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_veil_add']		= $valx2HistADef['eks_faktor_veil_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm']			= $valx2HistADef['eks_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['eks_faktor_csm_add']		= $valx2HistADef['eks_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin']				= $valx2HistADef['eks_resin'];
					$ArrBqDefaultHist[$val2HistADef]['eks_resin_thickness']		= $valx2HistADef['eks_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['topcoat_resin']			= $valx2HistADef['topcoat_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_a']		= $valx2HistADef['str_n1_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_b']		= $valx2HistADef['str_n1_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm']		= $valx2HistADef['str_n1_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_a']	= $valx2HistADef['str_n1_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add_b']	= $valx2HistADef['str_n1_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_csm_add']	= $valx2HistADef['str_n1_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_a']		= $valx2HistADef['str_n1_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_b']		= $valx2HistADef['str_n1_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr']			= $valx2HistADef['str_n1_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_a']	= $valx2HistADef['str_n1_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add_b']	= $valx2HistADef['str_n1_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_wr_add']		= $valx2HistADef['str_n1_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_a']		= $valx2HistADef['str_n1_resin_rv_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_b']		= $valx2HistADef['str_n1_resin_rv_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv']			= $valx2HistADef['str_n1_resin_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_a']	= $valx2HistADef['str_n1_resin_rv_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add_b']	= $valx2HistADef['str_n1_resin_rv_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_rv_add']		= $valx2HistADef['str_n1_resin_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm']		= $valx2HistADef['str_n1_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_csm_add']	= $valx2HistADef['str_n1_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr']		= $valx2HistADef['str_n1_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_wr_add']	= $valx2HistADef['str_n1_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv']		= $valx2HistADef['str_n1_faktor_rv'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_bw']		= $valx2HistADef['str_n1_faktor_rv_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_jb']		= $valx2HistADef['str_n1_faktor_rv_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add']	= $valx2HistADef['str_n1_faktor_rv_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_bw']	= $valx2HistADef['str_n1_faktor_rv_add_bw'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_faktor_rv_add_jb']	= $valx2HistADef['str_n1_faktor_rv_add_jb'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin']			= $valx2HistADef['str_n1_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n1_resin_thickness']	= $valx2HistADef['str_n1_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_a']		= $valx2HistADef['str_n2_resin_csm_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_b']		= $valx2HistADef['str_n2_resin_csm_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm']		= $valx2HistADef['str_n2_resin_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_a']	= $valx2HistADef['str_n2_resin_csm_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add_b']	= $valx2HistADef['str_n2_resin_csm_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_csm_add']	= $valx2HistADef['str_n2_resin_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_a']		= $valx2HistADef['str_n2_resin_wr_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_b']		= $valx2HistADef['str_n2_resin_wr_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr']			= $valx2HistADef['str_n2_resin_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_a']	= $valx2HistADef['str_n2_resin_wr_add_a'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add_b']	= $valx2HistADef['str_n2_resin_wr_add_b'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_wr_add']		= $valx2HistADef['str_n2_resin_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm']		= $valx2HistADef['str_n2_faktor_csm'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_csm_add']	= $valx2HistADef['str_n2_faktor_csm_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr']		= $valx2HistADef['str_n2_faktor_wr'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_faktor_wr_add']	= $valx2HistADef['str_n2_faktor_wr_add'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin']			= $valx2HistADef['str_n2_resin'];
					$ArrBqDefaultHist[$val2HistADef]['str_n2_resin_thickness']	= $valx2HistADef['str_n2_resin_thickness'];
					$ArrBqDefaultHist[$val2HistADef]['created_by']				= $valx2HistADef['created_by'];
					$ArrBqDefaultHist[$val2HistADef]['created_date']			= $valx2HistADef['created_date'];
					$ArrBqDefaultHist[$val2HistADef]['modified_by']				= $valx2HistADef['modified_by'];
					$ArrBqDefaultHist[$val2HistADef]['modified_date']			= $valx2HistADef['modified_date'];
					$ArrBqDefaultHist[$val2HistADef]['hist_by']					= $this->session->userdata['ORI_User']['username'];
					$ArrBqDefaultHist[$val2HistADef]['hist_date']				= date('Y-m-d H:i:s');
					
					
				}
			}
			//================================================================================================================
			//================================================================================================================
			//================================================================================================================
			
			//Insert Component Header To Hist
			$qHeaderHist	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qHeaderHist)){
				foreach($qHeaderHist AS $val2HistA => $valx2HistA){
					$ArrBqHeaderHist[$val2HistA]['id_product']			= $valx2HistA['id_product'];
					$ArrBqHeaderHist[$val2HistA]['id_milik']			= $valx2HistA['id_milik'];
					$ArrBqHeaderHist[$val2HistA]['id_bq']				= $valx2HistA['id_bq'];
					$ArrBqHeaderHist[$val2HistA]['parent_product']		= $valx2HistA['parent_product'];
					$ArrBqHeaderHist[$val2HistA]['nm_product']			= $valx2HistA['nm_product'];
					$ArrBqHeaderHist[$val2HistA]['series']				= $valx2HistA['series'];
					$ArrBqHeaderHist[$val2HistA]['resin_sistem']		= $valx2HistA['resin_sistem'];
					$ArrBqHeaderHist[$val2HistA]['pressure']			= $valx2HistA['pressure'];
					$ArrBqHeaderHist[$val2HistA]['diameter']			= $valx2HistA['diameter'];
					$ArrBqHeaderHist[$val2HistA]['liner']				= $valx2HistA['liner'];
					$ArrBqHeaderHist[$val2HistA]['aplikasi_product']	= $valx2HistA['aplikasi_product'];
					$ArrBqHeaderHist[$val2HistA]['criminal_barier']		= $valx2HistA['criminal_barier'];
					$ArrBqHeaderHist[$val2HistA]['vacum_rate']			= $valx2HistA['vacum_rate'];
					$ArrBqHeaderHist[$val2HistA]['stiffness']			= $valx2HistA['stiffness'];
					$ArrBqHeaderHist[$val2HistA]['design_life']			= $valx2HistA['design_life'];
					$ArrBqHeaderHist[$val2HistA]['standart_by']			= $valx2HistA['standart_by'];
					$ArrBqHeaderHist[$val2HistA]['standart_toleransi']	= $valx2HistA['standart_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['diameter2']			= $valx2HistA['diameter2'];
					$ArrBqHeaderHist[$val2HistA]['panjang']				= $valx2HistA['panjang'];
					$ArrBqHeaderHist[$val2HistA]['radius']				= $valx2HistA['radius'];
					$ArrBqHeaderHist[$val2HistA]['type_elbow']			= $valx2HistA['type_elbow'];
					$ArrBqHeaderHist[$val2HistA]['angle']				= $valx2HistA['angle'];
					$ArrBqHeaderHist[$val2HistA]['design']				= $valx2HistA['design'];
					$ArrBqHeaderHist[$val2HistA]['est']					= $valx2HistA['est'];
					$ArrBqHeaderHist[$val2HistA]['min_toleransi']		= $valx2HistA['min_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['max_toleransi']		= $valx2HistA['max_toleransi'];
					$ArrBqHeaderHist[$val2HistA]['waste']				= $valx2HistA['waste'];
					$ArrBqHeaderHist[$val2HistA]['area']				= $valx2HistA['area'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length']			= $valx2HistA['wrap_length'];
					$ArrBqHeaderHist[$val2HistA]['wrap_length2']		= $valx2HistA['wrap_length2'];
					$ArrBqHeaderHist[$val2HistA]['high']				= $valx2HistA['high'];
					$ArrBqHeaderHist[$val2HistA]['area2']				= $valx2HistA['area2'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_1']		= $valx2HistA['panjang_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['panjang_neck_2']		= $valx2HistA['panjang_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_1']		= $valx2HistA['design_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['design_neck_2']		= $valx2HistA['design_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_1']			= $valx2HistA['est_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['est_neck_2']			= $valx2HistA['est_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_1']			= $valx2HistA['area_neck_1'];
					$ArrBqHeaderHist[$val2HistA]['area_neck_2']			= $valx2HistA['area_neck_2'];
					$ArrBqHeaderHist[$val2HistA]['flange_od']			= $valx2HistA['flange_od'];
					$ArrBqHeaderHist[$val2HistA]['flange_bcd']			= $valx2HistA['flange_bcd'];
					$ArrBqHeaderHist[$val2HistA]['flange_n']			= $valx2HistA['flange_n'];
					$ArrBqHeaderHist[$val2HistA]['flange_oh']			= $valx2HistA['flange_oh'];
					$ArrBqHeaderHist[$val2HistA]['rev']					= $valx2HistA['rev'];
					$ArrBqHeaderHist[$val2HistA]['status']				= $valx2HistA['status'];
					$ArrBqHeaderHist[$val2HistA]['approve_by']			= $valx2HistA['approve_by'];
					$ArrBqHeaderHist[$val2HistA]['approve_date']		= $valx2HistA['approve_date'];
					$ArrBqHeaderHist[$val2HistA]['approve_reason']		= $valx2HistA['approve_reason'];
					$ArrBqHeaderHist[$val2HistA]['sts_price']			= $valx2HistA['sts_price'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_by']		= $valx2HistA['sts_price_by'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_date']		= $valx2HistA['sts_price_date'];
					$ArrBqHeaderHist[$val2HistA]['sts_price_reason']	= $valx2HistA['sts_price_reason'];
					$ArrBqHeaderHist[$val2HistA]['created_by']			= $valx2HistA['created_by'];
					$ArrBqHeaderHist[$val2HistA]['created_date']		= $valx2HistA['created_date'];
					$ArrBqHeaderHist[$val2HistA]['deleted']				= $valx2HistA['deleted'];
					$ArrBqHeaderHist[$val2HistA]['deleted_by']			= $valx2HistA['deleted_by'];
					$ArrBqHeaderHist[$val2HistA]['deleted_date']		= $valx2HistA['deleted_date'];
					$ArrBqHeaderHist[$val2HistA]['hist_by']				= $this->session->userdata['ORI_User']['username'];
					$ArrBqHeaderHist[$val2HistA]['hist_date']			= date('Y-m-d H:i:s');
					
				}
			}

			//Component Detail
			$qDetail	= $this->db->query("SELECT a.*, b.panjang FROM component_detail a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$product."' ")->result_array();
			foreach($qDetail AS $val2 => $valx2){
				$LoopDetail++;

				// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx2['id_material']."' ";
				// $restPrice = $this->db->query($sqlPrice)->result();

				$ArrBqDetail[$LoopDetail]['id_product']		= $product;
				$ArrBqDetail[$LoopDetail]['id_bq']			= $id_bq;
				$ArrBqDetail[$LoopDetail]['id_milik']		= $id_milik;
				$ArrBqDetail[$LoopDetail]['detail_name']	= $valx2['detail_name'];
				$ArrBqDetail[$LoopDetail]['acuhan']			= $valx2['acuhan'];
				$ArrBqDetail[$LoopDetail]['id_ori']			= $valx2['id_ori'];
				$ArrBqDetail[$LoopDetail]['id_ori2']		= $valx2['id_ori2'];
				$ArrBqDetail[$LoopDetail]['id_category']	= $valx2['id_category'];
				$ArrBqDetail[$LoopDetail]['nm_category']	= $valx2['nm_category'];
				$ArrBqDetail[$LoopDetail]['id_material']	= $valx2['id_material'];
				$ArrBqDetail[$LoopDetail]['nm_material']	= $valx2['nm_material'];
				$ArrBqDetail[$LoopDetail]['value']			= $valx2['value'];
				$ArrBqDetail[$LoopDetail]['thickness']		= $valx2['thickness'];
				$ArrBqDetail[$LoopDetail]['fak_pengali']	= $valx2['fak_pengali'];
				$ArrBqDetail[$LoopDetail]['bw']				= $valx2['bw'];
				$ArrBqDetail[$LoopDetail]['jumlah']			= $valx2['jumlah'];
				$ArrBqDetail[$LoopDetail]['layer']			= $valx2['layer'];
				$ArrBqDetail[$LoopDetail]['containing']		= $valx2['containing'];
				$ArrBqDetail[$LoopDetail]['total_thickness']	= $valx2['total_thickness'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / floatval($valx2['panjang']))* (floatval($panjang) + 400);
				}
				elseif ($qHeader[0]->parent_product == 'branch joint' OR $qHeader[0]->parent_product == 'field joint' OR $qHeader[0]->parent_product == 'shop joint') {
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['material_weight'];
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetail[$LoopDetail]['last_cost']	= (floatval($valx2['last_cost']) / 1000) * floatval($panjang);
				}
				else{
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['last_cost'];
				}
				$ArrBqDetail[$LoopDetail]['rev']				= $qHeader[0]->rev;
				//
				$ArrBqDetail[$LoopDetail]['area_weight']		= $valx2['area_weight'];
				$ArrBqDetail[$LoopDetail]['material_weight']	= $valx2['material_weight'];
				$ArrBqDetail[$LoopDetail]['percentage']			= $valx2['percentage'];
				$ArrBqDetail[$LoopDetail]['resin_content']		= $valx2['resin_content'];

				$ArrBqDetail[$LoopDetail]['price_mat']				= get_price_ref($valx2['id_material']);
			}

			//Component Lamination
			$qDetailLam	= $this->db->query("SELECT * FROM component_lamination WHERE id_product='".$product."' ")->result_array();
			foreach($qDetailLam AS $val2 => $valx2){
				$LoopDetailLam++;
				$ArrBqDetailLam[$LoopDetailLam]['id_product']	= $product;
				$ArrBqDetailLam[$LoopDetailLam]['id_bq']		= $id_bq;
				$ArrBqDetailLam[$LoopDetailLam]['id_milik']		= $id_milik;
				$ArrBqDetailLam[$LoopDetailLam]['detail_name']	= $valx2['detail_name'];
				$ArrBqDetailLam[$LoopDetailLam]['lapisan']		= $valx2['lapisan'];
				$ArrBqDetailLam[$LoopDetailLam]['std_glass']	= $valx2['std_glass'];
				$ArrBqDetailLam[$LoopDetailLam]['width']		= $valx2['width'];
				$ArrBqDetailLam[$LoopDetailLam]['stage']		= $valx2['stage'];
				$ArrBqDetailLam[$LoopDetailLam]['glass']		= $valx2['glass'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_1']	= $valx2['thickness_1'];
				$ArrBqDetailLam[$LoopDetailLam]['thickness_2']	= $valx2['thickness_2'];
				$ArrBqDetailLam[$LoopDetailLam]['glass_length']	= $valx2['glass_length'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_veil']	= $valx2['weight_veil'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_csm']	= $valx2['weight_csm'];
				$ArrBqDetailLam[$LoopDetailLam]['weight_wr']	= $valx2['weight_wr'];
			}

			//Insert Component Detail To Hist
			$qDetailHist	= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailHist)){
				foreach($qDetailHist AS $val2Hist => $valx2Hist){
					$ArrBqDetailHist[$val2Hist]['id_product']		= $valx2Hist['id_product'];
					$ArrBqDetailHist[$val2Hist]['id_bq']			= $valx2Hist['id_bq'];
					$ArrBqDetailHist[$val2Hist]['id_milik']			= $valx2Hist['id_milik'];
					$ArrBqDetailHist[$val2Hist]['detail_name']		= $valx2Hist['detail_name'];
					$ArrBqDetailHist[$val2Hist]['acuhan']			= $valx2Hist['acuhan'];
					$ArrBqDetailHist[$val2Hist]['id_ori']			= $valx2Hist['id_ori'];
					$ArrBqDetailHist[$val2Hist]['id_ori2']			= $valx2Hist['id_ori2'];
					$ArrBqDetailHist[$val2Hist]['id_category']		= $valx2Hist['id_category'];
					$ArrBqDetailHist[$val2Hist]['nm_category']		= $valx2Hist['nm_category'];
					$ArrBqDetailHist[$val2Hist]['id_material']		= $valx2Hist['id_material'];
					$ArrBqDetailHist[$val2Hist]['nm_material']		= $valx2Hist['nm_material'];
					$ArrBqDetailHist[$val2Hist]['value']			= $valx2Hist['value'];
					$ArrBqDetailHist[$val2Hist]['thickness']		= $valx2Hist['thickness'];
					$ArrBqDetailHist[$val2Hist]['fak_pengali']		= $valx2Hist['fak_pengali'];
					$ArrBqDetailHist[$val2Hist]['bw']				= $valx2Hist['bw'];
					$ArrBqDetailHist[$val2Hist]['jumlah']			= $valx2Hist['jumlah'];
					$ArrBqDetailHist[$val2Hist]['layer']			= $valx2Hist['layer'];
					$ArrBqDetailHist[$val2Hist]['containing']		= $valx2Hist['containing'];
					$ArrBqDetailHist[$val2Hist]['total_thickness']	= $valx2Hist['total_thickness'];
					$ArrBqDetailHist[$val2Hist]['last_cost']		= $valx2Hist['last_cost'];
					$ArrBqDetailHist[$val2Hist]['rev']				= $valx2Hist['rev'];
					$ArrBqDetailHist[$val2Hist]['price_mat']		= $valx2Hist['price_mat'];
					$ArrBqDetailHist[$val2Hist]['hist_by']			= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailHist[$val2Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Plus
			$qDetailPlus	= $this->db->query("SELECT a.*, b.panjang FROM component_detail_plus a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$product."' ")->result_array();
			foreach($qDetailPlus AS $val3 => $valx3){
				$LoopDetailPlus++;

				// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx3['id_material']."' ";
				// $restPrice = $this->db->query($sqlPrice)->result();

				$ArrBqDetailPlus[$LoopDetailPlus]['id_product']		= $product;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_bq']			= $id_bq;
				$ArrBqDetailPlus[$LoopDetailPlus]['id_milik']		= $id_milik;
				$ArrBqDetailPlus[$LoopDetailPlus]['detail_name']	= $valx3['detail_name'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori']			= $valx3['id_ori'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_ori2']		= $valx3['id_ori2'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_category']	= $valx3['id_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_category']	= $valx3['nm_category'];
				$ArrBqDetailPlus[$LoopDetailPlus]['id_material']	= $valx3['id_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['nm_material']	= $valx3['nm_material'];
				$ArrBqDetailPlus[$LoopDetailPlus]['containing']		= $valx3['containing'];
				$ArrBqDetailPlus[$LoopDetailPlus]['perse']			= $valx3['perse'];
				if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / floatval($valx3['panjang'])) * (floatval($panjang) + 400);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / floatval($valx3['panjang'])) * (floatval($panjang) + 400);
				}
				elseif($qHeader[0]->parent_product == 'frp pipe'){
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= (floatval($valx3['last_full']) / 1000) * floatval($panjang);
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= (floatval($valx3['last_cost']) / 1000) * floatval($panjang);
				}
				else{
					$ArrBqDetailPlus[$LoopDetailPlus]['last_full']	= $valx3['last_full'];
					$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']	= $valx3['last_cost'];
				}
				$ArrBqDetailPlus[$LoopDetailPlus]['rev']			= $qHeader[0]->rev;
				$ArrBqDetailPlus[$LoopDetailPlus]['price_mat']				= get_price_ref($valx3['id_material']);
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailPlusHist)){
				foreach($qDetailPlusHist AS $val3Hist => $valx3Hist){
					$ArrBqDetailPlusHist[$val3Hist]['id_product']	= $valx3Hist['id_product'];
					$ArrBqDetailPlusHist[$val3Hist]['id_bq']		= $valx3Hist['id_bq'];
					$ArrBqDetailPlusHist[$val3Hist]['id_milik']		= $valx3Hist['id_milik'];
					$ArrBqDetailPlusHist[$val3Hist]['detail_name']	= $valx3Hist['detail_name'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori']		= $valx3Hist['id_ori'];
					$ArrBqDetailPlusHist[$val3Hist]['id_ori2']		= $valx3Hist['id_ori2'];
					$ArrBqDetailPlusHist[$val3Hist]['id_category']	= $valx3Hist['id_category'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_category']	= $valx3Hist['nm_category'];
					$ArrBqDetailPlusHist[$val3Hist]['id_material']	= $valx3Hist['id_material'];
					$ArrBqDetailPlusHist[$val3Hist]['nm_material']	= $valx3Hist['nm_material'];
					$ArrBqDetailPlusHist[$val3Hist]['containing']	= $valx3Hist['containing'];
					$ArrBqDetailPlusHist[$val3Hist]['perse']		= $valx3Hist['perse'];
					$ArrBqDetailPlusHist[$val3Hist]['last_full']	= $valx3Hist['last_full'];
					$ArrBqDetailPlusHist[$val3Hist]['last_cost']	= $valx3Hist['last_cost'];
					$ArrBqDetailPlusHist[$val3Hist]['rev']			= $valx3Hist['rev'];
					$ArrBqDetailPlusHist[$val3Hist]['price_mat']	= $valx3Hist['price_mat'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailPlusHist[$val3Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}

			//Component Detail Add
			$qDetailAdd		= $this->db->query("SELECT a.*, b.panjang FROM component_detail_add a LEFT JOIN component_header b ON a.id_product = b.id_product WHERE a.id_product='".$product."' ")->result_array();
			if(!empty($qDetailAdd)){
				foreach($qDetailAdd AS $val4 => $valx4){
					$LoopDetailAdd++;
					// $sqlPrice = "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$valx4['id_material']."' ";
					// $restPrice = $this->db->query($sqlPrice)->result();
					$ArrBqDetailAdd[$LoopDetailAdd]['id_product']		= $product;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_bq']			= $id_bq;
					$ArrBqDetailAdd[$LoopDetailAdd]['id_milik']			= $id_milik;
					$ArrBqDetailAdd[$LoopDetailAdd]['detail_name']		= $valx4['detail_name'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_category']		= $valx4['id_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_category']		= $valx4['nm_category'];
					$ArrBqDetailAdd[$LoopDetailAdd]['id_material']		= $valx4['id_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['nm_material']		= $valx4['nm_material'];
					$ArrBqDetailAdd[$LoopDetailAdd]['containing']		= $valx4['containing'];
					$ArrBqDetailAdd[$LoopDetailAdd]['perse']			= $valx4['perse'];
					if($qHeader[0]->parent_product == 'pipe' OR $qHeader[0]->parent_product == 'pipe slongsong'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= (floatval($valx4['last_full']) / floatval($valx4['panjang'])) * (floatval($panjang) + 400);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= (floatval($valx4['last_cost']) / floatval($valx4['panjang'])) * (floatval($panjang) + 400);
					}
					elseif($qHeader[0]->parent_product == 'frp pipe'){
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= (floatval($valx4['last_full']) / 1000) * floatval($panjang);
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= (floatval($valx4['last_cost']) / 1000) * floatval($panjang);
					} 
					else{
						$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= $valx4['last_full'];
						$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= $valx4['last_cost'];
					}
					$ArrBqDetailAdd[$LoopDetailAdd]['rev']				= $qHeader[0]->rev;
					$ArrBqDetailAdd[$LoopDetailAdd]['price_mat']		= get_price_ref($valx4['id_material']);
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailAddHist)){
				foreach($qDetailAddHist AS $val4Hist => $valx4Hist){
					$ArrBqDetailAddHist[$val4Hist]['id_product']	= $valx4Hist['id_product'];
					$ArrBqDetailAddHist[$val4Hist]['id_bq']			= $valx4Hist['id_bq'];
					$ArrBqDetailAddHist[$val4Hist]['id_milik']		= $valx4Hist['id_milik'];
					$ArrBqDetailAddHist[$val4Hist]['detail_name']	= $valx4Hist['detail_name'];
					$ArrBqDetailAddHist[$val4Hist]['id_category']	= $valx4Hist['id_category'];
					$ArrBqDetailAddHist[$val4Hist]['nm_category']	= $valx4Hist['nm_category'];
					$ArrBqDetailAddHist[$val4Hist]['id_material']	= $valx4Hist['id_material'];
					$ArrBqDetailAddHist[$val4Hist]['nm_material']	= $valx4Hist['nm_material'];
					$ArrBqDetailAddHist[$val4Hist]['containing']	= $valx4Hist['containing'];
					$ArrBqDetailAddHist[$val4Hist]['perse']			= $valx4Hist['perse'];
					$ArrBqDetailAddHist[$val4Hist]['last_full']		= $valx4Hist['last_full'];
					$ArrBqDetailAddHist[$val4Hist]['last_cost']		= $valx4Hist['last_cost'];
					$ArrBqDetailAddHist[$val4Hist]['rev']			= $valx4Hist['rev'];
					$ArrBqDetailAddHist[$val4Hist]['price_mat']		= $valx4Hist['price_mat'];
					$ArrBqDetailAddHist[$val4Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqDetailAddHist[$val4Hist]['hist_date']		= date('Y-m-d H:i:s');
				}
			}

			//Component Footer
			$qDetailFooter	= $this->db->query("SELECT * FROM component_footer WHERE id_product='".$product."' ")->result_array();
			if (count($qDetailFooter)>0)
			{
				foreach($qDetailFooter AS $val5 => $valx5){
					$LoopFooter++;
					$ArrBqFooter[$LoopFooter]['id_product']		= $product;
					$ArrBqFooter[$LoopFooter]['id_bq']			= $id_bq;
					$ArrBqFooter[$LoopFooter]['id_milik']		= $id_milik;
					$ArrBqFooter[$LoopFooter]['detail_name']	= $valx5['detail_name'];
					$ArrBqFooter[$LoopFooter]['total']			= $valx5['total'];
					$ArrBqFooter[$LoopFooter]['min']			= $valx5['min'];
					$ArrBqFooter[$LoopFooter]['max']			= $valx5['max'];
					$ArrBqFooter[$LoopFooter]['hasil']			= $valx5['hasil'];
					$ArrBqFooter[$LoopFooter]['rev']			= $qHeader[0]->rev;
				}
			}
			//Insert Component Footer To Hist
			$qDetailFooterHist		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
			if(!empty($qDetailFooterHist)){
				foreach($qDetailFooterHist AS $val5Hist => $valx5Hist){
					$ArrBqFooterHist[$val5Hist]['id_product']	= $valx5Hist['id_product'];
					$ArrBqFooterHist[$val5Hist]['id_bq']		= $valx5Hist['id_bq'];
					$ArrBqFooterHist[$val5Hist]['id_milik']		= $valx5Hist['id_milik'];
					$ArrBqFooterHist[$val5Hist]['detail_name']	= $valx5Hist['detail_name'];
					$ArrBqFooterHist[$val5Hist]['total']		= $valx5Hist['total'];
					$ArrBqFooterHist[$val5Hist]['min']			= $valx5Hist['min'];
					$ArrBqFooterHist[$val5Hist]['max']			= $valx5Hist['max'];
					$ArrBqFooterHist[$val5Hist]['hasil']		= $valx5Hist['hasil'];
					$ArrBqFooterHist[$val5Hist]['rev']			= $valx5Hist['rev'];
					$ArrBqFooterHist[$val5Hist]['hist_by']		= $this->session->userdata['ORI_User']['username'];
					$ArrBqFooterHist[$val5Hist]['hist_date']	= date('Y-m-d H:i:s');
				}
			}
		

		// print_r($ArrBqHeader);
		// print_r($ArrBqDefault);
		// echo "</pre>";
		// exit;

		$UpdateBQ	= array(
			'estimasi'	=> 'Y',
			'est_by'	=> $this->session->userdata['ORI_User']['username'],
			'est_date'	=> date('Y-m-d H:i:s')
		);
		
		$ArrDetBq2	= array(
			'id_product'	=> $product
		);

		$this->db->trans_start();
			$this->db->where('id', $id_milik);
			$this->db->update('bq_detail_header', $ArrDetBq2);

			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_bq_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_bq_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(count($ArrBqFooterHist)>0){
			// 	$this->db->insert_batch('hist_bq_component_footer', $ArrBqFooterHist);
			// }
			// if(!empty($ArrBqDefaultHist)){
			// 	$this->db->insert_batch('hist_bq_component_default', $ArrBqDefaultHist);
			// }

			//Delete BQ Component
			$this->db->delete('bq_component_header', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('bq_component_detail', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('bq_component_lamination', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('bq_component_detail_plus', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('bq_component_detail_add', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('bq_component_footer', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('bq_component_default', array('id_bq' => $id_bq, 'id_milik' => $id_milik));

			//Insert BQ Component
			if(!empty($ArrBqHeader)){
				$this->db->insert('bq_component_header', $ArrBqHeader);
			}
			if(!empty($ArrBqDetail)){
				$this->db->insert_batch('bq_component_detail', $ArrBqDetail);
			}
			if(!empty($ArrBqDetailLam)){
				$this->db->insert_batch('bq_component_lamination', $ArrBqDetailLam);
			}
			if(!empty($ArrBqDetailPlus)){
				$this->db->insert_batch('bq_component_detail_plus', $ArrBqDetailPlus);
			}
			if(!empty($ArrBqDetailAdd)){
				$this->db->insert_batch('bq_component_detail_add', $ArrBqDetailAdd);
			}
			if(!empty($ArrBqFooter)){
				$this->db->insert_batch('bq_component_footer', $ArrBqFooter);
			}
			if(!empty($ArrBqDefault)){
				$this->db->insert('bq_component_default', $ArrBqDefault);
			}

			$this->db->where('id_bq', $id_bq);
			$this->db->update('bq_header', $UpdateBQ);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
				'status'	=> 0,
				'pembeda'	=> $pembeda
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pesan'		=>'Estimation structure bq data success. Thanks ...',
				'status'	=> 1,
				'pembeda'	=> $pembeda
			);
			history('Estimation Sebagian Structure BQ with code : '.$id_bq.' / '.$id_milik.' / '.$product);
		}

		echo json_encode($Arr_Kembali);
	}

	public function update_resin(){
		$data  		= $this->input->post();
		$resin 		= $this->uri->segment(3);
		$tanda 		= $this->uri->segment(4);
		$id_milik 	= $this->uri->segment(5);
		$beda 	= $data['pembeda'];
		$id_bq 	= $data['id_bq'];
		$category_id 	= $data['category_id'];

		$check = $this->input->post('check');
		$dtListArray = array();
		foreach($check AS $val => $valx){
			$dtListArray[$val] = $valx;
		}
		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$qListResin = "SELECT id_material, nm_material FROM raw_materials WHERE id_material='".$resin."' LIMIT 1 ";
		$dataResin	= $this->db->query($qListResin)->result();
		$resinNew	= $dataResin[0]->nm_material;

		if($tanda == 'liner'){
			$layer = "AND (detail_name = 'LINER THIKNESS / CB')";
			$table = "bq_component_detail";
			$table2 = "bq_component_detail_plus";
		}
		if($tanda == 'str'){
			$layer = "AND (detail_name = 'STRUKTUR THICKNESS' OR detail_name = 'STRUKTUR NECK 1' OR detail_name = 'STRUKTUR NECK 2')";
			$table = "bq_component_detail";
			$table2 = "bq_component_detail_plus";
		}
		if($tanda == 'eks'){
			$layer = "AND (detail_name = 'EXTERNAL LAYER THICKNESS')";
			$table = "bq_component_detail";
			$table2 = "bq_component_detail_plus";
		}
		if($tanda == 'tc'){
			$layer = "AND (detail_name = 'TOPCOAT')";
			$table = "bq_component_detail_plus";
		}
		
		$WHERE_ID_MILIK = "";
		if(!empty($this->input->post('check'))){
			$WHERE_ID_MILIK = " AND id_milik IN ".$dtImplode." ";
		}

		if($tanda == 'liner' OR $tanda == 'str' OR $tanda == 'eks'){
			$sqlUpdate 	= "SELECT * FROM ".$table." WHERE id_bq='".$id_bq."' AND id_category = '".$category_id."' ".$layer." ".$WHERE_ID_MILIK." ";
			$sqlUpdate2 	= "SELECT * FROM ".$table2." WHERE id_bq='".$id_bq."' AND id_category = '".$category_id."' ".$layer." ".$WHERE_ID_MILIK." ";
		}
		if($tanda == 'tc'){
			$sqlUpdate 	= "SELECT * FROM ".$table." WHERE id_bq='".$id_bq."' AND id_category = '".$category_id."' ".$layer." ".$WHERE_ID_MILIK." ";
		}

		$restUpdate = $this->db->query($sqlUpdate)->result_array();
		$ArrUpdate3 	= array();
		if($tanda == 'liner' OR $tanda == 'str' OR $tanda == 'eks'){
			$restUpdate3 = $this->db->query($sqlUpdate2)->result_array();
			
			foreach($restUpdate3 AS $val => $valx){
				$ArrUpdate3[$val]['id_detail'] 	= $valx['id_detail'];
				$ArrUpdate3[$val]['id_material'] = $resin;
				$ArrUpdate3[$val]['nm_material'] = $resinNew;
				$ArrUpdate3[$val]['price_mat'] 	= get_price_ref($resin);
			}
		}

		$ArrUpdate 	= array();
		foreach($restUpdate AS $val => $valx){
			$ArrUpdate[$val]['id_detail'] 	= $valx['id_detail'];
			$ArrUpdate[$val]['id_material'] = $resin;
			$ArrUpdate[$val]['nm_material'] = $resinNew;
			$ArrUpdate[$val]['price_mat'] 	= get_price_ref($resin);
		}

		
		//Update Joint
		$sqlv = "";
		if($category_id == 'TYP-0001'){
			if($tanda == 'liner'){
				// selain resin topcoat
				$sqlv_tc = "SELECT id_detail AS id_detail FROM help_update_joint_tc WHERE id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND (nm_category='RESIN INSIDE' OR nm_category='RESIN CARBOSIL')";
				$ListBQipp		= $this->db->query($sqlv_tc)->result_array();

				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['id_detail'];
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

				$sqlv = "SELECT id_detail FROM bq_component_detail WHERE detail_name = 'RESIN AND ADD' AND id_category = '".$category_id."' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND id_detail IN ".$dtImplode." ";
			}
			if($tanda == 'str'){
				// selain resin topcoat
				$sqlv_tc = "SELECT id_detail AS id_detail FROM help_update_joint_tc WHERE id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND (nm_category='RESIN OUTSIDE' OR nm_category='RESIN')";
				$ListBQipp		= $this->db->query($sqlv_tc)->result_array();

				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['id_detail'];
				}
				$dtImplode	= "('".implode("','", $dtListArray)."')";

				$sqlv = "SELECT id_detail FROM bq_component_detail WHERE detail_name = 'RESIN AND ADD' AND id_category = '".$category_id."' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." AND id_detail IN ".$dtImplode." ";
			}
			if($tanda == 'tc'){
				//resin top topcoat
				$sqlv = "SELECT id_detail AS id_detail FROM help_update_joint_tc WHERE nm_category='RESIN TOPCOAT' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." GROUP BY id_milik";
			}
		}
		else{
			if($tanda == 'str'){
				$sqlv = "SELECT id_detail FROM bq_component_detail WHERE id_category = '".$category_id."' AND id_bq='".$id_bq."' ".$WHERE_ID_MILIK." ";
			} 
		}
		// echo $sqlv; exit; 
		$ArrUpdate2 	= array();
		if($tanda == 'str' OR $tanda == 'tc' OR $tanda == 'liner'){
			if($sqlv != ""){
				$restUpdateJoint = $this->db->query($sqlv)->result_array();
				foreach($restUpdateJoint AS $val => $valx){
					$ArrUpdate2[$val]['id_detail'] 		= $valx['id_detail'];
					$ArrUpdate2[$val]['id_material'] 	= $resin;
					$ArrUpdate2[$val]['nm_material'] 	= $resinNew;
					$ArrUpdate2[$val]['price_mat'] 		= get_price_ref($resin);
				}
			}
		}

		// print_r($ArrUpdate);
		// print_r($ArrUpdate2);
		// exit; 

		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch($table, $ArrUpdate, 'id_detail');
			}
			if(!empty($ArrUpdate3)){
				$this->db->update_batch($table2, $ArrUpdate3, 'id_detail');
			}
			if(!empty($ArrUpdate2)){
				$this->db->update_batch("bq_component_detail", $ArrUpdate2, 'id_detail');
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update failed. Please try again later ...',
				'status'	=> 0,
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $beda
			);
		}
		else{
			$this->db->trans_commit();

			//TAMPILAN BARU
			$sqlResin = "(SELECT id_material, nm_material, id_category  FROM bq_component_detail WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)
			 UNION
			(SELECT id_material, nm_material, id_category  FROM bq_component_detail_plus WHERE (id_category='TYP-0001' OR id_category='TYP-0003' OR id_category='TYP-0004' OR id_category='TYP-0005' OR id_category='TYP-0006' OR id_category='TYP-0002' OR id_category='TYP-0007') AND id_bq = '".$id_bq."' GROUP BY id_material)";
			$ListBQipp		= $this->db->query($sqlResin)->result_array();
			$dtListArrayResin = array();
			$dtListArrayVeil = array();
			$dtListArrayCsm = array();
			$dtListArrayWR = array();
			$dtListArrayRooving = array();
			$dtListArrayCatalys = array();
			$dtListArrayPigment = array();
			foreach($ListBQipp AS $val => $valx){
				if($valx['id_category'] == 'TYP-0001'){
					$dtListArrayResin[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0003'){
					$dtListArrayVeil[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0004'){
					$dtListArrayCsm[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0006'){
					$dtListArrayWR[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0005'){
					$dtListArrayRooving[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0002'){
					$dtListArrayCatalys[$val] = $valx['nm_material'];
				}
				if($valx['id_category'] == 'TYP-0007'){
					$dtListArrayPigment[$val] = $valx['nm_material'];
				}
			}
			$dtImplodeResin	= "".implode("  ---  ", $dtListArrayResin)."";
			$dtImplodeVeil	= "".implode("  ---  ", $dtListArrayVeil)."";
			$dtImplodeCsm	= "".implode("  ---  ", $dtListArrayCsm)."";
			$dtImplodeWR	= "".implode("  ---  ", $dtListArrayWR)."";
			$dtImplodeRooving	= "".implode("  ---  ", $dtListArrayRooving)."";
			$dtImplodeCatalys	= "".implode("  ---  ", $dtListArrayCatalys)."";
			$dtImplodePigment	= "".implode("  ---  ", $dtListArrayPigment)."";

			$Arr_Data	= array(
				'pesan'		=>'Update success. Thanks ...',
				'status'	=> 1,
				'id_bqx'	=> $id_bq,
				'jumlah_resin'	=> COUNT($dtListArrayResin),
				'nama_resin'	=> $dtImplodeResin,
				'jumlah_veil'	=> COUNT($dtListArrayVeil),
				'nama_veil'	=> $dtImplodeVeil,
				'jumlah_csm'	=> COUNT($dtListArrayCsm),
				'nama_csm'	=> $dtImplodeCsm,
				'jumlah_wr'	=> COUNT($dtListArrayWR),
				'nama_wr'	=> $dtImplodeWR,
				'jumlah_rooving'	=> COUNT($dtListArrayRooving),
				'nama_rooving'	=> $dtImplodeRooving,
				'jumlah_catalys'	=> COUNT($dtListArrayCatalys),
				'nama_catalys'	=> $dtImplodeCatalys,
				'jumlah_pigment'	=> COUNT($dtListArrayPigment),
				'nama_pigment'	=> $dtImplodePigment,
				'pembeda'	=> $beda
			);
			history("Update all resin layer ".$tanda." / ".$id_bq." / ".$resin);
		}
		echo json_encode($Arr_Data);
	}
	
	
	
	
	//REJECT IPPNum
	public function reject_ipp(){
		if($this->input->post()){
			$no_ipp 		= $this->input->post('no_ipp');
			$status_reason 	= $this->input->post('status_reason');
			$data_session	= $this->session->userdata;
			// echo $no_ipp."<== IPP";
			$ArrCancel		= array(
				'status' 		=> 'WAITING IPP RELEASE',
				'status_reason' => $status_reason,
				'modified_by' 	=> $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
				);
			
			// print_r($ArrCancel); exit;
			$this->db->trans_start();
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $ArrCancel);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Reject IPP failed. Please try again later ...',
					'status'	=> 0
				);			
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Reject IPP success. Thanks ...',
					'status'	=> 1
				);				
				history('Reject IPP to Sales, '.$no_ipp);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$no_ipp = $this->uri->segment(3);

			$qRequest 		= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' ";
			$RestRequest	= $this->db->query($qRequest)->result();
			
			$data = array(
				'no_ipp'	=> $no_ipp,
				'RestRequest'	=> $RestRequest
			);

			$this->load->view('Machine/reject_ipp', $data);
		}
	}
	
	public function back_to_bq(){
		$id_bq 			= $this->uri->segment(3);
		$no_ipp 		= str_replace('BQ-','',$id_bq);
		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'aju_approved' 		=> 'N',
			'aju_approved_by' 	=> $data_session['ORI_User']['username'],
			'aju_approved_date' => date('Y-m-d H:i:s'),
			'approved' 			=> 'N',
			'approved_by' 		=> $data_session['ORI_User']['username'],
			'approved_date' 	=> date('Y-m-d H:i:s')
		);
		
		$Arr_Update = array(
			'status' => 'WAITING STRUCTURE BQ'
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('id_bq', $id_bq);
			$this->db->update('bq_header', $Arr_Edit);
			
			$this->db->where('no_ipp', $no_ipp);
			$this->db->update('production', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Back process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Back process success. Thanks ...',
				'status'	=> 1
			);				
			history('Proses back structure bq : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}

	function DeleteEditMultiple(){
		$data 	= $this->input->post();
		$id_bq 	= $data['id_bq'];
		$check 	= $data['check'];

		$dtListArray = array();
		$dtListArray2 = array();
		if(!empty($check)){
			foreach($check AS $val => $valx){
				$id_head = $this->db->get_where('bq_detail_header', array('id'=>$valx))->result();

				$dtListArray[$val] = $valx;
				$dtListArray2[$val] = $id_head[0]->id_bq_header;
			}
			$dtImplode	= "('".implode("','", $dtListArray)."')";
			$dtImplode2	= "('".implode("','", $dtListArray2)."')";
		}

		// print_r($check);
		// echo $dtImplode."<br>";
		// echo $dtImplode2;
		// exit;

		$this->db->trans_start();
			$this->db->query("DELETE FROM bq_detail_header WHERE id_bq = '".$id_bq."' AND id IN ".$dtImplode." ");
			$this->db->query("DELETE FROM bq_detail_detail WHERE id_bq = '".$id_bq."' AND id_bq_header IN ".$dtImplode2." ");

			$this->db->query("DELETE FROM bq_component_header WHERE id_bq = '".$id_bq."' AND id_milik IN ".$dtImplode." ");
			$this->db->query("DELETE FROM bq_component_detail WHERE id_bq = '".$id_bq."' AND id_milik IN ".$dtImplode." ");
			$this->db->query("DELETE FROM bq_component_detail_plus WHERE id_bq = '".$id_bq."' AND id_milik IN ".$dtImplode." ");
			$this->db->query("DELETE FROM bq_component_detail_add WHERE id_bq = '".$id_bq."' AND id_milik IN ".$dtImplode." ");
			$this->db->query("DELETE FROM bq_component_footer WHERE id_bq = '".$id_bq."' AND id_milik IN ".$dtImplode." ");
			$this->db->query("DELETE FROM bq_component_default WHERE id_bq = '".$id_bq."' AND id_milik IN ".$dtImplode." ");
			$this->db->query("DELETE FROM bq_component_lamination WHERE id_bq = '".$id_bq."' AND id_milik IN ".$dtImplode." ");
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Hapus sebagian BQ gagal. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Hapus sebagian BQ berhasil. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);
			history('Delete Multiple BQ with ID : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}

}
