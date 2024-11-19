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
        $this->load->model(array('Purchase_order/Purchase_order_model',
		                         'Po_stock/Po_stock_model',
								 'Po_nonstock/Po_nonstock_model',
								 'Po_aset/Po_aset_model',
								 'Jurnal_nomor/Acc_model',
                                 'Jurnal_nomor/Jurnal_model'));
        $this->template->title('Manage Data Purchase Order');
        $this->template->page_icon('fa fa-table');
        date_default_timezone_set("Asia/Bangkok");
		$this->datppn=array('0'=>'Non PPN','10'=>'PPN');
		$this->datcombodata=array('No'=>'No','Asli'=>'Asli','Copy'=>'Copy');		
    }

    public function index()
    {        
        $data = $this->Purchase_order_model->GetListPR('BIAYA');
        $this->template->set('results', $data);
        $this->template->title('Purchase Request Operational Titik (Existing)');
        $this->template->render('list');
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
		$nama_vendor = rawurldecode($nm_vendor);
		
		$tipe		= 'BUK';
		$jenisjurnal = 'pembayaran';
		$data['list_data'] 	    = $this->Jurnal_model->get_detail_jurnal($noreff,$tipe,$jenisjurnal);
		$data['data_perkiraan']	= $this->Acc_model->get_noperkiraan();
		$data['jenis']	        = $kode;
		$data['akses']	        = $akses;
		$data['jenis_jurnal']	= $jenisjurnal;
		$data['po_no']	        = $noreff;
		$data['total_po']		= $total_po;
		$data['id_vendor']		= $id_vendor;
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
		
		$db2 = $this->load->database('accounting', TRUE);
		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_Sales('101',$tgl_po);
       
       
				$Bln 			= substr($tgl_po,5,2);
				$Thn 			= substr($tgl_po,0,4);
				## NOMOR JV ##
				$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_Sales('101',$tgl_po);
				

        			    
        				
        				$dataJVhead = array(
          					'nomor' 	    	=> $Nomor_JV,
          					'tgl'	         	=> $tgl_po,
          					'jml'	            => $total,
          					'koreksi_no'		=> '-',
          					'kdcab'				=> '101',
          					'jenis'			    => 'JV',
          					'keterangan' 		=> $keterangan,
        					'bulan'				=> $Bln,
          					'tahun'				=> $Thn,
          					'user_id'			=> $this->auth->user_id(),
          					'memo'			    => '',
          					'tgl_jvkoreksi'	    => $tgl_po,
          					'ho_valid'			=> ''
          				);
					$db2->insert('javh',$dataJVhead);
		
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
		
		
		$Qry_Update_Cabang_acc	 = "UPDATE pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
        $db2->query($Qry_Update_Cabang_acc);
		
		
		
		if ($jenis =='kasbonnonstok' && $jenis_jurnal =='pembayaran' ){
		$status_tr	 = "UPDATE tr_pr_nonstock_kasbon SET sts_buk=1 WHERE no_pr  = '$reff' ";
		$this->db->query($status_tr);
		}
		elseif ($jenis =='pononstok' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_po_nonstock_request_payment SET sts_buk=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
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
		
		}
		elseif ($jenis =='pononstok' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_po_nonstock_request_payment SET sts_trm=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		}
		elseif ($jenis =='pononstok' && $jenis_jurnal =='approval'){
		$status_tr	 = "UPDATE tr_po_nonstock_request_payment SET sts_apr=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => 0,
				'kredit'          => $total_po,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,
				
                );
        $this->db->insert('tr_kartu_hutang',$datahutang);
		
		}
		elseif ($jenis =='ppnonstok' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_pp_nonstock SET sts_buk=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);	
		
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
		}
		elseif ($jenis =='ppnonstok' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_pp_nonstock SET sts_trm=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		}
		elseif ($jenis =='ppnonstok' && $jenis_jurnal =='approval'){
		$status_tr	 = "UPDATE tr_pp_nonstock SET sts_apr=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		
		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => 0,
				'kredit'          => $total_po,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,
				
                );
        $this->db->insert('tr_kartu_hutang',$datahutang);
		}
		
		elseif ($jenis =='poaset' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_po_aset_request_payment SET sts_buk=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
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
		}
		elseif ($jenis =='poaset' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_po_aset_request_payment SET sts_trm=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		}
		elseif ($jenis =='poaset' && $jenis_jurnal =='approval'){
		$status_tr	 = "UPDATE tr_po_aset_request_payment SET sts_apr=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => 0,
				'kredit'          => $total_po,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,
				
                );
        $this->db->insert('tr_kartu_hutang',$datahutang);
		}
		elseif ($jenis =='ppaset' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_pp_aset SET sts_buk=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		
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
		}
		elseif ($jenis =='ppaset' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_pp_aset SET sts_trm=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		}
		elseif ($jenis =='ppaset' && $jenis_jurnal =='approval'){
		$status_tr	 = "UPDATE tr_pp_aset SET sts_apr=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		
		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => 0,
				'kredit'          => $total_po,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,
				
                );
        $this->db->insert('tr_kartu_hutang',$datahutang);
		}
		elseif ($jenis =='postok' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_pr_po_stok_request_payment SET sts_buk=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
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
		
		}
		elseif ($jenis =='postok' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_pr_po_stok_request_payment SET sts_trm=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		}
		elseif ($jenis =='postok' && $jenis_jurnal =='approval'){
		$status_tr	 = "UPDATE tr_pr_po_stok_request_payment SET sts_apr=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => 0,
				'kredit'          => $total_po,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,
				
                );
        $this->db->insert('tr_kartu_hutang',$datahutang);
		}
		
		elseif ($jenis =='kasbonstok' && $jenis_jurnal =='pembayaran' ){
		$status_tr	 = "UPDATE tr_pr_kasbon_stok SET sts_buk=2 WHERE no_pr  = '$reff' ";
		$this->db->query($status_tr);
		}
		elseif ($jenis =='poaset' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_po_aset_request_payment SET sts_buk=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
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
		}
		elseif ($jenis =='poaset' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_po_aset_request_payment SET sts_trm=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		}
		
		
		elseif ($jenis =='poproduksi1' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_permintaan_bayar_po SET sts_trm=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		}
		
		
		elseif ($jenis =='poproduksi1' && $jenis_jurnal =='approval'){
		$status_tr	 = "UPDATE tr_permintaan_bayar_po SET sts_apr=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => 0,
				'kredit'          => $total_po,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,
				
                );
        $this->db->insert('tr_kartu_hutang',$datahutang);
		}
		
		elseif ($jenis =='poproduksi1' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_permintaan_bayar_po SET sts_buk=2 WHERE no_po  = '$reff' ";
		$this->db->query($status_tr);
		
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
		}
		
		
		
		
		elseif ($jenis =='ppproduksi1' && $jenis_jurnal =='penerimaan'){
		$status_tr	 = "UPDATE tr_permintaan_bayar SET sts_trm=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		}
		
		
		elseif ($jenis =='ppproduksi1' && $jenis_jurnal =='approval'){
		$status_tr	 = "UPDATE tr_permintaan_bayar SET sts_apr=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		
		$datahutang = array(
                'tipe'       	 => $type,
                'nomor'       	 => $Nomor_JV,
                'tanggal'        => $tgl_po,
                'no_perkiraan'    => $this->input->post('no_coa[0]'),
                'keterangan'      => $keterangan,
                'no_reff'     	  => $reff,
				'debet'      	  => 0,
				'kredit'          => $total_po,
				'id_supplier'     => $id_vendor,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_req,
				
                );
        $this->db->insert('tr_kartu_hutang',$datahutang);
		}
		
		
		elseif ($jenis =='ppproduksi1' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_permintaan_bayar SET sts_buk=2 WHERE no_pp  = '$reff' ";
		$this->db->query($status_tr);
		
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
		}
		
		
		
		elseif ($jenis =='kasbonproduksi1' && $jenis_jurnal =='pembayaran'){
		$status_tr	 = "UPDATE tr_pr_kasbon SET sts_buk=2 WHERE no_kasbon  = '$reff' ";
		$this->db->query($status_tr);
		
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
}

