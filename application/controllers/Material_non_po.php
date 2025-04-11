<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Material_non_po extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('All_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

    public function pengajuan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/pengajuan';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$tanda				= $this->uri->segment(3);
		if($tanda == 'approve'){
			$tandax = 'Approval';
		}
		elseif($tanda == 'non_po'){
			$tandax = 'List';
		}else{
			$tandax = 'Pengajuan';
		}
		$data = array(
			'title'			=> 'Pembelian Material >> Non PO >> '.$tandax.' Material Non PO',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'tanda'			=> $tanda
		);
		history('View data pembelian material non-po');
		$this->load->view('Material_non_po/pengajuan',$data);
	}

    public function server_side_pengajuan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/pengajuan";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_pengajuan(
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
			
			$tanda = $requestData['tanda'];
			
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";

			$nestedData[]	= "<div align='center'>".strtoupper($row['no_non_po'])."</div>";

			$list_barang	= $this->db->get_where('tran_material_non_po_detail',array('no_non_po'=>$row['no_non_po']))->result_array();
			$arr_nmbarang = array();
			$arr_qty = array();
			$arr_tanggal = array();
			foreach($list_barang AS $val => $valx){
				$nm_satuan = 'kg';
				$arr_nmbarang[$val] = "&bull; ".strtoupper($valx['nm_material']);
				$arr_qty[$val] = "&bull; ".number_format($valx['qty_purchase']).' '.$nm_satuan;
				$tgl_dibutuhkan = ($valx['tgl_dibutuhkan'] <> '0000-00-00')?date('d-M-Y', strtotime($valx['tgl_dibutuhkan'])):'not set';
				$arr_tanggal[$val] = "&bull; ".$tgl_dibutuhkan;
			}
			$dt_nama_barang	= implode("<br>", $arr_nmbarang);
			$dt_qty	= implode("<br>", $arr_qty);
			$dt_tanggal	= implode("<br>", $arr_tanggal);

			$nestedData[]	= "<div align='left'>".$dt_nama_barang."</div>";
			$nestedData[]	= "<div align='left'>".$dt_qty."</div>";
			$nestedData[]	= "<div align='left'>".$dt_tanggal."</div>";
			
			
			if($row['app_status'] == 'N'){
				$warna 	= 'blue';
				$sts 	= 'WAITING APPROVAL';
			}
			elseif($row['app_status'] == 'Y'){
				$warna 	= 'green';
				$sts 	= 'WAITING EXPENSE REPORT';
			}
			else{
				$warna 	= 'red';
				$sts 	= 'REJECTED';
			}

			if(!empty($row['expense_date'])){
				$warna 	= 'blue';
				$sts 	= 'EXPENSE REPORT';
			}
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color: ".$warna.";'>".$sts."</span></div>";
				$view		= "<a href='".base_url('material_non_po/app_non_po/'.$row['no_non_po'].'/view')."'  class='btn btn-sm btn-warning' title='View' data-role='qtip'><i class='fa fa-eye'></i></a>";
				$edit		= "";
				$approve	= "";
				$cancel		= "";
				$expense		= "";
				
				if($tanda <> 'approve'){
					if($Arr_Akses['update']=='1'){
						if($row['app_status'] == 'N'){
							$edit		= "<a href='".base_url('material_non_po/app_non_po/'.$row['no_non_po'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>";
						}
						// if($row['app_status'] == 'Y'){
						// 	$expense	= "<a href='".base_url('material_non_po/expense_non_po/'.$row['no_non_po'])."' class='btn btn-sm btn-success' title='Expense Report' data-role='qtip'><i class='fa fa-credit-card'></i></a>";
						// }
					}
				}
				
				if($tanda == 'approve'){
					$view		= "";
					if($Arr_Akses['approve']=='1'){
						if($row['app_status'] == 'N'){
							$approve	= "<a href='".base_url('material_non_po/app_non_po/'.$row['no_non_po'].'/approve')."' class='btn btn-sm btn-info' title='Approve' data-role='qtip'><i class='fa fa-check'></i></a>";
						}
					}
				}
			$nestedData[]	= "<div align='left'>
									".$view."
                                    ".$edit."
									".$approve."
									".$cancel."
									".$expense."
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

	public function query_data_json_pengajuan($tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($tanda == 'approve'){
			$where = "AND a.app_status = 'N' ";
		}
		if($tanda == 'non_po'){
			$where = "AND a.app_status = 'Y' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				tran_material_non_po_header a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." AND (
				a.no_non_po LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.pic LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.no_non_po
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_non_po'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function app_non_po(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			$code_plan  	= $data['id'];
			$tanda        	= $data['tanda'];
			$approve        = $data['approve'];
			
			$pic        	= strtolower($data['pic']);
			$keterangan     = strtolower($data['keterangan']);
			
			$detail 		= $data['detail'];
			
			//approve
			$sts_app        = (!empty($data['sts_app']))?$data['sts_app']:'';
			$reason        	= (!empty($data['reason']))?$data['reason']:'';
			
			$ym = date('ym');
			
			
			$SUM_QTY = 0;
			$SUM_HARGA = 0;
			if(empty($approve)){
				$ArrDetail = array();
				if(!empty($detail)){
					foreach($detail AS $val => $valx){
						$qty 	= str_replace(',','',$valx['qty']);
						$harga 	= str_replace(',','',$valx['price_unit']);
						
						$SUM_QTY 	+= $qty;
						$SUM_HARGA 	+= $harga * $qty;
				
						$ArrDetail[$val]['id'] 				= $valx['id'];
						$ArrDetail[$val]['id_material'] 	= $valx['id_material'];
						$ArrDetail[$val]['idmaterial'] 	    = get_name('raw_materials','idmaterial','id_material',$valx['id_material']);
						$ArrDetail[$val]['nm_material'] 	= get_name('raw_materials','nm_material','id_material',$valx['id_material']);
						$ArrDetail[$val]['qty_purchase'] 	= $qty;
						$ArrDetail[$val]['price_unit'] 		= $harga;
						$ArrDetail[$val]['keterangan'] 		= strtolower($valx['keterangan']);
						$ArrDetail[$val]['tgl_dibutuhkan'] 	= $valx['tanggal'];
						$ArrDetail[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
						$ArrDetail[$val]['updated_date'] 	= $dateTime;
					}
				}
			}
			
			//header edit
			$ArrHeader		= array(
				'pic' 			=> $pic,
				'keterangan' 	=> $keterangan,
				'total_material'=> $SUM_QTY,
				'nilai_request' => $SUM_HARGA,
				'updated_by'	=> $data_session['ORI_User']['username'],
				'updated_date'	=> $dateTime
			);
			
			
			//header approve
			if(!empty($approve)){
				$ArrDetail = array();
				if(!empty($detail)){
					foreach($detail AS $val => $valx){
						$qty 	= str_replace(',','',$valx['qty']);
						$harga 	= str_replace(',','',$valx['price_unit']);
						
						$SUM_QTY 	+= $qty;
						$SUM_HARGA 	+= $harga * $qty;
				
						$ArrDetail[$val]['id'] 				= $valx['id'];
						$ArrDetail[$val]['id_material'] 	= $valx['id_material'];
						$ArrDetail[$val]['idmaterial'] 	    = get_name('raw_materials','idmaterial','id_material',$valx['id_material']);
						$ArrDetail[$val]['nm_material'] 	= get_name('raw_materials','nm_material','id_material',$valx['id_material']);
						$ArrDetail[$val]['qty_rev'] 		= $qty;
						$ArrDetail[$val]['price_unit_rev'] 	= $harga;
						$ArrDetail[$val]['keterangan'] 		= strtolower($valx['keterangan']);
						$ArrDetail[$val]['tgl_dibutuhkan'] 	= $valx['tanggal'];
						$ArrDetail[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
						$ArrDetail[$val]['updated_date'] 	= $dateTime;
						$ArrDetail[$val]['app_reason'] 		= strtolower($reason);
						$ArrDetail[$val]['app_status'] 		= $sts_app;
						$ArrDetail[$val]['app_by'] 			= $data_session['ORI_User']['username'];
						$ArrDetail[$val]['app_date'] 		= $dateTime;
					}
				}
				
				$ArrHeader		= array(
					'pic' 			=> $pic,
					'keterangan' 	=> $keterangan,
					'total_material_rev' 	=> $SUM_QTY,
					'nilai_request_rev' 	=> $SUM_HARGA,
					'app_status' 	=> $sts_app,
					'app_by'		=> $data_session['ORI_User']['username'],
					'app_date'		=> $dateTime,
					'app_reason' 	=> strtolower($reason),
				);
			}

			// echo $approve.'/';
            // echo $tanda;
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// exit;
			
			$this->db->trans_start();
				if(empty($approve)){
					$this->db->where(array('no_non_po' => $code_plan));
					$this->db->update('tran_material_non_po_header', $ArrHeader);

					$this->db->update_batch('tran_material_non_po_detail', $ArrDetail, 'id');
				}
				if(!empty($approve)){
					$this->db->where(array('no_non_po' => $code_plan));
					$this->db->update('tran_material_non_po_header', $ArrHeader);
					
					$this->db->update_batch('tran_material_non_po_detail', $ArrDetail, 'id');
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Process data failed. Please try again later ...',
					'status'	=> 0,
					'tanda'	=> $approve
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Process data success. Thanks ...',
					'status'	=> 1,
					'tanda'	=> $approve
				);
				history($tanda.' pengajuan material non-po '.$code_plan);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/pengajuan';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['read'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			
			$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
			$id 		= $this->uri->segment(3);
			$approve 	= $this->uri->segment(4);
			$non_po 	= $this->uri->segment(5);
			$header 	= $this->db->query("SELECT * FROM tran_material_non_po_header WHERE no_non_po='".$id."' ")->result();
			$detail 	= $this->db->query("SELECT * FROM tran_material_non_po_detail WHERE no_non_po='".$id."' ")->result_array();
			$datacoa 	= $this->db->query("SELECT * FROM coa_category WHERE tipe='NONRUTIN' ")->result_array();
			$raw_material		= $this->db->get_where('raw_materials',array('delete'=>'N'))->result_array();
			$tanda 		= (!empty($header))?'Edit':'Add';
			if(!empty($approve)){
				$tanda 		= ($approve == 'view')?'View':'Approve';
			}
			$data = array(
				'title'				=> $tanda.' Material Non PO',
					'action'		=> strtolower($tanda),
					'akses_menu'	=> $Arr_Akses,
					'header'		=> $header,
					'detail'		=> $detail,
					'datacoa'		=> $datacoa,
					'raw_material'	=> $raw_material,
					'approve'		=> $approve,
					'non_po'		=> $non_po,
					'id'			=> $id 
			);
			
			$this->load->view('Material_non_po/app_non_po',$data);
		}
	}

}