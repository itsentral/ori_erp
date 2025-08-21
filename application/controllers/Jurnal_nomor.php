<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @author Harboens
 * @copyright Copyright (c) 2018, Harboens
 *
 * This is controller for Purchase Order
 */

class Jurnal_nomor extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Acc_model',
                                 'Jurnal_model'));
        date_default_timezone_set("Asia/Bangkok");
		$this->datppn=array('0'=>'Non PPN','10'=>'PPN');
		$this->datcombodata=array('No'=>'No','Asli'=>'Asli','Copy'=>'Copy');
    }

	function index()
    {
        // $data = $this->Purchase_order_model->GetListPR('BIAYA');
        // $this->template->set('results', $data);
        // $this->template->title('Purchase Request Operational Titik (Existing)');
        // $this->template->render('list');
	}

    function view_jurnal_jv() {
        // $data = $this->Purchase_order_model->GetListPR('BIAYA');
        // $this->template->set('results', $data);
        // $this->template->title('Purchase Request Operational Titik (Existing)');
        // $this->template->render('list');

		//JURNAL JV PINDAH GUDANG

		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);


		$no_request = $id;

		$nama = $this->db->query("SELECT * FROM jurnal WHERE id ='$id'")->row();
//        $Tgl_Invoice = date('Y-m-d');
        $Tgl_Invoice = $nama->tanggal;
		$tgl_voucher =$Tgl_Invoice;

		$kd_bayar    =$nama->kode_trans;

		$Keterangan_INV		    = 'PINDAH GUDANG '.$nama->nm_material.' Dari '.rawurldecode($ket);

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);

		foreach($datajurnal AS $record){
			$nokir1  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			if ($field == 'jumlah_bank'){
				$nokir = $kd_bank;
			} else{
				$nokir  = $record->no_perkiraan;
			}
			$no_voucher = $kd_bayar;
			$param  = 'id';
			$value_param  = $id;
			$val = $this->Acc_model->GetData($tabel,$field,$param,$value_param);
			$nilaibayar = $val[0]->$field;
			// print_r($nilaibayar);
			// exit;
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $nilaibayar,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'pindah gudang',
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $nilaibayar,
				  'jenis_jurnal'  => 'pindah gudang',
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'pindah gudang');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'pindah gudang';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
	function get_wip($id_milik,$id_spk,$date,$wip_material){
		$sqldata	="select id from jurnal_production_report where id_milik='".$id_milik."' and id_spk='".$id_spk."' and `date`='".$date."' group by `date`,id_milik,id_spk";
		$dtdata	= $this->db->query($sqldata)->row();
		if(!empty($dtdata)) return;

		$GET_COST_FOH = get_cost_foh();
		$kurs=1;
		$sqlkurs="select * from ms_kurs where tanggal <='".$date."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs	= $this->db->query($sqlkurs)->row();
		if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
		$sqlHeader	= "SELECT a.*, b.id_milik FROM history_pro_header_cron a LEFT JOIN production_detail b ON a.id_production_detail = b.id WHERE DATE(a.status_date)='".$date."' and a.id_spk='".$id_spk."' and b.id_milik='".$id_milik."' ";
		//echo $sqlHeader.'<hr>';
		$restHeader	= $this->db->query($sqlHeader)->result_array();
		if(!empty($restHeader)){
			$sqlDel2 = "DELETE FROM jurnal_production_report WHERE `date`='".$date."' and id_milik='".$id_milik."' and id_spk='".$id_spk."'";
			$this->db->query($sqlDel2);

			$ArrDay				= array();
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
			$Sum_man_hours      = 0;
            foreach($restHeader AS $val=>$valx){

                $restBy     = $this->db->select('direct_labour, indirect_labour, machine, mould_mandrill, consumable, est_harga')
										->limit(1)
										->get_where('so_estimasi_cost_and_mat_fast',array('id_milik'=>$valx['id_milik']))
										->result_array();

                $restBan    = $this->db->limit(1)->get_where('banding_so_mat_pro_fast',array('id_detail'=>$valx['id_production_detail']))->result_array();
				
                $jumTot     = ($valx['qty_akhir'] - $valx['product_ke']) + 1;
				$qty_real	= ($valx['qty_akhir']-$valx['product_ke']+1);

				$GET_DETAIL_PRODUCT = $this->db->select('diameter,diameter2,pressure,liner')->get_where('so_component_header',array('id_milik'=>$valx['id_milik']))->result_array();
				$GET_DETAIL_SO 		= $this->db->select('man_hours')->get_where('so_detail_header',array('id'=>$valx['id_milik']))->result_array();

				$diameter    = (!empty($GET_DETAIL_PRODUCT[0]['diameter']))?$GET_DETAIL_PRODUCT[0]['diameter']:NULL;
                $diameter2   = (!empty($GET_DETAIL_PRODUCT[0]['diameter2']))?$GET_DETAIL_PRODUCT[0]['diameter2']:NULL;
                $pressure    = (!empty($GET_DETAIL_PRODUCT[0]['pressure']))?$GET_DETAIL_PRODUCT[0]['pressure']:NULL;
                $liner       = (!empty($GET_DETAIL_PRODUCT[0]['liner']))?$GET_DETAIL_PRODUCT[0]['liner']:NULL;
                $man_hours   = (!empty($GET_DETAIL_SO[0]['man_hours']))?$GET_DETAIL_SO[0]['man_hours']:0;

				$total_biaya = $restBy[0]['direct_labour'] + $restBy[0]['indirect_labour'] + $restBy[0]['machine'] + $restBy[0]['mould_mandrill'] + $restBy[0]['consumable'] + $restBy[0]['est_harga'];
				$foh_consumable   			= $total_biaya * ($GET_COST_FOH[1] / 100) * $jumTot;
				$foh_depresiasi   			= $total_biaya * ($GET_COST_FOH[2] / 100) * $jumTot;
				$biaya_gaji_non_produksi   	= $total_biaya * ($GET_COST_FOH[3] / 100) * $jumTot;
				$biaya_non_produksi   		= $total_biaya * ($GET_COST_FOH[4] / 100) * $jumTot;
				$biaya_rutin_bulanan   		= $total_biaya * ($GET_COST_FOH[5] / 100) * $jumTot;

                $ArrDay[$val]['id_produksi']            = $valx['id_produksi'];
                $ArrDay[$val]['id_category']            = $valx['id_category'];
                $ArrDay[$val]['id_product']             = $valx['id_product'];
                $ArrDay[$val]['diameter']               = $diameter;
                $ArrDay[$val]['diameter2']              = $diameter2;
                $ArrDay[$val]['pressure']               = $pressure;
                $ArrDay[$val]['liner']                  = $liner;
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

                $ArrDay[$val]['direct_labour']          = $restBy[0]['direct_labour'] * $jumTot;
                $ArrDay[$val]['indirect_labour']        = $restBy[0]['indirect_labour'] * $jumTot;
                $ArrDay[$val]['machine']                = $restBy[0]['machine'] * $jumTot;
                $ArrDay[$val]['mould_mandrill']         = $restBy[0]['mould_mandrill'] * $jumTot;
                $ArrDay[$val]['consumable']             = $restBy[0]['consumable'] * $jumTot;
                $ArrDay[$val]['foh_consumable']         = $foh_consumable;
                $ArrDay[$val]['foh_depresiasi']         = $foh_depresiasi;
                $ArrDay[$val]['biaya_gaji_non_produksi']= $biaya_gaji_non_produksi;
                $ArrDay[$val]['biaya_non_produksi']     = $biaya_non_produksi;
                $ArrDay[$val]['biaya_rutin_bulanan']    = $biaya_rutin_bulanan;

                $ArrDay[$val]['insert_by']              = 'system';
                $ArrDay[$val]['insert_date']            = date('Y-m-d H:i:s');

				$sqlInsertDet = "INSERT INTO jurnal_production_report
									(id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
									qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
									real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
									consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
									biaya_rutin_bulanan,insert_by,insert_date,man_hours,qty_real,kurs,id_spk,wip_material)
									VALUE
									(
										'".$valx['id_produksi']."',
										'".$valx['id_category']."',
										'".$valx['id_product']."',
										'".$diameter."',
										'".$diameter2."',
										'".$pressure."', 
										'".$liner."',
										'".$valx['status_date']."',
										'".$valx['product_ke']."',
										'".$valx['qty_akhir']."',
										'".$valx['qty']."','".$date."',
										'".$valx['id_production_detail']."', 
										'".$valx['id_milik']."',
										'".$restBan[0]['est_material'] * $jumTot."',
										'".$restBan[0]['est_harga'] * $jumTot."', 
										'".$restBan[0]['real_material']."',
										'".$restBan[0]['real_harga']."',
										'".$restBy[0]['direct_labour'] * $jumTot."', 
										'".$restBy[0]['indirect_labour'] * $jumTot."',
										'".$restBy[0]['machine'] * $jumTot."', 
										'".$restBy[0]['mould_mandrill'] * $jumTot."',
										'".$restBy[0]['consumable'] * $jumTot."', 
										'".$foh_consumable."',
										'".$foh_depresiasi."', 
										'".$biaya_gaji_non_produksi."',
										'".$biaya_non_produksi."', 
										'".$biaya_rutin_bulanan."',
										'system',
										'".date('Y-m-d H:i:s')."',
										'".$man_hours * $jumTot."',
										'".$qty_real."','".$kurs."',
										'".$id_spk."',
										'".$wip_material."')
								";
				$this->db->query($sqlInsertDet);
			}
		}
	}
	function view_jurnal_wip()
    {
		//JURNAL JV PINDAH GUDANG WIP
		
		$autoj = $this->uri->segment(3);
		$id_milik	= $this->input->post('id_milik');
		$total_sum = $this->input->post('total_sum');
		$id_spk = $this->input->post('id_spk');
		$tanggal = $this->input->post('tanggal');
	   	$kodejurnal = $this->input->post('kd');
	   	$pp = $this->input->post('pp');
	   	$akses = $this->input->post('akses');
        $Tgl_Invoice = $this->input->post('tanggal');//date('Y-m-d');

		$tgl_voucher	= $Tgl_Invoice;
        $kd_bayar		= $id_milik.'-'.$id_spk;
		$id=$kd_bayar.$tanggal;
		$nilaibayar=$total_sum;
		$no_request		= $id;
		$this->get_wip($id_milik,$id_spk,$tanggal,$nilaibayar);
//		die();
		$sqldata="select 
			sum(direct_labour*kurs) as direct_labour,
			sum(indirect_labour*kurs) as indirect_labour,
			sum(consumable*kurs) as consumable,
			sum(machine*kurs) as machine,
			sum(mould_mandrill*kurs) as mould_mandrill,
			sum(foh_depresiasi*kurs) as foh_depresiasi,
			sum(biaya_rutin_bulanan*kurs) as biaya_rutin_bulanan,
			sum(foh_consumable*kurs) as foh_consumable 
			from jurnal_production_report where id_milik='".$id_milik."' and id_spk='".$id_spk."' and `date`='".$tanggal."' group by `date`,id_milik,id_spk";
		$dtwip	= $this->db->query($sqldata)->row();
		$direct_labour		= 0;
		$indirect_labour	= 0;
		$consumable			= 0;
		$foh				= 0;
		$mould_mandrill     = 0;
		if(!empty($dtwip)){
			$direct_labour		= $dtwip->direct_labour;
			$indirect_labour	= $dtwip->indirect_labour;
			$consumable			= $dtwip->consumable;
			$foh	 			= ($dtwip->machine + $dtwip->mould_mandrill + $dtwip->foh_depresiasi + $dtwip->biaya_rutin_bulanan + $dtwip->foh_consumable);
		}
		$Keterangan_INV	= 'PINDAH GUDANG WIP';
		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY
		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
		foreach($datajurnal AS $record){
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$parameter_no = $record->parameter_no;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$no_voucher = $kd_bayar;
			$param  = 'kd_jurnal';
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => "WIP Material",
				  'no_reff'       => $id,
				  'debet'         => $nilaibayar,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'pindah gudang wip',
				  'no_request'    => $id
				 );
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => "WIP Direct Labour",
				  'no_reff'       => $id,
				  'debet'         => $direct_labour,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'pindah gudang wip',
				  'no_request'    => $id
				 );
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => "WIP Indirect Labour",
				  'no_reff'       => $id,
				  'debet'         => $indirect_labour,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'pindah gudang wip',
				  'no_request'    => $id
				 );
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => "WIP Consumable",
				  'no_reff'       => $id,
				  'debet'         => $consumable,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'pindah gudang wip',
				  'no_request'    => $id
				 );
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => "WIP FOH",
				  'no_reff'       => $id,
				  'debet'         => $foh,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'pindah gudang wip',
				  'no_request'    => $id
				 );
			} elseif ($posisi=='K'){
				if($parameter_no=="1") {
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => "GUDANG PRODUKSI (Material)",
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $nilaibayar,
					  'jenis_jurnal'  => 'pindah gudang wip',
					   'no_request'    => $id
					 );
				}
				if($parameter_no=="2") {
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => "DIRECT LABOUR PC LIABILITIES",
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $direct_labour,
					  'jenis_jurnal'  => 'pindah gudang wip',
					   'no_request'    => $id
					 );
				}
				if($parameter_no=="3") {
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => "INDIRECT LABOUR PC LIABILITIES",
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $indirect_labour,
					  'jenis_jurnal'  => 'pindah gudang wip',
					   'no_request'    => $id
					 );
				}
				if($parameter_no=="4") {
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => "CONSUMABLE PC LIABILITIES",
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $consumable,
					  'jenis_jurnal'  => 'pindah gudang wip',
					   'no_request'    => $id
					 );
				}
				if($parameter_no=="5") {
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => "FOH PC LIABILITIES",
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $foh,
					  'jenis_jurnal'  => 'pindah gudang wip',
					   'no_request'    => $id
					 );
				}

			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'pindah gudang wip');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);

		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'pindah gudang wip';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = '';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= ($nilaibayar+$direct_labour+$indirect_labour+$consumable+$foh);
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$data['auto_jurnal']	= $autoj;
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}

	function view_jurnal_wip_history()
    {
		$id_milik	= $this->input->post('id_milik');
		$total_sum = $this->input->post('total_sum');
		$id_spk = $this->input->post('id_spk');
		$tanggal = $this->input->post('tanggal');
        $kd_bayar	= $id_milik.'-'.$id_spk;
		$id=$kd_bayar.$tanggal;
		$nilaibayar=$total_sum;
		$no_request		= $id;

		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'pindah gudang wip';
		$query 	= "SELECT jurnaltras.*, jurnaltras.no_perkiraan, coa_master.nama
		FROM jurnaltras
		left JOIN ".DBACC.".coa_master ON coa_master.no_perkiraan=jurnaltras.no_perkiraan
		WHERE jurnaltras.no_reff = '$id'
		AND jurnaltras.tipe = '$tipe'
		AND jurnaltras.jenis_jurnal = '$jenisjurnal'
		ORDER BY jurnaltras.debet DESC";
		$data['list_data'] 	    = $this->db->query($query)->result();
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = '';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal_history", $data);
	}

	function view_jurnal_penerimaan()
	{
		$db2=$this->load->database('accounting', TRUE);
		$noreff		= $this->uri->segment(3);
		$kode		= $this->uri->segment(4);
		$akses		= $this->uri->segment(5);
		$tipe		= 'JV';
		$jenisjurnal = 'penerimaan';
		$data['list_data'] 	= $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	= $kode;
		$data['akses']	= $akses;
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	= $noreff;
		$data['total_po']		= 0;
		$data['id_vendor']		= '-';
		$data['nama_vendor']	= '-';
		$this->load->view("v_detail_jurnal", $data);
	}

	function view_jurnal_approval()
	{
		$db2=$this->load->database('accounting', TRUE);
		$noreff		= $this->uri->segment(3);
		$kode		= $this->uri->segment(4);
		$akses		= $this->uri->segment(5);
		$total_po	= $this->uri->segment(6);
		$id_vendor	= $this->uri->segment(7);
		$nm_vendor	= $this->uri->segment(8);
		$nama_vendor = rawurldecode($nm_vendor);

		$tipe		= 'JV';
		$jenisjurnal = 'approval';
		$data['list_data'] 		= $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']			= $kode;
		$data['akses']			= $akses;
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']			= $noreff;
		$data['total_po']		= $total_po;
		$data['id_vendor']		= $id_vendor;
		$data['nama_vendor']	= $nama_vendor;
		$this->load->view("v_detail_jurnal", $data);
	}

	function view_jurnal_buk()
	{
		$db2=$this->load->database('accounting', TRUE);
		$noreff		= $this->uri->segment(3);
		$kode		= $this->uri->segment(4);
		$akses		= $this->uri->segment(5);
		$total_po	= $this->uri->segment(6);
		$id_vendor	= $this->uri->segment(7);
		$nm_vendor	= $this->uri->segment(8);
		//$nama_vendor = rawurldecode($nm_vendor);

		//$vendor = $this->db->query("SELECT id_supplier, nm_supplier_office FROM master_supplier WHERE id_supplier='$id_vendor'")->row();
		//echo "<!-- SELECT id_supplier, nm_supplier_office FROM master_supplier WHERE id_supplier='$id_vendor' -->";
		$nama_vendor =''; //$vendor->nm_supplier_office;

		$tipe		= 'BUK';
		$jenisjurnal = 'pembayaran';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = $kode;
		$data['akses']	        = $akses;
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $noreff;
		$data['total_po']		= $total_po;
		$data['id_vendor']		= $vendor->id_supplier;
		$data['nama_vendor']	= $nama_vendor;
		$this->load->view("v_detail_jurnal", $data);
	}

	function view_jurnal_penjualan()
	{
		$db2=$this->load->database('accounting', TRUE);
		$noreff		= $this->uri->segment(3);
		$kode		= $this->uri->segment(4);
		$akses		= $this->uri->segment(5);
		$tipe		= 'BUM';
		$jenisjurnal = 'penjualan';
		$data['list_data'] 	= $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	= $kode;
		$data['akses']	= $akses;
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	= $noreff;
		$this->load->view("v_detail_jurnal_penjualan", $data);
	}

	public function save_jurnal_tras(){



        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		$id    = $this->input->post('no_po');
		$akses       =$this->input->post('akses');
		$tgl_po  =$this->input->post('tgl_jurnal[0]');
		$keterangan  =$this->input->post('keterangan[0]');

		$type        =$this->input->post('type[0]');
		$reff        =$this->input->post('reff[0]');
		$no_req      =$this->input->post('no_request[0]');
		$total       =$this->input->post('total');
		$jenis       =$this->input->post('jenis');
		$tipe_jurnal       =$this->input->post('tipe');
		$jenis_jurnal       =$this->input->post('jenis_jurnal');

		$total_po           =$this->input->post('total_po');
		$id_vendor          =$this->input->post('vendor_id');
		$nama_vendor        =$this->input->post('vendor_nm');




		// print_r($jenis);
		// print_r ($jenis_jurnal);
		// print_r ($reff);
		// exit;





		$this->db->trans_begin();

		$Nomor_JV				= $this->Jurnal_model->get_no_buk('101');


				$Bln 			= substr($tgl_po,5,2);
				$Thn 			= substr($tgl_po,0,4);
				// ## NOMOR JV ##
				// $Nomor_JV				= $this->Jurnal_model->get_no_buk('101');




        				$dataJVhead = array(
          					'nomor' 	    	=> $Nomor_JV,
          					'tgl'	         	=> $tgl_po,
          					'jml'	            => $total,
          					'kdcab'				=> '101',
          					'jenis_reff'	    => 'BUK',
          					'no_reff' 		    => $reff,
							'customer' 		    => $nama_vendor,
							'bayar_kepada'      => $nama_vendor,
							'jenis_ap'			=> 'V',
							'note'				=> $keterangan,
        					'user_id'			=> $session['username'],
          					'ho_valid'			=> '',
							'batal'			    => '0'
          				);
				$this->db->insert(DBACC.'.JAPH',$dataJVhead);



        for($i=0;$i < count($this->input->post('type'));$i++){
			$tipe =$this->input->post('type')[$i];
			$perkiraan =$this->input->post('no_coa')[$i];
			$noreff =$this->input->post('reff')[$i];
			$jenisjurnal =$this->input->post('jenisjurnal')[$i];

            $datadetail = array(
                'tipe'        => $this->input->post('type')[$i],
                'nomor'       => $Nomor_JV,
                'tanggal'     => $this->input->post('tgl_jurnal')[$i],
                'no_perkiraan'    => $this->input->post('no_coa')[$i],
                'keterangan'      => $this->input->post('keterangan')[$i],
                'no_reff'     	  => $this->input->post('reff')[$i],
				'debet'      	  => $this->input->post('debet')[$i],
				'kredit'          => $this->input->post('kredit')[$i]
                );
            $this->db->insert(DBACC.'.jurnal',$datadetail);

			$jurnal_posting	 = "UPDATE jurnal SET stspos=1 WHERE tipe = '$tipe'
			AND  jenis_jurnal = '$jenisjurnal' AND no_reff  = '$noreff' ";
            $this->db->query($jurnal_posting);



        }


		$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobuk=nobuk + 1 WHERE nocab='101'";
        $this->db->query($Qry_Update_Cabang_acc);

		$jurnal_po	 = "UPDATE purchase_order_payment SET status_jurnal='1' WHERE kd_pembayaran = '$reff' ";
        $this->db->query($jurnal_po);







		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => $total_po,
				'kredit'          => 0,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,

                );

        $this->db->insert('tr_kartu_hutang',$datahutang);



        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            $param = array(
            'save' => 0,
            'msg' => "GAGAL, simpan data..!!!",

            );
        }
        else
        {
            $this->db->trans_commit();

            $param = array(
            'save' => 1,
            'msg' => "SUKSES, simpan data..!!!",

            );
        }
        echo json_encode($param);
    }



	public function save_jurnal_jv(){
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		$id    =  $this->input->post('po_no');
		$akses       =$this->input->post('akses');
		// print_r($akses);
		// exit;
		$tgl_po  =$this->input->post('tgl_jurnal[0]');
		$keterangan  =$this->input->post('keterangan[0]');
		$type        =$this->input->post('type[0]');
		$reff        =$this->input->post('reff[0]');
		$no_req      =$this->input->post('no_request[0]');
		$total       =$this->input->post('total');
		$jenis       =$this->input->post('jenis');
		$tipe_jurnal       =$this->input->post('tipe');
		$jenis_jurnal       =$this->input->post('jenis_jurnal');
		$total_po           =$this->input->post('total_po');
		$id_vendor          =$this->input->post('vendor_id');
		$nama_vendor        =$this->input->post('vendor_nm');
		// print_r($jenis);
		// print_r ($jenis_jurnal);
		// print_r ($reff);
		// exit;
		$this->db->trans_begin();
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);

		$Bln 			= substr($tgl_po,5,2);
		$Thn 			= substr($tgl_po,0,4);
		// ## NOMOR JV ##
		// $Nomor_JV				= $this->Jurnal_model->get_no_buk('101');
		// $dataJVhead = array(
					// 'nomor' 	    	=> $Nomor_JV,
					// 'tgl'	         	=> $tgl_po,
					// 'jml'	            => $total,
					// 'kdcab'				=> '101',
					// 'jenis_reff'	    => 'BUK',
					// 'no_reff' 		    => $reff,
					// 'customer' 		    => $nama_vendor,
					// 'bayar_kepada'      => $nama_vendor,
					// 'jenis_ap'			=> 'V',
					// 'note'				=> $keterangan,
					// 'user_id'			=> $session['username'],
					// 'ho_valid'			=> '',
					// 'batal'			    => '0'
				// );
		$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $total, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $session['username'], 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');

		$this->db->insert(DBACC.'.javh',$dataJVhead);

        for($i=0;$i < count($this->input->post('type'));$i++){
			$tipe =$this->input->post('type')[$i];
			$tgl_jurnal =$tgl_po;
			$perkiraan =$this->input->post('no_coa')[$i];
			$noreff =$this->input->post('reff')[$i];
			$jenisjurnal =$this->input->post('jenisjurnal')[$i];

            $datadetail = array(
                'tipe'        => $this->input->post('type')[$i],
                'nomor'       => $Nomor_JV,
                'tanggal'     => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa')[$i],
                'keterangan'      => $this->input->post('keterangan')[$i],
                'no_reff'     	  => $this->input->post('reff')[$i],
				'debet'      	  => $this->input->post('debet')[$i],
				'kredit'          => $this->input->post('kredit')[$i]
                );
            $this->db->insert(DBACC.'.jurnal',$datadetail);

			$jurnal_posting	 = "UPDATE jurnaltras SET stspos=1 WHERE tipe = '$tipe'
			AND  jenis_jurnal = '$jenisjurnal' AND no_reff  = '$noreff' ";
            $this->db->query($jurnal_posting);
        }
		if($jenis_jurnal=='intransit incustomer'){
			$this->db->query("UPDATE jurnal_product SET status_jurnal='1' WHERE id ='$id'");
		}
		if($jenis_jurnal=='finish good intransit'){
			$this->db->query("UPDATE jurnal_product SET status_jurnal='1' WHERE id ='$id'");
		}
		if($jenis_jurnal=='wip finishgood'){
			$this->db->query("UPDATE jurnal_product SET status_jurnal='1' WHERE id ='$id'");
	// jurnal_product
			$dtj = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
	// jurnal_production_report
			$djpr = $this->db->query("SELECT id FROM jurnal_production_report WHERE id_milik='".$dtj->id_milik."' and id_produksi like '%".$dtj->no_ipp."%' and qty_real <> qty_fg group by DATE_FORMAT(status_date, '%Y-%m-%d') order by id limit 1")->row();
			if(!empty($djpr)){
				//$this->db->query("update jurnal_production_report set qty_fg=(qty_fg+1) where id='".$djpr->id."'");
			}
		}
		if($jenis_jurnal=='pindah gudang wip'){
			
			$this->db->query("UPDATE jurnal SET status_jurnal='1' WHERE CONCAT(id_milik,'-',id_spk,tanggal) ='$id' and category='laporan produksi'");
		}
		if($jenis_jurnal=='incoming stok'){
			$this->db->query("UPDATE jurnal SET status_jurnal='1' WHERE kode_trans ='$id' and category='".$jenis_jurnal."'");
		}
		if($jenis_jurnal=='incoming department'){
			$this->db->query("UPDATE jurnal SET status_jurnal='1' WHERE kode_trans ='$id' and category='".$jenis_jurnal."'");
		}
		if($jenis_jurnal=='assets'){
			$this->db->query("UPDATE jurnal SET status_jurnal='1' WHERE id ='$id' and category='".$jenis_jurnal."'");
		}		
		if($jenis_jurnal=='incoming asset'){
			$jurnal_status	 = "UPDATE jurnal SET status_jurnal='1' WHERE kode_trans ='$id' and category='".$jenis_jurnal."'";
			$this->db->query($jurnal_status);
		}
		if($jenis_jurnal=='outgoing stok'){
			$jurnal_status	 = "UPDATE jurnal SET status_jurnal='1' WHERE kode_trans ='$id' and category='".$jenis_jurnal."'";
			$this->db->query($jurnal_status);
			$dt_adjheader=$this->db->query("SELECT * FROM warehouse_adjustment where kode_trans ='$id'")->row();
// cek warehouse project
			if($dt_adjheader->id_gudang_ke=='17'){
				$dt_jurnal=$this->db->query("SELECT * FROM jurnal where kode_trans ='$id' and category='".$jenis_jurnal."'")->result_array();
				$ArrDeferred=[];
				foreach ($dt_jurnal as $key => $value) {
					$ArrDeferred[$key]['kode_trans']	= $id;
					$ArrDeferred[$key]['no_so']			= $dt_adjheader->no_so;
					$ArrDeferred[$key]['tanggal']		= $dt_adjheader->tanggal;
					$ArrDeferred[$key]['tipe']			= 'material_indirect';
					$ArrDeferred[$key]['qty']			= $value['qty'];
					$ArrDeferred[$key]['amount']		= $value['total_nilai'];
					$ArrDeferred[$key]['id_material']	= $key;
					$ArrDeferred[$key]['nm_material']	= get_name('con_nonmat_new','material_name','code_group',$key);
				}
				if(!empty($ArrDeferred)) {
					$this->db->insert_batch('tr_deferred',$ArrDeferred);
				}
			}
		}
		if($jenis_jurnal=='finishgood spooling'){
			$this->db->query("UPDATE jurnal_product SET status_jurnal='1' WHERE id ='$id'");
		}
		if($jenis_jurnal=='spooling finishgood'){
			$this->db->query("UPDATE jurnal_product SET status_jurnal='1' WHERE id ='$id'");
		}
		if($jenis_jurnal=='finishgood cutting'){
			$this->db->query("UPDATE jurnal_product SET status_jurnal='1' WHERE id ='$id'");
		}
		if($akses=='jurnal'){
			$jurnal_status	 = "UPDATE jurnal SET status_jurnal='1' WHERE id ='$id'";
			$this->db->query($jurnal_status);
		}
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $param = array(
            'save' => 0,
            'msg' => "GAGAL, simpan data..!!!",
            );
        }
        else
        {
            $this->db->trans_commit();
            $param = array(
            'save' => 1,
            'msg' => "SUKSES, simpan data..!!!",
            );
        }
        echo json_encode($param);
    }

	public function save_jurnal_penjualan(){
        $session = $this->session->userdata('app_session');
		$tgl_po  =$this->input->post('tgl_jurnal[0]');
		$keterangan  =$this->input->post('keterangan[0]');
		$reff        =$this->input->post('reff[0]');
		$total       =$this->input->post('total');
		$jenis       =$this->input->post('jenis');
		$tipe_jurnal       =$this->input->post('tipe');
		$jenis_jurnal       =$this->input->post('jenis_jurnal');
		// print_r($jenis);
		// print_r ($jenis_jurnal);
		// print_r ($reff);
		// exit;

		$db2 = $this->load->database('accounting', TRUE);
		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_BUM('101',$tgl_po);


				$Bln 			= substr($tgl_po,5,2);
				$Thn 			= substr($tgl_po,0,4);
				## NOMOR JV ##
				$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_BUM('101',$tgl_po);

        				$dataJVhead = array(
          					'nomor' 	    	=> $Nomor_JV,
          					'tgl'	         	=> $tgl_po,
          					'jml'	            => $total,
          					'kdcab'				=> '101',
          					'jenis_reff'	    => 'BUM',
          					'no_reff' 		    => $reff,
							'terima_dari'	    => 'Penjualan Aset',
        					'jenis_ar'			=> 'V',
							'note'				=> $keterangan,
							'batal'				=> '0'
          				);
					    $db2->insert('jarh',$dataJVhead);

        for($i=0;$i < count($this->input->post('type'));$i++){
			$tipe =$this->input->post('type')[$i];
			$perkiraan =$this->input->post('no_coa')[$i];
			$noreff =$this->input->post('reff')[$i];
			$jenisjurnal =$this->input->post('jenisjurnal')[$i];

            $datadetail = array(
                'tipe'        => $this->input->post('type')[$i],
                'nomor'       => $Nomor_JV,
                'tanggal'     => $this->input->post('tgl_jurnal')[$i],
                'no_perkiraan'    => $this->input->post('no_coa')[$i],
                'keterangan'      => $this->input->post('keterangan')[$i],
                'no_reff'     	  => $this->input->post('reff')[$i],
				'debet'      	  => $this->input->post('debet')[$i],
				'kredit'          => $this->input->post('kredit')[$i]
                );
             $db2->insert('jurnal',$datadetail);

			$jurnal_posting	 = "UPDATE jurnal SET stspos=1 WHERE tipe = '$tipe'
			AND  jenis_jurnal = '$jenisjurnal' AND no_reff  = '$noreff' ";
            $this->db->query($jurnal_posting);

        }


		$Qry_Update_Cabang_acc	 = "UPDATE pastibisa_tb_cabang SET nobum=nobum + 1 WHERE nocab='101'";
        $db2->query($Qry_Update_Cabang_acc);

	 if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            $param = array(
            'save' =>false,
            'msg' => "GAGAL, simpan data..!!!",

            );
        }
        else
        {
            $this->db->trans_commit();

            $param = array(
            'save' =>true,
            'msg' => "SUKSES1, simpan data..!!!",

            );
        }
        echo json_encode($param);

    }


	function approval_jurnal_po_produksi(){

        $data = $this->Purchase_order_model->GetListPPPO(array(1,2));
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PO Produksi');
        $this->template->render('purchase_order/aproval_jurnal_po');
	}
	public function approval_jurnal_po_stok() {
        $this->auth->restrict($this->viewPermission);
        $data = $this->Po_stock_model->GetApprovaljurnal();
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PO Stok');
        $this->template->render('aproval_jurnal_po_stock');
    }
	public function approval_jurnal_po_nonstok() {
        $data = $this->Po_nonstock_model->GetApprovaljurnal();
        $this->template->set('results', $data);
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PO Non Stok');
        $this->template->render('aproval_jurnal_po_nonstock');
    }
	public function approval_jurnal_po_aset() {

        $data = $this->Po_aset_model->GetPoPaymentAset();
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PO Aset');
        $this->template->render('aproval_jurnal_po_aset');
    }
	public function approval_jurnal_pp_nonstok() {
	$data = $this->Po_nonstock_model->GetPpNonStok_jurnal();
        $this->template->set('results', $data);
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PP Non Stok');
        $this->template->render('aproval_jurnal_pp_nonstock');
    }
	public function approval_jurnal_kasbon_nonstok() {
	    $data = $this->Po_nonstock_model->GetKasbonNonStok();
        $this->template->set('results', $data);
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal Kasbon Non Stok');
        $this->template->render('aproval_jurnal_kasbon_nonstock');
    }
	public function approval_jurnal_pp_aset() {
	$data = $this->Po_aset_model->GetPpAsetJurnal();
        $this->template->set('results', $data);
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PP Aset');
        $this->template->render('aproval_jurnal_pp_aset');
    }
	public function approval_jurnal_kasbon_stok() {
	    $data = $this->Po_stock_model->GetKasbonStok();
        $this->template->set('results', $data);
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal Kasbon Stok');
        $this->template->render('aproval_jurnal_kasbon_stok');
    }
	public function approval_jurnal_po_produksi1() {
        $data = $this->Purchase_order_model->GetListDataPPPO('');
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PO Produksi');
        $this->template->render('aproval_jurnal_po_produksi1');
    }
	function approval_jurnal_pp_produksi1(){

		$data = $this->Purchase_order_model->GetListDataPP('');
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal PP Produksi');
        $this->template->render('aproval_jurnal_pp_produksi1');
	}

	function approval_jurnal_kasbon_produksi1(){

        $data = $this->Purchase_order_model->GetListKasbon('');
        $this->template->set('results', $data);
        $this->template->title('Approval Jurnal Kasbon Produksi');
        $this->template->render('aproval_jurnal_kasbon_produksi1');
	}

	 function view_jurnal_periodik()
    {


		//JURNAL JV PEMBAYARAN PERIODIK

		$id		= $this->uri->segment(3);

		// print_r($id);
		// exit;


		$detail1 = $this->db->query("SELECT * FROM tr_pengajuan_rutin WHERE no_doc='$id'")->row();
		$detail2 = $this->db->query("SELECT * FROM tr_pengajuan_rutin_detail WHERE no_doc='$id'")->result();

		$nilaibayar  = $detail1->nilai_total;
		$coabank 	 = $detail1->coa_bank;
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);

        $Tgl_Invoice = $detail1->tanggal_doc;

		$no_request = $id;
		$tgl_voucher =$Tgl_Invoice;
        $kd_bayar    =$id;

		$Keterangan_INV		    = 'PEMBAYARAN PERIODIK '.rawurldecode($id);


		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

						foreach($detail2 AS $dt1){


						$det_Jurnaltes1[]   = array(
      					  'nomor'         => '',
      					  'tanggal'       => $tgl_voucher,
      					  'tipe'          => 'BUK',
      					  'no_perkiraan'  => $dt1->coa,
      					  'keterangan'    => $Keterangan_INV,
      					  'no_reff'       => $id,
						  'debet'         => $dt1->nilai,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'periodik',
						  'no_request'    => $no_request
					     );


						}

						$this->db->where('no_reff', $id);
						$this->db->delete('jurnaltras');

						$this->db->insert_batch('jurnaltras',$det_Jurnaltes1);

						$det_Jurnaltes2[]  = array(
      					  'nomor'         => '',
      					  'tanggal'       => $tgl_voucher,
      					  'tipe'          => 'BUK',
      					  'no_perkiraan'  => $coabank,
      					  'keterangan'    => $Keterangan_INV,
      					  'no_reff'       => $id,
						  'debet'         => 0,
						  'kredit'        => $nilaibayar,
						  'jenis_jurnal'  => 'periodik',
						   'no_request'    => $no_request
					     );

                         $this->db->insert_batch('jurnaltras',$det_Jurnaltes2);



						$noreff     = $id;
						$tipe		= 'BUK';
						$jenisjurnal = 'periodik';
						$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
						$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
						$data['jenis']	        = 'BUK';
						$data['akses']	        = '';
						$data['jenis_jurnal']	= $jenisjurnal;
						$data['po_no']	        = $noreff;
						$data['total_po']		= $nilaibayar;
						$data['id_vendor']		= '';
						$data['nama_vendor']	= '';

						$this->load->view("Jurnal_tras/v_detail_jurnal", $data);



			}

    function view_jurnal_retur_gudang() {
		//JURNAL JV PINDAH GUDANG
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
        $Tgl_Invoice = date('Y-m-d');
		$no_request = $id;
		$tgl_voucher =$Tgl_Invoice;
		$nama = $this->db->query("select a.*,b.coa_1 coa_dari,c.coa_1 coa_ke from sentralsistem.jurnal a
		left join warehouse b on a.gudang_dari=b.id
		left join warehouse c on a.gudang_ke=c.id WHERE a.id ='$id'")->row();
		$kd_bayar = $nama->kode_trans;
		$Keterangan_INV = 'RETUR GUDANG '.$nama->nm_material.' Dari '.rawurldecode($ket);
		$nilaibayar=$nama->total_nilai;
		$no_voucher = $kd_bayar;
		$det_Jurnaltes[]  = array(
		  'nomor'         => '',
		  'tanggal'       => $tgl_voucher,
		  'tipe'          => 'JV',
		  'no_perkiraan'  => $nama->coa_ke,
		  'keterangan'    => $Keterangan_INV,
		  'no_reff'       => $id,
		  'debet'         => $nilaibayar,
		  'kredit'        => 0,
		  'jenis_jurnal'  => 'pindah gudang',
		  'no_request'    => $no_request
		 );
		$det_Jurnaltes[]  = array(
		  'nomor'         => '',
		  'tanggal'       => $tgl_voucher,
		  'tipe'          => 'JV',
		  'no_perkiraan'  => $nama->coa_dari,
		  'keterangan'    => $Keterangan_INV,
		  'no_reff'       => $id,
		  'debet'         => 0,
		  'kredit'        => $nilaibayar,
		  'jenis_jurnal'  => 'pindah gudang',
		   'no_request'    => $no_request
		 );
		$this->db->where('no_reff', $id);
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'pindah gudang';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}

    function view_jurnal_product() {
		//WIP - FINISH GOOD
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
		$autoj = $this->uri->segment(6);

// jurnal_product
		$datajurnal = $this->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.id ='$id' limit 1" )->row();
        $Tgl_Invoice = $datajurnal->tanggal;
		$no_request = $id;
		$tgl_voucher =$Tgl_Invoice;

// new agus 2023-08-02
// get nilai di so_detail_header
		$datasodetailheader = $this->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
// ambil kurs
		$kurs=1;
		$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs	= $this->db->query($sqlkurs)->row();
		if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
// update production_detail
		$wip_material=$datajurnal->total_nilai;
		$pe_direct_labour=($datasodetailheader->pe_direct_labour*$kurs);
		$foh=(($datasodetailheader->pe_machine + $datasodetailheader->pe_mould_mandrill + $datasodetailheader->pe_foh_depresiasi + $datasodetailheader->pe_biaya_rutin_bulanan + $datasodetailheader->pe_foh_consumable)*$kurs);
		$pe_indirect_labour=($datasodetailheader->pe_indirect_labour*$kurs);
		$pe_consumable=($datasodetailheader->pe_consumable*$kurs);
		$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
		
		$this->db->query("update production_detail set wip_kurs='".$kurs."'
		, wip_material='".$wip_material."'
		, wip_dl='".$pe_direct_labour."'
		, wip_foh='".$foh."'
		, wip_il='".$pe_indirect_labour."'
		, wip_consumable='".$pe_consumable."'
		, finish_good='".$finish_good."'
		WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );

		
// jurnal_production_report
/*
		$datajpr = $this->db->query("SELECT * FROM jurnal_production_report WHERE id_milik='".$datajurnal->id_milik."' and id_produksi like '%".$datajurnal->no_ipp."%' and qty_real <> qty_fg group by DATE_FORMAT(status_date, '%Y-%m-%d') order by id limit 1")->row();
		if(empty($datajpr)) {
			echo 'Error';die();
		}
*/
// master_oto_jurnal_detail
		$masterjurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
		$totaldebit=0;$totalkredit=0;$coa_cogm='';$no_spk=$datajurnal->id_spk;
		foreach($masterjurnal AS $record){
			$debit=0;$kredit=0;
			$nokir  	= $record->no_perkiraan;
			$posisi 	= $record->posisi;
			$parameter  = $record->parameter_no;
			$keterangan = $record->keterangan;
			if ($parameter=='1'){
				$debit=($wip_material);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => $debit,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='2'){
				$debit=($pe_direct_labour);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => $debit,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='3'){
				$debit=($pe_indirect_labour);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => $debit,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='4'){
				$debit=($pe_consumable);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => $debit,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='5'){
				$debit=($foh);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => ($debit),
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='6'){
				$kredit=($wip_material);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $datajurnal->coa,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $kredit,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='7'){
				$kredit=($pe_direct_labour);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $datajurnal->coa,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $kredit,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='8'){
				$kredit=($pe_indirect_labour);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $datajurnal->coa,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $kredit,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='9'){
				$kredit=($pe_consumable);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $datajurnal->coa,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $kredit,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='10'){
				$kredit=($foh);
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $datajurnal->coa,
				  'keterangan'    => $keterangan.' '.$datajurnal->id_spk,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $kredit,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request
				 );
			}
			if ($parameter=='11'){
				$coa_cogm=$nokir;
			}
			$totaldebit+=$debit;$totalkredit+=$kredit;
		}
		$Keterangan_INV=rawurldecode($ket).' ('.$datajurnal->no_so.' - '.$datajurnal->product.' - '.$no_spk.')';
		$nilaibayar=$datajurnal->total_nilai;
		$det_Jurnaltes[]  = array(
		  'nomor'         => '',
		  'tanggal'       => $tgl_voucher,
		  'tipe'          => 'JV',
		  'no_perkiraan'  => $coa_cogm,
		  'keterangan'    => $Keterangan_INV,
		  'no_reff'       => $id,
		  'debet'         => 0,
		  'kredit'        => $totalkredit,
		  'jenis_jurnal'  => 'wip finishgood',
		  'no_request'    => $no_request
		 );
		$det_Jurnaltes[]  = array(
		  'nomor'         => '',
		  'tanggal'       => $tgl_voucher,
		  'tipe'          => 'JV',
		  'no_perkiraan'  => $datajurnal->coa_fg,
		  'keterangan'    => $Keterangan_INV,
		  'no_reff'       => $id,
		  'debet'         => $totaldebit,
		  'kredit'        => 0,
		  'jenis_jurnal'  => 'wip finishgood',
		   'no_request'    => $no_request
		 );

		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'wip finishgood');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'wip finishgood';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$data['auto_jurnal']	= $autoj;		
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_only_jurnal_product() {
		//WIP - FINISH GOOD
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
		$autoj = "viewonly";

		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'wip finishgood';
		$query 	= "SELECT jurnaltras.*, jurnaltras.no_perkiraan, coa_master.nama
		FROM jurnaltras
		left JOIN ".DBACC.".coa_master ON coa_master.no_perkiraan=jurnaltras.no_perkiraan
		WHERE jurnaltras.no_reff = '$id'
		AND jurnaltras.tipe = '$tipe'
		ORDER BY jurnaltras.debet DESC";
		$data['list_data'] 	    = $this->db->query($query)->result();
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= 0;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$data['auto_jurnal']	= $autoj;		
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
	function view_jurnal_intransit()
    {
		//JURNAL JV FINISH GOOD - TRANSIT

		$id	= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
		$no_so = $this->uri->segment(8);
		$product = $this->uri->segment(9);


		$no_request = $id;
        $kd_bayar    =$id;

// jurnal_product
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
		$Keterangan_INV	= rawurldecode($ket.' ('.$no_so.'-'.$product.'-'.$datajurnal->no_surat_jalan.')');
        $Tgl_Invoice = $datajurnal->tanggal;
		$tgl_voucher =$Tgl_Invoice;
		$dataproductiondetail=$this->db->query("select * from production_detail where id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1")->row();
		if($dataproductiondetail->finish_good==0){
			$datasodetailheader = $this->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
	//cek sudah ada wip apa belum
	// ambil kurs
			$kurs=1;
			$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
			$dtkurs	= $this->db->query($sqlkurs)->row();
			if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
	// update production_detail
			$wip_material=$datajurnal->total_nilai;
			$pe_direct_labour=($datasodetailheader->pe_direct_labour*$kurs);
			$foh=(($datasodetailheader->pe_machine + $datasodetailheader->pe_mould_mandrill + $datasodetailheader->pe_foh_depresiasi + $datasodetailheader->pe_biaya_rutin_bulanan + $datasodetailheader->pe_foh_consumable)*$kurs);
			$pe_indirect_labour=($datasodetailheader->pe_indirect_labour*$kurs);
			$pe_consumable=($datasodetailheader->pe_consumable*$kurs);
			$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
			
			$this->db->query("update production_detail set wip_kurs='".$kurs."'
			, wip_material='".$wip_material."'
			, wip_dl='".$pe_direct_labour."'
			, wip_foh='".$foh."'
			, wip_il='".$pe_indirect_labour."'
			, wip_consumable='".$pe_consumable."'
			, finish_good='".$finish_good."'
			WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );
			$totalall=$finish_good;
		}else{
			$totalall=$dataproductiondetail->finish_good;
		}

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);

		foreach($datajurnal AS $record){
			$nokir1  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			if ($field == 'jumlah_bank'){
				$nokir = $kd_bank;
			} else{
				$nokir  = $record->no_perkiraan;
			}
			$no_voucher = $kd_bayar;
			$param  = 'id';
			if ($posisi=='D'){
				$value_param  = $id;
				$val = $this->Acc_model->GetData($tabel,$field,$param,$value_param);
				$nilaibayar = $val[0]->$field;
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $kd_bayar,
				  'debet'         => $totalall,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'finish good intransit',
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$coa = 	$this->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.id ='$id'")->result();
				$nokir=$coa[0]->coa_fg;
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $kd_bayar,
				  'debet'         => 0,
				  'kredit'        => $totalall,
				  'jenis_jurnal'  => 'finish good intransit',
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'finish good intransit');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);

		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'finish good intransit';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = '';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $totalall;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_jurnal_incustomer() {
		//JURNAL JV TRANSIT - CUSTOMER
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);

		$no_request = $id;
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
        $Tgl_Invoice = $datajurnal->tanggal;
		$tgl_voucher =$Tgl_Invoice;
		$kd_bayar = $datajurnal->kode_trans;
		$Keterangan_INV		    = 'TRANSIT - CUSTOMER ('.$datajurnal->no_so.' - '.$datajurnal->product.'-'.$datajurnal->no_surat_jalan.') ';
// jurnal_product
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
		$totalall=$datajurnal->total_nilai;

		$dataproductiondetail=$this->db->query("select * from production_detail where id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1")->row();
		if($dataproductiondetail->finish_good==0){
		$datasodetailheader = $this->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
//cek sudah ada wip apa belum
// ambil kurs
		$kurs=1;
		$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs	= $this->db->query($sqlkurs)->row();
		if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
// update production_detail
		$wip_material=$datajurnal->total_nilai;
		$pe_direct_labour=($datasodetailheader->pe_direct_labour*$kurs);
		$foh=(($datasodetailheader->pe_machine + $datasodetailheader->pe_mould_mandrill + $datasodetailheader->pe_foh_depresiasi + $datasodetailheader->pe_biaya_rutin_bulanan + $datasodetailheader->pe_foh_consumable)*$kurs);
		$pe_indirect_labour=($datasodetailheader->pe_indirect_labour*$kurs);
		$pe_consumable=($datasodetailheader->pe_consumable*$kurs);
		$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
		
		$this->db->query("update production_detail set wip_kurs='".$kurs."'
		, wip_material='".$wip_material."'
		, wip_dl='".$pe_direct_labour."'
		, wip_foh='".$foh."'
		, wip_il='".$pe_indirect_labour."'
		, wip_consumable='".$pe_consumable."'
		, finish_good='".$finish_good."'
		WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );

		}else{
			$totalall=$dataproductiondetail->finish_good;
		}

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);

		foreach($datajurnal AS $record){
			$nokir1  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$no_voucher = $kd_bayar;
			$param  = 'id';
			$value_param  = $id;
			$val = $this->Acc_model->GetData($tabel,$field,$param,$value_param);
			$nilaibayar = $val[0]->$field;
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $totalall,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'intransit incustomer',
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalall,
				  'jenis_jurnal'  => 'intransit incustomer',
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'intransit incustomer');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'intransit incustomer';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $totalall;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_jurnal_incoming_stock() {
		//JURNAL JV INCOMING STOCK

		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
		$kd_bayar = $this->uri->segment(9);
        $Tgl_Invoice = $this->uri->segment(8);//date('Y-m-d');
		$no_request = $id;
		$tgl_voucher =$Tgl_Invoice;
		$Keterangan_INV		    = 'INCOMING STOCK '.($kd_bayar);

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);

		foreach($datajurnal AS $record){
			$nokir1  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$no_voucher = $kd_bayar;
			$param  = 'id';
			$value_param  = $id;
			$jenisjurnal = 'incoming stok';
			$val = $this->db->query("select sum(total_nilai) as nilaibayar from jurnal where kode_trans='".$kd_bayar."'")->row();
			$nilaibayar = $val->nilaibayar;
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $nilaibayar,
				  'kredit'        => 0,
				  'jenis_jurnal'  => $jenisjurnal,
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $nilaibayar,
				  'jenis_jurnal'  => $jenisjurnal,
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', $jenisjurnal);
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_jurnal_incoming_department() {
		//JURNAL JV INCOMING DEPARTMENT

		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
		$kd_bayar = $this->uri->segment(9);
		$no_po = $this->uri->segment(10);
        $Tgl_Invoice = $this->uri->segment(8);//date('Y-m-d');
		$no_request = $id;
		$tgl_voucher =$Tgl_Invoice;
		$Keterangan_INV		    = 'INCOMING DEPARTMENT '.($kd_bayar) . ' '.$no_po;

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
		$nilaibayar=0;
		$totalbayar=0;
		foreach($datajurnal AS $record){
			$nokir1  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$no_voucher = $kd_bayar;
			$param  = 'id';
			$value_param  = $id;
			$jenisjurnal = 'incoming department';
			if ($posisi=='D'){
				$val = $this->db->query("select a.total_nilai,a.id_material,a.nm_material, c.coa from jurnal a left join rutin_non_planning_detail b on a.id_material=b.id left join rutin_non_planning_header c on b.no_pr=c.no_pr where a.kode_trans='".$kd_bayar."'")->result();
				foreach($val AS $rec){
					$nilaibayar = $rec->total_nilai;
					$totalbayar=($totalbayar+$nilaibayar);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $rec->coa,
					  'keterangan'    => $rec->nm_material.' '.$kd_bayar.', '.$no_po,
					  'no_reff'       => $id,
					  'debet'         => $nilaibayar,
					  'kredit'        => 0,
					  'jenis_jurnal'  => $jenisjurnal,
					  'no_request'    => $no_request
					 );
				}
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalbayar,
				  'jenis_jurnal'  => $jenisjurnal,
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', $jenisjurnal);
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}

    function view_jurnal_incoming_asset() {
		//JURNAL JV INCOMING DEPARTMENT

	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
		$kd_bayar = $this->uri->segment(9);
        $Tgl_Invoice = $this->uri->segment(8);
		$id		= $this->uri->segment(9);
		$no_request = $this->uri->segment(3);
		$tgl_voucher =$Tgl_Invoice;
		$Keterangan_INV		    = 'INCOMING ASSETS '.($kd_bayar);

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
		$nilaibayar=0;
		$totalbayar=0;
		foreach($datajurnal AS $record){
			$nokir1  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$no_voucher = $kd_bayar;
			$param  = 'id';
			$value_param  = $id;
			$jenisjurnal = 'incoming asset';
			if ($posisi=='D'){
				$val = $this->db->query("select a.total_nilai,a.id_material,a.nm_material, b.coa from jurnal a left join asset_planning b on a.id_material=b.code_plan where a.kode_trans='".$kd_bayar."'")->result();
				foreach($val AS $rec){
					$nilaibayar = $rec->total_nilai;
					$totalbayar=($totalbayar+$nilaibayar);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $rec->coa,
					  'keterangan'    => $rec->nm_material.' '.$kd_bayar,
					  'no_reff'       => $id,
					  'debet'         => $nilaibayar,
					  'kredit'        => 0,
					  'jenis_jurnal'  => $jenisjurnal,
					  'no_request'    => $no_request
					 );
				}
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalbayar,
				  'jenis_jurnal'  => $jenisjurnal,
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', $jenisjurnal);
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}	
    function view_jurnal_aset_depreciation() {
		//JURNAL JV ASSETS DEPRECIATION

		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
        $nilaibayar = $this->uri->segment(7);
        $TANGGAL = $this->uri->segment(8);
		$idjurnal = $this->uri->segment(9);
		$no_request = $id;
		$jenisjurnal = 'assets';
		$det_Jurnaltes=array();
		
		$coa_category = $this->db->query("select * from jurnal where id='".$idjurnal."'")->result();
		if($coa_category){
			foreach($coa_category as $rec){
				$coa_data = $this->db->query("select * from asset_coa where id='".$rec->id_detail."'")->row();
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $TANGGAL,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $coa_data->coa,
				  'keterangan'    => $coa_data->keterangan,
				  'no_reff'       => $idjurnal,
				  'debet'         => $rec->total_nilai,
				  'kredit'        => 0,
				  'jenis_jurnal'  => $jenisjurnal,
				  'no_request'    => $no_request
				 );

				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $TANGGAL,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $coa_data->coa_kredit,
				  'keterangan'    => $coa_data->keterangan,
				  'no_reff'       => $idjurnal,
				  'debet'         => 0,
				  'kredit'        => $rec->total_nilai,
				  'jenis_jurnal'  => $jenisjurnal,
				  'no_request'    => $no_request
				 );

			}
		}

/*
		$coa_category = $this->db->query("select * from asset_category where id='".$id."' and deleted='N'")->result();
		$totalall=0;
		if($coa_category){
			foreach($coa_category as $rec){
				$array_debit=explode(";",$rec->coa_debit);
				foreach ($array_debit as $coa_rec){
					$array_coa=explode("/",$coa_rec);
					$totalrow=floor($nilaibayar*$array_coa[1]/100);
					$coa_data=$this->db->query("select * from ".DBACC.".coa_master where no_perkiraan='".$array_coa[0]."'")->row();
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $TANGGAL,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_data->no_perkiraan,
					  'keterangan'    => $coa_data->nama,
					  'no_reff'       => $idjurnal,
					  'debet'         => $totalrow,
					  'kredit'        => 0,
					  'jenis_jurnal'  => $jenisjurnal,
					  'no_request'    => $no_request
					 );
					 $totalall=($totalall+$totalrow);
				}
			}
		}else{
			$det_Jurnaltes[]  = array(
			  'nomor'         => '',
			  'tanggal'       => $TANGGAL,
			  'tipe'          => 'JV',
			  'no_perkiraan'  => '0000-00-00',
			  'keterangan'    => "BELUM DI SETTING",
			  'no_reff'       => $idjurnal,
			  'debet'         => $totalrow,
			  'kredit'        => 0,
			  'jenis_jurnal'  => $jenisjurnal,
			  'no_request'    => $no_request
			 );
			$totalall=($totalall+$totalrow);			 
		}
		if($coa_category){
			foreach($coa_category as $rec){
				$array_kredit=explode(";",$rec->coa_kredit);
				foreach ($array_kredit as $coa_rec){
					$array_coa=explode("/",$coa_rec);
					$coa_data=$this->db->query("select * from ".DBACC.".coa_master where no_perkiraan='".$array_coa[0]."'")->row();
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $TANGGAL,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_data->no_perkiraan,
					  'keterangan'    => $coa_data->nama,
					  'no_reff'       => $idjurnal,
					  'debet'         => 0,
					  'kredit'        => floor($totalall*$array_coa[1]/100),
					  'jenis_jurnal'  => $jenisjurnal,
					  'no_request'    => $no_request
					 );
				}
			}
		}else{
			$det_Jurnaltes[]  = array(
			  'nomor'         => '',
			  'tanggal'       => $TANGGAL,
			  'tipe'          => 'JV',
			  'no_perkiraan'  => '0000-00-00',
			  'keterangan'    => "BELUM DI SETTING",
			  'no_reff'       => $idjurnal,
			  'debet'         => 0,
			  'kredit'        => $totalall,
			  'jenis_jurnal'  => $jenisjurnal,
			  'no_request'    => $no_request
			 );	
		}
*/
		if ($det_Jurnaltes) {
			$this->db->where('no_reff', $idjurnal);
			$this->db->where('jenis_jurnal', $jenisjurnal);
			$this->db->delete('jurnaltras');
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		}
		$noreff     = $idjurnal;
		$tipe		= 'JV';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $idjurnal;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_jurnal_outgoing_stock() {
		//JURNAL JV OUTGOING STOK

		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);
		$kd_bayar = $id	;
		$no_request = $id;
		$tgl_voucher = $this->uri->segment(9);
		$autoj = $this->uri->segment(11);
		$Keterangan_INV	= 'OUTGOING STOCK '.($kd_bayar);

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
		$nilaibayar=0;
		$totalbayar=0;
		$sql="SELECT * FROM warehouse_adjustment where kode_trans='".$kd_bayar."'";
		$wh=$this->db->query($sql)->row();
		$kode_gudang = $wh->id_gudang_ke;
		$coa_deffered='';		
		if($kode_gudang=='17'){
			$sql_deff="select c.nm_customer, c.coa_deffered from so_number a 
			left join table_sales_order b on a.id_bq=b.id_bq 
			left join customer c on b.id_customer=c.id_customer
			where a.so_number='".$wh->no_so."'";
			$dt_coa=$this->db->query($sql_deff)->row();
			if(!empty($dt_coa)) {
				$coa_deffered=$dt_coa->coa_deffered;
			}
			if($coa_deffered=="") {
				$sql_deff="select coa_biaya from costcenter where id='".$wh->id_gudang_ke."'";
				$dt_coa=$this->db->query($sql_deff)->row();
				$coa_deffered=$dt_coa->coa_biaya;
			}
		}
		foreach($datajurnal AS $record){
			$nokir1  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$no_voucher = $kd_bayar;
			$param  = 'id';
			$value_param  = $id;
			$jenisjurnal = 'outgoing stok';
			if ($posisi=='D'){
				if($kode_gudang=='17'){
					$val = $this->db->query("select ROUND(a.total_nilai) total_nilai,a.id_material,a.nm_material, a.gudang_ke, '".$coa_deffered."' coa_biaya from jurnal a  where a.kode_trans='".$kd_bayar."'")->result();
				}else{
					$val = $this->db->query("select ROUND(a.total_nilai) total_nilai,a.id_material,a.nm_material, a.gudang_ke, b.category_awal, c.coa_biaya from jurnal a left join con_nonmat_new b on a.id_material=b.code_group left join con_nonmat_category_costcenter c on a.gudang_ke=c.costcenter and b.category_awal=c.category where a.kode_trans='".$kd_bayar."'")->result();
				}
				foreach($val AS $rec){
					$nilaibayar = $rec->total_nilai;
					$totalbayar=($totalbayar+$nilaibayar);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $rec->coa_biaya,
					  'keterangan'    => $rec->nm_material.' '.$kd_bayar,
					  'no_reff'       => $id,
					  'debet'         => $nilaibayar,
					  'kredit'        => 0,
					  'jenis_jurnal'  => $jenisjurnal,
					  'no_request'    => $no_request
					 );
				}
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalbayar,
				  'jenis_jurnal'  => $jenisjurnal,
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', $jenisjurnal);
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $nilaibayar;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$data['auto_jurnal']	= $autoj;
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
	function view_jurnal_tras_finish ($id='',$tipe='',$jenisjurnal=''){
		$id		= $this->uri->segment(3);
		$tipe		= 'JV';
		$jenisjurnal = 'outgoing stok';
		$noreff     = $id;
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$this->load->view("Jurnal_tras/view_detail_jurnaltras", $data);
	}
    function view_jurnal_fgspooling() {
		//JURNAL JV FINISH GOOD - SPOOLING WIP
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);

		$no_request = $id;
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
        $Tgl_Invoice = $datajurnal->tanggal;
		$tgl_voucher =$Tgl_Invoice;
		$kd_bayar = $datajurnal->kode_trans;
		$Keterangan_INV		    = 'FINISH GOOD - SPOOLING WIP ('.$datajurnal->no_so.' - '.$datajurnal->product;
// jurnal_product
		$totalall=$datajurnal->total_nilai;

		$dataproductiondetail=$this->db->query("select * from production_detail where id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1")->row();
		if($dataproductiondetail->finish_good==0){
			$datasodetailheader = $this->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
	//cek sudah ada wip apa belum
	// ambil kurs
			$kurs=1;
			$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
			$dtkurs	= $this->db->query($sqlkurs)->row();
			if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
	// update production_detail
			$wip_material=$datajurnal->total_nilai;
			$pe_direct_labour=($datasodetailheader->pe_direct_labour*$kurs);
			$foh=(($datasodetailheader->pe_machine + $datasodetailheader->pe_mould_mandrill + $datasodetailheader->pe_foh_depresiasi + $datasodetailheader->pe_biaya_rutin_bulanan + $datasodetailheader->pe_foh_consumable)*$kurs);
			$pe_indirect_labour=($datasodetailheader->pe_indirect_labour*$kurs);
			$pe_consumable=($datasodetailheader->pe_consumable*$kurs);
			$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
			
			$this->db->query("update production_detail set wip_kurs='".$kurs."'
			, wip_material='".$wip_material."'
			, wip_dl='".$pe_direct_labour."'
			, wip_foh='".$foh."'
			, wip_il='".$pe_indirect_labour."'
			, wip_consumable='".$pe_consumable."'
			, finish_good='".$finish_good."'
			WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );

		}

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);

		foreach($datajurnal AS $record){
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$nilaibayar = $totalall;
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $totalall,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'finishgood spooling',
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalall,
				  'jenis_jurnal'  => 'finishgood spooling',
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'finishgood spooling');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'finishgood spooling';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $totalall;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_jurnal_spoolingfg() {
		//JURNAL JV SPOOLING WIP - FINISH GOOD
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);

		$no_request = $id;
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
        $Tgl_Invoice = $datajurnal->tanggal;
		$tgl_voucher =$Tgl_Invoice;
		$kd_bayar = $datajurnal->kode_trans;
		$Keterangan_INV		    = 'SPOOLING WIP - FINISH GOOD ('.$datajurnal->no_so.' - '.$datajurnal->product;
// jurnal_product
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
		$totalall=$datajurnal->total_nilai;

		$dataproductiondetail=$this->db->query("select * from production_detail where id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1")->row();
		if($dataproductiondetail->finish_good==0){
			$datasodetailheader = $this->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
	//cek sudah ada wip apa belum
	// ambil kurs
			$kurs=1;
			$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
			$dtkurs	= $this->db->query($sqlkurs)->row();
			if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
	// update production_detail
			$wip_material=$datajurnal->total_nilai;
			$pe_direct_labour=($datasodetailheader->pe_direct_labour*$kurs);
			$foh=(($datasodetailheader->pe_machine + $datasodetailheader->pe_mould_mandrill + $datasodetailheader->pe_foh_depresiasi + $datasodetailheader->pe_biaya_rutin_bulanan + $datasodetailheader->pe_foh_consumable)*$kurs);
			$pe_indirect_labour=($datasodetailheader->pe_indirect_labour*$kurs);
			$pe_consumable=($datasodetailheader->pe_consumable*$kurs);
			$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
			
			$this->db->query("update production_detail set wip_kurs='".$kurs."'
			, wip_material='".$wip_material."'
			, wip_dl='".$pe_direct_labour."'
			, wip_foh='".$foh."'
			, wip_il='".$pe_indirect_labour."'
			, wip_consumable='".$pe_consumable."'
			, finish_good='".$finish_good."'
			WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );

		}

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
		$jenisjurnal = 'spooling finishgood';

		foreach($datajurnal AS $record){
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$nilaibayar = $totalall;
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $totalall,
				  'kredit'        => 0,
				  'jenis_jurnal'  => $jenisjurnal,
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalall,
				  'jenis_jurnal'  => $jenisjurnal,
				   'no_request'   => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'spooling finishgood');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $totalall;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_jurnal_fgcutting() {
		//JURNAL JV FINISH GOOD - CUTTING WIP
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);

		$no_request = $id;
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
        $Tgl_Invoice = $datajurnal->tanggal;
		$tgl_voucher =$Tgl_Invoice;
		$kd_bayar = $datajurnal->kode_trans;
		$Keterangan_INV		    = 'FINISH GOOD - CUTTING WIP ('.$datajurnal->no_so.' - '.$datajurnal->product.')';
// jurnal_product
		$totalall=$datajurnal->total_nilai;

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);

		foreach($datajurnal AS $record){
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$nilaibayar = $totalall;
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $totalall,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'finishgood cutting',
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalall,
				  'jenis_jurnal'  => 'finishgood cutting',
				   'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'finishgood cutting');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'finishgood cutting';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $totalall;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}
    function view_jurnal_cuttingfg() {
		//JURNAL JV CUTTING WIP  - FINISH GOOD
		$id		= $this->uri->segment(3);
	   	$kodejurnal = $this->uri->segment(4);
		$ket = $this->uri->segment(5);

		$no_request = $id;
		$datajurnal = $this->db->query("SELECT a.* FROM jurnal_product a WHERE a.id ='$id' limit 1" )->row();
        $Tgl_Invoice = $datajurnal->tanggal;
		$tgl_voucher =$Tgl_Invoice;
		$kd_bayar = $datajurnal->kode_trans;
		$Keterangan_INV		    = 'CUTTING WIP - FINISH GOOD ('.$datajurnal->no_so.' - '.$datajurnal->product.')';
// jurnal_product
		$totalall=$datajurnal->total_nilai;

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);

		foreach($datajurnal AS $record){
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			$nokir  = $record->no_perkiraan;
			$nilaibayar = $totalall;
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $totalall,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'cutting finishgood',
				  'no_request'    => $no_request
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalall,
				  'jenis_jurnal'  => 'cutting finishgood',
				  'no_request'    => $no_request
				 );
			}
		}
		$this->db->where('no_reff', $id);
		$this->db->where('jenis_jurnal', 'finishgood cutting');
		$this->db->delete('jurnaltras');
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
		$noreff     = $id;
		$tipe		= 'JV';
		$jenisjurnal = 'finishgood cutting';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = 'JV';
		$data['akses']	        = 'jurnal';
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $id;
		$data['total_po']		= $totalall;
		$data['id_vendor']		= '';
		$data['nama_vendor']	= '';
		$this->load->view("Jurnal_tras/v_detail_jurnal", $data);
	}



	function jurnalWIPtanki(){
		
		//$idtrans       = $this->uri->segment(3);
		//$data_session	= $this->session->userdata;
		//$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		
		
	
		    $kodejurnal='JV004';
		  	$dataspool = $this->db->query("select * from data_jurnal_wip_tanki_erp")->result();
			foreach($dataspool AS $record){
		    $idtrans = $record->kode_trans;

			$wip = $this->db->query("SELECT * FROM data_erp_wip WHERE id_trans ='".$idtrans."'")->result();
			
			$totalwip =0;
			$wiptotal =0; 
			$det_Jurnaltes = [];
			  
			foreach($wip AS $data){
				
				$nm_material = $data->nm_material;	
				$product 	 = $data->product;	
				$tgl_voucher = $data->tanggal;	
				$keterangan  = $data->nm_material;
				$ket         = 'produksi wip tanki';
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
				
				
				
				
				/*if($nm_material=='WIP Total'){	
    				if($product=='pipe'){
						$nokir ='1103-03-02';	
					}else{
						$nokir ='1103-03-03';	
					}					
				}	*/

			    $debit  = $totalwip;			
				
				if($totalwip != 0 ){
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => '1103-03-03',
					  'keterangan'    => $keterangan.$ket,
					  'no_reff'       => $id,
					  'debet'         => $wiptotal,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'produksi wip tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					   );
					
				}else{
								
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $keterangan.$ket,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $kredit,
					  'jenis_jurnal'  => 'produksi wip tanki',
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
			$Keterangan_INV = 'Jurnal Produksi - WIP Tanki';
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalwip, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => '11', 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
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
		
	}

	function jurnalIntransit(){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
		$dataspool = $this->db->query("select * from data_jurnal_intransit_erp_spool")->result();
			foreach($dataspool AS $record){
		    $idtrans = $record->kode_trans;
	       
		   
			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,kode_trans, nilai_unit as finishgood  FROM data_erp_fg_spool WHERE id =$idtrans")->result();
			
				
			
			$totalfg =0;
			  
			$det_Jurnaltes = [];
			  
			foreach($wip AS $data){
				
				$nm_material = $data->product;	
				$tgl_voucher = $data->tanggal;	
				$spasi       = ',';
				$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
				$id          = $data->kode_trans;
               	$no_request  = $data->no_spk;	
				
				
				$finishgood    	= $data->finishgood;
				
				
				
				
				if ($nm_material=='pipe'){			
				$coa_wip 		='1103-03-02';	
				}else{
				$coa_wip 		='1103-03-03';						
				}					
			    				
				$coaintransit		='1103-04-06';
				$coafg   		    ='1103-04-01';
                				
				
								
				    			 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coaintransit,
					  'keterangan'    => 'FINISHED GOOD - INTRANSIT',
					  'no_reff'       => $id,
					  'debet'         => $finishgood,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coafg,
					  'keterangan'    => 'FINISHED GOOD - INTRANSIT',
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $finishgood,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					  	
				
				
			}
			
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='Finishgood-Intransit' and no_reff ='$id' AND tanggal ='".$tgl_voucher."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'FINISHED GOOD - INTRANSIT'.$keterangan;
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $finishgood, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$this->db->insert(DBACC.'.javh',$dataJVhead);
			$datadetail=array();
			foreach ($det_Jurnaltes as $vals) {
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tgl_voucher,
					'no_perkiraan'	=> $vals['no_perkiraan'],
					'keterangan'	=> $Keterangan_INV,
					'no_reff'		=> $vals['no_reff'],
					'debet'			=> $vals['debet'],
					'kredit'		=> $vals['kredit'],
					'created_on'		=> date('Y-m-d H:i:s'),
					'created_by'		=> 'intransit',
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
			
			}
		  
		}



		//SYAMSUDIN 20/03/2024

	function jurnalIntransitCustomer(){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
		$dataspool = $this->db->query("select * from data_jurnal_incustomer_erp_spool")->result();
		foreach($dataspool AS $record){
		$idtrans = $record->kode_trans;
		
		   
			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,kode_trans, nilai_unit as finishgood  FROM data_erp_in_customer_spool WHERE  id=$idtrans")->result();
			
			$totalfg =0;
			
			  
			$det_Jurnaltes = [];
			  
			foreach($wip AS $data){
				
				$nm_material = $data->product;	
				$tgl_voucher = $data->tanggal;	
				$spasi       = ',';
				$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
				$id          = $data->kode_trans;
               	$no_request  = $data->no_spk;	
				
				
				$finishgood    	= $data->finishgood;
				
				
				
				
				if ($nm_material=='pipe'){			
				$coa_wip 		='1103-03-02';	
				}else{
				$coa_wip 		='1103-03-03';						
				}					
			    				
				$coaintransit		='1103-04-06';
				$coacustomer   		    ='1103-04-07';
                				
				
								
				    			 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coacustomer,
					  'keterangan'    => 'INTRANSIT-CUSTOMER',
					  'no_reff'       => $id,
					  'debet'         => $finishgood,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coaintransit,
					  'keterangan'    => 'INTRANSIT-CUSTOMER',
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $finishgood,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					  	
				
				
			}
			
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='Finishgood-Intransit' and no_reff ='$id' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'INTRANSIT-CUSTOMER'.$keterangan;
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $finishgood, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$this->db->insert(DBACC.'.javh',$dataJVhead);
			$datadetail=array();
			foreach ($det_Jurnaltes as $vals) {
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tgl_voucher,
					'no_perkiraan'	=> $vals['no_perkiraan'],
					'keterangan'	=> $Keterangan_INV,
					'no_reff'		=> $vals['no_reff'],
					'debet'			=> $vals['debet'],
					'kredit'		=> $vals['kredit'],
					'created_on'		=> date('Y-m-d H:i:s'),
					'created_by'		=> 'customer'
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
			
		}
		  
	}

	function jurnalFGtanki(){
		
		$data_session	= $this->session->userdata;
		//$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	        $dataspool = $this->db->query("select * from data_jurnal_fg_tanki_erp_baru")->result();
			foreach($dataspool AS $record){
		    $idtrans = $record->kode_trans;
		   
			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg_tanki_baru WHERE id_trans ='".$idtrans."' AND jenis ='in' AND created_by='manual system 2' AND tanggal < '2024-09-01' ")->result();
			
			$totalfg =0;
			  
			$det_Jurnaltes = [];
			
			
			foreach($wip AS $data){
				
				$nm_material = $data->product;	
				$tgl_voucher = $data->tanggal;	
				$spasi       = ',Tanki';
				$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
				$FG = 'FINISHED GOOD';
				$cog = 'COGS';
				$finish = $FG.$keterangan;
				$coges = $cog.$keterangan;
				
				$id          = $data->id_trans;
               	$no_request  = $data->no_spk;	
				
				$wip           	= $data->wip;
				$material      	= $data->material;
				$wip_direct    	= $data->wip_direct;
				$wip_indirect  	= $data->wip_indirect;
				$wip_foh       	= $data->wip_foh;
				$wip_consumable = $data->wip_consumable;
				$finishgood    	= $data->finishgood;
				$cogs          	= $material+$wip_direct+$wip_indirect+$wip_foh+$wip_consumable;
				
				$totalfg        = $cogs;
				
				
				
				if ($nm_material=='pipe'){			
				$coa_wip 		='1103-03-02';	
				}else{
				$coa_wip 		='1103-03-03';						
				}					
			    $debit  		= $totalfg;	
				
				$coa_material	='5101-01-01';
				$coa_direct 	='5101-03-01';
				$coa_indirect 	='5101-04-01';
				$coa_foh 		='5101-05-01';
				$coa_consumable ='5101-02-01';
				
				$coacogs 		='5103-01-01';
				$coafg   		='1103-04-01';
                				
				
								
				     $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_material,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => $material,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					  $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_direct,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => $wip_direct,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					  $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_indirect,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => $wip_indirect,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_foh,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => $wip_foh,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_consumable,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => $wip_consumable,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $cogs,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coafg,
					  'keterangan'    => $finish,
					  'no_reff'       => $id,
					  'debet'         => $cogs,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coacogs,
					  'keterangan'    => $coges,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $cogs,
					  'jenis_jurnal'  => 'WIP-Finishgood tanki',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					  	
				
				
			}
			
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='wip finishgood tanki' and no_reff ='$idtrans' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'WIP-Finishgood tanki'.$keterangan;
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalfg, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'Manual System', 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
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
		
	}


	function jurnalCogs(){
		
		$data_session	= $this->session->userdata;
		//$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	        $dataspool = $this->db->query("select * from data_jurnal_cogs")->result();
			foreach($dataspool AS $record){
		    $idtrans = $record->id;
		   
			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_cogs WHERE id ='".$idtrans."' ")->result();
			
			$totalfg =0;
			  
			$det_Jurnaltes = [];
			
			
			foreach($wip AS $data){
				
				$nm_material = $data->no_so;	
				$tgl_voucher = $data->tanggal;	
				$spasi       = ' ';
				$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi; 
				$FG = 'FINISHED GOOD';
				$cog = 'adjust cogs';
				$finish = $FG.$keterangan;
				$coges = $cog.$keterangan;
				
				$id          = $data->id;
               	$no_request  = $data->id;
				
				$nilaiwip       = $data->nilai_wip;
				$cogs          	= $nilaiwip;
				
				$totalfg        = $cogs;
				
				
				
				if ($nm_material=='FG'){			
				$coa_fg 		='1103-03-02';	
				}else{
				$coa_fg 		='1103-04-07';						
				}					
			    $debit  		= $totalfg;	
				
				$coacogs 		='5104-01-01';
                				
				
								
				     $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coacogs,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => $nilaiwip,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'adjust cogs',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					  $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_fg,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => $nilaiwip,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'adjust cogs',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );			
					  	
				
				
			}
			
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='adjust cogs' and no_reff ='$idtrans'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'Adjust cogs'.$keterangan;
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalfg, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'Manual System', 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
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
					'created_on'		=> $DateTime,
					'created_by'		=> 'syam'
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
		  
		}
		
	}
}
