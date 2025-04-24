<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Closing_manual_tanki extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');

		$this->tanki = $this->load->database("tanki",TRUE);
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
		
		$this->get_user = get_detail_user();
	}

    public function index(){
        $status = 0;
        if($status == 1){
            $SQL    = "SELECT kode_spk, print_merge2_date AS hist_produksi  from production_detail where id in (225845) and process_manual='1' and result_manual is null GROUP BY kode_spk, print_merge2_date order by id asc";
            $result = $this->db->query($SQL)->result_array();
			// echo $SQL;
			// exit;
            foreach ($result as $key => $value) {
                $datetime       = date('Y-m-d H:i:s');
                $kode_spk 		= $value['kode_spk'];
                $hist_produksi	= $value['hist_produksi'];
                $id_gudang      = NULL;
                $closing_date   = $datetime;

                $detail_input	= [];
                $get_detail_spk2 = $this->db
                                ->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang, a.upload_eng_change, c.no_spk AS no_spk2, c.adjustment_type AS typeTanki, c.no_so, a.closing_produksi_date')
                                ->from('production_spk_parsial a')	
                                ->join('production_spk b','a.id_spk = b.id')
                                ->join('warehouse_adjustment c',"a.kode_spk = c.kode_spk AND c.no_ipp = 'resin mixing' AND c.status_id='1'")
                                ->where('a.kode_spk',$kode_spk)
                                ->where('a.created_date',$hist_produksi)
                                ->where('c.created_date',$hist_produksi)
                                ->where('a.spk','1')
                                ->get()
                                ->result_array();
                
                foreach ($get_detail_spk2 as $keyX => $valueX) {
                    $detail_input[$keyX]['id']          = $valueX['id'];
                    $detail_input[$keyX]['id_milik']    = $valueX['id_milik'];
                    $detail_input[$keyX]['qty_all']     = $valueX['qty'];
                    $detail_input[$keyX]['qty']         = $valueX['qty_parsial'];

                    $id_gudang      = $valueX['id_gudang'];
                    $closing_date   = $valueX['closing_produksi_date'];
                }

                $dateCreated = $datetime;
                if($hist_produksi != '0'){
                    $dateCreated = $hist_produksi;
                }

                $kode_spk_created = $kode_spk.'/'.$dateCreated;

                $ArrWhereIN_= [];
                foreach ($detail_input as $key => $value) {
                    $QTY = str_replace(',','',$value['qty']);
                    if($QTY > 0){
                        $ArrWhereIN_[] = $value['id'];
                    }
                }

				if(!empty($ArrWhereIN_)){
					$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

					$nomor = 0;
					$ID_PRODUKSI_DETAIL = [];
					foreach ($get_detail_spk as $key => $value) {
						$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date'=>$dateCreated))->result();
						$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
						
						if($id_pro_det != 0){
							$ID_PRODUKSI_DETAIL[] = $id_pro_det;
						}
					}


					$ARR_ID_PRO_UNIQ = array_unique($ID_PRODUKSI_DETAIL);

					$this->closing_produksi_tanki($ARR_ID_PRO_UNIQ,$closing_date);
					$this->closing_produksi_base_jurnal($kode_spk_created,$id_gudang,14,$closing_date);

					$this->db->where('kode_spk',$kode_spk);
					$this->db->where('print_merge2_date',$dateCreated);
					$this->db->update('production_detail',['result_manual'=>1]);

					echo $kode_spk_created.' Success Process !<br>';
				}
				else{
					echo $kode_spk_created.' <b>Failed Process !</b><br>';
				}
            }
        }
		else{
			echo "Proses Stop !";
		}
    }

    public function closing_produksi_tanki($ARR_ID_PRO_UNIQ,$closing_date){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime 		= $closing_date;
		
		$HelpDet3 		= "tmp_production_real_detail";
		$HelpDet4 		= "tmp_production_real_detail_plus";
		$HelpDet5 		= "tmp_production_real_detail_add";
		// print_r($ARR_ID_PRO_UNIQ); exit;
		if(!empty($ARR_ID_PRO_UNIQ)){
			$restDetail1	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet3)->result_array();
			$restDetail2	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet4)->result_array();
			$restDetail3	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet5)->result_array();
			// exit;

			$ArrDetail = array();
			if(!empty($restDetail1)){
				foreach($restDetail1 AS $val => $valx){
					$ArrDetail[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrDetail[$val]['id_detail'] = $valx['id_detail'];
					$ArrDetail[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrDetail[$val]['id_product'] = $valx['id_product'];
					$ArrDetail[$val]['batch_number'] = $valx['batch_number'];
					$ArrDetail[$val]['actual_type'] = $valx['actual_type'];
					$ArrDetail[$val]['benang'] = $valx['benang'];
					$ArrDetail[$val]['bw'] = $valx['bw'];
					$ArrDetail[$val]['layer'] = $valx['layer'];
					$ArrDetail[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrDetail[$val]['status'] = $valx['status'];
					$ArrDetail[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrDetail[$val]['status_date'] = $dateTime;
					$ArrDetail[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrDetail[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrDetail[$val]['spk'] = $valx['spk'];
					$ArrDetail[$val]['id_spk'] = $valx['id_spk'];
					$ArrDetail[$val]['updated_by'] = $valx['updated_by'];
					$ArrDetail[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrPlus = array();
			if(!empty($restDetail2)){
				foreach($restDetail2 AS $val => $valx){
					$ArrPlus[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrPlus[$val]['id_detail'] = $valx['id_detail'];
					$ArrPlus[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrPlus[$val]['id_product'] = $valx['id_product'];
					$ArrPlus[$val]['batch_number'] = $valx['batch_number'];
					$ArrPlus[$val]['actual_type'] = $valx['actual_type'];
					$ArrPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrPlus[$val]['status'] = $valx['status'];
					$ArrPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrPlus[$val]['status_date'] = $dateTime;
					$ArrPlus[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrPlus[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrPlus[$val]['spk'] = $valx['spk'];
					$ArrPlus[$val]['id_spk'] = $valx['id_spk'];
					$ArrPlus[$val]['updated_by'] = $valx['updated_by'];
					$ArrPlus[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrAdd = array();
			if(!empty($restDetail3)){
				foreach($restDetail3 AS $val => $valx){
					$ArrAdd[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrAdd[$val]['id_detail'] = $valx['id_detail'];
					$ArrAdd[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrAdd[$val]['id_product'] = $valx['id_product'];
					$ArrAdd[$val]['batch_number'] = $valx['batch_number'];
					$ArrAdd[$val]['actual_type'] = $valx['actual_type'];
					$ArrAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrAdd[$val]['status'] = $valx['status'];
					$ArrAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrAdd[$val]['status_date'] = $dateTime;
					$ArrAdd[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrAdd[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrAdd[$val]['spk'] = $valx['spk'];
					$ArrAdd[$val]['id_spk'] = $valx['id_spk'];
					$ArrAdd[$val]['updated_by'] = $valx['updated_by'];
					$ArrAdd[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			
			// if(!empty($ArrDetail)){
			// 	$this->db->insert_batch('production_real_detail', $ArrDetail);
			// }
			// if(!empty($ArrPlus)){
			// 	$this->db->insert_batch('production_real_detail_plus', $ArrPlus);
			// }
			// if(!empty($ArrAdd)){
			// 	$this->db->insert_batch('production_real_detail_add', $ArrAdd);
			// }

			foreach ($ARR_ID_PRO_UNIQ as $value) {

				$QUERY_GET1 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";
				$QUERY_GET2 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail_plus a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";
				$QUERY_GET = $QUERY_GET1.'UNION'.$QUERY_GET2;
				$getData = $this->db->query($QUERY_GET)->result_array();
				
				if(!empty($getData)){
					$ArrWIP = array(
						'id_produksi' => $getData[0]['id_produksi'],
						'id_milik' => $getData[0]['id_milik'],
						'id_production_detail' => $getData[0]['id_production_detail'],
						'qty_akhir' => $getData[0]['qty_akhir'],
						'product_ke' => $getData[0]['product_ke'],
						'id_category' => $getData[0]['id_category'],
						'id_product' => $getData[0]['id_product'],
						'status_date' => $getData[0]['status_date'],
						'kode_trans' => $getData[0]['kode_trans'],
						'qty' => $getData[0]['qty'],
					);

					$this->save_report_wip_closing_tanki($ArrWIP,$closing_date);

				}
			}
		}
	}

    public function save_report_wip_closing_tanki($ArrData,$closing_date){
        $dateNow    = date('Y-m-d',strtotime($closing_date));
        $username       = 'manual system';
		$datetime       = $closing_date;

		$sqlkurs	= "select * from ms_kurs where tanggal <='".$dateNow."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

		$sqlEstMaterial = "SELECT SUM(berat) AS est_berat, SUM(berat*price) AS est_price FROM est_material_tanki WHERE id_det='".$ArrData['id_milik']."' GROUP BY id_det";
        $restEstMat	    = $this->db->query($sqlEstMaterial)->result_array();

		$jumTot     = ($ArrData['qty_akhir'] - $ArrData['product_ke']) + 1;

        $est_material_bef          = (!empty($restEstMat[0]['est_berat']))?$restEstMat[0]['est_berat']:0;
        $est_harga_bef             = (!empty($restEstMat[0]['est_price']))?$restEstMat[0]['est_price']:0;

        $est_material           = $est_material_bef * $jumTot;
        $est_harga              = $est_harga_bef * $jumTot;

        $pe_direct_labour           = 4.05;
        $pe_indirect_labour         = 0.34;
        $pe_machine                 = 0.02;
        $pe_consumable              = 0.2;
        $pe_foh_consumable          = 0.5;
        $pe_foh_depresiasi          = 0.5;
        $pe_biaya_gaji_non_produksi = 1;
        $pe_biaya_non_produksi      = 1;
        $pe_biaya_rutin_bulanan     = 0.5;

		$sqlBy 		= " SELECT
							NULL AS diameter,
							NULL AS diameter2,
							(a.man_power * a.total_time) AS man_hours,
							((a.man_power * a.total_time) * $pe_direct_labour) AS direct_labour,
							((a.man_power * a.total_time) * $pe_indirect_labour) AS indirect_labour,
							(a.total_time * $pe_machine) AS machine,
							0 AS mould_mandrill,
							($est_material * $pe_consumable) AS consumable,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_foh_consumable / 100 ) AS foh_consumable,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_foh_depresiasi / 100 ) AS foh_depresiasi,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_biaya_gaji_non_produksi / 100 ) AS biaya_gaji_non_produksi,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_biaya_non_produksi / 100 ) AS biaya_non_produksi,
							(
									((((a.man_power * a.total_time) * $pe_direct_labour))+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_biaya_rutin_bulanan / 100 ) AS biaya_rutin_bulanan 
						FROM
								production_detail a
						WHERE a.id_milik='".$ArrData['id_milik']."' AND a.process_manual = '1' LIMIT 1";
		
		$restBy		= $this->db->query($sqlBy)->result_array();
		
		$sqlBan         = " SELECT 
								SUM(a.material_terpakai) AS real_material, 
								SUM(a.material_terpakai*b.price) AS real_harga 
							FROM 
								production_real_detail a
								INNER JOIN est_material_tanki b ON a.id_detail=b.id
							WHERE a.id_production_detail='".$ArrData['id_production_detail']."' 
							GROUP BY a.id_production_detail";
		$restBan	= $this->db->query($sqlBan)->result_array();

		$real_material          = (!empty($restBan[0]['real_material']))?$restBan[0]['real_material']:0;
        $real_harga             = (!empty($restBan[0]['real_harga']))?$restBan[0]['real_harga']:0;
        $real_harga_rp          = $real_harga * $kurs;
		// echo $sqlEst."<br>";
		
		
		$sqlInsertDet = "INSERT INTO laporan_wip_per_hari_action
							(id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
							qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
							real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
							consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
							biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs,kode_trans)
							VALUE
							('".$ArrData['id_produksi']."','".$ArrData['id_category']."','".$ArrData['id_product']."',
							'".$restBy[0]['diameter']."','".$restBy[0]['diameter2']."','0',
							'0','".$ArrData['status_date']."','".$ArrData['product_ke']."',
							'".$ArrData['qty_akhir']."','".$ArrData['qty']."','".date('Y-m-d',strtotime($ArrData['status_date']))."','".$ArrData['id_production_detail']."',
							'".$ArrData['id_milik']."','".$est_material."','".$est_harga."',
							'".$real_material."','".$real_harga."','".$restBy[0]['direct_labour'] * $jumTot."',
							'".$restBy[0]['indirect_labour'] * $jumTot."','".$restBy[0]['machine'] * $jumTot."',
							'".$restBy[0]['mould_mandrill'] * $jumTot."','".$restBy[0]['consumable'] * $jumTot."',
							'".$restBy[0]['foh_consumable'] * $jumTot."','".$restBy[0]['foh_depresiasi'] * $jumTot."',
							'".$restBy[0]['biaya_gaji_non_produksi'] * $jumTot."','".$restBy[0]['biaya_non_produksi'] * $jumTot."',
							'".$restBy[0]['biaya_rutin_bulanan'] * $jumTot."','".$username."','".$datetime."','".$restBy[0]['man_hours'] * $jumTot."','".$real_harga_rp."','".$kurs."','".$ArrData['kode_trans']."')
						";
		// echo $sqlInsertDet.'<br>';
		// exit;
		$this->db->query($sqlInsertDet);
	}

    public function closing_produksi_base_jurnal($kode_spk_time,$id_gudang,$id_gudang_ke,$closing_date){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username       = 'manual system';
		$datetime       = $closing_date;
		
		$restDetail1	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();
		$restDetail2	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail_plus',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();
		$restDetail3	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail_add',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();

		$restDetail		= array_merge($restDetail1,$restDetail2,$restDetail3);
		$dateKurs       = date('Y-m-d',strtotime($closing_date));
		$GET_COSTBOOK   = getPriceBookByDateproduksi($dateKurs);
		$GET_MAERIALS   = get_detail_material();
		$GET_MATERIAL	= get_detail_material();
		//KURS
		$sqlkurs	= "select * from ms_kurs where tanggal <='".$dateKurs."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

		$temp = [];
		$tempMaterial = [];
		$ArrIDSPK = [];
		$ArrUpdateStock = [];
		$SUM_MATERIAL = 0;
		$QTY_OKE = 0;
		foreach ($restDetail as $key => $value) {
			$UNIQ = $value['kode_trans'].'-'.$value['id_material'];

			if(!array_key_exists($UNIQ, $temp)) {
				$temp[$UNIQ]['berat'] = 0;
			}
			$temp[$UNIQ]['berat'] += $value['berat'];

			$temp[$UNIQ]['tanggal'] 	= $dateKurs;
			$temp[$UNIQ]['no_ipp'] 		= $value['no_ipp'];
			$temp[$UNIQ]['id_pro_det'] 	= $value['id_pro_det'];
			$temp[$UNIQ]['id_material'] = $value['id_material'];

			$nm_material = (!empty($GET_MAERIALS[$value['id_material']]['nm_material']))?$GET_MAERIALS[$value['id_material']]['nm_material']:'';
			$temp[$UNIQ]['nm_material'] = $nm_material;
			$temp[$UNIQ]['id_spk'] 		= $value['id_spk'];
			$temp[$UNIQ]['kode_trans'] 	= $value['kode_trans'];
			$temp[$UNIQ]['keterangan']	= "Gudang Produksi to WIP";

			$getDetailSPK = $this->db->get_where('production_spk',array('id'=>$value['id_spk']))->result_array();
			$temp[$UNIQ]['no_so'] 		= (!empty($getDetailSPK[0]['product_code']))?substr($getDetailSPK[0]['product_code'],0,9):'';
			$temp[$UNIQ]['product'] 	= (!empty($getDetailSPK[0]['product']))?$getDetailSPK[0]['product']:'';
			$temp[$UNIQ]['no_spk'] 		= (!empty($getDetailSPK[0]['no_spk']))?$getDetailSPK[0]['no_spk']:'';
			$temp[$UNIQ]['id_milik']	= (!empty($getDetailSPK[0]['id_milik']))?$getDetailSPK[0]['id_milik']:'';

			$costbook 	= (!empty($GET_COSTBOOK[$value['id_material']]))?$GET_COSTBOOK[$value['id_material']]:0;
			$berat 		= $temp[$UNIQ]['berat'];
			// $SUM_MATERIAL += round($costbook * $berat);
			
			$temp[$UNIQ]['costbook'] 		= $costbook;
			$temp[$UNIQ]['kurs'] 			= $kurs;
			$temp[$UNIQ]['total_price'] 	= round($costbook * $berat);
			$temp[$UNIQ]['total_price_debet'] 	= 0;
			$temp[$UNIQ]['created_by'] 		= $username;
			$temp[$UNIQ]['created_date'] 	= $datetime;

			$ArrUpdateStock[$UNIQ]['id'] 	= $value['id_material'];
			$ArrUpdateStock[$UNIQ]['qty'] 	= $berat;

			$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans']))->result_array();
			$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;
			$temp[$UNIQ]['id_trans'] = $id_trans;

			$ArrIDSPK[$value['id_pro_det']] = $value['id_pro_det'];


			//Group Material
			$UNIQ2 = $value['id_material'];
			if(!array_key_exists($UNIQ2, $tempMaterial)) {
				$tempMaterial[$UNIQ2]['qty'] = 0;
			}
			
			$getDetailSPK = $this->db->get_where('production_spk',array('id'=>$value['id_spk']))->result_array();
			$tempMaterial[$UNIQ2]['tanggal'] 		= $datetime;
			$tempMaterial[$UNIQ2]['keterangan'] 	= 'laporan produksi';
			$tempMaterial[$UNIQ2]['no_ipp'] 		= $value['no_ipp'];
			$tempMaterial[$UNIQ2]['no_spk'] 		= (!empty($getDetailSPK[0]['no_spk']))?$getDetailSPK[0]['no_spk']:'';
			$tempMaterial[$UNIQ2]['product'] 		= (!empty($getDetailSPK[0]['product']))?$getDetailSPK[0]['product']:'';
			$tempMaterial[$UNIQ2]['kode_trans'] 	= $value['kode_trans'];
			$tempMaterial[$UNIQ2]['id_material'] 	= $value['id_material'];
			$tempMaterial[$UNIQ2]['nm_material'] 	= $nm_material;
			$tempMaterial[$UNIQ2]['qty'] 			+= $value['berat'];
			$tempMaterial[$UNIQ2]['cost_book'] 		= $costbook;
			$tempMaterial[$UNIQ2]['created_by'] 	= $username;
			$tempMaterial[$UNIQ2]['created_date'] 	= $datetime;
			$tempMaterial[$UNIQ2]['tipe'] 			= 'out';
			$tempMaterial[$UNIQ2]['gudang'] 		= $id_gudang;
			$tempMaterial[$UNIQ2]['gudang_dari'] 	= $id_gudang;
			$tempMaterial[$UNIQ2]['gudng_ke'] 		= $id_gudang_ke;
			
			$getDetailSPK1 = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans'],'id_production_detail'=>$value['id_pro_det']))->result_array();
			
			$id_trans1 = (!empty($getDetailSPK1[0]['id']))?$getDetailSPK1[0]['id']:0;
			
			$id_material = $value['id_material'];

                $coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang))->row();
				$coa_gudang = $coa_1->coa_1;
				$kategori_gudang = $coa_1->category;				 
					
					$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				
				$QTY_OKE  = $berat; 
				$ACTUAL_MAT = $value['id_material'];
				$kode_trans = $id_trans1;
				$PRICE     = $costbook;
				
				$ArrJurnalNew[$UNIQ2]['id_material'] 		= $ACTUAL_MAT;
				$ArrJurnalNew[$UNIQ2]['idmaterial'] 		= $GET_MATERIAL[$ACTUAL_MAT]['idmaterial'];
				$ArrJurnalNew[$UNIQ2]['nm_material'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_material'];
				$ArrJurnalNew[$UNIQ2]['id_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['id_category'];
				$ArrJurnalNew[$UNIQ2]['nm_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_category'];
				$ArrJurnalNew[$UNIQ2]['id_gudang'] 			= $id_gudang;
				$ArrJurnalNew[$UNIQ2]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrJurnalNew[$UNIQ2]['id_gudang_dari'] 	    = $id_gudang;
				$ArrJurnalNew[$UNIQ2]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrJurnalNew[$UNIQ2]['id_gudang_ke'] 		= $id_gudang_ke;
				$ArrJurnalNew[$UNIQ2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew[$UNIQ2]['qty_stock_awal'] 		= $stokjurnalakhir;
				$ArrJurnalNew[$UNIQ2]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['kode_trans'] 			= $kode_trans;
				$ArrJurnalNew[$UNIQ2]['tgl_trans'] 			= $datetime;
				$ArrJurnalNew[$UNIQ2]['qty_out'] 			= $QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['ket'] 				= 'pindah gudang produksi - wip';
				$ArrJurnalNew[$UNIQ2]['harga'] 			= $PRICE;
				$ArrJurnalNew[$UNIQ2]['harga_bm'] 		= 0;
				$ArrJurnalNew[$UNIQ2]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$UNIQ2]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
				$ArrJurnalNew[$UNIQ2]['update_by'] 		= $username;
				$ArrJurnalNew[$UNIQ2]['update_date'] 		= $datetime;
				$ArrJurnalNew[$UNIQ2]['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew[$UNIQ2]['coa_gudang'] 		= $coa_gudang;
			
				
		}
		//biaya WIP
		$ArrDataWIP = ['Direct labour','Indirect labour','Consumable','FOH','Total'];
		$temp2 = [];
		if(!empty($temp)){
			foreach ($ArrDataWIP as $value2) {
				foreach ($temp as $key => $value) {
					$UNIQ = $value['id_spk'].'-'.$value2;

					$temp2[$UNIQ]['berat'] 		= 0;

					$WIPNmProduct = ($value2 == 'Total')?$value['product']:$value2;

					$temp2[$UNIQ]['tanggal'] 		= $dateKurs;
					$temp2[$UNIQ]['no_ipp'] 		= $value['no_ipp'];
					$temp2[$UNIQ]['id_pro_det'] 	= $value['id_pro_det'];
					$temp2[$UNIQ]['id_material'] = NULL;
					$temp2[$UNIQ]['nm_material'] = 'WIP '.$WIPNmProduct;
					$temp2[$UNIQ]['id_spk'] 		= $value['id_spk'];
					$temp2[$UNIQ]['kode_trans'] 	= $value['kode_trans'];
					$temp2[$UNIQ]['keterangan']	= "Gudang Produksi to WIP";
					$temp2[$UNIQ]['no_so'] 		= $value['no_so'];
					$temp2[$UNIQ]['product'] 	= $value['product'];
					$temp2[$UNIQ]['no_spk'] 		= $value['no_spk'];
					$temp2[$UNIQ]['id_milik']	= $value['id_milik'];
					
					// $Explode = explode('/',$value['kode_trans']);
					$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans'],'id_production_detail'=>$value['id_pro_det']))->result_array();
					$real_harga = (!empty($getDetailSPK[0]['real_harga']))?$getDetailSPK[0]['real_harga']:0;
					$direct_labour = (!empty($getDetailSPK[0]['direct_labour']))?$getDetailSPK[0]['direct_labour']:0;
					$indirect_labour = (!empty($getDetailSPK[0]['indirect_labour']))?$getDetailSPK[0]['indirect_labour']:0;
					$consumable = (!empty($getDetailSPK[0]['consumable']))?$getDetailSPK[0]['consumable']:0;
					$machine = (!empty($getDetailSPK[0]['machine']))?$getDetailSPK[0]['machine']:0;
					$mould_mandrill = (!empty($getDetailSPK[0]['mould_mandrill']))?$getDetailSPK[0]['mould_mandrill']:0;
					$foh_depresiasi = (!empty($getDetailSPK[0]['foh_depresiasi']))?$getDetailSPK[0]['foh_depresiasi']:0;
					$biaya_rutin_bulanan = (!empty($getDetailSPK[0]['biaya_rutin_bulanan']))?$getDetailSPK[0]['biaya_rutin_bulanan']:0;
					$foh_consumable = (!empty($getDetailSPK[0]['foh_consumable']))?$getDetailSPK[0]['foh_consumable']:0;
					
					$nilai = 0;
					$nilai2 = 0;
					if($value2 == 'Direct labour'){
						$nilai = round($direct_labour*$kurs);
					}
					if($value2 == 'Indirect labour'){
						$nilai = round($indirect_labour*$kurs);
					}
					if($value2 == 'Consumable'){
						$nilai = round($consumable*$kurs);
					}
					if($value2 == 'FOH'){
						$nilai = round(($machine + $mould_mandrill + $foh_depresiasi + $biaya_rutin_bulanan + $foh_consumable)*$kurs);
					}
					if($value2 == $value['product']){
						$nilai1 = round(($direct_labour+ $indirect_labour+$consumable + $machine + $mould_mandrill + $foh_depresiasi + $biaya_rutin_bulanan + $foh_consumable)*$kurs);
						$nilai  = $nilai1;
						$nilai2 = $nilai1;
					}					
					
					$temp2[$UNIQ]['costbook'] 		= 0;
					$temp2[$UNIQ]['kurs'] 			= $kurs;
					$temp2[$UNIQ]['total_price'] 		= $nilai;
					$temp2[$UNIQ]['total_price_debet'] 	= $nilai2;
					$temp2[$UNIQ]['created_by'] 		= $username;
					$temp2[$UNIQ]['created_date'] 	= $datetime;

					$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;
					$temp2[$UNIQ]['id_trans'] = $id_trans;
				}
			}
		}

		$dataWIP = array_merge($temp,$temp2);
		// echo "<pre>";
		// print_r($dataWIP);
		// exit;
		if(!empty($dataWIP)){
			$this->db->insert_batch('data_erp_wip',$dataWIP);
		}
		// if(!empty($ArrUpdateStock)){
		// 	move_warehouse($ArrUpdateStock,$id_gudang,$id_gudang_ke,$kode_spk_time);
		// }

		//GROUP DATA
		$ArrGroup = [];
		if(!empty($ArrIDSPK)){
			foreach ($ArrIDSPK as $value) {
				if($value > 0){
					$getSummary = $this->db->select('no_so,product,no_spk')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time))->result_array();

					$ArrGroup[$value]['tanggal'] = $dateKurs;
					$ArrGroup[$value]['keterangan'] = 'Gudang produksi to WIP';
					$ArrGroup[$value]['no_so'] = (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
					$ArrGroup[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
					$ArrGroup[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
					$ArrGroup[$value]['kode_trans'] = $kode_spk_time;
					$ArrGroup[$value]['id_pro_det'] = $value;

					$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$kode_spk_time,'id_production_detail'=>$value))->result_array();
					$qty_awal = (!empty($getDetailSPK[0]['qty_awal']))?$getDetailSPK[0]['qty_awal']:0;
					$qty_akhir = (!empty($getDetailSPK[0]['qty_akhir']))?$getDetailSPK[0]['qty_akhir']:0;
					$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;

					$ArrGroup[$value]['qty'] = $qty_akhir - $qty_awal + 1;

					$getSummaryMaterial 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'id_material <>'=>NULL))->result_array();
					$getSummaryDirect 		= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP Direct labour'))->result_array();
					$getSummaryIndirect 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP Indirect labour'))->result_array();
					$getSummaryConsumable 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP Consumable'))->result_array();
					$getSummaryFOH 			= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP FOH'))->result_array();
					
					$nilai_material 	= (!empty($getSummaryMaterial[0]['nilai']))?$getSummaryMaterial[0]['nilai']:0;
					$nilai_direct 		= (!empty($getSummaryDirect[0]['nilai']))?$getSummaryDirect[0]['nilai']:0;
					$nilai_indirect 	= (!empty($getSummaryIndirect[0]['nilai']))?$getSummaryIndirect[0]['nilai']:0;
					$nilai_consumable 	= (!empty($getSummaryConsumable[0]['nilai']))?$getSummaryConsumable[0]['nilai']:0;
					$nilai_foh 			= (!empty($getSummaryFOH[0]['nilai']))?$getSummaryFOH[0]['nilai']:0;
					$nilai_wip			= $nilai_material + $nilai_direct + $nilai_indirect + $nilai_consumable + $nilai_foh;
					
					$ArrGroup[$value]['nilai_wip'] = $nilai_wip;
					$ArrGroup[$value]['material'] = $nilai_material;
					$ArrGroup[$value]['wip_direct'] =  $nilai_direct;
					$ArrGroup[$value]['wip_indirect'] =  $nilai_indirect;
					$ArrGroup[$value]['wip_consumable'] =  $nilai_consumable;
					$ArrGroup[$value]['wip_foh'] =  $nilai_foh;
					$ArrGroup[$value]['created_by'] = $username;
					$ArrGroup[$value]['created_date'] = $datetime;
					$ArrGroup[$value]['id_trans'] = $id_trans;
					
					$this->db->where('id_trans',$id_trans);
					$this->db->where('nm_material','WIP '.$getSummary[0]['product']);
					$this->db->update('data_erp_wip',array('total_price'=>0,'total_price_debet'=>$nilai_wip)); 
				}
			}
		}


		

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_wip_group',$ArrGroup);
			$this->jurnalWIP($id_trans,$closing_date);
		}
		// if(!empty($tempMaterial)){
		// 	$this->db->insert_batch('erp_data_subgudang',$tempMaterial);
		// }
		if(!empty($ArrJurnalNew)){
			$this->db->insert_batch('tran_warehouse_jurnal_detail',$ArrJurnalNew);
		}


	}

	function jurnalWIP($idtrans,$closing_date){
		$UserName		= 'manual system';
		$DateTime		= $closing_date;
		
		$kodejurnal='JV004';
		
		$wip = $this->db->query("SELECT * FROM data_erp_wip WHERE id_trans ='".$idtrans."'")->result();
		
		$totalwip =0;
		$wiptotal =0; 
		$det_Jurnaltes = [];
			
		foreach($wip AS $data){
			
			$nm_material = $data->nm_material;	
			$product 	 = $data->product;	
			$tgl_voucher = $data->tanggal;	
			$keterangan  = $data->nm_material;
			$id          = $data->id_trans;
			$no_request  = $data->no_spk;	
			$kredit      = $data->total_price;
			$totalwip       = $data->total_price_debet;	
			$wiptotal       += $data->total_price;	
			
			if($nm_material=='WIP Direct labour'){					
				$nokir = '2107-01-02' ;
			}elseif($nm_material=='WIP Indirect labour'){					
				$nokir = '2107-01-03' ;
			}elseif($nm_material=='WIP Consumable'){					
				$nokir = '2107-01-01' ;				
			}elseif($nm_material=='WIP FOH'){					
				$nokir = '2107-01-04' ;
			}
			else{
				$nokir = '1103-01-03' ;
			}

			$debit  = $totalwip;			
			
			if($totalwip != 0 ){
					$det_Jurnaltes[]  = array(
					'nomor'         => '',
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => '1103-03-02',
					'keterangan'    => $keterangan,
					'no_reff'       => $id,
					'debet'         => $wiptotal,
					'kredit'        => 0,
					'jenis_jurnal'  => 'produksi wip',
					'no_request'    => $no_request,
					'stspos'		  =>1
					);
				
			}else{
							
				$det_Jurnaltes[]  = array(
					'nomor'         => '',
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir,
					'keterangan'    => $keterangan,
					'no_reff'       => $id,
					'debet'         => 0,
					'kredit'        => $kredit,
					'jenis_jurnal'  => 'produksi wip',
					'no_request'    => $no_request,
					'stspos'		  =>1
					);
			}
			
		}
		
		$this->db->query("delete from jurnaltras WHERE jenis_jurnal='produksi wip' and no_reff ='$id'");
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
		
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
		$Bln	= substr($tgl_voucher,5,2);
		$Thn	= substr($tgl_voucher,0,4);
		$idlaporan = $id;
		$Keterangan_INV = 'Jurnal Produksi - WIP';
		$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalwip, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
		$this->db->insert(DBACC.'.javh',$dataJVhead);
		$datadetail=array();
		foreach ($det_Jurnaltes as $vals) {
			$datadetail = array(
				'tipe'			=> 'JV',
				'nomor'			=> $Nomor_JV,
				'tanggal'		=> $tgl_voucher,
				'no_perkiraan'	=> $vals['no_perkiraan'],
				'keterangan'	=> $vals['keterangan'],
				'no_reff'		=> $vals['no_reff'],
				'debet'			=> $vals['debet'],
				'kredit'		=> $vals['kredit'],
				);
			$this->db->insert(DBACC.'.jurnal',$datadetail);
		}
		unset($det_Jurnaltes);unset($datadetail);
	}

	public function generateFG(){
        $status = 0;
        if($status == 1){
            $SQL    = "SELECT * FROM data_erp_wip_group where id_trans!='1398' AND created_by = 'manual system' and jenis='out'";
            $result = $this->db->query($SQL)->result_array();
			
			$ArrInsertBatch = [];
			$nomor = 0;
			foreach ($result as $key => $value) {
				$GetQTY = $this->db->select('product_ke')->get_where('production_detail',['id'=>$value['id_pro_det']])->result_array();
				$ProductKe = (!empty($GetQTY[0]['product_ke']))?$GetQTY[0]['product_ke']:1;
				for ($i=1; $i <= $value['qty']; $i++) {  $nomor++;
					$ArrInsertBatch[$nomor]['tanggal'] = $value['tanggal'];
					$ArrInsertBatch[$nomor]['keterangan'] = 'WIP to Finish Good';
					$ArrInsertBatch[$nomor]['no_so'] = $value['no_so'];
					$ArrInsertBatch[$nomor]['product'] = $value['product'];
					$ArrInsertBatch[$nomor]['no_spk'] = $value['no_spk'];
					$ArrInsertBatch[$nomor]['kode_trans'] = $value['kode_trans'];
					$ArrInsertBatch[$nomor]['id_pro_det'] = $value['id_pro_det'];
					$ArrInsertBatch[$nomor]['qty'] = $value['qty'];
					$ArrInsertBatch[$nomor]['nilai_wip'] = $value['nilai_wip']/$value['qty'];
					$ArrInsertBatch[$nomor]['material'] = $value['material']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_direct'] = $value['wip_direct']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_indirect'] = $value['wip_indirect']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_consumable'] = $value['wip_consumable']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_foh'] = $value['wip_foh']/$value['qty'];
					$ArrInsertBatch[$nomor]['created_by'] = $value['created_by'];
					$ArrInsertBatch[$nomor]['created_date'] = $value['tanggal'];
					$ArrInsertBatch[$nomor]['id_trans'] = $value['id_trans'];
					$ArrInsertBatch[$nomor]['id_pro'] = $value['id_pro_det'] + $i;
					$ArrInsertBatch[$nomor]['qty_ke'] = $ProductKe++;
					$ArrInsertBatch[$nomor]['nilai_unit'] = $value['nilai_wip']/$value['qty'];
					$ArrInsertBatch[$nomor]['jenis'] = 'in';
				}
				

			}

			if(!empty($ArrInsertBatch)){
				$this->db->insert_batch('data_erp_fg',$ArrInsertBatch);
			}
			
        }
		else{
			echo "Proses Stop !";
		}
    }

}