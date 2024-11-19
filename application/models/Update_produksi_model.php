<?php
class Update_produksi_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	//UPDATE 1
	public function update_produksi_1(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$id_produksi	= $data['id_produksi'];
			$Imp			= explode('-', $id_produksi);

			$dataUpdate = array(
				'real_start_produksi' => $data['real_start_produksi'],
				'real_end_produksi' => $data['real_end_produksi'],
				'sts_produksi' => 'FINISH',
				'modified_by' => $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
			);

			$dtUpdIpp = array(
				'status' => 'FINISH',
			);

			$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi)->update('production_header', $dataUpdate);
			$this->db->where('no_ipp', $Imp[1])->update('production', $dtUpdIpp);
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

			$HelpDet 	= "bq_detail_header";
			if($row[0]['jalur'] == 'FD'){
				$HelpDet = "so_detail_header";
			}
			$qDetail = "( SELECT
							min( a.product_ke ) AS qty_awal,
							max( a.product_ke ) AS qty_akhir,
							a.*,
							b.no_komponen
							FROM
								production_detail a
								LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."'
								AND a.print_merge_date <> ''
							GROUP BY
								a.print_merge_date
							)
							UNION
							(
							SELECT
								a.product_ke  AS qty_awal,
								a.product_ke  AS qty_akhir,
								a.*,
								b.no_komponen
							FROM
								production_detail a
								LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."'
								AND ( a.print_merge_date = '' OR a.print_merge_date IS NULL )
							)";
			// echo $qDetail;
			$rowD	= $this->db->query($qDetail)->result_array();

			$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_category <> 'pipe slongsong' AND a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
			$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();

			$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_category <> 'pipe slongsong' AND a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
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
			$this->load->view('Production/updateRealNew2',$data);
		}
	}
		
	public function modal_update_produksi_1(){
		$id_product = $this->uri->segment(3);
		$id_produksi = $this->uri->segment(4);
		$idProducktion = $this->uri->segment(5);
		$id_milik = $this->uri->segment(6);
		$qty_awal = $this->uri->segment(7);
		$qty_akhir = $this->uri->segment(8);
		// echo "Milik=>".$id_milik;

		$qty_total = ($qty_akhir - $qty_awal) + 1; 
		
		$qProduksi		= "SELECT * FROM production_header WHERE id_produksi='".$id_produksi."' ";
		$restProduksi	= $this->db->query($qProduksi)->result_array();
		
		$HelpDet 	= "bq_detail_header";
		$HelpDet_BCH 	= "bq_component_header";
		$HelpDet_BCD 	= "bq_component_detail";
		$HelpDet_BCDP 	= "bq_component_detail_plus";
		$HelpDet_BCDA 	= "bq_component_detail_add";
		if($restProduksi[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
			$HelpDet_BCH 	= "so_component_header";
			$HelpDet_BCD 	= "so_component_detail";
			$HelpDet_BCDP 	= "so_component_detail_plus";
			$HelpDet_BCDA 	= "so_component_detail_add";
		}
		
		$qHeader			= "SELECT * FROM ".$HelpDet_BCH." WHERE id_product='".$id_product."'";
		$restHeader			= $this->db->query($qHeader)->result_array();
		
		//LINER
		$qDetail1			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$qDetail1		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='GLASS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		}
		$restDetail1		= $this->db->query($qDetail1)->result_array(); 
		$qDetailResin1		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin1	= $this->db->query($qDetailResin1)->result_array(); 
		$qDetailPlus1		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
		
		$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
		
		//STRUKTURE
		$qDetail2			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$qDetail2		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
		}
		$restDetail2		= $this->db->query($qDetail2)->result_array();
		$qDetailResin2		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin2	= $this->db->query($qDetailResin2)->result_array();
		$qDetailPlus2		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
		$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
		
		//STRUKTURE NECK 1
		$qDetail2N1			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		$restDetail2N1		= $this->db->query($qDetail2N1)->result_array();
		$qDetailResin2N1	= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin2N1	= $this->db->query($qDetailResin2N1)->result_array();
		$qDetailPlus2N1		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
		$restDetailPlus2N1	= $this->db->query($qDetailPlus2N1)->result_array();
		
		//STRUKTURE NECK 2
		$qDetail2N2			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		$restDetail2N2		= $this->db->query($qDetail2N2)->result_array();
		$qDetailResin2N2	= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin2N2	= $this->db->query($qDetailResin2N2)->result_array();
		$qDetailPlus2N2		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
		$restDetailPlus2N2	= $this->db->query($qDetailPlus2N2)->result_array();
		
		//EXTERNAL
		$qDetail3			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		$restDetail3		= $this->db->query($qDetail3)->result_array();
		$qDetailResin3		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin3	= $this->db->query($qDetailResin3)->result_array();
		$qDetailPlus3		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
		$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
		
		//TOPCOAT
		$qDetailPlus4		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
		$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();

		$getWherehouse = $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		
		$data = array(
			'id_product' => $id_product,
			'id_produksi' => $id_produksi,
			'idProducktion' => $idProducktion,
			'id_milik' => $id_milik,
			'qty_awal' => $qty_awal,
			'qty_akhir' => $qty_akhir,
			'qty_total' => $qty_total,
			'warehouse' => $getWherehouse,
			'restProduksi' => $restProduksi,
			'restHeader' => $restHeader,
			'restDetail1' => $restDetail1,
			'restDetailResin1' => $restDetailResin1,
			'restDetailPlus1' => $restDetailPlus1,
			'restDetail2' => $restDetail2,
			'restDetailResin2' => $restDetailResin2,
			'restDetailPlus2' => $restDetailPlus2,
			'restDetail2N1' => $restDetail2N1,
			'restDetailResin2N1' => $restDetailResin2N1,
			'restDetailPlus2N1' => $restDetailPlus2N1,
			'restDetail2N2' => $restDetail2N2,
			'restDetailResin2N2' => $restDetailResin2N2,
			'restDetailPlus2N2' => $restDetailPlus2N2,
			'restDetail3' => $restDetail3,
			'restDetailResin3' => $restDetailResin3,
			'restDetailPlus3' => $restDetailPlus3,
			'restDetailPlus4' => $restDetailPlus4
		);
		
		$this->load->view('Production/modalReal1New', $data);
	}
	
	public function save_update_produksi_1(){
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$est_real				= $data['est_real'];
		$id_production_detail 	= $data['id_production_detail'];
		$id_milik 				= $data['id_milik'];
		$qty_awal 				= $data['qty_awal'];
		$qty_akhir 				= $data['qty_akhir'];
		$production_date 		= $data['production_date'];
		$finish_production_date = $data['finish_production_date'];
		$terima_spk_date 		= $data['terima_spk_date'];
		$id_gudang 				= $data['id_gudang'];

		if(!empty($data['DetailUtama'])){
			$DetailUtama	= $data['DetailUtama'];
		}

		if(!empty($data['DetailUtama2'])){
			$DetailUtama2	= $data['DetailUtama2'];
		}

		if(!empty($data['DetailUtama2N1'])){
			$DetailUtama2N1	= $data['DetailUtama2N1'];
		}

		if(!empty($data['DetailUtama2N2'])){
			$DetailUtama2N2	= $data['DetailUtama2N2'];
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

		if(!empty($data['DetailResin2N1'])){
			$DetailResin2N1	= $data['DetailResin2N1'];
		}

		if(!empty($data['DetailResin2N2'])){
			$DetailResin2N2	= $data['DetailResin2N2'];
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

		if(!empty($data['DetailPlus2N1'])){
			$DetailPlus2N1	= $data['DetailPlus2N1'];
		}

		if(!empty($data['DetailPlus2N2'])){
			$DetailPlus2N2	= $data['DetailPlus2N2'];
		}

		if(!empty($data['DetailPlus3'])){
			$DetailPlus3	= $data['DetailPlus3'];
		}

		if(!empty($data['DetailPlus4'])){
			$DetailPlus4	= $data['DetailPlus4'];
		}

		// echo "<pre>";`
		$ArrUpdateStock = array();
		$uniq_stock = 0;
		if(!empty($data['DetailUtama'])){
			$ArrDetailUtama	= array();
			foreach($DetailUtama AS $val => $valx){ $uniq_stock++;
				$ArrDetailUtama[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailUtama[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailUtama);
		}

		if(!empty($data['DetailUtama2'])){
			$ArrDetailUtama2	= array();
			foreach($DetailUtama2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailUtama2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama2[$val]['benang'] = (!empty($valx['benang']))?$valx['benang']:'';
				$ArrDetailUtama2[$val]['bw'] = (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetailUtama2[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailUtama2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailUtama2);
		}

		if(!empty($data['DetailUtama2N1'])){
			$ArrDetailUtama2N1	= array();
			foreach($DetailUtama2N1 AS $val => $valx){ $uniq_stock++;
				$ArrDetailUtama2N1[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama2N1[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama2N1[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama2N1[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama2N1[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama2N1[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama2N1[$val]['benang'] = (!empty($valx['benang']))?$valx['benang']:'';
				$ArrDetailUtama2N1[$val]['bw'] = (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetailUtama2N1[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama2N1[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailUtama2N1[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama2N1[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailUtama2N1);
		}

		if(!empty($data['DetailUtama2N2'])){
			$ArrDetailUtama2N2	= array();
			foreach($DetailUtama2N2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailUtama2N2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama2N2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama2N2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama2N2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama2N2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama2N2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama2N2[$val]['benang'] = (!empty($valx['benang']))?$valx['benang']:'';
				$ArrDetailUtama2N2[$val]['bw'] = (!empty($valx['bw']))?$valx['bw']:'';
				$ArrDetailUtama2N2[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama2N2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailUtama2N2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama2N2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailUtama2N2);
		}

		if(!empty($data['DetailUtama3'])){
			$ArrDetailUtama3	= array();
			foreach($DetailUtama3 AS $val => $valx){ $uniq_stock++;
				$ArrDetailUtama3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailUtama3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailUtama3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailUtama3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailUtama3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailUtama3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailUtama3[$val]['layer'] = $valx['layer'];
				$ArrDetailUtama3[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailUtama3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailUtama3[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailUtama3);
		}

		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin2N1'])){
			$ArrDetailResin2N1	= array();
			foreach($DetailResin2N1 AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin2N1[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2N1[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2N1[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2N1[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2N1[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2N1[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2N1[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin2N1[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2N1[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin2N1);
		}

		if(!empty($data['DetailResin2N2'])){
			$ArrDetailResin2N2	= array();
			foreach($DetailResin2N2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin2N2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2N2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2N2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2N2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2N2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2N2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2N2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin2N2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2N2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin2N2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus2N1'])){
			$ArrDetailPlus2N1	= array();
			foreach($DetailPlus2N1 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus2N1[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2N1[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2N1[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2N1[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2N1[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2N1[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2N1[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus2N1[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2N1[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus2N1);
		}


		if(!empty($data['DetailPlus2N2'])){
			$ArrDetailPlus2N2	= array();
			foreach($DetailPlus2N2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus2N2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2N2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2N2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2N2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2N2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2N2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2N2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus2N2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2N2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus2N2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['actual_type'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus4);
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		foreach ($temp as $key => $value) {
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			
			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrStock[$key]['update_date'] 	= date('Y-m-d H:i:s');

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['id_gudang_dari'] 	= NULL;
				$ArrHist[$key]['kd_gudang_dari'] 	= NULL;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $id_produksi;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi';
				$ArrHist[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist[$key]['update_date'] 		= date('Y-m-d H:i:s');
			}
		}


		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'est_real' => $est_real,
			'production_date' => $production_date,
			'finish_production_date' => $finish_production_date,
			'terima_spk_date' => $terima_spk_date,
			'upload_real' => "Y",
			'upload_by' => $data_session['ORI_User']['username'],
			'upload_date' => date('Y-m-d H:i:s')
			// 'print_merge' => "Y",
			// 'print_merge_by' => $data_session['ORI_User']['username'],
			// 'print_merge_date' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			if(!empty($qty_akhir)){
				$this->db->where('id_milik', $id_milik)
						->where('id_produksi', $id_produksi)
						->where('product_ke >=',$qty_awal)
						->where('product_ke <=',$qty_akhir)
						->update('production_detail', $dataDetailPro);
			}
			else{
				$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);
			}

			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}

			//Utama
			if(!empty($ArrDetailUtama)){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailUtama);
			}
			if(!empty($ArrDetailUtama2)){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailUtama2);
			}
			if(!empty($data['DetailUtama3'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailUtama3);
			}
			if(!empty($data['DetailUtama2N1'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailUtama2N1);
			}
			if(!empty($data['DetailUtama2N2'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailUtama2N2);
			}
			//Resin
			if(!empty($ArrDetailResin)){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin);
			}
			if(!empty($ArrDetailResin2)){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin2);
			}
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin3);
			}
			if(!empty($data['DetailResin2N1'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin2N1);
			}
			if(!empty($data['DetailResin2N2'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin2N2);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus);
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus2N1'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus2N1);
			}
			if(!empty($data['DetailPlus2N2'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus2N2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus4);
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
			if(!empty($qty_akhir)){
				history('Add Real Production 1 '.$id_produksi.'/'.$product.' / '.$id_production_detail.' / product: '.$qty_awal.'-'.$qty_akhir);
			}
			else{
				history('Add Real Production 1 '.$id_produksi.'/'.$product.' / '.$id_production_detail);
			}

		}
		echo json_encode($Arr_Kembali);
	}
	
	//UPDATE MIXSING
	
	public function update_produksi_2(){
		if($this->input->post()){
			$data = $this->input->post();
			$data_session	= $this->session->userdata;
			$id_produksi	= $data['id_produksi'];
			$Imp			= explode('-', $id_produksi);

			$dataUpdate = array(
				'real_start_produksi' => $data['real_start_produksi'],
				'real_end_produksi' => $data['real_end_produksi'],
				'sts_produksi' => 'FINISH',
				'modified_by' => $data_session['ORI_User']['username'],
				'modified_date' => date('Y-m-d H:i:s')
			);

			$dtUpdIpp = array(
				'status' => 'FINISH',
			);

			$this->db->trans_start();
			$this->db->where('id_produksi', $id_produksi)->update('production_header', $dataUpdate);
			$this->db->where('no_ipp', $Imp[1])->update('production', $dtUpdIpp);
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

			$HelpDet 	= "bq_detail_header";
			if($row[0]['jalur'] == 'FD'){
				$HelpDet = "so_detail_header";
			}

			// $qDetail	= "	SELECT
								// a.*,
								// b.no_komponen
							// FROM
								// production_detail a LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
							// WHERE
								// a.id_produksi = '".$id_produksi."' ";
			$qDetail = "( SELECT
							min( a.product_ke ) AS qty_awal,
							max( a.product_ke ) AS qty_akhir,
							a.*,
							b.no_komponen
							FROM
								production_detail a
								LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."'
								AND a.print_merge_date <> ''
							GROUP BY
								a.print_merge_date
							)
							UNION
							(
							SELECT
								a.product_ke  AS qty_awal,
								a.product_ke  AS qty_akhir,
								a.*,
								b.no_komponen
							FROM
								production_detail a
								LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
							WHERE
								a.id_produksi = '".$id_produksi."'
								AND ( a.print_merge_date = '' OR a.print_merge_date IS NULL )
							)";
			// echo $qDetail;
			$rowD	= $this->db->query($qDetail)->result_array();

			$qDetailBtn	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_category <> 'pipe slongsong' AND a.id_produksi = '".$id_produksi."' AND a.upload_real = 'N' ";
			$rowDBtn	= $this->db->query($qDetailBtn)->num_rows();

			$qDetailBtn2	= "	SELECT a.*, b.nm_product FROM production_detail a LEFT JOIN component_header b ON a.id_product=b.id_product WHERE a.id_category <> 'pipe slongsong' AND a.id_produksi = '".$id_produksi."' AND a.upload_real2 = 'N' ";
			$rowDBtn2	= $this->db->query($qDetailBtn2)->num_rows();
			// echo $qDetailBtn;
			$data = array(
				'title'		=> 'Upload Production Mixing',
				'action'	=> 'updateReal',
				'row'		=> $row,
				'rowD'		=> $rowD,
				'numB'		=> $rowDBtn,
				'numB2'		=> $rowDBtn2
			);
			$this->load->view('Production/updateRealNew3',$data);
		}
	}
	
	public function modal_update_produksi_2(){
		$id_product = $this->uri->segment(3);
		$id_produksi = $this->uri->segment(4);
		$idProducktion = $this->uri->segment(5);
		$id_milik = $this->uri->segment(6);
		$qty_awal = $this->uri->segment(7);
		$qty_akhir = $this->uri->segment(8);
		// echo $id_product;

		$qty_total = ($qty_akhir - $qty_awal) + 1;
		
		$qProduksi		= "SELECT * FROM production_header WHERE id_produksi='".$id_produksi."' ";
		$restProduksi	= $this->db->query($qProduksi)->result_array();
		
		$HelpDet 	= "bq_detail_header";
		$HelpDet_BCH 	= "bq_component_header";
		$HelpDet_BCD 	= "bq_component_detail";
		$HelpDet_BCDP 	= "bq_component_detail_plus";
		$HelpDet_BCDA 	= "bq_component_detail_add";
		if($restProduksi[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
			$HelpDet_BCH 	= "so_component_header";
			$HelpDet_BCD 	= "so_component_detail";
			$HelpDet_BCDP 	= "so_component_detail_plus";
			$HelpDet_BCDA 	= "so_component_detail_add";
		}
		
		$qHeader			= "SELECT * FROM ".$HelpDet_BCH." WHERE id_product='".$id_product."'";
		$restHeader			= $this->db->query($qHeader)->result_array();
		
		//LINER
		$qDetail1			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$qDetail1		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' AND (id_category != 'TYP-0002' AND id_category != 'TYP-0001')";
		}
		$restDetail1		= $this->db->query($qDetail1)->result_array(); 
		$qDetailResin1		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$qDetailResin1		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0001') LIMIT 1";
		}
		$restDetailResin1	= $this->db->query($qDetailResin1)->result_array(); 
		$qDetailPlus1		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002'";
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$qDetailPlus1		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' AND (id_category != 'TYP-0002' AND id_category != 'TYP-0001')";
		}
		$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
		$qDetailAdd1		= "SELECT * FROM ".$HelpDet_BCDA." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB'";
		$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
		
		//STRUKTURE
		$qDetail2			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		$restDetail2		= $this->db->query($qDetail2)->result_array();
		$qDetailResin2		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin2	= $this->db->query($qDetailResin2)->result_array();
		$qDetailPlus2		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002'";
		$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
		$qDetailAdd2		= "SELECT * FROM ".$HelpDet_BCDA." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS'";
		$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
		
		
		//STRUKTURE NECK 1
		$qDetail2N1			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		$restDetail2N1		= $this->db->query($qDetail2N1)->result_array();
		$qDetailResin2N1	= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin2N1	= $this->db->query($qDetailResin2N1)->result_array();
		$qDetailPlus2N1		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002'";
		$restDetailPlus2N1	= $this->db->query($qDetailPlus2N1)->result_array();
		$qDetailAdd2N1		= "SELECT * FROM ".$HelpDet_BCDA." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1'";
		$restDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->result_array();
		
		//STRUKTURE NECK 2
		$qDetail2N2			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		$restDetail2N2		= $this->db->query($qDetail2N2)->result_array();
		$qDetailResin2N2	= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin2N2	= $this->db->query($qDetailResin2N2)->result_array();
		$qDetailPlus2N2		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002'";
		$restDetailPlus2N2	= $this->db->query($qDetailPlus2N2)->result_array();
		$qDetailAdd2N2		= "SELECT * FROM ".$HelpDet_BCDA." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2'";
		$restDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->result_array();
		
		
		//EXTERNAL
		$qDetail3			= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
		$restDetail3		= $this->db->query($qDetail3)->result_array();
		$qDetailResin3		= "SELECT * FROM ".$HelpDet_BCD." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
		$restDetailResin3	= $this->db->query($qDetailResin3)->result_array();
		$qDetailPlus3		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002'";
		$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
		$qDetailAdd3		= "SELECT * FROM ".$HelpDet_BCDA." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS'";
		$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
		
		//TOPCOAT
		$qDetailResin4		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000' AND id_category ='TYP-0001'";
		$restDetailResin4	= $this->db->query($qDetailResin4)->result_array();
		$qDetailPlus4		= "SELECT * FROM ".$HelpDet_BCDP." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002' AND id_category <> 'TYP-0001'";
		$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
		$qDetailAdd4		= "SELECT * FROM ".$HelpDet_BCDA." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='TOPCOAT'";
		$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
		
		$getWherehouse = $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		$data = array(
			'id_product' => $id_product,
			'id_produksi' => $id_produksi,
			'idProducktion' => $idProducktion,
			'id_milik' => $id_milik,
			'qty_awal' => $qty_awal,
			'qty_akhir' => $qty_akhir,
			'qty_total' => $qty_total,
			'warehouse' => $getWherehouse,
			'restProduksi' => $restProduksi,
			'restHeader' => $restHeader,
			'restDetail1' => $restDetail1,
			'restDetailResin1' => $restDetailResin1,
			'restDetailPlus1' => $restDetailPlus1,
			'restDetailAdd1' => $restDetailAdd1,
			'restDetail2' => $restDetail2,
			'restDetailResin2' => $restDetailResin2,
			'restDetailPlus2' => $restDetailPlus2,
			'restDetailAdd2' => $restDetailAdd2,
			'restDetail2N1' => $restDetail2N1,
			'restDetailResin2N1' => $restDetailResin2N1,
			'restDetailPlus2N1' => $restDetailPlus2N1,
			'restDetailAdd2N1' => $restDetailAdd2N1,
			'restDetail2N2' => $restDetail2N2,
			'restDetailResin2N2' => $restDetailResin2N2,
			'restDetailPlus2N2' => $restDetailPlus2N2,
			'restDetailAdd2N2' => $restDetailAdd2N2,
			'restDetail3' => $restDetail3,
			'restDetailResin3' => $restDetailResin3,
			'restDetailPlus3' => $restDetailPlus3,
			'restDetailAdd3' => $restDetailAdd3,
			'restDetailResin4' => $restDetailResin4,
			'restDetailPlus4' => $restDetailPlus4,
			'restDetailAdd4' => $restDetailAdd4,
		);
		
		$this->load->view('Production/modalReal3New', $data);
	}
	
	public function save_update_produksi_2(){
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$product				= $data['product'];
		$id_production_detail 	= $data['id_production_detail'];
		$id_milik 				= $data['id_milik'];
		$qty_awal 				= $data['qty_awal'];
		$qty_akhir 				= $data['qty_akhir'];
		$id_gudang 				= $data['id_gudang'];
		
		$sqlChe 	= "SELECT id_category FROM production_detail WHERE id='".$id_production_detail."' LIMIT 1";
		$restData 	= $this->db->query($sqlChe)->result();
		$parentP 	= $restData[0]->id_category;
		// echo $parentP; exit;

		if(!empty($data['DetailResin'])){
			$DetailResin	= $data['DetailResin'];
		}

		if(!empty($data['DetailResin2'])){
			$DetailResin2	= $data['DetailResin2'];
		}

		if(!empty($data['DetailResin2N1'])){
			$DetailResin2N1	= $data['DetailResin2N1'];
		}

		if(!empty($data['DetailResin2N2'])){
			$DetailResin2N2	= $data['DetailResin2N2'];
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

		if(!empty($data['DetailPlus2N1'])){
			$DetailPlus2N1	= $data['DetailPlus2N1'];
		}

		if(!empty($data['DetailPlus2N2'])){
			$DetailPlus2N2	= $data['DetailPlus2N2'];
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

		if(!empty($data['DetailAdd2N1'])){
			$DetailAdd2N1		= $data['DetailAdd2N1'];
		}

		if(!empty($data['DetailAdd2N2'])){
			$DetailAdd2N2		= $data['DetailAdd2N2'];
		}

		if(!empty($data['DetailAdd3'])){
			$DetailAdd3		= $data['DetailAdd3'];
		}

		if(!empty($data['DetailAdd4'])){
			$DetailAdd4		= $data['DetailAdd4'];
		}

		// echo "<pre>";
		$ArrUpdateStock = array();
		$uniq_stock = 0;
		if(!empty($data['DetailResin'])){
			$ArrDetailResin	= array();
			foreach($DetailResin AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin);
		}

		if(!empty($data['DetailResin2'])){
			$ArrDetailResin2	= array();
			foreach($DetailResin2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin2);
		}

		if(!empty($data['DetailResin2N1'])){ $uniq_stock++;
			$ArrDetailResin2N1	= array();
			foreach($DetailResin2N1 AS $val => $valx){
				$ArrDetailResin2N1[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2N1[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2N1[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2N1[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2N1[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2N1[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2N1[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin2N1[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2N1[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin2N1);
		}

		if(!empty($data['DetailResin2N2'])){
			$ArrDetailResin2N2	= array();
			foreach($DetailResin2N2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin2N2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin2N2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin2N2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin2N2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin2N2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin2N2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin2N2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin2N2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin2N2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin2N2);
		}

		if(!empty($data['DetailResin3'])){
			$ArrDetailResin3	= array();
			foreach($DetailResin3 AS $val => $valx){ $uniq_stock++;
				$ArrDetailResin3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailResin3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailResin3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailResin3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailResin3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailResin3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailResin3[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailResin3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailResin3[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailResin3);
		}

		if(!empty($data['DetailPlus'])){
			$ArrDetailPlus	= array();
			foreach($DetailPlus AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus);
		}

		if(!empty($data['DetailPlus2'])){
			$ArrDetailPlus2	= array();
			foreach($DetailPlus2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus2);
		}

		if(!empty($data['DetailPlus2N1'])){
			$ArrDetailPlus2N1	= array();
			foreach($DetailPlus2N1 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus2N1[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2N1[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2N1[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2N1[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2N1[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2N1[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2N1[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus2N1[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2N1[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus2N1);
		}

		if(!empty($data['DetailPlus2N2'])){
			$ArrDetailPlus2N2	= array();
			foreach($DetailPlus2N2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus2N2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus2N2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus2N2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus2N2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus2N2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus2N2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus2N2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus2N2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus2N2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus2N2);
		}

		if(!empty($data['DetailPlus3'])){
			$ArrDetailPlus3	= array();
			foreach($DetailPlus3 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus3[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus3[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus3);
		}

		if(!empty($data['DetailPlus4'])){
			$ArrDetailPlus4	= array();
			foreach($DetailPlus4 AS $val => $valx){ $uniq_stock++;
				$ArrDetailPlus4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailPlus4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailPlus4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailPlus4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailPlus4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailPlus4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailPlus4[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailPlus4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailPlus4[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailPlus4);
		}

		if(!empty($data['DetailAdd'])){
			$ArrDetailAdd	= array();
			foreach($DetailAdd AS $val => $valx){ $uniq_stock++;
				$ArrDetailAdd[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailAdd);
		}

		if(!empty($data['DetailAdd2'])){
			$ArrDetailAdd2	= array();
			foreach($DetailAdd2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailAdd2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailAdd2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailAdd2);
		}

		if(!empty($data['DetailAdd2N1'])){
			$ArrDetailAdd2N1	= array();
			foreach($DetailAdd2N1 AS $val => $valx){ $uniq_stock++;
				$ArrDetailAdd2N1[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd2N1[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd2N1[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd2N1[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd2N1[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd2N1[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd2N1[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailAdd2N1[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd2N1[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailAdd2N1);
		}

		if(!empty($data['DetailAdd2N2'])){
			$ArrDetailAdd2N2	= array();
			foreach($DetailAdd2N2 AS $val => $valx){ $uniq_stock++;
				$ArrDetailAdd2N2[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd2N2[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd2N2[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd2N2[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd2N2[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd2N2[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd2N2[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailAdd2N2[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd2N2[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailAdd2N2);
		}

		if(!empty($data['DetailAdd3'])){
			$ArrDetailAdd3	= array();
			foreach($DetailAdd3 AS $val => $valx){ $uniq_stock++;
				$ArrDetailAdd3[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd3[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd3[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd3[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd3[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd3[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd3[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailAdd3[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd3[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailAdd3);
		}

		if(!empty($data['DetailAdd4'])){
			$ArrDetailAdd4	= array();
			foreach($DetailAdd4 AS $val => $valx){ $uniq_stock++;
				$ArrDetailAdd4[$val]['id_produksi'] = $id_produksi;
				$ArrDetailAdd4[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetailAdd4[$val]['id_product'] = $valx['id_product'];
				$ArrDetailAdd4[$val]['id_production_detail'] = $id_production_detail;
				$ArrDetailAdd4[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetailAdd4[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetailAdd4[$val]['material_terpakai'] = str_replace(',','',$valx['material_terpakai']);
				$ArrDetailAdd4[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetailAdd4[$val]['status_date'] = date('Y-m-d H:i:s');

				$ArrUpdateStock[$uniq_stock]['id'] 	= $valx['batch_number'];
				$ArrUpdateStock[$uniq_stock]['qty'] = str_replace(',','',$valx['material_terpakai']);
			}
			// print_r($ArrDetailAdd4);
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		foreach ($temp as $key => $value) {
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			
			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrStock[$key]['update_date'] 	= date('Y-m-d H:i:s');

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['id_gudang_dari'] 	= NULL;
				$ArrHist[$key]['kd_gudang_dari'] 	= NULL;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $id_produksi;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi';
				$ArrHist[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist[$key]['update_date'] 		= date('Y-m-d H:i:s');
			}
		}


		// echo "</pre>";
		// exit;

		$dataDetailPro = array(
			'upload_real2' => "Y",
			'upload_by2' => $data_session['ORI_User']['username'],
			'upload_date2' => date('Y-m-d H:i:s')
			// 'print_merge2' => "Y",
			// 'print_merge2_by' => $data_session['ORI_User']['username'],
			// 'print_merge2_date' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
			if(!empty($qty_akhir)){
				$this->db->where('id_milik', $id_milik)
						->where('id_produksi', $id_produksi)
						->where('product_ke >=',$qty_awal)
						->where('product_ke <=',$qty_akhir)
						->update('production_detail', $dataDetailPro);
			}
			else{
				$this->db->where('id', $id_production_detail)->update('production_detail', $dataDetailPro);
			}

			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}
			
			//Resin
			if(!empty($ArrDetailResin)){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin);
			}
			if(!empty($ArrDetailResin2)){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin2);
			}
			if(!empty($data['DetailResin2N1'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin2N1);
			}
			if(!empty($data['DetailResin2N2'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin2N2);
			}
			if(!empty($data['DetailResin3'])){
				$this->db->insert_batch('tmp_production_real_detail', $ArrDetailResin3);
			}
			//Detail Plus
			if(!empty($data['DetailPlus'])){
				if($parentP == 'shop joint' OR $parentP == 'branch joint' OR $parentP == 'field joint'){
					$this->db->insert_batch('tmp_production_real_detail', $ArrDetailPlus);
				}
				else{
					$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus);
				}
			}
			if(!empty($data['DetailPlus2'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus2);
			}
			if(!empty($data['DetailPlus2N1'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus2N1);
			}
			if(!empty($data['DetailPlus2N2'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus2N2);
			}
			if(!empty($data['DetailPlus3'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus3);
			}
			if(!empty($data['DetailPlus4'])){
				$this->db->insert_batch('tmp_production_real_detail_plus', $ArrDetailPlus4);
			}
			//Detail Add
			if(!empty($data['DetailAdd'])){
				$this->db->insert_batch('tmp_production_real_detail_add', $ArrDetailAdd);
			}
			if(!empty($data['DetailAdd2'])){
				$this->db->insert_batch('tmp_production_real_detail_add', $ArrDetailAdd2);
			}
			if(!empty($data['DetailAdd2N1'])){
				$this->db->insert_batch('tmp_production_real_detail_add', $ArrDetailAdd2N1);
			}
			if(!empty($data['DetailAdd2N2'])){
				$this->db->insert_batch('tmp_production_real_detail_add', $ArrDetailAdd2N2);
			}
			if(!empty($data['DetailAdd3'])){
				$this->db->insert_batch('tmp_production_real_detail_add', $ArrDetailAdd3);
			}
			if(!empty($data['DetailAdd4'])){
				$this->db->insert_batch('tmp_production_real_detail_add', $ArrDetailAdd4);
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

			if(!empty($qty_akhir)){
				history('Add Real Production 2 '.$id_produksi.'/'.$product.' / '.$id_production_detail.' / product: '.$qty_awal.'-'.$qty_akhir);
			}
			else{
				history('Add Real Production 2 '.$id_produksi.'/'.$product.' / '.$id_production_detail);
			}


		}
		echo json_encode($Arr_Kembali);
	}
	
	public function get_data_json_update_produksi_1(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_update_produksi_1(
			$requestData['id_produksi'],
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
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_komponen'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['id_category'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			if($row['qty_awal'] <> $row['qty_akhir']){
				$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['qty_awal']." - ".$row['qty_akhir']."</span></div>";
			}
			if($row['qty_awal'] == $row['qty_akhir']){
				$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$row['product_ke']."</span></div>";
			}


					$btn1	= "";
					$btn2	= "";
					$btn3	= "";
					$btn4	= "";
					$btn5	= "";
					$btn6	= "";
					$btn7	= "";
					if($row['sts_produksi'] == 'Y'){
						$jumlah = $row['upload_real'];
						if($jumlah == 'N'){
							$btn6	= "&nbsp;<button type='button' class='btn btn-sm btn-success' id='inputReal1New' title='SPK 1 ".ucwords(strtolower($row['id_category']))." ke (".$row['product_ke'].")' data-id_product='".$row['id_product']."' data-id_produksi='".$row['id_produksi']."' data-id_producktion='".$row['id']."' data-id_milik='".$row['id_milik']."' data-awal='".$row['qty_awal']."' data-akhir='".$row['qty_akhir']."'><i class='fa fa-edit'></i></button>";
						}
						else{
							$btn2	= "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
						}
					}
					else{
						$btn5	= "&nbsp;<button type='button' class='btn btn-sm btn-danger' title='SPK belum turun !!!'><i class='fa fa-close'></i></button>";
					}

					if($row['upload_real'] == 'Y' AND $row['upload_real2'] == 'Y'){
						$btn7	= "<a href='".site_url($this->uri->segment(1).'/printRealProduction/'.$row['id_produksi'].'/'.$row['id_product'].'/'.$row['product_ke'].'/'.$row['id'].'/'.$row['id_delivery'].'/'.$row['id_milik'])."' class='btn btn-sm btn-success' target='_blank' title='Print Comparison' data-role='qtip'><i class='fa fa-print'></i></a>";
						$btn3	= "&nbsp;<button type='button' class='btn btn-sm btn-primary Perbandingan' data-awal='".$row['qty_awal']."' data-akhir='".$row['qty_akhir']."' data-id_product = '".$row['id_product']."' data-id_pro_detail = '".$row['id']."' data-id_produksi = '".$row['id_produksi']."' data-id_milik = '".$row['id_milik']."' title='Production Comparison'><i class='fa fa-balance-scale '></i></button>";
					}

			$nestedData[]	= "<div align='left'>
									".$btn1."
									".$btn6."
									".$btn2."


									".$btn4."
									".$btn5."
									".$btn7."
									".$btn3."
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

	public function query_data_update_produksi_1($id_produksi, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_komponen
			FROM
				update_real_list a LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id,
				(SELECT @row:=0) r
			WHERE
				a.id_produksi = '".$id_produksi."'
				AND b.id_category <> 'pipe slongsong'
				AND (
					a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.no_komponen LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_komponen',
			2 => 'id_category',
			3 => 'id_product'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modal_actual_vs_real(){
		$id_product 	= $this->uri->segment(3);
		$id_milik 		= $this->uri->segment(4);
		$id_produksi 	= $this->uri->segment(5);
		$qty_awal 		= floatval($this->uri->segment(6));
		$qty_akhir 		= floatval($this->uri->segment(7));

		$qty_total = ($qty_akhir - $qty_awal) + 1;
		
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_component_header";
		$HelpDet2 	= "banding_mat";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_component_header";
			$HelpDet2 	= "fd_banding_mat";
		}
		
		$qHeader		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."'";
		$qDetail1		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
		$qDetail2		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
		$qDetail3		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
		$qDetail4		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
		$detailResin1	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin3	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		
		$restHeader		= $this->db->query($qHeader)->result_array();
		$product_parent = $restHeader[0]['parent_product'];

		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$restDetail4	= $this->db->query($qDetail4)->result_array();
		$restResin1		= $this->db->query($detailResin1)->result_array();
		$restResin2		= $this->db->query($detailResin2)->result_array();
		$restResin3		= $this->db->query($detailResin3)->result_array();
		
		//tambahan flange mould /slongsong
		$restDetail2N1	= array();
		$restDetail2N2	= array();
		$restResin2N1	= array();
		$restResin2N2	= array();

		if($product_parent == 'flange mould' OR $product_parent == 'flange slongsong' OR $product_parent == 'colar' OR $product_parent == 'colar slongsong'){
			$qDetail2N1		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
			$qDetail2N2		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
			$detailResin2N1	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
			$detailResin2N2	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
			
			$restDetail2N1	= $this->db->query($qDetail2N1)->result_array();
			$restDetail2N2	= $this->db->query($qDetail2N2)->result_array();
			$restResin2N1	= $this->db->query($detailResin2N1)->result_array();
			$restResin2N2	= $this->db->query($detailResin2N2)->result_array();
		}
		
		$data = array(
			'id_product' => $id_product,
			'id_milik' => $id_milik,
			'id_produksi' => $id_produksi,
			'qty_awal' => $qty_awal,
			'qty_akhir' => $qty_akhir,
			'qty_total' => $qty_total,
			'row' => $row,
			
			'restHeader' => $restHeader,
			'restDetail1' => $restDetail1,
			'restDetail2' => $restDetail2,
			'restDetail3' => $restDetail3,
			'restDetail4' => $restDetail4,
			'restResin1' => $restResin1,
			'restResin2' => $restResin2,
			'restResin3' => $restResin3,
			'restDetail2N1' => $restDetail2N1,
			'restDetail2N2' => $restDetail2N2,
			'restResin2N1' => $restResin2N1,
			'restResin2N2' => $restResin2N2
		);
		
		$this->load->view('Production/modalPerbandingan', $data);
	}
	
}
?>