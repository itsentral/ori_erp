<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_adjustment extends CI_Controller {
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
		$pusat				= $this->db->query("SELECT * FROM warehouse WHERE category='pusat' ORDER BY urut ASC")->result_array();
		$no_po				= $this->db->query("SELECT no_po FROM tran_material_po_header WHERE status='WAITING IN' OR status='IN PARSIAL' ORDER BY no_po ASC ")->result_array();
		$query	 	= "SELECT id_material, nm_material FROM raw_materials WHERE `delete` = 'N' ORDER BY nm_material ASC";
		$material	= $this->db->query($query)->result();
			
		$data = array(
			'title'			=> 'Warehouse Project >> Adjustment',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'material'		=> $material,
			'pusat'			=> $pusat,
			'no_po'			=> $no_po
		);
		history('View adjustment gudang project'); 
		$this->load->view('Project/adjustment/index',$data);
	}

    public function server_side_adjustment(){
		
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_adjustment(
			$requestData['type'],
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
			$nestedData[]	= "<div align='center'>".ucwords(strtolower($row['adjustment_type']))."</div>";
			$gudang_dari 	= (!empty($row['id_gudang_dari']))?ucwords(strtolower(get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_dari']))):ucwords(strtolower($row['kd_gudang_dari']." ".$row['adjustment_type']));
			$gudang_ke 		= (!empty($row['id_gudang_ke']))?ucwords(strtolower(get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']))):ucwords(strtolower($row['kd_gudang_ke']." ".$row['adjustment_type']));
			if($row['adjustment_type'] == 'plus' OR $row['adjustment_type'] == 'minus'){
			$gudang_ke 		= (!empty($row['id_gudang_ke']))?ucwords(strtolower(get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']))):ucwords(strtolower($row['kd_gudang_ke']." ".$row['adjustment_type']));
			}
			
			$nestedData[]	= "<div align='left'>".$gudang_dari."</div>";
			$nestedData[]	= "<div align='left'>".$gudang_ke."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_material']." - ".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='left'>".$row['spec']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'],2)."</div>";
			$nestedData[]	= "<div align='left'>".$row['pic']."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_ba']."</div>";
			$nestedData[]	= "<div align='left'>".$row['note']."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower(get_name('users','nm_lengkap','username',$row['created_by'])))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i', strtotime($row['created_date']))."</div>";
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

	public function query_data_json_adjustment($type, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($type <> '0'){
			$where = " AND a.adjustment_type='".$type."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_ba,
				b.id_material,
				b.nm_material,
				b.expired_date,
				c.spec
			FROM
				warehouse_adjustment a 
				LEFT JOIN warehouse_adjustment_detail b ON a.kode_trans=b.kode_trans
				LEFT JOIN con_nonmat_new c ON b.id_material=c.code_group,
				(SELECT @row:=0) r
		    WHERE 1=1 AND a.category = 'adjustment project' ".$where." and a.status_id = '1' AND a.deleted_date IS NULL
			AND(
				a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_dari LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.pic LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_ba LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
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
			6 => 'spec',
			7 => 'jumlah_mat',
			8 => 'expired_date',
			9 => 'pic',
			10 => 'no_ba',
			11 => 'note',
			12 => 'created_by',
			13 => 'created_date'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add(){
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
			$kd_gudang_ke_m 	= get_name('department', 'nm_dept', 'id', $id_gudang_ke_m);
			$pic_m 				= strtolower($data['pic_m']);
			
			$id_gudang_ke 		= $data['id_gudang_ke'];
			$kd_gudang_ke 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
			$pic 				= strtolower($data['pic']);
			
			$Ym 			= date('ym');
			
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
			
			$ArrHeader = array(
				'kode_trans' 		=> $kode_trans,
				'category' 			=> 'adjustment project',
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
				'nm_material' 		=> get_name('con_nonmat_new', 'material_name', 'code_group', $id_material),
				'id_category' 		=> get_name('con_nonmat_new', 'category_awal', 'code_group', $id_material),
				'nm_category' 		=> get_name('con_nonmat_new', 'spec', 'code_group', $id_material),
				'qty_order' 		=> $qty_oke,
				'qty_oke' 			=> $qty_oke,
				'no_ba' 			=> $no_ba,
				'keterangan' 		=> $keterangan,
				'update_by' 		=> $data_session['ORI_User']['username'],
				'update_date' 		=> date('Y-m-d H:i:s'),
				'check_qty_oke' 	=> $qty_oke,
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
			
			//MUTASI
			if($adjustment_type == 'mutasi'){
				//pengurangan gudang asal
				$check_gudang_dari	= "SELECT b.* FROM warehouse_rutin_stock b WHERE b.code_group = '".$id_material."' AND b.gudang='".$id_gudang_dari_m."' LIMIT 1";
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
					$ArrStockDari['stock'] 		= $rest_gudang_dari[0]->stock - $qty_oke;
					$ArrStockDari['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockDari['update_date'] 	= date('Y-m-d H:i:s');
					
					$ArrHistDari['code_group'] 	= $rest_gudang_dari[0]->code_group;
					$ArrHistDari['category_awal'] 		= $rest_gudang_dari[0]->category_awal;
					$ArrHistDari['category_code'] 	= $rest_gudang_dari[0]->category_code;
					$ArrHistDari['material_name'] 	= $rest_gudang_dari[0]->material_name;
					$ArrHistDari['id_gudang'] 		= $id_gudang_dari_m;
					$ArrHistDari['gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
					$ArrHistDari['id_gudang_dari'] 	= $id_gudang_dari_m;
					$ArrHistDari['gudang_dari'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_dari_m);
					$ArrHistDari['id_gudang_ke'] 	= $id_gudang_ke_m;
					$ArrHistDari['gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke_m);
					$ArrHistDari['qty_stock_awal'] 	= $rest_gudang_dari[0]->stock;
					$ArrHistDari['qty_stock_akhir'] = $rest_gudang_dari[0]->stock - $qty_oke;
					$ArrHistDari['qty_rusak_awal'] 		= $rest_gudang_dari[0]->rusak;
					$ArrHistDari['qty_rusak_akhir'] 	= $rest_gudang_dari[0]->rusak;
					$ArrHistDari['no_trans'] 				= $kode_trans;
					$ArrHistDari['jumlah_qty'] 			= $qty_oke;
					$ArrHistDari['ket'] 				= 'pengurangan gudang mutasi';
					$ArrHistDari['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistDari['update_date'] 		= date('Y-m-d H:i:s');	
				}	
			}
			//MINUS
			if($adjustment_type == 'minus'){
				//pengurangan gudang asal
				$check_gudang_dari	= "SELECT b.* FROM warehouse_rutin_stock b WHERE b.code_group = '".$id_material."' AND b.gudang='".$id_gudang_ke."' LIMIT 1";
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
					$ArrStockDari['stock'] 			= $rest_gudang_dari[0]->stock - $qty_oke;
					$ArrStockDari['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockDari['update_date'] 	= date('Y-m-d H:i:s');
					
					$ArrHistDari['code_group'] 	= $rest_gudang_dari[0]->code_group;
					$ArrHistDari['category_awal'] 		= $rest_gudang_dari[0]->category_awal;
					$ArrHistDari['category_code'] 	= $rest_gudang_dari[0]->category_code;
					$ArrHistDari['material_name'] 	= $rest_gudang_dari[0]->material_name;
					$ArrHistDari['id_gudang'] 		= $id_gudang_ke;
					$ArrHistDari['gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrHistDari['id_gudang_dari'] 	= NULL;
					$ArrHistDari['gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
					$ArrHistDari['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHistDari['gudang_ke'] 	= get_name('department', 'nm_dept', 'id', $id_gudang_ke);
					$ArrHistDari['qty_stock_awal'] 	= $rest_gudang_dari[0]->stock;
					$ArrHistDari['qty_stock_akhir'] = $rest_gudang_dari[0]->stock - $qty_oke;
					$ArrHistDari['qty_rusak_awal'] 		= $rest_gudang_dari[0]->rusak;
					$ArrHistDari['qty_rusak_akhir'] 	= $rest_gudang_dari[0]->rusak;
					$ArrHistDari['no_trans'] 				= $kode_trans;
					$ArrHistDari['jumlah_qty'] 			= $qty_oke;
					$ArrHistDari['ket'] 				= 'pengurangan gudang mutasi';
					$ArrHistDari['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistDari['update_date'] 		= date('Y-m-d H:i:s');
					
				}
			}
			//PLUS
			if($adjustment_type == 'plus'){
				
				//penambahan gudang tujuan
				$check_gudang_ke	= "SELECT b.* FROM warehouse_rutin_stock b WHERE b.code_group = '".$id_material."' AND b.gudang='".$id_gudang_ke."' LIMIT 1";
				$rest_gudang_ke	= $this->db->query($check_gudang_ke)->result();
				
				if(!empty($rest_gudang_ke)){
					$ArrStockKe['id'] 				= $rest_gudang_ke[0]->id;
					$ArrStockKe['stock'] 		= $rest_gudang_ke[0]->stock + $qty_oke;
					$ArrStockKe['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrStockKe['update_date'] 		= date('Y-m-d H:i:s');
					
					$ArrHistKe['code_group'] 	= $rest_gudang_ke[0]->code_group;
					$ArrHistKe['category_awal'] 		= $rest_gudang_ke[0]->category_awal;
					$ArrHistKe['category_code'] 	= $rest_gudang_ke[0]->category_code;
					$ArrHistKe['material_name'] 	= $rest_gudang_ke[0]->material_name;
					$ArrHistKe['id_gudang'] 		= $id_gudang_ke;
					$ArrHistKe['gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrHistKe['id_gudang_dari'] 	= $id_gudang_dari_m;
					$ArrHistKe['gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
					$ArrHistKe['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHistKe['gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrHistKe['qty_stock_awal'] 	= $rest_gudang_ke[0]->stock;
					$ArrHistKe['qty_stock_akhir'] = $rest_gudang_ke[0]->stock + $qty_oke;
					$ArrHistKe['qty_rusak_awal'] 		= $rest_gudang_ke[0]->rusak;
					$ArrHistKe['qty_rusak_akhir'] 	= $rest_gudang_ke[0]->rusak;
					$ArrHistKe['no_trans'] 				= $kode_trans;
					$ArrHistKe['jumlah_qty'] 			= $qty_oke;
					$ArrHistKe['ket'] 				= 'penambahan gudang mutasi';
					$ArrHistKe['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrHistKe['update_date'] 		= date('Y-m-d H:i:s');

				}
				
				if(empty($rest_gudang_ke)){
					$sql_mat	= "SELECT a.* FROM con_nonmat_new a WHERE a.code_group = '".$id_material."' LIMIT 1 ";
					$rest_mat	= $this->db->query($sql_mat)->result();
					
					$ArrPlus['code_group'] 		= $rest_mat[0]->code_group;
					$ArrPlus['category_awal'] 	= $rest_mat[0]->category_awal;
					$ArrPlus['category_code'] 	= $rest_mat[0]->category_code;
					$ArrPlus['material_name'] 	= $rest_mat[0]->material_name;
					$ArrPlus['gudang'] 			= $id_gudang_ke;
					$ArrPlus['stock'] 			= $qty_oke;
					$ArrPlus['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrPlus['update_date'] 	= date('Y-m-d H:i:s');
					
					$ArrPlusHist['code_group'] 	= $rest_mat[0]->code_group;
					$ArrPlusHist['category_awal'] 		= $rest_mat[0]->category_awal;
					$ArrPlusHist['category_code'] 	= $rest_mat[0]->category_code;
					$ArrPlusHist['material_name'] 	= $rest_mat[0]->material_name;
					$ArrPlusHist['id_gudang'] 		= $id_gudang_ke;
					$ArrPlusHist['gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrPlusHist['id_gudang_dari'] 	= NULL;
					$ArrPlusHist['gudang_dari'] 	= 'ADJUSTMENT '.strtoupper($adjustment_type);
					$ArrPlusHist['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrPlusHist['gudang_ke'] 	= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
					$ArrPlusHist['qty_stock_awal'] 	= 0;
					$ArrPlusHist['qty_stock_akhir'] = $qty_oke;
					$ArrPlusHist['qty_rusak_awal'] 		= 0;
					$ArrPlusHist['qty_rusak_akhir'] 	= 0;
					$ArrPlusHist['no_trans'] 				= $kode_trans;
					$ArrPlusHist['jumlah_qty'] 			= $qty_oke;
					$ArrPlusHist['ket'] 				= 'penambahan gudang mutasi (insert new)';
					$ArrPlusHist['update_by'] 			= $data_session['ORI_User']['username'];
					$ArrPlusHist['update_date'] 		= date('Y-m-d H:i:s');
					
				}
				
			}
			
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// print_r($ArrStockDari);
			// print_r($ArrHistDari);
			// print_r($ArrStockKe);
			// print_r($ArrHistKe);
			// print_r($ArrPlus);
			// print_r($ArrPlusHist);
			
			// exit;
			
			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrHeader);
				$this->db->insert('warehouse_adjustment_detail', $ArrDetail);
				
				if(!empty($ArrStockDari)){
					$this->db->where('id',$rest_gudang_dari[0]->id);
					$this->db->update('warehouse_rutin_stock', $ArrStockDari);
				}
				if(!empty($ArrHistDari)){
					$this->db->insert('warehouse_rutin_history', $ArrHistDari);
				}
				if(!empty($ArrStockKe)){
					$this->db->where('id',$rest_gudang_ke[0]->id);
					$this->db->update('warehouse_rutin_stock', $ArrStockKe);
				}
				if(!empty($ArrHistKe)){
					$this->db->insert('warehouse_rutin_history', $ArrHistKe);
				}
				if(!empty($ArrPlus)){
					$this->db->insert('warehouse_rutin_stock', $ArrPlus);
				}
				if(!empty($ArrPlusHist)){
					$this->db->insert('warehouse_rutin_history', $ArrPlusHist);
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
				history("Adjustment gudang project ".$adjustment_type." : ".$kode_trans);
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

            $ArrGudang = ['indirect','project'];
			
			$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
			$barang     = $this->db->order_by('material_name, spec')->get_where('con_nonmat_new',array('deleted'=>'N','code_group <>'=>null))->result_array();
            $gudang_to  = $this->db->where_in('category',$ArrGudang)->order_by('id','desc')->get('warehouse')->result_array();
			
			$data = array(
				'title'			=> 'Add Adjustment Gudang Project',
				'action'		=> 'index',
				'row_group'		=> $data_Group,
				'akses_menu'	=> $Arr_Akses,
				'gudang_to'		=> $gudang_to,
				'barang'		=> $barang
			);
			$this->load->view('Project/adjustment/add',$data);
		}
	}
	
	public function excel_adjustment(){
		$type		= $this->uri->segment(3);
  		//membuat objek PHPExcel
  		set_time_limit(0);
  		ini_set('memory_limit','1024M');

  		$this->load->library("PHPExcel");
  		// $this->load->library("PHPExcel/Writer/Excel2007");
  		$objPHPExcel	= new PHPExcel();

  		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

        $where = "";
		if($type <> '0'){
			$where = " AND a.adjustment_type='".$type."' ";
		}
		
		$sql = "
				SELECT
					a.*,
					b.no_ba,
					b.nm_material,
					b.expired_date,
					c.spec
				FROM
					warehouse_adjustment a 
					LEFT JOIN warehouse_adjustment_detail b ON a.kode_trans=b.kode_trans
					LEFT JOIN con_nonmat_new c ON b.id_material=c.code_group
				WHERE 1=1 AND a.category = 'adjustment project' ".$where." and a.status_id = '1' AND a.deleted_date IS NULL
			";
		// echo $sql; exit;
  		$detail    = $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(13);
		$sheet->setCellValue('A'.$Row, 'List Adjustment Gudang Project');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		// if($type <> '0'){
        // $NewRow	= $NewRow +2;
		// $NextRow= $NewRow +1;

		// $sheet->setCellValue('A'.$NewRow, 'ADJUSTMENT TYPE');
		// $sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($tableBodyLeft);
		// $sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		// $sheet->getColumnDimension('A')->setAutoSize(true);

        // $sheet->setCellValue('C'.$NewRow, strtoupper($type));
		// $sheet->getStyle('C'.$NewRow.':C'.$NewRow)->applyFromArray($tableBodyLeft);
		// $sheet->mergeCells('C'.$NewRow.':C'.$NewRow);
		// $sheet->getColumnDimension('C')->setAutoSize(true);
		// }
		
		// if($material <> '0'){
        // $NewRow	= $NewRow +1;
		// $NextRow= $NewRow +1;

		// $sheet->setCellValue('A'.$NewRow, 'MATERIAL NAME');
		// $sheet->getStyle('A'.$NewRow.':B'.$NewRow)->applyFromArray($tableBodyLeft);
		// $sheet->mergeCells('A'.$NewRow.':B'.$NewRow);
		// $sheet->getColumnDimension('A')->setAutoSize(true);

        // $sheet->setCellValue('C'.$NewRow, strtoupper(get_name('raw_materials','nm_material','id_material',$material)));
		// $sheet->getStyle('C'.$NewRow.':C'.$NewRow)->applyFromArray($tableBodyLeft);
		// $sheet->mergeCells('C'.$NewRow.':C'.$NewRow);
		// $sheet->getColumnDimension('C')->setAutoSize(true);
		// }
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('B'.$NewRow, 'No Trans');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Type');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

        $sheet->setCellValue('D'.$NewRow, 'Gudang Dari');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Gudang Ke');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Material Name');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Spec');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Qty');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$sheet->setCellValue('I'.$NewRow, 'PIC');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		
		$sheet->setCellValue('J'.$NewRow, 'No BA');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		$sheet->setCellValue('K'.$NewRow, 'Ket');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		
		$sheet->setCellValue('L'.$NewRow, 'Created');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$sheet->setCellValue('M'.$NewRow, 'Created Date');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($tableHeader);
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
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

  				$awal_col++;
  				$status_date	= $row['kode_trans'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= strtoupper($row['adjustment_type']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$gudang_dari 	= (!empty($row['id_gudang_dari']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_dari']):$row['kd_gudang_dari']." ".strtoupper($row['adjustment_type']);
				$gudang_ke 		= (!empty($row['id_gudang_ke']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']):$row['kd_gudang_ke']." ".strtoupper($row['adjustment_type']);
				
				if($row['adjustment_type'] == 'plus' OR $row['adjustment_type'] == 'minus'){
					$gudang_ke 		= (!empty($row['id_gudang_ke']))?get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_ke']):$row['kd_gudang_ke']." ".strtoupper($row['adjustment_type']);
				}
		
				$awal_col++;
  				$status_date	= ucwords(strtolower($gudang_dari));
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= ucwords(strtolower($gudang_ke));
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= strtoupper($row['nm_material']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$spec	= strtoupper($row['spec']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $spec);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= $row['jumlah_mat'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= strtoupper($row['pic']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= strtoupper($row['no_ba']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= strtoupper($row['note']);
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= $row['created_by'];
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
				$awal_col++;
  				$status_date	= date('d-M-Y H:i:s', strtotime($row['created_date']));
  				$Cols			= getColsChar($awal_col);
  				$sheet->setCellValue($Cols.$awal_row, $status_date);
  				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

          

  			}
  		}

  		$sheet->setTitle('Project');
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
  		header('Content-Disposition: attachment;filename="list-adjustment-project.xls"');
  		//unduh file
  		$objWriter->save("php://output");
  	}

}