<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penghapusan_piutang extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('penghapusan_piutang_model');
		$this->load->model('Acc_model');
		$this->load->model('Jurnal_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	//=========================================================================================================================
	//==================================================PLAN TAGIH=============================================================
	//=========================================================================================================================

	public function create_new(){
		$this->penghapusan_piutang_model->create_new();
	}

	public function server_side_create_new(){
		$this->penghapusan_piutang_model->get_data_json_create_new();
	}

	public function create_invoice(){
		$this->penghapusan_piutang_model->create_invoice($this->uri->segment(3));
	}

	public function save_invoice(){
		$this->penghapusan_piutang_model->save_invoice();
	}

	//=========================================================================================================================
	//====================================================PIUTANG==============================================================
	//=========================================================================================================================

	public function index(){
		$this->penghapusan_piutang_model->list_inv();
	}

	public function server_side_inv(){
		$this->penghapusan_piutang_model->get_data_json_inv();
	}

	public function modal_detail_invoice(){
		$this->penghapusan_piutang_model->modal_detail_invoice();
	}

	//PRINT SAVE JURNAL
	public function print_invoice_old(){
/*		
		$db2 			= $this->load->database('accounting', TRUE);
		$data_session 	= $this->session->userdata;
		$id   			= $this->uri->segment(3);
		$nomordoc 		= get_name('tr_invoice_header','no_invoice','id_invoice',$id);

		$gethd 			= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice='$nomordoc'")->row();
		$tgl       		= $gethd->tgl_invoice;
		$Jml_Ttl   		= $gethd->total_invoice_idr;
		$Id_klien     	= $gethd->id_customer;
		$Nama_klien   	= $gethd->nm_customer;
		$jenis_invoice  = $gethd->jenis_invoice;
		$Bln 			= substr($tgl,5,2);
		$Thn 			= substr($tgl,0,4);
		$tot_retensi    = $gethd->total_retensi_idr;
		$tot_um         = $gethd->total_um_idr;

		$update_invoice	= "UPDATE tr_invoice_header SET proses_print= '1' WHERE no_invoice='$nomordoc'";
		$update         = $this->db->query($update_invoice);

		if($jenis_invoice=='progress'){
			if($tot_um > 0 && $tot_retensi > 0 )   {
				$kodejurnal1	='JV006';
			}
			elseif($tot_um > 0 && $tot_retensi < 1){
				$kodejurnal1	='JV004';
			}
			elseif($tot_um < 1 && $tot_retensi > 0){
				$kodejurnal1	='JV005';
			}
			else{
				$kodejurnal1	='JV001';
			}
			$Keterangan_INV1	= 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
		}
		elseif($jenis_invoice=='uang muka'){
			if($tot_retensi > 0 )   {
				$kodejurnal1	='JV008';
			}
			else{
				$kodejurnal1	='JV002';
			}
			$Keterangan_INV1	= 'UANG MUKA PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
		}
		elseif($jenis_invoice=='retensi'){
			$kodejurnal1	 	='JV003';
			$Keterangan_INV1	= 'RETENSI PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
		}


		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_Sales('101',$tgl);
		$Keterangan_INV		    = 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tgl,
			'jml'	            => $Jml_Ttl,
			'koreksi_no'		=> '-',
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan' 		=> $Keterangan_INV1,
			'bulan'				=> $Bln,
			'tahun'				=> $Thn,
			'user_id'			=> $data_session['ORI_User']['username'],
			'memo'			    => '',
			'tgl_jvkoreksi'	    => $tgl,
			'ho_valid'			=> ''
		);

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal1  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal1);

		foreach($datajurnal1 AS $rec){
			$tabel1  		= $rec->menu;
			$posisi1 		= $rec->posisi;
			$field1  		= $rec->field;
			$param1  		= 'no_invoice';
			$value_param1  	= $nomordoc;
			$val1 			= $this->Acc_model->GetData($tabel1,$field1,$param1,$value_param1);
			$nilaibayar1 	= $val1[0]->$field1;

			// if ($field1 == 'request_payment'){ //full
			// $nokir1 =$coa_aset;
			// }
			// else {
			$nokir1  = $rec->no_perkiraan;
			// }

			if ($posisi1=='D'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $nilaibayar1,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
			}
			elseif ($posisi1=='K'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $nilaibayar1
					//'jenis_jurnal'  => 'invoicing'
				);
			}

		}

		// print_r($datajurnal1);
		// exit;
		$this->db->trans_begin();
			$db2->insert('javh',$dataJVhead);
			$db2->insert_batch('jurnal',$det_Jurnaltes1);

			$datapiutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl,
				//'no_perkiraan'    => $coa_penjualan,
				'no_perkiraan'  => '1102-01-01',
				'keterangan'    => $Keterangan_INV1,
				'no_reff'       => $nomordoc,
				'debet'         => $Jml_Ttl,
				'kredit'        =>  0,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);

			$this->db->insert('tr_kartu_piutang',$datapiutang);
			$Qry_Update_Cabang_acc	 = "UPDATE pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
			$db2->query($Qry_Update_Cabang_acc);
			$this->print_invoice_fix();


		if($this->db->trans_status() === FALSE){
		 $this->db->trans_rollback();
		 $Arr_Return		= array(
				'status'		=> 2,
				'pesan'			=> 'Error Process Failed. Please Try Again...'
		   );
		}else{
		$this->db->trans_commit();



		 // $Arr_Return		= array(
			// 'status'		=> 1,
			// 'pesan'			=> 'Cancel Process Success. Thank You & Have A Nice Day...'
			// );
		}
		// echo json_encode($Arr_Return);
*/
	}

	//PRINT SAVE JURNAL
	public function print_invoice(){
		$db2 			= $this->load->database('accounting', TRUE);
		$data_session 	= $this->session->userdata;
		$id   			= $this->uri->segment(3);
		$nomordoc 		= get_name('tr_invoice_header','no_invoice','id_invoice',$id);

		$gethd 			= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice='$nomordoc'")->row();
		$tgl       		= $gethd->tgl_invoice;
		$Jml_Ttl   		= $gethd->total_invoice_idr;
		$Id_klien     	= $gethd->id_customer;
		$Nama_klien   	= $gethd->nm_customer;
		$jenis_invoice  = $gethd->jenis_invoice;
		$Bln 			= substr($tgl,5,2);
		$Thn 			= substr($tgl,0,4);
		$tot_retensi    = $gethd->total_retensi_idr;
		$tot_um         = $gethd->total_um_idr;
		$isppn			= $gethd->total_ppn_idr;
		$total_retensi2_idr	= $gethd->total_retensi2_idr;

		$this->db->trans_begin();
		$db2->trans_begin();
		$update_invoice	= "UPDATE tr_invoice_header SET proses_print= '1' WHERE no_invoice='$nomordoc'";
		$update         = $this->db->query($update_invoice);

		$dt_no_ipp 	= explode(",",$gethd->no_ipp);
		if($jenis_invoice=='progress'){
			$kodejurnal1		= 'JV051';
			$Keterangan_INV1	= 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				$get_billing_so = $this->db->query("SELECT * FROM billing_so WHERE no_ipp='$keys'")->row();
				$getdtlinv = $this->db->query("SELECT sum(harga_total_idr) as total_dpp_rp FROM tr_invoice_detail WHERE id_penagihan='".$gethd->id_penagihan."' and no_ipp='$keys'")->row();
				$total_dpp_rp=0;
				$persentase=0;
				if(!empty($getdtlinv)){
					$total_dpp_rp=$getdtlinv->total_dpp_rp;
					if($total_dpp_rp=="") $total_dpp_rp=0;
					if($get_billing_so->total_deal_idr>0){
						$persentase=round(($total_dpp_rp/$get_billing_so->total_deal_idr*100),2);
						$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$persentase."), total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					}
				}
			}
		}
		elseif($jenis_invoice=='uang muka'){
			$kodejurnal1		= 'JV050';
			$Keterangan_INV1	= 'UANG MUKA PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$gethd->persentase."), total_invoice=(total_invoice+".$gethd->total_dpp_rp.") WHERE no_ipp='$keys'");
			}
		}
		elseif($jenis_invoice=='retensi'){
			if($isppn>0){
				$kodejurnal1	= 'JV052';
			}else{
				$kodejurnal1	= 'JV054';
			}
			$Keterangan_INV1	= 'RETENSI PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;
			foreach ($dt_no_ipp as $keys){
				$get_billing_so = $this->db->query("SELECT * FROM billing_so WHERE no_ipp='$keys'")->row();
				$getdtlinv = $this->db->query("SELECT sum(harga_total_idr) as total_dpp_rp FROM tr_invoice_detail WHERE id_penagihan='".$gethd->id_penagihan."' and no_ipp='$keys'")->row();
				$total_dpp_rp=0;
				$persentase=0;
				if(!empty($getdtlinv)){
					$total_dpp_rp=$getdtlinv->total_dpp_rp;
					if($total_dpp_rp=="") $total_dpp_rp=0;
					if($get_billing_so->total_deal_idr>0){
						$persentase=round(($total_dpp_rp/$get_billing_so->total_deal_idr*100),2);
						$this->db->query("update billing_so set percent_invoice=(percent_invoice+".$persentase."), total_invoice=(total_invoice+".$total_dpp_rp.") WHERE no_ipp='$keys'");
					}
				}
			}
		}
//		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_Sales('101',$tgl);
		$Nomor_JV				= get_generate_jurnal('GJ',date('y-m-d'));
		$Keterangan_INV		    = 'PENJUALAN A/N '.$Nama_klien.' INV NO. '.$nomordoc;

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

		$datajurnal1  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal1);
		$nilaitotaljurnal=0;
		foreach($datajurnal1 AS $rec){
			$tabel1  		= $rec->menu;
			$posisi1 		= $rec->posisi;
			$field1  		= $rec->field;
			$param1  		= 'no_invoice';
			$value_param1  	= $nomordoc;
			$val1 			= $this->Acc_model->GetData($tabel1,$field1,$param1,$value_param1);
			$nilaibayar1 	= $val1[0]->$field1;
			$nokir1  = $rec->no_perkiraan;
			if ($posisi1=='D'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $nilaibayar1,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				$nilaitotaljurnal=($nilaitotaljurnal+$nilaibayar1);
			}
			elseif ($posisi1=='K'){
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir1,
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $nilaibayar1
					//'jenis_jurnal'  => 'invoicing'
				);
			}

		}
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tgl,
			'jml'	            => $nilaitotaljurnal,
			'koreksi_no'		=> '-',
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan' 		=> $Keterangan_INV1,
			'bulan'				=> $Bln,
			'tahun'				=> $Thn,
			'user_id'			=> $data_session['ORI_User']['username'],
			'memo'			    => $nomordoc,
			'tgl_jvkoreksi'	    => $tgl,
			'ho_valid'			=> ''
		);
		// print_r($datajurnal1);
		// exit;
			$db2->insert('javh',$dataJVhead);
			$db2->insert_batch('jurnal',$det_Jurnaltes1);

			$datapiutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl,
				//'no_perkiraan'    => $coa_penjualan,
				'no_perkiraan'  => '1102-01-01',
				'keterangan'    => $Keterangan_INV1,
				'no_reff'       => $nomordoc,
				'debet'         => $Jml_Ttl,
				'kredit'        =>  0,
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);
			if($total_retensi2_idr>0){	// retensi 2
				$datapiutang = array(
					'tipe'       	 => 'JV',
					'nomor'       	 => $Nomor_JV,
					'tanggal'        => $tgl,
					'no_perkiraan'  => '1102-01-03',
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $total_retensi2_idr,
					'kredit'        =>  0,
					'id_supplier'   => $Id_klien,
					'nama_supplier' => $Nama_klien,
				);
				$this->db->insert('tr_kartu_piutang',$datapiutang);
			}
			$this->print_invoice_fix();


		if($this->db->trans_status() === FALSE or $db2->trans_status()=== FALSE){
		 $this->db->trans_rollback();
		 $db2->trans_rollback();
		 $Arr_Return		= array(
				'status'		=> 2,
				'pesan'			=> 'Error Process Failed. Please Try Again...'
		   );
		}else{
			$this->db->trans_commit();
			$db2->trans_commit();
		 // $Arr_Return		= array(
			// 'status'		=> 1,
			// 'pesan'			=> 'Cancel Process Success. Thank You & Have A Nice Day...'
			// );
		}
		// echo json_encode($Arr_Return);

	}

	public function save_penghapusan(){
		
		
		$session = $this->session->userdata('app_session');
		$Tgl_Invoice        = $this->input->post('tgl_bayar');
		$nomordoc 			= $this->input->post('no_invoice');
		$tgl				= $this->input->post('tgl_bayar');
		$db2 			= $this->load->database('accounting', TRUE);
		$data_session 	    = $this->session->userdata; 
		$kd_bayar 			= $this->penghapusan_piutang_model->generate_nopn($Tgl_Invoice);
		$id =$this->input->post('id_invoice');
		
		 $this->db->trans_begin();
		
	   
		$data = array(
						'no_invoice'=>$this->input->post('no_invoice'),
						'kd_pembayaran'=>$kd_bayar,
						'jenis_reff'=>'-',
						'no_reff'=>'-',
						'tgl_pembayaran'=>$this->input->post('tgl_bayar'),
						'kurs_bayar'=>$this->input->post('kurs'),
						'jumlah_pembayaran'=>str_replace(",","",$this->input->post('kurs')),
						'jumlah_pembayaran_idr'=>str_replace(",","",$this->input->post('total_rupiah')),
						'created_by'    => $session['id_user'],
			            'created_on'=> date('Y-m-d H:i:s'),
						'jenis_pph'=>$this->input->post('kategori'),
						'coa'=>$this->input->post('coa'),
						'keterangan'=>$this->input->post('ket_bayar'),
						'id_customer'=>$this->input->post('id_customer'),
						'nm_customer'=>$this->input->post('nm_customer'),
						

					);
					
		
						
		$this->db->insert('tr_invoice_dihapuskan',$data);
		
		
		    
			 
			
		
		$Nama_klien				= $this->input->post('nm_customer');
		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_Sales('101',$tgl);
		$Nomor_JV				= get_generate_jurnal('GJ',date('y-m-d'));
		$Keterangan_INV		    = 'Penghapusan Piutang A/N '.$Nama_klien.' INV NO. '.$nomordoc;

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY

			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => $this->input->post('coa'),
					'keterangan'    => $Keterangan_INV,
					'no_reff'       => $nomordoc,
					'debet'         => str_replace(",","",$this->input->post('total_rupiah')),
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '1102-01-01',
					'keterangan'    => $Keterangan_INV,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => str_replace(",","",$this->input->post('total_rupiah')),
					//'jenis_jurnal'  => 'invoicing'
				);
			

		
		
		$Bln = date('m',strtotime($tgl));
		$Thn = date('Y',strtotime($tgl));
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tgl,
			'jml'	            => str_replace(",","",$this->input->post('total_rupiah')),
			'koreksi_no'		=> '-',
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan' 		=> $Keterangan_INV,
			'bulan'				=> $Bln,
			'tahun'				=> $Thn,
			'user_id'			=> $data_session['ORI_User']['username'],
			'memo'			    => $nomordoc,
			'tgl_jvkoreksi'	    => $tgl,
			'ho_valid'			=> ''
		);
		// print_r($datajurnal1);
		// exit;
			$db2->insert('javh',$dataJVhead);
			$db2->insert_batch('jurnal',$det_Jurnaltes1);
			$Id_klien	= $this->input->post('id_customer');
			$Nama_klien = $this->input->post('nm_customer');
			$datapiutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl,
				//'no_perkiraan'    => $coa_penjualan,
				'no_perkiraan'  => '1102-01-01',
				'keterangan'    => $Keterangan_INV,
				'no_reff'       => $nomordoc,
				'debet'         => 0,
				'kredit'        =>  str_replace(",","",$this->input->post('total_rupiah')),
				'id_supplier'   => $Id_klien,
				'nama_supplier' => $Nama_klien,
			);
			$this->db->insert('tr_kartu_piutang',$datapiutang);
			
			$Qry_Update_Cabang_acc	 = "UPDATE pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
			$db2->query($Qry_Update_Cabang_acc);
			
			$rupiah =str_replace(",","",$this->input->post('total_rupiah'));
			$dolar = str_replace(",","",$this->input->post('kurs'));
			$update_invoice	= "UPDATE tr_invoice_header SET sisa_invoice_idr= sisa_invoice_idr-$rupiah, sisa_invoice= sisa_invoice-$dolar  WHERE id_invoice='$id'";
		$update         = $this->db->query($update_invoice);
		
		
		   
	    if($this->db->trans_status() === FALSE){
			 $this->db->trans_rollback(); 
			 $Arr_Return		= array(
					'status'		=> 2, 
					'pesan'			=> 'Save Process Failed. Please Try Again...' 
			   );
		}else{
			 $this->db->trans_commit();
			 $Arr_Return		= array(
				'status'		=> 1,
				'pesan'			=> 'Save Process Success. '
		   );
		}
		echo json_encode($Arr_Return);
		
	
	}

}