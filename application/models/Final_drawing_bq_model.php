<?php
class Final_drawing_bq_model extends CI_Model {

	public function __construct() {
		parent::__construct();
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
			'title'			=> 'Indeks Of Final Drawing Structure BQ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Final Drawing Structure BQ');
		$this->load->view('FinalDrawing/index',$data);
	}
	
	public function modal_detail_bq(){
		$id_bq 	= $this->uri->segment(3);

		$sql 	= "SELECT * FROM so_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id ASC";
		$result	= $this->db->query($sql)->result_array();
		
		$data = array(
			'id_bq' => $id_bq,
			'result' => $result
		);
		
		$this->load->view('FinalDrawing/modal_detail_bq', $data);
	}
	
	public function modal_edit_bq(){
		$id_bq 			= $this->uri->segment(3);

		$qBQdetHeader 	= "SELECT * FROM so_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id ASC";
		$qBQdetRest		= $this->db->query($qBQdetHeader)->result_array();
		$qBQdetRestVal	= $this->db->query($qBQdetHeader)->num_rows();

		$sqlSup			= "SELECT * FROM list_standard ORDER BY urut ASC";
		$restSup		= $this->db->query($sqlSup)->result_array();

		$sqlProduct		= "SELECT * FROM product_parent ORDER BY product_parent ASC";
		$restProduct	= $this->db->query($sqlProduct)->result_array();

		$ListSeries		= $this->db->query("SELECT * FROM so_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series")->result_array();
		$dtListArray = array();
		foreach($ListSeries AS $val => $valx){
			$dtListArray[$val] = $valx['series'];
		}
		$dtImplode	= implode(",", $dtListArray);
		
		$data = array(
			'id_bq' 		=> $id_bq,
			'qBQdetRest' 	=> $qBQdetRest,
			'qBQdetRestVal' => $qBQdetRestVal,
			'restSup' 		=> $restSup,
			'restProduct' 	=> $restProduct,
			'dtImplode' 	=> $dtImplode
		);

		$this->load->view('FinalDrawing/modal_edit_bq', $data);
	}
	
	public function delete_sebagian_bq(){
		$id_bqdet 		= $this->uri->segment(3);
		$id_bqdet_et 	= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$ExpTy			= explode('-', $id_bqdet_et);

		$id_bq =  $ExpTy[0]."-". $ExpTy[1];
		// echo  $id_bq;
		// exit;

		$ToHistBqDetDetail	= $this->db->query("SELECT * FROM so_detail_detail WHERE id_bq_header='".$id_bqdet_et."' ")->result_array();
		$sqlToHistHead		= "	INSERT INTO hist_so_detail_header
									(id_bq, id_bq_header, id_delivery, sub_delivery, series, no_komponen, sts_delivery, id_category, qty, diameter_1, diameter_2, length, thickness, sudut, id_standard, type, id_product, man_power, id_mesin, total_time, man_hours, hist_by, hist_date)
								SELECT
									id_bq, id_bq_header, id_delivery, sub_delivery, series, no_komponen, sts_delivery, id_category, qty, diameter_1, diameter_2, length, thickness, sudut, id_standard, type, id_product, man_power, id_mesin, total_time, man_hours, '".$this->session->userdata['ORI_User']['username']."', '".date('Y-m-d H:i:s')."'
								FROM so_detail_header
								WHERE id = '".$id_bqdet."'
								";

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

		// exit;

		$this->db->trans_start();
		$this->db->query($sqlToHistHead);
		// if(!empty($ArrToHistDetDetail)){
		// 	$this->db->insert_batch('hist_so_detail_detail', $ArrToHistDetDetail);
		// }
		$this->db->query("DELETE FROM so_detail_header WHERE id='".$id_bqdet."' AND id_bq_header='".$id_bqdet_et."' ");
		$this->db->query("DELETE FROM so_detail_detail WHERE id_bq_header='".$id_bqdet_et."' ");
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Hapus sebagian BQ Final Drawing gagal. Please try again later ...',
				'status'	=> 0,
				'id_bq'		=> $id_bq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Hapus sebagian BQ Final Drawing berhasil. Thanks ...',
				'status'	=> 1,
				'id_bq'		=> $id_bq
			);
			history('Delete Sebagian BQ Final Drawing with ID : '.$id_bqdet_et);
		}

		echo json_encode($Arr_Data);
	}
	
	public function update_bq(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_bq			= $data['id_bq'];
		$dateTime		= date('Y-m-d H:i:s');
		$username		= $this->session->userdata['ORI_User']['username'];

		if(!empty($data['DetailBq'])){
			$DataBQ = $data['DetailBq'];
		}

		if(!empty($data['ListDetail'])){
			$data2 = $data['ListDetail'];
		}

		$ToHistBqHeader		= $this->db->get_where('so_header',array('id_bq'=>$id_bq))->result_array();
		$ToHistBqDetHeader	= $this->db->get_where('so_detail_header',array('id_bq'=>$id_bq))->result_array();
		$ToHistBqDetDetail	= $this->db->get_where('so_detail_detail',array('id_bq'=>$id_bq))->result_array();

		// echo "SELECT * FROM bq_detail_detail WHERE id_bq='".$id_bq."'";
		// print_r($DataBQ);
		// print_r($data2);
		// exit();

		if(!empty($data['ListDetail'])){
			$DataHeader = $this->db->query("SELECT MAX(id_bq_header) AS maximalA FROM so_detail_header WHERE id_bq = '".$id_bq."' ")->result();
			$nst		= explode('-', $DataHeader[0]->maximalA);
			$numX 		= ltrim($nst[2], '0');
			// echo $numX; exit;
			$ArrInsertNew	= array();
			$Loop = 0;

			$ArrInsertDetDetail = array();
			foreach($data2 AS $val => $valx){
				$numX++;
				$dataKR4 	= sprintf('%03s',$numX);

				$wherePN = floatval(substr($valx['series'], 3,2));
				$whereLN = floatval(substr($valx['series'], 6,3));
				
				$wherePlus = '';
				if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
					$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
				}
				$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
				$restSer = $this->db->query($qSeries)->result();
				// echo $qSeries."<br>";

				$ArrInsertNew[$val]['id_bq'] 			= $id_bq;
				$ArrInsertNew[$val]['id_bq_header'] 	= $id_bq."-".$dataKR4;
				$ArrInsertNew[$val]['id_delivery'] 		= $valx['id_delivery'];
				$ArrInsertNew[$val]['sub_delivery'] 	= $valx['sub_delivery'];
				$ArrInsertNew[$val]['no_komponen'] 		= $valx['sub_delivery']."/".$dataKR4;
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

				$ArrInsertNew[$val]['man_power'] 	= (!empty($restSer[0]->man_power))?$restSer[0]->man_power:'';
				$ArrInsertNew[$val]['id_mesin'] 	= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
				$ArrInsertNew[$val]['total_time'] 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:'';
				$ArrInsertNew[$val]['man_hours'] 	= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:'';

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
					$ArrInsertDetDetail[$Loop]['sub_delivery'] 	= $valx['sub_delivery'];
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
		$Loop 			= 0;
		$ArrDetDetail 	= array();
		$whereINdelete	= [];
		if(!empty($data['DetailBq'])){
			foreach($DataBQ AS $val => $valx){
				$whereINdelete[] = $valx['id_bq_header'];
				$wherePN = floatval(substr($valx['series'], 3,2));
				$whereLN = floatval(substr($valx['series'], 6,3));
				
				$wherePlus = ''; 
				if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'reducer tee slongsong'){
					$wherePlus = " AND diameter2 = '".$valx['diameter_2']."' ";
				}
				$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$valx['id_category']."' AND diameter='".$valx['diameter_1']."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
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
				$ArrUpdateBq[$val]['pe_machine'] 					= pe_machine($total_time, $id_mesin);
				$ArrUpdateBq[$val]['pe_mould_mandrill'] 			= pe_mould_mandrill($valx['id_category'], $valx['diameter_1'], $valx['diameter_2']);
				$ArrUpdateBq[$val]['pe_consumable'] 				= pe_consumable($valx['id_category']);

				$DataHeader = $this->db->query("SELECT id_bq_header, id_delivery, sub_delivery, sts_delivery, series FROM so_detail_header WHERE id = '".$valx['id']."' ")->result();

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
			$ArrToHistHeader[$val]['hist_by'] 		= $username;
			$ArrToHistHeader[$val]['hist_date'] 	= $dateTime;
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
			$ArrToHistDetHeader[$val]['hist_by'] 		= $username;
			$ArrToHistDetHeader[$val]['hist_date'] 		= $dateTime;

			$ArrToHistDetHeader[$val]['man_power'] = $valx['man_power'];
			$ArrToHistDetHeader[$val]['id_mesin'] = $valx['id_mesin'];
			$ArrToHistDetHeader[$val]['total_time'] = $valx['total_time'];
			$ArrToHistDetHeader[$val]['man_hours'] = $valx['man_hours']; 

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
			$ArrToHistDetDetail[$val]['hist_by'] 		= $username;
			$ArrToHistDetDetail[$val]['hist_date'] 		= $dateTime;
		}

		$UpdateModif	= array(
			'rev'	=> $ToHistBqHeader[0]['rev'] + 1 ,
			'modified_by'	=> $username,
			'modified_date'	=> $dateTime
		);

		// if(!empty($ArrToHistDetDetail)){
				// echo "ADa";
			// }
		// exit;
		$this->db->trans_start();
			//INSERT HISTORY
			if(!empty($ArrToHistHeader)){
				$this->db->insert_batch('hist_so_header', $ArrToHistHeader);
			}
			if(!empty($ArrToHistDetHeader)){
				$this->db->insert_batch('hist_so_detail_header', $ArrToHistDetHeader);
			}
			// if(!empty($ArrToHistDetDetail)){
			// 	$this->db->insert_batch('hist_so_detail_detail', $ArrToHistDetDetail);
			// }
			//UPDATE HEADER
			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $UpdateModif);
			//UPDATE DETAIL
			if(!empty($ArrUpdateBq)){
				$this->db->update_batch('so_detail_header', $ArrUpdateBq, 'id');
			}
			//UPDATE DETAIL DETAIL
			if(!empty($whereINdelete)){
				$this->db->where_in('id_bq_header',$whereINdelete);
				$this->db->delete('so_detail_detail');
			}
			if(!empty($ArrDetDetail)){
				$this->db->insert_batch('so_detail_detail', $ArrDetDetail);
			}
			//INSERT NEW
			if(!empty($data['ListDetail'])){
				$this->db->insert_batch('so_detail_header', $ArrInsertNew);
				$this->db->insert_batch('so_detail_detail', $ArrInsertDetDetail);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit structure bq final drawing data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Edit structure bq final drawing data success. Thanks ...',
				'status'	=> 1
			);
			history('Edit Structure BQ Final Drawing with code : '.$id_bq);
		}

		echo json_encode($Arr_Kembali);
	}
	
	public function ajukan_bq(){
		$id_bq 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$Arr_Edit	= array(
			'aju_approved' 		=> 'Y',
			'aju_approved_by' 	=> $data_session['ORI_User']['username'],
			'aju_approved_date' => date('Y-m-d H:i:s'),
			'approved' 			=> 'Y',
			'approved_by' 		=> $data_session['ORI_User']['username'],
			'approved_date' 	=> date('Y-m-d H:i:s')
		);
		// print_r($Arr_Edit);
		// exit;
		$this->db->trans_start();
			$this->db->where('id_bq', $id_bq);
			$this->db->update('so_header', $Arr_Edit);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Next process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Next process success. Thanks ...',
				'status'	=> 1
			);				
			history('Proses next estimasi project (final drawing) with BQ : '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}
	
	//SERVER SIDE
	public function get_data_json_fd_est(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_fd_est(
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
			$nestedData[]	= "<div align='left'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['order_type']))."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM so_detail_header WHERE id_bq = '".$row['id_bq']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			$nestedData[]	= "<div align='left'>".$dtImplode."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green'>".strtoupper(strtolower($row['rev']))."</span></div>";

				$warna = Color_status($row['sts_ipp']);
				
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['sts_ipp']."</span></div>";
					$priX	= "";
					$updX	= "";
					$delX	= "";
					$app	= "";
					
					if($row['aju_approved'] == 'N'){
						if($Arr_Akses['approve']=='1'){
							$app	= "&nbsp;<button type='button' class='btn btn-sm btn-success ajukan' title='Lanjutkan ke Estimasi' data-id_bq='".$row['id_bq']."'><i class='fa fa-check'></i></button>";
						}
						if($Arr_Akses['update']=='1'){
							if($row['sts_ipp'] == 'WAITING FINAL DRAWING' OR $row['sts_ipp'] == 'PARTIAL PROCESS'){
								$updX	= "&nbsp;<button class='btn btn-sm btn-primary edit' title='Edit BQ' data-id_bq='".$row['id_bq']."' data-ciri='revisi_quo'><i class='fa fa-edit'></i></button>";
							}
						}
					}
			$nestedData[]	= "<div align='left'>
									<button class='btn btn-sm btn-warning detail' id='detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>
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

	public function query_data_fd_est($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.project,
				b.status AS sts_ipp
			FROM
				so_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 
				b.status <> 'FINISH'
				AND b.status <> 'WAITING PRODUCTION'
				AND b.status <> 'PROCESS PRODUCTION'
				AND b.sts_hide = 'N'
				AND
				(
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

		$sql .= " ORDER BY b.status ASC, a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

}