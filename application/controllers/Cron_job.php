<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_job extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('cron_model');
		$this->load->database();

        $this->db2 = $this->load->database('tanki', TRUE);
    }

    public function cron_product_jadi(){
		$ArrType = ['pipe','cutting','fitting'];
        $DateTime = date('Y-m-d H:i:s');
        $DETAIL_IPP = get_detail_ipp();

        $ArrInsert = [];
        foreach ($ArrType as $value) {
            $status = $value;
            //SQL
            if($status == 'pipe'){
                $where = " AND a.sts_cutting != 'Y' AND c.id_category='pipe' AND a.kode_spk != 'deadstok' ";
                $LEFT_JOIN = ",";
                $FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
                $LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";
    
            }
            if($status == 'cutting'){
                $where = " AND d.id IS NOT NULL AND c.id_category='pipe' AND d.app = 'Y' AND (f.lock_delivery_date IS NULL AND f.spool_induk IS NULL) AND a.sts_cutting = 'Y' AND a.kode_spk != 'deadstok' ";
                $LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header,";
                $FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,";
                $LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke,'.' ,f.cutting_ke)";
    
            }
            if($status == 'fitting'){
                $where = " AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' AND a.kode_spk != 'deadstok' ";
                $LEFT_JOIN = ",";
                $FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
                $LIKE_PRODUCT_CODE = "CONCAT(SUBSTRING_INDEX(a.product_code, '.', 1),'.', a.product_ke)";
    
            }
    
            $SQL = "
                SELECT
                    (@row:=@row+1) AS nomor,
                    '".$status."' AS category,
                    a.id_category AS product,
                    a.id_produksi AS no_ipp,
                    a.product_code AS no_so,
                    a.no_spk AS no_spk,
                    a.id_milik AS id_milik,
                    a.amount AS nilai_value,
                    a.qty AS qty_order,
                    ".$FIELD_CUTTING."
                    e.qty AS qty_group_spk
                FROM
                    production_detail a
                    LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
                    LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
                    LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
                    LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke".$LEFT_JOIN."
                    (SELECT @row:=0) r
                WHERE 1=1 ".$where."
                    AND a.kode_delivery IS NULL
                    AND a.kode_spool IS NULL
                    AND a.closing_produksi_date IS NOT NULL
                    AND a.release_to_costing_date IS NOT NULL
                    AND a.fg_date IS NOT NULL
                    AND a.lock_delivery_date IS NULL
            ";

            $RESULT = $this->db->query($SQL)->result_array();
            $nomor = 0;
            if(!empty($RESULT)){
                foreach ($RESULT as $key => $value) { $nomor++;
                    $NO_IPP     = str_replace('PRO-','',$value['no_ipp']);
                    $EXPLODE    = explode('-',$value['no_so']);
                    $UNIQ       = $status.'-'.$nomor;

                    $ArrInsert[$UNIQ]['category']      = $value['category'];
                    $ArrInsert[$UNIQ]['no_ipp']        = $NO_IPP;
                    $ArrInsert[$UNIQ]['no_so']         = $EXPLODE[0];
                    $ArrInsert[$UNIQ]['no_spk']        = $value['no_spk'];
                    $ArrInsert[$UNIQ]['id_milik']      = $value['id_milik'];
                    $ArrInsert[$UNIQ]['product']       = $value['product'];
                    $ArrInsert[$UNIQ]['spec']          = spec_bq2($value['id_milik']);
                    $ArrInsert[$UNIQ]['id_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['id_customer']);
                    $ArrInsert[$UNIQ]['nm_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['nm_customer']);
                    $ArrInsert[$UNIQ]['nm_project']    = strtoupper($DETAIL_IPP[$NO_IPP]['nm_project']);
                    $ArrInsert[$UNIQ]['qty_order']     = $value['qty_order'];
                    $ArrInsert[$UNIQ]['qty_group_spk'] = $value['qty_group_spk'];
                    $ArrInsert[$UNIQ]['nilai_value']   = $value['nilai_value'];
                    $ArrInsert[$UNIQ]['hist_date']     = $DateTime;
                }
            }


            // echo $SQL."<br>";
        }
        // echo "<pre>";
        // print_r($ArrInsert);

        if(!empty($ArrInsert)){
            $this->db->insert_batch('stock_barang_jadi_per_day',$ArrInsert);
        }

        //CRON JOB DEADSTOK
        $SQL_DEADSTOK = "SELECT
                            a.*
                        FROM
                            deadstok a
                        WHERE  
                            a.deleted_date IS NULL 
                            AND a.kode_delivery IS NULL 
                            AND a.id_booking IS NOT NULL 
                            AND a.process_next = 1
                            AND a.qc_date IS NOT NULL
                        ";
        $resultDeadstok = $this->db->query($SQL_DEADSTOK)->result_array();
        $ArrInsertDeadstok = [];
        foreach ($resultDeadstok as $key => $value) {
            $NO_IPP     = $value['no_ipp'];

            $length     = ($value['length'] > 0)?' x '.$value['length']:'';
            $type_std   = (!empty($value['type_std']))?$value['type_std'].', ':'';
            $resin      = (!empty($value['resin']))?$value['resin'].', ':'';

            $ArrInsertDeadstok[$key]['category']      = 'deadstok';
            $ArrInsertDeadstok[$key]['no_ipp']        = $NO_IPP;
            $ArrInsertDeadstok[$key]['no_so']         = $value['no_so'];
            $ArrInsertDeadstok[$key]['no_spk']        = $value['no_spk'];
            $ArrInsertDeadstok[$key]['id_milik']      = $value['id_milik'];
            $ArrInsertDeadstok[$key]['product']       = $value['product_name'];
            $ArrInsertDeadstok[$key]['spec']          = $type_std.$resin.$value['product_spec'].$length;
            $ArrInsertDeadstok[$key]['id_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['id_customer']);
            $ArrInsertDeadstok[$key]['nm_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['nm_customer']);
            $ArrInsertDeadstok[$key]['nm_project']    = strtoupper($DETAIL_IPP[$NO_IPP]['nm_project']);
            $ArrInsertDeadstok[$key]['qty_order']     = $value['id_product'];
            $ArrInsertDeadstok[$key]['qty_group_spk'] = NULL;
            $ArrInsertDeadstok[$key]['nilai_value']   = NULL;
            $ArrInsertDeadstok[$key]['hist_date']     = $DateTime;
        }

        if(!empty($ArrInsertDeadstok)){
            $this->db->insert_batch('stock_barang_jadi_per_day',$ArrInsertDeadstok);
        }

		//CRON JOB GUDANG PRODUKSI
        $SQL_GD_PRODUKSI = "SELECT
								a.*
							FROM
							spool_group_all a
							WHERE  
								a.spool_induk IS NOT NULL
								AND a.release_spool_date IS NULL
							";
        $resultGdProduksi = $this->db->query($SQL_GD_PRODUKSI)->result_array();
        $ArrInsertGdProduksi = [];
        foreach ($resultGdProduksi as $key => $value) {
            $NO_IPP     = str_replace('PRO-','',$value['id_produksi']);

            $ArrInsertGdProduksi[$key]['category']      = 'produksi';
            $ArrInsertGdProduksi[$key]['no_ipp']        = $NO_IPP;
            $ArrInsertGdProduksi[$key]['no_so']         = (!empty($DETAIL_IPP[$NO_IPP]['so_number']))?$DETAIL_IPP[$NO_IPP]['so_number']:null;
            $ArrInsertGdProduksi[$key]['no_spk']        = $value['no_spk'];
            $ArrInsertGdProduksi[$key]['id_milik']      = $value['id_milik'];
            $ArrInsertGdProduksi[$key]['product']       = $value['id_category'];
            $ArrInsertGdProduksi[$key]['spec']          = spec_bq2($value['id_milik']);
            $ArrInsertGdProduksi[$key]['id_customer']   = (!empty($DETAIL_IPP[$NO_IPP]['id_customer']))?$DETAIL_IPP[$NO_IPP]['id_customer']:null;
            $ArrInsertGdProduksi[$key]['nm_customer']   = (!empty($DETAIL_IPP[$NO_IPP]['nm_customer']))?$DETAIL_IPP[$NO_IPP]['nm_customer']:null;
            $ArrInsertGdProduksi[$key]['nm_project']    = (!empty($DETAIL_IPP[$NO_IPP]['nm_project']))?$DETAIL_IPP[$NO_IPP]['nm_project']:null;
            $ArrInsertGdProduksi[$key]['qty_order']     = NULL;
            $ArrInsertGdProduksi[$key]['qty_group_spk'] = $value['product_ke'];
            $ArrInsertGdProduksi[$key]['sts']			= $value['sts'];
            $ArrInsertGdProduksi[$key]['spool_induk']	= $value['spool_induk'];
            $ArrInsertGdProduksi[$key]['nilai_value']   = NULL;
            $ArrInsertGdProduksi[$key]['hist_date']     = $DateTime;
			$ArrInsertGdProduksi[$key]['nilai_wip']		= $value['nilai_wip'];
			$ArrInsertGdProduksi[$key]['nilai_fg']		= $value['nilai_fg'];
			$ArrInsertGdProduksi[$key]['spool_in']		= $value['spool_date'];
            $ArrInsertGdProduksi[$key]['spool_out']		= $value['lock_spool_date'];
        }

        if(!empty($ArrInsertGdProduksi)){
            $this->db->insert_batch('stock_barang_jadi_per_day',$ArrInsertGdProduksi);
        }

		//CRON JOB DEADSTOK VALUE
        $SQL_DEADSTOK_VALUE = "SELECT
                            a.*,
							COUNT(a.qty) AS qty_stock
                        FROM
                            deadstok a
                        WHERE  
							a.deleted_date IS NULL 
							AND a.kode_delivery IS NULL 
							AND a.id_booking IS NULL
						GROUP BY a.id_product
                        ";
        $resultDeadstokValue = $this->db->query($SQL_DEADSTOK_VALUE)->result_array();
        $ArrInsertDeadstokValue = [];
        foreach ($resultDeadstokValue as $key => $value) {
            $ArrInsertDeadstokValue[$key]['id_product']     = $value['id_product'];
            $ArrInsertDeadstokValue[$key]['no_barang']      = $value['no_barang'];
            $ArrInsertDeadstokValue[$key]['type']      		= $value['type'];
            $ArrInsertDeadstokValue[$key]['product_name']   = $value['product_name'];
            $ArrInsertDeadstokValue[$key]['type_std']     	= $value['type_std'];
            $ArrInsertDeadstokValue[$key]['product_spec']   = $value['product_spec'];
            $ArrInsertDeadstokValue[$key]['resin']     		= $value['resin'];
            $ArrInsertDeadstokValue[$key]['length']     	= $value['length'];
            $ArrInsertDeadstokValue[$key]['qty']     		= $value['qty_stock'];
            $ArrInsertDeadstokValue[$key]['price_book']     = $value['price_book'];
            $ArrInsertDeadstokValue[$key]['wip_deadstok']   = $value['wip_deadstok'];
            $ArrInsertDeadstokValue[$key]['fg_deadstok']    = $value['fg_deadstok'];
            $ArrInsertDeadstokValue[$key]['hist_date']     	= $DateTime;
        }

        if(!empty($ArrInsertDeadstokValue)){
            $this->db->insert_batch('deadstok_per_day',$ArrInsertDeadstokValue);
        }
        
	}

    public function cron_product_wip(){
		$ArrType = ['pipe','cutting','fitting'];
		// $ArrType = ['fitting'];
        $DateTime = date('Y-m-d H:i:s');
        $DETAIL_IPP = get_detail_ipp();

        $ArrInsert = [];
        foreach ($ArrType as $value) {
            $status = $value;
            //SQL
            if($status == 'pipe'){
                $where = " AND a.sts_cutting != 'Y' AND c.id_category='pipe' ";
                $LEFT_JOIN = ",";
                $FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
            }
            if($status == 'cutting'){
                $where = " AND d.id IS NOT NULL AND c.id_category='pipe' AND d.app = 'Y' AND a.sts_cutting = 'Y' ";
                $LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header,";
                $FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,";
            }
            if($status == 'fitting'){
                $where = " AND a.sts_cutting != 'Y' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' ";
                $LEFT_JOIN = ",";
                $FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
            }

			if($status == 'tanki'){
				$where = " AND a.product_code_cut = 'tanki' ";
				$LEFT_JOIN = ",";
				$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,";
			}
    
            $SQL = "
                SELECT
                    (@row:=@row+1) AS nomor,
                    '".$status."' AS category,
                    a.id_category AS product,
                    a.id_produksi AS no_ipp,
                    a.product_code AS no_so,
                    a.no_spk AS no_spk,
                    a.id_milik AS id_milik,
                    a.amount AS nilai_value,
                    a.qty AS qty_order,
                    ".$FIELD_CUTTING."
                    e.qty AS qty_group_spk
                FROM
					production_detail a
					LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
					LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
					LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke".$LEFT_JOIN."
                    (SELECT @row:=0) r
                WHERE 1=1 ".$where."
					AND a.upload_real = 'Y'
					AND a.upload_real2 = 'Y'
					AND a.kode_spk IS NOT NULL 
					AND a.kode_spk != 'deadstok'
					AND a.kode_delivery IS NULL
					AND a.kode_spool IS NULL
					AND a.closing_produksi_date IS NOT NULL
					AND a.release_to_costing_date IS NULL
					AND a.fg_date IS NULL
					AND a.id_category NOT IN ".DirectFinishGood()."
            ";

            // echo $SQL; exit;

            $RESULT = $this->db->query($SQL)->result_array();
            $nomor = 0;
            if(!empty($RESULT)){
                foreach ($RESULT as $key => $value) { $nomor++;
                    $NO_IPP     = str_replace('PRO-','',$value['no_ipp']);
                    $EXPLODE    = explode('-',$value['no_so']);
                    $UNIQ       = $status.'-'.$nomor;

					// $customer 	= (!empty($DETAIL_IPP[$NO_IPP]['nm_customer']))?$DETAIL_IPP[$NO_IPP]['nm_customer']:'';
					// $project 	= (!empty($DETAIL_IPP[$NO_IPP]['nm_project']))?$DETAIL_IPP[$NO_IPP]['nm_project']:'';
					// $NOMOR_SO 	= (!empty($DETAIL_IPP[$NO_IPP]['so_number']))?$DETAIL_IPP[$NO_IPP]['so_number']:'';
					// $product 	= $value['id_category'];
					// $spec 		= spec_bq2($value['id_milik']);

					// if($FLAG == 'tanki'){
					// 	$customer 	= $this->tanki_model->get_ipp_detail($NO_IPP)['customer'];
					// 	$project 	= $this->tanki_model->get_ipp_detail($NO_IPP)['nm_project'];
					// 	$NOMOR_SO 	= $this->tanki_model->get_ipp_detail($NO_IPP)['no_so'];
					// 	$product 	= $value['id_product'];
					// 	$spec 		= $this->tanki_model->get_spec($value['id_milik']);
					// }

                    $ArrInsert[$UNIQ]['category']      = $value['category'];
                    $ArrInsert[$UNIQ]['no_ipp']        = $NO_IPP;
                    $ArrInsert[$UNIQ]['no_so']         = $EXPLODE[0];
                    $ArrInsert[$UNIQ]['no_spk']        = $value['no_spk'];
                    $ArrInsert[$UNIQ]['id_milik']      = $value['id_milik'];
                    $ArrInsert[$UNIQ]['product']       = $value['product'];
                    $ArrInsert[$UNIQ]['spec']          = spec_bq2($value['id_milik']);
                    $ArrInsert[$UNIQ]['id_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['id_customer']);
                    $ArrInsert[$UNIQ]['nm_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['nm_customer']);
                    $ArrInsert[$UNIQ]['nm_project']    = strtoupper($DETAIL_IPP[$NO_IPP]['nm_project']);
                    $ArrInsert[$UNIQ]['qty_order']     = $value['qty_order'];
                    $ArrInsert[$UNIQ]['qty_group_spk'] = $value['qty_group_spk'];
                    $ArrInsert[$UNIQ]['nilai_value']   = $value['nilai_value'];
                    $ArrInsert[$UNIQ]['hist_date']     = $DateTime;
                }
            }


            // echo $SQL."<br>";
        }
        // echo "<pre>";
        // print_r($ArrInsert);

        if(!empty($ArrInsert)){
            $this->db->insert_batch('stock_barang_wip_per_day',$ArrInsert);
        }

		//CRON JOB GUDANG PRODUKSI
        $SQL_GD_PRODUKSI = "SELECT
								a.*
							FROM
							spool_group_all a
							WHERE  
								a.lock_spool_date IS NOT NULL
								AND a.release_spool_date IS NULL
							";
        $resultGdProduksi = $this->db->query($SQL_GD_PRODUKSI)->result_array();
        $ArrInsertGdProduksi = [];
        foreach ($resultGdProduksi as $key => $value) {
            $NO_IPP     = str_replace('PRO-','',$value['id_produksi']);

            $ArrInsertGdProduksi[$key]['category']      = 'spool';
            $ArrInsertGdProduksi[$key]['no_ipp']        = $NO_IPP;
            $ArrInsertGdProduksi[$key]['no_so']         = (!empty($DETAIL_IPP[$NO_IPP]['so_number']))?$DETAIL_IPP[$NO_IPP]['so_number']:null;
            $ArrInsertGdProduksi[$key]['no_spk']        = $value['no_spk'];
            $ArrInsertGdProduksi[$key]['id_milik']      = $value['id_milik'];
            $ArrInsertGdProduksi[$key]['product']       = $value['id_category'];
            $ArrInsertGdProduksi[$key]['spec']          = spec_bq2($value['id_milik']);
            $ArrInsertGdProduksi[$key]['id_customer']   = (!empty($DETAIL_IPP[$NO_IPP]['id_customer']))?$DETAIL_IPP[$NO_IPP]['id_customer']:null;
            $ArrInsertGdProduksi[$key]['nm_customer']   = (!empty($DETAIL_IPP[$NO_IPP]['nm_customer']))?$DETAIL_IPP[$NO_IPP]['nm_customer']:null;
            $ArrInsertGdProduksi[$key]['nm_project']    = (!empty($DETAIL_IPP[$NO_IPP]['nm_project']))?$DETAIL_IPP[$NO_IPP]['nm_project']:null;
            $ArrInsertGdProduksi[$key]['qty_order']     = NULL;
            $ArrInsertGdProduksi[$key]['qty_group_spk'] = $value['product_ke'];
            $ArrInsertGdProduksi[$key]['sts']			= $value['sts'];
            $ArrInsertGdProduksi[$key]['spool_induk']	= $value['spool_induk'];
            $ArrInsertGdProduksi[$key]['kode_spool']	= $value['kode_spool'];
            $ArrInsertGdProduksi[$key]['no_drawing']	= $value['no_drawing'];
            $ArrInsertGdProduksi[$key]['nilai_value']   = NULL;
            $ArrInsertGdProduksi[$key]['hist_date']     = $DateTime;
        }

        if(!empty($ArrInsertGdProduksi)){
            $this->db->insert_batch('stock_barang_wip_per_day',$ArrInsertGdProduksi);
        }
        
	}
    
    public function depresiasi_assets(){
 		$DATE_NOW	= date('Y-m-d');
        $TANGGAL    = date('Y-m-d', strtotime('-1 days', strtotime($DATE_NOW)));
		$TAHUN		= date('Y',strtotime($TANGGAL));
		$BULAN		= date('m',strtotime($TANGGAL));
        $username   = 'system';
	    $datetime   = date('Y-m-d H:i:s');		
        //INSERT JURNAL TEMP
        $SQL = "SELECT
                    a.category AS category,
                    a.nm_asset AS nm_asset,
					a.nm_category AS nm_category,
                    sum( a.nilai_susut ) AS sisa_nilai,
                    a.kdcab AS kdcab,
					b.id_coa,
					c.coa,
					c.coa_kredit
                FROM
                    asset_generate a
					LEFT JOIN asset b ON a.kd_asset=b.kd_asset
					LEFT JOIN asset_coa c ON b.id_coa=c.id
                WHERE
                    (a.bulan = '$BULAN' AND a.tahun = '$TAHUN' and a.flag='N') AND b.deleted_date IS NULL
                GROUP BY 
                    a.category, 
                    a.kdcab,
					b.id_coa,
					c.coa,
					c.coa_kredit";
        // echo $SQL;

        $ArrJurnal = $this->db->query($SQL)->result_array();
        
        $ArrDebit = array();
        $ArrKredit = array();
        $ArrJavh = array();
        $Loop = 0;
		if(!empty($ArrJurnal)){
			foreach($ArrJurnal AS $val => $valx){
				$Loop++;
				$ArrDebit[$Loop]['id_category'] 	= $valx['id_coa'];
				$ArrDebit[$Loop]['category'] 		= $valx['category'];
				$ArrDebit[$Loop]['tipe'] 			= "JV";
				$ArrDebit[$Loop]['nomor'] 			= $Loop;
				$ArrDebit[$Loop]['tanggal'] 		= $TANGGAL;
				$ArrDebit[$Loop]['no_perkiraan'] 	= $valx['coa'];
				$ArrDebit[$Loop]['keterangan'] 		= $valx['nm_category'];
				$ArrDebit[$Loop]['kdcab'] 			= $valx['kdcab'];
				$ArrDebit[$Loop]['debet'] 			= $valx['sisa_nilai'];
				$ArrDebit[$Loop]['kredit'] 			= 0;
				$ArrDebit[$Loop]['created_by'] 		= $username;
				$ArrDebit[$Loop]['created_date'] 	= $datetime;

				$ArrKredit[$Loop]['id_category'] 	= $valx['id_coa'];
				$ArrKredit[$Loop]['category'] 		= $valx['category'];
				$ArrKredit[$Loop]['tipe'] 			= "JV";
				$ArrKredit[$Loop]['nomor'] 			= $Loop;
				$ArrKredit[$Loop]['tanggal'] 		= $TANGGAL;
				$ArrKredit[$Loop]['no_perkiraan'] 	= $valx['coa_kredit'];
				$ArrKredit[$Loop]['keterangan'] 	= $valx['nm_category'];
				$ArrKredit[$Loop]['kdcab'] 			= $valx['kdcab'];
				$ArrKredit[$Loop]['debet'] 			= 0;
				$ArrKredit[$Loop]['kredit'] 		= $valx['sisa_nilai'];
				$ArrKredit[$Loop]['created_by'] 	= $username;
				$ArrKredit[$Loop]['created_date'] 	= $datetime;
			}
			$this->db->trans_start();
				$this->db->delete('asset_jurnal_temp',array('created_by'=>$username));
				$this->db->insert_batch('asset_jurnal_temp', $ArrDebit);
				$this->db->insert_batch('asset_jurnal_temp', $ArrKredit);

				$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
				$this->saved_jurnal_erp();
				$this->saved_jurnal_depresiasi();
				$this->db->query("UPDATE asset_generate SET flag='Y' WHERE bulan='".$BULAN."' AND tahun='".$TAHUN."' ");
		
			}
			echo "Update";
		}else{
			echo "Empty";
		}
	}
   

    public function saved_jurnal_erp(){
		$username = 'system';
		$datetime = date('Y-m-d H:i:s');

		$get_jurnal = $this->db->get_where('asset_jurnal_temp',array('created_by'=>$username,'debet'=>0))->result_array();
		$ArrJurnal = [];
		foreach ($get_jurnal as $key => $value) {
			$ArrJurnal[$key]['category'] 		= 'assets';
			$ArrJurnal[$key]['tanggal'] 		= $value['tanggal'];
			$ArrJurnal[$key]['id_detail'] 		= $value['id_category'];
			$ArrJurnal[$key]['product'] 		= $value['category'];
			$ArrJurnal[$key]['id_material'] 	= $value['no_perkiraan'];
			$ArrJurnal[$key]['nm_material'] 	= $value['keterangan'];
			$ArrJurnal[$key]['total_nilai'] 	= $value['kredit'];
			$ArrJurnal[$key]['created_by'] 		= $username;
			$ArrJurnal[$key]['created_date'] 	= $datetime;
		}

		$this->db->trans_start();
			$this->db->insert_batch('jurnal',$ArrJurnal);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}
	
	
	 public function saved_jurnal_depresiasi(){
		$username = 'system';
		$datetime = date('Y-m-d H:i:s');
		
		$this->db->trans_start();

		
		$bulan=date("m");
		$tahun=date("Y");
		
		$DATE_NOW	= date('Y-m-d');
		$date    = date('Y-m-d', strtotime('-1 days', strtotime($DATE_NOW)));

		$sqlHeader	= "select * from asset_jurnal_temp WHERE tanggal='".$date."'";
		$Q_Awal	= $this->db->query($sqlHeader)->result();

		//echo $sqlHeader."<hr>";

				
			$det_Jurnaltes1=array();
			$jenis_jurnal = 'DEPRESIASI';
			$nomor_jurnal = $jenis_jurnal . $tahun.$bulan . rand(100, 999);
			$payment_date=$date;
			foreach($Q_Awal AS $val => $valx){
				

				$sqlinsert="insert into jurnaltras (nomor, tanggal, tipe, no_perkiraan, keterangan, no_request, debet, kredit, jenis_jurnal)
				VALUE 
				('".$nomor_jurnal."','".$payment_date."','JV','".$valx->no_perkiraan."','".$valx->keterangan."','-','".$valx->debet."','".$valx->kredit."','".$valx->tipe."')";
				$this->db->query($sqlinsert);

		//		echo $sqlinsert.'<hr>';

		   }

			$nocab	= 'A';
			$Cabang	= '101';
			$bulan_Proses	= date('Y',strtotime($payment_date));
			$Urut			= 1;
			$Pros_Cab		= $this->db->query("SELECT subcab,nomorJC FROM ".DBACC.".pastibisa_tb_cabang WHERE nocab='".$Cabang."' limit 1");
			$det_Cab		= $Pros_Cab->row();
			
			
			if($det_Cab){
				$nocab		= $det_Cab ->subcab;
				$Urut		= intval($det_Cab ->nomorJC) + 1;
			}
			$Format			= $Cabang.'-'.$nocab.'JV'.date('y',strtotime($payment_date));
			$Nomor_JV		= $Format.str_pad($Urut, 5, "0", STR_PAD_LEFT);
			$this->db->query("UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=(nomorJC + 1),lastupdate='".date("Y-m-d")."' WHERE nocab='".$Cabang."'");


			$Bln	= substr($payment_date,5,2);
			$Thn	= substr($payment_date,0,4);
			$Q_Detail = "select * from jurnaltras where jenis_jurnal='JV' and stspos='0' and nomor='".$nomor_jurnal."'";
			$DtJurnal = $this->db->query($Q_Detail)->result();
			
            $total = 0;
			foreach($DtJurnal AS $keys => $vals){
				$total += $vals->debet;
			
				$sqlinsert1="insert into ".DBACC.".jurnal (nomor, tipe, tanggal, no_reff, no_perkiraan, keterangan, debet, kredit )
				VALUE 
				('".$Nomor_JV."','JV','".$payment_date."','".$vals->no_request."','".$vals->no_perkiraan."','".$vals->keterangan."','".$vals->debet."','".$vals->kredit."')";
				$this->db->query($sqlinsert1);
			}

			$sqlinsert="insert into ".DBACC.".javh (nomor, tgl, jml, kdcab, jenis, keterangan, bulan, tahun, user_id, ho_valid )
			VALUE 
			('".$Nomor_JV."','".$payment_date."','".$total."','101','JV','Depresiasi ".$Bln." - ".$Thn."','".$Bln."','".$Thn."','system','')";
			$this->db->query($sqlinsert);

		
		
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
		
	
	 }

    public function balancing_accesories(){
		$getAksesorisPF = $this->db->get_where('accessories',array('deleted_date'=>NULL,'id_acc_tanki <>'=>NULL))->result_array();

        $ArrWHERE_IN = [];
        foreach ($getAksesorisPF as $key => $value) {
            $ArrWHERE_IN[] = $value['id_acc_tanki'];
        }

        $getAksesoris = $this->db2->get_where('accessories',array('deleted_date'=>NULL))->result_array();
        if(!empty($ArrWHERE_IN)){
            $getAksesoris = $this->db2->where_not_in('id',$ArrWHERE_IN)->get_where('accessories',array('deleted_date'=>NULL))->result_array();
        }
        // echo '<pre>';
        // print_r($getAksesoris);
        // exit;

        $ArrInsert = [];
        if(!empty($getAksesoris)){
            foreach ($getAksesoris as $key => $value) {
                $ArrInsert[$key]['id_acc_tanki'] = $value['id'];
                $ArrInsert[$key]['category'] = 5;
                $ArrInsert[$key]['nama'] = $value['nama'];
                $ArrInsert[$key]['ukuran_standart'] = $value['fungtion'];
                $ArrInsert[$key]['material'] = $value['material'];
                $ArrInsert[$key]['spesifikasi'] = $value['spec'];
                $ArrInsert[$key]['standart'] = $value['standart'];
                $ArrInsert[$key]['id_material'] = $value['kode'];
                // $ArrInsert[$key]['id_acc_tanki'] = $value['unit'];
                $ArrInsert[$key]['harga'] = $value['unit_price'];
                $ArrInsert[$key]['created_by'] = 'system';
                $ArrInsert[$key]['created_date'] = date('Y-m-d H:i:s');
            }
        }

        // echo '<pre>';
        // print_r($ArrInsert);
        // exit;

        $this->db->trans_start();
            if(!empty($ArrInsert)){
                $this->db->insert_batch('accessories',$ArrInsert);
            }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}   

    public function balancing_accesories_pricing(){
		$getAksesorisPF = $this->db->get_where('accessories',array('deleted_date'=>NULL,'id_acc_tanki <>'=>NULL))->result_array();

        $ArrInsert = [];
        if(!empty($getAksesorisPF)){
            foreach ($getAksesorisPF as $key => $value) {
                $ArrInsert[$key]['id'] = $value['id_acc_tanki'];
                $ArrInsert[$key]['unit_price'] = $value['harga'];
            }
        }

        $this->db->trans_start();
            if(!empty($ArrInsert)){
                $this->db->update_batch('accessories',$ArrInsert,'id');
            }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}

    public function cron_product_spool(){
		$username = 'system';
		$datetime = date('Y-m-d H:i:s');
        $DETAIL_IPP = get_detail_ipp();

		$get_jurnal = $this->db->query("SELECT
                                            id,
                                            id_pro,
                                            spool_induk AS spool_induk,
                                            REPLACE ( id_produksi, 'PRO-', '' ) AS no_ipp,
                                            kode_spool AS kode_spool,
                                            no_spk,
                                            id_milik,
                                            id_category AS product,
                                            NULL AS qty,
                                            no_drawing,
                                            sts,
                                            product_code,
                                            product_ke,
                                            `length` AS length,
                                            kode_spk
                                        FROM
                                            spool_group_release 
                                        WHERE
                                            kode_delivery IS NULL 
                                        ORDER BY
                                            spool_induk,
                                            kode_spool,
                                            id_milik")->result_array();
		$ArrJurnal = [];
		foreach ($get_jurnal as $key => $value) {
            $EXPLODE = explode('-',$value['kode_spool']);
            $NO_IPP = $value['no_ipp'];
			$ArrJurnal[$key]['id'] 	            = $value['id'];
			$ArrJurnal[$key]['id_pro'] 	        = $value['id_pro'];
			$ArrJurnal[$key]['spool_induk'] 	= $value['spool_induk'];
			$ArrJurnal[$key]['kode_spool'] 		= $value['kode_spool'];
			$ArrJurnal[$key]['no_ipp'] 		    = $value['no_ipp'];
			$ArrJurnal[$key]['no_so'] 		    = $EXPLODE[0];
			$ArrJurnal[$key]['no_spk'] 	        = $value['no_spk'];
			$ArrJurnal[$key]['id_milik'] 	    = $value['id_milik'];
			$ArrJurnal[$key]['product'] 	    = $value['product'];
            $ArrJurnal[$key]['spec']            = spec_bq2($value['id_milik']);
            $ArrJurnal[$key]['id_customer']     = strtoupper($DETAIL_IPP[$NO_IPP]['id_customer']);
            $ArrJurnal[$key]['nm_customer']     = strtoupper($DETAIL_IPP[$NO_IPP]['nm_customer']);
            $ArrJurnal[$key]['nm_project']      = strtoupper($DETAIL_IPP[$NO_IPP]['nm_project']);
			$ArrJurnal[$key]['qty'] 	        = $value['qty'];
			$ArrJurnal[$key]['no_drawing'] 	    = $value['no_drawing'];
			$ArrJurnal[$key]['sts'] 	        = $value['sts'];
			$ArrJurnal[$key]['product_code'] 	= $value['product_code'];
			$ArrJurnal[$key]['product_ke'] 	    = $value['product_ke'];
			$ArrJurnal[$key]['length'] 	        = $value['length'];
			$ArrJurnal[$key]['kode_spk'] 	    = $value['kode_spk'];
			$ArrJurnal[$key]['hist_date'] 	    = $datetime;
		}
        // echo '<pre>';
        // print_r($ArrJurnal);
        // exit;

		$this->db->trans_start();
            if(!empty($ArrJurnal)){
                $this->db->insert_batch('stock_spool_per_day',$ArrJurnal);
            }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}

    public function try_deadstok(){
        $DateTime = date('Y-m-d H:i:s');
        // $DateTime = date('2024-02-15 23:59:59');
        // $DETAIL_IPP = get_detail_ipp();
        // //CRON JOB DEADSTOK
        // $SQL_DEADSTOK = "SELECT
        //                     a.*
        //                 FROM
        //                     deadstok a
        //                 WHERE  
        //                     a.deleted_date IS NULL 
        //                     AND a.kode_delivery IS NULL 
        //                     AND a.id_booking IS NOT NULL 
        //                     AND a.process_next = 1
        //                     AND a.qc_date IS NOT NULL
        //                 ";
        // $resultDeadstok = $this->db->query($SQL_DEADSTOK)->result_array();
        // $ArrInsertDeadstok = [];
        // foreach ($resultDeadstok as $key => $value) {
        //     $NO_IPP     = $value['no_ipp'];

        //     $length     = ($value['length'] > 0)?' x '.$value['length']:'';
        //     $type_std   = (!empty($value['type_std']))?$value['type_std'].', ':'';
        //     $resin      = (!empty($value['resin']))?$value['resin'].', ':'';

        //     $ArrInsertDeadstok[$key]['category']      = 'deadstok';
        //     $ArrInsertDeadstok[$key]['no_ipp']        = $NO_IPP;
        //     $ArrInsertDeadstok[$key]['no_so']         = $value['no_so'];
        //     $ArrInsertDeadstok[$key]['no_spk']        = $value['no_spk'];
        //     $ArrInsertDeadstok[$key]['id_milik']      = $value['id_milik'];
        //     $ArrInsertDeadstok[$key]['product']       = $value['product_name'];
        //     $ArrInsertDeadstok[$key]['spec']          = $type_std.$resin.$value['product_spec'].$length;
        //     $ArrInsertDeadstok[$key]['id_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['id_customer']);
        //     $ArrInsertDeadstok[$key]['nm_customer']   = strtoupper($DETAIL_IPP[$NO_IPP]['nm_customer']);
        //     $ArrInsertDeadstok[$key]['nm_project']    = strtoupper($DETAIL_IPP[$NO_IPP]['nm_project']);
        //     $ArrInsertDeadstok[$key]['qty_order']     = $value['id_product'];
        //     $ArrInsertDeadstok[$key]['qty_group_spk'] = NULL;
        //     $ArrInsertDeadstok[$key]['nilai_value']   = NULL;
        //     $ArrInsertDeadstok[$key]['hist_date']     = $DateTime;
        // }

        // if(!empty($ArrInsertDeadstok)){
        //     $this->db->insert_batch('stock_barang_jadi_per_day',$ArrInsertDeadstok);
        // }

		//CRON JOB DEADSTOK VALUE
        $SQL_DEADSTOK_VALUE = "SELECT 
                            a.*,
							COUNT(a.qty) AS qty_stock
                        FROM
                            deadstok a
                        WHERE  
							a.deleted_date IS NULL 
							-- AND a.kode_delivery IS NULL 
							-- AND a.id_booking IS NULL
						GROUP BY a.id_product
                        ";
        $resultDeadstokValue = $this->db->query($SQL_DEADSTOK_VALUE)->result_array();
        $ArrInsertDeadstokValue = [];
        foreach ($resultDeadstokValue as $key => $value) {
            $ArrInsertDeadstokValue[$key]['id_product']     = $value['id_product'];
            $ArrInsertDeadstokValue[$key]['no_barang']      = $value['no_barang'];
            $ArrInsertDeadstokValue[$key]['type']      		= $value['type'];
            $ArrInsertDeadstokValue[$key]['product_name']   = $value['product_name'];
            $ArrInsertDeadstokValue[$key]['type_std']     	= $value['type_std'];
            $ArrInsertDeadstokValue[$key]['product_spec']   = $value['product_spec'];
            $ArrInsertDeadstokValue[$key]['resin']     		= $value['resin'];
            $ArrInsertDeadstokValue[$key]['length']     	= $value['length'];
            $ArrInsertDeadstokValue[$key]['qty']     		= $value['qty_stock'];
            $ArrInsertDeadstokValue[$key]['price_book']     = $value['price_book'];
            $ArrInsertDeadstokValue[$key]['wip_deadstok']   = $value['wip_deadstok'];
            $ArrInsertDeadstokValue[$key]['fg_deadstok']    = $value['fg_deadstok'];
            $ArrInsertDeadstokValue[$key]['hist_date']     	= $DateTime;
        }

        if(!empty($ArrInsertDeadstokValue)){
            $this->db->insert_batch('deadstok_per_day',$ArrInsertDeadstokValue);
        }
        
	}

	public function jurnal_daily_report_disable(){
		$dateC = date('Y-m-d');
		$tanggal = date('Y-m-d', strtotime('-1 days', strtotime($dateC)));
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		// update jurnal gudang produksi ke wip berdasarkan laporan produksi
		$sql="select * from laporan_per_hari where qty_awal is not null and kurs >1 and `date`='".$tanggal."' order by `date`";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
		    foreach ($result as $data) {
				$id_milik=$data['id_milik'];
				$qty_awal=$data['qty_awal'];
				$qty_akhir=$data['qty_akhir'];
				$total_qty=($qty_akhir-$qty_awal+1);
				$material=$data['real_harga'];
				$dl=$data['direct_labour'];
				$idl=$data['indirect_labour'];
				$mch=$data['machine'];
				$mml=$data['mould_mandrill'];
				$csm=$data['consumable'];
				$fcsm=$data['foh_consumable'];
				$fdpr=$data['foh_depresiasi'];
				$bgnp=$data['biaya_gaji_non_produksi'];
				$bnp=$data['biaya_non_produksi'];
				$brb=$data['biaya_rutin_bulanan'];
				$wip_kurs=$data['kurs'];
				$wip_material=round($material*$wip_kurs/$total_qty);
				$wip_dl=round($dl*$wip_kurs/$total_qty);
				$wip_foh=round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs/$total_qty);
				$wip_il=round($idl*$wip_kurs/$total_qty);
				$wip_consumable=round($csm*$wip_kurs/$total_qty);
				$finish_good=($wip_material+$wip_dl+$wip_foh+$wip_il+$wip_consumable);
				$this->db->query("update production_detail set wip_kurs='".$wip_kurs."', wip_material='".$wip_material."', wip_dl='".$wip_dl."', wip_foh='".$wip_foh."', wip_il='".$wip_il."', wip_consumable='".$wip_consumable."', finish_good='".$finish_good."' where id_milik ='".$id_milik."' and product_ke between ".$qty_awal." and ".$qty_akhir."");
				$nomor++;
		// jurnal
				$tgl_po=$data['date'];
				$tgl_voucher=$data['date'];
				$total=round(($mch+$mml+$fdpr+$brb+$fcsm+$material+$dl+$idl+$csm)*$wip_kurs);
				$keterangan='GD. PRODUKSI - WIP ( '.$tgl_po.' )';
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);
				$reff=$data['id'];
				$no_spk=$data['no_spk'];
				$no_so=$data['no_so'];
				$Bln	= substr($tgl_po,5,2);
				$Thn	= substr($tgl_po,0,4);
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $total, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'system', 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				$kodejurnal='JV004';
				$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
				foreach($datajurnal AS $record) {
					$posisi = $record->posisi;
					$nokir  = $record->no_perkiraan;
					$parameter_no = $record->parameter_no;
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Material",
						  'no_reff'       => $reff,
						  'debet'         => round($material*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Direct Labour",
						  'no_reff'       => $reff,
						  'debet'         => round($dl*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Indirect Labour",
						  'no_reff'       => $reff,
						  'debet'         => round($idl*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Consumable",
						  'no_reff'       => $reff,
						  'debet'         => round($csm*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP FOH",
						  'no_reff'       => $reff,
						  'debet'         => round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
					} elseif ($posisi=='K'){
						if($parameter_no=="1") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "GUDANG PRODUKSI (Material)".$no_spk." ".$no_so,
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($material*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="2") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "DIRECT LABOUR PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($dl*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="3") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "INDIRECT LABOUR PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($idl*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="4") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "CONSUMABLE PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($csm*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="5") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "FOH PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							  'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
					}
				}
				$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
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
						'created_by'	=> 'system',
						'created_on'	=> date("Y-m-d"),
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				}
				unset ($det_Jurnaltes);
			}
		}else{
			echo 'EMPTY REPORT';
		}
	}

	public function cutoff_data_wip(){
		$datetime = date('Y-m-d H:i:s');
		$get_data = $this->db->query("SELECT * FROM data_erp_wip_group")->result_array();
		$ArrJurnal = [];
		foreach ($get_data as $key => $value) {
			$ArrJurnal[$key]['tanggal'] = $value['tanggal'];
			$ArrJurnal[$key]['keterangan'] = $value['keterangan'];
			$ArrJurnal[$key]['no_so'] = $value['no_so'];
			$ArrJurnal[$key]['product'] = $value['product'];
			$ArrJurnal[$key]['no_spk'] = $value['no_spk'];
			$ArrJurnal[$key]['kode_trans'] = $value['kode_trans'];
			$ArrJurnal[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrJurnal[$key]['qty'] = $value['qty'];
			$ArrJurnal[$key]['nilai_wip'] = $value['nilai_wip'];
            $ArrJurnal[$key]['material'] = $value['material'];
            $ArrJurnal[$key]['wip_direct'] = $value['wip_direct'];
            $ArrJurnal[$key]['wip_indirect'] = $value['wip_indirect'];
            $ArrJurnal[$key]['wip_consumable'] = $value['wip_consumable'];
			$ArrJurnal[$key]['wip_foh'] = $value['wip_foh'];
			$ArrJurnal[$key]['created_by'] = $value['created_by'];
			$ArrJurnal[$key]['created_date'] = $value['created_date'];
			$ArrJurnal[$key]['id_trans'] = $value['id_trans'];
			$ArrJurnal[$key]['jenis'] = $value['jenis'];
			$ArrJurnal[$key]['id_material'] = $value['id_material'];
			$ArrJurnal[$key]['nm_material'] = $value['nm_material'];
			$ArrJurnal[$key]['qty_mat'] = $value['qty_mat'];
			$ArrJurnal[$key]['cost_book'] = $value['cost_book'];
			$ArrJurnal[$key]['gudang'] = $value['gudang'];
			$ArrJurnal[$key]['kode_spool'] = $value['kode_spool'];
			$ArrJurnal[$key]['hist_date'] = $datetime;
		}

		$this->db->trans_start();
            if(!empty($ArrJurnal)){
                $this->db->insert_batch('data_erp_cutoff_wip_group',$ArrJurnal);
            }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}

	public function cutoff_data_fg(){
		$datetime = date('Y-m-d H:i:s');
		$get_data = $this->db->query("SELECT * FROM data_erp_fg")->result_array();
		$ArrJurnal = [];
		foreach ($get_data as $key => $value) {
			$ArrJurnal[$key]['tanggal'] = $value['tanggal'];
			$ArrJurnal[$key]['keterangan'] = $value['keterangan'];
			$ArrJurnal[$key]['no_so'] = $value['no_so'];
			$ArrJurnal[$key]['product'] = $value['product'];
			$ArrJurnal[$key]['no_spk'] = $value['no_spk'];
			$ArrJurnal[$key]['kode_trans'] = $value['kode_trans'];
			$ArrJurnal[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrJurnal[$key]['qty'] = $value['qty'];
			$ArrJurnal[$key]['nilai_wip'] = $value['nilai_wip'];
            $ArrJurnal[$key]['material'] = $value['material'];
            $ArrJurnal[$key]['wip_direct'] = $value['wip_direct'];
            $ArrJurnal[$key]['wip_indirect'] = $value['wip_indirect'];
            $ArrJurnal[$key]['wip_consumable'] = $value['wip_consumable'];
			$ArrJurnal[$key]['wip_foh'] = $value['wip_foh'];
			$ArrJurnal[$key]['created_by'] = $value['created_by'];
			$ArrJurnal[$key]['created_date'] = $value['created_date'];
			$ArrJurnal[$key]['id_trans'] = $value['id_trans'];
			$ArrJurnal[$key]['id_pro'] = $value['id_pro'];
			$ArrJurnal[$key]['qty_ke'] = $value['qty_ke'];
			$ArrJurnal[$key]['nilai_unit'] = $value['nilai_unit'];
			$ArrJurnal[$key]['jenis'] = $value['jenis'];
			$ArrJurnal[$key]['kode_delivery'] = $value['kode_delivery'];
			$ArrJurnal[$key]['id_material'] = $value['id_material'];
			$ArrJurnal[$key]['nm_material'] = $value['nm_material'];
			$ArrJurnal[$key]['qty_mat'] = $value['qty_mat'];
			$ArrJurnal[$key]['cost_book'] = $value['cost_book'];
			$ArrJurnal[$key]['gudang'] = $value['gudang'];
			$ArrJurnal[$key]['kode_spool'] = $value['kode_spool'];
			$ArrJurnal[$key]['hist_date'] = $datetime;
		}

		$this->db->trans_start();
            if(!empty($ArrJurnal)){
                $this->db->insert_batch('data_erp_cutoff_fg',$ArrJurnal);
            }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}

	public function cutoff_data_transit(){
		$datetime = date('Y-m-d H:i:s');
		$get_data = $this->db->query("SELECT * FROM data_erp_in_transit")->result_array();
		$ArrJurnal = [];
		foreach ($get_data as $key => $value) {
			$ArrJurnal[$key]['tanggal'] = $value['tanggal'];
			$ArrJurnal[$key]['keterangan'] = $value['keterangan'];
			$ArrJurnal[$key]['no_so'] = $value['no_so'];
			$ArrJurnal[$key]['product'] = $value['product'];
			$ArrJurnal[$key]['no_spk'] = $value['no_spk'];
			$ArrJurnal[$key]['kode_trans'] = $value['kode_trans'];
			$ArrJurnal[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrJurnal[$key]['qty'] = $value['qty'];
			$ArrJurnal[$key]['nilai_unit'] = $value['nilai_unit'];
            $ArrJurnal[$key]['created_by'] = $value['created_by'];
            $ArrJurnal[$key]['created_date'] = $value['created_date'];
            $ArrJurnal[$key]['id_trans'] = $value['id_trans'];
            $ArrJurnal[$key]['id_pro'] = $value['id_pro'];
			$ArrJurnal[$key]['qty_ke'] = $value['qty_ke'];
			$ArrJurnal[$key]['kode_delivery'] = $value['kode_delivery'];
			$ArrJurnal[$key]['jenis'] = $value['jenis'];
			$ArrJurnal[$key]['id_material'] = $value['id_material'];
			$ArrJurnal[$key]['nm_material'] = $value['nm_material'];
			$ArrJurnal[$key]['qty_mat'] = $value['qty_mat'];
			$ArrJurnal[$key]['cost_book'] = $value['cost_book'];
			$ArrJurnal[$key]['gudang'] = $value['gudang'];
			$ArrJurnal[$key]['kode_spool'] = $value['kode_spool'];
			$ArrJurnal[$key]['hist_date'] = $datetime;
		}

		$this->db->trans_start();
            if(!empty($ArrJurnal)){
                $this->db->insert_batch('data_erp_cutoff_in_transit',$ArrJurnal);
            }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}

	public function cutoff_data_customer(){
		$datetime = date('Y-m-d H:i:s');
		$get_data = $this->db->query("SELECT * FROM data_erp_in_customer")->result_array();
		$ArrJurnal = [];
		foreach ($get_data as $key => $value) {
			$ArrJurnal[$key]['tanggal'] = $value['tanggal'];
			$ArrJurnal[$key]['keterangan'] = $value['keterangan'];
			$ArrJurnal[$key]['no_so'] = $value['no_so'];
			$ArrJurnal[$key]['product'] = $value['product'];
			$ArrJurnal[$key]['no_spk'] = $value['no_spk'];
			$ArrJurnal[$key]['kode_trans'] = $value['kode_trans'];
			$ArrJurnal[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrJurnal[$key]['qty'] = $value['qty'];
			$ArrJurnal[$key]['nilai_unit'] = $value['nilai_unit'];
            $ArrJurnal[$key]['created_by'] = $value['created_by'];
            $ArrJurnal[$key]['created_date'] = $value['created_date'];
            $ArrJurnal[$key]['id_trans'] = $value['id_trans'];
            $ArrJurnal[$key]['id_pro'] = $value['id_pro'];
			$ArrJurnal[$key]['qty_ke'] = $value['qty_ke'];
			$ArrJurnal[$key]['kode_delivery'] = $value['kode_delivery'];
			$ArrJurnal[$key]['jenis'] = $value['jenis'];
			$ArrJurnal[$key]['id_material'] = $value['id_material'];
			$ArrJurnal[$key]['nm_material'] = $value['nm_material'];
			$ArrJurnal[$key]['qty_mat'] = $value['qty_mat'];
			$ArrJurnal[$key]['cost_book'] = $value['cost_book'];
			$ArrJurnal[$key]['gudang'] = $value['gudang'];
			$ArrJurnal[$key]['kode_spool'] = $value['kode_spool'];
			$ArrJurnal[$key]['hist_date'] = $datetime;
		}

		$this->db->trans_start();
            if(!empty($ArrJurnal)){
                $this->db->insert_batch('data_erp_cutoff_in_customer',$ArrJurnal);
            }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}
}