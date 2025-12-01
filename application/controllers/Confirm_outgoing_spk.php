<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Confirm_outgoing_spk extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('material_planning_model');
		$this->load->model('purchase_request_model');
		$this->load->model('purchase_order_model');
		$this->load->model('warehouse_model');
		$this->load->model('adjustment_material_model');
		$this->load->model('Jurnal_model');
		$this->load->model('tanki_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='pusat' ORDER BY urut ASC")->result_array();
		$subgudang			= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();
		
		$data = array(
			'title'			=> "Confirm Outgoing SPK",
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'pusat'			=> $pusat,
			'subgudang'		=> $subgudang
		);
		$this->load->view('Confirm_outgoing_spk/index',$data);
	}

	public function server_side_outgoing_spk(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_outgoing_spk(
			$requestData['pusat'],
			$requestData['subgudang'],
			$requestData['tanda'],
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
		$GET_USER = get_detail_user();
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

			$DATE_TRANS = (!empty($row['tanggal']))?$row['tanggal']:$row['created_date'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($DATE_TRANS))."</div>";
			$nestedData[]	= "<div align='center'>".$row['kd_gudang_dari']."</div>";
			$nestedData[]	= "<div align='center'>".$row['kd_gudang_ke']."</div>";
			$namaLengkap = (!empty($GET_USER[$row['created_by_spk']]['nm_lengkap']))?$GET_USER[$row['created_by_spk']]['nm_lengkap']:$row['created_by_spk'];
			$nestedData[]	= "<div align='center'>".strtoupper($namaLengkap)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date_spk']))."</div>";
			$status = "WAITING";
			$warna = 'blue';
			if($row['checked'] == 'Y'){
				$status = "CONFIRMED";
				$warna = 'green';
			}
			if(!empty($row['deleted_date'])){
				$status = "CANCELED";
				$warna = 'red';
			}
			$nestedData[]	= "<div align='center'><span class='badge bg-".$warna."'>".$status."</span></div>";
			$createspk	= "";
            if($row['checked'] == 'N'){
                $createspk	= "&nbsp;<button type='button' class='btn btn-sm btn-success createspk' title='Create SPK' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-check'></i></button>";
            }
            else{
                $createspk	= "&nbsp;<button type='button' class='btn btn-sm btn-default detailspk' title='Detail' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-file'></i></button>";
            }

			$nestedData[]	= "<div align='center'>
									".$createspk."
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

	public function query_data_json_outgoing_spk($pusat, $subgudang, $tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){


		$where_pusat ='';
		if(!empty($pusat)){
			$where_pusat = " AND a.id_gudang_dari = '".$pusat."' ";
		}

		$where_subgudang ='';
		if(!empty($subgudang)){
			$where_subgudang = " AND a.id_gudang_ke = '".$subgudang."' ";
		}

		$where_tanda ='';
		if(!empty($tanda)){
			$where_tanda = " AND a.category = '".$tanda."' ";
		}

		$where_tanda2 ='';
		// if(!empty($uri_tanda)){
			// $where_tanda2 = " AND a.checked = 'N' ";
		// }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.id AS id_spk_create,
                b.created_by AS created_by_spk,
                b.created_date AS created_date_spk
			FROM
				warehouse_adjustment a
				LEFT JOIN warehouse_adjustment_spk b ON a.kode_trans=b.kode_trans,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.status_id = '1' AND b.id IS NOT NULL
				".$where_tanda."
				".$where_tanda2."
				".$where_pusat."
				".$where_subgudang."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
				GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'created_date',
			3 => 'kd_gudang_dari',
			4 => 'kd_gudang_ke',
			5 => 'jumlah_mat',
			6 => 'created_by',
			7 => 'created_date'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function modal_confirm_spk(){
		if($this->input->post()){
			$post = $this->input->post();
			$data_session	= $this->session->userdata;
            $UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			$detail 		= $post['detail'];
			$kode_trans     = $post['kode_trans'];
            $id_gudang_dari	    = $post['gudang_before'];
			$kode_gudang_dari 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);

			$id_tujuan	    = $post['gudang_after'];			
			$kode_gudang_tujuan 	= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);

			$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->row();
			$coa_gudang = $coa_1->coa_1;
			
			
			$coa_2    = $this->db->get_where('warehouse', array('id'=>$id_tujuan))->row();
			$coa_gudang2 = $coa_2->coa_1;

			$ArrConfirm = [];
			$ArrUpdate = [];
            $ArrUpdateStock		= array();

			$SUM_MAT = 0;

			foreach ($detail as $key => $value) {
				$QTY_OKE = 0;
				$qty_confirm    = str_replace(',','',$value['qty_out']);
				$konversi       = $value['konversi'];
				$qty_pax_max        = $value['qty_pax_max'] * $konversi;
				$qty_confirm_pack   = $qty_confirm * $konversi;

                $selisih_booking = $qty_pax_max - $qty_confirm_pack;
                if($selisih_booking < 0){
                    $selisih_booking = 0;
                }

				$ArrConfirm[$key]['id']                 = $value['id'];
				$ArrConfirm[$key]['qty_confirm_pack']   = $qty_confirm;
                $ArrConfirm[$key]['confirm_by']         = $UserName;
				$ArrConfirm[$key]['confirm_date']       = $DateTime;

				$getQtyBooking  = $this->db->get_where('warehouse_adjustment_check',array('id'=>$value['id_lot']))->result_array();
				$qtyBooking     = (!empty($getQtyBooking[0]['qty_booking']))?$getQtyBooking[0]['qty_booking']:0;
				$qtyKeluar      = (!empty($getQtyBooking[0]['qty_out']))?$getQtyBooking[0]['qty_out']:0;

				$ArrUpdate[$key]['id'] 			= $value['id_lot'];
				$ArrUpdate[$key]['qty_booking'] = $qtyBooking - $selisih_booking - ($qty_confirm_pack);
				$ArrUpdate[$key]['qty_out']     = $qtyKeluar + ($qty_confirm_pack);

                //MATERIAL YANG AKAN DI UPDATE
                $ArrUpdateStock[$key]['id'] 	= $value['id_material'];
                $ArrUpdateStock[$key]['qty'] 	= $qty_confirm_pack;
				
				
				$ID_MATERIAL_ACT = $value['id_material'];	
               	$getDetMat 		= $this->db->get_where('raw_materials', array('id_material'=>$ID_MATERIAL_ACT))->result();
						
				$key2 = $getDetMat[0]->id_material;			
				$QTY_OKE = 	$qty_confirm_pack;
				
			  	$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$PRICE=0;
				$bmunit = 0;
				$bm = 0;

                $qty_akhir = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key2),1)->row();
				$costbook = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key2),1)->row();
				
				
				if(!empty($costbook)) $PRICE=$costbook->harga;
				if(!empty($qty_akhir)) $stokjurnalakhir=$qty_akhir->qty_stock;				
				if(!empty($qty_akhir)) $nilaijurnalakhir=$PRICE*$stokjurnalakhir;

				$ArrUpdateStock[$key]['harga_pusat'] 	= $PRICE;

				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				
				$GudangFrom = $id_gudang_dari;
					

				
				
				
				$ArrJurnalNew[$key]['id_material'] 		= $getDetMat[0]->id_material;
				$ArrJurnalNew[$key]['idmaterial'] 			= $getDetMat[0]->idmaterial;
				$ArrJurnalNew[$key]['nm_material'] 		= $getDetMat[0]->nm_material;
				$ArrJurnalNew[$key]['id_category'] 		= $getDetMat[0]->id_category;
				$ArrJurnalNew[$key]['nm_category'] 		= $getDetMat[0]->nm_category;
				$ArrJurnalNew[$key]['id_gudang'] 			= $id_gudang_dari;
				$ArrJurnalNew[$key]['kd_gudang'] 			= $kode_gudang_dari;
				$ArrJurnalNew[$key]['id_gudang_dari'] 	    = $id_gudang_dari;
				$ArrJurnalNew[$key]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
				$ArrJurnalNew[$key]['id_gudang_ke'] 		= $id_tujuan;
				$ArrJurnalNew[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew[$key]['qty_stock_awal'] 		= $stokjurnalakhir;
				$ArrJurnalNew[$key]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
				$ArrJurnalNew[$key]['kode_trans'] 			= $kode_trans;
				$ArrJurnalNew[$key]['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew[$key]['qty_out'] 			= $QTY_OKE;
				$ArrJurnalNew[$key]['ket'] 				= 'pindah gudang out';
				$ArrJurnalNew[$key]['harga'] 			= $PRICE;
				$ArrJurnalNew[$key]['harga_bm'] 		= 0;
				$ArrJurnalNew[$key]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$key]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
				$ArrJurnalNew[$key]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
				$ArrJurnalNew[$key]['update_by'] 		= $UserName;
				$ArrJurnalNew[$key]['update_date'] 		= $DateTime;
				$ArrJurnalNew[$key]['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew[$key]['coa_gudang'] 		= $coa_gudang;
				
				
			

				$stokjurnalakhir2=0;
				$nilaijurnalakhir2=0;
				$PRICE2 =0;
				$stok_jurnal_akhir2 = $this->db->order_by('tgl_trans','desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_tujuan, 'id_material'=>$key2),1)->row();
				if(!empty($stok_jurnal_akhir2)) $stokjurnalakhir2=$stok_jurnal_akhir2->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir2)) $nilaijurnalakhir2=$stok_jurnal_akhir2->nilai_akhir_rp;

				$qty_akhir2 = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_tujuan, 'id_material'=>$key2),1)->row();
				$costbook2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_tujuan, 'id_material'=>$key2),1)->row();
				
				
				if(!empty($costbook2)) $PRICE2=$costbook2->harga;
				if(!empty($qty_akhir2)) $stokjurnalakhir2=$qty_akhir->qty_stock;				
				if(!empty($qty_akhir2)) $nilaijurnalakhir2=$PRICE2*$stokjurnalakhir;
				
				
				$GudangFrom2 = $id_tujuan;
			
							
				
				$PRICENEW = round(($PRICE*$QTY_OKE) + ($PRICE2*$stokjurnalakhir2))/($QTY_OKE+$stokjurnalakhir2);
				$in   = 'pindah gudang in';
				$ket  = $in.$id_gudang_dari.$id_tujuan;
				
				$ArrJurnalNew2[$key]['id_material'] 		= $getDetMat[0]->id_material;
				$ArrJurnalNew2[$key]['idmaterial'] 		= $getDetMat[0]->idmaterial;
				$ArrJurnalNew2[$key]['nm_material'] 		= $getDetMat[0]->nm_material;
				$ArrJurnalNew2[$key]['id_category'] 		= $getDetMat[0]->id_category;
				$ArrJurnalNew2[$key]['nm_category'] 		= $getDetMat[0]->nm_category;
				$ArrJurnalNew2[$key]['id_gudang'] 			= $id_tujuan;
				$ArrJurnalNew2[$key]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrJurnalNew2[$key]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
				$ArrJurnalNew2[$key]['id_gudang_ke'] 		= $id_tujuan;
				$ArrJurnalNew2[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_tujuan);
				$ArrJurnalNew2[$key]['qty_stock_awal'] 	= $stokjurnalakhir2;
				$ArrJurnalNew2[$key]['qty_stock_akhir'] 	= $stokjurnalakhir2+$QTY_OKE;
				$ArrJurnalNew2[$key]['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew2[$key]['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew2[$key]['qty_in'] 			= $QTY_OKE;
				$ArrJurnalNew2[$key]['ket'] 				= $ket;
				$ArrJurnalNew2[$key]['harga'] 				= $PRICENEW;
				$ArrJurnalNew2[$key]['harga_bm'] 			= 0; 
				$ArrJurnalNew2[$key]['nilai_awal_rp']		= $nilaijurnalakhir2;
				$ArrJurnalNew2[$key]['nilai_trans_rp']		= $PRICE*$QTY_OKE;
				$ArrJurnalNew2[$key]['nilai_akhir_rp']		= ($stokjurnalakhir2+$QTY_OKE)*$PRICENEW;
				$ArrJurnalNew2[$key]['update_by'] 			= $UserName;
				$ArrJurnalNew2[$key]['update_date'] 		= $DateTime;
				$ArrJurnalNew2[$key]['no_jurnal'] 			= '-';
				$ArrJurnalNew2[$key]['coa_gudang'] 		= $coa_gudang2;
					
				
				$SUM_MAT 	+= $QTY_OKE;

				$ArrUpdateStock[$key]['harga_tujuan'] 	= $PRICE2;
				$ArrUpdateStock[$key]['harga_baru'] 	= $PRICENEW;
			}

            //grouping sum
			$temp = [];
			$grouping_temp = [];
			$key = 0;
			foreach($ArrUpdateStock as $value) { $key++;
				if($value['qty'] > 0){
					if(!array_key_exists($value['id'], $temp)) {
						$temp[$value['id']]['good'] = 0;
					}
					$temp[$value['id']]['good'] += $value['qty'];

					$grouping_temp[$value['id']]['id'] 			= $value['id'];
					$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
				}
			}

            //UPDATE NOMOR SURAT JALAN
			$monthYear 		= date('/m/Y');
			$kode_gudang 	= get_name('warehouse', 'kode', 'id', $id_gudang_dari);

			$qIPP			= "SELECT MAX(no_surat_jalan) as maxP FROM warehouse_adjustment WHERE no_surat_jalan LIKE '%/IA".$kode_gudang.$monthYear."' ";
			$numrowIPP		= $this->db->query($qIPP)->num_rows();
			$resultIPP		= $this->db->query($qIPP)->result_array();
			$angkaUrut2		= $resultIPP[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 0, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$no_surat_jalan	= $urut2."/IA".$kode_gudang.$monthYear;

			$ArrUpdateHeader = array( 
				'checked' 			=> 'Y',
				'no_surat_jalan' 	=> $no_surat_jalan,
				'checked_by'		=> $UserName,
				'checked_date'		=> $DateTime
			);

			$this->db->trans_start();
                if(!empty($ArrUpdateStock)){
                    move_warehouse($ArrUpdateStock,$id_gudang_dari,$id_tujuan,$kode_trans);
                }

				$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew);
				
				$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew2);

                if(!empty($grouping_temp)){
                    insert_jurnal($grouping_temp,$id_gudang_dari,$id_tujuan,$kode_trans,'transfer pusat - subgudang','pengurangan gudang pusat','penambahan subgudang');
                }
				if(!empty($ArrConfirm)){
					$this->db->update_batch('warehouse_adjustment_spk', $ArrConfirm, 'id');
				}
				if(!empty($ArrUpdate)){
					$this->db->update_batch('warehouse_adjustment_check', $ArrUpdate,'id');
				}

                $this->db->where('kode_trans', $kode_trans);
				$this->db->update('warehouse_adjustment', $ArrUpdateHeader);
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
				insertDataGroupReport($ArrUpdateStock, $id_gudang_dari, $id_tujuan, $kode_trans, null, null, null);
				history('Create SPK Outgoing '.$kode_trans);
			}
			echo json_encode($Arr_Data);

		}
		else{
			$kode_trans     = $this->uri->segment(3);
			$tanda     = $this->uri->segment(4);

			$result_header	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
			
			$listLotMaterial = $this->db
									->select('
										a.id,
										a.id_material,
										a.expired_date,
										a.qty_oke,
										a.qty_out,
										a.qty_booking,
										a.keterangan,
										b.nm_material,
										b.id_satuan,
										b.id_packing,
										b.nilai_konversi AS konversi,
										a.update_by,
										a.update_date,
                                        z.qty_unit,
                                        z.qty_pack,
                                        z.id AS id_spk,
                                        z.qty_confirm_pack AS qty_confirm
									')
									->where('a.qty_oke > a.qty_out')
									->join('raw_materials b','z.id_material=b.id_material','left')
									->join('warehouse_adjustment_check a','a.id=z.id_lot','left')
									->get_where('warehouse_adjustment_spk z',array('z.deleted_date'=>null,'z.kode_trans'=>$kode_trans))
									->result_array();


			$data = array(
				'tanda' 	=> $tanda,
				'listLotMaterial' 	=> $listLotMaterial,
				'checked' 		=> $result_header[0]->checked,
				'gudang_before' => $result_header[0]->id_gudang_dari,
				'gudang_after' 	=> $result_header[0]->id_gudang_ke,
				'kode_trans'	=> $result_header[0]->kode_trans,
				'no_ipp'		=> $result_header[0]->no_ipp,
				'dated' 		=> date('ymdhis', strtotime($result_header[0]->created_date)),
				'resv' 			=> date('d F Y', strtotime($result_header[0]->created_date)),
				'createdBy' 	=> get_name('users','nm_lengkap','username',$result_header[0]->created_by),
			);

			$this->load->view('Confirm_outgoing_spk/modal_confirm_spk', $data);
		}
	}

    public function check_qr(){
        $kode_trans = $this->input->post('kode_trans');
		$explode = explode('///', $this->input->post('qr_code'));
        $ID = $explode[0];

        $listLotMaterial = $this->db
									->select('
										a.id,
										a.id_material,
										a.expired_date,
										a.qty_oke,
										a.qty_out,
										a.qty_booking,
										a.keterangan,
										b.nm_material,
										b.id_satuan,
										b.id_packing,
										b.nilai_konversi AS konversi,
										a.update_by,
										a.update_date,
                                        z.qty_unit,
                                        z.qty_pack,
                                        z.id AS id_spk,
                                        z.qty_confirm_pack AS qty_confirm
									')
									->where('a.qty_oke > a.qty_out')
									->join('raw_materials b','z.id_material=b.id_material','left')
									->join('warehouse_adjustment_check a','a.id=z.id_lot','left')
									->get_where('warehouse_adjustment_spk z',array('z.deleted_date'=>null,'z.kode_trans'=>$kode_trans,'z.id_lot'=>$ID))
									->result_array();

        $Arr_Data	= array(
            'pesan'		=> (!empty($listLotMaterial))?'Product sesuai!':'Product tidak sesuai dengan spk!',
            'status'	=> (!empty($listLotMaterial))?1:0,
            'id_spk'	=> $ID,
            'qty_spk'	=> (!empty($listLotMaterial))?$listLotMaterial[0]['qty_pack']:'',
        );

		echo json_encode($Arr_Data);
	}

}