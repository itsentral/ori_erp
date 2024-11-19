<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_request_item extends CI_Controller {
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
		$data = array(
		  'title'			=> 'Request Item Project',
		  'action'		    => 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	    => $Arr_Akses
		);
		history('View request item project');
		$this->load->view('Project/request/index',$data);
	}

    public function server_side_request(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request(
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
			// $nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			// $nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			// $nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qtyCount'])."</div>";
			

            $stock	= "";
            $create	= "";
            $pemaikaian	= "";
            $retur	= "";
            $laporan	= "";
            $close	= "";

			$sts_close = "<span class='badge bg-red'>Close</span>";
            if($Arr_Akses['create']=='1'){
				if($row['close_sts'] == '0'){
					$create		= "<button type='button' class='btn btn-sm btn-success request' title='Request' data-id_bq='".$row['id_customer']."'><i class='fa fa-plus'></i></button>";
					$pemaikaian	= "&nbsp;<button type='button' class='btn btn-sm btn-primary pemakaian' title='Pemakaian' data-id_bq='".$row['id_customer']."'><i class='fa fa-edit'></i></button>";
				}
				$stock		= "&nbsp;<button type='button' class='btn btn-sm btn-default stock' title='Stock' data-id_bq='".$row['id_customer']."'><i class='fa fa-area-chart'></i></button>";
                $retur		= "&nbsp;<button type='button' class='btn btn-sm btn-danger retur' title='Retur' data-id_bq='".$row['id_customer']."'><i class='fa fa-reply'></i></button>";
                $laporan	= "&nbsp;<button type='button' class='btn btn-sm btn-primary laporan' title='Laporan Pemakaian' data-id_bq='".$row['id_customer']."'><i class='fa fa-file'></i></button>";
                if($row['close_sts'] == '0'){
					$sts_close = "<span class='badge bg-blue'>On Progress</span>";
					$close		= "&nbsp;<button type='button' class='btn btn-sm btn-danger closed' title='Close' data-id_bq='".$row['id_customer']."'><i class='fa fa-times'></i></button>";
				}
			}

			$nestedData[]	= "<div align='center'>".$sts_close."</div>";

            $nestedData[]	= "<div align='left'>".$create.$pemaikaian.$stock.$retur.$laporan.$close."</div>";

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

	public function query_data_json_request($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
                a.no_ipp as id_customer,
                c.nm_customer,
                COUNT(a.id) AS qtyCount,
				MIN(close_sts) AS close_sts
			FROM
				warehouse_planning_detail_acc a
                LEFT JOIN customer c ON a.no_ipp = c.id_customer,
				(SELECT @row:=0) r
		    WHERE a.idmaterial IN ('project') 
                AND (
                    a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                )
            GROUP BY a.no_ipp, a.close_date
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'c.nm_customer'
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function add(){
		if($this->input->post()){
			$tanda 			= $this->uri->segment(3);
		 	$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
            $username       = $data_session['ORI_User']['username'];
            $datetime       = date('Y-m-d H:i:s');
			$id_customer	= $data['id_customer'];
			$defered	    = $data['defered'];
			$sub_gudang  	= $data['sub_gudang'];
			
			if(!empty($data['add'])){
				$dataDetail	= $data['add'];
			}

            $Ym             = date('ym');
            $SQL			= "SELECT MAX(kode) as maxP FROM request_accessories WHERE kode LIKE 'X".$Ym."%' ";
            $resultIPP		= $this->db->query($SQL)->result_array();
            $angkaUrut2		= $resultIPP[0]['maxP'];
            $urutan2		= (int)substr($angkaUrut2, 5, 5);
            $urutan2++;
            $urut2			= sprintf('%05s',$urutan2);
            $kode			= "X".$Ym.$urut2;

			$ArrDeatil = array();
			$ArrDeatilUpdate = array();
            foreach($dataDetail AS $val => $valx){
                $QTY = str_replace(',','',$valx['request']);
                if($QTY > 0){
                    $ArrDeatil[$val]['kode']            = $kode;
                    $ArrDeatil[$val]['no_ipp']          = $valx['no_ipp'];
                    $ArrDeatil[$val]['id_milik']        = $valx['id'];
                    $ArrDeatil[$val]['qty_request']     = $QTY;
                    $ArrDeatil[$val]['created_by']    	= $username;
                    $ArrDeatil[$val]['created_date']    = $datetime;
                    $ArrDeatil[$val]['id_customer']     = $id_customer;
					$ArrDeatil[$val]['sub_gudang']      = $sub_gudang;
					$ArrDeatil[$val]['defered']         = $defered;

                    $GET_REQ        = $this->db->get_where('warehouse_planning_detail_acc',array('id' => $valx['id']))->result();
                    $QTY_REQUEST    = $GET_REQ[0]->request + $QTY;

                    $ArrDeatilUpdate[$val]['id']          = $valx['id'];
                    $ArrDeatilUpdate[$val]['request']     = $QTY_REQUEST;
                }
            }

            // print_r($ArrDeatil);
            // print_r($ArrDeatilUpdate);
            // exit;
			
			$this->db->trans_start();
				if(!empty($ArrDeatil)){
					$this->db->insert_batch('request_accessories', $ArrDeatil);
				}
                if(!empty($ArrDeatilUpdate)){
					$this->db->update_batch('warehouse_planning_detail_acc', $ArrDeatilUpdate,'id');
				}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);
				history('Request subgudang accessoeries '.$kode);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$id_bq              = $this->uri->segment(3);
			$result_aksesoris   = $this->db
                                        ->select('a.*, b.nm_customer, b.id_customer')
                                        // ->where('a.request < a.jumlah_mat')
                                        ->where('a.close_sts','0')
                                        ->join('customer b','a.no_ipp=b.id_customer','left')
                                        ->get_where('warehouse_planning_detail_acc a',
                                            array(
                                                'a.no_ipp'=>$id_bq,
                                                )
                                            )
                                        ->result_array();

			$data = array(
				'id_bq' 		    => $id_bq,
				'result_aksesoris' 	=> $result_aksesoris,
                'nm_customer' => (!empty($result_aksesoris[0]['nm_customer']))?$result_aksesoris[0]['nm_customer']:'',
                'id_customer' => (!empty($result_aksesoris[0]['id_customer']))?$result_aksesoris[0]['id_customer']:'',
				'sub_gudang' => (!empty($result_aksesoris[0]['sub_gudang']))?$result_aksesoris[0]['sub_gudang']:'',
                'defered'    => (!empty($result_aksesoris[0]['defered']))?$result_aksesoris[0]['defered']:'',
			);
			$this->load->view('Project/request/add', $data);
		}
	}

	public function pemakaian(){
		if($this->input->post()){
			$tanda 			= $this->uri->segment(3);
		 	$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
            $username       = $data_session['ORI_User']['username'];
            $datetime       = date('Y-m-d H:i:s');
			$id_customer	= $data['id_customer'];

			$gudang_before 	= (!empty(getSubGudangCustomer($id_customer)))?getSubGudangCustomer($id_customer):getSubGudangProject();
			
			$gudang_before2 = getGudangProject();
		    $gudang_after   = getSubGudangProject();
			
			if(!empty($data['add'])){
				$dataDetail	= $data['add'];
			}

            $Ym             = date('ym');
            $SQL			= "SELECT MAX(kode) as maxP FROM request_accessories WHERE kode LIKE 'X".$Ym."%' ";
            $resultIPP		= $this->db->query($SQL)->result_array();
            $angkaUrut2		= $resultIPP[0]['maxP'];
            $urutan2		= (int)substr($angkaUrut2, 5, 5);
            $urutan2++;
            $urut2			= sprintf('%05s',$urutan2);
            $kode			= "X".$Ym.$urut2;

			$Ym 			= date('ym');
			
			// print_r($kode);
			// exit;
			
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRO".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRO".$Ym.$urut2;

			$ArrUpdateStock = array();
			$ArrDeatilUpdate = array();
			$ArrDeatilAdj = array();
            foreach($dataDetail AS $val => $valx){
                $QTY = str_replace(',','',$valx['request']);
                if($QTY > 0){
                    $GET_REQ        = $this->db->get_where('warehouse_planning_detail_acc',array('id' => $valx['id']))->result();
                    $QTY_REQUEST    = $GET_REQ[0]->pemakaian + $QTY;

                    $ArrDeatilUpdate[$val]['id']          = $valx['id'];
                    $ArrDeatilUpdate[$val]['pemakaian']     = $QTY_REQUEST;

					$ArrUpdateStock[$val]['id'] 		= $valx['code_group'];
					$ArrUpdateStock[$val]['qty_good'] 	= $QTY;

					$GetStock	= $this->db->get_where('warehouse_rutin_stock',array('code_group'=>$valx['code_group'], 'gudang'=>$gudang_before))->result();

					//detail adjustmeny
					$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
					$ArrDeatilAdj[$val]['no_ipp'] 			= $valx['no_ipp'];
					$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
					$ArrDeatilAdj[$val]['id_material'] 		= $valx['code_group'];
					$ArrDeatilAdj[$val]['nm_material'] 		= $GetStock[0]->material_name;
					$ArrDeatilAdj[$val]['id_category'] 		= $GetStock[0]->category_awal;
					$ArrDeatilAdj[$val]['nm_category'] 		= $GetStock[0]->category_code;
					$ArrDeatilAdj[$val]['qty_order'] 		= $QTY;
					$ArrDeatilAdj[$val]['qty_oke'] 			= $QTY;
					$ArrDeatilAdj[$val]['keterangan'] 		= 'pemakaian aktual gudang customer';
					$ArrDeatilAdj[$val]['check_qty_oke'] 	= $QTY;
					$ArrDeatilAdj[$val]['check_keterangan']	= 'pemakaian aktual gudang customer';
					$ArrDeatilAdj[$val]['update_by'] 		= $username;
					$ArrDeatilAdj[$val]['update_date'] 		= $datetime;
                }
            }

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'category' 			=> 'subgudang to customer project accessories',
				'jumlah_mat' 		=> null,
				'tanggal' 			=> date('Y-m-d'),
				'no_so' 			=> $kode,
				'id_gudang_dari' 	=> $gudang_before,
				'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $gudang_before),
				'id_gudang_ke' 		=> NULL,
				'kd_gudang_ke' 		=> 'PEMAKAIAN',
				'checked'			=> 'Y',
				'created_by' 		=> $username,
				'created_date' 		=> $datetime,
				'checked_by' 		=> $username,
				'checked_date' 		=> $datetime
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
				$grouping_temp[$value['id']]['qty'] 	= $temp[$value['id']]['good'];
				$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
			}

			//tansaksi


            // print_r($ArrUpdateStock);
            // print_r($ArrDeatilUpdate);
            // exit;
			
			$this->db->trans_start();
				if(!empty($ArrDeatilAdj)){
					$this->db->insert('warehouse_adjustment', $ArrInsertH);
					$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				}
                if(!empty($ArrDeatilUpdate)){
					$this->db->update_batch('warehouse_planning_detail_acc', $ArrDeatilUpdate,'id');
				}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again ...',
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
					move_warehouse_barang_stok($grouping_temp, $gudang_before, NULL, $kode_trans);
				}
				if(!empty($grouping_temp)){
				    insert_jurnal($grouping_temp,$gudang_before2,$gudang_after,$kode_trans,'pemakaian gudangproject - subgudang project','pengurangan subgudang project','penambahan project customer');
			}
				history('Request subgudang accessoeries '.$kode);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$id_bq              = $this->uri->segment(3);
			$result_aksesoris   = $this->db
                                        ->select('a.*, b.nm_customer, b.id_customer')
                                        // ->where('a.request < a.jumlah_mat')
										->where('a.close_sts','0')
                                        ->join('customer b','a.no_ipp=b.id_customer','left')
                                        ->get_where('warehouse_planning_detail_acc a',
                                            array(
                                                'a.no_ipp'=>$id_bq,
                                                )
                                            )
                                        ->result_array();

			$data = array(
				'id_bq' 		    => $id_bq,
				'result_aksesoris' 	=> $result_aksesoris,
                'nm_customer' => (!empty($result_aksesoris[0]['nm_customer']))?$result_aksesoris[0]['nm_customer']:'',
                'id_customer' => (!empty($result_aksesoris[0]['id_customer']))?$result_aksesoris[0]['id_customer']:'',
			);
			$this->load->view('Project/request/pemakaian', $data);
		}
	}

	public function stock(){
		$id_customer     	= $this->uri->segment(3);
		$id_gudang 			= (!empty(getSubGudangCustomer($id_customer)))?getSubGudangCustomer($id_customer):0;

		$result_aksesoris   = $this->db
									->select('a.code_group, a.stock, b.material_name, b.spec, c.id')
									->join('con_nonmat_new b','a.code_group=b.code_group','left')
									->join('accessories c','b.code_group=c.id_material','left')
									->get_where('warehouse_rutin_stock a',
										array(
											'a.gudang'=>$id_gudang,
											)
										)
									->result_array();

		$data = array(
			'result_aksesoris' 	=> $result_aksesoris,
			'nm_customer' => get_name('customer','nm_customer','id_customer',$id_customer)
		);
		$this->load->view('Project/request/stock', $data);
	}

	public function retur(){
		$id_customer     	= $this->uri->segment(3);
		$id_gudang 			= (!empty(getSubGudangCustomer($id_customer)))?getSubGudangCustomer($id_customer):0;

		$result_aksesoris   = $this->db
									->select('a.code_group, a.stock, b.material_name, b.spec, c.id, a.id as id_stock, a.retur')
									->join('con_nonmat_new b','a.code_group=b.code_group','left')
									->join('accessories c','b.code_group=c.id_material','left')
									->get_where('warehouse_rutin_stock a',
										array(
											'a.gudang'=>$id_gudang,
											)
										)
									->result_array();

		$data = array(
			'result_aksesoris' 	=> $result_aksesoris,
			'nm_customer' => get_name('customer','nm_customer','id_customer',$id_customer),
			'id_customer' => $id_customer
		);
		$this->load->view('Project/request/retur', $data);
	}

	public function proses_retur(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username       = $data_session['ORI_User']['username'];
		$datetime       = date('Y-m-d H:i:s');
		$id_customer	= $data['id_customer'];
		$no_surat_jalan	= $data['no_surat_jalan'];

		$gudang_before 	= (!empty(getSubGudangCustomer($id_customer)))?getSubGudangCustomer($id_customer):getSubGudangProject();
		$gudang_after 	= getGudangProject();
		
		if(!empty($data['add'])){
			$dataDetail	= $data['add'];
		}

		$Ym 			= date('ym');
		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'RTR".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "RTR".$Ym.$urut2;

		$ArrUpdateStock = array();
		$ArrDeatilUpdate = array();
		$ArrDeatilAdj = array();
		$SUM = 0;
		foreach($dataDetail AS $val => $valx){
			$QTY = str_replace(',','',$valx['request']);
			if($QTY > 0){
				$GET_REQ        = $this->db->get_where('warehouse_rutin_stock',array('id' => $valx['id_stock']))->result();
				$QTY_REQUEST    = $GET_REQ[0]->retur + $QTY;

				$ArrUpdateStock[$val]['id'] 		= $valx['id_stock'];
				$ArrUpdateStock[$val]['retur'] 		= $QTY_REQUEST;

				$SUM += $QTY;

				//detail adjustmeny
				$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
				$ArrDeatilAdj[$val]['no_ipp'] 			= $id_customer;
				$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id_stock'];
				$ArrDeatilAdj[$val]['id_material'] 		= $valx['code_group'];
				$ArrDeatilAdj[$val]['nm_material'] 		= $valx['nm_material'];
				$ArrDeatilAdj[$val]['keterangan'] 		= $valx['ket'];
				$ArrDeatilAdj[$val]['qty_order'] 		= $QTY;
				$ArrDeatilAdj[$val]['qty_oke'] 			= $QTY;
				$ArrDeatilAdj[$val]['update_by'] 		= $username;
				$ArrDeatilAdj[$val]['update_date'] 		= $datetime;
			}
		}

		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'category' 			=> 'retur accessories',
			'jumlah_mat' 		=> $SUM,
			'tanggal' 			=> date('Y-m-d'),
			'no_ipp' 			=> $id_customer,
			'no_surat_jalan' 	=> $no_surat_jalan,
			'id_gudang_dari' 	=> $gudang_before,
			'kd_gudang_dari' 	=> get_name('warehouse', 'nm_gudang', 'id', $gudang_before),
			'id_gudang_ke' 		=> $gudang_after,
			'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $gudang_after),
			'checked'			=> 'N',
			'created_by' 		=> $username,
			'created_date' 		=> $datetime,
			'checked_by' 		=> $username,
			'checked_date' 		=> $datetime
		);

		// print_r($ArrUpdateStock);
		// exit;
		
		$this->db->trans_start();
			if(!empty($ArrDeatilAdj)){
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
			}
			if(!empty($ArrUpdateStock)){
				$this->db->update_batch('warehouse_rutin_stock', $ArrUpdateStock,'id');
			}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again ...',
				'status'	=> 0,
				'kode_trans' => $kode_trans
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1,
				'kode_trans' => $kode_trans
			);
			history('Request retur subgudang accessoeries '.$kode_trans);
		}
		echo json_encode($Arr_Data);
	}

	public function list_retur(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Retur Item Project',
		  'action'		    => 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	    => $Arr_Akses
		);
		$this->load->view('Project/request/list_retur',$data);
	}

    public function server_side_request_retur(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request_retur(
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
			$nestedData[]	= "<div align='center'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_surat_jalan']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['jumlah_mat'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i',strtotime($row['created_date']))."</div>";

            $print	= "&nbsp;<a href='".base_url('project_request_item/print_surat_jalan_spk/'.$row['kode_trans'])."' target='_blank' title='Print Permintaan'>Print Surat Jalan</a>";
					

            $nestedData[]	= "<div align='center'>".$print."</div>";

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

	public function query_data_json_request_retur($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				c.nm_customer
			FROM
				warehouse_adjustment a
                LEFT JOIN customer c ON a.no_ipp = c.id_customer,
				(SELECT @row:=0) r
		    WHERE a.category = 'retur accessories'
                AND (
                    a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR a.no_surat_jalan LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.kode_trans',
			2 => 'c.nm_customer',
			3 => 'a.no_surat_jalan',
			4 => 'a.jumlah_mat',
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function print_surat_jalan_spk(){
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
			'check' => $check
		);
		$this->load->view('Project/request/print_retur', $data);
	}

	public function add_project(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			//header
			$id_customer 	= $data['id_customer'];
			$sub_gudang 	= $data['sub_gudang'];
			$defered     	= $data['defered'];
			$Detail 		= $data['detail'];
			$Ym				= date('ym');

			$getIDGudangProject = $this->db->get_where('warehouse',array('category'=>'project'))->result_array();
			$id_gudang_project = (!empty($getIDGudangProject[0]['id']))?$getIDGudangProject[0]['id']:0;

			$ArrDetail				= array();
			$ArrUpdateStockAcc		= array();
			$ArrHistAcc		 		= array();
			$ArrUpdateStockAccInsert= array();
			$ArrHistAccInsert		= array();

			foreach($Detail AS $val => $valx){
				if($valx['qty'] > 0 AND $valx['product'] != '0'){
					$qty_booking 	= str_replace(',','',$valx['qty']);
					$code_group 	= $valx['product'];
					// echo $code_group;
					$nm_accessories = get_name_by_code_group($code_group);

					$ArrDetail[$val]['no_ipp'] 			= $id_customer;
					$ArrDetail[$val]['defered'] 		= $defered;
					$ArrDetail[$val]['sub_gudang'] 		= $sub_gudang;
					$ArrDetail[$val]['jumlah_mat'] 		= $qty_booking;
					$ArrDetail[$val]['ket_request'] 	= $valx['ket'];
					$ArrDetail[$val]['code_group'] 		= $code_group;
					$ArrDetail[$val]['idmaterial'] 		= 'project';
					$ArrDetail[$val]['nm_material'] 	= $nm_accessories;
					$ArrDetail[$val]['satuan'] 			= get_name('con_nonmat_new','satuan','code_group',$code_group);
					$ArrDetail[$val]['created_by'] 		= $data_session['ORI_User']['username'];
					$ArrDetail[$val]['created_date'] 	= $dateTime;
					
					$getStockPusat = $this->db->get_where('warehouse_rutin_stock',array('gudang'=>$id_gudang_project, 'code_group'=>$code_group))->result_array();
					$id_stock = (!empty($getStockPusat[0]['id']))?$getStockPusat[0]['id']:null;
					$stock_booking = (!empty($getStockPusat[0]['booking']))?$getStockPusat[0]['booking']:0;
					$stock = (!empty($getStockPusat[0]['stock']))?$getStockPusat[0]['stock']:0;

					if(!empty($id_stock)){
						$ArrUpdateStockAcc[$val]['id'] 			= $id_stock;
						$ArrUpdateStockAcc[$val]['booking'] 	= $stock_booking + $qty_booking;
						$ArrUpdateStockAcc[$val]['update_by'] 	= $data_session['ORI_User']['username'];
						$ArrUpdateStockAcc[$val]['update_date'] = date('Y-m-d H:i:s');

						$ArrHistAcc[$val]['code_group'] 		= $code_group;
						$ArrHistAcc[$val]['material_name'] 		= $nm_accessories;
						$ArrHistAcc[$val]['id_gudang'] 			= $id_gudang_project;
						$ArrHistAcc[$val]['id_gudang_dari'] 	= $id_gudang_project;
						$ArrHistAcc[$val]['gudang_ke'] 			= 'BOOKING';
						$ArrHistAcc[$val]['qty_stock_awal'] 	= $stock;
						$ArrHistAcc[$val]['qty_stock_akhir'] 	= $stock;
						$ArrHistAcc[$val]['qty_booking_awal'] 	= $stock_booking;
						$ArrHistAcc[$val]['qty_booking_akhir'] 	= $stock_booking + $qty_booking;
						$ArrHistAcc[$val]['no_trans'] 			= $id_customer;
						$ArrHistAcc[$val]['jumlah_qty'] 		= $qty_booking;
						$ArrHistAcc[$val]['ket'] 				= 'booking accessories';
						$ArrHistAcc[$val]['update_by'] 			= $data_session['ORI_User']['username'];
						$ArrHistAcc[$val]['update_date'] 		= date('Y-m-d H:i:s');
					}
					else{
						$ArrUpdateStockAccInsert[$val]['code_group'] 	= $code_group;
						$ArrUpdateStockAccInsert[$val]['material_name'] = $nm_accessories;
						$ArrUpdateStockAccInsert[$val]['gudang'] 		= $id_gudang_project;
						$ArrUpdateStockAccInsert[$val]['stock'] 		= 0;
						$ArrUpdateStockAccInsert[$val]['booking'] 		= $qty_booking;
						$ArrUpdateStockAccInsert[$val]['update_by'] 	= $data_session['ORI_User']['username'];
						$ArrUpdateStockAccInsert[$val]['update_date'] 	= date('Y-m-d H:i:s');

						$ArrHistAccInsert[$val]['code_group'] 			= $code_group;
						$ArrHistAccInsert[$val]['material_name'] 		= $nm_accessories;
						$ArrHistAccInsert[$val]['id_gudang'] 			= $id_gudang_project;
						$ArrHistAccInsert[$val]['id_gudang_dari'] 		= $id_gudang_project;
						$ArrHistAccInsert[$val]['gudang_ke'] 			= 'BOOKING';
						$ArrHistAccInsert[$val]['qty_stock_awal'] 		= 0;
						$ArrHistAccInsert[$val]['qty_stock_akhir'] 		= 0;
						$ArrHistAccInsert[$val]['qty_booking_awal'] 	= 0;
						$ArrHistAccInsert[$val]['qty_booking_akhir'] 	= $qty_booking;
						$ArrHistAccInsert[$val]['no_trans'] 			= $id_customer;
						$ArrHistAccInsert[$val]['jumlah_qty'] 			= $qty_booking;
						$ArrHistAccInsert[$val]['ket'] 					= 'booking accessories (insert new)';
						$ArrHistAccInsert[$val]['update_by'] 			= $data_session['ORI_User']['username'];
						$ArrHistAccInsert[$val]['update_date'] 			= date('Y-m-d H:i:s');
					}
				}
			}

			$ArrInsertGudang = [
				'kd_gudang' => $id_customer,
				'kode' => $id_customer,
				'nm_kode' => $id_customer,
				'nm_gudang' => $id_customer,
				'category' => 'customer',
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => date('Y-m-d H:i:s')
			];

			$checkGudangCust = $this->db->get_where('warehouse',array('nm_gudang'=>$id_customer))->result_array();
			
			// print_r($ArrDetail);
			// print_r($ArrUpdateStockAcc);
			// print_r($ArrUpdateStockAccInsert);
			// print_r($ArrHistAcc);
			// print_r($ArrHistAccInsert);
			// exit;
			
			$this->db->trans_start();
				if(!empty($ArrDetail)){
					$this->db->insert_batch('warehouse_planning_detail_acc', $ArrDetail);
				}
				if(!empty($ArrUpdateStockAcc)){
					$this->db->update_batch('warehouse_rutin_stock', $ArrUpdateStockAcc, 'id');
				}
				if(!empty($ArrUpdateStockAccInsert)){
					$this->db->insert_batch('warehouse_rutin_stock', $ArrUpdateStockAccInsert);
				}
				if(!empty($ArrHistAcc)){
					$this->db->insert_batch('warehouse_rutin_history', $ArrHistAcc);
				}
				if(!empty($ArrHistAccInsert)){
					$this->db->insert_batch('warehouse_rutin_history', $ArrHistAccInsert);
				}
				if(empty($checkGudangCust) AND !empty($ArrInsertGudang)){
					$this->db->insert('warehouse', $ArrInsertGudang);
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('Booking project '.$id_customer);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
	
			$customer		= $this->db->order_by('nm_customer','asc')->get_where('customer',array('deleted_date'=>NULL))->result_array();
			$query 	= "SELECT * FROM  ".DBACC.".coa_master";
			$coa    = $this->db->query($query)->result_array();
						
			$data = array(
				'title'		=> 'Add Project & Plan Pemakaian Barang',
				'action'	=> 'add_project',
				'customer'=> $customer,
				'coa'=> $coa
			);
			$this->load->view('Project/request/add_project',$data);
		}
	}

	public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][product]' class='chosen_select form-control input-sm inline-blockd'>";
				$d_Header .= "<option value='0'>Select Accessories</option>";
				foreach(get_detail_consumable() AS $val => $valx){
					$d_Header .= "<option value='".$val."'>".$val." - ".$valx['nm_barang']."</option>";
				  }
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][qty]' class='form-control input-sm text-center autoNumeric'></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][ket]' class='form-control input-sm'></td>";
			$d_Header .= "<td align='center'>";
				$d_Header .= "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left'><button type='button' class='btn btn-sm btn-warning addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>";
			$d_Header .= "<td align='center' colspan='3'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

	public function laporan(){
		$id_bq              = $this->uri->segment(3);
		$result_aksesoris   = $this->db
									->select('a.*, b.nm_customer, b.id_customer')
									// ->where('a.request < a.jumlah_mat')
									->join('customer b','a.no_ipp=b.id_customer','left')
									->get_where('warehouse_planning_detail_acc a',
										array(
											'a.no_ipp'=>$id_bq,
											)
										)
									->result_array();

		$data = array(
			'id_bq' 		    => $id_bq,
			'result_aksesoris' 	=> $result_aksesoris,
			'nm_customer' => (!empty($result_aksesoris[0]['nm_customer']))?$result_aksesoris[0]['nm_customer']:'',
			'id_customer' => (!empty($result_aksesoris[0]['id_customer']))?$result_aksesoris[0]['id_customer']:'',
		);
		$this->load->view('Project/request/laporan', $data);
	}

	public function proses_close(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username       = $data_session['ORI_User']['username'];
		$datetime       = date('Y-m-d H:i:s');
		$id_customer	= $data['id_customer'];

		$ArrInsertH = array(
			'close_sts'		=> 1,
			'close_by' 		=> $username,
			'close_date' 	=> $datetime
		);
		
		$this->db->trans_start();
			$this->db->where('close_date', NULL);
			$this->db->where('no_ipp', $id_customer);
			$this->db->update('warehouse_planning_detail_acc', $ArrInsertH);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save process failed. Please try again ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save process success. Thanks ...',
				'status'	=> 1
			);
			history('Close gudang project '.$id_customer);
		}
		echo json_encode($Arr_Data);
	}

}