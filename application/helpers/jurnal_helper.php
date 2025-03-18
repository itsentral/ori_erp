<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	date_default_timezone_set("Asia/Bangkok");
	// ini_set('memory_limit', '255M');

    function insert_jurnal($ArrData,$GudangFrom,$GudangTo,$kode_trans,$category,$ket_min,$ket_plus){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$getHeaderAdjust = $CI->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();
		$DATE_JURNAL = (!empty($getHeaderAdjust[0]->tanggal))?$getHeaderAdjust[0]->tanggal:$getHeaderAdjust[0]->created_date;
		$no_SO = (!empty($getHeaderAdjust[0]->no_so))?$getHeaderAdjust[0]->no_so:$getHeaderAdjust[0]->no_so;

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrDetailNew = [];
// agus
		$det_Jurnaltes = [];
		$datadetail=[];
		$kodejurnal='';
		$jenis_jurnal='';
		$ada_jurnal='';
		
		// print_r($getHeaderAdjust);
		// exit;
		
		$getGudang2 = $CI->db->get_where('warehouse', array('id'=>$GudangTo))->result();		
		$gudang2 = $getGudang2[0]->category;

// ok
		if($category=='transfer pusat - subgudang'){
			$kodejurnal = 'JV002';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
//
		if($category=='gudang pusat - origa'){
			$kodejurnal = 'JV008';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
//
		if($category=='transfer subgudang - produksi'){
			$kodejurnal = 'JV003';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
//
		if($category=='retur material' && $GudangTo =='3'){
			$kodejurnal = 'JV079';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		if($category=='retur material' && $GudangTo =='4'){
			$kodejurnal = 'JV079';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		
		if($category=='retur material' && $GudangTo =='2'){
			$kodejurnal = 'JV080';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		
		if($category=='retur material' && $GudangTo =='1'){
			$kodejurnal = 'JV080';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		if($category=='retur material' && $GudangTo =='27'){
			$kodejurnal = 'JV080';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}

		if($category=='material to FG'){
			$kodejurnal = 'JV056';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		
		if($category=='transfer gudangproject - subgudang project'){
			$kodejurnal = 'JV077';
			$jenis_jurnal = 'pindah gudang project';
			$ada_jurnal='ok';
		}
		
		if($category=='pemakaian gudangproject - subgudang project'){
			$kodejurnal = 'JV078';
			$jenis_jurnal = 'pindah gudang project';
			$ada_jurnal='ok';
		}

		$no_request = $kode_trans;
		$tgl_voucher =date('Y-m-d');
		
		$CI->load->model('jurnal_model');
		$Nomor_JV = $CI->jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
		$Bln	  = substr($tgl_voucher,5,2);
		$Thn	  = substr($tgl_voucher,0,4);
			// end agus		
		foreach ($ArrData as $key => $value) {
			// revisi agus
			if($GudangFrom == 'incoming'){
				$PRICE=$value['unit_price'];
				$bmunit = ($value['bm']/$value['qty_good']);
				$bm     =  $value['bm'];
			//revisi syam 10/07/2024		
			}elseif($GudangFrom == '2'){
				$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key))->result();
				$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
				$bmunit = 0;
				$bm = 0;
			}elseif($GudangFrom == '3'){
				$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
				$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
				$bmunit = 0;
				$bm = 0;
			}elseif($GudangFrom == '30'){
				$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$key))->result();
				$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
				$bmunit = 0;
				$bm = 0;
				
			}
			$SUM_PRICE += $PRICE * $value['qty_good'];
			/*
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$SUM_PRICE += $PRICE * $value['qty_good'];
			*/
           
			$get_coa = $CI->db->order_by('id','desc')->get_where('request_accessories',array('kode'=>$no_SO))->result();	
            $no_so  ="1110-01-19";			
			$coa_gudangtujuan =(!empty($get_coa[0]->sub_gudang))?$get_coa[0]->sub_gudang:$no_so;
			
				
			$ArrDetail[$key]['kode_trans'] = $kode_trans;
			$ArrDetail[$key]['id_material'] = $key;
			$ArrDetail[$key]['price_book'] = $PRICE+$bmunit;
			$ArrDetail[$key]['berat'] = $value['qty_good'];
			$ArrDetail[$key]['amount'] = $PRICE * $value['qty_good']+ $bm;
			$ArrDetail[$key]['updated_by'] = $UserName;
			$ArrDetail[$key]['updated_date'] = $DateTime;

			$ArrDetailNew[$key]['kode_trans'] = $kode_trans;
			$ArrDetailNew[$key]['category'] = $category;
			$ArrDetailNew[$key]['gudang_dari'] = $GudangFrom;
			$ArrDetailNew[$key]['gudang_ke'] = $GudangTo;
			$ArrDetailNew[$key]['tanggal'] = date('Y-m-d',strtotime($DATE_JURNAL));
			$ArrDetailNew[$key]['id_material'] = $key;
			$ArrDetailNew[$key]['nm_material'] = get_name('raw_materials','nm_material','id_material',$key);
			$ArrDetailNew[$key]['cost_book'] = $PRICE+$bmunit;
			$ArrDetailNew[$key]['qty'] = $value['qty_good'];
			$ArrDetailNew[$key]['total_nilai'] = $PRICE * $value['qty_good'] + $bm;
			$ArrDetailNew[$key]['created_by'] = $UserName;
			$ArrDetailNew[$key]['created_date'] = $DateTime;
			// agus 230308 auto approval
			$ArrDetailNew[$key]['approval_by'] = $UserName;
			$ArrDetailNew[$key]['approval_date'] = $DateTime;
			$ArrDetailNew[$key]['status_jurnal'] = '1';
			$ArrDetailNew[$key]['status_id'] = '1';
			#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY
			$nama_mt        = get_name('raw_materials','nm_material','id_material',$key);
			$Keterangan_INV	= $category.','.$key.','.$nama_mt.','.$value['qty_good'].'x'.$PRICE;
			if($ada_jurnal=='ok'){
				$datajurnal  	 = $CI->db->query("select a.* from ".DBACC.".master_oto_jurnal_detail a where kode_master_jurnal='".$kodejurnal."' order by a.posisi,a.parameter_no")->result();
				foreach($datajurnal AS $record){
					$tabel  = $record->menu;
					$posisi = $record->posisi;
					$field  = $record->field;
					$nokir  = $record->no_perkiraan;					
					if($nokir=='1103-02-09'){
					$nokir  = $coa_gudangtujuan;	
					}
										
					$no_voucher = $kode_trans;
					$nilaibayar = ($PRICE * $value['qty_good']) + $bm;
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						'nomor'         => $kode_trans,
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => $nilaibayar,
						'kredit'        => 0,
						'jenis_jurnal'  => $jenis_jurnal,
						'stspos'		  => '1',
						'no_request'    => $no_request
						);
						$datadetail[] = array(
							'tipe'        => 'JV',
							'nomor'       => $Nomor_JV,
							'tanggal'     => $tgl_voucher,
							'no_perkiraan'	=> $nokir,
							'keterangan'	=> $Keterangan_INV,
							'no_reff'		=> $no_request,
							'debet'			=> $nilaibayar,
							'kredit'		=> 0
						);

					} elseif ($posisi=='K'){
						$det_Jurnaltes[]  = array(
						'nomor'         => $kode_trans,
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => 0,
						'kredit'        => $nilaibayar,
						'jenis_jurnal'  => $jenis_jurnal,
						'stspos'		  => '1',
						'no_request'    => $no_request
						);
						$datadetail[] = array(
							'tipe'        => 'JV',
							'nomor'       => $Nomor_JV,
							'tanggal'     => $tgl_voucher,
							'no_perkiraan'	=> $nokir,
							'keterangan'	=> $Keterangan_INV,
							'no_reff'		=> $no_request,
							'debet'			=> 0,
							'kredit'		=> $nilaibayar
						);
					}
				}
			}
			// end agus

		}

		//DEBET
		$ArrJurnal[0]['category'] = $category;
		$ArrJurnal[0]['posisi'] = 'DEBIT';
		$ArrJurnal[0]['amount'] = $SUM_PRICE;
		$ArrJurnal[0]['gudang'] = $GudangTo;
		$ArrJurnal[0]['keterangan'] = $ket_plus;
		$ArrJurnal[0]['kode_trans'] = $kode_trans;
		$ArrJurnal[0]['updated_by'] = $UserName;
		$ArrJurnal[0]['updated_date'] = $DateTime;

		//KREDIT
		$ArrJurnal[1]['category'] = $category;
		$ArrJurnal[1]['posisi'] = 'KREDIT';
		$ArrJurnal[1]['amount'] = $SUM_PRICE;
		$ArrJurnal[1]['gudang'] = $GudangFrom;
		$ArrJurnal[1]['keterangan'] = $ket_min;
		$ArrJurnal[1]['kode_trans'] = $kode_trans;
		$ArrJurnal[1]['updated_by'] = $UserName;
		$ArrJurnal[1]['updated_date'] = $DateTime;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal',$ArrDetailNew);

		//print_r($PRICE);
		// print_r($det_Jurnaltes);
		// exit;
		// agus 230308 auto approval
		if($ada_jurnal=='ok'){
			if(!empty($det_Jurnaltes)){
				$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			}
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $SUM_PRICE+$bm, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $category, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
			if(!empty($det_Jurnaltes)){
				$CI->db->insert_batch(DBACC.'.jurnal',$datadetail);
			}
		}
		// end agus	

		if($GudangFrom == 'incoming'){
			update_price_book($ArrData,$kode_trans);
		}elseif($GudangFrom == '2'){
			update_price_book_subgudang($ArrData,$kode_trans);
		}elseif($GudangFrom == '3'){
			update_price_book_produksi($ArrData,$kode_trans);
		}
		
		
		// if($gudang2 == 'subgudang'){
			// update_price_book_subgudang($ArrData,$kode_trans);
		// }elseif($gudang2 == 'produksi'){
			// update_price_book_produksi($ArrData,$kode_trans);
		// }
		
	}
	
	
	function insert_jurnal_retur($ArrData,$GudangFrom,$GudangTo,$kode_trans,$category,$ket_min,$ket_plus){ 
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$getHeaderAdjust = $CI->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();
		$DATE_JURNAL = (!empty($getHeaderAdjust[0]->tanggal))?$getHeaderAdjust[0]->tanggal:$getHeaderAdjust[0]->created_date;
		$no_SO = (!empty($getHeaderAdjust[0]->no_so))?$getHeaderAdjust[0]->no_so:$getHeaderAdjust[0]->no_so;
		
		$getGudang = $CI->db->get_where('warehouse', array('id'=>$GudangFrom))->result();		
		$gudang = $getGudang[0]->category;
		
		$getGudang2 = $CI->db->get_where('warehouse', array('id'=>$GudangTo))->result();		
		$gudang2 = $getGudang2[0]->category;


		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrDetailNew = [];
// agus
		$det_Jurnaltes = [];
		$datadetail=[];
		$kodejurnal='';
		$jenis_jurnal='';
		$ada_jurnal='';
		
	
		
// ok
		if($category=='retur material' && $GudangTo =='3'){
			$kodejurnal = 'JV079';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		if($category=='retur material' && $GudangTo =='4'){
			$kodejurnal = 'JV079';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		
		if($category=='retur material' && $GudangTo =='2'){
			$kodejurnal = 'JV080';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		
		if($category=='retur material' && $GudangTo =='1'){
			$kodejurnal = 'JV080';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		if($category=='retur material' && $GudangTo =='27'){
			$kodejurnal = 'JV080';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}

		$no_request = $kode_trans;
		$tgl_voucher =date('Y-m-d');
		
		$CI->load->model('jurnal_model');
		$Nomor_JV = $CI->jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
		$Bln	  = substr($tgl_voucher,5,2);
		$Thn	  = substr($tgl_voucher,0,4);
// end agus		
		foreach ($ArrData as $key => $value) {
// revisi agus
			if($gudang == 'produksi'){
				$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_produksi',array('id_material'=>$key))->result();
				$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
				$bmunit = 0;
				$bm = 0;
			}elseif($gudang == 'subgudang'){
				$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
				$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
				$bmunit = 0;
				$bm = 0;
			}
			$SUM_PRICE += $PRICE * $value['qty_good'];
/*
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$SUM_PRICE += $PRICE * $value['qty_good'];
*/
           
		// print_r($PRICE);
		// print_r($key);
		// exit;
		
			
			$get_coa = $CI->db->order_by('id','desc')->get_where('request_accessories',array('kode'=>$no_SO))->result();	
            $no_so  ="1110-01-19";			
			$coa_gudangtujuan =(!empty($get_coa[0]->sub_gudang))?$get_coa[0]->sub_gudang:$no_so;
			
				
			$ArrDetail[$key]['kode_trans'] = $kode_trans;
			$ArrDetail[$key]['id_material'] = $key;
			$ArrDetail[$key]['price_book'] = $PRICE+$bmunit;
			$ArrDetail[$key]['berat'] = $value['qty_good'];
			$ArrDetail[$key]['amount'] = $PRICE * $value['qty_good']+ $bm;
			$ArrDetail[$key]['updated_by'] = $UserName;
			$ArrDetail[$key]['updated_date'] = $DateTime;

			$ArrDetailNew[$key]['kode_trans'] = $kode_trans;
			$ArrDetailNew[$key]['category'] = $category;
			$ArrDetailNew[$key]['gudang_dari'] = $GudangFrom;
			$ArrDetailNew[$key]['gudang_ke'] = $GudangTo;
			$ArrDetailNew[$key]['tanggal'] = date('Y-m-d',strtotime($DATE_JURNAL));
			$ArrDetailNew[$key]['id_material'] = $key;
			$ArrDetailNew[$key]['nm_material'] = get_name('raw_materials','nm_material','id_material',$key);
			$ArrDetailNew[$key]['cost_book'] = $PRICE+$bmunit;
			$ArrDetailNew[$key]['qty'] = $value['qty_good'];
			$ArrDetailNew[$key]['total_nilai'] = $PRICE * $value['qty_good'] + $bm;
			$ArrDetailNew[$key]['created_by'] = $UserName;
			$ArrDetailNew[$key]['created_date'] = $DateTime;
			// agus 230308 auto approval
			$ArrDetailNew[$key]['approval_by'] = $UserName;
			$ArrDetailNew[$key]['approval_date'] = $DateTime;
			$ArrDetailNew[$key]['status_jurnal'] = '1';
			$ArrDetailNew[$key]['status_id'] = '1';
			#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY
			$nama_mt        = get_name('raw_materials','nm_material','id_material',$key);
			$Keterangan_INV	= $category.','.$key.','.$nama_mt.','.$value['qty_good'].'x'.$PRICE;
			if($ada_jurnal=='ok'){
				$datajurnal  	 = $CI->db->query("select a.* from ".DBACC.".master_oto_jurnal_detail a where kode_master_jurnal='".$kodejurnal."' order by a.posisi,a.parameter_no")->result();
				foreach($datajurnal AS $record){
					$tabel  = $record->menu;
					$posisi = $record->posisi;
					$field  = $record->field;
					$nokir  = $record->no_perkiraan;					
					if($nokir=='1103-02-09'){
					$nokir  = $coa_gudangtujuan;	
					}
										
					$no_voucher = $kode_trans;
					$nilaibayar = ($PRICE * $value['qty_good']) + $bm;
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						'nomor'         => $kode_trans,
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => $nilaibayar,
						'kredit'        => 0,
						'jenis_jurnal'  => $jenis_jurnal,
						'stspos'		  => '1',
						'no_request'    => $no_request
						);
						$datadetail[] = array(
							'tipe'        => 'JV',
							'nomor'       => $Nomor_JV,
							'tanggal'     => $tgl_voucher,
							'no_perkiraan'	=> $nokir,
							'keterangan'	=> $Keterangan_INV,
							'no_reff'		=> $no_request,
							'debet'			=> $nilaibayar,
							'kredit'		=> 0
						);

					} elseif ($posisi=='K'){
						$det_Jurnaltes[]  = array(
						'nomor'         => $kode_trans,
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => 0,
						'kredit'        => $nilaibayar,
						'jenis_jurnal'  => $jenis_jurnal,
						'stspos'		  => '1',
						'no_request'    => $no_request
						);
						$datadetail[] = array(
							'tipe'        => 'JV',
							'nomor'       => $Nomor_JV,
							'tanggal'     => $tgl_voucher,
							'no_perkiraan'	=> $nokir,
							'keterangan'	=> $Keterangan_INV,
							'no_reff'		=> $no_request,
							'debet'			=> 0,
							'kredit'		=> $nilaibayar
						);
					}
				}
			}
			// end agus

		}

		//DEBET
		$ArrJurnal[0]['category'] = $category;
		$ArrJurnal[0]['posisi'] = 'DEBIT';
		$ArrJurnal[0]['amount'] = $SUM_PRICE;
		$ArrJurnal[0]['gudang'] = $GudangTo;
		$ArrJurnal[0]['keterangan'] = $ket_plus;
		$ArrJurnal[0]['kode_trans'] = $kode_trans;
		$ArrJurnal[0]['updated_by'] = $UserName;
		$ArrJurnal[0]['updated_date'] = $DateTime;

		//KREDIT
		$ArrJurnal[1]['category'] = $category;
		$ArrJurnal[1]['posisi'] = 'KREDIT';
		$ArrJurnal[1]['amount'] = $SUM_PRICE;
		$ArrJurnal[1]['gudang'] = $GudangFrom;
		$ArrJurnal[1]['keterangan'] = $ket_min;
		$ArrJurnal[1]['kode_trans'] = $kode_trans;
		$ArrJurnal[1]['updated_by'] = $UserName;
		$ArrJurnal[1]['updated_date'] = $DateTime;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal',$ArrDetailNew);

		// print_r($PRICE);
		// print_r($det_Jurnaltes);
		// exit;
		// agus 230308 auto approval
		if($ada_jurnal=='ok'){
			if(!empty($det_Jurnaltes)){
				$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			}
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $SUM_PRICE+$bm, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $category, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
			if(!empty($det_Jurnaltes)){
				$CI->db->insert_batch(DBACC.'.jurnal',$datadetail);
			}
		}
		// end agus	

		if($gudang2 == 'subgudang'){
			update_price_book_subgudang_retur($ArrData,$kode_trans);
		}elseif($gudang2 == 'pusat'){
			update_price_book_pusat_retur($ArrData,$kode_trans);
		}
	}
	

	function insert_jurnal_wip($ArrData,$GudangFrom,$GudangTo,$category,$ket_min,$ket_plus,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrDetailNew = [];
		$temp = [];
		$nomor = 0;
		foreach ($ArrData as $key => $data_detail) {
			foreach ($data_detail as $key2 => $value) { $nomor++;
				$key_uniq = $key.'-'.$value['id_spk'].'-'.$value['spk'];
				if(!array_key_exists($key_uniq, $temp)) {
					$temp[$key_uniq] = 0;
				}
				$temp[$key_uniq] += $value['amount'];

				//DETAIL MATERIAL JURNAL NEW
				$COSTBOOK = get_price_book($value['id_material']);
				$ArrDetailNew[$nomor]['kode_trans'] 	= $kode_trans;
				$ArrDetailNew[$nomor]['category'] 		= $category;
				$ArrDetailNew[$nomor]['gudang_dari'] 	= $GudangFrom;
				$ArrDetailNew[$nomor]['gudang_ke'] 		= $GudangTo;
				$ArrDetailNew[$nomor]['tanggal'] 		= date('Y-m-d');
				$ArrDetailNew[$nomor]['no_ipp']			= $value['no_ipp'];
				$ArrDetailNew[$nomor]['no_so'] 			= get_name('so_number','so_number','id_bq','BQ-'.$value['no_ipp']);
				$ArrDetailNew[$nomor]['product'] 		= $value['product'];
				$ArrDetailNew[$nomor]['id_detail'] 		= $key;
				$ArrDetailNew[$nomor]['id_milik'] 		= $value['id_milik'];
				$ArrDetailNew[$nomor]['id_spk'] 		= $value['id_spk'];
				$ArrDetailNew[$nomor]['spk'] 			= $value['spk'];
				$ArrDetailNew[$nomor]['id_material'] 	= $value['id_material'];
				$ArrDetailNew[$nomor]['nm_material'] 	= get_name('raw_materials','nm_material','id_material',$value['id_material']);
				$ArrDetailNew[$nomor]['spec'] 			= spec_bq2($value['id_milik']);
				$ArrDetailNew[$nomor]['cost_book'] 		= $COSTBOOK;
				$ArrDetailNew[$nomor]['qty'] 			= $value['qty'];
				$ArrDetailNew[$nomor]['total_nilai'] 	= $value['qty'] * $COSTBOOK;
				$ArrDetailNew[$nomor]['created_by'] 	= $UserName;
				$ArrDetailNew[$nomor]['created_date'] 	= $DateTime;

				$SPK_TYPE = $value['spk'];

			}
		}

		foreach ($temp as $key => $value) {$nomor++;
			$EXPLODE = explode('-',$key);
			$SUM_PRICE += $value;

			$kode_transx = $kode_trans.'/'.$EXPLODE[2];

			$ArrDetail[$nomor]['kode_trans'] = $kode_transx;
			$ArrDetail[$nomor]['hub_product'] = $key;
			$ArrDetail[$nomor]['id_material'] = $EXPLODE[0];
			$ArrDetail[$nomor]['price_book'] = NULL;
			$ArrDetail[$nomor]['berat'] = NULL;
			$ArrDetail[$nomor]['amount'] = $value;
			$ArrDetail[$nomor]['updated_by'] = $UserName;
			$ArrDetail[$nomor]['updated_date'] = $DateTime;

			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = $GudangTo;
			$ArrJurnal[$nomor]['keterangan'] = $ket_plus;
			$ArrJurnal[$nomor]['kode_trans'] = $kode_transx;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;
		}

		//KREDIT
		$ArrJurnal[999]['category'] = $category;
		$ArrJurnal[999]['posisi'] = 'KREDIT';
		$ArrJurnal[999]['amount'] = $SUM_PRICE;
		$ArrJurnal[999]['gudang'] = $GudangFrom;
		$ArrJurnal[999]['keterangan'] = $ket_min;
		$ArrJurnal[999]['kode_trans'] = $kode_transx;
		$ArrJurnal[999]['hub_product'] = $key;
		$ArrJurnal[999]['updated_by'] = $UserName;
		$ArrJurnal[999]['updated_date'] = $DateTime;

		$CI->db->where('kode_trans',$kode_transx);
		$CI->db->delete('jurnal_temp');

		$CI->db->where('kode_trans',$kode_transx);
		$CI->db->delete('jurnal_temp_detail');

		$CI->db->where('kode_trans',$kode_trans);
		$CI->db->where('spk',$SPK_TYPE);
		$CI->db->update('jurnal',array('status_id'=>'0'));

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal',$ArrDetailNew);
		
		//auto_jurnal_produksi($ArrDetailNew,'PRODUKSI - WIP');
	}

	function insert_jurnal_qc($ArrData, $kode_pro){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailNew = [];
		$ArrDetailProduct = [];
		$nomor = 0;

		// echo "Masuk";
		// print_r($ArrData);
		// exit;

		$temp = [];
		$category = 'quality control';
		foreach ($ArrData as $key => $value) {$nomor++;
			$get_detProduksi	= $CI->db->select('id_produksi, kode_spk, print_merge_date AS data_uniq, id_milik, product_ke, product_code, no_spk, id_category AS product')->get_where('production_detail',array('id'=>$value))->result();
			$data_uniq 			= $get_detProduksi[0]->data_uniq;
			$kode_spk 			= $get_detProduksi[0]->kode_spk;
			$kode_trans 		= $kode_spk.'/'.$data_uniq;
			$id_milik 			= $get_detProduksi[0]->id_milik;
			$product_ke 		= $get_detProduksi[0]->product_ke;
			$product_code 		= explode('.',$get_detProduksi[0]->product_code);
			$no_spk 			= $get_detProduksi[0]->no_spk;
			$product 			= $get_detProduksi[0]->product;
			
			$no_ipp 			= str_replace('PRO-','',$get_detProduksi[0]->id_produksi);
			$get_so 			= explode('-',$get_detProduksi[0]->product_code);

			$keterangan 		= $product.'/'.$id_milik.'/'.$product_code[0].'.'.$product_ke.'/'.$no_spk.'/'.$kode_pro;

			$get_detJurnal 		= $CI->db->select('SUM(amount) AS amount, hub_product')->from('jurnal_temp_detail')->where('id_material',$id_milik)->like('kode_trans',$kode_trans, 'after')->get()->result();
			
			$AMOUNT = 0;
            $hubX = 0;
			if(!empty($get_detJurnal[0]->hub_product)){
				$AMOUNT_SUM 		= $get_detJurnal[0]->amount;
				$hub_product 		= explode('-',$get_detJurnal[0]->hub_product);
                $hubX = $hub_product[1];
				$get_qty_product	= $CI->db->select('qty,id')->get_where('production_spk_parsial',array('id_spk'=>$hubX,'created_date'=>$data_uniq,'kode_spk'=>$kode_spk))->result();
				
				$qty 				= (!empty($get_qty_product[0]->qty))?$get_qty_product[0]->qty:0;
				$AMOUNT 			= 0;
				if($qty != 0){
					$AMOUNT 		= $AMOUNT_SUM / $qty;
				}
				
				
				//DETAIL MATERIAL JURNAL NEW
				$copy_jurnal = $CI->db->select('*')->from('jurnal')->where('id_milik',$id_milik)->where('category','laporan produksi')->like('kode_trans',$kode_trans, 'after')->get()->result_array();
				
				// print_r($copy_jurnal);
				// exit;
				if(!empty($copy_jurnal)){
					foreach ($copy_jurnal as $keyX2 => $valueX2) {$nomor++;
						$ArrDetailNew[$nomor]['kode_trans'] 	= $kode_trans;
						$ArrDetailNew[$nomor]['category'] 		= $category;
						$ArrDetailNew[$nomor]['gudang_dari'] 	= 14;
						$ArrDetailNew[$nomor]['gudang_ke'] 		= 15;
						$ArrDetailNew[$nomor]['tanggal'] 		= date('Y-m-d');
						$ArrDetailNew[$nomor]['no_ipp']			= $valueX2['no_ipp'];
						$ArrDetailNew[$nomor]['no_so'] 			= $valueX2['no_so'];
						$ArrDetailNew[$nomor]['product'] 		= $valueX2['product'];
						$ArrDetailNew[$nomor]['id_detail'] 		= $valueX2['id_detail'];
						$ArrDetailNew[$nomor]['id_milik'] 		= $valueX2['id_milik'];
						$ArrDetailNew[$nomor]['id_spk'] 		= $valueX2['id_spk'];
						$ArrDetailNew[$nomor]['spk'] 			= $valueX2['spk'];
						$ArrDetailNew[$nomor]['id_material'] 	= $valueX2['id_material'];
						$ArrDetailNew[$nomor]['nm_material'] 	= $valueX2['nm_material'];
						$ArrDetailNew[$nomor]['spec'] 			= $valueX2['spec'];
						$ArrDetailNew[$nomor]['cost_book'] 		= $valueX2['cost_book'];
						$ArrDetailNew[$nomor]['qty'] 			= $valueX2['qty'];
						
						$ArrDetailNew[$nomor]['created_by'] 	= $UserName;
						$ArrDetailNew[$nomor]['created_date'] 	= $DateTime;
						
						if($qty != 0){
							$ArrDetailNew[$nomor]['total_nilai'] 	= $valueX2['total_nilai'] / $qty;
						}
						else{
							$ArrDetailNew[$nomor]['total_nilai'] 	= 0;
						}
					}
				}
			}

			$ArrUpdate[$nomor]['id'] = $value;
			$ArrUpdate[$nomor]['amount'] = $AMOUNT;
			
			$ArrDetailProduct[$nomor]['kode_trans'] 	= $kode_trans;
			$ArrDetailProduct[$nomor]['category'] 		= $category;
			$ArrDetailProduct[$nomor]['gudang_dari'] 	= 14;
			$ArrDetailProduct[$nomor]['gudang_ke'] 		= 15;
			$ArrDetailProduct[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailProduct[$nomor]['no_ipp']			= $no_ipp;
			$ArrDetailProduct[$nomor]['no_so'] 			= $get_so[0];
			$ArrDetailProduct[$nomor]['product'] 		= $product;
			$ArrDetailProduct[$nomor]['id_detail'] 		= $value;
			$ArrDetailProduct[$nomor]['id_milik'] 		= $id_milik;
			$ArrDetailProduct[$nomor]['id_spk'] 		= $no_spk;
			$ArrDetailProduct[$nomor]['spec'] 			= spec_bq2($id_milik);
			$ArrDetailProduct[$nomor]['total_nilai'] 	= $AMOUNT;
			$ArrDetailProduct[$nomor]['created_by'] 	= $UserName;
			$ArrDetailProduct[$nomor]['created_date'] 	= $DateTime;

			$ArrDetail[$nomor]['kode_trans'] = $keterangan;
			$ArrDetail[$nomor]['hub_product'] = $id_milik.'-'.$hubX;
			$ArrDetail[$nomor]['id_material'] = $id_milik;
			$ArrDetail[$nomor]['price_book'] = NULL;
			$ArrDetail[$nomor]['berat'] = NULL;
			$ArrDetail[$nomor]['amount'] = $AMOUNT;
			$ArrDetail[$nomor]['updated_by'] = $UserName;
			$ArrDetail[$nomor]['updated_date'] = $DateTime;

			$key_uniq = $id_milik;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $AMOUNT;

		}
		
		$kredit = 999;
		
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 15;
			$ArrJurnal[$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 14;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'wip';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key.'-'.$key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}
		// print_r($ArrJurnal);
		// print_r($ArrDetail);
		// print_r($ArrDetailNew);
		// print_r($ArrDetailProduct);
		// print_r($ArrUpdate);
		// exit;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		if(!empty($ArrDetailNew)){
			$CI->db->insert_batch('jurnal',$ArrDetailNew);
		}
		$CI->db->insert_batch('jurnal_product',$ArrDetailProduct);
		auto_jurnal_product($ArrDetailProduct,'WIP - FINISH GOOD');
		if(!empty($ArrUpdate)){
			$CI->db->update_batch('production_detail',$ArrUpdate,'id');
		}
	}

	function insert_jurnal_delivery($ArrData, $kode_pro){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailProduct = [];
		$nomor = 0;
		$category = 'delivery';
		$temp = [];

		foreach ($ArrData as $key => $value) {$nomor++;
			$get_detProduksi	= $CI->db->select('id_produksi, kode_spk, print_merge_date AS data_uniq, id_milik, product_ke, product_code, no_spk, id_category AS product, amount, id')->get_where('production_detail',array('id'=>$value))->result();
			$data_uniq 			= $get_detProduksi[0]->data_uniq;
			$kode_spk 			= $get_detProduksi[0]->kode_spk;
			$kode_trans 		= $kode_spk.'/'.$data_uniq;
			$id_milik 			= $get_detProduksi[0]->id_milik;
			$product_ke 		= $get_detProduksi[0]->product_ke;
			$product_code 		= explode('.',$get_detProduksi[0]->product_code);
			$no_spk 			= $get_detProduksi[0]->no_spk;
			$product 			= $get_detProduksi[0]->product;
			$AMOUNT 			= $get_detProduksi[0]->amount;
			$id 				= $get_detProduksi[0]->id;
			
			$no_ipp 			= str_replace('PRO-','',$get_detProduksi[0]->id_produksi);
			$get_so 			= explode('-',$get_detProduksi[0]->product_code);

			$keterangan 		= $product.'/'.$id_milik.'/'.$product_code[0].'.'.$product_ke.'/'.$no_spk.'/'.$kode_pro;
			
			$ArrDetailProduct[$nomor]['kode_trans'] 	= $kode_trans;
			$ArrDetailProduct[$nomor]['category'] 		= $category;
			$ArrDetailProduct[$nomor]['gudang_dari'] 	= 15;
			$ArrDetailProduct[$nomor]['gudang_ke'] 		= 20;
			$ArrDetailProduct[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailProduct[$nomor]['no_ipp']			= $no_ipp;
			$ArrDetailProduct[$nomor]['no_so'] 			= $get_so[0];
			$ArrDetailProduct[$nomor]['product'] 		= $product;
			$ArrDetailProduct[$nomor]['id_detail'] 		= $value;
			$ArrDetailProduct[$nomor]['id_milik'] 		= $id_milik;
			$ArrDetailProduct[$nomor]['id_spk'] 		= $no_spk;
			$ArrDetailProduct[$nomor]['spec'] 			= spec_bq2($id_milik);
			$ArrDetailProduct[$nomor]['total_nilai'] 	= $AMOUNT;
			$ArrDetailProduct[$nomor]['created_by'] 	= $UserName;
			$ArrDetailProduct[$nomor]['created_date'] 	= $DateTime;
			$ArrDetailProduct[$nomor]['no_surat_jalan']	= $kode_pro;

			$ArrDetail[$nomor]['kode_trans'] = $keterangan;
			$ArrDetail[$nomor]['hub_product'] = $id_milik.'-'.$id;
			$ArrDetail[$nomor]['id_material'] = $id_milik;
			$ArrDetail[$nomor]['price_book'] = NULL;
			$ArrDetail[$nomor]['berat'] = NULL;
			$ArrDetail[$nomor]['amount'] = $AMOUNT;
			$ArrDetail[$nomor]['updated_by'] = $UserName;
			$ArrDetail[$nomor]['updated_date'] = $DateTime;

			$key_uniq = $id_milik;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $AMOUNT;
		}

		$kredit = 999;
		
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 20;
			$ArrJurnal[$nomor]['keterangan'] = 'transit';
			$ArrJurnal[$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 15;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}

		//$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		//$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal_product',$ArrDetailProduct);
		auto_jurnal_product($ArrDetailProduct,'FINISH GOOD - TRANSIT');
	}

	function insert_jurnal_delivery_reject($ArrData, $kode_pro){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailProduct = [];
		$nomor = 0;

		$temp = [];
		$category = 'delivery reject';

		foreach ($ArrData as $key => $value) {$nomor++;
			$get_detProduksi	= $CI->db->select('id_produksi, kode_spk, print_merge_date AS data_uniq, id_milik, product_ke, product_code, no_spk, id_category AS product, amount, id')->get_where('production_detail',array('id'=>$value))->result();
			$data_uniq 			= $get_detProduksi[0]->data_uniq;
			$kode_spk 			= $get_detProduksi[0]->kode_spk;
			$kode_trans 		= $kode_spk.'/'.$data_uniq;
			$id_milik 			= $get_detProduksi[0]->id_milik;
			$product_ke 		= $get_detProduksi[0]->product_ke;
			$product_code 		= explode('.',$get_detProduksi[0]->product_code);
			$no_spk 			= $get_detProduksi[0]->no_spk;
			$product 			= $get_detProduksi[0]->product;
			$AMOUNT 			= $get_detProduksi[0]->amount;
			$id 				= $get_detProduksi[0]->id;
			
			$no_ipp 			= str_replace('PRO-','',$get_detProduksi[0]->id_produksi);
			$get_so 			= explode('-',$get_detProduksi[0]->product_code);

			$keterangan 		= $product.'/'.$id_milik.'/'.$product_code[0].'.'.$product_ke.'/'.$no_spk.'/'.$kode_pro;
			
			$ArrDetailProduct[$nomor]['kode_trans'] 	= $kode_trans;
			$ArrDetailProduct[$nomor]['category'] 		= $category;
			$ArrDetailProduct[$nomor]['gudang_dari'] 	= 20;
			$ArrDetailProduct[$nomor]['gudang_ke'] 		= 15;
			$ArrDetailProduct[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailProduct[$nomor]['no_ipp']			= $no_ipp;
			$ArrDetailProduct[$nomor]['no_so'] 			= $get_so[0];
			$ArrDetailProduct[$nomor]['product'] 		= $product;
			$ArrDetailProduct[$nomor]['id_detail'] 		= $value;
			$ArrDetailProduct[$nomor]['id_milik'] 		= $id_milik;
			$ArrDetailProduct[$nomor]['id_spk'] 		= $no_spk;
			$ArrDetailProduct[$nomor]['spec'] 			= spec_bq2($id_milik);
			$ArrDetailProduct[$nomor]['total_nilai'] 	= $AMOUNT;
			$ArrDetailProduct[$nomor]['created_by'] 	= $UserName;
			$ArrDetailProduct[$nomor]['created_date'] 	= $DateTime;
			$ArrDetailProduct[$nomor]['no_surat_jalan']	= $kode_pro;

			$ArrDetail[$nomor]['kode_trans'] = $keterangan;
			$ArrDetail[$nomor]['hub_product'] = $id_milik.'-'.$id;
			$ArrDetail[$nomor]['id_material'] = $id_milik;
			$ArrDetail[$nomor]['price_book'] = NULL;
			$ArrDetail[$nomor]['berat'] = NULL;
			$ArrDetail[$nomor]['amount'] = $AMOUNT;
			$ArrDetail[$nomor]['updated_by'] = $UserName;
			$ArrDetail[$nomor]['updated_date'] = $DateTime;

			$key_uniq = $id_milik;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $AMOUNT;
		}

		$kredit = 999;
		$category = 'delivery reject';
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 20;
			$ArrJurnal[$nomor]['keterangan'] = 'transit';
			$ArrJurnal[$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 15;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal_product',$ArrDetailProduct);
	}

    function insert_jurnal_delivery_confirm($ArrData, $kode_pro){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailProduct = [];
		$nomor = 0;

		$temp = [];
		$category = 'diterima customer';

		foreach ($ArrData as $key => $value) {$nomor++;
			$get_detProduksi	= $CI->db->select('id_produksi, kode_spk, print_merge_date AS data_uniq, id_milik, product_ke, product_code, no_spk, id_category AS product, amount, id')->get_where('production_detail',array('id'=>$value))->result();
			$data_uniq = (!empty($get_detProduksi[0]->data_uniq))?$get_detProduksi[0]->data_uniq:NULL;
			$kode_spk = (!empty($get_detProduksi[0]->kode_spk))?$get_detProduksi[0]->kode_spk:NULL;
			$id_milik = (!empty($get_detProduksi[0]->id_milik))?$get_detProduksi[0]->id_milik:NULL;
			$product_ke = (!empty($get_detProduksi[0]->product_ke))?$get_detProduksi[0]->product_ke:NULL;
			$product_codex = (!empty($get_detProduksi[0]->product_code))?$get_detProduksi[0]->product_code:'0.-0';
			$no_spk = (!empty($get_detProduksi[0]->no_spk))?$get_detProduksi[0]->no_spk:NULL;
			$product = (!empty($get_detProduksi[0]->product))?$get_detProduksi[0]->product:NULL;
			$amount = (!empty($get_detProduksi[0]->amount))?$get_detProduksi[0]->amount:NULL;
			$id = (!empty($get_detProduksi[0]->id))?$get_detProduksi[0]->id:NULL;

			$id_produksi = (!empty($get_detProduksi[0]->id_produksi))?$get_detProduksi[0]->id_produksi:NULL;
			
			$kode_trans 		= $kode_spk.'/'.$data_uniq;
			$product_code 		= explode('.',$product_codex);
			$AMOUNT 			= $amount;
			
			$no_ipp 			= str_replace('PRO-','',$id_produksi);
			$get_so 			= explode('-',$product_codex);

			$keterangan 		= $product.'/'.$id_milik.'/'.$product_code[0].'.'.$product_ke.'/'.$no_spk.'/'.$kode_pro;
			
			$ArrDetailProduct[$nomor]['kode_trans'] 	= $kode_trans;
			$ArrDetailProduct[$nomor]['category'] 		= $category;
			$ArrDetailProduct[$nomor]['gudang_dari'] 	= 20;
			$ArrDetailProduct[$nomor]['gudang_ke'] 		= 21;
			$ArrDetailProduct[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailProduct[$nomor]['no_ipp']			= $no_ipp;
			$ArrDetailProduct[$nomor]['no_so'] 			= $get_so[0];
			$ArrDetailProduct[$nomor]['product'] 		= $product;
			$ArrDetailProduct[$nomor]['id_detail'] 		= $value;
			$ArrDetailProduct[$nomor]['id_milik'] 		= $id_milik;
			$ArrDetailProduct[$nomor]['id_spk'] 		= $no_spk;
			$ArrDetailProduct[$nomor]['spec'] 			= spec_bq2($id_milik);
			$ArrDetailProduct[$nomor]['total_nilai'] 	= $AMOUNT;
			$ArrDetailProduct[$nomor]['created_by'] 	= $UserName;
			$ArrDetailProduct[$nomor]['created_date'] 	= $DateTime;
			$ArrDetailProduct[$nomor]['no_surat_jalan']	= $kode_pro;

			$ArrDetail[$nomor]['kode_trans'] = $keterangan;
			$ArrDetail[$nomor]['hub_product'] = $id_milik.'-'.$id;
			$ArrDetail[$nomor]['id_material'] = $id_milik;
			$ArrDetail[$nomor]['price_book'] = NULL;
			$ArrDetail[$nomor]['berat'] = NULL;
			$ArrDetail[$nomor]['amount'] = $AMOUNT;
			$ArrDetail[$nomor]['updated_by'] = $UserName;
			$ArrDetail[$nomor]['updated_date'] = $DateTime;

			$key_uniq = $id_milik;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $AMOUNT;
		}

		$kredit = 999;
		
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 21;
			$ArrJurnal[$nomor]['keterangan'] = 'customer';
			$ArrJurnal[$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 20;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'transit';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal_product',$ArrDetailProduct);
		auto_jurnal_product($ArrDetailProduct,'TRANSIT - CUSTOMER');
	}

    function insert_jurnal_qc_spool($ArrData, $kode_pro){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailProduct = [];
		$nomor = 0;

		$temp = [];
		$category = 'quality control spool';
		foreach ($ArrData as $key => $value) {$nomor++;
			$get_detProduksi	= $CI->db->select('id_produksi, kode_spk, print_merge_date AS data_uniq, id_milik, product_ke, product_code, no_spk, id_category AS product, amount, id')->get_where('production_detail',array('id'=>$value))->result();
			$data_uniq 			= $get_detProduksi[0]->data_uniq;
			$kode_spk 			= $get_detProduksi[0]->kode_spk;
			$kode_trans 		= $kode_spk.'/'.$data_uniq;
			$id_milik 			= $get_detProduksi[0]->id_milik;
			$product_ke 		= $get_detProduksi[0]->product_ke;
			$product_code 		= explode('.',$get_detProduksi[0]->product_code);
			$no_spk 			= $get_detProduksi[0]->no_spk;
			$product 			= $get_detProduksi[0]->product;
			$AMOUNT 			= $get_detProduksi[0]->amount;
			$id 				= $get_detProduksi[0]->id;
			
			$no_ipp 			= str_replace('PRO-','',$get_detProduksi[0]->id_produksi);
			$get_so 			= explode('-',$get_detProduksi[0]->product_code);

			$keterangan 		= $product.'/'.$id_milik.'/'.$product_code[0].'.'.$product_ke.'/'.$no_spk.'/'.$kode_pro;
			
			$ArrDetailProduct[$nomor]['kode_trans'] 	= $kode_trans;
			$ArrDetailProduct[$nomor]['category'] 		= $category;
			$ArrDetailProduct[$nomor]['gudang_dari'] 	= 14;
			$ArrDetailProduct[$nomor]['gudang_ke'] 		= 15;
			$ArrDetailProduct[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailProduct[$nomor]['no_ipp']			= $no_ipp;
			$ArrDetailProduct[$nomor]['no_so'] 			= $get_so[0];
			$ArrDetailProduct[$nomor]['product'] 		= $product;
			$ArrDetailProduct[$nomor]['id_detail'] 		= $value;
			$ArrDetailProduct[$nomor]['id_milik'] 		= $id_milik;
			$ArrDetailProduct[$nomor]['id_spk'] 		= $no_spk;
			$ArrDetailProduct[$nomor]['spec'] 			= spec_bq2($id_milik);
			$ArrDetailProduct[$nomor]['total_nilai'] 	= $AMOUNT;
			$ArrDetailProduct[$nomor]['created_by'] 	= $UserName;
			$ArrDetailProduct[$nomor]['created_date'] 	= $DateTime;

			$ArrDetail[$nomor]['kode_trans'] = $keterangan;
			$ArrDetail[$nomor]['hub_product'] = $id_milik.'-'.$id;
			$ArrDetail[$nomor]['id_material'] = $id_milik;
			$ArrDetail[$nomor]['price_book'] = NULL;
			$ArrDetail[$nomor]['berat'] = NULL;
			$ArrDetail[$nomor]['amount'] = $AMOUNT;
			$ArrDetail[$nomor]['updated_by'] = $UserName;
			$ArrDetail[$nomor]['updated_date'] = $DateTime;

			$key_uniq = $id_milik;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $AMOUNT;
		}

		$kredit = 999;
		$category = 'quality control spool';
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 15;
			$ArrJurnal[$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 14;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'wip';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key.'-'.$key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal_product',$ArrDetailProduct);
	}

	function update_price_book($ArrData,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_WEIGHT = 0;
		foreach ($ArrData as $key => $value) { 
			$SUM_WEIGHT += $value['qty_good'];
		}

		$ArrInsertPriceBook = [];
		$nomor = 0;
		$harga_jurnal=0;
		$DELIVERY = 1;
		foreach ($ArrData as $key => $value) { $nomor++;
			$KG_PUSAT 		= getWeightMaterialWarehouse($key,'pusat');
			$KG_SUBGUDANG 	= getWeightMaterialWarehouse($key,'subgudang');
			$KG_PRODUKSI 	= getWeightMaterialWarehouse($key,'produksi');
			$KG_ALL 		= getWeightMaterialWarehouse($key,'all');

			$PRICE_INCOMING = $value['kurs'] * $value['unit_price'];

			$harga_jurnal_akhir2 = $CI->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$key),1)->row();
			if(!empty($harga_jurnal_akhir2)) $harga_jurnal=$harga_jurnal_akhir2->harga;

			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$OLD_PRICE_BOOK = $KG_PUSAT * $PRICE;
			$NEW_PRICE_BOOK = $value['qty_good'] * $PRICE_INCOMING; //anggap dulu 1k

			$OLD_BERAT = $KG_PUSAT;
			$NEW_BERAT = $value['qty_good'];

			$LOGISTIC_PROPOSIONAL = $DELIVERY*$NEW_BERAT/$SUM_WEIGHT;

			$SUM_BERAT = $OLD_BERAT + $NEW_BERAT;
			$SUM_PRICE = $OLD_PRICE_BOOK + $NEW_PRICE_BOOK + $value['bm']; //+ $LOGISTIC_PROPOSIONAL; proporsional dihilangkan 20/juni/2024 hasil meeting bareng pak iman //nanti ditambah logistik namin proposional

			$FINAL_PRICE_BOOK = 0;
			if($SUM_PRICE > 0 AND $SUM_BERAT > 0){
			$FINAL_PRICE_BOOK = $SUM_PRICE / $SUM_BERAT;
			}

			$ArrInsertPriceBook[$key]['id_material'] = $key;
			$ArrInsertPriceBook[$key]['pusat'] = $KG_PUSAT + $NEW_BERAT;
			$ArrInsertPriceBook[$key]['subgudang'] = $KG_SUBGUDANG;
			$ArrInsertPriceBook[$key]['produksi'] = $KG_PRODUKSI;
			$ArrInsertPriceBook[$key]['price_book'] = $harga_jurnal;
			$ArrInsertPriceBook[$key]['delivery'] = $DELIVERY;
			$ArrInsertPriceBook[$key]['delivery_proposional'] = $value['bm'];
			$ArrInsertPriceBook[$key]['incoming'] = $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price'] = $PRICE_INCOMING;
			$ArrInsertPriceBook[$key]['updated_by'] = $UserName;
			$ArrInsertPriceBook[$key]['updated_date'] = $DateTime;
			$ArrInsertPriceBook[$key]['kode_trans'] = $kode_trans;
		}

		$CI->db->insert_batch('price_book',$ArrInsertPriceBook);
	}
	
	
	
	function update_price_book_subgudang($ArrData,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_WEIGHT = 0;
		foreach ($ArrData as $key => $value) { 
			$SUM_WEIGHT += $value['qty_good'];
		}

		$ArrInsertPriceBook = [];
		$nomor = 0;
		$harga_jurnal=0;
		$DELIVERY = 1;
		foreach ($ArrData as $key => $value) { $nomor++;
			$KG_PUSAT 		= getWeightMaterialWarehouse($key,'pusat');
			$KG_SUBGUDANG 	= getWeightMaterialWarehouse($key,'subgudang');
			$KG_PRODUKSI 	= getWeightMaterialWarehouse($key,'produksi');
			$KG_ALL 		= getWeightMaterialWarehouse($key,'all');
			
			$harga_jurnal_akhir2 = $CI->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$key),1)->row();
			if(!empty($harga_jurnal_akhir2)) $harga_jurnal=$harga_jurnal_akhir2->harga;
				
			$get_price_book_pusat = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key))->result();
			$PRICE_INCOMING = (!empty($get_price_book_pusat[0]->price_book))?$get_price_book_pusat[0]->price_book:0;
			
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$OLD_PRICE_BOOK = $KG_SUBGUDANG * $PRICE;
			$NEW_PRICE_BOOK = $value['qty_good'] * $PRICE_INCOMING; //anggap dulu 1k

			$OLD_BERAT = $KG_SUBGUDANG;
			$NEW_BERAT = $value['qty_good'];

			$LOGISTIC_PROPOSIONAL = $DELIVERY*$NEW_BERAT/$SUM_WEIGHT;

			$SUM_BERAT = $OLD_BERAT + $NEW_BERAT;
			$SUM_PRICE = $OLD_PRICE_BOOK + $NEW_PRICE_BOOK; //+ $LOGISTIC_PROPOSIONAL; proporsional dihilangkan 20/juni/2024 hasil meeting bareng pak iman //nanti ditambah logistik namin proposional

			$FINAL_PRICE_BOOK = 0;
			if($SUM_PRICE > 0 AND $SUM_BERAT > 0){
			$FINAL_PRICE_BOOK = $SUM_PRICE / $SUM_BERAT;
			}

			$ArrInsertPriceBook[$key]['id_material'] = $key;
			$ArrInsertPriceBook[$key]['pusat'] = $KG_PUSAT;
			$ArrInsertPriceBook[$key]['subgudang'] = $KG_SUBGUDANG + $NEW_BERAT;
			$ArrInsertPriceBook[$key]['produksi'] = $KG_PRODUKSI;
			$ArrInsertPriceBook[$key]['price_book'] = $harga_jurnal;
			$ArrInsertPriceBook[$key]['delivery'] = $DELIVERY;
			$ArrInsertPriceBook[$key]['delivery_proposional'] = $LOGISTIC_PROPOSIONAL;
			$ArrInsertPriceBook[$key]['incoming'] = $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price'] = $PRICE_INCOMING;
			$ArrInsertPriceBook[$key]['updated_by'] = $UserName;
			$ArrInsertPriceBook[$key]['updated_date'] = $DateTime;
			$ArrInsertPriceBook[$key]['kode_trans'] = $kode_trans;
		}

		$CI->db->insert_batch('price_book_subgudang',$ArrInsertPriceBook);
	}
	
	
	function update_price_book_produksi($ArrData,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_WEIGHT = 0;
		foreach ($ArrData as $key => $value) { 
			$SUM_WEIGHT += $value['qty_good'];
		}

		$ArrInsertPriceBook = [];
		$nomor = 0;
		$harga_jurnal=0;
		$DELIVERY = 1;
		foreach ($ArrData as $key => $value) { $nomor++;
			$KG_PUSAT 		= getWeightMaterialWarehouse($key,'pusat');
			$KG_SUBGUDANG 	= getWeightMaterialWarehouse($key,'subgudang');
			$KG_PRODUKSI 	= getWeightMaterialWarehouse($key,'produksi');
			$KG_ALL 		= getWeightMaterialWarehouse($key,'all');
            
			$harga_jurnal_akhir2 = $CI->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$key),1)->row();
			if(!empty($harga_jurnal_akhir2)) $harga_jurnal=$harga_jurnal_akhir2->harga;
			
			$get_price_book_subgudang = $CI->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
			$PRICE_INCOMING = (!empty($get_price_book_subgudang[0]->price_book))?$get_price_book_subgudang[0]->price_book:0;
			
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_produksi',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$OLD_PRICE_BOOK = $KG_PRODUKSI * $PRICE;
			$NEW_PRICE_BOOK = $value['qty_good'] * $PRICE_INCOMING; //anggap dulu 1k

			$OLD_BERAT = $KG_PRODUKSI;
			$NEW_BERAT = $value['qty_good'];

			$LOGISTIC_PROPOSIONAL = 0;
			if($NEW_BERAT > 0 AND $SUM_WEIGHT > 0){
			$LOGISTIC_PROPOSIONAL = $DELIVERY*$NEW_BERAT/$SUM_WEIGHT;
			}

			$SUM_BERAT = $OLD_BERAT + $NEW_BERAT;
			$SUM_PRICE = $OLD_PRICE_BOOK + $NEW_PRICE_BOOK; //+ $LOGISTIC_PROPOSIONAL; proporsional dihilangkan 20/juni/2024 hasil meeting bareng pak iman //nanti ditambah logistik namin proposional

			$FINAL_PRICE_BOOK = 0;
			if($SUM_PRICE > 0 AND $SUM_BERAT > 0){
			$FINAL_PRICE_BOOK = $SUM_PRICE / $SUM_BERAT;
			}

			$ArrInsertPriceBook[$key]['id_material'] = $key;
			$ArrInsertPriceBook[$key]['pusat'] = $KG_PUSAT;
			$ArrInsertPriceBook[$key]['subgudang'] = $KG_SUBGUDANG;
			$ArrInsertPriceBook[$key]['produksi'] = $KG_PRODUKSI+ $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price_book'] = $harga_jurnal;
			$ArrInsertPriceBook[$key]['delivery'] = $DELIVERY;
			$ArrInsertPriceBook[$key]['delivery_proposional'] = $LOGISTIC_PROPOSIONAL;
			$ArrInsertPriceBook[$key]['incoming'] = $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price'] = $PRICE_INCOMING;
			$ArrInsertPriceBook[$key]['updated_by'] = $UserName;
			$ArrInsertPriceBook[$key]['updated_date'] = $DateTime;
			$ArrInsertPriceBook[$key]['kode_trans'] = $kode_trans;
		}

		$CI->db->insert_batch('price_book_produksi',$ArrInsertPriceBook);
	}
	
	
	function update_price_book_subgudang_retur($ArrData,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_WEIGHT = 0;
		foreach ($ArrData as $key => $value) { 
			$SUM_WEIGHT += $value['qty_good'];
		}
				
		// print_r($kode_trans);
		// exit;

		$ArrInsertPriceBook = [];
		$nomor = 0;
		$harga_jurnal=0;
		$DELIVERY = 1;
		foreach ($ArrData as $key => $value) { $nomor++;
			$KG_PUSAT 		= getWeightMaterialWarehouse($key,'pusat');
			$KG_SUBGUDANG 	= getWeightMaterialWarehouse($key,'subgudang');
			$KG_PRODUKSI 	= getWeightMaterialWarehouse($key,'produksi');
			$KG_ALL 		= getWeightMaterialWarehouse($key,'all');
			
			$harga_jurnal_akhir2 = $CI->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3,'id_material'=>$key),1)->row();
			if(!empty($harga_jurnal_akhir2)) $harga_jurnal=$harga_jurnal_akhir2->harga;
			$PRICE_INCOMING = $value['price'];
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$OLD_PRICE_BOOK = $KG_SUBGUDANG * $PRICE;
			$NEW_PRICE_BOOK = $value['qty_good'] * $PRICE_INCOMING; //anggap dulu 1k

			$OLD_BERAT = $KG_SUBGUDANG;
			$NEW_BERAT = $value['qty_good'];
			

			$LOGISTIC_PROPOSIONAL = $DELIVERY*$NEW_BERAT/$SUM_WEIGHT;

			$SUM_BERAT = $OLD_BERAT + $NEW_BERAT;
			$SUM_PRICE = $OLD_PRICE_BOOK + $NEW_PRICE_BOOK; //+ $LOGISTIC_PROPOSIONAL; proporsional dihilangkan 20/juni/2024 hasil meeting bareng pak iman //nanti ditambah logistik namin proposional

			$FINAL_PRICE_BOOK = 0;
			if($SUM_PRICE > 0 AND $SUM_BERAT > 0){
			$FINAL_PRICE_BOOK = $SUM_PRICE / $SUM_BERAT;
			}

			$ArrInsertPriceBook[$key]['id_material'] = $key;
			$ArrInsertPriceBook[$key]['pusat'] = $KG_PUSAT;
			$ArrInsertPriceBook[$key]['subgudang'] = $KG_SUBGUDANG + $NEW_BERAT;
			$ArrInsertPriceBook[$key]['produksi'] = $KG_PRODUKSI;
			$ArrInsertPriceBook[$key]['price_book'] = $harga_jurnal;
			$ArrInsertPriceBook[$key]['delivery'] = $DELIVERY;
			$ArrInsertPriceBook[$key]['delivery_proposional'] = $LOGISTIC_PROPOSIONAL;
			$ArrInsertPriceBook[$key]['incoming'] = $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price'] = $PRICE_INCOMING;
			$ArrInsertPriceBook[$key]['updated_by'] = $UserName;
			$ArrInsertPriceBook[$key]['updated_date'] = $DateTime;
			$ArrInsertPriceBook[$key]['kode_trans'] = $kode_trans;
		}
		
		

		$CI->db->insert_batch('price_book_subgudang',$ArrInsertPriceBook);
	}
	
	function update_price_book_pusat_retur($ArrData,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_WEIGHT = 0;
		foreach ($ArrData as $key => $value) { 
			$SUM_WEIGHT += $value['qty_good'];
		}
				
		// print_r($kode_trans);
		// exit;

		$ArrInsertPriceBook = [];
		$nomor = 0;
		$harga_jurnal=0;
		$DELIVERY = 1;
		foreach ($ArrData as $key => $value) { $nomor++;
			$KG_PUSAT 		= getWeightMaterialWarehouse($key,'pusat');
			$KG_SUBGUDANG 	= getWeightMaterialWarehouse($key,'subgudang');
			$KG_PRODUKSI 	= getWeightMaterialWarehouse($key,'produksi');
			$KG_ALL 		= getWeightMaterialWarehouse($key,'all');
			
			$harga_jurnal_akhir2 = $CI->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$key),1)->row();
			if(!empty($harga_jurnal_akhir2)) $harga_jurnal=$harga_jurnal_akhir2->harga;
			
			$PRICE_INCOMING = $value['price'];
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$OLD_PRICE_BOOK = $KG_SUBGUDANG * $PRICE;
			$NEW_PRICE_BOOK = $value['qty_good'] * $PRICE_INCOMING; //anggap dulu 1k

			$OLD_BERAT = $KG_SUBGUDANG;
			$NEW_BERAT = $value['qty_good'];
			

			$LOGISTIC_PROPOSIONAL = $DELIVERY*$NEW_BERAT/$SUM_WEIGHT;

			$SUM_BERAT = $OLD_BERAT + $NEW_BERAT;
			$SUM_PRICE = $OLD_PRICE_BOOK + $NEW_PRICE_BOOK; //+ $LOGISTIC_PROPOSIONAL; proporsional dihilangkan 20/juni/2024 hasil meeting bareng pak iman //nanti ditambah logistik namin proposional

			$FINAL_PRICE_BOOK = 0;
			if($SUM_PRICE > 0 AND $SUM_BERAT > 0){
			$FINAL_PRICE_BOOK = $SUM_PRICE / $SUM_BERAT;
			}

			$ArrInsertPriceBook[$key]['id_material'] = $key;
			$ArrInsertPriceBook[$key]['pusat'] = $KG_PUSAT;
			$ArrInsertPriceBook[$key]['subgudang'] = $KG_SUBGUDANG + $NEW_BERAT;
			$ArrInsertPriceBook[$key]['produksi'] = $KG_PRODUKSI;
			$ArrInsertPriceBook[$key]['price_book'] = $harga_jurnal;
			$ArrInsertPriceBook[$key]['delivery'] = $DELIVERY;
			$ArrInsertPriceBook[$key]['delivery_proposional'] = $LOGISTIC_PROPOSIONAL;
			$ArrInsertPriceBook[$key]['incoming'] = $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price'] = $PRICE_INCOMING;
			$ArrInsertPriceBook[$key]['updated_by'] = $UserName;
			$ArrInsertPriceBook[$key]['updated_date'] = $DateTime;
			$ArrInsertPriceBook[$key]['kode_trans'] = $kode_trans;
		}
		
		

		$CI->db->insert_batch('price_book',$ArrInsertPriceBook);
	}
	


	function getWeightMaterialWarehouse($id_material,$gudang){
		$CI 	=& get_instance();

		if($gudang != 'all'){
			$get_wherehouse = $CI->db->select('id')->from('warehouse')->where('category',$gudang)->get()->result_array();
		}
		else{
			$get_wherehouse = $CI->db->select('id')->from('warehouse')->or_where('category','pusat')->or_where('category','subgudang')->or_where('category','produksi')->get()->result_array();
		}
		$WHERE_IN = [];
		foreach ($get_wherehouse as $key => $value) {
			$WHERE_IN[] = $value['id'];
		}

		$get_gudang = $CI->db->select('SUM(qty_stock) AS stock')->from('warehouse_stock')->where('id_material',$id_material)->where_in('id_gudang',$WHERE_IN)->get()->result();
		$STOCK 		= (!empty($get_gudang[0]->stock) AND $get_gudang[0]->stock > 0)?$get_gudang[0]->stock:0;

		return $STOCK;
	}

	function insert_jurnal_stock($ArrData,$GudangFrom,$GudangTo,$kode_trans,$category,$ket_min,$ket_plus){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$getHeaderAdjust= $CI->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();
		$DATE_JURNAL 	= (!empty($getHeaderAdjust[0]->tanggal))?$getHeaderAdjust[0]->tanggal:$getHeaderAdjust[0]->created_date;

		$SUM_PRICE 		= 0;
		$ArrDetail 		= [];
		$ArrDetailNew	= [];
		$ArrDeferred	= [];		
		foreach ($ArrData as $key => $value) {
			if($category == 'outgoing stok'){
				$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key))->result();
				$PRICE 			= (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
// cek deferred gudang project
			  if($GudangTo=='17'){
				$ArrDeferred[$key]['kode_trans']	= $kode_trans;
				$ArrDeferred[$key]['no_so']			= $getHeaderAdjust[0]->no_so;
				$ArrDeferred[$key]['tanggal']		= date('Y-m-d',strtotime($DATE_JURNAL));
				$ArrDeferred[$key]['tipe']			= 'material_indirect';
				$ArrDeferred[$key]['qty']			= $value['qty_good'];
				$ArrDeferred[$key]['amount']		= $PRICE * $value['qty_good'];
				$ArrDeferred[$key]['id_material']	= $key;
				$ArrDeferred[$key]['nm_material']	= get_name('con_nonmat_new','material_name','code_group',$key);
			  }
			}else{
				$PRICE 			= (!empty($value['unit_price']))?$value['unit_price']:0;
			}
			$SUM_PRICE 		+= $PRICE * $value['qty_good'];

			$ArrDetail[$key]['kode_trans'] 		= $kode_trans;
			$ArrDetail[$key]['id_material'] 	= $key;
			$ArrDetail[$key]['price_book'] 		= $PRICE;
			$ArrDetail[$key]['berat'] 			= $value['qty_good'];
			$ArrDetail[$key]['amount'] 			= $PRICE * $value['qty_good'];
			$ArrDetail[$key]['updated_by'] 		= $UserName;
			$ArrDetail[$key]['updated_date']	= $DateTime;

			$ArrDetailNew[$key]['kode_trans'] 	= $kode_trans;
			$ArrDetailNew[$key]['category'] 	= $category;
			$ArrDetailNew[$key]['gudang_dari'] 	= $GudangFrom;
			$ArrDetailNew[$key]['gudang_ke'] 	= $GudangTo;
			$ArrDetailNew[$key]['tanggal'] 		= date('Y-m-d',strtotime($DATE_JURNAL));
			$ArrDetailNew[$key]['id_material'] 	= $key;
			$ArrDetailNew[$key]['nm_material'] 	= get_name('con_nonmat_new','material_name','code_group',$key);
			$ArrDetailNew[$key]['cost_book'] 	= $PRICE;
			$ArrDetailNew[$key]['qty'] 			= $value['qty_good'];
			$ArrDetailNew[$key]['total_nilai'] 	= $PRICE * $value['qty_good'];
			$ArrDetailNew[$key]['created_by'] 	= $UserName;
			$ArrDetailNew[$key]['created_date'] = $DateTime;
		}

		//DEBET
		$ArrJurnal[0]['category'] 	= $category;
		$ArrJurnal[0]['posisi'] 	= 'DEBIT';
		$ArrJurnal[0]['amount'] 	= $SUM_PRICE;
		$ArrJurnal[0]['gudang'] 	= $GudangTo;
		$ArrJurnal[0]['keterangan'] = $ket_plus;
		$ArrJurnal[0]['kode_trans'] = $kode_trans;
		$ArrJurnal[0]['updated_by'] = $UserName;
		$ArrJurnal[0]['updated_date'] = $DateTime;

		//KREDIT
		$ArrJurnal[1]['category'] 	= $category;
		$ArrJurnal[1]['posisi'] 	= 'KREDIT';
		$ArrJurnal[1]['amount'] 	= $SUM_PRICE;
		$ArrJurnal[1]['gudang'] 	= $GudangFrom;
		$ArrJurnal[1]['keterangan'] = $ket_min;
		$ArrJurnal[1]['kode_trans'] = $kode_trans;
		$ArrJurnal[1]['updated_by'] = $UserName;
		$ArrJurnal[1]['updated_date'] = $DateTime;
		
		
		// print_r($category);
		// exit;
		

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal',$ArrDetailNew);

		if($category == 'incoming stok'){
			auto_jurnal_product($kode_trans,$category);
			update_price_book_stock($ArrDetail,$kode_trans);
		}
		if($category == 'outgoing stok'){
			if(!empty($ArrDeferred)) {
				$CI->db->insert_batch('tr_deferred',$ArrDeferred);
			}
			auto_jurnal_product($kode_trans,$category);
		}
		if($category == 'incoming project'){
			auto_jurnal_product($kode_trans,$category);
			update_price_book_project($ArrDetail,$kode_trans);
		}

	}

	function insert_jurnal_department($ArrData,$GudangFrom,$GudangTo,$kode_trans,$category,$ket_min,$ket_plus){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$getHeaderAdjust = $CI->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();
		$DATE_JURNAL = (!empty($getHeaderAdjust[0]->tanggal))?$getHeaderAdjust[0]->tanggal:$getHeaderAdjust[0]->created_date;

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrDetailNew = [];
		foreach ($ArrData as $key => $value) {
			$PRICE 	= $value['unit_price'];
			$QTY 	= $value['qty'];
			$TOTAL 	= $PRICE * $QTY;
			$SUM_PRICE += $TOTAL;

			$ArrDetail[$key]['kode_trans'] 		= $kode_trans;
			$ArrDetail[$key]['id_material'] 	= $value['id_barang'];
			$ArrDetail[$key]['price_book'] 		= $PRICE;
			$ArrDetail[$key]['berat'] 			= $QTY;
			$ArrDetail[$key]['amount'] 			= $TOTAL;
			$ArrDetail[$key]['updated_by'] 		= $UserName;
			$ArrDetail[$key]['updated_date'] 	= $DateTime;

			$ArrDetailNew[$key]['kode_trans'] 	= $kode_trans;
			$ArrDetailNew[$key]['no_ipp'] 		= $value['no_po'];
			$ArrDetailNew[$key]['category'] 	= $category;
			$ArrDetailNew[$key]['gudang_dari'] 	= $GudangFrom;
			$ArrDetailNew[$key]['gudang_ke'] 	= $GudangTo;
			$ArrDetailNew[$key]['tanggal'] 		= date('Y-m-d',strtotime($DATE_JURNAL));
			$ArrDetailNew[$key]['id_material'] 	= $value['id_barang'];
			$ArrDetailNew[$key]['nm_material'] 	= $value['nm_barang'];
			$ArrDetailNew[$key]['cost_book'] 	= $PRICE;
			$ArrDetailNew[$key]['qty'] 			= $QTY;
			$ArrDetailNew[$key]['total_nilai'] 	= $TOTAL;
			$ArrDetailNew[$key]['created_by'] 	= $UserName;
			$ArrDetailNew[$key]['created_date'] = $DateTime;
		}

		//DEBET
		$ArrJurnal[0]['category'] = $category;
		$ArrJurnal[0]['posisi'] = 'DEBIT';
		$ArrJurnal[0]['amount'] = $SUM_PRICE;
		$ArrJurnal[0]['gudang'] = $GudangTo;
		$ArrJurnal[0]['keterangan'] = $ket_plus;
		$ArrJurnal[0]['kode_trans'] = $kode_trans;
		$ArrJurnal[0]['updated_by'] = $UserName;
		$ArrJurnal[0]['updated_date'] = $DateTime;

		//KREDIT
		$ArrJurnal[1]['category'] = $category;
		$ArrJurnal[1]['posisi'] = 'KREDIT';
		$ArrJurnal[1]['amount'] = $SUM_PRICE;
		$ArrJurnal[1]['gudang'] = $GudangFrom;
		$ArrJurnal[1]['keterangan'] = $ket_min;
		$ArrJurnal[1]['kode_trans'] = $kode_trans;
		$ArrJurnal[1]['updated_by'] = $UserName;
		$ArrJurnal[1]['updated_date'] = $DateTime;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal',$ArrDetailNew);
		if($category == 'incoming department'){
			auto_jurnal_product($kode_trans,$category);
		}
		if($category == 'incoming asset'){
			auto_jurnal_product($kode_trans,$category);
		}
	}

	function getWeightStockWarehouse($id_material,$gudang){
		$CI 	=& get_instance();

		$get_gudang = $CI->db->select('SUM(stock) AS stock')->from('warehouse_rutin_stock')->where('code_group',$id_material)->where('gudang',$gudang)->get()->result();
		$STOCK 		= (!empty($get_gudang[0]->stock) AND $get_gudang[0]->stock > 0)?$get_gudang[0]->stock:0;

		return $STOCK;
	}

	function update_price_book_stock($ArrData,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_WEIGHT = 0;
		foreach ($ArrData as $key => $value) { 
			$SUM_WEIGHT += $value['berat'];
		}

		$ArrInsertPriceBook = [];
		$nomor = 0;
		$DELIVERY = 1;
		foreach ($ArrData as $key => $value) { $nomor++;
			$KG_ALL 		= getWeightStockWarehouse($value['id_material'],10);

			$PRICE_INCOMING = $value['price_book'];

			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$value['id_material']))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$OLD_PRICE_BOOK = $KG_ALL * $PRICE;
			$NEW_PRICE_BOOK = $value['berat'] * $PRICE_INCOMING;

			$OLD_BERAT = $KG_ALL;
			$NEW_BERAT = $value['berat'];

			$LOGISTIC_PROPOSIONAL = $DELIVERY*$NEW_BERAT/$SUM_WEIGHT;

			$SUM_BERAT = $OLD_BERAT + $NEW_BERAT;
			$SUM_PRICE = $OLD_PRICE_BOOK + $NEW_PRICE_BOOK + $LOGISTIC_PROPOSIONAL; //nanti ditambah logistik namin proposional

			$FINAL_PRICE_BOOK = 0;
			if($SUM_PRICE > 0 AND $SUM_BERAT > 0){
			$FINAL_PRICE_BOOK = $SUM_PRICE / $SUM_BERAT;
			}

			$ArrInsertPriceBook[$key]['id_material'] = $value['id_material'];
			$ArrInsertPriceBook[$key]['pusat'] = $KG_ALL + $NEW_BERAT;
			$ArrInsertPriceBook[$key]['subgudang'] = 0;
			$ArrInsertPriceBook[$key]['produksi'] = 0;
			$ArrInsertPriceBook[$key]['price_book'] = $FINAL_PRICE_BOOK;
			$ArrInsertPriceBook[$key]['delivery'] = $DELIVERY;
			$ArrInsertPriceBook[$key]['delivery_proposional'] = $LOGISTIC_PROPOSIONAL;
			$ArrInsertPriceBook[$key]['incoming'] = $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price'] = $PRICE_INCOMING;
			$ArrInsertPriceBook[$key]['updated_by'] = $UserName;
			$ArrInsertPriceBook[$key]['updated_date'] = $DateTime;
			$ArrInsertPriceBook[$key]['kode_trans'] = $kode_trans;
		}

		$CI->db->insert_batch('price_book',$ArrInsertPriceBook);
	}
	
	function update_price_book_project($ArrData,$kode_trans){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_WEIGHT = 0;
		foreach ($ArrData as $key => $value) { 
			$SUM_WEIGHT += $value['berat'];
		}

		$ArrInsertPriceBook = [];
		$nomor = 0;
		$DELIVERY = 1;
		foreach ($ArrData as $key => $value) { $nomor++;
			$KG_ALL 		= getWeightStockWarehouse($value['id_material'],10);

			$PRICE_INCOMING = $value['price_book'];

			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$value['id_material']))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$OLD_PRICE_BOOK = $KG_ALL * $PRICE;
			$NEW_PRICE_BOOK = $value['berat'] * $PRICE_INCOMING;

			$OLD_BERAT = $KG_ALL;
			$NEW_BERAT = $value['berat'];

			$LOGISTIC_PROPOSIONAL = $DELIVERY*$NEW_BERAT/$SUM_WEIGHT;

			$SUM_BERAT = $OLD_BERAT + $NEW_BERAT;
			$SUM_PRICE = $OLD_PRICE_BOOK + $NEW_PRICE_BOOK + $LOGISTIC_PROPOSIONAL; //nanti ditambah logistik namin proposional

			$FINAL_PRICE_BOOK = 0;
			if($SUM_PRICE > 0 AND $SUM_BERAT > 0){
			$FINAL_PRICE_BOOK = $SUM_PRICE / $SUM_BERAT;
			}

			$ArrInsertPriceBook[$key]['id_material'] = $value['id_material'];
			$ArrInsertPriceBook[$key]['pusat'] = $KG_ALL + $NEW_BERAT;
			$ArrInsertPriceBook[$key]['subgudang'] = 0;
			$ArrInsertPriceBook[$key]['produksi'] = 0;
			$ArrInsertPriceBook[$key]['price_book'] = $FINAL_PRICE_BOOK;
			$ArrInsertPriceBook[$key]['delivery'] = $DELIVERY;
			$ArrInsertPriceBook[$key]['delivery_proposional'] = $LOGISTIC_PROPOSIONAL;
			$ArrInsertPriceBook[$key]['incoming'] = $NEW_BERAT;
			$ArrInsertPriceBook[$key]['price'] = $PRICE_INCOMING;
			$ArrInsertPriceBook[$key]['updated_by'] = $UserName;
			$ArrInsertPriceBook[$key]['updated_date'] = $DateTime;
			$ArrInsertPriceBook[$key]['kode_trans'] = $kode_trans;
		}

		$CI->db->insert_batch('price_book_project',$ArrInsertPriceBook);
	}
	
	function auto_jurnal_product($ArrDetailProduct,$ket){
		$CI 	=& get_instance();
		$CI->load->model('Jurnal_model');
		$CI->load->model('Acc_model');
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		
		
		if($ket=='WIP - FINISH GOOD'){
		  $kodejurnal='JV005';
		  foreach($ArrDetailProduct as $keys => $values) {
			$id=$values['id_detail'];
			$datajurnal = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.category='quality control' and a.status_jurnal='0' and a.id_detail ='$id' limit 1" )->row();
			$id=$datajurnal->id;
			$tgl_voucher = $datajurnal->tanggal;
			$no_request = $id;

			//$datasodetailheader = $CI->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
			$datasodetailheader = $CI->db->query("SELECT * FROM laporan_per_hari_action WHERE id_milik ='".$datajurnal->id_milik."' limit 1" )->row();
			
			// print_r($datajurnal->id_milik);
			// exit;
			
			
			$kurs=1;
			$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
			$dtkurs	= $CI->db->query($sqlkurs)->row();
			if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
			$data_pro_det = $CI->db->query("SELECT * FROM production_detail WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" )->row();
			$dataprodet="";
			if(!empty($data_pro_det)){
				if($data_pro_det->finish_good > 0){
					$dataprodet=$data_pro_det->id;
					$wip_material=$data_pro_det->wip_material;
					$pe_direct_labour=$data_pro_det->wip_dl;
					$foh=$data_pro_det->wip_foh;
					$pe_indirect_labour=$data_pro_det->wip_il;
					$pe_consumable=$data_pro_det->wip_consumable;
					$finish_good=$data_pro_det->finish_good;
				}
			}
			if($dataprodet==""){
				$wip_material=$datajurnal->total_nilai;
				$pe_direct_labour=(($datasodetailheader->direct_labour*$datasodetailheader->man_hours)*$kurs);
				$pe_indirect_labour=(($datasodetailheader->indirect_labour*$datasodetailheader->man_hours)*$kurs);
				$foh=(($datasodetailheader->machine + $datasodetailheader->mould_mandrill + $datasodetailheader->foh_depresiasi + $datasodetailheader->biaya_rutin_bulanan + $datasodetailheader->foh_consumable)*$kurs);
				$pe_consumable=($datasodetailheader->consumable*$kurs);
				$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);

				$CI->db->query("update production_detail set wip_kurs='".$kurs."', wip_material='".$wip_material."' , wip_dl='".$pe_direct_labour."' , wip_foh='".$foh."', wip_il='".$pe_indirect_labour."', wip_consumable='".$pe_consumable."', finish_good='".$finish_good."' WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );
			}
			$masterjurnal	= $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$totaldebit=0; $totalkredit=0; $coa_cogm=''; $no_spk=$datajurnal->id_spk;
			$det_Jurnaltes = [];
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='11'){
					$coa_cogm=$nokir;
				}
				$totaldebit+=$debit;$totalkredit+=$kredit;
			}
			$Keterangan_INV=($ket).' ('.$datajurnal->no_so.' - '.$datajurnal->product.' - '.$no_spk.')';
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
				'no_request'    => $no_request,
				'stspos'		=>1
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
				'no_request'    => $no_request,
				'stspos'		=>1
			 );
			$CI->db->query("delete from jurnaltras WHERE jenis_jurnal='wip finishgood' and no_reff ='$id'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalkredit, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.'-'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$CI->db->query("UPDATE jurnal_product SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE id ='$id'");
			unset($det_Jurnaltes);unset($datadetail);
		  }
		}
		/*if($ket=='FINISH GOOD - TRANSIT'){
		  $kodejurnal='JV006';
		  foreach($ArrDetailProduct as $keys => $values) {
			$id=$values['id_detail'];		
			$datajurnal = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.category='delivery' and a.status_jurnal='0' and a.id_detail ='$id' limit 1" )->row();
			$id=$datajurnal->id;
			$tgl_voucher = $datajurnal->tanggal;
			$no_request = $id;

			$dataproductiondetail=$CI->db->query("select * from production_detail where id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1")->row();
			if($dataproductiondetail->finish_good==0){
				//$datasodetailheader = $CI->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
				$datasodetailheader = $CI->db->query("SELECT * FROM laporan_per_hari_action WHERE id_milik ='".$datajurnal->id_milik."' limit 1" )->row();
			
				
				$kurs=1;
				$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
				$dtkurs	= $CI->db->query($sqlkurs)->row();
				if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
				$wip_material=$datajurnal->total_nilai;
				$pe_direct_labour=(($datasodetailheader->direct_labour*$datasodetailheader->man_hours)*$kurs);
				$pe_indirect_labour=(($datasodetailheader->indirect_labour*$datasodetailheader->man_hours)*$kurs);
				$foh=(($datasodetailheader->machine + $datasodetailheader->mould_mandrill + $datasodetailheader->foh_depresiasi + $datasodetailheader->biaya_rutin_bulanan + $datasodetailheader->foh_consumable)*$kurs);
				$pe_consumable=($datasodetailheader->consumable*$kurs);
				$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);

				$CI->db->query("update production_detail set wip_kurs='".$kurs."', wip_material='".$wip_material."' , wip_dl='".$pe_direct_labour."' , wip_foh='".$foh."', wip_il='".$pe_indirect_labour."', wip_consumable='".$pe_consumable."', finish_good='".$finish_good."' WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );
			
				$totalall=$finish_good;
			}else{
				$totalall=$dataproductiondetail->finish_good;
			}
			$no_spk=$datajurnal->id_spk;
			$Keterangan_INV=($ket).' ('.$datajurnal->no_so.' - '.$datajurnal->product.' - '.$no_spk.' - '.$datajurnal->no_surat_jalan.')';
			$datajurnal  	 = $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$det_Jurnaltes = [];
			foreach($datajurnal AS $record){
				$tabel  = $record->menu;
				$posisi = $record->posisi;
				$field  = $record->field;
				$nokir  = $record->no_perkiraan;

				$totalall2 = (!empty($totalall))?$totalall:0;
				$param  = 'id';
				if ($posisi=='D'){
					$value_param  = $id;
					$val = $CI->Acc_model->GetData($tabel,$field,$param,$value_param);
					$nilaibayar = $val[0]->$field;
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $Keterangan_INV,
					  'no_reff'       => $no_request,
					  'debet'         => $totalall2,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'finish good intransit',
					  'no_request'    => $no_request,
					  'stspos'		=>1
					 );
				} elseif ($posisi=='K'){
					$coa = 	$CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.id ='$id'")->result();
					$nokir=$coa[0]->coa_fg;
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $Keterangan_INV,
					  'no_reff'       => $no_request,
					  'debet'         => 0,
					  'kredit'        => $totalall2,
					  'jenis_jurnal'  => 'finish good intransit',
					  'no_request'    => $no_request,
					  'stspos'		=>1
					 );
				}
			}
			$CI->db->query("delete from jurnaltras WHERE jenis_jurnal='finish good intransit' and no_reff ='$id'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall2, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.'-'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$CI->db->query("UPDATE jurnal_product SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE id ='$id'");
			unset($det_Jurnaltes);unset($datadetail);
		  }
		}*/
		
		if($ket=='FINISH GOOD - TRANSIT'){
		  $kodejurnal='JV006';
		  foreach($ArrDetailProduct as $keys => $values) {
			$id=$values['id_detail'];		
			
			$datajurnal = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.category='delivery' and a.status_jurnal='0' and a.id_detail ='$id' limit 1" )->row();
			if(!empty($datajurnal)){
            $datajurnal = $datajurnal;
			}else {
			$datajurnal = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join view_ms_category_part b on a.product=b.id WHERE a.category='delivery' and a.status_jurnal='0' and a.id_detail ='$id' limit 1" )->row();
			}	
			
			$id=$datajurnal->id;
			$tgl_voucher = $datajurnal->tanggal;
			$no_request = $id;

			$dataproductiondetail=$CI->db->query("select * from production_detail where id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1")->row();
			if($dataproductiondetail->finish_good==0){
				//$datasodetailheader = $CI->db->query("SELECT * FROM so_detail_header WHERE id ='".$datajurnal->id_milik."' limit 1" )->row();
				$datasodetailheader = $CI->db->query("SELECT * FROM laporan_per_hari WHERE id_milik ='".$datajurnal->id_milik."' limit 1" )->row();
				
				$direct_labour 		= (!empty($datasodetailheader->direct_labour))?$datasodetailheader->direct_labour:0;
				$indirect_labour 	= (!empty($datasodetailheader->indirect_labour))?$datasodetailheader->indirect_labour:0;
				$machine 			= (!empty($datasodetailheader->machine))?$datasodetailheader->machine:0;
				$mould_mandrill 	= (!empty($datasodetailheader->mould_mandrill))?$datasodetailheader->mould_mandrill:0;
				$foh_depresiasi 	= (!empty($datasodetailheader->foh_depresiasi))?$datasodetailheader->foh_depresiasi:0;
				$biaya_rutin_bulanan = (!empty($datasodetailheader->biaya_rutin_bulanan))?$datasodetailheader->biaya_rutin_bulanan:0;
				$foh_consumable 	= (!empty($datasodetailheader->foh_consumable))?$datasodetailheader->foh_consumable:0;
				$consumable 		= (!empty($datasodetailheader->consumable))?$datasodetailheader->consumable:0;
				$man_hours 			= (!empty($datasodetailheader->man_hours))?$datasodetailheader->man_hours:0;
				
				$kurs=1;
				$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
				$dtkurs	= $CI->db->query($sqlkurs)->row();
				if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
				$wip_material=$datajurnal->total_nilai;
				$pe_direct_labour=(($direct_labour*$man_hours)*$kurs);
				$pe_indirect_labour=(($indirect_labour*$man_hours)*$kurs);
				$foh=(($machine + $mould_mandrill + $foh_depresiasi + $biaya_rutin_bulanan + $foh_consumable)*$kurs);
				$pe_consumable=($consumable*$kurs);
				$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);

				$CI->db->query("update production_detail set wip_kurs='".$kurs."', wip_material='".$wip_material."' , wip_dl='".$pe_direct_labour."' , wip_foh='".$foh."', wip_il='".$pe_indirect_labour."', wip_consumable='".$pe_consumable."', finish_good='".$finish_good."' WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );
			
				$totalall=$finish_good;
			}else{
				$totalall=$dataproductiondetail->finish_good;
			}
			$no_spk=$datajurnal->id_spk;
			$Keterangan_INV=($ket).' ('.$datajurnal->no_so.' - '.$datajurnal->product.' - '.$no_spk.' - '.$datajurnal->no_surat_jalan.')';
			$datajurnal  	 = $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			
			$det_Jurnaltes = [];
			foreach($datajurnal AS $record){
				$tabel  = $record->menu;
				$posisi = $record->posisi;
				$field  = $record->field;
				$nokir  = $record->no_perkiraan;

				$totalall2 = (!empty($totalall))?$totalall:0;
				$param  = 'id';
				if ($posisi=='D'){
					$value_param  = $id;
					$val = $CI->Acc_model->GetData($tabel,$field,$param,$value_param);
					$nilaibayar = $val[0]->$field;
					$det_Jurnaltes[]  = array(
					  'tipe'			=> 'JV',
					  'nomor'			=> $Nomor_JV,
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $Keterangan_INV,
					  'no_reff'       => $no_request,
					  'debet'         => $totalall2,
					  'kredit'        => 0
					 );
				} elseif ($posisi=='K'){
					//$coa = 	$CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.id ='$id'")->result();
					$coa = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.id ='$id'")->result();
					if(!empty($coa)){
					$coa = $coa;
					}else {
					$coa = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join view_ms_category_part b on a.product=b.id WHERE a.id ='$id'")->result();
					}	
					
					
					$nokir=$coa[0]->coa_fg;
					$det_Jurnaltes[]  = array(
					  'tipe'			=> 'JV',
					  'nomor'			=> $Nomor_JV,
					  'tanggal'       => $tgl_voucher,
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $Keterangan_INV,
					  'no_reff'       => $no_request,
					  'debet'         => 0,
					  'kredit'        => $totalall2
					 
					 );
				}
			}
			//$CI->db->query("delete from jurnaltras WHERE jenis_jurnal='finish good intransit' and no_reff ='$id'");
			//$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall2, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.'-'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
			$CI->db->insert_batch(DBACC.'.jurnal',$det_Jurnaltes);
			
			$CI->db->query("UPDATE jurnal_product SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE id ='$id'");
			unset($det_Jurnaltes);
		  }
		}
		
		if($ket=='TRANSIT - CUSTOMER'){
		  $kodejurnal='JV007';
		  foreach($ArrDetailProduct as $keys => $values) {
			$id=$values['id_detail'];		
			
			$datajurnal = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.category='diterima customer' and a.status_jurnal='0' and a.id_detail ='$id' limit 1" )->row();
			$id=(!empty($datajurnal->id))?$datajurnal->id:0;
			$tgl_voucher = (!empty($datajurnal->tanggal))?$datajurnal->tanggal:date('Y-m-d');
			$no_request = $id;

			$id_detail=(!empty($datajurnal->id_detail))?$datajurnal->id_detail:0;
			$id_milik=(!empty($datajurnal->id_milik))?$datajurnal->id_milik:0;
			$total_nilai=(!empty($datajurnal->total_nilai))?$datajurnal->total_nilai:0;
			$id_spk=(!empty($datajurnal->id_spk))?$datajurnal->id_spk:0;
			$no_so=(!empty($datajurnal->no_so))?$datajurnal->no_so:0;
			$product=(!empty($datajurnal->product))?$datajurnal->product:0;
			$no_surat_jalan=(!empty($datajurnal->no_surat_jalan))?$datajurnal->no_surat_jalan:0;

			$dataproductiondetail=$CI->db->query("select * from production_detail where id='".$id_detail."' and id_milik ='".$id_milik."' limit 1")->row();
			
			
			if(!empty($dataproductiondetail->finish_good)){
				if($dataproductiondetail->finish_good==0){
				$datasodetailheader = $CI->db->query("SELECT * FROM laporan_per_hari_action WHERE id_milik ='".$datajurnal->id_milik."' limit 1" )->row();
			
				
				$kurs=1;
				$sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
				$dtkurs	= $CI->db->query($sqlkurs)->row();
				if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
				$wip_material=$datajurnal->total_nilai;
				$pe_direct_labour=(($datasodetailheader->direct_labour*$datasodetailheader->man_hours)*$kurs);
				$pe_indirect_labour=(($datasodetailheader->indirect_labour*$datasodetailheader->man_hours)*$kurs);
				$foh=(($datasodetailheader->machine + $datasodetailheader->mould_mandrill + $datasodetailheader->foh_depresiasi + $datasodetailheader->biaya_rutin_bulanan + $datasodetailheader->foh_consumable)*$kurs);
				$pe_consumable=($datasodetailheader->consumable*$kurs);
				$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);

				$CI->db->query("update production_detail set wip_kurs='".$kurs."', wip_material='".$wip_material."' , wip_dl='".$pe_direct_labour."' , wip_foh='".$foh."', wip_il='".$pe_indirect_labour."', wip_consumable='".$pe_consumable."', finish_good='".$finish_good."' WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );
			
			    $totalall=$finish_good;
				}
			    else{
				$totalall= (!empty($dataproductiondetail->finish_good))?$dataproductiondetail->finish_good:0;
			    }
				
			}
			
			// print_r($totalall);
			// exit;
			
			$no_spk=$id_spk;
			$Keterangan_INV=($ket).' ('.$no_so.' - '.$product.' - '.$no_spk.' - '.$no_surat_jalan.')';
			$datajurnal  	 = $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$det_Jurnaltes = [];
			if(!empty($datajurnal)){
				foreach($datajurnal AS $record){
					$tabel  = $record->menu;
					$posisi = $record->posisi;
					$field  = $record->field;
					$nokir  = $record->no_perkiraan;
					$totalall2 = (!empty($totalall))?$totalall:0;
					$param  = 'id';
					if ($posisi=='D'){
						$value_param  = $id;
						$val = $CI->Acc_model->GetData($tabel,$field,$param,$value_param);
						$nilaibayar = (!empty($val[0]->$field))?$val[0]->$field:0;
						$det_Jurnaltes[]  = array(
						'nomor'         => '',
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => $totalall2,
						'kredit'        => 0,
						'jenis_jurnal'  => 'intransit incustomer',
						'no_request'    => $no_request,
						'stspos'		=>1
						);
					} elseif ($posisi=='K'){
						$det_Jurnaltes[]  = array(
						'nomor'         => '',
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => 0,
						'kredit'        => $totalall2,
						'jenis_jurnal'  => 'intransit incustomer',
						'no_request'    => $no_request,
						'stspos'		=>1
						);
					}
				}
			}
			$CI->db->query("delete from jurnaltras WHERE jenis_jurnal='diterima customer' and no_reff ='$id'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall2, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.'-'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$CI->db->query("UPDATE jurnal_product SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE id ='$id'");
			unset($det_Jurnaltes);unset($datadetail);
		  }
		}
		if($ket=='incoming stok'){
			$kodejurnal='JV035';
			$id=$ArrDetailProduct;
		  	$Keterangan_INV="INCOMING STOCK ".$id;
			$datajurnal = $CI->db->query("select sum(total_nilai) as nilaibayar, tanggal, no_ipp from jurnal where kode_trans='".$id."' limit 1" )->row();
			$tgl_voucher = $datajurnal->tanggal;
			$no_ipp = $datajurnal->no_ipp;
			$no_request = $id;
			$nilaibayar	= 0;
			$totalbayar	= 0;
			$masterjurnal  	 = $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$det_Jurnaltes = [];
			$unbill_coa='';
			
			// print_r($id);
			// exit;
			
			foreach($masterjurnal AS $record){
				$posisi = $record->posisi;
				$nokir  = $record->no_perkiraan;
				$param  = 'id';
				$value_param  = $id;
				$jenisjurnal = $ket;
				$totalall = $datajurnal->nilaibayar;
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				} elseif ($posisi=='K'){
					$unbill_coa=$nokir;
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
			}
			$CI->db->query("UPDATE jurnal SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE kode_trans ='$id' and category='".$jenisjurnal."'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$data_po=$CI->db->query("select * from tran_po_header where no_po in (select no_ipp from warehouse_adjustment where kode_trans='".$id."') limit 1" )->row();
			if($data_po->mata_uang!='IDR') $unbill_coa='2101-01-04';
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl_voucher,
				'no_perkiraan'   => $unbill_coa,
				'keterangan'     => $Keterangan_INV,
				'no_reff'     	 => $data_po->no_po,
				'kredit'      	 => $datajurnal->nilaibayar,
				'debet'          => 0,
				'id_supplier'    => $data_po->id_supplier,
				'nama_supplier'  => $data_po->nm_supplier,
				'no_request'     => $id,
			);
			$CI->db->insert('tr_kartu_hutang',$datahutang);
			unset($det_Jurnaltes);unset($datadetail);unset($datahutang);
		}
		if($ket=='outgoing stok'){
			$kodejurnal='JV039';
			$id=$ArrDetailProduct;
		  	$Keterangan_INV="OUTGOING STOCK ".$id;
			$datajurnal = $CI->db->query("select sum(ROUND(total_nilai)) as nilaibayar, tanggal from jurnal where kode_trans='".$id."' limit 1" )->row();
			$tgl_voucher = $datajurnal->tanggal;
			$no_request = $id;
			$nilaibayar	= 0;
			$totalbayar	= 0;
			$sql="SELECT * FROM warehouse_adjustment where kode_trans='".$id."'";
			$wh=$CI->db->query($sql)->row();
			$kode_gudang = $wh->id_gudang_ke;
			$coa_deffered='';		
			if($kode_gudang=='17'){
				$sql_deff="select c.nm_customer, c.coa_deffered from so_number a 
				left join table_sales_order b on a.id_bq=b.id_bq 
				left join customer c on b.id_customer=c.id_customer
				where a.so_number='".$wh->no_so."'";
				$dt_coa=$CI->db->query($sql_deff)->row();
				if(!empty($dt_coa)) {
					$coa_deffered=$dt_coa->coa_deffered;
				}
				if($coa_deffered=="") {
					$sql_deff="select coa_biaya from costcenter where id='".$wh->id_gudang_ke."'";
					$dt_coa=$CI->db->query($sql_deff)->row();
					$coa_deffered=$dt_coa->coa_biaya;
				}
			}
			$masterjurnal  	 = $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$det_Jurnaltes = [];
			foreach($masterjurnal AS $record){
				$posisi = $record->posisi;
				$nokir  = $record->no_perkiraan;
				$param  = 'id';
				$value_param  = $id;
				$jenisjurnal = $ket;
				$totalall = $datajurnal->nilaibayar;
				if ($posisi=='D'){
					if($kode_gudang=='17'){
						$val = $CI->db->query("select ROUND(a.total_nilai) total_nilai,a.id_material,a.nm_material, a.gudang_ke, '".$coa_deffered."' coa_biaya from jurnal a  where a.kode_trans='".$id."'")->result();
					}else{
						$val = $CI->db->query("select ROUND(a.total_nilai) total_nilai,a.id_material,a.nm_material, a.gudang_ke, b.category_awal, c.coa_biaya from jurnal a left join con_nonmat_new b on a.id_material=b.code_group left join con_nonmat_category_costcenter c on a.gudang_ke=c.costcenter and b.category_awal=c.category where a.kode_trans='".$id."'")->result();
					}
					foreach($val AS $rec){
						$nilaibayar = $rec->total_nilai;
						$totalbayar=($totalbayar+$nilaibayar);
						$dtcoa_biaya=$rec->coa_biaya;
						if($dtcoa_biaya!=""){
						}else{
							$dtcoa_biaya=$nokir;
						}
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $dtcoa_biaya,
						  'keterangan'    => $rec->nm_material.' '.$id,
						  'no_reff'       => $id,
						  'debet'         => $nilaibayar,
						  'kredit'        => 0,
						  'jenis_jurnal'  => $jenisjurnal,
						  'no_request'    => $no_request,
						  'stspos'		  =>1
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
					  'kredit'        => $totalall,
					  'jenis_jurnal'  => $jenisjurnal,
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
			}
			$CI->db->query("UPDATE jurnal SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE kode_trans ='$id' and category='".$jenisjurnal."'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
		}
		if($ket=='incoming department'){
			$kodejurnal='JV036';
			$id=$ArrDetailProduct;
		  	$Keterangan_INV="INCOMING DEPARTMENT ".$id;
			$datajurnal	= $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$nilaibayar	= 0;
			$totalbayar	= 0;
			$unbill_coa='';$no_po='';
			foreach($datajurnal AS $record){
				$nokir1 = $record->no_perkiraan;
				$tabel  = $record->menu;
				$posisi = $record->posisi;
				$field  = $record->field;
				$nokir  = $record->no_perkiraan;
				$kd_bayar = $id;
				$param  = 'id';
				$value_param  = $id;
				$jenisjurnal = 'incoming department';
				if ($posisi=='D'){
					$val = $CI->db->query("select a.no_ipp, a.tanggal, a.total_nilai,a.id_material,a.nm_material, c.coa from jurnal a left join rutin_non_planning_detail b on a.id_material=b.id left join rutin_non_planning_header c on b.no_pr=c.no_pr where a.kode_trans='".$kd_bayar."'")->result();
					foreach($val AS $rec){
						$tgl_voucher = $rec->tanggal;
						$no_po = $rec->no_ipp;
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
						  'no_request'    => $id
						 );
					}
				} elseif ($posisi=='K'){
					$unbill_coa=$nokir;
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
					   'no_request'    => $id
					 );
				}
			}

			$CI->db->query("UPDATE jurnal SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE kode_trans ='$id' and category='".$jenisjurnal."'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalbayar, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$data_po=$CI->db->query("select * from tran_po_header where no_po='".$no_po."' limit 1" )->row();
			if($data_po->mata_uang!='IDR') $unbill_coa='2101-01-04';
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl_voucher,
				'no_perkiraan'   => $unbill_coa,
				'keterangan'     => $Keterangan_INV,
				'no_reff'     	 => $no_po,
				'kredit'      	 => $totalbayar,
				'debet'          => 0,
				'id_supplier'    => $data_po->id_supplier,
				'nama_supplier'  => $data_po->nm_supplier,
				'no_request'     => $id,
			);
			$CI->db->insert('tr_kartu_hutang',$datahutang);
			unset($det_Jurnaltes);unset($datadetail);unset($datahutang);
		}
		if($ket=='incoming asset'){
			$kodejurnal='JV038';
			$id=$ArrDetailProduct;
		  	$Keterangan_INV="INCOMING ASSET ".$id;
			$datajurnal	= $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$nilaibayar	= 0;
			$totalbayar	= 0;
			$unbill_coa='';$no_po='';
			foreach($datajurnal AS $record){
				$nokir1 = $record->no_perkiraan;
				$tabel  = $record->menu;
				$posisi = $record->posisi;
				$field  = $record->field;
				$nokir  = $record->no_perkiraan;
				$kd_bayar = $id;
				$param  = 'id';
				$value_param  = $id;
				$jenisjurnal = 'incoming asset';
				if ($posisi=='D'){
					$val = $CI->db->query("select a.no_ipp, a.tanggal, a.total_nilai,a.id_material,a.nm_material, b.coa from jurnal a left join asset_planning b on a.id_material=b.code_plan where a.kode_trans='".$kd_bayar."'")->result();
					foreach($val AS $rec){
						$tgl_voucher = $rec->tanggal;
						$no_po = $rec->no_ipp;
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
						  'no_request'    => $id
						 );
					}
				} elseif ($posisi=='K'){
					$unbill_coa=$nokir;
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
					   'no_request'    => $id
					 );
				}
			}

			$CI->db->query("UPDATE jurnal SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE kode_trans ='$id' and category='".$jenisjurnal."'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalbayar, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$data_po=$CI->db->query("select * from tran_po_header where no_po='".$no_po."' limit 1" )->row();
			if($data_po->mata_uang!='IDR') $unbill_coa='2101-01-05';
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl_voucher,
				'no_perkiraan'   => $unbill_coa,
				'keterangan'     => $Keterangan_INV,
				'no_reff'     	 => $no_po,
				'kredit'      	 => $totalbayar,
				'debet'          => 0,
				'id_supplier'    => $data_po->id_supplier,
				'nama_supplier'  => $data_po->nm_supplier,
				'no_request'     => $id,
			);
			$CI->db->insert('tr_kartu_hutang',$datahutang);
			unset($det_Jurnaltes);unset($datadetail);
		}
		
		if($ket=='incoming project'){
			$kodejurnal='JV078';
			$id=$ArrDetailProduct;
		  	$Keterangan_INV="INCOMING PROJECT ".$id;
			$datajurnal = $CI->db->query("select sum(total_nilai) as nilaibayar, tanggal, no_ipp from jurnal where kode_trans='".$id."' limit 1" )->row();
			$tgl_voucher = $datajurnal->tanggal;
			$no_ipp = $datajurnal->no_ipp;
			$no_request = $id;
			$nilaibayar	= 0;
			$totalbayar	= 0;
			$masterjurnal  	 = $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$det_Jurnaltes = [];
			$unbill_coa='';
			
			
			
			foreach($masterjurnal AS $record){
				$posisi = $record->posisi;
				$nokir  = $record->no_perkiraan;
				$param  = 'id';
				$value_param  = $id;
				$jenisjurnal = $ket;
				$totalall = $datajurnal->nilaibayar;
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				} elseif ($posisi=='K'){
					$unbill_coa=$nokir;
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
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
			}
			$CI->db->query("UPDATE jurnal SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE kode_trans ='$id' and category='".$jenisjurnal."'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$data_po=$CI->db->query("select * from tran_po_header where no_po in (select no_ipp from warehouse_adjustment where kode_trans='".$id."') limit 1" )->row();
			if($data_po->mata_uang!='IDR') $unbill_coa='2101-01-04';
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl_voucher,
				'no_perkiraan'   => $unbill_coa,
				'keterangan'     => $Keterangan_INV,
				'no_reff'     	 => $data_po->no_po,
				'kredit'      	 => $datajurnal->nilaibayar,
				'debet'          => 0,
				'id_supplier'    => $data_po->id_supplier,
				'nama_supplier'  => $data_po->nm_supplier,
				'no_request'     => $id,
			);
			$CI->db->insert('tr_kartu_hutang',$datahutang);
			unset($det_Jurnaltes);unset($datadetail);unset($datahutang);
		}
				
	}

	function insert_jurnal_cutting($ArrData, $kode_pro){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		//GetNilai GinishGood
		$getCuttingHeader	= $CI->db->get_where('so_cutting_header',array('id'=>$kode_pro))->result();
		$id_pro 			= $getCuttingHeader[0]->id_pro_det;
		$id_deadstock 		= $getCuttingHeader[0]->id_deadstok;
		
		$getReportFG 	= $CI->db->order_by('id','DESC')->limit(1)->get_where('data_erp_fg',array('id_pro'=>$id_pro,'jenis'=>'in cutting'))->result_array();
		$ArrFG_OUT = [];
		if(!empty($getReportFG)){
			$ArrFG_OUT[0]['tanggal'] = date('Y-m-d');
			$ArrFG_OUT[0]['keterangan'] = 'Finish Good to WIP (Cutting)';
			$ArrFG_OUT[0]['no_so'] 	= (!empty($getReportFG[0]['no_so']))?$getReportFG[0]['no_so']:NULL;
			$ArrFG_OUT[0]['product'] = (!empty($getReportFG[0]['product']))?$getReportFG[0]['product']:NULL;
			$ArrFG_OUT[0]['no_spk'] = (!empty($getReportFG[0]['no_spk']))?$getReportFG[0]['no_spk']:NULL;
			$ArrFG_OUT[0]['kode_trans'] = (!empty($getReportFG[0]['kode_trans']))?$getReportFG[0]['kode_trans']:NULL;
			$ArrFG_OUT[0]['id_pro_det'] = (!empty($getReportFG[0]['id_pro_det']))?$getReportFG[0]['id_pro_det']:NULL;
			$ArrFG_OUT[0]['qty'] = (!empty($getReportFG[0]['qty']))?$getReportFG[0]['qty']:NULL;
			$ArrFG_OUT[0]['nilai_unit'] = (!empty($getReportFG[0]['nilai_unit']))?$getReportFG[0]['nilai_unit']:0;
			$ArrFG_OUT[0]['nilai_wip'] = (!empty($getReportFG[0]['nilai_wip']))?$getReportFG[0]['nilai_wip']:0;
			$ArrFG_OUT[0]['material'] = (!empty($getReportFG[0]['material']))?$getReportFG[0]['material']:0;
			$ArrFG_OUT[0]['wip_direct'] = (!empty($getReportFG[0]['wip_direct']))?$getReportFG[0]['wip_direct']:0;
			$ArrFG_OUT[0]['wip_indirect'] = (!empty($getReportFG[0]['wip_indirect']))?$getReportFG[0]['wip_indirect']:0;
			$ArrFG_OUT[0]['wip_consumable'] = (!empty($getReportFG[0]['wip_consumable']))?$getReportFG[0]['wip_consumable']:0;
			$ArrFG_OUT[0]['wip_foh'] = (!empty($getReportFG[0]['wip_foh']))?$getReportFG[0]['wip_foh']:0;
			$ArrFG_OUT[0]['created_by'] = $UserName;
			$ArrFG_OUT[0]['created_date'] = $DateTime;
			$ArrFG_OUT[0]['id_trans'] = (!empty($getReportFG[0]['id_trans']))?$getReportFG[0]['id_trans']:NULL;
			$ArrFG_OUT[0]['id_pro'] = (!empty($getReportFG[0]['id_pro']))?$getReportFG[0]['id_pro']:0;
			$ArrFG_OUT[0]['qty_ke'] = (!empty($getReportFG[0]['qty_ke']))?$getReportFG[0]['qty_ke']:0;
			$ArrFG_OUT[0]['jenis'] = 'out cutting';
		}

		//Deadstock
		$getReportFG_DEADSTOCK 	= $CI->db->order_by('id','DESC')->limit(1)->get_where('data_erp_fg',array('id_pro_det'=>$id_deadstock,'jenis'=>'in deadstok'))->result_array();
		$ArrFG_OUT_DEADSTOCK = [];
		if(!empty($getReportFG_DEADSTOCK)){
			$ArrFG_OUT_DEADSTOCK[0]['tanggal'] = date('Y-m-d');
			$ArrFG_OUT_DEADSTOCK[0]['keterangan'] = 'Finish Good to WIP (Cutting Deadstock)';
			$ArrFG_OUT_DEADSTOCK[0]['no_so'] 	= $getReportFG_DEADSTOCK[0]['no_so'];
			$ArrFG_OUT_DEADSTOCK[0]['product'] = $getReportFG_DEADSTOCK[0]['product'];
			$ArrFG_OUT_DEADSTOCK[0]['no_spk'] = $getReportFG_DEADSTOCK[0]['no_spk'];
			$ArrFG_OUT_DEADSTOCK[0]['kode_trans'] = $getReportFG_DEADSTOCK[0]['kode_trans'];
			$ArrFG_OUT_DEADSTOCK[0]['id_pro_det'] = $getReportFG_DEADSTOCK[0]['id_pro_det'];
			$ArrFG_OUT_DEADSTOCK[0]['qty'] = $getReportFG_DEADSTOCK[0]['qty'];
			$ArrFG_OUT_DEADSTOCK[0]['nilai_unit'] = $getReportFG_DEADSTOCK[0]['nilai_unit'];
			$ArrFG_OUT_DEADSTOCK[0]['nilai_wip'] = $getReportFG_DEADSTOCK[0]['nilai_wip'];
			$ArrFG_OUT_DEADSTOCK[0]['material'] = 0;
			$ArrFG_OUT_DEADSTOCK[0]['wip_direct'] = 0;
			$ArrFG_OUT_DEADSTOCK[0]['wip_indirect'] = 0;
			$ArrFG_OUT_DEADSTOCK[0]['wip_consumable'] = 0;
			$ArrFG_OUT_DEADSTOCK[0]['wip_foh'] = 0;
			$ArrFG_OUT_DEADSTOCK[0]['created_by'] = $UserName;
			$ArrFG_OUT_DEADSTOCK[0]['created_date'] = $DateTime;
			$ArrFG_OUT_DEADSTOCK[0]['id_trans'] = $getReportFG_DEADSTOCK[0]['id_trans'];
			$ArrFG_OUT_DEADSTOCK[0]['id_pro'] = $getReportFG_DEADSTOCK[0]['id_pro'];
			$ArrFG_OUT_DEADSTOCK[0]['jenis'] = 'out deadstok';
		}

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailNew = [];
		$ArrDetailProduct = [];
		$nomor = 0;
		$ArrWIP_IN = [];
		$ArrWIP_IN_DEADSTOCK = [];

		$temp = [];
		$category = 'cutting loose';
		foreach ($ArrData as $key => $value) {$nomor++;
			$get_detProduksi	= $CI->db->get_where('so_cutting_detail',array('id'=>$value['id']))->result();
			$kode_spk 			= $value['id'];
			$kode_trans 		= $kode_spk.'/'.$kode_pro;
			$id_milik 			= $get_detProduksi[0]->id_milik;
			$product_ke 		= $get_detProduksi[0]->cutting_ke;
			$id_bq 				= $get_detProduksi[0]->id_bq;
			$nomor_so 			= get_name('so_number','so_number','id_bq', $id_bq);
			$no_spk 			= get_name('so_detail_header','no_spk','id', $id_milik);
			$product 			= $get_detProduksi[0]->id_category;
			$length_cutting 	= $get_detProduksi[0]->length_split;
			$length_full 		= $get_detProduksi[0]->length;
			$no_ipp 			= str_replace('BQ-','',$id_bq);

			$keterangan 		= $product.'/'.$id_milik.'/'.$nomor_so.'.'.$product_ke.'/'.$no_spk.'/'.$kode_pro;

			$ArrDetailNew[$nomor]['kode_trans'] 	= $kode_trans;
			$ArrDetailNew[$nomor]['category'] 		= $category;
			$ArrDetailNew[$nomor]['gudang_dari'] 	= 15;
			$ArrDetailNew[$nomor]['gudang_ke'] 		= 14;
			$ArrDetailNew[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailNew[$nomor]['no_ipp']			= $no_ipp;
			$ArrDetailNew[$nomor]['no_so'] 			= $nomor_so;
			$ArrDetailNew[$nomor]['product'] 		= $product;
			$ArrDetailNew[$nomor]['id_detail'] 		= $value['id'];
			$ArrDetailNew[$nomor]['id_milik'] 		= $id_milik;
			$ArrDetailNew[$nomor]['id_spk'] 		= $no_spk;
			$ArrDetailNew[$nomor]['spk'] 			= $kode_pro;
			$ArrDetailNew[$nomor]['id_material'] 	= $kode_pro;
			$ArrDetailNew[$nomor]['nm_material'] 	= $length_cutting;
			$ArrDetailNew[$nomor]['spec'] 			= $id_milik;
			$ArrDetailNew[$nomor]['cost_book'] 		= $value['finish_good'];
			$ArrDetailNew[$nomor]['qty'] 			= 1;
			$ArrDetailNew[$nomor]['created_by'] 	= $UserName;
			$ArrDetailNew[$nomor]['created_date'] 	= $DateTime;
			$ArrDetailNew[$nomor]['total_nilai'] 	= $value['finish_good'];

			if(!empty($getReportFG)){
				$nilai_wip = ($ArrFG_OUT[0]['nilai_wip'] > 0)?$length_cutting/$length_full*$ArrFG_OUT[0]['nilai_wip']:0;
				$nilai_material = ($ArrFG_OUT[0]['material'] > 0)?$length_cutting/$length_full*$ArrFG_OUT[0]['material']:0;
				$nilai_wip_direct = ($ArrFG_OUT[0]['wip_direct'] > 0)?$length_cutting/$length_full*$ArrFG_OUT[0]['wip_direct']:0;
				$nilai_wip_indirect = ($ArrFG_OUT[0]['wip_indirect'] > 0)?$length_cutting/$length_full*$ArrFG_OUT[0]['wip_indirect']:0;
				$nilai_wip_consumable = ($ArrFG_OUT[0]['wip_consumable'] > 0)?$length_cutting/$length_full*$ArrFG_OUT[0]['wip_consumable']:0;
				$nilai_wip_foh = ($ArrFG_OUT[0]['wip_foh'] > 0)?$length_cutting/$length_full*$ArrFG_OUT[0]['wip_foh']:0;
				
				$ArrWIP_IN[$nomor]['tanggal'] = date('Y-m-d');
				$ArrWIP_IN[$nomor]['keterangan'] = 'Finish Good to WIP (Cutting)';
				$ArrWIP_IN[$nomor]['no_so'] 	= (!empty($getReportFG[0]['no_so']))?$getReportFG[0]['no_so']:NULL;
				$ArrWIP_IN[$nomor]['product'] = (!empty($getReportFG[0]['product']))?$getReportFG[0]['product']:NULL;
				$ArrWIP_IN[$nomor]['no_spk'] = (!empty($getReportFG[0]['no_spk']))?$getReportFG[0]['no_spk']:NULL;
				$ArrWIP_IN[$nomor]['kode_trans'] = (!empty($getReportFG[0]['kode_trans']))?$getReportFG[0]['kode_trans']:NULL;
				$ArrWIP_IN[$nomor]['id_pro_det'] = $id_pro;
				$ArrWIP_IN[$nomor]['qty'] = 1;
				$ArrWIP_IN[$nomor]['nilai_wip'] = $nilai_wip;
				$ArrWIP_IN[$nomor]['material'] = $nilai_material;
				$ArrWIP_IN[$nomor]['wip_direct'] = $nilai_wip_direct;
				$ArrWIP_IN[$nomor]['wip_indirect'] = $nilai_wip_indirect;
				$ArrWIP_IN[$nomor]['wip_consumable'] = $nilai_wip_consumable;
				$ArrWIP_IN[$nomor]['wip_foh'] = $nilai_wip_foh;
				$ArrWIP_IN[$nomor]['created_by'] = $UserName;
				$ArrWIP_IN[$nomor]['created_date'] = $DateTime;
				$ArrWIP_IN[$nomor]['jenis'] = 'in cutting';
				$ArrWIP_IN[$nomor]['id_trans'] = $kode_spk;
			}

			//DEADSTOCK Cutting
			$getDetailDead 	= $CI->db->get_where('data_erp_fg',array('id_pro_det'=>$id_deadstock,'jenis'=>'in deadstok'))->result_array();
			if(!empty($getDetailDead)){
				$nilai_wip = ($getDetailDead[0]['nilai_wip'] > 0)?$length_cutting/$length_full*$getDetailDead[0]['nilai_wip']:0;
				$ArrWIP_IN_DEADSTOCK[$nomor]['tanggal'] = date('Y-m-d');
				$ArrWIP_IN_DEADSTOCK[$nomor]['keterangan'] = 'Finish Good to WIP (Cutting Deadstock)';
				$ArrWIP_IN_DEADSTOCK[$nomor]['no_so'] 	= (!empty($getDetailDead[0]['no_so']))?$getDetailDead[0]['no_so']:NULL;
				$ArrWIP_IN_DEADSTOCK[$nomor]['product'] = (!empty($getDetailDead[0]['product']))?$getDetailDead[0]['product']:NULL;
				$ArrWIP_IN_DEADSTOCK[$nomor]['no_spk'] = (!empty($getDetailDead[0]['no_spk']))?$getDetailDead[0]['no_spk']:NULL;
				$ArrWIP_IN_DEADSTOCK[$nomor]['kode_trans'] = (!empty($getDetailDead[0]['kode_trans']))?$getDetailDead[0]['kode_trans']:NULL;
				$ArrWIP_IN_DEADSTOCK[$nomor]['id_pro_det'] = (!empty($getDetailDead[0]['id_pro_det']))?$getDetailDead[0]['id_pro_det']:NULL;
				$ArrWIP_IN_DEADSTOCK[$nomor]['qty'] = 1;
				$ArrWIP_IN_DEADSTOCK[$nomor]['nilai_wip'] = $nilai_wip;
				$ArrWIP_IN_DEADSTOCK[$nomor]['material'] = 0;
				$ArrWIP_IN_DEADSTOCK[$nomor]['wip_direct'] = 0;
				$ArrWIP_IN_DEADSTOCK[$nomor]['wip_indirect'] = 0;
				$ArrWIP_IN_DEADSTOCK[$nomor]['wip_consumable'] = 0;
				$ArrWIP_IN_DEADSTOCK[$nomor]['wip_foh'] = 0;
				$ArrWIP_IN_DEADSTOCK[$nomor]['created_by'] = $UserName;
				$ArrWIP_IN_DEADSTOCK[$nomor]['created_date'] = $DateTime;
				$ArrWIP_IN_DEADSTOCK[$nomor]['jenis'] = 'in cutting deadstok';
				$ArrWIP_IN_DEADSTOCK[$nomor]['id_trans'] = $kode_spk;
			}


			$key_uniq = $kode_pro;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $value['finish_good'];

		}
		
		$kredit = 999;
		
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 14;
			$ArrJurnal[$nomor]['keterangan'] = 'cutting wip';
			$ArrJurnal[$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 15;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key.'-'.$key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}
		// print_r($ArrFG_OUT_DEADSTOCK);
		// print_r($ArrWIP_IN_DEADSTOCK);
		// exit;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		if(!empty($ArrDetailNew)){
			$CI->db->insert_batch('jurnal_product',$ArrDetailNew);
		}

		if(!empty($ArrFG_OUT)){
			$CI->db->insert_batch('data_erp_fg',$ArrFG_OUT);
		}

		if(!empty($ArrWIP_IN)){
			$CI->db->insert_batch('data_erp_wip_group',$ArrWIP_IN);
		}

		if(!empty($ArrFG_OUT_DEADSTOCK)){
			$CI->db->insert_batch('data_erp_fg',$ArrFG_OUT_DEADSTOCK);
		}

		if(!empty($ArrWIP_IN_DEADSTOCK)){
			$CI->db->insert_batch('data_erp_wip_group',$ArrWIP_IN_DEADSTOCK);
		}
	}

	function insert_jurnal_qc_cutting($ArrData, $kode_pro){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailNew = [];
		$ArrDetailProduct = [];
		$nomor = 0;

		$temp = [];
		$category = 'quality control cutting';

		$getJurnalWIP = $CI->db->where_in('id_material',$ArrData)->get_where('jurnal_product',array('category'=>'cutting loose'))->result_array();

		foreach ($getJurnalWIP as $key => $value) {$nomor++;
			
			$ArrDetailNew[$nomor]['kode_trans'] 	= $value['kode_trans'];
			$ArrDetailNew[$nomor]['category'] 		= $category;
			$ArrDetailNew[$nomor]['gudang_dari'] 	= 14;
			$ArrDetailNew[$nomor]['gudang_ke'] 		= 15;
			$ArrDetailNew[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailNew[$nomor]['no_ipp']			= $value['no_ipp'];
			$ArrDetailNew[$nomor]['no_so'] 			= $value['no_so'];
			$ArrDetailNew[$nomor]['product'] 		= $value['product'];
			$ArrDetailNew[$nomor]['id_detail'] 		= $value['id_detail'];
			$ArrDetailNew[$nomor]['id_milik'] 		= $value['id_milik'];
			$ArrDetailNew[$nomor]['id_spk'] 		= $value['id_spk'];
			$ArrDetailNew[$nomor]['spk'] 			= $value['spk'];
			$ArrDetailNew[$nomor]['id_material'] 	= $value['id_material'];
			$ArrDetailNew[$nomor]['nm_material'] 	= $value['nm_material'];
			$ArrDetailNew[$nomor]['spec'] 			= $value['spec'];
			$ArrDetailNew[$nomor]['cost_book'] 		= $value['cost_book'];
			$ArrDetailNew[$nomor]['qty'] 			= $value['qty'];
			$ArrDetailNew[$nomor]['created_by'] 	= $UserName;
			$ArrDetailNew[$nomor]['created_date'] 	= $DateTime;
			$ArrDetailNew[$nomor]['total_nilai'] 	= $value['total_nilai'];

			$key_uniq = $kode_pro;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $value['total_nilai'];

		}
		
		$kredit = 999;
		
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 15;
			$ArrJurnal[$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 14;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'cutting wip';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $kode_pro;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key.'-'.$key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}
		// print_r($ArrJurnal);
		// print_r($ArrDetailNew);
		// exit;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		if(!empty($ArrDetailNew)){
			$CI->db->insert_batch('jurnal_product',$ArrDetailNew);
		}
	}

	function insert_jurnal_spool($spool){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailNew = [];
		$ArrDetailProduct = [];
		$nomor = 0;

		$temp = [];
		$category = 'wip spooling';

		$ArrData1	= $CI->db->select("id_produksi, id, 'loose' AS typeProduct, id_milik, finish_good, id_category, '' AS length_split, product_ke AS cutting_ke")->get_where('production_detail',array('spool_induk'=>$spool))->result_array();
		$ArrData2	= $CI->db->select("id_bq AS id_produksi, id, 'cutting' AS typeProduct, id_milik, finish_good, id_category, length_split, cutting_ke")->get_where('so_cutting_detail',array('spool_induk'=>$spool))->result_array();
		$ArrData3	= $CI->db->select("CONCAT('PRO-',no_ipp) AS id_produksi, id, 'deadstok' AS typeProduct, id_milik, finish_good, CONCAT(product_name,', ',type_std,' ',resin) AS id_category, '' AS length_split, qty_ke AS cutting_ke")->get_where('deadstok',array('spool_induk'=>$spool))->result_array();
		$ArrData	= array_merge($ArrData1,$ArrData2,$ArrData3);

		foreach ($ArrData as $key => $value) {
			$nomor++;
			
			$id_bq 				= str_replace('PRO','BQ',$value['id_produksi']);
			$id_key 			= $value['id'];
			$tipe 				= $value['typeProduct'];
			$kode_trans 		= $spool;
			$no_ipp 			= str_replace('BQ-','',$id_bq);
			$nomor_so 			= get_name('so_number','so_number','id_bq', $id_bq);
			$id_milik 			= $value['id_milik'];
			$finish_good 		= $value['finish_good'];
			$no_spk 			= get_name('so_detail_header','no_spk','id', $id_milik);
			$product 			= $value['id_category'];
			$product_ke 		= $value['cutting_ke'];
			$length_cutting 	= $value['length_split'];

			$ArrDetailNew[$nomor]['kode_trans'] 	= $kode_trans;
			$ArrDetailNew[$nomor]['category'] 		= $category;
			$ArrDetailNew[$nomor]['gudang_dari'] 	= 15;
			$ArrDetailNew[$nomor]['gudang_ke'] 		= 14;
			$ArrDetailNew[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailNew[$nomor]['no_ipp']			= $no_ipp;
			$ArrDetailNew[$nomor]['no_so'] 			= $nomor_so;
			$ArrDetailNew[$nomor]['product'] 		= $product;
			$ArrDetailNew[$nomor]['id_detail'] 		= $id_key;
			$ArrDetailNew[$nomor]['id_milik'] 		= $id_milik;
			$ArrDetailNew[$nomor]['id_spk'] 		= $no_spk;
			$ArrDetailNew[$nomor]['spk'] 			= $product_ke;
			$ArrDetailNew[$nomor]['id_material'] 	= $length_cutting;
			$ArrDetailNew[$nomor]['nm_material'] 	= $tipe;
			$ArrDetailNew[$nomor]['spec'] 			= NULL;
			$ArrDetailNew[$nomor]['cost_book'] 		= $finish_good;
			$ArrDetailNew[$nomor]['qty'] 			= 1;
			$ArrDetailNew[$nomor]['created_by'] 	= $UserName;
			$ArrDetailNew[$nomor]['created_date'] 	= $DateTime;
			$ArrDetailNew[$nomor]['total_nilai'] 	= $finish_good;

			$key_uniq = $spool;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $finish_good;

		}
		
		$kredit = 999;
		
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 14;
			$ArrJurnal[$nomor]['keterangan'] = 'spooling wip';
			$ArrJurnal[$nomor]['kode_trans'] = $spool;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 15;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $spool;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}
		// print_r($ArrJurnal);
		// print_r($ArrDetailNew);
		// exit;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		if(!empty($ArrDetailNew)){
			$CI->db->insert_batch('jurnal_product',$ArrDetailNew);
		}
	}

	function insert_jurnal_qc_spooling($spool){
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrJurnal = [];
		$ArrUpdate = [];
		$ArrDetailNew = [];
		$ArrDetailProduct = [];
		$nomor = 0;

		$temp = [];
		$category = 'quality control spooling';

		$getJurnalWIP = $CI->db->get_where('jurnal_product',array('category'=>'wip spooling','kode_trans'=>$spool))->result_array();

		foreach ($getJurnalWIP as $key => $value) {$nomor++;
			
			$ArrDetailNew[$nomor]['kode_trans'] 	= $value['kode_trans'];
			$ArrDetailNew[$nomor]['category'] 		= $category;
			$ArrDetailNew[$nomor]['gudang_dari'] 	= 14;
			$ArrDetailNew[$nomor]['gudang_ke'] 		= 15;
			$ArrDetailNew[$nomor]['tanggal'] 		= date('Y-m-d');
			$ArrDetailNew[$nomor]['no_ipp']			= $value['no_ipp'];
			$ArrDetailNew[$nomor]['no_so'] 			= $value['no_so'];
			$ArrDetailNew[$nomor]['product'] 		= $value['product'];
			$ArrDetailNew[$nomor]['id_detail'] 		= $value['id_detail'];
			$ArrDetailNew[$nomor]['id_milik'] 		= $value['id_milik'];
			$ArrDetailNew[$nomor]['id_spk'] 		= $value['id_spk'];
			$ArrDetailNew[$nomor]['spk'] 			= $value['spk'];
			$ArrDetailNew[$nomor]['id_material'] 	= $value['id_material'];
			$ArrDetailNew[$nomor]['nm_material'] 	= $value['nm_material'];
			$ArrDetailNew[$nomor]['spec'] 			= $value['spec'];
			$ArrDetailNew[$nomor]['cost_book'] 		= $value['cost_book'];
			$ArrDetailNew[$nomor]['qty'] 			= $value['qty'];
			$ArrDetailNew[$nomor]['created_by'] 	= $UserName;
			$ArrDetailNew[$nomor]['created_date'] 	= $DateTime;
			$ArrDetailNew[$nomor]['total_nilai'] 	= $value['total_nilai'];

			$key_uniq = $spool;
			if(!array_key_exists($key_uniq, $temp)) {
				$temp[$key_uniq] = 0;
			}
			$temp[$key_uniq] += $value['total_nilai'];

		}
		
		$kredit = 999;
		
		foreach ($temp as $key => $value) {$nomor++;
			//DEBIT
			$ArrJurnal[$nomor]['category'] = $category;
			$ArrJurnal[$nomor]['posisi'] = 'DEBIT';
			$ArrJurnal[$nomor]['amount'] = $value;
			$ArrJurnal[$nomor]['gudang'] = 15;
			$ArrJurnal[$nomor]['keterangan'] = 'finish good';
			$ArrJurnal[$nomor]['kode_trans'] = $spool;
			$ArrJurnal[$nomor]['hub_product'] = $key;
			$ArrJurnal[$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$nomor]['updated_date'] = $DateTime;

			//KREDIT
			$ArrJurnal[$kredit.$nomor]['category'] = $category;
			$ArrJurnal[$kredit.$nomor]['posisi'] = 'KREDIT';
			$ArrJurnal[$kredit.$nomor]['amount'] = $value;
			$ArrJurnal[$kredit.$nomor]['gudang'] = 14;
			$ArrJurnal[$kredit.$nomor]['keterangan'] = 'spooling wip';
			$ArrJurnal[$kredit.$nomor]['kode_trans'] = $spool;
			$ArrJurnal[$kredit.$nomor]['hub_product'] = $key;
			$ArrJurnal[$kredit.$nomor]['updated_by'] = $UserName;
			$ArrJurnal[$kredit.$nomor]['updated_date'] = $DateTime;
		}
		// print_r($ArrJurnal);
		// print_r($ArrDetailNew);
		// exit;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		if(!empty($ArrDetailNew)){
			$CI->db->insert_batch('jurnal_product',$ArrDetailNew);
		}
		
		
	}
	
	
	
	
	
	
