<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoicing extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model'); 
		$this->load->model('invoicing_model');
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
		$this->invoicing_model->create_new();
	}

	public function server_side_create_new(){
		$this->invoicing_model->get_data_json_create_new();
	}

	public function create_invoice(){
		$this->invoicing_model->create_invoice($this->uri->segment(3));
	}

	public function save_invoice(){
		$this->invoicing_model->save_invoice();
	}

	//=========================================================================================================================
	//====================================================PIUTANG==============================================================
	//=========================================================================================================================

	public function index(){
		$this->invoicing_model->list_inv();
	}

	public function server_side_inv(){
		$this->invoicing_model->get_data_json_inv();
	}

	public function modal_detail_invoice(){
		$this->invoicing_model->modal_detail_invoice();
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
//			$kodejurnal1		= 'JV051'; invoice manual
//			$kodejurnal1		= 'JV061'; sesuai invoice delivery
			$kodejurnal1		= 'JV063';
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
//		$Nomor_JV				= get_generate_jurnal('GJ',date('y-m-d'));
		$Nomor_JV				= $this->Acc_model->generate_jurnal_jv('GJ',date('y-m-d'));		
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
			$db2->insert('javh',$dataJVhead);
			$db2->insert_batch('jurnal',$det_Jurnaltes1);

			$db2->trans_commit();
		  $Arr_Return		= array(
			 'status'		=> 1,
			 'pesan'			=> 'Cancel Process Success. Thank You & Have A Nice Day...'
			 );
		}
		echo json_encode($Arr_Return);

	}

	public function print_invoice_fix(){
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/libraries/MPDF57/mpdf.php";

		$data_session	= $this->session->userdata;

		$mpdf		= new mPDF('utf-8','A4');
		$mpdf->SetImportUse();
        $id   		= $this->uri->segment(3);
		$nomordoc 	= get_name('tr_invoice_header','no_invoice','id_invoice',$id);
		$gethd 		= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice='$nomordoc'")->row();
		$tgl       	= $gethd->tgl_invoice;
		$Jml_Ttl   	= $gethd->total_invoice;
		$Id_klien   = $gethd->nm_customer;
		$Nama_klien = $gethd->nm_customer;
		$Bln 		= substr($tgl,5,2);
		$Thn 		= substr($tgl,0,4);

        $alamat_cust 	= $this->db->query("SELECT * FROM customer WHERE id_customer = '".$gethd->id_customer."'")->row();

		$count 			= $this->db->query("SELECT COUNT(no_invoice) as total FROM tr_invoice_detail WHERE no_invoice ='".$nomordoc."'")->row();
		$count1			= $count->total;

        $total  		= $this->db->get_where('tr_invoice_header', array('no_invoice' =>$nomordoc))->result();
		$detail  		= $this->invoicing_model->GetInvoiceDetail($nomordoc);

		$data['total'] 		= $this->invoicing_model->GetInvoiceHeader($nomordoc);
		$data['results']  	= $this->invoicing_model->GetInvoiceDetail($nomordoc);
		$data['user'] 		= $data_session['ORI_User']['username'];

        $show 		= $this->load->view('Invoicing/print_data_invoice_idr', $data ,TRUE);

        $tglprint 	= date("d-m-Y H:i:s");
		$tglprint2 	= date("d-m-Y");

		foreach($total as $val){
			$date 		= tgl_indo($val->tgl_invoice);//date('d-m-Y');
			$invoice  	= $val->no_invoice;
			$so  		= $val->so_number;
			$total2  	= $val->total_invoice;
			$customer  	= $val->nm_customer;
			$tagih  	= $val->jenis_invoice;
			$persentase = number_format($val->persentase);
			$persen     = '%';

			if($tagih=='uang muka'){
				$jenis_invoice1='DOWN PAYMENT OF ';
				$jenis_invoice=$jenis_invoice1.$persentase.$persen;
			}
			elseif($tagih=='progress'){
				$jenis_invoice1='PROGRESS';
				$jenis_invoice=$jenis_invoice1;
			}
			else{
				$jenis_invoice='RETENSI';
			}

	    }
		// echo "<pre>";
		// print_r($total); exit;
		$ArrHeader = array(
			'header'		=> $total,
			'jenis_invoice'	=> $jenis_invoice,
			'alamat_cust'	=> $alamat_cust->alamat
		);
		$header = '
        	<table width="100%">
				<tr>
					<td style="height: 210px;"></td>
				</tr>
        	</table>
			<table border="0" width="100%">
            <tr>
                 <td style="width: 15%; font-size:8pt !important;vertical-align:top"><b>Kepada Yth</b></td>
				 <td style="width: 1%; font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td style="width: 37%; font-size:8pt !important;vertical-align:top"><b>' .@$val->nm_customer.'</b></td>
                 <td style="width: 15%; font-size:8pt !important;vertical-align:top"><b>Faktur No.</b></td>
                 <td style="width: 1%; font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td style="width: 31%; font-size:8pt !important;vertical-align:top"><b>' .@$val->no_faktur.'</b></td>
			</tr>
			<tr>
                 <td style="font-size:8pt !important;vertical-align:top" rowspan="3"><b>Alamat</b></td>
                 <td style="font-size:8pt !important;vertical-align:top" rowspan="3"><b>:</b></td>
				 <td style="font-size:8pt !important;vertical-align:top" rowspan="3"><b>' .@$alamat_cust->alamat.'</b></td>
				 <td style="font-size:8pt !important;vertical-align:top"><b>F. Pajak No.</b></td>
                 <td style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td style="font-size:8pt !important;vertical-align:top"><b>' .@$val->no_pajak.'</b></td>
		    </tr>
			<tr>
                 <td style="font-size:8pt !important;vertical-align:top"><b>No PO.</b></td>
				 <td style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td style="font-size:8pt !important;vertical-align:top"><b>' .@$val->no_po.'</b></td>

		   </tr>
		   <tr>
                 <td style="font-size:8pt !important;vertical-align:top"><b>Payment Term</b></td>
				 <td style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td style="font-size:8pt !important;vertical-align:top"><b>' .(($val->payment_term==100)?"CASH BEFORE DELIVERY":$val->payment_term ." Days").'</b></td>
		    </tr>
          </table>
		  ';

        $mpdf->SetHeader($header);

        $mpdf->AddPageByArray([
                'orientation' => 'P',
                'margin-top' => 80,
                'margin-bottom' => 15,
                'margin-left' => 5,
                'margin-right' => 10,
                'margin-header' => 0,
                'margin-footer' => 0,
            ]);
		$mpdf->SetDefaultBodyCSS('background', "url('assets/images/kop-surat-opc.jpg')");
		$mpdf->SetDefaultBodyCSS('background-image-resize', 5);
		$mpdf->SetTitle($nomordoc);
        $mpdf->WriteHTML($show);
        $mpdf->Output($nomordoc." ".date('dmYHis').".pdf" ,'I');
    }

	public function print_invoice_fix2(){
	  $sroot 		= $_SERVER['DOCUMENT_ROOT'];
	  include $sroot."/application/libraries/MPDF57/mpdf.php";

	  $data_session	= $this->session->userdata;

      $mpdf=new mPDF('utf-8','A4');
      $mpdf->SetImportUse();
        $nomordoc   = $this->uri->segment(3);
		$gethd 		= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice='$nomordoc'")->row();
		$tgl       	= $gethd->tgl_invoice;
		$Jml_Ttl   	= $gethd->total_invoice;
		$Id_klien   = $gethd->nm_customer;
		$Nama_klien = $gethd->nm_customer;
		$Bln 		= substr($tgl,5,2);
		$Thn 		= substr($tgl,0,4);

		$data_header 	= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice ='".$nomordoc."'")->row();
        $alamat_cust 	= $this->db->query("SELECT * FROM customer WHERE id_customer = '".$data_header->id_customer."'")->row();

		$count 			= $this->db->query("SELECT COUNT(no_invoice) as total FROM tr_invoice_detail WHERE no_invoice ='".$nomordoc."'")->row();
		$count1			= $count->total;

        $total  	= $this->db->get_where('tr_invoice_header', array('no_invoice' =>$nomordoc))->result();
		$detail  	= $this->invoicing_model->GetInvoiceDetail($nomordoc);

		$data['total'] 		= $this->invoicing_model->GetInvoiceHeader($nomordoc);
		$data['results']  	= $this->invoicing_model->GetInvoiceDetail($nomordoc);
		$data['user'] 		 = $data_session['ORI_User']['username'];

		$show 		= $this->load->view('Invoicing/print_data_invoice_idr2', $data ,TRUE);

        $tglprint 	= date("d-m-Y H:i:s");
		$tglprint2 	= date("d-m-Y");

		foreach($total as $val){
			$date 		= tgl_indo($val->tgl_invoice);//date('d-m-Y');
			$invoice  	= $val->no_invoice;
			$so  		= $val->so_number;
			$total2  	= $val->total_invoice;
			$customer  	= $val->nm_customer;
			$tagih  	= $val->jenis_invoice;
			$persentase = number_format($val->persentase);
			$persen     = '%';

			if($tagih=='uang muka'){
				$jenis_invoice1='DOWN PAYMENT OF ';
				$jenis_invoice=$jenis_invoice1.$persentase.$persen;
			}
			elseif($tagih=='progress'){
				$jenis_invoice1='PROGRESS';
				$jenis_invoice=$jenis_invoice1;
			}
			else{
				$jenis_invoice='RETENSI';
			}

	    }
		// echo "<pre>";
		// print_r($total); exit;
		$ArrHeader = array(
			'header'		=> $total,
			'jenis_invoice'	=> $jenis_invoice,
			'alamat_cust'	=> $alamat_cust->alamat
		);

		// $header = $this->load->view('Invoicing/print_invoice_header', $ArrHeader ,TRUE);

        $header = '
        	<table width="100%" border="0" >
				<tr>
					<td width="8%" style="text-align: left; height: 75px;" >
					</td>
					<td align="center" style="font-size:10pt"><b>
					INVOICE <br>
					NO. '.@$val->no_invoice.'
					</td>
				</tr>
				<tr>

				</tr>
        	</table><br>
			<table border="0" width="100%"
				style="
					font-family: Arial, Helvetica, sans-serif;
					font-size:10px;
					color:#333333;
					border-width: 1px;
					border-color: #666666;
					border-collapse: collapse;">
				<tr>
					 <td width="15%"><b>Kepada YTH</b></td>
					 <td width="1%"><b>:</b></td>
					 <td width="35%"><b>' .@$val->nm_customer.'</b></td>
					 <td width="15%"><b>Faktur No</b></td>
					 <td width="1%"><b>:</b></td>
					 <td width="35%"><b>' .@$val->no_faktur.'</b></td>
				</tr>
				<tr>
					 <td><b>No PO</b></td>
					 <td><b>:</b></td>
					 <td><b>' .@$val->no_po.'</b></td>
					 <td><b>F. Pajak No</b></td>
					 <td><b>:</b></td>
					 <td><b>' .@$val->no_pajak.'</b></td>
				</tr>
				<tr>
					 <td><b>Jenis Tagihan</b></td>
					 <td><b>:</b></td>
					 <td><b>' .$jenis_invoice.'</b></td>
					 <td><b>Payment Term</b></td>
					 <td><b>:</b></td>
					 <td><b>' .@$val->payment_term.'</b></td>
				</tr>
				<tr>
					 <td style="vertical-align:top;"><b>Alamat</b></td>
					 <td style="vertical-align:top;"><b>:</b></td>
					 <td style="text-align: justify;"><b>' .@$alamat_cust->alamat.'</b></td>
					 <td><b></b></td>
					 <td><b></b></td>
					 <td><b></b></td>
				</tr>
			</table>
			';

        $mpdf->SetHeader($header);


        $mpdf->SetFooter('

       	<div id="footer">
        <table>
            <tr><td>PT ORI POLYTEC COMPOSITES - Printed By '.ucwords($data_session['ORI_User']['username']).' On '.$tglprint.' </td></tr>
        </table>
        </div>
        ');


         $mpdf->AddPageByArray([
                'orientation' => 'P',
                'margin-top' => 40,
                'margin-bottom' => 15,
                'margin-left' => 5,
                'margin-right' => 10,
                'margin-header' => 0,
                'margin-footer' => 0,
            ]);
        $mpdf->WriteHTML($show);
        $mpdf->Output();
    }

	public function print_invoice_usd(){
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/libraries/MPDF57/mpdf.php";

		$data_session	= $this->session->userdata;

		$mpdf		= new mPDF('utf-8','A4');
		$mpdf->SetImportUse();
        $id   		= $this->uri->segment(3);
		$nomordoc 	= get_name('tr_invoice_header','no_invoice','id_invoice',$id);
		$gethd 		= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice='$nomordoc'")->row();
		$tgl       	= $gethd->tgl_invoice;
		$Jml_Ttl   	= $gethd->total_invoice;
		$Id_klien   = $gethd->nm_customer;
		$Nama_klien = $gethd->nm_customer;
		$Bln 		= substr($tgl,5,2);
		$Thn 		= substr($tgl,0,4);

		$data_header 	= $this->db->query("SELECT a.*, b.type_lc, b.etd, b.eta, b.consignee, b.notify_party, b.port_of_loading, b.port_of_discharges, b.flight_airway_no, b.ship_via, b.saliling, b.vessel_flight, b.term_delivery FROM tr_invoice_header a LEFT JOIN penagihan b ON a.id_penagihan=b.id WHERE a.no_invoice ='".$nomordoc."'")->row();
        $alamat_cust 	= $this->db->query("SELECT * FROM customer WHERE id_customer = '".$data_header->id_customer."'")->row();

		$count 			= $this->db->query("SELECT COUNT(no_invoice) as total FROM tr_invoice_detail WHERE no_invoice ='".$nomordoc."'")->row();
		$count1			= $count->total;

        $total  		= $this->db->get_where('tr_invoice_header', array('no_invoice' =>$nomordoc))->result();
		$detail  		= $this->invoicing_model->GetInvoiceDetail($nomordoc);

		$data['total'] 		= $this->invoicing_model->GetInvoiceHeader($nomordoc);
		$data['results']  	= $this->invoicing_model->GetInvoiceDetail($nomordoc);
		$data['user'] 		= $data_session['ORI_User']['username'];
		$data['data_header']= $data_header;

        $show 		= $this->load->view('Invoicing/print_data_invoice_usd', $data ,TRUE);

        $tglprint 	= date("d-m-Y H:i:s");
		$tglprint2 	= date("d-m-Y");

		foreach($total as $val){
			$date 		= tgl_indo($val->tgl_invoice);//date('d-m-Y');
			$invoice  	= $val->no_invoice;
			$so  		= $val->so_number;
			$total2  	= $val->total_invoice;
			$customer  	= $val->nm_customer;
			$tagih  	= $val->jenis_invoice;
			$persentase = number_format($val->persentase);
			$persen     = '%';

			if($tagih=='uang muka'){
				$jenis_invoice1='DOWN PAYMENT OF ';
				$jenis_invoice=$jenis_invoice1.$persentase.$persen;
			}
			elseif($tagih=='progress'){
				$jenis_invoice1='PROGRESS';
				$jenis_invoice=$jenis_invoice1;
			}
			else{
				$jenis_invoice='RETENSI';
			}

	    }
		// echo "<pre>";
		// print_r($total); exit;
		$ArrHeader = array(
			'header'		=> $total,
			'jenis_invoice'	=> $jenis_invoice,
			'alamat_cust'	=> $alamat_cust->alamat
		);

		$header = '
        <table width="100%">
			<tr>
				<td style="height: 100px;"></td>
			</tr>
		</table>
        <table border="0" width="100%">
			<tr>
				<th colspan="6" style="height: 80px;" align=center>I N V O I C E</th>
			</tr>
			<tr>
				<td width="14%"></td>
				<td width="1%"></td>
				<td width="35%"></td>
				<td width="14%">No</td>
				<td width="1%">:</td>
				<td width="35%">'.@$val->no_invoice.'</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>Date</td>
				<td>:</td>
				<td>'.date('M d, Y', strtotime($data_header->tgl_invoice)).'</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>L/C</td>
				<td>:</td>
				<td>'.strtoupper($data_header->type_lc).'</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>ETD</td>
				<td>:</td>
				<td>'.(($data_header->etd=="" || $data_header->etd=="0000-00-00")?"":date('M d, Y', strtotime($data_header->etd))).'</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>ETA</td>
				<td>:</td>
				<td>'.(($data_header->eta=="" || $data_header->eta=="0000-00-00")?"":date('M d, Y', strtotime($data_header->eta))).'</td>
			</tr>
			<tr>
				<td colspan="6" height="10px"></td>
			</tr>
			<tr>
				<td colspan="3"><b>BILL TO:</b></td>
				<td colspan="3"><b>CONSIGNEE :</b></td>
			</tr>
			<tr>
				<td colspan="3" style="vertical-align:top;">'.@$val->nm_customer.'<br>'.$alamat_cust->alamat.'</td>
				<td colspan="3" style="vertical-align:top;">'.$data_header->consignee.'</td>
			</tr>
			<tr>
				<td colspan="6" height="10px"></td>
			</tr>'.(($data_header->notify_party!="")?'
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td colspan="3"><b>NOTIFY PARTY :</b></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td colspan="3">'.$data_header->notify_party.'</td>
			</tr>
		  ':'').'</table>';

        $mpdf->SetHeader($header);
        $mpdf->AddPageByArray([
				'orientation' => 'P',
                'margin-top' => 130,
                'margin-bottom' => 15,
                'margin-left' => 5,
                'margin-right' => 10,
                'margin-header' => 0,
                'margin-footer' => 0
            ]);
		$mpdf->SetDefaultBodyCSS('background', "url('assets/images/kop-surat-opc.jpg')");
		$mpdf->SetDefaultBodyCSS('background-image-resize', 5);
		$mpdf->SetTitle($nomordoc);
        $mpdf->WriteHTML($show);
        $mpdf->Output($nomordoc." ".date('dmYHis').".pdf" ,'I');
    }

	public function excels_old($id){
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		$data_session	= $this->session->userdata;
		$nomordoc 	= get_name('tr_invoice_header','no_invoice','id_invoice',$id);
		$gethd 		= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice='$nomordoc'")->row();
		$tgl       	= $gethd->tgl_invoice;
		$Jml_Ttl   	= $gethd->total_invoice;
		$Id_klien   = $gethd->nm_customer;
		$Nama_klien = $gethd->nm_customer;
		$Bln 		= substr($tgl,5,2);
		$Thn 		= substr($tgl,0,4);

		$data_header 	= $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice ='".$nomordoc."'")->row();
        $alamat_cust 	= $this->db->query("SELECT * FROM customer WHERE id_customer = '".$data_header->id_customer."'")->row();

		$count 			= $this->db->query("SELECT COUNT(no_invoice) as total FROM tr_invoice_detail WHERE no_invoice ='".$nomordoc."'")->row();
		$count1			= $count->total;

        //$total  		= $this->db->get_where('tr_invoice_header', array('no_invoice' =>$nomordoc))->result();
		$detail  		= $this->invoicing_model->GetInvoiceDetail($nomordoc);

		$data['total'] 		= $this->invoicing_model->GetInvoiceHeader($nomordoc);
		$data['results']  	= $this->invoicing_model->GetInvoiceDetail($nomordoc);
		$data['user'] 		= $data_session['ORI_User']['username'];

        $this->load->view('Invoicing/invoice_excel', $data);

 		$dataxls=$this->load->view('Invoicing/invoice_excel',$data, TRUE);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Invoice-'.$nomordoc.'.xls"');
		header('Cache-Control: max-age=0');
		echo $dataxls;
		die();
    }

	public function excels($id){
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		$data_session	= $this->session->userdata;
		$nomordoc 	= get_name('tr_invoice_header','no_invoice','id_invoice',$id);
		$gethd 		= $this->db->query("SELECT a.*, b.type_lc, b.etd, b.eta, b.consignee, b.notify_party, b.port_of_loading, b.port_of_discharges, b.flight_airway_no, b.ship_via, b.saliling, b.vessel_flight FROM tr_invoice_header a LEFT JOIN penagihan b ON a.id_penagihan=b.id WHERE a.no_invoice='$nomordoc'")->row();
		$tgl       	= $gethd->tgl_invoice;
		$Jml_Ttl   	= $gethd->total_invoice;
		$Id_klien   = $gethd->nm_customer;
		$Nama_klien = $gethd->nm_customer;
		$Bln 		= substr($tgl,5,2);
		$Thn 		= substr($tgl,0,4);
        $alamat_cust 	= $this->db->query("SELECT * FROM customer WHERE id_customer = '".$gethd->id_customer."'")->row();
		$count 			= $this->db->query("SELECT COUNT(no_invoice) as total FROM tr_invoice_detail WHERE no_invoice ='".$nomordoc."'")->row();
		$matauang='';		
		$count1			= $count->total;
		$data_detail  		= $this->invoicing_model->GetInvoiceDetail($nomordoc);
		$this->load->library("PHPExcel");
		$objPHPExcel	= new PHPExcel();
		$sheet	= $objPHPExcel->getActiveSheet();
if($gethd->base_cur=='IDR'){
		$sheet->getColumnDimension('A')->setWidth(1);
		$sheet->getColumnDimension('B')->setWidth(4);
		$sheet->getColumnDimension('C')->setWidth(6);
		$sheet->getColumnDimension('D')->setWidth(1);
		$sheet->getColumnDimension('E')->setWidth(10);
		$sheet->getColumnDimension('F')->setWidth(8);
		$sheet->getColumnDimension('G')->setWidth(8);
		$sheet->getColumnDimension('H')->setWidth(8);
		$sheet->getColumnDimension('I')->setWidth(4);
		$sheet->getColumnDimension('J')->setWidth(1);
		$sheet->getColumnDimension('K')->setWidth(8);
		$sheet->getColumnDimension('L')->setWidth(5);
		$sheet->getColumnDimension('M')->setWidth(1);
		$sheet->getColumnDimension('N')->setWidth(15);
		$sheet->getColumnDimension('O')->setWidth(10);
		$sheet->getColumnDimension('P')->setWidth(4);
		$sheet->getColumnDimension('Q')->setWidth(1);
		$Row	= 3;
		$sheet->setCellValue('B'.$Row, 'Kepada Yth');
		$sheet->setCellValue('D'.$Row, ':');
		$sheet->setCellValue('E'.$Row, $Nama_klien);
		$sheet->setCellValue('J'.$Row, 'Faktur No.');
		$sheet->setCellValue('M'.$Row, ':');
		$sheet->setCellValue('N'.$Row, $gethd->no_faktur);
		$NewRow	= $Row+1;
		$sheet->setCellValue('B'.$NewRow, 'Alamat');
		$sheet->setCellValue('D'.$NewRow, ':');
		$style_address = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
				'setWrapText'=>true
			)
		);
		$sheet->getStyle('E'.$NewRow.':I6')->applyFromArray($style_address);
		$sheet->mergeCells('E'.$NewRow.':I6');
		$sheet->setCellValue('E'.$NewRow, $alamat_cust->alamat);
		$sheet->setCellValue('J'.$NewRow, 'F. Pajak No.');
		$sheet->setCellValue('M'.$NewRow, ':');
		$sheet->setCellValue('N'.$NewRow, $gethd->no_pajak);
		$NewRow++;
		$sheet->setCellValue('J'.$NewRow, 'P.O No.');
		$sheet->setCellValue('M'.$NewRow, ':');
		$sheet->setCellValue('N'.$NewRow, $gethd->no_po);
		$NewRow++;
		$sheet->setCellValue('J'.$NewRow, 'Payment Term');
		$sheet->setCellValue('M'.$NewRow, ':');
		$sheet->setCellValue('N'.$NewRow, (($gethd->payment_term==0)?"CASH BEFORE DELIVERY":$gethd->payment_term ." Days"));
		$NewRow	= $NewRow+2;
		$no=0;
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$sheet->getStyle('B'.$NewRow.':P'.$NewRow)->applyFromArray($style_header);
		$sheet->setCellValue('B'.$NewRow, 'NO');
		$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
		$sheet->setCellValue('C'.$NewRow, 'N A M A   B A R A N G');
		$sheet->mergeCells('K'.$NewRow.':M'.$NewRow);
		$sheet->setCellValue('K'.$NewRow, 'QUANTITY');
		$sheet->setCellValue('N'.$NewRow, 'HARGA SATUAN');
		$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
		$sheet->setCellValue('O'.$NewRow, 'JUMLAH');
		$style_rows = array(
			'borders' => array(
				'left' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'right' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),			
		);
		$style_rows_right = array(
			'borders' => array(
				'left' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'right' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),			
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		foreach($data_detail as $val){
			if($val->harga_total_idr>0){
				$no++;
				$NewRow++;
				$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
				$sheet->setCellValue('B'.$NewRow, $no);
				$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
				$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
				$sheet->setCellValue('C'.$NewRow, $val->desc);
				$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);
				$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows_right);
				$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
				if($val->qty>0){
					$sheet->setCellValue('K'.$NewRow, $val->qty);
					$sheet->setCellValue('L'.$NewRow, $val->unit);
					$sheet->setCellValue('N'.$NewRow, number_format($val->harga_satuan_idr,2));
				}
				$sheet->setCellValue('O'.$NewRow, number_format($val->harga_total_idr,2));
			}
		}
		if($no<20){
			for($no;$no<20;$no++){
				$NewRow++;
				$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
				$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
				$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);
				$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows);
				$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
			}
		}
		$NewRow++;
		$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
		$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
		$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
		$sheet->setCellValue('C'.$NewRow, 'TOTAL');
		$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);
		$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
		$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows_right);
		$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
		$sheet->setCellValue('O'.$NewRow, number_format($gethd->total_dpp_rp,2));
		if($gethd->total_um_idr > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'UANG MUKA');
			$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);
			$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
			$sheet->setCellValue('O'.$NewRow, number_format($gethd->total_um_idr,2));
		}
		if($gethd->total_diskon_idr > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'DISKON');
			$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);
			$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
			$sheet->setCellValue('O'.$NewRow, number_format($gethd->total_diskon_idr,2));
		}
		if($gethd->total_retensi_idr > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'POTONGAN RETENSI');
			$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);
			$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
			$sheet->setCellValue('O'.$NewRow, number_format($gethd->total_retensi_idr,2));
		}
		if($gethd->total_ppn_idr > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'PPN');
			$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);
			$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
			$sheet->setCellValue('O'.$NewRow, number_format($gethd->total_ppn_idr,2));
		}
		if($gethd->total_retensi2_idr > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'POTONGAN RETENSI PPN');
			$sheet->mergeCells('L'.$NewRow.':M'.$NewRow);	
			$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
			$sheet->setCellValue('O'.$NewRow, number_format($gethd->total_retensi2_idr,2));
		}
		$style_endrows = array(
			'borders' => array(
				'left' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'right' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'bottom' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )			),			
		);
		$NewRow++;
		$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('C'.$NewRow.':J'.$NewRow)->applyFromArray($style_endrows);
		$sheet->mergeCells('C'.$NewRow.':J'.$NewRow);
		$sheet->mergeCells('K'.$NewRow.':M'.$NewRow);
		$sheet->getStyle('K'.$NewRow.':M'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('N'.$NewRow.':N'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_endrows);
		$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);

		$style_center_bold = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$NewRow++;
		$sheet->getStyle('N'.$NewRow.':O'.($NewRow+1))->applyFromArray($style_center_bold);
		$sheet->setCellValue('N'.$NewRow, 'TOTAL');
		$sheet->getStyle('O'.$NewRow.':P'.$NewRow)->applyFromArray($style_endrows);
		$sheet->mergeCells('O'.$NewRow.':P'.$NewRow);
		$sheet->setCellValue('O'.$NewRow, number_format($gethd->total_invoice_idr,2));
		$NewRow=$NewRow+2;
		$sheet->setCellValue('B'.$NewRow, 'Terbilang');
		$sheet->setCellValue('D'.$NewRow, ':');
		$style_terbilang = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$sheet->getStyle('E'.$NewRow.':O'.$NewRow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('E'.$NewRow.':O'.($NewRow+1))->applyFromArray($style_terbilang);
		$sheet->mergeCells('E'.$NewRow.':O'.($NewRow+1));
		$sheet->setCellValue('E'.$NewRow, strtoupper(ynz_terbilang_format($gethd->total_invoice_idr)).' RUPIAH');
		$NewRow=$NewRow+3;
		$sheet->setCellValue('B'.$NewRow, 'Catatan :');
		$sheet->setCellValue('N'.$NewRow, 'Bekasi,'.date('d F Y', strtotime($gethd->tgl_invoice)));
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, 'Pembayaran dengan Cheque/Giro dianggap sah,');
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, 'setelah Cheque/Giro dapat diuangkan (clearing)');
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, 'Pembayaran harap di transfer full amount ke :');
		$NewRow++;
		$sheet->getStyle('B'.$NewRow.':B'.($NewRow+8))->getFont()->setBold(true);
		$sheet->setCellValue('B'.$NewRow, 'PT. ORI POLYTEC COMPOSITES');
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, 'OCBC Mangga Dua Le Grandeur');
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, 'IDR : 0278.0001.6993');
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, 'USD : 0278.0001.6993');
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, '* Denda 0,1% / hari, max 5% dihitung sejak tanggal jatuh tempo pembayaran');
		
		$style_center_bold_italic = array(
			'font' => array(
				'bold' => true,
				'underline' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$sheet->getStyle('N'.$NewRow.':P'.($NewRow+1))->applyFromArray($style_center_bold_italic);
		$sheet->mergeCells('N'.$NewRow.':P'.($NewRow+1));
		$sheet->setCellValue('N'.$NewRow, 'VINA ISABELLA');
		
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, '* Untuk tagihan USD yang akan dibayarkan dalam rupiah, ');
		$NewRow++;
		$sheet->setCellValue('B'.$NewRow, '   harap konfirmasi kurs dengan finance kami');
}else{
		$sheet->getColumnDimension('A')->setWidth(8);
		$sheet->getColumnDimension('B')->setWidth(4);
		$sheet->getColumnDimension('C')->setWidth(41);
		$sheet->getColumnDimension('D')->setWidth(33);
		$sheet->getColumnDimension('E')->setWidth(12);
		$sheet->getColumnDimension('F')->setWidth(8);
		$sheet->getColumnDimension('G')->setWidth(16);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(26);
		$Row	= 8;
		$style_center = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$style_bold = array(
			'font' => array(
				'bold' => true,
			),
		);
		$sheet->getStyle('B'.$Row.':I'.($Row))->applyFromArray($style_center);
		$sheet->mergeCells('B'.$Row.':I'.$Row);
		$sheet->setCellValue('B'.$Row, 'I N V O I C E');
		$NewRow=$Row+3;
		$sheet->setCellValue('F'.$NewRow, 'No.');
		$sheet->setCellValue('G'.$NewRow, ': '.$gethd->no_faktur);
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'Date');
		$sheet->setCellValue('G'.$NewRow, ': '.date('M d, Y', strtotime($gethd->tgl_invoice)));
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'L/C');
		$sheet->setCellValue('G'.$NewRow, ': '.strtoupper($gethd->type_lc));
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'ETD');
		$sheet->setCellValue('G'.$NewRow, ': '.(($gethd->etd=="")?"":date('M d, Y', strtotime($gethd->etd))));
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'ETA');
		$sheet->setCellValue('G'.$NewRow, ': '.(($gethd->eta=="")?"":date('M d, Y', strtotime($gethd->eta))));
		$style_address = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
				'setWrapText'=>true
			)
		);
		$NewRow	= $NewRow+2;
		$sheet->getStyle('C'.$NewRow.':F'.($NewRow))->applyFromArray($style_bold);
		$sheet->setCellValue('C'.$NewRow, 'BILL TO :');
		$sheet->setCellValue('F'.$NewRow, 'CONSIGNEE :');
		$NewRow++;
		$sheet->setCellValue('C'.$NewRow, $gethd->nm_customer);
		$sheet->getStyle('F'.$NewRow.':I'.($NewRow))->applyFromArray($style_address);
		$sheet->getStyle('F'.$NewRow.':I'.$NewRow)->getAlignment()->setWrapText(true);
		$sheet->mergeCells('F'.$NewRow.':I'.($NewRow+2));
		$sheet->setCellValue('F'.$NewRow, strip_tags($gethd->consignee));
		if($gethd->notify_party!=""){
			$sheet->setCellValue('F'.($NewRow+1), 'NOTIFY PARTY :');
			$sheet->getStyle('F'.($NewRow+2).':I'.($NewRow+3))->applyFromArray($style_address);
			$sheet->mergeCells('F'.($NewRow+2).':I'.($NewRow+3));
			$sheet->setCellValue('F'.($NewRow+2), strip_tags($gethd->notify_party));
		}
		$NewRow++;
		$all_border = array(
		  'borders' => array(
			'allborders' => array(
			  'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		  )
		);
		$sheet->getStyle('C'.$NewRow.':C'.($NewRow+5))->applyFromArray($style_address);
		$sheet->mergeCells('C'.$NewRow.':C'.($NewRow+5));
		$sheet->setCellValue('C'.$NewRow, strip_tags($alamat_cust->alamat));
		$NewRow	= $NewRow+6;

		$sheet->getStyle('B'.$NewRow.':I'.($NewRow+3))->applyFromArray($all_border);
		$sheet->mergeCells('B'.$NewRow.':C'.($NewRow));
		$sheet->setCellValue('B'.$NewRow, 'Cust.Ref./P.O');

		$sheet->setCellValue('D'.$NewRow, 'Term of Payment');

		$sheet->mergeCells('E'.$NewRow.':F'.($NewRow));
		$sheet->setCellValue('E'.$NewRow, 'Port of Loading');

		$sheet->mergeCells('G'.$NewRow.':H'.($NewRow));
		$sheet->setCellValue('G'.$NewRow, 'Port of Discharges');

		$sheet->setCellValue('I'.$NewRow, 'Flight/Airway-bill No.');

		$NewRow++;
		$sheet->mergeCells('B'.$NewRow.':C'.($NewRow));
		$sheet->setCellValue('B'.$NewRow, $gethd->no_po);
		$sheet->setCellValue('D'.$NewRow, (($gethd->payment_term==0)?"CASH BEFORE DELIVERY":$gethd->payment_term ." Days"));
		$sheet->mergeCells('E'.$NewRow.':F'.($NewRow));
		$sheet->setCellValue('E'.$NewRow, $gethd->port_of_loading);
		$sheet->mergeCells('G'.$NewRow.':H'.($NewRow));
		$sheet->setCellValue('G'.$NewRow, $gethd->port_of_discharges);
		$sheet->setCellValue('I'.$NewRow, $gethd->flight_airway_no);

		$NewRow++;
		$sheet->mergeCells('B'.$NewRow.':C'.($NewRow));
		$sheet->setCellValue('B'.$NewRow, 'Term of Delivery');
		$sheet->setCellValue('D'.$NewRow, 'Ship Via');
		$sheet->mergeCells('E'.$NewRow.':F'.($NewRow));
		$sheet->setCellValue('E'.$NewRow, 'Sailing on/about');
		$sheet->mergeCells('G'.$NewRow.':H'.($NewRow));
		$sheet->setCellValue('G'.$NewRow, 'Vessel / Flight');
		$sheet->setCellValue('I'.$NewRow, 'Currency');

		$NewRow++;
		$sheet->mergeCells('B'.$NewRow.':C'.($NewRow));
		$sheet->setCellValue('B'.$NewRow, $gethd->term_delivery);
		$sheet->setCellValue('D'.$NewRow, $gethd->ship_via);
		$sheet->mergeCells('E'.$NewRow.':F'.($NewRow));
		if($gethd->saliling=="0000-00-00" || $gethd->saliling==""){
		}else{
			$sheet->setCellValue('E'.$NewRow, $gethd->saliling);
		}
		$sheet->mergeCells('G'.$NewRow.':H'.($NewRow));
		$sheet->setCellValue('G'.$NewRow, $gethd->vessel_flight);
		$sheet->setCellValue('I'.$NewRow, 'USD');

		$NewRow	= $NewRow+2;
		$no=0;
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$sheet->getStyle('B'.$NewRow.':I'.($NewRow+1))->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.($NewRow+1));
		$sheet->setCellValue('B'.$NewRow, 'NO');
		$sheet->mergeCells('C'.$NewRow.':D'.($NewRow+1));
		$sheet->setCellValue('C'.$NewRow, 'Description of Goods');
		$sheet->setCellValue('E'.$NewRow, 'Qty');
		$sheet->setCellValue('F'.$NewRow, 'Unit');
		$sheet->setCellValue('G'.$NewRow, 'Price');
		$sheet->setCellValue('H'.$NewRow, 'Disc');
		$sheet->mergeCells('I'.$NewRow.':I'.($NewRow+1));
		$sheet->setCellValue('I'.$NewRow, 'Net Value');
		$NewRow++;
		$sheet->setCellValue('E'.$NewRow, 'Delivered');
		$sheet->setCellValue('G'.$NewRow, ' List');
		$sheet->setCellValue('H'.$NewRow, '%');
		$style_rows = array(
			'borders' => array(
				'left' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'right' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),			
		);
		$style_rows_right = array(
			'borders' => array(
				'left' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'right' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),			
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		foreach($data_detail as $val){
			if($val->harga_total>0){
				$no++;
				$NewRow++;
				$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
				$sheet->setCellValue('B'.$NewRow, $no);
				$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
				$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
				$sheet->setCellValue('C'.$NewRow, $val->desc);
				$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows_right);
				$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
				if($val->qty>0){
					$sheet->setCellValue('E'.$NewRow, $val->qty);
					$sheet->setCellValue('F'.$NewRow, $val->unit);
					$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows_right);
					$sheet->setCellValue('G'.$NewRow, number_format($val->harga_satuan,2));
				}else{
					$sheet->setCellValue('E'.$NewRow, "");
					$sheet->setCellValue('F'.$NewRow, "");
					$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows_right);
					$sheet->setCellValue('G'.$NewRow, "");
				}
				$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows_right);
				$sheet->setCellValue('I'.$NewRow, number_format($val->harga_total,2));
			}
		}
		if($no<20){
			for($no;$no<20;$no++){
				$NewRow++;
				$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
				$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
				$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_rows);
				$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows);
			}
		}
		$NewRow++;
		$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
		$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
		$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
		$sheet->setCellValue('C'.$NewRow, 'TOTAL');
		$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows);
		$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
		$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows);
		$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_rows);
		$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows_right);
		$sheet->setCellValue('I'.$NewRow, number_format($gethd->total_dpp_usd,2));
		if($gethd->total_um > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'DP');
			$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->setCellValue('I'.$NewRow, number_format($gethd->total_um,2));
		}
		if($gethd->total_diskon > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'DISCOUNT');
			$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->setCellValue('I'.$NewRow, number_format($gethd->total_diskon,2));
		}
		if($gethd->total_retensi > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'POTONGAN RETENSI');
			$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->setCellValue('I'.$NewRow, number_format($gethd->total_retensi,2));
		}
		if($gethd->total_ppn > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'TAX');
			$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->setCellValue('I'.$NewRow, number_format($gethd->total_ppn,2));
		}
		if($gethd->total_retensi2 > 0){
			$NewRow++;
			$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_rows);
			$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
			$sheet->setCellValue('C'.$NewRow, 'POTONGAN RETENSI PPN');
			$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_rows);
			$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_rows_right);
			$sheet->setCellValue('I'.$NewRow, number_format($gethd->total_retensi2,2));
		}
		$style_endrows = array(
			'borders' => array(
				'left' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'right' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
				'bottom' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
		  ),			
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$NewRow++;
		$sheet->getStyle('B'.$NewRow.':B'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('C'.$NewRow.':D'.$NewRow)->applyFromArray($style_endrows);
		$sheet->mergeCells('C'.$NewRow.':D'.$NewRow);
		$sheet->getStyle('E'.$NewRow.':E'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('F'.$NewRow.':F'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('G'.$NewRow.':G'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('H'.$NewRow.':H'.$NewRow)->applyFromArray($style_endrows);
		$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_endrows);

		
		$style_center_bold = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$style_left_bold = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$style_total_footer = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT ,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$NewRow++;
		$sheet->getStyle('B'.$NewRow.':H'.($NewRow))->applyFromArray($style_total_footer);
		$sheet->mergeCells('B'.$NewRow.':H'.$NewRow);
		$sheet->setCellValue('B'.$NewRow, 'TOTAL');
		$sheet->getStyle('I'.$NewRow.':I'.$NewRow)->applyFromArray($style_endrows);
		$sheet->setCellValue('I'.$NewRow, number_format($gethd->total_invoice,2));

		$NewRow=$NewRow+3;
		$sheet->setCellValue('F'.$NewRow, 'Please remittance the payment in FULL AMOUNT to :');
		$NewRow++;
		$sheet->getStyle('F'.$NewRow.':F'.($NewRow))->applyFromArray($style_left_bold);
		$sheet->setCellValue('F'.$NewRow, 'PT. ORI POLYTEC COMPOSITES');
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'Mangga Dua Le Grandeur');
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'OCBC NISP');
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'Komplek Dusit Mangga Dua Ruko No 1.');
		$NewRow++;
		$sheet->setCellValue('F'.$NewRow, 'Jl. Mangga Dua Raya, Jakarta Pusat');
		$NewRow++;
		$sheet->getStyle('F'.$NewRow.':F'.($NewRow))->applyFromArray($style_left_bold);
		$sheet->setCellValue('F'.$NewRow, 'Swift Code : NISPIDJA');
		$style_border_bottom = array(
			'borders' => array(
				'bottom' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  ),
		  ),			
		);
		$sheet->getStyle('C'.$NewRow.':C'.($NewRow))->applyFromArray($style_border_bottom);
		$NewRow++;
		$sheet->getStyle('F'.$NewRow.':F'.($NewRow))->applyFromArray($style_left_bold);
		$sheet->setCellValue('F'.$NewRow, 'A/C : (USD)  0278.0001.6993');

		$sheet->getStyle('C'.$NewRow.':C'.($NewRow))->applyFromArray($style_center_bold);
		$sheet->setCellValue('C'.$NewRow, 'VINA ISABELLA');
}
//		$sheet->setTitle('Invoice '.$nomordoc);
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="Invoice'.$nomordoc.'.xls"');
		//unduh file
		$objWriter->save("php://output");		
    }
	
	function uploadfile(){
		$filenames="";
		$id=$this->input->post('noinv');
		if(!empty($_FILES['doc_file']['name'])){
			$_FILES['file']['name'] = $_FILES['doc_file']['name'];
			$_FILES['file']['type'] = $_FILES['doc_file']['type'];
			$_FILES['file']['tmp_name'] = $_FILES['doc_file']['tmp_name'];
			$_FILES['file']['error'] = $_FILES['doc_file']['error'];
			$_FILES['file']['size'] = $_FILES['doc_file']['size'];
			$config['upload_path'] = './assets/invoice/';
			$config['detect_mime'] = FALSE;
			$config['mod_mime_fix'] = FALSE;
			$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			if($extension=='xls' || $extension=='xlsx'){
				$config['allowed_types'] = '*';
			}else{
				$config['allowed_types'] = 'gif|jpg|jpeg|xls|xlsx|pdf|doc|docx|jfif|csv|html|html';
			}
			$config['remove_spaces'] = TRUE;
			$config['encrypt_name'] = TRUE;
//			$config['file_ext_tolower'] = TRUE;
			$this->load->library('upload',$config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('file')){
				$uploadData = $this->upload->data();
				$filenames = $uploadData['file_name'];
				$update_invoice	= "UPDATE tr_invoice_header SET file_inv= '".$filenames."' WHERE id_invoice='".$id."'";
				$this->db->query($update_invoice);
			}else{
				echo $this->upload->display_errors();echo $_FILES['file']['type'];
				die();
			}
		}
		redirect(base_url('invoicing'));
	}

}