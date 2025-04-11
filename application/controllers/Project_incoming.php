<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_incoming extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
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
        $pusat	            = $this->db->get_where('warehouse',array('category'=>'project'))->result_array();

		$CATEGORY = '1,6,7,8';
		$SQL_OLD = "(SELECT a.no_po, 'PO' AS typ, a.nm_supplier AS ket_name, a.created_by FROM tran_po_header a WHERE a.category='rutin' AND (a.status='WAITING IN' OR a.status='IN PARSIAL') AND a.status_id='1' ORDER BY a.no_po ASC)";
		$SQL_NEW = "(SELECT
						a.no_po,
						'PO' AS typ,
						c.nm_supplier AS ket_name,
						c.created_by,
						'' as no_surat_jalan
					FROM
						tran_po_detail a
						LEFT JOIN tran_po_header c ON a.no_po = c.no_po
						LEFT JOIN con_nonmat_new b ON a.id_barang = b.code_group 
						LEFT JOIN tran_rfq_detail x ON a.no_po=x.no_po AND a.id_barang=x.id_barang
						LEFT JOIN tran_pr_detail y ON x.no_rfq=y.no_rfq AND x.id_barang=y.id_barang
					WHERE
						a.qty_in < a.qty_po 
						AND c.category = 'rutin' 
						AND c.status_id = '1'
						AND b.category_awal IN ($CATEGORY)
						AND y.in_gudang = 'project'
					GROUP BY 
						a.no_po, b.category_awal 
					ORDER BY 
						a.no_po) 
						UNION 
					(SELECT
						a.kode_trans AS no_po,
						'Retur' AS typ,
						d.nm_customer AS ket_name,
						c.created_by,
						CONCAT(' - ',c.no_surat_jalan) as no_surat_jalan
					FROM
						warehouse_adjustment_detail a
						LEFT JOIN warehouse_adjustment c ON a.kode_trans = c.kode_trans
						LEFT JOIN customer d ON c.no_ipp = d.id_customer
					WHERE
						(a.check_qty_oke < a.qty_oke OR a.check_qty_oke is null)
						AND c.category = 'retur accessories' 
						AND c.status_id = '1'
					GROUP BY 
						a.kode_trans
					ORDER BY 
						a.kode_trans)";

		$no_po				= $this->db->query($SQL_NEW."UNION
												(SELECT b.no_non_po AS no_po, 'NON-PO' AS typ, b.pic AS ket_name, b.created_by, '' as no_surat_jalan FROM tran_non_po_header b WHERE b.category='rutin' AND (b.status='WAITING IN' OR b.status='IN PARSIAL') ORDER BY b.no_non_po ASC)")->result_array();
		$list_po	= $this->db->where_in('category',['incoming project','pengembalian project'])->group_by('no_ipp')->get_where('warehouse_adjustment',array('status_id'=>'1'))->result_array();
		$data_gudang= $this->db->where_in('category',['incoming project','pengembalian project'])->group_by('id_gudang_ke')->get_where('warehouse_adjustment',array('status_id'=>'1'))->result_array();
										
		$data = array( 
			'title'			=> 'Warehouse Project >> Incoming',
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
		history('View incoming item project');
		$this->load->view('Project/incoming/index',$data);
	}

    public function server_side_incoming(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

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

            $no_trans = $row['kode_trans']." / ".$row['no_ipp'];
            if($row['category'] == 'pengembalian project'){
                $no_trans = $row['kode_trans'];
            }

			$nm_gudang = get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']);
			$checkGudang = substr($nm_gudang,0,2);
			if($checkGudang == 'C1'){
				$nm_gudang = 'Gudang Project '.get_name('customer', 'nm_customer', 'id_customer', $nm_gudang);
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>"; 
			$nestedData[]	= "<div align='center'>".$row['no_ipp']." ".$row['no_so']."</div>"; 
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($TGL_INCOMING))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_gudang)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'])."</div>";
			$nestedData[]	= "<div>".ucwords(strtolower($row['pic']))."</div>";
			$nestedData[]	= "<div>".$row['no_surat_jalan']."</div>";
			$nestedData[]	= "<div>".ucwords(strtolower($NM_USER))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i', strtotime($row['created_date']))."</div>";
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
		$where_category ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}
		
		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang_ke = '".$gudang."' ";
		}

		// $where_category = " AND a.id_gudang_ke = '".$category."' ";
		
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
		    WHERE 1=1 AND a.category IN ('incoming project','pengembalian project','pusat to subgudang project accessories') AND a.status_id='1'
				".$where_no_po."
				".$where_gudang."
				".$where_category."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_surat_jalan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'no_ipp',
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function modal_pengembalian_barang(){
        $data 			= $this->input->post();

		$gudang 	    = $data['gudang'];
		$tanggal_trans 	= $data['tanggal_trans'];
		$pic 	        = $data['pic'];
		$note 	        = $data['note'];
		$no_ros 	    = $data['no_ros'];
		$no_po 	    = $data['no_po'];

		$data_session	= $this->session->userdata;
		$this->db->where('created_by', $data_session['ORI_User']['username']);
		$this->db->delete('temp_server_side');

		$data = array(
			'no_po' => $no_po,
			'gudang' => $gudang,
			'tanggal_trans' => $tanggal_trans,
			'pic' => $pic,
			'note' => $note,
			'no_ros' 	=> $no_ros
		);

		$this->load->view('Project/incoming/modal_pengembalian_barang', $data);
	}

    public function server_side_pengembalian_barang(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_modal_request_material(
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

			$data_session	= $this->session->userdata;
			$queryx 	= "SELECT * FROM temp_server_side WHERE id_mat = '".$row['id']."' AND created_by = '".$data_session['ORI_User']['username']."' AND category='pengembalian project' ";
			$get_temp 	= $this->db->query($queryx)->result();
			$qty   		= (!empty($get_temp))?number_format($get_temp[0]->qty,2):'';
			$ket    	= (!empty($get_temp))?$get_temp[0]->ket:'';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_category'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['code_group'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['material_name'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['brand'])."</div>";
			$nestedData[]	= "<div align='left'>
                                    <input type='hidden' name='detail[".$nomor."][id]' id='id_".$nomor."' value='".$row['id']."'>
                                    <input type='hidden' name='detail[".$nomor."][code_group]' id='code_".$nomor."' value='".$row['code_group']."'>
									<input type='text' name='detail[".$nomor."][qty]' style='width:100%' id='qty_".$nomor."' data-no='".$nomor."' value='".$qty."' class='form-control input-sm text-center maskM2 qtypack'>
									</div>";
			
			$nestedData[]	= "<div align='left'><input type='text' name='detail[".$nomor."][ket]' style='width:100%' id='ket_".$nomor."' data-no='".$nomor."' value='".$ket."' class='form-control input-sm text-left ket'><script type='text/javascript'>$('.maskM2').autoNumeric('init', {mDec: '2', aPad: false});</script></div>";

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

	public function query_data_json_modal_request_material($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_pusat ='';

		$sql = "
			SELECT
				a.*,
				b.category AS nm_category
			FROM
				con_nonmat_new a
				LEFT JOIN con_nonmat_category_awal b ON a.category_awal=b.id
		    WHERE 1=1
				".$where_pusat." AND a.status='1' AND deleted_date IS NULL
			AND(
				a.code_group LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.material_name LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.brand LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.category LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.category',
			2 => 'code_group',
			3 => 'material_name',
			4 => 'spec',
			5 => 'brand',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function process_pengembalian_barang(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;

		// $detail			= $data['detail'];
		$detail			= $this->db->get_where('temp_server_side', array('category'=>'pengembalian project','created_by'=>$data_session['ORI_User']['username']))->result_array();
		$gudang	        = $data['gudang'];
		$tanggal	    = $data['tanggal_trans'];
		$pic		    = $data['pic'];
		$note		    = $data['note'];
		$no_ros		    = $data['no_ros'];
		$no_po		    = $data['no_po'];
		$no_surat_jalan		    = $data['no_surat_jalan'];
		$Ym 			= date('ym');
        $UserName       = $data_session['ORI_User']['username'];
        $DateTime       = date('Y-m-d H:i:s');
		// print_r($data);
		// exit;

		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRS".$Ym.$urut2;

		$ArrDeatilAdj	 = array();
		$ArrayStock	 = array();
		$SUM_MAT = 0;
		foreach($detail AS $val => $valx){
			$qty_in 	= str_replace(',','',$valx['qty']);
			if($qty_in > 0){
				$SUM_MAT += $qty_in;
				$rest_pusat	= $this->db->get_where('con_nonmat_new', array('id'=>$valx['id_mat']))->result();

				//detail adjustmeny
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['id_material_req'] 	= $rest_pusat[0]->id;
				$ArrDeatilAdj[$val]['id_material'] 		= $rest_pusat[0]->code_group;
				$ArrDeatilAdj[$val]['nm_material'] 		= $rest_pusat[0]->material_name;
				$ArrDeatilAdj[$val]['id_category'] 		= $rest_pusat[0]->spec;
				$ArrDeatilAdj[$val]['nm_category'] 		= $rest_pusat[0]->brand;
				$ArrDeatilAdj[$val]['qty_order'] 		= $qty_in;
				$ArrDeatilAdj[$val]['qty_oke'] 			= $qty_in;
				$ArrDeatilAdj[$val]['keterangan'] 		= $valx['ket'];
				$ArrDeatilAdj[$val]['update_by'] 		= $UserName;
				$ArrDeatilAdj[$val]['update_date'] 		= $DateTime;

                $ArrayStock[$val]['id'] 		= $rest_pusat[0]->code_group;
                $ArrayStock[$val]['qty'] 		= $qty_in;
			}
		}

		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'tanggal' 			=> $tanggal,
			'category' 			=> 'pengembalian project',
			'jumlah_mat' 		=> $SUM_MAT,
			'id_gudang_dari' 	=> NULL,
			'kd_gudang_dari' 	=> NULL,
			'id_gudang_ke' 		=> $gudang,
			'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $gudang),
			'pic' 		        => $pic,
			'note' 		        => $note,
			'no_ros' 		    => $no_ros,
			'no_surat_jalan' 		    => $no_surat_jalan,
			'created_by' 		=> $UserName,
			'created_date' 		=> $DateTime
		);

		// print_r($ArrInsertH);
		// print_r($ArrDeatilAdj);
		// exit;
		$this->db->trans_start();
            if(!empty($ArrayStock)){
                move_warehouse_barang_stok($ArrayStock, null, $gudang, $kode_trans);
            }

			$this->db->insert('warehouse_adjustment', $ArrInsertH);
			$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
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
			history("Material request subgudang : ".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}

    public function save_temp_mutasi(){
		$data 			 	= $this->input->post();
		$data_session		= $this->session->userdata;
		$printby			= $data_session['ORI_User']['username'];

		$id			      	= $data['id'];
		$sudah_request	  	= str_replace(',','',$data['qty']);
		$ket_request	  	= $data['ket'];
		$category	  	    = 'pengembalian project';

		$ArrInsertH = array(
			'category' 		=> $category,
			'id_mat' 		=> $id,
			'qty'   	  	=> $sudah_request,
			'ket' 		  	=> $ket_request,
			'created_by' 	=> $printby,
			'created_date' 	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('id_mat', $id);
			$this->db->where('created_by', $printby);
			$this->db->where('category', $category);
			$this->db->delete('temp_server_side');

			$this->db->insert('temp_server_side', $ArrInsertH);
		$this->db->trans_complete();

	}

    public function modal_detail(){
		$kode_trans = $this->uri->segment(3);
		$tanda     	= $this->uri->segment(4);

		$result			= $this->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();
		$result_header	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();

		$data = array(
			'result' 	=> $result,
			'tanda' 	=> $tanda,
			'checked' 	=> $result_header[0]->checked,
			'kode_trans'=> $result_header[0]->kode_trans,
			'no_po' 	=> $result_header[0]->no_ipp,
			'no_ipp' 	=> $result_header[0]->no_ipp,
			'qty_spk' 	=> $result_header[0]->qty_spk,
			'no_ros' 	=> $result_header[0]->no_ros,
			'no_surat_jalan' 	=> $result_header[0]->no_surat_jalan,
			'tanggal' 	=> (!empty($result_header[0]->tanggal))?date('d-M-Y',strtotime($result_header[0]->tanggal)):'',
			'id_milik' 	=> get_name('production_detail','id_milik','no_spk',$result_header[0]->no_spk),
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 		=> date('d F Y', strtotime($result_header[0]->created_date))

		);

		$this->load->view('Project/incoming/modal_detail', $data);
	}

	public function modal_incoming(){
		$data = $this->input->post();
		
		$gudang_before 	= $data['gudang_before'];
		$no_po 			= $data['no_po'];
		$pic 			= strtolower($data['pic']);
		$note 			= strtolower($data['note']);
		$no_ros			= $data['no_ros'];
		$tanggal_trans	= $data['tanggal_trans'];
		$category		= $data['category'];

		$tanda = substr($no_po,0,3);

		if($tanda = 'RTR'){
			$sql 	= "	SELECT 
							a.* 
						FROM 
							warehouse_adjustment_detail a
						WHERE 
							a.kode_trans='".$no_po."'
							AND (a.check_qty_oke < a.qty_oke OR a.check_qty_oke is null)";
						}
		else{
		$sql 	= "	SELECT 
						a.* 
					FROM 
						tran_po_detail a 
						LEFT JOIN con_nonmat_new b ON a.id_barang = b.code_group
					WHERE 
						a.no_po='".$no_po."' 
						AND b.category_awal IN ($category)
						AND a.qty_in < a.qty_po";
		}
		$result	= $this->db->query($sql)->result_array();
		
		$data = array(
			'no_po' => $no_po,
			'tanggal_trans'=> $tanggal_trans,
			'gudang'=> $gudang_before,
			'pic' 	=> $pic,
			'tanda' 	=> $tanda,
			'note' 	=> $note,
			'no_ros'=> $no_ros,
			'result'=> $result
		);
		
		$this->load->view('Project/incoming/modal_incoming', $data);
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
			$histHlp = "Adjustment incoming project to ".$nm_gudang_ke." / ".$no_po;
			
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
									c.coa,
									c.spec,
									c.brand
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
				$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->spec;
				$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->brand;
				
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
				$ArrDeatilChk[$val]['id_category'] 	= $restWhDetail[0]->spec;
				$ArrDeatilChk[$val]['nm_category'] 	= $restWhDetail[0]->brand;
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
					$ArrHist[$val]['ket'] 				= 'incoming project';
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
					$ArrHistNew[$val]['ket'] 				= 'incoming project (insert new)';
					$ArrHistNew[$val]['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistNew[$val]['update_date'] 		= date('Y-m-d H:i:s');
				}
			}

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $no_po,
				'tanggal' 			=> $tanggal_trans,
				'no_ros' 			=> $no_ros,
				'category' 			=> 'incoming project',
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
				insert_jurnal_stock($grouping_temp,NULL,$gudang,$kode_trans,'incoming project','penambahan gudang project','incoming project');
			}
			history($histHlp);
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
		}
		echo json_encode($Arr_Data);
	}

	public function confirm_retur(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$no_po			= $data['no_po'];
		$no_ros			= $data['no_ros'];
		$gudang			= $data['gudang'];
		$pic			= $data['pic'];
		$note			= $data['note'];
		$tanggal_trans	= $data['tanggal_trans'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];

		$getHeader 			= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$no_po))->result_array();
		$id_customer 		= $getHeader[0]['no_ipp'];
		$gudang_customer 	= (!empty(getSubGudangCustomer($id_customer)))?getSubGudangCustomer($id_customer):getSubGudangProject();
		$gudang_project 	= getGudangProject();
		
		$ArrUpdateDetail = [];
		$ArrUpdateStock = [];
		$ArrUpdateStockRetur = [];
		$ArrDetailCheck = [];

		foreach($addInMat AS $val => $valx){
			$qtyIN 		= str_replace(',','',$valx['qty_in']);
			
			//update adjustmnet detail
			$getInBefore 	= $this->db->get_where('warehouse_adjustment_detail',array('id'=>$valx['id']))->result_array();
			$check_qty_oke 	= (!empty($getInBefore[0]['check_qty_oke']))?$getInBefore[0]['check_qty_oke']:0;
			$ArrUpdateDetail[$val]['id'] 				= $valx['id'];
			$ArrUpdateDetail[$val]['check_qty_oke'] 	= $check_qty_oke + $qtyIN;
			
			//update stock
			$getStock 		= $this->db->get_where('warehouse_rutin_stock',array('code_group'=>$valx['code_group'], 'gudang'=>$gudang_customer))->result_array();
			$stockRetur 	= (!empty($getStock[0]['retur']))?$getStock[0]['retur']:0;

			$ArrUpdateStock[$val]['id'] 		= $valx['code_group'];
			$ArrUpdateStock[$val]['qty_good'] 	= $qtyIN;

			$ArrUpdateStockRetur[$val]['id'] 		= $getStock[0]['id'];
			$ArrUpdateStockRetur[$val]['retur'] 	= $stockRetur - $qtyIN;

			//detail adjustment check
			$ArrDetailCheck[$val]['kode_trans'] 	= $no_po;
			$ArrDetailCheck[$val]['id_detail'] 		= $valx['id'];
			$ArrDetailCheck[$val]['id_material'] 	= $valx['code_group'];
			$ArrDetailCheck[$val]['qty_order'] 		= $valx['qty_order'];
			$ArrDetailCheck[$val]['qty_oke'] 		= $qtyIN ;
			$ArrDetailCheck[$val]['keterangan'] 	= strtolower($valx['keterangan']);
			$ArrDetailCheck[$val]['update_by'] 		= $data_session['ORI_User']['username'];
			$ArrDetailCheck[$val]['update_date'] 	= date('Y-m-d H:i:s');
		}

		//grouping sum
		$temp = [];
		$grouping_temp = [];
		$key = 0;
		foreach($ArrUpdateStock as $value) { $key++;
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']]['good'] = 0;
			}
			$temp[$value['id']]['good'] += $value['qty_good'];

			$grouping_temp[$value['id']]['id'] 		= $value['id'];
			$grouping_temp[$value['id']]['qty'] 	= $temp[$value['id']]['good'];
		}

		$this->db->trans_start();
			if(!empty($ArrUpdateStockRetur)){
				$this->db->update_batch('warehouse_rutin_stock', $ArrUpdateStockRetur, 'id');
			}
			if(!empty($ArrUpdateStockRetur)){
				$this->db->update_batch('warehouse_adjustment_detail', $ArrUpdateDetail,'id');
			}
			if(!empty($ArrUpdateStockRetur)){
				$this->db->insert_batch('warehouse_adjustment_check', $ArrDetailCheck);
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
			if(!empty($grouping_temp)){
				move_warehouse_barang_stok($grouping_temp, $gudang_customer, $gudang_project, $no_po);
			}
		}
		echo json_encode($Arr_Data);
	}

}
?>