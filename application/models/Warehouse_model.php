<?php
class Warehouse_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->gudang_produksi = getGudangProduksi();
	}

	public function index_material_stock(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)).'/'.strtolower($this->uri->segment(3)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' AND category='".strtolower($this->uri->segment(3))."' ORDER BY urut ASC ")->result_array();
		if($this->uri->segment(3) == 'origa'){
			$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' AND id='23' ")->result_array();
			$judul = "Warehouse Material >> Gudang Origa >> Stock";
		}
		elseif($this->uri->segment(3) == 'pusat'){
			$judul = "Warehouse Material >> Gudang Pusat >> Stock";
		}
		elseif($this->uri->segment(3) == 'subgudang'){
			$judul = "Warehouse Material >> Sub Gudang >> Stock";
		}
		elseif($this->uri->segment(3) == 'virtual'){
			$judul = "Gudang Finish Good >> Stock";
			$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE id='15' ")->result_array();
		}
		else{
			$judul = "Warehouse Material >> Gudang Produksi >> Stock";
		}
		$data = array(
			'title'			=> $judul,
			'action'		=> 'index',
			'category'		=> $this->uri->segment(3),
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View Material Stock');
		$this->load->view('Warehouse/material_stock',$data);
	}

	public function index_incoming_material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$pusat		= $this->db->query("SELECT * FROM warehouse WHERE category='pusat' ORDER BY urut ASC")->result_array();
		$no_po		= $this->db->query("
										(SELECT no_po, status1, 'PO' as ket_,nm_supplier FROM tran_material_po_header WHERE (status='WAITING IN' OR status='IN PARSIAL') AND status_id = '1' ORDER BY no_po ASC)
										UNION
										(SELECT no_non_po AS no_po, '' AS status1, 'NON-PO' as ket_,' ' nm_supplier FROM tran_material_non_po_header WHERE app_status = 'Y' AND (total_material_in < total_material_rev OR total_material_in IS NULL) ORDER BY no_non_po ASC)
										")->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'incoming material'))->result_array();
		$data_gudang= $this->db->group_by('id_gudang_ke')->get_where('warehouse_adjustment',array('category'=>'incoming material'))->result_array();

		$data = array(
			'title'			=> 'Warehouse Material >> Gudang Pusat >> Incoming Material',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_po'		=> $list_po,
			'data_gudang'	=> $data_gudang,
			'pusat'			=> $pusat,
			'no_po'			=> $no_po
		);
		history('View Incoming Material');
		$this->load->view('Warehouse/incoming_material',$data);
	}

	public function index_incoming_check(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE sts_ajust='N' ORDER BY urut ASC")->result_array();
		$list_po			= $this->db->query("SELECT * FROM tran_material_po_header WHERE deleted='N' ORDER BY no_po ASC")->result_array();
		$no_ipp				= $this->db->query("SELECT no_ipp FROM warehouse_planning_header WHERE sts_close='N' ")->result_array();

		$data = array(
			'title'			=> 'Warehouse Material >> Gudang Pusat >> Incoming Check',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang,
			'list_po'		=> $list_po,
			'no_ipp'		=> $no_ipp
		);
		history('View Incoming Check Material');
		$this->load->view('Warehouse/incoming_check',$data);
	}

	public function modal_detail_adjustment(){
		$kode_trans = $this->uri->segment(3);
		$tanda     	= $this->uri->segment(4);

		$result			= $this->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();
		$result_header	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();

		$data = array(
			'result' 	=> $result,
			'tanda' 	=> $tanda,
			'checked' 	=> $result_header[0]->checked,
			'dokumen_file' 	=> $result_header[0]->doc,
			'kode_trans'=> $result_header[0]->kode_trans,
			'no_po' 	=> $result_header[0]->no_ipp,
			'no_ipp' 	=> $result_header[0]->no_ipp,
			'qty_spk' 	=> $result_header[0]->qty_spk,
			'no_ros' 	=> $result_header[0]->no_ros,
			'file_eng_change' 	=> $result_header[0]->file_eng_change,
			'tanggal' 	=> (!empty($result_header[0]->tanggal))?date('d-M-Y',strtotime($result_header[0]->tanggal)):'',
			'id_milik' 	=> get_name('production_detail','id_milik','no_spk',$result_header[0]->no_spk),
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 		=> date('d F Y', strtotime($result_header[0]->created_date))

		);

		$this->load->view('Warehouse/modal_detail_adjustment', $data);
	}

	public function modal_incoming_check(){
		$kode_trans     = $this->uri->segment(3);
		
		
		

//		$result_header		= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
		$sql_header	= "SELECT a.*,b.id as id_ros, b.no_ros FROM warehouse_adjustment a left join report_of_shipment b on a.no_ros=b.id WHERE a.kode_trans='".$kode_trans."' ";
		$result_header		= $this->db->query($sql_header)->result();
		
		// print_r($result_header);
		// exit;
		$pembeda = substr($result_header[0]->no_ipp,0,1);

		if($pembeda == 'P'){
			$sql 	= "	SELECT
							a.*,
							b.qty_purchase,
							b.qty_in,
							b.satuan,
							b.id AS id2
						FROM
							warehouse_adjustment_detail a
							LEFT JOIN tran_material_po_detail b ON a.no_ipp=b.no_po AND a.id_po_detail = b.id
						WHERE
							a.id_material = b.id_material
							AND a.kode_trans='".$kode_trans."' ";
		}
		if($pembeda == 'N'){
			$sql 	= "	SELECT
							a.*,
							b.qty_purchase,
							b.qty_in,
							b.id AS id2
						FROM
							warehouse_adjustment_detail a
							LEFT JOIN tran_material_non_po_detail b ON a.no_ipp=b.no_non_po AND a.id_po_detail = b.id
						WHERE
							a.id_material = b.id_material
							AND a.kode_trans='".$kode_trans."' ";
		}
		$result			= $this->db->query($sql)->result_array();



		$data = array(
			'result' 	=> $result,
			'no_po' 	=> $result_header[0]->no_ipp,
			'dokumen_file' 	=> $result_header[0]->doc,
			'kode_trans' 	=> $result_header[0]->kode_trans,
			'id_header' 	=> $result_header[0]->id,
			'gudang_tujuan' 	=> $result_header[0]->kd_gudang_ke,
			'id_tujuan' 	=> $result_header[0]->id_gudang_ke,
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 	=> date('d F Y', strtotime($result_header[0]->created_date)),
			'id_ros'	=> $result_header[0]->id_ros,
			'no_ros'	=> $result_header[0]->no_ros,
			'total_freight'	=> $result_header[0]->total_freight,
		);

		$this->load->view('Warehouse/modal_incoming_check', $data);
	}

	public function modal_incoming_material(){
		$data = $this->input->post();
		$no_po 	= $data['no_ipp'];
		$gudang = $data['gudang_before'];
		$pembeda = $data['pembeda'];
		$asal_incoming = $data['asal_incoming'];
		$no_ros = $data['no_ros'];

		if($pembeda == 'P'){
			$sql 	= "SELECT a.* FROM tran_material_po_detail a WHERE a.no_po='".$no_po."' AND qty_in < qty_purchase ";
		}
		if($pembeda == 'N'){
			$sql 	= "SELECT a.*, a.no_non_po AS no_po FROM tran_material_non_po_detail a WHERE a.no_non_po='".$no_po."' AND qty_in < qty_purchase ";
		}
		$result	= $this->db->query($sql)->result_array();

		$data = array(
			'no_po' => $no_po,
			'pembeda' => $pembeda,
			'asal_incoming' => $asal_incoming,
			'gudang' => $gudang,
			'no_ros' => $no_ros,
			'result' => $result
		);

		$this->load->view('Warehouse/modal_incoming_material', $data);
	}

	public function process_in_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$no_po			= $data['no_po'];
		$gudang			= $data['gudang'];
		$pembeda		= $data['pembeda'];
		$asal_incoming	= $data['asal_incoming'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		// $note		= strtolower($data['note']);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];
		$no_ros			= $data['no_ros'];
		$Ym 			= date('ym');
		// echo $no_po;
		// print_r($addInMat);
		// exit;
		$table = 'tran_material_po_detail';
		if($pembeda == 'N'){
			$table = 'tran_material_non_po_detail';
		}

		if($adjustment == 'IN'){
			$histHlp = "Material Adjustment In Purchase To ".$nm_gudang_ke." / ".$no_po;
			
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
		
			$ArrUpdate		 = array();
			$ArrInList		 = array();
			$ArrDeatil		 = array();
			$ArrDeatilAdj	 = array();
			$ArrHist		 = array();
			$SumMat = 0;
			$SumRisk = 0;
			foreach($addInMat AS $val => $valx){
				$qtyIN 		= str_replace(',','',$valx['qty_in']);
				$qtyRISK 	= 0;
				
				$SumMat 	+= $qtyIN;
				$SumRisk 	+= $qtyRISK;

				$sqlWhDetail	= "	SELECT
									a.*,
									b.idmaterial,
									b.nm_material,
									b.id_category,
									b.nm_category
								FROM
									".$table." a
									LEFT JOIN raw_materials b
										ON a.id_material=b.id_material
								WHERE
									a.id = '".$valx['id']."'
								";
				$restWhDetail	= $this->db->query($sqlWhDetail)->result();


				//update detail purchase
				$ArrUpdate[$val]['id'] 			= $valx['id'];
				$ArrUpdate[$val]['qty_in'] 		= $restWhDetail[0]->qty_in + $qtyIN;
				
				//detail adjustmeny
				$ArrDeatilAdj[$val]['no_ipp'] 			= $no_po;
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
				$ArrDeatilAdj[$val]['id_material_req'] 	= $restWhDetail[0]->id_material;
				$ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
				$ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
				$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
				$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
				$ArrDeatilAdj[$val]['qty_order'] 		= str_replace(',','',$valx['qty_order']);
				$ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
				$ArrDeatilAdj[$val]['qty_rusak'] 		= $qtyRISK;
				$ArrDeatilAdj[$val]['expired_date'] 	= NULL;
				$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
				$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrDeatilAdj[$val]['update_date'] 		= $dateTime;
				$ArrDeatilAdj[$val]['harga']		 	= $valx['harga'];
			}

			//Upload File
			$nm_detail = 'file_dokumen';
			$file_name2			= '';
			if (!empty($_FILES[$nm_detail]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = "./assets/file/produksi/";
				$name_file      = 'incoming_'.$kode_trans.'_'. date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
				$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name2    	= $target_dir.$name_file . "." . $imageFileType;

				if (!empty($_FILES[$nm_detail]["tmp_name"])) {
					// if($imageFileType <> 'pdf'){
					// 	$Arr_Data	= array(
					// 		'pesan'		=>'Hanya file pdf yang diperbolehkan !!!',
					// 		'status'	=> 0
					// 	);
					// 	echo json_encode($Arr_Data);
					// 	return false;
					// }
					// if($imageFileType == 'pdf'){
					$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
					// if ($terupload) {
					//     echo "Upload berhasil!<br/>";
					// } else {
					//     echo "Upload Gagal!";
					// }
					// }
				}
			}

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $no_po,
				'note' 				=> $asal_incoming,
				'category' 			=> 'incoming material',
				'jumlah_mat' 		=> $SumMat + $SumRisk,
				'kd_gudang_dari' 	=> 'PURCHASE',
				'id_gudang_ke' 		=> $gudang,
				'kd_gudang_ke' 		=> $nm_gudang_ke,
				'no_ros' 			=> $no_ros,
				'doc' 				=> $file_name2,
				// 'note' => $note,
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime
			);

			$ArrHeader2 = array(
				'status' => 'COMPLETE',
			);

			$ArrHeader2x = array(
				'status' => 'COMPLETE',
				'total_material_in' => $SumMat + $SumRisk
			);
			
			$ArrHeader3 = array(
				'status' => 'IN PARSIAL',
			);

			// print_r($ArrUpdate);
			// print_r($ArrInsertH);
			// print_r($ArrDeatilAdj);
			// exit;
			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);

				if($pembeda == 'P'){
					$this->db->update_batch('tran_material_po_detail', $ArrUpdate, 'id');
					$qCheck = "SELECT * FROM tran_material_po_detail WHERE no_po='".$no_po."' AND qty_in < qty_purchase ";
					$NumChk = $this->db->query($qCheck)->num_rows();
					if($NumChk < 1){
						$this->db->where('no_po', $no_po);
						$this->db->update('tran_material_po_header', $ArrHeader2);
					}
					if($NumChk > 0){
						$this->db->where('no_po', $no_po);
						$this->db->update('tran_material_po_header', $ArrHeader3);
					}
				}
				if($pembeda == 'N'){
					$this->db->update_batch('tran_material_non_po_detail', $ArrUpdate, 'id');
					$this->db->where('no_non_po', $no_po);
					$this->db->update('tran_material_non_po_header', $ArrHeader2x);
				}
			$this->db->trans_complete();
		}


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}

	public function process_check_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		
			
		$detail			= $data['detail'];
		$id_header		= $data['id_header'];
		$gudang			= $data['gudang_tujuan'];
		$id_tujuan		= $data['id_tujuan'];
		$kode_trans		= $data['kode_trans'];
		$no_po			= $data['no_pox'];
		$no_ros			= $data['no_rosx'];
		$total_freight	= $data['total_freight'];

		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$tanggal        = date('Y-m-d');
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);

		//echo $gudang;
		// print_r($data);
		// exit;

		$ArrDeatil		 = array();
		$ArrDeatilAdj		 = array();
		$ArrUpdateStock		 = array();
		$ArrUpdateStockExp		 = array();
		$ArrStock		 = array();
		$ArrHist		 = array();
		$ArrStockNew		 = array();
		$ArrHistNew		 = array();
		$ArrUpdatePO 	= array();
		$ArrUpdExp		 = array();
		$ArrInsExp		 = array();
		$ArrUpdExpHist		 = array();
		$ArrInsExpHist		 = array();
		$SUM_MAT = 0;

		$total_harga_product=0;
		$total_harga_product_usd=0;
		$kurs_ros=1;
        $data_ros = $this->db->query("SELECT * FROM report_of_shipment WHERE id='$no_ros' ")->row();
		if(!empty($data_ros->freight_curs)) $kurs_ros = $data_ros->freight_curs;

		foreach($detail AS $val2 => $valx2){
			$qtyIN 		= 0;
			$qtyRISK 	= 0;
			
			foreach($valx2['detail'] AS $val => $valx){
				// $KONVERSI 	= (!empty($valx['konversi']))?str_replace(',','',$valx['konversi']):0;
				$KONVERSI 	= 1;
				$QTY_OKE 	= str_replace(',','',$valx['qty_oke']) * $KONVERSI;
				$QTY_RISK 	= str_replace(',','',$valx['qty_rusak']) * $KONVERSI;

				$EXPIRED = (!empty($valx['expired']))?$valx['expired']:NULL;

				if($QTY_OKE > 0 OR $QTY_RISK > 0){

					$qtyIN 		+= $QTY_OKE;
					$qtyRISK 	+= $QTY_RISK;
					
					//UPDATE ADJUSTMENT DETAIL
					$getDetAjust = $this->db->get_where('warehouse_adjustment_detail', array('id'=>$valx2['id']))->result();
					$HARGA_UNIT = $getDetAjust[0]->harga;
					$BM = $getDetAjust[0]->bm;

					$ArrDeatil[$val2]['id'] 				= $valx2['id'];
					$ArrDeatil[$val2]['check_qty_oke'] 		= $qtyIN ;
					$ArrDeatil[$val2]['check_qty_rusak'] 	= $qtyRISK;
					$ArrDeatil[$val2]['check_expired_date'] = $EXPIRED;
					$ArrDeatil[$val2]['check_keterangan'] 	= $valx['keterangan'];
					$ArrDeatil[$val2]['id_material']	 	= $getDetAjust[0]->id_material;
					$ArrDeatil[$val2]['harga']		 		= $HARGA_UNIT;

					//INSERT ADJUSTMENT CHECK
					$ArrDeatilAdj[$val2.$val]['no_ipp'] 		= $getDetAjust[0]->no_ipp;
					$ArrDeatilAdj[$val2.$val]['id_detail'] 		= $valx2['id'];
					$ArrDeatilAdj[$val2.$val]['kode_trans'] 	= $kode_trans;
					$ArrDeatilAdj[$val2.$val]['id_material'] 	= $getDetAjust[0]->id_material;
					$ArrDeatilAdj[$val2.$val]['nm_material'] 	= $getDetAjust[0]->nm_material;
					$ArrDeatilAdj[$val2.$val]['id_category'] 	= $getDetAjust[0]->id_category;
					$ArrDeatilAdj[$val2.$val]['nm_category'] 	= $getDetAjust[0]->nm_category;
					$ArrDeatilAdj[$val2.$val]['qty_order'] 		= $getDetAjust[0]->qty_order;
					$ArrDeatilAdj[$val2.$val]['qty_oke'] 		= $QTY_OKE;
					$ArrDeatilAdj[$val2.$val]['qty_rusak'] 		= $QTY_RISK;
					$ArrDeatilAdj[$val2.$val]['expired_date'] 	= $EXPIRED;
					$ArrDeatilAdj[$val2.$val]['konversi'] 		= $KONVERSI;
					$ArrDeatilAdj[$val2.$val]['keterangan'] 	= $valx['keterangan'];
					$ArrDeatilAdj[$val2.$val]['update_by'] 		= $UserName;
					$ArrDeatilAdj[$val2.$val]['update_date'] 	= $DateTime;
					$ArrDeatilAdj[$val2.$val]['unit_price'] 	= $HARGA_UNIT;
					$ArrDeatilAdj[$val2.$val]['unit_price_idr'] = $HARGA_UNIT * $kurs_ros;
					$ArrDeatilAdj[$val2.$val]['qr'] = 1;

					//Upload File
					$nm_detail = 'file_'.$val2.'_'.$val;
					$file_name2			= '';
					if (!empty($_FILES[$nm_detail]["name"])) {
						$target_dir     = "assets/file/produksi/";
						$target_dir_u   = "./assets/file/produksi/";
						$name_file      = 'incoming_'.$nm_detail.'_'. date('Ymdhis');
						$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
						$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
						$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
						$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
						$file_name2    	= $target_dir.$name_file . "." . $imageFileType;

						if (!empty($_FILES[$nm_detail]["tmp_name"])) {
							// if($imageFileType <> 'pdf'){
							// 	$Arr_Data	= array(
							// 		'pesan'		=>'Hanya file pdf yang diperbolehkan !!!',
							// 		'status'	=> 0
							// 	);
							// 	echo json_encode($Arr_Data);
							// 	return false;
							// }
							// if($imageFileType == 'pdf'){
							$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
							// if ($terupload) {
							//     echo "Upload berhasil!<br/>";
							// } else {
							//     echo "Upload Gagal!";
							// }
							// }
						}
					}
					$ArrDeatilAdj[$val2.$val]['dokumen'] = $file_name2;
					
					//MATERIAL YANG AKAN DI UPDATE
					$ArrUpdateStock[$val2]['id'] 		= $getDetAjust[0]->id_material;
					$ArrUpdateStock[$val2]['qty'] 	= $qtyIN;
					$ArrUpdateStock[$val2]['qty_good'] 	= $qtyIN;
					$ArrUpdateStock[$val2]['qty_risk'] 	= $qtyRISK;
					$ArrUpdateStock[$val2]['unit_price'] = $HARGA_UNIT;
					$ArrUpdateStock[$val2]['unit_price_idr'] = $HARGA_UNIT * $kurs_ros;
					$ArrUpdateStock[$val2]['bm'] = $BM;

					//MATERIAL YANG AKAN DI UPDATE EXPIRED
					if(!empty($EXPIRED)){
						$ArrUpdateStockExp[$val2]['id'] 		= $getDetAjust[0]->id_material;
						$ArrUpdateStockExp[$val2]['qty'] 	= $qtyIN;
						$ArrUpdateStockExp[$val2]['qty_good'] 	= $qtyIN;
						$ArrUpdateStockExp[$val2]['qty_risk'] 	= $qtyRISK;
						$ArrUpdateStockExp[$val2]['expired'] 	= $EXPIRED;
					}

					//UPDATE PO
					$rest_po	= $this->db->select('qty_in,price_ref_sup,net_price')->get_where('tran_material_po_detail',array('id'=>$getDetAjust[0]->id_po_detail))->result();

					$ArrUpdatePO[$val2]['id'] 			= $getDetAjust[0]->id_po_detail;
					$ArrUpdatePO[$val2]['qty_in'] 		= $rest_po[0]->qty_in + $qtyIN + $qtyRISK;
					$total_harga_product=($total_harga_product+($QTY_OKE*$getDetAjust[0]->harga*$kurs_ros));
					$total_harga_product_usd=($total_harga_product_usd+($QTY_OKE*$rest_po[0]->net_price));

				}
			}
			$SUM_MAT 	+= $qtyIN + $qtyRISK;
			
			
		}

		$getDetAjust = $this->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();
		$ArrUpdate = array(
			'checked' => 'Y',
			'jumlah_mat_check' => $getDetAjust[0]->jumlah_mat_check + $SUM_MAT,
			'kurs' => $kurs_ros,
			'checked_by' => $UserName,
			'checked_date' => $DateTime
		);

		//PROCESS UPDATE STOCK
		//grouping sum
		$temp = [];
		$temp2= [];
		$grouping_temp = [];
		$key = 0;
		foreach($ArrUpdateStock as $value) { $key++;
			//grouping good
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']]['good'] = 0;
			}
			$temp[$value['id']]['good'] += $value['qty_good'];

			//grouping risk
			if(!array_key_exists($value['id'], $temp2)) {
				$temp2[$value['id']]['risk'] = 0;
			}
			$temp2[$value['id']]['risk'] += $value['qty_risk'];


			$grouping_temp[$value['id']]['id'] 			= $value['id'];
			$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
			$grouping_temp[$value['id']]['qty_risk'] 	= $temp2[$value['id']]['risk'];
			$grouping_temp[$value['id']]['kurs'] 		= $kurs_ros;
			$grouping_temp[$value['id']]['unit_price'] 	= $value['unit_price'];
			$grouping_temp[$value['id']]['bm'] 			= $value['bm'];
		}
		
		$ArrStock = array();
		$ArrHist = array();
		$ArrStockNew = array();
		$ArrHistNew = array();
		$ArrJurnalNew = array();

		foreach ($grouping_temp as $key => $value) {
			$rest_pusat 	= $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_tujuan, 'id_material'=>$key))->result();
			$kode_gudang 	= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
			
			$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_tujuan))->row();
			$coa_gudang = $coa_1->coa_1;

			$qtyIN 		= $value['qty_good'];
			$qtyRISK 	= $value['qty_risk'];

			if(!empty($rest_pusat)){
				//update stock
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $qtyIN;
				$ArrStock[$key]['qty_rusak'] 	= $rest_pusat[0]->qty_rusak + $qtyRISK;
				$ArrStock[$key]['update_by'] 	= $UserName;
				$ArrStock[$key]['update_date'] 	= $DateTime;
				$ArrStock[$key]['harga']		= $value['unit_price'];//update agus
				
				//insert history
				$ArrHist[$key]['id_material'] 		= $rest_pusat[0]->id_material;
				$ArrHist[$key]['idmaterial'] 		= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 		= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 		= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 		= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_tujuan;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= "PURCHASE";
				$ArrHist[$key]['id_gudang_ke'] 		= $id_tujuan;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $qtyIN;
				$ArrHist[$key]['qty_booking_awal'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak + $qtyRISK;
				$ArrHist[$key]['no_ipp'] 			= $no_po."/".$kode_trans;
				$ArrHist[$key]['jumlah_mat'] 		= $qtyIN + $qtyRISK;
				$ArrHist[$key]['ket'] 				= 'incoming material';
				$ArrHist[$key]['update_by'] 		= $UserName;
				$ArrHist[$key]['update_date'] 		= $DateTime;
				//update agus
				$ArrHist[$key]['harga'] 			= $value['unit_price']; 
				//ambil saldo akhir 
				$saldoakhir=0;
				$saldo_akhir_gudang = $this->db->order_by('tgl_trans', 'desc')->get_where('warehouse_history',array('id_gudang'=>$id_tujuan, 'id_material'=>$key),1)->row();
				if(!empty($saldo_akhir_gudang)) $saldoakhir=$saldo_akhir_gudang->saldo_akhir;
				$ArrHist[$key]['saldo_awal']		= $saldoakhir;
				$ArrHist[$key]['saldo_akhir']		= ($saldoakhir+( $value['unit_price']*$qtyIN));
				
							
				
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_tujuan, 'id_material'=>$key),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;

				if(!empty($stok_jurnal_akhir)) $PRICE2=$stok_jurnal_akhir->harga;
				
				
				
								
				
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book',array('id_material'=>$key))->result();
					//$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
				
				
				
				
				
				$hargaBeli      = ($value['kurs'] * $value['unit_price']);
				$PRICE      = ($value['kurs'] * $value['unit_price']);
				$stok_akhir = ($PRICE*$qtyIN)+($PRICE2*$stok_jurnal_akhir);
				
				if($stok_akhir==0){
					$PRICENEW = 0;
				} else{
				   $PRICENEW = $stok_akhir/($qtyIN+$stok_jurnal_akhir);
		        }
				
				
				
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				$ArrJurnalNew[$key]['id_material'] 		= $rest_pusat[0]->id_material;
				$ArrJurnalNew[$key]['idmaterial'] 		= $rest_pusat[0]->idmaterial;
				$ArrJurnalNew[$key]['nm_material'] 		= $rest_pusat[0]->nm_material;
				$ArrJurnalNew[$key]['id_category'] 		= $rest_pusat[0]->id_category;
				$ArrJurnalNew[$key]['nm_category'] 		= $rest_pusat[0]->nm_category;
				$ArrJurnalNew[$key]['id_gudang'] 		= $id_tujuan;
				$ArrJurnalNew[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrJurnalNew[$key]['kd_gudang_dari'] 	= "PURCHASE";
				$ArrJurnalNew[$key]['id_gudang_ke'] 	= $id_tujuan;
				$ArrJurnalNew[$key]['kd_gudang_ke'] 	= $kode_gudang;
				$ArrJurnalNew[$key]['qty_stock_awal'] 	= $stokjurnalakhir;
				$ArrJurnalNew[$key]['qty_stock_akhir'] 	= $qtyIN+$stokjurnalakhir;
				$ArrJurnalNew[$key]['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew[$key]['tgl_trans'] 		= $DateTime;
				$ArrJurnalNew[$key]['qty_in'] 			= $qtyIN;
				$ArrJurnalNew[$key]['ket'] 				= 'incoming material (insert new), harga : $hargaBeli';
				$ArrJurnalNew[$key]['harga'] 			= $PRICENEW;
				$ArrJurnalNew[$key]['harga_bm'] 		= $value['bm'];
				$ArrJurnalNew[$key]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$key]['nilai_trans_rp']	= (( ($value['kurs'] * $value['unit_price'])*$qtyIN));
				$ArrJurnalNew[$key]['nilai_akhir_rp']	= $nilaijurnalakhir+(( ($value['kurs'] * $value['unit_price'])*$qtyIN));
				$ArrJurnalNew[$key]['update_by'] 		= $UserName;
				$ArrJurnalNew[$key]['update_date'] 		= $DateTime;
				$ArrJurnalNew[$key]['no_jurnal'] 		= $Nomor_JV;
				$ArrJurnalNew[$key]['coa_gudang'] 		= $coa_gudang;
				
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				//update stock
				$ArrStockNew[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockNew[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockNew[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockNew[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockNew[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockNew[$key]['id_gudang'] 	= $id_tujuan;
				$ArrStockNew[$key]['kd_gudang'] 	= $kode_gudang;
				$ArrStockNew[$key]['qty_stock'] 	= $qtyIN;
				$ArrStockNew[$key]['qty_booking'] 	= 0;
				$ArrStockNew[$key]['qty_rusak'] 	= $qtyRISK;
				$ArrStockNew[$key]['update_by'] 	= $UserName;
				$ArrStockNew[$key]['update_date'] 	= date('Y-m-d H:i:s');
				$ArrStockNew[$key]['harga'] 		= $value['unit_price'];//update agus
				
				//insert history
				$ArrHistNew[$key]['id_material'] 		= $restMat[0]->id_material;
				$ArrHistNew[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistNew[$key]['nm_material'] 		= $restMat[0]->nm_material;
				$ArrHistNew[$key]['id_category'] 		= $restMat[0]->id_category;
				$ArrHistNew[$key]['nm_category'] 		= $restMat[0]->nm_category;
				$ArrHistNew[$key]['id_gudang'] 			= $id_tujuan;
				$ArrHistNew[$key]['kd_gudang'] 			= $kode_gudang;
				$ArrHistNew[$key]['kd_gudang_dari'] 	= "PURCHASE";
				$ArrHistNew[$key]['id_gudang_ke'] 		= $id_tujuan;
				$ArrHistNew[$key]['kd_gudang_ke'] 		= $kode_gudang;
				$ArrHistNew[$key]['qty_stock_awal'] 	= 0;
				$ArrHistNew[$key]['qty_stock_akhir'] 	= $qtyIN;
				$ArrHistNew[$key]['qty_booking_awal'] 	= 0;
				$ArrHistNew[$key]['qty_booking_akhir']	= 0;
				$ArrHistNew[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistNew[$key]['qty_rusak_akhir'] 	= $qtyRISK;
				$ArrHistNew[$key]['no_ipp'] 			= $no_po."/".$kode_trans;
				$ArrHistNew[$key]['jumlah_mat'] 		= $qtyIN + $qtyRISK;
				$ArrHistNew[$key]['ket'] 				= 'incoming material (insert new)';
				$ArrHistNew[$key]['update_by'] 			= $UserName;
				$ArrHistNew[$key]['update_date'] 		= $DateTime;
				//update agus
				$ArrHistNew[$key]['harga'] 				= $value['unit_price'];
				$ArrHistNew[$key]['saldo_awal']			= 0;
				$ArrHistNew[$key]['saldo_akhir']		= (( $value['unit_price']*$qtyIN));
				
				
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_tujuan, 'id_material'=>$key),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;

				if(!empty($stok_jurnal_akhir)) $PRICE2=$stok_jurnal_akhir->harga;
				
				
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book',array('id_material'=>$key))->result();
					//$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
				
				
				
				
				
				$hargaBeli      = ($value['kurs'] * $value['unit_price']);
				$PRICE      = ($value['kurs'] * $value['unit_price']);
				$stok_akhir = ($PRICE*$qtyIN)+($PRICE2*$stok_jurnal_akhir);
				
				if($stok_akhir==0){
					$PRICENEW = 0;
				} else{
				   $PRICENEW = $stok_akhir/($qtyIN+$stok_jurnal_akhir);
		        }
				
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				$ArrJurnalNew[$key]['id_material'] 		= $restMat[0]->id_material;
				$ArrJurnalNew[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrJurnalNew[$key]['nm_material'] 		= $restMat[0]->nm_material;
				$ArrJurnalNew[$key]['id_category'] 		= $restMat[0]->id_category;
				$ArrJurnalNew[$key]['nm_category'] 		= $restMat[0]->nm_category;
				$ArrJurnalNew[$key]['id_gudang'] 		= $id_tujuan;
				$ArrJurnalNew[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrJurnalNew[$key]['kd_gudang_dari'] 	= "PURCHASE";
				$ArrJurnalNew[$key]['id_gudang_ke'] 	= $id_tujuan;
				$ArrJurnalNew[$key]['kd_gudang_ke'] 	= $kode_gudang;
				$ArrJurnalNew[$key]['qty_stock_awal'] 	= $stokjurnalakhir;
				$ArrJurnalNew[$key]['qty_stock_akhir'] 	= $qtyIN+$stokjurnalakhir;
				$ArrJurnalNew[$key]['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew[$key]['tgl_trans'] 		= $DateTime;
				$ArrJurnalNew[$key]['qty_in'] 			= $qtyIN;
				$ArrJurnalNew[$key]['ket'] 				= 'incoming material (insert new), harga : $hargaBeli'; 
				$ArrJurnalNew[$key]['harga'] 			= $PRICENEW;
				$ArrJurnalNew[$key]['harga_bm'] 		= $value['bm'];
				$ArrJurnalNew[$key]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$key]['nilai_trans_rp']	= (( ($value['kurs'] * $value['unit_price'])*$qtyIN));
				$ArrJurnalNew[$key]['nilai_akhir_rp']	= $nilaijurnalakhir+(( ($value['kurs'] * $value['unit_price'])*$qtyIN));
				$ArrJurnalNew[$key]['update_by'] 		= $UserName;
				$ArrJurnalNew[$key]['update_date'] 		= $DateTime;
				$ArrJurnalNew[$key]['no_jurnal'] 		= $Nomor_JV;
				$ArrJurnalNew[$key]['coa_gudang'] 		= $coa_gudang;
			}
		}

		//PROCESS UPDATE STOCK EXPIRED
		//grouping sum
		$tempEXP = [];
		$temp2EXP= [];
		$grouping_tempEXP = [];
		$key = 0;
		// print_r($ArrUpdateStockExp);
		// exit;
		foreach($ArrUpdateStockExp as $value) { $key++;
			//grouping good
			if(!array_key_exists($value['id'].$value['expired'], $tempEXP)) {
				$tempEXP[$value['id'].$value['expired']]['good'] = 0;
			}
			$tempEXP[$value['id'].$value['expired']]['good'] += $value['qty_good'];

			//grouping risk
			if(!array_key_exists($value['id'], $temp2EXP)) {
				$temp2EXP[$value['id'].$value['expired']]['risk'] = 0;
			}
			$temp2EXP[$value['id'].$value['expired']]['risk'] += $value['qty_risk'];


			$grouping_tempEXP[$value['id'].$value['expired']]['id'] 		= $value['id'];
			$grouping_tempEXP[$value['id'].$value['expired']]['qty_good'] 	= $tempEXP[$value['id'].$value['expired']]['good'];
			$grouping_tempEXP[$value['id'].$value['expired']]['qty_risk'] 	= $temp2EXP[$value['id'].$value['expired']]['risk'];
			$grouping_tempEXP[$value['id'].$value['expired']]['expired'] 	= $value['expired'];
		}

		foreach ($grouping_tempEXP as $key => $value) {
			$rest_exp	= $this->db->get_where('warehouse_stock_expired', array('id_material'=>$value['id'],'id_gudang'=>$id_tujuan,'expired'=>$value['expired']))->result();
			
			$QTY_GOOD = $value['qty_good'];
			$QTY_RISK = $value['qty_risk'];

			if(!empty($rest_exp)){
				$ArrUpdExp[$key]['id'] 			= $rest_exp[0]->id;
				$ArrUpdExp[$key]['qty_stock'] 	= $rest_exp[0]->qty_stock + $QTY_GOOD;
				$ArrUpdExp[$key]['qty_rusak'] 	= $rest_exp[0]->qty_rusak + $QTY_RISK;
				$ArrUpdExp[$key]['update_by'] 	= $UserName;
				$ArrUpdExp[$key]['update_date'] = $DateTime;
				
				$ArrUpdExpHist[$key]['id_material'] 	= $rest_exp[0]->id_material;
				$ArrUpdExpHist[$key]['nm_material'] 	= $rest_exp[0]->nm_material;;
				$ArrUpdExpHist[$key]['id_gudang'] 		= $id_tujuan;
				$ArrUpdExpHist[$key]['expired'] 		= $value['expired'];
				$ArrUpdExpHist[$key]['qty_stock'] 		= $rest_exp[0]->qty_stock;
				$ArrUpdExpHist[$key]['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
				$ArrUpdExpHist[$key]['qty_stock_akhir'] = $rest_exp[0]->qty_stock + $QTY_GOOD;
				$ArrUpdExpHist[$key]['qty_rusak_akhir'] = $rest_exp[0]->qty_rusak + $QTY_RISK;
				$ArrUpdExpHist[$key]['kode_trans'] 		= $kode_trans;
				$ArrUpdExpHist[$key]['update_by'] 		= $UserName;
				$ArrUpdExpHist[$key]['update_date'] 	= $DateTime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$value['id']."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();
				
				$ArrInsExp[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrInsExp[$key]['nm_material'] 	= $restMat[0]->nm_material;;
				$ArrInsExp[$key]['id_gudang'] 		= $id_tujuan;
				$ArrInsExp[$key]['expired'] 		= $value['expired'];
				$ArrInsExp[$key]['qty_stock'] 		= $QTY_GOOD;
				$ArrInsExp[$key]['qty_rusak'] 		= $QTY_RISK;
				$ArrInsExp[$key]['update_by'] 		= $UserName;
				$ArrInsExp[$key]['update_date'] 	= $DateTime;
				
				$ArrInsExpHist[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrInsExpHist[$key]['nm_material'] 	= $restMat[0]->nm_material;;
				$ArrInsExpHist[$key]['id_gudang'] 		= $id_tujuan;
				$ArrInsExpHist[$key]['expired'] 		= $value['expired'];
				$ArrInsExpHist[$key]['qty_stock'] 		= 0;
				$ArrInsExpHist[$key]['qty_rusak'] 		= 0;
				$ArrInsExpHist[$key]['qty_stock_akhir'] = $QTY_GOOD;
				$ArrInsExpHist[$key]['qty_rusak_akhir'] = $QTY_RISK;
				$ArrInsExpHist[$key]['kode_trans'] 		= $kode_trans;
				$ArrInsExpHist[$key]['update_by'] 		= $UserName;
				$ArrInsExpHist[$key]['update_date'] 	= $DateTime;
			}
		}


		// print_r($ArrUpdate);
		// print_r($ArrDeatil);
		// print_r($ArrDeatilAdj);

		// print_r($ArrStock);
		// print_r($ArrHist);
		// print_r($ArrStockNew);
		// print_r($ArrHistNew);

		// print_r($ArrUpdExp);
		// print_r($ArrUpdExpHist);
		// print_r($ArrInsExp);
		// print_r($ArrInsExpHist);
		// exit;

		// jurnal
		$jenis_jurnal = 'JV032';
        $nomor_jurnal = $jenis_jurnal . $no_ros . rand(100, 999);
        $det_Jurnaltes1 = array();
		$total_forward_bef_ppn=0;
		$total_forward_ppn=0;
		$payment_date=date('Y-m-d');
        $data_ros_forward = $this->db->query("SELECT * FROM report_of_shipment_forward WHERE id_ros='$no_ros' ")->result();
        if(!empty($data_ros_forward)){
		    foreach ($data_ros_forward as $keys) {
				$total_forward_bef_ppn=($total_forward_bef_ppn+$keys->cost);
				$total_forward_ppn=($total_forward_ppn+$keys->ppn);
            }
		}
		$this->db->trans_start();
			// loping warehouse_adjustment_detail
			$harga_freight=0;
			foreach ($ArrDeatil as $key=>$val){
				if($total_harga_product>0 ) $harga_freight=round(((($val['harga']*$kurs_ros*$val['check_qty_oke'])/$total_harga_product)*$total_freight*$kurs_ros/$val['check_qty_oke']),0);
				$ArrDeatil[$key]['harga_freight'] = $harga_freight;
				$stock_exp	= "SELECT sum(qty_stock) as ttl_qty,sum((qty_stock)*harga) as ttl_harga FROM warehouse_stock WHERE id_material = '".$val['id_material']."' and kd_gudang='".$kode_gudang."' group by id_material";
				$dtstock	= $this->db->query($stock_exp)->result();

				if(!empty($dtstock)){
					$ttl_harga=($dtstock[0]->ttl_harga+((($val['harga']*$kurs_ros)+$harga_freight)*$val['check_qty_oke']));
					$ttl_qty=($dtstock[0]->ttl_qty+$val['check_qty_oke']);
					$newharga=round(($ttl_harga/$ttl_qty),0);
					$this->db->query("update warehouse_stock set harga='".$newharga."' WHERE id_material = '".$val['id_material']."' and kd_gudang='".$kode_gudang."'");
				}else{
					$newharga=($val['harga']+$harga_freight);
					$ArrStockNew[$val['id_material']]['harga']=$newharga;
				}
			}


			// $this->db->update_batch('tran_material_po_detail', $ArrUpdatePO, 'id');
			insert_jurnal($grouping_temp,'incoming',$id_tujuan,$kode_trans,'incoming','incoming material','penambahan gudang pusat');

			$this->db->where('id', $id_header);
			$this->db->update('warehouse_adjustment', $ArrUpdate);
			
		// Jurnal
			$data_po = $this->db->query("SELECT * FROM tran_material_po_header WHERE no_po='$no_po'")->row();
			$datajurnal1 = $this->db->query("select * from " . DBACC . ".master_oto_jurnal_detail where kode_master_jurnal='" . $jenis_jurnal . "' order by parameter_no")->result();
			$hutang = 0;
			$hutang_kurs =0;
			$uangmuka = 0;
			$kurs=$kurs_ros;
			$total_harga=0;
			$total_rupiah=$total_harga_product;
			$total_forex=$total_harga_product_usd;
			$selisih_kurs=0;
			$unbill_coa="";
			$unbill_nilai=0;
			$coa_hutang_unbill='';
			foreach ($datajurnal1 as $rec) {
				if ($rec->parameter_no == "1") {
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Material ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : ($total_harga_product+$total_forward_bef_ppn)), 'kredit' => ($rec->posisi == 'D' ? 0 : ($total_harga_product+$total_forward_bef_ppn)), 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : 0), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : 0), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
					);
				}
				if ($rec->parameter_no == "2") {
					$uangmuka = $total_rupiah;
					
					$coa_uangmuka=$rec->no_perkiraan;
					if($kurs_ros>1) $coa_uangmuka='1111-01-02';
					
					if ($data_po->nilai_dp > 0) {
						if ($data_po->nilai_dp <= $total_forex) {
							$uangmuka = $data_po->nilai_dp_kurs;//($kurs * $data_po->nilai_dp);
							$uangmukausd = $data_po->nilai_dp;
							$selisih_kurs=($uangmuka-($kurs * $data_po->nilai_dp));
							$hutang = ($total_rupiah - ($kurs * $data_po->nilai_dp));
							$hutang_kurs = $total_forex-$data_po->nilai_dp;
							$this->db->query("update tran_material_po_header set nilai_terima_barang_kurs=".$hutang.",proses_uang_muka='Y', nilai_dp=0, sisa_dp=0 where no_po='" . $no_po . "'");
						} else {
							$nilai_kurs_saat_dp=($data_po->nilai_dp_kurs/$data_po->nilai_dp);
							$selisih_kurs=(($total_forex*$nilai_kurs_saat_dp)-($kurs * $total_forex));

							$this->db->query("update tran_material_po_header set proses_uang_muka='Y', nilai_dp=(nilai_dp-" . $total_forex . "), sisa_dp=(sisa_dp-" . $total_forex . ") where no_po='" . $no_po . "'");
						}
						$det_Jurnaltes1[] = array(
							'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $coa_uangmuka, 'keterangan' => 'Uang muka ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : $uangmuka), 'kredit' => ($rec->posisi == 'D' ? 0 : $uangmuka), 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : $uangmukausd), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : $uangmukausd), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
						);
					} else {
						$det_Jurnaltes1[] = array(
							'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $coa_uangmuka, 'keterangan' => 'Uang muka ' . $no_po, 'no_request' => $no_po, 'debet' => 0, 'kredit' => 0, 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : 0), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : 0), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
						);
					}
					
				$uangmuka2 = $data_po->nilai_dp_kurs;//($kurs * $data_po->nilai_dp);
				$uangmukausd2 = $data_po->nilai_dp;
				}
				if ($rec->parameter_no == "3") {
					$coa_hutang_unbill=$rec->no_perkiraan;
					if($kurs_ros>1) $coa_hutang_unbill='2101-01-04';					
					if ($hutang > 0) {
						$det_Jurnaltes1[] = array(
							'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $coa_hutang_unbill, 'keterangan' => 'Hutang ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : $hutang), 'kredit' => ($rec->posisi == 'D' ? 0 : $hutang), 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : $hutang_kurs), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : $hutang_kurs), 'no_reff' => $kode_trans, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
						);
						$unbill_nilai=$hutang;
					} else {
						$det_Jurnaltes1[] = array(
							'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $coa_hutang_unbill, 'keterangan' => 'Hutang ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : $total_rupiah), 'kredit' => ($rec->posisi == 'D' ? 0 : $total_rupiah), 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : $hutang_kurs), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : $hutang_kurs), 'no_reff' => $kode_trans, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
						);
					}
					$unbill_coa=$rec->no_perkiraan;
					$unbill_nilai=$total_rupiah;
				}
				if ($rec->parameter_no == "4") {
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Cash/Bank' . $no_po, 'no_request' => $no_po, 'debet' => 0, 'kredit' => 0, 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : 0), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : 0), 'no_reff' => $kode_trans, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
					);
				}
				if ($rec->parameter_no == "5") {
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Hutang Forwarder ' . $no_po, 'no_request' => $no_po, 'debet' => 0, 'kredit' => ($total_forward_bef_ppn+$total_forward_ppn), 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : 0), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : 0), 'no_reff' => $kode_trans, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
					);
				}
				if ($rec->parameter_no == "6") {
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'PPN dibayar dimuka' . $no_po, 'no_request' => $no_po, 'debet' => $total_forward_ppn, 'kredit' => 0,'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : 0), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : 0), 'no_reff' => $kode_trans, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
					);
				}
				if ($rec->parameter_no == "7") {
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Selisih kurs' . $no_po, 'no_request' => $no_po, 'kredit' => ($selisih_kurs<0?($selisih_kurs*-1):0), 'debet' => ($selisih_kurs>=0?$selisih_kurs:0), 'nilai_valas_debet' => ($rec->posisi == 'K' ? 0 : 0), 'nilai_valas_kredit' => ($rec->posisi == 'D' ? 0 : 0),'no_reff' => $kode_trans, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier, 'stspos' => "1"
					);
				}
			}
			
			if($kurs_ros>1){
			$this->db->query("update tran_material_po_header set terima_barang_idr=(terima_barang_idr+".$total_rupiah."), terima_barang_kurs=(terima_barang_kurs+".$total_forex."), kurs_terima=".$kurs_ros.", hutang_kurs=(hutang_kurs+".$hutang_kurs."),hutang_idr=(hutang_idr+".$hutang."),sisa_hutang_kurs=(sisa_hutang_kurs+".$hutang_kurs."),sisa_hutang_idr=(sisa_hutang_idr+".$hutang."),
			total_terima_barang_idr=(total_terima_barang_idr+" . $total_rupiah . ") where no_po='" . $no_po . "'");
			}else{
			$this->db->query("update tran_material_po_header set terima_barang_idr=(terima_barang_idr+".$total_rupiah."), terima_barang_kurs=(terima_barang_kurs+".$total_forex."), kurs_terima=".$kurs_ros.", hutang_kurs=(hutang_kurs+".$hutang_kurs."),hutang_idr=(hutang_idr+".$unbill_nilai."),sisa_hutang_kurs=(sisa_hutang_kurs+".$hutang_kurs."),sisa_hutang_idr=(sisa_hutang_idr+".$unbill_nilai."),
			total_terima_barang_idr=(total_terima_barang_idr+" . $total_rupiah . ") where no_po='" . $no_po . "'");
			}

			$this->db->insert_batch('jurnaltras', $det_Jurnaltes1);
//auto jurnal
		$session = $this->session->userdata('app_session');
		$jenis_jurnal='JV032';
		$data_vendor 	= $this->db->query("select * from supplier where id_supplier='".$data_po->id_supplier."'")->row();
		$nama_vendor 	= $data_vendor->nm_supplier;
		$tanggal		= $payment_date;
		$Bln 			= substr($tanggal,5,2);
		$Thn 			= substr($tanggal,0,4);
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);
		$keterangan		= 'Incoming Material '.$kode_trans;

			$datadetail=array();
			$total=0;
			foreach ($det_Jurnaltes1 as $vals) {
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tanggal,
					'no_perkiraan'	=> $vals['no_perkiraan'],
					'keterangan'	=> $vals['keterangan'],
					'no_reff'		=> $vals['no_reff'],
					'debet'			=> $vals['debet'],
					'kredit'		=> $vals['kredit'],
					'nilai_valas_debet'			=> $vals['nilai_valas_debet'],
					'nilai_valas_kredit'		=> $vals['nilai_valas_kredit'],
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
				$total=($total+$vals['kredit']);
			}

		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tanggal,
			'jml'	            => $total,
			'bulan'	            => $Bln,
			'tahun'	            => $Thn,
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan'		=> $keterangan,
			'user_id'			=> $session['username'],
			'ho_valid'			=> '',
		);
		$this->db->insert(DBACC . '.javh', $dataJVhead);
		$Qry_Update_Cabang_acc = "UPDATE " . DBACC . ".pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
		$this->db->query($Qry_Update_Cabang_acc);

		$matauang='';
		$no_reff = $no_po;
		$row_po = $this->db->query("select * from tran_material_po_header where no_po='".$no_reff."'")->row();
		if(!empty($row_po)) {
			if($row_po->mata_uang=='IDR') $matauang='IDR';
		}
		if($matauang=='IDR' ){
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tanggal,
				'no_perkiraan'    => $unbill_coa,
				'keterangan'      => $keterangan,
				'no_reff'     	  => $no_reff,
				'kredit'      	  => $unbill_nilai,
				'debet'          => 0,
				'id_supplier'     => $data_po->id_supplier,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $kode_trans,
			);
			$this->db->insert('tr_kartu_hutang',$datahutang);
		}else{
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tanggal,
				'no_perkiraan'    => $coa_hutang_unbill,
				'keterangan'      => $keterangan,
				'no_reff'     	  => $no_reff,
				'debet'          => 0,
				'kredit'      	  => $hutang,
				'debet_usd'          => 0,
				'kredit_usd'      	  => $hutang_kurs,
				'id_supplier'     => $data_po->id_supplier,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $kode_trans, 
			);
			$this->db->insert('tr_kartu_hutang', $datahutang);
		}
		
		    $datauangmuka = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tanggal,
				'no_perkiraan'    => $coa_uangmuka,
				'keterangan'      => $keterangan,
				'no_reff'     	  => $no_reff,
				'debet'           => 0,
				'kredit'      	  => $uangmuka2,
				'debet_usd'       => 0,
				'kredit_usd'      => $uangmukausd2,
				'id_supplier'     => $data_po->id_supplier,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $kode_trans, 
			);
			$this->db->insert('tr_kartu_hutang', $datauangmuka);
