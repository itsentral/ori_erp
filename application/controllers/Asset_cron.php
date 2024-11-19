<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Asset_cron extends CI_Controller{

    public function __construct(){
        parent::__construct();

		$this->load->model('asset_model');
		$this->load->model('master_model');
    }

    public function cron_asset_jurnal_depresiasi(){
        //SAVE JURNAL TO TEMP
        $ArrJurnal = $this->db->get('asset_jurnal')->result_array();

        $ArrDebit = array();
        $ArrKredit = array();
        $ArrJavh = array();
        $Loop = 0;
        foreach($ArrJurnal AS $val => $valx){
            $Loop++;
            
            if($valx['category'] == 1){
                $coaD 	= "6831-02-01";
                $ketD	= "BIAYA PENYUSUTAN KENDARAAN";
                $coaK 	= "1309-05-01";
                $ketK	= "AKUMULASI PENYUSUTAN KENDARAAN";
            }
            if($valx['category'] == 2){
                $coaD 	= "6831-06-01";
                $ketD	= "BIAYA PENYUSUTAN HARTA LAINNYA";
                $coaK 	= "1309-08-01";
                $ketK	= "AKUMULASI PENYUSUTAN HARTA LAINNYA";
            }
            if($valx['category'] == 3){
                $coaD 	= "6831-01-01";
                $ketD	= "BIAYA PENYUSUTAN BANGUNAN";
                $coaK 	= "1309-07-01";
                $ketK	= "AKUMULASI PENYUSUTAN BANGUNAN";
            }
            
            $ArrDebit[$Loop]['category'] 		= $valx['nm_category'];
            $ArrDebit[$Loop]['tipe'] 			= "JV";
            $ArrDebit[$Loop]['nomor'] 			= $Loop;
            $ArrDebit[$Loop]['tanggal'] 		= date('Y-m-d');
            $ArrDebit[$Loop]['no_perkiraan'] 	= $coaD;
            $ArrDebit[$Loop]['keterangan'] 		= $ketD;
            $ArrDebit[$Loop]['kdcab'] 			= $valx['kdcab'];
            $ArrDebit[$Loop]['debet'] 			= $valx['sisa_nilai'];
            $ArrDebit[$Loop]['kredit'] 			= 0;
            
            $ArrKredit[$Loop]['category'] 		= $valx['nm_category'];
            $ArrKredit[$Loop]['tipe'] 			= "JV";
            $ArrKredit[$Loop]['nomor'] 			= $Loop;
            $ArrKredit[$Loop]['tanggal'] 		= date('Y-m-d');
            $ArrKredit[$Loop]['no_perkiraan'] 	= $coaK;
            $ArrKredit[$Loop]['keterangan'] 	= $ketK;
            $ArrKredit[$Loop]['kdcab'] 			= $valx['kdcab'];
            $ArrKredit[$Loop]['debet'] 			= 0;
            $ArrKredit[$Loop]['kredit'] 		= $valx['sisa_nilai'];
        }
        
        $this->db->trans_start();
            $this->db->truncate('asset_jurnal_temp');
            $this->db->insert_batch('asset_jurnal_temp', $ArrDebit);
            $this->db->insert_batch('asset_jurnal_temp', $ArrKredit);
        $this->db->trans_complete();
        
        //SAVE JURNAL & UPDATE DEPRESIASI
		$ArrDel = $this->db->query("SELECT nomor FROM jurnaltras WHERE jenis_trans = 'asset jurnal' AND SUBSTRING_INDEX(tanggal, '-', 2) = '".date('Y-m')."' GROUP BY nomor ")->result_array();

		$dtListArray = array();
		foreach($ArrDel AS $val => $valx){
			$dtListArray[$val] = $valx['nomor'];
		}

		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$date_now	= date('Y-m-d');
		$bln		= ltrim(date('m'), 0);
		$thn		= date('Y');
		$bulanx		= date('m');

		if(!empty($this->input->post('tgl_jurnal'))){
			$date_now	= $this->input->post('tgl_jurnal')."-01";
			$DtExpl		= explode('-', $date_now);
			$bln		= ltrim($DtExpl[1], 0);
			$thn		= $DtExpl[0];
			$bulanx		= $DtExpl[1];
		}

		$ArrJurnal_D = $this->Asset_model->getList('asset_jurnal');
		$ArrDebit = array();
		$ArrKredit = array();
		$ArrJavh = array();
		$Loop = 0;
		foreach($ArrJurnal_D AS $val => $valx){
			$Loop++;

			if($valx['category'] == 1){
				$coaD 	= "6831-02-01";
				$ketD	= "BIAYA PENYUSUTAN KENDARAAN";
				$coaK 	= "1309-05-01";
				$ketK	= "AKUMULASI PENYUSUTAN KENDARAAN";
			}
			if($valx['category'] == 2){
				$coaD 	= "6831-06-01";
				$ketD	= "BIAYA PENYUSUTAN HARTA LAINNYA";
				$coaK 	= "1309-08-01";
				$ketK	= "AKUMULASI PENYUSUTAN HARTA LAINNYA";
			}
			if($valx['category'] == 3){
				$coaD 	= "6831-01-01";
				$ketD	= "BIAYA PENYUSUTAN BANGUNAN";
				$coaK 	= "1309-07-01";
				$ketK	= "AKUMULASI PENYUSUTAN BANGUNAN";
			}

			$ArrDebit[$Loop]['tipe'] 			= "JV";
			$ArrDebit[$Loop]['nomor'] 			= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrDebit[$Loop]['tanggal'] 		= $date_now;
			$ArrDebit[$Loop]['no_perkiraan'] 	= $coaD;
			$ArrDebit[$Loop]['keterangan'] 		= $ketD;
			$ArrDebit[$Loop]['no_reff'] 		= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrDebit[$Loop]['debet'] 			= $valx['sisa_nilai'];
			$ArrDebit[$Loop]['kredit'] 			= 0;
			$ArrDebit[$Loop]['jenis_trans'] 	= 'asset jurnal';

			$ArrKredit[$Loop]['tipe'] 			= "JV";
			$ArrKredit[$Loop]['nomor'] 			= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrKredit[$Loop]['tanggal'] 		= $date_now;
			$ArrKredit[$Loop]['no_perkiraan'] 	= $coaK;
			$ArrKredit[$Loop]['keterangan'] 	= $ketK;
			$ArrKredit[$Loop]['no_reff'] 		= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrKredit[$Loop]['debet'] 			= 0;
			$ArrKredit[$Loop]['kredit'] 		= $valx['sisa_nilai'];
			$ArrKredit[$Loop]['jenis_trans'] 	= 'asset jurnal';

			$ArrJavh[$Loop]['nomor'] 			= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrJavh[$Loop]['tgl'] 				= $date_now;
			$ArrJavh[$Loop]['jml'] 				= $valx['sisa_nilai'];
			$ArrJavh[$Loop]['kdcab'] 			= $valx['kdcab'];
			$ArrJavh[$Loop]['jenis'] 			= "V";
			$ArrJavh[$Loop]['keterangan'] 		= "PENYUSUTAN ASSET";
			$ArrJavh[$Loop]['bulan'] 			= $bln;
			$ArrJavh[$Loop]['tahun'] 			= $thn;
			$ArrJavh[$Loop]['user_id'] 			= "System";
			$ArrJavh[$Loop]['tgl_jvkoreksi'] 	= $date_now;

			$this->Jurnal_model->update_Nomor_Jurnal($valx['kdcab'],'JM');
		}

		// echo "<pre>";
		// print_r($ArrDebit);
		// print_r($ArrKredit);
		// print_r($ArrJavh);
		// exit;

		$this->db->trans_start();
			$this->db->query("DELETE FROM jurnaltras WHERE nomor IN ".$dtImplode." ");
			$this->db->query("DELETE FROM javh WHERE nomor IN ".$dtImplode." ");
			$this->db->insert_batch('jurnaltras', $ArrDebit);
			$this->db->insert_batch('jurnaltras', $ArrKredit);
			$this->db->insert_batch('javh', $ArrJavh);
			$this->db->query("UPDATE asset_generate SET flag='Y' WHERE bulan='".$bulanx."' AND tahun='".$thn."' ");
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$this->db->query("INSERT INTO asset_jurnal_log (tanggal, ket, jurnal_by, bulan, tahun, kdcab) VALUES ('".date('Y-m-d H:i:s')."', 'FAILED', 'system', '".$bulanx."', '".$thn."', 'ORI')");
		}
		else{
			$this->db->trans_commit();
			$this->db->query("INSERT INTO asset_jurnal_log (tanggal, ket, jurnal_by, bulan, tahun, kdcab) VALUES ('".date('Y-m-d H:i:s')."', 'SUCCESS', 'system', '".$bulanx."', '".$thn."', 'ORI')");
		}
	}
}
?>