function auto_jurnal_produksi($id,$milik,$category,$ket){
		$CI 	=& get_instance();
		$CI->load->model('Jurnal_model');
		$CI->load->model('Acc_model');
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		
		
		if($ket=='PRODUKSI - WIP'){
		  $kodejurnal='JV076';
		  	$id=$id;
			$idmilik=$milik;
			$idcategory=$category;
			
			
			
				
			$no_request = $id;

			$datasodetailheader = $CI->db->query("SELECT * FROM laporan_wip_per_hari_action WHERE id_milik ='".$idmilik."' AND id_produksi ='".$id."'  AND id_category ='".$idcategory."' ORDER BY id DESC limit 1" )->row();
			
			
				$tgl_voucher = $datasodetailheader->date;			
				$kurs=$datasodetailheader->kurs;
				$wip_material=$datasodetailheader->real_material*$kurs;
				
				$pe_direct_labour=(($datasodetailheader->direct_labour)*$kurs);
				$pe_indirect_labour=(($datasodetailheader->indirect_labour)*$kurs);
				$foh=(($datasodetailheader->machine + $datasodetailheader->mould_mandrill + $datasodetailheader->foh_depresiasi + $datasodetailheader->biaya_rutin_bulanan + $datasodetailheader->foh_consumable)*$kurs);
				$pe_consumable=($datasodetailheader->consumable*$kurs);
				$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
				
			
			if($datasodetailheader->id_category=='pipe'){			
				$coa_wip ='1103-03-02';				
			}
			else{
			    $coa_wip ='1103-03-03';		
			}
			
			$masterjurnal	= $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			$totaldebit=0; $totalkredit=0; $coa_cogm=''; $no_spk=$id;
			$det_Jurnaltes = [];
			foreach($masterjurnal AS $record){
				$debit=0;$kredit=0;	
				
				$nokir  	= $record->no_perkiraan;
				
				
				$posisi 	= $record->posisi;
				$parameter  = $record->parameter_no;
				$keterangan = $record->keterangan;
				
				if ($parameter=='6'){
					$kredit=($wip_material);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => $kredit,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='7'){
					$kredit=($pe_direct_labour);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => $kredit,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='8'){
					$kredit=($pe_indirect_labour);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => $kredit,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='9'){
					$kredit=($pe_consumable);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => $kredit,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='10'){
					$kredit=($foh);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => $kredit,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				
				
				if ($parameter=='1'){
					$debit=($wip_material);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => ($debit),
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='2'){
					$debit=($pe_direct_labour);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => ($debit),
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='3'){
					$debit=($pe_indirect_labour);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => ($debit),
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='4'){
					$debit=($pe_consumable);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => ($debit),
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				if ($parameter=='5'){
					$debit=($foh);
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan.' '.$id,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => ($debit),
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				
				$totaldebit+=$debit;$totalkredit+=$kredit;
			}
			
			$CI->db->query("delete from jurnaltras WHERE jenis_jurnal='produksi wip' and no_reff ='$id'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $datasodetailheader->id;
			$Keterangan_INV = 'Jurnal Produksi - WIP';
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalkredit, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
		  
		}
		
	}	
		//SYAMSUDIN 29/08/2024
		
		function insert_jurnal_adjustment($ArrData,$GudangFrom,$GudangTo,$kode_trans,$category,$ket_min,$ket_plus){ 
		$CI 	=& get_instance();
		$data_session	= $CI->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');

		$getHeaderAdjust = $CI->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();
		$DATE_JURNAL = (!empty($getHeaderAdjust[0]->tanggal))?$getHeaderAdjust[0]->tanggal:$getHeaderAdjust[0]->created_date;
		$no_SO = (!empty($getHeaderAdjust[0]->no_so))?$getHeaderAdjust[0]->no_so:$getHeaderAdjust[0]->no_so;
		
		$getGudang = $CI->db->get_where('warehouse', array('id'=>$GudangFrom))->result();		
		$gudang = $getGudang[0]->category;
		
		$getGudang2 = $CI->db->get_where('warehouse', array('id'=>$GudangTo))->result();		
		$gudang2 = $getGudang2[0]->category;


		$SUM_PRICE = 0;
		$ArrDetail = [];
		$ArrDetailNew = [];
// agus
		$det_Jurnaltes = [];
		$datadetail=[];
		$kodejurnal='';
		$jenis_jurnal='';
		$ada_jurnal='';
		
	
		
// ok
		if($category=='adjustment stok' && $ket_plus=='plus'){
			$kodejurnal = 'JV081';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}elseif($category=='adjustment stok' && $ket_plus=='minus'){
			$kodejurnal = 'JV082';
			$jenis_jurnal = 'pindah gudang';
			$ada_jurnal='ok';
		}
		

		$no_request = $kode_trans;
		$tgl_voucher =date('Y-m-d');
		
		$CI->load->model('jurnal_model');
		$Nomor_JV = $CI->jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
		$Bln	  = substr($tgl_voucher,5,2);
		$Thn	  = substr($tgl_voucher,0,4);
// end agus		
		foreach ($ArrData as $key => $value) {
// revisi agus

			//$PRICE = (!empty($GetCostBook[$key]))?$GetCostBook[$key]:0;
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key),1)->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$bmunit = 0;
			$bm = 0;
			
			$SUM_PRICE += $PRICE * $value['qty_good'];
/*
			$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$key))->result();
			$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
			$SUM_PRICE += $PRICE * $value['qty_good'];
*/
           
		//print_r($PRICE);
		//print_r($get_price_book);
		//exit;
		
			
			$get_coa = $CI->db->order_by('id','desc')->get_where('request_accessories',array('kode'=>$no_SO))->result();	
            $no_so  ="1110-01-19";			
			$coa_gudangtujuan =(!empty($get_coa[0]->sub_gudang))?$get_coa[0]->sub_gudang:$no_so;
			
				
			$ArrDetail[$key]['kode_trans'] = $kode_trans;
			$ArrDetail[$key]['id_material'] = $key;
			$ArrDetail[$key]['price_book'] = $PRICE+$bmunit;
			$ArrDetail[$key]['berat'] = $value['qty_good'];
			$ArrDetail[$key]['amount'] = $PRICE * $value['qty_good']+ $bm;
			$ArrDetail[$key]['updated_by'] = $UserName;
			$ArrDetail[$key]['updated_date'] = $DateTime;

			$ArrDetailNew[$key]['kode_trans'] = $kode_trans;
			$ArrDetailNew[$key]['category'] = $category;
			$ArrDetailNew[$key]['gudang_dari'] = $GudangFrom;
			$ArrDetailNew[$key]['gudang_ke'] = $GudangTo;
			$ArrDetailNew[$key]['tanggal'] = date('Y-m-d',strtotime($DATE_JURNAL));
			$ArrDetailNew[$key]['id_material'] = $key;
			$ArrDetailNew[$key]['nm_material'] = get_name('raw_materials','nm_material','id_material',$key);
			$ArrDetailNew[$key]['cost_book'] = $PRICE+$bmunit;
			$ArrDetailNew[$key]['qty'] = $value['qty_good'];
			$ArrDetailNew[$key]['total_nilai'] = $PRICE * $value['qty_good'] + $bm;
			$ArrDetailNew[$key]['created_by'] = $UserName;
			$ArrDetailNew[$key]['created_date'] = $DateTime;
			// agus 230308 auto approval
			$ArrDetailNew[$key]['approval_by'] = $UserName;
			$ArrDetailNew[$key]['approval_date'] = $DateTime;
			$ArrDetailNew[$key]['status_jurnal'] = '1';
			$ArrDetailNew[$key]['status_id'] = '1';
			#AMBIL TEMPLATE JURNAL DAN SIMPAN KE TEMPORARY
			$nama_mt        = get_name('raw_materials','nm_material','id_material',$key);
			$Keterangan_INV	= $category.','.$key.','.$nama_mt.','.$value['qty_good'].'x'.$PRICE;
			if($ada_jurnal=='ok'){
				$datajurnal  	 = $CI->db->query("select a.* from ".DBACC.".master_oto_jurnal_detail a where kode_master_jurnal='".$kodejurnal."' order by a.posisi,a.parameter_no")->result();
				foreach($datajurnal AS $record){
					$tabel  = $record->menu;
					$posisi = $record->posisi;
					$field  = $record->field;
					$nokir  = $record->no_perkiraan;					
					if($nokir=='1103-02-09'){
					$nokir  = $coa_gudangtujuan;	
					}
										
					$no_voucher = $kode_trans;
					$nilaibayar = ($PRICE * $value['qty_good']) + $bm;
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						'nomor'         => $kode_trans,
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => $nilaibayar,
						'kredit'        => 0,
						'jenis_jurnal'  => $jenis_jurnal,
						'stspos'		  => '1',
						'no_request'    => $no_request
						);
						$datadetail[] = array(
							'tipe'        => 'JV',
							'nomor'       => $Nomor_JV,
							'tanggal'     => $tgl_voucher,
							'no_perkiraan'	=> $nokir,
							'keterangan'	=> $Keterangan_INV,
							'no_reff'		=> $no_request,
							'debet'			=> $nilaibayar,
							'kredit'		=> 0
						);

					} elseif ($posisi=='K'){
						$det_Jurnaltes[]  = array(
						'nomor'         => $kode_trans,
						'tanggal'       => $tgl_voucher,
						'tipe'          => 'JV',
						'no_perkiraan'  => $nokir,
						'keterangan'    => $Keterangan_INV,
						'no_reff'       => $no_request,
						'debet'         => 0,
						'kredit'        => $nilaibayar,
						'jenis_jurnal'  => $jenis_jurnal,
						'stspos'		  => '1',
						'no_request'    => $no_request
						);
						$datadetail[] = array(
							'tipe'        => 'JV',
							'nomor'       => $Nomor_JV,
							'tanggal'     => $tgl_voucher,
							'no_perkiraan'	=> $nokir,
							'keterangan'	=> $Keterangan_INV,
							'no_reff'		=> $no_request,
							'debet'			=> 0,
							'kredit'		=> $nilaibayar
						);
					}
				}
			}
			// end agus

		}

		//DEBET
		$ArrJurnal[0]['category'] = $category;
		$ArrJurnal[0]['posisi'] = 'DEBIT';
		$ArrJurnal[0]['amount'] = $SUM_PRICE;
		$ArrJurnal[0]['gudang'] = $GudangTo;
		$ArrJurnal[0]['keterangan'] = $ket_plus;
		$ArrJurnal[0]['kode_trans'] = $kode_trans;
		$ArrJurnal[0]['updated_by'] = $UserName;
		$ArrJurnal[0]['updated_date'] = $DateTime;

		//KREDIT
		$ArrJurnal[1]['category'] = $category;
		$ArrJurnal[1]['posisi'] = 'KREDIT';
		$ArrJurnal[1]['amount'] = $SUM_PRICE;
		$ArrJurnal[1]['gudang'] = $GudangFrom;
		$ArrJurnal[1]['keterangan'] = $ket_min;
		$ArrJurnal[1]['kode_trans'] = $kode_trans;
		$ArrJurnal[1]['updated_by'] = $UserName;
		$ArrJurnal[1]['updated_date'] = $DateTime;

		$CI->db->insert_batch('jurnal_temp',$ArrJurnal);
		$CI->db->insert_batch('jurnal_temp_detail',$ArrDetail);
		$CI->db->insert_batch('jurnal',$ArrDetailNew);

		// print_r($PRICE);
		// print_r($det_Jurnaltes);
		// exit;
		// agus 230308 auto approval
		if($ada_jurnal=='ok'){
			if(!empty($det_Jurnaltes)){
				$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			}
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $SUM_PRICE+$bm, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $category, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
			if(!empty($det_Jurnaltes)){
				$CI->db->insert_batch(DBACC.'.jurnal',$datadetail);
			}
		}
		// end agus	

		//JURNAL SPOOL 14 MARET 2024
		function jurnal_spool_manual(){
            $CI 	=& get_instance();
			$data_session	= $CI->session->userdata;
			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			
			$dataspool = $CI->db->query("select a.* from dataspool_jurnal")->result();
			foreach($dataspool AS $record){
                $kd_trans = $record->kd_trans;
				$datatemp = $CI->db->query("select a.* from jurnal_temp WHERE kd_trans = $kd_trans AND updated_date LIKE '2024%' AND category LIKE '%spool%'")->result();				
				$nilai=0;
				$total=0;
				foreach($datatemp AS $datasp){	
						$tgl_spool =$datasp->updated_date;
						$posisi =$datasp->posisi;
						$keterangan    =$datasp->keterangan;
						$category      =$datasp->category;

						$tgl_voucher = substr($tgl_spool,0,10);
						$CI->load->model('jurnal_model');
						$Nomor_JV = $CI->jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
						$Bln	  = substr($tgl_voucher,5,2);
						$Thn	  = substr($tgl_voucher,0,4);

						if($keterangan=='wip'){
							$nokir ='';
						}elseif($keterangan=='finish good'){
                            $nokir ='';
						}
                        $nilai = round($datasp->amount);
						$total += $nilai;
						if ($posisi=='DEBET'){
							$det_Jurnaltes[] = array(
							'nomor'         => $Nomor_JV,
							'tanggal'       => $tgl_voucher,
							'tipe'          => 'JV',
							'no_perkiraan'  => $nokir,
							'keterangan'    => $category.' '.$kd_trans,
							'no_reff'       => $kd_trans,
							'debet'         => $nilai,
							'kredit'        => 0,
							);
						}
						if ($posisi=='KREDIT'){
							$det_Jurnaltes[] = array(
							'nomor'         => $Nomor_JV,
							'tanggal'       => $tgl_voucher,
							'tipe'          => 'JV',
							'no_perkiraan'  => $nokir,
							'keterangan'    => $category.' '.$kd_trans,
							'no_reff'       => $kd_trans,
							'debet'         => 0,
							'kredit'        => $nilai,
							);
						}

				}

				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $total, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $category, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
				$CI->db->insert(DBACC.'.javh',$dataJVhead);
				if(!empty($det_Jurnaltes)){
					$CI->db->insert_batch(DBACC.'.jurnal',$det_Jurnaltes);
				}

			}
			 
		}

        public function jurnal_incustomer_manual(){
			$CI 	=& get_instance();
		    $ket ='TRANSIT - CUSTOMER';
			$kodejurnal ='JV007';
            
			$ArrDetailProduct = $CI->db->query("SELECT a.* FROM jurnal_product_manual a ")->result();

			foreach($ArrDetailProduct as $keys => $values) {
			  $id=$values['id_detail'];		
			  
			  $datajurnal = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product_manual a join product_parent b on a.product=b.product_parent WHERE a.category='diterima customer' and a.status_jurnal='0' and a.id_detail ='$id' limit 1" )->row();
			  $id=(!empty($datajurnal->id))?$datajurnal->id:0;
			  $tgl_voucher = (!empty($datajurnal->tanggal))?$datajurnal->tanggal:'2024-01-31';
			  $no_request = $id;
  
			  $id_detail=(!empty($datajurnal->id_detail))?$datajurnal->id_detail:0;
			  $id_milik=(!empty($datajurnal->id_milik))?$datajurnal->id_milik:0;
			  $total_nilai=(!empty($datajurnal->total_nilai))?$datajurnal->total_nilai:0;
			  $id_spk=(!empty($datajurnal->id_spk))?$datajurnal->id_spk:0;
			  $no_so=(!empty($datajurnal->no_so))?$datajurnal->no_so:0;
			  $product=(!empty($datajurnal->product))?$datajurnal->product:0;
			  $no_surat_jalan=(!empty($datajurnal->no_surat_jalan))?$datajurnal->no_surat_jalan:0;
  
			  $dataproductiondetail=$CI->db->query("select * from production_detail where id='".$id_detail."' and id_milik ='".$id_milik."' limit 1")->row();
			  
			  
			  if(!empty($dataproductiondetail->finish_good)){
				  if($dataproductiondetail->finish_good==0){
				  $datasodetailheader = $CI->db->query("SELECT * FROM laporan_per_hari_action WHERE id_milik ='".$datajurnal->id_milik."' limit 1" )->row();
			  
				  
				  $kurs=1;
				  $sqlkurs="select * from ms_kurs where tanggal <='".$datajurnal->tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
				  $dtkurs	= $CI->db->query($sqlkurs)->row();
				  if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
				  $wip_material=$datajurnal->total_nilai;
				  $pe_direct_labour=(($datasodetailheader->direct_labour*$datasodetailheader->man_hours)*$kurs);
				  $pe_indirect_labour=(($datasodetailheader->indirect_labour*$datasodetailheader->man_hours)*$kurs);
				  $foh=(($datasodetailheader->machine + $datasodetailheader->mould_mandrill + $datasodetailheader->foh_depresiasi + $datasodetailheader->biaya_rutin_bulanan + $datasodetailheader->foh_consumable)*$kurs);
				  $pe_consumable=($datasodetailheader->consumable*$kurs);
				  $finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
  
				  $CI->db->query("update production_detail set wip_kurs='".$kurs."', wip_material='".$wip_material."' , wip_dl='".$pe_direct_labour."' , wip_foh='".$foh."', wip_il='".$pe_indirect_labour."', wip_consumable='".$pe_consumable."', finish_good='".$finish_good."' WHERE id='".$datajurnal->id_detail."' and id_milik ='".$datajurnal->id_milik."' limit 1" );
			  
				  $totalall=$finish_good;
				  }
				  else{
				  $totalall= (!empty($dataproductiondetail->finish_good))?$dataproductiondetail->finish_good:0;
				  }
				  
			  }
			  
			  // print_r($totalall);
			  // exit;
			  
			  $no_spk=$id_spk;
			  $Keterangan_INV=($ket).' ('.$no_so.' - '.$product.' - '.$no_spk.' - '.$no_surat_jalan.')';
			  $datajurnal  	 = $CI->Acc_model->GetTemplateJurnal($kodejurnal);
			  $det_Jurnaltes = [];
			  if(!empty($datajurnal)){
				  foreach($datajurnal AS $record){
					  $tabel  = $record->menu;
					  $posisi = $record->posisi;
					  $field  = $record->field;
					  $nokir  = $record->no_perkiraan;
					  $totalall2 = (!empty($totalall))?$totalall:0;
					  $param  = 'id';
					  if ($posisi=='D'){
						  $value_param  = $id;
						  $val = $CI->Acc_model->GetData($tabel,$field,$param,$value_param);
						  $nilaibayar = (!empty($val[0]->$field))?$val[0]->$field:0;
						  $det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $Keterangan_INV,
						  'no_reff'       => $no_request,
						  'debet'         => $totalall2,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'intransit incustomer',
						  'no_request'    => $no_request,
						  'stspos'		=>1
						  );
					  } elseif ($posisi=='K'){
						  $det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $Keterangan_INV,
						  'no_reff'       => $no_request,
						  'debet'         => 0,
						  'kredit'        => $totalall2,
						  'jenis_jurnal'  => 'intransit incustomer',
						  'no_request'    => $no_request,
						  'stspos'		=>1
						  );
					  }
				  }
			  }
			  $CI->db->query("delete from jurnaltras_manual WHERE jenis_jurnal='diterima customer' and no_reff ='$id'");
			  $CI->db->insert_batch('jurnaltras_manual',$det_Jurnaltes);
			  $Nomor_JV = $CI->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			  $Bln	= substr($tgl_voucher,5,2);
			  $Thn	= substr($tgl_voucher,0,4);
			  $dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall2, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.'-'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			  $CI->db->insert(DBACC.'.javh',$dataJVhead);
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
				  $CI->db->insert(DBACC.'.jurnal',$datadetail);
			  }
			  $CI->db->query("UPDATE jurnal_product_manual SET status_jurnal='1',approval_by='".$UserName."',approval_date='".$DateTime."' WHERE id ='$id'");
			  unset($det_Jurnaltes);unset($datadetail);
			}
		  

		}


	}
	     
	
	