// end auto jurnal


            $this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew);
			$this->db->update_batch('warehouse_adjustment_detail', $ArrDeatil, 'id');
			$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilAdj);
			
			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}
			if(!empty($ArrStockNew)){
				$this->db->insert_batch('warehouse_stock', $ArrStockNew);
				$this->db->insert_batch('warehouse_history', $ArrHistNew);
			}
			if(!empty($ArrUpdExp)){
				$this->db->update_batch('warehouse_stock_expired', $ArrUpdExp, 'id');
				$this->db->insert_batch('warehouse_stock_expired_hist', $ArrUpdExpHist);
			}
			if(!empty($ArrInsExp)){
				$this->db->insert_batch('warehouse_stock_expired', $ArrInsExp);
				$this->db->insert_batch('warehouse_stock_expired_hist', $ArrInsExpHist);
			}
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			// insertDataGroupReport($ArrUpdateStock, null, $id_tujuan, $kode_trans, null, null, null);
			insertDataGroupReport_Incoming($ArrUpdateStock, null, $id_tujuan, $kode_trans, null, null, null);
			history("Material Incoming Check id : ".$no_po."/".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}

	public function print_incoming(){
		$kode_trans     = $this->uri->segment(3);
		$check     = $this->uri->segment(4);

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();

		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/controllers/plusPurchaseOrder.php";

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print Incoming Material '.$kode_trans);

		print_incoming_material($Nama_Beda, $kode_trans, $koneksi, $printby, $check);
	}

	public function print_incoming2(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'check' => $check
		);

		history('Print Incoming Material '.$kode_trans);
		$this->load->view('Print/print_tt_material', $data);
	}

	public function print_request_before(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();

		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/controllers/plusPurchaseOrder.php";

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		$GET_SO_NUMBER = $this->db->get('so_number')->result_array();
		$ArrGetSO = [];
		foreach($GET_SO_NUMBER AS $val => $value){
			$ArrGetSO[$value['id_bq']] = $value['so_number'];
		}

		$GET_SPK_NUMBER = $this->db->get_where('so_detail_header',array('no_spk <>'=>NULL))->result_array();
		$ArrGetSPK = [];
		$ArrGetIPP = [];
		foreach($GET_SPK_NUMBER AS $val => $value){
			$ArrGetSPK[$value['id']] = $value['no_spk'];
			$ArrGetIPP[$value['id']] = $value['id_bq'];
		}

		history('Print Request Material '.$kode_trans);

		print_request_material($Nama_Beda, $kode_trans, $koneksi, $printby, $check,$ArrGetSO,$ArrGetSPK,$ArrGetIPP);
	}

	public function print_surat_jalan(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'check' => $check
		);
		history('Print Request Material '.$kode_trans);
		$this->load->view('Print/print_sj_material', $data);
	}

	public function print_request_sub(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'check' => $check
		);
		history('Print Request Material sub gudang '.$kode_trans);
		$this->load->view('Print/print_request_sub_new', $data);
	}

	public function process_adjustment(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$adjustment		= $data['adjustment'];
		$gudang_before	= $data['gudang_before'];
		$gudang_after	= $data['gudang_after'];
		$no_ipp			= $data['no_ipp'];
		// $note			= strtolower($data['note']);

		$histHlp = "Material Adjustment Out ".$gudang_before." ke ".$gudang_after."";


		// echo $adjustment."<br>";
		// echo $gudang_before."<br>";
		// echo $gudang_after."<br>";
		// echo $no_ipp."<br>";
		// exit;
		if($adjustment == 'OUT'){

			$sqlWhDetail	= "	SELECT
									a.*,
									b.id AS id2,
									b.qty_booking,
									b.kd_gudang,
									b.idmaterial,
									b.nm_material,
									b.id_category,
									b.nm_category,
									b.qty_stock
								FROM
									warehouse_planning_detail a
									LEFT JOIN warehouse_stock b
										ON a.id_material=b.id_material
								WHERE
									a.no_ipp = '".$no_ipp."'
									AND (b.kd_gudang='".$gudang_before."')
								";
			$restWhDetail	= $this->db->query($sqlWhDetail)->result_array();
			// echo $sqlWhDetail; exit;
			$ArrDeatil		 = array();
			$ArrHist		 = array();
			$SumMat = 0;
			foreach($restWhDetail AS $val => $valx){
				$SumMat += $valx['use_stock'];
				$ArrDeatil[$val]['id'] 			= $valx['id2'];
				$ArrDeatil[$val]['id_material'] = $valx['id_material'];
				$ArrDeatil[$val]['kd_gudang'] 	= $valx['kd_gudang'];
				// $ArrDeatil[$val]['qty_booking'] = $valx['qty_booking'] - $valx['use_stock'];
				$ArrDeatil[$val]['qty_stock'] 	= $valx['qty_stock'] - $valx['use_stock'];
				$ArrDeatil[$val]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrDeatil[$val]['update_date'] = date('Y-m-d H:i:s');
			}

			$ArrDeatil2		 = array();
			$ArrDeatil3		 = array();

			foreach($restWhDetail AS $val => $valx){
				$sqlCheck	= "SELECT * FROM warehouse_stock WHERE id_material='".$valx['id_material']."' AND kd_gudang = '".$gudang_after."' LIMIT 1 ";
				$restCheck	= $this->db->query($sqlCheck)->result();
				// echo $sqlCheck."<br>";
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$valx['id_material']."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();
				if(!empty($restCheck)){
					$ArrDeatil2[$val]['id'] 			= $restCheck[0]->id;
					$ArrDeatil2[$val]['id_material'] 	= $valx['id_material'];
					$ArrDeatil2[$val]['kd_gudang'] 		= $gudang_after;
					// $ArrDeatil2[$val]['qty_booking'] 	= $valx['qty_booking'] - $valx['use_stock'];
					$ArrDeatil2[$val]['qty_stock'] 		= $valx['qty_stock'] - $valx['use_stock'];
					$ArrDeatil2[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrDeatil2[$val]['update_date'] 	= date('Y-m-d H:i:s');
				}
				if(empty($restCheck)){
					$ArrDeatil3[$val]['id_material'] 	= $valx['id_material'];
					$ArrDeatil3[$val]['idmaterial'] 	= $valx['idmaterial'];
					$ArrDeatil3[$val]['nm_material'] 	= $valx['nm_material'];
					$ArrDeatil3[$val]['id_category'] 	= $restMat[0]->id_category;
					$ArrDeatil3[$val]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrDeatil3[$val]['kd_gudang'] 		= $gudang_after;
					$ArrDeatil3[$val]['qty_stock'] 		= $valx['use_stock'];
					$ArrDeatil3[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrDeatil3[$val]['update_date'] 	= date('Y-m-d H:i:s');
				}

			}
				// print_r($ArrDeatil2);
				// print_r($ArrDeatil3);

			// exit;

			foreach($restWhDetail AS $val => $valx){
				$ArrHist[$val]['id_material'] 		= $valx['id_material'];
				$ArrHist[$val]['idmaterial'] 		= $valx['idmaterial'];
				$ArrHist[$val]['nm_material'] 		= $valx['nm_material'];
				$ArrHist[$val]['id_category'] 		= $valx['id_category'];
				$ArrHist[$val]['nm_category'] 		= $valx['nm_category'];
				$ArrHist[$val]['kd_gudang_dari'] 	= $valx['kd_gudang'];
				$ArrHist[$val]['kd_gudang_ke'] 		= $gudang_after;
				$ArrHist[$val]['qty_stock_awal'] 	= $valx['qty_stock'];
				$ArrHist[$val]['qty_stock_akhir'] 	= $valx['qty_stock'] - $valx['use_stock'];
				$ArrHist[$val]['qty_booking_awal'] 	= $valx['qty_booking'];
				$ArrHist[$val]['qty_booking_akhir'] = $valx['qty_booking'];
				$ArrHist[$val]['no_ipp'] 			= $no_ipp;
				$ArrHist[$val]['jumlah_mat'] 		= $valx['use_stock'];
				$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist[$val]['update_date'] 		= date('Y-m-d H:i:s');
			}

			$ArrHeader = array(
					'sts_close' => 'Y',
					'last_gudang' => $gudang_after,
					'updated_by' => $data_session['ORI_User']['username'],
					'updated_date' => date('Y-m-d H:i:s')
				);

			$ArrInsertH = array(
				'no_ipp' => $no_ipp,
				'jumlah_mat' => $SumMat,
				'kd_gudang_dari' => $gudang_before,
				'kd_gudang_ke' => $gudang_after,
				// 'note' => $note,
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			);
			// print_r($ArrDeatil);
			// print_r($ArrDeatil2);
			// print_r($ArrDeatil3);
			// print_r($ArrHist);
			// print_r($ArrHeader);
			// print_r($ArrInsertH);
			// exit;
			$this->db->trans_start();
				$this->db->update_batch('warehouse_stock', $ArrDeatil, 'id');
				if(!empty($ArrDeatil2)){
					$this->db->update_batch('warehouse_stock', $ArrDeatil2, 'id');
				}
				if(!empty($ArrDeatil3)){
					$this->db->insert_batch('warehouse_stock', $ArrDeatil3);
				}
				$this->db->insert_batch('warehouse_history', $ArrHist);
				$this->db->insert('warehouse_adjustment', $ArrInsertH);

				$this->db->where('no_ipp', $no_ipp);
				$this->db->update('warehouse_planning_header', $ArrHeader);

			$this->db->trans_complete();
		}


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history($histHlp.' / '.$no_ipp);
		}
		echo json_encode($Arr_Data);
	}

	public function modal_move_gudang(){
		$this->load->view('Warehouse/modal_move_gudang');
	}

	public function move_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$gudang1		= $data['gudang1'];
		$gudang2		= $data['gudang2'];
		// $note			= strtolower($data['note']);
		$adjustment		= $data['ListMove'];
		$Ym 			= date('ym');
		// print_r($data);

		//pengurutan kode
		$srcMtr			= "SELECT MAX(no_po) as maxP FROM tran_material_purchase_header WHERE no_po LIKE 'MV".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 3);
		$urutan2++;
		$urut2			= sprintf('%03s',$urutan2);
		$no_move			= "MV".$Ym.$urut2;

		// echo $sqlWhDetail; exit;
		$ArrDeatil		 = array();
		$ArrHist		 = array();
		$SumMat = 0;
		foreach($adjustment AS $val => $valx){
			$qty_move = str_replace(',','',$valx['qty_move']);
			if($qty_move > 1){
				$SumMat += $qty_move;
				$ArrDeatil[$val]['id'] 			= $valx['id'];
				// $ArrDeatil[$val]['qty_booking'] = $valx['qty_booking'] - $qty_move;
				$ArrDeatil[$val]['qty_stock'] 	= $valx['qty_stock'] - $qty_move;
				$ArrDeatil[$val]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrDeatil[$val]['update_date'] = date('Y-m-d H:i:s');
			}
		}

		foreach($adjustment AS $val => $valx){
			$qty_move = str_replace(',','',$valx['qty_move']);
			if($qty_move > 1){
				$ArrHist[$val]['id_material'] 		= $valx['id_material'];
				$ArrHist[$val]['idmaterial'] 		= $valx['idmaterial'];
				$ArrHist[$val]['nm_material'] 		= $valx['nm_material'];
				$ArrHist[$val]['id_category'] 		= $valx['id_category'];
				$ArrHist[$val]['nm_category'] 		= $valx['nm_category'];
				$ArrHist[$val]['kd_gudang_dari'] 	= $valx['kd_gudang'];
				$ArrHist[$val]['kd_gudang_ke'] 		= $gudang2;
				$ArrHist[$val]['qty_stock_awal'] 	= $valx['qty_stock'];
				$ArrHist[$val]['qty_stock_akhir'] 	= $valx['qty_stock'] - $qty_move;
				$ArrHist[$val]['qty_booking_awal'] 	= $valx['qty_booking'];
				$ArrHist[$val]['qty_booking_akhir'] = $valx['qty_booking'];
				$ArrHist[$val]['no_ipp'] 			= $no_move;
				$ArrHist[$val]['jumlah_mat'] 		= $qty_move;
				$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist[$val]['update_date'] 		= date('Y-m-d H:i:s');
			}
		}

		$ArrInsertH = array(
			'no_ipp' => $no_move,
			'jumlah_mat' => $SumMat,
			'kd_gudang_dari' => $gudang1,
			'kd_gudang_ke' => $gudang2,
			// 'note' => $note,
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		);

		// print_r($ArrDeatil);
		// print_r($ArrHist);
		// print_r($ArrInsertH);
		// exit;

		$this->db->trans_start();
			$this->db->update_batch('warehouse_stock', $ArrDeatil, 'id');
			$this->db->insert_batch('warehouse_history', $ArrHist);
			$this->db->insert('warehouse_adjustment', $ArrInsertH);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history("Move Material ".$gudang1." to ".$gudang2." / ".$no_move);
		}
		echo json_encode($Arr_Data);
	}

	//==========================================================================================================================
	//===================================================REQUEST SUBGUDANG======================================================
	//==========================================================================================================================

	public function index_request_material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='pusat' ORDER BY urut ASC")->result_array();
		$subgudang			= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();
		$no_ipp				= $this->db->query("SELECT no_ipp FROM warehouse_planning_detail WHERE sudah_request < use_stock AND no_ipp LIKE 'IPP%' GROUP BY no_ipp")->result_array();
		$list_ipp_req		= $this->db->query("SELECT no_ipp FROM warehouse_adjustment WHERE no_ipp LIKE 'IPP%' GROUP BY no_ipp")->result_array();
		$uri_tanda			= $this->uri->segment(3);
		$judul = "Warehouse Material >> Gudang Pusat >> Request List";
		if($uri_tanda == ''){
			$judul = "Warehouse Material >> Sub Gudang >> Request SubGudang";
		}
		$data = array(
			'title'			=> $judul,
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'pusat'			=> $pusat,
			'subgudang'		=> $subgudang,
			'no_ipp'		=> $no_ipp,
			'list_ipp_req'	=> $list_ipp_req,
			'uri_tanda'		=> $uri_tanda
		);
		history('View Request SubGudang');
		$this->load->view('Warehouse/request_material',$data);
	}

	public function get_data_json_request_material(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request_material(
			$requestData['pusat'],
			$requestData['subgudang'],
			$requestData['tanda'],
			$requestData['uri_tanda'],
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
		$uri_tanda = $requestData['uri_tanda'];
		$GET_USER = get_detail_user();
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

			$DATE_TRANS = (!empty($row['tanggal']))?$row['tanggal']:$row['created_date'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($DATE_TRANS))."</div>";
			$nestedData[]	= "<div align='center'>".$row['kd_gudang_dari']."</div>";
			$nestedData[]	= "<div align='center'>".$row['kd_gudang_ke']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'],4)."</div>";
			$namaLengkap = (!empty($GET_USER[$row['created_by']]['nm_lengkap']))?$GET_USER[$row['created_by']]['nm_lengkap']:$row['created_by'];
			$nestedData[]	= "<div align='center'>".strtoupper($namaLengkap)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
			$status = "WAITING PROCESS";
			$warna = 'blue';
			if($uri_tanda == 'pusat' AND $row['checked'] == 'N' AND empty($row['deleted_date']) AND !empty($row['id_spk_create'])){
				$status = "WAITING CONFIRM";
				$warna = 'yellow';
			}
			if($row['checked'] == 'Y'){
				$status = "CONFIRMED";
				$warna = 'green';
			}

			if(!empty($row['deleted_date'])){
				$status = "CANCELED";
				$warna = 'red';
			}
			$nestedData[]	= "<div align='center'><span class='badge bg-".$warna."'>".$status."</span></div>";
				$plus	= "";
				$print	= "";
				$print2	= "";
				$edit	= "";
				$edit_new	= "";
				$reject	= "";
				$createspk	= "";

				if(!empty($uri_tanda)){
					if($row['checked'] == 'Y'){
						if(empty($row['id_spk_create'])){
						$print	= "&nbsp;<a href='".base_url('warehouse/print_surat_jalan/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Permintaan'><i class='fa fa-print'></i></a>";
						}
						if(!empty($row['id_spk_create'])){
							$print2	= "&nbsp;<a href='".base_url('warehouse/print_surat_jalan_spk_confirm/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-default' title='Print Confirm QR'><i class='fa fa-print'></i></a>";
						}
					}
					if($row['checked'] == 'N'){
						$plus	= "&nbsp;<button type='button' class='btn btn-sm btn-success check' title='Konfirmasi Permintaan' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-check'></i></button>";
						$reject	= "&nbsp;<button type='button' class='btn btn-sm btn-danger cancel_request' title='Cancel Request' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-trash'></i></button>";
					}
				}
				else{
					$print	= "&nbsp;<a href='".base_url('warehouse/print_request_sub/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Permintaan'><i class='fa fa-print'></i></a>";
					if($row['checked'] == 'N'){
						// $edit	= "&nbsp;<button type='button' class='btn btn-sm btn-info edit_material' title='Edit Permintaan' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-edit'></i></button>";
						$edit_new	= "&nbsp;<button type='button' class='btn btn-sm btn-default edit_material_new' title='Edit Permintaan (New)' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-edit'></i></button>";
					}
				}

				if($uri_tanda == 'pusat' AND $row['checked'] == 'N' AND empty($row['deleted_date']) AND empty($row['id_spk_create'])){
					$createspk	= "&nbsp;<button type='button' class='btn btn-sm btn-default createspk' title='Create SPK' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-hand-pointer-o'></i></button>";
				}
				if($uri_tanda == 'pusat' AND $row['checked'] == 'N' AND empty($row['deleted_date']) AND !empty($row['id_spk_create'])){
					$createspk	= "&nbsp;<a href='".base_url('warehouse/print_surat_jalan_spk/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-default' title='Print Permintaan'><i class='fa fa-print'></i> Print SPK</a>";
					$plus	= "";
					$reject	= "";
				}

				if(!empty($row['deleted_date'])){
					$plus	= "";
					$print	= "";
					$print2	= "";
					$edit	= "";
					$reject	= "";
				}

			$nestedData[]	= "<div align='left'>
									<button type='button' class='btn btn-sm btn-primary detailAjust' data-tanda='request' title='View Permintaan' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-eye'></i></button>
                                    ".$edit."
                                    ".$edit_new."
                                    ".$print."
                                    ".$print2."
									".$plus."
									".$reject."
									".$createspk."
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

	public function query_data_json_request_material($pusat, $subgudang, $tanda, $uri_tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){


		$where_pusat ='';
		if(!empty($pusat)){
			$where_pusat = " AND a.id_gudang_dari = '".$pusat."' ";
		}

		$where_subgudang ='';
		if(!empty($subgudang)){
			$where_subgudang = " AND a.id_gudang_ke = '".$subgudang."' ";
		}

		$where_tanda ='';
		if(!empty($tanda)){
			$where_tanda = " AND a.category = '".$tanda."' ";
		}

		$where_tanda2 ='';
		// if(!empty($uri_tanda)){
			// $where_tanda2 = " AND a.checked = 'N' ";
		// }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.id AS id_spk_create
			FROM
				warehouse_adjustment a
				LEFT JOIN warehouse_adjustment_spk b ON a.kode_trans=b.kode_trans,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.status_id = '1'
				".$where_tanda."
				".$where_tanda2."
				".$where_pusat."
				".$where_subgudang."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
				GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'created_date',
			3 => 'kd_gudang_dari',
			4 => 'kd_gudang_ke',
			5 => 'jumlah_mat',
			6 => 'created_by',
			7 => 'created_date'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function get_data_json_modal_request_material(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_modal_request_material(
			$requestData['pusat'],
			$requestData['subgudang'],
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

			$data_session	= $this->session->userdata;
			$queryx 	= "SELECT * FROM temp_server_side WHERE id_mat = '".$row['id']."' AND created_by = '".$data_session['ORI_User']['username']."' AND category='request subgudang' ";
			$get_temp 	= $this->db->query($queryx)->result();
			$qty   		= (!empty($get_temp))?number_format($get_temp[0]->qty,3):'';
			$ket    	= (!empty($get_temp))?$get_temp[0]->ket:'';
			
			$qty_pack = '';
			$qty_stock = 0;
			$qty_subgudang = 0;
			if($row['qty_stock'] > 0 AND $row['konversi'] > 0){
				$qty_stock = $row['qty_stock']/$row['konversi'];
			}
			if($row['qty_subgudang'] > 0 AND $row['konversi'] > 0){
				$qty_subgudang = $row['qty_subgudang']/$row['konversi'];
			}
			if(!empty($get_temp)){
				if($get_temp[0]->qty > 0 AND $row['konversi'] > 0){
					$qty_pack = $get_temp[0]->qty/$row['konversi'];
				}
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."<input type='hidden' name='detail[".$nomor."][id]' id='id_".$nomor."' value='".$row['id']."'></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material2'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($qty_stock,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($qty_subgudang,2)."</div>";
			if($row['konversi'] > 0){
			$nestedData[]	= "<div align='left'>
									<input type='hidden' style='width:100%' name='detail[".$nomor."][konversi]' id='konversi_".$nomor."' data-no='".$nomor."' value='".$row['konversi']."'>
									<input type='hidden' style='width:100%' name='detail[".$nomor."][sudah_request]' id='sudah_request_".$nomor."' data-no='".$nomor."' value='".$qty."'>
									<input type='text' style='width:100%' name='detail[".$nomor."][sudah_request_pack]' id='sudah_requestpack_".$nomor."' data-no='".$nomor."' value='".$qty_pack."' class='form-control input-sm text-center qtypack'>
									</div>";
			}
			else{
				$nestedData[]	= "<div align='center' class='text-bold text-danger'>Konversi Nol !</div>";
			}
			$nestedData[]	= "<div align='center'>".strtoupper($row['kode_satuan'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['konversi'],2)."</div>";
			if($row['konversi'] > 0){
				$nestedData[]	= "<div align='left'><input type='text' style='width:100%' name='detail[".$nomor."][ket_request]' id='ket_request_".$nomor."' data-no='".$nomor."' value='".$ket."' class='form-control input-sm text-left ket'><script type='text/javascript'>$('.maskM2').autoNumeric('init', {mDec: '4', aPad: false});</script></div>";
			}
			else{
				$nestedData[]	= "<div align='center'></div>";
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

	public function query_data_json_modal_request_material($pusat, $subgudang, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){


		$where_pusat ='';
		if(!empty($pusat)){
			$where_pusat = " AND a.id_gudang = '".$pusat."' ";
		}

		$sql = "
			SELECT
				a.*,
				b.nilai_konversi AS konversi,
				b.nm_material AS nm_material2,
				c.kode_satuan,
				(SELECT b.qty_stock FROM warehouse_stock b WHERE id_gudang='".$subgudang."' AND b.id_material=a.id_material LIMIT 1) AS qty_subgudang
			FROM
				warehouse_stock a
				LEFT JOIN raw_materials b ON a.id_material=b.id_material
				LEFT JOIN raw_pieces c ON b.id_packing = c.id_satuan
		    WHERE 1=1
				".$where_pusat."
			AND(
				b.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.nm_material',
			2 => 'qty_booking'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_request_material(){ 
		$gudang_before 	= $this->uri->segment(3);
		$gudang_after 	= $this->uri->segment(4);
		$tanggal_trans 	= $this->uri->segment(5);

		$data_session	= $this->session->userdata;
		$this->db->where('created_by', $data_session['ORI_User']['username']);
		$this->db->delete('temp_server_side');

		$data = array(
			'tanggal_trans' => $tanggal_trans,
			'gudang_before' => $gudang_before,
			'gudang_after' 	=> $gudang_after
		);

		$this->load->view('Warehouse/modal_request_material', $data);
	}

	public function process_request_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;

		// $detail			= $data['detail'];
		$detail			= $this->db->get_where('temp_server_side', array('category'=>'request subgudang','created_by'=>$data_session['ORI_User']['username']))->result_array();
		$gudang_before	= $data['gudang_before'];
		$gudang_after	= $data['gudang_after'];
		$tanggal		= $data['tanggal_trans'];
		$Ym 			= date('ym');
		

		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++; 
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRS".$Ym.$urut2;

		$ArrDeatilAdj	 = array();
		$SUM_MAT = 0;
		foreach($detail AS $val => $valx){
			$sudah_request 	= str_replace(',','',$valx['qty']);
			if($sudah_request > 0){
				$SUM_MAT += $sudah_request;
				$rest_pusat	= $this->db->get_where('warehouse_stock', array('id'=>$valx['id_mat'],'id_gudang'=>$gudang_before))->result();

				//detail adjustmeny
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['id_material_req'] 	= $rest_pusat[0]->id_material;
				$ArrDeatilAdj[$val]['id_material'] 		= $rest_pusat[0]->id_material;
				$ArrDeatilAdj[$val]['nm_material'] 		= $rest_pusat[0]->nm_material;
				$ArrDeatilAdj[$val]['id_category'] 		= $rest_pusat[0]->id_category;
				$ArrDeatilAdj[$val]['nm_category'] 		= $rest_pusat[0]->nm_category;
				$ArrDeatilAdj[$val]['qty_order'] 		= $sudah_request;
				$ArrDeatilAdj[$val]['qty_oke'] 			= $sudah_request;
				$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['ket']);
				$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrDeatilAdj[$val]['update_date'] 		= date('Y-m-d H:i:s');
			}
		}

		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'tanggal' 			=> $tanggal,
			'category' 			=> 'request subgudang',
			'jumlah_mat' 		=> $SUM_MAT,
			'id_gudang_dari' 	=> $gudang_before,
			'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $gudang_before),
			'id_gudang_ke' 		=> $gudang_after,
			'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $gudang_after),
			// 'note' => $note,
			'created_by' 		=> $data_session['ORI_User']['username'],
			'created_date' 		=> date('Y-m-d H:i:s')
		);

		// print_r($ArrInsertH);
		// print_r($ArrDeatilAdj);
		// exit;
		$this->db->trans_start();
			$this->db->insert('warehouse_adjustment', $ArrInsertH);
			$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history("Material request subgudang : ".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}

	public function modal_request_check(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;

			$detail			= $data['detail'];
			$kode_trans		= $data['kode_trans'];

			$id_gudang_dari	    = $data['gudang_before'];
			$kode_gudang_dari 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
			
			$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->row();
			$coa_gudang = $coa_1->coa_1;
			

			$id_tujuan	    = $data['gudang_after'];
			$kode_gudang_tujuan 	= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
			
			$coa_2    = $this->db->get_where('warehouse', array('id'=>$id_tujuan))->row();
			$coa_gudang2 = $coa_2->coa_1;
			
			$Ym 			= date('ym');

			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');

			//UPLOAD DOCUMENT
			$file_name = '';
			$ArrEndChange = [];
			if(!empty($_FILES["upload_spk"]["name"])){
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
				$name_file      = 'qc_eng_change_req_'.date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
				$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
				$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
				$file_name    	= $name_file.".".$imageFileType;

				if(!empty($_FILES["upload_spk"]["tmp_name"])){
					move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
				}

				$ArrEndChange = array(
					'file_eng_change' 	=> $file_name
				);
			}
			// print_r($data);
			// exit;

			$getHeaderAdjust 		= $this->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();

			$ArrDeatil		 	= array();
			$ArrDeatilAdj		= array();
			$ArrUpdateStock		= array();
			$ArrUpdateStockExp	= array();
			$ArrUpdateRequest	= array();


			$SUM_MAT = 0;
			foreach($detail AS $val2 => $valx2){
				$QTY_OKE 	= 0;

				foreach($valx2['detail'] AS $val => $valx){
					$QTY_OKE 	+= str_replace(',','',$valx['qty_oke']);

					if(str_replace(',','',$valx['qty_oke']) > 0){
						$EXPIRED = (!empty($valx['expired']))?$valx['expired']:NULL;

						$ID_MATERIAL_ACT = $valx2['id_material'];

						//UPDATE ADJUSTMENT DETAIL
						$ArrDeatil[$val2]['id'] 			    = $valx2['id'];
						$ArrDeatil[$val2]['id_material'] 		= $ID_MATERIAL_ACT;
						$ArrDeatil[$val2]['check_qty_oke'] 		= $QTY_OKE;
						$ArrDeatil[$val2]['check_keterangan']	= strtolower($valx['check_keterangan']);
						$ArrDeatil[$val2]['update_by'] 			= $UserName;
						$ArrDeatil[$val2]['update_date'] 		= $DateTime;

						$getDetAjust 	= $this->db->get_where('warehouse_adjustment_detail', array('id'=>$valx2['id']))->result();
						$getDetMat 		= $this->db->get_where('raw_materials', array('id_material'=>$ID_MATERIAL_ACT))->result();
						//INSERT ADJUSTMENT CHECK
						// $ArrDeatilAdj[$val2.$val]['no_ipp'] 		= $getDetAjust[0]->no_ipp;
						$ArrDeatilAdj[$val2.$val]['id_detail'] 		= $valx2['id'];
						$ArrDeatilAdj[$val2.$val]['kode_trans'] 	= $kode_trans;
						$ArrDeatilAdj[$val2.$val]['id_material'] 	= $getDetMat[0]->id_material;
						$ArrDeatilAdj[$val2.$val]['nm_material'] 	= $getDetMat[0]->nm_material;
						$ArrDeatilAdj[$val2.$val]['id_category'] 	= $getDetMat[0]->id_category;
						$ArrDeatilAdj[$val2.$val]['nm_category'] 	= $getDetMat[0]->nm_category;
						$ArrDeatilAdj[$val2.$val]['qty_order'] 		= $getDetAjust[0]->qty_order;
						$ArrDeatilAdj[$val2.$val]['qty_oke'] 		= $QTY_OKE;
						$ArrDeatilAdj[$val2.$val]['qty_rusak'] 		= 0;
						$ArrDeatilAdj[$val2.$val]['expired_date'] 	= $EXPIRED;
						$ArrDeatilAdj[$val2.$val]['keterangan'] 	= strtolower($valx['check_keterangan']);
						$ArrDeatilAdj[$val2.$val]['update_by'] 		= $UserName;
						$ArrDeatilAdj[$val2.$val]['update_date'] 	= $DateTime;

						//MATERIAL YANG AKAN DI UPDATE
						$ArrUpdateStock[$val2]['id'] 	= $ID_MATERIAL_ACT;
						$ArrUpdateStock[$val2]['qty'] 	= $QTY_OKE;

						//MATERIAL YANG AKAN DI UPDATE EXPIRED
						if(!empty($EXPIRED)){
							$ArrUpdateStockExp[$val2]['id'] 		= $ID_MATERIAL_ACT;
							$ArrUpdateStockExp[$val2]['qty_good'] 	= $QTY_OKE;
							$ArrUpdateStockExp[$val2]['expired'] 	= $EXPIRED;
						}

						//UPDATE TEQUEST MATERIAL
						$getRequest = $this->db->get_where('planning_detail_spk', array('id_milik'=>$getDetAjust[0]->id_po_detail,'no_ipp'=>$getHeaderAdjust[0]->no_ipp,'id_material'=>$getDetAjust[0]->id_material))->result();
						if(!empty($getRequest)){
							$ArrUpdateRequest[$val2.$val]['id'] 			= $getRequest[0]->id;
							$ArrUpdateRequest[$val2.$val]['total_aktual'] 	= $getRequest[0]->total_aktual + $QTY_OKE;
							$ArrUpdateRequest[$val2.$val]['total_request'] 	= $getRequest[0]->total_request - ($getDetAjust[0]->qty_oke - $QTY_OKE);
						}
						
						}
						
				$key = $getDetMat[0]->id_material;				
					
			    $stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				
				$GudangFrom = $id_gudang_dari;
				if($GudangFrom == '2'){
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book',array('id_material'=>$key))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$key),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;

				}elseif($GudangFrom == '3'){
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$key),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == '30'){
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_produksi',array('id_material'=>$key))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
					
				}
				
				
				$ArrJurnalNew[$val2]['id_material'] 		= $getDetMat[0]->id_material;
				$ArrJurnalNew[$val2]['idmaterial'] 			= $getDetMat[0]->idmaterial;
				$ArrJurnalNew[$val2]['nm_material'] 		= $getDetMat[0]->nm_material;
				$ArrJurnalNew[$val2]['id_category'] 		= $getDetMat[0]->id_category;
				$ArrJurnalNew[$val2]['nm_category'] 		= $getDetMat[0]->nm_category;
				$ArrJurnalNew[$val2]['id_gudang'] 			= $id_gudang_dari;
				$ArrJurnalNew[$val2]['kd_gudang'] 			= $kode_gudang_dari;
				$ArrJurnalNew[$val2]['id_gudang_dari'] 	    = $id_gudang_dari;
				$ArrJurnalNew[$val2]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
				$ArrJurnalNew[$val2]['id_gudang_ke'] 		= $id_tujuan;
				$ArrJurnalNew[$val2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew[$val2]['qty_stock_awal'] 		= $stokjurnalakhir;
				$ArrJurnalNew[$val2]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
				$ArrJurnalNew[$val2]['kode_trans'] 			= $kode_trans;
				$ArrJurnalNew[$val2]['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew[$val2]['qty_out'] 			= $QTY_OKE;
				$ArrJurnalNew[$val2]['ket'] 				= 'pindah gudang';
				$ArrJurnalNew[$val2]['harga'] 			= $PRICE;
				$ArrJurnalNew[$val2]['harga_bm'] 		= 0;
				$ArrJurnalNew[$val2]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$val2]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
				$ArrJurnalNew[$val2]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
				$ArrJurnalNew[$val2]['update_by'] 		= $UserName;
				$ArrJurnalNew[$val2]['update_date'] 		= $DateTime;
				$ArrJurnalNew[$val2]['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew[$val2]['coa_gudang'] 		= $coa_gudang;
				
				
				$stokjurnalakhir2=0;
				$nilaijurnalakhir2=0;
				$stok_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_tujuan, 'id_material'=>$key),1)->row();
				if(!empty($stok_jurnal_akhir2)) $stokjurnalakhir2=$stok_jurnal_akhir2->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir2)) $nilaijurnalakhir2=$stok_jurnal_akhir2->nilai_akhir_rp;
				
				
				$GudangFrom2 = $id_tujuan;
				$PRICE2 = 0;
				if($GudangFrom2 == '2'){
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book',array('id_material'=>$key))->result();
					//$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$key),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
				}elseif($GudangFrom2 == '3'){
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
					//$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$key),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
				}elseif($GudangFrom2 == '30'){
					//$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_produksi',array('id_material'=>$key))->result();
					//$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0; 
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
					
				}
				
				
				$PRICENEW = (($PRICE*$QTY_OKE) + ($PRICE2*$stokjurnalakhir2))/($QTY_OKE+$stokjurnalakhir2);
				$in   = 'pindah gudang in';
				$ket  = $in.$id_gudang_dari.$id_tujuan;
				
				$ArrJurnalNew2[$val2]['id_material'] 		= $getDetMat[0]->id_material;
				$ArrJurnalNew2[$val2]['idmaterial'] 		= $getDetMat[0]->idmaterial;
				$ArrJurnalNew2[$val2]['nm_material'] 		= $getDetMat[0]->nm_material;
				$ArrJurnalNew2[$val2]['id_category'] 		= $getDetMat[0]->id_category;
				$ArrJurnalNew2[$val2]['nm_category'] 		= $getDetMat[0]->nm_category;
				$ArrJurnalNew2[$val2]['id_gudang'] 			= $id_tujuan;
				$ArrJurnalNew2[$val2]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew2[$val2]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrJurnalNew2[$val2]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
				$ArrJurnalNew2[$val2]['id_gudang_ke'] 		= $id_tujuan;
				$ArrJurnalNew2[$val2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew2[$val2]['qty_stock_awal'] 	= $stokjurnalakhir2;
				$ArrJurnalNew2[$val2]['qty_stock_akhir'] 	= $stokjurnalakhir2+$QTY_OKE;
				$ArrJurnalNew2[$val2]['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew2[$val2]['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew2[$val2]['qty_in'] 			= $QTY_OKE;
				$ArrJurnalNew2[$val2]['ket'] 				= $ket;
				$ArrJurnalNew2[$val2]['harga'] 				= $PRICENEW;
				$ArrJurnalNew2[$val2]['harga_bm'] 			= 0; 
				$ArrJurnalNew2[$val2]['nilai_awal_rp']		= $nilaijurnalakhir2;
				$ArrJurnalNew2[$val2]['nilai_trans_rp']		= $PRICE*$QTY_OKE;
				$ArrJurnalNew2[$val2]['nilai_akhir_rp']		= ($stokjurnalakhir2+$QTY_OKE)*$PRICENEW;
				$ArrJurnalNew2[$val2]['update_by'] 			= $UserName;
				$ArrJurnalNew2[$val2]['update_date'] 		= $DateTime;
				$ArrJurnalNew2[$val2]['no_jurnal'] 			= '-';
				$ArrJurnalNew2[$val2]['coa_gudang'] 		= $coa_gudang2;
					
				}
				$SUM_MAT 	+= $QTY_OKE;
			}

			//PROCESS UPDATE STOCK
			//grouping sum
			$temp = [];
			$grouping_temp = [];
			$key = 0;
			foreach($ArrUpdateStock as $value) { $key++;
				if($value['qty'] > 0){
					if(!array_key_exists($value['id'], $temp)) {
						$temp[$value['id']]['good'] = 0;
					}
					$temp[$value['id']]['good'] += $value['qty'];

					$grouping_temp[$value['id']]['id'] 			= $value['id'];
					$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
				}
			}

			move_warehouse($ArrUpdateStock,$id_gudang_dari,$id_tujuan,$kode_trans);

			//Mengurangi Booking
			$getDetailSPK 	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
			$no_ipp 		= (!empty($getDetailSPK[0]->no_ipp))?$getDetailSPK[0]->no_ipp:0;

			$id_gudang_booking = 2;
			$GETS_STOCK = get_warehouseStockAllMaterial();
			$CHECK_BOOK = get_CheckBooking($no_ipp);
			$ArrUpdateSTockBook 		= [];
			$ArrUpdateHist 	= [];
			// print_r($temp);
			if($CHECK_BOOK === TRUE AND $no_ipp != 0){
				// echo 'Masuk';
				foreach ($temp as $material => $qty) {
					$KEY 		= $material.'-'.$id_gudang_booking;
					$booking 	= (!empty($GETS_STOCK[$KEY]['booking']))?$GETS_STOCK[$KEY]['booking']:0;
					$stock 		= (!empty($GETS_STOCK[$KEY]['stock']))?$GETS_STOCK[$KEY]['stock']:0;
					$rusak 		= (!empty($GETS_STOCK[$KEY]['rusak']))?$GETS_STOCK[$KEY]['rusak']:0;
					$id_stock 	= (!empty($GETS_STOCK[$KEY]['id']))?$GETS_STOCK[$KEY]['id']:null;
					$idmaterial 	= (!empty($GETS_STOCK[$KEY]['idmaterial']))?$GETS_STOCK[$KEY]['idmaterial']:null;
					$nm_material 	= (!empty($GETS_STOCK[$KEY]['nm_material']))?$GETS_STOCK[$KEY]['nm_material']:null;
					$id_category 	= (!empty($GETS_STOCK[$KEY]['id_category']))?$GETS_STOCK[$KEY]['id_category']:null;
					$nm_category 	= (!empty($GETS_STOCK[$KEY]['nm_category']))?$GETS_STOCK[$KEY]['nm_category']:null;

					$qtyInt = $qty['good'];
					// echo 'ID:'.$id_stock;
					if(!empty($id_stock)){
						$ArrUpdateSTockBook[$material]['id'] = $id_stock;
						$ArrUpdateSTockBook[$material]['qty_booking'] = $booking - $qtyInt;

						$ArrUpdateHist[$material]['id_material'] 	= $material;
						$ArrUpdateHist[$material]['idmaterial'] 	= $idmaterial;
						$ArrUpdateHist[$material]['nm_material'] 	= $nm_material;
						$ArrUpdateHist[$material]['id_category'] 	= $id_category;
						$ArrUpdateHist[$material]['nm_category'] 	= $nm_category;
						$ArrUpdateHist[$material]['id_gudang'] 		= $id_gudang_booking;
						$ArrUpdateHist[$material]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_booking);
						$ArrUpdateHist[$material]['id_gudang_dari'] = NULL;
						$ArrUpdateHist[$material]['kd_gudang_dari'] = 'BOOKING';
						$ArrUpdateHist[$material]['id_gudang_ke'] 	= NULL;
						$ArrUpdateHist[$material]['kd_gudang_ke'] 	= 'PENGURANG';
						$ArrUpdateHist[$material]['qty_stock_awal'] 	= $stock;
						$ArrUpdateHist[$material]['qty_stock_akhir'] 	= $stock;
						$ArrUpdateHist[$material]['qty_booking_awal'] 	= $booking;
						$ArrUpdateHist[$material]['qty_booking_akhir'] 	= $booking - $qtyInt;
						$ArrUpdateHist[$material]['qty_rusak_awal'] 	= $rusak;
						$ArrUpdateHist[$material]['qty_rusak_akhir'] 	= $rusak;
						$ArrUpdateHist[$material]['no_ipp'] 			= $no_ipp;
						$ArrUpdateHist[$material]['jumlah_mat'] 		= $qtyInt;
						$ArrUpdateHist[$material]['ket'] 				= 'pengurangan booking '.$kode_trans;
						$ArrUpdateHist[$material]['update_by'] 			= $UserName;
						$ArrUpdateHist[$material]['update_date'] 		= $DateTime;
					}
				}
			}

			// print_r($ArrUpdate);
			// print_r($ArrUpdateHist);
			// exit;

			//ENd Mengurangi Booking


			//PROCESS UPDATE STOCK EXPIRED
			//grouping sum
			$tempEXP = [];
			$grouping_tempEXP = [];
			$key = 0;
			// print_r($ArrUpdateStockExp);
			// exit;
			foreach($ArrUpdateStockExp as $value) { $key++;
				//grouping good
				if(!array_key_exists($value['id'].$value['expired'], $tempEXP)) {
					$tempEXP[$value['id'].$value['expired']]['good'] = 0;
				}
				$tempEXP[$value['id'].$value['expired']]['good'] += $value['qty_good'];

				$grouping_tempEXP[$value['id'].$value['expired']]['id'] 		= $value['id'];
				$grouping_tempEXP[$value['id'].$value['expired']]['qty_good'] 	= $tempEXP[$value['id'].$value['expired']]['good'];
				$grouping_tempEXP[$value['id'].$value['expired']]['expired'] 	= $value['expired'];
			}

			$ArrUpdExp		 = array();
			$ArrInsExp		 = array();
			$ArrUpdExpHist		 = array();
			$ArrInsExpHist		 = array();
			// PENAMBAHAN GUDANG
			foreach ($grouping_tempEXP as $key => $value) {
				$rest_exp	= $this->db->get_where('warehouse_stock_expired', array('id_material'=>$value['id'],'id_gudang'=>$id_tujuan,'expired'=>$value['expired']))->result();

				$QTY_GOOD = $value['qty_good'];

				if(!empty($rest_exp)){
					$ArrUpdExp[$key]['id'] 			= $rest_exp[0]->id;
					$ArrUpdExp[$key]['qty_stock'] 	= $rest_exp[0]->qty_stock + $QTY_GOOD;
					$ArrUpdExp[$key]['update_by'] 	= $UserName;
					$ArrUpdExp[$key]['update_date'] = $DateTime;

					$ArrUpdExpHist[$key]['id_material'] 	= $rest_exp[0]->id_material;
					$ArrUpdExpHist[$key]['nm_material'] 	= $rest_exp[0]->nm_material;;
					$ArrUpdExpHist[$key]['id_gudang'] 		= $id_tujuan;
					$ArrUpdExpHist[$key]['expired'] 		= $value['expired'];
					$ArrUpdExpHist[$key]['qty_stock'] 		= $rest_exp[0]->qty_stock;
					$ArrUpdExpHist[$key]['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist[$key]['qty_stock_akhir'] = $rest_exp[0]->qty_stock + $QTY_GOOD;
					$ArrUpdExpHist[$key]['qty_rusak_akhir'] = $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist[$key]['kode_trans'] 		= $kode_trans;
					$ArrUpdExpHist[$key]['update_by'] 		= $UserName;
					$ArrUpdExpHist[$key]['update_date'] 	= $DateTime;
				}
				else{
					$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$value['id']."' LIMIT 1 ";
					$restMat	= $this->db->query($sqlMat)->result();

					$ArrInsExp[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExp[$key]['nm_material'] 	= $restMat[0]->nm_material;;
					$ArrInsExp[$key]['id_gudang'] 		= $id_tujuan;
					$ArrInsExp[$key]['expired'] 		= $value['expired'];
					$ArrInsExp[$key]['qty_stock'] 		= $QTY_GOOD;
					$ArrInsExp[$key]['qty_rusak'] 		= 0;
					$ArrInsExp[$key]['update_by'] 		= $UserName;
					$ArrInsExp[$key]['update_date'] 	= $DateTime;

					$ArrInsExpHist[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExpHist[$key]['nm_material'] 	= $restMat[0]->nm_material;;
					$ArrInsExpHist[$key]['id_gudang'] 		= $id_tujuan;
					$ArrInsExpHist[$key]['expired'] 		= $value['expired'];
					$ArrInsExpHist[$key]['qty_stock'] 		= 0;
					$ArrInsExpHist[$key]['qty_rusak'] 		= 0;
					$ArrInsExpHist[$key]['qty_stock_akhir'] = $QTY_GOOD;
					$ArrInsExpHist[$key]['qty_rusak_akhir'] = 0;
					$ArrInsExpHist[$key]['kode_trans'] 		= $kode_trans;
					$ArrInsExpHist[$key]['update_by'] 		= $UserName;
					$ArrInsExpHist[$key]['update_date'] 	= $DateTime;
				}
			}

			$ArrUpdExp2		 = array();
			$ArrInsExp2		 = array();
			$ArrUpdExpHist2		 = array();
			$ArrInsExpHist2		 = array();
			// PENGURANGAN GUDANG
			foreach ($grouping_tempEXP as $key => $value) {
				$rest_exp	= $this->db->get_where('warehouse_stock_expired', array('id_material'=>$value['id'],'id_gudang'=>$id_gudang_dari,'expired'=>$value['expired']))->result();

				$QTY_GOOD = $value['qty_good'];

				if(!empty($rest_exp)){
					$ArrUpdExp2[$key]['id'] 			= $rest_exp[0]->id;
					$ArrUpdExp2[$key]['qty_stock'] 	    = $rest_exp[0]->qty_stock - $QTY_GOOD;
					$ArrUpdExp2[$key]['update_by'] 	    = $UserName;
					$ArrUpdExp2[$key]['update_date']    = $DateTime;

					$ArrUpdExpHist2[$key]['id_material'] 	= $rest_exp[0]->id_material;
					$ArrUpdExpHist2[$key]['nm_material'] 	= $rest_exp[0]->nm_material;;
					$ArrUpdExpHist2[$key]['id_gudang'] 		= $id_gudang_dari;
					$ArrUpdExpHist2[$key]['expired'] 		= $value['expired'];
					$ArrUpdExpHist2[$key]['qty_stock'] 		= $rest_exp[0]->qty_stock;
					$ArrUpdExpHist2[$key]['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist2[$key]['qty_stock_akhir']    = $rest_exp[0]->qty_stock - $QTY_GOOD;
					$ArrUpdExpHist2[$key]['qty_rusak_akhir']    = $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist2[$key]['kode_trans'] 		= $kode_trans;
					$ArrUpdExpHist2[$key]['update_by'] 		    = $UserName;
					$ArrUpdExpHist2[$key]['update_date'] 	    = $DateTime;
				}
				else{
					$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$value['id']."' LIMIT 1 ";
					$restMat	= $this->db->query($sqlMat)->result();

					$ArrInsExp2[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExp2[$key]['nm_material'] 	= $restMat[0]->nm_material;;
					$ArrInsExp2[$key]['id_gudang'] 		= $id_gudang_dari;
					$ArrInsExp2[$key]['expired'] 		= $value['expired'];
					$ArrInsExp2[$key]['qty_stock'] 		= 0 - $QTY_GOOD;
					$ArrInsExp2[$key]['qty_rusak'] 		= 0;
					$ArrInsExp2[$key]['update_by'] 		= $UserName;
					$ArrInsExp2[$key]['update_date'] 	= $DateTime;

					$ArrInsExpHist2[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExpHist2[$key]['nm_material'] 	= $restMat[0]->nm_material;;
					$ArrInsExpHist2[$key]['id_gudang'] 		= $id_gudang_dari;
					$ArrInsExpHist2[$key]['expired'] 		= $value['expired'];
					$ArrInsExpHist2[$key]['qty_stock'] 		= 0;
					$ArrInsExpHist2[$key]['qty_rusak'] 		= 0;
					$ArrInsExpHist2[$key]['qty_stock_akhir'] = 0 - $QTY_GOOD;
					$ArrInsExpHist2[$key]['qty_rusak_akhir'] = 0;
					$ArrInsExpHist2[$key]['kode_trans'] 		= $kode_trans;
					$ArrInsExpHist2[$key]['update_by'] 		= $UserName;
					$ArrInsExpHist2[$key]['update_date'] 	= $DateTime;
				}
			}

			//UPDATE NOMOR SURAT JALAN
			$monthYear 		= date('/m/Y');
			$kode_gudang 	= get_name('warehouse', 'kode', 'id', $id_gudang_dari);

			$qIPP			= "SELECT MAX(no_surat_jalan) as maxP FROM warehouse_adjustment WHERE no_surat_jalan LIKE '%/IA".$kode_gudang.$monthYear."' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 0, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$no_surat_jalan	= $urut2."/IA".$kode_gudang.$monthYear;

			$ArrUpdate2 = array(
				'checked' 			=> 'Y',
				'jumlah_mat_check' 	=> $getHeaderAdjust[0]->jumlah_mat_check + $SUM_MAT,
				'no_surat_jalan' 	=> $no_surat_jalan,
				'checked_by'		=> $UserName,
				'checked_date'		=> $DateTime
			);

			$ArrUpdate = array_merge($ArrEndChange,$ArrUpdate2);

			// print_r($ArrUpdate);
			// print_r($ArrDeatil);
			// print_r($ArrDeatilAdj);

			// print_r($ArrStock);
			// print_r($ArrHist);
			// print_r($ArrStockNew);
			// print_r($ArrHistNew);

			// print_r($ArrStock2);
			// print_r($ArrHist2);
			// print_r($ArrStockNew2);
			// print_r($ArrHistNew2);

			// print_r($ArrUpdExp);
			// print_r($ArrUpdExpHist);
			// print_r($ArrInsExp);
			// print_r($ArrInsExpHist);

			// print_r($ArrUpdExp2);
			// print_r($ArrUpdExpHist2);
			// print_r($ArrInsExp2);
			// print_r($ArrInsExpHist2);
			// exit;

			$type_gudang 	= get_name('warehouse', 'category', 'id', $id_gudang_dari);

			$this->db->trans_start();

				$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew);
				
				$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew2);

				if($type_gudang == 'pusat'){
					insert_jurnal($grouping_temp,$id_gudang_dari,$id_tujuan,$kode_trans,'transfer pusat - subgudang','pengurangan gudang pusat','penambahan subgudang');
				}
				else{
					insert_jurnal($grouping_temp,$id_gudang_dari,$id_tujuan,$kode_trans,'transfer subgudang - produksi','pengurangan subgudang','penambahan gudang produksi');
				}
				
				$this->db->where('kode_trans', $kode_trans);
				$this->db->update('warehouse_adjustment', $ArrUpdate);

				$this->db->update_batch('warehouse_adjustment_detail', $ArrDeatil, 'id');
				$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilAdj);

				if(!empty($ArrUpdateRequest)){
					$this->db->update_batch('planning_detail_spk', $ArrUpdateRequest, 'id');
				}

				if(!empty($ArrUpdateSTockBook)){
					$this->db->update_batch('warehouse_stock', $ArrUpdateSTockBook, 'id');
					$this->db->insert_batch('warehouse_history', $ArrUpdateHist);
				}

				if(!empty($ArrStock)){
					$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
					$this->db->insert_batch('warehouse_history', $ArrHist);
				}
				if(!empty($ArrStockNew)){
					$this->db->insert_batch('warehouse_stock', $ArrStockNew);
					$this->db->insert_batch('warehouse_history', $ArrHistNew);
				}
				if(!empty($ArrUpdExp)){
					$this->db->update_batch('warehouse_stock_expired', $ArrUpdExp, 'id');
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrUpdExpHist);
				}
				if(!empty($ArrInsExp)){
					$this->db->insert_batch('warehouse_stock_expired', $ArrInsExp);
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrInsExpHist);
				}



				if(!empty($ArrStock2)){
					$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
					$this->db->insert_batch('warehouse_history', $ArrHist2);
				}
				if(!empty($ArrStockNew2)){
					$this->db->insert_batch('warehouse_stock', $ArrStockNew2);
					$this->db->insert_batch('warehouse_history', $ArrHistNew2);
				}
				if(!empty($ArrUpdExp2)){
					$this->db->update_batch('warehouse_stock_expired', $ArrUpdExp2, 'id');
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrUpdExpHist2);
				}
				if(!empty($ArrInsExp2)){
					$this->db->insert_batch('warehouse_stock_expired', $ArrInsExp2);
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrInsExpHist2);
				}

			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);

				$getDetailTrans = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result_array();
				$no_ipp = (!empty($getDetailTrans[0]['no_ipp']))?$getDetailTrans[0]['no_ipp']:0;
				$no_spk = (!empty($getDetailTrans[0]['no_spk']))?$getDetailTrans[0]['no_spk']:0;
				
				$tandaTanki = substr($no_spk,0,3);
				$getProduct = $this->db->limit(1)->get_where('production_detail',array('no_spk'=>$no_spk))->result_array();
				$product 	= (!empty($getProduct[0]['id_category']))?$getProduct[0]['id_category']:null;
				if($tandaTanki == '90T'){
					$product 	= (!empty($getProduct[0]['id_product']))?$getProduct[0]['id_product']:null;
				}

				insertDataGroupReport($ArrUpdateStock, $id_gudang_dari, $id_tujuan, $kode_trans, $no_ipp, $no_spk, $product);
				history("Checking material request subgudang : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$kode_trans     = $this->uri->segment(3);

			$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
			$result_header		= $this->db->query($sql_header)->result();

			$sql 		= "	SELECT
								a.*,
								b.qty_stock AS stock,
								c.nm_material AS nm_material_stock,
								c.id_category AS id_category2,
								c.id_packing,
								c.nilai_konversi AS konversi
							FROM
								warehouse_adjustment_detail a
								LEFT JOIN warehouse_stock b ON a.id_material = b.id_material
								LEFT JOIN raw_materials c ON a.id_material = c.id_material
							WHERE
								a.kode_trans='".$kode_trans."'
								AND b.id_gudang = '".$result_header[0]->id_gudang_dari."'
							";
			$result		= $this->db->query($sql)->result_array();



			$data = array(
				'result' 		=> $result,
				'checked' 		=> $result_header[0]->checked,
				'category_req' 	=> $result_header[0]->category,
				'gudang_before' => $result_header[0]->id_gudang_dari,
				'gudang_after' 	=> $result_header[0]->id_gudang_ke,
				'kode_trans'	=> $result_header[0]->kode_trans,
				'no_ipp'		=> $result_header[0]->no_ipp,
				'dated' 		=> date('ymdhis', strtotime($result_header[0]->created_date)),
				'resv' 			=> date('d F Y', strtotime($result_header[0]->created_date))

			);

			$this->load->view('Warehouse/modal_request_check', $data);
		}
	}
	
	/*public function modal_request_check(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;

			$detail			= $data['detail'];
			$kode_trans		= $data['kode_trans'];

			$id_gudang_dari	    = $data['gudang_before'];
			
			// print_r($id_gudang_dari);
			// exit;
			
			$kode_gudang_dari 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
			
			$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->row();
			$coa_gudang = $coa_1->coa_1;

			$id_tujuan	    = $data['gudang_after'];
			$Ym 			= date('ym');

			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;

			$getHeaderAdjust 		= $this->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();

			$ArrDeatil		 	= array();
			$ArrDeatilAdj		= array();
			$ArrUpdateStock		= array();
			$ArrUpdateStockExp	= array();
			$ArrUpdateRequest	= array();
			$ArrJurnalNew		= array();
			 
			


			$SUM_MAT = 0;
			foreach($detail AS $val2 => $valx2){
				$QTY_OKE 	= 0;

				foreach($valx2['detail'] AS $val => $valx){
					$QTY_OKE 	+= str_replace(',','',$valx['qty_oke']);

					if(str_replace(',','',$valx['qty_oke']) > 0){
						$EXPIRED = (!empty($valx['expired']))?$valx['expired']:NULL;

						$ID_MATERIAL_ACT = $valx2['id_material'];

						//UPDATE ADJUSTMENT DETAIL
						$ArrDeatil[$val2]['id'] 			    = $valx2['id'];
						$ArrDeatil[$val2]['id_material'] 		= $ID_MATERIAL_ACT;
						$ArrDeatil[$val2]['check_qty_oke'] 		= $QTY_OKE;
						$ArrDeatil[$val2]['check_keterangan']	= strtolower($valx['check_keterangan']);
						$ArrDeatil[$val2]['update_by'] 			= $UserName;
						$ArrDeatil[$val2]['update_date'] 		= $DateTime;

						$getDetAjust 	= $this->db->get_where('warehouse_adjustment_detail', array('id'=>$valx2['id']))->result();
						$getDetMat 		= $this->db->get_where('raw_materials', array('id_material'=>$ID_MATERIAL_ACT))->result();
						//INSERT ADJUSTMENT CHECK
						// $ArrDeatilAdj[$val2.$val]['no_ipp'] 		= $getDetAjust[0]->no_ipp;
						$ArrDeatilAdj[$val2.$val]['id_detail'] 		= $valx2['id'];
						$ArrDeatilAdj[$val2.$val]['kode_trans'] 	= $kode_trans;
						$ArrDeatilAdj[$val2.$val]['id_material'] 	= $getDetMat[0]->id_material;
						$ArrDeatilAdj[$val2.$val]['nm_material'] 	= $getDetMat[0]->nm_material;
						$ArrDeatilAdj[$val2.$val]['id_category'] 	= $getDetMat[0]->id_category;
						$ArrDeatilAdj[$val2.$val]['nm_category'] 	= $getDetMat[0]->nm_category;
						$ArrDeatilAdj[$val2.$val]['qty_order'] 		= $getDetAjust[0]->qty_order;
						$ArrDeatilAdj[$val2.$val]['qty_oke'] 		= $QTY_OKE;
						$ArrDeatilAdj[$val2.$val]['qty_rusak'] 		= 0;
						$ArrDeatilAdj[$val2.$val]['expired_date'] 	= $EXPIRED;
						$ArrDeatilAdj[$val2.$val]['keterangan'] 	= strtolower($valx['check_keterangan']);
						$ArrDeatilAdj[$val2.$val]['update_by'] 		= $UserName;
						$ArrDeatilAdj[$val2.$val]['update_date'] 	= $DateTime;
						
						

						//MATERIAL YANG AKAN DI UPDATE
						$ArrUpdateStock[$val2]['id'] 	= $ID_MATERIAL_ACT;
						$ArrUpdateStock[$val2]['qty'] 	= $QTY_OKE;

						//MATERIAL YANG AKAN DI UPDATE EXPIRED
						if(!empty($EXPIRED)){
							$ArrUpdateStockExp[$val2]['id'] 		= $ID_MATERIAL_ACT;
							$ArrUpdateStockExp[$val2]['qty_good'] 	= $QTY_OKE;
							$ArrUpdateStockExp[$val2]['expired'] 	= $EXPIRED;
						}

						//UPDATE TEQUEST MATERIAL
						$getRequest = $this->db->get_where('planning_detail_spk', array('id_milik'=>$getDetAjust[0]->id_po_detail,'no_ipp'=>$getHeaderAdjust[0]->no_ipp,'id_material'=>$getDetAjust[0]->id_material))->result();
						if(!empty($getRequest)){
							$ArrUpdateRequest[$val2.$val]['id'] 			= $getRequest[0]->id;
							$ArrUpdateRequest[$val2.$val]['total_aktual'] 	= $getRequest[0]->total_aktual + $QTY_OKE;
							$ArrUpdateRequest[$val2.$val]['total_request'] 	= $getRequest[0]->total_request - ($getDetAjust[0]->qty_oke - $QTY_OKE);
						}
					}
				

                 $key = $getDetMat[0]->id_material;				
					
			    $stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				
				$GudangFrom = $id_gudang_dari;
				if($GudangFrom == '2'){
					$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book',array('id_material'=>$key))->result();
					$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
				}elseif($GudangFrom == '3'){
					$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
					$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
				}elseif($GudangFrom == '30'){
					$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_produksi',array('id_material'=>$key))->result();
					$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					
				}
				
				
				$ArrJurnalNew[$val2]['id_material'] 		= $getDetMat[0]->id_material;
				$ArrJurnalNew[$val2]['idmaterial'] 			= $getDetMat[0]->idmaterial;
				$ArrJurnalNew[$val2]['nm_material'] 		= $getDetMat[0]->nm_material;
				$ArrJurnalNew[$val2]['id_category'] 		= $getDetMat[0]->id_category;
				$ArrJurnalNew[$val2]['nm_category'] 		= $getDetMat[0]->nm_category;
				$ArrJurnalNew[$val2]['id_gudang'] 			= $id_gudang_dari;
				$ArrJurnalNew[$val2]['kd_gudang'] 			= $kode_gudang_dari;
				$ArrJurnalNew[$val2]['id_gudang_dari'] 	    = $id_gudang_dari;
				$ArrJurnalNew[$val2]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
				$ArrJurnalNew[$val2]['id_gudang_ke'] 		= $id_tujuan;
				$ArrJurnalNew[$val2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew[$val2]['qty_stock_awal'] 		= $stokjurnalakhir;
				$ArrJurnalNew[$val2]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
				$ArrJurnalNew[$val2]['kode_trans'] 			= $kode_trans;
				$ArrJurnalNew[$val2]['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew[$val2]['qty_out'] 			= $QTY_OKE;
				$ArrJurnalNew[$val2]['ket'] 				= 'pindah gudang';
				$ArrJurnalNew[$val2]['harga'] 			= $PRICE;
				$ArrJurnalNew[$val2]['harga_bm'] 		= 0;
				$ArrJurnalNew[$val2]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$val2]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
				$ArrJurnalNew[$val2]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
				$ArrJurnalNew[$val2]['update_by'] 		= $UserName;
				$ArrJurnalNew[$val2]['update_date'] 		= $DateTime;
				$ArrJurnalNew[$val2]['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew[$val2]['coa_gudang'] 		= $coa_gudang;
				
				
				$stokjurnalakhir2=0;
				$nilaijurnalakhir2=0;
				$stok_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key),1)->row();
				if(!empty($stok_jurnal_akhir2)) $stokjurnalakhir2=$stok_jurnal_akhir2->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir2)) $nilaijurnalakhir2=$stok_jurnal_akhir2->nilai_akhir_rp;
				
				
				$GudangFrom2 = $id_tujuan;
				if($GudangFrom2 == '2'){
					$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book',array('id_material'=>$key))->result();
					$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
				}elseif($GudangFrom2 == '3'){
					$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
					$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
				}elseif($GudangFrom2 == '30'){
					$get_price_book = $this->db->order_by('tgl_trans','desc')->get_where('price_book_produksi',array('id_material'=>$key))->result();
					$PRICE2 = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					
				}
				
				
				$PRICENEW = (($PRICE*$QTY_OKE) + ($PRICE2*$stokjurnalakhir2))/($QTY_OKE+$stokjurnalakhir2);
				
				
				$ArrJurnalNew2[$val2]['id_material'] 		= $getDetMat[0]->id_material;
				$ArrJurnalNew2[$val2]['idmaterial'] 		= $getDetMat[0]->idmaterial;
				$ArrJurnalNew2[$val2]['nm_material'] 		= $getDetMat[0]->nm_material;
				$ArrJurnalNew2[$val2]['id_category'] 		= $getDetMat[0]->id_category;
				$ArrJurnalNew2[$val2]['nm_category'] 		= $getDetMat[0]->nm_category;
				$ArrJurnalNew2[$val2]['id_gudang'] 			= $id_tujuan;
				$ArrJurnalNew2[$val2]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew2[$val2]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrJurnalNew2[$val2]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
				$ArrJurnalNew2[$val2]['id_gudang_ke'] 		= $id_tujuan;
				$ArrJurnalNew2[$val2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew2[$val2]['qty_stock_awal'] 	= $stokjurnalakhir2;
				$ArrJurnalNew2[$val2]['qty_stock_akhir'] 	= $stokjurnalakhir2+$QTY_OKE;
				$ArrJurnalNew2[$val2]['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew2[$val2]['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew2[$val2]['qty_in'] 			= $QTY_OKE;
				$ArrJurnalNew2[$val2]['ket'] 				= 'pindah gudang pusat - subgudang';
				$ArrJurnalNew2[$val2]['harga'] 				= $PRICENEW;
				$ArrJurnalNew2[$val2]['harga_bm'] 			= 0;
				$ArrJurnalNew2[$val2]['nilai_awal_rp']		= $nilaijurnalakhir2;
				$ArrJurnalNew2[$val2]['nilai_trans_rp']		= $PRICE*$QTY_OKE;
				$ArrJurnalNew2[$val2]['nilai_akhir_rp']		= ($stokjurnalakhir2+$QTY_OKE)*$PRICENEW;
				$ArrJurnalNew2[$val2]['update_by'] 			= $UserName;
				$ArrJurnalNew2[$val2]['update_date'] 		= $DateTime;
				$ArrJurnalNew2[$val2]['no_jurnal'] 			= '-';
				$ArrJurnalNew2[$val2]['coa_gudang'] 		= $coa_gudang;
				
				
				}
				$SUM_MAT 	+= $QTY_OKE;
			}

			//PROCESS UPDATE STOCK
			//grouping sum
			$temp = [];
			$grouping_temp = [];
			$key = 0;
			foreach($ArrUpdateStock as $value) { $key++;
				if($value['qty'] > 0){
					if(!array_key_exists($value['id'], $temp)) {
						$temp[$value['id']]['good'] = 0;
					}
					$temp[$value['id']]['good'] += $value['qty'];

					$grouping_temp[$value['id']]['id'] 			= $value['id'];
					$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
				}
			}

			move_warehouse($ArrUpdateStock,$id_gudang_dari,$id_tujuan,$kode_trans);

		//Mengurangi Booking
		$getDetailSPK 	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
		$no_ipp 		= (!empty($getDetailSPK[0]->no_ipp))?$getDetailSPK[0]->no_ipp:0;

		$id_gudang_booking = 2;
		$GETS_STOCK = get_warehouseStockAllMaterial();
		$CHECK_BOOK = get_CheckBooking($no_ipp);
		$ArrUpdateSTockBook 		= [];
		$ArrUpdateHist 	= [];
		// print_r($temp);
		if($CHECK_BOOK === TRUE AND $no_ipp != 0){
			// echo 'Masuk';
			foreach ($temp as $material => $qty) {
				$KEY 		= $material.'-'.$id_gudang_booking;
				$booking 	= (!empty($GETS_STOCK[$KEY]['booking']))?$GETS_STOCK[$KEY]['booking']:0;
				$stock 		= (!empty($GETS_STOCK[$KEY]['stock']))?$GETS_STOCK[$KEY]['stock']:0;
				$rusak 		= (!empty($GETS_STOCK[$KEY]['rusak']))?$GETS_STOCK[$KEY]['rusak']:0;
				$id_stock 	= (!empty($GETS_STOCK[$KEY]['id']))?$GETS_STOCK[$KEY]['id']:null;
				$idmaterial 	= (!empty($GETS_STOCK[$KEY]['idmaterial']))?$GETS_STOCK[$KEY]['idmaterial']:null;
				$nm_material 	= (!empty($GETS_STOCK[$KEY]['nm_material']))?$GETS_STOCK[$KEY]['nm_material']:null;
				$id_category 	= (!empty($GETS_STOCK[$KEY]['id_category']))?$GETS_STOCK[$KEY]['id_category']:null;
				$nm_category 	= (!empty($GETS_STOCK[$KEY]['nm_category']))?$GETS_STOCK[$KEY]['nm_category']:null;

				$qtyInt = $qty['good'];
				echo 'ID:'.$id_stock;
				if(!empty($id_stock)){
					$ArrUpdateSTockBook[$material]['id'] = $id_stock;
					$ArrUpdateSTockBook[$material]['qty_booking'] = $booking - $qtyInt;

					$ArrUpdateHist[$material]['id_material'] 	= $material;
					$ArrUpdateHist[$material]['idmaterial'] 	= $idmaterial;
					$ArrUpdateHist[$material]['nm_material'] 	= $nm_material;
					$ArrUpdateHist[$material]['id_category'] 	= $id_category;
					$ArrUpdateHist[$material]['nm_category'] 	= $nm_category;
					$ArrUpdateHist[$material]['id_gudang'] 		= $id_gudang_booking;
					$ArrUpdateHist[$material]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_booking);
					$ArrUpdateHist[$material]['id_gudang_dari'] = NULL;
					$ArrUpdateHist[$material]['kd_gudang_dari'] = 'BOOKING';
					$ArrUpdateHist[$material]['id_gudang_ke'] 	= NULL;
					$ArrUpdateHist[$material]['kd_gudang_ke'] 	= 'PENGURANG';
					$ArrUpdateHist[$material]['qty_stock_awal'] 	= $stock;
					$ArrUpdateHist[$material]['qty_stock_akhir'] 	= $stock;
					$ArrUpdateHist[$material]['qty_booking_awal'] 	= $booking;
					$ArrUpdateHist[$material]['qty_booking_akhir'] 	= $booking - $qtyInt;
					$ArrUpdateHist[$material]['qty_rusak_awal'] 	= $rusak;
					$ArrUpdateHist[$material]['qty_rusak_akhir'] 	= $rusak;
					$ArrUpdateHist[$material]['no_ipp'] 			= $no_ipp;
					$ArrUpdateHist[$material]['jumlah_mat'] 		= $qtyInt;
					$ArrUpdateHist[$material]['ket'] 				= 'pengurangan booking '.$kode_trans;
					$ArrUpdateHist[$material]['update_by'] 			= $UserName;
					$ArrUpdateHist[$material]['update_date'] 		= $DateTime;
					
					
				
				}
			}
		}

		// print_r($ArrUpdate);
		// print_r($ArrUpdateHist);
		// exit;

		//ENd Mengurangi Booking


			//PROCESS UPDATE STOCK EXPIRED
			//grouping sum
			$tempEXP = [];
			$grouping_tempEXP = [];
			$key = 0;
			// print_r($ArrUpdateStockExp);
			// exit;
			foreach($ArrUpdateStockExp as $value) { $key++;
				//grouping good
				if(!array_key_exists($value['id'].$value['expired'], $tempEXP)) {
					$tempEXP[$value['id'].$value['expired']]['good'] = 0;
				}
				$tempEXP[$value['id'].$value['expired']]['good'] += $value['qty_good'];

				$grouping_tempEXP[$value['id'].$value['expired']]['id'] 		= $value['id'];
				$grouping_tempEXP[$value['id'].$value['expired']]['qty_good'] 	= $tempEXP[$value['id'].$value['expired']]['good'];
				$grouping_tempEXP[$value['id'].$value['expired']]['expired'] 	= $value['expired'];
			}

			$ArrUpdExp		 = array();
			$ArrInsExp		 = array();
			$ArrUpdExpHist		 = array();
			$ArrInsExpHist		 = array();
			// PENAMBAHAN GUDANG
			foreach ($grouping_tempEXP as $key => $value) {
				$rest_exp	= $this->db->get_where('warehouse_stock_expired', array('id_material'=>$value['id'],'id_gudang'=>$id_tujuan,'expired'=>$value['expired']))->result();

				$QTY_GOOD = $value['qty_good'];

				if(!empty($rest_exp)){
					$ArrUpdExp[$key]['id'] 			= $rest_exp[0]->id;
					$ArrUpdExp[$key]['qty_stock'] 	= $rest_exp[0]->qty_stock + $QTY_GOOD;
					$ArrUpdExp[$key]['update_by'] 	= $UserName;
					$ArrUpdExp[$key]['update_date'] = $DateTime;

					$ArrUpdExpHist[$key]['id_material'] 	= $rest_exp[0]->id_material;
					$ArrUpdExpHist[$key]['nm_material'] 	= $rest_exp[0]->nm_material;;
					$ArrUpdExpHist[$key]['id_gudang'] 		= $id_tujuan;
					$ArrUpdExpHist[$key]['expired'] 		= $value['expired'];
					$ArrUpdExpHist[$key]['qty_stock'] 		= $rest_exp[0]->qty_stock;
					$ArrUpdExpHist[$key]['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist[$key]['qty_stock_akhir'] = $rest_exp[0]->qty_stock + $QTY_GOOD;
					$ArrUpdExpHist[$key]['qty_rusak_akhir'] = $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist[$key]['kode_trans'] 		= $kode_trans;
					$ArrUpdExpHist[$key]['update_by'] 		= $UserName;
					$ArrUpdExpHist[$key]['update_date'] 	= $DateTime;
				}
				else{
					$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$value['id']."' LIMIT 1 ";
					$restMat	= $this->db->query($sqlMat)->result();

					$ArrInsExp[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExp[$key]['nm_material'] 	= $restMat[0]->nm_material;;
					$ArrInsExp[$key]['id_gudang'] 		= $id_tujuan;
					$ArrInsExp[$key]['expired'] 		= $value['expired'];
					$ArrInsExp[$key]['qty_stock'] 		= $QTY_GOOD;
					$ArrInsExp[$key]['qty_rusak'] 		= 0;
					$ArrInsExp[$key]['update_by'] 		= $UserName;
					$ArrInsExp[$key]['update_date'] 	= $DateTime;

					$ArrInsExpHist[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExpHist[$key]['nm_material'] 	= $restMat[0]->nm_material;;
					$ArrInsExpHist[$key]['id_gudang'] 		= $id_tujuan;
					$ArrInsExpHist[$key]['expired'] 		= $value['expired'];
					$ArrInsExpHist[$key]['qty_stock'] 		= 0;
					$ArrInsExpHist[$key]['qty_rusak'] 		= 0;
					$ArrInsExpHist[$key]['qty_stock_akhir'] = $QTY_GOOD;
					$ArrInsExpHist[$key]['qty_rusak_akhir'] = 0;
					$ArrInsExpHist[$key]['kode_trans'] 		= $kode_trans;
					$ArrInsExpHist[$key]['update_by'] 		= $UserName;
					$ArrInsExpHist[$key]['update_date'] 	= $DateTime;
				}
			}

			$ArrUpdExp2		 = array();
			$ArrInsExp2		 = array();
			$ArrUpdExpHist2		 = array();
			$ArrInsExpHist2		 = array();
			// PENGURANGAN GUDANG
			foreach ($grouping_tempEXP as $key => $value) {
				$rest_exp	= $this->db->get_where('warehouse_stock_expired', array('id_material'=>$value['id'],'id_gudang'=>$id_gudang_dari,'expired'=>$value['expired']))->result();

				$QTY_GOOD = $value['qty_good'];

				if(!empty($rest_exp)){
					$ArrUpdExp2[$key]['id'] 			= $rest_exp[0]->id;
					$ArrUpdExp2[$key]['qty_stock'] 	    = $rest_exp[0]->qty_stock - $QTY_GOOD;
					$ArrUpdExp2[$key]['update_by'] 	    = $UserName;
					$ArrUpdExp2[$key]['update_date']    = $DateTime;

					$ArrUpdExpHist2[$key]['id_material'] 	= $rest_exp[0]->id_material;
					$ArrUpdExpHist2[$key]['nm_material'] 	= $rest_exp[0]->nm_material;;
					$ArrUpdExpHist2[$key]['id_gudang'] 		= $id_gudang_dari;
					$ArrUpdExpHist2[$key]['expired'] 		= $value['expired'];
					$ArrUpdExpHist2[$key]['qty_stock'] 		= $rest_exp[0]->qty_stock;
					$ArrUpdExpHist2[$key]['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist2[$key]['qty_stock_akhir']    = $rest_exp[0]->qty_stock - $QTY_GOOD;
					$ArrUpdExpHist2[$key]['qty_rusak_akhir']    = $rest_exp[0]->qty_rusak;
					$ArrUpdExpHist2[$key]['kode_trans'] 		= $kode_trans;
					$ArrUpdExpHist2[$key]['update_by'] 		    = $UserName;
					$ArrUpdExpHist2[$key]['update_date'] 	    = $DateTime;
				}
				else{
					$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$value['id']."' LIMIT 1 ";
					$restMat	= $this->db->query($sqlMat)->result();

					$ArrInsExp2[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExp2[$key]['nm_material'] 	= $restMat[0]->nm_material;;
					$ArrInsExp2[$key]['id_gudang'] 		= $id_gudang_dari;
					$ArrInsExp2[$key]['expired'] 		= $value['expired'];
					$ArrInsExp2[$key]['qty_stock'] 		= 0 - $QTY_GOOD;
					$ArrInsExp2[$key]['qty_rusak'] 		= 0;
					$ArrInsExp2[$key]['update_by'] 		= $UserName;
					$ArrInsExp2[$key]['update_date'] 	= $DateTime;

					$ArrInsExpHist2[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrInsExpHist2[$key]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrInsExpHist2[$key]['id_gudang'] 		= $id_gudang_dari;
					$ArrInsExpHist2[$key]['expired'] 		= $value['expired'];
					$ArrInsExpHist2[$key]['qty_stock'] 		= 0;
					$ArrInsExpHist2[$key]['qty_rusak'] 		= 0;
					$ArrInsExpHist2[$key]['qty_stock_akhir'] = 0 - $QTY_GOOD;
					$ArrInsExpHist2[$key]['qty_rusak_akhir'] = 0;
					$ArrInsExpHist2[$key]['kode_trans'] 		= $kode_trans;
					$ArrInsExpHist2[$key]['update_by'] 		= $UserName;
					$ArrInsExpHist2[$key]['update_date'] 	= $DateTime;
				}
			}

			//UPDATE NOMOR SURAT JALAN
			$monthYear 		= date('/m/Y');
			$kode_gudang 	= get_name('warehouse', 'kode', 'id', $id_gudang_dari);

			$qIPP			= "SELECT MAX(no_surat_jalan) as maxP FROM warehouse_adjustment WHERE no_surat_jalan LIKE '%/IA".$kode_gudang.$monthYear."' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 0, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$no_surat_jalan	= $urut2."/IA".$kode_gudang.$monthYear;

			$ArrUpdate = array(
				'checked' 			=> 'Y',
				'jumlah_mat_check' 	=> $getHeaderAdjust[0]->jumlah_mat_check + $SUM_MAT,
				'no_surat_jalan' 	=> $no_surat_jalan,
				'checked_by'		=> $UserName,
				'checked_date'		=> $DateTime
			);

			// print_r($ArrUpdate);
			// print_r($ArrDeatil);
			// print_r($ArrDeatilAdj);

			// print_r($ArrStock);
			// print_r($ArrHist);
			// print_r($ArrStockNew);
			// print_r($ArrHistNew);

			// print_r($ArrStock2);
			// print_r($ArrHist2);
			// print_r($ArrStockNew2);
			// print_r($ArrHistNew2);

			// print_r($ArrUpdExp);
			// print_r($ArrUpdExpHist);
			// print_r($ArrInsExp);
			// print_r($ArrInsExpHist);

			// print_r($ArrUpdExp2);
			// print_r($ArrUpdExpHist2);
			 // print_r($ArrJurnalNew);
			 // exit;
			

			$type_gudang 	= get_name('warehouse', 'category', 'id', $id_gudang_dari);

			$this->db->trans_start();
			
			    $this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew);
				
				$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew2);

				if($type_gudang == 'pusat'){
					insert_jurnal($grouping_temp,$id_gudang_dari,$id_tujuan,$kode_trans,'transfer pusat - subgudang','pengurangan gudang pusat','penambahan subgudang');
				}
				else{
					insert_jurnal($grouping_temp,$id_gudang_dari,$id_tujuan,$kode_trans,'transfer subgudang - produksi','pengurangan subgudang','penambahan gudang produksi');
				}
				
				
				$this->db->where('kode_trans', $kode_trans);
				$this->db->update('warehouse_adjustment', $ArrUpdate);

				$this->db->update_batch('warehouse_adjustment_detail', $ArrDeatil, 'id');
				$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilAdj);

				if(!empty($ArrUpdateRequest)){
					$this->db->update_batch('planning_detail_spk', $ArrUpdateRequest, 'id');
				}

				if(!empty($ArrUpdateSTockBook)){
					$this->db->update_batch('warehouse_stock', $ArrUpdateSTockBook, 'id');
					$this->db->insert_batch('warehouse_history', $ArrUpdateHist);
				}

				if(!empty($ArrStock)){
					$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
					$this->db->insert_batch('warehouse_history', $ArrHist);
				}
				if(!empty($ArrStockNew)){
					$this->db->insert_batch('warehouse_stock', $ArrStockNew);
					$this->db->insert_batch('warehouse_history', $ArrHistNew);
				}
				if(!empty($ArrUpdExp)){
					$this->db->update_batch('warehouse_stock_expired', $ArrUpdExp, 'id');
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrUpdExpHist);
				}
				if(!empty($ArrInsExp)){
					$this->db->insert_batch('warehouse_stock_expired', $ArrInsExp);
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrInsExpHist);
				}



				if(!empty($ArrStock2)){
					$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
					$this->db->insert_batch('warehouse_history', $ArrHist2);
				}
				if(!empty($ArrStockNew2)){
					$this->db->insert_batch('warehouse_stock', $ArrStockNew2);
					$this->db->insert_batch('warehouse_history', $ArrHistNew2);
				}
				if(!empty($ArrUpdExp2)){
					$this->db->update_batch('warehouse_stock_expired', $ArrUpdExp2, 'id');
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrUpdExpHist2);
				}
				if(!empty($ArrInsExp2)){
					$this->db->insert_batch('warehouse_stock_expired', $ArrInsExp2);
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrInsExpHist2);
				}
				
			

			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);
				history("Checking material request subgudang : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$kode_trans     = $this->uri->segment(3);

			$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
			$result_header		= $this->db->query($sql_header)->result();

			$sql 		= "	SELECT
								a.*,
								b.qty_stock AS stock,
								c.nm_material AS nm_material_stock,
								c.id_category AS id_category2
							FROM
								warehouse_adjustment_detail a
								LEFT JOIN warehouse_stock b ON a.id_material = b.id_material
								LEFT JOIN raw_materials c ON a.id_material = c.id_material
							WHERE
								a.kode_trans='".$kode_trans."'
								AND b.id_gudang = '".$result_header[0]->id_gudang_dari."'
							";
			$result		= $this->db->query($sql)->result_array();
			$data = array(
				'result' 		=> $result,
				'checked' 		=> $result_header[0]->checked,
				'category_req' 	=> $result_header[0]->category,
				'gudang_before' => $result_header[0]->id_gudang_dari,
				'gudang_after' 	=> $result_header[0]->id_gudang_ke,
				'kode_trans'	=> $result_header[0]->kode_trans,
				'no_ipp'		=> $result_header[0]->no_ipp,
				'dated' 		=> date('ymdhis', strtotime($result_header[0]->created_date)),
				'resv' 			=> date('d F Y', strtotime($result_header[0]->created_date))

			);

			
			$this->load->view('Warehouse/modal_request_check', $data);
		}
	}*/

	

	public function modal_request_check_before(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;

			$detail			= $data['detail'];
			$kode_trans		= $data['kode_trans'];
			$gudang_before	= $data['gudang_before'];
			$gudang_after	= $data['gudang_after'];
			$Ym 			= date('ym');
			// print_r($data);
			// exit;

			$ArrDeatil		 = array();
			$ArrStock		 = array();
			$ArrStockSup	 = array();
			$ArrStockSupIns	 = array();
			$ArrDeatilAdj	 = array();
			$ArrHist		 = array();
			$ArrHistSub		 = array();
			$ArrHistSubIns	 = array();
			$ArrUpdExp		 = array();
			$ArrInsExp		 = array();
			$ArrUpdExpHist		 = array();
			$ArrInsExpHist		 = array();
			$ArrUpdExp2		 = array();
			$ArrInsExp2		 = array();
			$ArrUpdExpHist2		 = array();
			$ArrInsExpHist2		 = array();
			$SUM_MAT = 0;
			foreach($detail AS $val2 => $valx2){
				$sudah_request 	= 0;
				$request_awal 	= str_replace(',','',$valx2['request_awal']);

				$rest_pusat	= $this->db->get_where('warehouse_stock', array('id_gudang'=>$gudang_before, 'id_material'=>$valx2['id_material']))->result();
				$det_mat 	= $this->db->get_where('warehouse_adjustment_detail', array('id'=>$valx2['id']))->result();

				foreach($valx2['detail'] AS $val => $valx){
					$sudah_request 	+= str_replace(',','',$valx['qty_oke']);
					if(str_replace(',','',$valx['qty_oke']) > 0){
						$SUM_MAT += $sudah_request;
						$expired = (!empty($valx['expired']))?$valx['expired']:NULL;

						//detail adjustment checked
						// $ArrDeatilAdj2[$val2.$val]['no_ipp'] 		= $rest_pusat[0]->no_ipp;
						$ArrDeatilAdj2[$val2.$val]['id_detail'] 	= $valx2['id'];
						$ArrDeatilAdj2[$val2.$val]['kode_trans'] 	= $kode_trans;
						$ArrDeatilAdj2[$val2.$val]['id_material'] 	= $rest_pusat[0]->id_material;
						$ArrDeatilAdj2[$val2.$val]['nm_material'] 	= $rest_pusat[0]->nm_material;
						$ArrDeatilAdj2[$val2.$val]['id_category'] 	= $rest_pusat[0]->id_category;
						$ArrDeatilAdj2[$val2.$val]['nm_category'] 	= $rest_pusat[0]->nm_category;
						$ArrDeatilAdj2[$val2.$val]['qty_order'] 	= $det_mat[0]->qty_order;
						$ArrDeatilAdj2[$val2.$val]['qty_oke'] 		= str_replace(',','',$valx['qty_oke']);
						$ArrDeatilAdj2[$val2.$val]['qty_rusak'] 	= 0;
						$ArrDeatilAdj2[$val2.$val]['expired_date'] 	= $expired;
						$ArrDeatilAdj2[$val2.$val]['keterangan'] 	= strtolower($valx['check_keterangan']);
						$ArrDeatilAdj2[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
						$ArrDeatilAdj2[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');

						//Update Warehouse Expired Minus
						$stock_exp	= "SELECT a.* FROM  warehouse_stock_expired a WHERE a.id_material = '".$rest_pusat[0]->id_material."' AND a.id_gudang='".$gudang_before."' AND a.expired='".$valx['expired']."'";
						$rest_exp	= $this->db->query($stock_exp)->result();

						//kurangi stock gudang dari
						if(!empty($rest_exp)){
							$ArrUpdExp[$val2.$val]['id'] 			= $rest_exp[0]->id;
							$ArrUpdExp[$val2.$val]['qty_stock'] 	= $rest_exp[0]->qty_stock - str_replace(',','',$valx['qty_oke']);
							$ArrUpdExp[$val2.$val]['qty_rusak'] 	= $rest_exp[0]->qty_rusak;
							$ArrUpdExp[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrUpdExp[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');

							$ArrUpdExpHist[$val2.$val]['id_material'] 	= $rest_exp[0]->id_material;
							$ArrUpdExpHist[$val2.$val]['nm_material'] 	= $rest_exp[0]->nm_material;;
							$ArrUpdExpHist[$val2.$val]['id_gudang'] 	= $gudang_before;
							$ArrUpdExpHist[$val2.$val]['expired'] 		= $expired;
							$ArrUpdExpHist[$val2.$val]['qty_stock'] 	= $rest_exp[0]->qty_stock;
							$ArrUpdExpHist[$val2.$val]['qty_rusak'] 	= $rest_exp[0]->qty_rusak;
							$ArrUpdExpHist[$val2.$val]['qty_stock_akhir'] 	= $rest_exp[0]->qty_stock - str_replace(',','',$valx['qty_oke']);
							$ArrUpdExpHist[$val2.$val]['qty_rusak_akhir'] 	= $rest_exp[0]->qty_rusak;
							$ArrUpdExpHist[$val2.$val]['kode_trans'] 	= $kode_trans;
							$ArrUpdExpHist[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrUpdExpHist[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');
						}
						if(empty($rest_exp)){
							$sql_mat	= "	SELECT a.* FROM raw_materials a WHERE a.id_material = '".$rest_pusat[0]->id_material."' LIMIT 1";
							$rest_mat	= $this->db->query($sql_mat)->result();

							$ArrInsExp[$val2.$val]['id_material'] 	= $rest_mat[0]->id_material;
							$ArrInsExp[$val2.$val]['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrInsExp[$val2.$val]['id_gudang'] 	= $gudang_before;
							$ArrInsExp[$val2.$val]['expired'] 		= $expired;
							$ArrInsExp[$val2.$val]['qty_stock'] 	= 0 - str_replace(',','',$valx['qty_oke']);
							$ArrInsExp[$val2.$val]['qty_rusak'] 	= 0;
							$ArrInsExp[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrInsExp[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');

							$ArrInsExpHist[$val2.$val]['id_material'] 	= $rest_mat[0]->id_material;
							$ArrInsExpHist[$val2.$val]['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrInsExpHist[$val2.$val]['id_gudang'] 	= $gudang_before;
							$ArrInsExpHist[$val2.$val]['expired'] 		= $expired;
							$ArrInsExpHist[$val2.$val]['qty_stock'] 	= 0;
							$ArrInsExpHist[$val2.$val]['qty_rusak'] 	= 0;
							$ArrInsExpHist[$val2.$val]['qty_stock_akhir'] 	= 0 - str_replace(',','',$valx['qty_oke']);
							$ArrInsExpHist[$val2.$val]['qty_rusak_akhir'] 	= 0;
							$ArrInsExpHist[$val2.$val]['kode_trans'] 	= $kode_trans;
							$ArrInsExpHist[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrInsExpHist[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');
						}

						//Update Warehouse Expired Plus
						$stock_exp	= "SELECT a.* FROM  warehouse_stock_expired a WHERE a.id_material = '".$rest_pusat[0]->id_material."' AND a.id_gudang='".$gudang_after."' AND a.expired='".$valx['expired']."'";
						$rest_exp	= $this->db->query($stock_exp)->result();

						//kurangi stock gudang tujuan
						if(!empty($rest_exp)){
							$ArrUpdExp2[$val2.$val]['id'] 			= $rest_exp[0]->id;
							$ArrUpdExp2[$val2.$val]['qty_stock'] 	= $rest_exp[0]->qty_stock + str_replace(',','',$valx['qty_oke']);
							$ArrUpdExp2[$val2.$val]['qty_rusak'] 	= $rest_exp[0]->qty_rusak;
							$ArrUpdExp2[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrUpdExp2[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');

							$ArrUpdExpHist2[$val2.$val]['id_material'] 	= $rest_exp[0]->id_material;
							$ArrUpdExpHist2[$val2.$val]['nm_material'] 	= $rest_exp[0]->nm_material;;
							$ArrUpdExpHist2[$val2.$val]['id_gudang'] 	= $gudang_after;
							$ArrUpdExpHist2[$val2.$val]['expired'] 		= $expired;
							$ArrUpdExpHist2[$val2.$val]['qty_stock'] 	= $rest_exp[0]->qty_stock;
							$ArrUpdExpHist2[$val2.$val]['qty_rusak'] 	= $rest_exp[0]->qty_rusak;
							$ArrUpdExpHist2[$val2.$val]['qty_stock_akhir'] 	= $rest_exp[0]->qty_stock + str_replace(',','',$valx['qty_oke']);
							$ArrUpdExpHist2[$val2.$val]['qty_rusak_akhir'] 	= $rest_exp[0]->qty_rusak;
							$ArrUpdExpHist2[$val2.$val]['kode_trans'] 	= $kode_trans;
							$ArrUpdExpHist2[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrUpdExpHist2[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');
						}
						if(empty($rest_exp)){
							$sql_mat	= "	SELECT a.* FROM raw_materials a WHERE a.id_material = '".$rest_pusat[0]->id_material."' LIMIT 1";
							$rest_mat	= $this->db->query($sql_mat)->result();

							$ArrInsExp2[$val2.$val]['id_material'] 	= $rest_mat[0]->id_material;
							$ArrInsExp2[$val2.$val]['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrInsExp2[$val2.$val]['id_gudang'] 	= $gudang_after;
							$ArrInsExp2[$val2.$val]['expired'] 		= $expired;
							$ArrInsExp2[$val2.$val]['qty_stock'] 	= str_replace(',','',$valx['qty_oke']);
							$ArrInsExp2[$val2.$val]['qty_rusak'] 	= 0;
							$ArrInsExp2[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrInsExp2[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');

							$ArrInsExpHist2[$val2.$val]['id_material'] 	= $rest_mat[0]->id_material;
							$ArrInsExpHist2[$val2.$val]['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrInsExpHist2[$val2.$val]['id_gudang'] 	= $gudang_after;
							$ArrInsExpHist2[$val2.$val]['expired'] 		= $expired;
							$ArrInsExpHist2[$val2.$val]['qty_stock'] 	= 0;
							$ArrInsExpHist2[$val2.$val]['qty_rusak'] 	= 0;
							$ArrInsExpHist2[$val2.$val]['qty_stock_akhir'] 	= str_replace(',','',$valx['qty_oke']);
							$ArrInsExpHist2[$val2.$val]['qty_rusak_akhir'] 	= 0;
							$ArrInsExpHist2[$val2.$val]['kode_trans'] 	= $kode_trans;
							$ArrInsExpHist2[$val2.$val]['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrInsExpHist2[$val2.$val]['update_date'] 	= date('Y-m-d H:i:s');
						}
					}
				}

				//STOCK UTAMA
				$ArrStock[$val2]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$val2]['qty_stock'] 		= $rest_pusat[0]->qty_stock - $sudah_request;
				$ArrStock[$val2]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStock[$val2]['update_date'] 	= date('Y-m-d H:i:s');

				$ArrHist[$val2]['id_material'] 	= $valx2['id_material'];
				$ArrHist[$val2]['idmaterial'] 		= $rest_pusat[0]->idmaterial;
				$ArrHist[$val2]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$val2]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$val2]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$val2]['id_gudang'] 		= $gudang_before;
				$ArrHist[$val2]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
				$ArrHist[$val2]['id_gudang_dari'] 	= $gudang_before;
				$ArrHist[$val2]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
				$ArrHist[$val2]['id_gudang_ke'] 	= $gudang_after;
				$ArrHist[$val2]['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $gudang_after);
				$ArrHist[$val2]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$val2]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $sudah_request;
				$ArrHist[$val2]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$val2]['qty_booking_akhir'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$val2]['qty_rusak_awal'] 		= $rest_pusat[0]->qty_rusak;
				$ArrHist[$val2]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$val2]['no_ipp'] 				= $kode_trans;
				$ArrHist[$val2]['jumlah_mat'] 			= $sudah_request;
				$ArrHist[$val2]['ket'] 				= 'pengurangan gudang';
				$ArrHist[$val2]['update_by'] 			= $data_session['ORI_User']['username'];
				$ArrHist[$val2]['update_date'] 		= date('Y-m-d H:i:s');


				$rest_subgudang	= $this->db->get_where('warehouse_stock', array('id_gudang'=>$gudang_after, 'id_material'=>$valx2['id_material']))->result();
				if(!empty($rest_subgudang)){
					//update stock sub gudang
					$ArrStockSup[$val2]['id'] 			= $rest_subgudang[0]->id;
					$ArrStockSup[$val2]['qty_stock'] 	= $rest_subgudang[0]->qty_stock + $sudah_request;
					$ArrStockSup[$val2]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStockSup[$val2]['update_date'] 	= date('Y-m-d H:i:s');

					$ArrHistSub[$val2]['id_material'] 		= $rest_subgudang[0]->id_material;
					$ArrHistSub[$val2]['idmaterial'] 		= $rest_subgudang[0]->idmaterial;
					$ArrHistSub[$val2]['nm_material'] 		= $rest_subgudang[0]->nm_material;
					$ArrHistSub[$val2]['id_category'] 		= $rest_subgudang[0]->id_category;
					$ArrHistSub[$val2]['nm_category'] 		= $rest_subgudang[0]->nm_category;
					$ArrHistSub[$val2]['id_gudang'] 			= $gudang_after;
					$ArrHistSub[$val2]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $gudang_after);
					$ArrHistSub[$val2]['id_gudang_dari'] 	= $gudang_before;
					$ArrHistSub[$val2]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
					$ArrHistSub[$val2]['id_gudang_ke'] 		= $gudang_after;
					$ArrHistSub[$val2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_after);
					$ArrHistSub[$val2]['qty_stock_awal'] 	= $rest_subgudang[0]->qty_stock;
					$ArrHistSub[$val2]['qty_stock_akhir'] 	= $rest_subgudang[0]->qty_stock + $sudah_request;
					$ArrHistSub[$val2]['qty_booking_awal'] 	= $rest_subgudang[0]->qty_booking;
					$ArrHistSub[$val2]['qty_booking_akhir'] 	= $rest_subgudang[0]->qty_booking;
					$ArrHistSub[$val2]['qty_rusak_awal'] 	= $rest_subgudang[0]->qty_rusak;
					$ArrHistSub[$val2]['qty_rusak_akhir'] 	= $rest_subgudang[0]->qty_rusak;
					$ArrHistSub[$val2]['no_ipp'] 			= $kode_trans;
					$ArrHistSub[$val2]['jumlah_mat'] 		= $sudah_request;
					$ArrHistSub[$val2]['ket'] 				= 'penambahan gudang';
					$ArrHistSub[$val2]['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistSub[$val2]['update_date'] 		= date('Y-m-d H:i:s');
				}

				if(empty($rest_subgudang)){
					$rest_mat	= $this->db->get_where('raw_materials', array('id_material'=>$valx2['id_material']))->result();
					//update stock sub gudang
					$ArrStockSupIns[$val2]['id_material'] 	= $rest_mat[0]->id_material;
					$ArrStockSupIns[$val2]['idmaterial'] 	= $rest_mat[0]->idmaterial;
					$ArrStockSupIns[$val2]['nm_material'] 	= $rest_mat[0]->nm_material;
					$ArrStockSupIns[$val2]['id_category'] 	= $rest_mat[0]->id_category;
					$ArrStockSupIns[$val2]['nm_category'] 	= $rest_mat[0]->nm_category;
					$ArrStockSupIns[$val2]['id_gudang'] 		= $gudang_after;
					$ArrStockSupIns[$val2]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_after);
					$ArrStockSupIns[$val2]['qty_stock'] 		= $sudah_request;
					$ArrStockSupIns[$val2]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockSupIns[$val2]['update_date'] 	= date('Y-m-d H:i:s');

					$ArrHistSubIns[$val2]['id_material'] 	= $rest_mat[0]->id_material;
					$ArrHistSubIns[$val2]['idmaterial'] 	= $rest_mat[0]->idmaterial;
					$ArrHistSubIns[$val2]['nm_material'] 	= $rest_mat[0]->nm_material;
					$ArrHistSubIns[$val2]['id_category'] 	= $rest_mat[0]->id_category;
					$ArrHistSubIns[$val2]['nm_category'] 	= $rest_mat[0]->nm_category;
					$ArrHistSubIns[$val2]['id_gudang'] 		= $gudang_after;
					$ArrHistSubIns[$val2]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_after);
					$ArrHistSubIns[$val2]['id_gudang_dari'] 	= $gudang_before;
					$ArrHistSubIns[$val2]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
					$ArrHistSubIns[$val2]['id_gudang_ke'] 	= $gudang_after;
					$ArrHistSubIns[$val2]['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $gudang_after);
					$ArrHistSubIns[$val2]['qty_stock_awal'] 	= 0;
					$ArrHistSubIns[$val2]['qty_stock_akhir'] 	= $sudah_request;
					$ArrHistSubIns[$val2]['qty_booking_awal'] 	= 0;
					$ArrHistSubIns[$val2]['qty_booking_akhir'] 	= 0;
					$ArrHistSubIns[$val2]['qty_rusak_awal'] 		= 0;
					$ArrHistSubIns[$val2]['qty_rusak_akhir'] 	= 0;
					$ArrHistSubIns[$val2]['no_ipp'] 				= $kode_trans;
					$ArrHistSubIns[$val2]['jumlah_mat'] 			= $sudah_request;
					$ArrHistSubIns[$val2]['ket'] 				= 'penambahan gudang (insert new)';
					$ArrHistSubIns[$val2]['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistSubIns[$val2]['update_date'] 		= date('Y-m-d H:i:s');
				}

				//detail adjustmeny
				$ArrDeatilAdj[$val2]['id'] 			= $valx2['id'];
				$ArrDeatilAdj[$val2]['id_material'] 	= $valx2['id_material'];
				$ArrDeatilAdj[$val2]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrDeatilAdj[$val2]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrDeatilAdj[$val2]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrDeatilAdj[$val2]['check_qty_oke'] 	= $sudah_request;
				$ArrDeatilAdj[$val2]['check_keterangan'] = '';
				$ArrDeatilAdj[$val2]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrDeatilAdj[$val2]['update_date'] 		= date('Y-m-d H:i:s');

			}


			$monthYear 		= date('/m/Y');
			$kode_gudang 	= get_name('warehouse', 'kode', 'id', $gudang_before);

			$qIPP			= "SELECT MAX(no_surat_jalan) as maxP FROM warehouse_adjustment WHERE no_surat_jalan LIKE '%/IA".$kode_gudang.$monthYear."' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 0, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$no_surat_jalan	= $urut2."/IA".$kode_gudang.$monthYear;

			$ArrUpdate = array(
				'checked' => 'Y',
				'jumlah_mat_check' => $SUM_MAT,
				'no_surat_jalan' => $no_surat_jalan,
				'checked_by' => $data_session['ORI_User']['username'],
				'checked_date' => date('Y-m-d H:i:s')
			);

			// print_r($ArrUpdate);
			// print_r($ArrDeatilAdj);
			// print_r($ArrDeatilAdj2);
			// print_r($ArrStock);
			// print_r($ArrHist);
			// print_r($ArrStockSup);
			// print_r($ArrHistSub);
			// print_r($ArrStockSupIns);
			// print_r($ArrHistSubIns);
			// print_r($ArrUpdExp);
			// print_r($ArrInsExp);
			// print_r($ArrUpdExpHist);
			// print_r($ArrInsExpHist);
			// print_r($ArrUpdExp2);
			// print_r($ArrInsExp2);
			// print_r($ArrUpdExpHist2);
			// print_r($ArrInsExpHist2);
			// exit;
			$this->db->trans_start();
				$this->db->where('kode_trans', $kode_trans);
				$this->db->update('warehouse_adjustment', $ArrUpdate);

				$this->db->update_batch('warehouse_adjustment_detail', $ArrDeatilAdj, 'id');
				$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilAdj2);

				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
				$this->db->insert_batch('warehouse_history', $ArrHist);

				if(!empty($ArrStockSup)){
					$this->db->update_batch('warehouse_stock', $ArrStockSup, 'id');
					$this->db->insert_batch('warehouse_history', $ArrHistSub);
				}
				if(!empty($ArrStockSupIns)){
					$this->db->insert_batch('warehouse_stock', $ArrStockSupIns);
					$this->db->insert_batch('warehouse_history', $ArrHistSubIns);
				}

				if(!empty($ArrUpdExp)){
					$this->db->update_batch('warehouse_stock_expired', $ArrUpdExp, 'id');
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrUpdExpHist);
				}
				if(!empty($ArrInsExp)){
					$this->db->insert_batch('warehouse_stock_expired', $ArrInsExp);
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrInsExpHist);
				}

				if(!empty($ArrUpdExp2)){
					$this->db->update_batch('warehouse_stock_expired', $ArrUpdExp2, 'id');
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrUpdExpHist2);
				}
				if(!empty($ArrInsExp2)){
					$this->db->insert_batch('warehouse_stock_expired', $ArrInsExp2);
					$this->db->insert_batch('warehouse_stock_expired_hist', $ArrInsExpHist2);
				}

			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);
				history("Checking material request subgudang : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$kode_trans     = $this->uri->segment(3);

			$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
			$result_header		= $this->db->query($sql_header)->result();

			$sql 		= "	SELECT
								a.*,
								b.qty_stock AS stock
							FROM
								warehouse_adjustment_detail a
								LEFT JOIN warehouse_stock b ON a.id_material = b.id_material
							WHERE
								a.kode_trans='".$kode_trans."'
								AND b.id_gudang = '".$result_header[0]->id_gudang_dari."'
							";
			$result		= $this->db->query($sql)->result_array();



			$data = array(
				'result' 		=> $result,
				'checked' 		=> $result_header[0]->checked,
				'gudang_before' => $result_header[0]->id_gudang_dari,
				'gudang_after' 	=> $result_header[0]->id_gudang_ke,
				'kode_trans'	=> $result_header[0]->kode_trans,
				'no_ipp'		=> $result_header[0]->no_ipp,
				'dated' 		=> date('ymdhis', strtotime($result_header[0]->created_date)),
				'resv' 			=> date('d F Y', strtotime($result_header[0]->created_date))

			);

			$this->load->view('Warehouse/modal_request_check', $data);
		}
	}

	public function get_list_exp(){
		$material = $this->uri->segment(3);
		$gudang = $this->uri->segment(4);

		$option = get_list_expired($material, $gudang);

		$data = array(
			'option' => $option
		);

		echo json_encode($data);
	}

	public function modal_request_edit(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;

			$detail			= $data['detail'];
			$kode_trans		= $data['kode_trans'];
			$Ym 			= date('ym');
			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;

			$ArrDeatil		 	= array();
			$ArrRequest		 	= array();
			$ArrRequestHist		= array();
			$SUM_MAT = 0;
			foreach ($detail as $key => $value) {
				$GET_DETAIL = $this->db->get_where('warehouse_adjustment_detail',array('id'=>$value['id']))->result();
				$kode_trans = $GET_DETAIL[0]->kode_trans;
				$id_milik 	= $GET_DETAIL[0]->id_po_detail;
				$id_material= $GET_DETAIL[0]->id_material_req;
				$GET_HEADER = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
				$no_ipp 	= $GET_HEADER[0]->no_ipp;
				$GET_REQUEST= $this->db->get_where('planning_detail_spk',array('no_ipp'=>$no_ipp,'id_milik'=>$id_milik,'id_material'=>$id_material))->result();

				$qty_awal 	= str_replace(',','',$value['edit_qty_before']);
				$qty_revisi = str_replace(',','',$value['edit_qty']);
				$SUM_MAT += $qty_revisi;
				$ArrDeatil[$key]['id'] 			= $value['id'];
				$ArrDeatil[$key]['qty_order'] 	= $qty_revisi;
				$ArrDeatil[$key]['qty_oke'] 	= $qty_revisi;
				$ArrDeatil[$key]['keterangan'] 	= $value['keterangan'];

				if(!empty($GET_REQUEST)){
					$REVISI_SISA = $GET_REQUEST[0]->total_request - $qty_awal + $qty_revisi;
					$ArrRequest[$key]['id'] 			= $GET_REQUEST[0]->id;
					$ArrRequest[$key]['total_request'] 	= $REVISI_SISA;
					$ArrRequest[$key]['update_by'] 		= $UserName;
					$ArrRequest[$key]['update_date'] 	= $DateTime;

					$ArrRequestHist[$key]['kode_trans'] 	= $kode_trans;
					$ArrRequestHist[$key]['no_ipp'] 		= $no_ipp;
					$ArrRequestHist[$key]['id_material'] 	= $GET_REQUEST[0]->id_material;
					$ArrRequestHist[$key]['nm_material'] 	= $GET_REQUEST[0]->nm_material;
					$ArrRequestHist[$key]['request'] 		= $REVISI_SISA;
					$ArrRequestHist[$key]['id_milik'] 		= $GET_REQUEST[0]->id_milik;
					$ArrRequestHist[$key]['no_spk'] 		= 'edit|'.$GET_REQUEST[0]->id;
					$ArrRequestHist[$key]['created_by'] 	= $UserName;
					$ArrRequestHist[$key]['created_date'] 	= $DateTime;
				}
			}

			$ArrUpdate = array(
				'jumlah_mat' => $SUM_MAT,
				'created_by' => $UserName,
				'created_date' => $DateTime
			);

			// exit;
			$this->db->trans_start();
				$this->db->where('kode_trans', $kode_trans);
				$this->db->update('warehouse_adjustment', $ArrUpdate);

				$this->db->update_batch('warehouse_adjustment_detail',$ArrDeatil,'id');
				if(!empty($ArrRequest)){
					$this->db->update_batch('planning_detail_spk',$ArrRequest,'id');
				}
				if(!empty($ArrRequestHist)){
					$this->db->insert_batch('planning_detail_request', $ArrRequestHist);
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);
				history("Update request material (gudang produksi) : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$kode_trans     = $this->uri->segment(3);

			$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
			$result_header		= $this->db->query($sql_header)->result();

			$sql 		= "	SELECT
								a.*,
								b.qty_stock AS stock,
								c.id_satuan,
								c.id_packing,
								c.nilai_konversi AS konversi
							FROM
								warehouse_adjustment_detail a
								LEFT JOIN warehouse_stock b ON a.id_material = b.id_material
								LEFT JOIN raw_materials c ON a.id_material=c.id_material
							WHERE
								a.kode_trans='".$kode_trans."'
								AND b.id_gudang = '".$result_header[0]->id_gudang_dari."'
							";
			$result		= $this->db->query($sql)->result_array();

			$data = array(
				'result' 		=> $result,
				'checked' 		=> $result_header[0]->checked,
				'gudang_before' => $result_header[0]->id_gudang_dari,
				'gudang_after' 	=> $result_header[0]->id_gudang_ke,
				'kode_trans'	=> $result_header[0]->kode_trans,
				'no_ipp'		=> $result_header[0]->no_ipp,
				'dated' 		=> date('ymdhis', strtotime($result_header[0]->created_date)),
				'resv' 			=> date('d F Y', strtotime($result_header[0]->created_date))

			);

			$this->load->view('Warehouse/modal_request_edit', $data);
		}
	}

	//==========================================================================================================================
	//===================================================REQUEST PRODUKSI======================================================
	//==========================================================================================================================

	public function modal_request_produksi(){
		$no_ipp 	= $this->uri->segment(3);
		$gudang_before 	= $this->uri->segment(4);
		$gudang_after 	= $this->uri->segment(5);
		$WHEREIN = ['TYP-0002','TYP-0003','TYP-0004','TYP-0005','TYP-0006'];
		$WHEREIN2 = ['TYP-0003','TYP-0004','TYP-0005','TYP-0006'];
		
		$IPP_TANDA = substr($no_ipp,0,4);
		$DMF_TANDA = substr($no_ipp,0,3);
		
		$get_planning = $this->db->select('a.*')->from('planning_detail a')->join('raw_materials b','a.id_material=b.id_material','left')->where('a.no_ipp',$no_ipp)->where_in('b.id_category',$WHEREIN)->order_by('a.nm_material','asc')->get()->result_array();
		
		$get_no_spk = $this->db->select('no_spk, upper(id_category) as product_name, id')->get_where('so_detail_header',array('id_bq'=>'BQ-'.$no_ipp,'no_spk !='=>null))->result_array();
		if($IPP_TANDA == 'IPPT'){
			$get_no_spk = $this->db->select('no_spk, upper(id_product) as product_name, id_milik AS id')->group_by('id_milik')->get_where('production_detail',array('id_produksi'=>'PRO-'.$no_ipp,'no_spk !='=>null))->result_array();
		}
		if($DMF_TANDA == 'DMF'){
			$get_no_spk = $this->db->select('no_spk, upper(product) as product_name, product_code_cut AS code_est, qty')->get_where('production_spk',array('product_code_cut'=>$no_ipp,'no_spk !='=>null))->result_array();
		}

		$get_category = $this->db->select('id_category, category')->from('raw_categories')->where_in('id_category',$WHEREIN2)->get()->result_array();
		$data = array(
			'IPP_TANDA' 		=> $IPP_TANDA,
			'DMF_TANDA' 		=> $DMF_TANDA,
			'no_ipp' 		=> $no_ipp,
			'get_no_spk' => $get_no_spk,
			'get_planning' => $get_planning,
			'get_category' => $get_category,
			'gudang_before' => $gudang_before,
			'gudang_after' 	=> $gudang_after
		);

		$this->load->view('Warehouse/modal_request_produksi', $data);
	}

	public function get_data_json_request_produksi(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request_produksi(
			$requestData['no_ipp'],
			$requestData['pusat'],
			$requestData['subgudang'],
			$requestData['uri_tanda'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$GET_SO_NUMBER1 = $this->db->select('id_bq, so_number')->get('so_number')->result_array();
		$GET_SO_NUMBER2 = $this->db->select('CONCAT("BQ-",no_ipp) AS id_bq, no_so AS so_number')->get('planning_tanki')->result_array();

		$GET_SO_NUMBER = array_merge($GET_SO_NUMBER1,$GET_SO_NUMBER2);
		$ArrGetSO = [];
		foreach($GET_SO_NUMBER AS $val => $value){
			$ArrGetSO[$value['id_bq']] = $value['so_number'];
		}

		$GET_SPK_NUMBER = $this->db->get_where('so_detail_header',array('no_spk <>'=>NULL))->result_array();
		$ArrGetSPK = [];
		$ArrGetIPP = [];
		foreach($GET_SPK_NUMBER AS $val => $value){
			$ArrGetSPK[$value['id']] = $value['no_spk'];
			$ArrGetIPP[$value['id']] = $value['id_bq'];
		}

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		$uri_tanda = $requestData['uri_tanda'];
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

			$no_so_tanki = '';
			$NO_SO = "";
			if($row['no_ipp'] != 'resin mixing' AND $row['no_ipp'] != 'ancuran' AND $row['no_ipp'] != 'internal' AND $row['no_ipp'] != 'reqnonso'){
				$SO_SEARCH = (!empty($ArrGetSO['BQ-'.$row['no_ipp']]))?$ArrGetSO['BQ-'.$row['no_ipp']]:$no_so_tanki;
				$NO_SO = (!empty($row['no_so']))?$row['no_so']:$SO_SEARCH;
			}
			$NO_SPK = $row['no_spk'];
			if($row['no_ipp'] == 'resin mixing'){
				$get_detail_spk2 = $this->db
								->select('a.id_milik')
								->from('production_spk_parsial a')
								->where('a.kode_spk',$row['kode_spk'])
								->where('a.created_date',$row['created_date'])
								->where('a.spk','1')
								->get()
								->result_array();
				$ArrNo_SPK = [];
				$ArrNo_SO = [];
				foreach ($get_detail_spk2 as $key => $value) {
					$SPK_SEARCH = (!empty($ArrGetSPK[$value['id_milik']]))?$ArrGetSPK[$value['id_milik']]:'';
					$ArrNo_SPK[] 	= (!empty($row['no_spk']))?$row['no_spk']:$SPK_SEARCH;
					
					$NO_IPP 		= (!empty($ArrGetIPP[$value['id_milik']]))?$ArrGetIPP[$value['id_milik']]:$row['keterangan'];
					$SO_SEARCH = (!empty($ArrGetSO[$NO_IPP]))?$ArrGetSO[$NO_IPP]:$no_so_tanki;
					$ArrNo_SO[] 	= (!empty($row['no_so']))?$row['no_so']:$SO_SEARCH;
				}

				$NO_SO = implode('<br>',array_unique($ArrNo_SO));
				$NO_SPK = implode('<br>',array_unique($ArrNo_SPK));
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."/<b>".strtoupper($row['no_ipp'])."</b></div>";
			$nestedData[]	= "<div align='center'>".$NO_SO."</div>";
			$nestedData[]	= "<div align='center'>".$NO_SPK."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('warehouse','nm_gudang','id',$row['id_gudang_dari']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('warehouse','nm_gudang','id',$row['id_gudang_ke']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'],4)."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='left'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$status = "WAITING CONFIRMATION";
			$warna = 'blue';
			if($row['checked'] == 'Y'){
				$status = "CONFIRMED";
				$warna = 'green';
			}
			if($row['no_ipp'] == 'resin mixing' AND !empty($row['closing_produksi_date'])){
				$status = "CONFIRMED";
				$warna = 'green';
			}
			if(!empty($row['file_eng_change'])){
				$FileEC = "<br><a href='".base_url('assets/file/produksi/').$row['file_eng_change']."' target='_blank'>Eng-change</a>";
			}
			else{
				$FileEC = "";
			}
			$nestedData[]	= "<div align='left'><span class='badge bg-".$warna."' title='Closing: ".$row['closing_produksi_date']."'>".$status."</span>".$FileEC."</div>";
				$plus	= "";
				$edit	= "";

				$print	= "&nbsp;<a href='".base_url('warehouse/print_request/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Surat Jalan (Total Request & Aktual)'><i class='fa fa-print'></i></a>";
				$print2	= "&nbsp;<button type='button' class='btn btn-sm btn-info history' data-tanda='request' title='History Pengeluaran' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-history'></i> Aktual</button>";
				if(!empty($uri_tanda)){
					if($row['checked'] == 'N' AND $row['req_mixing'] == 'N'){
						$plus	= "&nbsp;<button type='button' class='btn btn-sm btn-info check' title='Konfirmasi' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-check'></i></button>";
					}
					if($row['checked'] == 'N' AND $row['req_mixing'] == 'Y'){
						$plus	= "&nbsp;<a href='".base_url('warehouse/request_mat_resin/'.$row['id'])."' class='btn btn-sm btn-success' title='Konfirmasi'><i class='fa fa-check'></i></a>";
					}
				}
				else{
					if($row['checked'] == 'N' AND $row['req_mixing'] == 'N'){
						$edit	= "&nbsp;<button type='button' class='btn btn-sm btn-info edit_material' title='Edit Permintaan' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-edit'></i></button>";
					}
				}

			$nestedData[]	= "<div align='left'>
									<button type='button' class='btn btn-sm btn-primary detailAjust' data-tanda='request' title='Detail Adjustment' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-eye'></i></button>
                                    ".$edit."
                                    ".$print."
                                    ".$print2."
									".$plus."
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

	public function query_data_json_request_produksi($no_ipp, $pusat, $subgudang, $uri_tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_no_ipp ='';
		if(!empty($no_ipp)){
			$where_no_ipp = " AND a.no_ipp = '".$no_ipp."' ";
		}

		$where_pusat ='';
		if(!empty($pusat)){
			$where_pusat = " AND a.id_gudang_dari = '".$pusat."' ";
		}

		$where_subgudang ='';
		if(!empty($subgudang)){
			$where_subgudang = " AND a.id_gudang_ke = '".$subgudang."' ";
		}

		$where_tanda2 ='';
		if(!empty($uri_tanda)){
			$where_tanda2 = " AND a.checked = 'N' ";
		}
		// $sql = "
		// 		SELECT
		// 			(@row:=@row+1) AS nomor,
		// 			a.*
		// 		FROM
		// 			warehouse_adjustment a
		// 			LEFT JOIN production_spk_parsial b ON a.kode_spk=b.kode_spk AND a.created_date=b.created_date
		// 			LEFT JOIN production_spk c ON b.id_spk = c.id
		// 			LEFT JOIN production_detail d ON c.id_milik = d.id_milik AND c.kode_spk=d.kode_spk,
		// 			(SELECT @row:=0) r
		// 		WHERE 1=1 AND a.category = 'request produksi' AND a.deleted IS NULL
		// 			".$where_no_ipp."
		// 			".$where_pusat."
		// 			".$where_subgudang."
		// 			".$where_tanda2."
		// 		AND(
		// 			a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR c.product_code LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR d.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 		)
		// 		GROUP BY a.id
		// 	";
		if(empty($uri_tanda)){
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					b.closing_produksi_date
				FROM
					warehouse_adjustment a
					LEFT JOIN production_spk_parsial b ON a.kode_spk=b.kode_spk AND a.created_date=b.created_date AND b.spk=1
					LEFT JOIN production_spk c ON b.id_spk = c.id,
					(SELECT @row:=0) r
				WHERE 1=1 AND a.category = 'request produksi' AND a.deleted IS NULL AND a.status_id='1' AND a.created_date >= '2024-01-01 00:00:00'
					".$where_no_ipp."
					".$where_pusat."
					".$where_subgudang."
					".$where_tanda2."
				AND(
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.product_code LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.id
			";
		}
		if(!empty($uri_tanda)){
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					b.so_number,
					c.closing_produksi_date
				FROM
					warehouse_adjustment a
					LEFT JOIN so_number b ON a.no_ipp = REPLACE(b.id_bq,'BQ-','')
					LEFT JOIN production_spk_parsial c ON a.kode_spk=c.kode_spk AND a.created_date=c.created_date AND c.spk=1
					LEFT JOIN production_spk z ON a.kode_spk=z.kode_spk,
					(SELECT @row:=0) r
				WHERE 1=1 AND a.category = 'request produksi' AND a.deleted IS NULL AND a.status_id='1' AND c.closing_produksi_date IS NULL AND a.created_date >= '2024-01-01 00:00:00'
					".$where_no_ipp."
					".$where_pusat."
					".$where_subgudang."
					".$where_tanda2."
				AND(
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR z.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR z.product_code LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.id
			";
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'kode_trans',
			3 => 'kode_trans',
			4 => 'id_gudang_dari',
			5 => 'id_gudang_ke'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function get_data_json_modal_request_produksi(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_modal_request_produksi(
			$requestData['no_ipp'],
			$requestData['pusat'],
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
			$nestedData[]	= "<div align='center'>".$nomor."
								<input type='hidden' class='id' name='detailx[".$nomor."][id]' value='".$row['id']."'>
								<input type='hidden' class='nm_material' name='detailx[".$nomor."][nm_material]' value='".strtoupper($row['nm_material'])."'>
								<input type='hidden' class='qty_stock' name='detailx[".$nomor."][qty_stock]' value='".$row['qty_stock']."'>
								</div>";
			$nestedData[]	= "	<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],4)." kg</div>";
			// $nestedData[]	= "<div align='right'>".number_format(0,2)." kg</div>";
			$nestedData[]	= "<div align='left'><input type='text' style='width:100%' name='detailx[".$nomor."][sudah_request]' data-no='".$nomor."'class='form-control input-sm text-right maskM sudah_request'><script type='text/javascript'>$('.maskM').autoNumeric('init', {mDec: '4', aPad: false}); $('.chosen_select').chosen({width:'100%'});</script></div>";
			$nestedData[]	= "<div align='left'><input type='text' style='width:100%' name='detailx[".$nomor."][ket_request]' data-no='".$nomor."' class='form-control input-sm text-left ket_request'></div>";
			$nestedData[]	= "<div align='center'><button type='button' class='btn btn-primary btn-sm pindahkan' title='Pindahkan'><i class='fa fa-location-arrow'></i></button></div>";

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

	public function query_data_json_modal_request_produksi($no_ipp, $pusat, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){


		$where_no_ipp ='';
		if(!empty($no_ipp)){
			$where_no_ipp = " AND a.no_ipp = '".$no_ipp."' ";
		}

		$where_pusat ='';
		if(!empty($pusat)){
			$where_pusat = " AND b.id_gudang = '".$pusat."' ";
		}

		$sql = "
				SELECT
					b.*
				FROM
					warehouse_stock b
				WHERE 1=1
					".$where_pusat."
				AND(
					b.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
			";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_material',
			2 => 'qty_stock'
		);

		$sql .= " ORDER BY b.nm_material ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function process_request_produksi(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;

		if(!empty($data['detail'])){
			$detail			= $data['detail'];
		}
		if(!empty($data['detail2'])){
			$detail2		= $data['detail2'];
		}
		$no_ipp			= $data['no_ipp'];
		$id_milik		= $data['id_milik'];
		$gudang_before	= $data['gudang_before'];
		$gudang_after	= $data['gudang_after'];
		$no_spk			= $data['no_spk'];
		$tanggal		= $data['tanggal'];
		$qty_spk		= str_replace(',','',$data['qty_spk']);
		$keterangan		= strtolower($data['keterangan']);
		$Ym 			= date('ym');

		$UserName = $data_session['ORI_User']['username'];
		$DateTime = date('Y-m-d H:i:s');
		// print_r($data);
		// exit;

		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRS".$Ym.$urut2;

		$ArrDeatilAdj	 = array();
		$Arr_update_plan = array();
		$SUM_MAT = 0;
		if(!empty($data['detail'])){
			foreach($detail AS $val => $valx){
				$sudah_request 	= str_replace(',','',$valx['sudah_request']);
				if($sudah_request > 0){
					$SUM_MAT += $sudah_request;

					$det_mat = $this->db->get_where('warehouse_stock', array('id'=>$valx['id']))->result();

					//detail adjustmeny
					$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
					$ArrDeatilAdj[$val]['id_po_detail'] 	= $id_milik;
					$ArrDeatilAdj[$val]['id_material_req'] 	= $det_mat[0]->id_material;
					$ArrDeatilAdj[$val]['id_material'] 		= $det_mat[0]->id_material;
					$ArrDeatilAdj[$val]['nm_material'] 		= $det_mat[0]->nm_material;
					$ArrDeatilAdj[$val]['id_category'] 		= $det_mat[0]->id_category;
					$ArrDeatilAdj[$val]['nm_category'] 		= $det_mat[0]->nm_category;
					$ArrDeatilAdj[$val]['qty_order'] 		= $sudah_request;
					$ArrDeatilAdj[$val]['qty_oke'] 			= $sudah_request;
					$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['ket_request']);
					$ArrDeatilAdj[$val]['ket_req_pro'] 		= 'luar budget';
					$ArrDeatilAdj[$val]['update_by'] 		= $UserName;
					$ArrDeatilAdj[$val]['update_date'] 		= $DateTime;
				}
			}
		}

		$ArrSPKInsert = [];
		$ArrSPKUpdate = [];
		$ArrDetailTransaksi = [];
		if(!empty($data['detail2'])){
			foreach($detail2 AS $val => $valx){
				$val_number = $val.'9999';
				$sudah_request 	= str_replace(',','',$valx['sudah_request']);
				$berat_est 		= str_replace(',','',$valx['berat_est']);
				$qty_sisa 		= str_replace(',','',$valx['qty_sisa']);
				$qty_total_req 	= str_replace(',','',$valx['qty_total_req']);
				if($sudah_request > 0){
					$SUM_MAT += $sudah_request;

					$det_mat	= $this->db->select('*')->get_where('planning_detail', array('id'=>$valx['id']))->result();
					$det_mat2 	= $this->db->get_where('raw_materials', array('id_material'=>$det_mat[0]->id_material))->result();
					//detail adjustmeny
					$ArrDeatilAdj[$val_number]['kode_trans'] 		= $kode_trans;
					$ArrDeatilAdj[$val_number]['id_po_detail'] 		= $id_milik;
					$ArrDeatilAdj[$val_number]['id_material_req'] 	= $det_mat[0]->id_material;
					$ArrDeatilAdj[$val_number]['id_material'] 		= $det_mat[0]->id_material;
					$ArrDeatilAdj[$val_number]['nm_material'] 		= $det_mat[0]->nm_material;
					$ArrDeatilAdj[$val_number]['id_category'] 		= $det_mat2[0]->id_category;
					$ArrDeatilAdj[$val_number]['nm_category'] 		= $det_mat2[0]->nm_category;
					$ArrDeatilAdj[$val_number]['qty_order'] 		= $det_mat[0]->berat;
					$ArrDeatilAdj[$val_number]['qty_oke'] 			= $sudah_request;
					$ArrDeatilAdj[$val_number]['keterangan'] 		= strtolower($valx['ket_request']);
					$ArrDeatilAdj[$val_number]['ket_req_pro'] 		= 'budget';
					$ArrDeatilAdj[$val_number]['update_by'] 		= $UserName;
					$ArrDeatilAdj[$val_number]['update_date'] 		= $DateTime;
					$ArrDeatilAdj[$val_number]['qty_est'] 			= $berat_est;
					$ArrDeatilAdj[$val_number]['qty_sisa'] 			= $qty_sisa;
					$ArrDeatilAdj[$val_number]['qty_total_req'] 	= $qty_total_req;

					$ArrDetailTransaksi[$val_number]['kode_trans'] 		= $kode_trans;
					$ArrDetailTransaksi[$val_number]['no_ipp'] 			= $no_ipp;
					$ArrDetailTransaksi[$val_number]['id_material'] 	= $det_mat[0]->id_material;
					$ArrDetailTransaksi[$val_number]['nm_material'] 	= $det_mat[0]->nm_material;
					$ArrDetailTransaksi[$val_number]['total_est'] 		= $det_mat[0]->berat;
					$ArrDetailTransaksi[$val_number]['request'] 		= $sudah_request;
					$ArrDetailTransaksi[$val_number]['id_milik'] 		= $id_milik;
					$ArrDetailTransaksi[$val_number]['no_spk'] 			= $no_spk;
					$ArrDetailTransaksi[$val_number]['created_by'] 		= $UserName;
					$ArrDetailTransaksi[$val_number]['created_date'] 	= $DateTime;

					$Arr_update_plan[$val]['id'] 			= $det_mat[0]->id;
					$Arr_update_plan[$val]['total_request'] = $det_mat[0]->total_request + $sudah_request;
					$Arr_update_plan[$val]['update_by'] 	= $UserName;
					$Arr_update_plan[$val]['update_date'] 	= $DateTime;
					// $Arr_update_plan[$val]['ket_request'] 	= $kode_trans;

					//PLANNING DETAIL
					if(!empty($id_milik)){
						$det_mat_spk	= $this->db->select('*')->get_where('planning_detail_spk', array('no_ipp'=>$no_ipp,'id_milik'=>$id_milik,'id_material'=>$det_mat[0]->id_material))->result();
						if(!empty($det_mat_spk)){
							//UPDATE
							$ArrSPKUpdate[$val_number]['id'] 			= $det_mat_spk[0]->id;
							$ArrSPKUpdate[$val_number]['total_request'] = $det_mat_spk[0]->total_request + $sudah_request;
							$ArrSPKUpdate[$val_number]['update_by'] 		= $UserName;
							$ArrSPKUpdate[$val_number]['update_date'] 		= $DateTime;
						}
						else{
							$ArrSPKInsert[$val_number]['no_ipp'] 		= $no_ipp;
							$ArrSPKInsert[$val_number]['id_milik'] 		= $id_milik;
							$ArrSPKInsert[$val_number]['id_material'] 	= $det_mat[0]->id_material;
							$ArrSPKInsert[$val_number]['nm_material'] 	= $det_mat[0]->nm_material;
							$ArrSPKInsert[$val_number]['berat'] 		= $berat_est;
							$ArrSPKInsert[$val_number]['total_request'] = $sudah_request;
							$ArrSPKInsert[$val_number]['update_by'] 		= $UserName;
							$ArrSPKInsert[$val_number]['update_date'] 		= $DateTime;
						}
					}
				}
			}
		}

		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'category' 			=> 'request produksi',
			'jumlah_mat' 		=> $SUM_MAT,
			'no_ipp' 			=> $no_ipp,
			'no_spk' 			=> $no_spk,
			'tanggal' 			=> $tanggal,
			'qty_spk' 			=> $qty_spk,
			'keterangan' 		=> $keterangan,
			'id_gudang_dari' 	=> $gudang_before,
			'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $gudang_before),
			'id_gudang_ke' 		=> $gudang_after,
			'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $gudang_after),
			'created_by' 		=> $UserName,
			'created_date' 		=> $DateTime
		);

		// print_r($ArrInsertH);
		// print_r($ArrDeatilAdj);
		// print_r($Arr_update_plan);
		// exit;
		$this->db->trans_start();
			$this->db->insert('warehouse_adjustment', $ArrInsertH);
			if(!empty($ArrDeatilAdj)){
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
			}
			if(!empty($Arr_update_plan)){
				$this->db->update_batch('planning_detail', $Arr_update_plan, 'id');
			}
			if(!empty($ArrSPKUpdate)){
				$this->db->update_batch('planning_detail_spk', $ArrSPKUpdate, 'id');
			}
			if(!empty($ArrSPKInsert)){
				$this->db->insert_batch('planning_detail_spk', $ArrSPKInsert);
			}
			if(!empty($ArrDetailTransaksi)){
				$this->db->insert_batch('planning_detail_request', $ArrDetailTransaksi);
			}
		$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history("Material request subgudang : ".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}

	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	public function get_data_json_material_stock(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_material_stock(
			$requestData['gudang'],
			$requestData['date_filter'],
			$requestData['category'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$qty_stock		= $fetch['qty_stock'];
		$qty_booking	= $fetch['qty_booking'];
		$qty_rusak		= $fetch['qty_rusak'];

		$get_category = $this->db->select('category')->get_where('warehouse', array('id'=>$requestData['gudang']))->result();

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		$GET_COSTBOOK = get_costbook();
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

			$COSTBOO_ = (!empty($GET_COSTBOOK[$row['id_material']]))?$GET_COSTBOOK[$row['id_material']]:0;
			$COSTBOOK = (empty($requestData['date_filter']))?$COSTBOO_:$row['costbook'];
			$COSTBOOK_TOTAL = $COSTBOOK * $row['qty_stock'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_material'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['idmaterial'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_category'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_gudang'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],4)."</div>";
			if($requestData['category'] != 'produksi'){
				if($requestData['category'] == 'pusat'){
					$nestedData[]	= "<div align='right'><span class='detailBooking text-bold text-primary' style='cursor:pointer;' data-id_material='".$row['id_material']."' data-nm_material='".$row['nm_material']."' data-id_gudang='".$row['id_gudang']."'>".number_format($row['qty_booking'],4)."</span></div>";
				}
				else{
					$nestedData[]	= "<div align='right'>".number_format($row['qty_booking'],4)."</div>";
				}
				$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'] - $row['qty_booking'],4)."</div>";
				if($requestData['category'] == 'pusat'){
					$nestedData[]	= "<div align='right'>".number_format($row['qty_rusak'],4)."</div>";
					// $nestedData[]	= "<div align='right'>".number_format($COSTBOOK,2)."</div>";
					// $nestedData[]	= "<div align='right'>".number_format($COSTBOOK_TOTAL,2)."</div>";
				}
			}
			if($requestData['category'] == 'pusat'){
			$nestedData[]	= "<div align='center'>
								<button type='button' class='btn btn-sm btn-warning look_history' title='History' data-nm_material='".strtoupper($row['nm_material'])."' data-id_material='".$row['id_material']."' data-id_gudang='".$row['id_gudang']."'><i class='fa fa-history'></i></button>
								<button type='button' class='btn btn-sm btn-primary look_history_tras' title='History Tras' data-nm_material='".strtoupper($row['nm_material'])."' data-id_material='".$row['id_material']."' data-id_gudang='".$row['id_gudang']."'><i class='fa fa-history'></i></button>
								<button type='button' class='btn btn-sm btn-default lot_history' title='Lot' data-nm_material='".strtoupper($row['nm_material'])."' data-id_material='".$row['id_material']."' data-id_gudang='".$row['id_gudang']."'><i class='fa fa-history'></i></button>
								</div>";
			}
			else{
				$nestedData[]	= "<div align='center'>
								<button type='button' class='btn btn-sm btn-warning look_history' title='History' data-nm_material='".strtoupper($row['nm_material'])."' data-id_material='".$row['id_material']."' data-id_gudang='".$row['id_gudang']."'><i class='fa fa-history'></i></button>
								<button type='button' class='btn btn-sm btn-primary look_history_tras' title='History Tras' data-nm_material='".strtoupper($row['nm_material'])."' data-id_material='".$row['id_material']."' data-id_gudang='".$row['id_gudang']."'><i class='fa fa-history'></i></button>
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
			"data"            	=> $data,
			"category"          => $requestData['category'],
			"recordsStock"		=> $qty_stock,
			"recordsBooking"	=> $qty_booking,
			"recordsRusak"		=> $qty_rusak
		);

		echo json_encode($json_data);
	}

	public function query_data_json_material_stock($gudang, $date_filter, $category, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$table = "warehouse_stock";
		$where_gudang ='';
		$where_date =" AND c.delete = 'N'";
		$field_add = '';
		$group_by = '';
		$fieldStock = 'a.qty_stock, a.qty_booking,a.qty_rusak, a.id_gudang,b.nm_gudang,';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
		}
		
		if(!empty($date_filter)){
			$where_gudang = " AND a.id_gudang = '".$gudang."' ";
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_stock_per_day";
			$field_add = "a.costbook, a.total_value,";
		}

		if($gudang == '0'){
			$where_gudang = " AND a.id_gudang IN (".$this->gudang_produksi.") ";
			$group_by = ' GROUP BY c.id_material ';
			$fieldStock = 'SUM(a.qty_stock) AS qty_stock, SUM(a.qty_booking) AS qty_booking, SUM(a.qty_rusak) AS qty_rusak, "0" AS id_gudang, "Gudang Produksi" AS nm_gudang,';
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				c.idmaterial,
				c.id_material,
				c.nm_material,
				".$fieldStock."
				".$field_add."
				c.nm_category
			FROM
				".$table." a
				LEFT JOIN warehouse b ON a.id_gudang=b.id
				LEFT JOIN raw_materials c ON a.id_material = c.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.id_material <> 'MTL-1903000' AND c.delete = 'N' ".$where_gudang." ".$where_date." AND (
				c.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_gudang LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		".$group_by;

		$Query_Sum	= "SELECT
					SUM(a.qty_stock) AS qty_stock,
					SUM(a.qty_booking) AS qty_booking,
					SUM(a.qty_rusak) AS qty_rusak
				FROM
					".$table." a
					LEFT JOIN warehouse b ON a.kd_gudang=b.kd_gudang
					LEFT JOIN raw_materials c ON a.id_material = c.id_material,
					(SELECT @row:=0) r
				WHERE 1=1 AND a.id_material <> 'MTL-1903000' ".$where_gudang." ".$where_date." AND (
					c.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.nm_gudang LIKE '%".$this->db->escape_like_str($like_value)."%'
				)".$group_by;
		$qty_stock = $qty_booking = $qty_rusak	= 0;
		$Hasil_SUM		   = $this->db->query($Query_Sum)->result_array();
		if($Hasil_SUM){
			$qty_stock		= $Hasil_SUM[0]['qty_stock'];
			$qty_booking	= $Hasil_SUM[0]['qty_booking'];
			$qty_rusak		= $Hasil_SUM[0]['qty_rusak'];
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['qty_stock'] 	= $qty_stock;
		$data['qty_booking'] = $qty_booking;
		$data['qty_rusak'] 	= $qty_rusak;
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'idmaterial',
			2 => 'nm_material',
			3 => 'nm_category',
			4 => 'nm_gudang',
			5 => 'nm_material',
			6 => 'qty_stock'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function get_data_json_incoming_material(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_incoming_material(
			$requestData['no_po'],
			$requestData['gudang'],
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
			$nestedData[]	= "<div>".$row['no_ipp']."/".$row['kode_trans']."</div>";
			$nestedData[]	= "<div>".$row['nm_supplier']."</div>";
			// $nestedData[]	= "<div align='left'>".$row['kd_gudang_dari']."</div>";
			$nestedData[]	= "<div align='left'>".get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'],2)."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='left'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
				$plus	= "";

				$print	= "&nbsp;<a href='".base_url('warehouse/print_incoming/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Incoming'><i class='fa fa-print'></i></a>";
				// if($row['checked'] == 'N'){
					// $plus	= "&nbsp;<button type='button' class='btn btn-sm btn-info check' title='Check Incoming' data-no_ipp='".$row['no_ipp']."' data-users='".str_replace(' ','sp4si', $row['created_by'])."' data-tanggal='".str_replace(' ','sp4si', $row['created_date'])."'><i class='fa fa-check'></i></button>";
				// }

			$nestedData[]	= "<div align='center'>
									<button type='button' class='btn btn-sm btn-primary detailAjust' title='View Incoming' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-eye'></i></button>
                                    ".$print."
									".$plus."
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

	public function query_data_json_incoming_material($no_po, $gudang, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_no_po ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}

		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang_ke = '".$gudang."' ";
		}

		$sql = "
			SELECT
				a.*,b.nm_supplier
			FROM
				warehouse_adjustment a
				left join 
					(SELECT no_po,nm_supplier FROM tran_material_po_header)
				b on a.no_ipp=b.no_po
		    WHERE 1=1 AND a.category = 'incoming material' AND a.status_id = 1
				".$where_no_po."
				".$where_gudang."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function get_data_json_move_gudang(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_move_gudang(
			$requestData['gudang1'],
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
			$nestedData[]	= "<div align='left'>".$row['idmaterial']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty_booking'],2)."</div>";
			$nestedData[]	= "<div align='center'>
								<input type='hidden' name='ListMove[".$nomor."][id]' class='form-control input-sm' value='".$row['id']."'>
								<input type='hidden' name='ListMove[".$nomor."][qty_stock]' class='form-control input-sm' value='".$row['qty_stock']."'>
								<input type='hidden' name='ListMove[".$nomor."][qty_booking]' class='form-control input-sm' value='".$row['qty_booking']."'>
								<input type='hidden' name='ListMove[".$nomor."][id_material]' class='form-control input-sm' value='".$row['id_material']."'>
								<input type='hidden' name='ListMove[".$nomor."][idmaterial]' class='form-control input-sm' value='".$row['idmaterial']."'>
								<input type='hidden' name='ListMove[".$nomor."][nm_material]' class='form-control input-sm' value='".$row['nm_material']."'>
								<input type='hidden' name='ListMove[".$nomor."][id_category]' class='form-control input-sm' value='".$row['id_category']."'>
								<input type='hidden' name='ListMove[".$nomor."][nm_category]' class='form-control input-sm' value='".$row['nm_category']."'>
								<input type='hidden' name='ListMove[".$nomor."][kd_gudang]' class='form-control input-sm' value='".$row['kd_gudang']."'>
								<input type='input' name='ListMove[".$nomor."][qty_move]' class='form-control text-right input-sm maskM' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
								<script type='text/javascript'>$('.maskM').maskMoney();</script>
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

	public function query_data_json_move_gudang($gudang1, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*
			FROM
				warehouse_stock a
		    WHERE (a.kd_gudang='".$gudang1."') AND (
				a.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'idmaterial',
			2 => 'nm_material'
		);

		$sql .= " ORDER BY qty_booking DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function get_data_json_check_material(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_check_material(
			$requestData['no_po'],
			$requestData['gudang'],
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

			$list_supplier		= $this->db->query("SELECT nm_material FROM warehouse_adjustment_detail WHERE kode_trans='".$row['kode_trans']."'")->result_array();
			$arr_sup = array();
			foreach($list_supplier AS $val => $valx){
				$arr_sup[$val] = $valx['nm_material'];
			}
			$dt_sup	= implode("<br>", $arr_sup);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."/".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('warehouse','nm_gudang','id',$row['id_gudang_ke']))."</div>";
			$nestedData[]	= "<div align='left'>".$dt_sup."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat_check'],2)."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$status = "WAITING INSPECTION";
			$warna = 'blue';
			if($row['checked'] == 'Y'){
				$status = "CHECKED";
				$warna = 'green';
				if((int) $row['qty_cek'] < (int) $row['qty_req']){
					$status = "PARSIAL CHECK";
					$warna = 'orange';
				}
			}
			$nestedData[]	= "<div align='left'><span class='badge bg-".$warna."'>".$status."</span></div>";
				$plus	= "";
				$plus2	= "";

				$print	= "&nbsp;<a href='".base_url('warehouse/print_incoming2/'.$row['kode_trans'].'/check')."' target='_blank' class='btn btn-sm btn-warning' title='Print'><i class='fa fa-print'></i></a>";
				if((int) $row['qty_cek'] < (int) $row['qty_req']){
					// $plus	= "&nbsp;<button type='button' class='btn btn-sm btn-info check' title='Check Incoming' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-check'></i></button>";
					$plus2	= "&nbsp;<button type='button' class='btn btn-sm btn-success checknew' title='Check Incoming' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-check'></i>&nbsp; New</button>";
				}

			$nestedData[]	= "<div align='left'>
									<button type='button' class='btn btn-sm btn-default detailAjust' title='Detail & QR' data-tanda='check' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-qrcode'></i></button>
                                    ".$print."
									".$plus."
									".$plus2."
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

	public function query_data_json_check_material($no_po, $gudang, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_no_po ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}

		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.kd_gudang_ke = '".$gudang."' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				SUM(b.qty_oke) AS qty_req,
				SUM(b.check_qty_oke) AS qty_cek
			FROM
				warehouse_adjustment a
				LEFT JOIN warehouse_adjustment_detail b ON a.kode_trans=b.kode_trans AND b.check_qty_oke IS NULL,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.category = 'incoming material' AND a.status_id = '1'
				".$where_no_po."
				".$where_gudang."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function save_temp_mutasi(){
		$data 			 	= $this->input->post();
		$data_session		= $this->session->userdata;
		$printby			= $data_session['ORI_User']['username'];

		$id			      	= $data['id'];
		$sudah_request	  	= str_replace(',','',$data['sudah_request']);
		$ket_request	  	= $data['ket_request'];
		
		// print_r($data);
		// exit;

		$ArrInsertH = array(
			'category' 		=> 'request subgudang',
			'id_mat' 		=> $id,
			'qty'   	  	=> $sudah_request,
			'ket' 		  	=> $ket_request,
			'created_by' 	=> $printby,
			'created_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id_mat', $id);
			$this->db->where('created_by', $printby);
			$this->db->delete('temp_server_side');

			$this->db->insert('temp_server_side', $ArrInsertH);
		$this->db->trans_complete();

	}

	public function modal_history(){
		$id_material 	= $this->uri->segment(3);
		$id_gudang 		= $this->uri->segment(4);

		$tanggalNow = date('Y-m-d H:i:s');
		$TanggalFirst = date('Y-m-d H:i:s', strtotime('-5 month', strtotime($tanggalNow)));

		$result		= $this->db->get_where('warehouse_history', array('id_material'=>$id_material, 'id_gudang'=>$id_gudang, 'update_date >'=>$TanggalFirst,'jumlah_mat !='=>0))->result_array();
		$material	= $this->db->get_where('raw_materials', array('id_material'=>$id_material))->result_array();

		$data = array(
			'result' => $result,
			'material' => $material,
			'id_gudang' => $id_gudang
		);

		$this->load->view('Warehouse/modal_history', $data);
	}

	public function modal_history_booking(){
		$id_material 	= $this->uri->segment(3);
		$id_gudang 		= $this->uri->segment(4);

		$SQL = "SELECT
					* 
				FROM
					warehouse_history
				WHERE
					id_material= '$id_material' 
					AND id_gudang= '$id_gudang' 
					AND (kd_gudang_ke= 'BOOKING' OR kd_gudang_dari= 'BOOKING' )
					AND update_date > '2023-12-15 00:00:00'";
		$result		= $this->db->query($SQL)->result_array();

		$SQL2 		= $SQL." GROUP BY no_ipp";
		$result2	= $this->db->query($SQL2)->result_array();

		$material	= $this->db->get_where('raw_materials', array('id_material'=>$id_material))->result_array();

		$data = array(
			'id_material' => $id_material,
			'id_gudang' => $id_gudang,
			'result' => $result,
			'listSO' => $result2,
			'GET_SO' => get_detail_ipp(),
			'material' => $material
		);

		$this->load->view('Warehouse/modal_history_booking', $data);
	}

	public function request_mat_resin(){
		$ID	= $this->uri->segment(3);
		$detAdjustment = $this->db->get_where('warehouse_adjustment', array('id'=>$ID))->result_array();
		$get_detail_spk2 = $this->db
							->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang')
							->from('production_spk_parsial a')
							->join('production_spk b','a.id_spk = b.id')
							->where('a.kode_spk',$detAdjustment[0]['kode_spk'])
							->where('a.created_date',$detAdjustment[0]['created_date'])
							->where('a.spk','1')
							->get()
							->result_array();
		$getWherehouse = $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		$getWherehouse2 = $this->db->get_where('warehouse', array('category'=>'subgudang'))->result_array();
		$no_request = $this->db->order_by('id','desc')->get_where('print_header', array('kode_trans'=>$detAdjustment[0]['kode_trans'],'aktual_date'=>NULL))->result_array();

		$data = array(
			'title'			=> 'Request SPK Mixing',
			'action'		=> 'index',
			'no_request' 	=> $no_request,
			'warehouse' 	=> $getWherehouse,
			'warehouse2' 	=> $getWherehouse2,
			'get_detail_spk2' 		=> $get_detail_spk2,
			'file_eng_change' 		=> $detAdjustment[0]['file_eng_change'],
			'gudang_from' 		=> $detAdjustment[0]['id_gudang_dari'],
			'gudang_to' 		=> $detAdjustment[0]['id_gudang_ke'],
			'kode_spk' 				=> $detAdjustment[0]['kode_spk'],
			'kode_trans' 				=> $detAdjustment[0]['kode_trans'],
			'hist_produksi'			=> $detAdjustment[0]['created_date']
		);
		$this->load->view('Warehouse/request_mat_resin', $data);
	}


	public function index_material_stock_tras(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)).'/'.strtolower($this->uri->segment(3)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' AND category='".strtolower($this->uri->segment(3))."' ORDER BY urut ASC ")->result_array();
		if($this->uri->segment(3) == 'origa'){
			$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE `status`='Y' AND id='23' ")->result_array();
			$judul = "Warehouse Material >> Gudang Origa >> Stock Tras";
		}
		elseif($this->uri->segment(3) == 'pusat'){
			$judul = "Warehouse Material >> Gudang Pusat >> Stock Tras";
		}
		elseif($this->uri->segment(3) == 'subgudang'){
			$judul = "Warehouse Material >> Sub Gudang >> Stock Tras";
		}
		elseif($this->uri->segment(3) == 'virtual'){
			$judul = "Gudang Finish Good >> Stock Tras";
			$data_gudang		= $this->db->query("SELECT * FROM warehouse WHERE id='15' ")->result_array();
		}
		else{
			$judul = "Warehouse Material >> Gudang Produksi >> Stock Tras";
		}
		$data = array(
			'title'			=> $judul,
			'action'		=> 'index',
			'category'		=> $this->uri->segment(3),
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View Material Stock');
		$this->load->view('Warehouse/material_stock_tras',$data);
	}

	public function modal_history_tras(){
		$id_material 	= $this->uri->segment(3);
		$id_gudang 		= $this->uri->segment(4);

		$tanggalNow = date('Y-m-d H:i:s');
		$TanggalFirst = date('Y-m-d H:i:s', strtotime('-5 month', strtotime($tanggalNow)));

		$result		= $this->db->get_where('tran_warehouse_jurnal_detail', array('id_material'=>$id_material, 'id_gudang'=>$id_gudang, 'tgl_trans >'=>$TanggalFirst))->result_array();
		$material	= $this->db->get_where('raw_materials', array('id_material'=>$id_material))->result_array();

		$data = array(
			'result' => $result,
			'material' => $material,
			'id_gudang' => $id_gudang
		);

		$this->load->view('Warehouse/modal_history_tras', $data);
	}


}
