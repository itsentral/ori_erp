<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_mutasi extends CI_Controller {
	
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('request_mutasi_model');
		$this->load->model('All_model');
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
		$this->load->helper('app');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	
	public function index()
    {
       			
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
        $data_session 	    = $this->session->userdata; 
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$so = $this->request_mutasi_model->get_data_pn();
		$data = array(
			'title'			=> 'Request Mutasi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'results'			=> $so,
		);
		history('View Request Mutasi'.$data_session['ORI_User']['username']);
		$this->load->view('Request_mutasi/index',$data);
	
    }
	
	public function mutasi()
    {
       			
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
        $data_session 	    = $this->session->userdata; 
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$so = $this->request_mutasi_model->get_data_mutasi();
		$data = array(
			'title'			=> 'Request Mutasi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'results'			=> $so,
		);
		history('View Request Mutasi'.$data_session['ORI_User']['username']);
		$this->load->view('Request_mutasi/mutasi',$data);
	
    }
	
	
		public function admin()
    {
       			
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
        $data_session 	    = $this->session->userdata; 
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$so = $this->request_mutasi_model->get_data_admin();
		$data = array(
			'title'			=> 'Request Mutasi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'results'			=> $so,
		);
		history('View Request Mutasi'.$data_session['ORI_User']['username']);
		$this->load->view('Request_mutasi/admin',$data);
	
    }
	
	
	function terbilang()
    {
		$nilai=$_GET['nilai'];
		$matauang=$_GET['matauang'];
		$terbilang = ynz_terbilang($nilai);
		echo "$terbilang";
	}
	
	
	public function add_mutasi()
    {
       			
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
        $data_session 	    = $this->session->userdata; 
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$so = $this->request_mutasi_model->get_data_pn_edit($this->uri->segment(3));
		$matauang  	     = $this->All_model->matauang();
		$data = array(
			'title'			=> 'Add Mutasi',
			'action'		=> 'Add',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'results'			=> $so,
			'matauang' => $matauang,
		);
		history('Add Mutasi'.$data_session['ORI_User']['username']);
		$this->load->view('Request_mutasi/add_mutasi',$data);
	
    }
	
	
	
	
	
	public function server_side_inv(){
		$this->request_mutasi_model->get_data_json_inv();
	}
	public function create_penerimaan(){
		$this->invoicing_model->list_top();
	}
	
	public function server_side_payment(){
		$this->request_mutasi_model->get_data_json_payment(); 
	}
	public function server_side_top(){
		$this->invoicing_model->get_data_json_top();
	}
	
	public function modal_detail_invoice(){ 
		$this->request_mutasi_model->modal_detail_invoice($this->uri->segment(3));
	}
	
	public function modal_detail_admin(){ 
		$this->request_mutasi_model->modal_detail_admin($this->uri->segment(3));
	}
	
	public function modal_detail_invoice_draf(){ 
		$this->request_mutasi_model->modal_detail_invoice_draf($this->uri->segment(3));
	}
	
	public function view_penerimaan(){
		$kd_bayar = $this->uri->segment(3);		
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Penerimaan/view_penerimaan', $data);
	}
	
	public function view_penerimaan_draf(){
		$kd_bayar = $this->uri->segment(3);		
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Penerimaan/view_penerimaan_draf', $data);
	}
	
	public function save_request(){
		
		// print_r($this->input->post());
		// exit;
		 $session = $this->session->userdata('app_session');
		$Tgl_Invoice        = $this->input->post('tgl_bayar');
		
		$data_session 	    = $this->session->userdata; 
		$kd_bayar 			= $this->request_mutasi_model->generate_nopn($Tgl_Invoice);
		
		 $this->db->trans_begin();
		
        $dari = $this->input->post('dari');
		$ke = $this->input->post('ke');
        
        $bankasal    = $this->db->query("SELECT * FROM gl.coa_master WHERE no_perkiraan='$dari'")->row();
		
		$banktujuan  = $this->db->query("SELECT * FROM gl.coa_master WHERE no_perkiraan='$ke'")->row();

		$bank_asal    = $bankasal ->nama;
		$bank_tujuan  = $banktujuan->nama;

		
		$data = array(
						'kd_mutasi'=>$kd_bayar,
						'tgl_request'=>$this->input->post('tgl_request'),
						'bank_asal'=>$this->input->post('dari'),
						'bank_tujuan'=>$this->input->post('ke'),
					    'mata_uang'=>$this->input->post('matauang'),
						'nilai_request'=>str_replace(",","",$this->input->post('nilai')),
						'keterangan'=>$this->input->post('keterangan'),
						'terbilang'=>$this->input->post('terbilang'),
						'nama_bank_asal'=>$bank_asal,
						'nama_bank_tujuan'=>$bank_tujuan,
						'created_by'    => $data_session['ORI_User']['username'],
			            'created_on'=> date('Y-m-d H:i:s'),
						
					);
					
		
						
		$this->db->insert('tr_request_mutasi',$data);	
		
		
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
				'nomor'		    => $kd_bayar,
				'pesan'			=> 'Save Process Success. '
		   );
		   
		   history('Input Request Mutasi '.$kd_bayar.' with username : '.$data_session['ORI_User']['username']);
		}
		echo json_encode($Arr_Return);
		
	
	}
	
	
	
	public function save_mutasi(){
		
		// print_r($this->input->post());
		// exit;
		 $session = $this->session->userdata('app_session');
		$Tgl_Invoice        = $this->input->post('tgl_bayar');
		
		$data_session 	    = $this->session->userdata; 
		$kd_bayar 			= $this->request_mutasi_model->generate_nopn($Tgl_Invoice);
		
		$this->db->trans_begin();
		
        $dari = $this->input->post('dari');
		$ke = $this->input->post('ke');
        
        $bankasal    = $this->db->query("SELECT * FROM gl.coa_master WHERE no_perkiraan='$dari'")->row();
		
		$banktujuan  = $this->db->query("SELECT * FROM gl.coa_master WHERE no_perkiraan='$ke'")->row();

		$bank_asal    = $bankasal ->nama;
		$bank_tujuan  = $banktujuan->nama;
		$nilai = str_replace(",","",$this->input->post('rupiah'));
		$terbilang = ynz_terbilang($nilai);
		
		$kd_mutasi = $this->input->post('no_request');

		
		$data = array(
						'kd_mutasi'=>$this->input->post('no_request'),
						'tgl_request'=>$this->input->post('tgl'),
						'bank_asal'=>$this->input->post('dari'),
						'bank_tujuan'=>$this->input->post('ke'),
					    'mata_uang'=>$this->input->post('matauang'),
						'nilai_request'=>str_replace(",","",$this->input->post('nilai')),
						'keterangan'=>$this->input->post('keterangan'),
						'terbilang'=>$terbilang,
						'nama_bank_asal'=>$bank_asal,
						'nama_bank_tujuan'=>$bank_tujuan,
						'created_by'    => $data_session['ORI_User']['username'],
			            'created_on'=> date('Y-m-d H:i:s'),
						'dari'=>$this->input->post('dari_matauang'),
						'ke'=>$this->input->post('ke_matauang'),
						'kurs'=>$this->input->post('kurs'),
						'nilai_aktual'=>str_replace(",","",$this->input->post('rupiah')),
						'tgl_mutasi'=>$this->input->post('tgl_request'),
						
					);
					
		
						
		$this->db->insert('tr_request_mutasi_aktual',$data);	
		
		$Qry_Update	 = "UPDATE tr_request_mutasi SET status='1' WHERE kd_mutasi='$kd_mutasi'";
        $this->db->query($Qry_Update);
		
		
		
		
		
		
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
				'nomor'		    => $kd_mutasi,
				'pesan'			=> 'Save Process Success. '
		   );
		   $this->save_jurnal_jv($kd_mutasi);
		   history('Input Request Mutasi '.$kd_mutasi.' with username : '.$data_session['ORI_User']['username']);
		}
		echo json_encode($Arr_Return);
		
	
	}
	
	
	public function save_jurnal_jv($kd_mutasi){
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		$this->db->trans_begin();
				
		$jurnal = $this->db->query("SELECT * FROM tr_request_mutasi_aktual WHERE kd_mutasi='$kd_mutasi'")->row();
		$tgl_po = $jurnal->tgl_request;
		$keterangan = $jurnal->keterangan;
		$reff       = $kd_mutasi;
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);

		$Bln 			= substr($tgl_po,5,2);
		$Thn 			= substr($tgl_po,0,4);
		
		$dari = $jurnal->dari;
		if($dari =='IDR') {
		$total          = $jurnal->nilai_request;
		$dolar          = $jurnal->nilai_aktual;
		}else{			
		$total          = $jurnal->nilai_aktual;
		$dolar          = $jurnal->nilai_request;	
		}
		
		$nokir_debet = $jurnal->bank_tujuan;;
		$nokir_kredit = $jurnal->bank_asal;
		
		$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $total, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $session['username'], 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');

		$this->db->insert(DBACC.'.javh',$dataJVhead);

      
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_po,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir_debet,
				  'keterangan'    => $keterangan,
				  'no_reff'       => $reff,
				  'debet'         => $total,
				  'kredit'        => 0,
				  'nilai_valas_debet'         => $dolar,
				  'nilai_valas_kredit'        => 0,
				  
				 );
			
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_po,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $nokir_kredit,
				  'keterangan'    => $keterangan,
				  'no_reff'       => $reff,
				  'debet'         => 0,
				  'kredit'        => $total,
				  'nilai_valas_debet'         => 0,
				  'nilai_valas_kredit'        => $dolar,
				  
				 );
				 
				 
				 
	    $this->db->insert_batch(DBACC.'.jurnal',$det_Jurnaltes);
		
		
		$Qry_	 = "UPDATE tr_request_mutasi_aktual SET jurnal1='$Nomor_JV' WHERE kd_mutasi='$kd_mutasi'";
        $this->db->query($Qry_);
		
			
    }
	
	
	public function save_transaksi(){
		
		// print_r($this->input->post());
		// exit;
		 $session = $this->session->userdata('app_session');
		$Tgl_Invoice        = $this->input->post('tgl_request');
		
		$data_session 	    = $this->session->userdata; 
		$jenis = $this->input->post('jenis_transaksi');
		
		if($jenis=='keluar'){
		$kd_bayar 			= $this->request_mutasi_model->generate_notr($Tgl_Invoice);
		}else{
		$kd_bayar 			= $this->request_mutasi_model->generate_nokm($Tgl_Invoice);
		}
		
		 $this->db->trans_begin();
		
        $dari = $this->input->post('dari');
		$ke = $this->input->post('ke');
        
        $bankasal    = $this->db->query("SELECT * FROM gl.coa_master WHERE no_perkiraan='$dari'")->row();
		
		$banktujuan  = $this->db->query("SELECT * FROM gl.coa_master WHERE no_perkiraan='$ke'")->row();

		$bank_asal    = $bankasal ->nama;
		$bank_tujuan  = $banktujuan->nama;

		
		$data = array(
						'kd_mutasi'=>$kd_bayar,
						'tgl_request'=>$this->input->post('tgl_request'),
						'bank_asal'=>$this->input->post('dari'),
						'bank_tujuan'=>$this->input->post('ke'),
					    'mata_uang'=>$this->input->post('matauang'),
						'kurs'=>$this->input->post('kurs'),
						'nilai'=>str_replace(",","",$this->input->post('nilai')),
						'transaksi'=>str_replace(",","",$this->input->post('transaksi')),
						'keterangan'=>$this->input->post('keterangan'),
						'terbilang'=>$this->input->post('terbilang'),
						'nama_bank_asal'=>$bank_asal,
						'nama_bank_tujuan'=>$bank_tujuan,
						'created_by'    => $data_session['ORI_User']['username'],
			            'created_on'=> date('Y-m-d H:i:s'),
						'jenis_transaksi'=>$this->input->post('jenis_transaksi'),
						
					);
					
		
						
		$this->db->insert('tr_request_mutasi_admin',$data);	
		
		    
		
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
				'nomor'		    => $kd_bayar,
				'pesan'			=> 'Save Process Success. '
		   );
		   
		   if($jenis=='keluar'){
			$this->save_jurnal_BUK($kd_bayar);
			}
			elseif($jenis=='terima'){
			 $this->save_jurnal_BUM($kd_bayar);
			}
			
		
		   
		   history('Input Transaksi Bank'.$kd_bayar.' with username : '.$data_session['ORI_User']['username']);
		   
		   
			
		}
		echo json_encode($Arr_Return);
		
	
	}
	
	
	public function save_jurnal_BUM($kd_bayar){
		
				
	   $jurnal = $this->db->query("SELECT * FROM tr_request_mutasi_admin WHERE kd_mutasi='$kd_bayar'")->row();
		$tgl_po = $jurnal->tgl_request;
		$keterangan = $jurnal->keterangan;
		$reff       = $kd_bayar;
		$data_session 	    = $this->session->userdata; 
		$user = $data_session['ORI_User']['username'];
		$jenistransaksi = $jurnal->jenis_transaksi;
		
		$Bln 			= substr($tgl_po,5,2);
		$Thn 			= substr($tgl_po,0,4);
		
		
		$total          = $jurnal->transaksi;
		$dolar          = $jurnal->nilai;
		
		
			
		
		$nokir_debet  = $jurnal->bank_tujuan;
	    $nokir_kredit = $jurnal->bank_asal;
		
		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_BUM('101',$tgl_po);
		$Keterangan_INV		    = $keterangan;
       
       
	     			    
        								
				
				
				$dataJVhead = array(
          					'nomor' 	    	=> $Nomor_JV,
          					'tgl'	         	=> $tgl_po,
          					'jml'	            => $total,
							'kd_pembayaran'     => $kd_bayar,
          					'kdcab'				=> '101',
          					'jenis_reff'	    => 'BUM',
          					'no_reff' 		    => $kd_bayar,
							'terima_dari'	    => $keterangan,
        					'jenis_ar'			=> 'BUM',
							'note'				=> $keterangan,
							'batal'				=> '0'
          				);
						
			    $this->db->insert(DBACC.'.jarh',$dataJVhead);
       
		

        

				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_po,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => $nokir_debet,
				  'keterangan'    => $keterangan,
				  'no_reff'       => $reff,
				  'debet'         => $total,
				  'kredit'        => 0,
				  'nilai_valas_debet'         => $dolar,
				  'nilai_valas_kredit'        => 0,
				  'created_on'   =>date('Y-m-d H:i:s'),
				  'created_by'   =>$user,
				  
				 );
			
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_po,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => $nokir_kredit,
				  'keterangan'    => $keterangan,
				  'no_reff'       => $reff,
				  'debet'         => 0,
				  'kredit'        => $total,
				  'nilai_valas_debet'         => 0,
				  'nilai_valas_kredit'        => $dolar,
				   'created_on'   =>date('Y-m-d H:i:s'),
				  'created_by'   =>$user,
				  
				 );
				 
				 
				 
			$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnaltes);
			$Query_Cab		= "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobum=nobum + 1 WHERE nocab='101'";
		    $Pros_Cab		= $this->db->query($Query_Cab);
			
			$Qry_Update	 = "UPDATE tr_request_mutasi_admin SET jurnal1='$Nomor_JV' WHERE kd_mutasi='$kd_bayar'";
            $this->db->query($Qry_Update);
		
    }
	
	
	public function save_jurnal_BUK($kd_bayar){
		
				
	   $jurnal = $this->db->query("SELECT * FROM tr_request_mutasi_admin WHERE kd_mutasi='$kd_bayar'")->row();
	    $data_session 	    = $this->session->userdata; 
		$user = $data_session['ORI_User']['username'];
		
		$tgl_po = $jurnal->tgl_request;
		$keterangan = $jurnal->keterangan;
		$jenistransaksi = $jurnal->jenis_transaksi;
		$reff       = $kd_bayar;
		
		$Bln 			= substr($tgl_po,5,2);
		$Thn 			= substr($tgl_po,0,4);
		
		
		$total          = $jurnal->transaksi;
		$dolar          = $jurnal->nilai;
		
		if($jenistransaksi=='bank'){
		$nokir_debet  = '1112-01-01';
		}else{
		$nokir_debet  = $jurnal->bank_tujuan;
	    }
		$nokir_kredit = $jurnal->bank_asal;
		
		
		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_BUK2('101',$tgl_po);
		$Keterangan_INV		    = $keterangan;
       
       
	     			    
        								
				
				
				$dataJVhead = array(
          					'nomor' 	    	=> $Nomor_JV,
          					'tgl'	         	=> $tgl_po,
          					'jml'	            => $total,
          					'kdcab'				=> '101',
          					'jenis_reff'	    => 'BUK',
          					'no_reff' 		    => $kd_bayar,
							'bayar_kepada'	    => $nokir_kredit,
        					'jenis_ap'			=> 'BUK',
							'note'				=> $keterangan,
							'batal'				=> '0',
							'user_id'           =>$user,
          				);
						
			    $this->db->insert(DBACC.'.japh',$dataJVhead);
				
			        

				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_po,
				  'tipe'          => 'BUK',
				  'no_perkiraan'  => $nokir_kredit,
				  'keterangan'    => $keterangan,
				  'no_reff'       => $reff,
				  'debet'         => $total,
				  'kredit'        => 0,
				  'nilai_valas_debet'         => $dolar,
				  'nilai_valas_kredit'        => 0,
				  
				 );
			
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_po,
				  'tipe'          => 'BUK',
				  'no_perkiraan'  => $nokir_debet,
				  'keterangan'    => $keterangan,
				  'no_reff'       => $reff,
				  'debet'         => 0,
				  'kredit'        => $total,
				  'nilai_valas_debet'         => 0,
				  'nilai_valas_kredit'        => $dolar,
				  
				 );
				 
				 
				 
			$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnaltes);
			
			
			$Query_Cab		= "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobuk=nobuk + 1 WHERE nocab='101'";
		    $Pros_Cab		= $this->db->query($Query_Cab);
			
			$Qry_Update	 = "UPDATE tr_request_mutasi_admin SET jurnal2='$Nomor_JV' WHERE kd_mutasi='$kd_bayar'";
            $this->db->query($Qry_Update);
		
    }
	
	
	function appr_jurnal(){
		
		
		
	    
        $kd_bayar   = $this->uri->segment(3);
        $session = $this->session->userdata('app_session');

		$data_bayar =  $this->db->query("SELECT * FROM tr_invoice_payment WHERE kd_pembayaran = '$kd_bayar' ")->row();

		$tgl_byr 	= $data_bayar->tgl_pembayaran;
		$kd_invoice    	= $data_bayar->no_invoice; 
		$kd_bank 	= $data_bayar->kd_bank;
		$jenis_pph 	= $data_bayar->jenis_pph;
		$nama	= html_escape($data_bayar->nm_customer);
		$jmlpph   =$data_bayar->total_pph_idr;
		
       $id_cust =  $this->db->query("SELECT * FROM master_customer WHERE name_customer = '$nama'")->row();
	   $idcust  = $id_cust->id_customer;
		
	   
		
     				$No_Inv  = $kd_bayar;
					$Tgl_Inv = $tgl_byr; 
					$Bln 			= substr($Tgl_Inv,6,2);
					$Thn 			= substr($Tgl_Inv,0,4);
					$bulan_bayar = date("n",strtotime($Tgl_Inv));
					$tahun_bayar = date("Y",strtotime($Tgl_Inv));
                    $keterangan_byr  = $data_bayar->keterangan; 
					$jumlah_total    = $data_bayar->jumlah_pembayaran_idr; 
					$jumlah_terima   = $data_bayar->jumlah_bank_idr; 
					$biaya_admin     = $data_bayar->biaya_admin_idr;
                    $biaya_lain     = $data_bayar->biaya_pph_idr;	
                    $deposit         = $data_bayar->lebih_bayar;						
                    $jenis_reff      = $kd_bayar;
					$no_reff         = $kd_bayar;
        				## NOMOR JV ##
        				$Nomor_BUM				= $this->Jurnal_model->get_Nomor_Jurnal_BUM('101',$Tgl_Inv);

						//print_r($Nomor_BUM);
						//exit;


        			     //$Keterangan_INV		    = 'PENERIMAAN MULTI INVOICE A/N '.$nama.' INV NO. '.$No_Inv.
						//' Keterangan :'.$ket_invoice.', Catatan :'.$notes.', No Reff:'.$noreff.', No Pembayaran:'.$kd_pn;

        				$Keterangan_INV		    = 'PENERIMAAN MULTI INVOICE A/N '.$nama.' INV NO. '.$No_Inv.' Keterangan :'.$keterangan_byr;

						$dataJARH = array(
          					'nomor' 	    	=> $Nomor_BUM,
							'kd_pembayaran'    	=> $kd_pembayaran,
          					'tgl'	         	=> $Tgl_Inv,
          					'jml'	            => $jumlah_total,
          					'kdcab'				=> '101',
          					'jenis_reff'		=> $jenis_reff,
							'no_reff'		    => $no_reff,
							'customer'		    => $nama,
							'terima_dari'		=> '-',
							'jenis_ar'		    => 'V',
     						'note'				=> $Keterangan_INV,
        					'valid'				=> $session['id_user'],
          					'tgl_valid'			=> $Tgl_Inv,
							'user_id'			=> $session['id_user'],
							'tgl_invoice'	    => $Tgl_Inv,
          					'ho_valid'			=> '',
							'batal'			    => '0'
          				);

        				$det_Jurnal				= array();
        				$det_Jurnal[]			= array(
        					  'nomor'         => $Nomor_BUM,
        					  'tanggal'       => $Tgl_Inv,
        					  'tipe'          => 'BUM',
        					  'no_perkiraan'  => $kd_bank,
        					  'keterangan'    => $Keterangan_INV,
        					  'no_reff'       => $No_Inv,
        					  'debet'         => $jumlah_terima,
        					  'kredit'        => 0

        				);

						if($biaya_admin != 0){
        				$det_Jurnal[]			= array(
        					  'nomor'         => $Nomor_BUM,
        					  'tanggal'       => $Tgl_Inv,
        					  'tipe'          => 'BUM',
        					  'no_perkiraan'  => '7205-01-01',
        					  'keterangan'    => $Keterangan_INV,
        					  'no_reff'       => $No_Inv, 
        					  'debet'         => $biaya_admin,
        					  'kredit'        => 0

        				);
						}
						
						if($deposit != 0){
        				$det_Jurnal[]			= array(
        					  'nomor'         => $Nomor_BUM,
        					  'tanggal'       => $Tgl_Inv,
        					  'tipe'          => 'BUM',
        					  'no_perkiraan'  => '2109-02-01',
        					  'keterangan'    => $Keterangan_INV,
        					  'no_reff'       => $No_Inv, 
        					  'debet'         => $deposit,
        					  'kredit'        => 0

        				);
						}
						



						// if ($jumlah_piutang2 > $pembayaran){

						// $det_Jurnal[]			  = array(
      					  // 'nomor'         => $Nomor_BUM,
      					  // 'tanggal'       => $Tgl_Inv,
      					  // 'tipe'          => 'BUM',
      					  // 'no_perkiraan'  => $no_account,
      					  // 'keterangan'    => $Keterangan_INV,
      					  // 'no_reff'       => $No_Inv,
      					  // 'debet'         => $selisih,
      					  // 'kredit'        => 0
      				    // );

						// }
						// else if ($jumlah_piutang2 < $pembayaran){
						// $det_Jurnal[]			  = array(
      					  // 'nomor'         => $Nomor_BUM,
      					  // 'tanggal'       => $Tgl_Inv,
      					  // 'tipe'          => 'BUM',
      					  // 'no_perkiraan'  => $no_account,
      					  // 'keterangan'    => $Keterangan_INV,
      					  // 'no_reff'       => $No_Inv,
      					  // 'debet'         => 0,
      					  // 'kredit'        => $selisih
      				    // );

						// }



					 $data_jurnal = $this->db->query("SELECT * FROM tr_invoice_payment_detail WHERE kd_pembayaran = '$kd_bayar' ")->result();

			          foreach($data_jurnal as $jr){
						$jmlbayar   =$jr->total_bayar_idr;
						$invoice2    =$jr->no_invoice;
						
						
						if($biaya_lain != 0){
        				$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_BUM,
      					  'tanggal'       => $Tgl_Inv,
      					  'tipe'          => 'BUM',
      					  'no_perkiraan'  => $jenis_pph,
      					  'keterangan'    => $Keterangan_INV,
      					  'no_reff'       => $No_Inv,
      					  'debet'         => $jmlpph,
      					  'kredit'        => 0
      				    );
						}

						$det_Jurnal[]			  = array( 
      					  'nomor'         => $Nomor_BUM,
      					  'tanggal'       => $Tgl_Inv,
      					  'tipe'          => 'BUM', 
      					  'no_perkiraan'  => '1102-01-01',
      					  'keterangan'    => $Keterangan_INV,
      					  'no_reff'       => $invoice2,
      					  'debet'         => 0,
      					  'kredit'        => $jmlbayar,
      				    );

					  }


        				## INSERT JURNAL ##
        				$this->db->insert(DBACC.'.JARH',$dataJARH);
        				$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnal);

        				## UPDATE AR ##
			            $Query_AR	= "UPDATE ".DBACC.".ar SET kredit=kredit + ".$jumlah_total.", saldo_akhir=saldo_akhir - ".$jumlah_total." WHERE  no_invoice='".$No_Inv."' AND thn='$tahun_bayar' AND bln='$bulan_bayar'";
			            $this->db->query($Query_AR);

        				$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobum=nobum + 1 WHERE nocab='101'";
        				$this->db->query($Qry_Update_Cabang_acc);

    					//PROSES JURNAL

						$data_jr = $this->db->query("SELECT * FROM tr_invoice_payment_detail WHERE kd_pembayaran = '$kd_bayar' ")->result();

						foreach($data_jr as $val){
						$jml   =$val->total_bayar_idr;
						$inv   =$val->no_invoice;

						$Ket_INV		    = 'PENERIMAAN MULTI INVOICE A/N '.$nama.' INV NO. '.$inv.' Keterangan :'.$keterangan_byr;


						$datapiutang = array(
							'tipe'       	 => 'BUM',
							'nomor'       	 => $Nomor_BUM, 
							'tanggal'        => $Tgl_Inv,
							'no_perkiraan'  => '1103-01-01',
        					'keterangan'    => $Ket_INV,
        					'no_reff'       => $inv,
        					'debet'         => 0,
							'kredit'         => $jml,
							'id_supplier'     => $idcust,
							'nama_supplier'   => $nama,

							);



					    $idso=$this->db->insert('tr_kartu_piutang',$datapiutang);  
 
						}	
						
						$Qry  = "UPDATE tr_invoice_payment SET status_jurnal='1' WHERE kd_pembayaran='$kd_bayar'";
        	            $this->db->query($Qry);


                        $this->print_penerimaan_fix();			 			
	
	
	}
	
	
	
	public function save_penerimaan_proforma(){
		
		// print_r($this->input->post());
		// exit;
		 $session = $this->session->userdata('app_session');
		$Tgl_Invoice        = $this->input->post('tgl_bayar');
		
		$data_session 	    = $this->session->userdata; 
		$kd_bayar 			= $this->request_mutasi_model->generate_nopro($Tgl_Invoice);
		
		$pro    = '-PRO';
		$nomor  = $kd_bayar.$pro;
		
		 $this->db->trans_begin();
		
	    if(!empty($this->input->post('bank'))){
            // $bank = explode('|',$this->input->post('bank'));
            // $kd_bank = $bank[0];
            // $nmbank = $bank[1];
			
			$kd_bank  = $this->input->post('bank');
        }
		// print_r($kd_bank);
		// exit;
		$matauang = $this->input->post('matauang');
		if($matauang=='usd'){
		$kurs = str_replace(",","",$this->input->post('kurs'));
		}
		else if($matauang=='idr'){
		$kurs = 1;	
		}
		$jumlah_total_idr = number_format(str_replace(",","",$this->input->post('total_bank'))*$kurs);
		
		$unlocated =  str_replace(",","",$this->input->post('total_bank'));
		$id_unlocated = $this->input->post('id_unlocated'); 
		
		$lebihbayar =  str_replace(",","",$this->input->post('pakai_lebih_bayar'));
		$id_lebihbayar = $this->input->post('id_lebihbayar');
		
		$idcustomer = $this->input->post('customer');
		
		$customer =  $this->db->query("SELECT * FROM customer WHERE id_customer = '$idcustomer'")->row();
	   
	    $idcs   = $customer->id_customer;
		$nmcs	= html_escape($customer->nm_customer);
		
		
		
		    $selisih    = 0;
			$selisihidr = 0;
			$piutangidr=0;
		
		for($i=0;$i < count($this->input->post('kode_produk'));$i++){
			
			if($matauang=='usd'){
			$kurs_jual = str_replace(",","",$this->input->post('kurs_jual')[$i]);
			}
			else if($matauang=='idr'){
			$kurs_jual = 1;	
			}
			
			
			$nilai_bayar = round(str_replace(",","",$this->input->post('jml_bayar')[$i])*$kurs);
			$nilai_jual  = round($kurs_jual*str_replace(",","",$this->input->post('jml_bayar')[$i]));
			$pphidr      = round($kurs_jual* str_replace(",","",$this->input->post('pph')[$i]));
			
			$selisih     = $nilai_bayar - $nilai_jual;
			
			$selisihidr  += $selisih;
			
			$piutangidr  += $nilai_jual;
			
			$data = array(
						'no_invoice'=>$this->input->post('no_invoice'),
						'kd_pembayaran'=>$nomor,
						'jenis_reff'=>'-',
						'no_reff'=>'-',
						'tgl_pembayaran'=>$this->input->post('tgl_bayar'),
						'kurs_bayar'=>$this->input->post('kurs'),
						'jumlah_piutang'=>str_replace(",","",$this->input->post('total_invoice')),
						'jumlah_piutang_idr'=>$piutangidr,
						'jumlah_bank'=>str_replace(",","",$this->input->post('total_bank')),
						'jumlah_bank_idr'=>str_replace(",","",$this->input->post('total_bank'))*$kurs,
						'jumlah_pembayaran'=>str_replace(",","",$this->input->post('total_terima')),
						'jumlah_pembayaran_idr'=>str_replace(",","",$this->input->post('total_terima'))*$kurs,
						'kd_bank'=>$kd_bank,
						'biaya_admin'=>str_replace(",","",$this->input->post('biaya_adm')),
						'biaya_admin_idr'=>str_replace(",","",$this->input->post('biaya_adm'))*$kurs,
						'biaya_pph'=>str_replace(",","",$this->input->post('biaya_pph')),
						'biaya_pph_idr'=>str_replace(",","",$this->input->post('biaya_pph'))*$kurs,
						'created_by'    => $session['id_user'],
			            'created_on'=> date('Y-m-d H:i:s'),
						'jenis_pph'=>$this->input->post('jenis_pph'),
						'no_account'=>'-',
						'selisih'=>'-',
						'selisih_idr'=>$selisihidr,
						'keterangan'=>$this->input->post('ket_bayar'),
						'id_customer'=>$idcs,
						'nm_customer'=>$nmcs,
						'lebih_bayar'=>str_replace(",","",$this->input->post('pakai_lebih_bayar')),
						'tambah_lebih_bayar'=>str_replace(",","",$this->input->post('tambah_lebih_bayar')),

					);
					
		
						
			$this->db->insert('tr_invoice_payment_temp',$data);
		
			
            $datadetail = array(
                'kd_pembayaran'     => $nomor,
                'no_invoice'        => $this->input->post('kode_produk')[$i],
				'no_ipp'        => $this->input->post('no_surat')[$i],
                'nm_customer'       => $this->input->post('nm_customer2')[$i],
                'total_invoice_idr'    => str_replace(",","",$this->input->post('sisa_invoice')[$i]),
				'total_bayar'         => str_replace(",","",$this->input->post('jml_bayar')[$i]),
				'total_bayar_idr'     => round(str_replace(",","",$this->input->post('jml_bayar')[$i])*$kurs),
				'sisa_invoice_idr'    => str_replace(",","",$this->input->post('sisa_invoice')[$i]) - str_replace(",","",$this->input->post('jml_bayar')[$i]),
				'jenis_pph'           => str_replace(",","",$this->input->post('jenis_pph2')[$i]),
				'total_pph'           => str_replace(",","",$this->input->post('pph')[$i]),
				'total_pph_idr'       => $pphidr,
				'kurs_jual'				=>$kurs_jual,
				'kurs_bayar'			=>$kurs,
				'total_jual_idr'	    =>$nilai_jual,	
				'selisih_idr'	        =>$selisih,					
                'created_on'    => date('Y-m-d H:i:s'),
                'created_by'    => $session['id_user']
                );
             $this->db->insert('tr_invoice_payment_detail_temp',$datadetail);
			 
			// $this->printout_draft($nomor); 
						 
			 
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
					'pesan'			=> 'Save Process Success.',
					'nomor'		    => $nomor
			   );
			}
			echo json_encode($Arr_Return);
			
		
			
			 
		}
	}
	
	
	function print_penerimaan_fix(){
	  // $sroot 		= $_SERVER['DOCUMENT_ROOT'];
	  // include $sroot."/application/libraries/MPDF57/mpdf.php";
	  $data_session = $this->session->userdata;
	  $session      = $this->session->userdata('app_session');
	  
	  // print_r($session);
	  // exit;
	  
      $mpdf=new mPDF('utf-8','A5-L');
      $mpdf->SetImportUse();
	    
		$kd_bayar   = $this->uri->segment(3);
		$data_bayar =  $this->db->query("SELECT * FROM tr_invoice_payment WHERE kd_pembayaran = '$kd_bayar' ")->row();
		$coabank    =  $data_bayar->kd_bank;
		$coa        =  $this->db->query("SELECT * FROM ".DBACC.".coa_master WHERE no_perkiraan = '$coabank' ")->row();
	    
        $nomordoc   = html_escape($data_bayar->nm_customer);
		$gethd = $this->db->query("SELECT * FROM master_customer WHERE name_customer='$nomordoc'")->row();
		$tgl       = $gethd->tgl_invoice;
		$Jml_Ttl   = $gethd->total_invoice;
		$Id_klien     = $gethd->id_customer;
		$Nama_klien   = html_escape($gethd->nm_customer);
		$Bln 			= substr($tgl,5,2);
		$Thn 			= substr($tgl,0,4);
		
		$data_header = $this->db->query("SELECT * FROM tr_invoice_header WHERE no_invoice ='$nomordoc'")->row();
        $alamat_cust =  $this->db->query("SELECT * FROM master_customer WHERE id_customer = '$gethd->id_customer'")->row();
		$mso =  $this->db->query("SELECT * FROM mso_proses_header WHERE id_quotation = '$gethd->no_ipp'")->row();
		
		$quot =  $this->db->query("SELECT * FROM quotation_process WHERE id = '$gethd->no_ipp'")->row();
		
		$count = $this->db->query("SELECT COUNT(no_invoice) as total FROM tr_invoice_detail WHERE no_invoice ='$nomordoc'")->row();
		$count1= $count->total;
       
	   
        $total  = $this->invoicing_model->GetInvoiceHeader($nomordoc);
		$detail  = $this->invoicing_model->GetInvoiceDetail($nomordoc);

		$data['inv'] = $data_header;
		$data['quot'] = $quot;
		$data['total'] = $this->invoicing_model->GetInvoiceHeader($nomordoc);
		$data['results']  = $this->invoicing_model->GetInvoiceDetail($nomordoc);
		$data['user']  = $session['username'];
		$data['kodebayar'] = $kd_bayar;
		
		
		 $show = $this->load->view('penerimaan/print_penerimaan',$data,TRUE);
		

       

        $tglprint = date("d-m-Y H:i:s");
		$tglprint2 = date("d-m-Y");
		
		foreach($total as $val){
		$date = tgl_indo($val->tgl_invoice);//date('d-m-Y');
		$invoice  = $val->no_invoice;
		$so  = $val->so_number;
		$total2  = $val->total_invoice;
		$customer  = $val->nm_customer;
		$tagih  = $val->jenis_invoice;
		$persentase  = number_format($val->persentase);
		$persen      ='%';
		
		if($tagih=='TR-01'){
		$jenis_invoice1='DOWN PAYMENT OF ';
		$jenis_invoice=$jenis_invoice1.$persentase.$persen;
		}
		elseif($tagih=='TR-02'){
	    $jenis_invoice1='PAYMENT ';
		$jenis_invoice=$jenis_invoice1.$persentase.$persen;
		}
		else{
		$jenis_invoice='RETENSI';
		}
		
	    }
		
       
        $header = '
          <br>

        	<table width="100%" border="0"  style="font-size:7.5pt !important;max-height:100px;border-spacing:-1px">
			<tr>
  	      		<td width="8%" style="text-align: center;">
  	      			<img src="assets/images/logo.png" style="height: 40px;width: auto;">
  	      		</td>
  	      	</tr>
			</table>
			<br>
			<table width="100%" border="0"  style="font-size:7.5pt !important;max-height:100px;border-spacing:-1px">
			<tr>
  	      		<td style="text-align: center; font-weight: bold; font-size:12pt">
  	      			BUKTI UANG MASUK
  	      		</td>
  	      	</tr>
  	      	</table>
		  <br>
		  <br>
          <table border="0" width="100%">
            <tr><b>
                  <td width="15%" style="font-size:8pt !important;vertical-align:top"><b>Kode Penerimaan</b></td>
				 <td width="1%" style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b>' .@$kd_bayar.'</b></td>
				  <td width="15%" style="font-size:8pt !important;vertical-align:top"><b>Customer</b></td>
				 <td width="3%" style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b>' .@html_escape($gethd->name_customer).'</b></td>
		 </b> </tr>
		 <tr><b>
                 <<td width="10%"style="font-size:8pt !important;vertical-align:top"><b>Tgl Terima</b></td>
                 <td width="1%" style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b>' .@tgl_indo($data_bayar->tgl_pembayaran).'</b></td>
				 <td width="10%" style="font-size:8pt !important;vertical-align:top"><b></b></td> 
                 <td width="1%" style="font-size:8pt !important;vertical-align:top"><b></b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b>' .@$alamat_cust->address_office.'</b></td>
				 
		 </b> </tr>
		  <tr><b> 
		         <td width="10%" style="font-size:8pt !important;vertical-align:top"><b>Bank</b></td>
				 <td width="1%" style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b>'.@$coa->nama.'</b></td>
				 <td width="10%" style="font-size:8pt !important;vertical-align:top"><b></b></td>
				 <td width="1%" style="font-size:8pt !important;vertical-align:top"><b></b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b></b></td>
                
                 
		 </b> </tr> 
		    <tr><b>
                 <td width="10%" style="font-size:8pt !important;vertical-align:top"><b>Keterangan</b></td> 
                 <td width="1%" style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b>' .@$data_bayar->keterangan.'</b></td>
				 <td width="10%" style="font-size:8pt !important;vertical-align:top"><b></b></td>
                 <td width="1%" style="font-size:8pt !important;vertical-align:top"><b></b></td>
				 <td width="35%" style="font-size:8pt !important;vertical-align:top"><b></b></td>
				 
		 </b> </tr>
		 </table>
		    <br>
			
		  <hr> 
		  ';

        $this->mpdf->SetHTMLHeader($header,'0',true);
		
	    
        $this->mpdf->SetHTMLFooter('
        <hr>        
       	<div id="footer">
        <table>
            <tr><td>PT IDEFAB CIPTA - Printed By '.ucwords($session['username']).' On '.$tglprint.' </td></tr>
        </table>
        </div>
        ');
	    
       
         $this->mpdf->AddPageByArray([
                'orientation' => 'L',
                'margin-top' => 60,
                'margin-bottom' => 15,
                'margin-left' => 5,
                'margin-right' => 10,
                'margin-header' => 0,
                'margin-footer' => 0,
            ]);
        $this->mpdf->WriteHTML($show);
        $this->mpdf->Output();
    }

	
	public function unlocated(){ 

		$bank1			 = $this->Jurnal_model->get_Coa_Bank_Cabang('101');
		$pphpenjualan  	 = $this->Acc_model->combo_pph_penjualan(); 
		$datacoa  	     = $this->Acc_model->GetCoaCombo();
		$template  	     = $this->Acc_model->GetTemplate(); 
		$this->template->title('Penerimaan Unlocated');
		
				
		$this->template->set([
		  	'no_inv'  => $id,
			'datbank' => $bank1,
			'pphpenjualan'=> $pphpenjualan,
			'template'=> $template
		]);
		$this->template->render('create_unlocated');
	}
	public function lebihbayar(){ 

		$bank1			 = $this->Jurnal_model->get_Coa_Bank_Cabang('101');
		$pphpenjualan  	 = $this->Acc_model->combo_pph_penjualan(); 
		$datacoa  	     = $this->Acc_model->GetCoaCombo();
		$template  	     = $this->Acc_model->GetTemplate(); 
		$this->template->title('Penerimaan Lebih Bayar');
		
				
		$this->template->set([
		  	'no_inv'  => $id,
			'datbank' => $bank1,
			'pphpenjualan'=> $pphpenjualan,
			'template'=> $template
		]);
		$this->template->render('create_lebihbayar');
	}
    
	public function createunlocated(){ 

		$bank1			 = $this->Jurnal_model->get_Coa_Bank_Cabang('101');
		$pphpenjualan  	 = $this->Acc_model->combo_pph_penjualan(); 
		$datacoa  	     = $this->Acc_model->GetCoaCombo();
		$template  	     = $this->Acc_model->GetTemplate(); 
		$this->template->title('Penerimaan Unlocated');
		
				
		$this->template->set([
		  	//'no_inv'  => $id,
			'datbank' => $bank1,
			'pphpenjualan'=> $pphpenjualan,
			'template'=> $template
		]);
		$this->template->render('create_unlocated');
	}
	
	public function save_unlocated(){
		
		// print_r($this->input->post());
		// exit;
		 $session = $this->session->userdata('app_session');
	     $data_session 	    = $this->session->userdata;
		 
		
	    if(!empty($this->input->post('bank'))){
            $bank = explode('|',$this->input->post('bank'));
            $kd_bank = $bank[0];
            $nmbank = $bank[1];
        }
			
		
		for($i=0;$i < count($this->input->post('keterangan'));$i++){
            $datadetail = array(
                'tgl'               =>  $this->input->post('tanggal'),
                'keterangan'        => $this->input->post('keterangan')[$i],
                'bank'              => $this->input->post('bank'),
                'totalpenerimaan'   => $this->input->post('totalpenerimaan')[$i], 
				'saldo'             => $this->input->post('totalpenerimaan')[$i],
				'created_on'    => date('Y-m-d H:i:s'),
                'created_by'    => $session['id_user']
                );
             $this->db->insert('tr_unlocated_bank',$datadetail);
			 
			 
			 
			        $No_Inv  = $kd_bayar;
					$Tgl_Inv = $this->input->post('tanggal'); 
					$Bln 			= substr($Tgl_Inv,6,2);
					$Thn 			= substr($Tgl_Inv,0,4);
					$bulan_bayar = date("n",strtotime($Tgl_Inv));
					$tahun_bayar = date("Y",strtotime($Tgl_Inv));
                    $keterangan_byr  = $this->input->post('keterangan')[$i];
					$jumlah_total    = $this->input->post('totalpenerimaan')[$i];
						
                   $jenis_reff      = 'Deposit';
					$no_reff         = 'Deposit';
        				## NOMOR JV ##
        				$Nomor_BUM				= $this->Jurnal_model->get_Nomor_Jurnal_BUM('101',$Tgl_Inv);

						$Keterangan_INV		    = 'DEPOSIT CUSTOMER'.$keterangan_byr;

						$dataJARH = array(
          					'nomor' 	    	=> $Nomor_BUM,
							'kd_pembayaran'    	=> $kd_pembayaran,
          					'tgl'	         	=> $Tgl_Inv,
          					'jml'	            => $jumlah_total,
          					'kdcab'				=> '101',
          					'jenis_reff'		=> $jenis_reff,
							'no_reff'		    => $no_reff,
							'customer'		    => 'DEPOSIT CUSTOMER',
							'terima_dari'		=> '-',
							'jenis_ar'		    => 'V',
     						'note'				=> $Keterangan_INV,
        					'valid'				=> $session['id_user'],
          					'tgl_valid'			=> $Tgl_Inv,
							'user_id'			=> $session['id_user'],
							'tgl_invoice'	    => $Tgl_Inv,
          					'ho_valid'			=> '',
							'batal'			    => '0'
          				);

        				

					 
        				$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_BUM,
      					  'tanggal'       => $Tgl_Inv,
      					  'tipe'          => 'BUM',
      					  'no_perkiraan'  => $kd_bank,
      					  'keterangan'    => $Keterangan_INV,
      					  'no_reff'       => 'DEPOSIT CUSTOMER',
      					  'debet'         => $jumlah_total,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal[]			  = array( 
      					  'nomor'         => $Nomor_BUM,
      					  'tanggal'       => $Tgl_Inv,
      					  'tipe'          => 'BUM', 
      					  'no_perkiraan'  => '2101-08-01',
      					  'keterangan'    => $Keterangan_INV,
      					  'no_reff'       => 'DEPOSIT CUSTOMER',
      					  'debet'         => 0,
      					  'kredit'        => $jumlah_total,
      				    );

					  


        				## INSERT JURNAL ##
        				$this->db->insert(DBACC.'.jarh',$dataJARH);
        				$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnal);
						
						$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobum=nobum + 1 WHERE nocab='101'";
        				$this->db->query($Qry_Update_Cabang_acc);

        			
             
        }
		
		
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
	
	public function TambahInvoice()
    {
		$customer = $this->uri->segment(3);
		$data = array(
			'results' => $customer,			
		);
		
		$this->load->view('Penerimaan/invoice', $data);

    }
	
	public function TambahLebihBayar()
    {
		$customer = $this->uri->segment(3);
		$this->auth->restrict($this->viewPermission);
        $session = $this->session->userdata('app_session');
		$this->template->page_icon('fa fa-pencil');
		 $invoice = $this->db->query("SELECT * FROM tr_lebihbayar_bank WHERE saldo !=0 AND id_customer ='$customer'")->result();        
		$data = [
			'detail' => $customer
		];
        $this->template->set('results', $data);
        $this->template->title('List Invoice');
        $this->template->render('lebihbayar');

    }
	
	public function save_lebihbayar(){
		
		// print_r($this->input->post());
		// exit;
		 $session = $this->session->userdata('app_session');
	     $data_session 	    = $this->session->userdata;
		 
		
	    // if(!empty($this->input->post('bank'))){
            // $bank = explode('|',$this->input->post('bank'));
            // $kd_bank = $bank[0];
            // $nmbank = $bank[1];
        // }
			
		
		for($i=0;$i < count($this->input->post('tanggal'));$i++){
            $datadetail = array(
                'tgl'               =>  $this->input->post('tanggal'),
                'keterangan'        => $this->input->post('keterangan'),
                'bank'              => $this->input->post('bank'),
                'totalpenerimaan'   => $this->input->post('totalpenerimaan'),
				'saldo'             => $this->input->post('totalpenerimaan'),
				'created_on'    => date('Y-m-d H:i:s'),
                'created_by'    => $session['id_user']
                );
             $this->db->insert('tr_lebihbayar_bank',$datadetail);
             
        }
		
		
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
	
	public function jurnal_bum()
    {
        $this->auth->restrict($this->viewPermission);
        $session = $this->session->userdata('app_session');
		$this->template->page_icon('fa fa-list');			
		$data = $this->request_mutasi_model->get_data_pn_jurnal();			
		$this->template->set('results', $data);
        $this->template->title('Jurnal Penerimaan');
        $this->template->render('index_jurnal_penerimaan');
    }
	
	public function index_akhir_bulan()
    {
       			
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$so = $this->request_mutasi_model->get_data_invoice();
		$data = array(
			'title'			=> 'Penerimaan',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'results'			=> $so,
		);
		history('View Akhir Bulan');
		$this->load->view('Penerimaan/list_akhir_bulan',$data);
	
    }
	
	
	
	public function update_invoice_akhir_bulan()
    {
       $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$so                 = $this->request_mutasi_model->get_data_pn();
		$data = array(
			'title'			=> 'Update Invoice Akhir Bulan',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'results'		=> $so,
		);
		history('Update Invoice Akhir Bulan');
		$this->load->view('Penerimaan/index_update_invoice_akhir_bulan',$data);
    }
	
	function update_invoice(){
		
    					//UPDATE KURS PIUTANG USD
						$session = $this->session->userdata('app_session');
						$post    = $this->input->post();
						$kurs    = $post['kurs'];
						$tanggal = $post['tgl_update'];
						$bulan 	 =date("m",strtotime($tanggal));
						$thn 	 =date("Y",strtotime($tanggal));
											
						
						$this->db->query("INSERT INTO tr_invoice_tutup_bulan_history (
						id_invoice,
						id_penagihan,
						id_bq,
						no_ipp,
						so_number,
						no_invoice,
						tgl_invoice,
						nm_customer,
						jenis_invoice,
						kurs_jual,
						persentase,
						kurs_bayar,
						total_invoice,
						total_invoice_idr,
						total_bayar,
						total_bayar_idr,
						created_by,
						created_date,
						modified_date,
						modified_by,
						id_top,
						base_cur,
						sisa_invoice_idr,
						sisa_invoice,
						kurs_baru,
						nilai_invoice_baru,
						selisih_debit,
						selisih_kredit,
						tanggal,
						sisa_invoice_retensi2_idr,
						sisa_invoice_retensi2,
						selisih_debit_retensi,
						selisih_kredit_retensi
						) 
						(SELECT 
						id_invoice,
						id_penagihan,
						id_bq,
						no_ipp,
						so_number,
						no_invoice,
						tgl_invoice,
						nm_customer,
						jenis_invoice,
						kurs_jual,
						persentase,
						kurs_bayar,
						total_invoice,
						total_invoice_idr,
						total_bayar,
						total_bayar_idr,
						created_by,
						created_date,
						modified_date,
						modified_by,
						id_top,
						base_cur,
						sisa_invoice_idr,
						sisa_invoice,
						kurs_baru,
						nilai_invoice_baru,
						selisih_debit,
						selisih_kredit,
						tanggal,
						sisa_invoice_retensi2_idr,
						sisa_invoice_retensi2,
						selisih_debit_retensi,
						selisih_kredit_retensi
						FROM tr_invoice_tutup_bulan)");


						$this->db->query("DELETE FROM tr_invoice_tutup_bulan");


						$invoice = $this->db->query("SELECT * FROM tr_invoice_header WHERE Year(tgl_invoice)='$thn' AND month(tgl_invoice)='$bulan' AND base_cur='USD' AND sisa_invoice > 0")->result();

						foreach($invoice as $val){
							
						$nilailama 	   = $val->kurs_jual * $val->sisa_invoice;	
						$nilaibaru 	   = $kurs * $val->sisa_invoice;
						$nilai		   = $nilaibaru;
						$selisih       = $nilailama - $nilaibaru;
						if($selisih > 0){
        			    $selisihdebet  = $selisih;
						$selisihkredit = 0;
						}
						elseif($selisih < 0){
					    $selisihdebet  = 0;
        			    $selisihkredit = $selisih*-1;
						}
						

						$datainvoice = array(
						
						    'id_invoice'       	=> $val->id_invoice,
							'id_penagihan'      => $val->id_penagihan,
							'id_bq'       	 	=> $val->id_bq, 
							'no_ipp'        	=> $val->no_ipp,
							'so_number'  		=> $val->so_number,
        					'no_invoice'        => $val->no_invoice,
        					'tgl_invoice'       => $val->tgl_invoice,
        					'nm_customer'       => $val->nm_customer,
							'jenis_invoice'     => $val->jenis_invoice,
							'kurs_jual'     	=> $val->kurs_jual,
							'persentase'   		=> $val->persentase,
							'kurs_bayar'       	=> $val->kurs_bayar,
							'total_invoice'     => $val->total_invoice, 
							'total_invoice_idr' => $val->total_invoice_idr,
							'total_bayar'  		=> $val->total_bayar,
        					'total_bayar_idr'   => $val->total_bayar_idr,
        					'created_by'       	=> $val->created_by,
        					'created_date'      => $val->created_date,
							'modified_date'   	=> date('Y-m-d H:i:s'),
							'modified_by'    	=> $session['id_user'],
							'id_top'   			=> $val->id_top,
							'base_cur'       	=> $val->base_cur,
							'sisa_invoice_idr'  => $val->sisa_invoice_idr, 
							'sisa_invoice'      => $val->sisa_invoice,
							'kurs_baru'  		=> $kurs,
        					'nilai_invoice_baru'=> $nilai,
        					'selisih_debit'     => $selisihdebet,
        					'selisih_kredit'    => $selisihkredit,
							'tanggal'     		=> $tanggal,
							


							);



					    $idso=$this->db->insert('tr_invoice_tutup_bulan',$datainvoice);  
 
						}		
						
						
						
						
						//UPDATE KURS UNINVOICING USD
						
						
						$this->db->query("INSERT INTO tr_invoice_retensi_tutup_bulan_history (
						id_invoice,
						id_penagihan,
						id_bq,
						no_ipp,
						so_number,
						no_invoice,
						tgl_invoice,
						nm_customer,
						jenis_invoice,
						kurs_jual,
						persentase,
						kurs_bayar,
						total_invoice,
						total_invoice_idr,
						total_bayar,
						total_bayar_idr,
						created_by,
						created_date,
						modified_date,
						modified_by,
						id_top,
						base_cur,
						sisa_invoice_idr,
						sisa_invoice,
						kurs_baru,
						nilai_invoice_baru,
						selisih_debit,
						selisih_kredit,
						tanggal,
						sisa_invoice_retensi2_idr,
						sisa_invoice_retensi2,
						nilai_retensi_baru,
						selisih_debit_retensi,
						selisih_kredit_retensi
						) 
						(SELECT 
						id_invoice,
						id_penagihan,
						id_bq,
						no_ipp,
						so_number,
						no_invoice,
						tgl_invoice,
						nm_customer,
						jenis_invoice,
						kurs_jual,
						persentase,
						kurs_bayar,
						total_invoice,
						total_invoice_idr,
						total_bayar,
						total_bayar_idr,
						created_by,
						created_date,
						modified_date,
						modified_by,
						id_top,
						base_cur,
						sisa_invoice_idr,
						sisa_invoice,
						kurs_baru,
						nilai_invoice_baru,
						selisih_debit,
						selisih_kredit,
						tanggal,
						sisa_invoice_retensi2_idr,
						sisa_invoice_retensi2,
						nilai_retensi_baru,
						selisih_debit_retensi,
						selisih_kredit_retensi
						FROM tr_invoice_retensi_tutup_bulan)");


						$this->db->query("DELETE FROM tr_invoice_retensi_tutup_bulan");


						$invoice2 = $this->db->query("SELECT * FROM tr_invoice_header WHERE Year(tgl_invoice)='$thn' AND month(tgl_invoice)='$bulan' AND base_cur='USD' AND sisa_invoice_retensi2 > 0")->result();

						foreach($invoice2 as $val2){
							
						$nilailama2 	    = $val2->kurs_jual * $val2->sisa_invoice_retensi2;	
						$nilaibaru2 	    = $kurs2 * $val2->sisa_invoice_retensi2;
						$nilai2		   		= $nilaibaru2;
						$selisih2       	= $nilailama2 - $nilaibaru2;
						if($selisih2 > 0){
        			    $selisihdebet2  	= $selisih2;
						$selisihkredit2 	= 0;
						}
						elseif($selisih2 < 0){
					    $selisihdebet2  	= 0;
        			    $selisihkredit2 	= $selisih*-1;
						}
						

						$datainvoice2 = array(
						
						    'id_invoice'       	=> $val2->id_invoice,
							'id_penagihan'      => $val2->id_penagihan,
							'id_bq'       	 	=> $val2->id_bq, 
							'no_ipp'        	=> $val2->no_ipp,
							'so_number'  		=> $val2->so_number,
        					'no_invoice'        => $val2->no_invoice,
        					'tgl_invoice'       => $val2->tgl_invoice,
        					'nm_customer'       => $val2->nm_customer,
							'jenis_invoice'     => $val2->jenis_invoice,
							'kurs_jual'     	=> $val2->kurs_jual,
							'persentase'   		=> $val2->persentase,
							'kurs_bayar'       	=> $val2->kurs_bayar,
							'total_invoice'     => $val2->total_invoice, 
							'total_invoice_idr' => $val2->total_invoice_idr,
							'total_bayar'  		=> $val2->total_bayar,
        					'total_bayar_idr'   => $val2->total_bayar_idr,
        					'created_by'       	=> $val2->created_by,
        					'created_date'      => $val2->created_date,
							'modified_date'   	=> date('Y-m-d H:i:s'),
							'modified_by'    	=> $session['id_user'],
							'id_top'   			=> $val2->id_top,
							'base_cur'       	=> $val2->base_cur,
							'sisa_invoice_idr'  => $val2->sisa_invoice_idr, 
							'sisa_invoice'      => $val2->sisa_invoice,
							'kurs_baru'  		=> $kurs,
        					'nilai_invoice_baru'=> $nilai2,
        					'tanggal'     		=> $tanggal,
							'selisih_debit_retensi'     => $selisihdebet2,
        					'selisih_debit_retensi'    => $selisihkredit2,
							


							);



					    $idso=$this->db->insert('tr_invoice_retensi_tutup_bulan',$datainvoice2);  
 
						}		
						
						
						
						
						
						//UPDATE KURS UANG MUKA CUSTOMER USD
						
						
						$this->db->query("INSERT INTO tr_invoice_retensi_tutup_bulan_history (
						id_invoice,
						id_penagihan,
						id_bq,
						no_ipp,
						so_number,
						no_invoice,
						tgl_invoice,
						nm_customer,
						jenis_invoice,
						kurs_jual,
						persentase,
						kurs_bayar,
						total_invoice,
						total_invoice_idr,
						total_bayar,
						total_bayar_idr,
						created_by,
						created_date,
						modified_date,
						modified_by,
						id_top,
						base_cur,
						sisa_invoice_idr,
						sisa_invoice,
						kurs_baru,
						nilai_invoice_baru,
						selisih_debit,
						selisih_kredit,
						tanggal,
						sisa_invoice_retensi2_idr,
						sisa_invoice_retensi2,
						selisih_debit_retensi,
						selisih_kredit_retensi
						) 
						(SELECT 
						id_invoice,
						id_penagihan,
						id_bq,
						no_ipp,
						so_number,
						no_invoice,
						tgl_invoice,
						nm_customer,
						jenis_invoice,
						kurs_jual,
						persentase,
						kurs_bayar,
						total_invoice,
						total_invoice_idr,
						total_bayar,
						total_bayar_idr,
						created_by,
						created_date,
						modified_date,
						modified_by,
						id_top,
						base_cur,
						sisa_invoice_idr,
						sisa_invoice,
						kurs_baru,
						nilai_invoice_baru,
						selisih_debit,
						selisih_kredit,
						tanggal,
						sisa_invoice_retensi2_idr,
						sisa_invoice_retensi2,
						selisih_debit_retensi,
						selisih_kredit_retensi
						FROM tr_invoice_retensi_tutup_bulan)");


						$this->db->query("DELETE FROM tr_invoice_retensi_tutup_bulan");


						$invoice2 = $this->db->query("SELECT * FROM tr_invoice_header WHERE Year(tgl_invoice)='$thn' AND month(tgl_invoice)='$bulan' AND base_cur='USD' AND sisa_invoice_retensi2 > 0")->result();

						foreach($invoice2 as $val2){
							
						$nilailama2 	    = $val2->kurs_jual * $val2->sisa_invoice_retensi2;	
						$nilaibaru2 	    = $kurs2 * $val2->sisa_invoice_retensi2;
						$nilai2		   		= $nilaibaru2;
						$selisih2       	= $nilailama2 - $nilaibaru2;
						if($selisih2 > 0){
        			    $selisihdebet2  	= $selisih2;
						$selisihkredit2 	= 0;
						}
						elseif($selisih2 < 0){
					    $selisihdebet2  	= 0;
        			    $selisihkredit2 	= $selisih*-1;
						}
						

						$datainvoice2 = array(
						
						    'id_invoice'       	=> $val2->id_invoice,
							'id_penagihan'      => $val2->id_penagihan,
							'id_bq'       	 	=> $val2->id_bq, 
							'no_ipp'        	=> $val2->no_ipp,
							'so_number'  		=> $val2->so_number,
        					'no_invoice'        => $val2->no_invoice,
        					'tgl_invoice'       => $val2->tgl_invoice,
        					'nm_customer'       => $val2->nm_customer,
							'jenis_invoice'     => $val2->jenis_invoice,
							'kurs_jual'     	=> $val2->kurs_jual,
							'persentase'   		=> $val2->persentase,
							'kurs_bayar'       	=> $val2->kurs_bayar,
							'total_invoice'     => $val2->total_invoice, 
							'total_invoice_idr' => $val2->total_invoice_idr,
							'total_bayar'  		=> $val2->total_bayar,
        					'total_bayar_idr'   => $val2->total_bayar_idr,
        					'created_by'       	=> $val2->created_by,
        					'created_date'      => $val2->created_date,
							'modified_date'   	=> date('Y-m-d H:i:s'),
							'modified_by'    	=> $session['id_user'],
							'id_top'   			=> $val2->id_top,
							'base_cur'       	=> $val2->base_cur,
							'sisa_invoice_idr'  => $val2->sisa_invoice_idr, 
							'sisa_invoice'      => $val2->sisa_invoice,
							'kurs_baru'  		=> $kurs,
        					'nilai_invoice_baru'=> $nilai2,
        					'tanggal'     		=> $tanggal,
							'selisih_debit_retensi'     => $selisihdebet2,
        					'selisih_kredit_retensi'    => $selisihkredit2,
							


							);



					    $idso=$this->db->insert('tr_invoice_retensi_tutup_bulan',$datainvoice2);  
 
						}		

						
	
	
					}
					
					function create_jurnal_akhir_bulan(){
						
						
					 $data_jurnal = $this->db->query("SELECT * FROM tr_invoice_tutup_bulan")->result();

			          foreach($data_jurnal as $jr){
						  
						$tanggal  = $jr->tanggal;
						$invoice  = $jr->no_invoice;
						  
						$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);

						$Bln 			= substr($tanggal,5,2);
						$Thn 			= substr($tanggal,0,4);
						
						
						
						$totaldebet     =$jr->selisih_debit;
						$totalkredit    =$jr->selisih_kredit;
						
						$kurs		= $jr->kurs_baru;
						$Id_Inv		= $jr->id_invoice;
						
						$Keterangan    = 'Selisih Kurs Piutang USD $kurs periode $Bln-$Thn'.($invoice);
						
						if($totaldebet != 0){
						$totalselisih = $jr->selisih_debit;
						}else{
						$totalselisih = $jr->selisih_kredit;
						}
						
						$dataJVhead[] = array('nomor' => $Nomor_JV, 
											'tgl' => $tanggal, 
											'jml' => $totalselisih, 
											'koreksi_no' => '-', 
											'kdcab' => '101', 
											'jenis' => 'JV', 
											'keterangan' => $Keterangan, 
											'bulan' => $Bln, 'tahun' => $Thn, 
											'user_id' => '', 
											'memo' => $invoice, 
											'tgl_jvkoreksi' => $tanggal, 
											'ho_valid' => '');

						
						
						
						if($totaldebet != 0){
        				$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '7101-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => $totaldebet,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal[]			  = array( 
      					 'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '1102-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => 0,
      					  'kredit'        => $totaldebet
      				    );
						
						} else {
						
						$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '1102-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => $totalkredit,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal[]			  = array( 
      					 'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '7101-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => 0,
      					  'kredit'        => $totalkredit
      				    );						
							
						}
						
						
						// ## UPDATE AR ##
			            $Query_AR	= "UPDATE tr_invoice_header SET kurs_bayar=$kurs WHERE  id_invoice=$Id_Inv";
			            $this->db->query($Query_AR);

					  }

						## INSERT JURNAL ##
        				$this->db->insert_batch(DBACC.'.javh',$dataJVhead);
        				$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnal);
						$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
        				$this->db->query($Qry_Update_Cabang_acc);
        				

        				
						$this->create_jurnal_akhir_bulan_retensi();
        				
						
					}
					
					
					function create_jurnal_akhir_bulan_retensi(){
						
						
					 $data_jurnal = $this->db->query("SELECT * FROM tr_invoice_retensi_tutup_bulan")->result();

			          foreach($data_jurnal as $jr){
						  
						$tanggal  = $jr->tanggal;
						$invoice  = $jr->no_invoice;
						  
						$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);

						$Bln 			= substr($tanggal,5,2);
						$Thn 			= substr($tanggal,0,4);
						
						
						
						$totaldebet     =$jr->selisih_debit;
						$totalkredit    =$jr->selisih_kredit;
						
						$kurs		= $jr->kurs_baru;
						$Id_Inv		= $jr->id_invoice;
						
						
						$Keterangan    = 'Selisih Kurs UN INVOICING USD $kurs periode $Bln-$Thn'.($invoice);
						
						if($totaldebet != 0){
						$totalselisih = $jr->selisih_debit;
						}else{
						$totalselisih = $jr->selisih_kredit;
						}
						
						$dataJVhead[] = array('nomor' => $Nomor_JV, 
											'tgl' => $tanggal, 
											'jml' => $totalselisih, 
											'koreksi_no' => '-', 
											'kdcab' => '101', 
											'jenis' => 'JV', 
											'keterangan' => $Keterangan, 
											'bulan' => $Bln, 'tahun' => $Thn, 
											'user_id' => '', 
											'memo' => $invoice, 
											'tgl_jvkoreksi' => $tanggal, 
											'ho_valid' => '');

						
						
						
						if($totaldebet != 0){
        				$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '7101-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => $totaldebet,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal[]			  = array( 
      					 'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '1102-01-04',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => 0,
      					  'kredit'        => $totaldebet
      				    );
						
						} else {
						
						$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '1102-01-04',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => $totalkredit,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal[]			  = array( 
      					 'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '7101-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $invoice,
      					  'debet'         => 0,
      					  'kredit'        => $totalkredit
      				    );						
							
						}
						
						
						// ## UPDATE AR ##
			            $Query_AR	= "UPDATE tr_invoice_header SET kurs_bayar=$kurs WHERE  id_invoice=$Id_Inv";
			            $this->db->query($Query_AR);

					  }

						## INSERT JURNAL ##
        				$this->db->insert_batch(DBACC.'.javh',$dataJVhead);
        				$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnal);
						$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
        				$this->db->query($Qry_Update_Cabang_acc);
        				

        				
						$this->index_akhir_bulan();
        				
						
					}
					
					
					public function index_bank_akhir_bulan()
					{
								
						$controller			= ucfirst(strtolower($this->uri->segment(1)));
						$Arr_Akses			= getAcccesmenu($controller);
						if($Arr_Akses['read'] !='1'){
							$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
							redirect(site_url('dashboard'));
						}

						$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
						$so = $this->request_mutasi_model->get_data_bank();
						$data = array(
							'title'			=> 'Penerimaan',
							'action'		=> 'index',
							'row_group'		=> $data_Group,
							'akses_menu'	=> $Arr_Akses,
							'results'			=> $so,
						);
						history('View Akhir Bulan');
						$this->load->view('Penerimaan/list_bank_akhir_bulan',$data);
					
					}
					
					function update_bank(){
						
						//PROSES JURNAL
						$session = $this->session->userdata('app_session');
						$post    = $this->input->post();
						$kurs    = $post['kurs'];
						$tanggal = $post['tgl_update'];
						$bulan =date("m",strtotime($tanggal));
						$thn =date("Y",strtotime($tanggal));
						
						$data_jurnal = $this->db->query("SELECT * FROM tr_saldo_bank")->result();
						 
						foreach($data_jurnal as $jr){
						  
						$bank  = $jr->kd_bank;
						  
						$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);

						$Bln 			= substr($tanggal,5,2);
						$Thn 			= substr($tanggal,0,4);
						
						$Keterangan    = 'Selisih Kurs Bank'.($bank);
						
						$akhir_usd     = $jr->saldo_akhir;
						$akhir_idr     = $jr->saldo_akhir_idr;
						
						$nilai_tutup    = $akhir_usd*$kurs;
						$selisih		= $akhir_idr-$nilai_tutup;
						
						$dataJVhead[] = array('nomor' => $Nomor_JV, 
											'tgl' => $tanggal, 
											'jml' => $selisih, 
											'koreksi_no' => '-', 
											'kdcab' => '101', 
											'jenis' => 'JV', 
											'keterangan' => $Keterangan, 
											'bulan' => $Bln, 'tahun' => $Thn, 
											'user_id' => '', 
											'memo' => $bank, 
											'tgl_jvkoreksi' => $tanggal, 
											'ho_valid' => '');

						
						
						
						if($selisih < 0){
        				$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '7101-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $bank,
      					  'debet'         => $selisih*-1,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal[]			  = array( 
      					 'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => $bank,
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $bank,
      					  'debet'         => 0,
      					  'kredit'        => $selisih*-1,
      				    );
						
						} else {
						
						$det_Jurnal[]			  = array(
      					  'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => $bank,
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $bank,
      					  'debet'         => $selisih,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal[]			  = array( 
      					 'nomor'         => $Nomor_JV,
      					  'tanggal'       => $tanggal,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '7101-01-02',
      					  'keterangan'    => $Keterangan,
      					  'no_reff'       => $bank,
      					  'debet'         => 0,
      					  'kredit'        => $selisih
      				    );						
							
						}
						
						
						// ## UPDATE AR ##
			            // $Query_AR	= "UPDATE tr_invoice_header SET kurs_bayar=$kurs WHERE  id_invoice=$Id_Inv";
			            // $this->db->query($Query_AR);

					  }

						## INSERT JURNAL ##
        				$this->db->insert_batch(DBACC.'.javh',$dataJVhead);
        				$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnal);
						$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
        				$this->db->query($Qry_Update_Cabang_acc);
        				

        				
						$this->index_bank_akhir_bulan();
							
					}
	function printout($kd_bayar){
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Request_mutasi/print_request', $data);
	}
	function printout_mutasi($kd_bayar){
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Request_mutasi/print_mutasi', $data);
	}
	function printout_transaksi($kd_bayar){
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Request_mutasi/print_transaksi', $data);
	}
	
	function printout_pn(){
		$kd_bayar = $this->uri->segment('3');
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Penerimaan/print_penerimaan', $data);
	}
	
	function printout_draft(){
		$kd_bayar = $this->uri->segment('3');
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Penerimaan/print_penerimaandraft', $data);
	}
	
	function update_kartu_hutang(){
	  
	  $detail = $this->db->query("SELECT * FROM purchase_order_request_payment_nm")->result();
	  
	  foreach($detail as $dt){
		  $id 		= $dt->no_payment;	
          if($id !=''){		  
		  $bayar = $this->db->query("SELECT * FROM purchase_order_request_payment_header WHERE no_payment='$id'")->row();
		  $kurs 	= $bayar->curs;
	      $po   	= $dt->nilai_po_invoice;
		  $ppn   	= $dt->invoice_ppn;
		  $tglbayar = $dt->payment_date;
		  $nilai1 	= $kurs*$po; 
		  $nilai 	= $nilai1+$ppn; 
		  $noreff 	= $id;
		  $Qry_hutang	 = "UPDATE tr_kartu_hutang_dicek SET debet=$nilai WHERE no_reff='$noreff' AND no_request='$id'";
          $this->db->query($Qry_hutang);
		  }
		   
	  }
		
	}
}