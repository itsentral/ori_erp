<?php
class Adjustment_material_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function adjustment(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/adjustment';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='pusat' ORDER BY urut ASC")->result_array();
		$no_po				= $this->db->query("SELECT no_po FROM tran_material_po_header WHERE status='WAITING IN' OR status='IN PARSIAL' ORDER BY no_po ASC ")->result_array();
		$query	 	= "SELECT id_material, nm_material FROM raw_materials WHERE `delete` = 'N' ORDER BY nm_material ASC";
		$material	= $this->db->query($query)->result();
			
		$data = array(
			'title'			=> 'Warehouse Material >> Adjustment',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'material'		=> $material,
			'pusat'			=> $pusat,
			'no_po'			=> $no_po
		);
		history('View Adjustment Material'); 
		$this->load->view('Adjustment_material/index',$data);
	}
	
	public function get_data_json_adjustment(){
		
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_adjustment(
			$requestData['type'],
			$requestData['material'],
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
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['adjustment_type'])."</div>";
			$gudang_dari 	= (!empty($row['id_gudang_dari']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_dari']):$row['kd_gudang_dari']." ".strtoupper($row['adjustment_type']);
			$gudang_ke 		= (!empty($row['id_gudang_ke']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']):$row['kd_gudang_ke']." ".strtoupper($row['adjustment_type']);
			$nestedData[]	= "<div align='left'>".$gudang_dari."</div>";
			$nestedData[]	= "<div align='left'>".$gudang_ke."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'],4)."</div>";
			$expired 		= ($row['expired_date'] != '0000-00-00' AND $row['expired_date'] != NULL)?date('d-M-Y', strtotime($row['expired_date'])):'-';
			$nestedData[]	= "<div align='right'>".$expired."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['pic'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_ba'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['note'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
				// $detail	= "<button type='button' class='btn btn-sm btn-primary detailAjust' title='View Incoming' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-eye'></i></button>";
				// $print	= "&nbsp;<a href='".base_url('warehouse/print_incoming/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Incoming'><i class='fa fa-print'></i></a>";
				
				// $detail = "";
				// $print = "";
			// $nestedData[]	= "<div align='center'>
                                    // ".$detail."
									// ".$print."
									// </div>";
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

	public function query_data_json_adjustment($type, $material, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($type <> '0'){
			$where = " AND a.adjustment_type='".$type."' ";
		}
		
		$whereMaterial = "";
		if($material <> '0'){
			$whereMaterial = " AND b.id_material='".$material."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_ba,
				b.nm_material,
				b.expired_date
			FROM
				warehouse_adjustment a LEFT JOIN warehouse_adjustment_detail b ON a.kode_trans=b.kode_trans,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.category = 'adjustment material' ".$where." ".$whereMaterial." AND a.status_id='1'
			AND(
				a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.pic LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_ba LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'adjustment_type',
			3 => 'id_gudang_dari',
			4 => 'id_gudang_ke',
			5 => 'nm_material',
			6 => 'jumlah_mat',
			7 => 'expired_date',
			8 => 'pic',
			9 => 'no_ba',
			10 => 'note',
			11 => 'created_by',
			12 => 'created_date'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add_adjustment(){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			
			$adjustment_type 	= $data['adjustment_type'];
			$id_material 		= $data['id_material'];
			$no_ba 				= strtolower($data['no_ba']);
			$qty_oke 			= str_replace(',','',$data['qty_oke']);
			$keterangan 		= strtolower($data['keterangan']);
			
			$id_gudang_dari_m 	= $data['id_gudang_dari_m'];
			$id_gudang_ke_m 	= $data['id_gudang_ke_m'];
			$kd_gudang_dari_m 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
			$kd_gudang_ke_m 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
			$pic_m 				= strtolower($data['pic_m']);
			$expired_date_m 	= $data['expired_date_m'];
			
			$id_gudang_ke 		= $data['id_gudang_ke'];
			$kd_gudang_ke 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
			$pic 				= strtolower($data['pic']);
			$expired_date 		= $data['expired_date'];
			
			$Ym 			= date('ym');
			
			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
			$nm_material=get_name('raw_materials', 'nm_material', 'id_material', $id_material);
			
			$ArrHeader = array(
				'kode_trans' 		=> $kode_trans,
				'category' 			=> 'adjustment material',
				'adjustment_type' 	=> $adjustment_type,
				'jumlah_mat' 		=> $qty_oke,
				'jumlah_mat_check' 	=> $qty_oke,
				'id_gudang_dari' 	=> ($adjustment_type == 'mutasi')?$id_gudang_dari_m:NULL,
				'kd_gudang_dari' 	=> ($adjustment_type == 'mutasi')?$kd_gudang_dari_m:'ADJUSTMENT '.strtoupper($adjustment_type),
				'id_gudang_ke' 		=> ($adjustment_type == 'mutasi')?$id_gudang_ke_m:$id_gudang_ke,
				'kd_gudang_ke' 		=> ($adjustment_type == 'mutasi')?$kd_gudang_ke_m:$kd_gudang_ke,
				'pic' 				=> ($adjustment_type == 'mutasi')?$pic_m:$pic,
				'note' 				=> $keterangan,
				'checked' 			=> 'Y',
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> date('Y-m-d H:i:s'),
				'checked_by' 		=> $data_session['ORI_User']['username'],
				'checked_date' 		=> date('Y-m-d H:i:s')
			);
			
			$ArrDetail = array(
				'kode_trans' 		=> $kode_trans,
				'id_material' 		=> $id_material,
				'nm_material' 		=> $nm_material,
				'id_category' 		=> get_name('raw_materials', 'id_category', 'id_material', $id_material),
				'nm_category' 		=> get_name('raw_materials', 'nm_category', 'id_material', $id_material),
				'qty_order' 		=> $qty_oke,
				'qty_oke' 			=> $qty_oke,
				'expired_date' 		=> ($adjustment_type == 'mutasi')?$expired_date_m:$expired_date,
				'no_ba' 			=> $no_ba,
				'keterangan' 		=> $keterangan,
				'update_by' 		=> $data_session['ORI_User']['username'],
				'update_date' 		=> date('Y-m-d H:i:s'),
				'check_qty_oke' 	=> $qty_oke,
				'check_expired_date'=> ($adjustment_type == 'mutasi')?$expired_date_m:$expired_date,
				'check_keterangan' 	=> $keterangan
			);
			
			$ArrStockDari = array();
			$ArrHistDari = array();
			$ArrStockKe = array();
			$ArrHistKe = array();
			$ArrPlus = array();
			$ArrPlusHist = array();
			
			$ArrStockDariExp = array();
			$ArrHistDariExp = array();
			$ArrStockKeExp = array();
			$ArrHistKeExp = array();
			$ArrPlusExp = array();
			$ArrPlusHistExp = array();
			
			$this->db->trans_start();
			//MUTASI
			if($adjustment_type == 'mutasi'){
				//pengurangan gudang asal
				$check_gudang_dari	= "SELECT b.* FROM warehouse_stock b WHERE b.id_material = '".$id_material."' AND b.id_gudang='".$id_gudang_dari_m."' LIMIT 1";
				$rest_gudang_dari	= $this->db->query($check_gudang_dari)->result();
				
				
				
				if(empty($rest_gudang_dari)){
					$Arr_Data	= array(
						'pesan'		=>'Stock gudang tidak ditemukan ...',
						'status'	=> 0
					);
					echo json_encode($Arr_Data);
					return false;
				}
				
				if(!empty($rest_gudang_dari)){
					$ArrStockDari['id'] 			= $rest_gudang_dari[0]->id;
					$ArrStockDari['qty_stock'] 		= $rest_gudang_dari[0]->qty_stock - $qty_oke;
					$ArrStockDari['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockDari['update_date'] 	= date('Y-m-d H:i:s');
					
					$ArrHistDari['id_material'] 	= $rest_gudang_dari[0]->id_material;
					$ArrHistDari['idmaterial'] 		= $rest_gudang_dari[0]->idmaterial;
					$ArrHistDari['nm_material'] 	= $rest_gudang_dari[0]->nm_material;
					$ArrHistDari['id_category'] 	= $rest_gudang_dari[0]->id_category;
					$ArrHistDari['nm_category'] 	= $rest_gudang_dari[0]->nm_category;
					$ArrHistDari['id_gudang'] 		= $id_gudang_dari_m;
					$ArrHistDari['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
					$ArrHistDari['id_gudang_dari'] 	= $id_gudang_dari_m;
					$ArrHistDari['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
					$ArrHistDari['id_gudang_ke'] 	= $id_gudang_ke_m;
					$ArrHistDari['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
					$ArrHistDari['qty_stock_awal'] 	= $rest_gudang_dari[0]->qty_stock;
					$ArrHistDari['qty_stock_akhir'] = $rest_gudang_dari[0]->qty_stock - $qty_oke;
					$ArrHistDari['qty_booking_awal'] 	= $rest_gudang_dari[0]->qty_booking;
					$ArrHistDari['qty_booking_akhir'] 	= $rest_gudang_dari[0]->qty_booking;
					$ArrHistDari['qty_rusak_awal'] 		= $rest_gudang_dari[0]->qty_rusak;
					$ArrHistDari['qty_rusak_akhir'] 	= $rest_gudang_dari[0]->qty_rusak;
					$ArrHistDari['no_ipp'] 				= $kode_trans;
					$ArrHistDari['jumlah_mat'] 			= $qty_oke;
					$ArrHistDari['ket'] 				= 'pengurangan gudang mutasi';
					$ArrHistDari['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistDari['update_date'] 		= date('Y-m-d H:i:s');
					
					
				$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari_m))->row();
				$coa_gudang = $coa_1->coa_1;
				$kategori_gudang = $coa_1->category;
				
				$id_material = 	$rest_gudang_dari[0]->id_material;
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari_m, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				$QTY_OKE      = $qty_oke;
				
				$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
				
				
				$ArrJurnalNew['id_material'] 		= $rest_gudang_dari[0]->id_material;
				$ArrJurnalNew['idmaterial'] 		= $rest_gudang_dari[0]->idmaterial;
				$ArrJurnalNew['nm_material'] 		= $rest_gudang_dari[0]->nm_material;
				$ArrJurnalNew['id_category'] 		= $rest_gudang_dari[0]->id_category;
				$ArrJurnalNew['nm_category'] 		= $rest_gudang_dari[0]->nm_category;
				$ArrJurnalNew['id_gudang'] 			= $id_gudang_dari_m;
				$ArrJurnalNew['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
				$ArrJurnalNew['id_gudang_dari'] 	    = $id_gudang_dari_m;
				$ArrJurnalNew['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
				$ArrJurnalNew['id_gudang_ke'] 		= $id_gudang_ke_m;
				$ArrJurnalNew['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
				$ArrJurnalNew['qty_stock_awal'] 		= $stokjurnalakhir;
				$ArrJurnalNew['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
				$ArrJurnalNew['kode_trans'] 			= $kode_trans;
				$ArrJurnalNew['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew['qty_out'] 			= $QTY_OKE;
				$ArrJurnalNew['ket'] 				= 'pindah gudang';
				$ArrJurnalNew['harga'] 			= $PRICE;
				$ArrJurnalNew['harga_bm'] 		= 0;
				$ArrJurnalNew['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew['nilai_trans_rp']	= $PRICE*$QTY_OKE;
				$ArrJurnalNew['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
				$ArrJurnalNew['update_by'] 		= $UserName;
				$ArrJurnalNew['update_date'] 		= $DateTime;
				$ArrJurnalNew['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew['coa_gudang'] 		= $coa_gudang;
				
				
					
					if($expired_date_m != NULL AND $expired_date_m != '' AND $expired_date_m != '0'){
						//Update Warehouse Expired Minus
						$stock_exp	= "SELECT a.* FROM  warehouse_stock_expired a WHERE a.id_material = '".$id_material."' AND a.id_gudang='".$id_gudang_dari_m."' AND a.expired='".$expired_date_m."'";
						$rest_exp_dari	= $this->db->query($stock_exp)->result();
						//kurangi stock gudang dari
						if(!empty($rest_exp_dari)){
							$ArrStockDariExp['id'] 			= $rest_exp_dari[0]->id;
							$ArrStockDariExp['qty_stock'] 	= $rest_exp_dari[0]->qty_stock - $qty_oke;
							$ArrStockDariExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrStockDariExp['update_date'] = date('Y-m-d H:i:s');
							
							$ArrHistDariExp['id_material'] 		= $rest_exp_dari[0]->id_material;
							$ArrHistDariExp['nm_material'] 		= $rest_exp_dari[0]->nm_material;;
							$ArrHistDariExp['id_gudang'] 		= $id_gudang_dari_m;
							$ArrHistDariExp['expired'] 			= $expired_date_m;
							$ArrHistDariExp['qty_stock'] 		= $rest_exp_dari[0]->qty_stock;
							$ArrHistDariExp['qty_rusak'] 		= $rest_exp_dari[0]->qty_rusak;
							$ArrHistDariExp['qty_stock_akhir'] 	= $rest_exp_dari[0]->qty_stock - $qty_oke;
							$ArrHistDariExp['qty_rusak_akhir'] 	= $rest_exp_dari[0]->qty_rusak;
							$ArrHistDariExp['kode_trans'] 		= $kode_trans;
							$ArrHistDariExp['update_by'] 		= $data_session['ORI_User']['username'];
							$ArrHistDariExp['update_date'] 		= date('Y-m-d H:i:s');
						}
					}
					
				}
				
				//penambahan gudang tujuan
				$check_gudang_ke	= "SELECT b.* FROM warehouse_stock b WHERE b.id_material = '".$id_material."' AND b.id_gudang='".$id_gudang_ke_m."' LIMIT 1";
				$rest_gudang_ke	= $this->db->query($check_gudang_ke)->result();
				
				if(!empty($rest_gudang_ke)){
					$ArrStockKe['id'] 				= $rest_gudang_ke[0]->id;
					$ArrStockKe['qty_stock'] 		= $rest_gudang_ke[0]->qty_stock + $qty_oke;
					$ArrStockKe['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockKe['update_date'] 		= date('Y-m-d H:i:s');
					
					$ArrHistKe['id_material'] 		= $rest_gudang_ke[0]->id_material;
					$ArrHistKe['idmaterial'] 		= $rest_gudang_ke[0]->idmaterial;
					$ArrHistKe['nm_material'] 		= $rest_gudang_ke[0]->nm_material;
					$ArrHistKe['id_category'] 		= $rest_gudang_ke[0]->id_category;
					$ArrHistKe['nm_category'] 		= $rest_gudang_ke[0]->nm_category;
					$ArrHistKe['id_gudang'] 		= $id_gudang_ke_m;
					$ArrHistKe['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
					$ArrHistKe['id_gudang_dari'] 	= $id_gudang_dari_m;
					$ArrHistKe['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
					$ArrHistKe['id_gudang_ke'] 		= $id_gudang_ke_m;
					$ArrHistKe['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
					$ArrHistKe['qty_stock_awal'] 	= $rest_gudang_ke[0]->qty_stock;
					$ArrHistKe['qty_stock_akhir'] 	= $rest_gudang_ke[0]->qty_stock + $qty_oke;
					$ArrHistKe['qty_booking_awal'] 	= $rest_gudang_ke[0]->qty_booking;
					$ArrHistKe['qty_booking_akhir'] = $rest_gudang_ke[0]->qty_booking;
					$ArrHistKe['qty_rusak_awal'] 	= $rest_gudang_ke[0]->qty_rusak;
					$ArrHistKe['qty_rusak_akhir'] 	= $rest_gudang_ke[0]->qty_rusak;
					$ArrHistKe['no_ipp'] 			= $kode_trans;
					$ArrHistKe['jumlah_mat'] 		= $qty_oke;
					$ArrHistKe['ket'] 				= 'penambahan gudang mutasi';
					$ArrHistKe['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHistKe['update_date'] 		= date('Y-m-d H:i:s');
					
					
				
				$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_ke_m))->row();
				$coa_gudang = $coa_1->coa_1;
				$kategori_gudang = $coa_1->category;
				
				$id_material = 	$rest_gudang_ke[0]->id_material;
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke_m, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				$QTY_OKE      = $qty_oke;
				
				
				$coa_2   = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari_m))->row();
				$coa_gudang2 = $coa_2->coa_1;
				$kategori_gudang2 = $coa_2->category;
				
				
				
				$Gudang2 = $kategori_gudang2;
				if($Gudang2 == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir1 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir1)) $PRICE=$harga_jurnal_akhir1->harga;
				}elseif($Gudang2 == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir1 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir1)) $PRICE=$harga_jurnal_akhir1->harga;
				}elseif($Gudang2 == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_produksi',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir1 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir1)) $PRICE=$harga_jurnal_akhir1->harga;
					
				}
				
				
				$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
			
					
				}
				
				$stokjurnalakhir2=0;
				$nilaijurnalakhir2=0;
				$stok_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke_m, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir2)) $stokjurnalakhir2=$stok_jurnal_akhir2->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir2)) $nilaijurnalakhir2=$stok_jurnal_akhir2->nilai_akhir_rp;
				
								
				
				
				$PRICENEW = (($PRICE*$QTY_OKE) + ($PRICE2*$stokjurnalakhir2))/($QTY_OKE+$stokjurnalakhir2);
				
				
				$ArrJurnalNew2['id_material'] 		= $rest_gudang_ke[0]->id_material;
				$ArrJurnalNew2['idmaterial'] 		= $rest_gudang_ke[0]->idmaterial;
				$ArrJurnalNew2['nm_material'] 		= $rest_gudang_ke[0]->nm_material;
				$ArrJurnalNew2['id_category'] 		= $rest_gudang_ke[0]->id_category;
				$ArrJurnalNew2['nm_category'] 		= $rest_gudang_ke[0]->nm_category;
				$ArrJurnalNew2['id_gudang'] 			= $id_gudang_dari_m;
				$ArrJurnalNew2['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
				$ArrJurnalNew2['id_gudang_dari'] 	= $id_gudang_dari_m;
				$ArrJurnalNew2['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
				$ArrJurnalNew2['id_gudang_ke'] 		= $id_gudang_ke_m;
				$ArrJurnalNew2['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
				$ArrJurnalNew2['qty_stock_awal'] 	= $stokjurnalakhir2;
				$ArrJurnalNew2['qty_stock_akhir'] 	= $stokjurnalakhir2+$QTY_OKE;
				$ArrJurnalNew2['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew2['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew2['qty_in'] 			= $QTY_OKE;
				$ArrJurnalNew2['ket'] 				= 'mutasi adjustmnent';
				$ArrJurnalNew2['harga'] 				= $PRICENEW;
				$ArrJurnalNew2['harga_bm'] 			= 0;
				$ArrJurnalNew2['nilai_awal_rp']		= $nilaijurnalakhir2;
				$ArrJurnalNew2['nilai_trans_rp']		= $PRICE*$QTY_OKE;
				$ArrJurnalNew2['nilai_akhir_rp']		= ($stokjurnalakhir2+$QTY_OKE)*$PRICENEW;
				$ArrJurnalNew2['update_by'] 			= $UserName;
				$ArrJurnalNew2['update_date'] 		= $DateTime;
				$ArrJurnalNew2['no_jurnal'] 			= '-';
				$ArrJurnalNew2['coa_gudang'] 		= $coa_gudang;
				
					
					if($expired_date_m != NULL AND $expired_date_m != '' AND $expired_date_m != '0'){
						//Update Warehouse Expired Minus
						$stock_exp	= "SELECT a.* FROM  warehouse_stock_expired a WHERE a.id_material = '".$id_material."' AND a.id_gudang='".$id_gudang_ke_m."' AND a.expired='".$expired_date_m."'";
						$rest_exp	= $this->db->query($stock_exp)->result();
						//kurangi stock gudang dari
						if(!empty($rest_exp)){
							$ArrStockKeExp['id'] 			= $rest_exp[0]->id;
							$ArrStockKeExp['qty_stock'] 	= $rest_exp[0]->qty_stock + $qty_oke;
							$ArrStockKeExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrStockKeExp['update_date'] 	= date('Y-m-d H:i:s');
							
							$ArrHistKeExp['id_material'] 	= $rest_exp[0]->id_material;
							$ArrHistKeExp['nm_material'] 	= $rest_exp[0]->nm_material;;
							$ArrHistKeExp['id_gudang'] 		= $id_gudang_ke_m;
							$ArrHistKeExp['expired'] 		= $expired_date_m;
							$ArrHistKeExp['qty_stock'] 		= $rest_exp[0]->qty_stock;
							$ArrHistKeExp['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
							$ArrHistKeExp['qty_stock_akhir'] 	= $rest_exp[0]->qty_stock + $qty_oke;
							$ArrHistKeExp['qty_rusak_akhir'] 	= $rest_exp[0]->qty_rusak;
							$ArrHistKeExp['kode_trans'] 		= $kode_trans;
							$ArrHistKeExp['update_by'] 			= $data_session['ORI_User']['username'];
							$ArrHistKeExp['update_date'] 		= date('Y-m-d H:i:s');
						}
						$sql_mat	= "SELECT a.* FROM raw_materials a WHERE a.id_material = '".$id_material."' LIMIT 1 ";
						$rest_mat	= $this->db->query($sql_mat)->result();
						if(empty($rest_exp)){
							$ArrPlusExp['id_material'] 	= $rest_mat[0]->id_material;
							$ArrPlusExp['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrPlusExp['id_gudang'] 	= $id_gudang_ke_m;
							$ArrPlusExp['expired'] 		= $expired_date_m;
							$ArrPlusExp['qty_stock'] 	= $qty_oke;
							$ArrPlusExp['qty_rusak'] 	= 0;
							$ArrPlusExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrPlusExp['update_date'] 	= date('Y-m-d H:i:s');
							
							$ArrPlusHistExp['id_material'] 	= $rest_mat[0]->id_material;
							$ArrPlusHistExp['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrPlusHistExp['id_gudang'] 	= $id_gudang_ke_m;
							$ArrPlusHistExp['expired'] 		= $expired_date_m;
							$ArrPlusHistExp['qty_stock'] 	= 0;
							$ArrPlusHistExp['qty_rusak'] 	= 0;
							$ArrPlusHistExp['qty_stock_akhir'] 	= $qty_oke;
							$ArrPlusHistExp['qty_rusak_akhir'] 	= 0;
							$ArrPlusHistExp['kode_trans'] 	= $kode_trans;
							$ArrPlusHistExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrPlusHistExp['update_date'] 	= date('Y-m-d H:i:s');
						}
					}
				}
				
				if(empty($rest_gudang_ke)){
					$sql_mat	= "SELECT a.* FROM raw_materials a WHERE a.id_material = '".$id_material."' LIMIT 1 ";
					$rest_mat	= $this->db->query($sql_mat)->result();
					
					$ArrPlus['id_material'] 	= $rest_mat[0]->id_material;
					$ArrPlus['idmaterial'] 		= $rest_mat[0]->idmaterial;
					$ArrPlus['nm_material'] 	= $rest_mat[0]->nm_material;
					$ArrPlus['id_category'] 	= $rest_mat[0]->id_category;
					$ArrPlus['nm_category'] 	= $rest_mat[0]->nm_category;
					$ArrPlus['id_gudang'] 		= $id_gudang_ke_m;
					$ArrPlus['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
					$ArrPlus['qty_stock'] 		= $qty_oke;
					$ArrPlus['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrPlus['update_date'] 	= date('Y-m-d H:i:s');
					
					$ArrPlusHist['id_material'] 	= $rest_mat[0]->id_material;
					$ArrPlusHist['idmaterial'] 		= $rest_mat[0]->idmaterial;
					$ArrPlusHist['nm_material'] 	= $rest_mat[0]->nm_material;
					$ArrPlusHist['id_category'] 	= $rest_mat[0]->id_category;
					$ArrPlusHist['nm_category'] 	= $rest_mat[0]->nm_category;
					$ArrPlusHist['id_gudang'] 		= $id_gudang_ke_m;
					$ArrPlusHist['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
					$ArrPlusHist['id_gudang_dari'] 	= NULL;
					$ArrPlusHist['kd_gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
					$ArrPlusHist['id_gudang_ke'] 	= $id_gudang_ke_m;
					$ArrPlusHist['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
					$ArrPlusHist['qty_stock_awal'] 	= 0;
					$ArrPlusHist['qty_stock_akhir'] 	= $qty_oke;
					$ArrPlusHist['qty_booking_awal'] 	= 0;
					$ArrPlusHist['qty_booking_akhir'] 	= 0;
					$ArrPlusHist['qty_rusak_awal'] 		= 0;
					$ArrPlusHist['qty_rusak_akhir'] 	= 0;
					$ArrPlusHist['no_ipp'] 				= $kode_trans;
					$ArrPlusHist['jumlah_mat'] 			= $qty_oke;
					$ArrPlusHist['ket'] 				= 'penambahan gudang mutasi (insert new)';
					$ArrPlusHist['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrPlusHist['update_date'] 		= date('Y-m-d H:i:s');
					
					if($expired_date_m != NULL AND $expired_date_m != '' AND $expired_date_m != '0'){
						$ArrPlusExp['id_material'] 	= $rest_mat[0]->id_material;
						$ArrPlusExp['nm_material'] 	= $rest_mat[0]->nm_material;;
						$ArrPlusExp['id_gudang'] 	= $id_gudang_ke_m;
						$ArrPlusExp['expired'] 		= $expired_date_m;
						$ArrPlusExp['qty_stock'] 	= $qty_oke;
						$ArrPlusExp['qty_rusak'] 	= 0;
						$ArrPlusExp['update_by'] 	= $data_session['ORI_User']['username'];
						$ArrPlusExp['update_date'] 	= date('Y-m-d H:i:s');
						
						$ArrPlusHistExp['id_material'] 	= $rest_mat[0]->id_material;
						$ArrPlusHistExp['nm_material'] 	= $rest_mat[0]->nm_material;;
						$ArrPlusHistExp['id_gudang'] 	= $id_gudang_ke_m;
						$ArrPlusHistExp['expired'] 		= $expired_date_m;
						$ArrPlusHistExp['qty_stock'] 	= 0;
						$ArrPlusHistExp['qty_rusak'] 	= 0;
						$ArrPlusHistExp['qty_stock_akhir'] 	= $qty_oke;
						$ArrPlusHistExp['qty_rusak_akhir'] 	= 0;
						$ArrPlusHistExp['kode_trans'] 	= $kode_trans;
						$ArrPlusHistExp['update_by'] 	= $data_session['ORI_User']['username'];
						$ArrPlusHistExp['update_date'] 	= date('Y-m-d H:i:s');
					}
				}
				
			}
			//MINUS
			if($adjustment_type == 'minus'){
				//pengurangan gudang asal
				$check_gudang_dari	= "SELECT b.* FROM warehouse_stock b WHERE b.id_material = '".$id_material."' AND b.id_gudang='".$id_gudang_ke."' LIMIT 1";
				$rest_gudang_dari	= $this->db->query($check_gudang_dari)->result();
				
				$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_ke))->row();
				$coa_gudang = $coa_1->coa_1;
				$kategori_gudang = $coa_1->category;
				
				
				if(empty($rest_gudang_dari)){
					$Arr_Data	= array(
						'pesan'		=>'Stock gudang tidak ditemukan ...',
						'status'	=> 0
					);
					echo json_encode($Arr_Data);
					return false;
				}
				
				if(!empty($rest_gudang_dari)){
					$ArrStockDari['id'] 			= $rest_gudang_dari[0]->id;
					$ArrStockDari['qty_stock'] 		= $rest_gudang_dari[0]->qty_stock - $qty_oke;
					$ArrStockDari['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockDari['update_date'] 	= date('Y-m-d H:i:s');
					
					$ArrHistDari['id_material'] 	= $rest_gudang_dari[0]->id_material;
					$ArrHistDari['idmaterial'] 		= $rest_gudang_dari[0]->idmaterial;
					$ArrHistDari['nm_material'] 	= $rest_gudang_dari[0]->nm_material;
					$ArrHistDari['id_category'] 	= $rest_gudang_dari[0]->id_category;
					$ArrHistDari['nm_category'] 	= $rest_gudang_dari[0]->nm_category;
					$ArrHistDari['id_gudang'] 		= $id_gudang_ke;
					$ArrHistDari['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrHistDari['id_gudang_dari'] 	= NULL;
					$ArrHistDari['kd_gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
					$ArrHistDari['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHistDari['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrHistDari['qty_stock_awal'] 	= $rest_gudang_dari[0]->qty_stock;
					$ArrHistDari['qty_stock_akhir'] = $rest_gudang_dari[0]->qty_stock - $qty_oke;
					$ArrHistDari['qty_booking_awal'] 	= $rest_gudang_dari[0]->qty_booking;
					$ArrHistDari['qty_booking_akhir'] 	= $rest_gudang_dari[0]->qty_booking;
					$ArrHistDari['qty_rusak_awal'] 		= $rest_gudang_dari[0]->qty_rusak;
					$ArrHistDari['qty_rusak_akhir'] 	= $rest_gudang_dari[0]->qty_rusak;
					$ArrHistDari['no_ipp'] 				= $kode_trans;
					$ArrHistDari['jumlah_mat'] 			= $qty_oke;
					$ArrHistDari['ket'] 				= 'pengurangan gudang mutasi';
					$ArrHistDari['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistDari['update_date'] 		= date('Y-m-d H:i:s');
					
				$id_material = $rest_gudang_dari[0]->id_material;
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
								
				
				$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke , 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
				
				
				$ArrJurnalNew['id_material'] 		= $rest_gudang_dari[0]->id_material;
				$ArrJurnalNew['idmaterial'] 		= $rest_gudang_dari[0]->idmaterial;
				$ArrJurnalNew['nm_material'] 		= $rest_gudang_dari[0]->nm_material;
				$ArrJurnalNew['id_category'] 		= $rest_gudang_dari[0]->id_category;
				$ArrJurnalNew['nm_category'] 		= $rest_gudang_dari[0]->nm_category;
				$ArrJurnalNew['id_gudang'] 		= $id_gudang_ke;
				$ArrJurnalNew['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew['kd_gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
				$ArrJurnalNew['id_gudang_ke'] 	= $id_gudang_ke;
				$ArrJurnalNew['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew['qty_stock_awal'] 	= $stokjurnalakhir;
				$ArrJurnalNew['qty_stock_akhir'] 	= $stokjurnalakhir-$qty_oke;
				$ArrJurnalNew['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew['tgl_trans'] 		= $DateTime;
				$ArrJurnalNew['qty_out'] 			= $qty_oke;
				$ArrJurnalNew['ket'] 				= 'ADJUSTMENT '.strtoupper($adjustment_type);
				$ArrJurnalNew['harga'] 			= $PRICE * $qty_oke;
				$ArrJurnalNew['harga_bm'] 		= 0;
				$ArrJurnalNew['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew['nilai_trans_rp']	= $PRICE * $qty_oke;;
				$ArrJurnalNew['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE * $qty_oke);
				$ArrJurnalNew['update_by'] 		= $UserName;
				$ArrJurnalNew['update_date'] 		= $DateTime;
				$ArrJurnalNew['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew['coa_gudang'] 		= $coa_gudang;
				
					
					if($expired_date != NULL AND $expired_date != '' AND $expired_date != '0'){
						//Update Warehouse Expired Minus
						$stock_exp	= "SELECT a.* FROM  warehouse_stock_expired a WHERE a.id_material = '".$id_material."' AND a.id_gudang='".$id_gudang_ke."' AND a.expired='".$expired_date."'";
						$rest_exp	= $this->db->query($stock_exp)->result();
						//kurangi stock gudang dari
						if(!empty($rest_exp)){
							$ArrStockDariExp['id'] 			= $rest_exp[0]->id;
							$ArrStockDariExp['qty_stock'] 	= $rest_exp[0]->qty_stock - $qty_oke;
							$ArrStockDariExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrStockDariExp['update_date'] = date('Y-m-d H:i:s');
							
							$ArrHistDariExp['id_material'] 		= $rest_exp[0]->id_material;
							$ArrHistDariExp['nm_material'] 		= $rest_exp[0]->nm_material;;
							$ArrHistDariExp['id_gudang'] 		= $id_gudang_ke;
							$ArrHistDariExp['expired'] 			= $expired_date;
							$ArrHistDariExp['qty_stock'] 		= $rest_exp[0]->qty_stock;
							$ArrHistDariExp['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
							$ArrHistDariExp['qty_stock_akhir'] 	= $rest_exp[0]->qty_stock - $qty_oke;
							$ArrHistDariExp['qty_rusak_akhir'] 	= $rest_exp[0]->qty_rusak;
							$ArrHistDariExp['kode_trans'] 		= $kode_trans;
							$ArrHistDariExp['update_by'] 		= $data_session['ORI_User']['username'];
							$ArrHistDariExp['update_date'] 		= date('Y-m-d H:i:s');
						}
					}

				//jurnal minus
					$tgl_jurnal= date('Y-m-d');
					$Bln	= substr($tgl_jurnal,5,2);
					$Thn	= substr($tgl_jurnal,0,4);
					$coa_rl=$this->db->query("select * from ".DBACC.".master_oto_jurnal_detail where kode_master_jurnal='JV042' and parameter_no='1'")->row();
					$coa_gudang=$this->db->query("select * from warehouse where id='".$id_gudang_ke."' ")->row();
					$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_jurnal);
					$this->db->query("UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=nomorJC + 1  WHERE nocab='101'");
					$price_book=get_price_book($id_material);
					$nilai_jurnal=$price_book*$qty_oke;
					$keterangan_jurnal='ADJUSTMENT '.$nm_material;
					$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_jurnal, 'jml' => $nilai_jurnal, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan_jurnal, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $data_session['ORI_User']['username'], 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_jurnal, 'ho_valid' => '');
					$this->db->insert(DBACC.'.javh',$dataJVhead);
					$datadetail = array(
						'tipe'			=> 'JV',
						'nomor'			=> $Nomor_JV,
						'tanggal'		=> $tgl_jurnal,
						'no_perkiraan'	=> $coa_rl->no_perkiraan,
						'keterangan'	=> $keterangan_jurnal,
						'no_reff'		=> $kode_trans,
						'debet'			=> $nilai_jurnal,
						'kredit'		=> 0
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
					$datadetail = array(
						'tipe'			=> 'JV',
						'nomor'			=> $Nomor_JV,
						'tanggal'		=> $tgl_jurnal,
						'no_perkiraan'	=> $coa_gudang->coa_1,
						'keterangan'	=> $keterangan_jurnal,
						'no_reff'		=> $kode_trans,
						'debet'			=> 0,
						'kredit'		=> $nilai_jurnal
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				// end jurnal

				}
			}
			//PLUS
			if($adjustment_type == 'plus'){
				
				//penambahan gudang tujuan
				$check_gudang_ke	= "SELECT b.* FROM warehouse_stock b WHERE b.id_material = '".$id_material."' AND b.id_gudang='".$id_gudang_ke."' LIMIT 1";
				$rest_gudang_ke	= $this->db->query($check_gudang_ke)->result();
				
				$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_ke))->row();
				$coa_gudang = $coa_1->coa_1;
				$kategori_gudang = $coa_1->category;
				
				if(!empty($rest_gudang_ke)){
					$ArrStockKe['id'] 				= $rest_gudang_ke[0]->id;
					$ArrStockKe['qty_stock'] 		= $rest_gudang_ke[0]->qty_stock + $qty_oke;
					$ArrStockKe['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockKe['update_date'] 		= date('Y-m-d H:i:s');
					
					$ArrHistKe['id_material'] 		= $rest_gudang_ke[0]->id_material;
					$ArrHistKe['idmaterial'] 		= $rest_gudang_ke[0]->idmaterial;
					$ArrHistKe['nm_material'] 		= $rest_gudang_ke[0]->nm_material;
					$ArrHistKe['id_category'] 		= $rest_gudang_ke[0]->id_category;
					$ArrHistKe['nm_category'] 		= $rest_gudang_ke[0]->nm_category;
					$ArrHistKe['id_gudang'] 		= $id_gudang_ke;
					$ArrHistKe['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrHistKe['id_gudang_dari'] 	= $id_gudang_dari_m;
					$ArrHistKe['kd_gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
					$ArrHistKe['id_gudang_ke'] 		= $id_gudang_ke;
					$ArrHistKe['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrHistKe['qty_stock_awal'] 	= $rest_gudang_ke[0]->qty_stock;
					$ArrHistKe['qty_stock_akhir'] 	= $rest_gudang_ke[0]->qty_stock + $qty_oke;
					$ArrHistKe['qty_booking_awal'] 	= $rest_gudang_ke[0]->qty_booking;
					$ArrHistKe['qty_booking_akhir'] = $rest_gudang_ke[0]->qty_booking;
					$ArrHistKe['qty_rusak_awal'] 	= $rest_gudang_ke[0]->qty_rusak;
					$ArrHistKe['qty_rusak_akhir'] 	= $rest_gudang_ke[0]->qty_rusak;
					$ArrHistKe['no_ipp'] 			= $kode_trans;
					$ArrHistKe['jumlah_mat'] 		= $qty_oke;
					$ArrHistKe['ket'] 				= 'penambahan gudang mutasi';
					$ArrHistKe['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHistKe['update_date'] 		= date('Y-m-d H:i:s');
					
				$id_material = $rest_gudang_ke[0]->id_material;
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
								
				
				$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
				
				
				$ArrJurnalNew['id_material'] 		= $rest_gudang_ke[0]->id_material;
				$ArrJurnalNew['idmaterial'] 		= $rest_gudang_ke[0]->idmaterial;
				$ArrJurnalNew['nm_material'] 		= $rest_gudang_ke[0]->nm_material;
				$ArrJurnalNew['id_category'] 		= $rest_gudang_ke[0]->id_category;
				$ArrJurnalNew['nm_category'] 		= $rest_gudang_ke[0]->nm_category;
				$ArrJurnalNew['id_gudang'] 		= $id_gudang_ke;
				$ArrJurnalNew['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew['kd_gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
				$ArrJurnalNew['id_gudang_ke'] 	= $id_gudang_ke;
				$ArrJurnalNew['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew['qty_stock_awal'] 	= $stokjurnalakhir;
				$ArrJurnalNew['qty_stock_akhir'] 	= $qty_oke+$stokjurnalakhir;
				$ArrJurnalNew['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew['tgl_trans'] 		= $DateTime;
				$ArrJurnalNew['qty_in'] 			= $qty_oke;
				$ArrJurnalNew['ket'] 				= 'ADJUSTMENT '.strtoupper($adjustment_type);
				$ArrJurnalNew['harga'] 			= $PRICE;
				$ArrJurnalNew['harga_bm'] 		= 0;
				$ArrJurnalNew['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew['nilai_trans_rp']	= $PRICE * $qty_oke;;
				$ArrJurnalNew['nilai_akhir_rp']	= $nilaijurnalakhir+($PRICE * $qty_oke);
				$ArrJurnalNew['update_by'] 		= $UserName;
				$ArrJurnalNew['update_date'] 		= $DateTime;
				$ArrJurnalNew['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew['coa_gudang'] 		= $coa_gudang;
				
					
					if($expired_date != NULL AND $expired_date != '' AND $expired_date != '0'){
						//Update Warehouse Expired Minus
						$stock_exp	= "SELECT a.* FROM  warehouse_stock_expired a WHERE a.id_material = '".$id_material."' AND a.id_gudang='".$id_gudang_ke."' AND a.expired='".$expired_date."'";
						$rest_exp	= $this->db->query($stock_exp)->result();
						//kurangi stock gudang dari
						if(!empty($rest_exp)){
							$ArrStockKeExp['id'] 			= $rest_exp[0]->id;
							$ArrStockKeExp['qty_stock'] 	= $rest_exp[0]->qty_stock + $qty_oke;
							$ArrStockKeExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrStockKeExp['update_date'] 	= date('Y-m-d H:i:s');
							
							$ArrHistKeExp['id_material'] 	= $rest_exp[0]->id_material;
							$ArrHistKeExp['nm_material'] 	= $rest_exp[0]->nm_material;;
							$ArrHistKeExp['id_gudang'] 		= $id_gudang_ke;
							$ArrHistKeExp['expired'] 		= $expired_date;
							$ArrHistKeExp['qty_stock'] 		= $rest_exp[0]->qty_stock;
							$ArrHistKeExp['qty_rusak'] 		= $rest_exp[0]->qty_rusak;
							$ArrHistKeExp['qty_stock_akhir'] 	= $rest_exp[0]->qty_stock + $qty_oke;
							$ArrHistKeExp['qty_rusak_akhir'] 	= $rest_exp[0]->qty_rusak;
							$ArrHistKeExp['kode_trans'] 		= $kode_trans;
							$ArrHistKeExp['update_by'] 			= $data_session['ORI_User']['username'];
							$ArrHistKeExp['update_date'] 		= date('Y-m-d H:i:s');
						}
						$sql_mat	= "SELECT a.* FROM raw_materials a WHERE a.id_material = '".$id_material."' LIMIT 1 ";
						$rest_mat	= $this->db->query($sql_mat)->result();
						if(empty($rest_exp)){
							$ArrPlusExp['id_material'] 	= $rest_mat[0]->id_material;
							$ArrPlusExp['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrPlusExp['id_gudang'] 	= $id_gudang_ke;
							$ArrPlusExp['expired'] 		= $expired_date;
							$ArrPlusExp['qty_stock'] 	= $qty_oke;
							$ArrPlusExp['qty_rusak'] 	= 0;
							$ArrPlusExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrPlusExp['update_date'] 	= date('Y-m-d H:i:s');
							
							$ArrPlusHistExp['id_material'] 	= $rest_mat[0]->id_material;
							$ArrPlusHistExp['nm_material'] 	= $rest_mat[0]->nm_material;;
							$ArrPlusHistExp['id_gudang'] 	= $id_gudang_ke;
							$ArrPlusHistExp['expired'] 		= $expired_date;
							$ArrPlusHistExp['qty_stock'] 	= 0;
							$ArrPlusHistExp['qty_rusak'] 	= 0;
							$ArrPlusHistExp['qty_stock_akhir'] 	= $qty_oke;
							$ArrPlusHistExp['qty_rusak_akhir'] 	= 0;
							$ArrPlusHistExp['kode_trans'] 	= $kode_trans;
							$ArrPlusHistExp['update_by'] 	= $data_session['ORI_User']['username'];
							$ArrPlusHistExp['update_date'] 	= date('Y-m-d H:i:s');
						}
					}
				}
				
				if(empty($rest_gudang_ke)){
					$sql_mat	= "SELECT a.* FROM raw_materials a WHERE a.id_material = '".$id_material."' LIMIT 1 ";
					$rest_mat	= $this->db->query($sql_mat)->result();
					
					$ArrPlus['id_material'] 	= $rest_mat[0]->id_material;
					$ArrPlus['idmaterial'] 		= $rest_mat[0]->idmaterial;
					$ArrPlus['nm_material'] 	= $rest_mat[0]->nm_material;
					$ArrPlus['id_category'] 	= $rest_mat[0]->id_category;
					$ArrPlus['nm_category'] 	= $rest_mat[0]->nm_category;
					$ArrPlus['id_gudang'] 		= $id_gudang_ke;
					$ArrPlus['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrPlus['qty_stock'] 		= $qty_oke;
					$ArrPlus['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrPlus['update_date'] 	= date('Y-m-d H:i:s');
					
					$ArrPlusHist['id_material'] 	= $rest_mat[0]->id_material;
					$ArrPlusHist['idmaterial'] 		= $rest_mat[0]->idmaterial;
					$ArrPlusHist['nm_material'] 	= $rest_mat[0]->nm_material;
					$ArrPlusHist['id_category'] 	= $rest_mat[0]->id_category;
					$ArrPlusHist['nm_category'] 	= $rest_mat[0]->nm_category;
					$ArrPlusHist['id_gudang'] 		= $id_gudang_ke;
					$ArrPlusHist['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrPlusHist['id_gudang_dari'] 	= NULL;
					$ArrPlusHist['kd_gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
					$ArrPlusHist['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrPlusHist['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrPlusHist['qty_stock_awal'] 	= 0;
					$ArrPlusHist['qty_stock_akhir'] 	= $qty_oke;
					$ArrPlusHist['qty_booking_awal'] 	= 0;
					$ArrPlusHist['qty_booking_akhir'] 	= 0;
					$ArrPlusHist['qty_rusak_awal'] 		= 0;
					$ArrPlusHist['qty_rusak_akhir'] 	= 0;
					$ArrPlusHist['no_ipp'] 				= $kode_trans;
					$ArrPlusHist['jumlah_mat'] 			= $qty_oke;
					$ArrPlusHist['ket'] 				= 'penambahan gudang mutasi (insert new)';
					$ArrPlusHist['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrPlusHist['update_date'] 		= date('Y-m-d H:i:s');
					
					
					$id_material = $rest_mat[0]->id_material;
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
								
				
				$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
				
				
				$ArrJurnalNew['id_material'] 		= $rest_mat[0]->id_material;
				$ArrJurnalNew['idmaterial'] 		= $rest_mat[0]->idmaterial;
				$ArrJurnalNew['nm_material'] 		= $rest_mat[0]->nm_material;
				$ArrJurnalNew['id_category'] 		= $rest_mat[0]->id_category;
				$ArrJurnalNew['nm_category'] 		= $rest_mat[0]->nm_category;
				$ArrJurnalNew['id_gudang'] 		= $id_gudang_ke;
				$ArrJurnalNew['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew['kd_gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
				$ArrJurnalNew['id_gudang_ke'] 	= $id_gudang_ke;
				$ArrJurnalNew['kd_gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew['qty_stock_awal'] 	= $stokjurnalakhir;
				$ArrJurnalNew['qty_stock_akhir'] 	= $qty_oke+$stokjurnalakhir;
				$ArrJurnalNew['kode_trans'] 		= $kode_trans;
				$ArrJurnalNew['tgl_trans'] 		= $DateTime;
				$ArrJurnalNew['qty_in'] 			= $qty_oke;
				$ArrJurnalNew['ket'] 				= 'ADJUSTMENT '.strtoupper($adjustment_type);
				$ArrJurnalNew['harga'] 			= $PRICE;
				$ArrJurnalNew['harga_bm'] 		= 0;
				$ArrJurnalNew['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew['nilai_trans_rp']	= $PRICE * $qty_oke;;
				$ArrJurnalNew['nilai_akhir_rp']	= $nilaijurnalakhir+($PRICE * $qty_oke);
				$ArrJurnalNew['update_by'] 		= $UserName;
				$ArrJurnalNew['update_date'] 		= $DateTime;
				$ArrJurnalNew['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew['coa_gudang'] 		= $coa_gudang;
				
				
					
					if($expired_date != NULL AND $expired_date != '' AND $expired_date != '0'){
						$ArrPlusExp['id_material'] 	= $rest_mat[0]->id_material;
						$ArrPlusExp['nm_material'] 	= $rest_mat[0]->nm_material;;
						$ArrPlusExp['id_gudang'] 	= $id_gudang_ke;
						$ArrPlusExp['expired'] 		= $expired_date;
						$ArrPlusExp['qty_stock'] 	= $qty_oke;
						$ArrPlusExp['qty_rusak'] 	= 0;
						$ArrPlusExp['update_by'] 	= $data_session['ORI_User']['username'];
						$ArrPlusExp['update_date'] 	= date('Y-m-d H:i:s');
						
						$ArrPlusHistExp['id_material'] 	= $rest_mat[0]->id_material;
						$ArrPlusHistExp['nm_material'] 	= $rest_mat[0]->nm_material;;
						$ArrPlusHistExp['id_gudang'] 	= $id_gudang_ke;
						$ArrPlusHistExp['expired'] 		= $expired_date;
						$ArrPlusHistExp['qty_stock'] 	= 0;
						$ArrPlusHistExp['qty_rusak'] 	= 0;
						$ArrPlusHistExp['qty_stock_akhir'] 	= $qty_oke;
						$ArrPlusHistExp['qty_rusak_akhir'] 	= 0;
						$ArrPlusHistExp['kode_trans'] 	= $kode_trans;
						$ArrPlusHistExp['update_by'] 	= $data_session['ORI_User']['username'];
						$ArrPlusHistExp['update_date'] 	= date('Y-m-d H:i:s');
					}
					
					
					
				
				
				}
				
				
				
				//jurnal plus
					$tgl_jurnal= date('Y-m-d');
					$Bln	= substr($tgl_jurnal,5,2);
					$Thn	= substr($tgl_jurnal,0,4);
					$coa_rl=$this->db->query("select * from ".DBACC.".master_oto_jurnal_detail where kode_master_jurnal='JV042' and parameter_no='1'")->row();
					$coa_gudang=$this->db->query("select * from warehouse where id='".$id_gudang_ke."' ")->row();
					$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_jurnal);
					$this->db->query("UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=nomorJC + 1  WHERE nocab='101'");
					$price_book=get_price_book($id_material);
					$nilai_jurnal=$price_book*$qty_oke;
					$keterangan_jurnal='ADJUSTMENT '.$nm_material;
					$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_jurnal, 'jml' => $nilai_jurnal, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan_jurnal, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $data_session['ORI_User']['username'], 'memo' => $kode_trans, 'tgl_jvkoreksi' => $tgl_jurnal, 'ho_valid' => '');
					$this->db->insert(DBACC.'.javh',$dataJVhead);
					$datadetail = array(
						'tipe'			=> 'JV',
						'nomor'			=> $Nomor_JV,
						'tanggal'		=> $tgl_jurnal,
						'no_perkiraan'	=> $coa_gudang->coa_1,
						'keterangan'	=> $keterangan_jurnal,
						'no_reff'		=> $kode_trans,
						'debet'			=> $nilai_jurnal,
						'kredit'		=> 0
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
					$datadetail = array(
						'tipe'			=> 'JV',
						'nomor'			=> $Nomor_JV,
						'tanggal'		=> $tgl_jurnal,
						'no_perkiraan'	=> $coa_rl->no_perkiraan,
						'keterangan'	=> $keterangan_jurnal,
						'no_reff'		=> $kode_trans,
						'debet'			=> 0,
						'kredit'		=> $nilai_jurnal
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				// end jurnal
				
			}
			
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// print_r($ArrStockDari);
			// print_r($ArrHistDari);
			// print_r($ArrStockKe);
			// print_r($ArrHistKe);
			// print_r($ArrPlus);
			// print_r($ArrPlusHist);
			
			// print_r($ArrStockDariExp);
			// print_r($ArrHistDariExp);
			// print_r($ArrStockKeExp);
			// print_r($ArrHistKeExp);
			// print_r($ArrPlusExp);
			// print_r($ArrPlusHistExp);
			// exit;
			
				$this->db->insert('warehouse_adjustment', $ArrHeader);
				$this->db->insert('warehouse_adjustment_detail', $ArrDetail);
				
				 $this->db->insert('tran_warehouse_jurnal_detail', $ArrJurnalNew);
				 
				 if(!empty($ArrJurnalNew2)){
					 $this->db->insert('tran_warehouse_jurnal_detail', $ArrJurnalNew2);
				}
			
				
				if(!empty($ArrStockDari)){
					$this->db->where('id',$rest_gudang_dari[0]->id);
					$this->db->update('warehouse_stock', $ArrStockDari);
				}
				if(!empty($ArrHistDari)){
					$this->db->insert('warehouse_history', $ArrHistDari);
				}
				if(!empty($ArrStockKe)){
					$this->db->where('id',$rest_gudang_ke[0]->id);
					$this->db->update('warehouse_stock', $ArrStockKe);
				}
				if(!empty($ArrHistKe)){
					$this->db->insert('warehouse_history', $ArrHistKe);
				}
				if(!empty($ArrPlus)){
					$this->db->insert('warehouse_stock', $ArrPlus);
				}
				if(!empty($ArrPlusHist)){
					$this->db->insert('warehouse_history', $ArrPlusHist);
				}
				
				//EXPAIRED
				if(!empty($ArrStockDariExp)){
					$this->db->where('id',$rest_exp[0]->id);
					$this->db->update('warehouse_stock_expired', $ArrStockDariExp);
				}
				if(!empty($ArrHistDariExp)){
					$this->db->insert('warehouse_stock_expired_hist', $ArrHistDariExp);
				}
				if(!empty($ArrStockKeExp)){
					$this->db->where('id',$rest_exp[0]->id);
					$this->db->update('warehouse_stock_expired', $ArrStockKeExp);
				}
				if(!empty($ArrHistKeExp)){
					$this->db->insert('warehouse_stock_expired_hist', $ArrHistKeExp);
				}
				if(!empty($ArrPlusExp)){
					$this->db->insert('warehouse_stock_expired', $ArrPlusExp);
				}
				if(!empty($ArrPlusHistExp)){
					$this->db->insert('warehouse_stock_expired_hist', $ArrPlusHistExp);
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

				$ArrUpdateStock[0]['id'] 	= $id_material;
				$ArrUpdateStock[0]['qty'] 	= $qty_oke;
				if($adjustment_type == 'mutasi'){
					insertDataGroupReport($ArrUpdateStock, $id_gudang_dari_m, $id_gudang_ke_m, $kode_trans, null, null, null);
				}
				if($adjustment_type == 'minus'){
					insertDataGroupReport($ArrUpdateStock, $id_gudang_ke, null, $kode_trans, null, null, null);
				}
				if($adjustment_type == 'plus'){
					insertDataGroupReport($ArrUpdateStock, null, $id_gudang_ke, $kode_trans, null, null, null);
				}
				history("Adjustment material ".$adjustment_type." : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/adjustment';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['read'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			
			$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
			$gudang = $this->db->query("SELECT * FROM warehouse WHERE status='Y'")->result_array();
			
			$data = array(
				'title'			=> 'Indeks Of Add Adjustment Material',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'gudang'		=> $gudang
			);
			$this->load->view('Adjustment_material/add_adjustment',$data);
		}
	}
	
	public function excel_adjustment(){
		$type		= $this->uri->segment(3);
		$material	= $this->uri->segment(4);
  		//membuat objek PHPExcel
  		set_time_limit(0);
  		ini_set('memory_limit','1024M');

  		$this->load->library("PHPExcel");
  		// $this->load->library("PHPExcel/Writer/Excel2007");
  		$objPHPExcel	= new PHPExcel();

  		$style_header = array(
  			'borders' => array(
  				'allborders' => array(
  					  'style' => PHPExcel_Style_Border::BORDER_THIN,
  					  'color' => array('rgb'=>'000000')
  				  )
  			),
  			'fill' => array(
  				'type' => PHPExcel_Style_Fill::FILL_SOLID,
  				'color' => array('rgb'=>'e0e0e0'),
  			),
  			'font' => array(
  				'bold' => true,
  			),
  			'alignment' => array(
  				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
  				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
  			)
  		);

  		$style_header2 = array(
  			'fill' => array(
  				'type' => PHPExcel_Style_Fill::FILL_SOLID,
  				'color' => array('rgb'=>'e0e0e0'),
  			),
  			'font' => array(
  				'bold' => true,
  			),
  			'alignment' => array(
  				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
  				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
  			)
  		);

  		$styleArray = array(
  			  'alignment' => array(
  				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
  			  ),
  			  'borders' => array(
  				'allborders' => array(
  					  'style' => PHPExcel_Style_Border::BORDER_THIN,
  					  'color' => array('rgb'=>'000000')
  				  )
  			)
  		  );
  		$styleArray3 = array(
  			  'alignment' => array(
  				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
  			  ),
  			  'borders' => array(
  				'allborders' => array(
  					  'style' => PHPExcel_Style_Border::BORDER_THIN,
  					  'color' => array('rgb'=>'000000')
  				  )
  			)
  		  );
  		 $styleArray4 = array(
  			  'alignment' => array(
  				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT

  			  ),
  			  'borders' => array(
  				'allborders' => array(
  					  'style' => PHPExcel_Style_Border::BORDER_THIN,
  					  'color' => array('rgb'=>'000000')
  				  )
  			)
  		  );
  	    $styleArray1 = array(
  			  'borders' => array(
  				  'allborders' => array(
  					  'style' => PHPExcel_Style_Border::BORDER_THIN
  				  )
  			  ),
  			  'alignment' => array(
  				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
  				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
  			  )
  		  );
  		$styleArray2 = array(
  			  'borders' => array(
  				  'allborders' => array(
  					  'style' => PHPExcel_Style_Border::BORDER_THIN
  				  )
  			  ),
  			  'alignment' => array(
  				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
  				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
  			  )
  		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

        $where = "";
		if($type <> '0'){
			$where = " AND a.adjustment_type='".$type."' ";
		}
		
		$whereMaterial = "";
		if($material <> '0'){
			$whereMaterial = " AND b.id_material='".$material."' ";
		}
		
		$sql = "
				SELECT
					a.*,
					b.no_ba,
					b.nm_material,
					b.expired_date
				FROM
					warehouse_adjustment a LEFT JOIN warehouse_adjustment_detail b ON a.kode_trans=b.kode_trans
				WHERE 1=1 AND a.category = 'adjustment material' AND a.status_id='1' ".$where." ".$whereMaterial."
			";
  		$detail    = $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(13);
		$sheet->setCellValue('A'.$Row, 'LIST ADJUSTMENT');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		if($type <> '0'){
        $NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'ADJUSTMENT TYPE');
		$sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

        $sheet->setCellValue('C'.$NewRow, strtoupper($type));
		$sheet->getStyle('C'.$NewRow.':C'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':C'.$NewRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		}
		
		if($material <> '0'){
        $NewRow	= $NewRow +1;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'MATERIAL NAME');
		$sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

        $sheet->setCellValue('C'.$NewRow, strtoupper(get_name('raw_materials','nm_material','id_material',$material)));
		$sheet->getStyle('C'.$NewRow.':C'.$NewRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C'.$NewRow.':C'.$NewRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		}
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('B'.$NewRow, 'No Trans');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Type');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

        $sheet->setCellValue('D'.$NewRow, 'Gudang Dari');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Gudang Ke');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Material Name');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Qty');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Expired');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'PIC');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'No BA');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Ket');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Created');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Created Date');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
		$sheet->getColumnDimension('M')->setAutoSize(true); 
		
		if($detail){
  			$awal_row	= $NextRow;
  			$no=0;
  			foreach($detail as $key => $row){
  				$no++;
  				$awal_row++;
  				$awal_col	= 0;

  				$awal_col++;
  				$nomor	= $no;
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $nomor);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

  				$awal_col++;
  				$status_date	= $row['kode_trans'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= strtoupper($row['adjustment_type']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$gudang_dari 	= (!empty($row['id_gudang_dari']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_dari']):$row['kd_gudang_dari']." ".strtoupper($row['adjustment_type']);
				$gudang_ke 		= (!empty($row['id_gudang_ke']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']):$row['kd_gudang_ke']." ".strtoupper($row['adjustment_type']);
		
		
				$awal_col++;
  				$status_date	= $gudang_dari;
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= $gudang_ke;
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= strtoupper($row['nm_material']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= number_format($row['jumlah_mat'],2);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$expired 		= ($row['expired_date'] != '0000-00-00' AND $row['expired_date'] != NULL)?date('d-M-Y', strtotime($row['expired_date'])):'-';
		
				$awal_col++;
  				$status_date	= $expired;
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= strtoupper($row['pic']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= strtoupper($row['no_ba']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= strtoupper($row['note']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= $row['created_by'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
  				$status_date	= date('d-M-Y H:i:s', strtotime($row['created_date']));
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

          

  			}
  		}

  		


  		$sheet->setTitle('List Adjustment Material');
  		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
  		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  		ob_end_clean();
  		//sesuaikan headernya
  		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  		header("Cache-Control: no-store, no-cache, must-revalidate");
  		header("Cache-Control: post-check=0, pre-check=0", false);
  		header("Pragma: no-cache");
  		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  		//ubah nama file saat diunduh
  		header('Content-Disposition: attachment;filename="Adjustment Material '.date('YmdHis').'.xls"');
  		//unduh file
  		$objWriter->save("php://output");
  	}
}
?>