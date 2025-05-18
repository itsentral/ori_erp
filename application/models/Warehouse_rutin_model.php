<?php
class Warehouse_rutin_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function incoming(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');

		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='indirect' ORDER BY urut ASC")->result_array();
		$CATEGORY = '1,6,7,8,11';
		$SQL_OLD = "(SELECT a.no_po, 'PO' AS typ, a.nm_supplier AS ket_name, a.created_by FROM tran_po_header a WHERE a.category='rutin' AND (a.status='WAITING IN' OR a.status='IN PARSIAL') AND a.status_id='1' ORDER BY a.no_po ASC)";
		$SQL_NEW = "(SELECT
						a.no_po,
						'PO' AS typ,
						c.nm_supplier AS ket_name,
						c.created_by
					FROM
						tran_po_detail a
						LEFT JOIN tran_po_header c ON a.no_po = c.no_po
						LEFT JOIN con_nonmat_new b ON a.id_barang = b.code_group 
						LEFT JOIN tran_rfq_detail x ON a.no_po=x.no_po AND a.id_barang=x.id_barang
						LEFT JOIN tran_pr_detail y ON x.no_rfq=y.no_rfq AND x.id_barang=y.id_barang
					WHERE
						a.qty_in < a.qty_po 
						-- AND c.category = 'rutin' 
						AND a.id_barang LIKE 'CN%'
						AND c.status_id = '1'
						AND b.category_awal IN ($CATEGORY)
						AND (y.in_gudang != 'project' OR y.in_gudang IS NULL)
					GROUP BY 
						a.no_po, b.category_awal 
					ORDER BY 
						a.no_po)";

		$no_po				= $this->db->query($SQL_NEW."UNION
												(SELECT b.no_non_po AS no_po, 'NON-PO' AS typ, b.pic AS ket_name, b.created_by FROM tran_non_po_header b WHERE b.category='rutin' AND (b.status='WAITING IN' OR b.status='IN PARSIAL') ORDER BY b.no_non_po ASC)")->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'incoming rutin'))->result_array();
		$data_gudang= $this->db->group_by('id_gudang_ke')->get_where('warehouse_adjustment',array('category'=>'incoming rutin'))->result_array();
										
		$data = array( 
			'title'			=> 'Warehouse Stok >> Incoming Indirect',
			'action'		=> 'index',
			'uri_back' 		=> strtolower($this->uri->segment(2)),
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'pusat'			=> $pusat,
			'list_po'		=> $list_po,
			'data_gudang'	=> $data_gudang,
			'no_po'			=> $no_po,
			'category'		=> $CATEGORY,
			'GET_USER' => get_detail_user()
		);
		history('View incoming indirect');
		$this->load->view('Warehouse_rutin/incoming',$data);
	}

	public function incoming_consumable(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');

		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='consumable' ORDER BY urut ASC")->result_array();
		$CATEGORY = '1,6,7,8,11';
		$SQL_OLD = "(SELECT a.no_po, 'PO' AS typ, a.nm_supplier AS ket_name, a.created_by FROM tran_po_header a WHERE a.category='rutin' AND (a.status='WAITING IN' OR a.status='IN PARSIAL') AND a.status_id='1' ORDER BY a.no_po ASC)";
		$SQL_NEW = "(SELECT
						a.no_po,
						'PO' AS typ,
						c.nm_supplier AS ket_name,
						c.created_by
					FROM
						tran_po_detail a
						LEFT JOIN tran_po_header c ON a.no_po = c.no_po
						LEFT JOIN con_nonmat_new b ON a.id_barang = b.code_group 
					WHERE
						a.qty_in < a.qty_po 
						-- AND c.category = 'rutin' 
						AND a.id_barang LIKE 'CN%'
						AND c.status_id = '1'
						AND b.category_awal IN ($CATEGORY)
					GROUP BY 
						a.no_po, b.category_awal 
					ORDER BY 
						a.no_po)";

		$no_po				= $this->db->query($SQL_NEW."UNION
												(SELECT b.no_non_po AS no_po, 'NON-PO' AS typ, b.pic AS ket_name, b.created_by FROM tran_non_po_header b WHERE b.category='rutin' AND (b.status='WAITING IN' OR b.status='IN PARSIAL') ORDER BY b.no_non_po ASC)")->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'incoming rutin'))->result_array();
		$data_gudang= $this->db->group_by('id_gudang_ke')->get_where('warehouse_adjustment',array('category'=>'incoming rutin'))->result_array();
										
		$data = array( 
			'title'			=> 'Warehouse Stok >> Incoming Consumable',
			'action'		=> 'index',
			'uri_back' 		=> strtolower($this->uri->segment(2)),
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'pusat'			=> $pusat,
			'list_po'		=> $list_po,
			'data_gudang'	=> $data_gudang,
			'no_po'			=> $no_po,
			'category'		=> $CATEGORY,
			'GET_USER' => get_detail_user()
		);
		history('View incoming consumable'); 
		$this->load->view('Warehouse_rutin/incoming',$data);
	}

	public function incoming_household(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');

		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='household' ORDER BY urut ASC")->result_array();
		$CATEGORY = '2,10';
		$SQL_OLD = "(SELECT a.no_po, 'PO' AS typ, a.nm_supplier AS ket_name, a.created_by FROM tran_po_header a WHERE a.category='rutin' AND (a.status='WAITING IN' OR a.status='IN PARSIAL') AND a.status_id='1' ORDER BY a.no_po ASC)";
		$SQL_NEW = "(SELECT
						a.no_po,
						'PO' AS typ,
						c.nm_supplier AS ket_name,
						c.created_by
					FROM
						tran_po_detail a
						LEFT JOIN tran_po_header c ON a.no_po = c.no_po
						LEFT JOIN con_nonmat_new b ON a.id_barang = b.code_group 
					WHERE
						a.qty_in < a.qty_po 
						-- AND c.category = 'rutin' 
						AND a.id_barang LIKE 'CN%'
						AND c.status_id = '1'
						AND b.category_awal IN ($CATEGORY)
					GROUP BY 
						a.no_po, b.category_awal 
					ORDER BY 
						a.no_po)";

		$no_po				= $this->db->query($SQL_NEW."UNION
												(SELECT b.no_non_po AS no_po, 'NON-PO' AS typ, b.pic AS ket_name, b.created_by FROM tran_non_po_header b WHERE b.category='rutin' AND (b.status='WAITING IN' OR b.status='IN PARSIAL') ORDER BY b.no_non_po ASC)")->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'incoming rutin'))->result_array();
		$data_gudang= $this->db->group_by('id_gudang_ke')->get_where('warehouse_adjustment',array('category'=>'incoming rutin'))->result_array();
										
		$data = array( 
			'title'			=> 'Warehouse Stok >> Incoming Household',
			'action'		=> 'index',
			'uri_back' 		=> strtolower($this->uri->segment(2)),
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'pusat'			=> $pusat,
			'list_po'		=> $list_po,
			'data_gudang'	=> $data_gudang,
			'no_po'			=> $no_po,
			'category'		=> $CATEGORY,
			'GET_USER' => get_detail_user()
		);
		history('View incoming household'); 
		$this->load->view('Warehouse_rutin/incoming',$data);
	}
	
	public function get_data_json_incoming(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_incoming(
			$requestData['no_po'],
			$requestData['gudang'],
			$requestData['category'],
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
		$GET_USERNAME = get_detail_user();
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

			$TGL_INCOMING = (!empty($row['tanggal']))?$row['tanggal']:$row['created_date'];
			$NM_USER = (!empty($GET_USERNAME[$row['created_by']]['nm_lengkap']))?$GET_USERNAME[$row['created_by']]['nm_lengkap']:$row['created_by'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div>".$row['no_ipp']."/".$row['kode_trans']."</div>"; 
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($TGL_INCOMING))."</div>";
			$nestedData[]	= "<div>".strtoupper(get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'])."</div>";
			$nestedData[]	= "<div>".strtoupper($row['pic'])."</div>";
			$nestedData[]	= "<div>".strtoupper($NM_USER)."</div>";
			$nestedData[]	= "<div>".$row['nm_supplier']."</div>";
			//$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
				$plus	= "";
				
				$print	= "&nbsp;<a href='".base_url('warehouse_rutin/print_incoming_check/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Incoming'><i class='fa fa-print'></i></a>";
				// if($row['checked'] == 'N'){
					// $plus	= "&nbsp;<button type='button' class='btn btn-sm btn-info check' title='Check Incoming' data-no_ipp='".$row['no_ipp']."' data-users='".str_replace(' ','sp4si', $row['created_by'])."' data-tanggal='".str_replace(' ','sp4si', $row['created_date'])."'><i class='fa fa-check'></i></button>"; 
				// }

			$nestedData[]	= "<div align='center'>
									<button type='button' class='btn btn-sm btn-primary detailAjust' title='View Incoming' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-eye'></i></button>
                                    ".$print."
									".$plus."
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

	public function query_data_json_incoming($no_po, $gudang, $category, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_no_po ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}
		
		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang_ke = '".$gudang."' ";
		}

		$where_category = " AND a.id_gudang_ke = '".$category."' ";
		
		$sql = "
			SELECT
				a.*, b.nm_supplier
			FROM
				warehouse_adjustment a
				left join 
				(
				SELECT no_po, nm_supplier FROM tran_po_header WHERE category='rutin'
				UNION
				SELECT no_non_po AS no_po, pic AS nm_supplier FROM tran_non_po_header WHERE category='rutin'
				) b on a.no_ipp=b.no_po
		    WHERE 1=1 AND a.category = 'incoming rutin' AND a.status_id='1'
				".$where_no_po."
				".$where_gudang."
				".$where_category."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function process_incoming(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$no_po			= $data['no_po'];
		$no_ros			= $data['no_ros'];
		$gudang			= $data['gudang'];
		$pic			= $data['pic'];
		$note			= $data['note'];
		$tanggal_trans	= $data['tanggal_trans'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		// $note		= strtolower($data['note']);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];
		$Ym 			= date('ym'); 
		// echo $no_po;
		// print_r($addInMat);
		// exit;
		if($adjustment == 'IN'){
			$histHlp = "Adjustment incoming rutin to ".$nm_gudang_ke." / ".$no_po;
			
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRN".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRN".$Ym.$urut2;
		
			$ArrUpdate		= array();
			$ArrInList		= array();
			$ArrDeatil		= array();
			$ArrDeatilAdj	= array();
			$ArrHist		= array();
			$ArrDeatilChk	= array();
			$ArrStock		= array();
			$ArrHist		= array();
			$ArrStockNew	= array();
			$ArrHistNew		= array();
			
			$CoaMaterial	= array();
			$ArrUpdateStock	= array();

			$SumMat = 0;
			$SumRisk = 0;

			// jurnal
			$jenis_jurnal = 'JV035';
			$nomor_jurnal = $jenis_jurnal . $no_ros . rand(100, 999);
			$det_Jurnaltes1 = array();
			$total_forward_bef_ppn=0;
			$total_forward_ppn=0;
			$total_harga_product=0;
			$total_harga_product_usd=0;
			$kurs_ros=1;
			$payment_date=date('Y-m-d');
			if($no_ros!=''){
				$data_ros = $this->db->query("SELECT * FROM report_of_shipment WHERE id='$no_ros' ")->row();
				$kurs_ros = $data_ros->freight_curs;
				$data_ros_forward = $this->db->query("SELECT * FROM report_of_shipment_forward WHERE id_ros='$no_ros' ")->result();
				if(!empty($data_ros_forward)){
					foreach ($data_ros_forward as $keys) {
						$total_forward_bef_ppn=($total_forward_bef_ppn+$keys->cost);
						$total_forward_ppn=($total_forward_ppn+$keys->ppn);
					}
				}
			}
			foreach($addInMat AS $val => $valx){
				$qtyIN 		= str_replace(',','',$valx['qty_in']);
				$qtyRISK 	= 0;
				
				$SumMat 	+= $qtyIN;
				$SumRisk 	+= $qtyRISK;

				$sqlWhDetail	= "	SELECT
									a.*,
									b.id AS id2,
									c.code_group,
									c.category_awal,
									c.category_code,
									c.material_name,
									b.gudang,
									b.stock,
									b.rusak,
									c.coa
								FROM
									tran_po_detail a
									LEFT JOIN (select * from warehouse_rutin_stock where gudang='".$gudang."') b
										ON a.id_barang = b.code_group
									left join(select x.*,y.coa FROM con_nonmat_new x left join con_nonmat_category_awal y on x.category_awal=y.id) c on a.id_barang=c.code_group
								WHERE
									a.id = '".$valx['id']."'
									
								";
				$restWhDetail	= $this->db->query($sqlWhDetail)->result();


				//update detail purchase
				$ArrUpdate[$val]['id'] 			= $valx['id'];
				$ArrUpdate[$val]['qty_in'] 		= $restWhDetail[0]->qty_in + $qtyIN;
				
				$ArrUpdateStock[$val]['id'] 		= $restWhDetail[0]->code_group;
				$ArrUpdateStock[$val]['qty_good'] 	= $qtyIN;
				$ArrUpdateStock[$val]['unit_price'] = $restWhDetail[0]->net_price * $kurs_ros;

				//detail adjustmeny
				$ArrDeatilAdj[$val]['no_ipp'] 			= $no_po;
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
				$ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->code_group;
				$ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->material_name;
				$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->category_awal;
				$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->category_code;
				
				$ArrDeatilAdj[$val]['qty_order'] 		= str_replace(',','',$valx['qty_order']);
				$ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
				$ArrDeatilAdj[$val]['qty_rusak'] 		= $qtyRISK;
				$ArrDeatilAdj[$val]['expired_date'] 	= NULL;
				$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
				
				$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrDeatilAdj[$val]['update_date'] 		= date('Y-m-d H:i:s');
				
				$ArrDeatilAdj[$val]['check_qty_oke'] 	= $qtyIN ;
				$ArrDeatilAdj[$val]['check_qty_rusak'] 	= $qtyRISK;
				$ArrDeatilAdj[$val]['check_keterangan'] = strtolower($valx['keterangan']);
				$ArrDeatilAdj[$val]['harga']		 	= ($qtyIN*$restWhDetail[0]->net_price*$kurs_ros);
		//				$ArrDeatilAdj[$val]['harga_freight']	= $valx['harga_freight'];
				
				//detail adjustmeny
				$ArrDeatilChk[$val]['no_ipp'] 		= $no_po;
				$ArrDeatilChk[$val]['id_detail'] 	= NULL;
				$ArrDeatilChk[$val]['kode_trans'] 	= $kode_trans;
				$ArrDeatilChk[$val]['id_material'] 	= $restWhDetail[0]->code_group;
				$ArrDeatilChk[$val]['nm_material'] 	= $restWhDetail[0]->material_name;
				$ArrDeatilChk[$val]['id_category'] 	= $restWhDetail[0]->category_awal;
				$ArrDeatilChk[$val]['nm_category'] 	= $restWhDetail[0]->category_code;
				$ArrDeatilChk[$val]['qty_order'] 	= $restWhDetail[0]->qty_po;
				$ArrDeatilChk[$val]['qty_oke'] 		= $qtyIN ;
				$ArrDeatilChk[$val]['qty_rusak'] 	= $qtyRISK;
				$ArrDeatilChk[$val]['keterangan'] 	= strtolower($valx['keterangan']);
				$ArrDeatilChk[$val]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrDeatilChk[$val]['update_date'] 	= date('Y-m-d H:i:s');

				$total_harga_product=($total_harga_product+($qtyIN*$restWhDetail[0]->net_price*$kurs_ros));
				$total_harga_product_usd=($total_harga_product_usd+($qtyIN*$restWhDetail[0]->net_price));


				if($restWhDetail[0]->id2!=''){
					//update stock
					$ArrStock[$val]['id'] 			= $restWhDetail[0]->id2;
					$ArrStock[$val]['stock'] 		= $restWhDetail[0]->stock + $qtyIN;
					$ArrStock[$val]['rusak'] 		= $restWhDetail[0]->rusak + $qtyRISK;
					$ArrStock[$val]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStock[$val]['update_date'] = date('Y-m-d H:i:s');
					
					//insert history
					$ArrHist[$val]['code_group'] 		= $restWhDetail[0]->code_group;
					$ArrHist[$val]['category_awal'] 	= $restWhDetail[0]->category_awal;
					$ArrHist[$val]['category_code'] 	= $restWhDetail[0]->category_code;
					$ArrHist[$val]['material_name'] 	= $restWhDetail[0]->material_name;
					$ArrHist[$val]['id_gudang'] 		= $gudang;
					$ArrHist[$val]['gudang'] 			= $nm_gudang_ke;
					$ArrHist[$val]['gudang_dari'] 		= "PURCHASE";
					$ArrHist[$val]['id_gudang_ke'] 	= $gudang;
					$ArrHist[$val]['gudang_ke'] 		= $nm_gudang_ke;
					$ArrHist[$val]['qty_stock_awal'] 	= $restWhDetail[0]->stock;
					$ArrHist[$val]['qty_stock_akhir'] 	= $restWhDetail[0]->stock + $qtyIN;
					$ArrHist[$val]['qty_rusak_awal'] 	= $restWhDetail[0]->rusak;
					$ArrHist[$val]['qty_rusak_akhir'] 	= $restWhDetail[0]->rusak + $qtyRISK;
					$ArrHist[$val]['no_trans'] 		= $no_po."/".$kode_trans;
					$ArrHist[$val]['jumlah_qty'] 		= $qtyIN + $qtyRISK;
					$ArrHist[$val]['ket'] 				= 'incoming rutin';
					$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHist[$val]['update_date'] 		= date('Y-m-d H:i:s');
					$CoaMaterial[$restWhDetail[0]->coa] = ((isset($CoaMaterial[$restWhDetail[0]->coa])?$CoaMaterial[$restWhDetail[0]->coa]:0)+($qtyIN*$restWhDetail[0]->net_price*$kurs_ros));
				}
				
				if($restWhDetail[0]->id2==''){
					$sql_mat2	= "	SELECT a.*,c.coa FROM con_nonmat_new a left join con_nonmat_category_awal c on a.category_awal=c.id WHERE a.code_group = '".$restWhDetail[0]->code_group."' LIMIT 1";
					$rest_mat2	= $this->db->query($sql_mat2)->result();
					
					//update stock
					$ArrStockNew[$val]['code_group']	= $rest_mat2[0]->code_group;
					$ArrStockNew[$val]['category_awal']	= $rest_mat2[0]->category_awal;
					$ArrStockNew[$val]['category_code']	= $rest_mat2[0]->category_code;
					$ArrStockNew[$val]['material_name']	= $rest_mat2[0]->material_name;
					$ArrStockNew[$val]['gudang'] 		= $gudang;
					$ArrStockNew[$val]['stock'] 		= $qtyIN;
					$ArrStockNew[$val]['rusak'] 		= $qtyRISK;
					$ArrStockNew[$val]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStockNew[$val]['update_date']	= date('Y-m-d H:i:s');

					if(!empty($restWhDetail[0]->net_price) AND !empty($rest_mat2[0]->coa)){
					$CoaMaterial[$rest_mat2[0]->coa] = ($CoaMaterial[$rest_mat2[0]->coa]+($qtyIN*$restWhDetail[0]->net_price*$kurs_ros));
					}
					
					//insert history
					$ArrHistNew[$val]['code_group']		= $rest_mat2[0]->code_group;
					$ArrHistNew[$val]['category_awal'] 	= $rest_mat2[0]->category_awal;
					$ArrHistNew[$val]['category_code'] 	= $rest_mat2[0]->category_code;
					$ArrHistNew[$val]['material_name'] 	= $rest_mat2[0]->material_name;
					$ArrHistNew[$val]['id_gudang'] 		= $gudang;
					$ArrHistNew[$val]['gudang']			= $nm_gudang_ke;
					$ArrHistNew[$val]['gudang_dari']	= "PURCHASE";
					$ArrHistNew[$val]['id_gudang_ke']	= $gudang;
					$ArrHistNew[$val]['gudang_ke'] 		= $nm_gudang_ke;
					$ArrHistNew[$val]['qty_stock_awal']	= 0;
					$ArrHistNew[$val]['qty_stock_akhir']	= $qtyIN;
					$ArrHistNew[$val]['qty_rusak_awal'] 	= 0;
					$ArrHistNew[$val]['qty_rusak_akhir'] 	= $qtyRISK;
					$ArrHistNew[$val]['no_trans'] 			= $no_po."/".$kode_trans;
					$ArrHistNew[$val]['jumlah_qty'] 		= $qtyIN + $qtyRISK;
					$ArrHistNew[$val]['ket'] 				= 'incoming rutin (insert new)';
					$ArrHistNew[$val]['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistNew[$val]['update_date'] 		= date('Y-m-d H:i:s');
				}
			}

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $no_po,
				'tanggal' 			=> $tanggal_trans,
				'no_ros' 			=> $no_ros,
				'category' 			=> 'incoming rutin',
				'jumlah_mat' 		=> $SumMat + $SumRisk,
				'kd_gudang_dari' 	=> 'PURCHASE',
				'id_gudang_ke' 		=> $gudang,
				'kd_gudang_ke' 		=> $nm_gudang_ke,
				'pic' 				=> $pic,
				'note' 				=> $note,
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> date('Y-m-d H:i:s'),
				'checked' 			=> 'Y',
				'total_freight'		=> $total_forward_bef_ppn,
				'total_harga_product'=> $total_harga_product,
				'jumlah_mat_check' 	=> $SumMat + $SumRisk,
				'checked_by' 		=> $data_session['ORI_User']['username'],
				'checked_date' 		=> date('Y-m-d H:i:s')
			);

			

			//grouping sum
			$temp = [];
			$grouping_temp = [];
			$key = 0;
			$totalprice=0;
			$sum_totalprice=0;
			foreach($ArrUpdateStock as $value) { $key++;
				if(!array_key_exists($value['id'], $temp)) {
					$temp[$value['id']]['good'] = 0;
				}
				$temp[$value['id']]['good'] += $value['qty_good'];
				$grouping_temp[$value['id']]['id'] 			= $value['id'];
				$grouping_temp[$value['id']]['unit_price'] 	= $value['unit_price'];
				$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
				$totalprice=($totalprice+($value['unit_price']*$temp[$value['id']]['good']));

				$sum_totalprice += $totalprice;
			}

			$ArrHeader2 = array(
				'status' => 'COMPLETE',
				'nilai_terima_barang_kurs' => $sum_totalprice,
			);
			
			$ArrHeader3 = array(
				'status' => 'IN PARSIAL',
				'nilai_terima_barang_kurs' => $sum_totalprice,
			);

			// print_r($ArrDeatil);
			// print_r($ArrUpdate);
			// print_r($ArrHist);
			// print_r($ArrInsertH);
			// exit;
			$this->db->trans_start();


			// Jurnal
				// $data_po = $this->db->query("SELECT * FROM tran_po_header WHERE no_po='$no_po'")->row();
				// $datajurnal1 = $this->db->query("select * from " . DBACC . ".master_oto_jurnal_detail where kode_master_jurnal='" . $jenis_jurnal . "' order by parameter_no")->result();
				// $hutang = 0;
				// $uangmuka = 0;
				// $kurs=$kurs_ros;
				// $total_harga=0;
				// $total_rupiah=$total_harga_product;
				// $total_forex=$total_harga_product_usd;
				// $selisih_kurs=0;
				// if(!empty($datajurnal1)){
				// 	foreach ($datajurnal1 as $rec) {
				// 		if ($rec->parameter_no == "1") {
				// 			foreach ($CoaMaterial as $key =>$values){
				// 				$hargaforwardingpercoa = 0;
				// 				if($values > 0 AND $total_harga_product > 0){
				// 				$hargaforwardingpercoa = (($values/$total_harga_product)*$total_forward_bef_ppn);
				// 				}
				// 				$det_Jurnaltes1[] = array(
				// 					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $key, 'keterangan' => 'Material ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : ($values+$hargaforwardingpercoa)), 'kredit' => ($rec->posisi == 'D' ? 0 : ($values+$hargaforwardingpercoa)), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 				);
				// 			}
				// 		}
				// 		if ($rec->parameter_no == "2") {
				// 			$uangmuka = $total_rupiah;
				// 			if ($data_po->nilai_dp > 0) {
				// 				if ($data_po->nilai_dp <= $total_forex) {
				// 					$uangmuka = $data_po->nilai_dp_kurs;//($kurs * $data_po->nilai_dp);
				// 					$selisih_kurs=($uangmuka-($kurs * $data_po->nilai_dp));
				// 					$hutang = ($total_rupiah - ($kurs * $data_po->nilai_dp));
				// 					$this->db->query("update tran_po_header set nilai_terima_barang_kurs=".$hutang.",proses_uang_muka='Y', nilai_dp=0, sisa_dp=0 where no_po='" . $no_po . "'");
				// 				} else {
				// 					$nilai_kurs_saat_dp=($data_po->nilai_dp_kurs/$data_po->nilai_dp);
				// 					$selisih_kurs=(($data_po->total_forex*$nilai_kurs_saat_dp)-($kurs * $data_po->total_forex));

				// 					$this->db->query("update tran_po_header set proses_uang_muka='Y', nilai_dp=(nilai_dp-" . $total_forex . "), sisa_dp=(sisa_dp-" . $total_forex . ") where no_po='" . $no_po . "'");
				// 				}
				// 				$det_Jurnaltes1[] = array(
				// 					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Uang muka ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : $uangmuka), 'kredit' => ($rec->posisi == 'D' ? 0 : $uangmuka), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 				);
				// 			} else {
				// 				$det_Jurnaltes1[] = array(
				// 					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Uang muka ' . $no_po, 'no_request' => $no_po, 'debet' => 0, 'kredit' => 0, 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 				);
				// 			}
				// 		}
				// 		if ($rec->parameter_no == "3") {
				// 			if ($hutang > 0) {
				// 				$det_Jurnaltes1[] = array(
				// 					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Hutang ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : $hutang), 'kredit' => ($rec->posisi == 'D' ? 0 : $hutang), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 				);
				// 			} else {
				// 				$det_Jurnaltes1[] = array(
				// 					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Hutang ' . $no_po, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : $total_rupiah), 'kredit' => ($rec->posisi == 'D' ? 0 : $total_rupiah), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 				);
				// 			}
				// 		}
				// 		if ($rec->parameter_no == "4") {
				// 			$det_Jurnaltes1[] = array(
				// 				'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Cash/Bank' . $no_po, 'no_request' => $no_po, 'debet' => 0, 'kredit' => 0, 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 			);
				// 		}
				// 		if ($rec->parameter_no == "5") {
				// 			$det_Jurnaltes1[] = array(
				// 				'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Hutang Forwarder ' . $no_po, 'no_request' => $no_po, 'debet' => 0, 'kredit' => ($total_forward_bef_ppn+$total_forward_ppn), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 			);
				// 		}
				// 		if ($rec->parameter_no == "6") {
				// 			$det_Jurnaltes1[] = array(
				// 				'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'PPN dibayar dimuka' . $no_po, 'no_request' => $no_po, 'debet' => $total_forward_ppn, 'kredit' => 0, 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 			);
				// 		}
				// 		if ($rec->parameter_no == "7") {
				// 			$det_Jurnaltes1[] = array(
				// 				'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'Selisih kurs' . $no_po, 'no_request' => $no_po, 'kredit' => ($selisih_kurs<0?($selisih_kurs*-1):0), 'debet' => ($selisih_kurs>=0?$selisih_kurs:0), 'no_reff' => $no_ros, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $data_po->id_supplier
				// 			);
				// 		}
				// 	}
				// }
				// $this->db->query("update tran_po_header set total_terima_barang_idr=(total_terima_barang_idr+" . $total_rupiah . ") where no_po='" . $no_po . "'");
				// if(!empty($det_Jurnaltes1)){
				// $this->db->insert_batch('jurnaltras', $det_Jurnaltes1);
				// }
			// loping warehouse_adjustment_detail
			// foreach ($ArrDeatilAdj as $key=>$val){
			// 	$harga_freight = 0;
			// 	if($total_harga_product > 0){
			// 	$harga_freight=round(((($val['harga']*$val['check_qty_oke'])/$total_harga_product)*$total_forward_bef_ppn/$val['check_qty_oke']),0);
			// 	}
			// 	$ArrDeatil[$key]['harga_freight'] = $harga_freight;
			// 	$stock_exp	= "SELECT sum(stock) as ttl_qty,sum((stock)*harga) as ttl_harga FROM warehouse_rutin_stock WHERE code_group = '".$val['id_material']."' and gudang='10' group by code_group";
			// 	$dtstock	= $this->db->query($stock_exp)->result();

			// 	if(!empty($dtstock)){
			// 		$ttl_harga=($dtstock[0]->ttl_harga+(($val['harga']+$harga_freight)*$val['check_qty_oke']));
			// 		$ttl_qty=($dtstock[0]->ttl_qty+$val['check_qty_oke']);
			// 		$newharga=0;
			// 		if($ttl_qty > 0 AND $ttl_harga > 0){
			// 		$newharga=round(($ttl_harga/$ttl_qty),0);
			// 		}
			// 		$this->db->query("update warehouse_rutin_stock set harga='".$newharga."' WHERE code_group = '".$val['id_material']."' and gudang='10'");
			// 	}else{
			// 		if(!empty($ArrStockNew)){
			// 			$newharga=($val['harga']+$harga_freight);
			// 			$ArrStockNew[$val['id_material']]['harga']=$newharga;
			// 		}
			// 	}
			// }


				$this->db->update_batch('tran_po_detail', $ArrUpdate, 'id');

				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilChk);

				$qCheck = "SELECT * FROM tran_po_detail WHERE no_po='".$no_po."' AND qty_in < qty_po ";
				$NumChk = $this->db->query($qCheck)->num_rows();
				if($NumChk < 1){
					$this->db->where('no_po', $no_po);
					$this->db->update('tran_po_header', $ArrHeader2);
				}
				if($NumChk > 0){
					$this->db->where('no_po', $no_po);
					$this->db->update('tran_po_header', $ArrHeader3);
				}
				
				if(!empty($ArrStock)){
					$this->db->update_batch('warehouse_rutin_stock', $ArrStock, 'id');
					$this->db->insert_batch('warehouse_rutin_history', $ArrHist);
				}
				if(!empty($ArrStockNew)){
					$this->db->insert_batch('warehouse_rutin_stock', $ArrStockNew);
					$this->db->insert_batch('warehouse_rutin_history', $ArrHistNew);
				}
			$this->db->trans_complete();
		}


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			if(!empty($grouping_temp)){
				insert_jurnal_stock($grouping_temp,NULL,10,$kode_trans,'incoming stok','penambahan gudang indirect','incoming stok');
			}
			history($histHlp);
//			$this->db->trans_rollback();
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
		}
		echo json_encode($Arr_Data);
	}
	
	
	public function modal_detail_adjustment(){
		$kode_trans     = $this->uri->segment(3);
		$tanda     = $this->uri->segment(4);

		$sql 		= "SELECT * FROM warehouse_adjustment_detail WHERE kode_trans='".$kode_trans."' ";
		$result		= $this->db->query($sql)->result_array();
		
		$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
		$result_header		= $this->db->query($sql_header)->result();
		
		$data = array(
			'result' 	=> $result,
			'tanda' 	=> $tanda,
			'checked' 	=> $result_header[0]->checked,
			'kode_trans'=> $result_header[0]->kode_trans,
			'no_po' 	=> $result_header[0]->no_ipp,
			'no_ros' 	=> $result_header[0]->no_ros,
			'dated' 	=> $result_header[0]->kode_trans,
			'GET_SO'	=> get_detail_so_number(),
			'resv' 		=> (!empty($result_header[0]->tanggal))?date('d F Y', strtotime($result_header[0]->tanggal)):date('d F Y', strtotime($result_header[0]->created_date)),
			'result_header'	=> $result_header
			
		);

		$this->load->view('Warehouse_rutin/modal_detail_adjustment', $data);
	}
	
	//==========================================================================================================================
	//======================================================STCCK==============================================================
	//==========================================================================================================================
	
	public function stock(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/stock';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();
		$data = array(
			'title'			=> 'Warehouse Stok >> Stock',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View Consumable Stock');
		$this->load->view('Warehouse_rutin/stock',$data);
	}
	
	public function get_data_json_stock(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_stock(
			$requestData['gudang'],
			$requestData['date_filter'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$stock			= $fetch['stock'];
		$rusak			= $fetch['rusak'];
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
			$nestedData[]	= "<div align='left'>".strtoupper($row['code_group'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_item'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_excel'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nama_master'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('con_nonmat_category_awal', 'category', 'id', $row['category_awal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('warehouse', 'nm_gudang', 'id', $row['gudang']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['stock'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['rusak'])."</div>";
			
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data,
			"recordsStock"		=> $stock,
			"recordsRusak"		=> $rusak
		);

		echo json_encode($json_data);
	}

	public function query_data_json_stock($gudang, $date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$table = "warehouse_rutin_stock";
		$where_gudang ='';
		$where_date ='';

		if($gudang != '0'){
			$where_gudang = " AND b.category_awal = '".$gudang."' ";
		}

		if(!empty($date_filter)){
			if($gudang != '0'){
				$where_gudang = " AND b.category_awal = '".$gudang."' ";
			}
			$where_date = " AND DATE(a.hist_date) = '".$date_filter."' ";
			$table = "warehouse_rutin_stock_per_day";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.material_name AS nama_master,
				b.spec,
				b.code_group,
				b.kode_item,
				b.kode_excel,
				b.category_awal
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.gudang IN ('".getGudangIndirect()."','".getGudangHouseHold()."') ".$where_gudang." ".$where_date." AND b.status='1' AND b.deleted = 'N' AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_item LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_excel LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		$Query_Sum = "
			SELECT
				SUM(a.stock) AS stock,
				SUM(a.rusak) AS rusak
			FROM
				".$table." a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group
		    WHERE 1=1 AND a.gudang IN ('".getGudangIndirect()."','".getGudangHouseHold()."') ".$where_gudang." ".$where_date." AND b.status='1' AND b.deleted = 'N' AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_item LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.kode_excel LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$stock = $rusak = 0;
		$Hasil_SUM		   = $this->db->query($Query_Sum)->result_array();
		if($Hasil_SUM){
			$stock		= $Hasil_SUM[0]['stock'];
			$rusak	= $Hasil_SUM[0]['rusak'];
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$data['stock'] 	= $stock;
		$data['rusak'] = $rusak;
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'code_group',
			2 => 'kode_item',
			3 => 'kode_excel',
			4 => 'material_name',
			5 => 'spec',
			6 => 'b.category_awal',
			7 => 'stock',
			8 => 'rusak'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	//==========================================================================================================================
	//==============================================CHECKING MATERIAL===========================================================
	//==========================================================================================================================
	
	public function checking(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$list_po			= $this->db->query("SELECT * FROM tran_po_header WHERE category='rutin' AND deleted='N' ORDER BY no_po ASC")->result_array();

		$data = array(
			'title'			=> 'Indeks Of Incoming Check Rutin',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_po'		=> $list_po,
		);
		history('View incoming check rutin'); 
		$this->load->view('Warehouse_rutin/checking',$data);
	}
	
	public function get_data_json_checking(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_checking(
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."/".date('ymdhis', strtotime($row['created_date']))."</div>"; 
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat_check'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['pic'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='left'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$status = "WAITING INSPECTION";
			$warna = 'blue';
			if($row['checked'] == 'Y'){
				$status = "CHECKED";
				$warna = 'green';
			}
			$nestedData[]	= "<div align='left'><span class='badge bg-".$warna."'>".$status."</span></div>"; 
				$plus	= "";
				
				$print	= "&nbsp;<a href='".base_url('warehouse_rutin/print_incoming_check/'.$row['kode_trans'].'/check')."' target='_blank' class='btn btn-sm btn-warning' title='Print'><i class='fa fa-print'></i></a>";
				if($row['checked'] == 'N'){
					$plus	= "&nbsp;<button type='button' class='btn btn-sm btn-info check' title='Check Incoming' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-check'></i></button>"; 
				}

			$nestedData[]	= "<div align='left'>
									<button type='button' class='btn btn-sm btn-primary detailAjust' title='Detail' data-tanda='check' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-eye'></i></button>
                                    ".$print."
									".$plus."
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

	public function query_data_json_checking($no_po, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_no_po ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}
		
		
		$sql = "
			SELECT
				a.*
			FROM
				warehouse_adjustment a
		    WHERE 1=1 AND a.category = 'incoming rutin'
				".$where_no_po."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modal_incoming_check(){
		$kode_trans     = $this->uri->segment(3);

		$sql 			= "	SELECT 
								a.*, 
								b.qty_po, 
								b.qty_in,
								b.id AS id2
							FROM 
								warehouse_adjustment_detail a 
								LEFT JOIN tran_po_detail b ON a.no_ipp=b.no_po
							WHERE 
								a.id_material = b.id_barang
								AND a.kode_trans='".$kode_trans."' ";
		$result			= $this->db->query($sql)->result_array();
		
		$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
		$result_header		= $this->db->query($sql_header)->result();
		
		$data = array(
			'result' 	=> $result,
			'no_po' 	=> $result_header[0]->no_ipp,
			'kode_trans' 	=> $result_header[0]->kode_trans,
			'id_header' 	=> $result_header[0]->id,
			'gudang_tujuan' 	=> $result_header[0]->kd_gudang_ke,
			'id_tujuan' 	=> $result_header[0]->id_gudang_ke,
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 	=> date('d F Y', strtotime($result_header[0]->created_date))
			
		);

		$this->load->view('Warehouse_rutin/modal_incoming_check', $data);
	}
	
	public function process_checking(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		$detail			= $data['detail'];
		$id_header		= $data['id_header'];
		$gudang			= $data['gudang_tujuan'];
		$id_tujuan		= $data['id_tujuan'];
		$kode_trans		= $data['kode_trans'];

		// echo $gudang;
		// print_r($data);
		// exit;

		$ArrDeatil		 = array();
		$ArrDeatilAdj		 = array();
		$ArrStock		 = array();
		$ArrHist		 = array();
		$ArrStockNew		 = array();
		$ArrHistNew		 = array();
		$ArrUpdatePO 	= array();
		$ArrUpdExp		 = array();
		$ArrInsExp		 = array();
		$ArrUpdExpHist		 = array();
		$ArrInsExpHist		 = array();
		$SUM_MAT = 0;
		foreach($detail AS $val2 => $valx2){
			$qtyIN 		= 0;
			$qtyRISK 	= 0;
			
			foreach($valx2['detail'] AS $val => $valx){
				$qtyIN 		+= str_replace(',','',$valx['qty_oke']);
				$qtyRISK 	+= str_replace(',','',$valx['qty_rusak']);
				
				
				
				$sqlWhDetail	= "	SELECT
									a.*,
									b.id AS id2,
									b.gudang,
									b.code_group,
									b.category_awal,
									b.category_code,
									b.material_name,
									b.stock,
									b.rusak
								FROM
									warehouse_adjustment_detail a
									LEFT JOIN warehouse_rutin_stock b
										ON a.id_material=b.code_group
								WHERE
									a.id = '".$valx2['id']."'
									AND b.gudang='".$id_tujuan."'
								";
				$restWhDetail	= $this->db->query($sqlWhDetail)->result();
				
				$ArrDeatil[$val2]['id'] 				= $valx2['id'];
				$ArrDeatil[$val2]['check_qty_oke'] 		= $qtyIN ;
				$ArrDeatil[$val2]['check_qty_rusak'] 	= $qtyRISK;
				$ArrDeatil[$val2]['check_keterangan'] 	= $valx['keterangan'];
				
				if(!empty($restWhDetail)){
					//update stock
					$ArrStock[$val2]['id'] 			= $restWhDetail[0]->id2;
					$ArrStock[$val2]['stock'] 	= $restWhDetail[0]->stock + $qtyIN;
					$ArrStock[$val2]['rusak'] 	= $restWhDetail[0]->rusak + $qtyRISK;
					$ArrStock[$val2]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStock[$val2]['update_date'] = date('Y-m-d H:i:s');
					
					//insert history
					$ArrHist[$val2]['code_group'] 		= $restWhDetail[0]->code_group;
					$ArrHist[$val2]['category_awal'] 	= $restWhDetail[0]->category_awal;
					$ArrHist[$val2]['category_code'] 	= $restWhDetail[0]->category_code;
					$ArrHist[$val2]['material_name'] 	= $restWhDetail[0]->material_name;
					$ArrHist[$val2]['id_gudang'] 		= $id_tujuan;
					$ArrHist[$val2]['gudang'] 			= $gudang;
					$ArrHist[$val2]['gudang_dari'] 	= "PURCHASE";
					$ArrHist[$val2]['id_gudang_ke'] 		= $id_tujuan;
					$ArrHist[$val2]['gudang_ke'] 		= $gudang;
					$ArrHist[$val2]['qty_stock_awal'] 	= $restWhDetail[0]->stock;
					$ArrHist[$val2]['qty_stock_akhir'] 	= $restWhDetail[0]->stock + $qtyIN;
					$ArrHist[$val2]['qty_rusak_awal'] 	= $restWhDetail[0]->rusak;
					$ArrHist[$val2]['qty_rusak_akhir'] 	= $restWhDetail[0]->rusak + $qtyRISK;
					$ArrHist[$val2]['no_trans'] 			= $restWhDetail[0]->no_ipp."/".$kode_trans;
					$ArrHist[$val2]['jumlah_qty'] 		= $qtyIN + $qtyRISK;
					$ArrHist[$val2]['ket'] 				= 'incoming rutin';
					$ArrHist[$val2]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHist[$val2]['update_date'] 		= date('Y-m-d H:i:s');
				}
				
				if(empty($restWhDetail)){
					$sql_matIn	= "	SELECT a.id_material FROM warehouse_adjustment_detail a WHERE a.id = '".$valx2['id']."' LIMIT 1";
					$rest_matIn	= $this->db->query($sql_matIn)->result();
					
					$sql_mat2	= "	SELECT a.* FROM con_nonmat_new a WHERE a.code_group = '".$rest_matIn[0]->id_material."' LIMIT 1";
					$rest_mat2	= $this->db->query($sql_mat2)->result();
					
					//update stock
					$ArrStockNew[$val2]['code_group'] 		= $rest_mat2[0]->code_group;
					$ArrStockNew[$val2]['category_awal'] 	= $rest_mat2[0]->category_awal;
					$ArrStockNew[$val2]['category_code'] 	= $rest_mat2[0]->category_code;
					$ArrStockNew[$val2]['material_name'] 	= $rest_mat2[0]->material_name;
					$ArrStockNew[$val2]['gudang'] 	= $id_tujuan;
					$ArrStockNew[$val2]['stock'] 		= $qtyIN;
					$ArrStockNew[$val2]['rusak'] 		= $qtyRISK;
					$ArrStockNew[$val2]['update_by'] 	= $data_session['ORI_User']['username'];
					$ArrStockNew[$val2]['update_date'] = date('Y-m-d H:i:s');
					
					//insert history
					$ArrHistNew[$val2]['code_group'] 		= $rest_mat2[0]->code_group;
					$ArrHistNew[$val2]['category_awal'] 	= $rest_mat2[0]->category_awal;
					$ArrHistNew[$val2]['category_code'] 	= $rest_mat2[0]->category_code;
					$ArrHistNew[$val2]['material_name'] 	= $rest_mat2[0]->material_name;
					$ArrHistNew[$val2]['id_gudang'] 		= $id_tujuan;
					$ArrHistNew[$val2]['gudang'] 		= $gudang;
					$ArrHistNew[$val2]['gudang_dari'] 	= "PURCHASE";
					$ArrHistNew[$val2]['id_gudang_ke'] 		= $id_tujuan;
					$ArrHistNew[$val2]['gudang_ke'] 		= $gudang;
					$ArrHistNew[$val2]['qty_stock_awal'] 	= 0;
					$ArrHistNew[$val2]['qty_stock_akhir'] 	= $qtyIN;
					$ArrHistNew[$val2]['qty_rusak_awal'] 	= 0;
					$ArrHistNew[$val2]['qty_rusak_akhir'] 	= $qtyRISK;
					$ArrHistNew[$val2]['no_trans'] 			= $restWhDetail[0]->no_ipp."/".$kode_trans;
					$ArrHistNew[$val2]['jumlah_qty'] 		= $qtyIN + $qtyRISK;
					$ArrHistNew[$val2]['ket'] 				= 'incoming rutin (insert new)';
					$ArrHistNew[$val2]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrHistNew[$val2]['update_date'] 		= date('Y-m-d H:i:s');
				}
				
				//detail adjustmeny
				$ArrDeatilAdj[$val2.$val]['no_ipp'] 		= $restWhDetail[0]->no_ipp;
				$ArrDeatilAdj[$val2.$val]['id_detail'] 		= $valx2['id'];
				$ArrDeatilAdj[$val2.$val]['kode_trans'] 	= $kode_trans;
				$ArrDeatilAdj[$val2.$val]['id_material'] 	= $restWhDetail[0]->code_group;
				$ArrDeatilAdj[$val2.$val]['nm_material'] 	= $restWhDetail[0]->material_name;
				$ArrDeatilAdj[$val2.$val]['id_category'] 	= $restWhDetail[0]->category_awal;
				$ArrDeatilAdj[$val2.$val]['nm_category'] 	= $restWhDetail[0]->category_code;
				$ArrDeatilAdj[$val2.$val]['qty_order'] 		= $restWhDetail[0]->qty_order;
				$ArrDeatilAdj[$val2.$val]['qty_oke'] 		= str_replace(',','',$valx['qty_oke']);
				$ArrDeatilAdj[$val2.$val]['qty_rusak'] 		= str_replace(',','',$valx['qty_rusak']);
				$ArrDeatilAdj[$val2.$val]['keterangan'] 	= strtolower($valx['keterangan']);
				$ArrDeatilAdj[$val2.$val]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrDeatilAdj[$val2.$val]['update_date'] 		= date('Y-m-d H:i:s');
				
			}
			$SUM_MAT 	+= $qtyIN + $qtyRISK;
			
			
		}

		$ArrUpdate = array(
			'checked' => 'Y',
			'jumlah_mat_check' => $SUM_MAT,
			'checked_by' => $data_session['ORI_User']['username'],
			'checked_date' => date('Y-m-d H:i:s')
		);

		// print_r($ArrDeatil);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// print_r($ArrDeatilAdj);
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id_header);
			$this->db->update('warehouse_adjustment', $ArrUpdate);
			
			$this->db->update_batch('warehouse_adjustment_detail', $ArrDeatil, 'id');
			$this->db->insert_batch('warehouse_adjustment_check', $ArrDeatilAdj);
			
			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_rutin_stock', $ArrStock, 'id');
				$this->db->insert_batch('warehouse_rutin_history', $ArrHist);
			}
			if(!empty($ArrStockNew)){
				$this->db->insert_batch('warehouse_rutin_stock', $ArrStockNew);
				$this->db->insert_batch('warehouse_rutin_history', $ArrHistNew);
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
			history("Rutin incoming check id : ".$restWhDetail[0]->no_ipp."/".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}
	
	public function print_incoming_check(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'check' => $check
		);
		
		history('Print Incoming Rutin '.$kode_trans);
		$this->load->view('Print/print_incoming_rutin_check', $data);
	}
	
	//==========================================================================================================================
	//================================================== OUTGOING ==============================================================
	//==========================================================================================================================
	
	public function outgoing(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE (category='indirect' OR category='household') ORDER BY urut ASC")->result_array();
		$subgudang			= $this->db->query("SELECT * FROM costcenter WHERE deleted='N' ORDER BY nm_costcenter ASC")->result_array();
		$no_ipp				= $this->db->query("SELECT no_ipp FROM warehouse_planning_detail WHERE sudah_request < use_stock AND no_ipp LIKE 'IPP%' GROUP BY no_ipp")->result_array();
		$list_ipp_req		= $this->db->query("SELECT no_ipp FROM warehouse_adjustment WHERE no_ipp LIKE 'IPP%' GROUP BY no_ipp")->result_array();
		$uri_tanda			= $this->uri->segment(3);

		$so_number = $this->db	->select('a.so_number, b.project')
								->from('so_number a')
								->join('production b',"REPLACE(a.id_bq,'BQ-','') = b.no_ipp",'left')
								->where('a.id_bq <>','x')
								->get()
								->result_array();
		$data = array(
			'title'			=> 'Warehouse Stok >> Outgoing',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'pusat'			=> $pusat,
			'subgudang'		=> $subgudang,
			'no_ipp'		=> $no_ipp,
			'list_ipp_req'	=> $list_ipp_req,
			'so_number'		=> $so_number,
			'uri_tanda'		=> $uri_tanda
		);
		history('View request outgoing stok'); 
		$this->load->view('Warehouse_rutin/outgoing',$data);
	}
	
	public function get_data_json_outgoing(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_outgoing(
			$requestData['pusat'],
			$requestData['subgudang'],
			$requestData['tanda'],
			$requestData['uri_tanda'],
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
		$uri_tanda = $requestData['uri_tanda'];
		$GET_USERNAME = get_detail_user();
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

			$USERNAME = (!empty($GET_USERNAME[strtolower($row['created_by'])]['nm_lengkap']))?$GET_USERNAME[strtolower($row['created_by'])]['nm_lengkap']:'';
			$TGL_TRANS = (!empty($row['tanggal']))?$row['tanggal']:$row['created_date'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>"; 
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($TGL_TRANS))."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['kd_gudang_dari']."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['kd_gudang_ke'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['jumlah_mat'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($USERNAME)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
				$plus	= "";
				$print	= "";
				
				
				if($row['checked'] == 'Y'){
					$print	= "&nbsp;<a href='".base_url('warehouse_rutin/print_outgoing_rutin/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Permintaan'><i class='fa fa-print'></i></a>";
				}
				

			$nestedData[]	= "<div align='left'>
									<button type='button' class='btn btn-sm btn-primary detailAjust' data-tanda='outgoing_rutin' title='View Permintaan' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-eye'></i></button>
                                    ".$print."
									".$plus."
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

	public function query_data_json_outgoing($pusat, $subgudang, $tanda, $uri_tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_pusat ='';
		if(!empty($pusat)){
			$where_pusat = " AND a.id_gudang_dari = '".$pusat."' ";
		}
		
		$where_subgudang ='';
		if(!empty($subgudang)){
			$where_subgudang = " AND a.id_gudang_ke = '".$subgudang."' ";
		}
		
		// $where_tanda ='';
		// if(!empty($tanda)){
			$where_tanda = " AND a.category = 'outgoing rutin' ";
		// }
		
		$where_tanda2 ='';
		// if(!empty($uri_tanda)){
			// $where_tanda2 = " AND a.checked = 'N' ";
		// }
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				warehouse_adjustment a,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.status_id = '1'
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
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'kd_gudang_dari',
			3 => 'kd_gudang_ke',
			4 => 'jumlah_mat',
			5 => 'created_by',
			6 => 'created_date'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modal_outgoing(){
		$data = $this->input->post();
		
		$data_session	= $this->session->userdata;
		$this->db->where('created_by', $data_session['ORI_User']['username']);
		$this->db->delete('temp_server_side');
	
	
		$gudang_before 	= $data['gudang_before'];
		$gudang_after 	= $data['gudang_after'];
		$tanggal_trans 	= $data['tanggal_trans'];
		$sales_order_project 	= $data['sales_order_project'];
		$data_gudang		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();

		$data = array(
			'gudang_before' => $gudang_before,
			'gudang_after' 	=> $gudang_after,
			'tanggal_trans' => $tanggal_trans,
			'sales_order_project' => $sales_order_project,
			'data_gudang' 	=> $data_gudang
		);
		
		$this->load->view('Warehouse_rutin/modal_outgoing', $data);
	}
	
	public function get_data_json_modal_outgoing(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_modal_outgoing(
			$requestData['pusat'],
			$requestData['category'],
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

		$data_session	= $this->session->userdata;
		$GET_QUERY 		= $this->db->get_where('temp_server_side',array('created_by'=>$data_session['ORI_User']['username'],'category'=>'outgoing consumable'))->result_array();
		$ArrGetlastDet	= [];
		foreach ($GET_QUERY as $key => $value) {
			$ArrGetlastDet[$value['id_mat']]['qty'] = (!empty($value['qty']))?$value['qty']:'';
			$ArrGetlastDet[$value['id_mat']]['ket'] = (!empty($value['ket']))?$value['ket']:'';
		}

		$GET_CATEGORY 		= $this->db->get_where('con_nonmat_category_awal',array('delete'=>'N'))->result_array();
		$ArrGetCategory	= [];
		foreach ($GET_CATEGORY as $key => $value) {
			$ArrGetCategory[$value['id']] = $value['category'];
		}

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

			$QTY = (!empty($ArrGetlastDet[$row['id']]['qty']))?$ArrGetlastDet[$row['id']]['qty']:'';
			$KET = (!empty($ArrGetlastDet[$row['id']]['ket']))?$ArrGetlastDet[$row['id']]['ket']:'';
			
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."<input type='hidden' name='detail[".$nomor."][id]' id='id_".$nomor."' value='".$row['id']."'></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($ArrGetCategory[$row['category_awal']])."</div>"; 
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_barang'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>"; 
			$nestedData[]	= "<div align='right'><b id='stock_".$nomor."'>".number_format($row['stock'],2)."</b></div>";
			$nestedData[]	= "<div align='left'><input type='text' style='width:100%' name='detail[".$nomor."][sudah_request]' value='".$QTY."' id='sudah_request_".$nomor."' data-no='".$nomor."' class='form-control input-sm text-center autoNumeric qty'><script type='text/javascript'>$('.autoNumeric').autoNumeric();</script></div>";
			$nestedData[]	= "<div align='left'><input type='text' style='width:100%' name='detail[".$nomor."][ket_request]' value='".$KET."' id='ket_request_".$nomor."' data-no='".$nomor."' class='form-control input-sm text-left ket'></div>";
			
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

	public function query_data_json_modal_outgoing($pusat, $category, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		
		$where_pusat ='';
		$where_pusat2 ='';
		if(!empty($pusat)){
			$where_pusat = " AND a.gudang = '".$pusat."' ";
			if($pusat == '10'){
				$where_pusat2 = " AND a.category_awal IN (1,6,7,8,11)  ";
			}
			else{
				$where_pusat2 = " AND a.category_awal NOT IN (1,6,7,8,11)  ";
			}
		}
		
		$where_category ='';
		if($category <> '0'){
			$where_category = " AND a.category_awal = '".$category."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.material_name AS nm_barang,
				b.spec
			FROM
				warehouse_rutin_stock a
				LEFT JOIN con_nonmat_new b ON a.code_group=b.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1
				".$where_pusat."
				".$where_pusat2."
				".$where_category."
				AND b.deleted_date IS NULL
				AND b.status = '1'
			AND(
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'category_awal',
			2 => 'b.material_name',
			3 => 'b.spec',
			4 => 'stock'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function process_outgoing(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$UserName 		= $data_session['ORI_User']['username'];
		$DateTime 		= date('Y-m-d H:i:s');
		
		// $detail		= $data['detail'];
		$detail			= $this->db->query("SELECT * FROM temp_server_side WHERE category='outgoing consumable' AND created_by='".$data_session['ORI_User']['username']."'")->result_array();
		$gudang_before	= $data['gudang_before'];
		$gudang_after	= $data['gudang_after'];
		$tanggal_trans	= $data['tanggal_trans'];
		$sales_order_project	= $data['sales_order_project'];
		$Ym 			= date('ym');
		// print_r($data);
		// exit;
		
		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRN".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRN".$Ym.$urut2;

		$ArrDeatilAdj	 = array();
		$ArrStock	 = array();
		$ArrHist	 = array();
		$ArrUpdateStock	 = array();
		
		$SUM_MAT = 0;
		foreach($detail AS $val => $valx){
			$sudah_request 	= str_replace(',','',$valx['qty']);
			if($sudah_request > 0){
				$gud_pusat	= "	SELECT
									b.*
								FROM
									warehouse_rutin_stock b
								WHERE
									b.id = '".$valx['id_mat']."'
									AND b.gudang='".$gudang_before."'
								";
				$rest_pusat	= $this->db->query($gud_pusat)->result();

				$STOCK_QTY = (!empty($rest_pusat[0]->stock) AND $rest_pusat[0]->stock > 0)?$rest_pusat[0]->stock:0;
				$REQUEST_OUT = $sudah_request;
				if($STOCK_QTY < $sudah_request){
					$REQUEST_OUT = $STOCK_QTY;
				}

				$SUM_MAT += $REQUEST_OUT;

				//detail adjustmeny
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['id_material'] 		= $rest_pusat[0]->code_group;
				$ArrDeatilAdj[$val]['nm_material'] 		= $rest_pusat[0]->material_name;
				$ArrDeatilAdj[$val]['id_category'] 		= $rest_pusat[0]->category_awal;
				$ArrDeatilAdj[$val]['nm_category'] 		= $rest_pusat[0]->category_code;
				$ArrDeatilAdj[$val]['qty_order'] 		= $REQUEST_OUT;
				$ArrDeatilAdj[$val]['qty_oke'] 			= $REQUEST_OUT;
				$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['ket']);
				$ArrDeatilAdj[$val]['check_qty_oke'] 	= $REQUEST_OUT;
				$ArrDeatilAdj[$val]['check_keterangan']	= strtolower($valx['ket']);
				$ArrDeatilAdj[$val]['update_by'] 		= $UserName;
				$ArrDeatilAdj[$val]['update_date'] 		= $DateTime;
			
				//update stock
				$ArrStock[$val]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$val]['stock'] 		= $STOCK_QTY - $REQUEST_OUT;
				$ArrStock[$val]['update_by'] 	= $UserName;
				$ArrStock[$val]['update_date'] 	= $DateTime;

				$ArrUpdateStock[$val]['id'] 		= $rest_pusat[0]->code_group;
				$ArrUpdateStock[$val]['qty_good'] 	= $REQUEST_OUT;
				
				//insert history
				$ArrHist[$val]['code_group'] 		= $rest_pusat[0]->code_group;
				$ArrHist[$val]['category_awal'] 	= $rest_pusat[0]->category_awal;
				$ArrHist[$val]['category_code'] 	= $rest_pusat[0]->category_code;
				$ArrHist[$val]['material_name'] 	= $rest_pusat[0]->material_name;
				$ArrHist[$val]['id_gudang'] 		= $gudang_before;
				$ArrHist[$val]['gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
				$ArrHist[$val]['id_gudang_dari'] 	= $gudang_before;
				$ArrHist[$val]['gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
				$ArrHist[$val]['id_gudang_ke'] 		= $gudang_after;
				$ArrHist[$val]['gudang_ke'] 		= strtoupper(get_name('costcenter', 'nm_costcenter', 'id', $gudang_after));
				$ArrHist[$val]['qty_stock_awal'] 	= $STOCK_QTY;
				$ArrHist[$val]['qty_stock_akhir'] 	= $STOCK_QTY - $REQUEST_OUT;
				$ArrHist[$val]['qty_rusak_awal'] 	= $rest_pusat[0]->rusak;
				$ArrHist[$val]['qty_rusak_akhir'] 	= $rest_pusat[0]->rusak;
				$ArrHist[$val]['no_trans'] 			= $kode_trans;
				$ArrHist[$val]['jumlah_qty'] 		= $REQUEST_OUT;
				$ArrHist[$val]['ket'] 				= 'outgoing rutin';
				$ArrHist[$val]['update_by'] 		= $UserName;
				$ArrHist[$val]['update_date'] 		= $DateTime;
				
			}
		}
		
		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'category' 			=> 'outgoing rutin',
			'jumlah_mat' 		=> $SUM_MAT,
			'tanggal' 			=> $tanggal_trans,
			'no_so' 			=> $sales_order_project,
			'id_gudang_dari' 	=> $gudang_before,
			'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $gudang_before),
			'id_gudang_ke' 		=> $gudang_after,
			'kd_gudang_ke' 		=> get_name('costcenter', 'nm_costcenter', 'id', $gudang_after),
			'checked'			=> 'Y',
			'created_by' 		=> $UserName,
			'created_date' 		=> $DateTime,
			'checked_by' 		=> $UserName,
			'checked_date' 		=> $DateTime
		);


		//grouping sum
		$temp = [];
		$grouping_temp = [];
		$key = 0;
		foreach($ArrUpdateStock as $value) { $key++;
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']]['good'] = 0;
			}
			$temp[$value['id']]['good'] += $value['qty_good'];

			$grouping_temp[$value['id']]['id'] 			= $value['id'];
			$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
		}
		// print_r($ArrInsertH);
		// print_r($ArrDeatilAdj);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;
		$this->db->trans_start();
			$this->db->insert('warehouse_adjustment', $ArrInsertH);
			$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
			
			$this->db->update_batch('warehouse_rutin_stock', $ArrStock, 'id');
			$this->db->insert_batch('warehouse_rutin_history', $ArrHist);
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
			if(!empty($grouping_temp)){
				insert_jurnal_stock($grouping_temp,$gudang_before,$gudang_after,$kode_trans,'outgoing stok','pengurangan gudang indirect','distribusi ke costcenter');
			}
			history("Outgoing consumable rutin : ".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}
	
	public function print_outgoing_rutin(){
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
		
		history('Print Outgoing Rutin '.$kode_trans);
		$this->load->view('Print/print_outgoing_rutin', $data);
	}
	
	public function save_temp_mutasi(){
		$data 			 	= $this->input->post();
		$data_session		= $this->session->userdata;
		$printby			= $data_session['ORI_User']['username'];

		$id			      	= $data['id'];
		$sudah_request	  	= str_replace(',','',$data['sudah_request']);
		$ket_request	  	= $data['ket_request'];

		$ArrInsertH = array(
			'category' 		=> 'outgoing consumable',
			'id_mat' 		=> $id,
			'qty'   	  	=> $sudah_request,
			'ket' 		  	=> $ket_request,
			'created_by' 	=> $printby,
			'created_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id_mat', $id);
			$this->db->where('created_by', $printby);
			$this->db->delete('temp_server_side');
			
			$this->db->insert('temp_server_side', $ArrInsertH);
		$this->db->trans_complete();

	}
	
	//==========================================================================================================================
	//======================================================SUMMARY==============================================================
	//==========================================================================================================================
	
	public function summary(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/stock';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();
		$data = array(
			'title'			=> 'Warehouse Stok >> Pemakaian',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang
		);
		history('View consumable summary');
		$this->load->view('Warehouse_rutin/summary',$data);
	}
	
	public function get_data_json_summary(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_summary(
			$requestData['gudang'],
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
			$nestedData[]	= "<div align='left'>".strtoupper($row['material_name'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('con_nonmat_new', 'spec', 'code_group', $row['code_group']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('con_nonmat_category_awal', 'category', 'id', $row['category_awal']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['stock'],2)."</div>";
			$date_now = date('Y-m-d');
			for($a=0; $a<=3; $a++){
				$month 		= date('M-Y', strtotime('-'.$a.' month', strtotime($date_now)));
				$month_sql 	= date('Y-m', strtotime('-'.$a.' month', strtotime($date_now)));
				$query 		= $this->db->select('SUM(a.qty_oke) AS stock_out')
										->from('warehouse_adjustment_detail a')
										->join('warehouse_adjustment b', 'a.kode_trans = b.kode_trans','left')
										->where(array('b.category'=>'outgoing rutin','a.id_material'=>$row['code_group']))
										->like('a.update_date', $month_sql)
										->get()->result();
				$hasil = json_decode(json_encode($query),true);
				$nestedData[]	= "<div align='right'>".number_format($hasil[0]['stock_out'],2)."</div>";
			}
			
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

	public function query_data_json_summary($gudang, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.category_awal = '".$gudang."' ";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				warehouse_rutin_stock a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_gudang." AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'material_name',
			2 => 'category_awal',
			3 => 'stock'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	//==========================================================================================================================
	//======================================================SUMMARY==============================================================
	//==========================================================================================================================
	
	public function detil_transaction(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/stock';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_gudang		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ")->result_array();

		$data_material = $this->db->group_by('code_group')->get('warehouse_rutin_history')->result_array();
		$data = array(
			'title'			=> 'Warehouse Stok >> Detail Transaction',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'data_gudang'	=> $data_gudang,
			'data_material'=> $data_material
		);
		history('View consumable detil_transaction');
		$this->load->view('Warehouse_rutin/detil_transaction',$data);
	}
	
	public function get_data_json_detil_transaction(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_detil_transaction(
			$requestData['material'],
			$requestData['tgl_awal'],
			$requestData['tgl_akhir'],
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
			$nestedData[]	= "<div align='left'>".strtoupper($row['ket'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['update_date']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_trans'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['gudang_ke'])."</div>";
			$Category_awal = get_name('con_nonmat_new', 'category_awal', 'code_group', $row['code_group']);
			$Nm_Category = get_name('con_nonmat_category_awal', 'category', 'id', $Category_awal);

			$nestedData[]	= "<div align='left'>".strtoupper($Nm_Category)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['code_group'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['material_name_new'])."</div>";

			$nestedData[]	= "<div align='left'>".strtoupper(get_name('con_nonmat_new', 'spec', 'code_group', $row['code_group']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_qty'],2)."</div>";
			$get_ket = $this->db->select('keterangan')->get_where('warehouse_adjustment_detail', array('kode_trans'=>$row['no_trans'],'id_material'=>$row['code_group']))->result();
			$ket = (!empty($get_ket[0]->keterangan))?$get_ket[0]->keterangan:'';
			$nestedData[]	= "<div align='left'>".strtoupper($ket)."</div>";
			
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

	public function query_data_json_detil_transaction($material,$tgl_awal, $tgl_akhir,$like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_gudang ='';
		if(!empty($material)){
			$where_gudang = " AND a.code_group = '".$material."' ";
		}

		$where_daterange ='';
		if($tgl_awal != '0'){
			$where_daterange = " AND DATE(a.update_date) BETWEEN '".date('Y-m-d',strtotime($tgl_awal))."' AND '".date('Y-m-d',strtotime($tgl_akhir))."' ";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.material_name AS material_name_new
			FROM
				warehouse_rutin_history a
				LEFT JOIN con_nonmat_new b ON a.code_group = b.code_group AND b.deleted_date IS NULL,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where_gudang." ".$where_daterange." AND (
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'ket',
			2 => 'update_date',
			3 => 'no_trans',
			4 => 'gudang_ke',
			5 => 'material_name'
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
}