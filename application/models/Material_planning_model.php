<?php
class Material_planning_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	//INDEX
	public function index_material_planning(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Indeks Of Material Planning',
		  'action'		=> 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	=> $Arr_Akses
		);
		history('View Material Planning');
		$this->load->view('Material_planning/material_planning',$data);
	}	

	//DETAIL
	public function detail_material_planning(){
		$id_bq = $this->uri->segment(3);
		$type = $this->uri->segment(4);

		if($type == 'pipe'){
			$query	= "	SELECT
							a.id_bq AS id_bq,
							a.id_material AS id_material,
							a.nm_material AS nm_material,
							round( sum( ( a.last_cost * b.qty ) ), 3 ) AS last_cost 
						FROM
							( estimasi_total a LEFT JOIN so_bf_detail_header b ON ( ( a.id_milik = b.id_milik ) ) ) 
						WHERE
							( a.id_material <> '0' AND a.id_material <> 'MTL-1903000' )  
							AND a.id_bq='".$id_bq."'
						GROUP BY
							a.id_material,
							a.id_bq 
						ORDER BY
							a.nm_material";
			$non_frp		= $this->db->select("id_material, qty, 'pipe' AS type")->get_where('so_acc_and_mat', array('category <>'=>'mat', 'id_bq'=>$id_bq))->result_array();
			$material		= $this->db->get_where('so_acc_and_mat', array('category'=>'mat', 'id_bq'=>$id_bq))->result_array();
		}
		else{
			$query	= "	SELECT
							a.no_ipp AS id_bq,
							a.id_material AS id_material,
							b.nm_material AS nm_material,
							round( sum( ( a.berat ) ), 3 ) AS last_cost 
						FROM
							( planning_tanki_detail a LEFT JOIN raw_materials b ON ( ( a.id_material = b.id_material ) ) ) 
						WHERE
							( a.id_material <> '0' AND a.id_material <> 'MTL-1903000')  
							AND a.no_ipp='$id_bq'
							AND a.category='mat'
						GROUP BY
							a.id_material,
							a.no_ipp 
						ORDER BY
							b.nm_material";
			$non_frp		= $this->db->select("id_material, berat AS qty, 'tanki' AS type")->get_where('planning_tanki_detail', array('category'=>'acc', 'no_ipp'=>$id_bq))->result_array();
			$material		= array();
		}
		$result		= $this->db->query($query)->result_array();
		
		
		
		$data = array(
		  'id_bq' 		    => $id_bq,
		  'non_frp' 		=> $non_frp,
		  'material' 		=> $material,
		  'data_result' 	=> $result
		);
		$this->load->view('Material_planning/modal_detail_material_planing', $data);
	}

	//ADD
	public function add_get_query_material_planning(){
		if($this->input->post()){
			$tanda 			= $this->uri->segment(3);
		 	$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dataDetail		= $data['addMatPlanning'];
			$tgl_butuh		= $data['tgl_butuh'];
			$no_ipp			= $data['no_ipp'];
			$type			= $data['type'];
			
			if(!empty($data['add_acc_planning'])){
				$add_acc_planning	= $data['add_acc_planning'];
			}

			$ArrDeatil = array();
			$SUM_MAT = 0;
			$SUM_USE = 0;
			$SUM_PUR = 0;
			// if(empty($tanda)){
				foreach($dataDetail AS $val => $valx){
					$SUM_MAT += (int)str_replace(',', '', $valx['jumlah_mat']);
					$SUM_USE += (int)str_replace(',', '', $valx['use_stock']);
					$SUM_PUR += (int)str_replace(',', '', $valx['purchase']);
					$ArrDeatil[$val]['no_ipp']         = $no_ipp;
					$ArrDeatil[$val]['id_material']    = $valx['id_material'];
					$ArrDeatil[$val]['idmaterial']     = $valx['idmaterial'];
					$ArrDeatil[$val]['nm_material']    = $valx['nm_material'];
					$ArrDeatil[$val]['jumlah_mat']     = str_replace(',', '', $valx['jumlah_mat']);
					$ArrDeatil[$val]['use_stock']      = str_replace(',', '', $valx['use_stock']);
					$ArrDeatil[$val]['purchase']       = str_replace(',', '', $valx['purchase']);
					$ArrDeatil[$val]['moq']            = str_replace(',', '', $valx['moq']);
					$ArrDeatil[$val]['sisa_avl']       = str_replace(',', '', $valx['sisa_avl']);
					$ArrDeatil[$val]['reorder_point']  = str_replace(',', '', $valx['reorder_point']);
					$ArrDeatil[$val]['book_per_month'] = str_replace(',', '', $valx['book_per_month']);
					$ArrDeatil[$val]['max_stock']      = str_replace(',', '', $valx['max_stock']);
					$ArrDeatil[$val]['tanggal']    		= $tgl_butuh;
					$ArrDeatil[$val]['created_by']    	= $data_session['ORI_User']['username'];
					$ArrDeatil[$val]['created_date']    = date('Y-m-d H:i:s');
				}
			// }
			
			$ArrDetAcc	= array();
			// if(!empty($tanda)){
				if(!empty($data['add_acc_planning'])){
					foreach($add_acc_planning AS $val => $valx){
						// $SUM_PUR += (int)str_replace(',', '', $valx['purchase']);
						// $SUM_MAT += (int)str_replace(',', '', $valx['jumlah_mat']);
						$ArrDetAcc[$val]['no_ipp']         = $no_ipp;
						$ArrDetAcc[$val]['id_material']    = $valx['id_material'];
						$ArrDetAcc[$val]['code_group']    = $valx['code_group'];
						$ArrDetAcc[$val]['idmaterial']     = (empty($valx['nm_material']))?get_name('accessories','category','id',$valx['id_material']):NULL;
						$ArrDetAcc[$val]['nm_material']    = (empty($valx['nm_material']))?get_name('accessories','nama','id',$valx['id_material']):$valx['nm_material'];
						$ArrDetAcc[$val]['jumlah_mat']     = str_replace(',', '', $valx['jumlah_mat']);
						$ArrDetAcc[$val]['use_stock']      = 0;
						$ArrDetAcc[$val]['purchase']       = str_replace(',', '', $valx['purchase']);
						$ArrDetAcc[$val]['sudah_request']       = str_replace(',', '', $valx['purchase']);
						$ArrDetAcc[$val]['moq']            = 0;
						$ArrDetAcc[$val]['sisa_avl']       = 0;
						$ArrDetAcc[$val]['reorder_point']  = 0;
						$ArrDetAcc[$val]['book_per_month'] = 0;
						$ArrDetAcc[$val]['max_stock']      = 0;
						$ArrDetAcc[$val]['tanggal']    		= $tgl_butuh;
						$ArrDetAcc[$val]['satuan']    		= $valx['satuan'];
						$ArrDetAcc[$val]['created_by']    	= $data_session['ORI_User']['username'];
						$ArrDetAcc[$val]['created_date']    = date('Y-m-d H:i:s');
						$ArrDetAcc[$val]['in_gudang']    	= 'indirect';
						// $ArrDetAcc[$val]['in_gudang']    	= 'project';
					}
				}
			// }

			$ArrHeader = array(
				'no_ipp' => $no_ipp,
				'no_pr' => '',
				'jumlah_mat' => $SUM_MAT,
				'use_stock' => $SUM_USE,
				'purchase' => $SUM_PUR,
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			);

			if($type == 'pipe'){
				$ArrUpdate = array(
					'mat_plan_sts' => 'Y',
					'mat_plan_by' => $data_session['ORI_User']['username'],
					'mat_plan_date' => date('Y-m-d H:i:s')
				);
			}
			else{
				$ArrUpdate = array(
					'mat_plan_sts' => 'Y'
				);
			}

			//check di planning header ada atau tidak
			$check_plan = $this->db->get_where('warehouse_planning_header', array('no_ipp'=>$no_ipp))->result();

			// print_r($ArrHeader);
			// print_r($ArrDeatil);
			// print_r($ArrDetAcc);
			// exit;
			$this->db->trans_start();
				if(empty($check_plan)){
					$this->db->insert('warehouse_planning_header', $ArrHeader);
				}
				else{
					$this->db->where('no_ipp', $no_ipp);
					$this->db->update('warehouse_planning_header', $ArrHeader);
				}

				if(!empty($ArrDeatil)){
					$this->db->insert_batch('warehouse_planning_detail', $ArrDeatil);
				}
				if(!empty($ArrDetAcc)){
					$this->db->insert_batch('warehouse_planning_detail_acc', $ArrDetAcc);
				}
				if($type == 'pipe'){
					$this->db->where('no_ipp', $valx['no_ipp']);
					$this->db->update('so_header', $ArrUpdate);
				}
				else{
					$this->db->where('no_ipp', $valx['no_ipp']);
					$this->db->update('planning_tanki', $ArrUpdate);
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
				history('Create Material Planning '.$tanda.' '.$valx['no_ipp']);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$id_bq    = $this->uri->segment(3);
			$type    = $this->uri->segment(4);
			$no_ipp   = str_replace('BQ-','',$id_bq);
			$tanggal  = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
			$bulan    = ltrim(date('m', strtotime($tanggal)),'0');
			$tahun    = date('Y', strtotime($tanggal));
			
			if($type == 'pipe'){
			$query		= "	SELECT
								a.id_bq AS id_bq,
								a.id_material AS id_material,
								a.nm_material AS nm_material,
								round( sum( ( a.last_cost * d.qty ) ), 3 ) AS last_cost
							FROM
								estimasi_total a 
									LEFT JOIN so_bf_detail_header d ON a.id_milik = d.id_milik
							WHERE
								a.id_material <> '0'
								AND a.id_bq='".$id_bq."'
								AND a.id_material <> 'MTL-1903000'
							GROUP BY
								a.id_material,
								a.id_bq 
							ORDER BY
								a.nm_material";
			// echo $query; exit;
			$result		 = $this->db->query($query)->result_array();
			
			$sql_non_frp 	= "	SELECT
								a.id,
								a.id_milik,
								a.id_bq,
								a.id_material,
								a.qty,
								a.category,
								a.satuan,
								a.berat,
								y.id_material as code_group,
								0 as stock
							FROM
								so_acc_and_mat a
								LEFT JOIN accessories y ON a.id_material = y.id
								-- LEFT JOIN warehouse_rutin_stock b ON y.id_material = b.code_group
							WHERE
								a.category <> 'mat'
								AND a.id_bq='".$id_bq."'
								
								"; //AND b.id IS NOT NULL
								// AND a.id_milik IS NOT NULL
			$non_frp		= $this->db->query($sql_non_frp)->result_array();
			// echo $sql_non_frp;
			$pack_truck = $this->db
								->select('
									id,
									id_bq,
									category,
									caregory_sub AS sub_category,
									option_type AS jenis_packing,
									area,
									tujuan,
									kendaraan AS jenis_kendaraan,
									qty,
									price AS unit_price,
									price_total AS total_price
									')
								->from('cost_project_detail')
								->where('id_bq',$id_bq)
								->where('price_total > ', 0)
								->where("(category = 'packing' OR category = 'export' OR category = 'lokal')")
								->get()
								->result_array();
			}
			else{
				$query	= "	SELECT
								a.no_ipp AS id_bq,
								a.id_material AS id_material,
								b.nm_material AS nm_material,
								round( sum( ( a.berat ) ), 3 ) AS last_cost 
							FROM
								( planning_tanki_detail a LEFT JOIN raw_materials b ON ( ( a.id_material = b.id_material ) ) ) 
							WHERE
								( a.id_material <> '0' AND a.id_material <> 'MTL-1903000')  
								AND a.no_ipp='$id_bq'
								AND a.category='mat'
							GROUP BY
								a.id_material,
								a.no_ipp 
							ORDER BY
								b.nm_material";
				$result		 	= $this->db->query($query)->result_array();
				$non_frp		= $this->db->select("id_material, berat AS qty, 'tanki' AS type")->get_where('planning_tanki_detail', array('category'=>'acc', 'no_ipp'=>$id_bq))->result_array();
				
				$sql_non_frp 	= "	SELECT
										a.id,
										a.id AS id_milik,
										a.no_ipp AS id_bq,
										c.id AS id_material,
										SUM(a.berat) AS qty,
										'tanki' AS category,
										'pcs' AS satuan,
										SUM(a.berat) AS berat,
										b.stock,
										'' as code_group
									FROM
										planning_tanki_detail a
										LEFT JOIN accessories c ON a.id_material=c.id_acc_tanki AND c.category = 5
										LEFT JOIN warehouse_acc_stock b ON c.id = b.id_acc
									WHERE
										a.category = 'acc'
										AND a.no_ipp='$id_bq'
									GROUP BY a.id_material ";
				// echo $sql_non_frp;
				$non_frp		= $this->db->query($sql_non_frp)->result_array();
				$material		= array();
				$pack_truck		= array();
			}

			$data = array(
				'type' 		=> $type,
				'id_bq' 		=> $id_bq,
				'non_frp' 		=> $non_frp,
				'data_result' 	=> $result,
				'tanggal' 		=> $tanggal,
				'bulan' 		=> $bulan,
				'tahun' 		=> $tahun,
				'pack_truck' 	=> $pack_truck
			);
			$this->load->view('Material_planning/modal_add_material_planning', $data);
		}
	}

	//EDIT
	public function edit_get_query_material_planning(){
		if($this->input->post()){
			$tanda 			= $this->uri->segment(3);
		  	$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dataDetail		= $data['addMatPlanning'];
			$tgl_butuh		= $data['tgl_butuh'];
			$no_ipp			= $data['no_ipp'];
			$type			= $data['type'];
			// echo 'Tanda: '.$tanda.'<br>';
			$check_det = $this->db->get_where('warehouse_planning_detail', array('no_ipp'=>$no_ipp))->result();
			$check_acc = $this->db->get_where('warehouse_planning_detail_acc', array('no_ipp'=>$no_ipp))->result();

			if(!empty($data['add_acc_planning'])){
				$add_acc_planning	= $data['add_acc_planning'];
			}
			$ArrDeatil		 = array();
			$SUM_MAT = 0;
			$SUM_USE = 0;
			$SUM_PUR = 0;
			// if(empty($tanda)){
				foreach($dataDetail AS $val => $valx){
					$SUM_MAT += (int)str_replace(',', '', $valx['jumlah_mat']);
					$SUM_USE += (int)str_replace(',', '', $valx['use_stock']);
					$SUM_PUR += (int)str_replace(',', '', $valx['purchase']);
					if(!empty($check_det)){
					$ArrDeatil[$val]['id'] = $valx['id'];
					}
					$ArrDeatil[$val]['no_ipp']         = $no_ipp;
					$ArrDeatil[$val]['id_material'] = $valx['id_material'];
					$ArrDeatil[$val]['idmaterial'] = $valx['idmaterial'];
					$ArrDeatil[$val]['nm_material'] = $valx['nm_material'];
					$ArrDeatil[$val]['jumlah_mat']     = str_replace(',', '', $valx['jumlah_mat']);
					$ArrDeatil[$val]['use_stock']      = str_replace(',', '', $valx['use_stock']);
					$ArrDeatil[$val]['purchase']       = str_replace(',', '', $valx['purchase']);
					$ArrDeatil[$val]['moq']            = str_replace(',', '', $valx['moq']);
					$ArrDeatil[$val]['sisa_avl']       = str_replace(',', '', $valx['sisa_avl']);
					$ArrDeatil[$val]['reorder_point']  = str_replace(',', '', $valx['reorder_point']);
					$ArrDeatil[$val]['book_per_month'] = str_replace(',', '', $valx['book_per_month']);
					$ArrDeatil[$val]['max_stock']      = str_replace(',', '', $valx['max_stock']);
					$ArrDeatil[$val]['tanggal']    		= $tgl_butuh;
					$ArrDeatil[$val]['sts_app']    		= 'N';
					if(empty($check_det)){
					$ArrDeatil[$val]['created_by']    	= $data_session['ORI_User']['username'];
					$ArrDeatil[$val]['created_date']    = date('Y-m-d H:i:s');
					}
				}
			// }
			
			$ArrDetAcc	= array();
			if(!empty($tanda)){
				if(!empty($data['add_acc_planning'])){
					foreach($add_acc_planning AS $val => $valx){
						$sudahReq = 0;
						if(!empty($check_acc)){
						$SUM_PUR += (int)str_replace(',', '', $valx['purchase']);
						
						$ArrDetAcc[$val]['id'] 			= $valx['id'];

						$getDet = $this->db->get_where('warehouse_planning_detail_acc',array('id'=>$valx['id']))->result_array();
						$sudahReq = (!empty($getDet[0]['sudah_request']))?$getDet[0]['sudah_request']:0;
						}
						$ArrDetAcc[$val]['no_ipp']         = $no_ipp;
						$ArrDetAcc[$val]['id_material']    = $valx['id_material'];
						$ArrDetAcc[$val]['idmaterial']     = (!empty($valx['idmaterial']))?$valx['idmaterial']:NULL;
						$ArrDetAcc[$val]['nm_material']    = (!empty($valx['idmaterial']))?get_name('accessories','nama','id',$valx['id_material']):$valx['nm_material'];
						
						// $ArrDetAcc[$val]['idmaterial']     = (empty($valx['nm_material']))?get_name('accessories','category','id',$valx['id_material']):NULL;
						// $ArrDetAcc[$val]['nm_material']    = (empty($valx['nm_material']))?get_name('accessories','nama','id',$valx['id_material']):$valx['nm_material'];
						
						$ArrDetAcc[$val]['jumlah_mat']     = str_replace(',', '', $valx['jumlah_mat']);
						$ArrDetAcc[$val]['use_stock']      = 0;
						$ArrDetAcc[$val]['purchase']       = str_replace(',', '', $valx['purchase']);
						$ArrDetAcc[$val]['sudah_request']       = $sudahReq + str_replace(',', '', $valx['purchase']);
						$ArrDetAcc[$val]['moq']            = 0;
						$ArrDetAcc[$val]['sisa_avl']       = 0;
						$ArrDetAcc[$val]['reorder_point']  = 0;
						$ArrDetAcc[$val]['book_per_month'] = 0;
						$ArrDetAcc[$val]['max_stock']      = 0;
						$ArrDetAcc[$val]['tanggal']    		= $tgl_butuh;
						$ArrDetAcc[$val]['satuan']    		= $valx['satuan'];
						$ArrDetAcc[$val]['created_by']    	= $data_session['ORI_User']['username'];
						$ArrDetAcc[$val]['created_date']    = date('Y-m-d H:i:s');

					}
				}
			}

			$ArrHeader = array(
				'no_pr' => '',
				'jumlah_mat' => $SUM_MAT,
				'use_stock' => $SUM_USE,
				'purchase' => $SUM_PUR,
				'updated_by' => $data_session['ORI_User']['username'],
				'updated_date' => date('Y-m-d H:i:s')
			);

			

			// print_r($ArrHeader);
			// print_r($check_det);
			// print_r($ArrDeatil);
			// print_r($ArrDetAcc);
			// exit;
			$this->db->trans_start();
				if(!empty($check_det)){
					if(!empty($ArrDeatil) AND empty($tanda)){
						$this->db->where('sts_app <>','Y');
						$this->db->update_batch('warehouse_planning_detail', $ArrDeatil, 'id');
					}
				}
				if(!empty($check_acc)){
					if(!empty($ArrDetAcc) AND !empty($tanda)){
						$this->db->update_batch('warehouse_planning_detail_acc', $ArrDetAcc, 'id');
					}
				}

				if(empty($check_det)){
					if(!empty($ArrDeatil) AND empty($tanda)){
						$this->db->insert_batch('warehouse_planning_detail', $ArrDeatil);
					}
				}
				if(empty($check_acc)){
					if(!empty($ArrDetAcc) AND !empty($tanda)){
						$this->db->insert_batch('warehouse_planning_detail_acc', $ArrDetAcc);
					}
				}

				$this->db->where('no_ipp', $valx['no_ipp']);
				$this->db->update('warehouse_planning_header', $ArrHeader);
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
				history('Edit Material Planning '.$tanda.' '.$valx['no_ipp']);
			}
			echo json_encode($Arr_Data);
		}
		else{
		  $id_bq = $this->uri->segment(3);
		  $type    = $this->uri->segment(4);
		  $tanggal  = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
		  $bulan    = ltrim(date('m', strtotime($tanggal)),'0');
		  $tahun    = date('Y', strtotime($tanggal));
		  //edit
			$query 	= "	SELECT
										a.*,
										b.qty_stock,
										b.qty_booking,
										a.reorder_point,
										c.moq,
										c.unit,
										(SELECT d.purchase FROM check_book_per_month d WHERE d.id_material=a.id_material AND d.tahun='".$tahun."' AND d.bulan='".$bulan."') AS book_per_month
									FROM
										warehouse_planning_detail a
										LEFT JOIN warehouse_stock b ON a.id_material = b.id_material
										LEFT JOIN moq_material c ON a.id_material = c.id_material
									WHERE
										a.no_ipp='".str_replace('BQ-','',$id_bq)."'
										AND (b.id_gudang = '1' OR b.id_gudang = '2')
									ORDER BY a.nm_material";
			$result		= $this->db->query($query)->result_array();
			
			$sql_non_frp 	= "	SELECT
									a.*
								FROM
									warehouse_planning_detail_acc a
								WHERE
									a.no_ipp='".str_replace('BQ-','',$id_bq)."'
								ORDER BY a.id";
			$non_frp		= $this->db->query($sql_non_frp)->result_array();

			//Add
			if($type == 'pipe'){
				$query_add		= "	SELECT
									a.id_bq AS id_bq,
									a.id_material AS id_material,
									a.nm_material AS nm_material,
									round( sum( ( a.last_cost * d.qty ) ), 3 ) AS last_cost
								FROM
									estimasi_total a 
										LEFT JOIN so_bf_detail_header d ON a.id_milik = d.id_milik
								WHERE
									a.id_material <> '0'
									AND a.id_bq='".$id_bq."'
									AND a.id_material <> 'MTL-1903000'
								GROUP BY
									a.id_material,
									a.id_bq 
								ORDER BY
									a.nm_material";
				// echo $query; exit;
				$result_add		 = $this->db->query($query_add)->result_array();
				
				$sql_non_frp_add 	= "	SELECT
									a.id,
									a.id_milik,
									a.id_bq,
									a.id_material,
									a.qty,
									a.category,
									a.satuan,
									a.berat,
									b.stock,
									y.id_material as code_group
								FROM
									so_acc_and_mat a
									LEFT JOIN accessories y ON a.id_material = y.id
									LEFT JOIN warehouse_rutin_stock b ON y.id_material = b.code_group
								WHERE
									a.category <> 'mat'
									AND a.id_bq='".$id_bq."'
									AND a.id_milik IS NOT NULL
									"; //AND b.id IS NOT NULL
				$non_frp_add		= $this->db->query($sql_non_frp_add)->result_array();
				// echo $sql_non_frp;
				$pack_truck_add = $this->db
								->select('
									id,
									id_bq,
									category,
									caregory_sub AS sub_category,
									option_type AS jenis_packing,
									area,
									tujuan,
									kendaraan AS jenis_kendaraan,
									qty,
									price AS unit_price,
									price_total AS total_price
									')
								->from('cost_project_detail')
								->where('id_bq',$id_bq)
								->where('price_total > ', 0)
								->where("(category = 'packing' OR category = 'export' OR category = 'lokal')")
								->get()
								->result_array();
			}
			else{
					$query	= "	SELECT
									a.no_ipp AS id_bq,
									a.id_material AS id_material,
									b.nm_material AS nm_material,
									round( sum( ( a.berat ) ), 3 ) AS last_cost 
								FROM
									( planning_tanki_detail a LEFT JOIN raw_materials b ON ( ( a.id_material = b.id_material ) ) ) 
								WHERE
									( a.id_material <> '0' AND a.id_material <> 'MTL-1903000')  
									AND a.no_ipp='$id_bq'
									AND a.category='mat'
								GROUP BY
									a.id_material,
									a.no_ipp 
								ORDER BY
									b.nm_material";
					$result_add		 	= $this->db->query($query)->result_array();
					
					$sql_non_frp 	= "	SELECT
											a.id,
											a.id AS id_milik,
											a.no_ipp AS id_bq,
											c.id AS id_material,
											SUM(a.berat) AS qty,
											'tanki' AS category,
											'pcs' AS satuan,
											SUM(a.berat) AS berat,
											b.stock,
											c.id_material as code_group,
											0 as sudah_request
										FROM
											planning_tanki_detail a
											LEFT JOIN accessories c ON a.id_material=c.id_acc_tanki AND c.category = 5
											LEFT JOIN warehouse_acc_stock b ON c.id = b.id_acc
										WHERE
											a.category = 'acc'
											AND a.no_ipp='$id_bq'
										GROUP BY a.id_material ";
					// echo $sql_non_frp;
					$non_frp_add		= $this->db->query($sql_non_frp)->result_array();
					$pack_truck_add		= array();
					$non_frp			= array();
				}
				// print_r($result);
			$data = array(
				'type' 		=> $type,
				'id_bq' 		=> $id_bq,
				'non_frp' 		=> $non_frp,
				'data_result' 	=> $result,
				'tanggal' 		=> $tanggal,
				'bulan' 		=> $bulan,
				'tahun' 		=> $tahun,
				'result_add' 		=> $result_add,
				'non_frp_add' 		=> $non_frp_add,
				'pack_truck_add' 	=> $pack_truck_add
			);
			$this->load->view('Material_planning/modal_edit_material_planning', $data);
		}
	}

	//BOOKING
	public function process_booking_material_planning(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_bq			= str_replace('BQ-','',$this->uri->segment(3));

		$getDetailIPP = $this->db->get_where('production',array('no_ipp'=>$id_bq))->result_array();
		$id_customer = (!empty($getDetailIPP[0]['id_customer']))?$getDetailIPP[0]['id_customer']:0;
		// echo 'Cust:'.$id_customer;
		$ArrInsertGudang = [];
		if(!empty($getDetailIPP)){
			$ArrInsertGudang = [
				'kd_gudang' => $id_customer,
				'kode' => $id_customer,
				'nm_kode' => $id_customer,
				'nm_gudang' => $id_customer,
				'category' => 'customer'
			];
		}

		// print_r($ArrInsertGudang);

		$checkGudangCust = $this->db->get_where('warehouse',array('kd_gudang'=>$id_customer))->result_array();
		
		
		// echo $id_bq;
		// exit;
		$sqlWhDetail	= "	SELECT
								a.*,
								b.id AS id2,
								b.qty_booking,
								b.kd_gudang,
								b.id_gudang,
								b.id_category,
								b.nm_category,
								b.qty_stock
							FROM
								warehouse_planning_detail a
								LEFT JOIN warehouse_stock b
									ON a.id_material=b.id_material
							WHERE
								a.no_ipp = '".$id_bq."'
								AND a.id_material <> 'MTL-1903000'
								AND (b.id_gudang = '1' OR b.id_gudang = '2')
							";
		$restWhDetail	= $this->db->query($sqlWhDetail)->result_array();

		$ArrDeatil		 = array();
		$ArrHist		 = array();
		foreach($restWhDetail AS $val => $valx){
			$ArrDeatil[$val]['id'] 			= $valx['id2'];
			$ArrDeatil[$val]['id_material'] = $valx['id_material'];
			$ArrDeatil[$val]['id_gudang'] 	= $valx['id_gudang'];
			$ArrDeatil[$val]['qty_booking'] = $valx['qty_booking'] + $valx['use_stock'];
			$ArrDeatil[$val]['update_by'] 		= $data_session['ORI_User']['username'];
			$ArrDeatil[$val]['update_date'] 	= date('Y-m-d H:i:s');
		}

		foreach($restWhDetail AS $val => $valx){
			$ArrHist[$val]['id_material'] 		= $valx['id_material'];
			$ArrHist[$val]['idmaterial'] 		= $valx['idmaterial'];
			$ArrHist[$val]['nm_material'] 		= $valx['nm_material'];
			$ArrHist[$val]['id_category'] 		= $valx['id_category'];
			$ArrHist[$val]['nm_category'] 		= $valx['nm_category'];
			$ArrHist[$val]['id_gudang'] 		= $valx['id_gudang'];
			$ArrHist[$val]['kd_gudang'] 		= $valx['kd_gudang'];
			$ArrHist[$val]['id_gudang_dari'] 	= $valx['id_gudang'];
			$ArrHist[$val]['kd_gudang_dari'] 	= $valx['kd_gudang'];
			$ArrHist[$val]['kd_gudang_ke'] 		= 'BOOKING';
			$ArrHist[$val]['qty_stock_awal'] 	= $valx['qty_stock'];
			$ArrHist[$val]['qty_stock_akhir'] 	= $valx['qty_stock'];
			$ArrHist[$val]['qty_booking_awal'] 	= $valx['qty_booking'];
			$ArrHist[$val]['qty_booking_akhir'] = $valx['qty_booking'] + $valx['use_stock'];
			$ArrHist[$val]['no_ipp'] 			= $id_bq;
			$ArrHist[$val]['jumlah_mat'] 		= $valx['use_stock'];
			$ArrHist[$val]['ket'] 				= 'booking material';
			$ArrHist[$val]['update_by'] 		= $data_session['ORI_User']['username'];
			$ArrHist[$val]['update_date'] 		= date('Y-m-d H:i:s');
		}

		//New Booking Aksesories
		$getIDGudangProject = $this->db->get_where('warehouse',array('category'=>'project'))->result_array();
		$id_gudang_project = (!empty($getIDGudangProject[0]['id']))?$getIDGudangProject[0]['id']:0;

		$sqlGetAcc	= "	SELECT
								a.*,
								b.id AS id2,
								b.booking as qty_booking,
								b.gudang as id_gudang,
								b.stock as qty_stock
							FROM
								warehouse_planning_detail_acc a
								LEFT JOIN warehouse_rutin_stock b ON a.code_group=b.code_group AND b.gudang = '".$id_gudang_project."'
							WHERE
								a.no_ipp = '".$id_bq."'
								AND a.code_group <> 'non acc'
							";
		$restGetAcc	= $this->db->query($sqlGetAcc)->result_array();

		$ArrUpdateStockAcc		= array();
		$ArrHistAcc		 		= array();
		$ArrUpdateStockAccInsert		= array();
		$ArrHistAccInsert		 		= array();
		foreach($restGetAcc AS $val => $valx){
			if(!empty($valx['id2'])){
				$ArrUpdateStockAcc[$val]['id'] 			= $valx['id2'];
				$ArrUpdateStockAcc[$val]['booking'] 	= $valx['qty_booking'] + $valx['jumlah_mat'];
				$ArrUpdateStockAcc[$val]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrUpdateStockAcc[$val]['update_date'] = date('Y-m-d H:i:s');
			}
			else{
				$ArrUpdateStockAccInsert[$val]['code_group'] 	= $valx['code_group'];
				$ArrUpdateStockAccInsert[$val]['material_name'] = $valx['nm_material'];
				$ArrUpdateStockAccInsert[$val]['gudang'] 		= $id_gudang_project;
				$ArrUpdateStockAccInsert[$val]['stock'] 		= 0;
				$ArrUpdateStockAccInsert[$val]['booking'] 		= $valx['jumlah_mat'];
				$ArrUpdateStockAccInsert[$val]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrUpdateStockAccInsert[$val]['update_date'] 	= date('Y-m-d H:i:s');
			}
		}

		foreach($restGetAcc AS $val => $valx){
			if(!empty($valx['id2'])){
				$ArrHistAcc[$val]['code_group'] 		= $valx['code_group'];
				$ArrHistAcc[$val]['material_name'] 		= $valx['nm_material'];
				$ArrHistAcc[$val]['id_gudang'] 			= $valx['id_gudang'];
				$ArrHistAcc[$val]['id_gudang_dari'] 	= $valx['id_gudang'];
				$ArrHistAcc[$val]['gudang_ke'] 		= 'BOOKING';
				$ArrHistAcc[$val]['qty_stock_awal'] 	= $valx['qty_stock'];
				$ArrHistAcc[$val]['qty_stock_akhir'] 	= $valx['qty_stock'];
				$ArrHistAcc[$val]['qty_booking_awal'] 	= $valx['qty_booking'];
				$ArrHistAcc[$val]['qty_booking_akhir'] 	= $valx['qty_booking'] + $valx['jumlah_mat'];
				$ArrHistAcc[$val]['no_trans'] 			= $id_bq;
				$ArrHistAcc[$val]['jumlah_qty'] 		= $valx['jumlah_mat'];
				$ArrHistAcc[$val]['ket'] 				= 'booking accessories';
				$ArrHistAcc[$val]['update_by'] 			= $data_session['ORI_User']['username'];
				$ArrHistAcc[$val]['update_date'] 		= date('Y-m-d H:i:s');
			}
			else{
				$ArrHistAccInsert[$val]['code_group'] 			= $valx['code_group'];
				$ArrHistAccInsert[$val]['material_name'] 		= $valx['nm_material'];
				$ArrHistAccInsert[$val]['id_gudang'] 			= $valx['id_gudang'];
				$ArrHistAccInsert[$val]['id_gudang_dari'] 		= $valx['id_gudang'];
				$ArrHistAccInsert[$val]['gudang_ke'] 			= 'BOOKING';
				$ArrHistAccInsert[$val]['qty_stock_awal'] 		= 0;
				$ArrHistAccInsert[$val]['qty_stock_akhir'] 		= 0;
				$ArrHistAccInsert[$val]['qty_booking_awal'] 	= 0;
				$ArrHistAccInsert[$val]['qty_booking_akhir'] 	= $valx['jumlah_mat'];
				$ArrHistAccInsert[$val]['no_trans'] 			= $id_bq;
				$ArrHistAccInsert[$val]['jumlah_qty'] 			= $valx['jumlah_mat'];
				$ArrHistAccInsert[$val]['ket'] 					= 'booking accessories (insert new)';
				$ArrHistAccInsert[$val]['update_by'] 			= $data_session['ORI_User']['username'];
				$ArrHistAccInsert[$val]['update_date'] 			= date('Y-m-d H:i:s');
			}
		}


		$ArrHeader = array(
			'sts_booking' => 'Y',
			'book_by' => $data_session['ORI_User']['username'],
			'book_date' => date('Y-m-d H:i:s')
		);

		$ArrHeader2 = array(
			'sts_booking' => 'Y'
		);

		// print_r($ArrUpdateStockAcc);
		// print_r($ArrUpdateStockAccInsert);
		// print_r($ArrHistAcc);
		// print_r($ArrHistAccInsert);
		// print_r($ArrInsertGudang);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrDeatil)){
				$this->db->update_batch('warehouse_stock', $ArrDeatil, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}

			// if(!empty($ArrUpdateStockAcc)){
			// 	$this->db->update_batch('warehouse_rutin_stock', $ArrUpdateStockAcc, 'id');
			// }
			// if(!empty($ArrUpdateStockAccInsert)){
			// 	$this->db->insert_batch('warehouse_rutin_stock', $ArrUpdateStockAccInsert);
			// }
			// if(!empty($ArrHistAcc)){
			// 	$this->db->insert_batch('warehouse_rutin_history', $ArrHistAcc);
			// }
			// if(!empty($ArrHistAccInsert)){
			// 	$this->db->insert_batch('warehouse_rutin_history', $ArrHistAccInsert);
			// }

			$this->db->where('no_ipp', $id_bq);
			$this->db->update('warehouse_planning_header', $ArrHeader);

			$this->db->where('no_ipp', $id_bq);
			$this->db->update('planning_tanki', $ArrHeader2);

			$this->db->where('no_ipp', $id_bq);
			$this->db->update('warehouse_planning_detail_acc', $ArrHeader2);

			// if(empty($checkGudangCust) AND !empty($ArrInsertGudang)){
			// 	$this->db->insert('warehouse', $ArrInsertGudang);
			// }
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
			history('Booking Material Planning '.$id_bq);
		}
		echo json_encode($Arr_Data);
	}

	//PRINT
	public function print_material_planning(){
		$id_bq		= $this->uri->segment(3);

		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		$koneksi		= akses_server_side();

		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/controllers/plusPrintPlanning.php";

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		 = count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		// $okeH  			= $this->session->userdata("ses_username");

		history('Print Material Planning '.$id_bq);

		PrintSPKPlanning($Nama_Beda, $id_bq, $koneksi, $printby);
	}
	
	//INDEX
	public function index_reorder_point(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Indeks Of Re-Order Point',
		  'action'		=> 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	=> $Arr_Akses
		);
		history('View Material Re-Order Point');
		$this->load->view('Material_planning/reorder_point',$data);
	}
	
	public function save_reorder_point(){
		$data = $this->input->post();
		$id_material 	= $data['id_material'];
		$purchase 		= $data['purchase'];
		$tanggal 		= $data['tanggal'];
		$moq 			= $data['moq'];
		$reorder_point 	= $data['reorder_point'];
		$sisa_avl 		= $data['sisa_avl'];
		$book_per_month = $data['book_per_month'];
		
		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_ipp) as maxP FROM warehouse_planning_header WHERE no_ipp LIKE 'P".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 5, 5);
		$urutan2++;
		$urut2			= sprintf('%05s',$urutan2);
		$kodeP			= "P".$Ym.$urut2;
		
		$get_material = $this->db->query("SELECT * FROM raw_materials WHERE id_material='".$id_material."' LIMIT 1")->result();
		
		$ArrHeader = array(
			'no_ipp' => $kodeP,
			'jumlah_mat' => $purchase,
			'purchase' => $purchase,
			'book_by' => $this->session->userdata['ORI_User']['username'],
			'book_date' => date('Y-m-d H:i:s'),
			'created_by' => $this->session->userdata['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		);
		
		$ArrDetail = array(
			'no_ipp' 		=> $kodeP,
			
			'id_material' 	=> $get_material[0]->id_material,
			'idmaterial' 	=> $get_material[0]->idmaterial,
			'nm_material' 	=> $get_material[0]->nm_material,
			
			'jumlah_mat' 	=> $purchase,
			'purchase' 		=> $purchase,
			
			'moq' 			=> $moq,
			'reorder_point' => $reorder_point,
			'sisa_avl' 		=> $sisa_avl,
			'book_per_month'=> $book_per_month,
			'tanggal' 		=> $tanggal,
			'created_by' => $this->session->userdata['ORI_User']['username'],
			'created_date' => date('Y-m-d H:i:s')
		);
		
		
		// echo $kodeP."<br>";
		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// exit;
		
		$this->db->trans_start();
  			$this->db->insert('warehouse_planning_header', $ArrHeader);
  			$this->db->insert('warehouse_planning_detail', $ArrDetail);
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
  			history('Create List PR by re-order point '.$kodeP);
  		}
  		echo json_encode($Arr_Data);
	}

	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	public function get_data_json_material_planning(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/material_planing";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_material_planning(
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
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$no_ipp = str_replace('BQ-','',$row['id_bq']);
			$no_so = $row['so_number'];
			if($row['type'] == 'tanki'){
				$no_so = $row['no_so'];
			}
			$nestedData[]	= "<div align='center'>".$no_so."</div>";
			$date_so = (!empty($row['tgl_so']))?date('d-M-Y', strtotime($row['tgl_so'])):'-';
			$nestedData[]	= "<div align='center'>".$date_so."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";

          $detail_bq = "<button class='btn btn-sm btn-warning detailBQ' title='Detail BQ' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
					$create	= "";
					$edit	= "";
					$booking	= "";
					$spk_ambil_mat	= "";
					$download_excel	= "";
					if((is_null($row['sts_booking']) OR $row['sts_booking']=='N') AND $row['mp']=='Y' AND $row['sts_booking']!='Y'){
						if($row['mat_plan_sts']=='N' AND $row['mp']=='Y'){
							if($Arr_Akses['create']=='1'){
								$create	= "&nbsp;<button class='btn btn-sm btn-success createMat' title='Create Material Planning' data-id_bq='".$row['id_bq']."' data-type='".$row['type']."'><i class='fa fa-plus'></i></button>";
							}
						}
						if($row['mat_plan_sts']=='Y' AND $row['mp']=='Y'){
							if($Arr_Akses['update']=='1'){
								$edit		= "&nbsp;<button class='btn btn-sm btn-info editMat' title='Edit Material Planning' data-id_bq='".$row['id_bq']."' data-type='".$row['type']."'><i class='fa fa-pencil-square-o'></i></button>";
							}
							if($Arr_Akses['approve']=='1'){
								$booking	= "&nbsp;<button type='button' class='btn btn-sm btn-success bookMat' title='Booking Material Planning' data-id_bq='".$row['id_bq']."' data-type='".$row['type']."'><i class='fa fa-check'></i></button>";
							}
						}
					}
					if($row['sts_booking']=='Y' AND $row['mp']=='Y'){
						$spk_ambil_mat	= "&nbsp;<a href='".base_url('warehouse/spk_material/'.$row['id_bq'])."' target='_blank' class='btn btn-sm' style='background-color: #d25e0c; border-color: #d25e0c; color: white;' title='Print SPK Material Plan' data-role='qtip'><i class='fa fa-print'></i></a>";
					}
					$download_excel	= "&nbsp;<a href='".base_url('warehouse/download_excel/'.$row['id_bq'].'/'.$row['type'])."' target='_blank' class='btn btn-sm' style='background-color: #0cd2aa; border-color: #0cd2aa; color: white;' title='Download Excel' data-role='qtip'><i class='fa fa-file-excel-o'></i></a>";
			if($row['canceled_so']=="Y"){
				$nestedData[]	= "<div align='left'>
						  <button class='btn btn-sm btn-primary detailMat' title='Total Material' data-id_bq='".$row['id_bq']."' data-type='".$row['type']."'><i class='fa fa-eye'></i></button>
						  <span class='badge bg-red'>CLOSE</span>
								</div>";
			}else{
				$nestedData[]	= "<div align='left'>
						  <button class='btn btn-sm btn-primary detailMat' title='Total Material' data-id_bq='".$row['id_bq']."' data-type='".$row['type']."'><i class='fa fa-eye'></i></button>
								".$download_excel."
								".$create."
								".$edit."
								".$booking."
								".$spk_ambil_mat."
								</div>";
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

	public function query_data_json_material_planning($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id_bq,
				a.no_so,
				a.tgl_so,
				a.nm_customer,
				a.project,
				a.sts_booking,
				a.mp,
				a.mat_plan_sts,
				a.type,
				b.so_number,
				c.status,
				c.canceled_so
			FROM
				planning_pr a
				LEFT JOIN so_number b ON a.id_bq = b.id_bq
				left join table_sales_order c on a.id_bq = c.id_bq,
				(SELECT @row:=0) r
		    WHERE (a.mp = 'Y') AND (
				a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => ' b.so_number',
			3 => 'tgl_so',
			4 => 'nm_customer',
			5 => 'project'
		);

		$sql .= " ORDER BY a.tgl_so DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function get_data_json_reorder_point(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_reorder_point(
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
			
			$tgl_now = date('Y-m-d');
			$tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['idmaterial']." / ".$row['id_accurate']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_category_master']."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['qty_stock'],2)."</div>";
			
			$nestedData[]	= "<div align='right'>".number_format($row['qty_stock'] - $row['qty_booking'],2)."</div>";
			
			$bookpermonth 	= $row['book_per_month'];
			$leadtime 		= get_max_field('raw_material_supplier', 'lead_time_order', 'id_material', $row['id_material']);
			$safetystock 	= get_max_field('raw_materials', 'safety_stock', 'id_material', $row['id_material']);
			$max_stock 		= get_max_field('raw_materials', 'max_stock', 'id_material', $row['id_material']);
			$kg_per_bulan 	= get_max_field('raw_materials', 'kg_per_bulan', 'id_material', $row['id_material']);
			
			$reorder 		= ($safetystock/30) * $kg_per_bulan;
			$max_stock2 	= ($max_stock/30) * $kg_per_bulan;
			// $max_stock2 	= $safetystock;
			$sisa_avl 		= $row['qty_stock'] - $row['qty_booking'];
			$nestedData[]	= "<div align='right'>".number_format($reorder,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($max_stock2,2)."</div>";
			// $nestedData[]	= "<div align='right'>".get_qty_pr($row['id_material'])."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['moq'],2)."</div>";
			$qtypr = get_qty_pr($row['id_material']);
			$nestedData[]	= "<div align='right'>".number_format($qtypr,2)."</div>";
			
			$QTY_PR = $max_stock2 - ($row['qty_stock'] - $row['qty_booking']) - $qtypr;
			if($QTY_PR < 0){
				$QTY_PR = 0;
			}

			$purchase2 = (!empty($row['request']))?$row['request']:$QTY_PR;

			$nestedData[]	= "<div align='right'>
									<input type='text' name='purchase' id='purchase_".$nomor."' data-id_material='".$row['id_material']."' data-no='".$nomor."' class='form-control input-sm text-right maskM changeSave' style='width:100%;' value='".$purchase2."'>
									<input type='hidden' name='moq' id='moq_".$nomor."' class='form-control input-sm text-right' value='".$row['moq']."'>
									<input type='hidden' name='reorder_point' id='reorder_point_".$nomor."' class='form-control input-sm text-right' value='".$reorder."'>
									<input type='hidden' name='sisa_avl' id='sisa_avl_".$nomor."' class='form-control input-sm text-right' value='".$sisa_avl."'>
									<input type='hidden' name='book_per_month' id='book_per_month_".$nomor."' class='form-control input-sm text-right' value='".$bookpermonth."'>
									
								</div><script type='text/javascript'>$('.maskM').autoNumeric('init', {mDec: '2', aPad: false});</script>";
			
			// $nestedData[]	= "<div align='right'><input type='text' name='tanggal' id='tanggal_".$nomor."' class='form-control input-sm tgl' style='width:100%;' readonly placeholder='Tgl Dibutuhkan'  value='".$tgl_next_month."'></div>
			// 						<style>.tgl{cursor:pointer;}</style>
			// 						<script type='text/javascript'>
			// 						$('.tgl').datepicker({
			// 							dateFormat : 'yy-mm-dd',
			// 							changeMonth: true,
			// 							changeYear: true,
			// 							minDate : 0
			// 						});
			// 						</script>";
			
			// $save			= "<button type='button'class='btn btn-sm btn-info save_pr' title='Ajukan PR' data-id_material='".$row['id_material']."' data-no='".$nomor."'><i class='fa fa-check'></i></button>";
			
			// $nestedData[]	= "<div align='center'>".$save."</div>";
			
			// $nestedData[]	= "<script type='text/javascript'>$('.maskM').maskMoney();$('.tgl').datepicker();</script>";
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

	public function query_data_json_reorder_point($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$tanggal  = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
		$bulan    = ltrim(date('m', strtotime($tanggal)),'0');
		$tahun    = date('Y', strtotime($tanggal));
			
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				c.moq,
				d.request, d.id_accurate,
				d.nm_category AS nm_category_master,
				(SELECT d.purchase FROM check_book_per_month d WHERE d.id_material=a.id_material AND d.tahun='".$tahun."' AND d.bulan='".$bulan."') AS book_per_month
			FROM
				warehouse_stock a
				LEFT JOIN moq_material c ON a.id_material = c.id_material
				LEFT JOIN raw_materials d ON a.id_material = d.id_material,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.id_material <> 'MTL-1903000' 
				AND d.flag_active='Y' 
				AND (a.id_gudang = '1' OR a.id_gudang = '2')
				AND (
					a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR d.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR d.id_accurate LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'idmaterial',
			2 => 'nm_material'
		);

		$sql .= " ORDER BY a.id, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//NEW REORDER POINT
	public function save_reorder_change(){
		$data = $this->input->post();
		
		$id_material 	= $data['id_material'];
		$purchase 		= $data['purchase'];
		$tanggal 		= $data['tanggal'];
		$moq 			= $data['moq'];
		$reorder_point 	= $data['reorder_point'];
		$sisa_avl 		= $data['sisa_avl'];
		$book_per_month = $data['book_per_month'];
		
		
		$ArrHeader = array(
			'request' 			=> $purchase,
			'moq' 				=> $moq,
			'reorder_point' 	=> $reorder_point,
			'sisa_avl' 			=> $sisa_avl,
			'book_per_month' 	=> $book_per_month,
			'tgl_dibutuhkan' 	=> $tanggal
		);
		
		$this->db->trans_start();
  			$this->db->where('id_material', $id_material);
  			$this->db->update('raw_materials', $ArrHeader);
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
  			history('Change propose request material '.$id_material.' / '.$purchase.' / '.$tanggal);
  		}
  		echo json_encode($Arr_Data);
	}

	public function save_reorder_change_date(){
		$data = $this->input->post();
		
		$tanggal 		= $data['tanggal'];
		$get_materials 	= $this->db->get('raw_materials')->result_array();
		
		foreach ($get_materials as $key => $value) {
			$ArrUpdate[$key]['id_material'] = $value['id_material'];
			$ArrUpdate[$key]['tgl_dibutuhkan'] = $tanggal;
		}
		
		$this->db->trans_start();
  			$this->db->update_batch('raw_materials', $ArrUpdate,'id_material');
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
  			history('Change propose request material date all '.$tanggal);
  		}
  		echo json_encode($Arr_Data);
	}

	public function clear_update_reorder(){
		$data = $this->input->post();
		$tgl_now = date('Y-m-d');
		$tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
		$get_materials 	= $this->db->get('raw_materials')->result_array();
		
		foreach ($get_materials as $key => $value) {
			$ArrUpdate[$key]['id_material'] = $value['id_material'];
			$ArrUpdate[$key]['request'] = 0;
			$ArrUpdate[$key]['tgl_dibutuhkan'] = $tgl_next_month;
		}
		
		$this->db->trans_start();
  			$this->db->update_batch('raw_materials', $ArrUpdate,'id_material');
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
  			history('Clear all propose request material');
  		}
  		echo json_encode($Arr_Data);
	}

	public function save_reorder_all(){
		$data = $this->input->post();
		$UserName = $this->session->userdata['ORI_User']['username'];
		$DateTime = date('Y-m-d H:i:s');

		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_ipp) as maxP FROM warehouse_planning_header WHERE no_ipp LIKE 'P".$Ym."%' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 5, 5);
		$urutan2++;

		$getraw_materials 	= $this->db->get_where('raw_materials',array('request >'=>0))->result_array();
		$ArrSaveHeader = [];
		$ArrSaveDetail = [];

		foreach ($getraw_materials as $key => $value) {
			$urutan2++;
			$urut2			= sprintf('%05s',$urutan2);
			$kodeP			= "P".$Ym.$urut2;

			$ArrSaveHeader[] = array(
				'no_ipp' 	=> $kodeP,
				'purchase' 		=> $value['request'],
				'jumlah_mat' 		=> $value['request'],
				'book_by' 		=> $UserName,
				'book_date' 	=> $DateTime,
				'created_by' 	=> $UserName,
				'created_date' 	=> $DateTime
			);
			
			$ArrSaveDetail[] = array(
				'no_ipp' 	=> $kodeP,
				'id_material' 	=> $value['id_material'],
				'idmaterial' 	=> $value['idmaterial'],
				'nm_material' 	=> $value['nm_material'],

				'jumlah_mat' 	=> $value['request'],
				'purchase' 		=> $value['request'],
				'tanggal' 		=> $value['tgl_dibutuhkan'],
				'moq' 			=> $value['moq'],
				'reorder_point' => $value['reorder_point'],
				'sisa_avl' 		=> $value['sisa_avl'],
				'book_per_month'=> $value['book_per_month'],

				'created_by' 	=> $UserName,
				'created_date' 	=> $DateTime
			);
		}

		// print_r($ArrSaveHeader);
		// print_r($ArrSaveDetail);
		// exit;
		
		$this->db->trans_start();
			$this->db->insert_batch('warehouse_planning_header', $ArrSaveHeader);
			$this->db->insert_batch('warehouse_planning_detail', $ArrSaveDetail);
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
  			history('Save pengajuan propose material all');
  		}
  		echo json_encode($Arr_Data);
	}
	
}
