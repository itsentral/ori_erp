<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('cron_model');
		$this->load->model('tanki_model');
		$this->load->database();
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

    public function report_produksi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		// echo $controller;
		// print_r($Arr_Akses); exit;
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Production Report',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Report Produksi');
		$this->load->view('Cron/report_produksi',$data);
	}

    public function daily_report(){
		$dateC = date('Y-m-d');
		$date = date('Y-m-d', strtotime('-1 days', strtotime($dateC)));
		// echo $date; exit;
        // $date = date('2020-03-15'); 
		$Sum_real_harga_rp	= 0;
		$kurs=1;
		$sqlkurs="select * from ms_kurs where tanggal <='".date('Y-m-d')."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs	= $this->db->query($sqlkurs)->row();
		if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
        $sqlHeader = "SELECT a.*, b.id_milik FROM history_pro_header_cron a LEFT JOIN production_detail b ON a.id_production_detail = b.id  WHERE DATE(a.status_date)='".$date."' ";
        $restHeader = $this->db->query($sqlHeader)->result_array();
        if(!empty($restHeader)){
            // echo $sqlHeader;
            $ArrDay = array();
            $Sum_est_mat        = 0;
            $Sum_est_harga      = 0;
            $Sum_real_mat       = 0;
            $Sum_real_harga     = 0;
            $Sum_direct_labour  = 0;
            $Sum_in_labour      = 0;
            $Sum_machine        = 0;
            $Sum_mould_mandrill = 0;
            $Sum_consumable     = 0;
            $Sum_foh_consumable = 0;
            $Sum_foh_depresiasi = 0;
            $Sum_by_gaji        = 0;
            $Sum_by_non_pro     = 0;
            $Sum_by_rutin       = 0;
            foreach($restHeader AS $val=>$valx){
                $sqlCh = "SELECT jalur FROM production_header WHERE id_produksi='".$valx['id_produksi']."' ";
                $restCh		= $this->db->query($sqlCh)->result_array();
                $HelpDet 	= "estimasi_cost_and_mat";
                $HelpDet2 	= "banding_mat_pro";
                $HelpDet3 	= "bq_product";
                if($restCh[0]['jalur'] == 'FD'){
                    $HelpDet = "so_estimasi_cost_and_mat";
                    $HelpDet2 	= "banding_so_mat_pro";
                    $HelpDet3 	= "bq_product_fd";
                }

                $sqlBy 		= " SELECT
                                    b.diameter AS diameter,
                                    b.diameter2 AS diameter2,
                                    b.pressure AS pressure,
                                    b.liner AS liner,
                                    a.direct_labour AS direct_labour,
                                    a.indirect_labour AS indirect_labour,
                                    a.machine AS machine,
                                    a.mould_mandrill AS mould_mandrill,
                                    a.consumable AS consumable,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) AS foh_consumable,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) AS foh_depresiasi,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) AS biaya_gaji_non_produksi,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) AS biaya_non_produksi,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) AS biaya_rutin_bulanan 
                                FROM
                                    ".$HelpDet." a
                                    INNER JOIN ". $HelpDet3." b ON a.id_milik = b.id
                                WHERE id_milik='".$valx['id_milik']."' LIMIT 1";

                $restBy     = $this->db->query($sqlBy)->result_array();
                
                $sqlBan     = "SELECT * FROM ".$HelpDet2." WHERE id_detail='".$valx['id_production_detail']."' LIMIT 1";
                $restBan    = $this->db->query($sqlBan)->result_array();
                // echo $sqlEst."<br>";
                $jumTot     = ($valx['qty_akhir'] - $valx['product_ke']) + 1;
                
                $Sum_est_mat        += $restBan[0]['est_material'] * $jumTot;
                $Sum_est_harga      += $restBan[0]['est_harga'] * $jumTot;
                $Sum_real_mat       += $restBan[0]['real_material'];
                $Sum_real_harga     += $restBan[0]['real_harga'];
                $Sum_real_harga_rp  += $restBan[0]['real_harga_rp'];
                $Sum_direct_labour  += $restBy[0]['direct_labour'] * $jumTot;
                $Sum_in_labour      += $restBy[0]['indirect_labour'] * $jumTot;
                $Sum_machine        += $restBy[0]['machine'] * $jumTot;
                $Sum_mould_mandrill += $restBy[0]['mould_mandrill'] * $jumTot;
                $Sum_consumable     += $restBy[0]['consumable'] * $jumTot;
                $Sum_foh_consumable += $restBy[0]['foh_consumable'] * $jumTot;
                $Sum_foh_depresiasi += $restBy[0]['foh_depresiasi'] * $jumTot;
                $Sum_by_gaji        += $restBy[0]['biaya_gaji_non_produksi'] * $jumTot;
                $Sum_by_non_pro     += $restBy[0]['biaya_non_produksi'] * $jumTot;
                $Sum_by_rutin       += $restBy[0]['biaya_rutin_bulanan'] * $jumTot;

                $ArrDay[$val]['id_produksi']            = $valx['id_produksi'];
                $ArrDay[$val]['id_category']            = $valx['id_category'];
                $ArrDay[$val]['id_product']             = $valx['id_product'];
                $ArrDay[$val]['diameter']               = $restBy[0]['diameter'];
                $ArrDay[$val]['diameter2']              = $restBy[0]['diameter2'];
                $ArrDay[$val]['pressure']               = $restBy[0]['pressure'];
                $ArrDay[$val]['liner']                  = $restBy[0]['liner'];
                $ArrDay[$val]['status_date']            = $valx['status_date'];
                $ArrDay[$val]['qty_awal']               = $valx['product_ke'];
                $ArrDay[$val]['qty_akhir']              = $valx['qty_akhir'];
                $ArrDay[$val]['qty']                    = $valx['qty'];
                $ArrDay[$val]['date']                   = $date;
                $ArrDay[$val]['id_production_detail']   = $valx['id_production_detail'];
                $ArrDay[$val]['id_milik']               = $valx['id_milik'];
                $ArrDay[$val]['est_material']           = $restBan[0]['est_material'] * $jumTot;
                $ArrDay[$val]['est_harga']              = $restBan[0]['est_harga'] * $jumTot;
                $ArrDay[$val]['real_material']          = $restBan[0]['real_material'];
                $ArrDay[$val]['real_harga']             = $restBan[0]['real_harga'];
                $ArrDay[$val]['real_harga_rp']			= $restBan[0]['real_harga_rp'];
                $ArrDay[$val]['kurs']                   = $kurs;

                $ArrDay[$val]['direct_labour']          = $restBy[0]['direct_labour'] * $jumTot;
                $ArrDay[$val]['indirect_labour']        = $restBy[0]['indirect_labour'] * $jumTot;
                $ArrDay[$val]['machine']                = $restBy[0]['machine'] * $jumTot;
                $ArrDay[$val]['mould_mandrill']         = $restBy[0]['mould_mandrill'] * $jumTot;
                $ArrDay[$val]['consumable']             = $restBy[0]['consumable'] * $jumTot;
                $ArrDay[$val]['foh_consumable']         = $restBy[0]['foh_consumable'] * $jumTot;
                $ArrDay[$val]['foh_depresiasi']         = $restBy[0]['foh_depresiasi'] * $jumTot;
                $ArrDay[$val]['biaya_gaji_non_produksi']= $restBy[0]['biaya_gaji_non_produksi'] * $jumTot;
                $ArrDay[$val]['biaya_non_produksi']     = $restBy[0]['biaya_non_produksi'] * $jumTot;
                $ArrDay[$val]['biaya_rutin_bulanan']    = $restBy[0]['biaya_rutin_bulanan'] * $jumTot;

                $ArrDay[$val]['insert_by']              = 'system';
                $ArrDay[$val]['insert_date']            = date('Y-m-d H:i:s');

            }

            $ArrDayMonth = array(
                'date' => $date,
                'est_material' => $Sum_est_mat,
                'est_harga' => $Sum_est_harga,
                'real_material' => $Sum_real_mat,
                'real_harga' => $Sum_real_harga,
                'real_harga_rp' => $Sum_real_harga_rp,
                'kurs' => $kurs,
                'direct_labour' => $Sum_direct_labour,
                'indirect_labour' => $Sum_in_labour,
                'machine' => $Sum_machine,
                'mould_mandrill' => $Sum_mould_mandrill,
                'consumable' => $Sum_consumable,
                'foh_consumable' => $Sum_foh_consumable,
                'foh_depresiasi' => $Sum_foh_depresiasi,
                'biaya_gaji_non_produksi' => $Sum_by_gaji,
                'biaya_non_produksi' => $Sum_by_non_pro,
                'biaya_rutin_bulanan' => $Sum_by_rutin,
                'insert_by' => 'system',
                'insert_date' => date('Y-m-d H:i:s')

            );

            // echo "<pre>";
            // print_r($ArrDay);
            // print_r($ArrDayMonth);
            // exit;
            $this->db->trans_start();
                $this->db->delete('laporan_per_bulan', array('date' => $date));
                $this->db->delete('laporan_per_hari', array('date' => $date));

                $this->db->insert('laporan_per_bulan', $ArrDayMonth);
                $this->db->insert_batch('laporan_per_hari', $ArrDay);
            $this->db->trans_complete();

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $ArrHistF	= array(
                    'date'			=> $date,
                    'status'		=> 'FAILED',
                    'insert_by'		=> 'system',
                    'insert_date'	=> date('Y-m-d H:i:s')
                );
                $this->db->insert('laporan_status', $ArrHistF);
                echo "Failed Insert Data";
            }
            else{
                $this->db->trans_commit();
                $ArrHistS	= array(
                    'date'			=> $date,
                    'status'		=> 'SUCCESS',
                    'insert_by'		=> 'system',
                    'insert_date'	=> date('Y-m-d H:i:s') 
                );
                $this->db->insert('laporan_status', $ArrHistS);
                echo "Success Insert Data";
            }
        }
        else{
            $ArrHistE	= array(
                'date'			=> $date,
                'status'		=> 'EMPTY',
                'insert_by'		=> 'system',
                'insert_date'	=> date('Y-m-d H:i:s') 
            );
            $this->db->insert('laporan_status', $ArrHistE);
            echo "No Data Insert Data";
        }
	}
	
	public function insert_manual_quotation(){
		$id_bq = 'BQ-IPP20225L';

		$data_session	= $this->session->userdata;
		//Insert Detail Report Revised
		$sqlNoRev 	= "SELECT revised_no FROM laporan_revised_header WHERE id_bq='".$id_bq."' ORDER BY insert_date DESC LIMIT 1 ";
		$restNoRev 	= $this->db->query($sqlNoRev)->result_array();
		$restNumRev = $this->db->query($sqlNoRev)->num_rows();

		if($restNumRev > 0){
			$revised_no = $restNoRev[0]['revised_no'] + 1;
		}
		else{
			$revised_no = 0;
		}

		$sqlRevised 	= SQL_Revised($id_bq);
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
			$ArrDetRevised[$val]['profit'] = $valx['profit'];
				$unitProfitX = $unitPriceX *($valx['profit']/100);
				$unitAllowanceX = (($unitPriceX) + ($unitProfitX)) * $valx['qty'];
			$ArrDetRevised[$val]['total_price'] = $unitAllowanceX;
			$ArrDetRevised[$val]['allowance'] = $valx['allowance'];
				$unitAllowanceLast = (($unitAllowanceX) + ($unitAllowanceX * ($valx['allowance']/100)));
			$ArrDetRevised[$val]['total_price_last'] = $unitAllowanceLast;
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

		//Insert Header Report Revised
		$sqlRevisedHead 	= "SELECT id_customer, nm_customer, project FROM production WHERE no_ipp='".str_replace('BQ-','',$id_bq)."' ";
		$restRevisedHead 	= $this->db->query($sqlRevisedHead)->result_array();

		$sqlTotPro 		= "SELECT price_project FROM cost_project_header WHERE id_bq='".$id_bq."' ";
		$restsqlTotPro 	= $this->db->query($sqlTotPro)->result_array();
		$restNumTotPro 	= $this->db->query($sqlTotPro)->num_rows();

		if($restNumTotPro > 0){
			$totProject = $restsqlTotPro[0]['price_project'];
		}
		else{
			$totProject = 0;
		}
		
		$ArrHeadRevised = array(
			'id_bq' => $id_bq,
			'id_customer' => $restRevisedHead[0]['id_customer'],
			'nm_customer' => $restRevisedHead[0]['nm_customer'],
			'nm_project' => $restRevisedHead[0]['project'],
			'revised_no' => $revised_no,
			'price_project' => $totProject,
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

		//Insert Header Report Etc
		$sqlRevisedEtc 		= "SELECT * FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category <> 'material' ";
		$restRevisedEtc 	= $this->db->query($sqlRevisedEtc)->result_array();
		$restNumRevisedEtc 	= $this->db->query($sqlRevisedEtc)->num_rows();
		
		if($restNumRevisedEtc > 0){
			$ArrEtcRevised = array();
			foreach($restRevisedEtc AS $val => $valx){
				$ArrEtcRevised[$val]['id_bq'] = $valx['id_bq'];
				$ArrEtcRevised[$val]['category'] = $valx['category'];
				$ArrEtcRevised[$val]['caregory_sub'] = $valx['caregory_sub'];
				$ArrEtcRevised[$val]['option_type'] = $valx['option_type'];
				$ArrEtcRevised[$val]['area'] = $valx['area'];
				$ArrEtcRevised[$val]['tujuan'] = $valx['tujuan'];
				$ArrEtcRevised[$val]['kendaraan'] = $valx['kendaraan'];
				$ArrEtcRevised[$val]['unit'] = $valx['unit'];
				$ArrEtcRevised[$val]['qty'] = $valx['qty'];
				$ArrEtcRevised[$val]['fumigasi'] = $valx['fumigasi'];
				$ArrEtcRevised[$val]['price'] = $valx['price'];
				$ArrEtcRevised[$val]['price_total'] = $valx['price_total'];
				$ArrEtcRevised[$val]['revised_no'] = $revised_no;
				$ArrEtcRevised[$val]['insert_by'] = $data_session['ORI_User']['username'];
				$ArrEtcRevised[$val]['insert_date'] = date('Y-m-d H:i:s');
			}
		}
		// echo "<pre>";
		// print_r($ArrHeadRevised);
		// print_r($ArrDetRevised);
		// print_r($ArrEtcRevised);
		// exit;

		// echo "<pre>";
		// print_r($ArrDay);
		// print_r($ArrDayMonth);
		// exit;
		$this->db->trans_start();
			$this->db->insert('laporan_revised_header', $ArrHeadRevised);
			$this->db->insert_batch('laporan_revised_detail', $ArrDetRevised);
			if($restNumRevisedEtc > 0){
				$this->db->insert_batch('laporan_revised_etc', $ArrEtcRevised);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			echo "Failed Insert Data";
		}
		else{
			$this->db->trans_commit();
			echo "Success Insert Data";
		}
    }
	
	public function getDataJSON(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
            $requestData['bulan'],
            $requestData['tahun'],
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
            $view	= "<button type='button' class='btn btn-sm btn-warning' id='detail' title='Look Data' data-tanggal='".$row['date']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			$pdf='';

			$get_revenue = $this->db
							->select('SUM((( d.price_total / e.qty ) * (a.qty_akhir - a.qty_awal + 1))) AS revenue')
							->from('laporan_per_bulan z')
							->join('laporan_per_hari a','z.date=a.date')
							->join('so_detail_header b','a.id_milik=b.id')
							->join('so_bf_detail_header c','b.id_milik=c.id')
							->join('cost_project_detail d','c.id_milik=d.caregory_sub')
							->join('bq_detail_header e','c.id_milik=e.id')
							->where('z.date', $row['date'])
							->get()
							->result();
			$revenue 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;

            $nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$view."".$pdf."".$excel."</div>";
            $nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['date']))."</div>";
            $nestedData[]	= "<div align='right' class='text-blue'>".number_format($row['est_material'],4)."</div>";
            $nestedData[]	= "<div align='right' class='text-blue'><b>".number_format($row['est_harga'],2)."</b></div>";
            $nestedData[]	= "<div align='right' class='text-green'>".number_format($row['real_material'],4)."</div>";
			$nestedData[]	= "<div align='right' class='text-green'><b>".number_format($row['real_harga'],2)."</b></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['real_harga_rp'],2)."</div>"; 
			$nestedData[]	= "<div align='right'>".number_format($row['kurs'],2)."</div>"; 
            $nestedData[]	= "<div align='right' class='text-purple'><b>".number_format(($row['real_harga_rp']/$row['kurs']),2)."</b></div>";
            $nestedData[]	= "<div align='right'>".number_format($revenue,2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";


            $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
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

	public function queryDataJSON($bulan, $tahun, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
        $where_bln = "";
        if($bulan > 0){
            $where_bln = "AND MONTH(date) = '".$bulan."' ";
        }

        $where_thn = "";
        if($tahun > 0){
            $where_thn = "AND YEAR(date) = '".$tahun."' ";
        }

		$sql = "
			SELECT
				*
			FROM
				laporan_per_bulan
		    WHERE 1=1 ".$where_bln." ".$where_thn." AND (
				`date` LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'date'
			
		);

		$sql .= " ORDER BY `date` DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
    
    public function modalDetail(){
		$this->load->view('Cron/report_produksi_detail');
    }

    public function getDataJSONDetail(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail(
            $requestData['tanggal'],
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
            $view	= "<button type='button' class='btn btn-sm btn-warning' id='detail' title='Look Data' data-tanggal='".$row['date']."'><i class='fa fa-eye'></i></button>";
			$pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-primary'  title='Print Sales Order' ><i class='fa fa-file-excel-o'></i></a>";
			
			$qty = $row['qty_akhir'] - $row['qty_awal'] + 1;

			$NO_IPP = str_replace('PRO-','',$row['id_produksi']);
			$tandaIPP = substr($NO_IPP,0,4);
			
			$no_so = $row['no_so'];
			$no_spk = $row['no_spk'];
			$revenue = 0;
			$estimasi_material 	= $row['est_material'];
			$estimasi_price 	= $row['est_harga'];
			$real_material 		= $row['real_material'];
			if($tandaIPP != 'IPPT'){
				$no_so = get_detail_ipp()[$NO_IPP]['so_number'];
				$no_spk = $row['no_spk2'];

				$get_revenue = $this->db
							->select('(d.price_total / e.qty) AS revenue')
							->from('laporan_per_hari a')
							->join('so_detail_header b','a.id_milik=b.id')
							->join('so_bf_detail_header c','b.id_milik=c.id')
							->join('cost_project_detail d','c.id_milik=d.caregory_sub')
							->join('bq_detail_header e','c.id_milik=e.id')
							->where('a.id_milik', $row['id_milik'])
							->limit(1)
							->get()
							->result();
				$revenue2 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;
				$revenue 	= $revenue2 * $qty;

				$GET_EST_ACT = getEstimasiVsAktual($row['id_milik'], $NO_IPP, $qty, $row['id_production_detail']);

				$estimasi_material 	= (!empty($GET_EST_ACT['est_mat']))?$GET_EST_ACT['est_mat']:0;
				$estimasi_price 	= (!empty($GET_EST_ACT['act_mat']))?$GET_EST_ACT['act_mat']:0;
				$real_material 		= (!empty($GET_EST_ACT['est_price']))?$GET_EST_ACT['est_price']:0;
				// $real_material 		= $row['real_material'];
			}

            $nestedData 	= array();
            $nestedData[]	= "<div align='center'>".$nomor."</div>";
			// $nestedData[]	= "<div align='left'>".$row['id']."</div>";
            $nestedData[]	= "<div align='left'>".$NO_IPP."</div>";
            $nestedData[]	= "<div align='left'>".$no_so."</div>";
            $nestedData[]	= "<div align='left'>".$row['id_category']."</div>";
            $nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
            $nestedData[]	= "<div align='left'>".$no_spk."</div>";
            $nestedData[]	= "<div align='right'>".$row['diameter']."</div>";
            $nestedData[]	= "<div align='right'>".$row['diameter2']."</div>";
            $nestedData[]	= "<div align='center'>".$row['pressure']."</div>";
            $nestedData[]	= "<div align='center'>".$row['liner']."</div>";
            $nestedData[]	= "<div align='right'>".$qty."</div>";
            $nestedData[]	= "<div align='right'>".number_format($revenue,2)."</div>";

			

            $nestedData[]	= "<div align='right'>".number_format($estimasi_material,4)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($estimasi_price,2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($real_material,4)."</div>";
           	$nestedData[]	= "<div align='right'>".number_format($row['real_harga'],2)."</div>";
            // $nestedData[]	= "<div align='right'>".number_format(($row['real_harga_rp']/$row['kurs']),2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
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

	public function queryDataJSONDetail($tanggal, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
			SELECT
				a.*,
				b.no_spk as no_spk2
			FROM
				laporan_per_hari a
				LEFT JOIN so_detail_header b ON a.id_milik=b.id
		    WHERE a.date='".$tanggal."' AND (
				a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.date'
			
		);

		$sql .= " ORDER BY a.id_produksi ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }

    public function excel_lap_bulanan(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$tanggal = $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(2243);
		$sheet->setCellValue('A'.$Row, 'LAPORAN PRODUKSI ('.date('d F Y', strtotime($tanggal)).')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow1= $NewRow +1;
		
		// $sheet->setCellValue('A'.$NewRow, 'ID');
		// $sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($tableHeader);
		// $sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		// $sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('A'.$NewRow, 'IPP'); 
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Product');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow1);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'ID Product');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow1);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Dim');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow1);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Dim 2');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow1);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Pressure');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow1);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Liner');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow1);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Est Material (kg)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow1);
        $sheet->getColumnDimension('H')->setWidth(16);
        
        $sheet->setCellValue('I'.$NewRow, 'Est Price ($)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow1);
        $sheet->getColumnDimension('I')->setWidth(16);
        
        $sheet->setCellValue('J'.$NewRow, 'Aktual Material (kg)');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow1);
        $sheet->getColumnDimension('J')->setWidth(16);
        
        $sheet->setCellValue('K'.$NewRow, 'Aktual Price ($)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow1);
        $sheet->getColumnDimension('K')->setWidth(16);
        
        $sheet->setCellValue('L'.$NewRow, 'Qty');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow1);
        $sheet->getColumnDimension('L')->setWidth(16);
        
        $sheet->setCellValue('M'.$NewRow, 'Revenue');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow1);
        $sheet->getColumnDimension('M')->setWidth(16);
        
        $sheet->setCellValue('N'.$NewRow, 'Direct Labour');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow1);
        $sheet->getColumnDimension('N')->setWidth(16);
        
        $sheet->setCellValue('O'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow1);
        $sheet->getColumnDimension('O')->setWidth(16);
        


        $sheet->setCellValue('P'.$NewRow, 'Consumable');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow1);
        $sheet->getColumnDimension('P')->setWidth(16);

        $sheet->setCellValue('Q'.$NewRow, 'FOH');
		$sheet->getStyle('Q'.$NewRow.':U'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':U'.$NextRow);
        $sheet->getColumnDimension('Q')->setWidth(16);

		$sheet->setCellValue('V'.$NewRow, 'Sales & Marketing');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow1);
		$sheet->getColumnDimension('V')->setWidth(16);

		$sheet->setCellValue('W'.$NewRow, 'Umum & Admin');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow1);
		$sheet->getColumnDimension('W')->setWidth(16);

		$sheet->setCellValue('X'.$NewRow, 'No SPK');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow1);
		$sheet->getColumnDimension('X')->setWidth(16);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
        
        $sheet->setCellValue('Q'.$NewRow, 'Machine Cost');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
        $sheet->getColumnDimension('Q')->setWidth(16);
        
        $sheet->setCellValue('R'.$NewRow, 'Mold mandril Cost');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
        $sheet->getColumnDimension('R')->setWidth(16);
        

        $sheet->setCellValue('S'.$NewRow, 'Depreciation FOH');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
        $sheet->getColumnDimension('S')->setWidth(16);
        
        $sheet->setCellValue('T'.$NewRow, 'Factory Overhead');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
        $sheet->getColumnDimension('T')->setWidth(16);
        
        $sheet->setCellValue('U'.$NewRow, 'Salary Factory Management');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setWidth(16);


		$qSupplier	    = "	SELECT * FROM laporan_per_hari WHERE `date` = '".$tanggal."' ORDER BY id_produksi ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				
				// $awal_col++;
				// $id	= $row_Cek['id'];
				// $Cols			= getColsChar($awal_col);
				// $sheet->setCellValue($Cols.$awal_row, $id);
				// $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_produksi	= $row_Cek['id_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_category	= $row_Cek['id_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_product	= $row_Cek['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$diameter	= $row_Cek['diameter'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$diameter2	= $row_Cek['diameter2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$pressure	= $row_Cek['pressure'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $pressure);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$liner	= $row_Cek['liner'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $liner);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$NO_IPP = str_replace('PRO-','',$row_Cek['id_produksi']);
				$tandaIPP = substr($NO_IPP,0,4);

				$qty = $row_Cek['qty_akhir'] - $row_Cek['qty_awal'] + 1;

				$estimasi_material 	= $row_Cek['est_material'];
				$estimasi_price 	= $row_Cek['est_harga'];
				$real_material 		= $row_Cek['real_material'];
				$no_spk 			= $row_Cek['no_spk'];
				$revenue 			= $row_Cek['est_harga'];

				if($tandaIPP != 'IPPT'){
					$get_revenue = $this->db
								->select('(d.price_total / e.qty) AS revenue, b.no_spk')
								->from('laporan_per_hari a')
								->join('so_detail_header b','a.id_milik=b.id')
								->join('so_bf_detail_header c','b.id_milik=c.id')
								->join('cost_project_detail d','c.id_milik=d.caregory_sub')
								->join('bq_detail_header e','c.id_milik=e.id')
								->where('a.id_milik', $row_Cek['id_milik'])
								->limit(1)
								->get()
								->result();
					$revenue2 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;
					$no_spk 	= (!empty($get_revenue[0]->no_spk))?$get_revenue[0]->no_spk:'';
					$revenue 	= $revenue2 * $qty;
					
					$GET_EST_ACT = getEstimasiVsAktual($row_Cek['id_milik'], $NO_IPP, $qty, $row_Cek['id_production_detail']);

					$estimasi_material 	= (!empty($GET_EST_ACT['est_mat']))?$GET_EST_ACT['est_mat']:0;
					$estimasi_price 	= (!empty($GET_EST_ACT['act_mat']))?$GET_EST_ACT['act_mat']:0;
					$real_material 		= (!empty($GET_EST_ACT['est_price']))?$GET_EST_ACT['est_price']:0;
					// $real_material 		= $row_Cek['real_material'];
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estimasi_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estimasi_price);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$real_harga	= ($row_Cek['real_harga_rp']/$row_Cek['kurs']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$direct_labour	= $qty;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$direct_labour	= $revenue;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                

				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                
                $awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                

                $awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
			}
		}
		
		
		$sheet->setTitle('Report Produksi '.date('d-m-Y', strtotime($tanggal)));
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="report-produksi-'.date('d-m-Y', strtotime($tanggal)).'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_project(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		
		$bulan		= $this->uri->segment(3);
		$tahun		= $this->uri->segment(4);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(15);
		$sheet->setCellValue('A'.$Row, 'LAPORAN PRODUKSI PER DAY ('.$bulan.' '.$tahun.')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow1= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Tanggal Produksi');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Est Material (kg)');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow1);
        $sheet->getColumnDimension('B')->setWidth(16);
        
        $sheet->setCellValue('C'.$NewRow, 'Est Price ($)');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow1);
        $sheet->getColumnDimension('C')->setWidth(16);
        
        $sheet->setCellValue('D'.$NewRow, 'Aktual Material (kg)');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow1);
        $sheet->getColumnDimension('D')->setWidth(16);
        
        $sheet->setCellValue('E'.$NewRow, 'Aktual Price ($)');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow1);
        $sheet->getColumnDimension('E')->setWidth(16);
        
        $sheet->setCellValue('F'.$NewRow, 'Revenue');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow1);
        $sheet->getColumnDimension('F')->setWidth(16);
        
        $sheet->setCellValue('G'.$NewRow, 'Direct Labour');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow1);
        $sheet->getColumnDimension('G')->setWidth(16);
        
        $sheet->setCellValue('H'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow1);
        $sheet->getColumnDimension('H')->setWidth(16);

        $sheet->setCellValue('I'.$NewRow, 'Consumable');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow1);
        $sheet->getColumnDimension('I')->setWidth(16);
        
        $sheet->setCellValue('J'.$NewRow, 'FOH');
		$sheet->getStyle('J'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':N'.$NextRow);
        $sheet->getColumnDimension('N')->setWidth(16);

        $sheet->setCellValue('O'.$NewRow, 'Sales & Marketing');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow1);
        $sheet->getColumnDimension('O')->setWidth(16);

		$sheet->setCellValue('P'.$NewRow, 'Umum & Admin');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow1);
        $sheet->getColumnDimension('P')->setWidth(16);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

        $sheet->setCellValue('J'.$NewRow, 'Machine Cost');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
        $sheet->getColumnDimension('J')->setWidth(16);
        
        $sheet->setCellValue('K'.$NewRow, 'Mold mandril Cost');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
        $sheet->getColumnDimension('K')->setWidth(16);
        
        $sheet->setCellValue('L'.$NewRow, 'Depreciation FOH');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(16);
		
        $sheet->setCellValue('M'.$NewRow, 'Factory Overhead');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
        $sheet->getColumnDimension('M')->setWidth(16);

		$sheet->setCellValue('N'.$NewRow, 'Salary Factory Management');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
        $sheet->getColumnDimension('N')->setWidth(16);

		$sheet->setCellValue('O'.$NewRow, 'Kurs');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
        $sheet->getColumnDimension('O')->setWidth(16);


		$where_bln = "";
        if($bulan > 0){
            $where_bln = "AND MONTH(date) = '".$bulan."' ";
        }

        $where_thn = "";
        if($tahun > 0){
            $where_thn = "AND YEAR(date) = '".$tahun."' ";
        }
		
		$qSupplier	    = "	SELECT * FROM laporan_per_bulan WHERE 1=1 ".$where_bln." ".$where_thn." ORDER BY `date` ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $row_Cek['date'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= $row_Cek['est_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$est_harga	= $row_Cek['est_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$real_material	= $row_Cek['real_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				//$real_harga	= $row_Cek['real_harga'];				
				$real_harga	= ($row_Cek['real_harga_rp']/$row_Cek['kurs']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
				$get_revenue = $this->db
							->select('SUM((( d.price_total / e.qty ) * (a.qty_akhir - a.qty_awal + 1))) AS revenue, AVG(a.kurs) as kurs_det')
							->from('laporan_per_bulan z')
							->join('laporan_per_hari a','z.date=a.date')
							->join('so_detail_header b','a.id_milik=b.id')
							->join('so_bf_detail_header c','b.id_milik=c.id')
							->join('cost_project_detail d','c.id_milik=d.caregory_sub')
							->join('bq_detail_header e','c.id_milik=e.id')
							->where('z.date', $row_Cek['date'])
							->get()
							->result();
				$revenue 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;
				$kurs_det 	= (!empty($get_revenue))?$get_revenue[0]->kurs_det:0;

                $awal_col++;
				$direct_labour	= $revenue;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
				$awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
                $awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$kurs	= $row_Cek['kurs'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kurs);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		
		
		$sheet->setTitle('Report Produksi Per Day');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Report Produksi Per Day '.$bulan.' '.$tahun.' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_material(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq = $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(3);
		$sheet->setCellValue('A'.$Row, 'MATERIAL PLANNING '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Material Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
        $sheet->getColumnDimension('B')->setWidth(70);
        
        $sheet->setCellValue('C'.$NewRow, 'Est Mat');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
        $sheet->getColumnDimension('C')->setWidth(16);
		
		$qSupplier 		= "SELECT * FROM so_estimasi_total_material WHERE id_bq='".$id_bq."' ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
                
                $awal_col++;
				$est_harga	= $row_Cek['last_cost'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
			}
		}
		
		
		$sheet->setTitle('Material Planning');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Material Planning '.$id_bq.''.'_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	//update manual produksi
	public function update_manual_produksi(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
	
		$id_bq			= "BQ-IPP20071L";
		$id_milik		= "140";
		$panjang		= "0";
		$product		= "F-VPN20DN0050-1.3-090-LR-C100-1904034-20071";
		$id_milik_bq	= "5376";
		
		// echo $id_bq."<br>";
		// echo $id_milik."<br>";
		// echo $panjang."<br>";
		// echo $product."<br>";
		// echo $id_milik_bq."<br>";
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
			$qHeader	= $this->db->query("SELECT * FROM bq_component_header WHERE id_product='".$product."' AND id_milik='".$id_milik_bq."' LIMIT 1 ")->result();
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
			$ArrBqHeader['panjang']				= $qHeader[0]->panjang;
			$ArrBqHeader['radius']				= $qHeader[0]->radius;
			$ArrBqHeader['type_elbow']			= $qHeader[0]->type_elbow;
			$ArrBqHeader['angle']				= $qHeader[0]->angle;
			$ArrBqHeader['design']				= $qHeader[0]->design;
			$ArrBqHeader['est']					= $qHeader[0]->est;
			$ArrBqHeader['min_toleransi']		= $qHeader[0]->min_toleransi;
			$ArrBqHeader['max_toleransi']		= $qHeader[0]->max_toleransi;
			$ArrBqHeader['waste']				= $qHeader[0]->waste;
			$ArrBqHeader['area']				= $qHeader[0]->area;
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
				$getDefVal		= $this->db->query("SELECT * FROM bq_component_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." AND id_milik='".$id_milik_bq."' LIMIT 1 ")->result();
				$getDefValNum	= $this->db->query("SELECT * FROM bq_component_default WHERE product_parent='".$qHeader[0]->parent_product."' AND standart_code='".$qHeader[0]->standart_code."' AND diameter='".$qHeader[0]->diameter."' ".$plusSQL." AND id_milik='".$id_milik_bq."' LIMIT 1 ")->num_rows();
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
			$qHeaderHistDef		= $this->db->query("SELECT * FROM so_component_default WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
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
			$qHeaderHist	= $this->db->query("SELECT * FROM so_component_header WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
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
			$qDetail	= $this->db->query("SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$product."' AND a.id_milik='".$id_milik_bq."' ")->result_array();
			foreach($qDetail AS $val2 => $valx2){
				$LoopDetail++;
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
				if ($qHeader[0]->parent_product == 'branch joint' OR $qHeader[0]->parent_product == 'field joint' OR $qHeader[0]->parent_product == 'shop joint') {
					$ArrBqDetail[$LoopDetail]['last_cost']	= $valx2['material_weight'];
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

				$ArrBqDetail[$LoopDetail]['price_mat']		= $valx2['price_mat'];
			}

			//Component Lamination
			$qDetailLam	= $this->db->query("SELECT * FROM bq_component_lamination WHERE id_product='".$product."' AND id_milik='".$id_milik_bq."' ")->result_array();
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
			$qDetailHist	= $this->db->query("SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
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
			$qDetailPlus	= $this->db->query("SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$product."' AND a.id_milik='".$id_milik_bq."' ")->result_array();
			foreach($qDetailPlus AS $val3 => $valx3){
				$LoopDetailPlus++;
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
				$ArrBqDetailPlus[$LoopDetailPlus]['last_full']		= $valx3['last_full'];
				$ArrBqDetailPlus[$LoopDetailPlus]['last_cost']		= $valx3['last_cost'];
				$ArrBqDetailPlus[$LoopDetailPlus]['rev']			= $qHeader[0]->rev;
				$ArrBqDetailPlus[$LoopDetailPlus]['price_mat']		= $valx3['price_mat'];
			}

			//Insert Component Detail Plus To Hist
			$qDetailPlusHist	= $this->db->query("SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
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
			$qDetailAdd		= $this->db->query("SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$product."' AND a.id_milik='".$id_milik_bq."' ")->result_array();
			if(!empty($qDetailAdd)){
				foreach($qDetailAdd AS $val4 => $valx4){
					$LoopDetailAdd++;
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
					$ArrBqDetailAdd[$LoopDetailAdd]['last_full']	= $valx4['last_full'];
					$ArrBqDetailAdd[$LoopDetailAdd]['last_cost']	= $valx4['last_cost'];
					$ArrBqDetailAdd[$LoopDetailAdd]['rev']				= $qHeader[0]->rev;
					$ArrBqDetailAdd[$LoopDetailAdd]['price_mat']	= $valx4['price_mat'];
				}
			}

			//Insert Component Detail Add To Hist
			$qDetailAddHist		= $this->db->query("SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
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
			$qDetailFooter	= $this->db->query("SELECT * FROM bq_component_footer WHERE id_product='".$product."' AND id_milik='".$id_milik_bq."' ")->result_array();
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
			$qDetailFooterHist		= $this->db->query("SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->result_array();
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
			$this->db->update('so_detail_header', $ArrDetBq2);

			//Insert Batch Histories
			// if(!empty($ArrBqHeaderHist)){
			// 	$this->db->insert_batch('hist_so_component_header', $ArrBqHeaderHist);
			// }
			// if(!empty($ArrBqDetailHist)){
			// 	$this->db->insert_batch('hist_so_component_detail', $ArrBqDetailHist);
			// }
			// if(!empty($ArrBqDetailPlusHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_plus', $ArrBqDetailPlusHist);
			// }
			// if(!empty($ArrBqDetailAddHist)){
			// 	$this->db->insert_batch('hist_so_component_detail_add', $ArrBqDetailAddHist);
			// }
			// if(count($ArrBqFooterHist)>0){
			// 	$this->db->insert_batch('hist_so_component_footer', $ArrBqFooterHist);
			// }
			// if(!empty($ArrBqDefaultHist)){
			// 	$this->db->insert_batch('hist_so_component_default', $ArrBqDefaultHist);
			// }

			//Delete BQ Component
			$this->db->delete('so_component_header', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_detail', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_lamination', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_detail_plus', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_detail_add', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_footer', array('id_bq' => $id_bq, 'id_milik' => $id_milik));
			$this->db->delete('so_component_default', array('id_bq' => $id_bq, 'id_milik' => $id_milik));

			//Insert BQ Component
			if(!empty($ArrBqHeader)){
				$this->db->insert('so_component_header', $ArrBqHeader);
			}
			if(!empty($ArrBqDetail)){
				$this->db->insert_batch('so_component_detail', $ArrBqDetail);
			}
			if(!empty($ArrBqDetailLam)){
				$this->db->insert_batch('so_component_lamination', $ArrBqDetailLam);
			}
			if(!empty($ArrBqDetailPlus)){
				$this->db->insert_batch('so_component_detail_plus', $ArrBqDetailPlus);
			}
			if(!empty($ArrBqDetailAdd)){
				$this->db->insert_batch('so_component_detail_add', $ArrBqDetailAdd);
			}
			if(!empty($ArrBqFooter)){
				$this->db->insert_batch('so_component_footer', $ArrBqFooter);
			}
			if(!empty($ArrBqDefault)){
				$this->db->insert('so_component_default', $ArrBqDefault);
			}

			// $this->db->where('id_bq', $id_bq);
			// $this->db->update('so_header', $UpdateBQ);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
				'status'	=> 0
			);
			echo "Gagal Insert";
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pesan'		=>'Estimation structure bq data success. Thanks ...',
				'status'	=> 1
			);
			echo "Success Insert";
			// history('Estimation Sebagian Structure BQ (Tarik dari Est Sebelumnya) in Final Drawing with code : '.$id_bq.' / '.$id_milik.' / '.$product);
		}

		// echo json_encode($Arr_Kembali);
	}

	//update manual produksi
	public function insert_manual_produksi(){
		$id_bq 			= "BQ-IPP19064L";
		$data_session	= $this->session->userdata;
		$Imp			= explode('-', $id_bq);

		$ArrInsertPro = array(
			'id_produksi' => "PRO-".$Imp[1],
			'no_ipp' => $Imp[1],
			'jalur' => 'FD',
			'so_number' => "SO-".$Imp[1],
			'created_by' => $data_session['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		);
	
		$qDet_Gt	= "SELECT a.*, b.id AS id_milik , b.id_product AS id_product FROM so_detail_detail a INNER JOIN so_detail_header b ON a.id_bq_header = b.id_bq_header  WHERE a.id_bq = '".$id_bq."' ";
		$restBq		= $this->db->query($qDet_Gt)->result_array();
		 
		$ArrDetalPro = array();
		foreach($restBq AS $val => $valx){
			$ArrDetalPro[$val]['id_milik'] 		= $valx['id_milik']; 
			$ArrDetalPro[$val]['id_produksi'] 	= "PRO-".$Imp[1];
			$ArrDetalPro[$val]['id_delivery'] 	= $valx['id_delivery'];
			$ArrDetalPro[$val]['sts_delivery'] 	= $valx['sts_delivery'];
			$ArrDetalPro[$val]['sub_delivery'] 	= $valx['sub_delivery'];
			$ArrDetalPro[$val]['id_category'] 	= $valx['id_category'];
			$ArrDetalPro[$val]['id_product'] 	= $valx['id_product'];
			$ArrDetalPro[$val]['product_ke'] 	= $valx['product_ke'];
			$ArrDetalPro[$val]['qty'] 			= $valx['qty'];
		}

		$this->db->trans_start();
			$this->db->insert('production_header', $ArrInsertPro);
			$this->db->insert_batch('production_detail', $ArrDetalPro);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Estimation structure bq data failed. Please try again later ...',
				'status'	=> 0
			);
			echo "Gagal Insert";
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pesan'		=>'Estimation structure bq data success. Thanks ...',
				'status'	=> 1
			);
			echo "Success Insert";
			// history('Estimation Sebagian Structure BQ (Tarik dari Est Sebelumnya) in Final Drawing with code : '.$id_bq.' / '.$id_milik.' / '.$product);
		}

		// echo json_encode($Arr_Kembali);
	}
    
	//PRODUCTI PRODUCT Report
	public function report_produksi_product(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		// echo $controller;
		// print_r($Arr_Akses); exit;
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Production Daily Report',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Production Daily Report');
		$this->load->view('Cron/report_produksi_product',$data);
	}
	
	public function get_data_side_report_product(){
		$this->cron_model->get_json_report_product();
	}
	
	public function excel_report_bef(){
  		//membuat objek PHPExcel
  		set_time_limit(0);
  		ini_set('memory_limit','1024M');
		
  		$tanggal	= $this->uri->segment(3);
		$bulan		= $this->uri->segment(4);
		$tahun		= $this->uri->segment(5);
		$tgl_awal	= $this->uri->segment(6);
		$tgl_akhir	= $this->uri->segment(7);

  		$this->load->library("PHPExcel");
  		// $this->load->library("PHPExcel/Writer/Excel2007");
  		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

  		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
  		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$where_tgl = "";
        if($tanggal > 0){
            $where_tgl = "AND DAY(a.status_date) = '".$tanggal."' ";
        }
		
		$where_bln = "";
        if($bulan > 0){
            $where_bln = "AND MONTH(a.status_date) = '".$bulan."' ";
        }

        $where_thn = "";
        if($tahun > 0){
            $where_thn = "AND YEAR(a.status_date) = '".$tahun."' ";
        }
		
		$where_range = "";
        if($tgl_awal > 0){
            $where_range = "AND DATE(a.status_date) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ";
        }
		
		$sql = "
			SELECT
				a.id_produksi,
				a.status_date,
				b.id_category,
				b.id_product,
				b.production_date,
				b.finish_production_date,
				b.terima_spk_date,
				b.est_real,
				b.id_milik,
				b.id,
				(SELECT SUM(x.material_terpakai) FROM data_real_produksi x WHERE x.id_production_detail=a.id_production_detail) AS total_real
			FROM
				production_real_detail a
					LEFT JOIN production_detail b ON a.id_production_detail=b.id
		    WHERE b.id_category <> '' ".$where_tgl." ".$where_bln." ".$where_thn." ".$where_range." 
			GROUP BY
				a.id_production_detail
			ORDER BY
				a.status_date DESC
		";
		// echo $sql;exit;
		$product    = $this->db->query($sql)->result_array();

  		$Row		= 1;
  		$NewRow		= $Row+1;
  		$Col_Akhir	= $Cols	= getColsChar(21);
  		$sheet->setCellValue('A'.$Row, 'PRODUCTION DAILY REPORT ');
  		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
  		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

  		$NewRow	= $NewRow +2;
  		$NextRow= $NewRow +1;

  		$sheet->setCellValue('A'.$NewRow, 'No');
  		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
  		$sheet->getColumnDimension('A')->setAutoSize(true);

  		$sheet->setCellValue('B'.$NewRow, 'IPP Number');
  		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
  		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Production Input');
  		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
  		$sheet->getColumnDimension('C')->setAutoSize(true);
		
  		$sheet->setCellValue('D'.$NewRow, 'Finish Date');
  		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
  		$sheet->getColumnDimension('D')->setAutoSize(true);

  		$sheet->setCellValue('E'.$NewRow, 'SPK Date');
  		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
  		$sheet->getColumnDimension('E')->setAutoSize(true);

  		$sheet->setCellValue('F'.$NewRow, 'Production Date');
  		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
  		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sheet->setCellValue('G'.$NewRow, 'SPK Number');
  		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
  		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Product');
  		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
  		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'Id Product');
  		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
  		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'Diameter 1');
  		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
  		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Diameter 2');
  		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
  		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Pressure');
  		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
  		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Liner');
  		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
  		$sheet->getColumnDimension('M')->setAutoSize(true);
		
		$sheet->setCellValue('N'.$NewRow, 'Qty Order');
  		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
  		$sheet->getColumnDimension('N')->setAutoSize(true);
		
		$sheet->setCellValue('O'.$NewRow, 'Qty Produksi');
  		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
  		$sheet->getColumnDimension('O')->setAutoSize(true);
		
		$sheet->setCellValue('P'.$NewRow, 'Urutan');
  		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
  		$sheet->getColumnDimension('P')->setAutoSize(true);	

		$sheet->setCellValue('Q'.$NewRow, 'Thickness Est');
  		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
  		$sheet->getColumnDimension('Q')->setAutoSize(true);
		
		$sheet->setCellValue('R'.$NewRow, 'Tolerance Max');
  		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
  		$sheet->getColumnDimension('R')->setAutoSize(true);
		
		$sheet->setCellValue('S'.$NewRow, 'Tolerance Min');
  		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
  		$sheet->getColumnDimension('S')->setAutoSize(true);
		
		$sheet->setCellValue('T'.$NewRow, 'Thickness Actual');
  		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
  		$sheet->getColumnDimension('T')->setAutoSize(true);
		
		$sheet->setCellValue('U'.$NewRow, 'Total Real');
  		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
  		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
  		$sheet->getColumnDimension('U')->setAutoSize(true);

      
		if($product){
			$awal_row	= $NextRow;
			$no=0;
			foreach($product as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
	
				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_produksi	= str_replace('PRO-','',$row_Cek['id_produksi']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$prodate = (!empty($row_Cek['production_date']))?$row_Cek['production_date']:$row_Cek['status_date'];
				$awal_col++;
				$status_prodate	= date('d-m-Y', strtotime($prodate));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status_prodate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$findate 		= (!empty($row_Cek['finish_production_date']))?date('d-m-Y', strtotime($row_Cek['finish_production_date'])):'-';
				$awal_col++;
				$status_findate	= $findate;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status_findate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$spkdate 		= (!empty($row_Cek['terima_spk_date']))?date('d-m-Y', strtotime($row_Cek['terima_spk_date'])):'-';
				$awal_col++;
				$status_spkdate	= $spkdate;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status_spkdate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$status_date	= date('d-m-Y', strtotime($row_Cek['status_date']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status_date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$no_spk	= get_name_report(get_jalur($row_Cek['id_produksi'])['bq'], 'no_spk', 'id', $row_Cek['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_category	= ucwords(strtolower($row_Cek['id_category']));
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_product	= $row_Cek['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$diameter_1	= get_name_report(get_jalur($row_Cek['id_produksi'])['bq'], 'diameter_1', 'id', $row_Cek['id_milik']); 
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter_1);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$diameter_2	= get_name_report(get_jalur($row_Cek['id_produksi'])['bq'], 'diameter_2', 'id', $row_Cek['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter_2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_produksi	= substr(get_name_report(get_jalur($row_Cek['id_produksi'])['bq'], 'series', 'id', $row_Cek['id_milik']),1,4);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_produksi	= substr(get_name_report(get_jalur($row_Cek['id_produksi'])['bq'], 'series', 'id', $row_Cek['id_milik']),6);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_produksi	= get_name_report(get_jalur($row_Cek['id_produksi'])['bq'], 'qty', 'id', $row_Cek['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$qty_produksi = get_name_report('product_range', 'qty_akhir', 'id', $row_Cek['id']) - get_name_report('product_range', 'qty_awal', 'id', $row_Cek['id']) + 1;
				if(get_name_report('product_range', 'qty_awal', 'id', $row_Cek['id']) == get_name_report('product_range', 'qty_akhir', 'id', $row_Cek['id'])){
					$qty_produksi = 1;
				}
			
				$awal_col++;
				$id_produksi	= $qty_produksi;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$rgn = get_name_report('product_range', 'qty_awal', 'id', $row_Cek['id'])." - ".get_name_report('product_range', 'qty_akhir', 'id', $row_Cek['id']);
				if(get_name_report('product_range', 'qty_awal', 'id', $row_Cek['id']) == get_name_report('product_range', 'qty_akhir', 'id', $row_Cek['id'])){
					$rgn = get_name_report('product_range', 'qty_awal', 'id', $row_Cek['id']);
				}
				
				$awal_col++;
				$id_produksi	= $rgn;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				
				$awal_col++;
				$id_produksi	= number_format(get_name_report(get_jalur($row_Cek['id_produksi'])['comp'], 'est', 'id_milik', $row_Cek['id_milik']),2);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_produksi	= number_format(get_name_report(get_jalur($row_Cek['id_produksi'])['comp'], 'max_toleransi', 'id_milik', $row_Cek['id_milik']) * 100 ,2);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_produksi	= number_format(get_name_report(get_jalur($row_Cek['id_produksi'])['comp'], 'min_toleransi', 'id_milik', $row_Cek['id_milik']) * 100 ,2);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_produksi	= number_format($row_Cek['est_real'],2);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_produksi	= number_format($row_Cek['total_real'],3);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			}
		}


  		$sheet->setTitle('Production Daily Report');
  		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
  		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  		ob_end_clean();
  		//sesuaikan headernya
  		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  		header("Cache-Control: no-store, no-cache, must-revalidate");
  		header("Cache-Control: post-check=0, pre-check=0", false);
  		header("Pragma: no-cache");
  		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  		//ubah nama file saat diunduh
  		header('Content-Disposition: attachment;filename="Excel Production Daily Report '.date('YmdHis').'.xls"');
  		//unduh file
  		$objWriter->save("php://output");
  	}

	  public function excel_report(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
	  
		$tanggal	= $this->uri->segment(3);
	  $bulan		= $this->uri->segment(4);
	  $tahun		= $this->uri->segment(5);
	  $tgl_awal	= $this->uri->segment(6);
	  $tgl_akhir	= $this->uri->segment(7);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

	  $style_header = array(
		  'borders' => array(
			  'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
		  ),
		  'fill' => array(
			  'type' => PHPExcel_Style_Fill::FILL_SOLID,
			  'color' => array('rgb'=>'e0e0e0'),
		  ),
		  'font' => array(
			  'bold' => true,
		  ),
		  'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		  )
	  );

	  $style_header2 = array(
		  'fill' => array(
			  'type' => PHPExcel_Style_Fill::FILL_SOLID,
			  'color' => array('rgb'=>'e0e0e0'),
		  ),
		  'font' => array(
			  'bold' => true,
		  ),
		  'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		  )
	  );

	  $styleArray = array(
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
			  'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
		  )
		);
	  $styleArray3 = array(
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			),
			'borders' => array(
			  'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
		  )
		);
	   $styleArray4 = array(
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			),
			'borders' => array(
			  'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
		  )
		);
	  $styleArray1 = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
	  $styleArray2 = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
			  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();
	  
	  $where_tgl = "";
	  if($tanggal > 0){
		  $where_tgl = "AND DAY(a.status_date) = '".$tanggal."' ";
	  }
	  
	  $where_bln = "";
	  if($bulan > 0){
		  $where_bln = "AND MONTH(a.status_date) = '".$bulan."' ";
	  }

	  $where_thn = "";
	  if($tahun > 0){
		  $where_thn = "AND YEAR(a.status_date) = '".$tahun."' ";
	  }
	  
	  $where_range = "";
	  if($tgl_awal > 0){
		  $where_range = "AND DATE(a.status_date) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ";
	  }
	  
	  $sql = "
		  	SELECT
			  	a.*
		  	FROM
			  	laporan_per_hari a
		  	WHERE 
				a.id_category <> '' ".$where_tgl." ".$where_bln." ".$where_thn." ".$where_range." 
			ORDER BY 
				a.status_date DESC
	  ";
	//   echo $sql;exit;
	  	$product    = $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(21);
		$sheet->setCellValue('A'.$Row, 'PRODUCTION DAILY REPORT ');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('B'.$NewRow, 'Warehouse Produksi');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
	  
	  $sheet->setCellValue('C'.$NewRow, 'Customer');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
	  
		$sheet->setCellValue('D'.$NewRow, 'Project');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'SO Number');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F'.$NewRow, 'SPK Number');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

	  $sheet->setCellValue('G'.$NewRow, 'Start Date');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
	  
	  $sheet->setCellValue('H'.$NewRow, 'Finish Date');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
	  
	  $sheet->setCellValue('I'.$NewRow, '');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
	  
	  $sheet->setCellValue('J'.$NewRow, 'Product');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
	  
	  $sheet->setCellValue('K'.$NewRow, 'Diameter 1');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
	  
	  $sheet->setCellValue('L'.$NewRow, 'Diameter 2');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
	  
	  $sheet->setCellValue('M'.$NewRow, 'Length');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true);
	  
	  $sheet->setCellValue('N'.$NewRow, 'Thickness');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
		$sheet->getColumnDimension('N')->setAutoSize(true);
	  
	  $sheet->setCellValue('O'.$NewRow, 'Liner');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setAutoSize(true);
	  
	  $sheet->setCellValue('P'.$NewRow, 'Qty Order');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
		$sheet->getColumnDimension('P')->setAutoSize(true);	

	  $sheet->setCellValue('Q'.$NewRow, 'Qty Produksi');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
		$sheet->getColumnDimension('Q')->setAutoSize(true);
	  
	  $sheet->setCellValue('R'.$NewRow, 'Urutan');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
		$sheet->getColumnDimension('R')->setAutoSize(true);
	  
	  $sheet->setCellValue('S'.$NewRow, 'Veils');
		$sheet->getStyle('S'.$NewRow.':T'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':T'.$NewRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);
	  
	  $sheet->setCellValue('U'.$NewRow, '');
		$sheet->getStyle('U'.$NewRow.':V'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':V'.$NewRow);
		$sheet->getColumnDimension('U')->setAutoSize(true);
	  
	  $sheet->setCellValue('W'.$NewRow, 'CSM');
		$sheet->getStyle('W'.$NewRow.':X'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('W'.$NewRow.':X'.$NewRow);
		$sheet->getColumnDimension('W')->setAutoSize(true);

		$sheet->setCellValue('Y'.$NewRow, 'Roving');
		$sheet->getStyle('Y'.$NewRow.':Z'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('Y'.$NewRow.':Z'.$NewRow);
		$sheet->getColumnDimension('Y')->setAutoSize(true);

		$sheet->setCellValue('AA'.$NewRow, '');
		$sheet->getStyle('AA'.$NewRow.':AB'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AA'.$NewRow.':AB'.$NewRow);
		$sheet->getColumnDimension('AA')->setAutoSize(true);

		$sheet->setCellValue('AC'.$NewRow, 'WR');
		$sheet->getStyle('AC'.$NewRow.':AD'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AC'.$NewRow.':AD'.$NewRow);
		$sheet->getColumnDimension('AC')->setAutoSize(true);

		$sheet->setCellValue('AE'.$NewRow, 'Resin');
		$sheet->getStyle('AE'.$NewRow.':AF'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AE'.$NewRow.':AF'.$NewRow);
		$sheet->getColumnDimension('AE')->setAutoSize(true);

		$sheet->setCellValue('AG'.$NewRow, 'Catalys');
		$sheet->getStyle('AG'.$NewRow.':AH'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AG'.$NewRow.':AH'.$NewRow);
		$sheet->getColumnDimension('AG')->setAutoSize(true);

		$sheet->setCellValue('AI'.$NewRow, 'Lainnya');
		$sheet->getStyle('AI'.$NewRow.':AJ'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AI'.$NewRow.':AJ'.$NewRow);
		$sheet->getColumnDimension('AI')->setAutoSize(true);

		$sheet->setCellValue('AK'.$NewRow, 'Add');
		$sheet->getStyle('AK'.$NewRow.':AL'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AK'.$NewRow.':AL'.$NewRow);
		$sheet->getColumnDimension('AK')->setAutoSize(true);

		$sheet->setCellValue('AM'.$NewRow, 'Total Material (Kg)');
		$sheet->getStyle('AM'.$NewRow.':AM'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('AM'.$NewRow.':AM'.$NextRow);
		$sheet->getColumnDimension('AM')->setAutoSize(true);

		$sheet->setCellValue('AN'.$NewRow, 'Work Hour');
		$sheet->getStyle('AN'.$NewRow.':AN'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('AN'.$NewRow.':AN'.$NextRow);
		$sheet->getColumnDimension('AN')->setAutoSize(true);

		$sheet->setCellValue('AO'.$NewRow, 'Man Power');
		$sheet->getStyle('AO'.$NewRow.':AO'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('AO'.$NewRow.':AO'.$NextRow);
		$sheet->getColumnDimension('AO')->setAutoSize(true);

		$sheet->setCellValue('AP'.$NewRow, 'Man Hour');
		$sheet->getStyle('AP'.$NewRow.':AP'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('AP'.$NewRow.':AP'.$NextRow);
		$sheet->getColumnDimension('AP')->setAutoSize(true);

		$NewRow	= $NextRow;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('S'.$NewRow, 'Material');
		$sheet->getStyle('S'.$NewRow.':S'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NewRow);
		$sheet->getColumnDimension('S')->setAutoSize(true);

		$sheet->setCellValue('T'.$NewRow, 'Berat');
		$sheet->getStyle('T'.$NewRow.':T'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NewRow);
		$sheet->getColumnDimension('T')->setAutoSize(true);

		$sheet->setCellValue('U'.$NewRow, 'Material');
		$sheet->getStyle('U'.$NewRow.':U'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':U'.$NewRow);
		$sheet->getColumnDimension('U')->setAutoSize(true);

		$sheet->setCellValue('V'.$NewRow, '');
		$sheet->getStyle('V'.$NewRow.':V'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('V'.$NewRow.':V'.$NewRow);
		$sheet->getColumnDimension('V')->setAutoSize(true);

		$sheet->setCellValue('W'.$NewRow, '');
		$sheet->getStyle('W'.$NewRow.':W'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('W'.$NewRow.':W'.$NewRow);
		$sheet->getColumnDimension('W')->setAutoSize(true);

		$sheet->setCellValue('X'.$NewRow, 'Berat');
		$sheet->getStyle('X'.$NewRow.':X'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('X'.$NewRow.':X'.$NewRow);
		$sheet->getColumnDimension('X')->setAutoSize(true);

		$sheet->setCellValue('Y'.$NewRow, 'Material');
		$sheet->getStyle('Y'.$NewRow.':Y'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('Y'.$NewRow.':Y'.$NewRow);
		$sheet->getColumnDimension('Y')->setAutoSize(true);

		$sheet->setCellValue('Z'.$NewRow, 'Berat');
		$sheet->getStyle('Z'.$NewRow.':Z'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('Z'.$NewRow.':Z'.$NewRow);
		$sheet->getColumnDimension('Z')->setAutoSize(true);

		$sheet->setCellValue('AA'.$NewRow, '');
		$sheet->getStyle('AA'.$NewRow.':AA'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AA'.$NewRow.':AA'.$NewRow);
		$sheet->getColumnDimension('AA')->setAutoSize(true);

		$sheet->setCellValue('AB'.$NewRow, '');
		$sheet->getStyle('AB'.$NewRow.':AB'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AB'.$NewRow.':AB'.$NewRow);
		$sheet->getColumnDimension('AB')->setAutoSize(true);

		$sheet->setCellValue('AC'.$NewRow, 'Material');
		$sheet->getStyle('AC'.$NewRow.':AC'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AC'.$NewRow.':AC'.$NewRow);
		$sheet->getColumnDimension('AC')->setAutoSize(true);

		$sheet->setCellValue('AD'.$NewRow, 'Berat');
		$sheet->getStyle('AD'.$NewRow.':AD'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AD'.$NewRow.':AD'.$NewRow);
		$sheet->getColumnDimension('AD')->setAutoSize(true);

		$sheet->setCellValue('AE'.$NewRow, 'Material');
		$sheet->getStyle('AE'.$NewRow.':AE'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AE'.$NewRow.':AE'.$NewRow);
		$sheet->getColumnDimension('AE')->setAutoSize(true);

		$sheet->setCellValue('AF'.$NewRow, 'Berat');
		$sheet->getStyle('AF'.$NewRow.':AF'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AF'.$NewRow.':AF'.$NewRow);
		$sheet->getColumnDimension('AF')->setAutoSize(true);

		$sheet->setCellValue('AG'.$NewRow, 'Material');
		$sheet->getStyle('AG'.$NewRow.':AG'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AG'.$NewRow.':AG'.$NewRow);
		$sheet->getColumnDimension('AG')->setAutoSize(true);

		$sheet->setCellValue('AH'.$NewRow, 'Berat');
		$sheet->getStyle('AH'.$NewRow.':AH'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AH'.$NewRow.':AH'.$NewRow);
		$sheet->getColumnDimension('AH')->setAutoSize(true);

		$sheet->setCellValue('AI'.$NewRow, 'Material');
		$sheet->getStyle('AI'.$NewRow.':AI'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AI'.$NewRow.':AI'.$NewRow);
		$sheet->getColumnDimension('AI')->setAutoSize(true);

		$sheet->setCellValue('AJ'.$NewRow, 'Berat');
		$sheet->getStyle('AJ'.$NewRow.':AJ'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AJ'.$NewRow.':AJ'.$NewRow);
		$sheet->getColumnDimension('AJ')->setAutoSize(true);

		$sheet->setCellValue('AK'.$NewRow, 'Material');
		$sheet->getStyle('AK'.$NewRow.':AK'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AK'.$NewRow.':AK'.$NewRow);
		$sheet->getColumnDimension('AK')->setAutoSize(true);

		$sheet->setCellValue('AL'.$NewRow, 'Berat');
		$sheet->getStyle('AL'.$NewRow.':AL'.$NewRow)->applyFromArray($style_header);
		$sheet->mergeCells('AL'.$NewRow.':AL'.$NewRow);
		$sheet->getColumnDimension('AL')->setAutoSize(true);

		$SERACH_DETAIL_IPP 			= get_detail_ipp();
		$SERACH_DETAIL_SPEC 		= get_detail_spec_fd();
		$SEARCH_DETAIL_BERAT 		= get_input_produksi_detail();
		$SEARCH_DETAIL_BERAT_PLUS 	= get_input_produksi_plus();
		$SEARCH_DETAIL_BERAT_ADD 	= get_input_produksi_add();
		$SEARCH_DETAIL_BERAT_PLUS_EX 	= get_input_produksi_plus_exclude();
	
	  if($product){
		  $awal_row	= $NewRow;
		  $no=0;
		  foreach($product as $key => $row_Cek){
			  $no++;
			  $awal_row++;
			  $awal_col	= 0;

			  $NO_IPP = str_replace('PRO-','',$row_Cek['id_produksi']);
			  $GET_PRODUKSI_DETAIL 	= $this->db->get_where('production_detail',array('id'=>$row_Cek['id_production_detail']))->result();
			  $kode_hist			= (!empty($GET_PRODUKSI_DETAIL[0]->print_merge_date))?$GET_PRODUKSI_DETAIL[0]->print_merge_date:'-';
			  $id_milik				= $row_Cek['id_milik'];
			  $QTY_ORDER			= (!empty($GET_PRODUKSI_DETAIL[0]->qty))?$GET_PRODUKSI_DETAIL[0]->qty:'-';
			  $START_PRODUKSI		= (!empty($GET_PRODUKSI_DETAIL[0]->production_date))?$GET_PRODUKSI_DETAIL[0]->production_date:'-';
			  $SELESAI_PRODUKSI		= (!empty($GET_PRODUKSI_DETAIL[0]->finish_production_date))?$GET_PRODUKSI_DETAIL[0]->finish_production_date:'-';
			  $GET_PRODUKSI_PARSIAL = $this->db->get_where('production_spk_parsial',array('id_milik'=>$id_milik,'created_date'=>$kode_hist))->result();
			  $id_gudang			= (!empty($GET_PRODUKSI_PARSIAL[0]->id_gudang))?$GET_PRODUKSI_PARSIAL[0]->id_gudang:'-';
			//   $GET_PRODUKSI_FIN 	= $this->db->get_where('production_detail',array('id_milik'=>$id_milik,'print_merge_date'=>$kode_hist))->result();
			//   $SELESAI_PRODUKSI		= (!empty($GET_PRODUKSI_FIN[0]->finish_production_date))?$GET_PRODUKSI_FIN[0]->finish_production_date:'-';
			 
			  $awal_col++;
			  $nomor	= $no;
			  $Cols		= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nomor);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $warehouse	= get_name('warehouse','nm_gudang','id',$id_gudang);
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $warehouse);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $tandaIPP = substr($NO_IPP,0,4);
			  if($tandaIPP != 'IPPT'){
				$customer		= $SERACH_DETAIL_IPP[$NO_IPP]['nm_customer'];
				$project		= $SERACH_DETAIL_IPP[$NO_IPP]['nm_project'];
				$so_number		= $SERACH_DETAIL_IPP[$NO_IPP]['so_number'];
				$length			= $SERACH_DETAIL_SPEC[$id_milik]['length'];
				$thickness		= $SERACH_DETAIL_SPEC[$id_milik]['thickness'];
				$no_spk			= (!empty($GET_PRODUKSI_DETAIL[0]->no_spk))?$GET_PRODUKSI_DETAIL[0]->no_spk:'-';
			  }
			  else{
				$getDetailTanki = $this->tanki_model->get_ipp_detail($NO_IPP);
				$customer		= $getDetailTanki['customer'];
				$project		= $getDetailTanki['nm_project'];
				$so_number		= $row_Cek['no_so'];
				$length			= '';
				$thickness		= '';
				$no_spk			= $row_Cek['no_spk'];
			  }

			  $awal_col++;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $customer);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $project);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $so_number);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $no_spk);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $start_date	= $START_PRODUKSI;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $start_date);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $finish_date	= $SELESAI_PRODUKSI;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $finish_date);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $input_date	= $row_Cek['id_production_detail'];
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $input_date);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $id_category	= $row_Cek['id_category'];
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $id_category);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $diameter	= $row_Cek['diameter'];
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $diameter);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $diameter2	= $row_Cek['diameter2'];
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $diameter2);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $length);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $thickness);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $liner	= $row_Cek['liner'];
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $liner);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $QTY_ORDER);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			
			  $awal_col++;
			  $qty_produksi = $row_Cek['qty_akhir'] - $row_Cek['qty_awal'] + 1;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $qty_produksi);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $urutan	= $row_Cek['qty_awal'].'-'.$row_Cek['qty_akhir'];
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $urutan);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
			
			  $awal_col++;
			  $nm_veil	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['nm_material']:'';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_veil);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_veil	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['terpakai']:0;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_veil);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $nm_veil_carbon		= '';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_veil_carbon);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_veil_carbon	= '';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_veil_carbon);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $nm_csm	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['nm_material']:'';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_csm);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_cms	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['terpakai']:0;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_cms);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $nm_rooving	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['nm_material']:'';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_rooving);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_rooving	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['terpakai']:0;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_rooving);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $nm_rooving_carbon		= '';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_rooving_carbon);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_rooving_carbon	= '';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_rooving_carbon);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $nm_wr	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['nm_material']:'';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_wr);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_wr	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['terpakai']:0;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_wr);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $nm_resin	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['nm_material']:'';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_resin);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_resin	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']:0;
			  $berat_resin_tc	= (!empty($SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']:0;
			  $berat_resin_sum = $berat_resin + $berat_resin_tc;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_resin_sum);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $nm_catalys	= (!empty($SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['nm_material']))?$SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['nm_material']:'';
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $nm_catalys);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			  $awal_col++;
			  $berat_catalys	= (!empty($SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['terpakai']:0;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_catalys);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			$berat_lainnya	= (!empty($SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['terpakai']:0;
			$berat_add		= (!empty($SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['terpakai']))?$SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['terpakai']:0;
			$nm_lainnya		= (!empty($SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['nm_material']))?$SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['nm_material']:'';
			$nm_add			= (!empty($SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['nm_material']))?$SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['nm_material']:'';

			$awal_col++;
			$Cols			= getColsChar($awal_col);
			$sheet->setCellValue($Cols.$awal_row, $nm_lainnya);
			$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			$awal_col++;
			$Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_lainnya);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $Cols			= getColsChar($awal_col);
			$sheet->setCellValue($Cols.$awal_row, $nm_add);
			$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

			$awal_col++;
			$Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $berat_add);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);


			  $TOTAL_MATERIAL = $berat_veil+$berat_cms+$berat_rooving+$berat_wr+$berat_resin_sum+$berat_catalys+$berat_lainnya+$berat_add;

			  $awal_col++;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $TOTAL_MATERIAL);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $GET_DETAIL_SO = $this->db->get_where('so_detail_header',array('id'=>$id_milik))->result();
				$WH = 0;
				$MP = 0;
				$MH = 0;
				if(!empty($GET_DETAIL_SO)){
					$WH = $GET_DETAIL_SO[0]->total_time;
					$MP = $GET_DETAIL_SO[0]->man_power;
					$MH = $GET_DETAIL_SO[0]->man_hours;
				}

			  $awal_col++;
			  $work_hours	= $qty_produksi * $WH;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $work_hours);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $man_power	= $qty_produksi * $MP;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $man_power);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

			  $awal_col++;
			  $man_hours	= $qty_produksi * $MH;
			  $Cols			= getColsChar($awal_col);
			  $sheet->setCellValue($Cols.$awal_row, $man_hours);
			  $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			  

		  }
	  }


		$sheet->setTitle('Production Daily Report');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="production-daily-report.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	//report produksi WIP
	public function report_produksi_wip(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		// echo $controller;
		// print_r($Arr_Akses); exit;
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Production Report WIP',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Report Produksi WIP');
		$this->load->view('Cron/report_produksi_wip',$data);
	}

	public function getDataJSON_WIP(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON_WIP(
            $requestData['bulan'],
            $requestData['tahun'],
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
            $view	= "<button type='button' class='btn btn-sm btn-warning detail' title='Look Data' data-tanggal='".$row['date']."'><i class='fa fa-eye'></i></button>";
			$excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan_wip/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			$pdf='';

			$get_revenue = $this->db
							->select('SUM((( d.price_total / e.qty ) * (a.qty_akhir - a.qty_awal + 1))) AS revenue')
							->from('laporan_per_bulan z')
							->join('laporan_per_hari a','z.date=a.date')
							->join('so_detail_header b','a.id_milik=b.id')
							->join('so_bf_detail_header c','b.id_milik=c.id')
							->join('cost_project_detail d','c.id_milik=d.caregory_sub')
							->join('bq_detail_header e','c.id_milik=e.id')
							->where('z.date', $row['date'])
							->get()
							->result();
			$revenue 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;

            $nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$view."".$pdf."".$excel."</div>";
            $nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['date']))."</div>";
            $nestedData[]	= "<div align='right' class='text-blue'>".number_format($row['est_material'],2)."</div>";
            $nestedData[]	= "<div align='right' class='text-blue'><b>".number_format($row['est_harga'],2)."</b></div>";
            $nestedData[]	= "<div align='right' class='text-green'>".number_format($row['real_material'],2)."</div>";
			$nestedData[]	= "<div align='right' class='text-green'><b>".number_format($row['real_harga'],2)."</b></div>";
			$nestedData[]	= "<div align='right'>".number_format($row['real_harga_rp'],2)."</div>"; 
			$nestedData[]	= "<div align='right'>".number_format($row['kurs'],2)."</div>"; 
            $nestedData[]	= "<div align='right' class='text-purple'><b>".number_format(($row['real_harga_rp']/$row['kurs']),2)."</b></div>";
            $nestedData[]	= "<div align='right'>".number_format($revenue,2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";


            $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
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

	public function queryDataJSON_WIP($bulan, $tahun, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
        $where_bln = "";
        if($bulan > 0){
            $where_bln = "AND MONTH(date) = '".$bulan."' ";
        }

        $where_thn = "";
        if($tahun > 0){
            $where_thn = "AND YEAR(date) = '".$tahun."' ";
        }

		$sql = "
			SELECT
				*
			FROM
				laporan_wip_per_bulan
		    WHERE 1=1 ".$where_bln." ".$where_thn." AND (
				`date` LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'date'
			
		);

		$sql .= " ORDER BY `date` DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function excel_lap_bulanan_wip(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$tanggal = $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(2243);
		$sheet->setCellValue('A'.$Row, 'LAPORAN PRODUKSI WIP ('.date('d F Y', strtotime($tanggal)).')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow1= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'IPP');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Product');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow1);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'ID Product');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow1);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Dim');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow1);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Dim 2');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow1);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Pressure');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow1);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Liner');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow1);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Est Material (kg)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow1);
        $sheet->getColumnDimension('H')->setWidth(16);
        
        $sheet->setCellValue('I'.$NewRow, 'Est Price ($)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow1);
        $sheet->getColumnDimension('I')->setWidth(16);
        
        $sheet->setCellValue('J'.$NewRow, 'Aktual Material (kg)');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow1);
        $sheet->getColumnDimension('J')->setWidth(16);
        
        $sheet->setCellValue('K'.$NewRow, 'Aktual Price ($)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow1);
        $sheet->getColumnDimension('K')->setWidth(16);
        
        $sheet->setCellValue('L'.$NewRow, 'Qty');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow1);
        $sheet->getColumnDimension('L')->setWidth(16);
        
        $sheet->setCellValue('M'.$NewRow, 'Revenue');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow1);
        $sheet->getColumnDimension('M')->setWidth(16);
        
        $sheet->setCellValue('N'.$NewRow, 'Direct Labour');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow1);
        $sheet->getColumnDimension('N')->setWidth(16);
        
        $sheet->setCellValue('O'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow1);
        $sheet->getColumnDimension('O')->setWidth(16);
        


        $sheet->setCellValue('P'.$NewRow, 'Consumable');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow1);
        $sheet->getColumnDimension('P')->setWidth(16);

        $sheet->setCellValue('Q'.$NewRow, 'FOH');
		$sheet->getStyle('Q'.$NewRow.':U'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':U'.$NextRow);
        $sheet->getColumnDimension('Q')->setWidth(16);

		$sheet->setCellValue('V'.$NewRow, 'Sales & Marketing');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow1);
		$sheet->getColumnDimension('V')->setWidth(16);

		$sheet->setCellValue('W'.$NewRow, 'Umum & Admin');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow1);
		$sheet->getColumnDimension('W')->setWidth(16);

		$sheet->setCellValue('X'.$NewRow, 'No SPK');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow1);
		$sheet->getColumnDimension('X')->setWidth(16);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
        
        $sheet->setCellValue('Q'.$NewRow, 'Machine Cost');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
        $sheet->getColumnDimension('Q')->setWidth(16);
        
        $sheet->setCellValue('R'.$NewRow, 'Mold mandril Cost');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
        $sheet->getColumnDimension('R')->setWidth(16);
        

        $sheet->setCellValue('S'.$NewRow, 'Depreciation FOH');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
        $sheet->getColumnDimension('S')->setWidth(16);
        
        $sheet->setCellValue('T'.$NewRow, 'Factory Overhead');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
        $sheet->getColumnDimension('T')->setWidth(16);
        
        $sheet->setCellValue('U'.$NewRow, 'Salary Factory Management');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setWidth(16);


		$qSupplier	    = "	SELECT a.*, b.no_spk AS no_spk2 FROM laporan_wip_per_hari a LEFT JOIN so_detail_header b ON a.id_milik = b.id WHERE a.date = '".$tanggal."' ORDER BY a.id_produksi ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$NO_IPP 				= str_replace('PRO-','',$row_Cek['id_produksi']);
				$tandaIPP = substr($NO_IPP,0,4);
				
				$awal_col++;
				$id_produksi	= $row_Cek['id_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_category	= $row_Cek['id_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_product	= $row_Cek['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$diameter	= $row_Cek['diameter'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$diameter2	= $row_Cek['diameter2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$pressure	= $row_Cek['pressure'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $pressure);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$liner	= $row_Cek['liner'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $liner);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$NO_IPP = str_replace('PRO-','',$row_Cek['id_produksi']);
				$qty = $row_Cek['qty_akhir'] - $row_Cek['qty_awal'] + 1;
				$GET_EST_ACT = getEstimasiVsAktual($row_Cek['id_milik'], $NO_IPP, $qty, $row_Cek['id_production_detail']);

				$estimasi_material 	= (!empty($GET_EST_ACT['est_mat']))?$GET_EST_ACT['est_mat']:0;
				$estimasi_price 	= (!empty($GET_EST_ACT['act_mat']))?$GET_EST_ACT['act_mat']:0;
				$real_material 		= (!empty($GET_EST_ACT['est_price']))?$GET_EST_ACT['est_price']:0;
				// $real_material 		= $row_Cek['real_material'];


				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estimasi_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estimasi_price);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$real_harga	= ($row_Cek['real_harga_rp']/$row_Cek['kurs']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
				
				$get_revenue = $this->db
							->select('(d.price_total / e.qty) AS revenue, b.no_spk')
							->from('laporan_wip_per_hari a')
							->join('so_detail_header b','a.id_milik=b.id')
							->join('so_bf_detail_header c','b.id_milik=c.id')
							->join('cost_project_detail d','c.id_milik=d.caregory_sub')
							->join('bq_detail_header e','c.id_milik=e.id')
							->where('a.id_milik', $row_Cek['id_milik'])
							->limit(1)
							->get()
							->result();
				$revenue2 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;
				$no_spk 	= (!empty($get_revenue[0]->no_spk))?$get_revenue[0]->no_spk:'';
				$revenue 	= $revenue2 * $qty;

				if($tandaIPP == 'IPPT'){
					$no_spk 	= $row_Cek['no_spk'];
				}

                $awal_col++;
				$direct_labour	= $qty;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$direct_labour	= $revenue;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                

				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                
                $awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                

                $awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
			}
		}
		
		
		$sheet->setTitle('Report Produksi WIP '.date('d-m-Y', strtotime($tanggal)));
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="report-produksi-wip-'.date('d-m-Y', strtotime($tanggal)).'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_project_wip(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		
		$bulan		= $this->uri->segment(3);
		$tahun		= $this->uri->segment(4);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(	
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
		 $styleArray4 = array(					  
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );  
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(15);
		$sheet->setCellValue('A'.$Row, 'LAPORAN PRODUKSI WIP PER DAY ('.$bulan.' '.$tahun.')');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow1= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Tanggal Produksi');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Est Material (kg)');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow1);
        $sheet->getColumnDimension('B')->setWidth(16);
        
        $sheet->setCellValue('C'.$NewRow, 'Est Price ($)');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow1);
        $sheet->getColumnDimension('C')->setWidth(16);
        
        $sheet->setCellValue('D'.$NewRow, 'Aktual Material (kg)');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow1);
        $sheet->getColumnDimension('D')->setWidth(16);
        
        $sheet->setCellValue('E'.$NewRow, 'Aktual Price ($)');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow1);
        $sheet->getColumnDimension('E')->setWidth(16);
        
        $sheet->setCellValue('F'.$NewRow, 'Revenue');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow1);
        $sheet->getColumnDimension('F')->setWidth(16);
        
        $sheet->setCellValue('G'.$NewRow, 'Direct Labour');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow1);
        $sheet->getColumnDimension('G')->setWidth(16);
        
        $sheet->setCellValue('H'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow1);
        $sheet->getColumnDimension('H')->setWidth(16);

        $sheet->setCellValue('I'.$NewRow, 'Consumable');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow1);
        $sheet->getColumnDimension('I')->setWidth(16);
        
        $sheet->setCellValue('J'.$NewRow, 'FOH');
		$sheet->getStyle('J'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':N'.$NextRow);
        $sheet->getColumnDimension('N')->setWidth(16);

        $sheet->setCellValue('O'.$NewRow, 'Sales & Marketing');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow1);
        $sheet->getColumnDimension('O')->setWidth(16);

		$sheet->setCellValue('P'.$NewRow, 'Umum & Admin');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow1)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow1);
        $sheet->getColumnDimension('P')->setWidth(16);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;

        $sheet->setCellValue('J'.$NewRow, 'Machine Cost');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
        $sheet->getColumnDimension('J')->setWidth(16);
        
        $sheet->setCellValue('K'.$NewRow, 'Mold mandril Cost');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
        $sheet->getColumnDimension('K')->setWidth(16);
        
        $sheet->setCellValue('L'.$NewRow, 'Depreciation FOH');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(16);
		
        $sheet->setCellValue('M'.$NewRow, 'Factory Overhead');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
        $sheet->getColumnDimension('M')->setWidth(16);

		$sheet->setCellValue('N'.$NewRow, 'Salary Factory Management');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
        $sheet->getColumnDimension('N')->setWidth(16);

		$sheet->setCellValue('O'.$NewRow, 'Kurs');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
        $sheet->getColumnDimension('O')->setWidth(16);


		$where_bln = "";
        if($bulan > 0){
            $where_bln = "AND MONTH(date) = '".$bulan."' ";
        }

        $where_thn = "";
        if($tahun > 0){
            $where_thn = "AND YEAR(date) = '".$tahun."' ";
        }
		
		$qSupplier	    = "	SELECT * FROM laporan_wip_per_bulan WHERE 1=1 ".$where_bln." ".$where_thn." ORDER BY `date` ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $row_Cek['date'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= $row_Cek['est_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$est_harga	= $row_Cek['est_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$real_material	= $row_Cek['real_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;			
				$real_harga	= ($row_Cek['real_harga_rp']/$row_Cek['kurs']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
				$get_revenue = $this->db
							->select('SUM((( d.price_total / e.qty ) * (a.qty_akhir - a.qty_awal + 1))) AS revenue, AVG(a.kurs) as kurs_det')
							->from('laporan_wip_per_bulan z')
							->join('laporan_wip_per_hari a','z.date=a.date')
							->join('so_detail_header b','a.id_milik=b.id')
							->join('so_bf_detail_header c','b.id_milik=c.id')
							->join('cost_project_detail d','c.id_milik=d.caregory_sub')
							->join('bq_detail_header e','c.id_milik=e.id')
							->where('z.date', $row_Cek['date'])
							->get()
							->result();
				$revenue 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;
				$kurs_det 	= (!empty($get_revenue))?$get_revenue[0]->kurs_det:0;

                $awal_col++;
				$direct_labour	= $revenue;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
				$awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
                $awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$kurs	= $row_Cek['kurs'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kurs);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		
		
		$sheet->setTitle('Report Produksi Per Day');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Report Produksi WIP Per Day '.$bulan.' '.$tahun.' '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function modalDetail_WIP(){
		$this->load->view('Cron/report_produksi_detail_wip');
    }

	public function getDataJSONDetail_WIP(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail_WIP(
            $requestData['tanggal'],
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
            // $view	= "<button type='button' class='btn btn-sm btn-warning' id='detail' title='Look Data' data-tanggal='".$row['date']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            // $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-primary'  title='Print Sales Order' ><i class='fa fa-file-excel-o'></i></a>";
			
			$qty = $row['qty_akhir'] - $row['qty_awal'] + 1;

			$NO_IPP = str_replace('PRO-','',$row['id_produksi']);
			$tandaIPP = substr($NO_IPP,0,4);
			
			$no_so = $row['no_so'];
			$no_spk = $row['no_spk'];
			$revenue = 0;
			$estimasi_material 	= $row['est_material'];
			$estimasi_price 	= $row['est_harga'];
			$real_material 		= $row['real_material'];
			if($tandaIPP != 'IPPT'){
				$no_so = get_detail_ipp()[$NO_IPP]['so_number'];
				$no_spk = $row['no_spk2'];

				$get_revenue = $this->db
								->select('(d.price_total / e.qty) AS revenue')
								->from('laporan_wip_per_hari a')
								->join('so_detail_header b','a.id_milik=b.id')
								->join('so_bf_detail_header c','b.id_milik=c.id')
								->join('cost_project_detail d','c.id_milik=d.caregory_sub')
								->join('bq_detail_header e','c.id_milik=e.id')
								->where('a.id_milik', $row['id_milik'])
								->limit(1)
								->get()
								->result();
				$revenue2 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;
				$revenue 	= $revenue2 * $qty;
				$GET_EST_ACT = getEstimasiVsAktual($row['id_milik'], $NO_IPP, $qty, $row['id_production_detail']);

				$estimasi_material 	= (!empty($GET_EST_ACT['est_mat']))?$GET_EST_ACT['est_mat']:0;
				$estimasi_price 	= (!empty($GET_EST_ACT['act_mat']))?$GET_EST_ACT['act_mat']:0;
				$real_material 		= (!empty($GET_EST_ACT['est_price']))?$GET_EST_ACT['est_price']:0;
			}

			$NO_IPP = str_replace('PRO-','',$row['id_produksi']);
            $nestedData 	= array();
            $nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['id']."</div>";
            $nestedData[]	= "<div align='left'>".$NO_IPP."</div>";
            $nestedData[]	= "<div align='left'>".$no_so."</div>";
            $nestedData[]	= "<div align='left'>".$row['id_category']."</div>";
            $nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
            $nestedData[]	= "<div align='right'>".$no_spk."</div>";
            $nestedData[]	= "<div align='right'>".$row['diameter']."</div>"; 
            $nestedData[]	= "<div align='right'>".$row['diameter2']."</div>";
            $nestedData[]	= "<div align='center'>".$row['pressure']."</div>";
            $nestedData[]	= "<div align='center'>".$row['liner']."</div>";
            $nestedData[]	= "<div align='right'>".$qty."</div>";
            $nestedData[]	= "<div align='right'>".number_format($revenue,2)."</div>";

			$GET_EST_ACT = getEstimasiVsAktual($row['id_milik'], $NO_IPP, $qty, $row['id_production_detail']);

			$estimasi_material 	= (!empty($GET_EST_ACT['est_mat']))?$GET_EST_ACT['est_mat']:0;
			$estimasi_price 	= (!empty($GET_EST_ACT['act_mat']))?$GET_EST_ACT['act_mat']:0;
			$real_material 		= (!empty($GET_EST_ACT['est_price']))?$GET_EST_ACT['est_price']:0;
			// $real_material 		= $row['real_material'];

            $nestedData[]	= "<div align='right'>".number_format($estimasi_material,2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($estimasi_price,2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($real_material,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['real_harga'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format(($row['real_harga_rp']/$row['kurs']),2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
            $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
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

	public function queryDataJSONDetail_WIP($tanggal, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
			SELECT
				a.*,
				b.no_spk as no_spk2
			FROM
				laporan_wip_per_hari a
				LEFT JOIN so_detail_header b ON a.id_milik=b.id
		    WHERE a.date='".$tanggal."' AND (
				a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'date'
			
		);

		$sql .= " ORDER BY a.id_produksi ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }

	public function excel_detail_all(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		$objPHPExcel	= new PHPExcel();
		
		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(2243);
		$sheet->setCellValue('A'.$Row, 'LAPORAN PRODUKSI');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		$NextRow1= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'Date'); 
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow1);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'IPP');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow1);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Product');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow1);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'ID Product');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow1);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'Dim x Dim 2');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow1);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'Pressure');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow1);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'Liner');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow1);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'Est Material (kg)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow1);
        $sheet->getColumnDimension('H')->setWidth(16);
        
        $sheet->setCellValue('I'.$NewRow, 'Est Price ($)');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow1);
        $sheet->getColumnDimension('I')->setWidth(16);
        
        $sheet->setCellValue('J'.$NewRow, 'Aktual Material (kg)');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow1);
        $sheet->getColumnDimension('J')->setWidth(16);
        
        $sheet->setCellValue('K'.$NewRow, 'Aktual Price ($)');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow1);
        $sheet->getColumnDimension('K')->setWidth(16);
        
        $sheet->setCellValue('L'.$NewRow, 'Qty');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow1);
        $sheet->getColumnDimension('L')->setWidth(16);
        
        $sheet->setCellValue('M'.$NewRow, 'Revenue');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow1);
        $sheet->getColumnDimension('M')->setWidth(16);
        
        $sheet->setCellValue('N'.$NewRow, 'Direct Labour');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow1);
        $sheet->getColumnDimension('N')->setWidth(16);
        
        $sheet->setCellValue('O'.$NewRow, 'Indirect Labour');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow1);
        $sheet->getColumnDimension('O')->setWidth(16);
        
        $sheet->setCellValue('P'.$NewRow, 'Consumable');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow1);
        $sheet->getColumnDimension('P')->setWidth(16);

        $sheet->setCellValue('Q'.$NewRow, 'FOH');
		$sheet->getStyle('Q'.$NewRow.':U'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':U'.$NextRow);
        $sheet->getColumnDimension('Q')->setWidth(16);

		$sheet->setCellValue('V'.$NewRow, 'Sales & Marketing');
		$sheet->getStyle('V'.$NewRow.':V'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('V'.$NewRow.':V'.$NextRow1);
		$sheet->getColumnDimension('V')->setWidth(16);

		$sheet->setCellValue('W'.$NewRow, 'Umum & Admin');
		$sheet->getStyle('W'.$NewRow.':W'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('W'.$NewRow.':W'.$NextRow1);
		$sheet->getColumnDimension('W')->setWidth(16);

		$sheet->setCellValue('X'.$NewRow, 'No SPK');
		$sheet->getStyle('X'.$NewRow.':X'.$NextRow1)->applyFromArray($tableHeader);
		$sheet->mergeCells('X'.$NewRow.':X'.$NextRow1);
		$sheet->getColumnDimension('X')->setWidth(16);

		$NewRow	= $NewRow +1;
		$NextRow= $NewRow;
        
        $sheet->setCellValue('Q'.$NewRow, 'Machine Cost');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
        $sheet->getColumnDimension('Q')->setWidth(16);
        
        $sheet->setCellValue('R'.$NewRow, 'Mold mandril Cost');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
        $sheet->getColumnDimension('R')->setWidth(16);
        
        $sheet->setCellValue('S'.$NewRow, 'Depreciation FOH');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
        $sheet->getColumnDimension('S')->setWidth(16);
        
        $sheet->setCellValue('T'.$NewRow, 'Factory Overhead');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
        $sheet->getColumnDimension('T')->setWidth(16);
        
        $sheet->setCellValue('U'.$NewRow, 'Salary Factory Management');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setWidth(16);


		$qSupplier	    = "	SELECT * FROM laporan_per_hari WHERE `date` >= '2024-01-01' ORDER BY `date` ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				
				$awal_col++;
				$date	= $row_Cek['date'];
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $date);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_produksi	= $row_Cek['id_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_category	= $row_Cek['id_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$id_product	= $row_Cek['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
				$diameter2	= $row_Cek['diameter'].' X '.$row_Cek['diameter2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$pressure	= $row_Cek['pressure'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $pressure);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$awal_col++;
				$liner	= $row_Cek['liner'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $liner);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
				
				$NO_IPP = str_replace('PRO-','',$row_Cek['id_produksi']);
				$tandaIPP = substr($NO_IPP,0,4);

				$qty = $row_Cek['qty_akhir'] - $row_Cek['qty_awal'] + 1;

				$estimasi_material 	= $row_Cek['est_material'];
				$estimasi_price 	= $row_Cek['est_harga'];
				$real_material 		= $row_Cek['real_material'];
				$no_spk 			= $row_Cek['no_spk'];
				$revenue 			= $row_Cek['est_harga'];

				if($tandaIPP != 'IPPT'){
					$get_revenue = $this->db
								->select('(d.price_total / e.qty) AS revenue, b.no_spk')
								->from('laporan_per_hari a')
								->join('so_detail_header b','a.id_milik=b.id')
								->join('so_bf_detail_header c','b.id_milik=c.id')
								->join('cost_project_detail d','c.id_milik=d.caregory_sub')
								->join('bq_detail_header e','c.id_milik=e.id')
								->where('a.id_milik', $row_Cek['id_milik'])
								->limit(1)
								->get()
								->result();
					$revenue2 	= (!empty($get_revenue))?$get_revenue[0]->revenue:0;
					$no_spk 	= (!empty($get_revenue[0]->no_spk))?$get_revenue[0]->no_spk:'';
					$revenue 	= $revenue2 * $qty;
					
					$GET_EST_ACT = getEstimasiVsAktual($row_Cek['id_milik'], $NO_IPP, $qty, $row_Cek['id_production_detail']);

					$estimasi_material 	= (!empty($GET_EST_ACT['est_mat']))?$GET_EST_ACT['est_mat']:0;
					$estimasi_price 	= (!empty($GET_EST_ACT['act_mat']))?$GET_EST_ACT['act_mat']:0;
					$real_material 		= (!empty($GET_EST_ACT['est_price']))?$GET_EST_ACT['est_price']:0;
					// $real_material 		= $row_Cek['real_material'];
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estimasi_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $estimasi_price);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$real_harga	= ($row_Cek['real_harga_rp']/$row_Cek['kurs']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$qty	= $qty;
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$revenue	= $revenue;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $revenue);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                

				$awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                
                $awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

                $awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                

                $awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);
                
                $awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyRight);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no_spk);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
			}
		}
		
		
		$sheet->setTitle('Report');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5          
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya 
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="all-report-produksi-detail.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

}