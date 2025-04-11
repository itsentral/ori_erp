<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_material extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('jurnal_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
	public function index(){
		$controller			= (strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$pusat		= $this->db->query("SELECT * FROM warehouse WHERE category='pusat' ORDER BY urut ASC")->result_array();
		$no_po		= $this->db->query("SELECT no_po, status1, 'PO' as ket_,nm_supplier FROM tran_material_po_header WHERE (total_bayar_rupiah=0 and nilai_dp=0) ORDER BY no_po ASC")->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'retur material'))->result_array();

		$data = array(
			'title'			=> 'Retur Material',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_po'		=> $list_po,
			'pusat'			=> $pusat,
			'no_po'			=> $no_po
		);
		history('View Retur Material');
		$this->load->view('Retur_material/index_retur_material.php',$data);
	}

	public function server_side_retur_material(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_retur_material(
			$requestData['no_po'],
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

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."/".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='left'>".$row['note']."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$print	= "&nbsp;<a href='".base_url('retur_material/print_retur_material/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print'><i class='fa fa-print'></i></a>";
			$nestedData[]	= "<div align='left'>
								<button type='button' class='btn btn-sm btn-primary detailAjust hidden' title='Detail' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-eye'></i></button>
								".$print."
								</div>";
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
	public function query_data_json_retur_material($no_po,$like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_no_po ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}
		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.kd_gudang_ke = '".$gudang."' ";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*, b.nm_supplier
			FROM
				warehouse_adjustment a
				LEFT JOIN tran_material_po_header b ON a.no_ipp=b.no_po,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.category = 'retur material'
				".$where_no_po."
				".$where_gudang."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);
		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}	
	public function modal_retur_material(){
		$data	= $this->input->post();
		$no_po 	= $data['no_ipp'];
		$gudang_before = $data['gudang_before'];
		$result	= $this->db->query("SELECT * FROM tran_material_po_detail WHERE no_po='".$no_po."' ORDER BY id")->result_array();
		$data = array(
			'title'			=> 'Retur Material',
			'action'		=> 'index',
			'no_po'			=> $no_po,
			'result'		=> $result,
			'gudang'		=> $gudang_before
		);
		$this->load->view('Retur_material/form_retur_material.php',$data);
	}
	public function process_retur_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$tipe_out		= 'RETUR';
		$gudang			= $data['gudang'];
		$tujuan_out		= 'OUT';
		$kd_gudang_dari	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addOutMat		= $data['addOutMat'];
		$Ym 			= date('ym');
		$no_po			= $data['no_po'];
		$mata_uang		= $data['mata_uang'];
		$id_supplier	= $data['id_supplier'];
		$nm_supplier	= $data['nm_supplier'];
		$kurs			= $data['kurs'];

        $histHlp = "Material retur: ".$kd_gudang_dari." / ".$tipe_out;

			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;

			$ArrUpdate		 = array();
			$ArrDeatilAdj	 = array();
			$ArrStock = array();
			$ArrHist = array();
			$ArrStockInsert = array();
			$ArrHistInsert = array();
			$SumMat = 0;
			$ArrDetailNew = [];
			$datadetail=[];
			$totalRetur = 0;
			$totalReturDollar = 0;
			$theprice=0;
			$thepriceDollar=0;

			foreach($addOutMat AS $val => $valx){
				$qtyOUT	= str_replace(',','',$valx['qty_out']);
				$SumMat	+= $qtyOUT;

				$restWhDetail	= $this->db->get_where('raw_materials',array('id_material'=>$valx['id_material']))->result();

				//detail adjustment
				$ArrDeatilAdj[$val]['no_ipp'] 			= $no_po;
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
				$ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material'];
				$ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
				$ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
				$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
				$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
				$ArrDeatilAdj[$val]['qty_order'] 		= $valx['qty_order'];
				$ArrDeatilAdj[$val]['qty_oke'] 			= $qtyOUT;
				$ArrDeatilAdj[$val]['key_gudang'] 		= NULL;
				$ArrDeatilAdj[$val]['expired_date'] 	= NULL;
				$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
				$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrDeatilAdj[$val]['update_date'] 		= $dateTime;
				$ArrDeatilAdj[$val]['harga'] 			= $valx['harga']*$kurs;

				//detail material po				
				$ArrUpdate[$val]['id']		= $valx['id'];
				$ArrUpdate[$val]['qty_in']	= $valx['qty_in'] - $qtyOUT;
				
// --------------------------------------
				//PENGURANGAN GUDANG
				$id_gudang_dari=$gudang;
				$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$valx['id_material']))->result();

				$theprice		= $valx['harga']*$kurs;
				$thepriceDollar	= $valx['harga'];
				if(!empty($rest_pusat)){
					$ArrStock[$val]['id'] 			= $rest_pusat[0]->id;
					$ArrStock[$val]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $qtyOUT;
					$ArrStock[$val]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStock[$val]['update_date'] 	= $dateTime;

					$ArrHist[$val]['id_material'] 	= $valx['id_material'];
					$ArrHist[$val]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
					$ArrHist[$val]['nm_material'] 	= $rest_pusat[0]->nm_material;
					$ArrHist[$val]['id_category'] 	= $rest_pusat[0]->id_category;
					$ArrHist[$val]['nm_category'] 	= $rest_pusat[0]->nm_category;
					$ArrHist[$val]['id_gudang'] 		= $id_gudang_dari;
					$ArrHist[$val]['kd_gudang'] 		= $kd_gudang_dari;
					$ArrHist[$val]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrHist[$val]['kd_gudang_dari'] 	= $kd_gudang_dari;
					$ArrHist[$val]['id_gudang_ke'] 		= NULL;
					$ArrHist[$val]['kd_gudang_ke'] 		= 'OUT';
					$ArrHist[$val]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
					$ArrHist[$val]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $qtyOUT;
					$ArrHist[$val]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
					$ArrHist[$val]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
					$ArrHist[$val]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist[$val]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist[$val]['no_ipp'] 			= $kode_trans;
					$ArrHist[$val]['jumlah_mat'] 		= $qtyOUT;
					$ArrHist[$val]['ket'] 				= 'retur po';
					$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHist[$val]['update_date'] 		= $dateTime;
					//update agus
					$ArrHist[$val]['harga'] 			= $theprice;
					//ambil saldo akhir
					$saldoakhir=0;
					$saldo_akhir_gudang = $this->db->order_by('id', 'desc')->get_where('warehouse_history',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$valx['id_material']),1)->row();
					if(!empty($saldo_akhir_gudang)) $saldoakhir=$saldo_akhir_gudang->saldo_akhir;
					$ArrHist[$val]['saldo_awal']		= $saldoakhir;
					$ArrHist[$val]['saldo_akhir']		= ($saldoakhir-($theprice*$qtyOUT));
				}
				else{
					$restMat	= $this->db->get_where('raw_materials',array('id_material'=>$valx['id_material']))->result();

					$ArrStockInsert[$val]['id_material'] 	= $restMat[0]->id_material;
					$ArrStockInsert[$val]['idmaterial'] 	= $restMat[0]->idmaterial;
					$ArrStockInsert[$val]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrStockInsert[$val]['id_category'] 	= $restMat[0]->id_category;
					$ArrStockInsert[$val]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrStockInsert[$val]['id_gudang'] 		= $id_gudang_dari;
					$ArrStockInsert[$val]['kd_gudang'] 		= $kd_gudang_dari;
					$ArrStockInsert[$val]['qty_stock'] 		= 0 - $qtyOUT;
					$ArrStockInsert[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockInsert[$val]['update_date'] 	= $dateTime;

					$ArrHistInsert[$val]['id_material'] 	= $valx['id_material'];
					$ArrHistInsert[$val]['idmaterial'] 		= $restMat[0]->idmaterial;
					$ArrHistInsert[$val]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrHistInsert[$val]['id_category'] 	= $restMat[0]->id_category;
					$ArrHistInsert[$val]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrHistInsert[$val]['id_gudang'] 		= $id_gudang_dari;
					$ArrHistInsert[$val]['kd_gudang'] 		= $kd_gudang_dari;
					$ArrHistInsert[$val]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrHistInsert[$val]['kd_gudang_dari'] 	= $kd_gudang_dari;
					$ArrHistInsert[$val]['id_gudang_ke'] 	= NULL;
					$ArrHistInsert[$val]['kd_gudang_ke'] 	= 'OUT';
					$ArrHistInsert[$val]['qty_stock_awal'] 	    = 0;
					$ArrHistInsert[$val]['qty_stock_akhir']     = 0 - $qtyOUT;
					$ArrHistInsert[$val]['qty_booking_awal']    = 0;
					$ArrHistInsert[$val]['qty_booking_akhir']   = 0;
					$ArrHistInsert[$val]['qty_rusak_awal'] 	    = 0;
					$ArrHistInsert[$val]['qty_rusak_akhir'] 	= 0;
					$ArrHistInsert[$val]['no_ipp'] 			= $kode_trans;
					$ArrHistInsert[$val]['jumlah_mat'] 		= $qtyOUT;
					$ArrHistInsert[$val]['ket'] 			= 'retur po (insert new)';
					$ArrHistInsert[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHistInsert[$val]['update_date'] 	= $dateTime;
					//update agus
					$ArrHistInsert[$val]['harga'] 			= $theprice;
					$ArrHistInsert[$val]['saldo_awal']		= 0;
					$ArrHistInsert[$val]['saldo_akhir']		= 0 - ($theprice*$qtyOUT);
				}
				$totalRetur			= ($totalRetur+($qtyOUT * $theprice));
				$totalReturDollar	= ($totalReturDollar+($qtyOUT * $thepriceDollar));
// jurnal
				$ArrDetailNew[$val]['kode_trans'] 	= $kode_trans;
				$ArrDetailNew[$val]['category']		= 'retur po';
				$ArrDetailNew[$val]['gudang_dari'] 	= $id_gudang_dari;
				$ArrDetailNew[$val]['gudang_ke']	= 'OUT';
				$ArrDetailNew[$val]['tanggal'] 		= date('Y-m-d');
				$ArrDetailNew[$val]['no_ipp']		= $no_po;
				$ArrDetailNew[$val]['no_so']		= '';
				$ArrDetailNew[$val]['product'] 		= '';
				$ArrDetailNew[$val]['id_detail']	= '';
				$ArrDetailNew[$val]['id_milik']		= '';
				$ArrDetailNew[$val]['id_spk'] 		= '';
				$ArrDetailNew[$val]['spk'] 			= '';
				$ArrDetailNew[$val]['id_material'] 	= $restWhDetail[0]->id_material;
				$ArrDetailNew[$val]['nm_material'] 	= $restWhDetail[0]->nm_material;
				$ArrDetailNew[$val]['spec']			= '';
				$ArrDetailNew[$val]['cost_book']	= $theprice;
				$ArrDetailNew[$val]['qty'] 			= $qtyOUT;
				$ArrDetailNew[$val]['total_nilai'] 	= $qtyOUT * $theprice;
				$ArrDetailNew[$val]['created_by'] 	= $data_session['ORI_User']['username'];
				$ArrDetailNew[$val]['created_date']	= $dateTime;
			}
			
			// jurnal erp
			$tgl_voucher=date('Y-m-d');
			$kodejurnal='JV072';
			$Keterangan_INV='Retur material '.$no_po;
			$jenis_jurnal='retur po';
			$Nomor_JV = $this->jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	  = substr($tgl_voucher,5,2);
			$Thn	  = substr($tgl_voucher,0,4);
			$unbill_coa='';
			$nilaibayar=0;

			$datajurnal  	 = $this->db->query("select a.* from ".DBACC.".master_oto_jurnal_detail a where kode_master_jurnal='".$kodejurnal."' order by a.posisi,a.parameter_no")->result();
			foreach($datajurnal AS $record){
				$tabel  = $record->menu;
				$posisi = $record->posisi;
				$field  = $record->field;
				$nokir  = $record->no_perkiraan;
				$no_voucher = $kode_trans;
				$nilaibayar = $totalRetur;
				if ($posisi=='D'){
					$unbill_coa = $nokir;
					$det_Jurnaltes[]  = array(
					'nomor'         => $kode_trans,
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir,
					'keterangan'    => $Keterangan_INV,
					'no_reff'       => $no_po,
					'debet'         => $nilaibayar,
					'kredit'        => 0,
					'jenis_jurnal'  => $jenis_jurnal,
					'stspos'		  => '1',
					'no_request'    => $no_po
					);
					$datadetail[] = array(
						'tipe'        => 'JV',
						'nomor'       => $Nomor_JV,
						'tanggal'     => $tgl_voucher,
						'no_perkiraan'	=> $nokir,
						'keterangan'	=> $Keterangan_INV,
						'no_reff'		=> $no_po,
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
					'no_reff'       => $no_po,
					'debet'         => 0,
					'kredit'        => $nilaibayar,
					'jenis_jurnal'  => $jenis_jurnal,
					'stspos'		  => '1',
					'no_request'    => $no_po
					);
					$datadetail[] = array(
						'tipe'        => 'JV',
						'nomor'       => $Nomor_JV,
						'tanggal'     => $tgl_voucher,
						'no_perkiraan'	=> $nokir,
						'keterangan'	=> $Keterangan_INV,
						'no_reff'		=> $no_po,
						'debet'			=> 0,
						'kredit'		=> $nilaibayar
					);
				}
			}

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $no_po,
				'note' 			    => $data['note'],
				'category' 			=> 'retur material',
				'jumlah_mat' 		=> $SumMat,
				'kurs' 				=> $kurs,
				'id_gudang_dari' 	=> $gudang,
				'tanggal'			=> $tgl_voucher,
				'kd_gudang_dari' 	=> $kd_gudang_dari,
				'kd_gudang_ke' 		=> 'OUT',
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime
			);
			
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl_voucher,
				'no_perkiraan'   => $unbill_coa,
				'keterangan'     => $Keterangan_INV,
				'no_reff'     	 => $no_po,
				'kredit'      	 => 0,
				'debet'          => $nilaibayar,
				'id_supplier'    => $id_supplier,
				'nama_supplier'  => $nm_supplier,
				'no_request'     => $kode_trans,
			);			

			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				if(!empty($ArrStock)){
					$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
				}
				if(!empty($ArrHist)){
					$this->db->insert_batch('warehouse_history', $ArrHist);
				}

				if(!empty($ArrStockInsert)){
					$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
				}
				if(!empty($ArrHistInsert)){
					$this->db->insert_batch('warehouse_history', $ArrHistInsert);
				}
				if(!empty($ArrUpdate)){
					$this->db->query("update tran_material_po_header set retur=1 where no_po='".$no_po."'");
					$this->db->update_batch('tran_material_po_detail', $ArrUpdate, 'id');
					$this->db->insert('tr_kartu_hutang',$datahutang);
				}
				//jurnal
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $nilaibayar, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $data_session['ORI_User']['username'], 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				if(!empty($det_Jurnaltes)){
					$this->db->insert_batch(DBACC.'.jurnal',$datadetail);
					$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
				}
			$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}
	public function print_retur_material(){
		$kode_trans     = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans
		);

		history('Print Retur Material '.$kode_trans);
		$this->load->view('Print/print_retur_material', $data);
	}
