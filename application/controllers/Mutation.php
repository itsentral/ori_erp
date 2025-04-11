<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mutation extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('mutation_model');
		$this->load->model('Jurnal_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

    public function index(){
		$uri_approve = $this->uri->segment(3);
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		if(!empty($uri_approve)){
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/index/app';
		}
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group		= $this->master_model->getArray('groups',array(),'id','name');
		$material	    = $this->master_model->getDataOrderBy('raw_materials','delete','N','nm_material');
		$title_add 		= (empty($uri_approve))?' Request':' Approval';
		$data = array(
			'title'			=> 'Warehouse Material >> Retur'.$title_add,
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'uri_approve'	=> $uri_approve,
			'material'		=> $material
		);
		history('View mutation material'); 
		$this->load->view('Mutation/index',$data);
	}

    public function get_data_json_mutation(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_mutation(
			$requestData['type'],
			$requestData['material'],
			$requestData['uri_approve'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$TANDA = $requestData['uri_approve'];
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
			$REJECT = (!empty($row['deleted']))?"<br><span class='text-red text-bold'>Reject</span>":'';
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_trans'].$REJECT."</div>";
			$ADJTYPE = ($row['adjustment_type'] == 'mutasi')?'retur':$row['adjustment_type'];
			$nestedData[]	= "<div align='center'>".strtoupper($ADJTYPE)."</div>";
			$gudang_dari 	= (!empty($row['id_gudang_dari']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_dari']):$row['kd_gudang_dari']." ".strtoupper($row['adjustment_type']);
			$gudang_ke 		= (!empty($row['id_gudang_ke']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']):$row['kd_gudang_ke']." ".strtoupper($row['adjustment_type']);
			$nestedData[]	= "<div align='left'>".strtoupper($gudang_dari)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($gudang_ke)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['pic'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_ba'])."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'],4)."</div>";
			$nestedData[]	= "<div align='left'>".get_nama_user($row['created_by'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";

                $detail     = "";
                $edit       = "";
                $approve      = "";
                $print      = "";
				if(empty($TANDA)){
                	$detail	= "<a href='".base_url('mutation/add/'.$row['kode_trans'].'/view')."' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";
                }
				if($Arr_Akses['update']=='1' AND $row['checked'] == 'N' AND empty($TANDA)){
                    $edit	= "<a href='".base_url('mutation/add/'.$row['kode_trans'])."' class='btn btn-sm btn-primary' title='Edit'><i class='fa fa-edit'></i></a>";
                }
                if($Arr_Akses['download']=='1'){
                    $print	= "<a href='".base_url('mutation/print_new/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
                }
                if($Arr_Akses['approve']=='1' AND $row['checked'] == 'N' AND $row['deleted'] == NULL AND !empty($TANDA)){
                    $approve	= "<a href='".base_url('mutation/approve/'.$row['kode_trans'])."' class='btn btn-sm btn-success' title='Approve'><i class='fa fa-check'></i></a>";
                }
            
			$nestedData[]	= "<div align='left'>
                                    ".$detail."
									".$print."
                                    ".$edit."
                                    ".$approve."
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

	public function query_data_json_mutation($type, $material, $uri_approve, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		$where_by = "";
		// $where_by = " AND a.created_by != 'json'";
		// if($type <> '0'){
		// 	$where = " AND a.adjustment_type='".$type."' ";
		// }

		$WHERE_APP = "";
		if(!empty($uri_approve)){
			$WHERE_APP = " AND a.checked='N' AND a.deleted IS NULL ";
		}
		
		$whereMaterial = "";
		// if($material <> '0'){
		// 	$whereMaterial = " AND b.id_material='".$material."' ";
		// }
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				warehouse_adjustment a,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.category = 'mutation material' AND a.status_id = '1' ".$where." ".$whereMaterial." ".$WHERE_APP." ".$where_by."
			AND(
				a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.pic LIKE '%".$this->db->escape_like_str($like_value)."%'
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
			4 => 'id_gudang_ke'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function add($kode_trans=null,$view=null){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			
			// print_r($data);
			// exit;

			$UserName = $data_session['ORI_User']['username'];
			$dateTime = date('Y-m-d H:i:s');


			$kode_trans 	    = $data['kode_trans'];
			$adjustment_type 	= $data['adjustment_type'];
			$no_ba 				= strtolower($data['no_ba']);
			$id_gudang_dari_m 	= $data['id_gudang_dari_m'];
			$id_gudang_ke_m 	= $data['id_gudang_ke_m'];
			$kd_gudang_dari_m 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
			$kd_gudang_ke_m 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
			$pic_m 				= strtolower($data['pic_m']);
			$detail 			= $data['detail'];
			
            if(empty($kode_trans)){
                $Ym 			= date('ym');
                $srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
                $numrowMtr		= $this->db->query($srcMtr)->num_rows();
                $resultMtr		= $this->db->query($srcMtr)->result_array();
                $angkaUrut2		= $resultMtr[0]['maxP'];
                $urutan2		= (int)substr($angkaUrut2, 7, 4);
                $urutan2++;
                $urut2			= sprintf('%04s',$urutan2);
                $kode_trans		= "TRS".$Ym.$urut2;
            }

            $ArrDetail = array();
            $SUM = 0;
			foreach ($detail as $key => $value) {
                $QTY            = str_replace(',','',$value['qty']);
                $ID_MATERIAL    = $value['material'];
                $EXPIRED        = $value['expired'];

                if($QTY > 0 AND $ID_MATERIAL <> '0'){
                    $SUM += $QTY;
                    $GET_MATERIAL = $this->db->get_where('raw_materials',array('id_material'=>$ID_MATERIAL))->result();
                    
                    $nm_material = (!empty($GET_MATERIAL))?$GET_MATERIAL[0]->nm_material:NULL;
                    $id_category = (!empty($GET_MATERIAL))?$GET_MATERIAL[0]->id_category:NULL;
                    $nm_category = (!empty($GET_MATERIAL))?$GET_MATERIAL[0]->nm_category:NULL;

                    $ArrDetail[] = array(
                        'kode_trans' 		=> $kode_trans,
                        'id_material' 		=> $ID_MATERIAL,
                        'nm_material' 		=> $nm_material,
                        'id_category' 		=> $id_category,
                        'nm_category' 		=> $nm_category,
                        'qty_order' 		=> $QTY,
                        'qty_oke' 			=> $QTY,
                        'expired_date' 		=> ($adjustment_type == 'mutasi')?$EXPIRED:NULL,
                        'lot_number' 		=> strtolower($value['lot_number']),
                        'keterangan' 		=> strtolower($value['reason']),
                        'update_by' 		=> $UserName,
                        'update_date' 		=> $dateTime
                    );
                }
            }

            $ArrHeader = array(
				'kode_trans' 		=> $kode_trans,
				'category' 			=> 'mutation material',
				'adjustment_type' 	=> $adjustment_type,
				'jumlah_mat' 		=> $SUM,
				'id_gudang_dari' 	=> ($adjustment_type == 'mutasi')?$id_gudang_dari_m:NULL,
				'kd_gudang_dari' 	=> ($adjustment_type == 'mutasi')?$kd_gudang_dari_m:'ADJUSTMENT '.strtoupper($adjustment_type),
				'id_gudang_ke' 		=> ($adjustment_type == 'mutasi')?$id_gudang_ke_m:NULL,
				'kd_gudang_ke' 		=> ($adjustment_type == 'mutasi')?$kd_gudang_ke_m:NULL,
				'pic' 				=> ($adjustment_type == 'mutasi')?$pic_m:NULL,
				'no_ba' 			=> $no_ba,
				'created_by' 		=> $UserName,
				'created_date' 		=> $dateTime
			);
			
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// exit;

            $CHECK_ADJUSTMENT = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
			
			$this->db->trans_start();
                if(empty($CHECK_ADJUSTMENT)){
                    $this->db->insert('warehouse_adjustment', $ArrHeader);
                    $this->db->insert_batch('warehouse_adjustment_detail', $ArrDetail);
                }
                else{
                    $this->db->where('kode_trans', $kode_trans);
                    $this->db->update('warehouse_adjustment', $ArrHeader);

                    $this->db->where('kode_trans', $kode_trans);
                    $this->db->delete('warehouse_adjustment_detail');

                    $this->db->insert_batch('warehouse_adjustment_detail', $ArrDetail);
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
				history("Mutation material ".$adjustment_type." : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['read'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			
			$data_Group		= $this->master_model->getArray('groups',array(),'id','name');
			$gudang         = $this->db->where_in('category',['subgudang','produksi'])->get_where('warehouse',array('status'=>'Y'))->result_array();
			$data           = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
			$data_detail    = $this->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();
            $material	    = $this->master_model->getDataOrderBy('raw_materials','delete','N','nm_material');
			
			$data = array(
				'title'			=> 'Add Retur',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'kode_trans'	=> $kode_trans,
				'material'	    => $material,
				'view'	        => $view,
				'data'	        => $data,
				'data_detail'	=> $data_detail,
				'gudang'		=> $gudang
			);
			$this->load->view('Mutation/add',$data);
		}
	}

    public function list_gudang_ke(){
		$gudang		= $this->input->post('gudang');
		$tandax		= $this->input->post('tandax');

		if($gudang <> '0'){
			$queryIpp	= "SELECT b.urut2, b.category FROM  warehouse b WHERE b.id = '".$gudang."' LIMIT 1";
			$restIpp	= $this->db->query($queryIpp)->result();
			$category = $restIpp[0]->category;

			if($tandax == 'MOVE'){
				$whLef = " id != '".$gudang."' AND status = 'Y' ";
			}
			else{
				$whLef = " urut2 > ".$restIpp[0]->urut2;
			}
			// echo $category;
			if($category == 'subgudang'){
				$WHERE_2 = "AND category='pusat'";
			}
			else{
				$WHERE_2 = "AND category='subgudang'";
			}

			$query	 	= "SELECT id, kd_gudang, nm_gudang FROM warehouse WHERE ".$whLef." ".$WHERE_2." ORDER BY urut ASC";
			// echo $query;
			$Q_result	= $this->db->query($query)->result();

			$Opt 		= (!empty($Q_result))?'Select An Warehouse':'List Empty - Not Found';
		}
		if($gudang == '0'){
			$Opt = 'List Empty';
		}

		$option = "<option value='0'>".$Opt."</option>";
		if($gudang <> '0'){
		foreach($Q_result as $row)
			{
				$option .= "<option value='".$row->id."'>".strtoupper($row->nm_gudang)."</option>";
			}
		}
		echo json_encode(array(
			'option' => $option
		));
	}

    public function list_expired_date(){
		$id_gudang_ke		= $this->input->post('id_gudang_ke');
		$id_material		= $this->input->post('id_material');
		
		$query	 	= "SELECT expired FROM warehouse_stock_expired WHERE id_gudang = '".$id_gudang_ke."' AND id_material = '".$id_material."' GROUP BY expired ORDER BY expired ASC";
		$Q_result	= $this->db->query($query)->result();
		// echo $query;
		if(!empty($Q_result)){
			$option = "<option value='0'>Select Expired</option>";
			foreach($Q_result as $row){
				if($row->expired <> NULL AND $row->expired <> '0000-00-00'){
					$option .= "<option value='".$row->expired."'>".date('d-M-Y', strtotime($row->expired))."</option>";
				}
			}
		}
		
		if(empty($Q_result)){
			$option = "<option value='0'>Expired Not Found</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

    public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

        $material	 = $this->master_model->getDataOrderBy('raw_materials','delete','N','nm_material');
    	
		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][material]' class='chosen_select form-control input-sm material'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($material AS $row){
				  $d_Header .= "<option value='".$row->id_material."'>".strtoupper($row->id_material.' - '.$row->nm_material)."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][lot_number]' placeholder='Lot Number/Received Dat' class='form-control input-md text-left'></td>";
			$d_Header .= "<td align='left'>";
                $d_Header .= "<select name='detail[".$id."][expired]' class='chosen_select form-control input-sm expired'>";
				$d_Header .= "<option value='0'>List Empty</option>";
				$d_Header .= "</select>";
            $d_Header .= "</td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][qty]' placeholder='Qty' class='form-control input-md text-center autoNumeric4'></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][reason]' placeholder='Reason' class='form-control input-md text-left'></td>";
			
            $d_Header .= "<td align='center'>";
				$d_Header .= "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>";
			$d_Header .= "<td align='center' colspan='5'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

    public function print_new(){
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
			'kode_trans' => $kode_trans,
		);
		history('Print mutasi material '.$kode_trans);
		$this->load->view('Print/print_mutasi', $data);
	}

    public function approve($kode_trans=null,$view=null){
		if($this->input->post()){
			$data 			= $this->input->post();
			$data_session	= $this->session->userdata;

			$UserName = $data_session['ORI_User']['username'];
			$dateTime = date('Y-m-d H:i:s');
			
			$DateTime = date('Y-m-d H:i:s');

			$kode_trans 	    = $data['kode_trans'];
			$detail 			= $data['detail'];

            $GET_HEADER = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();

            $id_gudang_dari 		= $GET_HEADER[0]->id_gudang_dari;
            $id_gudang_ke 			= $GET_HEADER[0]->id_gudang_ke;

            $kd_gudang_dari 		= $GET_HEADER[0]->kd_gudang_dari;
            $kd_gudang_ke 			= $GET_HEADER[0]->kd_gudang_ke;

            $ArrDetail = array();
            $ArrDetailCheck = array();
            $ArrUpdateStock = array();
            $SUM = 0;
			foreach ($detail as $key => $value) {
                $QTY            = str_replace(',','',$value['qty_check']);
                $SUM += $QTY;

                $GET_DETAIL = $this->db->get_where('warehouse_adjustment_detail',array('id'=>$value['id']))->result();
				$getGudang2 = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->result();		
				$gudang2 = $getGudang2[0]->category;
				
				if($gudang2 == 'subgudang'){
				//$get_price_book_produksi = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$key))->result();
				//$PRICE_INCOMING = (!empty($get_price_book_produksi[0]->price_book))?$get_price_book_pusat[0]->price_book:0;

				$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3,'id_material'=>$key),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE_INCOMING=$harga_jurnal_akhir2->harga;
				
				}elseif($gudang2 == 'produksi'){
				//$get_price_book_produksi = $this->db->order_by('id','desc')->get_where('price_book_produksi',array('id_material'=>$key))->result();
				//$PRICE_INCOMING = (!empty($get_price_book_produksi[0]->price_book))?$get_price_book_pusat[0]->price_book:0;

				$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$key),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
				
				}
		
				
                $ArrDetail[] = array(
                    'id' 		        => $value['id'],
                    'check_qty_oke' 	=> $QTY,
                    'check_keterangan' 	=> strtolower($value['reason_check']),
                    'update_by' 	    => $UserName,
                    'update_date'   	=> $dateTime
                );

                $ArrDetailCheck[] = array(
                    'id_detail' 		=> $value['id'],
                    'kode_trans' 		=> $kode_trans,
                    'id_material' 		=> $GET_DETAIL[0]->id_material,
                    'nm_material' 		=> $GET_DETAIL[0]->nm_material,
                    'id_category' 		=> $GET_DETAIL[0]->id_category,
                    'nm_category' 		=> $GET_DETAIL[0]->nm_category,
                    'qty_order' 		=> $GET_DETAIL[0]->qty_order,
                    'qty_oke' 			=> $QTY,
                    'expired_date' 		=> $GET_DETAIL[0]->expired_date,
                    'keterangan' 		=> strtolower($value['reason_check']),
                    'update_by' 		=> $UserName,
                    'update_date' 		=> $dateTime
                );

                $ArrUpdateStock[] = array(
                    'id'    => $GET_DETAIL[0]->id_material,
                    'qty' 	=> $QTY
                );
            }

            $ArrHeader = array(
				'jumlah_mat_check' 	=> $SUM,
				'checked' 			=> 'Y',
				'checked_by' 		=> $UserName,
				'checked_date' 		=> $dateTime
			);

            //grouping sum
            $temp = [];
			$grouping_temp = [];
            foreach($ArrUpdateStock as $value) {
                if(!array_key_exists($value['id'], $temp)) {
                    $temp[$value['id']] = 0;
                }
                $temp[$value['id']] += $value['qty'];

				$grouping_temp[$value['id']]['id'] 			= $value['id'];
				$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']];
				$grouping_temp[$value['id']]['price'] 	    = (!empty($PRICE_INCOMING))?$PRICE_INCOMING:0;
            }

            $ArrStock = array();
            $ArrHist = array();
            $ArrStockInsert = array();
            $ArrHistInsert = array();

            $ArrStock2 = array();
            $ArrHist2 = array();
            $ArrStockInsert2 = array();
            $ArrHistInsert2 = array();
			$ArrJurnalNew2  = array();
			$ArrJurnalNew  = array();
            foreach ($temp as $key => $value) {
                //PENGURANGAN GUDANG
                $rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key))->result();

                if(!empty($rest_pusat)){
                    $ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
                    $ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
                    $ArrStock[$key]['update_by'] 	= $UserName;
                    $ArrStock[$key]['update_date'] 	= $dateTime;

                    $ArrHist[$key]['id_material'] 	= $key;
                    $ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
                    $ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
                    $ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
                    $ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
                    $ArrHist[$key]['id_gudang'] 		= $id_gudang_dari;
                    $ArrHist[$key]['kd_gudang'] 		= $kd_gudang_dari;
                    $ArrHist[$key]['id_gudang_dari'] 	= $id_gudang_dari;
                    $ArrHist[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
                    $ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_ke;
                    $ArrHist[$key]['kd_gudang_ke'] 		= $kd_gudang_ke;
                    $ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
                    $ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
                    $ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
                    $ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
                    $ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
                    $ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
                    $ArrHist[$key]['no_ipp'] 			= $kode_trans;
                    $ArrHist[$key]['jumlah_mat'] 		= $value;
                    $ArrHist[$key]['ket'] 				= 'pengurangan gudang';
                    $ArrHist[$key]['update_by'] 		= $UserName;
                    $ArrHist[$key]['update_date'] 		= $dateTime;
					
						
					$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->row();
					$coa_gudang = $coa_1->coa_1;
					$kategori_gudang = $coa_1->category;
					
					$id_material = 	$rest_pusat[0]->id_material;
					$stokjurnalakhir=0;
					$nilaijurnalakhir=0;
					$stok_jurnal_akhir = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
					
					$tanggal		= date('Y-m-d');
					$Bln 			= substr($tanggal,5,2);
					$Thn 			= substr($tanggal,0,4);
					$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
					
					$QTY_OKE      = $value;
					
					$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
					
					
					$ArrJurnalNew[$key]['id_material'] 		= $rest_pusat[0]->id_material;
					$ArrJurnalNew[$key]['idmaterial'] 		= $rest_pusat[0]->idmaterial;
					$ArrJurnalNew[$key]['nm_material'] 		= $rest_pusat[0]->nm_material;
					$ArrJurnalNew[$key]['id_category'] 		= $rest_pusat[0]->id_category;
					$ArrJurnalNew[$key]['nm_category'] 		= $rest_pusat[0]->nm_category;
					$ArrJurnalNew[$key]['id_gudang'] 			= $id_gudang_dari;
					$ArrJurnalNew[$key]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
					$ArrJurnalNew[$key]['id_gudang_dari'] 	    = $id_gudang_dari;
					$ArrJurnalNew[$key]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
					$ArrJurnalNew[$key]['id_gudang_ke'] 		= $id_gudang_ke;
					$ArrJurnalNew[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrJurnalNew[$key]['qty_stock_awal'] 		= $stokjurnalakhir;
					$ArrJurnalNew[$key]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
					$ArrJurnalNew[$key]['kode_trans'] 			= $kode_trans;
					$ArrJurnalNew[$key]['tgl_trans'] 			= $DateTime;
					$ArrJurnalNew[$key]['qty_out'] 			= $QTY_OKE;
					$ArrJurnalNew[$key]['ket'] 				= 'Retur';
					$ArrJurnalNew[$key]['harga'] 			= $PRICE;
					$ArrJurnalNew[$key]['harga_bm'] 		= 0;
					$ArrJurnalNew[$key]['nilai_awal_rp']	= $nilaijurnalakhir;
					$ArrJurnalNew[$key]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
					$ArrJurnalNew[$key]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
					$ArrJurnalNew[$key]['update_by'] 		= $UserName;
					$ArrJurnalNew[$key]['update_date'] 		= $DateTime;
					$ArrJurnalNew[$key]['no_jurnal'] 		= $Nojurnal;
					$ArrJurnalNew[$key]['coa_gudang'] 		= $coa_gudang;
				
                }
                else{
                    $sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
                    $restMat	= $this->db->query($sqlMat)->result();

                    $ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
                    $ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
                    $ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
                    $ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
                    $ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
                    $ArrStockInsert[$key]['id_gudang'] 		= $id_gudang_dari;
                    $ArrStockInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
                    $ArrStockInsert[$key]['qty_stock'] 		= 0 - $value;
                    $ArrStockInsert[$key]['update_by'] 		= $UserName;
                    $ArrStockInsert[$key]['update_date'] 	= $dateTime;

                    $ArrHistInsert[$key]['id_material'] 	= $key;
                    $ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
                    $ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
                    $ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
                    $ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
                    $ArrHistInsert[$key]['id_gudang'] 		= $id_gudang_dari;
                    $ArrHistInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
                    $ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang_dari;
                    $ArrHistInsert[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
                    $ArrHistInsert[$key]['id_gudang_ke'] 	= $id_gudang_ke;
                    $ArrHistInsert[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
                    $ArrHistInsert[$key]['qty_stock_awal'] 	    = 0;
                    $ArrHistInsert[$key]['qty_stock_akhir']     = 0 - $value;
                    $ArrHistInsert[$key]['qty_booking_awal']    = 0;
                    $ArrHistInsert[$key]['qty_booking_akhir']   = 0;
                    $ArrHistInsert[$key]['qty_rusak_awal'] 	    = 0;
                    $ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
                    $ArrHistInsert[$key]['no_ipp'] 			= $kode_trans;
                    $ArrHistInsert[$key]['jumlah_mat'] 		= $value;
                    $ArrHistInsert[$key]['ket'] 			= 'pengurangan gudang (insert new)';
                    $ArrHistInsert[$key]['update_by'] 		= $UserName;
                    $ArrHistInsert[$key]['update_date'] 	= $dateTime;
					
					
					
					$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->row();
					$coa_gudang = $coa_1->coa_1;
					$kategori_gudang = $coa_1->category;
					
					$id_material = 	$restMat[0]->id_material;
					$stokjurnalakhir=0;
					$nilaijurnalakhir=0;
					$stok_jurnal_akhir = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
					
					$tanggal		= date('Y-m-d');
					$Bln 			= substr($tanggal,5,2);
					$Thn 			= substr($tanggal,0,4);
					$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
					
					$QTY_OKE      = $value;
					
					$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
					
					$ArrJurnalNew[$key]['id_material'] 		= $restMat[0]->id_material;
					$ArrJurnalNew[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
					$ArrJurnalNew[$key]['nm_material'] 		= $restMat[0]->nm_material;
					$ArrJurnalNew[$key]['id_category'] 		= $restMat[0]->id_category;
					$ArrJurnalNew[$key]['nm_category'] 		= $restMat[0]->nm_category;
					$ArrJurnalNew[$key]['id_gudang'] 			= $id_gudang_dari;
					$ArrJurnalNew[$key]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
					$ArrJurnalNew[$key]['id_gudang_dari'] 	    = $id_gudang_dari;
					$ArrJurnalNew[$key]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
					$ArrJurnalNew[$key]['id_gudang_ke'] 		= $id_gudang_ke;
					$ArrJurnalNew[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrJurnalNew[$key]['qty_stock_awal'] 		= $stokjurnalakhir;
					$ArrJurnalNew[$key]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
					$ArrJurnalNew[$key]['kode_trans'] 			= $kode_trans;
					$ArrJurnalNew[$key]['tgl_trans'] 			= $DateTime;
					$ArrJurnalNew[$key]['qty_out'] 			= $QTY_OKE;
					$ArrJurnalNew[$key]['ket'] 				= 'Retur';
					$ArrJurnalNew[$key]['harga'] 			= $PRICE;
					$ArrJurnalNew[$key]['harga_bm'] 		= 0;
					$ArrJurnalNew[$key]['nilai_awal_rp']	= $nilaijurnalakhir;
					$ArrJurnalNew[$key]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
					$ArrJurnalNew[$key]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
					$ArrJurnalNew[$key]['update_by'] 		= $UserName;
					$ArrJurnalNew[$key]['update_date'] 		= $DateTime;
					$ArrJurnalNew[$key]['no_jurnal'] 		= $Nojurnal;
					$ArrJurnalNew[$key]['coa_gudang'] 		= $coa_gudang;
					
				}

				//PENAMBAHAN GUDANG
				$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$key))->result();

				if(!empty($rest_pusat)){
					$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
					$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrStock2[$key]['update_by'] 	=  $UserName;
					$ArrStock2[$key]['update_date'] 	= $dateTime;

					$ArrHist2[$key]['id_material'] 	= $key;
					$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
					$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
					$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
					$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
					$ArrHist2[$key]['id_gudang'] 		= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang'] 		= $kd_gudang_ke;
					$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrHist2[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
					$ArrHist2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
					$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
					$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrHist2[$key]['qty_booking_awal'] = $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_booking_akhir']= $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['no_ipp'] 			= $kode_trans;
					$ArrHist2[$key]['jumlah_mat'] 		= $value;
					$ArrHist2[$key]['ket'] 				= 'penambahan gudang';
					$ArrHist2[$key]['update_by'] 		= $UserName;
					$ArrHist2[$key]['update_date'] 		= $dateTime;
					
					
					$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_ke))->row();
					$coa_gudang = $coa_1->coa_1;
					$kategori_gudang = $coa_1->category;
					
					$id_material = 	$rest_pusat[0]->id_material;
					$stokjurnalakhir=0;
					$nilaijurnalakhir=0;
					$stok_jurnal_akhir = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
					
					$tanggal		= date('Y-m-d');
					$Bln 			= substr($tanggal,5,2);
					$Thn 			= substr($tanggal,0,4);
					$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
					
					$QTY_OKE      = $value;
					
					
					$coa_2   = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->row();
					$coa_gudang2 = $coa_2->coa_1;
					$kategori_gudang2 = $coa_2->category;
					
					
					
					$Gudang2 = $kategori_gudang2;
					
				if($Gudang2 == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($Gudang2 == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($Gudang2 == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
					
					
				$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
			
					
				}
					
					$stokjurnalakhir2=0;
					$nilaijurnalakhir2=0;
					$stok_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir2)) $stokjurnalakhir2=$stok_jurnal_akhir2->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir2)) $nilaijurnalakhir2=$stok_jurnal_akhir2->nilai_akhir_rp;
					
									
					$Price_1 = (($PRICE*$QTY_OKE) + ($PRICE2*$stokjurnalakhir2));
					$Price_2 = ($QTY_OKE+$stokjurnalakhir2);
					
					$PRICENEW = 0;
					if($Price_1 > 0 AND $Price_2 > 0){
						$PRICENEW = $Price_1 / $Price_2;
					}
					
					
					$ArrJurnalNew2[$key]['id_material'] 		= $rest_pusat[0]->id_material;
					$ArrJurnalNew2[$key]['idmaterial'] 		= $rest_pusat[0]->idmaterial;
					$ArrJurnalNew2[$key]['nm_material'] 		= $rest_pusat[0]->nm_material;
					$ArrJurnalNew2[$key]['id_category'] 		= $rest_pusat[0]->id_category;
					$ArrJurnalNew2[$key]['nm_category'] 		= $rest_pusat[0]->nm_category;
					$ArrJurnalNew2[$key]['id_gudang'] 			= $id_gudang_ke;
					$ArrJurnalNew2[$key]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrJurnalNew2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrJurnalNew2[$key]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
					$ArrJurnalNew2[$key]['id_gudang_ke'] 		= $id_gudang_ke;
					$ArrJurnalNew2[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrJurnalNew2[$key]['qty_stock_awal'] 	= $stokjurnalakhir2;
					$ArrJurnalNew2[$key]['qty_stock_akhir'] 	= $stokjurnalakhir2+$QTY_OKE;
					$ArrJurnalNew2[$key]['kode_trans'] 		= $kode_trans;
					$ArrJurnalNew2[$key]['tgl_trans'] 			= $DateTime;
					$ArrJurnalNew2[$key]['qty_in'] 			= $QTY_OKE;
					$ArrJurnalNew2[$key]['ket'] 				= 'mutasi adjustmnent';
					$ArrJurnalNew2[$key]['harga'] 				= $PRICENEW;
					$ArrJurnalNew2[$key]['harga_bm'] 			= 0;
					$ArrJurnalNew2[$key]['nilai_awal_rp']		= $nilaijurnalakhir2;
					$ArrJurnalNew2[$key]['nilai_trans_rp']		= $PRICE*$QTY_OKE;
					$ArrJurnalNew2[$key]['nilai_akhir_rp']		= ($stokjurnalakhir2+$QTY_OKE)*$PRICENEW;
					$ArrJurnalNew2[$key]['update_by'] 			= $UserName;
					$ArrJurnalNew2[$key]['update_date'] 		= $DateTime;
					$ArrJurnalNew2[$key]['no_jurnal'] 			= '-';
					$ArrJurnalNew2[$key]['coa_gudang'] 		= $coa_gudang;
				
				
                }
                else{
                    $sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
                    $restMat	= $this->db->query($sqlMat)->result();

                    $ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
                    $ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
                    $ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
                    $ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
                    $ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
                    $ArrStockInsert2[$key]['id_gudang'] 	= $id_gudang_ke;
                    $ArrStockInsert2[$key]['kd_gudang'] 	= $kd_gudang_ke;
                    $ArrStockInsert2[$key]['qty_stock'] 	= $value;
                    $ArrStockInsert2[$key]['update_by'] 	= $UserName;
                    $ArrStockInsert2[$key]['update_date'] 	= $dateTime;

                    $ArrHistInsert2[$key]['id_material'] 	= $key;
                    $ArrHistInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
                    $ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
                    $ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
                    $ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
                    $ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_ke;
                    $ArrHistInsert2[$key]['kd_gudang'] 		= $kd_gudang_ke;
                    $ArrHistInsert2[$key]['id_gudang_dari'] = $id_gudang_dari;
                    $ArrHistInsert2[$key]['kd_gudang_dari'] = $kd_gudang_dari;
                    $ArrHistInsert2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
                    $ArrHistInsert2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
                    $ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
                    $ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value;
                    $ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
                    $ArrHistInsert2[$key]['qty_booking_akhir']  = 0;
                    $ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
                    $ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
                    $ArrHistInsert2[$key]['no_ipp'] 			= $kode_trans;
                    $ArrHistInsert2[$key]['jumlah_mat'] 		= $value;
                    $ArrHistInsert2[$key]['ket'] 				= 'penambahan gudang (insert new)';
                    $ArrHistInsert2[$key]['update_by'] 		    = $UserName;
                    $ArrHistInsert2[$key]['update_date'] 		= $dateTime;
					
					$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang_ke))->row();
					$coa_gudang = $coa_1->coa_1;
					$kategori_gudang = $coa_1->category;
					
					$id_material = 	$restMat[0]->id_material;
					$stokjurnalakhir=0;
					$nilaijurnalakhir=0;
					$stok_jurnal_akhir = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
					
					$tanggal		= date('Y-m-d');
					$Bln 			= substr($tanggal,5,2);
					$Thn 			= substr($tanggal,0,4);
					$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
					
					$QTY_OKE      = $value;
					
					
					$coa_2   = $this->db->get_where('warehouse', array('id'=>$id_gudang_dari))->row();
					$coa_gudang2 = $coa_2->coa_1;
					$kategori_gudang2 = $coa_2->category;
					
					
					
					
					$Gudang2 = $kategori_gudang2;
					
				if($Gudang2 == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


				}elseif($Gudang2 == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
		
				}elseif($Gudang2 == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
			
					
				}
					
					
				$GudangFrom = $kategori_gudang;
				if($GudangFrom == 'pusat'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;

					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;


				}elseif($GudangFrom == 'subgudang'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
		
				}elseif($GudangFrom == 'produksi'){
					//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
					//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
					$bmunit = 0;
					$bm = 0;
					$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
					if(!empty($harga_jurnal_akhir2)) $PRICE2=$harga_jurnal_akhir2->harga;
			
					
				}
					$stokjurnalakhir2=0;
					$nilaijurnalakhir2=0;
					$stok_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$id_material),1)->row();
					if(!empty($stok_jurnal_akhir2)) $stokjurnalakhir2=$stok_jurnal_akhir2->qty_stock_akhir;
					
					if(!empty($stok_jurnal_akhir2)) $nilaijurnalakhir2=$stok_jurnal_akhir2->nilai_akhir_rp;
					
					$PRICENEW = (($PRICE*$QTY_OKE) + ($PRICE2*$stokjurnalakhir2))/($QTY_OKE+$stokjurnalakhir2);
					
					
					$ArrJurnalNew2[$key]['id_material'] 		= $restMat[0]->id_material;
					$ArrJurnalNew2[$key]['idmaterial'] 			= $restMat[0]->idmaterial;
					$ArrJurnalNew2[$key]['nm_material'] 		= $restMat[0]->nm_material;
					$ArrJurnalNew2[$key]['id_category'] 		= $restMat[0]->id_category;
					$ArrJurnalNew2[$key]['nm_category'] 		= $restMat[0]->nm_category;
					$ArrJurnalNew2[$key]['id_gudang'] 			= $id_gudang_ke;
					$ArrJurnalNew2[$key]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke); 
					$ArrJurnalNew2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrJurnalNew2[$key]['kd_gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari);
					$ArrJurnalNew2[$key]['id_gudang_ke'] 		= $id_gudang_ke;
					$ArrJurnalNew2[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrJurnalNew2[$key]['qty_stock_awal'] 	= $stokjurnalakhir2;
					$ArrJurnalNew2[$key]['qty_stock_akhir'] 	= $stokjurnalakhir2+$QTY_OKE;
					$ArrJurnalNew2[$key]['kode_trans'] 		= $kode_trans;
					$ArrJurnalNew2[$key]['tgl_trans'] 			= $DateTime;
					$ArrJurnalNew2[$key]['qty_in'] 			= $QTY_OKE;
					$ArrJurnalNew2[$key]['ket'] 				= 'mutasi adjustmnent';
					$ArrJurnalNew2[$key]['harga'] 				= $PRICENEW;
					$ArrJurnalNew2[$key]['harga_bm'] 			= 0;
					$ArrJurnalNew2[$key]['nilai_awal_rp']		= $nilaijurnalakhir2;
					$ArrJurnalNew2[$key]['nilai_trans_rp']		= $PRICE*$QTY_OKE;
					$ArrJurnalNew2[$key]['nilai_akhir_rp']		= ($stokjurnalakhir2+$QTY_OKE)*$PRICENEW;
					$ArrJurnalNew2[$key]['update_by'] 			= $UserName;
					$ArrJurnalNew2[$key]['update_date'] 		= $DateTime;
					$ArrJurnalNew2[$key]['no_jurnal'] 			= '-';
					$ArrJurnalNew2[$key]['coa_gudang'] 		= $coa_gudang;
				
                }
            }
			
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// print_r($ArrDetailCheck);

			// print_r($ArrStock);
			// print_r($ArrHist);
			// print_r($ArrStockInsert);
			// print_r($ArrHistInsert);
			// print_r($ArrStock2);
			// print_r($ArrHist2);
			// print_r($ArrStockInsert2);
			// print_r($grouping_temp);
			// exit;
			
			$this->db->trans_start();
                $this->db->where('kode_trans', $kode_trans);
                $this->db->update('warehouse_adjustment', $ArrHeader);
				if(!empty($grouping_temp)){
					insert_jurnal_retur($grouping_temp,$id_gudang_dari,$id_gudang_ke,$kode_trans,'retur material','retur material','retur material');
				}
                $this->db->update_batch('warehouse_adjustment_detail',$ArrDetail,'id');
                $this->db->insert_batch('warehouse_adjustment_check',$ArrDetailCheck);
				
				$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew);
				 
				 if(!empty($ArrJurnalNew2)){
					 $this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew2);
				}

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
    
                if(!empty($ArrStock2)){
                    $this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
                }
                if(!empty($ArrHist2)){
                    $this->db->insert_batch('warehouse_history', $ArrHist2);
                }
    
                if(!empty($ArrStockInsert2)){
                    $this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
                }
                if(!empty($ArrHistInsert2)){
                    $this->db->insert_batch('warehouse_history', $ArrHistInsert2);
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
				insertDataGroupReport($ArrUpdateStock, $id_gudang_dari, $id_gudang_ke, $kode_trans, null, null, null);
				history("Approve mutation material : ".$kode_trans);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['read'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			
			$data_Group		= $this->master_model->getArray('groups',array(),'id','name');
			$gudang         = $this->db->get_where('warehouse',array('status'=>'Y'))->result_array();
			$data           = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result();
			$data_detail    = $this->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();
            $material	    = $this->master_model->getDataOrderBy('raw_materials','delete','N','nm_material');
			
			$data = array(
				'title'			=> 'Approve Retur',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'kode_trans'	=> $kode_trans,
				'material'	    => $material,
				'view'	        => $view,
				'data'	        => $data,
				'data_detail'	=> $data_detail,
				'gudang'		=> $gudang
			);
			$this->load->view('Mutation/approve',$data);
		}
	}

	public function reject($kode_trans=null,$view=null){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;

		$UserName = $data_session['ORI_User']['username'];
		$dateTime = date('Y-m-d H:i:s');

		$kode_trans 	    = $data['kode_trans'];

		$ArrHeader = array(
			// 'jumlah_mat_check' 	=> $SUM,
			'checked' 			=> 'Y',
			'checked_by' 		=> $UserName,
			'checked_date' 		=> $dateTime
		);
		
		$this->db->trans_start();
			$this->db->where('kode_trans', $kode_trans);
			$this->db->update('warehouse_adjustment', $ArrHeader);
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
			history("Reject mutation material : ".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}
}