<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penerimaan_np extends CI_Controller {
	
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('penerimaan_np_model');
		$this->load->model('All_model');
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
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

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$so = $this->penerimaan_np_model->get_data_pn();
		$data = array(
			'title'			=> 'Penerimaan Non Product',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'results'			=> $so,
		);
		history('View Revenue');
		$this->load->view('Penerimaan_np/list_payment',$data);
	
    }
		
	public function penerimaan_buktipotong($kd_bayar){
		$noinvoice=$this->db->query("SELECT no_invoice FROM tr_invoice_np_payment_detail WHERE kd_pembayaran = '$kd_bayar' ")->result();
		$buktipotong=$this->db->query("SELECT * FROM tr_invoice_bukti_potong WHERE kd_pembayaran = '$kd_bayar' ")->result();
		$data = array(
			'kodebayar' => $kd_bayar,
			'noinvoice'=>$noinvoice,
			'buktipotong'=>$buktipotong
		);		
		$this->load->view('form_buktipotong', $data);
	}
	
	public function save_buktipotong(){
		$data = array(
					'no_invoice'=>$this->input->post('no_invoice'),
					'tgl_terima'=>$this->input->post('tgl_terima'),
					'kd_pembayaran'=>$this->input->post('kd_pembayaran'),
					'no_bukti_potong'=>$this->input->post('no_bukti_potong'),
					'created_by'=> $this->auth->user_id(),
					'created_date'=> date('Y-m-d H:i:s'),
				);
		$this->db->insert('tr_invoice_bukti_potong',$data);
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
	
	public function create_new(){
		$this->auth->restrict($this->viewPermission);
        	   
			$this->template->page_icon('fa fa-list');			
			$data = '0';			
			$this->template->set('results', $data);
			$this->template->title('Indeks Of Invoice');
			$this->template->render('invoice_siap_terima');
	} 
	
	public function server_side_inv(){
		$this->penerimaan_np_model->get_data_json_inv();
	}
	public function create_penerimaan(){
		$this->invoicing_model->list_top();
	}
	
	public function server_side_payment(){
		$this->penerimaan_np_model->get_data_json_payment(); 
	}
	public function server_side_top(){
		$this->invoicing_model->get_data_json_top();
	}
	
	public function modal_detail_invoice(){ 
		$this->penerimaan_np_model->modal_detail_invoice($this->uri->segment(3));
	}
	
	public function modal_detail_invoice_old(){ 
		$this->penerimaan_np_model->modal_detail_invoice($this->uri->segment(3));
	}
	
	public function view_penerimaan(){
		$kd_bayar = $this->uri->segment(3);		
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Penerimaan_np/view_penerimaan', $data);
	}
	
	public function save_penerimaan(){
		
		// print_r($this->input->post());
		// exit;
		 $session = $this->session->userdata('app_session');
		$Tgl_Invoice        = $this->input->post('tgl_bayar');
		
		$data_session 	    = $this->session->userdata; 
		$kd_bayar 			= $this->penerimaan_np_model->generate_nopn($Tgl_Invoice);
		
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
		$jumlah_total_idr = str_replace(",","",$this->input->post('total_bank'))*$kurs;
		
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
			
			
			$nilai_bayar = str_replace(",","",$this->input->post('jml_bayar')[$i])*$kurs;
			$nilai_jual  = $kurs_jual*str_replace(",","",$this->input->post('jml_bayar')[$i]);
			
			$selisih     = $nilai_bayar - $nilai_jual;
			
			$selisihidr  += $selisih;
			
			$piutangidr  += $nilai_jual;
			
            $datadetail = array(
                'kd_pembayaran'     => $kd_bayar,
                'no_invoice'        => $this->input->post('kode_produk')[$i],
				'no_ipp'        => $this->input->post('no_surat')[$i],
                'nm_customer'       => $this->input->post('nm_customer2')[$i],
                'total_invoice_idr'    => str_replace(",","",$this->input->post('sisa_invoice')[$i]),
				'total_bayar'         => str_replace(",","",$this->input->post('jml_bayar')[$i]),
				'total_bayar_idr'     => str_replace(",","",$this->input->post('jml_bayar')[$i])*$kurs,
				'sisa_invoice_idr'    => str_replace(",","",$this->input->post('sisa_invoice')[$i]) - str_replace(",","",$this->input->post('jml_bayar')[$i]),
				'total_pph_idr'     => str_replace(",","",$this->input->post('pph')[$i]),
				'kurs_jual'				=>$kurs_jual,
				'kurs_bayar'			=>$kurs,
				'total_jual_idr'	    =>$nilai_jual,	
				'selisih_idr'	        =>$selisih,					
                'created_on'    => date('Y-m-d H:i:s'),
                'created_by'    => $session['id_user']
                );
             $this->db->insert('tr_invoice_np_payment_detail',$datadetail);
             //Update QTY_AVL
             $invoice = $this->input->post('kode_produk')[$i];
             $jmlbyr  = str_replace(",","",$this->input->post('jml_bayar')[$i]);
			 
			 $Qry_Update	 = "UPDATE tr_invoice_np_header SET total_bayar_idr=total_bayar_idr + $jmlbyr, sisa_invoice_idr=sisa_invoice_idr - $jmlbyr WHERE no_invoice='$invoice'";
        	 $this->db->query($Qry_Update);


			 $so  = $this->db->query("SELECT * FROM tr_invoice_np_payment WHERE no_invoice='$invoice'")->row();
			 $no_so = $this->input->post('no_surat')[$i];

			 // $Qry_Update_so	 = "UPDATE so_bf_header SET total_bayar_so=total_bayar_so + $jmlbyr WHERE so_number='$no_so'";
        	 // $this->db->query($Qry_Update_so);
			 
			 // $Qry_Update_py	 = "UPDATE tr_invoice_np_payment SET selisih_idr = selisih_idr + $selisih WHERE kd_pembayaran='$kd_bayar'";
        	 // $this->db->query($Qry_Update_py);


        }
		               $tambah_lebih_bayar = $this->input->post('tambah_lebih_bayar');
					
					
		               if($tambah_lebih_bayar != 0){
						   
						 		
						   
        				$data_lebih_bayar[]			= array(
        					  'tgl'                => $this->input->post('tgl_bayar'),
        					  'keterangan'         => $nmcs,
        					  'totalpenerimaan'    => str_replace(",","",$this->input->post('tambah_lebih_bayar')),
        					  'saldo'              => str_replace(",","",$this->input->post('tambah_lebih_bayar')),
        					  'created_on'         => date('Y-m-d H:i:s'),
                              'created_by'         => $session['id_user'],
        					  'bank'         	  => $this->input->post('bank')       					  

        				);
						
						
						//$this->db->insert_batch('tr_unlocated_bank',$data_lebih_bayar);
						
					$Nomor_BUM				= $this->Jurnal_model->get_Nomor_Jurnal_BUM('101',$Tgl_Invoice);
						
					// $Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $Tgl_Invoice);
					$Keterangan_INV1 = 'LEBIH BAYAR ' .$nmcs;
					$Jml_Ttl  = str_replace(",","",$this->input->post('tambah_lebih_bayar'));
					 $Bln = substr($Tgl_Invoice, 5, 2);
                     $Thn = substr($Tgl_Invoice, 0, 4);
					 
					// $dataJVhead = array(
										// 'nomor' => $Nomor_JV, 
										// 'tgl' => $Tgl_Invoice,
										// 'jml' => $Jml_Ttl, 
										// 'koreksi_no' => '-', 
										// 'kdcab' => '101', 
										// 'jenis' => 'JV', 
										// 'keterangan' => $Keterangan_INV1, 
										// 'bulan' => $Bln, 
										// 'tahun' => $Thn, 
										// 'user_id' => $session['id_user'], 
										// 'memo' => '', 
										// 'tgl_jvkoreksi' => $Tgl_Invoice, 
										// 'ho_valid' => ''
										// );
										
						$dataJARH2 = array(
          					'nomor' 	    	=> $Nomor_BUM,
							'kd_pembayaran'    	=> $kd_bayar,
          					'tgl'	         	=> $Tgl_Invoice,
          					'jml'	            => $Jml_Ttl,
          					'kdcab'				=> '101',
          					'jenis_reff'		=> $kd_bayar,
							'no_reff'		    => $kd_bayar,
							'customer'		    => $nmcs,
							'terima_dari'		=> '-',
							'jenis_ar'		    => 'V',
     						'note'				=> $Keterangan_INV1,
        					'valid'				=> $session['id_user'],
          					'tgl_valid'			=> $Tgl_Invoice,
							'user_id'			=> $session['id_user'],
							'tgl_invoice'	    => $Tgl_Invoice,
          					'ho_valid'			=> '',
							'batal'			    => '0'
          				);
										
                        $det_Jurnal_lebih  = array();
					    $det_Jurnal_lebih[]= array(
      					  'nomor'         => $Nomor_BUM,
      					  'tanggal'       => $Tgl_Invoice,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => $kd_bank,
      					  'keterangan'    => $Keterangan_INV1,
      					  'no_reff'       => $kd_bayar,
      					  'debet'         => $Jml_Ttl,
      					  'kredit'        => 0
      				    );
						

						$det_Jurnal_lebih[] = array( 
      					  'nomor'         => $Nomor_BUM,
      					  'tanggal'       => $Tgl_Invoice,
      					  'tipe'          => 'JV',
      					  'no_perkiraan'  => '2109-02-01',
      					  'keterangan'    => $Keterangan_INV1,
      					  'no_reff'       => $kd_bayar,
      					  'debet'         => 0,
      					  'kredit'        => $Jml_Ttl
      				    );
					 
					
					   
					// $this->db->insert(DBACC.'.JARH',$dataJARH2);
        			// $this->db->insert_batch(DBACC.'.jurnal',$det_Jurnal_lebih);
					
					//$this->db->insert(DBACC.'.JARH',$dataJARH2);
        			//$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnal_lebih);
					
					//$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobum=nobum + 1 WHERE nocab='101'";
        			//$this->db->query($Qry_Update_Cabang_acc); 
						
					// $Qry_Update_Cabang_acc = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
					// $this->db->query($Qry_Update_Cabang_acc);
								
		            }
		
		if($id_unlocated !=''){
		$Qry_Update2	 = "UPDATE tr_unlocated_bank SET saldo=saldo - $unlocated WHERE id='$id_unlocated'";
        	 $this->db->query($Qry_Update2);
	    }
			
        // elseif($id_lebihbayar !=''){			
		// $Qry_Update3	 = "UPDATE tr_lebihbayar_bank SET saldo=saldo - $lebihbayar WHERE id='$id_lebihbayar'";
        // 	 $this->db->query($Qry_Update3); 
		// } 
		
		
		$data = array(
						'no_invoice'=>$this->input->post('no_invoice'),
						'kd_pembayaran'=>$kd_bayar,
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
					
		
						
		$this->db->insert('tr_invoice_np_payment',$data);
		
		
		$this->save_jurnal_BUM();
		
		
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
	
	
	public function save_jurnal_BUM(){
		
	   	$kodejurnal = 'BUM02';				
		$nomor      = $this->db->query("SELECT max(id) as id from tr_invoice_np_payment limit 1")->row();
		$id			= $nomor->id;
						
			
		$tr      = $this->db->query("SELECT * from tr_invoice_np_payment where id='$id'")->row();
		$idcust  = $tr->id_customer;
		$tgl_inv = $tr->tgl_pembayaran;
		$total	 = $tr->jumlah_pembayaran_idr;
		
		$nama      =  $tr->nm_customer;
        $nomoripp  =  $tr->kd_pembayaran;
		
		$selisih   = $tr->selisih_idr;
		
		$pph    = $tr->biaya_pph_idr;
		$coaPPH = $tr->jenis_pph; 
		
		$lebihbayar = $tr->tambah_lebih_bayar;
		
		$Nomor_JV				= $this->Jurnal_model->get_Nomor_Jurnal_BUM('101',$tgl_inv);
		$Keterangan_INV		    = 'Penerimaan Invoice '.($nama).' No Penerimaan'.($nomoripp);
       
       
				$Bln 			= substr($tgl_inv,5,2);
				$Thn 			= substr($tgl_inv,0,4);
				     			    
        								
				
				
				$dataJVhead = array(
          					'nomor' 	    	=> $Nomor_JV,
          					'tgl'	         	=> $tgl_inv,
          					'jml'	            => $total,
          					'kdcab'				=> '101',
          					'jenis_reff'	    => 'BUM',
          					'no_reff' 		    => $nomoripp,
							'terima_dari'	    => $nama,
        					'jenis_ar'			=> 'BUM',
							'note'				=> $Keterangan_INV,
							'batal'				=> '0'
          				);
						
			$this->db->insert(DBACC.'.jarh',$dataJVhead);
       
		

        


        $Tgl_Invoice = $tgl_inv;
		$no_request = $id;
		$tgl_voucher =$Tgl_Invoice;

		

		#AMBIL TEMPLATE JURNAL DAN SIMPAN KE JURNAL TRAS
        $kd_bank         = $tr->kd_bank; 
		$jenispph		 = $tr->jenis_pph; 
		
		$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
		foreach($datajurnal AS $record){
			//$nokir  = $record->no_perkiraan;
			$tabel  = $record->menu;
			$posisi = $record->posisi;
			$field  = $record->field;
			if ($field == 'jumlah_bank_idr'){
				$nokir = $kd_bank;
			} elseif ($field == 'biaya_pph_idr'){
				$nokir = $jenispph;
			}else{
				$nokir  = $record->no_perkiraan;
			}
			$no_voucher = $id;
			$param  = 'id';
			$value_param  = $id;
			$val = $this->Acc_model->GetData($tabel,$field,$param,$value_param);
			$nilaibayar = $val[0]->$field;
			
			if ($posisi=='D'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $nilaibayar,
				  'kredit'        => 0,
				  
				 );
			} elseif ($posisi=='K'){
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => $nokir,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $nilaibayar,
				  
				 );
			}
			
		}
		
		if ($selisih < 0 ){
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => '7101-01-02',
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $selisih,
				  'kredit'        => 0,
				  
				 );
			} elseif ($selisih > 0){
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => '7101-01-02',
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $selisih,
				  
				 );
			}
			
			if ($pph > 0){
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => $coaPPH,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $pph,
				  'kredit'        => 0,
				  
				 );
			}
			
			if ($lebihbayar > 0){
				$det_Jurnaltes[]  = array(
				  'nomor'         => $Nomor_JV,
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'BUM',
				  'no_perkiraan'  => '7201-01-03',
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $lebihbayar,
				  
				 );
			}

		$this->db->insert_batch(DBACC.'.jurnal',$det_Jurnaltes);
		
		
		$Qry_Update_Cabang_acc	 = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobum=nobum + 1 WHERE nocab='101'";
        $this->db->query($Qry_Update_Cabang_acc);
		
		$dt      = $this->db->query("SELECT * from tr_invoice_np_payment_detail where kd_pembayaran='$nomoripp'")->result();
		
		foreach($dt as $val)
		{
			$invoice = $val->no_invoice;
			$nilai   = $val->total_bayar_idr;
			
		
		$datapiutang = array(
            'tipe'       	 => 'BUM',
            'nomor'       	 => $Nomor_JV,
            'tanggal'        => $tgl_voucher,
            'no_perkiraan'   => '1102-01-01',
            'keterangan'     => $Keterangan_INV,
            'no_reff'        => $invoice,
            'debet'          => 0,
            'kredit'         => round($nilai),
            'id_supplier'     => $idcust,
            'nama_supplier'   => $nama,
            
            );
        $this->db->insert('tr_kartu_piutang',$datapiutang);
		
		}
		
    }
	
	
	function appr_jurnal(){
		
		
		
	    
        $kd_bayar   = $this->uri->segment(3);
        $session = $this->session->userdata('app_session');

		$data_bayar =  $this->db->query("SELECT * FROM tr_invoice_np_payment WHERE kd_pembayaran = '$kd_bayar' ")->row();

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



					 $data_jurnal = $this->db->query("SELECT * FROM tr_invoice_np_payment_detail WHERE kd_pembayaran = '$kd_bayar' ")->result();

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

						$data_jr = $this->db->query("SELECT * FROM tr_invoice_np_payment_detail WHERE kd_pembayaran = '$kd_bayar' ")->result();

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
						
						$Qry  = "UPDATE tr_invoice_np_payment SET status_jurnal='1' WHERE kd_pembayaran='$kd_bayar'";
        	            $this->db->query($Qry);


                        $this->print_penerimaan_fix();			 			
	
	
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
		$data_bayar =  $this->db->query("SELECT * FROM tr_invoice_np_payment WHERE kd_pembayaran = '$kd_bayar' ")->row();
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
		
		$data_header = $this->db->query("SELECT * FROM tr_invoice_np_header WHERE no_invoice ='$nomordoc'")->row();
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
		
		$this->load->view('Penerimaan_np/invoice', $data);

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
		$data = $this->penerimaan_np_model->get_data_pn_jurnal();			
		$this->template->set('results', $data);
        $this->template->title('Jurnal Penerimaan');
        $this->template->render('index_jurnal_penerimaan');
    }
	function printout($kd_bayar){
		$data = array(
			'kodebayar' => $kd_bayar,		
		);		
		$this->load->view('Penerimaan_np/print_penerimaan', $data);
	}
}