//-----------------STOK
	public function stok(){
		$controller			= (strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$pusat		= $this->db->query("SELECT * FROM warehouse WHERE category in ('indirect','household') ORDER BY urut ASC")->result_array();
		$no_po		= $this->db->query("SELECT no_po, status1, 'PO' as ket_,nm_supplier FROM tran_po_header WHERE (total_bayar_rupiah=0 and nilai_dp=0) ORDER BY no_po ASC")->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'retur material'))->result_array();

		$data = array(
			'title'			=> 'Retur Stok',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_po'		=> $list_po,
			'pusat'			=> $pusat,
			'no_po'			=> $no_po
		);
		history('View Retur Material');
		$this->load->view('Retur_material/index_retur_stok.php',$data);
	}

	public function server_side_retur_stok(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_retur_stok(
			$requestData['no_po'],
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

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."/".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_supplier']."</div>";
			$nestedData[]	= "<div align='left'>".$row['note']."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$print	= "&nbsp;<a href='".base_url('print/print_retur_stok/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print'><i class='fa fa-print'></i></a>";
			$nestedData[]	= "<div align='left'>
								<button type='button' class='btn btn-sm btn-primary detailAjust hidden' title='Detail' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-eye'></i></button>
								".$print."
								</div>";
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
	public function query_data_json_retur_stok($no_po,$like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_no_po ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}
		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.kd_gudang_ke = '".$gudang."' ";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*, b.nm_supplier
			FROM
				warehouse_adjustment a
				LEFT JOIN tran_material_po_header b ON a.no_ipp=b.no_po,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.category = 'retur stok'
				".$where_no_po."
				".$where_gudang."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);
		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}	
	public function modal_retur_stok(){
		$data	= $this->input->post();
		$no_po 	= $data['no_ipp'];
		$gudang_before = $data['gudang_before'];
		$result	= $this->db->query("SELECT a.*,b.category_awal FROM tran_po_detail a LEFT JOIN con_nonmat_new b ON a.id_barang = b.code_group WHERE a.no_po='".$no_po."' ORDER BY a.id")->result_array();
		$data = array(
			'title'			=> 'Retur Material',
			'action'		=> 'index',
			'no_po'			=> $no_po,
			'result'		=> $result,
			'gudang'		=> $gudang_before
		);
		$this->load->view('Retur_material/form_retur_stok.php',$data);
	}
	public function process_retur_stok(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$tipe_out		= 'RETUR';
		$gudang			= $data['gudang'];
		$tujuan_out		= 'OUT';
		$kd_gudang_dari	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addOutMat		= $data['addOutMat'];
		$Ym 			= date('ym');
		$no_po			= $data['no_po'];
		$mata_uang		= $data['mata_uang'];
		$id_supplier	= $data['id_supplier'];
		$nm_supplier	= $data['nm_supplier'];
		$kurs			= $data['kurs'];

        $histHlp = "Stok retur: ".$kd_gudang_dari." / ".$tipe_out;

			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;

			$ArrUpdate		 = array();
			$ArrDeatilAdj	 = array();
			$ArrStock = array();
			$ArrHist = array();
			$ArrStockInsert = array();
			$ArrHistInsert = array();
			$SumMat = 0;
			$ArrDetailNew = [];
			$datadetail=[];
			$totalRetur = 0;
			$totalReturDollar = 0;
			$theprice=0;
			$thepriceDollar=0;

			foreach($addOutMat AS $val => $valx){
				$qtyOUT	= str_replace(',','',$valx['qty_out']);
				$SumMat	+= $qtyOUT;

				$restWhDetail	= $this->db->get_where('con_nonmat_new',array('code_group'=>$valx['id_material']))->result();

				//detail adjustment
				$ArrDeatilAdj[$val]['no_ipp'] 			= $no_po;
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
				$ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material'];
				$ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->code_group;
				$ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->material_name;
				$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->category_awal;
				$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->category_code;
				$ArrDeatilAdj[$val]['qty_order'] 		= $valx['qty_order'];
				$ArrDeatilAdj[$val]['qty_oke'] 			= $qtyOUT;
				$ArrDeatilAdj[$val]['key_gudang'] 		= NULL;
				$ArrDeatilAdj[$val]['expired_date'] 	= NULL;
				$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
				$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrDeatilAdj[$val]['update_date'] 		= $dateTime;
				$ArrDeatilAdj[$val]['harga'] 			= $valx['harga']*$kurs;

				//detail material po				
				$ArrUpdate[$val]['id']		= $valx['id'];
				$ArrUpdate[$val]['qty_in']	= $valx['qty_in'] - $qtyOUT;
				
// --------------------------------------
				//PENGURANGAN STOK
				$id_gudang_dari=$gudang;
				$rest_pusat = $this->db->get_where('warehouse_rutin_stock',array('id_gudang'=>$id_gudang_dari, 'code_group'=>$valx['id_material']))->result();

				$theprice		= $valx['harga']*$kurs;
				$thepriceDollar	= $valx['harga'];
				if(!empty($rest_pusat)){
					$ArrStock[$val]['id'] 			= $rest_pusat[0]->id;
					$ArrStock[$val]['stock'] 		= $rest_pusat[0]->stock - $qtyOUT;
					$ArrStock[$val]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStock[$val]['update_date'] 	= $dateTime;

					$ArrHist[$val]['code_group'] 		= $valx['id_material'];
					$ArrHist[$val]['category_awal'] 	= $rest_pusat[0]->category_awal;
					$ArrHist[$val]['category_code'] 	= $rest_pusat[0]->category_code;
					$ArrHist[$val]['material_name'] 	= $rest_pusat[0]->material_name;
					$ArrHist[$val]['id_gudang'] 		= $id_gudang_dari;
					$ArrHist[$val]['gudang'] 			= $kd_gudang_dari;
					$ArrHist[$val]['gudang_dari'] 		= $id_gudang_dari;
					$ArrHist[$val]['id_gudang_ke'] 		= NULL;
					$ArrHist[$val]['gudang_ke'] 		= 'OUT';
					$ArrHist[$val]['qty_stock_awal'] 	= $rest_pusat[0]->stock;
					$ArrHist[$val]['qty_stock_akhir'] 	= $rest_pusat[0]->stock - $qtyOUT;
					$ArrHist[$val]['qty_rusak_awal'] 	= $rest_pusat[0]->rusak;
					$ArrHist[$val]['qty_rusak_akhir'] 	= $rest_pusat[0]->rusak;
					$ArrHist[$val]['no_trans'] 			= $no_po."/".$kode_trans;
					$ArrHist[$val]['jumlah_qty'] 		= $qtyOUT;
					$ArrHist[$val]['ket'] 				= 'retur po';
					$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHist[$val]['update_date'] 		= date('Y-m-d H:i:s');

					//update agus
					$ArrHist[$val]['harga'] 			= $theprice;
					//ambil saldo akhir
					$saldoakhir=0;
					$saldo_akhir_gudang = $this->db->order_by('id', 'desc')->get_where('warehouse_rutin_history',array('id_gudang'=>$id_gudang_dari, 'code_group'=>$valx['id_material']),1)->row();
					if(!empty($saldo_akhir_gudang)) $saldoakhir=$saldo_akhir_gudang->saldo_akhir;
					$ArrHist[$val]['saldo_awal']		= $saldoakhir;
					$ArrHist[$val]['saldo_akhir']		= ($saldoakhir-($theprice*$qtyOUT));
				}
				else{
					$restMat	= $this->db->get_where('con_nonmat_new',array('code_group'=>$valx['id_material']))->result();

					$ArrStockInsert[$val]['code_group']	= $restMat[0]->code_group;
					$ArrStockInsert[$val]['category_awal']	= $restMat[0]->category_awal;
					$ArrStockInsert[$val]['category_code']	= $restMat[0]->category_code;
					$ArrStockInsert[$val]['material_name']	= $restMat[0]->material_name;
					$ArrStockInsert[$val]['gudang'] 		= $id_gudang_dari;
					$ArrStockInsert[$val]['stock'] 		= 0 - $qtyOUT;
					$ArrStockInsert[$val]['rusak'] 		= 0;
					$ArrStockInsert[$val]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStockInsert[$val]['update_date']	= date('Y-m-d H:i:s');


					$ArrHistInsert[$val]['code_group']		= $restMat[0]->code_group;
					$ArrHistInsert[$val]['category_awal'] 	= $restMat[0]->category_awal;
					$ArrHistInsert[$val]['category_code'] 	= $restMat[0]->category_code;
					$ArrHistInsert[$val]['material_name'] 	= $restMat[0]->material_name;
					$ArrHistInsert[$val]['id_gudang'] 		= $id_gudang_dari;
					$ArrHistInsert[$val]['gudang']			= $kd_gudang_dari;
					$ArrHistInsert[$val]['gudang_dari']	= $id_gudang_dari;
					$ArrHistInsert[$val]['id_gudang_ke']	= NULL;
					$ArrHistInsert[$val]['gudang_ke'] 		= 'OUT';
					$ArrHistInsert[$val]['qty_stock_awal']	= 0;
					$ArrHistInsert[$val]['qty_stock_akhir']	= 0 - $qtyOUT;
					$ArrHistInsert[$val]['qty_rusak_awal'] 	= 0;
					$ArrHistInsert[$val]['qty_rusak_akhir'] 	= 0;
					$ArrHistInsert[$val]['no_trans'] 			= $no_po."/".$kode_trans;
					$ArrHistInsert[$val]['jumlah_qty'] 		= $qtyOUT;
					$ArrHistInsert[$val]['ket'] 				= 'retur po (insert new)';
					$ArrHistInsert[$val]['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistInsert[$val]['update_date'] 		= date('Y-m-d H:i:s');

					//update agus
					$ArrHistInsert[$val]['harga'] 			= $theprice;
					$ArrHistInsert[$val]['saldo_awal']		= 0;
					$ArrHistInsert[$val]['saldo_akhir']		= 0 - ($theprice*$qtyOUT);
				}
				$totalRetur			= ($totalRetur+($qtyOUT * $theprice));
				$totalReturDollar	= ($totalReturDollar+($qtyOUT * $thepriceDollar));
// jurnal
				$ArrDetailNew[$val]['kode_trans'] 	= $kode_trans;
				$ArrDetailNew[$val]['category']		= 'retur po';
				$ArrDetailNew[$val]['gudang_dari'] 	= $id_gudang_dari;
				$ArrDetailNew[$val]['gudang_ke']	= 'OUT';
				$ArrDetailNew[$val]['tanggal'] 		= date('Y-m-d');
				$ArrDetailNew[$val]['no_ipp']		= $no_po;
				$ArrDetailNew[$val]['no_so']		= '';
				$ArrDetailNew[$val]['product'] 		= '';
				$ArrDetailNew[$val]['id_detail']	= '';
				$ArrDetailNew[$val]['id_milik']		= '';
				$ArrDetailNew[$val]['id_spk'] 		= '';
				$ArrDetailNew[$val]['spk'] 			= '';
				$ArrDetailNew[$val]['id_material'] 	= $restWhDetail[0]->code_group;
				$ArrDetailNew[$val]['nm_material'] 	= $restWhDetail[0]->material_name;
				$ArrDetailNew[$val]['spec']			= '';
				$ArrDetailNew[$val]['cost_book']	= $theprice;
				$ArrDetailNew[$val]['qty'] 			= $qtyOUT;
				$ArrDetailNew[$val]['total_nilai'] 	= $qtyOUT * $theprice;
				$ArrDetailNew[$val]['created_by'] 	= $data_session['ORI_User']['username'];
				$ArrDetailNew[$val]['created_date']	= $dateTime;
			}
			
			// jurnal erp
			$tgl_voucher=date('Y-m-d');
			$kodejurnal='JV073';
			$Keterangan_INV='Retur stok '.$no_po;
			$jenis_jurnal='retur po';
			$Nomor_JV = $this->jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	  = substr($tgl_voucher,5,2);
			$Thn	  = substr($tgl_voucher,0,4);
			$unbill_coa='';
			$nilaibayar=0;

			$datajurnal  	 = $this->db->query("select a.* from ".DBACC.".master_oto_jurnal_detail a where kode_master_jurnal='".$kodejurnal."' order by a.posisi,a.parameter_no")->result();
			foreach($datajurnal AS $record){
				$tabel  = $record->menu;
				$posisi = $record->posisi;
				$field  = $record->field;
				$nokir  = $record->no_perkiraan;
				$no_voucher = $kode_trans;
				$nilaibayar = $totalRetur;
				if ($posisi=='D'){
					$unbill_coa = $nokir;
					$det_Jurnaltes[]  = array(
					'nomor'         => $kode_trans,
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir,
					'keterangan'    => $Keterangan_INV,
					'no_reff'       => $no_po,
					'debet'         => $nilaibayar,
					'kredit'        => 0,
					'jenis_jurnal'  => $jenis_jurnal,
					'stspos'		  => '1',
					'no_request'    => $no_po
					);
					$datadetail[] = array(
						'tipe'        => 'JV',
						'nomor'       => $Nomor_JV,
						'tanggal'     => $tgl_voucher,
						'no_perkiraan'	=> $nokir,
						'keterangan'	=> $Keterangan_INV,
						'no_reff'		=> $no_po,
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
					'no_reff'       => $no_po,
					'debet'         => 0,
					'kredit'        => $nilaibayar,
					'jenis_jurnal'  => $jenis_jurnal,
					'stspos'		  => '1',
					'no_request'    => $no_po
					);
					$datadetail[] = array(
						'tipe'        => 'JV',
						'nomor'       => $Nomor_JV,
						'tanggal'     => $tgl_voucher,
						'no_perkiraan'	=> $nokir,
						'keterangan'	=> $Keterangan_INV,
						'no_reff'		=> $no_po,
						'debet'			=> 0,
						'kredit'		=> $nilaibayar
					);
				}
			}

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $no_po,
				'note' 			    => $data['note'],
				'category' 			=> 'retur stok',
				'jumlah_mat' 		=> $SumMat,
				'kurs' 				=> $kurs,
				'id_gudang_dari' 	=> $gudang,
				'tanggal'			=> $tgl_voucher,
				'kd_gudang_dari' 	=> $kd_gudang_dari,
				'kd_gudang_ke' 		=> 'OUT',
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime
			);
			
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tgl_voucher,
				'no_perkiraan'   => $unbill_coa,
				'keterangan'     => $Keterangan_INV,
				'no_reff'     	 => $no_po,
				'kredit'      	 => 0,
				'debet'          => $nilaibayar,
				'id_supplier'    => $id_supplier,
				'nama_supplier'  => $nm_supplier,
				'no_request'     => $kode_trans,
			);			

			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				if(!empty($ArrStock)){
					$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
				}
				if(!empty($ArrHist)){
					$this->db->insert_batch('warehouse_history', $ArrHist);
				}

				if(!empty($ArrStockInsert)){
					$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
				}
				if(!empty($ArrHistInsert)){
					$this->db->insert_batch('warehouse_history', $ArrHistInsert);
				}
				if(!empty($ArrUpdate)){
					$this->db->query("update tran_po_header set retur=1 where no_po='".$no_po."'");
					$this->db->update_batch('tran_po_detail', $ArrUpdate, 'id');
					$this->db->insert('tr_kartu_hutang',$datahutang);
				}
				//jurnal
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $nilaibayar, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $data_session['ORI_User']['username'], 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				if(!empty($det_Jurnaltes)){
					$this->db->insert_batch(DBACC.'.jurnal',$datadetail);
					$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
				}
			$this->db->trans_complete();


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}
	public function print_retur_stok(){
		$kode_trans     = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans
		);

		history('Print Retur Stok'.$kode_trans);
		$this->load->view('Print/print_retur_stok', $data);
	}

}
